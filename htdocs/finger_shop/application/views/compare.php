<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-theme.min.css">
<script src="<?php echo base_url();?>js/jquery-2.1.1.min.js"></script>
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<script>
	var rnd=0; 
	var cinterval="";
	call_socket_compare();
	function call_socket_compare(){
		clearInterval(cinterval);
			//if(rnd < 4){
			console.log(rnd);
			$("#download").addClass('loading2');
				$.ajax({
					  type: "POST",
					  url: "<?php echo base_url();?>index.php/compare/socket",
					  data:{user_id:""},
					  dataType:"text"
					})
					  .success(function(msg) { 
						if(msg == 'success'){
							step(rnd);
							rnd = rnd + 1;
							cinterval = setInterval('reloadcompare()',1000);
						}else{
							ck_hardware();
						}
				$("#download").removeClass('loading2');			
						
				});
				
			//}else{
				//clearInterval(cinterval);
				//rnd = 0;
			//}
	}
	
	
	
	function ck_hardware(){
		$.ajax({
					  type: "POST",
					  url: "<?php echo base_url();?>index.php/compare/ckhardware",
					  data:{user_id:""},
					  dataType:"text"
					})
					  .success(function(msg) { 
					$("#body").html(msg);
						
			});
	}
	
	function step(level){
		$.ajax({
					  type: "POST",
					  url: "<?php echo base_url();?>index.php/compare/step",
					  data:{level:level},
					  dataType:"text"
					})
					  .success(function(msg) { 
					$("#body").html(msg);
						
			});
	}
	
	
	function reloadcompare(){
		
		$(window).load("<?php echo base_url();?>index.php/compare/get_user_id",function(response,status,xhr){
		if(status=="success"){
			var json =JSON.parse(response);
			//alert(json.num);
			if(json.status != ""){
				if(json.status == "y"){
					$("#sound").html('<audio autoplay><source src="/finger_shop/beep.wav"></audio>');
					clearInterval(cinterval);
					$("#body").html('<h1>รหัสพนักงาน : '+json.userid+'</h1>');			
				}else{
					$("#sound").html('<audio autoplay><source src="/finger_shop/beep.wav"></audio>');
					call_socket_compare();	
				}
			}
		}
		});
	}

	function clear_finger(){
		$.ajax({
					  type: "POST",
					  url: "<?php echo base_url();?>index.php/compare/clear_finger",
					  data:{level:""},
					  dataType:"text"
					})
					  .success(function(msg) { 
						console.log("clear finger");
						
			});
	}
	
</script>

<style>
	#body{ margin:auto; width:500px;overflow:hidden;}
	.col-xs-6 {
    text-align: center;
    width: 50%;
}

.loading2{
		background:#000 url(<?php echo base_url();?>css/loading.gif) no-repeat center center;
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
	<div class="container" id="body">
    	
    </div>
  	<div id="sound"></div>  
</body>
</html>
