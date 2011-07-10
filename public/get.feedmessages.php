<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/get.feedmessage.class.php';

$feedMessages = new GetFeedMessages();
$feedMessages->GetFeedMessagesGo();

?>