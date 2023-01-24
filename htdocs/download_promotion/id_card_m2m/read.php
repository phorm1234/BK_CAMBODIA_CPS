<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<APPLET CODE = 'idcard.RdNationalCardID' archive='../../download_promotion/id_card/lib/RdNIDApplet090DL.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>  
<link href="/sales/css/promotion/newmember.css" rel="stylesheet">


<!--<script type="text/javascript" src="js/jquery-1.3.2.js"></script>-->
<link rel="stylesheet" href="../../../download_promotion/id_card_m2m/js/tinyboy_css.css" />
<script type="text/javascript" src="../../../download_promotion/id_card_m2m/js/tinybox.js"></script>

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
		add_member_idcard(data);
		var data_arr = data.split('#');


		
		document.getElementById('status_readcard').value="AUTO";
		if(data_arr[0]!=document.getElementById('id_card').value){
			alert("บัตรประชาชนที่เสียบเข้ามาไม่ตรงกับของลูกค้าเลย กรุณาเสียบบัตรประชาชนรหัส : "+document.getElementById('id_card').value+"เข้ามาค่ะ");
			return false;
		}
		document.getElementById('id_card').value=data_arr[0];
		document.getElementById('mr').value=data_arr[1];
		document.getElementById('fname').value= data_arr[2];
		document.getElementById('lname').value= data_arr[4];
		
		document.getElementById('mr_en').value=data_arr[5];
		document.getElementById('fname_en').value= data_arr[6];
		document.getElementById('lname_en').value= data_arr[8];
		
		document.getElementById('address1').value= data_arr[9];
		document.getElementById('address2').value= data_arr[10];
		document.getElementById('address3').value= data_arr[14];
		document.getElementById('address4').value= data_arr[15];
		document.getElementById('address5').value= data_arr[16];
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
			hbd_format=hbd_d+"/"+hbd_m+"/"+hbd_y;
		document.getElementById('hbd').value= hbd_format; //วันเกิด
		//$("#hbd_day").val(parseInt(hbd_d,10));
		//$("#hbd_month").val(parseInt(hbd_m,10));
		//$("#hbd_year").val(parseInt(hbd_y-543,10));

		document.getElementById('hbd_day').value= parseInt(hbd_d,10);
		document.getElementById('hbd_month').value= parseInt(hbd_m,10);
		document.getElementById('hbd_year').value= parseInt(hbd_y-543,10);
		
		document.getElementById('card_at').value= data_arr[19]; //สถานที่ออกบัตร
		var start_date=data_arr[20];
			start_date_d=start_date.substr(6,2);
			start_date_m=start_date.substr(4,2);
			start_date_y=start_date.substr(0,4);
			start_date_format=start_date_d+"/"+start_date_m+"/"+start_date_y;
		document.getElementById('start_date').value= start_date_format;//วันออกบัตร
		var end_date=data_arr[21];
			end_date_d=end_date.substr(6,2);
			end_date_m=end_date.substr(4,2);
			end_date_y=end_date.substr(0,4);
			end_date_format=end_date_d+"/"+end_date_m+"/"+end_date_y;
		document.getElementById('end_date').value= end_date_format;//วันหมดอายุ
		
		//var data = document.NIDApplet.getNIDPictureRD();
		//document.getElementById('show_pic').innerHTML="<img width='150' src='data:image/png;base64,"+data+"'>";

		$("#id_card").attr("readonly",true);
		$("#name").attr("readonly",true);
		$("#surname").attr("readonly",true);
	
	}
	 	
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


function add_member_idcard(x)
{
	var data_arr = x.split('#');
	var id_card=data_arr[0];
	var mr=data_arr[1];
	var fname=data_arr[2];
	var lname=data_arr[4];
	var mr_en=data_arr[5];
	var fname_en=data_arr[6];
	var lname_en=data_arr[8];
	var address= data_arr[9];
	var mu=data_arr[10];
	var tambon_name= data_arr[14];
	var amphur_name= data_arr[15];
	var province_name= data_arr[16];
	var sex=data_arr[17];

	var hbd=data_arr[18];
		hbd_d=hbd.substr(6,2);
		hbd_m=hbd.substr(4,2);
		hbd_y=hbd.substr(0,4);
		hbd_y=parseInt(hbd_y-543,10);
		birthday=hbd_y+"-"+hbd_m+"-"+hbd_d;
	var card_at = data_arr[19]; //สถานที่ออกบัตร
	var start_date=data_arr[20];
		start_date_d=start_date.substr(6,2);
		start_date_m=start_date.substr(4,2);
		start_date_y=start_date.substr(0,4);
		start_date_format=start_date_y+"-"+start_date_m+"-"+start_date_d;
	
	var end_date=data_arr[21];
	var	end_date_d=end_date.substr(6,2);
	var	end_date_m=end_date.substr(4,2);
	var	end_date_y=end_date.substr(0,4);
	var	end_date_format=end_date_y+"-"+end_date_m+"-"+end_date_d;
	var member_no="";
		
		
creatajax();
var my=document.frmbill_open;
ajaxRequest.onreadystatechange = function()
{
	if(ajaxRequest.readyState == 4)
	{
					var data= ajaxRequest.responseText;
		
	}
}



var ran=Math.random();
ajaxRequest.open("GET", "../../download_promotion/id_card_newmem/add_member_idcard.php?ran="+ran+"&id_card="+id_card+"&mr="+mr+"&fname="+fname+"&lname="+lname+"&mr_en="+mr_en+"&fname_en="+fname_en+"&lname_en="+lname_en+"&address="+address+"&mu="+mu+"&tambon_name="+tambon_name+"&amphur_name="+amphur_name+"&province_name="+province_name+"&sex="+sex+"&birthday="+birthday+"&card_at="+card_at+"&start_date_format="+start_date_format+"&end_date_format="+end_date_format+"&member_no="+member_no, true);
ajaxRequest.send(null); 

}


function from_search(){

	    $.get("../../download_promotion/id_card_m2m/from_search.php",{
		    	    	ran:Math.random()},function(data){
						$("#from_search").html(data);
						$("#id_card").focus();
						$("#id_card").val($("#friend_id_card").val());
						$("#mobile_no").val($("#friend_mobile_no").val());
						
	    }); 

}
function from_photo(){

    $.get("../../download_promotion/id_card_m2m/from_photo.php",{
	    	    	ran:Math.random()},function(data){
					$("#from_search").html(data);
    }); 

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
function search_friend(){
	var mobile_no=$("#mobile_no").val();
	var friend_id_card=$("#friend_id_card").val();
	var status_readcard=$("#status_readcard").val();

		var application_id=$("#chk_application_id").val();
		if($("#chk_nation").val()=="thai"){

		
			var mobile_no=$("#mobile_no").val();
			if(!check_number(mobile_no)){
				alert("เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}else if(mobile_no.length!=10){
				alert("เบอร์มือถือไม่ครบ 10 หลักค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
				alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			var id_card=$("#id_card").val();
			
			var fname=$("#fname").val();
			var lname=$("#lname").val();
			var hbd=$("#hbd").val();
			if(id_card.length==0){
				alert("กรุณาป้อนรหัสบัตรประชาชนเพื่อใช้ในการค้นหาค่ะ");
				$("#id_card").focus();
				return false;
			}

			if(!checkID(id_card)){
				alert('รูปแบบรหัสประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
			
			
			if(fname.length==0){
				alert("กรุณาป้อนชื่อลูกค้าเพื่อใช้ในการค้นหาค่ะ");
				$("#fname").focus();
				return false;
			}
			if(lname.length==0){
				alert("กรุณาป้อนนามสกุลลูกค้าเพื่อใช้ในการค้นหาค่ะ");
				$("#lname").focus();
				return false;
			}
			if($("#hbd_day").val()==1 && $("#hbd_month").val()==1 && $("#hbd_year").val()==1814){
				alert("กรุณาป้อนเลือกวันเกิดลูกค้าเพื่อใช้ในการค้นหาค่ะ");
				return false;
			}
			

			var arr=hbd.split("/");

			if($("#friend_id_card").val()==$("#id_card").val()){
				alert("ห้ามใส่รหัสบัตรประชาชนของสมาชิกที่เชิญชวนตรงกับรหัสของผู้มาสมัคร");	
				return false;
			}
			if($("#friend_mobile").val()==$("#mobile_no").val()){
				alert("ห้ามใส่เบอร์มือถือของสมาชิกที่เชิญชวนตรงกับเบอร์ของผู้มาสมัคร");	
				return false;
			}			
			
		}
		

			
				
    $.get("../../download_promotion/id_card_m2m/search_friend.php",{
					friend_mobile:$("#friend_mobile").val(),
					friend_id_card:$("#friend_id_card").val(),
					id_card:$("#id_card").val(),
					mobile_no:$("#mobile_no").val(),
					fname:$("#fname").val(),
					lname:$("#lname").val(),
					hbd:$("#hbd").val(),
					hbd_day:$("#hbd_day").val(),
					hbd_month:$("#hbd_month").val(),
					hbd_year:$("#hbd_year").val(),						
	    	    	ran:Math.random()},function(data){
					var arr=data.split("@@");
					if(arr[0]=="Have"){
						var name=arr[1];
						var surname=arr[2];
						var fullname="<span class='style3'>คุณสามารถสมัครสมาชิกได้</span>&nbsp;&nbsp;<img src='../../download_promotion/id_card_m2m/yes.gif' width=30 hieght=30>";
												$("#show_line1").html("<hr>");
												
						$("#show_btn_sendotp").html("<input name='button2' type='button' style='font-size:18px; color:#4f81bd;' onclick='sendotp();' value='ยืนยันการใช้สิทธิ์'/>");
						$("#friend_name").html(fullname);
						$("#friend_id_card").val(arr[3]);
						$("#friend_mobile").val(arr[4]);
						$("#friend_customer_id").val(arr[5]);
						
						$("#id_card").attr("readonly",true);
						$("#mobile_no").attr("readonly",true);
						$("#fname").attr("readonly",true);
						$("#lname").attr("readonly",true);
						$("#hbd_day").attr("disabled", true);
						$("#hbd_month").attr("disabled", true);
						$("#hbd_year").attr("disabled", true);
						
						$("#friend_status").val("Y");
						$("#id_card").focus();
					}else if(arr[0]=="Stop"){
						alert(arr[1]);
						$("#friend_status").val("N");
						return false;
					}else if(arr[0]=="No"){
						alert("ไม่พบรหัสบัตรประชาชนนี้ในระบบค่ะ");
						$("#friend_status").val("N");
						return false;
					}else{
						alert("ไม่สามารถทำรายการได้เนื่องจากระบบขัดข้อง");
						$("#friend_status").val("N");
						return false;
					}
					
    }); 

}

function sendotp(){	
	if(status_readcard=="Manual"){
		alert("ต้องอ่านข้อมูลบัตรประชาชนจากเครื่องอ่านบัตรประชาชนค่ะ หากอ่านไม่ได้ต้องถ่ายรูปบัตรประชาชนของลูกค้าไว้ค่ะ");
		return false;
	}		
    $.get("../../download_promotion/id_card_m2m/sendotp.php",{
					friend_mobile:$("#friend_mobile").val(),
					friend_id_card:$("#friend_id_card").val(),
					id_card:$("#id_card").val(),
					mobile_no:$("#mobile_no").val(),
					fname:$("#fname").val(),
					lname:$("#lname").val(),
					hbd:$("#hbd").val(),
					hbd:$("#hbd").val(),
					hbd_day:$("#hbd_day").val(),
					hbd_month:$("#hbd_month").val(),
					hbd_year:$("#hbd_year").val(),					
	    	    	ran:Math.random()},function(data){
					var arrdata=data.split("###");
					if(arrdata[0]=="NO"){
						alert(arrdata[1]);
						$("#val_otp").focus();
					} else {
						alert("OTP CODE ถูกส่งไปที่เบอร์ : "+$("#mobile_no").val());
						$("#show_label_val_otp").html("<table ><tr><td><span class='style0'>ใส่ OTP CODE : </span></td><td><input name='val_otp' type='text' id='val_otp' size='15' class='textotp' onKeyDown=\"if (event.keyCode == 13){m2mchknew();}\" /></td><td><input name='button22' type='button' style='font-size:18px; color:#4f81bd;' onclick='m2mchknew();' value='ตกลง'/></td></tr></table>");
						
						$("#val_otp").focus();
					}
    }); 

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



</script>

		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				from_search();
			} );
		</script>

<div id='popup_snapx'  style="display:none;"  title="ถ่ายรูป" >

</div>

    <input name="status_readcard" type="text" id="status_readcard" size='5' value='Manual' readonly="true"/>
    <input name="chk_nation" type="hidden" id="chk_nation" size='5' value='thai'/>
    <input name="chk_application_id" type="hidden" id="chk_application_id" size='5' value='thai'/>
    
    <input name="status_photo" type="hidden" id="status_photo" />
	
	<input name="memold_id_card" type="hidden" id="memold_id_card" value="<?php echo $_GET['memold_id_card']; ?>" />
	<input name="memold_mobile_no" type="hidden" id="memold_mobile_no" value="<?php echo  $_GET['memold_mobile_no']; ?>"/>
	<input name="memold_customer_id" type="hidden" id="memold_customer_id" value="<?php echo  $_GET['memold_customer_id']; ?>" />
	<input name="memold_member_no" type="hidden" id="memold_member_no" value="<?php echo  $_GET['memold_member_no']; ?>" />
	
	<input name="friend_id_card" type="hidden" id="friend_id_card" value="<?php echo  $_GET['friend_id_card']; ?>" />
	<input name="friend_mobile_no" type="hidden" id="friend_mobile_no" value="<?php echo  $_GET['friend_mobile_no']; ?>" />	 
	<input name="promo_code_play" type="hidden" id="promo_code_play" value="<?php echo  $_GET['promo_code_play']; ?>" />	  	 	
	<input name="promo_code" type="hidden" id="promo_code" value="<?php echo  $_GET['promo_code']; ?>" />	
	<input name="coupon_code" type="hidden" id="coupon_code" value="<?php echo  $_GET['coupon_code']; ?>" />
	
	
<!--	
    <table align='center'>
<tr>

<td>
<input name="nation" type="radio" value="thai" checked="checked" onclick="$('#chk_nation').val('thai'); from_search();"/>
  <span style="color:#FF9966; font-size:28px">คนไทย</span>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="nation" type="radio" value="no" id='nation_no' onclick="$('#chk_nation').val('nothai'); alert('ในช่วงเดือน พฤศจิกายน - ธันวาคม 2557 ชาวต่างชาติไม่สามารถสมัครสมาชิกใหม่ได้'); return false;  "/>
<span style="color:#0066CC; font-size:28px">ชาวต่างชาติ</span>
</td></tr>
</table>
-->
<br>

		
<div id='from_search' align='center'></div>		


