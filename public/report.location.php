<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/report.location.class.php';

$location = new ReportLocationClass();
$location->ReportLocationGo();
?>