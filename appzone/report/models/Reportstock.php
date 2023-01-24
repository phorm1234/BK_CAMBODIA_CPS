<?php 
class Model_Reportstock{	
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
								case "01" :  return  "Jan." ; break;
								case "02" :  return  "Feb." ; break;
								case "03" :  return  "Mar." ; break;
								case "04" :  return  "Apr." ; break;
								case "05" :  return  "May." ; break;
								case "06" :  return  "Jun." ; break;
								case "07" :  return  "Jul." ; break;
								case "08" :  return  "Aug." ; break;
								case "09" :  return  "Sept." ; break;
								case "10" :  return  "Oct." ; break;
								case "11" :  return  "Nov." ; break;
								case "12" :  return  "Dec." ; break;
								default : return  "00" ;
								} 
	}
        
	
		public function getprinter(){ // check printer
		$sql="SELECT thermal_printer FROM `com_branch_computer`";
		                
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
		}       
        
        
        public function get_begin($product_start,$product_end,$month,$year){ // 
		$sql="SELECT `product_id`, `month`, `year`, `begin` FROM `com_stock_master` WHERE `month` like ".$month." and `year` = ".$year." and `product_id` between ".$product_start."  and ".$product_end." order by product_id ASC";
		                
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        
        public function get_transection($product_start,$product_end,$start_date,$end_date){ // 
		$sql="SELECT `doc_tp`,`product_id`,`stock_st`,sum(`quantity` * `stock_st`) FROM `trn_diary2` WHERE `doc_date` between '".$start_date."'  and '".$end_date."' and `product_id` between ".$product_start."  and ".$product_end." and `flag` != 'C' GROUP BY doc_tp,product_id,`stock_st`";
		                
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
     
        
        public function get_begin2($product_start,$product_end,$start_date,$end_date){ // 
		$sql="SELECT `product_id` , sum( `quantity` * `stock_st` ) as begin
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$start_date."'
                        AND '".$end_date."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp in ('SL','VT')
                        GROUP BY product_id";
		                
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
     
        
        
      /*  public function get_transection2($product_start,$product_end,$start_date,$end_date,$month,$year){ // 
		$sql="SELECT product_id, sum(
BEGIN ) as begin , sum( sl ) as sl , sum( ti ) as ti ,sum( dn ) as dn ,sum( ai ) as ai ,sum(ao) as ao ,sum(cn) as cn,sum(tto) as tto
FROM (
(

SELECT `product_id` , `month` , `year` , `begin` , 0 AS sl, 0 AS ti , 0 AS dn ,0 AS ai , 0 AS ao ,0 AS cn,0 AS tto
FROM `com_stock_master`
WHERE `month` = '".$month."'
AND `year` = '".$year."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."')
UNION ALL (

SELECT `product_id` , 0, 0, 0, sum( `quantity` * `stock_st` ) , 0 ,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp in ('SL','VT')
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 0, 0, 0, sum( `quantity` * `stock_st` ) ,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 0, 0, 0, 0, sum( `quantity` * `stock_st` ) ,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'DN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 0, 0, 0, 0 ,0 , sum( `quantity` * `stock_st` ) ,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 0, 0, 0, 0, 0 , 0 ,sum( `quantity` * `stock_st` ) ,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AO'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 0, 0, 0,0,0,0,0, sum( `quantity` * `stock_st` ),0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'CN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 0, 0, 0,0,0,0,0,0, sum( `quantity` * `stock_st` )
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TO'
GROUP BY product_id
)
)a
GROUP BY product_id
";
		                
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                 return $result ;
	}*/
        
        
public function get_transection3($product_start,$product_end,$start_date,$end_date,$month,$year){ //  begin เริ่มต้นวันที่ 01
$sql="select sub1.*,com_product_master.price ,com_product_master.name_product from 
(
SELECT product_id, 
sum(BEGIN ) as begin , sum( sl ) as sl , sum( ti ) as ti ,sum( dn ) as dn ,sum( ai ) as ai ,sum(ao) as ao ,sum(cn) as cn,sum(tto) as tto,
sum(amt_sl) as amt_sl,sum(amt_ti) as amt_ti,sum(amt_dn) as amt_dn,sum(amt_ai) as amt_ai,sum(amt_ao) as amt_ao,sum(amt_cn) as amt_cn,sum(amt_tto) as amt_tto
FROM (
(

SELECT `product_id` , `begin` ,
 0 AS sl, 0 AS ti , 0 AS dn ,0 AS ai , 0 AS ao ,0 AS cn,0 AS tto,
 0 AS amt_sl,0 AS amt_ti,0 AS amt_dn,0 AS amt_ai,0 AS amt_ao,0 AS amt_cn,0 AS amt_tto
FROM `com_stock_master`
WHERE `month` = '".$month."'
AND `year` = '".$year."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."')
UNION ALL (

SELECT `product_id` , 0,
sum( `quantity` * `stock_st` ) , 0 ,0,0,0,0,0,
sum(`amount` * `stock_st`),0,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp in ('SL','VT') 
AND status_no != '18'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` ,  0,
0, sum( `quantity` * `stock_st` ) ,0,0,0,0,0,
0, sum( `amount` * `stock_st` ) ,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id`, 0,
0, 0, sum( `quantity` * `stock_st` ) ,0,0,0,0,
0, 0, sum( `amount` * `stock_st` ) ,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'DN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id`, 0,
0, 0 ,0 , sum( `quantity` * `stock_st` ) ,0,0,0,
0, 0 ,0 , sum( `amount` * `stock_st` ) ,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0,
0, 0, 0 , 0 ,sum( `quantity` * `stock_st` ) ,0,0,
0, 0, 0 , 0 ,sum( `amount` * `stock_st` ) ,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AO'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id`,  0,
0,0,0,0,0, sum( `quantity` * `stock_st` ),0,
0,0,0,0,0, sum( `amount` * `stock_st` ),0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'CN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 
0,0,0,0,0,0, sum( `quantity` * `stock_st` ),
0,0,0,0,0,0, sum( `amount` * `stock_st` )
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$start_date."'
AND '".$end_date."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TO'
GROUP BY product_id
)
)a
GROUP BY product_id
)as sub1 inner join com_product_master on sub1.product_id = com_product_master.product_id 

";
		                
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                 return $result ;
	}
        
        
        public function get_transection4($product_start,$product_end,$newdayfrom,$newdayend,$month,$year,$dayfrom,$tofrom){ // begin วันอื่นๆ
$sql="select sub1.*,sub2.*,com_product_master.price,com_product_master.name_product  from
(
SELECT product_id,
sum(BEGIN ) as begin , sum( sl ) as sl , sum( ti ) as ti ,sum( dn ) as dn ,sum( ai ) as ai ,sum(ao) as ao ,sum(cn) as cn,sum(tto) as tto,
sum(amt_sl) as amt_sl,sum(amt_ti) as amt_ti,sum(amt_dn) as amt_dn,sum(amt_ai) as amt_ai,sum(amt_ao) as amt_ao,sum(amt_cn) as amt_cn,sum(amt_tto) as amt_tto
FROM (
(

SELECT `product_id` ,`begin` ,
 0 AS sl, 0 AS ti , 0 AS dn ,0 AS ai , 0 AS ao ,0 AS cn,0 AS tto,
 0 AS amt_sl,0 AS amt_ti,0 AS amt_dn,0 AS amt_ai,0 AS amt_ao,0 AS amt_cn,0 AS amt_tto
FROM `com_stock_master`
WHERE `month` = '".$month."'
AND `year` = '".$year."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."')
UNION ALL (

SELECT `product_id` , 0, 
sum( `quantity` * `stock_st` ) , 0 ,0,0,0,0,0,
sum(`amount` * `stock_st`),0,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp in ('SL','VT')
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0,
0, sum( `quantity` * `stock_st` ) ,0,0,0,0,0,
0, sum( `amount` * `stock_st` ) ,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0,  
0, 0, sum( `quantity` * `stock_st` ) ,0,0,0,0,
0, 0, sum( `amount` * `stock_st` ) ,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'DN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0,
0, 0 ,0 , sum( `quantity` * `stock_st` ) ,0,0,0,
0, 0 ,0 , sum( `amount` * `stock_st` ) ,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0, 
0, 0, 0 , 0 ,sum( `quantity` * `stock_st` ) ,0,0,
0, 0, 0 , 0 ,sum( `amount` * `stock_st` ) ,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AO'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0,
0,0,0,0,0, sum( `quantity` * `stock_st` ),0,
0,0,0,0,0, sum( `amount` * `stock_st` ),0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'CN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 0,
0,0,0,0,0,0, sum( `quantity` * `stock_st` ),
0,0,0,0,0,0, sum( `amount` * `stock_st` )
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$newdayfrom."'
AND '".$newdayend."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TO'
GROUP BY product_id
)
)a
GROUP BY product_id
) as sub1 inner join (

SELECT product_id, sum( sl2 ) as sl2 , sum( ti2 ) as ti2 ,sum( dn2 ) as dn2 ,sum( ai2 ) as ai2 ,sum(ao2) as ao2 ,sum(cn2) as cn2,sum(tto2) as tto2,sum(amt_sl2) as amt_sl2,sum(amt_ti2) as amt_ti2,sum(amt_dn2) as amt_dn2,sum(amt_ai2) as amt_ai2,sum(amt_ao2) as amt_ao2,sum(amt_cn2) as amt_cn2,sum(amt_tto2) as amt_tto2
FROM (
(

SELECT `product_id` , 0 AS sl2, 0 AS ti2 , 0 AS dn2 ,0 AS ai2 , 0 AS ao2 ,0 AS cn2,0 AS tto2,0 AS amt_sl2,0 AS amt_ti2,0 AS amt_dn2,0 AS amt_ai2,0 AS amt_ao2,0 AS amt_cn2,0 AS amt_tto2
FROM `com_stock_master`
WHERE `month` = '".$month."'
AND `year` = '".$year."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."')
UNION ALL (

SELECT `product_id`, 
sum( `quantity` * `stock_st` ) , 0 ,0,0,0,0,0,
sum( `amount` * `stock_st` ) , 0 ,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp in ('SL','VT')
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 
0, sum( `quantity` * `stock_st` ) ,0,0,0,0,0,
0, sum( `amount` * `stock_st` ) ,0,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 
0, 0, sum( `quantity` * `stock_st` ) ,0,0,0,0,
0, 0, sum( `amount` * `stock_st` ) ,0,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'DN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 
0, 0, 0, sum( `quantity` * `stock_st` ) ,0,0,0,
0, 0, 0, sum( `amount` * `stock_st` ) ,0,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AI'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` , 
0, 0, 0, 0 ,sum( `quantity` * `stock_st` ) ,0,0,
0, 0, 0, 0 ,sum( `amount` * `stock_st` ) ,0,0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'AO'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id` ,
0,0, 0,0,0, sum( `quantity` * `stock_st` ),0,
0,0, 0,0,0, sum( `amount` * `stock_st` ),0
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'CN'
GROUP BY product_id
)
UNION ALL (

SELECT `product_id`,
0 , 0, 0,0,0,0, sum( `quantity` * `stock_st` ),
0 , 0, 0,0,0,0, sum( `amount` * `stock_st` )
FROM `trn_diary2`
WHERE `doc_date`
BETWEEN '".$dayfrom."'
AND '".$tofrom."'
AND `product_id`
BETWEEN '".$product_start."'
AND '".$product_end."'
AND `flag` != 'C'
AND doc_tp = 'TO'
GROUP BY product_id
)
)a
GROUP BY product_id

) as sub2
on sub1.product_id=sub2.product_id
inner join com_product_master on sub1.product_id = com_product_master.product_id 

";
		                
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                 return $result ;
	}
              
              
 public function getbill_sl($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT min(`doc_no`) ,max(`doc_no`),sum(quantity) as quantity,sum(amount) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp ='SL'
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
					//echo $sql;
                  return $result ;
	}
	
	public function getbill_vt($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT min(`doc_no`) ,max(`doc_no`)
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp ='VT' 
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
          public function getsumbill_slvt($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('SL','VT') 
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
         public function getbill_ti($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT  `doc_no` , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('TI')
                        GROUP BY doc_no";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getbill_to($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT  `doc_no` , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('TO') 
                        GROUP BY doc_no
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getbill_cn($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT `doc_no` , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('CN') 
                        GROUP BY doc_no
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getbill_dn($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT min(`doc_no`) , max(`doc_no`) , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('DN')                         
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getbill_ai($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT  `doc_no` , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
                        FROM `trn_diary2`
                        WHERE `doc_date`
                        BETWEEN '".$dayfrom."'
                        AND '".$tofrom."'
                        AND `product_id`
                        BETWEEN '".$product_start."'
                        AND '".$product_end."'
                        AND `flag` != 'C'
                        AND doc_tp IN ('AI') 
                        GROUP BY doc_no
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getbill_ao($dayfrom,$tofrom,$product_start,$product_end){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT  `doc_no` , sum( `quantity` * `stock_st` ) as quantity , sum( `amount` * `stock_st` ) as amount
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
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getstock_tmp(){ // เลขที่บิลอย่างย่อ
                  $sql="select * from tmp_rpt_stock";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
                
        
        
        public function truncate_tmp(){ // เลขที่บิลอย่างย่อ
               
            $this->db->query("TRUNCATE TABLE `tmp_rpt_stock`");
	}
        
        public function update_stock_tmp($product_id,$month,$year,$begin,$sale,$dn,$cn,$ti,$tto,$ai,$ao,$bal){ // 
            //$this->db->query("TRUNCATE TABLE `tmp_rpt_stock`");
            $sql="INSERT INTO `tmp_rpt_stock` set 
                    product_id = '$product_id',                    
                    month = '$month',
                    year = '$year',
                    begin = '$begin',
                    sale = '$sale',
                    dn = '$dn',
                    cn = '$cn',
                    ti = '$ti',
                    tto = '$tto',
                    ai = '$ai',
                    ao = '$ao',
                    bal = '$bal'";
//echo $sql;            
            $result = $this->db->query($sql);
	}
	
	
	
 public function view_stockcard($dayfrom,$tofrom,$product_start,$product_end){ // 
            $sql="SELECT `doc_date`,`product_id` ,`doc_no`,`quantity`,`stock_st`,`price`,`amount`
					FROM `trn_diary2`
					WHERE `doc_date`
					BETWEEN '".$dayfrom."'
					AND '".$tofrom."'
					AND `product_id`
					BETWEEN '".$product_start."'
					AND '".$product_end."'
					AND `flag` != 'C'
					GROUP BY product_id
					order by `doc_date`,`stock_st` desc,`doc_no`
                        ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
	
public function view_stockdata0($product_start,$product_end,$month){ // 
            $sql="SELECT a.`product_id`, a.`month`, a.`begin` as begin , b.barcode , b.name_product , b.price 
            		FROM `com_stock_master` a inner join com_product_master b on a.`product_id`=b.`product_id` 
            		WHERE a.`product_id` between '".$product_start."' AND '".$product_end."' and a.month = '".$month."'";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}	
   
public function view_stockdata($dayfrom,$tofrom,$product_start,$product_end,$month){ // 
            $sql=	"SELECT a.`doc_date`,a.`product_id` ,a.`doc_no`,a.`quantity`,a.`stock_st`,a.`price`,a.`amount`,b.`product_id`, b.`month`, b.`begin` 
            		FROM `trn_diary2` a  inner join com_stock_master b on  a.`product_id`= b.`product_id` WHERE a.`doc_date` BETWEEN '".$dayfrom."' AND '".$tofrom."' 
            		AND a.`product_id` BETWEEN '".$product_start."' AND '".$product_end."' and b.month = '".$month."' AND a.`flag` != 'C'";   
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
	
	/*public function getdocnodetail($doc_no){
		$sql="
		select 
			tbl_iq.product_id, tbl_iq.price, tbl_iq.quantity, tbl_iq.amount, tbl_prod.name_product 
		from 
			trn_diary2_iq as tbl_iq 
		left join 
			com_product_master as tbl_prod 
		on 
			tbl_iq.product_id=tbl_prod.product_id 
		where 
			tbl_iq.doc_no='$doc_no' 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}*/
}	
?>
