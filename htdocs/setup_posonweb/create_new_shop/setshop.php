<?php
	date_default_timezone_set("Asia/Bangkok");
	$db_host = "10.100.53.2";
	$db_username = "master";
	$db_pass = "master";
	$db_name = "pos_ssup";

	mysql_connect("$db_host","$db_username","$db_pass") or die (mysql_error());
	mysql_select_db("$db_name") or die (mysql_error());
	mysql_query("SET NAMES UTF8");
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	
	$txt_brand_id = mysql_real_escape_string($_POST["txt_brand_id"]);
	$txt_shop_id = mysql_real_escape_string($_POST["txt_shop_id"]);
	$txt_shop_name = mysql_real_escape_string($_POST["txt_shop_name"]);
	$txt_ip = mysql_real_escape_string($_POST["txt_ip"]);
	$txt_phone = mysql_real_escape_string($_POST["txt_phone"]);
	$txt_moblie = mysql_real_escape_string($_POST["txt_moblie"]);
	
	$txt_uname = mysql_real_escape_string($_POST["txt_uname"]);
	$txt_pass = mysql_real_escape_string($_POST["txt_pass"]);
	
	$type = mysql_real_escape_string($_POST["type"]);
	$dtime = date("H:i:s");	
	$ddate = date("Y-m-d");
	
	if(array_key_exists('txt_date', $_POST))
	{
		$arr = explode('/', $_POST["txt_date"]);
		$ddate = $arr[2].'/'.$arr[1].'/'.$arr[0];
	}
	
	$sql_select = "SELECT * FROM `shop` WHERE `shop` = '$txt_shop_id' and `brand` = '$txt_brand_id'";
	$get_select = mysql_query($sql_select);
	if(mysql_num_rows($get_select) < 1 && $type=="add")
	{	
		$sql = "INSERT INTO `shop` (`brand`, `shop`, `shop_name`, `ip`, `phone`, `mobile`, `user_name`, `password`, `status`, `send`, `date_setup`, `time_setup` ) VALUES ('$txt_brand_id', '$txt_shop_id', '$txt_shop_name', '$txt_ip', '$txt_phone', '$txt_moblie', '$txt_uname', '$txt_pass', 'Y', 'Y', '$ddate', '$dtime')";
		$succ = mysql_query($sql);
		if($succ)
		{
			echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'index.php';</script>";		
		}
		else
		{
			echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
		}
	}
	else if($type=="edit")
	{
		$sql = "UPDATE `pos_ssup`.`shop` SET `shop_name` = '$txt_shop_name', `ip` = '$txt_ip', `phone` = '$txt_phone', `mobile` = '$txt_moblie', `date_setup` = '$ddate' WHERE `shop` = '$txt_shop_id'";
		$succ = mysql_query($sql);
		if($succ)
		{
			echo "<script>alert('บันทึกข้อมูลเรียบร้อย');window.location = 'index.php';</script>";		
		}
		else
		{
			echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง');window.history.back();</script>";	
		}
	}
?>