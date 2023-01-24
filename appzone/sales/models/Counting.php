<?php 
class Model_Counting extends Model_PosGlobal{
		/**
		 * 
		 * @desc :global variable
		 * public $this->db ;connection
		 * public $this->m_corporation_id ;OP
		 * public $this->m_company_id   ;OP
		 * public $this->m_branch_id       ;ex 7777
		 * public $this->m_computer_no   ;computer no.
		 * public $this->m_com_ip;
		 * pubic $this->doc_date;
		 * public $this->m_thermal_printer ;Y,N
		 */
		function __construct(){
			parent::__construct();	
			$this->date = date('Y-m-d');
		}//func		
		
		
		function checkemp($eid=""){
			$arr = array();
			$sql = "select 
						employee_id,
						position 
					from conf_employee 
					where 
						employee_id = '$eid'";
			$res  = $this->db->FetchAll($sql);
			if(count($res) > 0){
				$status = "y";
				$eid 	= $eid;
				$position = $res[0]['position'];
				$arr = array("status"=>$status,"eid"=>$eid,"position"=>$position);
			}else{
				$status = "n";
				$arr = array("status"=>$status);
			}
			return $arr;
		}
		
		function checkdocstatus(){
			$arr = array();
			$sql = "select * from com_cash_control where doc_date = '$this->date'";
			$res = $this->db->FetchAll($sql);
			$num = count($res);
			if($num < 2){
				if($num == 0){   // open
					$number = "1";
				}else{			//close
					$tmp = $this->checkstatusclose();
					if($tmp == 1){ //ปิดการขายแล้ว
						$number = "2";
					}else{
						$arr =array("status"=>'err1');  // ยังไม่ปิดการขาย
						return $arr;
					}		
				}
				$arr = array(
							"status"=>'y',
							"number"=>$number,
							"pay"=>$this->paypos(),
							"chg_cash"=>$this->get_chg_petty("change"),
							"pett_cash"=>$this->get_chg_petty("petty")
							);
			}else{
				$arr =array("status"=>'err2');  // ไม่สามารถทำรายการได้ 
			}
			return $arr;
		}
		
		function checkstatusclose(){
			$docdate = "select * from com_doc_date where doc_date > '$this->date'";
			$resdoc = $this->db->FetchAll($docdate);
			if(count($resdoc) > 0){
				return 1; // ปิดการขายแล้ว
			}else{
				return 0; // ยังไม่ปิดการขาย
			}
		}
		
		function paypos(){
			$sqldocdate = "SELECT date_add( doc_date, INTERVAL -1 DAY ) as doc_date FROM `com_doc_date` WHERE 1";
			$resdate = $this->db->FetchRow($sqldocdate);
			$sqlstart = "SELECT sum(net_amt) as starts 
						FROM trn_diary1 
						WHERE flag=''  
						and (doc_tp = 'SL' or doc_tp = 'VT') and paid ='0000' and doc_date='{$resdate['doc_date']}'";
			$restop	 = $this->db->FetchRow($sqlstart);
			
			$sqlend  = "SELECT sum(net_amt) as ends
						FROM trn_diary1 
						WHERE flag=''  
						and  doc_tp = 'CN' and status_no='40' and doc_date='{$resdate['doc_date']}' ";
			$resend = $this->db->FetchRow($sqlend);
			return $restop['starts']-$resend['ends'];
		}
		
		function get_chg_petty($id){
			//$this->m_branch_id
			$sql = "select * from com_cash_branch where branch_id='$this->m_branch_id'";
			$res = $this->db->FetchRow($sql);
			if($id =="change"){
				return $res['change_cash'];
			}else{
				return $res['petty_cash'];
			}
		}
		
				
		function savedata($data){
			$res = $this->db->insert('com_cash_control',$data);
			if($res)
				return 'y';
			else{
				return 'n';
			}
		}	

		function checkprinting($date,$type){
			if($date != "" && $type != ""){
				
				$sql = "select * from com_cash_control where doc_date='$date' and status='$type'";
				$res = $this->db->FetchAll($sql);
				if(count($res) > 0){
					return 'y';
				}else{
					return 'n';
				}
			}
		}
		
		function printing($date,$type){
			if($date != "" && $type != ""){
				$sql = "select * from com_cash_control where doc_date='$date' and status='$type'";
				$res = $this->db->FetchAll($sql);
				return $res;
			}
		}
				
}//class		
?>