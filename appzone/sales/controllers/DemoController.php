<?php 
	class DemoController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
		}
		
		function preDispatch()
		{
			$this->_helper->layout()->setLayout('default_layout');//default_layout
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if($empprofile['user_id']==''){
				$this->_redirect('/pos/logout/index');
			}
			$res=SSUP_Controller_Plugin_Db::check_log_out($empprofile['user_id'],$empprofile['group_id'],$empprofile['perm_id']);
			if($res== "out"){
				$this->_redirect('/pos/logout/index');
			}			
			$this->view->session_employee_id=$empprofile['employee_id'];	
		}//func
		
		function indexAction(){
			$objTest=new Model_PosGlobal();
			$objTest->testFetch();
			$objTax=new Model_Cashier();
			$percen_tax=$objTax->getTax("THA","V");			
		}//func
		function toolAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		function examAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		function testAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		
	}
?>