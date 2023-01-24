<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta charset="utf-8">
	
<APPLET CODE = 'idcard.RdNationalCardID' archive='joke_dicard/RdNIDApplet.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>  
<script>

function readIDCARD(){

		if(!document.NIDApplet.isCardInsertRD()){
			alert("คุณยังไม่ได้ Scan บัตรประชาชน");
			return false;
		}

		var id_card = document.NIDApplet.getNIDNumberRD();
		alert(id_card);

		document.getElementById('id_card').value=id_card;



}
function onreadidcheck(){
	if(!document.NIDApplet.isCardInsertRD()){
		alert("คุณยังไม่ได้ Scan บัตรประชาชน");
		return false;
	}


	if(document.NIDApplet.isCardInsertRD()){

		var data = document.NIDApplet.getNIDDataRD();
		alert(data);
		var data_arr = data.split('#');

		//var photo = document.NIDApplet.getNIDPictureRD();
		//$('img_from_card_reader').innerHTML="<img width='150' src='data:image/png;base64,"+photo+"'>";


		
		// _d[0] บัตรประชาชน
		/*var _id1=_d[0].substr(0,1);
		var _id2=_d[0].substr(1,4);
		var _id3=_d[0].substr(5,5);
		var _id4=_d[0].substr(10,2);
		var _id5=_d[0].substr(12,1);
	
		document.getElementById('idcard_1').value=_id1;
		document.getElementById('idcard_2').value=_id2;
		document.getElementById('idcard_3').value=_id3;
		document.getElementById('idcard_4').value=_id4;
		document.getElementById('idcard_5').value=_id5;*/

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
</script>





ข้อมูลส่วนตัว (Personal Information)&nbsp;<br>
<table border="0">
  <tr>
    <td width="144"><input name="button" type="button" id="button" onClick="readIDCARD()" value="อ่าน IDCARD อย่างเดียว"></td>
    <td width="148"><input name="button2" type="button" id="get_idcard" onClick="onreadidcheck()" value="อ่านข้อมูลทั้งหมด"></td>
    <td width="148"><input name="button22" type="button" id="button2" onclick="readPic()" value="อ่านรูปภาพ" /></td>
    <td width="148">&nbsp;</td>
    <td width="181">&nbsp;</td>
  </tr>
  <tr>
    <td>รหัสบัตรประชาชน</td>
    <td><label>
      <input name="id_card" type="text" id="id_card" />
    </label></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td rowspan="5" valign="top"><div align="center" id='show_pic'>แสดงรูป</div></td>
  </tr>
  <tr>
    <td>ชื่อ</td>
    <td><input name="mr" type="text" id="mr"></td>
    <td><input name="fname" type="text" id="fname"></td>
    <td><input name="lname" type="text" id="lname"></td>
  </tr>
  <tr>
    <td>Name</td>
    <td><input name="mr_en" type="text" id="mr_en"></td>
    <td><label>
      <input name="fname_en" type="text" id="fname_en">
    </label></td>
    <td><label>
      <input name="lname_en" type="text" id="lname_en">
    </label></td>
  </tr>
  <tr>
    <td>บ้านเลขที่</td>
    <td><label>
      <input name="address1" type="text" id="address1">
    </label></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>หมู่ที่</td>
    <td><input name="address2" type="text" id="address2"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>ตำบล</td>
    <td><input name="address3" type="text" id="address3"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>อำเภอ</td>
    <td><input name="address4" type="text" id="address4"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>จังหวัด</td>
    <td><input name="address5" type="text" id="address5"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>เพศ</td>
    <td><input name="sex" type="text" id="sex"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>วันเกิด</td>
    <td><input name="hbd" type="text" id="hbd"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>หน่วยงานที่ออกบัตร</td>
    <td colspan="4"><input name="card_at" type="text" id="card_at" size="50"></td>
  </tr>
  <tr>
    <td>วันออกบัตร</td>
    <td><input name="start_date" type="text" id="start_date"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>วันบัตรหมดอายุ</td>
    <td><input name="end_date" type="text" id="end_date"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
