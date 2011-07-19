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

class GetParticipantInfo extends ReqBase
{
	public $dataObj;
	private $requiredFields = array('registeredId');
	
	function GetParticipantInfo()
	{
		parent::__construct();
	}
	
	function GetParticipantInfoGo()
	{
		$this->dataObj = $this->genDataObj();
		$this->checkProperties($this->requiredFields, $this->dataObj);

		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		// get all of my events
		$eventArray = $me->GetEventList();
		
		$xmlUtil = new XMLUtil();
		$xmlArray = array();
		$index = 0;
		foreach ($eventArray as $i => $value)
		{
			$event = $eventArray[$i];
			
			$participantsArray = $event->GetParticipantList();
			foreach ($participantsArray as $ii => $value)
			{
				$c_participant = $participantsArray[$ii];
				if (strlen($c_participant->registeredId) > 0) // user is registered
				{
					$xmlArray[$index] = $xmlUtil->GetParticipantXML($c_participant);
					$index++;
				}
			}
			
		}
		
		$s = new SuccessResponse();
		echo $s->genSuccessWithXMLArray(SuccessResponse::ParticipantListGetSuccess, $xmlArray);
	}
}