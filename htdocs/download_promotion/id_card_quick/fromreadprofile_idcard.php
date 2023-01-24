<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<style type="text/css">
<!--
.style1 {color: #0066FF; font-size:28px;}
-->
</style>




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


.style2 {font-size: 36px}
</style>



<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		//from_search();
	} );
</script>






<body>
<table border="0" align="center">
  <tr>
    <td height="100" colspan="3"><div align="center" >
      <table border="0" cellpadding="3" cellspacing="3">
        <tr>
          <td><img src="../../../download_promotion/id_card_quick/id_card.jpeg" width="65px" height="50px" /></td>
          <td><span style="font-size:28px; color:#99CC00;">โปรโมชั่น : </span>
                <input name="promo_code" type="text" id="promo_code"  style="color:#0066FF; font-size:28px; background-color:#FFFFFF; border:0px;"/></td>
        </tr>
      </table>
    </div>
        <center>
          <div id='promo_des' style="font-size:28px; color:#99CC00;"></div>
          <hr style="color:#17a009" />
        </center></td>
  </tr>
  <tr>
    <td><table border="0" align="center" cellpadding="5" cellspacing="5">
      <tr>
        <td class="style1"><div id='show_label_coupon'><span font-size:28px;="font-size:28px;"">E-coupon :</span></div></td>
        <td><div id='show_text_coupon'>
          <input name="otp_code" type="text" id="otp_code" style="width:200px; height:50px; font-size:26px;color:#f07938;" onKeyDown="if (event.keyCode == 13){fromreadprofile_idcard_save();}" />
        </div>
		<div id='show_msg' style='font-size:28px; color:#FF0000;'></div></td>
        <td><input name="chk_ecoupon" type="hidden" id="chk_ecoupon"/>
        <input name="id_card" type="hidden" id="id_card"/></td>
        <td><input name="mobile_no" type="hidden" id="mobile_no" /></td>
      </tr>
    </table></td>
    <td style="border-left:1px; dashed #000;"><input name="member_no" type="hidden" id="member_no" /></td>
    <td valign="top" style="border-left:1px; dashed #000;">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><hr style="color:#17a009" /></td>
  </tr>
  <tr>
    <td colspan="3"><br />
        <br />
      <div align="center"></div></td>
  </tr>
</table>
</body>



