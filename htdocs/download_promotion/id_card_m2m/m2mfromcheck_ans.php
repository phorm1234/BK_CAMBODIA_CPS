<?php
set_time_limit(0);
include("connect.php");
$ip=$_SERVER['REMOTE_ADDR'];
$coupon_code=$_GET['coupon_code'];
$id_card=$_GET['id_card'];
$friend_mobile_no=$_GET['friend_mobile_no'];

$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");	
mysql_select_db($db_local);	


if($id_card!=""){
	mysql_select_db("ssup");
	//chk id card
	$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' ";
	$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
			echo "No###ขออภัยค่ะ เนื่องจากหมายบัตรประชาชน $id_card นี้เป็นของพนักงาน SSUP ซึ่งโปรโมชั่นนี้ขอสงวนสิทธิ์สำหรับลูกค้าเท่านั้นค่ะ######";
			return false;
		}
	}
}

mysql_select_db($db_local);	

if($id_card!=""){
	$find_register="select * from trn_diary1 where doc_date>='2017-04-01' and application_id like 'OPMGM%' and flag<>'C' and idcard = '$id_card' ";
	//echo $find_register;
	$run_play_register=mysql_query($find_register,$conn_local);
	$rows_play_register=mysql_num_rows($run_play_register);
	if($rows_play_register>0){
		$dataplay=mysql_fetch_array($run_play_register);
		echo "no###Local:ลูกค้าท่านนี้ได้สมัครสมาชิกแบบ Member Get Member ไปแล้วเมื่อวันที่ $dataplay[doc_date] ด้วยบิลเลขที่ $dataplay[doc_no] ที่สาขา $dataplay[branch_id]###ใช้สิทธิ์ไปแล้ว###@@@@@###10";
		return false;
		
	}
}



//echo "http://mshop.ssup.co.th/shop_op/opmgm2016.php?idcard=$id_card&coupon_code=$coupon_code";

$fp = @fopen("http://mshop.ssup.co.th/shop_op/opmgm2016.php?idcard=$id_card&coupon_code=$coupon_code", "r");
$textapi=@fgetss($fp, 4096);
$ans_textapi=json_decode($textapi, true);	
//echo "x=" . $ans_textapi['status'];
//print_r($ans_textapi);
if($ans_textapi['status']=="NO"){
	echo "no###$ans_textapi[status_msg]######";
	return false;
}



$fp = @fopen("http://$server_app/ims/joke/app_service_op/api_member/api_m2mfromcheck_ans.php?coupon_code=$coupon_code&id_card=$id_card&memold_id_card=$ans_textapi[memold_id_card]&memold_mobile_no=$ans_textapi[memold_mobile_no]&memold_customer_id=$ans_textapi[memold_customer_id]&memold_member_no=$ans_textapi[memold_member_no]&friend_id_card=$ans_textapi[friend_id_card]&friend_mobile_no=$ans_textapi[friend_mobile_no]", "r");
$text_point=@fgetss($fp);
echo $text_point;

?>