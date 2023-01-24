<?php
set_time_limit(0);

$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";


$ji_ip="10.100.53.2";
$ji_user="master";
$ji_pass="master";
$ji_db="pos_ssup";


mysql_close($conn_shop);
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];




$search_cr="OP" . $shop;
mysql_db_query($dbname,"delete from `com_ecoupon` where op = '$search_cr' ",$conn_shop);

mysql_close($c53_2);
$c53_2=mysql_connect($ji_ip,$ji_user,$ji_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$sql_ecoupon="select * from com_ecoupon where op = '$search_cr' ";

$r_ecoupon=mysql_db_query($ji_db,$sql_ecoupon,$c53_2);
$rows_ecoupon=mysql_num_rows($r_ecoupon);
echo "rows=$rows_ecoupon";



for($iecoupon=1; $iecoupon<=$rows_ecoupon; $iecoupon++){
	$data_ecoupon=mysql_fetch_array($r_ecoupon);
	$up_ecoupon="
		insert into
		 com_ecoupon
		set
		corporation_id='$data_ecoupon[corporation_id]',
		company_id='$data_ecoupon[company_id]',
		dept_id='$data_ecoupon[dept_id]',
		employee_id='$data_ecoupon[employee_id]',
		name='$data_ecoupon[name]',
		surname='$data_ecoupon[surname]',
		start_date='$data_ecoupon[start_date]',
		end_date='$data_ecoupon[end_date]',
		op='$data_ecoupon[op]',
		cps='$data_ecoupon[cps]',
		gnc='$data_ecoupon[gnc]',
		amount_op='$data_ecoupon[amount_op]',
		amount_cps='$data_ecoupon[amount_cps]',
		amount_gnc='$data_ecoupon[amount_gnc]',
		percent_discount='$data_ecoupon[percent_discount]',
		level='$data_ecoupon[level]',
		cancel='$data_ecoupon[cancel]',
		edit_date='$data_ecoupon[edit_date]',
		reg_date=date(now()),
		reg_time=time(now()),
		reg_user='IT',
		upd_date=date(now()),
		upd_time=time(now()),
		upd_user='IT'
	";
	//echo $up_ecoupon;
	mysql_close($conn_shop);
	$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
	mysql_query("SET character_set_results=tis620");
	mysql_query("SET character_set_client=tis620");
	mysql_query("SET character_set_connection=tis620");
	$rup_ecoupon=mysql_db_query($dbname,$up_ecoupon,$conn_shop);
}//loop
?>
<script>window.location='report_coupon.php'</script>