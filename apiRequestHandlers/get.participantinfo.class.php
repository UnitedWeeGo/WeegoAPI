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
require_once '../facebook-php-sdk/src/facebook.php';

date_default_timezone_set('GMT');

class GetParticipantInfo extends ReqBase
{
	public $dataObj;
	private $requiredFields = array('registeredId');
	private $foundParticipantDict = array();
	private $xmResultArray = array();
	
	/** @var $participantLookup Participant */
	private $participantLookup;
	
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
		
		$this->participantLookup = new Participant();
		$this->populateResultWithRecentParticipants($me);
		$this->populateResultWithFacebookParticipants($me);
		
		$s = new SuccessResponse();
		echo $s->genSuccessWithXMLArray(SuccessResponse::ParticipantListGetSuccess, $this->xmResultArray);
	}
	
	/**
	* Populates the result array with participants from my FB friends registered with weego
	* @param Participant $me
	* @return DOMDocument
	*/
	function populateResultWithFacebookParticipants($me)
	{
		$access_token = $me->facebookToken;
		$facebook = new Facebook(array(
				  'appId'  => '221300981231092',
				  'secret' => '9670bee46bf64a4e52a86716df51a8dc',
		));
		$facebook->setAccessToken($access_token);
		
		$user = $facebook->getUser();		
		if ($user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_friends = $facebook->api('/me/friends');
				$friendsArray = $user_friends['data'];
				
				$xmlUtil = new XMLUtil();
				for ($i=0; $i<count($friendsArray); $i++)
				{
					$fbId = $friendsArray[$i]['id'];
					$c_participant = $this->getParticipantForFacebookId($fbId);
					
					if(!$c_participant) continue;
					$alreadyAdded = key_exists($c_participant->email, $this->foundParticipantDict);
					if (!$alreadyAdded)
					{
						$this->foundParticipantDict[$c_participant->email] = '';
						array_push($this->xmResultArray, $xmlUtil->GetParticipantXML($c_participant, null, 'facebook'));
					}
				}
				
			} catch (FacebookApiException $e) {
				$e = new ErrorResponse();
				echo $e->genError(ErrorResponse::InvalidCredentialsError, 'Facebook connect failed, please try again.');
				die();
			}
		}
		else
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidCredentialsError, 'Facebook connect failed, please try again.');
			die();
		}
	}
	
	/**
	* Returns a participant for a facebook id if one exists
	* @param string $fbId
	* @return DOMDocument
	*/
	function getParticipantForFacebookId($fbId)
	{
		$p = $this->participantLookup->GetList( array( array("facebookId", "=", $fbId ) ) );
		if (count($p) > 0) return $p[0];
		return null;
	}
	
	/**
	* Populates the result array with participants from my events, no dupes
	* @param Participant $me
	* @return DOMDocument
	*/
	function populateResultWithRecentParticipants($me)
	{
		$xmlUtil = new XMLUtil();
		$lookupInvite = new Invite();
		$lookupParticipant = new Participant();
		$all_recents = $lookupInvite->GetList( array( array("inviterId", "=", $me->email ),  array("pending", "=", 0 ),  array("hasBeenRemoved", "=", 0 ) ) );
		$acceptedInvites = array();
		foreach ($all_recents as $i => $value)
		{
			$invite = $all_recents[$i];
			
			$now = new DateTime();
			$inviteTime = new DateTime($invite->timestamp);
			$nowTs = $now->getTimestamp();
			$inviteTs =  $inviteTime->getTimestamp();
			
			$inviteTooOld = ($nowTs - $inviteTs) > 2592000; // 30 days in seconds
			if ($inviteTooOld) continue;
			
			$c_participant_list = $lookupParticipant->GetList( array( array("email", "=", $invite->inviteeId ) ) );
			$c_participant = $c_participant_list[0];
			if (strlen($c_participant->registeredId) > 0) // user is registered
			{
				$alreadyAdded = key_exists($c_participant->email, $this->foundParticipantDict);
			
				if (!$alreadyAdded)
				{
					$this->foundParticipantDict[$c_participant->email] = '';
					array_push($this->xmResultArray, $xmlUtil->GetParticipantXML($c_participant, null, 'recent'));
				}
			}
		}
	}
	
	/**
	* Populates the result array with participants from my events, no dupes
	* @param Participant $me
	* @return DOMDocument
	
	function populateResultWithRecentParticipants($me)
	{
		// get all of my events
		$eventArray = $me->GetEventList();
		$xmlUtil = new XMLUtil();
		foreach ($eventArray as $i => $value)
		{
			$event = $eventArray[$i];
				
			$participantsArray = $event->GetParticipantList();
			foreach ($participantsArray as $ii => $value)
			{
				$c_participant = $participantsArray[$ii];
				if ($me->email == $c_participant->email) continue; // don't include me
				if (strlen($c_participant->registeredId) > 0) // user is registered
				{
					$alreadyAdded = key_exists($c_participant->email, $this->foundParticipantDict);
						
					if (!$alreadyAdded)
					{
						$this->foundParticipantDict[$c_participant->email] = '';
						array_push($this->xmResultArray, $xmlUtil->GetParticipantXML($c_participant, null, 'recent'));
					}
				}
			}
				
		}
	}
	*/
	
}