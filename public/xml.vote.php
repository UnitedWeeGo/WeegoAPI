<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/xml.vote.class.php';

$xmlRequestHandler = new XMLVoteClass();
$xmlRequestHandler->XMLVoteGo();

?>