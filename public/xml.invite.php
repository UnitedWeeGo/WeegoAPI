<?php

header ("Content-Type:text/xml"); 

require_once '../apiRequestHandlers/xml.invite.class.php';

$xmlRequestHandler = new XMLInviteClass();
$xmlRequestHandler->XMLInviteGo();

?>