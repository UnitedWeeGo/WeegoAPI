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

date_default_timezone_set('GMT');

class GetReportLocations extends ReqBase
{
	public $dataObj;
		
	private $requiredFields = array('registeredId', 'eventId');
//	private $allFields = array();
	
	function GetReportLocations()
	{
		parent::__construct();
	}
	
	function GetReportLocationsGo()
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
		
		// check to ensure I am part of this event. I must be a participant of the event to add a message.
		$this->validateUserPartOfEvent($event, $me->email);
		
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
		
		$reportLocations = array();
		
		$eventParticipants = $event->GetParticipantList();
		$reportedLocationLookup = new ReportLocation();
		
		for ($i=0; $i<count($eventParticipants); $i++)
		{
			$participant = $eventParticipants[$i];
			if (!$this->getHasAcceptedEvent($event, $participant->email)) continue; // skip any user that has not accepted the event
			$reportedLocationList = $reportedLocationLookup->GetList( array( array("email", "=", $participant->email) ) );
			if (count($reportedLocationList) > 0)
			{
				/** @var $reportLocation ReportLocation */
				$reportedLocation = $reportedLocationList[0];
				if ($this->locationEligibleForReporting($event, $reportedLocation)) array_push($reportLocations, $reportedLocation);
			}
		}
		
		$xmlUtil = new XMLUtil();
		$xmlArray = array();
		
		if ($userTs)
		{
			for($i=0; $i<count($reportLocations); $i++)
			{
				/** @var $reportLocation ReportLocation */
				$reportLocation = $reportLocations[$i];
				$reportLocationTimestampNumber = $reportLocation->timestamp;
				$reportLocationTimestamp = new DateTime( $reportLocationTimestampNumber );
				
				if ( $userTs < $reportLocationTimestamp)
				{
					$xml = $xmlUtil->GetReportLocationXML($reportLocation);
					array_unshift($xmlArray, $xml);
				}
			}
		}
		else // just give me everything
		{
			for($i=0; $i<count($reportLocations); $i++)
			{
				$reportLocation = $reportLocations[$i];
				$xml = $xmlUtil->GetReportLocationXML($reportLocation);
				array_unshift($xmlArray, $xml);
			}
		}
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccessWithXMLArray(SuccessResponse::ReportLocationGetSuccess, $xmlArray, '', $event->eventId);
			die();
		}
	}
	
	/**
	* Does the time calculation to determine if the reported location should be sent
	* Sends 
	* @param Event $event
	* @param ReportLocation $reportLocation
	* @return boolean
	*/
	function locationEligibleForReporting(&$event, &$reportLocation)
	{
		$now = new DateTime();
		$nowTs = $now->getTimestamp();
		
		$reportedLocDate = new DateTime($reportLocation->timestamp);
		$reportedLocTs = $reportedLocDate->getTimestamp();
	
		$eventTime = new DateTime($event->eventDate);
		$eventTs =  $eventTime->getTimestamp();	
	
		$timeUntilStart = ceil( ($eventTs - $nowTs) / 60);
		$timeSinceLocationReported = ceil( ($reportedLocTs - $nowTs) / 60);
		
		/*
		echo 'nowTs: ' . $nowTs . PHP_EOL;
		echo 'eventTs: ' . $eventTs . PHP_EOL;
		echo 'timeUntilStart: ' . $timeUntilStart . PHP_EOL;
		echo 'timeSinceLocationReported: ' . $timeSinceLocationReported . PHP_EOL;
		*/
		return $timeUntilStart < 120 && $timeUntilStart > -120 && $timeSinceLocationReported > - 180;
	}
	
	/**
	* Determines if the user has accepted the event
	* @param Event $event
	* @param string $email
	* @return Boolean
	*/
	function getHasAcceptedEvent(&$event, $email)
	{
		$acceptedParticipantList = preg_split('/,/', $event->acceptedParticipantList, NULL, PREG_SPLIT_NO_EMPTY);
		$hasAccepted = in_array($email, $acceptedParticipantList);
	
		return $hasAccepted;
	}
	
}
?>