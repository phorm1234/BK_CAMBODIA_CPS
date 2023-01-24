<?php
set_time_limit(0);
include("connect.php");
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$find="select province_id,zip_province_nm_th as province_name  from view_province  group by province_id,zip_province_nm_th order by zip_province_nm_th";
$run=mysql_query($find,$conn_local);
$rows=mysql_num_rows($run);

echo "<select  name='send_province_id' id='send_province_id' onchange=\"showamphur();\" style=\"width:172px;font-size:26px;\" class='listbox1' >";
echo "<option value=\"0\">กรุณาเลือก</option>";
for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($run);
	$province_id=$data['province_id'];
	$province_name=$data['province_name'];
	
	echo "<option value=\"$province_id\" >$province_name</option>";
  
}
echo "</select>";
?>