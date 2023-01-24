<?php
	class PromotionController extends Zend_Controller_Action
	{


		public function init(){
		//Set no caching
		//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

			$this->initView();

			//header("Content-type:text/html; charset=tis-620"); 
		}//func
		
		function preDispatch()
        {
            $this->_helper->layout()->setLayout('default_layout');
            $session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile;
            if(!isset($empprofile)){
                $this->_redirect('/error/sessionexpire');
            }
            $this->view->session_employee_id=$empprofile['employee_id'];
            $this->CHECK_SESSION=$empprofile['employee_id'];
        }//func 
        
        
        
		public function indexAction()
		{	
			

			//$this->_helper->layout()->setLayout('sales_layout');
			$this->_helper->layout()->disableLayout();
			//$this->initView();
			//$this->view->baseUrl = $this->_request->getBaseUrl();	
			$myRun=new Model_Calpromotion();

			//select province to Listmenu
			$rselectTable=$myRun->list_product_nopro(''); 
            $this->view->assign('diary2',$rselectTable);
			
		}//func		
		
		public function mapproAction()
		{	
			$this->_helper->layout()->disableLayout();
		 	$myRun=new Model_Calpromotion();
			$filter = new Zend_Filter_StripTags();
			$doc_noSend = $filter->filter($this->getRequest()->getParam("doc_no"));
			$myRun->copyToTmp($doc_noSend);
			//clear tmp by doc_no
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$myRun->db->query("delete from trn_promotion_tmp1");
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");
			$myRun->db->query("delete from promo_last_net");
			//$myRun->playPro($doc_noSend,"one");
			//$myRun->playPro($doc_noSend,"more");
			//$myRun->playPro($doc_noSend,"no");
     	
           
			$doc_noSend=array("doc_noSend"=>$doc_noSend);
			echo json_encode($doc_noSend);
			
		}//func		

		
		public function selectproAction()
		{	
			//$this->_helper->layout()->setLayout('sales_layout');
			$this->_helper->layout()->disableLayout();
			$myRun=new Model_Calpromotion();
			$filter = new Zend_Filter_StripTags();
			$doc_noSend = $filter->filter($this->getRequest()->getParam("doc_no")); 
			
            //clear tmp
            $myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_step");
			$myRun->db->query("delete from promo_step");			
			
			
			
			
			$Pro=$myRun->selectPro($doc_noSend);
			if(count(json_decode($Pro))>1){//ถ้าต้องเลือกโปร
				$Pro=json_decode($Pro,true);
            	$this->view->assign('pro',$Pro);
			}else if(count($Pro)==1) {
				$Pro=json_decode($Pro,true);
				$promo_code=$Pro[0]['promo_code'];
				$product_id=$Pro[0]['product_id'];
				$seq=$Pro[0]['seq'];
				if($promo_code==""){
					echo 12;
				}else{
					echo "playpro('$promo_code','','$product_id','$seq');";
				}
				
				
			}
            
            
			
            
            //show list promotion

			
		}//func		
		
		public function detailproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code")); 
            $doc_no = $filter->filter($this->getRequest()->getParam("doc_no")); 
            $product_id = $filter->filter($this->getRequest()->getParam("product_id")); 
            $seq = $filter->filter($this->getRequest()->getParam("seq")); 
            $price = $filter->filter($this->getRequest()->getParam("price")); 
			
			$myRun=new Model_Calpromotion();
			$detailPro=$myRun->detailPro($promo_code);//แสดงStepการเล่นโปรโมชั่น
			$this->view->assign('detailPro',$detailPro);
			$this->view->assign('promo_code',$promo_code);
			$this->view->assign('doc_no',$doc_no);
			$this->view->assign('product_id',$product_id);
			$this->view->assign('seq',$seq);
			$this->view->assign('price',$price);
			
		}//func	
		
		
		
		public function addproductdetailAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->getRequest()->getParam("product_id")); 
            $quantity = $filter->filter($this->getRequest()->getParam("quantity")); 
            $compare=$filter->filter($this->getRequest()->getParam("compare"));
            $promo_code=$filter->filter($this->getRequest()->getParam("promo_code")); 
			$promo_seq=$filter->filter($this->getRequest()->getParam("promo_seq"));
			$myRun=new Model_Calpromotion();
			echo $myRun->addproductdetail($product_id,$quantity,$compare,$promo_code,$promo_seq);
			
		}//func	
		
		
		public function playproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code")); 
			$doc_noSend = $filter->filter($this->getRequest()->getParam("doc_no"));
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$price = $filter->filter($this->getRequest()->getParam("price"));
			
			$myRun=new Model_Calpromotion();
			
			
			
			
			//ตรวจสอบว่าสินค้ามีครบที่จะเล่นโปรหรือไม่
			$chkStep=$myRun->numProduct($doc_noSend,$promo_code,$product_id,$seq);//เล่นครบทุก Stepหรือไม่

			if($chkStep=="N") {//ของไม่ครบ
				echo $chkStep;
			} else if($chkStep=="Y") {
				$ansPro=$myRun->addPro($doc_noSend,$promo_code,$seq);
				echo $chkStep;
			}

				

			
		}//func	

		public function setproductnoproAction()
		{	
			

			//$this->_helper->layout()->setLayout('sales_layout');
			 $this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->getRequest()->getParam("product_id")); 
			$seq = $filter->filter($this->getRequest()->getParam("seq"));
			$myRun=new Model_Calpromotion();
			
			//ตรวจสอบว่าสินค้ามีครบที่จะเล่นโปรหรือไม่
			$set=$myRun->setproductnopro($product_id,$seq);
			echo $set;	

			
		}//func		
		
		public function playnoproAction()
		{	
			

			//$this->_helper->layout()->setLayout('sales_layout');
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code")); 
			
			$doc_noSend = $filter->filter($this->getRequest()->getParam("doc_no"));

			$myRun=new Model_Calpromotion();
			$myRun->playPro($doc_noSend,"no");
		}//func		
		
		
		
		public function showtblAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
			$myRun=new Model_Calpromotion();
			$doc_noSend = $filter->filter($this->getRequest()->getParam("doc_no"));
			$Rtmppro=$myRun->showtmppro($doc_noSend); 
            $this->view->assign('tmppro',$Rtmppro);
				

			
		}//func	

		
		
			public function hotprosearchAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
			$myRun=new Model_Calpromotion();
			
			
			$hotpro_product_search = $filter->filter($this->getRequest()->getParam("hotpro_product_search"));
			$hotpro_quantity_search = $filter->filter($this->getRequest()->getParam("hotpro_quantity_search"));
			$chkProduct=$myRun->dataProduct($hotpro_product_search);
			$this->view->assign('chkProduct',$chkProduct);
			$clearTmp2=$myRun->clearTmp2();
			if($clearTmp2==1){
				$Rhotpro=$myRun->dataHotPro($hotpro_product_search,$hotpro_quantity_search);
	            $this->view->assign('hotpro',$Rhotpro);
	            
			}
		}

			
		public function hotchkkeyAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
			$myRun=new Model_Calpromotion();
			
			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			//คืน Stock กรณีออกแบบปิด Firefox
			$myRun->cancle_stock();
			
			
			//clear
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");
			$myRun->db->query("delete from promo_last_net where computer_ip='$computer_ip' ");
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cancle");
			$myRun->db->query("delete from promo_last_cancle  where computer_ip='$computer_ip' ");

			
			
			
			$hotpro_product_search = $filter->filter($this->getRequest()->getParam("hotpro_product_search"));
			$hotpro_quantity_search = $filter->filter($this->getRequest()->getParam("hotpro_quantity_search"));
			$member_no=$filter->filter($this->getRequest()->getParam("member_no"));
			
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl_val");
			$myRun->db->query("update trn_tdiary2_sl_val set member_no='$member_no'  where computer_ip='$computer_ip' ");
			
			
			$chkProduct=$myRun->dataProduct($hotpro_product_search);
			if(empty($chkProduct)){
				$chkBarcode=$myRun->chkBarcodeProduct($hotpro_product_search);
				if($chkBarcode){
					$hotpro_product_search=$chkBarcode[0]['product_id'];
				}else{
					echo "Noproduct";
					return false;
				}
			}
			$chkStock=$myRun->dataStockProduct($hotpro_product_search,$hotpro_quantity_search);
			if(empty($chkStock)){
				echo "Nostock";
				return false;
			}else{
				$stock=$chkStock[0]['stock'];
				if($hotpro_quantity_search>$stock){
					echo "stockShort";
					return false;
				}
			}
		
			
			
			
			echo "Yes";
	
		}//func	
		
		
		
		public function hotproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
			$myRun=new Model_Calpromotion();
			
			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_chkbarcode");
			$myRun->db->query("delete from trn_promotion_chkbarcode  where computer_ip='$computer_ip' ");
			
			$hotpro_product_search = $filter->filter($this->getRequest()->getParam("hotpro_product_search"));
			$hotpro_quantity_search = $filter->filter($this->getRequest()->getParam("hotpro_quantity_search"));
			$new_member_2 = $filter->filter($this->getRequest()->getParam("new_member"));
			$chkProduct=$myRun->dataProduct($hotpro_product_search);
			if(empty($chkProduct)){
				$chkBarcode=$myRun->chkBarcodeProduct($hotpro_product_search);
				if($chkBarcode){
					$hotpro_product_search=$chkBarcode[0]['product_id'];
				}
			}
			
			$price=$chkProduct[0]['price'];
			if($price<=0 && ($hotpro_product_search!='25221' ||$hotpro_product_search!='25224' ||$hotpro_product_search!='25225' ||$hotpro_product_search!='25193')){
				$this->view->assign('chkprice','price_null');
				return false;
			}else{
				$this->view->assign('chkprice','price_ok');
			}
			
			
			$clearTmp2=$myRun->clearTmp2();
			if($clearTmp2==1){
				$Rhotpro=$myRun->dataHotPro($hotpro_product_search,$hotpro_quantity_search,$new_member_2);
				
				$dataConfig=$myRun->hotproconfig('');
				$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				$dataSeq1=$myRun->db->fetchAll("select sum(quantity) as sumSeq1 from trn_promotion_tmp2 where computer_ip='$computer_ip' and promo_seq='1'");	
				if($dataSeq1){
					$sumSeq1=$dataSeq1[0]['sumSeq1'];
				}else{
					$sumSeq1=0;
				}
				
				
	            $this->view->assign('hotpro',$Rhotpro);
	            $this->view->assign('dataConfig',$dataConfig);
				$this->view->assign('hotpro_product_search',$hotpro_product_search);
	            $this->view->assign('hotpro_quantity_search',$hotpro_quantity_search);
	            $this->view->assign('sumSeq1',$sumSeq1);
	            
			}
			
				

				

			
		}//func	
		
		public function openscanbarcodeAction(){//เปิดpopup ให้ยิงบาร์โค๊ต
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $myRun=new Model_Calpromotion();

            $this->view->assign('promo_code',$promo_code);
         
            
		}
		
		public function chkscanbarcodeAction(){//เปิดpopup ให้ยิงบาร์โค๊ต
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			
            $scan_barcode = $filter->filter($this->getRequest()->getParam("scan_barcode"));
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
      
            $myRun=new Model_Calpromotion();
            
            $chk_barcode=$myRun->chkbarcode($promo_code,$scan_barcode);
            if($chk_barcode=="Y"){
            	
            	$up="insert into trn_promotion_chkbarcode set promo_code='$promo_code',scan_barcode='$scan_barcode',computer_ip='$computer_ip' ";
            	$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_chkbarcode");
            	$myRun->query($up);
            	echo "Y";
            }else if($chk_barcode=="play_have"){
            	echo "play_have";
            }else{
            	echo "N";
            }
 			
            
            
		}

		
		
		public function chkstepAction(){
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $chk_barcode = $filter->filter($this->getRequest()->getParam("chk_barcode"));
            $myRun=new Model_Calpromotion();         
			
			
            
            $chkLimite_qty=$myRun->chkLimit_qty($promo_code);//เล่นได้กี่ครั้งต่อบิล
            
			$chkLimite_qtybyone=$myRun->chkLimit_qtybyone($promo_code);
			
            
			$dataHead=$myRun->dataPromoHead($promo_code);
			$coupon=$dataHead[0]['coupon'];
			$bundle=$dataHead[0]['bundle'];

            $barcode=$myRun->hotprobarcode($computer_ip);
            if($barcode){
            	$scan_barcode_chk=$barcode[0]['scan_barcode'];
			}else{
				$scan_barcode_chk="";
			}
            

            
            //chk ว่าเป็นโปรจับคู่หรือไม่
            $max_pro=$myRun->maxPro($promo_code);
            
            
			if(substr($chkLimite_qty,0,12)=="limite_false"){//เกินครั้งที่กำหนด
				$myRun->cancle_stock();
				echo $chkLimite_qty;
				
			} else if($coupon=="S" && $scan_barcode_chk=="") {//เปิดBlock scan barcode
				echo "open_scan_coupon";
			} else if($coupon=="I") {//เปิดBlock scan barcode
				echo "open_scan_code_mobile";
			} else if($coupon=="V") {//เปิดBlock verify by read IDCARD
				echo "alert_from_idcard";				
			} else if($coupon=="R" && $chk_barcode=="") {//Alert Only
				echo "alert_coupon_only";
			} else if($max_pro==2){
				
				if($promo_code == "2204S601" || $promo_code == "2108W302" || $promo_code == "2107N601" || $promo_code=="2101W302" || $promo_code=="OS06130114" || $promo_code=="OT03050815" || $promo_code=="OT03060815" || $promo_code=="OT03100815" || $promo_code=="OP03261115" || $promo_code=="OP03271115"  || $promo_code=="2007W302" || $promo_code=="2007W303" || $bundle=="a"){//ยกเว้น
					
					echo $myRun->stepPromotion($promo_code);
				}else{
					echo "open_promotion_auto";
				}
				
			}else{

            	echo $myRun->stepPromotion($promo_code);
			}
            
		}
		public function giproAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			//print_r($_SESSION);
			
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
			$id_card = $filter->filter($this->getRequest()->getParam("id_card"));
      
            
			$myRun=new Model_Calpromotion();
			 //config pro
			$proDetail=$myRun->seqDetailPromotionLimit($promo_code,'1');
			$type_discount=$proDetail[0]['type_discount'];
			$quantityPro=$proDetail[0]['quantity'];
			
			

			
			$datatmp2=$myRun->datatmp2('1');
			$quantity=$datatmp2[0]['quantity'];
			
			
			if($quantity>$quantityPro){//ถ้ายิงมามากกว่าที่กำหนดในโปรโมชั่น
				$set_quantity=ceil($quantity/$quantityPro);
			}else if($quantity<=$quantityPro){
				$set_quantity=1;
			}
			$this->view->assign('set_quantity',$set_quantity);
			
			
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_config");
			$myRun->db->query("delete from trn_promotion_config where computer_ip='$computer_ip' ");
			$addConfig="
			insert into trn_promotion_config 
				set 
				computer_no='$computer_no',
				computer_ip='$computer_ip',
				promo_code='$promo_code',
				set_quantity='$set_quantity' 
			";
			//echo $addConfig;
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_config");
			$myRun->query($addConfig);
			
			$uppromo_code="
			update  trn_promotion_tmp2
			set 
				computer_no='$computer_no',
				computer_ip='$computer_ip',
				promo_code='$promo_code',
				type_discount='$type_discount' 
			where
				computer_ip='$computer_ip'
			";
			
			$myRun->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
			$myRun->query($uppromo_code);
			
			
					
			$Rdetailhotpro=$myRun->dataDetailHotPro($promo_code,1);
            $this->view->assign('detailhotpro',$Rdetailhotpro);
 
            $maxPro=$myRun->maxPro($promo_code);
            $this->view->assign('maxPro',$maxPro);
            
            $doc_no='';
            $startSeq=$myRun->setQty($promo_code,$doc_no);
            $this->view->assign('startSeq',$startSeq);
			$this->view->assign('id_card',$id_card);
			//}

		}//func	
		
		
		public function openproautoAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			//print_r($_SESSION);
			
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
      
            
			$myRun=new Model_Calpromotion();
			$myRun->clear_tmp_auto();
			
			$this->view->assign('promo_code',$promo_code);
            

		}//func	
		
		
		public function runproautoAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			//print_r($_SESSION);
			
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
      
            
			$myRun=new Model_Calpromotion();
			
			

			$myRun->runproauto($promo_code,'2');
			$this->view->assign('promo_code',$promo_code);
			
			
            

		}//func	
		
		public function delallautoAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

			$read_session = new Zend_Session_Namespace('empprofile');
            $empprofile=$read_session->empprofile;
			$computer_ip=$empprofile['com_ip'];
			$computer_no=$empprofile['computer_no'];
			$computer_ip=$_SERVER['REMOTE_ADDR'];
			
			//print_r($_SESSION);
			
            $keepid = $filter->filter($this->getRequest()->getParam("keepid"));
      
            
			$myRun=new Model_Calpromotion();
			$data=$myRun->delallauto($keepid);

			
			
            

		}//func	
		
		
		public function findproAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            
            
			$myRun=new Model_Calpromotion();
			$data=$myRun->dataPromoHead($promo_code);
			$promo_des=$data[0]['promo_des'];
			echo "$promo_code - $promo_des";
			//$this->view->assign('promo_des',$promo_des);
            
			//}

		}//func	
		
		
		
		public function viewproAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));
            $set_quantity = $filter->filter($this->getRequest()->getParam("set_quantity"));
            
			$myRun=new Model_Calpromotion();

		            $dataScan=$myRun->dataScanHotPro();
		            $this->view->assign('scanhotpro',$dataScan);
		            
		            $this->view->assign('promo_code',$promo_code);
		            $this->view->assign('promo_seq',$promo_seq);
		            
		            $dataPro=$myRun->seqDetailPromotionLimit($promo_code,$promo_seq);
		            $type_discount=$dataPro[0]['type_discount'];
		            $discount=$dataPro[0]['discount'];
		            $discount=number_format($discount,0,'.',',');
		            $quantity=$dataPro[0]['quantity'];
		            
		            $yes_quantity=$quantity*$set_quantity;
					if(strtolower($type_discount)=="price1") {
						$desStep="สแกนสินค้าแลกซื้อ $yes_quantity ชิ้น";
					} else if(strtolower($type_discount)=="normal") {
						$desStep="โปรโมชั่นหลัก 1 ชิ้น<br>";
					} else if(strtolower($type_discount)=="free") {
						$desStep="สแกนสินค้าแถมฟรี<br> $yes_quantity ชิ้น";
					} else if(strtolower($type_discount)=="percent") {
						$desStep="แนะนำสินค้าใหม่ ลด<br> $discount%  $yes_quantity ชิ้น";
					} else if(strtolower($type_discount)=="discount") {
						$desStep="แนะนำสินค้า ลดราคา  $yes_quantity ชิ้น";
					}
					$this->view->assign('desStep',$desStep);
            
			//}

		}//func	
		
		
		public function viewproautoAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));
            $set_quantity = $filter->filter($this->getRequest()->getParam("set_quantity"));
            
			$myRun=new Model_Calpromotion();

			$this->view->assign('promo_code',$promo_code);
            $dataScan=$myRun->dataScanHotPro_auto();
            $this->view->assign('scanhotpro',$dataScan);

			
		}//func	
		
	
		
		
		public function chkendproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            
			$myRun=new Model_Calpromotion();

			$chkpricelimitpro = $myRun->chkpricelimitpro($promo_code);	//  check sum trn_promotion_tmp2.price > promo_other.promo_amt
			if($chkpricelimitpro == "N"){
				echo "chkpricelimitpro";
				return false;
			}
		
			$datatmp2=$myRun->datatmp2($promo_code);
			if(count($datatmp2)==1){//ถ้ายิงเข้ามาแล้วจบโปร แสดงว่าไม่เล่น
				echo "Y";
				return false;
			}else{//ถ้าเล่นโปรเกิน Step 1 แล้วให้ chk ตามที่กำหนดไ้ว้เมื่อกดจบโปร
				$dataHead=$myRun->dataPromoHead($promo_code);
				$re_calculate=$dataHead[0]['re_calculate'];
	            echo $re_calculate;
			}


		}//func	

		
		public function addproductAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));
            $product_id = $filter->filter($this->getRequest()->getParam("product_id"));
            $quantity = $filter->filter($this->getRequest()->getParam("quantity"));
            
			$myRun=new Model_Calpromotion();
			$RaddProduct=$myRun->addProduct($promo_code,$promo_seq,$product_id,$quantity);
            echo $RaddProduct;

		}//func	


		public function addhotproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

            
			$myRun=new Model_Calpromotion();
			
			
			$RaddHotPro=$myRun->addHotPro('HOTPRO');
            echo $RaddHotPro;

		}//func	
		
		
		public function addnohotproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            
			$myRun=new Model_Calpromotion();
			

			
			$RaddHotPro=$myRun->addNoHotPro($promo_code);
            echo $RaddHotPro;

		}//func	

		
		public function addnormalAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->getRequest()->getParam("product_id"));
            $quantity = $filter->filter($this->getRequest()->getParam("quantity"));
            
			$myRun=new Model_Calpromotion();
			
			//chk ทะเบียน
			$chkProduct=$myRun->dataProduct($product_id);
			if(empty($chkProduct)){
				$chkBarcode=$myRun->chkBarcodeProduct($product_id);
				if($chkBarcode){
					$product_id=$chkBarcode[0]['product_id'];
				}else{
					echo "Noproduct";
					return false;
				}
			}
			
				//chk price
				$chkProduct=$myRun->dataProduct($product_id);
				$price=$chkProduct[0]['price'];
				if($price<=0){
					echo "price_null";
					return false;
				}
			
			//chk stock
			$chkStock=$myRun->dataStockProduct($product_id,$quantity);
			if(empty($chkStock)){
				echo "Nostock";
				return false;
			}else{
				$stock=$chkStock[0]['stock'];
				if($quantity>$stock){
					echo "stockShort";
					return false;
				}
			}
			
			$Raddnormal=$myRun->addnormal($product_id,$quantity);
            echo $Raddnormal;

		}//func	
		
		
		
		
		public function addseqhotproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
      
            
			$myRun=new Model_Calpromotion();

			
			$RaddHotPro=$myRun->addSeqHotPro($promo_code);
            echo $RaddHotPro;

		}//func			
		
		
		
		public function addproductautoAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));
            $product_id = $filter->filter($this->getRequest()->getParam("product_id"));
            $quantity = $filter->filter($this->getRequest()->getParam("quantity"));
            
			$myRun=new Model_Calpromotion();
			$RaddProduct=$myRun->addProductauto($promo_code,$promo_seq,$product_id,$quantity);
            echo $RaddProduct;

		}//func	

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//start lastpromotion
		public function lastprochkAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$myRun=new Model_Calpromotion();

			$dataChkPro=$myRun->findProByAmtlastbill();
			
			
			if($dataChkPro=="stopAmt"){
				echo "stopAmt";
			}else if(count($dataChkPro)>1){//หลายโปร
				$this->view->assign('dataChkPro',$dataChkPro);
			}else if(count($dataChkPro)==1){//โปรเดียว
				
				
				$promo_code=$dataChkPro[0]['promo_code'];
				echo "lastgipro('$promo_code','1');";
			}
			
            
		}//func	
		
		public function lastprochkqtyAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$myRun=new Model_Calpromotion();
			$dataChkPro=$myRun->findProByQtylastbill();
			if($dataChkPro=="stopQty"){
				echo "stopQty";
			}else if(count($dataChkPro)>1){//หลายโปร
				$this->view->assign('dataChkPro',$dataChkPro);
			}else if(count($dataChkPro)==1){//โปรเดียว
				$promo_code=$dataChkPro[0]['promo_code'];
				echo "lastgipro('$promo_code','1');";
			}
			
            
		}//func	
		
		public function lastgiproAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
			$id_card = $filter->filter($this->getRequest()->getParam("id_card"));
            
			$myRun=new Model_Calpromotion();
				
			$Rdetailhotpro=$myRun->dataDetailHotPro($promo_code,'1');
            $this->view->assign('detailhotpro',$Rdetailhotpro);
            
            //$datapro=$myRun->datapromocute($promo_code);

            $maxPro=$myRun->maxPro($promo_code);
            $this->view->assign('maxPro',$maxPro);
            
            $dataSet=$myRun->datapromocute($promo_code);
            $set_quantity=$dataSet[0]['set_quantity'];
            $this->view->assign('set_quantity',$set_quantity);
			$this->view->assign('id_card',$id_card);
			
			$datapro=$myRun->dataPromoHead($promo_code);
			$this->view->assign('datapro',$datapro);
			

			if($promo_code=="OS07110116"){
				$chkstock_step1=$myRun->chkstock("25041@25042@25043@25044",15);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				$chkstock_step2=$myRun->chkstock("25045@25046@25047@25048",15);
				$chkstock_step2_arr=explode("@",$chkstock_step2);
				if($chkstock_step1_arr[0]=="Y" && $chkstock_step2_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}
			}else if($promo_code=="OX08030915"){
				$chkstock_step1=$myRun->chkstock("26098@26101",15);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				$chkstock_step2=$myRun->chkstock("26098@26101",15);
				$chkstock_step2_arr=explode("@",$chkstock_step2);
				if($chkstock_step1_arr[0]=="Y" && $chkstock_step2_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}
			}else if($promo_code=="OX08040915"){
				$chkstock_step1=$myRun->chkstock("26102@26166",15);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				$chkstock_step2=$myRun->chkstock("26102@26166",15);
				$chkstock_step2_arr=explode("@",$chkstock_step2);
				if($chkstock_step1_arr[0]=="Y" && $chkstock_step2_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}	
			}else if($promo_code=="OX08211215"){
				$chkstock_step1=$myRun->chkstock("26102@26166",15);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				$chkstock_step2=$myRun->chkstock("26102@26166",15);
				$chkstock_step2_arr=explode("@",$chkstock_step2);
				if($chkstock_step1_arr[0]=="Y" && $chkstock_step2_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}		
			}else if($promo_code=="OX08250116"){
				$chkstock_step1=$myRun->chkstock("25874",3);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				if($chkstock_step1_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}
			}else if($promo_code=="OX08210515"){
				$chkstock_step1=$myRun->chkstock("25647@25648@25649@25650@25955@25956@25957@25958",10);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				if($chkstock_step1_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}	
			}else if($promo_code=="OX08080616"){
				$chkstock_step1=$myRun->chkstock("26227@26228@26229@26230",10);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				if($chkstock_step1_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}	
			}else if($promo_code=="OX07290616"){
				$chkstock_step1=$myRun->chkstock("22143@22144@22148@22152@22218@23644@24273@24916@25230@25350@25507",10);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				if($chkstock_step1_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}
			}else if($promo_code=="OX15300616"){
				$chkstock_step1=$myRun->chkstock("9000695",30);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				if($chkstock_step1_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}		
			}else if($promo_code=="OX07400616"){
				$chkstock_step1=$myRun->chkstock("25390@25391@25392@25393@25443@25447@25448@25450@25451@25453@25454",10);
				$chkstock_step1_arr=explode("@",$chkstock_step1);
				
				if($chkstock_step1_arr[0]=="Y"){
					$this->view->assign('chkstock',$chkstock_step1);
				}else{
					$this->view->assign('chkstock',"N@0@0");
				}							
			}else{
				$this->view->assign('chkstock',"N@0@0");
			}
			
			
			

		}//func	
		
		
		public function lastviewproAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));
            $set_quantity = $filter->filter($this->getRequest()->getParam("set_quantity"));
         
            
			$myRun=new Model_Calpromotion();

		            $dataScan=$myRun->dataScanHotPro();
		            $this->view->assign('scanhotpro',$dataScan);
		            $this->view->assign('promo_code',$promo_code);
		            $this->view->assign('promo_seq',$promo_seq);
		            
		            //list product by seq
		            /*$dataDetailSeq=$myRun->seqDetailPromotion($promo_code,$seq_pro);
		            $this->view->assign('dataDetailSeq',$dataDetailSeq);*/
		            
		            $dataPro=$myRun->seqDetailPromotionLimit($promo_code,$promo_seq);
		            $type_discount=$dataPro[0]['type_discount'];
		            $discount=$dataPro[0]['discount'];
		            $discount=number_format($discount,0,'.',',');
		            $quantity=$dataPro[0]['quantity'];
		            
		            $yes_quantity=$quantity*$set_quantity;
					if(strtolower($type_discount)=="price1") {
						$desStep="Scan products for redeem $yes_quantity";
					} else if(strtolower($type_discount)=="normal") {
						$desStep="Main promotioin for 1<br>";
					} else if(strtolower($type_discount)=="free") {
						$desStep="Scan products for<br>free $yes_quantity ";
					} else if(strtolower($type_discount)=="percent") {
						$desStep="Recommend products for discount<br> $discount%  $yes_quantity ชิ้น";
					} else if(strtolower($type_discount)=="discount") {
						$desStep="Recommend products for discount  $yes_quantity ชิ้น";
					}
					
					/*
					if(strtolower($type_discount)=="price1") {
						$desStep="Scan products for redeem $yes_quantity ชิ้น";
					} else if(strtolower($type_discount)=="normal") {
						$desStep="โปรโมชั่นหลัก 1 ชิ้น<br>";
					} else if(strtolower($type_discount)=="free") {
						$desStep="Scan products for <br>free $yes_quantity";
					} else if(strtolower($type_discount)=="percent") {
						$desStep="แนะนำสินค้าใหม่ ลด<br> $discount%  $yes_quantity ชิ้น";
					} else if(strtolower($type_discount)=="discount") {
						$desStep="แนะนำสินค้า ลดราคา  $yes_quantity ชิ้น";
					}
					*/
					
					$this->view->assign('desStep',$desStep);
					
					
					//chk limit pro
					$find_pro="select * from promo_head where promo_code='$promo_code' ";
					//echo $del_pro;
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$data_pro=$this->db->fetchAll($find_pro, 2);
					$start_date=$data_pro[0]['start_date'];
					$end_date=$data_pro[0]['end_date'];
								
					
					if($promo_code=="OX08020814"){
						$datapos=$myRun->dataPos();
						$member_no=$datapos[0]['member_no'];
						$doc_date=$datapos[0]['doc_date'];

							$ans_chk=$myRun->clear_probymonth_onlimit($promo_code,$start_date,$end_date,$member_no,$doc_date,10000,3);
						

						if($ans_chk=='Del'){
							$msg_limit="<center><span style='color: #FF0000'>โปรโมชั่นนี้ถูกกำหนดให้เล่นได้ 3 ครั้งต่อเดือน<br>เดือนนี้ลูกค้าเล่นครบ 3 ครั้งแล้วจึงไม่สามารถเล่นได้อีก<br>ต้องรอเดือนหน้าครับ</span></center>";
							$this->view->assign('msg_limit',$msg_limit);
						}
					}	
					
					/*if($promo_code=="OX07571014" || $promo_code=="OX07581014"){
						$datapos=$myRun->dataPos();
						$member_no=$datapos[0]['member_no'];
						$doc_date=$datapos[0]['doc_date'];
						$ans_chk=$myRun->clear_probyone($promo_code,$member_no,$doc_date);
						
						if($ans_chk=='Del'){
							$msg_limit="<center><span style='color: #FF0000'>โปรโมชั่นนี้ถูกกำหนดให้สมาชิกเล่นได้คนละ 1 ครั้งเท่านั้น และสมาชิกได้เล่นครบแล้วครับ</span></center>";
							$this->view->assign('msg_limit',$msg_limit);
						}
					}		*/								
            
			//}

		}//func	
		
		
		public function viewproductAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));

         
            
			$myRun=new Model_Calpromotion();


            //list product by seq
            $dataDetailSeq=$myRun->seqDetailPromotionName($promo_code,$promo_seq);
            $this->view->assign('dataDetailSeq',$dataDetailSeq);
		            
		           
            
			//}

		}//func	
		
		public function viewproductautoAction()
		{	
			
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));

         
            
			$myRun=new Model_Calpromotion();


            //list product by seq
            $dataDetailSeq=$myRun->seqDetailPromotionName_auto($promo_code);
            $this->view->assign('dataDetailSeq',$dataDetailSeq);
		            
		           
            
			//}

		}//func	
		
		
		
		
		public function lastaddproductAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            $promo_seq = $filter->filter($this->getRequest()->getParam("promo_seq"));
            $product_id = $filter->filter($this->getRequest()->getParam("product_id"));
            $quantity = $filter->filter($this->getRequest()->getParam("quantity"));
			$lot_dt = $filter->filter($this->getRequest()->getParam("lot_dt"));
            
			$myRun=new Model_Calpromotion();
			$RaddProduct=$myRun->lastaddProduct($promo_code,$promo_seq,$product_id,$quantity,$lot_dt);
            echo $RaddProduct;

		}//func	
		
		
		
		public function lastaddhotproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

			$myRun=new Model_Calpromotion();
			

			
			$RaddHotPro=$myRun->addLastHotPro('LASTPRO');
            echo $RaddHotPro;

		}//func	
		
		
		public function canclelastproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            
			$myRun=new Model_Calpromotion();
			$myRun->canclelastpro_one($promo_code);

		}//func	
		
		
		public function canclelastprolevelAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code"));
            
			$myRun=new Model_Calpromotion();
			$myRun->canclelastpro($promo_code);

		}//func	
		
		
		
		public function canclestockAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$myRun=new Model_Calpromotion();
			$myRun->cancle_stock();

		}//func	
		
		public function canclestockautoAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$myRun=new Model_Calpromotion();
			$myRun->cancle_stock_auto();

		}//func	
		
		public function lastdelproAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            
			$myRun=new Model_Calpromotion();
			$myRun->lastdelpro();

		}//func	
		
		
		
		public function deltmp1Action()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $seq = $filter->filter($this->getRequest()->getParam("seq"));
			$myRun=new Model_Calpromotion();
			$myRun->deltmp1($seq);

		}//func	
		
		public function mailpromotionAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

			$myRun=new Model_Calpromotion();
			echo $myRun->mail_promotion();

		}//func	
		
		


		public function fromidcardAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

			$myRun=new Model_Calpromotion();
			$member_no = $filter->filter($this->getRequest()->getParam("member_no"));
			
			$fp = @fopen("http://crmopkh.ssup.co.th/app_service_opkh/process/api_profile.php?member_no=$member_no", "r");
            $text=@fgetss($fp, 4096);
			
			$this->view->assign('member_no',$member_no);
			$this->view->assign('profile',$text);
		}//func	

		public function addidcardAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

            
			$myRun=new Model_Calpromotion();
			$member_no = $filter->filter($this->getRequest()->getParam("keep_member_no"));
			$customer_id = $filter->filter($this->getRequest()->getParam("keep_customer_id"));
			$id_card = $filter->filter($this->getRequest()->getParam("keep_id_card"));
			//$shop=$this->shop;
			$data_branch=$myRun->data_branch();
			$shop=$data_branch[0]['branch_id'];

			$fp = @fopen("http://crmopkh.ssup.co.th/app_service_opkh/process/api_add_survey_profile.php?member_no=$member_no&customer_id=$customer_id&id_card=$id_card&shop=$shop", "r");
            $text=@fgetss($fp, 4096);
			echo $text;
		}//func	
		
		public function addidcardcancleAction()
		{	
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();

            
			$myRun=new Model_Calpromotion();
			$member_no = $filter->filter($this->getRequest()->getParam("keep_member_no"));
			$customer_id = $filter->filter($this->getRequest()->getParam("keep_customer_id"));
			$id_card = $filter->filter($this->getRequest()->getParam("keep_id_card"));
			//$shop=$this->shop;
			$data_branch=$myRun->data_branch();
			$shop=$data_branch[0]['branch_id'];

			$fp = @fopen("http://crmopkh.ssup.co.th/app_service_opkh/process/api_cancle_survey_profile.php?member_no=$member_no&customer_id=$customer_id&id_card=$id_card&shop=$shop", "r");
            $text=@fgetss($fp, 4096);
			echo $text;
		}//func	
		
		
		
		
	}//class	
	
?>
