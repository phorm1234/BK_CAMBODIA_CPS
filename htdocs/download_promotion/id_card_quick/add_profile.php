<?php
set_time_limit(0);
include("connect.php");
$member_no=$_GET['member_no'];
$customer_id=$_GET['customer_id'];
$status_readcard=$_GET['status_readcard'];
$status_photo=$_GET['status_photo'];
$num_snap=$_GET['num_snap'];
$id_img=$_GET['id_img'];

$id_card=$_GET['id_card'];
$hbd_day=$_GET['hbd_day'];

if(strlen($hbd_day)==1){
	$hbd_day="0" . $hbd_day;
}
$hbd_month=$_GET['hbd_month'];
if(strlen($hbd_month)==1){
	$hbd_month="0" . $hbd_month;
}

$hbd_year=$_GET['hbd_year'];
$birthday="$hbd_year-$hbd_month-$hbd_day";
$fname=$_GET['fname'];
$lname=$_GET['lname'];
$mobile_no_old=$_GET['mobile_no_old'];
$mobile_no_new=$_GET['mobile_no_new'];
$status_nothai=$_GET['status_nothai'];


//ข้อมูลจาก id card
$mr=$_GET['mr'];

$sex=$_GET['sex'];
if($sex=="ชาย"){
	$sex=1;
}else{
	$sex=2;
}

$address=$_GET['address'];
$mu=$_GET['mu'];
$tambon_name=$_GET['tambon_name'];
$amphur_name=$_GET['amphur_name'];
$province_name=$_GET['province_name'];
$mr_en=$_GET['mr_en'];
$fname_en=$_GET['fname_en'];
$lname_en=$_GET['lname_en'];
$card_at=$_GET['card_at'];
$start_date=$_GET['start_date'];
$start_arr=explode("/",$start_date);
$start_year=$start_arr[2]-543;
$start_date="$start_year-$start_arr[1]-$start_arr[0]";
$end_date=$_GET['end_date'];
$end_arr=explode("/",$end_date);
$end_year=$end_arr[2]-543;
$end_date="$end_year-$end_arr[1]-$end_arr[0]";		



//ที่อยู่ในการจัดส่ง
$status_no_address=$_GET['status_no_address'];
$send_address=$_GET['send_address'];
$send_mu=$_GET['send_mu'];
$send_home_name=$_GET['send_home_name'];
$send_soi=$_GET['send_soi'];
$send_road=$_GET['send_road'];
$send_province_id=$_GET['send_province_id'];
$send_province_name=$_GET['send_province_name'];
$send_amphur_id=$_GET['send_amphur_id'];
$send_amphur_name=$_GET['send_amphur_name'];
$send_tambon_id=$_GET['send_tambon_id'];
$send_tambon_name=$_GET['send_tambon_name'];
$send_postcode=$_GET['send_postcode'];
$send_fax=$_GET['send_fax'];
$email_=$_GET['email_'];
						

$noid_type=$_GET['noid_type'];
$noid_remark=$_GET['noid_remark'];

						
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$clear_tmp="delete from member_register_change_tmp where add_ip='$ip' ";
$run_clear_tmp=mysql_query($clear_tmp,$conn_local);
if($run_clear_tmp){

		$find_tambon="select * from view_province where zip_tambon_nm_th='$tambon_name' and zip_amphur_nm_th='$amphur_name' and zip_province_nm_th='$province_name' ";
		$run_data_tambon=mysql_query($find_tambon,$conn_local);
		$data_tambon=mysql_fetch_array($run_data_tambon);
		$tambon_id=$data_tambon['zip_tambon_id'];
		$zipcode=$data_tambon['zipcode'];
		$find_amphur="select * from view_province where  zip_amphur_nm_th='$amphur_name' and zip_province_nm_th='$province_name' limit 1 ";
		$run_data_amphur=mysql_query($find_amphur,$conn_local);
		$data_amphur=mysql_fetch_array($run_data_amphur);	
		$amphur_id=$data_amphur['zip_amphur_id'];
		
		$find_province="select * from view_province where  zip_province_nm_th='$province_name' limit 1 ";
		$run_data_province=mysql_query($find_province,$conn_local);
		$data_province=mysql_fetch_array($run_data_province);	
		$province_id=$data_province['province_id'];
		
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];
		
		
		
		mysql_close($conn_local);
		
		$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("Offline");
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_service);
		$send_province_id=$_GET['send_province_id'];
		$find="select a.* from member_register as a inner join member_history as b
		on a.customer_id=b.customer_id where b.member_no='$member_no' limit 1 ";
		$run=mysql_query($find,$conn_service);
		
		$data=mysql_fetch_array($run);
		$name=$data['name'];
		$surname=$data['surname'];
		
		mysql_close($conn_service);
		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		$map_key_new="$fname$lname$birthday";
		$map_key_old="$data[name]$data[surname]$data[birthday]";
		
		//echo "$map_key_old===$map_key_new";
		
		$add_befor="insert into member_register_change_tmp(`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`, `add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`) values('$ip','$shop',date(now()),time(now()),'$data[add_user]','Befor','$member_no','$data[add_doc_no]','$doc_date','$status_readcard','$data[add_send_status]','$data[add_send_date]','$data[add_send_time]','$data[var1]','$data[var2]','$data[var3]','$data[num1]','$data[num2]','$data[num3]','$data[transfer_date]','$data[customer_id_new]','$data[customer_id]','$data[mobile_no]','$data[phone_home_office]','$data[phone_home]','$data[phone_office]','$data[id_card]','$data[prefix]','$data[name]','$data[surname]','$data[mr_en]','$data[fname_en]','$data[lname_en]','$data[sex]','$data[nation]','$data[address]','$data[mu]','$data[road]','$data[area]','$data[region_id]','$data[province_id]','$data[province_name]','$data[district_id]','$data[district]','$data[sub_district]','$data[sub_district_id]','$data[zip]','$data[card_at]','$data[start_date]','$data[end_date]','$data[birthday]','$data[brand]','$data[shop]','$data[doc_no]','$data[doc_date]','$data[email_]','$data[facebook]','$data[customer_type]','$data[send_company]','$data[send_address]','$data[send_mu]','$data[send_home_name]','$data[send_soi]','$data[send_road]','$data[send_tambon_id]','$data[send_tambon_name]','$data[send_amphur_id]','$data[send_amphur_name]','$data[send_province_id]','$data[send_province_name]','$data[send_postcode]','$data[send_tel]','$data[send_mobile]','$data[send_fax]','$data[send_remark]','$data[reg_user]','$data[reg_date]','$data[reg_time]','$data[upd_user]','$data[upd_date]','$data[upd_time]','$data[sendtoserver_status]','$data[sendtoserver_date]','$data[sendtoserver_time]')";
		$run_add_befor=mysql_query($add_befor,$conn_local);
		//echo $add_befor;
		if($run_add_befor){
			if($id_card!=$data['id_card']){
				$change_id_card='Y';
			}else{
				$change_id_card='';
			}
		
			if($birthday!=$data['birthday']){
				$change_birthday='Y';
			}else{
				$change_birthday='';
			}
			if($fname!=$data['name']){
				$change_fname='Y';
			}else{
				$change_fname='';
			}
			if($lname!=$data['surname']){
				$change_lname='Y';
			}else{
				$change_lname='';
			}
			if($mobile_no_new!=""){
				$change_mobile='Y';
			}else{
				$change_mobile='';
			}				
			$add_after="insert into member_register_change_tmp(`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`,`change_id_card`, `change_birthday`, `change_fname`, `change_lname`, `change_mobile`,status_no_address,`add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`,mobile_no_new, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`,id_img) values('$ip','$shop',date(now()),time(now()),'$data[add_user]','After','$member_no','$data[add_doc_no]','$doc_date','$status_readcard','$change_id_card', '$change_birthday','$change_fname', '$change_lname','$change_mobile','$status_no_address','$data[add_send_status]','$data[add_send_date]','$data[add_send_time]','$data[var1]','$data[var2]','$noid_remark','$noid_type','$data[num2]','$data[num3]','$data[transfer_date]','$data[customer_id_new]','$data[customer_id]','$data[mobile_no]','$mobile_no_new','$data[phone_home_office]','$data[phone_home]','$data[phone_office]','$id_card','$mr','$fname','$lname','$mr_en','$fname_en','$lname_en','$sex','$data[nation]','$address','$mu','$data[road]','$data[area]','$data[region_id]','$province_id','$province_name','$tambon_id','$tambon_name','$amphur_name','$amphur_id','$zipcode','$card_at','$start_date','$end_date','$birthday','$data[brand]','$data[shop]','$data[doc_no]','$data[doc_date]','$email_','$data[facebook]','$data[customer_type]','$data[send_company]','$send_address','$send_mu','$send_home_name','$send_soi','$send_road','$send_tambon_id','$send_tambon_name','$send_amphur_id','$send_amphur_name','$send_province_id','$send_province_name','$send_postcode','$data[send_tel]','$data[send_mobile]','$send_fax','$data[send_remark]','$data[reg_user]',date(now()),time(now()),'$data[upd_user]',date(now()),time(now()),'$data[sendtoserver_status]','$data[sendtoserver_date]','$data[sendtoserver_time]','$id_img')";
		
			$run_add_after=mysql_query($add_after,$conn_local);
			if($run_add_after){
				if($status_readcard=="AUTO"){
					
					if($data['id_card']!="" && $id_card==$data['id_card']){ //มี id card และตรงกับของใหม่  update
						$confrim_otp="";
						$up_full="Y";
						$wait_crm="";
						//echo "id_card!='' and id_card=>ok<br>";
						$up="update member_register_change_tmp set upmaster='Y' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);	
					}else if($data['id_card']!="" && $map_key_new==$map_key_old){//มี id card แต่ชื่อ+นามสกุล+วันเกิดตรง update
						$confrim_otp="";
						$up_full="Y";
						$wait_crm="";
						//echo "id_card=='' and key=>ok<br>";
						$up="update member_register_change_tmp set upmaster='Y' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);							
					}else if($data['id_card']=="" && $map_key_new==$map_key_old){//ไม่มี id card แต่ชื่อ+นามสกุล+วันเกิดตรง update
						$confrim_otp="";
						$up_full="Y";
						$wait_crm="";
						//echo "id_card=='' and key=>ok<br>";
						$up="update member_register_change_tmp set upmaster='Y' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);
				
					}else if($data['id_card']=="" && $map_key_new!=$map_key_old){//id และ ชื่อ+นามสกุล+วันเกิดตรง ไม่ตรงเลย add record เก็บไว้
						$confrim_otp="Y";
						$up_full="";
						$wait_crm="CRM";
						$up="update member_register_change_tmp set upmaster='CRM' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);
						
					}else if($data['id_card']!="" && $id_card!=$data['id_card'] && $map_key_new!=$map_key_old){//id และ ชื่อ+นามสกุล+วันเกิดตรง ไม่ตรงเลย add record เก็บไว้
						$confrim_otp="Y";
						$up_full="";
						$wait_crm="CRM";
						$up="update member_register_change_tmp set upmaster='CRM' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);
					}
					
					
					
					//add idcard
					$add_idcard="
						insert into member_idcard(`shop`, `member_no`, `id_card`, `name`, `surname`, `birthday`, `address`, `mu`, `tambon_name`, `amphur_name`, `province_name`, `mr`, `sex`, `mr_en`, `fname_en`, `lname_en`, `card_at`, `start_date`, `end_date`, `time_up`) values('$shop','$member_no','$id_card','$fname','$lname','$birthday','$address','$mu','$tambon_name','$amphur_name','$province_name','$mr','$sex','$mr_en','$fname_en', '$lname_en','$card_at','$start_date','$end_date',now())
					";
					//echo $add_idcard;
					mysql_query($add_idcard,$conn_local);
					
				}else{//อ่านไม่ได้
					$confrim_otp="Y";
					if($data['id_card']!="" && $id_card==$data['id_card']){ //มี id card และตรงกับของใหม่  update
						$up_full="";
						//echo "id_card!='' and id_card=>ok<br>";
						$up="update member_register_change_tmp set upmaster='Y' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);	
					}else if($data['id_card']!="" && $map_key_new==$map_key_old){//มี id card แต่ชื่อ+นามสกุล+วันเกิดตรง update
						$up_full="";
						//echo "id_card=='' and key=>ok<br>";
						$up="update member_register_change_tmp set upmaster='Y' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);												
					}else if($data['id_card']=="" && $map_key_new==$map_key_old){//ไม่มี id card แต่ชื่อ+นามสกุล+วันเกิดตรง update
						$up_full="";
						//echo "id_card=='' and key=>ok<br>";
						$up="update member_register_change_tmp set upmaster='Y' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);
					}else if($data['id_card']=="" && $map_key_new!=$map_key_old){//id และ ชื่อ+นามสกุล+วันเกิดตรง ไม่ตรงเลย add record เก็บไว้
						$wait_crm="CRM";
						$up="update member_register_change_tmp set upmaster='CRM' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);
					}else if($data['id_card']!="" && $id_card!=$data['id_card'] && $map_key_new!=$map_key_old){//id และ ชื่อ+นามสกุล+วันเกิดตรง ไม่ตรงเลย add record เก็บไว้
						$wait_crm="CRM";
						$up="update member_register_change_tmp set upmaster='CRM' where add_ip='$ip' and add_type='After' and add_member_no='$member_no' ";
						mysql_query($up,$conn_local);
					}
				}	
				
			/*if($mobile_no_new!=""){
				$confrim_otp="Y";
			}	*/
			if($status_nothai==2){
				$confrim_otp="";
			}
			if($confrim_otp==""){ //ไม่ต้อง Confirm opt code
				$coppy="insert into member_register_change(`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`, `change_id_card`, `change_birthday`, `change_fname`, `change_lname`, `change_mobile`, `upmaster`, `add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`, `mobile_no_new`, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`)
				select 
				`add_ip`, `add_shop`, `add_date`, `add_time`, `add_user`, `add_type`, `add_member_no`, `add_doc_no`, `add_doc_date`, `add_readtype`, `change_id_card`, `change_birthday`, `change_fname`, `change_lname`, `change_mobile`, `upmaster`, `add_send_status`, `add_send_date`, `add_send_time`, `var1`, `var2`, `var3`, `num1`, `num2`, `num3`, `transfer_date`, `customer_id_new`, `customer_id`, `mobile_no`, `mobile_no_new`, `phone_home_office`, `phone_home`, `phone_office`, `id_card`, `prefix`, `name`, `surname`, `mr_en`, `fname_en`, `lname_en`, `sex`, `nation`, `address`, `mu`, `road`, `area`, `region_id`, `province_id`, `province_name`, `district_id`, `district`, `sub_district`, `sub_district_id`, `zip`, `card_at`, `start_date`, `end_date`, `birthday`, `brand`, `shop`, `doc_no`, `doc_date`, `email_`, `facebook`, `customer_type`, `send_company`, `send_address`, `send_mu`, `send_home_name`, `send_soi`, `send_road`, `send_tambon_id`, `send_tambon_name`, `send_amphur_id`, `send_amphur_name`, `send_province_id`, `send_province_name`, `send_postcode`, `send_tel`, `send_mobile`, `send_fax`, `send_remark`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `sendtoserver_status`, `sendtoserver_date`, `sendtoserver_time`
				from 
				member_register_change_tmp where add_ip='$ip' order by id asc
				";
				//echo $coppy;
				$run_coppy=mysql_query($coppy,$conn_local);
				mysql_close($conn_local);
				if($run_coppy){
					if($status_no_address=="false"){
					$val_upsendaddress="
email_='$email_',send_address='$send_address',send_mu='$send_mu',send_home_name='$send_home_name',send_soi='$send_soi',send_road='$send_road',send_tambon_id='$send_tambon_id',send_tambon_name='$send_tambon_name',send_amphur_id='$send_amphur_id',send_amphur_name='$send_amphur_name',send_province_id='$send_province_id', send_province_name='$send_province_name',send_postcode='$send_postcode',send_fax='$send_fax',
					";
					}else{
						$val_upsendaddress="";
					}
					if($up_full=="Y"){
						$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("Offline");
						mysql_query("SET character_set_results=utf8");
						mysql_query("SET character_set_client=utf8");
						mysql_query("SET character_set_connection=utf8");
						mysql_select_db($db_service);
						if($mobile_no_new!=""){
							$mobile_up="mobile_no='$mobile_no_new', ";
						}else{
							$mobile_up="";
						}
													
						$upprofile="
							update member_register 
							set
								$mobile_up
								$val_upsendaddress
								sex='$sex',
								prefix='$mr',
								name='$fname',
								surname='$lname',
								
								mr_en='$mr_en',
								fname_en='$fname_en',
								lname_en='$lname_en',
																
								birthday='$birthday',
								id_card='$id_card',
								
								address='$address',
								mu='$mu',
								district_id='$tambon_id',
								district='$tambon_name',
								sub_district_id='$amphur_id',
								sub_district='$amphur_name',
								province_id='$province_id',
								province_name='$province_name',
								zip='$zipcode',
								card_at='$card_at',
								start_date='$start_date',
								end_date='$end_date'
								where
								customer_id='$customer_id'
						";
						if($wait_crm!="CRM"){
							mysql_query($upprofile,$conn_service);
						}
						mysql_close($conn_service);
						
					}
					
					if($up_full==""){
						$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("Offline");
						mysql_query("SET character_set_results=utf8");
						mysql_query("SET character_set_client=utf8");
						mysql_query("SET character_set_connection=utf8");
						mysql_select_db($db_service);
						if($mobile_no_new!=""){
							$mobile_up="mobile_no='$mobile_no_new', ";
						}else{
							$mobile_up="";
						}
						$upprofile="
							update member_register 
							set
								$mobile_up
								$val_upsendaddress
								name='$fname',
								surname='$lname',
								birthday='$birthday',
								id_card='$id_card',
								upd_user='IDCARD',
								upd_date=date(now()),
								upd_time=time(now())
								
								where
								customer_id='$customer_id'
						";
						
						if($wait_crm!="CRM"){
							mysql_query($upprofile,$conn_service);
						}
						mysql_close($conn_service);
						
					}
						//echo $upprofile;				
					
					
					echo "finish";
				}else{
					echo "keep_coppy_error";
				}
			}else{
				
				echo "show_from_otp_code";
			}
				
				
				
			}else{
				echo "keep_after_error";
			}
		
		}else{
			echo "keep_befor_error";
		}


}else {//end if clear tmp
	echo "clear_tmp_error";
}


?>