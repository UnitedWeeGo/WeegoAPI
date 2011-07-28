<?php

header ("Content-Type:text/xml");

require_once "../configuration.php";
foreach(glob("../apiRequestHandlers/*.php") as $class_filename) {
	require_once($class_filename);
}


require_once '../util/XMLUtil.php';
require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';

//error_reporting (E_ERROR);

class XMLFacebookClass extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array();
	
	/** @var $eventToReturnThatMatchesRequestId Event */
	private $eventToReturnThatMatchesRequestId;
	
	function XMLFacebookClass()
	{
		parent::__construct();
	}
	
	function XMLFacebookGo()
	{
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
		}
		//$this->checkProperties($this->requiredFields, $this->dataObj);
		
		//$this->dataObj['registeredId']
		/** @var $me Participant */
		$me;
		if (isset($this->dataObj['access_token']))
		{
			$loginObj;
			$access_token = $this->dataObj['access_token'];
			$loginObj['access_token'] = $access_token;
			$fbLogin = new FacebookLogin();
			$fbLogin->dataObj = $loginObj;
			$me = $fbLogin->FacebookLoginGo();
			
			$this->dataObj['registeredId'] = $me->registeredId;
		}
		else
		{
			// check if you are a registered user
			$me = $this->checkRegUserId($this->dataObj);
		}
		
		// if no events are present, just pass back the registered user success
		if (!isset($this->dataObj['xml']))
		{
			// registered and authenticated, respond with success
			$s = new SuccessResponse();
			$xmlUtil = new XMLUtil();
			$xmlArray = array();
			$participantXML= $xmlUtil->GetParticipantWithRuidXML($me);
			$xmlArray[0] = $participantXML;
		
			echo $s->genSuccessWithXMLArray(SuccessResponse::LoginSuccess, $xmlArray);
			die();
		}
		
		$returnEventWithRequestId = false;
		if (isset($this->dataObj['requestId'])) $returnEventWithRequestId = true;
		
		$xml = $this->dataObj['xml'];
		$doc = new DOMDocument('1.0', 'UTF-8');
		$loadSuccess = $doc->loadXML($xml);
		if (!$loadSuccess) $this->xmlStructureError(); // error out and die
		
		$events = $doc->getElementsByTagName( "event" );
		$nodeListLength = $events->length;
		
		for ($i = 0; $i < $nodeListLength; $i++)
		{
			/** @var $event DOMNode  */
			$event = $events->item($i);
			
			// at a minimum, an event node must be passed
			if ( !$event ) $this->xmlStructureError(); // error out and die
			
			// event exits, create object to store data
			$eventObj = array();
			$eventObj['registeredId'] = $this->dataObj['registeredId'];
			
			$xmlUtil = new XMLUtil();
			$eventInfo = $event->getElementsByTagName( "eventInfo" ) -> item(0);

			$eventObj = $xmlUtil->populateObject($eventInfo, $eventObj);
			
			// process the event
			$eventGenerator = new EventClass();
			$eventGenerator->dataObj = $eventObj;
			$savedEvent = $eventGenerator->EventGo();
			
			// if a request id is passes, check the event for that value and set it to be returned if it exists
			if ($returnEventWithRequestId)
			{
				$requestId = $event->attributes->getNamedItem('requestId')->nodeValue;
				if ($requestId == $this->dataObj['requestId']) $this->eventToReturnThatMatchesRequestId = $savedEvent;
			}
			
			// Locations -------
			$tempLocationStorageForVote = array();
			$locationObj = array();
			$locations = $event->getElementsByTagName( "location" );
			for ($j = 0; $j < $locations->length; $j++)
			{
				$locationObj = array();
				$locationObj['registeredId'] = $this->dataObj['registeredId'];
				$locationObj['eventId'] = $savedEvent->eventId;
				$node = $locations->item($j);
				$locationObj = $xmlUtil->populateObject($node, $locationObj);
				$locationGenerator = new LocationClass();
				$locationGenerator->dataObj = $locationObj;
				$savedLocation = $locationGenerator->LocationGo();
				$tempLocationStorageForVote[$j] = $savedLocation;
			}
			
			// Participants -------
			$participantObj;
			$participants = $event->getElementsByTagName( "participant" );
			for ($k = 0; $k < $participants->length; $k++)
			{
				$participantObj = array();
				$participantObj['registeredId'] = $this->dataObj['registeredId'];
				$participantObj['eventId'] = $savedEvent->eventId;
				$node = $participants->item($k);
				$participantObj = $xmlUtil->populateObject($node, $participantObj);
				$participantGenerator = new ParticipantClass();
				$participantGenerator->dataObj = $participantObj;
				$savedParticipant = $participantGenerator->ParticipantGo();
			}

			// Votes
			$votes = $event->getElementsByTagName( "vote" );
			
			for ($l=0; $l<$votes->length; $l++)
			{
				$voteObj = array();
				$voteObj['registeredId'] = $this->dataObj['registeredId'];
				$voteObj['eventId'] = $savedEvent->eventId;
				$node = $votes->item($l);
				
				if ( $node->hasAttribute('selectedLocationIndex') ) // client does not have the id (because new), must look up
				{
					$locIndex = $node->getAttribute('selectedLocationIndex');
					$savedLocation = $tempLocationStorageForVote[$locIndex];
					if ( isset($savedLocation) ) $voteObj['locationId'] = $savedLocation->locationId; // if not set will send missing param error
				}
				else
				{
					$voteObj = $xmlUtil->populateObject($node, $voteObj);
				}
				
				$voteGenerator = new VoteClass();
				$voteGenerator->dataObj = $voteObj;
				$savedVote = $voteGenerator->VoteGo();
			}
		}
		
		// registered and authenticated, respond with success
		$s = new SuccessResponse();
		$xmlUtil = new XMLUtil();
		$xmlArray = array();
		$participantXML= $xmlUtil->GetParticipantWithRuidXML($me);
		$xmlArray[0] = $participantXML;
		
		if ($this->eventToReturnThatMatchesRequestId)
		{
			$xmlArray[1] = $xmlUtil->GetEventXML($this->eventToReturnThatMatchesRequestId, $me);
		}
	
		echo $s->genSuccessWithXMLArray(SuccessResponse::LoginSuccess, $xmlArray);
		die();

	}
	function xmlStructureError()
	{
		$e = new ErrorResponse();
		echo $e->genError(ErrorResponse::InvalidXMLError, 'XML was invalid.');
		die();
	}
}