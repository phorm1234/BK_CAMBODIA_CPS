<?php 
	class MiscellaneousController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('stock_layout');
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
			/*$this->view->arr_layout=array(
						'brand'=>$templete,
						'shop'=>$shop,
						'comno'=>$comno,
						'user_id'=>$user_id
			);
			$this->view->templete=$templete;*/
		}
		
		public function formcontactbillvtAction(){
			$filter = new Zend_Filter_StripTags();
			//$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			//$obj=new Model_Miscellaneous();
		}
		
		public function getdocnobillAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp")); 
			$obj=new Model_Stock();
			$view=$obj->getdoctranferto($doc_tp,"trn_diary1");
			$this->view->data=$view;
		}
		
		public function getbillvtdetailAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Stock();
			$view=$obj->getbillvtdetail($doc_no);
			$this->view->arr_data=$view;
		}
		
		public function editcontactbillvtAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$edit_doc_no = $filter->filter($this->getRequest()->getParam("edit_doc_on"));
			$fullname = $filter->filter($this->getRequest()->getParam("fullname"));
			$addr1 = $filter->filter($this->getRequest()->getParam("addr1"));
			$addr2 = $filter->filter($this->getRequest()->getParam("addr2"));
			$addr3 = $filter->filter($this->getRequest()->getParam("addr3"));
			$obj=new Model_Miscellaneous();
			$arr=array(
						'edit_doc_no'=>$edit_doc_no,
						'fullname'=>$fullname,
						'addr1'=>$addr1,
						'addr2'=>$addr2,
						'addr3'=>$addr3
					  );
			$view=$obj->editcontactbillvt($arr);
			$this->view->data=$view;
		}
		
		public function configprinterAction(){
			$obj=new Model_Miscellaneous();
			$code_type="THERMAL_PRINTER";
			$view=$obj->getcodetype($code_type);
			$this->view->code_type=$view;
		}
		
		public function editconfigprinterAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$code_type = $filter->filter($this->getRequest()->getParam("code_type"));
			$default_status = $filter->filter($this->getRequest()->getParam("default_status"));
			$obj=new Model_Miscellaneous();
			$view=$obj->editconfigprinter($code_type,$default_status);
			$this->view->data=$view;
		}
		
		public function formgpcornerAction(){
			$txt="กำหนด GP จุดขาย Corner";
			$this->view->txt=$txt;
		}
		
		public function getgpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$barcode = $filter->filter($this->getRequest()->getParam("barcode"));
			$txt = $filter->filter($this->getRequest()->getParam("txt"));
			$this->view->barcode=$barcode;
			$this->view->txt=$txt;
			$obj=new Model_Stock();
		}
		
		public function getdatagpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$barcode = $filter->filter($this->getRequest()->getParam("barcode"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Miscellaneous();
			$view=$obj->getdatagp($sort,$barcode);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
		}
		
		public function getsearchgpAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Miscellaneous();
			$sort=" start_date ASC";
			$view=$obj->getdatagp($sort,'');
			$this->view->data=$view;
		}
		
		public function formgpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_barcode = $filter->filter($this->getRequest()->getParam("barcode"));
			$obj=new Model_Miscellaneous();
			$ex_barcode=explode(",",$get_barcode);
			$barcode=$ex_barcode[0];
			$sort=" start_date ASC";
			if(!empty($barcode)){
				$view=$obj->getdatagp($sort,$barcode);
			}else{
				$view="n";
			}
			$this->view->data=$view;
		}
		
		public function saveformgpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$barcode = $filter->filter($this->getRequest()->getParam("i_barcode"));
			$edit_barcode = $filter->filter($this->getRequest()->getParam("barcode"));
			$desc = $filter->filter($this->getRequest()->getParam("desc"));
			$gp = $filter->filter($this->getRequest()->getParam("gp"));
			$start_date = $filter->filter($this->getRequest()->getParam("start_date"));
			$end_date = $filter->filter($this->getRequest()->getParam("end_date"));
			$status_edit= $filter->filter($this->getRequest()->getParam("status_edit"));
			$arr=array(
						'barcode'=>$barcode,
						'edit_barcode'=>$edit_barcode,
						'desc'=>$desc,
						'gp'=>$gp,
						'start_date'=>$start_date,
						'end_date'=>$end_date,
						'status_edit'=>$status_edit
					   );
					   
			$obj=new Model_Miscellaneous();
			$view=$obj->saveformgp($arr);
			//print_r($view);
			$this->view->data=$view;
		}
		
		public function deletegpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$barcode = $filter->filter($this->getRequest()->getParam("barcode"));
			$ex_id=explode(",",$barcode);
			$barcode=$ex_id[0];
			$obj=new Model_Miscellaneous();
			$view=$obj->deletegp($barcode);
			$this->view->data=$view;
		}
		
	}