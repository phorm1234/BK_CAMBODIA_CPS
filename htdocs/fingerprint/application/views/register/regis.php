<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Insert title here</title>

<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-theme.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/style.css">

<script src="<?php echo base_url();?>js/jquery-2.1.1.min.js"></script>
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<script>

	$( document ).ready(function() {
    	$("#user_id").focus();
	});

	var recive_interval="";
	function check_event(event){
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		keyCode = event.which; // Firefox   
		if (keyCode == 13) {
			check_user();
		}
	}
	
	function check_user(){
		var user_id = $("#user_id").val();
		$("#finger_l_status").val("");
		$("#finger_r_status").val("");
		if(user_id == ""){
			alert("กรุณาใส่รหัสพนักงาน");
		}else{
			$.ajax({
			  type: "POST",
			  url: "<?php echo base_url()?>index.php/register/checkuserid",
			  data:{user_id:user_id},
			  dataType:"json",
			})
			 .success(function( msg ) { 
			 	
				if(msg.status == "y"){
					$("#h_user_id").val(user_id);
					$("#name").val(msg.name);
					$("#surname").val(msg.surname);
					//call_socket(user_id);
				}else if(msg.status == "n"){
					$("#h_user_id").val("");
					alert("ไม่มีรหัสพนักงาน");
				}else if(msg.status == 'd'){
					//$("#h_user_id").val("");
					//alert("ได้บันทึกรหัสพนักงานคนนี้ไปแล้ว");
					$("#h_user_id").val(user_id);
					$("#name").val(msg.name);
					$("#surname").val(msg.surname);
					$("#"+msg.right).addClass('finger-active');
					$("#"+msg.left).addClass('finger-active');
						/*window.addEventListener("DOMContentLoaded", function() {
						var imgs= "profile/"+user_id+".png";
						var canvas = document.getElementById("canvas");
						context = canvas.getContext("2d");
						
						//alert(imgs);
						canvas.drawImage(imgs, 230, 180);
						}, false);*/
					var imgs= "/FingerScanTemplate/"+user_id+".png";
					$("#showimg").html('<img src="'+imgs+'"><h5 style="text-align:center">Picture Profile</h5>');
				}

				});
			  
		}
	}
	
	
	function call_socket(user_id){
	// E = Save;
		clearInterval(recive_interval);
		var save_userid = "E-"+user_id;
		var fingerid =$("#finger_status").val();
		$.ajax({
				  type: "POST",
				  url: "<?php echo base_url();?>index.php/socket/index",
				  data:{user_id:save_userid,fingerid:fingerid},
				  dataType:"text"
				})
				  .success(function(msg) { 
					if(msg == 1){
					recieve_socket(user_id);
					recive_interval = setInterval(recieve_socket,1000);
					//clearInterval(recive_interval);
					}else{
						alert("ไม่สามารถเชื่อมต่อเครื่องสแกนลายนิ้วมือได้");
					}
					
		});
	}
	
	function recieve_socket(user_id){
		var h_user_id = $("#h_user_id").val();
		var fingerid =$("#finger_status").val();
		var finl_status = $("#finger_l_status").val();
		var finr_status = $("#finger_r_status").val();
		 if(finl_status == "success" && finr_status=="success"){
			clearInterval(recive_interval);
		 }
		$.ajax({
				  type: "POST",
				  url: "<?php echo base_url();?>index.php/socket/recieve",
				  data:{user_id:h_user_id,fingerid:fingerid},
				  dataType:"json"
				})
				  .success(function( msg ) {

						if(msg.type == "L"){
							$("#status_l").html(msg.num);
							$("#percen_l").css("width",msg.percen+"%");
							$("#percen_l").html(msg.percen+"%");
							if(msg.final=="final"){
								$("#finger_l_status").val("success");
							}
					   }else if(msg.type == "R"){
						   $("#status_r").html(msg.num);
							$("#percen_r").css("width",msg.percen+"%");
							$("#percen_r").html(msg.percen+"%");
							if(msg.final=="final"){
								$("#finger_r_status").val("success");
							}
					   }
					   setTimeout(function() {
						     if(finl_status == "success" && finr_status=="success"){
								clearInterval(recive_interval);
								alert("บันทึกข้อมูลสำเร็จ");
								$('.beginR,.beginL,.pgL,.pgR').css('opacity','0');
								$("#user_id").val("");
								$("#h_user_id").val("");
								var clearfg =$("#finger_status").val();
								$("#"+clearfg).removeClass('finger-active');
								$("#finger_status").val("");
								$("#name").val("");
								$("#surname").val("");
								$("#finger_l_status").val("");
								$("#finger_r_status").val("");
								$("#status_l").html("เริ่มสแกน");
								$("#percen_r").css("width","0%");
								$("#percen_r").html("0%");
								$("#percen_l").css("width","0%");
								$("#percen_l").html("0%");
								var canvas = $('#canvas')[0]; // or document.getElementById('canvas');
								canvas.width = canvas.width;
								$("#showimg").html('');
								$("#user_id").focus();
					   			}
							},1000);
					  
		});
}
	
</script>

</head>
<body>
<div class="container">
	<div class="row header">
		<div class="col-md-6 logo">
			<div class="col-sm-3"><img src="<?php echo base_url();?>images/ssup-logo.jpg"></div>
			<div class="col-sm-9">
				<div class="logo-txt1">Empoyee Register</div>
				<div class="logo-txt2">with Finger Scan</div>
			</div>
		</div>
		<div class="col-md-6 version">version : 1.0</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="row txt-row">EID : <input id="user_id" class="txt-box" name="user_id" type="text" onKeyPress="return check_event(event)"; ></div>
			<div class="row">
            	<div class="photo">
                <video id="video" width="230" height="180" autoplay></video>
					<button type="button" class="btn btn-large btn-block btn-primary" id="snap">Snap Photo</button>
					<span id="showimg" style="align:center"></span>
					<canvas id="canvas" width="230" height="180"></canvas>
					
                    <script>
					//======================================== webcam
			// Put event listeners into place
			window.addEventListener("DOMContentLoaded", function() {
				// Grab elements, create settings, etc.
				var canvas = document.getElementById("canvas"),
					context = canvas.getContext("2d"),
					video = document.getElementById("video"),
					videoObj = { "video": true },
					errBack = function(error) {
						console.log("Video capture error: ", error.code); 
					};
			
				// Put video listeners into place
				if(navigator.getUserMedia) { // Standard
					navigator.getUserMedia(videoObj, function(stream) {
						video.src = stream;
						video.play();
					}, errBack);
				} else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
					navigator.webkitGetUserMedia(videoObj, function(stream){
						video.src = window.webkitURL.createObjectURL(stream);
						video.play();
					}, errBack);
				}
				else if(navigator.mozGetUserMedia) { // Firefox-prefixed
					navigator.mozGetUserMedia(videoObj, function(stream){
						video.src = window.URL.createObjectURL(stream);
						video.play();
					}, errBack);
					//mozSrcObject
				}
				
	// Trigger photo take
			document.getElementById("snap").addEventListener("click", function() {
				var h_user_id = $("#h_user_id").val();
				$("#showimg").html('');
				if(h_user_id != ""){
					context.drawImage(video, 0, 0, 230, 180);
	
					var canvasData = canvas.toDataURL("image/png");
					var user_id = $("#user_id").val();
					$.ajax({
					  type: "POST",
					  url: "/saveimg.php",
					  data: { 
						 imgData: canvasData,user_id:user_id
					  }
					}).done(function(o) {
					  console.log('saved'); 
					  // If you want the file to be visible in the browser 
					  // - please modify the callback in javascript. All you
					  // need is to return the url to the file, you just saved 
					  // and than put the image in your browser.
					});
				}else{
					alert("ยังไม่ได้กรอกรหัสพนักงาน");
				}
			});
			
}, false);
					
					</script>
                </div>
            </div>
			<!--<div class="row">
				<div class="col-sm-6 text-right"><div class="btn btn-success">Confirm</div></div>
				<div class="col-sm-6"><div class="btn btn-warning">Retake</div></div>
			</div>-->
		</div>
		<div class="col-md-6">
		<div class="row well margin">
			<div class="row margin">
				<div class="txt-front text-right left">Name : </div> 
				<div class="left"><input id="name" class="txt-box-1" name="name" type="text"></div>
			</div>
			<div class="row margin">
				<div class="txt-front text-right left">Surname : </div> 
				<div class="left"><input id="surname" class="txt-box-1" name="surname" type="text"></div>
			</div>
            <input type="hidden" id="h_user_id" value="" />
            <input type="hidden" id="checkstatsu" value="" />
            <input type="hidden" id="finger_status" value=""/>
            <input type="hidden" id="finger_r_status" value=""/>
            <input type="hidden" id="finger_l_status" value=""/>
		</div>
		<div class="row">
			<div class="col-md-6">
			<div class="hand">
				<img src="<?php echo base_url();?>images/HandL.png" />
				<a><div id="finL1" class="finger img-circle"></div></a>
				<a><div id="finL2" class="finger img-circle"></div></a>
				<a><div id="finL3" class="finger img-circle"></div></a>
				<a><div id="finL4" class="finger img-circle"></div></a>
				<a><div id="finL5" class="finger img-circle"></div></a>
			</div>
			<div id="status_l" class="row beginL" style="width: 80%;">เริ่มสแกน</div>
			<div class="progress pgL" style="margin-top: 50px; width: 80%;">
			  <div id="percen_l" class="progress-bar progress-bar-striped active text-center" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width:;">
			    0%
			  </div>
			</div>
			
			</div>
			
			<div class="col-md-6">
			<div class="hand">
				<img src="<?php echo base_url();?>images/HandR.png" />
				<a><div id="finR1" class="finger img-circle"></div></a>
				<a><div id="finR2" class="finger img-circle"></div></a>
				<a><div id="finR3" class="finger img-circle"></div></a>
				<a><div id="finR4" class="finger img-circle"></div></a>
				<a><div id="finR5" class="finger img-circle"></div></a>
			</div>
			<div id="status_r" class="row beginR pull-right" style="width: 80%;">เริ่มสแกน</div>
			<div class="progress pgR pull-right" style="margin-top: 50px; width: 80%;">
			  <div id="percen_r" class="progress-bar progress-bar-striped active text-center" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width:;">
			    0%
			  </div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
		$('.finger').click(function(){
			
			var h_user_id = $("#h_user_id").val();
			if(h_user_id != ""){
				//$('.beginR,.beginL,.pgL,.pgR').css('opacity','1');
				var text=  $(this).attr("id");
				if(text == "finL1" || text == "finL2" || text == "finL3" || text == "finL4" || text == "finL5"){
					$('.beginL,.pgL,.pgR').css('opacity','1');
					
				}else if(text == "finR1" || text == "finR2" || text == "finR3" || text == "finR4" || text == "finR5"){
					$('.beginR,.pgL,.pgR').css('opacity','1');
					//$("#percen_r").css("width","50%");
				} 
					$('.finger').each(function(){
						$(this).removeClass('finger-active');
					});
		
				$(this).addClass('finger-active');
				$("#finger_status").val(text);
				call_socket(h_user_id);
			}else{
				alert("กรุณาใส่รหัสพนักงานก่อนค่ะ");
			}
		});

	

	$("#user_id").keyup(function(){
         if($(this).val().length == 6){
           check_user();
		 }
    });



</script>
</body>
</html>
