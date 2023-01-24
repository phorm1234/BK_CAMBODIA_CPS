<?php
  class Model_Ecommerce extends Model_PosGlobal{
  		/**
  		 * @desc 16092013
  		 * Enter description here ...
  		 */
  		function __construct(){
			parent::__construct();	
			$this->doc_tp="VT";
		}//func
		
		function decodeReceiveGood($string,$key) {
			$key = sha1($key);
			$strLen = strlen($string);
			$keyLen = strlen($key);
			for ($i = 0; $i < $strLen; $i+=2) {
				$ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
				if ($j == $keyLen) { $j = 0; }
				$ordKey = ord(substr($key,$j,1));
				$j++;
				$hash .= chr($ordStr - $ordKey);
			}
			return $hash;
		}//func
		
		function allocateStock($user_action){
			/**
			 * @desc
			 * @create 20042017
			 * @return null
			 */
			$url_stock="http://transferservices.ssup.co.th/pos/ord_getstock.php?shop_no=".$this->m_branch_id."&user=".$user_action;
			$json = file_get_contents($url_stock);
			$arr_json=json_decode($json);
			if(empty($arr_json)) {
				return true;
			}			
			$sql_clsecom="TRUNCATE TABLE `com_stock_ecom`";
			$this->db->query($sql_clsecom);
			foreach($arr_json->ordStock as $objStk) {				
				$sql_add="INSERT INTO com_stock_ecom
								SET
									 corporation_id='$objStk->corporation_id',
									 company_id ='$objStk->company_id',
									 branch_id='$objStk->branch_id',
									 product_id='$objStk->product_id',
									 onhand='$objStk->onhand',
									 allocate='$objStk->allocate',
									 reg_date=CURDATE(),
									 reg_time=CURTIME(),
									 reg_user='$user_action'";
				$this->db->query($sql_add);
			}//foreach
			
			$arr_trans=array();
			$arr_today=explode("-",$this->doc_date);
			$y=$arr_today[0];
			$m=$arr_today[1];
			$m=intval($m);
			foreach($arr_json->ordStock as $objStk) {
				$product_id=$objStk->product_id;
				$allocate=$objStk->allocate;
				$sql_trn2="SELECT SUM(quantity) AS sum_qty FROM trn_diary2 
								WHERE 
									product_id='$product_id' AND flag<>'C' AND 
									co_promo_code='OMNI' AND doc_date > DATE_FORMAT( CURDATE( ) , '%Y-%m-%d' ) - INTERVAL 7 DAY";				
				$qty_trn=$this->db->fetchOne($sql_trn2);				
				if($qty_trn>0){
					//update local
					$qty_allocate=$allocate-$qty_trn;									
				}else{
					$qty_allocate=$allocate;
				}
				
				//*WR24042017
				$sql_chkonhand="SELECT onhand FROM com_stock_master WHERE year='$y' AND month='$m' AND product_id='$product_id' ";
				$qty_onhand=$this->db->fetchOne($sql_chkonhand);	
				$chk_xallocate=$allocate*3;
				if($qty_onhand>=$chk_xallocate){
					$sql_upd_local="UPDATE com_stock_master
					SET allocate='$qty_allocate'
					WHERE year='$y' AND month='$m' AND product_id='$product_id'";
					$this->db->query($sql_upd_local);
						
					$sql_upd_local2="UPDATE com_stock_ecom
					SET onhand='$qty_allocate'
					WHERE branch_id='$this->m_branch_id' AND product_id='$product_id'";
					$this->db->query($sql_upd_local2);
				}else{
					$sql_upd_local="UPDATE com_stock_master
					SET allocate='0'
					WHERE year='$y' AND month='$m' AND product_id='$product_id'";
					$this->db->query($sql_upd_local);
						
					$sql_upd_local2="UPDATE com_stock_ecom
					SET onhand='0'
					WHERE branch_id='$this->m_branch_id' AND product_id='$product_id'";
					$this->db->query($sql_upd_local2);
				}//end if	
				
			}//foreach
			//update to server
			@file_get_contents("http://transferservices.ssup.co.th/pos/ord_updstock.php?shop_no=".$this->m_branch_id."&user=".$user_action);			
		}//func
		
		function getNotifications($order_status){
			/**
			 * @desc
			 * @create 17032017
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//$strSQL = "SELECT * FROM `com_order_notifications` WHERE order_status='$order_status'";
			$strSQL="SELECT a.order_no, a.order_status, b.subject, b.description
						FROM trn_order_status AS a
						INNER JOIN `com_order_notifications` AS b ON a.order_status = b.order_status
						WHERE a.order_no NOT
						IN (						
							SELECT order_no
							FROM `trn_order_status`
							WHERE order_status
								IN (
									'C', 'D'
								)
						)
						ORDER BY a.reg_date DESC , a.reg_time DESC	";			
			$rows_noty=$this->db->fetchAll($strSQL);
			$ord_chk_exist='';
			$arrNoty=array();
			$i=0;
			foreach($rows_noty as $data){
				$order_no=$data['order_no'];
				if($order_no!=$ord_chk_exist){
					$ord_chk_exist=$order_no;
					$arrNoty[$i]['order_no']=$data['order_no'];
					$arrNoty[$i]['description']=$data['description'];
					$i++;
				}else{
					continue;
				}
				
			}
			return $arrNoty;
		}//func
		
		function NewGuid() {
			$s = strtoupper(md5(uniqid(rand(),true)));
			$guidText =
			substr($s,0,8) . '-' .
			substr($s,8,4) . '-' .
			substr($s,12,4). '-' .
			substr($s,16,4). '-' .
			substr($s,20);
			return $guidText;
		}//func
		
		function capturePhoto(){
			/**
			 * @desc
			 * @return
			 */			
			$path_folder=date("Ym");			
			$file_name=$this->NewGuid() . ".jpg";
			$command="/usr/bin/lwp-request http://127.0.0.1:8080/0/action/snapshot > /dev/null";
			exec( $command );
			usleep(900000);
			
			$directoryName="/var/www/pos/htdocs/sales/cmd/webcam/ecom/photo/$path_folder";			
			$cmd_cd="cd /var/www/pos/htdocs/sales/cmd/webcam/";
			exec($cmd_cd);
			//The name of the directory that we need to create.
			
			if(!is_dir($directoryName)){
				mkdir($directoryName, 0777, true);
			}
			
			$s_copy="/var/www/shop/cam1/snapshot.jpg";
			$t_copy="/var/www/pos/htdocs/sales/cmd/webcam/ecom/photo/$path_folder/".$file_name;			
			$cp_photo="cp $s_copy $t_copy";
			exec($cp_photo);
			$cmd_chmodfolder="chmod 777 $t_copy";
			exec( $cmd_chmodfolder);
			/*$delete="rm -f /var/www/shop/cam1/*";
			 exec( $delete );*/
			$path_img="/sales/cmd/webcam/ecom/photo/$path_folder/$file_name";
			return $path_img;
		}//func
		
		
		function saveReceiveGoods($qstr_receive,$type_receive){
			/**
			 * @desc check and save data to receive
			 * @create 03032017
			 * @return message is receive
			 * @assume $qstr_receive as $order_no
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$orArray = array();
			$qstr_receive=trim($qstr_receive);
			if($qstr_receive==''){
				$orArray['status'] = 'N';
				$orArray['order_no'] = '';
				$orArray['msg'] = "ไม่พบรายการยืนยันใบสั่งสินค้า";
				return $orArray;
			}
						
			$sql_ord="SELECT doc_no, remark2 FROM `trn_diary1_or_web` WHERE remark2 = '$qstr_receive'";
			$row_ord=$this->db->fetchAll($sql_ord);
			if(!empty($row_ord)){
				$order_no=$row_ord[0]['doc_no'];				
				$sql_chkorder="SELECT doc_no,remark2 FROM trn_diary1 WHERE refer_doc_no='$order_no' AND flag<>'C'";
				$r_chkorder=$this->db->fetchAll($sql_chkorder);
				if(!empty($r_chkorder)){
					$doc_no=$r_chkorder[0]['doc_no'];
					if($r_chkorder[0]['remark2']!=''){
						$orArray['status'] = 'N';
						$orArray['order_no'] = '';
						$orArray['msg'] = "ใบสั่งสินค้า {$order_no} ตามเลขที่ใบรับสินค้า {$qstr_receive} ถูกรับไปแล้ว";
						return $orArray;
					}
					
					$sql_upd_receive="UPDATE trn_diary1 SET remark2='$qstr_receive' WHERE doc_no='$doc_no'";
					$res_upd=$this->db->query($sql_upd_receive);
					if($res_upd){
						$orArray['status'] = 'Y';
						$orArray['order_no'] = $order_no;
						$orArray['msg'] = "บันทึกการรับสินค้าตามใบสั่งสินค้า  {$order_no} สมบูรณ์";
					}else{
						$orArray['status'] = 'N';
						$orArray['order_no'] = $order_no;
						$orArray['msg'] = "ระบบมีปัญหาไม่สามารถบันทึการรับสินค้าได้";
					}
				}else{
					$orArray['status'] = 'N';
					$orArray['order_no'] = '';
					$orArray['msg'] = "ไม่พบรายการยืนยันใบสั่งสินค้า  {$order_no}";
				}//end if $r_chkorder
			}else{
				$orArray['status'] = 'N';
				$orArray['order_no'] = '';
				$orArray['msg'] = "ไม่พบรายการยืนยันใบสั่งสินค้า ตามเลขที่ใบรับสินค้า";
			}//end if			
			return $orArray;			
		}//func
		
		function orderStatus($doc_no,$action_status,$user_action=''){
			/**
			 * @desc set order acton in local
			 * @create 02032017
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$r_action='N';
			$sql_chkexist = "SELECT COUNT(*)  FROM  `trn_order_status` 	WHERE order_no='$doc_no' AND order_status='$action_status'";
			$n_chkexist=$this->db->fetchOne($sql_chkexist);
			if($n_chkexist<1) {
				$sql_action="INSERT INTO `trn_order_status`
									SET
									`corporation_id`='$this->m_corporation_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`order_no`='$doc_no',
									`order_status`='$action_status',
									`date_status`=CURDATE(),
									`order_active`='1',
									`reg_date`=CURDATE(),
									`reg_time`=CURTIME(),
									`reg_user`='$user_action',
									`upd_date`='',
									`upd_time`='',
									`upd_user`=''";
				$res_action=$this->db->query($sql_action);
				if($res_action){
					$r_action='Y';
				}
			}
			return $r_action;
		}//func
		
		function cmpPickingConfirmOrder($doc_no){
			/**
			 * @desc check head compare detail of picking with order
			 * @create 28022017
			 * @return Boolean
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//check status confirm order
			$orArray = array();
			$sql_horder="SELECT amount,quantity,discount,net_amt FROM trn_diary1_or_web WHERE doc_no='$doc_no'";
			$row_horder=$this->db->fetchAll($sql_horder);
			if(!empty($row_horder)){
				$order_quantity=$row_horder[0]['quantity'];
				$order_net_amt=$row_horder[0]['net_amt'];
				
				$sql_picking="SELECT SUM(amount) AS s_amount,SUM(quantity) AS s_quantity,SUM(discount) AS s_discount,SUM(net_amt) AS s_net_amt
								 FROM trn_tdiary2_sl WHERE doc_no='$doc_no'";
				$row_picking=$this->db->fetchAll($sql_picking);
				if(!empty($row_picking)){
					$p_quantity=$row_picking[0]['s_quantity'];
					$p_net_amt=$row_picking[0]['s_net_amt'];
					
					$order_quantity=intval($order_quantity);
					$p_quantity=intval($p_quantity);
					if($p_quantity!=$order_quantity){
						$orArray['success'] = 'N';
						$orArray['msg_error'] = "จำนวนที่จัด Picking ({$p_quantity}) ไม่เท่ากับจำนวนใบสั่ง Order ({$order_quantity}) กรุณาตรวจสอบอีกครั้ง";
					}else if($p_net_amt!=$order_net_amt){
						$order_net_amt=number_format($row_horder[0]['net_amt'],'2','.',',');
						$p_net_amt=number_format($row_picking[0]['s_net_amt'],'2','.',',');
						$orArray['success'] = 'N';
						$orArray['msg_error'] = "ยอดที่จัด Picking ({$p_net_amt}) ไม่เท่ากับยอดใบสั่ง Order ({$order_net_amt}) กรุณาตรวจสอบอีกครั้ง";
					}else{
						$orArray['success'] = 'Y';
						$orArray['msg_error'] = "";						
					}
					
				}else{
					$orArray['success'] = 'N';
					$orArray['msg_error'] = "ไม่พบรายการจัดสินค้า Picking {$order}";
				}//end if $row_picking
			}else{
				$orArray['success'] = 'N';
				$orArray['msg_error'] = "ไม่พบเลขที่เอกสาร {$order}";
			}//end if $row_order
			return $orArray;
		}//func
		
		function chkProductPicking($doc_no,$product_id,$quantity='1'){
			/**
			*  @desc
			*  @create 17022017
			 * @return
			 */		
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");		
			$product_id=parent::getProduct($product_id,$quantity,$status_no,'');
			if($product_id=='2'){
				//stock loss
				$proc_status='2';
				return $proc_status."#".$product_id;
			}
			//var_dump($product_id);
			
			$arr_product=parent::browsProduct($product_id);	
			
			$quantity=intval($quantity);
			$stock_st='-1';
			$proc_status=1;
			if(!empty($arr_product)){
				$sql_pdtorder="SELECT COUNT(*) FROM trn_diary2_or_web WHERE product_id='$product_id' AND doc_no='$doc_no'";
				$n_pdtorder=$this->db->fetchOne($sql_pdtorder);
				if($n_pdtorder<1){
					$proc_status='4';
				}else{
					$sql_pdtorder="SELECT SUM(quantity) AS sum_quantity_order FROM trn_diary2_or_web WHERE product_id='$product_id' AND doc_no='$doc_no'";
					$row_pdtorder=$this->db->fetchAll($sql_pdtorder);//master order
					$sql_pdtorder_temp="SELECT SUM(quantity) AS sum_quantity_order_temp FROM trn_tdiary2_or_web WHERE product_id='$product_id' AND doc_no='$doc_no'";
					$row_pdtorder_temp=$this->db->fetchAll($sql_pdtorder_temp);//temp order
					$chk_qty=$quantity+$row_pdtorder_temp[0]['sum_quantity_order_temp'];
					$bb=$row_pdtorder[0]['sum_quantity_order'];				
					if($chk_qty>$row_pdtorder[0]['sum_quantity_order']){
						$proc_status='5';
					}else{
						//---------------------------------------------------------------------- START --------------------------------------------------------
						$balance=intval($arr_product[0]['onhand'])-$arr_product[0]['allocate'];
						if($balance<$quantity){
							//stock ขาด
							$proc_status='2';
						}
						//---------------------------------------------------------------------- END ------------------------------------------------------------
					}
					
				}//end if check from com_product_expire
			}else{
				//ไม่พบสินค้าในทะเบียน
				$proc_status='0';
			}
			return $proc_status."#".$product_id;
		}//func
		
		function getHeadOrder($doc_no){
			/**
			 * @desc
			 * @create 16022017
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_oh="SELECT* FROM trn_diary1_or_web WHERE doc_no='$doc_no'";
			$row_h=$this->db->fetchAll($sql_oh);
			$objPos=new Model_PosGlobal();
			$row_branch=$objPos->getBranch();
			$row_company=$objPos->getCompany();
			if(count($row_branch)>0){
				$sql_qty_details="SELECT SUM(quantity) FROM trn_diary2_or_web WHERE doc_no='$doc_no'";
				$qty_details=$this->db->fetchOne($sql_qty_details);
				$row_h[0]['quantity']=$qty_details;
				$row_h[0]['branch_seq']=$row_branch[0]['branch_seq'];
				$row_h[0]['branch_name']=$row_branch[0]['branch_name'];
				$row_h[0]['branch_name_e']=$row_branch[0]['branch_name_e'];
				$row_h[0]['branch_tel']=$row_branch[0]['branch_phone'];
				$row_h[0]['company_name_print']=$row_company[0]['company_name_print'];
				$row_h[0]['tax_id']=$row_company[0]['tax_id'];
			}else{
				$row_h[0]['branch_name']='';
				$row_h[0]['branch_name_e']='';
				$row_h[0]['branch_tel']='';
				$row_h[0]['company_name_print']='';
				$row_h[0]['tax_id']='';
			}
			
			return $row_h;
		}//func
		
		function getListOrder($doc_no){
			/**
			 * @desc
			 * @create 16022017
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_detail="
			SELECT
			a.seq,
			a.quantity,
			a.product_id,
			a.price,
			a.discount,
			a.amount,
			a.net_amt,
			a.member_discount1,
			a.member_discount2,
			b.name_product
			from
			trn_diary2_or_web as a inner join com_product_master as b
			on a.product_id=b.product_id
			where
			a.doc_no='$doc_no'";
			$row_l=$this->db->fetchAll($sql_detail);
			return $row_l;
		}//func
		
		function chkExistOrder($refer_doc_no){
			/***
			 * @modify : 16022017
			 * @var unknown
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk="SELECT COUNT(*) FROM trn_diary1 WHERE refer_doc_no='$refer_doc_no' AND flag<>'C' LIMIT 0,1";
			$n_chk=$this->db->fetchOne($sql_chk);		
			$json='';
			if($n_chk<1){
				$sql_h="SELECT `member_id` , `name` , `address1` , `address2` , `address3` FROM `trn_diary1_or_web` WHERE doc_no ='$refer_doc_no'";
				$row_h=$this->db->fetchAll($sql_h);
				return json_encode($row_h[0]);
			}
			return $json;						
		}//func		
				
	  	function getWebOrder(){
			/**
			 * @desc show list รายการที่ไม่มีใน refer_doc_no
			 * @return 25072013
			 */
	  		$sql_doc="SELECT l.doc_date, l.doc_no, l.doc_tp, l.status_no, l.flag
							FROM trn_diary1_or_web AS l
							WHERE l.doc_no NOT IN (
																	SELECT r.refer_doc_no
																	FROM trn_diary1 AS r
																	WHERE r.refer_doc_no = l.doc_no
																	AND r.flag <> 'C')";
	  		$row_doc=$this->db->fetchAll($sql_doc);
	  		return $row_doc;
		}//func
		
		function setWebOrderTemp($doc_no){
		}//func
		
		function getNewOrderPicking($user_action){
			/**
			 * @desc get new order web if exist by manual
			 * @create 15022017
			 */
			
			//----------------------------------------- START -------------------------------------------
			
			
			$order_no='';//get all new
			$res_get_order='N';		
			$url_head="http://transferservices.ssup.co.th/pos/ord_getorder.php?shop_no=".$this->m_branch_id."&order_no=".$order_no."&user=$user_action&act=getall&format=json";
			$json = file_get_contents($url_head);
			$arr_json=json_decode($json);
			if(empty($arr_json)) {
				return true;
			}			
			foreach($arr_json->ordHead as $dataH) {
				$doc_no=$dataH->doc_no;								
				////////////////////////////////////////////////////////// check if exist /////////////////////////////////////////////////
				$sql_chkorder="SELECT COUNT(*) FROM `trn_diary1_or_web` WHERE doc_no='$doc_no'";
				$n_chkorder=$this->db->fetchOne($sql_chkorder);
				if($n_chkorder>0){
					$sql_chkorder2="SELECT COUNT(*)  FROM `trn_order_status` WHERE order_no='$doc_no' AND (order_status = 'C' OR order_status = 'D') ";
					$n_chkorder2=$this->db->fetchOne($sql_chkorder2);
					if($n_chkorder2>0){
						continue;
					}else{
						$sql_del_head="DELETE FROM `trn_diary1_or_web` WHERE doc_no='$doc_no'";
						$res_del=$this->db->query($sql_del_head);
						$sql_del_list="DELETE FROM `trn_diary2_or_web` WHERE doc_no='$doc_no'";
						$res_del=$this->db->query($sql_del_list);
					}
				}
				////////////////////////////////////////////////////////// check if exist /////////////////////////////////////////////////
				$sql_addhead="INSERT INTO trn_diary1_or_web
				SET
				`corporation_id`='$dataH->corporation_id',
				`company_id`='$dataH->company_id',
				`branch_id`='$dataH->branch_id',
				`doc_date`='$dataH->doc_date',
				`doc_time`='$dataH->doc_time',
				`doc_no`='$dataH->doc_no',
				`doc_tp`='$dataH->doc_tp',
				`status_no`='$dataH->status_no',
				`flag`='$dataH->flag',
				`member_id`='$dataH->member_id',
				`member_percent`='$dataH->member_percent',
				`special_percent`='$dataH->special_percent',
				`refer_member_id`='$dataH->refer_member_id',
				`refer_doc_no`='$dataH->refer_doc_no',
				`quantity`='$dataH->quantity',
				`amount`='$dataH->amount',
				`amount_sale`='$dataH->amount_sale',
				`discount`='$dataH->discount',
				`discount_sale`='$dataH->discount_sale',
				`member_discount1`='$dataH->member_discount1',
				`member_discount2`='$dataH->member_discount2',
				`co_promo_discount`='$dataH->co_promo_discount',
				`coupon_discount`='$dataH->coupon_discount',
				`special_discount`='$dataH->special_discount',
				`other_discount`='$dataH->other_discount',
				`net_amt`='$dataH->net_amt',
				`vat`='$dataH->vat',
				`ex_vat_amt`='$dataH->ex_vat_amt',
				`ex_vat_net`='$dataH->ex_vat_net',
				`coupon_cash`='$dataH->coupon_cash',
				`coupon_cash_qty`='$dataH->coupon_cash_qty',
				`point_begin`='$dataH->point_begin',
				`point1`='$dataH->point1',
				`point2`='$dataH->point2',
				`redeem_point`='$dataH->redeem_point',
				`total_point`='$dataH->total_point',
				`co_promo_code`='$dataH->co_promo_code',
				`coupon_code`='$dataH->coupon_code',
				`special_promo_code`='$dataH->special_promo_code',
				`other_promo_code`='$dataH->other_promo_code',
				`application_id`='$dataH->application_id',
				`new_member_st`='$dataH->new_member_st',
				`birthday_card_st`='$dataH->birthday_card_st',
				`not_cn_st`='$dataH->not_cn_st',
				`paid`='$dataH->paid',
				`pay_cash`='$dataH->pay_cash',
				`pay_credit`='$dataH->pay_credit',
				`pay_cash_coupon`='$dataH->pay_cash_coupon',
				`credit_no`='$dataH->credit_no',
				`credit_tp`='$dataH->credit_tp',
				`bank_tp`='$dataH->bank_tp',
				`change`='$dataH->change',
				`pos_id`='$dataH->pos_id',
				`computer_no`='$dataH->computer_no',
				`user_id`='$dataH->user_id',
				`cashier_id`='$dataH->cashier_id',
				`saleman_id`='$dataH->saleman_id',
				`cn_status`='$dataH->cn_status',
				`refer_cn_net`='$dataH->refer_cn_net',
				`name`='$dataH->name',
				`address1`='$dataH->address1',
				`address2`='$dataH->address2',
				`address3`='$dataH->address3',
				`doc_remark`='$dataH->doc_remark',
				`create_date`='$dataH->create_date',
				`create_time`='$dataH->create_time',
				`cancel_id`='$dataH->cancel_id',
				`cancel_date`='$dataH->cancel_date',
				`cancel_time`='$dataH->cancel_time',
				`cancel_tp`='$dataH->cancel_tp',
				`cancel_remark`='$dataH->cancel_remark',
				`cancel_auth`='$dataH->cancel_auth',
				`keyin_st`='$dataH->keyin_st',
				`keyin_tp`='$dataH->keyin_tp',
				`keyin_remark`='$dataH->keyin_remark',
				`post_id`='$dataH->post_id',
				`post_date`='$dataH->post_date',
				`post_time`='$dataH->post_time',
				`transfer_ts`='$dataH->transfer_ts',
				`transfer_date`='$dataH->transfer_date',
				`transfer_time`='$dataH->transfer_time',
				`order_id`='$dataH->order_id',
				`order_no`='$dataH->order_no',
				`order_date`='$dataH->order_date',
				`order_time`='$dataH->order_time',
				`acc_name`='$dataH->acc_name',
				`bank_acc`='$dataH->bank_acc',
				`bank_name`='$dataH->bank_name',
				`tel1`='$dataH->tel1',
				`tel2`='$dataH->tel2',
				`dn_name`='$dataH->dn_name',
				`dn_address1`='$dataH->dn_address1',
				`dn_address2`='$dataH->dn_address2',
				`dn_address3`='$dataH->dn_address3',
				`remark1`='$dataH->remark1',
				`remark2`='$dataH->remark2',
				`deleted`='$dataH->deleted',
				`print_no`='$dataH->print_no',
				`reg_date`='$dataH->reg_date',
				`reg_time`='$dataH->reg_time',
				`reg_user`='$dataH->reg_user',
				`upd_date`='$dataH->upd_date',
				`upd_time`='$dataH->upd_time',
				`upd_user`='$dataH->upd_user'";
				$res_get_head=$this->db->query($sql_addhead);
				if($res_get_head){
					$this->orderStatus($doc_no,'A',$user_action);
				}
			}//foreach
			
			//Order Detail
			foreach($arr_json->ordList as $dataL) {
				$doc_no=$dataL->doc_no;
				$sql_addlist="INSERT INTO `trn_diary2_or_web`
				SET
				`corporation_id`='$dataL->corporation_id',
				`company_id`='$dataL->company_id',
				`branch_id`='$dataL->branch_id',
				`doc_date`='$dataL->doc_date',
				`doc_time`='$dataL->doc_time',
				`doc_no`='$dataL->doc_no',
				`doc_tp`='$dataL->doc_tp',
				`status_no`='$dataL->status_no',
				`flag`='$dataL->flag',
				`seq`='$dataL->seq',
				`seq_set`='$dataL->seq_set',
				`promo_code`='$dataL->promo_code',
				`promo_seq`='$dataL->promo_seq',
				`promo_pos`='$dataL->promo_pos',
				`promo_st`='$dataL->promo_st',
				`promo_tp`='$dataL->promo_tp',
				`product_id`='$dataL->product_id',
				`price`='$dataL->price',
				`price_sale`='$dataL->price_sale',
				`quantity`='$dataL->quantity',
				`stock_st`='$dataL->stock_st',
				`amount`='$dataL->amount',
				`amount_sale`='$dataL->amount_sale',
				`discount`='$dataL->discount',
				`discount_sale`='$dataL->discount_sale',
				`member_discount1`='$dataL->member_discount1',
				`member_discount2`='$dataL->member_discount2',
				`co_promo_discount`='$dataL->co_promo_discount',
				`coupon_discount`='$dataL->coupon_discount',
				`special_discount`='$dataL->special_discount',
				`other_discount`='$dataL->other_discount',
				`net_amt`='$dataL->net_amt',
				`cost`='$dataL->cost',
				`cost_amt`='$dataL->cost_amt',
				`promo_qty`='$dataL->promo_qty',
				`weight`='$dataL->weight',
				`exclude_promo`='$dataL->exclude_promo',
				`location_id`='$dataL->location_id',
				`product_status`='$dataL->product_status',
				`get_point`='$dataL->get_point',
				`discount_member`='$dataL->discount_member',
				`cal_amt`='$dataL->cal_amt',
				`tax_type`='$dataL->tax_type',
				`gp`='$dataL->gp',
				`point1`='$dataL->point1',
				`point2`='$dataL->point2',
				`discount_percent`='$dataL->discount_percent',
				`member_percent1`='$dataL->member_percent1',
				`member_percent2`='$dataL->member_percent2',
				`co_promo_percent`='$dataL->co_promo_percent',
				`co_promo_code`='$dataL->co_promo_code',
				`coupon_code`='$dataL->coupon_code',
				`coupon_percent`='$dataL->coupon_percent',
				`not_return`='$dataL->not_return',
				`lot_no`='$dataL->lot_no',
				`lot_expire`='$dataL->lot_expire',
				`lot_date`='$dataL->lot_date',
				`short_qty`='$dataL->short_qty',
				`short_amt`='$dataL->short_amt',
				`ret_short_qty`='$dataL->ret_short_qty',
				`ret_short_amt`='$dataL->ret_short_amt',
				`cn_qty`='$dataL->cn_qty',
				`cn_amt`='$dataL->cn_amt',
				`cn_tp`='$dataL->cn_tp',
				`cn_remark`='$dataL->cn_remark',
				`deleted`='$dataL->deleted',
				`reg_date`='$dataL->reg_date',
				`reg_time`='$dataL->reg_time',
				`reg_user`='$dataL->reg_user',
				`upd_date`='$dataL->upd_date',
				`upd_time`='$dataL->upd_time',
				`upd_user`='$dataL->upd_user'";
				$res_get_list=$this->db->query($sql_addlist);
				if($res_get_list){
					$res_get_order='Y';
				}
			}//end foreach
			return 'Y';
		}//func
		
		function getOrderPicking($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @create 1002017
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
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$this->order_no=$order_no;
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			$sql_numrows = "SELECT COUNT(*) FROM trn_diary1_or_web
									WHERE
										`corporation_id`='$this->m_company_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id'";
			$total=$this->db->fetchOne($sql_numrows);			
			$sql_order="SELECT
										id,
										doc_no,
										doc_date,
										member_id,
										quantity,										
										amount,
										net_amt,
										post_date,
										post_time
								FROM 
										trn_diary1_or_web
								WHERE
									`corporation_id`='$this->m_company_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' 	$sort $limit";
			$arr_list=$this->db->fetchAll($sql_order);
		
			if(count($arr_list)>0){
				$data['page'] = $page;
				$data['total'] = $total;
				//$i=1;
				$i=(($page*$rp)-$rp)+1;
				foreach($arr_list as $row){
					$doc_no=$row['doc_no'];
					$sql_action="SELECT* FROM trn_order_status WHERE order_no='$doc_no' AND order_active = '1' ORDER BY order_status DESC LIMIT 0,1";
					$row_action=$this->db->fetchAll($sql_action);
					if(!empty($row_action)){
						$order_status=$row_action[0]['order_status'];
						switch($order_status){
							case "A":
								$order_status_show='รับใบสั่งซื้อ';
								break;
							case "B":
								$order_status_show='พิมพ์ใบจัด';
								break;
							case "C":
								$order_status_show='ยืนยันใบจัด';
								break;
							case "D":
								$order_status_show='รับสินค้าแล้ว';
								break;
							default :
								$order_status_show='';
								break;							
						}
					}else{
						$order_status_show='';
						$order_status='A';
					}
					
					$rows[] = array(
							"id" => $row['id'],
							"cell" => array(
									$i,
									$row['doc_no'],
									$row['doc_date'],
									$row['post_date'],
									$row['post_time'],
									$row['member_id'],
									intval($row['quantity']),									
									number_format($row['net_amt'],'2','.',','),	
									$order_status_show,
									$order_status
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
		
		
		function getSumOrderPicking(){
			/**
			 * @name getSumProductExpireTemp
			 * @param
			 * @modify : 27042016
			 * @return array
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_sum="SELECT
				SUM(quantity) AS sum_quantity,
				SUM(discount) AS sum_discount,
				SUM(amount) AS sum_amount,
				SUM(net_amt) AS sum_net_amt,
				status_no
			FROM
				`trn_diary1_or_web`
			WHERE
				`corporation_id`='$this->m_company_id' AND
				`company_id`='$this->m_company_id' AND
				`branch_id`='$this->m_branch_id' ";
			$row_l2h=$this->db->fetchAll($sql_sum);
			if(!empty($row_l2h)){						
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('orderhead',$row_l2h[0],'yes');
				return $json;
			}
		}//func
		
		function getOrderTemp($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @ is old version
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
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
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
					$discount_tmp=$row['sum_discount'];
					$s_pos=strrpos($discount_tmp,'.');
					if($s_pos>0){
						$s_pos+=3;
						$discount_tmp=substr($discount_tmp,0,$s_pos);			
					}					
					$rows[] = array(
								"id" => $row['id'],
								"cell" => array(
											$i,										
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
		
		public function getPageTotal($rp){
			/**
			 * @name getPageTotal
			 * @desc
			 * @param $rp is row per page
			 * @return $cpage is total of page
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");		
			$sql_row = "SELECT count(*) 
							FROM `trn_tdiary2_sl` 
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
		
	   public function getSumOrderWeb(){
	       $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");		
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
			$row_l2h=$this->db->fetchAll($sql_suminv);
			if(!empty($row_l2h)){				
				$row_l2h[0]['sum_quantity']=intval($row_l2h[0]['sum_quantity']);
			 	$net_amt=$row_l2h[0]['sum_net_amt'];			 	 	
				//$xpoint=intval($xpoint);
				$xpoint=1;
				if($xpoint==0 || $xpoint=='') $xpoint=1;
				$point=0;			
				$point1=parent::getPoint1($net_amt);
				$point2=parent::getPoint2($net_amt);		
				$point1*=$xpoint;
				$point=$point1+$point2;
						
				$row_l2h[0]['point_total']=$point;
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('sumcsh',$row_l2h[0],'yes');	
				return $json;				
			}
	   }//func
	   
	   function setPdtPickingToConfirmOrder($order_no,$product_id,$quantity,$status_no){
	   	/**
	   	 * @desc set product picking to confirm order not fixed seq
	   	 * @create 17022017
	   	 * @return null
	   	 */
	   	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	   	$arr_product=parent::browsProduct($product_id);
	   	$name_product=$arr_product[0]['name_product'];
	   	/*
	   	$sql_max_seq="SELECT MAX(seq) FROM `trn_tdiary2_sl` WHERE doc_no='$order_no' ORDER BY seq DESC LIMIT 0,1";
	   	$max_seq=$this->db->fetchOne($sql_max_seq);
	   	if($max_seq<1){
	   		$max_seq=1;
	   	}else{
	   		$max_seq=$max_seq+1;
	   	}
	   	*/
	   	$quantity=intVal($quantity);
	   	$sql_chk_exist="SELECT COUNT(*) FROM trn_tdiary2_sl WHERE doc_no='$order_no'  AND product_id='$product_id' AND quantity='$quantity'";
	   	$nchk_exist=$this->db->fetchOne($sql_chk_exist);
	   	if($nchk_exist>0){
	   		$orArray['success'] = 'N';
	   		$orArray['msg_error'] = "ไม่สามารถคีย์รายการ รหัสสินค้า {$product_id} จำนวน {$quantity} ชิ้น ซ้ำได้";	   		
	   		return $orArray;
	   	}
	   
	   	$orArray = array();	   
	   	//check quantity with seq
	   	$sql_or2="SELECT* FROM trn_diary2_or_web WHERE doc_no='$order_no'  AND product_id='$product_id' AND quantity='$quantity'";
	   	$row_or2=$this->db->fetchAll($sql_or2);
	   	if(!empty($row_or2)){
	   		//---------------------------------- START ----------------------
	   		$doc_tp='SL';
	   		$stock_st='-1';
	   		$employee_id=$this->user_id;
	   		$seq=$row_or2[0]['seq'];
	   		$or_promo_code=$row_or2[0]['promo_code'];
	   		$or_amount=$row_or2[0]['amount'];
	   		$or_price=$row_or2[0]['price'];
	   		$or_net_amt=$row_or2[0]['net_amt'];
	   		$res_status='N';
	   		$this->db->beginTransaction();
	   		try{
	   			$sql_diary2="INSERT INTO `trn_tdiary2_sl`
	   			SET
	   			`corporation_id`='$this->m_corporation_id',
	   			`company_id`='$this->m_company_id',
	   			`branch_id`='$this->m_branch_id',
	   			`doc_date`='$this->doc_date',
	   			`doc_time`=CURTIME(),
	   			`doc_no`='$order_no',
	   			`doc_tp`='$doc_tp',
	   			`status_no`='$status_no',
	   			`seq`='$seq',
	   			`seq_set`='1',
	   			`promo_code`='$or_promo_code',
	   			`promo_seq`='',
	   			`promo_st`='',
	   			`promo_tp`='',
	   			`product_id`='$product_id',
	   			`name_product`='$name_product',
	   			`stock_st`='$stock_st',
	   			`price`='$or_price',
	   			`quantity`='$quantity',
	   			`amount`='$or_amount',
	   			`discount`='',
	   			`member_discount1`='',
	   			`member_discount2`='',
	   			`co_promo_discount`='',
	   			`coupon_discount`='',
	   			`special_discount`='',
	   			`other_discount`='',
	   			`net_amt`='$or_net_amt',
	   			`product_status`='',
	   			`get_point`='',
	   			`discount_member`='',
	   			`cal_amt`='',
	   			`tax_type`='',
	   			`discount_percent`='',
	   			`member_percent1`='',
	   			`member_percent2`='',
	   			`co_promo_percent`='',
	   			`coupon_percent`='',
	   			`computer_no`='$this->m_computer_no',
	   			`computer_ip`='$this->m_com_ip',
	   			`member_no`='',
	   			`reg_date`=CURDATE(),
	   			`reg_time`=CURTIME(),
	   			`reg_user`='IT'";
	   			$this->db->query($sql_diary2);
	   			$res_status='Y';
	   			$this->db->commit();
	   		}catch(Zend_Db_Exception $e){
	   			$res_status=$e->getMessage();
	   			$this->db->rollBack();
	   		}//end try
	   		if($res_status){
	   			//parent::decreaseStock($product_id,$quantity,'Y');
	   			$orArray['success'] = 'Y';
	   			$orArray['msg_error'] = '';
	   		}
	   		//---------------------------------- START ----------------------
	   	}else{
	   		$orArray['success'] = 'N';
	   		$orArray['msg_error'] = 'กรุณาตรวจสอบ จำนวนสินค้า รหัสสินค้า <br>ตรงกับลำดับรายการในใบจัดสินค้าหรือไม่?';
	   	}//end if
	   	return $orArray;
	   }//func
	   
	   
	   function setPdtPickingToConfirmOrderFixSeq28022017($order_no,$product_id,$quantity,$status_no){
		   	/**
		   	 * @desc set product picking to confirm order
		   	 * @create 17022017
		   	 * @return null
		   	 */
		   	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		   	$arr_product=parent::browsProduct($product_id);
		   	$name_product=$arr_product[0]['name_product'];
		   	$sql_max_seq="SELECT MAX(seq) FROM `trn_tdiary2_sl` WHERE doc_no='$order_no' ORDER BY seq DESC LIMIT 0,1";	   		   	
		   	$max_seq=$this->db->fetchOne($sql_max_seq);
		   	if($max_seq<1){
		   		$max_seq=1;
		   	}else{
		   		$max_seq=$max_seq+1;
		   	}
		   	
		   	$orArray = array();
		   	
		   	//check quantity with seq
		   	$sql_or2="SELECT* FROM trn_diary2_or_web WHERE doc_no='$order_no' AND seq='$max_seq' AND product_id='$product_id' AND quantity='$quantity'";
		   	$row_or2=$this->db->fetchAll($sql_or2);
		   	if(!empty($row_or2)){
		   		//---------------------------------- START ----------------------
		   		$doc_tp='SL';
		   		$stock_st='-1';
		   		$employee_id=$this->user_id;
		   		$or_promo_code=$row_or2[0]['promo_code'];
		   		$or_amount=$row_or2[0]['amount'];
		   		$or_price=$row_or2[0]['price'];
		   		$or_net_amt=$row_or2[0]['net_amt'];
		   		$res_status='N';
		   		$this->db->beginTransaction();
		   		try{
		   			$sql_diary2="INSERT INTO `trn_tdiary2_sl`
							   			SET
							   			`corporation_id`='$this->m_corporation_id',
							   			`company_id`='$this->m_company_id',
							   			`branch_id`='$this->m_branch_id',
							   			`doc_date`='$this->doc_date',
							   			`doc_time`=CURTIME(),
							   			`doc_no`='$order_no',
							   			`doc_tp`='$doc_tp',
							   			`status_no`='$status_no',
							   			`seq`='$max_seq',
							   			`seq_set`='1',
							   			`promo_code`='$or_promo_code',
							   			`promo_seq`='',
							   			`promo_st`='',
							   			`promo_tp`='',
							   			`product_id`='$product_id',
							   			`name_product`='$name_product',
							   			`stock_st`='$stock_st',
							   			`price`='$or_price',
							   			`quantity`='$quantity',
							   			`amount`='$or_amount',
							   			`discount`='',
							   			`member_discount1`='',
							   			`member_discount2`='',
							   			`co_promo_discount`='',
							   			`coupon_discount`='',
							   			`special_discount`='',
							   			`other_discount`='',
							   			`net_amt`='$or_net_amt',
							   			`product_status`='',
							   			`get_point`='',
							   			`discount_member`='',
							   			`cal_amt`='',
							   			`tax_type`='',
							   			`discount_percent`='',
							   			`member_percent1`='',
							   			`member_percent2`='',
							   			`co_promo_percent`='',
							   			`coupon_percent`='',
							   			`computer_no`='$this->m_computer_no',
							   			`computer_ip`='$this->m_com_ip',
							   			`member_no`='',
							   			`reg_date`=CURDATE(),
							   			`reg_time`=CURTIME(),
							   			`reg_user`='IT'";
		   			$this->db->query($sql_diary2);
		   			$res_status='Y';
		   			$this->db->commit();
		   		}catch(Zend_Db_Exception $e){
		   			$res_status=$e->getMessage();
		   			$this->db->rollBack();
		   		}//end try
		   		if($res_status){
		   			parent::decreaseStock($product_id,$quantity,'Y');		   			
		   			$orArray['success'] = 'Y';
		   			$orArray['msg_error'] = '';
		   		}
		   		//---------------------------------- START ----------------------
		   	}else{		   		
		   		$orArray['success'] = 'N';
		   		$orArray['msg_error'] = 'กรุณาตรวจสอบ จำนวนสินค้า รหัสสินค้า <br>ตรงกับลำดับรายการในใบจัดสินค้าหรือไม่?';
		   	}//end if	   					
		    return $orArray;
	   }//func
		
		function initOrdTemp(){
			/**
			 * @desc
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->db->query('TRUNCATE TABLE `trn_tdiary1_sl`');
			$this->db->query('TRUNCATE TABLE `trn_tdiary2_sl`');
		}//func
		
		function saveOrdWeb($doc_no,$user_id,$cashier_id,$saleman_id){
			/**
			 * @desc confirm order by picking and gen bill
			 * @create : 17092013
			 * @modify : 22022017
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");			
			$sql_l2h="SELECT * FROM `trn_diary1_or_web` WHERE doc_no='$doc_no'";	
			$row_orh=$this->db->fetchAll($sql_l2h);
			$this->status_no=$row_orh[0]['status_no'];
			$this->quantity=$row_orh[0]['quantity'];	
			$this->discount=$row_orh[0]['discount'];
			$this->member_discount1= $row_orh[0]['member_discount1'];			
		 	$this->member_discount2= $row_orh[0]['member_discount2'];						 	
		 	$this->co_promo_discount= $row_orh[0]['co_promo_discount']; 
		 	$this->coupon_discount= $row_orh[0]['coupon_discount'];
		 	$this->special_discount= $row_orh[0]['special_discount'];
		 	$this->other_discount= $row_orh[0]['other_discount'];
		 	$this->amount=$row_orh[0]['amount'];		
		 	$this->net_amt=$row_orh[0]['net_amt'];		 
		 	$member_no=$row_orh[0]['member_id'];
		 	$name=$row_orh[0]['name'];
		 	$address1=$row_orh[0]['address1'];
		 	$address2=$row_orh[0]['address2'];
		 	$address3=$row_orh[0]['address3'];
		 	$dn_name=$row_orh[0]['dn_name'];
		 	$dn_address1=$row_orh[0]['dn_address1'];
		 	$dn_address2=$row_orh[0]['dn_address2'];
		 	$dn_address3=$row_orh[0]['dn_address3'];		 	
		 	$status_no="00";
		 	$this->doc_tp="VT";
		 	$pay_credit=$this->net_amt;
		 	$paid="0002";
		 	$credit_tp = "Omni";
		 	$bank_tp = "Omni";		 	
		 	
		 	$point1=0;
		 	$point2=0;
		 	$total_point=0;
		 	if($member_no!=''){		 		
		 		$point1=parent::getPoint1($this->net_amt);
		 		$point2=parent::getPoint2($this->net_amt);
		 	}
		 	$total_point=$point1+$point2;
		 	
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
					
			//create doc_no for diary
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);		
			
			//cal tax vat
			$this->vat=parent::calVat($this->net_amt);
			$this->vat=round($this->vat,2);		
			
			$this->db->beginTransaction();
			$this->chk_trans=TRUE;
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
									`refer_doc_no`='$doc_no',
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
									`dn_name`='$dn_name',
									`dn_address1`='$dn_address1',
									`dn_address2`='$dn_address2',
									`dn_address3`='$dn_address3',									
									`refer_cn_net`='',
									`co_promo_code`='',
									`birthday_card_st`='',
									`reg_date`=CURDATE(),
								  	`reg_time`=CURTIME(),
								    `reg_user`='$saleman_id'";	
				$this->db->query($sql_add_l2h);				
				
				//update doc_no in trn_tdiary2_sl
				$sql_upd_trn_tdiary2_sl="UPDATE 
													`trn_tdiary2_sl`
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
									 CURDATE(),
									  CURTIME(),
									  `reg_user`
								FROM
									`trn_tdiary2_sl`
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
												`dn_name`,
												`dn_address1`,
												`dn_address2`,
												`dn_address3`,												
												`refer_cn_net`,												
												`co_promo_code`,
												`birthday_card_st`,	
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
												`dn_name`,
												`dn_address1`,
												`dn_address2`,
												`dn_address3`,
												`refer_cn_net`,												
												`co_promo_code`,
												`birthday_card_st`,												
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
					echo $this->msg_error;
				}
				$this->chk_trans=FALSE;
			}//end try
			
		
			
			if($this->chk_trans){
				//////// COMPLETE TRANSACTION ///////////
				$sql_updd2="UPDATE trn_diary2 SET co_promo_code='OMNI' WHERE doc_no='$this->m_doc_no' AND promo_code<>'FREEBAG' ";
				$this->db->query($sql_updd2);
				//*WR 15032017 update stock after gen bill
				$sql_td2="SELECT* FROM trn_tdiary2_sl WHERE doc_no='$this->m_doc_no' ORDER BY seq";				
				$row_td2=$this->db->fetchAll($sql_td2);
				foreach($row_td2 as $data){
					$product_id=$data['product_id'];
					$quantity=$data['quantity'];
					//parent::decreaseStock($product_id,$quantity,'Y');					
					$stock_st = -1;
					$arr_date=explode("-",$this->doc_date);
					$month=intval($arr_date[1]);
					$year=intval($arr_date[0]);
					$sql_stockmaster="SELECT
					onhand,allocate
				 	FROM
					com_stock_master
				 	WHERE
					corporation_id='$this->m_corporation_id' AND
					company_id='$this->m_company_id' AND
					branch_id='$this->m_branch_id' AND
					product_id='$product_id' AND
					month='$month' AND
					year='$year'";
					$row_onhand=$this->db->fetchAll($sql_stockmaster);
					if(!empty($row_onhand)){
						$n_onhand= $row_onhand[0]['onhand']+($quantity*$stock_st);
						$n_allocate= $row_onhand[0]['allocate']+($quantity*$stock_st);
						$sql_updstock="UPDATE 	com_stock_master
						SET
						onhand='$n_onhand',
						allocate='$n_allocate',
						upd_date='$this->doc_date',
						upd_time=CURTIME(),
						upd_user='$this->employee_id'
						WHERE
						corporation_id='$this->m_corporation_id' AND
						company_id='$this->m_company_id' AND
						branch_id='$this->m_branch_id' AND
						product_id='$product_id' AND
						month='$month' AND
						year='$year'";
						$this->db->query($sql_updstock);
					}					
				}//foreach
				
				parent::TrancateTable("trn_tdiary2_sl");
				parent::TrancateTable("trn_tdiary1_sl");
				$arrJson = array("status" =>"1","doc_no"=>$this->m_doc_no, "doc_tp" => $this->doc_tp,
									"thermal_printer"=>$this->m_thermal_printer,"$net_amt"=>$this->net_amt,
									"status_no"=>$status_no,"branch_tp"=>$this->m_branch_tp,"cashdrawer"=>$this->m_cashdrawer,"msg"=>"");
				
				////////COMPLETE TRANSACTION///////////
			}else{
				$arrJson = array("status" =>"0","doc_no"=>$this->m_doc_no, "doc_tp" => $this->doc_tp,
						"thermal_printer"=>$this->m_thermal_printer,"$net_amt"=>$this->net_amt,
						"status_no"=>$status_no,"branch_tp"=>$this->m_branch_tp,"cashdrawer"=>$this->m_cashdrawer,"msg"=>$this->msg_error);				
			}//if	
			return json_encode($arrJson);
		}//func
	   
  }//class
