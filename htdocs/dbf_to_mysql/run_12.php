<?php
ini_set('display_errors', '1');
set_time_limit(0);
header("Content-type:text/html; charset=tis-620");  

$db_uname = 'pos-ssup';
$db_passwd = 'P0z-$$up';
$db = 'transfer_to_office';
$reg_user="005949";

//--------------------------------------------------------------------------
$arr_config=array();
$PATH_FILE_INI="/var/www/pos/htdocs/.setuppos.wr";
if(!file_exists($PATH_FILE_INI)){
	echo "FILE NOT FOUND.";
	exit();
}
$file = fopen($PATH_FILE_INI, "r");
$i = 0;
while (!feof($file)) {
	$arr_config[] = fgets($file);
}
fclose($file);
$brand=trim($arr_config[0]);
$brand=strtoupper($brand);
$corporation_id=$brand;
$company_id=$brand;
$branch_id=trim($arr_config[1]);
$branch_no=trim($arr_config[1]);
//--------------------------------------------------------------------------
$conn = mysql_connect('localhost',$db_uname,$db_passwd);
mysql_query("SET NAMES UTF8");  
mysql_select_db($db);
//--------------------------------------------------------------------------
$sql_del="TRUNCATE TABLE com_branch_os";
mysql_query($sql_del,$conn);
//--------------------------------------------------------------------------
$ip=$_SERVER['HTTP_HOST'];

$sql_uu="
INSERT INTO `com_branch_os` (`id`, `corporation_id`, `company_id`, `branch_id`, `computer_ip`, `computer_no`, `active_status`, `os`, `UNIQUE_ID`, `HTTP_HOST`, `HTTP_USER_AGENT`, `HTTP_ACCEPT`, `HTTP_ACCEPT_LANGUAGE`, `HTTP_ACCEPT_ENCODING`, `HTTP_CONNECTION`, `HTTP_COOKIE`, `HTTP_X_INSIGHT`, `HTTP_CACHE_CONTROL`, `PATH`, `SERVER_SIGNATURE`, `SERVER_SOFTWARE`, `SERVER_NAME`, `SERVER_ADDR`, `SERVER_PORT`, `REMOTE_ADDR`, `DOCUMENT_ROOT`, `SERVER_ADMIN`, `SCRIPT_FILENAME`, `REMOTE_PORT`, `GATEWAY_INTERFACE`, `SERVER_PROTOCOL`, `REQUEST_METHOD`, `QUERY_STRING`, `REQUEST_URI`, `SCRIPT_NAME`, `PATH_INFO`, `PATH_TRANSLATED`, `PHP_SELF`, `REQUEST_TIME`, `start_send_day`, `start_send_time`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES
(4, '$corporation_id', '$company_id', '$branch_id', '$ip', '1', 'N', 'Linux', '', '$ip', 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1 FirePHP/0.7.1', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'en-us,en;q=0.5', 'gzip, deflate', 'keep-alive', 'PHPSESSID=i6q912082d6aadn0ijj6gpb4l3', 'activate', '', '/usr/local/bin:/usr/bin:/bin', '<address>Apache/2.2.20 (Ubuntu) Server at $ip Port 80</address>\n', 'Apache/2.2.20 (Ubuntu)', '$ip', '$ip', '80', '192.168.252.241', '/var/www/pos/htdocs', 'webmaster@localhost', '/var/www/pos/htdocs/pos/index.php', '3904', 'CGI/1.1', 'HTTP/1.1', 'GET', '', '/pos/index/test', '/pos/index.php', '/test', '/var/www/pos/htdocs/test.php', '/pos/index.php/test', '1345173600', 'Allday', '00:00:00', '1900-01-01', '00:00:00', '', '1900-01-01', '00:00:00', '') ";

mysql_query($sql_uu,$conn);
system("touch /home/shopsetup/dbf_to_mysql/pook12.txt");
?>























