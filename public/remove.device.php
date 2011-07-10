<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/remove.device.class.php';

$deviceRemove = new DeviceRemoveClass();
$deviceRemove->DeviceRemoveGo();

?>