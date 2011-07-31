<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';
require_once '../util/class.uuid.php';
require_once '../util/XMLUtil.php';

class Register extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array();
	private $allFields = array('email', 'firstName', 'lastName', 'avatarURL', 'facebookId', 'facebookToken');
	
	function Register()
	{
		parent::__construct();
	}
	
	function RegisterGo()
	{		
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);

		$email = $this->dataObj['email'];
		
		$this->checkValidEmail($email);
		
		$existingRegisteredUser = $this->isParticipantRegistered($email);
		
		if ( $existingRegisteredUser ) // user is already registered, error out
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::DuplicateParticipantError, 'user already registered');
			die();
		} 
		else
		{
			// this will replace any invited, but not regisered person in the system
			$lookup = new Participant();
			$existingParticipantsList = $lookup->GetList( array( array("email", "=", $email ) ) );
			
			if( count($existingParticipantsList) > 0)
			{
				$participant = $existingParticipantsList[0]; //  grab the user, should only ever be one...
			}
			else
			{
				$participant = new Participant(); // create a new user
			}
			
			// all checked, save participant, return success
			$uuid=new uuid();
			$uuidString = $uuid->genUUID();
			$participant->registeredId = $uuidString;
			$this->populateObject($this->allFields, $this->dataObj, $participant);
			$participant->timestamp = $this->getTimeStamp();
			
			$participant->Save(true);
			
			$s = new SuccessResponse();
			$xmlUtil = new XMLUtil();
			$xmlArray = array();
			$participantXML= $xmlUtil->GetParticipantWithRuidXML($participant);
			$xmlArray[0] = $participantXML;
			echo $s->genSuccessWithXMLArray(SuccessResponse::RegisterSuccess, $xmlArray);
			die();
		}
	}
}

?>