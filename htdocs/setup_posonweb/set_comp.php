<?php 
	session_start(); 
	date_default_timezone_set("Asia/Bangkok");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Setup New Post</title>
	<!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/custom.css" rel="stylesheet">
	
	<link href="css/icon.css" rel="stylesheet">
	<link href="css/jquery-ui.css" rel="stylesheet" >
</head>

<body>
<?php 
	if(empty($_SESSION["login"]))
	{
		echo "<script>window.location = 'login.php';</script>";		
	}
	require_once("connect.php");
	$ddate = date("d/m/Y");
	$dtime = date("H:i:s");
	
	$all = "";
	$sql_chkdate = "select * from check_in_out where cid = '000009'";	
	$result_chkdate = mysql_query($sql_chkdate, $condb1);	
	while($rows = mysql_fetch_array($result_chkdate))
	{
		$arr = explode('-', $rows["check_date"]);
		$newDate = $arr[2].'/'.$arr[1].'/'.$arr[0];
		$all = $all."".$newDate."*";
	}
	
	$sql = "SELECT * FROM `com_branch_detail`";
	$result = mysql_query($sql,$condb2);
	if(mysql_num_rows($result) < 1)
	{
		echo "<script>alert('กรุณาเพิ่มสาขา');window.location='create_branch.php';</script>";
	}
	
	
?>
<div class="container-narrow">
<?php 
	require_once("header.php");
?>
<center><h3>Set IP</h3>
<div class="row marketing">	 
	<form action="set_comp_submit.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill_comp();" style="width:700px;">
		<table width="60%">
			<tr style="height:60px;">
				<td class="td-form-left" width="30%">รหัสสาขา <lebel class="a-td"> : </lebel></td>
				<td width="60%"> 
					<select name="txt_shop_id" id="txt_shop_id" class="form-control">
					<?php
						while($row = mysql_fetch_array($result))
						{
							echo "<option value='".$row["branch_id"]."'>".$row["branch_id"]."</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">IP Computer <a class="a-td"> : </a></td>
				<td> <input type="text" name="txt_ip" id="txt_ip" onchange="get_comp()" placeholder="IP Computer" class="form-control"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">Computer No. <a class="a-td"> : </a></td>
				<td>
					<select name="txt_no" id="txt_no" onchange="get_comp()" class="form-control">
						<?php
							for($i=1;$i<=10;$i++)
							{
								echo  "<option value='".$i."'>".$i."</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">POS ID <a class="a-td"> : </a></td>
				<td> <input type="text" name="txt_pos_id" id="txt_pos_id" placeholder="หมายเลขเครื่อง POS" class="form-control"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">Thermal Printer <a class="a-td"> : </a></td>
				<td> 
					<select name="txt_thermal" id="txt_thermal" class="form-control">
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">Cashdrawer <a class="a-td"> : </a></td>
				<td> 
					<select name="txt_cash" id="txt_cash" class="form-control">
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td colspan="2" align="center" class="btn-submit-form">
					<input type="hidden" name="hdn_shop_id" id="hdn_shop_id">
					<input type="hidden" name="hdn_ip" id="hdn_ip">
					<input type="hidden" name="hdn_no" id="hdn_no">
					<input type="submit" name="btn_submit" id="btn_submit" value="บันทึก" class="btn btn-primary"/>
					<input type="reset" name="btn_cancel" id="btn_cancel" value="ยกเลิก" onclick="reset_comp()" class="btn btn-default"/>
				</td>
			</tr>
		</table>
	</form>
	
	</div>
</center>
</body>
</html>
<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>
<script>
	$(function(){
		$("#txt_shop_id").focus();
		
		$("#txt_shop_id").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_ip").focus();				
				return false;
			 }
		});
		
		$("#txt_ip").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_no").focus();				
				return false;
			 }
		});
		
		$("#txt_no").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_pos_id").focus();				
				return false;
			 }
		});
		
		$("#txt_pos_id").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_thermal").focus();				
				return false;
			 }
		});
		
		$("#txt_thermal").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_cash").focus();				
				return false;
			 }
		});
	});
</script>