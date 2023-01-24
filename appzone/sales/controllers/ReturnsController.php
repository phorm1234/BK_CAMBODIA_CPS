<?php 
/**
 * 
 * Enter description here ...
 * @author is-wirat
 *
 */
class ReturnsController extends Zend_Controller_Action{	
		public function init(){			
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();			
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
		
		function billshortproductAction(){
			/**
			 * @ 05012015
			 * Description 
			 */
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
				}else if($promo_code=='R_GST201501' || $promo_code=='R_GST201502'){
					$objRe=new Model_Returns();
					$this->view->arr_docno=$objRe->getBillShortGiftset($member_no,$promo_code);
				}else if($promo_code=='R_RED2015'){
					$objRe=new Model_Returns();
					$this->view->arr_docno=$objRe->getBillShortRed($member_no,$promo_code);
				}else{
					$objAcc=new Model_Accessory();
					$this->view->arr_docno=$objAcc->getBillShortProduct($member_no,$promo_code);
				}
			}
		}//func
		
		public function chkfreeshortAction(){
			/**
			 * @desc 05012015 ทำแก้ปัญหาเฉพาะหน้าใช้ครั้งเดียว
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
				}else if($promo_code=='R_GST201501' || $promo_code=='R_GST201502'){
					$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));	
					$member_no=$filter->filter($this->getRequest()->getPost('member_no'));	
					$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));	
					$objR=new Model_Returns();
					$res=$objR->chkFreeShortGiftSet($doc_no,$member_no,$promo_code);
				}else if($promo_code=='R_RED2015'){
					$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));	
					$member_no=$filter->filter($this->getRequest()->getPost('member_no'));	
					$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));	
					$objR=new Model_Returns();
					$res=$objR->chkFreeShortRed($doc_no,$member_no,$promo_code);
				}else{
					$res=$objCsh->chkFreeShortNew($doc_no,$member_no,$promo_code,$member_tp);
				}
				echo $res;
			}
		}//func
}//class