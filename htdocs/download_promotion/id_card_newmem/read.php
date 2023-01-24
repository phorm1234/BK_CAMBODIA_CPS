<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<style type="text/css">
<!--
.style3 {	font-size: 36px;
	color: #9bbb59;
}
-->
</style>
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
		/*if(document.getElementById('id_card').value==""){
			alert("ใส่รหัสบัตรประชาชนก่อนครับ");
			return false;	
		}
		$("#dialog-lastpro_play" ).dialog('close'); 
		callBackIdCard(document.getElementById('promo_code').value,document.getElementById('id_card').value);
		return false;	
		*/
		
	if(!document.NIDApplet.isCardInsertRD()){
		alert("คุณยังไม่ได้ Scan บัตรประชาชน");
		return false;
	}


	if(document.NIDApplet.isCardInsertRD()){

		var data = document.NIDApplet.getNIDDataRD();

		add_member_idcard(data);
		var data_arr = data.split('#');
		
		
		
		document.getElementById('status_readcard').value="AUTO";
		
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

		//$("#id_card").attr("readonly",true);
		//$("#name").attr("readonly",true);
		//$("#surname").attr("readonly",true);
		
		
		if(document.getElementById('id_card').value==""){
			alert("ใส่รหัสบัตรประชาชนก่อนครับ");
			return false;	
		}
			
	
		var birthday=hbd_y-543+"-"+hbd_m+"-"+hbd_d;
		ccs_chknew(data_arr[0],data_arr[2],data_arr[4],birthday);
	}else{
		alert("บัตรปชช. ลูกค้าไม่สามารถอ่านจากเครื่องอ่าน ID CARD ได้ โปรโมชั่นนี้สำหรับลูกค้าที่บัตร ปชช. อ่านได้เท่านั้น");
		return false;	
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

function from_search(){

	    $.get("../../download_promotion/id_card_newmem/from_search.php",{
		    	    	ran:Math.random()},function(data){
						$("#from_search").html(data);
						$("#id_card").focus();
						
	    }); 

}
function from_photo(){

    $.get("../../download_promotion/id_card_newmem/from_photo.php",{
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

function ccs_chknew(id_card,name,surname,birthday)
{

creatajax();
var my=document.frmbill_open;
ajaxRequest.onreadystatechange = function()
{
	if(ajaxRequest.readyState == 4)
	{
					var data= ajaxRequest.responseText;
					var arr=data.split("##");
					if(arr[0]=="ok"){
						$("#dialog-lastpro_play" ).dialog('close'); 
						callBackIdCard(document.getElementById('promo_code').value,document.getElementById('id_card').value,document.getElementById('coupon_code').value);
						return false;   
					}else{
                       alert(arr[1]);
					   return false;
   
					}
					
	}
}



var ran=Math.random();
ajaxRequest.open("GET", "../../download_promotion/id_card_newmem/ccs_chknew.php?ran="+ran+"&id_card="+id_card+"&name="+name+"&surname="+surname+"&birthday="+birthday, true);
ajaxRequest.send(null); 

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

</script>

		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				from_search();
			} );
		</script>

<div id='popup_snapx'  style="display:none;"  title="ถ่ายรูป" >

</div>

    <input name="status_readcard" type="hidden" id="status_readcard" size='5' value='Manual'/>
    <input name="chk_nation" type="hidden" id="chk_nation" size='5' value='thai'/>
    <input name="chk_application_id" type="hidden" id="chk_application_id" size='5' value='thai'/>
    
    <input name="status_photo" type="hidden" id="status_photo" />
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
<span class="style3">
<input name="promo_code" type="hidden" id="promo_code" value="<?php echo $_GET['application_id'];?>" />
</span>
<input name="coupon_code" type="hidden" id="coupon_code" value="<?php echo $_GET['coupon_code'];?>"/>
<br>

		
<div id='from_search' align='center'></div>		


