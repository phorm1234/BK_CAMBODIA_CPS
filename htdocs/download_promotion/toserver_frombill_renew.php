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
	if(document.getElementById("doc_no").value==''){
		alert("กรุณากรอกเลขที่บิล");
		return false;
	}
	document.form1.submit();

	
}
</script>
<body>
<form id="form1" name="form1" method="post" action="toserver_frombill_send_renew.php">
  <table width="533" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="3" bgcolor="#FF9900"><div align="center" class="style2">โอนยอดซื้อสะสมและคะแนน สำหรับสมาชิกบัตรหายหรือต่ออายุ <br />
        <br />
      </div></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00"><div align="right"><span class="style2">เลขที่บิล</span><span class="style1">&nbsp;</span></div></td>
	  <td bgcolor="#FFCC00"><input name="doc_no" type="text" id="doc_no"  style="width:300px; height:50px; font-size:20px; color:#FF0000;"/></td>     
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
    </tr>
    <tr>
      <td bgcolor="#FFCC00"><span class="style1"></span></td>
      <td bgcolor="#FFCC00"><a href="#" class="style2" onclick="sendbill();">ส่งข้อมูล</a></td>
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
