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
echo "Deep delete of all Events, includes related Participants, Votes and Locations.\n";


$conditions = 
array( array("participantId", ">", 0) );
$participant = new Participant();
$participant->DeleteList($conditions);
echo "Delete of all Participants.\n";

echo "All data removed.\n\n";


?>