<?php
header("Cache-Control: no-cache, must-revalidate");
header ("Content-Type:text/plain");

require_once "../configuration.php";

$logFilePath = $GLOBALS['configuration']['log_location_file'];

$current = file_get_contents($logFilePath);

echo $current;

?>