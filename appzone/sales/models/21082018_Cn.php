<?php 
	class Model_Cn extends Model_PosGlobal{		
		private $doc_no;
		private $doc_no_ref;
		private $product_id;
		private $product_name;
		private $unit;
		private $quantity;
		private $status_no;
		private $amount;
		private $net_amt;
		private $percent_discount;
		private $seq;
		private $discount;
		private $member_discount1;
		private $member_discount2;
		private $co_promo_discount;
		private $coupon_discount;
		private $special_discount;
		private $other_discount;
		private $paid;//วิธีการชำระเงิน
		private $pay_cash;//จำนวนเงินที่ชำระ
		
		function __construct(){
			parent::__construct();
			$this->doc_tp="CN";
		}//func
		
		function reCheckCnByDayEnd(){
			/**
			 * @desc modify : 10072014
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_ref="SELECT doc_no,refer_doc_no FROM trn_diary1 
							WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`status_no`='06' AND
									`flag`<>'C' AND
									`refer_doc_no`<>'' AND
									`doc_date`='$this->doc_date'";
			$rows_ref=$this->db->fetchAll($sql_ref);
			if(!empty($rows_ref)){
				foreach($rows_ref as $data){
					$refer_doc_no=$data['refer_doc_no'];
					$sql_upd="UPDATE trn_diary1 SET cn_status='Y' WHERE doc_no='$refer_doc_no'";
					$this->db->query($sql_upd);
				}
			}
		}//func
		
		function initCNTemp(){
			/**
			 * @desc
			 * @param String $ref_doc_no;
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
			$sql_cn="SELECT* 
							FROM trn_tdiary2_cn 
							WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
 									`computer_ip`='$this->m_com_ip' AND
									`doc_date`='$this->doc_date'";
			$rows_cn=$this->db->fetchAll($sql_cn);
			foreach($rows_cn AS $data){
				$refer_doc_no=$data['refer_doc_no'];
				$cn_product_id=$data['product_id'];
				$cn_qty=$data['quantity'];
				$cn_seq=$data['refer_seq'];
				$sql_upd="UPDATE trn_diary2 
								SET cn_qty=cn_qty-'$cn_qty' 
								WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										doc_no='$refer_doc_no' AND
										seq='$cn_seq' AND
										product_id='$cn_product_id'";
				$res_upd=$this->db->query($sql_upd);				
				if($res_upd){
					parent::updStockMaster($cn_product_id,$cn_qty,'-1');		
				}	
			}
			$objPos=new Model_PosGlobal();
			$objPos->TrancateTable("trn_tdiary2_cn");
			$objPos->TrancateTable("trn_tdiary1_cn");
		}//func
		
		function getMemberCn($status_no,$doc_no_ref,$member_no,$cn_member_no_ref){
			/**
			 * @name getMemberCn
			 * @desc : ตรวจสอบและดึงข้อมูลสมาชิกที่สแกนเข้ามาตามเลขที่เอกสารที่ทำ CN
			 * @Last Modify : 22042016
			 * @param String $status_no : 40 ลูกค้าเปลี่ยนสินค้า or 41 คืนเงินลูกค้า
			 * @param String $doc_no_ref : doc_no เลขที่เอกสารที่จะทำ CN
			 * @param String $member_no : member_no เกิดจากการสแกนบัตร
			 * @param String $cn_member_no_ref : member_no ที่เกิดจากบิลจริง
			 * @return json data of member
			 */
			//-------------------------------------------------------------------------------------------------
			$this->doc_no_ref=$doc_no_ref;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_exist="SELECT discount,member_percent,special_percent
			FROM
			`trn_diary1`
			WHERE
			corporation_id='$this->m_corporation_id' AND
			company_id='$this->m_company_id' AND
			branch_id='$this->m_branch_id' AND
			doc_no='$this->doc_no_ref' AND
			member_id='$member_no'";
			$cmember=$this->db->fetchAll($sql_exist);
			if(count($cmember)>0){
				//get member info
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($member_no);
				if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
					//พอเจอข้างบนมันจะ write ลง local ด้วย
					//******************* OFFLINE PROCESS ****************
					$sql_member="SELECT b.name AS name,b.surname AS surname,
					b.h_address,b.h_village_id,b.h_village,b.h_soi,h_road,
					b.h_district,b.h_amphur,b.h_province,b.h_postcode,b.h_tel_no
					FROM crm_card AS a INNER JOIN crm_profile AS b
					ON(a.customer_id=b.customer_id)
					WHERE a.member_no='$member_no'";
					$rmember=$this->db->fetchAll($sql_member);
					if(count($rmember)>0){
						$rmember[0]['member_fullname']=$rmember[0]['name']." ".$rmember[0]['surname'];
					}else{
						$rmember[0]['member_fullname']='';
					}
					//******************* OFFLINE PROCESS ****************
				}else{
					//ONLINE DATA RETRUN
					$array_meminfo= json_decode($json_meminfo,true);
					$o=array();
					foreach($array_meminfo as $key=>$val){
						$o[0][$key]=$val;
					}//foreach
					if(!empty($o)){
						$rmember=$o;
					}
					//ONLINE DATA RETRUN
				}
				$rmember[0]['discount']=$cmember[0]['discount'];
				$rmember[0]['member_percent']=$cmember[0]['member_percent'];
				$rmember[0]['special_percent']=$cmember[0]['special_percent'];
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('member',$rmember[0],'yes');
				////////////////////////////////////////// init trn_tdiary2_sl_val ///////////////////////////////////////////////////////////////
				if($rmember[0]['card_level']!=''){
					$card_info=$rmember[0]['card_level']."#".$rmember[0]['ops'];
					parent::addCardInfoToValTemp($member_no,$card_info);
				}
				//////////////////////////////////////// init trn_tdiary2_sl_val ////////////////////////////////////////////////////////////////////
				return $json;
			}else{
				//case ไม่พบอาจเป็นบิลต่ออายุหรือลูกค้าเปลี่ยนบัตร
				//not found or member_no not match  doc_no
				$sql_chkchange="SELECT COUNT(*) FROM trn_diary1
				WHERE
				member_id='$member_no' AND
				refer_member_id='$cn_member_no_ref' AND
				flag<>'C' AND status_no IN('01','05')";
				$n_chkchange=$this->db->fetchOne($sql_chkchange);
				if($n_chkchange>0){
					//หาเจอในบิล 01,05
					///////////////////// re search ////////////////////////
					$objCal=new Model_Calpromotion();
					$json_meminfo=$objCal->read_profile($member_no);
					if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
						//******************* OFFLINE PROCESS ****************
						$sql_member="SELECT b.name AS name,b.surname AS surname,
						b.h_address,b.h_village_id,b.h_village,b.h_soi,h_road,
						b.h_district,b.h_amphur,b.h_province,b.h_postcode,b.h_tel_no
						FROM crm_card AS a INNER JOIN crm_profile AS b
						ON(a.customer_id=b.customer_id)
						WHERE a.member_no='$member_no'";
						$rmember=$this->db->fetchAll($sql_member);
						if(count($rmember)>0){
							$rmember[0]['member_fullname']=$rmember[0]['name']." ".$rmember[0]['surname'];
						}else{
							$rmember[0]['member_fullname']='';
						}
						//******************* OFFLINE PROCESS ****************
					}else{
						//ONLINE DATA RETRUN
						$array_meminfo= json_decode($json_meminfo,true);
						$o=array();
						foreach($array_meminfo as $key=>$val){
							$o[0][$key]=$val;
						}//foreach
						if(!empty($o)){
							$rmember=$o;
						}
						//ONLINE DATA RETRUN
					}//end if //re search
		
					$sql_exist="SELECT discount,member_percent,special_percent
					FROM
					`trn_diary1`
					WHERE
					corporation_id='$this->m_corporation_id' AND
					company_id='$this->m_company_id' AND
					branch_id='$this->m_branch_id' AND
					doc_no='$this->doc_no_ref'";
					$cmember=$this->db->fetchAll($sql_exist);
		
					$rmember[0]['discount']=$cmember[0]['discount'];
					$rmember[0]['member_percent']=$cmember[0]['member_percent'];
					$rmember[0]['special_percent']=$cmember[0]['special_percent'];
					$objUtils=new Model_Utils();
					$json=$objUtils->ArrayToJson('member',$rmember[0],'yes');
					////////////////////////////////////////// init trn_tdiary2_sl_val ///////////////////////////////////////////////////////////////
					if($rmember[0]['card_level']!=''){
						$card_info=$rmember[0]['card_level']."#".$rmember[0]['ops'];
						parent::addCardInfoToValTemp($member_no,$card_info);
					}
					//////////////////////////////////////// init trn_tdiary2_sl_val ////////////////////////////////////////////////////////////////////
					return $json;
					//////////////////// re search /////////////////////////
				}else{
					//*WR04052016 case ลูกค้าเปลี่ยนบัตรเป็นบัตรประชนหรือบัตรอื่น
					////////////////////////// START ////////////////////////////
		
					$src = "http://10.100.53.2/ims/joke/app_service_op/process/member_bypeple.php?member_no=".$member_no;
					$str_member_no=@file_get_contents($src,0);
					if($str_member_no!=''){
						$sql_exist="SELECT discount,member_percent,special_percent
						FROM
						`trn_diary1`
						WHERE
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND
						branch_id='$this->m_branch_id' AND
						doc_no='$this->doc_no_ref' AND
						member_id IN($str_member_no)";
						$row_member=$this->db->fetchAll($sql_exist);
						if(!empty($row_member)){
							//$rpos=strrpos($str_member_no,',');
							//$rpos=$rpos+2;
							//$member_no=substr($str_member_no,$rpos,13);
							//get member info
							$objCal=new Model_Calpromotion();
							$json_meminfo=$objCal->read_profile($member_no);
							if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
								//พอเจอข้างบนมันจะ write ลง local ด้วย
								//******************* OFFLINE PROCESS ****************
								$sql_member="SELECT b.name AS name,b.surname AS surname,
								b.h_address,b.h_village_id,b.h_village,b.h_soi,h_road,
								b.h_district,b.h_amphur,b.h_province,b.h_postcode,b.h_tel_no
								FROM crm_card AS a INNER JOIN crm_profile AS b
								ON(a.customer_id=b.customer_id)
								WHERE a.member_no='$member_no'";
								$rmember=$this->db->fetchAll($sql_member);
								if(count($rmember)>0){
									$rmember[0]['member_fullname']=$rmember[0]['name']." ".$rmember[0]['surname'];
								}else{
									$rmember[0]['member_fullname']='';
								}
								//******************* OFFLINE PROCESS ****************
							}else{
								//ONLINE DATA RETRUN
								$array_meminfo= json_decode($json_meminfo,true);
								$o=array();
								foreach($array_meminfo as $key=>$val){
									$o[0][$key]=$val;
								}//foreach
								if(!empty($o)){
									$rmember=$o;
								}
								//ONLINE DATA RETRUN
							}
							$rmember[0]['discount']=$cmember[0]['discount'];
							$rmember[0]['member_percent']=$cmember[0]['member_percent'];
							$rmember[0]['special_percent']=$cmember[0]['special_percent'];
							$objUtils=new Model_Utils();
							$json=$objUtils->ArrayToJson('member',$rmember[0],'yes');
							////////////////////////////////////////// init trn_tdiary2_sl_val ///////////////////////////////////////////////////////////////
							if($rmember[0]['card_level']!=''){
								$card_info=$rmember[0]['card_level']."#".$rmember[0]['ops'];
								parent::addCardInfoToValTemp($member_no,$card_info);
							}
							//////////////////////////////////////// init trn_tdiary2_sl_val ////////////////////////////////////////////////////////////////////
							return $json;
						}else{
							return "0";
						}
					}else{
						return "0";
					}
					////////////////////////// END   /////////////////////////////
						
				}
			}
		}//func
		
		function getMemberCn04052016($status_no,$doc_no_ref,$member_no,$cn_member_no_ref){
			/**
			 * @name getMemberCn
			 * @desc : Modify 30062014,WR08052014
			 * @param String $doc_no_ref : doc_no to reference
			 * @param String $member_no : member_no to reference
			 * @return json data of member
			 */
			$this->doc_no_ref=$doc_no_ref;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_exist="SELECT discount,member_percent,special_percent
						FROM 
							`trn_diary1`
						WHERE 
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							doc_no='$this->doc_no_ref' AND
							member_id='$member_no'";
			$cmember=$this->db->fetchAll($sql_exist);
			if(count($cmember)>0){
				//get member info			
				$objCal=new Model_Calpromotion();
				$json_meminfo=$objCal->read_profile($member_no); 
				if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
					//******************* OFFLINE PROCESS ****************	
					$sql_member="SELECT b.name AS name,b.surname AS surname,
										b.h_address,b.h_village_id,b.h_village,b.h_soi,h_road,
										b.h_district,b.h_amphur,b.h_province,b.h_postcode,b.h_tel_no
									FROM crm_card AS a INNER JOIN crm_profile AS b
											ON(a.customer_id=b.customer_id)
										WHERE a.member_no='$member_no'";				
					$rmember=$this->db->fetchAll($sql_member);				
					if(count($rmember)>0){
						$rmember[0]['member_fullname']=$rmember[0]['name']." ".$rmember[0]['surname'];
					}else{
						$rmember[0]['member_fullname']='';
					}
					//******************* OFFLINE PROCESS ****************				
				}else{
					//ONLINE DATA RETRUN
					$array_meminfo= json_decode($json_meminfo,true);				
					$o=array();
					foreach($array_meminfo as $key=>$val){
						$o[0][$key]=$val;
					}//foreach										
					if(!empty($o)){
						$rmember=$o;
					}
					//ONLINE DATA RETRUN
				}			
				$rmember[0]['discount']=$cmember[0]['discount'];
				$rmember[0]['member_percent']=$cmember[0]['member_percent'];
				$rmember[0]['special_percent']=$cmember[0]['special_percent'];	
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('member',$rmember[0],'yes');	
				return $json;				
			}else{
				//not found or member_no not match  doc_no
				$sql_chkchange="SELECT COUNT(*) FROM trn_diary1 
											WHERE 
												member_id='$member_no' AND
												refer_member_id='$cn_member_no_ref' AND 
												flag<>'C' AND status_no IN('01','05')";
				$n_chkchange=$this->db->fetchOne($sql_chkchange);
				if($n_chkchange>0){
					///////////////////// re search ////////////////////////
					$objCal=new Model_Calpromotion();
					$json_meminfo=$objCal->read_profile($member_no); 
					if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){
							//******************* OFFLINE PROCESS ****************	
							$sql_member="SELECT b.name AS name,b.surname AS surname,
												b.h_address,b.h_village_id,b.h_village,b.h_soi,h_road,
												b.h_district,b.h_amphur,b.h_province,b.h_postcode,b.h_tel_no
											FROM crm_card AS a INNER JOIN crm_profile AS b
													ON(a.customer_id=b.customer_id)
												WHERE a.member_no='$member_no'";				
							$rmember=$this->db->fetchAll($sql_member);				
							if(count($rmember)>0){
								$rmember[0]['member_fullname']=$rmember[0]['name']." ".$rmember[0]['surname'];
							}else{
								$rmember[0]['member_fullname']='';
							}
							//******************* OFFLINE PROCESS ****************				
					}else{
						//ONLINE DATA RETRUN
						$array_meminfo= json_decode($json_meminfo,true);				
						$o=array();
						foreach($array_meminfo as $key=>$val){
							$o[0][$key]=$val;
						}//foreach										
						if(!empty($o)){
							$rmember=$o;
						}
						//ONLINE DATA RETRUN
					}
					
					$sql_exist="SELECT discount,member_percent,special_percent
									FROM 
										`trn_diary1`
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										doc_no='$this->doc_no_ref'";
					$cmember=$this->db->fetchAll($sql_exist);
					
					$rmember[0]['discount']=$cmember[0]['discount'];
					$rmember[0]['member_percent']=$cmember[0]['member_percent'];
					$rmember[0]['special_percent']=$cmember[0]['special_percent'];	
					$objUtils=new Model_Utils();
					$json=$objUtils->ArrayToJson('member',$rmember[0],'yes');	
					return $json;				
					//////////////////// re search /////////////////////////
				}else{
					return "0";
				}
				
			}
		}//func
		
		
		
		
		function getSumCnTemp(){
			/**
			 * @name getSumCnTemp 
			 * @desc same as Model_Member.getSumRwdTemp
			 * @param 
			 * @return array 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
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
							`trn_tdiary2_cn`
						WHERE
							`corporation_id`='$this->m_company_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date'";
			$row_l2h=$this->db->fetchAll($sql_sum);
			if(!empty($row_l2h)){
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
		
		function getSumCnTemp22(){
			/**
			 * @name getSumCnTemp
			 * @desc 
			 * @param 
			 * @return array 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
			$sql_sum="SELECT 
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
								`trn_tdiary2_cn`
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
		
		function getCshTemp($page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @name getCshTemp
			 * @desc			
			 * @param Integer $page :
			 * @param String $qtype :
			 * @param String $query :
			 * @param Integer $rp : row per page
			 * @param String $sortname : field to sort
			 * @param String $sortorder : order to sort
			 * @return json format string of data
			 * @lastmodify :15102011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_cn'); 
			$sql_numrows = "SELECT * FROM trn_tdiary2_cn 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
		 								`computer_ip`='$this->m_com_ip' AND
										`doc_date`='$this->doc_date' ";
			$stmt_numrows=$this->db->query($sql_numrows);			
			$total = $stmt_numrows->rowCount();
			
			$sql_l="SELECT 
							a.id as id,
							a.promo_code as promo_code,
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
							trn_tdiary2_cn as a LEFT join com_product_master as b
							ON(a.product_id=b.product_id)		
						WHERE
							a.corporation_id='$this->m_corporation_id' AND
							a.company_id='$this->m_company_id' AND
							a.branch_id='$this->m_branch_id' AND
							a.computer_no='$this->m_computer_no' AND
		 					a.computer_ip='$this->m_com_ip' AND
							a.doc_date='$this->doc_date'					
							$sort $limit";
				
			$stmt_l=$this->db->query($sql_l);
			if($stmt_l->rowCount()>0){
				$arr_list=$stmt_l->fetchAll(PDO::FETCH_ASSOC);
				$data['page'] = $page;
				$data['total'] = $total;
				$i=(($page*$rp)-$rp)+1;
				foreach($arr_list as $row){	
					$status_pro_tmp="";//temp 23082011					
					$discount_tmp=$row['discount']+$row['member_discount1']+$row['member_discount2'];
					$discount_tmp=number_format($discount_tmp,'2','.',',');
					
					$rows[] = array(
								"id" => $row['product_id'],
								"absid" => $row['id'],
								"cell" => array(
											$i,
											$row['product_id'],
											$row['promo_code'],
											$row['product_name'],
											'Y',
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
		
	   function reCn($refer_doc_no,$cn_product_id,$cn_seq,$cn_qty){
	   		/***
	   		 * @desc
	   		 * @param String $refer_doc_no
	   		 * @param String $cn_product_id
	   		 * @param String $cn_seq
	   		 * @param String $cn_qty
	   		 * @return
	   		 */
	   		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_cn'); 
	   		$trn_status='N';
	   		$this->db->beginTransaction();
	   		try{
	   				//upd trn_diary2.cn_qty
					$sql_upd="UPDATE trn_diary2 
								SET cn_qty=cn_qty-'$cn_qty' 
								WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										doc_no='$refer_doc_no' AND
										seq='$cn_seq' AND
										product_id='$cn_product_id'";
					$this->db->query($sql_upd);
					$sql_del="DELETE FROM trn_tdiary2_cn
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
		 								`computer_ip`='$this->m_com_ip' AND
										`refer_seq`='$cn_seq' AND 
										`product_id`='$cn_product_id'";
					$this->db->query($sql_del);
	   				$this->db->commit();
	   				$trn_status='Y';
	   		}catch (Exception $e) {
	   			$db->rollback();	   			
	   		}
	   		if($trn_status=='Y'){
	   			parent::updStockMaster($cn_product_id,$cn_qty,'-1');
	   		}
	   }//func

		function delCn($items){
			/**
			 * @desc
			 * @param String $items : id to remove
			 * @return Boolean True
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_cn'); 
			$arr_items=explode("#",$items);
			if(empty($arr_items)) return FALSE;
			foreach($arr_items as $id_remove){
				if($id_remove=='') continue;
				$sql_chkdel="SELECT product_id,quantity,promo_code,promo_seq,seq,refer_seq,refer_doc_no,cn_qty 
									FROM trn_tdiary2_cn 
									WHERE id='$id_remove' AND doc_date='$this->doc_date'";						
				$row_chkdel=$this->db->fetchAll($sql_chkdel);						
				if(count($row_chkdel)>0){
					$refer_doc_no=$row_chkdel[0]['refer_doc_no'];
					$cn_product_id=$row_chkdel[0]['product_id'];
					$cn_seq=$row_chkdel[0]['refer_seq'];
					$cn_qty=$row_chkdel[0]['quantity'];					
					$this->reCn($refer_doc_no,$cn_product_id,$cn_seq,$cn_qty);				
				}//if
			
			}
			return TRUE;
		}//func
		
		function chkSeqProduct($doc_no,$product_id){
			/**
			 * @desc
			 * @return
			 */
			$sql_seq="SELECT 
							COUNT(*)
						FROM 
							trn_diary2
						WHERE 
							corporation_id='$this->m_corporation_id' AND 
							company_id='$this->m_company_id' AND 
							branch_id='$this->m_branch_id' AND 
							doc_no = '$doc_no' AND 
							product_id = '$product_id'";
			$row_seq=$this->db->fetchOne($sql_seq);
			return $row_seq;
		}//func
		
		function getCnProduct($cn_seq,$doc_no,$product_id,$quantity){
			/**
			 * @desc
			 * @param String $doc_no : doc_no to reference
			 * @param String $product_id : product_id to reference
			 * @param Integer $quantity :
			 * @return Char 
			 */
			$this->doc_no_ref=$doc_no;
			$this->product_id=$product_id;	
			$this->quantity=$quantity;		
			$this->seq=$cn_seq;
			$status_chk='1';
			$sql_c2="SELECT 
							seq, seq_set, product_id,sum(quantity) AS quantity
						FROM 
							trn_tdiary2_cn
						WHERE 
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`seq`='$this->seq' AND
							`product_id` = '$product_id'";			
			$row_c2=$this->db->fetchAll($sql_c2);
			if(count($row_c2)>0){
					$qty_c2=$row_c2[0]['quantity']+$this->quantity;
			}else{
					$qty_c2=$this->quantity;
			}
		
			$sql_d2="SELECT 
							seq, seq_set, product_id,(quantity - cn_qty) AS quantity
						FROM 
							trn_diary2
						WHERE 
							corporation_id='$this->m_corporation_id' AND 
							company_id='$this->m_company_id' AND 
							branch_id='$this->m_branch_id' AND 
							doc_no = '$doc_no' AND 
							seq='$this->seq' AND
							product_id = '$product_id'";
			$row_d2=$this->db->fetchAll($sql_d2);
			if(count($row_d2)>0){
				$qty_d2=intval($row_d2[0]['quantity']);							
				if($qty_c2>$qty_d2){
					//over quantity
					$status_chk='2';
				}
			}else{
				//product not found
				$status_chk='3';
			}
			return $status_chk;
		}//func
		
		function checkCnDocno($doc_no){
			/**
			 * @desc
			 * @return
			 * @lastmodify 15102011
			 */
			$this->doc_no=$doc_no;
			$sql_diary1="SELECT flag,doc_date 
							FROM trn_diary1
								WHERE
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									doc_no='$this->doc_no' AND
									doc_tp IN('SL','VT','DN')";
			$row_diary1=$this->db->fetchAll($sql_diary1);
			if(count($row_diary1)>0){
				if($row_diary1[0]['flag']!='C'){
					################ com_pos_config ##################
				$this->doc_time=date("H:i:s");			
				$arr_posconfig=$this->getPosConfig('CN_DAY');		
				if($arr_posconfig[0]['value_type']=='N'){
					if($this->doc_date>=$arr_posconfig[0]['start_date'] && $this->doc_date<=$arr_posconfig[0]['end_date']){
						$default_day=$arr_posconfig[0]['condition_day'];
					}else{
						$default_day=$arr_posconfig[0]['default_day'];
					}
				}				
				
				$sql_l="SELECT 
									doc_date,doc_no,member_id,flag,status_no
								FROM 
									trn_diary1
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									doc_no='$this->doc_no' AND
									doc_tp IN('SL','VT','DN') AND
									doc_date BETWEEN '$this->doc_date' - INTERVAL '$default_day' DAY AND '$this->doc_date' - INTERVAL 1 DAY";
					$row_l=$this->db->fetchAll($sql_l);				
					if(count($row_l)>0){
						//yes it's can
						return '9#'.$row_l[0]['member_id'];
					}else{
						//out condition
						return '3#'.$default_day;
					}
					################ com_pos_config ##################
				}else{
					//cancel doc_no
					return '2';
				}
			}else{
				//not found doc_no
				return '1';
			}
		}//func
		
		function bwsCnDocNO(){
			/**
			 * @desc brows doc_no to show for cn
			 * @modify : 18042016 allow bill 01
			 * @return
			 */
			$this->doc_time=date("H:i:s");			
			$arr_posconfig=$this->getPosConfig('CN_DAY');		
			if($arr_posconfig[0]['value_type']=='N'){
				if($this->doc_date>=$arr_posconfig[0]['start_date'] && $this->doc_date<=$arr_posconfig[0]['end_date']){
					$default_day=$arr_posconfig[0]['condition_day'];
				}else{
					$default_day=$arr_posconfig[0]['default_day'];
				}
			}
			$sql_l="SELECT 
							DATE_FORMAT(doc_date,'%d/%m/%Y') as doc_date_show,
							doc_no,member_id,flag,status_no
						FROM 
							trn_diary1
						WHERE 
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							status_no NOT IN('19') AND
							doc_tp IN('SL','VT','DN') AND
							doc_date BETWEEN '$this->doc_date' - INTERVAL '$default_day' DAY AND '$this->doc_date' AND
							doc_date <> '$this->doc_date'
						ORDER BY doc_date DESC";		
			$row_l=$this->db->fetchAll($sql_l);
			return $row_l;			
		}//func
		
		function bwsCnTp(){
			/**
			 * @desc
			 * @return
			 */
			$sql_cntp="SELECT `cn_tp` , `description` , `get_remark`
							FROM `com_cn_tp`
							ORDER BY cn_tp";
			$rows_cntp=$this->db->fetchAll($sql_cntp);
			return $rows_cntp;
		}//func
		
		function bwsCnProduct($doc_no_ref,$product_id){
			/**
			 * @desc 02102014 ยังมีปัญหาการตรวจสอบจำนวนรายการที่เลือกได้มากว่าบิลที่อ้างถึงนะ
			 * @param String $doc_no_ref
			 * @return
			 */
			//WR21112013 แก้ปัญหาเลือกสินค้าแล้วไม่บันทึก CN
			$sql_cn_ref="SELECT COUNT(*) FROM trn_diary1 WHERE refer_doc_no='$doc_no_ref' AND doc_tp='CN'";
			$n_ref=$this->db->fetchOne($sql_cn_ref);
			if($n_ref<1){
				$sql_cn_qty="UPDATE trn_diary2 SET cn_qty='0' WHERE doc_no='$doc_no_ref'";
				$this->db->query($sql_cn_qty);
			}
			
			$wh_product="";
			if($product_id!=''){
				$wh_product=" AND a.product_id='$product_id'";
			}
			$sql_product="SELECT 
									a.seq,a.product_id AS product_id,a.price,a.net_amt,
									b.name_product AS name_product,
									a.quantity AS quantity,
									a.cn_qty AS cn_qty,
									(a.quantity-a.cn_qty) AS balance_qty,
									b.unit
								FROM 
									trn_diary2 AS a LEFT JOIN com_product_master AS b
									ON(a.product_id=b.product_id)
								WHERE
									a.doc_no='$doc_no_ref' $wh_product";			
			$arr_item=array();
			$rows_product=$this->db->fetchAll($sql_product);
			$i=0;
			foreach($rows_product as $data){
				$seq=$data['seq'];//seq from trn_diary2
				$product_id=$data['product_id'];
				$quantity=$data['quantity'];
				$sql_cn2="SELECT 
									SUM(quantity) AS qty 
							  FROM 
							  		trn_tdiary2_cn
							  WHERE
							  		`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
		 							`computer_ip`='$this->m_com_ip' AND
									`doc_date`='$this->doc_date' AND
									`refer_seq`='$seq' AND
									`product_id`='$product_id'";
				$cn_qty=$this->db->fetchOne($sql_cn2);	
							
				$arr_item[$i]['seq']=$data['seq'];//ลำดับ seq ในรายการ seq นั้นๆ
				$arr_item[$i]['product_id']=$data['product_id'];
				$arr_item[$i]['name_product']=$data['name_product'];
				$arr_item[$i]['quantity']=$data['quantity'];//จำนวนสินค้าจริงในรายการ seq นั้นๆ
				$arr_item[$i]['price']=$data['price'];
				$arr_item[$i]['net_amt']=$data['net_amt'];		

				//*WR02102014
				//$arr_item[$i]['balance_qty']=$data['balance_qty'];//จำนวนคงเหลือหลังจากหัก cn ไปแล้ว
				
				$bl_qty=$data['balance_qty']-$cn_qty;
				if($bl_qty<0){
					$bl_qty=0;
				}
				$arr_item[$i]['balance_qty']=$bl_qty;
				
				$arr_item[$i]['unit']=$data['unit'];			
				$arr_item[$i]['cn_qty']=$data['cn_qty'];
				$i++;
			}			
			return $arr_item;
		}//func
		
		public function getPageTotal($rp){
			/**
			 * @name getPageTotal
			 * @desc
			 * @param $rp is row per page
			 * @return $cpage is total of page
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
			$sql_row = "SELECT count(*) 
							FROM `trn_tdiary2_cn` 
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
		
		function setCN($status_no,$doc_no_ref,$product_id,$quantity,$seq,$cashier_id,$cn_tp,$lot_expire,$lot_no,$cn_remark){
			/**
			 * @name setCn()
			 * @desc
			 * @param String $status_no :
			 * @param String $doc_no_ref :
			 * @param String $product_id :
			 * @param Integer $quantity :
			 * @param String $cashier_id :
			 * @last modify:12062012
			 * @return
			 */
			$this->status_no=$status_no;
			$this->doc_no_ref=$doc_no_ref;
			$this->product_id=$product_id;
			$this->quantity=intval($quantity);			
			$this->seq=$seq;
			$this->stock_st='1';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->chk_trans='1';
			$sql_l2="SELECT * 
						FROM 
							trn_diary2
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							doc_no='$this->doc_no_ref' AND
							seq='$this->seq' AND
							product_id='$this->product_id'";		
			$arrL2=$this->db->fetchAll($sql_l2);
			//****************** CAL DISCOUNT ********************************
			if(intval($arrL2[0]['discount'])!=0){
				$this->discount=($this->quantity*$arrL2[0]['discount'])/$arrL2[0]['quantity'];
			}
			
			if(intval($arrL2[0]['member_discount1'])!=0){
				$this->member_discount1=($this->quantity*$arrL2[0]['member_discount1'])/$arrL2[0]['quantity'];
			}
			if(intval($arrL2[0]['member_discount2'])!=0 ){
				$this->member_discount2=($this->quantity*$arrL2[0]['member_discount2'])/$arrL2[0]['quantity'];
			}
			if(intval($arrL2[0]['co_promo_discount'])!=0 ){
				$this->co_promo_discount=($this->quantity*$arrL2[0]['co_promo_discount'])/$arrL2[0]['quantity'];
			}
			if(intval($arrL2[0]['coupon_discount'])!=0 ){
				$this->coupon_discount=($this->quantity*$arrL2[0]['coupon_discount'])/$arrL2[0]['quantity'];
			}
			
			if(intval($arrL2[0]['special_discount'])!=0 ){
				$this->special_discount=($this->quantity*$arrL2[0]['special_discount'])/$arrL2[0]['quantity'];
			}
			
			if(intval($arrL2[0]['other_discount'])!=0 ){
				$this->other_discount=($this->quantity*$arrL2[0]['other_discount'])/$arrL2[0]['quantity'];
			}
			$this->amount=$this->quantity*$arrL2[0]['price'];
			$this->net_amt=$this->amount-($this->discount+
														$this->member_discount1+
														$this->member_discount2+
														$this->co_promo_discount+
														$this->coupon_discount+
														$this->special_discount+
														$this->other_discount);
			
			//****************** CAL DISCOUNT ********************************
			
			$sql_seq="SELECT MAX(seq)  
							FROM trn_tdiary2_cn
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
		 							`computer_ip`='$this->m_com_ip' AND
		 							`doc_date`='$this->doc_date'	";		
			$max_seq=$this->db->fetchOne($sql_seq);
			$cnseq=$max_seq+1;
			
		   $cn_qty=$this->quantity;
		   $cn_amt=$this->net_amt;
			
			$this->chk_trans='N';
			$this->db->beginTransaction();
			try{
					$sql_insert="INSERT INTO trn_tdiary2_cn
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='$this->doc_tp',
									  `status_no`='$this->status_no',
									  `refer_seq` ='$this->seq',
									  `seq` ='$cnseq',
									  `seq_set` ='{$arrL2[0]['seq_set']}',
									  `promo_code` ='{$arrL2[0]['promo_code']}',
									  `promo_seq` ='{$arrL2[0]['promo_seq']}',
									  `promo_st` ='{$arrL2[0]['promo_st']}',
									  `promo_tp` ='{$arrL2[0]['promo_tp']}',
									  `product_id`='$this->product_id',				
									  `name_product`='{$this->product_name}',		
									  `refer_doc_no`='{$this->doc_no_ref}',
									  `unit`='',			  
									  `stock_st` ='$this->stock_st',								  
									  `price` ='{$arrL2[0]['price']}',
									  `quantity`='$this->quantity',
									  `amount`='$this->amount',
									  `discount`='$this->discount',		
									  `member_discount1`='$this->member_discount1',
									  `member_discount2`='$this->member_discount2',		  
									  `co_promo_percent`='$this->co_promo_discount',
									  `coupon_discount`='$this->coupon_discount',
									  `special_discount`='$this->special_discount',
									  `other_discount`='$this->other_discount',	
									  `net_amt`='$this->net_amt',
									  `product_status`='{$arrL2[0]['product_status']}',
									  `get_point`='{$arrL2[0]['get_point']}',
									  `discount_member`='{$arrL2[0]['discount_member']}',
									  `cal_amt`='{$arrL2[0]['cal_amt']}',											  
									  `discount_percent`='{$arrL2[0]['discount_percent']}',
									  `member_percent1`='{$arrL2[0]['member_percent1']}',
									  `member_percent2`='{$arrL2[0]['member_percent2']}',
									  `coupon_percent`='{$arrL2[0]['coupon_percent']}',											  
									  `tax_type`='{$arrL2[0]['tax_type']}',
									  
									   `cn_qty`='$cn_qty',
									   `cn_amt`='$cn_amt',
									   `cn_tp`='$cn_tp',
									   `lot_expire`='$lot_expire',
									   `lot_no`='$lot_no',
									   `cn_remark`='$cn_remark',
									   
									   `computer_no`='$this->m_computer_no',
		 							   `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					
					$this->db->query($sql_insert);
					$this->chk_trans='Y';
					$this->db->commit();
			}catch(Zend_Db_Exception $e){
					$e->getMessage();
					$this->db->rollBack();
			}
			if($this->chk_trans=='Y'){
				//WR21112013 แก้ปัญหาเลือกสินค้าแล้วไม่บันทึก CN
				//parent::increaseStock($this->product_id,$this->quantity);
				//update trn_diary2.cn_qty
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
				$sql_upd="UPDATE trn_diary2 
								SET cn_qty=cn_qty+'$this->quantity' 
								WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										doc_no='$this->doc_no_ref' AND
										seq='$this->seq' AND
										product_id='$this->product_id'";
				$this->db->query($sql_upd);
			}
			return $this->chk_trans;
		}//func
		
		function saveCn($doc_no_ref,$member_no,$cashier_id,$status_no,
								$member_percent,$cn_fullname,$cn_remark,$cn_address,$acc_name,$bank_acc,$bank_name,$tel1,$tel2){
			/**
			 * @desc
			 * @return
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
			$this->doc_no_ref=strtoupper($doc_no_ref);
			$this->status_no=$status_no;		
			$this->paid="0000";	
			
			//check transaction data
			$sql_chk_trns="SELECT COUNT(*)
									FROM 
										`trn_tdiary2_cn`
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
							SUM(amount) AS sum_amount,
							SUM(net_amt) AS sum_net_amt							
						FROM 
							`trn_tdiary2_cn`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date'";
			$row_l2h=$this->db->fetchAll($sql_l2h);
//			if(count($row_l2h)<1){
//				$strResult= "0#ไม่พบรายการขาย ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
//				return $strResult;
//			}
		
			$this->quantity=$row_l2h[0]['sum_quantity'];
			$this->amount=$row_l2h[0]['sum_amount'];
			
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
		 	
			//create doc_no for diary
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);
			//address
			
			$address1=mb_substr($cn_address,0,50,'utf-8');
			$address2=mb_substr($cn_address,50,100,'utf-8');
			
			//check point
			$point1=0;
			$point2=0;
			$redeem_point=0;
			$total_point=0;
			if($member_no!=''){
				$point1=parent::getPoint1($this->net_amt);
				$point2=parent::getPoint2($this->net_amt);
			}			
			
			if($status_no!='04'){
				$point1=-1*$point1;
				$point2=-1*$point2;
			}
			
			$total_point=$point1+$point2+$redeem_point;
			
			//*========================== WR01102014 CN from bill 04 ==========================
			$chk_doc_no_ref=$this->doc_no_ref;
			$sql_chkref="SELECT point1,point2,total_point,status_no,amount FROM trn_diary1 WHERE doc_no='$chk_doc_no_ref'";
			$rows_chkref=$this->db->fetchAll($sql_chkref);
			$status_no_ref=$rows_chkref[0]['status_no'];
			$amount_ref=$rows_chkref[0]['amount'];
			$ref_point1=$rows_chkref[0]['point1'];
			$ref_point2=$rows_chkref[0]['point2'];
			$ref_redeem_point=$rows_chkref[0]['redeem_point'];
			$total_point_ref=$rows_chkref[0]['total_point'];			
			if($status_no_ref=='04'){	
				//$sql_chkref_dl="SELECT COUNT(*) AS n_chk FROM trn_diary2 WHERE doc_no='$chk_doc_no_ref' AND promo_code LIKE '%STKER%'";
				$sql_chkref_dl="SELECT COUNT(*) AS n_chk FROM trn_diary2 WHERE doc_no='$chk_doc_no_ref'";
				$n_chk_ref=$this->db->fetchOne($sql_chkref_dl);
				if($n_chk_ref>0){					
						if($status_no=='41'){				
							//คืนเงิน			
							$sql_cn_curr="SELECT 	SUM(amount) as cn_amount 
												FROM trn_tdiary2_cn 
												WHERE 
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`computer_no`='$this->m_computer_no' AND
						 							`computer_ip`='$this->m_com_ip' AND 
						 							`doc_date`='$this->doc_date'";	
							$row_cn_curr=$this->db->fetchAll($sql_cn_curr);
							$cn_amount=$row_cn_curr[0]['cn_amount'];
							if($amount_ref==$cn_amount){							
								$point1=-1*$total_point_ref;
								$point2=0;
								$redeem_point=0;
								$total_point=-1*$total_point_ref;				
							}else{
								$point1=0;
								$point2=0;
								$redeem_point=0;
								$total_point=0;
							}
							
						}else{
							$point1=0;
							$point2=0;
							$redeem_point=0;
							$total_point=0;
						}
				}else{
						$point1=0;
						$point2=0;
						$redeem_point=0;
						$total_point=0;
					}//end if
			}else if($status_no_ref=='01'){
				if($status_no=='41'){
					$point1=0;
					$point2=0;
					$redeem_point=0;
					$total_point=0;
				}
			}//if
			//*========================== WR01102014 CN from bill 04 ==========================
			
			
			//cal tax vat
			$this->vat=parent::calVat($this->net_amt);//*WR10102012
			$this->vat=round($this->vat,2);
			//$this->vat=parent::getSatang($this->vat,'normal');
			//$this->vat=$this->calVat('trn_tdiary2_cn');
			//*WR04102016
			$card_level=parent::getCardMemberInfo($member_no);
			$this->db->beginTransaction();
			$this->chk_trans=TRUE;
			try{
				//start query				
				$this->doc_time=date("H:i:s");
				$sql_add_l2h="INSERT INTO trn_tdiary1_cn
								SET
									`corporation_id`='$this->m_company_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`doc_date`='$this->doc_date',
									`doc_time`='$this->doc_time',
									`doc_no`='$this->m_doc_no',
									`doc_tp`='$this->doc_tp',
									`status_no`='$this->status_no',
									`member_id`='$member_no',
									`member_percent`='$member_percent',
									`refer_member_id`='',
									`refer_doc_no`='$this->doc_no_ref',
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
									`ex_vat_amt`='',
									`ex_vat_net`='',
									`vat`='$this->vat',
									`paid`='$this->paid',
									`pay_cash`='$this->net_amt',
									`pay_credit`='',									
									`point1`='$point1',
									`point2`='$point2',
									`redeem_point`='$redeem_point',
									`total_point`='$total_point',
									`change`='',
									`coupon_code`='',
									`pay_cash_coupon`='',
									`credit_no`='',
									`credit_tp`='',
									`bank_tp`='',
									`computer_no`='$this->m_computer_no',
									`computer_ip`='$this->m_com_ip',
									`pos_id`='$this->m_pos_id',
									`user_id`='$cashier_id',	
									`cashier_id`='$cashier_id',
									`saleman_id`='$cashier_id',								
									`doc_remark`='$cn_remark',
									`name`='$cn_fullname',
									`address1`='$address1',
									`address2`='$address2',
									`address3`='',
									`acc_name`='$acc_name',
									`bank_acc`='$bank_acc',
									`bank_name`='$bank_name',
									`tel1`='$tel1',
									`tel2`='$tel2',
									`deleted`='$card_level',
									`reg_date`=CURDATE(),
								  	`reg_time`=CURTIME(),
								    `reg_user`='$cashier_id'";
				$this->db->query($sql_add_l2h);
				//update doc_no in trn_tdiary2_sl
				$sql_upd_trn_cn2="UPDATE 
												`trn_tdiary2_cn`
											SET
												doc_no='$this->m_doc_no'
											 WHERE							 	
											 	`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`computer_no`='$this->m_computer_no' AND
					 							`computer_ip`='$this->m_com_ip' AND
												`doc_date`='$this->doc_date'";
				$this->db->query($sql_upd_trn_cn2);
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
										  `co_promo_percent`,
										  `coupon_discount`,
										  `special_discount`,
										  `other_discount`,
										  `net_amt`,
										  `product_status`,
										  `get_point`,
										  `discount_member`,
										  `cal_amt`,
										  `cn_qty`,
										  `cn_amt`,
										  `cn_tp`,
										  `cn_remark`,
										  `lot_no`,
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
										  `co_promo_percent`,
										  `coupon_discount`,
										  `special_discount`,
										  `other_discount`,						  
										  `net_amt`,
										  `product_status`,
										  `get_point`,
										  `discount_member`,
										  `cal_amt`,
										  `cn_qty`,
										  `cn_amt`,
										  `cn_tp`,
										  `cn_remark`,
										  `refer_seq`,
										  `tax_type`,
										  CURDATE(),
									  	  CURTIME(),
										  `reg_user`
									FROM
										`trn_tdiary2_cn`
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`computer_no`='$this->m_computer_no' AND
			 							`computer_ip`='$this->m_com_ip' AND
										`doc_no`='$this->m_doc_no'";	
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
												`point1`,
												`point2`,
												`redeem_point`,
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
												`acc_name`,
												`bank_acc`,
												`bank_name`,
												`tel1`,
												`tel2`,
												`doc_remark`,
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
												`point1`,
												`point2`,
												`redeem_point`,
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
												`acc_name`,
												`bank_acc`,
												`bank_name`,
												`tel1`,
												`tel2`,
												`doc_remark`,
												`deleted`,
												`reg_date`,
												`reg_time`,
												`reg_user`
										FROM 
											`trn_tdiary1_cn`
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND 
											doc_no='$this->m_doc_no'";
				$this->db->query($sql_diary1);
				//Next update table doc_no			
				$sql_inc_docno="UPDATE 
											com_doc_no 
										SET 
											doc_no=doc_no+1,
											upd_date=CURDATE(),
					   						upd_time=CURTIME(),
					   						upd_user='$cashier_id' 
										WHERE 
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND 	
											branch_id='$this->m_branch_id' AND 
											branch_no='$this->m_branch_no' AND
											doc_tp='$this->doc_tp'";
				$this->db->query($sql_inc_docno);
				
				//commit trans
				$this->db->commit();
			}catch(Zend_Db_Exception $e){
				$this->msg_error=$e->getMessage();
				//rollback trans
				$this->db->rollBack();
				$this->chk_trans=FALSE;
			}//end try
			
			if($this->chk_trans){
				$sql_tmp="SELECT 
									`refer_seq`,`product_id`,`cn_qty`,`cn_amt`,`cn_tp`,`cn_remark`
							   FROM
							   		trn_tdiary2_cn
							   	WHERE
							   		doc_no='$this->m_doc_no'";
				$rows_tmp=$this->db->fetchAll($sql_tmp);
				foreach($rows_tmp as $dataL){
					$product_id=$dataL['product_id'];
					$cn_qty=$dataL['cn_qty'];
					$cn_amt=$dataL['cn_amt'];
					$refer_seq=$dataL['refer_seq'];
					$sql_updL="UPDATE trn_diary2 
										SET											
											`cn_amt`=$cn_amt										
										WHERE
											doc_no='$this->doc_no_ref' AND
											seq='$refer_seq' AND
											product_id='$product_id'";
					$this->db->query($sql_updL);
				}
				////////COMPLETE TRANSACTION///////////				
				parent::TrancateTable("trn_tdiary2_cn");
				parent::TrancateTable("trn_tdiary1_cn");
				$strResult= "1#".$this->m_doc_no."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt."#".$status_no;
				//WR21112013 แก้ปัญหาเลือกสินค้าแล้วไม่บันทึก CN
				//stock balance 
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$objPos->stockBalance($this->m_corporation_id,$this->m_company_id,$this->m_branch_id,$this->doc_date);
				////////COMPLETE TRANSACTION///////////			
				//*WR18032016 for support line promotion
				/*
				if($status_no=='41'){	
					$objAcc=new Model_Accessory();
					$objAcc->unLockMobileCoupon($this->doc_no_ref);
				}*/
			}else{
				//return "0#".$this->msg_error;
				$strResult= "0#".$this->msg_error."#".$this->doc_tp."#".$this->m_thermal_printer."#".$this->net_amt."#".$status_no;
			}//if	
			return $strResult;
		}//func
		
		function up2j($doc_no){
			/**
			 * @desc joke test
			 */
			//*WR 15012013 Test For New solution
			$objCal=new Model_Calpromotion();
			@$objCal->up_point($doc_no);
		}//func
		
	}//class
?>