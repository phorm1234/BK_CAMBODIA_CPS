<?php 
	session_start(); 
	require("funcDB.php"); 
	date_default_timezone_set('Asia/Bangkok');
	
	$txt_avt_code = trim(mysql_real_escape_string($_POST["txt_avt_code"]));
	$txt_avt_name = trim(mysql_real_escape_string($_POST["txt_avt_name"]));
	
	$txt_avt_point = trim(mysql_real_escape_string($_POST["txt_avt_point"]));
	$txt_avt_amnt = trim(mysql_real_escape_string($_POST["txt_avt_amnt"]));
	$txt_avt_sdate = trim(mysql_real_escape_string($_POST["txt_avt_sdate"]));
	$txt_avt_edate = trim(mysql_real_escape_string($_POST["txt_avt_edate"]));
	
	$date = date("Y-m-d");
	$time = date("H:i:s");
	//echo $txt_rw_code."----";
	$txt_avt_sdate = explode("/",$txt_avt_sdate);
	$txt_avt_sdate = $txt_avt_sdate["2"]."-".$txt_avt_sdate["1"]."-".$txt_avt_sdate["0"];
	
	$txt_avt_edate = explode("/",$txt_avt_edate);
	$txt_avt_edate = $txt_avt_edate["2"]."-".$txt_avt_edate["1"]."-".$txt_avt_edate["0"];
	
	$user = select_user_from_uid($_SESSION["u_id"]);
	
	$edit = select_activity_from_pro_id($txt_avt_code);
	if($edit != "norecord")
	{
		//delete_avt_from_pro_id($txt_avt_code);
		$head = edit_avt_head($txt_avt_code,$txt_avt_name,$txt_avt_sdate,$txt_avt_edate,$txt_avt_point,$txt_avt_amnt,$date,$time,$_SESSION["u_id"]);
		$counter=1;
		$row = select_start_end_time($txt_avt_code);
		delete_detail($txt_avt_code);
		for($i=1;$i<=5;$i++)
		{
			$name = "txt_avt_desc".$i;
			$txt_avt_desc = trim(mysql_real_escape_string($_POST[$name]));
			if($txt_avt_desc != "")
			{
				$branch = add_avt_detail("OP","OP",$txt_avt_code,$counter++,$txt_avt_desc,$txt_avt_sdate,$txt_avt_edate,$row["reg_date"],$row["reg_time"],$row["reg_user"],$date,$time,$_SESSION["u_id"]);
			}
		}
	}
	else
	{
		$head = add_avt_head("OP","OP",$txt_avt_code,$txt_avt_name,$txt_avt_sdate,$txt_avt_edate,$txt_avt_point,$txt_avt_amnt,$date,$time,$_SESSION["u_id"],$date,$time,$_SESSION["u_id"]);
		if($head)
		{
			$counter=1;
			for($i=1;$i<=5;$i++)
			{
				$name = "txt_avt_desc".$i;
				$txt_avt_desc = trim(mysql_real_escape_string($_POST[$name]));
				if($txt_avt_desc != "")
				{
					add_avt_detail("OP","OP",$txt_avt_code,$counter++,$txt_avt_desc,$txt_avt_sdate,$txt_avt_edate,$date,$time,$_SESSION["u_id"],$date,$time,$_SESSION["u_id"]);
				}
			}
			$branch = add_rwd_branch("OP","OP",$txt_avt_code,"ALL", $txt_avt_sdate,$txt_avt_edate, $date,$time,$_SESSION["u_id"],$date,$time,$_SESSION["u_id"]);
		}
	}
if($branch)
{
?>
	<script>
		alert("บันทึกข้อมูลเรียบร้อย");
		window.location = "activity.php";
	</script>
<?php
}
else
{
?>
	<script>
		alert("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");
		window.location = "activity.php";
	</script>
<?php
}
?>