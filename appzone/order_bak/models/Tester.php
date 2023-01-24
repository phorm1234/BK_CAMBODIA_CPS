<?php 
	class Model_Tester{
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
		public $doc_tp;
		public $stock_st;
		public $status_no;
		public  $doc_date;
		public function __construct(){
			$this->db=Zend_Registry::get('db1');
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$this->empprofile=$empprofile;
			$this->corporation_id=$this->empprofile['corporation_id'];
			$this->company_id=$this->empprofile['company_id'];
			$this->branch_id=$this->empprofile['branch_id'];
			$this->branch_no=$this->empprofile['branch_no'];
			$this->com_no=$this->empprofile['computer_no'];
			$this->user_id=$this->empprofile['user_id'];			
			$this->doc_tp='RT';
			$this->status_no='25';			
			$this->date=date('Y-m-d');
			$this->year_month=date('Y-m');
			$this->time=date('H:i:s');
			$this->month=date('n');
			$this->m_month=date('m');
			$this->year=date('Y');
			//check doc_date
			$objPos=new SSUP_Controller_Plugin_PosGlobal();
			$this->doc_date=$objPos->getDocDatePos($this->corporation_id,$this->company_id,$this->branch_id,$this->branch_no);
		}
		
		public function listDocno(){
			$arrDate=explode("-",$this->doc_date);
			$m=$arrDate[1];
			$d=$arrDate[2];
			$y=$arrDate[0];
			
			$str = "<option value=\"\">**New**</option>";
			$sql = "SELECT 
						id,
						doc_no,
						status_transfer 
					FROM 
						trn_diary1_rt
					WHERE
						YEAR(doc_date)='$y' AND MONTH(doc_date)='$m' 
					order by id asc
					";
			$res = $this->db->FetchAll($sql);
			if(count($res) > 0){
// 				echo "<pre>";
// 				print_r($res);
// 				echo "</pre>";
				foreach ($res as $index){
					
					//*WR02112016 
					if($index['status_transfer']!='T'){
						$str .= '<option value="'.$index['doc_no'].','.$index['status_transfer'].'">'.$index['doc_no'].'</option>';
					}
					/*
					$doc_no=$index['doc_no'];
					$sql_chkconfirm="SELECT COUNT(*) AS num_chk FROM trn_diary1_rt
					WHERE doc_no='$doc_no' AND status_transfer='T'";echo $sql_chkconfirm;exit();
					$row_chkconfirm = $this->db->FetchAll($sql_chkconfirm);
					if(empty($row_chkconfirm)){
						$str .= '<option value="'.$index['doc_no'].','.$index['status_transfer'].'">'.$index['doc_no'].'</option>';
					}*/
					
					//$str .= '<option value="'.$index['doc_no'].','.$index['status_transfer'].'">'.$index['doc_no'].'</option>';
				}
			}
			return $str;
		}//func
		
		public function check_order_tester(){
			$sql = "SELECT * 
					FROM com_order_quata_tester t1 
					INNER JOIN com_doc_date t2 ON t1.company_id = t2.company_id 
					WHERE branch_id='$this->branch_id' AND t1.month <= month(t2.doc_date) and
						  t1.year <= year(t2.doc_date)
					";
			if(count($this->db->FetchAll($sql) > 0)){
				return "1";  //ทำงานไม่ได้
			}else{
				return "0";  //ทำงานได้
			}
		}
		
		public function check_quota(){			
			/*
			 * @desc check quota
			 * @modify 27102016
			 */			
			$sqlq = "SELECT * FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";
			$resq = $this->db->FetchRow($sqlq);			
			//*WR09112016
			$quota = $resq['month_amount'];
			$balance = $resq['month_amount']-$resq['rt_amount'];
			//$quota = $resq['balance_amount']-$resq['rt_amount'];
			//$balance = $resq['balance_amount']-$resq['rt_balance'];
			
			$arr['quota'] = $quota;
			$arr['balance'] = $balance;
			return $arr;
		}
		
		public function doc_date(){
			$sql = "select doc_date from com_doc_date";
			$res = $this->db->FetchRow($sql);
			$arr = explode("-", $res['doc_date']);
			$date = $arr[2].'/'.$arr[1].'/'.$arr[0];
			return $date;
		}
		
		public function cProductId($product_id){
			/**
			 * @desc convert barcode to product_id
			 * @var $product_id = product_id	 or barcode
			 * @create 03112016
			 * @return product_id
			 */
			$found=FALSE;
			if($product_id!=''){
				
				if(substr($product_id,0,3)=='885' && strlen($product_id)==13){
					//assume product_id is barcode
					$sql_nb="SELECT product_id
					FROM com_product_master
					WHERE
					corporation_id='$this->corporation_id' AND
					company_id='$this->company_id' AND
					barcode='$product_id'";
					$row_nb=$this->db->fetchCol($sql_nb);
					if(count($row_nb)>0){
						$found=true;
						$product_id=$row_nb[0];
					}else{
						$sql_np="SELECT COUNT(*)
						FROM `com_product_master`
						WHERE
						corporation_id='$this->corporation_id' AND
						company_id='$this->company_id' AND
						product_id='$product_id'";
						$n_np=$this->db->fetchOne($sql_np);
						if($n_np>0){
							$found=true;
						}
					}
				}else{
					//out of case check barcode then check product_id
					$sql_np="SELECT COUNT(*)
					FROM `com_product_master`
					WHERE
					corporation_id='$this->corporation_id' AND
					company_id='$this->company_id' AND
					product_id='$product_id'";
					$n_np=$this->db->fetchOne($sql_np);
					if($n_np>0){
						$found=true;
					}
				}
			}//end if
			if($found){
				return $product_id;
			}else{
				return '';
			}
		}//func
		
		public function check_product($id,$doc_no){
			/**
			 * @desc
			 * @var $id = product_id
			 * @modify 22092016
			 */
			$tmp = substr($id, 0,2);
			if($tmp == "11" || $tmp == "12"){
				return "1";
			}else{				
				$sql = "select product_id 
						from com_product_master_tester 
						where product_id='$id' OR product_id ='ALL' ";
				$res = $this->db->FetchAll($sql);
				if(count($res) > 0){
					// เช็คห้ามีการคีย์ซ้ำ
					$tmp = $this->check_duplicate_pid($id,$doc_no);
					if($tmp == "1"){
						// รหัสสินค้าซ้ำ
						return "2";
					}else// มีรหัสสินค้า
					{
						return "1"; 
					}
				}else{//ไม่พบรหัสสินค้า
					return "0";
				}
			}
		}
		
		public function check_duplicate_pid($id,$doc_no){
			/**
			 * @des check duplicate by doc_no
			 * @var $id = product_id
			 * @var $doc_no = doc_no
			 * @modify 03112016
			 */
			$id=$this->cProductId($id);
			$sql = "SELECT product_id FROM trn_diary2_rt WHERE product_id='$id' AND  doc_no ='$doc_no'";
			$res = $this->db->FetchAll($sql);
			if(count($res) > 0){
				return "1";
			}else{
				return "0";
			}
		}
		
		public function check_productmas($id){
			  /**
			   * @desc แสดงข้อมูลการทำ TO ล่าสุด
			   * @var $id = product_id
			   * @modify 03112016 convert barcode to product_id
			   */
			    $id=$this->cProductId($id);
				$arr = array();
				$sql = "SELECT product_id FROM com_product_master  WHERE product_id='$id'";
				$res = $this->db->FetchAll($sql);
				if(count($res) > 0){
					$tmp = $this->check_doc_date($id);
					if($tmp == ""){
						$tmp = date('Y-m-d');
					}
					$qty = $this->sum_qty($id,$tmp);
					
					$arr['summary_sales']=$this->getSummarySales($id, $tmp);
					
					$arr['status'] 	= 	"1";
					$arr['doc']	   	=	$tmp;
					$arr['qty']		=	$qty;
					$arr['product_id']=$id;
				}else{
					$arr['status'] 	= 	"0";
					$arr['doc']	   	=	"";
					$arr['qty']		=	"";
					$arr['product_id']="";
					$i++;
				}
				return $arr;
		}
		
		
		public function check_qty($productid,$qty){
			/**
			 * @desc
			 * @var unknown
			 * @modify 22092016
			 */
			$sqlqtytester = "
					select * 
					from 
						com_product_master_tester 
					where 
					(product_id ='$productid' OR product_id='ALL')  and qty >= '$qty'";
			$restest = $this->db->FetchAll($sqlqtytester);
			if(count($restest) > 0){
				$arr_date=explode('-',$this->doc_date);
				$m=$arr_date[1];
				$y=intval($arr_date[0]);
				
// 				$m = (int)date('m');
// 				$y = date('Y');
				$sql = "select * 
						from 
							com_stock_master 
						where 
						product_id ='$productid' and onhand >= '$qty' and month='$m' and year='$y'";
				$res = $this->db->FetchAll($sql);
				if(count($res) > 0){
					return "1";
				}else{
					return "0";
				}
			}else{
				//จำนวนไม่พอ
				return "2";
			}
		}//func
		
		private function check_doc_date($productid){
			/**
			 * 
			 * @ตรวจสอบว่าสินค้าตัวนี้เคยทำ TO มาล่าสุดวันที่เท่าไหร่
			 * @modify 27102016
			 */
			$row = 1;
			$sql = "select doc_date 
					from trn_diary2 
					where 
						product_id ='$productid' 
						and doc_tp='TO' and status_no='25'
					order by id desc";
			$res = $this->db->FetchRow($sql);
			if(count($res) > 0){
				return $res['doc_date'];
			}else{
				if($row == 1){
					$productid = "11".$productid;
					$this->check_doc_date($productid);
				}else if($row== 2){
					$productid = "12".$productid;
					$this->check_doc_date($productid);
				}
				$i++;
				return date('Y-m-d');
			}
		}//func
				
		public function getSummarySales($productid,$doc_date){
			/**
			 * @desc
			 * @var $productid
			 * @var $mp date check
			 */
			$date_chk=date('Y-m-d', strtotime("$doc_date first day of last month"));	
			$sql ="
			select sum(amount) as summary_sales 
			from trn_diary2
			where
			product_id='$productid' and (doc_tp='SL' || doc_tp='VT')
			and doc_date >= '$date_chk'";
			$res = $this->db->FetchRow($sql);
			return number_format($res['summary_sales'],2);
		}//func
		
		public function sum_qty($productid,$tmp){
			$sql ="
				select sum(quantity) as qty
				from trn_diary2 
				where 
						product_id='$productid' and (doc_tp='SL' || doc_tp='VT')
				and doc_date >= '$tmp'
			";
			$res = $this->db->FetchRow($sql);
			return number_format($res['qty'],2);
		}//func
		
		public function checkstatus($status,$status_trans){
			/***
			 * @desc
			 * @modify 27102016
			 */
			if($status == "" || $status_trans==""){
				$sql = "SELECT * 
						FROM com_order_quota_tester t1 
							INNER JOIN com_doc_date t2 ON t1.company_id = t2.company_id
						WHERE t1.branch_id='$this->branch_id' AND t1.month <= month(t2.doc_date) AND 
							  t1.year <= year(t2.doc_date)";
				$res = $this->db->FetchAll($sql);
				if(count($res) > 0){
					return "1";
				}else{
					return "0";
				}
		   }else{
		   		return "2";
		   }
		}//func
		
		public function saveD2RT($doc_no,$product_id,$quantity,$quota,$sum){
			/**
			 * @desc
			 * @create 30112016
			 */
			if($doc_no ==""){
				//*WR17022017
				$arrDate=explode("-",$this->doc_date);
				$m=$arrDate[1];
				$d=$arrDate[2];
				$y=$arrDate[0];				
				$sqlolddoc = "SELECT COUNT(*) as n FROM trn_diary1_rt
				WHERE branch_id='$this->branch_id' AND  status_transfer='' AND YEAR(doc_date)='$y' AND MONTH(doc_date)='$m'";
				//$sqlolddoc = "SELECT COUNT(*) as n FROM trn_diary1_rt WHERE branch_id='$this->branch_id' AND  status_transfer=''";
				$n_olddoc = $this->db->FetchOne($sqlolddoc);
				if($n_olddoc > 0){
					echo "n";//มีเอกสารที่รอการอนุมัติให้ทำรายการนั้นก่อน
					exit();
				}
			}//end if
			
			$sqlpro = "SELECT price 	FROM com_product_master 	WHERE product_id ='$product_id'";
			$respro = $this->db->FetchRow($sqlpro);
			$price=$respro['price'];
			$amount=$quantity*$price;
			$net_amt=$amount;
			if($net_amt < $quota){
				$seq=1;
				if($doc_no !=""){
					$sqldocno = "SELECT balance_amount FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";
					$resdocno = $this->db->FetchRow($sqldocno);
					$balance = $resdocno['balance_amount'];
					$balance = $balance - $data['net_amt'];
					$sql_upd = "UPDATE com_order_quota_tester SET balance_amount ='$balance' WHERE branch_id='$this->branch_id'";
					$this->db->query($sql_upd);
				}
				//find max seq
				$sql_seq_max="SELECT MAX(seq) AS max_seq
				FROM trn_diary2_rt
				WHERE
				`corporation_id` ='$this->corporation_id' AND
				`company_id` ='$this->company_id' AND
				`branch_id` ='$this->branch_id' AND
				`doc_date` ='$this->doc_date'";
				$row_seq=$this->db->fetchCol($sql_seq_max);
				$seq=$row_seq[0]+1;
				
				$data['corporation_id'] = $this->corporation_id;
				$data['company_id']	= $this->company_id;
				$data['branch_id']	= $this->branch_id;
				$data['doc_date']		= $this->doc_date;
				$data['doc_time']		= date('H:i:s');
				$data['doc_tp']		=$this->doc_tp;
				$data['status_no']	=$this->status_no;
				$data['seq']			= $seq;
				$data['product_id']	= $product_id;
				$data['price']			= $price;
				$data['quantity']		= $quantity;
				$data['stock_st']		= '-1';
				$data['point1']		= $sum;
				$data['doc_no']		= $doc_no;
				$data['amount']		= $amount;
				$data['net_amt']		= $net_amt;
				$data['reg_date']		= date('Y-m-d');
				$data['reg_time']		= date('H:i:s');
				$data['reg_user']		= $this->user_id;		
				$data['upd_date']		= date('Y-m-d');
				$data['upd_time']		= date('H:i:s');
				$data['upd_user']		= $this->user_id;				
				$resin = $this->db->insert("trn_diary2_rt",$data);
				if($resin){
					echo "1";//บันทึกสำเร็จ
				}
				else {
					echo "0";//ไม่สามารถทำรายการได้
				}
			}else{
				echo "2";//ยอดเงินคงเหลือไม่พอในการทำรายการ
			}
			
		}//func
		
		public function savedata30112016($data,$amt){
			$data['doc_date']	=date('Y-m-d');
			$product_id = $data['product_id'];
			$total = 0;
			//check old doc_no 
			if($data['doc_no'] ==""){
				$sqlolddoc = "SELECT id FROM trn_diary1_rt WHERE branch_id='$this->branch_id' AND  status_transfer=''";
				$resold = $this->db->FetchAll($sqlolddoc);
				if(count($resold) > 0){
					echo "n";//มีเอกสารที่รอการอนุมัติให้ทำรายการนั้นก่อน
					exit();
				}
			}
			
			$sqlpro = "select price 
						from com_product_master 
						where product_id ='$product_id'";
			$respro = $this->db->FetchRow($sqlpro);
			$data['price']  	= $respro['price'];
			$data['amount']  	= $data['quantity'] * $respro['price'];
			$data['net_amt']  	= $data['quantity'] * $respro['price'];
			$total = $total + $data['net_amt'];
			if($total < $amt){
				if($data['doc_no'] !=""){
					$sqldocno = "SELECT balance_amount FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";
					$resdocno = $this->db->FetchRow($sqldocno);
					$balance = $resdocno['balance_amount'];
					$balance = $balance - $data['net_amt'];					
					$sql_upd = "UPDATE com_order_quota_tester SET balance_amount ='$balance' WHERE branch_id='$this->branch_id'";
					$this->db->query($sql_upd);
				}
				$resin = $this->db->insert("trn_diary2_rt",$data);
				if($resin){
					echo "1";//บันทึกสำเร็จ
				}
				else {
					echo "0";//ไม่สามารถทำรายการได้
				}
			}else{
				echo "2";//ยอดเงินคงเหลือไม่พอในการทำรายการ
			}
		}//func
		
		public function getdataproduct($page,$doc_no=''){
			$data = array();
			
			//*WR28102016 wait for modify ให้รู้ว่า $doc_no นี้ถูก confirm ไปหรือยัง
			/*
			if($doc_no!=''){
				$sql_chkconfirm="SELECT COUNT(*) AS num_chk FROM trn_diary1_rt 
										WHERE doc_no='$doc_no' AND status_transfer='T'";
				$row_chkconfirm = $this->db->FetchAll($sql_chkconfirm);
				if(empty($row_chkconfirm)){
					$wheres = "doc_no='$doc_no'";
				}else{
					$wheres ="doc_no=''";
				}
			}else{
				$wheres ="doc_no=''";
			}
			*/
			
			
			if($doc_no != "") $wheres = "doc_no='$doc_no'";
			else $wheres ="doc_no=''";
			$sql = "select *
					from trn_diary2_rt
					where $wheres";
			$res = $this->db->FetchAll($sql);
			$page=$page;
			$total=count($res);
			if(count($res) > 0){
				$data['page'] = $page;
				$data['total'] = $total;
				$data['rows'] = array();
				$i=1;
				foreach($res as $val){
					$data['rows'][] = array(
							'id' => $val['id'],
							'cell' => array(
									$i,
									$val['product_id'], 
									$this->getproduct($val['product_id']), 
									number_format($val['price'],2),
									number_format($val['quantity'],2) , 
									number_format($val['amount'],2), 
									$val['doc_date'],
									$val['point1']
							)
					);
					$i++;
				}
			}else{
				$data = array();
				$data['page'] = $page;
				$data['total'] = $total;
				$data['rows'] = array();
			}
			//print_r($data);
			return $data;
		}//func
		
		public function getproduct($product_id){
			$sql = "select name_product from com_product_master where product_id='$product_id'";
			$res = $this->db->FetchRow($sql);
			return $res['name_product'];
		}//func
		
		public function showamount27102016($doc_no){
			$arr = array();
			if($doc_no != ""){
				$wheres = "doc_no='$doc_no'";
				$remark = $this->remark($doc_no);
			}else{
				$remark = "";
				$wheres ="doc_no=''";
			}
			$sql = "select
			SUM(amount) as amount,
			SUM(quantity) as qty
			from trn_diary2_rt
			where $wheres";
			$res = $this->db->FetchRow($sql);
			if(count($res)){
				$arr['quota']	=$this->quota();
				$arr['am']		=number_format($res['amount'],2);
				$arr['amt']		=$this->quota();
				$arr['qty']		=number_format($res['qty'],2);
				$arr['remark']	=$remark;
			}
			return $arr;
		}//func
		
		public function showamount($doc_no){
			/**
			 * @desc
			 * @var $doc_no
			 * @modify 27102016
			 */
			$arr = array();
			if($doc_no != ""){
				$wheres = "doc_no='$doc_no'";
				$remark = $this->remark($doc_no);
			}else{
				$remark = "";
				$wheres ="doc_no=''";
			}
			$sql = "select 
						SUM(amount) as amount,
						SUM(quantity) as qty 
					from trn_diary2_rt 
					where $wheres";
			$res = $this->db->FetchRow($sql);
			if(count($res)){
				$arr['quota']	=$this->quota();
				$arr['am']		=number_format($res['amount'],2);
				$arr['amt']		=$this->amount();
				$arr['qty']		=number_format($res['qty'],2);
				$arr['remark']	=$remark;
			}
			return $arr;
		}//func
		
		public function amount(){
			/**
			 * @desc
			 * @create 27102016
			 */
			$sqlcount = "SELECT SUM(amount) as amount,SUM(quantity) as qty  FROM trn_diary2_rt  WHERE doc_no=''";
			$rescount = $this->db->FetchRow($sqlcount);
			$sql = "SELECT * FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";
			$res = $this->db->FetchRow($sql);
			if(count($rescount) == 0){	
				$total = $res['balance_amount']-$res['rt_amount'];
				return  $total;
			}else{
				return $res['balance_amount']-$rescount['amount'];
			}			
		}//func
		
		public function quota(){
			/**
			 * @desc 
			 * @create 27102016
			 */
			$sql = "SELECT * FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";	
			$res = $this->db->fetchAll($sql);			
			if(empty($res)){				
				return  0;
			}else{				
				return $res[0]['month_amount'];
			}				
		}//func
		
		private function remark($doc_no){
			$sql = "select remark1 from trn_diary1_rt where doc_no='$doc_no'";
			$res = $this->db->FetchRow($sql);
			if(count($res) > 0){
				return $res['remark1'];
			}else{
				return "";
			}
		}//func
		
		public function savediary1($data,$quota,$str_doc_no){
			/**
			 * @desc
			 * @var Array $data
			 * @var Int $quota
			 * @var String $str_doc_no
			 * @return
			 */
			$branch_id  = $data['branch_id'];
			$sqldoc_no = "SELECT doc_no FROM com_doc_no WHERE doc_tp ='RT' AND branch_id='$branch_id'";
			$resdoc 	=$this->db->FetchRow($sqldoc_no);
			$num_doc   	=$resdoc['doc_no'];
			$tmp_docno 	=str_pad($num_doc, 8, "0", STR_PAD_LEFT);
			$doc_no = $this->company_id.$branch_id.'RT-'.'01'.'-'.$tmp_docno;			
			// insert diarty1 rt				
			if($str_doc_no == ""){
				$data['doc_date']	= $this->doc_date;
				$data['doc_time']	=date('H:i:s');
				$data['doc_no']	=$doc_no;
				$data['doc_tp']	='RT';
				$data['status_no']=25;
				$data['reg_date']	=date('Y-m-d');
				$data['reg_time']	=date('H:i:s');
				$data['upd_date']	=date('Y-m-d');
				$data['upd_time']	=date("H:i:s");
				$sqlcount = "SELECT * FROM trn_diary2_rt WHERE doc_no =''";
				$rescount = $this->db->FetchAll($sqlcount);
				if(count($rescount) > 0){					
					$resin = $this->db->insert("trn_diary1_rt",$data);
					if($resin){
						//update quota tester				
						$sqlupqo = "UPDATE com_order_quota_tester 
											SET balance_amount ='$quota' 
												WHERE branch_id='$this->branch_id'";
						$resqo  = $this->db->query($sqlupqo);				
						//update count doc_no
						$num_doc +=1;
						$sqlupdoc = "UPDATE com_doc_no SET doc_no='$num_doc' WHERE branch_id='$branch_id' AND doc_tp ='RT'";
						$resdoc = $this->db->query($sqlupdoc);						
						//seq
				        $sqlrow = "select id from trn_diary2_rt where doc_no='' ORDER BY id ASC";
				        $resrow = $this->db->FetchAll($sqlrow);
				        if(count($resrow) > 0){
				        	$i = 1;
				        	foreach ($resrow as $index){
								$sqlup ="UPDATE trn_diary2_rt SET doc_no='$doc_no',seq='$i' where doc_no ='' and id='{$index['id']}'";
								$resup = $this->db->query($sqlup);
								$i++;
				        	}
				        }
						if($resup){
							echo "1";
						}else{
							echo "0";
						}
					}
				}else{
					echo "2";
				}
			}else{				
				$sqlup ="UPDATE trn_diary2_rt SET doc_no='$str_doc_no' where doc_no =''";
				$resup = $this->db->query($sqlup);
				if($resup){
					//get doc no diary1
					$sqlupqty 	= "update trn_diary1_rt set quantity = '{$data['quantity']}', net_amt = '{$data['quantity']}' where doc_no='$str_doc_no'";
					$ses =$this->db->query($sqlupqty);
					if($ses){
						echo "1";
					}else echo "0";
				}else{
					echo "0";
				}
			}			
		}//func
		
		public function deldiary2($str){
			/**
			 * 
			 * @var unknown
			 */
			$str = substr($str, 0,-1);
			//check docno --- not null
			$sqlcheck = "SELECT doc_no,net_amt FROM trn_diary2_rt WHERE id in($str)";
			$resdoc = $this->db->FetchAll($sqlcheck);
			foreach ($resdoc as $index){
				if($index['doc_no'] != ""){
					$sqlupdate = "SELECT balance_amount FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";
					$resbalance = $this->db->FetchRow($sqlupdate);
					$balance = $resbalance['balance_amount']+$index['net_amt'];					
					//$this->db->update("com_order_quota_tester",array("balance_amount"=>$balance));
					//*WR27102016
					$sql_upd = "UPDATE com_order_quota_tester SET balance_amount ='$balance' WHERE branch_id='$this->branch_id'";
					$this->db->query($sql_upd);					
				}
			}//foreach			
			$sqldel = "DELETE FROM trn_diary2_rt where id in($str)";
			$res = $this->db->query($sqldel);
			if($res)
				echo "1";
			else echo "0";
		}//func
		
		public function canceldiary2($doc_no=''){
			/**
			 * 
			 * @var unknown
			 */
			$sqlcount = "select doc_no,net_amt from trn_diary2_rt where doc_no ='$doc_no'";
			$rescount = $this->db->FetchAll($sqlcount);
			if(count($rescount) >0){
				//update balance
				if($doc_no != ""){
					$sqldoc = "select sum(net_amt) as amt from trn_diary2_rt where doc_no='$doc_no'";
					$resdoc = $this->db->FetchRow($sqldoc);
					$sqlbl 	= "SELECT balance_amount FROM  com_order_quota_tester WHERE branch_id='$this->branch_id'";
					$resbl 	= $this->db->FetchRow($sqlbl);
					$balance = $resbl['balance_amount'] + $resdoc['amt'];					
					//$this->db->update("com_order_quota_tester",array("balance_amount"=>$balance));
					//*WR27102016
					$sql_upd = "UPDATE com_order_quota_tester SET balance_amount ='$balance' WHERE branch_id='$this->branch_id'";
					$this->db->query($sql_upd);
				}				
				$sql = "delete from trn_diary2_rt where doc_no='$doc_no'";
				$res = $this->db->query($sql);
				if($res){
					echo "1";
				}else{
					echo "0";
				}
			}else{
				echo "2";
			}
			
		}//func		
	}//class
?>