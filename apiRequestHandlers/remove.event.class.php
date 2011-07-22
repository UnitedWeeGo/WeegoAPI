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

require_once 'acceptevent.class.php';
require_once '../push/class.push.php';

class RemoveEventClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'eventId');
	
	function RemoveEventClass()
	{
		parent::__construct();
	}
	function RemoveEventGo()
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
		
		$shouldCountOutUser = false;
		if ( isset($this->dataObj['countMeOut'] )) $shouldCountOutUser = $this->dataObj['countMeOut'] == 'true';
		
		if ($shouldCountOutUser)
		{
			$aec = new AcceptEventClass(); // not setting didAccept in data obj will default to false
			$aec->dataObj = $this->dataObj;
			$aec->dataObj['didAccept'] = 'false';
			$aec->AcceptEventGo();
		}
		
		$this->markEventRemovedByParticipant($event, $me->participantId);
		
		/* maybe enable this later, not sure yet
		$push = new Push();
		$push->triggerClientUpdateForEvent($event);
		*/
		
		$s = new SuccessResponse();
		echo $s->genSuccess(SuccessResponse::EventRemoveSuccess, $event->eventId);
	}
	
	/**
	* Marks the event removed by a participant
	* @param Event $event
	* @param String $participantId
	* @return DOMDocument
	*/
	function markEventRemovedByParticipant(&$event, $participantId)
	{
		// adding only if user has not previously read
		$removedParticipantList = explode(',', $event->removedParticipantList);
		$hasRemoved = in_array($participantId, $removedParticipantList);
		if (!$hasRemoved)
		{
			array_push($removedParticipantList, $participantId);
			$event->removedParticipantList = implode(",", $removedParticipantList);
			$event->Save();
		}
	}
}

?>