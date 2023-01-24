<?php
	set_time_limit(0);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_errors','On');
	$localhost="localhost";
	$password='P0z-$$up';
	$user_name="pos-ssup";
	$command = "mysqldump -d -u$user_name -p'$password'  pos_ssup > /var/www/pos_ssup.sql";
	system($command);
?>