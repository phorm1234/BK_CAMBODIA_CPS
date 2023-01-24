<?php
ini_set('display_errors', '1');	
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

//--------------------------------------------------------------------------------------------------
$db_uname = 'pos-ssup';
$db_passwd = 'P0z-$$up';
$db = 'pos_ssup';
$reg_user="005949";

$conn = mysql_connect('localhost',$db_uname, $db_passwd);
mysql_query("SET NAMES UTF8");  
mysql_select_db($db);
//--------------------------------------------------------------------------------------------------
$sql_clear="TRUNCATE TABLE `com_pos_config` ";
$res_clear=mysql_query($sql_clear);

$sql_3_2="INSERT INTO `com_pos_config` (`id`, `id_config`, `corporation_id`, `company_id`, `branch_id`, `branch_no`, `code_type`, `value_type`, `default_status`, `condition_status`, `default_day`, `condition_day`, `start_date`, `end_date`, `start_time`, `end_time`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES
(1, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'NO_KEYIN_TI', 'C', 'Y', 'N', '0', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(2, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'NO_KEYIN_MEMBER', 'C', 'Y', 'N', '0', '0',CURDATE(),CURDATE(), '08:00:00', '22:00:00', CURDATE(), CURTIME(), '', CURDATE(), '00:00:00', ''),
(3, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'NO_KEYIN_TO', 'C', 'Y', '', '0', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(4, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'CHECK_DOC_DATE', 'C', 'Y', 'N', '0', '0', '2011-12-01', '2012-09-02', '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(5, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'TI_DAY', 'N', 'Y', '', '14', '30', '2012-03-22', '2012-03-28', '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(6, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'CN_DAY', 'N', 'Y', '', '14', '100', '2011-10-14', '2011-10-14', '14:00:00', '16:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(7, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'RQ_DAY', 'N', 'Y', '', '3', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(8, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'THERMAL_PRINTER', 'C', 'Y', '', '0', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(9, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'LAN', 'C', 'N', '', '0', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(10, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'NO_KEYIN_BARCODE', 'C', 'Y', '', '0', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', ''),
(11, 1, '$corporation_id', '$company_id', '$branch_id', '$branch_id', 'NO_KEYIN_COUPON', 'C', 'Y', '', '0', '0', CURDATE(), CURDATE(), '00:00:00', '00:00:00', CURDATE(), '00:00:00', '', CURDATE(), '00:00:00', '');";
mysql_query($sql_3_2);

$sql_clear="TRUNCATE TABLE `com_doc_date` ";
$res_clear=mysql_query($sql_clear);
$sql_5_2="INSERT INTO `com_doc_date` (`id`, `corporation_id`, `company_id`, `doc_date`, `remark`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES
(NULL, '$corporation_id', '$company_id', CURDATE() , 'วันที่เอกสาร ', CURDATE(), CURTIME(), '', CURDATE(), CURTIME(), '$upd_user');";
mysql_query($sql_5_2);

$sql_5_4="INSERT INTO ssup.`check_in_out` (`check_id`, `cid`, `time_id`, `shop_ip`, `shop_id`, `check_date`, `check_in`, `check_in_img_path`, `check_in_seq`, `check_in_sent`, `check_in_reason`) VALUES (NULL, 't01', 5, '127.0.0.1', $branch_id, CURDATE(), CURTIME(), '', 2, 1, 1);";
mysql_query($sql_5_4);

print(" Successfully \n");
system("touch /home/shopsetup/dbf_to_mysql/pook10.txt");
?>
