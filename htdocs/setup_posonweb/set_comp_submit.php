<?php
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$txt_shop_id = mysql_real_escape_string($_POST["txt_shop_id"]);
	$txt_ip = mysql_real_escape_string($_POST["txt_ip"]);
	$txt_no = mysql_real_escape_string($_POST["txt_no"]);
	$txt_pos_id = mysql_real_escape_string($_POST["txt_pos_id"]);	
	$txt_thermal = mysql_real_escape_string($_POST["txt_thermal"]);
	$txt_cash = mysql_real_escape_string($_POST["txt_cash"]);		
	
	$now_d = date("Y-m-d");
	$now_t = date("H:i:s");
	
	$sql_chk_no = "SELECT * FROM `com_branch_computer` WHERE computer_no = '$txt_no' AND com_ip = '$txt_ip' AND branch_id = '$txt_shop_id'";
	$result_chk_no = mysql_query($sql_chk_no, $condb2);
	
	if(mysql_num_rows($result_chk_no) > 0)
	{	
		$txt_shop_id = mysql_real_escape_string($_POST["hdn_shop_id"]);
		$txt_ip = mysql_real_escape_string($_POST["hdn_ip"]);
		$txt_no = mysql_real_escape_string($_POST["hdn_no"]);
		$sql = "UPDATE `com_branch_computer` SET `pos_id` = '$txt_pos_id', `thermal_printer` = '$txt_thermal', `cashdrawer` = '$txt_cash', `upd_date` = '$now_d', `upd_time` = '$now_t', `upd_user` = 'is-helpdesk' WHERE `computer_no` = '$txt_no' AND com_ip = '$txt_ip'";
		$succ = mysql_query($sql,$condb2);
		
		//-------- update pos_id every shop
		/*
		$sql = "UPDATE `com_branch_computer` SET `pos_id` = '$txt_pos_id', `upd_date` = '$now_d', `upd_time` = '$now_t', `upd_user` = 'is-helpdesk' WHERE `branch_id` = '$txt_shop_id'";
		$succ = mysql_query($sql,$condb2);
		*/
	
		if($txt_no == "1")// update local
		{
			$sql = "UPDATE `com_branch_computer` SET `pos_id` = '$txt_pos_id', `thermal_printer` = '$txt_thermal', `cashdrawer` = '$txt_cash', `upd_date` = '$now_d', `upd_time` = '$now_t', `upd_user` = 'is-helpdesk' WHERE `computer_no` = '1' AND com_ip = '127.0.0.1'";
			$succ = mysql_query($sql,$condb2);
		}
	}
	else
	{
		$sql_del = "DELETE FROM `com_branch_computer` WHERE computer_no = '$txt_no'";
		mysql_query($sql_del, $condb2);
		
		$sql = "SELECT * FROM `com_branch_detail` WHERE `branch_id` = '$txt_shop_id'";
		$result = mysql_query($sql,$condb2);
		$row = mysql_fetch_array($result);
		
		$corp = $row["corporation_id"];
		$comp = $row["company_id"];
		
		if($txt_no == "1")// local
		{
			$sql_ins = "INSERT INTO `com_branch_computer` (`corporation_id`, `company_id`, `branch_id`, `com_ip`, `computer_no`, `pos_id`, `thermal_printer`, `cashdrawer`, `network`, `lock_status`, `online_status`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corp', '$comp', '$txt_shop_id', '127.0.0.1', '1', '$txt_pos_id', '$txt_thermal', '$txt_cash', 'N', 'N', '1', '$now_d', '$now_t', 'is-helpdesk', '$now_d', '$now_t', 'is-helpdesk')";
			$succ = mysql_query($sql_ins,$condb2);
		}
		$sql_ins = "INSERT INTO `com_branch_computer` (`corporation_id`, `company_id`, `branch_id`, `com_ip`, `computer_no`, `pos_id`, `thermal_printer`, `cashdrawer`, `network`, `lock_status`, `online_status`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corp', '$comp', '$txt_shop_id', '$txt_ip', '$txt_no', '$txt_pos_id', '$txt_thermal', '$txt_cash', 'Y', 'N', '1', '$now_d', '$now_t', 'is-helpdesk', '$now_d', '$now_t', 'is-helpdesk')";
		$succ = mysql_query($sql_ins,$condb2);
	}

	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'set_comp.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}
?>