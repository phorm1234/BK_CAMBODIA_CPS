<?php 
	class OrderController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('order_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$templete="templete_".$brand;
			$user_id="";
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile; 
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
			$this->view->arr_layout=array(
						'brand'=>$templete,
						'shop'=>$shop,
						'comno'=>$comno,
						'user_id'=>$user_id
			);
			$this->view->templete=$templete;
		}
		
		public function indexAction(){
			$date=date("d/m/Y");
			$this->view->date=$date;
			$obj=new Model_Order();
			$view=$obj->getquotadetail();
			$this->view->data=$view;
		}

		////////Get Orders Fix Stock//////////
		public function fixorderAction(){
			$date=date("d/m/Y");
			$this->view->date=$date;
			$obj=new Model_Order();
			$view=$obj->getquotadetail();
			$this->view->data=$view;
		}

		public function getproductorderfixAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Order();
			$sum_inv=$obj->sumivn("trn_tdiary2_or");
			$this->view->sum_inv=$sum_inv;
		}

		public function getdataproductorderfixAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Order();
			$view=$obj->getdataproduct($sort);
			//print_r($view);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
		}

		////////////////////////////////////

		public function orderspAction(){
			$this->_helper->layout()->setLayout('order_layoutsp');
			$date=date("d/m/Y");
			$this->view->date=$date;
			$obj=new Model_Order();
			$view=$obj->getquotadetail();
			$this->view->data=$view;
		}	

		public function getproductorderAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Order();
			$sum_inv=$obj->sumivn("trn_tdiary2_or");
			$this->view->sum_inv=$sum_inv;
		}
		public function getproductorderspAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Order();
			$sum_inv=$obj->sumivn("trn_tdiary2_or");
			$this->view->sum_inv=$sum_inv;
		}
		
		public function getproductiqAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Order();
			$sum_inv=$obj->sumivn("trn_tdiary2_iq");
			$this->view->sum_inv=$sum_inv;
		}
		
		public function getdataproductorderAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Order();
			$view=$obj->getdataproduct($sort);
			//print_r($view);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
		}
		
		public function getdataproductiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Order();
			$view=$obj->getdataproduct_iq($sort);
			//print_r($view);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
		}
		
		public function checkproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$obj=new Model_Order();
			if($status_no=="71"){
				$view=$obj->checkproduct_tester($product_id);
				$this->view->chk_type="y";
			}else{
				$view=$obj->checkproduct($product_id);
			}
			$this->view->data=$view;
			$check_lock=$obj->checkproductlock($product_id);
			$check_quota=$obj->getquotadetail();
			$this->view->checkquota=$check_quota;
			$this->view->product_lock=$check_lock;
			$this->view->date=date("Y-m-d");
			$this->view->status_no=$status_no;
			$this->view->doc_tp=$doc_tp;
		}
		
		public function chkproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$obj=new Model_Order();
			$product_id=$obj->convproduct_id($product_id);
			if(empty($product_id[0]['product_id'])){
				$product_id[0]['product_id']="";
			}
			$view=$obj->checkproduct($product_id[0]['product_id']);
			$this->view->data=$view;
		}
		
		public function inserttdiaryorAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$obj=new Model_Order();
			$product_id = $obj->convproduct_id($filter->filter($this->getRequest()->getParam("product_id"))); 
			//print_R($product_id);
			//$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark")); 
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp")); 
			$qty = $filter->filter($this->getRequest()->getParam("qty")); 
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$avg = $filter->filter($this->getRequest()->getParam("avg"));
			$bal = $filter->filter($this->getRequest()->getParam("balance"));
			$fix = $filter->filter($this->getRequest()->getParam("fix"));

			$array_data=array(
				'doc_tp'=>$doc_tp,
				'status_no'=>$status_no,
				'product_id'=>$product_id[0]['product_id'],
				'qty'=>$qty,
				'doc_remark'=>$doc_remark,
				'avg'=>$avg,'bal'=>$bal,'fix'=>$fix
			);
			//print_r($array_data);
			
			$view=$obj->insert_tdiary_or($array_data);
			//print_r($view);
			
		}
		
		public function inserttdiaryiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id")); 
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark")); 
			$inv = $filter->filter($this->getRequest()->getParam("inv")); 
			$qty = $filter->filter($this->getRequest()->getParam("qty")); 
			$status_no = $filter->filter($this->getRequest()->getParam("doc_status"));
			$array_data=array(
				'inv'=>$inv,
				'doc_status'=>$status_no,
				'product_id'=>$product_id,
				'qty'=>$qty,
				'doc_remark'=>$doc_remark
			);
			$obj=new Model_Order();
			$view=$obj->insert_tdiary_iq($array_data);
			$this->view->data=$view;
			
		}
		
		public function deletetmplistAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$arr_seq=explode(',',$seq);
			array_pop($arr_seq);
			$obj=new Model_Order();
			$view=$obj->delettemlist($arr_seq,"trn_tdiary2_or");
			//print_r($view);
			$this->view->data=$view;	
		}
		
		public function deletetmplistiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$arr_seq=explode(',',$seq);
			array_pop($arr_seq);
			$obj=new Model_Order();
			$view=$obj->delettemlist($arr_seq,"trn_tdiary2_iq");
			//print_r($view);
			$this->view->data=$view;	
		}
		
		public function deleteordertempAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$obj=new Model_Order();
			$view=$obj->deleteordertemp($tbl);
			$this->view->data=$view;
		}
		
		public function tempor2diaryorAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			//$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$doc_tp = "OR";
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark"));
			$tbl_tdiray_or="trn_tdiary2_or";
			$obj=new Model_Order();
			if($status_no==71){
				$view=$obj->tranfertmpor2diaryortester($doc_tp,$status_no,$doc_remark);
			}else{
				$view=$obj->tranfertmpor2diaryor($doc_tp,$status_no,$doc_remark,$tbl_tdiray_or);
			}
			//echo $view;
			$this->view->data=$view;
		}
		
		public function tempor2diaryorextraAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			//$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$doc_tp = "OR";
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark"));
			$tbl_tdiray_or="trn_tdiary2_or";
			$obj=new Model_Order();
			if($status_no==71){
				$view=$obj->tranfertmpor2diaryortester($doc_tp,$status_no,$doc_remark);
			}else{
				$view=$obj->tranfertmpor2diaryorextra($doc_tp,$status_no,$doc_remark,$tbl_tdiray_or);
			}
			//echo $view;
			$this->view->data=$view;
		}

		public function tempor2diaryiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_tp = "IQ";
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark"));
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$obj=new Model_Order();
			$view=$obj->tranfertmpor2diaryiq($doc_tp,$status_no,$doc_remark,$inv);
			$this->view->data=$view;
			
		}
		
		public function getquotaAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Order();
			$view=$obj->getquotadetail();
			$this->view->data=$view;
		}
		
		public function gettypeproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$obj=new Model_Order();
			$view=$obj->gettypeproduct($doc_tp);
			$this->view->data=$view;
		}
		
		public function checkdocnodiaryorAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Order();
			$view=$obj->checkdocnodiaryor();
			$this->view->data=$view;
			
		}
		
		public function iqformAction(){
			$date=date("d/m/Y");
			$this->view->date=$date;
			$obj=new Model_Order();
			$arr_doc_no=$obj->gettypeproduct('IQ');
			$this->view->arr_doc_no=$arr_doc_no;
		}
		
		public function printiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Order();
			$detail=$obj->getdocnodetail($doc_no);
			$this->view->data_detail=$detail;
			$head=$obj->getdocnohead($doc_no);
			$this->view->doc_no=$head;
			
		}
		//=========================== RT Tester =============================================
		public function rttesterAction(){
			$doc_no = new Model_Tester();
			$this->view->doc_no =$doc_no->listDocno();
			$arr =$doc_no->check_quota();
			//print_r($arr);
			$this->view->quota =$arr['quota'];
			$this->view->balance =$arr['balance'];
			$this->view->doc_date = $doc_no->doc_date();
		}
		
		
		
		public function checkproductidAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest ();
			$product_id = $request->getParam("product_id");
			$doc_no		= $request->getParam("doc_no");
			if($product_id != ""){
				$tmp = new Model_Tester();
				$res = $tmp->check_product($product_id,$doc_no);
				if($res == '1'){
					echo "y";
				}else if($res == '2'){
					echo "d";
				}else{
					echo "n";
				}
			}else{
				echo "n";
			}
		}
		
		public function checkproductmasAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest ();
			$product_id = $request->getParam("product_id");
			if($product_id != ""){
				$tmp = new Model_Tester();
				$res = $tmp->check_productmas($product_id);
				echo json_encode($res);

			}else{
				echo json_encode($res);
			}
		}
		
		public function checkqtyAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest ();
			$product_id = $request->getParam("product_id");
			$qty 		= $request->getParam("qty");
			if($product_id != "" && $qty != ""){
				$tmp = new Model_Tester();
				$res = $tmp->check_qty($product_id, $qty);
				if($res == '1'){
					echo "y";
				}else if($res=='0'){
					echo "n";
				}else if($res=='2'){
					echo 'f';
				}
			}else{
				// สินค้าไม่พอ
				echo "n";
			}
		}
		
		public function checkstatusrtAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest();
			$status = $request->getParam("checkstatus");
			$status_trans = $request->getParam("str_status");
			//echo "status = ".$status;
			$tmp = new Model_Tester();
			$str = $tmp->checkstatus($status,$status_trans);
			echo $str;
		}
		
		public function savertAction(){
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			//print_r($empprofile);
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$data = array();
 			$request = $this->getRequest();
// 			$doc_date 		= $request->getParam("doc_date");
// 			$product_id		= $request->getParam("product_id");
// 			$qty			= $request->getParam("qty");
// 			$order_date		= $request->getParam("last_date");
			
			$data['corporation_id'] = $empprofile['corporation_id'];
			$data['company_id']		= $empprofile['company_id'];
			$data['branch_id']		= $empprofile['branch_id'];
			$data['doc_date']		= "";
			$data['doc_time']		= date('H:i:s');
			$data['doc_tp']			="RT";
			$data['status_no']		="25";
			$data['seq']			="";
			$data['product_id']		= $request->getParam("product_id");
			$data['price']			="";
			$data['quantity']		=$request->getParam("qty");
			$data['stock_st']		='-1';
			$data['point1']			=$request->getParam("sum");
			$data['doc_no']			=$request->getParam("doc_no");
			$data['amount']			="";
			$data['net_amt']		="";
			$data['reg_date']		=date('Y-m-d');
			$data['reg_time']		=date('H:i:s');
			$data['reg_user']		=$empprofile['user_id'];
			$data['upd_date']		=date('Y-m-d');
			$data['upd_time']		=date('H:i:s');
			$data['upd_user']		=$empprofile['user_id'];
			$tmp = new Model_Tester();
			$sa = $tmp->savedata($data,$request->getParam("quota"));

			//print_r($data);
		}
		
		public function rtlistAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Tester();
			
			$doc_no = $filter->filter($this->getRequest()->getParam("query"));
			$view=$obj->getdataproduct($page,$doc_no);
			//echo "doc_no=".$doc_no;
			//print_r($view);
			echo json_encode($view);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
		}
		
		public function showamountAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Tester();
			$doc_no = $this->getRequest()->getParam("checkstatus");
			$res = $obj->showamount($doc_no);
			echo json_encode($res);
		}
		
		public function savediaryAction(){
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$data = array();
			$request = $this->getRequest();
			if($request->getParam("checkstatus")){
				$obj = new Model_Tester();
				$data['amount']					=$request->getParam("am");
				$data['net_amt']				=$request->getParam("am");
				$data['quantity']				=$request->getParam('qt');
				$data['remark1']				=$request->getParam("remark");
				$data['branch_id'] 				=$empprofile['branch_id'];
				$data['corporation_id']			=$empprofile['corporation_id'];
				$data['company_id']				=$empprofile['company_id'];
				$data['reg_user']				=$empprofile['user_id'];
				$data['upd_user']				=$empprofile['user_id'];
				$res = $obj->savediary1($data,$request->getParam("amt"),$request->getParam("doc_no"));
			}
		}
		
		public function deletediarty2rtAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest();
			$wherein = $request->getParam("seq");
			$obj = new Model_Tester();
			$obj->deldiary2($wherein);
		}
		
		public function trndiary2detailAction(){
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest();
			$doc_no = $request->getParam("doc_no");
			$this->view->tdoc_no = $doc_no;
		}
		
		public function canceldiaryAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Tester();
			$obj->canceldiary2($this->getRequest()->getParam("doc_no"));
		}
		
		public function docnumberAction(){
			$str = "";
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Tester();
			$str .='<select id="doc_status" class="NFText_hidden" onchange="gendocto();" name="doc_status">';
			$str .=$obj->listDocno();
		    $str .='</select>';
		    echo $str;
		}
		
		//=========================== RT Tester =============================================
		public function totesterAction(){
			$doc_no = new Model_Totester();
			$this->view->doc_no =$doc_no->listDocno();
			$arr =$doc_no->check_quota();
			//print_r($arr);
			$this->view->quota =$arr['quota'];
			$this->view->balance =$arr['balance'];
			$this->view->doc_date = $doc_no->doc_date();
		}	
		
		public function otlistAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Totester();
				
			$doc_no = $filter->filter($this->getRequest()->getParam("query"));
			$view=$obj->getdataproduct($page,$doc_no);
			//echo "doc_no=".$doc_no;
			//print_r($view);
			echo json_encode($view);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
		}
		
		public function showamounttoAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Totester();
			$doc_no = $this->getRequest()->getParam("checkstatus");
			$res = $obj->showamount($doc_no);
			echo json_encode($res);
		}
		
		public function checkapproveAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Totester();
			$ap = $this->getRequest()->getParam('approve');
			$status = $this->getRequest()->getParam('status');
			$tmp  =$obj->approve($ap, $status);
			echo $tmp;
		}
		
		public function save2diaryrqAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Totester();
			$doc_no = $this->getRequest()->getParam("doc_no");
			if($doc_no != ""){
				$sess_ros = new Zend_Session_Namespace('rosprofile');
				$tmp = $obj->movetodiary1rq($doc_no,$sess_ros->rospass);
				echo $tmp;
			}
		}
		
		public function canceltoAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj = new Model_Totester();
			$tmp = $obj->cancel_to($this->getRequest()->getParam("doc_no"));
			
		}
		
		public function otcheckcpassAction(){
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$cpass = $this->getRequest()->getParam("chk_cpass");
			$obj = new Model_Totester();
			$tmp = $obj->check_cpass($empprofile['user_id'], $cpass);
			$sess_ros = new Zend_Session_Namespace('rosprofile');
			//echo  $sess_ros->rospass;
			echo json_encode(array("status"=>$tmp,"pass"=>$sess_ros->rospass));
		}

		public function productinfoAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$obj=new Model_Order();
			$tmp = $obj->getproductinfo($this->getRequest()->getParam("product_id"));
			echo json_encode($tmp);
			
		}
	}
?>