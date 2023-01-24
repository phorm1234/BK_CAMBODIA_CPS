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
	    }//func
	                
	    
		protected function _initDoctype()
		{
		    $this->bootstrap('view');
		    $view = $this->getResource('view');
		    $view->doctype('XHTML1_STRICT');
		}//func
	
		public function _initDbAdapter()
		{
			$resource = $this->getPluginResource('multidb');
			$resource->init() ;
			$dbDefault = $resource->getDb('dbOfline');
			Zend_Registry::set('dbOfline', $dbDefault);
			
			/*
			$options = $this->getOptions();
			echo "<pre>";
			print_r($options['resources']['multidb']['db1']);
			echo "</pre>";
			*/
						
		}//func 
		
		protected function _initPlugins() {		
			$autoLoader = Zend_Loader_Autoloader::getInstance ();
			$autoLoader->registerNamespace ('SSUP');
	    	$objFront = Zend_Controller_Front::getInstance();
	    	$objFront->throwExceptions(true);
	    	$objFront->registerPlugin(new SSUP_Controller_Plugin_PosGlobal(),1);
	    	$objFront->registerPlugin(new SSUP_Controller_Plugin_Menu(),2);
	    	$objFront->registerPlugin(new SSUP_Controller_Plugin_Db(),3);
	    	return $objFront;
	    }
		
		
		
	}
