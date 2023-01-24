<?php 
	session_start(); 
	if(empty($_SESSION["u_id"]))
	{
		echo "<script>window.location = 'login.php'</script>";
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<link href="css/custom.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery-ui.css" rel="stylesheet"/>
</head>

<body>
<h4>โปรแกรม Set OPS Activity</h4>
<div class="magin-top-20"></div>
<center>

<form action="activity_submit.php" id="reward_submit" method="post" accept-charset="UTF-8" onsubmit="return check_avt_blank();">
    <table>
		<tr class="control-group" id="tr_rw_code">
			<td colspan="2" style="text-align:right;"><strong>Status : </strong><lebel id="status_id">เพิ่มข้อมูลใหม่</lebel></td>
		</tr>
		<tr>
			<td style="text-align:right; padding-right:15px;">รหัส Activity</td>
			<td><input type="text" name="txt_avt_code" id="txt_avt_code" placeholder="รหัส Activity" maxlength="13" onchange="check_avt_field()"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">หัวข้อกิจกรรม</td>
			<td><input type="text" name="txt_avt_name" id="txt_avt_name" placeholder="หัวข้อกิจกรรม" maxlength="40"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รายละเอียด บรรทัดที่ 1</td>
			<td><input type="text" name="txt_avt_desc1" id="txt_avt_desc1" placeholder="รายละเอียด บรรทัดที่ 1" maxlength="40"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รายละเอียด บรรทัดที่ 2</td>
			<td><input type="text" name="txt_avt_desc2" id="txt_avt_desc2" placeholder="รายละเอียด บรรทัดที่ 2" maxlength="40"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รายละเอียด บรรทัดที่ 3</td>
			<td><input type="text" name="txt_avt_desc3" id="txt_avt_desc3" placeholder="รายละเอียด บรรทัดที่ 3" maxlength="40"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รายละเอียด บรรทัดที่ 4</td>
			<td><input type="text" name="txt_avt_desc4" id="txt_avt_desc4" placeholder="รายละเอียด บรรทัดที่ 4" maxlength="40"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รายละเอียด บรรทัดที่ 5</td>
			<td><input type="text" name="txt_avt_desc5" id="txt_avt_desc5" placeholder="รายละเอียด บรรทัดที่ 5" maxlength="40"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">คะแนนที่ใช้</td>
			<td><input type="text" name="txt_avt_point" id="txt_avt_point" placeholder="คะแนนที่ใช้แลก" maxlength="10"></td>
		</tr>
		<tr class="control-group" id="tr_rw_amnt">
			<td style="text-align:right;padding-right:15px;">จำนวนเงิน</td>
			<td><input type="text" name="txt_avt_amnt" id="txt_avt_amnt" placeholder="จำนวนเงินที่ใช้" maxlength="10"></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">วันที่เริ่ม</td>
			<td><input type="text" name="txt_avt_sdate" id="txt_avt_sdate" placeholder="วันที่เริ่มใช้งาน"/></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">วันที่สิ้นสุด</td>
			<td><input type="text" name="txt_avt_edate"  id="txt_avt_edate" placeholder="วันที่สิ้นสุดการใช้งาน"/></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input type="submit" class="btn btn-info" id="btn_save" value="บันทึกข้อมูล">
				<input type="button" class="btn btn-danger" id="btn_del" value="ลบข้อมูล" onclick="del_avt()" style="display:none;">
				<input type="reset" class="btn btn-warning" id="btn_cancel" onclick="reset_avt()" value="ยกเลิก" >
			</td>
		</tr>
	</table>
</form>
</center>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/custom.js"></script>
<script>
	$(function() {
		$("#txt_avt_code").focus();
		//----------------------------------------------------------------------------- datepicker
		$( "#txt_avt_sdate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#txt_avt_edate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		
		//----------------------------------------------------------------------------- check input
		$("#txt_avt_point").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
		});
		
		$("#txt_avt_amnt").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
		});
		
		$("#txt_avt_code").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && ((e.which<48 || e.which>57) && (e.which<65 || e.which>90) && (e.which<97 || e.which>122))) ? false : true ;
		});
		
		//----------------------------------------------------------------------------- keypress
		$("#txt_avt_code").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});	
		$("#txt_avt_name").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});	
		$("#txt_avt_desc1").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_desc2").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_desc3").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_desc4").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_desc5").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_point").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_amnt").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_sdate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_edate").val() == "")
				{
					$("#txt_avt_edate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
		$("#txt_avt_edate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_avt_code").val() == "")
				{
					$("#txt_avt_code").focus();	
				}
				else if($("#txt_avt_name").val() == "")
				{
					$("#txt_avt_name").focus();	
				}
				else if($("#txt_avt_desc1").val() == "")
				{
					$("#txt_avt_desc1").focus();	
				}
				else if($("#txt_avt_desc2").val() == "")
				{
					$("#txt_avt_desc2").focus();	
				}
				else if($("#txt_avt_desc3").val() == "")
				{
					$("#txt_avt_desc3").focus();	
				}
				else if($("#txt_avt_desc4").val() == "")
				{
					$("#txt_avt_desc4").focus();	
				}
				else if($("#txt_avt_desc5").val() == "")
				{
					$("#txt_avt_desc5").focus();	
				}
				else if($("#txt_avt_point").val() == "")
				{
					$("#txt_avt_point").focus();	
				}
				else if($("#txt_avt_amnt").val() == "")
				{
					$("#txt_avt_amnt").focus();	
				}
				else if($("#txt_avt_sdate").val() == "")
				{
					$("#txt_avt_sdate").focus();	
				}
				else
				{
					check_avt_blank();
				}
				return false;
			}
		});
	});	
</script>
</body>
</html>
