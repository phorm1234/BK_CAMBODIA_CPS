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
    /*            
    protected function _initMailTransport()
	{
	        // configure and create the SMTP connection
	        $emailConfig = $this->getOption('email') ;
	        $mailTransport = new Zend_Mail_Transport_Smtp(
	                                 $emailConfig['TransportOptions']['host'],
	                                 $emailConfig['TransportOptions']);
	        Zend_Mail::setDefaultTransport($mailTransport);
	}
	*/
	protected function _initDoctype()
	{
	    $this->bootstrap('view');
	    $view = $this->getResource('view');
	    $view->doctype('XHTML1_STRICT');
	}
	public function _initDbAdapter()
	{
		$resource = $this->getPluginResource('multidb');
		$resource->init();
		$dbOfline="";
		$dbOfline = $resource->getDb('dbOfline');
		Zend_Registry::set('dbOfline', $dbOfline);
	} 
	protected function _initPlugins() {	
		$autoLoader = Zend_Loader_Autoloader::getInstance ();
		$autoLoader->registerNamespace ('SSUP');
		$objCall=new SSUP_Controller_Plugin_Db();
		$objCall->processDb('','');
		$dbOnline=Zend_Registry::get('dbOnline');

    	$objFront = Zend_Controller_Front::getInstance();
    	$objFront->throwExceptions(true);
    	$objFront->registerPlugin(new SSUP_Controller_Plugin_PosGlobal(),1);
    	$objFront->registerPlugin(new SSUP_Controller_Plugin_Menu(),2);
    	return $objFront;
    }
    
    protected function _initSession(){
		# set up the session as per the config.
		$options = $this->getOptions();
		$sessionOptions = array(
			'gc_probability'    =>    $options['resources']['session']['gc_probability'],
			'gc_divisor'        =>    $options['resources']['session']['gc_divisor'],
			'gc_maxlifetime'    =>    $options['resources']['session']['gc_maxlifetime']
		);
	
		$idleTimeout = $options['resources']['session']['idle_timeout'];
	
		Zend_Session::setOptions($sessionOptions);
		Zend_Session::start();
		# now check for idle timeout.
		if(isset($_SESSION['timeout_idle']) && $_SESSION['timeout_idle'] < time()) {
			Zend_Session::destroy();
			Zend_Session::regenerateId();
			$r = new Zend_Controller_Action_Helper_Redirector;
    		$r->gotoUrl('/pos/logout/index')->redirectAndExist();
			
			//header('Location: /pos/logout/index');
			exit();
		}
		$_SESSION['timeout_idle'] = time() + $idleTimeout;
	}//func

}
