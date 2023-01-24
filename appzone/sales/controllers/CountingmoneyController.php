<?php
	class CountingmoneyController extends Zend_Controller_Action{
		private $_SESSION_EMPLOYEE_ID;
		private $_SESSION_USER_ID;
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->date = date('Y-m-d');
		}//func
		
		function preDispatch()
		{
			
				$this->_helper->layout()->setLayout('default_layout');
				$session = new Zend_Session_Namespace('empprofile');
				$empprofile=$session->empprofile; 
				if($empprofile['user_id']==''){
					$this->_redirect('../pos/logout/index');
					exit();
				}
				$res=SSUP_Controller_Plugin_Db::check_log_out($empprofile['user_id'],$empprofile['group_id'],$empprofile['perm_id']);
				if($res== "out"){
					$this->_redirect('../pos/logout/index');
					exit();
				}			
				$this->view->employee_id=$empprofile['employee_id'];
				$this->_SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
				$this->_SESSION_USER_ID=$empprofile['user_id'];
			
		}//func preDispatch
		
		public function indexAction(){
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$this->view->company 	= $empprofile['company_name'];
			$this->view->branch 	= $empprofile['branch_name'];
			$this->view->branch_no 	= $empprofile['branch_no'];
			//print_r($empprofile);
		}	
		
		public function checkempAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$eid =$filter->filter($this->getRequest()->getPost('eid'));
			$obj = new Model_Counting();
			$tmp = $obj->checkemp($eid);
			echo json_encode($tmp);
		}
		
		public function checkdocstatusAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Counting();
			$tmp = $obj->checkdocstatus();
			echo json_encode($tmp);
		}
		
		public function savedataAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$filter = new Zend_Filter_StripTags();
			$eid =$filter->filter($this->getRequest()->getPost('eid'));
			$status_no = $filter->filter($this->getRequest()->getPost('status_no'));
			if($status_no == 1){
				$status = "O";
			}else if($status_no == 2){
				$status = "C";
			}
			$data = array(
						"corporation_id"=>$empprofile['corporation_id'],
						"company_id"=>$empprofile['company_id'],
						"branch_id"=>$empprofile['branch_id'],
						"doc_date"=>$this->date,
						"doc_time"=>date('H:i:s'),
						"time_count"=>$status_no,
						"status"=>$status,
						"amount_s_01"=>$filter->filter($this->getRequest()->getPost('s1000')),
						"amount_s_02"=>$filter->filter($this->getRequest()->getPost('s500')),
						"amount_s_03"=>$filter->filter($this->getRequest()->getPost('s100')),
						"amount_s_04"=>$filter->filter($this->getRequest()->getPost('s50')),
						"amount_s_05"=>$filter->filter($this->getRequest()->getPost('s20')),
						"amount_s_06"=>$filter->filter($this->getRequest()->getPost('s10')),
						"amount_s_07"=>$filter->filter($this->getRequest()->getPost('s5')),
						"amount_s_08"=>$filter->filter($this->getRequest()->getPost('s2')),
						"amount_s_09"=>$filter->filter($this->getRequest()->getPost('s1')),
						"amount_s_10"=>$filter->filter($this->getRequest()->getPost('s05')),
						"amount_s_11"=>$filter->filter($this->getRequest()->getPost('s025')),
						"amount_c_01"=>$filter->filter($this->getRequest()->getPost('c1000')),
						"amount_c_02"=>$filter->filter($this->getRequest()->getPost('c500')),
						"amount_c_03"=>$filter->filter($this->getRequest()->getPost('c100')),
						"amount_c_04"=>$filter->filter($this->getRequest()->getPost('c50')),
						"amount_c_05"=>$filter->filter($this->getRequest()->getPost('c20')),
						"amount_c_06"=>$filter->filter($this->getRequest()->getPost('c10')),
						"amount_c_07"=>$filter->filter($this->getRequest()->getPost('c5')),
						"amount_c_08"=>$filter->filter($this->getRequest()->getPost('c2')),
						"amount_c_09"=>$filter->filter($this->getRequest()->getPost('c1')),
						"amount_c_10"=>$filter->filter($this->getRequest()->getPost('c05')),
						"amount_c_11"=>$filter->filter($this->getRequest()->getPost('c025')),
						"amount_p_01"=>$filter->filter($this->getRequest()->getPost('p1000')),
						"amount_p_02"=>$filter->filter($this->getRequest()->getPost('p500')),
						"amount_p_03"=>$filter->filter($this->getRequest()->getPost('p100')),
						"amount_p_04"=>$filter->filter($this->getRequest()->getPost('p50')),
						"amount_p_05"=>$filter->filter($this->getRequest()->getPost('p20')),
						"amount_p_06"=>$filter->filter($this->getRequest()->getPost('p10')),
						"amount_p_07"=>$filter->filter($this->getRequest()->getPost('p5')),
						"amount_p_08"=>$filter->filter($this->getRequest()->getPost('p2')),
						"amount_p_09"=>$filter->filter($this->getRequest()->getPost('p1')),
						"amount_p_10"=>$filter->filter($this->getRequest()->getPost('p05')),
						"amount_p_11"=>$filter->filter($this->getRequest()->getPost('p025')),
						"total_amount"=>$filter->filter($this->getRequest()->getPost('total_s')),
						"other_amount"=>$filter->filter($this->getRequest()->getPost('total_c')),
						"special_amount"=>$filter->filter($this->getRequest()->getPost('total_p')),
						"pay_in"=>$filter->filter($this->getRequest()->getPost('pay')),
						"pay_in_c"=>$filter->filter($this->getRequest()->getPost('pay_c')),
						"pay_in_p"=>$filter->filter($this->getRequest()->getPost('pay_p')),
						"balance_amount"=>$filter->filter($this->getRequest()->getPost('mnt_s')),
						"balance_amount_c"=>$filter->filter($this->getRequest()->getPost('mnt_c')),
						"balance_amount_p"=>$filter->filter($this->getRequest()->getPost('mnt_p')),
						"user_id"=>$filter->filter($this->getRequest()->getPost('eid')),
						"saleman_id"=>$empprofile['employee_id'],
						"remark1"=>$filter->filter($this->getRequest()->getPost('txt_remark'))
					);
			//print_r($data);
			$obj = new Model_Counting();
			$tmp = $obj->savedata($data);
			echo $tmp;
			
		}
		
		public function reportcountingAction(){
			$this->view->date = date('Y-m-d');
		}
		
		public function checkprintingAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$filter = new Zend_Filter_StripTags();
			$date =$filter->filter($this->getRequest()->getPost('ddate'));
			$tmp  = explode('/',$date);
			$date = $tmp[2].'-'.$tmp[0].'-'.$tmp[1];
			$type = $filter->filter($this->getRequest()->getPost('typecounting'));
			$obj = new Model_Counting();
			$status = $obj->checkprinting($date,$type);
			echo $status;
		}
		
		public function printingAction(){
			//$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$filter = new Zend_Filter_StripTags();
			$date =$filter->filter($this->getRequest()->getPost('ddate'));	
			$type = $filter->filter($this->getRequest()->getPost('typecounting'));
			$obj = new Model_Counting();
			$tmp  = explode('/',$date);
			$date = $tmp[2].'-'.$tmp[0].'-'.$tmp[1];
			$tmp = $obj->printing($date,$type);
			//print_r($tmp);
			//$this->view->user_id  = $tmp[0]['user_id'];
			$this->view->content = $tmp;
			$this->view->branch 	= $empprofile['branch_name'];
			$this->view->branch_no 	= $empprofile['branch_no'];
		}
		
				
	}//class 
?>