<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/get.report.location.class.php';

$lookup = new GetReportLocations();
$lookup->GetReportLocationsGo();

?>