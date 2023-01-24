<?php
	class IndexController extends Zend_Controller_Action
	{
		public function init()
		{	
			//for overide lifetime session
			/*
			$sessionLifetime=2000;
			Zend_Session::rememberMe($sessionLifetime); 
			$saveHandler = Zend_Session::getSaveHandler();
			$saveHandler->setLifetime($sessionLifetime)
            			->setOverrideLifetime(true); 
			*/

			$this->_helper->layout()->setLayout('sales_layout');
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();	
			
		}//func		
		
		function preDispatch()
		{
			/*
			$session_user = new Zend_Session_Namespace('USERLOGIN');
			if(!isset($session_user->arr_login['user_login'])){				
				$this->_redirect('/auth/');
			}
			*/
			
			//assume WR21052011
			$this->view->timeTilEnd = "aaa";			
			$this->view->arr_login="aaa";
			$this->view->user="aaa";
			$this->view->m_corporation_id ="aaa";
			$this->view->m_company_id ="aaa";
			$this->view->m_company_name ="aaa";
	
			$this->view->m_branch_id ="aaa";
			$this->view->m_branch_no ="aaa";
			$this->view->m_branch_tp ="aaa";
			$this->view->m_acc_no ="aaa";
			$this->view->m_pos_id ="aaa";
			$this->view->m_tel ="aaa";
			
			$this->view->m_thermal_printer ="aaa";
			$this->view->m_network ="aaa";
			$this->view->m_computer_no ="aaa";
			$this->view->m_check_date ="aaa";
			$this->view->time_system="aaa";
			
			
			//remind session before expire
			/*
			$this->view->timeTilEnd = $session_user->arr_login['endOfTimer'];			
			$this->view->arr_login=$session_user->arr_login['user_login'];
			$this->view->user=$session_user->arr_login['user_login'];   
			$this->view->m_corporation_id =$session_user->arr_login['m_corporation_id'];
			$this->view->m_company_id =$session_user->arr_login['m_company_id'];
			$this->view->m_company_name =$session_user->arr_login['m_company_name'];
	
			$this->view->m_branch_id =$session_user->arr_login['m_branch_id'];
			$this->view->m_branch_no =$session_user->arr_login['m_branch_no'];
			$this->view->m_branch_tp =$session_user->arr_login['m_branch_tp'];
			$this->view->m_acc_no =$session_user->arr_login['m_acc_no'];
			$this->view->m_pos_id =$session_user->arr_login['m_pos_id'];
			$this->view->m_tel =$session_user->arr_login['m_tel'];
			
			$this->view->m_thermal_printer =$session_user->arr_login['m_thermal_printer'];
			$this->view->m_network =$session_user->arr_login['m_network'];
			$this->view->m_computer_no =$session_user->arr_login['m_computer_no'];
			$this->view->m_check_date =$session_user->arr_login['m_check_date'];
			
			$this->view->time_system=$session_user->arr_login['time_system'];
			*/
			
		}//func
		
		public function indexAction()
		{	
			//$objx=new SSUP_Controller_Plugin_PosGlobal();
			//echo $objx->getBook();	
			//$objc=new SSUP_Controller_Plugin_Test();
			//print_r($objc);
			/*
			$session_user = new Zend_Session_Namespace('USERLOGIN');
			if(!isset($session_user->arr_login['user_login'])){				
				//$this->_redirect('../pos/auth/');
				exit();
			}
			*/
			$this->_helper->layout()->setLayout('sales_layout');
		}//func
		
		public function getdatetimeserverAction(){
			$this->_helper->layout()->disableLayout();
			$objDate=new Model_PosGlobal();
			$sdatetime=$objDate->getDateTimeServer();
			$this->view->sdatetime=$sdatetime;
		}//
		
		public function menuAction(){
			$objMenu=new Model_Menu();
			$aa=$objMenu->getMenu('');
			$xx=$objMenu->menuHtml($aa);
			echo $xx;
		}//func
	
	}//class	
	
?>
