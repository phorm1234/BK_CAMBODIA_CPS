<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<style type="text/css">
<!--
.style1 {color: #0066FF; font-size:28px;}
.style2 {color: #0066FF; font-size:20px;}
-->
</style>
<APPLET CODE = 'idcard.RdNationalCardID' archive='../../download_promotion/id_card_quick/lib/RdNIDApplet090DL.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>  

<!--<script type="text/javascript" src="js/jquery-1.3.2.js"></script>-->
<link rel="stylesheet" href="../../../download_promotion/id_card_quick/js/tinyboy_css.css" />
<script type="text/javascript" src="../../../download_promotion/id_card_quick/js/tinybox.js"></script>


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
	

	
	
	document.getElementById('showfromotpcode').style.display = 'none';
	//$("#showfromotpcode").hide();
	 	
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
</script>




<script type="text/javascript">

function from_snap(){
			var num_snap=document.getElementById('num_snap').value;
			num_snap=parseInt(num_snap)+1;
			document.getElementById('num_snap').value;
	var url="../../../download_promotion/id_card_quick/from_snap.php?id_card="+document.getElementById('id_card').value+"&num_snap="+num_snap;
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
	if(document.getElementById("nothai").checked==false){
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
		if(id_card==""){
			alert('กรุณาใส่เลขที่ Passport ของสมาชิกครับ');
			document.getElementById('id_card').focus();
			return false;
		}
	}
	
	
		
	TINY.box.show({url:'../../../download_promotion/id_card_quick/from_snap.php',post:'id=16',width:500,height:400,opacity:20,topsplit:3})
}
</script>



<style></style>


<style>
#hbd_table{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:100%;
text-align:left;border-collapse:collapse;border:1px solid #FFFFFF;margin:5px;}
#hbd_table th{font-weight:bold;font-size:12px;color:#174b84;padding:10px 10px 10px;background:#f9fab6;}
#hbd_table td{color:#174b84;border-top:1px dashed #d3dae1;padding:3px;}
#hbd_table tbody tr:hover td{color:#339;background:#f9fab6;}
	
	
#tabs {
  overflow: auto;
  width: 100%;
  list-style: none;
  margin: 0;
  padding: 0;
}

#tabs li {
    margin: 0;
    padding: 0;
    float: left;
}

#tabs a {
    box-shadow: -4px 0 0 rgba(0, 0, 0, .2);
    background: #ad1c1c;
    background: linear-gradient(220deg, transparent 10px, #CCCCCC 10px);

    color: #649ccc;
    float: left;
    font: bold 12px/35px 'Lucida sans', Arial, Helvetica;
    height: 30px;
    padding: 0 30px;
    text-decoration: none;
}

#tabs a:hover {
    background: #2c83cf;
	color: #215786;
    background: linear-gradient(220deg, transparent 10px, #CCCCCC 10px);     
}

#tabs a:focus {
	background: #f0cd40;
    outline: 0;
}

#tabs #current a {
    background: #CCCCCC;
    background: linear-gradient(220deg, transparent 10px, #FFFFFF 10px);
	color: #b65610;
    text-shadow: none;    
}

#content {
    background-color: #f5f9fd;
    background-image:         linear-gradient(top, #fff, #ddd);
    border-radius: 0 2px 2px 2px;
    box-shadow: 0 2px 2px #000, 0 -1px 0 #fff inset;
    padding: 30px;
	/*height: 700px;
	overflow:auto;*/
}

/* Remove the rule below if you want the content to be "organic" */
#content div {
   /* height: 220px; */
   background-color:#FFFFFF;
}

.fontfrom{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:20px; color:#263de0;}

.textbox1{
height:40px; font-size:20px; width:200px;
}
.textbox2{
height:30px; font-size:20px; width:200px; color:#FF0000;  border:2; border-bottom-color:#FF0000; border-left-color:#FF0000; border-right-color:#FF0000; border-top-color:#FF0000;
}

.listbox1{
height:30px; font-size:26px; 
}

.show_line{
background-color:#E0E0E0; display:block; height:1px;
}

#g{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:80%;
text-align:left;border-collapse:collapse;border:1px solid #FFFFFF;margin:2px;}
#g th{font-weight:bold;font-size:12px;color:#174b84;padding:2px 2px 2px;background:#dfe8f0;}
#g tbody{background:#edeeef;}
#g td{color:#174b84;border-top:1px dashed #d3dae1;padding:3px;}



</style>



<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		//from_search();
	} );
</script>



	
<script>

var pathidcard="../../../download_promotion/id_card_quick/";
function find_profile_old(member_no) {
	var pathajax=pathidcard+"find_profile_old.php";
	$.get(pathajax,{
					member_no:member_no,
					ran:Math.random()},function(data){
		
						var arr=data.split("@@");
						$("#customer_id").val(arr[0]);
						$("#id_card").val(arr[1]);
						$("#fname").val(arr[2]);
						$("#lname").val(arr[3]);
						$("#mobile_no_old").val(arr[5]);
						$("#member_no").val(arr[6]);

						var hbd=arr[4];
						var arr_hbd=hbd.split("-");
						var hbd_day=arr_hbd[2];
						var hbd_month=arr_hbd[1];
						var hbd_year=arr_hbd[0];
						
						document.getElementById("hbd_day").value =parseInt(hbd_day,10);
						document.getElementById("hbd_month").value =parseInt(hbd_month,10);
						document.getElementById("hbd_year").value =parseInt(hbd_year,10);
						
						document.getElementById("send_address").value =arr[7];
						document.getElementById("send_mu").value =arr[8];
						document.getElementById("send_home_name").value =arr[9];
						document.getElementById("send_soi").value =arr[10];
						document.getElementById("send_road").value =arr[11];
						document.getElementById("send_province_id").value =arr[16];
						if(document.getElementById("send_province_id").value!=0 || document.getElementById("send_province_id").value!=""){
							
							showamphur_edit(arr[14],arr[12]);
						}
						document.getElementById("send_postcode").value =arr[18];
						document.getElementById("send_fax").value =arr[19];
						document.getElementById("email_").value =arr[20];
						
					

						
						
						
						
						
						

	}); 
}	




	
</script>


<body>
<div id='popup_snapx'  style="display:none;"  title="ถ่ายรูป" >

</div>

<div align="left">
  <?php
$com_ip=$_SERVER['REMOTE_ADDR'];
?>
  <input name="status_readcard" type="text" id="status_readcard" size='5' value='MANUAL' readonly="true" style="color:#0066FF; background-color:#FFFFFF; font-size:16px"/>
  <input name="status_photo" type="hidden" id="status_photo" size='5' value='' />
  <input name="num_snap" type="hidden" id="num_snap" size='5' value=0 />
  <input name="id_img" type="hidden" id="id_img" />
  <input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>
  <input name="fname_en" type="hidden" id="fname_en" />
  <input name="lname_en" type="hidden" id="lname_en" />
  <input name="address" type="hidden" id="address" />
  <input name="mu" type="hidden" id="mu" />
  <input name="tambon_name" type="hidden" id="tambon_name" />
  <input name="amphur_name" type="hidden" id="amphur_name" />
  <input name="province_name" type="hidden" id="province_name" />
  <input name="mr" type="hidden" id="mr" />
  <input name="sex" type="hidden" id="sex" />
  <input name="mr_en" type="hidden" id="mr_en" />
  <input name="card_at" type="hidden" id="card_at" size="50" />
  <input name="start_date" type="hidden" id="start_date" />
  <input name="end_date" type="hidden" id="end_date" />
  <input name="nothai" type="hidden" id="nothai" />
</div>
  <div id="fromreadprofile" >
  <table border="0" align="center">
    <tr>
      <td><table border="0" align="center" cellpadding="5" cellspacing="5">
        <tr>
          <td colspan="4"><div align="center" class="style1">อ่านข้อมูลบัตรประชาชน</div></td>
        </tr>
        <tr>
          <td colspan="4"><div align="center"><img src="../../../download_promotion/id_card_quick/icon_idcard.png" width="50" height="50" onclick="onreadidcheck();"/></div></td>
          </tr>
        <tr>
          <td class="style1">รหัสบัตรประชาชน</td>
          <td><input name="id_card" type="text" id="id_card"  style="width:200px; height:50px;font-size:24px;color:#0066FF;" onkeydown="if (event.keyCode == 13){document.getElementById('fname').focus();}"/></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1">ชื่อ</td>
          <td><input name="fname" type="text" id="fname"  style="width:200px; height:50px;font-size:24px;color:#0066FF;" onkeydown="if (event.keyCode == 13){document.getElementById('lname').focus();}"/></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1">นามสกุล</td>
          <td><input name="lname" type="text" id="lname"  style="width:200px; height:50px;font-size:24px;color:#0066FF;" onkeydown="if (event.keyCode == 13){document.getElementById('birthday').focus();}"/></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1">วันเกิด</td>
          <td><input name="birthday" type="text" id="birthday"  style="width:200px; height:50px;font-size:24px;color:#0066FF;" /></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" class="style1"><div id='showfromotpcode'>
		  <fieldset title="Confirm OTP CODE" style="border-bottom-color:#99CC33; border-left-color:#99CC33; border-right-color:#99CC33; border-top-color:#99CC33;">
		  <legend style="style15">Confirm OTP CODE</legend>
            <table border="0">
              <tr>
                <td><span class="style2">กดปุ่มส่ง OTP CODE เข้ามือถือสมาชิก 
                  
                </span></td>
                <td><input name="Button" type="button" class="style1" value=" ส่ง OTP CODE " onclick="apisendotp('<?php echo $_GET[mobile_no];?>');" /></td>
              </tr>
              <tr>
                <td><span class="style2">ใส่ OTP CODE ที่ได้จากมือถือสมาชิกที่ช่องนี้ </span></td>
                <td><input name="otpcode" type="text" id="otpcode"  style="width:200px; height:50px;font-size:24px;color:#99CC33;" onkeydown="if (event.keyCode == 13){document.getElementById('fname').focus();}"/></td>
              </tr>
            </table>
		  </fieldset>
          </div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        

      </table></td>
      <td style="border-left:1px; dashed #000;"><p align="center" class="style1 style2">&nbsp;</p>      </td>
      <td valign="top" style="border-left:1px; dashed #000;">

		  <table width="200" border="0" align="center">
			<tr>
			  <td><div align="center" class="style1">ถ่ายรูป</div></td>
			</tr>
			<tr>
			  <td><div align="center"><img src="../../../download_promotion/id_card_quick/camera.jpeg" width="50" onclick="chk_snap();"/></div></td>
			</tr>
			<tr>
			  <td><div id='show_photo'>
				<div align="center"></div>
			  </div></td>
			</tr>
		  </table>
	  </td>
    </tr>
  </table>
</div>


</body>


