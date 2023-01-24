<?php
	class DaytransactionController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
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
			$this->view->session_employee_id=$empprofile['employee_id'];
			$this->SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
			$this->SESSION_USER_ID=$empprofile['user_id'];
		}//func
		
		function opencashdrawerAction(){	
		}//func
		
		function getempopencashdrawerAction(){
			/**
			 * @modify : 05092013
			 */
			$this->_helper->layout->disableLayout();			
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {		
				$employee_id=$filter->filter($this->getRequest()->getPost('employee_id'));		
				$actions = $filter->filter($this->getRequest()->getPost('actions'));
				if($actions=='saleman'){	
					$objDT=new Model_DayTransaction();
					$arr_res=$objDT->getSalemanOpenCashDrawer($employee_id);							
					$this->view->arr_data=$arr_res;
				}
			}
		}//func
		
		function formconfirmopencashdrawerAction(){
			$this->_helper->layout()->disableLayout();
			//$this->_helper->viewRenderer->setNoRender(TRUE);
			$objChk=new Model_DayTransaction();
			$m_cashdrawer=$objChk->getCashDrawer();
			$this->view->m_cashdrawer=$m_cashdrawer;
			$arr_remark=$objChk->getRemarkOpenCashDrawer();
			$this->view->arr_remark=$arr_remark;
		}//func
		
		function confirmopencashdrawerAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$employee_id=$filter->filter($this->getRequest()->getPost('employee_id'));
				$remark_id=$filter->filter($this->getRequest()->getPost('remark_id'));
				$objCashDw=new Model_DayTransaction();
				$res=$objCashDw->openCashDrawer($employee_id,$remark_id);
				echo $res;
			}
		}//func
		
		function roundexpenseAction(){
			$this->_helper->layout->disableLayout();
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$objTrans=new Model_DayTransaction();
				$this->view->arr_roundexpense=$objTrans->getRoundExpense();
			}
		}//func
		
		function expenseAction(){
			//$this->_helper->layout()->disableLayout();
		}
		
		function equipmentAction(){
		}//func
		
		function saveexpenseAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_dt=$filter->filter($this->getRequest()->getPost('doc_dt'));
				$json=$filter->filter($this->getRequest()->getPost('json_string'));
				$objExpense=new Model_DayTransaction();
				$result=$objExpense->saveExpense($json,$doc_dt);
			}
		}//func
		
		function waitequipmentAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$strjson=$filter->filter($this->getRequest()->getPost('json_string'));
				$objEquipment=new Model_DayTransaction();
				$result=$objEquipment->waitEquipment($strjson);
				echo $result;
			}
		}//func
		
		function initequipmentAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objEquipment=new Model_DayTransaction();
			$result=$objEquipment->initEquipment();
			echo $result;
		}//func
		
		function chkwaitAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objEquipment=new Model_DayTransaction();
			$result=$objEquipment->chkWait();
			echo $result;
		}//func
		
		function clearwaitAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objEquipment=new Model_DayTransaction();
			$result=$objEquipment->clearWait();
			echo $result;
		}//func
		
		function saveequipmentAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$strjson=$filter->filter($this->getRequest()->getPost('json_string'));
				$flg_status=$filter->filter($this->getRequest()->getPost('flg'));
				$objEquipment=new Model_DayTransaction();
				$result=$objEquipment->saveEquipment($strjson,$flg_status);
				echo $result;
			}
		}//func
		
		function getexpenseAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_dt=$filter->filter($this->getRequest()->getPost('doc_dt'));		
				$objExpense=new Model_DayTransaction();
				$objExpense->getExpense($doc_dt);	
			}
		}//func
		
		function chkequipmentdateAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$objTrans=new Model_DayTransaction();
				$status_chk=$objTrans->chkEquipmentDate();
				echo $status_chk;	
			}
		}//func
		
		function getequipmentAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$flg_status=$filter->filter($this->getRequest()->getPost('flg'));
				$objTrans=new Model_DayTransaction();
				$objTrans->getEquipment($flg_status);	
			}
		}//func
		
		function getdocdtAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objExpense=new Model_DayTransaction();
			$arr_expense=$objExpense->getDocDt('');
			//$result=$arr_expense['expense_month'].",".$arr_expense['doc_dt'].",".$arr_expense['start_date'].",".$arr_expense['end_date'];
			echo json_encode($arr_expense);	
		}//func
	}//class 
?>