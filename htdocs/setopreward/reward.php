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
<h4>โปรแกรม Set OPS Reward</h4>
<div class="magin-top-20"></div>
<center>

<form action="reward_submit.php" id="reward_submit" method="post" accept-charset="UTF-8" onsubmit="return check_blank();" >
    <table width="400">
		<tr class="control-group" id="tr_rw_code">
			<td colspan="2" style="text-align:right;"><strong>Status : </strong><lebel id="status_id">เพิ่มข้อมูลใหม่</lebel></td>
		</tr>
		<tr class="control-group" id="tr_rw_code">
			<td width="100" style="text-align:right; padding-right:15px;">รหัส reward</td>
			<td width="300" ><input type="text" name="txt_rw_code" id="txt_rw_code" placeholder="รหัส reward" maxlength="10" onchange="check_field()"/></td>
		</tr>
		<!--<tr>
			<td style="text-align:right; padding-right:15px;">ชื่อ reward</td>
			<td><input type="text" name="txt_rw_name" id="txt_rw_name" placeholder="ชื่อ reward"></td>
		</tr>-->
		<tr class="control-group" id="tr_rw_prod">
			<td style="text-align:right;padding-right:15px;">รหัสสินค้า</td>
			<td>
				<input type="text" name="txt_rw_prod" id="txt_rw_prod" placeholder="รหัสสินค้า" maxlength="13" /> <!--onchange="check_product('txt_rw_prod')"-->
			</td>
		</tr>
		<!--<tr class="control-group" id="tr_rw_prod">
			<td style="text-align:right;padding-right:15px;">ชื่อสินค้า</td>
			<td>
				<input type="text" name="txt_prod_name" id="txt_prod_name" placeholder="ชื่อสินค้า" />
			</td>
		</tr>-->
		<tr class="control-group" id="tr_rw_desc">
			<td style="text-align:right;padding-right:15px;">รายละเอียด</td>
			<td><textarea name="txt_rw_desc" id="txt_rw_desc" placeholder="รายละเอียดของ reward"></textarea></td>
		</tr>
		<!--<tr class="control-group" id="tr_rw_shop">
			<td style="text-align:right;padding-right:15px;">shop ที่สามารถใช้งานได้</td>
			<td><textarea name="txt_rw_shop" id="txt_rw_shop"  placeholder="ex. 0001,0002"></textarea></td>
		</tr>-->
		<tr class="control-group" id="tr_rw_point">
			<td style="text-align:right;padding-right:15px;">คะแนนที่ใช้</td>
			<td><input type="text" name="txt_rw_point" id="txt_rw_point" placeholder="คะแนนที่ใช้แลก" maxlength="10"></td>
		</tr>
		<tr class="control-group" id="tr_rw_amnt">
			<td style="text-align:right;padding-right:15px;">จำนวนเงิน</td>
			<td><input type="text" name="txt_rw_amnt" id="txt_rw_amnt" placeholder="จำนวนเงินที่ใช้" maxlength="10"></td>
		</tr>
        <tr class="control-group" id="tr_rw_sdate">
			<td style="text-align:right;padding-right:15px;">วันที่เริ่มแลก</td>
			<td><input type="text" name="txt_rw_sdate" id="txt_rw_sdate" placeholder="วันที่เริ่มต้นการแลกของรางวัล"></td>
		</tr>
		<tr class="control-group" id="tr_rw_edate">
			<td style="text-align:right;padding-right:15px;">ถึง</td>
			<td><input type="text" name="txt_rw_edate"  id="txt_rw_edate" placeholder="วันที่สิ้นสุดการแลกของรางวัล"></td>
		</tr>
        
		<tr class="control-group" id="tr_rw_usdate">
			<td style="text-align:right;padding-right:15px;">วันที่เริ่มรับ</td>
			<td><input type="text" name="txt_rw_usdate" id="txt_rw_usdate" placeholder="วันที่เริ่มต้นการรับของรางวัล"></td>
		</tr>
		<tr class="control-group" id="tr_rw_uedate">
			<td style="text-align:right;padding-right:15px;">ถึง</td>
			<td><input type="text" name="txt_rw_uedate"  id="txt_rw_uedate" placeholder="วันที่สิ้นสุดการใช้งานของรางวัล"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input type="submit" class="btn btn-info" id="btn_save" value="บันทึกข้อมูล">
				<input type="button" class="btn btn-danger" id="btn_del" value="ลบข้อมูล" onclick="del_rwd()" style="display:none;">
				<input type="reset" class="btn btn-warning" id="btn_cancel" onclick="reset_rwd()" value="ยกเลิก" >
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
		$("#txt_rw_code").focus();
		//----------------------------------------------------------------------------- datepicker
		$( "#txt_rw_sdate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#txt_rw_edate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#txt_rw_usdate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#txt_rw_uedate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		
		//----------------------------------------------------------------------------- check input
		$("#txt_rw_point").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
		});
		
		$("#txt_rw_amnt").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
		});
		
		$("#txt_rw_prod").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && ((e.which<48 || e.which>57) && (e.which<65 || e.which>90) && (e.which<97 || e.which>122))) ? false : true ;
		});
		
		$("#txt_rw_code").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && ((e.which<48 || e.which>57) && (e.which<65 || e.which>90) && (e.which<97 || e.which>122))) ? false : true ;
		});
		
		//----------------------------------------------------------------------------- keypress
		$("#txt_rw_code").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}	*/
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_desc").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}	*/
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_prod").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{	
				/*if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}	
				else*/ if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}
				else
				{
					check_blank();
				}
				return false;
			}
		});
		/*$("#txt_prod_name").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				else if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}	
				else
				{
					check_blank();
				}
				return false;
			}
		});*/	
		
		$("#txt_rw_point").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}*/
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}	
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_amnt").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}*/
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}	
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_sdate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}*/	
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_edate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}*/
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}	
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_usdate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}*/
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				else if($("#txt_rw_uedate").val() == "")
				{
					$("#txt_rw_uedate").focus();	
				}	
				else
				{
					check_blank();
				}
				return false;
			}
		});	
		$("#txt_rw_uedate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_rw_code").val() == "")
				{
					$("#txt_rw_code").focus();	
				}
				else if($("#txt_rw_prod").val() == "")
				{
					$("#txt_rw_prod").focus();	
				}
				else if($("#txt_rw_desc").val() == "")
				{
					$("#txt_rw_desc").focus();	
				}
				else if($("#txt_rw_point").val() == "")
				{
					$("#txt_rw_point").focus();	
				}
				else if($("#txt_rw_amnt").val() == "")
				{
					$("#txt_rw_amnt").focus();	
				}
				else if($("#txt_rw_sdate").val() == "")
				{
					$("#txt_rw_sdate").focus();	
				}
				/*else if($("#txt_prod_name").val() == "")
				{
					$("#txt_prod_name").focus();	
				}*/
				else if($("#txt_rw_edate").val() == "")
				{
					$("#txt_rw_edate").focus();	
				}
				else if($("#txt_rw_usdate").val() == "")
				{
					$("#txt_rw_usdate").focus();	
				}	
				else
				{
					check_blank();
				}
				return false;
			}
		});	
	});
	
</script>
</body>
</html>
