<style type="text/css">
<!--
.style1 {color: #0066FF;
font-size:20px;
}
.block_photo{
  background: none repeat scroll 0 0 rgba(222, 56, 50, 0.85);
    border-color: rgba(0, 0, 0, 0.1) !important;
    border-style: solid !important;
    border-width: 2px !important;
    color: #FFFFFF !important;
    font-size: 1rem;
    font-style: italic;
    margin: 0 0 20px;
    padding: 3px;
	width:300px;
	hieght:300px;
}
.style4 {
	color: #FF0000;
	font-size: 22px;
}
-->
</style>
<table border="0">
  <tr>
    <td  valign="middle">    </td>
  </tr>
  <tr>
    <td valign="top" bgcolor="#bbe0e3" class='label1'><div align="center">
      <input name="button2" type="button" id="get_idcard" onclick="onreadidcheck()"  class='btn1' style="font-size: 14px;" value="อ่านข้อมูลจากบัตรประชาชน" />
      <input name="address1" type="hidden" id="address1" />
      <input name="address2" type="hidden" id="address2" />
      <input name="address3" type="hidden" id="address3" />
      <input name="address4" type="hidden" id="address4" />
      <input name="address5" type="hidden" id="address5" />
      <input name="mr" type="hidden" id="mr" />
      <input name="sex" type="hidden" id="sex" />
      <input name="mr_en" type="hidden" id="mr_en" />
      <input name="fname_en" type="hidden" id="fname_en" />
      <input name="lname_en" type="hidden" id="lname_en" />
      <input name="card_at" type="hidden" id="card_at" size="50" />
      <input name="start_date" type="hidden" id="start_date" />
      <input name="end_date" type="hidden" id="end_date" />
    </div></td>
  </tr>
  <tr>
    <td width="567" valign="top" bgcolor="#bbe0e3" class='label1'><table border="0">
      <tr>
        <td class='label1'>รหัสบัตรประชาชน</td>
        <td ><label>
          <input name="id_card" type="text" id="id_card"  class='text1' onKeyDown="if (event.keyCode == 13){document.getElementById('fname').focus();}"/>
        </label>          </td>
      </tr>
      <tr>
        <td class='label1'>ชื่อ</td>
        <td><input name="fname" type="text" id="fname" class='text1' size='10' onKeyDown="if (event.keyCode == 13){document.getElementById('lname').focus();}"/>
          &nbsp;&nbsp;นามสกุล
          <input name="lname" type="text" id="lname" class='text1' size='10'/></td>
      </tr>
      <tr>
        <td class='label1'>วันเกิด</td>
        <td><input name="hbd" type="hidden" id="hbd" class='text1' size='10' />
              <select name="hbd_day" id='hbd_day'>
                <?php
			  for($numday=1; $numday<=31; $numday++){
					echo "<option value='$numday'>$numday</option>";
			
			  }
			  ?>
              </select>
              <select name="hbd_month" id='hbd_month'>
                <option value="1" <?php echo $month_select1; ?> >1-มกราคม</option>
                <option value="2" <?php echo $month_select2; ?> >2-กุมภาพันธ์</option>
                <option value="3" <?php echo $month_select3; ?> >3-มีนาคม</option>
                <option value="4" <?php echo $month_select4; ?> >4-เมษายน</option>
                <option value="5" <?php echo $month_select5; ?> >5-พฤษภาคม</option>
                <option value="6" <?php echo $month_select6; ?> >6-มิถุนายน</option>
                <option value="7" <?php echo $month_select7; ?> >7-กรกฎาคม</option>
                <option value="8" <?php echo $month_select8; ?> >8-สิงหาคม</option>
                <option value="9" <?php echo $month_select9; ?> >9-กันยายน</option>
                <option value="10" <?php echo $month_select10; ?> >10-ตุลาคม</option>
                <option value="11" <?php echo $month_select11; ?> >11-พฤศจิกายน</option>
                <option value="12" <?php echo $month_select12; ?> >12-ธันวาคม</option>
              </select>
              <select name="hbd_year" id='hbd_year'>
                <?php
			   $numyear_this=date("Y");
				for($numyear=$numyear_this-200; $numyear<=$numyear_this-10; $numyear++){
					$numyear_thai=$numyear+543;
					echo "<option value='$numyear'>ค.ศ.$numyear / พ.ศ.$numyear_thai</option>";
			

			  }
			  ?>
              </select>        </td>
      </tr>
    </table></td>

  </tr>
</table>
