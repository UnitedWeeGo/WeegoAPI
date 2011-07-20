<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once '../util/XMLUtil.php';
require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';
require_once '../push/class.push.php';

class EventClass extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('registeredId');
	private $allFields = array('eventTitle', 'eventDate', 'eventDescription', 'guestListOpen', 'locationListOpen');
	
	function EventClass()
	{
		parent::__construct();
	}
	
	function EventGo()
	{
		$doSkipResult = true;
		
		if ($this->dataObj == null) // called from http
		{		
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
//		print_r($this->dataObj);
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
			
		// determine if this is an update or new event
		$isAnUpdate = isset($this->dataObj['eventId']);
		if ($isAnUpdate) $eventId = $this->dataObj['eventId'];
			
		$event;
		if ($isAnUpdate)
		{
			$lookup = new Event();
			$events = $lookup->GetList( array( array("eventId", "=", $eventId), array("creatorId", "=", $me->email) ) );
				
			if ( count($events) == 0 )
			{
				$e = new ErrorResponse();
				echo $e->genError(ErrorResponse::InvalidParamError, 'either you are not the owner of the event, or the event does not exist.');
				die();
			}
			else // event is an update
			{
				$event = $events[0];
				$this->checkForEventExpiration($event); // if event decided, error out
			}
		}
		else
		{
			$event = new Event();
		}
//			print_r($event);
		$eventTimeDidChange = false;
		// check the event expiration 
		if (isset($this->dataObj['eventDate']))
		{
			$eventTimeDidChange = $event->eventDate != $this->dataObj['eventDate'];
			$expirationTimeDidChange = $this->populateExpirationForEvent($event, $this->dataObj['eventDate']);
		}
		
		$eventDetailsDidChange = $this->populateObject($this->allFields, $this->dataObj, $event);
		
		$ts = $this->getTimeStamp();
		
		if (!$isAnUpdate) // only set on a new event
		{
			$me->timestamp = $ts;
			$event->creatorId = $me->email;
			$event->readParticipantList = $me->participantId;
			$event->AddParticipant($me);
			$event->acceptedParticipantList = $me->email; // mark as accepted by creator
			
			$event->Save();
			
			// every new event must be added to the push queue list 
			$push = new Push();
			$push->addEventToQueue($event);
		}

		$event->timestamp = $ts;
		if ($eventDetailsDidChange || $expirationTimeDidChange)	$event->infoTimestamp = $ts;
		$eventId = $event->Save(true);
		
		if ($isAnUpdate) 
		{
			if ($eventTimeDidChange)
			{
				// Send out a feed message for the location addition
				$message = new FeedMessage();
				$message->timestamp = $this->getTimeStamp();
				$message->type = FeedMessageClass::TYPE_SYSTEM;
				$message->message = $event->name . ' time changed!';
				$message->senderId = $me->email;
				$message->readParticipantList = $me->participantId;
				
				$event->AddFeedmessage($message);
				$event->Save(true);
			}

			$push = new Push();
			$push->triggerClientUpdateForEvent($event);
		}
			
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(($isAnUpdate)?(SuccessResponse::EventUpdateSuccess):(SuccessResponse::EventAddSuccess), $eventId);
			die();
		}
		else 
		{
			return $event;
		}
	}
}
?>