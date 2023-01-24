<table >
  <tr>
    <td colspan="5" class='fontfrom'><span align="center">ที่อยู่การจัดส่งเอกสาร</span>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="status_no_address" type="checkbox" id="status_no_address" value="checkbox" /><span style="color:#FF0000;" onclick="if(document.getElementById('status_no_address').checked==true){document.getElementById('status_no_address').checked=false;}else{document.getElementById('status_no_address').checked=true;}">
      
    สมาชิกไม่ให้ที่อยู่</span></td>
    
  </tr>
  <tr>
    <td class='fontfrom'>บ้านเลขที่</td>
    <td ><label>
      <input name="send_address" type="text" id="send_address"  class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('send_mu').focus();}"/>
      </label>    </td>
    <td >&nbsp;</td>
    <td class='fontfrom'>หมู่</td>
    <td ><input name="send_mu" type="text" id="send_mu" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('send_home_name').focus();}"/></td>
  </tr>
  <tr>
    <td class='fontfrom'>ชื่อหมู่บ้าน</td>
    <td><input name="send_home_name" type="text" id="send_home_name" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('send_soi').focus();}"/>
      &nbsp;&nbsp;</td>
    <td>&nbsp;</td>
    <td class='fontfrom'>ซอย</td>
    <td><input name="send_soi" type="text" id="send_soi" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('send_road').focus();}"/></td>
  </tr>
  <tr>
    <td class='fontfrom'>ถนน</td>
    <td><input name="send_road" type="text" id="send_road" class='textbox1' /></td>
    <td>&nbsp;</td>
    <td class='fontfrom'>จังหวัด</td>
    <td ><?php include("find_province.php"); ?></td>
  </tr>
  <tr>
    <td class='fontfrom'>แขวง</td>
    <td><span id='showsend_amphur'></span></td>
    <td>&nbsp;</td>
    <td><span class='fontfrom'>เขต</span></td>
    <td><span id='showsend_tambon'></span></td>
  </tr>
  <tr>
    <td class='fontfrom'>รหัสไปรษณีย์</td>
    <td><input name="send_postcode" type="text" id="send_postcode" readonly="true" class='textbox1'/></td>
    <td>&nbsp;</td>
    <td class='fontfrom'>FAX</td>
    <td><input name="send_fax" type="text" id="send_fax" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('email_').focus();}"/></td>
  </tr>
  <tr>
    <td class='fontfrom'>E-mail</td>
    <td><input name="email_" type="text" id="email_" class='textbox1'/></td>
    <td>&nbsp;</td>
    <td class='fontfrom'>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
