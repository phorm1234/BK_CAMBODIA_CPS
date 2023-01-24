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
	<link href="css/jquery-ui.css" rel="stylesheet">
</head>

<body>

<div class="container-narrow">
<?php 
	if(empty($_SESSION["login"]))
	{
		echo "<script>window.location = 'login.php';</script>";		
	}
	require_once("header.php");
?>
<center><h3>Create Shop</h3>
<div class="row marketing">	  

	<form action="create_branch_submit.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill_create_branch();" style="width:700px;" class="form-horizontal" role="form">
		<table width="70%">
			<tr style="height:40px;">
				<td class="td-form-left" width="30%">บริษัท <lebel class="a-td"> : </lebel></td>
				<td width="60%">
					<select name="slt_brand" id="slt_brand" class="form-control">	
						<option value="OP">OP</option>			
						<option value="CPS">CPS</option>	
						<option value="GNC">GNC</option>	
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">รหัสสาขา <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_branch_id" id="txt_branch_id" maxlength="4" class="form-control" onchange="get_branch_detail()" placeholder="รหัสสาขา" /></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ขื่อสาขา <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_branch_name" id="txt_branch_name" class="form-control" placeholder="ชื่อสาขา" /></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ประเภทสาขา <lebel class="a-td"> : </lebel></td>
				<td>
					<select name="slt_branch_tp" id="slt_branch_tp" class="form-control">	
						<option value="S">Shop</option>			
						<option value="C">Corner</option>	
						<option value="F">Franchise</option>	
					</select>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">เบอร์โทรศัพท์ร้าน <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_phone" id="txt_phone" class="form-control" maxlength="15" placeholder="ex. 02-123-4567"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">เบอร์โทรศัพท์มือถือ <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_mobile" id="txt_mobile" class="form-control" maxlength="15" placeholder="ex. 081-123-4567"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ที่อยู่ <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_address" id="txt_address" class="form-control" maxlength="50" placeholder="ที่อยู่ร้าน"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left"></td>
				<td><input type="text" name="txt_road" id="txt_road" class="form-control" maxlength="50" placeholder="ที่อยู่ร้าน (ต่อ)"/></td>
			</tr>
			<tr style="height:60px;">
				<td colspan="2" align="center" class="btn-submit-form">
					<input type="submit" name="btn_submit" id="btn_submit" value="บันทึก" class="btn btn-primary"/>
					<input type="reset" name="btn_cancel" id="btn_cancel" value="ยกเลิก" onclick="reset_create_branch()" class="btn btn-default"/>
				</td>
			</tr>
		</table>
	</form><!-- /example -->

</center>
</div>
</body>
</html>
<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>
<script>
	$(function(){
		$("#slt_brand").focus();
		
		$("#slt_brand").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_branch_id").focus();				
				return false;
			 }
		});
		$("#txt_branch_id").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_branch_name").focus();				
				return false;
			 }
		});
		$("#txt_branch_name").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#slt_branch_tp").focus();				
				return false;
			 }
		});
		$("#slt_branch_tp").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_phone").focus();				
				return false;
			 }
		});
		$("#txt_phone").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_mobile").focus();				
				return false;
			 }
		});
		$("#txt_mobile").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_address").focus();				
				return false;
			 }
		});
		$("#txt_address").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_road").focus();				
				return false;
			 }
		});
	});
</script>