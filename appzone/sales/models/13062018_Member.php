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
		
		function checkLocalMemberExist($member_no,$mobile_no){
		    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		    $chk_m="N";
		    $sql_chk="SELECT COUNT(*)
                                   FROM `trn_diary1`
                                    WHERE
                                     `corporation_id`='$this->m_corporation_id' AND
			        				 `company_id`='$this->m_company_id' AND
                                     `branch_id`='$this->m_branch_id' AND
                                     `member_id` IN('$member_no')";
		    $n_chk=$this->db->fetchOne($sql_chk);
		    if($n_chk>0){
		        $chk_m="Y";
		    }else{
		        $sql_chk2="SELECT COUNT(*)
                                   FROM `trn_diary1`
                                    WHERE
                                     `corporation_id`='$this->m_corporation_id' AND
			        				 `company_id`='$this->m_company_id' AND
                                     `branch_id`='$this->m_branch_id' AND
                                     `mobile_no` IN('$mobile_no')";
		        $n_chk2=$this->db->fetchOne($sql_chk2);
		        if($n_chk2>0){
		            $chk_m="Y";
		        }
		    }
		    return  $chk_m;
		}//func
		
		function chkPlaybillRegister(){
		    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		    $bill_regis="N";
		    $sql_chk="SELECT COUNT(*) 
                                   FROM `trn_promotion_tmp1` 
                                    WHERE
                                     `corporation_id`='$this->m_corporation_id' AND
			        				 `company_id`='$this->m_company_id' AND
                                     `branch_id`='$this->m_branch_id' AND 
								     `computer_ip` ='$this->m_com_ip' AND 
								     `doc_date` ='$this->doc_date' AND 
                                     `product_id` IN('9900730')";
		    $n_chk=$this->db->fetchOne($sql_chk);
		    if($n_chk>0){
		        $bill_regis="Y";
		    }
		    return  $bill_regis;
		    
		}//func
		
		function clsCardQuota(){
			/**
			 * @for support OPID300
			 * @create 26052015
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_del="DELETE FROM `trn_card_quota` WHERE 1";
			$this->db->query($sql_del);
		}//func
		
		function addCardQuota($str_quota){
			/**
			 * @for support OPID300
			 * @create 26052015
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$obj_json=json_decode($str_quota);
			$special_day=$obj_json[0]->{'ops'};
			$quota=$obj_json[0]->{'quota'};
			$card_used=$obj_json[0]->{'card'};
			$card_balance=$obj_json[0]->{'balance'};
			$sql_add="INSERT INTO `trn_card_quota`
								SET
									`corporation_id`='$this->m_corporation_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id', 
									`special_day`='$special_day',
									`quota`='$quota',
									`card_used`='$card_used',
									`card_balance`='$card_balance',
									`reg_date`=CURDATE(),
									`reg_time`=CURTIME(), 
									`reg_user`='IT'	";
			$this->db->query($sql_add);
		}//func
		
		function add2Display($arr_data,$member_no,$tcase){
			/**
			 * @ create 27012015 for support 2 display
			 */
			 //add to trn_member_privilege		  
			 foreach($arr_data as $dataPro){
			 	$p_code=$dataPro['promo_code'];
			 	$status_color=strtoupper($dataPro['promo_color']);
		        if($status_color=='#DFDFDF'){
		        	$p_status='W';
		        }else if($status_color=='#FF3333'){
		        	$p_status='N';
		        }else if($status_color=='#66FF99'){
		        	$p_status='Y';
		        }
			 	 $sql_insert="INSERT INTO 
		        							`trn_member_privilege`
		        					SET
			        						 `corporation_id`='$this->m_corporation_id',
			        						 `company_id`='$this->m_company_id',
			        						 `branch_id`='$this->m_branch_id',
			        						 `computer_no` ='$this->m_computer_no',
									 		 `computer_ip` ='$this->m_com_ip',
			        						 `doc_date`='$this->doc_date',
			        						 `member_no`='$member_no',
			        						 `customer_id`='',
			        						 `promo_code`='$p_code',
			        						 `promo_type`='$tcase',
			        						 `promo_name`='$dataPro[promo_name]',
			        						 `promo_detail`='$dataPro[promo_detail]',
			        						 `status`='$p_status',
			        						 `reg_date`=CURDATE(),
			        						 `reg_time`=CURTIME(),
			        						 `reg_user`='IT',
			        						 `upd_date`='',
			        						 `upd_time`='',
			        						 `upd_user`=''";
		        $this->db->query($sql_insert);
			 }//foreach      
		}//func
		
		function trn2Display($member_no){
			/**
			 * @des for support  2 display
			 * @create 12022015
			 */
			$objCal=new Model_Calpromotion();
			$arr_a=$objCal->promo_chk_hbd($member_no,'N');
			if(!empty($arr_a)){
				$this->add2Display($arr_a,$member_no,'1');
			}
				
			$arr_b=$objCal->promo_chk_other($member_no,'N');
			if(!empty($arr_b)){
				$this->add2Display($arr_b,$member_no,'2');
			}
				
			$arr_c=$objCal->promo_chk_play($member_no,'N');
			if(!empty($arr_c)){
				$this->add2Display($arr_c,$member_no,'3');
			}
				
			$arr_d=$objCal->promo_chk_play_renew($member_no,'N');
			if(!empty($arr_d)){
				$this->add2Display($arr_d,$member_no,'4');
			}
		}//func
		
		function updDstCshTemp($promo_code,$type_discount,$discount){
			/**
			 * @desc for support promo member get member OPMGMC300
			 * @create 07052015
			 * @modify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$str_tbl=parent::countDiaryTemp();
			$arr_tbl=explode('#',$str_tbl);
			$tbl_name=$arr_tbl[1];
			$where="";
			if($promo_code=='OPPL300' || $promo_code=='OPMGMC300' || $promo_code=='OPMGMI300' ||
				$promo_code=='OPPLC300' || $promo_code=='OPPLI300'){
				$where.=" AND price>0";
			}			
			$sql_chk="SELECT * FROM $tbl_name 
							WHERE 
									`corporation_id`='$this->m_corporation_id' AND 
									`company_id`='$this->m_company_id'  AND
									`branch_id`='$this->m_branch_id' AND 
								    `computer_no` ='$this->m_computer_no' AND 
								    `computer_ip` ='$this->m_com_ip' AND 
								    `doc_date` ='$this->doc_date' AND
									`promo_code`='$promo_code' $where 
						ORDER BY price DESC";
			$rows_chk=$this->db->fetchAll($sql_chk);
			$i=0;
			foreach($rows_chk as $data){
				$seq=$data['seq'];
				$amount=$data['amount'];
				if($i<3){
					if($type_discount=='PERCENT'){
						$amount=$data['amount'];
						$s_discount=$amount*($discount/100);
						$net_amt=$amount-$s_discount;
					}
					$sql_upd_dist="UPDATE $tbl_name 
								SET 
									discount='$s_discount',
									net_amt='$net_amt'
								 WHERE 
									`corporation_id`='$this->m_corporation_id' AND 
									`company_id`='$this->m_company_id'  AND
									`branch_id`='$this->m_branch_id' AND 
								    `computer_no` ='$this->m_computer_no' AND 
								    `computer_ip` ='$this->m_com_ip' AND 
								    `doc_date` ='$this->doc_date' AND
								    `promo_code`='$promo_code'  AND 
									`seq`='$seq'";
					$this->db->query($sql_upd_dist);
				}else{
					$sql_upd_dist="UPDATE $tbl_name 
								SET 
									discount='0',
									net_amt='$amount'
								 WHERE 
									`corporation_id`='$this->m_corporation_id' AND 
									`company_id`='$this->m_company_id'  AND
									`branch_id`='$this->m_branch_id' AND 
								    `computer_no` ='$this->m_computer_no' AND 
								    `computer_ip` ='$this->m_com_ip' AND 
								    `doc_date` ='$this->doc_date' AND
								     `promo_code`='$promo_code'  AND 
									`seq`='$seq'";
					$this->db->query($sql_upd_dist);
				}
				$i++;
			}//foreach			
		}//func
		
		
		function chkProductCoPromotion($promo_code,$product_id,$quantity='1'){
			/***
			 **@desc
			 **@create 08042015
			 **@modify
			 **@return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_pro="SELECT * FROM `promo_other` 
			WHERE 
			promo_code='$promo_code' AND 
			promo_tp IN('MCOUPON','COUPON','LINE','MCS') AND 
			'$this->doc_date' BETWEEN start_date AND end_date";
			$rows_pro=$this->db->fetchAll($sql_pro);
			if(!empty($rows_pro)){
				$product_id=parent::getProduct($product_id,$quantity,'');
				//*WR25012016 for check unit on promo_head
				$co_lpromotion=$rows_pro[0]['play_last_promotion'];
				if($co_lpromotion=='Y'){
					$tbl_temp="trn_promotion_tmp1";
				}else{
					$tbl_temp="trn_tdiary2_sl";
				}
				//*WR25012016 for check unit on promo_head
				$sql_proh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$rows_proh=$this->db->fetchAll($sql_proh);
				if(!empty($rows_proh)){
					$unitn=$rows_proh[0]['unitn'];
					if($unitn>0){
						$sql_chk_limit="SELECT SUM(quantity) AS n_limit 
						FROM $tbl_temp 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND 
								`company_id`='$this->m_company_id'  AND
								`branch_id`='$this->m_branch_id' AND 
								`computer_no` ='$this->m_computer_no' AND 
								`computer_ip` ='$this->m_com_ip' AND 
								`doc_date` ='$this->doc_date' AND
								`promo_code`='$promo_code'";
						$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
						$n_curr=$row_chk_limit[0]['n_limit'];
						$n_curr+=$quantity;
						if($n_curr>$unitn){
							return '6';
						}
					}
				}	
				//*WR25012016 for check unit on promo_head
				
				$sql_chk_product="SELECT * FROM promo_detail 
							WHERE 
								promo_code='$promo_code' AND 
								(product_id='$product_id' OR product_id LIKE 'ALL') AND 
								'$this->doc_date' BETWEEN start_date AND end_date";
							$r_chk_product=$this->db->fetchAll($sql_chk_product);
							if(empty($r_chk_product)){
								return "5";
							}else{
								return true;
							}
			}else{
				return true;
			}
			
		}//function
		
		function chkLstOtherPro($promo_code){
			/***
			 * @desc for support promotion in table promo_other
			 * @modify : 24032015
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg="";
			$sql_pro="SELECT COUNT(*) FROM `promo_other` 
							WHERE 
								promo_code='$promo_code' AND 
								play_last_promotion='Y' AND
								'$this->doc_date' BETWEEN start_date AND end_date";
			$n_lstpro=$this->db->fetchOne($sql_pro);
			if($n_lstpro>0){
				return 'Y';
			}else{
				return 'N';
			}
		}//func
		
		function chkCompBySeqPro($promo_code,$seq_pro,$play_last_pro=''){
			/**
			 * @desc check complete promotion by seq
			 * @credate 18022016
			 * @modify 30062016
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				
			$res_tbltemp=parent::countDiaryTemp();
			$arr_tbltemp=explode('#',$res_tbltemp);
			$tbl_temp=$arr_tbltemp[1];
				
			//*WR13072016 for support last promotion
			if($play_last_pro=='Y'){
				$tbl_temp="trn_promotion_tmp1";
			}else{
				$tbl_temp="trn_tdiary2_sl";
			}
			
			$res_msg='Y';
			$sql_ph="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_ph=$this->db->fetchAll($sql_ph);
			$promo_amt_type=$rows_ph[0]['promo_amt_type'];
			$promo_price=$rows_ph[0]['promo_price'];
			$promo_amt=$rows_ph[0]['promo_amt'];
			$unitn=$rows_ph[0]['unitn'];
			if($promo_amt_type=='N' || $promo_price=='N'){
				$field_net='net_amt';
			}else if($promo_amt_type=='G' || $promo_price=='G'){
				$field_net='amount';
			}
			
			//*WR25112016 case check quantity
			$sql_pl="SELECT* FROM promo_detail WHERE promo_code='$promo_code' AND seq_pro='$seq_pro' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_pl=$this->db->fetchAll($sql_pl);
			$pl_quantity=intval($row_pl[0]['quantity']);
			$pl_limite_qty=$row_pl[0]['limite_qty'];
			$sql_sum_qty="SELECT SUM(quantity) AS sum_qty FROM $tbl_temp
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id'  AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no` ='$this->m_computer_no' AND
			`computer_ip` ='$this->m_com_ip' AND
			`doc_date` ='$this->doc_date' AND
			`promo_code`='$promo_code' AND
			`seq_set`='$seq_pro'";
			$row_sum_qty=$this->db->fetchAll($sql_sum_qty);
			$curr_qty=$row_sum_qty[0]['sum_qty'];
			if($curr_qty<$pl_quantity){
				$res_msg="ต้องซื้อให้ครบจำนวน ".$pl_quantity." ชิ้น";
				return $res_msg;
			}
				
			//check net
			$sql_sum_net="SELECT SUM($field_net) AS sum_net FROM $tbl_temp
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id'  AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no` ='$this->m_computer_no' AND
			`computer_ip` ='$this->m_com_ip' AND
			`doc_date` ='$this->doc_date' AND
			`promo_code`='$promo_code' 	";
			$row_sum_net=$this->db->fetchAll($sql_sum_net);
			$curr_net=$row_sum_net[0]['sum_net'];
			if($curr_net<$promo_amt && $promo_amt>0){
				$res_msg="ยอดซื้อสุทธิต้องมากกว่าหรือเท่ากับ ".$promo_amt." บาท";
			}else{		
				//check quantity
				$sql_sum_qty="SELECT SUM(quantity) AS sum_qty FROM $tbl_temp
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id'  AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no` ='$this->m_computer_no' AND
				`computer_ip` ='$this->m_com_ip' AND
				`doc_date` ='$this->doc_date' AND
				`promo_code`='$promo_code' ";//AND `seq_set`='$seq_pro'
				$row_sum_qty=$this->db->fetchAll($sql_sum_qty);
				$curr_qty=$row_sum_qty[0]['sum_qty'];
				if($curr_qty<$unitn && $unitn>0){
					$res_msg="ต้องซื้อให้ครบจำนวน ".$unitn." ชิ้น";
				}		
			}
			//*WR20092016 fixed code for promo_code
			if($promo_code=='OK03120816'){
				$arr_tmp1=array();
				$arr_tmp1[0]=2025;
				$arr_tmp1[1]=1905;
				$arr_tmp1[2]=1925;
				//check net
				$sql_sum_net="SELECT SUM($field_net) AS sum_net FROM $tbl_temp
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id'  AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no` ='$this->m_computer_no' AND
				`computer_ip` ='$this->m_com_ip' AND
				`doc_date` ='$this->doc_date' AND
				`promo_code`='$promo_code' 	";
				$row_sum_net=$this->db->fetchAll($sql_sum_net);
				$curr_net=$row_sum_net[0]['sum_net'];
				$curr_net=intval($curr_net);
				if(!in_array($curr_net,$arr_tmp1)){
					$res_msg="ซื้อสินค้าไม่ครบตามเงือนไขโปรโมชั่น $curr_net กรุณาตรวจสอบรายการซื้ออีกครั้ง";
				}			
			}
			return $res_msg;
		}//func
		
		function chkCompBySeqPro21072016($promo_code,$seq_pro){
			/**
			 * @desc check complete promotion by seq
			 * @credate 18022016
			 * @modify 30062016
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");				
			$res_tbltemp=parent::countDiaryTemp();
			$arr_tbltemp=explode('#',$res_tbltemp);
			$tbl_temp=$arr_tbltemp[1];				
			//*WR23052016 for support mini bueaty book				
			if($tbl_temp=='N' || $tbl_temp==''){
				$tbl_temp="trn_tdiary2_sl";
			}				
			$res_msg='Y';
			$sql_ph="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_ph=$this->db->fetchAll($sql_ph);
			$promo_amt_type=$rows_ph[0]['promo_amt_type'];
			$promo_price=$rows_ph[0]['promo_price'];
			$promo_amt=$rows_ph[0]['promo_amt'];
			$unitn=$rows_ph[0]['unitn'];
			if($promo_amt_type=='N' || $promo_price=='N'){
				$field_net='net_amt';
			}else if($promo_amt_type=='G' || $promo_price=='G'){
				$field_net='amount';
			}				
			//check net
			$sql_sum_net="SELECT SUM($field_net) AS sum_net FROM $tbl_temp
								WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id'  AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date` ='$this->doc_date' AND
								`promo_code`='$promo_code' 	";
			$row_sum_net=$this->db->fetchAll($sql_sum_net);
			$curr_net=$row_sum_net[0]['sum_net'];
			if($curr_net<$promo_amt){
				$res_msg="ยอดซื้อสุทธิต้องมากกว่าหรือเท่ากับ ".$promo_amt." บาท";
			}else{		
				//check quantity
				$sql_sum_qty="SELECT SUM(quantity) AS sum_qty FROM $tbl_temp
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id'  AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no` ='$this->m_computer_no' AND
				`computer_ip` ='$this->m_com_ip' AND
				`doc_date` ='$this->doc_date' AND
				`promo_code`='$promo_code' AND
				`seq_set`='$seq_pro'";
				$row_sum_qty=$this->db->fetchAll($sql_sum_qty);
				$curr_qty=$row_sum_qty[0]['sum_qty'];
				if($curr_qty<$unitn){
					$res_msg="ต้องซื้อให้ครบจำนวน ".$unitn." ชิ้น";
				}		
			}
			return $res_msg;
		}//func
		
		function chkCompBySeqPro04072016($promo_code,$seq_pro){
			/**
			 * @desc check complete promotion by seq
			 * @credate 18022016
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				
			$res_tbltemp=parent::countDiaryTemp();
			$arr_tbltemp=explode('#',$res_tbltemp);
			$tbl_temp=$arr_tbltemp[1];
			
			//*WR23052016 for support mini bueaty book
			if($tbl_temp=='N' || $tbl_temp==''){
				$tbl_temp="trn_tdiary2_sl";
			}
				
			$res_msg='Y';
			$sql_ph="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_ph=$this->db->fetchAll($sql_ph);
			$promo_amt_type=$rows_ph[0]['promo_amt_type'];
			$promo_amt=$rows_ph[0]['promo_amt'];
			$unitn=$rows_ph[0]['unitn'];
			if($promo_amt_type=='N'){
				$field_net='net_amt';
			}else if($promo_amt_type=='G'){
				$field_net='amount';
			}
				
			//check net
			$sql_sum_net="SELECT SUM($field_net) AS sum_net FROM $tbl_temp
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id'  AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no` ='$this->m_computer_no' AND
			`computer_ip` ='$this->m_com_ip' AND
			`doc_date` ='$this->doc_date' AND
			`promo_code`='$promo_code' 	";
			$row_sum_net=$this->db->fetchAll($sql_sum_net);
			$curr_net=$row_sum_net[0]['sum_net'];
			if($curr_net<$promo_amt){
				$res_msg="ยอดซื้อสุทธิต้องมากกว่าหรือเท่ากับ ".$promo_amt." บาท";
			}else{
		
				//check quantity
				$sql_sum_qty="SELECT SUM(quantity) AS sum_qty FROM $tbl_temp
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id'  AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no` ='$this->m_computer_no' AND
				`computer_ip` ='$this->m_com_ip' AND
				`doc_date` ='$this->doc_date' AND
				`promo_code`='$promo_code' AND
				`seq_set`='$seq_pro'";
				$row_sum_qty=$this->db->fetchAll($sql_sum_qty);
				$curr_qty=$row_sum_qty[0]['sum_qty'];
				if($curr_qty<$unitn){
					$res_msg="ต้องซื้อให้ครบจำนวน ".$unitn." ชิ้น";
				}
		
			}
			return $res_msg;
		}//func
		
		function playBySeqPro($promo_code,$seq_pro){
			/**
			 * @desc
			 * @credate 18022016
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$res_msg='N#';
			$sql_chk_seqpro="SELECT * FROM promo_detail
			WHERE
			promo_code='$promo_code' AND
			seq_pro='$seq_pro' AND
			'$this->doc_date' BETWEEN start_date AND end_date";
			$rows_pro=$this->db->fetchAll($sql_chk_seqpro);
			if(!empty($rows_pro)){
				$type_discount=$rows_pro[0]['type_discount'];
				if(strtoupper($type_discount)=='NORMAL'){
					$title="กรุณาสแกนสินค้าตัวซื้อ";
				}else if(strtoupper($type_discount)=='FREE'){
					$title="กรุณาสแกนสินค้าตัวแถมฟรี";
				}
				$res_msg='Y#'.$title;
			}
			return $res_msg;
		}//func
		
		function setPdtMstPro($seq_pro,$member_no,$status_no='00',$promo_code,$product_id,$quantity,$member_percent='0',$play_last_pro='N'){
		    /***
		     * @desc for support promotion in table promo_head,promo_detail
		     * @modify : 06022017
		     */
		    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		    $msg="";
		    if($play_last_pro=='N' || $play_last_pro==''){
		        //*WR08082017
		        $str_tbl_temp=parent::countDiaryTemp();
		        $arr_tbl_temp=explode('#',$str_tbl_temp);
		        $tbl_temp=$arr_tbl_temp[1];
		        //*WR24022016
		        if($tbl_temp=='trn_promotion_tmp1'){
		            $tbl_temp="trn_promotion_tmp1";
		        }else{
		            $tbl_temp="trn_tdiary2_sl";
		        }
		        
		        //$tbl_temp="trn_tdiary2_sl";
		        $seq_set=$seq_pro;
		    }else{
		        $tbl_temp="trn_promotion_tmp1";
		        $sql_max_seq_set="SELECT MAX(seq_set) AS max_seq_set
				FROM $tbl_temp
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id'  AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no` ='$this->m_computer_no' AND
				`computer_ip` ='$this->m_com_ip' AND
				`doc_date` ='$this->doc_date' AND
				`promo_code`='$promo_code' ";
		        $row_max_seq_set=$this->db->fetchAll($sql_max_seq_set);
		        if(!empty($row_max_seq_set)){
		            $seq_set=$row_max_seq_set[0]['max_seq_set'];
		        }else{
		            $seq_set=$seq_pro;
		        }
		    }//end if
		    
		    //*WR25092017 check for support pro line เลือกได้โปรใดโปรหนึ่งเท่านั้น
		    $n_chkone=0;
		    if($promo_code=='OI10490817'){
		        $sql_chkone="SELECT COUNT(*) FROM $tbl_temp WHERE promo_code='OI10500817'";
		        $n_chkone=$this->db->fetchOne($sql_chkone);
		    }else if($promo_code=='OI10500817'){
		        $sql_chkone="SELECT COUNT(*) FROM $tbl_temp WHERE promo_code='OI10490817'";
		        $n_chkone=$this->db->fetchOne($sql_chkone);
		    }
		    if($n_chkone>0){
		        $msg="N#เลือกเล่นได้แค่โปรโมชั่นเดียวเท่านั้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		        return $msg;
		    }
		    
		    ////*****************************START***********************************************************
		    $product_id=parent::getProduct($product_id,$quantity,'','',$promo_code);
		    if($product_id=='3'){
		        //product lock
		        $msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		    }else if($product_id=='2'){
		        //not found product in com_stock_master
		        $msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		    }else if($product_id=='1'){
		        //not found product in com_product_master
		        $msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		    }else{
		        //-------------------- GET PRICE 16012017 ------------------------------------------------
		        $arr_product=parent::browsProduct($product_id);
		        $item_price=$arr_product[0]['price'];
		        $item_amount=$item_price * $quantity;
		        //-------------------- CHECK LIMIT AMOUNT 16012017 -----------------------------
		        //ใช้ได้กับการจำกัดวงเงินเท่านั้น
		        $sql_other="SELECT* FROM promo_other WHERE promo_code ='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date ";
		        $row_other=$this->db->fetchAll($sql_other);
		        if(!empty($row_other)){
		            $sql_chkamt="SELECT SUM(amount) as c_amt,SUM(net_amt) as c_net  FROM $tbl_temp
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id'  AND
					`branch_id`='$this->m_branch_id' AND
					`computer_no` ='$this->m_computer_no' AND
					`computer_ip` ='$this->m_com_ip' AND
					`doc_date` ='$this->doc_date' AND
					`promo_code`='$promo_code'";
		            $row_chkamt=$this->db->fetchAll($sql_chkamt);
		            $c_amt=$row_chkamt[0]['c_amt'];
		            $c_net=$row_chkamt[0]['c_net'];
		            $c_net=intval($c_net);
		            $sum_amt=$item_amount+$c_amt;
		            $sum_net=$item_amount+$c_net;//return to check $item_amount for net_amt?
		            $buy_type=$row_other[0]['buy_type'];
		            $buy_status=$row_other[0]['buy_status'];
		            $start_baht=$row_other[0]['start_baht'];
		            $end_baht=$row_other[0]['end_baht'];
		            if($buy_type=='G' && $end_baht>1){
		                if($buy_status=='L'){
		                    //ต้องไม่เกิน
		                    if($sum_amt>$end_baht){
		                        //flag Y or N#buy_status L or G # new amount
		                        $msg="N#ซื้อได้ไม่เกิน $end_baht  บาท กรุณาตรวจสอบอีกครั้ง";
		                        return $msg;
		                    }
		                }else if($buy_status=='G'){
		                    //ต้องไม่น้อยกว่า
		                    if($sum_amt<$end_baht){
		                        $msg="N#ยอดซื้อน้อยกว่า $end_baht  บาท กรุณาตรวจสอบอีกครั้ง";
		                        return $msg;
		                    }
		                }
		            }
		            
		        }//end if
		        
		        //-------------------- CHECK LIMIT AMOUNT 16012017 -----------------------------
		        
		        $arr_product=parent::browsProduct($product_id);
		        $sql_proh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
		        $rows_proh=$this->db->fetchAll($sql_proh);
		        if(!empty($rows_proh)){
		            //*WR01072016 for suport line promotion
		            $unitn=$rows_proh[0]['unitn'];
		            $promo_price=$rows_proh[0]['promo_price'];
		            $process=$rows_proh[0]['process'];
		            $promo_amt=$rows_proh[0]['promo_amt'];
		            //*WR12022015 chkeck repeat
		            $check_repeat=$rows_proh[0]['check_repeat'];
		            if($check_repeat=='Y'){
		                $sql_rpt="SELECT COUNT(*)
						FROM $tbl_temp
						WHERE
						`corporation_id` ='$this->m_corporation_id' AND
						`company_id` ='$this->m_company_id' AND
						`branch_id` ='$this->m_branch_id' AND
                        `computer_ip` ='$this->m_com_ip'  AND
						`doc_date` ='$this->doc_date' AND
						`promo_code` ='$promo_code'	 AND
						`product_id` ='$product_id'";/*AND seq_set='$seq_set'*/
		                $n_rpt=$this->db->fetchOne($sql_rpt);
		                if($n_rpt>0){
		                    $msg="N#ไม่สามารถซื้อสินค้า $product_id ซ้ำได้ กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		                    return $msg;
		                    exit();
		                }
		            }//if
		            //*WR12022015 check limited
		            
		            //*WR01072016 for last promotion
		            $sql_chk_limit3="SELECT SUM(quantity) AS curr_qty
					FROM $tbl_temp
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id'  AND
					`branch_id`='$this->m_branch_id' AND
					`computer_no` ='$this->m_computer_no' AND
					`computer_ip` ='$this->m_com_ip' AND
					`doc_date` ='$this->doc_date' AND
					`promo_code`='$promo_code' AND
					`seq_set`='$seq_set'";//$seq_pro
		            $row_limit_lastpro2=$this->db->fetchAll($sql_chk_limit3);
		            if(!empty($row_limit_lastpro2)){
		                $curr_qty=$row_limit_lastpro2[0]['curr_qty'];
		            }else{
		                $curr_qty=0;
		            }
		            $curr_qty=$curr_qty+1;
		            $sql_chk_limit2="SELECT b.promo_pos, SUM( a.net_amt ) AS sum_net_amt, SUM( a.amount ) AS sum_amount
					FROM trn_tdiary2_sl AS a INNER JOIN promo_head AS b ON ( a.promo_code = b.promo_code )
					WHERE
					a.`corporation_id`='$this->m_corporation_id' AND
					a.`company_id`='$this->m_company_id'  AND
					a.`branch_id`='$this->m_branch_id' AND
					a.`computer_no` ='$this->m_computer_no' AND
					a.`computer_ip` ='$this->m_com_ip' AND
					a.`doc_date` ='$this->doc_date' AND
					b.`promo_pos` NOT IN ('LO')";
		            $row_limit_lastpro=$this->db->fetchAll($sql_chk_limit2);
		            if(!empty($row_limit_lastpro)){
		                $sum_net_amt=$row_limit_lastpro[0]['sum_net_amt'];
		                $sum_amount=$row_limit_lastpro[0]['sum_amount'];
		            }else{
		                $sum_net_amt=0;
		                $sum_amount=0;
		            }
		            
		            //*WR01072016 for suport line promotion
		            if($process=='C'){//C=ซื้อครบทุกๆ,F=Fix ที่ unitn ที่กำหนด
		                if($promo_price=='N'){
		                    //ใช้ยอด $net_amt
		                    $unitn=$unitn * floor($sum_net_amt/$promo_amt); //จะทราบว่าได้กี่ชิ้น
		                }else if($promo_price=='G'){
		                    //ใช้ยอด $amount
		                    $unitn=$unitn* floor($sum_amount/$promo_amt); //จะทราบว่าได้กี่ชิ้น
		                }
		                
		                if($curr_qty>$unitn){
		                    $msg="N#ซื้อได้ไม่เกิน $unitn  ชิ้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		                    return $msg;
		                }
		                //*WR07022017 ตรวจสอบสิทธิที่ได้เล่นโปรท้ายบิลตาม จำนวน unitn สิทธิ
		                $sql_chkpriv="SELECT COUNT(*) AS n_priv
						FROM $tbl_temp
						WHERE
						`corporation_id`='$this->m_corporation_id' AND
						`company_id`='$this->m_company_id'  AND
						`branch_id`='$this->m_branch_id' AND
						`computer_no` ='$this->m_computer_no' AND
						`computer_ip` ='$this->m_com_ip' AND
						`doc_date` ='$this->doc_date' AND
						`promo_code` IN('OI10511116','OI10521116','OI10330417') ";
		                $n_priv=$this->db->fetchOne($sql_chkpriv);
		                if($n_priv>=$unitn){
		                    $msg="N#ซื้อได้ไม่เกิน $unitn  สิทธิเท่านั้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		                    return $msg;
		                }
		            }//end process ='C'
		            
		            $sql_chk_limit="SELECT SUM(quantity) AS n_limit
					FROM $tbl_temp
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id'  AND
					`branch_id`='$this->m_branch_id' AND
					`computer_no` ='$this->m_computer_no' AND
					`computer_ip` ='$this->m_com_ip' AND
					`doc_date` ='$this->doc_date' AND
					`promo_code`='$promo_code' AND seq_set='$seq_set' ";
		            $row_chk_limit=$this->db->fetchAll($sql_chk_limit);
		            if(!empty($row_chk_limit)){
		                $n_curr=$row_chk_limit[0]['n_limit'];
		            }else{
		                $n_curr=0;
		            }
		            $n_curr+=$quantity;
		            //*WR ================= 12022015 check limited ===============
		            $sql_chk_product="SELECT * FROM promo_detail
						WHERE
						promo_code='$promo_code' AND
						seq_pro=$seq_pro AND
						(product_id='$product_id' OR product_id='ALL') AND
						'$this->doc_date' BETWEEN start_date AND end_date";
		            $r_chk_product=$this->db->fetchAll($sql_chk_product);
		            if(empty($r_chk_product)){
		                $msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		                return $msg;
		            }else if($r_chk_product[0]['limite_qty']>0 && $n_curr>$r_chk_product[0]['limite_qty']){
		                $limite_qty=$r_chk_product[0]['limite_qty'];
		                $msg="N#ซื้อได้ไม่เกิน $limite_qty  ชิ้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		                return $msg;
		            }else{
		                //--------------------------check price --------------------------------------
		                $compare_price=$rows_proh[0]['compare'];
		                if($compare_price=='G'){
		                    $price=$arr_product[0]['price'];
		                    $chk_type_discount=$r_chk_product[0]['type_discount'];
		                    if($seq_pro>'1' && strtoupper($chk_type_discount)=="FREE"){
		                        $sql_cp="SELECT price
									FROM $tbl_temp
									WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id'  AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no` ='$this->m_computer_no' AND
									`computer_ip` ='$this->m_com_ip' AND
									`promo_code`='$promo_code' AND
									`seq_set`>'0' AND
									`price` > '0'   AND
									`doc_date`= '$this->doc_date'
									ORDER BY price ASC LIMIT 0,1";
		                        $r2_cp=$this->db->fetchAll($sql_cp);
		                        if(!empty($r2_cp)){
		                            if($price>$r2_cp[0]['price']){
		                                $msg="N#รหัสสินค้า $product_id ราคาต้องน้อยกว่าสินค้าตัวหลัก กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
		                                return $msg;
		                                exit();
		                            }
		                            
		                        }
		                    }//if seq_pro
		                }
		            }
		            //*WR26112014
		            ////////////////////////////////////////////////////////////
		            //--------------------------check price --------------------------------------
		            $type_discount=$r_chk_product[0]['type_discount'];
		            $doc_tp='SL';
		            $seq_set=$r_chk_product[0]['seq_pro'];//*WR21072016 remark
		            if(strtoupper($type_discount)=='FREE'){
		                $promo_tp='F';
		            }else if(strtoupper($type_discount)=='PERCENT'){
		                $promo_tp='P';
		                $discount_percent='PERCENT';//for line items
		                $discount=$r_chk_product[0]['discount'];
		            }else if(strtoupper($type_discount)=='PRICE1'){
		                //*WR27022015 PRICE1 สินค้าต้องการขายราคานี้
		                $promo_tp='P';
		                $sp_price=$arr_product[0]['price'];
		                $discount=$sp_price-$r_chk_product[0]['discount'];
		            }else{
		                $promo_tp='P';
		            }
		            if($r_chk_product[0]['discount_member']=='Y'){
		                $member_percent1=$member_percent;
		            }
		            //*WR24032015
		            $employee_id=$this->user_id;
		            $product_status='';
		            $application_id=$promo_code;
		            $card_status='';
		            $get_point='Y';
		            $promo_id='';
		            $member_percent2='';
		            $co_promo_percent='';
		            $promo_st='';
		            $promo_amt='';
		            $promo_amt_type='';
		            $check_repeat='';
		            $web_promo='N';
		            $objCashier=new Model_Cashier();
		            if($tbl_temp=="trn_tdiary2_sl"){
		                $msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
		                    $status_no,$product_status,$application_id,$card_status,
		                    $get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
		                    $co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
		                    $check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount,$seq_set);
		            }else{
		                @$objCashier->setCshValTemp($doc_tp,'',$employee_id,$member_no,$product_id,$quantity,
		                    $status_no,$product_status,'',$card_status,$get_point,
		                    $promo_id,$discount_percent,$member_percent1,$member_percent2,
		                    $co_promo_percent,$coupon_percent,$promo_st);
		                $msg=$objCashier->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
		                    $status_no,$product_status,$application_id,$card_status,
		                    $get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
		                    $co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);
		            }
		        }
		        ///////////////////////////////////////////////////////////
		    }
		    ////******************************************************* STOP **********************************************************************
		    return $msg."#$tbl_temp";
		}//func
		
		function setPdtMstPro26032018($seq_pro,$member_no,$status_no='00',$promo_code,$product_id,$quantity,$member_percent='0',$play_last_pro='N'){
			/***
			 * @desc for support promotion in table promo_head,promo_detail
			 * @modify : 06022017
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg="";
			if($play_last_pro=='N' || $play_last_pro==''){
				$tbl_temp="trn_tdiary2_sl";
				$seq_set=$seq_pro;
			}else{
				$tbl_temp="trn_promotion_tmp1";
				$sql_max_seq_set="SELECT MAX(seq_set) AS max_seq_set
				FROM $tbl_temp
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id'  AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no` ='$this->m_computer_no' AND
				`computer_ip` ='$this->m_com_ip' AND
				`doc_date` ='$this->doc_date' AND
				`promo_code`='$promo_code' ";
				$row_max_seq_set=$this->db->fetchAll($sql_max_seq_set);
				if(!empty($row_max_seq_set)){
					$seq_set=$row_max_seq_set[0]['max_seq_set'];
				}else{
					$seq_set=$seq_pro;
				}
			}//end if
			////*****************************START***********************************************************
			$product_id=parent::getProduct($product_id,$quantity,'');
			if($product_id=='3'){
				//product lock
				$msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
			}else if($product_id=='2'){
				//not found product in com_stock_master
				$msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
			}else if($product_id=='1'){
				//not found product in com_product_master
				$msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
			}else{		
				//-------------------- GET PRICE 16012017 ------------------------------------------------
				$arr_product=parent::browsProduct($product_id);
				$item_price=$arr_product[0]['price'];
				$item_amount=$item_price * $quantity;
				//-------------------- CHECK LIMIT AMOUNT 16012017 -----------------------------
				//ใช้ได้กับการจำกัดวงเงินเท่านั้น
				$sql_other="SELECT* FROM promo_other WHERE promo_code ='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date ";
				$row_other=$this->db->fetchAll($sql_other);
				if(!empty($row_other)){
					$sql_chkamt="SELECT SUM(amount) as c_amt,SUM(net_amt) as c_net  FROM $tbl_temp
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id'  AND
					`branch_id`='$this->m_branch_id' AND
					`computer_no` ='$this->m_computer_no' AND
					`computer_ip` ='$this->m_com_ip' AND
					`doc_date` ='$this->doc_date' AND
					`promo_code`='$promo_code'";
					$row_chkamt=$this->db->fetchAll($sql_chkamt);
					$c_amt=$row_chkamt[0]['c_amt'];
					$c_net=$row_chkamt[0]['c_net'];						
					$c_net=intval($c_net);						
					$sum_amt=$item_amount+$c_amt;
					$sum_net=$item_amount+$c_net;//return to check $item_amount for net_amt?						
					$buy_type=$row_other[0]['buy_type'];
					$buy_status=$row_other[0]['buy_status'];
					$start_baht=$row_other[0]['start_baht'];
					$end_baht=$row_other[0]['end_baht'];
					if($buy_type=='G' && $end_baht>1){
						if($buy_status=='L'){
							//ต้องไม่เกิน								
							if($sum_amt>$end_baht){
								//flag Y or N#buy_status L or G # new amount
								$msg="N#ซื้อได้ไม่เกิน $end_baht  บาท กรุณาตรวจสอบอีกครั้ง";
								return $msg;
							}
						}else if($buy_status=='G'){
							//ต้องไม่น้อยกว่า
							if($sum_amt<$end_baht){
								$msg="N#ยอดซื้อน้อยกว่า $end_baht  บาท กรุณาตรวจสอบอีกครั้ง";
								return $msg;
							}
						}
					}
						
				}//end if
						
				//-------------------- CHECK LIMIT AMOUNT 16012017 -----------------------------	
		
				$arr_product=parent::browsProduct($product_id);
				$sql_proh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$rows_proh=$this->db->fetchAll($sql_proh);
				if(!empty($rows_proh)){
					//*WR01072016 for suport line promotion
					$unitn=$rows_proh[0]['unitn'];
					$promo_price=$rows_proh[0]['promo_price'];
					$process=$rows_proh[0]['process'];
					$promo_amt=$rows_proh[0]['promo_amt'];
					//*WR12022015 chkeck repeat
					$check_repeat=$rows_proh[0]['check_repeat'];
					if($check_repeat=='Y'){
						$sql_rpt="SELECT COUNT(*)
						FROM $tbl_temp
						WHERE
						`corporation_id` ='$this->m_corporation_id' AND
						`company_id` ='$this->m_company_id' AND
						`branch_id` ='$this->m_branch_id' AND
						`doc_date` ='$this->doc_date' AND
						`promo_code` ='$promo_code'	 AND
						`product_id` ='$product_id' AND
						`computer_no` ='$this->m_computer_no' AND
						`computer_ip` ='$this->m_com_ip'";
						$n_rpt=$this->db->fetchOne($sql_rpt);
						if($n_rpt>0){
							$msg="N#ไม่สามารถซื้อสินค้า $product_id ซ้ำได้ กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
							return $msg;
							exit();
						}
					}//if
					//*WR12022015 check limited
		
					//*WR01072016 for last promotion
					$sql_chk_limit3="SELECT SUM(quantity) AS curr_qty
					FROM $tbl_temp
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id'  AND
					`branch_id`='$this->m_branch_id' AND
					`computer_no` ='$this->m_computer_no' AND
					`computer_ip` ='$this->m_com_ip' AND
					`doc_date` ='$this->doc_date' AND
					`promo_code`='$promo_code' AND
					`seq_set`='$seq_set'";//$seq_pro
					$row_limit_lastpro2=$this->db->fetchAll($sql_chk_limit3);
					if(!empty($row_limit_lastpro2)){
						$curr_qty=$row_limit_lastpro2[0]['curr_qty'];
					}else{
						$curr_qty=0;
					}
					$curr_qty=$curr_qty+1;
					$sql_chk_limit2="SELECT b.promo_pos, SUM( a.net_amt ) AS sum_net_amt, SUM( a.amount ) AS sum_amount
					FROM trn_tdiary2_sl AS a INNER JOIN promo_head AS b ON ( a.promo_code = b.promo_code )
					WHERE
					a.`corporation_id`='$this->m_corporation_id' AND
					a.`company_id`='$this->m_company_id'  AND
					a.`branch_id`='$this->m_branch_id' AND
					a.`computer_no` ='$this->m_computer_no' AND
					a.`computer_ip` ='$this->m_com_ip' AND
					a.`doc_date` ='$this->doc_date' AND
					b.`promo_pos` NOT IN ('LO')";
					$row_limit_lastpro=$this->db->fetchAll($sql_chk_limit2);
					if(!empty($row_limit_lastpro)){
						$sum_net_amt=$row_limit_lastpro[0]['sum_net_amt'];
						$sum_amount=$row_limit_lastpro[0]['sum_amount'];
					}else{
						$sum_net_amt=0;
						$sum_amount=0;
					}
		
					//*WR01072016 for suport line promotion
					if($process=='C'){//C=ซื้อครบทุกๆ,F=Fix ที่ unitn ที่กำหนด
						if($promo_price=='N'){
							//ใช้ยอด $net_amt
							$unitn=$unitn * floor($sum_net_amt/$promo_amt); //จะทราบว่าได้กี่ชิ้น
						}else if($promo_price=='G'){
							//ใช้ยอด $amount
							$unitn=$unitn* floor($sum_amount/$promo_amt); //จะทราบว่าได้กี่ชิ้น
						}
						if($curr_qty>$unitn){
							$msg="N#ซื้อได้ไม่เกิน $unitn  ชิ้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
							return $msg;
						}
						//*WR07022017 ตรวจสอบสิทธิที่ได้เล่นโปรท้ายบิลตาม จำนวน unitn สิทธิ
						$sql_chkpriv="SELECT COUNT(*) AS n_priv
						FROM $tbl_temp
						WHERE
						`corporation_id`='$this->m_corporation_id' AND
						`company_id`='$this->m_company_id'  AND
						`branch_id`='$this->m_branch_id' AND
						`computer_no` ='$this->m_computer_no' AND
						`computer_ip` ='$this->m_com_ip' AND
						`doc_date` ='$this->doc_date' AND
						`promo_code` IN('OI10511116','OI10521116') ";
						$n_priv=$this->db->fetchOne($sql_chkpriv);
						if($n_priv>=$unitn){
							$msg="N#ซื้อได้ไม่เกิน $unitn  สิทธิเท่านั้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
							return $msg;
						}
					}//end process ='C'
					$sql_chk_limit="SELECT SUM(quantity) AS n_limit
					FROM $tbl_temp
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id'  AND
					`branch_id`='$this->m_branch_id' AND
					`computer_no` ='$this->m_computer_no' AND
					`computer_ip` ='$this->m_com_ip' AND
					`doc_date` ='$this->doc_date' AND
					`promo_code`='$promo_code' AND seq_set='$seq_set' ";
					$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
					if(!empty($row_chk_limit)){
						$n_curr=$row_chk_limit[0]['n_limit'];
					}else{
						$n_curr=0;
					}
					$n_curr+=$quantity;
					//*WR ================= 12022015 check limited ===============		
						$sql_chk_product="SELECT * FROM promo_detail
						WHERE
						promo_code='$promo_code' AND
						seq_pro=$seq_pro AND
						(product_id='$product_id' OR product_id='ALL') AND
						'$this->doc_date' BETWEEN start_date AND end_date";
						$r_chk_product=$this->db->fetchAll($sql_chk_product);
						if(empty($r_chk_product)){
							$msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
							return $msg;
						}else if($r_chk_product[0]['limite_qty']>0 && $n_curr>$r_chk_product[0]['limite_qty']){
							$limite_qty=$r_chk_product[0]['limite_qty'];
							$msg="N#ซื้อได้ไม่เกิน $limite_qty  ชิ้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
							return $msg;
						}else{
							//--------------------------check price --------------------------------------
							$compare_price=$rows_proh[0]['compare'];
							if($compare_price=='G'){
								$price=$arr_product[0]['price'];
								$chk_type_discount=$r_chk_product[0]['type_discount'];
								if($seq_pro>'1' && strtoupper($chk_type_discount)=="FREE"){
									$sql_cp="SELECT price
									FROM $tbl_temp
									WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id'  AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no` ='$this->m_computer_no' AND
									`computer_ip` ='$this->m_com_ip' AND
									`promo_code`='$promo_code' AND
									`seq_set`>'0' AND
									`price` > '0'   AND
									`doc_date`= '$this->doc_date'
									ORDER BY price ASC LIMIT 0,1";
									$r2_cp=$this->db->fetchAll($sql_cp);
									if(!empty($r2_cp)){
										if($price>$r2_cp[0]['price']){
											$msg="N#รหัสสินค้า $product_id ราคาต้องน้อยกว่าสินค้าตัวหลัก กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
											return $msg;
											exit();
										}
		
									}
								}//if seq_pro
							}
						}
						//*WR26112014
						////////////////////////////////////////////////////////////
						//--------------------------check price --------------------------------------
						$type_discount=$r_chk_product[0]['type_discount'];
						$doc_tp='SL';							
						$seq_set=$r_chk_product[0]['seq_pro'];//*WR21072016 remark							
						if(strtoupper($type_discount)=='FREE'){
							$promo_tp='F';
						}else if(strtoupper($type_discount)=='PERCENT'){
							$promo_tp='P';
							$discount_percent='PERCENT';//for line items
							$discount=$r_chk_product[0]['discount'];
						}else if(strtoupper($type_discount)=='PRICE1'){
							//*WR27022015 PRICE1 สินค้าต้องการขายราคานี้
							$promo_tp='P';
							$sp_price=$arr_product[0]['price'];
							$discount=$sp_price-$r_chk_product[0]['discount'];
						}else{
							$promo_tp='P';
						}
						if($r_chk_product[0]['discount_member']=='Y'){
							$member_percent1=$member_percent;
						}
						//*WR24032015
						$employee_id=$this->user_id;
						$product_status='';
						$application_id=$promo_code;
						$card_status='';
						$get_point='Y';
						$promo_id='';
						$member_percent2='';
						$co_promo_percent='';
						$promo_st='';
						$promo_amt='';
						$promo_amt_type='';
						$check_repeat='';
						$web_promo='N';
						$objCashier=new Model_Cashier();
						if($tbl_temp=="trn_tdiary2_sl"){
							$msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
									$status_no,$product_status,$application_id,$card_status,
									$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
									$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
									$check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount,$seq_set);
						}else{
							@$objCashier->setCshValTemp($doc_tp,'',$employee_id,$member_no,$product_id,$quantity,
									$status_no,$product_status,'',$card_status,$get_point,
									$promo_id,$discount_percent,$member_percent1,$member_percent2,
									$co_promo_percent,$coupon_percent,$promo_st);
							$msg=$objCashier->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
									$status_no,$product_status,$application_id,$card_status,
									$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
									$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);
						}
					}
					///////////////////////////////////////////////////////////
			}
			////******************************************************* STOP **********************************************************************
			return $msg."#$tbl_temp";
		}//func
		
		
				
		function setPdtOtherPro($seq_pro='',$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent=''){			
			/***
			 * @desc for support promotion in table promo_other
			 * @modify : 24032015
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg="";
			$sql_pro="SELECT * FROM `promo_other` WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_pro=$this->db->fetchAll($sql_pro);
			if(!empty($rows_pro)){
				//*WR10092015
				$co_lpromotion=$rows_pro[0]['play_last_promotion'];
				$co_mpromotion=$rows_pro[0]['play_main_promotion'];
				$buy_type=$rows_pro[0]['buy_type'];
				$buy_type=$rows_pro[0]['buy_type'];
				$buy_status=$rows_pro[0]['buy_status'];
				$start_baht=$rows_pro[0]['start_baht'];
				$end_baht=$rows_pro[0]['end_baht'];
				if($co_lpromotion=='Y'){
					$tbl_temp="trn_promotion_tmp1";
				}else{
					$tbl_temp="trn_tdiary2_sl";
				}
				////*****************************START***********************************************************
				$product_id=parent::getProduct($product_id,$quantity,'');
				if($product_id=='3'){
					//product lock
					$msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง#$tbl_temp";						
				}else if($product_id=='2'){
					//not found product in com_stock_master
					$msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
				}else if($product_id=='1'){
					//not found product in com_product_master
					$msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
				}else{
						//*WR26112014
						$arr_product=parent::browsProduct($product_id);							
						$sql_proh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
						$rows_proh=$this->db->fetchAll($sql_proh);
								if(!empty($rows_proh)){
									//*WR ================= 12022015 check limited ===============
									$unitn=$rows_proh[0]['unitn'];
									if($unitn>0){
										$sql_chk_limit="SELECT SUM(quantity) AS n_limit 
																FROM $tbl_temp 
																	WHERE 
																		`corporation_id`='$this->m_corporation_id' AND 
																		`company_id`='$this->m_company_id'  AND
																		`branch_id`='$this->m_branch_id' AND 
																	    `computer_no` ='$this->m_computer_no' AND 
																	    `computer_ip` ='$this->m_com_ip' AND 
																	    `doc_date` ='$this->doc_date' AND
																		`promo_code`='$promo_code'";
										$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
										$n_curr=$row_chk_limit[0]['n_limit'];
										$n_curr+=$quantity;
										if($n_curr>$unitn){
											$msg="N#ซื้อได้ไม่เกิน $unitn  ชิ้น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
											return $msg;
										}
									}
									//*WR ================= 12022015 check limited ===============
									$compare_price=$rows_proh[0]['compare'];
									if($compare_price=='G'){
										if($seq_pro=='2'){
												$price=$arr_product[0]['price'];
												$sql_cp="SELECT price
																FROM $tbl_temp 
																WHERE 
																			`corporation_id`='$this->m_corporation_id' AND 
																			`company_id`='$this->m_company_id'  AND
																			`branch_id`='$this->m_branch_id' AND 
																		    `computer_no` ='$this->m_computer_no' AND 
																		    `computer_ip` ='$this->m_com_ip' AND 
																			`promo_code`='$promo_code' AND 
																			`doc_date`= '$this->doc_date' 
																ORDER BY seq DESC LIMIT 0,1"; 
												$r2_cp=$this->db->fetchAll($sql_cp);
												if(!empty($r2_cp)){
													if($price>$r2_cp[0]['price']){
														$msg="N#รหัสสินค้า $product_id ราคาต้องน้อยกว่าสินค้าตัวหลัก กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
														return $msg;
														exit();
													}
													
												}
										}//if seq_pro
									}
								}
							//*WR26112014
							////////////////////////////////////////////////////////////
							$m_coupon_percent=$rows_pro[0]['type_discount'];
							$m_coupon_discount=$rows_pro[0]['discount'];
							$sql_chk_product="SELECT * FROM promo_detail 
															WHERE 
																promo_code='$promo_code' AND 
																(product_id='$product_id' OR product_id='ALL') AND 
																'$this->doc_date' BETWEEN start_date AND end_date";
							$r_chk_product=$this->db->fetchAll($sql_chk_product);
							if(empty($r_chk_product)){
								$msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง#$tbl_temp";
							}else{
								$type_discount=$r_chk_product[0]['type_discount'];
								$doc_tp='SL';			
								if(strtoupper($type_discount)=='FREE'){
									$promo_tp='F';
								}else if(strtoupper($type_discount)=='PERCENT'){
									$promo_tp='P';
									$discount_percent='PERCENT';
									$discount=$r_chk_product[0]['discount'];
								}else if(strtoupper($type_discount)=='PRICE1'){
									//*WR27022015 PRICE1 สินค้าต้องการขายราคานี้
									$promo_tp='P';
									$sp_price=$arr_product[0]['price'];
									$discount=$sp_price-$r_chk_product[0]['discount'];
								}else{
									$promo_tp='P';
								}
								if($r_chk_product[0]['discount_member']=='Y'){
									$member_percent1=$member_percent;
								}
								//*WR24032015
								if($rows_pro[0]["type_discount"]=="BAHT"){
									$m_coupon_percent="";
									$m_coupon_discount="";			
									//$discount="";
								}
								$employee_id=$this->user_id;		
								$quantity='1';
								$status_no='00';
								$product_status='';
								$application_id=$promo_code;
								$card_status='';
								$get_point='Y';
								$promo_id='';
								$member_percent2='';
								$co_promo_percent='';
								$coupon_percent=$m_coupon_percent;
								$coupon_discount=$m_coupon_discount;
								$promo_st='';								
								$promo_amt='';
								$promo_amt_type='';
								$check_repeat='';
								$web_promo='N';
								$point1=$point_2used;
								$point2=0;
								$objCashier=new Model_Cashier();
								if($tbl_temp=="trn_tdiary2_sl"){
									$msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
																						$status_no,$product_status,$application_id,$card_status,
																						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
																						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
																						$check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount);
								}else{
									@$objCashier->setCshValTemp($doc_tp,'',$employee_id,$member_no,$product_id,$quantity,
												$status_no,$product_status,'',$card_status,$get_point,
												$promo_id,$discount_percent,$member_percent1,$member_percent2,
												$co_promo_percent,$coupon_percent,$promo_st);
									$msg=$objCashier->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
															$status_no,$product_status,$application_id,$card_status,
															$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
															$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);														
								}
							}
							///////////////////////////////////////////////////////////
						}
				////******************************************************* STOP **********************************************************************
			}
			return $msg."#$tbl_temp";
		}//func
		
		function chkProdGroup($promo_code,$product_id){
			/**
			 * @desc : 05012015 support OPPF300
			 * @reutrn char :status check product in group
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk="SELECT COUNT(*) FROM promo_detail 
							WHERE promo_code='$promo_code' AND (product_id='$product_id' OR product_id='ALL') AND '$this->doc_date' BETWEEN start_date AND end_date";
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk>0){
				return 'Y';
			}else{
				return 'N';
			}
		}//func
		
		function setPdt2Temp($seq_pro,$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent=''){
			/***
			 * @desc
			 * @modify : 20102014
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg="";
			$sql_pro="SELECT * FROM `promo_other` WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_pro=$this->db->fetchAll($sql_pro);
			if(!empty($rows_pro)){
				//*WR24022016
				$play_lst_pro=$rows_pro[0]['play_last_promotion'];
				////start
				$product_id=parent::getProduct($product_id,$quantity,'');
				if($product_id=='3'){
					//product lock
					$msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง";
				}else if($product_id=='2'){
					//not found product in com_stock_master
					$msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง";
				}else if($product_id=='1'){
					//not found product in com_product_master
					$msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง";
				}else{
					//*WR26112014
					$arr_product=parent::browsProduct($product_id);
					$str_tbl_temp=parent::countDiaryTemp();
					$arr_tbl_temp=explode('#',$str_tbl_temp);
					$tbl_temp=$arr_tbl_temp[1];
						
					//*WR24022016
					if($tbl_temp=='N' && $play_lst_pro=='Y'){
						$tbl_temp="trn_promotion_tmp1";
					}else if($tbl_temp=='N'){
						$tbl_temp="trn_tdiary2_sl";
					}
		
					//*WR28012015
					if($promo_code=='OK01160115' && $tbl_temp!='N'){
						$buy_type=$rows_pro[0]['buy_type'];
						$buy_status=$rows_pro[0]['buy_status'];
						$start_baht=$rows_pro[0]['start_baht'];
						$end_baht=$rows_pro[0]['end_baht'];
						if($buy_type=='N' && $buy_status=='L'){
							$price=$arr_product[0]['price'];
							$chk_amount=$quantity*$price;
							if($seq_pro=='1'){
								$chk_net_pdt=$chk_amount*($member_percent/100);
								$chk_net_pdt=$chk_amount-$chk_net_pdt;
							}else if($seq_pro=='2'){
								$chk_net_pdt=$chk_amount*0.5;
							}
		
							$sql_cp="SELECT SUM(net_amt) as curr_net
							FROM $tbl_temp
							WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id'  AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no` ='$this->m_computer_no' AND
							`computer_ip` ='$this->m_com_ip' AND
							`promo_code`='$promo_code' AND
							`doc_date`= '$this->doc_date'";
							$r_cp=$this->db->fetchAll($sql_cp);
							if(!empty($r_cp)){
								$chk_net_curr=$r_cp[0]['curr_net'];
								$chk_net_last=$chk_net_pdt+$chk_net_curr;
								//echo $chk_net_last."=".$chk_net_pdt."+".$chk_net_curr;
								//echo "#".$chk_net_last.">".$end_baht;
								if($chk_net_last>$end_baht){
									$msg="N#ยอดซื้อสุทธิต้องไม่เกิน $end_baht บาท กรุณาตรวจสอบอีกครั้ง";
									return $msg;
									exit();
								}
									
							}
						}
					}//end if
		
					$sql_proh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
					$rows_proh=$this->db->fetchAll($sql_proh);
					if(!empty($rows_proh)){
						//*WR ================= 12022015 check limited ===============
						$unitn=$rows_proh[0]['unitn'];
						if($unitn>0){
							$sql_chk_limit="SELECT SUM(quantity) AS n_limit
							FROM trn_tdiary2_sl
							WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id'  AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no` ='$this->m_computer_no' AND
							`computer_ip` ='$this->m_com_ip' AND
							`doc_date` ='$this->doc_date' AND
							`promo_code`='$promo_code'";
							$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
							$n_curr=$row_chk_limit[0]['n_limit'];
							$n_curr+=$quantity;
							if($n_curr>$unitn){
								$msg="N#ซื้อได้ไม่เกิน $unitn  ชิ้น กรุณาตรวจสอบอีกครั้ง";
								return $msg;
							}
						}
						//*WR ================= 12022015 check limited ===============
						$compare_price=$rows_proh[0]['compare'];
						if($compare_price=='G'){
							if($seq_pro=='2'){
								$price=$arr_product[0]['price'];
								$sql_cp="SELECT price
								FROM $tbl_temp
								WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id'  AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`promo_code`='$promo_code' AND
								`doc_date`= '$this->doc_date'
								ORDER BY seq DESC LIMIT 0,1";
								$r2_cp=$this->db->fetchAll($sql_cp);
								if(!empty($r2_cp)){
									//echo $price.">".$r2_cp[0]['price'];
									if($price>$r2_cp[0]['price']){
										$msg="N#รหัสสินค้า $product_id ราคาต้องน้อยกว่าสินค้าตัวหลัก กรุณาตรวจสอบอีกครั้ง";
										return $msg;
										exit();
									}
		
								}
							}//if seq_pro
						}
					}
					//*WR26112014
		
					////////////////////////////////////////////////////////////
					$m_coupon_percent=$rows_pro[0]['type_discount'];
					$m_coupon_discount=$rows_pro[0]['discount'];
					$sql_chk_product="SELECT * FROM promo_detail
					WHERE
					promo_code='$promo_code' AND
					(product_id='$product_id' OR product_id='ALL') AND
					seq_pro='$seq_pro' AND
					'$this->doc_date' BETWEEN start_date AND end_date";
					$r_chk_product=$this->db->fetchAll($sql_chk_product);
					if(empty($r_chk_product)){
						$msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง";
					}else{
						$type_discount=$r_chk_product[0]['type_discount'];
						$doc_tp='SL';
						if(strtoupper($type_discount)=='FREE'){
							$promo_tp='F';
						}else if(strtoupper($type_discount)=='PERCENT'){
							$promo_tp='P';
							$discount_percent='PERCENT';
							$discount=$r_chk_product[0]['discount'];
						}else if(strtoupper($type_discount)=='PRICE1'){
							//*WR27022015 PRICE1 สินค้าต้องการขายราคานี้
							$promo_tp='P';
							$sp_price=$arr_product[0]['price'];
							$discount=$sp_price-$r_chk_product[0]['discount'];
						}else{
							$promo_tp='P';
						}
						if($r_chk_product[0]['discount_member']=='Y'){
							$member_percent1=$member_percent;
						}
						$employee_id=$this->user_id;
						$quantity='1';
						$status_no='00';
						$product_status='';
						$application_id=$promo_code;
						$card_status='';
						$get_point='Y';
						$promo_id='';
						$member_percent2='';
						$co_promo_percent='';
						$coupon_percent=$m_coupon_percent;//*WR16102014
						$coupon_discount=$m_coupon_discount;//*WR16102014
						$promo_st='';
						$promo_amt='';
						$promo_amt_type='';
						$check_repeat='';
						$web_promo='N';
						$point1=$point_2used;
						$point2=0;
						$objCashier=new Model_Cashier();
						$msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
								$status_no,$product_status,$application_id,$card_status,
								$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
								$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
								$check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount);
						//end add product to redeem point
					}
					///////////////////////////////////////////////////////////
				}
				////stop
			}
			return $msg;
		}//func
		
		function setPdt2Temp25022016($seq_pro,$member_no,$status_no,$promo_code,$product_id,$quantity,$member_percent=''){
			/***
			 * @desc
			 * @modify : 20102014
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg="";
			$sql_pro="SELECT * FROM `promo_other` WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_pro=$this->db->fetchAll($sql_pro);
			if(!empty($rows_pro)){
				////start
				$product_id=parent::getProduct($product_id,$quantity,'');
				if($product_id=='3'){
					//product lock
					$msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง";						
				}else if($product_id=='2'){
					//not found product in com_stock_master
					$msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง";
				}else if($product_id=='1'){
					//not found product in com_product_master
					$msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง";
				}else{
							//*WR26112014
							$arr_product=parent::browsProduct($product_id);	
							$str_tbl_temp=parent::countDiaryTemp();
							$arr_tbl_temp=explode('#',$str_tbl_temp);
							$tbl_temp=$arr_tbl_temp[1];
							
							//*WR28012015
							if($promo_code=='OK01160115' && $tbl_temp!='N'){
									$buy_type=$rows_pro[0]['buy_type'];
									$buy_status=$rows_pro[0]['buy_status'];
									$start_baht=$rows_pro[0]['start_baht'];
									$end_baht=$rows_pro[0]['end_baht'];
									if($buy_type=='N' && $buy_status=='L'){
										$price=$arr_product[0]['price'];
										$chk_amount=$quantity*$price;
										if($seq_pro=='1'){
											$chk_net_pdt=$chk_amount*($member_percent/100);
											$chk_net_pdt=$chk_amount-$chk_net_pdt;
										}else if($seq_pro=='2'){
											$chk_net_pdt=$chk_amount*0.5;
										}
										
										$sql_cp="SELECT SUM(net_amt) as curr_net
														FROM $tbl_temp 
														WHERE 
																	`corporation_id`='$this->m_corporation_id' AND 
																	`company_id`='$this->m_company_id'  AND
																	`branch_id`='$this->m_branch_id' AND 
																    `computer_no` ='$this->m_computer_no' AND 
																    `computer_ip` ='$this->m_com_ip' AND 
																	`promo_code`='$promo_code' AND 
																	`doc_date`= '$this->doc_date'";
										$r_cp=$this->db->fetchAll($sql_cp);
										if(!empty($r_cp)){
											$chk_net_curr=$r_cp[0]['curr_net'];
											$chk_net_last=$chk_net_pdt+$chk_net_curr;
											//echo $chk_net_last."=".$chk_net_pdt."+".$chk_net_curr;
											//echo "#".$chk_net_last.">".$end_baht;
											if($chk_net_last>$end_baht){
												$msg="N#ยอดซื้อสุทธิต้องไม่เกิน $end_baht บาท กรุณาตรวจสอบอีกครั้ง";
												return $msg;
												exit();
											}
											
										}
									}
							}//end if							
							
							$sql_proh="SELECT* FROM promo_head WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
							$rows_proh=$this->db->fetchAll($sql_proh);
							if(!empty($rows_proh)){
								//*WR ================= 12022015 check limited ===============
								$unitn=$rows_proh[0]['unitn'];
								if($unitn>0){
									$sql_chk_limit="SELECT SUM(quantity) AS n_limit 
															FROM trn_tdiary2_sl 
																WHERE 
																	`corporation_id`='$this->m_corporation_id' AND 
																	`company_id`='$this->m_company_id'  AND
																	`branch_id`='$this->m_branch_id' AND 
																    `computer_no` ='$this->m_computer_no' AND 
																    `computer_ip` ='$this->m_com_ip' AND 
																    `doc_date` ='$this->doc_date' AND
																	`promo_code`='$promo_code'";
									$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
									$n_curr=$row_chk_limit[0]['n_limit'];
									$n_curr+=$quantity;
									if($n_curr>$unitn){
										$msg="N#ซื้อได้ไม่เกิน $unitn  ชิ้น กรุณาตรวจสอบอีกครั้ง";
										return $msg;
									}
								}
								//*WR ================= 12022015 check limited ===============
								$compare_price=$rows_proh[0]['compare'];
								if($compare_price=='G'){
									if($seq_pro=='2'){
											$price=$arr_product[0]['price'];
											$sql_cp="SELECT price
															FROM $tbl_temp 
															WHERE 
																		`corporation_id`='$this->m_corporation_id' AND 
																		`company_id`='$this->m_company_id'  AND
																		`branch_id`='$this->m_branch_id' AND 
																	    `computer_no` ='$this->m_computer_no' AND 
																	    `computer_ip` ='$this->m_com_ip' AND 
																		`promo_code`='$promo_code' AND 
																		`doc_date`= '$this->doc_date' 
															ORDER BY seq DESC LIMIT 0,1"; 
											$r2_cp=$this->db->fetchAll($sql_cp);
											if(!empty($r2_cp)){
												//echo $price.">".$r2_cp[0]['price'];
												if($price>$r2_cp[0]['price']){
													$msg="N#รหัสสินค้า $product_id ราคาต้องน้อยกว่าสินค้าตัวหลัก กรุณาตรวจสอบอีกครั้ง";
													return $msg;
													exit();
												}
												
											}
									}//if seq_pro
								}
							}
							//*WR26112014
					
							////////////////////////////////////////////////////////////
							$m_coupon_percent=$rows_pro[0]['type_discount'];
							$m_coupon_discount=$rows_pro[0]['discount'];												
							$sql_chk_product="SELECT * FROM promo_detail 
															WHERE 
																promo_code='$promo_code' AND 
																(product_id='$product_id' OR product_id='ALL') AND 
																seq_pro='$seq_pro' AND 
																'$this->doc_date' BETWEEN start_date AND end_date";
							$r_chk_product=$this->db->fetchAll($sql_chk_product);
							if(empty($r_chk_product)){
								$msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง";
							}else{
								$type_discount=$r_chk_product[0]['type_discount'];
								$doc_tp='SL';			
								if(strtoupper($type_discount)=='FREE'){
									$promo_tp='F';
								}else if(strtoupper($type_discount)=='PERCENT'){
									$promo_tp='P';
									$discount_percent='PERCENT';
									$discount=$r_chk_product[0]['discount'];
								}else if(strtoupper($type_discount)=='PRICE1'){
									//*WR27022015 PRICE1 สินค้าต้องการขายราคานี้
									$promo_tp='P';
									$sp_price=$arr_product[0]['price'];
									$discount=$sp_price-$r_chk_product[0]['discount'];
								}else{
									$promo_tp='P';
								}
								
								//print_r($r_chk_product);
								
								if($r_chk_product[0]['discount_member']=='Y'){
									$member_percent1=$member_percent;
								}
								
								$employee_id=$this->user_id;		
								$quantity='1';
								$status_no='00';
								$product_status='';
								$application_id=$promo_code;
								$card_status='';
								$get_point='Y';
								$promo_id='';
								//$discount='';
								//$discount_percent='';
								//$member_percent1='';
								$member_percent2='';
								$co_promo_percent='';
								$coupon_percent=$m_coupon_percent;//*WR16102014
								$coupon_discount=$m_coupon_discount;//*WR16102014
								$promo_st='';								
								$promo_amt='';
								$promo_amt_type='';
								$check_repeat='';
								$web_promo='N';
								$point1=$point_2used;
								$point2=0;
								$objCashier=new Model_Cashier();
								$msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
																						$status_no,$product_status,$application_id,$card_status,
																						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
																						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
																						$check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount);
								//end add product to redeem point 
							}
							///////////////////////////////////////////////////////////
						}
				////stop
			}
			return $msg;
		}//func
				
		function selLstPromotion($promo_code,$member_no,$ops_day){
			/**
			 * @desc
			 * @create 25022016
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$str_tbl_temp=parent::countDiaryTemp();
			$arr_tbl_temp=explode('#',$str_tbl_temp);
			$tbl_temp=$arr_tbl_temp[1];
			$sql_trans="SELECT SUM(amount) AS amount,SUM(net_amt) AS net_amt
			FROM $tbl_temp
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id'  AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no` ='$this->m_computer_no' AND
			`computer_ip` ='$this->m_com_ip' AND
			`doc_date`= '$this->doc_date'";
			$rows_trans=$this->db->fetchAll($sql_trans);
			if(!empty($rows_trans)){
				$net_amt=$rows_trans[0]['net_amt'];
				$amount=$rows_trans[0]['amount'];
			}
				
			$arr_promo=array();
			$sql_chk="SELECT promo_play FROM promo_check WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			//echo $sql_chk;
			$rows_chk=$this->db->fetchAll($sql_chk);
			if(!empty($rows_chk)){
				$i=0;
				foreach($rows_chk as $data){
					$promo_code_play=$data['promo_play'];
					$sql_prob="SELECT * FROM `promo_branch`
					WHERE promo_code='$promo_code_play'  AND '$this->doc_date' BETWEEN start_date AND end_date";
					$rows_prob=$this->db->fetchAll($sql_prob);
					if(!empty($rows_prob)){
						$sql_proh="SELECT * FROM `promo_head`
						WHERE promo_code='$promo_code_play'  AND '$this->doc_date' BETWEEN start_date AND end_date";
						$rows_proh=$this->db->fetchAll($sql_proh);
						if(!empty($rows_proh)){
							
							//---- 05082016 START ตรวจสอบยอดซื้อถึงป่าว -----								
							$unitn=$rows_proh[0]['unitn'];
							$promo_price=$rows_proh[0]['promo_price'];
							$process=$rows_proh[0]['process'];
							$promo_amt=$rows_proh[0]['promo_amt'];
							$sql_chk_limit2="SELECT SUM(net_amt) AS sum_net_amt,SUM(amount) AS sum_amount
							FROM $tbl_temp
							WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id'  AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no` ='$this->m_computer_no' AND
							`computer_ip` ='$this->m_com_ip' AND
							`doc_date` ='$this->doc_date'";
							$row_limit_lastpro=$this->db->fetchAll($sql_chk_limit2);
							if(!empty($row_limit_lastpro)){
								$sum_net_amt=$row_limit_lastpro[0]['sum_net_amt'];
								$sum_amount=$row_limit_lastpro[0]['sum_amount'];
							}else{
								$sum_net_amt=0;
								$sum_amount=0;
							}
							if($process=='C'){
								//C=ซื้อครบทุกๆ,F=Fix ที่ unitn ที่กำหนด
								if($promo_price=='N'){
									$unitn=$unitn * floor($sum_net_amt/$promo_amt); 
								}else if($promo_price=='G'){
									$unitn=$unitn* floor($sum_amount/$promo_amt);
								}
									
								if($unitn<1){
									return $arr_promo;
								}
							}
							//------ 05082016 END ตรวจสอบยอดซื้อถึงป่าว --------
							
							$chk_net='N';
							$promo_amt_type=$rows_proh[0]['promo_amt_type'];
							$promo_amt=$rows_proh[0]['promo_amt'];
							$promo_price =$rows_proh[0]['promo_price'];//*WR26082015 for support LINE
							if($promo_amt_type=='N' && $net_amt >= $promo_amt){
								$chk_net='Y';
							}else if($promo_price=='N' && $net_amt >= $promo_amt){
								//*WR26082015 for support LINE
								$chk_net='Y';
							}else if($promo_amt_type=='G' && $amount >= $promo_amt){
								$chk_net='Y';
							}
							
							if($chk_net=='Y'){
								$arr_promo[$i]['promo_code']=$rows_proh[0]['promo_code'];
								$arr_promo[$i]['promo_des']=$rows_proh[0]['promo_des'];
								$arr_promo[$i]['unitn']=$rows_proh[0]['unitn'];
							}
							$i++;
						}
					}
				}//foreach
			}//if
			return $arr_promo;
		}//func
				
		function selCoPromotion($promo_code,$net_amt,$amount,$member_no='',$ops_day='',$promo_tp='',$bday=''){
			/***
			 * @desc 22072014
			 * * for table promo_check co promotion เล่นท้ายบิล
			 * * create 22072014
			 * * modify 01092014 FSTPCH02
			 * * modify 24122014 append member_no,ops_day
			 * * modify 03012017 append promo_tp,bday
			 * @return array of co promotion
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//*WR02072015
			$str_tbl_temp=parent::countDiaryTemp();
			$arr_tbl_temp=explode('#',$str_tbl_temp);
			$tbl_temp=$arr_tbl_temp[1];
			$sql_trans="SELECT SUM(amount) AS amount,SUM(net_amt) AS net_amt 
									FROM $tbl_temp 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND 
										`company_id`='$this->m_company_id'  AND
										`branch_id`='$this->m_branch_id' AND 
									    `computer_no` ='$this->m_computer_no' AND 
									    `computer_ip` ='$this->m_com_ip' AND 
										`doc_date`= '$this->doc_date'";
			$rows_trans=$this->db->fetchAll($sql_trans);
			if(!empty($rows_trans)){
				$net_amt=$rows_trans[0]['net_amt'];
				$amount=$rows_trans[0]['amount'];
			}
			//*WR24122014
			if($promo_code=='OX02691014'){
				//1520144261437
				/*
				if(substr($member_no,0,4)=='1120'){
					$promo_code="OX02691014TH";
				}else if(substr($member_no,0,4)=='1520'){
					$promo_code="OX02691014TU";
				}
				*/				
				if($promo_code=='OX02691014'){
					if(substr($ops_day,0,2)=='TH'){
						$promo_code="OX02691014TH";
					}else if(substr($ops_day,0,2)=='TU'){
						$promo_code="OX02691014TU";
					}
				}				
			}//if			
			
			$arr_promo=array();
			//*WR03012017 for new birth
			$m_bday='';
			if($bday!='' && $promo_tp=='NEWBIRTH'){
				$arr_bday=explode('-',$bday);
				$m_bday=$arr_bday[1];
			}
			if($m_bday=='01'){
				$sql_chk="SELECT promo_play FROM promo_check WHERE promo_st='F' AND  promo_code='$promo_code' AND end_date ='2018-01-31'";
			}else if($m_bday=='12'){
				$sql_chk="SELECT promo_play FROM promo_check WHERE promo_st='F' AND  promo_code='$promo_code' AND end_date ='2017-01-31'";
			}else{
				$sql_chk="SELECT promo_play FROM promo_check WHERE promo_st='F' AND  promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			}
			//$sql_chk="SELECT promo_play FROM promo_check WHERE promo_st='F' AND  promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";			
			$rows_chk=$this->db->fetchAll($sql_chk);
			if(!empty($rows_chk)){
				$i=0;
				foreach($rows_chk as $data){
					$promo_code_play=$data['promo_play'];
					$sql_prob="SELECT * FROM `promo_branch` 
										WHERE promo_code='$promo_code_play'  AND '$this->doc_date' BETWEEN start_date AND end_date";
					$rows_prob=$this->db->fetchAll($sql_prob);
					if(!empty($rows_prob)){
						$sql_proh="SELECT * FROM `promo_head` 
										WHERE promo_code='$promo_code_play'  AND '$this->doc_date' BETWEEN start_date AND end_date";
						$rows_proh=$this->db->fetchAll($sql_proh);
						if(!empty($rows_proh)){
							$chk_net='N';
							$promo_amt_type=$rows_proh[0]['promo_amt_type'];
							$promo_amt=$rows_proh[0]['promo_amt'];
							//$promo_price =$rows_proh[0]['promo_amt'];//*WR26082015 for support LINE
							$promo_price =$rows_proh[0]['promo_price'];//*WR30082016 for support pro lucky draw
							if($promo_amt_type=='N' && $net_amt >= $promo_amt){
								$chk_net='Y';
							}else if($promo_price=='N' && $net_amt >= $promo_amt){
								//*WR26082015 for support LINE
								$chk_net='Y';
							}else if($promo_amt_type=='G' && $amount >= $promo_amt){
								$chk_net='Y';
							}else if($promo_amt_type==''){
								$chk_net='Y';
							}
							if($chk_net=='Y'){								
								$arr_promo[$i]['promo_code']=$rows_proh[0]['promo_code'];
								$arr_promo[$i]['promo_des']=$rows_proh[0]['promo_des'];
								$arr_promo[$i]['unitn']=$rows_proh[0]['unitn'];
							}
							$i++;
						}
					}
				}//foreach
			}//if
			return $arr_promo;
		}//func
		
		function setPdtMCoupon($member_no,$status_no,$promo_code,$product_id,$quantity='1',$member_percent=''){
			/**
			 * @desc 16102014
			 * * for support redeem point by discount
			 * * modify : 20112014 get config from table promo_head,promo_detail
			 * * modify : 12012015 append argument $member_percent,member_discount
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg="";
			$sql_pro="SELECT * FROM `promo_other` WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows_pro=$this->db->fetchAll($sql_pro);
			if(!empty($rows_pro)){
				////start
						$product_id=parent::getProduct($product_id,$quantity,'');
						//*WR26082015 for support LINE coupon
						$arr_product=parent::browsProduct($product_id);
						$item_price=$arr_product[0]['price'];
						$item_amount=$item_price * $quantity;
						if($product_id=='3'){
							//product lock
							$msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง";						
						}else if($product_id=='2'){
							//not found product in com_stock_master
							$msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง";
						}else if($product_id=='1'){
							//not found product in com_product_master
							$msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง";
						}else{
							
							////////////////////////////////////////////////////////////
							$m_coupon_percent=$rows_pro[0]['type_discount'];
							$m_coupon_discount=$rows_pro[0]['discount'];
							
							//*WR13012015
							$sql_proh="SELECT * FROM promo_head 
															WHERE 
																promo_code='$promo_code' AND
																'$this->doc_date' BETWEEN start_date AND end_date";
							$rows_proh=$this->db->fetchAll($sql_proh);
							if(!empty($rows_proh)){
								if($rows_proh[0]['promo_amt']>0){
									$m_coupon_discount='';
								}
								
							//*WR26082015 for support LINE coupon
								if($rows_pro[0]['promo_tp']=='LINE'){
									$tbl_temp="trn_promotion_tmp1";
								}else{
									$tbl_temp="trn_tdiary2_sl";
								}
								//*WR26082015 check วงเงิน
								$sql_chkamt="SELECT SUM(amount) as s_amt FROM $tbl_temp 
														WHERE
															`corporation_id`='$this->m_corporation_id' AND 
															`company_id`='$this->m_company_id'  AND
															`branch_id`='$this->m_branch_id' AND 
														    `computer_no` ='$this->m_computer_no' AND 
														    `computer_ip` ='$this->m_com_ip' AND 
														    `doc_date` ='$this->doc_date' AND
															`promo_code`='$promo_code'";
								$row_chkamt=$this->db->fetchAll($sql_chkamt);
								$c_amt=$row_chkamt[0]['s_amt'];
								$sum_amt=$item_amount+$c_amt;
								$buy_type=$rows_pro[0]['buy_type'];
								$buy_status=$rows_pro[0]['buy_status'];
								$start_baht=$rows_pro[0]['start_baht'];
								$end_baht=$rows_pro[0]['end_baht'];
								if($buy_type=='G'){
									if($buy_status=='L'){
											//ต้องไม่เกิน
											if($sum_amt>$end_baht){
												//flag Y or N#buy_status L or G # new amount											
												$msg="N#ซื้อได้ไม่เกิน $end_baht  บาท กรุณาตรวจสอบอีกครั้ง";
												return $msg;
											}
									}else if($buy_status=='G'){
											//ต้องไม่น้อยกว่า
											if($sum_amt<$end_baht){
												$msg="N#ยอดซื้อน้อยกว่า $end_baht  บาท กรุณาตรวจสอบอีกครั้ง";
												return $msg;
											}
									}
								}else if($buy_type=='N'){
									//Net
								}
								
								//*WR20012015 check play limit quantity
								$unitn=$rows_proh[0]['unitn'];
								if($unitn>0){
									$sql_chk_limit="SELECT SUM(quantity) AS n_limit FROM $tbl_temp 
																WHERE 
																	`corporation_id`='$this->m_corporation_id' AND 
																	`company_id`='$this->m_company_id'  AND
																	`branch_id`='$this->m_branch_id' AND 
																    `computer_no` ='$this->m_computer_no' AND 
																    `computer_ip` ='$this->m_com_ip' AND 
																    `doc_date` ='$this->doc_date' AND
																	`promo_code`='$promo_code'";
									$row_chk_limit=$this->db->fetchAll($sql_chk_limit);
									$n_curr=$row_chk_limit[0]['n_limit'];
									$n_curr+=$quantity;
									if($n_curr>$unitn){
										$msg="N#ซื้อได้ไม่เกิน $unitn  ชิ้น กณาตรวจสอบอีกครั้ง";
										return $msg;
									}
								}
							}				
							
							//*WR20012015
							$sql_chk_product="SELECT * FROM promo_detail 
															WHERE 
																promo_code='$promo_code' AND 
																(product_id='$product_id' OR product_id='ALL') AND 
																'$this->doc_date' BETWEEN start_date AND end_date";
							$r_chk_product=$this->db->fetchAll($sql_chk_product);
							if(empty($r_chk_product)){
								$msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง";
							}else{								
								$type_discount=$r_chk_product[0]['type_discount'];
								$doc_tp='SL';			
								if(strtoupper($type_discount)=='FREE'){
									$promo_tp='F';
								}else{
									$promo_tp='P';
								}	
								
								//*WR12012015
								if($r_chk_product[0]['discount_member']=='Y'){
									$member_percent1=$member_percent;
								}else{
									$member_percent1='';
								}
																
								$employee_id=$this->user_id;		
								//$quantity='1';
								$status_no='00';
								$product_status='';
								$application_id=$promo_code;
								$card_status='';
								$get_point='Y';
								$promo_id='';
								$discount='';
								$discount_percent='';
								//$member_percent1='';
								$member_percent2='';
								$co_promo_percent='';								
								if($m_coupon_percent=="PERCENT"){
									$coupon_percent=$m_coupon_percent;
									$coupon_discount=$m_coupon_discount;
								}else{
									$coupon_percent='';
									$coupon_discount='';
								}
								$promo_st='';
								$promo_amt='';
								$promo_amt_type='';
								$check_repeat='';
								$web_promo='N';
								$point1=$point_2used;
								$point2=0;
								$objCashier=new Model_Cashier();
								if($rows_pro[0]['promo_tp']=='LINE'){
									@$objCashier->setCshValTemp($doc_tp,'',$employee_id,$member_no,$product_id,$quantity,
											$status_no,$product_status,'',$card_status,$get_point,
											$promo_id,$discount_percent,$member_percent1,$member_percent2,
											$co_promo_percent,$coupon_percent,$promo_st);

									$msg=$objCashier->setPmtTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
																						$status_no,$product_status,$application_id,$card_status,
																						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
																						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
																						$check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount);
								}else{
									$msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
																						$status_no,$product_status,$application_id,$card_status,
																						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
																						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,
																						$check_repeat,$web_promo,$point1,$point2,$discount,$coupon_discount);
								}
								
								//end add product to redeem point 
								
							}
							///////////////////////////////////////////////////////////
						}
				////stop
			}
			return $msg;
		}//func
		
		function setPdtRedeemPoint($member_no,$member_point,$status_no,$promo_code,$product_id,$quantity){
			/**
			 * @desc 22072014
			 * * for support redeem point by discount
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_p1="SELECT* FROM promo_point1_detail WHERE promo_id='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_p1=$this->db->fetchAll($sql_p1);
			if(!empty($row_p1)){
				$type_discount=$row_p1[0]['type_discount'];
				$p_discount=$row_p1[0]['discount'];
				$point_2used=$row_p1[0]['point'];
				if($type_discount=='DISCOUNT'){
					$sql_point_used="SELECT SUM(point1) AS point_used FROM trn_tdiary2_sl 
						WHERE 
							`corporation_id`='$this->m_corporation_id' AND 
							`company_id`='$this->m_company_id'  AND
							`branch_id`='$this->m_branch_id' AND 
						    `computer_no` ='$this->m_computer_no' AND 
						    `computer_ip` ='$this->m_com_ip' AND 
						    `doc_date` ='$this->doc_date' AND
						    `member_no` ='$member_no'";
					$point_used=$this->db->fetchOne($sql_point_used);
					$point_used=$point_used+$point_2used;
					if($point_used>$member_point){
						$msg="N#คะแนนไม่พอ กรุณาตรวจสอบอีกครั้ง";
					}else{
						
						$product_id=parent::getProduct($product_id,$quantity,'');
						if($product_id=='3'){
							//product lock
							$msg="N#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง";						
						}else if($product_id=='2'){
							//not found product in com_stock_master
							$msg="N#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง";
						}else if($product_id=='1'){
							//not found product in com_product_master
							$msg="N#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง";
						}else{
							
							////////////////////////////////////////////////////////////
							$sql_chk_product="SELECT COUNT(*) FROM promo_detail 
															WHERE 
																promo_code='$promo_code' AND 
																product_id='$product_id' AND 
																'$this->doc_date' BETWEEN start_date AND end_date";
							$n_chk_product=$this->db->fetchOne($sql_chk_product);
							if($n_chk_product<1){
								$msg="N#รหัสสินค้า $product_id ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง";
							}else{
								//add product to redeem point
								$doc_tp='SL';						
								$employee_id=$this->user_id;				
								//$product_id=$data['product_id'];
								$quantity='1';
								//$status_no='00';
								$product_status='';
								$application_id=$promo_code;
								$card_status='';
								$get_point='Y';
								$promo_id='';
								$discount=$p_discount;
								$discount_percent='';
								$member_percent1='';
								$member_percent2='';
								$co_promo_percent='';
								$coupon_percent='';
								$promo_st='';
								$promo_tp='P';
								$promo_amt='';
								$promo_amt_type='';
								$check_repeat='';
								$web_promo='N';
								$point1=$point_2used;
								$point2=0;
								$objCashier=new Model_Cashier();
								$msg=$objCashier->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
																						$status_no,$product_status,$application_id,$card_status,
																						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
																						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo,$point1,$point2,$discount);
								
								//end add product to redeem point 
								
							}
							///////////////////////////////////////////////////////////
						}
						
					}
					
				}
				
			}
			
			return $msg;
			
		}//func
		
		function setCshValTemp($member_no,$doc_tp='SL',$status_no='00',$mem_card_info=''){
			/**
			 * @desc 22072014
			 * @modify 21072016
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_clear="DELETE FROM trn_tdiary2_sl_val
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id'  AND
			`branch_id`='$this->m_branch_id' AND
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
			`doc_no` ='$this->m_com_ip',
			`doc_tp` ='$doc_tp',
			`status_no`='$status_no',
			`seq` ='',
			`promo_code` ='$this->promo_code',
			`promo_seq` ='',
			`promo_st` ='',
			`promo_tp` ='',
			`product_id`='',
			`stock_st` ='',
			`price` ='',
			`quantity`='',
			`amount`='',
			`discount`='',
			`member_discount1`='',
			`member_discount2`='',
			`net_amt`='',
			`product_status`='',
			`get_point`='',
			`discount_member`='',
			`cal_amt`='Y',
			`discount_percent`='',
			`member_percent1`='',
			`member_percent2`='',
			`co_promo_percent`='',
			`coupon_percent`='',
			`tax_type`='',
			`computer_no`='$this->m_computer_no',
			`computer_ip`='$this->m_com_ip',
			 `cn_remark`='$mem_card_info',
			`reg_date`='$this->doc_date',
			`reg_time`='$this->doc_time',
			`reg_user`='$this->employee_id'";
			$stmt_insert=$this->db->query($sql_insert2);
		}//func
				
		function chkCouPonIsUsed23032018($member_no,$coupon_code){
			/**
			 * @desc 22072014
			 * @return message check status
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//$sql_chk_format="SELECT COUNT(*) FROM com_special_coupon WHERE '$coupon_code' BETWEEN start_code AND end_code";	
			$sql_chk_format="SELECT COUNT(*) FROM com_special_coupon WHERE ".$coupon_code." BETWEEN start_code AND end_code";
			$n_chk_format=$this->db->fetchOne($sql_chk_format);
			if($n_chk_format>0){
				//check is used
				$objCal=new Model_Calpromotion();
				$res_coupon=$objCal->chk_coupon_code($coupon_code);
				$arr_coupon=explode('@@',$res_coupon);
				if($arr_coupon[0]=='Y'){
					$res_msg="Y#รหัส Coupon $coupon_code \nถูกใช้ไปแล้ว ที่สาขา ".substr($arr_coupon[1],2,4);
				}else{
					$res_msg="N#ท่านสามารถใช้สิทธิ Coupon $coupon_code ได้";
				}
			}else{
				$res_msg="Y#รูปแบบรหัส Coupon ไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง";
			}
			return $res_msg;
		}//func
		
		function chkCouPonIsUsed($member_no,$coupon_code){
		    /**
		     * @desc ตรวจสอบว่า coupon ถูกใช้ไปหรือยัง Local Only
		     * @return message check status
		     */
		    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		    $sql_chk_format="SELECT COUNT(*) FROM com_special_coupon WHERE '$coupon_code' BETWEEN start_code AND end_code";
		    $n_chk_format=$this->db->fetchOne($sql_chk_format);
		    if($n_chk_format>0){
		        $sql_isused="SELECT member_id,doc_date,doc_no,coupon_code 
                                        FROM trn_diary1 
                                            WHERE doc_date>='2018-03-01' AND coupon_code = '$coupon_code' AND flag<>'C'";
		        $r_isused=$this->db->fetchAll($sql_isused);
		        if(!empty($r_isused)){
		            $arr_d=explode("-",$r_isused[0]['doc_date']);
		            $show_date_used=$arr_d[2]."/".$arr_d[1]."/".$arr_d[0];
		            $res_msg="Y#Coupon code  $coupon_code \n was already used on. ".$show_date_used;
		        }else{
		            $res_msg="N#You can use coupon  $coupon_code rights.";
		        }
		    }else{
		        $res_msg="Y#Coupon code format is invalid.";
		    }
		    return $res_msg;
		}//func
		
		function chkCouPonIsUsed28082015($member_no,$coupon_code){
			/**
			 * @desc 22072014 ตรวจสอบว่า coupon ถูกใช้ไปหรือยัง Local Only
			 * @return message check status
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk_format="SELECT COUNT(*) FROM com_special_coupon WHERE '$coupon_code' BETWEEN start_code AND end_code";			
			$n_chk_format=$this->db->fetchOne($sql_chk_format);
			if($n_chk_format>0){
				//*WR01122014
				$sql_isused="SELECT member_id,doc_date,doc_no,coupon_code FROM trn_diary1 WHERE doc_date>='2014-12-01' AND coupon_code = '$coupon_code' AND flag<>'C'";
				$r_isused=$this->db->fetchAll($sql_isused);
				if(!empty($r_isused)){
					$arr_d=explode("-",$r_isused[0]['doc_date']);
					$show_date_used=$arr_d[2]."/".$arr_d[1]."/".$arr_d[0];
					$res_msg="Y#รหัส Coupon $coupon_code \nถูกใช้ไปแล้ว เมื่อวันที่ ".$show_date_used;
//					//check is used
//					$objCal=new Model_Calpromotion();
//					$res_coupon=$objCal->chk_coupon_code($coupon_code);
//					$arr_coupon=explode('@@',$res_coupon);
//					if($arr_coupon[0]=='Y'){
//						$res_msg="Y#รหัส Coupon $coupon_code \nถูกใช้ไปแล้ว ที่สาขา ".substr($arr_coupon[1],2,4);
				}else{
					//check local ว่า member_id ได้รับ coupon ในวันนี้จริง 
					$sql_local="SELECT COUNT(*)
									FROM `trn_diary2` AS a
									INNER JOIN trn_diary1 AS b ON a.doc_no = b.doc_no
									WHERE 
										b.corporation_id='$this->m_corporation_id' AND 
										b.company_id='$this->m_company_id'  AND
										b.branch_id='$this->m_branch_id' AND 
										a.promo_code = 'OX15080814' AND 
										b.member_id = '$member_no' 	AND 
										b.doc_date = '$this->doc_date' AND 
										a.flag <> 'C'";
					$n_local=$this->db->fetchOne($sql_local);
					if($n_local<1){
						//*WR11122014 ลูกค้าต่ออายุ เปลี่ยนบัตร แจ้งบัตรหาย 
							$sql_chkchange="SELECT refer_member_id FROM trn_diary1 
														WHERE 
															doc_date='$this->doc_date' AND
															member_id='$member_no' AND 
															flag<>'C' AND status_no IN('01','05')";
							$r_chkchange=$this->db->fetchAll($sql_chkchange);
							if(!empty($r_chkchange)){
								$member_no=$r_chkchange[0]['refer_member_id'];
							}
						//*WR11122014 ลูกค้าต่ออายุ เปลี่ยนบัตร แจ้งบัตรหาย
					}//end if $n_local
					
					//*WR26112014
					//check local ว่าใช้เกินจำนวนที่ได้รับหรือไม่?เค้ายังมีสิทธิใช้ต่างสาขาหรือไม่?
					$sql_chkused="SELECT COUNT(*) FROM trn_diary1 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id'  AND
											branch_id='$this->m_branch_id' AND 
											member_id = '$member_no' 	AND 
											coupon_code like '2014%' AND
											doc_date='$this->doc_date' AND 
											flag<>'C'";
					$n_chkused=$this->db->fetchOne($sql_chkused);
					
					//check local ว่า member_id ได้รับ coupon ในวันนี้จริง 
					$sql_local="SELECT COUNT(*)
									FROM `trn_diary2` AS a
									INNER JOIN trn_diary1 AS b ON a.doc_no = b.doc_no
									WHERE 
										b.corporation_id='$this->m_corporation_id' AND 
										b.company_id='$this->m_company_id'  AND
										b.branch_id='$this->m_branch_id' AND 
										a.promo_code = 'OX15080814' AND 
										b.member_id = '$member_no' 	AND 
										b.doc_date = '$this->doc_date' AND 
										a.flag <> 'C'";
					$n_local=$this->db->fetchOne($sql_local);
					if($n_local>0){
						$n_chkused=$n_chkused+1;
						if($n_chkused>$n_local){
							$res_msg="Y#ท่านไม่สามารถใช้สิทธิ Coupon มากกว่าสิทธิที่ได้รับจากการจับ Coupon";
						}else{
							$res_msg="N#ท่านสามารถใช้สิทธิ Coupon $coupon_code ได้";
						}
					}else{
						$res_msg="Y#ท่านสามารถใช้สิทธิ Coupon ในวันที่ได้สิทธิจับฉลากเท่านั้น";
					}
					
				}
			}else{
				$res_msg="Y#รูปแบบรหัส Coupon ไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง";
			}
			return $res_msg;
		}//func
		
		
		function chkCoProOtherPro($promo_code,$net_amt,$amount){
			/***
			 * @desc 29072014
			 * ** for table promo_other กลับมาดูการ set promo_check
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$chr_result='N';
			$sql_chk1="SELECT promo_play FROM promo_check WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";			
			$row_chk1=$this->db->fetchAll($sql_chk1);
			if(!empty($row_chk1)){
				$promo_play=$row_chk1[0]['promo_play'];
				$sql_chk2="SELECT COUNT(*) FROM promo_branch WHERE promo_code='$promo_play' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$n_chk=$this->db->fetchOne($sql_chk2);
				if($n_chk>0){
					$sql_pro="SELECT * FROM `promo_other` 
										WHERE promo_code='$promo_code'  AND '$this->doc_date' BETWEEN start_date AND end_date";
					$rows_pro=$this->db->fetchAll($sql_pro);
					if(!empty($rows_pro)){
						$promo_amt=$rows_pro[0]['promo_amt'];
						$promo_amt_type=$rows_pro[0]['promo_amt_type'];
						if($promo_amt_type=='N'){
							if($net_amt>=$promo_amt){
								$chr_result='Y';
							}
						}else if($promo_amt_type=='G'){
							if($amount>=$promo_amt){
								$chr_result='Y';
							}
						}else if($promo_amt<1){
							$chr_result='Y';
						}
					}//if
				}//if
			}//if
			return $chr_result;
		}//func
		
		function setCoPromoProduct($member_no,$promo_code,$product_id,$unitn,$status_no){
			/**
			 * @desc : สำหรับสินค้าแถมฟรีท้ายบิล 
			 * ** create 29072014
			 * ** modify 01092014
			 * ** modify 31082015
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			
			//*WR01092014
			$quantity='1';
			$product_id=parent::getProduct($product_id,$quantity,'');
			//*WR 11052015
			if($product_id=='3'){
				//product lock
				echo "3#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง";		
				return true;				
			}else if($product_id=='2'){
				//not found product in com_stock_master
				echo "2#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง";
				return true;
			}else if($product_id=='1'){
				//not found product in com_product_master
				echo "1#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง";
				return true;
			}
			
			$sql_chk_l="SELECT COUNT(*) FROM promo_detail 
								WHERE 
									promo_code='$promo_code' AND 
									(product_id='$product_id' OR product_id LIKE 'ALL') AND
									'$this->doc_date' BETWEEN start_date AND end_date";
			$n_chk_l=$this->db->fetchOne($sql_chk_l);
			if($n_chk_l<1){
				echo  "4#รายการสินค้า $product_id ไม่ร่วมโปรโมชั่น";
				exit();
			}
			
		/**
		    * WR01012016 OX07081115,OX07091115 
			 * WR20102014 OX07320814,OX07561014
			 * ตรวจสอบให้คีย์สินค้าได้แค่ชิ้นเดียวกรณี first purchase เลือกตัวแถวได้แค่ 1 ชิ้นระหว่างโปร 10000 กับ 500 
			 * และเลือกได้ 1 โปรเท่านั้น
			*/
			if($promo_code=='OX07021114' || $promo_code=='OX07031114' || $promo_code=='OX07081115' ||  $promo_code=='OX07091115'){
				$sql_chk_l2="SELECT COUNT(*) FROM trn_promotion_tmp1 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no` ='$this->m_computer_no' AND
										`computer_ip` ='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND 
										`promo_code` IN('OX07021114','OX07031114','OX07081115','OX07091115')";
				$n_chk_l2=$this->db->fetchOne($sql_chk_l2);
				if($n_chk_l2>0){
					echo  "5#0#trn_promotion_tmp1";
					exit();
				}
			}
			
			$unitn=intval($unitn);//ห้ามเกิน
			$doc_tp='SL';
			//$status_no='00';
			$promo_tp='F';
			$employee_id=$this->employee_id;
			$member_no='';			
			$quantity='1';			
			$check_repeat='Y';
			$promo_st='FREE';
			$price='0.00';
			$objCsh=new Model_Cashier();	
			$objCsh->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
										$status_no,$product_status,$application_id,$card_status,
										$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
										$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat,$unitn);
		}//func
		
		function setCoPromoProduct31082015($member_no,$promo_code,$product_id,$unitn,$status_no){
			/**
			 * @desc : สำหรับสินค้าแถมฟรีท้ายบิล 
			 * ** create 29072014
			 * ** modify 01092014
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			
			//*WR01092014
			$quantity='1';
			$product_id=parent::getProduct($product_id,$quantity,'');
			//*WR 11052015
			if($product_id=='3'){
				//product lock
				echo "3#รหัสสินค้า $product_id นี้ห้ามขาย กรุณาตรวจสอบอีกครั้ง";		
				return true;				
			}else if($product_id=='2'){
				//not found product in com_stock_master
				echo "2#รหัสสินค้า $product_id ไม่พบในสต๊อกหรือสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง";
				return true;
			}else if($product_id=='1'){
				//not found product in com_product_master
				echo "1#รหัสสินค้า $product_id ไม่พบในทะเบียน กรุณาตรวจสอบอีกครั้ง";
				return true;
			}
			
			$sql_chk_l="SELECT COUNT(*) FROM promo_detail 
								WHERE 
									promo_code='$promo_code' AND 
									(product_id='$product_id' OR product_id LIKE 'ALL') AND
									'$this->doc_date' BETWEEN start_date AND end_date";
			$n_chk_l=$this->db->fetchOne($sql_chk_l);
			if($n_chk_l<1){
				echo  "4#รายการสินค้า $product_id ไม่ร่วมโปรโมชั่น";
				exit();
			}
			
//			$sql_chk_l="SELECT COUNT(*) FROM promo_detail 
//								WHERE promo_code='$promo_code' AND product_id='$product_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
//			$n_chk_l=$this->db->fetchOne($sql_chk_l);
//			if($n_chk_l<1){
//				echo  "4#รายการสินค้า $product_id ไม่ร่วมโปรโมชั่น";
//				exit();
//			}
			
		/**
			 * WR20102014 
			 * ตรวจสอบให้คีย์สินค้าได้แค่ชิ้นเดียวกรณี first purchase เลือกตัวแถวได้แค่ 1 ชิ้นระหว่างโปร 10000 OX07320814  กับ 500 OX07561014
			 * และเลือกได้ 1 โปรเท่านั้น
			*/
			if($promo_code=='OX07320814' || $promo_code=='OX07561014'){
				$sql_chk_l2="SELECT COUNT(*) FROM trn_promotion_tmp1 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no` ='$this->m_computer_no' AND
										`computer_ip` ='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND 
										`promo_code` IN('OX07320814','OX07561014') AND 
										`product_id`='$product_id'";
				$n_chk_l2=$this->db->fetchOne($sql_chk_l2);
				if($n_chk_l2>0){
					echo  "5#0#trn_promotion_tmp1";
					exit();
				}
			}
			
			$unitn=intval($unitn);//ห้ามเกิน
			$doc_tp='SL';
			//$status_no='00';
			$promo_tp='F';
			$employee_id=$this->employee_id;
			$member_no='';			
			$quantity='1';			
			$check_repeat='Y';
			$promo_st='FREE';
			$price='0.00';
			$objCsh=new Model_Cashier();	
			$objCsh->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
										$status_no,$product_status,$application_id,$card_status,
										$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
										$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat,$unitn);
		}//func
		
		function setFreeProductOtherPro($member_no,$promo_code,$product_id,$unitn){
			/**
			 * @desc : modify 29072014
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk_l="SELECT COUNT(*) FROM promo_detail 
								WHERE promo_code='$promo_code' AND product_id='$product_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$n_chk_l=$this->db->fetchOne($sql_chk_l);
			if($n_chk_l<1){
				return "N#รายการสินค้า $product_id ไม่ร่วมโปรโมชั่น";
			}
			
			$unitn=intval($unitn);
			if($unitn>=1){
				$sql_chk="SELECT SUM(quantity) as qty_total FROM trn_tdiary2_sl 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date' AND 
								`promo_code` ='$promo_code'";
				$n_chk=$this->db->fetchOne($sql_chk);
				if($n_chk>0){
					return "N#จำกัดรายการสินค้าไม่เกิน $unitn ชิ้น";
				}
			}
			$doc_tp='SL';
			$status_no='00';
			$promo_tp='F';
			$employee_id=$this->employee_id;
			$member_no='';			
			$quantity='1';			
			$check_repeat='N';
			$promo_st='';
			$price='0.00';
			$res_insert=$this->setCshTemp($promo_code,$promo_st,$employee_id,$member_no,$product_id,$quantity,$price,$doc_tp,$status_no);			
			return $res_insert;
		}//func
		
		function chkLastOtherPro($promo_code,$net_amt,$amount){
			/**
			 * @desc 29072014
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$row_h=array();
			$sql_chk="SELECT promo_play FROM promo_check WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_chk=$this->db->fetchAll($sql_chk);
			if(!empty($row_chk)){
				$co_promo_code=$row_chk[0]['promo_play'];
				$sql_h="SELECT* FROM promo_head WHERE promo_code='$co_promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$row_h=$this->db->fetchAll($sql_h);					
			}
			return $row_h;
		}//func
		
		function getListProRedeemPiont($promo_code){
			/**
			 * @desc 
			 * @create 22072014
			 * @modify 03092014 play 16092014 -15102014
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_ph1="SELECT* FROM promo_point1_detail WHERE promo_code='$promo_code'";
			$rows_ph1=$this->db->fetchAll($sql_ph1);
			$arr_pro=array();			
			if(!empty($rows_ph1)){
				$i=0;
				foreach($rows_ph1 as $data){
					$promo_code_chk=$data['promo_id'];
					$sql_chk="SELECT COUNT(*) FROM promo_branch WHERE promo_code='$promo_code_chk' AND '$this->doc_date' BETWEEN start_date AND end_date";					
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk>0){
						$arr_pro[$i]['promo_code']=$data['promo_code'];
						$arr_pro[$i]['promo_id']=$data['promo_id'];
						$arr_pro[$i]['promo_des']=$data['promo_des'];
						$arr_pro[$i]['start_date']=$data['start_date'];
						$arr_pro[$i]['end_date']=$data['end_date'];
						$arr_pro[$i]['start_baht']=$data['start_baht'];
						$arr_pro[$i]['end_baht']=$data['end_baht'];
						$arr_pro[$i]['cal_amount']=$data['cal_amount'];
						$arr_pro[$i]['start_percent']=$data['start_percent'];
						$arr_pro[$i]['end_percent']=$data['end_percent'];
						$arr_pro[$i]['type_discount']=$data['type_discount'];
						$arr_pro[$i]['discount']=$data['discount'];
						$arr_pro[$i]['point']=$data['point'];
						$i++;
					}					
				}
			}
			return $arr_pro;
		}//func
		
		function getCouponDiscount($netvt,$promo_code){
			/**
			 * @desc ส่วนลดคูปองที่หัว trn_diary1
			 * @create : 23062014
			 * @modify : 14012015 change com_application_coupon to promo_other
			 * @modify : 24032015 support L promotion
			 * @modify : 08042015 support M promotion
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$discount=0;
			$str_tbl_temp=parent::countDiaryTemp();
			$arr_tbl_temp=explode('#',$str_tbl_temp);
			$tbl_temp=$arr_tbl_temp[1];
			$sql_chk="SELECT COUNT(*) FROM $tbl_temp 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";//AND `promo_code` ='$promo_code'
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk>0){
					$sql_promo="SELECT 
											type_discount,discount
										FROM
											`promo_other`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											promo_code='$promo_code' AND
											promo_tp IN('MCOUPON','COUPON','LINE','MCS') AND 
											type_discount = 'BAHT' AND 
											'$netvt' BETWEEN start_baht AND end_baht ";
					$row_promo=$this->db->fetchAll($sql_promo);
					if(count($row_promo)>0){
						$discount=$row_promo[0]['discount'];
					}
			}
			return $discount;
		}//func
		
		function getCouponDiscount08042015($netvt,$promo_code){
			/**
			 * @desc ส่วนลดคูปองที่หัว trn_diary1
			 * @create : 23062014
			 * @modify : 14012015 change com_application_coupon to promo_other
			 * @modify : 24032015 support M,L promotion
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$discount=0;
			$str_tbl_temp=parent::countDiaryTemp();
			$arr_tbl_temp=explode('#',$str_tbl_temp);
			$tbl_temp=$arr_tbl_temp[1];
			$sql_chk="SELECT COUNT(*) FROM $tbl_temp 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date' AND 
								`promo_code` ='$promo_code'";
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk>0){
					$sql_promo="SELECT 
											type_discount,discount
										FROM
											`promo_other`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											promo_code='$promo_code' AND
											'$netvt' BETWEEN start_baht AND end_baht ";
					$row_promo=$this->db->fetchAll($sql_promo);
					if(count($row_promo)>0){
						$discount=$row_promo[0]['discount'];
					}
			}
			return $discount;
		}//func
		
		function getCouponDiscount31032015($netvt,$promo_code){
			/**
			 * @desc ส่วนลดคูปองที่หัว trn_diary1
			 * @create : 23062014
			 * @modify : 14012015 change com_application_coupon to promo_other
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$discount=0;
			$sql_chk="SELECT COUNT(*) FROM trn_tdiary2_sl 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date' AND 
								`promo_code` ='$promo_code'";
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk>0){
					$sql_promo="SELECT 
											type_discount,discount
										FROM
											`promo_other`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											promo_code='$promo_code' AND
											'$netvt' BETWEEN start_baht AND end_baht ";
					$row_promo=$this->db->fetchAll($sql_promo);
					if(count($row_promo)>0){
						$discount=$row_promo[0]['discount'];
					}
			}
			return $discount;
		}//func
		
		function getListCoupon(){
			/**
			 * @desc modify : 22072014
			 * * last modify : 04082014 change table `com_application_coupon` to `promo_other` and change field promo_type=promo_tp
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_promo="SELECT 
									*
								FROM
									`promo_other`
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									promo_tp IN('COUPON','MCOUPON','SMS','LINE') AND
									'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_promo=$this->db->fetchAll($sql_promo);
			if(count($row_promo)>0){
				return $row_promo;
			}else{
				return '';
			}
		}//func
		
//		function getListCoupon(){
//			/**
//			 * @desc modify : 23062014
//			 * @param
//			 */
//			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
//			$sql_promo="SELECT 
//									promo_code,promo_des,start_baht,end_baht,type_discount,discount,play_main_promotion,
//									play_last_promotion,member_discount,get_point,buy_type,buy_status,xpoint
//								FROM
//									`com_application_coupon`
//								WHERE
//									corporation_id='$this->m_corporation_id' AND
//									company_id='$this->m_company_id' AND
//									promo_type='COUPON' AND
//									'$this->doc_date' BETWEEN start_date AND end_date	";
//			$row_promo=$this->db->fetchAll($sql_promo);
//			if(count($row_promo)>0){
//				return $row_promo;
//			}else{
//				return '';
//			}
//		}//func
		
		function setFreegifset($member_no,$promo_code,$net_amt){
			/**
			 * @desc : บันทึกรายการสินค้าแถมฟรีใน temp
			 * @desc : used table trn_promotion_tmp1 for function setGapValTemp in Model_Cashier
			 * @desc : create 19052014
			 * @desc : modify 20102014 replace start_date by $this->doc_date
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_app="SELECT COUNT(*) FROM trn_diary1
								WHERE 
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND												
													`doc_date`='$this->doc_date' AND 
													`member_id` ='$member_no' AND 
													`status_no` ='01' AND application_id IN('OPPN300','OPPS300')";
			$n_app=$this->db->fetchOne($sql_app);
			if($n_app>0){
				$sql_chk="SELECT product_id FROM promo_detail WHERE promo_code ='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$rows_chk=$this->db->fetchAll($sql_chk);
				if(!empty($rows_chk)){
					foreach($rows_chk as $data){
						$product_id=$data['product_id'];
						$sql_chk2="SELECT COUNT(*) FROM `trn_promotion_tmp1` 
												WHERE 
														`corporation_id`='$this->m_corporation_id' AND
														`company_id`='$this->m_company_id' AND
														`branch_id`='$this->m_branch_id' AND
														`computer_no` ='$this->m_computer_no' AND
														`computer_ip` ='$this->m_com_ip' AND
														`doc_date`='$this->doc_date' AND 
														`promo_code` ='$promo_code' AND 
														`product_id` ='$product_id'";
						$n_chk=$this->db->fetchOne($sql_chk2);
						if($n_chk<1){
							//insert
									$doc_tp='SL';
									//$promo_code=$promo_play;
									$promo_tp='F';
									$employee_id=$this->employee_id;
									$member_no='';
									//$product_id=$product_id;
									$quantity='1';
									$status_no='00';
									$product_status='';
									$application_id='';
									$card_status='';
									$get_point='';
									$promo_id='';
									$discount_percent='';
									$member_percent1='';
									$member_percent2='';
									$co_promo_percent='';
									$coupon_percent='';
									$promo_st='';
									//call net_amt
									$net_amt='';
									$discount=0;
									$discount_member='';
									$check_repeat='N';
									$objCsh=new Model_Cashier();
									$str_res_save=$objCsh->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no,$product_id,$quantity,
															$status_no,$product_status,$application_id,$card_status,
															$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
															$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);
							//insert
							
						}
					}
				}
			}//end if
			
		}//func
		
		function chkOpsTuesPriv($member_no,$promo_code){
			/**
			 * @desc 31032014
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$status_chk='N';
			$sql_chk="SELECT doc_no,status_no,application_id FROM trn_diary1 
							WHERE 
								member_id='$member_no' AND 
								doc_date='$this->doc_date' AND 
								status_no IN('01','05') AND 
								application_id NOT IN('OPPN300') AND 
								flag<>'C'";
			$row_chk=$this->db->fetchAll($sql_chk);
			if(!empty($row_chk)){
				$doc_no=$row_chk[0]['doc_no'];
				$status_no=$row_chk[0]['status_no'];
				$application_id=$row_chk[0]['application_id'];
				if($status_no=='05' && $application_id=='OPT'){
					$status_chk='Y';
				}else{
					$sql_exist="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code='$promo_code'";
					$n_exist=$this->db->fetchOne($sql_exist);
					if($n_exist<1){
						$status_chk='Y';
					}
				}//if
			}
			return $status_chk;
		}//func
				
		function changeNewCard($member_no,$member_no_ref,$ops_day_ref){
			/**
			 * @desc 24032014 change OPS Day card TH => TU
			 * @return string message
			 */
			//*WR05102015 for check exist card
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_chkonline="SELECT COUNT(*) FROM com_branch_computer WHERE online_status='1'";
			$n_chkonline=$this->db->fetchOne($sql_chkonline);
			if($n_chkonline>0){
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($member_no); 			
				if($json_meminfo!='false' && $json_meminfo!='' && $json_meminfo!='[]'){
					$str_msg= "ไม่พบรหัสบัตรนี้ในทะเบียน หรือรหัสบัตรนี้ถูกใช้สมัครแล้ว";
					return $str_msg;
				}
			}
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$str_msg="";
			$sql_chkregcard="SELECT COUNT(*) FROM com_register_new_card WHERE member_id='$member_no' AND  card_type='N' AND apply_date='0000-00-00'";
			$n_chkregcard=$this->db->fetchOne($sql_chkregcard);
			if($n_chkregcard<1){
				$str_msg= "ไม่พบรหัสบัตรนี้ในทะเบียน หรือรหัสบัตรนี้ถูกใช้สมัครแล้ว";
				return $str_msg;
			}
			
			$sql_chkopsday="SELECT start_code,end_code,special_day FROM com_special_day WHERE '$member_no' BETWEEN start_code AND end_code";
			$row_chkopsday=$this->db->fetchAll($sql_chkopsday);
			if(!empty($row_chkopsday)){
				$ops_day_cmp=$row_chkopsday[0]['special_day'];
				switch($ops_day_cmp){
					case '31':$ops_day_cmp='TH1';$ops_product_id='25654';break;
					case '32':$ops_day_cmp='TH2';$ops_product_id='25655';break;
					case '33':$ops_day_cmp='TH3';$ops_product_id='25656';break;
					case '34':$ops_day_cmp='TH4';$ops_product_id='25657';break;
					default :$ops_day_cmp='';
				}

				switch($ops_day_ref){
					case 'TH1':$msg_show="บัตรอังคารที่1";break;
					case 'TH2':$msg_show="บัตรอังคารที่2";break;
					case 'TH3':$msg_show="บัตรอังคารที่3";break;
					case 'TH4':$msg_show="บัตรอังคารที่4";break;
					default :$msg_show="";
				}
				//*WR20052015 for support เลือกบัตรอังคารอะไรก็ได้
				if($ops_day_cmp!=$ops_day_ref || $ops_day_cmp==$ops_day_ref){				
					$promo_code="";
					$promo_st="";
					$employee_id="";
					$member_no="";
					$product_id=$ops_product_id;
					$quantity=1;
					$price=0;
					$doc_tp="DN";
					$status_no="05";
					$result=$this->setCshTemp($promo_code,$promo_st,$employee_id,$member_no,$product_id,$quantity,$price,$doc_tp,$status_no);
					if($result==1){
						$str_msg= "Y";
					}else{
						$str_msg= "N";
					}
				}else{
					$str_msg="กรุณาเลือกบัตรให้ตรงกับ $msg_show";
				}
			}else{
				$str_msg="ไม่พบรหัสนี้ กรุณาตรวจสอบอีกครั้ง";
			}
			return $str_msg;
		}//func
		
		function updDisCntFshPurchase($member_no,$amount,$net_amt){
			/**
			 * @desc modify : 24042014
			 * @return null
			 */
			$str_percent_discount="";
			$sql_chk="SELECT application_id FROM trn_diary1 
				WHERE doc_date='$this->doc_date' AND member_id='$member_no' 
				AND status_no='01'  AND flag<>'C'";
			$row_chk=$this->db->fetchAll($sql_chk);
			if(!empty($row_chk)){
				$application_id=$row_chk[0]['application_id'];
				$sql_data="SELECT * FROM `com_application_fstpurchase` 
								WHERE application_id='$application_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$row_data=$this->db->fetchAll($sql_data);
				if(!empty($row_data)){
					$promo_code=$row_data[0]['promo_code'];
					if($row_data[0]['type_amount']=='G'){
						if($row_data[0]['cmp_amount']=='G' && $amount>=$row_data[0]['start_baht']){						
							//Goss >= start_baht
							$type_discount=$row_data[0]['type_discount'];
							$first_percent=$row_data[0]['first_percent'];
							$add_first_percent=$row_data[0]['add_first_percent'];
							$cal_amount=$row_data[0]['cal_amount'];
							if($type_discount=='PERCENT'){
								$first_percent=$first_percent/100;
								$add_first_percent=$add_first_percent/100;
								$sql_items="SELECT seq,product_id,amount,member_discount1,member_discount2,net_amt 
												FROM `trn_promotion_tmp1`
												WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`computer_no` ='$this->m_computer_no' AND
													`computer_ip` ='$this->m_com_ip' AND
													`doc_date`='$this->doc_date'";
								$row_items=$this->db->fetchAll($sql_items);
								foreach($row_items as $data){
									if($data['amount']>0){
										$seq=$data['seq'];
										$member_discount1=$data['amount']*$first_percent;
										$amount2=$data['amount']-$member_discount1;
										$member_discount2=$amount2*$add_first_percent;
										$net_amt=$data['amount']-($member_discount1+$member_discount2);
										$sql_upd="UPDATE `trn_promotion_tmp1` 
														SET 
															`member_discount1`='$member_discount1',
															`member_discount2`='$member_discount2',
															`promo_code`='$promo_code',
															`net_amt`='$net_amt'
														WHERE
															`branch_id`='$this->m_branch_id' AND
															`computer_no` ='$this->m_computer_no' AND
															`computer_ip` ='$this->m_com_ip' AND
															`doc_date`='$this->doc_date' AND
															`seq`='$seq'";
										$this->db->query($sql_upd);
									}
								}//foreach
								 $str_percent_discount=$row_data[0]['first_percent']."+".$row_data[0]['add_first_percent'];
							}
							
						}
					}
				}//!empty row_data
			}//end ifchk
			echo $str_percent_discount;
		}//func
		
		function reDisCntFshPurchase($member_no){
			/**
			 * @desc modify : 24042014
			 * @return null
			 */
			$str_percent_discount="";
			$sql_chk="SELECT application_id FROM trn_diary1 
				WHERE doc_date='$this->doc_date' AND member_id='$member_no' 
				AND status_no='01'  AND flag<>'C'";
			$row_chk=$this->db->fetchAll($sql_chk);
			if(!empty($row_chk)){
				$application_id=$row_chk[0]['application_id'];				
				//*WR20082014 for support we love ops day 500 net free coupon
				$sql_refree_coupon="DELETE FROM `trn_promotion_tmp1` 
										WHERE  
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`computer_no` ='$this->m_computer_no' AND
												`computer_ip` ='$this->m_com_ip' AND
												`doc_date`='$this->doc_date' AND promo_code='OX15080814'";
				@$this->db->query($sql_refree_coupon);
				////// start
				$sql_app="SELECT* FROM com_application_head WHERE application_id='$application_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$row_app=$this->db->fetchAll($sql_app);
				if(!empty($row_app)){
					$first_percent=$row_app[0]['first_percent'];
					$add_first_percent=$row_app[0]['add_first_percent'];
					$str_percent_discount=$row_app[0]['first_percent']."+".$row_app[0]['add_first_percent'];
					$first_percent=$first_percent/100;
					$add_first_percent=$add_first_percent/100;
					$sql_items="SELECT seq,product_id,amount,member_discount1,member_discount2,net_amt 
									FROM `trn_promotion_tmp1`
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no` ='$this->m_computer_no' AND
										`computer_ip` ='$this->m_com_ip' AND
										`doc_date`='$this->doc_date'";
					$row_items=$this->db->fetchAll($sql_items);
					foreach($row_items as $data){
						if($data['amount']>0){
							$seq=$data['seq'];
							$member_discount1=$data['amount']*$first_percent;
							$amount2=$data['amount']-$member_discount1;
							$member_discount2=$amount2*$add_first_percent;
							$net_amt=$data['amount']-($member_discount1+$member_discount2);
							$sql_upd="UPDATE `trn_promotion_tmp1` 
											SET 
												`member_discount1`='$member_discount1',
												`member_discount2`='$member_discount2',
												`promo_code`='',
												`net_amt`='$net_amt'
											WHERE
												`branch_id`='$this->m_branch_id' AND
												`computer_no` ='$this->m_computer_no' AND
												`computer_ip` ='$this->m_com_ip' AND
												`doc_date`='$this->doc_date' AND
												`seq`='$seq'";
							$this->db->query($sql_upd);
						}
					}//foreach				
				}
				///// end
			}//end if
			echo $str_percent_discount;
		}//func
		
		function chkFreeProductNewbirth($promo_code,$product_id){
			/**
			 * @desc modify : 10022014
			 * @return null
			 */
			$sql_chk="SELECT COUNT(*) FROM trn_tdiary2_sl 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date' AND
								`promo_code`='$promo_code' AND `product_id`='$product_id'";
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk<1){
				echo "N";
			}else if($n_chk>1){
				echo "O";//over
			}
		}//func
		
		function chkCardExpireForRenew($expire_date){
			/**
			 * @desc 14102013
			 * @return
			 */
			$chk_status='0';
			$arr_expire=explode("-",$expire_date);
			$arr_docdate=explode("-",$this->doc_date);	
			$timeStmpExpire = mktime(0,0,0,$arr_expire[1],'01',$arr_expire[0]);
			$timeStmpDocDate = mktime(0,0,0,$arr_docdate[1],$arr_docdate[2],$arr_docdate[0]);	
			if($timeStmpExpire>$timeStmpDocDate){
				//expire is over ต่อบัตรก่อนหมดอายุ
				$dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($this->doc_date)) . "+1 month");	
				$dateOneMonth=date("Y-m-d",$dateOneMonthAdded);												
				$arr_OneMonth=explode('-',$dateOneMonth);
				$onemonth_lastday=parent::lastday($arr_OneMonth[1],$arr_OneMonth[0]);
				$arr_next=explode("-",$onemonth_lastday);
				$timeStmpNextMonth = mktime(0,0,0,$arr_next[1],$arr_next[2],$arr_next[0]);
				if($timeStmpExpire>$timeStmpNextMonth){
					//ต่อก่อนหมดอายุ
					$chk_status=1;
				}																				
			}else if($timeStmpExpire<$timeStmpDocDate){
				//expire is less ต่อหลังหมดอายุ					
				//*WR29032013
				$arr_curr_month=explode('-',$this->doc_date);
				$cuur_date_chk=$arr_curr_month[0]."-".$arr_curr_month[1]."-01";
				$datePrevOneMonthSub = strtotime(date("Y-m-d", strtotime($cuur_date_chk)) . "-1 month");	
				$datePrevOneMonth=date("Y-m-d",$datePrevOneMonthSub);	
				$arr_prev=explode("-",$datePrevOneMonth);												
				$timeStmpPrevMonth = mktime(0,0,0,$arr_prev[1],'01',$arr_prev[0]);
				if($timeStmpExpire<$timeStmpPrevMonth){
					//สามารถต่อก่อนเดือนหมดอายุ 1 เดือน หรือภายในเดือนหมดอายุเท่านั้น				
					$chk_status=2;
				}											
			}
			return $chk_status;
		}//func
		
		function clsOpsNewCard(){
			/**
			 * @desc modify:26042013
			 * @return
			 */
			$sql_cls="TRUNCATE TABLE `trn_topsday_newcard`";
			@$this->db->query($sql_cls);			
		}//func	
		
		function setOpsDayNewCard($arr_data){
			/**
			 * @desc modify:26042013
			 * @return
			 */
			
			if(empty($arr_data)) return false;	
			$sql_cls="TRUNCATE TABLE `trn_topsday_newcard`";
			@$this->db->query($sql_cls);		
			foreach($arr_data as $data){
				if(trim($data['ops'])!=''){
					$sql_add="INSERT INTO trn_topsday_newcard SET opsday='$data[ops]',doc_date='$this->doc_date'";
					@$this->db->query($sql_add);
				}
			}
		}//func
		
		function getOpsNewCard(){
			/**
			 * @desc modify:26042013
			 * @return
			 */
			$sql_ops="SELECT opsday FROM trn_topsday_newcard WHERE doc_date='$this->doc_date'";
			$rows_ops=$this->db->fetchAll($sql_ops);
			return $rows_ops;
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
			 * @desc ยอดที่ใช้ในวันนี้
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
			 * @desc new solution employee buy all shop 01042013
			 * @etc 25062012 not check corporation_id and company_id
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_ecoupon");
			$rows_e=array();
			//check ops day
			$arr_opsday=parent::getPromotionDay();
			if($arr_opsday[0]=='31' || $arr_opsday[0]=='32' || $arr_opsday[0]=='33' || $arr_opsday[0]=='34' || $arr_opsday[0]=='35' 
				|| $arr_opsday[0]=='51' || $arr_opsday[0]=='52' || $arr_opsday[0]=='53' || $arr_opsday[0]=='54' || $arr_opsday[0]=='55'){
				$rows_e[0]['error_opsday']="Y";
			}else{
				
				//check webcam
				$sql_cam="SELECT COUNT(*) FROM com_web_pt WHERE member='$employee_id'";			
				$n_cam=$this->db->fetchOne($sql_cam);
				if($n_cam<1){
					$rows_e[0]['error_cam']="Y";
				}else{
						//*WR03042014 Check used only once
						$rows_e[0]['is_used']="N";
						//New solution Joke 18032013
						$objCal=new Model_Calpromotion();
						$arr_coupon=$objCal->read_ecoupon($employee_id); 	
						//print_r($arr_coupon);
						if(!empty($arr_coupon)){				
							$rows_e[0]['name']=$arr_coupon['name'];
							$rows_e[0]['surname']=$arr_coupon['surname'];			
							$rows_e[0]['amount_gnc']=$arr_coupon['amount_gnc'];
							$rows_e[0]['amount_cps']=$arr_coupon['amount_cps'];
							$rows_e[0]['amount_op']=$arr_coupon['amount_op'];
							$rows_e[0]['percent_discount']=$arr_coupon['percent_discount'];
							$rows_e[0]['amount_used']=$arr_coupon['amount'];
							$rows_e[0]['amount_balance']=$arr_coupon['credit'];
							$rows_e[0]['emp_tp']=$arr_coupon['emp_tp'];
							if($rows_e[0]['emp_tp']=='O'){
								if($rows_e[0]['amount_balance']<1){
									$rows_e[0]['is_used']="Y";
								}
							}else{
								//check used one only
								if($rows_e[0]['amount_op']!=$rows_e[0]['amount_balance']){
									$rows_e[0]['is_used']="Y";
								}
							}
						}else{
							$rows_e[0]['error_empty']="Y";
						}
				}
			}//end if ops day
			return $rows_e;
		}//func
		
		function chkEmpPrivilage18112015($employee_id){
			/**
			 * @desc new solution employee buy all shop 01042013
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
					//*WR03042014 Check used only once
					$rows_e[0]['is_used']="N";
					//New solution Joke 18032013
					$objCal=new Model_Calpromotion();
					$arr_coupon=$objCal->read_ecoupon($employee_id); 	
					//print_r($arr_coupon);
					if(!empty($arr_coupon)){				
						$rows_e[0]['name']=$arr_coupon['name'];
						$rows_e[0]['surname']=$arr_coupon['surname'];			
						$rows_e[0]['amount_gnc']=$arr_coupon['amount_gnc'];
						$rows_e[0]['amount_cps']=$arr_coupon['amount_cps'];
						$rows_e[0]['amount_op']=$arr_coupon['amount_op'];
						$rows_e[0]['percent_discount']=$arr_coupon['percent_discount'];
						$rows_e[0]['amount_used']=$arr_coupon['amount'];
						$rows_e[0]['amount_balance']=$arr_coupon['credit'];
						$rows_e[0]['emp_tp']=$arr_coupon['emp_tp'];
						if($rows_e[0]['emp_tp']=='O'){
							if($rows_e[0]['amount_balance']<1){
								$rows_e[0]['is_used']="Y";
							}
						}else{
							//check used one only
							if($rows_e[0]['amount_op']!=$rows_e[0]['amount_balance']){
								$rows_e[0]['is_used']="Y";
							}
						}
					}else{
						$rows_e[0]['error_empty']="Y";
					}
			}
			return $rows_e;
		}//func
		
		function chkEmpPrivilage05022015($employee_id){
			/**
			 * @desc new solution employee buy all shop 01042013
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
				//*WR03042014 Check used only once
				$rows_e[0]['is_used']="N";
				$sql_chklocal="SELECT COUNT(*) FROM trn_diary1 
										WHERE member_id='$employee_id' AND 
													status_no='03' AND 
													doc_date='$this->doc_date' AND 
													flag<>'C' ";
				$n_chklocal=$this->db->fetchOne($sql_chklocal);
				if($n_chklocal>0){
					$rows_e[0]['is_used']="Y";
				}else{
					//New solution Joke 18032013
					$objCal=new Model_Calpromotion();
					$arr_coupon=$objCal->read_ecoupon($employee_id); 	
					if(!empty($arr_coupon)){				
						$rows_e[0]['name']=$arr_coupon['name'];
						$rows_e[0]['surname']=$arr_coupon['surname'];			
						$rows_e[0]['amount_gnc']=$arr_coupon['amount_gnc'];
						$rows_e[0]['amount_cps']=$arr_coupon['amount_cps'];
						$rows_e[0]['amount_op']=$arr_coupon['amount_op'];
						$rows_e[0]['percent_discount']=$arr_coupon['percent_discount'];
						$rows_e[0]['amount_used']=$arr_coupon['amount'];
						$rows_e[0]['amount_balance']=$arr_coupon['credit'];
						//check used one only						
						if($rows_e[0]['amount_op']!=$rows_e[0]['amount_balance']){
							$rows_e[0]['is_used']="Y";
						}
					}else{
						$rows_e[0]['error_empty']="Y";
					}
				}//end if
			}
			return $rows_e;
		}//func
		
		function chkEmpPrivilage333($employee_id){
			/**
			 * @desc new solution employee buy all shop 01042013
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
				//*WR03042014 Check used only once
				$rows_e[0]['is_used']="N";
				$sql_chklocal="SELECT COUNT(*) FROM trn_diary1 
										WHERE member_id='$employee_id' AND 
													status_no='03' AND 
													doc_date='$this->doc_date' AND 
													flag<>'C' ";
				$n_chklocal=$this->db->fetchOne($sql_chklocal);
				if($n_chklocal>0){
					$rows_e[0]['is_used']="Y";
				}else{
					//New solution Joke 18032013
					$objCal=new Model_Calpromotion();
					$arr_coupon=$objCal->read_ecoupon($employee_id); 	
					if(!empty($arr_coupon)){				
						$rows_e[0]['name']=$arr_coupon['name'];
						$rows_e[0]['surname']=$arr_coupon['surname'];			
						$rows_e[0]['amount_gnc']=$arr_coupon['amount_gnc'];
						$rows_e[0]['amount_cps']=$arr_coupon['amount_cps'];
						$rows_e[0]['amount_op']=$arr_coupon['amount_op'];
						$rows_e[0]['percent_discount']=$arr_coupon['percent_discount'];
						$rows_e[0]['amount_used']=$arr_coupon['amount'];
						$rows_e[0]['amount_balance']=$arr_coupon['credit'];
						//check used one only						
						if($rows_e[0]['amount_op']!=$rows_e[0]['amount_balance']){
							$rows_e[0]['is_used']="Y";
						}
					}else{
						$rows_e[0]['error_empty']="Y";
					}
				}//end if
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
			 * @desc exam NEWBIRTH 29072014
			 * @param
			 */
			$sql_promo="SELECT * FROM `promo_other`
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
		
		function getXPoint($promo_code){
			/**
			 * @desc 10062013
			 * get xpoint->point from  promo_header on web privilege
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_point="SELECT promo_code,promo_des,point as xpoint FROM promo_head WHERE promo_code='$promo_code'";
			$row_point=$this->db->fetchAll($sql_point);
			if(count($row_point)>0){
				return json_encode($row_point[0]);
			}else{
				return '';
			}
		}//func
		
		function getMemberPrivilege($member_no,$birthday,$apply_date,$expire_date,$tcase){
			/***
			 * @desc Joke Version
			 * modify:29042013
			 */
			$o=array();
			$objCal=new Model_Calpromotion();
			if($tcase=='1'){	
				$o=$objCal->promo_chk_hbd($member_no);	
			}
			if($tcase=='2'){	
				$o=$objCal->promo_chk_other($member_no);
			}
			
			if($tcase=='3'){	
				$o=$objCal->promo_chk_play($member_no);
			}
			
			if($tcase=='4'){	
				$o=$objCal->promo_chk_play_renew($member_no);
			}
			
			if($tcase=='5'){
				$o=$objCal->promo_sp($member_no);
			}
			
			if(empty($o)){
				 return $o;
				exit();
			}		
			  
		   $k=0;
	       foreach($o as $dataPro){
		        if($o[0]['promo_code']!=''){
		        	$p_code=$dataPro['promo_code'];
		        	
		         	//* ==== WR17122014 for NEWBIRTH
		        	$sql_prooth="SELECT promo_tp FROM promo_other WHERE promo_code='$p_code'"; 
		        	$row_prooth=$this->db->fetchAll($sql_prooth);
		        	if(!empty($row_prooth)){
		        		$o[$k]['promo_tp']=$row_prooth[0]['promo_tp'];
		        	}else{
		        		$o[$k]['promo_tp']="";
		        	}
		        	
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
	       }//foreach
	       return $o;
		}//func
		
		function setLogMemberPrivilege($member_no,$promo_code,$customer_id,$idcard='',$redeem_code='',$privilege_type='',$lock_status='',$doc_no=''){
			/**
			 * @create 15032013
			 * @modify 23022015
			 */			
			$status_qry="1";
			try{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("log","log_member_privilege");
				$sql_del="DELETE FROM log_member_privilege WHERE doc_date < DATE_SUB(NOW(), INTERVAL 7 DAY)";
				$this->db->query($sql_del);
				$sql_log="INSERT INTO 
								`pos_ssup`.`log_member_privilege` 
							  SET
									`corporation_id`='$this->m_corporation_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`doc_date` ='$this->doc_date',
									`member_no` ='$member_no',
									`customer_id` ='$customer_id',
									`promo_code` ='$promo_code',
									`flag` ='',
									`idcard`='$idcard',
									`redeem_code`='$redeem_code',
									`privilege_type`='$privilege_type' ,
									`lock_status`='$lock_status',
									`doc_no`='$doc_no',
									`reg_date` = CURDATE(),
									`reg_time` =CURTIME(),
									`reg_user` ='IT',
									`upd_date` ='',
									`upd_time` ='',
									`upd_user`=''";		
				$this->db->query($sql_log);
				if($lock_status=='Y'){
					//*WR04082015 support coupon_code อันเดียว
					$sql_log="UPDATE `pos_ssup`.`log_member_privilege` 
										SET 
												flag='',
												lock_status='Y',
												upd_date=CURDATE(),
												upd_time=CURTIME(),
												upd_user='IT'
										WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`promo_code`='$promo_code' AND 
												`idcard`='$idcard' AND
												`doc_no`<>'$doc_no' AND 
												`privilege_type` NOT IN('LINE','MCOUPON','COUPON')";
						$this->db->query($sql_log);
				}
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
			 * @last modify : 27012014
			 * @return discount by percent range
			 */
			$this->promo_code=$promo_code;
			$this->promo_id=$promo_id;			
			//หายอด net โดยไม่รวมรายการที่ได้ส่วนลด 50 % แล้ว
			if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113'){
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
			 * @last modify : 22122015 for support new redeem point 2016
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
			if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113'){
				$sql_item="SELECT id,product_id,amount,net_amt,quantity,price
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
				$sql_item="SELECT id,product_id,amount,net_amt,quantity,price
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
					$item_price=$data['price'];
					if($this->amount<1){
						$product_id=$data['product_id'];
						$arr_product=parent::browsProduct($product_id);
						$item_price=$arr_product[0]['price'];
						$this->amount=$data['quantity']*$item_price;
					}
					//*WR22122015
					$sql_upd="UPDATE `$tbl_temp`
										SET
											price='$item_price',
											amount='$this->amount',
											discount='0',
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
		
		function discountBeforePayment($promo_code,$promo_id,$promo_tp){
			/**
			 * @desc
			 * * WR25082015 คำนวนตอนสุดท้าย F10 ส่วนเกินชำระเต็ม
			 */
			$result=0;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_point1_detail");
			$sql_pt="SELECT
							start_baht,
							end_baht,
							discount,
							point,
							cal_amount,
							type_discount,
							cal_discount
						FROM
							promo_point1_detail
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							promo_code='$promo_code' AND
							promo_id='$promo_id' AND
							'$this->doc_date' BETWEEN start_date AND end_date";
			//echo $sql_pt;
			$row_pt=$this->db->fetchAll($sql_pt);
			if(count($row_pt)>0){				
				$res_tbltemp=parent::countDiaryTemp();
				$arr_tbltemp=explode('#',$res_tbltemp);
				$tbl_temp=$arr_tbltemp[1];
				$start_baht=$row_pt[0]['start_baht'];
				$end_baht=$row_pt[0]['end_baht'];
				$discount=$row_pt[0]['discount'];
				$point=$row_pt[0]['point'];
				$cal_amount=$row_pt[0]['cal_amount'];//5 จำนวนจริงที่นำมาคำนวณส่วนลด
				$type_discount=$row_pt[0]['type_discount'];//DISCOUNT
				$cal_discount=$row_pt[0]['cal_discount'];//0
				//*========== START==========
				//find max seq ล่าสุด
				$sql_mseq="SELECT seq 	FROM `$tbl_temp`
								WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`promo_code`='$promo_id' AND										
										`computer_ip` ='$this->m_com_ip' AND
										`doc_date` ='$this->doc_date' AND
										`price` >0 ORDER BY seq DESC LIMIT 0,1";
				$r_Mseq=$this->db->fetchAll($sql_mseq);
				$Mseq=$r_Mseq[0]['seq'];
				//cal ยอด GOSS
				$sql_cal_amount="SELECT SUM(amount) as cal_amt
										FROM `$tbl_temp`
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`promo_code`='$promo_id' AND
											`computer_ip` ='$this->m_com_ip' AND
											`doc_date` ='$this->doc_date' ";
				$row_cal_amt=$this->db->fetchAll($sql_cal_amount);
				$cal_amt_tmp=$row_cal_amt[0]['cal_amt_tmp'];//amount form table temp
				
				$sql_item="SELECT id,product_id,amount,net_amt,seq,price,quantity
								FROM `$tbl_temp`
								WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`promo_code`='$promo_id' AND							
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date` ='$this->doc_date' ORDER BY seq ASC";
				$row_items=$this->db->fetchAll($sql_item);
				if(!empty($row_items)){
					if($promo_tp=='N'){						
						$balance=$cal_amount;//5		
						$n_rows=count($row_items);
						foreach($row_items as $data){
							$Lseq=$data['seq'];//item seq
							$items_amount=$data['amount'];//item amount
							$items_price=$data['price'];
							if($balance>0){
								if($items_amount<$balance){
									
									$items_discount=0;
									$items_price=0;
									$items_net_amt=0;
									$balance=0;
									$items_amount=0;
									
// 									$items_discount=$items_amount;
// 									$items_net_amt=$items_amount-$items_discount;		
// 									$balance=$balance-$items_amount;

								}else{
									$items_discount=$balance;
									$items_net_amt=$items_amount-$items_discount;
									$balance=$balance-$items_discount;
								}
					
							}else{
								$items_discount=0;								
								$items_net_amt=$items_amount;

// 								$items_discount=0;
// 								$items_price=0;
// 								$items_amount=0;
// 								$items_net_amt=0;
								
							}
							//assum data in trn_tdiary2_sl only
							$sql_upd="UPDATE `$tbl_temp`
											SET
													price='$items_price',
													discount='$items_discount',
													amount='$items_amount',
													net_amt='$items_net_amt'
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`promo_code`='$promo_id' AND												
													`computer_ip` ='$this->m_com_ip' AND
													`doc_date` ='$this->doc_date' AND
													`id`='$data[id]'";
							$this->db->query($sql_upd);//echo $sql_upd;
							//*WR20082015 redeem point 2015
							$cr_rows++;
						}//foreach
						
						//check net<0
						$sql_chk_net="SELECT COUNT(*)
											FROM `$tbl_temp`
											WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`promo_code`='$promo_id' AND						
											`computer_ip` ='$this->m_com_ip' AND
											`doc_date` ='$this->doc_date' AND
											`net_amt`<0";
						$n_chk_net=$this->db->fetchOne($sql_chk_net);
						if($n_chk_net>0){
							return 'E9';
						}
						
						$result=3;
					
					}//end if promo_tp
				}//end if 
				//*==========  STOP===========
			}//end if 
			return '3';
		}//func
		
		function promoDiscountNew($promo_code,$promo_id,$promo_tp,$s_percent='',$s_point='',$s_discount=''){
			/**
			 * @desc
			 * *WR25082015 คำนวนตอนสุดท้าย F10
			 * * กรณี BURNPOINT2 ปรับส่วนลดตาม % เฉพาะกรณีสินค้าไม่ได้ส่วนลดเท่านั้น
			 * @param String $promo_code ex. BURNPOINT2
			 * @param String $promo_id ex. STKER-2
			 * @param String $promo_tp ex. S
			 * @param Integer $s_percent ex. 50 percent discount
			 * @param Integer $s_point ex. 142 point used
			 * @param Integer $s_discount ex.710 Baht
			 * @last modify : 21122015
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
						type_discount,
						cal_discount
					FROM 
						promo_point1_detail
					WHERE 
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND 
						promo_code='$this->promo_code' AND
						promo_id='$this->promo_id' AND 
						'$this->doc_date' BETWEEN start_date AND end_date";
			//echo $sql_pt;
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
						$cal_amount=$row_pt[0]['cal_amount'];//1000 ยอดเงินที่จ่ายจริง
						$type_discount=$row_pt[0]['type_discount'];//PERCENT
						
						$cal_discount=$row_pt[0]['cal_discount'];//*WR20082015 redeem point 2015 800
						//OX24171113 The first purchase 1 คะแนน  ส่วนลด 5 บาท  รับสิทธิ์ส่วนลด
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
						if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113' || $this->promo_code=='AAA'){
							$sql_item="SELECT id,promo_code,product_id,amount,discount,net_amt,seq,price,quantity
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
									 		 	`status_no` ='04' AND
												`doc_date` ='$this->doc_date' ORDER BY id";
						}else{
							
							//*========== WR01012016 START==========
							//find max seq ล่าสุด
							$sql_mseq="SELECT seq
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' AND 
												`price` >0
											ORDER BY seq DESC LIMIT 0,1";
							$r_Mseq=$this->db->fetchAll($sql_mseq);
							$Mseq=$r_Mseq[0]['seq'];
							//cal ยอด GOSS
							$sql_cal_amount="SELECT SUM(amount) as cal_amt
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' ";
							$row_cal_amt=$this->db->fetchAll($sql_cal_amount);
							$cal_amt=$row_cal_amt[0]['cal_amt'];
							$sql_item="SELECT id,product_id,amount,net_amt,seq,price,quantity
											FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' ORDER BY seq ASC";
							//*========== WR01012016 STOP===========
							
							
						}
						//echo $sql_item;
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
									
									$balance=$cal_amount;//1000
									//*WR20082015 redeem point 2015
									if($cal_discount>0){
										$cal_discount=$cal_amt-$cal_amount;//2545
										$pdiscount=($cal_discount/$cal_amt)*100;//38%
										$pdiscount=floor($pdiscount);//floor
										//$pdiscount=ceil($pdiscount);
										$balance=$cal_amt;//1000
									}
									
									$n_rows=count($row_items);									
									foreach($row_items as $data){
										$Lseq=$data['seq'];//item seq
										$this->amount=$data['amount'];//item amount
										$items_price=$data['price'];
										if($balance>0){
																
											if($this->amount<$balance){
												$items_discount=0;
												$this->net_amt=$this->amount;
												$balance=$balance-$this->amount;
											}else{
												$items_discount=$this->amount-$balance;
												$this->net_amt=$this->amount-$items_discount;
												$balance=$balance-$this->amount;
											}
																				
										}else{
												$items_discount=0;
												$items_price=0;
												$this->amount=0;
												$this->net_amt=0;
										}
										//assum data in trn_tdiary2_sl only
										$sql_upd="UPDATE `$tbl_temp`
													SET
														price='$items_price',
														special_discount='$items_discount',
														amount='$this->amount',
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
										//*WR20082015 redeem point 2015
										$cr_rows++;
									}//foreach									
									//check net<0
									$sql_chk_net="SELECT COUNT(*)
															FROM `$tbl_temp`
																WHERE
																	`corporation_id`='$this->m_corporation_id' AND
																	`company_id`='$this->m_company_id' AND 
																	`promo_code`='$this->promo_id' AND
																	`computer_no` ='$this->m_computer_no' AND
														 		 	`computer_ip` ='$this->m_com_ip' AND
																	`doc_date` ='$this->doc_date' AND 
																	`net_amt`<0";
									$n_chk_net=$this->db->fetchOne($sql_chk_net);
									if($n_chk_net>0){
										return 'E9';
									}		
																
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
											if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113' || $this->promo_code=='AAA'){
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
		
		function promoDiscount($promo_code,$promo_id,$promo_tp,$s_percent='',$s_point='',$s_discount=''){
			/**
			 * @desc
			 * *WR25082015 คำนวนตอนสุดท้าย F10
			 * * กรณี BURNPOINT2 ปรับส่วนลดตาม % เฉพาะกรณีสินค้าไม่ได้ส่วนลดเท่านั้น
			 * @param String $promo_code ex. BURNPOINT2
			 * @param String $promo_id ex. STKER-2
			 * @param String $promo_tp ex. S
			 * @param Integer $s_percent ex. 50 percent discount
			 * @param Integer $s_point ex. 142 point used
			 * @param Integer $s_discount ex.710 Baht
			 * @last modify : 27012014
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
						type_discount,
						cal_discount
					FROM 
						promo_point1_detail
					WHERE 
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND 
						promo_code='$this->promo_code' AND
						promo_id='$this->promo_id' AND 
						'$this->doc_date' BETWEEN start_date AND end_date";
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
						$cal_amount=$row_pt[0]['cal_amount'];//500
						$type_discount=$row_pt[0]['type_discount'];//PERCENT
						
						$cal_discount=$row_pt[0]['cal_discount'];//*WR20082015 redeem point 2015
						
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
						if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113'){
							$sql_item="SELECT id,promo_code,product_id,amount,discount,net_amt,seq
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
									 		 	`status_no` ='04' AND
												`doc_date` ='$this->doc_date' ORDER BY id";
						}else{
							
							//*WR25082015
							
							//find max seq
							$sql_mseq="SELECT seq
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' AND 
												`price` >0
											ORDER BY seq DESC LIMIT 0,1";
							$r_Mseq=$this->db->fetchAll($sql_mseq);
							$Mseq=$r_Mseq[0]['seq'];
							//cal ยอด GOSS
							$sql_cal_amount="SELECT SUM(amount) as cal_amt
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' ";
							$row_cal_amt=$this->db->fetchAll($sql_cal_amount);
							$cal_amt=$row_cal_amt[0]['cal_amt'];//675
							
							$sql_item="SELECT id,product_id,amount,net_amt,seq
										FROM `$tbl_temp`
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND 
												`promo_code`='$this->promo_id' AND
												`computer_no` ='$this->m_computer_no' AND
									 		 	`computer_ip` ='$this->m_com_ip' AND
												`doc_date` ='$this->doc_date' ORDER BY seq ASC";//*WR20082015 redeem point 2015 ORDER BY price DESC
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
										$pdiscount=$discount;//28
									}
									$balance=$cal_amount;//700
									
									//*WR20082015 redeem point 2015
									if($cal_discount>0){
										$cal_discount=$cal_amt-$cal_amount;//2545
										$pdiscount=($cal_discount/$cal_amt)*100;//38%
										$pdiscount=floor($pdiscount);//floor
										//$pdiscount=ceil($pdiscount);
										$balance=$cal_amt;//6545
									}
									
									$n_rows=count($row_items);
									$bf_last_rows=$n_rows-1;
									$cr_rows=0;
									$sum_cdiscount=0;
									$sum_camount=0;
									$bal_last_discount=0;
									foreach($row_items as $data){
										$Lseq=$data['seq'];
										$this->amount=$data['amount'];
										if($balance>0){
											if($data['net_amt']>$balance){
												$x_amt=$balance;
											}else{
												$x_amt=$data['net_amt'];
											}
											
											if($sum_cdiscount<$cal_discount && $Lseq==$Mseq){
												$items_discount=$cal_discount-$sum_cdiscount;//16												
											}else{
												//original
												$items_discount=$x_amt*($pdiscount/100);
											}						
										
											//*WR20082015 redeem point 2015
											$sum_camount+=$x_amt;
											$balance=$balance-$x_amt;										
											
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
											//*WR20082015 redeem point 2015
											$sum_cdiscount+=$items_discount;											
										}
										//*WR20082015 redeem point 2015
										$cr_rows++;
										
									}//foreach									
									//check net<0
									$sql_chk_net="SELECT COUNT(*)
															FROM `$tbl_temp`
																WHERE
																	`corporation_id`='$this->m_corporation_id' AND
																	`company_id`='$this->m_company_id' AND 
																	`promo_code`='$this->promo_id' AND
																	`computer_no` ='$this->m_computer_no' AND
														 		 	`computer_ip` ='$this->m_com_ip' AND
																	`doc_date` ='$this->doc_date' AND 
																	`net_amt`<0";
									$n_chk_net=$this->db->fetchOne($sql_chk_net);
									if($n_chk_net>0){
										return 'E9';
									}									
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
											if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113'){
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
		
		function promoDiscountOrg020920151($promo_code,$promo_id,$promo_tp,$s_percent='',$s_point='',$s_discount=''){
			/**
			 * @desc
			 * * กรณี BURNPOINT2 ปรับส่วนลดตาม % เฉพาะกรณีสินค้าไม่ได้ส่วนลดเท่านั้น
			 * @param String $promo_code ex. BURNPOINT2
			 * @param String $promo_id ex. STKER-2
			 * @param String $promo_tp ex. S
			 * @param Integer $s_percent ex. 50 percent discount
			 * @param Integer $s_point ex. 142 point used
			 * @param Integer $s_discount ex.710 Baht
			 * @last modify : 27012014
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
						if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113'){
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
											if($this->promo_code=='BURNPOINT2' || $this->promo_code=='OX24171113'){
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
		
		function chkNetBefore($member_no,$net_amt){
			/**
			 * @desc : modify 04012014
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk="SELECT COUNT(*) FROM trn_diary1 
							WHERE doc_date='$this->doc_date' AND member_id='$member_no' AND net_amt>='$net_amt' AND flag<>'C'";
			$n_chk=$this->db->fetchOne($sql_chk);
			return $n_chk;
		}//func
		
		function getPromoPointHeadWeb($promo_code){
			/**
			 * @desc
			 * @return
			 * @modify 22012014
			 */
			 //check branch
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_branch");
			$sql_branch="SELECT promo_code 
							FROM promo_branch 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									(branch_id='$this->m_branch_id' OR branch_id='ALL') AND
									promo_code='$promo_code' AND
									'$this->doc_date' BETWEEN start_date AND end_date";	
			$rows_promocode=$this->db->fetchAll($sql_branch);
			$arr_promo=array();
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
										promo_code='$promo_code' AND
										web_promo='Y' AND 
										'$this->doc_date' BETWEEN start_date AND end_date
									ORDER BY promo_code";
				$rows_promo=$this->db->fetchAll($sql_promo);
				if(count($rows_promo)>0){
					$arr_promo['promo_code']=$rows_promo[0]['promo_code'];
					$arr_promo['promo_des']=$rows_promo[0]['promo_des'];
					$arr_promo['promo_tp']=$rows_promo[0]['promo_tp'];
					$arr_promo['play_main_promotion']=$rows_promo[0]['play_main_promotion'];
					$arr_promo['play_last_promotion']=$rows_promo[0]['play_last_promotion'];
					$arr_promo['get_promotion']=$rows_promo[0]['get_promotion'];
					$arr_promo['member_discount']=$rows_promo[0]['member_discount'];
					$arr_promo['get_point']=$rows_promo[0]['get_point'];
				}
			}			
			$sql_detail="SELECT promo_id,promo_des,point,start_baht,end_baht,type_discount,discount FROM promo_point1_detail WHERE promo_code='$promo_code'";
			$row_detail=$this->db->fetchAll($sql_detail);
			$arr_promo['promo_id']=$row_detail[0]['promo_id'];
			$arr_promo['promo_des']=$row_detail[0]['promo_des'];
			$arr_promo['point']=$row_detail[0]['point'];
			$arr_promo['start_baht']=$row_detail[0]['start_baht'];
			$arr_promo['end_baht']=$row_detail[0]['end_baht'];
			$arr_promo['type_discount']=$row_detail[0]['type_discount'];
			$arr_promo['discount']=$row_detail[0]['discount'];
			return $arr_promo;
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
										web_promo<>'Y' AND 
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
				foreach($rows_promo1 as $data){
					$arr_promo['actHead'][]=$data;
				}
			}
		
			$sql_promo="SELECT line,promo_code,promo_des
			FROM promo_point2_detail
			WHERE
			corporation_id='$this->m_corporation_id' AND
			company_id='$this->m_company_id' AND
			promo_code='$promo_code' AND
			'$this->doc_date' BETWEEN start_date AND end_date 	ORDER BY line";
			$rows_promo2=$this->db->fetchAll($sql_promo);
			if(count($rows_promo2)>0){
				foreach($rows_promo2 as $data){
					$arr_promo['actList'][]=$data;
				}
			}
			return json_encode($arr_promo);
		}//func
		
		function getPromoActPointDetail10042017($promo_code,$member_no){
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
			 * @desc 2013-06-10
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//for support old pos
			$sql_o="SELECT doc_no FROM trn_diary1 WHERE status_no='18' AND member_id='$member_no' AND doc_date>='2012-12-01'";
			$rows_o=$this->db->fetchAll($sql_o);
			if(!empty($rows_o)){
				foreach($rows_o as $data){
					$doc_no=$data['doc_no'];
					$sql_upd="UPDATE trn_diary2 SET quantity='1' WHERE doc_no='$doc_no' AND status_no='18' AND quantity='0.0000'";
					$this->db->query($sql_upd);
				}
			}
			//for support old pos			
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
				//0=doc_no,1=product_id,2=seq,3=quantity
				$doc_no=$arr_2[0];
				$product_id=$arr_2[1];
				//$quantity=$arr_2[2];
				$quantity=$arr_2[3];//*WR25092013
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
					//0=doc_no,1=product_id,2=seq;3=quantity
					$doc_no=$arr_2[0];
					$code=$arr_2[1];
					//$quantity=$arr_2[2];
					$quantity=$arr_2[3];//*WR25092013
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
			 * @desc 19072013 same as Model_Cn.getSumCnTemp
			 * @return Array
			 * 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_sum="SELECT 
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
			$row_l2h=$this->db->fetchAll($sql_sum);
			if(!empty($row_l2h)){
				//$xx=amount-net
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
			 	$netvt=parent::getSatang($net_amt,'normal');
				
				$netvt=number_format($netvt,"2",".","");
				$row_l2h[0]['sum_discount']=$sum_discount;
				$row_l2h[0]['sum_net_amt']=$netvt;
								
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcn',$row_l2h[0],'yes');	
				return $json;				
			}
		}//func
		
		function getSumRwdTemp22(){
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
		
		function chkNewCardLost($member_id,$card_type,$ops_day_ref){
			/**
			 * @desc
			 * @careate 17112015
			 * @modify
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_chkonline="SELECT COUNT(*) FROM com_branch_computer WHERE online_status='1'";
			$n_chkonline=$this->db->fetchOne($sql_chkonline);
			if($n_chkonline>0){
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($member_id); 	
				if($json_meminfo!='false' && $json_meminfo!='' && $json_meminfo!='[]'){
					return 2;
				}
			}
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			$wh_ops="";
			if(substr($member_id,0,4)!='1520'){
				$wh_ops=" card_type='$card_type' AND ";
			}			
			$s_check=0;
			$sql_chk="SELECT apply_date
						FROM 
							com_register_new_card
						WHERE
							corporation_id='$this->m_corporation_id' AND 
							company_id='$this->m_company_id' AND 
							branch_id='$this->m_branch_id' AND
							member_id='$member_id' AND 
							$wh_ops
							register_date<>'0000-00-00'";
			$crow=$this->db->fetchAll($sql_chk);
			if(count($crow)>0){
				$s_check=1;
				if($crow[0]['apply_date']!='0000-00-00'){
					$s_check=2;
				}else{
					//check ops day
					$sql_chkopsday="SELECT start_code,end_code,special_day FROM com_special_day WHERE '$member_id' BETWEEN start_code AND end_code";
					$row_chkopsday=$this->db->fetchAll($sql_chkopsday);
					if(!empty($row_chkopsday)){
						$ops_day_cmp=$row_chkopsday[0]['special_day'];
						switch($ops_day_cmp){
							case '31':$ops_day_cmp='OPT1';break;
							case '32':$ops_day_cmp='OPT2';break;
							case '33':$ops_day_cmp='OPT3';break;
							case '34':$ops_day_cmp='OPT4';break;	
							case '51':$ops_day_cmp='OPS1';break;
							case '52':$ops_day_cmp='OPS2';break;
							case '53':$ops_day_cmp='OPS3';break;
							case '54':$ops_day_cmp='OPS4';break;	
							case '50':$ops_day_cmp='OPS0';break;	
							default :$ops_day_cmp='';
						}												
						if(substr($member_id,0,4)=='1520'){
							if($ops_day_ref=='OPS1' || $ops_day_ref=='OPS2' || $ops_day_ref=='OPS3' || $ops_day_ref=='OPS4'){
								$s_check=6;
							}
						}else if(substr($member_id,0,4)=='1120'){
							if($ops_day_ref=='OPT1' || $ops_day_ref=='OPT2' || $ops_day_ref=='OPT3' || $ops_day_ref=='OPT4'){
								$s_check=7;
							}
							if($card_type=='L' && $ops_day_cmp=='OPS0' ){
								$chk_ops_th_out=parent:: getProduct('23983','1');
								if($chk_ops_th_out=='2'){
									$s_check=5;//stock out
								}
							}
						}
					}
					//check ops day
				}
			}else{
				if(substr($member_id,0,4)=='1520'){
					//รหัสบัตรนี้ไม่พบในทะเบียน
					$s_check=4;
				}else{
					$s_check=0;
				}
				//$s_check=0;
			}		
			if($s_check==1){
				$s_check=$ops_day_cmp;
			}
			return $s_check;		
		}//func
		
		function chkCardRegister($member_id,$card_type,$ops_day_ref){
			/**
			 * @desc today for process card lost
			 * @param String $member_id : id of new member
			 * @param String $card_type : type of card N is normal ,L is lost
			 * @lastmodify 03042014
			 */
			//*WR05102015 for check exist card		
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_chkonline="SELECT COUNT(*) FROM com_branch_computer WHERE online_status='1'";
			$n_chkonline=$this->db->fetchOne($sql_chkonline);
			if($n_chkonline>0){
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($member_id); 			
				//echo $json_meminfo;
				if($json_meminfo!='false' && $json_meminfo!='' && $json_meminfo!='[]'){
					return 2;
				}
			}
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			$wh_ops="";
			if(substr($member_id,0,4)!='1520'){
				$wh_ops=" card_type='$card_type' AND ";
			}			
			$s_check=0;
			$sql_chk="SELECT apply_date
						FROM 
							com_register_new_card
						WHERE
							corporation_id='$this->m_corporation_id' AND 
							company_id='$this->m_company_id' AND 
							branch_id='$this->m_branch_id' AND
							member_id='$member_id' AND 
							$wh_ops
							register_date<>'0000-00-00'";
			$crow=$this->db->fetchAll($sql_chk);
			if(count($crow)>0){
				$s_check=1;
				if($crow[0]['apply_date']!='0000-00-00'){
					$s_check=2;
				}else{
					//check ops day
					$sql_chkopsday="SELECT start_code,end_code,special_day FROM com_special_day WHERE '$member_id' BETWEEN start_code AND end_code";
					$row_chkopsday=$this->db->fetchAll($sql_chkopsday);
					if(!empty($row_chkopsday)){
						$ops_day_cmp=$row_chkopsday[0]['special_day'];
						switch($ops_day_cmp){
							case '31':$ops_day_cmp='OPT1';break;
							case '32':$ops_day_cmp='OPT2';break;
							case '33':$ops_day_cmp='OPT3';break;
							case '34':$ops_day_cmp='OPT4';break;	
							
							case '51':$ops_day_cmp='OPS1';break;
							case '52':$ops_day_cmp='OPS2';break;
							case '53':$ops_day_cmp='OPS3';break;
							case '54':$ops_day_cmp='OPS4';break;	
							case '50':$ops_day_cmp='OPS0';break;	
													
							default :$ops_day_cmp='';
						}												
						if(substr($member_id,0,4)=='1520' && $ops_day_ref!=$ops_day_cmp){
							$s_check=3;
						}else if(substr($member_id,0,4)=='1120'){
							if($ops_day_ref=='OPT1' || $ops_day_ref=='OPT2' || $ops_day_ref=='OPT3' || $ops_day_ref=='OPT4'){
								$s_check=3;
							}
							
							
							if($card_type=='L' && $ops_day_cmp=='OPS0' ){
								$chk_ops_th_out=parent:: getProduct('23983','1');
								if($chk_ops_th_out=='2'){
									$s_check=5;//stock out
								}
							}else if($card_type=='L'){
								if($ops_day_ref!=$ops_day_cmp){
									$s_check=3;
								}
							}
							
							/*
							if($card_type=='L'){
								$chk_ops_th_out=parent:: getProduct('23983','1');
								if($chk_ops_th_out=='2' && $ops_day_ref!=$ops_day_cmp){
									$s_check=3;
								}
							}
							*/
							
						}
					}
					//check ops day
				}
			}else{
				if(substr($member_id,0,4)=='1520'){
					$s_check=4;
				}else{
					$s_check=0;
				}
				//$s_check=0;
			}
			return $s_check;			
		}//func
		
		function chkCardRegister444($member_id,$card_type){
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
			 * @modify 01012016
			 * @param String $member_id
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			//*WR30112015 Check Play OX07101115,OX07111115 ห้ามกลับมาเล่นบิล 02 for 2016
			//*WR01112014 Check Play OX07571014,OX07581014 ห้ามกลับมาเล่นบิล 02
			$sql_doc="SELECT doc_no FROM trn_diary1
									WHERE 
										corporation_id='$this->m_corporation_id' AND 
										company_id='$this->m_company_id' AND 
										member_id='$member_id' AND
										branch_id='$this->m_branch_id' AND
										doc_date='$this->doc_date' AND 
										flag<>'C' AND status_no NOT IN('01','04','05','06')";
			$row_doc=$this->db->fetchAll($sql_doc);
			if(!empty($row_doc)){
				foreach($row_doc as $data){
					$doc_no_chk=$data['doc_no'];
						$sql_chkdup="SELECT COUNT(*) FROM trn_diary2
											WHERE 
												doc_no='$doc_no_chk' AND 
												promo_code IN('OX07571014','OX07581014','OX07041114','OX07051114','OX07101115','OX07111115')";
					$n_chkdup=$this->db->fetchOne($sql_chkdup);
					if($n_chkdup>0){
						return '2';
						exit();
					}
					
				}
			}
			
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
				$crow_diary=$this->db->fetchOne($sql_diary);
				if($crow_diary<1){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
					//check com_application_head
					$application_id=$crow[0];
					$sql_app="SELECT 
									first_sale,first_percent,xpoint,
									play_main_promotion,play_last_promotion,
									first_limited,add_first_percent,start_date1,end_date1,
									CONCAT(first_percent,'+',add_first_percent) AS percent_discount,
									application_type,card_type
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
									'$member_id' BETWEEN start_code AND end_code";//special_day='50' AND 
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
			 * @version Joke 19032013
			 * @desc เปลี่ยนไปดึงโดยตรงที่ jinet 01062012
			 * @param String $refer_member_st : old id member to reference
			 * @return
			 */
			if($actions=='ONLINE'){				
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($refer_member_st); 			
				if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
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
					}else{
						//******************* ONLINE PROCESS ****************
						$array_meminfo= json_decode($json_meminfo,true);					
						$o=array();
						foreach($array_meminfo as $key=>$val){
							$o[0][$key]=$val;
						}//foreach				
						if(!empty($o)){				
							//WR09092013
							$o[0]['prefix']=$this->escapeJsonString($o[0]['prefix']);
							$o[0]['name']=$this->escapeJsonString($o[0]['name']);
							$o[0]['surname']=$this->escapeJsonString($o[0]['surname']);
							$o[0]['address']=$this->escapeJsonString($o[0]['address']);
							$o[0]['road']=$this->escapeJsonString($o[0]['road']);
							$o[0]['province_name']=$this->escapeJsonString($o[0]['province_name']);
							$o[0]['district']=$this->escapeJsonString($o[0]['district']);
							$o[0]['sub_district']=$this->escapeJsonString($o[0]['sub_district']);
							
							/////////////////////////////24122013POINT2014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
							/*[0]=คะแนนที่โอนมาจากบัตรเก่า
							  [1]=วันที่หมดอายุของคะแนนบัตรเก่า
							  [2]=คะแนนบัตรปัจจุบัน
							  [3]=คะแนนคงเหลือทั้งสิ้น */
							$objCalX=new Model_Calpromotion();		
							$str_point=$objCalX->read_point($refer_member_st);
							$arr_point=explode('@@@',$str_point);
							$transfer_point=intval($arr_point[0]);
							$curr_point=intval($arr_point[2]);
							$balance_point=intval($arr_point[3]);
							$expire_transfer_point=$arr_point[1];
							$o[0]['transfer_point']=$transfer_point;
							$o[0]['curr_point']=$curr_point;
							$o[0]['balance_point']=$balance_point;
							$o[0]['expire_transfer_point']=$expire_transfer_point;	
							$o[0]['mp_point_sum_1']=$balance_point;	
							$o[0]['mp_point_sum']=$balance_point;		
							/////////////////////////////24122013POINT2014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\					
							//WR02052013 
							$opsd=$o[0]['cust_day'];
							switch($opsd){
									case 'OPS0':$opsd="TH0";break;
									case 'OPS1':$opsd="TH1";break;
									case 'OPS2':$opsd="TH2";break;
									case 'OPS3':$opsd="TH3";break;
									case 'OPS4':$opsd="TH4";break;
									case 'OPT0':$opsd="TU0";break;
									case 'OPT1':$opsd="TU1";break;
									case 'OPT2':$opsd="TU2";break;
									case 'OPT3':$opsd="TU3";break;
									case 'OPT4':$opsd="TU4";break;
									default: $opsd="";break;
							}
							$o[0]['cust_day']=$opsd;						
							$member_no=$o[0]['member_no'];
							$expire_date=$o[0]['expire_date'];
//							$buy_amt=$o[0]['buy_amt'];
//							$buy_net=$o[0]['buy_net'];
							
							$str_amt=$objCal->sale_all($member_no);
							$arr_amt=explode('#',$str_amt);
							$buy_amt=$arr_amt[0];
							$buy_net=$arr_amt[1];
							
							$member_year=$o[0]['age_card'];
							$application_id=$o[0]['appli_code'];						
							//get current data by local
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
		
		function comMemberExpireAxe($refer_member_st,$actions){
			/**
			 * @version Axe
			 * @desc เปลี่ยนไปดึงโดยตรงที่ jinet 01062012
			 * @param String $refer_member_st : old id member to reference
			 * @return
			 */
			if($actions=='ONLINE'){
				$ws = "http://10.100.53.2/wservice/webservices/services/member_data.php?";
				$type = "json"; //Only Support JSON 
				$shop=$this->m_branch_id;
				$cid = $refer_member_st;
				$act = "detail3";
				$src = $ws."callback=jsonpCallback&cid=".$cid."&brand=op&dtype=".$type."&shop=".$shop."&act=".$act."&_=1334128422190";
				$row_member=array();	
				$o=@file_get_contents($src,0);				
				if ($o === FALSE || !$o){	
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
				}else{
					//******************* ONLINE PROCESS ****************
					$o = str_replace("jsonpCallback(","",$o);
					$o = str_replace(")","",$o);
					$o = json_decode($o,true);
					if(!empty($o)){
						//INSERT OR UPDATE table com_member_expire
						$member_no=$o[0]['member_no'];	
						$expire_date=$o[0]['expire_date'];
						$buy_amt=$o[0]['buy_amt'];
						$buy_net=$o[0]['buy_net'];						

//Fixed to resoult problem
//						$expire_date='2013-06-30';
//						$o[0]['expire_date']='2013-06-30';
						
//New call to joke 13062013 case age_card=null
//						$objOps=new Model_Calpromotion();
//					    $jsonOps=$objOps->read_profile($member_no);	
//					    $ob_json=json_decode($jsonOps);
//					    $o[0]['age_card']=$ob_json->age_card;
//					    $member_year=$ob_json->age_card;
						
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
					if($chk_amount>$free_product_amount && $application_id!='OPPS300' && 
							$application_id!='OPPH300' && $application_id!='OPPHI300' && $application_id!='OPPHC300' && 
							$application_id!='OPPL300' && $application_id!='OPMGMC300' && $application_id!='OPMGMI300' &&
							$application_id!='OPPLC300' && $application_id!='OPPLI300' && $application_id!='OPPGI300' && 
							$application_id!='OPDTAC300' && $application_id!='OPKTC300' && $application_id!='OPTRUE300'){
						$pay=$chk_amount-$free_product_amount;
						$discount=$data['amount']-$pay;
						$id=$data['id'];
						$arr_b[$i]['id']=$id;
						$arr_b[$i]['price']=$data['price'];
						$arr_b[$i]['amount']=$data['amount'];
						$arr_b[$i]['discount']=$discount;
						$arr_b[$i]['pay']=$pay;
						break;
					}else if($chk_amount==$free_product_amount && $application_id!='OPPS300' && 
							$application_id!='OPPH300' && $application_id!='OPPHC300' && $application_id!='OPPHI300' && 
							$application_id!='OPPL300' && $application_id!='OPMGMC300' && $application_id!='OPMGMI300' &&
							$application_id!='OPPLC300' && $application_id!='OPPLI300' && $application_id!='OPPGI300' && 
							$application_id!='OPDTAC300' && $application_id!='OPKTC300' && $application_id!='OPTRUE300'){
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
		
		function chkAmtProBalanceCoupon($promo_code,$product_id,$quantity){
			/**
			 * @desc บันทึกมาก่อนแล้วค่อยมาอัพเดทส่วนลดทีหลัง
			 * ******* ไม่ได้ส่วนลดสมาชิก
			 * ******* ยังไม่ได้ดูส่วนลดที่เป็นตัวเงิน
			 * ******* สำหรับตาราง com_applicatoin_coupon
			 * ******* create : 22072014
			 * ******* modify : 04082014 com_application_coupon to promo_other,trn_tdiary2_sl  to `trn_promotion_tmp1`
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_pro="SELECT * FROM promo_other WHERE promo_code='$promo_code' AND  '$this->doc_date' BETWEEN start_date AND end_date";
			$row_pro=$this->db->fetchAll($sql_pro);
			$promo_amt=$row_pro[0]['end_baht'];
			$discount=$row_pro[0]['discount'];
			$buy_status=$row_pro[0]['buy_status'];//L
			$buy_type=$row_pro[0]['buy_type'];//G
			$type_discount=$row_pro[0]['type_discount'];
			if(strtoupper($type_discount)=='PERCENT'){
				$discount=$discount/100;
			}	
			$sql_maxseq="SELECT seq,amount,net_amt FROM `trn_promotion_tmp1`
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
			$max_seq_amount=$row_maxseq[0]['amount'];//current item amount
			
			//ตรวจสอบยอดรวมก่อนเรคอร์ดล่าสุด
			$sql_b="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_promotion_tmp1`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code' AND seq<>'$max_seq'";
			$sum_check=$this->db->fetchOne($sql_b);//sum all except current seq	
					
			$sql_a="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_promotion_tmp1`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code'";
			$sum_curr=$this->db->fetchOne($sql_a);//sum all			
			$res_msg="Y";
			if($buy_type=='G'){
				################################ GROSS #############################
				if($buy_status=='L'){
					if($sum_curr>$promo_amt){
						$res_msg="N#ยอดซื้อห้ามเกินกว่า ".number_format($promo_amt,'2','.',',')." บาท กรุณาตรวจสอบอีกครั้ง";
						$sql_del="DELETE FROM `trn_promotion_tmp1`
										WHERE
											`computer_no`='$this->m_computer_no' AND
				 							`computer_ip`='$this->m_com_ip' AND
											`doc_date`='$this->doc_date' AND
											`seq`='$max_seq'";
						$this->db->query($sql_del);
											
					}else{
						$new_discount=$max_seq_amount*$discount;
						$new_net_amt=$max_seq_amount-$new_discount;
						$sql_upd="UPDATE`trn_promotion_tmp1`
											SET net_amt='$new_net_amt',member_discount1='$new_discount'
											WHERE
												`computer_no`='$this->m_computer_no' AND
					 							`computer_ip`='$this->m_com_ip' AND
												`doc_date`='$this->doc_date' AND
												`seq`='$max_seq'";
						$this->db->query($sql_upd);
					}
				}else if($buy_status=='G'){
					//COMMING SOON	
					//////////START//////
					$sql_items="SELECT 
										seq,amount,member_discount1,net_amt
									FROM 
										`trn_promotion_tmp1`
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`promo_code`='$promo_code'
									ORDER BY seq ASC";
					$rows_pro=$this->db->fetchAll($sql_items);
					$sum_amt=0;
					foreach($rows_pro as $data){
						$seq=$data['seq'];
						if($buy_type=='G'){
							$tmp_amt=$data['amount'];
						}else if($buy_type=='N'){
							$tmp_amt=$data['net_amt'];					
						}
						$sum_amt+=$tmp_amt;					
						
						if($sum_amt>$promo_amt){
							///////////////START
							$bf_sum_amt=$sum_amt-$tmp_amt;					
							if($bf_sum_amt<$promo_amt){
								//case ยอด amount ปัจจุบันน้อยกว่ายอดที่เช็ค
								$curr_discount=$promo_amt-$bf_sum_amt;
								$new_discount=$curr_discount*$discount;
							}else{
								$new_discount=0.00;
							}
							$new_net_amt=$tmp_amt-$new_discount;
							$sql_upd="UPDATE 
													trn_promotion_tmp1 
												SET 
													net_amt='$new_net_amt',
													member_discount1='$new_discount'
												WHERE
													`computer_no`='$this->m_computer_no' AND
						 							`computer_ip`='$this->m_com_ip' AND
													`doc_date`='$this->doc_date' AND
													`seq`='$seq'";
							$this->db->query($sql_upd);
							///////////////END
														
						}else{
							
							$new_discount=$tmp_amt*$discount;
							$new_net_amt=$tmp_amt-$new_discount;
							$sql_upd="UPDATE trn_promotion_tmp1 
												SET net_amt='$new_net_amt',member_discount1='$new_discount'
												WHERE
													`computer_no`='$this->m_computer_no' AND
						 							`computer_ip`='$this->m_com_ip' AND
													`doc_date`='$this->doc_date' AND
													`seq`='$seq'";
							$this->db->query($sql_upd);
							
						}
						
					}//foreach
					//////////END//////////
					
							
				}
				################################ GROSS #############################
			}else if($buy_type=='N'){
				################################ NET ################################
				//COMMING SOON
				if($buy_status=='L'){
				}else if($buy_status=='G'){
				}
				################################ NET ################################
			}
			return $res_msg;
		}//func
		
		function chkAmtProBalanceCoupon14092015($promo_code,$product_id,$quantity){
			/**
			 * @desc บันทึกมาก่อนแล้วค่อยมาอัพเดทส่วนลดทีหลัง
			 * ******* ไม่ได้ส่วนลดสมาชิก
			 * ******* ยังไม่ได้ดูส่วนลดที่เป็นตัวเงิน
			 * ******* สำหรับตาราง com_applicatoin_coupon
			 * ******* create : 22072014
			 * ******* modify : 04082014 com_application_coupon to promo_other,trn_tdiary2_sl  to `trn_promotion_tmp1`
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_pro="SELECT * FROM promo_other WHERE promo_code='$promo_code' AND  '$this->doc_date' BETWEEN start_date AND end_date";
			$row_pro=$this->db->fetchAll($sql_pro);
			$promo_amt=$row_pro[0]['end_baht'];
			$discount=$row_pro[0]['discount'];
			$buy_status=$row_pro[0]['buy_status'];//L
			$buy_type=$row_pro[0]['buy_type'];//G
			$type_discount=$row_pro[0]['type_discount'];
			if(strtoupper($type_discount)=='PERCENT'){
				$discount=$discount/100;
			}	
			$sql_maxseq="SELECT seq,amount,net_amt FROM `trn_promotion_tmp1`
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
			$max_seq_amount=$row_maxseq[0]['amount'];//current item amount
			
			//ตรวจสอบยอดรวมก่อนเรคอร์ดล่าสุด
			$sql_b="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_promotion_tmp1`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code' AND seq<>'$max_seq'";
			$sum_check=$this->db->fetchOne($sql_b);//sum all except current seq	
					
			$sql_a="SELECT 
							sum(amount) as sum_amount
						FROM 
							`trn_promotion_tmp1`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code'";
			$sum_curr=$this->db->fetchOne($sql_a);//sum all			
			$res_msg="Y";
			if($buy_type=='G'){
				################################ GROSS #############################
				if($buy_status=='L'){
					if($sum_curr>$promo_amt){
						$res_msg="N#ยอดซื้อห้ามเกินกว่า ".number_format($promo_amt,'2','.',',')." บาท กรุณาตรวจสอบอีกครั้ง";
						$sql_del="DELETE FROM `trn_promotion_tmp1`
										WHERE
											`computer_no`='$this->m_computer_no' AND
				 							`computer_ip`='$this->m_com_ip' AND
											`doc_date`='$this->doc_date' AND
											`seq`='$max_seq'";
						$this->db->query($sql_del);
						
					}else{
						$new_discount=$max_seq_amount*$discount;
						$new_net_amt=$max_seq_amount-$new_discount;
						$sql_upd="UPDATE`trn_promotion_tmp1`
											SET net_amt='$new_net_amt',member_discount1='$new_discount'
											WHERE
												`computer_no`='$this->m_computer_no' AND
					 							`computer_ip`='$this->m_com_ip' AND
												`doc_date`='$this->doc_date' AND
												`seq`='$max_seq'";
						$this->db->query($sql_upd);
					}
				}else if($buy_status=='G'){
					//COMMING SOON					
				}//end if
				################################ GROSS #############################
			}else if($buy_type=='N'){
				################################ NET ################################
				//COMMING SOON
				if($buy_status=='L'){					
				}else if($buy_status=='G'){					
				}
				################################ NET ################################
			}
			return $res_msg;
		}//func
		
		function chkAmtProBalance($promo_code,$product_id='',$quantity=''){
			/**
			 * @desc บันทึกมาก่อนแล้วค่อยมาอัพเดทส่วนลดทีหลัง
			
			 * *** ไม่ได้ส่วนลดสมาชิก
			 * *** ยังไม่ได้ดูส่วนลดที่เป็นตัวเงิน
			 * *** สำหรับตาราง promo_other new birth
			 * *** last modify : 09092014 update for support delete items
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			//*WR15122016 for support last promotion
			$str_tbl_temp=parent::countDiaryTemp();
			$arr_tbl_temp=explode('#',$str_tbl_temp);
			$tbl_temp=$arr_tbl_temp[1];
			
			//*WR19122016 for support จำกัดวงเงินส่วนเกินชำระเต็ม bill first purchase
			if($promo_code=='OX02321116'){
				$sql_upd_fstpch="UPDATE $tbl_temp SET promo_code='$promo_code'
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id' AND
				`branch_id`='$this->m_branch_id' AND
				`computer_no`='$this->m_computer_no' AND
				`computer_ip`='$this->m_com_ip' AND
				`promo_code`='' AND
				`doc_date`='$this->doc_date'";
				$this->db->query($sql_upd_fstpch);
			}
			
			$sql_pro="SELECT * FROM promo_other WHERE promo_code='$promo_code' AND  '$this->doc_date' BETWEEN start_date AND end_date";
			$row_pro=$this->db->fetchAll($sql_pro);
			//*WR23122016
			if(empty($row_pro)){
				exit();
			}
			$promo_amt=$row_pro[0]['end_baht'];
			$discount=$row_pro[0]['discount'];
			$buy_type=$row_pro[0]['buy_type'];
			$buy_status=$row_pro[0]['buy_status'];
			$type_discount=$row_pro[0]['type_discount'];
			if(strtoupper($type_discount)=='PERCENT'){
				$discount=$discount/100;
			}				
			$sql_a="SELECT 
							sum(amount) as sum_amount
						FROM 
							$tbl_temp
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' AND
							`promo_code`='$promo_code'";
			$sum_all=$this->db->fetchOne($sql_a);
			if($sum_all>$promo_amt){
				
					$sql_items="SELECT 
										seq,amount,member_discount1,net_amt
									FROM 
										$tbl_temp
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`promo_code`='$promo_code'
									ORDER BY seq ASC";
					$rows_pro=$this->db->fetchAll($sql_items);
					$sum_amt=0;
					foreach($rows_pro as $data){
						$seq=$data['seq'];
						if($buy_type=='G'){
							$tmp_amt=$data['amount'];
						}else if($buy_type=='N'){
							$tmp_amt=$data['net_amt'];					
						}
						$sum_amt+=$tmp_amt;					
						
						if($sum_amt>$promo_amt){
							
							$bf_sum_amt=$sum_amt-$tmp_amt;					
							if($bf_sum_amt<$promo_amt){
								//case ยอด amount ปัจจุบันน้อยกว่ายอดที่เช็ค
								$curr_discount=$promo_amt-$bf_sum_amt;
								$new_discount=$curr_discount*$discount;
							}else{
								$new_discount=0.00;
							}
							$new_net_amt=$tmp_amt-$new_discount;
							$sql_upd="UPDATE 
													$tbl_temp
												SET 
													net_amt='$new_net_amt',
													member_discount1='$new_discount'
												WHERE
													`computer_no`='$this->m_computer_no' AND
						 							`computer_ip`='$this->m_com_ip' AND
													`doc_date`='$this->doc_date' AND
													`seq`='$seq'";
							$this->db->query($sql_upd);
														
						}else{
							
							$new_discount=$tmp_amt*$discount;
							$new_net_amt=$tmp_amt-$new_discount;
							$sql_upd="UPDATE $tbl_temp
												SET net_amt='$new_net_amt',member_discount1='$new_discount'
												WHERE
													`computer_no`='$this->m_computer_no' AND
						 							`computer_ip`='$this->m_com_ip' AND
													`doc_date`='$this->doc_date' AND
													`seq`='$seq'";
							$this->db->query($sql_upd);
							
						}
						
					}//foreach
					
					
			}else{		
				
				
				$sql_items="SELECT 
										seq,amount,member_discount1,net_amt
									FROM 
										$tbl_temp
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`promo_code`='$promo_code'
									ORDER BY seq ASC";
					$rows_pro=$this->db->fetchAll($sql_items);
					$sum_amt=0;
					foreach($rows_pro as $data){
						$seq=$data['seq'];
						if($buy_type=='G'){
							$tmp_amt=$data['amount'];
						}else if($buy_type=='N'){
							$tmp_amt=$data['net_amt'];					
						}
						$new_discount=$tmp_amt*$discount;
						$new_net_amt=$tmp_amt-$new_discount;
						$sql_upd="UPDATE $tbl_temp
											SET net_amt='$new_net_amt',member_discount1='$new_discount'
											WHERE
												`computer_no`='$this->m_computer_no' AND
					 							`computer_ip`='$this->m_com_ip' AND
												`doc_date`='$this->doc_date' AND
												`seq`='$seq'";
						$this->db->query($sql_upd);
					}//foreach
				
			}
		}//func
		
		function chkAmtProBalance2222($promo_code,$product_id,$quantity){
			/**
			 * @desc บันทึกมาก่อนแล้วค่อยมาอัพเดทส่วนลดทีหลัง
			 * @desc ไม่ได้ส่วนลดสมาชิก
			 * @desc ยังไม่ได้ดูส่วนลดที่เป็นตัวเงิน
			 * @desc สำหรับตาราง promo_other new birth
			 * @desc last modify : 08052014 append computert_no,computer_ip
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_pro="SELECT * FROM promo_other WHERE promo_code='$promo_code' AND  '$this->doc_date' BETWEEN start_date AND end_date";
			$row_pro=$this->db->fetchAll($sql_pro);
			$promo_amt=$row_pro[0]['end_baht'];
			$discount=$row_pro[0]['discount'];
			$type_discount=$row_pro[0]['type_discount'];
			if(strtoupper($type_discount)=='PERCENT'){
				$discount=$discount/100;
			}	
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
			$max_seq_amount=$row_maxseq[0]['amount'];//current item amount
			
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
			$sum_check=$this->db->fetchOne($sql_b);//sum all except current seq
			
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
			$sum_curr=$this->db->fetchOne($sql_a);//sum all
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
									SET net_amt='$new_net_amt',member_discount1='$new_discount'
									WHERE
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`seq`='$max_seq'";
				$this->db->query($sql_upd);
			}else{		
				$new_discount=$max_seq_amount*$discount;
				$new_net_amt=$max_seq_amount-$new_discount;
				$sql_upd="UPDATE trn_tdiary2_sl 
									SET net_amt='$new_net_amt',member_discount1='$new_discount'
									WHERE
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`seq`='$max_seq'";
				$this->db->query($sql_upd);
			}
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
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`seq`='$max_seq'";
				$this->db->query($sql_upd);
			}else{				
				$new_discount=$max_seq_amount*$discount;
				$new_net_amt=$max_seq_amount-$new_discount;
				$sql_upd="UPDATE trn_tdiary2_sl 
									SET net_amt='$new_net_amt',discount='$new_discount'
									WHERE
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' AND
										`seq`='$max_seq'";
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
			 * @modify 24042014
			 * @return arrPromo
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
			$arr_promo=array();
			$chk_status=0;
			$i=0;
			if($refer_member_st==''){
				//assume promotion OPPN300 สมัครสมาชิกใหม่
								$sql_promo="SELECT *
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
										
										//*WR24042014 fixed date
										$arr_promo[$i]['oppn_opps_range']='N';
										$timeStmpStartPro = mktime(0,0,0,'11','03','2014');
										$timeStmpEndPro = mktime(0,0,0,'11','30','2014');
										$arr_docdatePro=explode("-",$this->doc_date);
										$timeStmpDocDatePro = mktime(0,0,0,$arr_docdatePro[1],$arr_docdatePro[2],$arr_docdatePro[0]);											
										if($timeStmpDocDatePro>=$timeStmpStartPro && $timeStmpDocDatePro<=$timeStmpEndPro){
											$arr_promo[$i]['oppn_opps_range']='Y';
										}										
										//*WR24042014
										
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
										
										$arr_promo[$i]['refer_member_st']=$dataPromo['refer_member_st'];
										$arr_promo[$i]['card_type']=$dataPromo['card_type'];
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
							//$member_year=4;
							$sql_promo="SELECT *
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
										unset($this->db);
										$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_list");
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
											$timeStmpExpire = mktime(0,0,0,$arr_expire[1],'01',$arr_expire[0]);//WR07012013
											
											$timeStmpDocDate = mktime(0,0,0,$arr_docdate[1],$arr_docdate[2],$arr_docdate[0]);											
											if($timeStmpExpire>$timeStmpDocDate){
												//expire is over ต่อบัตรก่อนหมดอายุ 2015 not allow
												$arr_promo[$i]['chk_status']=6;					
												$chk_status=6;
												//expire is over ต่อบัตรก่อนหมดอายุ
//												$dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($this->doc_date)) . "+1 month");
//												$dateOneMonth=date("Y-m-d",$dateOneMonthAdded);												
//												$arr_OneMonth=explode('-',$dateOneMonth);
//												$onemonth_lastday=parent::lastday($arr_OneMonth[1],$arr_OneMonth[0]);
//												$arr_next=explode("-",$onemonth_lastday);
//												$timeStmpNextMonth = mktime(0,0,0,$arr_next[1],$arr_next[2],$arr_next[0]);
//												if($timeStmpExpire>$timeStmpNextMonth){
//													//ต่อก่อนหมดอายุ
//													$arr_promo[$i]['chk_status']=4;					
//													$chk_status=4;
//												}																				
											}else if($timeStmpExpire<$timeStmpDocDate){
												//expire is less ต่อหลังหมดอายุ													
												//*WR29032013
												$arr_curr_month=explode('-',$this->doc_date);
												$cuur_date_chk=$arr_curr_month[0]."-".$arr_curr_month[1]."-01";
												//$datePrevOneMonthSub = strtotime(date("Y-m-d", strtotime($this->doc_date)) . "-1 month");	
												//$datePrevOneMonthSub = strtotime(date("Y-m-d", strtotime($cuur_date_chk)) . "-1 month");	
												$datePrevOneMonthSub = strtotime(date("Y-m-d", strtotime($cuur_date_chk)) . "-2 month");//*WR04022015 ต่อหลัง 2 เดือนได้
												$datePrevOneMonth=date("Y-m-d",$datePrevOneMonthSub);
												$arr_prev=explode("-",$datePrevOneMonth);												
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
										//*WR24042014
										$arr_promo[$i]['oppn_opps_range']='N';										
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
										
										$arr_promo[$i]['refer_member_st']=$dataPromo['refer_member_st'];
										$arr_promo[$i]['card_type']=$dataPromo['card_type'];
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
						ORDER BY start_date,product_no,item_seq ASC";
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
			 * @desc modify:06022014
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
					if(!empty($row_stock)){
						if($row_stock[0]['onhand']>0){							
							//*WR06022014 OPPN300 Revised
							$price=$row_stock[0]['price'];
							if($row['price']=='0'){
								$price=0;
							}
							$arr_data[$i]['application_id']=$application_id;
							$arr_data[$i]['product_no']=$product_no;
							$arr_data[$i]['product_seq']=$product_seq;
							$arr_data[$i]['product_sub_seq']=$product_sub_seq;
							//*WR10062015
							$arr_data[$i]['price']=$row['price'];//price จาก promotion ไม่ใช่จากทะเบียน		
												
							//*WR06022014 OPPN300 Revised
							//$arr_data[$i]['price']=$price;//price จากทะเบียน
							
							$arr_data[$i]['product_id']=$row['product_id'];
							$arr_data[$i]['name_product']=$row['name_product'];
							$arr_data[$i]['quantity']=$row['quantity'];
							$arr_data[$i]['pn']=$pn;
							$arr_data[$i]['ps']=$ps;
							$i++;				
						}			
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
		
		function checkMemberExist($member_no,$ops_day_old,$ops_day_new=''){
			/**
			 * @desc for check new member
			 * @param String $member_no is new card
			 * @param String $ops_day_old is old ops day card
			 * @modify 21092015
			 * @return 
			 */
			
			//*WR15102015 check thai lang
			//*WR05102015 for check exist card			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$sql_chkonline="SELECT COUNT(*) FROM com_branch_computer WHERE online_status='1'";
			$n_chkonline=$this->db->fetchOne($sql_chkonline);
			if($n_chkonline>0){
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($member_no); 	
				//echo $json_meminfo;return  2;		
				if($json_meminfo!='false' && $json_meminfo!='' && $json_meminfo!='[]'){
					return 2;	
				}
			}
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");
			//*WR27032014
			$sql_chk_ops="SELECT* FROM com_special_day WHERE '$member_no' BETWEEN start_code AND end_code";
			$row_chk_ops=$this->db->fetchAll($sql_chk_ops);
			$sp_day_cmp=$row_chk_ops[0]['special_day'];
			switch($ops_day_old){
				case 'TH1':$ops_cmp='51';break;
				case 'TH2':$ops_cmp='52';break;
				case 'TH3':$ops_cmp='53';break;
				case 'TH4':$ops_cmp='54';break;	
				case 'TU1':$ops_cmp='31';break;
				case 'TU2':$ops_cmp='32';break;
				case 'TU3':$ops_cmp='33';break;
				case 'TU4':$ops_cmp='34';break;
				default:$ops_cmp='';	
			}
			
			switch($ops_day_new){
				case 'TH1':$ops_day_sel='51';break;
				case 'TH2':$ops_day_sel='52';break;
				case 'TH3':$ops_day_sel='53';break;
				case 'TH4':$ops_day_sel='54';break;	
				case 'TU1':$ops_day_sel='31';break;
				case 'TU2':$ops_day_sel='32';break;
				case 'TU3':$ops_day_sel='33';break;
				case 'TU4':$ops_day_sel='34';break;
				default:$ops_day_sel='';	
			}
			/*
			$chk_ops_th=substr($member_no,0,4);			
			if($chk_ops_th=='1120' && $ops_cmp!='' && $sp_day_cmp!=$ops_cmp){
				return 3;
			}else 
			 */
			 if($ops_day_sel!='' && $sp_day_cmp!=$ops_day_sel){
				//สแกนบัตรไม่ตรงกับบัตรที่เลือก
				return 5;
			}else if($ops_cmp==''){
				$sql_chk="SELECT COUNT(*) FROM `trn_tdiary2_sl` WHERE promo_code IN('OPPN300','OPPS300','OPPH300','OPMGMC300')";
				$n_chk=$this->db->fetchOne($sql_chk);
				if($n_chk>0){
					$chk_ops_card=substr($member_no,0,4);
					if($chk_ops_card=='1520'){
						return 4;
					}
				}
			}
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
		
		function setNewCardBalance($promo_code){
			/**
			 * @desc : modify 26022014
			 * @desc : for OPPN300 2014 revised to 300
			 */			
			$sql_chk_sum="SELECT SUM(amount)  AS sum_amount
										FROM trn_tdiary2_sl
											WHERE
											  `corporation_id` ='$this->m_corporation_id' AND
											  `company_id` ='$this->m_company_id' AND
											  `branch_id` ='$this->m_branch_id' AND
											  `computer_no`='$this->m_computer_no' AND
 											  `computer_ip`='$this->m_com_ip' AND
 											   `promo_code`='OPPN300' AND
											  `doc_date` ='$this->doc_date'";	
			$row_chk_sum=$this->db->fetchAll($sql_chk_sum);
			if(!empty($row_chk_sum)){
				$sum_amt=$row_chk_sum[0]['sum_amount'];
				if($sum_amt>300){
					$m_chk=$sum_amt-300;
					$item_discount=$m_chk*0.5;
					$sql_upd_bal="UPDATE trn_tdiary2_sl 
										SET 
											`discount`='$item_discount',
											`net_amt`=`amount`-$item_discount
										WHERE
											 `corporation_id` ='$this->m_corporation_id' AND
											  `company_id` ='$this->m_company_id' AND
											  `branch_id` ='$this->m_branch_id' AND
											  `computer_no`='$this->m_computer_no' AND
 											  `computer_ip`='$this->m_com_ip' AND
 											  `promo_code`='OPPN300' AND
											  `doc_date` ='$this->doc_date' AND `seq` <> '1' AND `amount` >'0'";
					$this->db->query($sql_upd_bal);
				}
			}			
		}//func
		
		function setCshTemp($promo_code,$promo_st,$employee_id,$member_no,$product_id,$quantity,$price,$doc_tp,$status_no){
			/**
			 * @desc : modify 06022014
			 * @name setCshTemp
			 * @desc store data temp before insert trn_diary1,2
			 * @param String $application_id 
			 * @param String $employee_id 
			 * @param String $member_no 
			 * @param String $product_id 
			 * @param Integer $quantity 
			 * @param String $doc_tp
			 * @param Char $status_no 			
			 * @return  Boolean 1
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
					//$this->price=$price;
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
					//*WR08052015 update  ป้องกันการบันทึกสินค้าซ้ำ
					$sql_chk="SELECT COUNT(*) FROM `trn_tdiary2_sl` 
													WHERE 
														`corporation_id` ='$this->m_corporation_id' AND
														`company_id` ='$this->m_company_id' AND
														`branch_id` ='$this->m_branch_id' AND
														`computer_no`='$this->m_computer_no' AND
			 											`computer_ip`='$this->m_com_ip' AND
														`status_no`='01' and `promo_code` ='$this->promo_code' and  `product_id`='$this->product_id' ";
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
			 * @desc สมาชิกแลกคะแนนกิจกรรมมีแต่หัว
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
			//*WR04102016
			$card_level=parent::getCardMemberInfo($member_no);
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
									`deleted`='$card_level',
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
				
				//*WR 23052013 complete transaction
				if($this->m_cashdrawer=='Y' && ($this->doc_tp=='SL' || $this->doc_tp=='VT')){
					parent::openCashDrawer();
				}
				
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
		 * @lastmodify 25032013
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
							$point_begin,
							$point_receive,
							$point_used,
							$point_net,
							$card_status,
							$get_point,
							$xpoint,
							$remark1,
							$remark2,$special_day='',$idcard='',$mobile_no='',$bill_manual_no='',$ticket_no=''){	
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
			//end type paid
			
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
			}
			
			$this->doc_tp=$doc_tp;	
			if($this->doc_tp=='VT'){
				$taxid=$remark1;
				$taxid_branch_seq=$remark2;
			}
			
			if(floatval($pay_cash)<1){
				$change=0.00;
			}	
			
			//WR19122013
			$status_no=trim($status_no);
			$application_id=trim($application_id);
			$refer_member_id=trim($refer_member_id);
			
			//*WR25022016			
			if($status_no=='01'){
				$arr_app=parent::chkTypeCardNewMember($application_id);				
				if($arr_app['application_type']=="ALL" && $refer_member_id==''){
					$strResult= "0#ไม่พบรหัสบัตรเก่าที่ใช้อ้างอิง ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
					return $strResult;
					exit();
				}else if($arr_app['application_type']=="NEW" && $member_no=='' && $arr_app['card_type']=="MBC" ){
					$strResult= "0#ไม่พบรหัสบัตรเก่าที่ใช้อ้างอิง ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
					return $strResult;
					exit();
				}
			}else{
				$arr_app['application_type']='';
				$arr_app['card_type']='';
			}
			
			//*WR04032016 check if $special_day is empty
			if($arr_app['card_type']=="IDC" && $special_day==''){
				$url_gcard="http://10.100.53.2/ims/joke/app_service_op/quota_card/api_member_quota_card.php";
				$str_gcard=@file_get_contents($url_gcard,0);
				if($str_gcard!=''){
					$obj_json=json_decode($str_quota);
					$special_day=$obj_json[0]->{'ops'};
					$quota=$obj_json[0]->{'quota'};
					$card_used=$obj_json[0]->{'card'};
					$card_balance=$obj_json[0]->{'balance'};
				}
			}
			
			if($status_no=='05' && $refer_member_id==''){					
				$strResult= "0#ไม่พบรหัสบัตรเก่าที่ใช้อ้างอิง ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
				return $strResult;
				exit();
			}
			
			//*WR29032016
			if($application_id=='OPPA300' || $application_id=='OPPB300' || $application_id=='OPPC300'  || $application_id=='OPPD300' || $application_id=='OPPF300'){
				switch($special_day){
					case "OPS1":
						$special_day='OPT1';
						break;
					case "OPS2":
						$special_day='OPT2';
						break;
					case "OPS3":
						$special_day='OPT3';
						break;
					case "OPS4":
						$special_day='OPT4';
						break;
				}
			}//end if
			
			//check transaction data
			$sql_chk_trns="SELECT COUNT(*)
									FROM 
										trn_tdiary2_sl
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
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
			
			//check exist data new member
			if($member_no!='' && $status_no=='01'){
				$sql_chk_exist="SELECT COUNT(*)
								FROM
								trn_diary1
								WHERE
								`corporation_id`='$this->m_corporation_id' AND
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
			}//if
			
			
			//WR30032013 resoult problem status_no 01 -> 00	
			$sql_upd_sts="UPDATE 
								 	trn_tdiary2_sl AS a INNER JOIN com_application_head AS b
								ON 
									a.promo_code=b.application_id
								SET
									a.status_no='01'
								WHERE
									a.corporation_id='$this->m_company_id' AND
									a.company_id='$this->m_company_id' AND
									a.branch_id='$this->m_branch_id' AND
									a.computer_no='$this->m_computer_no' AND
 									a.computer_ip='$this->m_com_ip' AND
									a.doc_date='$this->doc_date'";
			$this->db->query($sql_upd_sts);
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
							`corporation_id`='$this->m_corporation_id' AND
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
		 	$this->amount=$row_l2h[0]['sum_amount'];
		 	$this->net_amt=$this->amount-($this->discount+
		 												$this->member_discount1+
		 												$this->member_discount2+
		 												$this->co_promo_discount+
		 												$this->coupon_discount+
		 												$this->special_discount+
		 												$this->other_discount);				
			//WR22052013
		 	$this->net_amt=parent::getSatang($this->net_amt,'normal');
			//check application_head->register_free or not
			$chk_regfree=parent::chkRegFree($application_id);
			if((intval($this->amount)==0 && $chk_regfree=='Y') || $card_status=='C2'){
				$this->doc_tp="DN";//กรณีบัตรชำรุด/เสีย ปรับเป็นบิล DN
				$this->amount=0.00;
				$net_amount=0.00;
			}				
			$point1=0;
			$point2=0;
			$total_point=0;
			$redeem_point=0;
			//*WR27032013 protect bill 01 get_point== 'N'
			if($status_no!='01'){
				if(intval($point_used)!=0){
					$redeem_point=$point_receive+(-1*$point_used);
				}			
				if($member_no!='' && $get_point=='Y'){
					$point1=parent::getPoint1($this->net_amt);
					$point2=parent::getPoint2($this->net_amt);
				}			
				$point1*=$xpoint;
				$total_point=$point1+$point2+$redeem_point;		
			}		
			//WR25122013
			if($status_no=='01'){
				$point1=0;
				$point2=0;
				$redeem_point=0;
				if($application_id=='OPPC300'){
					if(intval($point_used)!=0){
						$redeem_point=$point_receive+(-1*$point_used);
					}	
					$total_point=$point1+$point2+$redeem_point;	
				}else if($arr_app['application_type']=="NEW"){
					$total_point=0;
				}				
			}
			
			if($status_no=='05' && $card_status=='C3'){
				//*WR24032014 change card to tuesday card expire 31-07-2014
				$this->doc_tp="DN";
				$application_id="OPT";
				//$point1=30;
				//$total_point=30;
			}
			//*WR17122014 for support OPPC300
			if($this->net_amt<1){
				$this->doc_tp="DN";
			}
			//*WR22052013
			if($this->doc_tp=="DN"){
		 		$paid="0000";
		 	}
		 	
			//*WR 02072015
			if($coupon_code!=''){
				$arr_coupon_s=explode('#',$coupon_code);
				$co_promo_code=$arr_coupon_s[0];
				$coupon_code=$arr_coupon_s[1];		
				if($application_id=='OPMGMC300'  || $application_id=='OPMGMI300'){
					$co_promo_code=$arr_coupon_s[2];
					$coupon_code=$arr_coupon_s[0].'#'.$arr_coupon_s[1].'#'.$arr_coupon_s[3];	
				}
			}
		 	
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
			
			//*WR20072015 bill manual	
			$keyin_st='N';
			$chk_exist_billmanual_no='N';
			$bill_manual_no=trim($bill_manual_no);
			if($bill_manual_no!=''){
				$objC1=new Model_Cashier();
				$chk_exist_billmanual_no=$objC1->checkBillManualExist($bill_manual_no);
				if($chk_exist_billmanual_no=='Y'){
					$strResult= "0#เลขที่การเปิดบิลมือซ้ำไม่สามารถบันทึกได้ กรุณาตรวจสอบอีกครั้ง#".$this->doc_tp."#Y";
					return $strResult;
					exit();
				}else{
					$this->m_doc_no=$bill_manual_no;
					$keyin_st='Y';
				}
			}
			
			//cal tax vat			
			$this->vat=parent::calVat($this->net_amt);//*WR10102012
			$this->vat=round($this->vat,2);
			
			//*WR19052015 Create Dummy Member_no for IdCard
			if($arr_app['card_type']=="IDC"){
				$r_member_no=parent::getRngMemberNo();
				$str_pattern2=str_pad($r_member_no,7,"0",STR_PAD_LEFT);
				$member_no="ID".$this->m_branch_id."".$str_pattern2;
			}
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary1_sl");
			//*WR04102016
			$card_level=parent::getCardMemberInfo($member_no);
			if($status_no=='01' || $status_no=='02'){
				$card_level='White';
			}
			//*WR15112016
			//get credit no wait
			if($paid=='0002' && $credit_no==''){
				$arr_credit=parent::getCreditCardInfo();
				if(!empty($arr_credit)){
					$credit_no=$arr_credit[0]['credit_no'];
				}
			}
			$this->db->beginTransaction();
			$this->chk_trans='Y';
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
										`point_begin`='$point_begin',
										`redeem_point`='$redeem_point',
										`point1`='$point1',
										`point2`='$point2',
										`total_point`='$total_point',
										`change`='$change',
										`co_promo_code`='$co_promo_code',
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
										`remark1`='$taxid',
										`remark2`='$taxid_branch_seq',
										`special_day`='$special_day',
										`idcard`='$idcard',
										`mobile_no`='$mobile_no',
										`keyin_st`='$keyin_st',
										`keyin_tp`='$ticket_no',
										`keyin_remark`='$bill_manual_no',	
										`deleted`='$card_level',
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
										  `lot_no`,
									      `lot_date`,
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
										  `lot_no`,
									      `lot_date`,
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
												`point_begin`,
												`redeem_point`,
												`point1`,
												`point2`,
												`total_point`,
												`change`,
												`co_promo_code`,
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
												`remark1`,
										        `remark2`,
										        `special_day`,
										        `idcard`,
												`mobile_no`,
												`keyin_st`,
												`keyin_tp`,
												`keyin_remark`,
												`deleted`,
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
												`point_begin`,
												`redeem_point`,
												`point1`,
												`point2`,
												`total_point`,
												`change`,
												`co_promo_code`,
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
												`remark1`,
										        `remark2`,
										        `special_day`,
										        `idcard`,
												`mobile_no`,
												`keyin_st`,
												`keyin_tp`,
												`keyin_remark`,
												`deleted`,
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
				//*WR20072015 bill manual
				if($bill_manual_no==''){
					//Next update table doc_no					
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
				}
				$this->db->commit();
			}catch(Zend_Db_Exception $e){				
				$arr_error=explode(":",$e->getMessage());
				//$this->msg_error=$e->getCode()."\n".$arr_error[2];
				$this->msg_error=$arr_error[2];
				$this->db->rollBack();
				$this->chk_trans='N';
			}
			
			$refer_member_expire_date=date("Y-m-d", strtotime($this->doc_date." -1 Days"));			
			if($this->chk_trans=='Y'){
				//*WR 23052013 complete transaction
//				if($this->m_cashdrawer=='Y' && ($this->doc_tp=='SL' || $this->doc_tp=='VT')){
//					parent::openCashDrawer();
//				}
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
					
					//*WR25052015 for support OPID300
					if($arr_app['application_type']=="NEW" && $arr_app['card_type']=="MBC"){
					//if($application_id!='OPID300' && $application_id!='OPMGMI300' && $application_id!='OPPLI300' && $application_id!='OPPHI300'){
						$arr_special_day=parent::getComSpecialDay($member_no);
						$special_day=$arr_special_day[0]['special_day'];
					}
					
					//$arr_special_day=parent::getComSpecialDay($member_no);
					//$special_day=$arr_special_day[0]['special_day'];
					
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
					//*WR 19052015 for support idcard
					if($arr_app['card_type']=="IDC"){
						$r_member_no=$r_member_no+1;
						$sql_upd="UPDATE 
											`com_member_dummy` 
									   SET 
										  	member_no='$r_member_no'
									   WHERE
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id'";
						$res_upd=$this->db->query($sql_upd);
					}
				}//end if status_no=05
				//..........crm data.......
				////////COMPLETE TRANSACTION///////////
				parent::TrancateTable("trn_tdiary2_sl_val");
				parent::TrancateTable("trn_promotion_tmp1");
				parent::TrancateTable("trn_tdiary2_sl");
				parent::TrancateTable("trn_tdiary1_sl");
				$strResult= "1#".$this->m_doc_no."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt."#".$status_no."#".$this->m_branch_tp."#".$this->m_cashdrawer."#".$application_id."#".$keyin_st;		
				////////COMPLETE TRANSACTION///////////
			}else{
				$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt;
			}//if
			return $strResult;								
		  }//func
	}//class
?>