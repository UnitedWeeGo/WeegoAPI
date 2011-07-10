<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/login.class.php';

$loginRef = new Login();
$loginRef->LoginGo();

?>