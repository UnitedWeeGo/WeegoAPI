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

class ReadFeedMessages extends ReqBase
{
	public $dataObj;
		
	private $requiredFields = array('registeredId', 'eventId', 'messageId');
//	private $allFields = array();
	
	function ReadFeedMessages()
	{
		parent::__construct();
	}
	
	function ReadFeedMessagesGo()
	{
		$doSkipResult = true;
		
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		/** @var $lookup Event */
		$lookup = new Event();
		/** @var $event Event */
		$event = $lookup->Get($this->dataObj['eventId']);
		
		if(!$event)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to mark a message.
		$this->validateUserPartOfEvent($event, $me->email);
		
		$foundMessages = $event->GetFeedmessageList( array(array("feedmessageId", "=", $this->dataObj['messageId'])) );
		if (count($foundMessages) == 0)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'messageId invalid');
			die();
		}
		$feedMessage = $foundMessages[0];
		$unreadParticipantList = explode(',', $feedMessage->readParticipantList);
		$hasRead = in_array($me->participantId, $unreadParticipantList);
		if (!$hasRead)	
		{
			array_push($unreadParticipantList, $me->participantId);
			$feedMessage->readParticipantList = implode(",", $unreadParticipantList);
			$feedMessage->Save();
		}
		else
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'participant send read for message already');
			die();
		}
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::FeedMessageReadSuccess);
			die();
		}
	}
}
?>