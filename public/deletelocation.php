<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/deletelocation.class.php';

$requestHandler = new DeleteLocationClass();
$requestHandler->DeleteLocationGo();

?>