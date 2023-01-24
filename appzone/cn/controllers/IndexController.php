<?php 
	class IndexController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
		}//func
		
		public function indexAction(){
			$this->_helper->layout()->setLayout('/index_layout');
			$this->view->greet="Hello my CN project!";
		}//func
	}
?>