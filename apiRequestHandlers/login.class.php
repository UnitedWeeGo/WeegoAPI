<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}
require_once '../objects/class.participant.php';

require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';
require_once '../util/XMLUtil.php';

class Login extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('email', 'password');
	
	function Login()
	{
		parent::__construct();
	}
	function LoginGo()
	{
		// test for the case that we are calling this from add location
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);

		$email = $this->dataObj['email'];
		$this->checkValidEmail($email);
		
		$existingRegisteredUser = $this->isParticipantRegistered($email);

		if (!$existingRegisteredUser)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidCredentialsError, 'Please check your login credentials, or register.');
			die();
		}
		
		// check the password
		if ($existingRegisteredUser->password != $this->dataObj['password'])
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidCredentialsError, 'Please check your login credentials.');
			die();
		}
		
		// authenticated, respond with success
		$s = new SuccessResponse();
		$xmlUtil = new XMLUtil();
		$xmlArray = array();
		$participantXML= $xmlUtil->GetParticipantWithRuidXML($existingRegisteredUser);
		$xmlArray[0] = $participantXML;
		echo $s->genSuccessWithXMLArray(SuccessResponse::LoginSuccess, $xmlArray);
		die();
	}
}

?>