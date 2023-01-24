<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-theme.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery-confirm.css">
<script src="<?php echo base_url();?>js/jquery-2.1.1.min.js"></script>
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>js/jquery-confirm.js"></script>
<APPLET CODE = 'idcard.RdNationalCardID' archive='/finger_shop/RdNIDApplet.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>
<script type="text/javascript">
$(document).ready(function(){
	$("#reload").hide();
	$("#detail").html('<h2 style="text-align:center;color:#CCC; font-size:18px;">กรุณาเสียบบัตรประชาชนเพื่อยืนยันตัวตน</h2>');
	$("#txt_unlock").focus();
	$("#scan_idcard").click(function (){
		if($("#txt_unlock").val() == ""){
			$.alert({
				title: 'แจ้งเตือน',
				icon: 'glyphicon glyphicon-lock',
				confirmButton: 'ตกลง',
				content: 'กรุณาใส่รหัสเพื่อปลดล็อก',
				confirm: function(){
					$("#txt_unlock").focus();
				}
			});
		}else{
			readIDCARD()
		}
	});
});

//readIDCARD();
function readIDCARD(){
	try{
		if(!document.NIDApplet.isCardInsertRD()){
			console.log("scan again!");
			$.alert({
				title: 'แจ้งเตือน',
				icon: 'glyphicon glyphicon-warning-sign',
				confirmButton: 'ตกลง',
				content: 'ไม่สามารถอ่านหมายเลขบัตรประชาชนได้',
				confirm: function(){
					$("#txt_unlock").focus();
				}
			});
			
		
		}else{
			$("#reload").show();
			var id_card = document.NIDApplet.getNIDNumberRD();
			var unlock_key = $("#txt_unlock").val();
				$.ajax({
				  type: "POST",
				  url: "<?php echo base_url();?>index.php/idcard/compare_idcard",
				  data:{id_card:id_card,unlock_key:unlock_key},
				  dataType:"json"
				})
				  .success(function(msg) { 
					if(msg.user_id != "000000" && msg.status != 'N'){
						$("#id_txt_unlock").hide();
						$("#id_txt_scan_idcard").hide();
						$("#reload").hide();
						$("#detail").html('<h3 style="text-align:center;color:#0000FF; font-size:18px;">รหัสพนักงาน '+msg.user_id+'</h3>');
					}else{
						$("#reload").hide();
						$.alert({
							title: 'แจ้งเตือน',
							icon: 'glyphicon glyphicon-warning-sign',
							confirmButton: 'ตกลง',
							content: msg.msg,
							confirm: function(){
								$("#txt_unlock").focus();
							}
						});
						$("#detail").html('<h3 style="text-align:center;color:#FF0000; font-size:18px;">ไม่พบรหัสพนักงาน</h3>');
					}
				});
		
		}
	}catch(e){
		$("#detail").html('<h3 style="text-align:center;color:#FF0000; font-size:18px;">ไม่สามารถอ่านหมายเลขบัตรประชาชนได้<br>กรุณาลองใหม่</h3>');	
		//setTimeout(function() {
		//	readIDCARD();
		//},1000);
	}	
}
</script>
<style>
	#body{ margin:auto; width:500px; height:500px;}
	.col-xs-6 {
    	text-align: center;
    	width: 50%;
	}
	#reload{ position:relative;left: 142px;top: -211px;}
</style>
</head>
<body>
	<div class="container" id="body" >
    	<!--<p><img style="float:right;" src="<?php echo base_url();?>images/ssup-logo.jpg" width="100px;"></p>-->
    	<div class="col-xs-12">
	    	<p style="margin:auto;width:322px; clear:both;">
	        <h2 style="text-align:center">ระบบสแกนบัตรประชาชน</h2>
	        </p>
	        <p style="margin:auto;width:195px; clear:both;">
	        <img style="margin:auto" src="<?php echo base_url();?>images/idcard.jpg" width="160" heigh="164"  /> 
	        </p>
	        <div class="col-xs-12" id="id_txt_unlock">
	        	<input class="form-control" type="text" name="txt_unlock" id="txt_unlock" value="" autocomplete="off">
	        </div>
	        <div class="col-xs-12" id="id_txt_scan_idcard">
	        	<button type="button" id="scan_idcard" class="btn btn-primary btn-lg btn-block">คลิกเพื่อสแกนบัตรประชาชน</button>
	        </div>
	        
	        <div class="col-xs-12" id="detail">
	       		
			</div>
		<div id="reload"><img src="<?php echo base_url();?>images/loading.gif"></div>
	    </div>
    </div>
    
</body>
</html>
