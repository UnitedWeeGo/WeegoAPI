<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/badge.reset.class.php';

$deviceReset = new BadgeResetClass();
$deviceReset->BadgeResetGo();

?>