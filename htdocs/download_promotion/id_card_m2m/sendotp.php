<?php
set_time_limit(0);
include("connect.php");

$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];
$fname=$_GET['fname'];
$lname=$_GET['lname'];
$hbd=$_GET['hbd'];



echo $path_api;


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




$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&promortion=OPMGMC300";	
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	



mysql_close($conn_local);
?>