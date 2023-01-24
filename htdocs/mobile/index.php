<?php
define('ROOT_DIR', dirname(dirname(__FILE__)));

//Error Reporting
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');

ini_set('include_path', ini_get('include_path')
		. PATH_SEPARATOR . 'application/models' 
		. PATH_SEPARATOR . '../../libraryzone/library.11.5');
//require_once 'library/Zend/Captcha/Figlet.php';		
require_once '../../libraryzone/library.11.5/Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

Zend_Loader::loadClass('Zend_Controller_Front');

// load configuration
$config = new Zend_Config_Ini('application/config2.ini', 'app');
$title  = $config->appName;
$params = $config->database->toArray();
$registry = Zend_Registry::getInstance();
//db
$DB = new Zend_Db_Adapter_Pdo_Mysql($params); 
//$DB->setFetchMode(DB_FETCHMODE_OBJECT);
$registry->set('DB',$DB);
//Zend_Registry
$registry->set('config', $config);

#Enable Layout
$layout = Zend_Layout::startMvc(array('layoutPath' => 'application/views/layouts'));

//get the front contorller instance
$front = Zend_Controller_front::getInstance();
$front->throwExceptions(true);
$front->setControllerDirectory('application/controllers');

//Go Go Go
$front->dispatch();

?>
