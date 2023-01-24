<?php
set_time_limit(0);
include("connect.php");
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$send_province_id=$_GET['send_province_id'];
$send_amphur_id=$_GET['send_amphur_id'];
$send_tambon_id=$_GET['send_tambon_id'];

$find="select zipcode  from view_province  where province_id='$send_province_id' and zip_amphur_id='$send_amphur_id' and zip_tambon_id='$send_tambon_id'";

$run=mysql_query($find,$conn_local);
$rows=mysql_num_rows($run);
if($rows>0){
	$data=mysql_fetch_array($run);
	$zipcode=$data['zipcode'];
}else{
	$zipcode=0;
}
echo $zipcode;
?>