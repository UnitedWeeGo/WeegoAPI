<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/get.participantinfo.class.php';

$pinfo = new GetParticipantInfo();
$pinfo->GetParticipantInfoGo();

?>