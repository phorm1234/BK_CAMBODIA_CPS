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
	
</head>

<body>
<?php 
	if(array_key_exists('login', $_GET) && empty($_SESSION["login"]))
	{
		$e_id = $_GET["login"];
		if($e_id == md5('is-helpdesk'))
		{
				$_SESSION["login"] = "is-helpdesk";
				$_SESSION["name"] = "Demo Demo";
				echo "<script>window.location = 'index.php';</script>";
			
		}	
		else
		{
			echo "<script>alert('ไม่พบข้อมูล');</script>";
		}
	}
	else if(!empty($_SESSION["login"]))
	{
		echo "<script>window.location = 'index.php';</script>";
	}
?>	
<div class="container-middle">
	<div class="content-middle">
		<div class="middle">
				<input type="password" name="txt_pass" id="txt_pass" placeholder="Password" class="form-control" />
				<input type="button" name="btn_login" id="btn_login" value="OK" onclick="checkPass()" class="btn btn-primary"/>
				<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" class="btn btn-default" onclick="closeOpenedWindow()"/>
				<input type="hidden" name="checkFail" id="checkFail" value="0" />
				<input type="hidden" name="md5user" id="md5user" value="<?php echo md5('is-helpdesk')?>">
		</div>
	</div>
</div>	
</body>
</html>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script>
	$(function(){
		$("#txt_pass").focus();
	});
	
	
	$("#txt_pass").on('keypress', function (event) {
		if(event.keyCode  == '13'){
			if($("#txt_pass").val()!="")
			{
				checkPass();
			}
         }
	});
	
	function checkPass()
	{
		var d = new Date();		
		var day = d.getDate();
		var month = d.getMonth()+1;
		var year = d.getFullYear();
		
		var real_pass = day + month + year;		
		real_pass = (real_pass*9)/7;
		real_pass = parseInt(real_pass);
		
		var txt_pass = $("#txt_pass").val();
		var tmp_chkF = parseInt($("#checkFail").val())+1;
		
		if(tmp_chkF > 3)
		{
			alert("คุณ Login ผิดพลาดเกิน 3 ครั้งแล้ว");
			$("#txt_pass").val("");
			$("#txt_pass").focusout();
			$("#txt_pass").attr("disabled", "disabled");
			closeOpenedWindow();
			return false;
		}		
		
		if(txt_pass != real_pass)
		{
			alert("รหัสไม่ถูกต้อง");
			
			$("#checkFail").val(tmp_chkF);
			$("#txt_uname").val("");
			$("#txt_pass").val("");
			$("#txt_uname").focus();		
			
		}	
		else
		{
			var md5 = $("#md5user").val();
			window.location = "login.php?login="+md5;
		}	
	}	
	
	var openedWindow;

	function closeOpenedWindow()
	{
		$("#txt_pass").val("");
			$("#txt_pass").focusout();
		window.close();
    
	}
	</script>