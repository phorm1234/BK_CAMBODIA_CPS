<?php 
class Model_Reportpremium{	
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
	
        
        function chkmonth($a)
	 {
						switch ($a){
								case "01" :  return  "ม.ค." ; break;
								case "02" :  return  "ก.พ." ; break;
								case "03" :  return  "มี.ค." ; break;
								case "04" :  return  "เม.ย." ; break;
								case "05" :  return  "พ.ค." ; break;
								case "06" :  return  "มิ.ย." ; break;
								case "07" :  return  "ก.ค." ; break;
								case "08" :  return  "ส.ค." ; break;
								case "09" :  return  "ก.ย." ; break;
								case "10" :  return  "ต.ค." ; break;
								case "11" :  return  "พ.ย." ; break;
								case "12" :  return  "ธ.ค." ; break;
								default : return  "00" ;
								} 
	}
        
	
		
        public function getprinter(){ // check printer
		$sql="SELECT thermal_printer FROM `com_branch_computer`";
		                
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
		}    
        
       
        
        public function getpremium($dayfrom,$tofrom,$product_start,$product_end){ 
                /*  $sql="SELECT  `doc_no` , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('AO')
                        GROUP BY doc_no
                        ";*/
		 $sql="SELECT  a.`product_id`,b.name_print, sum(a.`quantity`) as quan,b.price,sum(a.`quantity`*b.price) as total_price ,a.`flag`
                       from trn_diary2  a inner join com_product_master b on a.`product_id` = b.`product_id`
                       WHERE a.`doc_date` between '".$dayfrom."' and '".$tofrom."' and a.`product_id` 
                       between '".$product_start."' and '".$product_end."' and a.`price`='0' and a.`flag`!= 'C' and a.`doc_tp` in('SL','VT','DN') 
                       group by a.`product_id` ";
                 
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
       
}	
?>