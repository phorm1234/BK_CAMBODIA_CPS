<?php 
    $db_host = "localhost";
	$db_username = "pos-ssup";
	//$db_pass = "";
	$db_pass = 'P0z-$$up';
	
	//$db_name1 = "manage";	
	//$db_name2 = "promotion";
	$db_name2 = "pos_ssup";

	//$condb_mng = mysql_connect("$db_host","$db_username","$db_pass") or die (mysql_error());
	$condb_pro = mysql_connect("$db_host","$db_username","$db_pass", true) or die (mysql_error());
	//mysql_select_db("$db_name1", $condb_mng) or die (mysql_error());
	mysql_select_db("$db_name2", $condb_pro) or die (mysql_error());
	
	/*mysql_query("SET NAMES UTF8", $condb_mng);
	mysql_query("SET character_set_results=utf8", $condb_mng);
	mysql_query("SET character_set_client=utf8", $condb_mng);
	mysql_query("SET character_set_connection=utf8", $condb_mng);*/
	
	mysql_query("SET NAMES UTF8", $condb_pro);
	mysql_query("SET character_set_results=utf8", $condb_pro);
	mysql_query("SET character_set_client=utf8", $condb_pro);
	mysql_query("SET character_set_connection=utf8", $condb_pro);
	
	//---------------------------------------------------------------------------------------------- bkoff
	
	$db_host_bkoff = "192.168.0.20";
	$db_username_bkoff = "sqlbkoff";
	$db_pass_bkoff = "bkoffpwd";
	
	$db_name3 = "dbcenter";	

	$condb_bk = mysql_connect("$db_host_bkoff","$db_username_bkoff","$db_pass_bkoff", true) or die (mysql_error());
	mysql_select_db("$db_name3", $condb_bk) or die (mysql_error());
	
	mysql_query("SET NAMES UTF8", $condb_bk);
	mysql_query("SET character_set_results=utf8", $condb_bk);
	mysql_query("SET character_set_client=utf8", $condb_bk);
	mysql_query("SET character_set_connection=utf8", $condb_bk);
	
?>