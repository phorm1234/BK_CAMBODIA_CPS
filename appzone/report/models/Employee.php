<?php 
	class Model_Employee{
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
		
		function getEmployee($employee_id){
			/**
			 * @desc WR06062014
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_emp="SELECT* FROM conf_employee WHERE employee_id='$employee_id'";
			$arr_emp=$this->db->fetchAll($sql_emp);
			return $arr_emp;
		}//func
		
		
		
		function getSummaryBySale($sale_id,$date_start,$date_stop){
			/**
			 * @desc 
			 * * 23092014
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
			$sale_id=trim($sale_id);
			$arr_sale=array();
			if($sale_id!=''){
				$sql_data="SELECT 
								COUNT(*) as n_bill,SUM(amount) AS sum_amount,SUM(net_amt) AS sum_net 
							FROM 
								trn_diary1 
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								branch_id='$this->m_branch_id' AND
								doc_tp IN('SL','VT') AND
								saleman_id='$sale_id' AND
								doc_date BETWEEN '$date_start'  AND '$date_stop'";
					$row_chk=$this->db->fetchAll($sql_data);
					if(!empty($row_chk)){
						$sql_sale="SELECT name,surname FROM conf_employee WHERE employee_id='$sale_id'";
						$row_sale=$this->db->fetchAll($sql_sale);
						$fullname="";
						if(!empty($row_sale)){
							$fullname=$row_sale[0]['name']." ".$row_sale[0]['surname'];
						}
						$arr_sale[$sale_id]['fullname']=$fullname;
						$arr_sale[$sale_id]['n_bill']=$row_chk[0]['n_bill'];
						$arr_sale[$sale_id]['sum_amount']=$row_chk[0]['sum_amount'];
						$arr_sale[$sale_id]['sum_net']=$row_chk[0]['sum_net'];
					}
			}else{
				
					$sql_data="SELECT 
										saleman_id,COUNT(*) as n_bill,SUM(amount) AS sum_amount,SUM(net_amt) AS sum_net 
									FROM 
										trn_diary1 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND								
										doc_tp IN('SL','VT') AND
										doc_date BETWEEN '$date_start'  AND '$date_stop'
								GROUP BY saleman_id";
					$row_chk=$this->db->fetchAll($sql_data);
					if(!empty($row_chk)){
						foreach($row_chk as $data){
							$sale_id=$data['saleman_id'];
							$sql_sale="SELECT name,surname FROM conf_employee 
													WHERE employee_id='$sale_id'";
							$row_sale=$this->db->fetchAll($sql_sale);
							$fullname="";
							if(!empty($row_sale)){
								$fullname=$row_sale[0]['name']." ".$row_sale[0]['surname'];
							}
							
							$arr_sale[$sale_id]['fullname']=$fullname;
							$arr_sale[$sale_id]['n_bill']=$data['n_bill'];
							$arr_sale[$sale_id]['sum_amount']=$data['sum_amount'];
							$arr_sale[$sale_id]['sum_net']=$data['sum_net'];
							
						}
						
					}
					
				
			}
			return $arr_sale;
		}//func
		
		function SaleByEmpPreviews($sale_id='',$date_start,$date_stop){
			/**
			 * 
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
			$sql_branch="SELECT* FROM com_branch_detail WHERE branch_id='$this->m_branch_id'";		
			$rec_branch=$this->db->fetchAll($sql_branch);
			$branch_name=$rec_branch[0]['branch_name'];
			$arr_cdate=explode('-',date("Y-m-d"));
			$cdate=$arr_cdate[2]."/".$arr_cdate[1]."/".$arr_cdate[0];
			$arr_sdate=explode('-',$date_start);
			$arr_edate=explode('-',$date_stop);
			$sdate_show=$arr_sdate[2]."/".$arr_sdate[1]."/".$arr_sdate[0];
			$edate_show=$arr_edate[2]."/".$arr_edate[1]."/".$arr_edate[0];
			
			$arr_sale=$this->getSummaryBySale($sale_id,$date_start,$date_stop);
			
			
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>
					<tr>
						<td colspan='4' align='center'>&nbsp;</td>
					</tr>
					<tr>
						<td colspan='4' align='center'><span style='font-size:26px;'><u>Employee Sales Summary Report</u></span></td>
					</tr>
					<tr>
						<td colspan='4' align='center'>&nbsp;</td>
					</tr>
					<tr>
						<td align='right' width='20%'>Branch&nbsp;:&nbsp;</td>
						<td align='left'>$this->m_branch_id &nbsp;$branch_name</td>
						<td align='right' width='15%'>Print Date&nbsp;:&nbsp;</td>
						<td align='left'>$cdate</td>
					</tr>
					<tr>
						<td align='right'>Date&nbsp;:&nbsp;</td>
						<td align='left'>$sdate_show - $edate_show</td>
						<td align='right'></td>
						<td align='left'></td>
					</tr>
					</table>
					
					<table width='100%' border='0' cellpadding='0' cellspacing='0'>";
				echo "
						<tr>
							<td align='center' colspan='5'>==========================================================================</td>
						</tr>";
				echo "<tr>							
							<td align='center' width='20'>Employee Id</td>
							<td align='center' width='25'>Fullname</td>
							<td align='center' width='15'>Amount of bill</td>
							<td align='center' width='20'>Gross</td>
							<td align='center' width='20'>Net</td>							
						</tr>";
			
				echo "
						<tr>
							<td align='center' colspan='5'>==========================================================================</td>
						</tr>";
				
				foreach($arr_sale as $key=>$data){
					
					$n_bill=$data['n_bill'];
					number_format($data['sum_amount'],'2','.',',').
					$sum_amount=number_format($data['sum_amount'],'2','.',',');
					$sum_net=number_format($data['sum_net'],'2','.',',');
					$fullname=$data['fullname'];
					echo "
						<tr>
							<td align='center' width='20%'>&nbsp;$key</td>
							<td align='center' width='25%'>&nbsp;$fullname</td>
							<td align='center' width='15%'>&nbsp;$n_bill</td>
							<td align='center' width='20%'>$sum_amount</td>
							<td align='center' width='20%'>$sum_net</td>
						</tr>";
					
				}
				
					
				echo "
						<tr>
							<td align='center' colspan='5'>==========================================================================</td>
						</tr>
				</table>
			";
		}
	}//class
?>