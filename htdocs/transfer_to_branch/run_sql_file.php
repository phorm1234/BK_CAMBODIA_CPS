<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_errors','On');
$file_data=urldecode($_REQUEST['file_data']);
if($file_data!=""){
    system("rm -rf /var/www/pos/htdocs/transfer_to_branch/$file_data");
	system("wget http://10.100.53.2/ims/transfer_to_branch/sql/$file_data");
	system("chmod 777 -R  $file_data");
	
	$pass='P0z-$$up';
	$user='pos-ssup';
	$comman="mysql -u ".$user." -p'".$pass."' pos_ssup <  /var/www/pos/htdocs/transfer_to_branch/$file_data";
	system($comman);
	echo"OK";
}


?>
