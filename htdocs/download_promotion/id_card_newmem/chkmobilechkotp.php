<?php
set_time_limit(0);
header("Content-type:text/html; charset=utf-8"); 
include("connect.php");


$mobile_no=$_GET['mobile_no'];
$otp_code=$_GET['otp_code'];
$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=3333333333333&shop=3333&memberid=3333333333333&mobile=$mobile_no&otp=$otp_code&promotion=chkmobile";	
//echo $api_chkotp;
$ftp_api_chkotp = @fopen($api_chkotp, "r");
$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
$ans_api_chkotp=json_decode($ans_api_chkotp, true);
if($ans_api_chkotp['otp_status']=="OK"){
	echo "$ans_api_chkotp[otp_status]###OTP CODE ถูกต้อง";
}else{
	echo "$ans_api_chkotp[otp_status]###OTP CODE ไม่ถูกต้อง";
}
?>