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

class DeviceRemoveClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array( 'registeredId', 'deviceUuid' );
	
	function DeviceRemoveClass()
	{
		parent::__construct();
	}
	
	function DeviceRemoveGo()
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
		
		if (!$doSkipResult) // only give success and kill if not called by location class
		{
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::DeviceRemoveSuccess);
			die();
		}
		else 
		{
			return $device;
		}

	}
}

?>