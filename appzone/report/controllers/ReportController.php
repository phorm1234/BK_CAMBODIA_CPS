<?php 
	class ReportController extends Zend_Controller_Action{
	public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('report_layout');
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
                   // $this->_helper->layout()->setLayout("report_layout");
                   //$this->_helper->layout()->disableLayout();
                    /*$obj=new Model_Report();
			$detail=$obj->getquotadetail();
			print_r($detail);*/
                    
                    
                    
                    
                }
                
                public function printiqAction(){
			/*$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$doc_no = $filter->filter($this->getRequest()->getParam("doc_no"));
			$obj=new Model_Order();
			$detail=$obj->getdocnodetail($doc_no);
			$this->view->data_detail=$detail;
			$head=$obj->getdocnohead($doc_no);
			$this->view->doc_no=$head;*/
			
		}
                
                
                public function viewreportAction(){
                  $this->_helper->layout()->disableLayout();
                   
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Report();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y');
                        $pdetail=$obj->chkmonth($monthprint);
                        $this->view->ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint;                        
                      	$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
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
                         $this->view->s_date= $fday." ".$detail." ".$fyear;
                         
                         $detail2=$obj->chkmonth($tmonth);
                         $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                    	$get_printer = $obj->getprinter();
                        foreach($get_printer as $p){
                        $this->view->get_printer = $p['thermal_printer'];                           
                        }
                        
                        
                        $get_doc_date = $obj->getdoc_date();
                        foreach($get_doc_date as $a){
                             $this->view->get_doc_date = $a['doc_date'];                           
                        }
                        
                        
						$amountdetail=$obj->getamount($dayfrom,$tofrom);
                        
                        foreach($amountdetail as $a){
                             $this->view->amount_data = substr($a['amount'], 0, -2);
                             
                        }
                        
                        $discountdetail=$obj->getdiscount($dayfrom,$tofrom);
                        foreach($discountdetail as $b){
                            // $this->view->discount_data = substr($b['discount'], 0, -2);                           
                            //$discount_data = $b['discount'];
                            $this->view->discount_data = $obj->getSatang($b['discount'],'up');
                        }
                        
                        
                        $creditdetail=$obj->getcredit($dayfrom,$tofrom);
                        foreach($creditdetail as $b){
                             $credit_data = substr($b['amount'], 0, -2);
                            
                        }

                        $creditaba = $obj->getaba($dayfrom, $tofrom);
                        foreach ($creditaba as $b) {
                             $this->view->credit_aba = substr($b['amount'], 0, -2);
                         //     $credit_data = substr($b['amount'], 0, -2);
                        }
                        
                        $getcncredit=$obj->getCN_credit($dayfrom,$tofrom);
                        foreach($getcncredit as $mm){
                             
                             $getcn_credit = $mm['net'];
                            
                        }

                        $allcreditdetail=$obj->getallcredit($dayfrom,$tofrom);
                        foreach($allcreditdetail as $bb){
                            $allcredit_data = substr($bb['amount'], 0, -2);
                            //$credit_data = substr($b['amount'], 0, -2);
                        }



                        $this->view->allcredit_data = $allcredit_data-$getcn_credit;
                        $this->view->credit_data = $credit_data-$getcn_credit;
                        
                        $this->view->getcn_credit = $getcn_credit;
                        
                        $netamtdetail=$obj->getnetamt($dayfrom,$tofrom);
                        foreach($netamtdetail as $c){
                             $netamt_data = substr($c['netamt'], 0, -2);
                            
                        }
                        
                        $cashcoupondetail=$obj->getpaycashcoupon($dayfrom,$tofrom);
                        foreach($cashcoupondetail as $cc){
                             $cashcoupon_data = substr($cc['pay_cash_coupon'], 0, -2);
                            
                        }
                        
                        
                        
                        $this->view->netcashcoupon =$netamt_data+$cashcoupon_data;
                        
                        $cashdetail=$obj->getcash($dayfrom,$tofrom);
                        foreach($cashdetail as $d){
                             $this->view->cash_data = substr($d['cash'], 0, -2);
                            
                        }
                        
                        $incomedetail=$obj->getincome($dayfrom,$tofrom);
                        foreach($incomedetail as $e){
                             $this->view->qincome_data = substr($e['quantity'], 0, -2);
                             $this->view->aincome_data = substr($e['amount'], 0, -2);
                        }
                        
                        $outcomedetail=$obj->getoutcome($dayfrom,$tofrom);
                        foreach($outcomedetail as $f){
                             $this->view->qoutcome_data = substr($f['quantity'], 0, -2);
                             $this->view->aoutcome_data = substr($f['amount'], 0, -2);
                        }
                        
                        $getchdetail=$obj->getchdata($dayfrom,$tofrom);
                        foreach($getchdetail as $g){
                             $this->view->getquan_data = substr($g['quantity'], 0, -2);
                             $this->view->getamt_data = substr($g['amount'], 0, -2);
                             $this->view->getgross_data = substr($g['gross'], 0, -2);
                        }
                        
                        $getbackdetail=$obj->getback($dayfrom,$tofrom);
                        foreach($getbackdetail as $h){
                             $this->view->qback_data = substr($h['quantity'], 0, -2);
                             $this->view->qbackamt_data = substr($h['amount'], 0, -2);
                             $this->view->qbackgross_data = substr($h['gross'], 0, -2);
                        }
                        
                        $getotherdetail=$obj->getchother($dayfrom,$tofrom);
                        foreach($getotherdetail as $i){
                             $this->view->qother_data = substr($i['quantity'], 0, -2);
                             $this->view->otheramt_data = substr($i['amount'], 0, -2);
                        }
                        
                        $getchincomedetail=$obj->getchincome($dayfrom,$tofrom);
                        foreach($getchincomedetail as $j){
                             $this->view->qchincome_data = substr($j['quantity'], 0, -2);
                             $this->view->chincomeamt_data = substr($j['amount'], 0, -2);
                        }
                        
                        $getchoutcomedetail=$obj->getchoutcome($dayfrom,$tofrom);
                        foreach($getchoutcomedetail as $k){
                             $this->view->qchoutcome_data = substr($k['quantity'], 0, -2);
                             $this->view->choutcomeamt_data = substr($k['amount'], 0, -2);
                        }
                        
                        $getbilldetail=$obj->getbill($dayfrom,$tofrom);
                        foreach($getbilldetail as $l){
                             
                             $this->view->getbill_data = substr($l['amount'], 0, -2);
                        }
                        
                        
                        $newmem=$obj->getnewmember($dayfrom,$tofrom);
                        $this->view->getmember_data = $newmem;
                                             
                        
                     	$memcount=$obj->getcountmem($dayfrom,$tofrom);
                        foreach($memcount as $l){
                             
                             $this->view->getmember_data2 = $l['count'];
                             $this->view->getmemamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $newwalkin=$obj->getwalkin($dayfrom,$tofrom);
                        foreach($newwalkin as $l){
                             
                             $this->view->getwalkin_data = $l['count'];
                             $this->view->getwkamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $getcncash=$obj->getCN_cash($dayfrom,$tofrom);
                        foreach($getcncash as $nn){
                             
                             $this->view->getcn_cash = $nn['net'];
                            
                        }
                       
                        $this->view->payin = $this->view->amount_data - $this->view->discount_data - $this->view->allcredit_data - $this->view->netcashcoupon - $this->view->getcn_cash  - $this->view->getcn_credit;
 
                        $paid_data=$obj->getcreditkbank($dayfrom,$tofrom);
                        $this->view->getpaid_data = $paid_data;
                        
                        $paid_data2=$obj->getcreditbbl($dayfrom,$tofrom);
                        $this->view->getpaid_data2 = $paid_data2;
                      
                        $paid_credit = $obj->getcreditmain($dayfrom,$tofrom); 
                        $this->view->getpaid_credit = $paid_credit;
                        
                        $paid_data3=$obj->getcreditscb($dayfrom,$tofrom);
                        $this->view->getpaid_data3 = $paid_data3;

                        $paid_data4=$obj->getcreditaba($dayfrom,$tofrom);
                        $this->view->getpaid_data4 = $paid_data4;
                        
                        $bill_data=$obj->getbill_no($dayfrom,$tofrom);
                        $this->view->getbill_datano = $bill_data;
                        
                        $cancel_data=$obj->getcancel($dayfrom,$tofrom);
                        $this->view->getcancel_data = $cancel_data;
                        
                        $mem5_count=$obj->getmember5_count($dayfrom,$tofrom);
                        foreach($mem5_count as $nn){
                             
                             $this->view->getmember5_count_data = $nn['count'];
                            
                        }                        
                        $mem5_data=$obj->getmember5_discount($dayfrom,$tofrom);
                        $this->view->getmember5_dis_data = $mem5_data;
                        
                        
                        $get_othercredit = $obj->getOtherCredit($dayfrom,$tofrom);  // get other credit excep creditcard 0002 PM20-1-2023
                        $this->view->othercredit_data = $get_othercredit;

                        $get_detailcredit = $obj->getAllCreditDetail($dayfrom,$tofrom); // gen all credit and get detail for use auto append in view PM20-1-2023
                        $this->view->detailcredit_data = $get_detailcredit;

                    //     $detail_credit_array = array();


                    //     foreach($get_detailcredit as $detail_sub){
                    //           if(!array_key_exists($detail_sub['credit_tp'],$detail_credit_array['credit_tp'])){
                    //                array_push($detail_credit_array['credit_tp'],$detail_sub['credit_tp']);
                    //                // array_push($detail_credit_array[$detail_sub['credit_tp']],$detail_sub['amount']);
                    //           }else{
                    //                // array_push($detail_credit_array[$detail_sub['credit_tp']],$detail_sub['amount']);
                    //           }
                    //     }




                    //     print_r($detail_credit_array);
                        
                        
                        $mem15_count=$obj->getmember15_count($dayfrom,$tofrom);
                        foreach($mem15_count as $nn){
                             
                             $this->view->getmember15_count_data = $nn['count'];
                            
                        }
                        $mem15_data=$obj->getmember15_discount($dayfrom,$tofrom);
                        $this->view->getmember15_dis_data = $mem15_data;
                        
                        
                        
                        
                        $mem15_10_count=$obj->getmember15_10_count($dayfrom,$tofrom);
                        foreach($mem15_10_count as $nn){
                             
                             $this->view->getmember15_10_count_data = $nn['count'];
                            
                        }
                        $mem15_10_data=$obj->getmember15_10_discount($dayfrom,$tofrom);
                        $this->view->getmember15_10_dis_data = $mem15_10_data;
                        
                        
                        
                        $mem25_count=$obj->getmember25_count($dayfrom,$tofrom);
                        foreach($mem25_count as $nn){
                             
                             $this->view->getmember25_count_data = $nn['count'];
                            
                        }
                        $mem25_data=$obj->getmember25_discount($dayfrom,$tofrom);
                        $this->view->getmember25_dis_data = $mem25_data;
                        
                        
                        
                        $mem50_count=$obj->getmember50_count($dayfrom,$tofrom);
                        foreach($mem50_count as $nn){
                             
                             $this->view->getmember50_count_data = $nn['count'];
                            
                        }
                        $mem50_data=$obj->getmember50_discount($dayfrom,$tofrom);
                        $this->view->getmember50_dis_data = $mem50_data;
                        
                        
                        
                        $count_discount_trn2=$obj->count_discount_trn2($dayfrom,$tofrom);
                        foreach($count_discount_trn2 as $nn){
                             
                             $this->view->count_discount_trn2 = $nn['count'];
                            
                        }
                        $disc_data=$obj->get_discount_trn2($dayfrom,$tofrom);
                        $this->view->disc_data = $disc_data;
                        
                        
                        
                        $count_coupon_discount=$obj->count_coupon_discount($dayfrom,$tofrom);
                        foreach($count_coupon_discount as $nn){
                             
                             $this->view->count_coupon_discount = $nn['count'];
                            
                        }
                        $coupon_disc_data=$obj->get_coupon_discount($dayfrom,$tofrom);
                        $this->view->coupon_disc_data = $coupon_disc_data;
                        
                        
                        
                      /*  $count_newbirth=$obj->count_newbirth($dayfrom,$tofrom);
                        foreach($count_newbirth as $nn){
                             
                             $this->view->count_newbirth = $nn['count'];
                            
                        }*/
                        $newbirth_data=$obj->get_newbirth($dayfrom,$tofrom);
                        $this->view->newbirth_data = $newbirth_data;
                        
                        
                        $point_data=$obj->get_pointdata($dayfrom,$tofrom);
                        $this->view->point_data = $point_data;
                        
                        $newpoint_data=$obj->get_new_pointdata($dayfrom,$tofrom);
                        $this->view->newpoint_data = $newpoint_data;
                        
                        $getbill02=$obj->get_bill02($dayfrom,$tofrom);
                        $this->view->bill02_data = $getbill02;
                         
                        } //end post
                        
                        
                        

                    } //end action
                
		public function printreportAction(){
                   $this->_helper->layout()->disableLayout();
                   
                   
                        
			
                   if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Report();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $this->view->ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint;                        
                      	$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                        $tofrom = $filter->filter($this->_request->getPost('end_date'));
                      
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;

                        $get_detailcredit = $obj->getAllCreditDetail($dayfrom,$tofrom);
                        $this->view->detailcredit_data = $get_detailcredit;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4);
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4);

                        $get_othercredit = $obj->getOtherCredit($dayfrom,$tofrom);
                        $this->view->othercredit_data = $get_othercredit;
                        
                        
                         $detail=$obj->chkmonth($fmonth);
                         $this->view->s_date= $fday." ".$detail." ".$fyear;
                         
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                    	$get_printer = $obj->getprinter();
                        foreach($get_printer as $p){
                        $this->view->get_printer = $p['thermal_printer'];                           
                        }
                        
                        
                        $get_doc_date = $obj->getdoc_date();
                        foreach($get_doc_date as $a){
                             $this->view->get_doc_date = $a['doc_date'];                           
                        }
                        
                        
						$amountdetail=$obj->getamount($dayfrom,$tofrom);
                        
                        foreach($amountdetail as $a){
                             $this->view->amount_data = substr($a['amount'], 0, -2);
                             
                        }
                        
                        $discountdetail=$obj->getdiscount($dayfrom,$tofrom);
                        foreach($discountdetail as $b){
                              $this->view->discount_data = $obj->getSatang($b['discount'],'up');
                            
                        }
                        
                        $creditdetail=$obj->getcredit($dayfrom,$tofrom);
                        foreach($creditdetail as $b){
                             $credit_data = substr($b['amount'], 0, -2);
                            
                        }

                        $creditaba = $obj->getaba($dayfrom, $tofrom);
                        foreach ($creditaba as $b) {
                             $this->view->credit_aba = substr($b['amount'], 0, -2);
                         //     $credit_data = substr($b['amount'], 0, -2);
                        }
                        
                        $getcncredit=$obj->getCN_credit($dayfrom,$tofrom);
                        foreach($getcncredit as $mm){
                             
                             $getcn_credit = $mm['net'];
                            
                        }
                        
                        $this->view->credit_data = $credit_data-$getcn_credit;
                        
                        $this->view->getcn_credit = $getcn_credit;
                        
                        $netamtdetail=$obj->getnetamt($dayfrom,$tofrom);
                        foreach($netamtdetail as $c){
                             $netamt_data = substr($c['netamt'], 0, -2);
                            
                        }
                        
                        $cashcoupondetail=$obj->getpaycashcoupon($dayfrom,$tofrom);
                        foreach($cashcoupondetail as $cc){
                             $cashcoupon_data = substr($cc['pay_cash_coupon'], 0, -2);
                            
                        }
                        
                        
                        
                        $this->view->netcashcoupon =$netamt_data+$cashcoupon_data;
                        
                        $cashdetail=$obj->getcash($dayfrom,$tofrom);
                        foreach($cashdetail as $d){
                             $this->view->cash_data = substr($d['cash'], 0, -2);
                            
                        }
                        
                        $incomedetail=$obj->getincome($dayfrom,$tofrom);
                        foreach($incomedetail as $e){
                             $this->view->qincome_data = substr($e['quantity'], 0, -2);
                             $this->view->aincome_data = substr($e['amount'], 0, -2);
                        }
                        
                        $outcomedetail=$obj->getoutcome($dayfrom,$tofrom);
                        foreach($outcomedetail as $f){
                             $this->view->qoutcome_data = substr($f['quantity'], 0, -2);
                             $this->view->aoutcome_data = substr($f['amount'], 0, -2);
                        }
                        
                        $getchdetail=$obj->getchdata($dayfrom,$tofrom);
                        foreach($getchdetail as $g){
                             $this->view->getquan_data = substr($g['quantity'], 0, -2);
                             $this->view->getamt_data = substr($g['amount'], 0, -2);
                             $this->view->getgross_data = substr($g['gross'], 0, -2);
                        }
                        
                        $getbackdetail=$obj->getback($dayfrom,$tofrom);
                        foreach($getbackdetail as $h){
                             $this->view->qback_data = substr($h['quantity'], 0, -2);
                             $this->view->qbackamt_data = substr($h['amount'], 0, -2);
                             $this->view->qbackgross_data = substr($h['gross'], 0, -2);
                        }
                        
                        $getotherdetail=$obj->getchother($dayfrom,$tofrom);
                        foreach($getotherdetail as $i){
                             $this->view->qother_data = substr($i['quantity'], 0, -2);
                             $this->view->otheramt_data = substr($i['amount'], 0, -2);
                        }
                        
                        $getchincomedetail=$obj->getchincome($dayfrom,$tofrom);
                        foreach($getchincomedetail as $j){
                             $this->view->qchincome_data = substr($j['quantity'], 0, -2);
                             $this->view->chincomeamt_data = substr($j['amount'], 0, -2);
                        }
                        
                        $getchoutcomedetail=$obj->getchoutcome($dayfrom,$tofrom);
                        foreach($getchoutcomedetail as $k){
                             $this->view->qchoutcome_data = substr($k['quantity'], 0, -2);
                             $this->view->choutcomeamt_data = substr($k['amount'], 0, -2);
                        }
                        
                        $getbilldetail=$obj->getbill($dayfrom,$tofrom);
                        foreach($getbilldetail as $l){
                             
                             $this->view->getbill_data = substr($l['amount'], 0, -2);
                        }
                        
                        
                        $newmem=$obj->getnewmember($dayfrom,$tofrom);
                        $this->view->getmember_data = $newmem;
                                             
                        
                     	$memcount=$obj->getcountmem($dayfrom,$tofrom);
                        foreach($memcount as $l){
                             
                             $this->view->getmember_data2 = $l['count'];
                             $this->view->getmemamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $newwalkin=$obj->getwalkin($dayfrom,$tofrom);
                        foreach($newwalkin as $l){
                             
                             $this->view->getwalkin_data = $l['count'];
                             $this->view->getwkamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $getcncash=$obj->getCN_cash($dayfrom,$tofrom);
                        foreach($getcncash as $nn){
                             
                             $this->view->getcn_cash = $nn['net'];
                            
                        }
                        $allcreditdetail=$obj->getallcredit($dayfrom,$tofrom);
                        foreach($allcreditdetail as $bb){
                            $allcredit_data = substr($bb['amount'], 0, -2);
                            //$credit_data = substr($b['amount'], 0, -2);
                        }



                        $this->view->allcredit_data = $allcredit_data-$getcn_credit;
                        $this->view->credit_data = $credit_data-$getcn_credit;

                        $this->view->payin = $this->view->amount_data - $this->view->discount_data - $this->view->allcredit_data - $this->view->netcashcoupon - $this->view->getcn_cash  - $this->view->getcn_credit;
 
                        $paid_data=$obj->getcreditkbank($dayfrom,$tofrom);
                        $this->view->getpaid_data = $paid_data;
                        
                        $paid_data2=$obj->getcreditbbl($dayfrom,$tofrom);
                        $this->view->getpaid_data2 = $paid_data2;

                        $paid_credit = $obj->getcreditmain($dayfrom,$tofrom); 
                        $this->view->getpaid_credit = $paid_credit;
                        
                        $paid_data3=$obj->getcreditscb($dayfrom,$tofrom);
                        $this->view->getpaid_data3 = $paid_data3;

                        $paid_data4=$obj->getcreditaba($dayfrom,$tofrom);
                        $this->view->getpaid_data4 = $paid_data4;
                        
                        $bill_data=$obj->getbill_no($dayfrom,$tofrom);
                        $this->view->getbill_datano = $bill_data;
                        
                        $cancel_data=$obj->getcancel($dayfrom,$tofrom);
                        $this->view->getcancel_data = $cancel_data;
                        
                        $mem5_count=$obj->getmember5_count($dayfrom,$tofrom);
                        foreach($mem5_count as $nn){
                             
                             $this->view->getmember5_count_data = $nn['count'];
                            
                        }                        
                        $mem5_data=$obj->getmember5_discount($dayfrom,$tofrom);
                        $this->view->getmember5_dis_data = $mem5_data;
                        
                        
                        
                        
                        $mem15_count=$obj->getmember15_count($dayfrom,$tofrom);
                        foreach($mem15_count as $nn){
                             
                             $this->view->getmember15_count_data = $nn['count'];
                            
                        }
                        $mem15_data=$obj->getmember15_discount($dayfrom,$tofrom);
                        $this->view->getmember15_dis_data = $mem15_data;
                        
                        
                        
                        
                        $mem15_10_count=$obj->getmember15_10_count($dayfrom,$tofrom);
                        foreach($mem15_10_count as $nn){
                             
                             $this->view->getmember15_10_count_data = $nn['count'];
                            
                        }
                        $mem15_10_data=$obj->getmember15_10_discount($dayfrom,$tofrom);
                        $this->view->getmember15_10_dis_data = $mem15_10_data;
                        
                        
                        
                        $mem25_count=$obj->getmember25_count($dayfrom,$tofrom);
                        foreach($mem25_count as $nn){
                             
                             $this->view->getmember25_count_data = $nn['count'];
                            
                        }
                        $mem25_data=$obj->getmember25_discount($dayfrom,$tofrom);
                        $this->view->getmember25_dis_data = $mem25_data;
                        
                        
                        
                        $mem50_count=$obj->getmember50_count($dayfrom,$tofrom);
                        foreach($mem50_count as $nn){
                             
                             $this->view->getmember50_count_data = $nn['count'];
                            
                        }
                        $mem50_data=$obj->getmember50_discount($dayfrom,$tofrom);
                        $this->view->getmember50_dis_data = $mem50_data;
                        
                        
                        
                        $count_discount_trn2=$obj->count_discount_trn2($dayfrom,$tofrom);
                        foreach($count_discount_trn2 as $nn){
                             
                             $this->view->count_discount_trn2 = $nn['count'];
                            
                        }
                        $disc_data=$obj->get_discount_trn2($dayfrom,$tofrom);
                        $this->view->disc_data = $disc_data;
                        
                        
                        
                        $count_coupon_discount=$obj->count_coupon_discount($dayfrom,$tofrom);
                        foreach($count_coupon_discount as $nn){
                             
                             $this->view->count_coupon_discount = $nn['count'];
                            
                        }
                        $coupon_disc_data=$obj->get_coupon_discount($dayfrom,$tofrom);
                        $this->view->coupon_disc_data = $coupon_disc_data;
                        
                        
                        
                        
                        /*$count_newbirth=$obj->count_newbirth($dayfrom,$tofrom);
                        foreach($count_newbirth as $nn){
                             
                             $this->view->count_newbirth = $nn['count'];
                            
                        }*/
                        $newbirth_data=$obj->get_newbirth($dayfrom,$tofrom);
                        $this->view->newbirth_data = $newbirth_data;
                        
                        $point_data=$obj->get_pointdata($dayfrom,$tofrom);
                        $this->view->point_data = $point_data;
                        
                        $newpoint_data=$obj->get_new_pointdata($dayfrom,$tofrom);
                        $this->view->newpoint_data = $newpoint_data;                       
                        
                        $getbill02=$obj->get_bill02($dayfrom,$tofrom);
                        $this->view->bill02_data = $getbill02;
                        
                        } //end post
                        
                        
                        
                        

                    } //end action
		
		
           public function stockreportAction(){
                  // $this->_helper->layout()->disableLayout();
                  // $objPos=new SSUP_Controller_Plugin_PosGlobal();                    
                   //$objPos->stockBalance('OP','OP','7338','2011-04-01'); 
                  // $objPos->stockBalance(); 
                   
           }
                    
           public function viewstockAction(){
                   $this->_helper->layout()->disableLayout();
                   
                   
                        
			
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportstock();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                        
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $product_start = $filter->filter($this->_request->getPost('product1'));
                        $product_end = $filter->filter($this->_request->getPost('product2'));
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                        $this->view->condition = $filter->filter($this->_request->getPost('condition'));
                        $this->view->report_detail = $filter->filter($this->_request->getPost('detail'));
                      
                        /*if($product_start =="" or $product_end =="" or $dayfrom =="" or $tofrom ==""){
                            
                            echo " <script language='javascript'>
                                alert('กรุณากรอกข้อมูลให้ครบถ้วน')
                                </script>";
                        }else{ */
                        
                        
                    	$get_printer = $obj->getprinter();
                        foreach($get_printer as $p){
                             $this->view->get_printer = $p['thermal_printer'];                           
                        }
                        
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        $beginyear = substr($dayfrom, 0,4);
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        
                        if($fday > "01"){
                            
                            $fday2 = $fday-1;
                            $newdayfrom = $beginyear."-".$fmonth."-01";
                            $newdayend = $beginyear."-".$fmonth."-".$fday2; 
                            $trs_data4=$obj->get_transection4($product_start,$product_end,$newdayfrom,$newdayend,$fmonth,$beginyear,$dayfrom,$tofrom);
                            $this->view->gettrs_data2 = $trs_data4;  
                            $this->view->fmonth = $fmonth;
                            $this->view->fyear = substr($dayfrom, 0,4);
                            
                        }  elseif ($fday == "01") {
                                                      
                            $trs_data3=$obj->get_transection3($product_start,$product_end,$dayfrom,$tofrom,$fmonth,$beginyear);
                            $this->view->gettrs_data1 = $trs_data3;  
                            $this->view->fmonth = $fmonth;
                            $this->view->fyear = substr($dayfrom, 0,4);
                            
                        }
                                             
                      
                        $billti_data=$obj->getbill_ti($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillti_data = $billti_data;
                        
                        $billsl_data=$obj->getbill_sl($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillsl_data = $billsl_data;
                        
                        $billvt_data=$obj->getbill_vt($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillvt_data = $billvt_data;
                        
                        $billslvt_data=$obj->getsumbill_slvt($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillslvt_data = $billslvt_data;
                        
                        $billto_data=$obj->getbill_to($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillto_data = $billto_data;
                        
                        $billcn_data=$obj->getbill_cn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillcn_data = $billcn_data;
                        
                        $billdn_data=$obj->getbill_dn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbilldn_data = $billdn_data;
                        
                        $billai_data=$obj->getbill_ai($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillai_data = $billai_data;
                        
                        $billao_data=$obj->getbill_ao($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillao_data = $billao_data;
                        
                      /*  if ($fmonth <> $tmonth){
                           echo " <script language='javascript'>
                                alert('วันที่เริ่มต้นและสิ้นสุดต้องเป็นเดือนเดียวกัน')
                                </script>";
                        }else{*/
                        
                        
			$detail=$obj->chkmonth($fmonth);
			$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                  
                        
                        
                        
                        
                        
                                         
                       // $begin_data=$obj->get_begin($product_start,$product_end,$fmonth,$beginyear);
                        //$this->view->getbegin_data = $begin_data;
                        
                     //   $trs_data=$obj->get_transection2($product_start,$product_end,$newdayfrom,$newdayend,$fmonth,$beginyear);
                      //  $this->view->gettrs_data = $trs_data;
                        
                        
                            
                        
                          
                        
                        
                        
                        
                        
                        
                        
                     //   }
			/*$amountdetail=$obj->getamount($dayfrom,$tofrom);
                        
                        foreach($amountdetail as $a){
                             $this->view->amount_data = substr($a['amount'], 0, -2);
                             $this->view->discount_data = substr($a['discount'], 0, -2);
                        }*/
                        
                        $this->view->product_start = $product_start;
                        $this->view->product_end = $product_end;
                        
                    //}   
                 }
           }
           public function printviewstockAction(){
                   $this->_helper->layout()->disableLayout();
                   
                   
                        
			
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportstock();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                        
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $product_start = $filter->filter($this->_request->getPost('product1'));
                        $product_end = $filter->filter($this->_request->getPost('product2'));
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                        $this->view->condition = $filter->filter($this->_request->getPost('condition'));
                        $this->view->report_detail = $filter->filter($this->_request->getPost('detail'));
                      
                                                
                        
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        $beginyear = substr($dayfrom, 0,4);
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        if($fday > "01"){
                            
                            $fday2 = $fday-1;
                            $newdayfrom = $beginyear."-".$fmonth."-01";
                            $newdayend = $beginyear."-".$fmonth."-".$fday2; 
                            $trs_data4=$obj->get_transection4($product_start,$product_end,$newdayfrom,$newdayend,$fmonth,$beginyear,$dayfrom,$tofrom);
                            $this->view->gettrs_data2 = $trs_data4;  
                            
                        }  elseif ($fday == "01") {
                                                      
                            $trs_data3=$obj->get_transection3($product_start,$product_end,$dayfrom,$tofrom,$fmonth,$beginyear);
                            $this->view->gettrs_data1 = $trs_data3;  
                            
                        }
                       
                        
                        $billti_data=$obj->getbill_ti($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillti_data = $billti_data;
                        
                        $billsl_data=$obj->getbill_sl($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillsl_data = $billsl_data;
                        
                        $billvt_data=$obj->getbill_vt($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillvt_data = $billvt_data;
                        
                        $billslvt_data=$obj->getsumbill_slvt($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillslvt_data = $billslvt_data;
                        
                        $billto_data=$obj->getbill_to($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillto_data = $billto_data;
                        
                        $billcn_data=$obj->getbill_cn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillcn_data = $billcn_data;
                        
                        $billdn_data=$obj->getbill_dn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbilldn_data = $billdn_data;
                        
                        $billai_data=$obj->getbill_ai($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillai_data = $billai_data;
                        
                        $billao_data=$obj->getbill_ao($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillao_data = $billao_data;
                        
                        
                        
			$detail=$obj->chkmonth($fmonth);
			$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        
                                         
                        
                        
                        
                        }
			
                    }
                    
	public function printmtstockAction(){
                   $this->_helper->layout()->disableLayout();              
                                         
			
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportstock();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                        
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $product_start = $filter->filter($this->_request->getPost('product1'));
                        $product_end = $filter->filter($this->_request->getPost('product2'));
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                        $this->view->condition = $filter->filter($this->_request->getPost('condition'));
                        $this->view->report_detail = $filter->filter($this->_request->getPost('detail'));
                      
                                                
                        
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        $beginyear = substr($dayfrom, 0,4);
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        if($fday > "01"){
                            
                            $fday2 = $fday-1;
                            $newdayfrom = $beginyear."-".$fmonth."-01";
                            $newdayend = $beginyear."-".$fmonth."-".$fday2; 
                            $trs_data4=$obj->get_transection4($product_start,$product_end,$newdayfrom,$newdayend,$fmonth,$beginyear,$dayfrom,$tofrom);
                            $this->view->gettrs_data2 = $trs_data4;  
                            
                        }  elseif ($fday == "01") {
                                                      
                            $trs_data3=$obj->get_transection3($product_start,$product_end,$dayfrom,$tofrom,$fmonth,$beginyear);
                            $this->view->gettrs_data1 = $trs_data3;  
                            
                        }
                       
                        
                        $billti_data=$obj->getbill_ti($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillti_data = $billti_data;
                        
                        $billsl_data=$obj->getbill_sl($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillsl_data = $billsl_data;
                        
                        $billto_data=$obj->getbill_to($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillto_data = $billto_data;
                        
                        $billcn_data=$obj->getbill_cn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillcn_data = $billcn_data;
                        
                        $billdn_data=$obj->getbill_dn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbilldn_data = $billdn_data;
                        
                        $billai_data=$obj->getbill_ai($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillai_data = $billai_data;
                        
                        $billao_data=$obj->getbill_ao($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillao_data = $billao_data;
                        
                        
                        
						$detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        
                                         
                        
                        
                        
                        }
			
                    }
                    
               public function premiumreportAction(){
                   
                // $this->_helper->layout()->disableLayout();
                   
               }
           public function viewpremiumAction(){
               $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportpremium();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                                                   
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        $filter = new Zend_Filter_StripTags();
                        $product_start = $filter->filter($this->_request->getPost('product1'));
                        $product_end = $filter->filter($this->_request->getPost('product2'));
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                        
                      
                        /*if($product_start =="" or $product_end =="" or $dayfrom =="" or $tofrom ==""){
                            
                            echo " <script language='javascript'>
                                alert('กรุณากรอกข้อมูลให้ครบถ้วน')
                                </script>";
                        }else{ */
                        
                  		$get_printer = $obj->getprinter();
                        foreach($get_printer as $p){
                             $this->view->get_printer = $p['thermal_printer'];                           
                        }
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;                   
                        
			                       
                        $premium_data=$obj->getpremium($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getpremium_data = $premium_data;
                        
                        $detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        $this->view->product_start = $product_start;
                        $this->view->product_end = $product_end;
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                  }
                   
               }
               
            public function printpremiumAction(){
               $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportpremium();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                                                   
                       
                        $filter = new Zend_Filter_StripTags();
                        $product_start = $filter->filter($this->_request->getPost('product1'));
                        $product_end = $filter->filter($this->_request->getPost('product2'));
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                        
                      
                        /*if($product_start =="" or $product_end =="" or $dayfrom =="" or $tofrom ==""){
                            
                            echo " <script language='javascript'>
                                alert('กรุณากรอกข้อมูลให้ครบถ้วน')
                                </script>";
                        }else{ */
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;                   
                        
			                       
                        $premium_data=$obj->getpremium($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getpremium_data = $premium_data;
                        
                        $detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        $this->view->product_start = $product_start;
                        $this->view->product_end = $product_end;
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                  }
                   
               }
               
               
	 		public function gpreportAction(){
                  $this->_helper->layout()->setLayout('report2_layout');
                   
           	}
           
	 		public function viewgpAction(){
	  					$this->_helper->layout()->disableLayout();
                  		if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportgp();
						$this->view->print_time = date('d/m/y');
						$this->view->ptime = date('H:i:s');
                  		$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                  			
                        $filter = new Zend_Filter_StripTags();
                        $this->view->dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $this->view->tofrom = $filter->filter($this->_request->getPost('data2'));
                        
                        $dayfrom = $this->view->dayfrom;
                        $tofrom = $this->view->tofrom;
                       /* 
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                       */ 
                        $fday = substr($dayfrom, 0,2);
                        $fmonth = substr($dayfrom, 3, 2);
                        $fyear = substr($dayfrom, -4);
                        
                        $eday = substr($tofrom, 0,2);
                        $emonth = substr($tofrom, 3, 2);
                        $eyear = substr($tofrom, -4);
                        
                        $startdate = $fyear."-".$fmonth."-".$fday; 
                        $enddate = $eyear."-".$emonth."-".$eday; 
                                               
                      	$getgp_data=$obj->getgp($startdate,$enddate);
                        $this->view->gp_data = $getgp_data;
                        
                  }           
                   
           }       
           
           public function printgpAction(){
	  					$this->_helper->layout()->disableLayout();
                  		if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportgp();
						$this->view->print_time = date('d/m/y');
						$this->view->ptime = date('H:i:s');
                  		$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                  			
                        $filter = new Zend_Filter_StripTags();
                        $this->view->dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $this->view->tofrom = $filter->filter($this->_request->getPost('data2'));
                        
                        $dayfrom = $this->view->dayfrom;
                        $tofrom = $this->view->tofrom;
                       /* 
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                       */ 
                        $fday = substr($dayfrom, 0,2);
                        $fmonth = substr($dayfrom, 3, 2);
                        $fyear = substr($dayfrom, -4);
                        
                        $eday = substr($tofrom, 0,2);
                        $emonth = substr($tofrom, 3, 2);
                        $eyear = substr($tofrom, -4);
                        
                        $startdate = $fyear."-".$fmonth."-".$fday; 
                        $enddate = $eyear."-".$emonth."-".$eday; 
                                               
                      	$getgp_data=$obj->getgp($startdate,$enddate);
                        $this->view->gp_data = $getgp_data;
                        
                  }           
                   
           }  
           public function screportAction(){
                  $this->_helper->layout()->setLayout('report2_layout');
                   
           	}
           public function viewscAction(){
                  $this->_helper->layout()->setLayout('report2_layout');
            if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportstock();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                        
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $this->view->dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $this->view->tofrom = $filter->filter($this->_request->getPost('data2'));
                        $this->view->product_start = $filter->filter($this->_request->getPost('data3'));
                        $this->view->product_end = $filter->filter($this->_request->getPost('data4'));
                        
                        $dayfrom = $this->view->dayfrom;
                        $tofrom = $this->view->tofrom;
                        $product_start = $this->view->product_start;
                        $product_end = $this->view->product_end;
                       /* 
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;*/
                        
                        $fday = substr($dayfrom, 0,2);
                        $fmonth = substr($dayfrom, 3, 2);
                        $fyear = substr($dayfrom, -4);
                        
                        $eday = substr($tofrom, 0,2);
                        $emonth = substr($tofrom, 3, 2);
                        $eyear = substr($tofrom, -4);
                        
                        $startdate = $fyear."-".$fmonth."-".$fday; 
                        $enddate = $eyear."-".$emonth."-".$eday; 
                        
                      //  echo $fyear;
                        
                        if($fday > "01"){
                            
                            $fday2 = $fday-1;
                            $newdayfrom = $fyear."-".$fmonth."-01";
                            $newdayend = $fyear."-".$fmonth."-".$fday2; 
                            $trs_data4=$obj->get_transection4($product_start,$product_end,$newdayfrom,$newdayend,$fmonth,$fyear,$dayfrom,$tofrom);
                            $this->view->gettrs_data2 = $trs_data4;  
                            
                            //print_r($trs_data4);
                            $this->view->fmonth = $fmonth;
                            $this->view->fyear = $fyear;
                            
                        }  elseif ($fday == "01") {
                                                      
                            $trs_data3=$obj->get_transection3($product_start,$product_end,$dayfrom,$tofrom,$fmonth,$fyear);
                            $this->view->gettrs_data1 = $trs_data3;  
                            //$this->view->fmonth = $fmonth;
                            //$this->view->fyear = substr($dayfrom, 0,4);
                            
                        }
                        
                     /*   $billti_data=$obj->getbill_ti($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillti_data = $billti_data;
                        
                        $billsl_data=$obj->getbill_sl($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillsl_data = $billsl_data;
                        
                        $billto_data=$obj->getbill_to($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillto_data = $billto_data;
                        
                        $billcn_data=$obj->getbill_cn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillcn_data = $billcn_data;
                        
                        $billdn_data=$obj->getbill_dn($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbilldn_data = $billdn_data;
                        
                        $billai_data=$obj->getbill_ai($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillai_data = $billai_data;
                        
                        $billao_data=$obj->getbill_ao($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getbillao_data = $billao_data;*/
                        
                 
                        
			$detail=$obj->chkmonth($fmonth);
			$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        $this->view->product_start = $product_start;
                        $this->view->product_end = $product_end;
                        
                    //}   
                 }
                   
           	}
           
	 		/*public function viewscAction(){
	  					$this->_helper->layout()->disableLayout();
                  		if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportstock();
						$this->view->print_time = date('d/m/y');
						$this->view->ptime = date('H:i:s');
                  		$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                  			
                        $filter = new Zend_Filter_StripTags();
                        $this->view->dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $this->view->tofrom = $filter->filter($this->_request->getPost('data2'));
                        $this->view->product_start = $filter->filter($this->_request->getPost('data3'));
                        $this->view->product_end = $filter->filter($this->_request->getPost('data4'));
                        
                        $dayfrom = $this->view->dayfrom;
                        $tofrom = $this->view->tofrom;
                       /* 
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $fday = substr($dayfrom, 0,2);
                        $fmonth = substr($dayfrom, 3, 2);
                        $fyear = substr($dayfrom, -4);
                        
                        $eday = substr($tofrom, 0,2);
                        $emonth = substr($tofrom, 3, 2);
                        $eyear = substr($tofrom, -4);
                        
                        $startdate = $fyear."-".$fmonth."-".$fday; 
                        $enddate = $eyear."-".$emonth."-".$eday; 
                        
                        $viewsc_data=$obj->view_stockdata($this->view->product_start,$this->view->product_end,$fmonth);
                        $this->view->view_sc = $viewsc_data;
                                               
                      	$getsc_data=$obj->view_stockcard($startdate,$enddate,$this->view->product_start,$this->view->product_end);
                        $this->view->sc_data = $getsc_data;
                        
                  }           
                   
           }       */
           
           public function printscAction(){
	  					$this->_helper->layout()->disableLayout();
                  		if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportgp();
						$this->view->print_time = date('d/m/y');
						$this->view->ptime = date('H:i:s');
                  		$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                  			
                        $filter = new Zend_Filter_StripTags();
                        $this->view->dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $this->view->tofrom = $filter->filter($this->_request->getPost('data2'));
                        
                        $dayfrom = $this->view->dayfrom;
                        $tofrom = $this->view->tofrom;
                       /* 
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                       */ 
                        $fday = substr($dayfrom, 0,2);
                        $fmonth = substr($dayfrom, 3, 2);
                        $fyear = substr($dayfrom, -4);
                        
                        $eday = substr($tofrom, 0,2);
                        $emonth = substr($tofrom, 3, 2);
                        $eyear = substr($tofrom, -4);
                        
                        $startdate = $fyear."-".$fmonth."-".$fday; 
                        $enddate = $eyear."-".$emonth."-".$eday; 
                                               
                      	$getgp_data=$obj->getgp($startdate,$enddate);
                        $this->view->gp_data = $getgp_data;
                        
                  }           
                   
           }

           
           public function systemAction(){
                //  $this->_helper->layout()->disableLayout();
                   
           	}   
           	
          public function system2Action(){
                  $this->_helper->layout()->disableLayout();
                  if ($this->getRequest()->isPost()) {		        
                    	$dayprint = "X".date('ymd')."Z";
                    	$dayprint2 = "x".date('ymd')."z";
                        $filter = new Zend_Filter_StripTags();
                        $ddate = $filter->filter($this->_request->getPost('data1'));
                        
                        if($ddate == $dayprint or $ddate == $dayprint2){                        	
                      		$obj=new Model_Reportsystem();                     		
                        	
                        $getdoc_date=$obj->view_docdate();
                        foreach($getdoc_date as $d){
                             $this->view->v_docdate = $d['doc_date'];
                        }
                        
                        $getdoc_no=$obj->view_docno();
                        $this->view->v_docno = $getdoc_no ;
                        
                        $gettime_branch=$obj->view_timebranch();
                        foreach($gettime_branch as $t){
                             $this->view->nopen_time = $t['normal_open_time'];
                             $this->view->nclose_time = $t['normal_close_time'];
                             $this->view->sopen_time = $t['special_open_time'];
                             $this->view->sclose_time = $t['special_close_time'];
                        }
                        
                        }else{
                        	
                        	echo "รหัสผ่านไม่ถูกต้อง";
                        	$this->_redirect('report/system4') ;	                     

                        } //end password
                  }//end post
           	}
           	public function system3Action(){
                //  $this->_helper->layout()->disableLayout();
                   if ($this->getRequest()->isPost()) {		
                   		$filter = new Zend_Filter_StripTags();
                        $nopentime = $filter->filter($this->_request->getPost('nopen_time'));
                        $nclosetime = $filter->filter($this->_request->getPost('nclose_time'));
                        $sopentime = $filter->filter($this->_request->getPost('sopen_time'));
                        $sclosetime = $filter->filter($this->_request->getPost('sclose_time'));
                        
                        $obj=new Model_Reportsystem();
                        $update_time=$obj->update_time($nopentime,$nclosetime,$sopentime,$sclosetime);
                   } 
           	}  
           		public function system4Action(){
                //  $this->_helper->layout()->disableLayout();
                   
           	}       
           	public function printmtAction(){
                //  $this->_helper->layout()->disableLayout();
                
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Report();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $this->view->ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint;                        
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        $filter = new Zend_Filter_StripTags();
                        $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                        $tofrom = $filter->filter($this->_request->getPost('end_date'));
                      
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        
						$detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        
                        $get_doc_date = $obj->getdoc_date();
                        foreach($get_doc_date as $a){
                             $this->view->get_doc_date = $a['doc_date'];                           
                        }
                        
                        
						$amountdetail=$obj->getamount($dayfrom,$tofrom);
                        
                        foreach($amountdetail as $a){
                             $this->view->amount_data = substr($a['amount'], 0, -2);
                             
                        }
                        
                        $discountdetail=$obj->getdiscount($dayfrom,$tofrom);
                        foreach($discountdetail as $b){
                              $this->view->discount_data = $obj->getSatang($b['discount'],'up');
                            
                        }
                        
                        
                        $creditdetail=$obj->getcredit($dayfrom,$tofrom);
                        foreach($creditdetail as $b){
                             $credit_data = substr($b['amount'], 0, -2);
                            
                        }

                        $creditaba = $obj->getaba($dayfrom, $tofrom);
                        foreach ($creditaba as $b) {
                             $this->view->credit_aba = substr($b['amount'], 0, -2);
                         //     $credit_data = substr($b['amount'], 0, -2);
                        }

                        $creditaba = $obj->getaba($dayfrom, $tofrom);
                        foreach ($creditaba as $b) {
                             $this->view->credit_aba = substr($b['amount'], 0, -2);
                         //     $credit_data = substr($b['amount'], 0, -2);
                        }
                        
                        $getcncredit=$obj->getCN_credit($dayfrom,$tofrom);
                        foreach($getcncredit as $mm){
                             
                             $getcn_credit = $mm['net'];
                            
                        }
                        
                        $this->view->credit_data = $credit_data-$getcn_credit;
                        
                        $this->view->getcn_credit = $getcn_credit;
                        
                        
                        $netamtdetail=$obj->getnetamt($dayfrom,$tofrom);
                        foreach($netamtdetail as $c){
                             $netamt_data = substr($c['netamt'], 0, -2);
                            
                        }
                        
                        $cashcoupondetail=$obj->getpaycashcoupon($dayfrom,$tofrom);
                        foreach($cashcoupondetail as $cc){
                             $cashcoupon_data = substr($cc['pay_cash_coupon'], 0, -2);
                            
                        }
                        
                        
                        $this->view->netcashcoupon =$netamt_data+$cashcoupon_data;
                        
                        $cashdetail=$obj->getcash($dayfrom,$tofrom);
                        foreach($cashdetail as $d){
                             $this->view->cash_data = substr($d['cash'], 0, -2);
                        }
                        
                        $incomedetail=$obj->getincome($dayfrom,$tofrom);
                        foreach($incomedetail as $e){
                             $this->view->qincome_data = substr($e['quantity'], 0, -2);
                             $this->view->aincome_data = substr($e['amount'], 0, -2);
                        }
                        
                        $outcomedetail=$obj->getoutcome($dayfrom,$tofrom);
                        foreach($outcomedetail as $f){
                             $this->view->qoutcome_data = substr($f['quantity'], 0, -2);
                             $this->view->aoutcome_data = substr($f['amount'], 0, -2);
                        }
                        
                        $getchdetail=$obj->getchdata($dayfrom,$tofrom);
                        foreach($getchdetail as $g){
                             $this->view->getquan_data = substr($g['quantity'], 0, -2);
                             $this->view->getamt_data = substr($g['amount'], 0, -2);
                             $this->view->getgross_data = substr($g['gross'], 0, -2);
                        }
                        
                        $getbackdetail=$obj->getback($dayfrom,$tofrom);
                        foreach($getbackdetail as $h){
                             $this->view->qback_data = substr($h['quantity'], 0, -2);
                             $this->view->qbackamt_data = substr($h['amount'], 0, -2);
                             $this->view->qbackgross_data = substr($h['gross'], 0, -2);
                        }
                        
                        $getotherdetail=$obj->getchother($dayfrom,$tofrom);
                        foreach($getotherdetail as $i){
                             $this->view->qother_data = substr($i['quantity'], 0, -2);
                             $this->view->otheramt_data = substr($i['amount'], 0, -2);
                        }
                        
                        $getchincomedetail=$obj->getchincome($dayfrom,$tofrom);
                        foreach($getchincomedetail as $j){
                             $this->view->qchincome_data = substr($j['quantity'], 0, -2);
                             $this->view->chincomeamt_data = substr($j['amount'], 0, -2);
                        }
                        
                        $getchoutcomedetail=$obj->getchoutcome($dayfrom,$tofrom);
                        foreach($getchoutcomedetail as $k){
                             $this->view->qchoutcome_data = substr($k['quantity'], 0, -2);
                             $this->view->choutcomeamt_data = substr($k['amount'], 0, -2);
                        }
                        
                        $getbilldetail=$obj->getbill($dayfrom,$tofrom);
                        foreach($getbilldetail as $l){
                             
                             $this->view->getbill_data = substr($l['amount'], 0, -2);
                        }
                        
                        
                        $newmem=$obj->getnewmember($dayfrom,$tofrom);
                        $this->view->getmember_data = $newmem;
                                             
                       
                    	$memcount=$obj->getcountmem($dayfrom,$tofrom);
                        foreach($memcount as $l){
                             
                             $this->view->getmember_data2 = $l['count'];
                             $this->view->getmemamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $newwalkin=$obj->getwalkin($dayfrom,$tofrom);
                        foreach($newwalkin as $l){
                             
                             $this->view->getwalkin_data = $l['count'];
                             $this->view->getwkamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $getcncash=$obj->getCN_cash($dayfrom,$tofrom);
                        foreach($getcncash as $nn){
                             
                             $this->view->getcn_cash = $nn['net'];
                            
                        }
                       
                        $this->view->payin = $this->view->amount_data - $this->view->discount_data - $this->view->credit_data - $this->view->netcashcoupon - $this->view->getcn_cash  - $this->view->getcn_credit;
                        
                        $paid_data=$obj->getcreditkbank($dayfrom,$tofrom);
                        $this->view->getpaid_data = $paid_data;
                        
                        $paid_data2=$obj->getcreditbbl($dayfrom,$tofrom);
                        $this->view->getpaid_data2 = $paid_data2;

                        $paid_credit = $obj->getcreditmain($dayfrom,$tofrom); 
                        $this->view->getpaid_credit = $paid_credit;
                        
                        $paid_data3=$obj->getcreditscb($dayfrom,$tofrom);
                        $this->view->getpaid_data3 = $paid_data3;

                        $paid_data4=$obj->getcreditaba($dayfrom,$tofrom);
                        $this->view->getpaid_data4 = $paid_data4;
                        
                        $bill_data=$obj->getbill_no($dayfrom,$tofrom);
                        $this->view->getbill_datano = $bill_data;
                        
                        
                        $cancel_data=$obj->getcancel($dayfrom,$tofrom);
                        $this->view->getcancel_data = $cancel_data;
                        
                        $mem5_count=$obj->getmember5_count($dayfrom,$tofrom);
                        foreach($mem5_count as $nn){
                             
                             $this->view->getmember5_count_data = $nn['count'];
                            
                        }                        
                        $mem5_data=$obj->getmember5_discount($dayfrom,$tofrom);
                        $this->view->getmember5_dis_data = $mem5_data;
                        
                        
                        
                        
                        $mem15_count=$obj->getmember15_count($dayfrom,$tofrom);
                        foreach($mem15_count as $nn){
                             
                             $this->view->getmember15_count_data = $nn['count'];
                            
                        }
                        $mem15_data=$obj->getmember15_discount($dayfrom,$tofrom);
                        $this->view->getmember15_dis_data = $mem15_data;
                        
                        
                        
                        
                        $mem15_10_count=$obj->getmember15_10_count($dayfrom,$tofrom);
                        foreach($mem15_10_count as $nn){
                             
                             $this->view->getmember15_10_count_data = $nn['count'];
                            
                        }
                        $mem15_10_data=$obj->getmember15_10_discount($dayfrom,$tofrom);
                        $this->view->getmember15_10_dis_data = $mem15_10_data;
                        
                        
                        
                        $mem25_count=$obj->getmember25_count($dayfrom,$tofrom);
                        foreach($mem25_count as $nn){
                             
                             $this->view->getmember25_count_data = $nn['count'];
                            
                        }
                        $mem25_data=$obj->getmember25_discount($dayfrom,$tofrom);
                        $this->view->getmember25_dis_data = $mem25_data;
                        
                        
                        
                        $mem50_count=$obj->getmember50_count($dayfrom,$tofrom);
                        foreach($mem50_count as $nn){
                             
                             $this->view->getmember50_count_data = $nn['count'];
                            
                        }
                        $mem50_data=$obj->getmember50_discount($dayfrom,$tofrom);
                        $this->view->getmember50_dis_data = $mem50_data;
                        
                        
                        
                        $count_discount_trn2=$obj->count_discount_trn2($dayfrom,$tofrom);
                        foreach($count_discount_trn2 as $nn){
                             
                             $this->view->count_discount_trn2 = $nn['count'];
                            
                        }
                        $disc_data=$obj->get_discount_trn2($dayfrom,$tofrom);
                        $this->view->disc_data = $disc_data;
                        
                        
                        
                        $count_coupon_discount=$obj->count_coupon_discount($dayfrom,$tofrom);
                        foreach($count_coupon_discount as $nn){
                             
                             $this->view->count_coupon_discount = $nn['count'];
                            
                        }
                        $coupon_disc_data=$obj->get_coupon_discount($dayfrom,$tofrom);
                        $this->view->coupon_disc_data = $coupon_disc_data;
                        
                        
                        
                        
                        $count_newbirth=$obj->count_newbirth($dayfrom,$tofrom);
                        foreach($count_newbirth as $nn){
                             
                             $this->view->count_newbirth = $nn['count'];
                            
                        }
                        $newbirth_data=$obj->get_newbirth($dayfrom,$tofrom);
                        $this->view->newbirth_data = $newbirth_data;
                        
                        
                        
                        } //end post
                        
                        
                        
                   
           	}

           	public function commissionAction(){
                //  $this->_helper->layout()->disableLayout();
                   
           	} 
           	public function viewcommAction(){
               $this->_helper->layout()->disableLayout();
                   
                   
                        
			
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Report();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $this->view->ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint;                        
                      	$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                      
                        
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        
						$detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                    	$get_printer = $obj->getprinter();
                        foreach($get_printer as $p){
                        $this->view->get_printer = $p['thermal_printer'];                           
                        }
                        
                        $commdetail=$obj->getcomm($dayfrom,$tofrom);
                        $this->view->getcomm_data = $commdetail;
                    
                        
				//		$commdetail=$obj->getcomm($dayfrom,$tofrom);
                  //      $this->view->comm_data = $commdetail;
                        
                        
                    }//end post
           	} // end viewcomm
           	public function printcommAction(){
               $this->_helper->layout()->disableLayout();
                   
                   
                        
			
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Report();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $this->view->ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint;                        
                      	$this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        
                        $filter = new Zend_Filter_StripTags();
                        $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                        $tofrom = $filter->filter($this->_request->getPost('end_date'));
                      
                        
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        
						$detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                    	$get_printer = $obj->getprinter();
                        foreach($get_printer as $p){
                        $this->view->get_printer = $p['thermal_printer'];                           
                        }
                        
                        $commdetail=$obj->getcomm($dayfrom,$tofrom);
                        $this->view->getcomm_data = $commdetail;
                    
                        
                        
                        
                    }//end post
           	} // end printcomm
           	
           	public function mtpremiumAction(){
           		$this->_helper->layout()->disableLayout();
           	if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Reportpremium();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $ptime = date('H:i:s');
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint."  ".$ptime;                                                   
                       
                        $filter = new Zend_Filter_StripTags();
                        $product_start = $filter->filter($this->_request->getPost('product1'));
                        $product_end = $filter->filter($this->_request->getPost('product2'));
                        $dayfrom = $filter->filter($this->_request->getPost('data1'));
                        $tofrom = $filter->filter($this->_request->getPost('data2'));
                        
                      
                        /*if($product_start =="" or $product_end =="" or $dayfrom =="" or $tofrom ==""){
                            
                            echo " <script language='javascript'>
                                alert('กรุณากรอกข้อมูลให้ครบถ้วน')
                                </script>";
                        }else{ */
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;                   
                        
			                       
                        $premium_data=$obj->getpremium($dayfrom,$tofrom,$product_start,$product_end);
                        $this->view->getpremium_data = $premium_data;
                        
                        $detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        $this->view->product_start = $product_start;
                        $this->view->product_end = $product_end;
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                  }
           	}
           	
	public function printmt2Action(){
                //  $this->_helper->layout()->disableLayout();
                
                    if ($this->getRequest()->isPost()) {
		        
                        $obj=new Model_Report();
                        
                        $dayprint = date('d');
                        $monthprint =date('m');
                        $yearprint = date('Y')+543;
                        $pdetail=$obj->chkmonth($monthprint);
                        $this->view->ptime = date('H:i:s');
                        $this->view->print_time = $dayprint." ".$pdetail." ".$yearprint;                        
                        $this->view->branch_id=$this->empprofile['branch_id'];
                  		$this->view->branch_name=$this->empprofile['branch_name'];
                        $filter = new Zend_Filter_StripTags();
                        $dayfrom = $filter->filter($this->_request->getPost('start_date'));
                        $tofrom = $filter->filter($this->_request->getPost('end_date'));
                      
                        $this->view->dayfrom = $dayfrom;
                        $this->view->tofrom = $tofrom;
                        
                        $fday = substr($dayfrom, -2);
                        $fmonth = substr($dayfrom, 5, 2);
                        $fyear = substr($dayfrom, 0,4)+543;
                        
                        $tday = substr($tofrom,-2);
                        $tmonth = substr($tofrom, 5, 2);
                        $tyear = substr($tofrom, 0,4)+543;
                        
                        
						$detail=$obj->chkmonth($fmonth);
						$this->view->s_date= $fday." ".$detail." ".$fyear;
                        
                        $detail2=$obj->chkmonth($tmonth);
                        $this->view->e_date= $tday." ".$detail2." ".$tyear;
                        
                        
                        $get_doc_date = $obj->getdoc_date();
                        foreach($get_doc_date as $a){
                             $this->view->get_doc_date = $a['doc_date'];                           
                        }
                        
                        
						$amountdetail=$obj->getamount($dayfrom,$tofrom);
                        
                        foreach($amountdetail as $a){
                             $this->view->amount_data = substr($a['amount'], 0, -2);
                             
                        }
                        
                        $discountdetail=$obj->getdiscount($dayfrom,$tofrom);
                        foreach($discountdetail as $b){
                             $this->view->discount_data = $obj->getSatang($b['discount'],'up');
                            
                        }
                        
                        
                        $creditdetail=$obj->getcredit($dayfrom,$tofrom);
                        foreach($creditdetail as $b){
                             $credit_data = substr($b['amount'], 0, -2);
                            
                        }

                        $creditaba = $obj->getaba($dayfrom, $tofrom);
                        foreach ($creditaba as $b) {
                             $this->view->credit_aba = substr($b['amount'], 0, -2);
                         //     $credit_data = substr($b['amount'], 0, -2);
                        }
                        
                        $getcncredit=$obj->getCN_credit($dayfrom,$tofrom);
                        foreach($getcncredit as $mm){
                             
                             $getcn_credit = $mm['net'];
                            
                        }
                        
                        $this->view->credit_data = $credit_data-$getcn_credit;
                        
                        $this->view->getcn_credit = $getcn_credit;
                        
                        
                        $netamtdetail=$obj->getnetamt($dayfrom,$tofrom);
                        foreach($netamtdetail as $c){
                             $netamt_data = substr($c['netamt'], 0, -2);
                            
                        }
                        
                        $cashcoupondetail=$obj->getpaycashcoupon($dayfrom,$tofrom);
                        foreach($cashcoupondetail as $cc){
                             $cashcoupon_data = substr($cc['pay_cash_coupon'], 0, -2);
                            
                        }
                        
                        
                        $this->view->netcashcoupon =$netamt_data+$cashcoupon_data;
                        
                        $cashdetail=$obj->getcash($dayfrom,$tofrom);
                        foreach($cashdetail as $d){
                             $this->view->cash_data = substr($d['cash'], 0, -2);
                            
                        }
                        
                        $incomedetail=$obj->getincome($dayfrom,$tofrom);
                        foreach($incomedetail as $e){
                             $this->view->qincome_data = substr($e['quantity'], 0, -2);
                             $this->view->aincome_data = substr($e['amount'], 0, -2);
                        }
                        
                        $outcomedetail=$obj->getoutcome($dayfrom,$tofrom);
                        foreach($outcomedetail as $f){
                             $this->view->qoutcome_data = substr($f['quantity'], 0, -2);
                             $this->view->aoutcome_data = substr($f['amount'], 0, -2);
                        }
                        
                        $getchdetail=$obj->getchdata($dayfrom,$tofrom);
                        foreach($getchdetail as $g){
                             $this->view->getquan_data = substr($g['quantity'], 0, -2);
                             $this->view->getamt_data = substr($g['amount'], 0, -2);
                             $this->view->getgross_data = substr($g['gross'], 0, -2);
                        }
                        
                        $getbackdetail=$obj->getback($dayfrom,$tofrom);
                        foreach($getbackdetail as $h){
                             $this->view->qback_data = substr($h['quantity'], 0, -2);
                             $this->view->qbackamt_data = substr($h['amount'], 0, -2);
                             $this->view->qbackgross_data = substr($h['gross'], 0, -2);
                        }
                        
                        $getotherdetail=$obj->getchother($dayfrom,$tofrom);
                        foreach($getotherdetail as $i){
                             $this->view->qother_data = substr($i['quantity'], 0, -2);
                             $this->view->otheramt_data = substr($i['amount'], 0, -2);
                        }
                        
                        $getchincomedetail=$obj->getchincome($dayfrom,$tofrom);
                        foreach($getchincomedetail as $j){
                             $this->view->qchincome_data = substr($j['quantity'], 0, -2);
                             $this->view->chincomeamt_data = substr($j['amount'], 0, -2);
                        }
                        
                        $getchoutcomedetail=$obj->getchoutcome($dayfrom,$tofrom);
                        foreach($getchoutcomedetail as $k){
                             $this->view->qchoutcome_data = substr($k['quantity'], 0, -2);
                             $this->view->choutcomeamt_data = substr($k['amount'], 0, -2);
                        }
                        
                        $getbilldetail=$obj->getbill($dayfrom,$tofrom);
                        foreach($getbilldetail as $l){
                             
                             $this->view->getbill_data = substr($l['amount'], 0, -2);
                        }
                        
                        
                        $newmem=$obj->getnewmember($dayfrom,$tofrom);
                        $this->view->getmember_data = $newmem;
                                             
                       
                    	$memcount=$obj->getcountmem($dayfrom,$tofrom);
                        foreach($memcount as $l){
                             
                             $this->view->getmember_data2 = $l['count'];
                             $this->view->getmemamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $newwalkin=$obj->getwalkin($dayfrom,$tofrom);
                        foreach($newwalkin as $l){
                             
                             $this->view->getwalkin_data = $l['count'];
                             $this->view->getwkamt_data = substr($l['amt'], 0, -2);
                        }
                        
                        
                        $getcncash=$obj->getCN_cash($dayfrom,$tofrom);
                        foreach($getcncash as $nn){
                             
                             $this->view->getcn_cash = $nn['net'];
                            
                        }
                       
                        $this->view->payin = $this->view->amount_data - $this->view->discount_data - $this->view->credit_data - $this->view->netcashcoupon - $this->view->getcn_cash  - $this->view->getcn_credit;
                        
                        $paid_data=$obj->getcreditkbank($dayfrom,$tofrom);
                        $this->view->getpaid_data = $paid_data;
                        
                        $paid_data2=$obj->getcreditbbl($dayfrom,$tofrom);
                        $this->view->getpaid_data2 = $paid_data2;

                        $paid_credit = $obj->getcreditmain($dayfrom,$tofrom); 
                        $this->view->getpaid_credit = $paid_credit;
                        
                        $paid_data3=$obj->getcreditscb($dayfrom,$tofrom);
                        $this->view->getpaid_data3 = $paid_data3;

                        $paid_data4=$obj->getcreditaba($dayfrom,$tofrom);
                        $this->view->getpaid_data4 = $paid_data4;
                        
                        $bill_data=$obj->getbill_no($dayfrom,$tofrom);
                        $this->view->getbill_datano = $bill_data;
                        
                        
                        $cancel_data=$obj->getcancel($dayfrom,$tofrom);
                        $this->view->getcancel_data = $cancel_data;
                        
                        $mem5_count=$obj->getmember5_count($dayfrom,$tofrom);
                        foreach($mem5_count as $nn){
                             
                             $this->view->getmember5_count_data = $nn['count'];
                            
                        }                        
                        $mem5_data=$obj->getmember5_discount($dayfrom,$tofrom);
                        $this->view->getmember5_dis_data = $mem5_data;
                        
                        
                        
                        
                        $mem15_count=$obj->getmember15_count($dayfrom,$tofrom);
                        foreach($mem15_count as $nn){
                             
                             $this->view->getmember15_count_data = $nn['count'];
                            
                        }
                        $mem15_data=$obj->getmember15_discount($dayfrom,$tofrom);
                        $this->view->getmember15_dis_data = $mem15_data;
                        
                        
                        
                        
                        $mem15_10_count=$obj->getmember15_10_count($dayfrom,$tofrom);
                        foreach($mem15_10_count as $nn){
                             
                             $this->view->getmember15_10_count_data = $nn['count'];
                            
                        }
                        $mem15_10_data=$obj->getmember15_10_discount($dayfrom,$tofrom);
                        $this->view->getmember15_10_dis_data = $mem15_10_data;
                        
                        
                        
                        $mem25_count=$obj->getmember25_count($dayfrom,$tofrom);
                        foreach($mem25_count as $nn){
                             
                             $this->view->getmember25_count_data = $nn['count'];
                            
                        }
                        $mem25_data=$obj->getmember25_discount($dayfrom,$tofrom);
                        $this->view->getmember25_dis_data = $mem25_data;
                        
                        
                        
                        $mem50_count=$obj->getmember50_count($dayfrom,$tofrom);
                        foreach($mem50_count as $nn){
                             
                             $this->view->getmember50_count_data = $nn['count'];
                            
                        }
                        $mem50_data=$obj->getmember50_discount($dayfrom,$tofrom);
                        $this->view->getmember50_dis_data = $mem50_data;
                        
                        
                        
                        $count_discount_trn2=$obj->count_discount_trn2($dayfrom,$tofrom);
                        foreach($count_discount_trn2 as $nn){
                             
                             $this->view->count_discount_trn2 = $nn['count'];
                            
                        }
                        $disc_data=$obj->get_discount_trn2($dayfrom,$tofrom);
                        $this->view->disc_data = $disc_data;
                        
                        
                        
                        $count_coupon_discount=$obj->count_coupon_discount($dayfrom,$tofrom);
                        foreach($count_coupon_discount as $nn){
                             
                             $this->view->count_coupon_discount = $nn['count'];
                            
                        }
                        $coupon_disc_data=$obj->get_coupon_discount($dayfrom,$tofrom);
                        $this->view->coupon_disc_data = $coupon_disc_data;
                        
                        
                        
                        
                        $count_newbirth=$obj->count_newbirth($dayfrom,$tofrom);
                        foreach($count_newbirth as $nn){
                             
                             $this->view->count_newbirth = $nn['count'];
                            
                        }
                        $newbirth_data=$obj->get_newbirth($dayfrom,$tofrom);
                        $this->view->newbirth_data = $newbirth_data;
                        
                        
                        
                        } //end post
                        
                        
                        
                   
           	}
           	
	}
?>
