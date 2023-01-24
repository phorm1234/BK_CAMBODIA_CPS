<?php
	class SetpromotionController extends Zend_Controller_Action
	{
		public function init(){
			$this->initView();
			//header("Content-type:text/html; charset=tis-620"); 
		}//func
		
		public function indexAction()
		{	
			$this->_helper->layout()->disableLayout();
			
		}//func		
		
		public function addproAction()
		{
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code")); 
            $promo_des = $filter->filter($this->getRequest()->getParam("promo_des")); 
            $promo_pos = $filter->filter($this->getRequest()->getParam("promo_pos")); 
            $level = $filter->filter($this->getRequest()->getParam("level")); 
            $process = $filter->filter($this->getRequest()->getParam("process")); 
            $start_date = $filter->filter($this->getRequest()->getParam("start_date")); 
            $end_date = $filter->filter($this->getRequest()->getParam("end_date")); 
            $compare = $filter->filter($this->getRequest()->getParam("compare")); 
            $check_repeat = $filter->filter($this->getRequest()->getParam("check_repeat")); 
            $seq_pro = $filter->filter($this->getRequest()->getParam("seq_pro")); 
            $product_id = $filter->filter($this->getRequest()->getParam("product_id")); 
            $quantity = $filter->filter($this->getRequest()->getParam("quantity")); 
            $type_discount = $filter->filter($this->getRequest()->getParam("type_discount")); 
            $discount = $filter->filter($this->getRequest()->getParam("discount")); 
            $weight=$filter->filter($this->getRequest()->getParam("weight")); 

            
			$myRun=new Model_Setpromotion();


			$chkHavePro=$myRun->chkHeadPro($promo_code);
			if($chkHavePro==1){//มีหัวแล้ว
				$myRun->insertProdetail($promo_code,$promo_pos,$seq_pro,$product_id,$quantity,$type_discount,$discount,$weight);
			}else{
				$myRun->insertProHead($promo_code,$promo_des,$promo_pos,$level,$process,$start_date,$end_date,$compare,$check_repeat);
				$myRun->insertProdetail($promo_code,$promo_pos,$seq_pro,$product_id,$quantity,$type_discount,$discount,$weight);
			}
			
		}
		
		
			public function showlistproAction()
		{
			$this->_helper->layout()->disableLayout();
            $filter = new Zend_Filter_StripTags();
            $promo_code = $filter->filter($this->getRequest()->getParam("promo_code")); 

            
			$myRun=new Model_Setpromotion();


			$dataList=$myRun->showlistpro($promo_code);
			$this->view->assign('dataList',$dataList);

			
		}
		
		
		
	}