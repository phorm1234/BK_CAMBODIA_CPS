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

<style>
	.loading{
		background:#000 url(<?php echo base_url();?>css/select2-spinner.gif) no-repeat center center;
		height:100%;
		width:100%;
		position:fixed;
		z-index:1000;
		/*left:50%;
		top:50%;*/
		opacity:0.3;
		filter:alpha(opacity=30);
		
	}
</style>
<script>
function download(){
	$("#download").addClass('loading');
	$(window).load("<?php echo base_url();?>index.php/transfer/download",function(response,status,xhr){
		//alert(response);
		if(response=="y"){
			alert("ถ่ายโอนข้อมูลสำเร็จ");
			$("#download").remove("");
			location.reload();
		}else{
			alert("ถ่ายโอนข้อมูลไม่สำเร็จ");
			$("#download").remove("");
			location.reload();
		}
	});
}

function upload(){
	$("#download").addClass('loading');
	$(window).load("<?php echo base_url();?>index.php/transfer/upload",function(response,status,xhr){
		
		if(response=="y"){
			$("#download").remove("");
			alert("ถ่ายโอนข้อมูลสำเร็จ");
			location.reload();
		}else{
			$("#download").remove("");
			alert("ถ่ายโอนข้อมูลไม่สำเร็จ");
			location.reload();
		}
	});
}
</script>
</head>
<body>
<div id="download"></div>
<div class="container">
	<div class="row header">
		<div class="col-md-6 logo">
			<div class="col-sm-3 img-logo"><img src="<?php echo base_url();?>images/ssup-logo.jpg"></div>
			<div class="col-sm-9">
				<div class="logo-txt1">Empoyee Register</div>
				<div class="logo-txt2">with Finger Scan</div>
			</div>
		</div>
		<div class="col-md-6 version">version : 1.0</div>
	</div>
	<div class="row">
	
		<div class="dis-menu img-rounded">
			<a href="<?php echo base_url();?>index.php/register">
			<div class="row text-center menu-box-img">
            <img src="<?php echo base_url();?>images/fingerprint.png" /></div>
			<div class="row text-center">Fingerprint</div>
			</a>
		</div>
		<div class="dis-menu img-rounded">
			<a >
			<div  class="row text-center menu-box-img"><img onclick="download();" src="<?php echo base_url();?>images/download.png" /></div>
			<div class="row text-center">Download</div>
			</a>
		</div>
		<div class="dis-menu img-rounded">
			<a>
			<div class="row text-center menu-box-img"><img onclick="upload();" src="<?php echo base_url();?>images/upload.png" /></div>
			<div class="row text-center">Upload</div>
			</a>
		</div>
		
	</div>
</div>
</body>
</html>