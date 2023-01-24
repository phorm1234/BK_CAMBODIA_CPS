<?php
	$user_id=$_REQUEST['user_id'];
	$connect = mysql_connect("localhost","pos-ssup",'P0z-$$up');
	mysql_select_db("transfer_to_branch",$connect);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	$sql_1="INSERT INTO  `resive_file` SET `user_sent` = '$user_id' ";
	$run_1=mysql_query($sql_1,$connect);
	system("chmod 777  -R /var/www/pos");
	system("rm -rf /var/www/pos/htdocs/transfer_to_branch/pos.zip");
	system("wget http://10.100.53.2/wservice/possupport/up2shop/cps-kh/pos.zip && echo 'Y' || echo 'N' ");

	system("rm -rf /var/www/pos.zip");
	system("unzip -o pos.zip -d /var/www/");
	system("chmod 777  -R /var/www/pos");
?>
