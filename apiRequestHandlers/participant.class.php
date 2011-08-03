<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once '../util/XMLUtil.php';
require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.uuid.php';
require_once '../util/class.success.php';
require_once '../push/class.push.php';

class ParticipantClass extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('registeredId', 'email', 'eventId');
	private $allFields = array('email');
	
	function ParticipantClass()
	{
		parent::__construct();
	}
	
	function ParticipantGo()
	{
		$doSkipResult = true;
		
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		$email = $this->dataObj['email'];
		$this->checkValidEmail($email);
		
		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		// check if you are adding yourself
		if ($me->email == $email)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'No need to add yourself');
			die();
		}
		$lookup = new Event();
		// check to make sure event exists
		$event = $lookup->Get($this->dataObj['eventId']);
		
		if ( !$event )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to invite a participant.
		$this->validateUserPartOfEvent($event, $me->email);
		
		// get the actual participant if they exist in the system
		$existingUser = $this->isParticipantInSystem($email);

		// check for already added participant
		$userHasBeenInvited = false;
		if ($existingUser) $userHasBeenInvited = $this->checkUserInvitedToEvent($event, $existingUser->email);
		if($userHasBeenInvited)
		{
			return $event;
			/*
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::DuplicateParticipantError, 'User already added to this event');
			die();
			*/
		}
		
		
		if($existingUser)
		{
			$participant = $existingUser; //  grab the user, should only ever be one... no need to manipulate
		}
		else
		{
			$participant = new Participant(); // create a new user
			$this->populateObject($this->allFields, $this->dataObj, $participant); // populate that user
		}
		$participant->timestamp = $this->getTimeStamp();
		$participant->Save();
		
		$event->AddParticipant($participant);
		
		$userIsRegistered = (strlen($participant->registeredId) > 0);
		
		$invite = new Invite();
		$invite->sent = 0;
		$invite->inviterId = $me->email;
		$invite->inviteeId = $participant->email;
		$invite->timestamp = $this->getTimeStamp();
		
		// if the user is NOT registered, we must mark the invite pending so an invite email will go out
		if (!$userIsRegistered)
		{
			$invite->pending = 1;
			$uuid=new uuid();
			$uuidString = $uuid->genUUID();
			$invite->token = $uuidString;
		}
		
		$event->AddInvite($invite);
		$event->timestamp = $this->getTimeStamp();
		$event->Save(true);
		
		$s = new SuccessResponse();
		if ($existingUser) // if the user existed, the client needs to have the full user record
		{
			// only queue up a notification if the user is registered
			
			if ($userIsRegistered)
			{
				$push = new Push();
				$push->addInviteToQueue($invite);
			}
		
			if (!$doSkipResult) // only give success and kill if not called by http
			{
				$xmlUtil = new XMLUtil();
				$xmlArray = array();
				$participantXML= $xmlUtil->GetParticipantXML($participant, $event->eventId);
				$xmlArray[0] = $participantXML;
				
				echo $s->genSuccessWithXMLArray(SuccessResponse::ParticipantAddRegisteredSuccess, $xmlArray);
				die();
			}
			else 
			{
				return $event;
			}
		}
		else // its just a new user, client has everything already
		{
			if (!$doSkipResult) // only give success and kill if not called by http
			{
				echo $s->genSuccess(SuccessResponse::ParticipantAddSuccess, $participant->email);
				die();
			}
			else 
			{
				return $event;
			}
		}
	}
}
?>