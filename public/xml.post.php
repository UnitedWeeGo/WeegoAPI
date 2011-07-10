<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/xml.post.class.php';

$xmlRequestHandler = new XMLPostClass();
$xmlRequestHandler->XMLPostGo();

?>