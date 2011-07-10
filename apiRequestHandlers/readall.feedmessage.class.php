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

class ReadAllFeedMessages extends ReqBase
{
	public $dataObj;
		
	private $requiredFields = array('registeredId', 'eventId');
//	private $allFields = array();
	
	function ReadAllFeedMessages()
	{
		parent::__construct();
	}
	
	function ReadAllFeedMessagesGo()
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
		
		$foundMessages = $event->GetFeedmessageList();
		
		// for each feed message mark them as read if they have not already been read
		for ($i=0; $i<count($foundMessages); $i++)
		{
			$feedMessage = $foundMessages[$i];
			$readParticipantList = explode(',', $feedMessage->readParticipantList);
			$hasRead = in_array($me->participantId, $readParticipantList);
			if (!$hasRead)	
			{
				array_push($readParticipantList, $me->participantId);
				$feedMessage->readParticipantList = implode(",", $readParticipantList);
				$feedMessage->Save();
			}
		}
		
		$event->timestamp = $this->getTimeStamp();
		$event->Save(true);
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::FeedMessageReadSuccess);
			die();
		}
	}
}
?>