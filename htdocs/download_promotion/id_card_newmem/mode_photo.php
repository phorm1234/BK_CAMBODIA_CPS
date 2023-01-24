<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
.style2 {color: #067db7; font-size:20px;}
.style3 {color: #e46c0a; font-size:24px;}
.textmanual {color: #376092; font-size:24px;}
.textidcard {font-size:28px; color:#99CCFF;}

-->
</style>
<link rel="stylesheet" href="../../../download_promotion/id_card_newmem/js/tinyboy_css.css" />
<script type="text/javascript" src="../../../download_promotion/id_card_newmem/js/tinybox.js"></script>
<?php
$com_ip=$_SERVER['REMOTE_ADDR'];
?>
<style type="text/css">
<!--
.style1 {color: #0066FF; font-size:28px;}
.style5 {	color: #0066FF;
	font-size: 18px;
}
-->
</style>
<input name="status_photo" type="hidden" id="status_photo" size='5' value='' />
<input name="num_snap" type="hidden" id="num_snap" size='5' value="0" />
<input name="id_img" type="hidden" id="id_img" />
<input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>
<input name="status_readcard" type="hidden" id="status_readcard" />

<script type="text/javascript">

</script>
<script type="text/javascript">
function from_snap(){
			var num_snap=document.getElementById('num_snap').value;
			num_snap=parseInt(num_snap)+1;
			document.getElementById('num_snap').value;
	var url="../../../download_promotion/id_card/from_snap.php?id_card="+document.getElementById('id_card').value+"&num_snap="+num_snap;
	popup(url,"from_snap","500","500");
}

function popup(url,name,windowWidth,windowHeight){    
	myleft=(screen.width)?(screen.width-windowWidth)/2:100;	
	mytop=(screen.height)?(screen.height-windowHeight)/2:100;	
	properties = "width="+windowWidth+",height="+windowHeight;
	properties +=",scrollbars=yes, top="+mytop+",left="+myleft;   
	window.open(url,name,properties);
}
function chk_snap(){
	
	var id_card=document.getElementById('id_card').value;
		
		if(document.getElementById("nation2").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นครับ");
				document.getElementById('id_card').focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักครับ");
				document.getElementById('id_card').focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				document.getElementById('id_card').focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
	
	
		
       TINY.box.show({url:'../../../download_promotion/id_card_newmem/from_snap.php',post:'id=16',width:500,height:400,opacity:20,topsplit:3})
}

function sendotp(){
		if(document.getElementById("status_readcard").value!="Photo"){
			alert("ถ่ายรูปบัตรร ปชช. ก่อนค่ะ");
			return false;
		}
		document.getElementById("id_card").readOnly=true;
		$("#val_otp").focus();
	
		var id_card=$("#id_card").val();

		if(document.getElementById("nation2").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		

		if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
					alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
					return false;
		}
		
		$.get("../../../download_promotion/id_card_newmem/sendotp.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			id_card:$("#id_card").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				if(arrdata[0]=="OK"){
					alert(arrdata[1]);
					$("#var_otp").focus();
				} else {
					alert(arrdata[1]);
					
				}

		}); 
}		

function sendotp_chk(){
		if(document.getElementById("status_readcard").value!="Photo"){
			alert("ถ่ายรูปบัตรร ปชช. ก่อนค่ะ");
			return false;
		}
		document.getElementById("id_card").readOnly=true;
		$("#val_otp").focus();
	
		var id_card=$("#id_card").val();
		if(document.getElementById("nation2").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		

		if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
					alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
					return false;
		}
		
		$.get("../../../download_promotion/id_card_newmem/sendotp_chk.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			id_card:$("#id_card").val(),
			var_otp:$("#var_otp").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				if(arrdata[0]=="OK"){
					search_idcard($("#id_card").val(),'Manual',$("#var_otp").val());
				} else {
					alert(arrdata[1]);
					
				}

		}); 
}		
	
		
</script>

<table align="center" cellpadding="3" cellspacing="3">	
  <tr>
    <td class='style3'>1.</td>
    <td class='textmanual'><span class="textmanual">เลือกสัญชาติ</span></td>
    <td><input name="mynation" type="radio" value="1" checked="checked" id="nation1" onclick="$('#label_id').html('ใส่เลขที่บัตรประจำตัวประชาชน');" />
      <span class="style2">ไทย</span>
      <input name="mynation" type="radio" value="2" id="nation2" onclick="$('#label_id').html('ใส่เลขที่ Passport');" />
      <span class="style2">ต่างชาติ</span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class='style3'>2. </td>
  <td class='textmanual'><div id='label_id'>ใส่เลขที่บัตรประชาชน</div></td>
  <td><input  name='id_card' type='text' id='id_card'  /></td>
  <td><a  class='round-button' style='color:#355e90; vertical-align:middle;' ></a></td>
  </tr>
  <tr>
    <td class='style3'>3.</td>
    <td class='textmanual'>ถ่ายรูป</td>
    <td><a  class='round-button' style='color:#355e90; vertical-align:middle;' ><img src='../../../download_promotion/id_card_newmem/icon_camera.gif' width='60px' height='60px' onclick='chk_snap();' /></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class='textmanual'>&nbsp;</td>
    <td class='textmanual'>&nbsp;</td>
    <td><div  id='show_photo'></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class='style3'>4.</td>
    <td class='textmanual'>ขอ OTP CODE </td>
    <td><span id='show_idcard'><span id='show_idcard'>
      <span id='from_ok' ><input name="button" type='button'  value='ขอ OTP CODE '  style="color:#99CC00;" onclick="sendotp();"/>
      </span>
    </span></span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class='style3'>5.</td>
    <td class='textmanual'><span id='from_label_otp' >ยืนยัน OTP CODE</span></td>
    <td><span id='from_val_otp' ><input  name='var_otp' type='text' id='var_otp'   style="color:#99CC00;" size="10" onKeyDown="if (event.keyCode == 13){sendotp_chk();}"/></span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class='textmanual'>&nbsp;</td>
    <td class='textmanual'>&nbsp;</td>
    <td><span id='from_ok_otp' ><input name="button" type='button' onclick='sendotp_chk();' value='ยืนยัน' style="color:#99CC00;" />
    </span></td>
    <td>&nbsp;</td>
  </tr>
</table>
