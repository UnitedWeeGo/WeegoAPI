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

class XMLPostClass extends ReqBase
{
	public $dataObj;	
	private $requiredFields = array('registeredId');
	
	function XMLPostClass()
	{
		parent::__construct();
	}
	
	function XMLPostGo()
	{
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
		}
		$this->checkProperties($this->requiredFields, $this->dataObj);
	
	// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		// check for valid xml
		$xml = $this->dataObj['xml'];
		$doc = new DOMDocument('1.0', 'UTF-8');
		$loadSuccess = $doc->loadXML($xml);
		if (!$loadSuccess) $this->xmlStructureError(); // error out and die
		
		$event = $doc->getElementsByTagName( "event" ) -> item(0);
		// at a minimum, an event node must be passed
		if ( !$event ) $this->xmlStructureError(); // error out and die
		
		// event exits, create object to store data
		$eventObj;
		$eventObj['registeredId'] = $this->dataObj['registeredId'];
		
		$eventId = $event->getAttribute('id');
		if ( $eventId ) $eventObj['eventId'] = $eventId;
		
		$xmlUtil = new XMLUtil();
		$eventInfo = $event->getElementsByTagName( "eventInfo" ) -> item(0);
		if ( $eventInfo ) // eventInfo exists, process
		{
			// use XMLUtil to populate the object
			$eventObj = $xmlUtil->populateObject($eventInfo, $eventObj);
		}
		// process the event
		$eventGenerator = new EventClass();
		$eventGenerator->dataObj = $eventObj;
		$savedEvent = $eventGenerator->EventGo();
		
		/**
		 * loop through the locations, participants and votes and add them
		 */
		// Locations -------
		$tempLocationStorageForVote = array();
		$locationObj;
		$locations = $event->getElementsByTagName( "location" );
		$nodeListLength = $locations->length;
		for ($i = 0; $i < $nodeListLength; $i++)
		{
			$locationObj = array();
			$locationObj['registeredId'] = $this->dataObj['registeredId'];
			$locationObj['eventId'] = $savedEvent->eventId;
			$node = $locations->item($i);
			$locationObj = $xmlUtil->populateObject($node, $locationObj);
			$locationGenerator = new LocationClass();
			$locationGenerator->dataObj = $locationObj;
			$savedLocation = $locationGenerator->LocationGo();
			$tempLocationStorageForVote[$i] = $savedLocation;
		}
		// Participants -------
		$participantObj;
		$participants = $event->getElementsByTagName( "participant" );
		$nodeListLength = $participants->length;
		for ($i = 0; $i < $nodeListLength; $i++)
		{
			$participantObj = array();
			$participantObj['registeredId'] = $this->dataObj['registeredId'];
			$participantObj['eventId'] = $savedEvent->eventId;
			$node = $participants->item($i);
			$participantObj = $xmlUtil->populateObject($node, $participantObj);
			$participantGenerator = new ParticipantClass();
			$participantGenerator->dataObj = $participantObj;
			$savedParticipant = $participantGenerator->ParticipantGo();
		}
		
		// Votes
		$votes = $event->getElementsByTagName( "vote" );
		$nodeListLength = $votes->length;
		
		for ($i=0; $i<$nodeListLength; $i++)
		{
			$voteObj = array();
			$voteObj['registeredId'] = $this->dataObj['registeredId'];
			$voteObj['eventId'] = $savedEvent->eventId;
			$node = $votes->item($i);
			
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
		
		// Return the dashboard data upon create success
		$events = new GetEvents();
		$events->lightEventInfo = true;
		$events->GetEventsGo();

	}
	function xmlStructureError()
	{
		$e = new ErrorResponse();
		echo $e->genError(ErrorResponse::InvalidXMLError, 'XML was invalid.');
		die();
	}
}