<?php

header ("Content-Type:text/javascript"); 
require_once '../apiRequestHandlers/search.yelp.class.php';

$ref = new SearchYelpClass();
$ref->SearchYelpGo();

?>