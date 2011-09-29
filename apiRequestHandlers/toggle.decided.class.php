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

class ToggleDecidedClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'eventId');
	
	function ToggleDecidedClass()
	{
		parent::__construct();
	}
	function ToggleDecidedGo()
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
		
		// check to ensure I created this event.
		$this->validateUserCreatedEvent($event, $me->email);
		
		
		$eventVotingCurrentlyDisabled = $event->forcedDecided || $this->checkForEventDecidedState($event);
		
		$event->forcedDecided = !$eventVotingCurrentlyDisabled;
		$event->timestamp = $this->getTimeStamp();
		$event->infoTimestamp = $this->getTimeStamp();
		$event->Save();
		
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
	
	/**
	* Checks to see if the event is decided
	* @param Event $event
	* @return boolean
	*/
	function checkForEventDecidedState(&$event)
	{
		$now = new DateTime();
		$eventDecidedDate = new DateTime($event->eventExpireDate);
	
		$nowTs = $now->getTimestamp();
		$eventDecidedTs =  $eventDecidedDate->getTimestamp();
	
		return $nowTs > $eventDecidedTs;
	}
	
}

?>