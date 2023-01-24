<?php
set_time_limit(0);
include("connect.php");


							
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);

$find="select * from member_register_change where add_send_status='' order by id desc ";
echo $find;
$run=mysql_query($find,$conn_local) or die(mysql_error());
$rows=mysql_num_rows($run);
echo "Rows change profile:$rows<br>";
for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($run);
	
	$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("Offline");
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);
	
	$insert="insert into member_register_change(`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`, `change_id_card`, `change_birthday`, `change_fname`, `change_lname`, `change_mobile`, `upmaster`, `id_img`, `status_no_address`, `otp_mobile`, `otp_code`, `otp_confirm`, `add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`, `mobile_no_new`, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`) values('$data[add_ip]','$data[add_shop]','$data[add_date]','$data[add_time]','$data[add_user]','$data[add_type]','$data[add_member_no]','$data[add_doc_no]','$data[add_doc_date]','$data[add_readtype]','$data[change_id_card]','$data[change_birthday]','$data[change_fname]','$data[change_lname]','$data[change_mobile]','$data[upmaster]','$data[id_img]','$data[status_no_address]','$data[otp_mobile]','$data[otp_code]','$data[otp_confirm]','$data[add_send_status]','$data[add_send_date]','$data[add_send_time]','$data[var1]','$data[var2]','$data[var3]','$data[num1]','$data[num2]','$data[num3]','$data[transfer_date]','$data[customer_id_new]','$data[customer_id]','$data[mobile_no]','$data[mobile_no_new]','$data[phone_home_office]','$data[phone_home]','$data[phone_office]','$data[id_card]','$data[prefix]','$data[name]','$data[surname]','$data[mr_en]','$data[fname_en]','$data[lname_en]','$data[sex]','$data[nation]','$data[address]','$data[mu]','$data[road]','$data[area]','$data[region_id]','$data[province_id]','$data[province_name]','$data[district_id]','$data[district]','$data[sub_district]','$data[sub_district_id]','$data[zip]','$data[card_at]','$data[start_date]','$data[end_date]','$data[birthday]','$data[brand]','$data[shop]','$data[doc_no]','$data[doc_date]','$data[email_]','$data[facebook]','$data[customer_type]','$data[send_company]','$data[send_address]','$data[send_mu]','$data[send_home_name]','$data[send_soi]','$data[send_road]','$data[send_tambon_id]','$data[send_tambon_name]','$data[send_amphur_id]','$data[send_amphur_name]','$data[send_province_id]','$data[send_province_name]','$data[send_postcode]','$data[send_tel]','$data[send_mobile]','$data[send_fax]','$data[send_remark]','$data[reg_user]','$data[reg_date]','$data[reg_time]','$data[upd_user]','$data[upd_date]','$data[upd_time]','$data[sendtoserver_status]','$data[sendtoserver_date]','$data[sendtoserver_time]')
	";
	$run_insert=mysql_query($insert,$conn_service);
	if($run_insert){
		mysql_close($conn_service);
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		$mark="update member_register_change set add_send_status='Y', add_send_date=date(now()),add_send_time=time(now()) where id='$data[id]' ";
		mysql_query($mark,$conn_local);
		mysql_close($conn_local);
	}
}

						
?>