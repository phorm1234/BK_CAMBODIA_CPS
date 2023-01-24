<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
        {
        // Add autoloader empty namespace
        $autoLoader = Zend_Loader_Autoloader::getInstance();
                 $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                 'basePath'       => APPLICATION_PATH,
                 'namespace' => '',
                 'resourceTypes' => array(
                          'form' => array(
                          'path' => 'forms/',
                          'namespace' => 'Form_',
                    ),
                      'model' => array(
                          'path' => 'models/',
                          'namespace' => 'Model_',
                    ),
                                                      ),     ));
                                                      
					Zend_View_Helper_PaginationControl::setDefaultViewPartial('page.phtml');
                    return $autoLoader;
                }
                
    protected function _initMailTransport()
		{
		        // configure and create the SMTP connection
		        $emailConfig = $this->getOption('email') ;
		        $mailTransport = new Zend_Mail_Transport_Smtp(
		                                 $emailConfig['TransportOptions']['host'],
		                                 $emailConfig['TransportOptions']);
		        Zend_Mail::setDefaultTransport($mailTransport);
		}

	protected function _initDoctype()
		{
		    $this->bootstrap('view');
		    $view = $this->getResource('view');
		    $view->doctype('XHTML1_STRICT');
		}

	public function _initDbAdapter()
	{
		$resource = $this->getPluginResource('multidb');
		$resource->init() ;
		$db1 = $resource->getDb('db1');
		$db2 = $resource->getDb('db2');
		Zend_Registry::set('db1', $db1);
		Zend_Registry::set('db2', $db2);
	} 
	
	protected function _initPlugins() {		
		$autoLoader = Zend_Loader_Autoloader::getInstance ();
		$autoLoader->registerNamespace ('SSUP');
    	$objFront = Zend_Controller_Front::getInstance();
    	$objFront->throwExceptions(true);
    	$objFront->registerPlugin(new SSUP_Controller_Plugin_PosGlobal(),1);
    	return $objFront;
    }
		
	
}
