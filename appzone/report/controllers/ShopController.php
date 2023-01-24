<?php 
	class ShopController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('report_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$this->brand=$path_config_ini->common->params->brand;
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
                   // $this->_helper->layout()->setLayout("report_layout");
                   //$this->_helper->layout()->disableLayout();
                    /*$obj=new Model_Report();
			$detail=$obj->getquotadetail();
			print_r($detail);*/
                    
                    
                    
                    
                }
                
        public function salesummaryreportAction(){
                  // $this->_helper->layout()->setLayout("report_layout");
                  //$this->_helper->layout()->disableLayout();
                   /*$obj=new Model_Report();
           $detail=$obj->getquotadetail();
           print_r($detail);*/
               }
   		public function viewsalesummaryAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                                      $obj=new Model_Report();
                                              $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id'];                                          //  $this->view->branch = $this->branch_no;
                                              $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                                            $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                                              $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                                              $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  }
                                               $data=$obj->getsalesummary($dayfrom,$tofrom);
                       $this->view->get_data = $data;
                                    } //end post
                     } //end action
        public function printsalesummaryAction(){
                  $this->_helper->layout()->disableLayout();
                                                                                         if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                          $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getsalesummary($dayfrom,$tofrom);
                       $this->view->get_data = $data;
  
                                                    
                                                      
                         } //end post
                                                                                           
                   } //end action 
                   
		public function productsummarytiAction(){
                  // $this->_helper->layout()->setLayout("report_layout");
                  //$this->_helper->layout()->disableLayout();
                   /*$obj=new Model_Report();
           $detail=$obj->getquotadetail();
           print_r($detail);*/
               }
        public function viewproductsummarytiAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                                      $obj=new Model_Report();
                                              $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name'];
                       $this->view->branch_id=$this->empprofile['branch_id']; 
                                                                  //  $this->view->branch = $this->branch_no;
                                              $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                                            $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                                              $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                                              $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  }
                                               $data=$obj->getproductsummary($dayfrom,$tofrom,'TI');
                       $this->view->get_data = $data;
                                    } //end post
                     } //end action
           public function printproductsummarytiAction(){
                  $this->_helper->layout()->disableLayout();
                                                                                         if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                         $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getproductsummary($dayfrom,$tofrom,'TI');
                       $this->view->get_data = $data;
  
                         //print_r($data);                           
                                                      
                         } //end post
                                                                                           
                   } //end action  

		public function productsummarytoAction(){
                  // $this->_helper->layout()->setLayout("report_layout");
                  //$this->_helper->layout()->disableLayout();
                   /*$obj=new Model_Report();
           $detail=$obj->getquotadetail();
           print_r($detail);*/
               }       
        public function viewproductsummarytoAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                                      $obj=new Model_Report();
                                              $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id']; 
                                                                 //  $this->view->branch = $this->branch_no;
                                              $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                                            $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                                              $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                                              $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  }
                                               $data=$obj->getproductsummary($dayfrom,$tofrom,'TO');
                       $this->view->get_data = $data;
                                    } //end post
                     } //end action  

           public function printproductsummarytoAction(){
                  $this->_helper->layout()->disableLayout();
                                                                                         if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];    
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                       $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getproductsummary($dayfrom,$tofrom,'TO');
                       $this->view->get_data = $data;
  
                                                    
                                                      
                         } //end post
                                                                                           
                   } //end action  
           public function productsummarycnAction(){
                 
               } 
           public function viewproductsummarycnAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                                      $obj=new Model_Report();
                                              $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id']; 
                                                                 //  $this->view->branch = $this->branch_no;
                                              $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                                            $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                                              $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                                              $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  }
                                               $data=$obj->getproductsummary($dayfrom,$tofrom,'CN');
                       $this->view->get_data = $data;
                                    } //end post
                     } //end action     
              public function printproductsummarycnAction(){
                  $this->_helper->layout()->disableLayout();
                                                                                         if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                         $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getproductsummary($dayfrom,$tofrom,'CN');
                       $this->view->get_data = $data;
  
                                                    
                                                      
                         } //end post
                                                                                           
                   } //end action   
          public function couponAction(){
                  // $this->_helper->layout()->setLayout("report_layout");
                  //$this->_helper->layout()->disableLayout();
                   /*$obj=new Model_Report();
           $detail=$obj->getquotadetail();
           print_r($detail);*/
               } 
          public function viewcouponAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id'];  //  $this->view->branch = $this->branch_no;
                       $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                       $couponstart = $filter->filter($this->_request->getPost('data3'));
                       $couponend = $filter->filter($this->_request->getPost('data4'));
                       $this->view->c_start =  $couponstart;
                       $this->view->c_end = $couponend;
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                    /*   $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }*/
                            $data=$obj->getcoupon($dayfrom,$tofrom,$couponstart,$couponend);
                       		$this->view->get_data = $data;
                       		
                       		$cndata=$obj->getcountcncoupon($dayfrom,$tofrom,$couponstart,$couponend);
                       		foreach($cndata as $cc){
                       		$this->view->get_cndata =$cc['flag'];
                       		}
                       		
                        } //end post
                     } //end action  
           public function printcouponAction(){
                  $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                         
                       $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $couponstart = $filter->filter($this->_request->getPost('cstart'));
                       $couponend = $filter->filter($this->_request->getPost('cend'));
                       $this->view->c_start =  $couponstart;
                       $this->view->c_end = $couponend;
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getcoupon($dayfrom,$tofrom,$couponstart,$couponend);
                       $this->view->get_data = $data;
                       
                       $cndata=$obj->getcountcncoupon($dayfrom,$tofrom,$couponstart,$couponend);
                       		foreach($cndata as $cc){
                       		$this->view->get_cndata =$cc['flag'];
                       		}                               
                                                      
                         } //end post
                                                                                           
                   } //end action 
         
         public function viewcountcouponAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id'];  //  $this->view->branch = $this->branch_no;
                       $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                       $couponstart = $filter->filter($this->_request->getPost('data3'));
                       $couponend = $filter->filter($this->_request->getPost('data4'));
                       $this->view->c_start =  $couponstart;
                       $this->view->c_end = $couponend;
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                    /*   $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }*/
                            $data=$obj->getcountcoupon($dayfrom,$tofrom,$couponstart,$couponend);
                       		$this->view->get_data = $data;
                        } //end post
                     } //end action  
         public function printcountcouponAction(){
                  $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                         
                       $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $couponstart = $filter->filter($this->_request->getPost('cstart'));
                       $couponend = $filter->filter($this->_request->getPost('cend'));
                       $this->view->c_start =  $couponstart;
                       $this->view->c_end = $couponend;
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getcountcoupon($dayfrom,$tofrom,$couponstart,$couponend);
                       $this->view->get_data = $data;
                                                      
                                                      
                         } //end post
                                                                                           
                   } //end action 
                      
			public function taxAction(){
                   
                }     
            public function viewtaxAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                                      $obj=new Model_Report();
                                              $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id'];                                           //  $this->view->branch = $this->branch_no;
                                              $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                       $type = $filter->filter($this->_request->getPost('type'));
                                            $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                                              $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                                              $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                       
                       if($type == '2'){
                       		$data_sl=$obj->gettax_sl($dayfrom,$tofrom);
                       		$this->view->get_data_sl = $data_sl;
                       		
                       		$data_vt=$obj->gettax_vt($dayfrom,$tofrom);
                       		$this->view->get_data_vt = $data_vt;
                        	$this->view->type = '2';
                        }else{
                        	/*$data2=$obj->gettax2($dayfrom,$tofrom);
                       		$this->view->get_data2 = $data2;*/
                       		
                       		$data_sl=$obj->gettax2_sl($dayfrom,$tofrom);
                       		$this->view->get_data_sl = $data_sl;
                       		
                       		
                       		$data_vt=$obj->gettax2_vt($dayfrom,$tofrom);
                       		$this->view->get_data_vt = $data_vt;
                       		//$this->view->days = substr($dayfrom,8,2);
                       		//$this->view->daye = substr($tofrom,8,2);
                        	$this->view->type = '1';	
                        }
                                    } //end post
                     } //end action 

               public function printtaxAction(){
                  $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
						$this->view->company_name =$this->brand;
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$obj->company_id;                                                                                         
                       $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $type = $filter->filter($this->_request->getPost('type'));
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       
						$this->view->type = $type;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           		$this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                	if($type == '2'){	
		               $data_sl=$obj->gettax_sl($dayfrom,$tofrom);
		               $this->view->get_data_sl = $data_sl;
		               		
		               	$data_vt=$obj->gettax_vt($dayfrom,$tofrom);
		               	$this->view->get_data_vt = $data_vt;
			}else{
				$data_sl=$obj->gettax2_sl($dayfrom,$tofrom);
                       		$this->view->get_data_sl = $data_sl;
                       		
                       		$data_vt=$obj->gettax2_vt($dayfrom,$tofrom);
                       		$this->view->get_data_vt = $data_vt;
			}
  
                                                    
                                                      
                         } //end post
                                                                                           
                   } //end action 
				public function cnAction(){
                   
                }
                public function viewcnAction(){
                 $this->_helper->layout()->disableLayout();
                 if ($this->getRequest()->isPost()) {
                                      $obj=new Model_Report();
                                              $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                       $this->view->branch_name=$this->empprofile['branch_name']; 
                       $this->view->branch_id=$this->empprofile['branch_id'];                                           //  $this->view->branch = $this->branch_no;
                                              $filter = new Zend_Filter_StripTags();
                       
                       $dayfrom = $filter->filter($this->_request->getPost('data1'));
                       $tofrom = $filter->filter($this->_request->getPost('data2'));
                                            $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                                              $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                                              $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       $detail=$obj->chkmonth($fmonth);
                       $this->view->s_date= $fday."/".$fmonth."/".$fyear;
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  }
                            $data=$obj->getcn($dayfrom,$tofrom);
                       $this->view->get_data = $data;
                     } //end post
               } //end action 
               public function printcnAction(){
                  $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                         
                       $filter = new Zend_Filter_StripTags();
                       $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                       $tofrom = $filter->filter($this->_request->getPost('end_date'));
                       $this->view->dayfrom = $dayfrom;
                       $this->view->tofrom = $tofrom;
                       $fday = substr($dayfrom, -2);
                       $fmonth = substr($dayfrom, 5, 2);
                       $fyear = substr($dayfrom, 0,4);
                       $tday = substr($tofrom,-2);
                       $tmonth = substr($tofrom, 5, 2);
                       $tyear = substr($tofrom, 0,4);
                       
                       $detail=$obj->chkmonth($fmonth);
           			   $this->view->s_date= $fday."/".$fmonth."/".$fyear;
           			   
                       $detail2=$obj->chkmonth($tmonth);
                       $this->view->e_date= $tday."/".$tmonth."/".$tyear;
                       
                       $get_doc_date = $obj->getdoc_date();
                       foreach($get_doc_date as $a){
                            $this->view->get_doc_date = $a['doc_date'];                                                  
                       }
                		
                       $data=$obj->getcn($dayfrom,$tofrom);
                       $this->view->get_data = $data;
  
                                                    
                                                      
                         } //end post
                                                                                           
                   } //end action 
                   
                   public function keyinpoAction(){
		           $this->_helper->layout()->disableLayout();
		           $branch_id=$this->empprofile['branch_id'];
		          
		           $obj=new Model_Report();
		           $day =date('d');
		           if($day > '05'){
		           	$month = date('m');
		           }else{
		           	$month = date('m')-1;
		           }
		           $year = date('Y');
		           $now_date = $year."-".$month ;
		           //echo $branch_id; 
		           $get_com_po = $obj->get_com_po($now_date,$branch_id);
		       	   $this->view->get_com_po = $get_com_po;
		           $get_data_po = $obj->getpo($now_date,$branch_id);
		           $this->view->get_data_po = $get_data_po;
		           
		           //echo count($get_com_po) ;
		           if(count($get_com_po) < 1){
		           	date_default_timezone_set ( "Asia/Bangkok" );
				$m = date ( "m" ); 
				//$m = '09'; 
				$y = date ( "Y" ); 
				if($m == '4' || $m == '6' || $m == '9' || $m == '11'){
					$d = '30' ;
				}if($m == '2'){
					$d = '29';
				}else{
					$d = '31';			
				}
				//echo $m;
		           	for($i = 1; $i <= $d; $i ++) {
		           		if($i < '10'){
		           			$i = '0'.$i;
		           		}
		           		$date = $y."-".$m."-".$i;
		           		$get_data_po2 = $obj->getpo2($date,$branch_id);
		           		if(count($get_data_po2) > 0){
				   		foreach($get_data_po2 as $v){
				   			$amount = $v['amount'] ;
				   		}
				   	}else{
				   		$amount = '0' ;	
					}
					$obj=new Model_Report();
					//echo $date ." ". $amount ."<br>";
					$get_po = $obj->add_po($branch_id,$date,$amount);
		           	}
			   }else{
			   	date_default_timezone_set ( "Asia/Bangkok" );
				$m = date ( "m" ); 
				//$m = '09'; 
				$y = date ( "Y" ); 
				if($m == '4' || $m == '6' || $m == '9' || $m == '11'){
					$d = '30' ;
				}if($m == '2'){
					$d = '29';
				}else{
					$d = '31';			
				}
				//echo $m;
		           	for($i = 1; $i <= $d; $i ++) {
		           		if($i < '10'){
		           			$i = '0'.$i;
		           		}
		           		$date = $y."-".$m."-".$i;
		           		$get_data_po2 = $obj->getpo2($date,$branch_id);
		           		if(count($get_data_po2) > 0){
				   		foreach($get_data_po2 as $v){
				   			$amount = $v['amount'] ;
				   		}
				   	}else{
				   		$amount = '0' ;	
					}
					$obj=new Model_Report();
					//echo $date ." ". $amount ."<br>";
					$get_po = $obj->set_po2($date,$brand_id,$amount);
				}
			   }
                   		$get_com_po = $obj->get_com_po($now_date,$branch_id);
		       	   	$this->view->get_com_po = $get_com_po;
                   }
                    public function poupdateAction(){
                   	$this->_helper->layout()->disableLayout();
                   	
                   	$branch_id=$this->empprofile['branch_id'];
                   	if ($this->getRequest()->isPost()) {
                   		$filter = new Zend_Filter_StripTags();
                   		$loop = $filter->filter($this->_request->getPost('total'));
                   		/*$add_po_1 = $filter->filter($this->_request->getPost('amt_po1'));
                   		$add_po_2 = $filter->filter($this->_request->getPost('amt_po2'));*/
		           	for ($i = 1 ; $i <= $loop ; $i++)
				{
					$add = $filter->filter($this->_request->getPost("amt_po$i"));
					$obj=new Model_Report();
					$data_po = $obj->set_po($i,$branch_id,$add);
					//echo $i."-".$add."<br>"; 
				}
			}
			$this->_redirect('/shop/keyinpo');
			/*$where = " substr(`doc_date`,9,2) = $i and brand_id = '7338' " ;
			$data = array() ;
								
			$insResult = $table->update_items("commission", $data,$where) ;	*/
                   }
                   
                   public function poAction(){
                   	//$this->_helper->layout()->disableLayout();
                   	
                   	$branch_id=$this->empprofile['branch_id'];
                   }
                   
                   public function viewpoAction(){
                   	$this->_helper->layout()->disableLayout();
                   	
                   	$branch_id=$this->empprofile['branch_id'];
                   	if ($this->getRequest()->isPost()) {
                   		$filter = new Zend_Filter_StripTags();
		       		$month = $filter->filter($this->_request->getPost('data1'));
                       		$year = $filter->filter($this->_request->getPost('data2'));	
                   		$now_date = $year."-".$month ;
                   		//echo $date ;
                   		$obj=new Model_Report();
                   		$get_com_po = $obj->get_com_po($now_date,$branch_id);
                   		$this->view->get_com_po = $get_com_po;
                   		
                   		$this->view->month =$month;
                   		$this->view->year = $year;
                   		
                   		$dayprint = date('d');
		               $monthprint =date('m');
		               $yearprint = date('Y');
		               $pdetail=$obj->chkmonth($monthprint);
		               $this->view->ptime = date('H:i:s');
		               $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;  
                   	}
                   }
                   
                   public function printpoAction(){
                  $this->_helper->layout()->disableLayout();
                 
                  $branch_id=$this->empprofile['branch_id'];
                  if ($this->getRequest()->isPost()) {
                       $obj=new Model_Report();
                       $dayprint = date('d');
                       $monthprint =date('m');
                       $yearprint = date('Y');
                       $pdetail=$obj->chkmonth($monthprint);
                       $this->view->ptime = date('H:i:s');
                       $this->view->print_time = $dayprint."/".$monthprint."/".$yearprint;   
                       $this->view->branch_name=$this->empprofile['branch_name'];  
                       $this->view->branch_id=$this->empprofile['branch_id'];                                                                                         			       $filter = new Zend_Filter_StripTags();
                       $month = $filter->filter($this->_request->getPost('data1'));
                       	$year = $filter->filter($this->_request->getPost('data2'));	
                   	$now_date = $year."-".$month ;
                   		//echo $date ;
                   	$obj=new Model_Report();
                   	$get_com_po = $obj->get_com_po($now_date,$branch_id);
                   	$this->view->get_data = $get_com_po;
                   		
                   	$this->view->month =$month;
                   	$this->view->year = $year;
                		
                       /*$data=$obj->getcoupon($dayfrom,$tofrom);
                       $this->view->get_data = $data;*/
  
                                                    
                                                      
                         } //end post
                                                                                           
                   } //end action 
	}
?>
