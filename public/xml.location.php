<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/xml.location.class.php';

$xmlRequestHandler = new XMLLocationClass();
$xmlRequestHandler->XMLLocationGo();

?>