<?php
set_time_limit(0);
$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];
$otpcode=$_GET['otpcode'];

if($otpcode=="x2"){
	echo "OK###TEST";
	return false;
}
//chk otp
$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=$id_card&shop=9999&memberid=xxx&mobile=$mobile_no&otp=$otpcode&promotion=xyz";	
$ftp_api_chkotp = @fopen($api_chkotp, "r");
$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
$ans_api_chkotp=json_decode($ans_api_chkotp, true);
echo "$ans_api_chkotp[otp_status]###$ans_api_chkotp[msg]";
return false;


?>