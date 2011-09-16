<?php

//header ("Content-Type:text/xml");

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once 'class.error.php';
require_once 'class.success.php';
require_once 'TimeZoneUtil.php';

date_default_timezone_set('GMT');

class ReqBase
{
	private $timestamp;
	function __construct() {
		set_error_handler( array(&$this, "errorHandler") );
		$this->timestamp = microtime(true);
   	}

   	function errorHandler($errno, $errstr, $errfile, $errline)
   	{
	   	$e = new ErrorResponse();
		echo $e->genError(ErrorResponse::ServerError, "severity:$errno $errstr in $errfile on line $errline");
		die();
   	}

   	/**
	* gets the current unix timestamp as a float
	* @return Float
	*/
   	function getTimeStamp()
   	{
   		return date('Y-m-d H:i:s', $this->timestamp);
   	}
   	
   	/**
   	* Get the formatted event time
   	* @param string $eventDate
   	* @param string $eventTimeZone
   	* @return string
   	*/
   	function getFormattedTime($eventDate, $eventTimeZone=null)
   	{
   		$tz = TimeZoneUtil::getPHPTimeZoneStampForAbbreviation($eventTimeZone);
   		$eventTime = new DateTime($eventDate);
   		if ($tz) $eventTime->setTimezone(new DateTimeZone($tz));
   		$dateStr = $eventTime->format('D, M j g:i A') . ' ' . (($tz) ? $eventTimeZone : 'GMT');
   		return $dateStr;
   	}

	/**
	* Populates a data object with either get or post data
	* @return Array
	*/
	function genDataObj()
	{
		$request_method = strtolower($_SERVER['REQUEST_METHOD']);
		$data;
		switch ($request_method)
		{
			case 'get':
				$data = $_GET;
				break;
			case 'post':
				$this->stripmagicquotes(); // tests to see if magic quotes is on, and strips slashes if it is
				$data = $_POST;
				break;
			case 'put':
				//
				break;
		}
		return $data;
	}

	function stripmagicquotes()
	{
		if (get_magic_quotes_gpc()) {

			if (!function_exists('stripslashes_gpc'))
			{
			    function stripslashes_gpc(&$value)
			    {
			        $value = stripslashes($value);
			    }
			}
//		    array_walk_recursive($_GET, 'stripslashes_gpc');
		    array_walk_recursive($_POST, 'stripslashes_gpc');
//		    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
//		    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
		}
	}



	/**
	* Tests an object to check if all needed properties exist, will send error if not
	* @param Array $props
	* @param Array $object
	* @return
	*/
	function checkProperties(&$props,&$object)
	{
		for ($i=0; $i<count($props); $i++)
		{
			$prop = $props[$i];
			if ( !isset($object[$prop]) )
			{
				$e = new ErrorResponse();
				echo $e->genError(ErrorResponse::MissingParamError, 'Missing some parameters');
				die();
			}
		}
	}

	/**
	* Populates an object with properties from a source object using an array of prop names
	* @param Array $props
	* @param Array $srcObject
	* @param Object $trgObject
	*/
	function populateObject(&$props,&$srcObject,&$trgObject)
	{
		$didChange = false;
		for ($i=0; $i<count($props); $i++)
		{
			$prop = $props[$i];
			if ( isset($srcObject[$prop]) )
			{
				$trgObject->$prop = $srcObject[$prop];
				$didChange = true;
			}
		}
		return $didChange;
//		print_r($trgObject);
	}

	/**
	* Checks to see if the registeredId is valid
	* @param Array $dataObj
	* @return Participant
	*/
	function checkRegUserId(&$dataObj)
	{
		$registeredId = $dataObj['registeredId'];
		if (strlen($registeredId) == 0)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'registeredId missing');
			die();
		}
		$participant = new Participant();
		$resultingObjects = $participant->GetList( array( array("registeredId", "=", $registeredId) ) );

		if ( count($resultingObjects) == 0 )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidRUIDError, 'registeredId not found');
			die();
		}
		else
		{
			return $resultingObjects[0]; // Participant
		}
	}

	/**
	* Checks to see if the Participant is registered and return them
	* @param string $email
	* @return Participant
	*/
	function isParticipantRegistered($email)
	{
		$lookup = new Participant();
		$participants = $lookup->GetList( array( array("email", "=", $email) ) );
		for ($i=0; $i<count($participants); $i++)
		{
			$participant = $participants[$i];
			if (strlen($participant->registeredId) > 0) return $participant;
		}
		return null;
	}
	
	/**
	* Checks to see if the Participant is registered and return them
	* @param string $email
	* @return Participant
	*/
	function isParticipantRegisteredWithFacebookId($facebookId)
	{
		$lookup = new Participant();
		$participants = $lookup->GetList( array( array("facebookId", "=", $facebookId) ) );
		if (count($participants) > 0) return $participants[0];
		return null;
	}
	
	/**
	* Checks to see if the Participant exists in the system
	* @param string $email
	* @return Participant
	*/
	function isParticipantInSystem($email)
	{
		$lookup = new Participant();
		$participants = $lookup->GetList( array( array("email", "=", $email) ) );
		if ( count($participants) > 0 )
		{
			$participant = $participants[0];
			return $participant;
		}
		else // check for alternate email addresses
		{
			$lookupAlt = new AltEmail();
			$alternates = $lookupAlt->GetList( array( array("email", "=", $email) ) );
			if ( count($alternates) > 0 ) // alternate found, return parent participant
			{
				$alternate = $alternates[0];
				return $lookup->Get($alternate->participantId);
			}
		}
		return null;
	}

	/**
	* Checks to see if the email address is valid
	* @param string $email
	* @return
	*/
	function checkValidEmail($email)
	{
		//$isValid = preg_match('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $email);
		
		$isValid = true;
		
		if (!$isValid)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidEmailError, 'Invalid email address');
			die();
		}
	}

	/**
	* Checks to see if the email address is associated with event
	* @param Event $event
	* @param string $email
	*/
	function validateUserPartOfEvent(&$event, $email)
	{
		$participants = $event->GetParticipantList( array( array("email", "=", $email ) ) );
		$isValid = count($participants) > 0;

		if (!$isValid)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'User is not part of this event');
			die();
		}
	}
	
	/**
	* Checks to see if the email address is the creator of the event
	* @param Event $event
	* @param string $email
	*/
	function validateUserCreatedEvent(&$event, $email)
	{
		$didCreateEvent = $event->creatorId == $email;
		
		if (!$didCreateEvent)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'User did not create this event');
			die();
		}
	}
	
	/**
	* Checks to see if the user has neem invited to the event
	* @param Event $event
	* @param string $email
	* @return boolean
	*/
	function checkUserInvitedToEvent(&$event, $email)
	{
		$participants = $event->GetInviteList( array( array("inviteeId", "=", $email ) ) );
		// check if the user belongs to the event
		if (count($participants) > 0) return true;
		return false;
	}

	/**
	* Checks to see if the event is expired
	* @param Event $event
	* @return
	*/
	function checkForEventExpiration(&$event)
	{
		$now = new DateTime();
		$eventExpireDate = new DateTime($event->eventExpireDate);

		$nowTs = $now->getTimestamp();
		$eventExpireTs =  $eventExpireDate->getTimestamp();

		$hasExpired =  $nowTs > $eventExpireTs;

		if ($hasExpired)
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'Event is decided, and can no longer be modified. nowTs:' . $nowTs . ' - eventExpireTs:' . $eventExpireTs);
			die();
		}
	}
	
	/**
	* Does the time calculation to determine if an event voting is over
	* @param Event $event
	* @return boolean
	*/
	function eventVotingIsOver(&$event)
	{
		$now = new DateTime();
		$eventExpireTime = new DateTime($event->eventExpireDate);
	
		$nowTs = $now->getTimestamp();
		$eventExpireTs =  $eventExpireTime->getTimestamp();
	
		return $nowTs > $eventExpireTs;
	}

	/**
	* Checks to see if the event is expired, returns if changed
	* @param Event $event
	* @param string $eventStartTime
	* @return boolean
	*/
	function populateExpirationForEvent(&$event, $eventStartTime)
	{
		$now = new DateTime();
		$eventTime = new DateTime($eventStartTime);

		$nowTs = $now->getTimestamp();
		$eventTs =  $eventTime->getTimestamp();
		
		$timeUntilStart = ceil( ($eventTs - $nowTs) / 60);

		$minTime = 10;
		$maxTimeAfterDecided = 4320; // 3 days in minutes
		$timeLeftToVote = min( $timeUntilStart - (($timeUntilStart - $minTime) / 2), $timeUntilStart);

		$timeLeftToVote = ceil($timeLeftToVote);
		$timeAfterDecided = ($timeUntilStart * 60) - min($timeLeftToVote * 60, $maxTimeAfterDecided * 60);
		
		if ($timeUntilStart >= 10080) // seven days in minutes
		{
			$timeAfterDecided = $maxTimeAfterDecided * 60;
		}
		
		$eventExpireTime = date('Y-m-d H:i:s', $eventTs - $timeAfterDecided);
		
		
		if ($event->eventExpireDate != $eventExpireTime)
		{
			$event->eventExpireDate = $eventExpireTime;
			return true;
		}

		return false;
	}

	/**
	* Determines the order of locations and returns comma delimited list
	* @param Event $event
	* @return Array
	*/
	function getLocationObjectsInOrderForEvent(&$event)
	{
		$locationsArray = $event->GetLocationList( array( array("hasBeenRemoved", "=", 0 ) ) );
		//$locationsArray = $event->GetLocationList();
		usort($locationsArray, array("ReqBase", "cmp_obj"));
		return $locationsArray;
	}

	/**
	* Determines the order of locations and returns comma delimited list
	* @param Event $event
	* @return string
	*/
	function getLocationIdsInOrderForEvent(&$event)
	{
		$locationsArray = $this->getLocationObjectsInOrderForEvent($event);
		$sortedLocIds = array();
		for ($i=0; $i<count($locationsArray); $i++) array_unshift($sortedLocIds, $locationsArray[$i]->locationId);
		return implode(',', $sortedLocIds);
	}

	static function cmp_obj($a, $b)
    {
        $al = $a->voteCount;
        $bl = $b->voteCount;
        if ($al == $bl) {
        	$ai = $a->locationId;
        	$bi = $b->locationId;
            return ($ai > $bi) ? +1 : -1;
        }
        return ($al > $bl) ? +1 : -1;
    }

    /**
	* Returns a list of locations you voted for comma delimited
	* @param Event $event
	* @param string $email
	* @return
	*/
	function getMyVotedForLocationsForEvent(&$event, $email)
	{
		$myVotesArray = $event->GetVoteList( array( array("email", "=", $email ) ) );
		$locIds = array();
		for ($i=0; $i<count($myVotesArray); $i++) array_push($locIds, $myVotesArray[$i]->locationId);
		return implode(',', $locIds);
	}

	/**
	* Determines the winning location and if one exists return it
	* @param Event $event
	* @return Location
	*/
	function determineWinningLocationForEvent(&$event)
	{
		$locations = $this->getLocationObjectsInOrderForEvent($event);
		if ( count($locations) == 0 ) return null;
		return $locations[count($locations) - 1]; // first one is the winner
	}

	/**
	* Checks to see you voted for the location
	* @param Array $voteArray
	* @param Participant $participant
	* @param string $locationId
	* @return
	*/
	function iVotedForLocation(&$voteArray, &$participant, $locationId)
	{
		for ($i=0; $i<count($voteArray); $i++)
		{
			$vote = $voteArray[$i];
			if ($vote->email == $participant->email && $vote->locationId == $locationId && $vote->hasBeenRemoved == 0) return true;
		}
		return false;
	}
	
	/**
	* Determines if the user has removed the event
	* @param Event $event
	* @param string $participantId
	* @return Boolean
	*/
	function participantHasRemoved(&$event, $participantId)
	{
		$removedParticipantList = explode(',', $event->removedParticipantList);
		$hasRemoved = in_array($participantId, $removedParticipantList);
	
		return $hasRemoved;
	}

	/**
	 * Simple logging
	 * @param string $filename
	 * @param string $msg
	 */
	function logToFile($filename, $msg)
   	{
	   // open file
	   $fd = fopen($filename, "a");
	   // write string
	   fwrite($fd, $msg . "\n");
	   // close file
	   fclose($fd);
   	}
}

?>