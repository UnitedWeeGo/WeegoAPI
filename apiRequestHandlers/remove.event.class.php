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
require_once '../invite/inviteservice.class.php';

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
		
		$eventShouldBeCancelled = false;
		if ( isset($this->dataObj['cancel'] )) $eventShouldBeCancelled = $this->dataObj['cancel'] == 'true';
		if ($eventShouldBeCancelled)
		{
			$this->validateUserCreatedEvent($event, $me->email); // exits with error upon non-validation
			$event->cancelled = 1;
			$event->timestamp = $this->getTimeStamp();
			$event->infoTimestamp = $this->getTimeStamp();
			$pushdispatchList = array();
			$event->SetPushdispatchList($pushdispatchList); // remove from push dispatch list so notifications don't go out
			$event->Save(true);
			
			$inviteService = new InviteService();
			$inviteService->dispatchEventCancelledEmailForEvent($event);
		}
		else
		{
			$this->markEventRemovedByParticipant($event, $me->participantId);
		}
		
		if ($eventShouldBeCancelled)
		{
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
			
			$push = new Push();
			$push->triggerClientUpdateForEvent($event);
			
			$xmlArray = array();
			$xmlArray[0] = $xml;
			$s = new SuccessResponse();
			echo $s->genSuccessWithXMLArray(SuccessResponse::EventPostSuccess, $xmlArray);
		}
		else
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::EventRemoveSuccess, $event->eventId);
		}
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