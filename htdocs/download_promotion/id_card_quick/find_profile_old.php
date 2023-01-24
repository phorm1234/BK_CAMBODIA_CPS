<?php
set_time_limit(0);
include("connect.php");
$member_no=$_GET['member_no'];
$conn_service=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);
$send_province_id=$_GET['send_province_id'];
$find="select a.* from member_register as a inner join member_history as b
on a.customer_id=b.customer_id where b.member_no='$member_no' limit 1 ";
$run=mysql_query($find,$conn_service);

$data=mysql_fetch_array($run);
$customer_id=$data['customer_id'];
$id_card=$data['id_card'];
$name=$data['name'];
$surname=$data['surname'];
$birthday=$data['birthday'];
$mobile_no=$data['mobile_no'];

echo "$customer_id@@$id_card@@$name@@$surname@@$birthday@@$mobile_no@@$member_no@@$data[send_address]@@$data[send_mu]@@$data[send_home_name]@@$data[send_soi]@@$data[send_road]@@$data[send_tambon_id]@@$data[send_tambon_name]@@$data[send_amphur_id]@@$data[send_amphur_name]@@$data[send_province_id]@@$data[send_province_name]@@$data[send_postcode]@@$data[send_fax]@@$data[email_]";
?>