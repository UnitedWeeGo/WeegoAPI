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
require_once '../push/class.push.php';

class VoteClass extends ReqBase
{
	public $dataObj = null;	
	private $requiredFields = array('registeredId', 'locationId', 'eventId');
	private $allFields = array('locationId');
	
	function VoteClass()
	{
		parent::__construct();
	}
	
	function VoteGo()
	{
		$doSkipResult = true;
		
		// test for the case that we are calling this from add location
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		$lookup = new Event();
		// check to make sure event exists
		$event = $lookup->Get($this->dataObj['eventId']);
		
		if ( !$event )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to vote.
		$this->validateUserPartOfEvent($event, $me->email);
		
		// check to make sure location exists
		$locations = $event->GetLocationList( array( array("locationId", "=", $this->dataObj['locationId']) ) );
		if ( count($locations) == 0 )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'locationId invalid');
			die();
		}
		
		/** @var $locationBeingVotedOn Location **/
		$locationBeingVotedOn = $locations[0];
		$existingVoteForLocationList = $event->GetVoteList( array( array("email", "=", $me->email), array("locationId", "=", $this->dataObj['locationId']) ) );
		
		$locationOrderChanged = false;
		
		// case REMOVE vote
		if ( count($existingVoteForLocationList) > 0 )
		{
			$voteToRemove = $existingVoteForLocationList[0];
			$voteToRemove->Delete();
			
			// clients need only be notified if the location order changed
			$currentLocationOrder = $this->getLocationIdsInOrderForEvent($event);
			//$this->logToFile("/www/bigbabyllc.com/beta/apiRequestHandlers/log.txt", $currentLocationOrder . '\n');
			
			$locationBeingVotedOn->voteCount--;
			$locationBeingVotedOn->Save();
			$newLocationOrder = $this->getLocationIdsInOrderForEvent($event);			
			$locationOrderChanged = $currentLocationOrder != $newLocationOrder;
			$event->Save(true);
			
			if ($locationOrderChanged) 
			{
				$event->timestamp = $this->getTimeStamp();
				$event->locationReorderTimestamp = $this->getTimeStamp();
				$event->Save(true);
				
				$push = new Push();
				$push->triggerClientUpdateForEvent($event);
			}

			if (!$doSkipResult) // only give success and kill if not called by location class
			{
				$s = new SuccessResponse();
				echo $s->genSuccess(SuccessResponse::VoteRemoveSuccess, '', '', $event->eventId, $this->getMyVotedForLocationsForEvent($event, $me->email));
				die();
			}
			else 
			{
				return $event;
			}
		}
		else // case ADD vote
		{	
			$newVote = new Vote();
			$this->populateObject($this->allFields, $this->dataObj, $newVote); // populate the vote obj
			$newVote->email = $me->email;
			$newVote->timestamp = $this->getTimeStamp();
			$event->AddVote($newVote);
				
			$currentLocationOrder = $this->getLocationIdsInOrderForEvent($event);
			$locationBeingVotedOn->voteCount++;
			$locationBeingVotedOn->Save();
			$newLocationOrder = $this->getLocationIdsInOrderForEvent($event);
			$locationOrderChanged = $currentLocationOrder != $newLocationOrder;
			$event->Save(true);
			if ($locationOrderChanged) 
			{
				$event->locationReorderTimestamp = $this->getTimeStamp();
				$event->timestamp = $this->getTimeStamp();
				$event->Save(true);
				$push = new Push();
				$push->triggerClientUpdateForEvent($event);
			}
			
			if (!$doSkipResult) // only give success and kill if not called by location class
			{
				$s = new SuccessResponse();
				echo $s->genSuccess(SuccessResponse::VoteAddSuccess, '', '', $event->eventId, $this->getMyVotedForLocationsForEvent($event, $me->email));
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