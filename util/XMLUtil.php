<?php

foreach(glob("objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once 'request.base.php';

class XMLUtil extends ReqBase
{
	// construct
	function XMLUtil()
	{
		parent::__construct();
	}
	
	/**
	* Generates an xml object for the client info
	* @return DOMDocument
	*/
	function GetAppInfoXML()
	{
		$doc = new DOMDocument('1.0', 'UTF-8');
		$root = $doc->createElement('appinfo');
		$doc->appendChild($root);
		
		$app_store_id = $GLOBALS['configuration']['app_store_id'];
		$root->setAttribute('app_store_id', $app_store_id);
		
		$app_store_version = $GLOBALS['configuration']['app_store_version'];
		$root->setAttribute('app_store_version', $app_store_version);
		
		return $doc;
	}
	
	/**
	* Generates an xml object for a Participant object type
	* @param Participant $participant
	* @param string $eventId
	* @param string $type
	* @return DOMDocument
	*/
	function GetParticipantXML(&$participant, $eventId=null, $type=null)
	{
		if ($participant instanceof Participant)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('participant');
			$doc->appendChild($root);
			
			if($eventId) $root->setAttribute('eventId', $eventId);
			if($type) $root->setAttribute('type', $type);
			$root->setAttribute('email', $participant->email);
		
			if ( strlen($participant->firstName) > 0 )
			{
				$firstNameNode = $doc->createElement('firstName');
				$root->appendChild($firstNameNode);
				$firstName = $doc->createCDATASection($participant->firstName);
				$firstNameNode->appendChild($firstName);
			}
			if ( strlen($participant->lastName) > 0 )
			{
				$lastNameNode = $doc->createElement('lastName');
				$root->appendChild($lastNameNode);
				$lastName = $doc->createCDATASection($participant->lastName);
				$lastNameNode->appendChild($lastName);
			}
			if ( strlen($participant->avatarURL) > 0 )
			{
				$avatarURLNode = $doc->createElement('avatarURL');
				$root->appendChild($avatarURLNode);
				$avatarURL = $doc->createCDATASection($participant->avatarURL);
				$avatarURLNode->appendChild($avatarURL);
			}
   			return $doc;
		}
	}
	/**
	* Generates an xml object for a Participant object type
	* @param Participant $participant
	* @return DOMDocument
	*/
	function GetParticipantWithRuidXML(&$participant)
	{
		if ($participant instanceof Participant)
		{	
			$doc = $this->GetParticipantXML($participant);
			$root = $doc->getElementsByTagName('participant')->item(0);
			
			$ruidNode = $doc->createElement('ruid');
			$root->appendChild($ruidNode);
			$ruid = $doc->createCDATASection($participant->registeredId);
			$ruidNode->appendChild($ruid);
   		
   			return $doc;
		}
	}
	
	/**
	* Generates an xml object for a FeedMessage object type
	* @param FeedMessage $feedMessage
	* @param Boolean $messageRead
	* @return DOMDocument
	*/
	function GetFeedMessageXML(&$feedMessage, $messageRead=false)
	{
		if ($feedMessage instanceof FeedMessage)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('feedMessage');
			$doc->appendChild($root);
			
			$root->setAttribute('id', $feedMessage->feedmessageId);
			$root->setAttribute('type', $feedMessage->type);
			$root->setAttribute('senderId', $feedMessage->senderId);
			$root->setAttribute('messageRead', $messageRead ? 'true' : 'false');
			$root->setAttribute('timestamp', $feedMessage->timestamp);
			if(strlen($feedMessage->message) > 0)
			{
				$messageNode = $doc->createElement('message');
				$root->appendChild($messageNode);
				$message = $doc->createCDATASection($feedMessage->message);
				$messageNode->appendChild($message);
			}
			if(strlen($feedMessage->imageURL) > 0)
			{
				$imageNode = $doc->createElement('imageURL');
				$root->appendChild($imageNode);
				$imageURL = $doc->createCDATASection($feedMessage->imageURL);
				$imageNode->appendChild($imageURL);
			}
			return $doc;
		}
	}
	
	/**
	* Generates an xml object for a FeedMessage object type
	* @param SuggestedTime $suggestedTime
	* @return DOMDocument
	*/
	function GetSuggestedTimeXML(&$suggestedTime)
	{
		if ($suggestedTime instanceof SuggestedTime)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('suggestedTime');
			$doc->appendChild($root);
			
			$root->setAttribute('id', $suggestedTime->suggestedtimeId);
			$root->setAttribute('email', $suggestedTime->email);
			$root->setAttribute('suggestedTime', $suggestedTime->suggestedTime);
			return $doc;
		}
	}
	
	/**
	* Generates an xml object for a removed invalid Participant object type
	* @param Invite $invite
	* @return DOMDocument
	*/
	function GetInvalidParticipantRemovedXML(&$invite)
	{
		if ($invite instanceof Invite)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('participant');
			$doc->appendChild($root);
			
			$root->setAttribute('hasBeenRemoved', 'true');
			$root->setAttribute('email', $invite->inviteeId);
			return $doc;
		}
	}
	
	
	/**
	* Generates an xml object for a Location object type
	* @param Location $location
	* @param Boolean $iVotedForLocation
	* @param Boolean $showTempId
	* @return DOMDocument
	*/
	function GetLocationXML(&$location, $iVotedForLocation=false, $showTempId=false, $hasDeal=false)
	{
		if ($location instanceof Location)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('location');
			$doc->appendChild($root);
			
			$root->setAttribute('id', $location->locationId);
			
			// check for removal, exit early
			
			if ($location->hasBeenRemoved)
			{
				$root->setAttribute('hasBeenRemoved', 'true');
				return $doc;
			}
			
			$root->setAttribute('longitude', $location->longitude);
			$root->setAttribute('latitude', $location->latitude);
			$root->setAttribute('addedById', $location->addedByParticipantId);
			
			if ($iVotedForLocation) {
				$root->setAttribute('iVotedFor', "true");
			}
			if ($showTempId) {
				$root->setAttribute('tempId', $location->tempId);
			}
			if ($hasDeal) {
				$root->setAttribute('hasDeal', "true");
			}
			
			$nameNode = $doc->createElement('name');
			$root->appendChild($nameNode);
			$name = $doc->createCDATASection($location->name);
			$nameNode->appendChild($name);
			
			$vicinityode = $doc->createElement('vicinity');
			$root->appendChild($vicinityode);
			$vicinity= $doc->createCDATASection($location->vicinity);
			$vicinityode->appendChild($vicinity);
			
			$g_idNode = $doc->createElement('g_id');
			$root->appendChild($g_idNode);
			$g_id = $doc->createCDATASection($location->g_id);
			$g_idNode->appendChild($g_id);
			
			$formatted_addressNode = $doc->createElement('formatted_address');
			$root->appendChild($formatted_addressNode);
			$formatted_address = $doc->createCDATASection($location->formatted_address);
			$formatted_addressNode->appendChild($formatted_address);
			
			$formatted_phone_numberNode = $doc->createElement('formatted_phone_number');
			$root->appendChild($formatted_phone_numberNode);
			$formatted_phone_number = $doc->createCDATASection($location->formatted_phone_number);
			$formatted_phone_numberNode->appendChild($formatted_phone_number);
	   		
			$ratingNode = $doc->createElement('rating');
			$root->appendChild($ratingNode);
			$rating = $doc->createCDATASection($location->rating);
			$ratingNode->appendChild($rating);
			
			$typeNode = $doc->createElement('location_type');
			$root->appendChild($typeNode);
			$type = $doc->createCDATASection($location->location_type);
			$typeNode->appendChild($type);
			
	   		return $doc;
		}
	}
	
	/**
	* Generates an xml object for a ReportLocation object type
	* @param ReportLocation $reportlocation
	* @return DOMDocument
	*/
	function GetReportLocationXML(&$reportlocation)
	{
		if ($reportlocation instanceof ReportLocation)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('reportLocation');
			$doc->appendChild($root);
			
			$root->setAttribute('id', $reportlocation->reportlocationId);
			$root->setAttribute('longitude', $reportlocation->longitude);
			$root->setAttribute('latitude', $reportlocation->latitude);
			$root->setAttribute('email', $reportlocation->email);
	   		$root->setAttribute('reportTime', $reportlocation->timestamp);
	   		$root->setAttribute('disableLocationReporting', $reportlocation->hasDisabledTracking ? 'true' : 'false');
	   		
	   		return $doc;
		}
	}
	
	/**
	* Generates an xml object for a Vote object type
	* @param Vote $vote
	* @return DOMDocument
	*/
	function GetVoteXML(&$vote)
	{
		if ($vote instanceof Vote)
		{
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('vote');
			$doc->appendChild($root);
			
			$root->setAttribute('locationId', $vote->locationId);
			$root->setAttribute('email', $vote->email);
			
			if ($vote->hasBeenRemoved) $root->setAttribute('hasBeenRemoved', 'true');
	   		
	   		return $doc;
		}
	}
	
	/**
	* Generates an xml object for a Event object type
	* @param Event $event
	* @param Participant $participant
	* @param DateTime $timestamp
	* @param Boolean $showTempId
	* @return DOMDocument
	*/
	function GetEventXML(&$event, &$participant, &$timestamp=null, $showTempId=false)
	{
		if ($event instanceof Event)
		{
			if ($timestamp == null) $timestamp = new DateTime('1974-06-21 01:00:00');
			
			$doc = new DOMDocument('1.0', 'UTF-8');
//			$doc->preserveWhiteSpace = false;
//			$doc->formatOutput   = true;
			$root = $doc->createElement('event');
			$doc->appendChild($root);
			
			$root->setAttribute('id', $event->eventId);
			
			$objTimestampNumber = $event->infoTimestamp;
			$objTimestamp = new DateTime( $objTimestampNumber );
    		if ( $timestamp < $objTimestamp )
    		{
				$eventInfoNode = $doc->createElement('eventInfo');
				$root->appendChild($eventInfoNode);
				$eventInfoNode->setAttribute('eventDate', $event->eventDate);
				$eventInfoNode->setAttribute('eventExpireDate', $event->eventExpireDate);
				if ($event->cancelled == 1) $eventInfoNode->setAttribute('hasBeenCancelled', 'true');
				
				$eventTitleNode = $doc->createElement('eventTitle');
				$eventInfoNode->appendChild($eventTitleNode);
				$eventTitle = $doc->createCDATASection($event->eventTitle);
				$eventTitleNode->appendChild($eventTitle);
				
				$eventDescriptionNode = $doc->createElement('eventDescription');
				$eventInfoNode->appendChild($eventDescriptionNode);
				$eventDescription = $doc->createCDATASection($event->eventDescription);
				$eventDescriptionNode->appendChild($eventDescription);
				
				$creatorIdNode = $doc->createElement('creatorId');
				$eventInfoNode->appendChild($creatorIdNode);
				$creatorId = $doc->createCDATASection($event->creatorId);
				$creatorIdNode->appendChild($creatorId);
				
				$acceptedParticipantListNode = $doc->createElement('acceptedParticipantList');
				$eventInfoNode->appendChild($acceptedParticipantListNode);
				$acceptedParticipantList = $doc->createCDATASection($event->acceptedParticipantList);
				$acceptedParticipantListNode->appendChild($acceptedParticipantList);
				
				$declinedParticipantListNode = $doc->createElement('declinedParticipantList');
				$eventInfoNode->appendChild($declinedParticipantListNode);
				$declinedParticipantList = $doc->createCDATASection($event->declinedParticipantList);
				$declinedParticipantListNode->appendChild($declinedParticipantList);
				
				$guestListOpenNode = $doc->createElement('guestListOpen');
				$eventInfoNode->appendChild($guestListOpenNode);
				$guestListOpen = $doc->createCDATASection($event->guestListOpen?'true':'false');
				$guestListOpenNode->appendChild($guestListOpen);
				
				$locationListOpenNode = $doc->createElement('locationListOpen');
				$eventInfoNode->appendChild($locationListOpenNode);
				$locationListOpen = $doc->createCDATASection($event->locationListOpen?'true':'false');
				$locationListOpenNode->appendChild($locationListOpen);
			}
			
			$objTimestampNumber = $event->locationReorderTimestamp;
			$objTimestamp = new DateTime( $objTimestampNumber );
    		if ( $timestamp < $objTimestamp )
    		{
    			$locationOrderNode = $doc->createElement('locationOrder');
				$root->appendChild($locationOrderNode);
				$locationOrderNode->setAttribute('order', $this->getLocationIdsInOrderForEvent($event));
    		}
			
    		$iVotedForNode = $doc->createElement('iVotedFor');
    		$root->appendChild($iVotedForNode);
    		$iVotedForNode->setAttribute('locations', $this->getMyVotedForLocationsForEvent($event, $participant->email));
    		
			$locationsArray = $event->GetLocationList();
			$created = false;
			foreach ($locationsArray as $i => $value) 
			{
	    		$location = $locationsArray[$i];
	    		$objTimestampNumber = $location->timestamp;
				$objTimestamp = new DateTime( $objTimestampNumber );
				
	    		if ( $timestamp < $objTimestamp )
	    		{
	    			// create the node
	    			if (!$created)
	    			{
	    				$created = true;
	    				$locationsNode = $doc->createElement('locations');
						$root->appendChild($locationsNode);
	    			}
	    			
	    			$dealLookup = new Deal();
	    			$hasDeal = count ($dealLookup->GetList( array( array("featureId", "=", $location->g_id ))) ) > 0;
	    			
	    			$locationXML = $this->GetLocationXML($location, false, $showTempId);
	    			$locationsNode->appendChild( $doc->importNode($locationXML->firstChild, true)  );
	    		}
			}
			
			$suggestedTimeList = $event->GetSuggestedtimeList();
			$created = false;
			foreach ($suggestedTimeList as $i => $value)
			{
				$suggestedTime = $suggestedTimeList[$i];
				$objTimestampNumber = $suggestedTime->timestamp;
				$objTimestamp = new DateTime( $objTimestampNumber );
				
				if ( $timestamp < $objTimestamp )
				{
					if (!$created)
					{
						$created = true;
						$suggestedTimeNode = $doc->createElement('suggestedTimes');
						$root->appendChild($suggestedTimeNode);
					}
					$suggestedtimeXML = $this->GetSuggestedTimeXML($suggestedTime);
					$suggestedTimeNode->appendChild( $doc->importNode($suggestedtimeXML->firstChild, true)  );
				}
			}
			
			$participantsArray = $event->GetParticipantList();
			$created = false;
			foreach ($participantsArray as $i => $value) 
			{
	    		$c_participant = $participantsArray[$i];
	    		$objTimestampNumber = $c_participant->timestamp;
				$objTimestamp = new DateTime( $objTimestampNumber );
	    		if ( $timestamp < $objTimestamp )
	    		{
	    			// create the node
	    			if (!$created)
	    			{
	    				$created = true;
	    				$participantsNode = $doc->createElement('participants');
						$root->appendChild($participantsNode);
	    			}
	    			$participantXML = $this->GetParticipantXML($c_participant);
	    			$participantsNode->appendChild( $doc->importNode($participantXML->firstChild, true) );
	    		}
			}
			
			// send out any invalid participant removals to the client
			$invitesArray = $event->GetInviteList( array(array("hasBeenRemoved", "=", 1) ) );
			foreach ($invitesArray as $i => $value) 
			{
				$c_invite = $invitesArray[$i];
				$objTimestampNumber = $c_invite->timestamp;
				$objTimestamp = new DateTime( $objTimestampNumber );
				if ( $timestamp < $objTimestamp )
	    		{
	    			if (!$created)
	    			{
	    				$created = true;
	    				$participantsNode = $doc->createElement('participants');
						$root->appendChild($participantsNode);
	    			}
	    			// we will append the locations node
					$participantXML = $this->GetInvalidParticipantRemovedXML($c_invite);
					$participantsNode->appendChild( $doc->importNode($participantXML->firstChild, true) );
	    		}
			}
			
			
			$feedMessageArray = $event->GetFeedmessageList();
			// pass the unreadMessageCount regardless of messages
			$feedNode = $doc->createElement('feedMessages');
	    	$feedNode->setAttribute('unreadMessageCount', $this->getMessageUnreadCount($feedMessageArray, $participant->participantId));
			$root->appendChild($feedNode);
			
			foreach ($feedMessageArray as $i => $value) 
			{
				$feedMessage = $feedMessageArray[$i];
	    		$objTimestampNumber = $feedMessage->timestamp;
				$objTimestamp = new DateTime( $objTimestampNumber );
				
				if ( $timestamp < $objTimestamp )
	    		{
	    			$feedXML = $this->GetFeedMessageXML($feedMessage, $this->participantHasRead($feedMessage, $participant->participantId));
	    			$feedNode->appendChild( $doc->importNode($feedXML->firstChild, true)  );
	    		}
			}
			
	   		return $doc;
		}
	}

	/**
	* Generates an xml object for a Event object type, light info only.
	* @param Event $event
	* @param Participant $participant
	* @return DOMDocument
	*/
	function GetEventXMLLight(&$event, &$participant)
	{
		if ($event instanceof Event)
		{			
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('event');
			$doc->appendChild($root);
			
			$root->setAttribute('id', $event->eventId);
			
			$eventInfoNode = $doc->createElement('eventInfo');
			$root->appendChild($eventInfoNode);
			$eventInfoNode->setAttribute('eventDate', $event->eventDate);
			$eventInfoNode->setAttribute('eventExpireDate', $event->eventExpireDate);
			$eventInfoNode->setAttribute('hasBeenRead', $this->participantHasRead($event, $participant->participantId) ? 'true':'false');
			$eventInfoNode->setAttribute('hasCheckedIn', $this->getCheckedIn($event, $participant->participantId) ? 'true':'false');
			if ($event->cancelled == 1) $eventInfoNode->setAttribute('hasBeenCancelled', 'true');
			
			$eventTitleNode = $doc->createElement('eventTitle');
			$eventInfoNode->appendChild($eventTitleNode);
			$eventTitle = $doc->createCDATASection($event->eventTitle);
			$eventTitleNode->appendChild($eventTitle);
			
			$creatorIdNode = $doc->createElement('creatorId');
			$eventInfoNode->appendChild($creatorIdNode);
			$creatorId = $doc->createCDATASection($event->creatorId);
			$creatorIdNode->appendChild($creatorId);
			
			$acceptedParticipantListNode = $doc->createElement('acceptedParticipantList');
			$eventInfoNode->appendChild($acceptedParticipantListNode);
			$acceptedParticipantList = $doc->createCDATASection($event->acceptedParticipantList);
			$acceptedParticipantListNode->appendChild($acceptedParticipantList);
			
			$declinedParticipantListNode = $doc->createElement('declinedParticipantList');
			$eventInfoNode->appendChild($declinedParticipantListNode);
			$declinedParticipantList = $doc->createCDATASection($event->declinedParticipantList);
			$declinedParticipantListNode->appendChild($declinedParticipantList);
			
			// get the top voted for location
			// sort by locationId, decending order, one result
			//$votesArray = $event->GetVoteList( array( array("voteId", ">", 0) , array("hasBeenRemoved", "=", 0) ) );
			$votesArray = $event->GetVoteList( array(array("hasBeenRemoved", "=", 0) ) );
			
			$locationsArray = $event->GetLocationList( array( array("hasBeenRemoved", "=", 0 ) ) );
			$winningLocation = null;
			if ( count($locationsArray) > 0)
			{			
				$winningLocation = $this->determineWinningLocationForEvent($event);
				
				$dealLookup = new Deal();
				$hasDeal = count ($dealLookup->GetList( array( array("featureId", "=", $winningLocation->g_id ))) ) > 0;
				
	    		$locationsNode = $doc->createElement('locations');
				$root->appendChild($locationsNode);
				$iVotedForLocation = $this->iVotedForLocation($votesArray, $participant, $winningLocation->locationId);
    			$locationXML = $this->GetLocationXML($winningLocation, $iVotedForLocation, false, $hasDeal);
    			$locationsNode->appendChild( $doc->importNode($locationXML->firstChild, true)  );
			}
			
			$participantsArray = $event->GetParticipantList();
			$participantsNode = $doc->createElement('participants');
			$eventInfoNode->setAttribute('count', count($participantsArray));
			$root->appendChild($participantsNode);
			
			// add the creator participant only if the timestamp is older
			$creatorArray = $event->GetParticipantList( array( array("email", "=", $event->creatorId) ) );
			
			try
			{
				$creatorArray = $event->GetParticipantList( array( array("email", "=", $event->creatorId) ) );
				if (count($creatorArray) == 0) throw new Exception($event->creatorId . " not found in event: " . $event->eventId);
				$creator = $creatorArray[0];
				$participantXML = $this->GetParticipantXML($creator);
	    		$participantsNode->appendChild( $doc->importNode($participantXML->firstChild, true)  );
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
			
	    	if ($winningLocation)	$eventInfoNode->setAttribute('topLocationId', $winningLocation->locationId);
	    	
			$feedNode = $doc->createElement('feedMessages');
	    	$feedNode->setAttribute('unreadMessageCount', $this->getMessageUnreadCount($event->GetFeedmessageList(), $participant->participantId));
	    	$root->appendChild($feedNode);
	    	
	   		return $doc;
		}
	}
	
	/**
	* Gets the count of unread messages for a user
	* @param array $feedMessageArray
	* @param string $participantId
	* @return int
	*/
	function getMessageUnreadCount($feedMessageArray, $participantId)
	{
		$count = 0;
		for ($i=0; $i<count($feedMessageArray); $i++)
		{
			$feedMessage = $feedMessageArray[$i];
			$readParticipantList = explode(',', $feedMessage->readParticipantList);
			$hasRead = in_array($participantId, $readParticipantList);
			if (!$hasRead) $count++;
		}
		return $count;
	}
	
	/**
	* Determines if the user has viewed the feed message
	* @param POG_Base $object
	* @param string $participantId
	* @return Boolean
	*/
	function participantHasRead(&$object, $participantId)
	{
		$readParticipantList = explode(',', $object->readParticipantList);
		$hasRead = in_array($participantId, $readParticipantList);
		
		return $hasRead;
	}
	
	/**
	* Determines if the user has checked into the event
	* @param Event $event
	* @param string $participantId
	* @return Boolean
	*/
	function getCheckedIn(&$event, $participantId)
	{
		$checkedInParticipantList = explode(',', $event->checkedInParticipantList);
		$hasCheckedIn = in_array($participantId, $checkedInParticipantList);
		
		return $hasCheckedIn;
	}
	
	/**
	* Populate an object with xml data
	* @param DOMElement $node
	* @param Object $object
	* @return Object
	*/
	function populateObject(&$node, &$object)
	{
	    if ($node->hasAttributes())
	    {
	        foreach ($node->attributes as $attr)
	        {
	            $object[$attr->nodeName] = $attr->nodeValue;
	        }
	    }

	    foreach ($node->childNodes as $childNode)
        {
			if ($childNode->nodeType != XML_TEXT_NODE)
            { 
            	$object[$childNode->nodeName] = $childNode->nodeValue;
            } 
        }
    
	    return $object; 
	}	
}

?>