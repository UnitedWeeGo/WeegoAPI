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

class DeviceClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'deviceToken', 'deviceUuid', 'deviceName', 'deviceModel', 'deviceSystemVersion', 'pushBadge', 'pushAlert', 'pushSound', 'isSandbox');
	
	function DeviceClass()
	{
		parent::__construct();
	}
	
	function DeviceGo()
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
		
		$lookup = new Device();
		$existingDevices = $lookup->GetList( array( array("deviceUuid", "=", $this->dataObj['deviceUuid'] ) ) );
		
		if( count($existingDevices) > 0) // device has been added, so delete it
		{
			$device = $existingDevices[0]; // should only be one device with a unique uuid
			$device->Delete();
		}
		
		$device = new Device();
		$device->badgeCount = 0; // device initial badge count
		$me->AddDevice($device);

		$device->deviceModel = $this->dataObj['deviceModel'];
		$device->deviceName = $this->dataObj['deviceName'];
		$device->deviceSystemVersion = $this->dataObj['deviceSystemVersion'];
		$device->deviceToken = $this->dataObj['deviceToken'];
		$device->deviceUuid = $this->dataObj['deviceUuid'];
		$device->pushBadge = $this->dataObj['pushBadge'];
		$device->pushAlert = $this->dataObj['pushAlert'];
		$device->pushSound = $this->dataObj['pushSound'];
		$device->isSandbox = ($this->dataObj['isSandbox'] == "true") ? (1) : (0);
		$device->timestamp = $this->getTimeStamp();
		
		$device->Save();
		$me->Save(); // deep save of participant should update the Device
		
		if (!$doSkipResult) // only give success and kill if not called by location class
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::DeviceAddSuccess);
			die();
		}
		else 
		{
			return $device;
		}

	}
}

?>