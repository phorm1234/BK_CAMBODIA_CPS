<?php
	class AccessoryController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
		}//func
		function preDispatch()
		{				
			$this->_helper->layout()->setLayout('default_layout');			
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if(!isset($empprofile)){
				$this->_redirect('/error/sessionexpire');
				exit();
			}
			
			$objPos=new Model_PosGlobal();
			$doc_date=$objPos->checkDocDate();
			if(!$doc_date){
				$this->_redirect('/error/billopen');
				exit();
			}
			$arr_docdate=explode("-",$doc_date);
			$this->view->doc_date=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
			$this->view->session_employee_id=$empprofile['employee_id'];		
			$this->CHECK_SESSION=$empprofile['employee_id'];
			$this->view->lock_status=$empprofile['lock_status'];
		}//func
		
		function formauditcanceldocAction(){
			/**
			 * @desc cancel doc AI,AO
			 * @create : 18032015
			 * 
			 */
			$this->_helper->layout()->disableLayout();
		}//func
		
		function reupddataAction(){
			/**
			 * @desc 230 215
			 * */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){	
				$objData=new Model_Accessory();
				$res_upd=$objData->reUpdData();
			}
			return false;
		}//func
		
		function indexAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		
		function chkusedgreenbagAction(){
			/**
			 * @desc WR12062014
			 * @return
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$objAcc=new Model_Accessory();
				$res_chk=$objAcc->chkUsedGreenBag(); 
				echo $res_chk;
			}
		}//func
		
		function usedbagtotempAction(){
			/**
			 * @desc modify : 05062014
			 */
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$str_greenbag=$filter->filter($this->getRequest()->getPost('str_greenbag'));
				$member_id=$filter->filter($this->getRequest()->getPost('member_no'));				
				$arr_greenbag=explode('#',$str_greenbag);
				for($i=0;$i<count($arr_greenbag);$i++){
					if($arr_greenbag[$i]!=''){
						$objCalPro=new Model_Calpromotion();
						$res_chk=$objCalPro->promotion_product_privacy($arr_greenbag[$i],$member_id); 
						//echo $arr_greenbag[$i]."=>".$member_id." = ".$res_chk."#";
						if($res_chk=='Y'){
							$objAcc=new Model_Accessory();
							$res_chk=$objAcc->usedBagToTemp($member_id,$arr_greenbag[$i]);
						}else{
							//*WR25062014 update 16062014 problem
							$bag_barcode=$arr_greenbag[$i];
							$url="http://10.100.53.2/wservice/possupport/cmd/upd_bag_barcode.php?member_no=$member_id&bag_barcode=$bag_barcode";
							$fp=@fopen($url, "r");
							$res_upd=@fgetss($fp, 4096);
							if($res_upd=='Y'){
								$objAcc=new Model_Accessory();
								$res_chk=$objAcc->usedBagToTemp($member_id,$arr_greenbag[$i]);	
							}						
						}
						echo $res_chk;
					}
				}				
			}
		}//func
		
		function updbagbarcodeAction(){
			/**
			 * @desc : 22052014
			 */
			
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$bagbarcode=$filter->filter($this->getRequest()->getPost('bagbarcode'));
				$objAcc=new Model_Accessory();
				$objAcc->updBagBarcode($bagbarcode);
			}
		}//func
				
		function getformbagbarcodeAction(){
			/**
			 * @desc : 22052014
			 */
			$this->_helper->layout()->disableLayout();
			$objAcc=new Model_Accessory();
			$this->view->arr_chkbag=$objAcc->getFormBagBarcode();
		}//func
		
		function formsetbagAction(){
			/**
			 * @desc : 25092013
			 */
			$this->_helper->layout()->disableLayout();
			$objAcc=new Model_Accessory();
			$arr_bag=$objAcc->getBag();
			$this->view->arr_bag=$arr_bag;
		}//func
		
		function checkaccessoryAction(){
			$this->_helper->layout()->setLayout('blank_layout');
		}//func
		
		function setbagtotempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$status_no=$filter->filter($this->getRequest()->getPost('status_no'));
				$str_items=$filter->filter($this->getRequest()->getPost('items'));
				$objAcc=new Model_Accessory();
				$objAcc->setBagToTemp($str_items,$status_no);
				echo "Y";
			}
		}//func
		
//		function setbagtotempAction(){
//			$this->_helper->layout()->disableLayout();
//			$this->_helper->viewRenderer->setNoRender(TRUE);
//			Zend_Loader::loadClass('Zend_Filter_StripTags');
//			$filter = new Zend_Filter_StripTags();		
//			if ($this->_request->isPost()){
//				$str_items=$filter->filter($this->getRequest()->getPost('items'));
//				$objAcc=new Model_Accessory();
//				$objAcc->setBagToTemp($str_items);
//				echo "Y";
//			}
//		}//func
		
		function getipaccessoryAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$objAcc=new Model_Accessory();
				$res=$objAcc->getIPAccessory();
				echo json_encode($res);				
			}
		}//func
		
		function pingaccAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$ip=$filter->filter($this->getRequest()->getPost('ip'));
				$objAcc=new Model_Accessory();
				$res=$objAcc->pingServerStatus($ip);
				echo $res;				
			}
		}//func
		
		function transferbeforecheckstockAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		
		function transferaftercheckstockAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		
		function chkrosaromAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$n_cancel=$filter->filter($this->getRequest()->getPost('n_cancel'));
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$cpassword=$filter->filter($this->getRequest()->getPost('cpassword'));
				$objAcc=new Model_Accessory();
				$res_status=$objAcc->chkRosARom($doc_no,$n_cancel,$cpassword);
				echo $res_status;
			}
		}//func
		
		function countcancelAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$doc_tp=$filter->filter($this->getRequest()->getPost('doc_tp'));
				$objAcc=new Model_Accessory();
				$n_cancel=$objAcc->countCancel($doc_tp);
				echo $n_cancel;
			}
		}//func
		
		function transfercheckstockAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();		
			if ($this->_request->isPost()){
				$acts=$filter->filter($this->getRequest()->getPost('act'));
				if($acts=='beforecheck'){
					$objAcc=new Model_Accessory();
					$result=$objAcc->beforeTransferToCheckStock();
					echo $result;
				}else if($acts=='aftercheck'){
					$objAcc=new Model_Accessory();
					$result=$objAcc->afterTransferToCheckStock();
					echo $result;
				}
			}
		}//func
		
		function formcanceldocAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function formcanceldoc19Action(){
			$this->_helper->layout()->disableLayout();
		}//func
		
		function formquestionAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		function canceldocAction(){
			$this->_helper->layout()->setLayout('default_layout');
			$filter=new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doctp"));
			$this->view->doc_tp=$doc_tp;
		}//func
		function questiondocAction(){
			$this->_helper->layout()->setLayout('default_layout');
			$filter=new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doctp"));
			$this->view->doc_tp=$doc_tp;
		}//func
		function autodocAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$term = $filter->filter($this->getRequest()->getParam("term"));
			$str_doctp=$filter->filter($this->getRequest()->getParam("str_doctp"));
			$objAuto=new Model_PosGlobal();
			$this->view->arr_doc=$objAuto->getDocAutoComplete($term,$str_doctp);
		}//func
		function autodocformAction(){
			$this->_helper->layout()->disableLayout();
		}//func
		
	
		function chkdocdatecancelAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);			
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){				
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$doc_tp=$filter->filter($this->getRequest()->getPost('doc_tp'));
				$objAcc=new Model_Accessory();
				$res_chk=$objAcc->chkDocDateCancel($doc_no,$doc_tp);
				echo $res_chk;
			}
		}//func
		
				
		function canceldocnoAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);			
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$doc_tp=$filter->filter($this->getRequest()->getPost('doc_tp'));
				$employee_id=$filter->filter($this->getRequest()->getPost('employee_id'));				
				$aromros_id=$filter->filter($this->getRequest()->getPost('aromros_id'));
				$cancel_type=$filter->filter($this->getRequest()->getPost('cancel_type'));
				$cancel_description=$filter->filter($this->getRequest()->getPost('cancel_description'));				
				$objAcc=new Model_Accessory();
				switch ($doc_tp)
			    {
			    case ($doc_tp=='SL' || $doc_tp=='VT' || $doc_tp=='DN'):
			      $result=$objAcc->cancelDocNo($doc_no,$employee_id);
			       //set flag='1'  at 10.100.53.52 ??????????????????????????????????????????????????????????????????
			      $objPos=new Model_PosGlobal();
			      $objPos->chkForCancelMemPrivilage($doc_no);
			      //set flag='C' at 10.100.53.52
			      $arr_result=explode('#',$result);
			      if($arr_result[0]=='Y'){
				      	//*WR23012014 cancle bill for joke
						$objCal=new Model_Calpromotion();
						$objCal->up_cancle_bill($doc_no);
						$objPos->markCancelBill($doc_no);
						//*WR20012015 line and mobile coupon application 
					   $objAcc->unLockMobileCoupon($doc_no);
			      }
//			      //*WR 08082014 mobile coupon application
//				   $objM=new Model_Member();
//				   $objM->unLockMobileCoupon($doc_no);

			       break;
			     case ($doc_tp=='TO'):
			       $result=$objAcc->cancelDocTO($doc_no,$employee_id);
			        break;
			     case ($doc_tp=='AO'):
			       $result=$objAcc->cancelDocAO($doc_no,$employee_id);
			        break;
			     case ($doc_tp=='AI'):
			      $result=$objAcc->cancelDocAI($doc_no,$employee_id);
			        break;
			     case ($doc_tp=='CN'):
			       $result=$objAcc->cancelDocCN($doc_no,$employee_id);
			       $arr_result=explode('#',$result);
			    	if($arr_result[0]=='Y'){
			    		//*WR28012014
			    		$objCal=new Model_Calpromotion();
						$objCal->up_cancle_bill($doc_no);
//			    		$objPos=new Model_PosGlobal();
//				      	$objPos->cancelPoint($doc_no);
//				      	$objPos->markCancelBill($doc_no);
			       }
			        break;
			      case ($doc_tp=='OR'):
			       $result=$objAcc->cancelDocOR($doc_no,$employee_id);
			        break;
			     case ($doc_tp=='RQ'):
			       $result=$objAcc->cancelDocRQ($doc_no,$employee_id);
			        break;
			      case ($doc_tp=='IQ'):
			       $result=$objAcc->cancelDocIQ($doc_no,$employee_id);
			        break;
			    } 				
				$chk_result=explode('#',$result);				
				if($chk_result[0]=='Y'){
					$objAcc->markCancelDoc($doc_no,$employee_id,$aromros_id,$cancel_type,$cancel_description);
				}
				echo $result;
			}
		}//func
				
		function checkdocnoAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
				$flg_cancel=$filter->filter($this->getRequest()->getPost('flg_cancel'));
				$objPos=new Model_PosGlobal();
				$result=$objPos->checkDocNoExist($doc_no,$flg_cancel);
				echo $result;
			}
		}//func		
		
		function listdocnoAction(){
			$this->_helper->layout->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_tp_cancel=$filter->filter($this->getRequest()->getPost('doc_tp_cancel'));
				$start_date=$filter->filter($this->getRequest()->getPost('start_date'));
				$end_date=$filter->filter($this->getRequest()->getPost('end_date'));
				$actions=$filter->filter($this->getRequest()->getPost('actions'));
				$objAcc=new Model_Accessory();
				$this->view->arr_docno=$objAcc->getDocNo($doc_tp_cancel,$actions,$start_date,$end_date);
			}
		}//func
		
		function retempAction(){
			$this->_helper->layout()->disableLayout();
			$filter=new Zend_Filter_StripTags();
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_stop = $filter->filter($this->getRequest()->getParam("doc_no_stop"));
		     //echo "doc_start=>$doc_no_start    doc_stop=>$doc_no_stop";
			 if ($this->_request->isPost()){
				$actions=$filter->filter($this->getRequest()->getPost('action'));
				if($actions=='gettmp'){
					$page=$filter->filter($this->getRequest()->getPost('page'));
					$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
					$query=$filter->filter($this->getRequest()->getPost('query'));	
					$rp=$filter->filter($this->getRequest()->getPost('rp'));
					$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
					$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
					
					//$doc_no_start=$filter->filter($this->getRequest()->getPost('doc_no_start'));
					//$doc_no_stop=$filter->filter($this->getRequest()->getPost('doc_no_stop'));
					//echo "doc_no_start=>$doc_no_start,doc_no_stop=>$doc_no_stop<br><br>";
				}
		    }
		    $objAcc=new Model_Accessory();
			$arr_json=$objAcc->getReTemp($page,$qtype,$query,$rp,$sortname,$sortorder,$doc_no_start,$doc_no_stop);							
			$this->view->arr_json=$arr_json;
		}//func
		
	}//class 
?>