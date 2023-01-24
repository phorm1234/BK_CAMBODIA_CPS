<?php
set_time_limit(0);
$inc_connect="../inc/connect.php";
if (file_exists($inc_connect)) {  //ถ้าเป็นแบบ manual
	echo "case=Manual<br>";
	include($inc_connect);	
}else{
	echo "case=AUTO<br>";
	include("/var/www/pos/htdocs/download_promotion/inc/connect.php");
}
$num_day=5;


$doc_date=$_GET['doc_date'];
$branch_id=$_GET['branch_id'];
if($doc_date==""){
	echo "I want doc_date value.";
	return false;
}


//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];



$conf="
select 
*
from trn_diary2
where
doc_date='$doc_date' and branch_id='$branch_id'
order by id desc
";
echo $conf;

$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);
echo "Rows=$rows<br>";




for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($result);

$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
	$insert="
insert into trn_diary2(`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`, `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`, `coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`, `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`, `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`, `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`,`time_up`) values('$data[corporation_id]','$data[company_id]','$data[branch_id]','$data[doc_date]','$data[doc_time]','$data[doc_no]','$data[doc_tp]','$data[status_no]','$data[flag]','$data[seq]','$data[seq_set]','$data[promo_code]','$data[promo_seq]','$data[promo_pos]','$data[promo_st]','$data[promo_tp]','$data[product_id]','$data[price]','$data[quantity]','$data[stock_st]','$data[amount]','$data[discount]','$data[member_discount1]','$data[member_discount2]','$data[co_promo_discount]','$data[coupon_discount]','$data[special_discount]','$data[other_discount]','$data[net_amt]','$data[cost]','$data[cost_amt]','$data[promo_qty]','$data[weight]','$data[exclude_promo]','$data[location_id]','$data[product_status]','$data[get_point]','$data[discount_member]','$data[cal_amt]','$data[tax_type]','$data[gp]','$data[point1]','$data[point2]','$data[discount_percent]','$data[member_percent1]','$data[member_percent2]','$data[co_promo_percent]','$data[co_promo_code]','$data[coupon_code]','$data[coupon_percent]','$data[not_return]','$data[lot_no]','$data[lot_expire]','$data[lot_date]','$data[short_qty]','$data[short_amt]','$data[ret_short_qty]','$data[ret_short_amt]','$data[cn_qty]','$data[cn_amt]','$data[cn_tp]','$data[cn_remark]','$data[deleted]','$data[reg_date]','$data[reg_time]','$data[reg_user]','$data[upd_date]','$data[upd_time]','$data[upd_user]',now())
	";
	echo "$insert<br>";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);
	if($result_insert){
		//echo "$i $doc_date Insert Yes Complete<br>";
	}else{
		echo "$i $doc_date $insert <br>None Complete<br>";
	}

mysql_close($conn_ji);


}




?>