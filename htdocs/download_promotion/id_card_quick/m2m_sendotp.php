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
		if($mobile_no!=$profile_mobile){
			echo "MOBILEDIFF###";
			return false;
		}
		
	}else{
		echo "NOPROFILE###";
		return false;
	}
}




if($chk_ecoupon=="Y"){
	preg_match("/[[:digit:]]+\.?[[:digit:]]*/", $promo_code , $promo_code_num ) ;
	$otp_code_ok = substr(intval($promo_code_num[0])+$id_card+29381,-5);
	if($otp_code!=$otp_code_ok){
		echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
		return false;
	}
}


if($promo_code!="OX02031215"){
	$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=$promo_code&idcard=$id_card&act=request&memberid=$member_no";	
	//echo $path_api;
	$ftp_otpcode = @fopen($path_api, "r");
	$arrotpcode=@fgetss($ftp_otpcode, 4096);	
	$arrotpcode=json_decode($arrotpcode, true);
	//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
	if($arrotpcode['status']=="lock"){
		echo "stop###$arrotpcode[status_msg]";
		return false;
	}
	if($arrotpcode['status']=="NO"){
		echo "code_eror###$arrotpcode[status_msg]";
		return false;
	}


	$conn_local=mysql_connect($server_local,$user_local,$pass_local);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_local);

	//chk have local
	$find_local="select * from trn_diary1 where doc_date>='2017-04-01' and idcard='$id_card' and flag<>'C' and coupon_code='$promo_code'  ";
	//echo $find_local;
	$run_find_local=mysql_query($find_local,$conn_local);
	$rows_find_local=mysql_num_rows($run_find_local);
	if($rows_find_local>0){
		$data_local=mysql_fetch_array($run_find_local);
		echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
		return false;
	}

	//chk have local
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-04-01' and member_id='$member_no' and flag<>'C' and coupon_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัส Member นี้ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}
	}

}

$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&promortion=$promo_code";	

$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	


$arrotpcode=json_decode($arrotpcode, true);
echo "$arrotpcode[sms_status]###$arrotpcode[msg]";
?>