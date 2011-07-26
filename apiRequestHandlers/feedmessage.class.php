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

class FeedMessageClass extends ReqBase
{
	const TYPE_SYSTEM = 'system';
	const TYPE_SYSTEM_CHECKIN = 'checkin';
	const TYPE_SYSTEM_DECIDED = 'decided';
	const TYPE_SYSTEM_LOCATION_ADDED = 'locationadd';
	const TYPE_SYSTEM_INVITE = 'invite';
	const TYPE_SYSTEM_EVENT_UPDATE = 'eventupdate';
	const TYPE_SYSTEM_EVENT_TIME_SUGGESTION = 'timesuggestion';
	const TYPE_USER = 'user';
	
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'eventId');
	private $allFields = array('message', 'imageURL', 'type');
	
	function FeedMessageClass()
	{
		parent::__construct();
	}
	
	function FeedMessageGo()
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
		
		$message = new FeedMessage();
		$message->timestamp = $this->getTimeStamp();
		
		if (isset($this->dataObj['type']))
		{
			$message->type = $this->dataObj['type'];
		}
		else
		{
			$message->type = FeedMessageClass::TYPE_USER;
		}
		
		$contentAvailable = false;
		if (isset($this->dataObj['message']))
		{
			$contentAvailable = true;
			$message->message = $this->dataObj['message'];
		}
		if (isset($this->dataObj['imageURL']))
		{
			$contentAvailable = true;
			$message->imageURL = $this->dataObj['imageURL'];
		}
		if (!$contentAvailable)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::MissingParamError, 'you must specify an image or message parameter');
			die();
		}
		$message->senderId = $me->email;

		if ($message->type == FeedMessageClass::TYPE_USER)	$message->readParticipantList = $me->participantId; // just add me as having read it
		
		$event->AddFeedmessage($message);
		$event->timestamp = $this->getTimeStamp();
		$event->Save(true);
		
		$push = new Push();
		$push->addFeedMessageToQueue($message);
		
		if (!$doSkipResult) // only give success and kill if not called by location class
		{
			// So here we actually just want to process it like a single event request so the client updates the event data
			$events = new GetEvents();
			$events->GetEventsGo();
		}
		else 
		{
			return $message;
		}
	}
}

?>