<?php
set_time_limit(0);
include("connect.php");
$id_card=$_GET['id_card'];
$member_no=$_GET['member_no'];
$mobile_no=$_GET['mobile_no'];
$otp_confirm=$_GET['otp_confirm'];				
$customer_id=$_GET['customer_id'];
$status_readcard=$_GET['status_readcard'];
$status_no_address=$_GET['status_no_address'];
					
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
		
$find_shop="select *  from com_branch_computer limit 0,1";
$run_shop=mysql_query($find_shop,$conn_local);
$data_shop=mysql_fetch_array($run_shop);
$shop=$data_shop['branch_id'];
$com_ip=$data_shop['com_ip'];

$find_doc_date="select *  from com_doc_date";
$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
$data_doc_date=mysql_fetch_array($run_find_doc_date);
$doc_date=$data_doc_date['doc_date'];
		
$path_api="http://mshop.ssup.co.th/shop_op/op_membercard_check.php?idcard=$id_card&shop=$shop&memberid=$member_no&mobile=$mobile_no&otp=$otp_confirm";	
//echo $path_api;
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	


$arrotpcode=json_decode($arrotpcode, true);

if($arrotpcode['otp_status']=="OK"){
				$coppy="insert into member_register_change(`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`, `change_id_card`, `change_birthday`, `change_fname`, `change_lname`, `change_mobile`, `upmaster`,status_no_address,otp_mobile,otp_code,otp_confirm, `add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`, `mobile_no_new`, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`,id_img)
				select 
				`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`, `change_id_card`, `change_birthday`, `change_fname`, `change_lname`, `change_mobile`, `upmaster`,'$status_no_address','$mobile_no','','$otp_confirm', `add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`, `mobile_no_new`, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`,id_img
				from 
				member_register_change_tmp where add_ip='$ip' order by id asc
				";
				//echo $coppy;
				$run_coppy=mysql_query($coppy,$conn_local);
				if($run_coppy){
				
						$find_dataup="select * from member_register_change_tmp where add_ip='$ip' and upmaster='Y' and add_type='After' ";
						$run_find_dataup=mysql_query($find_dataup,$conn_local);
						$rows_dataup=mysql_num_rows($run_find_dataup);
						if($rows_dataup>0){
							$dataup=mysql_fetch_array($run_find_dataup);
							if($status_no_address=="false"){
								$val_upsendaddress="
		email_='$dataup[email_]',send_address='$dataup[send_address]',send_mu='$dataup[send_mu]',send_home_name='$dataup[send_home_name]',send_soi='$dataup[send_soi]',send_road='$dataup[send_road]',send_tambon_id='$dataup[send_tambon_id]',send_tambon_name='$dataup[send_tambon_name]',send_amphur_id='$dataup[send_amphur_id]',send_amphur_name='$dataup[send_amphur_name]',send_province_id='$dataup[send_province_id]', send_province_name='$dataup[send_province_name]',send_postcode='$dataup[send_postcode]',send_fax='$dataup[send_fax]',
							";
							}else{
								$val_upsendaddress="";
							}
												
							$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("Offline");
							mysql_query("SET character_set_results=utf8");
							mysql_query("SET character_set_client=utf8");
							mysql_query("SET character_set_connection=utf8");
							mysql_select_db($db_service);
							if($dataup['mobile_no_new']!=""){
								$mobile_up="mobile_no='$dataup[mobile_no_new]', ";
							}else{
								$mobile_up="";
							}
							$upprofile="
								update member_register 
								set
									$mobile_up
									$val_upsendaddress
									name='$dataup[name]',
									surname='$dataup[surname]',
	
									birthday='$dataup[birthday]',
									id_card='$dataup[id_card]'
									
									where
									customer_id='$customer_id'
							";
							mysql_query($upprofile,$conn_service);
							mysql_close($conn_service);
							//echo $upprofile;
					}
					echo "$arrotpcode[otp_status]##$arrotpcode[msg]";
				}else{
					echo "keep_coppy_error";
				}
								
}else {
	echo "$arrotpcode[otp_status]##$arrotpcode[msg]";
}

?>