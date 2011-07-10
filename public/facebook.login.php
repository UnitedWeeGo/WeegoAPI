<?php

header ("Content-Type:text/xml"); 
require_once '../apiRequestHandlers/facebook.login.class.php';

$loginRef = new FacebookLogin();
$loginRef->FacebookLoginGo();

?>