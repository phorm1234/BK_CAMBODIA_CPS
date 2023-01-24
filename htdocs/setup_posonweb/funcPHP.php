<?php 
	require_once("connect.php");
	$ftype = $_POST["ftype"];
	if($ftype == "get_branch_detail")
	{
		$txt_branch_id = $_POST["txt_branch_id"];
		$slt_brand = $_POST["slt_brand"];
		$sql = "SELECT * FROM `com_branch_detail` WHERE `branch_id` = '$txt_branch_id' AND corporation_id = '$slt_brand'";
		
		$result = mysql_query($sql,$condb2);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			echo $row["branch_name"]."-next-".$row["branch_tp"]."-next-".$row["branch_phone"]."-next-".$row["branch_mobile"]."-next-".$row["address"]."-next-".$row["road"];
		}
		else
		{
			echo "norecord";
		}
	}
	else if($ftype == "get_comp")
	{
		$txt_ip = $_POST["txt_ip"];
		$txt_no = $_POST["txt_no"];
		$txt_shop_id = $_POST["txt_shop_id"];
		$sql = "SELECT * FROM `com_branch_computer` WHERE computer_no = '$txt_no' AND com_ip = '$txt_ip' AND  branch_id = '$txt_shop_id'";
		
		$result = mysql_query($sql,$condb2);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			echo $row["pos_id"]."-next-".$row["thermal_printer"]."-next-".$row["cashdrawer"];
		}
		else
		{
			echo "norecord";
		}
	}
	else if($ftype == "get_code")
	{
		$slt_code = $_POST["slt_code"];
		$sql = "SELECT * FROM `com_pos_config` WHERE code_type = '$slt_code'";
		$result = mysql_query($sql,$condb2);
		
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$start_date = explode('-', $row["start_date"]);
			$start_date = $start_date[2].'/'.$start_date[1].'/'.$start_date[0];
			
			$end_date = explode('-', $row["end_date"]);
			$end_date = $end_date[2].'/'.$end_date[1].'/'.$end_date[0];
		
			echo $row["default_status"]."-next-".$row["condition_status"]."-next-".$start_date."-next-".$end_date."-next-".$row["start_time"]."-next-".$row["end_time"];
		}
		else
		{
			echo "norecord";
		}
	}
?>