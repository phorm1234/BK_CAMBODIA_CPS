<?php 
	session_start(); 
	if(empty($_SESSION["u_id"]))
	{
		echo "<script>window.location = 'login.php'</script>";
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Set OP Reward</title>

<link href="css/custom.css" rel="stylesheet">
<link href="css/bootstrap.css" rel="stylesheet">
<style>
	body{
		position: relative;
  		padding-top: 40px;
	}
</style>
</head>

<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
    	<a class="brand" href="index.php"></a>
		<div class="on-right form-inline">
			<?php 
				if(!empty($_SESSION['u_id']) && !empty($_SESSION['u_name']))
				{
					echo "<span class='help-inline u-10'>".$_SESSION['u_name']."</span> <a class='btn btn-small' href='logout.php'><i class='icon-off'></i></a>";
					
				}	
				
			?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="span3 menu"><!--Sidebar content-->
			<div class="accordion" id="accordionMenu">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" id="head-collapseOP" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseOP">
							 <i class="icon-chevron-right"></i> OP
						</a>
					</div>
					<div id="collapseOP" class="accordion-body collapse in">
						<div class="accordion-inner">
						<!-- 
							<p class="btn-link" onclick="get_page('reward.php')">- โปรแกรม Set OPS Reward</p>
							<p class="btn-link" onclick="get_page('activity.php')">- โปรแกรม Set OPS Activity</p>							
							<p class="btn-link" onclick="get_page('product.php')">- สินค้า New product</p>
						-->
							<p class="btn-link" > ------------------------------- </p>
							<p class="btn-link" onclick="get_page('stock.php')"> เพิ่ม stock เครื่องเทส </p>
						</div>
					</div>
				</div>
				<!--<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" id="head-collapseCPS" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseCPS">
							<i class="icon-chevron-right"></i> CPS
						</a>
					</div>
					<div id="collapseCPS" class="accordion-body collapse">
						<div class="accordion-inner">

						</div>
					</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" id="head-collapseGNC" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseGNC">
							<i class="icon-chevron-right"></i> GNC
						</a>
					</div>
					<div id="collapseGNC" class="accordion-body collapse">
						<div class="accordion-inner">

						</div>
					</div>
				</div>-->
			</div>
    	</div>
		<div class="span9"><!--Body content-->
			
			<iframe src="" class="content" allowTransparency="true" scrolling="no" frameborder="0" >
    </iframe>
		</div>
    </div>
</div>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script>
	$(".menu-clone").html($(".menu").html());
	setwind();
	$(window).resize(function() {
		setwind();
	});
	$("#head-collapseOP").click(function(){
		var myclass = $('#head-collapseOP  i').attr('class');
		if(myclass != "icon-chevron-down")
		{ 
			$('#head-collapseOP  i').removeClass('icon-chevron-right').addClass('icon-chevron-down');
			$('#head-collapseCPS  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
			$('#head-collapseGNC  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
		}
		else
		{ 
			$('#head-collapseOP  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
		}
	});
	$("#head-collapseCPS").click(function(){
		var myclass = $('#head-collapseCPS  i').attr('class');
		if(myclass != "icon-chevron-down")
		{ 
			$('#head-collapseCPS  i').removeClass('icon-chevron-right').addClass('icon-chevron-down');
			$('#head-collapseOP  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
			$('#head-collapseGNC  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
		}
		else
		{ 
			$('#head-collapseCPS  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
		}
	});
	$("#head-collapseGNC").click(function(){
		var myclass = $('#head-collapseGNC  i').attr('class');
		if(myclass != "icon-chevron-down")
		{ 
			$('#head-collapseGNC  i').removeClass('icon-chevron-right').addClass('icon-chevron-down');
			$('#head-collapseOP  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
			$('#head-collapseCPS  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
		}
		else
		{ 
			$('#head-collapseGNC  i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
		}
	});
	
	function get_page(page_link)
	{
		$('.content').attr('src', page_link)
	}
	function setwind()
	{
		var newsize = parseInt($(window).width()-290);
		$(".span9").css( "width" , newsize );
		$(".span9").css( "height" , $(window).height()-40 );
	}
</script>
</body>
</html>
