<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/event.class.php';

$event = new EventClass();
$event->EventGo();

?>