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
$var_tambon=$_GET['var_tambon'];
$find="select zip_tambon_id as tambon_id,zip_tambon_nm_th as tambon_name  from view_province  where province_id='$send_province_id' and zip_amphur_id='$send_amphur_id' group by zip_tambon_id,zip_tambon_nm_th order by zip_tambon_nm_th";
$run=mysql_query($find,$conn_local);
$rows=mysql_num_rows($run);
echo "<select  name='send_tambon_id' id='send_tambon_id' onchange=\"showpostcode();\" onclick=\"showpostcode();\" class='listbox1' style=\"width:172px;font-size:26px;\">";
for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($run);
	$tambon_id=$data['tambon_id'];
	$tambon_name=$data['tambon_name'];
	
	if($var_tambon==$tambon_id){
		echo "<option value=\"$tambon_id\" selected>$tambon_name</option>";
	}else{
		echo "<option value=\"$tambon_id\" >$tambon_name</option>";
	}
  
}
echo "</select>";
?>