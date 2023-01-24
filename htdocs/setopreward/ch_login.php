<?php 
	session_start(); 
	include("funcDB.php");
	
	$txt_username = mysql_real_escape_string($_POST["txt_username"]);
	$txt_password = mysql_real_escape_string($_POST["txt_password"]);
	
	$get_user = select_user_form_userpass($txt_username,$txt_password);
	/*if($txt_username == "admin" && $txt_password == "admin_mng")
	{
		$_SESSION["u_id"] = $txt_username;
		$_SESSION["u_name"] = $txt_username;
		echo $txt_username;
	}
	else if($get_user != "norecord")
	{
		$_SESSION["u_id"] = $get_user["user_id"];
		$_SESSION["u_name"] = $get_user["user_name"];
		echo $_SESSION["u_id"]."//".$_SESSION["u_name"];
	}
	else
	{
		echo "norecord";
	}*/
	if($get_user != "norecord")
	{
		$_SESSION["u_id"] = $get_user["user_id"];
		$_SESSION["u_name"] = $get_user["name"]." ".$get_user["surname"];
		echo $_SESSION["u_id"]."//".$_SESSION["u_name"];
	}
	else
	{
		echo "norecord";
	}
?>