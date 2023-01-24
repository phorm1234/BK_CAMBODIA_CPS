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
	$sql_br="SELECT branch_id FROM com_branch_computer WHERE computer_no='1' LIMIT 0,1";
	$res_br=mysql_query($sql_br,$conn);
	$rec_br=mysql_fetch_assoc($res_br);
	$branch_id=$rec_br['branch_id'];
	mysql_close($conn);
	$conn2=mysql_connect($hst_server,$usr_server,$pwd_server) OR die(MYSQL_ERROR());
	mysql_select_db("pos_support",$conn2) OR die(MYSQL_ERROR);
	$sql_br2="SELECT status_send,normal_open_time,normal_close_time,special_open_time,special_close_time
							FROM  com_time_branch 
							WHERE branch_id='$branch_id'";
	$res_br2=mysql_query($sql_br2,$conn2);	
	$rec_br2=mysql_fetch_assoc($res_br2);
	$normal_open_time = $rec_br2['normal_open_time'];
	$normal_close_time = $rec_br2['normal_close_time'];
	$special_open_time = $rec_br2['special_open_time'];
	$special_close_time = $rec_br2['special_close_time'];
	mysql_close($conn2);
	$conn=mysql_connect($hst_local,$usr_local,$pwd_local) OR die(MYSQL_ERROR());
	mysql_select_db("pos_ssup",$conn) OR die(MYSQL_ERROR);
	$sql_upd="UPDATE 
						`pos_ssup`.`com_time_branch` 
					SET 
						`start_date` = '2014-06-20',
						`end_date` = '2100-01-31',
						`normal_open_time` = '$normal_open_time',
						`normal_close_time` = '$normal_close_time',
						`special_open_time` = '$special_open_time',
						`special_close_time` = '$special_close_time' 
					WHERE `com_time_branch`.`branch_id` ='$branch_id'";
	$res_upd=mysql_query($sql_upd,$conn);
	if($res_upd){
		$status_upd='Y';
	}
	mysql_close($conn);
	echo $status_upd;
?>