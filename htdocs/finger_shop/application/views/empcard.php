<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-theme.min.css">
<script src="<?php echo base_url();?>js/jquery-2.1.1.min.js"></script>
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<APPLET CODE = 'idcard.RdNationalCardID' archive='/finger_shop/RdNIDApplet.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>
<script type="text/javascript">
$(document).ready(function(){
	//$("#detail").html('<h2 style="text-align:center;color:#CCC; font-size:18px;">กรุณาสแกนบัตรพนักงาน</h2>');
	$("#empcard").focus();
});

//readIDCARD();
function readIDCARD(){
	try{
		
			var id_card = document.NIDApplet.getNIDNumberRD();
				$.ajax({
				  type: "POST",
				  url: "<?php echo base_url();?>index.php/idcard/compare_idcard",
				  data:{id_card:id_card},
				  dataType:"json"
				})
				  .success(function(msg) { 
					if(msg.user_id != "000000"){
						$("#detail").html('<h3 style="text-align:center;color:#0000FF; font-size:18px;">รหัสพนักงาน '+msg.user_id+'</h3>');
					}else{
						$("#detail").html('<h3 style="text-align:center;color:#FF0000; font-size:18px;">ไม่พบรหัสพนักงาน</h3>');
					}
				});
	}catch(e){
		$("#detail").html('<h3 style="text-align:center;color:#FF0000; font-size:18px;">ไม่สามารถอ่านหมายเลขบัตรประชาชนได้<br>กรุณาลองใหม่</h3>');	
		setTimeout(function() {
			readIDCARD();
		},1000);
	}	
}
</script>
<style>
	#body{ margin:auto; width:500px; height:500px;}
	.col-xs-6 {
    	text-align: center;
    	width: 50%;
	}
	#reload{ position:relative;left: 204px;top: -198px;}
</style>
</head>
<body>
	<div class="container" id="body" >
    	<p style="margin:auto;width:322px; clear:both;">
        <h2 style="text-align:center">สแกนรหัสพนักงาน</h2>
        	
        </p>
       <p style="margin:auto;width:160px; clear:both;">
        	<input style="height: 50px" type="text" name="empcard" id="empcard">
		</p>
    </div>
    
</body>
</html>
