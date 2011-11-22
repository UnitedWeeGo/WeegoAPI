<?php

header ("Content-Type:application/json"); 
require_once '../apiRequestHandlers/get.categories.class.php';

$ref = new GetCategoriesClass();
$ref->GetCategoriesGo();

?>