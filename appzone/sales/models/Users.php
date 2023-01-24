<?php 	
	/*
	*@ name Users
	*@ desc for manage user systerm
	*/
	//require_once('DB.php');
	class Model_Users extends Zend_Db_Table{	
		//init db
		protected $dbMaster;
		protected $dbSlave;
		//system variables
		public $m_corporation_id;
		public $m_company_id;
		public $m_company_name;
		
		public $m_branch_id;
		public $m_branch_no;
		public $m_branch_tp;
		public $m_acc_no;
		public $m_pos_id;
		public $m_tel;
		
		public $m_thermal_printer;
		public $m_network;
		public $m_computer_no;
		public $m_check_date;
				
		//public $db=null;
		public $msg_error="";		
		public $arr_user=array();
		public $today;
		
		public function __construct(){
			$this->dbMaster = Zend_Registry::get('db1');
			$this->dbSlave = Zend_Registry::get('db2');			
			$this->today=date("Y-m-d");
		}//	
					
		public function chkUserExist($user,$password){			
			$this->user_id=trim($user);
			$this->password_id=trim($password);
			if($this->user_id=="" || $this->password_id=="") return false;	
			try {			
				$stmt_chk=$this->dbMaster->select()
										->from('login',array('employee_id','user_id','password_id'))
 										->where('user_id = ?', $user)
             							->where('password_id = ?', $password);
				$row_data=$stmt_chk->query()->fetchAll();				
				$c_row=count($row_data);
				if($c_row>0){
					$data = $row_data[0]['employee_id'];
				}else{
					$data="null";
				}
				return $data;			    
			} catch (Zend_Db_Exception  $e) {
				echo $this->msg_error=$e;
			}
		}//func
		
		public function setCompany(){			
			$stmt_company=$this->dbMaster->select()
										->from('company',array('company_id','company_name','corporation_id'))
 										->where("active='Y'");
			$company=$stmt_company->query()->fetchAll();		
			$c_row=count($company);
			if($c_row > 0) {
				$this->m_company_id=$company[0]['company_id'];
				$this->m_company_name=$company[0]['company_name'];
				$this->m_corporation_id=$company[0]['corporation_id'];
			}
		}//func
		
		public function getCompany(){
			$arr_company=array();		
			$arr_company['m_corporation_id']=$this->m_corporation_id;
			$arr_company['m_company_id']=$this->m_company_id;
			$arr_company['m_company_name']=$this->m_company_name;
			return $arr_company;
		}//func
		
		public function setBranch($m_corporation_id,$m_company_id){
			if($m_corporation_id!=""){
				$this->m_corporation_id=$m_corporation_id;
			}else{
				return "null";
			}
			
			if($m_company_id!=""){
				$this->m_company_id=$m_company_id;
			}else{
				return "null";
			}
			
			$sql_branch='SELECT * FROM branch 
							WHERE corporation_id="'.$this->m_corporation_id .'" 
									AND company_id="'.$this->m_company_id.'"
									AND CURDATE( ) BETWEEN start_date AND end_date';
			
			$stmt=$this->dbMaster->query($sql_branch);
			if($stmt->rowCount()>0){
				$branch=$stmt->fetchAll(PDO::FETCH_ASSOC);
				$this->m_branch_id=$branch[0]['branch_id'];
				$this->m_branch_no=$branch[0]['branch_no'];
				$this->m_branch_tp=$branch[0]['branch_tp'];
				$this->m_acc_no=$branch[0]['acc_no'];
				$this->m_pos_id=$branch[0]['pos_id'];
				$this->m_tel=$branch[0]['tel'];	
			}
		}//func
		
		function getBranch(){			
			$arr_branch=array();
			$arr_branch['m_branch_id']=$this->m_branch_id;	
			$arr_branch['m_branch_no']=$this->m_branch_no;
			$arr_branch['m_branch_tp']=$this->m_branch_tp;
			$arr_branch['m_acc_no']=$this->m_acc_no;
			$arr_branch['m_pos_id']=$this->m_pos_id;
			$arr_branch['m_tel']=$this->m_tel;
			return $arr_branch;	
		}//func		
		
		public function getSystem(){	
			$stmt_system=$this->dbMaster->select()
										->from('system',array('thermal_printer','network','computer_no','check_date'));
			$system=$stmt_system->query()->fetchAll();	
			$crow=count($system);
			if($crow>0){
				$this->m_thermal_printer=$system[0]['thermal_printer'];
				$this->m_network=$system[0]['network'];
				$this->m_computer_no=$system[0]['computer_no'];
				$this->m_check_date=$system[0]['check_date'];
				$arr_sys=array();
				if($this->m_thermal_printer=='Y'){
					$arr_sys['m_thermal_printer']="YES";
				}else{
					$arr_sys['m_thermal_printer']="NO";
				}
				
				if($this->m_network=='Y'){
					$arr_sys['m_network']="YES";
				}else{
					$arr_sys['m_network']="NO";
				}
				$arr_sys['m_computer_no']=$this->m_computer_no;
				$arr_sys['m_check_date']=$this->m_check_date;
				return $arr_sys;
			}			
		}//func
					
		function getExpireDate(){			
			try{						
				$sql_expr="SELECT end_date FROM login WHERE user_id:user_id";
				$stmt=$this->dbMaster->prepaire($sql_expr);
				$stmt->bindParam(':user_id',$this->user_id,PDO::PARAM_STR);	
				$stmt->execute();
				$row=$stmt->fetchAll(PDO::FETCH_ASSOC);
				if($row!=""){
					$expr_date=$row[0]['end_date'];
				}
			} catch(Zend_Db_Exception $e){
				$this->msg_error=$e;
			}
		}//func
		
		function __destruct() {
	       //testing *WR04052011
	       //$this->dbMaster->closeConnection();
	       //$this->dbSlave->closeConnection();
	   	}//func		
	   	
	   	function getIniTime(){
	   		$sql_time="SELECT * FROM control";
	   		$stmt_time=$this->dbMaster->query($sql_time);
	   		$row_time=$stmt_time->fetchAll(PDO::FETCH_ASSOC);
	   		if($row_time[0]['check_date']!='Y'){
	   			$check_start=$row_time[0]['check_start'];
	   			$check_end=$row_time[0]['check_end'];
	   			$sql_date="SELECT doc_date FROM doc_date WHERE doc_date BETWEEN '$check_start' AND '$check_end'";
	   			$stmt_date=$this->dbMaster->query($sql_date);
	   			if($stmt_date->rowCount()>0){
	   				$row_date=$stmt_date->fetchAll(PDO::FETCH_ASSOC);
	   				return $row_date[0]['doc_date'];
	   			}else{
	   				return 'Y';
	   			}
	   		}else{
	   			return 'Y';
	   		}
	   	}//func
	   	
	   
		
	}//class
?>