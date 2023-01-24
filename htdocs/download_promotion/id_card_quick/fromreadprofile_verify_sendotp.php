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

$confirm_mobile=$_GET['confirm_mobile'];


						
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


//chk have local
/*$find="select * from member_idcard_other where id_card='$id_card' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_local);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "IDCARD_PLAY";
	return false;
}
$find="select * from member_idcard_other where mobile_no='$mobile_no' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_local);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "MOBILE_PLAY";
	return false;
}

mysql_close($conn_local);
*/

//chk have server online
/*$conn_server=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);
//chk have local
$find="select * from member_idcard_other where id_card='$id_card' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_server);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "IDCARD_PLAY";
	return false;
}
$find="select * from member_idcard_other where mobile_no='$mobile_no' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_server);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "MOBILE_PLAY";
	return false;
}*/

if($member_no!=""){
	$conn_server=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);
	//chk have local
	$find="
	select b.* from 
	member_history as a inner join member_register as b
	on a.customer_id=b.customer_id
	where a.member_no='$member_no'
	";
	$run_find=mysql_query($find,$conn_server);
	$rows_find=mysql_num_rows($run_find);
	if($rows_find>0){
		$dataprofile=mysql_fetch_array($run_find);
		$profile_mobile=$dataprofile['mobile_no'];
		$profile_id_card=$dataprofile['id_card'];
		if($mobile_no!=$profile_mobile && $mobile_no!="" && $confirm_mobile=="N"){
			echo "MOBILEDIFF###";
			return false;
		}
		if($id_card!=$profile_id_card){
			echo "IDCARDDIFF###";
			return false;
		}
	}else{
		echo "NOPROFILE###";
		return false;
	}
}



if($id_card!=""){
	mysql_select_db("ssup");
	//chk id card
	$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' 	";
	//echo $chk_idcard;
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





$chk_id="http://10.100.53.2/ims/joke/app_service_op/api_member/api_verify_idcard.php?id_card=$id_card";	
$ftp_chk_id = @fopen($chk_id, "r");
$datachk_id=@fgetss($ftp_chk_id, 4096);	
$datachk_id=json_decode($datachk_id, true);
if($datachk_id[0]['status']=="Y"){
	echo $datachk_id[0]['status'] . "###" . $datachk_id[0]['msg'];
	return false;
}



$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&promortion=$promo_code";	
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	


$arrotpcode=json_decode($arrotpcode, true);
echo "$arrotpcode[sms_status]###$arrotpcode[msg]";
?>