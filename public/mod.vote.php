<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/vote.class.php';

$voter = new VoteClass();
$voter->VoteGo();

?>