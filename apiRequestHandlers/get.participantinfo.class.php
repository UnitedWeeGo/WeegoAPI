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
	private $requiredFields = array('registeredId', 'email');
	
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
		$email = $this->dataObj['email'];
		
		$existingRegisteredUser = $this->isParticipantRegistered($email);
		$s = new SuccessResponse();
		if ($existingRegisteredUser)
		{
			// return registered info
			$xmlUtil = new XMLUtil();
			$xmlArray = array();
			$participantXML= $xmlUtil->GetParticipantXML($existingRegisteredUser);
			$xmlArray[0] = $participantXML;
			echo $s->genSuccessWithXMLArray(SuccessResponse::ParticipantRegisteredSuccess, $xmlArray);
		}
		else
		{
			// return not registered //ParticipantNotRegisteredSuccess
			echo $s->genSuccess(SuccessResponse::ParticipantNotRegisteredSuccess);
		}
	}
}