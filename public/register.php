<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/register.class.php';

$register = new Register();
$register->RegisterGo();

?>