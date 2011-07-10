<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/read.feedmessage.class.php';

$readMessageObj = new ReadFeedMessages();
$readMessageObj->ReadFeedMessagesGo();

?>