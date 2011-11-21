<?php

header ("Content-Type:application/json"); 
require_once '../apiRequestHandlers/search.yelp.class.php';

$ref = new SearchYelpClass();
$ref->SearchYelpGo();

?>