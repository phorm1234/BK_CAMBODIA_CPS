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
<?php require("funcDB.php"); ?>
<h4>สินค้า New produc</h4>
<div class="magin-top-20"></div>
<center>

<form action="product_submit.php" id="reward_submit" method="post" accept-charset="UTF-8" onsubmit="return check_p_blank();" >
    <table width="400">
		<tr>
			<td colspan="2" style="text-align:right;"><strong>Status : </strong><lebel id="status_id">เพิ่มข้อมูลใหม่</lebel></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รหัสสินค้า</td>
			<td>
				<input type="text" name="txt_p_prod" id="txt_p_prod" placeholder="รหัสสินค้า" maxlength="13"/>
			</td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">ชื่อสินค้า</td>
			<td>
				<input type="text" name="txt_prod_name" id="txt_prod_name" placeholder="ชื่อสินค้า" readonly="true"/>
			</td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">ประเภทเอกสาร</td>
			<td>
				<select name="slt_doc_no" id="slt_doc_no" onchange="get_doc_status('')">
					<option value="">ประเภทเอกสาร</option>
				<?php
					$all_doc_no = select_all_doc_no();
					 while($doc_no = mysql_fetch_array($all_doc_no))
					 {
					 	echo "<option value='".$doc_no["doc_tp"]."'>".$doc_no["doc_tp"]." : ".$doc_no["remark"]."</option>";
					 }
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">สถานะเอกสาร</td>
			<td>
				<select name="slt_doc_status" id="slt_doc_status">
					<option value="">สถานะเอกสาร</option>
				</select>
			</td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">วันที่เริ่ม Lock สินค้า</td>
			<td><input type="text" name="txt_p_sdate" id="txt_p_sdate" placeholder="วันที่เริ่มใช้งาน"></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">วันที่สิ้นสุด Lock สินค้า</td>
			<td><input type="text" name="txt_p_edate"  id="txt_p_edate" placeholder="วันที่สิ้นสุดการใช้งาน"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input type="submit" class="btn btn-info" id="btn_save" value="บันทึกข้อมูล">
				<input type="button" class="btn btn-danger" id="btn_del" value="ลบข้อมูล" onclick="del_p()" style="display:none;">
				<input type="reset" class="btn btn-warning" id="btn_cancel" onclick="reset_p()" value="ยกเลิก" >
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
		$("#txt_p_prod").focus();
		//----------------------------------------------------------------------------- datepicker
		$( "#txt_p_sdate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#txt_p_edate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		
		$("#txt_p_prod").change(function(){
			check_p_field();
		});
		
		$("#txt_p_prod").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{		
				if($("#slt_doc_no").val() == "")
				{
					$("#slt_doc_no").focus();	
				}
				else if($("#slt_doc_status").val() == "")
				{
					$("#slt_doc_status").focus();	
				}
				else if($("#txt_p_sdate").val() == "")
				{
					$("#txt_p_sdate").focus();	
				}
				else if($("#txt_p_edate").val() == "")
				{
					$("#txt_p_edate").focus();	
				}
				else
				{
					var ch = check_p_field();
					if(ch)
					{
						check_p_blank();
					}
				}
				return false;
			}
		});	
		$("#slt_doc_no").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_p_prod").val() == "")
				{
					$("#txt_p_prod").focus();	
				}
				else if($("#slt_doc_status").val() == "")
				{
					$("#slt_doc_status").focus();	
				}
				else if($("#txt_p_sdate").val() == "")
				{
					$("#txt_p_sdate").focus();	
				}
				else if($("#txt_p_edate").val() == "")
				{
					$("#txt_p_edate").focus();	
				}
				else
				{
					check_p_blank();
				}
				return false;
			}
		});	
		$("#slt_doc_status").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_p_prod").val() == "")
				{
					$("#txt_p_prod").focus();	
				}
				else if($("#slt_doc_no").val() == "")
				{
					$("#slt_doc_no").focus();	
				}
				else if($("#txt_p_sdate").val() == "")
				{
					$("#txt_p_sdate").focus();	
				}
				else if($("#txt_p_edate").val() == "")
				{
					$("#txt_p_edate").focus();	
				}
				else
				{
					check_p_blank();
				}
				return false;
			}
		});	
		$("#txt_p_sdate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_p_prod").val() == "")
				{
					$("#txt_p_prod").focus();	
				}
				else if($("#slt_doc_no").val() == "")
				{
					$("#slt_doc_no").focus();	
				}
				else if($("#slt_doc_status").val() == "")
				{
					$("#slt_doc_status").focus();	
				}
				else if($("#txt_p_edate").val() == "")
				{
					$("#txt_p_edate").focus();	
				}
				else
				{
					check_p_blank();
				}
				return false;
			}
		});	
		$("#txt_p_edate").keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13')
			{			
				if($("#txt_p_prod").val() == "")
				{
					$("#txt_p_prod").focus();	
				}
				else if($("#slt_doc_no").val() == "")
				{
					$("#slt_doc_no").focus();	
				}
				else if($("#slt_doc_status").val() == "")
				{
					$("#slt_doc_status").focus();	
				}
				else if($("#txt_p_sdate").val() == "")
				{
					$("#txt_p_sdate").focus();	
				}
				else
				{
					check_p_blank();
				}
				return false;
			}
		});	
	});
	
		
	
</script>
</body>
</html>
