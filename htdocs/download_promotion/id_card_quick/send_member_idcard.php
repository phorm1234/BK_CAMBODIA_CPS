<?php
set_time_limit(0);
include("connect.php");


							
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);

$find="select * from member_idcard order by id desc limit 10 ";
echo $find;
$run=mysql_query($find,$conn_local) or die(mysql_error());
$rows=mysql_num_rows($run);
echo "Rows change profile:$rows<br>";
for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($run);
	
	$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("Offline");
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);
	
	$insert="insert into member_idcard(`shop`, `member_no`, `id_card`, `name`, `surname`, `birthday`, `address`, `mu`, `tambon_name`, `amphur_name`, `province_name`, `mr`, `sex`, `mr_en`, `fname_en`, `lname_en`, `card_at`, `start_date`, `end_date`, `time_up`) values('$data[shop]','$data[member_no]','$data[id_card]','$data[name]','$data[surname]','$data[birthday]','$data[address]','$data[mu]','$data[tambon_name]','$data[amphur_name]','$data[province_name]','$data[mr]','$data[sex]','$data[mr_en]','$data[fname_en]','$data[lname_en]','$data[card_at]','$data[start_date]','$data[end_date]','$data[time_up]')
	";
	$run_insert=mysql_query($insert,$conn_service);
	mysql_close($conn_service);

}




?>