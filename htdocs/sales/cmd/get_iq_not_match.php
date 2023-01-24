<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_errors','On');
	/****************************************/
	$hst_local="localhost";
	$usr_local="pos-ssup";
	$pwd_local='P0z-$$up'; 
	$hst_server="10.100.53.2";
	$usr_server="master";
	$pwd_server="master"; 
	$status_upd='N';
	/*****************************************/
	$conn=mysql_connect($hst_local,$usr_local,$pwd_local) OR die(MYSQL_ERROR());
	mysql_select_db("pos_ssup",$conn) OR die(MYSQL_ERROR);
	$sql_br="SELECT trn_diary1_iq.branch_id AS branch_id_out
						FROM trn_diary1_iq
						LEFT JOIN com_customer_id ON trn_diary1_iq.branch_id = com_customer_id.branch_id
						WHERE com_customer_id.branch_id IS NULL LIMIT 0,1";
	$res_br=mysql_query($sql_br,$conn);
	$rec_br=mysql_fetch_assoc($res_br);
	$branch_id_out=$rec_br['branch_id_out'];
	echo $branch_id_out;
	mysql_close($conn);
?>