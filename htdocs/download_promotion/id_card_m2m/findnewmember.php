<?php
set_time_limit(0);
include("connect.php");

						
$memold_id_card=$_GET['memold_id_card'];
$memold_mobile_no=$_GET['memold_mobile_no'];
$memold_customer_id=$_GET['memold_customer_id'];
$memold_member_no=$_GET['memold_member_no'];
$promo_code_play=$_GET['promo_code_play'];

						
$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];
$fname=$_GET['fname'];
$lname=$_GET['lname'];
$hbd=$_GET['hbd'];
$hbd_arr=explode("/",$hbd);
$hbd_year=$hbd_arr[2];

$val_otp=$_GET['val_otp'];


$age=(date("Y")+543)-(int)$hbd_year;
if($age<15){
	echo "No###ขออภัยค่ะ เนื่องจากสมาชิกมีอายุ $age ปี โปรโมชั่นนี้ขอสงวนสิทธิ์สำหรับลูกค้าที่มาอายุ 15 ปีขึ้นไปค่ะ";
	return false;
}

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
mysql_close($conn_local);

$server_service="10.100.53.2";
$user_service="master";
$pass_service="master";
$db_service="service_pos_op";


$conn_service=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);


if($id_card!=""){
	mysql_select_db("ssup");
	//chk id card
	$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' ";
	$run_chk_idcard=mysql_query($chk_idcard,$conn_service);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
			echo "No###ขออภัยค่ะ เนื่องจากหมายบัตรประชาชน $id_card นี้เป็นของพนักงาน SSUP ซึ่งโปรโมชั่นนี้ขอสงวนสิทธิ์สำหรับลูกค้าเท่านั้นค่ะ";
			return false;
		}
	}
}

mysql_select_db($db_service);			
								
$find="select a.* from member_register as a inner join member_history as b
		on a.customer_id=b.customer_id
		where
		a.id_card='$id_card'  and b.expire_date>='2017-02-01' and b.status_active='Y'  limit 1";
$run_find=mysql_query($find,$conn_service);
$rows=mysql_num_rows($run_find);
if($rows>0){
	echo "No###ขออภัยค่ะ เนื่องจากหมายบัตรประชาชน $id_card นี้เป็นสมาชิกปัจจุบัน ขอสงวนสิทธิ์รายการชวนเพื่อนสมัครสมาชิกไว้สำหรับเพื่อนที่ยังไม่เคยเป็น สมาชิกเท่านั้นน่ะค่ะ";
	return false;
}
$find="select a.* from member_register as a inner join member_history as b
		on a.customer_id=b.customer_id
		where
		a.mobile_no='$mobile_no'  and b.expire_date>='2017-02-01' and b.status_active='Y'  limit 1";
$run_find=mysql_query($find,$conn_service);
$rows=mysql_num_rows($run_find);
if($rows>0){
	echo "No###ขออภัยค่ะ เนื่องจากหมายเลขมือถือ $mobile_no นี้เป็นสมาชิกปัจจุบัน ขอสงวนสิทธิ์รายการชวนเพื่อนสมัครสมาชิกไว้สำหรับเพื่อนที่ยังไม่เคยเป็น สมาชิกเท่านั้นน่ะค่ะ";
	return false;
}

$find="select a.* from member_register as a inner join member_history as b
		on a.customer_id=b.customer_id
		where
		a.name='$name' and a.surname='$surname' and a.birthday='$hbd'  and b.expire_date>='2017-02-01' and b.status_active='Y'  limit 1";
$run_find=mysql_query($find,$conn_service);
$rows=mysql_num_rows($run_find);
if($rows>0){
	echo "No###ขออภัยค่ะ เนื่องจากชื่อ-นามสกุลและวันเดือนปีเกิด นี้เป็นสมาชิกปัจจุบัน ขอสงวนสิทธิ์รายการชวนเพื่อนสมัครสมาชิกไว้สำหรับเพื่อนที่ยังไม่เคยเป็น สมาชิกเท่านั้นน่ะค่ะ";
	return false;
}


echo "OK###ผ่าน";



mysql_close($conn_local);
?>