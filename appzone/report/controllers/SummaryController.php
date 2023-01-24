<?php 
class SummaryController extends Zend_Controller_Action{
		public $_CORPORATION_ID;
		public $_COMPANY_ID;
		public $_BRANCH_ID;
		public $_BRANCH_NO;
		public $_BRANCH_NAME;
		public $_USER_ID;
		public $_USER_NAME;
		public $_USER_SURNAME;
		public $_DOC_DATE;
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('report_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$templete="templete_".$brand;
			$user_id="";
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if(!empty($empprofile)){
				$this->_CORPORATION_ID=$empprofile['corporation_id'];
				$this->_COMPANY_ID=$empprofile['company_id'];
				$this->_BRANCH_ID=$empprofile['branch_id'];
				$this->_BRANCH_NO=$empprofile['branch_no'];
				$this->_BRANCH_NAME=$empprofile['branch_name'];
				$this->_USER_NAME=$empprofile['name'];
				$this->_USER_SURNAME=$empprofile['surname'];
				$this->_COMPANY_ID=strtolower($empprofile['company_id']);
				$this->_USER_ID=$empprofile['user_id'];
				$templete="templete_".$this->_COMPANY_ID;
		     	if(!empty($this->_USER_ID)){
					$this->view->checklogin=1;
				}
				
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$this->_DOC_DATE=$objPos->getDocDatePos($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_BRANCH_NO);
				
			}
			
			$this->view->arr_layout=array(
						'brand'=>$templete,
						'shop'=>$shop,
						'comno'=>$comno,
						'user_id'=>$user_id
			);
			$this->view->templete=$templete;
		}
		
		public function preDispatch(){
		}
		
		public function indexAction(){
		}
		
		public function zreportAction(){
			
		}//func
		
		public function formselprinterAction(){			
			//select printer name
			$objSum=new Model_Summary();
			$this->view->arr_printer=$objSum->getPrinterName();
		}//func
		
		public function rptzreportAction(){
			/***
			 * @desc file to print
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$doc_date=$filter->filter($this->getRequest()->getPost('date_start'));		
				$this->view->corporation_id=$this->_CORPORATION_ID;
				$this->view->company_id=$this->_COMPANY_ID;
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_no=$this->_BRANCH_NO;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->name=$this->_USER_NAME;
				$this->view->surname=$this->_USER_SURNAME;
				$this->view->doc_date=$this->_DOC_DATE;
				
				/////////////////////// START ============
				$objSum=new Model_Summary();				
				$objRpt=new Model_Report();	


				$arr_sale_date=explode('-',$doc_date);
				$sale_date=$arr_sale_date[2]."/".$arr_sale_date[1]."/".$arr_sale_date[0];
				$this->view->sale_date=$sale_date;

			

				
				$this->view->other_credit = $objRpt->getOtherCredit($doc_date,$doc_date);

				$get_allcredit =$objRpt->getallcredit($doc_date,$doc_date);
				$pay_allcredit = $get_allcredit[0]['amount'];

					
				$sale_quantity=$objSum->getSumQtyByDay($doc_date);//จำนวนชิ้นที่ขาย
				$this->view->sale_quantity=$sale_quantity;
				$sale_net_amt=$objSum->getSumNetByDay($doc_date);//จำนวนเงิน net
				$this->view->sale_net_amt=$sale_net_amt;
				$sale_amount=$objSum->getSumAmtByDay($doc_date);//จำนวนเงิน amount
				$this->view->sale_amount=$sale_amount;
				$bill_total=$objSum->getCountBillByDay($doc_date);//จำนวนบิลทั้งหมด
				$this->view->bill_total=$bill_total;
				//$bill_net_amt=$bill_net_amt;//จำนวนเงินสุทธิ
					
				$pay_cash=$objSum->getSumPayCash($doc_date);//จำนวนเงินสดสุทธิ
				$this->view->pay_cash=$pay_cash;
				$pay_credit=$objSum->getSumPayCredit($doc_date);//จำนวนเครดิตสุทธิ
				$this->view->pay_credit=$pay_credit;
				$pay_cn=$objSum->getSumPayCn($doc_date);//จำนวนเงินเปลี่ยนสินค้าสุทธิ
				$this->view->pay_cn=$pay_cn;
				$bill_total_check=0;
				$this->view->bill_total_check=$bill_total_check;
					
				$tax_amt=$sale_net_amt;//จำนวนเงินรวมสุทธิที่นำมาคำนวนภาษี
				$this->view->tax_amt=$tax_amt;
				
				$tax=($sale_net_amt*10)/100;//จำนนวนเงิน ภาษี(จำนวนเงิน *10/110)
				$this->view->tax=$tax;
				// $gt=$pay_cash+$pay_credit+$pay_cn+$bill_total_check;//จำนวนเงินสุทธิ = CASH + CREDIT + CHANGE + CHECK

				$gt = $pay_cash+$pay_allcredit+$pay_cn+$bill_total_check; //จำนวนเงินสุทธิ = CASH + CREDIT + CHANGE + CHECK
				$this->view->gt=$gt;
				
				$num_customer=$objSum->getNumCustomer($doc_date);
				//$discount_day=$this->getDiscountDay($doc_date);
				$this->view->num_customer=$num_customer;
				//$this->view->discount_day=$discount_day;
				$this->view->discount_day=$sale_amount-$sale_net_amt;
			
				$this->view->settlement_number=$objSum->getSettlementNumber($doc_date);
				/////////////////////// END    ============
		
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->user_id=$this->_USER_ID;
				$this->view->user_fullname=$this->_USER_NAME." ".$this->_USER_SURNAME;
				$arr_dstart=explode('-',$date_start);
				$this->view->date_start=$arr_dstart[2]."/".$arr_dstart[1]."/".$arr_dstart[0];
				$arr_dstop=explode('-',$date_stop);
				$this->view->date_stop=$arr_dstop[2]."/".$arr_dstop[1]."/".$arr_dstop[0];
		
			}
		}//func
                
		public function rptdailysalesAction(){
			/***
			 * @desc file to print
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				
				$date_start=$filter->filter($this->getRequest()->getPost('date_start'));
				$date_stop=$filter->filter($this->getRequest()->getPost('date_stop'));			
				
				/////////////////////// START ============
				
				$this->view->corporation_id=$this->_CORPORATION_ID;
				$this->view->company_id=$this->_COMPANY_ID;
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_no=$this->_BRANCH_NO;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->name=$this->_USER_NAME;
				$this->view->surname=$this->_USER_SURNAME;
				$this->view->doc_date=$this->_DOC_DATE;				
				
				$objSum=new Model_Summary();				
				$total_sales=$objSum->getTotalSales($date_start,$date_stop);
				$this->view->total_sales_show=number_format($total_sales,2,".","");
				
				$vat=($total_sales*10)/110;
				$this->view->vat_show=number_format($vat,2,".","");
				
				$net_sales=$total_sales-$vat;
				$this->view->net_sales_show=number_format($net_sales,2,".","");
					
				$this->view->total_bill=$objSum->getSumBills($date_start,$date_stop);
				
				$total_cash_sales=$objSum->getSumCashSales($date_start,$date_stop);
				$this->view->total_cash_sales_show=number_format($total_cash_sales,2,".","");
					
				$total_credit_sales=$objSum->getSumCreditSales($date_start,$date_stop);
				$this->view->total_credit_sales_show=number_format($total_credit_sales,2,".","");
					
				$this->view->total_credit_bill=$objSum->getCountCreditSales($date_start,$date_stop);
					
				$total_cn_sales=$objSum->getSumCnSales($date_start,$date_stop);
				$this->view->total_cn_sales_show=number_format($total_cn_sales,2,".","");
					
				$this->view->total_cn=$objSum->getCountCnSales($date_start,$date_stop);
				/////////////////////// END    ============
				
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->user_id=$this->_USER_ID;
				$this->view->user_fullname=$this->_USER_NAME." ".$this->_USER_SURNAME;
				
// 				$objPos=new SSUP_Controller_Plugin_PosGlobal();
// 				$this->view->doc_date=$objPos->getDocDatePos($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_BRANCH_NO);
				
				$arr_dstart=explode('-',$date_start);
				$this->view->date_start=$arr_dstart[2]."/".$arr_dstart[1]."/".$arr_dstart[0];
				$arr_dstop=explode('-',$date_stop);
				$this->view->date_stop=$arr_dstop[2]."/".$arr_dstop[1]."/".$arr_dstop[0];
				
			}			
		}//func
		
		public function rptdailysalesa4Action(){
			/***
			 * @desc file to print
			 */
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$this->view->printer_name=$filter->filter($this->getRequest()->getPost('printer_name'));				
				$date_start=$filter->filter($this->getRequest()->getPost('date_start'));
				$date_stop=$filter->filter($this->getRequest()->getPost('date_stop'));
				
				//echo $date_start."=====".$date_stop;
		
				/////////////////////// START ============
		
				$this->view->corporation_id=$this->_CORPORATION_ID;
				$this->view->company_id=$this->_COMPANY_ID;
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_no=$this->_BRANCH_NO;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->name=$this->_USER_NAME;
				$this->view->surname=$this->_USER_SURNAME;
				$this->view->doc_date=$this->_DOC_DATE;
		
				$objSum=new Model_Summary();

				$objRpt=new Model_Report();

				
				$this->view->other_credit = $objRpt->getOtherCredit($date_start,$date_stop);

				$this->view->print2ip=$objSum->print2Printer();
				$total_sales=$objSum->getTotalSales($date_start,$date_stop);
				$this->view->total_sales_show=number_format($total_sales,2,".","");
		
				$vat=($total_sales*10)/110;
				$this->view->vat_show=number_format($vat,2,".","");
		
				$net_sales=$total_sales-$vat;
				$this->view->net_sales_show=number_format($net_sales,2,".","");
					
				$this->view->total_bill=$objSum->getSumBills($date_start,$date_stop);
		
				$total_cash_sales=$objSum->getSumCashSales($date_start,$date_stop);
				$this->view->total_cash_sales_show=number_format($total_cash_sales,2,".","");
					
				$total_credit_sales=$objSum->getSumCreditSales($date_start,$date_stop);
				$this->view->total_credit_sales_show=number_format($total_credit_sales,2,".","");
					
				$this->view->total_credit_bill=$objSum->getCountCreditSales($date_start,$date_stop);
					
				$total_cn_sales=$objSum->getSumCnSales($date_start,$date_stop);
				$this->view->total_cn_sales_show=number_format($total_cn_sales,2,".","");
					
				$this->view->total_cn=$objSum->getCountCnSales($date_start,$date_stop);
				/////////////////////// END    ============
		
				$this->view->branch_id=$this->_BRANCH_ID;
				$this->view->branch_name=$this->_BRANCH_NAME;
				$this->view->user_id=$this->_USER_ID;
				$this->view->user_fullname=$this->_USER_NAME." ".$this->_USER_SURNAME;
		
				// 				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				// 				$this->view->doc_date=$objPos->getDocDatePos($this->_CORPORATION_ID,$this->_COMPANY_ID,$this->_BRANCH_ID,$this->_BRANCH_NO);
		
				$arr_dstart=explode('-',$date_start);
				$this->view->date_start=$arr_dstart[2]."/".$arr_dstart[1]."/".$arr_dstart[0];
				$arr_dstop=explode('-',$date_stop);
				$this->view->date_stop=$arr_dstop[2]."/".$arr_dstop[1]."/".$arr_dstop[0];
				
				//*WR23052018
				$this->view->arrBranchDetail=$objSum->getBranchDetail();
		
			}
		}//func
		
		public function rptdailysalespreviewsAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$date_start=$filter->filter($this->getRequest()->getPost('date_start'));
				$date_stop=$filter->filter($this->getRequest()->getPost('date_stop'));				
				$objRpt=new Model_Summary();
				$objRpt->dailysalesPreviews($date_start,$date_stop);
			}			
		}//func
		
		public function zreportpreviewsAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$filter = new Zend_Filter_StripTags();
			if ($this->_request->isPost()){
				$date_start=$filter->filter($this->getRequest()->getPost('date_start'));
				$objRpt=new Model_Summary();
				$objRpt->zreportPreviews($date_start);
			}
		}//func
                
   }//class
   ?>