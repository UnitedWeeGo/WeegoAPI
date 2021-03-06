<?php

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

// Using Autoload all classes are loaded on-demand
require_once '../../apns-php/ApnsPHP/Autoload.php';
require_once '../invite/inviteservice.class.php';

date_default_timezone_set('GMT');

class Push
{
	// array to hold invite id's, to check against so we dont send more than 1 notification to a user
	public $queuedUpUserIDCollection;

	function Push()
	{
		$this->queuedUpUserIDCollection = array();
	}

	/**
	 * Processes the Queued Feed Message Notifications
	 */
	function dispatchQueuedFeedMessageNotifications()
	{
		/** @var $queue PushDispatch */
		$queue = $this->getQueue();
		$feedMessages = $queue->GetFeedmessageList();

		// delete all the feed messages
		$emptyList = array();
		$queue->SetFeedmessageList($emptyList);
		$queue->Save();

		// get the production and sandbox ApnsPHP_Push objects
		$prodPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION);
		$sandPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX);

		$doSendProdMessages = false;
		$doSendSandMessages = false;

		for ($i=0; $i<count($feedMessages); $i++)
		{
			/** @var $feedMessage FeedMessage */
			$feedMessage = $feedMessages[$i];
				
			$lookup = new Event();
			/** @var $event Event */
			$event = $lookup->Get($feedMessage->eventId);
				
			// for each participant...
			$participants = $event->GetParticipantList();
			for ($j=0; $j<count($participants); $j++)
			{
				/** @var $participant Participant */
				$participant = $participants[$j];

				// skip user if they have removed the event
				if ($this->getHasRemovedEvent($event, $participant->participantId)) continue;

				// skip user if they are in queue to receive an invite, so they do not get over messaged
				if (in_array($participant->email, $this->queuedUpUserIDCollection)) continue;
				array_push($this->queuedUpUserIDCollection, $participant->email);

				if ($feedMessage->senderId == $participant->email && $feedMessage->type != 'system') continue; // skips the sender

				// for each device, generate message and add to appropriate push queue
				$devices = $participant->GetDeviceList();
				for ($k=0; $k<count($devices); $k++)
				{
					/** @var $device Device */
					$device = $devices[$k];
						
					if ($device->pushBadge != 'enabled' && $device->pushAlert != 'enabled') continue;
						
					$badgeCount = $device->badgeCount;
					$newBadgeCount = $badgeCount + 1;
					$device->badgeCount = $newBadgeCount;
					$device->Save();
						
					// construct the message
					$message = new ApnsPHP_Message($device->deviceToken);
						
					if ($device->pushBadge == 'enabled')
					{
						$message->setBadge($newBadgeCount);
					}
						
					if ($device->pushAlert == 'enabled' && $feedMessage->type == 'eventupdate')
					{
						$message->setText($feedMessage->message);
					}
					else if ($device->pushAlert == 'enabled' && $feedMessage->type == 'timesuggestion')
					{
						if ($participant->email == $event->creatorId && $feedMessage->senderId != $event->creatorId) // creator should get an alert
						{
							$senderLookup = new Participant();
							$senderList = $senderLookup->GetList( array(array("email", "=", $feedMessage->senderId) ) );
							if (count($senderList) > 0)
							{
								$sender = $senderList[0];
								$senderName = $this->getFriendlyName($sender);
								$message->setText($senderName . ' suggests ' . $this->getFormattedTime($feedMessage->message, $event->eventTimeZone) . ' as an alternate event time.');
							}
						}
					}
						
					$message->setCustomProperty('messageType', 'feed');
						
					// Play the default sound
					if ($device->pushSound == 'enabled') $message->setSound();
						
					if ($device->isSandbox) // determine which queue to add to
					{
						$doSendSandMessages = true;
						$sandPush->add($message);
					}
					else
					{
						$doSendProdMessages = true;
						$prodPush->add($message);
					}
						
				}
			}
		}

		// Send all messages in the message queue
		if ($doSendProdMessages)
		{
			// Connect to the Apple Push Notification Service
			$prodPush->connect();
			// Send all messages in the message queue
			$prodPush->send();
			// Disconnect from the Apple Push Notification Service
			$prodPush->disconnect();
		}

		// Send all messages in the message queue
		if ($doSendSandMessages)
		{
			// Connect to the Apple Push Notification Service
			$sandPush->connect();
			// Send all messages in the message queue
			$sandPush->send();
			// Disconnect from the Apple Push Notification Service
			$sandPush->disconnect();
		}
	}

	/**
	 * Processes the Queued Event Decided Notifications
	 */
	function checkEventDecidedStatus()
	{
		/** @var $lookup PushDispatch */
		$queue = $this->getQueue();
		$events = preg_split('/,/', $queue->decidedNotificationDispatchEventIdList, NULL, PREG_SPLIT_NO_EMPTY);

		/** @var $lookup Event */
		$lookup = new Event();
		$didFindOneToDispatch = false;
		$ts = date('Y-m-d H:i:s', microtime(true) - 60);
		$indexCount = count($events);

		for ($i=0; $i<$indexCount; $i++)
		{
			/** @var $event Event */
			$event = $lookup->Get($events[$i]);
				
			if ($event->cancelled == 1) // remove any cancelled events from decided queue
			{
				$didFindOneToDispatch = true;
				unset($events[$i]);
				continue;
			}
				
			if ($this->eventShouldDispatchDecided($event))
			{
				$didFindOneToDispatch = true;
				unset($events[$i]);
				echo "create DECIDED feed notification for event: " . $event->eventTitle . PHP_EOL;
				echo "create DECIDED feed notification for event with id: " . $event->eventId . PHP_EOL;
					
				if ( count($event->GetLocationList( array( array("hasBeenRemoved", "=", 0) ) )) > 0) // make sure there are locations
				{
					// Add the decided feed message to the queue
					$message = new FeedMessage();
					$message->timestamp = $ts;
					$message->type = 'decided';
					$message->senderId = 'weego';
					$message->message = 'replace with decided content on client';

					$event->AddFeedmessage($message);
					$event->timestamp = $ts;
					$event->Save(true);

					$this->addFeedMessageToQueue($message);
						
					$inviteService = new InviteService();
					$inviteService->dispatchEventDecidedEmailForEvent($event);
				}
			}
		}
		if ($didFindOneToDispatch)
		{
			echo 'events array: ' . print_r($events) . PHP_EOL;
			echo 'final decidedNotificationDispatchEventIdList: ' . implode(',', $events) . PHP_EOL;
			$queue->decidedNotificationDispatchEventIdList = implode(',', $events);
			$queue->Save();
		}
	}


	/**
	 * Processes the Queued Event Start Notifications
	 */
	//TODO Fix this shit
	function dispatchQueuedEventStartNotifications()
	{
		/** @var $lookup PushDispatch */
		$queue = $this->getQueue();
		$events = preg_split('/,/', $queue->startedNotificationDispatchEventIdList, NULL, PREG_SPLIT_NO_EMPTY);
		$lookup = new Event();
		$didFindOneToDispatch = false;
		$indexCount = count($events);
		
		// get the production and sandbox ApnsPHP_Push objects
		$prodPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION);
		$sandPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX);

		$doSendProdMessages = false;
		$doSendSandMessages = false;

		for ($i=0; $i<$indexCount; $i++)
		{
			
			/** @var $event Event */
			$event = $lookup->Get($events[$i]);
			
			if ($event->cancelled == 1) // remove any cancelled events from started queue
			{
				$didFindOneToDispatch = true;
				unset($events[$i]);
				continue;
			}
				
			if (!$this->eventShouldDispatch($event)) continue; // skips to the next if event does not meet time requirement
			$didFindOneToDispatch = true;
			unset($events[$i]);
			echo 'event dispatch start notification: ' . $event->eventTitle . PHP_EOL;
				
			// for each participant...
			$participants = $event->GetParticipantList();
			
			echo 'event participants count: ' . count($participants) . PHP_EOL;
			
			for ($j=0; $j<count($participants); $j++)
			{
				/** @var $participant Participant */
				$participant = $participants[$j];

				// skip user if they have removed the event
				if ($this->getHasRemovedEvent($event, $participant->participantId)) 
				{
					echo 'participant: ' . $participant->email . ' removed the event, continue.' . PHP_EOL;
					continue;
				}

				// skip user if they have not accepted the event
				echo 'participant accepted event : ' . $event->eventTitle . ' -- ' . $this->getHasAcceptedEvent($event, $participant->email) . PHP_EOL;
				if (!$this->getHasAcceptedEvent($event, $participant->email)) 
				{
					echo 'participant: ' . $participant->email . ' did not accept event, continue.' . PHP_EOL;
					continue;
				}
				
				// add the user to the queue so any other messages get ignored and this one is delivered
				array_push($this->queuedUpUserIDCollection, $participant->email);

				echo 'checking user for devices: ' . $participant->email . PHP_EOL;

				// for each device, generate message and add to appropriate push queue
				$devices = $participant->GetDeviceList();
				for ($k=0; $k<count($devices); $k++)
				{
					/** @var $device Device */
					$device = $devices[$k];
						
					if ($device->pushAlert == 'enabled')
					{
						// construct the message content
						$messageString = $event->eventTitle . " is starting.";

						// construct the message
						$message = new ApnsPHP_Message_Custom($device->deviceToken);
						$message->setText($messageString);
						// Play the default sound
						if ($device->pushSound == 'enabled') $message->setSound();
						// Set the "View" button title.
						$message->setActionLocKey('View');
						$message->setCustomProperty('messageType', 'upcoming');

						if ($device->pushBadge == 'enabled')
						{
							$badgeCount = $device->badgeCount;
							$newBadgeCount = $badgeCount + 1;
							$device->badgeCount = $newBadgeCount;
							$device->Save();
							$message->setBadge($newBadgeCount);
						}

						if ($device->isSandbox) // determine which queue to add to
						{
							$doSendSandMessages = true;
							$sandPush->add($message);
								
							echo 'adding: ' . $device->deviceName . ' to queue' . PHP_EOL;
						}
						else
						{
							$doSendProdMessages = true;
							$prodPush->add($message);
						}
					}
				}
			}
		}
		
		if ($didFindOneToDispatch)
		{
			echo 'events array: ' . print_r($events) . PHP_EOL;
			echo 'final startedNotificationDispatchEventIdList: ' . implode(',', $events) . PHP_EOL;
			$queue->startedNotificationDispatchEventIdList = implode(',', $events);
			$queue->Save();
		}
		
		// Send all messages in the message queue
		if ($doSendProdMessages)
		{
			// Connect to the Apple Push Notification Service
			$prodPush->connect();
			// Send all messages in the message queue
			$prodPush->send();
			// Disconnect from the Apple Push Notification Service
			$prodPush->disconnect();
		}

		// Examine the error message container
		/* this is how we could log errors
		$aErrorQueue = $prodPush->getErrors();
		if (!empty($aErrorQueue)) {
		var_dump($aErrorQueue);
		}
		*/

		// Send all messages in the message queue
		if ($doSendSandMessages)
		{
			// Connect to the Apple Push Notification Service
			$sandPush->connect();
			// Send all messages in the message queue
			$sandPush->send();
			// Disconnect from the Apple Push Notification Service
			$sandPush->disconnect();
		}
	}

	/**
	 * Processes the Queued Invite Notifications
	 */
	function dispatchQueuedInviteNotifications()
	{
		$queue = $this->getQueue();
		$invites = $queue->GetInviteList();

		// delete all the invites
		$inviteList = array();
		$queue->SetInviteList($inviteList);
		$queue->lastDispatch = date('Y-m-d H:i:s', microtime(true));
		$queue->Save();

		$participantLookup = new Participant();

		// get the production and sandbox ApnsPHP_Push objects
		$prodPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION);
		$sandPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX);

		$doSendProdMessages = false;
		$doSendSandMessages = false;

		for ($i=0; $i<count($invites); $i++)
		{
			/** @var $invite Invite */
			$invite = $invites[$i];
				
			$lookup = new Event();
			/** @var $event Event */
			$event = $lookup->Get($invite->eventId);
			$inviterList = $participantLookup->GetList( array( array("email", "=", $invite->inviterId ) ) );
			$participants = $event->GetParticipantList();
				
			if(count($inviterList) < 1) continue; // couldnt find participant
				
			/** @var $inviter Participant */
			$inviter = $inviterList[0];
				
			for($k=0; $k<count($participants); $k++)
			{
				/** @var $targetedUser Participant */
				$targetedUser = $participants[$k];

				if ($targetedUser->email == $event->creatorId) continue; // skip creator

				$isInvitee = $targetedUser->email == $invite->inviteeId;
				if ($isInvitee) {
					array_push($this->queuedUpUserIDCollection, $invite->inviteeId);
				} else {
					// skip user if they are in queue to receive an invite, so they do not get over messaged
					// upon event creation
					if (in_array($targetedUser->email, $this->queuedUpUserIDCollection)) continue;
				}

				// for each device, generate message and add to appropriate push queue
				$devices = $targetedUser->GetDeviceList();
				for ($j=0; $j<count($devices); $j++)
				{
					/** @var $device Device */
					$device = $devices[$j];
						
					// construct the message content
					$sendersName = $this->getFriendlyName($inviter);
					$messageString = "You have been invited to " . $event->eventTitle . " by " . $sendersName . ". Help decide where to go!";
						
					// construct the message
					$message = new ApnsPHP_Message($device->deviceToken);
					if ($isInvitee && $device->pushAlert == 'enabled')	$message->setText($messageString);
					$message->setCustomProperty('messageType', 'invite');
					// Play the default sound
					if ($device->pushSound == 'enabled') $message->setSound();

					if ($device->pushBadge == 'enabled')
					{
						$badgeCount = $device->badgeCount;
						$newBadgeCount = $badgeCount + 1;
						$device->badgeCount = $newBadgeCount;
						$device->Save();
						$message->setBadge($newBadgeCount);
					}
						
						
					if ($device->isSandbox) // determine which queue to add to
					{
						$doSendSandMessages = true;
						$sandPush->add($message);
					}
					else
					{
						$doSendProdMessages = true;
						$prodPush->add($message);
					}
				}
			}
		}
		// Send all messages in the message queue
		if ($doSendProdMessages)
		{
			// Connect to the Apple Push Notification Service
			$prodPush->connect();
			// Send all messages in the message queue
			$prodPush->send();
			// Disconnect from the Apple Push Notification Service
			$prodPush->disconnect();
		}

		// Examine the error message container
		/* this is how we could log errors
		$aErrorQueue = $prodPush->getErrors();
		if (!empty($aErrorQueue)) {
		var_dump($aErrorQueue);
		}
		*/

		// Send all messages in the message queue
		if ($doSendSandMessages)
		{
			// Connect to the Apple Push Notification Service
			$sandPush->connect();
			// Send all messages in the message queue
			$sandPush->send();
			// Disconnect from the Apple Push Notification Service
			$sandPush->disconnect();
		}
		
	}

	/**
	 * Processes the Queued Event Start Notifications
	 */
	function dispatchQueuedEventUpdateNotifications()
	{
		/** @var $lookup PushDispatch */
		$queue = $this->getQueue();
		$events = preg_split('/,/', $queue->generalEventUpdateIdList, NULL, PREG_SPLIT_NO_EMPTY);
		$queue->generalEventUpdateIdList = '';
		$queue->Save();

		/** @var $lookup Event */
		$lookup = new Event();

		// get the production and sandbox ApnsPHP_Push objects
		$prodPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION);
		$sandPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX);

		$doSendProdMessages = false;
		$doSendSandMessages = false;

		for ($i=0; $i<count($events); $i++)
		{
			if (strlen($events[$i]) == 0) continue;
			/** @var $event Event */
			$event = $lookup->Get($events[$i]);
			// for each participant...
			$participants = $event->GetParticipantList();
				
			for ($j=0; $j<count($participants); $j++)
			{
				/** @var $participant Participant */
				$participant = $participants[$j];

				// skip user if they are in queue to receive an invite, so they do not get over messaged
				if (in_array($participant->email, $this->queuedUpUserIDCollection)) continue;
				array_push($this->queuedUpUserIDCollection, $participant->email);

				// for each device, generate message and add to appropriate push queue
				$devices = $participant->GetDeviceList();
				for ($k=0; $k<count($devices); $k++)
				{
					/** @var $device Device */
					$device = $devices[$k];

					// construct the message
					$message = new ApnsPHP_Message($device->deviceToken);
					// Play the default sound
					// if ($device->pushSound == 'enabled') $message->setSound();
					// Set the "View" button title.
					$message->setCustomProperty('messageType', 'refresh');
						
					if ($device->pushBadge == 'enabled')
					{
						$badgeCount = $device->badgeCount;
						if ($badgeCount > 0) $message->setBadge(intval($badgeCount));
					}
						
					if ($device->isSandbox) // determine which queue to add to
					{
						$doSendSandMessages = true;
						$sandPush->add($message);
					}
					else
					{
						$doSendProdMessages = true;
						$prodPush->add($message);
					}

				}
			}
		}
		// Send all messages in the message queue
		if ($doSendProdMessages)
		{
			// Connect to the Apple Push Notification Service
			$prodPush->connect();
			// Send all messages in the message queue
			$prodPush->send();
			// Disconnect from the Apple Push Notification Service
			$prodPush->disconnect();
		}

		// Examine the error message container
		/* this is how we could log errors
		$aErrorQueue = $prodPush->getErrors();
		if (!empty($aErrorQueue)) {
		var_dump($aErrorQueue);
		}
		*/

		// Send all messages in the message queue
		if ($doSendSandMessages)
		{
			// Connect to the Apple Push Notification Service
			$sandPush->connect();
			// Send all messages in the message queue
			$sandPush->send();
			// Disconnect from the Apple Push Notification Service
			$sandPush->disconnect();
		}
	}

	/**
	 * Does the time calculation to determine if an event notification should be sent out
	 * @param Event $event
	 * @return boolean
	 */
	//TODO Fix this shit
	function eventShouldDispatch(&$event)
	{
		$now = new DateTime();
		$nowTs = $now->getTimestamp();

		$eventTime = new DateTime($event->eventDate);
		$eventTs =  $eventTime->getTimestamp();

		$timeUntilStart = ceil( ($eventTs - $nowTs) / 60);

		/*
		 echo 'nowTs: ' . $nowTs . PHP_EOL;
		echo 'eventTs: ' . $eventTs . PHP_EOL;
		*/

		// event must be just about started
		return $timeUntilStart < 2;
	}

	/**
	 * Does the time calculation to determine if an event DECIDED notification should be sent out
	 * @param Event $event
	 * @return boolean
	 */
	function eventShouldDispatchDecided(&$event)
	{
		$now = new DateTime();
		$eventExpireTime = new DateTime($event->eventExpireDate);

		$nowTs = $now->getTimestamp();
		$eventExpireTs =  $eventExpireTime->getTimestamp();

		return $nowTs > $eventExpireTs;
	}

	/**
	 * Returns a firendly name string
	 * @param Participant $participant
	 * @return string
	 */
	function getFriendlyName(&$participant)
	{
		$fName = $participant->firstName;
		$lName = $participant->lastName;
		$hasFName = strlen($fName) > 0;
		$hasLName = strlen($lName) > 0;
		if ($hasFName && $hasLName)
		{
			return $fName . " " . $lName;
		}
		else if ($hasFName)
		{
			return $fName;
		}
		else if ($hasLName)
		{
			return $lName;
		}
		else
		{
			return $participant->email;
		}
	}

	/**
	 * Returns a ApnsPHP_Push object to send messages
	 * @param integer $env
	 * @return ApnsPHP_Push
	 */
	function getPushQueue($env)
	{
		// Instanciate a new ApnsPHP_Push object
		$cert = $env == ApnsPHP_Abstract::ENVIRONMENT_SANDBOX ? '../../certs/server_certificates_bundle_sandbox.pem' : '../../certs/server_certificates_bundle_production.pem';
		$push = new ApnsPHP_Push(
		$env,
		$cert
		);
		// Set the Root Certificate Autority to verify the Apple remote peer
		$push->setRootCertificationAuthority('../../certs/entrust_root_certification_authority.pem');

		return $push;
	}

	/**
	 * Adds the Event to the queue to notify all participants that something has changed
	 * @param Event $event
	 * @return
	 */
	function triggerClientUpdateForEvent(&$event)
	{
		$queue = $this->getQueue();
		$generalEventUpdateIdList = preg_split('/,/', $queue->generalEventUpdateIdList, NULL, PREG_SPLIT_NO_EMPTY);
		array_push($generalEventUpdateIdList, $event->eventId);
		$queue->generalEventUpdateIdList = implode(',', $generalEventUpdateIdList);
		$queue->Save();
	}

	/**
	 * Adds the FeedMessage to the queue to be dispatched asap
	 * @param FeedMessage $feedmessage
	 * @return
	 */
	function addFeedMessageToQueue(&$feedmessage)
	{
		$queue = $this->getQueue();
		$queue->AddFeedmessage($feedmessage);
		$queue->Save();
	}

	/**
	 * Adds the Event to the queue to be dispatched at appropriate time
	 * @param Event $event
	 * @return
	 */
	function addEventToQueue(&$event)
	{
		$queue = $this->getQueue();
		
		// add the event to dispatch the started notification
		$startedNotificationDispatchEventIdList = preg_split('/,/', $queue->startedNotificationDispatchEventIdList, NULL, PREG_SPLIT_NO_EMPTY);
		array_push($startedNotificationDispatchEventIdList, $event->eventId);
		$queue->startedNotificationDispatchEventIdList = implode(',', $startedNotificationDispatchEventIdList);

		// add the event to dispatch the decided notification
		$decidedNotificationDispatchEventIdList = preg_split('/,/', $queue->decidedNotificationDispatchEventIdList, NULL, PREG_SPLIT_NO_EMPTY);
		array_push($decidedNotificationDispatchEventIdList, $event->eventId);
		$queue->decidedNotificationDispatchEventIdList = implode(',', $decidedNotificationDispatchEventIdList);

		$queue->Save();
	}

	/**
	 * Adds the invite to the queue to be dispatched asap
	 * @param Invite $invite
	 * @return
	 */
	function addInviteToQueue(&$invite)
	{
		$queue = $this->getQueue();
		$queue->AddInvite($invite);
		$queue->Save();
	}

	/**
	 * Gets the queue if it exists, creates if not
	 * @return PushDispatch
	 */
	function getQueue()
	{
		$lookup = new PushDispatch();
		$queueList = $lookup->GetList();
		$queue;
		if (count($queueList) == 0)
		{
			$queue = new PushDispatch();
			$queue->Save();
		}
		else
		{
			$queue = $queueList[0];
		}
		return $queue;
	}

	/**
	 * Determines if the user has accepted the event
	 * @param Event $event
	 * @param string $email
	 * @return Boolean
	 */
	function getHasAcceptedEvent(&$event, $email)
	{
		$acceptedParticipantList = preg_split('/,/', $event->acceptedParticipantList, NULL, PREG_SPLIT_NO_EMPTY);		
		$hasAccepted = in_array($email, $acceptedParticipantList);
		
		return $hasAccepted;
	}

	/**
	 * Determines if the user has removed the event
	 * @param Event $event
	 * @param string $id
	 * @return Boolean
	 */
	function getHasRemovedEvent(&$event, $id)
	{
		$removedParticipantList = preg_split('/,/', $event->removedParticipantList, NULL, PREG_SPLIT_NO_EMPTY);
		$hasRemoved = in_array($id, $removedParticipantList);

		return $hasRemoved;
	}

	/**
   	* Get the formatted event time
   	* @param string $eventDate
   	* @param string $eventTimeZone
   	* @return string
   	*/
   	function getFormattedTime($eventDate, $eventTimeZone=null)
   	{
   		$isRFC822Timezone = intval($eventTimeZone) != 0;
   		if ($isRFC822Timezone) // new style, RFC 822 Timezone
   		{
   			// for php to properly parse the timezone, a + must be added to positive values
   			$char0 = substr($eventTimeZone, 0, 1);
   			if ($char0 != '+' && $char0 != '-') $eventTimeZone = '+' . $eventTimeZone;
   			// now apply the timezone offset to the event date
   			$eventTimeGMT = new DateTime($eventDate . ' GMT');
   			$eventTimeTZ = new DateTime($eventDate . ' ' . $eventTimeZone);
   			
   			$ts1 = $eventTimeGMT->getTimestamp();
			$ts2 = $eventTimeTZ->getTimestamp();
			
			$diff1 = $ts1 - $ts2;
			$d2 = $ts1 + $diff1;
			
			$isDaylightSavings = $eventTimeTZ->format('I');
			$tz = TimeZoneUtil::getPHPTimeZoneNameForOffset($eventTimeZone, $isDaylightSavings);
			
			$formattedDate = date('D, M j g:i A', $d2) . ' ' . (($tz) ? $tz : $eventTimeZone);
			
			return $formattedDate;
   		}
   		else
   		{
   			$tz = TimeZoneUtil::getPHPTimeZoneStampForAbbreviation($eventTimeZone);
   			$eventTime = new DateTime($eventDate);
   			if ($tz) $eventTime->setTimezone(new DateTimeZone($tz));
   			$dateStr = $eventTime->format('D, M j g:i A') . ' ' . (($tz) ? $eventTimeZone : 'GMT');
   			return $dateStr;
   		}
   	}
}

?>