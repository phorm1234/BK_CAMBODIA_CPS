<?php
set_time_limit(0);
header("Content-type:text/html; charset=tis-620"); 
include("connect.php");

$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];

if($id_card==""){
	echo "STOP###��س��͡�ҡ�к����Ǵ��Թ���������";
	return false;
}


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

$find_doc_date="select *  from com_doc_date";
$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
$data_doc_date=mysql_fetch_array($run_find_doc_date);
$doc_date=$data_doc_date['doc_date'];


$conn_online=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);

/*$chkopid="select * from member_register  where id_card='$id_card' ";
$run_chkopid=mysql_query($chkopid,$conn_online);
$rowchkopid=mysql_num_rows($run_chkopid);
if($rowchkopid){
	echo "STOP###�����źѵ� ���. ��� �������Ѥ�Ẻ�� ���� ���. ᷹�ѵ���Ҫԡ���";
	return false;
}
if($rowchkopid>1){
	echo "STOP###�����źѵ� ���. ������ҡ���� 1 �� ��к� ��س����Ѿവ��������ǹ��ǡѺ beauty line ��͹���";
	return false;
}
*/

$findprofile="select * from member_register as a inner join member_history as b
on a.customer_id=b.customer_id
where
a.id_card='$id_card' and b.expire_date>='$doc_date' and b.status_active='Y' and  b.member_no like 'ID%'
";
//echo $findprofile;
$run_findprofile=mysql_query($findprofile,$conn_online);
$rowsprofile=mysql_num_rows($run_findprofile);
if($rowsprofile==0){
	echo "STOP###��辺�����źѵ� ���. �����к�";
	return false;
}


$profile=mysql_fetch_array($run_findprofile);
$mobile_no=$profile['mobile_no'];

$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&promortion=OPID300";	
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	
echo "OK###OTP CODE ���ѧ��价������ $mobile_no";


mysql_close($conn_local);
?>