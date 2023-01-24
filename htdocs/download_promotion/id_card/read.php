<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<APPLET CODE = 'idcard.RdNationalCardID' archive='../../download_promotion/id_card/lib/RdNIDApplet090DL.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>  
<link href="/sales/css/promotion/newmember.css" rel="stylesheet">
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


		
		document.getElementById('status_readcard').value="Scan";
		
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
		
		var data = document.NIDApplet.getNIDPictureRD();
		document.getElementById('show_pic').innerHTML="<img width='150' src='data:image/png;base64,"+data+"'>";

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

function from_search(){

	    $.get("../../download_promotion/id_card/from_search.php",{
		    	    	ran:Math.random()},function(data){
						$("#from_search").html(data);
						$("#id_card").focus();
	    }); 

}
function from_photo(){

    $.get("../../download_promotion/id_card/from_photo.php",{
	    	    	ran:Math.random()},function(data){
					$("#from_search").html(data);
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

    <input name="status_readcard" type="hidden" id="status_readcard" size='5' value='Manual'/>
    <input name="chk_nation" type="hidden" id="chk_nation" size='5' value='thai'/>
    <input name="chk_application_id" type="hidden" id="chk_application_id" size='5' value='thai'/>
    
<table align='center'>
<tr>

<td>
<input name="nation" type="radio" value="thai" checked="checked" onclick="$('#chk_nation').val('thai'); from_search();"/>
  <span style="color:#FF9966; font-size:28px">คนไทย</span>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="nation" type="radio" value="no" id='nation_no' onclick="$('#chk_nation').val('nothai'); alert('ในช่วงเดือน พฤษภาคม - มิถุนายน 2557 ชาวต่างชาติไม่สามารถสมัครสมาชิกใหม่ได้'); return false;  "/>
<span style="color:#0066CC; font-size:28px">ชาวต่างชาติ</span>
</td></tr>
</table>
<br>

		
<div id='from_search' align='center'></div>		


