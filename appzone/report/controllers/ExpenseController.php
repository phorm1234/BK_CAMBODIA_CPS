<?php 
class ExpenseController extends Zend_Controller_Action{
		public $_CORPORATION_ID;
		public $_COMPANY_ID;
		public $_BRANCH_ID;
		public $_BRANCH_NO;
		public $_BRANCH_NAME;
		public $_USER_ID;
		public $_USER_NAME;
		public $_USER_SURNAME;
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('report_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$templete="templete_".$brand;
			$user_id="";
			/*
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile; 
			*/
			
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			/*
			echo "<pre>";
			print_r($empprofile);
			echo "</pre>";
			*/
			if(!empty($empprofile)){
				$this->_CORPORATION_ID=$empprofile['corporation_id'];
				$this->_COMPANY_ID=$empprofile['company_id'];
				$this->_BRANCH_ID=$empprofile['branch_id'];
				$this->_BRANCH_NO=$empprofile['branch_no'];
				$this->_BRANCH_NAME=$empprofile['branch_name'];
				$this->_USER_NAME=$empprofile['name'];
				$this->_USER_SURNAME=$empprofile['surname'];
			
					$this->_COMPANY_ID=strtolower($empprofile['company_id']);
					$this->_USER_ID=$empprofile['user_id'];
					$templete="templete_".$this->_COMPANY_ID;
			     	if(!empty($this->_USER_ID)){
						$this->view->checklogin=1;
					}
				
			}
			
			$this->view->arr_layout=array(
						'brand'=>$templete,
						'shop'=>$shop,
						'comno'=>$comno,
						'user_id'=>$user_id
			);
			$this->view->templete=$templete;
		}
		
		public function preDispatch(){
		}
		
		public function indexAction(){
		}
                
		public function rptexpenseAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$date_start=$filter->filter($this->getRequest()->getPost('date_start'));
				$date_stop=$filter->filter($this->getRequest()->getPost('date_stop'));				
				$objRpt=new Model_Expense();
				$this->view->arr_expense=$objRpt->getExpense($date_start,$date_stop);
				$this->view->sum_amount=$objRpt->getSumExpense($date_start,$date_stop);
				
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->user_id=$this->_USER_ID;
				$this->view->user_fullname=$this->_USER_NAME." ".$this->_USER_SURNAME;
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$this->view->doc_date=$objPos->getDocDatePos($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_BRANCH_NO);
				$arr_dstart=explode('-',$date_start);
				$this->view->date_start=$arr_dstart[2]."/".$arr_dstart[1]."/".$arr_dstart[0];
				$arr_dstop=explode('-',$date_stop);
				$this->view->date_stop=$arr_dstop[2]."/".$arr_dstop[1]."/".$arr_dstop[0];
				
			}			
		}//func
		
		public function rptexpensepreviewsAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$date_start=$filter->filter($this->getRequest()->getPost('date_start'));
				$date_stop=$filter->filter($this->getRequest()->getPost('date_stop'));				
				$objRpt=new Model_Expense();
				$this->view->arr_expense=$objRpt->expensePreviews($date_start,$date_stop);
			}			
		}//func
                
   }//class
   ?>