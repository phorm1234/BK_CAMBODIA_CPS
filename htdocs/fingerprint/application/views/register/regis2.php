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
<!-- <APPLET CODE = 'idcard.RdNationalCardID' archive='/RdNIDApplet.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0"> -->
</APPLET>  
<script>
	
	var cinterval="";
	var numSound = 0;
	var num_finger;
	$( document ).ready(function() {
    	$("#user_id").focus();
	});

	var recive_interval="";
	function check_event(event,type){
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		keyCode = event.which; // Firefox   
		if (keyCode == 13) {
			if(type == "user_id"){
				check_user();
			}else if(type == "id_card"){
				readIDCARD();
			}
		}
	}

	function readIDCARD(){
		try{
		if(!document.NIDApplet.isCardInsertRD()){
			alert("คุณยังไม่ได้ Scan บัตรประชาชน");
			$("#id_card").val("-");
			$("#id_card").select();
			return false;
		}else{
			var id_card = document.NIDApplet.getNIDNumberRD();
			$("#id_card").val(id_card);
		}
		}catch (e) {
			alert("ไม่สามารถอ่านข้อมูลบัตรประชาชนได้");
		}
	
	}
	
	function check_user(){
		//cleardata();
		var user_id = $("#user_id").val();
		$("#finger_l_status").val("");
		$("#finger_r_status").val("");
		
		if(user_id == ""){
			alert("กรุณาใส่รหัสพนักงาน");
		}else{
			$.ajax({
			  type: "POST",
			  url: "<?php echo base_url();?>index.php/register/checkuserid",
			  data:{user_id:user_id},
			  dataType:"json",
			})
			 .success(function( msg ) { 
			 	
				if(msg.status == "y"){
					max_time = 7;
					num_finger = 0;
					$("#countdown").html("นับถอยหลัง 7");	
					cinterval = setInterval('countdown_timer()',1000);
					$("#user_id").select();
					$("#h_user_id").val(user_id);
					$("#name").val(msg.name);
					$("#surname").val(msg.surname);
					readIDCARD();
				}else if(msg.status == "n"){
					clearInterval(cinterval);
					$("#h_user_id").val("");
					$("#user_id").select();
					alert("ไม่มีรหัสพนักงาน");
				}else if(msg.status == 'd'){
					$("#showimg").html('');
					$('#finL1,#finL2,#finL3,#finL4,#finL5,#finR1,#finR2,#finR3,#finR4,#finR5').removeClass('finger-active');
					$("#h_user_id").val(user_id);
					$("#user_id").select();
					$("#name").val(msg.name);
					$("#surname").val(msg.surname);
					$("#id_card").val(msg.id_card);
					$("#"+msg.right).addClass('finger-active');
					$("#"+msg.left).addClass('finger-active');
					var canvas = $('#canvas')[0]; // or document.getElementById('canvas');
					canvas.width = canvas.width;	
					var imgs= "/FingerScanTemplate/"+user_id+".png?dumy="+Math.random();
					$("#showimg").html('<img src="'+imgs+'"><h5 style="text-align:center">Picture Profile</h5>');
				}

				});
			  
		}
	}


	var max_time = 7;
	function countdown_timer(){
		max_time--;
		document.getElementById('countdown').innerHTML = "นับถอยหลัง "+max_time;
		if(max_time == 0){
			document.getElementById('countdown').innerHTML = "";
			//cinterval="";
			clearInterval(cinterval);
			$("#countdown").html("");
			$("#snap").trigger('click');
			$("#finR1").trigger('click');
		}
		if(max_time < 0){
			document.getElementById('countdown').innerHTML = "";	
			clearInterval(cinterval);
		}
	}

	
	
	
	function call_socket(user_id){
	// E = Save;
		numSound = 1;
		clearInterval(cinterval);
		clearInterval(recive_interval);
		$("#countdown").html("");
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
						//cinterval="";
						
						
						alert("ไม่สามารถเชื่อมต่อเครื่องสแกนลายนิ้วมือได้");
						cleardata();
					}
					
		});
		//clearInterval(cinterval);
	}

	function call_socket_compare(){
		// E = Save;
			clearInterval(cinterval);
			clearInterval(recive_interval);
			$.ajax({
					  type: "POST",
					  url: "<?php echo base_url();?>index.php/compare/socket",
					  data:{user_id:""},
					  dataType:"text"
					})
					  .success(function(msg) { 
						if(msg == 'success'){
							reloadcompare();
						}else{
							alert("ไม่สามารถเชื่อมต่อเครื่องสแกนลายนิ้วมือได้");
							cleardata();
						}
						
			});
	}

	function reloadcompare(){
		cleardata();
		$("#download").addClass('loading2');
		$(window).load("<?php echo base_url();?>index.php/compare/get_user_id",function(response,status,xhr){
		if(status=="success"){
			var json =JSON.parse(response);
			//alert(json.status);
			if(json.status != ""){
				if(json.status == "1"){
					$("#sound").html('<audio autoplay><source src="/beep.wav"></audio>');
					$("#user_id").val(json.userid);
					check_user();			
				}else{
					$("#sound").html('<audio autoplay><source src="/beep.wav"></audio>');
					alert("ไม่พบข้อมูลพนักงาน");
					cleardata();
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
	
	function recieve_socket(user_id){
		var h_user_id 	= $("#h_user_id").val();
		var fingerid 	=$("#finger_status").val();
		var finl_status = $("#finger_l_status").val();
		var finr_status = $("#finger_r_status").val();
		var id_card 	= $("#id_card").val();
		 if(finl_status == "success" && finr_status=="success"){
			clearInterval(recive_interval);
		 }
		$.ajax({
				  type: "POST",
				  url: "<?php echo base_url();?>index.php/socket/recieve",
				  data:{user_id:h_user_id,fingerid:fingerid,id_card:id_card},
				  dataType:"json"
				})
				  .success(function( msg ) {

						if(msg.type == "L"){
							$("#status_l").html(msg.num);
							$("#percen_l").css("width",msg.percen+"%");
							$("#percen_l").html(msg.percen+"%");
							if(numSound == msg.beep){
								$("#sound").html('<audio autoplay><source src="/beep.wav"></audio>');
								numSound++;
							}
							if(msg.final=="final"){
								
								$("#finger_l_status").val("success");
								console.log(num_finger);
								if(num_finger == 1){
									setTimeout(function() {
										clearInterval(recive_interval);
										$("#finger_l_status").val("");
										$("#finger_r_status").val("");
										$("#status_r").html("เริ่มสแกน");
										$("#percen_r").css("width","0%");
										$("#percen_r").html("0%");
										$("#percen_l").html("0%");
										$("#percen_l").css("width","0%");
										$("#finR2").trigger('click');
									},1000);
								}
								if(msg.beep == 5){
									if(msg.error == ""){
										num_finger++;
									}else{
										alert("การบันทึกไม่สมบูรณ์ กรุณาทำรายการใหม่");
										$("#"+msg.error).trigger('click');
										$("#status_r").html("เริ่มสแกน");
										$("#percen_r").css("width","0%");
										$("#percen_r").html("0%");
										$("#percen_l").html("0%");
										$("#percen_l").css("width","0%");
									}
								}
							}
					   }else if(msg.type == "R"){
						   $("#status_r").html(msg.num);
							$("#percen_r").css("width",msg.percen+"%");
							$("#percen_r").html(msg.percen+"%");
							if(numSound == msg.beep){
								$("#sound").html('<audio autoplay><source src="/beep.wav"></audio>');
								numSound++;
							}
							if(msg.final=="final"){
								$("#finger_r_status").val("success");
								console.log(num_finger);
								if(num_finger == 1){
									clearInterval(recive_interval);
									setTimeout(function() {
										$("#finger_l_status").val("");
										$("#finger_r_status").val("");
										$("#status_r").html("เริ่มสแกน");
										$("#percen_r").css("width","0%");
										$("#percen_r").html("0%");
										$("#percen_l").html("0%");
										$("#percen_l").css("width","0%");
										$("#finR2").trigger('click');
									},1000);
								}
								if(msg.beep == 5){
									if(msg.error == ""){
										num_finger++;
									}else{
										alert("การบันทึกไม่สมบูรณ์ กรุณาทำรายการใหม่");
										$("#"+msg.error).trigger('click');
										$("#status_r").html("เริ่มสแกน");
										$("#percen_r").css("width","0%");
										$("#percen_r").html("0%");
										$("#percen_l").html("0%");
										$("#percen_l").css("width","0%");
									}
								}
							}
					   }
					   setTimeout(function() {
						   //  if(finl_status == "success" && finr_status=="success"){
						   if(num_finger >= 3){
							   num_finger = 0;
							    //alert(num_finger);
								clearInterval(recive_interval);
								alert("บันทึกข้อมูลสำเร็จ");
								$('.beginR,.beginL,.pgL,.pgR,.pl_t_r,.pl_t_l').css('opacity','0');
								$("#user_id").val("");
								$("#h_user_id").val("");
								var clearfg =$("#finger_status").val();
								$("#"+clearfg).removeClass('finger-active');
								$("#finger_status").val("");
								$("#name").val("");
								$("#surname").val("");
								$("#finger_l_status").val("");
								$("#finger_r_status").val("");
								$("#status_r").html("เริ่มสแกน");
								$("#status_l").html("เริ่มสแกน");
								$("#percen_r").css("width","0%");
								$("#percen_r").html("0%");
								$("#percen_l").html("0%");
								$("#percen_l").css("width","0%");
								var canvas = $('#canvas')[0]; // or document.getElementById('canvas');
								canvas.width = canvas.width;
								$("#showimg").html('');
								$("#user_id").focus();
								$("#id_card").val("");
					   			}
							},1000);
					  
		});
}

	// syne server
function download_data(){
	cleardata();
	$("#download").addClass('loading');
	$(window).load("<?php echo base_url();?>index.php/transfer/download",function(response,status,xhr){
		//alert(response);
		if(response=="y"){
			alert("ถ่ายโอนข้อมูลสำเร็จ");
			$("#download").removeClass('loading');
			//location.reload();
		}else{
			alert("ถ่ายโอนข้อมูลไม่สำเร็จ");
			$("#download").removeClass('loading');
			//location.reload();
		}
	});
}

function upload(){
	cleardata();
	$("#download").addClass('loading');
	$(window).load("<?php echo base_url();?>index.php/transfer/upload",function(response,status,xhr){
		
		if(response=="y"){
			$("#download").removeClass('loading');
			alert(" ========= ถ่ายโอนข้อมูลสำเร็จ =========");
			//location.reload();
		}else{
			$("#download").removeClass('loading');
			alert("xxxxxxxxxx ถ่ายโอนข้อมูลไม่สำเร็จ xxxxxxxxxx");
			//location.reload();
		}
	});
}

	function cleardata(){
		//clearInterval(cinterval);
		$("#showimg").html('');
		$('.beginR,.beginL,.pgL,.pgR,.pl_t_r,.pl_t_l').css('opacity','0');
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
		$("#percen_l").html("0%");
		$("#percen_l").css("width","0%");
		var canvas = $('#canvas')[0]; // or document.getElementById('canvas');
		canvas.width = canvas.width;
		$("#showimg").html('');
		$("#user_id").focus();	
		$('#finL1,#finL2,#finL3,#finL4,#finL5,#finR1,#finR2,#finR3,#finR4,#finR5').removeClass('finger-active');
		$("#id_card").val("");
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
<div class="container">
	<div class="row header text-right">
		version : 1.0
	</div>
	<div class="row header logo">		
			<div class="col-xs-9">
				<div class="logo-txt1">Employee Register <b>ROS</b></div>
				<div class="logo-txt2">with Finger Scan</div>
			</div>
			<div class="col-xs-3 text-right"><img src="<?php echo base_url();?>images/ssup-logo.jpg"></div>		
	</div>
	<div class="row menu">
		<ul class="v_menu">
			<li class="a-active"><a href="<?php base_url()?>register">Register</a></li>
			<li><a href="#" onclick="call_socket_compare();">Test</a></li>
			<li><a href="#" onclick="download_data();">Download Data</a></li>
			<li><a href="#" onclick="upload();">Upload Data</a></li>
		</ul>
	</div>
	<div class="row contrainer">
		<div class="col-md-6">
			<div class="txt-front text-right left">
				Employee ID :&nbsp;
			</div>
			<div class="left">
				<input id="user_id" class="txt-box" name="user_id" type="text" onKeyPress="return check_event(event,'user_id')"; >
			</div>
			<div style="clear:both">&nbsp;</div>
			<div class="txt-front text-right left" style="clear:both;margin-top:5px;">Card ID :&nbsp;</div> 
			<div class="left">
			<input id="id_card" name="id_card" class="txt-box" placeholder="สแกนบัติประจำตัวประชาชน" type="text" onKeyPress="return check_event(event,'id_card')"; >
			</div>
			<div class="row" style="margin-top:30px;">
            	<div class="photo">
                <video id="video" width="230" height="180" autoplay></video>
                	<span id="countdown" style="text-aling:center;font-size:26px;"></span>
					<button type="button" class="btn btn-large btn-block btn-primary" id="snap">Take Photo</button>
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
			$("#showimg").html('');
			document.getElementById("snap").addEventListener("click", function() {
				var h_user_id = $("#h_user_id").val();
				$("#showimg").html('');
				if(h_user_id != ""){
					context.drawImage(video, 0, 0, 230, 180);
					//$("#sound").html('<audio autoplay><source src="/camera.wav"></audio>');
					var canvasData = canvas.toDataURL("image/png");
					var user_id = $("#user_id").val();
					$.ajax({
					  type: "POST",
					  url: "/fingerprint/saveimg.php",
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
					$("#user_id").select();
				}else{
					alert("ยังไม่ได้กรอกรหัสพนักงาน");
				}
			});

			
}, false);//$(this).removeClass('finger-active');

					</script>
                </div>
            </div>
			<!--<div class="row">
				<div class="col-sm-6 text-right"><div class="btn btn-success">Confirm</div></div>
				<div class="col-sm-6"><div class="btn btn-warning">Retake</div></div>
			</div>-->
		</div>
		<div class="col-md-6">
		<div class="row margin tab-menu">
		
		<!------------------- Tab ----------------->
		<ul class="nav nav-tabs" role="tablist">
		  <li class="active"><a href="#home" role="tab" data-toggle="tab">Basic Info</a></li>
		  <li><a href="#profile" role="tab" data-toggle="tab">Department Info</a></li>
		  <!--<li><a href="#messages" role="tab" data-toggle="tab">Messages</a></li>
		  <li><a href="#settings" role="tab" data-toggle="tab">Settings</a></li>-->
		</ul>
		
		<!-- Tab panes -->
		<div class="tab-content">
		  <div class="tab-pane active" id="home">
			  	<div class="row margin">
					<div class="txt-front text-right left">Name : </div> 
					<div class="left"><input id="name" class="txt-box-1" name="name" type="text"></div>
				</div>
				<div class="row margin">
					<div class="txt-front text-right left">Surname : </div> 
					<div class="left"><input id="surname" class="txt-box-1" name="surname" type="text"></div>
				</div>
		  </div>
		  <div class="tab-pane" id="profile">...</div>
		  <!--<div class="tab-pane" id="messages">...</div>
		  <div class="tab-pane" id="settings">...</div>-->
		</div>
		<!----------------------------------------->
		
			
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
			<div id="status_l" class="row beginL text-center" style="width: 100%; margin-top: 10px;">เริ่มสแกน</div>
			<div id="pl_t_l" class="row pl_t_l" style="padding-left: 20px; margin-top: 10px;">Progress</div>
			<div class="progress pgL" style="width: 100%;">
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
			<div id="status_r" class="row beginR text-center" style="width: 100%; padding-left: 40px;margin-top: 10px">เริ่มสแกน</div>
			<div id="pl_t_r" class="row pl_t_r" style="padding-left: 20px; margin-top: 10px;">Progress</div>
			<div class="progress pgR pull-right" style="width: 100%;">
			  <div id="percen_r" class="progress-bar progress-bar-striped active text-center" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width:;">
			    0%
			  </div>
			</div>
		</div>
	</div>
</div>
<div id="sound"></div>
<script type="text/javascript">
	
		$('.finger').click(function(){
			
			var h_user_id = $("#h_user_id").val();
			if(h_user_id != ""){
				//$('.beginR,.beginL,.pgL,.pgR').css('opacity','1');
				var text=  $(this).attr("id");
				if(text == "finL1" || text == "finL2" || text == "finL3" || text == "finL4" || text == "finL5"){
					$('.beginL,.pgL,.pgR,.pl_t_l').css('opacity','1');
					
				}else if(text == "finR1" || text == "finR2" || text == "finR3" || text == "finR4" || text == "finR5"){
					$('.beginR,.pgL,.pgR,.pl_t_r').css('opacity','1');
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
			$("#user_id").select();
		});

	

	$("#user_id").keyup(function(){
         if($(this).val().length == 6){
           check_user();
		 }
    });



</script>
</body>
</html>

