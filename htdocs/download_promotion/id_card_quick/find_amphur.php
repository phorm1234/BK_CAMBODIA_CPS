<?php
set_time_limit(0);
include("connect.php");
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$send_province_id=$_GET['send_province_id'];
$var_amphur=$_GET['var_amphur'];
$var_tambon=$_GET['var_tambon'];

$find="select zip_amphur_id as amphur_id,zip_amphur_nm_th as amphur_name  from view_province  where province_id='$send_province_id' group by zip_amphur_id,zip_amphur_nm_th order by zip_amphur_nm_th";

$run=mysql_query($find,$conn_local);
$rows=mysql_num_rows($run);
echo "<select  name='send_amphur_id' id='send_amphur_id' onchange=\"showtambon();\" style=\"width:172px;font-size:26px;\" class='listbox1'>";
for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($run);
	$amphur_id=$data['amphur_id'];
	$amphur_name=$data['amphur_name'];
	
	if($var_select==$amphur_id){
		echo "<option value=\"$amphur_id\" selected>$amphur_name-$var_select</option>";
	}else{
		echo "<option value=\"$amphur_id\" >$amphur_name</option>";
	}
  
}
echo "</select>";
?>