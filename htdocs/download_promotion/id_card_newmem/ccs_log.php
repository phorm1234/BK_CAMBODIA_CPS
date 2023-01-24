<?php
set_time_limit(0);
header("Content-type:text/html; charset=utf-8"); 
include("connect.php");

$id_card=$_GET['id_card'];
$member_no=$_GET['member_no'];
$status_readcard=$_GET['status_readcard'];
$otpcode=$_GET['otpcode'];


if($id_card==""){
	echo "stop##ไม่มีค่ารหัสบัตร ปชช. ส่งเข้ามา";
	return false;
}


$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$find_timebill="select * from com_doc_date";
$run_find_timebill=mysql_query($find_timebill,$conn_local);
$datatimebill=mysql_fetch_array($run_find_timebill);
$doc_date=$datatimebill['doc_date'];

/*$clear="delete from  memccs_log where ip='$ip' ";
$runclear=mysql_query($clear,$conn_local);
if($runclear){*/
	$add="insert into memccs_log(ip,id_card,member_no,status_readcard,otpcode,reg_date,reg_time,time_up) values('$ip','$id_card','$member_no','$status_readcard','$otpcode','$doc_date',time(now()),now())";
	$run=mysql_query($add,$conn_local);
	if($run){
		echo "Y";
	}else{
		echo "N";
	}
/*}else{
	echo "ClearN";
}*/



mysql_close($conn_local);
?>