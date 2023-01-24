<?php
	class EcommerceController extends Zend_Controller_Action{
		private $_DOC_DATE;
		private $_CHECK_SESSION;
		private $_CORPORATION_ID;
		private $_COMPANY_ID;
		private $_BRANCH_ID;
		private $_BRANCH_NO;
		private $_GROUP_ID;
		private $_IPADDRESS;	
		public function init(){			
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_IPADDRESS=$_SERVER['REMOTE_ADDR'];	
		}//func
		
		function preDispatch()
		{				
			//$this->_helper->layout()->setLayout('default_layout');
			$this->_helper->layout()->setLayout('ecommerce_layout');
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if(empty($empprofile)){
				$this->_redirect('/error/sessionexpire');
				exit();				
			}	
			$this->_CORPORATION_ID=$empprofile['corporation_id'];
			$this->_COMPANY_ID=$empprofile['company_id'];
		    $this->_BRANCH_ID=$empprofile['branch_id'];
		    $this->_BRANCH_NO=$empprofile['branch_no'];
		    $this->_CHECK_SESSION=$empprofile['employee_id'];
		    $this->_GROUP_ID=$empprofile['group_id'];		    
		   	$this->view->session_employee_id=$empprofile['employee_id'];		
		   	$this->view->group_id=$this->_GROUP_ID;		   	
		   	$objPos=new Model_PosGlobal();
		 	//check lock keyboard status
			$this->view->lock_status=$objPos->getLockStatus();				
			//check doc_date
			$this->_DOC_DATE=$objPos->checkDocDate();	
			//check ip system
			$arr_client=$objPos->getClientProfile();
			if(count($arr_client)==0){
				$this->_redirect('/error/outip');
				exit();
			}			
		}//func
		/**
		 * @name allocatestock
		 * @desc
		 * @create 20042017
		 * @return null
		 */
		function allocatestockAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$user_action=$filter->filter($this->getRequest()->getPost('user_action'));
				$objOrder=new Model_Ecommerce();
				$objOrder->allocateStock($user_action);
			}
		}//func
		
		/**
		 * @name
		 * @desc
		 * @create 17032017
		 * @return json string
		 */
		function getnotificationsAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$order_status=$filter->filter($this->getRequest()->getPost('order_status'));
				$objOrder=new Model_Ecommerce();
				$arr_noty=$objOrder->getNotifications($order_status);
				echo json_encode($arr_noty);
			}
		}//func
		
		/**
		 * @desc
		 * @create 03032017
		 * @return message status
		 */
		function capturephotoAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$rnd=$filter->filter($this->getRequest()->getPost('rnd'));
				$objOrder=new Model_Ecommerce();
				$result=$objOrder->capturePhoto();
				echo $result;
			}
		}//func
		
		/**
		 * @desc
		 * @create 03032017
		 * @return message status
		 */
		function savereceivegoodsAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$qstr_receive=$filter->filter($this->getRequest()->getPost('qstr_receive'));
				$type_receive=$filter->filter($this->getRequest()->getPost('type_receive'));
				$objOrder=new Model_Ecommerce();
				$result=$objOrder->saveReceiveGoods($qstr_receive,$type_receive);
				echo json_encode($result);
			}
		}//func
		
		/**
		 * @desc print invoice
		 * @create 23022017
		 */
		function receivegoodsAction(){			
			$this->view->doc_date=$this->_DOC_DATE;
			$arr_docdate=explode("-",$this->_DOC_DATE);
			$this->view->doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
		}//func
		
		/***
		 * @desc
		 * @create
		 * @return
		 */
		function cmppickingconfirmorderAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$objOrder=new Model_Ecommerce();
				$result=$objOrder->cmpPickingConfirmOrder($doc_no);
				echo json_encode($result);
			}
		}//func
		
		
		/***
		 * @desc
		 * @create 17022017
		 * @return
		 */
		function sendorderstatusAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$order_no=$filter->filter($this->getRequest()->getPost('order_no'));
				$action_status=$filter->filter($this->getRequest()->getPost('order_status'));
				$user_action=$filter->filter($this->getRequest()->getPost('user_action'));
				$stauts_action = file_get_contents("http://transferservices.ssup.co.th/pos/order_action.php?shop_no=".$this->_BRANCH_ID."&order_no=".$order_no."&action=".$action_status."&user=".$user_action);
				//local
				$objOrder=new Model_Ecommerce();
				$objOrder->orderStatus($order_no,$action_status,$user_action);
				return $status_action;
			}
		}//func
		
		/***
		 * @desc
		 * @create 17022017
		 * @return
		 */
		function chkproductpickingAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){					
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));				
				$objOrder=new Model_Ecommerce();
				$result=$objOrder->chkProductPicking($doc_no,$product_id,$quantity);
				echo $result;
			}
		}//func
		
		function subtotalorderpickingAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()) {
				$objDT=new Model_Ecommerce();
				$json=$objDT->getSumOrderPicking();
				$arr_data=array(
						'action'=>'getSumOrderPicking',
						'data'=>$json
				);				
				$this->view->arr_data=$arr_data;
			}
		}//func
		
		function pickingAction(){
			/**
			 * @desc
			 * @create
			 */
			$this->view->doc_date=$this->_DOC_DATE;
			$arr_docdate=explode("-",$this->_DOC_DATE);
			$this->view->doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
		}//func
		
		function printorderpickingAction(){
			/**
			 * @desc
			 * @create 16022017
			 * @return null
			 */
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$objOrder=new Model_Ecommerce();
				//check status befor print
				
				$this->view->arr_h=$objOrder->getHeadOrder($doc_no);
				$this->view->arr_l=$objOrder->getListOrder($doc_no);
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
			}
		}//func
		
		function getneworderpickingAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){	
				$objInv=new Model_Ecommerce();
				$user_action=$filter->filter($this->getRequest()->getPost('user_action'));
				$res_order=$objInv->getNewOrderPicking($user_action);
				echo $res_order;
			}
		}//func
		
		function getorderpickingAction(){
			/**
			 * @06022017
			 */
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			$order_no=$filter->filter($this->getRequest()->getPost('order_no'));
			$page=$filter->filter($this->getRequest()->getPost('page'));
			$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
			$query=$filter->filter($this->getRequest()->getPost('query'));
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
			$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
			$objInv=new Model_Ecommerce();
			$arr_json=$objInv->getOrderPicking($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder);
			$this->view->arr_json=$arr_json;
		}//func
		
		
		function adjuststockecomAction(){
			/**
			 * @desc
			 * @create 29112016
			 * @return
			 */		
			$this->view->doc_date=$this->_DOC_DATE;
			$arr_docdate=explode("-",$this->_DOC_DATE);
			$this->view->doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];			
			if($this->_CHECK_SESSION==''){
				$r = new Zend_Controller_Action_Helper_Redirector;
				$r->gotoUrl('../pos/logout/index')->redirectAndExist();
				exit();
			}				
			//clear cache browser
			$objPos=new Model_PosGlobal();
			$objPos->clearBrowserCache();			
		}//func
		
		function orderecomAction(){		
			//check condition to open bill						
			if(!$this->_DOC_DATE){
				$this->_redirect('/error/billopen');
				exit();
			} 
			$this->view->doc_date=$this->_DOC_DATE;
			$arr_docdate=explode("-",$this->_DOC_DATE);
			$this->view->doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
			//check invoice			
			$objPos=new Model_PosGlobal();
			$res_invoice=$objPos->checkInvoice();			
			if(!$res_invoice){
				$this->_redirect('/error/confirminv');
				exit();
			}
			//check rq
			$objPos=new Model_PosGlobal();
			$res_rq=$objPos->checkRQ();
			if(!$res_rq){
				$this->_redirect('/error/confirmrq');
				exit();
			}
			//echo Zend_Version::VERSION;
			if($this->_CHECK_SESSION==''){
				$r = new Zend_Controller_Action_Helper_Redirector;
    			$r->gotoUrl('../pos/logout/index')->redirectAndExist();
				exit();
			}			
			
			//check open before real time
			$res_timeopenbill=$objPos->timeOpenBill();
			$arr_timeopenbill=explode('#',$res_timeopenbill);
			if($arr_timeopenbill[0]=='N'){
				$this->_redirect('/error/timeopenbill/status/'.$arr_timeopenbill[0].'/compare/'.$arr_timeopenbill[1].'/time/'.$arr_timeopenbill[2]); 
				exit();
			}		
			
			//clear cache browser
			$objPos->clearBrowserCache();
			
			$objCsh=new Model_Cashier();
			$percen_tax=$objCsh->getTax("THA","V");
		}//func		
		
		function chkexistorderAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){	
				$refer_doc_no=$filter->filter($this->getRequest()->getPost('refer_doc_no'));
				$objOrder=new Model_Ecommerce();
				$json=$objOrder->chkExistOrder($refer_doc_no);
				echo $json;
			}
		}//func
		
		function initordtempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objOrder=new Model_Ecommerce();
			$objOrder->initOrdTemp();
		}//func
		
		function getweborderAction(){
			/**
			 * @desc 25072013 for web ecommerce
			 * @return
			 */
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){	
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$objEcom=new Model_Ecommerce();
				$arr_order=$objEcom->getWebOrder();
				$this->view->arr_order=$arr_order;
			}
		}//func		
		
		function setordtempAction(){		
		}//func
		
		function getordtempAction(){
			$this->_helper->layout()->disableLayout();		
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));		
			$order_no=$filter->filter($this->getRequest()->getPost('order_no'));			
			$page=$filter->filter($this->getRequest()->getPost('page'));
			$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
			$query=$filter->filter($this->getRequest()->getPost('query'));	
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
			$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
			$objInv=new Model_Ecommerce();
			$arr_json=$objInv->getOrderTemp($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder);							
			$this->view->arr_json=$arr_json;
		}//func
		
		public function getpagesAction(){			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
			$objOrder=new Model_Ecommerce();
			$cpage=$objOrder->getPageTotal($rp,$doc_no);
			echo $cpage;
		}//func		
		
		public function getsumorderwebAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();			
			$objOrder=new Model_Ecommerce();
			$json=$objOrder->getSumOrderWeb();
			echo $json;
		}//func
		
		
		function setpdtpickingtoconfirmorderAction(){
			/**
			 * @desc  set product product picking to confrim order
			 * @create 17022017
			 * @return null
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$order_no=$filter->filter($this->getRequest()->getPost('order_no'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$objEcom=new Model_Ecommerce();
				$orArray=$objEcom->setPdtPickingToConfirmOrder($order_no,$product_id,$quantity,$status_no);
				echo json_encode($orArray);
			}
		}//func
		
// 		function orderwebAction(){
// 			/**
// 			 * @desc 23072013
// 			 */
// 			$this->_helper->layout->disableLayout();
// 			$this->_helper->viewRenderer->setNoRender(TRUE);
// 			Zend_Loader::loadClass('Zend_Filter_StripTags');
// 			$filter = new Zend_Filter_StripTags();
// 			if ($this->_request->isPost()){	
// 				$order_no=$filter->filter($this->getRequest()->getPost('order_no'));
// 				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
// 				$objEcom=new Model_Ecommerce();
// 				$str_json=$objEcom->setOrderWeb2Temp($order_no,$status_no);
// 				echo $str_json;
// 			}
// 		}//func
		
		function saveordwebAction(){
			/**
			 * @desc confirm order by picking and gen bill
			 * @create : 17092013
			 * @modify : 22022017
			 * @return null
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$user_id=$filter->filter($this->getRequest()->getPost('user_id'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));
				$saleman_id=$filter->filter($this->getRequest()->getPost('saleman_id'));
				$objOrd=new Model_Ecommerce();
				$res_ordweb=$objOrd->saveOrdWeb($doc_no,$user_id,$cashier_id,$saleman_id);
				echo $res_ordweb;
			}
		}//func		
	}//class 
?>
