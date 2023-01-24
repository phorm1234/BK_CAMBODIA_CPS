<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
include("connect.php");
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_select_db($db_local);

?>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<style type="text/css">
<!--
.style1 {color: #0066FF; font-size:28px;}
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
	
	
	if(document.getElementById('promo_code').value!="OPPLI300" && document.getElementById('promo_code').value!="I06470416"){
		document.getElementById('show_btn_send_otp').style.visibility='hidden';
		document.getElementById('show_label_otp').style.visibility='hidden';
		document.getElementById('val_otp').style.visibility='hidden';
		
		document.getElementById('show_label_mobile').style.visibility='hidden';
		document.getElementById('mobile_no').style.visibility='hidden';		
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
	if(document.getElementById("nation2").checked==false){
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


.style2 {font-size: 36px}
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


function chk_passport(){
	var doc_date=$("#csh_doc_date").html();
	var date_arr=doc_date.split("/");
	var sys_day=date_arr[0];
	var sys_month=date_arr[1];
	var sys_year=date_arr[2];
	
	
    if(parseInt(sys_year,10)==2017 && parseInt(sys_month,10)>=2){
		if($("#promo_code").val()=="OPID300" || $("#promo_code").val()=="OPPGI300"  || $("#promo_code").val()=="OPPLI300"  || $("#promo_code").val()=="OPKTC300"   || $("#promo_code").val()=="OPTRUE300"  || $("#promo_code").val().substring(0,3)=="OPN" ){
			alert('การสมัครสมาชิกในรายการนี้สงวนสิทธิ์เฉพาะคนไทยเท่านั้น ชาวต่างชาติไม่สามารถสมัครสมาชิกได้');
			document.getElementById("nation2").checked = false;
			document.getElementById("nation1").checked = true;
			return false;
		}
	 } 
	 

	 $('#label_id').html('เลขที่ Passport');
	 document.getElementById("show_country").style.visibility="visible";
}

function chk_idcard(){
	var doc_date=$("#csh_doc_date").html();
	var date_arr=doc_date.split("/");
	var sys_day=date_arr[0];
	var sys_month=date_arr[1];
	var sys_year=date_arr[2];
	
    if($("#promo_code").val()=="OX02460217" || $("#promo_code").val()=="OX02460217_2" || $("#promo_code").val()=="OX02250117" || $("#promo_code").val()=="TOUR01"  ){
			alert('โปรนี้สงวนสิทธิ์เฉพาะชาวต่างชาติเท่านั้น คนไทยไม่สามารถร่วมรายการได้');
			document.getElementById("nation2").checked = true;
			document.getElementById("nation1").checked = false;
			 $('#label_id').html('เลขที่ Passport');
			return false;
	 } 
	
	 $('#label_id').html('เลขที่บัตรประชาชน');
	 document.getElementById("show_country").style.visibility="hidden";
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
    
  <input name="birthday" type="hidden" id="birthday" />
</div>
  <div id="fromreadprofile" >
  <table border="0" align="center">
    <tr>
      <td height="100" colspan="3"><div align="center" >
        <table border="0" cellpadding="3" cellspacing="3">
            <tr>
              <td><img src="../../../download_promotion/id_card_quick/icon_line.jpeg" width="50px" height="50px" /></td>
              <td><span style="font-size:28px; color:#99CC00;">Promotion : </span>
              <input name="promo_code" type="text" id="promo_code"  style="color:#0066FF; font-size:28px; background-color:#FFFFFF; border:0px;"/></td>
            </tr>
          </table>
      </div><center><div id='promo_des' style="font-size:28px; color:#99CC00;"></div><hr style="color:#17a009"></center></td>
    </tr>
    <tr>
      <td><table border="0" align="center" cellpadding="3" cellspacing="3">
        <tr>
          <td colspan="4"><div align="center" class="style1">Reader ID Card</div></td>
        </tr>
        <tr>
          <td colspan="4"><div align="center"><img src="../../../download_promotion/id_card_quick/icon_idcard.png" width="50" height="50" onclick="onreadidcheck();"/></div></td>
          </tr>
        <tr>
          <td id="Nationality" class="style1">Nationality</td>
          <td><input name="mynation" type="radio" value="1" checked="checked" id="nation1" onclick="chk_idcard();"/>
              <span id="Thai" class="style1">Thai</span>
              <input name="mynation" type="radio" value="2" id="nation2" onclick="chk_passport();" />
              <span id="Foreign" class="style1">Foreign</span></td>

          <td><script>document.getElementById("show_country").style.visibility="hidden";</script>		  </td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" class="style1"><div id="show_country1" class="style1">Select Country
            <?php
			$find_country="SELECT *  FROM com_country_master WHERE country_status = 'Y' order by country_name_en ";
			$run_country=mysql_query($find_country,$conn_local);
			$rows_country=mysql_num_rows($run_country);
		  
		  ?>
                <select id="country_code" name="country_code" class="style1" style="font-size:20px;">
                  <option value=''>Please select country</option>
                  <?php
		  for($i=1; $i<=$rows_country; $i++){
		  	$datacountry=mysql_fetch_array($run_country);
			echo "<option value='$datacountry[country_code]'>$datacountry[country_name_en]  ($datacountry[country_name_th])</option>";
		  }
		  ?>
                </select>
          </div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1"><div id='label_id'>Id Number</div></td>
          <td><input name="id_card" type="text" id="id_card"  style="width:200px; height:50px;font-size:24px;color:#0066FF;" /></td>
          <td><input name="nothai" type="hidden" id="nothai" value="checkbox" />
            <input name="fname" type="hidden" id="fname" style="width:200px; height:50px" onkeydown="if (event.keyCode == 13){document.getElementById('lname').focus();}"/>
            <input name="lname" type="hidden" id="lname" style="width:200px; height:50px" onkeydown="if (event.keyCode == 13){document.getElementById('mobile_no').focus();}"/>
            <input name="member_no" type="hidden" id="member_no" /></td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td class="style1"><div id='show_label_coupon'><span font-size:28px;">E-coupon :</span></div></td>
          <td><div id='show_text_coupon'><input name="otp_code" type="text" id="otp_code" style="width:200px; height:50px; font-size:26px;color:#f07938;" /></div></td>
          <td><input name="chk_ecoupon" type="hidden" id="chk_ecoupon" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1"><div id='show_label_mobile'><span >Mobile Number :</span></div></td>
          <td><div id='show_input_mobile'><input name="mobile_no" type="text" id="mobile_no" style="width:200px; height:50px; font-size:24px;color:#0066FF;"/></div></td>
          <td><div id='show_btn_send_otp'><input name="Button" type="button" style="color:17a009; font-size:20px;" value=" Send OTP Code " onclick="fromreadprofile_sendotp();"/></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1">&nbsp;</td>
          <td style='color:#FF0000;'><input name="confirm_mobile" type="hidden" id="confirm_mobile" value="N" width='5px;' /><div id='show_status_confirm_mobile'></div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1"><div id='show_label_otp'><span style="color:17a009; font-size:28px;">OTP CODE Number  :</span></div></td>
          <td><div id='show_input_otp'><input name="val_otp" type="text" id="val_otp" style="width:200px; height:50px; font-size:26px;color:#f07938; border-color:#99CC00; background-color:#caf4c6" /></div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="style1">Email : </td>
          <td colspan="2"><input name="email" type="text" id="email" style="width:250px; height:50px; font-size:24px;color:#0066FF;"/></td>
          <td>&nbsp;</td>
        </tr>
        

      </table></td>
      <td style="border-left:1px; dashed #000;"><p align="center" class="style1 style2">&nbsp;</p>      </td>
      <td valign="top" style="border-left:1px; dashed #000;">

	  
		<table width="200" border="0" align="center">
			<tr>
			  <td><div align="center" class="style1">Photograph</div></td>
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
    <tr>
      <td colspan="3"><hr style="color:#17a009" /></td>
    </tr>
    <tr>
      <td colspan="3"><br><br><div align="center"></div></td>
    </tr>
  </table>
</div>


</body>



