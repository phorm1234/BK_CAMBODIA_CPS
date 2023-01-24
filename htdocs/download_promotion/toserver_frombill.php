<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">
<!--
.style1 {color: #0066CC; font-size:18px;}
.style2 {
	color: #0066CC;
	font-weight: bold;
	font-size: 18px;
}
-->
</style>
<script>
function sendbill(){
	if(document.getElementById("send_type").value=="B" && document.getElementById("member_no").value==''){
		alert("กรุณาระบุรหัสสมาชิกด้วยครับ");
		return false;
	}
	document.form1.submit();

	
}
function show_cr(){
	
	if(document.getElementById("send_type").value=="B"){
		document.getElementById('show_cr').style.display = 'block';
		document.getElementById("member_no").value="";
		document.getElementById('show_cr2').style.display = 'none';
	}else if(document.getElementById("send_type").value=="D"){
		document.getElementById('show_cr').style.display = 'block';
		document.getElementById("member_no").value="";
		document.getElementById('show_cr2').style.display = 'none';
	}else if(document.getElementById("send_type").value=="F"){
		document.getElementById('show_cr2').style.display = 'block';
		document.getElementById("doc_no").value="";
		document.getElementById('show_cr').style.display = 'none';
	}else{
		document.getElementById('show_cr').style.display = 'none';
		document.getElementById('show_cr2').style.display = 'none';
	}
}
</script>
<body onLoad="document.getElementById('show_cr').style.display = 'none';document.getElementById('show_cr2').style.display = 'none';">
<form id="form1" name="form1" method="post" action="toserver_frombill_send.php">
  <table width="533" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="3" bgcolor="#FF9900"><div align="center" class="style2">ส่งข้อมูล Online <br />
        <br />
      </div></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00"><div align="right"><span class="style2">รูปแบบการส่ง</span><span class="style1">&nbsp;</span></div></td>
      <td bgcolor="#FFCC00"><span class="style1">&nbsp;
        <select name="send_type" id="send_type" onChange="show_cr();" style="width:400px; font-size:20px; color:#FF3300;">
          <option value="A">ส่งบิลล่าสุด ที่เกิดขึ้นเมื่อสักครู่</option>
          <option value="F">ส่งบิล โดยการระบุเลขที่บิล</option>
          <option value="C">ส่งบิลทั้งหมดของวันนี้</option>
          <option value="B">ส่งบิล โดยระบุรหัสสมาชิก</option>
        </select>
      </span> </td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
    </tr>
    <tr>
      <td height="194" bgcolor="#FFCC00"><span class="style1"></span></td>
      <td valign="top" bgcolor="#FFCC00"><div class="style1" id='show_cr'>
          <table border="0">
            <tr>
              <td class="style1">ระบุรหัสสมาชิก</td>
              <td><input name="member_no" type="text" id="member_no"  style="width:200px; height:50px; font-size:20px; color:#FF0000;"/></td>
            </tr>
            <tr>
              <td class="style1">ระบุวันที่เปิดบิล</td>
              <td><select name="bill_d" id="bill_d">
                  <?php
		  	for($i=1; $i<=31; $i++){
				if($i==date("d")){
					echo " <option selected value='$i'>$i</option>";
				}else{
					echo " <option value='$i'>$i</option>";
				}
				
			}

		  ?>
                </select>
				
		<?php
			$month_now=date("m");
		?>		
                  <select name="bill_month" id="bill_month">
                    <option value="1">มกราคม</option>
                    <option value="2">กุมภาพันธ์</option>
                    <option value="3">มีนาคม</option>
                    <option value="4">เมษายน</option>
                    <option value="5">พฤษภาคม</option>
                    <option value="6">มิถุนายน</option>
                    <option value="7">กรกฎาคม</option>
                    <option value="8">สิงหาคม</option>
                    <option value="9">กันยายน</option>
                    <option value="10">ตุลาคม</option>
                    <option value="11">พฤศจิกายน</option>
                    <option value="12">ธันวาคม</option>
                  </select>
                  <script>document.getElementById("bill_month").value = <?php echo $month_now; ?>;</script>
                  <select name="bill_year" id="bill_year">
                    <?php
		  	for($y=date("Y")-5; $y<=date("Y"); $y++){
				if($y==date("Y")){
					echo " <option selected value='$y'>$y</option>";
				}else{
					echo " <option value='$y'>$y</option>";
				}
				
			}
		  ?>
                  </select>              </td>
            </tr>
          </table>
                         &nbsp;</div>
	  
	  <div id='show_cr2'>
			<table border="0">
			  <tr>
				<td>ระบุเลขที่บิล</td>
				<td><input name="doc_no" type="text" id="doc_no" /></td>
			  </tr>
			</table>
	  </div>
	  
	  
	  </td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
      <td bgcolor="#FFCC00"><a href="#" class="style2" onClick="sendbill();">ส่งข้อมูล</a></td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00">&nbsp;</td>
      <td bgcolor="#FFCC00">&nbsp;&nbsp;&nbsp;</td>
      <td bgcolor="#FFCC00">&nbsp;</td>
    </tr>
  </table>
  <br />
</form>

</body>
