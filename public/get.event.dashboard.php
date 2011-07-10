<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/get.event.class.php';

$events = new GetEvents();
$events->lightEventInfo = true;
$events->GetEventsGo();

?>