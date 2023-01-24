<?php
set_time_limit(0);
header("Content-type:text/html; charset=utf-8"); 
include("connect.php");


$mobile_no=$_GET['mobile_no'];
$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=3333333333333&shop=3333&memberid=3333333333333&mobile=$mobile_no&promortion=chkmobile";	
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	
$arrotpcode=json_decode($arrotpcode, true);

echo "$arrotpcode[sms_status]###$arrotpcode[msg]";
?>