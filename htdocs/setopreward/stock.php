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
<title>STOCK</title>

<link href="css/custom.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery-ui.css" rel="stylesheet"/>
</head>

<body>
<?php require("funcDB.php"); ?>
<h4>เพิ่มสินค้าในสต๊อก</h4>
<div class="magin-top-20"></div>
<center>

<form id="reward_submit" method="post" accept-charset="UTF-8">
    <table width="400">
		<tr>
			<td colspan="2" style="text-align:right;"><strong>Status : </strong><lebel id="status_id">เพิ่มข้อมูลใหม่</lebel></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">ปี</td>
			<td>
				<input type="text" name="stk_year" id="stk_year" placeholder="ปี" maxlength="13"/>
			</td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">เดือน</td>
			<td>
				<input type="text" name="stk_month" id="stk_month" placeholder="เดือน" maxlength="13"/>
			</td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:15px;">รหัสสินค้า</td>
			<td>
				<input type="text" name="stk_product_id" id="stk_product_id" placeholder="รหัสสินค้า" maxlength="13"/>
			</td>
		</tr>		
		<tr>
			<td colspan="2" style="text-align:center;">
				<input type="button" class="btn btn-info" id="btn_save" value="บันทึกข้อมูล">
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
		$('#btn_save').click(function(e){
			e.preventDefault();
			var stk_year=$('#stk_year').val();
			var stk_month=$('#stk_month').val();
			var stk_product_id=$('#stk_product_id').val();

			if(stk_year.lenth==0){
				alert("กรุณาระบุปี");
				$('#stk_year').focus();
				return false;
			}

			if(stk_month.lenth==0){
				alert("กรุณาระบุเดือน");
				$('#stk_month').focus();
				return false;
			}

			if(stk_product_id.lenth==0){
				alert("กรุณาระบุรหัสสินค้า");
				$('#stk_product_id').focus();
				return false;
			}
			
			
			$.ajax({
    			type:'post',
    			url:'add_stock.php',
    			data:{
    				year:stk_year,
    				month:stk_month,
    				product_id:stk_product_id,
    				rnd:Math.random()
    			},success:function(data){
    				var arr_data=data.split('#');
    				if(arr_data[0]=='Y'){
    					//is used
    					alert('บันทึก ' + stk_product_id + ' สมบูรณ์');
    					$('#stk_product_id').val('').focus();
    					return false;
    				}else{
    					// is available
    					alert('ไม่สามารถบันทึก ' + stk_product_id + ' ได้กรณาตรวจสอบอีกครั้ง');
    					$('#stk_product_id').focus();
    					return false;
    				}//end if
    			}
    		});
		});

		$('#stk_product_id').keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	        	evt.preventDefault();
    			evt.stopPropagation();
    			$('#btn_save').trigger('click');
		        return false;
	        }
		});
		
	});
</script>
</body>
</html>
