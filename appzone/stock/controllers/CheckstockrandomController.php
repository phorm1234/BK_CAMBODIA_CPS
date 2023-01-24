<?php 
	class CheckstockrandomController extends Zend_Controller_Action{
		public $doc_date;
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('checkstockrandom_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile; 
			$this->empprofile=$empprofile;
			
			if(!empty($myprofile)){
				foreach($myprofile as $data){
					$company_id=strtolower($data['company_id']);
					$user_id=$data['user_id'];
					$templete="templete_".$company_id;
			     	if(!empty($user_id)){
						$this->view->checklogin=1;
					}
				}
			}
		}
		
		public function preDispatch(){
           $objPos=new SSUP_Controller_Plugin_PosGlobal();
           $doc_date_pos=$objPos->getDocDatePos($this->empprofile['corporation_id'],$this->empprofile['company_id'],$this->empprofile['branch_id'],$this->empprofile['branch_no']); 
           $this->doc_date=$doc_date_pos;
        }

		public function indexAction(){
			$session = new Zend_Session_Namespace('empprofile');
        	$empprofile=$session->empprofile; 
			echo "<pre>";
			print_r($empprofile);
			echo "</pre>";
		}
		
		public function formcheckstockrandomAction(){
			$date=date("d/m/Y");
			$this->view->date=$date;
		}
		
		public function checkpasswordAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$passw = $filter->filter($this->getRequest()->getParam("passw")); 
			$obj=new Model_Checkstockrandom();
			$view=$obj->checkpassword($passw);
			$this->view->data=$view;
		}
		
		public function inserttempproductrandomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$chk_product = $filter->filter($this->getRequest()->getParam("chk_product")); 
			$obj=new Model_Checkstockrandom();
			//จำนวนวันย้อนหลัง
			$backdate=30;
			$view=$obj->inserttempproductrandom($backdate,$chk_product);
			$this->view->data=$view;
		}
		
		public function viewrandomstockAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstockrandom();
			$view=$obj->viewrandomstock($where='');
			$this->view->data=$view;
		}
		
		public function printrandomstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$option_1 = $this->getRequest()->getParam("option_1");
			$obj=new Model_Checkstockrandom();
			$view=$obj->printrandomstock($option_1);
			$this->view->data=$view;
		}
		
		public function printreportAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstockrandom();
			$where="and (option1!='' or option2='X') ";
			$view=$obj->viewrandomstock($where);
			$this->view->data=$view;
			$sum_all=$obj->sumrandomstock($where);
			$this->view->sum=$sum_all;
			$doc_no_vt=$obj->docnodetail("VT");
			$doc_no_sl=$obj->docnodetail("SL");
			$doc_no_ti=$obj->docnodetail("TI");
			$doc_no_to=$obj->docnodetail("TO");
			$doc_no_cn=$obj->docnodetail("CN");
			$doc_no_ai=$obj->docnodetail("AI");
			$doc_no_ao=$obj->docnodetail("AO");
			$this->view->doc_no_vt=$doc_no_vt;
			$this->view->doc_no_sl=$doc_no_sl;
			$this->view->doc_no_ti=$doc_no_ti;
			$this->view->doc_no_to=$doc_no_to;
			$this->view->doc_no_cn=$doc_no_cn;
			$this->view->doc_no_ai=$doc_no_ai;
			$this->view->doc_no_ao=$doc_no_ao;
			
			$doc_no_vt2=$obj->docnodetail2("VT");
			$doc_no_sl2=$obj->docnodetail2("SL");
			$doc_no_ti2=$obj->docnodetail2("TI");
			$doc_no_to2=$obj->docnodetail2("TO");
			$doc_no_cn2=$obj->docnodetail2("CN");
			$doc_no_ai2=$obj->docnodetail2("AI");
			$doc_no_ao2=$obj->docnodetail2("AO");
			$this->view->doc_no_vt2=$doc_no_vt2;
			$this->view->doc_no_sl2=$doc_no_sl2;
			$this->view->doc_no_ti2=$doc_no_ti2;
			$this->view->doc_no_to2=$doc_no_to2;
			$this->view->doc_no_cn2=$doc_no_cn2;
			$this->view->doc_no_ai2=$doc_no_ai2;
			$this->view->doc_no_ao2=$doc_no_ao2;
			
			$objPos=new SSUP_Controller_Plugin_PosGlobal();
           	$objPos->stockBalance($this->empprofile['corporation_id'],$this->empprofile['company_id'],$this->empprofile['branch_id'],$this->doc_date);	
			
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			
		}
		
	}
?>