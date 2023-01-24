<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Insert title here</title>

<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-theme.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/style.css">
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.js"></script>

<script>
	$(document).ready(function(){
		$( "#login_frm" ).hide();
		$( "#fingerscan" ).html('<iframe src="/finger_shop/index.php/compare/" width="500" height="350">');
	});



	var cinterval="";
	function reloadcompare(){
		$(window).load("/fingerprint/index.php/login/getuserid",function(response,status,xhr){
		if(status=="success"){
			var json =JSON.parse(response);
			//alert(json.num);
			if(json.status != ""){
				if(json.status == "y"){
					clearInterval(cinterval);
					$("#user_id").val(json.userid);
					$("#password").val(json.password);
					$("#btn_login_submit").trigger('click');
				}else if(json.status == "sk"){
					$( "#fingerscan" ).html('<iframe src="/finger_shop/index.php/idcard/" width="490" height="350">');
				}else if(json.status == "f"){
					alert("คุณไม่มีสิทธิ์เข้าใช้ระบบ");
					$( "#fingerscan" ).html('<iframe src="/finger_shop/index.php/compare/" width="490" height="350">');
				}
			}
		}
		});
	}
	cinterval = setInterval('reloadcompare()',1000);
	
</script>
</head>
<body>
<div class="container">
	<div class="row header text-right">
		version : 1.0
	</div>
	<div class="row header logo">		
			<div class="col-xs-9">
				<div class="logo-txt1">Employee Register</div>
				<div class="logo-txt2">with Finger Scan</div>
			</div>
			<div class="col-xs-3 text-right"><img src="<?php echo base_url();?>images/ssup-logo.jpg"></div>		
	</div>
	<div class="login-box" id="fingerscan"></div>
	<div class="login-box" id="login_frm">
		<div class="row img-login" style="color:#ccc;text-align:center"><h1>Log On</h1></div>
		<div class="row margin">
		<form name="" method="post" action="<?php echo base_url();?>index.php/login/index">
			<div class="row margin">
				<div class="txt-front-login text-right left">User ID : </div> 
				<div class="left"><input class="txt-box-3" name="username" id="user_id" type="text"></div>
			</div>
			<div class="row margin">
				<div class="txt-front-login text-right left">Password : </div> 
				<div class="left"><input class="txt-box-3" type="password" name="password" type="text"></div>
			</div>
			
			<div class="row text-center">
            <input id="btn_login_submit" class="img-circle submit-login" type="submit" value="Login" >
             <span style="color:#FF0000"><br><?php echo $error;?></span>
</div>
		</form>
		</div>
	</div>
</div>
</body>
</html>
