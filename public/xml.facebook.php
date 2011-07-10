<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/xml.facebook.class.php';

$xmlRequestHandler = new XMLFacebookClass();
$xmlRequestHandler->XMLFacebookGo();

?>