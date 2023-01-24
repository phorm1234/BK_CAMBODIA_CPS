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
	
	$txt_shop_id = mysql_real_escape_string($_POST["txt_shop_id"]);
	$sql = "SELECT * FROM `shop` where shop = '$txt_shop_id'";
	//echo $sql;
	$get_select = mysql_query($sql);
	if(mysql_num_rows($get_select) > 0)
	{		
		$result = mysql_fetch_array($get_select);
		
			$arr = explode('-', $result["date_setup"]);
			$ddate = $arr[2].'/'.$arr[1].'/'.$arr[0];
		
		echo $result["shop_name"]."-next-".$result["ip"]."-next-".$result["phone"]."-next-".$result["mobile"]."-next-".$ddate;
		
		
	}
	else
	{
		echo "nodata";
	}
?>