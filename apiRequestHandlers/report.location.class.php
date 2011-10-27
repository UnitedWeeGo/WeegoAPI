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
	private $requiredFields = array('registeredId', 'latitude', 'longitude');
	
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
		
		/** @var $reportlocation ReportLocation */
		$reportlocation;
		
		$lookup = new ReportLocation();
		$reportlocations = $lookup->GetList( array( array("email", "=", $me->email) ) );
		
		$hasPrevReportedLoc = false;
		if ( count($reportlocations) > 0 )
		{
			$hasPrevReportedLoc = true;
			$reportlocation = $reportlocations[0];
		}
		else 
		{
			$reportlocation = new ReportLocation();
			$reportlocation->email = $me->email;
		}
		
		$reportlocation->timestamp = $this->getTimeStamp();
		
		$hasDisabledTracking = false;
		
		if ( isset($this->dataObj['disableLocationReporting']) )
		{
			$hasDisabledTracking = $this->dataObj['disableLocationReporting'] == 'true';
			$reportlocation->hasDisabledTracking = $hasDisabledTracking;
		}
		
		if (!$hasDisabledTracking)
		{
			$reportlocation->latitude = $this->dataObj['latitude'];
			$reportlocation->longitude = $this->dataObj['longitude'];
		}
		
		if (!$hasPrevReportedLoc && $hasDisabledTracking)
		{
			// skip save
		}
		else
		{
			$reportlocation->Save();
		}
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$xmlUtil = new XMLUtil();
			$xmlArray = array();
			$reportedLocationXML= $xmlUtil->GetReportLocationXML($reportlocation);
			$xmlArray[0] = $reportedLocationXML;
			
			$s = new SuccessResponse();
			echo $s->genSuccessWithXMLArray(SuccessResponse::ReportLocationSuccess, $xmlArray);
			die();
		}
		else 
		{
			return $reportlocation;
		}
	}
}
?>