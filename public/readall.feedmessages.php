<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/readall.feedmessage.class.php';

$readMessageObj = new ReadAllFeedMessages();
$readMessageObj->ReadAllFeedMessagesGo();

?>