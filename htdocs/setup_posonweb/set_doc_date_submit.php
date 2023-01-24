<?php
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$txt_date = mysql_real_escape_string($_POST["txt_date"]);	

	$arr = explode('/', $txt_date);
	$newDate = $arr[2].'-'.$arr[1].'-'.$arr[0];
	
	$ddate = date("Y-m-d");
	$dtime = date("H:i:s");

	$sql_chkdate = "SELECT * FROM `com_doc_date`";
	$result_chkdate = mysql_query($sql_chkdate, $condb2);
	
	$sql_get = "SELECT * FROM `com_branch_detail`";
	$result_get = mysql_query($sql_get,$condb2);
	$row_get = mysql_fetch_array($result_get);
	
	if(mysql_num_rows($result_chkdate) > 0)
	{			
		$sql_update = "UPDATE `com_doc_date` SET `doc_date` = '$newDate',`upd_date` = '$ddate',`upd_time` = '$dtime',`upd_user` = 'is-helpdesk',`reg_user` = '".$row_get["branch_id"]."'";
		$succ = mysql_query($sql_update,$condb2);
	}
	else
	{
		$sql = "INSERT INTO `com_doc_date` (`corporation_id`, `company_id`, `doc_date`, `remark`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('".$row_get["corporation_id"]."', '".$row_get["company_id"]."', '$newDate', 'วันที่เอกสาร', '$ddate', '$dtime', '".$row_get["branch_id"]."', '$ddate', '$dtime', 'is-helpdesk')";
		//echo $sql;
		$succ = mysql_query($sql,$condb2);
	}
	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'set_doc_date.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}

?>