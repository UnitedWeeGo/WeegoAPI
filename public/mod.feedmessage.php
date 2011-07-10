<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/feedmessage.class.php';

$fm = new FeedMessageClass();
$fm->FeedMessageGo();

?>