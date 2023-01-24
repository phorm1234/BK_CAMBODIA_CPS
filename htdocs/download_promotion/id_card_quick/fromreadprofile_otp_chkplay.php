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
$mobile_new=$_GET['mobile_no'];
$birthday=$_GET['birthday'];

$otp_code=$_GET['otp_code'];
$val_otp=$_GET['val_otp'];
$member_no=$_GET['member_no'];
$chk_ecoupon=$_GET['chk_ecoupon'];
$confirm_mobile=$_GET['confirm_mobile'];



	$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_bypeple.php?member_no=$member_no", "r");
	$cr=@fgetss($fp, 4096);


	$conn_local=mysql_connect($server_local,$user_local,$pass_local);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_local);
	//chk have local
	$find="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and member_id in($cr) and net_amt>=1000 and  co_promo_code in('OI06320417','OPPLI300') ";
	$run_find=mysql_query($find,$conn_local);
	$rows_find=mysql_num_rows($run_find);
	mysql_close($conn_local);
	
	
	if($rows_find>0){
		echo "Y###Complete";

	}else{
		$conn_server=mysql_connect($server_service,$user_service,$pass_service);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_service);
		//chk have local
		$find="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and member_id in($cr) and net_amt>=1000 and  co_promo_code in('OI06320417','OPPLI300')";
		//echo $find;
		$run_find=mysql_query($find,$conn_server);
		$rows_find=mysql_num_rows($run_find);
		if($rows_find>0){
			echo "Y###Complete";
		}else{
			echo "N###ลูกค้ายังไม่ได้เล่นโปรไลน์ หรือ เล่นแล้วแต่ยอดสุทธิ ไม่ถึง 1,000 บาท";
		}
	}
?>