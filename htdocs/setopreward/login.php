<?php 
	session_start(); 
	if(!empty($_SESSION["u_id"]))
	{
		echo "<script>window.location = 'index.php'</script>";
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>

<link href="css/login.css" rel="stylesheet">
<link href="css/bootstrap.css" rel="stylesheet">
</head>
<body>

<div class="container-middle">
	<div class="content-middle">
		<div class="middle">
			<img src="img/user-icon.png" width="128px"/></br>
			<input type="text" name="txt_username" id="txt_username" placeholder="Username"/></br>
			<input type="password" name="txt_password" id="txt_password" placeholder="Password"/></br>
			<button class="btn btn-info" name="btn_login" id="btn_login" onClick="check_user()" ><i class='icon-white icon-user'></i> Login</button>
		</div>
	</div>
</div>

<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script>
	window.onload=function(){
		$("#txt_username").focus();					
	};
	
	function check_user()
	{
		var txt_username = $("#txt_username").val();
		var txt_password = $("#txt_password").val();
		
		if(txt_username != "" && txt_password != "")
		{
			$.ajax({
				type: "POST",
			  	url: "ch_login.php",
			  	data: { txt_username: txt_username , txt_password : txt_password }, 
			  	success: function(msg)
				{
				  	if(msg == "norecord")
				  	{
					  	alert("username หรือ password ไม่ถูกต้อง");
					  	$("#txt_username").focus();	
					  	$("#txt_username").val("");	
					  	$("#txt_password").val("")
				  	}
					else if(msg == "admin")
					{
						window.location = "admin/index.php";
					}
				  	else
				  	{
					  	window.location = "index.php";
				  	}
				  
				}
			});
		}
	}
	
	$("#txt_username").keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13')
		{			
			if($("#txt_password").val()=="")
			{
				$("#txt_password").focus();
			}
			else
			{
				check_user();
			}
		}
	});	
	
	$("#txt_password").keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13')
		{
			if($("#txt_username").val()=="")
			{
				$("#txt_username").focus();
			}
			else
			{
				check_user();
			}
		}
	});	
</script>
</body>
</html>
