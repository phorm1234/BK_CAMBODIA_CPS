<?php
	//define date time zone
	date_default_timezone_set("Asia/Bangkok");

	// Define path to application directory
	
	defined('APPLICATION_PATH')
	    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../appzone/sales'));
	    
	//define fpdf path
	defined('FPDF_PATH')
		|| define('FPDF_PATH', realpath(dirname(__FILE__) . '/../../libraryzone/FPDF'));
	    
	defined('REPORT_PATH')
	    || define('REPORT_PATH', realpath(dirname(__FILE__) . '/../../htdocs/sales/report'));
	    
	defined('LOGO_PATH')
	    || define('LOGO_PATH', realpath(dirname(__FILE__) . '/../../htdocs/sales/img/op'));
	
	
	defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../../appzone/pos'));
   
	    
	//define layout path
	defined('LAYOUT_PATH')
		|| define('LAYOUT_PATH', realpath(dirname(__FILE__) . '/../../appzone/pos'));
	
	
	// Define application environment
	defined('APPLICATION_ENV')
	    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
	
	// Ensure library/ is on include_path
	set_include_path(implode(PATH_SEPARATOR, array(
	    realpath(APPLICATION_PATH . '/../../libraryzone'),
	    realpath(APPLICATION_PATH . '/../../libraryzone/library.11.5'),
	    get_include_path(),
	)));
	
	/** Zend_Application */
	require_once 'Zend/Application.php';	
	
	
	// Create application, bootstrap, and run
	$application = new Zend_Application(
	    APPLICATION_ENV,
	    APPLICATION_PATH . '/configs/application.ini'
	);
	$application->bootstrap()
	            ->run();
 