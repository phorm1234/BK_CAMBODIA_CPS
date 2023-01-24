<?php
set_time_limit(0);
//include("/var/www/pos/htdocs/download_promotion/toserver_play_promotion.php");
//include("/var/www/pos/htdocs/download_promotion/toserver_bill_diary2.php");
//include("/var/www/pos/htdocs/download_promotion/toserver_mem_register.php");


if(date("H")==11){
	echo "Sendsumbill";
	$fp = @fopen("http://localhost/download_promotion/toserver_chk_bill_day.php", "r");
	$text=@fgetss($fp, 4096);
}


?>