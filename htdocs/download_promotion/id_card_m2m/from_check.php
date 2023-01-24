<?php
/*include("connect.php");
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$find_shop="select *  from com_branch_computer limit 0,1";
$run_shop=mysql_query($find_shop,$conn_local);
$data_shop=mysql_fetch_array($run_shop);
$shop=$data_shop['branch_id'];
$com_ip=$data_shop['com_ip'];
mysql_close($conn_local);*/
$com_ip=$_SERVER['REMOTE_ADDR'];
		
?>
 <applet code = 'idcard.RdNationalCardID' name='NIDApplet' width = "0" height = "0" archive='../../download_promotion/id_card/lib/RdNIDApplet090DL.jar' id="NIDApplet">
</applet>
<script>
function readIDCARD(){

		if(!document.NIDApplet.isCardInsertRD()){
			alert("คุณยังไม่ได้ Scan บัตรประชาชน");
			return false;
		}

		var id_card = document.NIDApplet.getNIDNumberRD();
		document.getElementById('id_card').value=id_card;

}

</script>
<style type="text/css">
<!--

.x {
	font-size: 24px;
	color: #376092;
}

.style3 {
	font-size: 36px;
	color: #9bbb59;
}


-->
</style>


<table border="0" align="center" cellpadding="3" cellspacing="3">
  
  <tr>
    <td colspan="2" class='style1'><p align="center" class="style3">

      ตรวจสอบสิทธิ์<span style="color:#FF0000;">(สำหรับผู้ถูกชวน)</span><br>ว่าสามารถสมัครสมาชิกใหม่แบบเพื่อนชวนเพื่อนได้หรือไม่</p>
    <div align="center">    </div></td>
  </tr>
  <tr>
    <td class='x'>1. ใส่รหัส Ecoupon Code จากมือถือ<span style="color:#FF0000;">ผู้ถูกชวน</span>ที่ช่องนี้</td>
    <td ><label>
      <input name="coupon_code" type="text" id="coupon_code"   onkeydown="if (event.keyCode == 13){$('#id_card').focus();}"/>
      </label>    </td>
  </tr>
  <tr>
    <td class='x'>2. ใส่รหัสบัตรประชาชน<span style="color:#FF0000;">ผู้ถูกชวน</span>ที่ช่องนี้<br>
    &nbsp;&nbsp;หรือเสียบบัตรประชาชนลูกค้าแล้วกดปุ่มนี้ <img  style="cursor:pointer;" onclick="readIDCARD();" src="../../download_promotion/id_card_m2m/icon_idcard.png" width="30opx" height="30px"></td>
    <td><input name="id_card" type="text" id="id_card"   onkeydown="if (event.keyCode == 13){m2mfromcheck_ans();}"/></td>
  </tr>
  <tr>
    <td class='x'>3. กดปุ่มนี้เพื่อตรวจสอบสิทธิ์</td>
    <td><input name="button" type="button"  onclick="m2mfromcheck_ans();" value="ตรวจสอบสิทธิ์"/></td>
  </tr>
  <tr>
    <td colspan="2" class='x' ><fieldset>
      <legend><span style="color:#FF0000;">ข้อมูลผู้ชวน(ตัวแม่)</span></legend>
      <input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>
      <input name="memold_customer_id" type="hidden" id="memold_customer_id" />
      <input name="memold_member_no" type="hidden" id="memold_member_no" />
      <input name="friend_id_card" type="hidden" id="friend_id_card" />
      <input name="friend_mobile_no" type="hidden" id="friend_mobile_no" />
      <input name="promo_code_play" type="hidden" id="promo_code_play" />
      <input name="promo_code" type="hidden" id="promo_code" />
      <table border="0" align="center">
        <tr>
          <td class='x'>ชื่อ-สกุล</td>
          <td><input name="hid_name" type="text" id="hid_name"  readonly="true" style="background-color:#FFFFFF; font-size:16px; width:150px; border:0;"/></td>
          <td>&nbsp;</td>
          <td class='x'>รหัสบัตรปชช</td>
          <td><input name="memold_id_card" type="text" id="memold_id_card"  readonly="true"  style="background-color:#FFFFFF; font-size:16px; width:150px;  border:0;"/></td>
        </tr>
        <tr>
          <td class='x'>เบอร์โทร</td>
          <td><input name="memold_mobile_no" type="text" id="memold_mobile_no" readonly="true" style="background-color:#FFFFFF; font-size:16px; width:150px;  border:0;"/></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </fieldset>    </td>
  </tr>
  <tr>
    <td colspan="2" class='x' ><fieldset style="height:100px;"><legend>ผลการตรวจสอบ</legend>
	<div id='show_limit' style="color:#d94531; font-size:20px;"></div>
	<div id='show_ans' style="color:#d94531; font-size:20px;"></div>
	<div id='show_button' style="color:#d94531; font-size:20px;"></div>
	</fieldset> </td>
  </tr>
</table>
