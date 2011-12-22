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

require_once 'get.event.class.php';
require_once '../push/class.push.php';

class DeleteLocationClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'eventId', 'locationId');
	
	function DeleteLocationClass()
	{
		parent::__construct();
	}
	
	function DeleteLocationGo()
	{
		$doSkipResult = true;
		
		// test for the case that we are calling this from add location
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);

		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		// check to make sure event exists
		$lookup = new Event();
		/** @var $event Event */
		$event = $lookup->Get($this->dataObj['eventId']);
		if ( !$event )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to add a message.
		$this->validateUserPartOfEvent($event, $me->email);
		
		/** @var $location Location */
		$locations = $event->GetLocationList( array( array("locationId", "=", $this->dataObj['locationId'] ) ) );
		if ( !$locations )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'locationId invalid');
			die();
		}
		$location = $locations[0];
		
		$iAddedLocation = $location->addedByParticipantId == $me->email;
		if ( !$iAddedLocation )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'you did not add location, so you can\'t delete it');
			die();
		}
		
		if ( $location->hasBeenRemoved )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'location already deleted');
			die();
		}
		
		// mark the location removed
		$location->hasBeenRemoved = 1;
		$location->timestamp = $this->getTimeStamp();
		$location->Save();
		
		// remove any votes for this location
		$vote = new Vote();
		$vote->DeleteList( array( array("locationId", "=", $this->dataObj['locationId'] ) ) );
		
		// must update my timestamp so my new accept status will come through
		$event->timestamp = $this->getTimeStamp();
		$event->locationReorderTimestamp = $this->getTimeStamp();
		$event->Save(true);
		
		$savedEvent = $lookup->Get($this->dataObj['eventId']);
		
		$userTs = null;
		if (isset($this->dataObj['timestamp'])) // hit the method to only do timestamp stuff
		{
			try {
				$userTs = new DateTime($this->dataObj['timestamp']);
			} catch (Exception $e) {
				$e = new ErrorResponse();
				echo $e->genError(ErrorResponse::InvalidTimestampError, 'Timestamp was invalid format, must be 2011-02-10 18:50:06');
				die();
			}
		}
		$xmlUtil = new XMLUtil();
		
		if ($userTs)
		{
			$xml = $xmlUtil->GetEventXML($savedEvent, $me, $userTs);
		}
		else
		{
			$xml = $xmlUtil->GetEventXML($savedEvent, $me);
		}
		
		$message = new FeedMessage();
		$message->timestamp = $this->getTimeStamp();
		$message->type = FeedMessageClass::TYPE_SYSTEM_LOCATION_ADDED;
		$message->message = 'Removed "' . $savedLocation->name . '"';
		$message->senderId = $me->email;
		$message->readParticipantList = $me->participantId;
			
		$savedEvent->AddFeedmessage($message);
		$savedEvent->timestamp = $this->getTimeStamp();
		$savedEvent->Save(true);
		
		$push = new Push();
		$push->triggerClientUpdateForEvent($savedEvent);
		
		$xmlArray = array();
		$xmlArray[0] = $xml;
		$s = new SuccessResponse();
		echo $s->genSuccessWithXMLArray(SuccessResponse::EventPostSuccess, $xmlArray);
	}
}

?>