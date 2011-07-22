<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/remove.event.class.php';

$requestHandler = new RemoveEventClass();
$requestHandler->RemoveEventGo();

?>