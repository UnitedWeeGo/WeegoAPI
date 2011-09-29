<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/toggle.decided.class.php';

$requestHandler = new ToggleDecidedClass();
$requestHandler->ToggleDecidedGo();

?>