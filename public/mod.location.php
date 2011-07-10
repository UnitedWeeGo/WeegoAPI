<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/location.class.php';

$location = new LocationClass();
$location->LocationGo();
?>