<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="js/jquery-2.1.1.min.js"></script>
</head>
<script>
	$(window).load("api.php",function(response,status,xhr){
		if(status=="success"){
				document.write(status);
				document.write(response);
		}else{
				reloadapi();
		}
	});
	
	function reloadapi(){
		$(window).load("api.php",function(response,status,xhr){
		if(status=="success"){
				document.write(status);
				document.write(response);
		}else{
				reloadapi();
		}
		});
	}
</script>
<body>
</body>
</html>
