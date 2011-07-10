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

class BadgeResetClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'deviceUuid');
	
	function BadgeResetClass()
	{
		parent::__construct();
	}
	
	/**
	* Adds the invite to the queue to be dispatched asap
	* @param Participant $participant
	* @return
	*/
	function resetAllDeviceBadgesForUser($participant)
	{
		$existingDevices = $participant->GetDeviceList();
		for ($i=0; $i<count($existingDevices); $i++)
		{
			$device = $existingDevices[$i]; // should only be one device with a unique uuid'
			$device->badgeCount = 0; // reset badge
			$device->Save();
		}
	}
	
	function BadgeResetGo()
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
		$existingDevices = $me->GetDeviceList( array( array("deviceUuid", "=", $this->dataObj['deviceUuid'] ) ) );
		
		$device;
		if( count($existingDevices) == 0) // device has not been added, so add it
		{
			/*
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'deviceUuid invalid');
			die();
			*/
			// device has not been saved, just do nothing
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::DeviceBadgeResetSuccess);
			die();
		}
		else 
		{
			$device = $existingDevices[0]; // should only be one device with a unique uuid'
			$device->badgeCount = 0; // reset badge
			$device->Save();
		}
		
		if (!$doSkipResult) // only give success and kill if called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::DeviceBadgeResetSuccess);
			die();
		}
		else 
		{
			return $device;
		}

	}
}

?>