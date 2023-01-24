<?php
	class ReportController extends Zend_Controller_Action{
		function preDispatch()
		{
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			
// 			echo "<pre>";
// 			print_r($empprofile);
// 			echo "</pre>";
			
			if($empprofile['user_id']==''){
				$this->_redirect('/pos/logout/index');
			}
			$res=SSUP_Controller_Plugin_Db::check_log_out($empprofile['user_id'],$empprofile['group_id'],$empprofile['perm_id']);
			if($res== "out"){
				$this->_redirect('/pos/logout/index');
			}	
			$this->_helper->layout()->setLayout('default_layout');
			$this->view->csh_employee_id=$empprofile['employee_id'];		
			$this->view->csh_branch_id=$empprofile['branch_id'];
			if($empprofile['corporation_id']=='CPS'){
				$brand_fullname="SSUP BANGKOK(1991) CO.LTD.";
			}else if($empprofile['corporation_id']=='OP'){
				$brand_fullname="O.P. NATURAL PRODUCTS CO.,LTD.";
			}else if($empprofile['corporation_id']=='GNC'){
				$brand_fullname="SSUP BANGKOK(1991) CO.LTD.";
			}else if($empprofile['corporation_id']=='SS'){
				$brand_fullname="SSUP (CAMBODIA) CO.LTD.";
			}
			$this->view->brand_fullname=$brand_fullname;
		}//func
		
		public function indexAction()
		{	
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$objReport=new Model_Report();
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
			}
			
		}//func
		
		
		public function billvatshortdotmatrixAction(){
			$this->_helper->layout()->disableLayout();			
		}//func
		
		public function billshortdnAction(){
			$this->_helper->layout()->disableLayout();			
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				
				//WR24122013
				$point_total_today = $filter->filter($this->getRequest()->getParam('point_total_today'));
				$transfer_point = $filter->filter($this->getRequest()->getParam('transfer_point'));
				$expire_transfer_point = $filter->filter($this->getRequest()->getParam('expire_transfer_point'));
				$curr_point = $filter->filter($this->getRequest()->getParam('curr_point'));
				$balance_point = $filter->filter($this->getRequest()->getParam('balance_point'));		
				$this->view->point_total_today=intval($point_total_today);
				$this->view->transfer_point=intval($transfer_point);
				$this->view->expire_transfer_point=$expire_transfer_point;
				$this->view->curr_point=intval($curr_point);
				$this->view->balance_point=intval($balance_point);
				
				//*WR09062016
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				
				$objReport=new Model_Report('DN');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				
				//for test 31052016
				if($actions!="reprint"){
					$point_total_today=$arr_h[0]['point_begin'];
				}else{
					$point_total_today=0;
				}
				$this->view->point_total_today=intval($point_total_today);
				
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->paiddesc=$paiddesc;
			}
		}//func
		
		public function billvatshortenAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$point_total_today = $filter->filter($this->getRequest()->getParam('point_total_today'));	
				
				//WR24122013
				$transfer_point = $filter->filter($this->getRequest()->getParam('transfer_point'));
				$expire_transfer_point = $filter->filter($this->getRequest()->getParam('expire_transfer_point'));
				$curr_point = $filter->filter($this->getRequest()->getParam('curr_point'));
				$balance_point = $filter->filter($this->getRequest()->getParam('balance_point'));
				$this->view->point_total_today=intval($point_total_today);
				$this->view->transfer_point=intval($transfer_point);
				$this->view->expire_transfer_point=$expire_transfer_point;
				$this->view->curr_point=intval($curr_point);
				$this->view->balance_point=intval($balance_point);				
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);				
				$arr_l=$objReport->getDetailBill($doc_no);
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$arr_refdiscount=$objReport->discountRefMember($doc_no);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				
				$green_point=$objReport->getGreenPoint($doc_no);
				$this->view->green_point=$green_point;				
				
				$this->view->paiddesc=$paiddesc;
				if(trim($arr_h[0]['member_id'])==''){
					$this->view->arr_refdiscount=$arr_refdiscount;
				}else{
					$this->view->arr_refdiscount=array();
				}
			}
		}//func
		
		public function billrtAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('RT');
				$arr_h=$objReport->getHeaderBillRt($doc_no);
				$arr_l=$objReport->getDetailBillRt($doc_no);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
			}
		}//func
		
		public function billvattotalAction(){
			$this->_helper->layout()->disableLayout();
			$objPos=new Model_PosGlobal();
			$doc_date=$objPos->getDocDate();
			if(!$doc_date){
				$this->_redirect('/error/billopen');
				exit();
			}
			if ($this->_request->isGET()) {
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));
				$point_total_today = $filter->filter($this->getRequest()->getParam('point_total_today'));
		
				//WR24122013
				$transfer_point = $filter->filter($this->getRequest()->getParam('transfer_point'));
				$expire_transfer_point = $filter->filter($this->getRequest()->getParam('expire_transfer_point'));
				$curr_point = $filter->filter($this->getRequest()->getParam('curr_point'));
				$balance_point = $filter->filter($this->getRequest()->getParam('balance_point'));
				$this->view->point_total_today=intval($point_total_today);
				$this->view->transfer_point=intval($transfer_point);
				$this->view->expire_transfer_point=$expire_transfer_point;
				$this->view->curr_point=intval($curr_point);
				$this->view->balance_point=intval($balance_point);
		
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('VT');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
		
				//for test 31052016
				if($actions!="reprint"){
					$point_total_today=$arr_h[0]['point_begin'];
				}else{
					$point_total_today=0;
				}
				$this->view->point_total_today=intval($point_total_today);
		
				$this->view->doc_date=$doc_date;
				$this->view->paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
			}
		}//func
		
// 		public function billvattotalAction(){
// 			$this->_helper->layout()->disableLayout();
// 			$objPos=new Model_PosGlobal();
// 			$doc_date=$objPos->getDocDate();
// 			if(!$doc_date){
// 				$this->_redirect('/error/billopen');
// 				exit();
// 			}
// 			if ($this->_request->isGET()) {
// 				Zend_Loader::loadClass('Zend_Filter_StripTags');
// 				$filter = new Zend_Filter_StripTags();
// 				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));
// 				$actions = $filter->filter($this->getRequest()->getParam('actions'));
// 				$this->view->act=$actions;
// 				$objReport=new Model_Report('VT');
// 				$arr_h=$objReport->getHeaderBill($doc_no);
// 				$arr_l=$objReport->getDetailBill($doc_no);
// 				$this->view->arr_h=$arr_h;
// 				$this->view->arr_l=$arr_l;
// 				$this->view->doc_date=$doc_date;
// 				$this->view->paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
// 			}
// 		}//func
		
		public function billvatshortAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$point_total_today = $filter->filter($this->getRequest()->getParam('point_total_today'));	
				
				//WR24122013
				$transfer_point = $filter->filter($this->getRequest()->getParam('transfer_point'));
				$expire_transfer_point = $filter->filter($this->getRequest()->getParam('expire_transfer_point'));
				$curr_point = $filter->filter($this->getRequest()->getParam('curr_point'));
				$balance_point = $filter->filter($this->getRequest()->getParam('balance_point'));
				$this->view->point_total_today=intval($point_total_today);
				$this->view->transfer_point=intval($transfer_point);
				$this->view->expire_transfer_point=$expire_transfer_point;
				$this->view->curr_point=intval($curr_point);
				$this->view->balance_point=intval($balance_point);				
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);				
				$arr_l=$objReport->getDetailBill($doc_no);
				
				//*WR20032017
				if($arr_h[0]['credit_tp']=="Alipay"){
					$objAcc=new Model_Accessory();
					//ws add
					$arrpay = $objAcc->checkpaychannel($doc_no);
					$arr_h[0]['cnyamt']=$arrpay['cnyamt'];
					$arr_h[0]['convrate']=$arrpay['convrate'];
					$paiddesc="Partner Trans ID :";
				}else{
					$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				}
				
				//$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$arr_refdiscount=$objReport->discountRefMember($doc_no);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				
				//for test 31052016
				if($actions!="reprint"){
					$point_total_today=$arr_h[0]['point_begin'];
				}else{
					$point_total_today=0;
				}
				$this->view->point_total_today=intval($point_total_today);
				
				$green_point=$objReport->getGreenPoint($doc_no);
				$this->view->green_point=$green_point;				
				
				$this->view->paiddesc=$paiddesc;
				if(trim($arr_h[0]['member_id'])==''){
					$this->view->arr_refdiscount=$arr_refdiscount;
				}else{
					$this->view->arr_refdiscount=array();
				}
			}
		}//func
		

		
		public function billdncornerAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));					
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$arr_refdiscount=$objReport->discountRefMember($doc_no);
				$this->view->act=$actions;
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->paiddesc=$paiddesc;
				if(trim($arr_h[0]['member_id'])==''){
					$this->view->arr_refdiscount=$arr_refdiscount;
				}else{
					$this->view->arr_refdiscount=array();
				}
			}
		}//func
		
		public function billvatshortcornerAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$point_total_today = $filter->filter($this->getRequest()->getParam('point_total_today'));	
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$arr_refdiscount=$objReport->discountRefMember($doc_no);
				$this->view->act=$actions;
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->point_total_today=$point_total_today;
				$this->view->paiddesc=$paiddesc;
				if(trim($arr_h[0]['member_id'])==''){
					$this->view->arr_refdiscount=$arr_refdiscount;
				}else{
					$this->view->arr_refdiscount=array();
				}
			}
		}//func
		
		public function billcnAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->paiddesc=$paiddesc;
			}
		}//func
		
		public function billecouponemployeeAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->paiddesc=$paiddesc;
			}
		}//func
		
		public function billvatecouponemployeeAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('VS');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$paiddesc=$objReport->getPaidDesc($arr_h[0]['paid']);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->paiddesc=$paiddesc;
			}
		}//func
		
		public function billactpointrdAction(){
			$this->_helper->layout()->disableLayout(); // Will not load the layout
       		//$this->_helper->viewRenderer->setNoRender(true); //Will not render view
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$refer_doc_no=$arr_h[0]['refer_doc_no'];
				$arr_actpointdetail=$objReport->getActPointDetail($doc_no);				
				$this->view->arr_actpointdetail=$arr_actpointdetail;				
				$arr_pbill=array();
				if($arr_h[0]['member_id']!=''){
					$arr_pbill=$objReport->getProfile($arr_h[0]['member_id']);
				}
				$this->view->arr_pbill=$arr_pbill;
				$this->view->arr_h=$arr_h;				
				//mark print_no
				$objReport->setReprintNo($doc_no);
				$this->view->print_no=$objReport->getReprintNo($doc_no);
			}			
		}//func
		
		public function billactpointslAction(){
			$this->_helper->layout()->disableLayout(); // Will not load the layout
       		$this->_helper->viewRenderer->setNoRender(true); //Will not render view
		}//func
		
		public function testAction(){
			$this->_helper->layout()->disableLayout();
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
			}
		}//func
		
		function billvatorderwebAction(){
			$this->_helper->layout()->disableLayout();
			$objPos=new Model_PosGlobal();
			$doc_date=$objPos->getDocDate();
			if(!$doc_date){
				$this->_redirect('/error/billopen');
				exit();
			}
			if ($this->_request->isGET()) {						
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				$doc_no = $filter->filter($this->getRequest()->getParam('doc_no'));	
				$actions = $filter->filter($this->getRequest()->getParam('actions'));
				$this->view->act=$actions;
				$objReport=new Model_Report('VT');
				$arr_h=$objReport->getHeaderBill($doc_no);
				$arr_l=$objReport->getDetailBill($doc_no);
				$this->view->arr_h=$arr_h;
				$this->view->arr_l=$arr_l;
				$this->view->doc_date=$doc_date;
			}
		}//func
		
		function billdetailAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter=new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_stop = $filter->filter($this->getRequest()->getParam("doc_no_stop"));
			$doc_tp=strtoupper($doc_tp);					
			
			$arr_refdiscount=array();
			$this->view->act='detail';				
			$this->view->point_total_today='';
			$this->view->paiddesc='';
			$this->view->arr_refdiscount=array();			
			
			$objAcc=new Model_Report();
			if($doc_tp=='CN'){
				$objAcc->billCnDetail($doc_no_start,$doc_no_stop);		
			}else if($doc_tp=='SL,VT'){			
				$objAcc->billDetail($doc_no_start,$doc_no_stop);		
			}else if($doc_tp=='RD'){
				$objAcc->billRdDetail($doc_no_start,$doc_no_stop);	
			}else if($doc_tp=='DN'){
				$objAcc->billDNDetail($doc_no_start,$doc_no_stop);	
			}else if($doc_tp=='PL'){
				$objInv=new Model_Inventory();
				$objInv->pickinglistReport($doc_no_start,$doc_no_stop);	
			}else if($doc_tp=='RT'){
				$objAcc->billRTDetail($doc_no_start,$doc_no_stop);	
			}
		}//func
		
	}//calss
?>