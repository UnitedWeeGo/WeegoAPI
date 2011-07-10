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

class ReportLocationClass extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('registeredId', 'latitude', 'longitude', 'eventId');
	
	function ReportLocationClass()
	{
		parent::__construct();
	}
	
	function ReportLocationGo()
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
		
		// check to make sure event exists
		$lookup = new Event();
		$event = $lookup->Get($this->dataObj['eventId']);
				
		if ( !$event )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to invite a participant.
		$this->validateUserPartOfEvent($event, $me->email);
		
		/** @var $reportlocation ReportLocation */
		$reportlocation;
		
		$reportlocations = $event->GetReportlocationList( array( array("email", "=", $me->email) ) );
		if ( count($reportlocations) > 0 )
		{
			$reportlocation = $reportlocations[0];
		}
		else 
		{
			$reportlocation = new ReportLocation();
			$reportlocation->email = $me->email;
		}
		
		$reportlocation->timestamp = $this->getTimeStamp();
		$reportlocation->latitude = $this->dataObj['latitude'];
		$reportlocation->longitude = $this->dataObj['longitude'];
		
		$event->AddReportlocation($reportlocation);		
		$event->Save(true);
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::ReportLocationSuccess);
			die();
		}
		else 
		{
			return $location;
		}
	}
}
?>