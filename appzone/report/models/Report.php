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
		
        
		public function getprinter(){ // check printer
		$sql="SELECT thermal_printer FROM `com_branch_computer`";
		                
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
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
                        
        public function getcredit($dayfrom,$tofrom){  // get credit only
        	/**
        	 * @desc บัตรเครดิต
        	 * @return 
        	 */
                    	
			$sql="SELECT sum(`net_amt`) as amount FROM `trn_diary1` 
                  WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                  and `paid` !='0000' and `paid` !='' and `paid` ='0002' AND (`bank_tp` in ('Credit Card') OR `credit_tp` = '0002')  and `doc_tp` in ('SL','VT') and flag != 'C'  ";
              $result = $this->db->fetchAll($sql , 2);
              return $result ;
        	
			// $sql="SELECT sum(`net_amt`) as amount FROM `trn_diary1` 
	            //           WHERE `doc_date` between '$dayfrom' and '$tofrom' 
	            //           and `paid` !='0000' and `paid` !='' and `credit_tp` ='0002' and `doc_tp` in ('SL','VT') and flag != 'C'  ";
	            //       $result = $this->db->fetchAll($sql , 2);
	            //       return $result ;
	                  

		}//func
       


      public function getcreditaba($dayfrom, $tofrom)
      { // aba
            

            $sql = "SELECT * FROM `trn_diary1`
            WHERE `doc_date` between '$dayfrom' and '$tofrom' 
            and `paid` !='0000' and `paid` !='' and `doc_tp` in ('SL','VT') and flag != 'C'  AND `bank_tp` in ('ABA PAY+','ABA PAY')";

            $result = $this->db->fetchAll($sql, 2);

            return $result;
      }
      public function getaba($dayfrom, $tofrom)
      {
            /**
             * @desc บัตรเครดิต
             * @return 
             */

            $sql = "SELECT sum(`net_amt`) as amount FROM `trn_diary1` 
                                    WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                                    and `paid` !='0000' and `paid` !='' and `doc_tp` in ('SL','VT') and flag != 'C'  AND `bank_tp` in ('ABA PAY+','ABA PAY')";
            $result = $this->db->fetchAll($sql, 2);
            return $result;
      } //func

        public function getallcredit($dayfrom, $tofrom){  // บัตรเครดิตทั้งหมด

                  $sql = "SELECT sum(`net_amt`) as amount FROM `trn_diary1` 
                              WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                              and `paid` !='0000' and `paid` ='0002' and `doc_tp` in ('SL','VT') and flag != 'C'  ";
                  $result = $this->db->fetchAll($sql, 2);
                  return $result;
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
                      WHERE `doc_date` between '$dayfrom' and '$tofrom' and (`paid`= '0000' OR `paid`='')
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
                            WHERE flag != 'C' AND doc_date between '$dayfrom' and '$tofrom' AND `status_no` = '01' AND promo_code != 'FREEBAG')
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
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,sum(trn_diary1.pay_credit) as sum ,com_paid.description  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid !='0000' and trn_diary1.doc_tp in ('SL','VT')  
                           group by trn_diary1.paid,trn_diary1.credit_tp";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
        
        
        public function getcreditkbank($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิตแต่ละประเภท กสิกร
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,com_paid.description,trn_diary1.pay_credit  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid ='0001' and trn_diary1.doc_tp in ('SL','VT')  ";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
                  
	}

      public function getcreditmain($dayfrom,$tofrom){ // PM make credit only 18-1-2023                  
            $sql = "SELECT * FROM `trn_diary1`
                        WHERE `doc_date` between '$dayfrom' and '$tofrom' 
                        and `paid` ='0002' and `paid` !='' and `doc_tp` in ('SL','VT') and flag != 'C'  AND `credit_tp` = '0002'";

                  $result = $this->db->fetchAll($sql, 2);
                  return $result;
      }



      public function getOtherCredit($dayfrom,$tofrom){
            $sql ="SELECT sum( a.net_amt ) AS amount, b.credit_tp, b.description
            FROM com_paid b
            INNER JOIN trn_diary1 a ON ( a.bank_tp
            IN (
            b.description
            )
            OR a.credit_tp = b.credit_tp )
            AND a.paid = '0002'
            AND a.credit_tp != '0002'
            WHERE a.doc_date
            BETWEEN '$dayfrom'
            AND '$tofrom'
            AND a.flag != 'C'
            AND a.doc_tp
            IN (
            'SL', 'VT'
            )
            GROUP BY b.seq";     
                  $result = $this->db->fetchAll($sql , 2);
            return $result;
      }


        public function getAllCreditDetail($dayfrom,$tofrom){
            $sql ="SELECT a.net_amt AS amount, b.credit_tp, b.description
            FROM com_paid b
            INNER JOIN trn_diary1 a ON ( a.bank_tp
            IN (
            b.description
            )
            OR a.credit_tp = b.credit_tp )
            AND a.paid = '0002'
            WHERE a.doc_date
            BETWEEN '$dayfrom'
            AND '$tofrom'
            AND a.flag != 'C'
            AND a.doc_tp
            IN (
            'SL', 'VT'
            )
            ORDER BY b.seq";     
                  $result = $this->db->fetchAll($sql , 2);
            return $result;

        }




        
        public function getcreditbbl($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิตแต่ละประเภท กรุงเทพ
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,com_paid.description,trn_diary1.pay_credit  
                           FROM `trn_diary1` inner join `com_paid` ON trn_diary1.paid = com_paid.paid 
                           where trn_diary1.doc_date between '$dayfrom'and '$tofrom' and trn_diary1.flag != 'C'  
                           and trn_diary1.paid ='0002' and trn_diary1.doc_tp in ('SL','VT')  ";     
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}



        
        public function getcreditscb($dayfrom,$tofrom){ // รายละเอียดบัตรเครดิตแต่ละประเภท ไทยพาณิชย์
                  //$sql="SELECT paid ,credit_tp,sum(net_amt)  FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom' and flag != 'C'  and paid !='0000' and `doc_tp` in ('SL','VT')  group by paid,credit_tp ";
		    $sql ="SELECT trn_diary1.paid ,trn_diary1.credit_tp,com_paid.description,trn_diary1.pay_credit  
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
                  $sql="SELECT a.doc_no ,a.refer_doc_no, a.net_amt as net_sl ,b.net_amt as net_cn,
                        sum(if (b.net_amt<= a.net_amt,b.net_amt,a.net_amt)) as net, 
                        b.doc_no FROM trn_diary1  a inner join  trn_diary1 b on(a.refer_doc_no != '' and a.refer_doc_no = b.doc_no and a.status_no='06') 
                        where  a.doc_date between '$dayfrom' and '$tofrom'  
                        and a.doc_tp in ('SL','VT') and a.flag != 'C' and a.paid = '0000'";
		            
                  $result = $this->db->fetchAll($sql , 2);
                  
                  //echo $sql;

                  return $result ;
	}
        
        public function getCN_credit($dayfrom,$tofrom){ // ลดหนี้เครดิต
                  $sql="SELECT a.doc_no ,a.refer_doc_no, a.net_amt as net_sl ,b.net_amt as net_cn,
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
                 /* $sql="SELECT  sum(`net_amt`) as amount FROM `trn_diary2` 
                        WHERE promo_code ='NEWBIRTH' and `doc_date` between '$dayfrom' and '$tofrom' 
                        and flag != 'C' and doc_tp in ('SL','VT')";*/
                        
                  $sql="SELECT promo_code, count( DISTINCT (`doc_no`) ) AS count, sum( `amount` ) AS amount
							FROM `trn_diary2`
							WHERE promo_code LIKE 'OM02%' or  promo_code LIKE 'OX02%'
							AND `doc_date`
							BETWEEN  '$dayfrom'
							AND '$tofrom' 
							AND flag != 'C'
							AND doc_tp
							IN ('SL', 'VT')
							GROUP BY promo_code";      
		            
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
	
		public function get_pointdata($dayfrom,$tofrom){
				$sql="SELECT promo_code, count( DISTINCT (`doc_no`) ) AS count, sum( `amount` ) AS amount
						FROM `trn_diary2`
						WHERE promo_code LIKE '%STKER%' and promo_code != 'STKER-2'
						AND doc_date
						BETWEEN  '$dayfrom' and '$tofrom' 
						AND flag != 'C'
						AND doc_tp
						IN ('SL', 'VT')
						GROUP BY promo_code";
						//echo $sql;
			    $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}	
	
		public function get_new_pointdata($dayfrom,$tofrom){
				$sql="SELECT promo_code, count( DISTINCT (`doc_no`) ) AS count, sum( `amount` ) AS amount
						FROM `trn_diary2`
						WHERE promo_code LIKE '%OM19%' 
						AND doc_date
						BETWEEN  '$dayfrom' and '$tofrom' 
						AND flag != 'C'
						AND doc_tp
						IN ('SL', 'VT')
						GROUP BY promo_code";
						//echo $sql;
			    $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}	
	public function get_bill02($dayfrom,$tofrom){ // ส่วนลด newbirth
                  $sql="SELECT tb1.application_id, count( tb2.doc_no ) AS count, sum( tb2.amount ) AS amount
							FROM (

							SELECT member_id, application_id, doc_no, status_no
							FROM `trn_diary1`
							WHERE `status_no` = '01'
							AND application_id != 'OPPN300'
							AND doc_date
							between '$dayfrom' and '$tofrom' 
							AND flag != 'C'
							) AS tb1
							INNER JOIN (

							SELECT member_id, application_id, doc_no, status_no, amount
							FROM `trn_diary1`
							WHERE `status_no` = '02'
							AND doc_date
							between '$dayfrom' and '$tofrom' 
							AND flag != 'C'
							) AS tb2 ON tb1.member_id = tb2.member_id
							GROUP BY tb1.application_id";
		            
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
		public function getcoupon($dayfrom,$tofrom,$couponstart,$couponend){
                  /*$sql="SELECT `co_promo_code`, count(`doc_no`) as bill , sum(`coupon_discount`) as dis FROM `trn_diary1` 
                  		WHERE `coupon_code` != '' AND `doc_date` BETWEEN '$dayfrom' AND '$tofrom' 
                  		GROUP BY `coupon_code`";*/  // old
                  		
		         if($couponstart != "" or $couponend != ""){
		         	$couponcode = "and substr(coupon_code,1,6)>='$couponstart' and substr(coupon_code,1,6)<='$couponend' ";
		         	 $couponcode2 = " union all SELECT `doc_date`,`promo_code`,`doc_no` ,`flag` FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'
		         	and `promo_code` between '".$couponstart."' and '".$couponend."' ";
		         }else{
		         $couponcode = "";
		         $couponcode2 = "";
		         }
		         
		         $sql = "SELECT `doc_date`,`coupon_code`,`doc_no` ,`flag` FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom'  and `coupon_code` != '' $couponcode and `coupon_code` not like '%OK%' $couponcode2 ";
		         
		         //echo $sql;
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	
		public function getcountcoupon($dayfrom,$tofrom,$couponstart,$couponend){
                  /*$sql="SELECT `co_promo_code`, count(`doc_no`) as bill , sum(`coupon_discount`) as dis FROM `trn_diary1` 
                  		WHERE `coupon_code` != '' AND `doc_date` BETWEEN '$dayfrom' AND '$tofrom' 
                  		GROUP BY `coupon_code`";*/  // old
                  		
		            if($couponstart != "" or $couponend != ""){
		         	$couponcode = "and substr(coupon_code,1,6)>='$couponstart' and substr(coupon_code,1,6)<='$couponend' ";
		         	 $couponcode2 = " union all SELECT `doc_date`,0 as countcoupon,count( `promo_code` ) AS count_promo_coupon,`doc_no` ,`flag` FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom' and `promo_code` between '".$couponstart."' and '".$couponend."' GROUP BY promo_code";
		         }else{
		         $couponcode = "";
		         $couponcode2 = "";
		         }
		         
		         $sql = "SELECT `doc_date`, count(`coupon_code`) as countcoupon , 0 as count_promo_coupon, `doc_no`,`flag` FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom'  and `coupon_code` != '' $couponcode and `coupon_code` not like '%OK%'  group by `doc_date` $couponcode2";
		         //echo $sql;
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
		}
		public function getcountcncoupon($dayfrom,$tofrom,$couponstart,$couponend){              
                  		
		         if($couponstart != "" or $couponend != ""){
		         	$couponcode = "and substr(coupon_code,1,6)>='$couponstart' and substr(coupon_code,1,6)<='$couponend' ";
		         }else{
		         $couponcode = "";
		         }
		         
		         $sql = "SELECT count( `flag` ) as flag FROM `trn_diary1` WHERE `doc_date` between '$dayfrom' and '$tofrom'  and `coupon_code` != '' $couponcode  and `coupon_code` not like '%OK%' AND flag = 'C' ORDER BY doc_no ASC";
		         //echo $sql;
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	
	
	public function getcountcoupon2($dayfrom,$tofrom,$couponstart,$couponend){
                  /*$sql="SELECT `co_promo_code`, count(`doc_no`) as bill , sum(`coupon_discount`) as dis FROM `trn_diary1` 
                  		WHERE `coupon_code` != '' AND `doc_date` BETWEEN '$dayfrom' AND '$tofrom' 
                  		GROUP BY `coupon_code`";*/  // old
                  		
		    /*     if($couponstart != "" or $couponend != ""){
		         	$couponcode = "and substr(promo_code,1,6)>='$couponstart' and substr(promo_code,1,6)<='$couponend' ";
		         }else{
		         $couponcode = "";
		         }
		         */
		         $sql = "SELECT `doc_date`, count(`promo_code`) as countpromo ,`flag` FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'   and `promo_code` between '".$couponstart."' and '".$couponend."'   group by `doc_date` ";
		         //echo $sql;
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
		}
		public function getcountcncoupon2($dayfrom,$tofrom,$couponstart,$couponend){              
                  		
		     /*    if($couponstart != "" or $couponend != ""){
		         	$couponcode = "and substr(promo_code,1,6)>='$couponstart' and substr(promo_code,1,6)<='$couponend' ";
		         }else{
		         $couponcode = "";
		         }*/
		         
		         $sql = "SELECT count( `flag` ) as flag FROM `trn_diary2` WHERE `doc_date` between '$dayfrom' and '$tofrom'   and `promo_code` between '".$couponstart."' and '".$couponend."'  AND flag = 'C' ORDER BY doc_no ASC";
		         //echo $sql;
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	
		public function gettax_sl($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,`doc_no`,`net_amt`,`vat`,`paid`,`flag` FROM `trn_diary1` 
                  		where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('SL') 
                  		order by `doc_date`,`doc_no`";
		 		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
		public function gettax_vt($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,`doc_no`,`net_amt`,`vat`,`paid`,`flag` FROM `trn_diary1` 
                  		where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('VT')  
                  		order by `doc_date`,`doc_no`";
		 		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	/*	public function gettax2($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,max(`doc_no`) as max,min(`doc_no`) as min,sum(`net_amt`) as net_amt,sum(`vat`) as vat,sum(`paid`) as 				paid FROM `trn_diary1` where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('SL','VT') group by `doc_date` 				order by `doc_date`";
		  //echo $sql;		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}*/
		public function gettax2_sl($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,max(`doc_no`) as max,min(`doc_no`) as min,sum(`net_amt`) as net_amt,sum(if(paid='0000' && flag <> 'c',net_amt,0)) as cash ,sum(if(paid<>'0000' && flag <> 'c',net_amt,0))  as credit,sum(`vat`) as vat,sum(`paid`) as paid FROM `trn_diary1` where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('SL') group by `doc_date` order by `doc_date`";
		  //echo $sql;		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	/*	public function gettax2_sl_cash($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,max(`doc_no`) as max,min(`doc_no`) as min,sum(`net_amt`) as net_amt,sum(`vat`) as vat,sum(`paid`) as 				paid FROM `trn_diary1` where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('SL') and `paid` = '0000' group by 				`doc_date` order by `doc_date`";
		  //echo $sql;		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
		public function gettax2_sl_credit($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,max(`doc_no`) as max,min(`doc_no`) as min,sum(`net_amt`) as net_amt,sum(`vat`) as vat,sum(`paid`) as 				paid FROM `trn_diary1` where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('SL') and `paid` != '0000' group by 				`doc_date` order by `doc_date`";
		 // echo $sql;		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}*/
	
		public function gettax2_vt($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,max(`doc_no`) as max,min(`doc_no`) as min,sum(`net_amt`) as net_amt,sum(if(paid='0000' && flag <> 'c',net_amt,0)) as cash ,sum(if(paid<>'0000' && flag <> 'c',net_amt,0))  as credit,sum(`vat`) as vat,sum(`paid`) as paid FROM `trn_diary1` where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('VT') group by `doc_date` order by `doc_date`";
		  //echo $sql;		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
		public function getcn($dayfrom,$tofrom){
                  $sql="SELECT `doc_date`,`doc_no`,`net_amt`,`vat`,`paid` FROM `trn_diary1` 
                  		where doc_date BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in ('CN') 
                  		order by `doc_date`,`doc_no`";
		 		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	
		public function getcomm($dayfrom,$tofrom){
                  $sql="SELECT `doc_date` , sum(`amount`) , (sum(`amount`)-sum(`net_amt`)) as discount, sum(`net_amt`) 
                  		FROM `trn_diary1`  WHERE `doc_date` BETWEEN '$dayfrom' AND '$tofrom' and doc_tp in('SL','VT') and flag !='C' group by `doc_date`";
		 		  
                  $result = $this->db->fetchAll($sql , 2);

                  return $result ;
	}
	
		public function getpo($now_date,$branch_id){
                  $sql="SELECT `doc_date`, sum(`amount`) as amount FROM `trn_diary1` where substr(`doc_date`,1,7) = '$now_date' and `branch_id` = '$branch_id' and doc_tp in('SL','VT') and flag !='C' group by `doc_date`";
		 		  
                  $result = $this->db->fetchAll($sql , 2);
		  //echo $sql;
                  return $result ;
	}
		public function getpo2($date,$branch_id){
                  $sql="SELECT `doc_date`, sum(`amount`) as amount FROM `trn_diary1` where doc_date = '$date' and `branch_id` = '$branch_id' and doc_tp in('SL','VT') and flag !='C' group by `doc_date`";
		 		  
                  $result = $this->db->fetchAll($sql , 2);
		  //echo $sql;
                  return $result ;
	}
	
		public function get_com_po($now_date,$branch_id){
                  $sql="SELECT * FROM `com_data_po` where substr(`doc_date`,1,7) = '$now_date' and `branch_id` = '$branch_id'";
		 		  
                  $result = $this->db->fetchAll($sql , 2);
		  //echo $sql;
                  return $result ;
	}
		public function get_com_po2($date,$branch_id){
                  $sql="SELECT * FROM `com_data_po` where doc_date = '$date' and `branch_id` = '$branch_id' ";
		 		  
                  $result = $this->db->fetchAll($sql , 2);
		  //echo $sql;
                  return $result ;
	}
	
		public function add_po($branch_id,$doc_date,$amt_sale){
		  $date = date('Y-m-d');
		  $time = date('H:i:s');
                  $sql = array(
                  		'corporation_id'=>'OP',
                  		'company_id'=>'OP',
                  		'branch_id'=>$branch_id,
                  		'branch_no'=>$branch_id,
				'doc_date'=>$doc_date,
				'amt_sale'=>$amt_sale,
				'reg_date'=>$date,
				'reg_time'=>$time);
		 		  
                  $result=$this->db->insert('com_data_po', $sql);
		  //echo $sql;
                  return $result ;
	}	
	
		public function set_po($i,$branch_id,$add){
		  $now_date = date('Y-m');
		  $date = date('Y-m-d');
		  $time = date('H:i:s');
		  if($i < '10'){
		           $i = '0'.$i;
		  }
                  $sql = "update com_data_po 
                  	set 
                  		amt_po = '".$add."',
                  		upd_date = '".$date."',
                  		upd_time = '".$time."',
                  		upd_user = ''
                  	where  branch_id = '".$branch_id."' and `doc_date` ='".$now_date."-".$i."'";
		 		  
                  $result=$this->db->query($sql);
		  //echo $sql."<br>";
                  return $result ;
	}		
	public function set_po2($date,$branch_id,$add){
		  $now_date = date('Y-m-d');
		  $time = date('H:i:s');
		  if($i < '10'){
		           $i = '0'.$i;
		  }
                  $sql = "update com_data_po 
                  	set 
                  		amt_sale = '".$add."',
                  		upd_date = '".$now_date."',
                  		upd_time = '".$time."',
                  		upd_user = ''
                  	where  branch_id = '".$branch_id."' and `doc_date` ='".$date."'";
		 		  
                  $result=$this->db->query($sql);
		  //echo $sql."<br>";
                  return $result ;
	}				
	public function getSatang($net_amt,$flgs='normal'){
            /**
             * @desc
             * @return
             */
            if(!is_numeric($net_amt)) return '0.00';
            $stangpos=strrpos($net_amt,'.');

            $old_pos=strrpos($net_amt,'.');

            if($stangpos>0){
                $stangpos+=1;
                $stang=substr($net_amt,$stangpos,2);
                $stang=substr($stang."00",0,2);//*WR 19072012 resol 412.5 to 412.50
            }else{
                $stang=0;
            }
            $stang=(int)$stang;
            if($flgs=='normal'){
                if(($stang >= 1) && ($stang <= 24)) $stang=00;
                if(($stang >= 26) && ($stang <= 49)) $stang=25;
                if(($stang >= 51) && ($stang <= 74)) $stang=50;
                if(($stang >= 76) && ($stang <= 99)) $stang=75;
            }else if($flgs=='up'){
                if(($stang >= 1) && ($stang <= 25)) $stang=25;
                if(($stang >= 26) && ($stang <= 50)) $stang=50;
                if(($stang >= 51) && ($stang <= 75)) $stang=75;
                if(($stang >= 76) && ($stang <= 99)){
                    $stang='00';
                    $net_amt=$net_amt+1;
                    $stangpos_net_amt=strrpos($net_amt,'.');
                    if($stangpos_net_amt>$old_pos){
                        $stangpos+=1;
                        //ex. 99.90 ==> 100.00
                    }

                }
            }
            if($stangpos>0){
                $net_amt=substr($net_amt,0,$stangpos).$stang;
            }
            return $net_amt;
        }//func 		
}	
?>
