<?php
set_time_limit(0);
header("Content-type:text/html; charset=utf8"); 
include("connect.php");

$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];
$var_otp=$_GET['var_otp'];
if($id_card==""){
	echo "STOP###ไม่พบรหัสบัตรประชาชนค่ะ";
	return false;
}


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

$chkotpcode="select * from memccs_bill where id_card='$id_card' and doc_date='$doc_date' and otpcode='$var_otp' ";
//echo $chkotpcode;
$run_chkotpcode=mysql_query($chkotpcode,$conn_local);
$rowschkotpcode=mysql_num_rows($run_chkotpcode);
if($rowschkotpcode>0){
	echo "STOP### OTP CODE นี้ถูกใช้ไปแล้ว กรุณากดขอ OTP CODE ใหม่ค่ะ";
	return false;
}

$conn_online=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);

/*$chkopid="select * from member_register  where id_card='$id_card' ";
$run_chkopid=mysql_query($chkopid,$conn_online);
$rowchkopid=mysql_num_rows($run_chkopid);
if($rowchkopid){
	echo "STOP###źѵ .  ѤẺ  . ᷹ѵҪԡ";
	return false;
}
if($rowchkopid>1){
	echo "STOP###źѵ . ҡ 1  к سѾവǹǡѺ beauty line ͹";
	return false;
}
*/

$findprofile="select * from member_register as a inner join member_history as b
on a.customer_id=b.customer_id
where
a.id_card='$id_card' and b.expire_date>='$doc_date' and b.status_active='Y'   and b.member_no like 'ID%'
";
//echo $findprofile;
$run_findprofile=mysql_query($findprofile,$conn_online);
$rowsprofile=mysql_num_rows($run_findprofile);
if($rowsprofile==0){
	echo "STOP###ไม่พบข้อมูลสมาชิก";
	return false;
}

$profile=mysql_fetch_array($run_findprofile);
$mobile_no=$profile['mobile_no'];


$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&otp=$var_otp&promotion=OPID300";	
//echo $api_chkotp;
$ftp_api_chkotp = @fopen($api_chkotp, "r");
$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
$ans_api_chkotp=json_decode($ans_api_chkotp, true);

echo "$ans_api_chkotp[otp_status]###$ans_api_chkotp[msg]";
return false;

//echo "OK###xxx";



mysql_close($conn_local);
?>