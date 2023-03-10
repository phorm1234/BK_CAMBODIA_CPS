<?php 
	class Model_Summary{
		public $db;
		public $m_lock_status;
		public $m_corporation_id;
		public $m_company_id;
		public $m_branch_id;
		public $m_branch_no;
		public $m_computer_no;
		public $m_com_ip;
		public $m_pos_id;
		public $m_thermal_printer;
		public $employee_id;
		public $user_id;
		public $year;
		public $month;
		public $msg_error="";
		public $msg_show="";
		public $doc_date="";
		public $user_fullname;
		public function __construct(){
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$this->m_lock_status=$empprofile['lock_status'];
			$this->m_corporation_id=$empprofile['corporation_id'];
			$this->m_company_id=$empprofile['company_id'];
			$this->m_branch_id=$empprofile['branch_id'];
			$this->m_branch_no=$empprofile['branch_no'];
			$this->m_computer_no=$empprofile['computer_no'];
			$this->m_com_ip=$empprofile['com_ip'];
			$this->m_pos_id=$empprofile['pos_id'];
			$this->m_thermal_printer=$empprofile['thermal_printer'];
			$this->employee_id=$empprofile['employee_id'];
			$this->user_id=$empprofile['user_id'];
			$this->user_fullname=$empprofile['name']." ".$empprofile['surname'];
			$this->year=date("Y");
			$this->month=date("m");
			$objPos=new SSUP_Controller_Plugin_PosGlobal();
			$this->doc_date=$objPos->getDocDatePos($this->m_corporation_id,$this->m_company_id,$this->m_branch_id,$this->m_branch_no);
			$this->db=Zend_Registry::get('dbOfline'); 
			$this->msg_error="";
			$this->msg_show="";
		}//func
		
		function getBranchDetail(){
		    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		    $sql_data="SELECT * FROM com_branch_detail
							WHERE branch_id='$this->m_branch_id' ";
		    $rows_b=$this->db->fetchAll($sql_data);
		    return $rows_b[0];
		}//func
		
		function print2Printer(){
			/**
			 * @desc
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($this->m_com_ip=='127.0.0.1'){
				$print2ip='LOCAL';
			}else{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
				$sql_chk="SELECT COUNT(*)
				FROM com_branch_computer
				WHERE
				corporation_id='$this->m_corporation_id' AND
				company_id='$this->m_company_id' AND
				branch_id='$this->m_branch_id' AND
				com_ip='$this->m_com_ip' AND
				computer_no<>'1' AND
				thermal_printer='Y'";
				$n_chk=$this->db->fetchOne($sql_chk);
				if($n_chk>0){
					$print2ip=$this->m_com_ip;
				}else{
					$print2ip='LOCAL';
				}
			}
			return $print2ip;
		}//func
		
		function getPrinterName(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT * FROM com_printer
							WHERE status='Y' ORDER BY printer_name ASC";
			$rows_printer=$this->db->fetchAll($sql_data);
			return $rows_printer;
		}//func
		
		public function getSettlementNumber($doc_date){
			/**
			 * @name
			 * @desc
			 * @param
			 * @return
			 */
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_doc_no");
			$arr_date=explode('-',$doc_date);
			$y=$arr_date[0];
			$sql_docno="SELECT COUNT( * ) AS sno
			FROM `com_dayend_time`
			WHERE
			branch_id='$this->m_branch_id' AND
			YEAR(dayend_date)='$y' AND
			dayend_date <= '$doc_date'";
			$n_docno=$this->db->fetchOne($sql_docno);
			if($n_docno>0){
				$s_doc_no=str_pad($n_docno,6,"0",STR_PAD_LEFT);
			}else{
				$s_doc_no="000001";
			}
			return $s_doc_no;				
		}//func
		
		
		function getNumCustomer($doc_date){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT COUNT(*) FROM trn_diary1
							WHERE doc_date='$doc_date' AND flag<>'C' AND doc_tp IN('SL','VT')";
			$numofcust=$this->db->fetchOne($sql_data);
			return $numofcust;
		}//func
		
		function getDiscountDay($doc_date){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT SUM(discount+member_discount1) AS discount FROM trn_diary1
			WHERE doc_date='$doc_date' AND flag<>'C' AND doc_tp IN('SL','VT')";
			$numofcust=$this->db->fetchOne($sql_data);
			return $numofcust;
		}//func
		
		function getCountCnSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT COUNT(*) AS total_sales FROM trn_diary1
			WHERE status_no='41' AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func


		
		function getSumCnSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT SUM(net_amt) AS total_sales FROM trn_diary1
			WHERE status_no='41'  AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func
		
		function getCountCreditSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT COUNT(*) AS total_sales FROM trn_diary1
			WHERE paid='0002' AND  doc_tp IN('SL','VT') AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func
		
		function getSumCreditSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT SUM(net_amt) AS total_sales FROM trn_diary1
			WHERE paid='0002' AND  doc_tp IN('SL','VT') AND (`bank_tp` in ('Credit Card') OR `credit_tp` = '0002')  AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func
		// function getSumCreditSales($date_start,$date_stop){
		// 	/**
		// 	 * @desc
		// 	 * @param
		// 	 */
		// 	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		// 	$sql_data="SELECT SUM(net_amt) AS total_sales FROM trn_diary1
		// 	WHERE paid='0002' AND  doc_tp IN('SL','VT') AND bank_tp NOT IN('ABA PAY+','ABA PAY')  AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
		// 	$total_sales=$this->db->fetchOne($sql_data);
		// 	return $total_sales;
		// }//func

		function getSum???AbaSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT SUM(net_amt) AS total_sales FROM trn_diary1
			WHERE paid='0002' AND  doc_tp IN('SL','VT') AND bank_tp IN('ABA PAY+','ABA PAY') AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func
		
		
		function getSumCashSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT SUM(net_amt) AS total_sales FROM trn_diary1 
			WHERE paid='0000' AND  doc_tp IN('SL','VT') AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func
		
		function getSumBills($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_data="SELECT COUNT(*) AS total_bill FROM trn_diary1
			WHERE doc_tp IN('SL','VT') AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_bill=$this->db->fetchOne($sql_data);
			return $total_bill;
		}//func
		
		function getTotalSales($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
			$sql_data="SELECT SUM(net_amt) AS total_sales FROM trn_diary1 
								WHERE doc_tp IN('SL','VT') AND  flag<>'C' AND 	doc_date BETWEEN '$date_start'  AND '$date_stop'";
			$total_sales=$this->db->fetchOne($sql_data);
			return $total_sales;
		}//func
		
		//--------------------------------------------------------------------------
		function getSumQtyByDay($doc_date){
			/**
			 *@desc
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
			$sql_sumqty="SELECT SUM(quantity) as sum_qty FROM trn_diary1 
								WHERE doc_date='$doc_date' AND flag<>'C' AND doc_tp IN('SL','VT')";
			$n_sumqty=$this->db->fetchOne($sql_sumqty);
			return $n_sumqty;
		}//func
		
		function getSumNetByDay($doc_date){
			/**
			 *@desc
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT SUM(net_amt) as sum_net FROM trn_diary1 
									WHERE doc_date='$doc_date' AND flag<>'C' AND doc_tp IN('SL','VT')";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}//func
		
		function getSumAmtByDay($doc_date){
			/**
			 *@desc
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT SUM(amount) as sum_amt FROM trn_diary1 
										WHERE doc_date='$doc_date' AND flag<>'C' AND doc_tp IN('SL','VT')";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}//func
		
		function getCountBillByDay($doc_date){
			/**
			 *@desc
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT COUNT(*) as bill_total FROM trn_diary1 
									WHERE doc_date='$doc_date' AND flag<>'C' AND doc_tp IN('SL','VT')";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}//func
		
		function getSumPayCash($doc_date){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT SUM(net_amt) as pay_cash FROM trn_diary1 
							WHERE doc_date='$doc_date' AND paid='0000' AND flag<>'C' AND doc_tp IN('SL','VT')";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}//func
		
		// function getSumPayCredit($doc_date){
		// 	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		// 	if($doc_date=='')
		// 		return true;
		// 		$sql_sumqty="SELECT SUM(net_amt) as pay_cash FROM trn_diary1 
		// 							WHERE doc_date='$doc_date' AND paid='0002' AND flag<>'C' AND doc_tp IN('SL','VT') AND bank_tp NOT IN('ABA PAY+','ABA PAY')";
		// 		$n_sumqty=$this->db->fetchOne($sql_sumqty);
		// 		return $n_sumqty;
		// }//func

		function getSumPayCredit($doc_date){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT SUM(net_amt) as pay_cash FROM trn_diary1 
									WHERE doc_date='$doc_date' and `paid` ='0002' AND (`bank_tp` in ('Credit Card') OR `credit_tp` = '0002') AND flag<>'C' AND doc_tp IN('SL','VT') ";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}//func

		function getSumPayCreditABA($doc_date){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT SUM(net_amt) as pay_cash FROM trn_diary1 
									WHERE doc_date='$doc_date' AND paid='0002' AND flag<>'C' AND doc_tp IN('SL','VT') AND  bank_tp IN('ABA PAY+','ABA PAY')";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}
		
		function getSumPayCn($doc_date){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_date=='')
				return true;
				$sql_sumqty="SELECT SUM(net_amt) as pay_cash FROM trn_diary1 
									WHERE doc_date='$doc_date' AND doc_tp='CN' AND flag<>'C' AND status_no='40'";
				$n_sumqty=$this->db->fetchOne($sql_sumqty);
				return $n_sumqty;
		}//func
		
		function zreportPreviews($doc_date){
			/**
			 *@desc
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");

			$obj=new Model_Report();
			if($doc_date=='')
				return true;
			$today=$this->doc_date;
			$curr_date=date("Y-m-d");
			$arr_curr_date=explode('-',$curr_date);
			$curr_date=$arr_curr_date[2]."/".$arr_curr_date[1]."/".$arr_curr_date[0];
			
			$curr_time=date("H:i:s");
			
			$arr_sale_date=explode('-',$doc_date);
			$sale_date=$arr_sale_date[2]."/".$arr_sale_date[1]."/".$arr_sale_date[0];
			
			$sale_quantity=$this->getSumQtyByDay($doc_date);//?????????????????????????????????????????????
			$sale_net_amt=$this->getSumNetByDay($doc_date);//??????????????????????????? net
			$sale_amount=$this->getSumAmtByDay($doc_date);//??????????????????????????? amount			
			$bill_total=$this->getCountBillByDay($doc_date);//?????????????????????????????????????????????
			
			//$bill_net_amt=$bill_net_amt;//??????????????????????????????????????????
			
			$pay_cash=$this->getSumPayCash($doc_date);//????????????????????????????????????????????????
			$pay_credit=$this->getSumPayCredit($doc_date);//????????????????????????????????????????????????

			$pay_credit_aba=$this->getSumPayCreditABA($doc_date);//????????????????????????????????????????????????ABA
			
			$pay_cn=$this->getSumPayCn($doc_date);//?????????????????????????????????????????????????????????????????????????????????
			$bill_total_check=0;
			
			$tax_amt=$sale_net_amt;//???????????????????????????????????????????????????????????????????????????????????????????????????
			$tax=($sale_net_amt*10)/100;//?????????????????????????????? ????????????(??????????????????????????? *10/110)
			
			$num_customer=$this->getNumCustomer($doc_date);
			//$discount_day=$this->getDiscountDay($doc_date);
			$discount_day=$sale_amount-$sale_net_amt;

			$get_othercredit = $obj->getOtherCredit($doc_date,$doc_date);

			$get_allcredit =$obj->getallcredit($doc_date,$doc_date);
			$pay_allcredit = $get_allcredit[0]['amount'];
			// print_r($get_allcredit[0]['amount']);

			// $gt=$pay_cash+$pay_credit+$pay_cn+$bill_total_check;//?????????????????????????????????????????? = CASH + CREDIT + CHANGE + CHECK		
			$gt=$pay_cash+$pay_allcredit+$pay_cn+$bill_total_check;					
		//		$total_sales_show=number_format($total_sales,2,".","");	
			
			$settlement_number=$this->getSettlementNumber($doc_date);
			
				echo "<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr>
				<td colspan='4' align='center'><span style='font-size:26px;'><u> Z Report</u></span></td>
				</tr>
					
				<tr>
				<td align='left' width='40%'>REPORT DATE</td>
				<td align='center' width='20%'>&nbsp;</td>
				<td align='center' width='10%'>&nbsp;</td>
				<td align='right' width='30%'>PRINT DATE : $curr_date</td>
				</tr>
					
				<tr>
				<td>REPORT TIME</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align='right'>$curr_time</td>
				</tr>
					
				<tr>
				<td>SETTLEMENT&nbsp;</td>
				<td align='center'>DAILY</td>
				<td align='center'>Z</td>
				<td align='right'>$settlement_number</td>
				</tr>
				
				<tr>
				<td>NUMBER OF CUSTOMERS&nbsp;</td>
				<td align='center'>&nbsp;</td>
				<td align='center'>&nbsp;</td>
				<td align='right'>$num_customer</td>
				</tr>
					
				<tr>
				<td >SALES DATE</td>
				<td ></td>
				<td ></td>
				<td align='right'>$sale_date</td>
				</tr>
				<tr>
				<td >SALES</td>
				<td align='center'>QTY</td>
				<td></td>
				<td align='right'>".number_format($sale_quantity,2)."</td>
				</tr>
				<tr>
				<td  width='40%'>&nbsp;</td>
				<td   width='20%' align='center'>&nbsp;</td>
				<td  width='10%'>&nbsp;</td>
				<td   width='30%' align='right'>".number_format($sale_amount,2,".","")."</td>
				</tr>
						
						<tr>
				<td >DISCOUNT</td>
				<td align='center'>&nbsp;</td>
				<td></td>
				<td align='right'>".number_format($discount_day,2)."</td>
				</tr>
						
				</table>";
				echo "	<table width='85%' align='center'  border='0' cellpadding='0' cellspacing='0'>
				<tr><td align='center'>
				========================================================================
				</td></tr>
				</table>";
				
				echo "<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr>
				<td  width='40%'>GROSS SALES</td>
				<td   width='20%' align='center'>NO</td>
				<td  width='10%'></td>
				<td   width='30%' align='right'>".number_format($sale_quantity,2)."</td>
				</tr>
						
				<tr>
				<td  width='40%'>&nbsp;</td>
				<td   width='20%' align='center'>&nbsp;</td>
				<td  width='10%'>&nbsp;</td>
				<td   width='30%' align='right'>".number_format($sale_net_amt,2,".","")."</td>
				</tr>
			   </table>";
				echo "	<table width='85%' align='center'  border='0' cellpadding='0' cellspacing='0'>
				<tr><td align='center'>
				========================================================================
				</td></tr>
				</table>";
				$vat_xx=($sale_net_amt*10)/110;
				echo "<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr>
				<td  width='40%'>VAT</td>
				<td   width='20%' align='center'>&nbsp;</td>
				<td  width='10%'>&nbsp;</td>
				<td   width='30%' align='right'>".number_format($vat_xx,2)."</td>
				</tr>
				</table>";
				echo "	<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr><td align='center'>
				========================================================================
				</td></tr>
				</table>";
				$net_sales_xx=$sale_net_amt-$vat_xx;
				echo "<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr>
					<td  width='40%'>NET SALES</td>
					<td  width='20%' align='center'>&nbsp;</td>
					<td  width='10%'>&nbsp;</td>
					<td   width='30%' align='right'>".number_format($net_sales_xx,2)."</td>
				</tr>
				
				<tr>
				<td >CASH</td>
				<td align='center'></td>
				<td></td>
				<td align='right'>".number_format($pay_cash,2,".","")."</td>
				</tr>
			   <tr>
				<td >CREDIT</td>
				<td align='center'></td>
				<td></td>
				<td align='right'>".number_format($pay_credit,2,".","")."</td>
				</tr>";
				if(sizeof($get_othercredit)>0){
					// $count=7;
					foreach($get_othercredit as  $data_sub)
					{				
						echo "<td >".$data_sub['description']."</td>
						<td align='center'></td>
						<td></td>
						<td align='right'>".number_format($data_sub['amount'],2,".","")."</td>
						</tr>";
					}				
				}			
				echo"
				<tr>
				<td >CHANGE</td>
				<td align='center'></td>
				<td></td>
				<td align='right'>".number_format($pay_cn,2,".","")."</td>
				</tr>
				<tr>
				<td >CHECK</td>
				<td align='center'></td>
				<td></td>
				<td align='right'>0</td>
				</tr>
				</table>";
				
// 				echo "	<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
// 				<tr><td align='center'>
// 				========================================================================
// 				</td></tr>
// 				</table>";
				
// 				echo "<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
// 				<tr>
// 				<td  width='40%'>TAX-AMT</td>
// 				<td  width='20%' align='center'></td>
// 				<td  width='10%'>&nbsp;</td>
// 				<td  width='30%' align='right'>".number_format($tax_amt,2,".","")."</td>
// 				</tr>
// 				<tr>
// 				<td >TAX</td>
// 				<td align='center'></td>
// 				<td></td>
// 				<td align='right'>".number_format($tax,2,".","")."</td>
// 				</tr>
// 				</table>";
				echo "	<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr><td align='center'>
				&nbsp;
				</td></tr>
				</table>";
				echo "<table width='85%' align='center' border='0' cellpadding='0' cellspacing='0'>
				<tr>
				<td  width='40%'>GT</td>
				<td  width='20%' align='center'></td>
				<td  width='10%'>&nbsp;</td>
				<td  width='30%' align='right'>".number_format($gt,2,".","")."</td>
				</tr>
				</table>";
		}//func

		
		
		
		function dailysalesPreviews($date_start,$date_stop){		
			/**
			 * 
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$obj=new Model_Report();
			if($date_stop=='')
				$date_stop=$date_start;
			$total_sales=$this->getTotalSales($date_start,$date_stop);
			$total_sales_show=number_format($total_sales,2,".","");
			$vat=($total_sales*10)/110;
			$vat_show=number_format($vat,2,".","");
			$net_sales=$total_sales-$vat;
			$net_sales_show=number_format($net_sales,2,".","");
			
			$total_bill=$this->getSumBills($date_start,$date_stop);
			$total_cash_sales=$this->getSumCashSales($date_start,$date_stop);
			$total_cash_sales_show=number_format($total_cash_sales,2,".","");
			
			
			$total_credit_sales=$this->getSumCreditSales($date_start,$date_stop);

			$total_credit_sales_show=number_format($total_credit_sales,2,".","");

			$total_aba_sales=$this->getSum???AbaSales($date_start,$date_stop);
			$total_aba_sales_show=number_format($total_aba_sales,2,".","");
			
			
			$total_credit_bill=$this->getCountCreditSales($date_start,$date_stop);
			
			$total_cn_sales=$this->getSumCnSales($date_start,$date_stop);
			$total_cn_sales_show=number_format($total_cn_sales,2,".","");
			$get_othercredit = $obj->getOtherCredit($date_start,$date_stop);
			$total_cn=$this->getCountCnSales($date_start,$date_stop);
			$count=1;
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>
					<tr>
						<td colspan='4' align='center'><span style='font-size:26px;'><u>Daily Sales Report</u></span></td>
					</tr>
					
					<tr>
						<td align='right' width='20%'>Tanant Code&nbsp;:&nbsp;</td>
						<td align='left'>0000287</td>
						<td align='right' width='15%'>Print Date&nbsp;:&nbsp;</td>
						<td align='left'>$this->doc_date</td>
					</tr>
					
					<tr>
						<td align='right' width='20%'>Tanant Name&nbsp;:&nbsp;</td>
						<td align='left'>SSUP (Combodia) CO.LTD.</td>
						<td align='right' width='15%'>&nbsp;</td>
						<td align='left'>&nbsp;</td>
					</tr>
					
					<tr>
						<td align='right' width='20%'>Branch No&nbsp;:&nbsp;</td>
						<td align='left'>$this->m_branch_id</td>
						<td align='right' width='15%'>&nbsp;</td>
						<td align='left'>&nbsp;</td>
					</tr>
					
					<tr>
						<td align='right'>Document Date&nbsp;:&nbsp;</td>
						<td align='left'>$date_start</td>
						<td align='right'></td>
						<td align='left'></td>
					</tr>
					<tr>
						<td align='right'>Employee Id&nbsp;:&nbsp;</td>
						<td align='left'>$this->user_id ($this->user_fullname)</td>
						<td align='center'></td>
						<td align='center'></td>
					</tr>
					</table>";
			echo "	<table width='100%' border='0' cellpadding='0' cellspacing='0'>
					<tr><td align='center'>
					====================================================================
					</td></tr>
					</table>
					
					<table width='85%' align='center' border='0' cellpadding='2' cellspacing='1' bgcolor='#FFF'>
					<tr bgcolor='#E5E4E2'>
							<td width='5%' align='center'>#</td><td align='center'>Daily Sales Report</td><td align='center'>Amount</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Total Sales</td><td align='right'>$total_sales_show</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Vat</td><td align='right'>$vat_show</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Net Sales</td><td align='right'>$net_sales_show</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Number of customers</td><td align='right'>$total_bill</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Sattlement Number</td><td align='right'>1</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Cash Sales</td><td align='right'>$total_cash_sales_show</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Credit/Debit Sales</td><td align='right'>$total_credit_sales_show</td>
					</tr>";
					if(sizeof($get_othercredit)>0){
						// $count=7;
						foreach($get_othercredit as  $data_sub)
						{
							echo "	<tr>
							<td align='center'>".$count++.".</td><td>".$data_sub['description']."</td><td align='right'>".number_format($data_sub['amount'], 2, '.', ',')."</td>
							</tr>";				
							// echo $data_sub['seq'];
						}				
					}			
					echo "
					<tr>
							<td align='center'>".$count++.".</td><td>Number of Credit/Debit Sales</td><td align='right'>$total_credit_bill</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Sales by other means</td><td align='right'>0.00</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Number of cash voucher sales</td><td align='right'>0.00</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Refund/Return Amount</td><td align='right'>$total_cn_sales_show</td>
					</tr>
					<tr>
							<td align='center'>".$count++.".</td><td>Number of Refund/Return</td><td align='right'>$total_cn</td>
					</tr>
					</table>
					<table width='100%' border='0' cellpadding='0' cellspacing='0'>
							<tr><td align='center'>
							====================================================================
							</td></tr>
				</table>";
		}//func
	}//class
?>