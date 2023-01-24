<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Insert title here</title>
<script src="<?php echo base_url();?>js/jquery-2.1.1.min.js"></script>
<script>
	function call_socket_compare(){
			$.ajax({
					  type: "POST",
					  url: "<?php echo base_url();?>index.php/compare/socket",
					  data:{user_id:""},
					  dataType:"text"
					})
					  .success(function(msg) { 
						if(msg == 'success'){
							reloadcompare();
						}else{  // fail
							alert("ไม่สามารถเชื่อมต่อเครื่องสแกนลายนิ้วมือได้");
						}
						
			});
	}

	function reloadcompare(){
		$("#download").addClass('loading2');
		$(window).load("<?php echo base_url();?>index.php/compare/get_user_id",function(response,status,xhr){
		if(status=="success"){
			var json =JSON.parse(response);
			if(json.status != ""){
				if(json.status == "y"){
					$("#sound").html('<audio autoplay><source src="/finger_shop/beep.wav"></audio>');
					$("#profile").html("<h1>รหัสพนักงาน "+json.userid+"</h1>");		
				}else{
					$("#sound").html('<audio autoplay><source src="/finger_shop/beep.wav"></audio>');
					$("#profile").html("");
					alert("ไม่พบข้อมูลพนักงาน");
				}
				$("#download").removeClass('loading2');
			}else{
				reloadcompare();	
			}
		}else{
			reloadcompare();
		}
		});
	}

	
</script>
<style>
	.loading{
		background:#000 url(<?php echo base_url();?>css/loading.gif) no-repeat center center;
		height:100%;
		width:100%;
		position:fixed;
		z-index:1000;
		/*left:50%;
		top:50%;*/
		opacity:0.3;
		filter:alpha(opacity=30);
		
	}
	
	.loading2{
		background:#000 url(<?php echo base_url();?>css/finger.png) no-repeat center center;
		height:100%;
		width:100%;
		position:fixed;
		z-index:1000;
		/*left:50%;
		top:50%;*/
		opacity:0.6;
		filter:alpha(opacity=60);
		
	}
</style>
</head>
<body>
<div id="download"></div>
<div style="margin:auto;text-align: center;"><input onclick="call_socket_compare();" type="button" value="คลิกเพื่อสแกน"></div>
<div style="margin:auto;text-align: center;" id="profile"></div>
<div style="margin:auto" id="sound"></div>
</body>
</html>

