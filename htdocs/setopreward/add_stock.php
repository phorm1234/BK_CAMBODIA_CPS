<?php 
	session_start(); 
	include("inc/mysql.ini.php"); 
	date_default_timezone_set('Asia/Bangkok');
	$date = date("Y-m-d");
	$time = date("H:i:s");
	$year = $_POST["year"];
	$month = $_POST["month"];
	$product_id = $_POST["product_id"];
	$product_id=trim($product_id);
	if($product_id!=''){
		$sql_del="DELETE FROM `pos_ssup`.`com_stock_master` WHERE `product_id`='$product_id' AND `month`='$month' AND `year`='$year'";
		mysql_query($sql_del , $local);
		$sql_add = "INSERT INTO `pos_ssup`.`com_stock_master` ( `corporation_id`, `company_id`, `branch_id`, `branch_no`, `product_id`, `month`, `year`, `product_status`, `begin`, `onhand`, `allocate`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) 
		VALUES ( 'SS', 'OP', '1100', '1100', '$product_id', '$month', '$year', 'N', '1000', '1000', '0.0000',CURDATE(),CURTIME(), '', '1900-01-01', '00:00:00', '')";
		//echo $sql_add;exit();
		$res_add = mysql_query($sql_add , $local);
		if($res_add){
			echo "Y";
		}else{
			echo "N";
		}
	}else{
		echo "N";
	}
?>