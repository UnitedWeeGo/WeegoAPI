<?php

header ("Content-Type:text/xml");
require_once '../apiRequestHandlers/checkin.class.php';

$ref = new Checkin();
$ref->CheckinGo();

?>