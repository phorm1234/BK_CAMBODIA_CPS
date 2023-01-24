<?php
set_time_limit(0);

$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];

$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=9999&memberid=xxx&mobile=$mobile_no&promortion=xyz";	
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	


$arrotpcode=json_decode($arrotpcode, true);
echo "$arrotpcode[sms_status]###$arrotpcode[msg]";
?>