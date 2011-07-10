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
		
		$reportLocations = $event->GetReportlocationList();
		
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
}
?>