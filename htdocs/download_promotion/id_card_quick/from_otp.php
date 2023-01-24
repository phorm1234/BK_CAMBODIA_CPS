<?php 
$id_card=$_GET['id_card'];
$fname=$_GET['fname'];
$lname=$_GET['lname'];
$mobile_otp=$_GET['mobile_otp'];
$member_no=$_GET['member_no'];

$customer_id=$_GET['customer_id'];
$status_readcard=$_GET['status_readcard'];
$status_no_address=$_GET['status_no_address'];

?>
<style>
	/*.ui-widget-header
	{
		background-color: #0e1c32;
		background-image: none;
		color: #FFFFFF;
	}*/

</style>

<style>
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

</style>
<table border="0" align="center" cellpadding="5" cellspacing="5">
  <tr>
    <td class='fontfrom'>ID CARD </td>
    <td class='fontfrom'><input name="show_id_card" type="text" id="show_id_card" value='<?php echo $id_card; ?>' readonly="true"/></td>
  </tr>
  <tr>
    <td class='fontfrom'>ชื่อ - นามสกุล </td>
    <td class='fontfrom'><input name="show_fullname" type="text" id="show_fullname" value='<?php echo "$fname $lname"; ?>' readonly="true"/></td>
  </tr>
  <tr>
    <td class='fontfrom'>Mobile_no</td>
    <td class='fontfrom'><input name="show_mobile" type="text" id="show_mobile" value='<?php echo $mobile_otp; ?>' readonly="true" /></td>
  </tr>
  <tr>
    <td colspan="2" class='fontfrom'><span class="show_line">&nbsp;</span></td>
  </tr>
  <tr>
    <td class='fontfrom'>OTP CODE </td>
    <td><input name="otp_confirm" type="text" id="otp_confirm" class='textbox2' onkeydown="if (event.keyCode == 13){from_otp_chk('<?php echo $id_card; ?>','<?php echo $member_no; ?>','<?php echo $mobile_otp; ?>','<?php echo $customer_id; ?>','<?php echo $status_readcard; ?>','<?php echo $status_no_address; ?>');}"/>&nbsp;&nbsp;<a href="#" style="color:#009900; cursor:pointer; font-size:20px;"  onclick="from_otp_gen('<?php echo $id_card; ?>','<?php echo $member_no; ?>','<?php echo $mobile_otp; ?>');">ส่ง OTP CODE อีกครั้ง</a>  &nbsp;&nbsp; <span id='otp_code' ></span></td>
  </tr>
</table>
