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
	require_once("connect.php");
	$sql = "SELECT * FROM `com_branch_detail`";
	$result = mysql_query($sql,$condb2);
	if(mysql_num_rows($result) < 1)
	{
		echo "<script>alert('กรุณาเพิ่มสาขา');window.location='create_branch.php';</script>";
	}
	
	$row = mysql_fetch_array($result);
?>
<center><h3>เวลาเปิด/ปิดร้าน</h3>
<div class="row marketing">	 

	<form action="set_shop_submit.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill_shop();" style="width:600px;" class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-lg-3 control-label">บริษัท :</label>
			<div class="col-lg-7">
				<select name="slt_brand" id="slt_brand" class="form-control" disabled="disabled">	
					<option value="OP" <?php if($row["corporation_id"] == "OP") echo "selected";?>>OP</option>			
					<option value="CPS" <?php if($row["corporation_id"] == "CPS") echo "selected";?>>CPS</option>	
					<option value="GNC" <?php if($row["corporation_id"] == "GNC") echo "selected";?>>GNC</option>	
				</select>
				<input type="hidden" name="txt_brand" value="<?php echo $row["corporation_id"];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-3 control-label">รหัสสาขา :</label>
			<div class="col-lg-7">
				<input type="text" name="txt_shop_id" id="txt_shop_id" class="form-control" readonly="true" value="<?php echo $row["branch_id"];?>" placeholder="รหัสสาขา"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-3 control-label">รหัสสาขา :</label>
			<div class="col-lg-7">
				<input type="text" name="txt_shop_name" id="txt_shop_name" class="form-control" readonly="true" value="<?php echo $row["branch_name"];?>" placeholder="ชื่อสาขา"/>
			</div>
		</div>
		<div class="bs-example normal" role="form">
			<div class="form-group">
				<label class="col-lg-3 control-label">เวลาที่เปิด :</label>
				<div class="col-lg-7">
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
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">เวลาที่ปิด :</label>
				<div class="col-lg-7">
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
				</div>
			</div>
		</div>
		
		<div class="bs-example weekend" role="form">
			<div class="form-group">
				<label class="col-lg-3 control-label">เวลาที่เปิด :</label>
				<div class="col-lg-7">
					<select name="txt_stime_w" id="txt_stime_w" class="form-control">	
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
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">เวลาที่ปิด :</label>
				<div class="col-lg-7">
					<select name="txt_etime_w" id="txt_etime_w" class="form-control">	
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
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-offset-9 col-lg-9">
				<input type="submit" name="btn_submit" id="btn_submit" value="บันทึก" class="btn btn-primary"/>
				<input type="reset" name="btn_cancel" id="btn_cancel" value="ยกเลิก" class="btn btn-default"/>
			</div>
		</div>
	</form><!-- /example -->
</center></div>
</body>
</html>
<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>
<script>
$(function(){
	$("#txt_stime").focus();
	
	$("#txt_shop_id").bind('keypress', function(e) {
		return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
	});
	
	$("#slt_brand").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_shop_id").focus();
			return false;
		 }
	});
	
	$("#txt_shop_id").on('keypress', function (event) {
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
	
	$("#txt_etime").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_stime_w").focus();				
			return false;
		 }
	});
	
	$("#txt_stime_w").on('keypress', function (event) {
		if(event.keyCode  == '13'){				
			$("#txt_etime_w").focus();				
			return false;
		 }
	});
});
</script>