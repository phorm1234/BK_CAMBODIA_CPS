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
			$objCsh=new Model_Cashier();
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
					$res_set=$objCsh->setCshTemp($doc_tp,$promo_code,$employee_id,$member_no,$product_id,$quantity,
						$status_no,$product_status,$application_id,$card_status,
						$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
						$co_promo_percent,$coupon_percent,$promo_st,$promo_tp,$promo_amt,$promo_amt_type,$check_repeat,$web_promo);
					$k++;
				}//foreach
		}//func
	
}//class