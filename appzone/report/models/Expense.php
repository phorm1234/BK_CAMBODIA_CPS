<?php 
	class Model_Expense{
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
		
		function getSumExpense($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_expense_data");			
			$sql_data="SELECT sum( b.amount ) AS sum_amount
							FROM `com_expense_data` AS a
							INNER JOIN com_expense_list AS b ON ( a.account_code = b.account_code )
							WHERE 
								b.corporation_id='$this->m_corporation_id' AND
								b.company_id='$this->m_company_id' AND
								b.branch_id='$this->m_branch_id' AND
								b.branch_no='$this->m_branch_no' AND
								b.doc_dt BETWEEN '$date_start'  AND '$date_stop'";
			$sum_amount=$this->db->fetchCol($sql_data);
			return $sum_amount[0];
		}//func
		
		function getExpense($date_start,$date_stop){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_expense_data");			
			$sql_data="SELECT a.account_code, a.description, sum( b.amount ) AS sum_amount
							FROM `com_expense_data` AS a
							INNER JOIN com_expense_list AS b ON ( a.account_code = b.account_code )
							WHERE 
								b.corporation_id='$this->m_corporation_id' AND
								b.company_id='$this->m_company_id' AND
								b.branch_id='$this->m_branch_id' AND
								b.branch_no='$this->m_branch_no' AND
								b.doc_dt BETWEEN '$date_start'  AND '$date_stop'
							GROUP BY a.account_code";
			$rows_data=$this->db->fetchAll($sql_data);
			return $rows_data;
		}//func
		function expensePreviews($date_start,$date_stop){
			/**
			 * 
			 * @var unknown_type
			 */
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>
					<tr>
						<td colspan='4' align='center'><span style='font-size:26px;'>Claim Expense Doc.</span></td>
					</tr>
					<tr>
						<td align='right' width='20%'>Branch&nbsp;:&nbsp;</td>
						<td align='left'>$this->m_branch_id</td>
						<td align='right' width='15%'>Print Date&nbsp;:&nbsp;</td>
						<td align='left'>$this->doc_date</td>
					</tr>
					<tr>
						<td align='right'>Start Date&nbsp;:&nbsp;</td>
						<td align='left'>$date_start - $date_stop</td>
						<td align='right'></td>
						<td align='left'></td>
					</tr>
					<tr>
						<td align='right'>Employee ID&nbsp;:&nbsp;</td>
						<td align='left'>$this->user_id ($this->user_fullname)</td>
						<td align='center'></td>
						<td align='center'></td>
					</tr>
					</table>
					=======================================================================
					<table width='100%' border='0' cellpadding='0' cellspacing='0'>
					";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_expense_data");			
			$sql_data="SELECT a.account_code, a.description, sum( b.amount ) AS sum_amount
							FROM `com_expense_data` AS a
							INNER JOIN com_expense_list AS b ON ( a.account_code = b.account_code )
							WHERE 
								b.corporation_id='$this->m_corporation_id' AND
								b.company_id='$this->m_company_id' AND
								b.branch_id='$this->m_branch_id' AND
								b.branch_no='$this->m_branch_no' AND
								b.doc_dt BETWEEN '$date_start'  AND '$date_stop'
							GROUP BY a.account_code";
			$rows_data=$this->db->fetchAll($sql_data);
			foreach($rows_data as $dataL){
				echo "<tr>
						<td align='center' width='15%'>&nbsp;</td>
						<td align='center' width='17%'>".$dataL['account_code']."</td>
						<td align='left' width=''>".$dataL['description']."</td>
						<td align='center' width='25%'>".$dataL['sum_amount']."</td>
						<td align='center' width='10%'>&nbsp;</td>
					</tr>";
			}
			$sum_amount=$this->getSumExpense($date_start,$date_stop);
			echo "</table>
			=======================================================================
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td align='center' width='15%'>&nbsp;</td>
				<td align='center' width='17%'>Total Amount</td>
				<td align='left' width=''>".number_format($sum_amount,'2','.',',')."&nbsp;Baht</td>
				<td align='left' width='25%'></td>
				<td align='center' width='10%'>&nbsp;</td>
			</tr>
			</table>
			<br><br><br>
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td align='center' width='10%'>Received by;</td>
				<td align='center' width='17%'>.......................................</td>
				<td align='left' width='10%'>Examiners/Auditors</td>
				<td align='left' width='15%'>.......................................</td>
				<td align='center' width='10%'>Approvers</td>
				<td align='left' width='15%'>.......................................</td>
			</tr>
			</table>
			";
		}
	}//class
?>