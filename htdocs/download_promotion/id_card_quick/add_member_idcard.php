<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
set_time_limit(0);
header("Content-type:text/html; charset=utf-8"); 
include("connect.php");

		
		
$id_card=$_GET['id_card'];
if($id_card==""){
	echo "stop##กรุณากดอ่านข้อมูลจากบัตรประชาชนก่อนค่ะ";
	return false;
}


		



$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");	
mysql_select_db($db_local);
$find_shop="select * from com_branch where company_id='OP' and active='1' ";
$run_find_shop=mysql_query($find_shop,$conn_local);
$datashop=mysql_fetch_array($run_find_shop);
if($id_card!=""){
	//add idcard
	$add_idcard="
		insert into member_idcard(`shop`, `member_no`, `id_card`, `name`, `surname`, `birthday`, `address`, `mu`, `tambon_name`, `amphur_name`, `province_name`, `mr`, `sex`, `mr_en`, `fname_en`, `lname_en`, `card_at`, `start_date`, `end_date`, `time_up`) values('$datashop[branch_id]','$_GET[member_no]','$_GET[id_card]','$_GET[fname]','$_GET[lname]','$_GET[birthday]','$_GET[address]','$_GET[mu]','$_GET[tambon_name]','$_GET[amphur_name]','$_GET[province_name]','$_GET[mr]','$_GET[sex]','$_GET[mr_en]','$_GET[fname_en]', '$_GET[lname_en]','$_GET[card_at]','$_GET[start_date_format]','$_GET[end_date_format]',now())
	";
	//echo $add_idcard;
	mysql_query($add_idcard,$conn_local);
}
					

mysql_close($conn_local);
?>