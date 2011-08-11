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
require_once 'feedmessage.class.php';

class Checkin extends ReqBase
{
	public $dataObj;
		
	private $requiredFields = array('registeredId', 'eventId', 'locationId');
//	private $allFields = array();
	
	function Checkin()
	{
		parent::__construct();
	}
	
	function CheckinGo()
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
		
		$lookupLoc = new Location();
		$location = $lookupLoc->Get($this->dataObj['locationId']);
		
		if(!$location)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'locationId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to mark a message.
		$this->validateUserPartOfEvent($event, $me->email);
		
		// check to see if I have checked in, if not check me in to the event
		$checkedInParticipantList = explode(',', $event->checkedInParticipantList);
		$hasCheckedIn = in_array($me->participantId, $checkedInParticipantList);
		if (!$hasCheckedIn)	
		{
			array_push($checkedInParticipantList, $me->participantId);
			$event->checkedInParticipantList = implode(",", $checkedInParticipantList);
			
			// need to send a feed message that will alert the users that the participant has arrived
			$message = new FeedMessageClass();
			$this->dataObj['type'] = FeedMessageClass::TYPE_SYSTEM_CHECKIN;
			$this->dataObj['message'] = 'Checked-in to ' . $location->name;
			$message->dataObj = $this->dataObj;
			$message->FeedMessageGo();
		}
		
		//$event->timestamp = $this->getTimeStamp();
		//$event->Save();
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::CheckinSuccess, $event->eventId);
			die();
		}
	}
	/**
	* Returns a firendly name string
	* @param Participant $participant
	* @return string
	*/
	function getFriendlyName(&$participant)
	{
		$fName = $participant->firstName;
		$lName = $participant->lastName;
		$hasFName = strlen($fName) > 0;
		$hasLName = strlen($lName) > 0;
		if ($hasFName && $hasLName)
		{
			return $fName . " " . $lName;
		}
		else if ($hasFName)
		{
			return $fName;
		}
		else if ($hasLName)
		{
			return $lName;
		}
		else
		{
			return $participant->email;
		}
	}
	/*
- (NSString *)fullName
{
	NSString *output = [NSString stringWithFormat:@"%@ %@", (!firstName) ? @"" : firstName, (!lastName) ? @"" : lastName];
	if ([[output stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] isEqualToString:@""]) output = email;
	return output;
}
*/
}
?>