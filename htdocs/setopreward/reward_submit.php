<?php 
	session_start(); 
	require("funcDB.php"); 
	date_default_timezone_set('Asia/Bangkok');
	
	$txt_rw_code = trim(mysql_real_escape_string($_POST["txt_rw_code"]));
	//$txt_rw_name = mysql_real_escape_string($_POST["txt_rw_name"]);
	$txt_rw_desc = nl2br(trim(mysql_real_escape_string($_POST["txt_rw_desc"])));
	$txt_rw_point = trim(mysql_real_escape_string($_POST["txt_rw_point"]));
	$txt_rw_prod = trim(mysql_real_escape_string($_POST["txt_rw_prod"]));
	//$txt_rw_shop = trim(mysql_real_escape_string($_POST["txt_rw_shop"]));
	$txt_rw_amnt = trim(mysql_real_escape_string($_POST["txt_rw_amnt"]));
	$txt_rw_sdate = trim(mysql_real_escape_string($_POST["txt_rw_sdate"]));
	$txt_rw_edate = trim(mysql_real_escape_string($_POST["txt_rw_edate"]));
	$txt_rw_usdate = trim(mysql_real_escape_string($_POST["txt_rw_usdate"]));
	$txt_rw_uedate = trim(mysql_real_escape_string($_POST["txt_rw_uedate"]));
	
	$date = date("Y-m-d");
	$time = date("H:i:s");
	//echo $txt_rw_code."----";
	$txt_rw_sdate = explode("/",$txt_rw_sdate);
	$txt_rw_sdate = $txt_rw_sdate["2"]."-".$txt_rw_sdate["1"]."-".$txt_rw_sdate["0"];
	
	$txt_rw_edate = explode("/",$txt_rw_edate);
	$txt_rw_edate = $txt_rw_edate["2"]."-".$txt_rw_edate["1"]."-".$txt_rw_edate["0"];
	
	$txt_rw_usdate = explode("/",$txt_rw_usdate);
	$txt_rw_usdate = $txt_rw_usdate["2"]."-".$txt_rw_usdate["1"]."-".$txt_rw_usdate["0"];
	
	$txt_rw_uedate = explode("/",$txt_rw_uedate);
	$txt_rw_uedate = $txt_rw_uedate["2"]."-".$txt_rw_uedate["1"]."-".$txt_rw_uedate["0"];
	
	$user = select_user_from_uid($_SESSION["u_id"]);
	
	$edit = select_reward_from_rwd_id($txt_rw_code);
	if($edit != "norecord")
	{
		//delete_rwd_from_pro_id($txt_rw_code);
		$branch = edit_rwd($txt_rw_code,$txt_rw_desc,$txt_rw_sdate,$txt_rw_edate,$txt_rw_point,$date,$time,$_SESSION["u_id"],$txt_rw_prod,$txt_rw_amnt);		
	}
	else
	{
		$head = add_rwd_head("OP","OP",$txt_rw_code,$txt_rw_desc,"N",$txt_rw_sdate,$txt_rw_edate,$txt_rw_point,"18",$date,$time,$_SESSION["u_id"],$date,$time,$_SESSION["u_id"]);
		if($head)
		{
			$detail = add_rwd_detail("OP","OP",$txt_rw_code,$txt_rw_prod, $txt_rw_sdate,$txt_rw_edate, $txt_rw_amnt, $date,$time,$_SESSION["u_id"],$date,$time,$_SESSION["u_id"]);
			if($detail)
			{
				$oprwd = add_rwd_opreward($txt_rw_code,$txt_rw_desc,$txt_rw_prod,$txt_rw_point,$txt_rw_usdate,$txt_rw_uedate);
				if($oprwd)
				{
					$branch = add_rwd_branch("OP","OP",$txt_rw_code, "ALL", $txt_rw_sdate,$txt_rw_edate, $date,$time,$_SESSION["u_id"],$date,$time,$_SESSION["u_id"]);
				}
			}
		}
	}
	
if($branch)
{
?>
	<script>
		alert("บันทึกข้อมูลเรียบร้อย");
		window.location = "reward.php";
	</script>
<?php
}
else
{
?>
	<script>
		alert("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");
		window.location = "reward.php";
	</script>
<?php
}
?>