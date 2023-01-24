<?php
	$host="localhost";
	$user="pos-ssup";
	$pwd ='P0z-$$up';
	$db="pos_ssup";	
	$local=mysql_connect($host,$user,$pwd) OR DIE(MYSQL_ERROR());
	if($local){
		mysql_select_db($db,$local) or DIE(MYSQL_ERROR());
		mysql_query("SET NAMES UTF8", $local);
		mysql_query("SET character_set_results=utf8", $local);
		mysql_query("SET character_set_client=utf8", $local);
		mysql_query("SET character_set_connection=utf8", $local);
	}
?>