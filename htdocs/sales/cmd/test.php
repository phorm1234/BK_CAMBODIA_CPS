<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_errors','On');
	/****************************************/
    //$hst_local="192.168.3.166";
    //$hst_local="172.70.101.14";
    $hst_local="192.168.3.247";
	$usr_local="support";
	$pwd_local='$upport0789'; 
	
	/*****************************************/
	$conn=mysql_connect($hst_local,$usr_local,$pwd_local) OR die(MYSQL_ERROR());
    $xxx=mysql_select_db("pos_ssup",$conn) OR die(MYSQL_ERROR);
    echo $xxx;
    /*
	$sql_br="SELECT trn_diary1_iq.branch_id AS branch_id_out
						FROM trn_diary1_iq
						LEFT JOIN com_customer_id ON trn_diary1_iq.branch_id = com_customer_id.branch_id
						WHERE com_customer_id.branch_id IS NULL LIMIT 0,1";
	$res_br=mysql_query($sql_br,$conn);
	$rec_br=mysql_fetch_assoc($res_br);
	$branch_id_out=$rec_br['branch_id_out'];
    echo $branch_id_out;
    */
	mysql_close($conn);
?>