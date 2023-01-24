<?php 
	class CashierController extends Zend_Controller_Action{
		public $_DOC_DATE;
		public $_CHECK_SESSION;
		public $_COUNTRY_ID;
		public $_CORPORATION_ID;
		public $_COMPANY_ID;
		public $_BRANCH_ID;
		public $_BRANCH_NO;
		public $_GROUP_ID;
		public $_IPADDRESS;	
		public $_COMPUTER_NO;
		public $_LOCK_FINGER_SCAN;
		public function init(){			
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_IPADDRESS=$_SERVER['REMOTE_ADDR'];	
		}//func
		
		function preDispatch()
		{				
			$this->_helper->layout()->setLayout('default_layout');		
			//check session
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if(empty($empprofile)){
				$this->_redirect('/error/sessionexpire');
				exit();				
			}	
			$this->_COUNTRY_ID=$empprofile['country_id'];
			$this->_CORPORATION_ID=$empprofile['corporation_id'];
			$this->_COMPANY_ID=$empprofile['company_id'];
			$this->_BRANCH_ID=$empprofile['branch_id'];
			$this->_BRANCH_NO=$empprofile['branch_no'];
			$this->_CHECK_SESSION=$empprofile['employee_id'];
			$this->_GROUP_ID=$empprofile['group_id'];
			$this->_COMPUTER_NO=$empprofile['computer_no'];//*WR16032017 for support alipay
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
			//*WR04052017
			$lockfinger = $objPos->checkLogFingerScan();
			$this->_LOCK_FINGER_SCAN = $lockfinger;
		}//func		
		
		/**
		 * @desc for support alipay
		 * @create 14032017
		 */
		public function alipayconfirmformAction(){
			$this->_helper->layout->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$reqid = $filter->filter($this->getRequest()->getParam('reqid'));
			$reqdt = $filter->filter($this->getRequest()->getParam('reqdt'));
			$amt = $filter->filter($this->getRequest()->getParam('amt'));
			$this->view->reqid = $reqid;
			$this->view->reqdt = $reqdt;
			$this->view->amt = $amt;
		}
		
		/**
		 * @desc for support alipay
		 * @create 14032017
		 */
		public function confirmalipayAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$pay=new SSUP_Controller_Plugin_AliPayV4();
		
			$reqid=$filter->filter($this->getRequest()->getPost('reqid'));
			$reqdt = $filter->filter($this->getRequest()->getPost('reqdt'));
			$amt = $filter->filter($this->getRequest()->getPost('amt'));
		
			$storeid = $this->_BRANCH_NO;
			$deviceid = $this->_COMPUTER_NO;
			//inquiryRequest($storeid, $deviceid, $origreqid, $origreqdt)
			$resp = $pay -> inquiryRequest($storeid, $deviceid, $reqid, $reqdt);
			if($resp){
				$respcode = $resp['respcode'];
				$errmsg = $resp['errmsg'];
				$transid = $resp['transid'];
				$alipaytransid = $resp['alipaytransid'];
				$transdt = $resp['transdt'];
				$buyerid = $resp['buyerid'];
		
				$cnyamt = $resp['cnyamt'];
				$convrate = $resp['convrate'];
		
				$log = new Model_PosGlobal();
				$log->save_alipay_request("inquiry",$storeid, $deviceid, $reqid,$reqdt,$custcode,$amt,$respcode, $errmsg, $transid, $alipaytransid, $transdt, $buyerid,$cnyamt,$convrate);
				//if($log_resp =="ins_y")
		
				if($resp['respcode']=="0"){
					echo "y||$resp[transid]";
				}else{
					echo "n||$resp[errmsg]";
				}
			}else{
				//do somthing to cancel and re-request
				echo "n||lost connection";
			}
		}//func
		
		public function cancelalipayAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$pay=new SSUP_Controller_Plugin_AliPayV4();
		
			$reqid=$filter->filter($this->getRequest()->getPost('reqid'));
			$reqdt = $filter->filter($this->getRequest()->getPost('reqdt'));
			$amt = $filter->filter($this->getRequest()->getPost('amt'));
		
			$storeid = $this->_BRANCH_NO;
			$deviceid = $this->_COMPUTER_NO;
			//inquiryRequest($storeid, $deviceid, $origreqid, $origreqdt)
			//$resp = $pay -> inquiryRequest($storeid, $deviceid, $reqid, $reqdt);
			$resp = $pay -> cancelRequest($storeid, $deviceid, $reqid, $reqdt, $amt);
			if($resp){
				$respcode = $resp['respcode'];
				$errmsg = $resp['errmsg'];
				$transid = $resp['transid'];
				$alipaytransid = $resp['alipaytransid'];
				$transdt = $resp['transdt'];
				$buyerid = $resp['buyerid'];
		
				$cnyamt = $resp['cnyamt'];
				$convrate = $resp['convrate'];
		
				$log = new Model_PosGlobal();
				$log->save_alipay_request("cancel",$storeid, $deviceid, $reqid,$reqdt,$custcode,$amt,$respcode, $errmsg, $transid, $alipaytransid, $transdt, $buyerid,$cnyamt,$convrate);
				if($resp['respcode']=="0"){
					echo "y";
				}else{
					echo "n";
				}
			}else{
				//do somthing to cancel and re-request
				echo "n";
			}
		}//func
		
		/**
		 * @desc for support alipay
		 * @create 14032017
		 */
		public function doalipayAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$pay=new SSUP_Controller_Plugin_AliPayV4();
			$storeid = $this->_BRANCH_NO;
			$deviceid = $this->_COMPUTER_NO;
			$reqid=$this->_BRANCH_NO.date("YmdHis");
			$reqdt=str_replace("-","",$this->_DOC_DATE).date("His");
			$custcode=$filter->filter($this->getRequest()->getPost('txt_credit_no_value'));
			$amt = $filter->filter($this->getRequest()->getPost('txt_credit_value'));
			//$amt=0.04;//fix for test 16032017
			$resp = $pay -> saleRequest($storeid, $deviceid, $reqid, $reqdt, $custcode, $amt, $curr = 'THB');
			if($resp){
				$respcode = $resp['respcode'];
				$errmsg = $resp['errmsg'];
				$transid = $resp['transid'];
				$alipaytransid = $resp['alipaytransid'];
				$transdt = $resp['transdt'];
				$buyerid = $resp['buyerid'];
				$cnyamt = $resp['cnyamt'];
				$convrate = $resp['convrate'];
				$log = new Model_PosGlobal();
				$log->save_alipay_request("request",$storeid, $deviceid, $reqid,$reqdt,$custcode,$amt,$respcode, $errmsg, $transid, $alipaytransid, $transdt, $buyerid,$cnyamt,$convrate);
		
				if($resp['respcode']=="0"){
					echo "y||$resp[transid]";
				}else{
					if($resp['respcode']=="1"){
						echo "u||$reqid#$reqdt#$amt";
					}else{
						echo "n||$resp[errmsg]";
					}
				}
			}else{
				//do somthing to cancel and re-request
				echo "n||lost connection";
		
			}
		}//func
		
		function setonlinemodeAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$online_status=$filter->filter($this->getRequest()->getPost('online_status'));
				$objCsh=new Model_Cashier();
				$objCsh->setOnlineMode($online_status);
			}
		}//func
		
		function checkbillmanualexistAction(){
			/**
			 * @desc check bill manual no for protect duplicate bill no
			 * @create 20072015
			 * @return Y is exist,N=is available
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$bill_manual_no=$filter->filter($this->getRequest()->getPost('bill_manual_no'));				
				$objCsh=new Model_Cashier();
				$exist_status=$objCsh->checkBillManualExist($bill_manual_no);
				echo $exist_status;
			}
		}//func
		
		function up2clusterAction(){
			/**
			 * @desc 11062013
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$objCsh=new Model_PosGlobal();
				$res_up1=$objCsh->up2rePriv();
				//$res_up2=$objCsh->up2cluster();//05112014 ณ ปัจจุบันเลิกใช้แล้ว
			}
			return false;
		}//func
		
		function contactAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$objAccessory=new Model_Accessory();
			$objAccessory->showContact();			
		}//func
		
		function callmemberofflineAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()){			
				$member_no = $filter->filter($this->getRequest()->getParam('member_no'));	
				$objPosGlobal=new Model_PosGlobal();
				$row_member=$objPosGlobal->getOfflineProfile($member_no);				
				if($row_member!=''){
						if($row_member[0]['exist_status']=='YES'){
							$objUtils=new Model_Utils();
							$json=$objUtils->ArrayToJson('member',$row_member[0],'yes');				
							echo  $json;
						}else{
							echo  '2';
						}
				}else{
					echo  '2';
				}
				
			}
		}//func
		
		function getlockstatusAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objPos=new Model_PosGlobal();
			$lock_status=$objPos->getLockStatus();
			echo $lock_status;
		}//func
		
		function expirepageAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		public function indexAction()
		{			
			
			
			
// 			echo "<pre>";
// 			print_r($_SESSION);
// 			echo "</pre>";
// 			exit();
			
			//check condition to open bill			
			if(!$this->_DOC_DATE){
				$this->_redirect('/error/billopen');
				exit();
			} 
			$this->view->doc_date=$this->_DOC_DATE;
			$arr_docdate=explode("-",$this->_DOC_DATE);
			$this->view->doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];				
			$objPos=new Model_PosGlobal();		
			
			//check count cash
// 			$res_countcash=$objPos->getCountCash();
// 			if($res_countcash<1){
// 				$this->_redirect('/error/uncountcash');
// 				exit();
// 			}
			
			//check invoice			
			$res_invoice=$objPos->checkInvoice();			
			if(!$res_invoice){
				$this->_redirect('/error/confirminv');
				exit();
			}
			//check rq
			//$objPos=new Model_PosGlobal();
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
				//$this->_redirect('/error/timeopenbill',array("status"=>$arr_timeopenbill[0],"compare"=>$arr_timeopenbill[1],"time"=>$arr_timeopenbill[2])); 
				$this->_redirect('/error/timeopenbill/status/'.$arr_timeopenbill[0].'/compare/'.$arr_timeopenbill[1].'/time/'.$arr_timeopenbill[2]); 
				exit();
			}		
			
			//clear cache browser
			$objPos->clearBrowserCache();
			$objCsh=new Model_Cashier();
			$percen_tax=$objCsh->getTax($this->_COUNTRY_ID,"V");
		}//func
		
		
		public function chkfreeshortAction(){
			/**
			 * @desc WR10012013 ทำแก้ปัญหาเฉพาะหน้าใช้ครั้งเดียว
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));				
				$member_tp=$filter->filter($this->getRequest()->getPost('member_tp'));		
				$objCsh=new Model_Cashier();
				if(substr($promo_code,0,9)=='R_STORIES'){
					$res=$objCsh->chkFreeShort($doc_no,$member_no,$promo_code);
				}else if($promo_code=='R_WB'){
					$objR=new Model_Returns();
					$res=$objR->chkFreeShortRWB($doc_no,$member_no,$promo_code,$member_tp);
				}else if($promo_code=='R_OPS'){
					$product_id=$filter->filter($this->getRequest()->getPost('product_id'));	
					$quantity=$filter->filter($this->getRequest()->getPost('quantity'));	
					$objR=new Model_Returns();
					$res=$objR->chkFreeShortOPS($doc_no,$member_no,$promo_code,$member_tp,$product_id,$quantity);
				}else if($promo_code=='R_POWER_C'){
					$product_id=$filter->filter($this->getRequest()->getPost('product_id'));	
					$quantity=$filter->filter($this->getRequest()->getPost('quantity'));	
					$objR=new Model_Returns();
					$res=$objR->chkFreeShortPowerC($doc_no,$member_no,$promo_code,$member_tp,$product_id,$quantity);
				}else{
					$res=$objCsh->chkFreeShortNew($doc_no,$member_no,$promo_code,$member_tp);
				}
				echo $res;
			}
		}//func
		
		public function addcoproductAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$objCsh=new Model_Cashier();
				$res=$objCsh->addCoProduct($promo_code,$product_id,$member_no);
			}
		}//func
		
		public function chkcopromoAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$objCsh=new Model_Cashier();
				$res=$objCsh->chkCopromo($promo_code,$product_id);
				echo $res;
			}
		}//func
		
		public function chkmorepointmorevalAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objCsh=new Model_Cashier();
				$res=$objCsh->chkMorePointMoreVal($promo_code);
				echo $res;
			}
		}//func
		
		public function calledcAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$credit_net_amt=$filter->filter($this->getRequest()->getPost('credit_net_amt'));
				$actions=$filter->filter($this->getRequest()->getPost('actions'));
				$objPos=new Model_PosGlobal();
				if($actions=='calledc'){
					$call_status=$objPos->callEDC($credit_net_amt);
				}else if($actions=='settlement'){
					$call_status=$objPos->autoSettlement();
				}
				echo $call_status;
			}
		}//func
		
		function decode($string="",$key="OPCAMBODIA") {		
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			$string = trim($string);
			if(strlen($string)<=13){
				return $string;
			}else{
				$key = sha1($key, FALSE);
				$strLen = strlen($string);
				$keyLen = strlen($key);
				for ($i = 0; $i < $strLen; $i+=2) {
					$ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
					if ($j == $keyLen) { $j = 0; }
					$ordKey = ord(substr($key,$j,1));
					$j++;
					$hash .= chr($ordStr - $ordKey);
				}
				return $hash;
			}
		}//func
		
		function decodecallerAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$objPos=new Model_PosGlobal();
			$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
			$timecheck = date("YmdHi", strtotime("-8 minutes"));
			$member_no=$this->decode($member_no);
			$mem = explode("||",$member_no);			
			//print_r($mem);
			if(strlen($mem[0])==6 || strlen($mem[0])==9 || strlen($mem[0])==10){
				$on_trans=$objPos->memberOnTrans($mem[0]);
				echo $on_trans."#".$mem[0];				 
			}elseif($timecheck>$mem[3]){
				echo "T"."#".$mem[1]."#".$timecheck."#".$mem[3]."#".$member_no;				
			}else{
				$on_trans=$objPos->memberOnTrans($mem[1]);
				echo $on_trans."#".$mem[1];
			}		      
		}//func
		
		public function memberontransAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$member_no=$this->decode($member_no);
				$mem = explode("||",$member_no);
				$objPos=new Model_PosGlobal();
				$timecheck = date("YmdHi", strtotime("-8 minutes"));
				if(strlen($mem[0])==9 || strlen($mem[0])==10 || strlen($mem[0])==6){
					$on_trans=$objPos->memberOnTrans($mem[0]);
					echo $on_trans."#".$mem[0];
				}elseif($timecheck > $mem[3]){
					//echo $timecheck .">". $mem[3];
					echo "T"."#".$mem[1]."#".$timecheck."#".$mem[3]."#".$member_no;
				}else{
					$on_trans=$objPos->memberOnTrans($mem[1]);
					echo $on_trans."#".$mem[1]."#".$timecheck."#".$mem[3]."#".$member_no;
				}
				 
			}
			//exit();
		}//func
		
// 		public function memberontransAction(){
// 			$this->_helper->layout->disableLayout();
// 			$this->_helper->viewRenderer->setNoRender(TRUE);
// 			Zend_Loader::loadClass('Zend_Filter_StripTags');
// 			$filter = new Zend_Filter_StripTags();
// 			if ($this->_request->isPost()){			
// 				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
// 				$objPos=new Model_PosGlobal();
// 				$on_trans=$objPos->memberOnTrans($member_no);
// 				echo $on_trans;
// 			}
			
// 		}//func
		
		function bill2dayAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$objAcc=new Model_Accessory();
				$this->view->arr_docno=$objAcc->getBill2Day();
			}
		}//func
		
		/***
		 * @desc 11032013
		 * @desc ตอนนี้ยังไม่ถูกเรียกใช้ทำไว้รองรับการดูประวัติการซื้อของสมาชิก
		 */
		function billprofilesAction(){			
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$objAcc=new Model_Accessory();
				$this->view->arr_docno=$objAcc->getBillProfiles($member_no);
			}
		}//func
		
		/**
		 * @ 13032013
		 * Enter description here ...
		 */
		function billshortproductAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				if($promo_code=='R_WB'){
					$objRe=new Model_Returns();
					$this->view->arr_docno=$objRe->getBillShortRWB($member_no,$promo_code);
				}else if($promo_code=='R_OPS'){
					$objRe=new Model_Returns();
					$this->view->arr_docno=$objRe->getBillShortOPS($member_no,$promo_code);
				}else if($promo_code=='R_POWER_C'){
					$objRe=new Model_Returns();
					$this->view->arr_docno=$objRe->getBillShortPowerC($member_no,$promo_code);
				}else{
					$objAcc=new Model_Accessory();
					$this->view->arr_docno=$objAcc->getBillShortProduct($member_no,$promo_code);
				}
			}
		}//func
		
		public function repairmysqldbAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){		
				//clear file report temp
				@chmod(REPORT_PATH,0777);
				echo shell_exec("rm -rf ".REPORT_PATH."/*");
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$objPos->optimizeMysqlTable();				
				//clear browser cache
				$objPos=new Model_PosGlobal();
				$objPos->clearBrowserCache();
			}
		}//func
		
		public function productfreeAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objPro=new Model_Cashier();
				$this->view->arr_product=$objPro->getProductFree($promo_code);
			}
		}//func
		
		public function promoitemsAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isGet()){
				$objPos=new Model_PosGlobal();
				$this->view->arr_promo=$objPos->getPromoItems();
			}
		}//func
		
		public function promoitemsnextweekAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isGet()){
				$objPos=new Model_PosGlobal();
				$this->view->arr_promo=$objPos->getPromoItemsNextWeek();
			}
		}//func
		
		public function promoitemsdetailAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objPos=new Model_PosGlobal();
				$this->view->arr_promo=$objPos->getPromoItemsDetails($promo_code);
			}
		}//func
		
		public function promoitemsdetailnextweekAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objPos=new Model_PosGlobal();
				$this->view->arr_promo=$objPos->getPromoItemsDetailsNextWeek($promo_code);
			}
		}//func
		
		public function otherproAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$objPro=new Model_PosGlobal();
				$this->view->arr_promo=$objPro->getListOtherPromotion();
			}
		}//func
		
		public function setsmstempAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objCashier=new Model_Cashier();
				$result=$objCashier->setSmsTemp($promo_code);
				echo $result;
			}
		}//func
		
		public function callsmsproAction(){
			/**
			 * @desc 23092013 append id card
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('sms_promo_code'));
				$mobile_no=$filter->filter($this->getRequest()->getPost('sms_mobile'));
				$redeem_code=$filter->filter($this->getRequest()->getPost('redeem_code'));
				$idcard=$filter->filter($this->getRequest()->getPost('idcard'));
				
				$ws = "http://10.100.53.2/wservice/webservices/services/sms_promotion.php?";
				$type = "json";
				$shop=$this->_BRANCH_ID;				
				$act = "request";
				$src = $ws."callback=jsonpCallback&member_no=".$member_no."&brand=op&dtype=".$type."&shop=".$shop."&promo_code=".$promo_code."&mobile_no=".$mobile_no."&redeem_code=".$redeem_code."&idcard=".$idcard."&act=".$act."&_=1334128422190";
				
				$row_member=array();	
				$o=@file_get_contents($src,0);
				$o = str_replace("jsonpCallback(","",$o);
				$o = str_replace(")","",$o);				
				$o = json_decode($o ,true);
				echo json_encode($o);
			}
		}//func
		
		public function locksmsproAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$id_smspromo=$filter->filter($this->getRequest()->getPost('id_smspromo'));
				$promo_code=$filter->filter($this->getRequest()->getPost('sms_promo_code'));
				$mobile_no=$filter->filter($this->getRequest()->getPost('sms_mobile'));
				$redeem_code=$filter->filter($this->getRequest()->getPost('redeem_code'));
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$ws = "http://10.100.53.2/wservice/webservices/services/sms_promotion.php?";
				$type = "json";
				$shop=$this->_BRANCH_ID;
				$act = "lock";
				$src = $ws."callback=jsonpCallback&id=".$id_smspromo."&brand=op&dtype=".$type."&shop=".$shop.
								"&promo_code=".$promo_code."&mobile_no=".$mobile_no."&redeem_code=".$redeem_code.
									"&act=".$act."&doc_no=".$doc_no."&_=1334128422190";
				$row_member=array();	
				$o=@file_get_contents($src,0);				
				$o = str_replace("jsonpCallback(","",$o);
				$o = str_replace(")","",$o);
				$o = json_decode($o ,true);
				echo json_encode($o);
			}
		}//func
		
		public function formsmspromobileAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){				
				$str_field=$filter->filter($this->getRequest()->getPost('inputfield'));
				$this->view->str_field=$str_field;
			}
		}//func
		
		public function smsproAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$objPro=new Model_PosGlobal();
				$this->view->arr_promo=$objPro->getListSmsPromotion();
			}
		}//func
		
		public function listcnAction(){
			/**
			 * @desc
			 * @return
			 */
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){							
				$objCsh=new Model_Cashier();
				$arr_cn=$objCsh->listCN();		
				$this->view->arr_cn=$arr_cn;		
			}
		}//func
		
		public function qestpdtofbillAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$objCsh=new Model_Cashier();
				$result=$objCsh->getPdtOfBill($product_id);		
				echo $result;		
			}
		}//func
		
		public function getaoAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$objCsh=new Model_Cashier();
				$objCsh->getAO($status_no);				
			}
		}//func
		
		public function prevpromoAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objCsh=new Model_Cashier();
				$prev_promo_code=$objCsh->getPrevPromo($promo_code);
				echo $prev_promo_code;
			}
		}//func
		
		public function markmemberuseAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$result="";
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$customer_id=$filter->filter($this->getRequest()->getPost('customer_id'));
				$promo_year=$filter->filter($this->getRequest()->getPost('promo_year'));				
				$objPos=new Model_PosGlobal();
				$result=$objPos->markMemberUsePriv($member_no,$customer_id,$promo_code,$promo_year);
			}
			echo $result;
		}//func	
		
		public function unmarkmemberuseAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$customer_id=$filter->filter($this->getRequest()->getPost('customer_id'));
				$promo_year=$filter->filter($this->getRequest()->getPost('promo_year'));				
				$objPos=new Model_PosGlobal();
				$result=$objPos->unMarkMemberUsePriv($member_no,$customer_id,$promo_code,$promo_year);				
			}
		}//func	
		
		public function initmarkpointAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$objPos=new Model_PosGlobal();
				$result=$objPos->initMarkPoint($doc_no);
				echo $result;
			}
		}//func
		
		public function add2pdiaryAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				//*WR02072014 up to service_pos_op			
				$objCal=new Model_Calpromotion();
				$objCal->up_point($doc_no);
				echo "Y";
				//*WR25022015 up2 mobile app
				/*
				$objAcc=new Model_Accessory();
				$objAcc->reUpMobileApp();
				*/
			}
		}//func
		
		public function chkamtvipAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$limited=$filter->filter($this->getRequest()->getPost('limited'));
				$limited_type=$filter->filter($this->getRequest()->getPost('limited_type'));
				$sum_amt=$filter->filter($this->getRequest()->getPost('sum_amt'));
				$vip_percent_discount=$filter->filter($this->getRequest()->getPost('vip_percent_discount'));				
				$objCsh=new Model_Cashier();
				$result=$objCsh->chkAmtVip($product_id,$quantity,$limited,$limited_type,$sum_amt,$vip_percent_discount);
				echo $result;
			}
		}//func
		
		public function countqtytempAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objRwd=new Model_PosGlobal();
				$result=$objRwd->countQtyTmp($promo_code);
				echo $result;
			}
		}//func
		
		public function clstblinitvalAction(){
			/**
			 * @desc 22032013 clear trn_tdiray2_sl_val อยู่ระหว่างการทดสอบ
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objPosGlobal=new Model_PosGlobal();
			$objPosGlobal->TrancateTable("trn_tdiary2_sl_val");
		}//func
		
		public function initbltempAction(){
			$this->_helper->layout()->disableLayout();
			$objCsh=new Model_Cashier();
			$result=$objCsh->cancelBill();			
			//stock balance
			$objPos=new SSUP_Controller_Plugin_PosGlobal();
			$objPos->stockBalance($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_DOC_DATE);	
		}//func
		
		public function countdiarytempAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();	
			if ($this->_request->isPost()) {	
				$action = $filter->filter($this->getRequest()->getPost('actions'));
				$objPos=new Model_PosGlobal();	
				$this->view->crow=$objPos->countDiaryTemp();
			}	
			
		}//func
		
		public function docstatusAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();	
			if ($this->_request->isGet()) {	
				$action = $filter->filter($this->getRequest()->getParam('actions'));				
				if($action=='brows_docstatus'){
						$objPos=new Model_PosGlobal();
						$rows=$objPos->browsDocStatus("SL","all");
						if(count($rows)>0){	
							$arr_data=array(
								'action'=>'brows_docstatus',
								'data'=>$rows
							);
							$this->view->arr_data=$arr_data;
						}else{
							$this->view->arr_data=array();
						}
				}
				
				if($action=='brows_rwddocstatus'){
						$objPos=new Model_PosGlobal();
						//$rows=$objPos->browsDocStatus('','04,18,51');
						$rows=$objPos->browsDocStatus('','18');
						if(count($rows)>0){	
							$arr_data=array(
								'action'=>'brows_rwddocstatus',
								'data'=>$rows
							);
							$this->view->arr_data=$arr_data;
						}else{
							$this->view->arr_data=array();
						}
				}				
			}			
		}//func
		
		public function formconfirmclosedayAction(){
			$this->_helper->layout()->disableLayout();
			$objPos=new Model_PosGlobal();
			$this->view->doc_date=$objPos->getDocDate();			
		}//func
		
		public function confirmclosedayAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			$result='';	
			if ($this->_request->isPost()) {	
				$actions = $filter->filter($this->getRequest()->getPost('action'));
				if($actions=='getdocdate'){
					$objPos=new Model_PosGlobal();
					$result=$objPos->getDocDate();
				}
			}
			
			if ($this->_request->isGet()) {	
				$actions = $filter->filter($this->getRequest()->getParam('action'));
				if($actions=='confirmcloseday'){
					$objCsh=new Model_Cashier();
					$result=$objCsh->closeDay();
					($result)?$result='1':$result='0';		
				}
			}
			
			$arr_data=array(
				'action'=>$actions,
				'data'=>$result
			);
			$this->view->arr_data=$arr_data;			
		}//func
		
		public function closedayAction(){	
			/**
			 * @desc modify : 10072014 
			 */			
			$objCN=new Model_Cn();
			$objCN->reCheckCnByDayEnd();		
		}//func
		
		public function checkstockAction(){
			$this->_helper->layout()->disableLayout();
			//set stock balance
			//$objPos=new SSUP_Controller_Plugin_PosGlobal();
			//$status_proc=$objPos->stockBalance($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_DOC_DATE);
		}//func
		
		public function chkforclosedayAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if($this->_request->isPost()){	
				$doc_date=$filter->filter($this->getRequest()->getPost('doc_date'));	
				$objCsh=new Model_Cashier();
				$result=$objCsh->checkCloseDay($doc_date);
				$this->view->result=$result;
				//*WR08062017 for khm transfer daily sales
// 				$objAcc=new Model_Accessory();
// 				$result=$objAcc->transferDailySales();
			}
		}//func
		
		public function checkstocklistAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {	
				$actions = $filter->filter($this->getRequest()->getPost('actions'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));		
				if($actions=='checkstock_list'){
					$objStock=new Model_Cashier();
					//$arr_stock=$objStock->browsProduct($product_id);
					$arr_stock=$objStock->getOnhandCurrent($product_id);					
					$fix_stock=$objStock->getFixStock($product_id);				
					$arr_stock_sl=$objStock->checkStock($arr_stock,"VT,SL,DN");					
					$arr_stock_ti=$objStock->checkStock($arr_stock,"TI");
					$arr_stock_to=$objStock->checkStock($arr_stock,"TO");
					$arr_stock_cn=$objStock->checkStock($arr_stock,"CN");
					$arr_stock_ai=$objStock->checkStock($arr_stock,"AI");
					$arr_stock_ao=$objStock->checkStock($arr_stock,"AO");					
					$this->view->arr_stock=$arr_stock;
					$this->view->arr_stock_sl=$arr_stock_sl;
					$this->view->arr_stock_ti=$arr_stock_ti;
					$this->view->arr_stock_to=$arr_stock_to;
					$this->view->arr_stock_cn=$arr_stock_cn;					
					$this->view->arr_stock_ai=$arr_stock_ai;
					$this->view->arr_stock_ao=$arr_stock_ao;
					//$this->view->fix_stock=intval($fix_stock);
					$this->view->fix_stock=$fix_stock;
				}
			}
		}//func
		
		public function autoproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$term = $filter->filter($this->getRequest()->getParam("term"));
			$objAutoProduct=new Model_Cashier();
			$result=$objAutoProduct->getProductAutoComplete($term);
			$this->view->arr_product=$result;
		}//func
		
		public function vattotalAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$member_id = $filter->filter($this->getRequest()->getParam("member_id"));
			$objCsh=new Model_Cashier();
			$arr_data=$objCsh->getProfileMemVT($member_id);
			$this->view->arr_data=$arr_data;
			//*WR23052017
			$objAcc=new Model_Accessory();
			$this->view->arr_countrylist=$objAcc->getCountryList();
		}//func
		
		public function swapcashierAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		public function salemanAction(){
			$this->_helper->layout()->disableLayout();
			$this->view->lockfinger = $this->_LOCK_FINGER_SCAN;
		}//func
		
		public function promodetailAction(){
			$this->_helper->layout()->disableLayout();
			$objPromoDetail=new Model_Cashier();
			$arr_data=$objPromoDetail->getPromoDetail();
			$this->view->arr_data=$arr_data;
			$this->view->arr_sum=$objPromoDetail->getSumPromoDetail();
		}//func
		
		public function paycaseAction(){
			/*
			 * @desc :หน้าจอการชำระเงิน
			 * 		  ปัญหาการเลือกตารางระหว่างเล่นโปรกับไม่เล่นโปรยังมีการ fixed เลขที่สถานะเอกสาร
			 * @
			 */
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions = $filter->filter($this->getRequest()->getParam("actions"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$cn_amount = $filter->filter($this->getRequest()->getParam("cn_amount"));
			$coupon_code=$filter->filter($this->getRequest()->getParam("coupon_code"));//*WR23062014
			if($actions=='winpayment'){
				$objCsh=new Model_Cashier();
				$tbl_target=$objCsh->selTblTemp();
				//cal vat
				$objVT=new Model_PosGlobal();				
				$netvt=$objVT->getNetVT($tbl_target);//for support promotion finish
				if($cn_amount!=''){
					$cn_amount=floatVal($cn_amount);
					if($cn_amount<$netvt){
						$netvt=$netvt-$cn_amount;
					}else{
						$netvt=0.00;
					}
				}else{
					$cn_amount=0.00;
				}								
				$this->view->cn_amount=number_format($cn_amount,'2','.',',');
				//$netvt=number_format($netvt,'2','.','');//*WR13112012 122 to 122.00				
				$netvt=$objVT->getSatang($netvt,'normal');
				$arr_data=array(
					'action'=>'winpayment',
					'data'=>null
				);				
				$this->view->arr_data=$arr_data;
				$this->view->netvt=number_format($netvt,'2','.',',');
				//*WR31052017
				$netvt_by_rate=$objVT->reteChange($netvt);
				$this->view->netvt_by_rate=number_format($netvt_by_rate,'2','.',',');
				$this->view->default_curr_rate=$objVT->getRateDefault();
				
				//*WR21122017 for support 2018 chage
				$this->view->default_curr_rate_change=$objVT->getRateDefaultChange();
				
				
				//*WR23062014
				if($coupon_code!=''){
					$objMember=new Model_Member();
					$coupon_discount=$objMember->getCouponDiscount($netvt,$coupon_code);
					$this->view->txt_voucher_cash=$coupon_discount;
					$netvt=$netvt-$coupon_discount;
					$this->view->netvt=number_format($netvt,'2','.',',');
				}else{
					$this->view->txt_voucher_cash=0;
				}
				
			}//	
			
			if($actions=='rwdpayment'){
				//cal vat
				$objVT=new Model_PosGlobal();
				//old trn_tdiary2_rwd
				$tbl_target="trn_tdiary2_sl";
				$netvt=$objVT->getNetVT($tbl_target);//for support promotion finish		
						
//				$stangpos=strrpos($netvt,'.')+1;
//				$stang=substr($netvt,$stangpos);
//				if(($stang >= 1) && ($stang <= 24)) $stang=00;
//				if(($stang >= 26) && ($stang <= 49)) $stang=25;
//				if(($stang >= 51) && ($stang <= 74)) $stang=50;
//				if(($stang >= 76) && ($stang <= 99)) $stang=75;
//				$netvt=substr($netvt,0,$stangpos).$stang;
				$netvt=$objVT->getSatang($netvt,'normal');
				$arr_data=array(
					'action'=>'rwdpayment',
					'data'=>null
				);
				$this->view->arr_data=$arr_data;
				$this->view->netvt=number_format($netvt,'2','.',',');
				
				//*WR30082012
				if($cn_amount!=''){
					$cn_amount=floatVal($cn_amount);
					if($cn_amount<$netvt){
						$netvt=$netvt-$cn_amount;
					}else{
						$netvt=0.00;
					}
				}else{
					$cn_amount=0.00;
				}				
				$this->view->cn_amount=number_format($cn_amount,'2','.',',');				
			}//	
			
			if($actions=='wincredit'){
				$objCreditCard=new Model_Cashier();
				$arr_paid=$objCreditCard->getPaid();
				$this->view->arr_paid=$arr_paid;		
				$arr_data=array(
					'action'=>'wincredit',
					'data'=>null
				);
				$this->view->arr_data=$arr_data;
			}
			//*WR13032017
			if($actions=='alipay'){
				$objCreditCard=new Model_Cashier();
				$arr_paid=$objCreditCard->getPaid();
				$this->view->arr_paid=$arr_paid;
				$arr_data=array(
						'action'=>'alipay',
						'data'=>null
				);
				$this->view->arr_data=$arr_data;
			}
		}//func
		
		public function getempAction(){
			$this->_helper->layout->disableLayout();			
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {		
				$employee_id=$filter->filter($this->getRequest()->getPost('employee_id'));		
				//*WR29042016
				$password=$filter->filter($this->getRequest()->getPost('password'));
				$actions = $filter->filter($this->getRequest()->getPost('actions'));
				if($actions=='userlogin'){	
					$objPos=new Model_PosGlobal();
					$result=$objPos->getEmployee($employee_id);	
					$arr_data=array(
						'action'=>'userlogin',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}
				
				if($actions=='saleman'){	
					$objPos=new Model_PosGlobal();
					$result=$objPos->getSaleman($employee_id);		
					if($result!=""){
						$result=$result[0]['employee_id']."#".$result[0]['name']."#".$result[0]['surname']."#".$result[0]['check_status'];
					}
					$arr_data=array(
						'action'=>'saleman',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}
				
				if($actions=='audit'){	
					$objPos=new Model_PosGlobal();
					$result=$objPos->getAudit($employee_id);	
					if($result!=""){
						$result=$result[0]['employee_id']."#".$result[0]['name']."#".$result[0]['surname']."#".$result[0]['check_status'];
					}
					$arr_data=array(
						'action'=>'audit',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}//if
				
				//*WR29042016
				if($actions=='ros'){
					$objPos=new Model_PosGlobal();
					$arr_result=$objPos->getRos($employee_id,$password);
					
					if(!empty($arr_result)){
						$result=$arr_result[0]['employee_id']."#".$arr_result[0]['name']."#".$arr_result[0]['surname']."#".$arr_result[0]['check_status'];
					}
					//echo $result;
					$arr_data=array(
							'action'=>'ros',
							'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}
				
				//*WR29042016
				if($actions=='ros-saleman'){
					$objPos=new Model_PosGlobal();
					$arr_result=$objPos->getRosSaleman($employee_id,$password);
					//print_r($arr_result);
					if(!empty($arr_result)){
						$result=$arr_result[0]['employee_id']."#".$arr_result[0]['name']."#".$arr_result[0]['surname']."#".$arr_result[0]['check_status'];
					}
					//echo $result;
					$arr_data=array(
							'action'=>'ros-saleman',
							'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}
				
			}
			
			if ($this->_request->isGET()) {	
				$action = $filter->filter($this->getRequest()->getParam("actions"));
				$employee_id = $filter->filter($this->getRequest()->getParam("employee_id"));		
				if($action=='swapcashier'){	
					$objPos=new Model_PosGlobal();
					$result=$objPos->swapCashier($employee_id);	
					if($result!=''){
						$objUtils=new Model_Utils();
						$json=$objUtils->ArrayToJson('emp',$result[0],'yes');			
					}else{
						$json="";
					}
					$arr_data=array(
						'action'=>'swapcashier',
						'data'=>$json
					);
					$this->view->arr_data=$arr_data;
				}
			}			
			
		}//func
		
		public function productAction(){
			$this->_helper->layout->disableLayout();			
			$filter=new Zend_Filter_StripTags();
			$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
			$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
			$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));	
			$objMb=new Model_Member();
			//$res_chkpdt_copro=$objMb->chkProductCoPromotion($promo_code,$product_id);
			$res_chkpdt_copro=$objMb->chkProductCoPromotion($promo_code,$product_id,$quantity);
			if($res_chkpdt_copro!==true){
				$arr_data=array(
					'action'=>'checkproductexist',
					'data'=>$res_chkpdt_copro
				);
				$this->view->arr_data=$arr_data;
				return  false;
			}
			
			$quantity=$filter->filter($this->getRequest()->getPost('quantity'));		
			$status_no=$filter->filter($this->getRequest()->getPost('status_no'));				
			$actions = $filter->filter($this->getRequest()->getPost('action'));
			if($actions=='checkproductexist'){	
				$objPos=new Model_PosGlobal();
				$result=$objPos->getProduct($product_id,$quantity,$status_no);	
				$arr_data=array(
					'action'=>'checkproductexist',
					'data'=>$result
				);
				$this->view->arr_data=$arr_data;
			}
			
		}//func
		
		public function ajaxAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isPost()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$action = $filter->filter($this->getRequest()->getPost('actions'));	
				if($action=="cancelBill"){
					$objCsh=new Model_Cashier();
					$result=$objCsh->cancelBill();		
					$arr_data=array(
						'action'=>'cancelBill',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;			
				}
				if($action=="cancelPro"){
					$objCsh=new Model_Cashier();
					$result=$objCsh->cancelPromotion();		
					$arr_data=array(
						'action'=>'cancelPro',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;			
				}
				if($action=='getemployee'){			
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));				
					$objEmp = new Model_Cashier();					
					$rows=$objEmp->getEmployee($employee_id);
					$arr_data=array(
						'action'=>'getemployee',
						'data'=>$rows
					);
					$this->view->arr_data=$arr_data;
				}//
				
				if($action=='set_csh_val_tmp'){
					$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));
					$promo_id= $filter->filter($this->getRequest()->getPost('promo_id'));
					$promo_st= $filter->filter($this->getRequest()->getPost('promo_st'));				
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
					$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
					$quantity= $filter->filter($this->getRequest()->getPost('quantity'));
					$status_no= $filter->filter($this->getRequest()->getPost('status_no'));
					$product_status= $filter->filter($this->getRequest()->getPost('product_status'));
					$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
					$card_status= $filter->filter($this->getRequest()->getPost('card_status'));
					$discount_percent= $filter->filter($this->getRequest()->getPost('discount_percent'));
					$member_percent1= $filter->filter($this->getRequest()->getPost('member_percent1'));
					$member_percent2= $filter->filter($this->getRequest()->getPost('member_percent2'));
					$co_promo_percent= $filter->filter($this->getRequest()->getPost('co_promo_percent'));
					$coupon_percent= $filter->filter($this->getRequest()->getPost('coupon_percent'));
					$get_point= $filter->filter($this->getRequest()->getPost('get_point'));								
					$objCashier = new Model_Cashier();		
					$result=$objCashier->setCshValTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
													$status_no,$product_status,$application_id,$card_status,$get_point,
													$promo_id,$discount_percent,$member_percent1,$member_percent2,
													$co_promo_percent,$coupon_percent,$promo_st);
					$arr_data=array(
						'action'=>'set_csh_val_tmp',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}//
				
				if($action=='set_gap_val_tmp'){
					$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));
					$promo_tp= $filter->filter($this->getRequest()->getPost('promo_tp'));
					$promo_id= $filter->filter($this->getRequest()->getPost('promo_id'));
					$promo_st= $filter->filter($this->getRequest()->getPost('promo_st'));				
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
					$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
					$quantity= $filter->filter($this->getRequest()->getPost('quantity'));
					$status_no= $filter->filter($this->getRequest()->getPost('status_no'));
					$product_status= $filter->filter($this->getRequest()->getPost('product_status'));
					$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
					$card_status= $filter->filter($this->getRequest()->getPost('card_status'));
					$discount_member= $filter->filter($this->getRequest()->getPost('discount_member'));
					$discount_percent= $filter->filter($this->getRequest()->getPost('discount_percent'));
					$member_percent1= $filter->filter($this->getRequest()->getPost('member_percent1'));
					$member_percent2= $filter->filter($this->getRequest()->getPost('member_percent2'));
					$co_promo_percent= $filter->filter($this->getRequest()->getPost('co_promo_percent'));
					$coupon_percent= $filter->filter($this->getRequest()->getPost('coupon_percent'));
					$get_point= $filter->filter($this->getRequest()->getPost('get_point'));		
					$net_amt= $filter->filter($this->getRequest()->getPost('net_amt'));		
					$discount= $filter->filter($this->getRequest()->getPost('discount'));		
					$check_repeat= $filter->filter($this->getRequest()->getPost('check_repeat'));
										
					$objCashier = new Model_Cashier();		
					$result=$objCashier->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
													$status_no,$product_status,$application_id,$card_status,$get_point,
													$promo_id,$discount_percent,$member_percent1,$member_percent2,
													$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);
					$arr_data=array(
						'action'=>'set_gap_val_tmp',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}//
				
				//*WR16012015 for support LINE OPPL300
				if($action=='line_set_csh_tmp'){	
					$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));
					$promo_id= $filter->filter($this->getRequest()->getPost('promo_id'));
					$promo_tp= $filter->filter($this->getRequest()->getPost('promo_tp'));
					$promo_st= $filter->filter($this->getRequest()->getPost('promo_st'));				
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
					$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
					$quantity= $filter->filter($this->getRequest()->getPost('quantity'));				
					$status_no= $filter->filter($this->getRequest()->getPost('status_no'));
					$product_status= $filter->filter($this->getRequest()->getPost('product_status'));
					$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
					$card_status= $filter->filter($this->getRequest()->getPost('card_status'));
					$discount_percent= $filter->filter($this->getRequest()->getPost('discount_percent'));
					$member_percent1= $filter->filter($this->getRequest()->getPost('member_percent1'));
					$member_percent2= $filter->filter($this->getRequest()->getPost('member_percent2'));
					$co_promo_percent= $filter->filter($this->getRequest()->getPost('co_promo_percent'));
					$coupon_percent= $filter->filter($this->getRequest()->getPost('coupon_percent'));
					$get_point= $filter->filter($this->getRequest()->getPost('get_point'));		
					$promo_amt= $filter->filter($this->getRequest()->getPost('promo_amt'));	
					$promo_amt_type= $filter->filter($this->getRequest()->getPost('promo_amt_type'));	
					$check_repeat= $filter->filter($this->getRequest()->getPost('check_repeat'));
					$web_promo= $filter->filter($this->getRequest()->getPost('web_promo'));
					$point1='';
					$point2='';
					$discount= $filter->filter($this->getRequest()->getPost('discount'));
					$coupon_discount='';	
					$objCashier = new Model_Cashier();
					$str_status=$objCashier->chkQLimit($application_id,$quantity);
					$arr_status=explode('#',$str_status);
					if($arr_status[0]=='N'){
						$result=$objCashier->setCshTemp(
													$doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
													$status_no,$product_status,$application_id,$card_status,$get_point,
													$promo_id,$discount_percent,$member_percent1,$member_percent2,
													$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo,
													$point1,$point2,$discount,$coupon_discount);		
						$result=$result."#".$str_status;							
					}else{
						$result=$str_status;
					}
										
					$arr_data=array(
						'action'=>'line_set_csh_tmp',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}//			
				
				if($action=='set_csh_tmp'){	
					$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));
					$promo_id= $filter->filter($this->getRequest()->getPost('promo_id'));
					$promo_tp= $filter->filter($this->getRequest()->getPost('promo_tp'));
					$promo_st= $filter->filter($this->getRequest()->getPost('promo_st'));				
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
					$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
					$quantity= $filter->filter($this->getRequest()->getPost('quantity'));				
					$status_no= $filter->filter($this->getRequest()->getPost('status_no'));
					$product_status= $filter->filter($this->getRequest()->getPost('product_status'));
					$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
					$card_status= $filter->filter($this->getRequest()->getPost('card_status'));
					$discount_percent= $filter->filter($this->getRequest()->getPost('discount_percent'));
					$member_percent1= $filter->filter($this->getRequest()->getPost('member_percent1'));
					$member_percent2= $filter->filter($this->getRequest()->getPost('member_percent2'));
					$co_promo_percent= $filter->filter($this->getRequest()->getPost('co_promo_percent'));
					$coupon_percent= $filter->filter($this->getRequest()->getPost('coupon_percent'));
					$get_point= $filter->filter($this->getRequest()->getPost('get_point'));		
					$promo_amt= $filter->filter($this->getRequest()->getPost('promo_amt'));	
					$promo_amt_type= $filter->filter($this->getRequest()->getPost('promo_amt_type'));	
					$check_repeat= $filter->filter($this->getRequest()->getPost('check_repeat'));
					$web_promo= $filter->filter($this->getRequest()->getPost('web_promo'));		
					
					$objCashier = new Model_Cashier();		
					$result=$objCashier->setCshTemp(
													$doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
													$status_no,$product_status,$application_id,$card_status,$get_point,
													$promo_id,$discount_percent,$member_percent1,$member_percent2,
													$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo);
					$arr_data=array(
						'action'=>'set_csh_tmp',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}//
				
				if($action=='init_grid'){
					$objInit=new Model_PosGlobal() ;
					$objInit->TrancateTable('pdiary2');
				}//
				
				if($action=='check_promotion'){
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));	
					$objChkPro=new Model_Cashier();
					$result=$objChkPro->checkPromotionFirst($product_id);
					$arr_data=array(
						'action'=>'check_promotion',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}//
				
				if($action=='get_promotion'){
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));	
					$objPromo=new Model_Cashier();
					$result=$objPromo->getPromotion($promo_code);
					$arr_data=array(
						'action'=>'get_promotion',
						'data'=>$result
					);
					$this->view->arr_data=$arr_data;
				}
				
			}//post
			
			
			if ($this->_request->isGET()) {	
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();				
				$action = $filter->filter($this->getRequest()->getParam("actions"));	
				if($action=='memberinfo'){
					$status_no = $filter->filter($this->getRequest()->getParam('status_no'));
					$member_no = $filter->filter($this->getRequest()->getParam('member_no'));	
					$objPos =new Model_PosGlobal();
					$json=$objPos->getMemberInfo($status_no,$member_no);
					$arr_data=array(
						'action'=>'memberinfo',
						'data'=>$json
					);
					$this->view->arr_data=$arr_data;
				}//
				
				if($action=='getSumCshTemp'){
					$flg_chk_point=$filter->filter($this->getRequest()->getParam('flg_chk_point'));	
					$xpoint = $filter->filter($this->getRequest()->getParam('xpoint'));	
					$flg_tbl = $filter->filter($this->getRequest()->getParam('flg_tbl'));	
					$cn_amount = $filter->filter($this->getRequest()->getParam('cn_amount'));	
					$objCashier =new Model_Cashier();
					$json=$objCashier->getSumCshTemp($xpoint,$flg_chk_point,$flg_tbl,$cn_amount);
					$arr_data=array(
						'action'=>'getSumCshTemp',
						'data'=>$json
					);
					$this->view->arr_data=$arr_data;
				}
			}//get		
		}//func
		
		function getsumcshtempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			if ($this->_request->isGet()) {	
				$flg_chk_point=$filter->filter($this->getRequest()->getParam('flg_chk_point'));	
				$xpoint = $filter->filter($this->getRequest()->getParam('xpoint'));	
				$flg_tbl = $filter->filter($this->getRequest()->getParam('flg_tbl'));	
				$cn_amount = $filter->filter($this->getRequest()->getParam('cn_amount'));
				$objCashier =new Model_Cashier();
				$json=$objCashier->getSumCshTemp($xpoint,$flg_chk_point,$flg_tbl,$cn_amount);
				echo $json;
			}
		}//func
		
		function setpromotiontempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			if ($this->_request->isPost()) {	
					$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));
					$promo_id= $filter->filter($this->getRequest()->getPost('promo_id'));
					$promo_st= $filter->filter($this->getRequest()->getPost('promo_st'));				
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
					$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
					$quantity= $filter->filter($this->getRequest()->getPost('quantity'));
					$status_no= $filter->filter($this->getRequest()->getPost('status_no'));
					$product_status= $filter->filter($this->getRequest()->getPost('product_status'));
					$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
					$card_status= $filter->filter($this->getRequest()->getPost('card_status'));
					$discount_member= $filter->filter($this->getRequest()->getPost('discount_member'));
					$discount_percent= $filter->filter($this->getRequest()->getPost('discount_percent'));
					$member_percent1= $filter->filter($this->getRequest()->getPost('member_percent1'));
					$member_percent2= $filter->filter($this->getRequest()->getPost('member_percent2'));
					$co_promo_percent= $filter->filter($this->getRequest()->getPost('co_promo_percent'));
					$coupon_percent= $filter->filter($this->getRequest()->getPost('coupon_percent'));
					$get_point= $filter->filter($this->getRequest()->getPost('get_point'));		
					$net_amt= $filter->filter($this->getRequest()->getPost('net_amt'));		
					$discount= $filter->filter($this->getRequest()->getPost('discount'));								
					$objCashier = new Model_Cashier();		
					$result=$objCashier->setPromotionTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
													$status_no,$product_status,$application_id,$card_status,$get_point,
													$promo_id,$discount_percent,$member_percent1,$member_percent2,
													$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member);
					echo $result;
			}//if
		}//func
		
		function setcshvaltempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);			
			$filter=new Zend_Filter_StripTags();
			if ($this->_request->isPost()) {	
					$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
					$promo_code= $filter->filter($this->getRequest()->getPost('promo_code'));
					$promo_id= $filter->filter($this->getRequest()->getPost('promo_id'));
					$promo_st= $filter->filter($this->getRequest()->getPost('promo_st'));				
					$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
					$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
					$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
					$quantity= $filter->filter($this->getRequest()->getPost('quantity'));
					$status_no= $filter->filter($this->getRequest()->getPost('status_no'));
					$product_status= $filter->filter($this->getRequest()->getPost('product_status'));
					$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
					$card_status= $filter->filter($this->getRequest()->getPost('card_status'));
					$discount_percent= $filter->filter($this->getRequest()->getPost('discount_percent'));
					$member_percent1= $filter->filter($this->getRequest()->getPost('member_percent1'));
					$member_percent2= $filter->filter($this->getRequest()->getPost('member_percent2'));
					$co_promo_percent= $filter->filter($this->getRequest()->getPost('co_promo_percent'));
					$coupon_percent= $filter->filter($this->getRequest()->getPost('coupon_percent'));
					$get_point= $filter->filter($this->getRequest()->getPost('get_point'));				
					//*WR17082016 for support lucky draw promotion
					$mem_card_info= $filter->filter($this->getRequest()->getPost('mem_card_info'));
					$objCashier = new Model_Cashier();		
					$result=$objCashier->setCshValTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
											$status_no,$product_status,$application_id,$card_status,$get_point,
											$promo_id,$discount_percent,$member_percent1,$member_percent2,
											$co_promo_percent,$coupon_percent,$promo_st,$mem_card_info);	
					echo $result;
			}		
		
		}//func
		
		function cashiertempAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			$order_no=$filter->filter($this->getRequest()->getPost('order_no'));
			if($actions=='gettmp'){
				if($order_no!='xx'){
					$page=$filter->filter($this->getRequest()->getPost('page'));
					$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
					$query=$filter->filter($this->getRequest()->getPost('query'));	
					$rp=$filter->filter($this->getRequest()->getPost('rp'));
					$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
					$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
					$objInv=new Model_Cashier();
					$arr_json=$objInv->getCshTemp($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder);							
					$this->view->arr_json=$arr_json;
				}else{
					$data=array();
					$this->view->arr_json=$data;
				}
			}
			
		}//func
		
		function promotiontempAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			$page=$filter->filter($this->getRequest()->getPost('page'));
			$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
			$query=$filter->filter($this->getRequest()->getPost('query'));	
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
			$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
			$objInv=new Model_Cashier();
			$arr_json=$objInv->getPromotionTemp($page,$qtype,$query,$rp,$sortname,$sortorder);							
			$this->view->arr_json=$arr_json;
			
		}//func
		
		public function getpagesAction(){			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$flg=$filter->filter($this->getRequest()->getPost('flg'));
			$objTr=new Model_Cashier();
			$cpage=$objTr->getPageTotal($rp,$flg);
			echo $cpage;
			//$this->view->cpage=$cpage;
		}//func		
		
		function deletetempAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			if($actions=='removeFormGrid'){
				$items=$filter->filter($this->getRequest()->getPost('items'));
				//check 50BTO1P can't delete				
				$objRmv= new Model_Cashier();
				$res_check=$objRmv->chkProLockDel();
				if($res_check=='N'){
					$objResult=$objRmv->removeGridTemp($items);
					echo $objResult;
				}else{
					echo $res_check;
				}
				//stock balance
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$objPos->stockBalance($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_DOC_DATE);
				
			}
		}//func
		
		public function formbrowsAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			$now = $filter->filter($this->getRequest()->getParam("now"));
			$actions = $filter->filter($this->getRequest()->getParam("actions"));			
			$this->view->now=$now;			
			if($actions=='brows_product'){
				//set stock balance
				//$objPos=new SSUP_Controller_Plugin_PosGlobal();
				//$objPos->stockBalance($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_DOC_DATE);
				$objCashier=new Model_Cashier();
				$rows=$objCashier->browsProduct("all");
				if(count($rows)>0){	
					$arr_data=array(
						'action'=>'brows_product',
						'data'=>$rows
					);
					$this->view->arr_data=$arr_data;
				}else{
					$this->view->arr_data=array();
				}
			}
			
			if($actions=='brows_docstatus'){
				$objPos=new Model_PosGlobal();
				$rows=$objPos->browsDocStatus("SL","all");
				if(count($rows)>0){	
					$arr_data=array(
						'action'=>'brows_docstatus',
						'data'=>$rows
					);
					$this->view->arr_data=$arr_data;
				}else{
					$this->view->arr_data=array();
				}
			}
			
			if($actions=='brows_paid'){
				$obj_sale=new Model_PosGlobal();
				$rows=$obj_sale->getPaid();
				$arr_data=array(
					'action'=>'brows_paid',
					'data'=>$rows
				);
				$this->view->arr_data=$arr_data;
			}
			
		}//func
		
		function paymentAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			if($actions=='savetransaction'){
				$user_id=$filter->filter($this->getRequest()->getPost('user_id'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));
				$saleman_id=$filter->filter($this->getRequest()->getPost('saleman_id'));
				$doc_tp=$filter->filter($this->getRequest()->getPost('doc_tp'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));				
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				$special_percent=$filter->filter($this->getRequest()->getPost('special_percent'));			
				$net_amount=$filter->filter($this->getRequest()->getPost('net_amount'));				
				$ex_vat_amt=$filter->filter($this->getRequest()->getPost('ex_vat_amt'));
				$ex_vat_net=$filter->filter($this->getRequest()->getPost('ex_vat_net'));
				$vat=$filter->filter($this->getRequest()->getPost('vat'));
				$paid=$filter->filter($this->getRequest()->getPost('paid'));
				$pay_cash=$filter->filter($this->getRequest()->getPost('pay_cash'));
				$pay_cash2=$filter->filter($this->getRequest()->getPost('pay_cash2'));
				$pay_credit=$filter->filter($this->getRequest()->getPost('pay_credit'));
				$redeem_point=$filter->filter($this->getRequest()->getPost('redeem_point'));				
				$change=$filter->filter($this->getRequest()->getPost('change'));
				$change2=$filter->filter($this->getRequest()->getPost('change2'));
				$coupon_code=$filter->filter($this->getRequest()->getPost('coupon_code'));
				$pay_cash_coupon=$filter->filter($this->getRequest()->getPost('pay_cash_coupon'));
				$credit_no=$filter->filter($this->getRequest()->getPost('credit_no'));
				$credit_tp=$filter->filter($this->getRequest()->getPost('credit_tp'));
				$bank_tp=$filter->filter($this->getRequest()->getPost('bank_tp'));				
				$name=$filter->filter($this->getRequest()->getPost('name'));
				$address1=$filter->filter($this->getRequest()->getPost('address1'));
				$address2=$filter->filter($this->getRequest()->getPost('address2'));
				$address3=$filter->filter($this->getRequest()->getPost('address3'));				
				$get_point=$filter->filter($this->getRequest()->getPost('get_point'));
				$point_begin=$filter->filter($this->getRequest()->getPost('point_begin'));//WR26122013
				$point_receive=$filter->filter($this->getRequest()->getPost('point_receive'));
				$point_used=$filter->filter($this->getRequest()->getPost('point_used'));
				$point_net=$filter->filter($this->getRequest()->getPost('point_net'));				
				$xpoint=$filter->filter($this->getRequest()->getPost('xpoint'));				
				$refer_doc_no=$filter->filter($this->getRequest()->getPost('refer_doc_no'));
				$cn_amount=$filter->filter($this->getRequest()->getPost('cn_amount'));
				$remark1=$filter->filter($this->getRequest()->getPost('remark1'));
				$remark2=$filter->filter($this->getRequest()->getPost('remark2'));
				$xpoint_promo_code=$filter->filter($this->getRequest()->getPost('xpoint_promo_code'));
				//*WR22012015
				$special_day=$filter->filter($this->getRequest()->getPost('special_day'));
				$idcard=$filter->filter($this->getRequest()->getPost('idcard'));	
				$mobile_no=$filter->filter($this->getRequest()->getPost('mobile_no'));
				//*WR10032015
				$bill_manual_no=$filter->filter($this->getRequest()->getPost('bill_manual_no'));
				$ticket_no=$filter->filter($this->getRequest()->getPost('ticket_no'));
				//*WR10032017
				$passport_no=$filter->filter($this->getRequest()->getPost('passport_no'));
				//*WR27032018
				$application_id=$filter->filter($this->getRequest()->getPost('application_id'));
				$objPay=new Model_Cashier();				
				$result=$objPay->paymentNew(
											$user_id,
											$cashier_id,
											$saleman_id,
											$doc_tp,
											$status_no,
											$member_no,											
											$member_percent,
											$special_percent,
											$net_amount,
											$ex_vat_amt,
											$ex_vat_net,
											$vat,
											$paid,
											$pay_cash,
											$pay_cash2,
											$pay_credit,
											$redeem_point,
											$change,
											$change2,
											$coupon_code,									
											$pay_cash_coupon,
											$credit_no,
											$credit_tp,
											$bank_tp,											
											$name,
											$address1,
											$address2,
											$address3,
											$point_begin,
											$point_receive,
											$point_used,											
											$point_net,
											$xpoint,
											$get_point,
											$refer_doc_no,
											$cn_amount,
				    $remark1,$remark2,$xpoint_promo_code,$special_day,$idcard,$mobile_no,$bill_manual_no,$ticket_no,$passport_no,$application_id);		
				if($result==FALSE){
					$result=0;
				}			
				$this->view->result=$result;
			}
		}//func
		
	//-----------------------  finger scan  ----------------------
		public function getuseridAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			
			$ip = $_SERVER['REMOTE_ADDR'];
			$file = file_get_contents("http://$ip/finger_shop/finger/user_id.txt");
			if(trim($file) == ""){
				$arr = array("status"=>'',"userid"=>"");
			}else{
				if($file != "000000"){
					if($file == "not_found"){
						// ข้ามไป id card scanner
						$arr = array("status"=>'sk',"userid"=>"");
						echo json_encode($arr);
						exit();
					}
					$tmp = explode("_",$file);
					$obj = new Model_Cashier();
					list($userid,$password) = $obj->check_user_finger($tmp[0]);
					if($password == ""){
						// user not found
						$arr = array(
								"status"=>"f",
								"userid"=>""
						);
					}else{
						$arr = array(
								"status"=>"y",
								"userid"=>$userid,
								"password"=>$password
						);
					}
				}else{
					$arr = array("status"=>'n',"userid"=>"");
				}

			}
			echo json_encode($arr);	
			
		}//func
		
	}//class
?>