<?php 
class Model_Report{	
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
        
	public function getquotadetail(){
		$sql=$this->db->select()
		    		  	->from('trn_diary1',array('doc_no'));
                                       /* $sql=$this->db->select()
		    		  	->from('com_order_quota',array('month_amount','month_order','balance_amount'));
		    		  	->where('month = ?', $this->month)
		    		  	->where('year = ?', $this->year)
		    		  	->where('corporation_id  = ?', $this->corporation_id)
		    		  	->where('company_id = ?', $this->company_id)
		    		  	->where('branch_id = ?', $this->branch_id);*/
	    $data=$sql->query()->fetchAll();
	    $count=count($data);
	    if($count>0){
	    	$data=$data;
	    }else{
	    	$data=array();
	    }
	   	return $data;
	}
		
        
        
        public function getdoc_date(){ // จำนวนเงินทั้งสิ้น + ส่วนลด
		$sql="SELECT doc_date FROM `com_doc_date`";
		                
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        
        public function getamount($dayfrom,$tofrom){ // จำนวนเงินทั้งสิ้น + ส่วนลด
		$sql="SELECT sum(`amount`) as amount,sum(`discount`) as discount from trn_diary1 where `doc_date` 
                      between '$dayfrom' and '$tofrom' and `doc_tp` in ('SL','VT') and flag != 'C'";
		                
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getdiscount($dayfrom,$tofrom){ // จำนวนเงินทั้งสิ้น + ส่วนลด
		$sql="SELECT sum(`discount`+`member_discount1`+ `member_discount2` + `co_promo_discount`+ `coupon_discount`+ `special_discount`+ `other_discount`) as discount 
                      from trn_diary1 where `doc_date` between '$dayfrom' and '$tofrom' and `doc_tp` in ('SL','VT') and flag != 'C'";
                $result = $this->db->fetchAll($sql , 2);

                return $result ;
	}
                        
        public function getcredit($dayfrom,$tofrom){ // บัตรเครดิต
		$sql="SELECT sum(`net_amt`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                      and `paid`!='0000' and `doc_tp` in ('SL','VT') and flag != 'C'  ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}       
       
        
        public function getnetamt($dayfrom,$tofrom){ // คูปองเงินสด 9999
		$sql="SELECT sum(`net_amt`) as netamt FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and `paid`='9999' 
                      and `doc_tp` in ('SL','VT') and flag != 'C'  ";
		
               // echo $sql;
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
         public function getpaycashcoupon($dayfrom,$tofrom){ // คูปอง 9991-9993
		$sql="SELECT sum(`pay_cash_coupon`) as `pay_cash_coupon` FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and `paid` in ('9991','9992','9993') 
                      and `doc_tp` in ('SL','VT') and flag != 'C'  ";
		//echo $sql;              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        public function getcash($dayfrom,$tofrom){ // เงินสด
		$sql="SELECT sum(`net_amt`) as cash FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and `paid`= '0000' 
                      and flag != 'C' and `doc_tp` in ('SL','VT') ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
         public function getincome($dayfrom,$tofrom){ // โอนสินค้าเข้า 
		$sql="SELECT sum(`quantity`) as quantity,sum(`amount`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='TI'";
		              
                  $result = $this->db->fetchAll($sql , 2);
 
                  return $result ;
	}
        
        public function getoutcome($dayfrom,$tofrom){ // โอนสินค้าออก
		$sql="SELECT sum(`quantity`) as quantity,sum(`amount`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='TO'";
		              
                  $result = $this->db->fetchAll($sql , 2);
                  
                //  echo $sql;

                  return $result ;
	}
        
        public function getchdata($dayfrom,$tofrom){ //เปลี่ยนสินค้า
		$sql="SELECT sum(`quantity`) as quantity,sum(`net_amt`) as amount ,sum(`amount`) as gross FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='CN' 
                      and `status_no`='40' ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getback($dayfrom,$tofrom){  //คืนเงิน
		$sql="SELECT sum(`quantity`) as quantity,sum(`net_amt`) as amount ,sum(`amount`) as gross
                      FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='CN' 
                      and `status_no`='41' ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getchother($dayfrom,$tofrom){ // เปลี่ยนสินค้าต่างสาขา
		$sql="SELECT sum(`quantity`) as quantity,sum(`amount`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='CN' 
                      and `status_no`='42' ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getchincome($dayfrom,$tofrom){ //ปรับปรุงเข้า AI
		$sql="SELECT sum(`quantity`) as quantity,sum(`amount`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='AI'  ";
		              
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getchoutcome($dayfrom,$tofrom){  //ปรับปรุงออก AO
		$sql="SELECT sum(`quantity`) as quantity,sum(`amount`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='AO'  ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getbill($dayfrom,$tofrom){  // สินค้าขาด
		$sql="SELECT sum(`quantity`) as quantity,sum(`amount`) as amount FROM `trn_diary1` 
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C' and `doc_tp`='SL' and status_no ='19'  ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getnewmember_0($dayfrom,$tofrom){ //สมัครสมาชิกใหม่ คำสั่งเดิม
                  $sql="SELECT count(status_no) as newmem   FROM `trn_diary1` 
                        WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  
                        and status_no ='01' and `doc_tp` in ('SL','VT')  ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        
        public function getnewmember($dayfrom,$tofrom){ //สมัครสมาชิกใหม่ 
                  $sql="select count(a.doc_no),sum(a.net_amt) as sum_net_amt,b.promo_code from trn_diary1 a inner join 
                            (SELECT distinct doc_no,promo_code FROM trn_diary2
                            WHERE flag != 'C' AND doc_date between '$dayfrom' and '$tofrom' AND `status_no` = '01')
                        b on a.doc_no = b.doc_no WHERE a.flag != 'C' 
                        AND a.doc_date
                        between '$dayfrom' and '$tofrom'
                        AND a.`status_no` = '01'
                        group by b.promo_code ";
		              
                  $result = $this->db->fetchAll($sql , 2);

                 // echo $sql;
                  
                  return $result ;
	}
        
        
        public function getwalkin($dayfrom,$tofrom){ // นับจำนวน walkin
                  $sql="SELECT count(distinct(`doc_no`)) as count,sum(`net_amt`) as amt  FROM `trn_diary1` 
                        WHERE `doc_date` between '$dayfrom' and '$tofrom' and `member_id`='' 
                        and flag != 'C' and `doc_tp` in('SL','VT')";
		              
                  $result = $this->db->fetchAll($sql , 2);

              //   echo $sql;
                  
                  return $result ;
	}
	
 public function getcountmem($dayfrom,$tofrom){ // นับจำนวนสมาชิกที่ซื้อ 
                  $sql="SELECT  count(`doc_no`) as count,sum(`net_amt`) as amt  FROM `trn_diary1` 
                  		WHERE `doc_date` between '$dayfrom' and '$tofrom' and `member_id` != '' and flag != 'C' 
                  		and `doc_tp` in('SL','VT')and length(`member_id`)='13'";
		               
                  $result = $this->db->fetchAll($sql , 2);

         //        echo $sql;
                  
                  return $result ;
	}
        
        
        public function getcredittype($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิต
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,sum(trn_diary1.net_amt) as sum ,com_paid.description  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid !='0000' and trn_diary1.doc_tp in ('SL','VT')  
                           group by trn_diary1.paid,trn_diary1.credit_tp";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        
        public function getcreditkbank($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิตแต่ละประเภท กสิกร
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,com_paid.description,trn_diary1.net_amt  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid ='0001' and trn_diary1.doc_tp in ('SL','VT')  ";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getcreditbbl($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิตแต่ละประเภท กรุงเทพ
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,com_paid.description,trn_diary1.net_amt  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid ='0002' and trn_diary1.doc_tp in ('SL','VT')  ";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getcreditscb($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิตแต่ละประเภท ไทยพาณิชย์
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,com_paid.description,trn_diary1.net_amt  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid ='0003' and trn_diary1.doc_tp in ('SL','VT')  ";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        public function getbill_no($dayfrom,$tofrom){ // เลขที่บิลอย่างย่อ
                  $sql="SELECT a.doc_tp,min(a.doc_no) as min,max(a.doc_no) as max,count(a.doc_no) as count,sum(a.quantity) as quantity, sum(a.amount) as amount,b.remark 
                        FROM trn_diary1 a inner join com_doc_no  b on a.doc_tp = b.doc_tp 
                        WHERE a.doc_date  between '$dayfrom'and '$tofrom' and a.flag != 'C' group by b.doc_tp";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getcancel($dayfrom,$tofrom){ // บิลยกเลิก
                  $sql="SELECT doc_tp,doc_no,quantity,amount FROM trn_diary1 WHERE doc_date between '$dayfrom'and '$tofrom'  and flag = 'C' ";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getCN_cash($dayfrom,$tofrom){ // ลดหนี้เงินสด
                  $sql="SELECT a.doc_no ,a.refer_doc_no, a.net_amt as net_cn ,b.net_amt as net_sl,
                        sum(if (b.net_amt<= a.net_amt,b.net_amt,a.net_amt)) as net, 
                        b.doc_no FROM trn_diary1  a inner join  trn_diary1 b on(a.refer_doc_no != '' and a.refer_doc_no = b.doc_no) 
                        where  a.doc_date between '$dayfrom' and '$tofrom'  
                        and a.doc_tp in ('SL','VT') and a.flag != 'C' and a.paid = '0000'";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getCN_credit($dayfrom,$tofrom){ // ลดหนี้เครดิต
                  $sql="SELECT a.doc_no ,a.refer_doc_no, a.net_amt as net_cn ,b.net_amt as net_sl,
                        sum(if (b.net_amt<= a.net_amt,b.net_amt,a.net_amt)) as net, b.doc_no 
                        FROM trn_diary1  a inner join  trn_diary1 b on(a.refer_doc_no != '' and a.refer_doc_no = b.doc_no) 
                        where  a.doc_date between '$dayfrom' and '$tofrom'  
                        and a.doc_tp in ('SL','VT') and a.flag != 'C' and a.paid != '0000'";
		  //echo $sql;          
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
	
        public function getmember5_discount($dayfrom,$tofrom){ // ส่วนลด 5 %
                  $sql="SELECT  sum(`member_discount1`) as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 5.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getmember5_count($dayfrom,$tofrom){ // จำนวนส่วนลด 5 %
                  $sql="SELECT  count(distinct(`doc_no`)) as count
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 5.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getmember15_discount($dayfrom,$tofrom){ // ส่วนลด 15 %
                  $sql="SELECT  sum(`member_discount1`) as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 15.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getmember15_count($dayfrom,$tofrom){ // ส่วนลด 15 %
                  $sql="SELECT  count(distinct(`doc_no`)) as count
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 15.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getmember15_10_discount($dayfrom,$tofrom){ // ส่วนลด 15+10 %
                  $sql="SELECT sum(`member_discount1`+`member_discount2`)as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 15.00 AND `member_percent2` = 10.00 and flag != 'C'";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getmember15_10_count($dayfrom,$tofrom){ // ส่วนลด 15+10 %
                  $sql="SELECT count(distinct(`doc_no`)) as count
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 15.00 AND `member_percent2` = 10.00 and flag != 'C'";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getmember20_discount($dayfrom,$tofrom){ // ส่วนลด 20 %
                  $sql="SELECT  sum(`member_discount1`) as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 20.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        
        public function getmember20_count($dayfrom,$tofrom){ // ส่วนลด 20 %
                  $sql="SELECT count(distinct(`doc_no`)) as count
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 20.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function getmember25_discount($dayfrom,$tofrom){ // ส่วนลด 25 %
                  $sql="SELECT  sum(`member_discount1`) as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 25.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
       
        public function getmember25_count($dayfrom,$tofrom){ // ส่วนลด 25 %
                  $sql="SELECT  count(distinct(`doc_no`)) as count
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 25.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	} 
        
        public function getmember50_discount($dayfrom,$tofrom){ // ส่วนลด 50 %
                  /*$sql="SELECT  sum(`member_discount1`) as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `member_percent1` = 50.00  and `member_discount1` > 0 and flag != 'C' and doc_tp in ('SL','VT')";*/
		  
                  
                  $sql= "SELECT sum(`net_amt`) as sum_net_amt
                        FROM `trn_diary2`  WHERE `doc_date` between '$dayfrom' and '$tofrom'   
                        and `status_no` = 03  and flag != 'C' and doc_tp in ('SL','VT')";
                          
                  $result = $this->db->fetchAll($sql , 2);
//echo $sql;
                  return $result ;
	}
        
        public function getmember50_count($dayfrom,$tofrom){ // ส่วนลด 50 %
                  $sql="SELECT  count(distinct(`doc_no`)) as count 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'  
                        and `status_no` = 03  and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function get_discount_trn2($dayfrom,$tofrom){ // ส่วนลด discount
                  $sql="SELECT sum(discount) as sum_discount FROM `trn_diary2` 
                        WHERE `doc_date` between '$dayfrom' and '$tofrom' and `discount` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function count_discount_trn2($dayfrom,$tofrom){ // ส่วนลด discount
                  $sql="SELECT count(distinct(`doc_no`)) as count  FROM `trn_diary2` 
                        WHERE `doc_date` between '$dayfrom' and '$tofrom' and `discount` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function get_coupon_discount($dayfrom,$tofrom){ // ส่วนลด discount
                  $sql="SELECT sum(coupon_discount) as sum_discount 
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                        and `coupon_discount` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function count_coupon_discount($dayfrom,$tofrom){ // ส่วนลด discount
                  $sql="SELECT count(distinct(`doc_no`)) as count
                        FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                        and `coupon_discount` > 0 and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        public function get_newbirth($dayfrom,$tofrom){ // ส่วนลด newbirth
                  $sql="SELECT  sum(`net_amt`) as amount FROM `trn_diary2` 
                        WHERE promo_code ='NEWBIRTH' and `doc_date` between '$dayfrom' and '$tofrom' 
                        and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        public function count_newbirth($dayfrom,$tofrom){ // ส่วนลด newbirth
                  $sql="SELECT  count(distinct(`doc_no`)) as count FROM `trn_diary2` 
                        WHERE promo_code ='NEWBIRTH' and `doc_date` between '$dayfrom' and '$tofrom' 
                        and flag != 'C' and doc_tp in ('SL','VT')";
		            
                  $result = $this->db->fetchAll($sql , 2);

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
	
		public function getsalesummary($dayfrom,$tofrom){
                  $sql="SELECT d.product_id, p.`name_product`, p.`name_print`, sum(`quantity`) as quantity, sum(`amount`) as amount, 
                        sum(`net_amt`) as net_amt  FROM `trn_diary2` as d left join com_product_master as p 
                        on d.product_id = p.product_id where flag != 'C' and doc_tp in ('SL','VT','DN') 
                        and `doc_date` between '$dayfrom' and '$tofrom' group by `product_id`";
		         
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
		public function getproductsummary($dayfrom,$tofrom,$type){
                  $sql="SELECT d.product_id, p.`name_product`, p.`name_print`, sum(`quantity`) as quantity, sum(`amount`) as amount, 
                        sum(`net_amt`) as net_amt  FROM `trn_diary2` as d left join com_product_master as p 
                        on d.product_id = p.product_id where flag != 'C' and doc_tp = '$type' 
                        and `doc_date` between '$dayfrom' and '$tofrom' group by `product_id`";
		         
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
		public function getcoupon($dayfrom,$tofrom){
                  $sql="SELECT `co_promo_code`, count(`doc_no`) as bill , sum(`coupon_discount`) as dis FROM `trn_diary1` 
                  		WHERE `coupon_code` != '' AND `doc_date` BETWEEN '$dayfrom' AND '$tofrom' 
                  		GROUP BY `coupon_code`";
		         
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
}	
?>
