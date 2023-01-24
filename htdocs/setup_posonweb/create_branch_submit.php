<?php 
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$txt_branch_id = mysql_real_escape_string($_POST["txt_branch_id"]);
	$txt_branch_name = mysql_real_escape_string($_POST["txt_branch_name"]);
	$slt_branch_tp = mysql_real_escape_string($_POST["slt_branch_tp"]);	
	$txt_phone = mysql_real_escape_string($_POST["txt_phone"]);
	$txt_mobile = mysql_real_escape_string($_POST["txt_mobile"]);	
	$txt_address = mysql_real_escape_string($_POST["txt_address"]);
	$txt_road = mysql_real_escape_string($_POST["txt_road"]);	
	
	$now_d = date("Y-m-d");
	$now_t = date("H:i:s");
	$sql = "SELECT * FROM `com_branch_detail` WHERE branch_id = '$txt_branch_id'";
	$result_chkdate = mysql_query($sql, $condb2);
	
	if(mysql_num_rows($result_chkdate) > 0 ) // update record
	{	
		$sql_del = "DELETE FROM `com_branch` WHERE `branch_id` NOT LIKE '$txt_branch_id'";
		$succ = mysql_query($sql_del,$condb2);
			
		$sql_update = "UPDATE `com_branch_detail` SET `branch_name` = '$txt_branch_name', `branch_tp` = '$slt_branch_tp', `branch_phone` = '$txt_phone', `branch_mobile` = '$txt_mobile', `address` = '$txt_address', `road` = '$txt_road', `upd_date` = '$now_d', `upd_time` = '$now_t', `upd_user` = 'is-helpdesk' WHERE `branch_id` = '$txt_branch_id'";
		$succ = mysql_query($sql_update,$condb2);
	}
	else // clear and add new record
	{
		$slt_brand = mysql_real_escape_string($_POST["slt_brand"]);
		$sql_del = "TRUNCATE TABLE `com_branch_detail`";
		$succ = mysql_query($sql_del,$condb2);
		
		$sql_ins = "INSERT INTO `com_branch_detail` (`corporation_id`, `company_id`, `branch_id`,`branch_no` , `branch_name`, `branch_tp`, `branch_phone`, `branch_mobile`, `address`, `road`, `start_date`, `end_date`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$slt_brand', '$slt_brand', '$txt_branch_id', '$txt_branch_id', '$txt_branch_name', '$slt_branch_tp', '$txt_phone', '$txt_mobile', '$txt_address', '$txt_road', '$now_d', '0000-00-00', '$now_d', '$now_t', 'is-helpdesk', '$now_d', '$now_t', 'is-helpdesk')";
		$succ = mysql_query($sql_ins,$condb2);
	}
	
	$sql_get = "SELECT * FROM `com_branch_detail`";
	$result_get = mysql_query($sql_get,$condb2);
	$row_get = mysql_fetch_array($result_get);
	
	$slt_brand = $row_get["company_id"];
		
		$sql_del = "DELETE FROM `com_branch` WHERE `branch_id` NOT LIKE '0001'";
		$succ = mysql_query($sql_del,$condb2);
		
		$sql_head = "INSERT INTO `com_branch` (`corporation_id`, `company_id`, `branch_id`, `active`) VALUES ('$slt_brand', '$slt_brand', '$txt_branch_id', '1')";
		$succ = mysql_query($sql_head,$condb2);
		
		$sql = "SELECT * FROM `com_system` WHERE `branch_id` = '$txt_branch_id'";
		$result_chksys = mysql_query($sql, $condb2);
		if(mysql_num_rows($result_chksys) > 0)
		{
			// update
			$sql = "UPDATE `com_system` SET `thermal_printer` = 'Y',`network` = 'N',`computer_no` = '1' WHERE `branch_id` = '$txt_branch_id'";
			$succ = mysql_query($sql, $condb2);
		}
		else
		{
			//ins
			$sql_del = "TRUNCATE TABLE `com_system`";
			$succ = mysql_query($sql_del,$condb2);
			$sql = "INSERT INTO `com_system` (`branch_id`, `thermal_printer`, `network`, `computer_no`) VALUES ('$txt_branch_id', 'Y', 'N', '1')";
			$succ = mysql_query($sql, $condb2);
		}
		
		//------------- conf doc no
		$sql_get = "SELECT * FROM `conf_doc_no`";
		$result = mysql_query($sql_get, $condb2);
		if(mysql_num_rows($result) > 0 ) // update record
		{
			$sql_del = "TRUNCATE TABLE `conf_doc_no`";
			$succ = mysql_query($sql_del, $condb2);
		}
		
		$prefix = $slt_brand."".$txt_branch_id;
		$sql = "INSERT INTO `conf_doc_no` (`corporation_id`, `company_id`, `doc_prefix1`, `def_value`, `doc_prefix2`, `run_no`, `start_date`, `end_date`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`) VALUES ('$slt_brand', '$slt_brand', '$prefix', '01', '00000000', '8', '$now_d', '2100-12-31', 'is-helpdesk', '$now_d', '$now_t', 'is-helpdesk', '$now_d', '$now_t')";
		$succ = mysql_query($sql,$condb2);
		
		//-------------- com_customer_id
		$sql_get = "SELECT * FROM `com_customer_id`";
		$result = mysql_query($sql_get, $condb2);
		if(mysql_num_rows($result) > 0 ) // update record
		{
			$sql_del = "TRUNCATE TABLE `com_customer_id`";
			$succ = mysql_query($sql_del, $condb2);
		}
		
		$sql_ins = "INSERT INTO `com_customer_id` (`corporation_id`, `company_id`, `branch_id`, `customer_id`, `remark`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$slt_brand', '$slt_brand', '$txt_branch_id', '1', 'id สมาชิกใหม่', '$now_d', '$now_t', 'is-helpdesk', '$now_d', '$now_t', 'is-helpdesk')";
		$succ = mysql_query($sql_ins,$condb2);
		
		
		//-------------------- com_doc_no
		$sql = "UPDATE `com_doc_no` SET `corporation_id` = '$slt_brand', `company_id` = '$slt_brand', `branch_id` = '$txt_branch_id',`branch_no` = '$txt_branch_id',`doc_no` = '1',`upd_date` = '$now_d',`upd_time` = '$now_t', `upd_user` = 'is-helpdesk'";
		$succ = mysql_query($sql,$condb2);
		
		$sql = "UPDATE `com_pos_config` SET `branch_id` = '$txt_branch_id',`branch_no` = '$txt_branch_id',`upd_date` = '$now_d',`upd_time` = '$now_t', `upd_user` = 'is-helpdesk'";
		$succ = mysql_query($sql,$condb2);
		
		$sql = "UPDATE `conf_employee` SET `branch_id` = '$txt_branch_id'";
		$succ = mysql_query($sql,$condb2);
		
		$sql_get = "SELECT * FROM `com_branch_os`";
		$result = mysql_query($sql_get, $condb3);
		if(mysql_num_rows($result) > 0 ) // update record
		{
			$sql_del = "TRUNCATE TABLE `com_branch_os`";
			$succ = mysql_query($sql_del, $condb3);
		}
		
		$sql_ins = "INSERT INTO `com_branch_os` (`corporation_id`, `company_id`, `branch_id`, `computer_no`) VALUES ('$slt_brand', '$slt_brand', '$txt_branch_id', '1')";
		$succ = mysql_query($sql_ins,$condb3);
	
	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'create_branch.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}
?>