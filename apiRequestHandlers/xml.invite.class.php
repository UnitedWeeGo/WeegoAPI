<?php

header ("Content-Type:text/xml");

require_once "../configuration.php";
foreach(glob("../apiRequestHandlers/*.php") as $class_filename) {
	require_once($class_filename);
}


require_once '../util/XMLUtil.php';
require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';

//error_reporting (E_ERROR);

class XMLInviteClass extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('registeredId');
	
	function XMLInviteClass()
	{
		parent::__construct();
	}
	
	function XMLInviteGo()
	{
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
		}
		$this->checkProperties($this->requiredFields, $this->dataObj);

		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		// check for valid xml
		$xml = $this->dataObj['xml'];
		$doc = new DOMDocument('1.0', 'UTF-8');
		$loadSuccess = $doc->loadXML($xml);
		if (!$loadSuccess) $this->xmlStructureError(); // error out and die
		
		$eventXML = $doc->getElementsByTagName( "event" ) -> item(0);
		// at a minimum, an event node must be passed
		if ( !$eventXML ) $this->xmlStructureError(); // error out and die
		
		$eventId = $eventXML->getAttribute('id');
		if ( !$eventId ) $this->xmlStructureError(); // error out and die
		
		$xmlUtil = new XMLUtil();
		
		$eventLookup = new Event();
		$savedEvent = $eventLookup->Get($eventId);
		
		if ( !$savedEvent )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to add a message.
		$this->validateUserPartOfEvent($savedEvent, $me->email);
		
		// Participants -------
		$participantObj;
		$participants = $eventXML->getElementsByTagName( "participant" );
		$nodeListLength = $participants->length;
		for ($i = 0; $i < $nodeListLength; $i++)
		{
			$participantObj = array();
			$participantObj['registeredId'] = $this->dataObj['registeredId'];
			$participantObj['eventId'] = $savedEvent->eventId;
			$node = $participants->item($i);
			$participantObj = $xmlUtil->populateObject($node, $participantObj);
			$participantGenerator = new ParticipantClass();
			$participantGenerator->dataObj = $participantObj;
			$savedEvent = $participantGenerator->ParticipantGo();
		}
		
		$requestId = $eventXML->getAttribute('requestId');
		
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
		
		$savedEvent = $eventLookup->Get($eventId);
		
		if ($nodeListLength > 0)
		{
			// Send out a feed message for the invite
			$message = new FeedMessage();
			$message->timestamp = $this->getTimeStamp();
			$message->type = FeedMessageClass::TYPE_SYSTEM_INVITE;
			$message->message = 'Invited ' . $nodeListLength . ' friend' . ($nodeListLength > 1 ? 's' : '');
			$message->senderId = $me->email;
			$message->readParticipantList = $me->participantId;
			
			$savedEvent->AddFeedmessage($message);
			$savedEvent->timestamp = $this->getTimeStamp();
			$savedEvent->Save(true);
			
			$push = new Push();
			$push->triggerClientUpdateForEvent($savedEvent);
		}
		if ($userTs)
		{
			$xml = $xmlUtil->GetEventXML($savedEvent, $me, $userTs);
		}
		else
		{
			$xml = $xmlUtil->GetEventXML($savedEvent, $me);
		}
		
		$xmlArray = array();
		$xmlArray[0] = $xml;
		$s = new SuccessResponse();
		echo $s->genSuccessWithXMLArray(SuccessResponse::EventPostSuccess, $xmlArray, $requestId);

	}
	function xmlStructureError()
	{
		$e = new ErrorResponse();
		echo $e->genError(ErrorResponse::InvalidXMLError, 'XML was invalid.');
		die();
	}
}