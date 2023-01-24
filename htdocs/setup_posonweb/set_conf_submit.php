<?php
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$slt_code = mysql_real_escape_string($_POST["slt_code"]);
	$slt_default = mysql_real_escape_string($_POST["slt_default"]);
	$slt_condition = mysql_real_escape_string($_POST["slt_condition"]);
	$txt_sdate = mysql_real_escape_string($_POST["txt_sdate"]);	
	$txt_edate = mysql_real_escape_string($_POST["txt_edate"]);		
	$txt_stime = mysql_real_escape_string($_POST["txt_stime"]);	
	$txt_etime = mysql_real_escape_string($_POST["txt_etime"]);		
	
	$now_d = date("Y-m-d");
	$now_t = date("H:i:s");
	
	$start_date = explode('/', $txt_sdate);
	$start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];
	
	$end_date = explode('/', $txt_edate);
	$end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];
	
	$sql = "UPDATE `com_pos_config` SET `default_status` = '$slt_default',`condition_status` = '$slt_condition',`start_date` = '$start_date',`end_date` = '$end_date',`start_time` = '$txt_stime',`end_time` = '$txt_etime',`upd_date` = '$now_d',`upd_time` = '$now_t',`upd_user` = 'is-helpded' WHERE `code_type` = '$slt_code'";
	$succ = mysql_query($sql,$condb2);
		
	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'set_conf.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}
?>