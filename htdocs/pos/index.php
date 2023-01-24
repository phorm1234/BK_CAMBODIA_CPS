<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../appzone/pos'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../../libraryzone'),
    realpath(APPLICATION_PATH . '/../../libraryzone/library.11.5'),
    get_include_path(),
)));

//define layout path
	defined('LAYOUT_PATH')
		|| define('LAYOUT_PATH', realpath(dirname(__FILE__) . '/../../appzone/pos'));
		
//define upload path
	defined('UPLOAD_PATH')
		|| define('UPLOAD_PATH', realpath(dirname(__FILE__) . '/images/upload'));
		

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


$application->bootstrap()->run();
