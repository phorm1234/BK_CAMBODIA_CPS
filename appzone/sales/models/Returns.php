<?php 
/**
 * 
 * Enter description here ...
 * @author is-wirat
 *
 */
class Model_Returns extends Model_PosGlobal{	
	function __construct(){
		parent::__construct();		
	}//func
	
	function getBillShortRed($member_no,$promo_code,$aa=''){
		/**
		 * @desc ใช้ดูประวัติการซื้อของบิลสินค้า short
		 * * create : 05012015
		 * * modify : 
		 * @return Array List
		 */
		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		$sql_pdtshort="SELECT* FROM promo_short WHERE promo_code ='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
		$rows_pdtshort=$this->db->fetchAll($sql_pdtshort);
		$play_promo_code=$rows_pdtshort[0]['play_promo_code'];		
		$start_date=$rows_pdtshort[0]['start_date'];//วันเริ่มรับ
		$end_date=$rows_pdtshort[0]['end_date'];//วันสิ้นสุดการรับ		
		$amount=$rows_pdtshort[0]['amount'];	
		$check_amount=$rows_pdtshort[0]['check_amount'];
		$check_amount_type=$rows_pdtshort[0]['check_amount_type'];
		$amount_type=$rows_pdtshort[0]['amount_type'];			
		$sel_product=$rows_pdtshort[0]['sel_product'];	
		$limited_qty=$rows_pdtshort[0]['limited_qty'];	//ควรได้รับจริงในบิลนี้		
		$start_baht=$rows_pdtshort[0]['start_baht'];
		$end_baht=$rows_pdtshort[0]['end_baht'];		
		$check_date_start=$rows_pdtshort[0]['check_date_start'];	
		$check_date_end=$rows_pdtshort[0]['check_date_end'];		
		if($amount_type=='N'){
			$field_amount='net_amt';
		}else if($amount_type=='G'){
			$field_amount='amount';
		}			
		$sql_doc="SELECT *
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							member_id = '$member_no' 	AND 
							$field_amount BETWEEN '$start_baht' AND '$end_baht' AND
							status_no = '00' AND
							doc_tp <> 'DN' AND
							birthday_card_st <> 'Y' AND
							birthday_card_st <> '' AND
							flag<>'C' AND doc_date BETWEEN '$check_date_start' AND '$check_date_end'";
		$rows_doc=$this->db->fetchAll($sql_doc);
		if(empty($rows_doc)){
			//*WR13012015 ========== ต่ออายุ/เปลี่ยนบัตร ===========
			$sql_m="SELECT refer_member_id
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND 
							status_no IN('01','05') AND
							member_id = '$member_no' 	AND 
							flag<>'C' AND doc_date >= '$check_date_start' ";
			$rows_m=$this->db->fetchAll($sql_m);
			if(!empty($rows_m)){
				$member_no=$rows_m[0]['refer_member_id'];
				$sql_doc="SELECT *
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							member_id = '$member_no' 	AND 
							$field_amount BETWEEN '$start_baht' AND '$end_baht' AND
							status_no = '00' AND
							doc_tp <> 'DN' AND
							birthday_card_st <> 'Y' AND
							birthday_card_st <> '' AND
							flag<>'C' AND
							doc_date <= '$check_date_end'";	
				$rows_doc=$this->db->fetchAll($sql_doc);
			}
		}
		
		$arr_r=array();
		$j=0;		
		foreach($rows_doc as $data){
					$doc_no=$data['doc_no'];
					$doc_date=$data['doc_date'];
					//check รับซ้ำ
					$re_chk='N';
					$sql_rechk="SELECT doc_no as doc_no_dn 
									FROM trn_diary1 
									WHERE 
											doc_tp='DN' AND refer_doc_no='$doc_no' AND
											flag<>'C' AND 
											doc_date BETWEEN '$start_date' AND '$end_date'";
					$row_rechk=$this->db->fetchAll($sql_rechk);
					if(!empty($row_rechk)){
						//case 25470 เป็นตัวซื้อคือมีราคาแสดงว่าค้างตัวแถม
						$doc_no_receive=$row_rechk[0]['doc_no_dn'];
						$sql_rechk2="SELECT COUNT(*) FROM trn_diary2 
											WHERE 
												doc_no ='$doc_no_receive' AND product_id='25470' AND price<'1'";						
						$n_rechk2=$this->db->fetchOne($sql_rechk2);
						if($n_rechk2>0){
							$re_chk='Y';
						}						
					}else{
						//case 25470 เป็นตัวซื้อคือมีราคาแสดงว่าค้างตัวแถม
						$sql_rechk2="SELECT COUNT(*) FROM trn_diary2 
											WHERE 
												doc_no ='$doc_no' AND product_id='25470' AND price<'1'";			
						$n_rechk2=$this->db->fetchOne($sql_rechk2);
						if($n_rechk2>0){
							$re_chk='Y';
						}
					}
					
					$sql_d2="SELECT * FROM trn_diary2 
									WHERE 
										doc_no='$doc_no' AND 
										product_id IN('24916','','24917','24918','24919','24920','24921','25553','25477','25470')";
					$r_d2=$this->db->fetchAll($sql_d2);
					if(!empty($r_d2)){
								$arr_r[$j]['doc_date']=$data['doc_date'];
								$arr_r[$j]['doc_no']=$data['doc_no'];
								$arr_r[$j]['doc_no_receive']=$doc_no_receive;
								$arr_r[$j]['flag']=$data['flag'];
								$arr_r[$j]['member_id']=$data['member_id'];
								$arr_r[$j]['status_no']=$data['status_no'];
								$arr_r[$j]['refer_member_id']=$data['refer_member_id'];
								$arr_r[$j]['amount']=$data['amount'];
								$arr_r[$j]['net_amt']=$data['net_amt'];
								$arr_r[$j]['paid']=$data['paid'];
								$arr_r[$j]['cashier_id']=$data['cashier_id'];
								$arr_r[$j]['application_id']=$data['application_id'];
								$arr_r[$j]['status_receive']=$re_chk;
								$arr_r[$j]['sel_product']=$sel_product;
								$arr_r[$j]['limited_qty']=$limited_qty;
								$j++;		
					}
			}//foreach
			return $arr_r;		
	}//func
	
	 function chkFreeShortRed($doc_no,$member_no,$promo_code,$aa=''){
			/**
			 * *@desc
			 * *@create : 18052015
			 * *@ตรวจสอบตามเลขที่บิล
			 * *
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			$msg_status="";
			$sql_short="SELECT * FROM promo_short WHERE promo_code='$promo_code'";	
			$row_short=$this->db->fetchAll($sql_short);
			if(!empty($row_short)){
				$amount=$row_short[0]['amount'];
				$check_amount=$row_short[0]['check_amount'];//ตรวจสอบยอดซื้อหรือไม่ Y
				$amount_type=$row_short[0]['amount_type'];//net or gross
				$check_amount_type=$row_short[0]['check_amount_type'];//ยอดซื้อมากกว่าหรือน้อยกว่า G,L
				$sel_product=$row_short[0]['sel_product'];//รายการสินค้าให้เลือก N
				$limited_qty=$row_short[0]['limited_qty'];//สินค้าค้างรับไม่เกิน
				$start_baht=$row_short[0]['start_baht'];//ยอดซื้อเริ่มต้น
				$end_baht=$row_short[0]['end_baht'];//ยอดซื้อสูงสุด
				$play_promo_code=$row_short[0]['play_promo_code'];//รหัสโปรโมชั่นที่ค้าง
				
				$sql_chkbill="SELECT* FROM trn_diary1 WHERE doc_no='$doc_no' AND net_amt BETWEEN '$start_baht' AND '$end_baht'";
				$rows_chkbill=$this->db->fetchAll($sql_chkbill);
				if(!empty($rows_chkbill)){
					$arr_product=array();
					$net_amt=$rows_chkbill[0]['net_amt'];
					
					if($check_amount=='Y'){
						//check ยอดซื้อนะ
						if($amount_type=='N'){
							//Net
							if($check_amount_type=='G'){
								//Getter
								if($net_amt>=$amount){
									//ค้าง 1 ตัว
									$arr_product[0]['product_id']="25470";
									self::setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);	
									
								}
								
							}
							
						}
					}//end if					
				}else{
					$msg_status="ไม่พบรหัสเลขที่เอกสาร กรุณาตรวจสอบอีกครั้ง";
				}//end if
			}			
			return $msg_status;
	}//func
	
	function getBillShortGiftset($member_no,$promo_code,$aa=''){
		/**
		 * @desc ใช้ดูประวัติการซื้อของบิลสินค้า short
		 * * create : 05012015
		 * * modify : 
		 * @return Array List
		 */
		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		$sql_pdtshort="SELECT* FROM promo_short WHERE promo_code ='$promo_code'";
		$rows_pdtshort=$this->db->fetchAll($sql_pdtshort);
		$play_promo_code=$rows_pdtshort[0]['play_promo_code'];		
		$start_date=$rows_pdtshort[0]['start_date'];//วันเริ่มรับ
		$end_date=$rows_pdtshort[0]['end_date'];//วันสิ้นสุดการรับ		
		$amount=$rows_pdtshort[0]['amount'];	
		$check_amount=$rows_pdtshort[0]['check_amount'];
		$check_amount_type=$rows_pdtshort[0]['check_amount_type'];
		$amount_type=$rows_pdtshort[0]['amount_type'];			
		$sel_product=$rows_pdtshort[0]['sel_product'];	
		$limited_qty=$rows_pdtshort[0]['limited_qty'];	//ควรได้รับจริงในบิลนี้		
		$start_baht=$rows_pdtshort[0]['start_baht'];
		$end_baht=$rows_pdtshort[0]['end_baht'];		
		$check_date_start=$rows_pdtshort[0]['check_date_start'];	
		$check_date_end=$rows_pdtshort[0]['check_date_end'];		
		if($amount_type=='N'){
			$field_amount='net_amt';
		}else if($amount_type=='G'){
			$field_amount='amount';
		}			
		$sql_doc="SELECT *
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							member_id = '$member_no' 	AND 
							$field_amount BETWEEN '$start_baht' AND '$end_baht' AND
							status_no = '00' AND
							doc_tp <> 'DN' AND
							birthday_card_st <> 'Y' AND
							flag<>'C' AND doc_date BETWEEN '$check_date_start' AND '$check_date_end'";//2014-01-17-2015-01-20	
		$rows_doc=$this->db->fetchAll($sql_doc);
		//*WR13012015 ========== ต่ออายุ/เปลี่ยนบัตร ===========
		if(empty($rows_doc)){
			$sql_m="SELECT refer_member_id
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND 
							status_no IN('01','05') AND
							member_id = '$member_no' 	AND 
							flag<>'C' AND doc_date >= '$check_date_start' ";
			$rows_m=$this->db->fetchAll($sql_m);
			if(!empty($rows_m)){
				$member_no=$rows_m[0]['refer_member_id'];
				$sql_doc="SELECT *
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							member_id = '$member_no' 	AND 
							$field_amount BETWEEN '$start_baht' AND '$end_baht' AND
							status_no = '00' AND
							doc_tp <> 'DN' AND
							birthday_card_st <> 'Y' AND
							flag<>'C' AND
							doc_date <= '$check_date_end'";		
							//doc_date BETWEEN '$check_date_start' AND '$check_date_end'";					
				$rows_doc=$this->db->fetchAll($sql_doc);
				
			}
		}
		//*WR13012015 ========== ต่ออายุ/เปลี่ยนบัตร ===========
		else{
			//ตรวจสอบมีการเปลี่ยนหรือต่อบัตรในช่วง 2014-12-17 ขึ้นไป ต้องเอาบิลบัตรเก่าหรือใหม่มาแสดงด้วย
			$sql_chk3="SELECT refer_member_id FROM trn_diary1 WHERE member_id = '$member_no' AND status_no IN('01','05') AND  doc_date >='$check_date_start'";
			$rows_chk3=$this->db->fetchALL($sql_chk3);
			if(!empty($rows_chk3)){
				$old_member_no=$rows_chk3[0]['refer_member_id'];
				$sql_doc="SELECT *
						FROM 
							trn_diary1 
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							branch_id='$this->m_branch_id' AND
							member_id  IN('$member_no','$old_member_no') 	AND 
							$field_amount BETWEEN '$start_baht' AND '$end_baht' AND
							status_no = '00' AND
							doc_tp <> 'DN' AND
							birthday_card_st <> 'Y' AND
							birthday_card_st <> '' AND
							flag<>'C' AND doc_date BETWEEN '$check_date_start' AND '$check_date_end'";
				$rows_doc=$this->db->fetchAll($sql_doc);
			}
		}
		$arr_r=array();
		$j=0;		
		foreach($rows_doc as $data){
					$doc_no=$data['doc_no'];
					$doc_date=$data['doc_date'];
					//check รับซ้ำ
					$re_chk='N';
					$sql_rechk="SELECT doc_no as doc_no_dn FROM trn_diary1 
										WHERE 
											doc_tp='DN' AND refer_doc_no='$doc_no' AND
											flag<>'C' AND 
											doc_date BETWEEN '$start_date' AND '$end_date'";
					$row_rechk=$this->db->fetchAll($sql_rechk);
					if(!empty($row_rechk)){
						$doc_no_receive=$row_rechk[0]['doc_no_dn'];
						$sql_rechk2="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no ='$doc_no_receive' AND product_id IN('25681','25684','25685','25689')";						
						$n_rechk2=$this->db->fetchOne($sql_rechk2);
						if($n_rechk2>0){
							$re_chk='Y';
						}						
					}
					$sql_d2="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code='$play_promo_code'";
					$n_d2=$this->db->fetchOne($sql_d2);
					if($n_d2 < $limited_qty){
								$arr_r[$j]['doc_date']=$data['doc_date'];
								$arr_r[$j]['doc_no']=$data['doc_no'];
								$arr_r[$j]['flag']=$data['flag'];
								$arr_r[$j]['member_id']=$data['member_id'];
								$arr_r[$j]['status_no']=$data['status_no'];
								$arr_r[$j]['refer_member_id']=$data['refer_member_id'];
								$arr_r[$j]['amount']=$data['amount'];
								$arr_r[$j]['net_amt']=$data['net_amt'];
								$arr_r[$j]['paid']=$data['paid'];
								$arr_r[$j]['cashier_id']=$data['cashier_id'];
								$arr_r[$j]['application_id']=$data['application_id'];
								$arr_r[$j]['status_receive']=$re_chk;
								$arr_r[$j]['sel_product']=$sel_product;
								$arr_r[$j]['limited_qty']=$limited_qty;
								$j++;		
					}
			}//foreach
			return $arr_r;		
	}//func
	
	 function chkFreeShortGiftSet($doc_no,$member_no,$promo_code,$aa=''){
			/**
			 * * create : 05012015
			 * * ตรวจสอบตามเลขที่บิล
			 * *
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			$msg_status="";
			$sql_short="SELECT * FROM promo_short WHERE promo_code='$promo_code'";	
			$row_short=$this->db->fetchAll($sql_short);
			if(!empty($row_short)){
				$play_promo_code=$row_short[0]['play_promo_code'];				
			}
			//check $play_promo_code
			$sql_chkbill="SELECT* FROM trn_diary1 WHERE doc_no='$doc_no'";
			$rows_chkbill=$this->db->fetchAll($sql_chkbill);
			if(!empty($rows_chkbill)){
				$arr_product=array();
				$net_amt=$rows_chkbill[0]['net_amt'];
				if($net_amt>=1000 && $net_amt<2000){
					//OX08020814 1000
					$sql_chkbill_detail="SELECT* FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code='OX08020814'";
					$rows_chkbill_detail=$this->db->fetchAll($sql_chkbill_detail);
					if(empty($rows_chkbill_detail)){
						//ค้าง 2 ตัว
						$arr_product[0]['product_id']="25681";
						$arr_product[1]['product_id']="25684";
					}else if(count($arr_product)<2){
						$i=0;
						foreach($rows_chkbill_detail as $data){
							if($data['product_id']!=''){
								$arr_product[$i]['product_id']=$data['product_id'];
								$i++;
							}
						}//foreach
						
						if($arr_product[0]['product_id']=='25681'){
							$arr_product[0]['product_id']="25684";
						}else if($arr_product[0]['product_id']=='25684'){
							$arr_product[0]['product_id']="25681";
						}
					}else{
						$msg_status="ไม่มีสินค้าค้างในบิลนี้ กรุณาตรวจสอบอีกครั้ง";
					}	
					self::setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);	
					
				}else if($net_amt>=2000){
					//OX08040814 2000
					//ค้าง 4 ตัว
//					$arr_product[0]['product_id']="25681";
//					$arr_product[1]['product_id']="25684";
//					$arr_product[2]['product_id']="25685";
//					$arr_product[3]['product_id']="25689";

				
					
					$arr_product1 = array("25681", "25684", "25685", "25689");					
					$sql_chkbill_detail="SELECT* FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code IN('OX08020814','OX08040814')";
					$rows_chkbill_detail=$this->db->fetchAll($sql_chkbill_detail);
					$k=0;
					foreach($rows_chkbill_detail as $arr_bill){
							if (in_array($arr_bill['product_id'], $arr_product1)) {
							    $arr_product2[$k]=$arr_bill['product_id'];
							    $k++;
							}
					}
					
					$arr_product=array();
					$v=0;
					for($i=0;$i<count($arr_product1);$i++){
							if (!in_array($arr_product1[$i], $arr_product2)) {
							    $arr_product[$v]['product_id']=$arr_product1[$i];
							    $v++;
							}
					}
				  self::setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);	
				}else{
					$msg_status="ยอดซื้อสุทธิไม่ถึง 2000 บาท กรุณาตรวจสอบอีกครั้ง";
				}
			}else{
				$msg_status="ไม่พบรหัสเลขที่เอกสาร กรุณาตรวจสอบอีกครั้ง";
			}//end if
			
			return $msg_status;
	}//func
	
	function getBillShortPowerC($member_no,$promo_code){
		/**
		 * @desc
		 * * create : 19082013
		 * * modify : 19082013
		 * @return Array List
		 */
		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		$sql_pdtshort="SELECT* FROM promo_short WHERE promo_code ='$promo_code'";
		$rows_pdtshort=$this->db->fetchAll($sql_pdtshort);
		$start_date=$rows_pdtshort[0]['start_date'];
		$end_date=$rows_pdtshort[0]['end_date'];		
		$amount_type=$rows_pdtshort[0]['amount_type'];			
		$sel_product=$rows_pdtshort[0]['sel_product'];	
		$limited_qty=$rows_pdtshort[0]['limited_qty'];	
		//get doc_No
		$sql_doc="SELECT 
									doc_date,doc_no,refer_doc_no,flag,member_id,status_no,refer_member_id,amount,net_amt,paid,cashier_id,application_id
								FROM 
									trn_diary1 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									member_id = '$member_no' 	AND 
									net_amt >='1200' AND
									status_no IN('00','02') AND
									flag<>'C' AND
									doc_date BETWEEN '2013-08-01' AND '2013-09-30'";	
			$rows_doc=$this->db->fetchAll($sql_doc);		
			$arr_r=array();
			$j=0;
			//get detail
			foreach($rows_doc as $data){
					$doc_no=$data['doc_no'];
					$doc_date=$data['doc_date'];
					//check รับซ้ำ
					$re_chk='N';
//					$sql_rechk="SELECT COUNT(*) FROM trn_diary1 WHERE doc_date >='$doc_date' AND doc_tp='DN' AND refer_doc_no='$doc_no' ";					
//					$n_rechk=$this->db->fetchOne($sql_rechk);
//					if($n_rechk>0){
//						$re_chk='Y';
//					}
					$sql_d2="SELECT COUNT(*) 
									FROM trn_diary2 WHERE doc_no='$doc_no' AND 
											product_id	IN ('25350', '25351', '25352', '25353', '25354', '25355', '25356', '25464', '25465')";
					$n_d2=$this->db->fetchOne($sql_d2);
					if($n_d2>0){					
							$arr_r[$j]['doc_date']=$data['doc_date'];
							$arr_r[$j]['doc_no']=$data['doc_no'];
							$arr_r[$j]['flag']=$data['flag'];
							$arr_r[$j]['member_id']=$data['member_id'];
							$arr_r[$j]['status_no']=$data['status_no'];
							$arr_r[$j]['refer_member_id']=$data['refer_member_id'];
							$arr_r[$j]['amount']=$data['amount'];
							$arr_r[$j]['net_amt']=$data['net_amt'];
							$arr_r[$j]['paid']=$data['paid'];
							$arr_r[$j]['cashier_id']=$data['cashier_id'];
							$arr_r[$j]['application_id']=$data['application_id'];
							$arr_r[$j]['status_receive']=$re_chk;
							$arr_r[$j]['sel_product']=$sel_product;
							$arr_r[$j]['limited_qty']=$limited_qty;
							$j++;			
					}
			}//foreach				
			return $arr_r;
	}//func
	
	function getBillShortOPS($member_no,$promo_code){
		/**
		 * @desc ใช้ดูประวัติการซื้อของบิลสินค้า short
		 * * create : 28062013
		 * * modify : 28062013
		 * @return Array List
		 */
		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		$sql_pdtshort="SELECT* FROM promo_short WHERE promo_code ='$promo_code'";
		$rows_pdtshort=$this->db->fetchAll($sql_pdtshort);
		$start_date=$rows_pdtshort[0]['start_date'];
		$end_date=$rows_pdtshort[0]['end_date'];		
		$amount_type=$rows_pdtshort[0]['amount_type'];			
		$sel_product=$rows_pdtshort[0]['sel_product'];	
		$limited_qty=$rows_pdtshort[0]['limited_qty'];	
		$sql_doc="SELECT 
									doc_date,doc_no,refer_doc_no,flag,member_id,status_no,refer_member_id,amount,net_amt,paid,cashier_id,application_id
								FROM 
									trn_diary1 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									member_id = '$member_no' 	AND 
									net_amt >='1200' AND
									status_no IN('00','02') AND
									flag<>'C' AND
									doc_date IN('2013-05-02','2013-05-09','2013-05-16')";			
			$rows_doc=$this->db->fetchAll($sql_doc);		
			$arr_r=array();
			$j=0;
			foreach($rows_doc as $data){
					$doc_no=$data['doc_no'];
					$doc_date=$data['doc_date'];
					//check รับซ้ำ
					$re_chk='N';
					$sql_rechk="SELECT COUNT(*) FROM trn_diary1 WHERE doc_date >='$doc_date' AND doc_tp='DN' AND refer_doc_no='$doc_no' ";					
					$n_rechk=$this->db->fetchOne($sql_rechk);
					if($n_rechk>0){
						$re_chk='Y';
					}
					$sql_d2="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code='OX07261212'";
					$n_d2=$this->db->fetchOne($sql_d2);
					if($n_d2<1){					
							$arr_r[$j]['doc_date']=$data['doc_date'];
							$arr_r[$j]['doc_no']=$data['doc_no'];
							$arr_r[$j]['flag']=$data['flag'];
							$arr_r[$j]['member_id']=$data['member_id'];
							$arr_r[$j]['status_no']=$data['status_no'];
							$arr_r[$j]['refer_member_id']=$data['refer_member_id'];
							$arr_r[$j]['amount']=$data['amount'];
							$arr_r[$j]['net_amt']=$data['net_amt'];
							$arr_r[$j]['paid']=$data['paid'];
							$arr_r[$j]['cashier_id']=$data['cashier_id'];
							$arr_r[$j]['application_id']=$data['application_id'];
							$arr_r[$j]['status_receive']=$re_chk;
							$arr_r[$j]['sel_product']=$sel_product;
							$arr_r[$j]['limited_qty']=$limited_qty;
							$j++;			
					}
			}//foreach				
			return $arr_r;
	}//func
	
	function getBillShortRWB($member_no,$promo_code){
			/**
			 * @desc ใช้ดูประวัติการซื้อของบิลสินค้า short
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_pdtshort="SELECT* FROM promo_short WHERE promo_code ='$promo_code'";
			$rows_pdtshort=$this->db->fetchAll($sql_pdtshort);
			$start_date=$rows_pdtshort[0]['start_date'];
			$end_date=$rows_pdtshort[0]['end_date'];		
			$amount_type=$rows_pdtshort[0]['amount_type'];			
			$sql_doc="SELECT 
									doc_date,doc_no,flag,member_id,status_no,refer_member_id,amount,net_amt,paid,cashier_id,application_id
								FROM 
									trn_diary1 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									member_id = '$member_no' 	AND 
									net_amt >='1500' AND
									status_no IN('00','02') AND
									flag<>'C' AND
									doc_date BETWEEN '$start_date' AND '$end_date'";													
			$rows_doc=$this->db->fetchAll($sql_doc);		
			$arr_r=array();
			$j=0;
			foreach($rows_doc as $data){
					$arr_r[$j]['doc_date']=$data['doc_date'];
					$arr_r[$j]['doc_no']=$data['doc_no'];
					$arr_r[$j]['flag']=$data['flag'];
					$arr_r[$j]['member_id']=$data['member_id'];
					$arr_r[$j]['status_no']=$data['status_no'];
					$arr_r[$j]['refer_member_id']=$data['refer_member_id'];
					$arr_r[$j]['amount']=$data['amount'];
					$arr_r[$j]['net_amt']=$data['net_amt'];
					$arr_r[$j]['paid']=$data['paid'];
					$arr_r[$j]['cashier_id']=$data['cashier_id'];
					$arr_r[$j]['application_id']=$data['application_id'];
					$arr_r[$j]['status_receive']='N';
					$j++;			
			}//foreach
			return $arr_r;
		}//func
		
		public function chkFreeShortPowerC($doc_no,$member_no,$promo_code,$member_tp,$product_id,$quantity){
			/**
			 * @desc WR28062013
			 * * ตรวจสอบตามเลขที่บิล
			 * *
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			$msg_status="";
			$sql_chk="SELECT COUNT(*) FROM promo_short WHERE promo_code='$promo_code' AND play_product_id='$product_id'";	
			$n_chk=$this->db->fetchOne($sql_chk);			
			if($n_chk>0){
				$arr_product=array();	
				$i=0;
				while($i<$quantity){			
					$arr_product[$i]['product_id']=$product_id;						
					$i++;
				}	
				self::setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);				
			}else{
				$msg_status="รหัสสินค้านี้ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง";
			}
			return $msg_status;
		}//func
		
		public function chkFreeShortOPS($doc_no,$member_no,$promo_code,$member_tp,$product_id,$quantity){
			/**
			 * @desc WR28062013
			 * * ตรวจสอบตามเลขที่บิล
			 * *
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			$msg_status="";
			$sql_chk="SELECT COUNT(*) FROM promo_short WHERE promo_code='$promo_code' AND play_product_id='$product_id'";	
			$n_chk=$this->db->fetchOne($sql_chk);			
			if($n_chk>0){
				$arr_product=array();			
				//for($i=0;$i<$quantity;$i++){		
				$i=0;
				while($i<$quantity){			
					$arr_product[$i]['product_id']=$product_id;						
					$i++;
				}		
				self::setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);				
			}else{
				$msg_status="รหัสสินค้านี้ไม่ร่วมโปรโมชั่น กรุณาตรวจสอบอีกครั้ง";
			}
			return $msg_status;
		}//func
		
		public function chkFreeShortRWB($doc_no,$member_no,$promo_code,$member_tp){
			/**
			 * @desc WR26022013
			 * @desc ปรับปรุงให้ใช้ได้กับทุกโปรที่มีการทำสินค้า Short
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			$msg_status="";
			$sql_chk_pro="SELECT product_id FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code IN('OX08050113','OX08030413','OX08120413') ";			
			$rows_chk_pro=$this->db->fetchAll($sql_chk_pro);
			if(empty($rows_chk_pro)){
				//check product in promo_check
				$sql_chk_pro="SELECT product_id FROM trn_diary2 WHERE doc_no='$doc_no'";			
				$rows_chk_pro=$this->db->fetchAll($sql_chk_pro);
				$chk_exist='N';
				foreach($rows_chk_pro as $dataP){
					$sql_chkp="SELECT COUNT(*) FROM promo_check WHERE promo_code='OX08050113' AND (product_id='ALL' OR product_id='$dataP[product_id]')";
					//$sql_chkp="SELECT COUNT(*) FROM promo_check WHERE promo_code='OX08050113' AND product_id='$dataP[product_id]'";
					$n_chkp=$this->db->fetchOne($sql_chkp);
					if($n_chkp>0){
						$chk_exist='Y';
						break;
					}
				}//foreach
				
				
				if($chk_exist=='Y'){
					$arr_product=array();
					$arr_product[0]['product_id']='24528';
					$arr_product[1]['product_id']='25493';
					$this->setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);
				}else{
					$msg_status="บิลนี้ไม่เป็นไปตามเงื่อนไขโปรโมชั่น";
					$msg_status='13';
				}
			}else{
				//case diary2 has promo_code
				$sql_chk_pro="SELECT * FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code ='OX08050113'";			
				$row_chkp=$this->db->fetchAll($sql_chk_pro);
				if(!empty($row_chkp)){					
					$arr_product=array();		
					if(count($row_chkp)>1){
						$msg_status="รับไปแล้ว";
						$msg_status='3';					
					}else{
						if($row_chkp[0]['product_id']=='24528'){
							$arr_product[0]['product_id']='25493';
						}else if($row_chkp[0]['product_id']=='25493'){
							$arr_product[0]['product_id']='24528';
						}
						
						$this->setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);
						
					}
					
				}else{
					$sql_chk_pro="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code ='OX08030413'";			
					$n_chk_pro=$this->db->fetchOne($sql_chk_pro);
					if($n_chk_pro>0){
						$arr_product=array();
						$arr_product[0]['product_id']='24528';
						$this->setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);
					}else{
						$sql_chk_pro="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code ='OX08120413'";			
						$n_chk_pro=$this->db->fetchOne($sql_chk_pro);
						if($n_chk_pro>0){
							$arr_product=array();
							$arr_product[0]['product_id']='25493';
							$this->setShortToTemp($doc_no,$member_no,$promo_code,$arr_product);							
						}else{
							$msg_status="ไม่พบโปรโมชั่นหรือเงื่อนไขที่เข้าร่วม";
							$msg_status='7';
						}
					}
				}
				
			}//if
			return $msg_status;
		}//func
		
		private function setShortToTemp($doc_no,$member_no,$promo_code,$arr_product){
			/**
			 * @desc
			 * @return
			 */			
			$doc_no=strtoupper($doc_no);
			$member_no=trim($member_no);
			if(empty($arr_product)){
				return false;
			}		
			
			$k=0;
			foreach($arr_product as $data){		
					$arr_product=parent::browsProduct($data['product_id']);	
					if(empty($arr_product)){
						echo "2#ไม่มีสินค้าในสต๊อก";
						exit();
					}
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
					$res_set=$this->setCshTempReturn($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
						$status_no,$product_status,$application_id,$card_status,
						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo);						
					$k++;
				}//foreach
		}//func
		
		function setCshTempReturn($doc_tp,$promo_code,$employee_id,$member_no='',$product_id,$quantity,
							$status_no,$product_status,$application_id,$card_status,
							$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
							$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo){
			/**
			 * @desc for product return only
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
			 * @return  
			 */
		   
			if(trim($member_no)==''){
				$discount_percent='';
				$member_percent1='';
				$member_percent2='';
				$co_promo_percent='';
				$coupon_percent='';
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
			
			if($this->promo_tp=='F'){
					$this->discount_percent='';			
					$this->member_percent1='';
					$this->member_percent2='';
					$this->co_promo_percent='';
					$this->coupon_percent='';
			}else{
					$this->discount_percent=$discount_percent;			
					$this->member_percent1=$member_percent1;//% ค่าส่วนลดสมาชิกที่1	
					$this->member_percent2=$member_percent2;
					$this->co_promo_percent=$co_promo_percent;
					$this->coupon_percent=$coupon_percent;
			}
			
			$this->status_no=$status_no;
			$arr_product_status=explode("-",$product_status);
			$this->product_status=$arr_product_status[0];		
			
			
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
			$this->product_id=$this->getProductReturn($this->product_id,$this->quantity,$status_no);//*WR10012013 prepend $status_no for product tester
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
									  `tax_type`='$this->tax_type',
									  `computer_no`='$this->m_computer_no',
									  `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert);		
					parent::decreaseStock($this->product_id,$this->quantity);
					$proc_status=1;
				}
			}
			return $proc_status."#".$this->amount;
		}//func
		
		public function getProductReturn($product_id="",$quantity,$status_no=""){
			/**
			 * @desc : for product return only
			 * @param String $product_id
			 * @return $product_id
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
			$found=false;
			if($product_id!=""){
				if(substr($product_id,0,3)=='885' && strlen($product_id)==13){
						//assume product_id is barcode
						$sql_nb="SELECT product_id 
										FROM com_product_master 
											WHERE 
												corporation_id='$this->m_corporation_id' AND 
												company_id='$this->m_company_id' AND
												barcode='$product_id'";	
						$row_nb=$this->db->fetchCol($sql_nb);
						if(count($row_nb)>0){
							$found=true;
							$product_id=$row_nb[0];	
						}else{					
							$sql_np="SELECT COUNT(*) 
											FROM `com_product_master` 
												WHERE 
													corporation_id='$this->m_corporation_id' AND 
													company_id='$this->m_company_id' AND
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
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											product_id='$product_id'";
						$n_np=$this->db->fetchOne($sql_np);
						if($n_np>0){
							$found=true;
						}
				 }
				
			}//
			if($found){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
				//*WR10012013 prepend $status_no for product tester	not for sale except bill 19
				if(substr($product_id,0,2)=='11' && $status_no!='19'){
					return '4';
				}
			
				//check onhand on com_stock_master
				$arr_year=explode("-",$this->doc_date);
				$year=$arr_year[0];
				$month=$arr_year[1];				
				$sql_stk="SELECT COUNT(*) 
									FROM com_stock_master 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND
											year='$year' AND
											month='$month' AND
											product_id='$product_id' AND
											'$quantity'<=onhand-allocate";
				
				$n_stk=$this->db->fetchOne($sql_stk);
				if($n_stk>0){
					return $product_id;
				}else{
					return '2';//not found product in com_stock_master or onland = 0
				}
			}else{
				return '1';// not found product in com_product_master
			}
		}//func
	
}//class