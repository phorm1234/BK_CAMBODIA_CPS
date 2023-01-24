<?php
	class ProductexpireController extends Zend_Controller_Action{
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
			$this->view->SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
			$this->SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
			$this->SESSION_USER_ID=$empprofile['user_id'];
			$this->CHECK_SESSION=$empprofile['employee_id'];
		}//func
		
		function formconfirmdocAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function formcanceldocAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function delproductexpireAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$items=$filter->filter($this->getRequest()->getPost('items'));
				$objDT=new Model_ProductExpire();
				$result=$objDT->delProductExpire($items);
				($result)?$result=1:$result=0;
				echo $result;
			}
		}//func
		
		function bwspdtexpiredocnoAction(){
			$this->_helper->layout()->disableLayout();
			$objDT=new Model_ProductExpire();
			$rows=$objDT->bwsPdtExpireDocNO();
			$this->view->arr_docno=$rows;
		}//func
		
		function cancelproductexpireAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$doc_date=$filter->filter($this->getRequest()->getPost('doc_date'));
				$remark=$filter->filter($this->getRequest()->getPost('remark'));
				$ros_employee_id=$filter->filter($this->getRequest()->getPost('ros_employee_id'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));
				$objDT=new Model_ProductExpire();
				$this->result=$objDT->cancelProductExpire($doc_no,$doc_date,$remark,$cashier_id,$ros_employee_id);
				echo $this->result;
			}
		}//func
		
		function confirmproductexpireAction(){
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$doc_date=$filter->filter($this->getRequest()->getPost('doc_date'));
				$remark=$filter->filter($this->getRequest()->getPost('remark'));
				$ros_employee_id=$filter->filter($this->getRequest()->getPost('ros_employee_id'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier'));
				$objDT=new Model_ProductExpire();
				$this->result=$objDT->confirmProductExpire($doc_no,$doc_date,$remark,$cashier_id,$ros_employee_id);
				echo $this->result;
			}
		}//func
		
		function saveproductexpireAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$doc_date=$filter->filter($this->getRequest()->getPost('doc_date'));
				$remark=$filter->filter($this->getRequest()->getPost('remark'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));
				$objDT=new Model_ProductExpire();
				$this->view->result=$objDT->saveProductExpire($doc_no,$doc_date,$remark,$cashier_id);
			}
		}//func
		
		function getpagesproductexpireAction(){
			$this->_helper->layout->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$objTr=new Model_ProductExpire();
			$cpage=$objTr->getPageTotalProductExpire($rp);
			$this->view->cpage=$cpage;
		}//func
		
		function getproductexpire2confirmAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$objDT=new Model_ProductExpire();
				$objDT->getProductExpire2Confirm($doc_no);
			}
		}//func
		
		function initproductexpiretempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$objDT=new Model_ProductExpire();
				$objDT->initProductExpireTemp();
			}
		}//func
		
		function subtotalproductexpireAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()) {
				$objDT=new Model_ProductExpire();
				$json=$objDT->getSumProductExpireTemp();
				$arr_data=array(
						'action'=>'getSumProductExpireTemp',
						'data'=>$json
				);
				$this->view->arr_data=$arr_data;
			}
		}//func
		
		
		function productexpiretempAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			if($actions=='gettmp'){
				$page=$filter->filter($this->getRequest()->getPost('page'));
				$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
				$query=$filter->filter($this->getRequest()->getPost('query'));
				$rp=$filter->filter($this->getRequest()->getPost('rp'));
				$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
				$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
				$objDT=new Model_ProductExpire();
				$arr_json=$objDT->getCshTempProductExpire($page,$qtype,$query,$rp,$sortname,$sortorder);
				$this->view->arr_json=$arr_json;
			}
		}//func
		
		function chkproductexpireAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_date=$filter->filter($this->getRequest()->getPost('doc_date'));				
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));				
				$objDT=new Model_ProductExpire();
				$result=$objDT->chkProductExpire($doc_date,$product_id,$quantity);
				echo $result;
			}
		}//func
		
		function setproductexpireAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$remark=$filter->filter($this->getRequest()->getPost('remark'));
				$doc_date=$filter->filter($this->getRequest()->getPost('doc_date'));
				$manufac_date=$filter->filter($this->getRequest()->getPost('manufac_date'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));			
				$objDT=new Model_ProductExpire();
				$result=$objDT->setProductExpire($doc_no,$remark,$doc_date,$manufac_date,$product_id,$quantity,$seq,$cashier_id);
				//($result)?$result="1":$result="0";
				$this->view->result=$result;
			}
		}//func
		
		function productexpireAction(){
			if($this->CHECK_SESSION==''){				
				$r = new Zend_Controller_Action_Helper_Redirector;
    			$r->gotoUrl('../pos/logout/index')->redirectAndExist();
				exit();
			}			
			$objPos=new Model_PosGlobal();
			$doc_date=$objPos->checkDocDate();
			if(!$doc_date){
				$this->_redirect('/error/billopen');
				exit();
			}
			$this->view->doc_date=$doc_date;
		}//func
	}//class 
?>