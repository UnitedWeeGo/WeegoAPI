<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
    
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
    
// Set library/ as include_path
set_include_path(
    realpath(APPLICATION_PATH . '/library')
);

require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance()->registerNamespace('Weego_');

$weego = new Weego_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configuration/application.ini'
);

$weego->run();

//echo memory_get_usage();