<?php

header ("Content-Type:text/xml");
require_once '../apiRequestHandlers/pair.class.php';

$ref = new Pair();
$ref->PairGo();

?>