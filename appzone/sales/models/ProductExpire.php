<?php
	class Model_ProductExpire extends Model_PosGlobal{
		private $doc_dt;
		private $edit_dt;
		private $expense_dt;
		private $account_code;
		private $amount;
		function __construct(){
			parent::__construct();	
		}//func
		
		function updStockProductExpire($doc_no=''){
			/**
			 * @desc
			 * @create 30/062016
			 * @return TRUE
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($doc_no!=''){
				//case cancel bill
				return true;
			}else{
				$sql_app="UPDATE  `trn_diary2_ex`
							SET
							`short_qty`=`short_qty` + `ret_short_qty`,
							`ret_short_qty`='0'
							WHERE
							`cn_remark`='$this->m_com_ip' AND  `ret_short_qty`<>'0'";
				@$this->db->query($sql_app);
			}//if				
			return true;
		}//func
		
		function delProductExpire($items){
			/**
			 * @desc
			 * @param String $items : id to remove
			 * @return Boolean True
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_diary1');
			$arr_items=explode("#",$items);
			if(empty($arr_items)) return FALSE;
			foreach($arr_items as $id_remove){
				if($id_remove=='') continue;
				$sql_chkdel="DELETE 	FROM trn_tdiary2_ex WHERE id='$id_remove' ";
				$row_chkdel=$this->db->query($sql_chkdel);
			}
			return TRUE;
		}//func
		
		function getProductExpire2Confirm($doc_no){
			/**
			 * 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			parent::TrancateTable("trn_tdiary2_ex");
			parent::TrancateTable("trn_tdiary1_ex");
			$sql_d1="SELECT 
							doc_no,doc_date,remark1
							FROM trn_diary1_ex 
						WHERE doc_no='$doc_no'";
			$rows_d1=$this->db->fetchAll($sql_d1);
			$doc_date=$rows_d1[0]['doc_date'];
			$remark=$rows_d1[0]['remark1'];
			
			$sql_d2="SELECT * FROM trn_diary2_ex WHERE doc_no='$doc_no'";
			$rows_d2=$this->db->fetchAll($sql_d2);
			foreach ($rows_d2 as $dataL){
				$product_id=$dataL['product_id'];
				$arr_product=parent::browsProduct($product_id);
				$name_product=$arr_product[0]['name_product'];
				$quantity=$dataL['quantity'];
				$price=$dataL['price'];
				$amount=$dataL['amount'];
				$net_amt=$dataL['net_amt'];
				$lot_date=$dataL['lot_date'];
				$sql_add="INSERT INTO trn_tdiary2_ex
							SET
								`corporation_id`='$this->m_corporation_id',
								`company_id`='$this->m_company_id',
								`branch_id`='$this->m_branch_id',
								`doc_date`='$doc_date',
								`lot_date`='$lot_date',
								`product_id`='$product_id',
								`quantity`='$quantity', 
								`price`='$price',								
								`amount`='$amount',
								`net_amt`='$net_amt',
								`computer_ip`='$this->m_com_ip',
								`computer_no`='$this->m_computer_no'
							";//echo $sql_add;exit();
				$this->db->query($sql_add);
			}//foreach
		}//func
		
		function bwsPdtExpireDocNO(){
			/**
			 * @desc
			 * @return
			 */
			$this->doc_time=date("H:i:s");
			
			$sql_l="SELECT
			DATE_FORMAT(doc_date,'%d/%m/%Y') as doc_date_show,
			doc_no,member_id,flag,status_no,doc_date,remark1
			FROM
			trn_diary1_ex
			WHERE
			corporation_id='$this->m_corporation_id' AND
			company_id='$this->m_company_id' AND
			branch_id='$this->m_branch_id' 
			ORDER BY doc_date DESC";
			$row_l=$this->db->fetchAll($sql_l);//AND flag NOT IN('Y','C') 
			return $row_l;
		}//func
		
		function getSumProductExpireTemp(){
			/**
			 * @name getSumProductExpireTemp			
			 * @param
			 * @modify : 27042016
			 * @return array
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_cn");
			$sql_sum="SELECT
			SUM(quantity) AS sum_quantity,
			SUM(discount) AS sum_discount,
			SUM(amount) AS sum_amount,
			SUM(net_amt) AS sum_net_amt,
			status_no
			FROM
			`trn_tdiary2_ex`
			WHERE
			`corporation_id`='$this->m_company_id' AND
			`company_id`='$this->m_company_id' AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no`='$this->m_computer_no' AND
			`computer_ip`='$this->m_com_ip'";
			$row_l2h=$this->db->fetchAll($sql_sum);
			if(!empty($row_l2h)){					
				$amount=$row_l2h[0]['sum_amount'];
				$net_amt=$amount;
				$row_l2h[0]['sum_net_amt']=$net_amt;
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumproductexpire',$row_l2h[0],'yes');
				return $json;
			}
		}//func
		
		function cancelProductExpire($doc_no,$doc_date,$remark,$saleman_id,$ros_employee_id=''){
			/**
			 * @desc
			 * @param
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_confirm="UPDATE trn_diary1_ex 
								SET 
									flag='C',cancel_id='$saleman_id',cancel_auth='$ros_employee_id',upd_date=CURDATE(),upd_time=CURTIME(),upd_user='$saleman_id' 
								WHERE doc_no='$doc_no'";
			$res_confirm=$this->db->query($sql_confirm);
			if($res_confirm){
				$sql_confirm="UPDATE trn_diary2_ex 
									SET 
											flag='C',upd_date=CURDATE(),upd_time=CURTIME(),upd_user='$saleman_id'
										WHERE doc_no='$doc_no'";
				$res_confirm=$this->db->query($sql_confirm);
				return '1#'.$doc_no;
			}else{
				return '0#'.$doc_no;
			}
		}//func
		
		function confirmProductExpire($doc_no,$doc_date,$remark,$saleman_id,$ros_employee_id){
			/**
			 * @desc
			 * @param
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_confirm="UPDATE trn_diary1_ex 
								SET flag='Y',user_id='$ros_employee_id',upd_date=CURDATE(),upd_time=CURTIME(),upd_user='$ros_employee_id' 
									WHERE doc_no='$doc_no'";
			$res_confirm=$this->db->query($sql_confirm);
			if($res_confirm){
				$sql_confirm="UPDATE trn_diary2_ex
				SET
				flag='Y',upd_date=CURDATE(),upd_time=CURTIME(),upd_user='$ros_employee_id'
				WHERE doc_no='$doc_no'";
				$res_confirm=$this->db->query($sql_confirm);
				return '1#'.$doc_no;
			}else{
				return '0#'.$doc_no;
			}
		}//func		
		
		function saveProductExpire($doc_no,$doc_date,$remark,$saleman_id){
					/**
					 * @desc
					 * @param
					 * @return
					 */
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
					$sql_chk_trns="SELECT 
											COUNT(*)
										FROM
											trn_tdiary2_ex
										WHERE
											`corporation_id`='$this->m_company_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`computer_no`='$this->m_computer_no' AND
											`computer_ip`='$this->m_com_ip' AND
											`doc_date`='$this->doc_date'";
					$n_chk_trns=$this->db->fetchOne($sql_chk_trns);
					if($n_chk_trns<1){
						$strResult= "0#ไม่พบการทำรายการ ไม่สามารถบันทึกได้#".$this->doc_tp."#Y";
						return $strResult;
						exit();
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
									`trn_tdiary2_ex`
								WHERE
									`corporation_id`='$this->m_company_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
									`computer_ip`='$this->m_com_ip' AND
									`doc_date`='$this->doc_date'";
					$row_l2h=$this->db->fetchAll($sql_l2h);
					//กำหนดให้เปิดบิลเล่นได้หลายโปรโมชั่น
					$this->status_no=$row_l2h[0]['status_no'];
					$this->quantity=$row_l2h[0]['sum_quantity'];		
					$this->net_amt=$this->amount;
					//check doc type
					$this->doc_tp='EX';
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
						
					
						
					//create doc_no for diary
					$this->m_doc_no=parent::getDocNumber($this->doc_tp);
					$this->db->beginTransaction();
					$this->chk_trans=TRUE;
					try{
						$this->doc_time=date("H:i:s");
						//start query
						$sql_add_l2h="INSERT INTO trn_tdiary1_ex
						SET
						`corporation_id`='$this->m_company_id',
						`company_id`='$this->m_company_id',
						`branch_id`='$this->m_branch_id',
						`doc_date`='$this->doc_date',
						`doc_time`='$this->doc_time',
						`doc_no`='$this->m_doc_no',
						`doc_tp`='$this->doc_tp',
						`status_no`='$this->status_no',
						`member_id`='',
						`member_percent`='',
						`special_percent`='',
						`refer_member_id`='',
						`refer_doc_no`='',
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
						`paid`='',
						`pay_cash`='',
						`pay_credit`='',
						`point_begin`='',
						`redeem_point`='',
						`point1`='',
						`point2`='',
						`total_point`='',
						`change`='',
						`coupon_code`='',
						`pay_cash_coupon`='',
						`credit_no`='',
						`credit_tp`='',
						`bank_tp`='',
						`computer_no`='$this->m_computer_no',
						`computer_ip`='$this->m_com_ip',
						`pos_id`='$this->m_pos_id',
						`user_id`='$user_id',
						`cashier_id`='$saleman_id',
						`saleman_id`='$saleman_id',
						`doc_remark`='',
						`name`='$name',
						`address1`='',
						`address2`='',
						`address3`='',
						`refer_cn_net`='',
						`co_promo_code`='',
						`birthday_card_st`='',
						`remark1`='$remark',
						`remark2`='',
						`special_day`='',
						`idcard`='',
						`mobile_no`='',
						`keyin_st`='',
						`keyin_tp`='',
						`keyin_remark`='',
						`reg_date`=CURDATE(),
						`reg_time`=CURTIME(),
						`reg_user`='$saleman_id'";
						$this->db->query($sql_add_l2h);
		
						//update doc_no in trn_tdiary2_sl
						$sql_upd_trn_tdiary2_ex="UPDATE
															`trn_tdiary2_ex`
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
						$this->db->query($sql_upd_trn_tdiary2_ex);
		
		
						//load data from trn_promotion_tmp1 to tdiary2
						$sql_diary2="INSERT INTO
						`trn_diary2_ex`
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
						`lot_date`,
						CURDATE(),
						CURTIME(),
						`reg_user`
						FROM
						`trn_tdiary2_ex`
						WHERE
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND
						branch_id='$this->m_branch_id' AND
						doc_no='$this->m_doc_no'";
						$this->db->query($sql_diary2);
		
						
						$sql_diary1="INSERT INTO trn_diary1_ex(
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
						`keyin_st`,
						`keyin_tp`,
						`keyin_remark`,
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
						`keyin_st`,
						`keyin_tp`,
						`keyin_remark`,
						`reg_date`,
						`reg_time`,
						`reg_user`
						FROM
						`trn_tdiary1_ex`
						WHERE
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND
						branch_id='$this->m_branch_id' AND
						doc_no='$this->m_doc_no'";
						$this->db->query($sql_diary1);
						
						$sql_inc_docno="UPDATE
												com_doc_no
											SET
												doc_no=doc_no+1,
												upd_date='$this->doc_date',
												upd_time='$this->doc_time',
												upd_user='$saleman_id'
												WHERE
												corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND
												branch_id='$this->m_branch_id' AND
												branch_no='$this->m_branch_no' AND
												doc_tp='$this->doc_tp'";
						$this->db->query($sql_inc_docno);
						
						
						$this->db->commit();
					}catch(Zend_Db_Exception $e){
						$this->db->rollBack();
						if($e->getCode()==23000){							
							$this->msg_error=" { <u>ไม่สามารถบันทึกเลขที่บิลซ้ำได้</u> } ";
						}else{
							$this->msg_error=$e->getMessage();							
						}
						$this->chk_trans=FALSE;
					}//end try
						
					if($this->chk_trans){
						//////// COMPLETE TRANSACTION ///////////	
						parent::TrancateTable("trn_tdiary2_ex");
						parent::TrancateTable("trn_tdiary1_ex");
						$strResult= "1#".$this->m_doc_no."#".$this->doc_tp;
						////////COMPLETE TRANSACTION///////////
					}else{
						$strResult= "0#".$this->msg_error."#".$this->doc_tp;
					}//if
					return $strResult;
		}//func
		
		
		public function getPageTotalProductExpire($rp){
			/**
			 * @name getPageTotal
			 * @desc
			 * @param $rp is row per page
			 * @return $cpage is total of page
			 * AND 	`doc_date`='$this->doc_date'";
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_row = "SELECT count(*)
			FROM `trn_tdiary2_ex`
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id' AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no`='$this->m_computer_no' AND
			`computer_ip`='$this->m_com_ip' ";
			$crow=$this->db->fetchOne($sql_row);
			$cpage=ceil($crow/$rp);
			return $cpage;
		}//func
		
		function initProductExpireTemp(){
			/**
			 * @desc
			 * @param String $ref_doc_no;
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");			
			$objPos=new Model_PosGlobal();
			$objPos->TrancateTable("trn_tdiary2_ex");
			$objPos->TrancateTable("trn_tdiary1_ex");
		}//func
		
		function getCshTempProductExpire($page,$qtype,$query,$rp,$sortname,$sortorder){
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
			 * AND			 * 
			`doc_date`='$this->doc_date' 
			AND
			a.doc_date='$this->doc_date'
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
				
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
				
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
				
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_diary1');
			$sql_numrows = "SELECT * FROM trn_tdiary2_ex
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id' AND
			`branch_id`='$this->m_branch_id' AND
			`computer_no`='$this->m_computer_no' AND
			`computer_ip`='$this->m_com_ip' ";
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
			a.net_amt as net_amt,a.lot_date,DATE_FORMAT(a.lot_date,'%d/%m/%Y') as lot_date_show
			FROM
			trn_tdiary2_ex as a LEFT join com_product_master as b
			ON(a.product_id=b.product_id)
			WHERE
			a.corporation_id='$this->m_corporation_id' AND
			a.company_id='$this->m_company_id' AND
			a.branch_id='$this->m_branch_id' AND
			a.computer_no='$this->m_computer_no' AND
			a.computer_ip='$this->m_com_ip' 
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
									$row['product_name'],
									$row['lot_date_show'],	
									number_format($row['price'],'2','.',','),
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
		
		function chkProductExpire($doc_date,$product_id,$quantity='1'){
			/**
			 * @name setCn()
			 * @desc
			 * @param String $status_no :
			 * @param String $doc_no_ref :
			 * @param String $product_id :
			 * @param Integer $quantity :
			 * @param String $cashier_id :
			 * @last modify:27042016
			 * @return
			 */
			$status_no='00';
			$doc_tp='EX';
			$quantity=intval($quantity);
			$stock_st='0';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				
			$product_id=parent::getProduct($product_id,$quantity,$status_no,'');
			$arr_product=parent::browsProduct($product_id);
			//print_r($arr_product);
			$proc_status=1;
			if(!empty($arr_product)){
				//check from com_product_expire
				$sql_pdtexpire="SELECT COUNT(*) FROM com_product_expire
				WHERE (product_id='$product_id' OR product_id='ALL') AND '$doc_date' BETWEEN start_date AND end_date ";
				$n_pdtexpire=$this->db->fetchOne($sql_pdtexpire);
				if($n_pdtexpire<1){
					$proc_status='4';
				}else{
					//---------------------------------------------------------------------- START --------------------------------------------------------
					$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
					if($balance<$quantity){
						//stock ขาด
						$proc_status='2';
					}
					//---------------------------------------------------------------------- END ------------------------------------------------------------
				}//end if check from com_product_expire
			}else{
				//ไม่พบสินค้าในทะเบียน
				$proc_status='0';
			}
			return $proc_status."#".$product_id;
		}//func
		
		
		function setProductExpire($doc_no,$remark,$doc_date,$manufac_date,$product_id,$quantity,$seq,$cashier_id){
			/**
			 * @name setCn()
			 * @desc
			 * @param String $status_no :
			 * @param String $doc_no_ref :
			 * @param String $product_id :
			 * @param Integer $quantity :
			 * @param String $cashier_id :
			 * @last modify:27042016
			 * @return
			 */
			$status_no='00';			
			$doc_tp='EX';
			$quantity=intval($quantity);	
			$stock_st='0';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");					
			
			$product_id=parent::getProduct($product_id,$quantity,$status_no,'');
			$arr_product=parent::browsProduct($product_id);
				
			$proc_status=0;
			if(!empty($arr_product)){
				
				$manufac_date_chk=$manufac_date;
				$arr_mnf=explode('-', $manufac_date_chk);
				$manufac_date_chk=$arr_mnf[0]."-".$arr_mnf[1]."-01";
				
				//check manu fac
				$chk_less_date= date('Y-m-01', strtotime('-18 month'));
				if($manufac_date_chk > $chk_less_date){
					return "5#0";
				}//func
				
				//check lock product expire
				$sql_chklock="SELECT COUNT(*) FROM com_product_expire_lock WHERE product_id='$product_id' AND '$doc_date' BETWEEN start_date AND end_date ";
				$n_chklock=$this->db->fetchOne($sql_chklock);
				if($n_chklock>0){
					return "6#0";
				}
				
				//check from com_product_expire
				$sql_pdtexpire="SELECT COUNT(*) FROM com_product_expire 
										WHERE (product_id='$product_id' OR product_id='ALL') AND '$doc_date' BETWEEN start_date AND end_date ";
				$n_pdtexpire=$this->db->fetchOne($sql_pdtexpire);
				if($n_pdtexpire<1){
					$proc_status='4';
				}else{
					//---------------------------------------------------------------------- START --------------------------------------------------------
					$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
					if($balance<$this->quantity){
						//stock ขาด
						$proc_status='2';
					}else{
						$product_name=$arr_product[0]['name_product'];
						$price=$arr_product[0]['price'];
						$unit=$arr_product[0]['unit'];
						$amount=$price * $quantity;
						$net_amt=$amount;
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
									doc_tp="EX"';
						$rowdoc=$this->db->fetchAll($sql_doc);
						if(count($rowdoc)>0){
							$this->m_branch_no=$rowdoc[0]['branch_no'];
							$stock_st=$rowdoc[0]['stock_st'];
						}
					
						$doc_time=date("H:i:s");
						$sql_seq="SELECT MAX(seq)  FROM trn_tdiary2_ex
						WHERE
						`corporation_id` ='$this->m_corporation_id' AND
						`company_id` ='$this->m_company_id' AND
						`branch_id` ='$this->m_branch_id' AND
						`doc_date` ='$this->doc_date' AND
						`computer_no` ='$this->m_computer_no' AND
						`computer_ip` ='$this->m_com_ip'";
						$max_seq=$this->db->fetchOne($sql_seq);
						$seq=$max_seq+1;
							
						//joke
						if($seq_set=='0'){
							$sql_cseq="SELECT seq_set
							FROM trn_tdiary2_ex
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
								if($seq_set=='0'){
									$seq_set=1;
								}
							}else{
								$sql_maxseq="SELECT MAX(seq_set) AS max_seq_set
								FROM trn_tdiary2_ex
								WHERE
								`corporation_id` ='$this->m_corporation_id' AND
								`company_id` ='$this->m_company_id' AND
								`branch_id` ='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip'";
								$max_seq_set=$this->db->fetchOne($sql_maxseq);
								$seq_set=$max_seq_set+1;
							}
						}//if
					
						$sql_pro_seq="SELECT MAX(promo_seq) AS max_promo_seq
						FROM trn_tdiary2_ex
						WHERE
						`corporation_id` ='$this->m_corporation_id' AND
						`company_id` ='$this->m_company_id' AND
						`branch_id` ='$this->m_branch_id' AND
						`promo_code`='$this->promo_code' AND
						`computer_no` ='$this->m_computer_no' AND
						`computer_ip` ='$this->m_com_ip'";
						$max_promo_seq=$this->db->fetchOne($sql_pro_seq);
						$promo_seq=$max_promo_seq+1;
					}
						
					$this->chk_trans='N';
					$this->db->beginTransaction();
					try{
						$sql_insert="INSERT INTO trn_tdiary2_ex
						SET
						`corporation_id` ='$this->m_corporation_id',
						`company_id` ='$this->m_company_id',
						`branch_id` ='$this->m_branch_id',
						`doc_date` ='$this->doc_date',
						`doc_time` ='$this->doc_time',
						`doc_no` ='',
						`doc_tp` ='$doc_tp',
						`status_no`='$status_no',
						`seq` ='$seq',
						`seq_set` ='$seq_set',
						`promo_code` ='',
						`promo_seq` ='',
						`promo_st` ='$promo_st',
						`promo_tp` ='',
						`product_id`='$product_id',
						`name_product`='$product_name',
						`unit`='',
						`stock_st` ='$stock_st',
						`price` ='$price',
						`quantity`='$quantity',
						`amount`='$amount',
						`discount`='',
						`member_discount1`='',
						`coupon_discount`='',
						`special_discount`='',
						`other_discount`='',
						`net_amt`='',
						`product_status`='',
						`get_point`='',
						`discount_member`='',
						`cal_amt`='',
						`discount_percent`='',
						`member_percent1`='',
						`member_percent2`='',
						`coupon_percent`='',
						`tax_type`='',
						`cn_qty`='',
						`cn_amt`='',
						`cn_tp`='',
						`lot_date`='$manufac_date',
						`promo_qty`='',
						`lot_expire`='',
						`lot_no`='',
						`cn_remark`='',
						`computer_no`='$this->m_computer_no',
						`computer_ip`='$this->m_com_ip',
						`reg_date`='$this->doc_date',
						`reg_time`=CURTIME(),
						`reg_user`='$this->employee_id'";
						$this->db->query($sql_insert);
						$proc_status='1';
						$this->chk_trans='Y';
						$this->db->commit();
					}catch(Zend_Db_Exception $e){
						$proc_status='3';
						$e->getMessage();
						$this->db->rollBack();
					}
					//---------------------------------------------------------------------- END ------------------------------------------------------------
				}//end if check from com_product_expire
			}else{
				//ไม่พบสินค้าในทะเบียน
				return "0#0";
			}
			return $proc_status."#".$amount;
		}//func
	}//class		
?>