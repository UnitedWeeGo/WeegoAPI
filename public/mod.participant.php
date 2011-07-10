<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/participant.class.php';

$participant = new ParticipantClass();
$participant->ParticipantGo();

?>