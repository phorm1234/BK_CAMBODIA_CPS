<?php 
	require("funcDB.php"); 
	$ftype = $_POST["ftype"];
	
	if($ftype == "add_prog")
	{
		$name = trim(mysql_real_escape_string($_POST["txt_prog_name"]));
		$link = trim(mysql_real_escape_string($_POST["txt_prog_link"]));
		$chk = select_program_from_name($name);
		if($chk != "norecord")
		{
			del_program($_POST["txt_prog_id"]);
		}
		add_prog($name,$link);
	}
	else if($ftype == "check_field")
	{
		echo select_reward_from_rwd_id(mysql_real_escape_string($_POST["txt_rw_code"]));
	}
	else if($ftype == "del_rwd")
	{
		delete_rwd_from_pro_id(mysql_real_escape_string($_POST["txt_rw_code"]));
	}
	else if($ftype == "del_prog")
	{
		del_program($_POST["prog_id"]);
	}
	else if($ftype == "check_prog")
	{
		echo select_program_from_name(trim(mysql_real_escape_string($_POST["txt_prog_name"])));
	}
	else if($ftype == "check_avt_field")
	{
		echo select_activity_from_pro_id(trim(mysql_real_escape_string($_POST["txt_avt_code"])));
	}
	else if($ftype == "del_avt")
	{
		delete_avt_from_pro_id($_POST["txt_avt_code"]);
	}
	else if($ftype == "check_product")
	{
		echo select_product_from_id($_POST["txt_prod"]);
	}
	else if($ftype == "get_doc_status")
	{
		$txt_return = "";
		$result = select_all_doc_status_from_doc_tp($_POST["slt_doc_no"]);
		while($row = mysql_fetch_array($result))
		{
			$txt_return .= "<option value='".$row["status_no"]."'>".$row["status_no"]." : ".$row["description"]."</option>";
		}
		echo $txt_return;
	}
	else if($ftype == "check_p_field")
	{
		echo select_product_lock_from_prod_id(mysql_real_escape_string($_POST["txt_prod"]));
	}
	else if($ftype == "del_p")
	{
		delete_product_lock(trim(mysql_real_escape_string($_POST["txt_p_prod"])));
	}
?>