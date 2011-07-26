<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/suggesttime.class.php';

$requestHandler = new SuggestTimeClass();
$requestHandler->SuggestTimeGo();

?>