<?php
	class Model_Accessory extends Model_PosGlobal{
		function __construct(){
			parent::__construct();	
			$this->curr_date=date("Y-m-d");
			if($this->doc_date){
				$arr_date=explode("-",$this->doc_date);
				$this->year=$arr_date[0];
				$this->month=intVal($arr_date[1]);
			}
		}//func
		
		function full_rmdir($dirname){
		    if ($dirHandle = @opendir($dirname)){
		        $old_cwd = getcwd();
		        chdir($dirname);
		        while ($file = readdir($dirHandle)){
		            if ($file == '.' || $file == '..') continue;
		            
		            if (is_dir($file)){
		                if (!full_rmdir($file)) return false;
		            }else{
		                if (!unlink($file)) return false;
		            }
		        }
		        closedir($dirHandle);
		        chdir($old_cwd);
		        if (!rmdir($dirname)) return false;
		        return true;
		    }else{
		        return false;
		    }
		}//func
		
	
		function exportTrnSummary(){
		    /**
		     * @desc
		     * @create 14032018
		     */
		    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		    $this->db->query("TRUNCATE TABLE `transfer_to_office`.`trn_summary` ");
		   $sql_diary="SELECT*
                             FROM
            					pos_ssup.trn_diary1
            				WHERE
            				`corporation_id`='$this->m_corporation_id' AND
            				`company_id`='$this->m_company_id' AND
            				`branch_id`='$this->m_branch_id' AND
            				`doc_tp` IN('SL','VT','CN','DN') AND 
            				`doc_date` BETWEEN DATE_SUB(DATE_FORMAT( NOW( ),'%Y-%m-01'),INTERVAL 1 MONTH) AND DATE_SUB( '$this->doc_date', INTERVAL 1 DAY)";
		   $rows_diary=$this->db->fetchAll($sql_diary);
		   $json_data=array();		  
		   foreach ($rows_diary as $data){
		       $json_array['TransactionId']=$data['id']; 
		       $json_array['InvoiceId']=$data['doc_no']; 
		       $json_array['ReciepId']=$data['doc_no']; 
		       $json_array['Date']=$data['doc_date']; 
		       $doc_no=$data['doc_no']; 
		       $sql_stime="SELECT seq, doc_date, doc_time
                                    FROM pos_ssup.trn_diary2
                                    WHERE doc_no = '$doc_no'
                                    ORDER BY seq ASC LIMIT 0 , 1";
		       $r_stime=$this->db->fetchAll($sql_stime);
		       $start_time=$r_stime[0]['doc_date']." ".$r_stime[0]['doc_time'];
		       
		       $sql_etime="SELECT seq, doc_date, doc_time
                                    FROM pos_ssup.trn_diary2
                                    WHERE doc_no = '$doc_no'
                                    ORDER BY seq DESC  LIMIT 0 , 1";
		       $r_etime=$this->db->fetchAll($sql_etime);
		       $end_time=$r_etime[0]['doc_date']." ".$r_etime[0]['doc_time'];
		       
		       $json_array['StartTime']=$start_time; 
		       $json_array['EndTime']=$end_time;
		       
		       $json_array['NumberOfCustomer']=$data['member_id']; 
		       
		       $json_array['SubTotal']=$data['amount']; 
		       $json_array['DiscountDollar']=$data['discount']; 
		       $json_array['DiscountRiel']='null'; 
		       $json_array['DiscountTypeId']='1'; 
		       $json_array['ReturnAmount']='null'; 
		       $json_array['RefundAmount']='null'; 
		       $json_array['Vat']=$data['vat']; 
		       $json_array['Net']=$data['net_amt']; 
		       
		       $json_array['GrandTotal']=$data['net_amt']+$data['vat']; 
		       
		       if($data['paid']=='0000'){
		           $json_array['PaymentDollar']=$data['pay_cash1']; 
		       }else if($data['paid']=='0002'){
		           $json_array['PaymentDollar']=$data['pay_credit']; 
		       }
		       
		       
		       $json_array['PaymentRiel']=$data['pay_cash2']; 
		       $json_array['ChangeDollar']=$data['change']; 
		       $json_array['ChangeRiel']='0.00'; 
		       
		       if($data['paid']=='0000'){
		           $json_array['PaymentMethodId']='1'; 
		       }else if($data['paid']=='0002'){
		           $json_array['PaymentMethodId']='2'; 
		       }
		       
		       $json_array['Cashier']=$data['saleman_id']; 
		       
		       if($data['flag']==''){
		           $json_array['StatusId']='1';
		       }else{
		           $json_array['StatusId']='5';
		       }		       
		       $json_array['Comment']=''; 
		     
		       $sql_insert="INSERT INTO transfer_to_office.trn_summary
                                    SET
                                        `TransactionId`='$json_array[TransactionId]',  	  	  	
                                        `InvoiceId`='$json_array[InvoiceId]', 	  	  	 
                                        `ReciepId`='$json_array[ReciepId]', 	  	  	 
                                        `Date`='$json_array[Date]',	  	 
                                        `StartTime`='$json_array[StartTime]',  	  	 
                                        `EndTime`='$json_array[EndTime]',  	  	  	 
                                        `NumberOfCustomer`='$json_array[NumberOfCustomer]',  	  	  	 
                                        `SubTotal`='$json_array[SubTotal]',  	  	 
                                        `DiscountDollar`='$json_array[DiscountDollar]',  	  	 
                                        `DiscountRiel`='$json_array[DiscountRiel]',	  	  	 
                                        `DiscountTypeId`='$json_array[DiscountTypeId]',  	  	 
                                        `ReturnAmount`='$json_array[ReturnAmount]',  	  	  	 
                                        `RefundAmount`='$json_array[RefundAmount]',	  	  	 
                                        `Vat`='$json_array[Vat]', 	  	  	 
                                        `Net`='$json_array[Net]',  	  	  	 
                                        `GrandTotal`='$json_array[GrandTotal]', 	  	  	 
                                        `PaymentDollar`='$json_array[PaymentDollar]',  	  	  	 
                                        `PaymentRiel`='$json_array[PaymentRiel]',	  	  	 
                                        `ChangeDollar`='$json_array[ChangeDollar]',  	  	  	 
                                        `ChangeRiel`='$json_array[ChangeRiel]', 	  	  	 
                                        `PaymentMethodId`='$json_array[PaymentMethodId]',  	  	  	 
                                        `Cashier`='$json_array[Cashier]',	  	 
                                        `StatusId`='$json_array[StatusId]', 	  	  	 
                                        `Comment`='$json_array[Comment]'";
		       $this->db->query($sql_insert);
		       array_push($json_data,$json_array);
		       
		   }//foreach
		   //file_put_contents("data.json", json_encode($json_data));
		   //echo json_encode($json_data);  
		   
		   $branch_id=$this->m_branch_id;
		   if($branch_id=="1100"){
		       $branch_id="1101";
		   }
		   $folder="DATA-".$branch_id;
		   $file_name=$branch_id.".json";
		   $path="/home/ss-".$branch_id."/Documents/";	
		//    echo $path;
		//    chdir($path);
		   if(is_dir($folder)){
		      $this->full_rmdir($folder);
		       system("rm -rf $file_name");
		   }
		   
		   mkdir("$folder",0777,TRUE);
		   chmod("$folder", 0777); 
		   chdir($folder);
		   file_put_contents("$file_name", json_encode($json_data));
		   
// 		   $fp = fopen('$file_name', 'w');
// 		   if(!fwrite($fp, json_encode($json_data))) { 
// 		       die('Error : File Not Opened. ' . mysql_error());
// 		   } 
// 		   fclose($fp);
		}//func		
		
		
		/**
		 * @desc transfer sales transaction to transfer_to_office db
		 * @create 07062017
		 * @return null
		 */
	
		function transferDailySales(){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->db->query("TRUNCATE TABLE `transfer_to_office`.`trn_diary1` ");
			$this->db->query("TRUNCATE TABLE `transfer_to_office`.`trn_diary2` ");
			//begin
			$this->db->beginTransaction();
			$chk_trans="N";
			try{
				//sleep(3);
				$sql_step3="
				INSERT INTO
				transfer_to_office.trn_diary1
				(
					`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`,
					`member_id`, `idcard`, `special_day`, `mobile_no`, `member_percent`, `special_percent`, `refer_member_id`,
					`refer_doc_no`, `quantity`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`,
					`coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`, `coupon_cash`, 
					`coupon_cash_qty`, `point_begin`, `point1`, `point2`, `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`,
					`special_promo_code`, `other_promo_code`, `application_id`, `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
					`pay_cash`, `pay_cash2`, `pay_credit`, `pay_cash_coupon`, `credit_no`, `credit_tp`, `bank_tp`, `change`, `change2`, `pos_id`,
					`computer_no`, `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`, `name`, `address1`, `address2`, `address3`,
					`doc_remark`, `create_date`, `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`, `cancel_remark`, `cancel_auth`,
					`keyin_st`, `keyin_tp`, `keyin_remark`, `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`, `order_id`, `order_no`,
					`order_date`, `order_time`, `acc_name`, `bank_acc`, `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
					`remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
				)
				SELECT
					`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`,
					`member_id`, `idcard`, `special_day`, `mobile_no`, `member_percent`, `special_percent`, `refer_member_id`,
					`refer_doc_no`, `quantity`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`,
					`coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`, `coupon_cash`, 
					`coupon_cash_qty`, `point_begin`, `point1`, `point2`, `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`,
					`special_promo_code`, `other_promo_code`, `application_id`, `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
					`pay_cash`, `pay_cash2`, `pay_credit`, `pay_cash_coupon`, `credit_no`, `credit_tp`, `bank_tp`, `change`, `change2`, `pos_id`,
					`computer_no`, `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`, `name`, `address1`, `address2`, `address3`,
					`doc_remark`, `create_date`, `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`, `cancel_remark`, `cancel_auth`,
					`keyin_st`, `keyin_tp`, `keyin_remark`, `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`, `order_id`, `order_no`,
					`order_date`, `order_time`, `acc_name`, `bank_acc`, `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
					`remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
				FROM
					pos_ssup.trn_diary1
				WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id' AND
				`branch_id`='$this->m_branch_id' AND
				`doc_tp` IN('SL','VT','CN','DN') AND 
				`doc_date` BETWEEN DATE_SUB(DATE_FORMAT( NOW( ),'%Y-%m-01'),INTERVAL 1 MONTH) AND DATE_SUB( '$this->doc_date', INTERVAL 1 DAY)";
				$this->db->query($sql_step3);
				//`doc_date` BETWEEN DATE_SUB( '$this->doc_date', INTERVAL 6 DAY ) AND DATE_SUB( '$this->doc_date', INTERVAL 1 DAY )
				//commit trans
				$this->db->commit();
				$chk_trans="Y";
			}catch(Zend_Db_Exception $e){
				$this->msg_error=$e->getMessage();
				echo $this->msg_error;
				//rollback trans
				$this->db->rollBack();
				$chk_trans="N";
			}//end try
				
			if($chk_trans=='Y'){
				//begin
				$this->db->beginTransaction();
				try{
					$sql_step3="
					INSERT INTO
					transfer_to_office.trn_diary2
					(
						`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`,
						`seq`, `seq_set`, `promo_code`, `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
						`quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`,
						`coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`, `promo_qty`, `weight`,
						`exclude_promo`, `location_id`, `product_status`, `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, 
						`point1`, `point2`, `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`, `co_promo_code`,
						`coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`,
						`ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
					)
					SELECT
						`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`,
						`seq`, `seq_set`, `promo_code`, `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
						`quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`,
						`coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`, `promo_qty`, `weight`,
						`exclude_promo`, `location_id`, `product_status`, `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, 
						`point1`, `point2`, `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`, `co_promo_code`,
						`coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`,
						`ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
					FROM
					pos_ssup.trn_diary2
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id' AND
					`branch_id`='$this->m_branch_id' AND `doc_tp` IN('SL','VT','CN','DN') AND 
				    `doc_date` BETWEEN DATE_SUB(DATE_FORMAT( NOW( ),'%Y-%m-01'),INTERVAL 1 MONTH) AND DATE_SUB( '$this->doc_date', INTERVAL 1 DAY)";
					$this->db->query($sql_step3);
					
					//commit trans
					$this->db->commit();
					$chk_trans="Y";
				}catch(Zend_Db_Exception $e){
					$this->msg_error=$e->getMessage();
					//rollback trans
					$this->db->rollBack();
					$chk_trans="N";
				}//end try
		
			}//if
			return $chk_trans;
		}//func
		
		
		
		/**
		 * @create 22052017
		 * @return array country
		 */
		function getCountryList(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_country="SELECT `country_code`, `country_name_th`, `country_name_en`, `country_status` 
								FROM `com_country_master`
								WHERE `country_status`='Y' ORDER BY `country_name_en` ASC";
			$rows_country=$this->db->fetchAll($sql_country);
			return $rows_country;
		}//func
		
		/**
		 * @create 06042016
		 * @desc
		 */
		
		function getTrnAlipay(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$arr_alipay=array();
			$sql_aliplay="SELECT *
								FROM `trn_alipay_request`
								WHERE substring( reqdt, 1, 4 ) = YEAR( CURDATE( ) )
								AND substring( reqdt, 5, 2 ) = MONTH( CURDATE( ) )
								AND substring( reqdt, 7, 2 ) = SUBSTRING( CURDATE( ) , 9, 2 )";
			$rows_alipay=$this->db->fetchAll($sql_aliplay);
			return $rows_alipay;
		}//func
		/**
		 * @create 30032017
		 * @param unknown $product_id
		 */
		function chkCoProduct($product_id){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$res_chk='N';
			$sql_chk="SELECT COUNT(*) FROM `promo_detail`
			WHERE
			product_id='$product_id' AND `promo_code` IN('OS06270317', 'OS06280317', 'OL06290317', 'OT06300317'  , 'OT06310317', 'OX06320317', 'OX06330317')";
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk>0){
				$res_chk='Y';
			}
			return $res_chk;
		}//func
		
		/**
		 * @desc check fixed promotion to pay with alipay
		 * @create
		 */
		
		function chkPayWithAlipay(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$res_chk='N';
			$sql_chk="SELECT COUNT(*) FROM `trn_promotion_tmp1`
			WHERE
			`corporation_id` ='$this->m_corporation_id' AND
			`company_id` ='$this->m_company_id' AND
			`branch_id` ='$this->m_branch_id' AND
			`computer_ip`='$this->m_com_ip' AND
			`promo_code` IN('OX07140317','OX07140317_1')";
			$n_chk=$this->db->fetchOne($sql_chk);
			if($n_chk>0){
				$res_chk='Y';
			}
			return $res_chk;
		}//func
		
		/**
		 * @desc for spport alipay
		 * @create 20032017
		 * @param unknown $doc_no
		 * @return unknown
		 */
		function checkpaychannel($doc_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_t1="
			select
			a.credit_no,
			a.credit_tp,
			b.*
			from
			`trn_diary1` as a
			left join
			trn_alipay_request as b
			on
			a.credit_no=b.transid
			where
			a.corporation_id='$this->m_corporation_id'
			and
			a.company_id='$this->m_company_id'
			and
			a.branch_id='$this->m_branch_id'
			and
			a.doc_no='$doc_no'
			and
			a.flag !='C'
			";
			//$credit_tp=$this->db->fetchOne($sql_t1);
			$arr=$this->db->fetchAll($sql_t1);
			return $arr[0];
		}//func
		
		function setCardDummy2Temp($application_id){
			/**
			 * @desc ไม่มีการตัด stock เฉพาะบิลต่ออายุเปลี่ยนไปเป็น Id Card เท่านั้น
			 * @create 08032016
			 * @modify 08032016
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$status_no='01';
			$doc_tp='SL';
			$product_id='CHG2ID';
			if($application_id=='OPPF300'){
				$doc_tp='DN';
			}
			$this->db->beginTransaction();
			try{
				$sql_insert="INSERT INTO
				trn_tdiary2_sl
				SET
				`corporation_id` ='$this->m_corporation_id',
				`company_id` ='$this->m_company_id',
				`branch_id` ='$this->m_branch_id',
				`doc_date` ='$this->doc_date',
				`doc_time` ='$this->doc_time',
				`member_no` ='',
				`doc_no` ='',
				`doc_tp` ='$doc_tp',
				`status_no`='$status_no',
				`web_promo` ='N',
				`seq` ='1',
				`seq_set` ='1',
				`promo_code` ='$application_id',
				`promo_seq` ='1',
				`promo_st` ='',
				`promo_tp` ='',
				`product_id`='$product_id',
				`name_product`='CHANGE_CARD_TO_IDCARD',
				`unit`='',
				`stock_st` ='0',
				`price` ='0',
				`quantity`='0',
				`amount`='0.00',
				`discount`='0.00',
				`member_discount1`='0',
				`member_discount2`='0',
				`net_amt`='0.00',
				`product_status`='',
				`get_point`='',
				`discount_member`='N',
				`cal_amt`='Y',
				`discount_percent`='',
				`member_percent1`='',
				`member_percent2`='',
				`co_promo_percent`='',
				`coupon_percent`='',
				`coupon_discount`='',
				`tax_type`='',
				`computer_no`='$this->m_computer_no',
				`computer_ip`='$this->m_com_ip',
				`point1`='',
				`point2`='',
				`reg_date`='$this->doc_date',
				`reg_time`='$this->doc_time',
				`reg_user`='$this->employee_id'";
				$this->db->query($sql_insert);
				$this->db->commit();
				$chk_trans="Y";
			}catch(Zend_Db_Exception $e){
				$this->msg_error=$e->getMessage();
				$this->db->rollBack();
				$chk_trans="N";
			}//end try
			return $chk_trans;
		}//func
		
		public function getListCoOperation(){
			/**
			 * @desc 
			 * @return list co promotion 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_promo="SELECT * FROM `promo_other` 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`promo_tp` = 'COR' AND
										'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_promo=$this->db->fetchAll($sql_promo);
			$arr_promo=array();
			if(count($row_promo)>0){				
				$i=0;
				foreach($row_promo as $data){
					$promo_code=$data['promo_code'];					
					//-------------------------------------------------------------------------- 99
					$sql_chk="SELECT COUNT(*) FROM promo_branch 
										WHERE 
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND											
											`promo_code`='$promo_code' AND 
											'$this->doc_date' BETWEEN start_date AND end_date AND (
											`branch_id`='$this->m_branch_id' OR `branch_id`='ALL') ";
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk>0){	
						$arr_promo[$i]['promo_code']=$data['promo_code'];
						$arr_promo[$i]['promo_des']=$data['promo_des'];
						$arr_promo[$i]['promo_tp']=$data['promo_tp'];
						$arr_promo[$i]['member_tp']=$data['member_tp'];
						$arr_promo[$i]['promo_amt']=$data['promo_amt'];
						$arr_promo[$i]['point']=$data['point'];
						$arr_promo[$i]['point_to_discount']=$data['point_to_discount'];
						$arr_promo[$i]['discount_member']=$data['discount_member'];
					    $arr_promo[$i]['web_promo']=$data['web_promo'];
					    $arr_promo[$i]['check_repeat']=$data['check_repeat'];
					    $arr_promo[$i]['limite_qty']=$data['limite_qty'];
						$i++;
					}
					//-------------------------------------------------------------------------- 99
				}//foreach
			}//if
			return $arr_promo;
		}//func
		
		function reUpMobileApp(){
			/**
			 * @desc remark privilege to mobile application
			 * @create 25022015
			 * @var
			 * @return
			 */
			return true;
		}//func
		
		function reUpdData(){
			/**
			 * @desc
			 * @create : 23012015
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk="SELECT doc_no,flag,member_id,idcard,co_promo_code,coupon_code 
							FROM trn_diary1 
								WHERE 
										doc_date='$this->doc_date' AND flag='C' AND co_promo_code<>''";
			$r_chk=$this->db->fetchAll($sql_chk);
			if(!empty($r_chk)){
				foreach($r_chk as $data){
					$flag=$data['flag'];
					$doc_no=$data['doc_no'];				
					$promo_code=$data['co_promo_code'];	
					$sql_log="SELECT COUNT(*) FROM `log_member_privilege` 
									WHERE doc_no='$doc_no' AND lock_status='Y' AND privilege_type IN('LINE','MCOUPON','COUPON')";
					$n_log=$this->db->fetchOne($sql_log);
					if($n_log>0){
						$this->unLockMobileCoupon($doc_no);					
						sleep(2);				
					}	
				}
			}//if			
			//exec("php /var/www/pos/htdocs/transfer_to_branch/uppro2shop.php > /dev/null &");
		}//func
		
		function unLockMcsSMS($doc_no){
			/***
			 * @desc
			 * @create : 30092016
			 * @modify : 11112016
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_d2="SELECT promo_code FROM trn_diary2 WHERE doc_no='$doc_no'  GROUP BY promo_code";
			$rows_d2=$this->db->fetchAll($sql_d2);
			if(!empty($rows_d2)){
				$str_promo_code='';
				foreach($rows_d2 as $data){
					$str_promo_code.="'".$data['promo_code']."',";
				}//foreach
				if($str_promo_code!=''){
					$str_promo_code=substr($str_promo_code,0,-1);
				}//end if
				$sql_oth="SELECT COUNT(*) FROM promo_other WHERE promo_tp='MCS' AND promo_code IN($str_promo_code)";
				$n_c=$this->db->fetchOne($sql_oth);
				if($n_c>0){
					$ws = "http://10.100.53.2/wservice/webservices/services/sms_promotion.php?";
					$type = "json";
					$act = "unlock";
					$src = $ws."callback=jsonpCallback&brand=op&dtype=".$type."&shop=".$this->m_branch_id.
					"&act=".$act."&doc_no=".$doc_no."&_=1334128422190";
					@file_get_contents($src,0);
				}//end if
			}//end if
		}//func
		
		function unLockMcsSMS11112016($doc_no){
			/***
			 * @desc
			 * @create : 30092016
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_c="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code IN('OX02130116','OX31330916')";
			$n_c=$this->db->fetchOne($sql_c);
			if($n_c>0){
				$ws = "http://10.100.53.2/wservice/webservices/services/sms_promotion.php?";
				$type = "json";
				$act = "unlock";
				$src = $ws."callback=jsonpCallback&brand=op&dtype=".$type."&shop=".$this->m_branch_id.
				"&act=".$act."&doc_no=".$doc_no."&_=1334128422190";
				$o=@file_get_contents($src,0);
				//$o = str_replace("jsonpCallback(","",$o);
				//$o = str_replace(")","",$o);
				//$o = json_decode($o ,true);
				//echo json_encode($o);
			}//end if
				
		}//func
		
		function unLockMobileCoupon($doc_no){
			/**
			 * @desc unlock mobile coupon line application
			 * @create : 08082014
			 * @modify : 20012015 for support Line Promotion
			 * @modify : 21072015 for support table log_member_privilege
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_h="SELECT branch_id,member_id,co_promo_code,coupon_code FROM trn_diary1 
						WHERE 
							doc_no='$doc_no'	AND 
							doc_date='$this->doc_date' AND 
							flag='C' AND co_promo_code<>''";
			$row_h=$this->db->fetchAll($sql_h);
			if(!empty($row_h)){
				$promo_code=$row_h[0]['co_promo_code'];
				$coupon_code=$row_h[0]['coupon_code'];
				$member_no=$row_h[0]['member_id'];				
				$sql_oth="SELECT* FROM promo_other WHERE promo_code='$promo_code' AND '$this->doc_date' BETWEEN start_date AND end_date";
				$row_oth=$this->db->fetchAll($sql_oth);
				if(!empty($row_oth)){
					$promo_tp=$row_oth[0]['promo_tp'];	
				}else if($promo_code=='OPPLI300' || $promo_code=='OPPLC300'){
					//$promo_code='OI06010615';
					//$promo_code='OI06470416';
					//$promo_code='OI06501116';//2017
					$promo_code='OI06320417';//2017/2
					$promo_tp='LINE';					
				}else if($promo_code=='OPLID300'){
					$promo_code='OI07200216';
					$promo_tp='LINE';
				}//if !empty $row_oth			
				
				//--------------------- START UNLOCK ----------------		
				//cancel mobile line,copon
				$ws = "http://mshop.ssup.co.th/ecoupon/promo_redeem.php?";
				/*
				if($promo_tp=='LINE'){
					//cancel line
					$ws = "http://mshop.ssup.co.th/shop_op/line_redeem.php?";	
					//*WR31082015 fixed	
					if($promo_code=='OI02110615' || $promo_code=='OI07200216'){
						$ws = "http://mshop.ssup.co.th/ecoupon/promo_redeem.php?";
					}			
				}else if($promo_tp=='MCOUPON' || $promo_tp=='COUPON'){
					//cancel mobile copon
					$ws = "http://mshop.ssup.co.th/ecoupon/promo_redeem.php?";
				}
				*/
				$type = "json";
				$shop=$row_h[0]['branch_id'];	
				$act = "cancel";
				$src = $ws."brand=op&doc_no=".$doc_no."&shop=".$shop."&act=".$act."&promo_code=".$promo_code."&_=1334128422190";
				$o=@file_get_contents($src,0);
				$objJson=json_decode($o);				
				if($objJson->status=='OK' || $objJson->status=='YES'){
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("log","log_member_privilege");
						$sql_log="UPDATE `pos_ssup`.`log_member_privilege` 
										SET 
												flag='C',
												lock_status='N',
												upd_date=CURDATE(),
												upd_time=CURTIME(),
												upd_user='IT'
										WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`promo_code`='$promo_code' AND 
												`doc_no`='$doc_no' AND
												`doc_date`='$this->doc_date'";
						$this->db->query($sql_log);						
					}//unlock complete					
					//--------------------- START UNLOCK ----------------
				
			}//if !empty $row_h
			
		}//func		
		
		function usedBagToTemp($member_id,$bag_barcode){
			/**
			 * @desc ไม่มีการตัดสต๊อก
			 **@modify : 01092014 for support bill 04 green bag
			 * @var 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
			$str_tbltemp=parent::countDiaryTemp();	
			$arr_tbl=explode('#',$str_tbltemp);
			$tbl_temp=$arr_tbl[1];
			if($tbl_temp=='N'){
				$tbl_temp="trn_promotion_tmp1";
			}
			$sql_chkexist="SELECT COUNT(*) FROM `$tbl_temp`
										WHERE
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip' AND 
 											 	   `lot_no`='$bag_barcode'";
			$n_chkexist=$this->db->fetchOne($sql_chkexist);
			if($n_chkexist>0){
				return "R";
				exit();
			}
			
			$sql_seq="SELECT MAX(seq) AS seq 
										FROM `$tbl_temp`
										WHERE 
												  `corporation_id` ='$this->m_corporation_id' AND
												  `company_id` ='$this->m_company_id' AND
												  `branch_id` ='$this->m_branch_id' AND
												  `computer_no`='$this->m_computer_no' AND
 											 	  `computer_ip`='$this->m_com_ip'";
					$maxseq=$this->db->fetchOne($sql_seq);
					$this->seq=$maxseq+1;
					
					$sql_cseq="SELECT seq_set
										FROM `$tbl_temp` 
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
										FROM `$tbl_temp`
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
										FROM `$tbl_temp`
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
				
					$sql_insert2="INSERT INTO `$tbl_temp`
									SET 
									  `corporation_id` ='$this->m_corporation_id',
									  `company_id` ='$this->m_company_id',
									  `branch_id` ='$this->m_branch_id',
									  `doc_date` ='$this->doc_date',
									  `doc_time` ='$this->doc_time',
									  `doc_no` ='',
									  `doc_tp` ='SL',
									  `status_no`='00',
									  `seq` ='$this->seq',
									  `seq_set` ='$seq_set',
									  `promo_code` ='OX17160514',
									  `promo_seq` ='$this->promo_seq',
									  `promo_st` ='',
									  `promo_pos` ='G',
									  `promo_tp` ='F',
									  `product_id`='$bag_barcode',								  
									  `stock_st` ='',								  
									  `price` ='0',
									  `quantity`='0',
									  `amount`='0',
									  `discount`='0',								  
									  `member_discount1`='0',
									  `member_discount2`='0',								  
									  `net_amt`='0',
									  `product_status`='0',
									  `get_point`='0',
									  `discount_member`='0',
									  `cal_amt`='Y',											  
									  `discount_percent`='0',
									  `member_percent1`='0',
									  `member_percent2`='0',
									  `co_promo_percent`='0',
									  `coupon_percent`='0',											  
									  `tax_type`='0',
									  `lot_no`='$bag_barcode',									  
									  `computer_no`='$this->m_computer_no',
 									  `computer_ip`='$this->m_com_ip',
									  `reg_date`='$this->doc_date',
									  `reg_time`='$this->doc_time',
									  `reg_user`='$this->employee_id'";	
					$stmt_insert=$this->db->query($sql_insert2);
					if($stmt_insert){
						$sql_setval="INSERT INTO `trn_tdiary2_sl_val` 
												SET 
													`corporation_id`='$this->m_company_id',
													`company_id`='$this->m_company_id',
													 `branch_id`='$this->m_branch_id',
													 `computer_no`='$this->m_computer_no' ,
													`computer_ip`='$this->m_com_ip' ,
													`doc_date`='$this->doc_date' ,
													`member_no`='$member_id',
													`doc_tp`='SL',
													`status_no`='00'";
						$this->db->query($sql_setval);						
					}			
					return $tbl_temp;
		}//func
		
//		function usedBagToTemp($member_id,$bag_barcode){
//			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
//			$sql_chkexist="SELECT COUNT(*) FROM trn_promotion_tmp1 
//										WHERE
//												  `corporation_id` ='$this->m_corporation_id' AND
//												  `company_id` ='$this->m_company_id' AND
//												  `branch_id` ='$this->m_branch_id' AND
//												  `computer_no`='$this->m_computer_no' AND
// 											 	  `computer_ip`='$this->m_com_ip' AND 
// 											 	   `lot_no`='$bag_barcode'";
//			$n_chkexist=$this->db->fetchOne($sql_chkexist);
//			if($n_chkexist>0){
//				return "R";
//				exit();
//			}
//			
//			$sql_seq="SELECT MAX(seq) AS seq 
//										FROM `trn_promotion_tmp1`
//										WHERE 
//												  `corporation_id` ='$this->m_corporation_id' AND
//												  `company_id` ='$this->m_company_id' AND
//												  `branch_id` ='$this->m_branch_id' AND
//												  `computer_no`='$this->m_computer_no' AND
// 											 	  `computer_ip`='$this->m_com_ip'";
//					$maxseq=$this->db->fetchOne($sql_seq);
//					$this->seq=$maxseq+1;
//					
//					$sql_cseq="SELECT seq_set
//										FROM `trn_promotion_tmp1` 
//										WHERE 
//												  `corporation_id` ='$this->m_corporation_id' AND
//												  `company_id` ='$this->m_company_id' AND
//												  `branch_id` ='$this->m_branch_id' AND
//												  `computer_no`='$this->m_computer_no' AND
// 											 	  `computer_ip`='$this->m_com_ip' AND
//												  `promo_code`='$this->promo_code'";						
//					$row_cseq=$this->db->fetchAll($sql_cseq);
//					if(count($row_cseq)>0){
//						$seq_set=$row_cseq[0]['seq_set'];
//					}else{
//						$sql_maxseq="SELECT MAX(seq_set) AS max_seq_set
//										FROM `trn_promotion_tmp1`
//											WHERE 
//												  `corporation_id` ='$this->m_corporation_id' AND
//												  `company_id` ='$this->m_company_id' AND
//												  `computer_no`='$this->m_computer_no' AND
// 											 	  `computer_ip`='$this->m_com_ip' AND
//												  `branch_id` ='$this->m_branch_id'";	
//						$max_seq_set=$this->db->fetchOne($sql_maxseq);			
//						$seq_set=$max_seq_set+1;						
//					}					
//					
//					$sql_pro_seq="SELECT MAX(promo_seq) AS max_promo_seq
//										FROM `trn_promotion_tmp1`
//											WHERE 
//												  `corporation_id` ='$this->m_corporation_id' AND
//												  `company_id` ='$this->m_company_id' AND
//												  `branch_id` ='$this->m_branch_id'  AND 
//												  `promo_code` ='$this->promo_code'";	
//					$max_promo_seq=$this->db->fetchOne($sql_pro_seq);
//					if($max_promo_seq>0){
//						$this->promo_seq=$max_promo_seq+1;
//					}else{
//						$this->promo_seq=1;
//					}						
//				
//					$sql_insert2="INSERT INTO `trn_promotion_tmp1`
//									SET 
//									  `corporation_id` ='$this->m_corporation_id',
//									  `company_id` ='$this->m_company_id',
//									  `branch_id` ='$this->m_branch_id',
//									  `doc_date` ='$this->doc_date',
//									  `doc_time` ='$this->doc_time',
//									  `doc_no` ='',
//									  `doc_tp` ='SL',
//									  `status_no`='00',
//									  `seq` ='$this->seq',
//									  `seq_set` ='$seq_set',
//									  `promo_code` ='OX17160514',
//									  `promo_seq` ='$this->promo_seq',
//									  `promo_st` ='',
//									  `promo_pos` ='G',
//									  `promo_tp` ='F',
//									  `product_id`='$bag_barcode',								  
//									  `stock_st` ='',								  
//									  `price` ='0',
//									  `quantity`='0',
//									  `amount`='0',
//									  `discount`='0',								  
//									  `member_discount1`='0',
//									  `member_discount2`='0',								  
//									  `net_amt`='0',
//									  `product_status`='0',
//									  `get_point`='0',
//									  `discount_member`='0',
//									  `cal_amt`='Y',											  
//									  `discount_percent`='0',
//									  `member_percent1`='0',
//									  `member_percent2`='0',
//									  `co_promo_percent`='0',
//									  `coupon_percent`='0',											  
//									  `tax_type`='0',
//									  `lot_no`='$bag_barcode',									  
//									  `computer_no`='$this->m_computer_no',
// 									  `computer_ip`='$this->m_com_ip',
//									  `reg_date`='$this->doc_date',
//									  `reg_time`='$this->doc_time',
//									  `reg_user`='$this->employee_id'";	
//					$stmt_insert=$this->db->query($sql_insert2);
//					if($stmt_insert){
//						$sql_setval="INSERT INTO `trn_tdiary2_sl_val` 
//												SET 
//													`corporation_id`='$this->m_company_id',
//													`company_id`='$this->m_company_id',
//													 `branch_id`='$this->m_branch_id',
//													 `computer_no`='$this->m_computer_no' ,
//													`computer_ip`='$this->m_com_ip' ,
//													`doc_date`='$this->doc_date' ,
//													`member_no`='$member_id',
//													`doc_tp`='SL',
//													`status_no`='00'";
//						$this->db->query($sql_setval);						
//					}						
//		}//func
		
		function setBagToTemp($str_items,$status_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");			
			//*WR28052014 Check data before save temp
			$str_chkdata=parent::countDiaryTemp();
			$arr_chkdata=explode("#",$str_chkdata);
			if($arr_chkdata[1]=='N'){
				return false;
			}else{
				$tbl_temp=$arr_chkdata[1];
			}
			
			$sql_status="SELECT status_no FROM $tbl_temp WHERE status_no<>'' LIMIT 0,1";
			$row_status=$this->db->fetchAll($sql_status);
			$status_no=$row_status[0]['status_no'];
			
			
			$objCsh=new Model_Cashier();
			$arr_items_set=explode('#',$str_items);
			for($i=0;$i<count($arr_items_set);$i++){
				if($arr_items_set[$i]!=''){
					$arr_data=explode(':',$arr_items_set[$i]);
				    if($arr_data[1]=='' || $arr_data[1]=='0'){
						continue;
					}
					//set data
					$doc_tp='';
					$promo_code='FREEBAG';
					$promo_tp='F';
					$employee_id='';
					$member_no='';
					$product_id=$arr_data[0];
					$quantity=$arr_data[1];						
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
					$net_amt='';
					$discount='';
					$discount_member='';
					$check_repeat='';			
					$objCsh->setGapValTemp($doc_tp,$promo_code,$promo_tp,$employee_id,$member_no='',$product_id,$quantity,
										$status_no,$product_status,$application_id,$card_status,
										$get_point,$promo_id,$discount_percent,$member_percent1,$member_percent2,
										$co_promo_percent,$coupon_percent,$promo_st,$net_amt,$discount,$discount_member,$check_repeat);
				}//if
			}//for i
		}//func
		
		function chkUsedGreenBag(){
			/**
			 * @desc 12062014 
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$res_chk='N';
			$sql_gpoint="SELECT COUNT(*) 
								FROM trn_promotion_tmp1
								WHERE
											`corporation_id`='$this->m_company_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`computer_no`='$this->m_computer_no' AND
 											`computer_ip`='$this->m_com_ip' AND	
 											`promo_code`='OX17160514' AND										
											`doc_date`='$this->doc_date'";
			$n_gpoint=$this->db->fetchOne($sql_gpoint);
			if($n_gpoint>0){
				$res_chk='Y';
			}
			return $res_chk;
		}//func
		
		function updBagBarcode($bagbarcode){
			/**
			 * @desc 22052014 กลับมาดูใหม่
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");			
			$arr_bagbarcode=explode('#',$bagbarcode);
			for($i=0;$i<count($arr_bagbarcode);$i++){
				$bag_barcode=$arr_bagbarcode[$i];
				if($bag_barcode=='') continue;
				$sql_chkbag="SELECT* FROM trn_promotion_tmp1 
									WHERE 
										 `corporation_id`='$this->m_company_id' AND
										 `company_id`='$this->m_company_id' AND
										 `branch_id`='$this->m_branch_id' AND
										 `computer_no`='$this->m_computer_no' AND
	 									 `computer_ip`='$this->m_com_ip' AND 										
										 `doc_date`='$this->doc_date' AND 
										 `lot_no`<>'$bag_barcode' AND
										 `lot_no`='' AND
										 `promo_code` IN('OX08140214','OX09150214') LIMIT 0,1";
				$rows_chkbag=$this->db->fetchAll($sql_chkbag);
				if(!empty($rows_chkbag)){
					$seq=$rows_chkbag[0]['seq'];
					//update
					$sql_upd="UPDATE trn_promotion_tmp1
									SET
										`lot_no`='$bag_barcode'
									WHERE
										 `corporation_id`='$this->m_company_id' AND
										 `company_id`='$this->m_company_id' AND
										 `branch_id`='$this->m_branch_id' AND
										 `computer_no`='$this->m_computer_no' AND
	 									 `computer_ip`='$this->m_com_ip' AND 										
										 `doc_date`='$this->doc_date' AND 
										 seq='$seq'";
					$this->db->query($sql_upd);
				}//foreach
			}//for
			
		}//func
		
		function getFormBagBarcode(){
			/**
			 * @desc 152062014
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chkbag="SELECT* FROM trn_promotion_tmp1 
								WHERE 
									 `corporation_id`='$this->m_company_id' AND
									 `company_id`='$this->m_company_id' AND
									 `branch_id`='$this->m_branch_id' AND
									 `computer_no`='$this->m_computer_no' AND
 									 `computer_ip`='$this->m_com_ip' AND 										
									 `doc_date`='$this->doc_date' AND promo_code IN('OX08140214','OX09150214')";
			$rows_chkbag=$this->db->fetchAll($sql_chkbag);
			return $rows_chkbag;
		}//func
		
		function getBag(){
			/**
			 * @desc 25092013
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
			$sql_bag="SELECT* FROM com_free_bag WHERE '$this->doc_date' BETWEEN start_date AND end_date ORDER BY seq_no";
			$rows_bag=$this->db->fetchAll($sql_bag);
			return $rows_bag;
		}//func
		
		function pingServerStatus($ip){
			/**
			 *@desc
			 *@return
			 */			
            @exec("/usr/bin/fping $ip",$arr,$err);
            
            $ex=explode(" ",@$arr[0]);
            $status_ping="0";
            if(@$ex[2]=="alive"){
                    $status_ping= "1";
            }
            return $status_ping;
		}//func
		
	
		
		function getCamIP(){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_cam");
			$sql_cam="SELECT cam_ip_1,cam_ip_2
								FROM `com_branch_cam`
								WHERE 
									 `corporation_id`='$this->m_corporation_id' AND
								     `company_id`='$this->m_company_id' AND
								     `branch_id`='$this->m_branch_id'";
			$row_cam=$this->db->fetchAll($sql_cam);
			return $row_cam;
		}//func
		
		function getComIP(){
			/**
			 * 
			 * Enter description here ...
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
			$sql_profile="SELECT 
       								`com_ip`,`computer_no`,`pos_id`,`thermal_printer`,`cashdrawer`,`network`,`lock_status`
								 FROM 
									`com_branch_computer` 
								WHERE
								       `corporation_id`='$this->m_corporation_id' AND
								       `company_id`='$this->m_company_id' AND
								       `branch_id`='$this->m_branch_id' AND
								       `computer_no`='1' AND
								       `com_ip`<>'127.0.0.1'";
			$row_profile=$this->db->fetchAll($sql_profile);
			return $row_profile;
		}//func
		
		function getIPAccessory(){
			/**
			 * @desc
			 * @return
			 */			
			$arr_data=$this->getComIP();
			$com_ip=$arr_data[0]['com_ip'];
			$arr_com=explode(".",$com_ip);			
			$rounter_ip="$arr_com[0].$arr_com[1].$arr_com[2].254";
			$computer_ip=$com_ip;
			$arr_cam=$this->getCamIP();			 
			$cam1_ip=$arr_cam[0]['cam_ip_1'];
		    $cam2_ip=$arr_cam[0]['cam_ip_2'];		     
		    $edc_ip="$arr_com[0].$arr_com[1].$arr_com[2].".($arr_com[3]+5);		     
		    $office_ip="192.168.252.240";
		    $bank1_ip="10.9.200.46";
		    $bank2_ip="10.9.200.54";
		    $arr_ip['office']=$office_ip;
		    $arr_ip['rounter']=$rounter_ip;
		    $arr_ip['computer']=$computer_ip;
		    $arr_ip['edc']=$edc_ip;
		    $arr_ip['cam1']=$cam1_ip;
		    $arr_ip['cam2']=$cam2_ip;
		    $arr_ip['bank1']=$bank1_ip;
		    $arr_ip['bank2']=$bank2_ip;
		    return $arr_ip;
		}//func
		
		function chkDocDateCancel($doc_no,$doc_tp){
			/***
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$tbl_check="trn_diary1";
			if($doc_tp=='IQ'){
				$tbl_check="trn_diary1_iq";
			}else if($doc_tp=='RQ'){
				$tbl_check="trn_diary1_rq";
			}
			$sql_chk="SELECT COUNT(*) FROM $tbl_check WHERE doc_no='$doc_no' AND doc_date='$this->doc_date'";
			$n_chk=$this->db->fetchOne($sql_chk);
			$res_chk='N';
			if($n_chk>0){
				$res_chk='Y';
			}
			return $res_chk;
		}//func
		
		function str2strnumber($str){
			if(trim($str)=='')
				return '0';
			$arr_num=array();
			$arr_str=array();
			$arr_num=str_split($str);			
			$k=0;
			for($i=0;$i<count($arr_num);$i++){				
				if($i>0 && $arr_num[$i-1]!=0){
					$arr_str[$k]=$arr_num[$i];
					$k++;
				}else{
					if($arr_num[$i]!=0){
						$arr_str[$k]=$arr_num[$i];
						$k++;
					}
				}
			}		
			return $arr_str;
		}//func		
				
		function chkRosARom($doc_no,$n_cancel,$cpassword){
			/**
			 * @desc
			 * @param String $doc_no เลขที่เอกสาร
			 * @param String $n_cancel ครั้งที่ยกเลิก
			 * @return
			 */
			$doc_tp=parent:: getDocTpByDocNo($doc_no);
			if($n_cancel<=5 && $doc_tp!='DN'){
				//ros
				if($this->chkMemberByDoc($doc_no)){
					$wg=3;
				}else{
					$wg=5;
				}
			}else{
				//arom
				if($this->chkMemberByDoc($doc_no)){
					$wg=4;
				}else{
					$wg=6;
				}
			}				
			$str_doc=substr($doc_no,-5);
			$f_doc=substr($str_doc,0,3);
			$l_doc=substr($str_doc,-2);
			$f_doc=intval($f_doc);
			$l_doc=intval($l_doc);
			$doc_1=$f_doc+$l_doc;
			$doc_2=$doc_1*$wg;			
			$doc_3=intval($cpassword)-$doc_2;
			$doc_4=substr(("000000".abs($doc_3)),-6);		
			
			if($n_cancel<=5 && $doc_tp!='DN'){
				$res_chk_ros=$this->chkRos($doc_no,$n_cancel,$cpassword,$doc_4);
				if($res_chk_ros=='Y'){
					$res_status='Y#ROS#'.$doc_4;
				}else 	if($res_chk_ros=='N'){//unlock all call arom when not found ros : && $n_cancel=='1'
					$res_chk_rom=$this->chkARom($doc_no,$n_cancel,$cpassword,$doc_4);
					if($res_chk_rom=='Y'){
						//found
						$res_status='Y#AROM#'.$doc_4;
					}else{
						//not found
						$res_status='N#AROM#'.$doc_4;
						//echo 'N#AROM#'.$str_doc.'#'.$f_doc.'#'.$l_doc.'#'.$doc_1.'#'.$doc_2.'#'.$doc_3.'#'.$doc_4."#".$wg;
					}					
				}else{
					$res_status='N#ROS#'.$doc_4;
					//$res_status='N#ROS#'.$str_doc.'#'.$f_doc.'#'.$l_doc.'#'.$doc_1.'#'.$doc_2.'#'.$doc_3.'#'.$doc_4;
				}
			}else{
				
					$res_chk_rom=$this->chkARom($doc_no,$n_cancel,$cpassword,$doc_4);
					if($res_chk_rom=='Y'){
						//found
						$res_status='Y#AROM#'.$doc_4;
					}else{
						//not found
						$res_status='N#AROM#'.$doc_4;
						//echo 'N#AROM#'.$str_doc.'#'.$f_doc.'#'.$l_doc.'#'.$doc_1.'#'.$doc_2.'#'.$doc_3.'#'.$doc_4;
					}					
			}
			return $res_status;
		}//func
		
		function chkRos($doc_no,$n_cancel,$cpassword,$doc_4){
			/**
			 * @desc
			 * @param String $doc_no เลขที่เอกสาร
			 * @param String $n_cancel ครั้งที่ยกเลิก
			 * @return
			 */
			$sql_conf="SELECT * FROM conf_employee WHERE employee_id='$doc_4' AND position='ROS'";
			$row_conf=$this->db->fetchAll($sql_conf);
			if(count($row_conf)>0){
				//found		
				return 'Y';		
			}else{
				return 'N';
				//not found				
			}
		}//func
		
		function chkARom($doc_no,$n_cancel,$cpassword,$doc_4){
			/**
			 * @desc
			 * @param String $doc_no เลขที่เอกสาร
			 * @param String $n_cancel ครั้งที่ยกเลิก
			 * @return
			 */
			$sql_conf="SELECT * FROM conf_employee WHERE employee_id='$doc_4' AND position='AROM'";			
			$row_conf=$this->db->fetchAll($sql_conf);
			if(count($row_conf)>0){
				//found
				return 'Y';			
			}else{
				//not found
				return 'N';			
			}
		}//func
		
		function chkMemberByDoc($doc_no){
			/**			  
			 * Enter description here ...
			 * @var unknown_type
			 * @return Boolean true is member or False is not
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_mem="SELECT COUNT(*) FROM trn_diary1 WHERE doc_no='$doc_no' AND member_id<>''";
			$n=$this->db->fetchOne($sql_mem);
			if($n>0){
				return TRUE;
			}else{
				return FALSE;
			}
		}//func
		
		function countCancel($doc_tp){
			/**
			 * @desc
			 * @param String $doc_tp
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$arr_date=explode('-',$this->doc_date);
			$date_f=$arr_date[0]."-".$arr_date[1]."-01";
			if($doc_tp=='SL' || $doc_tp=='VT'){
				$doc_tp="'SL','VT'";
			}else{
				$doc_tp="'".$doc_tp."'";
			}
			$sql_n="SELECT COUNT(*) 
							FROM trn_diary1 
							WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`doc_date`>='$date_f' AND 
									`doc_tp` IN($doc_tp) AND flag='C' AND status_no<>'19'";				
			$n=$this->db->fetchOne($sql_n);
			$n=$n+1;
			return$n;
		}//func
		
		function backupTransferToCheckStock(){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			//backup for check stock by doc_date
			$sql_empty_bkup1="TRUNCATE TABLE `trn_tdiary1_ck_bkup`";
			$this->db->query($sql_empty_bkup1);
			$sql_empty_bkup2="TRUNCATE TABLE `trn_tdiary2_ck_bkup`";
			$this->db->query($sql_empty_bkup2);
			$sql_bkup1="			
			INSERT INTO trn_tdiary1_ck_bkup
									(
								`corporation_id`, `company_id`, `branch_id`,
								 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
								 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
								 `member_percent`, `special_percent`, 
								 `refer_member_id`, `refer_doc_no`, `quantity`,
								 `amount`, `discount`, `member_discount1`, 
								 `member_discount2`, `co_promo_discount`, 
								 `coupon_discount`, `special_discount`, `other_discount`, 
								 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
								 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
								 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
								 `special_promo_code`, `other_promo_code`, `application_id`, 
								 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
								 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
								 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
								 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
								 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
								 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
								 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
								 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
								 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
								 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
								 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
							)
								SELECT 
									`corporation_id`, `company_id`, `branch_id`,
									 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
									 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
									 `member_percent`, `special_percent`, 
									 `refer_member_id`, `refer_doc_no`, `quantity`,
									 `amount`, `discount`, `member_discount1`, 
									 `member_discount2`, `co_promo_discount`, 
									 `coupon_discount`, `special_discount`, `other_discount`, 
									 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
									 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
									 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
									 `special_promo_code`, `other_promo_code`, `application_id`, 
									 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
									 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
									 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
									 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
									 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
									 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
									 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
									 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
									 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
									 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
									 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
								FROM trn_diary1
								WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')
								UNION ALL
								SELECT 
									`corporation_id`, `company_id`, `branch_id`,
									 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
									 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
									 `member_percent`, `special_percent`, 
									 `refer_member_id`, `refer_doc_no`, `quantity`,
									 `amount`, `discount`, `member_discount1`, 
									 `member_discount2`, `co_promo_discount`, 
									 `coupon_discount`, `special_discount`, `other_discount`, 
									 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
									 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
									 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
									 `special_promo_code`, `other_promo_code`, `application_id`, 
									 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
									 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
									 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
									 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
									 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
									 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
									 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
									 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
									 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
									 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
									 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
								FROM `trn_tdiary1_ck`
								WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')";
			$this->db->query($sql_bkup1);
			$sql_bkup2="INSERT INTO trn_tdiary2_ck_bkup
								(
										 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
										 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
										 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
										 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
										 `member_discount2`, `co_promo_discount`, `coupon_discount`,
										 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
										 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
										 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
										 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
										 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
										 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
										 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
								)
								SELECT 
									 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
									 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
									 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
									 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
									 `member_discount2`, `co_promo_discount`, `coupon_discount`,
									 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
									 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
									 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
									 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
									 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
									 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
									 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
								FROM trn_diary2
								WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')
								UNION ALL
								SELECT 
									 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
									 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
									 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
									 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
									 `member_discount2`, `co_promo_discount`, `coupon_discount`,
									 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
									 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
									 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
									 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
									 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
									 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
									 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
								FROM `trn_tdiary2_ck`
								WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')";
			$this->db->query($sql_bkup2);
		}//func
		
		
		
		function beforeTransferToCheckStock(){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
			$this->backupTransferToCheckStock();
			
			//begin
			$this->db->beginTransaction();
			$chk_trans="N";
			try{					
					//sleep(3);
					$sql_step3="
					
					INSERT INTO
							pos_ssup.trn_tdiary1_ck 
							(
								`corporation_id`, `company_id`, `branch_id`,
								 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
								 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
								 `member_percent`, `special_percent`, 
								 `refer_member_id`, `refer_doc_no`, `quantity`,
								 `amount`, `discount`, `member_discount1`, 
								 `member_discount2`, `co_promo_discount`, 
								 `coupon_discount`, `special_discount`, `other_discount`, 
								 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
								 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
								 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
								 `special_promo_code`, `other_promo_code`, `application_id`, 
								 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
								 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
								 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
								 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
								 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
								 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
								 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
								 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
								 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
								 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
								 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
							)
							SELECT
								`corporation_id`, `company_id`, `branch_id`,
								 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
								 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
								 `member_percent`, `special_percent`, 
								 `refer_member_id`, `refer_doc_no`, `quantity`,
								 `amount`, `discount`, `member_discount1`, 
								 `member_discount2`, `co_promo_discount`, 
								 `coupon_discount`, `special_discount`, `other_discount`, 
								 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
								 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
								 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
								 `special_promo_code`, `other_promo_code`, `application_id`, 
								 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
								 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
								 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
								 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
								 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
								 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
								 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
								 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
								 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
								 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
								 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
							FROM
								pos_ssup.trn_diary1
							WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`doc_date`='$this->doc_date' AND 
									`doc_tp` NOT IN('TO','TI','AI','AO')";					
					$this->db->query($sql_step3);
					$sql_step4="DELETE FROM pos_ssup.trn_diary1
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')";
					$this->db->query($sql_step4);					
				//commit trans
				$this->db->commit();
				$chk_trans="Y";
			}catch(Zend_Db_Exception $e){
				$this->msg_error=$e->getMessage();
				echo $this->msg_error;
				//rollback trans
				$this->db->rollBack();
				$chk_trans="N";
			}//end try
			
			if($chk_trans=='Y'){
				//begin
				$this->db->beginTransaction();
				try{
					$sql_step3="					
						INSERT INTO
						pos_ssup.trn_tdiary2_ck 
						(
							 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
							 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
							 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
							 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
							 `member_discount2`, `co_promo_discount`, `coupon_discount`,
							 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
							 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
							 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
							 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
							 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
							 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
							 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
						)
						SELECT
							 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
							 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
							 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
							 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
							 `member_discount2`, `co_promo_discount`, `coupon_discount`,
							 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
							 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
							 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
							 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
							 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
							 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
							 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
						FROM
							pos_ssup.trn_diary2 
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`doc_date`='$this->doc_date' AND 
							`doc_tp` NOT IN('TO','TI','AI','AO')";								
					$this->db->query($sql_step3);
					$sql_step4="DELETE FROM pos_ssup.trn_diary2
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')";
					$this->db->query($sql_step4);
					//commit trans
					$this->db->commit();
					$chk_trans="Y";
				}catch(Zend_Db_Exception $e){
					$this->msg_error=$e->getMessage();
					//rollback trans
					$this->db->rollBack();
					$chk_trans="N";
				}//end try
				
			}//if
			return $chk_trans;
		}//func
		
		
//		function beforeTransferToCheckStock(){
//			/**
//			 * @desc
//			 * @param
//			 */
//			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
//			$this->backupTransferToCheckStock();
//			
//			//begin
//			$this->db->beginTransaction();
//			$chk_trans="N";
//			try{					
//					//sleep(3);
//					$sql_step3="INSERT pos_ssup.trn_tdiary1_ck 
//									SELECT * 
//											FROM pos_ssup.trn_diary1 
//									WHERE 
//											`corporation_id`='$this->m_corporation_id' AND
//											`company_id`='$this->m_company_id' AND
//											`branch_id`='$this->m_branch_id' AND
//											`doc_date`='$this->doc_date' AND 
//											`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step3);
//					$sql_step4="DELETE FROM pos_ssup.trn_diary1
//									WHERE 
//										`corporation_id`='$this->m_corporation_id' AND
//										`company_id`='$this->m_company_id' AND
//										`branch_id`='$this->m_branch_id' AND
//										`doc_date`='$this->doc_date' AND 
//										`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step4);					
//				//commit trans
//				$this->db->commit();
//				$chk_trans="Y";
//			}catch(Zend_Db_Exception $e){
//				$this->msg_error=$e->getMessage();
//				echo $this->msg_error;
//				//rollback trans
//				$this->db->rollBack();
//				$chk_trans="N";
//			}//end try
//			
//			if($chk_trans=='Y'){
//				//begin
//				$this->db->beginTransaction();
//				try{
//					$sql_step3="INSERT pos_ssup.trn_tdiary2_ck 
//									SELECT * 
//											FROM pos_ssup.trn_diary2 
//									WHERE 
//											`corporation_id`='$this->m_corporation_id' AND
//											`company_id`='$this->m_company_id' AND
//											`branch_id`='$this->m_branch_id' AND
//											`doc_date`='$this->doc_date' AND 
//											`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step3);
//					$sql_step4="DELETE FROM pos_ssup.trn_diary2
//									WHERE 
//										`corporation_id`='$this->m_corporation_id' AND
//										`company_id`='$this->m_company_id' AND
//										`branch_id`='$this->m_branch_id' AND
//										`doc_date`='$this->doc_date' AND 
//										`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step4);
//					//commit trans
//					$this->db->commit();
//					$chk_trans="Y";
//				}catch(Zend_Db_Exception $e){
//					$this->msg_error=$e->getMessage();
//					//rollback trans
//					$this->db->rollBack();
//					$chk_trans="N";
//				}//end try
//				
//			}//if
//			return $chk_trans;
//		}//func

		function afterTransferToCheckStock(){
			/**
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
			$this->backupTransferToCheckStock();
			//กรณีระหว่าง check stock ยังมีการขายตามปกติ
			//begin
			$this->db->beginTransaction();
			$chk_trans="N";
			try{					
					$sql_step3="
						INSERT INTO
							pos_ssup.trn_diary1
							(
								`corporation_id`, `company_id`, `branch_id`,
								 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
								 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
								 `member_percent`, `special_percent`, 
								 `refer_member_id`, `refer_doc_no`, `quantity`,
								 `amount`, `discount`, `member_discount1`, 
								 `member_discount2`, `co_promo_discount`, 
								 `coupon_discount`, `special_discount`, `other_discount`, 
								 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
								 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
								 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
								 `special_promo_code`, `other_promo_code`, `application_id`, 
								 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
								 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
								 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
								 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
								 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
								 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
								 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
								 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
								 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
								 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
								 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
							)
							SELECT
								`corporation_id`, `company_id`, `branch_id`,
								 `doc_date`, `doc_time`, `doc_no`, `doc_tp`,
								 `status_no`, `flag`, `member_id`,`idcard`,`special_day`,`mobile_no`,
								 `member_percent`, `special_percent`, 
								 `refer_member_id`, `refer_doc_no`, `quantity`,
								 `amount`, `discount`, `member_discount1`, 
								 `member_discount2`, `co_promo_discount`, 
								 `coupon_discount`, `special_discount`, `other_discount`, 
								 `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`,
								 `coupon_cash`, `coupon_cash_qty`,`point_begin`, `point1`, `point2`, 
								 `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, 
								 `special_promo_code`, `other_promo_code`, `application_id`, 
								 `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`,
								 `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, 
								 `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`,
								 `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`,
								 `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`,
								 `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`,
								 `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`,
								 `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`,
								 `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`,
								 `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`,
								 `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
							FROM
								pos_ssup.trn_tdiary1_ck					
							WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`doc_date`='$this->doc_date' AND 
									`doc_tp` NOT IN('TO','TI','AI','AO')";
					$this->db->query($sql_step3);
					$sql_step4="DELETE FROM pos_ssup.trn_tdiary1_ck 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')";
					$this->db->query($sql_step4);
				//commit trans
				$this->db->commit();
				$chk_trans="Y";
			}catch(Zend_Db_Exception $e){
				$this->msg_error=$e->getMessage();
				//rollback trans
				$this->db->rollBack();
				$chk_trans="N";
			}//end try
			if($chk_trans=='Y'){
				//begin
				
				$this->db->beginTransaction();
				try{
					$sql_step3="					
					INSERT INTO
						pos_ssup.trn_diary2
						(
							 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
							 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
							 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
							 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
							 `member_discount2`, `co_promo_discount`, `coupon_discount`,
							 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
							 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
							 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
							 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
							 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
							 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
							 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
						)
						SELECT
							 `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
							 `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, 
							 `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`,
							 `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, 
							 `member_discount2`, `co_promo_discount`, `coupon_discount`,
							 `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`,
							 `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`,
							 `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, 
							 `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`,
							 `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, 
							 `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, 
							 `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
						FROM
							pos_ssup.trn_tdiary2_ck					
						WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`doc_date`='$this->doc_date' AND 
								`doc_tp` NOT IN('TO','TI','AI','AO')";
					$this->db->query($sql_step3);
					$sql_step4="DELETE FROM pos_ssup.trn_tdiary2_ck
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND 
										`doc_tp` NOT IN('TO','TI','AI','AO')";
					$this->db->query($sql_step4);
					//commit trans
					$this->db->commit();
					$chk_trans="Y";
				}catch(Zend_Db_Exception $e){
					$this->msg_error=$e->getMessage();
					//rollback trans
					$this->db->rollBack();
					$chk_trans="N";
				}//end try
				
			}//if
			return $chk_trans;
		}//func
		
//		function afterTransferToCheckStock(){
//			/**
//			 * @desc
//			 * @param
//			 */
//			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");	
//			$this->backupTransferToCheckStock();
//			//กรณีระหว่าง check stock ยังมีการขายตามปกติ
//			//begin
//			$this->db->beginTransaction();
//			$chk_trans="N";
//			try{					
//					$sql_step3="INSERT pos_ssup.trn_diary1
//									SELECT * 
//											FROM pos_ssup.trn_tdiary1_ck  
//									WHERE 
//											`corporation_id`='$this->m_corporation_id' AND
//											`company_id`='$this->m_company_id' AND
//											`branch_id`='$this->m_branch_id' AND
//											`doc_date`='$this->doc_date' AND 
//											`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step3);
//					$sql_step4="DELETE FROM pos_ssup.trn_tdiary1_ck 
//									WHERE 
//										`corporation_id`='$this->m_corporation_id' AND
//										`company_id`='$this->m_company_id' AND
//										`branch_id`='$this->m_branch_id' AND
//										`doc_date`='$this->doc_date' AND 
//										`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step4);
//				//commit trans
//				$this->db->commit();
//				$chk_trans="Y";
//			}catch(Zend_Db_Exception $e){
//				$this->msg_error=$e->getMessage();
//				//rollback trans
//				$this->db->rollBack();
//				$chk_trans="N";
//			}//end try
//			if($chk_trans=='Y'){
//				//begin
//				
//				$this->db->beginTransaction();
//				try{
//					$sql_step3="INSERT pos_ssup.trn_diary2 
//									SELECT * 
//											FROM pos_ssup.trn_tdiary2_ck 
//									WHERE 
//											`corporation_id`='$this->m_corporation_id' AND
//											`company_id`='$this->m_company_id' AND
//											`branch_id`='$this->m_branch_id' AND
//											`doc_date`='$this->doc_date' AND 
//											`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step3);
//					$sql_step4="DELETE FROM pos_ssup.trn_tdiary2_ck
//									WHERE 
//										`corporation_id`='$this->m_corporation_id' AND
//										`company_id`='$this->m_company_id' AND
//										`branch_id`='$this->m_branch_id' AND
//										`doc_date`='$this->doc_date' AND 
//										`doc_tp` NOT IN('TO','TI','AI','AO')";
//					$this->db->query($sql_step4);
//					//commit trans
//					$this->db->commit();
//					$chk_trans="Y";
//				}catch(Zend_Db_Exception $e){
//					$this->msg_error=$e->getMessage();
//					//rollback trans
//					$this->db->rollBack();
//					$chk_trans="N";
//				}//end try
//				
//			}//if
//			return $chk_trans;
//		}//func
		
		
		function cancelMember($member_id,$employee_id){
			/**
			 * @desc for bill 01,05
			 * @param String $member_id : refer to cancel
			 * @modify 08122012 cut  ,card_type='L' from query
			 * @return void
			 */
			//cancel card new
			if($member_id=='') return false;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_register_new_card");			
			$status_cancelmember='N';
			$this->db->beginTransaction();
			try{ 
				
//					$sql_crm="SELECT * FROM `crm_card` WHERE `member_no` = '$member_id'";
//					$row_crm=$this->db->fetchAll($sql_crm);
//					if(!empty($row_crm)){
//						$cust_id=$row_crm[0]['customer_id'];
//						$sql_del_crm="DELETE FROM crm_card WHERE `member_no` = '$member_id'";
//						$this->db->query($sql_del_crm);
//						$sql_del_crmprofile="DELETE FROM crm_profile WHERE `customer_id` = '$cust_id'";
//						$this->db->query($sql_del_crmprofile);						
//					}
					
					$sql_del="DELETE a.*,b.*
								FROM crm_card AS a INNER JOIN crm_profile AS b 
								ON a.customer_id=b.customer_id							 
								WHERE a.member_no='$member_id'";
					$this->db->query($sql_del);					

					$sql_cancel_card="UPDATE 
													com_register_new_card
												SET
													apply_date='',
													apply_time='',
													remark=''
												WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`member_id`='$member_id'";
					$this->db->query($sql_cancel_card);					
					$this->db->commit();
					$status_cancelmember='Y';
			}catch(Zend_Db_Exception $e){
					$this->db->rollBack();
			}//catch

			//delete member form crm_card and crm_profile
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			if($status_cancelmember=='Y'){
				$this->db->beginTransaction();
				try{ 
					$sql_cusid="SELECT customer_id 
									FROM crm_card 
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`member_no`='$member_id'";
					$row_cusid=$this->db->fetchCol($sql_cusid);
					if(count($row_cusid)>0){
						$customer_id=$row_cusid[0];
						$sql_cancel_crmprofile="DELETE FROM 
															crm_profile
														WHERE
															`corporation_id`='$this->m_corporation_id' AND
															`company_id`='$this->m_company_id' AND
															`customer_id`='$member_id'";
						$this->db->query($sql_cancel_crmprofile);
					}							
					$sql_cancel_crm="DELETE FROM 
													crm_card
												WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`member_no`='$member_id'";
					$this->db->query($sql_cancel_crm);
					$this->db->commit();
					$status_cancelmember='Y';
				}catch(Zend_Db_Exception $e){
					$this->db->rollBack();
					$status_cancelmember='N';
				}//catch
			}//		
			return $status_cancelmember;			
		}//func
		
		function updateStock($doc_no){
			/**
			 * @desc การยกเลิกเอกสารเป็นการคืน Stock
			 * @param
			 */
			$sql_t2="SELECT product_id,SUM(quantity*stock_st) AS qty_st
								FROM
									trn_diary2
								WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_no`='$doc_no'
								GROUP BY product_id";
				$row_t2=$this->db->fetchAll($sql_t2);
				if(count($row_t2)>0){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
					$this->db->beginTransaction();
					try{
						foreach($row_t2 as $data){
							$product_id=$data['product_id'];
							$quantity=$data['qty_st'];
							$sql_stock="SELECT onhand 
											FROM com_stock_master
											WHERE
												`corporation_id`='$this->m_corporation_id' AND
												`company_id`='$this->m_company_id' AND
												`branch_id`='$this->m_branch_id' AND
												`branch_no`='$this->m_branch_no' AND		
												`year`='$this->year' AND		
												`month`='$this->month' AND		
												`product_id`='$product_id'";
							$row_stock=$this->db->fetchCol($sql_stock);
							if(count($row_stock)>0){
								$onhand=$row_stock[0]-$quantity;
								$sql_upd_stock="UPDATE com_stock_master 
														SET onhand='$onhand' 
														WHERE
															`corporation_id`='$this->m_corporation_id' AND
															`company_id`='$this->m_company_id' AND
															`branch_id`='$this->m_branch_id' AND
															`branch_no`='$this->m_branch_no' AND		
															`year`='$this->year' AND		
															`month`='$this->month' AND		
															`product_id`='$product_id'";
								$this->db->query($sql_upd_stock);								
							}
						}//foreach
						$this->db->commit();
					}catch(Zend_Db_Exception $e){
						$this->db->rollBack();
					}
					
				}
		}//func		
		
		function cancelDocTO($doc_no,$employee_id){
			/**
			 * @desc
			 * @return
			 */
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->arr_date=explode('-',$this->doc_date);
			$this->year=intval($this->arr_date[0]);
			$this->month=intval($this->arr_date[1]);
			$status_cancel='N';
			$sql_t1="SELECT refer_doc_no
			FROM trn_diary1
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id' AND
			`branch_id`='$this->m_branch_id' AND
			`doc_no`='$doc_no' AND
			`flag`<>'C'";
			$row_ref=$this->db->fetchCol($sql_t1);
			if(count($row_ref)>0){
				$refer_doc_no=$row_ref[0];
				//*WR01122016
				$refer_doc_tp=substr($refer_doc_no,6,2);
		
				$this->db->beginTransaction();
				try{
					//*WR01122016
					if($refer_doc_tp=='RT'){
						$sql_del1="DELETE FROM trn_diary1_rq WHERE doc_no='$refer_doc_no'";
						$this->db->query($sql_del1);
						$sql_del2="DELETE FROM trn_diary2_rq WHERE doc_no='$refer_doc_no'";
						$this->db->query($sql_del2);
							
						$sql_upd_rt1="UPDATE trn_diary1_rt SET status_transfer='' WHERE doc_no='$refer_doc_no'";
						$this->db->query($sql_upd_rt1);
						$sql_upd_rt2="UPDATE trn_diary2_rt SET flag='' WHERE doc_no='$refer_doc_no'";
						$this->db->query($sql_upd_rt2);
		
						//update previous month
						$str_prv_month = date('Y-m', strtotime($this->doc_date. "-1 Month"));
						$arr_prv_month=explode('-',$str_prv_month);
						$m_prv=$arr_prv_month[1];
						$y_prv=$arr_prv_month[0];
						$sql_upd_prv="UPDATE com_order_quota_tester SET year='$y_prv',month='$m_prv' WHERE branch_id='$this->m_branch_id'";
						$this->db->query($sql_upd_prv);
		
					}else{
						$sql_upd="UPDATE trn_diary1_rq
						SET
						status_transfer='Y'
						WHERE
						`corporation_id`='$this->m_corporation_id' AND
						`company_id`='$this->m_company_id' AND
						`branch_id`='$this->m_branch_id' AND
						`doc_no`='$refer_doc_no'";
						$res_upd=$this->db->query($sql_upd);
					}
						
					// 					$sql_upd="UPDATE trn_diary1_rq
					// 					SET
					// 					status_transfer='Y'
					// 					WHERE
					// 					`corporation_id`='$this->m_corporation_id' AND
					// 					`company_id`='$this->m_company_id' AND
					// 					`branch_id`='$this->m_branch_id' AND
					// 					`doc_no`='$refer_doc_no'";
					// 					$res_upd=$this->db->query($sql_upd);
						
					$sql_upd_t1="UPDATE trn_diary1
					SET
					flag='C',
					cancel_id='$employee_id',
					cancel_date=CURDATE(),
					cancel_time=CURTIME()
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id' AND
					`branch_id`='$this->m_branch_id' AND
					`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t1);
						
					$sql_upd_t2="UPDATE trn_diary2
					SET
					flag='C'
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id' AND
					`branch_id`='$this->m_branch_id' AND
					`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t2);
						
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
					$this->db->commit();
				}catch(Zend_Db_Exception $e){
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
					$this->db->rollBack();
				}
		
				if($msg_status=='Y'){
					$this->updateStock($doc_no);
				}
		
			}else{
				$msg_title="เกิดความผิดพลาด";
				$msg_promt="เอกสาร $doc_no ถูกยกเลิกแล้ว กรุณาตรวจสอบอีกครั้ง";
			}
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		function cancelDocTO26122016($doc_no,$employee_id){
			/**
			 * @desc
			 * @return 
			 */
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->arr_date=explode('-',$this->doc_date);
			$this->year=intval($this->arr_date[0]);
			$this->month=intval($this->arr_date[1]);
			$status_cancel='N';
			$sql_t1="SELECT refer_doc_no 
							FROM trn_diary1  
								WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_no`='$doc_no' AND
										`flag`<>'C'";
			$row_ref=$this->db->fetchCol($sql_t1);
			if(count($row_ref)>0){
				$refer_doc_no=$row_ref[0];
				$this->db->beginTransaction();
				try{
					$sql_upd="UPDATE trn_diary1_rq 
								SET 
										status_transfer='Y'
									WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`doc_no`='$refer_doc_no'";
					$res_upd=$this->db->query($sql_upd);
					$sql_upd_t1="UPDATE trn_diary1 
											SET
												flag='C',
												cancel_id='$employee_id',
												cancel_date=CURDATE(),
												cancel_time=CURTIME()
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t1);
					
					$sql_upd_t2="UPDATE trn_diary2
											SET
												flag='C'
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t2);					
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
					$this->db->commit();
				}catch(Zend_Db_Exception $e){
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
					$this->db->rollBack();
				}
				
				if($msg_status=='Y'){
					$this->updateStock($doc_no);
				}
				
			}else{
				$msg_title="เกิดความผิดพลาด";
				$msg_promt="เอกสาร $doc_no ถูกยกเลิกแล้ว กรุณาตรวจสอบอีกครั้ง";
			}
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		function cancelDocAO($doc_no,$employee_id){
			/**
			 * @desc
			 * @param
			 */
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->arr_date=explode('-',$this->doc_date);
			$this->year=intval($this->arr_date[0]);
			$this->month=intval($this->arr_date[1]);
			$sql_sl="SELECT COUNT(*) 
						FROM trn_diary1 
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`doc_no`='$doc_no' AND
							`flag`='C'	";
			$n_sl=$this->db->fetchOne($sql_sl);
			if($n_sl>0){
				$msg_title="เกิดความผิดพลาด";
				$msg_promt="เอกสาร $doc_no ถูกยกเลิกแล้ว กรุณาตรวจสอบอีกครั้ง";
			}else{
				$this->db->beginTransaction();
				try{
					$sql_upd_t1="UPDATE trn_diary1 
												SET
													flag='C',
													cancel_id='$employee_id',
													cancel_date=CURDATE(),
													cancel_time=CURTIME()
												WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t1);
					
					$sql_upd_t2="UPDATE trn_diary2
											SET
												flag='C'
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t2);					
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
					$this->db->commit();
				}catch(Zend_Db_Exception $e){
					//echo $e->getMessage();
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
					$this->db->rollBack();
				}
				
				if($msg_status=='Y'){
					$this->updateStock($doc_no);
				}
				
			}	
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		function cancelDocAI($doc_no,$employee_id){
			/**
			 * @desc
			 * @param
			 */
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->arr_date=explode('-',$this->doc_date);
			$this->year=intval($this->arr_date[0]);
			$this->month=intval($this->arr_date[1]);
			$sql_sl="SELECT COUNT(*) 
						FROM trn_diary1 
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`doc_no`='$doc_no' AND
							`flag`='C'	";
			$n_sl=$this->db->fetchOne($sql_sl);
			if($n_sl>0){
				$msg_title="เกิดความผิดพลาด";
				$msg_promt="เอกสาร $doc_no ถูกยกเลิกแล้ว กรุณาตรวจสอบอีกครั้ง";
			}else{
				$this->db->beginTransaction();
				try{
					$sql_upd_t1="UPDATE trn_diary1 
												SET
													flag='C',
													cancel_id='$employee_id',
													cancel_date=CURDATE(),
													cancel_time=CURTIME()
												WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t1);
					
					$sql_upd_t2="UPDATE trn_diary2
											SET
												flag='C'
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t2);					
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
					$this->db->commit();
				}catch(Zend_Db_Exception $e){
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
					$this->db->rollBack();
				}
				
				if($msg_status=='Y'){
					$this->updateStock($doc_no);
				}
				
			}	
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		function cancelDocCN($doc_no,$employee_id){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			
			$this->arr_date=explode('-',$this->doc_date);
			$this->year=intval($this->arr_date[0]);
			$this->month=intval($this->arr_date[1]);
			
			//find refer_doc_no to cn
			$sql_doc_refer="SELECT doc_no,refer_doc_no
									FROM trn_diary1
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_no`='$doc_no'";
			$row_refer=$this->db->fetchAll($sql_doc_refer);
			$refer_doc_no=$row_refer[0]['refer_doc_no'];
			//check bill 06
			$sql_sl="SELECT flag,doc_no 
						FROM trn_diary1 
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`refer_doc_no`='$doc_no' AND
							`status_no`='06'";			
			$row_sl=$this->db->fetchAll($sql_sl);
			if(count($row_sl)>0){
				//case has bill 06 
				if($row_sl[0]['flag']!='C'){
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="กรุณายกเลิกบิลประกบลดหนี้ 06 ก่อน";
				}else{
					//case bill 06 has been cancel
					$this->db->beginTransaction();
					try{
						$sql_upd_t1="UPDATE trn_diary1 
												SET
													flag='C',
													cancel_id='$employee_id',
													cancel_date=CURDATE(),
													cancel_time=CURTIME()
												WHERE
														`corporation_id`='$this->m_corporation_id' AND
														`company_id`='$this->m_company_id' AND
														`branch_id`='$this->m_branch_id' AND
														`doc_no`='$doc_no'";
						$this->db->query($sql_upd_t1);
						
						$sql_upd_t2="UPDATE trn_diary2
												SET
													flag='C'
												WHERE
														`corporation_id`='$this->m_corporation_id' AND
														`company_id`='$this->m_company_id' AND
														`branch_id`='$this->m_branch_id' AND
														`doc_no`='$doc_no'";
						$this->db->query($sql_upd_t2);					
						$msg_title="ผลการยกเลิกเอกสาร";
						$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
						$msg_status="Y";
						$this->db->commit();
					}catch(Zend_Db_Exception $e){
						$msg_title="เกิดความผิดพลาด";
						$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
						$this->db->rollBack();
					}
				}//end else flag==C
			}else{
				//case not has bill 06
				$this->db->beginTransaction();
				try{
					$sql_upd_t1="UPDATE trn_diary1 
											SET
												flag='C',
												cancel_id='$employee_id',
												cancel_date=CURDATE(),
												cancel_time=CURTIME()
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t1);
					
					$sql_upd_t2="UPDATE trn_diary2
											SET
												flag='C'
											WHERE
													`corporation_id`='$this->m_corporation_id' AND
													`company_id`='$this->m_company_id' AND
													`branch_id`='$this->m_branch_id' AND
													`doc_no`='$doc_no'";
					$this->db->query($sql_upd_t2);					
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
					$this->db->commit();
				}catch(Zend_Db_Exception $e){
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
					$this->db->rollBack();
				}
			}//end else
			if($msg_status=='Y'){				
				$sql_cn2="SELECT lot_no,product_id,cn_qty,cn_amt FROM trn_diary2 
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_no`='$doc_no'";
				$row_cn2=$this->db->fetchAll($sql_cn2);
				foreach($row_cn2 as $data){
					$refer_seq=$data['lot_no'];	
					$product_id=$data['product_id'];
					$cn_qty=$data['cn_qty'];			
					$cn_amt=$data['cn_amt'];		
					$sql_upd_t2="UPDATE trn_diary2
												SET
													cn_qty=cn_qty-'$cn_qty',
													cn_amt=cn_amt-'$cn_amt'
												WHERE
														`corporation_id`='$this->m_corporation_id' AND
														`company_id`='$this->m_company_id' AND
														`branch_id`='$this->m_branch_id' AND
														`doc_no`='$refer_doc_no' AND
														`seq`='$refer_seq' AND
														`product_id`='$product_id'";						
						$this->db->query($sql_upd_t2);
				}//foreach
				$this->updateStock($doc_no);
			}
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		function cancelDocOR($doc_no,$employee_id){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_or");
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$sql_or="SELECT COUNT(*) 
							FROM `trn_diary1_or` 
							WHERE
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`doc_no`='$doc_no' AND
								`flag`<>'C'	";
			$n_or=$this->db->fetchOne($sql_or);
			if($n_or>0){
				$this->db->beginTransaction();
				try{
						$sql_upd_or="UPDATE 
											`trn_diary1_or`
										SET
											`flag`='C',
											cancel_id='$employee_id',
											cancel_date=CURDATE(),
											cancel_time=CURTIME()
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`doc_no`='$doc_no'";
						$res_upd=$this->db->query($sql_upd_or);
				
						$sql_upd_or2="UPDATE 
											`trn_diary2_or`
										SET
											`flag`='C'
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`doc_no`='$doc_no'";
						$res_upd=$this->db->query($sql_upd_or2);
						$msg_title="ผลการยกเลิกเอกสาร";
						$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
						$msg_status="Y";
						$this->db->commit();
				}catch(Zend_Db_Exception $e){
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
					$this->db->rollBack();
				}			
			}else{
				$msg_title="เกิดความผิดพลาด";
				$msg_promt="เอกสาร $doc_no ถูกยกเลิกแล้ว กรุณาตรวจสอบอีกครั้ง";
			}
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		
		function cancelDocRQ($doc_no,$employee_id){
			/**
			 * @desc check doc_date only not check refer_doc_no cancel
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_rq");
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$sql_chk="SELECT COUNT(*)
							FROM trn_diary1_rq 
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`doc_no`='$doc_no' AND doc_date='$this->doc_date'";				
			$n_chk=$this->db->fetchOne($sql_chk);
			if(count($n_chk)>0){			
				//cancel to complete
				$sql_upd_t1="UPDATE 
									`trn_diary1_rq`
								SET
									`flag`='C',
									cancel_id='$employee_id',
									cancel_date=CURDATE(),
									cancel_time=CURTIME()
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`doc_no`='$doc_no'";
				$res_upd=$this->db->query($sql_upd_t1);
				if($res_upd){
					$sql_upd_t2="UPDATE 
											`trn_diary2_rq`
										SET
											`flag`='C'
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND
											`doc_no`='$doc_no'";
					$res_upd2=$this->db->query($sql_upd_t2);
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
				}				
			}else{
				//cancel to before rq
				$msg_title="เกิดความผิดพลาด";			
				$msg_promt="ไม่สามารถยกเลิกเอกสารข้ามวันได้ กรุณาตรวจสอบอีกครั้ง";
			}			
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;
		}//func
		
		
//		function cancelDocRQ($doc_no,$employee_id){
//			/**
//			 * @desc
//			 * @return
//			 */		
//			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
//			$msg_title="";
//			$msg_promt="";
//			$msg_status="N";
//			$str_res="";
//			$sql_chk="SELECT refer_doc_no
//							FROM trn_diary1_rq 
//								WHERE
//									`corporation_id`='$this->m_corporation_id' AND
//									`company_id`='$this->m_company_id' AND
//									`branch_id`='$this->m_branch_id' AND
//									`doc_no`='$doc_no' AND doc_date='$this->doc_date'";				
//			$row_chk=$this->db->fetchAll($sql_chk);
//			if(count($row_chk)>0){
//				$refer_doc_no=$row_chk[0]['refer_doc_no'];
//				$sql_to="SELECT COUNT(*) 
//							FROM trn_diary1 
//								WHERE
//									`corporation_id`='$this->m_corporation_id' AND
//									`company_id`='$this->m_company_id' AND
//									`branch_id`='$this->m_branch_id' AND
//									`doc_no`='$refer_doc_no' AND
//									`flag`='C'";
//				$n_to=$this->db->fetchOne($sql_to);
//				if($n_to>0){
//						//cancel to complete
//						$sql_upd_or="UPDATE 
//											`trn_diary1_rq`
//										SET
//											`flag`='C',
//											cancel_id='$employee_id',
//											cancel_date=CURDATE(),
//											cancel_time=CURTIME()
//										WHERE
//											`corporation_id`='$this->m_corporation_id' AND
//											`company_id`='$this->m_company_id' AND
//											`branch_id`='$this->m_branch_id' AND
//											`doc_no`='$doc_no'";
//						$res_upd=$this->db->query($sql_upd_or);
//						if($res_upd){
//							$msg_title="ผลการยกเลิกเอกสาร";
//							$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
//							$msg_status="Y";
//						}
//					}else{
//						//cancel to before rq
//						$msg_title="เกิดความผิดพลาด";
//						$msg_promt="กรุณายกเลิกเอกสาร TO ก่อนยกเลิกเอกสาร RQ";
//				}
//				
//			}else{
//				//cancel to before rq
//				$msg_title="เกิดความผิดพลาด";			
//				$msg_promt="ไม่สามารถยกเลิกเอกสารข้ามวันได้ กรุณาตรวจสอบอีกครั้ง";
//			}			
//			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
//			return $str_res;
//		}//func
		
		function cancelDocIQ($doc_no,$employee_id){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary1_iq");
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$sql_iq="SELECT COUNT(*) 
							FROM trn_diary1_iq
								WHERE
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`doc_no`='$doc_no'  AND doc_date='$this->doc_date'";
			$n_iq=$this->db->fetchOne($sql_iq);
			if($n_iq>0){				
				$sql_upd_iq="UPDATE 
										`trn_diary1_iq`
									SET
										`flag`='C'	,
										cancel_id='$employee_id',
										cancel_date=CURDATE(),
										cancel_time=CURTIME()							
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_no`='$doc_no'";					
					$res_upd=$this->db->query($sql_upd_iq);
				if($res_upd){
					$sql_upd_iq="UPDATE 
										`trn_diary2_iq`
									SET
										`flag`='C'
									WHERE
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_no`='$doc_no'";					
					$res_upd=$this->db->query($sql_upd_iq);
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";
				}else{
					$msg_title="เกิดความผิดพลาด";
					$msg_promt="ไม่สามารถยกเลิกเอกสาร $doc_no ได้ กรุณาตรวจสอบอีกครั้ง";
				}
			}else{
				$msg_title="เกิดความผิดพลาด";			
				$msg_promt="ไม่สามารถยกเลิกเอกสารข้ามวันได้ กรุณาตรวจสอบอีกครั้ง";
			}			
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;			
		}//func
		
		function markCancelDoc($doc_no,$employee_id,$aromros_id,$cancel_type,$cancel_description){
			/**
			 * @desc
			 * @param
			 * @return null			 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_t1="UPDATE trn_diary1
						SET
							cancel_auth='$aromros_id',
							cancel_tp='$cancel_type',
							cancel_remark='$cancel_description'
						WHERE						
							`doc_no`='$doc_no'";			
			$this->db->query($sql_t1);
		}//func
		
		function cancelDocNo($doc_no,$employee_id){
			/**
			 * @desc
			 * @param String $doc_no
			 * @return Boolean True or False
			 */
			//check flag='C'
			$msg_title="";
			$msg_promt="";
			$msg_status="N";
			$str_res="";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_t1="SELECT doc_date,status_no,total_point,member_id,flag,refer_doc_no
						FROM trn_diary1
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`doc_no`='$doc_no' AND
							`flag`<>'C'";
			$row_t1=$this->db->fetchAll($sql_t1);
			$status_update="N";
			if(count($row_t1)>0){
					$status_no=$row_t1[0]['status_no'];
					$total_point=$row_t1[0]['total_point'];
					$member_id=$row_t1[0]['member_id'];
					$doc_date=$row_t1[0]['doc_date'];
					$this->db->beginTransaction();
					try{
							$sql_updflag="UPDATE trn_diary1 
													SET
														flag='C',
														cancel_id='$employee_id',
														cancel_date=CURDATE(),
														cancel_time=CURTIME()
													WHERE
															`corporation_id`='$this->m_corporation_id' AND
															`company_id`='$this->m_company_id' AND
															`branch_id`='$this->m_branch_id' AND
															`doc_no`='$doc_no'";
							$this->db->query($sql_updflag);
							$sql_updflag="UPDATE trn_diary2 
													SET
														flag='C'
													WHERE
															`corporation_id`='$this->m_corporation_id' AND
															`company_id`='$this->m_company_id' AND
															`branch_id`='$this->m_branch_id' AND
															`doc_no`='$doc_no'";
							$this->db->query($sql_updflag);
							//commit trans
							$this->db->commit();
							$status_update="Y";
						}catch(Zend_Db_Exception $e){
							//$e->getMessage();
							$this->db->rollBack();
						}//catch
						
						// step2
						
						if($status_update=="Y"){
								//cancel point
								$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_mem_pt");
								$this->db->beginTransaction();
								try{
									$sql_cancel_point="UPDATE com_mem_pt
															  SET flag='C'
															  WHERE `doc_no`='$doc_no'";
									$this->db->query($sql_cancel_point);
									$this->db->commit();
								}catch(Zend_Db_Exception $e){
									$this->db->rollBack();
									//$status_update="N";
								}//catch								
								//update stock
								$this->updateStock($doc_no);
																
								//cancel new member
								if($status_no=='06'){
									$doc_cnref=$row_t1[0]['refer_doc_no'];
									$sql_cnref="UPDATE trn_diary1 SET cn_status='' WHERE doc_no='$doc_cnref'";
									$this->db->query($sql_cnref);
								}else if($status_no=='05'){
									$this->cancelMember($member_id,$employee_id);
								}else if($status_no=='01'){
									//
									//check 02 before 01
									$res_cancel_member=$this->cancelMember($member_id,$employee_id);
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");		
									if($res_cancel_member=='Y'){							
											$sql_bill02="SELECT doc_no
															FROM trn_diary1
															WHERE
																`corporation_id`='$this->m_corporation_id' AND
																`company_id`='$this->m_company_id' AND
																`branch_id`='$this->m_branch_id' AND
																`doc_date`='$doc_date' AND 
																`status_no`='02' AND 
																`member_id`='$member_id'";
											$row_bill02=$this->db->fetchAll($sql_bill02);									
											if(count($row_bill02)>0){
												$doc_no_bill02=$row_bill02[0]['doc_no'];
												$this->cancelDocNo($doc_no_bill02,$employee_id);
											}//if
									
									}//if res_cancel_member=Y
									
								}//if status_no=01					
								
					}//if $status_update=Y
					
					$msg_title="ผลการยกเลิกเอกสาร";
					$msg_promt="ยกเลิกเอกสาร $doc_no สมบูรณ์";
					$msg_status="Y";						
			}else{
				$msg_title="เกิดความผิดพลาด";
				$msg_promt="เอกสาร $doc_no ถูกยกเลิกแล้ว กรุณาตรวจสอบอีกครั้ง";
				$msg_status="N";
			}
			$str_res.=$msg_status."#".$msg_promt."#".$msg_title;
			return $str_res;	
		}//func	
		
		function getBill2Day(){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_doc="SELECT 
									doc_date,doc_no,flag,member_id,status_no,refer_member_id,amount,net_amt,paid,cashier_id,bank_tp
								FROM 
									trn_diary1 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									doc_date = '$this->doc_date'
								ORDER BY doc_no ASC";
			$rows_doc=$this->db->fetchAll($sql_doc);
			if(count($rows_doc)>0){
				$i=0;
				foreach($rows_doc AS $data){
					$paid=$data['paid'];
					$bank_tp=$data['bank_tp'];
					if(trim($paid)!=''){

						$sql_paid="SELECT paid,description FROM com_paid WHERE paid='$paid' ";
						$row_paid=$this->db->fetchAll($sql_paid);					
						if($bank_tp=="ABA PAY+" || $bank_tp=="ABA PAY")
						{
							$rows_doc[$i]['paid']='ABA PAY';
						}
						else
						{
							if(count($row_paid)>0){
								$rows_doc[$i]['paid']=$row_paid[0]['description'];
							}
						}	
					}
					$i++;
				}
			}
			return $rows_doc;
		}//func
		
		/**
		 * @desc
		 * 11032013 
		 */
		
		function getBillProfiles($member_no){
			/**
			 * @desc ใช้ดูประวัติการซื้อของทุกบิลของสมาชิก
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_doc="SELECT 
									doc_date,doc_no,flag,member_id,status_no,refer_member_id,amount,net_amt,paid,cashier_id
								FROM 
									trn_diary1 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									member_id = '$member_no' 
								ORDER BY doc_no ASC";
			$rows_doc=$this->db->fetchAll($sql_doc);
			if(count($rows_doc)>0){
				$i=0;
				foreach($rows_doc AS $data){
					$paid=$data['paid'];
					if(trim($paid)!=''){
						$sql_paid="SELECT paid,description FROM com_paid WHERE paid='$paid'";
						$row_paid=$this->db->fetchAll($sql_paid);
						if(count($row_paid)>0){
							$rows_doc[$i]['paid']=$row_paid[0]['description'];
						}
					}
					$i++;
				}
			}
			return $rows_doc;
		}//func
		/**
		 * 13032013
		 * Enter description here ...
		 * @param unknown_type $member_no
		 */
		function getBillShortProduct($member_no,$promo_code){
			/**
			 * @desc ใช้ดูประวัติการซื้อของบิลสินค้า short
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk_pdt="SELECT product_id FROM promo_detail WHERE promo_code='$promo_code'";
			$row_chk_pdt=$this->db->fetchAll($sql_chk_pdt);
			$str_pdt="";
			foreach($row_chk_pdt as $data){
				$str_pdt.="'".$data['product_id']."',";
			}
			$str_pdt=substr($str_pdt,0,-1);
			
			$sql_pdtshort="SELECT* FROM promo_short WHERE promo_code ='$promo_code'";
			$rows_pdtshort=$this->db->fetchAll($sql_pdtshort);
			$start_date=$rows_pdtshort[0]['start_date'];
			$end_date=$rows_pdtshort[0]['end_date'];
			$newcard=$rows_pdtshort[0]['newcard'];
			
			$andWhere="";
			if($newcard=='Y'){
				//check bill 01
				$andWhere.=" AND status_no='01'";
			}
			
//			$str_pro="";
//			foreach($rows_pdtshort as $data){
//				if($data['play_promo_code']!='ALL'){
//					$str_pro.="'".$data['play_promo_code']."',";
//				}
//			}//foreach
//			$str_pro=substr($str_pro,0,-1);			
//			if($str_pro!=''){
//				//check bill co promo
//				$andWhere.=" AND application_id IN($str_pro)";
//			}
			
			$sql_doc="SELECT 
									doc_date,doc_no,flag,member_id,status_no,refer_member_id,amount,net_amt,paid,cashier_id,application_id
								FROM 
									trn_diary1 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									member_id = '$member_no' 	AND 
									flag<>'C' AND
									doc_date >= '$start_date'";													
			$rows_doc=$this->db->fetchAll($sql_doc);			
			$chk_receive='0';	
			$k=0;
			
			foreach($rows_doc as $data){
				$doc_no=$data['doc_no'];
				$sql_d2="SELECT COUNT(*)	FROM trn_diary2 WHERE doc_no='$doc_no' AND product_id IN($str_pdt)";							
				$n_d2=$this->db->fetchOne($sql_d2);
				if($n_d2>0){
					$chk_receive='1';
					break;
				}
				$k++;
			}//foreach
					
			
			if(empty($rows_doc) || $chk_receive=='1' ){
				$j=0;
				foreach($rows_doc as $data){
					$rows_doc[$j]['status_receive']='Y';
					$j++;
				}
			}else{
				$j=0;
				foreach($rows_doc as $data){
					$rows_doc[$j]['status_receive']='N';
					$j++;
				}
			}//if
			
			$arr_r=array();
			$j=0;
			foreach($rows_doc as $data){
				if($data['status_no']=='01'){
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
					$arr_r[$j]['status_receive']=$data['status_receive'];
					$j++;
				}				
			}//foreach
			
			return $arr_r;
		}//func
		
		function getDocNo($str_doctp="",$actions,$start_date="",$end_date=""){
			/**
			 * @desc @use for cancel doc only and now we use question doc too
			 * @param String $str_doctp : string set to find doc_no
			 * @return Array $rows_doc : array of doc_date and doc_no
			 */
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");			
			if($str_doctp=="") return false;
			$str_doctp=strtoupper($str_doctp);
			$tbl_target="trn_diary1";
			if($str_doctp=='OR'){
				$tbl_target="trn_diary1_or";
			}else if($str_doctp=='RQ'){
				$tbl_target="trn_diary1_rq";
			}else if($str_doctp=='IQ' || $str_doctp=='PL'){
				$tbl_target="trn_diary1_iq";
			}else if($str_doctp=='RT'){
				//*WR21122016
				$tbl_target="trn_diary1_rt";
			}			
			
			$arr_doctp=explode(",",$str_doctp);
			$in_doctp='';
			foreach($arr_doctp as $doc_tp){
				$doc_tp=strtoupper($doc_tp);
				$in_doctp.="'$doc_tp',";
			}//foreach
			$pos=strrpos($in_doctp,',');
			if($pos!=0){
				$in_doctp=substr($in_doctp,0,$pos);
			}
			
			$range_date='';
			if($start_date!='' && $end_date!=''){
				$range_date=" doc_date BETWEEN '$start_date' AND '$end_date' AND ";
			}
			
			$isf='';
			if($actions=='cancel_doc'){
				$isf=" AND flag<>'C' AND doc_date='$this->doc_date'";
			}
			
//			else{
//				$isf=" AND doc_date >=date_add('$this->doc_date',interval -2 month)";
//			}
		
			//DATE_FORMAT(doc_date,'%d/%m/%Y') AS doc_date2		
			$sql_doc="SELECT 
									member_id,status_no,doc_date,doc_no,flag
								FROM 
									$tbl_target
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									$range_date
									doc_tp IN($in_doctp)  $isf
								ORDER BY doc_date DESC,doc_no ASC";
			$rows_doc=$this->db->fetchAll($sql_doc);
			return $rows_doc;
		}//func

		function getDocNofromwh($str_doctp="",$actions,$start_date="",$end_date=""){
		$wh_ip='10.100.59.156';
		$db_wh="warehouse_kh";
		$usr_wh="warehouse_kh_db";
		$pass_wh='8s6CUpEh7LXDdN8E2B5hzVjhMHwVehYB';
		$conn=mysql_connect($wh_ip,$usr_wh,$pass_wh) OR die(MYSQL_ERROR());		
		$conn2=mysql_select_db("warehouse_kh",$conn) OR die(MYSQL_ERROR());
		
			if($conn2){
				$range_date='';
				if($start_date!='' && $end_date!=''){
					$range_date=" doc_date BETWEEN '$start_date' AND '$end_date' ";
				}
					//seach data exist
					$sql_search="SELECT doc_date,doc_no,quantity FROM trn_in2shop_head
									WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									$range_date
								ORDER BY doc_date DESC,doc_no ASC";
					$sql_query=mysql_query($sql_search,$conn);
					$json_data=array();
					while ($row=mysql_fetch_assoc($sql_query)) {
						$rows[] = array(
										"doc_date" => $rs['doc_date'],
										"doc_no" => $rs['doc_no'],
										"flag"=>$rs['flag1']
									);
						 array_push($json_data,$row);
					}
			}
			
			// mysql_close($conn);
			return $json_data;
		}//func

		function getDestShop(){
			$sql_branch_type="SELECT
									type_id
							FROM 
									com_shop_destination
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' ";
			$rs_type_id=$this->db->fetchAll($sql_branch_type);
			$row_typeid=$rs_type_id[0]['type_id'];
			if($row_typeid=='WH'){ // seach shop
				$sql_shop="SELECT 
									company_id,branch_id,branch_name,com_ip
								FROM 
									com_shop_destination
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id!='$this->m_branch_id' AND
									type_id='SHOP'
								ORDER BY branch_id DESC";
			}
			if($row_typeid=='SHOP'){ //seach wh
				$sql_shop="SELECT 
									company_id,branch_id,branch_name,com_ip
								FROM 
									com_shop_destination
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id!='$this->m_branch_id' AND
									type_id='WH'
								ORDER BY branch_id DESC";
			}
			$rows_shop=$this->db->fetchAll($sql_shop);
			return $rows_shop;

		}//func
		
		
		
		
		function getReTemp($page,$qtype,$query,$rp,$sortname,$sortorder,$doc_no_start='',$doc_no_stop=''){
			/**
			 * @name getCshTemp
			 * @desc			
			 * @param $page is
			 * @param $qtype is
			 * @param $query is
			 * @param $rp is
			 * @param $sortname is
			 * @param $sortorder is
			 * @return json format string of data
			 * @lastmodify :15102011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$doc_range="";
			if($doc_no_start!='' && $doc_no_stop!=''){
				$doc_range=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
			}
						
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = "ORDER BY $sortname $sortorder";
			
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_diary2'); 
			$sql_numrows = "SELECT COUNT(*) FROM trn_diary2 WHERE doc_date='$this->doc_date' $doc_range ";
			$total = $this->db->fetchOne($sql_numrows);		
			
			$sql_l="SELECT 
							a.id as id,
							a.doc_no,
							a.doc_date,
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
							trn_diary2 as a LEFT join com_product_master as b
							ON(a.product_id=b.product_id)		
						WHERE
							a.doc_date='$this->doc_date'	$doc_range				
							$sort $limit";
			//echo $sql_l;
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
											$row['doc_date'],
											$row['doc_no'],
											$row['product_id'],
											$row['product_name'],
											'Y',
											intval($row['quantity']),
											//$row['unit'],
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
		
		function showContact(){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_contact");
			$sql_contact="SELECT* FROM com_contact WHERE brand='$this->m_corporation_id' OR brand='ALL'";
			$rows_contact=$this->db->fetchAll($sql_contact);			
			echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' bgcolor='#FFFFFF'>";
			echo "
						<tr bgcolor='#669999'>
							<td align='center' width='30%'><span style='color:#FFFFFF;font-size:24px;'>NAME</span></td>
							<td align='center' width='25%'><span style='color:#FFFFFF;font-size:24px;'>TELEPHONE</span></td>
							<td align='center' width='10%' align='center'><span style='color:#FFFFFF;font-size:24px;'>NETWORK</span></td>
							<td align='center'><span style='color:#FFFFFF;font-size:24px;'>DESCRIPTION</span></td>
						</tr>";
			if(count($rows_contact)>0){
				$i=0;
				foreach($rows_contact as $data){
					$name=$data['name'];
					$mobile=$data['mobile'];
					$mobile_type=$data['mobile_type'];
					$desc=$data['description'];
					($i%2==0)?$bg_color="#CDCDC7":$bg_color="#99CCCC";
					echo "
						<tr bgcolor='$bg_color'>
							<td align='center'>$name</td>
							<td align='center'>$mobile</td>
							<td align='center'>$mobile_type</td>
							<td align='center'>$desc</td>
						</tr>";
					$i++;
				}
			}else{
				echo "
						<tr>
							<td colspan='2' align='center'>DATA NOT FOUND</td>
						</tr>";
			}
		
			echo "</table>";
		}//func

		function trn_inv_in2shop($doc_no,$dest_branch,$dest_ip){
		//select data from trn_diary1
		$sql_branch_start="SELECT 
		id,
		corporation_id,
		company_id,
		doc_date,
		doc_no,
		flag,
		quantity 
		FROM trn_diary1 
		WHERE  `doc_no` = '$doc_no' ";
		$row_t1=$this->db->fetchAll($sql_branch_start);
		$row_doc_date=$row_t1[0]['doc_date'];
		$row_qty=$row_t1[0]['quantity'];
		//select data from trn_diary2
		$sql_t2_search="SELECT 
		id,
		corporation_id,
		company_id,
		doc_date,
		doc_no,
		seq,
		product_id,
		quantity,
		price
		FROM trn_diary2 
		WHERE  `doc_no` = '$doc_no' ";
		$row_t2=$this->db->fetchAll($sql_t2_search);
		//connect to shop
		// $this->db->closeConnection();
		//$dbshop=mysql_connect($dest_ip,'pos-ssup',"P0z-$$up",'pos_ssup');
		$hst_local="localhost";
		$usr_local="pos-ssup";
		$pwd_local='P0z-$$up'; 
		// $usr_local="support";
		// $pwd_local='$upport0789'; 
		
		/*****************************************/
		$wh_ip='10.100.59.156';
		$usr_wh="warehouse_kh_db";
		$pass_wh='8s6CUpEh7LXDdN8E2B5hzVjhMHwVehYB';
		$conn=mysql_connect($wh_ip,$usr_wh,$pass_wh) OR die(MYSQL_ERROR());	
		// $conn=mysql_connect($dest_ip,$usr_local,$pwd_local) OR die(MYSQL_ERROR());	
		$conn2=mysql_select_db("warehouse_kh",$conn) OR die(MYSQL_ERROR());				
			if($conn2){
					//seach data exist
					$sql_search="SELECT * FROM trn_in2shop_head
									WHERE
										`doc_no`='$doc_no'";
					$sql_query=mysql_query($sql_search,$conn);
					$rs_search=mysql_fetch_row($sql_query);
					//cleardata exist before insert new record
					if($rs_search>0){
					$sql_delete="DELETE FROM trn_in2shop_head
							WHERE `doc_no`='$doc_no' ";
					$rs_delete=mysql_query($sql_delete,$conn);
					$sql_delete="DELETE FROM trn_in2shop_list
							WHERE `doc_no`='$doc_no' ";
					$rs_delete=mysql_query($sql_delete,$conn);
					}
					//insert data to trn_in2shop_head
					$sql_insert="INSERT INTO trn_in2shop_head
					SET
						`corporation_id`='$this->m_corporation_id',  	  	  	
						`company_id`='$this->m_company_id', 	  	  	 
						`branch_id`='$dest_branch', 	  	  	 
						`doc_date`='$row_doc_date',	  	 
						`doc_no`='$doc_no',  	  	 
						`flag1`='D',  	  	  	 
						`status_transfer`='Y',  	  	  	 
						`quantity`='$row_qty'";
					$r_sql_insert=mysql_query($sql_insert,$conn);
					//insert data to trn_in2shop list
					foreach($row_t2 as $t2_data[0]){
						$t2_doc_date=$t2_data[0]['doc_date'];
						$t2_qty=$t2_data[0]['quantity'];
						$t2_seq=$t2_data[0]['seq'];
						$t2_product_id=$t2_data[0]['product_id'];
						$t2_qty=$t2_data[0]['quantity'];
						$t2_price=$t2_data[0]['price'];
						
					$t2_insert="INSERT INTO trn_in2shop_list
					SET
						`corporation_id`='$this->m_corporation_id',  	  	  	
						`company_id`='$this->m_company_id', 	  	  	 
						`branch_id`='$dest_branch', 	  	  	 
						`doc_date`='$t2_doc_date',	  	 
						`doc_no`='$doc_no',
						`seq`='$t2_seq', 	 
						`product_id`='$t2_product_id',	 
						`flag1`='D',  	  	  	 
						`status_transfer`='Y',  	  	  	 
						`quantity`='$t2_qty',
						`price`='$t2_price'";
					$rs_t2_insert=mysql_query($t2_insert,$conn);

					}
				

				if($rs_t2_insert){
					$status_exist='Y';
				}else{
					$status_exist=MYSQL_ERROR();
				}
				mysql_close($conn);
			}		
		// if(count($row_t1)>0){
		// 	$status_exist='Y';
		// }
		return $status_exist;
		}//func


		function download_inv_fromwh($doc_no,$dest_branch,$dest_ip){
		$wh_ip='10.100.59.156';
		$usr_wh="warehouse_kh_db";
		$pass_wh='8s6CUpEh7LXDdN8E2B5hzVjhMHwVehYB';
		$conn=mysql_connect($wh_ip,$usr_wh,$pass_wh) OR die(MYSQL_ERROR());	
		$conn2=mysql_select_db("warehouse_kh",$conn) OR die(MYSQL_ERROR());	
		if($conn){
			//search in2shop_head in wh
			$sql_search_head="SELECT * FROM trn_in2shop_head
									WHERE
										`doc_no`='$doc_no'";
			$sql_query_head=mysql_query($sql_search_head,$conn);
			$rs_search_head=mysql_fetch_assoc($sql_query_head);
			$branch_id=$rs_search_head['branch_id'];
			$doc_date=$rs_search_head['doc_date'];
			$doc_no=$rs_search_head['doc_no'];
			$flag1=$rs_search_head['flag1'];
			$status_transfer=$rs_search_head['status_transfer'];
			$quantity=$rs_search_head['quantity'];
			if($rs_search_head>0){
				$search_duplicate="SELECT * FROM trn_in2shop_head WHERE `doc_no`='$doc_no' ";
				$rs_duplicate=$this->db->query($search_duplicate);
				if($rs_duplicate){//delete old duplicate doc_no
					$sql_delete="DELETE FROM trn_in2shop_head
					WHERE `doc_no`='$doc_no' ";
					$this->db->query($sql_delete);
					$sql_delete="DELETE FROM trn_in2shop_list
					WHERE `doc_no`='$doc_no' ";
					$this->db->query($sql_delete);
				}
				$sql_insert_head="INSERT INTO trn_in2shop_head
					SET
						`corporation_id`='$this->m_corporation_id',  	  	  	
						`company_id`='$this->m_company_id', 	  	  	 
						`branch_id`='$branch_id', 	  	  	 
						`doc_date`='$doc_date',	  	 
						`doc_no`='$doc_no',  	  	 
						`status_transfer`='$status_transfer',
						`flag1`='$flag1',  	  	  	   	  	  	 
						`quantity`='$quantity'";
					$this->db->query($sql_insert_head);

				//search in2shop_list in wh
				$sql_search_list="SELECT * FROM trn_in2shop_list
				WHERE
					`doc_no`='$doc_no'";
				$sql_query_list=mysql_query($sql_search_list,$conn);
				while($rs_search_list=mysql_fetch_assoc($sql_query_list)){
					$branch_id=$rs_search_list['branch_id'];
					$doc_date=$rs_search_list['doc_date'];
					$doc_no=$rs_search_list['doc_no'];
					$seq=$rs_search_list['seq'];
					$product_id=$rs_search_list['product_id'];
					$flag1=$rs_search_list['flag1'];
					$status_transfer=$rs_search_list['status_transfer'];
					$quantity=$rs_search_list['quantity'];
					$price=$rs_search_list['price'];
					//insert in2shop_list
					$sql_insert_list="INSERT INTO trn_in2shop_list
					SET
						`corporation_id`='$this->m_corporation_id',  	  	  	
						`company_id`='$this->m_company_id', 	  	  	 
						`branch_id`='$branch_id', 	  	  	 
						`doc_date`='$doc_date',	  	 
						`doc_no`='$doc_no',  
						`seq`='$seq',	  
						`product_id`='$product_id',	 
						`status_transfer`='$status_transfer',
						`flag1`='$flag1',  	  	  	   	  	  	 
						`quantity`='$quantity',
						`price`='$price'
						";
					$this->db->query($sql_insert_list);

					}

				$status_exist='Y';
			}else{
				$status_exist='N';
			}
		}		
		return $status_exist;
		}//end func
		
	}//class
?>