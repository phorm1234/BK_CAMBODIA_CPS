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
<div class="container-narrow">
	<?php 
		if(empty($_SESSION["login"]))
		{
			echo "<script>window.location = 'login.php';</script>";		
		}
		require_once("header.php");
		require_once("connect.php");
		$sql = "SELECT * FROM `com_branch_detail`";
		$result = mysql_query($sql,$condb2);
		if(mysql_num_rows($result) < 1)
		{
			echo "<script>alert('กรุณาเพิ่มสาขา');window.location='create_branch.php';</script>";
		}
		
		$row = mysql_fetch_array($result);
		
	?>
<center><h3>Set Configuration</h3>
<div class="row marketing">	 
	<form action="set_conf_submit.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill_conf();" style="width:700px;">
		<table width="60%">
			<tr style="height:60px;">
				<td class="td-form-left">Code Type <lebel class="a-td"> : </lebel></td>
				<td>
					<select name="slt_code" id="slt_code" class="form-control" onchange="get_code()">	
						<option value="NO_KEYIN_MEMBER">NO_KEYIN_MEMBER</option>			
						<option value="CHECK_DOC_DATE">CHECK_DOC_DATE</option>	
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ค่าปกติ <a class="a-td"> : </a></td>
				<td> 
					<select name="slt_default" id="slt_default" class="form-control">
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ค่าพิเศษ <a class="a-td"> : </a></td>
				<td> 
					<select name="slt_condition" id="slt_condition" class="form-control">
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">วันที่เริ่มต้น <lebel class="a-td"> : </lebel></td>
				<td> <input type="text" name="txt_sdate" id="txt_sdate" class="form-control" placeholder="วันที่เริ่มใช้งาน"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">วันที่สิ้นสุด <lebel class="a-td"> : </lebel></td>
				<td> <input type="text" name="txt_edate" id="txt_edate" class="form-control" placeholder="วันที่สิิ้นสุดการใช้งาน"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">เวลาเริ่มต้น <lebel class="a-td"> : </lebel></td>
				<td>
					<select name="txt_stime" id="txt_stime" class="form-control">	
					<?php
						for($i=8;$i<=24;$i++)
						{
							$txt = "0".$i;
							$txt = substr($txt, -2);
							if($i != 24)
							{
								echo '<option value="'.$txt.':00:00">'.$txt.':00</option>';	
								echo '<option value="'.$txt.':30:00">'.$txt.':30</option>';
							}
							else
							{
								echo '<option value="00:00:00">00:00</option>';	
							}					
						} 
					?>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">เวลาสิ้นสุด <lebel class="a-td"> : </lebel></td>
				<td>
					<select name="txt_etime" id="txt_etime" class="form-control">	
					<?php
						for($i=8;$i<=24;$i++)
						{
							$txt = "0".$i;
							$txt = substr($txt, -2);
							if($i != 24)
							{
								echo '<option value="'.$txt.':00:00">'.$txt.':00</option>';	
								echo '<option value="'.$txt.':30:00">'.$txt.':30</option>';
							}
							else
							{
								echo '<option value="00:00:00">00:00</option>';	
							}					
						} 
					?>
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td colspan="2" align="center" class="btn-submit-form">
					<input type="submit" name="btn_submit" id="btn_submit" value="บันทึก" class="btn btn-primary"/>
					<input type="reset" name="btn_cancel" id="btn_cancel" value="ยกเลิก" class="btn btn-default"/>
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
	$( "#txt_sdate" ).datepicker({ dateFormat: 'dd/mm/yy' });
	$( "#txt_edate" ).datepicker({ dateFormat: 'dd/mm/yy' });
	
	$("#slt_code").focus();
	
	get_code();
		
	$("#slt_code").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#slt_default").focus();				
			return false;
		 }
	});
	
	$("#slt_default").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#slt_condition").focus();				
			return false;
		 }
	});
	
	$("#slt_condition").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_sdate").focus();				
			return false;
		 }
	});
	
	$("#txt_sdate").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_edate").focus();				
			return false;
		 }
	});
	
	$("#txt_edate").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_stime").focus();				
			return false;
		 }
	});
	
	$("#txt_stime").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_etime").focus();				
			return false;
		 }
	});
});

function checkFill_conf()
{
	if($("#txt_sdate").val() == "")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_sdate").focus();
		return false;
	}
	else if($("#txt_edate").val() == "")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_edate").focus();
		return false;
	}
	else
	{
		return true;
	}
}
</script>