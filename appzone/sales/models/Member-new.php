<?php 
	class Model_Member extends Model_PosGlobal{
		private $product_id;
		private $product_name;
		private $unit;
		private $promo_code;
		private $promo_id;
		private $promo_seq;
		private $promo_st;/*สถานะตัวสินค้าที่เล่น promotion เช่น S ตัวซื้อ P ตัวแถมหรือ*/
		private $promo_tp;
		private $seq_pro;
		private $application_id;
		private $product_no;
		private $product_seq;
		private $product_sub_seq;
		private $doc_no_update;
		private $expire_date;
		private $gift_set;//if gift_set==N,key product append by amount and check price with register_free if Y then clear price
		private $net_amt;
		private $amount;
		private $register_free; 
		
		function __construct(){
			parent::__construct();	
			$this->doc_tp="SL";
		}//func
		
		function chkFirstLimited($first_limited,$first_percent,$add_first_percent,$product_id,$quantity){
			/**
			 * @desc
			 * @return
			 */
			$s_ck="Y";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");						
			$price=1;
			//assume product_id is barcode
			if(substr($product_id,0,3)=='885' && strlen($product_id)==13){
					$sql_nb="SELECT product_id,price 
									FROM com_product_master 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											barcode='$product_id'";	
					$row_nb=$this->db->fetchAll($sql_nb);
					if(!empty($row_nb)){				
						$price=$row_nb[0]['price'];	
					}else{					
						$sql_np="SELECT product_id,price 
										FROM `com_product_master` 
											WHERE 
												corporation_id='$this->m_corporation_id' AND 
												company_id='$this->m_company_id' AND
												product_id='$product_id'";
						$row_nb=$this->db->fetchAll($sql_np);
						if(!empty($row_nb)){					
							$price=$row_nb[0]['price'];	
						}
					}
			}else{
						//out of case check barcode then check product_id
						$sql_np="SELECT product_id,price 
										FROM `com_product_master` 
											WHERE 
												corporation_id='$this->m_corporation_id' AND 
												company_id='$this->m_company_id' AND
												product_id='$product_id'";
						$row_nb=$this->db->fetchAll($sql_np);
						if(!empty($row_nb)){					
							$price=$row_nb[0]['price'];	
						}
			}
			$pdt_amt=$quantity*$price;			
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk="SELECT SUM(amount) AS curr_amt 
							FROM `trn_promotion_tmp1`
							WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";
			$row_chk=$this->db->fetchAll($sql_chk);
			$curr_amt=$row_chk[0]['curr_amt'];
			
			$curr_amt=$curr_amt+$pdt_amt;
			//echo $curr_amt.">".$first_limited;
			if($curr_amt>$first_limited){
				$s_ck="N";
			}
			echo $s_ck;			
		}//func
		
		function chkBalVipToday($member_no,$limited_type){
			/**
			 * @desc
			 * @return
			 */
			
			if($limited_type=='N'){
				$filed_check='net_amt';
			}else{
				$filed_check='amount';
			}
			$sql_today="SELECT SUM($filed_check) AS diary1_amount 
							FROM trn_diary1 
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								member_id='$member_no' AND 
								doc_date='$this->doc_date' AND
								flag<>'C'";
			$row_amt=$this->db->fetchAll($sql_today);
			$diary1_amount='0.00';
			if(count($row_amt)>0){
				$diary1_amount=$row_amt[0]['diary1_amount'];
			}
			return $diary1_amount;
		}//func
		
		function add2Crm($str_json){
			/**
			 * @desc
			 * *ไม่รวมถึงการสมัครสมาชิกใหม่ที่มีการ gen customer_id format ใหม่
			 * @return
			 */
			$obj_json=json_decode($str_json);
			$member_no=$obj_json->{'member_no'};
			$arr_spday=$this->getComSpecialDay($member_no);	
			$special_day=$arr_spday[0]['special_day'];				
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			$sql_crm_card="SELECT 
											customer_id
									FROM
										crm_card
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										member_no='$member_no'	";
				$row_crm_card=$this->db->fetchAll($sql_crm_card);
				if(count($row_crm_card)>0){
					//update local
					$sql_upd_crm_card="UPDATE crm_card
													SET
														expire_date='".$obj_json->{'expire_date'}."',
														upd_date=CURDATE(),
														upd_time=CURTIME(),
														upd_user='$this->employee_id'
													WHERE
														corporation_id='$this->m_corporation_id' AND
														company_id='$this->m_company_id' AND
														member_no='$member_no'	";
					$this->db->query($sql_upd_crm_card);
					
					$sql_upd_crm_profile="UPDATE crm_profile
													SET															
														title='".$obj_json->{'title'}."',
														name='".$obj_json->{'name'}."',
														surname='".$obj_json->{'surname'}."',
														birthday='".$obj_json->{'birthday'}."',
														mobile_no='".$obj_json->{'mobile_no'}."',
														upd_date=CURDATE(),
														upd_time=CURTIME(),
														upd_user='$this->employee_id'
													WHERE
														corporation_id='$this->m_corporation_id' AND
														company_id='$this->m_company_id' AND
														customer_id='".$row_crm_card[0]['customer_id']."'";
					$this->db->query($sql_upd_crm_profile);						
				}else{
					//insert local
					/*
					$customer_id=$this->getCstNumber();
					$crm_customer_id=$this->getCustomerIdFormat($customer_id);			
					*/			
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
					$sql_crm_card="INSERT INTO crm_card 
											SET
												corporation_id='$this->m_corporation_id',
												company_id='$this->m_company_id',
												brand_id='".$obj_json->{'brand_id'}."',
												cardtype_id='".$obj_json->{'cardtype_id'}."',
												customer_id='".$obj_json->{'customer_id'}."',
												member_no='$member_no',
												apply_date='".$obj_json->{'apply_date'}."',
												expire_date='".$obj_json->{'expire_date'}."',
												member_ref='',
												apply_shop='$this->m_branch_id',
												apply_promo='".$obj_json->{'appli_code'}."',
												special_day='$special_day',
												reg_date=CURDATE(),
												reg_time=CURTIME(),
												reg_user='$this->employee_id'";
					$res_qry_card=$this->db->query($sql_crm_card);
					$sql_crm_profile="INSERT INTO crm_profile
											SET
												title='".$obj_json->{'title'}."',
												name='".$obj_json->{'name'}."',
												surname='".$obj_json->{'surname'}."',
												birthday='".$obj_json->{'birthday'}."',
												mobile_no='".$obj_json->{'mobile_no'}."',
												corporation_id='$this->m_corporation_id',
												company_id='$this->m_company_id',
												customer_id='".$obj_json->{'customer_id'}."'";
					$res_qry_profile=$this->db->query($sql_crm_profile);						
				}//if count
		}//func
		
		function chkEdate(){
			/**
			 * 
			 * ตรวจสอบเดือนนั้นใช้สิทธิได้หรือไม่ ใช้ได้ระหว่างวันเวลาอะไร
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_edate");
			$arr_docdate=explode('-',$this->doc_date);
			$f_month="month_".$arr_docdate[1];
			$d=intval($arr_docdate[2]);
			$status_ck='N';
			$arr_json=array();
			//check eholiday
			$sql_eholiday="SELECT 
									`holiday`, `special_open_time`, `special_close_time`
								FROM 
									`com_eholiday`
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`holiday`='$this->doc_date'";
			$row_eholiday=$this->db->fetchAll($sql_eholiday);
			if(count($row_eholiday)>0){
				$curr_time=time();
				$start_time=strtotime($row_eholiday[0]['special_open_time']);
				$end_time=strtotime($row_eholiday[0]['special_close_time']);
				if($curr_time>=$start_time && $curr_time<=$end_time){
					//ok
					$arr_json[0]['status_ck']='Y';
					$arr_json[0]['month_enable']='Y';
					$arr_json[0]['outoftime']='N';
					$status_ck='Y';
				}else{
					//no
					$arr_json[0]['status_ck']='N';
					$arr_json[0]['month_enable']='Y';
					$arr_json[0]['outoftime']='Y';
					$arr_json[0]['start_day']=$row_eholiday[0]['holiday'];
					$arr_json[0]['end_day']=$row_eholiday[0]['holiday'];
					$arr_json[0]['start_time']=$row_eholiday[0]['special_open_time'];
					$arr_json[0]['end_time']=$row_eholiday[0]['special_close_time'];
				}
			}else{
				$sql_getEdate="SELECT 
								`start_day`, `end_day`, `normal_open_time`, `normal_close_time`, `special_open_time`, `special_close_time` ,$f_month
							FROM
								com_edate
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND 
								$f_month='Y'";
				$row_edate=$this->db->fetchAll($sql_getEdate);
				if(count($row_edate)>0){					
					if($d>=$row_edate[0]['start_day'] && $d<=$row_edate[0]['end_day']){
						//in range
							$arr_day = getdate();
							if($arr_day['weekday']=='Saturday' || $arr_day['weekday']=='Sunday'){
								$time_begin=$row_edate[0]['special_open_time'];
								$time_end=$row_edate[0]['special_close_time'];
							}else{
								$time_begin=$row_edate[0]['normal_open_time'];
								$time_end=$row_edate[0]['normal_close_time'];
							}
							$now=time();
							if($now>=strtotime($time_begin) && $now<=strtotime($time_end)){
								//ok
								$arr_json[0]['status_ck']='Y';
								$arr_json[0]['month_enable']='Y';
								$arr_json[0]['outofday']='N';
								$arr_json[0]['outoftime']='N';
								$status_ck='Y';
							}else{
								//no
								$arr_json[0]['status_ck']='N';
								$arr_json[0]['month_enable']='Y';
								$arr_json[0]['outofday']='N';
								$arr_json[0]['outoftime']='Y';
								$arr_json[0]['start_day']=$row_edate[0]['start_day'];
								$arr_json[0]['end_day']=$row_edate[0]['end_day'];
								$arr_json[0]['start_time']=$time_begin;
								$arr_json[0]['end_time']=$time_end;
							}
					}else{
						//out of range
						$arr_json[0]['status_ck']='N';
						$arr_json[0]['month_enable']='Y';
						$arr_json[0]['outofday']='Y';
						$arr_json[0]['outoftime']='N';
						$arr_json[0]['start_day']=$row_edate[0]['start_day'];
						$arr_json[0]['end_day']=$row_edate[0]['end_day'];						
					}
				}else{
					//out of month
					$arr_json[0]['status_ck']='N';
					$arr_json[0]['month_enable']='N';
					$arr_json[0]['outofday']='N';
					$arr_json[0]['outoftime']='N';
				}
			}//if
			return $arr_json;				
		}//func	
		
		function chkEmpPrivilage($employee_id){
			/**
			 * @desc
			 * @etc 25062012 not check corporation_id and company_id
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_ecoupon");
			$rows_e=array();
			//check webcam
			$sql_cam="SELECT COUNT(*) FROM com_web_pt WHERE member='$employee_id'";			
			$n_cam=$this->db->fetchOne($sql_cam);
			if($n_cam<1){
				$rows_e[0]['error_cam']="Y";
			}else{
				#################################### WR next start get sum amount form jinet
//				$f_brand=$this->m_corporation_id."".$this->m_branch_id;
//				$sql_e="SELECT * 
//								FROM `com_ecoupon`
//							WHERE
//								op='$f_brand' AND
//								'$this->doc_date' BETWEEN start_date AND end_date AND
//								employee_id ='$employee_id'";
//				$rows_e=$this->db->fetchAll($sql_e);
//				if(count($rows_e)>0){
//					//ยอดใช้ไป
//					$arr_docdate=explode('-',$this->doc_date);
//					$yyyy=$arr_docdate[0];
//					$mm=$arr_docdate[1];
//					$sql_amt_used="SELECT sum(coupon_discount*2) AS amt_used
//											FROM `trn_diary1` 
//											WHERE 
//												status_no='03' AND 
//												flag<>'C' AND
//												member_id='$employee_id' and YEAR(doc_date)='$yyyy' and MONTH(doc_date)='$mm'";				
//					$row_amt_used=$this->db->fetchAll($sql_amt_used);			
//					if(count($row_amt_used)>0){	
//						$rows_e[0]['amount_used']=floatval($row_amt_used[0]['amt_used']);
//						$rows_e[0]['amount_balance']=floatval($rows_e[0]['amount_op'])-floatval($row_amt_used[0]['amt_used']);	//ยอดรวมที่ใช้ได้ของพนักงาน - ยอดรวมที่ใช้ไปแล้วของเดือนนี้ปีนี้
//					}else{
//						$rows_e[0]['amount_used']=0.00;
//					}
//				}

			//New solution 18032013
			$objCal=new Model_Calpromotion();
			$arr_coupon=$objCal->read_ecoupon($employee_id); 
			
//			echo "<pre>";
//			print_r($arr_coupon);
//			echo "</pre>";
			
			
			$rows_e[0]['name']=$arr_coupon['name'];
			$rows_e[0]['surname']=$arr_coupon['surname'];
			
			$rows_e[0]['amount_gnc']=$arr_coupon['amount_gnc'];
			$rows_e[0]['amount_cps']=$arr_coupon['amount_cps'];
			$rows_e[0]['amount_op']=$arr_coupon['amount_op'];
			$rows_e[0]['percent_discount']=$arr_coupon['percent_discount'];
			$rows_e[0]['amount_used']=$arr_coupon['amount'];
			$rows_e[0]['amount_balance']=$arr_coupon['credit'];
			
//			echo "<pre>";
//			print_r($arr_coupon);
//			echo "<pre>";
			
			/*
			 [amount_op] => 2500.00
			 [amount_cps] => 0.00
			 [amount_gnc] => 0.00   
			 [percent_discount] => 50    
			 [amount] => 0
			 [credit] => 2500.0000
			 */
				#################################### WR next start get sum amount form jinet
			}
			return $rows_e;
		}//func
		
		function calEcouponDiscount($ecoupon_percent_discount,$ecp_amount_bal){
			/**
			 * @desc
			 * @param Int $ecoupon_percent_discount
			 * @param Float $ecp_amount_bal
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_data="SELECT 
								seq,product_id,amount,net_amt
							FROM 
								trn_tdiary2_sl
							WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";
			$rows_data=$this->db->fetchAll($sql_data);
			$next_discount=$ecp_amount_bal;
			foreach($rows_data as $data){
				if($ecp_amount_bal<1)
					break;
				if($ecp_amount_bal<=$data['amount']){
					$next_discount=$ecp_amount_bal;
				}else{
					$next_discount=$data['amount'];
				}
				
				$seq=$data['seq'];
				$special_discount=$next_discount*$ecoupon_percent_discount;//100*0.5
				$net_amt=$data['amount']-$special_discount;//225-50=175			
				$sql_upd="UPDATE trn_tdiary2_sl
									SET										
										coupon_discount='$special_discount',
										net_amt='$net_amt'
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										`computer_no` ='$this->m_computer_no' AND
										`computer_ip` ='$this->m_com_ip' AND
										doc_date='$this->doc_date' AND
										seq='$seq'";
				$this->db->query($sql_upd);				
				$ecp_amount_bal=$ecp_amount_bal-$next_discount;
			}
		}//func
		
		function reCalEcouponDiscount(){
			/**
			 * @desc
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");			
			$sql_redisc="UPDATE trn_tdiary2_sl 
								SET 
									coupon_discount='',
									net_amt=amount
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no` ='$this->m_computer_no' AND
									`computer_ip` ='$this->m_com_ip' AND
									`doc_date`='$this->doc_date'	";
			$this->db->query($sql_redisc);
		}//func
		
		function getMemberBuyProfile($member_no){		
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("memberop","diary1");
			$sql_p="SELECT shop,doc_no,doc_dt,member,amount,net_amt							
						FROM `diary1` 						
						WHERE member='$member_no' and doc_tp in('SL','VT') and flag<>'C'
							order by doc_dt desc";
			$row_p=$this->db->fetchAll($sql_p);
			return $row_p;
		}//func
		
		function getSumMemberBuyProfile($member_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("memberop","diary1");
			$sql_s="SELECT member,sum( amount ) AS sum_amt, sum( net_amt ) AS sum_net_amt
						FROM `diary1`
						WHERE member = '$member_no'
						AND doc_tp
						IN (
						'SL', 'VT'
						)
						AND flag <> 'C'";
			$row_s=$this->db->fetchAll($sql_s);
			return $row_s;
		}
		
		function getMemberPointToDay($member_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("pdy","mem_pt2");
			$sql_point="SELECT `doc_no` , `doc_dt` , `flag` , `member` , `point` , `shop` , `request_time`
								FROM `mem_pt2`
								WHERE 
								flag <> 'C'	AND 
								doc_dt='$this->doc_date' AND
								member = '$member_no'";
			$row_point=$this->db->fetchAll($sql_point);
			return $row_point;
		}//func		
		function getOtherPromotion($promo_code){
			/**
			 * @desc exam NEWBIRTH
			 * @param
			 */
			$sql_promo="SELECT 
									promo_des,start_baht,end_baht,type_discount,discount,play_main_promotion,
									play_last_promotion,get_promotion,member_discount,get_point,buy_type,buy_status,birthday_month,xpoint
								FROM
									`promo_other`
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									promo_code='$promo_code' AND
									'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_promo=$this->db->fetchAll($sql_promo);
			if(count($row_promo)>0){
				return json_encode($row_promo[0]);
			}else{
				return '';
			}
		}//func
		
		function getMemberPrivilege1($member_no,$birthday,$apply_date,$expire_date,$tcase){
			/***
			 * @desc modify:29042013
			 */
			$objCal=new Model_Calpromotion();
			$o=$objCal->promo_chk_hbd($member_no);
			//var_dump($o);
			
			if(empty($o)){
				 return $o;
				exit();
			}
			
//			echo "<pre>";
//			print_r($o);
//			echo "</pre>";
			
			//$o = json_decode($o,true);		       
		    $k=0;
	       foreach($o as $dataPro){
		        if($o[0]['promo_code']!=''){
		        	$p_code=$dataPro['promo_code'];
		        	$sql_pro="SELECT promo_pos FROM promo_head WHERE promo_code='$p_code'";		        	
		        	$row_pro=$this->db->fetchAll($sql_pro);
		        	if(!empty($row_pro)){
			        	$o[$k]['promo_pos']=$row_pro[0]['promo_pos'];
		        	}else{
		        		$o[$k]['promo_pos']='';
		        	}
		        }else{		        	
		        	$o[$k]['promo_pos']='';
		        }
		        $k++;
	       }//func
	        return $o;
		}//func
		
		function getMemberPrivilege2($member_no,$birthday,$apply_date,$expire_date,$tcase){
			/***
			* @desc modify:29042013
			 */
			$objCal=new Model_Calpromotion();
			$o=$objCal->promo_chk_other($member_no);
			//var_dump($o);
			
			if(empty($o)){
				 return $o;
				exit();
			}
			
//			echo "<pre>";
//			print_r($o);
//			echo "</pre>";
			
			//$o = json_decode($o,true);		       
		    $k=0;
	       foreach($o as $dataPro){
		        if($o[0]['promo_code']!=''){
		        	$p_code=$dataPro['promo_code'];
		        	$sql_pro="SELECT promo_pos FROM promo_head WHERE promo_code='$p_code'";		        	
		        	$row_pro=$this->db->fetchAll($sql_pro);
		        	if(!empty($row_pro)){
			        	$o[$k]['promo_pos']=$row_pro[0]['promo_pos'];
		        	}else{
		        		$o[$k]['promo_pos']='';
		        	}
		        }else{		        	
		        	$o[$k]['promo_pos']='';
		        }
		        $k++;
	       }//func
	        return $o;
		}//func
		
		function getMemberPrivilege3($member_no,$birthday,$apply_date,$expire_date,$tcase){
			/***
			* @desc modify:29042013
			 */
			$objCal=new Model_Calpromotion();
			$o=$objCal->promo_chk_play($member_no);
			//var_dump($o);
			
			if(empty($o)){
				 return $o;
				exit();
			}
			
//			echo "<pre>";
//			print_r($o);
//			echo "</pre>";
			
			//$o = json_decode($o,true);		       
		    $k=0;
	       foreach($o as $dataPro){
		        if($o[0]['promo_code']!=''){
		        	$p_code=$dataPro['promo_code'];
		        	$sql_pro="SELECT promo_pos FROM promo_head WHERE promo_code='$p_code'";		        	
		        	$row_pro=$this->db->fetchAll($sql_pro);
		        	if(!empty($row_pro)){
			        	$o[$k]['promo_pos']=$row_pro[0]['promo_pos'];
		        	}else{
		        		$o[$k]['promo_pos']='';
		        	}
		        }else{		        	
		        	$o[$k]['promo_pos']='';
		        }
		        $k++;
	       }//func
	        return $o;
		}//func
		
		function getMemberPrivilege($member_no,$birthday,$apply_date,$expire_date,$tcase){
			/**
			 * @desc
			 * @param
			 */
			if($tcase=='1'){
				$o=$this->getMemberPrivilege1($member_no,$birthday,$apply_date,$expire_date,$tcase);
				return $o;
				exit();
			}
			
			if($tcase=='2'){
				$o=$this->getMemberPrivilege2($member_no,$birthday,$apply_date,$expire_date,$tcase);
				return $o;
				exit();
			}
			
			if($tcase=='3'){
				$o=$this->getMemberPrivilege3($member_no,$birthday,$apply_date,$expire_date,$tcase);
				return $o;
				exit();
			}
			$ws = "http://10.100.53.2/wservice/webservices/services/promotions.php?";
		    $parameter = array(
		                    "brand"  =>$this->m_company_id,
		                    "dtype"=>"json",
		                    "act" =>"promotion",
		                    "data"=> array(
		                                        "member"  =>$member_no,
		                                        "shop"    =>$this->m_branch_id,
		                                        "birthday"  =>$birthday,
		                                        "apply_date"  =>$apply_date,
		                                        "expire_date" =>$expire_date,
		                                        "ip"  => $this->m_com_ip,
		    									"promo_set"=>$tcase
		                                    )
		                );		   
		    $src = $ws."callback=jsonpCallback&data=".serialize($parameter)."&_=1334128422190";
		    $o=@file_get_contents($src,0);
		    if ($o === FALSE || !$o){
		        //OFFLINE PROCESS
		       return array();
		    }
		    else{
		       $o = str_replace("jsonpCallback(","",$o);
		       $o = str_replace(")","",$o);
		       $o = json_decode($o,true);		       
		       $k=0;
		       foreach($o as $dataPro){
			        if($o[0]['promo_code']!=''){
			        	$p_code=$dataPro['promo_code'];
			        	$sql_pro="SELECT promo_pos FROM promo_head WHERE promo_code='$p_code'";
			        	$row_pro=$this->db->fetchAll($sql_pro);
			        	if(!empty($row_pro)){
				        	$o[$k]['promo_pos']=$row_pro[0]['promo_pos'];
			        	}else{
			        		$o[$k]['promo_pos']='';
			        	}
			        }else{		        	
			        	$o[$k]['promo_pos']='';
			        }
			        $k++;
		       }
		        //ONLINE DATA RETRUN		     
		        return $o;
		    } 
		}//func
		
		function setLogMemberPrivilege($member_no,$promo_code,$customer_id){
			/**
			 * @desc 15032013
			 */
			$status_qry="1";
			try{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("log","log_member_privilege");
				$sql_log="INSERT INTO `pos_ssup`.`log_member_privilege` (
									`id` ,
									`corporation_id`,
									`company_id`,
									`branch_id`,
									`doc_date` ,
									`member_no` ,
									`customer_id` ,
									`promo_code` ,
									`flag` ,
									`reg_date` ,
									`reg_time` ,
									`reg_user` ,
									`upd_date` ,
									`upd_time` ,
									`upd_user`
								)
								VALUES (
								NULL ,'$this->m_corporation_id','$this->m_company_id','$this->m_branch_id', '$this->doc_date',
								'$member_no','$customer_id', '$promo_code', '', CURDATE(),CURTIME(), 'IT', '', '', '')";				
				$this->db->query($sql_log);
			}catch(Zend_Db_Exception $e){
					$status_qry="0";
			}
			return $status_qry;
		}//func
		
		function unLogMemberPrivilege($member_no,$promo_code,$customer_id){
			/**
			 * @desc 15032013
			 */
			$status_qry="1";
			try{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("log","log_member_privilege");
				$sql_log="UPDATE `pos_ssup`.`log_member_privilege` 
								SET flag='C',
										upd_date=CURDATE(),
										upd_time=CURTIME(),
										upd_user='IT'
								WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`member_no`='$member_no' AND 
										`promo_code`='$promo_code' AND 
										`customer_id`='$customer_id' AND
										`doc_date`='$this->doc_date'";
				$this->db->query($sql_log);
			}catch(Zend_Db_Exception $e){
					$status_qry="0";
			}
			return $status_qry;
		}//func
		
		function clsLogMemberPrivilege(){
			/**
			 * @clear data previous 7 day
			 */
			try{
				$status_qry="1";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("log","log_member_privilege");
				$sql_cls="DELETE FROM `log_member_privilege` WHERE doc_date < DATE_SUB(NOW(), INTERVAL 7 DAY)";
				$this->db->query($sql_cls);
			}catch(Zend_Db_Exception $e){
				$status_qry="0";
			}
		}//func
		
		function getMemberPrivilege222($member_no,$birthday,$apply_date,$expire_date){
			/**
			 * @desc กำลังทดสอบการเปรียบเทียบความเร็วการ call service 10102012
			 * @modify is being modify new concept 
			 * @param
			 */
			
			$context = stream_context_create(array('http' => array('header'=>'Connection: close')));
			$ws = "http://10.100.53.2/wservice/webservices/services/promotions2.php?";
			$type = "json"; //Only Support JSON 
			$shop=$this->m_branch_id;
			$cid = $member_no;			
			$ip=$this->m_com_ip;
			$act = "promotion";
			$mtype="Member Promotion";
			$src2 = $ws."callback=jsonpCallback&cid=".$cid."&brand=op&dtype=".$type."&shop=".$shop."&act=".$act."&birthday=".$birthday."&apply_date=".$apply_date."&expire_date=".$expire_date."&mtype=".$mtype."&ip=".$ip."&_=1334128422190";
					
		    $parameter = array(
		                    "brand"  =>$this->m_company_id,
		                    "dtype"=>"json",
		                    "act" =>"promotion",
		                    "data"=> array(
		                                        "member"  =>$member_no,
		                                        "shop"    =>$this->m_branch_id,
		                                        "birthday"  =>$birthday,
		                                        "apply_date"  =>$apply_date,
		                                        "expire_date" =>$expire_date,
		                                        "ip"  => $this->m_com_ip,
		    									"mtype"=>$mtype
		                                    )
		                );			       
		   //$src = $ws."callback=jsonpCallback&data=".serialize($parameter)."&_=1334128422190";
		   $src = $src2."&_=1334128422190";
		   //$o=@file_get_contents($src,0);
		  	$o=file_get_contents($src,false,$context); 
		    if ($o === FALSE || !$o){
		        //OFFLINE PROCESS
		       return array();
		    }
		    else{
		        $o = str_replace("jsonpCallback(","",$o);
		        $o = str_replace(")","",$o);
		        $o = json_decode($o,true);
		        //ONLINE DATA RETRUN		     
		        return $o;
		    } 
		}//func
		
		function promoPercentRange($promo_code,$promo_id,$net_amt){
			/**
			 * @desc
			 * @param
			 */
			$this->promo_code=$promo_code;
			$this->promo_id=$promo_id;			
			//หายอด net โดยไม่รวมรายการที่ได้ส่วนลด 50 % แล้ว
			if($this->promo_code=='BURNPOINT2'){
				//*WR20120904
				$res_tbltemp=parent::countDiaryTemp();
				$arr_tbltemp=explode('#',$res_tbltemp);
				$tbl_temp=$arr_tbltemp[1];
				//*WR20120904
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
				$sql_net="SELECT SUM(net_amt) as net_amt 
								FROM `$tbl_temp` 
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND 
									`computer_no` ='$this->m_computer_no' AND
								 	`computer_ip` ='$this->m_com_ip' AND
								 	`status_no`='04' AND
								 	`amount`=`net_amt` AND
									`doc_date` ='$this->doc_date'";
				$net_amt=$this->db->fetchOne($sql_net);
			}
			
			$this->net_amt=$net_amt;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point1_detail");
			$sql_prange="SELECT 
									discount,point,start_percent,end_percent
								FROM 
									promo_point1_detail 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND 
									promo_code='$this->promo_code' AND
									promo_id='$this->promo_id'";
			$row_range=$this->db->fetchAll($sql_prange);
			$arr_prange=array();
			$arr_prange[0]['net_amt']=$this->net_amt;
			if(count($row_range)>0){
				$discount=$row_range[0]['discount'];//ส่วนลดโปรโมชั่น
				$point=$row_range[0]['point'];
				$start_percent=$row_range[0]['start_percent'];
				$end_percent=$row_range[0]['end_percent'];
				$k=0;
				for($i=$start_percent;$i<=$end_percent;$i++){
					$arr_prange[$k]['percent']=$i;
					$tmp_discount=floor($this->net_amt*($i/100));//ส่วนลด step1
					$tmp_discount=number_format($tmp_discount,2,'.','');
					$aa=floor($tmp_discount/$discount);//เอาผล
					$arr_prange[$k]['point']=$aa;
					$arr_prange[$k]['discount']=$aa*$discount;					
					$k++;
				}
			}
			return $arr_prange;
		}//func
		
		function rePromoDiscount($promo_code,$promo_id){
			/**
			 * @desc fixed used for bill 04 สมาชิกแลกคะแนน only
			 * * ปัญหาคือตอนนี้ promo_code BURNPOINT2 สามารถเล่นร่วมกับ โปรหลัก ซื้อสินค้าปกติ 1 ชิ้นๆต่อไปลด 50%
			 * @param String $promo_code ปกติบิล 04 จะมี promo_code เช่น BURNPOINT2 promo_id เช่น	STKER-2
			 * @return
			 */
			$this->promo_code=$promo_code;
			$this->promo_id=$promo_id;
			$result='0';
			//*WR20120904
			$res_tbltemp=parent::countDiaryTemp();
			$arr_tbltemp=explode('#',$res_tbltemp);
			$tbl_temp=$arr_tbltemp[1];
			//*WR20120904
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($this->promo_code=='BURNPOINT2'){
				$sql_item="SELECT id,product_id,amount,net_amt
								FROM `$tbl_temp`
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND 
										`computer_no` ='$this->m_computer_no' AND
									 	`computer_ip` ='$this->m_com_ip' AND
									 	`status_no`='04' AND
								 		`special_discount`>'0' AND
										`doc_date` ='$this->doc_date' ORDER BY id";	
			}else{
				$sql_item="SELECT id,product_id,amount,net_amt
								FROM `$tbl_temp`
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND 
										`promo_code`='$this->promo_id' AND
										`computer_no` ='$this->m_computer_no' AND
									 	`computer_ip` ='$this->m_com_ip' AND
										`doc_date` ='$this->doc_date' ORDER BY id";
			}
			
			$row_items=$this->db->fetchAll($sql_item);
			if(count($row_items)>0){
				foreach($row_items as $data){
					$this->amount=$data['amount'];
					/*
					 $sql_upd="UPDATE `$tbl_temp`
										SET
											special_discount='0',
											net_amt='$this->amount'
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND 
											`promo_code`='$this->promo_id' AND
											`computer_no` ='$this->m_computer_no' AND
									 		`computer_ip` ='$this->m_com_ip' AND
											`doc_date` ='$this->doc_date' AND
											`id`='$data[id]'";
					 */
					//*WR20120904
					$sql_upd="UPDATE `$tbl_temp`
										SET
											special_discount='0',
											net_amt='$this->amount'
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`computer_no` ='$this->m_computer_no' AND
									 		`computer_ip` ='$this->m_com_ip' AND
											`doc_date` ='$this->doc_date' AND
											`id`='$data[id]'";
					$this->db->query($sql_upd);
				}
				$result='1';
			}				
			return $result;
		}//func
		
		function promoDiscount($promo_code,$promo_id,$promo_tp,$s_percent='',$s_point='',$s_discount=''){
			/**
			 * @desc
			 * * กรณี BURNPOINT2 ปรับส่วนลดตาม % เฉพาะกรณีสินค้าไม่ได้ส่วนลดเท่านั้น
			 * @return
			 */
			$this->promo_code=$promo_code;
			$this->promo_id=$promo_id;
			$this->promo_tp=$promo_tp;
			$result='0';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point1_detail");
			$sql_pt="SELECT 
						start_baht,
						end_baht,
						discount,
						point,
						cal_amount,
						type_discount
					FROM 
						promo_point1_detail
					WHERE 
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND 
						promo_code='$this->promo_code' AND
						promo_id='$this->promo_id'";
			$row_pt=$this->db->fetchAll($sql_pt);
			if(count($row_pt)>0){
						//*WR20120904
						$res_tbltemp=parent::countDiaryTemp();
						$arr_tbltemp=explode('#',$res_tbltemp);
						$tbl_temp=$arr_tbltemp[1];
						//*WR20120904
						$start_baht=$row_pt[0]['start_baht'];
						$end_baht=$row_pt[0]['end_baht'];
						$discount=$row_pt[0]['discount'];
						$point=$row_pt[0]['point'];
						$cal_amount=$row_pt[0]['cal_amount'];
						$type_discount=$row_pt[0]['type_discount'];
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
						if($this->promo_code=='BURNPOINT2'){
							$sql_item="SELECT id,promo_code,product_id,amount,discount,net_amt
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
									 		 	`status_no` ='04' AND
												`doc_date` ='$this->doc_date' ORDER BY id";
						}else{
							$sql_item="SELECT id,product_id,amount,net_amt
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' ORDER BY id";
						}
						
						$row_items=$this->db->fetchAll($sql_item);
						if(count($row_items)>0){	
							if($this->promo_tp=='N'){
									if(trim($type_discount)=='DISCOUNT'){		
										if(intval($cal_amount)!=0){						
											$pdiscount=round(($discount/$cal_amount)*100,2);
										}else{
											$pdiscount=0.00;
										}
									}else if(trim($type_discount)=='PERCENT'){
										$pdiscount=$discount;
									}
									$balance=$cal_amount;
									foreach($row_items as $data){
										$this->amount=$data['amount'];
										if($balance>0){
											if($data['net_amt']>$balance){
												$x_amt=$balance;
											}else{
												$x_amt=$data['net_amt'];
											}
											$balance=$balance-$x_amt;
											$items_discount=$x_amt*($pdiscount/100);
											$this->net_amt=$this->amount-$items_discount;
											//assum data in trn_tdiary2_sl only
											$sql_upd="UPDATE `$tbl_temp`
														SET
															special_discount='$items_discount',
															net_amt='$this->net_amt'
														WHERE
															`corporation_id`='$this->m_corporation_id' AND
															`company_id`='$this->m_company_id' AND 
															`promo_code`='$this->promo_id' AND
															`computer_no` ='$this->m_computer_no' AND
									 		 				`computer_ip` ='$this->m_com_ip' AND
															`doc_date` ='$this->doc_date' AND
															`id`='$data[id]'";
											$this->db->query($sql_upd);
										}
									}//foreach
							}else if($this->promo_tp=='S'){
								//assume data in tmp1 of joke only
									$balance=$s_discount;
									$tmp_sum_discount=0.00;
									foreach($row_items as $data){
										//find discount
										if(intval($data['discount'])>0)
											continue;
										$this->amount=$data['amount'];
										if($balance>0){			
											$items_discount=$this->amount*($s_percent/100);
											$items_discount_roundup=round($items_discount,2);
											$items_discount_roundup=number_format($items_discount_roundup,2,'.','');
											$items_discount_roundup=parent::getSatang($items_discount_roundup,'up');
											$tmp_sum_discount+=$items_discount_roundup;
											if($tmp_sum_discount>$s_discount){
												$items_discount_roundup=$s_discount-($tmp_sum_discount-$items_discount_roundup);
											}	
											$balance=$balance-$items_discount_roundup;
											$this->net_amt=$this->amount-$items_discount_roundup;
											if($this->promo_code=='BURNPOINT2'){
												$sql_upd="UPDATE `$tbl_temp`
															SET
																special_discount='$items_discount_roundup',
																net_amt='$this->net_amt'
															WHERE
																`corporation_id`='$this->m_corporation_id' AND
																`company_id`='$this->m_company_id' AND 
																`computer_no` ='$this->m_computer_no' AND
										 		 				`computer_ip` ='$this->m_com_ip' AND
																`doc_date` ='$this->doc_date' AND
																`amount` =`net_amt` AND
																`id`='$data[id]'";
											}else{
													$sql_upd="UPDATE `$tbl_temp`
														SET
															special_discount='$items_discount_roundup',
															net_amt='$this->net_amt'
														WHERE
															`corporation_id`='$this->m_corporation_id' AND
															`company_id`='$this->m_company_id' AND 
															`promo_code`='$this->promo_id' AND
															`computer_no` ='$this->m_computer_no' AND
									 		 				`computer_ip` ='$this->m_com_ip' AND
															`doc_date` ='$this->doc_date' AND
															`id`='$data[id]'";
											}
											$this->db->query($sql_upd);											
										}
									}//foreach
							}//end S
							
							$result='3';
							
						}else{
							$result='2';
						}		
			}else{
				return '1';
			}
			return $result;
		}//func
		
		function getPromoPointHead(){
			/**
			 * @desc
			 * @return
			 * @modify 16112011
			 */
			 //check branch
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_branch");
			$sql_branch="SELECT promo_code 
							FROM promo_branch 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									(branch_id='$this->m_branch_id' OR branch_id='ALL') AND
									'$this->doc_date' BETWEEN start_date AND end_date";		
			$rows_promocode=$this->db->fetchAll($sql_branch);
			$arr_promo=array();
			$i=0;
			foreach($rows_promocode as $data){
				$sql_promo="SELECT 
										promo_code,promo_des,promo_tp,
										play_main_promotion,
										play_last_promotion,
										get_promotion,
										member_discount,
										get_point
									FROM promo_point1_head 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										promo_code='$data[promo_code]' AND
										'$this->doc_date' BETWEEN start_date AND end_date
									ORDER BY promo_code";
				$rows_promo=$this->db->fetchAll($sql_promo);
				if(count($rows_promo)>0){
					$arr_promo[$i]['promo_code']=$rows_promo[0]['promo_code'];
					$arr_promo[$i]['promo_des']=$rows_promo[0]['promo_des'];
					$arr_promo[$i]['promo_tp']=$rows_promo[0]['promo_tp'];
					$arr_promo[$i]['play_main_promotion']=$rows_promo[0]['play_main_promotion'];
					$arr_promo[$i]['play_last_promotion']=$rows_promo[0]['play_last_promotion'];
					$arr_promo[$i]['get_promotion']=$rows_promo[0]['get_promotion'];
					$arr_promo[$i]['member_discount']=$rows_promo[0]['member_discount'];
					$arr_promo[$i]['get_point']=$rows_promo[0]['get_point'];
					$i++;
				}
			}
			return $arr_promo;
		}//func
		
		function getPromoPointDetail($promo_code,$promo_tp,$member_no){
			/**
			 * @desc
			 * @return
			 * @modify 16112011
			 */
			$objPos=new Model_PosGlobal();
			$member_point=$objPos->getMemberPoint($member_no);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point1_detail");
			$sql_promo="SELECT promo_id,promo_des,point,start_baht,end_baht,type_discount,discount 
							FROM promo_point1_detail 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									promo_code='$promo_code' AND
									point<='$member_point' AND
									'$this->doc_date' BETWEEN start_date AND end_date
								ORDER BY promo_id";
			$rows_promo=$this->db->fetchAll($sql_promo);
			if(count($rows_promo)<1){
				$rows_promo[0]['promo_id']="0";
				$rows_promo[0]['promo_des']="คะแนนสะสมไม่พอสำหรับการใช้";
				$rows_promo[0]['point']="0";
				$rows_promo[0]['start_baht']="0";
				$rows_promo[0]['end_baht']="0";
				$rows_promo[0]['type_discount']="0";
				$rows_promo[0]['discount']="0";
			}
			return $rows_promo;
		}//func
		
		function getPromoActPointHead(){
			/**
			 * @desc
			 * @return
			 * @modify 16112011
			 */
			 //check branch
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_branch");
			$sql_branch="SELECT promo_code 
								FROM promo_branch 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										(branch_id='$this->m_branch_id' OR branch_id='ALL') AND
										'$this->doc_date' BETWEEN start_date AND end_date";		
			$rows_promocode=$this->db->fetchAll($sql_branch);
			$arr_promo=array();
			$i=0;
			foreach($rows_promocode as $data){
				$sql_promo="SELECT promo_code,promo_des,point,amount
									FROM promo_point2_head 
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											promo_code='$data[promo_code]' AND
											'$this->doc_date' BETWEEN start_date AND end_date
										ORDER BY promo_code";				
				$rows_promo=$this->db->fetchAll($sql_promo);
				if(count($rows_promo)>0){					
					$arr_promo[$i]['promo_code']=$rows_promo[0]['promo_code'];
					$arr_promo[$i]['promo_des']=$rows_promo[0]['promo_des'];					
					$arr_promo[$i]['point']=$rows_promo[0]['point'];
					$arr_promo[$i]['amount']=$rows_promo[0]['amount'];
					$i++;
				}
			}
			return $arr_promo;
		}//func
		
		function getPromoActPointDetail($promo_code,$member_no){
			/**
			 * @desc
			 * @return
			 * @modify 16112011
			 */
			$objPos=new Model_PosGlobal();
			$member_point=$objPos->getMemberPoint($member_no);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point2_detail");
			$arr_promo=array();
			$sql_promo="SELECT promo_code,promo_des,point,amount
								FROM promo_point2_head 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										promo_code='$promo_code'";
			$rows_promo1=$this->db->fetchAll($sql_promo);
			if(count($rows_promo1)>0){
				$arr_promo[0]['promo_code']=$rows_promo1[0]['promo_code'];
				$arr_promo[0]['promo_des']=$rows_promo1[0]['promo_des'];					
				$arr_promo[0]['point']=$rows_promo1[0]['point'];
				$arr_promo[0]['amount']=$rows_promo1[0]['amount'];				
			}
			
			$sql_promo="SELECT line,promo_code,promo_des
								FROM promo_point2_detail 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										promo_code='$promo_code' AND
										'$this->doc_date' BETWEEN start_date AND end_date
									ORDER BY promo_code";
			$rows_promo2=$this->db->fetchAll($sql_promo);
			if(count($rows_promo2)<1){
				$arr_promo[0]['promo_code']="0";
				$arr_promo[0]['promo_des']="คะแนนสะสมไม่พอสำหรับการใช้";				
			}else{
				$i=0;
				foreach($rows_promo2 as $data){
					$arr_promo[$i]['line']=$data['line'];
					$arr_promo[$i]['promo_des2']=$data['promo_des'];
					$i++;
				}
			}
			return $arr_promo;
		}//func
		
		function iniTblRwd(){
			/**
			 * @desc
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary1_rwd");
			$sql_rd2="SELECT product_id,quantity,stock_st
							FROM
								trn_tdiary2_rwd
							WHERE
								 `corporation_id` ='$this->m_corporation_id' AND
								 `company_id` ='$this->m_company_id' AND
								 `branch_id` ='$this->m_branch_id' AND
								 `computer_no`='$this->m_computer_no' AND
 								 `computer_ip`='$this->m_com_ip'	";
			$rows_rd2=$this->db->fetchAll($sql_rd2);
			if(count($rows_rd2)>0){
				foreach($rows_rd2 as $data){
					$product_id=$data['product_id'];
					$quantity=$data['quantity'];
					$stock_st=$data['stock_st'];
					$this->increaseStock($product_id,$quantity);
				}
				
				$sql_d2="DELETE FROM trn_tdiary2_rwd 
									WHERE 
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `computer_no`='$this->m_computer_no' AND
	 										 `computer_ip`='$this->m_com_ip'";
				$this->db->query($sql_d2);
				$sql_d1="DELETE FROM trn_tdiary1_rwd 
									WHERE 
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `computer_no`='$this->m_computer_no' AND
	 										 `computer_ip`='$this->m_com_ip'";
				$this->db->query($sql_d1);
			}//end count
		}//func	
		
		function getReceiveReward($member_no){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_rwd="SELECT a.member_id,a.doc_date,a.doc_no,b.product_id,b.quantity,b.promo_qty,b.seq
						 FROM trn_diary1 AS a INNER JOIN trn_diary2 AS b
						 		  ON(a.doc_no=b.doc_no)
						WHERE 
							a.corporation_id='$this->m_corporation_id' AND
							a.company_id='$this->m_company_id' AND
							a.branch_id='$this->m_branch_id' AND
							a.status_no='18' AND 
							a.flag<>'C' AND 
							a.member_id='$member_no' AND
							b.quantity-b.promo_qty>'0'
						ORDER BY a.doc_date,a.doc_no";		
			$rows_rwd=$this->db->fetchAll($sql_rwd);
			return $rows_rwd;
		}//func
		
		function chekReceiveStock($items){
			/**
			 * 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_opreward");
			$arr_1=explode('&',$items);
			$arr_date=explode('-',$this->doc_date);
			$yyyy=$arr_date[0];
			$mm=intval($arr_date[1]);
			$arr_stk=array();
			$i=0;
			foreach($arr_1 as $dataL1){
				$arr_2=explode('@',$dataL1);
				//0=doc_no,1=product_id,2=quantity
				$doc_no=$arr_2[0];
				$product_id=$arr_2[1];
				$quantity=$arr_2[2];
				$sql_oprwd="SELECT b.onhand,a.product_id 
									FROM 
										com_opreward AS a LEFT JOIN com_stock_master AS b
										ON(a.product_id=b.product_id)
									WHERE 
										a.`code`='$product_id' AND 										
										b.`year`='$yyyy' AND
										b.`month`='$mm' AND 
										b.`product_id`=a.`product_id`";		
				$row_oprwd=$this->db->fetchAll($sql_oprwd);
				if(count($row_oprwd)>0){
					if($row_oprwd[0]['onhand']<$quantity){
						$arr_stk[$i]['product_id']=$product_id;
						$arr_stk[$i]['error_status']='0';//stock ไม่พอ
						$i++;
					}
				}else{
					//stock no have
					$arr_stk[$i]['product_id']=$product_id;
					$arr_stk[$i]['error_status']='1';//product_id ไม่พบ	
					$i++;
				}					
			}//foreach
			return $arr_stk;
		}//func
		
		function saveReceiveReward($member_no,$items){		
			/**
			 * @desc ปัญหาตอนนี้คือในทะเบียน com_product_master 
			 * @desc field barcode คือ product_id จริงแต่ field product_id เป็นรหัส com_opreward->code จึงทำให้ระบบไม่ตัด stock
			 * @return
			 */
			$this->doc_tp="DN";
			$this->status_no="00";
			$point1=0;
			$point2=0;
			$redeem_point=0;
			$total_point=0;
			$this->quantity=1;
			$this->price=0.00;
			$this->amount=0.00;
			$this->net_amt=0.00;
			$this->promo_code='';
			$this->tax_type='';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$arr_docno=array();
			$arr_1=explode('&',$items);
			$k=0;
			foreach($arr_1 as $dataL1){
				$arr_2=explode('@',$dataL1);
					//0=doc_no,1=product_id,2=quantity
					$doc_no=$arr_2[0];
					$code=$arr_2[1];
					$quantity=$arr_2[2];
					$arr_docno[$k]['doc_no']=$doc_no;
					$arr_docno[$k]['product_id']=$code;			
					$arr_docno[$k]['quantity']=$quantity;				
					///////////////////////////////////////////////////////
					$this->stock_st='-1';
					$this->doc_time=date("H:i:s");
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_rwd");
					$sql_seq="SELECT MAX(seq) AS max_seq 
										FROM trn_tdiary2_rwd 
										WHERE
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `computer_no`='$this->m_computer_no' AND
 											 `computer_ip`='$this->m_com_ip' AND
											 `doc_date` ='$this->doc_date'";
					$row_seq=$this->db->fetchCol($sql_seq);
					$this->seq=$row_seq[0]+1;
					$this->promo_seq=$this->seq;
					
					$sql_oprwd="SELECT code,product_id FROM `com_opreward` WHERE code='$code'";
					$row_oprwd=$this->db->fetchAll($sql_oprwd);
					$product_id=@$row_oprwd[0]['product_id'];
					//Temp
					$sql_pdt="SELECT product_id,name_product FROM com_product_master WHERE barcode='$code'";					
					$row_pdt=$this->db->fetchAll($sql_pdt);
					$product_name=@$row_pdt[0]['name_product'];			
					
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_rwd");					
					$sql_insert="INSERT INTO trn_tdiary2_rwd
										SET 
										  `corporation_id` ='$this->m_corporation_id',
										  `company_id` ='$this->m_company_id',
										  `branch_id` ='$this->m_branch_id',
										  `doc_date` ='$this->doc_date',
										  `doc_time` ='$this->doc_time',
										  `doc_no` ='',
										  `doc_tp` ='$this->doc_tp',
										  `status_no`='$this->status_no',
										  `seq` ='$this->seq',
										  `promo_code` ='$this->promo_code',
										  `promo_seq` ='$this->promo_seq',
										  `promo_st` ='$this->promo_st',
										  `promo_tp` ='',
										  `product_id`='$product_id',		
										  `name_product`='$product_name',				
										  `unit`='$this->unit',										  
										  `stock_st` ='$this->stock_st',								  
										  `price` ='$this->price',
										  `quantity`='$quantity',
										  `amount`='$this->amount',
										  `discount`='',								  
										  `member_discount1`='',
										  `member_discount2`='',								  
										  `net_amt`='$this->net_amt',
										  `product_status`='',
										  `get_point`='',
										  `discount_member`='',
										  `cal_amt`='Y',
										  `tax_type`='$this->tax_type',
										  `computer_no`='$this->m_computer_no',
										  `computer_ip`='$this->m_com_ip',
										  `reg_date`='$this->doc_date',
										  `reg_time`='$this->doc_time',
										  `reg_user`='$this->user_id'";	
					$stmt_insert=$this->db->exec($sql_insert);		
					$this->decreaseStock($product_id,$quantity);
					$k++;
			}//foreach
			
					
			$sql_sum="SELECT 
									SUM(quantity) AS sum_quantiy,SUM(amount) AS sum_amount,SUM(net_amt) AS sum_net
						  FROM 
						  			trn_tdiary2_rwd 
						  WHERE
						  		 `corporation_id` ='$this->m_corporation_id' AND
								 `company_id` ='$this->m_company_id' AND
								 `branch_id` ='$this->m_branch_id' AND
								 `computer_no`='$this->m_computer_no' AND
 								 `computer_ip`='$this->m_com_ip' AND
								 `doc_date` ='$this->doc_date'";		
			$row_sum=$this->db->fetchAll($sql_sum);			
			$this->quantity=$row_sum[0]['sum_quantiy'];
			$this->amount=$row_sum[0]['sum_amount'];
			$this->net_amt=$row_sum[0]['sum_net'];
			
			////////////////////////////////// OLD //////////////////////////////////////
			//create doc_no for diary
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->doc_time=date("H:i:s");
			$status_res=0;
			$this->db->beginTransaction();
				try{
					$sql_add2h="INSERT INTO trn_tdiary1_rwd
									SET
										`corporation_id`='$this->m_corporation_id',
										`company_id`='$this->m_company_id',
										`branch_id`='$this->m_branch_id',
										`doc_date`='$this->doc_date',
										`doc_time`='$this->doc_time',
										`doc_no`='$this->m_doc_no',
										`doc_tp`='$this->doc_tp',
										`status_no`='$this->status_no',
										`member_id`='$member_no',
										`member_percent`='',
										`refer_member_id`='',
										`refer_doc_no`='$this->promo_code',
										`quantity`='$this->quantity',
										`amount`='$this->amount',
										`discount`='',
										`member_discount1`='',
										`member_discount2`='',
										`co_promo_discount`='',
										`coupon_discount`='',
										`special_discount`='',
										`other_discount`='',
										`net_amt`='$this->net_amt',
										`ex_vat_amt`='',
										`ex_vat_net`='',
										`vat`='',
										`point1`='',
										`point2`='',
										`redeem_point`='$redeem_point',
										`total_point`='',
										`paid`='',
										`pay_cash`='',
										`pay_credit`='',
										`change`='',
										`coupon_code`='',
										`pay_cash_coupon`='',
										`credit_no`='',
										`credit_tp`='',
										`bank_tp`='',
										`application_id`='',
										`new_member_st`='',
										`computer_no`='$this->m_computer_no',
										`computer_ip`='$this->m_com_ip',
										`pos_id`='$this->m_pos_id',									
										`user_id`='$this->user_id',	
										`cashier_id`='$this->user_id',
										`saleman_id`='$this->user_id',								
										`doc_remark`='',
										`name`='',
										`address1`='',
										`address2`='',
										`address3`='',
										`reg_date`='$this->doc_date',
									  	`reg_time`='$this->doc_time',
									    `reg_user`='$this->user_id'";		
					$this->db->query($sql_add2h);	
					
					$sql_upd2l="UPDATE trn_tdiary2_rwd 
									SET doc_no='$this->m_doc_no' 
									WHERE
											 `corporation_id` ='$this->m_corporation_id' AND
											 `company_id` ='$this->m_company_id' AND
											 `branch_id` ='$this->m_branch_id' AND
											 `computer_no`='$this->m_computer_no' AND
 											 `computer_ip`='$this->m_com_ip' AND
											 `doc_date` ='$this->doc_date'";
					$this->db->query($sql_upd2l);
					
					//save data to trn_diary2
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
										  `reg_date`,
										  `reg_time`,
										  `reg_user`
									FROM
										`trn_tdiary2_rwd`
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND 
										doc_no='$this->m_doc_no'";	
					$this->db->query($sql_diary2);
					
					//save data  to trn_diary1
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
												`application_id`,
												`new_member_st`,
												`computer_no`,
												`pos_id`,
												`user_id`,
												`cashier_id`,
												`saleman_id`,
												`name`,
												`address1`,
												`address2`,
												`address3`,
												`doc_remark`,
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
											`member_id`,
											`member_percent`,
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
											`application_id`,
											`new_member_st`,
											`computer_no`,
											`pos_id`,
											`user_id`,
											`cashier_id`,
											`saleman_id`,
											`name`,
											`address1`,
											`address2`,
											`address3`,
											`doc_remark`,
											`reg_date`,
											`reg_time`,
											`reg_user`
										FROM 
											`trn_tdiary1_rwd`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND 
											doc_no='$this->m_doc_no'";
					$this->db->query($sql_diary1);				
					
					//update cn_remark to trn_dairy2
					foreach($arr_docno as $data){
						$doc_no_upd=$data['doc_no'];
						$product_id_upd=$data['product_id'];
						$quantity_upd=$data['quantity'];
						$sql_upd="UPDATE trn_diary2 
											SET
												promo_qty=promo_qty+'$quantity_upd', 
												cn_remark='$this->m_doc_no'
									WHERE doc_no='$doc_no_upd' AND product_id='$product_id_upd'";
						$this->db->query($sql_upd);
					}							
					$this->db->commit();
					$status_res=1;
				}catch(Zend_Db_Exception $e){
					$this->msg_error=$e->getMessage();
					$this->db->rollBack();				
				}
				if($status_res==1){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
					//update table doc_no
					$arr_temp=explode("-",$this->m_doc_no);
					$this->doc_no_update=intval($arr_temp[2])+1;
					$sql_inc_docno="UPDATE 
												com_doc_no 
											SET 
												doc_no='$this->doc_no_update',
												upd_date='$this->doc_date',
						   						upd_time='$this->doc_time',
						   						upd_user='$this->employee_id'
											WHERE 
												corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND 	
												branch_id='$this->m_branch_id' AND 
												branch_no='$this->m_branch_no' AND
												doc_tp='$this->doc_tp'";
					$this->db->query($sql_inc_docno);
					////////COMPLETE TRANSACTION///////////
					parent::TrancateTable("trn_tdiary1_rwd");
					parent::TrancateTable("trn_tdiary2_rwd");
					////////COMPLETE TRANSACTION///////////
					$strResult= "1#".$this->m_doc_no."#".$this->doc_tp."#".$this->m_thermal_printer;
				}else{
					$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer;
				}
				return $strResult;
				////////////////////////////////// OLD //////////////////////////////////////
		}//func
		
		function chkRwdProduct($promo_code,$product_id){
			/**
			 * @desc check product exist in promo_code
			 * @param String $promo_code :
			 * @param String $product_id :
			 * @return Boolean Y is exist and N is not found
			 * @modify 20120105
			 */
			$this->promo_code=$promo_code;
			$status_exist='N';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point3_detail");
			$sql_product="SELECT 
								COUNT(*)
							FROM 
								`promo_point3_detail` 
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								promo_code = '$this->promo_code' AND
								product_id='$product_id' AND
								'$this->doc_date' BETWEEN start_date AND end_date";
			$n=$this->db->fetchOne($sql_product);
			if($n>0){
				$status_exist='Y';
			}
			return $status_exist;
		}//func		
		
		function chkRwdComplete(){
			/**
			 * @desc : ตรวจสอบกรณี fix_quantity='Y'
			 * @param
			 * @return $str_chkqty='' ผ่าน,$str_chkqty<>'' ยังไม่ครบจำนวนที่ fix ไว้
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_pro="SELECT 
									promo_seq,promo_code,SUM(quantity) AS sumqty
							FROM
									`trn_tdiary2_sl`
							 WHERE
						 			`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND 
									`computer_no` ='$this->m_computer_no' AND
									`computer_ip` ='$this->m_com_ip' AND
									`doc_date`='$this->doc_date'
							GROUP BY promo_seq";
			$row_pro=$this->db->fetchAll($sql_pro);
			//$arr_chkqty=array();
			$str_chkqty="";
			if(count($row_pro)>0){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point3_head");
				foreach($row_pro as $dataPro){
					$promo_code=$dataPro['promo_code'];
					$promo_seq=$dataPro['promo_seq'];
					$quantity=intval($dataPro['sumqty']);
					$sql_chkqty="SELECT quantity 
									  FROM `promo_point3_head` 
									  WHERE
									  		corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											promo_code='$promo_code' AND
											fix_quantity='Y' AND
											'$this->doc_date' BETWEEN start_date AND end_date";
					$row_fixqty=$this->db->fetchAll($sql_chkqty);
					
					if(count($row_fixqty)>0){
						$qty=intval($row_fixqty[0]['quantity']);
						if($quantity<$qty){
							$str_chkqty=$promo_code."#".$qty;
						}
					}
					
					
				}
			}
			return $str_chkqty;
		}//func
		
		function getRwdProduct($promo_code){
			/**
			 * @desc get product detail for redeempoint rewards
			 * @param String $promo_code :
			 * @return Array $rows_product :
			 */
			$this->promo_code=$promo_code;
			$sql_product="SELECT 
								a.product_id,a.amount,b.name_product
							FROM 
								`promo_point3_detail` AS a LEFT JOIN `com_product_master` AS b
								ON(a.corporation_id=b.corporation_id AND a.company_id=b.company_id AND a.product_id=b.product_id)
							WHERE 
								a.corporation_id='$this->m_corporation_id' AND
								a.company_id='$this->m_company_id' AND
								a.promo_code = '$this->promo_code' AND
								'$this->doc_date' BETWEEN a.start_date AND a.end_date";
			$rows_product=$this->db->fetchAll($sql_product);
			return $rows_product;			
		}//func
		
		function getRwdPromoHead($status_no){
			/**
			 * @desc for bill
			 * @param String $status_no : 
			 * @return
			 * @modify 19122011
			 */
			 //check branch
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_branch");
			$sql_branch="SELECT promo_code 
								FROM promo_branch 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										(branch_id='$this->m_branch_id' OR branch_id='ALL') AND
										'$this->doc_date' BETWEEN start_date AND end_date";
			$rows_promocode=$this->db->fetchAll($sql_branch);
			$arr_promo=array();
			$i=0;
			foreach($rows_promocode as $data){
				$sql_promo="SELECT 
										promo_code,promo_des,promo_tp,start_date,end_date,
										point,point_per_baht,start_baht,end_baht,quantity,
										fix_quantity,product_set ,scan_barcode_coupon,status_no
								FROM promo_point3_head 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									promo_code='$data[promo_code]' AND
									status_no='$status_no' AND
									'$this->doc_date' BETWEEN start_date AND end_date
								ORDER BY promo_code";
				$rows_promo=$this->db->fetchAll($sql_promo);
				if(count($rows_promo)>0){
					$arr_promo[$i]['promo_code']=$rows_promo[0]['promo_code'];
					$arr_promo[$i]['promo_des']=$rows_promo[0]['promo_des'];
					$arr_promo[$i]['point']=$rows_promo[0]['point'];
					$arr_promo[$i]['promo_tp']=$rows_promo[0]['promo_tp'];
					$arr_promo[$i]['point_per_baht']=$rows_promo[0]['point_per_baht'];
					$arr_promo[$i]['start_baht']=$rows_promo[0]['start_baht'];
					$arr_promo[$i]['end_baht']=$rows_promo[0]['end_baht'];
					$arr_promo[$i]['quantity']=$rows_promo[0]['quantity'];
					$arr_promo[$i]['fix_quantity']=$rows_promo[0]['fix_quantity'];
					$arr_promo[$i]['product_set']=$rows_promo[0]['product_set'];
					$arr_promo[$i]['scan_barcode_coupon']=$rows_promo[0]['scan_barcode_coupon'];
					$arr_promo[$i]['status_no']=$rows_promo[0]['status_no'];
					$i++;
				}
			}			
			return $arr_promo;
		}//func
		
		function addProductItem($promo_code,$product_id,$fix_quantity,$quantity,$status_no,$amount){
			/**
			 * @desc บันทึกสินค้า ราย item
			 * @param String $promo_code :
			 * @param String $product_id :
			 * @param String $quantity :
			 * @param String $status_no :
			 * @return
			 */
			$this->promo_code=$promo_code;
			$this->product_id=$product_id;
			$this->quantity=$quantity;
			$this->status_no=$status_no;
			$this->doc_tp=parent::getDocTp($this->status_no);
			$arr_product=parent::browsProduct($this->product_id);	
			
			if(empty($arr_product)){
				//สินค้าไม่ต้องตัดสต๊อก
				$this->stock_st=0;
			}else{
				//สินค้าต้องตัดสต๊อก
				$this->stock_st=parent::getStockSt($this->doc_tp);
				$balance=$arr_product[0]['onhand']-$arr_product[0]['allocate'];
				if($balance<$this->quantity){
					$proc_status='2';
				}
			}

			/*
			$this->price=$arr_product[0]['price'];
			$this->tax_type=$arr_product[0]['tax_type'];
			$this->amount=$this->price * $this->quantity;
			$this->net_amt=$this->quantity*$this->price;
			*/
			//กลับมาดูใหม่
			$this->price=$amount;
			$this->tax_type=@$arr_product[0]['tax_type'];
			$this->amount=$this->price * $this->quantity;
			$this->net_amt=$this->amount;
			$this->doc_time=date("H:i:s");
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			//old trn_tdiary2_rwd
			$sql_seq="SELECT MAX(seq) AS max_seq
								FROM `trn_tdiary2_sl`
									WHERE
										 `corporation_id` ='$this->m_corporation_id' AND
										  `company_id` ='$this->m_company_id' AND
										  `branch_id` ='$this->m_branch_id' AND
										  `computer_no` ='$this->m_computer_no' AND
									  	  `computer_ip` ='$this->m_com_ip' AND
										  `doc_date` ='$this->doc_date'";
			$row_seq=$this->db->fetchAll($sql_seq);
			$this->seq=$row_seq[0]['max_seq']+1;		
			
			if($fix_quantity=='Y'){
				$fix_qty=0;
				$sql_chkqty="SELECT quantity 
									FROM `promo_point3_head` 
									WHERE 
										 `corporation_id` ='$this->m_corporation_id' AND
										  `company_id` ='$this->m_company_id' AND
										  `promo_code`='$promo_code'";
				$row_chkqty=$this->db->fetchAll($sql_chkqty);
				if(count($row_chkqty)>0){
					$fix_qty=intval($row_chkqty[0]['quantity']);
				}
				
				
			$sql_proseq="SELECT SUM(quantity) AS qty,promo_seq
								FROM `trn_tdiary2_sl`
									WHERE
										 `corporation_id` ='$this->m_corporation_id' AND
										  `company_id` ='$this->m_company_id' AND
										  `branch_id` ='$this->m_branch_id' AND
										  `computer_no` ='$this->m_computer_no' AND
									  	  `computer_ip` ='$this->m_com_ip' AND
									  	  `promo_code`='$this->promo_code' AND
										  `doc_date` ='$this->doc_date'";
			$row_proseq=$this->db->fetchAll($sql_proseq);
			if(count($row_proseq)>0){
				if(intval($row_proseq[0]['promo_seq'])<$fix_qty){
					$this->promo_seq=$row_proseq[0]['promo_seq'];
				}
				
			}else{
				$this->promo_seq=1;
			}
				
				
			}//			
			
			try{						
					$proc_status=1;
					$sql_insert="INSERT INTO `trn_tdiary2_sl`
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='$this->doc_tp',
									  `status_no`='$this->status_no',
									  `seq` ='$this->seq',
									  `promo_code` ='$this->promo_code',
									  `promo_seq` ='$this->promo_seq',
									  `promo_st` ='',
									  `promo_tp` ='',
									  `product_id`='$this->product_id',								  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='$this->price',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='',								  
									  `member_discount1`='',
									  `member_discount2`='',								  
									  `net_amt`='$this->net_amt',
									  `product_status`='',
									  `get_point`='',
									  `discount_member`='',
									  `cal_amt`='Y',
									  `tax_type`='$this->tax_type',
									  `computer_no` ='$this->m_computer_no',
									  `computer_ip` ='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert);		
					if($this->stock_st == -1){
						$this->decreaseStock($this->product_id,$this->quantity);
					}
			}catch(Zend_Db_Exception $e){
				$e->getMessage();
				$proc_status=0;
			}
			return $proc_status;
		}//func
		
		function addProductSet($promo_code,$quantity,$status_no,$amount){
			/**
			 * @desc : บันทึกสินค้าสินค้าแบบเป็นชุด
			 * @param String $promo_code :
			 * @return
			 */
			$this->promo_code=$promo_code;	
			$this->quantity=$quantity;
			$this->status_no=$status_no;
			$this->doc_tp=parent::getDocTp($this->status_no);
			
			$sql_doc='SELECT stock_st,doc_no 
							FROM 
								`com_doc_no` 
							WHERE 
								corporation_id="'.$this->m_corporation_id.'" AND 
								company_id="'.$this->m_company_id.'" AND
								branch_id="'.$this->m_branch_id.'" AND
								doc_tp="'.$this->doc_tp.'"';				
			$row_stockst=$this->db->fetchAll($sql_doc);				
			$this->stock_st=$row_stockst[0]['stock_st'];		
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point3_detail");
			$sql_rwddetail="SELECT 
									product_id,amount
								FROM `promo_point3_detail` 
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`promo_code` = '$this->promo_code' AND
									'$this->doc_date' BETWEEN start_date AND end_date";
			$row_rwd=$this->db->fetchAll($sql_rwddetail);
			if(count($row_rwd)>0){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
				$sql_pseq="SELECT MAX(promo_seq) AS promo_seq 
										FROM `trn_tdiary2_sl`
											WHERE
												 `corporation_id`='$this->m_corporation_id' AND
												 `company_id`='$this->m_company_id' AND
												 `doc_date`='$this->doc_date' AND
												 `computer_no` ='$this->m_computer_no' AND
									  	 		 `computer_ip` ='$this->m_com_ip' ";
				$row_pseq=$this->db->fetchCol($sql_pseq);
				$this->promo_seq=$row_pseq[0]+1;
			
				try{						
						$proc_status=1;
						foreach($row_rwd as $dataRwd){
									$arr_product=parent::browsProduct($dataRwd['product_id']);
									if(empty($arr_product)){
										//สินค้าไม่ต้องตัดสต๊อก bill 18 แลกของรางวัล Premier Rewards มีสินค้าใน com_product_master แต่ไม่มีใน com_stock_master
										$this->stock_st=0;
									}else{
										//สินค้าต้องตัดสต๊อก
										$balance=$arr_product[0]['onhand']-$arr_product[0]['allocate'];
										if($balance<$this->quantity){
											$proc_status='2';
										}
									}
									$this->product_name=$arr_product[0]['name_product'];
									$this->product_id=$dataRwd['product_id'];						
									//$this->price=$arr_product[0]['price'];
									$this->price=$amount;
									$this->tax_type=$arr_product[0]['tax_type'];
									$this->amount=$this->price * $this->quantity;
									$this->net_amt=$this->amount;
									$this->doc_time=date("H:i:s");
									$sql_seq="SELECT MAX(seq) AS max_seq 
													FROM `trn_tdiary2_sl` 
														WHERE 
															 `corporation_id` ='$this->m_corporation_id' AND
															  `company_id` ='$this->m_company_id' AND
															  `branch_id` ='$this->m_branch_id' AND
															  `computer_no`='$this->m_computer_no' AND
													  		  `computer_ip`='$this->m_com_ip'";
									$row_seq=$this->db->fetchCol($sql_seq);
									$this->seq=$row_seq[0]+1;
									$sql_insert="INSERT INTO `trn_tdiary2_sl`
													SET 
													  `corporation_id` ='$this->m_corporation_id',
													  `company_id` ='$this->m_company_id',
													  `branch_id` ='$this->m_branch_id',
													  `doc_date` ='$this->doc_date',
													  `doc_time` ='$this->doc_time',
													  `doc_no` ='',
													  `doc_tp` ='$this->doc_tp',
													  `status_no`='$this->status_no',
													  `seq` ='$this->seq',
													  `promo_code` ='$this->promo_code',
													  `promo_seq` ='$this->promo_seq',
													  `promo_st` ='',
													  `promo_tp` ='',
													  `product_id`='$this->product_id',				
													  `name_product`=' $this->product_name',	
													  `stock_st` ='$this->stock_st',								  
													  `price` ='$this->price',
													  `quantity`='$this->quantity',
													  `amount`='$this->amount',
													  `discount`='',								  
													  `member_discount1`='',
													  `member_discount2`='',								  
													  `net_amt`='$this->net_amt',
													  `product_status`='',
													  `get_point`='',
													  `discount_member`='',
													  `cal_amt`='Y',
													  `tax_type`='$this->tax_type',
													  `computer_no`='$this->m_computer_no',
													  `computer_ip`='$this->m_com_ip',	
													  `reg_date`='$this->doc_date',
													  `reg_time`='$this->doc_time',
													  `reg_user`='$this->employee_id'";	
									$stmt_insert=$this->db->query($sql_insert);		
									if($this->stock_st == -1){
										$this->decreaseStock($this->product_id,$this->quantity);
									}
						}//foreach
				}catch(Zend_Db_Exception $e){
					$e->getMessage();
					$proc_status=0;
					break;
				}
			}
			return $proc_status;
		}//func
		
		public function getPageTotal($rp){
			/**
			 * @name getPageTotal
			 * @desc
			 * @param  Integer $rp : row per page
			 * @return Integer $cpage : total of page
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_row = "SELECT count(*) 
							FROM `trn_tdiary2_sl` 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no`='$this->m_computer_no' AND
 								`computer_ip`='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";
			$crow=$this->db->fetchOne($sql_row);
			$cpage=ceil($crow/$rp);
			return $cpage;
		}//func		
		
		function getSumRwdTemp(){
			/**
			 * @name getSumCnTemp
			 * @desc 
			 * @param 
			 * @return array 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_sum="SELECT 
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
							`trn_tdiary2_sl`
						WHERE 
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date'";
			$rowsum=$this->db->fetchAll($sql_sum);
			if(count($rowsum)>0){
				$netvt=floatval($rowsum[0]['sum_net_amt']);
				$sum_discount=$rowsum[0]['sum_amount']-$rowsum[0]['sum_net_amt'];
				$netvt=number_format($netvt,"2",".","");
				$sum_discount=number_format($sum_discount,"2",".","");
				$rowsum[0]['sum_discount']=parent::getSatang($sum_discount);						
				$rowsum[0]['sum_net_amt']=parent::getSatang($netvt);						
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcn',$rowsum[0],'yes');	
				return $json;				
			}
		}//func
		
		function getRwdTemp($page,$qtype,$query,$rp,$sortname,$sortorder){
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
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";			
			$sql_numrows = "SELECT COUNT(*) FROM trn_tdiary2_sl 
										WHERE 
												`corporation_id`='$this->m_corporation_id' AND
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
											promo_seq,
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
										`corporation_id`='$this->m_corporation_id' AND
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
					$discount_tmp=number_format($row['sum_discount'],'2','.',',');
					$rows[] = array(
								"id" => $row['product_id'],
								"absid" => $row['promo_seq'],
								"cell" => array(
											$i,
											$row['promo_code'],
											$row['product_id'],
											$row['name_product'],
											intval($row['quantity']),
											number_format($row['amount'],'2','.',',')										
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
		
		function getRwdTemp22($page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @name getCshTemp
			 * @desc get temp item from target table			
			 * @param Integer $page :
			 * @param String $qtype :
			 * @param String $query :
			 * @param Integer $rp : row per page
			 * @param String $sortname : field to sort
			 * @param String $sortorder : order by field
			 * @return json format string of data
			 * @modify :15102011
			 * */
			
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_sl'); 
			$sql_numrows = "SELECT count(*) FROM `trn_tdiary2_sl` 
										WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
 										`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' ";
			$total = $this->db->fetchOne($sql_numrows);
			$sql_l="SELECT 
							a.id as id,
							a.promo_code as promo_code,
							a.promo_st as promo_st,
							a.promo_seq as promo_seq,
							a.product_id as product_id,
							b.name_product as product_name,
							a.product_status as product_status,
							a.get_point AS get_point,
							a.discount_member AS discount_member,
							a.quantity as quantity,
							b.unit as unit,
							a.price as price,
							b.tax_type as tax_type,
							a.discount as discount,
							a.member_discount1 as member_discount1,
							a.member_discount2 as member_discount2,
							a.amount as amount,
							a.net_amt as net_amt
						FROM 
							`trn_tdiary2_sl` as a LEFT JOIN `com_product_master` as b
							ON(a.product_id=b.product_id)		
						WHERE
							a.corporation_id='$this->m_corporation_id' AND
							a.company_id='$this->m_company_id' AND
							a.branch_id='$this->m_branch_id' AND
							a.computer_no='$this->m_computer_no' AND
 							a.computer_ip='$this->m_com_ip' AND
							a.doc_date='$this->doc_date'					
							$sort $limit";
			$arr_list=$this->db->fetchAll($sql_l);
			
			if(count($arr_list)>0){
				$data['page'] = $page;
				$data['total'] = $total;
				$i=(($page*$rp)-$rp)+1;
				foreach($arr_list as $row){	
					$status_pro_tmp="";//temp 23082011					
					$discount_tmp=$row['discount']+$row['member_discount1']+$row['member_discount2'];
					$discount_tmp=number_format($discount_tmp,'2','.',',');
					$rows[] = array(
								"id" => $row['product_id'],
								"absid" => $row['promo_seq'],
								"cell" => array(
											$i,
											$row['promo_code'],
											$row['product_id'],
											$row['product_name'],
											intval($row['quantity']),
											number_format($row['amount'],'2','.',',')										
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
		
		function delRwd($items){
			/**
			 * @desc : delete item product from trn_tdiary2_sl for bill reward
			 *           : delete by promotion sequence not by item
			 * @param String $items : value of field promo_seq to remove from table trn_tdiary2_sl
			 * @return void
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_sl'); 
			$arr_items=explode("#",$items);
			if(empty($arr_items)) return '-1';
			
			foreach($arr_items as $id_remove){
				//check promo เพื่อ update คะแนนที่ใช้นะ
				$sql_chk="SELECT promo_code,quantity 
							FROM 
								`trn_tdiary2_sl`
							WHERE
								`corporation_id`='$this->m_corporation_id' AND 
								`company_id`='$this->m_company_id' AND 
								`branch_id`='$this->m_branch_id' AND 
								`computer_no`='$this->m_computer_no' AND
 								`computer_ip`='$this->m_com_ip' AND
								`promo_seq`='$id_remove' ";
				$row_chk=$this->db->fetchAll($sql_chk);
				if(count($row_chk)>0){
					$promo_code=$row_chk[0]['promo_code'];
					$sql_promo="SELECT point,quantity 
								FROM `promo_point3_head`
								WHERE
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND 
									promo_code='$promo_code'";
					$row_promo=$this->db->fetchAll($sql_promo);
					if(count($row_promo)>0){
						$repoint=($row_promo[0]['point']*$row_chk[0]['quantity'])/$row_promo[0]['quantity'];
						$repoint=intval($repoint);
					}else{
						$repoint='0';
					}
				}
				$sql_del="DELETE FROM `trn_tdiary2_sl`
								WHERE
									`corporation_id`='$this->m_corporation_id' AND 
									`company_id`='$this->m_company_id' AND 
									`branch_id`='$this->m_branch_id' AND 
									`computer_no`='$this->m_computer_no' AND
 									`computer_ip`='$this->m_com_ip' AND
									`promo_seq`='$id_remove'";
				$this->db->query($sql_del);
				//*16012012 ตอนนี้ยังไม่มีการ update stock เพราะสินค้าไม่มีใน stock กลับมาดูอีกทีนะ
			}
			return $repoint;
		}//func
		
		function initRwdTemp(){
			/**
			 * @desc initial table temp for bill reward
			 * @param
			 * @return void
			 */
			//old trn_tdiary2_rwd
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			parent::TrancateTable('trn_tdiary2_sl');
		}//func
		
		function getMemberAutoComplete($term="",$fieldauto){
			/**
			 * @desc
			 * @param String $term
			 * @param String $fieldauto :field to get auto complete
			 * @return Array of term
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_profile");
			if($term=="") return false;			
			$sql_member="SELECT 
									name,surname
								FROM 
									crm_profile
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND		
									$fieldauto LIKE '%$term%'
								ORDER BY id";	
			$rows=$this->db->fetchAll($sql_member);
			return $rows;
		}//func
		
		function chkCardRegister($member_id,$card_type){
			/**
			 * @desc today for process card lost
			 * @param String $member_id : id of member
			 * @param String $card_type : type of card N is normal ,L is lost
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			$s_check=0;
			$sql_chk="SELECT apply_date
						FROM 
							com_register_new_card
						WHERE
							corporation_id='$this->m_corporation_id' AND 
							company_id='$this->m_company_id' AND 
							branch_id='$this->m_branch_id' AND
							member_id='$member_id' AND 
							card_type='$card_type' AND 
							register_date<>'0000-00-00'";
			$crow=$this->db->fetchAll($sql_chk);
			if(count($crow)>0){
				$s_check=1;
				if($crow[0]['apply_date']!='0000-00-00'){
					$s_check=2;
				}
			}else{
				$s_check=0;
			}
			return $s_check;			
		}//func
		
		function chkFirstBuy($member_id){
			/**
			 * @desc
			 * @param String $member_id
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			$sql_chk="SELECT apply_promo
							FROM crm_card
								WHERE 
										corporation_id='$this->m_corporation_id' AND 
										company_id='$this->m_company_id' AND 
										member_no='$member_id' AND
										apply_shop='$this->m_branch_id' AND
										apply_date='$this->doc_date'";			
			$crow=$this->db->fetchCol($sql_chk);
			if(count($crow)>0){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				//check trn_diary1
				$sql_diary="SELECT COUNT(*) 
								FROM trn_diary1
									WHERE
										status_no='02' AND
										member_id='$member_id' AND
										flag!='C'";
				//echo $sql_diary;
				$crow_diary=$this->db->fetchOne($sql_diary);
				if($crow_diary<1){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
					//check com_application_head
					$application_id=$crow[0];
					$sql_app="SELECT 
									first_sale,first_percent,xpoint,
									play_main_promotion,play_last_promotion,
									first_limited,add_first_percent,start_date1,end_date1,CONCAT(first_percent,'+',add_first_percent) AS percent_discount
								 FROM
								 	com_application_head
								 WHERE
								 	corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND 
									application_id='$application_id' AND
									'$this->doc_date' BETWEEN start_date AND end_date AND
									first_sale='Y'";
					$row_app=$this->db->fetchAll($sql_app);
					if(count($row_app)>0){
						$objUtils=new Model_Utils();
						$json=$objUtils->ArrayToJson('firstbuy',$row_app[0],'yes');				
						return $json;
					}else{
						return '3';
					}
				}else{
					return '2';
				}
			}else{
				return '1';
			}
		}//func
		
		function regNewCard($member_id,$card_type){
			/**
			 * @desc
			 * @param String $member_id
			 * @param Char $card_type			 
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			if($member_id=='') return FALSE;
			if($card_type=='N'){
				//check card lost
				$sql_lost="SELECT COUNT(*)
								FROM com_special_day 
								WHERE  
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									special_day='50' AND 
									'$member_id' BETWEEN start_code AND end_code";
				$clost=$this->db->fetchOne($sql_lost);
				if($clost>0){
					return '5';
				}
			}else if($card_type=='L'){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
				//check card lost
				$sql_lost="SELECT COUNT(*)
							FROM com_special_day 
								WHERE  
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									special_day='50' AND 
									'$member_id' BETWEEN start_code AND end_code";
				$clost=$this->db->fetchOne($sql_lost);
				if($clost<1){
					return '6';
				}
			}
			
			//**check EN13
			$objPos=new Model_PosGlobal();
			$result=$objPos->checkEn13($member_id);
			//echo "----------------->".$result;
			if($result){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
				//check crm_card
				$sql_chkcrm="SELECT COUNT(*) FROM crm_card
								WHERE
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND 
									member_no='$member_id'";
				$res_chkcrm=$this->db->fetchOne($sql_chkcrm);
				if($res_chkcrm<1){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
					//check duplicate
					$sql_dup="SELECT COUNT(*) FROM com_register_new_card
								WHERE
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND 
									branch_id='$this->m_branch_id' AND
									member_id='$member_id'";
					$res_dup=$this->db->fetchOne($sql_dup);
					if($res_dup<1){
						////////////// insert //////////////
						$register_date=date("Y-m-d");
						$register_time=date("H:i:s");
						$apply_date="";
						$apply_time="";
						$data = array( 
										'corporation_id' => $this->m_corporation_id,
										'company_id' => $this->m_company_id,
										'branch_id' => $this->m_branch_id,
										'member_id' => $member_id,
									    'register_date' => $register_date,
									    'register_time' => $register_time,
									    'apply_date' => $apply_date,
									    'apply_time' => $apply_time,
										'card_type'=>$card_type);
						$this->db->insert('com_register_new_card', $data);
						return TRUE;
						////////////// insert //////////////
					}else{
						return "4";
					}
				}else{
					return "3";
				}
			}else{
				return "2";
			}
						
		}//func
		
		function delRegCard($items){
			/**
			 * @desc
			 * @param String $items
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			$arr_items=explode("#",$items);
			if(empty($arr_items)) return FALSE;
			foreach($arr_items as $id_remove){
				$sql_chk="SELECT COUNT(*) FROM `com_register_new_card` 
									WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND 
											branch_id='$this->m_branch_id' AND
											id='$id_remove' AND
											apply_date<>'0000-00-00'";
				$nchk=$this->db->fetchOne($sql_chk);
				if($nchk>0)
					return FALSE;
				$sql_del="DELETE FROM com_register_new_card
							WHERE
								corporation_id='$this->m_corporation_id' AND 
								company_id='$this->m_company_id' AND 
								branch_id='$this->m_branch_id' AND 
								id='$id_remove'";
				$this->db->query($sql_del);
			}
			return TRUE;
		}//func
		
		function getRegCard($page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @desc
			 * @param Integer $page
			 * @param String $qtype :field to search
			 * @param String $query :key word to search
			 * @param Integer $rp :row per page
			 * @param String $sortname :field to sort order
			 * @param String $sortorder : sort by DESC or ASC
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sortSql = "ORDER BY $sortname $sortorder";
			$searchSql = ($qtype != '' && $query != '') ? " AND $qtype = '$query'" : '';
			
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('com','com_register_new_card'); 
			$sql_numrows = "SELECT 
										COUNT(*) 
									FROM 
										`com_register_new_card` 
									WHERE 
										corporation_id='$this->m_corporation_id' AND 
										company_id='$this->m_company_id' AND 
										branch_id='$this->m_branch_id'  $searchSql";
			$total =$this->db->fetchOne($sql_numrows);
			$sql_l="SELECT 
							id,member_id,register_date,apply_date,card_type,remark
					FROM 
						`com_register_new_card`
					WHERE
						corporation_id='$this->m_corporation_id' AND 
						company_id='$this->m_company_id' AND 
						branch_id='$this->m_branch_id' 
						$searchSql $sortSql $limit";
			$arr_list=$this->db->fetchAll($sql_l);
			if(count($arr_list)>0){
				$data['page'] = $page;
				$data['total'] = $total;
				$i=(($page*$rp)-$rp)+1;
				foreach($arr_list as $row){	
					$arr_regdate=explode("-",$row['register_date']);
					$arr_appdate=explode("-",$row['apply_date']);
					$register_date=$arr_regdate[2]."/".$arr_regdate[1]."/".$arr_regdate[0];
					$apply_date=$arr_appdate[2]."/".$arr_appdate[1]."/".$arr_appdate[0];
					if($row['apply_date']!='0000-00-00' && $row['remark']!=''){
						$remark="ชุดสมัคร ".$row['remark'];
					}else{
						$remark="";
					}
					switch($row['card_type']){
						case 'N':$cardtype="บัตรใหม่";break;
						case 'L':$cardtype="บัตรเสีย/บัตรหาย";break;
						default:$cardtype="";break;						
					}
					$rows[] = array(
								"id" => $row['member_id'],
								"absid" => $row['id'],
								"cell" => array(
											$i,
											$row['member_id'],
											$register_date,
											$apply_date,
											$cardtype,
											$remark,
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
		
		function getCurNetAmt($buy_amt_s,$buy_net_s,$refer_member_st){			
			/**
			 * @desc get current day transaction net amount
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$arr_curr=array();
			$sql_curdate="SELECT SUM(amount) as sum_amount,SUM(net_amt) as sum_net_amt 
									FROM trn_diary1 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`member_id`='$refer_member_st' AND										
										`doc_date`='$this->doc_date' AND
										`flag`<>'C'";					
			$row_curdate=$this->db->fetchALL($sql_curdate);
			if(!empty($row_curdate)){
				$buy_amt=$buy_amt_s+$row_curdate[0]['sum_amount'];
				$buy_net=$buy_net_s+$row_curdate[0]['sum_net_amt'];
				$arr_curr[0]['buy_amt']=$buy_amt;
				$arr_curr[0]['buy_net']=$buy_net;				
			}
			return $arr_curr;
		}//func
		
		function comMemberExpire($refer_member_st,$actions){
			/**
			 * @desc เปลี่ยนไปดึงโดยตรงที่ jinet 01062012
			 * @param String $refer_member_st : old id member to reference
			 * @return
			 */
			if($actions=='ONLINE'){
			//Test joke 19032013
			$objCal=new Model_Calpromotion();
			$json_meminfo=$objCal->read_profile($refer_member_st); 
			
			if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
//				$ws = "http://10.100.53.2/wservice/webservices/services/member_data.php?";
//				$type = "json"; //Only Support JSON 
//				$shop=$this->m_branch_id;
//				$cid = $refer_member_st;
//				$act = "detail3";
//				$src = $ws."callback=jsonpCallback&cid=".$cid."&brand=op&dtype=".$type."&shop=".$shop."&act=".$act."&_=1334128422190";
//				$row_member=array();	
//				$o=@file_get_contents($src,0);				
//				if ($o === FALSE || !$o){	
					//******************* OFFLINE PROCESS ****************
					$arr_member=parent::getOfflineProfile($refer_member_st);				
					
					//print_r($arr_member);
					
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_member_expire");
					$sql_expire="SELECT 
											expire_date,buy_amt,	buy_net,member_year,application_id
										FROM 
											com_member_expire
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND 
											member_no='$refer_member_st'";
					$rows_expire=$this->db->fetchAll($sql_expire);
					if(count($rows_expire)>0){
						$arr_member[0]['expire_date']=$rows_expire[0]['expire_date'];
						$arr_member[0]['buy_amt']=$rows_expire[0]['buy_amt'];
						$arr_member[0]['buy_net']=$rows_expire[0]['buy_net'];
						$arr_member[0]['member_year']=$rows_expire[0]['member_year'];
						$arr_member[0]['application_id']=$rows_expire[0]['application_id'];		
						$arr_member[0]['link_status']='OFFLINE';
						echo  json_encode($arr_member[0]);
					}else{
						echo '';
					}
					//******************* OFFLINE PROCESS ****************
				}else{
					//******************* ONLINE PROCESS ****************
//					$o = str_replace("jsonpCallback(","",$o);
//					$o = str_replace(")","",$o);
//					$o = json_decode($o,true);

					$array_meminfo= json_decode($json_meminfo,true);					
					$o=array();
					foreach($array_meminfo as $key=>$val){
						$o[0][$key]=$val;
					}//foreach
				
					if(!empty($o)){
						//INSERT OR UPDATE table com_member_expire
						$member_no=$o[0]['member_no'];
						$expire_date=$o[0]['expire_date'];
						$buy_amt=$o[0]['buy_amt'];
						$buy_net=$o[0]['buy_net'];
						$member_year=$o[0]['age_card'];
						$application_id=$o[0]['appli_code'];						
						//get current data
						$arr_curr=$this->getCurNetAmt($buy_amt,$buy_net,$refer_member_st);						
						if(!empty($arr_curr)){
							$buy_amt=$arr_curr[0]['buy_amt'];
							$buy_net=$arr_curr[0]['buy_net'];
							$o[0]['buy_amt']=$buy_amt;
							$o[0]['buy_net']=$buy_net;
						}			
						
						$sql_chk="SELECT COUNT(*) FROM com_member_expire WHERE member_no='$member_no'";
						$n_chk=$this->db->fetchOne($sql_chk);
						if($n_chk>0){
							//update
							$sql_update="UPDATE
													com_member_expire 
												SET
													`expire_date`='$expire_date',
													`buy_amt`='$buy_amt',
													`buy_net`='$buy_net',
													`member_year`='$member_year',
													`application_id`='$application_id',
													`upd_date`=CURDATE(),
													`upd_time`=CURTIME(),
													`upd_user`='$this->user_id'
												WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`member_no`='$member_no'";
							$this->db->query($sql_update);
						}else{
							//insert
							$sql_add="INSERT INTO com_member_expire 
											SET
												`corporation_id`='$this->m_corporation_id',
												`company_id`='$this->m_company_id',
												`member_no`='$member_no',
												`expire_date`='$expire_date',
												`buy_amt`='$buy_amt',
												`buy_net`='$buy_net',
												`member_year`='$member_year',
												`application_id`='$application_id',
												`reg_date`=CURDATE(),
												`reg_time`=CURTIME(),
												`reg_user`='$this->user_id'";
							$this->db->query($sql_add);
						}
						$o[0]['link_status']='ONLINE';
						return json_encode($o[0]);
					}else{
						return '';
					}		
					
					//******************* ONLINE PROCESS ****************
				}//if
				
			}else{
				//******************* OFFLINE PROCESS ****************
				$arr_member=parent::getOfflineProfile($refer_member_st);				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_member_expire");
				$sql_expire="SELECT 
										expire_date,buy_amt,	buy_net,member_year,application_id
									FROM 
										com_member_expire
									WHERE 
										corporation_id='$this->m_corporation_id' AND 
										company_id='$this->m_company_id' AND 
										member_no='$refer_member_st'";
				$rows_expire=$this->db->fetchAll($sql_expire);
				if(count($rows_expire)>0){
					$arr_member[0]['expire_date']=$rows_expire[0]['expire_date'];
					$arr_member[0]['buy_amt']=$rows_expire[0]['buy_amt'];
					$arr_member[0]['buy_net']=$rows_expire[0]['buy_net'];
					$arr_member[0]['member_year']=$rows_expire[0]['member_year'];
					$arr_member[0]['application_id']=$rows_expire[0]['application_id'];		
					$arr_member[0]['link_status']='OFFLINE';
					echo  json_encode($arr_member[0]);
				}else{
					echo '';
				}
				//******************* OFFLINE PROCESS ****************
				
			}
			
		}//func	
				
		function checkFreePremium($application_id,$product_id,$quantity,$free_premium_amount,$premium_amount_type){
			/**
			 * @desc
			 * @param String $application_id : id of promotion
			 * @param String $product_id : id of product in free premium
			 * @param Integer $quantity : quantity of item
			 * @param Float $free_premium_amount : amount of free premium product
			 * @param Char $premium_amount_type : G is not lover free_premium_amount ,L is not over free_premium_amount
			 */
			
			//check onhand on com_stock_master
			$arr_year=explode("-",$this->doc_date);
			$year=$arr_year[0];
			$month=$arr_year[1];
			$str_result="";
			$this->product_id=parent::getProduct($product_id,$quantity,'');
			if($this->product_id=='3'){
				//product lock
				$str_result="3,'','',$this->product_id";
			}else if($this->product_id=='2'){
				//not found product in com_stock_master
				$str_result="2,'','',$this->product_id";
			}else if($this->product_id=='1'){
				//not found product in com_product_master
				$str_result="1,'','',$this->product_id";
			}else{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_premium");
				$sql_p="SELECT COUNT(*) 
							FROM `com_application_premium`
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								application_id='$application_id' AND
								product_id='$this->product_id' AND
								'$this->doc_date' BETWEEN start_date AND end_date	";
				$n=$this->db->fetchOne($sql_p);
				if($n>0){
					$sql_product="SELECT name_product,price
										FROM `com_product_master`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											product_id='$this->product_id'	";
					$row_product=$this->db->fetchAll($sql_product);
					$str_result="5,$row_product[0][price],$row_product[0]['name_product'],$this->product_id";
				}else{
					//not found on promotion
					$str_result="4,'',''";
				}				
			}
			return $str_result;
		}//func
		
		function setProBalance($application_id,$free_product_amount,$product_amount_type){
			/**
			 *@desc
			 *@param String $application_id : id of promotion
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_a="SELECT 
							id,product_id,price,amount 
						FROM 
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$application_id' AND
							`promo_st`='P' AND
							LEFT(`price`,1)<>'0'	";
			$row_a=$this->db->fetchAll($sql_a);
			$arr_b=array();
			if(count($row_a)>0){
				$chk_amount=0.00;
				$pay=0.00;
				$discount=0.00;
				$id=0;		
				$i=0;		
				foreach($row_a AS $data){
					$chk_amount+=$data['amount'];
					$arr_b[$i]['id']=$data['id'];
					$arr_b[$i]['discount']=$data['discount'];
					$arr_b[$i]['price']='0.00';
					$arr_b[$i]['amount']='0.00';
					$arr_b[$i]['pay']=$data['net_amt'];
					if($chk_amount>$free_product_amount){
						$pay=$chk_amount-$free_product_amount;
						$discount=$data['amount']-$pay;
						$id=$data['id'];
						$arr_b[$i]['id']=$id;
						$arr_b[$i]['price']=$data['price'];
						$arr_b[$i]['amount']=$data['amount'];
						$arr_b[$i]['discount']=$discount;
						$arr_b[$i]['pay']=$pay;
						break;
					}else if($chk_amount==$free_product_amount){
						$pay=$chk_amount-$free_product_amount;
						$pay=0.00;
						$id=$data['id'];
						$arr_b[$i]['id']=$id;
						$arr_b[$i]['price']=0.00;
						$arr_b[$i]['amount']=0.00;
						$arr_b[$i]['discount']=0.00;
						$arr_b[$i-1]['pay']=0.00;	//new	
						$arr_b[$i]['pay']=$pay;
						break;
					}					
					$i++;
				}//foreach
				if($id!=0){
					foreach($arr_b as $data){
						$id=$data['id'];
						$price=$data['price'];
						$discount=$data['discount'];
						$amount=$data['amount'];
						$pay=$data['pay'];
						$sql_update="UPDATE `trn_tdiary2_sl` 
											SET discount='$discount',price='$price',amount='$amount',net_amt='$pay'
											WHERE id='$id'";
						$this->db->query($sql_update);
					}
				}
			}
		}//func
		
		function chkAmtPro($promo_code,$start_baht,$end_baht,$buy_type,$buy_status,$product_id,$quantity){
			/**
			 * @desc
			 * @return
			 */
			$str_chk="";
			$product_id=parent::setBarcodeToProductID($product_id);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
			$sql_product="SELECT price FROM com_product_master 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									product_id='$product_id'";
			$row_pro=$this->db->fetchAll($sql_product);
			$chk_qty=$quantity*$row_pro[0]['price'];
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			if($promo_code=='FREEBIRTH'){
				$sql_chk="SELECT product_id,quantity FROM trn_tdiary2_sl 
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
 									`computer_ip`='$this->m_com_ip' AND
									`doc_date`='$this->doc_date' AND
									`promo_code`='$promo_code'";
				$row_chk=$this->db->fetchAll($sql_chk);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
				$chk_amount=0.00;
				foreach($row_chk as $dataChk){
					$sql_cal="SELECT price 
									FROM com_product_master
									WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											product_id='$dataChk[product_id]'";
					$p1=$this->db->fetchOne($sql_cal);
					$chk_amount+=$p1*$dataChk['quantity'];
				}//foreach
				$chk_amount+=$chk_qty;
				if($chk_amount>$end_baht){
					$str_chk="N#L#$end_baht";
					return	 $str_chk;		
					exit();
				}				
			}else{
				//*WR06022013 for support amount not over 500 BURNPOINT3
				
				$sql_chk="SELECT product_id,quantity FROM trn_tdiary2_sl 
								WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
 									`computer_ip`='$this->m_com_ip' AND
									`doc_date`='$this->doc_date'";
				
				$row_chk=$this->db->fetchAll($sql_chk);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
				$chk_amount=0.00;
				foreach($row_chk as $dataChk){
					$sql_cal="SELECT price 
									FROM com_product_master
									WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											product_id='$dataChk[product_id]'";
					$p1=$this->db->fetchOne($sql_cal);
					$chk_amount+=$p1*$dataChk['quantity'];
				}//foreach
				$chk_amount+=$chk_qty;
				if($chk_amount>$end_baht){
					$str_chk="N#L#$end_baht";
					return	 $str_chk;		
					exit();
				}				
				
			}
			
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_a="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code'";
			$amt=$this->db->fetchOne($sql_a);
			$sum_amt=$chk_qty+$amt;
			if($buy_type=='G'){
				//Goss
				if($buy_status=='L'){
						//ต้องไม่เกิน
						if($sum_amt>$end_baht){
							//flag Y or N#buy_status L or G # new amount
							$str_chk="N#L#$sum_amt";
						}
				}else if($buy_status=='G'){
						//ต้องไม่น้อยกว่า
						if($sum_amt<$end_baht){
							$str_chk="N#G#$sum_amt";
						}
				}
				
			}else if($buy_type=='N'){
				//Net
			}
			return $str_chk;
		}//func
		
		function chkAmtProSMS($promo_code,$product_id,$quantity,$promo_amt,$discount,$type_discount){
			/**
			 * @desc 23012013 บันทึกมาก่อนแล้วค่อยมาอัพเดทส่วนลดทีหลัง
			 * @desc ไม่ได้ส่วนลดสมาชิก
			 * @desc ยังไม่ได้ดูส่วนลดที่เป็นตัวเงิน
			 */
			if($type_discount=='percent'){
				$discount=$discount/100;
			}
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");			
			$sql_maxseq="SELECT seq,amount,net_amt FROM trn_tdiary2_sl 
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
		 							`computer_ip`='$this->m_com_ip' AND
									`doc_date`='$this->doc_date' AND
									`promo_code`='$promo_code' ORDER BY seq DESC LIMIT 0,1";
			$row_maxseq=$this->db->fetchAll($sql_maxseq);
			$max_seq=$row_maxseq[0]['seq'];
			$max_seq_amount=$row_maxseq[0]['amount'];
			
			//ตรวจสอบยอดรวมก่อนเรคอร์ดล่าสุด
			$sql_b="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code' AND seq<>'$max_seq'";
			$sum_check=$this->db->fetchOne($sql_b);
			
			$sql_a="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code'";
			$sum_curr=$this->db->fetchOne($sql_a);		
			
			if($sum_curr>$promo_amt){				
				if($sum_check<$promo_amt){
					//case ยอด amount ปัจจุบันน้อยกว่ายอดที่เช็ค
					$curr_discount=$promo_amt-$sum_check;
					$new_discount=$curr_discount*$discount;
				}else{
					$new_discount=0.00;
				}
				$new_net_amt=$max_seq_amount-$new_discount;
				$sql_upd="UPDATE trn_tdiary2_sl 
									SET net_amt='$new_net_amt',discount='$new_discount'
									WHERE
										seq='$max_seq'";
				$this->db->query($sql_upd);
			}else{				
				$new_discount=$max_seq_amount*$discount;
				$new_net_amt=$max_seq_amount-$new_discount;
				$sql_upd="UPDATE trn_tdiary2_sl 
									SET net_amt='$new_net_amt',discount='$new_discount'
									WHERE
										seq='$max_seq'";
				$this->db->query($sql_upd);
			}
		}//func
		
		function chkAmount($application_id,$product_id,$quantity,$giftset_amount){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_a="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$application_id' AND
							`promo_st`='S'";
			$sum_amount=$this->db->fetchOne($sql_a);
			if(!$sum_amount){
				$sum_amount=0;
			}
			return $sum_amount;
		}//
		
		function getPromoNewMember($application_id){
			/**
			 * @desc สำหรับการสมัครสมาชิกใหม่ ไม่ใช่การต่อบัตร
			 * @param
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
			$arr_promo=array();
			$i=0;			
			$sql_promo="SELECT 
									application_id,									
									description,									
								 	refer_member_st,
								 	gift_set,
								 	amount,
								 	register_free,
								 	redeem_point,
								 	first_sale,
								 	first_percent,
								 	first_limited,
								 	play_main_promotion,
								 	play_last_promotion,
								 	xpoint,
								 	member_year,
								 	refer_member_st,
								 	application_type,
								 	buy_type,
								 	buy_status,
								 	start_baht,
								 	end_baht 
								FROM 
									`com_application_head` 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									application_id='$application_id' AND
									'$this->doc_date' BETWEEN start_date AND end_date
									group by application_id ORDER BY application_id";
				$row_promo=$this->db->fetchAll($sql_promo);
				if(count($row_promo)<1){
					//not found promotion
					$arr_promo[$i]['chk_status']=1;
					$chk_status=1;
				}else{
					foreach($row_promo as $dataPromo){
						    $app_id=$dataPromo['application_id'];
							$sql_list="SELECT MAX(product_no) AS maxno, Max(product_seq) AS maxnoseq
											FROM `com_application_list`
											WHERE application_id='$app_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
							$row_list=$this->db->fetchAll($sql_list);
										
							$arr_promo[$i]['chk_status']=0;
							$arr_promo[$i]['application_id']=$dataPromo['application_id'];
							$arr_promo[$i]['description']=$dataPromo['description'];
							$arr_promo[$i]['gift_set']=$dataPromo['gift_set'];
							$arr_promo[$i]['amount']=$dataPromo['amount'];
							$arr_promo[$i]['register_free']=$dataPromo['register_free'];
							$arr_promo[$i]['application_type']=$dataPromo['application_type'];
							$arr_promo[$i]['refer_member_st']=$dataPromo['refer_member_st'];
							$arr_promo[$i]['redeem_point']=$dataPromo['redeem_point'];
							$arr_promo[$i]['maxno']=$row_list[0]['maxno'];
							$arr_promo[$i]['maxnoseq']=$row_list[0]['maxnoseq'];			
							$i++;
					}
				}
					return $arr_promo;
		}//func		
				
		function getPromoNewCard($refer_member_st=''){
			/**
			 * @desc สำหรับการต่อบัตรใหม่ ปรับ process ตรวจสอบบัตรเก่าก่อนว่ามีคุณสมบัติกับโปรใดบ้าง
			 * @param Date $expire_date :
			 * @param Float $buy_amt :
			 * @param Float $net_amt :
			 * @param Integer $member_year :
			 * @modify 29122012
			 * @return arrPromo
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
			$arr_promo=array();
			$chk_status=0;
			$i=0;
			if($refer_member_st==''){
				//assume promotion OPPN300 สมัครสมาชิกใหม่
								$sql_promo="SELECT 
													application_id,													
													description,													
												 	gift_set,
												 	amount,
												 	register_free,
												 	redeem_point,
												 	play_main_promotion,
												 	play_last_promotion,
												 	application_type,
												 	free_product,
												 	free_product_amount,
												 	product_amount_type,
												 	free_premium,
												 	free_premium_amount,
												 	premium_amount_type,
												 	get_point,
												 	xpoint
												FROM 
													`com_application_head`
												WHERE 
													corporation_id='$this->m_corporation_id' AND
													company_id='$this->m_company_id' AND
													application_type='NEW' AND
													'$this->doc_date' BETWEEN start_date AND end_date
												GROUP BY application_id ORDER BY application_id";
							$row_promo=$this->db->fetchAll($sql_promo);
							if(count($row_promo)<1){
								//not found promotion
								$arr_promo[$i]['chk_status']=2;
								$chk_status=2;
							}else{
								foreach($row_promo as $dataPromo){
										$app_id=$dataPromo['application_id'];
										$sql_list="SELECT MAX(product_no) AS maxno, Max(product_seq) AS maxnoseq
														FROM `com_application_list`
														WHERE application_id='$app_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
										$row_list=$this->db->fetchAll($sql_list);
										
										$arr_promo[$i]['chk_status']=$chk_status;
										$arr_promo[$i]['application_id']=$dataPromo['application_id'];
										$arr_promo[$i]['description']=$dataPromo['description'];
										$arr_promo[$i]['gift_set']=$dataPromo['gift_set'];
										$arr_promo[$i]['amount']=$dataPromo['amount'];
										$arr_promo[$i]['register_free']=$dataPromo['register_free'];
										$arr_promo[$i]['redeem_point']=$dataPromo['redeem_point'];
										$arr_promo[$i]['application_type']=$dataPromo['application_type'];
										$arr_promo[$i]['maxno']=$row_list[0]['maxno'];
										$arr_promo[$i]['maxnoseq']=$row_list[0]['maxnoseq'];
										$arr_promo[$i]['free_product']=$dataPromo['free_product'];
										$arr_promo[$i]['free_product_amount']=$dataPromo['free_product_amount'];
										$arr_promo[$i]['product_amount_type']=$dataPromo['product_amount_type'];
										$arr_promo[$i]['free_premium']=$dataPromo['free_premium'];
										$arr_promo[$i]['free_premium_amount']=$dataPromo['free_premium_amount'];
										$arr_promo[$i]['premium_amount_type']=$dataPromo['premium_amount_type'];
										$arr_promo[$i]['get_point']=$dataPromo['get_point'];
										$arr_promo[$i]['xpoint']=$dataPromo['xpoint'];
										$i++;										
								}//foreach					
							}//end else count row_promo
			}else{
				//other promotion for new card
					$sql_expire="SELECT 
										expire_date,buy_amt,buy_net,member_year
									FROM 
										com_member_expire
									WHERE 
										corporation_id='$this->m_corporation_id' AND 
										company_id='$this->m_company_id' AND 
										member_no='$refer_member_st'";					
					$row_expire=$this->db->fetchAll($sql_expire);
					if(count($row_expire)>0){
							$expire_date=$row_expire[0]['expire_date'];
							$buy_amt=$row_expire[0]['buy_amt'];
							$buy_net=$row_expire[0]['buy_net'];
							$member_year=$row_expire[0]['member_year'];
									$sql_promo="SELECT 
													application_id,													
													description,													
												 	refer_member_st,
												 	gift_set,
												 	amount,
												 	register_free,
												 	redeem_point,
												 	first_sale,
												 	first_percent,
												 	first_limited,
												 	play_main_promotion,
												 	play_last_promotion,
												 	get_point,
												 	xpoint,
												 	member_year,
												 	refer_member_st,
												 	application_type,
												 	buy_type,
												 	buy_status,
												 	start_baht,
												 	end_baht,
												 	free_product,
												 	free_product_amount,
												 	product_amount_type,
												 	free_premium,
												 	free_premium_amount,
												 	premium_amount_type
												FROM 
													`com_application_head` 													
												WHERE 
													corporation_id='$this->m_corporation_id' AND
													company_id='$this->m_company_id' AND
													member_year='$member_year' AND
													'$buy_net' BETWEEN start_baht 	AND end_baht AND
													'$this->doc_date' BETWEEN start_date AND end_date
												GROUP BY application_id ORDER BY application_id";							
							$row_promo=$this->db->fetchAll($sql_promo);
							if(count($row_promo)<1){
								//not found promotion
								$arr_promo[$i]['chk_status']=2;
								$chk_status=2;
							}else{								
								foreach($row_promo as $dataPromo){
										$app_id=$dataPromo['application_id'];
										$sql_list="SELECT MAX( product_no ) AS maxno, Max( product_seq ) AS maxnoseq
														FROM `com_application_list`
														WHERE application_id='$app_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
										$row_list=$this->db->fetchAll($sql_list);
										
										//------------- check condition by $refer_member_st
										if(trim($dataPromo['application_type'])=='ALL'){
											//-------------------- ALL ต่อก่อนหรือต่อหลังก็ได้ ----------------------------------																														
											$arr_expire=explode("-",$expire_date);
											$arr_docdate=explode("-",$this->doc_date);	
											
											//$timeStmpExpire = mktime(0,0,0,$arr_expire[1],$arr_expire[2],$arr_expire[0]);
											$timeStmpExpire = mktime(0,0,0,$arr_expire[1],'01',$arr_expire[0]);//WR07012013
											
											$timeStmpDocDate = mktime(0,0,0,$arr_docdate[1],$arr_docdate[2],$arr_docdate[0]);											
											if($timeStmpExpire>$timeStmpDocDate){
												//expire is over ต่อบัตรก่อนหมดอายุ
												//$mdif=parent::monthDif($this->doc_date,$expire_date);
												$dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($this->doc_date)) . "+1 month");			
												$dateOneMonth=date("Y-m-d",$dateOneMonthAdded);												
												$arr_OneMonth=explode('-',$dateOneMonth);
												$onemonth_lastday=parent::lastday($arr_OneMonth[1],$arr_OneMonth[0]);
												
												$arr_next=explode("-",$onemonth_lastday);
												$timeStmpNextMonth = mktime(0,0,0,$arr_next[1],$arr_next[2],$arr_next[0]);
												if($timeStmpExpire>$timeStmpNextMonth){
													//ต่อก่อนหมดอายุ
													$arr_promo[$i]['chk_status']=4;					
													$chk_status=4;
												}																				
											}else if($timeStmpExpire<$timeStmpDocDate){
												//expire is less ต่อหลังหมดอายุ	
												//$mdif=parent::monthDif($expire_date,$this->doc_date);	
												
												$datePrevOneMonthSub = strtotime(date("Y-m-d", strtotime($this->doc_date)) . "-1 month");			
												$datePrevOneMonth=date("Y-m-d",$datePrevOneMonthSub);													
												
												$arr_prev=explode("-",$datePrevOneMonth);
												//$timeStmpPrevMonth = mktime(0,0,0,$arr_prev[1],$arr_prev[2],$arr_prev[0]);		
												$timeStmpPrevMonth = mktime(0,0,0,$arr_prev[1],'01',$arr_prev[0]);//WR07012013																						
												
												if($timeStmpExpire<$timeStmpPrevMonth){
													//สามารถต่อก่อนเดือนหมดอายุ 1 เดือน หรือภายในเดือนหมดอายุเท่านั้น
													$arr_promo[$i]['chk_status']=5;
													$chk_status=5;
												}											
											}else{
												//บัตรหมดอายุวันนี้พอดี
												//$mdif=0;//current month
											}
											
											$mem_point=parent::getPointOfDay($refer_member_st);
											if($dataPromo['redeem_point']!=0 && $mem_point<$dataPromo['redeem_point']){
												//คะแนนสะสมไม่พอใช้แลก
												$arr_promo[$i]['chk_status']=3;
												$chk_status=3;
											}else if($dataPromo['member_year']!='0' && $member_year!=$dataPromo['member_year']){
												//กรณีคิดปีของอายุบัตร
												$arr_promo[$i]['chk_status']=6;
												$chk_status=6;	
											}
										}else{
											$arr_promo[$i]['chk_status']=8;
											$chk_status=8;
											//-------------------- AFTER comming soon
										}
										//------------- check condition by $refer_member_st
										$arr_promo[$i]['chk_status']=$chk_status;
										$arr_promo[$i]['application_id']=$dataPromo['application_id'];
										$arr_promo[$i]['description']=$dataPromo['description'];
										$arr_promo[$i]['gift_set']=$dataPromo['gift_set'];
										$arr_promo[$i]['amount']=$dataPromo['amount'];
										$arr_promo[$i]['register_free']=$dataPromo['register_free'];
										$arr_promo[$i]['application_type']=$dataPromo['application_type'];
										$arr_promo[$i]['refer_member_st']=$dataPromo['refer_member_st'];
										$arr_promo[$i]['redeem_point']=$dataPromo['redeem_point'];
										$arr_promo[$i]['maxno']=$row_list[0]['maxno'];
										$arr_promo[$i]['maxnoseq']=$row_list[0]['maxnoseq'];
										$arr_promo[$i]['free_product']=$dataPromo['free_product'];
										$arr_promo[$i]['free_product_amount']=$dataPromo['free_product_amount'];
										$arr_promo[$i]['product_amount_type']=$dataPromo['product_amount_type'];
										$arr_promo[$i]['free_premium']=$dataPromo['free_premium'];
										$arr_promo[$i]['free_premium_amount']=$dataPromo['free_premium_amount'];
										$arr_promo[$i]['premium_amount_type']=$dataPromo['premium_amount_type'];
										$arr_promo[$i]['get_point']=$dataPromo['get_point'];
										$arr_promo[$i]['xpoint']=$dataPromo['xpoint'];
										$i++;										
								}//foreach					
							}
						}
			}
			//print_r($arr_promo);
			return $arr_promo;
		}//func		
		
		
		function getCatalog(){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
			$sql_catalog="SELECT 
									a.application_id,MAX(b.product_no) AS maxno,
									a.description,Max( b.product_seq ) AS maxnoseq,
								 	a.refer_member_st,
								 	a.gift_set AS gift_set,
								 	a.amount AS amount,
								 	a.register_free AS register_free,
								 	a.redeem_point AS redeem_point
								FROM 
									`com_application_head` as a inner join com_application_list as b 
									ON(a.application_id=b.application_id)
								WHERE 
									a.corporation_id='$this->m_corporation_id' AND
									a.company_id='$this->m_company_id' AND
									'$this->doc_date' BETWEEN a.start_date AND a.end_date
									group by a.application_id ORDER BY a.application_id";
			$rows=$this->db->fetchAll($sql_catalog);
			return $rows;
		}//func
		
		function getOnhandStock($product_id){
			/**
			 * @desc
			 * @param String $product_id : id of product onhand
			 * @return onhand on table com_stock_master
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			$sql_onhand="SELECT onhand 
								FROM com_stock_master 
								WHERE
									corporation_id='$this->m_coporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									branch_no='$this->m_branch_no' AND
									year='$this->year' AND
									month='$this->month' AND
									product_id='$product_id'";
			$row_onhand=$this->db->fetchCol($sql_onhand);
			return $row_onhand[0];
		}//func
		
		function countProductSeq($application_id,$product_no,$product_seq){
			/**
			 * @desc
			 * @param String $application_id
			 * @param String $product_no
			 * @param String $product_seq
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_list");
			$sql_maxseq="SELECT 
									COUNT(product_sub_seq) AS max_pro_sub_seq
								FROM 
									`com_application_list`
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									application_id = '$application_id' AND 
									product_no='$product_no' AND
									product_seq='$product_seq' AND 
									'$this->doc_date' BETWEEN start_date AND end_date";
			$row_maxseq=$this->db->fetchCol($sql_maxseq);
			return $row_maxseq[0];
		}//func
		
		
		function chkOnhand($application_id,$product_no,$product_seq,$product_sub_seq){
			/**
			 * @desc
			 * @param String $application_id
			 * @param Integer $product_no
			 * @param Integer $product_seq
			 * @param Integer $product_sub_seq
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_product");
			$this->application_id=$application_id;
			$this->product_no=$product_no;
			$this->product_seq=$product_seq;
			$this->product_sub_seq=$product_sub_seq;
			
			$sql_product="SELECT product_id,name_product,quantity
								FROM
									com_application_product
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									application_id='$this->application_id' AND
									product_no='$this->product_no' AND
									product_seq='".$this->product_seq."' AND
									product_sub_seq='".$this->product_sub_seq."' AND
									'$this->doc_date' BETWEEN start_date AND end_date";	
			//echo $sql_product;
			//exit();
			$arr_prod=$this->db->fetchAll($sql_product);
			$str_dp="";
			foreach($arr_prod as $dp){
				$str_dp.="'".$dp['product_id']."',";	
			}
			$str_dp=substr($str_dp,0,-1);
			$st_onhand=0;
			if($str_dp!=''){
				$sql_st_onhand="SELECT 
											SUM(onhand) as sum_onhand
										FROM 
											com_stock_master
										WHERE 
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND
											branch_no='$this->m_branch_no' AND
											year='$this->year' AND
											month='$this->month' AND
											product_id IN($str_dp)";
				$row_st_onhand=$this->db->fetchCol($sql_st_onhand);
				$st_onhand=$row_st_onhand[0];
			}
			return $st_onhand;
		}//func
		
	
		function getAppOnhand($application_id,$product_no,$product_seq,$product_sub_seq){
			/**
			 * @desc
			 * @param String $application_id :
			 * @param Integer $product_no :
			 * @param Integer $product_seq :
			 * @param Integer $product_sub_sub :
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_list");
			$sql_onhand="SELECT 
									onhand,price 
								FROM 
									com_application_list 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									application_id='$application_id' AND
									product_no='$product_no' AND
									product_seq='$product_seq' AND
									product_sub_seq='$product_sub_seq' AND 
							'$this->doc_date' BETWEEN start_date AND end_date";
			//echo $sql_onhand;
			$row_onhand=$this->db->fetchAll($sql_onhand);			
			return $row_onhand;
		}//func
		
		function getProductList($application_id,$product_no,$product_seq,$product_sub_seq){
			/**
			 * @desc ยังติดปัญหา $row_list ว่าง
			 * @param String $application_id
			 * @param Integer $product_no
			 * @param Integer $product_seq
			 * @param Integer $product_sub_sub 
			 * @return
			 */
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_product");	
			$sql_list="SELECT 
								product_id,name_product,quantity
						 FROM
								com_application_product
						 WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								application_id='$application_id' AND
								product_no='$product_no' AND
								product_seq='".$product_seq."' AND
								product_sub_seq='".$product_sub_seq."' AND
								'$this->doc_date' BETWEEN start_date AND end_date
						ORDER BY name_product,product_id ASC";			
			///echo $sql_list;
			$row_list=$this->db->fetchAll($sql_list);			
			if(count($row_list)>0){
				$i=0;
				foreach($row_list as $data){
					$arr_productlist= $this->getAppOnhand($application_id,$product_no,$product_seq,$product_sub_seq);//get from com_application_list
					$row_list[$i]['price']=$arr_productlist[0]['price'];
					$i++;
				}
			}
			return $row_list;
		}//func
		
		function showSelProduct($application_id,$product_no,$product_seq,$product_sub_seq,$pn,$ps,$row_list){
			/**
			 * @desc
			 * @param String $application_id :
			 * @param Integer $product_no :
			 * @param Integer $product_seq :
			 * @param Integer $product_sub_sub :
			 * @param Integer $pn :
			 * @param Integer $ps :
			 * @param Integer $row_list :
			 * @return 
			 */
			$i=0;
			$arr_data=array();			
			foreach($row_list as $row ){
					$row_stock=parent::browsProduct($row['product_id']);					
					if($row_stock[0]['onhand']>0){
						$arr_data[$i]['application_id']=$application_id;
						$arr_data[$i]['product_no']=$product_no;
						$arr_data[$i]['product_seq']=$product_seq;
						$arr_data[$i]['product_sub_seq']=$product_sub_seq;
						$arr_data[$i]['price']=$row['price'];//price จาก promotion ไม่ใช่จากทะเบียน
						$arr_data[$i]['product_id']=$row['product_id'];
						$arr_data[$i]['name_product']=$row['name_product'];
						$arr_data[$i]['quantity']=$row['quantity'];
						$arr_data[$i]['pn']=$pn;
						$arr_data[$i]['ps']=$ps;
						$i++;				
					}			
			}//end foreach	
			return $arr_data;
		}//func
		
		function showSelProductEmpty($application_id,$product_no,$product_seq,$product_sub_seq,$pn,$ps,$row_list){
			/**
			 * @desc ปัญหา กรณีสินค้าหมด stock ยังไม่สามารถแสดงรหัสสินค้าด้วยได้
			 * @param String $application_id :
			 * @param Integer $product_no :
			 * @param Integer $product_seq :
			 * @param Integer $product_sub_sub :
			 * @param Integer $pn :
			 * @param Integer $ps :
			 * @param Integer $row_list :
			 * @return 			 
			 * @modify 15112012
			 */			
			$str_productlist="";
			foreach($row_list as $data){
				$str_productlist.=$data['product_id'].",";
			}
			$str_productlist=substr($str_productlist,0,-1);
			$arr_show=array();									
			$arr_show[0]['application_id']=$application_id;
			$arr_show[0]['product_no']=$product_no;
			$arr_show[0]['product_seq']=$product_seq;
			$arr_show[0]['product_sub_seq']=$product_sub_seq;
			$arr_show[0]['price']='';
			$arr_show[0]['product_id']='';
			$arr_show[0]['name_product']='';
			$arr_show[0]['quantity']='';
			$arr_show[0]['pn']=$pn;
			$arr_show[0]['ps']=$ps;
			$arr_show[0]['product_list']=$str_productlist;	
			return $arr_show;			
		}//func
		
		function getCatListTest($application_id,$product_no,$product_seq,$product_sub_seq,$pn,$ps){
			/**
			 * @desc
			 * @param application_id
			 * @param product_no
			 * @param product_seq
			 * @param product_sub_seq
			 * @param pn check count n
			 * @param ps check count seq
			 * @return
			 */
			$arr_show=array();
			$this->application_id=$application_id;
			$this->product_no=$product_no;
			$this->product_seq=$product_seq;
			$this->product_sub_seq=$product_sub_seq;
			if($this->product_no==0){
				$this->product_no++;
			}
			//นับจำนวน product_no(ลำดับจำนวน loop ของ application_id) ของรหัสชุดสมัคร
			$n=$this->countProductNo($this->application_id,$this->product_no);			
			if($n>1){
				if($pn<$n){
						if($this->product_sub_seq==0){
							$this->product_seq++;
						}	
						$s=$this->countProductSeq($this->application_id,$this->product_no,$this->product_seq);
						if($s>1){
							$this->product_sub_seq++;
							$ps++;
							if($ps<=$s){
								$pn++;
								############################## show #######################
								$arr_onhand=$this->getAppOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								$st_onhand=$this->chkOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								if($st_onhand>$arr_onhand[0]['onhand']){
									$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
									if($ps==$s){
										$this->product_no++;
										$this->product_seq=0;
										$this->product_sub_seq=0;
										$pn=0;
										$ps=0;
									}
									$arr_show=$this->showSelProduct($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
								}else{
									$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);								
									//echo "($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list)";
									$arr_show=$this->showSelProductEmpty($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
									//$this->getCatListTest($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
								}
								############################## show #######################
							}else{
								$this->product_no++;
								$this->product_seq++;
								$this->product_sub_seq++;
								$pn=0;
								$ps=0;
								$this->getCatListTest($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
							}
						}else{
							$pn++;
							############################## show #######################
							$arr_onhand=$this->getAppOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
							$st_onhand=$this->chkOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
							if($st_onhand>$arr_onhand[0]['onhand']){
								$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								$this->product_no++;
								$this->product_seq=0;
								$this->product_sub_seq=0;
								$pn=0;
								$ps=0;
								$arr_show=$this->showSelProduct($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
							}else{
								$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								//echo "($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list)";								
								$arr_show=$this->showSelProductEmpty($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
								//$this->getCatListTest($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);								
							}
							############################## show #######################
						}//end if			
				}else{
					//echo "<br>NEXT PRODUCT NO<br>";
					$this->product_no++;
					$this->product_seq=0;
					$this->product_sub_seq=0;
					$pn=0;
					$ps=0;
					$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
				}//end if
			}else{
				if($n==1){
					$arr_onhand=$this->getAppOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
					$st_onhand=$this->chkOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
					if($st_onhand>$arr_onhand[0]['onhand']){
						$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
						$this->product_no++;
						//echo "<hr>############ CASE ONE RECORD ($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list) <hr>";
						$arr_show=$this->showSelProduct($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
					}else{
						$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);						
						//echo "<hr>############ STOCK ST LETTER ONHAND<hr>";
						$this->product_no++;
						$this->product_seq=0;
						$this->product_sub_seq=0;
						$pn=0;
						$ps=0;
						//$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
						$arr_show=$this->showSelProductEmpty($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
					}
				}//end if
			}//end if
			return $arr_show;
		}//func
		
		
		function getCatList($application_id,$product_no,$product_seq,$product_sub_seq,$pn,$ps){
			/**
			 * @desc
			 * @param application_id
			 * @param product_no
			 * @param product_seq
			 * @param product_sub_seq
			 * @param pn check count n
			 * @param ps check count seq
			 * @return
			 */
			$arr_show=array();
			$this->application_id=$application_id;
			$this->product_no=$product_no;
			$this->product_seq=$product_seq;
			$this->product_sub_seq=$product_sub_seq;
			if($this->product_no==0){
				$this->product_no++;
			}
			$n=$this->countProductNo($this->application_id,$this->product_no);
			if($n>1){
				if($pn<$n){
						if($this->product_sub_seq==0){
							$this->product_seq++;
						}	
						$s=$this->countProductSeq($this->application_id,$this->product_no,$this->product_seq);
						if($s>1){
							$this->product_sub_seq++;
							$ps++;
							if($ps<=$s){
								$pn++;
								############################## show #######################
								$arr_onhand=$this->getAppOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								$st_onhand=$this->chkOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								if($st_onhand>$arr_onhand[0]['onhand']){
									$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
									if($ps==$s){
										$this->product_no++;
										$this->product_seq=0;
										$this->product_sub_seq=0;
										$pn=0;
										$ps=0;
									}
									$arr_show=$this->showSelProduct($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
								}else{
									//$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
									$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
									$arr_show=$this->showSelProductEmpty($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);				
								}
								############################## show #######################
							}else{
								$this->product_no++;
								$this->product_seq++;
								$this->product_sub_seq++;
								$pn=0;
								$ps=0;
								$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
							}
						}else{
							$pn++;
							############################## show #######################
							$arr_onhand=$this->getAppOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
							$st_onhand=$this->chkOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
							if($st_onhand>$arr_onhand[0]['onhand']){
								$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								$arr_show=$this->showSelProduct($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
								
							}else{
								$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
								$arr_show=$this->showSelProductEmpty($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
							}
							/*
							else{
								echo "<br>STOCK ST <= STOCK ONHAND <br>";
								echo "******************************<br>";
								echo "product_no=>".$this->product_no;
								echo "<br>product_seq=>".$this->product_seq;
								echo "<br>product_sub_seq=>".$this->product_sub_seq;
								echo "<br>ps=>$ps";
								echo "<br>s=>$s";
								echo "<br>******************************<br><br>";
								$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);								
								
							}
							*/
							############################## show #######################
						}//end if			
				}else{
					//echo "<br>NEXT PRODUCT NO<br>";
					$this->product_no++;
					$this->product_seq=0;
					$this->product_sub_seq=0;
					$pn=0;
					$ps=0;
					$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
				}//end if
			}else{
				if($n==1){
					$arr_onhand=$this->getAppOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
					$st_onhand=$this->chkOnhand($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
					
					if($st_onhand>$arr_onhand[0]['onhand']){
						//echo "<hr>*******CASE ONE RECORD<hr>";
						$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
						$this->product_no++;
						$arr_show=$this->showSelProduct($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
						
					}else{
						$row_list=$this->getProductList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq);
						//echo "<hr>*******STOCK ST LETTER ONHAND<hr>";
						$this->product_no++;
						$this->product_seq=0;
						$this->product_sub_seq=0;
						$pn=0;
						$ps=0;
						//$this->getCatList($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps);
						$arr_show=$this->showSelProductEmpty($this->application_id,$this->product_no,$this->product_seq,$this->product_sub_seq,$pn,$ps,$row_list);
					}
					
				}//end if
			}//end if
			
			return $arr_show;
		
		}//func
		
		function countProductNo($application_id='0',$product_no='0'){
			/**
			 * @desc
			 * @param String $application_id
			 * @param Integer $product_no
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_list");
			if($application_id=='0') return false;
			$sql_maxseq="SELECT COUNT(product_seq) AS cproduct_seq
							FROM `com_application_list`
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								application_id = '$application_id' AND 
								product_no='$product_no' AND
								'$this->doc_date' BETWEEN start_date AND end_date";			
			$row_maxseq=$this->db->fetchAll($sql_maxseq);
			return $row_maxseq[0]['cproduct_seq'];
		}//func
		
		function getMaxProductSeq($application_id='0',$product_no='0'){
			/**
			 * @desc
			 * @param String $application_id
			 * @param Integer $product_no
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_list");
			if($application_id=='0') return false;
			$sql_maxseq="SELECT MAX(product_seq) AS maxseq
							FROM `com_application_list`
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								application_id ='$application_id' AND 
								product_no='$product_no' AND 
							'$this->doc_date' BETWEEN start_date AND end_date";
			$row_maxseq=$this->db->fetchAll($sql_maxseq);
			return $row_maxseq[0]['maxseq'];
		}//func
			
		function maxSeq($application_id=''){
			/**
			 * @desc
			 * @param String $application_id			
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_list");
			if($application_id=='') return false;
			$sql_maxseq="SELECT MAX( product_no )
							FROM `com_application_list`
							WHERE application_id = '$application_id' AND 
							'$this->doc_date' BETWEEN start_date AND end_date";			
			$row_maxseq=$this->db->fetchCol($sql_maxseq);
			return $row_maxseq[0];
		}//func
		
		function getExpireDate(){
			/**
			 * @desc
			 * @return 
			 */
			$today=date("Y-m-d");
			$month=date("m");
			$expire_year=date('Y',strtotime('+2 year'));
		    $res_expiredate = strtotime('-1 second', strtotime('+1 month',strtotime("{$expire_year}-{$month}-01")));
		    $this->expire_date=date('Y-m-d', $res_expiredate);
		    return $this->expire_date;
		}//func
		
		function checkMemberExist($member_no){
			/**
			 * @desc for check new member
			 * @param String $member_no
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			$sql_row = "SELECT count(*) FROM `crm_card` WHERE member_no='$member_no' AND `status`<>'1'";
			$crow=$this->db->fetchOne($sql_row);
			if($crow>0){
				return 1;
			}else{
				$sql_chk="SELECT COUNT(*) 
								FROM com_register_new_card 
									WHERE 
										corporation_id='$this->m_corporation_id' AND 
										company_id='$this->m_company_id' AND 
										branch_id='$this->m_branch_id' AND
										member_id='$member_no' AND 
									    card_type='N' AND
									    apply_date='0000-00-00' AND 
									    apply_time='00:00:00'";
				$crow=$this->db->fetchOne($sql_chk);
				if($crow>0){
					$card_expire=$this->getExpireDate();
					return $card_expire;
				}else{
					return 2;
				}
			}
		}//func
		
		function setCshTemp($promo_code,$promo_st,$employee_id,$member_no,$product_id,$quantity,
							$price,$doc_tp,$status_no){
			/**
			 * @name setCshTemp
			 * @desc store data temp before insert trn_diary1,2
			 * @param String $application_id 
			 * @param String $employee_id 
			 * @param String $member_no 
			 * @param String $product_id 
			 * @param Integer $quantity 
			 * @param String $doc_tp
			 * @param Char $status_no 			
			 * @return  
			 */
			$this->promo_code=$promo_code;
			$this->promo_st=$promo_st;
			$this->product_id=$product_id;
			$this->employee_id=$employee_id;
			$this->member_no=$member_no;
			$this->quantity=$quantity;		
			$this->price=$price;	
			$this->doc_tp=$doc_tp;
			$this->status_no=$status_no;
			$arr_product=$this->browsProduct($this->product_id);
			$proc_status=0;
			if(!empty($arr_product)){
				$balance=$arr_product[0]['onhand']-$arr_product[0]['allocate'];
				if($balance<$this->quantity){
					$proc_status='2';
				}else{
					$this->product_name=$arr_product[0]['name_product'];	
					$this->unit=$arr_product[0]['unit'];
					$this->price=$price;
					$this->tax_type=$arr_product[0]['tax_type'];
					$this->amount=$this->price * $this->quantity;
					$this->net_amt=$this->quantity*$this->price;
					if($this->promo_st=='F'){
						//for free premium
						$this->price=0.00;
						$this->amount=0.00;
						$this->net_amt=0.00;
					}
					//----------------------------------------------------
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
					//detail table trn_tdiary2_sl
					$sql_doc='SELECT 
									stock_st
								FROM 
									`com_doc_no` 
								WHERE 
									corporation_id="'.$this->m_corporation_id.'" AND 
									company_id="'.$this->m_company_id.'" AND
									branch_id="'.$this->m_branch_id.'" AND
									doc_tp="'.$this->doc_tp.'"';
					$row_stock_st=$this->db->fetchCol($sql_doc);
					$this->stock_st=$row_stock_st[0];
					$this->doc_time=date("H:i:s");
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
					//Check Bill 01 30012013 ป้องกันการบันทึกสินค้าซ้ำ
					$sql_chk="SELECT COUNT(*) FROM `trn_tdiary2_sl` WHERE `status_no`='01' and `promo_code` ='$this->promo_code' and  `product_id`='$this->product_id' ";
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk>0){
						return 1;
						exit();
					}
					$sql_seq="SELECT MAX(seq) AS max_seq 
										FROM trn_tdiary2_sl 
										WHERE
											 `corporation_id` ='$this->m_corporation_id' AND
											  `company_id` ='$this->m_company_id' AND
											  `branch_id` ='$this->m_branch_id' AND
											  `computer_no`='$this->m_computer_no' AND
 											  `computer_ip`='$this->m_com_ip' AND
											  `doc_date` ='$this->doc_date'";	
					$row_seq=$this->db->fetchCol($sql_seq);
					$this->seq=$row_seq[0]+1;
					$this->promo_seq=$this->seq;
					
					$sql_insert="INSERT INTO trn_tdiary2_sl
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='$doc_tp',
									  `status_no`='$status_no',
									  `seq` ='$this->seq',
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
									  `discount`='',								  
									  `member_discount1`='',
									  `member_discount2`='',								  
									  `net_amt`='$this->net_amt',
									  `product_status`='',
									  `get_point`='',
									  `discount_member`='',
									  `cal_amt`='Y',
									  `tax_type`='$this->tax_type',
									  `computer_no`='$this->m_computer_no',
									  `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->exec($sql_insert);		
					$this->decreaseStock($this->product_id,$this->quantity);
					$proc_status=1;
				}
			}
			return $proc_status;
		}//func
		
		function getAmount(){
			/**
			 * @desc current for catalog member
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_amt="SELECT SUM(amount) 
						FROM `trn_tdiary2_sl` 
							WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND  
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";
			$row_amt=$this->db->fetchCol($sql_amt);
			return $row_amt[0];
		}//func
		
		function paymentActPoint($member_no,$promo_code,$redeem_point,$amount,$cashier_id,$name,$address){
			/**
			 * @desc สมาชิกแลกคะแนนกิจกรรม
			 * @param
			 * @return
			 */
			if(intval($amount)==0){
				$this->status_no='50';
				$this->doc_tp='RD';
			}else{
				$this->status_no='09';
				$this->doc_tp='SL';
			}
			$point1=0;
			$point2=0;
			$redeem_point=-1*$redeem_point;
			$total_point=$point1+$point2+$redeem_point;
			$this->quantity=1;
			$this->amount=$amount;
			$this->net_amt=$amount;
			$this->cashier_id=$cashier_id;
			$this->promo_code=$promo_code;
			$address1=mb_substr($address,0,50,'utf-8');
			$address2=mb_substr($address,50,100,'utf-8');
			
			//create doc_no for diary
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->doc_time=date("H:i:s");
			$status_res=0;
			$this->db->beginTransaction();
			try{
				$sql_add2h="INSERT INTO trn_diary1
								SET
									`corporation_id`='$this->m_corporation_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`doc_date`='$this->doc_date',
									`doc_time`='$this->doc_time',
									`doc_no`='$this->m_doc_no',
									`doc_tp`='$this->doc_tp',
									`status_no`='$this->status_no',
									`member_id`='$member_no',
									`member_percent`='',
									`refer_member_id`='',
									`refer_doc_no`='$this->promo_code',
									`quantity`='$this->quantity',
									`amount`='$this->amount',
									`discount`='',
									`member_discount1`='',
									`member_discount2`='',
									`co_promo_discount`='',
									`coupon_discount`='',
									`special_discount`='',
									`other_discount`='',
									`net_amt`='$this->net_amt',
									`ex_vat_amt`='',
									`ex_vat_net`='',
									`vat`='',
									`point1`='',
									`point2`='',
									`redeem_point`='$redeem_point',
									`total_point`='$total_point',
									`paid`='',
									`pay_cash`='',
									`pay_credit`='',
									`change`='',
									`coupon_code`='',
									`pay_cash_coupon`='',
									`credit_no`='',
									`credit_tp`='',
									`bank_tp`='',
									`application_id`='',
									`new_member_st`='',
									`computer_no`='$this->m_computer_no',
									`pos_id`='$this->m_pos_id',									
									`user_id`='$this->user_id',	
									`cashier_id`='$this->cashier_id',
									`saleman_id`='$this->cashier_id',								
									`doc_remark`='',
									`name`='$name',
									`address1`='$address1',
									`address2`='$address2',
									`address3`='',
									`reg_date`='$this->doc_date',
								  	`reg_time`='$this->doc_time',
								    `reg_user`='$this->cashier_id'";		
				$this->db->query($sql_add2h);	
				$this->db->commit();
				$status_res=1;
			}catch(Zend_Db_Exception $e){
				$this->msg_error=$e->getMessage();
				$this->db->rollBack();				
			}
			if($status_res==1){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
				//update table doc_no
				$arr_temp=explode("-",$this->m_doc_no);
				$this->doc_no_update=intval($arr_temp[2])+1;
				$sql_inc_docno="UPDATE 
									com_doc_no 
								SET 
									doc_no='$this->doc_no_update',
									upd_date='$this->doc_date',
			   						upd_time='$this->doc_time',
			   						upd_user='$this->employee_id'
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND 	
									branch_id='$this->m_branch_id' AND 
									branch_no='$this->m_branch_no' AND
									doc_tp='$this->doc_tp'";
				$this->db->query($sql_inc_docno);
				//add point member to com_mem_pt
				$sql_addpoint="INSERT INTO 
										com_mem_pt
									SET
										doc_no='$this->m_doc_no',
										doc_dt='$this->doc_date',
										flag='',
										member='$member_no',
										point='$total_point',
										amount='$this->amount',
										net_amt='$this->net_amt',
										status=''";
				$this->db->query($sql_addpoint);
				$strResult= "1#".$this->m_doc_no."#".$this->doc_tp."#".$this->m_thermal_printer;
			}else{
				$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer;
			}
			return $strResult;
		}//func
		
		/**
		 * @desc
		 * @return
		 * @lastmodify
		 */
		function payment(
							$user_id,
							$cashier_id,
							$saleman_id,
							$doc_tp,
							$status_no,
							$member_no,
							$member_percent,
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
							$application_id,
							$refer_member_id,
							$expire_date,
							$point_receive,
							$point_used,
							$point_net,
							$card_status,
							$get_point,
							$xpoint){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");					
			//start type paid
			$pay_cash=floatVal($pay_cash);
			$pay_credit=floatVal($pay_credit);
			$pay_cash_coupon=floatVal($pay_cash_coupon);			
			if(!empty($pay_cash) && !empty($pay_credit) && !empty($pay_cash_coupon)){
				$paid="9993";
			}else if(!empty($pay_credit) && !empty($pay_cash_coupon)){
				$paid="9992";
			}else if(!empty($pay_cash) && !empty($pay_credit)){
				$paid="9990";
			}else if(!empty($pay_cash) && !empty($pay_cash_coupon)){
				$paid="9991";
				$credit_tp="";
				$bank_tp="";
			}else if(!empty($pay_cash)){
				$paid="0000";
				$credit_tp="";
				$bank_tp="";
			}else if(!empty($pay_cash_coupon)){
				$paid="9999";
				$credit_tp="";
				$bank_tp="";
			}
			//end type paid
			$this->doc_tp=$doc_tp;	
			//check transaction data
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
			if($n_chk_trns<1){					
				$strResult= "0#ไม่พบรายการขาย ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
				return $strResult;
				exit();
			}	
			//check no member
			if($status_no=='01' && $member_no==''){
				$strResult= "0#ไม่พบรหัสบัตรสมาชิก กรุณาตรวจสอบอีกครั้ง#".$this->doc_tp."#Y";
				return $strResult;
				exit();
			}
			//check exist data			
			$sql_chk_exist="SELECT COUNT(*)
									FROM 
										trn_diary1
									WHERE
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`status_no`='$status_no' AND
 										`member_id`='$member_no' AND 
 										`flag`<>'C' AND 
 										`doc_date`='$this->doc_date'";
			$n_chk_exist=$this->db->fetchOne($sql_chk_exist);
			if($n_chk_exist>0){					
				$strResult= "0#ไม่สามารถบันทึกข้อมูลซ้ำได้#".$this->doc_tp."#Y";
				return $strResult;
				exit();
			}	
			
			//create header temp for trn_tdiary1_sl
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
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_company_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date'";
			$row_l2h=$this->db->fetchAll($sql_l2h);			
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
		 	$this->net_amt=$this->amount-($this->discount+
		 												$this->member_discount1+
		 												$this->member_discount2+
		 												$this->co_promo_discount+
		 												$this->coupon_discount+
		 												$this->special_discount+
		 												$this->other_discount);				
			//check application_head->register_free or not
			$chk_regfree=parent::chkRegFree($application_id);
			if((intval($this->amount)==0 && $chk_regfree=='Y') || $card_status=='C2'){
				$this->doc_tp="DN";//กรณีบัตรชำรุด/เสีย ปรับเป็นบิล DN
				$this->amount=0.00;
				$net_amount=0.00;
			}			
			if(intval($point_used)!=0){
				$redeem_point=$point_receive+(-1*$point_used);
			}
			$point1=0;
			$point2=0;
			$total_point=0;
			if($member_no!='' && $get_point=='Y'){
				$point1=parent::getPoint1($this->net_amt);
				$point2=parent::getPoint2($this->net_amt);
			}			
			$point1*=$xpoint;
			$total_point=$point1+$point2+$redeem_point;		
			//check stock_st
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
			$sql_doc='SELECT 
							stock_st 
						FROM 
							`com_doc_no` 
						WHERE 
							corporation_id="'.$this->m_corporation_id.'" AND 
							company_id="'.$this->m_company_id.'" AND
							branch_id="'.$this->m_branch_id.'" AND
							doc_tp="'.$this->doc_tp.'"';				
			$row_stockst=$this->db->fetchCol($sql_doc);
			$this->stock_st=$row_stockst[0];
			
			//create doc_no for diary
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);
			$this->doc_time=date("H:i:s");		
			//cal tax vat			
			$this->vat=parent::calVat($this->net_amt);//*WR10102012
			$this->vat=round($this->vat,2);
			//$this->vat=parent::getSatang($this->vat,'normal');	
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary1_sl");
			$this->db->beginTransaction();
			$this->chk_trans=TRUE;
			try{
				$sql_add_l2h="INSERT INTO trn_tdiary1_sl
									SET
										`corporation_id`='$this->m_corporation_id',
										`company_id`='$this->m_company_id',
										`branch_id`='$this->m_branch_id',
										`doc_date`='$this->doc_date',
										`doc_time`='$this->doc_time',
										`doc_no`='$this->m_doc_no',
										`doc_tp`='$this->doc_tp',
										`status_no`='$status_no',
										`member_id`='$member_no',
										`member_percent`='$member_percent',
										`refer_member_id`='$refer_member_id',
										`refer_doc_no`='',
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
										`application_id`='$application_id',
										`new_member_st`='Y',
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
										`reg_date`=CURDATE(),
									  	`reg_time`=CURTIME(),
									    `reg_user`='$saleman_id'";		
				$this->db->query($sql_add_l2h);				
				//update doc_no in trn_tdiary2_sl
				$sql_upd_trn_tdiary2_sl="UPDATE trn_tdiary2_sl
											SET
												`doc_tp`='$this->doc_tp',
												`doc_no`='$this->m_doc_no',
												`reg_date`=CURDATE(),
								  				`reg_time`=CURTIME()
											 WHERE							 	
											 	`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`computer_no`='$this->m_computer_no' AND
 												`computer_ip`='$this->m_com_ip' AND
												`doc_date`='$this->doc_date'";
				$this->db->query($sql_upd_trn_tdiary2_sl);				
				//load data from trn_tdiary2_sl to trn_diary2
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
										  `reg_date`,
										  `reg_time`,
										  `reg_user`
									FROM
										`trn_tdiary2_sl`
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND 
										doc_no='$this->m_doc_no'";	
					$this->db->query($sql_diary2);
					//load data from pdiary1 to tdiary1
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
												`application_id`,
												`new_member_st`,
												`computer_no`,
												`pos_id`,
												`user_id`,
												`cashier_id`,
												`saleman_id`,
												`name`,
												`address1`,
												`address2`,
												`address3`,
												`doc_remark`,
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
												`member_id`,
												`member_percent`,
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
												`application_id`,
												`new_member_st`,
												`computer_no`,
												`pos_id`,
												`user_id`,
												`cashier_id`,
												`saleman_id`,
												`name`,
												`address1`,
												`address2`,
												`address3`,
												`doc_remark`,
												`reg_date`,
												`reg_time`,
												`reg_user`
										FROM 
											`trn_tdiary1_sl`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND 
											doc_no='$this->m_doc_no'";
				$this->db->query($sql_diary1);	
				//update table doc_no
				$arr_temp=explode("-",$this->m_doc_no);
				$this->doc_no_update=intval($arr_temp[2])+1;//นับชุดสุดท้าย
				//$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
				$sql_inc_docno="UPDATE 
											com_doc_no 
										SET 
											doc_no='$this->doc_no_update',
											upd_date=CURDATE(),
					   						upd_time=CURTIME(),
					   						upd_user='$this->employee_id'
										WHERE 
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND 	
											branch_id='$this->m_branch_id' AND 
											branch_no='$this->m_branch_no' AND
											doc_tp='$this->doc_tp'";
				$this->db->query($sql_inc_docno);	
				$this->db->commit();
			}catch(Zend_Db_Exception $e){				
				$arr_error=explode(":",$e->getMessage());
				//$this->msg_error=$e->getCode()."\n".$arr_error[2];
				$this->msg_error=$arr_error[2];
				$this->db->rollBack();
				$this->chk_trans=FALSE;
			}
			
			$refer_member_expire_date=date("Y-m-d", strtotime($this->doc_date." -1 Days"));			
			if($this->chk_trans){
				//append point to com_mem_pt
				if($member_no!=''){
					$sql_addpoint="INSERT INTO 
											com_mem_pt
										SET
											doc_no='$this->m_doc_no',
											doc_dt='$this->doc_date',
											flag='',
											member='$member_no',
											point='$total_point',
											amount='$this->amount',
											net_amt='$this->net_amt',
											status=''";
					$this->db->query($sql_addpoint);
				}
				
				//update crm_card
				if($this->status_no=='05'){
					//บัตรเสีย บัตรหาย
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
					$sql_updnewcard="UPDATE 
													com_register_new_card 
												SET 
													remark='$refer_member_id',
													apply_date='$this->doc_date',
													apply_time='$this->doc_time',
							   						upd_date=CURDATE(),
					   								upd_time=CURTIME(),
							   						upd_user='$this->employee_id'
												WHERE 
													corporation_id='$this->m_corporation_id' AND 
													company_id='$this->m_company_id' AND 
													branch_id='$this->m_branch_id' AND
													member_id='$member_no'";
					$this->db->query($sql_updnewcard);
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
					//insert new member
					$sql_oldcrmcard="SELECT
												corporation_id,
												company_id,
												reg_date,
												reg_time,
												reg_user,
												upd_date,
												upd_time,
												upd_user,
												customer_id,
												member_no,
												brand_id,
												cardtype_id,
												id_card,
												action_id,
												apply_date,
												expire_date,
												status,
												member_ref,
												point,
												apply_shop,
												buy_amt,
												buy_net,
												apply_promo,
												co_promo,
												discount_per,
												limit_amt,
												special_day,
												renewal_card,
												send_date,
												send_time,
												send_status,
												profile_status
										 FROM
										 		crm_card
										 WHERE
										 		corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND
												member_no='$refer_member_id'";
						$row_oldcrmcard=$this->db->fetchAll($sql_oldcrmcard);
						if(count($row_oldcrmcard)>0){
							$arr_spday=parent::getComSpecialDay($refer_member_id);//refer old card
							$special_day=$arr_spday[0]['special_day'];	
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
								foreach($row_oldcrmcard as $data){
									$sql_crmcard="INSERT INTO crm_card
														SET
															corporation_id='$data[corporation_id]',
															company_id='$data[company_id]',
															reg_date=CURDATE(),
															reg_time=CURTIME(),
															reg_user='$this->employee_id',
															customer_id='$data[customer_id]',
															member_no='$member_no',
															brand_id='$data[brand_id]',
															cardtype_id='$data[cardtype_id]',
															id_card='$data[id_card]',
															action_id='$data[action_id]',
															apply_date='$data[apply_date]',
															expire_date='$data[expire_date]',
															status='0',
															member_ref='$refer_member_id',
															point='$data[point]',
															apply_shop='$data[apply_shop]',
															buy_amt='$data[buy_amt]',
															buy_net='$data[buy_net]',
															apply_promo='$data[apply_promo]',
															co_promo='$data[co_promo]',
															discount_per='$data[discount_per]',
															limit_amt='$data[limit_amt]',
															special_day='$special_day',
															renewal_card='$data[renewal_card]',
															send_date='$data[send_date]',
															send_time='$data[send_time]',
															send_status='$data[send_status]',
															profile_status='$data[profile_status]'";
									$this->db->query($sql_crmcard);
								}//foreach
						}//if		
						//update case card lost	
					
						//SET expire_date=date("Y-m-d", strtotime($this->doc_date,"-1 Days"));
						$sql_upd_crmcard="UPDATE crm_card
														SET 
															status='1',
															expire_date='$refer_member_expire_date'
													  WHERE 
													  		corporation_id='$this->m_corporation_id' AND
													  		company_id='$this->m_company_id' AND
													  		member_no='$refer_member_id'";
					  $res_upd_crmcard=$this->db->query($sql_upd_crmcard);
					  //end case $this->status_no='05' card lost
				}else{
					//update card
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
					$sql_updcard="UPDATE 
											com_register_new_card 
										SET 
											remark='$application_id',
											apply_date='$this->doc_date',
											apply_time=CURTIME(),
											upd_date=CURDATE(),
					   						upd_time=CURTIME(),
					   						upd_user='$this->employee_id'
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND 
											branch_id='$this->m_branch_id' AND
											member_id='$member_no'";								
					$this->db->query($sql_updcard);
					//clear if exist data in crm_crad,crm_profile
					$sql_del="DELETE a,b
								FROM crm_card AS a INNER JOIN crm_profile AS b 
								ON a.customer_id=b.customer_id							 
								WHERE a.member_no='$member_no'";
					$this->db->query($sql_del);	
					//create new format of customer_id
					$this->customer_id=parent::getCstNumber();
					$str_pattern=str_pad($this->customer_id,6,"0",STR_PAD_LEFT);
					$crm_customer_id=$this->m_company_id."-".$this->m_branch_id."-".$str_pattern;
					
					$arr_special_day=parent::getComSpecialDay($member_no);
					$special_day=$arr_special_day[0]['special_day'];
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
					$sql_crm_card="INSERT INTO crm_card 
										SET
											corporation_id='$this->m_corporation_id',
											company_id='$this->m_company_id',
											brand_id='2',
											cardtype_id='6',
											customer_id='$crm_customer_id',
											member_no='$member_no',
											apply_date='$this->doc_date',
											expire_date='$expire_date',
											member_ref='',
											apply_shop='$this->m_branch_id',
											apply_promo='$application_id',
											special_day='$special_day',
											reg_date=CURDATE(),
											reg_time=CURTIME(),
											reg_user='$this->employee_id'";					
					$res_qry_card=$this->db->query($sql_crm_card);
					$sql_crm_profile="INSERT INTO crm_profile
											SET
												corporation_id='$this->m_corporation_id',
												company_id='$this->m_company_id',
												customer_id='$crm_customer_id',
												reg_date=CURDATE(),
												reg_time=CURTIME(),
												reg_user='$this->employee_id'";
					$res_qry_profile=$this->db->query($sql_crm_profile);
					//set expire date for refer_member_id add 2 year is expire
					$this->expire_date=date("Y-m-d",strtotime($this->doc_date."+2 years"));					
					$arr_expiredate=explode("-",$this->expire_date);
					$exp_month=$arr_expiredate[1];
					$exp_year=$arr_expiredate[0];
					$this->expire_date=parent::lastday($exp_month,$exp_year);					
					$sql_upd_crmcard="UPDATE crm_card 
												SET 
													expire_date='$this->expire_date'
												WHERE 
													corporation_id='$this->m_corporation_id' AND
													company_id='$this->m_company_id' AND
													member_no='$member_no'";
					$res_upd_crmcard=$this->db->query($sql_upd_crmcard);
					
					//set expire date - 1 day of old card
					if(trim($refer_member_id)!=trim($member_no)){						
							$sql_upd_crmcard="UPDATE crm_card
															SET 
																status='1',
																expire_date='$refer_member_expire_date',
																upd_date=CURDATE(),
										   						upd_time=CURTIME(),
										   						upd_user='$this->employee_id'
														  WHERE 
														  		corporation_id='$this->m_corporation_id' AND
														  		company_id='$this->m_company_id' AND
														  		member_no='$refer_member_id'";
						   $this->db->query($sql_upd_crmcard);
					}
					
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_customer_id");					
					//running number customer_id
					$this->customer_id+=1;
					$sql_upd="UPDATE com_customer_id 
									SET customer_id='$this->customer_id'
										WHERE
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id'";
					$res_upd=$this->db->query($sql_upd);					
				}//end if status_no=05
				//..........crm data.......
				////////COMPLETE TRANSACTION///////////
				parent::TrancateTable("trn_tdiary2_sl_val");
				parent::TrancateTable("trn_promotion_tmp1");
				parent::TrancateTable("trn_tdiary2_sl");
				parent::TrancateTable("trn_tdiary1_sl");
				$strResult= "1#".$this->m_doc_no."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt;
				////////COMPLETE TRANSACTION///////////
			}else{
				$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt;
			}//if
			return $strResult;								
		  }//func
	}//class
?>