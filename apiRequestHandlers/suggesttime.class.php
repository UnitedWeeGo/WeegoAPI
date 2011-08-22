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
require_once 'feedmessage.class.php';

class SuggestTimeClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'eventId', 'suggestedTime');
	
	function SuggestTimeClass()
	{
		parent::__construct();
	}
	function SuggestTimeGo()
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
		
		/** @var $suggestedTime SuggestedTime */
		$suggestedTime;
		$suggestedTimeList = $event->GetSuggestedtimeList( array(array("email", "=", $me->email) ) );
		
		if (count($suggestedTimeList) == 0) // no existing suggested time for me in this event
		{
			$suggestedTime = new SuggestedTime();
			$event->AddSuggestedtime($suggestedTime);
		}
		else 
		{
			$suggestedTime = $suggestedTimeList[0];
		}
		$suggestedTime->timestamp = $this->getTimeStamp();
		$suggestedTime->email = $me->email;
		$suggestedTime->suggestedTime = $this->dataObj['suggestedTime'];
		$suggestedTime->Save();
		
		$push = new Push();
		// Send out a feed message for the suggested time addition
		$message = new FeedMessage();
		$message->timestamp = $this->getTimeStamp();
		$message->type = FeedMessageClass::TYPE_SYSTEM_EVENT_TIME_SUGGESTION;
		$message->message = $this->dataObj['suggestedTime'];
		$message->senderId = $me->email;
		$message->readParticipantList = $me->participantId;
		
		$event->AddFeedmessage($message);
		$event->timestamp = $this->getTimeStamp();
		$event->Save(true);
		
		$push = new Push();
		$push->addFeedMessageToQueue($message);
		
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
			$xml = $xmlUtil->GetEventXML($event, $me, $userTs);
		}
		else
		{
			$xml = $xmlUtil->GetEventXML($event, $me);
		}
		
		//$push = new Push();
		//$push->triggerClientUpdateForEvent($savedEvent);
		
		$xmlArray = array();
		$xmlArray[0] = $xml;
		$s = new SuccessResponse();
		echo $s->genSuccessWithXMLArray(SuccessResponse::EventPostSuccess, $xmlArray);
	}
	
}

?>