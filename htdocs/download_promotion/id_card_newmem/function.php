<?php
function up_profile($data,$server,$user,$pass,$db){

	$conn_local=mysql_connect($server,$user,$pass);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");			
	mysql_select_db($db);
	
	$chk_have="select * from member_register_offline where customer_id='$data[customer_id]' ";
	$run_have=mysql_query($chk_have,$conn_local);
	$rows_have=mysql_num_rows($run_have);
	if($rows_have>0){
		$up="
		update member_register_offline 
		set
		 `mobile_no`='$data[mobile_no]', `id_card`='$data[id_card]', `prefix`='$data[prefix]', `name`='$data[name]', `surname`='$data[surname]', `birthday`='$data[birthday]', `shop`='$data[shop]', `application_id`='$data[application_id]', `send_company`='$data[send_company]', `send_address`='$data[send_address]', `send_mu`='$data[send_mu]', `send_home_name`='$data[send_home_name]', `send_soi`='$data[send_soi]', `send_road`='$data[send_road]', `send_tambon_id`='$data[send_tambon_id]', `send_tambon_name`='$data[send_tambon_name]', `send_amphur_id`='$data[send_amphur_id]', `send_amphur_name`='$data[send_amphur_name]', `send_province_id`='$data[send_province_id]', `send_province_name`='$data[send_province_name]', `send_postcode`='$data[send_postcode]', `send_tel`='$data[send_tel]', `send_mobile`='$data[send_mobile]', `send_fax`='$data[send_fax]', `send_remark`='$data[send_remark]', `time_up`=now()
		where
		customer_id='$data[customer_id]'
		 ";
	}else{
		$up="
		insert member_register_offline 
		set
		 `mobile_no`='$data[mobile_no]', `id_card`='$data[id_card]', `prefix`='$data[prefix]', `name`='$data[name]', `surname`='$data[surname]', `birthday`='$data[birthday]', `shop`='$data[shop]', `application_id`='$data[application_id]', `send_company`='$data[send_company]', `send_address`='$data[send_address]', `send_mu`='$data[send_mu]', `send_home_name`='$data[send_home_name]', `send_soi`='$data[send_soi]', `send_road`='$data[send_road]', `send_tambon_id`='$data[send_tambon_id]', `send_tambon_name`='$data[send_tambon_name]', `send_amphur_id`='$data[send_amphur_id]', `send_amphur_name`='$data[send_amphur_name]', `send_province_id`='$data[send_province_id]', `send_province_name`='$data[send_province_name]', `send_postcode`='$data[send_postcode]', `send_tel`='$data[send_tel]', `send_mobile`='$data[send_mobile]', `send_fax`='$data[send_fax]', `send_remark`='$data[send_remark]', `time_up`=now(),customer_id='$data[customer_id]'
		
		
		";
	}
	//echo $up;
	mysql_query($up,$conn_local);
	mysql_close($conn_local);


}

function up_card($data,$server,$user,$pass,$db){
	$conn_local=mysql_connect($server,$user,$pass);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");			
	mysql_select_db($db);
	$chk_have="select * from member_history_offline where member_no='$data[member_no]' ";
	$run_have=mysql_query($chk_have,$conn_local);
	$rows_have=mysql_num_rows($run_have);
	if($rows_have>0){
		$up="
		update member_history_offline 
		set
		`customer_id`='$data[customer_id]', `id_card`='$data[id_card]', `shop`='$data[shop]', `apply_date`='$data[apply_date]', `expire_date`='$data[expire_date]', `status_active`='$data[status_active]', `status`='$data[status]', `ops`='$data[ops]', `age_card`='$data[age_card]', `time_up`=now()
		where
		member_no='$data[member_no]'
		 ";
	}else{
		$up="
		insert member_history_offline 
		set
		`customer_id`='$data[customer_id]', `id_card`='$data[id_card]', `shop`='$data[shop]', `apply_date`='$data[apply_date]', `expire_date`='$data[expire_date]', `status_active`='$data[status_active]', `status`='$data[status]', `ops`='$data[ops]', `age_card`='$data[age_card]', `time_up`=now(),member_no='$data[member_no]'
		
		
		";
	}
	//echo $up;
	mysql_query($up,$conn_local);
	mysql_close($conn_local);
}
?>