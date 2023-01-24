<?php 
	class MemberController extends Zend_Controller_Action{
		//*WR23062014
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
			//*WR23062014
			$this->_CORPORATION_ID=$empprofile['corporation_id'];
			$this->_COMPANY_ID=$empprofile['company_id'];
		    $this->_BRANCH_ID=$empprofile['branch_id'];
		    $this->_BRANCH_NO=$empprofile['branch_no'];
		    $this->_CHECK_SESSION=$empprofile['employee_id'];
		    $this->_GROUP_ID=$empprofile['group_id'];
			$this->view->session_employee_id=$empprofile['employee_id'];
			$this->SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
			$this->SESSION_USER_ID=$empprofile['user_id'];
			//check doc_date
			$objPos=new Model_PosGlobal();
			$this->_DOC_DATE=$objPos->checkDocDate();
		}//func
		
		function checkismemberAction(){
		    $this->_helper->layout()->disableLayout();
		    $this->_helper->viewRenderer->setNoRender(TRUE);
		    Zend_Loader::loadClass('Zend_Filter_StripTags');
		    $filter = new Zend_Filter_StripTags();
		    if ($this->_request->isPost()){
		        $member_no=$filter->filter($this->getRequest()->getPost('member_no'));
		        $mobile_no=$filter->filter($this->getRequest()->getPost('mobile_no'));
		        $objM=new Model_Member();
		        $is_member=$objM->checkLocalMemberExist($member_no,$mobile_no);
		        if($is_member=='N'){
		            $url_gcard="http://crmcpskh.ssup.co.th/app_service_cpskh/api_member/api_check_member.php?mobile=$mobile_no&member_no=$member_no";
		            $str_gcard=@file_get_contents($url_gcard,0);
		        }else{
		            $str_gcard='[{"status":"Y","msg":"YOU ARE ALREADY A MEMBER."}]';
		        }
		        echo $str_gcard;
		        //echo json_encode($str_gcard);
		    }
		}//func
		
		function formregisterAction(){
		    $this->_helper->layout()->disableLayout();
		    $this->_helper->viewRenderer->setNoRender(TRUE);
		    $objM=new Model_Member();
		    $chkbillregister=$objM->chkPlaybillRegister();		    
		    echo $chkbillregister;
		}//func
		
		function chkmemberbycompanyAction(){
			/**
			 * @desc
			 * *WR 10062016 for support gnc member
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no_by_comp=$filter->filter($this->getRequest()->getPost('member_no_by_comp'));
				$url_gcard="http://10.100.53.2/ims/joke/app_service_op/api_member/api_member_allbrand_op.php?member_no=$member_no_by_comp";
				$str_gcard=@file_get_contents($url_gcard,0);
				echo $str_gcard;
			}
		}//func
		
		function formcheckmemberbycompanyAction(){
			/**
			 * @desc 10062016 for gnc member
			 * @return phtml form confirm member_no
			 */
			$this->_helper->layout()->disableLayout();
		}//func
		
		function getcardquotaAction(){
			/**
			 * @desc for support OPID300
			 * @create 26052015
			 * @return String card quota
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$objM=new Model_Member();
				$objM->clsCardQuota();
				$url_gcard="http://10.100.53.2/ims/joke/app_service_op/quota_card/api_member_quota_card.php";
				$str_gcard=@file_get_contents($url_gcard,0);	
				if($str_gcard!=''){
					$objM->addCardQuota($str_gcard);
				}
				echo $str_gcard;
			}
		}//func
		
		function chklstotherproAction(){
			/**
			 * @desc for support promotion table promo_other
			 * @create 24032015 
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();	
				$res_msg=$objMember->chkLstOtherPro($promo_code);
				echo $res_msg;
			}
		}//func
		
		function chkcompbyseqproAction(){
			/**
			 * @desc check complete condition by seq
			 * for support promotion table promo_other
			 * @create 17022016
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$seq_pro=$filter->filter($this->getRequest()->getPost('seq_pro'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				//*WR13072016 for suport last promotion
				$play_last_pro=$filter->filter($this->getRequest()->getPost('play_last_pro'));
				$objMember=new Model_Member();
				$res_msg=$objMember->chkCompBySeqPro($promo_code,$seq_pro,$play_last_pro);
				echo $res_msg;
			}
		}//func
		
		function chkcompbyseqpro21072016Action(){
			/**
			 * @desc check complete condition by seq
			 * for support promotion table promo_other
			 * @create 17022016
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$seq_pro=$filter->filter($this->getRequest()->getPost('seq_pro'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();
				$res_msg=$objMember->chkCompBySeqPro($promo_code,$seq_pro);
				echo $res_msg;
			}
		}//func
		
		function playbyseqproAction(){
			/**
			 * @desc for support promotion table promo_other
			 * @create 17022016
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$seq_pro=$filter->filter($this->getRequest()->getPost('seq_pro'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();
				$res_msg=$objMember->playBySeqPro($promo_code,$seq_pro);
				echo $res_msg;
			}
		}//func
		
		function setmstpdtproAction(){
			/**
			 * @desc for support promotion table promo_other
			 * @create 17022016
			 * @modify 13072016
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$seq_pro=$filter->filter($this->getRequest()->getPost('seq_pro'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				//*WR13072016 for support last promotion
				$play_last_pro=$filter->filter($this->getRequest()->getPost('play_last_pro'));
				$objMember=new Model_Member();
				$res_msg=$objMember->setPdtMstPro($seq_pro,$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent,$play_last_pro);
				echo $res_msg;
			}
		}//func
		
		function setmstpdtpro21072016Action(){
			/**
			 * @desc for support promotion table promo_other
			 * @create 17022016
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$seq_pro=$filter->filter($this->getRequest()->getPost('seq_pro'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				$objMember=new Model_Member();
				$res_msg=$objMember->setPdtMstPro($seq_pro,$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent);
				echo $res_msg;
			}
		}//func
		
		function setpdtotherproAction(){
			/**
			 * @desc for support promotion table promo_other
			 * @create 24032015 
			 * @return $res_msg
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				$objMember=new Model_Member();	
				$res_msg=$objMember->setPdtOtherPro('',$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent);
				echo $res_msg;
			}
		}//func	
		
		function chkprodgroupAction(){
			/***
			 * @desc for support OPPF300
			 * @modify : 05012015
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$objMember=new Model_Member();	
				$status_chk=$objMember->chkProdGroup($promo_code,$product_id);
				echo $status_chk;
			}
		}//func
		
		function getproductpriceAction(){
			/***
			 * @desc for support OPPC300
			 * @create : 17122014
			 * @modify : 06032015 append check barcode
			 * 	*/
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$objPos=new Model_PosGlobal();	
				$product_id=$objPos->getProduct($product_id,'1','','');
				$arr_product=$objPos->browsProduct($product_id);
				echo $arr_product[0]['price'];
			}
		}//func 
		
//		function getproductpriceAction(){
//			/***
//			 * @desc for support OPPC300
//			 * @modify : 17122014
//			 */
//			$this->_helper->layout()->disableLayout();
//			$this->_helper->viewRenderer->setNoRender(TRUE);
//			Zend_Loader::loadClass('Zend_Filter_StripTags');
//			$filter = new Zend_Filter_StripTags();
//			if ($this->_request->isPost()){	
//				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
//				$objPos=new Model_PosGlobal();	
//				$arr_product=$objPos->browsProduct($product_id);
//				echo $arr_product[0]['price'];
//			}
//		}//func 
		
		function setpd2tempAction(){
			/***
			 * @desc
			 * @modify : 20102014
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$seq_pro=$filter->filter($this->getRequest()->getPost('seq_pro'));
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				$objMember=new Model_Member();	
				$res_msg=$objMember->setPdt2Temp($seq_pro,$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent);
				echo $res_msg;
			}
		}//func
				
		function setlstpromoproductAction(){
			/**
			 * @desc
			 * @create 25022016
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$unitn=$filter->filter($this->getRequest()->getPost('unitn'));
				$seq_pro='1';
				$quantity='1';
				$objMember=new Model_Member();
				$res_setitems=$objMember->setPdtMstPro($seq_pro,$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent='0');
				echo $res_setitems;
			}
		}//func
		
		function setcopromoproductAction(){
			/**
			 * @desc 22072014 ***********************come back to test na ja 13082014
			 * * ใช้ร่วมกับ new birth และ co promotion ด้วยกับมาทดสอบนะ
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){			
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));	
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$unitn=$filter->filter($this->getRequest()->getPost('unitn'));	
				$objMember=new Model_Member();		
				$objMember->setCoPromoProduct($member_no,$promo_code,$product_id,$unitn,$status_no);
			}			
		}//func
		
		function setpdtmcouponAction(){
			/**
			 * @desc 16102014 for support mobile coupon 30%
			 * @modify 13052015 append param $member_percent
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$member_percent=$filter->filter($this->getRequest()->getPost('member_percent'));
				$objMember=new Model_Member();	
				$res_msg=$objMember->setPdtMCoupon($member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent);
				echo $res_msg;
			}
		}//func	
		
		function setpdtredeempointAction(){
			/**
			 * @desc 22072014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$member_point=$filter->filter($this->getRequest()->getPost('member_point'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$objMember=new Model_Member();	
				$res_msg=$objMember->setPdtRedeemPoint($member_no,$member_point,$status_no,$promo_code,$product_id,$quantity);
				echo $res_msg;
			}
		}//func	
		
		function setcshvaltempAction(){
			/**
			 * @desc 22072014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				//*WR17082016 for support lucky draw promotion
				$mem_card_info= $filter->filter($this->getRequest()->getPost('mem_card_info'));
				$objMember=new Model_Member();
				$objMember->setCshValTemp($member_no,'','',$mem_card_info);
			}
		}//func
		
		function sellstpromotionAction(){
			/**
			 * @desc
			 * @create
			 * @return
			 */
			/***
			 * @desc 22072014 check co promotion
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				//*WR24122014
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$ops_day=$filter->filter($this->getRequest()->getPost('ops_day'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$amount=$filter->filter($this->getRequest()->getPost('amount'));
				$objMember=new Model_Member();
				$this->view->arr_promo=$objMember->selLstPromotion($promo_code,$member_no,$ops_day);
			}
		}//func
		
		function selcopromotionAction(){
			/***
			 * @desc 22072014 check co promotion
			 * @return
			 */
			$this->_helper->layout()->disableLayout();			
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				//*WR24122014
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$ops_day=$filter->filter($this->getRequest()->getPost('ops_day'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$amount=$filter->filter($this->getRequest()->getPost('amount'));
				//*WR03012017 for new birth
				$promo_tp=$filter->filter($this->getRequest()->getPost('promo_tp'));
				$bday=$filter->filter($this->getRequest()->getPost('bday'));
				$objMember=new Model_Member();	
				//$this->view->arr_promo=$objMember->selCoPromotion($promo_code,$net_amt,$amount,$member_no,$ops_day);	
				$this->view->arr_promo=$objMember->selCoPromotion($promo_code,$net_amt,$amount,$member_no,$ops_day,$promo_tp,$bday);
			}
		}//func
		
		function chkcoprootherproAction(){
			/***
			 * @desc 29072014 check co promotion
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$amount=$filter->filter($this->getRequest()->getPost('amount'));
				$objMember=new Model_Member();		
				$result=$objMember->chkCoProOtherPro($promo_code,$net_amt,$amount);
				echo $result;
			}
		}//func
		
		function setfreeproductotherproAction(){
			/**
			 * @desc 29072014
			 * *
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$unitn=$filter->filter($this->getRequest()->getPost('unitn'));	
				//check product exist update on 31072014
				$objPos=new Model_PosGlobal();
				$res_product_id=$objPos->getProduct($product_id,'1','00');
				if($res_product_id=='1'){
					$msg_result="ไม่พบรายการสินค้า $product_id ในทะเบียน";
				}else if($res_product_id=='2'){
					$msg_result="รายการสินค้า $product_id สต๊อกไม่พอ";
				}else{
					$product_id=$res_product_id;
					$objMember=new Model_Member();		
					$msg_result=$objMember->setFreeProductOtherPro($member_no,$promo_code,$product_id,$unitn);
					if($msg_result=='2'){
						$msg_result="รายการสินค้า $product_id สต๊อกไม่พอ";
					}
				}
				echo $msg_result;
			}			
		}//func
		
		function chklastotherproAction(){
			/**
			 * @desc 29072014
			 * *ตรวจสอบของแถวท้ายบิล
			 * return last copromotion
			 */
			$this->_helper->layout()->disableLayout();
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				if ($this->_request->isPost()){
					$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
					$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
					$amount=$filter->filter($this->getRequest()->getPost('amount'));					
					$objMember=new Model_Member();
					$this->view->arr_promo=$objMember->chkLastOtherPro($promo_code,$net_amt,$amount);
				}
		}//func
		
		function chkcouponisusedAction(){
			/**
			 * @desc 22072014
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				//*WR26112014
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$coupon_code=$filter->filter($this->getRequest()->getPost('coupon_code'));
				$objMember=new Model_Member();
				$res_msg=$objMember->chkCouPonIsUsed($member_no,$coupon_code);
				echo $res_msg;
			}
		}//func
		
	   public function getproredeempointAction(){
	   	/**
	   	 * @desc 22072014
	   	 * @return 
	   	 */
	   		$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objPro=new Model_Member();
				$this->view->arr_promo=$objPro->getListProRedeemPiont($promo_code);
			}
	   }//func
		
		public function callcouponAction(){
			/**
			 * @desc : modify 23062014
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$coupon_code=$filter->filter($this->getRequest()->getPost('coupon_code'));
				$mobile_no=$filter->filter($this->getRequest()->getPost('mobile_no'));//*WR27102014
				$redeem_code=$filter->filter($this->getRequest()->getPost('coupon_code'));
				$idcard=$filter->filter($this->getRequest()->getPost('idcard'));
				//$ws = "http://mobile.orientalprincess.com/opmsg/opsimg/ecoupon_redeem.php?";
				$ws = "http://mshop.ssup.co.th/ecoupon/promo_redeem.php?";
				//*WR27102014 for MCS Power C
				if($promo_code=='OS02401014' || $promo_code=='OM12271114'){
					$ws="http://mshop.ssup.co.th/shop_op/sms_redeem.php?";
				}
				$type = "json";
				$shop=$this->_BRANCH_ID;			
				$act = "request";
				$src = $ws."member_no=".$member_no."&brand=op&dtype=".$type."&shop=".$shop."&promo_code=".$promo_code."&mobile_no=".$mobile_no."&coupon_code=".$coupon_code."&idcard=".$idcard."&act=".$act."&_=1334128422190";
				$o=@file_get_contents($src,0);		
				echo $o;	
			}
		}//func
		
		public function lockcouponproAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){		
				$idcard=$filter->filter($this->getRequest()->getPost('idcard'));
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$coupon_code=$filter->filter($this->getRequest()->getPost('coupon_code'));
				$privilege_type='';
				if($promo_code=='OS02401014' || $promo_code=='OM12271114'){
					$ws="http://mshop.ssup.co.th/shop_op/sms_redeem.php?";
					$privilege_type='SMS';
				}else if($promo_code=='OI27010115' || $promo_code=='OI06010615'){
					$ws = "http://mshop.ssup.co.th/shop_op/line_redeem.php?";
					$privilege_type='LINE';					
				}else{
					$ws = "http://mshop.ssup.co.th/ecoupon/promo_redeem.php?";
					$privilege_type='COUPON';
				}
				$type = "json";
				$shop=$this->_BRANCH_ID;
				$act = "lock";
				$src = $ws."member_no=".$member_no."&brand=op&doc_no=".$doc_no."&shop=".$shop."&promo_code=".$promo_code."&mobile_no=".$mobile_no."&coupon_code=".$coupon_code."&idcard=".$idcard."&act=".$act."&_=1334128422190";
				$o=@file_get_contents($src,0);
				
				$lock_status='N';
				$objJson=json_decode($o);				
				if($objJson->status=='OK' || $objJson->status=='YES'){
					$lock_status='Y';
				}
				$objMember=new Model_Member();
				$objMember->setLogMemberPrivilege($member_no,$promo_code,'',$idcard,$coupon_code,$privilege_type,$lock_status,$doc_no);	
				echo $o;	
			}
		}//func
		
		public function couponproAction(){
			/**
			 * @desc :
			 * @modify 17082016 for support lucky draw promotion
			 */
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
				
			if ($this->_request->isPost()){
				$mem_card_info=$filter->filter($this->getRequest()->getPost('mem_card_info'));
				$objPro=new Model_Member();
				$this->view->arr_promo=$objPro->getListCoupon();
				$this->view->mem_card_info=$mem_card_info;
			}
		}//func
				
		function callmemberprofileAction(){
			/**
			 * @desc modify : 18062014
			 * @return json data
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGET()) {	
				$status_no = $filter->filter($this->getRequest()->getParam('status_no'));
				$member_no = $filter->filter($this->getRequest()->getParam('member_no'));	
				$objPos =new Model_PosGlobal();
				$json=$objPos->getMemberInfo($status_no,$member_no);
				echo $json;
			}
		}//func
		
		function freegifsetproductAction(){
			/**
			 * @desc : modify 19052014
			 * @return null
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$objMember=new Model_Member();		
				$msg_result=$objMember->setFreegifset($member_no,$promo_code,$net_amt);
				echo $msg_result;
			}
		}//func
		
		function formconfirmmemberidAction(){
			/**
			 * @desc 24042014
			 * @return phtml form confirm member_no
			 */
			$this->_helper->layout()->disableLayout();
		}//func
		
		function chkopstuesprivAction(){
			/**
			 * @desc 31032014 for opsday tuesday lip more than thank 
			 * @return 
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$ops_day=$filter->filter($this->getRequest()->getPost('ops_day'));
				$objMember=new Model_Member();		
				$msg_result=$objMember->chkOpsTuesPriv($member_no,$promo_code);
				echo $msg_result;
			}
		}//func
		
		function changenewcardAction(){
			/**
			 * @desc 21032014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$member_no_ref=$filter->filter($this->getRequest()->getPost('member_no_ref'));
				$ops_day=$filter->filter($this->getRequest()->getPost('ops_day'));
				$objMember=new Model_Member();		
				$msg_result=$objMember->changeNewCard($member_no,$member_no_ref,$ops_day);
				echo $msg_result;
			}
		}//func
		
		function setnewcardbalanceAction(){
			/**
			 * @desc modify 26022014
			 * @return null
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));						
				$objMember=new Model_Member();
				$objMember->setNewCardBalance($promo_code);
			}
		}//func
		
		function chkfreeproductnewbirthAction(){
			/**
			 * @desc modify 10022014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));		
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));	
				$objMember=new Model_Member();
				$objMember->chkFreeProductNewbirth($promo_code,$product_id);
			}
		}//func
		
		function rediscntfshpurchaseAction(){
			/**
			 * @desc modify 10022014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));		
				$objMember=new Model_Member();
				$objMember->reDisCntFshPurchase($member_no);
			}
		}//func
		
		function upddiscntfshpurchaseAction(){
			/**
			 * @desc modify 24042014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){								
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));	
				$amount=$filter->filter($this->getRequest()->getPost('amount'));	
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));	
				$objMember=new Model_Member();
				$objMember->updDisCntFshPurchase($member_no,$amount,$net_amt);
			}
		}//func
		
		function chkopsdaybyidcardAction(){
			/***
			 * @desc create : 26042013
			 */
			$this->_helper->layout()->disableLayout();		
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){
				$idcard=$filter->filter($this->getRequest()->getPost('idcard'));
				
				//chk online mode
				$objPos=new Model_PosGlobal();
				$res_mode=$objPos->chkModeOnLine();
				if($res_mode=='1'){				
					$objOps=new Model_Calpromotion();
					$arr_data=$objOps->chk_ops($idcard);				
					if(!empty($arr_data)){
						$objMember=new Model_Member();
						$str_json=$objMember->setOpsDayNewCard($arr_data);			
					}
					$this->view->arr_ops=$arr_data;				
				}else{
					$this->view->arr_ops=array();
				}				
			}
		}//func
		
		function receiverewardAction(){
			$this->_helper->layout()->disableLayout();		
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$objMember=new Model_Member();
				$arr_data=$objMember->getReceiveReward($member_no);				
				$this->view->arr_data=$arr_data;
			}
		}//func
		
		function formmemberAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function chkfirstlmtAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$first_limited=$filter->filter($this->getRequest()->getPost('first_limited'));	
				$first_percent=$filter->filter($this->getRequest()->getPost('first_percent'));	
				$add_first_percent=$filter->filter($this->getRequest()->getPost('add_first_percent'));	
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));	
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));											
				$objMember=new Model_Member();
				$objMember->chkFirstLimited($first_limited,$first_percent,$add_first_percent,$product_id,$quantity);			
			}
		}//func
		
		function initblrwdAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$objMember=new Model_Member();
				$objMember->iniTblRwd();			
			}
		}//func
		
		function chekreceivestockAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$items=$filter->filter($this->getRequest()->getPost('items'));
				$objMember=new Model_Member();
				$arr_result=$objMember->chekReceiveStock($items);
				$str_res="";
				if(!empty($arr_result)){
					foreach($arr_result as $data){
						$str_error='';
						if($data['error_status']=='0'){
							$str_error="สต๊อกไม่พอ";
						}else if($data['error_status']=='1'){
							$str_error="ไม่พบสินค้า หรือสินค้าไม่อยู่ในช่วงโปรโมชั่น";
						}
						$str_res.=$data['product_id']." ".$str_error.",";
					}
				}
				echo $str_res;
			}
		}//func
		
		function savereceiverewardAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$items=$filter->filter($this->getRequest()->getPost('items'));
				$objMember=new Model_Member();
				$result=$objMember->saveReceiveReward($member_no,$items);
				echo $result;
			}
		}//func
		
		function add2crmAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$str_json=$filter->filter($this->getRequest()->getPost('str_json'));
				$objMember=new Model_Member();
				$objMember->add2Crm($str_json);
			}
		}//func
		
		function calecoupondiscountAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$ecoupon_percent_discount=$filter->filter($this->getRequest()->getPost('ecoupon_percent_discount'));
				$ecp_amount_bal=$filter->filter($this->getRequest()->getPost('ecp_amount_bal'));
				$objMember=new Model_Member();
				$objMember->calEcouponDiscount($ecoupon_percent_discount,$ecp_amount_bal);
			}
		}//func
		
		function recalecoupondiscountAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$objMember=new Model_Member();
				$objMember->reCalEcouponDiscount();
			}
		}//func
		
		function chkedateAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$objMember=new Model_Member();
				$arr_edate= $objMember->chkEdate();
				echo json_encode($arr_edate[0]);
			}
		}//func
		
		function chkempprivilageAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$employee_id=$filter->filter($this->getRequest()->getPost('employee_id'));
				$objMember=new Model_Member();
				$arr_e=$objMember->chkEmpPrivilage($employee_id);
				if(!empty($arr_e)){
					echo json_encode($arr_e[0]);
				}else{
					echo  '';
				}
			}
		}//func
		
		function rwdreceiveAction(){
		}//func
		
		function searchmemberprofileAction(){
			/**
			 * @desc modify : 18062014
			 * @return array of member profile
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$mobile_no=$filter->filter($this->getRequest()->getPost('mobile_no'));
				$id_card=$filter->filter($this->getRequest()->getPost('id_card'));
				$name=$filter->filter($this->getRequest()->getPost('name'));
				$surname=$filter->filter($this->getRequest()->getPost('surname'));
				$ws = "http://10.100.53.2/wservice/webservices/services/search_member_info.php?";
				$type = "json";
				$shop=$this->m_branch_id;
				$act = "search_lost";
				$src = $ws."callback=jsonpCallback&mobile_no=".$mobile_no.
											"&member_no=".$member_no.
											"&id_card=".$id_card.
											"&name=".$name.
											"&surname=".$surname.
											"&brand=op&dtype=".$type."&shop=".$shop."&act=".$act."&_=1334128422190";
				$row_member=array();	
				$o=@file_get_contents($src,0);			
				if ($o === FALSE || !$o){				
					//******************* OFFLINE PROCESS ***************
					$objPos=new Model_PosGlobal();
					$o=$objPos->getMemberOffLineByKeyWord($mobile_no,$id_card,$name,$surname);					
				}else{
					//******************* ONLINE PROCESS ****************
					$o = str_replace("jsonpCallback(","",$o);
					$o = str_replace(")","",$o);
					$o = json_decode($o ,true);
					if(empty($o)) exit();
					$objMember=new Model_Member();					
					$i=0;					
					foreach($o as $data){
						$expire_date=$data['expire_date'];
						$res_status=$objMember->chkCardExpireForRenew($expire_date);
						if($res_status=='2'){
							$o[$i]['remark']="บัตรหมดอายุ";
						}
						$o[$i]['expire_status']=$res_status;
						$o[$i]['link_status']='ONLINE';
						$i++;
					}			
					//******************* ONLINE PROCESS ****************
				}
			    $this->view->arr_json=$o;
				$this->view->doc_date=$this->doc_date;
			}
		}//func
		
		/**
		 * @desc
		 * @modify:11112015
		 */
		function searchmemberAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$mobile_no=$filter->filter($this->getRequest()->getPost('mobile_no'));
				$id_card=$filter->filter($this->getRequest()->getPost('id_card'));
				$name=$filter->filter($this->getRequest()->getPost('name'));
				$surname=$filter->filter($this->getRequest()->getPost('surname'));	
				$ws = "http://10.100.53.2/wservice/webservices/services/search_member.php?";
				$type = "json"; //Only Support JSON 
				$shop=$this->m_branch_id;
				$act = "search_lost";
				$src = $ws."callback=jsonpCallback&mobile_no=".$mobile_no.
											"&id_card=".$id_card.
											"&name=".$name.
											"&surname=".$surname.
											"&brand=op&dtype=".$type."&shop=".$shop."&act=".$act."&_=1334128422190";
				$row_member=array();	
				$o=@file_get_contents($src,0);				
				if ($o === FALSE || !$o){				
					//******************* OFFLINE  ***************
					$objPos=new Model_PosGlobal();
					$o=$objPos->getMemberOffLineByKeyWord($mobile_no,$id_card,$name,$surname);
					//******************* OFFLINE  ***************
				}else{
					//******************* ONLINE  ****************
					$o = str_replace("jsonpCallback(","",$o);
					$o = str_replace(")","",$o);
					$o = json_decode($o ,true);		
					$objMember=new Model_Member();								
					$i=0;					
					foreach($o as $data){
						$expire_date=$data['expire_date'];
						$res_status=$objMember->chkCardExpireForRenew($expire_date);
						if($res_status=='2'){
							$arr_expire=explode("-",$expire_date);							
							$arr_curdate=explode('-',$this->_DOC_DATE);
							$timeStmpCurDate = mktime(0,0,0,$arr_curdate[1],$arr_curdate[2],$arr_curdate[0]);
							$time3MonthAdded = strtotime(date("Y-m-d", strtotime($expire_date)) . "+3 month");								
							if($timeStmpCurDate>$time3MonthAdded){
								//สามารถแจ้งหายหลังเดือนหมดอายุ 2 เดือน หรือภายในเดือนหมดอายุเท่านั้น
								$o[$i]['available']='0';
								$o[$i]['remark']="<u>แจ้งบัตรเสีย/หาย หลังบัตรหมดอายุไม่เกิน 3 เดือนเท่านั้น</u>";
							}else{
								$o[$i]['available']='1';
							}
						}else{
							$o[$i]['available']='1';
						}						
						$o[$i]['expire_status']=$res_status;
						$o[$i]['link_status']='ONLINE';
						$i++;
					}			
					//******************* ONLINE  ****************
				}
			    $this->view->arr_json=$o;
				$this->view->card_status=$filter->filter($this->getRequest()->getPost('card_status'));
			}
		}//func
				
		function autocmemberAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$term = $filter->filter($this->getRequest()->getParam("term"));
			$fieldauto=$filter->filter($this->getRequest()->getParam("fieldauto"));
			$objAutoMember=new Model_Member();
			$result=$objAutoMember->getMemberAutoComplete($term,$fieldauto);
			$this->view->fieldauto=$fieldauto;
			$this->view->arr_member=$result;
		}//func
		
		function percentrangeAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$promo_id=$filter->filter($this->getRequest()->getPost('promo_id'));
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$objMember=new Model_Member();
				$this->view->arr_prange=$objMember->promoPercentRange($promo_code,$promo_id,$net_amt);
			}
		}//func
		
		function giftsetproductAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function freeproductAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function freepremiumAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function memberpointtodayAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isGet()){
				$member_no=$filter->filter($this->getRequest()->getParam("member_no"));
				$objMember=new Model_Member();
				$arr_member=$objMember->getMemberPointToDay($member_no);
				$this->view->arr_member=$arr_member;
				$arr_amt=$objMember->getSumMemberBuyProfile($member_no);
				$this->view->arr_amt=$arr_amt;
			}
		}//func
		
		function memberbuyprofileAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isGet()){
				$member_no=$filter->filter($this->getRequest()->getParam("member_no"));
//				$objMember=new Model_Member();
//				$arr_member=$objMember->getMemberBuyProfile($member_no);
//				$this->view->arr_member=$arr_member;
//				$arr_amt=$objMember->getSumMemberBuyProfile($member_no);
//				$this->view->arr_amt=$arr_amt;
				$objCal=new Model_Calpromotion();
				$arr_profile=$objCal->sale_history($member_no);
				$amount=0.00;
				$net_amt=0.00;
				$total_point_today=0;
				if(!empty($arr_profile)){
					foreach($arr_profile as $dataProfile){
						$amount+=$dataProfile['amount'];
						$net_amt+=$dataProfile['net_amt'];
						if($this->doc_date==$dataProfile['doc_date']){
							$total_point_today+=$dataProfile['total_point'];
						}
					}
				}
				$this->view->total_point_today=$total_point_today;
				$this->view->amount=$amount;
				$this->view->net_amt=$net_amt;
				$this->view->arr_profile=$arr_profile;
			}
		}//func
		
		function firstbuyAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isGet()){
				$member_id=$filter->filter($this->getRequest()->getParam("member_id"));
				$objMember=new Model_Member();
				$result=$objMember->chkFirstBuy($member_id);
				$this->view->result=$result;
			}
		}//func
		
		function memotherproAction(){
			/**
			 * @desc
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objPro=new Model_PosGlobal();
				$arr_promo=$objPro->getOtherPromotion($promo_code);
				if(!empty($arr_promo)){
					echo json_encode($arr_promo);
				}else{
					echo '0';
				}
			}
		}//func
		
		function otherpromoAction(){
			/**
			 * @desc
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();
				$arr_promo=$objMember->getOtherPromotion($promo_code);
				echo $arr_promo;
			}
		}//func
		
		
		function getxpointAction(){
			/**
			 * @desc 10062013
			 * get xpoint from  promo_header by promo_code
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();
				$arr_promo=$objMember->getXPoint($promo_code);
				echo $arr_promo;
			}
		}//func
		
		function checkmountAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();
				$arr_promo=$objMember->chkAmount($promo_code);
				echo $arr_promo;
			}
		}//func
		
		function memberprivilegeAction(){
				$this->_helper->layout()->disableLayout();
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();
				if ($this->_request->isPost()){
					$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
					$birthday=$filter->filter($this->getRequest()->getPost('birthday'));
					$apply_date=$filter->filter($this->getRequest()->getPost('apply_date'));
					$expire_date=$filter->filter($this->getRequest()->getPost('expire_date'));
					$tcase=$filter->filter($this->getRequest()->getPost('tcase'));
					$objMember=new Model_Member();
					$this->view->arr_promo=$objMember->getMemberPrivilege($member_no,$birthday,$apply_date,$expire_date,$tcase);
				}
		}//func
		
//		function logmemprivilegeAction(){
//				$this->_helper->layout()->disableLayout();
//				Zend_Loader::loadClass('Zend_Filter_StripTags');
//				$filter = new Zend_Filter_StripTags();
//				if ($this->_request->isPost()){
//					$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
//					$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
//					$objMember=new Model_Member();
//					$r=$objMember->setLogMemberPrivilege($member_no,$promo_code);
//				}
//		}//func

		function promodiscountAction(){
			/**
			 * @modify 21122015 for support new 2016
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$promo_id=$filter->filter($this->getRequest()->getPost('promo_id'));
				$promo_tp=$filter->filter($this->getRequest()->getPost('promo_tp'));
				$percent=$filter->filter($this->getRequest()->getPost('percent'));
				$point=$filter->filter($this->getRequest()->getPost('point'));
				$discount=$filter->filter($this->getRequest()->getPost('discount'));
				$objMember=new Model_Member();
				
				if($promo_code=='POINT50'){
					$this->view->result=$objMember->discountBeforePayment($promo_code,$promo_id,$promo_tp);
				}else if($this->_DOC_DATE>='2016-01-04' && $promo_code!=='OX29260216' && $promo_code!=='BURNPOINT4'){
					$this->view->result=$objMember->promoDiscountNew($promo_code,$promo_id,$promo_tp,$percent,$point,$discount);
				}else{
					$this->view->result=$objMember->promoDiscount($promo_code,$promo_id,$promo_tp,$percent,$point,$discount);
				}
			}
		}//func
		
		
		
		function repromodiscountAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$promo_id=$filter->filter($this->getRequest()->getPost('promo_id'));
				$objMember=new Model_Member();
				$this->view->result=$objMember->rePromoDiscount($promo_code,$promo_id);
			}
		}//func
		
		function activitypointAction(){
			//$this->_helper->layout()->disableLayout();
		}//func
		
		function formcashierAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function chknetbeforeAction(){
			/**
			 * @desc modify:04022014
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$net_amt=$filter->filter($this->getRequest()->getPost('net_amt'));
				$objMember=new Model_Member();
				$n_chk=$objMember->chkNetBefore($member_no,$net_amt);
				if($n_chk>0){
					echo 'Y';
				}else{
					echo 'N';
				}
			}			
		}//func
		
		function promopointheadwebAction(){
			/**
			 * *@desc create:22012014
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$objMember=new Model_Member();
				$arr_promo=$objMember->getPromoPointHeadWeb($promo_code);
				if(!empty($arr_promo)){
					echo json_encode($arr_promo);
				}else{
					echo '0';
				}
			}			
		}//func
		
		function promopointheadAction(){
			$this->_helper->layout()->disableLayout();
			$objMember=new Model_Member();
			$this->view->arr_promo=$objMember->getPromoPointHead();
		}//func
		
		function promopointdetailAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$promo_tp=$filter->filter($this->getRequest()->getPost('promo_tp'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$objMember=new Model_Member();
				$this->view->arr_promo=$objMember->getPromoPointDetail($promo_code,$promo_tp,$member_no);
			}
		}//func
		
		function promoactpointdescriptionAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$objMember=new Model_Member();
				$rs_json=$objMember->getPromoActPointDetail($promo_code,$member_no);
				$this->view->arr_promo=json_decode($rs_json);
			}
		}//func
		
// 		function promoactpointdescriptionAction(){
// 			$this->_helper->layout()->disableLayout();
// 			Zend_Loader::loadClass('Zend_Filter_StripTags');
// 			$filter = new Zend_Filter_StripTags();
// 			if ($this->_request->isPost()){
// 				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
// 				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));				
// 				$objMember=new Model_Member();
// 				$this->view->arr_promo=$objMember->getPromoActPointDetail($promo_code,$member_no);
// 			}
			
// 		}//func
		
		function promoactpointheadAction(){
			$this->_helper->layout()->disableLayout();
			$objMember=new Model_Member();
			$this->view->arr_promo=$objMember->getPromoActPointHead();			
		}//func
		
		function promoactpointdetailAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$objMember=new Model_Member();
				$this->view->arr_promo=$objMember->getPromoActPointDetail($promo_code,$member_no);
			}
		}//func
		
		function paymentactpointAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));
				$redeem_point=$filter->filter($this->getRequest()->getPost('redeem_point'));
				$amount=$filter->filter($this->getRequest()->getPost('amount'));
				$cashier_id=$filter->filter($this->getRequest()->getPost('cashier_id'));
				$name=$filter->filter($this->getRequest()->getPost('name'));
				$address=$filter->filter($this->getRequest()->getPost('address'));
				$objMember=new Model_Member();
				$result=$objMember->paymentActPoint($member_no,$promo_code,$redeem_point,$amount,$cashier_id,$name,$address);
				$this->view->result=$result;
				//*WR18032013
				$arr_res=explode('#',$result);
				if($arr_res[0]=='1'){
					$doc_no=$arr_res[1];
					//$objPos=new Model_PosGlobal();
					//$result=$objPos->initMarkPoint($doc_no); //WR04112013 cut off mark point of axe
					$objCsh=new Model_Cashier();
					$result=$objCsh->up2jinet($doc_no);
				}
			}
		}//func
		
		function chkamtproAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$giftset_amount=$filter->filter($this->getRequest()->getPost('giftset_amount'));
				$application_id=$filter->filter($this->getRequest()->getPost('application_id'));				
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$start_baht=$filter->filter($this->getRequest()->getPost('start_baht'));
				$end_baht=$filter->filter($this->getRequest()->getPost('end_baht'));
				$buy_type=$filter->filter($this->getRequest()->getPost('buy_type'));
				$buy_status=$filter->filter($this->getRequest()->getPost('buy_status'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));		
				$objMember=new Model_Member();
				$result=$objMember->chkAmtPro($promo_code,$start_baht,$end_baht,$buy_type,$buy_status,$product_id,$quantity);
				echo $result;
			}
		}//func
		
		function chkamtprobalancecouponAction(){
			/**
			 * @desc ตรวจสอบยอด Goss สำหรับโปร coupon
			 * @return
			 * @modify 22072014
			 * @last modify 04082014
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));								
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));	
				$objMember=new Model_Member();
				$result=$objMember->chkAmtProBalanceCoupon($promo_code,$product_id,$quantity);
				echo $result;
			}
		}//func
		
		function chkamtprobalanceAction(){
			/**
			 * @desc ตรวจสอบยอด Goss สำหรับโปร new birth 2014
			 * @return
			 * @modify 20122013
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));								
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));	
				$objMember=new Model_Member();
				$result=$objMember->chkAmtProBalance($promo_code,$product_id,$quantity);
				echo $result;
			}
		}//func
		
		function chkamtprosmsAction(){
			/**
			 * @desc ตรวจสอบยอด Goss สำหรับโปร SMS ที่มี promo_amt > 0
			 * @return
			 * @modify 23012013
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));								
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));	
				$promo_amt=$filter->filter($this->getRequest()->getPost('promo_amt'));
				$discount=$filter->filter($this->getRequest()->getPost('discount'));			
				$type_discount=$filter->filter($this->getRequest()->getPost('type_discount'));		
				$objMember=new Model_Member();
				$result=$objMember->chkAmtProSMS($promo_code,$product_id,$quantity,$promo_amt,$discount,$type_discount);
				echo $result;
			}
		}//func
		
		function initrwdtempAction(){
			/*
			 * @desc
			 * @param
			 * @return
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objRwd=new Model_Member();
			$objRwd->initRwdTemp();
		}//func
		
		function rwdtempAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$actions=$filter->filter($this->getRequest()->getPost('action'));
			if($actions=='gettmp'){
				$page=$filter->filter($this->getRequest()->getPost('page'));
				$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
				$query=$filter->filter($this->getRequest()->getPost('query'));	
				$rp=$filter->filter($this->getRequest()->getPost('rp'));
				$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
				$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
				$objMember=new Model_Member();
				$arr_json=$objMember->getRwdTemp($page,$qtype,$query,$rp,$sortname,$sortorder);	
				if(empty($arr_json)){
					$arr_json=array(
							    "page"=>1,
							    "total"=>0,
							    "rows"=>array()
							);
				}
				echo json_encode($arr_json);
			}
		}//func
		
		function getpagesAction(){			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter=new Zend_Filter_StripTags();
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$objTr=new Model_Member();
			$cpage=$objTr->getPageTotal($rp);
			echo $cpage;
		}//func		
		
		function subtotalrwdAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isGet()) {	
				$objRwd=new Model_Member();
				$json=$objRwd->getSumRwdTemp();
				echo $json;				
			}
		}//func
		
		function delrwdAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$items=$filter->filter($this->getRequest()->getPost('items'));
				$objRwd=new Model_Member();
				$result=$objRwd->delRwd($items);
				echo $result;
			}
		}//func
		
		function rwdAction(){
			
		}//func
		
		function rwdpromoheadAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$objMember=new Model_Member();
				$this->view->arr_promo=$objMember->getRwdPromoHead($status_no);
			}
			
		}//func
		
		function chkrwdcompleteAction(){function clsopsdayAction(){
			/**
			 * @desc modify:30042013
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objCatalog=new Model_Member();
			$objCatalog->clsOpsNewCard();			
		}//func
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objMember=new Model_Member();
			$str_chkqty=$objMember->chkRwdComplete();
			echo $str_chkqty;
		}//func
		
		function chkrwdproductAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				if($actions=='chkrwdproduct'){
					$objMember=new Model_Member();
					$result=$objMember->chkRwdProduct($promo_code,$product_id);
					echo $result;
				}
			}
		}//func
		
		function rwdproductAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				if($actions=='rwdproduct'){
					$objMember=new Model_Member();
					$this->view->arr_product=$objMember->getRwdProduct($promo_code);
				}
			}
		}//func
		
		function addproductitemAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$fix_quantity=$filter->filter($this->getRequest()->getPost('fix_quantity'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$amount=$filter->filter($this->getRequest()->getPost('amount'));
				if($actions=='rwdproductitem'){
					$objMember=new Model_Member();
					$result=$objMember->addProductItem($promo_code,$product_id,$fix_quantity,$quantity,$status_no,$amount);
					echo $result;
				}
			}
		}//func
		
		function addproductsetAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$promo_code=$filter->filter($this->getRequest()->getPost('promo_code'));
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));	
				$amount=$filter->filter($this->getRequest()->getPost('amount'));			
				if($actions=='rwdproductset'){
					$objMember=new Model_Member();
					$result=$objMember->addProductSet($promo_code,$quantity,$status_no,$amount);
					echo $result;
				}
			}
		}//func
		
		function listcardAction(){
		}//func
		
		function cardlostAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function memberprofileAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if($this->_request->isGET()){
				$actions=$filter->filter($this->getRequest()->getParam('action'));
				$col=$filter->filter($this->getRequest()->getParam('col'));
				$txtsearch=$filter->filter($this->getRequest()->getParam('txtsearch'));
				if($actions=='memberprofile'){
					$objPos=new Model_PosGlobal();
					$json=$objPos->getMemberProfile($col,$txtsearch);
					$arr_data=array(
						'action'=>'memberprofile',
						'data'=>$json
					);
					$this->view->arr_data=$arr_data;
				}
			}
		}//func
		
		function chknewcardlostAction(){
			/**
			 * @desc new create for support card lost
			 * @create : 17112015
			 * @modify : 
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$member_id=$filter->filter($this->getRequest()->getPost('member_id'));
				$ops_day=$filter->filter($this->getRequest()->getPost('ops_day'));
				if($actions=='cardlost'){
					$objMember=new Model_Member();
					$result=$objMember->chkNewCardLost($member_id,'L',$ops_day);
					 echo $result;
				}
			}
		}//func
		
		function checknewcardAction(){
			/**
			 * @desc modify : 03042014
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$member_id=$filter->filter($this->getRequest()->getPost('member_id'));
				$ops_day=$filter->filter($this->getRequest()->getPost('ops_day'));
				if($actions=='cardlost'){
					$objMember=new Model_Member();
					$result=$objMember->chkCardRegister($member_id,'L',$ops_day);
					$this->view->result=$result;
				}
			}
		}//func
		
		function delregcardAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				$items=$filter->filter($this->getRequest()->getPost('items'));
				$objMember=new Model_Member();
				$result=$objMember->delRegCard($items);
				($result)?$result=1:$result=0;
				$this->view->result=$result;
			}
		}//func
		
		function regnewcardAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$member_id=$filter->filter($this->getRequest()->getPost('member_id'));
				$card_type=$filter->filter($this->getRequest()->getPost('card_type'));
				$objMember=new Model_Member();
				$result=$objMember->regNewCard($member_id,$card_type);
				$this->view->result=$result;
			}
		}//func
		
		function getpageregcardAction(){			
			$this->_helper->layout->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$rp=$filter->filter($this->getRequest()->getPost('rp'));
			$tblname=$filter->filter($this->getRequest()->getPost('tblname'));
			$card_type=$filter->filter($this->getRequest()->getPost('card_type'));
			$objPos=new Model_PosGlobal();
			$where=" AND apply_date='0000-00-00'";
			$where=" AND card_type='$card_type'";
			$cpage=$objPos->flgTotalPage($tblname,$rp,$where);
			$this->view->cpage=$cpage;
		}//func		
		
		function regcardAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
					$page=$filter->filter($this->getRequest()->getPost('page'));
					$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
					$query=$filter->filter($this->getRequest()->getPost('query'));	
					$rp=$filter->filter($this->getRequest()->getPost('rp'));
					$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
					$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
					$objMember=new Model_Member();
					$arr_json=$objMember->getRegCard($page,$qtype,$query,$rp,$sortname,$sortorder);
					$this->view->arr_json=$arr_json;
			}
			
		}//func
		
		function commemberexpireAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('actions'));
				$refer_member_st=$filter->filter($this->getRequest()->getPost('refer_member_st'));	
				$objMember=new Model_Member();
				$str_json=$objMember->comMemberExpire($refer_member_st,$actions);
				echo $str_json;
			}
		}//func
		
		function newmemberpromotionAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
					$application_id=$filter->filter($this->getRequest()->getPost('application_id'));
					$objMember=new Model_Member();
					$this->view->arr_promo=$objMember->getPromoNewMember($application_id);
			}
		}//func
		
		function regmemberpromotionAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
					$refer_member_st=$filter->filter($this->getRequest()->getPost('refer_member_st'));
					$objMember=new Model_Member();					
					$this->view->arr_promo=$objMember->getPromoNewCard($refer_member_st);					
			}
		}//func
		
		function currpointAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$refer_member_st=$filter->filter($this->getRequest()->getPost('refer_member_st'));	
				$objMember=new Model_PosGlobal();
				$cpoint=$objMember->getPointOfDay($refer_member_st);
				$this->view->cpoint=$cpoint;
			}			
		}//func
		
		function catalogAction(){
			$this->_helper->layout()->disableLayout();
			$objCatalog=new Model_Member();
			$arr_catalog=$objCatalog->getCatalog();
			$this->view->arr_catalog=$arr_catalog;
		}//func
		
		function cataloglistAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$application_id = $filter->filter($this->getRequest()->getParam("application_id"));
			$product_no = $filter->filter($this->getRequest()->getParam("product_no"));
			$product_seq = $filter->filter($this->getRequest()->getParam("product_seq"));			
			$product_sub_seq = $filter->filter($this->getRequest()->getParam("product_sub_seq"));
			$pn = $filter->filter($this->getRequest()->getParam("pn"));
			$ps = $filter->filter($this->getRequest()->getParam("ps"));
			$objCatalog=new Model_Member();
			$arr_cataloglist=$objCatalog->getCatListTest($application_id,$product_no,$product_seq,$product_sub_seq,$pn,$ps);
			//$arr_cataloglist=$objCatalog->getCatList($application_id,$product_no,$product_seq,$product_sub_seq,$pn,$ps);
			$this->view->arr_cataloglist=$arr_cataloglist;
			
			//WR262042013
			$arr_ops=array();
			if($application_id=='OPPN300' || $application_id=='OPPS300'){
				$arr_ops=$objCatalog->getOpsNewCard();
			}
			$this->view->arr_ops=$arr_ops;		
				
		}//func
		
		function clsopsdayAction(){
			/**
			 * @desc modify:30042013
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$objCatalog=new Model_Member();
			$objCatalog->clsOpsNewCard();			
		}//func
		
		function checkmemberAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {
				$member_no=$filter->filter($this->getRequest()->getPost('member_no'));	
				$ops_day_old=$filter->filter($this->getRequest()->getPost('ops_day_old'));	
				$ops_day_new=$filter->filter($this->getRequest()->getPost('ops_day_new'));	
				$objMember=new Model_Member();
				$result=$objMember->checkMemberExist($member_no,$ops_day_old,$ops_day_new);
				$this->view->resmember=$result;
			}
		}//func
		
		function setprobalanceAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {
				$application_id=$filter->filter($this->getRequest()->getPost('application_id'));		
				$free_product_amount=$filter->filter($this->getRequest()->getPost('free_product_amount'));
				$product_amount_type=$filter->filter($this->getRequest()->getPost('product_amount_type'));
				$objMember=new Model_Member();
				$result=$objMember->setProBalance($application_id,$free_product_amount,$product_amount_type);
				echo $result;
			}
		}//func
		
		function productfreepremiumAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {
				$free_premium_amount=$filter->filter($this->getRequest()->getPost('free_premium_amount'));	
				$premium_amount_type=$filter->filter($this->getRequest()->getPost('premium_amount_type'));
				$application_id=$filter->filter($this->getRequest()->getPost('application_id'));		
				$product_id=$filter->filter($this->getRequest()->getPost('product_id'));
				$quantity=$filter->filter($this->getRequest()->getPost('quantity'));
				$objMember=new Model_Member();
				$result=$objMember->checkFreePremium($application_id,$product_id,$quantity,$free_premium_amount,$premium_amount_type);
				echo $result;
			}
		}//func
		
		function setcshtempAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();			
			if ($this->_request->isPost()) {
				$application_id= $filter->filter($this->getRequest()->getPost('application_id'));
				$promo_st=$filter->filter($this->getRequest()->getPost('promo_st'));
				$employee_id= $filter->filter($this->getRequest()->getPost('employee_id'));	
				$member_no= $filter->filter($this->getRequest()->getPost('member_no'));
				$product_id= $filter->filter($this->getRequest()->getPost('product_id'));
				$quantity= $filter->filter($this->getRequest()->getPost('quantity'));	
				$price= $filter->filter($this->getRequest()->getPost('price'));
				$doc_tp= $filter->filter($this->getRequest()->getPost('doc_tp'));
				$status_no= $filter->filter($this->getRequest()->getPost('status_no'));				
				$objMember=new Model_Member();
				$result=$objMember->setCshTemp($application_id,$promo_st,$employee_id,$member_no,$product_id,$quantity,$price,$doc_tp,$status_no);
				//for ชุดสมัคร
				$amt=$objMember->getAmount();
				$this->view->result=$result;
				$this->view->amt=$amt;
			}
		}//action
		
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
				$net_amount=$filter->filter($this->getRequest()->getPost('net_amount'));				
				$ex_vat_amt=$filter->filter($this->getRequest()->getPost('ex_vat_amt'));
				$ex_vat_net=$filter->filter($this->getRequest()->getPost('ex_vat_net'));
				$vat=$filter->filter($this->getRequest()->getPost('vat'));
				$paid=$filter->filter($this->getRequest()->getPost('paid'));
				$pay_cash=$filter->filter($this->getRequest()->getPost('pay_cash'));
				$pay_credit=$filter->filter($this->getRequest()->getPost('pay_credit'));
				$redeem_point=$filter->filter($this->getRequest()->getPost('redeem_point'));
				$change=$filter->filter($this->getRequest()->getPost('change'));
				$coupon_code=$filter->filter($this->getRequest()->getPost('coupon_code'));
				$pay_cash_coupon=$filter->filter($this->getRequest()->getPost('pay_cash_coupon'));
				$credit_no=$filter->filter($this->getRequest()->getPost('credit_no'));
				$credit_tp=$filter->filter($this->getRequest()->getPost('credit_tp'));
				$bank_tp=$filter->filter($this->getRequest()->getPost('bank_tp'));
				$name=$filter->filter($this->getRequest()->getPost('name'));
				$address1=$filter->filter($this->getRequest()->getPost('address1'));
				$address2=$filter->filter($this->getRequest()->getPost('address2'));
				$address3=$filter->filter($this->getRequest()->getPost('address3'));
				$application_id=$filter->filter($this->getRequest()->getPost('application_id'));	
				$refer_member_id=$filter->filter($this->getRequest()->getPost('refer_member_id'));
				$expire_date=$filter->filter($this->getRequest()->getPost('expire_date'));
				$point_begin=$filter->filter($this->getRequest()->getPost('point_begin'));//WR25122013
				$point_receive=$filter->filter($this->getRequest()->getPost('point_receive'));
				$point_used=$filter->filter($this->getRequest()->getPost('point_used'));
				$point_net=$filter->filter($this->getRequest()->getPost('point_net'));
				$card_status=$filter->filter($this->getRequest()->getPost('card_status'));	
				$get_point=$filter->filter($this->getRequest()->getPost('get_point'));
				$xpoint=$filter->filter($this->getRequest()->getPost('xpoint'));			
				$remark1=$filter->filter($this->getRequest()->getPost('remark1'));
				$remark2=$filter->filter($this->getRequest()->getPost('remark2'));
				//*WR25052015
				$special_day=$filter->filter($this->getRequest()->getPost('special_day'));		
				//*WR08052015
				$idcard=$filter->filter($this->getRequest()->getPost('idcard'));	
				$mobile_no=$filter->filter($this->getRequest()->getPost('mobile_no'));		
				//*WR10032015
				$bill_manual_no=$filter->filter($this->getRequest()->getPost('bill_manual_no'));
				$ticket_no=$filter->filter($this->getRequest()->getPost('ticket_no'));
				$objPay=new Model_Member();
				$result=$objPay->payment(
											$user_id,
											$cashier_id,
											$saleman_id,
											$doc_tp,
											$status_no,
											$member_no,
											$member_percent,
											$net_amount,
											$ex_vat_amt,
											$ex_vat_net,
											$vat,
											$paid,
											$pay_cash,
											$pay_credit,
											$redeem_point,
											$change,
											$coupon_code,									
											$pay_cash_coupon,
											$credit_no,
											$credit_tp,
											$bank_tp,
											$name,
											$address1,
											$address2,
											$address3,
											$application_id,
											$refer_member_id,
											$expire_date,
											$point_begin,
											$point_receive,
											$point_used,
											$point_net,
											$card_status,
											$get_point,
											$xpoint,
											$remark1,$remark2,$special_day,$idcard,$mobile_no,$bill_manual_no,$ticket_no);	
				$this->view->result=$result;
			}
		}//func		
	}//class
?>