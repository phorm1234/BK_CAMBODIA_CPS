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
<center><h3>กำหนดรหัสผู้ใช้</h3>
<div class="row marketing">	 
	<form action="set_user_submit.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill_user();" style="width:700px;">
		<table width="60%">
			<tr style="height:60px;">
				<td class="td-form-left">บริษัท <lebel class="a-td"> : </lebel></td>
				<td>
					<select name="slt_brand" id="slt_brand" class="form-control" disabled="disabled">	
						<option value="OP" <?php if($row["corporation_id"] == "OP") echo "selected";?>>OP</option>			
						<option value="CPS" <?php if($row["corporation_id"] == "CPS") echo "selected";?>>CPS</option>	
						<option value="GNC" <?php if($row["corporation_id"] == "GNC") echo "selected";?>>GNC</option>	
					</select>
					<input type="hidden" name="txt_brand" value="<?php echo $row["corporation_id"];?>">
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">รหัสสาขา <lebel class="a-td"> : </lebel></td>
				<td> 
					<input type="text" name="txt_shop_id" id="txt_shop_id" class="form-control" readonly="true" value="<?php echo $row["branch_id"];?>" placeholder="รหัสสาขา"/>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ชื่อสาขา <lebel class="a-td"> : </lebel></td>
				<td>
					<input type="text" name="txt_shop_name" id="txt_shop_name" class="form-control" readonly="true" value="<?php echo $row["branch_name"];?>" placeholder="ชื่อสาขา"/>
				</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">รหัสพนักงาน <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_emp_id" id="txt_emp_id" class="form-control" value="000009" readonly="true"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">รหัสผ่าน <lebel class="a-td"> : </lebel></td>
				<td><input type="text" name="txt_emp_pass" id="txt_emp_pass" class="form-control" value="000009" readonly="true"/></td>
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
				<td colspan="2" align="center" class="btn-submit-form">
					<input type="submit" name="btn_submit" id="btn_submit" value="บันทึก" class="btn btn-primary"/>
					<input type="reset" name="btn_cancel" id="btn_cancel" value="ยกเลิก" class="btn btn-default"/>
				</td>
			</tr>
		</table>
	</form>
	</center>
	</div>
</div>
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
	
	$("#txt_shop_id").bind('keypress', function(e) {
		return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
	});
	
	$("#txt_sdate").focus();
	
	$("#slt_brand").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_shop_id").focus();
			return false;
		 }
	});
	
	$("#txt_shop_id").on('keypress', function (event) {
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
	
});

</script>