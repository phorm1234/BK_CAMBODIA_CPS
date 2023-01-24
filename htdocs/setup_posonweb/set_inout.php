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
	
	$row = mysql_fetch_array($result);
?>
<div class="container-narrow">
	<?php 
		require_once("header.php");
	?>
<center><h3>ลงเวลาทำงาน</h3>
<div class="row marketing">	 
	<form action="set_inout_submit.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill_inout();" style="width:700px;">
		<table width="60%">
			<tr style="height:60px;">
				<td class="td-form-left">รหัสพนักงาน <a class="a-td"> : </a></td><td>000009</td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">ชื่อ-นามสุกล <a class="a-td"> : </a></td><td>Demo Demo</td>
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
				<td class="td-form-left">วันที่ <a class="a-td"> : </a></td><td> <input type="text" name="txt_date" id="txt_date" value="<?php echo $ddate; ?>" class="form-control"/></td>
			</tr>
			<tr style="height:60px;">
				<td class="td-form-left">เวลา <a class="a-td"> : </a></td><td> <input type="text" name="txt_time" id="txt_time" value="<?php echo $dtime; ?>" readonly="true" class="form-control"/></td>
			</tr>
			<tr style="height:60px;">
				<td colspan="2" align="center" class="btn-submit-form">
					<input type="hidden" name="txt_type" id="txt_type" value="N" class="form-control"/>
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
		$("#txt_shop_id").focus();
		//$.datepicker.formatDate( "yy-mm-dd" );
		$( "#txt_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$("#txt_shop_id").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
		});
		
		$("#txt_shop_id").on('keypress', function (event) {
			if(event.keyCode  == '13'){				
				$("#txt_date").focus();				
				return false;
			 }
		});
		$("#txt_date").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				else
				{
					return true;
				}
				return false;
			 }
		});
		
		var timerId = setInterval(function() {
			var now = new Date();
			thour = now.getHours();
			tmin = now.getMinutes();
			tsec = now.getSeconds();			
			
			var time = (thour < 10 ? "0" : "") + thour + (tmin < 10 ? ":0" : ":") + tmin + (tsec < 10 ? ":0" : ":") + tsec;
			$("#txt_time").val(time);
		}, 1000);
	});
</script>