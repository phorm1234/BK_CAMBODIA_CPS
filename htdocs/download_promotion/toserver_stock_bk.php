<?php
set_time_limit(0);
$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";


$h=date("H");
if($h>=10 && $h<=11){
		//shop
		$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		
		$add="
		insert into com_stock_master_history(`date_bk`, `time_bk`, `corporation_id`, `company_id`, `branch_id`, `branch_no`, `product_id`, `month`, `year`, `product_status`, `begin`, `onhand`, `allocate`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`)
		select 
		date(now()),time(now()), `corporation_id`, `company_id`, `branch_id`, `branch_no`, `product_id`, `month`, `year`, `product_status`, `begin`, `onhand`, `allocate`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
		from 
		com_stock_master
		where 
		year=year(now()) and month=month(now())
		";
		mysql_select_db($dbname);
		mysql_query($add,$conn_shop);

}
?>