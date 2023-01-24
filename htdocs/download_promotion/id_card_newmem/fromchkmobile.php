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
$xdata=$_GET['xdata'];	
//echo $xdata;
$arr=explode("###",$xdata);	
$rows=$arr[1];
$mobile_no=$arr[6];
//echo "<center><span class='style1' >Mobile : " . $mobile_no . "</span></center>";
function formatdate($dt){
	$arr=explode('-',$dt);
	return "$arr[2]/$arr[1]/$arr[0]";
}
?>
<style type="text/css">
<!--
.style0 {
	font-size: 36px;
	color: #FF0000;
}
.style1 {
	font-size: 20px;
	color: #376092;
}
.style2 {font-size: 24}
.style3 {
	font-size: 26px;
	color: #9bbb59;
}
.styleX {
	font-size: 30px;
	color: #9bbb59;
}
.headx {
	font-size: 36px;
	color: #376092;
}
.text2{
font-size:24px; color:#9bbb59; width:200px; border-bottom-color:#9bbb59; border-top-color:#9bbb59; border-left-color:#9bbb59; border-right-color:#9bbb59;
}
.text3{
font-size:24px; color:#426898; width:200px; border-bottom-color:#4f81bd; border-top-color:#4f81bd; border-left-color:#4f81bd; border-right-color:#4f81bd;
}
.textotp{
font-size:24px; color:#9bbb59; width:200px; border-bottom-color:#4f81bd; border-top-color:#4f81bd; border-left-color:#4f81bd; border-right-color:#4f81bd;
}

-->
</style>

<table border="0" align="center">
  <tr>
    <td><div align="center">
      <p class="style3">
	  <?php
	  	if($rows>0){
			echo "<span style='color:#FF0000'>เบอร์มือถือนี้ มีสมาชิกท่านอื่นใช้แล้วคือ คุณ$arr[2] $arr[3] <br>รหัสบัตรประชาชนเลขที่ : $arr[4] วันเกิด : " . formatdate($arr[5]) . "</span>";
			echo "<input type='hidden' id='status_mobile_dup' value='dup'>";
		}else{
			echo "<input type='hidden' id='status_mobile_dup' value=''>";
		}
	  ?>
	  </p>
      <p class="style1">ให้กดปุ่มนี้
          <input type="button" name="Button" value="ส่ง OTP CODE"  onclick="if(document.getElementById('status_mobile_dup').value=='dup'){var c=confirm('คุณแน่ใจนะว่าลูกค้ายืนยันจะใช้เบอร์มือถือนี้จริง');if(!c){return false;}}chkmobilesendotp('<?php echo $mobile_no; ?>','');"/>
ระบบจะส่ง OTP CODE เข้าโทรศัพท์มือถือของลูกค้า     </p>
      <p class="style1">ให้นำ OTP CODE จากโทรศัพท์มือถือของลูกค้ามาใส่ที่ช่องนี้ เพื่อยืนยันว่าเบอร์นี้เป็นของลูกค้าจริง เพื่อไม่ให้ลูกค้าเสียสิทธิ์ค่ะ</p>
    </div></td>
  </tr>
  <tr>
    <td><div align="center">
      <input name="otp_code_input" type="text" id="otp_code_input" />
    </div></td>
  </tr>
</table>
