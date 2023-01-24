<?php
set_time_limit(0);
include("connect.php");
$ip=$_SERVER['REMOTE_ADDR'];
$field_search=$_GET['field_search'];
$var_search=$_GET['var_search'];
$table_search=$_GET['table_search'];

$conn_local=mysql_connect($local_server,$local_user,$local_pass);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");	
mysql_select_db($local_db);


function set_formatdate($var){
	$arr=explode("-",$var);
	return "$arr[2]/$arr[1]/$arr[0]";
}

$find="select * from $table_search where $field_search like '%$var_search%'  and application_id not in('OPPA300','OPPB300','OPPC300','OPPD300','OPPF300') and application_id<>'' ";
//echo $find;
$run=mysql_query($find,$conn_local);
$rows=mysql_num_rows($run);
//print_r($listnewmember);
echo "<center><br>$show_cr</center>";
echo "<table id='newspaper-prob' cellspacing='0' cellpadding='0'>";
echo "<tbody>";
echo "<tr >";
		echo "<th  align='center' >ลำดับ</th>";
		echo "<th  align='center' >วันที่สมัคร</th>";
		echo "<th  align='center' >เลขที่เอกสาร</th>";
		echo "<th align='center'>รหัสสมาชิก</th>";
		echo "<th align='center'>ปชช.</th>";
		echo "<th align='center'>มือถือ</th>";
		echo "<th align='center'>สิทธิ์</th>";
		echo "<th align='center'>ชุดสมัคร</th>";
		echo "<th align='center'>สาขา</th>";
		echo "<th  align='center' >ผลการบันทึก</th>";
		echo "<th  align='center' >สถานะการส่งเข้าส่วนกลาง</th>";
echo "</tr>";
$i=1;
for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($run);
	$doc_no=$data['doc_no'];
	$doc_date=set_formatdate($data['doc_date']);
	$doc_time=$data['doc_time'];
	$member_finish=$data['member_finish'];

	$sendtoserver_status=$data['sendtoserver_status'];
	$sendtoserver_date=$data['sendtoserver_date'];
	$sendtoserver_date=set_formatdate($sendtoserver_date);
	$sendtoserver_time=$data['sendtoserver_time'];
	if($sendtoserver_status=="Y"){
		$msg_send="ส่งแล้วเมื่อ $sendtoserver_date $sendtoserver_time"; 
	}else{
		$msg_send=""; 
	}
	
	if($data[application_id]=="OPID300" || $data[application_id]=="OPPLI300"){
		$show_img="<br><img src='../../../download_promotion/img/id_card.gif' width='40' hieght='40'>";
	}else{
		$show_img="";
	}
	
	$findchk="select * from member_history where member_no='$data[member_id]' ";
	$conn_service=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");	
	mysql_select_db($db_service);
	$run_findchk=mysql_query($findchk,$conn_service);
	$rows_findchk=mysql_num_rows($run_findchk);
	if($rows_findchk>0){
		$show_add="<span style='color:#1b7412;'>บันทึกแล้ว</span>";
	}else{
		$show_add="<input  type='button' value='รอการบันทึก' onclick=\"registerfromafter('$doc_no');\";>";
	}

		echo "<tr>";
				echo "<td  align='center' >$i</th>";
				echo "<td  align='center' >$doc_date $doc_time</th>";
				echo "<td  align='center' >$data[doc_no]</th>";
				echo "<td  align='center' >$data[member_id]</th>";
				echo "<td  align='center' >$data[idcard]</th>";
				echo "<td  align='center' >$data[mobile_no]</th>";
				echo "<td  align='center' >$data[special_day]</th>";
				echo "<td  align='center' >$data[application_id] $show_img</th>";
				echo "<td  align='center' >$data[branch_id]</th>";
				echo "<td  align='center' >$show_add</th>";
				echo "<td  align='center' >$msg_send</th>";
		echo "</tr>";
	
	$i++;
}
	echo "</tbody>";
	echo "</table>";
?>