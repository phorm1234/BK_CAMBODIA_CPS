<?php 
	$db_host = "localhost";
	$db_username = "pos-ssup";
	$db_pass = "P0z-\$\$up";
	
	/*$db_host = "localhost";
	$db_username = "root";
	$db_pass = "";*/
	
	$db_name1 = "ssup";	
	$db_name2 = "pos_ssup";
	$db_name3 = "transfer_to_office";

	$condb1 = mysql_connect("$db_host","$db_username","$db_pass") or die (mysql_error());
	$condb2 = mysql_connect("$db_host","$db_username","$db_pass", true) or die (mysql_error());
	$condb3 = mysql_connect("$db_host","$db_username","$db_pass", true) or die (mysql_error());
	mysql_select_db("$db_name1", $condb1) or die (mysql_error());
	mysql_select_db("$db_name2", $condb2) or die (mysql_error());
	mysql_select_db("$db_name3", $condb3) or die (mysql_error());
	
	mysql_query("SET NAMES UTF8", $condb1);
	mysql_query("SET character_set_results=utf8", $condb1);
	mysql_query("SET character_set_client=utf8", $condb1);
	mysql_query("SET character_set_connection=utf8", $condb1);
	
	mysql_query("SET NAMES UTF8", $condb2);
	mysql_query("SET character_set_results=utf8", $condb2);
	mysql_query("SET character_set_client=utf8", $condb2);
	mysql_query("SET character_set_connection=utf8", $condb2);	
	
	mysql_query("SET NAMES UTF8", $condb3);
	mysql_query("SET character_set_results=utf8", $condb3);
	mysql_query("SET character_set_client=utf8", $condb3);
	mysql_query("SET character_set_connection=utf8", $condb3);	
?>