<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<script type="text/javascript" src="jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="ccs.js"></script>-->

<style type="text/css">
<!--
.style2 {color: #067db7; font-size:20px;}
.style3 {color: #e46c0a; font-size:24px;}
.textmanual {color: #376092; font-size:24px;}
.textidcard {font-size:28px; color:#99CCFF;}

-->
</style>
<style>
.round-button {
	width:50px;
}
.round-button-circle {
	width: 90px;
	height:0;
	padding-bottom: 90px;
    border-radius: 50%;
	border:5px solid #cfdcec;
    overflow:hidden;
    
    background: #4679BD; 
    box-shadow: 0 0 3px gray;
	cursor:pointer;
}
.round-button-circle:hover {
	background:#30588e;
}
.round-button a {
    display:block;
	float:left;
	width:100%;
	padding-top:50%;
    padding-bottom:50%;
	line-height:1em;
	margin-top:-0.5em;
    
	text-align:center;
	color:#e2eaf3;
    font-family:Verdana;
    font-size:1.2em;
    font-weight:bold;
    text-decoration:none;
}
</style>

<APPLET CODE = 'idcard.RdNationalCardID.class' archive='../../download_promotion/id_card_newmem/lib/RdNIDApplet090DL.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>  
<link href="/sales/css/promotion/newmember.css" rel="stylesheet">

<script src="jquery-1.10.1.min.js"></script> 
<link rel="stylesheet" href="styles/jquery.dialog.min.css"/>
<script src="scripts/jquery.dialog.min.js"></script> 
<!--<script type="text/javascript" src="js/jquery-1.3.2.js"></script>-->
<link rel="stylesheet" href="../../../download_promotion/id_card_newmem/js/tinyboy_css.css" />
<script type="text/javascript" src="../../../download_promotion/id_card_newmem/js/tinybox.js"></script>

<script>




function readIDCARD(){
		
		if(!document.NIDApplet.isCardInsertRD()){
			alert("คุณยังไม่ได้ Scan บัตรประชาชน");
			return false;
		}

		var id_card = document.NIDApplet.getNIDNumberRD();

		document.getElementById('id_card').value=id_card;



}
function onreadidcheck(){
	
	if(!document.NIDApplet.isCardInsertRD()){
		alert("คุณยังไม่ได้ Scan บัตรประชาชน");
		return false;
	}


	if(document.NIDApplet.isCardInsertRD()){

		var data = document.NIDApplet.getNIDDataRD();
		var data_arr = data.split('#');


		

		
		document.getElementById('id_card').value=data_arr[0];
		document.getElementById('mr').value=data_arr[1];
		document.getElementById('fname').value= data_arr[2];
		document.getElementById('lname').value= data_arr[4];
		
		
		document.getElementById('mr_en').value=data_arr[5];
		document.getElementById('fname_en').value= data_arr[6];
		document.getElementById('lname_en').value= data_arr[8];
		
		/*document.getElementById('address1').value= data_arr[9];
		document.getElementById('address2').value= data_arr[10];
		document.getElementById('address3').value= data_arr[14];
		document.getElementById('address4').value= data_arr[15];
		document.getElementById('address5').value= data_arr[16];*/
		
		document.getElementById('address').value= data_arr[9];//บ้านเลขที่
		
		var mu=data_arr[10];
		mu=mu.replace('หมู่ที่','');
		mu=mu.replace(' ','');
		document.getElementById('mu').value=mu;//หมู่
		var tambon=data_arr[14];
			tambon=tambon.replace('ตำบล','');
			tambon=tambon.replace('แขวง','');
			document.getElementById('tambon_name').value=tambon;
		var amphur=data_arr[15];
			amphur=amphur.replace('อำเภอ','');
			amphur=amphur.replace('เขต','');
			document.getElementById('amphur_name').value= amphur;
		var province=data_arr[16];
			province=province.replace('จังหวัด','');
			document.getElementById('province_name').value= province;
			
					
		var sex=data_arr[17];
		var sex_show="";
			if(sex==1){
				sex_show="ชาย";
			}else{
				sex_show="หญิง";
			}
		document.getElementById('sex').value= sex_show; //เพศ
		var hbd=data_arr[18];
			hbd_d=hbd.substr(6,2);
			hbd_m=hbd.substr(4,2);
			hbd_y=hbd.substr(0,4);		
			document.getElementById('birthday').value= parseInt(hbd_y-543,10)+"-"+hbd_m+"-"+hbd_d;
		
		document.getElementById('card_at').value= data_arr[19]; //สถานที่ออกบัตร
		var start_date=data_arr[20];
			start_date_d=start_date.substr(6,2);
			start_date_m=start_date.substr(4,2);
			start_date_y=start_date.substr(0,4);
			start_date_format=parseInt(start_date_y-543,10)+"-"+start_date_m+"-"+start_date_d;
		document.getElementById('start_date').value= start_date_format;//วันออกบัตร
		var end_date=data_arr[21];
			end_date_d=end_date.substr(6,2);
			end_date_m=end_date.substr(4,2);
			end_date_y=end_date.substr(0,4);
			end_date_format=parseInt(end_date_y-543,10)+"-"+end_date_m+"-"+end_date_d;
		document.getElementById('end_date').value= end_date_format;//วันหมดอายุ
		
		/*var data = document.NIDApplet.getNIDPictureRD();
		document.getElementById('show_pic').innerHTML="<img width='150' src='data:image/png;base64,"+data+"'>";*/

		
	
	}
	
	document.getElementById('status_readcard').value="AUTO";

	document.getElementById("id_card").disabled = true;
	document.getElementById("fname").disabled = true;
	document.getElementById("lname").disabled = true;
	document.getElementById("birthday").disabled = true;
	search_idcard($('#id_card').val(),'AUTO','',$('#member_no').val());
 	
}

function readPic(){
	if(!document.NIDApplet.isCardInsertRD()){
		alert("คุณยังไม่ได้ Scan บัตรประชาชน");
		return false;
	}
	
	
	var data = document.NIDApplet.getNIDPictureRD();

	if(data==null){

		alert("Can not get Picture");
		return false;
	}
	document.getElementById('show_pic').innerHTML="<img width='150' src='data:image/png;base64,"+data+"'>";
}



function creatajax() {

		if (window.XMLHttpRequest)
		{ 
				ajaxRequest = new XMLHttpRequest();
		}
		else if(window.ActiveXObject)
		{ 
				 ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		else
		{
				alert("Browser error");
				return false;
		}
}

function webcam_save()
{
var c=confirm("Snap ?");
if(!c){
	return false;
}
creatajax();
var my=document.frmbill_open;
ajaxRequest.onreadystatechange = function()
{
	if(ajaxRequest.readyState == 4)
	{
					var data= ajaxRequest.responseText;
					if(data=="snap_error"){
                        alert("Snap_error");
					  return false;
					}else{
					  alert("Snap OK");
                                          
					}
					
	}
}



var ran=Math.random();
ajaxRequest.open("GET", "webcam_save.php?ran="+ran, true);
ajaxRequest.send(null); 

}


function check_number(ch){
		var len, digit;
		if(ch == " "){ 
			return false;
			len=0;
		}else{
			len = ch.length;
		}

			for(var i=0 ; i<len ; i++)
			{
													digit = ch.charAt(i)
													if(digit >="0" && digit <="9" || digit==","){
													; 
													}else{
													return false; 
													} 

													if (   isNaN(ch)   ) {
														return false; 
													} else {
														;
													}
			} 

		return true;
}


function check_name(ch){
	var len, digit;
	if(ch.length==0 || ch==" "){ 
		return false;
		len=0;
	}else{
		len = ch.length;
	}

		for(var i=0 ; i<len ; i++)
		{
				digit = ch.charAt(i)
				if(digit=="0"  || digit=="1" || digit=="2" || digit=="3" || digit=="4" || digit=="5" || digit=="6" || digit=="7" || digit=="8" || digit=="9"  ){
					return false; 
				}
				
				if(digit=="." || digit=="," || digit=="/" || digit=="*" || digit=="-" || digit=="+" || digit==" " || digit=="_" || digit=="-" || digit=="!" || digit=="@" || digit=="#" || digit=="%" || digit=="^" || digit=="&" || digit=="(" || digit==")" || digit=="="){
					return false; 
				}	
				
				if(digit=="๐"  || digit=="๑" || digit=="๒" || digit=="๓" || digit=="๓" || digit=="๔" || digit=="๕" || digit=="๖" || digit=="๗" || digit=="๘"   || digit=="๙"  || digit=="|"  ){
					return false; 
				}	
				
				
				if(digit=="}"  || digit=="{" || digit=="[" || digit=="]" || digit=="'" || digit=='"' || digit==";" || digit=="," ){
					return false; 
				}	
																	
				
				

		} 

	return true;
}





function chk_format_mobile(x){
	var mobile=x
	if(mobile.length!=10){
		return "len_error";
	}
	if(mobile.length!=10){
		return "len_error";
	}
	
}

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
	
	
		
       TINY.box.show({url:'../../../download_promotion/id_card_newmem/api_from_snap.php',post:'id=16',width:500,height:400,opacity:20,topsplit:3})
}

function view_phto(){
			var date_now = new Date();
			var year_now = date_now.getFullYear(); 	
			

			month_now=('0' + (date_now.getMonth()+1)).slice(-2);

			var path_folder=year_now+month_now;
			var path_img='http://'+$("#ip_this").val()+'/download_promotion/id_card_quick/image_member/'+path_folder+'/'+$("#id_card").val()+"_snap"+$("#num_snap").val()+".jpg";
			//alert(path_img);
			alert("ถ่ายรูปเรียบร้อยแล้วค่ะ");

			$("#show_photo").html("<img width='230px' height='180px' src='"+path_img+"'></img>");
			
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
				//$("#id_card").focus();
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
			member_no:$("#member_no").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				//search_idcard($("#id_card").val(),'Manual',$("#var_otp").val(),$("#member_no").val());
				if(arrdata[0]=="OK"){
					search_idcard($("#id_card").val(),'Manual',$("#var_otp").val(),$("#member_no").val());
				} else {
					alert(arrdata[1]);
					
				}

		}); 
}		

function api_mode_photo() {
	
    $.get("../../../download_promotion/id_card_newmem/api_mode_photo.php",{
					function_next:$("#function_next").val(),
	    	    	ran:Math.random()},function(data){
					 $("#lastpro_play").html(data);		
						
					
    }); 
	
}	

function search_idcard(id_card,status_readcard,otpcode,member_no) {
    $.get("../../../download_promotion/id_card_newmem/api_search_idcard.php",{
	      	    	id_card:id_card,
					status_readcard:status_readcard,
					otpcode:otpcode,
					member_no:member_no,
					function_next:$("#function_next").val(),
	    	    	ran:Math.random()},function(data){
						var arr=data.split("###");
						if(arr[0]=="member_null"){
							alert("ไม่พบข้อมูลสมาชิกในระบบ");
							return false
						}else if(arr[0]=="one"){
							senddata(arr[3],id_card,$("#status_readcard").val());
						}else{
							ccsreadidcardfrom_select_card(arr[1]);
						}
						
					
    }); 
	
}	
function ccsreadidcardfrom_select_card(data){
			    $("#lastpro_play").html(data);	
}	
function senddata(member_no,id_card,status_readcard){
	var x=member_no+"#"+id_card+"#"+status_readcard;
	var xfunction_next=$("#function_next").val()+"(x)";
	//callbackCnIdCard(x);
	eval(xfunction_next);
}	
</script>
<script>
function keynext(id,idnext,numtext){

	if(document.getElementById(id).value.length>numtext){
		document.getElementById(id).value="";
	}
	if(document.getElementById(id).value.length==numtext){
		document.getElementById(idnext).value="";
		document.getElementById(idnext).focus();
	}
}
</script>
<?php

$com_ip=$_SERVER['REMOTE_ADDR'];
		
?>
<style type="text/css">
<!--
.style0 {
	font-size: 36px;
	color: #FF0000;
}
.style1 {
	font-size: 30px;
	color: #376092;
}
.style2 {font-size: 24}

.styleX {
	font-size: 30px;
	color: #9bbb59;
}
.headx {
	font-size: 36px;
	color: #376092;
}
.text2{
font-size:24px; color:#9bbb59; width:200px; border-bottom-color:#9bbb59; border-top-color:#9bbb59; border-left-color:#9bbb59; border-right-color:#9bbb59;
}
.text3{
font-size:24px; color:#426898; width:200px; border-bottom-color:#4f81bd; border-top-color:#4f81bd; border-left-color:#4f81bd; border-right-color:#4f81bd;
}
.textotp{
font-size:24px; color:#9bbb59; width:200px; border-bottom-color:#4f81bd; border-top-color:#4f81bd; border-left-color:#4f81bd; border-right-color:#4f81bd;
}

-->
</style>

<input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>





<title>Untitled Document</title>
</head>
<input name="function_next" type="hidden" id="function_next" value='<?php echo $_GET['function_next']; ?>'/><input name="member_no" type="hidden" id="member_no" value='<?php echo $_GET['member_no']; ?>'/>
<div id='lastpro_play'>
	<body onload="$('#id_card').focus();">
	<table border="0" align="center" cellpadding="3" cellspacing="3" bordercolor="#0099FF">
	  <tr>
		<td class="style3">1.</td>
		<td class="textmanual">เสียบบัตร ปชช. ของสมาชิกที่เครื่องอ่าน ID CARD<img src="../../download_promotion/id_card_newmem/icon_idcard.png" width="30" height="30">
	    </td>
		<td><input name="status_readcard" type="text" id="status_readcard" size='5' value='MANUAL' readonly="true" style="color:#0066FF; background-color:#FFFFFF; font-size:16px"/></td>
	  </tr>
	  <tr>
		<td><span class="style3">2.</span></td>
		<td colspan="2" class="textmanual">กดปุ่มอ่านข้อมูลบัตร ปชช. หากอ่านไม่ได้ให้กดปุ่มถ่ายรูปบัตร ปชช. ของสมาชิก
		  <table border="0" align="center" cellspacing="0" bordercolor="#99CCFF">
		  <tr>
			<td><?php
	$com_ip=$_SERVER['REMOTE_ADDR'];
	?>
			  <input name="status_photo" type="hidden" id="status_photo" size='5' value='' />
				<input name="num_snap" type="hidden" id="num_snap" size='5' value="0" />
				<input name="id_img" type="hidden" id="id_img" />
				<input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>
				<input name="address" type="hidden" id="address" />
				<input name="mu" type="hidden" id="mu" />
				<input name="tambon_name" type="hidden" id="tambon_name" />
				<input name="amphur_name" type="hidden" id="amphur_name" />
				<input name="province_name" type="hidden" id="province_name" />
				<input name="mr" type="hidden" id="mr" />
				<input name="sex" type="hidden" id="sex" />
				<input name="mr_en" type="hidden" id="mr_en" />
				<input name="fname_en" type="hidden" id="fname_en" />
				<input name="lname_en" type="hidden" id="lname_en" />
				<input name="card_at" type="hidden" id="card_at" size="50" />
				<input name="start_date" type="hidden" id="start_date" />
				<input name="end_date" type="hidden" id="end_date" />
				<input name="birthday2" type="hidden" id="birthday2" />
				<input name="nothai" type="hidden" id="nothai" value="checkbox" />
				<input name="fname" type="hidden" id="fname" style="background-color:#FFFFFF;  color:#000000; height:30px;"/>
				<input name="lname" type="hidden" id="lname" style="background-color:#FFFFFF; color:#000000; height:30px;">
				<input name="birthday" type="hidden" id="birthday" style="background-color:#FFFFFF; color:#000000;"/>
				<input name="id_card" type="hidden" id="id_card" style="background-color:#FFFFFF; color:#000000;"/></td>
		  </tr>
		</table></td>
	  </tr>
	  <tr>
		<td class="style3">&nbsp;</td>
		<td colspan="2"><table border="0" cellpadding="3" cellspacing="3">
		  <tr>
			<td width="100"><div onclick="onreadidcheck();" class="round-button-circle" align="center" style="background-color:#9bbb59;border:5px solid #bed78a;"><a  class="round-button" style="color:#355e90; vertical-align:middle; " ><img src="../../download_promotion/id_card_newmem/icon_idcard.png" width="40px" height="40px" /><br />
			  อ่านบัตร ปชช.</a></div></td>
			  <td width="40" class="style3">หรือ</td>
			<td width="100"><div   onclick='api_mode_photo();' class="round-button-circle" align="center" style="background-color:#f79646;border:5px solid #f9d2b2;"><a    class="round-button" style="color:#355e90; vertical-align:middle;" ><img  src="../../../download_promotion/id_card_newmem/icon_camera.gif" width="40px" height="40px" /><br />
			  ถ่ายบัตร ปชช.</a></div></td>
			</tr>
		</table></td>
	  </tr>
	</table>
	<p>&nbsp;</p>
</div>
</body>
</html>
