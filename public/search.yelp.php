<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/search.yelp.class.php';

$ref = new SearchYelpClass();
$ref->SearchYelpGo();

?>