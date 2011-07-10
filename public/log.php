<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/log.class.php';

$logRef = new LogClass();
$logRef->LogClassGo();

?>