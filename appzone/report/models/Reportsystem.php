<?php 
class Model_Reportsystem{	
	public $db1;
	public $corporation_id;
	public $company_id;
	public $branch_id;
	public $com_no;
	public $user_id;
	public $date;
	public $time;
	public $month;
	public $m_month;
	public $year_month;
	public $year;
	public $doc_no;
	public function __construct(){
            
		$this->db=Zend_Registry::get('db1');
		
		//ใช้จริง 
		$session = new Zend_Session_Namespace('empprofile');
        $empprofile=$session->empprofile; 
		$this->empprofile=$empprofile;
		$this->corporation_id=$this->empprofile['corporation_id'];
		$this->company_id=$this->empprofile['company_id'];
		//$this->vendor_id=$this->empprofile['corporation_id'];
		$this->branch_id=$this->empprofile['branch_id'];
		$this->branch_no=$this->empprofile['branch_no'];
		$this->com_no=$this->empprofile['computer_no'];
		$this->user_id=$this->empprofile['user_id'];
		
		
		//ทดสอบ
		/*$this->corporation_id='OP';
		$this->company_id='OP';
		$this->vendor_id='OP';
		$this->branch_id='OP7777';
		$this->branch_no='7338 (ESPLANADE)';
		$this->com_no='1';
		$this->user_id='004716';*/
		//
		
		$this->date=date('Y-m-d');
		$this->year_month=date('Y-m');
		$this->time=date('H:i:s');
		$this->month=date('n');
		$this->m_month=date('m');
		$this->year=date('Y');
                $this->printtime =date('d-m-Y');
                
	}
	
public function view_docdate(){ // 
            $sql="SELECT doc_date FROM com_doc_date";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}	
   
public function view_docno(){ // 
            $sql=	"SELECT t.`branch_id`,t.`doc_tp` , max(t.`doc_no`) , d.`doc_no` 
            		FROM `trn_diary1` t inner join com_doc_no d on t.`doc_tp` = d.`doc_tp`
            		group by t.`doc_tp`";   
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
	
public function view_timebranch(){ // 
            $sql=	"SELECT * FROM com_time_branch ";   
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
	
public function update_time($n_open,$n_close,$s_open,$s_close){ // 
            $sql=	"UPDATE com_time_branch 
            		SET normal_open_time='".$n_open."', normal_close_time='".$n_close."' ,special_open_time='".$s_open."', special_close_time='".$s_close."' ";   
			 $result = $this->db->exec($sql);
            // $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
	
	
}	
?>