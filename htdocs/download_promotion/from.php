<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script>
function set_pro() {
	$("#show_load").html("<img src='preload.gif'>");
   $.get("call_op.php",{
   					type_pro:"New",
	    	    	ran:Math.random()},function(data){
						if(data=="No"){
							set_pro();
						}else{
							$("#show_load").html(data);
						}
    }); 
}
function set_pro_one() {
	$("#show_load").html("<img src='preload.gif'>");
   $.get("from_listpro.php",{
   					type_pro:"One",
	    	    	ran:Math.random()},function(data){
						if(data=="No"){
							set_pro();
						}else{
							$("#show_load").html(data);
						}
    }); 
}
function download_pro(promo_code) {
	$("#show_load").html("<img src='preload.gif'>");
   $.get("call_op.php",{
   					type_pro:"One",
					promo_code:promo_code,
	    	    	ran:Math.random()},function(data){
						if(data=="No"){
							set_pro();
						}else{
							$("#show_load").html(data);
						}
    }); 
}


function set_pro_all() {
	$("#show_load").html("<img src='preload.gif'>");
   $.get("call_op.php",{
   					type_pro:"All",
	    	    	ran:Math.random()},function(data){
						if(data=="No"){
							set_pro();
						}else{
							$("#show_load").html(data);
						}
    }); 
}

</script>

<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>
	<style>
	#newspaper-b{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:80%;
	text-align:left;border-collapse:collapse;border:1px solid #69c;margin:20px;}
	#newspaper-b th{font-weight:bold;font-size:14px;color:#039;padding:15px 10px 10px;background:#d0dafd;}
	#newspaper-b tbody{background:#e8edff;}
	#newspaper-b td{color:#669;border-top:1px dashed #fff;padding:10px;}
	#newspaper-b tbody tr:hover td{color:#339;background:#d0dafd;}
	-->

	.last_logobutton {
    background: -moz-linear-gradient(center top , #D5DEE5 5%, #B7CBDB 100%) repeat scroll 0 0 #D5DEE5;
    border: 2px solid #B2CBDF;
    border-radius: 5px 5px 5px 5px;
    box-shadow: 0 1px 0 0 #BBDAF7 inset;
    cursor: pointer;
    display: inline-block;
    font-family: Trebuchet MS;
    font-size: 14pt;
    font-weight: bold;
    padding: 10px 17px;
	}


	</style>


<center>
<br>
<span style="color:#A11B34; font-size:24px;">เลือกรูปแบบการ Update Promotion</span>
<br>
<img src='down.png' width='30px' >
<br>
<table border="0" cellpadding="10" cellspacing="10">
  <tr>
    <td><a class='last_logobutton'  onclick="set_pro();" style="color:#FF0000;">Update Promotion ที่เพิ่มมาใหม่เร็วๆนี้</a></td>
   <td><a class='last_logobutton'  onclick="set_pro_one(); " style="color:#0000FF;">Update ทีละ Promotion</a></td>
    <td><a class='last_logobutton'  onclick="set_pro_all();" style="color:#0000FF;">Update Promotion ทั้งหมด (นานมาก)</a></td>
  </tr>
</table>
<br>

<br>
<div id='show_load'>
<img src='promotion.jpeg'>
</div>
<br>

<?php
$fp = @fopen("http://localhost/download_promotion/toserver_bill_day_quick.php", "r");
$text=@fgetss($fp, 4096);
echo "SendBill30Minit";
?>
<!--
<table border="0" cellpadding="10" cellspacing="10">
  <tr>
    <td><a class='last_logobutton'  href='toserver_process_wait.php?url=http://crmcps.ssup.co.th/app_service_cps/sms_mobile/op/' style="color:#0000FF;">1. ส่ง SMS ให้ลูกค้าทำแบบสอบถามความพึงพอใจ<img src='img/new.gif'></a></td>
  </tr>
</table>
-->
</center>