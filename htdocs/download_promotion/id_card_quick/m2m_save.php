<?php
set_time_limit(0);
include("connect.php");

$status_readcard=$_GET['status_readcard'];
$status_photo=$_GET['status_photo'];
$num_snap=$_GET['num_snap'];
$id_img=$_GET['id_img'];
$ip_this=$_GET['ip_this'];
$address=$_GET['address'];
$mu=$_GET['mu'];
$tambon_name=$_GET['tambon_name'];
$amphur_name=$_GET['amphur_name'];
$province_name=$_GET['province_name'];
$mr=$_GET['mr'];
$sex=$_GET['sex'];
$mr_en=$_GET['mr_en'];
$fname_en=$_GET['fname_en'];
$lname_en=$_GET['lname_en'];
$card_at=$_GET['card_at'];
$start_date=$_GET['start_date'];
$end_date=$_GET['end_date'];
$promo_code=$_GET['promo_code'];
$id_card=$_GET['id_card'];
$fname=$_GET['fname'];
$lname=$_GET['lname'];
$mobile_no=$_GET['mobile_no'];
$birthday=$_GET['birthday'];

$otp_code=$_GET['otp_code'];
$val_otp=$_GET['val_otp'];
$member_no=$_GET['member_no'];
$chk_ecoupon=$_GET['chk_ecoupon'];






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


/*if($id_card!=""){
	mysql_select_db("ssup");
	//chk id card
	$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' 	";
	$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
				$dataemp=mysql_fetch_array($run_chk_idcard);
				echo "stop###รหัสประชาชนนี้เป็นของพนักงาน SSUP ซึ่งทาง OP กำหนดไว้ว่า พนักงาน SSUP ไม่สามารถร่วมโปรโมชั่นนี้ได้";
			return false;
		}
	}
}
*/


if($status_readcard=="MANUAL"){
	//chk otp
	$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&otp=$val_otp&promotion=$promo_code";	
	$ftp_api_chkotp = @fopen($api_chkotp, "r");
	$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
	$ans_api_chkotp=json_decode($ans_api_chkotp, true);
	if($ans_api_chkotp['otp_status']!="OK"){
		echo "$ans_api_chkotp[otp_status]###$ans_api_chkotp[msg]";
		return false;
	}
}




$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);



$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$val_otp',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				      
";
//echo $add;
$run_add=mysql_query($add,$conn_local);
if($run_add){
	echo "Y###Complete";
}else{
	echo "N###ไม่สามารถบันทึกข้อมูลบัตรประชาชนได้";
}


mysql_close($conn_local);
?>