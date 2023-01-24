<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<style>
	.form-control{
		padding:5px;
	}
	.td-form-right
	{
		text-align:right;
		padding-right:20px;
	}
	.td-form-left
	{
		text-align:left;
		padding-left:30px;
	}
	.btn-submit-form input
	{
		width:100px;
	}
</style>
</head>
<body>
	<?php
		$ddate = date("d/m/Y");
		$dtime = date("H:i:s");
	?>
			<center><form action="setshop.php" method="post" accept-charset="UTF-8" onsubmit="return checkFill();">			
     
			<table >
				<tr style="height:50px;">
					<td class="td-form-right" width="170">บริษัท : </td>
					<td width="300">					
						<select class="form-control" name="txt_brand_id" id="txt_brand_id">
						  <option value="OP" selected="selected"> OP </option>
						</select>		
					</td>
				</tr>
				<tr style="height:50px;">
					<td class="td-form-right">รหัสสาขา : </td><td> <input type="text" name="txt_shop_id" id="txt_shop_id" class="form-control" maxlength="4" placeholder="รหัสสาขา"/></td>
				</tr>
				<tr style="height:50px;">
					<td class="td-form-right">ชื่อจุดขาย : </td><td> <input type="text" name="txt_shop_name" id="txt_shop_name" class="form-control" placeholder="ชื่อจุดขาย"/></td>
				</tr>
				<tr style="height:50px;">
					<td class="td-form-right">IP Address : </td><td> <input type="text" name="txt_ip" id="txt_ip" class="form-control" placeholder="IP Address"/></td>
				</tr>
				<tr style="height:50px;">
					<td class="td-form-right">เบอร์โทรศัพท์จุดขาย : </td><td> <input type="text" name="txt_phone" id="txt_phone" class="form-control" placeholder="เบอร์โทรศัพท์จุดขาย"/></td>
				</tr>
				<tr style="height:50px;">
					<td class="td-form-right">เบอร์โทรศัพท์มือถือ : </td><td> <input type="text" name="txt_moblie" id="txt_moblie" class="form-control" placeholder="เบอร์โทรศัพท์มือถือ"/></td>
				</tr>
				<tr style="height:50px;">
					<td class="td-form-right">วันที่เปิดจุดขาย : </td><td> <input type="text" name="txt_date" id="txt_date" class="form-control" placeholder="วันที่เปิดจุขาย" value="<?php echo $ddate; ?>"/></td>
				</tr>
				<tr style="height:50px;">
					<td></td>
					<td class="btn-submit-form">
						<input type="hidden" name="txt_uname" id="txt_uname" value="pos-ssup" class="form-control"/>
						<input type="hidden" name="txt_pass" id="txt_pass" value="P0z-$$up" class="form-control"/>
						
						<input type="hidden" name="type" id="type" value="add" class="form-control"/>
						
						<input type="submit" name="btn_submit" id="btn_submit" value="บันทึก" class="btn btn-primary" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="ยกเลิก" class="btn btn-default" onclick="window.location='index.php'"/>
					</td>
				</tr>
			</table>
		</form></center>
	</div>
</div>
</body>
</html>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery-ui.js"></script>
<script>
	// -> /srv/www/htdocs/ims/create_new_shop
	$(function(){
		$("#txt_shop_id").focus();
		
		$("#txt_shop_id").bind('keypress', function(e) {
			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
		});
		
		$("#txt_date").datepicker({ dateFormat: 'dd/mm/yy' });
		
		$("#txt_brand_id").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				
				if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				else if($("#txt_shop_name").val()=="")
				{
					$("#txt_shop_name").focus();
				}
				else if($("#txt_ip").val()=="")
				{
					$("#txt_ip").focus();
				}
				else if($("#txt_phone").val()=="")
				{
					$("#txt_phone").focus();
				}
				else if($("#txt_moblie").val()=="")
				{
					$("#txt_moblie").focus();
				}
				else if($("#txt_brand_id").val()=="")
				{
					$("#txt_brand_id").focus();
				}
				return false;
			 }
		});
		
		$("#txt_shop_id").change(function() {
			get_shop();
		});
		
		$("#txt_shop_id").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				get_shop();
				
				if($("#txt_shop_name").val()=="")
				{
					$("#txt_shop_name").focus();
				}
				else if($("#txt_ip").val()=="")
				{
					$("#txt_ip").focus();
				}
				else if($("#txt_phone").val()=="")
				{
					$("#txt_phone").focus();
				}
				else if($("#txt_moblie").val()=="")
				{
					$("#txt_moblie").focus();
				}
				else if($("#txt_brand_id").val()=="")
				{
					$("#txt_brand_id").focus();
				}
				else if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				return false;
			 }
		});
		
		$("#txt_shop_name").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				if($("#txt_ip").val()=="")
				{
					$("#txt_ip").focus();
				}
				else if($("#txt_phone").val()=="")
				{
					$("#txt_phone").focus();
				}
				else if($("#txt_moblie").val()=="")
				{
					$("#txt_moblie").focus();
				}
				else if($("#txt_brand_id").val()=="")
				{
					$("#txt_brand_id").focus();
				}
				else if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				else if($("#txt_shop_name").val()=="")
				{
					$("#txt_shop_name").focus();
				}
				return false;
			 }
		});
		
		$("#txt_ip").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				if($("#txt_phone").val()=="")
				{
					$("#txt_phone").focus();
				}
				else if($("#txt_moblie").val()=="")
				{
					$("#txt_moblie").focus();
				}
				else if($("#txt_brand_id").val()=="")
				{
					$("#txt_brand_id").focus();
				}
				else if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				else if($("#txt_shop_name").val()=="")
				{
					$("#txt_shop_name").focus();
				}
				else if($("#txt_ip").val()=="")
				{
					$("#txt_ip").focus();
				}
				return false;
			 }
		});
		
		$("#txt_phone").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				if($("#txt_moblie").val()=="")
				{
					$("#txt_moblie").focus();
				}
				else if($("#txt_brand_id").val()=="")
				{
					$("#txt_brand_id").focus();
				}
				else if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				else if($("#txt_shop_name").val()=="")
				{
					$("#txt_shop_name").focus();
				}
				else if($("#txt_ip").val()=="")
				{
					$("#txt_ip").focus();
				}
				else if($("#txt_phone").val()=="")
				{
					$("#txt_phone").focus();
				}
				return false;
			 }
		});
		
		$("#txt_moblie").on('keypress', function (event) {
			if(event.keyCode  == '13'){
				if($("#txt_brand_id").val()=="")
				{
					$("#txt_brand_id").focus();
				}
				else if($("#txt_shop_id").val()=="")
				{
					$("#txt_shop_id").focus();
				}
				else if($("#txt_shop_name").val()=="")
				{
					$("#txt_shop_name").focus();
				}
				else if($("#txt_ip").val()=="")
				{
					$("#txt_ip").focus();
				}
				else if($("#txt_phone").val()=="")
				{
					$("#txt_phone").focus();
				}
				else if($("#txt_moblie").val()=="")
				{
					$("#txt_moblie").focus();
				}
				return false;
			 }
		});
		
	});
	function checkFill()
	{
		//alert("Check");
		if($("#txt_brand_id").val()!="" && $("#txt_shop_id").val()!="" && $("#txt_shop_name").val()!="" && $("#txt_ip").val()!="" && $("#txt_phone").val()!="" && $("#txt_moblie").val()!="" && $("#txt_date").val()!="")
		{
			return true;
		}
		else if($("#txt_brand_id").val()=="")
		{
			alert("กรุณากรอกข้อมูลให้ครบ");
			$("#txt_brand_id").focus();
			return false;
		}
		else if($("#txt_shop_id").val()=="")
		{
			alert("กรุณากรอกข้อมูลให้ครบ");
			$("#txt_shop_id").focus();
			return false;
		}
		else if($("#txt_shop_name").val()=="")
		{
			alert("กรุณากรอกข้อมูลให้ครบ");
			$("#txt_shop_name").focus();
			return false;
		}
		else if($("#txt_ip").val()=="")
		{
			alert("กรุณากรอกข้อมูลให้ครบ");
			$("#txt_ip").focus();
			return false;
		}
		else if($("#txt_phone").val()=="")
		{
			alert("กรุณากรอกข้อมูลให้ครบ");
			$("#txt_phone").focus();
			return false;
		}
		else if($("#txt_moblie").val()=="")
		{
			alert("กรุณากรอกข้อมูลให้ครบ");
			$("#txt_moblie").focus();
			return false;
		}
		else
		{
			return false;
		}
	}
	
	function get_shop()
	{
		var txt_shop_id = $("#txt_shop_id").val();
		$.ajax({
		  type: "POST",
		  url: "search.php",
		  data: { txt_shop_id: txt_shop_id }, 
		  success: function(msg){				  	
			//alert("//"+msg);
			if(msg!="nodata")
			{
				var query=msg.split("-next-");
				$("#txt_shop_name").val(query[0]);
				$("#txt_ip").val(query[1]);
				$("#txt_phone").val(query[2]);
				$("#txt_moblie").val(query[3]);
				$("#txt_date").val(query[4]);
				$("#type").val("edit");
				$("#btn_submit").val("แก้ไข");				
			}
			else
			{
				var currentDate = new Date();
				var date = currentDate.getDate();
				var month = currentDate.getMonth()+1;
				var year = currentDate.getFullYear();
				var ddate = (date < 10 ? "0" : "") + date + "/" + (month < 10 ? "0" : "") + month + "/" + year;
				
				$("#txt_shop_name").val("");
				$("#txt_ip").val("");
				$("#txt_phone").val("");
				$("#txt_moblie").val("");
				$("#txt_date").val(ddate);
				$("#type").val("add");
				$("#btn_submit").val("บันทึก");
				
				
			}
		  }
		});
	}
</script>