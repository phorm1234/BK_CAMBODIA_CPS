<?php 
	class Model_Cashier extends Model_PosGlobal{
		private $member_no;		
		private $product_id;
		private $product_name;
		private $unit;
		private $point;
		private $quantity;
		private $price;
		private $amount;//จำนวนเงิน
		private $net_amt;//จำนวนเงินหลังหักส่วนลด
		private $status_no;
		private $product_status;
		private $curr_date;
		private $seq;
		private $get_point;
		private $type_discount;//ประเภทส่วนลด
		private $discount;//ส่วนลด promotion
		private $percent_discount;//ส่วนลดโปรโมชั่น %
		private $discount_member;//ให้ส่วนลดสมาชิกหรือไม่ Y or N
		private $member_discount1;//ส่วนลดสมาชิก1
		private $member_discount2;//ส่วนลดสมาชิก2	
		private $co_promo_discount;//ส่วนลดร่วมกับโปรโมชั่น
		private $coupon_discount;//ส่วนลดคูปอง
		private $special_discount;//ส่วนลดพิเศษ
		private $other_discount;//ส่วนลดอื่นๆ
		private $total_discount;
		private $discount_percent;
		private $member_percent1;
		private $member_percent2;
		private $co_promo_percent;
		private $coupon_percen;
		private $promo_code;		
		private $promo_seq;
		private $promo_st;//สถานะตัวสินค้าที่เล่น promotion เช่น S ตัวซื้อ P ตัวแถม
		private $promo_tp;//ประเภทโปรโมชั่น
		private $seq_pro;
		private $start_date;
		private $end_date;
		private $chk_trans;
		private $curr_doc_no;//เลขที่ running ปัจจุบันของตาราง doc_no
		private $tax_type;
		private $vat;
		
		public function __construct(){
			parent::__construct();			
			$this->doc_tp="SL";
			$this->curr_doc_no=0;
			$this->curr_date=date("Y-m-d");			
			if($this->doc_date){
				$arr_date=explode("-",$this->doc_date);
				$this->year=$arr_date[0];
				$this->month=intVal($arr_date[1]);
			}			
		}//func
		
		public function setOnlineMode($online_status){
			/**
			 * @desc modify:06052013
			 * @param Char $online_status 1:online,0:offline
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
			$sql_upd="UPDATE com_branch_computer SET online_status='$online_status'";
			@$this->db->query($sql_upd);
		}//func
		
		function chkQLimit($promo_code,$quantity){
			/**
			 * @des
			 * careate : 20012015
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$str_tbl=parent::countDiaryTemp();
			$arr_tbl=explode('#',$str_tbl);
			$tbl_name=$arr_tbl[1];
			
			$sql_proh="SELECT * 
							FROM promo_head 
							WHERE 
								promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_proh=$this->db->fetchAll($sql_proh);
			if(!empty($rows_proh)){
				$where="";
				if($promo_code=='OPPL300' || $promo_code=='OPMGMC300'){
					$where.=" AND seq>2";
				}
				//*WR20012015 check play limit quantity
				$unitn=$rows_proh[0]['unitn'];
				$sql_chk_limit="SELECT SUM(quantity) AS n_limit FROM $tbl_name 
											WHERE 
												`corporation_id`='$this->m_corporation_id' AND 
												`company_id`='$this->m_company_id'  AND
												`branch_id`='$this->m_branch_id' AND 
											    `computer_no` ='$this->m_computer_no' AND 
											    `computer_ip` ='$this->m_com_ip' AND 
											    `doc_date` ='$this->doc_date' AND
												`promo_code`='$promo_code' $where";
				$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
				$n_curr=$row_chk_limit[0]['n_limit'];
				$n_curr=$n_curr+$quantity;
				if($n_curr>$unitn){
					$msg="E_QTYLIMIT#ซื้อได้ไม่เกิน $unitn  ชิ้น กณาตรวจสอบอีกครั้ง";
					return $msg;
				}else{
					return "N#$n_curr";
				}
			}
			
		}//func
		
		public function chkFreeShortNew($doc_no,$member_no,$promo_code,$member_tp){
			/**
			 * @desc WR26022013
			 * @desc ปรับปรุงให้ใช้ได้กับทุกโปรที่มีการทำสินค้า Short
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			//check promo_short
			$sql_chk_play="SELECT * FROM promo_short WHERE promo_code='$promo_code'";
			$rec_chk_play=$this->db->fetchAll($sql_chk_play);
			if(!empty($rec_chk_play)){
				$doc_start_date=$rec_chk_play[0]['start_date'];
				$doc_end_date=$rec_chk_play[0]['end_date'];
				if($rec_chk_play[0]['play_promo_code']=='ALL'){
					//not check co_promotion
					//setShortToTemp($doc_no,$member_no,$promo_code);
				}else{
					//check co_promotion แสดงว่าระบุโปรโมชั่นที่เล่นด้วยเท่านั้น application_id ใน trn_diary1
					$arr_copromo=array();
					$i=0;
					$str_pro="";
					foreach($rec_chk_play as $data){
						$arr_copromo[$i]=$data['play_promo_code'];
						$i++;
						$str_pro.=$data['play_promo_code'];
					}
					//ได้ bill มาแล้วค่อยมา check อีกที
					//check diary1
					$sql_d1="SELECT * FROM trn_diary1 WHERE doc_no='$doc_no' AND doc_date BETWEEN '$doc_start_date' AND '$doc_end_date'";	
					$rec_d1=$this->db->fetchAll($sql_d1);
					if(!empty($rec_d1)){
						if($rec_d1[0]['flag']=='C'){
							return '12';
							exit();
						}
						if($member_tp=='Y' && $rec_d1[0]['member_id']!=$member_no){
							return '2';
							exit();
						}
						if($rec_chk_play[0]['check_amount']=='N'){
							if($rec_chk_play[0]['newcard']=='Y'){
								if($rec_d1[0]['status_no']=='01'){
									//case status_no = 01 บัตรใหม่
									//ทำรายการ pos เก่า => รับสินค้า short pos ใหม่
									$chk_old_doc=substr($doc_no,0,2);
									if($chk_old_doc != 'OP'){
										$sql_upd="UPDATE trn_diary1 AS a INNER JOIN trn_diary2 AS b ON (a.doc_no=b.doc_no)
													  SET a.application_id=b.promo_code
													  WHERE
													  	a.doc_no='$doc_no' AND a.application_id=''";
										$this->db->query($sql_upd);										
									}
									if(in_array($rec_d1[0]['application_id'],$arr_copromo)){																	
										//check current transaction ป้องกันไม่ให้มีการ insert ซ้ำ
										$sql_chk_trn="SELECT COUNT(*) FROM trn_tdiary2_sl 
															WHERE 
																`promo_code`='$promo_code' AND
																`computer_no`='$this->m_computer_no' AND
							 									`computer_ip`='$this->m_com_ip'";
										$n_chk_trn=$this->db->fetchOne($sql_chk_trn);
										if($n_chk_trn>0){
											// ไม่สามารถคีย์สินค้าซ้ำได้
											return '6';
										}else{
											$this->setShortToTemp($doc_no,$member_no,$promo_code);	
										}
									}else{
										// case ไม่พบโปรโมชั่นที่เล่นร่วม
										return '7';
									}
								}else{
									//case ไม่ใช่บิล 01 บัตรใหม่หรือบัตรต่ออายุ 
									return '8';
								}
							}else{
								//case ไม่ใช่บิล 01 บัตรใหม่หรือบัตรต่ออายุ comming soon
								return '8';
							}//end if case newcard
						}else{
							//case bill ทีมีการตรวจสอบยอดซื้อ comming soon
							return '9';
						}
					}else{
						//ไม่พบเลขที่เอกสาร หรือเลขที่เอกสารไม่อยู่ในช่วงที่กำหนด
						return '10';
					}
				}// end check copromotion
			}else{
				//case ที่ไม่พบโปรในตาราง promo_short
				return '11';
			}
			//--------------------------------- end new		
		}//func
		
		public function setShortToTemp($doc_no,$member_no,$promo_code){
			/**
			 * @desc
			 * @return
			 */
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			$sql_prod="SELECT* FROM promo_detail WHERE promo_code='$promo_code'";
			$arr_product=$this->db->fetchAll($sql_prod);
				
			foreach($arr_product as $data){
					$doc_tp='DN';						
					$employee_id=$this->user_id;				
					$product_id=$data['product_id'];
					$quantity='1';
					$status_no='00';
					$product_status='';
					$application_id=$promo_code;
					$card_status='';
					$get_point='N';
					$promo_id='';
					$discount_percent='';
					$member_percent1='';
					$member_percent2='';
					$co_promo_percent='';
					$coupon_percent='';
					$promo_st='';
					$promo_tp='F';
					$promo_amt='';
					$promo_amt_type='';
					$check_repeat='';
					$web_promo='';
					$res_set=$this->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
						$status_no,$product_status,$application_id,$card_status,
						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo);
				}//foreach
		}//func
		
		public function chkFreeShort($doc_no,$member_no,$promo_code){
			/**
			 * @desc WR10012013 ทำแก้ปัญหาเฉพาะหน้าใช้ครั้งเดียวถึง 2013-01-31
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			//check current transaction
			$sql_chk_trn="SELECT COUNT(*) FROM trn_tdiary2_sl 
								WHERE 
									`promo_code` IN('R_STORIES1','R_STORIES2') AND
									`computer_no`='$this->m_computer_no' AND
 									`computer_ip`='$this->m_com_ip'";
			$n_chk_trn=$this->db->fetchOne($sql_chk_trn);
			if($n_chk_trn>0){
				return '6';
			}
			
			// status_no 07 bill VT old pos
			$sql_chk="SELECT member_id,amount FROM trn_diary1 WHERE doc_no='$doc_no' AND doc_date>='2012-11-22' AND status_no IN('00','02','07')";
			$row_h=$this->db->fetchAll($sql_chk);			
			if(empty($row_h)){
				//ไม่พบ
				return '1';
			}else if($row_h[0]['member_id']!=$member_no){
				//member not math with doc
				return '2';
			}else if($row_h[0]['amount']<2000){
				//ยอดซื้อน้อยกว่า 2000 ไม่สามารถรับโปรนี้ได้
				return '4';
			}else{				
				$arr_product=array();				
				$amount=$row_h[0]['amount'];
				if($promo_code=='R_STORIES2' && $amount<3000){
					//ยอดซื้อน้อยกว่า 3000 ไม่สามารถเล่นโปรนี้ได้
					return '5';
				}
				
				if($promo_code=='R_STORIES1'){
					$sql_chkpro="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND  promo_code='OX08071112'";
					$n_chkpro=$this->db->fetchOne($sql_chkpro);
					if($n_chkpro>0){
						//รับสินค้าไปแล้ว
						return '3';
					}
					$arr_product[0]['product_id']='25295';
					$arr_product[1]['product_id']='25299';
				}
				
				if($promo_code=='R_STORIES2'){
					$sql_chkpro="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND  promo_code='OX08081112'";
					$n_chkpro=$this->db->fetchOne($sql_chkpro);
					if($n_chkpro>0){
						//รับสินค้าไปแล้ว
						return '3';
					}
					$arr_product[2]['product_id']='25300';
					$arr_product[3]['product_id']='25307';
				}				
				//check detail pay bill dn
				$sql_detail="SELECT* FROM trn_diary2 WHERE doc_no='$doc_no'";
				$rows_detail=$this->db->fetchAll($sql_detail);
				
				foreach($arr_product as $data){
						################################ start set ################################
						$doc_tp='DN';						
						$employee_id=$this->user_id;				
						$product_id=$data['product_id'];
						$quantity='1';
						$status_no='00';
						$product_status='';
						$application_id=$promo_code;
						$card_status='';
						$get_point='N';
						$promo_id='';
						$discount_percent='';
						$member_percent1='';
						$member_percent2='';
						$co_promo_percent='';
						$coupon_percent='';
						$promo_st='';
						$promo_tp='F';
						$promo_amt='';
						$promo_amt_type='';
						$check_repeat='';
						$web_promo='';
						$res_set=$this->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
							$status_no,$product_status,$application_id,$card_status,
							$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
							$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo);
						################################ end set ################################
					}
				
			}
		}//func
		
		public function addCoProduct($promo_play,$product_id,$member_no_ref){
			/**
			 * @desc ตรวจสอบสินค้านี้ร่วมโปรใดหรือไม่
			 * @desc บันทึกรายการสินค้าตามโปรโมชั่นร่วม โดยเลือก seq_pro ลำดับที่ 2
			 * @return
			 */
			$res_status='###';
			$product_id=parent:: getProduct($product_id,'1');
			if($product_id=='1' || $product_id=='2' || $product_id=='3'){
				//1 not found in com_product_master,2 not found in com_stock_master,3 product is lock
				$res_status='###'.$product_id;
			}else{
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
					$sql_coproduct="SELECT 
													 `promo_pos`, `promo_code`, `promo_tp`,
													 `status_pro`, `seq_pro`, `order_st`, `product_id`,
													  `round_date`, `show_promo`, `quantity`, `type_discount`, `discount`,
													  `promo_baht`, `promo_price`, `promo_over`, `check_double`, `cal_amt`,
													  `weight`, `doc_status`, `check_pro`, `get_point`, `discount_member`, `product_st`,
													  `balance_qty`, `seq_order`, `re_calculate`, `limite_qty`
												FROM 
													`promo_detail`
												WHERE 
													`corporation_id` ='$this->m_corporation_id' AND
													`company_id` ='$this->m_company_id' AND	
													`promo_code` = '$promo_play' AND 
													`product_id` = '$product_id' AND 
													`seq_pro` = '2' AND
													'$this->doc_date' BETWEEN start_date AND end_date";
					$row_detail=$this->db->fetchAll($sql_coproduct);
					if(count($row_detail)>0){
						//check price 
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
						$sql_chkprice="SELECT price FROM com_product_master WHERE product_id='$product_id'";
						$row_price=$this->db->fetchAll($sql_chkprice);
						$price=$row_price[0]['price'];
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
						$sql_maxseq="SELECT seq,price
											FROM trn_promotion_tmp1
											WHERE
												`corporation_id`='$this->m_corporation_id' AND 
												`company_id`='$this->m_company_id' AND
												`branch_id` ='$this->m_branch_id' AND
												`computer_no`='$this->m_computer_no' AND
 												`computer_ip`='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' 
											ORDER BY seq DESC LIMIT 0,1";
						$row_maxseq=$this->db->fetchAll($sql_maxseq);
						if($price<=$row_maxseq[0]['price']){
								$sql_product="";
								$doc_tp='SL';
								$promo_code=$promo_play;
								$promo_tp=$row_detail[0]['promo_tp'];
								$employee_id=$this->employee_id;
								$member_no=$member_no_ref;
								$product_id=$product_id;
								$quantity='1';
								$status_no='00';
								$product_status='';
								$application_id='';
								$card_status='';
								$get_point='Y';
								$promo_id='';
								$discount_percent=$row_detail[0]['discount'];//เปอร์เซนต์ส่วนลดโปรโมชั่น
								$member_percent1='';
								$member_percent2='';
								$co_promo_percent='';
								$coupon_percent='';
								$promo_st=$row_detail[0]['type_discount'];
								//call net_amt
								$net_amt='';
								$discount=0;
								$discount_member=$row_detail[0]['discount_member'];//ส่วนลดสมาชิก
								$check_repeat='N';
								$str_res_save=$this->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
														$status_no,$product_status,$application_id,$card_status,
														$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
														$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);
								$res_status=$str_res_save;
						}else{
							//สินค้าร่วมโปรราคามากว่าสินค้าปกติ
							$res_status='###4';
						}
					}else{
						//สินค้าไม่ร่วมโปรโมชั่น
						$res_status='###0';
					}
			}
			echo $res_status;
		}//func
		
		public function chkCopromo($promo_code,$product_id){
			/**
			 * @desc ตรวจสอบสินค้านี้ร่วมโปรใดหรือไม่
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
			$str_result='';
			$sql_copromo="SELECT promo_play
								FROM `promo_check`
								WHERE 
									`corporation_id` ='$this->m_corporation_id' AND
									`company_id` ='$this->m_company_id' AND	
									`promo_code` = '$promo_code' AND
									'$this->doc_date' BETWEEN start_date AND end_date";
			$row_promo=$this->db->fetchAll($sql_copromo);
			if(count($row_promo)>0){
				$promo_play=$row_promo[0]['promo_play'];
				$sql_coproduct="SELECT COUNT(*)
										FROM 
											`promo_detail`
										WHERE 
											`promo_code` = '$promo_play' AND 
											`product_id` = '$product_id' AND 
											`seq_pro` = '1' AND
											'$this->doc_date' BETWEEN start_date AND end_date";
				$n_co=$this->db->fetchOne($sql_coproduct);
				if($n_co>0){
					$str_result=$promo_play;
				}
			}
			return $str_result;
		}//func
		
		public function getProfileMemVT($member_id){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$row_p=array();
			if($member_id!=''){
				$sql_p="SELECT name,address1,address2,address3 
							FROM trn_diary1 
							WHERE member_id='$member_id' AND doc_tp='VT' ORDER BY doc_date DESC,doc_no DESC LIMIT 0,1";				
				$row_p=$this->db->fetchAll($sql_p);
			}
			return $row_p;
		}//func
		
		public function chkMorePointMoreVal($promo_code){
			/**
			 * @desc เอายอด net_amt ไปคิดส่วนลด more point
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			//find max seq หา net_amt ของรายการตัวล่าสุดก่อนเล่นโปร 50BTO1P
			$sql_max_seq="SELECT seq
								FROM trn_promotion_tmp1 
								WHERE
									  `corporation_id` ='$this->m_corporation_id' AND
									  `company_id` ='$this->m_company_id' AND									  
									  `branch_id` ='$this->m_branch_id' AND
									  `computer_no`='$this->m_computer_no' AND
 									  `computer_ip`='$this->m_com_ip'
								ORDER BY seq DESC LIMIT 0,1";
			$row_maxseq=$this->db->fetchAll($sql_max_seq);
			if(count($row_maxseq)>0){
				$max_seq=$row_maxseq[0]['seq'];				
			}else{
				$max_seq=0;				
			}
			//หา net_amt ของโปร 50BTO1P ตัวล่าสุดกรณีเริ่มเล่น
			$net_amt='';
			$sql_net="SELECT seq,net_amt 
							FROM trn_promotion_tmp1 
							WHERE
								  `corporation_id` ='$this->m_corporation_id' AND
								  `company_id` ='$this->m_company_id' AND
								  `branch_id` ='$this->m_branch_id' AND
								  `computer_no`='$this->m_computer_no' AND
 								  `computer_ip`='$this->m_com_ip' AND
								  `promo_code` ='$promo_code' 
							ORDER BY seq DESC LIMIT 0,1";
			$row_net=$this->db->fetchAll($sql_net);
			if(count($row_net)>0){
				if($row_net[0]['seq']>=$max_seq){ //แสดงว่าเป็นตัวแรกที่เริ่มเล่นโปร 50BTO1P ใช้ยอด net_amt ของตัวปัจจุบัน
					$net_amt=$row_net[0]['net_amt'];
				}else{
					//ใช้ยอด net_amt รวม
					////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//find max net amt out promo_code
					$over_seq=$row_net[0]['seq'];//เอา seq มากกว่าตัวมันมาคิดยอด net_amt
					$sql_max_netamt="SELECT SUM(net_amt) AS net_amt
										FROM trn_promotion_tmp1 
										WHERE
											  `corporation_id` ='$this->m_corporation_id' AND
											  `company_id` ='$this->m_company_id' AND
											  `branch_id` ='$this->m_branch_id' AND 
											  `computer_no`='$this->m_computer_no' AND
 									  		  `computer_ip`='$this->m_com_ip' AND
											  `seq`>='$over_seq'";
					$row_maxnetamt=$this->db->fetchAll($sql_max_netamt);
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
					$net_amt=$row_maxnetamt[0]['net_amt'];
				}
			}
			return $net_amt;
		}//func
		
		public function listCN(){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_cn="SELECT 
								doc_date,doc_no,doc_tp,amount,net_amt,member_id
						FROM
								trn_diary1
						WHERE
								  `corporation_id` ='$this->m_corporation_id' AND
								  `company_id` ='$this->m_company_id' AND
								  `branch_id` ='$this->m_branch_id' AND
								  `doc_date` ='$this->doc_date' AND
								  `doc_tp` ='CN' AND 
								  `flag` <>'C' AND
								  `status_no` <>'41' AND 
								  `cn_status` <>'Y'";	
			$rows_cn=$this->db->fetchAll($sql_cn);	
			return $rows_cn;
		}//func
		
		public function getPdtOfBill($product_id){
			/**
			 * @desc
			 * @return
			 */
			$product_id=parent::setBarcodeToProductID($product_id);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$sql_p="SELECT 
							doc_no,product_id,price,quantity,amount,flag
						FROM 
							trn_diary2
						WHERE 
							  `corporation_id` ='$this->m_corporation_id' AND
							  `company_id` ='$this->m_company_id' AND
							  `branch_id` ='$this->m_branch_id' AND
							  `doc_date` ='$this->doc_date' AND
							  `product_id` ='$product_id'
						 
						UNION ALL
						SELECT 
							doc_no,product_id,price,quantity,amount,flag
						FROM 
							trn_tdiary2_ck
						WHERE 
						  `corporation_id` ='$this->m_corporation_id' AND
						  `company_id` ='$this->m_company_id' AND
						  `branch_id` ='$this->m_branch_id' AND
						  `product_id` ='$product_id'";
			
			$rows_pdt=$this->db->fetchAll($sql_p);
			echo "<table width='100%' border='0' cellpadding='2' cellspacing='1' bgcolor='#cccccc'>
						<tr height='32' bgcolor='#669999'>
							<td align='center' width='5%'><span style='font-size:26px;color:#fff;'>สถานะ</span></td>
							<td align='center' width='20%'><span style='font-size:26px;color:#fff;'>เลขที่</span></td>
							<td align='center' width='13%'><span style='font-size:26px;color:#fff;'>รหัสสินค้า</span></td>
							<td align='center'><span style='font-size:26px;color:#fff;'>รายละเอียด</span></td>
							<td align='center' width='10%'><span style='font-size:26px;color:#fff;'>ราคา</span></td>
							<td align='center' width='5%'><span style='font-size:26px;color:#fff;'>จำนวน(ชิ้น)</span></td>
							<td align='center' width='15%'><span style='font-size:26px;color:#fff;'>จำนวนเงิน</span></td>
						</tr>";
			$i=1;
			$sum_amount=0.00;
			$sum_qty=0;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
			foreach($rows_pdt as $data){		
				($i%2==0)?$bg_color="#FFFFFF":$bg_color="#F4F4F4";
				$sql_pname="SELECT name_product FROM com_product_master WHERE product_id='$product_id'";
				$row_pname=$this->db->fetchCol($sql_pname);
				$product_name=$row_pname[0];
				echo "
						<tr bgcolor='$bg_color'>
							<td align='center'>".$data['flag']."</td>
							<td align='center'>".$data['doc_no']."</td>
							<td align='center'>".$data['product_id']."</td>
							<td align='left'>&nbsp;".$product_name."</td>
							<td align='center'>".number_format($data['price'],'2','.',',')."</td>
							<td align='center'>".intval($data['quantity'])."</td>
							<td align='center'>".number_format($data['amount'],'2','.',',')."</td>
						</tr>";
				$sum_amount+=$data['amount'];
				$sum_qty+=$data['quantity'];
				$i++;
			}
			echo "
				<tr height='32' bgcolor='#999999'>
						<td align='center'>&nbsp;</td>
						<td align='center'>&nbsp;</td>
						<td align='center'><span style='font-size:26px;color:#fff;'>Total</span></td>
						<td align='center'>&nbsp;</td>
						<td align='center'>&nbsp;</td>
						<td align='center'><span style='font-size:26px;color:#fff;'>".intval($sum_qty)."</span></td>
						<td align='center'><span style='font-size:26px;color:#fff;'>".number_format($sum_amount,'2','.',',')."</span></td>
					</tr>
				</table>";
		}//func
		
		public function getAO($status_no){
			/**
			 * @desc get and set bill 19 to temp
			 * @modify : 04092014
			 * @param String $status_no
			 * @return null
			 */
			$sql_ck="SELECT doc_tp,product_id,name,dif,price
							FROM chk_stock 
							WHERE 
								doc_tp='AO' AND 
								doc_date='$this->doc_date'
							ORDER BY product_id";			
			$row_ck=$this->db->fetchAll($sql_ck);		
			$i=1;
			$j=1;
			$this->stock_st='-1';
			foreach($row_ck as $data){
				$this->doc_tp=$data['doc_tp'];
				$this->status_no=$status_no;
				$this->product_id=$data['product_id'];
				$this->product_name=$data['name'];
				$this->quantity=$data['dif'];
				$this->price=$data['price'];//*WR04092014
				$this->amount=$data['dif']*$data['price'];				
				$this->unit='ชิ้น';
				$this->net_amt=$this->amount;
				$sql_insert="INSERT INTO trn_tdiary2_sl
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` =CURTIME(),
									  `doc_no` ='',
									  `doc_tp` ='$this->doc_tp',
									  `status_no`='$this->status_no',
									  `seq` ='$i',
									  `seq_set` ='$j',
									  `promo_code` ='',
									  `promo_seq` ='',
									  `promo_st` ='',
									  `promo_tp` ='',
									  `product_id`='$this->product_id',				
									  `name_product`='$this->product_name',			
									  `unit`='$this->unit',			  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='$this->price',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='',								  
									  `member_discount1`='',
									  `member_discount2`='',		  
									  `net_amt`='$this->net_amt',
									  `product_status`='',
									  `get_point`='N',
									  `discount_member`='N',
									  `cal_amt`='Y',											  
									  `discount_percent`='',
									  `member_percent1`='',
									  `member_percent2`='',
									  `co_promo_percent`='',
									  `coupon_percent`='',											  
									  `tax_type`='V',
									  `computer_no`='$this->m_computer_no',
 									  `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`=CURTIME(),
									  `reg_user`='$this->employee_id'";					
					$stmt_insert=$this->db->query($sql_insert);
					if($stmt_insert){
						parent::decreaseStock($this->product_id,$this->quantity);						
						$i++;
					}
			}//foreach
		}//func
		
		public function getPrevPromo($promo_code){
			/**
			 * @desc
			 */
			$tbl_temp=$this->selTblTemp();
			if($promo_code=='')
				return '';
			$sql_p="SELECT promo_code,status_no 
							FROM $tbl_temp
							WHERE
									`corporation_id`='$this->m_corporation_id' AND 
									`company_id`='$this->m_company_id' AND
									`branch_id` ='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
 									`computer_ip`='$this->m_com_ip' AND
									`doc_date` ='$this->doc_date' AND
									`promo_code`<>'$promo_code' 
						 ORDER BY seq DESC limit 0,1";
			$row_pro=$this->db->fetchAll($sql_p);
			if(!empty($row_pro)){
				if($row_pro[0]['status_no']=='04'){
					$sql_sp="SELECT promo_code FROM promo_point1_detail WHERE promo_id='".$row_pro[0]['promo_code']."'";
					$row_sp=$this->db->fetchAll($sql_sp);
					return $row_sp[0]['promo_code'];
				}else{
					return $row_pro[0]['promo_code'];
				}
			}else{
				return '';
			}				
		}//func
		
		public function chkAmtVip($product_id,$quantity,$limited,$limited_type,$sum_amt,$vip_percent_discount){
			/**
			 * @desc
			 * @return
			 */
			$sql_prod="SELECT  price
							FROM `com_product_master` 
								WHERE 
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND
									product_id='$product_id'";			
			$price=$this->db->fetchOne($sql_prod);
			$p_amt=$price*$quantity;
			$p_amt=$p_amt-$p_amt*($vip_percent_discount/100);			
			$sql_trn2="SELECT 
									SUM(amount) AS sum_amount,SUM(net_amt) AS sum_net_amt
								FROM
									trn_tdiary2_sl
								WHERE
										`corporation_id`='$this->m_corporation_id' AND 
										`company_id`='$this->m_company_id' AND
										`branch_id` ='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
 										`computer_ip`='$this->m_com_ip' AND
										`doc_date` ='$this->doc_date'";
			
			$row_trn2=$this->db->fetchAll($sql_trn2);
			if(count($row_trn2)>0){
				if($limited_type=='N'){
					$chk_amt=$row_trn2[0]['sum_net_amt']+$p_amt;
				}else{
					$chk_amt=$row_trn2[0]['sum_amount']+$p_amt;
				}
				
				//$limited=$limited-$sum_amt;				
				if($chk_amt>$sum_amt){
					return 'N';
				}else{
					return 'Y';
				}
			}
							
		}//func
		
		public function selTblTemp(){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				$sql_chk_tbl="SELECT COUNT(*) 
										FROM trn_promotion_tmp1 
											WHERE 
													`corporation_id`='$this->m_corporation_id' AND 
													`company_id`='$this->m_company_id' AND
													`branch_id` ='$this->m_branch_id' AND
													`computer_no`='$this->m_computer_no' AND
 													`computer_ip`='$this->m_com_ip' AND
													 `doc_date` ='$this->doc_date'";
				$n_chk_tbl=$this->db->fetchOne($sql_chk_tbl);
				if($n_chk_tbl>0){
					$tbl_temp="trn_promotion_tmp1";
				}else{
					$tbl_temp="trn_tdiary2_sl";
				}
				return $tbl_temp;
		}//func
		
		function getProductFree($promo_code){
			/**
			 * @desc modify : 05092013
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
			$sql_unit="SELECT unitn FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_unit=$this->db->fetchAll($sql_unit);
			$unitn=$row_unit[0]['unitn'];
			
			$sql_product="SELECT a.product_id,b.name_product
									FROM `promo_detail` AS a LEFT JOIN `com_product_master` AS b
											  ON(a.product_id=b.product_id)
									WHERE  
											a.corporation_id ='$this->m_corporation_id' AND 
											a.company_id ='$this->m_company_id' AND
											a.promo_code = '$promo_code' AND
											'$this->doc_date' BETWEEN a.start_date AND a.end_date";
			//echo $sql_product;exit();
			$arr_product=$this->db->fetchAll($sql_product);

			//*WR05092014 check stock
			$arr_date=explode('-',$this->doc_date);
			$stk_year=$arr_date[0];
			$stk_month=$arr_date[1];
			$stk_month=intval($stk_month);
			$arr_data=array();
			$i=0;
			foreach($arr_product as $data){
				$product_id=$data['product_id'];
				$name_product=$data['name_product'];
				$sql_stk="SELECT COUNT(*) FROM com_stock_master WHERE product_id='$product_id' AND `year`='$stk_year' AND `month`='$stk_month' AND `onhand`>0 ";				
				$n_stk=$this->db->fetchOne($sql_stk);
				if($n_stk>0){
					$arr_data[$i]['product_id']=$product_id;
					$arr_data[$i]['name_product']=$name_product;
					$i++;
				}
			}//foreach
			
			$sql_chk_exist="SELECT COUNT(*) FROM `trn_tdiary2_sl` WHERE promo_code='$promo_code'";
			$n_chk_exist=$this->db->fetchOne($sql_chk_exist);
			$b_chk='N';
			if($n_chk_exist<1){
				$sql_chk_exist="SELECT COUNT(*) FROM `trn_promotion_tmp1` WHERE promo_code='$promo_code'";
				$n_chk_exist=$this->db->fetchOne($sql_chk_exist);
				if($n_chk_exist>0){
					$b_chk='Y';
				}
			}else{
				$b_chk='Y';
			}
			if($n_chk_exist>=$unitn){
				$arr_data=array();
			}
			return $arr_data;
		}//func
		
		function getProductFree22($promo_code){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
			$sql_product="SELECT a.product_id,b.name_product
									FROM `promo_detail` AS a LEFT JOIN `com_product_master` AS b
											  ON(a.product_id=b.product_id)
									WHERE  
											a.corporation_id ='$this->m_corporation_id' AND 
											a.company_id ='$this->m_company_id' AND
											a.promo_code = '$promo_code' AND
											'$this->doc_date' BETWEEN a.start_date AND a.end_date";
			$arr_product=$this->db->fetchAll($sql_product);
			return $arr_product;
		}//func
		
		public function closeDay(){
			/**
			 * @param
			 * @desc
			 * @modify 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_date");
			$status_closeday=0;
			$objPos=new Model_PosGlobal();			
			$this->doc_date=parent::getDocDate();
			if($this->doc_date <= date("Y-m-d")){
				//$nextday = date("Y-m-d",strtotime("+1 days"));
				$sql_closeday="UPDATE com_doc_date 
									SET doc_date=(SELECT DATE_ADD(doc_date,INTERVAL 1 DAY) AS doc_date)
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id'";
				$res_closeday=$this->db->query($sql_closeday);
				if($res_closeday){
					//*****ถ้าเป็นวันสุดท้ายของเดือนตต้องทำยอดยกมาในเดือนถัดไปและปีถัดไป
					$this->dumpStock($this->doc_date);
					$status_closeday=1;			
					//keep log
					$sql_log="INSERT INTO com_dayend_time
									SET
										corporation_id='$this->m_corporation_id',
										company_id='$this->m_company_id',
										branch_id='$this->m_branch_id',
										dayend_date='$this->doc_date',
										date_close=CURDATE(),
										time_close=CURTIME()";					
					$this->db->query($sql_log);
				
					//////////////////////// edc settlement ////////////////////
					//$call_status=$objPos->autoSettlement();
					/////////////////////// edc settlement /////////////////////
				}
			}
			return $status_closeday;			
		}//func		
		
		public function docNo2Int($doc_no){
			/**
			 * @desc to sorting to for date close bill
			 * @param String $doc_no:
			 * @return Integer $doc_number
			 */
			$lpos=strrpos($doc_no,'-');
			if($lpos){
				$lpos+=1;
				$doc_number=intval(substr($doc_no,$lpos));
			}else{
				$doc_number='';
			}
			return $doc_number;
		}//func
		
		public function checkCloseDay($doc_date){
			/**
			 * @desc
			 * @param String $doc_date
			 * @return Char $chk_status
			 * @last modify 23032012
			 */	
			$doc_no_src='';
			$chk_status='1';
			$str_detail='';
		    //0.ปิดบิลก่อนเวลาที่กำหนดหรือไม่
			$arr_day = getdate();			
			
			if(!$this->doc_date){
				$doc_date=parent::getDocDate();//get current doc_date from com_doc_date
			}else{
				$doc_date=$this->doc_date;
			}
			
		//*WR23052014
			$arr_docdate2day=explode("-",$this->doc_date);
			$timeStmpDocDate2Day = mktime(0,0,0,$arr_docdate2day[1],$arr_docdate2day[2],$arr_docdate2day[0]);
			$arr_date2system=explode("-",date("Y-m-d"));
			$timeStmpDocDate2System = mktime(0,0,0,$arr_date2system[1],$arr_date2system[2],$arr_date2system[0]);			
			if($timeStmpDocDate2Day >= $timeStmpDocDate2System){
				//*WR23052014 allow re close day
				if($arr_day['weekday']=='Saturday' || $arr_day['weekday']=='Sunday'){
					$sql_chktime="SELECT special_open_time AS c_start_time,special_close_time AS c_end_time
									FROM `com_time_branch`
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										'$doc_date' BETWEEN start_date AND end_date AND
										CURTIME() < special_close_time";
				}else{
					$sql_chktime="SELECT normal_open_time  AS c_start_time,normal_close_time AS c_end_time
									FROM `com_time_branch`
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										'$doc_date' BETWEEN start_date AND end_date AND
										CURTIME() < normal_close_time";
				}
				$row_chktime=$this->db->fetchAll($sql_chktime);		
				if(count($row_chktime)>0){
					$chk_status='9';
					$arr_time=explode(":",$row_chktime[0]['c_end_time']);
					$str_detail=$arr_time[0].".00 น.";
					return $chk_status."#".$str_detail;
					exit();
				}
				//*WR23052014 allow re close day				
			}
			
//			if($arr_day['weekday']=='Saturday' || $arr_day['weekday']=='Sunday'){
//				$sql_chktime="SELECT special_open_time AS c_start_time,special_close_time AS c_end_time
//								FROM `com_time_branch`
//								WHERE 
//									corporation_id='$this->m_corporation_id' AND
//									company_id='$this->m_company_id' AND
//									branch_id='$this->m_branch_id' AND
//									'$doc_date' BETWEEN start_date AND end_date AND
//									CURTIME() < special_close_time";
//			}else{
//				$sql_chktime="SELECT normal_open_time  AS c_start_time,normal_close_time AS c_end_time
//								FROM `com_time_branch`
//								WHERE 
//									corporation_id='$this->m_corporation_id' AND
//									company_id='$this->m_company_id' AND
//									branch_id='$this->m_branch_id' AND
//									'$doc_date' BETWEEN start_date AND end_date AND
//									CURTIME() < normal_close_time";
//			}
//			$row_chktime=$this->db->fetchAll($sql_chktime);		
//			if(count($row_chktime)>0){
//				$chk_status='9';
//				$arr_time=explode(":",$row_chktime[0]['c_end_time']);
//				$str_detail=$arr_time[0].".00 น.";
//				return $chk_status."#".$str_detail;
//				exit();
//			}
			
			//10 ตรวจสอบมีการโอนข้อมูลระหว่างการเช็คสต๊อกค้างอยู่หรือไม่ให้โอนกลับก่อน
			$sql_trf="SELECT COUNT(*) FROM `trn_tdiary1_ck`";
			$n_trf=$this->db->fetchOne($sql_trf);
			if($n_trf>0){
				$chk_status='10';
				$str_detail='';
				return $chk_status."#".$str_detail;
				exit();
			}
			
			//1. ตรวจสอบมีการเปิดบิลตามวันที่ doc_date หรือไม่
			$sql_diary1="SELECT doc_date,doc_no,doc_tp,status_no
							 FROM `trn_diary1` 
							WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									doc_date='$doc_date' ORDER BY doc_tp,doc_no";
			$rows=$this->db->fetchAll($sql_diary1);
			if(count($rows)>0){
				$arr_docno=array();
				$arr_doctp=array();	
				$i=0;
				foreach($rows as $data){
					$doc_date=$data['doc_date'];
					$doc_tp=$data['doc_tp'];
					$doc_no=$data['doc_no'];		
					$status_no=$data['status_no'];			
					if(!in_array($doc_tp,$arr_doctp))
						array_push($arr_doctp,$doc_tp);
					$arr_docno[$i]['doc_date']=$doc_date;
					$arr_docno[$i]['doc_no']=$doc_no;
					$arr_docno[$i]['doc_tp']=$doc_tp;
					$arr_docno[$i]['status_no']=$status_no;
					$i++;
				}
				
				$arr_docorder=array();
				foreach($arr_doctp as $doc_tp){
					$i=0;
					foreach($rows as $data){
						if($doc_tp==$data['doc_tp']){
							$arr_docorder[$doc_tp][$i]=$this->docNo2Int($data['doc_no']);
							$i++;
						}
					}
				}
				
				//2.ตรวจสอบความต่อเนื่องของเลขที่เอกสารย้อนหลัง 1 วัน		
				/*
				$arrDate=explode("-",$doc_date);
				$m=$arrDate[1];
				$d=$arrDate[2];
				$y=$arrDate[0];			
				$prv_date = date('Y-m-d',strtotime($doc_date."-1 day"));
				foreach($arr_doctp as $doc_tp){
					$sql_doc_prv="SELECT doc_no
										FROM `trn_diary1` 
											WHERE 
												corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND
												branch_id='$this->m_branch_id' AND
												doc_tp='$doc_tp' AND
												doc_date='$prv_date' ORDER BY doc_no DESC LIMIT 0,1";					
					$doc_no_prv=$this->db->fetchOne($sql_doc_prv);
					if($doc_no_prv){
						$doc_prv_cmp=$this->docNo2Int($doc_no_prv)+1;
						if($arr_docorder[$doc_tp][0]!=$doc_prv_cmp){
							$chk_status='3';
							$doc_no_src=$doc_no_prv;
							break;
						}
					}					
				}//foreach
				
				//3.ตรวจสอบความต่อเนื่องของเลขที่เอกสารแต่ละประเภท	
				//กรณีติดตั้งวันแรกข้ามขั้นตอนนี้นะ ป้องกัน format doc_no เก่าใหม่ไม่เหมือนกัน
				$n_day_first=0;
				$sql_day_first="SELECT COUNT(*) 
										FROM `com_dayend_time` 
										WHERE 
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id'";	
				$n_day_first=$this->db->fetchOne($sql_day_first);
				if($chk_status=='1' && $n_day_first>0){		
					foreach($arr_doctp as $doc_tp){
						$s_doc=$arr_docorder[$doc_tp][0];
						for($i=0;$i<count($arr_docorder[$doc_tp]);$i++){
							if($arr_docorder[$doc_tp][$i]!=$s_doc){
								$chk_status='4';
								break;
							}
							$s_doc++;
						}
						if($chk_status!='1')
							break;
					}//foreach
				}
				*/
				//4.ตรวจสอบข้อมูลใบลดหนี้ CN 40 เปลี่ยนต้องมีการเปิดบิลประกบ SL 06 ทุกใบลดหนี้
				if($chk_status=='1'){
					foreach($arr_docno as $data){
						if($data['doc_tp']=='CN'){
							$doc_no=$data['doc_no'];
							$sql_chk="SELECT COUNT(*) FROM trn_diary1 WHERE doc_no='$doc_no' AND cn_status<>'Y' AND status_no<>'41'  AND flag<>'C'";
							$n_chk=$this->db->fetchOne($sql_chk);
							if($n_chk>0){
								$chk_status='5';
								$str_detail=$doc_no;
								break;
							}
						}
					}
				}
				
				//5.ทุกสิ้นเดือน ตรวจสอบข้อมูลการอนุมัติ RQ ถ้าพบต้องทำการ confirm TO ก่อน			
				if($chk_status=='1'){	
					if($doc_date==parent::lastday($m,$y)){
						$sql_rq="SELECT COUNT(*) FROM `trn_diary1_rq` 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND
											flag<>'C' AND
											status_transfer='Y'";
						$n_rq=$this->db->fetchOne($sql_rq);
						if($n_rq>0){
							$chk_status='6';
						}
					}
				}
				
			//6.ตรวจสอบข้อมูลการบันทึก profile ของสมาชิกใหม่ต้องบันทึกข้อมูลภายใน 2 วันนับจากวันสมัคร
			if($chk_status=='1'){
					foreach($arr_docno as $data){
						if($data['status_no']=='01'){
							$doc_date=$data['doc_date'];
							$prv_date=date('Y-m-d', strtotime($doc_date.' -2 days'));
							$doc_no=$data['doc_no'];
							$sql_new_member="SELECT b.member_id 
															FROM trn_diary1 AS a LEFT JOIN com_register_new_card AS b
																ON(a.member_id=b.member_id)
															WHERE 
																a.doc_no='$doc_no' AND 
																a.doc_date<='$prv_date' AND
																b.flag_save<>'1'";	
							$row_new_member=$this->db->fetchAll($sql_new_member);							
							if(count($row_new_member)>0){
								$chk_status='7';
								break;
							}
						}
					}
				}
				
				//7.ตรวจสอบตารางเก็บข้อมูลการขายระหว่างการเช็คสต๊อก
				if($chk_status=='1'){
					$sql_chk1="SELECT COUNT(*) FROM `trn_tdiary1_ck`";
					$n_chk1=$this->db->fetchOne($sql_chk1);
					if($n_chk1>0){
						$chk_status='8';
					}else{
						$sql_chk2="SELECT COUNT(*) FROM `trn_tdiary2_ck`";
						$n_chk2=$this->db->fetchOne($sql_chk2);
						if($n_chk2>0){
							$chk_status='8';
						}
					}
				}
				
				//ประมวลผลข้อมูลสต๊อกจุดขายตาราง com_stock_master
				if($chk_status=='1'){
//					//stock balance
//					$objPos=new SSUP_Controller_Plugin_PosGlobal();
//					$objPos->stockBalance($this->m_corporation_id,$this->m_company_id,$this->m_branch_id,$doc_date);
					//backup data
//					$arr_tblbk=array('com_doc_no','com_stock_master','trn_diary1','trn_diary2');
//					$abs_path="/var/www/dbposbackup";
//					$folder_backup='';
//					foreach($arr_tblbk as $tbl_name){			
//						$folder_backup=$objPos->backupTableData($tbl_name,$abs_path);
//					}		
//					$objPos->delSrcTableData($folder_backup);

					//keep log
					$sql_log="INSERT INTO com_dayend_time
									SET
										corporation_id='$this->m_corporation_id',
										company_id='$this->m_company_id',
										branch_id='$this->m_branch_id',
										dayend_date='$doc_date',
										date_close=CURDATE(),
										time_close=CURTIME()";					
					$this->db->query($sql_log);
					
					//update com_doc_date
					$sql_update="UPDATE com_doc_date 
									  SET 
											 doc_date=(SELECT DATE_ADD(doc_date,INTERVAL 1 DAY) AS doc_date),
											 upd_date='$doc_date',
					   						 upd_time=CURTIME(),
					   						 upd_user='$this->employee_id'
									  WHERE
									  		corporation_id='$this->m_corporation_id' AND
									  		company_id='$this->m_company_id'";
					$this->db->query($sql_update);	
					
					//stock balance
					$objPos=new SSUP_Controller_Plugin_PosGlobal();
					$objPos->stockBalance($this->m_corporation_id,$this->m_company_id,$this->m_branch_id,$doc_date);
					//*****ถ้าเป็นวันสุดท้ายของเดือนตต้องทำยอดยกมาในเดือนถัดไปและปีถัดไป
					$this->dumpStock($doc_date);
				}//end if chk_status=1
				
			}else{
				//doc_date not found
				$chk_status='2';
			}
			return $chk_status.'#'.$str_detail.'#'.$doc_no_src;
		}//func	

		function dumpStock($doc_date){
			/**
			 * @desc :ถ้าเป็นวันสุดท้ายของเดือนตต้องทำยอดยกมาในเดือนถัดไปและปีถัดไป
			 * @param String $doc_date
			 * @return void
			 */
			$arrDate=explode("-",$doc_date);
			$m=$arrDate[1];
			$d=$arrDate[2];
			$y=$arrDate[0];
			$mm=intval($m);
			$str_nextmonth=date("Y-m-d",strtotime($y."-".$m."+1 months"));
			$arr_nextmonth=explode("-",$str_nextmonth);
			if($mm==12){
				$next_y=$arr_nextmonth[0];
			}else{
				$next_y=$y;
			}
			$next_m=intval($arr_nextmonth[1]);
			if($doc_date==parent::lastday($m,$y)){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
				$date_insert=date("Y-m-d");
				$time_insert=date("H:i:s");
				//clear if exsit
				$sql_del="DELETE FROM `com_stock_master` 
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`month`='$next_m' AND
									`year`='$next_y'";
				@$this->db->query($sql_del);
				$sql_insert="INSERT INTO 
								`com_stock_master`(corporation_id,company_id,branch_id,branch_no,product_id,month,
															year,product_status,begin,onhand,allocate,reg_date,reg_time,reg_user,upd_date,upd_time,upd_user)
								SELECT corporation_id,company_id,branch_id,branch_no,
										    product_id,'$next_m','$next_y',product_status,onhand,onhand,
											allocate,'$date_insert','$time_insert','$this->employee_id',upd_date,upd_time,upd_user 
								FROM 
									`com_stock_master` 
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`month`='$mm' AND
									`year`='$y'";
				$this->db->query($sql_insert);
			}//if
		}//func
				
		
		function getTax($country,$tax_type){
			/**
			 * 
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_tax");
			$sql_tax="SELECT percent_tax FROM com_tax WHERE country_id='$country' AND tax_type='$tax_type'";
			$row_tax=$this->db->fetchCol($sql_tax);
			return $row_tax[0]['percent_tax'];
		}//func
		
		function getPromoDetail(){
			/**
			 * @desc show item finish before payment 
			 * @return Array on play promotion complete
			 * @lastmodify 18082011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$stmt_pro=$this->db->select()
								->from(array('a'=>'trn_promotion_tmp1'),array('id','promo_code',
									'promo_st','promo_tp','promo_tp','product_id','get_point',
									'discount_member','quantity','price','discount','member_discount1',
									'member_discount2','amount','net_amt'))
								->joinLeft(array('b'=>'com_product_master'),'a.product_id=b.product_id',
											array('unit','tax_type','name_product'))
								->where('a.corporation_id=?',$this->m_corporation_id)
								->where('a.company_id=?',$this->m_company_id)
								->where('a.branch_id=?',$this->m_branch_id)
								->where('a.doc_date=?',$this->doc_date);
			$row_pro=$stmt_pro->query()->fetchAll();
			if(count($row_pro)>0){
				return $row_pro;
			}else{
				return false;
			}
		}//func
		
		function getSumPromoDetail(){
			/**
			 * @name getSumInv
			 * @desc get summary info data of sale transaction			
			 * @param Integer $xpoint :ตัวคูณคะแนนปกติ บิลแรกหลังสมัครสมาชิก
			 * @param Char flg_chk_point: 'Y' สมาชิกซื้อแต่ไม่ใช่บิล 01(สมัครสมาชิกใหม่) 
			 * @return array of row member
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$sql_suminv="SELECT 
								SUM(quantity) AS sum_quantity,							
							 	(sum( discount ) + 
							 	sum( member_discount1 ) +
							 	sum( member_discount2 ) + 
							 	sum( co_promo_discount ) + 
							 	sum( coupon_discount ) + 
							 	sum( special_discount ) + 
							 	sum( other_discount )) AS sum_discount,
							 	sum(amount) AS sum_amount,
							 	sum(net_amt) AS sum_net_amt,
							 	status_no		
							FROM 
								`trn_promotion_tmp1`
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								branch_id='$this->m_branch_id' AND
								doc_date='$this->doc_date'";
			$row=$this->db->fetchAll($sql_suminv);
			if(count($row)>0){
				//update 20/07/2012
				$sum_discount=number_format($row[0]['sum_discount'],"2",".","");
				$sum_amount=number_format($row[0]['sum_amount'],"2",".","");
				$sum_amount=parent::getSatang($sum_amount,'up');
				$sum_discount=parent::getSatang($sum_discount,'up');		
				$row[0]['sum_discount']=$sum_discount;
				$row[0]['sum_net_amt']=$sum_amount-$sum_discount;		
					
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcsh',$row[0],'yes');	
				return $json;				
			}
		}//func
		
		function getPaid(){
			/**
			 * @desc get paid detail
			 * @return Array of paid data
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_paid");
			$stmt_paid=$this->db->select()
								->from('com_paid',array('paid_tp','paid','description'))
								->where('corporation_id=?',$this->m_corporation_id)
								->where('company_id=?',$this->m_company_id)
								->order('paid ASC');
			$row_paid=$stmt_paid->query()->fetchAll();
			return $row_paid;
		}//func
		
		function setPromotionTemp($doc_tp,$promo_code,$employee_id,$member_no='',$product_id,$quantity,
										$status_no,$product_status,$application_id,$card_status,
										$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
										$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member){
				/**
				 * @name
				 * @return
				 */			
			if(trim($member_no)==''){
				$discount_percent='';
				$member_percent1='';
				$member_percent2='';
				$co_promo_percent='';
				$coupon_percent='';
			}
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$this->promo_code=$promo_code;
			$this->promo_st=$promo_st;
			$this->product_id=$product_id;
			$this->discount=$discount;
			$this->employee_id=$employee_id;
			$this->member_no=$member_no;
			$this->quantity=$quantity;//จำนวนสินค้าที่มาจากหน้าขาย		
			$this->discount_percent=$discount_percent;
			$this->member_percent1=$member_percent1;//% ค่าส่วนลดสมาชิกที่1	
			$this->member_percent2=$member_percent2;
			$this->co_promo_percent=$co_promo_percent;
			$this->coupon_percent=$coupon_percent;
			$this->status_no=$status_no;
			$arr_product_status=explode("-",$product_status);
			$this->product_status=$arr_product_status[0];
			$this->discount_member=$discount_member;		
			$arr_product=parent::browsProduct($this->product_id);
			$proc_status=0;
			if(!empty($arr_product)){
				$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
				if($balance<$this->quantity){
					//stock ขาด
					$proc_status='2';
				}else{
					$this->product_name=$arr_product[0]['name_product'];			
					$this->price=$arr_product[0]['price'];
					if($this->status_no=='05' && ($card_status=='C2' || $card_status=='C3')){
						//บัตรเสีย ชำรุด
						$this->price=0.00;
					}
					$this->tax_type=$arr_product[0]['tax_type'];							
					$this->amount=$this->price * $this->quantity;
					$this->get_point=$get_point;//ใด้รับคะแนนหรือไม่
					/*
					if(intval($this->member_percent1)!=0 || intval($this->member_percent2)!=0){
						$this->discount_member='Y';//คำนวณส่วนลดสมาชิก
					}else{
						$this->discount_member='N';//คำนวณส่วนลดสมาชิก
					}
					*/
					if($this->member_no!='' && $this->discount_member=='Y'){					
						$this->member_discount1=$this->price*$this->quantity*($this->member_percent1/100);
						if(intval($this->member_percent2)!=0){
							$amount_by_discount=$this->amount-$this->member_discount1;
							$this->member_discount2=$amount_by_discount*($this->member_percent2/100);
						}else{
							$this->member_discount2=0.0000;
						}
						
					}else{
						$this->member_discount1=0.0000;								
					}
					
					//cal promotion discount
					$this->total_discount=$this->discount+$this->member_discount1+$this->member_discount2;
					//$this->total_discount=$this->discount;//*WR 10042012 ไม่นำส่วนลดสมาชิกมาคิด
					$this->net_amt=$this->amount-$this->total_discount;
					//----------------------------------------------------							
					//detail table trn_tdiary2_sl
					$sql_doc='SELECT 
									stock_st,branch_no 
								FROM 
									`com_doc_no` 
								WHERE 
									corporation_id="'.$this->m_corporation_id.'" AND 
									company_id="'.$this->m_company_id.'" AND
									branch_id="'.$this->m_branch_id.'" AND
									doc_tp="'.$this->doc_tp.'"';
					$rowdoc=$this->db->fetchAll($sql_doc);
					if(count($rowdoc)>0){
						$this->m_branch_no=$rowdoc[0]['branch_no'];
						$this->stock_st=$rowdoc[0]['stock_st'];
					}
									
					$this->doc_time=date("H:i:s");
					
					$sql_cseq="SELECT seq
										FROM trn_promotion_tmp1
										WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip' AND
												  `promo_code`='$this->promo_code'";	
					$row_cseq=$this->db->fetchCol($sql_cseq);
					if(count($row_cseq)>0){
						$this->seq=$row_cseq[0];
					}else{
						$sql_maxseq="SELECT MAX(seq) AS max_seq
										FROM trn_promotion_tmp1
											WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip'";	
						$row_seq=$this->db->fetchAll($sql_maxseq);			
						$this->seq=$row_seq[0]['max_seq']+1;
					}					
					
					$sql_pro_seq="SELECT MAX(promo_seq) AS max_promo_seq
										FROM trn_promotion_tmp1
											WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip' AND
												  `promo_code`='$this->promo_code'";	
					$row_pro_seq=$this->db->fetchAll($sql_pro_seq);
					$this->promo_seq=$row_pro_seq[0]['max_promo_seq']+1;		
				
					$sql_insert2="INSERT INTO trn_promotion_tmp1
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='$doc_tp',
									  `status_no`='$this->status_no',
									  `seq` ='$this->seq',
									  `promo_code` ='$this->promo_code',
									  `promo_seq` ='$this->promo_seq',
									  `promo_st` ='Other',
									  `promo_pos` ='O',
									  `promo_tp` ='',
									  `product_id`='$this->product_id',								  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='$this->price',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='$this->discount',								  
									  `member_discount1`='$this->member_discount1',
									  `member_discount2`='$this->member_discount2',								  
									  `net_amt`='$this->net_amt',
									  `product_status`='$this->product_status',
									  `get_point`='$this->get_point',
									  `discount_member`='$this->discount_member',
									  `cal_amt`='Y',											  
									  `discount_percent`='$this->discount_percent',
									  `member_percent1`='$this->member_percent1',
									  `member_percent2`='$this->member_percent2',
									  `co_promo_percent`='$this->co_promo_percent',
									  `coupon_percent`='$this->coupon_percent',											  
									  `tax_type`='$this->tax_type',
									  `computer_no`='$this->m_computer_no',
 									  `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert2);		
					$proc_status=1;
				}
			}
		
			return $proc_status."#".$this->amount;						
		}//func
		
		function setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no='',$product_id,$quantity,
										$status_no,$product_status,$application_id,$card_status,
										$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
										$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat,$unitn=''){
			/**
			 * @name
			 * @return
			 */	
			$sql_chk_tbl="SELECT COUNT(*) 
									FROM trn_promotion_tmp1 
										WHERE 
												`corporation_id`='$this->m_corporation_id' AND 
												`company_id`='$this->m_company_id' AND
												 `branch_id` ='$this->m_branch_id' AND
												 `computer_no`='$this->m_computer_no' AND
 												`computer_ip`='$this->m_com_ip' AND
												 `doc_date` ='$this->doc_date'";
			$n_chk_tbl=$this->db->fetchOne($sql_chk_tbl);
			if($n_chk_tbl>0){
				$tbl_temp="trn_promotion_tmp1";
			}else{
				$sql_chk_tbl2="SELECT COUNT(*) 
									FROM trn_tdiary2_sl 
										WHERE 
												`corporation_id`='$this->m_corporation_id' AND 
												`company_id`='$this->m_company_id' AND
												 `branch_id` ='$this->m_branch_id' AND
												 `computer_no`='$this->m_computer_no' AND
 												`computer_ip`='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date'";
				$n_chk_tbl2=$this->db->fetchOne($sql_chk_tbl2);
				if($n_chk_tbl2>0){
					$tbl_temp="trn_tdiary2_sl";
				}else{
					$tbl_temp="trn_promotion_tmp1";//*WR24072012 ทดสอบปรับให้วิ่งเข้าตารางของ joke อย่างเดียวกรณีบิลเล่นหลายโปร
				}
				
			}
			
			//*WR01092014 ห้ามเกิน			
			if($unitn!='' && $unitn>0){					
					$sql_unit_pro="SELECT SUM(quantity) AS curr_qty FROM $tbl_temp 
											WHERE 
												`corporation_id`='$this->m_corporation_id' AND 
												`company_id`='$this->m_company_id' AND
												 `branch_id` ='$this->m_branch_id' AND
												`computer_no`='$this->m_computer_no' AND
 												`computer_ip`='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' AND
												`promo_code`='$promo_code'";
					$row_unit_pro=$this->db->fetchAll($sql_unit_pro);
					if(!empty($row_unit_pro)){
						$curr_qty=$row_unit_pro[0]['curr_qty']+$quantity;
						if($curr_qty>$unitn){
							$str_res= "5#0#".$tbl_temp;
							echo $str_res;
							exit();
						}
					}
			}
			
			//*WR20120906 กลับมาดูบิล 04 เกี่ยวอะไรกับ setGapValTemp WR11082014 mark ไว้ก่อนรอตรวจสอบ
//			if($status_no=='04'){
//				$tbl_temp="trn_tdiary2_sl";
//			}
			
			if($promo_code=='17041015'){
				$promo_st="Percent";
			}else if($promo_st=='' && $promo_tp!='F'&& $promo_tp!='F'){
				$promo_st='Normal';
			}else if(strtolower($promo_st)=='percent'){
				$promo_st="Percent";
			}else if($promo_tp=='F'){
				$promo_st='Free';
			}
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn",$tbl_temp);
			$this->promo_code=$promo_code;
			$this->promo_tp=$promo_tp;
			$this->promo_st=$promo_st;
			$this->product_id=$product_id;
			$this->discount=$discount;
			$this->employee_id=$employee_id;
			$this->member_no=$member_no;
			$this->quantity=$quantity;//จำนวนสินค้าที่มาจากหน้าขาย		
			$this->discount_percent=$discount_percent;
			$this->member_percent1=$member_percent1;//% ค่าส่วนลดสมาชิกที่1	
			$this->member_percent2=$member_percent2;
			$this->co_promo_percent=$co_promo_percent;
			$this->coupon_percent=$coupon_percent;
			$this->status_no=$status_no;
			$arr_product_status=explode("-",$product_status);
			$this->product_status=$arr_product_status[0];
			$this->discount_member=$discount_member;		
			
			//*WR30032015
			$promo_pos ='O';
			$sql_promoh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";	
			$row_promoh=$this->db->fetchAll($sql_promoh);
			if(!empty($row_promoh)){
				$promo_pos=$row_promoh[0]['promo_pos'];
			}
				
			//chkeck repeat
			if($check_repeat=='Y'){
				$sql_rpt="SELECT COUNT(*) 
								FROM $tbl_temp 
									WHERE
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `computer_no`='$this->m_computer_no' AND
 											 `computer_ip`='$this->m_com_ip' AND
											 `doc_date` ='$this->doc_date' AND
											 `promo_code` ='$this->promo_code'  AND 
											 `product_id` ='$this->product_id'";				
				$n_rpt=$this->db->fetchOne($sql_rpt);
				if($n_rpt>0){
					$str_res= "3#0#".$tbl_temp;
					echo $str_res;
					exit();
				}
			}
		
			$arr_product=parent::browsProduct($this->product_id);
			$proc_status=0;
			$str_res='';
			if(!empty($arr_product)){
				$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
				if($balance<$this->quantity){
					//stock ขาด
					$proc_status='2';
				}else{
					$this->product_name=$arr_product[0]['name_product'];		
					if($this->promo_tp=='F'){
						$this->price=0;
					}else{
						$this->price=$arr_product[0]['price'];
					}
					if($this->status_no=='05' && ($card_status=='C2' || $card_status=='C3')){
						//บัตรเสีย ชำรุด
						$this->price=0.00;
					}
					$this->tax_type=$arr_product[0]['tax_type'];							
					$this->amount=$this->price * $this->quantity;
					$this->get_point=$get_point;//ใด้รับคะแนนหรือไม่
				
					if($this->member_no!='' && $this->discount_member=='Y'){		
									
						$this->member_discount1=$this->price*$this->quantity*($this->member_percent1/100);
						if(intval($this->member_percent2)!=0){
							$amount_by_discount=$this->amount-$this->member_discount1;
							$this->member_discount2=$amount_by_discount*($this->member_percent2/100);
						}else{
							$this->member_discount2=0.0000;
						}
						
					}else if($this->discount_member=='N'){
						//*WR20120906 for support 50BTO1P
						if(intval($this->discount_percent)!=0){							
							$amount_by_discount=$this->amount-($this->member_discount1+$this->member_percent2);
							$this->discount=$amount_by_discount*($this->discount_percent/100);
						}
						$this->member_discount1=0.0000;								
					}
					
					//*WR17102012
					$bal_discount=0;
					if($this->amount < $this->discount){
						$bal_discount=$this->discount-$this->amount;
						$this->discount=$this->amount;
					}
					//*WR17102012
				
					$this->total_discount=$this->discount+$this->member_discount1+$this->member_discount2;
					$this->net_amt=$this->amount-$this->total_discount;
					//----------------------------------------------------							
					//detail table trn_tdiary2_sl
					$sql_doc='SELECT 
									stock_st,branch_no 
								FROM 
									`com_doc_no` 
								WHERE 
									corporation_id="'.$this->m_corporation_id.'" AND 
									company_id="'.$this->m_company_id.'" AND
									branch_id="'.$this->m_branch_id.'" AND
									doc_tp="'.$this->doc_tp.'"';
					$rowdoc=$this->db->fetchAll($sql_doc);
					if(count($rowdoc)>0){
						$this->m_branch_no=$rowdoc[0]['branch_no'];
						$this->stock_st=$rowdoc[0]['stock_st'];
					}
									
					$this->doc_time=date("H:i:s");
					
					$sql_seq="SELECT MAX(seq) AS seq 
										FROM $tbl_temp
										WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip'";
					$maxseq=$this->db->fetchOne($sql_seq);
					$this->seq=$maxseq+1;
					
					$sql_cseq="SELECT seq_set
										FROM $tbl_temp 
										WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip' AND
												  `promo_code`='$this->promo_code'";						
					$row_cseq=$this->db->fetchAll($sql_cseq);
					if(count($row_cseq)>0){
						$seq_set=$row_cseq[0]['seq_set'];
					}else{
						$sql_maxseq="SELECT MAX(seq_set) AS max_seq_set
										FROM $tbl_temp
											WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip' AND
												  `branch_id` ='$this->m_branch_id'";	
						$max_seq_set=$this->db->fetchOne($sql_maxseq);			
						$seq_set=$max_seq_set+1;						
					}					
					
					$sql_pro_seq="SELECT MAX(promo_seq) AS max_promo_seq
										FROM $tbl_temp
											WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id'  AND 
												  `promo_code` ='$this->promo_code'";	
					$max_promo_seq=$this->db->fetchOne($sql_pro_seq);
					if($max_promo_seq>0){
						$this->promo_seq=$max_promo_seq+1;
					}else{
						$this->promo_seq=1;
					}						
				
					$sql_insert2="INSERT INTO $tbl_temp
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='$doc_tp',
									  `status_no`='$this->status_no',
									  `seq` ='$this->seq',
									  `seq_set` ='$seq_set',
									  `promo_code` ='$this->promo_code',
									  `promo_seq` ='$this->promo_seq',
									  `promo_st` ='$this->promo_st',
									  `promo_pos` ='O',
									  `promo_tp` ='$this->promo_tp',
									  `product_id`='$this->product_id',								  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='$this->price',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='$this->discount',								  
									  `member_discount1`='$this->member_discount1',
									  `member_discount2`='$this->member_discount2',								  
									  `net_amt`='$this->net_amt',
									  `product_status`='$this->product_status',
									  `get_point`='$this->get_point',
									  `discount_member`='$this->discount_member',
									  `cal_amt`='Y',											  
									  `discount_percent`='$this->discount_percent',
									  `member_percent1`='$this->member_percent1',
									  `member_percent2`='$this->member_percent2',
									  `co_promo_percent`='$this->co_promo_percent',
									  `coupon_percent`='$this->coupon_percent',											  
									  `tax_type`='$this->tax_type',
									  `computer_no`='$this->m_computer_no',
 									  `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert2);		
					parent::decreaseStock($this->product_id,$this->quantity);
					$proc_status=1;
				}				
			}		
			
			$str_res= $proc_status."#".$this->amount."#".$tbl_temp."#".$bal_discount;
			echo $str_res;
		}//func
		
		function setCshValTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
							$status_no,$product_status,$application_id,$card_status,
							$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
							$co_promo_percent,$coupon_percent,$promo_st){
			/**
			 * @name setCshValTemp
			 * @desc store data temp before insert odiary
			 * @param String $promo_code :
			 * @param String $employee_id :
			 * @param String $member_no :
			 * @param String $product_id :
			 * @param Integer $quantity :
			 * @param Interger $point :
			 * @param Float $discount :
			 * @param Char $status_no :
			 * @param Char $product_status :
			 * @return  
			 */	
			if(trim($member_no)==''){
				$discount_percent='';
				$member_percent1='';
				$member_percent2='';
				$co_promo_percent='';
				$coupon_percent='';
			}
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl_val");				
			if($promo_id!=''){
				$this->promo_code=$promo_id;
			}else if($application_id!=''){
				$this->promo_code=$application_id;
			}			
			$this->promo_st=$promo_st;
			$this->product_id=$product_id;
			$this->employee_id=$employee_id;
			$this->member_no=$member_no;
			$this->quantity=$quantity;//จำนวนสินค้าที่มาจากหน้าขาย		
			$this->discount_percent=$discount_percent;
			$this->member_percent1=$member_percent1;//% ค่าส่วนลดสมาชิกที่1	
			$this->member_percent2=$member_percent2;//% ค่าส่วนลดสมาชิกที่2
			$this->co_promo_percent=$co_promo_percent;
			$this->coupon_percent=$coupon_percent;
			$this->status_no=$status_no;
			$arr_product_status=explode("-",$product_status);
			$this->product_status=$arr_product_status[0];
			//check product lock
			$arr_product=parent::browsProduct($this->product_id);
			
			$proc_status=0;
			if(!empty($arr_product)){
				$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
				if($balance<$this->quantity){
					//stock ขาด
					$proc_status='2';
				}else{
					$this->product_name=$arr_product[0]['name_product'];			
					$this->price=$arr_product[0]['price'];
					if($this->status_no=='05' && ($card_status=='C2' || $card_status=='C3')){
						//บัตรเสีย ชำรุด
						$this->price=0.00;
					}
					$this->tax_type=$arr_product[0]['tax_type'];							
					$this->amount=$this->price * $this->quantity;
					$this->get_point=$get_point;//ใด้รับคะแนนหรือไม่
					
					if(intval($this->member_percent1)!=0 || intval($this->member_percent2)!=0){
						$this->discount_member='Y';//คำนวณส่วนลดสมาชิก
					}else{
						$this->discount_member='N';//คำนวณส่วนลดสมาชิก
					}
				
					if($this->status_no=='02'){
							//*WR07052013 แก้ปัญหารายการแรกไม่ลด							
							$sql_t1="SELECT application_id FROM `trn_diary1` 
							WHERE member_id = '$member_no' AND status_no='01' AND doc_date='$this->doc_date' AND flag<>'C'";
							$row_t1=$this->db->fetchAll($sql_t1);
							if(!empty($row_t1)){
								$application_id=$row_t1[0]['application_id'];							
								$sql_app="SELECT application_id, first_sale, first_percent, add_first_percent
												FROM `com_application_head`
												WHERE application_id='$application_id' AND
															'$this->doc_date' BETWEEN start_date AND end_date";
								$rows_app=$this->db->fetchAll($sql_app);
								$this->member_percent1=$rows_app[0]['first_percent'];
								$this->member_percent2=$rows_app[0]['add_first_percent'];								
								$this->member_discount1=$this->price*$this->quantity*($this->member_percent1/100);
								if(intval($this->member_percent2)!=0){
									$amount_by_discount=$this->amount-$this->member_discount1;
									$this->member_discount2=$amount_by_discount*($this->member_percent2/100);
								}else{
									$this->member_discount2=0.0000;
								}			
							}
					}else{
						if($this->member_no!='' && $this->discount_member=='Y'){					
							$this->member_discount1=$this->price*$this->quantity*($this->member_percent1/100);
							if(intval($this->member_percent2)!=0){
								$amount_by_discount=$this->amount-$this->member_discount1;
								$this->member_discount2=$amount_by_discount*($this->member_percent2/100);
							}else{
								$this->member_discount2=0.0000;
							}							
						}else{
							$this->member_discount1=0.0000;								
						}						
					}//end if	
					
					$this->total_discount=$this->member_discount1+$this->member_discount2;//*WR07052013 ตอนนี้คิดเฉพาะส่วนลดสมาชิก
					$this->net_amt=$this->amount-$this->total_discount;
					//----------------------------------------------------							
					//detail table trn_tdiary2_sl
					$sql_doc='SELECT 
									stock_st,branch_no 
								FROM 
									`com_doc_no` 
								WHERE 
									corporation_id="'.$this->m_corporation_id.'" AND 
									company_id="'.$this->m_company_id.'" AND
									branch_id="'.$this->m_branch_id.'" AND
									doc_tp="'.$this->doc_tp.'"';
					$rowdoc=$this->db->fetchAll($sql_doc);
					if(count($rowdoc)>0){
						$this->m_branch_no=$rowdoc[0]['branch_no'];
						$this->stock_st=$rowdoc[0]['stock_st'];
					}
									
					$this->doc_time=date("H:i:s");
					$sql_seq="SELECT MAX(seq) AS max_seq 
									FROM trn_tdiary2_sl_val 
										WHERE  
												`corporation_id`='$this->m_company_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`computer_no` ='$this->m_computer_no' AND 
				   								`computer_ip` ='$this->m_com_ip'";
					$row_seq=$this->db->fetchAll($sql_seq);
					$this->seq=$row_seq[0]['max_seq']+1;
					$this->promo_seq=$this->seq;		
					
					//joke 14/022012
					//parent::TrancateTable("trn_tdiary2_sl_val");
					$sql_clear="DELETE FROM trn_tdiary2_sl_val 
										WHERE 
											`corporation_id`='$this->m_corporation_id' AND 
											`company_id`='$this->m_company_id'  AND
											`branch_id`='$this->m_branch_id' AND 
										    `computer_no` ='$this->m_computer_no' AND 
										    `computer_ip` ='$this->m_com_ip'";
					$this->db->query($sql_clear);
					
					$sql_insert2="INSERT INTO trn_tdiary2_sl_val
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `member_no` ='$member_no',									  
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='$doc_tp',
									  `status_no`='$this->status_no',
									  `seq` ='$this->seq',
									  `promo_code` ='$this->promo_code',
									  `promo_seq` ='$this->promo_seq',
									  `promo_st` ='$this->promo_st',
									  `promo_tp` ='',
									  `product_id`='$this->product_id',								  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='$this->price',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='$this->discount',								  
									  `member_discount1`='$this->member_discount1',
									  `member_discount2`='$this->member_discount2',								  
									  `net_amt`='$this->net_amt',
									  `product_status`='$this->product_status',
									  `get_point`='$this->get_point',
									  `discount_member`='$this->discount_member',
									  `cal_amt`='Y',											  
									  `discount_percent`='$this->discount_percent',
									  `member_percent1`='$this->member_percent1',
									  `member_percent2`='$this->member_percent2',
									  `co_promo_percent`='$this->co_promo_percent',
									  `coupon_percent`='$this->coupon_percent',											  
									  `tax_type`='$this->tax_type',
									  `computer_no`='$this->m_computer_no',
									  `computer_ip`='$this->m_com_ip',	
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert2);		
					$proc_status=1;
				}
			}
			return $proc_status."#".$this->amount;
		}//func
		
		function setSmsTemp($promo_code){
			/**
			 * @desc
			 * @param String $promo_code
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$this->promo_code=$promo_code;
			$sql_promoh="SELECT unitn,limite_qty,member_tp,check_repeat,discount_member  
									FROM promo_head 
										WHERE 
											promo_code='$this->promo_code' AND
											'$this->doc_date' BETWEEN start_date AND end_date";			
			$rows_promoh=$this->db->fetchAll($sql_promoh);
			if(count($rows_promoh)>0){
				$sql_promol="SELECT `promo_pos` , `promo_code` , `promo_tp` , `status_pro` , `product_id` , `product_status`
									FROM `promo_detail`
									WHERE 
											`promo_code`='$this->promo_code' AND 
											'$this->doc_date' BETWEEN start_date AND end_date";	
				$rows_promol=$this->db->fetchAll($sql_promol);			
				if(count($rows_promol)>0){
					$res_set='';
					foreach($rows_promol as $data){
						################################ start set ################################
						$doc_tp='SL';						
						$employee_id='$this->user_id';
						$member_no='';
						$product_id=$data['product_id'];
						$quantity='1';
						$status_no='00';
						$product_status=$data['product_status'];
						$application_id='';
						$card_status='';
						$get_point='N';
						$promo_id='';
						$discount_percent='';
						$member_percent1='';
						$member_percent2='';
						$co_promo_percent='';
						$coupon_percent='';
						$promo_st='';
						$promo_tp=$data['promo_tp'];
						$promo_amt='';
						$promo_amt_type='';
						$check_repeat='';
						$web_promo='';
						$res_set=$this->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no='',$product_id,$quantity,
							$status_no,$product_status,$application_id,$card_status,
							$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
							$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo);
						################################ end set ################################
					}
				}
				
			}//end $rows_promoh			
			echo $res_set;
		}//func
		
		function setCshTemp($doc_tp,$promo_code,$employee_id,$member_no='',$product_id,$quantity,
							$status_no,$product_status,$application_id,$card_status,
							$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
							$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
							$check_repeat,$web_promo,$point1='',$point2='',$discount='0',$coupon_discount=''){
			/**
			 * @name setCshTemp
			 * @desc store data temp before insert odiary
			 * @param String $promo_code :
			 * @param String $employee_id :
			 * @param String $member_no :
			 * @param String $product_id :
			 * @param Integer $quantity :
			 * @param Interger $point :
			 * @param Float $discount :
			 * @param Char $status_no :
			 * @param Char $product_status :
			 * @modify 16102014
			 * @return  
			 */
								
			//*WR22072014
			$this->discount=$discount;
			if(trim($member_no)=='' && $coupon_percent=='' && $application_id!='OPPL300' && $application_id!='OPMGMC300'){
				//*WR20012015 for Line OPPL300
				//*WR16102014 coom back to do
				$discount_percent='';
				$member_percent1='';
				$member_percent2='';
				$co_promo_percent='';
				$coupon_percent='';
			}
			
			//*WR19032014 resolt member_discount of bill 04
			if($status_no=='04'){
				$member_percent1='';
				$member_percent2='';
			}
								
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($promo_id!=''){
				$this->promo_code=$promo_id;
			}else if($application_id!=''){
				$this->promo_code=$application_id;
			}		
			$this->doc_tp=$doc_tp;
			$this->promo_tp=$promo_tp;	
			$this->promo_st=$promo_st;
			$this->product_id=$product_id;
			$this->employee_id=$employee_id;
			$this->member_no=$member_no;
			$this->quantity=$quantity;//จำนวนสินค้าที่มาจากหน้าขาย	
			
			//*WR10022014 auto add free product 25096			
			if($this->promo_code=='BBSAM1216' && $product_id=='60294'){
				$this->promo_tp='F';
				$this->quantity=1;
				$check_repeat='Y';
				//echo "OK1";
			}
			if($this->promo_code=='BBSAM1216' ){
				$this->product_id="60294";
				$this->promo_tp='F';
				$this->quantity=1;
				$check_repeat='N';
				//echo "OK2";
			}			
			//exit();
			//if
			/////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
			
			if($this->promo_tp=='F'){
					$this->discount_percent='';			
					$this->member_percent1='';
					$this->member_percent2='';
					$this->co_promo_percent='';
					$this->coupon_percent='';
					$this->coupon_discount='';//*WR16102014
			}else{
					$this->discount_percent=$discount_percent;			
					$this->member_percent1=$member_percent1;//% ค่าส่วนลดสมาชิกที่1	
					$this->member_percent2=$member_percent2;
					$this->co_promo_percent=$co_promo_percent;
					$this->coupon_percent=$coupon_percent;
					
					//*WR16102014
					if($coupon_percent=='PERCENT'){
						$this->coupon_discount=$coupon_discount/100;//*WR16102014
					}else{
						$this->coupon_discount=$coupon_discount;//*WR16102014
					}
					
			}
			
			$this->status_no=$status_no;
			$arr_product_status=explode("-",$product_status);
			$this->product_status=$arr_product_status[0];		
			
			//check magazine repeat
//			$sql_rpt="SELECT COUNT(*) 
//							FROM trn_tdiary2_sl 
//								WHERE
//										 `corporation_id` ='$this->m_corporation_id' AND
//										 `company_id` ='$this->m_company_id' AND
//										 `branch_id` ='$this->m_branch_id' AND
//										 `doc_date` ='$this->doc_date' AND										
//										 `product_id` ='$this->product_id' AND 
//										 `price` ='0.0000' AND					  	
//										 `computer_no` ='$this->m_computer_no' AND 
//										 `computer_ip` ='$this->m_com_ip'";
//			$n_rpt=$this->db->fetchOne($sql_rpt);
//			if($n_rpt>0){
//				return "1#0";
//				exit();
//			}
			
			//chkeck repeat
			if($check_repeat=='Y'){
				$sql_rpt="SELECT COUNT(*) 
								FROM trn_tdiary2_sl 
									WHERE
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `doc_date` ='$this->doc_date' AND
											 `promo_code` ='$this->promo_code'	 AND 
											 `product_id` ='$this->product_id' AND 
											 `computer_no` ='$this->m_computer_no' AND 
											 `computer_ip` ='$this->m_com_ip'";
				$n_rpt=$this->db->fetchOne($sql_rpt);
				if($n_rpt>0){
					return "3#0";
					exit();
				}
			}
			//$this->product_id=parent::getProduct($this->product_id,$this->quantity,$status_no);//*WR10012013 prepend $status_no for product tester
			$this->product_id=parent::getProduct($this->product_id,$this->quantity,$status_no,$promo_tp);//*WR18092013 unlock bill dn 
			$arr_product=parent::browsProduct($this->product_id);	
			
			$proc_status=0;
			if(!empty($arr_product)){
				//check amout
				$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
				if($balance<$this->quantity){
					//stock ขาด
					$proc_status='2';
				}else{
					$this->product_name=$arr_product[0]['name_product'];			
					$this->price=$arr_product[0]['price'];
					$this->unit=$arr_product[0]['unit'];
					if($this->promo_tp=='F' || ($this->status_no=='05' && $card_status=='C2')){
						//บัตรเสีย ชำรุด
						$this->price=0.00;
					}
					$this->tax_type=$arr_product[0]['tax_type'];							
					$this->amount=$this->price * $this->quantity;
					if(intval($this->amount)==0){
						$this->doc_tp="DN";
				 	}
					$this->get_point=$get_point;//ใด้รับคะแนนหรือไม่
										
					if(intval($this->member_percent1)!=0 || intval($this->member_percent2)!=0){
						$this->discount_member='Y';//คำนวณส่วนลดสมาชิก
					}else{
						$this->discount_member='N';//คำนวณส่วนลดสมาชิก
					}					
						
					if($this->member_no!='' && $this->discount_member=='Y'){					
						$this->member_discount1=$this->price*$this->quantity*($this->member_percent1/100);
						if(intval($this->member_percent2)!=0){
							$amount_by_discount=$this->amount-$this->member_discount1;
							$this->member_discount2=$amount_by_discount*($this->member_percent2/100);
						}else{
							$this->member_discount2=0.0000;
						}						
					}else{
						$this->member_discount1=0.0000;								
					}
					
					//*WR16102014 for mobile coupon coom back to do
					if($this->coupon_percent=='PERCENT'){
						$this->coupon_discount=$this->amount*$this->coupon_discount;
					}
					
					//*WR24112014 new idea for support discount promotion
					if($this->discount_percent=='PERCENT'){
						$this->discount=$this->amount*($this->discount/100);
					}
					
					//*WR15022012 to support multi discount
					$this->total_discount=$this->discount+
												$this->member_discount1+
												$this->member_discount2+
												$this->co_promo_discount+
												$this->coupon_discount+
												$this->special_discount+
												$this->other_discount;
					$this->net_amt=$this->amount-$this->total_discount;
					//----------------------------------------------------							
					//detail table trn_tdiary2_sl
					$sql_doc='SELECT 
									stock_st,branch_no 
								FROM 
									`com_doc_no` 
								WHERE 
									corporation_id="'.$this->m_corporation_id.'" AND 
									company_id="'.$this->m_company_id.'" AND
									branch_id="'.$this->m_branch_id.'" AND
									doc_tp="'.$this->doc_tp.'"';
					$rowdoc=$this->db->fetchAll($sql_doc);
					if(count($rowdoc)>0){
						$this->m_branch_no=$rowdoc[0]['branch_no'];
						$this->stock_st=$rowdoc[0]['stock_st'];
					}
									
					$this->doc_time=date("H:i:s");
					$sql_seq="SELECT MAX(seq)  FROM trn_tdiary2_sl 
										WHERE 
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `doc_date` ='$this->doc_date' AND
											`computer_no` ='$this->m_computer_no' AND  
											`computer_ip` ='$this->m_com_ip'";
					$max_seq=$this->db->fetchOne($sql_seq);
					$this->seq=$max_seq+1;
										
					//joke
					$sql_cseq="SELECT seq_set
										FROM trn_tdiary2_sl 
										WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `promo_code`='$this->promo_code' AND
												  `computer_no` ='$this->m_computer_no' AND  
												  `computer_ip` ='$this->m_com_ip'";			
				$row_cseq=$this->db->fetchAll($sql_cseq);
				if(count($row_cseq)>0){
						$seq_set=$row_cseq[0]['seq_set'];
				}else{
						$sql_maxseq="SELECT MAX(seq_set) AS max_seq_set
										FROM trn_tdiary2_sl
											WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND 
												  `computer_no` ='$this->m_computer_no' AND  
												  `computer_ip` ='$this->m_com_ip'";	
						$max_seq_set=$this->db->fetchOne($sql_maxseq);			
						$seq_set=$max_seq_set+1;
					}		
					
					$sql_pro_seq="SELECT MAX(promo_seq) AS max_promo_seq
										FROM trn_tdiary2_sl
											WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `promo_code`='$this->promo_code' AND
												  `computer_no` ='$this->m_computer_no' AND  
												  `computer_ip` ='$this->m_com_ip'";	
					$max_promo_seq=$this->db->fetchOne($sql_pro_seq);
					$this->promo_seq=$max_promo_seq+1;
					
					$sql_insert="INSERT INTO trn_tdiary2_sl
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `member_no` ='$this->member_no',
									  `doc_no` ='',
									  `doc_tp` ='$this->doc_tp',
									  `status_no`='$this->status_no',
									   `web_promo` ='$web_promo',
									  `seq` ='$this->seq',
									  `seq_set` ='$seq_set',
									  `promo_code` ='$this->promo_code',
									  `promo_seq` ='$this->promo_seq',
									  `promo_st` ='$this->promo_st',
									  `promo_tp` ='',
									  `product_id`='$this->product_id',				
									  `name_product`='$this->product_name',			
									  `unit`='$this->unit',			  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='$this->price',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='$this->discount',								  
									  `member_discount1`='$this->member_discount1',
									  `member_discount2`='$this->member_discount2',		  
									  `net_amt`='$this->net_amt',
									  `product_status`='$this->product_status',
									  `get_point`='$this->get_point',
									  `discount_member`='$this->discount_member',
									  `cal_amt`='Y',											  
									  `discount_percent`='$this->discount_percent',
									  `member_percent1`='$this->member_percent1',
									  `member_percent2`='$this->member_percent2',
									  `co_promo_percent`='$this->co_promo_percent',
									  `coupon_percent`='$this->coupon_percent',									  
									   `coupon_discount`='$this->coupon_discount',									  
									  `tax_type`='$this->tax_type',
									  `computer_no`='$this->m_computer_no',
									  `computer_ip`='$this->m_com_ip',
									   `point1`='$point1',
									  `point2`='$point2',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert);					
					parent::decreaseStock($this->product_id,$this->quantity);
					$proc_status=1;
					
					//*WR 07052015 support member get member
					if($this->promo_code=='OPMGMC300'){
						$objMember = new Model_Member();
						$objMember->updDstCshTemp($this->promo_code,$discount_percent,$discount);
					}	
					
				}
			}
	
			return $proc_status."#".$this->amount;
		}//func
		
		function getCshTemp($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @name getCshTemp
			 * @desc
			 * @param $order_no is
			 * @param $page is
			 * @param $qtype is
			 * @param $query is
			 * @param $rp is
			 * @param $sortname is
			 * @param $sortorder is
			 * @return json format string of data.
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");//trn_promotion_tmp1
			$this->order_no=$order_no;
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";			
			$sql_numrows = "SELECT COUNT(*) FROM trn_tdiary2_sl 
										WHERE 
											`corporation_id`='$this->m_company_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`computer_no`='$this->m_computer_no' AND
				 							`computer_ip`='$this->m_com_ip' AND
											`doc_date`='$this->doc_date' ";
			$total=$this->db->fetchOne($sql_numrows);
			//test start
			$sql_product="SELECT 
											id,
											promo_code,
											promo_st,
											product_id,
											name_product,
											unit,
											product_status,
											get_point,
											discount_member,
											quantity,
											price,
										( discount  + 
									 	member_discount1 +
									 	member_discount2 + 
									 	co_promo_discount + 
									 	coupon_discount + 
									 	special_discount+ 
									 	other_discount) AS sum_discount,
											amount,
											net_amt
									FROM trn_tdiary2_sl 
									WHERE
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date'					
										$sort $limit";			
			$arr_list=$this->db->fetchAll($sql_product);			
			//test start
			if(count($arr_list)>0){
				$data['page'] = $page;
				$data['total'] = $total;
				//$i=1;
				$i=(($page*$rp)-$rp)+1;
				foreach($arr_list as $row){	
					$status_pro_tmp="";//temp 23082011
					/*
					if($row['tax_type']=='V'){
						$imgVat="<img src='/sales/img/icon_checkbox_black_16x16.png' border='0' align='absmiddle'>";
					}else{
						$imgVat="";
					}
					*/
					$discount_tmp=$row['sum_discount'];
					$s_pos=strrpos($discount_tmp,'.');
					if($s_pos>0){
						$s_pos+=3;
						$discount_tmp=substr($discount_tmp,0,$s_pos);			
					}
					//$discount_tmp=number_format($row['sum_discount'],'2','.',',');
					$rows[] = array(
								"id" => $row['id'],
								"cell" => array(
											$i,
											$row['promo_st'],
											$row['promo_code'],
											$row['product_id'],
											$row['name_product'],
											intval($row['quantity']),
											number_format($row['price'],'2','.',','),
											number_format($row['amount'],'2','.',','),
											$discount_tmp,				
											number_format($row['net_amt'],'2','.',','),
								)
							);
					$i++;
				}//foreach
				$data['rows'] = $rows;	
			}else{
				$data=array();
			}			
			return $data;			
		}//func
		
		function getPromotionTemp($page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @name getCshTemp
			 * @desc
			 * @param $order_no is
			 * @param $page is
			 * @param $qtype is
			 * @param $query is
			 * @param $rp is
			 * @param $sortname is
			 * @param $sortorder is
			 * @return json format string of data.
			 * @modify 27102011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";			
			$sql_numrows = "SELECT COUNT(*) 
									FROM trn_promotion_tmp1 
									WHERE 
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' ";
			/*
			$stmt_numrows=$this->db->query($sql_numrows);			
			$total = $stmt_numrows->rowCount();
			*/
			$total=$this->db->fetchOne($sql_numrows);
			//test start
			$sql_product="SELECT 
											id,
											promo_code,
											promo_st,
											product_id,
											name_product,
											unit,
											product_status,
											get_point,
											discount_member,
											quantity,
											price,
										( discount  + 
									 	member_discount1 +
									 	member_discount2 + 
									 	co_promo_discount + 
									 	coupon_discount + 
									 	special_discount+ 
									 	other_discount) AS sum_discount,
											amount,
											net_amt
									FROM trn_promotion_tmp1 
									WHERE
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date'					
										$sort $limit";			
			$arr_list=$this->db->fetchAll($sql_product);
			//test start
		
			if(count($arr_list)>0){
				$data['page'] = $page;
				$data['total'] = $total;
				//$i=1;
				$i=(($page*$rp)-$rp)+1;
				foreach($arr_list as $row){	
					$status_pro_tmp="";//temp 23082011
					/*
					if($row['tax_type']=='V'){
						$imgVat="<img src='/sales/img/icon_checkbox_black_16x16.png' border='0' align='absmiddle'>";
					}else{
						$imgVat="";
					}
					*/
			
					$discount_tmp=number_format($row['sum_discount'],'2','.',',');
					
					$rows[] = array(
								"id" => $row['id'],
								"cell" => array(
											$i,
											$row['promo_st'],
											$row['promo_code'],
											$row['product_id'],
											$row['name_product'],
											intval($row['quantity']),
											number_format($row['price'],'2','.',','),
											number_format($row['amount'],'2','.',','),
											$discount_tmp,				
											number_format($row['net_amt'],'2','.',','),
								)
							);
					$i++;
				}//foreach
				$data['rows'] = $rows;
			}else{
				$data=array();
			}			
			return $data;			
		}//func
		
		public function getPageTotal($rp,$flg){
			/**
			 * @name getPageTotal
			 * @desc
			 * @param $rp is row per page
			 * @return $cpage is total of page
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($flg=='cashier'){
				$tbl_temp="trn_tdiary2_sl";
			}else if($flg=='promotion'){
				$tbl_temp="trn_promotion_tmp1";
			}
			$sql_row = "SELECT count(*) 
							FROM `$tbl_temp` 
							WHERE 
								`corporation_id`='$this->m_company_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no`='$this->m_computer_no' AND
	 							`computer_ip`='$this->m_com_ip' AND
								doc_date='$this->doc_date'";
			$crow=$this->db->fetchOne($sql_row);
			$cpage=ceil($crow/$rp);
			return $cpage;
		}//func
				
		function getProductAutoComplete($term=""){
			/**
			 * @desc
			 * @param String $term : word to find auto complete
			 * @comment : ติดปัญหาต้องหาจำนวนรวมจาก table temp ทั้งสองตาราง
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			if($term=="") return false;
			$arr_date=explode("-",$this->doc_date);
			$this->year=$arr_date[0];
			$this->month=intVal($arr_date[1]);
			
			//step1
			$sql_stock="SELECT 
								a.product_id,
								a.begin,
								a.onhand,
								b.name_product,
								b.unit 
							FROM com_stock_master as a LEFT JOIN com_product_master AS b
								ON(a.product_id=b.product_id)
							WHERE 
								a.corporation_id='$this->m_corporation_id' AND
								a.company_id='$this->m_company_id' AND (
								b.product_id LIKE '$term%' OR
								b.name_product LIKE '$term%')  AND
								a.year='$this->year' AND
								a.month='$this->month'";
			$row_stock=$this->db->fetchAll($sql_stock);
			$arr_stk=array();
			if(count($row_stock)>0){		
				//$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				$i=0;
				foreach($row_stock as $dataStk){
					$onhand_on_stock=$dataStk['onhand'];//onhand on stock
					$begin=$dataStk['begin'];
					$product_id=$dataStk['product_id'];					
					$arr_stk[$i]['product_id']=$dataStk['product_id'];
					$arr_stk[$i]['name_product']=$dataStk['name_product'];
					$arr_stk[$i]['beging']=$dataStk['begin'];
					$arr_stk[$i]['unit']=$dataStk['unit'];			
					$arr_stk[$i]['onhand']=$dataStk['onhand'];
					$i++;
				}
			}
		
			return $arr_stk;
		}//func
		
		
		function getOnhandCurrent($product_id){
			/**
			 * 
			 * ติดปัญหาต้องหาจำนวนรวมจาก table temp ทั้งสองตาราง
			 * @var unknown_type
			 */
			$product_id=parent::setBarcodeToProductID($product_id);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			$arr_docdate=explode('-',$this->doc_date);
			$this->year=intval($arr_docdate[0]);
			$this->month=intval($arr_docdate[1]);
			//step1
			$sql_stock="SELECT a.product_id,a.begin,a.onhand,b.name_product 
					FROM com_stock_master as a LEFT JOIN com_product_master AS b
					ON(a.product_id=b.product_id)
					WHERE 
						a.year='$this->year' AND
						a.month='$this->month' AND
						a.product_id='$product_id'";		
			$row_stock=$this->db->fetchAll($sql_stock);
			return $row_stock;
		}//func
		
		
		function getFixStock($product_id){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_fix_stock");
			$sql_fix="SELECT quantity_normal FROM `trn_fix_stock` WHERE branch_id='$this->m_branch_id' AND product_id='$product_id'";
			$quantity_normal=$this->db->fetchOne($sql_fix);
			return $quantity_normal;
		}//func
		
		
		function checkStock($arr_stock,$doc_tp){
			/**
			 * @desc trn_promotion_tmp1
			 * @return
			 */
			if(empty($arr_stock)) return false;
			$arr_chkstock=array();			
			$arr_doctp=explode(",",$doc_tp);
			$str_doc_tp='';
			for($i=0;$i<count($arr_doctp);$i++){
				$str_doc_tp.="'".$arr_doctp[$i]."',";
			}
			$p=strrpos($str_doc_tp,",");
			$str_doc_tp=substr($str_doc_tp,0,$p);
			$start_date=$this->year."-".substr(("00".$this->month),-2)."-01";			
			foreach($arr_stock as $data){
				$product_id=$data['product_id'];
				if($doc_tp=='AI' || $doc_tp=='AO'){
					$sql_chkstock="SELECT sum( quantity ) AS sum_quantity
									FROM `trn_diary2`
									WHERE 
										doc_date BETWEEN '$start_date' AND '$this->doc_date' AND
										product_id = '$product_id' AND
										flag<>'C' AND
										doc_tp IN($str_doc_tp)";
				}else{
					$sql_chkstock="SELECT xtable.product_id,sum(xtable.quantity) as sum_quantity FROM
										(
											SELECT product_id,quantity FROM `trn_diary2` 
														WHERE 
																`corporation_id`='$this->m_company_id' AND
																`company_id`='$this->m_company_id' AND
																`branch_id`='$this->m_branch_id' AND
																`doc_date` BETWEEN '$start_date' AND '$this->doc_date' AND
																`doc_tp` IN($str_doc_tp) AND
																`product_id`='$product_id'  AND
																`flag`<>'C'  
											union all
											SELECT product_id,quantity FROM `trn_tdiary2_sl` 
														WHERE 
																`corporation_id`='$this->m_company_id' AND
																`company_id`='$this->m_company_id' AND
																`branch_id`='$this->m_branch_id' AND
																`doc_date` BETWEEN '$start_date' AND '$this->doc_date' AND
																`doc_tp` IN($str_doc_tp) AND
																`product_id`='$product_id' AND
																`flag`<>'C'   
											union all
											SELECT product_id,quantity FROM `trn_promotion_tmp1` 
														WHERE 
																`corporation_id`='$this->m_company_id' AND
																`company_id`='$this->m_company_id' AND
																`branch_id`='$this->m_branch_id' AND
																`doc_date` BETWEEN '$start_date' AND '$this->doc_date' AND
																`doc_tp` IN($str_doc_tp) AND
																`product_id`='$product_id'  AND
																`flag`<>'C'  
										) as xtable
										GROUP BY xtable.product_id";
				}
				
				$row_stock=$this->db->fetchAll($sql_chkstock);
				if(count($row_stock)>0){
					$arr_chkstock[$product_id]=$row_stock[0]['sum_quantity'];
				}else{
					$arr_chkstock[$product_id]=0;
				}
					
			}
			
			return $arr_chkstock;
		}//func
		
		function getSumCshTemp($xpoint,$flg_chk_point,$flg_tbl,$cn_amount){
			/**
			 * @name getSumInv 19072013
			 * @desc get summary info data of sale transaction			
			 * @param Integer $xpoint :ตัวคูณคะแนนปกติ บิลแรกหลังสมัครสมาชิก
			 * @param Char flg_chk_point: 'Y' สมาชิกซื้อแต่ไม่ใช่บิล 01(สมัครสมาชิกใหม่) 
			 * @return array of row member
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($flg_tbl=='cashier'){
				$tbl_temp="trn_tdiary2_sl";
			}else if($flg_tbl=='promotion'){
				$tbl_temp="trn_promotion_tmp1";
			}	
			$sql_suminv="SELECT 
								SUM(quantity) AS sum_quantity,	
							 	SUM(discount) AS sum_discount,
							 	SUM(member_discount1)  AS  sum_member_discount1,
							 	SUM(member_discount2)  AS sum_member_discount2,						 	
							 	SUM(co_promo_discount)  AS sum_co_promo_discount, 
							 	SUM(coupon_discount)  AS sum_coupon_discount,
							 	SUM(special_discount) AS sum_special_discount,
							 	SUM(other_discount) AS sum_other_discount,		
							 	SUM(special_discount) AS sum_special_discount,				 	 
								SUM(amount) AS sum_amount,
								SUM(net_amt) AS sum_net_amt,
								SUM(point1) AS used_point
							FROM 
								`$tbl_temp`
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no`='$this->m_computer_no' AND
 								`computer_ip`='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";
			$row_l2h=$this->db->fetchAll($sql_suminv);
			if(count($row_l2h)>0){				
				$discount=parent::getSatang($row_l2h[0]['sum_discount'],'up');
				$member_discount1= parent::getSatang($row_l2h[0]['sum_member_discount1'],'up');			
			 	$member_discount2= parent::getSatang($row_l2h[0]['sum_member_discount2'],'up');						 	
			 	$co_promo_discount= parent::getSatang($row_l2h[0]['sum_co_promo_discount'],'up'); 
			 	$coupon_discount= parent::getSatang($row_l2h[0]['sum_coupon_discount'],'up');
			 	$special_discount= parent::getSatang($row_l2h[0]['sum_special_discount'],'up');
			 	$other_discount= parent::getSatang($row_l2h[0]['sum_other_discount'],'up');
			 	$amount=$row_l2h[0]['sum_amount'];
			 	$sum_discount=$discount+$member_discount1+$member_discount2+$co_promo_discount+$coupon_discount+$special_discount+$other_discount;
			 	$net_amt=$amount - $sum_discount;
			 	$net_chk_point=parent::getSatang($net_amt,'normal');
			 	$row_l2h[0]['sum_discount'] = $sum_discount;
	            $row_l2h[0]['sum_member_discount1'] = $member_discount1;
	            $row_l2h[0]['sum_member_discount2'] =$member_discount2;
	            $row_l2h[0]['sum_co_promo_discount'] = $co_promo_discount;
	            $row_l2h[0]['sum_coupon_discount'] =$coupon_discount;
	            $row_l2h[0]['sum_special_discount'] =$special_discount;
	            $row_l2h[0]['sum_other_discount'] =$other_discount;
	            $row_l2h[0]['sum_amount'] = $amount;
	            $row_l2h[0]['sum_net_amt'] = $net_chk_point;
			 	if($cn_amount!=''){
					$cn_amount=(float)$cn_amount;
					$net_chk_point=(float)$net_chk_point;
					$net_chk_point=($net_chk_point-$cn_amount);
				}
				$xpoint=intval($xpoint);
				if($xpoint==0 || $xpoint=='') $xpoint=1;
				$point=0;
				if($flg_chk_point=='yes'){
					$point1=parent::getPoint1($net_chk_point);
					$point2=parent::getPoint2($net_chk_point);		
					$point1*=$xpoint;
					$point=$point1+$point2;
				}				
				$row_l2h[0]['point_receive']=$point;			
				
				////*WR20120901 test for 50BTO1P
				$sql_pused="SELECT SUM(discount) AS promo_discount
										FROM `$tbl_temp`
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
		 								`computer_ip`='$this->m_com_ip' AND
		 								`promo_code`='50BTO1P' AND
										`doc_date`='$this->doc_date'";
				$mp_sum_discount=$this->db->fetchOne($sql_pused);//discount รวมของ 50BTO1P
				if($mp_sum_discount>0){					
					////////////////////////////////////////////////////////////////////////////////////
					$mp_sum_net_amt=$net_chk_point;//net รวมทั้งหมด				
					$re_mp_discount=($mp_sum_discount/5)*50;					
					$net_balance=$mp_sum_net_amt-$re_mp_discount;				
					$mp_point_net=parent::getPoint1($net_balance);//คะแนนที่เหลือ					
					$mp_point_net2=parent::getPoint2($net_balance);
					$total_point=$mp_point_net+$mp_point_net2;
					$px1=parent::getPoint1($mp_sum_net_amt);//normal point
					$px2=parent::getPoint2($mp_sum_net_amt);//special point					
					$mp_point_receive=$px1+$px2;
					$mp_point_used=$mp_point_receive-$total_point;//คะแนนที่ใช้ไป
					//$redeem_point=-1*$mp_point_used;
					$row_l2h[0]['mp_point_receive']=$mp_point_receive;
					$row_l2h[0]['mp_point_used']=$mp_point_used;
					$row_l2h[0]['mp_point_net']=$total_point;
					////////////////////////////////////////////////////////////////////////////////////
				}else{
					$row_l2h[0]['mp_point_receive']='0';
					$row_l2h[0]['mp_point_used']='0';
					$row_l2h[0]['mp_point_net']='0';
				}
				////*WR20120901 test for 50BTO1P
				
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcsh',$row_l2h[0],'yes');	
				return $json;				
			}
		}//func
		
		function getSumCshTemp22($xpoint,$flg_chk_point,$flg_tbl,$cn_amount){
			/**
			 * @name getSumInv
			 * @desc get summary info data of sale transaction			
			 * @param Integer $xpoint :ตัวคูณคะแนนปกติ บิลแรกหลังสมัครสมาชิก
			 * @param Char flg_chk_point: 'Y' สมาชิกซื้อแต่ไม่ใช่บิล 01(สมัครสมาชิกใหม่) 
			 * @return array of row member
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($flg_tbl=='cashier'){
				$tbl_temp="trn_tdiary2_sl";
			}else if($flg_tbl=='promotion'){
				$tbl_temp="trn_promotion_tmp1";
			}
	
			$sql_suminv="SELECT 
								SUM(quantity) AS sum_quantity,
								SUM( discount + 
							 	member_discount1+
							 	member_discount2+ 
							 	co_promo_discount+ 
							 	coupon_discount+ 
							 	special_discount+ 
							 	other_discount) AS sum_discount,
							 	sum(amount) AS sum_amount,
							 	sum(net_amt) AS sum_net_amt,
							 	status_no	
							FROM 
								`$tbl_temp`
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no`='$this->m_computer_no' AND
 								`computer_ip`='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";
			$row=$this->db->fetchAll($sql_suminv);
			if(count($row)>0){
				//---------- start cal vat
				//update 20/07/2012
				$sum_discount=number_format($row[0]['sum_discount'],"2",".","");
				$sum_amount=number_format($row[0]['sum_amount'],"2",".","");
				$sum_amount=parent::getSatang($sum_amount,'up');
				$sum_discount=parent::getSatang($sum_discount,'up');		
				$row[0]['sum_discount']=$sum_discount;
				$row[0]['sum_net_amt']=$sum_amount-$sum_discount;
				$net_chk_point=$row[0]['sum_net_amt'];
				if($cn_amount!=''){
					$cn_amount=(float)$cn_amount;
					$net_chk_point=(float)$net_chk_point;
					$net_chk_point=($net_chk_point-$cn_amount);
				}		
				$point=0;
				if($flg_chk_point=='yes'){
					$point1=parent::getPoint1($net_chk_point);
					$point2=parent::getPoint2($net_chk_point);				
					$point1*=$xpoint;
					$point=$point1+$point2;
				}				
				$row[0]['point_receive']=$point;			
				
				////*WR20120901 test for 50BTO1P
				$sql_pused="SELECT SUM(discount) AS promo_discount
									FROM `$tbl_temp`
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
	 								`computer_ip`='$this->m_com_ip' AND
	 								`promo_code`='50BTO1P' AND
									`doc_date`='$this->doc_date'";
				$mp_sum_discount=$this->db->fetchOne($sql_pused);//discount รวมของ 50BTO1P
				if($mp_sum_discount>0){					
					////////////////////////////////////////////////////////////////////////////////////
					$mp_sum_net_amt=$row[0]['sum_net_amt'];//net รวมทั้งหมด
					//$mp_sum_discount=$row_pused[0]['promo_discount'];//discount รวมของ 50BTO1P
					$re_mp_discount=($mp_sum_discount/5)*50;
					
					$net_balance=$mp_sum_net_amt-$re_mp_discount;				
					$mp_point_net=parent::getPoint1($net_balance);//คะแนนที่เหลือ
					
					$px1=parent::getPoint1($mp_sum_net_amt);//normal point
					$px2=parent::getPoint1($mp_sum_net_amt);//special point
					
					$mp_point_receive=$px1+$px2;
					$mp_point_used=$mp_point_receive-$mp_point_net;//คะแนนที่ใช้ไป
					
					$row[0]['mp_point_receive']=$mp_point_receive;
					$row[0]['mp_point_used']=$mp_point_used;
					$row[0]['mp_point_net']=$mp_point_net;
					
					////////////////////////////////////////////////////////////////////////////////////
				}else{
					//$row[0]['point_used']='0';
					$row[0]['mp_point_receive']='0';
					$row[0]['mp_point_used']='0';
					$row[0]['mp_point_net']='0';
				}
				////*WR20120901 test for 50BTO1P
				
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcsh',$row[0],'yes');	
				return $json;				
			}
		}//func	
		
		
		function chkProLockDel(){
			/**
			 * @desc
			 * @return
			 */
			//*WR19122013
			$res_tbltemp=parent::countDiaryTemp();
			$arr_tbltemp=explode('#',$res_tbltemp);
			$tbl_temp=$arr_tbltemp[1];
			//*WR20120904
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$str_lock='LOCKDEL';
			$sql_chklock="SELECT COUNT(*) 
								FROM $tbl_temp 
								WHERE
										`corporation_id`='$this->m_corporation_id' AND 
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
										`computer_ip`='$this->m_com_ip' AND	
										`doc_date`='$this->doc_date' AND
										`promo_code` IN('50BTO1P')";//,'OM02071113','OM02081113','OM02091113'
			$n_chklock=$this->db->fetchOne($sql_chklock);
			if($n_chklock<1){
				$str_lock='N';
			}
			return $str_lock;			
		}//func
		
		function removeGridTemp($str_items){
			/**
			 * @name removeGridTemp
			 * @desc
			 * @param $str_items : set of id to delete
			 * @lastmodify 30062011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$tbl_temp="trn_tdiary2_sl";
			if($str_items!=''){
				try{					
					$arr_data=explode('#',$str_items);
					//for play promo
					$sql_chk_tmp1="SELECT COUNT(*) 
											FROM trn_promotion_tmp1
												WHERE 
													`corporation_id`='$this->m_corporation_id' AND 
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`computer_no`='$this->m_computer_no' AND
													`computer_ip`='$this->m_com_ip' AND	
													`doc_date`='$this->doc_date'";
					$n_chk_tmp1=$this->db->fetchOne($sql_chk_tmp1);
					if($n_chk_tmp1>0){
						$tbl_temp="trn_promotion_tmp1";
						foreach($arr_data as $id_delete){
							if($id_delete=='') continue;
								$sql_chkdel="SELECT product_id,quantity,promo_code,promo_seq,seq,seq_set 
													FROM trn_promotion_tmp1 
													WHERE id='$id_delete' AND doc_date='$this->doc_date'";						
								$row_chkdel=$this->db->fetchAll($sql_chkdel);						
								if(count($row_chkdel)>0){
									$seq=$row_chkdel[0]['seq'];		
									$seq_set=$row_chkdel[0]['seq_set'];	
									$sql_seq="SELECT product_id,quantity 
													FROM trn_promotion_tmp1 
														WHERE 
															`doc_date`='$this->doc_date' AND
															`seq_set`='$seq_set' AND 
															`computer_no`='$this->m_computer_no' AND
															`computer_ip`='$this->m_com_ip'	";		
									$row_seq=$this->db->fetchAll($sql_seq);					
									foreach($row_seq as $dataSeq){
											$this->product_id=$dataSeq['product_id'];
											$this->quantity=$dataSeq['quantity'];
											$sql_del="DELETE FROM `trn_promotion_tmp1` 
															WHERE 
																	`product_id`='$this->product_id' AND 
																	`seq_set`='$seq_set' AND 
																	`doc_date`='$this->doc_date' AND 
																	`computer_no`='$this->m_computer_no' AND
																	`computer_ip`='$this->m_com_ip'";								
											$stmt_del=$this->db->query($sql_del);
											if($stmt_del){
												$this->increaseStock($this->product_id,$this->quantity);
											}
									}//foreach									
									
								}
						}
					}else{	
						$tbl_temp="trn_tdiary2_sl";				
							foreach($arr_data as $id_delete){
								if($id_delete=='') continue;
								$sql_chkdel="SELECT product_id,quantity,promo_code,promo_seq,seq 
													FROM trn_tdiary2_sl 
													WHERE id='$id_delete' AND doc_date='$this->doc_date'";						
								$row_chkdel=$this->db->fetchAll($sql_chkdel);						
								if(count($row_chkdel)>0){	
									if($row_chkdel[0]['promo_code']!='' && $row_chkdel[0]['promo_seq']!='0'){
										$sql_del="DELETE FROM `trn_tdiary2_sl` 
													 WHERE 
													 		promo_code='".$row_chkdel[0]['promo_code']."' 
															AND promo_seq='".$row_chkdel[0]['promo_seq']."'
															AND computer_no='".$this->m_computer_no."'
															AND computer_ip='".$this->m_com_ip."'
															AND	doc_date='$this->doc_date'";
									}else{						
										$sql_del="DELETE FROM `trn_tdiary2_sl` WHERE id='$id_delete' AND doc_date='$this->doc_date'";
									}
									$stmt_del=$this->db->query($sql_del);
									if($stmt_del){
										$this->product_id=$row_chkdel[0]['product_id'];
										$this->quantity=$row_chkdel[0]['quantity'];
										$this->increaseStock($this->product_id,$this->quantity);
									}
								}
							}//foreach
					}
					
				}catch(Zend_Db_Exception $e){
					echo $e->getMessage();
				}
			}
			return $tbl_temp;
		}//func
		
		function cancelPromotion(){
			/**
			 * @desc 
			 * @param
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$status_cancel="1";
			$stmt_cancel=$this->db->select()
									->from('trn_promotion_tmp1',array('id','product_id','quantity'))
									->where('corporation_id=?',$this->m_corporation_id)
									->where('company_id=?',$this->m_company_id)
									->where('branch_id=?',$this->m_branch_id)
									->where('computer_no=?',$this->m_computer_no)
									->where('computer_ip=?',$this->m_com_ip)
									->where('doc_date=?',$this->doc_date);
			$row_cancel=$stmt_cancel->query()->fetchAll();
			if(count($row_cancel)>0){
				$this->db->beginTransaction();
				try{
					foreach($row_cancel as $data){
						$this->increaseStock($data['product_id'],$data['quantity']);		
					}
					parent::TrancateTable("trn_promotion_tmp1");
					$stmt_cancel2=$this->db->select()
									->from('trn_tdiary2_sl',array('id','product_id','quantity'))
									->where('corporation_id=?',$this->m_corporation_id)
									->where('company_id=?',$this->m_company_id)
									->where('branch_id=?',$this->m_branch_id)
									->where('computer_no=?',$this->m_computer_no)
									->where('computer_ip=?',$this->m_com_ip)
									->where('doc_date=?',$this->doc_date);
					$row_cancel2=$stmt_cancel2->query()->fetchAll();
					foreach($row_cancel2 as $data2){
						$this->decreaseStock($data2['product_id'],$data2['quantity']);	
					}
					//commit trans
					$this->db->commit();
				}catch(Zend_Db_Exception $e){
					$e->getMessage();
					//rollback trans
					$this->db->rollBack();
					$status_cancel="0";
				}
			}
			return $status_cancel;
		}//func
		
		function cancelBill(){
			/**
			 * @desc cancel bill and update stock 
			 * @return status of process
			 * @lastmodify 06102011 support promotion
			 * @table trn_tdiary2_sl trn_promotion_tmp1
			 * ->where('doc_date=?',$this->doc_date); check computer_ip only
			 */			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$status_cancel="1";
			$stmt_cancel=$this->db->select()
									->from('trn_promotion_tmp1',array('id','product_id','quantity'))
									->where('corporation_id=?',$this->m_corporation_id)
									->where('company_id=?',$this->m_company_id)
									->where('branch_id=?',$this->m_branch_id)
									->where('computer_no=?',$this->m_computer_no)
									->where('computer_ip=?',$this->m_com_ip);
			$row_cancel=$stmt_cancel->query()->fetchAll();			
			if(count($row_cancel)>0){
				try{
					foreach($row_cancel as $data){
						$this->increaseStock($data['product_id'],$data['quantity']);		
					}
					//commit trans assume trn_tdiary2_sl union trn_promotion_tmp1
					parent::TrancateTable("trn_promotion_tmp1");
					parent::TrancateTable("trn_tdiary1_sl");
					parent::TrancateTable("trn_tdiary2_sl");
					parent::TrancateTable("trn_tdiary2_sl_val");					
				}catch(Zend_Db_Exception $e){
					$e->getMessage();
					//rollback trans
					$status_cancel="0";
				}
			}else{
				//not have promotion				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
				try{
					$stmt_cancel2=$this->db->select()
									->from('trn_tdiary2_sl',array('id','product_id','quantity'))
									->where('corporation_id=?',$this->m_corporation_id)
									->where('company_id=?',$this->m_company_id)
									->where('branch_id=?',$this->m_branch_id)
									->where('computer_no=?',$this->m_computer_no)
									->where('computer_ip=?',$this->m_com_ip);								
					$row_cancel2=$stmt_cancel2->query()->fetchAll();	
					foreach($row_cancel2 as $data2){
						$this->increaseStock($data2['product_id'],$data2['quantity']);	
					}
					//commit trans		
					parent::TrancateTable("trn_tdiary1_sl");				
					parent::TrancateTable("trn_tdiary2_sl");	
					parent::TrancateTable("trn_tdiary2_sl_val");					
				}catch(Zend_Db_Exception $e){
					$e->getMessage();
					//rollback trans
					$status_cancel="0";
				}
			}
			return $status_cancel;
		}//func
		
		function getNetVTold($tbl_target=''){
			/**
			 * @desc not complete please try again tomorrow!
			 * @param
			 * @return
			 * @lastmodify 17082011
			 * @for support play promotion is finish
			 * @วิธีนี้มารวม vat ทีหลังยอดสุทธิซึงเค้าไม่เอา เพราะสินค้ารวม vat แล้ว
			 */
			if($tbl_target=='') return false;			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn",$tbl_target);
			$stmt_vat=$this->db->select()
								->from($tbl_target,array('tax_type','net_amt'))
								->where('corporation_id=?',$this->m_corporation_id)
								->where('company_id=?',$this->m_company_id)
								->where('computer_no=?',$this->m_computer_no)
								->where('computer_ip=?',$this->m_com_ip)
								->where('branch_id=?',$this->m_branch_id);
			$row_vat=$stmt_vat->query()->fetchAll();
			$netvt=0.00;
			$taxvat=0.00;
			$net_amt_vt=0.00;
			$net_amt_nonvt=0.00;
			if(count($row_vat)>0){
				foreach($row_vat as $data){
					if($data['tax_type']=='V'){
						$net_amt_vt+=$data['net_amt'];						
					}else{
						$net_amt_nonvt+=$data['net_amt'];
					}
				}
				$taxvat=($net_amt_vt*7)/100;
				$net_amt_vt+=$taxvat;
			}
			$netvt=round(($net_amt_vt+$net_amt_nonvt),2);
			return number_format($netvt,2,".","");
		}//func
		
		function calVat_old(){
			/**
			 *@desc 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$stmt_vat=$this->db->select()
								->from('trn_tdiary2_sl',array(new Zend_Db_Expr('SUM(net_amt) as net_amt')))
								->where('corporation_id=?',$this->m_corporation_id)
								->where('company_id=?',$this->m_company_id)
								->where('branch_id=?',$this->m_branch_id)			
								->where('tax_type="V"');
			$row_vat=$stmt_vat->query()->fetchAll();
			$taxvat=0.00;
			if(count($row_vat)>0){
				$taxvat=($row_vat[0]['net_amt']*7)/100;
			}
			return $taxvat;
		}//func
		
		function paymentNew($user_id,
							$cashier_id,
							$saleman_id,
							
							$doc_tp,
							$status_no,
							$member_no='',
							
							$member_percent,
							$special_percent,
							$net_amount,
							
							$ex_vat_amt,
							$ex_vat_net,
							$vat,
							
							$paid,
							$pay_cash,
							$pay_credit,

							$redeem_point,
							$change,
							$coupon_code,

							$pay_cash_coupon,
							$credit_no,
							$credit_tp,

							$bank_tp,
							$name,
							$address1,

							$address2,
							$address3,
							$point_begin,

							$point_receive,
							$point_used,
							$point_net,

							$xpoint,
							$get_point,
							$refer_doc_no,

							$cn_amount,
							$remark1,
							$remark2,

							$xpoint_promo_code,
							$special_day='',
							$idcard='',
							$mobile_no=''){
			/**
			 * @desc
			 * @param
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//start type paid
			$pay_cash=floatVal($pay_cash);
			$pay_credit=floatVal($pay_credit);
			$pay_cash_coupon=floatVal($pay_cash_coupon);
			
			//*WR23062014
			if(!empty($pay_cash) && !empty($pay_credit)){
				//$paid="9990";
				$paid="0000";
				$credit_tp="";
				$bank_tp="";
			}else if(!empty($pay_cash)){
				$paid="0000";
				$credit_tp="";
				$bank_tp="";
			}else if(!empty($pay_credit)){
				$paid="0002";
			}else{
				if(trim($credit_no)!=''){
					$paid="0002";
				}else{
					$paid="0000";
					$credit_tp="";
					$bank_tp="";
				}
			}
			
			//*WR10072014 แก้ปัญหาเลือกประเภทการชำระเงินทั้งเงินสดและเครดิต
//			if($paid=="9990"){
//					$strResult= "0#ตรวจพบข้อมูลการชำระเงินทั้งเครดิตและเงินสด ไม่สามารถบันทึกได้#".$doc_tp."#Y";
//					return $strResult;
//					exit();
//			}

							//*WR22012015
			switch($special_day){
				case "TU1":
					$special_day='OPT1';
					break;
				case "TU2":
					$special_day='OPT2';
					break;
				case "TU3":
					$special_day='OPT3';
					break;	
				case "TU4":
					$special_day='OPT4';
					break;	
				case "TH1":
					$special_day='OPS1';
					break;
				case "TH2":
					$special_day='OPS2';
					break;
				case "TH3":
					$special_day='OPS3';
					break;	
				case "TH4":
					$special_day='OPS4';
					break;
				default:
					$special_day='';
					break;
			}
			
			//WR 18122013 taxid ลูกค้าสาขาที่_
			$remark2=trim($remark2);
			if($remark2!='' && $remark2!='-1'){
				$remark2=str_pad($remark2,5,"0",STR_PAD_LEFT);
			}
			
			//end type paid
			if(floatval($pay_cash)<1){
				$change=0.00;
			}			
			
			//get credit no wait
//			if($paid=='0002'){
//				$arr_credit=parent::getCreditCardInfo();
//				if(!empty($arr_credit)){
//					$credit_no=$arr_credit[0]['credit_no'];
//				}				
//			}			
			//check transaction data 
			$sql_chk_trns="SELECT COUNT(*) 
									FROM 
										trn_promotion_tmp1
									WHERE
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
 										`computer_ip`='$this->m_com_ip' AND 										
										`doc_date`='$this->doc_date'";
			$n_chk_trns=$this->db->fetchOne($sql_chk_trns);
			if($n_chk_trns>0){
				$tbl_tmp_detail="trn_promotion_tmp1";
			}else{
				$sql_chk_trns="SELECT COUNT(*) 
									FROM 
										trn_tdiary2_sl
									WHERE
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
 										`computer_ip`='$this->m_com_ip' AND 									
										`doc_date`='$this->doc_date'";
				$n_chk_trns=$this->db->fetchOne($sql_chk_trns);
				if($n_chk_trns>0){
					$tbl_tmp_detail="trn_tdiary2_sl";
				}else{
					$strResult= "0#ไม่พบรายการขาย ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
					return $strResult;
					exit();
				}
			}	
			
			//*WR23022014 Promotion On Web 
			$sql_web="SELECT promo_code,web_promo
								FROM `$tbl_tmp_detail` 
									WHERE 
							`corporation_id`='$this->m_company_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND 
							`web_promo`='Y' LIMIT 0,1";
			$row_web=$this->db->fetchAll($sql_web);
			if(count($row_web)>0){
				$co_promo_code=$row_web[0]['promo_code'];
				$birthday_card_st='Y';
			}else{
				$co_promo_code='';
				$birthday_card_st='N';
			}
			//*WR23022014 Promotion On Web
			
			//*WR21032014 for case bill 04 is member_discount1
			if($status_no=='04'){
				$sql_upd="UPDATE `$tbl_tmp_detail`
								SET
									member_discount1='',
									member_discount2=''
								WHERE
									 `corporation_id`='$this->m_company_id' AND
									 `company_id`='$this->m_company_id' AND
									 `branch_id`='$this->m_branch_id' AND
									 `computer_no`='$this->m_computer_no' AND
 									 `computer_ip`='$this->m_com_ip' AND 										
									 `doc_date`='$this->doc_date' AND
									 `status_no`='04'";
				$this->db->query($sql_upd);
			}
			
			//check status_no=02			
			$sql_l2h="SELECT 
							status_no,
							SUM(quantity) AS sum_quantity,	
						 	SUM(discount) AS sum_discount,
						 	SUM(member_discount1)  AS  sum_member_discount1,
						 	SUM(member_discount2)  AS sum_member_discount2,						 	
						 	SUM(co_promo_discount)  AS sum_co_promo_discount, 
						 	SUM(coupon_discount)  AS sum_coupon_discount,
						 	SUM(special_discount) AS sum_special_discount,
						 	SUM(other_discount) AS sum_other_discount,		
						 	SUM(special_discount) AS sum_special_discount,				 	 
							SUM(amount) AS sum_amount,
							SUM(net_amt) AS sum_net_amt							
						FROM 
							`$tbl_tmp_detail`
						WHERE
							`corporation_id`='$this->m_company_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date'";	
			$row_l2h=$this->db->fetchAll($sql_l2h);
			
			$chk_member_discount=$row_l2h[0]['sum_member_discount1'];
			$chk_member_discount=intval($chk_member_discount);
			if($chk_member_discount != 0 && $member_no == ''){
				$this->msg_error="ไม่พบรหัสบัตรสมาชิก กรุณายกเลิกการขายแล้วเปิดบิลใหม่อีกครั้ง";
				$this->doc_tp="";
				$this->net_amt="";
				$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt."#".$status_no."#".$this->m_branch_tp;
				return $strResult;
				exit();
			}
			
			//กำหนดให้เปิดบิลเล่นได้หลายโปรโมชั่น
			$this->status_no=$row_l2h[0]['status_no'];
			$this->quantity=$row_l2h[0]['sum_quantity'];	
			
			$this->discount=parent::getSatang($row_l2h[0]['sum_discount'],'up');
			$this->member_discount1= parent::getSatang($row_l2h[0]['sum_member_discount1'],'up');			
		 	$this->member_discount2= parent::getSatang($row_l2h[0]['sum_member_discount2'],'up');						 	
		 	$this->co_promo_discount= parent::getSatang($row_l2h[0]['sum_co_promo_discount'],'up'); 
		 	$this->coupon_discount= parent::getSatang($row_l2h[0]['sum_coupon_discount'],'up');
		 	$this->special_discount= parent::getSatang($row_l2h[0]['sum_special_discount'],'up');
		 	$this->other_discount= parent::getSatang($row_l2h[0]['sum_other_discount'],'up');
			$this->amount=parent::getSatang($row_l2h[0]['sum_amount'],'up');
		 	$this->amount=$row_l2h[0]['sum_amount'];		 	
		 	
		 	$this->net_amt=$this->amount-($this->discount+
		 												$this->member_discount1+
		 												$this->member_discount2+
		 												$this->co_promo_discount+
		 												$this->coupon_discount+
		 												$this->special_discount+
		 												$this->other_discount);
		 	$this->net_amt=parent::getSatang($this->net_amt,'normal');		 	
		 	//check doc type
		 	$this->doc_tp=$doc_tp;
		 	if(intval($this->amount)==0 && $this->status_no!='06'){
				$this->doc_tp="DN";				
		 	}
		 	
		 	if($this->doc_tp=="DN"){
		 		$paid="0000";
		 	}
		 	
		 	$sql_doc='SELECT 
							stock_st,doc_no 
						FROM 
							`com_doc_no` 
						WHERE 
							corporation_id="'.$this->m_corporation_id.'" AND 
							company_id="'.$this->m_company_id.'" AND
							branch_id="'.$this->m_branch_id.'" AND
							doc_tp="'.$this->doc_tp.'"';				
			$row_stockst=$this->db->fetchAll($sql_doc);				
			$this->stock_st=$row_stockst[0]['stock_st'];		
			$this->curr_doc_no=$row_stockst[0]['doc_no'];				
			$this->refer_doc_no=$refer_doc_no;	
			
			////*WR20120901 test for 50BTO1P
			$sql_pused="SELECT SUM(discount) AS promo_discount
									FROM `$tbl_tmp_detail`
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
	 								`computer_ip`='$this->m_com_ip' AND
	 								`promo_code`='50BTO1P' AND
									`doc_date`='$this->doc_date'";
			$mp_sum_discount=$this->db->fetchOne($sql_pused);//discount รวมของ 50BTO1P			
			if($mp_sum_discount>0){
					/////////////////////////////////////=WR05032014=///////////////////////////////////////////////
					$mp_sum_net_amt=$row_l2h[0]['sum_net_amt'];
					$re_mp_discount=($mp_sum_discount/5)*50;
					$net_balance=$mp_sum_net_amt-$re_mp_discount;
					$mp_point_net=parent::getPoint1($net_balance);		
					$mp_point_net2=parent::getPoint2($net_balance);
					$total_point=$mp_point_net+$mp_point_net2;
					$point1=parent::getPoint1($mp_sum_net_amt);
					$point2=parent::getPoint2($mp_sum_net_amt);	
					$mp_point_receive=$point1+$point2;
					$mp_point_used=$mp_point_receive-$total_point;
					$redeem_point=-1*$mp_point_used;
					/////////////////////////////////////=WR05032014=///////////////////////////////////////////////
			}else{
					/// case normal ///			
					$point_used=intval($point_used);
					if($point_used !== 0){
						//$redeem_point=$point_receive+(-1*$point_used);
						$redeem_point=-1*$point_used;//*WR20120901 bill 04,50 แลกคะแนน point1,point2 เท่ากับ 0 อยู่แล้ว
					}
					$point1=0;
					$point2=0;
					$total_point=0;
					if($member_no!='' && $get_point=='Y'){
						$point1=parent::getPoint1($this->net_amt);
						$point2=parent::getPoint2($this->net_amt);
					}			
					$xpoint=intval($xpoint);
					if($xpoint==0) $xpoint=1;
					
					//*WR07032014 for check last promotion xpoint x2
					if($xpoint>1){
						$sql_chkxpoint="SELECT COUNT(*) 
												FROM `$tbl_tmp_detail`
												WHERE 
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`computer_no`='$this->m_computer_no' AND
					 								`computer_ip`='$this->m_com_ip' AND
					 								`promo_code`='$xpoint_promo_code' AND
													`doc_date`='$this->doc_date'";
						$n_chkxpoint=$this->db->fetchOne($sql_chkxpoint);
						if($n_chkxpoint<1){
							$xpoint=1;
						}			
					}
					//*WR07032014 for check last promotion xpoint x2
					
					$point1*=$xpoint;
					$total_point=$point1+$point2+$redeem_point;//assume $redeem_point is nagative value					
					/// case normal ///
			}
			////*WR20120901 test for 50BTO1P	
			
			///*WR20120905 test for BURNPOINT2
			if($status_no=='04'){
					$sql_promocode="SELECT promo_code 
											FROM `trn_tdiary2_sl_val`
											WHERE
													`corporation_id`='$this->m_company_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`computer_no`='$this->m_computer_no' AND
						 							`computer_ip`='$this->m_com_ip' AND
													`doc_date`='$this->doc_date' AND 
													`status_no`='04'";
					$row_promo=$this->db->fetchAll($sql_promocode);
					if(count($row_promo)>0){
						//BURNPOINT2,STKER-2,1 คะแนน  ส่วนลด 5 บาท  รับสิทธิ์ส่วนลด 30%-50%
						$re_promo_code=$row_promo[0]['promo_code'];
						$sql_upd_pro="UPDATE 
												`$tbl_tmp_detail`
											 SET
											 	promo_code='$re_promo_code'
											 WHERE							 	
											 	`corporation_id`='$this->m_company_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`computer_no`='$this->m_computer_no' AND
	 											`computer_ip`='$this->m_com_ip' AND	
	 											`promo_code`='' AND										
												`doc_date`='$this->doc_date'";
						$this->db->query($sql_upd_pro);
					}else{
						//*WR07052013 แก้ปัญหาบิล 04 ไม่บันทึกคะแนนที่ใช้
						$sql_tsl2="SELECT promo_code FROM `$tbl_tmp_detail` 
										WHERE 
												`corporation_id`='$this->m_company_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`computer_no`='$this->m_computer_no' AND
					 							`computer_ip`='$this->m_com_ip' AND
												`doc_date`='$this->doc_date' AND 
												`status_no`='04' LIMIT 0,1";
						$row_tsl2=$this->db->fetchAll($sql_tsl2);
						if(!empty($row_tsl2)){
							$tsl2_promo_code=$row_tsl2[0]['promo_code'];
							//*WR22072014
							$sql_p="SELECT 
											a.promo_code AS h_promo_code, b.promo_id AS promo_code, b.point, a.get_point, a.promo_des
										FROM 
											`promo_point1_head` AS a LEFT JOIN `promo_point1_detail` AS b ON a.promo_code = b.promo_code
										WHERE b.`promo_id` = '$tsl2_promo_code' AND a.get_point <> 'Y'";
							//$sql_p="SELECT point FROM `promo_point1_detail` WHERE `promo_id` = '$tsl2_promo_code'";
							$row_p=$this->db->fetchAll($sql_p);
							if(!empty($row_p)){
								$point_used=$row_p[0]['point'];
								$point_used=intval($point_used);
								$redeem_point=-1*$point_used;//*WR20120901 bill 04,50 แลกคะแนน point1,point2 เท่ากับ 0 อยู่แล้ว
								$total_point=$redeem_point;									
							}//if
						}//if	
						
					}//if						
			}
			///*WR20120905 test for BURNPOINT2
			
			//*WR23062014 update coupon discount for coupon promotion
			//*WR14012015 change com_application_coupon to promo_other
			if($coupon_code!=''){
				$sql_ecoupon="SELECT* FROM promo_other WHERE promo_code='$coupon_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$rows_ecoupon=$this->db->fetchAll($sql_ecoupon);
				if(!empty($rows_ecoupon)){
					$discount=$rows_ecoupon[0]['discount'];//50
					$type_discount=$rows_ecoupon[0]['type_discount'];//BAHT
					$buy_type=$rows_ecoupon[0]['buy_type'];//N=net
					$buy_status=$rows_ecoupon[0]['buy_status'];//G=getter
					$start_baht=$rows_ecoupon[0]['start_baht'];//50
					$end_baht=$rows_ecoupon[0]['end_baht'];//50
					if($buy_type=='N'){
						if($buy_status=='G'){
							if($this->net_amt>=$start_baht){
								if($type_discount=='BAHT'){
									$this->net_amt=$this->net_amt-$discount;
									$this->coupon_discount=$discount;//add diary1 only
								}
							}
						}
					}
				}//end if not empty
			}//end if $coupon<>''
			
			//*WR12062014 Green Bag 2 Point
			$sql_gpoint="SELECT COUNT(*) 
								FROM $tbl_tmp_detail
								WHERE
											`corporation_id`='$this->m_company_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`computer_no`='$this->m_computer_no' AND
 											`computer_ip`='$this->m_com_ip' AND	
 											`promo_code`='OX17160514' AND										
											`doc_date` BETWEEN '2015-02-09' AND '2015-03-31'";
			$n_gpoint=$this->db->fetchOne($sql_gpoint);
			if($n_gpoint>0){
				$objCalPro=new Model_Calpromotion();
				$n_play=$objCalPro->chk_play_promotion($member_no); 
				if($n_play<5){
					//*WR30072014 add 2 point at special point
					$point2=$point2+4;//*WR18082014 revised point 16/08/2014-15/10/2014 to 15/11/2014
					//$point2=$point2+2;				
					$total_point=$point1+$point2+$redeem_point;
				}
			}
			
			//create doc_no for diary
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);			
			
			//change
			$change=floatval($change);
			//cal tax vat
			$this->vat=parent::calVat($this->net_amt);//*WR10102012
			//$this->vat=parent::getSatang($this->vat,'normal');
			$this->vat=round($this->vat,2);
						
			//*WR 09082012 web promotion ใช้สิทธิบนเวบ
			$sql_web="SELECT promo_code,web_promo
								FROM `$tbl_tmp_detail` 
									WHERE 
							`corporation_id`='$this->m_company_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND 
							`web_promo`='Y' LIMIT 0,1";
			$row_web=$this->db->fetchAll($sql_web);
			if(count($row_web)>0){
				$co_promo_code=$row_web[0]['promo_code'];
				$birthday_card_st='Y';
			}else{
				$co_promo_code='';
				$birthday_card_st='N';
			}
			if(!empty($xpoint_promo_code)){
				$co_promo_code=$xpoint_promo_code;
			}else{
				$co_promo_code='';
			}	

			//*WR 08052015 support pro member get member
			if($coupon_code!=''){
				$arr_coupon_s=explode('#',$coupon_code);
				if($arr_coupon_s[0]=='OX02230315'){
					$co_promo_code=$arr_coupon_s[0];
					$coupon_code=$arr_coupon_s[1]."#".$arr_coupon_s[2];
				}
			}
			
			if($this->status_no=='19'){
				$pay_cash=0;
				$change=0;
			}
			
			$this->db->beginTransaction();
			$this->chk_trans=TRUE;
			if(substr($co_promo_code,0,4)=='TOUR'){
				$tmpp = explode('||',$coupon_code);
				$coupon_code = $tmpp[0];
				$post_id = $tmpp[1];
			}else{
				$post_id = "";
			}
			try{
				$this->doc_time=date("H:i:s");
				//start query
				$sql_add_l2h="INSERT INTO trn_tdiary1_sl
								SET
									`corporation_id`='$this->m_company_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`doc_date`='$this->doc_date',
									`doc_time`='$this->doc_time',
									`doc_no`='$this->m_doc_no',
									`doc_tp`='$this->doc_tp',
									`status_no`='$status_no',
									`member_id`='$member_no',
									`member_percent`='$member_percent',
									`special_percent`='$special_percent',
									`refer_member_id`='',
									`refer_doc_no`='$this->refer_doc_no',
									`quantity`='$this->quantity',
									`amount`='$this->amount',
									`discount`='$this->discount',
									`member_discount1`='$this->member_discount1',
									`member_discount2`='$this->member_discount2',
									`co_promo_discount`='$this->co_promo_discount',
									`coupon_discount`='$this->coupon_discount',
									`special_discount`='$this->special_discount',
									`other_discount`='$this->other_discount',
									`net_amt`='$this->net_amt',
									`ex_vat_amt`='$ex_vat_amt',
									`ex_vat_net`='$ex_vat_net',
									`vat`='$this->vat',
									`paid`='$paid',
									`pay_cash`='$pay_cash',
									`pay_credit`='$pay_credit',
									`point_begin`='$point_begin',
									`redeem_point`='$redeem_point',
									`point1`='$point1',
									`point2`='$point2',
									`total_point`='$total_point',
									`change`='$change',
									`coupon_code`='$coupon_code',
									`pay_cash_coupon`='$pay_cash_coupon',
									`credit_no`='$credit_no',
									`credit_tp`='$credit_tp',
									`bank_tp`='$bank_tp',
									`computer_no`='$this->m_computer_no',
									`computer_ip`='$this->m_com_ip',
									`pos_id`='$this->m_pos_id',
									`user_id`='$user_id',	
									`cashier_id`='$cashier_id',
									`saleman_id`='$saleman_id',								
									`doc_remark`='',
									`name`='$name',
									`address1`='$address1',
									`address2`='$address2',
									`address3`='$address3',
									`refer_cn_net`='$cn_amount',
									`co_promo_code`='$co_promo_code',
									`birthday_card_st`='$birthday_card_st',									
									`remark1`='$remark1',
									`remark2`='$remark2',
									`special_day`='$special_day',
									`idcard`='$idcard',
									`mobile_no`='$mobile_no',									
									`reg_date`=CURDATE(),
								  	`reg_time`=CURTIME(),
									`post_id`='$post_id',
								    `reg_user`='$saleman_id'";			
				$this->db->query($sql_add_l2h);				
				
				//update doc_no in trn_tdiary2_sl
				$sql_upd_trn_tdiary2_sl="UPDATE 
											`$tbl_tmp_detail`
										 SET
										 	status_no='$this->status_no',
										 	stock_st='$this->stock_st',
										 	doc_tp='$this->doc_tp',
											doc_no='$this->m_doc_no'
										 WHERE							 	
										 	`corporation_id`='$this->m_company_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`computer_no`='$this->m_computer_no' AND
 											`computer_ip`='$this->m_com_ip' AND											
											`doc_date`='$this->doc_date'";
				$this->db->query($sql_upd_trn_tdiary2_sl);//return num row effect
				
				
				//load data from trn_promotion_tmp1 to tdiary2
				$sql_diary2="INSERT INTO
								`trn_diary2`
									(
									  `corporation_id`,
									  `company_id`,
									  `branch_id`,
									  `doc_date`,
									  `doc_time`,
									  `doc_no`,
									  `doc_tp`,
									  `status_no`,
									  `seq`,
									  `seq_set`,
									  `promo_code`,
									  `promo_seq`,
									  `promo_st`,
									  `promo_tp`,
									  `product_id`,								  
									  `stock_st`,								  
									  `price`,
									  `quantity`,
									  `amount`,
									  `discount`,
									  `member_discount1`,
									  `member_discount2`,
									  `co_promo_discount`,
									  `coupon_discount`,
									  `special_discount`,
									  `other_discount`,		  
									  `net_amt`,
									  `product_status`,
									  `get_point`,
									  `discount_member`,
									  `cal_amt`,
									  `tax_type`,
									  `discount_percent`,
									  `member_percent1`,
									  `member_percent2`,
									  `co_promo_percent`,
									  `coupon_percent`,
									   `lot_no`,
									  `reg_date`,
									  `reg_time`,
									  `reg_user`
									)
								SELECT
									  `corporation_id`,
									  `company_id`,
									  `branch_id`,
									  `doc_date`,
									  `doc_time`,
									  `doc_no`,
									  `doc_tp`,
									  `status_no`,
									  `seq`,
									  `seq_set`,
									  `promo_code`,
									  `promo_seq`,
									  `promo_st`,
									  `promo_tp`,
									  `product_id`,								  
									  `stock_st`,								  
									  `price`,
									  `quantity`,
									  `amount`,
								  	  `discount`,
									  `member_discount1`,
									  `member_discount2`,
									  `co_promo_discount`,
									  `coupon_discount`,
									  `special_discount`,
									  `other_discount`,	  
									  `net_amt`,
									  `product_status`,
									  `get_point`,
									  `discount_member`,
									  `cal_amt`,
									  `tax_type`,
									  `discount_percent`,
									  `member_percent1`,
									  `member_percent2`,
									  `co_promo_percent`,
									  `coupon_percent`,
									    `lot_no`,
									 CURDATE(),
									  CURTIME(),
									  `reg_user`
								FROM
									`$tbl_tmp_detail`
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND 
									doc_no='$this->m_doc_no'";							
				$this->db->query($sql_diary2);			
			   
				//load data from trn_tdiary1_sl to trn_diary1
				$sql_diary1="INSERT INTO trn_diary1(
												`corporation_id`,
												`company_id`,
												`branch_id`,
												`doc_date`,
												`doc_time`,
												`doc_no`,
												`doc_tp`,
												`status_no`,
												`member_id`,
												`member_percent`,
												`special_percent`,
												`refer_member_id`,
												`refer_doc_no`,
												`quantity`,
												`amount`,
												`discount`,
												`member_discount1`,
												`member_discount2`,
												`co_promo_discount`,
												`coupon_discount`,
												`special_discount`,
												`other_discount`,
												`net_amt`,
												`ex_vat_amt`,
												`ex_vat_net`,
												`vat`,												
												`paid`,
												`pay_cash`,
												`pay_credit`,
												`point_begin`,
												`redeem_point`,
												`point1`,
												`point2`,
												`total_point`,
												`change`,
												`coupon_code`,	
												`pay_cash_coupon`,
												`credit_no`,
												`credit_tp`,
												`bank_tp`,
												`computer_no`,
												`pos_id`,
												`user_id`,
												`cashier_id`,
												`saleman_id`,
												`name`,
												`address1`,
												`address2`,
												`address3`,
												`refer_cn_net`,
												`co_promo_code`,
												`birthday_card_st`,		
												`doc_remark`,												
												`remark1`,
												`remark2`,
												`special_day`,
												`idcard`,
												`mobile_no`,												
												`reg_date`,
												`reg_time`,
												`post_id`,
												`reg_user`
											)
										SELECT
											`corporation_id`,
												`company_id`,
												`branch_id`,
												`doc_date`,
												`doc_time`,
												`doc_no`,
												`doc_tp`,
												`status_no`,
												`member_id`,
												`member_percent`,
												`special_percent`,
												`refer_member_id`,
												`refer_doc_no`,
												`quantity`,
												`amount`,
												`discount`,
												`member_discount1`,
												`member_discount2`,
												`co_promo_discount`,
												`coupon_discount`,
												`special_discount`,
												`other_discount`,
												`net_amt`,
												`ex_vat_amt`,
												`ex_vat_net`,
												`vat`,
												`paid`,
												`pay_cash`,
												`pay_credit`,
												`point_begin`,
												`redeem_point`,
												`point1`,
												`point2`,
												`total_point`,
												`change`,
												`coupon_code`,
												`pay_cash_coupon`,
												`credit_no`,
												`credit_tp`,
												`bank_tp`,
												`computer_no`,
												`pos_id`,
												`user_id`,
												`cashier_id`,
												`saleman_id`,
												`name`,
												`address1`,
												`address2`,
												`address3`,
												`refer_cn_net`,												
												`co_promo_code`,
												`birthday_card_st`,												
												`doc_remark`,
												`remark1`,
												`remark2`,
												`special_day`,
												`idcard`,
												`mobile_no`,
												`reg_date`,
												`reg_time`,
												`post_id`,
												`reg_user`
										FROM 
											`trn_tdiary1_sl`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND 
											doc_no='$this->m_doc_no'";

				$this->db->query($sql_diary1);
				
				
				//Next update table doc_no
				//$this->curr_doc_no+=1;
				$sql_inc_docno="UPDATE 
											com_doc_no 
										SET 
											doc_no=doc_no+1,
											upd_date='$this->doc_date',
					   						upd_time='$this->doc_time',
					   						upd_user='$cashier_id' 
										WHERE 
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND 	
											branch_id='$this->m_branch_id' AND 
											branch_no='$this->m_branch_no' AND
											doc_tp='$this->doc_tp'";
				$this->db->query($sql_inc_docno);
				//remove insert com_mem_pt 30072014
				//commit trans
				$this->db->commit();
			}catch(Zend_Db_Exception $e){
				//rollback trans
				$this->db->rollBack();				
				//$this->msg_error=$e->getMessage();				
				if($e->getCode()==23000){
					$this->msg_error=" { <u>ไม่สามารถบันทึกเลขที่บิลซ้ำได้</u> } ";
				}else{
					$this->msg_error=$e->getMessage();
				}
				$this->chk_trans=FALSE;
			}//end try
			
			if($this->chk_trans){
				//////// COMPLETE TRANSACTION ///////////
				
//			   $sql_clslog_credit="DELETE FROM `trn_credit_card` 
//											WHERE `computer_no`='$this->m_computer_no' AND	`computer_ip`='$this->m_com_ip'";
//			   $this->db->query($sql_clslog_credit);
				
//				if($this->m_cashdrawer=='Y' && ($this->doc_tp=='SL' || $this->doc_tp=='VT')){
//					parent::openCashDrawer();
//				}
				
				//append point to com_mem_pt
				if($this->status_no=='06'){
					//update cn_status=Y and paid cn reference by paid of bill 06
					$sql_upd="UPDATE 	trn_diary1 
										SET 
											cn_status='Y',
											paid='$paid',
											upd_date='$this->doc_date',
					   						upd_time='$this->doc_time',
					   						upd_user='$cashier_id' 
										WHERE doc_no='$this->refer_doc_no'";
					$this->db->query($sql_upd);
				}
				
				parent::TrancateTable("trn_tdiary2_sl_val");
				parent::TrancateTable("trn_promotion_tmp1");
				parent::TrancateTable("trn_tdiary2_sl");
				parent::TrancateTable("trn_tdiary1_sl");
				$strResult= "1#".$this->m_doc_no."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt."#".$status_no."#".$this->m_branch_tp."#".$this->m_cashdrawer;	
				
				//////////////////////////////////// IF SHOP IS CORNER SHOP ////////////////////////////////
				if($this->m_branch_tp=='C'){
					$sql_d2="SELECT promo_code,product_id,price FROM trn_diary2 WHERE doc_no='$this->m_doc_no'";
					$row_d2=$this->db->fetchAll($sql_d2);
					foreach($row_d2 as $data){						
						if(intval($data['price'])!=0){
								$gp_promo_code=$data['promo_code'];
								$gp_product_id=$data['product_id'];								
								$gp='';
								$lot_no='';
								if($this->member_no!=''){
									$sql_gp1="SELECT gp,barcode FROM com_gp_corner WHERE gp='11' AND '$this->doc_date' BETWEEN start_date AND end_date";
									$row_gp1=$this->db->fetchAll($sql_gp1);
									if(count($row_gp1)>0){
										$gp='11';
										$lot_no=$row_gp1[0]['barcode'];
									}
								}else if($this->member_no=='' && $gp_promo_code==''){
									$sql_gp2="SELECT gp,barcode FROM com_gp_corner WHERE gp='30' AND '$this->doc_date' BETWEEN start_date AND end_date";
									$row_gp2=$this->db->fetchAll($sql_gp2);
									if(count($row_gp2)>0){
										$gp='30';
										$lot_no=$row_gp2[0]['barcode'];
									}
								}else if($this->member_no=='' && $gp_promo_code!=''){
									$sql_gp3="SELECT gp,barcode FROM com_gp_corner WHERE gp='11' AND '$this->doc_date' BETWEEN start_date AND end_date";									
									$row_gp3=$this->db->fetchAll($sql_gp3);
									if(count($row_gp3)>0){
										$gp='11';
										$lot_no=$row_gp3[0]['barcode'];
									}
								}
								$sql_upd_diary2="UPDATE trn_diary2 SET gp='$gp',lot_no='$lot_no' WHERE doc_no='$this->m_doc_no' AND product_id='$gp_product_id'";
								$this->db->query($sql_upd_diary2);
						}						
					}//foreach		
				}			
				//////////////////////////////////// IF SHOP IS CORNER SHOP ////////////////////////////////
				
				//TRUNCATE com_web_pt for ecoupon employee
				if($this->status_no=='03'){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_web_pt");
					$sql_ecoupon="SELECT member FROM com_web_pt ORDER BY id DESC LIMIT 0,1";
					$e_coupon_member_no=$this->db->fetchOne($sql_ecoupon);
					$sql_ecoupon_profile=" INSERT INTO `trn_ecoupon_profile` 
							SET
									`corporation_id`='$this->m_corporation_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`doc_date`='$this->doc_date',
									`doc_time`=CURTIME(),
									`doc_no`='$this->m_doc_no',
									`member_no`='$e_coupon_member_no',
									`reg_date`=CURDATE(),
									`reg_time`=CURTIME(),
									`reg_user`='$cashier_id'";
					$this->db->query($sql_ecoupon_profile);					
					$sql_clear="TRUNCATE TABLE `com_web_pt` ";
					$this->db->query($sql_clear);
				}
				////////COMPLETE TRANSACTION///////////
			}else{
				$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt."#".$status_no."#".$this->m_branch_tp;
			}//if	
			return $strResult;
		}//func
		
		function up2jinet($doc_no){
			/**
			 * @desc up bill to 10.100.53.2 service_pos_op
			 * @return
			 */
			//up to service_pos_op			
			$objCal=new Model_Calpromotion();
			$objCal->up_point($doc_no);
			//return free birthday
			unset($this->db);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_freeb="SELECT a.member_id,a.doc_date as doc_date,b.promo_code as promo_code,b.product_id as product_id,a.branch_id as branch_id
							FROM `trn_diary1` as a inner join trn_diary2 as b on(a.doc_no=b.doc_no)
							WHERE a.doc_no='$doc_no' AND b.promo_code = 'FREEBIRTH'";
			$row_freeb=$this->db->fetchAll($sql_freeb);
			if(!empty($row_freeb)){
				$member_no=$row_freeb[0]['member_id'];
				$doc_date=$row_freeb[0]['doc_date'];
				$promo_code=$row_freeb[0]['promo_code'];
				$product_id=$row_freeb[0]['product_id'];
				$shop=$row_freeb[0]['branch_id'];
				$objCal->promo_wait_finish($member_no,$doc_date,$promo_code,$product_id,$shop,$doc_no);
			}			
		}//func
		
		//------------------ check user finger
		function check_user_finger($user_id){
			$sql="
			SELECT * FROM conf_employee
			WHERE
			user_id='$user_id'";
			$this->db=Zend_Registry::get('dbOfline');
			$res=$this->db->fetchRow($sql);
			if(count($res) > 0){
				$password_id 	= $res['password_id'];
				$user_id 		= $res['user_id'];
			}else{
				$password_id 		="";
				$user_id			="";
			}
			return array($user_id,$password_id);
		}//func
				function getSumDiscountLast($xpoint,$flg_chk_point,$flg_tbl,$cn_amount,$get_discount_member="",$promo_code="",$do_pay="",$cut_point_last="",$discount_baht="",$member_no=""){
			/**
			 * @name getSumInv 19072013
			 * @desc get summary info data of sale transaction			
			 * @param Integer $xpoint :ตัวคูณคะแนนปกติ บิลแรกหลังสมัครสมาชิก
			 * @param Char flg_chk_point: 'Y' สมาชิกซื้อแต่ไม่ใช่บิล 01(สมัครสมาชิกใหม่) 
			 * @return array of row member
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($flg_tbl=='cashier'){
				$tbl_temp="trn_tdiary2_sl";
			}else if($flg_tbl=='promotion'){
				$tbl_temp="trn_promotion_tmp1";
			}
			
			$sql="
			select 
				SUM(net_amt) AS sum_net_amt
			from
				`$tbl_temp`
			where
				`corporation_id`='$this->m_corporation_id'
			and
				`company_id`='$this->m_company_id'
			and
				`branch_id`='$this->m_branch_id'
			and
				`computer_no`='$this->m_computer_no'
			and
 				`computer_ip`='$this->m_com_ip'
 			and
				`doc_date`='$this->doc_date'				
			";
			$sum_net_amt_all = $this->db->fetchOne($sql);
			
			//echo "sum_net_amt_all = $sum_net_amt_all\r\n";
			
			$sql1="
			select 
				SUM(net_amt) AS sum_net_amt
			from
				`$tbl_temp`
			where
				`corporation_id`='$this->m_corporation_id'
			and
				`company_id`='$this->m_company_id'
			and
				`branch_id`='$this->m_branch_id'
			and
				`computer_no`='$this->m_computer_no'
			and
 				`computer_ip`='$this->m_com_ip'
 			and
				`doc_date`='$this->doc_date'
			and
				discount_member != 'N'				
			";
			//echo $sql1;
			$sum_net_amt_for_discount = $this->db->fetchOne($sql1);
			
			//echo "sum_net_amt_for_discount = $sum_net_amt_for_discount\r\n";
			
			if($member_no!=""){
				$sql_disc="
				select 
					normal_percent
				from
					com_percent_discount
				where
					'$sum_net_amt_all' between `start_baht` and `end_baht`
				";
				$percent_discount = $this->db->fetchOne($sql_disc);
				
				$mem_discount = $sum_net_amt_for_discount*($percent_discount/100);
			}else{
				$mem_discount = 0;
				$percent_discount=0;
			}
			
			//echo "percent_discount = $percent_discount\r\n";
			
			
			//echo "mem_discount = $mem_discount\r\n";
			$total_discount = $sum_net_amt_all-$mem_discount;
			
			//echo "total_discount = $total_discount\r\n";
			/*
			$sql_disc="
			select 
				normal_percent
			from
				com_percent_discount
			where
				'$sum_net_amt_all' between `start_baht` and `end_baht`
			";
			$percent_discount = $this->db->fetchOne($sql_disc);
			//echo "percent_discount = $percent_discount\r\n";
			if($get_discount_member=="N" or empty($get_discount_member)){
				$mem_discount=0;
				$total_discount=0;
			}else{
				$mem_discount = $sum_net_amt_for_discount*($percent_discount/100);
				$total_discount = $sum_net_amt_all-$discount;
			}
			*/

			/*	
			$sql_suminv="
			SELECT 
				SUM(quantity) AS sum_quantity,	
			 	SUM(discount) AS sum_discount,
			 	SUM(member_discount1)  AS  sum_member_discount1,
			 	SUM(member_discount2)  AS sum_member_discount2,						 	
			 	SUM(co_promo_discount)  AS sum_co_promo_discount, 
			 	SUM(coupon_discount)  AS sum_coupon_discount,
			 	SUM(special_discount) AS sum_special_discount,
			 	SUM(other_discount) AS sum_other_discount,		
			 	SUM(special_discount) AS sum_special_discount,				 	 
				SUM(amount) AS sum_amount,
				SUM(net_amt) AS sum_net_amt,
				SUM(point1) AS used_point
			FROM 
				`$tbl_temp`
			WHERE 
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id' AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no`='$this->m_computer_no' AND
 				`computer_ip`='$this->m_com_ip' AND
				`doc_date`='$this->doc_date'
			";
			*/
			
			$sql_suminv="
			SELECT 
				SUM(quantity) AS sum_quantity,	
			 	SUM(discount) AS sum_discount,
				SUM(amount) AS sum_amount,
				SUM(net_amt) AS sum_net_amt,
				SUM(point1) AS used_point
			FROM 
				`$tbl_temp`
			WHERE 
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id' AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no`='$this->m_computer_no' AND
 				`computer_ip`='$this->m_com_ip' AND
				`doc_date`='$this->doc_date'
				
			";
			$row_l2h=$this->db->fetchAll($sql_suminv);
			//print_r($row_l2h);
			if(count($row_l2h)>0){
				$discount=parent::getSatang($row_l2h[0]['sum_discount'],'up');
				$member_discount1= parent::getSatang($row_l2h[0]['sum_member_discount1'],'up');			
			 	$member_discount2= parent::getSatang($row_l2h[0]['sum_member_discount2'],'up');						 	
			 	$co_promo_discount= parent::getSatang($row_l2h[0]['sum_co_promo_discount'],'up'); 
			 	$coupon_discount= parent::getSatang($row_l2h[0]['sum_coupon_discount'],'up');
			 	$special_discount= parent::getSatang($row_l2h[0]['sum_special_discount'],'up');
			 	$other_discount= parent::getSatang($row_l2h[0]['sum_other_discount'],'up');
			 	$amount=$row_l2h[0]['sum_amount'];
			 	
			 	/*
			 	$sum_discount=$discount+$member_discount1+$member_discount2+$co_promo_discount+$coupon_discount+$special_discount+$other_discount;
			 	//ws new
			 	$sum_discount=$sum_discount+$mem_discount;
			 	*/
			 	
				if($member_no==""){
			 		$net_amt=$total_discount;
			 	}else{
			 		//$net_amt=$total_discount - ($member_discount1+$member_discount2+$co_promo_discount+$coupon_discount+$special_discount+$other_discount);
			 		$net_amt=$total_discount;
			 	}
			 	
			 	//echo "$net_amt $cn_amount";
			 	//$net_amt=$amount - $sum_discount;
			 	$net_chk_point=parent::getSatang($net_amt,'normal');
			 	$row_l2h[0]['sum_discount'] = $mem_discount;
			 	//$row_l2h[0]['sum_discount'] = $sum_discount;
			 	
			 	
	            
	            /*
			 	if($cn_amount!=''){
			 		
			 		//echo "$net_chk_point=($net_chk_point-$cn_amount)";
					$cn_amount=(float)$cn_amount;
					$net_chk_point=(float)$net_chk_point;
					
					//$net_chk_point=($net_chk_point-$cn_amount);
					
					//if($net_chk_point < 0){
						//$net_chk_point=0;
					//}
					
					
			 		if($cn_amount > $net_chk_point){
						$net_chk_point=0;
					}else{
						$net_chk_point=($net_chk_point-$cn_amount);
					}
				}
				*/
				
				$row_l2h[0]['sum_member_discount1'] = $member_discount1;
	            $row_l2h[0]['sum_member_discount2'] =$member_discount2;
	            $row_l2h[0]['sum_co_promo_discount'] = $co_promo_discount;
	            $row_l2h[0]['sum_coupon_discount'] =$coupon_discount;
	            $row_l2h[0]['sum_special_discount'] =$special_discount;
	            $row_l2h[0]['sum_other_discount'] =$other_discount;
	            $row_l2h[0]['sum_amount'] = $amount;
	            $row_l2h[0]['sum_net_amt'] = $net_chk_point;
				
				$xpoint=intval($xpoint);
				if($xpoint==0 || $xpoint=='') $xpoint=1;
				$point=0;
				if($flg_chk_point=='Y'){
					$point1=parent::getPoint1($net_chk_point);
					
					//cps not use point2 $point2=parent::getPoint2($net_chk_point);
					$point2='0';
					$point1*=$xpoint;
					$point=$point1+$point2;
					$row_l2h[0]['point_receive']=$point;			
				}else{
					$row_l2h[0]['point_receive']="0";			
				}
				if($get_discount_member=="N"){
					$row_l2h[0]['percent_discount']="0";
				}else{
					$row_l2h[0]['percent_discount']=$percent_discount;
				}
				
				if($do_pay=="Y"){
					if(substr($promo_code,0,6)=="BPOINT"){
						$row_l2h[0]['used_point']=$cut_point_last;
						$row_l2h[0]['sum_net_amt'] = $net_chk_point-$discount_baht;
						$row_l2h[0]['sum_discount'] = $sum_discount+$discount_baht;
					}
				}
				
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcsh',$row_l2h[0],'yes');	
				return $json;				
			}
		}//func
	}//class
?>