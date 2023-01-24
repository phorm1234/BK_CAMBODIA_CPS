<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-theme.min.css">
<script src="<?php echo base_url();?>js/jquery-2.1.1.min.js"></script>
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<style>
	#body{ margin:auto; width:500px; overflow:hidden;}
	.col-xs-6 {
    text-align: center;
    width: 50%;
}
</style>
</head>
<body>
	<div class="container" id="body" >
    	<!--<p><img style="float:right;" src="<?php echo base_url();?>images/ssup-logo.jpg" width="100px;"></p>-->
    	<p style="margin:auto;width:322px; clear:both;">
        <h2 style="text-align:center">ระบบสแกนลายนิ้วมือ</h2>
        </p>
        <p style="margin:auto;width:322px; clear:both;">
        <img style="margin:auto" src="<?php echo base_url();?>images/fingerprint.jpg" width="322" height="158" />
        </p>
        <p id="detail">
       		<?php echo $str;?>
		</p>
     
    </div>
    
</body>
</html>
