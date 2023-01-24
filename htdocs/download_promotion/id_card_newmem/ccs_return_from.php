<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
<meta charset="utf-8">
	
<style type="text/css">
<!--
.style2 {color: #067db7; font-size:20px;}
.style3 {color: #e46c0a; font-size:24px;}
.textmanual {color: #376092; font-size:24px;}
.textidcard {font-size:28px; color:#99CCFF;}

-->
</style>
<style>
.round-button {
	width:50px;
}
.round-button-circle {
	width: 90px;
	height:0;
	padding-bottom: 90px;
    border-radius: 50%;
	border:5px solid #cfdcec;
    overflow:hidden;
    
    background: #4679BD; 
    box-shadow: 0 0 3px gray;
	cursor:pointer;
}
.round-button-circle:hover {
	background:#30588e;
}
.round-button a {
    display:block;
	float:left;
	width:100%;
	padding-top:50%;
    padding-bottom:50%;
	line-height:1em;
	margin-top:-0.5em;
    
	text-align:center;
	color:#e2eaf3;
    font-family:Verdana;
    font-size:1.2em;
    font-weight:bold;
    text-decoration:none;
}
.round-button-circle1 {	width: 90px;
	height:0;
	padding-bottom: 90px;
    border-radius: 50%;
	border:5px solid #cfdcec;
    overflow:hidden;
    
    background: #4679BD; 
    box-shadow: 0 0 3px gray;
	cursor:pointer;
}
</style>
<APPLET CODE = 'idcard.RdNationalCardID' archive='../../download_promotion/id_card/lib/RdNIDApplet090DL.jar' name='NIDApplet' WIDTH = "0" HEIGHT = "0">
</APPLET>  





<script>
function readIDCARD(){

		if(!document.NIDApplet.isCardInsertRD()){
			alert("คุณยังไม่ได้ Scan บัตรประชาชน");
			return false;
		}

		var id_card = document.NIDApplet.getNIDNumberRD();

		document.getElementById('id_card').value=id_card;
		chkok();


}
function chkok(){
	var xfunction_next=$("#function_next").val()+"()";
	if($("#status_readcard").val()=="AUTO"){
		if($("#id_card").val()!=$("#id_card_log").val()){
			alert("รหัสบัตร ปชช. ที่เสียบอยู่ ไม่ใช่ของสมาชิกท่านนี้ รหัสบัตร ปชช. ของสมาชิกที่เปิดบิลนี้คือ : "+$("#id_card_log").val());
			return false;
		}
		
		$("#dialog-lastpro_play" ).dialog('close'); 
		eval(xfunction_next);
	}else{
		$("#dialog-lastpro_play" ).dialog('close'); 
		eval(xfunction_next);
	}

	//checkSaleMan();
}
</script>



<?php
include("connect.php");
$com_ip=$_SERVER['REMOTE_ADDR'];
$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$find_timebill="select * from com_doc_date";
$run_find_timebill=mysql_query($find_timebill,$conn_local);
$datatimebill=mysql_fetch_array($run_find_timebill);
$doc_date=$datatimebill['doc_date'];

$log="select *  from memccs_log  where ip='$com_ip' and reg_date='$doc_date'  order by time_up desc limit 0,1";
//echo $log;
$run_log=mysql_query($log,$conn_local);
$data_log=mysql_fetch_array($run_log);
$ip=$data_log['ip'];
$id_card=$data_log['id_card'];
$member_no=$data_log['member_no'];
$status_readcard=$data_log['status_readcard'];
//echo "ss=$status_readcard";
$otpcode=$data_log['otpcode'];
mysql_close($conn_local);


		
?>
<style type="text/css">
<!--
.style0 {
	font-size: 36px;
	color: #FF0000;
}
.style1 {
	font-size: 30px;
	color: #376092;
}
.style2 {font-size: 24}
.style3 {
	font-size: 36px;
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

    <input name="ip" type="hidden" id="ip" size='5' value='<?php echo $ip; ?>'/>
	<input name="id_card_log" type="hidden" id="id_card_log" size='5' value='<?php echo $id_card; ?>'/>
	<input name="member_no" type="hidden" id="member_no" size='5' value='<?php echo $member_no; ?>'/>
	<input name="otpcode" type="hidden" id="otpcode" size='5' value='<?php echo $otpcode; ?>'/>
<input name="function_next" type="hidden" id="function_next" value='<?php echo $_GET['function_next']; ?>'/>

<?php if($status_readcard=="AUTO"){ ?>
<table border="0" align="center">

    <tr>
      <td valign="top"><div align="center">
        <input name="status_readcard" type="text" id="status_readcard"  value='<?php echo $status_readcard; ?>' style="font-size:10px; color:#99CC00;"/>
      </div></td>
    </tr>
    <td valign="top"><div align="center"><span class="style3">
      เสียบบัตร ปชช. ลูกค้าที่เครื่องอ่านบัตร ปชช. แล้วกดปุ่มนี้ </span></div></td>
    </tr>
  <tr>
    <td valign="top"><table border="0" align="center" cellpadding="3" cellspacing="3">

      <tr>
        <td><div align="center"><img src="../../download_promotion/id_card_newmem/pointer_down.jpeg" width="50px" height="50px" /></div></td>
        <td><div align="center">
		<input name="id_card" type="hidden" id="id_card" />
        </div></td>
      </tr>
      <tr>
        <td><div onclick="readIDCARD();" class="round-button-circle" align="center" ><a  class="round-button"  style="color:#FFFFFF; size:20px;"><br />
          อ่านบัตร<br />ปชช.</a></div></td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>

<?php }else{ ?>
<table border="0" align="center">
  <tr>
    <td class="style3">นำบัตรประชาชนคืนลูกค้าแล้วกดปุ่มนี้</td>
  </tr>
  <tr>
    <td><table border="0" align="center" cellpadding="3" cellspacing="3">
      <tr>
        <td><div align="center"><img src="../../download_promotion/id_card_newmem/pointer_down.jpeg" width="50px" height="50px" /></div></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div onclick="chkok();" class="round-button-circle1" align="center" ><a  class="round-button"  style="color:#FFFFFF; size:20px;"><br>ยืนยัน</a></div></td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>

<?php } ?>