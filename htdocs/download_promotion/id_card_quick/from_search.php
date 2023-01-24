
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
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>

<table>
  <tbody>
  <tr>
    <td>
	   
	  <input name="status_readcard" type="text" id="status_readcard" size='5' value='MANUAL' readonly="true" />
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
      &nbsp;&nbsp;
      <table border="0" align="right" cellpadding="2" cellspacing="2">
        <tr>
          <td height="64"><img src="../../../download_promotion/id_card_quick/icon_idcard.png" width="50" height="50" / onclick="onreadidcheck();" style=" cursor:pointer;" /></td>
          <td><img src="../../../download_promotion/id_card_quick/icon_camera.png" width="60" height="60" onclick="chk_snap();" style=" cursor:pointer;"/>
		
		    </td>
        </tr>
      </table>	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>

	   
    <td><table border="0" align="center" cellpadding="2" cellspacing="2">
      <tr>
        <td valign="middle" class='fontfrom'>รหัสสมาชิก</td>
        <td valign="middle" ><input name="member_no" type="text" id="member_no" class='textbox1'  readonly="true"></td>
        <td valign="middle" >&nbsp;</td>
        <td align="right" valign="middle" class='fontfrom'>&nbsp;</td>
        <td valign="middle" ><input name="customer_id" type="hidden" id="customer_id" /></td>
      </tr>
      <tr>
        <td colspan="5" valign="middle" class='fontfrom'><span class="show_line">&nbsp;</span></td>
        </tr>
      <tr>
        <td valign="middle" class='fontfrom'>รหัสบัตรประชาชน</td>
        <td valign="middle" ><input name="id_card" type="text" id="id_card" style="width:200px;" onkeydown="if (event.keyCode == 13){document.getElementById('fname').focus();}" />
          <input name="nothai" type="checkbox" id="nothai" value="checkbox" />
          <span class="style1">ต่างชาติ</span></td>
        <td valign="middle" >&nbsp;</td>
        <td align="right" valign="middle" class='fontfrom'>วันเกิด          </td>
        <td valign="middle" ><select name="hbd_day" id='hbd_day' class='listbox1' style="font-size:26px; ">
            <?php
			  for($numday=1; $numday<=31; $numday++){
					echo "<option value='$numday'>$numday</option>";
			
			  }
			  ?>
          </select>
            <select name="hbd_month" id='hbd_month' class='listbox1' style="font-size:26px;width:100px;">
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
            <select name="hbd_year" id='hbd_year' class='listbox1' style="font-size:26px; ">
              <?php
			   $numyear_this=date("Y");
				for($numyear=$numyear_this-200; $numyear<=$numyear_this+10; $numyear++){
					$numyear_thai=$numyear+543;
					echo "<option value='$numyear'>ค.ศ.$numyear / พ.ศ.$numyear_thai</option>";
			

			  }
			  ?>
          </select></td>
      </tr>
      <tr>
        <td colspan="5" class='fontfrom'><span class="show_line">&nbsp;</span></td>
        </tr>
      <tr>
        <td valign="middle" class='fontfrom'>ชื่อ</td>
        <td valign="middle"><input name="fname" type="text" id="fname" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('lname').focus();}"/>
          &nbsp;&nbsp;</td>
        <td valign="middle">&nbsp;</td>
        <td align="right" valign="middle" class='fontfrom'>นามสกุล</td>
        <td valign="middle"><input name="lname" type="text" id="lname" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('mobile_no_new').focus();}"/></td>
      </tr>
      <tr>
        <td colspan="5" class='label1'><span class="show_line">&nbsp;</span></td>
        </tr>
      <tr>
        <td valign="middle" class='fontfrom'>เบอร์มือถือเดิม</td>
        <td colspan="4" valign="middle"><input name="mobile_no_old" type="text" id="mobile_no_old" class='textbox1' onkeydown="if (event.keyCode == 13){document.getElementById('lname').focus();}" readonly="true"/ />
          <span class='fontfrom' style="color:#FF0000;"><strong align="right">เปลี่ยนเบอร์ใหม่คือ</strong></span>		
		  <input name="mobile_no_new" type="text" id="mobile_no_new" class='textbox2'  /></td>
        </tr>
    </table></td>
    <td valign="top" align="center">
	<span id='show_photo'  ></span>
	<span id='show_noid_type' style="display:none;">
      <fieldset class="style1" style="border-style: solid;border-color: #98bf21;">
      <legend class="style1" style=" font-size:22px;">เหตุผลที่อ่านบัตร ปชช. ไม่ได้</legend>
      <table border="0">
        <tr>
          <td><input name="radiobutton" type="radio" value="radiobutton" onclick="document.getElementById('noid_type').value=1;" /></td>
          <td  style="font-size:20px; color:#FF0000;">บัตรรุ่นเก่าไม่มีชิพ</td>
        </tr>
        <tr>
          <td><input name="radiobutton" type="radio" value="radiobutton" onclick="document.getElementById('noid_type').value=2;"/></td>
          <td style="font-size:20px; color:#FF0000;">บัตรรุ่นใหม่มีชิพ แต่อ่านไม่ได้ </td>
        </tr>
        <tr>
          <td><input name="radiobutton" type="radio" value="radiobutton" onclick="document.getElementById('noid_type').value=3;"/></td>
          <td style="font-size:20px; color:#FF0000;">อุปกรณ์ ID CARD ไม่ทำงาน </td>
        </tr>
        <tr>
          <td><input name="radiobutton" type="radio" value="radiobutton" onclick="document.getElementById('noid_type').value=4;"/></td>
          <td style="font-size:20px; color:#FF0000;">ลูกค้าไม่ยินยอมให้อ่านบัตร ปปช.</td>
        </tr>
        <tr>
          <td><input name="radiobutton" type="radio" value="radiobutton" onclick="document.getElementById('noid_type').value=5;"/></td>
          <td><span style="font-size:20px; color:#FF0000;">อื่นๆ โปรดระบุ</span></td>
        </tr>
        <tr>
          <td><input name="noid_type" type="hidden" id="noid_type" size="5" /></td>
          <td><input name="noid_remark" type="text" id="noid_remark" /></td>
        </tr>
      </table>
      </fieldset></span>	</td>
  </tr>
  <tr>
    <td><span class="show_line">&nbsp;</span></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
      <input type="button" name="Button" value="บันทึก" />
    </div></td>
    <td>&nbsp;</td>
  </tr>
  </tbody>
</table>
