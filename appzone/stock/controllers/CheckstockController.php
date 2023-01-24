<?php 
	class CheckstockController extends Zend_Controller_Action{
		public $doc_date;
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('checkstock_layout');
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
		
		public function preDispatch(){
           $objPos=new SSUP_Controller_Plugin_PosGlobal();
           $doc_date_pos=$objPos->getDocDatePos($this->empprofile['corporation_id'],$this->empprofile['company_id'],$this->empprofile['branch_id'],$this->empprofile['branch_no']); 
           $this->doc_date=$doc_date_pos;
        } 

		public function indexAction(){
			$session = new Zend_Session_Namespace('empprofile');
        	$empprofile=$session->empprofile; 
			echo "<pre>";
			print_r($empprofile);
			echo "</pre>";
		}
		
		public function configshelfAction(){
			$obj=new Model_Checkstock();
			$view=$obj->viewshelf("");
			$this->view->viewshelf=$view;
		}
		
		public function viewshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$shelf = $filter->filter($this->getRequest()->getParam("shelf")); 
			$obj=new Model_Checkstock();
			$view=$obj->viewshelf($shelf);
			$this->view->viewshelf=$view;
		}
		
		public function formaddshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$obj=new Model_Checkstock();
			$view=$obj->checkeditshelf($shelf_no);
			$this->view->checkeditshelf=$view;
		}
		
		public function saveshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$chk_func_save = $filter->filter($this->getRequest()->getParam("chk_func_save")); 
			$shelf_desc = $filter->filter($this->getRequest()->getParam("shelf_desc")); 
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$shelf_no_up = $filter->filter($this->getRequest()->getParam("shelf_no_up"));
			$corporation_id="op";
			$company_id="op";
			$user_id="test";
			$data=array(
						'shelf_no'=>$shelf_no,
						'desc'=>$shelf_desc
			);
			$data_detail=array(
						'corporation_id'=>$corporation_id,
						'company_id'=>$company_id,
						'chk_func_save'=>$chk_func_save,
						'shelf_no_up'=>$shelf_no_up,
						'user_id'=>$user_id
			);
			$obj=new Model_Checkstock();
			$view=$obj->saveshelf($data,$data_detail);
			$this->view->checkstatus=$view;
		}
		
		public function deleteshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$obj=new Model_Checkstock();
			$view=$obj->deleteshelf($shelf_no);
			$this->view->checkstatus=$view;
		}
		
		public function formaddfloorAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no"));
			$obj=new Model_Checkstock();
			$view=$obj->checkeditfloor($shelf_no,$floor_no);
			$view_floor=$obj->viewfloor($shelf_no);
			$this->view->checkeditfloor=$view;
			$this->view->shelf_no=$shelf_no;
			$this->view->floor=$view_floor;
			
		}
		
		public function savefloorAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$chk_func_save = $filter->filter($this->getRequest()->getParam("chk_func_save")); 
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no"));
			$floor_desc = $filter->filter($this->getRequest()->getParam("floor_desc"));  
			$floor_no_up = $filter->filter($this->getRequest()->getParam("floor_no_up"));
			$corporation_id="op";
			$company_id="op";
			$user_id="test";
			$data=array(
						'shelf_no'=>$shelf_no,
						'floor_no'=>$floor_no,
						'desc'=>$floor_desc
			);
			$data_detail=array(
						'corporation_id'=>$corporation_id,
						'company_id'=>$company_id,
						'chk_func_save'=>$chk_func_save,
						'floor_no_up'=>$floor_no_up,
						'user_id'=>$user_id
			);
			$obj=new Model_Checkstock();
			$view=$obj->savefloor($data,$data_detail);
			$this->view->checkstatus=$view;
		}
		
		public function deletefloorAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no")); 
			$obj=new Model_Checkstock();
			$view=$obj->deletefloor($shelf_no,$floor_no);
			$this->view->checkstatus=$view;
		}
		
		public function formaddroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no"));
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$view=$obj->checkeditroom($shelf_no,$floor_no,$room_no);
			$view_room=$obj->viewroom($shelf_no,$floor_no);
			$this->view->checkeditroom=$view;
			$this->view->shelf_no=$shelf_no;
			$this->view->floor_no=$floor_no;
			$this->view->room=$view_room;
			
		}
		
		public function saveroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$chk_func_save = $filter->filter($this->getRequest()->getParam("chk_func_save")); 
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no"));
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$room_desc = $filter->filter($this->getRequest()->getParam("room_desc"));  
			$room_no_up = $filter->filter($this->getRequest()->getParam("room_no_up"));
			$corporation_id="op";
			$company_id="op";
			$user_id="test";
			$data=array(
						'shelf_no'=>$shelf_no,
						'floor_no'=>$floor_no,
						'room_no'=>$room_no,
						'desc'=>$room_desc
			);
			$data_detail=array(
						'corporation_id'=>$corporation_id,
						'company_id'=>$company_id,
						'chk_func_save'=>$chk_func_save,
						'room_no_up'=>$room_no_up,
						'user_id'=>$user_id
			);
			$obj=new Model_Checkstock();
			$view=$obj->saveroom($data,$data_detail);
			$this->view->checkstatus=$view;
		}
		
		public function deleteroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$view=$obj->deleteroom($shelf_no,$floor_no,$room_no);
			$this->view->checkstatus=$view;
		}
		
		public function productonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$listproduct=$obj->listproduct();
			$datadetail=array('shelf_no'=>$shelf_no,'floor_no'=>$floor_no,'room_no'=>$room_no);
			$this->view->detaildata=$datadetail;
			$this->view->listproduct=$listproduct;
		}
		
		public function keyproductonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$listproduct=$obj->listproduct();
			$datadetail=array('shelf_no'=>$shelf_no,'floor_no'=>$floor_no,'room_no'=>$room_no);
			$this->view->detaildata=$datadetail;
			$this->view->listproduct=$listproduct;
			$this->view->doc_no=$doc_no;
		}
		
		public function addproducttoshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$get_product_id = $filter->filter($this->getRequest()->getParam("product_id")); 
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$aqty = $filter->filter($this->getRequest()->getParam("aqty"));
			$obj=new Model_Checkstock();
			$product_id=$obj->convproduct_id($get_product_id);
			if(empty($product_id[0]['product_id'])){
				$product_id[0]['product_id']=null;
			}
			$arr_data=array(
					'shelf_no'=>$shelf_no,
					'floor_no'=>$floor_no,
					'room_no'=>$room_no,
					'doc_no'=>$doc_no,
					'qty'=>$aqty,
					'product_id'=>$product_id[0]['product_id']
			);		
			$view=$obj->addproducttoshelf($arr_data);
			$this->view->viewstatus=$view;
		}
		
		public function viewproductonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$view=$obj->productonshelf($shelf_no,$floor_no,$room_no);
			$this->view->productdata=$view;
			$this->view->room=$room_no;
		}
		
		public function viewkeyproductonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no"));
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$page_seq = $filter->filter($this->getRequest()->getParam("page_seq"));
			$obj=new Model_Checkstock();
			$view=$obj->productlisttag($shelf_no,$floor_no,$room_no,$doc_no);
			$getqty=$obj->getquantity($doc_no,$shelf_no,$floor_no,$room_no);
			$this->view->qty=$getqty;
			$this->view->productdata=$view;
			$this->view->doc_no=$doc_no;
			$this->view->room_no=$room_no;
			$this->view->page_seq=$page_seq;
		}
		
		public function deleteproductonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("send_shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("send_floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("send_room_no"));
			//$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$product_id=$this->getRequest()->getParam("delproductid");
			$obj=new Model_Checkstock();
			$data=array(
						'shelf_no'=>$shelf_no,
						'floor_no'=>$floor_no,
						'room_no'=>$room_no
			);
			$view=$obj->deleteproductonshelf($data,$product_id);
		}
		
		public function deletelisttagAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("send_shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("send_floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("send_room_no"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$product_id=$this->getRequest()->getParam("delproductid");
			$obj=new Model_Checkstock();
			$data=array(
						'shelf_no'=>$shelf_no,
						'floor_no'=>$floor_no,
						'room_no'=>$room_no,
						'doc_no'=>$doc_no
			);
			$view=$obj->deletelisttag($data,$product_id);
		}
		
		public function reflisttagAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$view=$obj->reflisttag($room_no);
		}
		
		public function testpdfAction() {
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstock();
			$view=$obj->viewshelf("");
			$this->view->shelf=$view;
		}
		
		public function genlisttagAction() {
			$obj=new Model_Checkstock();
			$docno=$obj->getdocno();
			$this->view->doc_no=$docno;
			$shelf=$obj->viewshelf("");
			$this->view->shelf=$shelf;
		}
		
		public function printlisttagAction(){
			$this->_helper->layout()->disableLayout();
			$shelf_no = $this->getRequest()->getParam("shelf_no");
			//print_r($shelf_no);
			$obj=new Model_Checkstock();
			$view=$obj->selectviewshelf($shelf_no);
			//print_r($view);
			$genlisttag=$obj->genlisttag($view);
			$this->view->shop=$this->empprofile['branch_id'];
			//print_r($genlisttag);
			//echo $genlisttag;
			$this->view->doc_no=$genlisttag;
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
			
		}
		
		public function getdoconprintlisttagAction(){
			$this->_helper->layout()->disableLayout();
			$shelf_no = $this->getRequest()->getParam("shelf_no");
			//print_r($shelf_no);
			$obj=new Model_Checkstock();
			$view=$obj->selectviewshelf($shelf_no);
			//print_r($view);
			$genlisttag=$obj->genlisttag($view);
			//echo $genlisttag;
			$this->view->doc_no=$genlisttag;
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
			
		}
		
		public function checkdocnoAction() {
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstock();
			$view=$obj->checkdocno(1);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function printlisttagoldAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_old = $filter->filter($this->getRequest()->getParam("doc_no_old"));
			$obj=new Model_Checkstock();
			$arr_shelf=$obj->getshelflisttag($doc_no_old,"");
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$view=$obj->selectviewshelf($arr_shelf);
			$this->view->doc_no=$doc_no_old;
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		
		public function printshelfonAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf = $filter->filter($this->getRequest()->getParam("shelf"));
			$obj=new Model_Checkstock();
			//$arr_shelf=$obj->getshelflisttag($doc_no_old,"");
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			
			$shelf_no=json_decode($shelf,true);
			$view=$obj->selectviewshelf($shelf_no);
			$this->view->doc_no="shelf";
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function printcounttagAction(){
			/*$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no_pdf"));
			$shlef = $filter->filter($this->getRequest()->getParam("shlef"));
			//$view=$obj->selectviewshelf($arr_shelf);
			$this->view->doc_no=$doc_no;
			//$arr_json=json_encode($shlef);
			$this->view->jsondata=$shlef;*/
			
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf = $filter->filter($this->getRequest()->getParam("shelf"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			
			//$arr_shelf=$obj->getshelflisttag($doc_no_old,"");
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->doc_no=$doc_no;
			$this->view->jsondata=$shelf;
			
			
			/*$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no_old = $filter->filter($this->getRequest()->getParam("doc_no_pdf"));
			echo $doc_no_old;
			$obj=new Model_Checkstock();
			$arr_shelf=$obj->getshelflisttag($doc_no_old,"");
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$view=$obj->selectviewshelf($arr_shelf);
			$this->view->doc_no=$doc_no_old;
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;*/
			
			
		}
		
		
		public function printcounttagpaperAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf = $filter->filter($this->getRequest()->getParam("shelf"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			$this->view->doc_no=$doc_no;
			$this->view->jsondata=$shelf;
		}
		
		public function keylisttagAction(){
			$obj=new Model_Checkstock();
			$view=$obj->checkdocno(1);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function keyshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$get_product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$obj=new Model_Checkstock();
			if(!empty($get_product_id)){
				$product_id=$obj->convproduct_id($get_product_id);
				$product_id=$product_id[0]['product_id'];
				$this->view->search_product_id=$product_id;
			}else{
				$product_id="";
			}
			$arr_shelf=$obj->getshelflisttag($doc_no,$product_id);
			$view=$obj->selectviewshelftaglist($arr_shelf,$doc_no);
			$this->view->viewshelf=$view;
			$this->view->doc_no=$doc_no;
		}
		
		public function savelisttagAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$shelf_no = $filter->filter($this->getRequest()->getParam("send_shelf_no")); 
			$floor_no = $filter->filter($this->getRequest()->getParam("send_floor_no")); 
			$room_no = $filter->filter($this->getRequest()->getParam("send_room_no"));
			$chk_product_id = $filter->filter($this->getRequest()->getParam("chkproductid"));
			$arr_product_id = $this->getRequest()->getParam("arr_product_id");
			$arr_product_id=json_decode($arr_product_id,true);
			//print_r($arr_product_id);
			$obj=new Model_Checkstock();
			if(empty($arr_product_id)){return false;}
			foreach($arr_product_id as $val_product){
				$get_product_id="prod_".$val_product['product_id']."_".$val_product['seq'];
				//echo $get_product_id;
				$count_product_id=$filter->filter($this->getRequest()->getParam($get_product_id));
				//echo "--->---".$count_product_id;
				if($chk_product_id=="prod_".$val_product['product_id']."_".$val_product['seq']){
					$view=$obj->saveproductlisttag($val_product['product_id'],$val_product['seq'],$count_product_id,$doc_no,$shelf_no,$floor_no,$room_no);
				}
			}
		}
		
		public function gencountlisttagAction(){
			$obj=new Model_Checkstock();
			$view=$obj->checkdocno(1);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function getshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$arr_shelf=$obj->getshelflisttag($doc_no,"");
			$view=$obj->selectviewshelf($arr_shelf);
			$this->view->viewshelf=$arr_shelf;
			$this->view->doc_no=$doc_no;
		}
		
		public function printcountlisttagAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$shelf_no = $this->getRequest()->getParam("shelf_no");
			$obj=new Model_Checkstock();
			$view=$obj->selectcountviewshelf($shelf_no,$doc_no);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
			$this->view->doc_no=$doc_no;
		}
		
		public function difcheckAction(){
			$obj=new Model_Checkstock();
			$view=$obj->checkdocno(1);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
			$objPos=new SSUP_Controller_Plugin_PosGlobal();
           	$objPos->stockBalance($this->empprofile['corporation_id'],$this->empprofile['company_id'],$this->empprofile['branch_id'],$this->doc_date);	
		}
		
		public function getdifcheckAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$this->view->doc_no=$doc_no;
			$objPos=new SSUP_Controller_Plugin_PosGlobal();
           	$objPos->stockBalance($this->empprofile['corporation_id'],$this->empprofile['company_id'],$this->empprofile['branch_id'],$this->doc_date);	
		}
		
		public function datadifcheckAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$page = $filter->filter($this->getRequest()->getParam("page"));
			$sortname = $filter->filter($this->getRequest()->getParam("sortname"));
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder"));
			$rp = $filter->filter($this->getRequest()->getParam("rp"));
			$sort=$sortname." ".$sortorder;
			$this->view->doc_no=$doc_no;
			$tname="chk_dif_stock where doc_no='$doc_no'";
			$fname="product_id";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$obj=new Model_Checkstock();
			
			$view=$obj->difcheck($doc_no,$sort,$start,$rp);
			$total=$obj->countRec($fname,$tname);
			
			//print_r($view);
			$this->view->arr_data=$view;
			$this->view->page=$page;
			$this->view->sum=$page;
			$this->view->total=$total;
		}
		
		public function viewporductcheckdifAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$product_id=explode(",",$product_id);
			array_pop($product_id);
			
			$obj=new Model_Checkstock();
			$view=$obj->getproducdifstock($product_id);
			$this->view->arr_data=$view;
		}
		
		public function viewcheckdiforderbyAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$this->view->doc_no=$doc_no;
		}
		
		public function getarrdifstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$orderby = $filter->filter($this->getRequest()->getParam("orderby"));
			$difby = $filter->filter($this->getRequest()->getParam("difby"));
			$obj=new Model_Checkstock();
			$view=$obj->datadifcheck($doc_no,$orderby,$difby);
			/*echo "<pre>";
			print_r($view);
			echo "</pre>";*/
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
			$this->view->doc_no=$doc_no;
			$this->view->sel_rpt=$sel_rpt;
		}
		
		public function printcheckdifAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no_pdf"));
			$sel_rpt = $filter->filter($this->getRequest()->getParam("sel_rpt_pdf"));
			$arr_json = $filter->filter($this->getRequest()->getParam("product_id_pdf"));
			//print_r($dat);
			$this->view->jsondata=$arr_json;
			$this->view->doc_no=$doc_no;
			$this->view->sel_rpt=$sel_rpt;
		}
		
		public function balanceproductAction(){
			$obj=new Model_Checkstock();
			$view=$obj->checkdocno(1);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function getbalanceproductAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$view=$obj->viewbalanceproduct($doc_no);
			//print_r($view);
			$this->view->data=$view;
		}
		
		public function tranferstockAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$doc_date=$this->doc_date;
			$view=$obj->tranferstock($doc_no,$doc_date);
			$this->view->data=$view;
			
			//echo $this->doc_date;
			
		}
		
		public function updateseqroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$room = $filter->filter($this->getRequest()->getParam("room"));
			$obj=new Model_Checkstock();
			$product_id=$obj->getroom($room);
			//echo $product_id;
			if(empty($product_id)){return false;}
			foreach($product_id as $val_product){
				$get_product_id="seq_".$val_product['product_id'];
				$seq_product_id=$filter->filter($this->getRequest()->getParam($get_product_id));
				$view=$obj->saveproductseq($val_product['product_id'],$room,$seq_product_id);
			}
			$obj->updateseqproduct($room);
		}
		
		public function producttranferroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$floor_no = $filter->filter($this->getRequest()->getParam("floor_no"));
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$view=$obj->getroomtranfer();
			$data_product=$obj->productonshelf($shelf_no,$floor_no,$room_no);
			$this->view->data=$view;
			$this->view->room_no=$room_no;
			$this->view->data_product=$data_product;
			
		}
		
		public function gotranferroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$old_room_no = $filter->filter($this->getRequest()->getParam("send_room_no"));
			$new_room = $filter->filter($this->getRequest()->getParam("new_room"));
			$tranf_product = $this->getRequest()->getParam("tranf_product");
			$obj=new Model_Checkstock();
			$view=$obj->gotranferroom($old_room_no,$new_room,$tranf_product);
			$this->view->status=$view;
		}
		
		public function checkqtyonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$view=$obj->checkqtyonshelf($doc_no);
			$this->view->data=$view;
			$this->view->doc_no=$doc_no;
		}
		
		public function printqtyonshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$view=$obj->checkqtyonshelf($doc_no);
			$this->view->data=$view;
			$this->view->doc_no=$doc_no;
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
		}
		
		public function keyproductonroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Checkstock();
			$view=$obj->getproductroom($doc_no,$product_id);
			$this->view->data=$view;
			$this->view->product_id=$product_id;
			$this->view->doc_no=$doc_no;
		}
		
		public function savelisttagroomAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$qty = $filter->filter($this->getRequest()->getParam("qty"));
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$obj=new Model_Checkstock();
			$view=$obj->savelisttagroom($doc_no,$product_id,$room_no,$qty,$seq);
		}
		
		public function printshelfAction(){
			$this->_helper->layout()->disableLayout();
			$shelf_no = $this->getRequest()->getParam("shelf_no");
			//print_r($shelf_no);
			$obj=new Model_Checkstock();
			$view=$obj->selectviewshelf($shelf_no);
			//print_r($view);
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function printshelfnoAction(){
			$this->_helper->layout()->disableLayout();
			$shelf_no = $this->getRequest()->getParam("shelf");
			$shelf_no=json_decode($shelf_no,true);
			$obj=new Model_Checkstock();
			
			
			//print_r($shelf_no);
			$view=$obj->selectviewshelf($shelf_no);
			//print_r($view);
			$this->view->shop=$this->empprofile['branch_id'];
			$this->view->branch_name=$this->empprofile['branch_name'];
			//print_r($genlisttag);
			//echo $genlisttag;
			$this->view->doc_no="Shelf";
			$arr_json=json_encode($view);
			$this->view->jsondata=$arr_json;
		}
		
		public function frmpwdAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$this->view->data=$doc_no;
		}
		
		public function checkpasswordAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$pwd = $filter->filter($this->getRequest()->getParam("pwd"));
			$obj=new Model_Checkstock();
			$view=$obj->chk_password_checkstock($pwd,"AUDIT");
			$this->view->data=$view;
		}
		
		public function frmaddshelfAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			
		}
		
		public function delroomckAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$room_no = $filter->filter($this->getRequest()->getParam("room_no"));
			$obj=new Model_Checkstock();
			$view=$obj->delroomck($doc_no,$room_no);
			$this->view->data=$view;
		}
		
		public function tranfershelfmastertolisttagAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$shelf_no = $filter->filter($this->getRequest()->getParam("shelf_no"));
			$obj=new Model_Checkstock();
			$view=$obj->selectviewshelfmaster($shelf_no);
			$obj->tranfershelfmastertolisttag($view,$doc_no);
			
		}
		
		public function checkrunckAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstock();
			$view=$obj->checkrunck();
			$this->view->data=$view;
		}
		
		public function genshelfauditAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstock();
			$view=$obj->genshelfaudit();
			$this->view->data=$view;
		}
		
		public function viewgenlisttagAction(){
			$this->_helper->layout()->disableLayout();
			$obj=new Model_Checkstock();
			$docno=$obj->getdocno();
			$this->view->doc_no=$docno;
			$shelf=$obj->viewshelf("");
			$this->view->shelf=$shelf;
		}
	}
?>