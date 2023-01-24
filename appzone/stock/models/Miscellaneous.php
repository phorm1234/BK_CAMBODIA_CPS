<?php 
class Model_Miscellaneous{	
	public $db;
	public $corporation_id;
	public $company_id;
	public $branch_id;
	public $com_no;
	public $user_id;
	public $date;
	public $time;
	public $year_month;
	public $month;
	public $m_month;
	public $year;
	public $amount;
	public $amount_item;
	public $doc_no;
	public $ref_doc_no;
	public $doc_tp;
	public $get_doc_no;
	public $checkstock;
	public $getstock;
	public $branch_no;
	public $vendor_id;
	
	public function __construct(){
		$this->db=Zend_Registry::get('db1');
		$this->dbji=Zend_Registry::get('dbji');
		$session = new Zend_Session_Namespace('empprofile');
        $empprofile=$session->empprofile; 
		$this->empprofile=$empprofile;
		$this->corporation_id=$this->empprofile['corporation_id'];
		$this->company_id=$this->empprofile['company_id'];
		$this->branch_id=$this->empprofile['branch_id'];
		$this->branch_no=$this->empprofile['branch_no'];
		$this->com_no=$this->empprofile['computer_no'];
		$this->user_id=$this->empprofile['user_id'];
		$this->date=date('Y-m-d');
		$this->year_month=date('Y-m');
		$this->time=date('H:i:s');
		$this->month=date('n');
		$this->m_month=date('m');
		$this->year=date('Y');
	}
	
	public function editcontactbillvt($arr){
		$sql="
		UPDATE 
			trn_diary1 
		SET 
			name='$arr[fullname]', 
			address1='$arr[addr1]', 
			address2='$arr[addr2]', 
			address3='$arr[addr3]'  
		WHERE 
			doc_no='$arr[edit_doc_no]' 
		";
		$rs=$this->db->query($sql);
		if($rs){
			$status="y";
		}else{
			$status="n";
		}
		return $status;
	}
	
	public function getcodetype($code_type){
		$sql="
		SELECT 
			code_type,value_type,default_status 
		FROM 
			com_pos_config 
		WHERE 
			code_type='$code_type' 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function editconfigprinter($code_type,$default_status){
		$sql="
		UPDATE  
			com_pos_config 
		SET  
			default_status='$default_status' 
		WHERE 
			code_type='$code_type'
		";
		$rs=$this->db->query($sql);
		if($rs){
			$sql_up="update com_branch_computer set thermal_printer='$default_status' where branch_id='$this->branch_id' ";
			$rs_up=$this->db->query($sql_up);
			if($rs){
				$status="y";
			}else{
				$status="n";
			}
		}else{
			$status="n";
		}
		return $status;
	}
	
	public function getdatagp($sort,$barcode){
		$sql="
		SELECT 
			id,barcode,description ,gp ,start_date,end_date 
		FROM 
			`com_gp_corner` 
		WHERE 
			barcode LIKE '$barcode%' 
		ORDER BY 
			$sort 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function saveformgp($arr){
		if($arr['status_edit']=="y"){
			$sql="
			UPDATE 
				com_gp_corner 
			SET 
				corporation_id='$this->corporation_id', 
				company_id='$this->company_id',
				description='$arr[desc]', 
				gp='$arr[gp]', 
				start_date='$arr[start_date]', 
				end_date='$arr[end_date]', 
				upd_date=NOW(), 
				upd_time=NOW(), 
				upd_user='$this->user_id' 
			WHERE 
				barcode='$arr[edit_barcode]' 
			";
			$stmt=$this->db->query($sql);
			if($stmt){
				$status="y";
			}else{
				$status="n";
			}
		}else{
			$sql_count="SELECT barcode FROM com_gp_corner WHERE barcode='$arr[barcode]'";
			$rs_count=$this->db->query($sql_count);
			$count=$rs_count->fetchAll();
			$count=count($count);
			if($count>0){
				$status="w";
			}else{
				$sql="
				INSERT INTO 
					com_gp_corner 
				SET 
					corporation_id='$this->corporation_id', 
					company_id='$this->company_id', 
					barcode='$arr[barcode]', 
					description='$arr[desc]', 
					gp='$arr[gp]', 
					start_date='$arr[start_date]', 
					end_date='$arr[end_date]', 
					reg_date=NOW(), 
					reg_time=NOW(), 
					reg_user='$this->user_id'
				";
				$stmt=$this->db->query($sql);
				if($stmt){
					$status="y";
				}else{
					$status="n";
				}
			}
		}
		return $status;
	}
	
	public function deletegp($barcode){
		$sql="DELETE FROM com_gp_corner WHERE barcode='$barcode'";
		$rs=$this->db->query($sql);
		if($rs){
			$status="y";
		}else{
			$status="n";
		}
		echo $status;
	}
}
?>