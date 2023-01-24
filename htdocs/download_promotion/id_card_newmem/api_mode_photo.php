<?php
$com_ip=$_SERVER['REMOTE_ADDR'];
?>
<style type="text/css">
<!--
.textmanual {color: #376092; font-size:24px;}
.style2 {color: #067db7; font-size:20px;}
-->
</style>

<input name="status_photo" type="hidden" id="status_photo" size='5' value='' />
<input name="num_snap" type="hidden" id="num_snap" size='5' value="0" />
<input name="id_img" type="hidden" id="id_img" />
<input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>
<input name="status_readcard" type="hidden" id="status_readcard" />


<span class="textmanual">
<input name="function_next" type="hidden" id="function_next" value='<?php echo $_GET['function_next']; ?>'/>
</span>

<div id='lastpro_play'>
<table align="center" cellpadding="3" cellspacing="3">	
  <tr>
    <td class='style3'>1.</td>
    <td class='textmanual'>เลือกสัญชาติ</td>
    <td><input name="mynation" type="radio" value="1" checked="checked" id="nation1" onclick="$('#label_id').html('ใส่เลขที่บัตรประจำตัวประชาชน');" />
      <span class="textmanual">ไทย</span>
      <input name="mynation" type="radio" value="2" id="nation2" onclick="$('#label_id').html('ใส่เลขที่ Passport');" />
      <span class="textmanual">ต่างชาต</span></td>
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
    <td><span id='from_val_otp' ><input  name='var_otp' type='text' id='var_otp'   style="color:#99CC00;" size="10"/></span></td>
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
</div>
