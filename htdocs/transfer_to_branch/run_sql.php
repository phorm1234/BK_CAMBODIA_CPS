<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_errors','On');
$sql=$_REQUEST['sql'];
$sql=urldecode($_REQUEST['sql']);
if($sql!=""){
	$conn=mysql_connect("localhost","pos-ssup",'P0z-$$up');
	mysql_select_db('pos_ssup',$conn);
	//mysql_query("SET NAMES utf8");
	mysql_query("SET character_set_results=utf8");//ตั้งค่าการดึงข้อมูลออกมาให้เป็น utf8
	mysql_query("SET character_set_client=utf8");//ตั้งค่าการส่งข้อมุลลงฐานข้อมูลออกมาให้เป็น utf8
	mysql_query("SET character_set_connection=utf8");//ตั้งค่าการติดต่อฐานข้อมูลให้เป็น utf8
	$run=mysql_query($sql);
	if($run){
		echo"OK";
	}else{
		echo"Error";
	}
	mysql_close($conn);
}
?>
