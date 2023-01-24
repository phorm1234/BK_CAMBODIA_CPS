<?php
set_time_limit(0);
header("Content-type:text/html; charset=utf-8"); 
include("connect.php");

$id_card=$_GET['id_card'];
$name=$_GET['name'];
$surname=$_GET['surname'];
$hbd=$_GET['birthday'];

if($id_card==""){
	echo "stop##กรุณากดอ่านข้อมูลจากบัตรประชาชนก่อนค่ะ";
	return false;
}

$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");	
mysql_select_db($db_local);
	
if($id_card!=""){
	mysql_select_db("ssup");
	//chk id card
	$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' 	";
	$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
				$dataemp=mysql_fetch_array($run_chk_idcard);
				echo "stop##รหัสประชาชนนี้เป็นของพนักงาน SSUP ซึ่งทาง OP กำหนดไว้ว่า พนักงาน SSUP ไม่สามารถร่วมโปรโมชั่นนี้ได้";
				return false;
		}
	}
}

//chk diary
if($id_card!=""){
	mysql_select_db("pos_ssup");
	//chk id card
	$chk_idcard="SELECT * FROM trn_diary1 	where doc_date>='2015-06-01' and idcard='$id_card' and flag<>'C' and application_id in('OPID300','OPPLI300')	";
	$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
				$databill=mysql_fetch_array($run_chk_idcard);
				echo "stop##(Offline)รหัสประชาชนนี้ ได้สมัครสมาชิกแบบ ใช้บัตรประชาชนแทนบัตรสมาชิกไปแล้วที่สาขา $databill[branch_id] เมื่อ $databill[doc_date] ด้วยบิล $databill[doc_no]";
				return false;
		}
	}
}
mysql_close($conn_local);	

	
$conn_service=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);
		
$find="
select a.*  from 
member_register  as a inner join member_history as b
on a.customer_id=b.customer_id
where a.id_card='$id_card' and b.expire_date>=date(now()) and status_active='Y'
 ";
$run_find=mysql_query($find,$conn_service);
$rows=mysql_num_rows($run_find);
if($rows>0){
	$data=mysql_fetch_array($run_find);
	$msg="รหัสบัตรประชาชน : $data[id_card]  นี้ เป็นสมาชิกของเราอยู่แล้วค่ะ";
	echo "stop##$msg";
	return false;
}
//chk diary
if($id_card!=""){
	
	//chk id card
	$chk_idcard="SELECT * FROM trn_diary1 	where doc_date>='2015-06-01' and idcard='$id_card' and flag<>'C'  and application_id in('OPID300','OPPLI300')	";
	$run_chk_idcard=mysql_query($chk_idcard,$conn_service);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
				$databill=mysql_fetch_array($run_chk_idcard);
				echo "stop##(Online)รหัสประชาชนนี้ ได้สมัครสมาชิกแบบ ใช้บัตรประชาชนแทนบัตรสมาชิกไปแล้วที่สาขา $databill[branch_id] เมื่อ $databill[doc_date] ด้วยบิล $databill[doc_no]";
				return false;
		}
	}
}



$find="
select a.*  from 
member_register  as a inner join member_history as b
on a.customer_id=b.customer_id
where a.name='$name' and a.surname='$surname' and a.birthday='$hbd' and b.expire_date>=date(now()) and status_active='Y'
";
//echo $find;
$run_find=mysql_query($find,$conn_service);


$rows=mysql_num_rows($run_find);
if($rows>0){
	$data=mysql_fetch_array($run_find);
	$msg="คุณ $data[name] $data[surname] เป็นสมาชิกของเราอยู่แล้วค่ะ";
	echo "stop##$msg";
	return false;
}

echo "ok##";

mysql_close($conn_service);
?>