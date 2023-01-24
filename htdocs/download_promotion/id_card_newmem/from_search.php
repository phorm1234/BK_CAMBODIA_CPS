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

<input name="ip_this" type="hidden" id="ip_this" value="<?php echo $com_ip; ?>"/>
<script type="text/javascript">

function from_snap(){
			var num_snap=document.getElementById('num_snap').value;
			num_snap=parseInt(num_snap)+1;
			document.getElementById('num_snap').value;
	var url="../../../download_promotion/id_card/from_snap.php?id_card="+document.getElementById('id_card').value+"&num_snap="+num_snap;
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
	
	
		
       TINY.box.show({url:'../../../download_promotion/id_card/from_snap.php',post:'id=16',width:500,height:400,opacity:20,topsplit:3})
}
</script>

<style type="text/css">
<!--

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
</style>
<input type='hidden' id='num_snap' size='3' value='0'>
<table border="0">

    <td valign="top"><div align="center"><span class="style3">
      เสียบบัตร ปชช. ลูกค้าที่เครื่องอ่านบัตร ปชช. แล้วกดปุ่มนี้ </span></div></td>
    <td valign="top"><div align="center" id='show_pic'  ></div></td>
    </tr>
  <tr>
    <td valign="top"><table border="0" align="center" cellpadding="3" cellspacing="3">

      <tr>
        <td><div align="center"><img src="../../download_promotion/id_card_newmem/pointer_down.jpeg" width="50px" height="50px" /></div></td>
        <td><div align="center">
		<input name="id_card" type="hidden" id="id_card" />
		<input name="fname" type="hidden" id="fname" />
		<input name="lname" type="hidden" id="lname" />
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
			<input name="hbd_day" type="hidden" id="hbd_day" />
			<input name="hbd_month" type="hidden" id="hbd_month" />
			<input name="hbd_year" type="hidden" id="hbd_year" />
			<input name="hbd" type="hidden" id="hbd" />
			
			
        </div></td>
      </tr>
      <tr>
        <td><div onclick="onreadidcheck();" class="round-button-circle" align="center" ><a  class="round-button"  style="color:#FFFFFF; size:20px;"><br />
          อ่านบัตร<br />ปชช.</a></div></td>
        <td><div align="center" id='show_photo'></div></td>
      </tr>
    </table></td>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
