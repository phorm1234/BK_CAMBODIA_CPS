<?php
	set_time_limit(0);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_errors','On');	
	$hst="localhost";
	$usr="pos-ssup";
	$pwd='P0z-$$up';
	$db="pos_ssup";
	$local = mysql_connect($hst,$usr,$pwd) OR DIE(MYSQL_ERROR());
	mysql_select_db($db,$local) OR DIE(MYSQL_ERROR());
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	$sql_local="SELECT * FROM `com_branch_computer` 
						WHERE computer_no = '1' AND com_ip <> '127.0.0.1' ORDER BY id ASC ";
	$res_local=mysql_query($sql_local);
	$rec_local=mysql_fetch_assoc($res_local);
	$brand=$rec_local['company_id'];
	$branch_id=$rec_local['branch_id'];
	$ip=$rec_local['com_ip'];
	$fp = @fopen("http://10.100.53.2/wservice/possupport/up2shop/op/data/gen_zip_pro.php?brand=$brand&branch_id=$branch_id&ip=$ip", "r");
	if($fp){
		$text=@fgetss($fp, 4096);
		echo $text;
	}
	echo "STEP3\n";
?>
