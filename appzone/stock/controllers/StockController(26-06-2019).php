<?php 
	class StockController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('stock_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile; 
			$this->empprofile=$empprofile;
			
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
		}

		public function indexAction(){
			$session = new Zend_Session_Namespace('empprofile');
        	$empprofile=$session->empprofile; 
        	$obj=new Model_Stock();
			echo "<pre>";
			print_r($empprofile);
			echo "</pre>";
			
		}
		
		public function tranferinAction(){

			$this->view->date=date('d/m/Y');
			$obj=new Model_Stock();
			$obj->TrancateTable("trn_in2shop_list_tmp");
			$obj->TrancateTable("trn_in2shop_head_tmp");
		}
		
		public function getinvAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Stock();
			$view=$obj->getinv();
			$this->view->data=$view;
		}
		
		public function getproducttranferAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$chk = $filter->filter($this->getRequest()->getParam("chk")); 
			$status_no = $filter->filter($this->getRequest()->getParam("status_no")); 
			$obj=new Model_Stock();
			$view=$obj->sumivn($doc_no);
			$this->view->status_no=$status_no;
			$this->view->sum=$view;
			$this->view->doc_no=$doc_no;
			$this->view->chk=$chk;
			$chkdocno=$obj->checkinv($doc_no);
			$count_chkdocno=count($chkdocno);
			$this->view->chkdocno=$count_chkdocno;
		}
		
		public function getkeyproducttranferAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$chk = $filter->filter($this->getRequest()->getParam("chk")); 
			$obj=new Model_Stock();
			$view=$obj->sumivn($doc_no);
			$this->view->sum=$view;
			$this->view->doc_no=$doc_no;
			$this->view->chk=$chk;
			$chkdocno=$obj->checkinv($doc_no);
			$count_chkdocno=count($chkdocno);
			$this->view->chkdocno=$count_chkdocno;
		}
		
		public function getdataproducttranferAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			$this->view->doc_no=$doc_no;
			$tname="trn_in2shop_list_tmp";
			$fname="doc_date";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproduct($doc_no,$sort,$start,$rp);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
		}
		
		public function getkeyproducttranfertoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$chk = $filter->filter($this->getRequest()->getParam("chk")); 
			$obj=new Model_Stock();
			$view=$obj->sumivn_to();
			$this->view->sum=$view;
			$this->view->chk=$chk;
			$chkdocno=$obj->checkinv_to();
			$count_chkdocno=count($chkdocno);
			$this->view->chkdocno=$count_chkdocno;
		}
		
		public function getdataproducttranfertoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Stock();
			$view=$obj->getdataproduct_to($sort);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			
		}
		
		//rq
		public function getkeyproducttranferrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$chk = $filter->filter($this->getRequest()->getParam("chk")); 
			$obj=new Model_Stock();
			$view=$obj->sumivn_rq();
			$this->view->sum=$view;
			$this->view->chk=$chk;
			$chkdocno=$obj->checkinv_rq();
			$count_chkdocno=count($chkdocno);
			$this->view->chkdocno=$count_chkdocno;
		}
		
		public function getdataproducttranferrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$sort=$sortname." ".$sortorder;
			$obj=new Model_Stock();
			$view=$obj->getdataproduct_rq($sort);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			
		}
		//end rq
		
		
		public function checkinvAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$obj=new Model_Stock();
			$view=$obj->checkinv($doc_no);
			$cview=count($view);
			$this->view->doc_no=$doc_no;
			$this->view->data=$cview;
		}
		
		public function tranin2shopheadtodiaryAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark")); 
			$status_no=$filter->filter($this->getRequest()->getParam("status_no")); 
			$doc_tp=$filter->filter($this->getRequest()->getParam("doc_tp")); 
			$obj=new Model_Stock();
			$view=$obj->tranin2shopheadtodiary($doc_no,$doc_remark,$status_no,$doc_tp);
			//print_r($view);
			$this->view->status=$view;
		}
		
		public function tranin2shopheadtodiaryaiaoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark")); 
			$status_no=$filter->filter($this->getRequest()->getParam("status_no")); 
			$doc_tp=$filter->filter($this->getRequest()->getParam("doc_tp")); 
			$obj=new Model_Stock();
			$view=$obj->tranfertodiary($doc_no,$doc_remark,$status_no,$doc_tp);
			//echo $view;
			$this->view->status=$view;
		}
		
		public function tranin2shopheadtodiarytoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark")); 
			$status_no=$filter->filter($this->getRequest()->getParam("status_no")); 
			$doc_tp=$filter->filter($this->getRequest()->getParam("doc_tp")); 
			$inv = $filter->filter($this->getRequest()->getParam("inv")); 
			$obj=new Model_Stock();
			$view=$obj->tranin2shopheadtodiary_to($doc_no,$doc_remark,$status_no,$doc_tp,$inv);
			$this->view->status=$view;
		}
		
		public function tranin2shopheadtodiaryrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark")); 
			$status_no=$filter->filter($this->getRequest()->getParam("status_no")); 
			$doc_tp=$filter->filter($this->getRequest()->getParam("doc_tp")); 
			$inv = $filter->filter($this->getRequest()->getParam("inv")); 
			$obj=new Model_Stock();
			$view=$obj->tranin2shopheadtodiary_rq($doc_no,$doc_remark,$status_no,$doc_tp,$inv);
			$this->view->status=$view;
		}
		
		public function gentmpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$obj=new Model_Stock();
			$view=$obj->tshop_head2tshop_tmp($doc_no);
		}
		
		public function checkpasswordAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$pwd = $filter->filter($this->getRequest()->getParam("pwd")); 
			$obj=new Model_Stock();
			$view=$obj->checkpassword($doc_no,$pwd);
			$this->view->reqpwd=$pwd;
			$this->view->truepwd=$view;
			
		}
		
		public function tranferkeyinAction(){
			
		}
		
		public function checkheaddocnoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Stock();
			$view=$obj->checkheaddocno($doc_no);
			$this->view->data=$view;
		}
		
		public function checkdiary1docnoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Stock();
			$view=$obj->checkdiary1docno($doc_no);
			$this->view->data=$view;
		}
		
		public function checkproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			
			$count=count($pro_id);
			
			if($count>0){
				$pro_id=$pro_id[0]['product_id'];
				//echo ">".$pro_id."<";
				$view=$obj->checkproduct($pro_id);
			}else{
				$view=array();
			}
			//print_r($view);
			$this->view->data=$view;
			$this->view->status_no=$status_no;
		}
		
		public function checkproducttestterfixAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$view=$obj->checkproducttestterfix($product_id);
			$this->view->data=$view;
			$this->view->status_no=$status_no;
			$this->view->qty=$qty;
		}
		
		public function inserttmpdiaryAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$invoice = $filter->filter($this->getRequest()->getParam("invoice"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$type_tranfer = $filter->filter($this->getRequest()->getParam("type_tranfer"));
			$type_product = $filter->filter($this->getRequest()->getParam("type_product"));
			$tbl_head="trn_in2shop_head_tmp";
			$tbl_list="trn_in2shop_list_tmp";
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$array_data=array(
				'doc_no'=>$invoice,
				'doc_remark'=>$doc_remark,
				'product_id'=>$pro_id,
				'qty'=>$qty,
				'type_tranfer'=>$type_tranfer,
				'type_product'=>$type_product,
				'doc_dt'=>$doc_tp,
				'tbl_head'=>$tbl_head,
				'tbl_list'=>$tbl_list
			);
			$view=$obj->gen_tmp_diary($array_data);
			$this->view->data=$view;
		}
		
		public function inserttmpdiarytoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$invoice = $filter->filter($this->getRequest()->getParam("invoice"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$type_tranfer = $filter->filter($this->getRequest()->getParam("type_tranfer"));
			$type_product = $filter->filter($this->getRequest()->getParam("type_product"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$tbl_head="trn_tdiary1_to";
			$tbl_list="trn_tdiary2_to";
			$array_data=array(
				'doc_no'=>$invoice,
				'doc_remark'=>$doc_remark,
				'product_id'=>$pro_id[0]['product_id'],
				'qty'=>$qty,
				'type_tranfer'=>$type_tranfer,
				'type_product'=>$type_product,
				'doc_dt'=>$doc_tp,
				'tbl_head'=>$tbl_head,
				'tbl_list'=>$tbl_list
			);
			
			$view=$obj->gen_tmp_diary_to($array_data);
			$this->view->data=$view;
		}
		
		public function inserttmpdiaryrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$invoice = $filter->filter($this->getRequest()->getParam("invoice"));
			$doc_remark = $filter->filter($this->getRequest()->getParam("doc_remark"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$type_tranfer = $filter->filter($this->getRequest()->getParam("type_tranfer"));
			$type_product = $filter->filter($this->getRequest()->getParam("type_product"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$tbl_head="trn_tdiary1_rq";
			$tbl_list="trn_tdiary2_rq";
			$array_data=array(
				'doc_no'=>$invoice,
				'doc_remark'=>$doc_remark,
				'product_id'=>$pro_id[0]['product_id'],
				'qty'=>$qty,
				'type_tranfer'=>$type_tranfer,
				'type_product'=>$type_product,
				'doc_dt'=>$doc_tp,
				'tbl_head'=>$tbl_head,
				'tbl_list'=>$tbl_list
			);
			
			$view=$obj->gen_tmp_diary_rq($array_data);
			$this->view->data=$view;
		}
		
		public function deletetmplistAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$arr_seq=explode(',',$seq);
			array_pop($arr_seq);
			$obj=new Model_Stock();
			$view=$obj->delettemlist($doc_no,$arr_seq);
			$this->view->data=$view;
			
		}
		
		public function deletetmplisttoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$arr_seq=explode(',',$seq);
			array_pop($arr_seq);
			$obj=new Model_Stock();
			$view=$obj->delettemlist_to($arr_seq);
			$this->view->data=$view;
			
		}
		
		public function deletetmplistrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$arr_seq=explode(',',$seq);
			array_pop($arr_seq);
			$obj=new Model_Stock();
			$view=$obj->delettemlist_rq($arr_seq);
			$this->view->data=$view;
			
		}
		
		
		public function cancelkeytranferAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Stock();
			$view=$obj->cancelkeytranfer($doc_no);
			$this->view->data=$view;
		}
		
		public function cancelkeytranfertoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$obj=new Model_Stock();
			$obj->canceltranfer_to();
			$this->view->data="y";
		}
		
		public function cancelkeytranferrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$obj=new Model_Stock();
			$obj->canceltranfer_rq();
			$this->view->data="y";
		}
		
		public function cleardiaryAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Stock();
			$view=$obj->clear_diary($doc_no);
		}
		
		public function tranferaiAction(){
			$obj=new Model_Stock();
			$obj->canceltranfer();
			$view=$obj->getdocnocheckstock("AI");
			$this->view->doc_no=$view;
		}
		
		public function getdocnochkstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$p_type = $filter->filter($this->getRequest()->getParam("p_type"));
			$obj=new Model_Stock();
			$view=$obj->getdocnocheckstock($p_type);
			$this->view->data=$view;
			
		}
		
		public function checkpasswordckAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$pwd = $filter->filter($this->getRequest()->getParam("pwd")); 
			$obj=new Model_Stock();
			//$view=$obj->chk_password_checkstock($doc_no);
			$group_id="AUDIT";
			$view=$obj->chk_password_checkstock($pwd,$group_id);
			$this->view->data=$view;
			//$this->view->pwd=$pwd;
		}
		
		public function checkinvstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$obj=new Model_Stock();
			$view=$obj->checkinv_stock($doc_no);
			$cview=count($view);
			$this->view->data=$cview;
			$this->view->doc_no=$doc_no;
		}
		
		public function gentmpcheckstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$status_no=$filter->filter($this->getRequest()->getParam("status_no")); 
			$doc_tp=$filter->filter($this->getRequest()->getParam("doc_tp"));
			$obj=new Model_Stock();
			$view=$obj->tshop_head2tshop_tmp_checkstock($doc_no,$status_no,$doc_tp);
			//echo $view;
		}
		
		public function tranferaoAction(){
			$obj=new Model_Stock();
			$obj->canceltranfer();
			$view=$obj->getdocnocheckstock("AO");
			$this->view->doc_no=$view;
			
		}
		
		public function tranfertoAction(){
			$obj=new Model_Stock();

			$obj->canceltranfer_to();
			$to_menu = $obj->getTOMenu();
			$doc_tp="TO";
			$view=$obj->getto($doc_tp);
			$this->view->data=$view;
			$this->view->menu = $to_menu;
		}
//transfertobyorder

public function getdataproducttranfertoorAction(){
	$this->_helper->layout()->disableLayout();
	$filter = new Zend_Filter_StripTags();
	$page = $filter->filter($this->getRequest()->getParam("page"));
	$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
	$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
	$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
	$sort=$sortname." ".$sortorder;
	$obj=new Model_Stock();
	$view=$obj->getdataproduct_toor($sort,$doc_no);
	$this->view->arr_data=$view;
	$this->view->page=$page;
	$this->view->sum=$page;
	
}

public function getproducttoorAction(){
	$this->_helper->layout()->disableLayout();
	$filter = new Zend_Filter_StripTags();
	$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
	$chk = $filter->filter($this->getRequest()->getParam("chk")); 
	$status_no = $filter->filter($this->getRequest()->getParam("status_no")); 
	$obj=new Model_Stock();
	$view=$obj->sumivn_to_or($doc_no);
	$this->view->status_no=$status_no;
	$this->view->sum=$view;
	$this->view->doc_no=$doc_no;
	$this->view->chk=$chk;
	$chkdocno=$obj->checkinv_to_or($doc_no);
	$count_chkdocno=count($chkdocno);
	$this->view->chkdocno=$count_chkdocno;
}

public function tranfertobyorderAction(){
	$obj=new Model_Stock();

	$obj->canceltranfer_to();
	$to_menu = $obj->getTOMenu();
	$doc_tp="TO";
	$view=$obj->getto($doc_tp);
	$this->view->data=$view;
	$this->view->menu = $to_menu;
}

public function getdoctobyorAction(){
	$this->_helper->layout()->disableLayout();
	$obj=new Model_Stock();
	$view=$obj->getdoctobyor();
	$this->view->data=$view;
}
/////////

		
		public function printstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$obj=new Model_Stock();
			$data=$obj->docnobydocrefer($doc_no);
			$content=$obj->getdiary2($data[0]['doc_no']);
			$status_no=$obj->getstatusno($data[0]['status_no']);
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->doc_no=$data[0]['doc_no'];
			$this->view->doc_no_refer=$data[0]['refer_doc_no'];
			$this->view->flag=$data[0]['flag'];
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->user=$this->empprofile['user_id'];
			$this->view->doc_dt=$data[0]['doc_date'];
			$this->view->print_no=$data[0]['print_no'];
			$this->view->time_doc_no=$data[0]['doc_time'];
			$this->view->doc_remark=$data[0]['doc_remark'];
			$this->view->date_regis=date("d/m/Y");
			$this->view->content=$content;
			$data_sum=$obj->sumivnconfirm($data[0]['doc_no']);
			$this->view->data_sum=$data_sum;
			$doc_tp=$obj->getdoctptxt($data[0]['doc_tp']);
			
			if($doc_tp[0]['stock_st']>0){
				$head_txt="?????????????????????????????????????????????";
			}else{
				$head_txt="??????????????????????????????????????????";
			}
			$this->view->head_txt=$head_txt;
			$this->view->status_no="(".$status_no[0]['status_no'].") ".$status_no[0]['description'];
		}
		
		public function checkproducttesterAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$tbl=array(
						'tbl1'=>'trn_diary2',
						'tbl2'=>'trn_diary1',
						'tbl3'=>'trn_tdiary2_to'
					   );
			$view=$obj->checkproducttester($pro_id,$tbl);
			$this->view->data=$view;
			$this->view->qty=$qty;
		}
		
		public function checkproducttesterrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$tbl=array(
						'tbl1'=>'trn_diary2_rq',
						'tbl2'=>'trn_diary1_rq',
						'tbl3'=>'trn_tdiary2_to'
					   );
			$view=$obj->checkproducttester($pro_id,$tbl);
			$this->view->data=$view;
			$this->view->qty=$qty;
		}
		//to
		public function checkinvoiceAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$doc_status = $filter->filter($this->getRequest()->getParam("doc_status"));
			$obj=new Model_Stock();
			$doc_tp=$obj->getdocstatus($doc_status);
			$tbl="trn_diary1";
			$view=$obj->checkinvoice($inv,$doc_tp,$tbl);
			$this->view->data=$view;
		}
		
		public function checkproductinvoiceAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_status = $filter->filter($this->getRequest()->getParam("doc_status"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$doc_tp=$obj->getdocstatus($doc_status);
			$tbl="trn_diary2";
			$view=$obj->checkproductinvoice($inv,$pro_id,$doc_tp,$tbl);
			$this->view->data=$view;
		}
		
		public function checkproductinvoiceqtyAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$doc_status = $filter->filter($this->getRequest()->getParam("doc_status"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$doc_tp=$obj->getdocstatus($doc_status);
			$view_onhand=$obj->checkstockproduct($pro_id);
			$tbl=array(
						'tbl1'=>'trn_diary2',
						'tbl2'=>'trn_diary1',
						'tbl3'=>'trn_tdiary2_to'
					   );
			$type_doc_tp="TO";
			//$view=$obj->checkproductinvoiceqty($inv,$pro_id,$doc_tp,$qty,$tbl,$type_doc_tp);
			$view=$obj->checkproductrans($pro_id);
			$view_max_inv=$obj->getmaxcninv($pro_id,$doc_tp,$inv);
			$this->view->data_onhand=$view_onhand;
			$this->view->data_product=$view;
			$this->view->data_product_max=$view_max_inv;
			$this->view->qty=$qty;
		}
		//end to
		//rq
		public function checkinvoicerqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$doc_status = $filter->filter($this->getRequest()->getParam("doc_status"));
			$obj=new Model_Stock();
			$doc_tp=$obj->getdocstatus($doc_status);
			$tbl="trn_diary1";
			$view=$obj->checkinvoice($inv,$doc_tp,$tbl);
			$this->view->data=$view;
		}
		
		public function checkproductinvoicerqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_status = $filter->filter($this->getRequest()->getParam("doc_status"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$doc_tp=$obj->getdocstatus($doc_status);
			$tbl="trn_diary2";
			$view=$obj->checkproductinvoice($inv,$pro_id,$doc_tp,$tbl);
			$this->view->data=$view;
		}
		
		public function checkproductinvoiceqtyrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$inv = $filter->filter($this->getRequest()->getParam("inv"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$doc_status = $filter->filter($this->getRequest()->getParam("doc_status"));
			$obj=new Model_Stock();
			$pro_id=$obj->convproduct_id($product_id);
			$pro_id=$pro_id[0]['product_id'];
			$doc_tp=$obj->getdocstatus($doc_status);
			$view_onhand=$obj->checkstockproduct($pro_id);
			$tbl=array(
						'tbl1'=>'trn_diary2_rq',
						'tbl2'=>'trn_diary1_rq',
						'tbl3'=>'trn_tdiary2_rq'
					   );
			$type_doc_tp="RQ";
			$view=$obj->checkproductinvoiceqty($inv,$pro_id,$doc_tp,$qty,$tbl,$type_doc_tp);
			$view_max_inv=$obj->getmaxcninv($pro_id,$doc_tp,$inv);
			$this->view->data_onhand=$view_onhand;
			$this->view->data_product=$view;
			$this->view->data_product_max=$view_max_inv;
			$this->view->qty=$qty;
		}
		//end rq
		public function getdocnotoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_status = $filter->filter($this->getRequest()->getParam("status_no"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$obj=new Model_Stock();
			$doc_tp=$obj->getdocstatus($doc_status);
			$view=$obj->getdoctranferto($doc_tp,$tbl);
			$this->view->data=$view;
		}
		
		public function checktempdiarytoAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Stock();
			$view=$obj->checktempdiaryto();
			$this->view->data=$view;
		}
		
		public function checktempdiaryrqAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Stock();
			$view=$obj->checktempdiaryrq();
			$this->view->data=$view;
		}
		
		public function tranferrqAction(){
			$obj=new Model_Stock();
			$obj->canceltranfer_rq();
			$doc_tp="RQ";
			$view=$obj->getto($doc_tp);
			$this->view->data=$view;
		}
		
		public function getfixstockAction(){
			$obj=new Model_Stock();
			$view=$obj->sumfixstock();
			$view_stock=$obj->sumstockmaster();
			$this->view->data=$view;
			$this->view->data_stock=$view_stock;
		}
		
		public function getproductfixstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$this->view->product_id=$product_id;
			$obj=new Model_Stock();
		}
		
		public function getdataproductfixstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			if(!empty($product_id)){
				$where=" where product_id LIKE '".$product_id."%' ";
			}else{
				$where="";
			}
			
			
			$tname="trn_fix_stock".$where;
			$fname="product_id";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sort=$sortname." ".$sortorder." ".$limit;
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproductfixstock($where,$sort);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
			
			
			
			/*$obj=new Model_Stock();
			$view=$obj->getdataproductfixstock($product_id,$sort);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;*/
			
			
			/*$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			if(!empty($flag)){
				$where=" where flag1 LIKE '".$flag."%' ";
			}else{
				$where="";
			}
			$tname="trn_in2shop_list".$where;
			$fname="flag1";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sort=$sortname." ".$sortorder." ".$limit;
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproductquery($sort,$flag);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;*/
			
		}
		
		public function productqueryorderAction(){
			$flag="O";
			$this->view->flag=$flag;
			$this->view->txt="ORDER GOODS";
		}
		
		public function getproductqueryAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Stock();
			$filter = new Zend_Filter_StripTags();
			$flag = $filter->filter($this->getRequest()->getParam("flag"));
			$txt = $filter->filter($this->getRequest()->getParam("txt"));
			$this->view->flag=$flag;
			$this->view->txt=$txt;
		}
		
		public function getdataproductqueryAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$flag = $filter->filter($this->getRequest()->getParam("flag"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			if(!empty($flag)){
				$where=" where flag1 LIKE '".$flag."%' ";
			}else{
				$where="";
			}
			$tname="trn_in2shop_list".$where;
			$fname="flag1";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sort=$sortname." ".$sortorder." ".$limit;
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproductquery($sort,$flag);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
			
			
			/*
			 $page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			if(!empty($product_id)){
				$where=" where product_id LIKE '".$product_id."%' ";
			}else{
				$where="";
			}
			$tname="com_stock_km".$where;
			$fname="product_id";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sort=$sortname." ".$sortorder." ".$limit;
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproductinventories($product_id,$sort); 
			 */
		}
		
		public function productqueryinvAction(){
			$flag="D";
			$this->view->flag=$flag;
			$this->view->txt="INVOICE";
		}
		
		public function formcountstockAction(){
			
		}
		
		public function printcountstockAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		public function formcheckcashAction(){
			
		}
		
		public function printcheckcashAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		public function forinventoriesAction(){
			
		}
		
		public function getproductinventoriesAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$this->view->product_id=$product_id;
			$obj=new Model_Stock();
		}
		
		public function getdataproductinventoriesAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			if(!empty($product_id)){
				$where=" where product_id LIKE '".$product_id."%' ";
			}else{
				$where="";
			}
			$tname="com_stock_km".$where;
			$fname="product_id";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sort=$sortname." ".$sortorder." ".$limit;
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproductinventories($product_id,$sort);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
		}
		
		public function formrqAction(){
			
		}
		
		public function getrqAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		public function getdatarqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$tname="trn_diary1_rq";
			$fname="doc_no";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sort=$sortname." ".$sortorder." ".$limit;
			$where="";
			$obj=new Model_Stock();
			$view=$obj->getdatarq($sort,$where);
			$total=$obj->countRec($fname,$tname);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
			
		}
		
		public function checkstatusrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$doc_no=explode(',',$get_doc_no);
			$where="and doc_no='$doc_no[0]'";
			$sort="doc_no";
			$obj=new Model_Stock();
			$view=$obj->getdatarq($sort,$where);
			$this->view->data=$view;
		}
		
		public function getdocnorqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_doc_no_rq = $filter->filter($this->getRequest()->getParam("doc_no_rq"));
			$doc_no=explode(',',$get_doc_no_rq);
			$type_docno="TO";
			$obj=new Model_Stock();
			$doc_no_to=$obj->gendocno('',$type_docno);
			$this->view->doc_no_rq=$doc_no[0];
			$this->view->doc_no_to=$doc_no_to;
		}
		
		public function tranferrqtoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_doc_no_rq = $filter->filter($this->getRequest()->getParam("doc_no_rq"));
			$cashier_id = $filter->filter($this->getRequest()->getParam("cashier_id"));
			$type_docno="TO";
			$obj=new Model_Stock();
			$doc_no_to=$obj->gendocno('',$type_docno);
			$view=$obj->tranferrqto($get_doc_no_rq,$type_docno,$cashier_id);
			$this->view->data=$view;
		}
		
		public function checkcashierAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$cashier_id = $filter->filter($this->getRequest()->getParam("cashier_id"));
			$obj=new Model_Stock();
			$view=$obj->checkcashier($cashier_id);
			$data=count($view);
			$this->view->data=$data;
		}
		
		public function rqdetailAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$doc_no=explode(',',$get_doc_no);
			$obj=new Model_Stock();
			$view=$obj->rqdetail($doc_no[0]);
			$this->view->data=$view;
		}
		
		public function checkproductmasterAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Stock();
			$view=$obj->checkproductmaster($get_doc_no);
			$this->view->data=$view;
		}
		
		public function printtoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no_refer = $filter->filter($this->getRequest()->getParam("doc_no_refer")); 
			$obj=new Model_Stock();
			$data=$obj->docnobydocreferto($doc_no_refer);
			$content=$obj->getdiary2to($data[0]['doc_no']);
			$status_no=$obj->getstatusno($data[0]['status_no']);
			print_r($status_no);
			echo "----".$status_no[0]['description'];
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->doc_no=$data[0]['doc_no'];
			$this->view->doc_no_refer=$doc_no_refer;
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->user=$this->empprofile['user_id'];
			$this->view->doc_dt=$data[0]['doc_date'];
			$this->view->print_no=$data[0]['print_no'];
			$this->view->time_doc_no=$data[0]['doc_time'];
			$this->view->doc_remark=$data[0]['doc_remark'];
			$this->view->date_regis=date("d/m/Y");
			$this->view->content=$content;
			$data_sum=$obj->sumivnconfirmto($data[0]['doc_no']);
			$this->view->data_sum=$data_sum;
			$this->view->status_no="(".$status_no[0]['status_no'].") ".$status_no[0]['description'];
		}
		
		public function printtrantoAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
			$obj=new Model_Stock();
			$data=$obj->docnobydocto($doc_no);
			$content=$obj->getdiary2to($data[0]['doc_no']);
			$status_no=$obj->getstatusno($data[0]['status_no']);
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->doc_no=$data[0]['doc_no'];
			$this->view->flag=$data[0]['flag'];
			$this->view->doc_no_refer=$data[0]['refer_doc_no'];
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->user=$this->empprofile['user_id'];
			$this->view->doc_dt=$data[0]['doc_date'];
			$this->view->print_no=$data[0]['print_no'];
			$this->view->time_doc_no=$data[0]['doc_time'];
			$this->view->doc_remark=$data[0]['doc_remark'];
			$this->view->date_regis=date("d/m/Y");
			$this->view->content=$content;
			$data_sum=$obj->sumivnconfirmto($data[0]['doc_no']);
			$this->view->data_sum=$data_sum;
			$this->view->status_no="(".$status_no[0]['status_no'].") ".$status_no[0]['description'];
		}
		
		public function getproducttempAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("invoice")); 
			$status_no = $filter->filter($this->getRequest()->getParam("status_no")); 
			$obj=new Model_Stock();
			$view=$obj->sumivn($doc_no);
			$this->view->sum=$view;
			$this->view->status_no=$status_no;
			$this->view->doc_no=$doc_no;
		}
		
		public function getdataproducttempAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			$this->view->doc_no=$doc_no;
			$tname="trn_in2shop_list_tmp";
			$fname="doc_date";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdataproduct($doc_no,$sort,$start,$rp);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
			$this->view->rp=$rp;
		}
		
		public function deletetempproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$arr_seq=explode(',',$seq);
			array_pop($arr_seq);
			$obj=new Model_Stock();
			$view=$obj->delettemlist($doc_no,$arr_seq);
			$this->view->data=$view;
		}
		
		public function tranferkeytiAction(){
			$this->view->doc_tp="TI";
			$this->view->status_no="62";
			$obj=new Model_Stock();
			$obj->TrancateTable("trn_in2shop_head_tmp");
			$obj->TrancateTable("trn_in2shop_list_tmp");
			$view=$obj->check_config_ti_keyin();
			$this->view->status_lock=$view;
		}
		
		public function formdataAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$obj=new Model_Stock();
			$view_sum=$obj->sumin2shop();
			$this->view->sum=$view_sum;
			$this->view->doc_tp=$doc_tp;
			$this->view->status_no=$status_no;
		}
		
		public function formdataproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			$tname="trn_in2shop_list_tmp";
			$fname="doc_date";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$obj=new Model_Stock();
			$total=$obj->countRec($fname,$tname);
			$view=$obj->getdatain2shop_temp($sort,$start,$rp);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
		}
		
		public function addtmpdiaryAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_tp"));
			$status_no = $filter->filter($this->getRequest()->getParam("status_no"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$invoice = $filter->filter($this->getRequest()->getParam("invoice"));
			$data_array=array(
							'doc_tp'=>$doc_tp,
							'status_no'=>$status_no,
							'product_id'=>$product_id,
							'qty'=>$qty,
							'invoice'=>$invoice,
			);
			$obj=new Model_Stock();
			$view=$obj->add_temp_in2shop($data_array);
			$this->view->data=$view;
		}
		
		public function checkproductidAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$obj=new Model_Stock();
			$view=$obj->checkproduct($product_id);
			$this->view->data=$view;
		}
		
		public function canceltranfertiAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$obj=new Model_Stock();
			$view=$obj->canceltranfer();
			$this->view->data=$view;
			
		}
		
		public function selectprintrqAction(){
				
		}
		
		public function selectprinttoAction(){
				
		}
		
		public function selectprinttiAction(){
				
		}
		
		public function selectprintaiAction(){
				
		}
		
		public function selectprintaoAction(){
				
		}
		
		public function selectprintorAction(){
				
		}
		
		public function selectprintiqAction(){
				
		}
		
		public function getdoctpAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_type = $filter->filter($this->getRequest()->getParam("doc_type"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$obj=new Model_Stock();
			$view=$obj->getdoctranferto($doc_type,$tbl);
			$this->view->data=$view;
		}
		
		public function printrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$tbl = "trn_diary1_rq";
			$obj=new Model_Stock();
			$data=$obj->getdocall($doc_no,$tbl);
			$content=$obj->getdiary2rq($doc_no);
			$this->view->doc_no=$doc_no;
			$status_no=$obj->getstatusno($data[0]['status_no']);
			//$this->view->doc_no_refer=$doc_no_refer;
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->user=$this->empprofile['user_id'];
			$this->view->doc_dt=$data[0]['doc_date'];
			$this->view->flag=$data[0]['flag'];
			$this->view->print_no=$data[0]['print_no'];
			$this->view->time_doc_no=$data[0]['doc_time'];
			$this->view->doc_remark=$data[0]['doc_remark'];
			$this->view->date_regis=date("d/m/Y");
			$this->view->content=$content;
			$data_sum=$obj->sumivnconfirmrq($doc_no);
			$this->view->data_sum=$data_sum;
			$this->view->status_no="(".$status_no[0]['status_no'].") ".$status_no[0]['description'];
		}
		
		public function printorAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$tbl = "trn_diary1_or";
			$obj=new Model_Stock();
			$data=$obj->getdocall($doc_no,$tbl);
			$content=$obj->getdiary2or($doc_no);
			$this->view->doc_no=$doc_no;
			$status_no=$obj->getstatusno($data[0]['status_no']);
			//$this->view->doc_no_refer=$doc_no_refer;
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->user=$this->empprofile['user_id'];
			$this->view->doc_dt=$data[0]['doc_date'];
			$this->view->flag=$data[0]['flag'];
			$this->view->print_no=$data[0]['print_no'];
			$this->view->time_doc_no=$data[0]['doc_time'];
			$this->view->doc_remark=$data[0]['doc_remark'];
			$this->view->date_regis=date("d/m/Y");
			$this->view->content=$content;
			$data_sum=$obj->sumivnconfirmor($doc_no);
			$this->view->data_sum=$data_sum;
			$this->view->status_no="(".$status_no[0]['status_no'].") ".$status_no[0]['description'];
		}
		
		public function printiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$tbl = "trn_diary1_iq";
			$obj=new Model_Stock();
			$data=$obj->getdocall($doc_no,$tbl);
			$content=$obj->getdiary2iq($doc_no);
			$this->view->doc_no=$doc_no;
			$status_no=$obj->getstatusno($data[0]['status_no']);
			//$this->view->doc_no_refer=$doc_no_refer;
			$this->view->company_name=$this->empprofile['company_name'];
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->user=$this->empprofile['user_id'];
			$this->view->doc_dt=$data[0]['doc_date'];
			$this->view->flag=$data[0]['flag'];
			$this->view->print_no=$data[0]['print_no'];
			$this->view->time_doc_no=$data[0]['doc_time'];
			$this->view->doc_remark=$data[0]['doc_remark'];
			$this->view->date_regis=date("d/m/Y");
			$this->view->content=$content;
			$data_sum=$obj->sumivnconfirmiq($doc_no);
			$this->view->data_sum=$data_sum;
			$this->view->status_no="(".$status_no[0]['status_no'].") ".$status_no[0]['description'];
		}
		
		public function getdocnoreAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_end = $filter->filter($this->getRequest()->getParam("doc_no_end"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			if(empty($doc_no_end)){
				$doc_no_end=$doc_no_start;
			}
			if(empty($doc_no_start)){
				$doc_no_start=$doc_no_end;
			}
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_type"));
			$obj=new Model_Stock();
			$content=$obj->getdocnore($doc_no_start,$doc_no_end,$doc_tp,$tbl);
			$this->view->data=$content;
		}
		
		public function previewtiAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_end = $filter->filter($this->getRequest()->getParam("doc_no_end"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$txt_head = $filter->filter($this->getRequest()->getParam("txt_head"));
			if(empty($doc_no_end)){
				$doc_no_end=$doc_no_start;
			}
			if(empty($doc_no_start)){
				$doc_no_start=$doc_no_end;
			}
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_type"));
			$obj=new Model_Stock();
			$content=$obj->previewti($doc_no_start,$doc_no_end,$doc_tp,$tbl);
			$this->view->data=$content;
			$this->view->txt_head=$txt_head;
		}
		
		public function previewrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_end = $filter->filter($this->getRequest()->getParam("doc_no_end"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$txt_head = $filter->filter($this->getRequest()->getParam("txt_head"));
			if(empty($doc_no_end)){
				$doc_no_end=$doc_no_start;
			}
			if(empty($doc_no_start)){
				$doc_no_start=$doc_no_end;
			}
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_type"));
			$obj=new Model_Stock();
			$content=$obj->previewrq($doc_no_start,$doc_no_end,$doc_tp,$tbl);
			$this->view->data=$content;
			$this->view->txt_head=$txt_head;
		}
		
		public function previeworAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_end = $filter->filter($this->getRequest()->getParam("doc_no_end"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$txt_head = $filter->filter($this->getRequest()->getParam("txt_head"));
			if(empty($doc_no_end)){
				$doc_no_end=$doc_no_start;
			}
			if(empty($doc_no_start)){
				$doc_no_start=$doc_no_end;
			}
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_type"));
			$obj=new Model_Stock();
			$content=$obj->previewor($doc_no_start,$doc_no_end,$doc_tp,$tbl);
			$this->view->data=$content;
			$this->view->txt_head=$txt_head;
		}
		
		public function previewiqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_start = $filter->filter($this->getRequest()->getParam("doc_no_start"));
			$doc_no_end = $filter->filter($this->getRequest()->getParam("doc_no_end"));
			$tbl = $filter->filter($this->getRequest()->getParam("tbl"));
			$txt_head = $filter->filter($this->getRequest()->getParam("txt_head"));
			if(empty($doc_no_end)){
				$doc_no_end=$doc_no_start;
			}
			if(empty($doc_no_start)){
				$doc_no_start=$doc_no_end;
			}
			$doc_tp = $filter->filter($this->getRequest()->getParam("doc_type"));
			$obj=new Model_Stock();
			$content=$obj->previewiq($doc_no_start,$doc_no_end,$doc_tp,$tbl);
			$this->view->data=$content;
			$this->view->txt_head=$txt_head;
		}
		
		public function checkqtyproductrqAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$obj=new Model_Stock();
			$content=$obj->check_qty_product_rq($product_id);
			$this->view->data=$content;
			$this->view->key_qty=$qty;
		}

		public function checkplAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Stock();
			$view=$obj->check_pl();
			$this->view->data=$view;
		}
		
		public function testapiAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Stock();
		}
		

		public function checktoprocessAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$obj=new Model_Stock();
			$view=$obj->check_stock_master($product_id,$qty);
			

			$this->view->data=$view;			
		
		
		}
	}