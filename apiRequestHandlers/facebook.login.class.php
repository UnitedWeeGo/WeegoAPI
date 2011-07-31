<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}
require_once '../objects/class.participant.php';
require_once 'register.class.php';

require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';
require_once '../util/XMLUtil.php';
require_once '../facebook-php-sdk/src/facebook.php';

class FacebookLogin extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('access_token');
	
	function FacebookLogin()
	{
		parent::__construct();
	}
	function FacebookLoginGo()
	{
		$doSkipResult = true;
		
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		$access_token = $this->dataObj['access_token'];
		
		// Create our Application instance
		
		$facebook = new Facebook(array(
		  'appId'  => '221300981231092',
		  'secret' => '9670bee46bf64a4e52a86716df51a8dc',
		));
		$facebook->setAccessToken($access_token);
		
		// Get User ID
		$user = $facebook->getUser();
		
		if ($user) {
		  try {
		    // Proceed knowing you have a logged in user who's authenticated.
		    $user_profile = $facebook->api('/me');
		  } catch (FacebookApiException $e) {
		    error_log($e);
		    $user = null;
		  }
		}
		else 
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidCredentialsError, 'Facebook lookup failed.');
			die();
		}
		$email = $user_profile['email'];
		$fb_id = $user_profile['id'];
		$avatarURL = 'http://graph.facebook.com/' . $fb_id . '/picture';
		
		/** @var $existingRegisteredUser Participant */
		$existingRegisteredUser = $this->isParticipantRegisteredWithFacebookId($fb_id);
				
		$firstName = isset($user_profile['first_name']) ? $user_profile['first_name'] : null;
		$lastName = isset($user_profile['last_name']) ? $user_profile['last_name'] : null;
		
		if (!$existingRegisteredUser)
		{
			// user does not exist, register them
			$register = new Register();
			$regObj = array();
			$regObj['email'] = $email;
			if ($firstName) $regObj['firstName'] = $firstName;
			if ($lastName) $regObj['lastName'] = $lastName;
			$regObj['facebookId'] = $fb_id;
			$regObj['avatarURL'] = $avatarURL;
			$regObj['facebookToken'] = $access_token;
			
			$register->dataObj = $regObj;
			$register->RegisterGo();
			// will return successful registration to user and die
		}
		// should update the existing user with any new information
		if ($firstName) $existingRegisteredUser->firstName = $firstName;
		if ($lastName) $existingRegisteredUser->lastName = $lastName;
		$existingRegisteredUser->email = $email;
		$existingRegisteredUser->facebookId = $fb_id;
		$existingRegisteredUser->avatarURL = $avatarURL;
		$existingRegisteredUser->timestamp = $this->getTimeStamp();
		$existingRegisteredUser->facebookToken = $access_token;
		$existingRegisteredUser->Save();
		
		if (!$doSkipResult) // only give success and kill if not called by post class
		{
			// registered and authenticated, respond with success
			$s = new SuccessResponse();
			$xmlUtil = new XMLUtil();
			$xmlArray = array();
			$participantXML= $xmlUtil->GetParticipantWithRuidXML($existingRegisteredUser);
			$xmlArray[0] = $participantXML;
		
			echo $s->genSuccessWithXMLArray(SuccessResponse::LoginSuccess, $xmlArray);
			die();
		}
		else
		{
			return $existingRegisteredUser;
		}
	}
}
?>