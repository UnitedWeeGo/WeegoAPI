<?php

require_once "../configuration.php";

foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once 'XMLUtil.php';

$conditions = 
array( array("eventId", ">", 0) );
$event = new Event();
$event->DeleteList($conditions, true);
echo "Deep delete of all Events, includes related Participants and Locations.\n";


$conditions = 
array( array("participantId", ">", 0) );
$participant = new Participant();
$participant->DeleteList($conditions);
echo "Delete of all Participants.\n";

echo "All data removed.\n\n";

generateNewData();

function generateNewData()
{
	echo "Generating new object structures and adding to database.\n\n";
	$xmlUtil = new XMLUtil();
	
	for ($i=0; $i<2; $i++) {
		$event = new Event();
		$event->eventTitle = 'Title '.$i;
		
		for ($j=0; $j<2; $j++) {
			$participant = new Participant();
			$participant->firstName = 'First '.$j;
			$participant->lastName = 'Last '.$j;
			$participant->email = 'Email '.$j;
			
	//		$xml = $xmlUtil->GetParticipantXML($participant);
	//		print $xml->saveXML();
			
			$event->AddParticipant($participant);
		}
		
		for ($k=0; $k<2; $k++) {
			$location = new Location();
			$location->establishment = 'Establishment '.$k;
			$event->AddLocation($location);
			
	//		$xml = $xmlUtil->GetLocationXML($location);
	//		print $xml->saveXML();
		}
		
		$event->Save(true);
		
		$participantsArray = $event->GetParticipantList( array( array("participantId", ">", 0) ) );
		$locationsArray = $event->GetLocationList( array( array("locationId", ">", 0) ) );
		
		foreach ($participantsArray as $i => $value) 
		{
	    	$participant = $participantsArray[$i];
	    	$location = $locationsArray[$i];
	    	$vote = new Vote();
	    	$vote->participantId = $participant->participantId;
	    	$vote->locationId = $location->locationId;
	    	$event->AddVote($vote);
		}
		$event->Save(true);
		
		$xml = $xmlUtil->GetEventXML($event);
		print $xml->saveXML();
		
		
	}
	
	echo "\nComplete.\n";
}

?>