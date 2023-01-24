<?php 
	class CnController extends Zend_Controller_Action{
		function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
		}//func
	
		function preDispatch()
		{
			$this->_helper->layout()->setLayout('default_layout');			
			//check ip system
			$objPos=new Model_PosGlobal();
			$arr_client=$objPos->getClientProfile();
			if(count($arr_client)==0){
				$this->_redirect('/error/outip');
				exit();
			}
			
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			
			//$idleTimeout = $options['resources']['session']['idle_timeout'];
			if(isset($_SESSION['timeout_idle']) && $_SESSION['timeout_idle'] < time()) {
				Zend_Session::destroy();
				Zend_Session::regenerateId();
				$r = new Zend_Controller_Action_Helper_Redirector;
    			$r->gotoUrl('../pos/logout/index')->redirectAndExist();
				exit();
			}
			
			//$_SESSION['timeout_idle'] = time() + $idleTimeout;
			
			$this->view->SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
			$this->CHECK_SESSION=$empprofile['employee_id'];
		}//func
		
		function indexAction(){
			if($this->CHECK_SESSION==''){
				//$this->_redirect('/error/sessionexpire');
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
			$objPos= new Model_PosGlobal();
			$rows=$objPos->getDocStatusByType('CN');
			$this->view->arr_docstatus=$rows;
		}//func
		
		function chkdocnoAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$objCn=new Model_Cn();
				$result=$objCn->checkCnDocno($doc_no);
				$this->view->result=$result;
			}
			
		}//func
		
		function cntempAction(){
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
				$objCn=new Model_Cn();
				$arr_json=$objCn->getCshTemp($page,$qtype,$query,$rp,$sortname,$sortorder);							
				$this->view->arr_json=$arr_json;
			}
		}//func
		
		function chkseqAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$objCn=new Model_Cn();
				$result=$objCn->chkSeqProduct($doc_no,$product_id);		
				echo $result;		
			}
		}//func
		
		function productcnAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$cn_seq=$filter->filter($this->getRequest()->getPost('cn_seq'));
				$objPos=new Model_PosGlobal();
				$product_id=$objPos->setBarcodeToProductID($product_id);
				$objCn=new Model_Cn();
				$result=$objCn->getCnProduct($cn_seq,$doc_no,$product_id,$quantity);
				$this->view->result=$result;
			}			
		}//func
		
		function setcnAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$seq=$filter->filter($this->getRequest()->getPost('seq'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));				
				
				$cn_tp=$filter->filter($this->getRequest()->getPost('cn_tp'));
				$lot_expire=$filter->filter($this->getRequest()->getPost('lot_expire'));
				$lot_no=$filter->filter($this->getRequest()->getPost('lot_no'));
				$cn_remark=$filter->filter($this->getRequest()->getPost('cn_remark2'));		
				$objCn=new Model_Cn();
				$result=$objCn->setCn($status_no,$doc_no,$product_id,$quantity,$seq,$cashier_id,$cn_tp,$lot_expire,$lot_no,$cn_remark);	
				($result)?$result="1":$result="0";	
				$this->view->result=$result;
			}
		}//func
		
		function bwscntpdescAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function bwscnrefundAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function bwscndocnoAction(){
			$this->_helper->layout()->disableLayout();
			$objCn=new Model_Cn();
			$rows=$objCn->bwsCnDocNO();
			$this->view->arr_cndocno=$rows;
		}//func
		
		function bwscntpAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()){	
				$objCn=new Model_Cn();
				$this->view->arr_cntp=$objCn->bwsCnTp();				
			}
		}//func
		
		function bwscnproductAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()) {	
				$doc_no_ref = $filter->filter($this->getRequest()->getParam('doc_no_ref'));
				$product_id = $filter->filter($this->getRequest()->getParam('product_id'));
				$objCn=new Model_Cn();
				$rows=$objCn->bwsCnProduct($doc_no_ref,$product_id);
				$this->view->arr_cnproduct=$rows;
			}
		}//func
		
		function getpagesAction(){			
			$this->_helper->layout->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$objTr=new Model_Cn();
			$cpage=$objTr->getPageTotal($rp);
			$this->view->cpage=$cpage;
		}//func		
		
		function up2jAction(){
			/**
			 * @joke test
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){		
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$objCal=new Model_Calpromotion();
				@$objCal->up_point($doc_no);
				//$objCn=new Model_Cn();
				//$result=$objCn->up2j($doc_no);	
			}
		}//func
		
		function savecnAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				$doc_no_ref=$filter->filter($this->getRequest()->getPost('doc_no_ref'));
				$cn_fullname=$filter->filter($this->getRequest()->getPost('cn_fullname'));
				$cn_remark=$filter->filter($this->getRequest()->getPost('cn_remark'));
				$cn_address=$filter->filter($this->getRequest()->getPost('cn_address'));
				
				$acc_name=$filter->filter($this->getRequest()->getPost('acc_name'));
				$bank_acc=$filter->filter($this->getRequest()->getPost('bank_acc'));
				$bank_name=$filter->filter($this->getRequest()->getPost('bank_name'));
				$tel1=$filter->filter($this->getRequest()->getPost('tel1'));
				$tel2=$filter->filter($this->getRequest()->getPost('tel2'));
								
				$objCn=new Model_Cn();
				$this->view->result=$objCn->saveCn($doc_no_ref,$member_no,$cashier_id,
																	$status_no,$member_percent,$cn_fullname,
																		$cn_remark,$cn_address,$acc_name,$bank_acc,$bank_name,$tel1,$tel2);				
			}
		}//func
		
		function initcntempAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				//recover trn_diary2.cn_qty
				$objCn=new Model_Cn();
				$objCn->initCNTemp();
				/*
				$objPos=new Model_PosGlobal();
				$objPos->TrancateTable("trn_tdiary2_cn");
				$objPos->TrancateTable("trn_tdiary1_cn");
				*/
			}
		}//func
		
		function subtotalcnAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()) {	
				$objCn=new Model_Cn();
				$json=$objCn->getSumCnTemp();
				$arr_data=array(
						'action'=>'getSumCnTemp',
						'data'=>$json
					);
					$this->view->arr_data=$arr_data;
			}
		}//func
		
		function chkmemberAction(){
			/*
			 **@desc modify : 30062014
			 **@return array of member data
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()){
				$status_no = $filter->filter($this->getRequest()->getParam('status_no'));		
				$doc_no_ref = $filter->filter($this->getRequest()->getParam('doc_no_ref'));		
				$member_no = $filter->filter($this->getRequest()->getParam('member_no'));	
				$cn_member_no_ref = $filter->filter($this->getRequest()->getParam('cn_member_no_ref'));//*WR08052014
				$objCn=new Model_Cn();
				$json=$objCn->getMemberCn($status_no,$doc_no_ref,$member_no,$cn_member_no_ref);//*WR08052014
				$arr_data=array(
						'action'=>'getMemberCn',
						'data'=>$json
					);
				$this->view->arr_data=$arr_data;
			}
		}//func
		
		function delcnAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$items=$filter->filter($this->getRequest()->getPost('items'));
				$objCn=new Model_Cn();
				$result=$objCn->delCn($items);
				($result)?$result=1:$result=0;
				$this->view->result=$result;
			}
		}//func
	}//class
?>