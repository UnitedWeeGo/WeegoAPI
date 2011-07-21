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

require_once 'badge.reset.class.php';

class GetEvents extends ReqBase
{
	public $dataObj;
	public $lightEventInfo = false;
		
	private $requiredFields = array('registeredId');
//	private $allFields = array();
	
	function GetEvents()
	{
		parent::__construct();
	}
	
	function GetEventsGo()
	{
		$doSkipResult = true;
		
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
//		print_r($this->dataObj);
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		
		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
//		print_r($me);
		
		$lookup = new Event();
		$isSingleEventRequest = false;
		if (isset($this->dataObj['eventId'])) // then the UI only wants to check this one event, called from event detail
		{
			$isSingleEventRequest = true;
			$myEvents = $me->GetEventList( array(array("eventId", "=", $this->dataObj['eventId'])) );
			$eventToMarkRead = $myEvents[0];
			$this->markEventReadByParticipant($eventToMarkRead, $me->participantId);
		}
		else
		{
			$myEvents = $me->GetEventList();
		}
		
		$xmlUtil = new XMLUtil();
		$xmlArray = array();
		$userTs = null;
		if (isset($this->dataObj['timestamp'])) // hit the method to only do timestamp stuff
		{
			try {
				$userTs = new DateTime($this->dataObj['timestamp']);
			} catch (Exception $e) {
				$e = new ErrorResponse();
				echo $e->genError(ErrorResponse::InvalidTimestampError, 'Timestamp was invalid format, must be 2011-02-10 18:50:06');
				die();
			}
		}
		
		if ($userTs)
		{
			for($i=0; $i<count($myEvents); $i++)
			{
				$event = $myEvents[$i];
				$eventTimestampNumber = $event->timestamp;
				$eventTimestamp = new DateTime( $eventTimestampNumber );
				
				if ( $userTs < $eventTimestamp)
				{
					if ($this->lightEventInfo)
					{
						$xml = $xmlUtil->GetEventXMLLight($event, $me);
					} else {
						$xml = $xmlUtil->GetEventXML($event, $me, $userTs);
					}
					array_unshift($xmlArray, $xml);
				}
			}
		}
		else // just give me everything
		{
			for($i=0; $i<count($myEvents); $i++)
			{
				$event = $myEvents[$i];
				if ($this->lightEventInfo)
				{
					$xml = $xmlUtil->GetEventXMLLight($event, $me);
				} else {
					$xml = $xmlUtil->GetEventXML($event, $me);
				}
				array_unshift($xmlArray, $xml);
			}
		}
		
		// reset the devices badge count
		$badgeResetter = new BadgeResetClass();
		$badgeResetter->resetAllDeviceBadgesForUser($me);
		
		// append the app info to trigger a client update if necessary
		$appInfoXML = $xmlUtil->GetAppInfoXML();
		array_unshift($xmlArray, $xml);
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$s = new SuccessResponse();
			echo $s->genSuccessWithXMLArray($isSingleEventRequest ? SuccessResponse::EventGetSuccess : SuccessResponse::EventsGetSuccess, $xmlArray);
			die();
		}
	}
	/**
	* Marks the event read by a participant
	* @param Event $event
	* @param String $participantId
	* @return DOMDocument
	*/
	function markEventReadByParticipant(&$event, $participantId)
	{
		// adding only if user has not previously read
		$readParticipantList = explode(',', $event->readParticipantList);
		$hasRead = in_array($participantId, $readParticipantList);
		if (!$hasRead)	
		{
			array_push($readParticipantList, $participantId);
			$event->readParticipantList = implode(",", $readParticipantList);
			$event->Save();
		}
	}
}
?>