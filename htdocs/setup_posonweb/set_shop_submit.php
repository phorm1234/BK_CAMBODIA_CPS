<?php 
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$txt_brand = mysql_real_escape_string($_POST["txt_brand"]);
	$txt_shop_id = mysql_real_escape_string($_POST["txt_shop_id"]);
	$txt_stime = mysql_real_escape_string($_POST["txt_stime"]);
	$txt_etime = mysql_real_escape_string($_POST["txt_etime"]);	
	$txt_stime_w = mysql_real_escape_string($_POST["txt_stime_w"]);
	$txt_etime_w = mysql_real_escape_string($_POST["txt_etime_w"]);	
		
	$now_d = date("Y-m-d");
	
	$sql_chkdate = "SELECT * FROM `com_time_branch` WHERE `branch_id` = '$txt_shop_id'";
	$result_chkdate = mysql_query($sql_chkdate, $condb2);
	
	if(mysql_num_rows($result_chkdate) > 0)
	{		
		$sql_update = "UPDATE `com_time_branch` SET `normal_open_time` = '$txt_stime', `normal_close_time` = '$txt_etime', `special_open_time` = '$txt_stime_w', `special_close_time` = '$txt_etime_w', `start_date` = '$now_d' WHERE `branch_id` = '$txt_shop_id'";
		$succ = mysql_query($sql_update,$condb2);
	}
	else
	{
		$sql = "INSERT INTO `com_time_branch` (`corporation_id`, `company_id`, `branch_id`, `start_date`, `end_date`, `normal_open_time`, `normal_close_time`, `special_open_time`, `special_close_time`) VALUES ('$txt_brand', '$txt_brand', '$txt_shop_id', '$now_d', '2100-12-31', '$txt_stime', '$txt_etime', '$txt_stime_w', '$txt_etime_w')";
		$succ = mysql_query($sql,$condb2);
	}
	
	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'set_shop.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}
	
?>