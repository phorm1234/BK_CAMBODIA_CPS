<?php
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$txt_brand = mysql_real_escape_string($_POST["txt_brand"]);
	$txt_shop_id = mysql_real_escape_string($_POST["txt_shop_id"]);
	$txt_emp_id = mysql_real_escape_string($_POST["txt_emp_id"]);
	$txt_emp_pass = mysql_real_escape_string($_POST["txt_emp_pass"]);	
	$txt_sdate = mysql_real_escape_string($_POST["txt_sdate"]);
	$txt_edate = mysql_real_escape_string($_POST["txt_edate"]);	
	
	$txt_sdate = explode('/', $txt_sdate);
	$txt_sdate = $txt_sdate[2].'-'.$txt_sdate[1].'-'.$txt_sdate[0];
	
	$txt_edate = explode('/', $txt_edate);
	$txt_edate = $txt_edate[2].'-'.$txt_edate[1].'-'.$txt_edate[0];
	
	$now_d = date("Y-m-d");
	$now_t = date("H:i:s");
	
	$sql_chkdate = "SELECT * FROM `conf_employee` WHERE `employee_id` = '000009'";
	$result_chkdate = mysql_query($sql_chkdate, $condb2);
	
	if(mysql_num_rows($result_chkdate) > 0)
	{		
		$sql_update = "UPDATE `conf_employee` SET `corporation_id` = '$txt_brand', `company_id` = '$txt_brand', `branch_id` = '$txt_shop_id', `start_date` = '$txt_sdate', `end_date` = '$txt_edate', `upd_date` = '$now_d', `upd_time` = '$now_t', `upd_user` = 'is-helpdesk', `group_id` = 'OpShopMng', `user_level` = '4' , `user_id` = '000009', `password_id` = '000009',`name` = 'Demo',`surname` = 'Demo', `position` = 'BE' WHERE `employee_id` = '000009'";
		$succ = mysql_query($sql_update,$condb2);
	}
	else
	{
		$sql = "INSERT INTO `conf_employee` (`corporation_id`, `company_id`, `branch_id`, `employee_id`, `user_id`, `password_id`, `group_id`, `name`, `surname`, `position`, `start_date`, `end_date`, `cancel`, `remark`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user` , `user_level`) VALUES
('$txt_brand', '$txt_brand', '$txt_shop_id', '000009', '000009', '000009', 'OpShopMng', 'Demo', 'Demo', 'BE', '$txt_sdate', '$txt_edate', '1', '', '$now_d', '$now_t', 'is-helpdesk', '$now_d', '$now_t', 'is-helpdesk', '4')";
		$succ = mysql_query($sql,$condb2);
	}
	
	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'set_user.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}
?>