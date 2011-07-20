<?php

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

// Using Autoload all classes are loaded on-demand
require_once '../../apns-php/ApnsPHP/Autoload.php';

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
					
					if ($device->pushBadge == 'enabled')
					{
						$badgeCount = $device->badgeCount;
						$newBadgeCount = $badgeCount + 1;
						$device->badgeCount = $newBadgeCount;
						$device->Save();
						
						// construct the message
						$message = new ApnsPHP_Message($device->deviceToken);
						$message->setBadge($newBadgeCount);
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
			// remove the event from the queue
			$pushdispatchList = array();
			$event->SetPushdispatchList($pushdispatchList);
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
	function dispatchQueuedEventStartNotifications()
	{
		/** @var $lookup PushDispatch */
		$queue = $this->getQueue();
		$events = $queue->GetEventList();
		
		// get the production and sandbox ApnsPHP_Push objects
		$prodPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION);
		$sandPush = $this->getPushQueue(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX);
		
		$doSendProdMessages = false;
		$doSendSandMessages = false;
		
		for ($i=0; $i<count($events); $i++)
		{
			/** @var $event Event */
			$event = $events[$i];
			
			if (!$this->eventShouldDispatch($event)) continue; // skips to the next if event does not meet time requirement
			
			echo 'event dispatch start notification: ' . $event->eventTitle;
			
			// for each participant...
			$participants = $event->GetParticipantList();
			for ($j=0; $j<count($participants); $j++)
			{
				/** @var $participant Participant */
				$participant = $participants[$j];
				
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
						$messageString = $event->eventTitle . " is starting. Are you coming?";
						
						// construct the message
						$message = new ApnsPHP_Message_Custom($device->deviceToken);
						$message->setText($messageString);
						// Play the default sound
						if ($device->pushSound == 'enabled') $message->setSound();
						// Set the "View" button title.
						$message->setActionLocKey('Yes!');
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
			// remove the event from the queue
			$pushdispatchList = array();
			$event->SetPushdispatchList($pushdispatchList);
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
				$targetedUser = $participants[$k];
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
	function checkEventDecidedStatus()
	{
		/** @var $lookup PushDispatch */
		$queue = $this->getQueue();
		$events = explode(',', $queue->decidedNotificationDispatchEventIdList);
		
		/** @var $lookup Event */
		$lookup = new Event();
		$didFindOneToDispatch = false;
		$ts = date('Y-m-d H:i:s', microtime(true));
		for ($i=0; $i<count($events); $i++)
		{
			/** @var $event Event */
			$event = $lookup->Get($events[$i]);	
			if ($this->eventShouldDispatchDecided($event))
			{
				$didFindOneToDispatch = true;
				unset($events[$i]);
				echo "create DECIDED feed notification for event: " . $event->eventTitle . PHP_EOL;
				
				if ( count($event->GetLocationList()) > 0) // make sure there are locations
				{
					$message = new FeedMessage();
					$message->timestamp = $ts;
					$message->type = 'decided';
					$message->senderId = 'weego';
					$message->message = 'replace with decided content on client';
					
					$event->AddFeedmessage($message);
					$event->timestamp = $ts;
					$event->Save(true);
		
					$this->addFeedMessageToQueue($message);
				}
			}
		}
		if ($didFindOneToDispatch)
		{
			$queue->decidedNotificationDispatchEventIdList = implode(',', $events);
			$queue->Save();
		}
	}
	
	/**
	 * Processes the Queued Event Start Notifications
	 */
	function dispatchQueuedEventUpdateNotifications()
	{
		/** @var $lookup PushDispatch */
		$queue = $this->getQueue();
		$events = explode(',', $queue->generalEventUpdateIdList);
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
			// remove the event from the queue - NO, we should not remove from the queue
			//$pushdispatchList = array();
			//$event->SetPushdispatchList($pushdispatchList);
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
	function eventShouldDispatch(&$event)
	{	
		$now = new DateTime();
		$nowTs = $now->getTimestamp();
		
		$eventTime = new DateTime($event->eventDate);
		$eventTs =  $eventTime->getTimestamp();
		
		$eventExpireTime = new DateTime($event->eventExpireDate);
		$eventExpireTs =  $eventExpireTime->getTimestamp();
		
		
		$timeUntilStart = ceil( ($eventTs - $nowTs) / 60);
		$timeUntilVotingEnds = ceil( ($eventExpireTs - $nowTs) / 60);
		
		echo 'nowTs: ' . $nowTs . PHP_EOL;
		echo 'eventTs: ' . $eventTs . PHP_EOL;
		echo 'timeUntilStart: ' . $timeUntilStart . PHP_EOL;
		echo 'timeUntilVotingEnds: ' . $timeUntilVotingEnds . PHP_EOL . PHP_EOL;
		
		// event must be DECIDED and under 30 minutes away
		return $timeUntilStart < 30 && $nowTs > $eventExpireTs;
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
		$cert = $env == ApnsPHP_Abstract::ENVIRONMENT_SANDBOX ? '../../certs/server_cerificates_bundle_sandbox.pem' : '../../certs/server_cerificates_bundle_prod.pem';
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
		$generalEventUpdateIdList = explode(',', $queue->generalEventUpdateIdList);
		array_push($generalEventUpdateIdList, $event->eventId);
		$queue->generalEventUpdateIdList = implode(',', $generalEventUpdateIdList);
		$queue->Save();
		/*
		$queue = $this->getQueue();
		$generalEventUpdateIdList = explode(',', $queue->generalEventUpdateIdList);
		$inlist = in_array($event->eventId, $generalEventUpdateIdList);
		if (!$inlist) {
			array_push($generalEventUpdateIdList, $event->eventId);
			$queue->generalEventUpdateIdList = implode(',', $generalEventUpdateIdList);
			$queue->Save();
		}*/
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
		$queue->AddEvent($event);
		
		// add the event to dispatch the decided notification
		$decidedNotificationDispatchEventIdList = explode(',', $queue->decidedNotificationDispatchEventIdList);
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
}

?>