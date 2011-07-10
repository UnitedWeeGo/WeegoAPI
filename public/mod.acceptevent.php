<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/acceptevent.class.php';

$requestHandler = new AcceptEventClass();
$requestHandler->AcceptEventGo();

?>