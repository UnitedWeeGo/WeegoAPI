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

class AcceptEventClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'eventId', 'didAccept');
	
	function AcceptEventClass()
	{
		parent::__construct();
	}
	
	function AcceptEventGo()
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
			
		$didAccept = $this->dataObj['didAccept'] == 'true';
		$this->setEventAcceptedByParticipant($event, $me->email, $didAccept);
		
		// must update my timestamp so my new accept status will come through
		$event->timestamp = $this->getTimeStamp();
		$event->infoTimestamp = $this->getTimeStamp();
		$event->Save();
		
		if (!$doSkipResult)
		{
			// So here we actually just want to process it like a single event request so the client updates the event data
			$events = new GetEvents();
			$events->GetEventsGo();
		}
		else 
		{
			return $event;
		}
	}
	/**
	* Marks the event accepted or rejected by a participant
	* @param Event $event
	* @param String $email
	* @param Boolean $didAccept
	* @return DOMDocument
	*/
	function setEventAcceptedByParticipant(&$event, $email, $didAccept)
	{
		$declinedParticipantList = explode(',', $event->declinedParticipantList);
		$acceptedParticipantList = explode(',', $event->acceptedParticipantList);
		
		$hasAccepted = in_array($email, $acceptedParticipantList);
		$hasDeclined = in_array($email, $declinedParticipantList);
		
		if ($didAccept)
		{
			if ($hasDeclined) // remove from declined list
			{
				$index = array_search($email, $declinedParticipantList);
				unset($declinedParticipantList[$index]);
			}
			if (!$hasAccepted) // add to accepted list if not already there
			{
				array_push($acceptedParticipantList, $email);
			}
		}
		else // user declining
		{
			if ($hasAccepted) // remove from accepted list
			{
				$index = array_search($email, $acceptedParticipantList);
				unset($acceptedParticipantList[$index]);
			}
			if (!$hasDeclined) // add to declined list if not already there
			{
				array_push($declinedParticipantList, $email);
			}
		}
		$event->acceptedParticipantList = implode(",", $acceptedParticipantList);
		$event->declinedParticipantList = implode(",", $declinedParticipantList);
	}
}

?>