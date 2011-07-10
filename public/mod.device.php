<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/device.class.php';

$deviceAdd = new DeviceClass();
$deviceAdd->DeviceGo();

?>