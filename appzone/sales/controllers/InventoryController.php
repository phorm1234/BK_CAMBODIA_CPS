<?php
	class InventoryController extends Zend_Controller_Action{
		private $_SESSION_EMPLOYEE_ID;
		private $_SESSION_USER_ID;
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
		}//func
		
		function preDispatch()
		{
			if($this->getRequest()->getActionName()==="addhead"){
				
			}else{
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
				$this->view->employee_id=$empprofile['employee_id'];
				$this->_SESSION_EMPLOYEE_ID=$empprofile['employee_id'];
				$this->_SESSION_USER_ID=$empprofile['user_id'];
			}
		}//func preDispatch
		
		function pickinglistAction(){
			/**
			 * @desc
			 */
			$this->_helper->layout()->setLayout('default_layout');  
			$objInv=new Model_Inventory();
			
			//get doc no
			$doc_date=$objInv->getDocDate();			
			$this->view->txt_ddate = $doc_date;
			
			//get picking list number
			$pick_no=$objInv->getPickNo();			
			$this->view->txt_plno = $pick_no;		

			
		}//func pickinglistAction
		
		function getpickinglistAction(){
			$this->_helper->layout()->disableLayout();			
			$objInv=new Model_Inventory();
			/*$arr_json=$objInv->getPickingList();							
			$this->view->arr_json=$arr_json;
			$this->_helper->layout()->disableLayout();*/
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
					$page=$filter->filter($this->getRequest()->getPost('page'));
					$qtype=$filter->filter($this->getRequest()->getPost('qtype'));
					$query=$filter->filter($this->getRequest()->getPost('query'));	
					$order_no=$filter->filter($this->getRequest()->getPost('order_no'));
					$rp=$filter->filter($this->getRequest()->getPost('rp'));
					$sortname=$filter->filter($this->getRequest()->getPost('sortname'));
					$sortorder=$filter->filter($this->getRequest()->getPost('sortorder'));
					$objMember=new Model_Member();
					$arr_json=$objInv->getPickingList($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder);
					$this->view->arr_json=$arr_json;
			}
			
		}//func getpickinglistAction
		
		
		function setpicktempAction(){
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$objInv=new Model_Inventory();	
			$txt_pl=$filter->filter($this->getRequest()->getPost('txt_pl'));		
			$txt_plno=$filter->filter($this->getRequest()->getPost('txt_plno'));		
			$tmp_seq=$filter->filter($this->getRequest()->getPost('tmp_seq'));	
			$txt_cbox=$filter->filter($this->getRequest()->getPost('txt_cbox'));		
			$txt_remark=$filter->filter($this->getRequest()->getPost('txt_remark'));	
			$status=$filter->filter($this->getRequest()->getPost('status'));	
			$slt_promo_st=$filter->filter($this->getRequest()->getPost('slt_promo_st'));	
			$point1=$filter->filter($this->getRequest()->getPost('point1'));		
			$point2=$filter->filter($this->getRequest()->getPost('point2'));	
			$redeem_point=$filter->filter($this->getRequest()->getPost('redeem_point'));	
			$total_point=$filter->filter($this->getRequest()->getPost('total_point'));
			$box_amount=$filter->filter($this->getRequest()->getPost('box_amount'));
			$result = $objInv->setPickTemp($txt_pl,$txt_plno,$tmp_seq,$txt_cbox,$txt_remark,$status,$slt_promo_st,$point1,$point2,$redeem_point,$total_point,$box_amount);	
			echo $result;
		}
		
		function delpicklistAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
	
				$items=$filter->filter($this->getRequest()->getPost('items'));
				
				//die($items);
				$objInv=new Model_Inventory();
				$result=$objInv->delPickList($items);
				echo $result;

		}
		
		function getcountpickAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$doc_no=$filter->filter($this->getRequest()->getPost('doc_no'));
			$objInv=new Model_Inventory();
			$result=$objInv->getCountPick($doc_no);
			echo $result;
		}
		
		function cleartmpAction(){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE); 
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$objInv=new Model_Inventory();
			$txt_plno=$filter->filter($this->getRequest()->getPost('txt_plno'));	
			$result=$objInv->clearTmp($txt_plno);
			echo $result;
		}
		
		function getempAction(){
				$this->_helper->layout->disableLayout();			
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();			
				if ($this->_request->isPost()) {		
					$employee_id=$filter->filter($this->getRequest()->getPost('employee_id'));		
					$actions = $filter->filter($this->getRequest()->getPost('actions'));
					if($actions=='userlogin'){	
						$objPos=new Model_PosGlobal();
						$result=$objPos->getEmployee($employee_id);	
						$arr_data=array(
							'action'=>'userlogin',
							'data'=>$result
						);
						$this->view->arr_data=$arr_data;
					}//f($actions=='userlogin')
					
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
					}//if($actions=='saleman')
					
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
					}//if($actions=='audit')		
				}//if ($this->_request->isPost())
				
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
				}//		if ($this->_request->isGET())	
				
			}//getempAction
			
			function savepickingAction()
			{
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender(TRUE);
				$objInv=new Model_Inventory();
				
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();				
				$emp=$filter->filter($this->getRequest()->getPost('emp'));		
				
				$txt_plno=$objInv->savePicking($emp);
				
				$pick_no=$objInv->getPickNo();			
				//$this->view->txt_plno = $pick_no;
			
				//echo $result;
				if($txt_plno!="")
				{
					$result=$objInv->clearTmp($txt_plno);
					echo "1#".$txt_plno."#".$pick_no;
				}			
				else
				{
					echo "0#".$txt_plno."#".$pick_no;
				}					
			}//savepickingAction
			
			function reportpickAction()
			{
				$this->_helper->layout->disableLayout();
				$objInv=new Model_Inventory();
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();				
				$doc_no=$filter->filter($this->getRequest()->getParam("doc_no"));
				$page = $filter->filter($this->getRequest()->getParam("page"));
				$rnd=$filter->filter($this->getRequest()->getParam("rnd"));
				$arr_data=$objInv->reportPick($doc_no);
				if(md5("first") != $rnd)
				{
					$objInv->setReprintNo($doc_no);
				}
				$this->view->print_no = $objInv->getReprintNo($doc_no);
				$this->view->data = $arr_data;
				
				$branch_data=$objInv->getBranchData($arr_data[0]["branch_id"]);
				$this->view->branch_data = $branch_data;
				
				/*$objInv->setReprintNo($doc_no);
				$this->view->print_no=$objInv->getReprintNo($doc_no);		*/
				
				$objPos=new Model_PosGlobal();
				$result=$objPos->getSaleman($arr_data[0]["reg_user"]);		
				//$result=$result[0]['employee_id']."#".$result[0]['name']."#".$result[0]['surname']."#".$result[0]['check_status'];
				if($result[0]['name']=="")
				{
					$result[0]['name']=$arr_data[0]["reg_user"];
					$result[0]['surname']=$arr_data[0]["reg_user"];
				}
				$this->view->data_emp = $result[0]['name']." ".$result[0]['surname'];	
				$this->view->page = $page;
			}
			function delboxAction()
			{
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender(TRUE);
				$objInv=new Model_Inventory();
				
				$objInv->delBox();					
			}
			function getoldboxAction()
			{
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender(TRUE);
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();				
				$doc_no=$filter->filter($this->getRequest()->getPost("doc_no"));
				$objInv=new Model_Inventory();
				
				echo $objInv->getOldBox($doc_no);					
			}
		
//		function formconfirmopencashdrawerAction(){
//			$this->_helper->layout()->disableLayout();
//			//$this->_helper->viewRenderer->setNoRender(TRUE);
//			$objChk=new Model_DayTransaction();
//			$m_cashdrawer=$objChk->getCashDrawer();
//			$this->view->m_cashdrawer=$m_cashdrawer;
//		}//func
//		
//		function confirmopencashdrawerAction(){
//			$this->_helper->layout()->disableLayout();
//			$this->_helper->viewRenderer->setNoRender(TRUE);
//			$filter = new Zend_Filter_StripTags();
//			if ($this->_request->isPost()){
//				$remark=$filter->filter($this->getRequest()->getPost('remark'));
//				$objCashDw=new Model_DayTransaction();
//				$res=$objCashDw->openCashDrawer($remark);
//				echo $res;
//			}
//		}//func

	    // รับกล่องสินค้า
	public function addheadAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		if(trim($request->getParam("qrcode")) != ""){
			$_bar = new Model_Barcode();
			$_bar->get_doc_no(strtoupper($request->getParam("qrcode")));
			if($_bar->status == true)
						echo json_encode(array("status"=>'y'));
			else 		echo json_encode(array("status"=>'n')); 
		}
	}
	
	public function addheadwebAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		if(trim($request->getParam("qrcode")) != ""){
			$_bar = new Model_Barcode();
			$_bar->get_doc_no(strtoupper($request->getParam("qrcode")));
			if($_bar->status == true)
				echo 'y';
			else 
				echo 'n';
		}
	}

	public function receiveAction(){
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		$this->view->assign("url",$request->getBaseUrl());
		$receive = new Model_Receivedb();
		$this->view->assign("NO_KEY",$receive->check_key_barcode());
		$arr = $receive->head_box();
		$this->view->assign("arr",$arr);
	}
	
	public function receivedetailAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$receive = new Model_Receivedb();
		echo $receive->head_box2();
	}
	
	public function adddetailAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		$id 	= trim($request->getParam("id"));
		$val 	= trim($request->getParam("val"));
		if($id !="" && $val != ""){
			$tmp = new Model_Receivedb();
			$status = $tmp->add_box($id, strtoupper($val));
			if($status == 'y') echo "y";
			else echo "n";
		}else{
			echo "n";
		}
		
	}
	
	public function detailAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		$id 	= trim($request->getParam("id"));
		
		$tmp = new Model_Receivedb();
		$str = $tmp->show_table_receve_detail($id);
	  	echo $str;
	}
	
	public function detailregAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		$id 	= trim($request->getParam("id"));
		$sql = "select doc_no from trn_diary1_iq where id='$id'";
		$rowsb		=$this->db->FetchRow($sql);
		$doc_no	=  $rowsb['doc_no'];
		// select detail
		$sql = "
		select 
			id,product_id,quantity 
			from trn_diary2_iq 
			where doc_no='$doc_no' and doc_tp='PL'";
		$res = $this->db->FetchAll($sql);
		$str = "
		<table class=\"table table-bordered\">
		<thead>
		<tr>
		<th>Picking</th>
		<th>Qty</th>
		<th>Manage</th>
		</tr>
		</thead><tbody>";
		if(count($res) >0){
			foreach ($res as $index){
				$str .="<tr>";
				$str .='<td> '.$index['product_id'].'</td><td>'.number_format($index['quantity'], 2, '.', '').'</td>
				<td>
				<a href="#myModal" role="button" class="btn" data-toggle="modal" onclick="show_qty(\''.$index['id'].'\',\''.$index['product_id'].'\',\''.number_format($index['quantity'], 2, '.', '').'\');">
				<i class="icon-edit"></i></a>
				</td>';
				$str .="</tr>";
			}
		}
		$str .='</tbody></table>';
		$str .='<div style="margin:auto;"><input class="btn btn-primary" type="button" value="พิมพ์"></div>';
		echo $str;
	}
	
	public function showdetailAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		$id 	= trim($request->getParam("id"));
		$tmp = new Model_Receivedb();
		$str = $tmp->show_table_receive($id);
		echo $str;
	}
	
	public function showdetailregAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		$id 	= trim($request->getParam("id"));
		$tmp = new Model_Receivedb();
		$str = $tmp->showreg_edit($id);
		echo $str;
	}
	
	public function updateqtyAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		$id 	= trim($request->getParam("id"));
		$qty 	= trim($request->getParam("qty"));
		if($id != "" && $qty != ""){
			$tmp = new Model_Receivedb();
			$res = $tmp->update_receive_detail($qty,$id);
		}else{
			echo "n";
		}
	}
	
	
	public function repatriateAction(){
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		$this->view->assign("url",$request->getBaseUrl());
		$receive = new Model_Receivedb();
		$arr = $receive->head_box_reg();
		$this->view->assign("arr",$arr);
	}
	
	public function addheadregAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		//$registry = Zend_Registry::getInstance ();
		//$DB = $registry ['DB'];
		$bar	= trim($request->getParam("bar"));
		$qty	= trim($request->getParam("qty"));
		if($bar != "" && $qty != ""){
			$_bar = new Model_Barcode();
			$_bar->gen_doc_no_rep(strtoupper($bar),$qty);
			if($_bar->status == true)
				echo "y";
			else 		echo "n";
		}
	}
	
	public function showitemAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		$receive = new Model_Receivedb();
		$arr = $receive->head_box_reg_show();
	}
	
	
	public function genpdfAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		date_default_timezone_set("Asia/Bangkok");
		//require_once('tcpdf/tcpdf.php');
		try {
			 
			  //$registry = Zend_Registry::getInstance ();
			  //$DB = $registry ['DB'];
			  $id = trim($request->getParam("id"));
			  $str = "";
			  if($id != ""){
			  	$tmp = new Model_Receivedb();
			  	$str = $tmp->show_pdf($id);
			  }

			  require_once('/var/www/pos/htdocs/sales/mpdf/mpdf.php');
			  $mpdf = new mPDF('th','C7',10,'',0,0,0,10,9,20,'P');
			 // $mpdf->SetFont('th','B',13); 
			 $stylesheet = file_get_contents('/var/www/pos/htdocs/sales/css/receive/style.css');
// 			 $stylesheet2 = file_get_contents('css/bootstrap-responsive.css');
 			  $mpdf->WriteHTML($stylesheet,1);
// 			  $mpdf->WriteHTML($stylesheet2,1);
			  $mpdf->WriteHTML($str, 2);
			  $pdfname = "genpdf";
			  $mpdf->Output('/var/www/pos/htdocs/sales/mpdf/data/'.$pdfname.".pdf",'F');
			  $output ='/var/www/pos/htdocs/sales/mpdf/data/'.$pdfname.".pdf";
			  echo shell_exec("lp -o media=Letter $output");
			  if(@file_exists($output)){
				 echo shell_exec("rm $output");			
			  }
			  //echo json_encode(array("name"=>'/sales/mpdf/data/'.$pdfname.".pdf","status"=>"success"));
		} catch (Zend_Pdf_Exception $e) {
		 	die ('PDF error: ' . $e->getMessage());  
		}
	}		
				
	}//class 
?>