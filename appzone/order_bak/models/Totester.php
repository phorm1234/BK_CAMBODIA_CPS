<?php 
	class Model_Totester extends Model_Tester{
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
			parent::__construct();
		}
		
		public function listDocno(){
			$arrDate=explode("-",$this->doc_date);
			$m=$arrDate[1];
			$d=$arrDate[2];
			$y=$arrDate[0];
			$str = "<option value=\"\">**เลือกเลขที่เอกสาร**</option>";
			$sql = "SELECT
						id,
						doc_no,
						status_transfer 
					FROM
						trn_diary1_rt
					WHERE 
						status_transfer<>'T' AND 
						YEAR(doc_date)='$y' AND MONTH(doc_date)='$m' 
					order by id asc
					";
			$res = $this->db->FetchAll($sql);
			if(count($res) > 0){
				foreach ($res as $index){
					$str .= '<option value="'.$index['doc_no'].','.$index['status_transfer'].'">'.$index['doc_no'].'</option>';
				}
			}	
			return $str;
		}//func
		
// 		public function check_order_tester(){
// 			$sql = "select * 
// 					from com_order_quata_tester t1 
// 					INNER JOIN com_doc_date t2 ON t1.company_id = t2.company_id 
// 					where t1.month <= month(t2.doc_date) and
// 						  t1.year <= year(t2.doc_date)
// 					";
// 			if(count($this->db->FetchAll($sql) > 0)){
// 				return "1";  //ทำงานไม่ได้
// 			}else{
// 				return "0";  //ทำงานได้
// 			}
// 		}
		
// 		public function check_quota(){
// 			// check quota

// 			$sqlq = "select * from com_order_quota_tester";
// 			$resq = $this->db->FetchRow($sqlq);
// 			$quota = $resq['balance_amount']-$resq['rt_amount'];
// 			$balance = $resq['balance_amount']-$resq['rt_balance'];
// 			$arr['quota'] = $quota;
// 			$arr['balance'] = $balance;
// 			return $arr;
// 		}
		
		
		
// 		public function doc_date(){
// 			$sql = "select doc_date from com_doc_date";
// 			$res = $this->db->FetchRow($sql);
// 			$arr = explode("-", $res['doc_date']);
// 			$date = $arr[2].'/'.$arr[1].'/'.$arr[0];
// 			return $date;
// 		}
		
// 		public function check_product($id){
// 			$tmp = substr($id, 0,2);
// 			if($tmp == "11" || $tmp == "12"){
// 				return "1";
// 			}else{
// 				$sql = "select product_id 
// 						from com_product_master_tester 
// 						where product_id='$id'";
// 				$res = $this->db->FetchAll($sql);
// 				if(count($res) > 0){
// 					return "1";
// 				}else{
// 					return "0";
// 				}
// 			}
// 		}
		
// 		public function check_productmas($id){
// 				$arr = array();
// 				$sql = "select product_id 
// 						from com_product_master_tester 
// 						where product_id='$id'";
// 				$res = $this->db->FetchAll($sql);
// 				if(count($res) > 0){
// 					$tmp = $this->check_doc_date($id);
// 					$qty = $this->sum_qty($id,$tmp);
// 					$arr['status'] 	= 	"1";
// 					$arr['doc']	   	=	$tmp;
// 					$arr['qty']		=	$qty;
// 				}else{
// 					$arr['status'] 	= 	"0";
// 					$arr['doc']	   	=	"";
// 					$arr['qty']		=	"";
// 				}
// 				return $arr;
// 		}
		
// 		public function check_qty($productid,$qty){
// 			$sql = "select * 
// 					from 
// 						com_stock_master 
// 					where 
// 					product_id ='$productid' and onhand >= '$qty'";
// 			$res = $this->db->FetchAll($sql);
// 			if(count($res) > 0){
// 				return "1";
// 			}else{
// 				return "0";
// 			}
// 		}
		
// 		private function check_doc_date($productid){
// 			$sql = "select doc_date 
// 					from trn_diary2 
// 					where 
// 						product_id ='$productid' 
// 						and doc_tp='TO' and status_no='25'
// 					order by id desc";
// 			$res = $this->db->FetchRow($sql);
// 			if(count($res) > 0){
// 				return $res['doc_date'];
// 			}else{
// 				return "";
// 			}
// 		}
		
// 		private function sum_qty($productid,$tmp){
// 			$sql ="
// 				select sum(quantity) as qty
// 				from trn_diary2 
// 				where 
// 						product_id='$productid' and (doc_tp='SL' || doc_tp='VT')
// 				and doc_date >= '$tmp'
// 			";
// 			$res = $this->db->FetchRow($sql);
// 			return number_format($res['qty'],2);
// 		}
		
// 		public function checkstatus($status,$status_trans){
// 			if($status == "" || $status_trans==""){
// 				$sql = "select * 
// 						from com_order_quota_tester t1 
// 							INNER JOIN com_doc_date t2 ON t1.company_id = t2.company_id
// 						where t1.month <= month(t2.doc_date) and 
// 							  t1.year <= year(t2.doc_date)";
// 				$res = $this->db->FetchAll($sql);
// 				if(count($res) > 0){
// 					return "1";
// 				}else{
// 					return "0";
// 				}
// 		   }else{
// 		   		return "2";
// 		   }
// 		}
		
		public function savedata($data){
			$data['doc_date']	=date('Y-m-d');
			$product_id = $data['product_id'];
			$sqlpro = "select price 
						from com_product_master 
						where product_id ='$product_id'";
			$respro = $this->db->FetchRow($sqlpro);
			$data['price']  	= $respro['price'];
			$data['amount']  	= $data['quantity'] * $respro['price'];
			$data['net_amt']  	= $data['quantity'] * $respro['price'];
			
			//print_r($data);
			$resin = $this->db->insert("trn_diary2_rt",$data);
			if($resin) echo "1";
			else echo "0";
		}
		
		public function getdataproduct($page,$doc_no=""){
			$data 	= array();
			$tmp 	= explode("=", $doc_no);
			$doc_no = $tmp[0];
			//print_r($tmp);
			$sql = "select *
					from trn_diary2_rt
					where doc_no='$doc_no' and doc_no <> ''
					Order by id DESC";
			$res = $this->db->FetchAll($sql);
			$page=$page;
			$total=count($res);
			if(count($res) > 0){
				$data['page'] = $page;
				$data['total'] = $total;
				$data['rows'] = array();
				$i=1;
				foreach($res as $val){
					if(@$tmp[1] == "s_all"){
						$s_all = '<input type="checkbox" id="s_all" 
									checked="checked" name="rowid'.$val['id'].'"
									value="'.$val['id'].'" onclick="showid('.$val['id'].',1);">';
						$this->approve($val['id'], 1);
					}else {
						$s_all = '<input name="rowid'.$val['id'].'" type="checkbox" id="s_all" onclick="showid('.$val['id'].',2);">';
						$this->approve($val['id'], '');
					}
					$data['rows'][] = array(
							'id' => $val['id'],
							'cell' => array(
									$i,
									$val['product_id'], 
									//$this->getproduct($val['product_id']), 
									parent::getproduct($val['product_id']),
									number_format($val['price'],2),
									number_format($val['quantity'],2) , 
									number_format($val['amount'],2), 
									$val['doc_date'],
									$val['point1'],
									$s_all
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
		}
		
// 		private function getproduct($product_id){
// 			$sql = "select name_product from com_product_master where product_id='$product_id'";
// 			$res = $this->db->FetchRow($sql);
// 			return $res['name_product'];
// 		}
		
		public function showamount($doc_no=""){
			/**
			 * @desc
			 * @var $doc_no
			 * @modify 27102016
			 */
			$objTest=new Model_Tester();			
			$arr = array();
			if($doc_no != ""){				
				$remark = $this->remark($doc_no);
				$sql = "select
				SUM(amount) as amount,
				SUM(quantity) as qty
				from trn_diary2_rt
				where doc_no='$doc_no'";
				$res = $this->db->FetchRow($sql);
				if(count($res)){
					$arr['quota']	=$objTest->quota();
					$arr['am']		=number_format($res['amount'],2);
					$arr['amt']		=$objTest->amount();
					$arr['qty']		=number_format($res['qty'],2);
					$arr['remark']	=$remark;
				}
			}else{
				$remark = "";
				$arr['quota']	=$objTest->quota();
				$arr['am']		="0.00";
				$arr['amt']		="0.00";
				$arr['qty']		="0.00";
				$arr['remark']	=$remark;
			}
			return $arr;
		}//functin 
		
// 		public function quota(){
// 			$sqlcount = "select 
// 							SUM(amount) as amount,
// 							SUM(quantity) as qty  
// 					     from trn_diary2_rt 
// 						 where doc_no=''";
// 			$rescount = $this->db->FetchRow($sqlcount);
// 			$sql = "select * from com_order_quota_tester";
// 			$res = $this->db->FetchRow($sql);
// 			if(count($rescount) == 0){	
// 				$total = $res['balance_amount']-$res['rt_amount'];
// 				return  $total;
// 			}else{
// 				return $res['balance_amount']-$rescount['amount'];
// 			}
			
// 		}
		
		private function remark($doc_no){
			$sql = "select remark1 from trn_diary1_rt where doc_no='$doc_no'";
			$res = $this->db->FetchRow($sql);
			if(count($res) > 0){
				return $res['remark1'];
			}else{
				return "";
			}
		}
		
		public function savediary1($data,$quota,$str_doc_no){
			$branch_id  = $data['branch_id'];
			$sqldoc_no = "select doc_no from com_doc_no where doc_tp ='RT' and branch_id='$branch_id'";
			$resdoc 	=$this->db->FetchRow($sqldoc_no);
			$num_doc   	=$resdoc['doc_no'];
			$tmp_docno 	=str_pad($num_doc, 8, "0", STR_PAD_LEFT);
			$doc_no = $branch_id.'RT-'.$branch_id.'-'.$tmp_docno;
			
			// insert diarty1 rt
			
			
			if($str_doc_no == ""){
				$data['doc_date']				=date('Y-m-d');
				$data['doc_time']				=date('H:i:s');
				$data['doc_no']					=$doc_no;
				$data['doc_tp']					='RT';
				$data['status_no']				=$num_doc;
				$data['reg_date']				=date('Y-m-d');
				$data['reg_time']				=date('H:i:s');
				$data['upd_date']				=date('Y-m-d');
				$data['upd_time']				=date("H:i:s");
				$sqlcount = "select * from trn_diary2_rt where doc_no =''";
				$rescount = $this->db->FetchAll($sqlcount);
				if(count($rescount) > 0){
					$resin = $this->db->insert("trn_diary1_rt",$data);
					if($resin){
						//update quota tester
				
						$sqlupqo = "update com_order_quota_tester set balance_amount ='$quota'";
						$resqo  = $this->db->query($sqlupqo);
				
						//update count doc_no
						$num_doc +=1;
						$sqlupdoc = "UPDATE com_doc_no SET doc_no='$num_doc' where branch_id='$branch_id' and doc_tp ='RT' ";
						$resdoc = $this->db->query($sqlupdoc);
				
						$sqlup ="UPDATE trn_diary2_rt SET doc_no='$doc_no' where doc_no =''";
						$resup = $this->db->query($sqlup);
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
					echo "1";
				}else{
					echo "0";
				}
			}
			
		}
		
		public function deldiary2($str){
			$str = substr($str, 0,-1);
			$sqldel = "DELETE FROM trn_diary2_rt where id in($str)";
			$res = $this->db->query($sqldel);
			if($res)
				echo "1";
			else echo "0";
		}
		
		public function canceldiary2(){
			$sqlcount = "select doc_no from trn_diary2_rt where doc_no =''";
			$rescount = $this->db->FetchAll($sqlcount);
			if(count($rescount) >0){
				$sql = "delete from trn_diary2_rt where doc_no=''";
				$res = $this->db->query($sql);
				if($res){
					echo "1";
				}else{
					echo "0";
				}
			}else{
				echo "2";
			}
			
		}
		
		public function approve($id,$status=''){
			if($status == 1){
				$sql = "UPDATE trn_diary2_rt SET flag='1' where id='$id'";
			}else{
				$sql = "UPDATE trn_diary2_rt SET flag='' where id='$id'";
			}
			$res = $this->db->query($sql);
			if($res)
					return "1";
			else 	return "0";
		}
		
		public function chkSelListItem($doc_no){
			/**
			 * @WR 20012017
			 */
			$sql_chklist="SELECT COUNT(*) FROM trn_diary2_rt WHERE doc_no ='$doc_no' AND flag='1'";
			$n_chklist  = $this->db->fetchOne($sql_chklist);
			if($n_chklist<1){
				return '1';
			}else{
				return '0';
			}
		}//func
		
		public function movetodiary1rq($doc_no,$rosid){
			/**
			 * @modify 20012017
			 * @var unknown
			 */
			$sqldiary1rt  = "select * from trn_diary1_rt where doc_no='$doc_no'";
			$res = $this->db->FetchRow($sqldiary1rt);
			//count qty
			$sqldiary2rt = "select count(*) as num from trn_diary2_rt where doc_no ='$doc_no' and flag='1'";
			$res2rt2  = $this->db->FetchRow($sqldiary2rt);
			try {
			if($res){
				
				$data = array(
					"corporation_id"=>$res['corporation_id'],
					"company_id"=>$res['company_id'],
					"branch_id"=>$res['branch_id'],
					"doc_date"=>$res['doc_date'],
					"doc_time"=>$res['doc_time'],
					"doc_no"=>$res['doc_no'],
					"status_transfer"=>'Y',
					"doc_tp"=>$res['doc_tp'],
					"status_no"=>25,
					"refer_doc_no"=>$res['refer_doc_no'],
					"quantity"=>$res2rt2['num'],
					"amount"=>$res['amount'],
					"net_amt"=>$res['amount'],
					"remark1"=>$res['remark1'],
					"reg_date"=>date('Y-m-d'),
					"reg_time"=>date('H:i:s'),
					"reg_user"=>$this->user_id,
					"user_id"=>$rosid,
					"upd_date"=>date('Y-m-d'),
					"upd_time"=>date('H:i:s'),
					"upd_user"=>$this->user_id
				);
				$resin = $this->db->insert("trn_diary1_rq",$data);
				if($resin){
					$sqldiary2rt = "select * from trn_diary2_rt where doc_no ='$doc_no' and flag='1'";
					$res2rt  = $this->db->FetchAll($sqldiary2rt);
					$i = 1;
					foreach ($res2rt as $index){
						$datain = array(
								"corporation_id"=>$index['corporation_id'],
								"company_id"=>$index['company_id'],
								"branch_id"=>$index['branch_id'],
								"doc_time"=>$index['doc_time'],
								"doc_no"=>$index['doc_no'],
								"doc_tp"=>$index['doc_tp'],
								"status_no"=>25,
								"seq"=>$i,
								"product_id"=>$index['product_id'],
								"price"=>$index['price'],
								"quantity"=>$index['quantity'],
								"stock_st"=>"0",
								"amount"=>$index['amount'],
								"net_amt"=>$index['net_amt'],
								"point1"=>$index['point1'],
								"reg_date"=>date('Y-m-d'),
								"reg_time"=>date('H:i:s'),
								"reg_user"=>$this->user_id,
								"upd_date"=>date('Y-m-d'),
								"upd_time"=>date('H:i:s'),
								"upd_user"=>$this->user_id
						);
						$i++;
						$this->db->insert('trn_diary2_rq',$datain);
					}
				}
	
				$sqluprt1  = "UPDATE 
								trn_diary1_rt 
								SET 
									status_transfer='T',
									upd_date='".date('Y-m-d')."',
									upd_time ='".time()."',
									upd_user='".$this->user_id."'
							  WHERE doc_no ='$doc_no'";
				$this->db->query($sqluprt1);
				
				//update balance amount
				$sqlbalance  = "SELECT * FROM com_order_quota_tester WHERE branch_id='$this->branch_id'";
				$resbl = $this->db->FetchRow($sqlbalance);
				$month = $resbl['month'];
				$month +=1;
				$year = $resbl['year'];
				$balance = $resbl['month_amount']*($resbl['balance_amount']-$resbl['rt_amount']);
				if($month == 13 ){
					$year += 1;
					$month = 1;
				}
// 				$sqlup = "UPDATE com_order_quota_tester 
// 						  	SET balance_amount='$balance',month='$month',year='$year' 
// 							WHERE branch_id='$this->branch_id'";
// 				$resup = $this->db->query($sqlup); 
				$sqlup = "UPDATE com_order_quota_tester
				SET month='$month',year='$year'
				WHERE branch_id='$this->branch_id'";
				$resup = $this->db->query($sqlup);				
				return "1";
			}
		  }catch (Exception $e){
		  	  return "0";
		  }
		}
		
		public function cancel_to($doc_no){
			$sql = "UPDATE trn_diary1_rt SET status_transfer ='C' WHERE doc_no ='$doc_no'";
			$res = $this->db->query($sql);
			if($res){
				// คืนยอดเงิน
				$sql = "select amount  from trn_diary1_rt where doc_no='$doc_no'";
				$resqty = $this->db->FetchRow($sql);
				$sqlbl = "select balance_amount from com_order_quota_tester";
				$resbl = $this->db->FetchRow($sqlbl);
				$balance = $resbl['balance_amount'] + $resqty['amount'];
				$resup  = $this->db->update("com_order_quota_tester",array("balance_amount"=>$balance));
				if($resup)
					echo "1";
				else echo "0";
			}else{
				echo "0";
			}
		}
		
		public function check_cpass($user_id,$cpass){
			//echo $cpass;
			 $sql ="SELECT * 
				   FROM conf_employee  
				   WHERE 
					 (position='ROS' 
					 OR position='AROM') 
					 AND password_id='$cpass'";
			$res =$this->db->FetchAll($sql);
			if(count($res) > 0){
				$row = $this->db->FetchRow($sql);
				$sess_ros = new Zend_Session_Namespace('rosprofile');
				$sess_ros->rospass  =$row['employee_id'];
				return "y";
			}else{
				return "n";
			}
		}
	
	}
	
	
	
?>