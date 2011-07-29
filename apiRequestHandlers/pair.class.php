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
require_once '../util/class.uuid.php';
require_once '../facebook-php-sdk/src/facebook.php';

date_default_timezone_set('GMT');

class Pair extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('fb_token', 'pair_token');
	
	function Pair()
	{
		parent::__construct();
	}
	function PairGo()
	{
		$this->dataObj = $this->genDataObj();
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		// check to make sure pair token is correct length
		$pair_token = $this->dataObj['pair_token'];
		if (strlen($pair_token) != 36)
		{
			echo '{"status" : "ERROR", "message" : "Pair token invalid"}';
			die();
		}
		
		// get the user profile object
		$user_profile = $this->getFBUserProfile();
		
		$validEmailAddress = $user_profile['email'];
		$fb_id = $user_profile['id'];
		
		// get the Invite list for user with email address
		$inviteList = $this->getInviteList();
		if (count($inviteList) == 0)
		{
			//echo "No invites found. Exiting." . PHP_EOL;
			die();
		}
		
		// for (each invite with invitee email == invitee email)
		for ($i=0; $i<count($inviteList); $i++)
		{
			// check to see if user is registered
			$existingRegisteredUser = $this->isParticipantRegistered($validEmailAddress);
			if(!$existingRegisteredUser) $existingRegisteredUser = $this->isParticipantRegisteredWithFacebookId($fb_id);
			
			$invite = $inviteList[$i];
			$invalidEmailAddress = $invite->inviteeId;
			// get the associated event
			$event = $invite->GetEvent();
			$event->timestamp = $this->getTimeStamp();
			$event->Save(true);
			
			if (!$existingRegisteredUser)
			{
				//echo "User is NOT registered." . PHP_EOL;
				
				// delete the invalid participant from the system
				$this->deleteInvalidParticipant($invalidEmailAddress);
				// register the user
				$newParticipant = $this->createNewParticipant($user_profile);
				// clean the invite, user not added previously
				$this->cleanInvite($invite, false, $validEmailAddress);
				// add the valid participant to the event
				$this->addParticipantToEvent($newParticipant, $event);
				// add alternate email to valid participant
				$this->addAlternateEmail($newParticipant, $invalidEmailAddress);
				continue;
			}
			
			// Check if user is added to the event
			$userAddedToEvent = $this->validateUserPartOfEvent($event, $validEmailAddress);
			
			if (!$userAddedToEvent)
			{
				//echo "User is registered, but NOT added to the event." . PHP_EOL;
				// clean the invite, user not added previously
				$this->cleanInvite($invite, false, $validEmailAddress);
				// delete the invalid participant from the system
				$this->deleteInvalidParticipant($invalidEmailAddress);
				// add the valid participant to the event
				$this->addParticipantToEvent($existingRegisteredUser, $event);
				// add alternate email to valid participant
				$this->addAlternateEmail($existingRegisteredUser, $invalidEmailAddress);
				continue;
			}
			else
			{
				//echo "User is registered, and added to the event." . PHP_EOL;
				// clean the invite, user not added previously
				$this->cleanInvite($invite, true, $validEmailAddress);
				// delete the invalid participant from the system
				$this->deleteInvalidParticipant($invalidEmailAddress);
				// add alternate email to valid participant
				$this->addAlternateEmail($existingRegisteredUser, $invalidEmailAddress);
				continue;
			}
		}
		echo '{"status" : "SUCCESS", "message" : ""}';
		die();
	}
	
	/**
	* Create the participant
	* @param Participant $participant
	* @param Event $event
	* @return 
	*/
	function addParticipantToEvent(&$participant, &$event)
	{
		// update the participants timestamp
		$participant->timestamp = $this->getTimeStamp();
		$event->AddParticipant($participant);
		$event->timestamp = $this->getTimeStamp();
		$event->Save(true);
		//echo "Adding valid participant ." . $participant->email . " to event " . $event->eventTitle . PHP_EOL;
	}
	
	/**
	* Create the participant
	* @param ArrayObject $user_profile
	* @return Participant
	*/
	function createNewParticipant(&$user_profile)
	{
		$newParticipant = new Participant();
		$firstName = isset($user_profile['first_name']) ? $user_profile['first_name'] : null;
		$lastName = isset($user_profile['last_name']) ? $user_profile['last_name'] : null;
		
		$fb_id = $user_profile['id'];
		$avatarURL = 'http://graph.facebook.com/' . $fb_id . '/picture';
		
		// should update the existing user with any new information
		if ($firstName) $newParticipant->firstName = $firstName;
		if ($lastName) $newParticipant->lastName = $lastName;
		$newParticipant->facebookId = $fb_id;
		$newParticipant->facebookToken = $this->dataObj['fb_token'];
		$newParticipant->avatarURL = $avatarURL;
		$newParticipant->timestamp = $this->getTimeStamp();
		$newParticipant->email = $user_profile['email'];
		$uuid=new uuid();
		$uuidString = $uuid->genUUID();
		$newParticipant->registeredId = $uuidString;
		$newParticipant->Save();
		
		//echo "New participant created." . PHP_EOL;
		
		return $newParticipant;
	}
	
	/**
	* Get user users FB profile object
	* @return ArrayObject
	*/
	function getFBUserProfile()
	{
		$access_token = $this->dataObj['fb_token'];
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
		    
		    echo '{"status" : "ERROR", "message" : "Facebook lookup failed"}';
			die();
		  }
		}
		else 
		{
			echo '{"status" : "ERROR", "message" : "Facebook lookup failed, token invalid"}';
			die();
		}
		//echo "Facebook profile retrieved." . PHP_EOL;
		//echo print_r($user_profile) . PHP_EOL;
		return $user_profile;
	}
	
	/**
	* Get the invite object using the token from html
	* @param Invite $invite
	* @param boolean $userPreviouslyAdded
	* @return 
	*/
	function cleanInvite(&$invite, $userPreviouslyAdded, $validEmailAddress)
	{
		$invite->token = '';
		$invite->pending = 0;
		$invite->hasBeenRemoved = 1;
		$invite->timestamp = $this->getTimeStamp();
		$invite->Save();
		
		if (!$userPreviouslyAdded)
		{
			$invite->inviteeId = $validEmailAddress;
			$invite->hasBeenRemoved = 0;
			$invite->SaveNew();
		}
		
		//echo "Invite cleaned: remove token, valid invitee email set, set pending to 0, and had been removed to " . ($userPreviouslyAdded ? "1" : "0") . PHP_EOL;
	}
	
	/**
	* Get the invite object using the token from html
	* @return ArrayObject
	*/
	function getInviteList()
	{
		$invite_token = $this->dataObj['pair_token'];
		$lookup = new Invite();
		$inviteList1 = $lookup->GetList( array( array("token", "=", $invite_token ) ) );
		if (count($inviteList1) == 0)
		{
			echo '{"status" : "SUCCESS", "message" : "Not found, may have been paired previously"}';
			die();
		}
		$invite = $inviteList1[0];
		$inviteeEmail = $invite->inviteeId;
		$inviteList2 = $lookup->GetList( array( array("inviteeId", "=", $inviteeEmail ) ) );
		
		//echo "Invite list retrieved." . PHP_EOL;
		return $inviteList2;
	}
	
	/**
	* Deletes the invalid participant from the system
	* @return 
	*/
	function deleteInvalidParticipant($email)
	{
		$invalidParticipants = new Participant();
		$invalidParticipants->DeleteList( array( array("email", "=", $email ) ), true );
		//echo "Invalid participant with email " . $email . " deleted." . PHP_EOL;
	}
	
	/**
	* Add alternate email to valid participant if it does not exist
	* @param Participant $participant
	* @param string $email
	* @return 
	*/
	function addAlternateEmail(&$participant, $email)
	{
		if ($participant->email == $email) return; // invited email was actually valid, just not registered
		
		$altemail = new AltEmail();
		// check to see if alternate email has already been added
		$list = $altemail->GetList( array( array("email", "=", $email ) ) );
		if (count($list) > 0) return; // this alternate email already exists
		
		$altemail->email = $email;
		$participant->AddAltemail($altemail);
		$participant->timestamp = $this->getTimeStamp();
		$participant->Save(true);
		//echo "Add alternate email" . $email . "to valid participant " . $participant->participantId . PHP_EOL;
	}
	
	/**
	* Checks to see if the user is in the event
	* @param Event $event
	* @param string $email
	* @return boolean
	*/
	function validateUserPartOfEvent(&$event, $email)
	{
		$participants = $event->GetParticipantList( array( array("email", "=", $email ) ) );
		$isValid = count($participants) > 0;
		//echo "Is user part of event " . $event->eventName . ", " . ($isValid ? "YES" : "NO") . PHP_EOL;
		return $isValid;
	}
}
?>