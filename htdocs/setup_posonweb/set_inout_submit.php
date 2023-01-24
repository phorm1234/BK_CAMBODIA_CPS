<?php
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	require_once("connect.php");
	
	$txt_shop_id = mysql_real_escape_string($_POST["txt_shop_id"]);
	$txt_date = mysql_real_escape_string($_POST["txt_date"]);
	$txt_time = mysql_real_escape_string($_POST["txt_time"]);
	$txt_type = mysql_real_escape_string($_POST["txt_type"]);		
	
	$cid = "000009";

	$arr = explode('/', $txt_date);
	$newDate = $arr[2].'-'.$arr[1].'-'.$arr[0];
	
	$dtime = date("H:i:s");

	$sql_chkdate = "select * from check_in_out where cid = '$cid' and check_date = '$newDate'";
	
	$result_chkdate = mysql_query($sql_chkdate, $condb1);
	
	if(mysql_num_rows($result_chkdate) > 0 && $txt_type == "Y")
	{	
		$rows = mysql_fetch_array($result_chkdate);
		$ch_id = $rows["check_id"];
		
		$sql_update = "UPDATE `check_in_out` SET `shop_id` = '$txt_shop_id',`check_in` = '$dtime' WHERE `check_id` ='$ch_id'";
		//echo $sql_update;
		$succ = mysql_query($sql_update,$condb1);
	}
	else
	{
		$sql = "INSERT INTO `check_in_out` (`cid`, `time_id`, `shop_id`, `check_date`, `check_in`, `check_in_seq`, `check_in_sent`, `check_in_reason`) VALUES ('$cid', '5', '$txt_shop_id', '$newDate', '$dtime', '1', '1', '1')";
		//echo $sql;
		$succ = mysql_query($sql,$condb1);
	}
	if($succ)
	{
		echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'set_inout.php';</script>";		
	}
	else
	{
		echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
	}

?>