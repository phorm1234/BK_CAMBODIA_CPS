<?php
mysql_db_query($dbname,"delete from promo_check where  promo_code='$promo_code'  ",$conn_shop);


$sql_promochk="select * from promo_check  where  promo_code='$promo_code' ";
$r_promochk=mysql_db_query($db_jinet_center,$sql_promochk,$c53_2);
$rows_promochk=mysql_num_rows($r_promochk);
//echo "rows Office : $rows<br>";
for($x=1; $x<=$rows_promochk; $x++){
	$data_promochk=mysql_fetch_array($r_promochk);

	$up_promochk="
		insert into
		 promo_check
		set
		corporation_id='$data_promochk[corporation_id]',
		company_id='$data_promochk[company_id]',
		promo_code='$data_promochk[promo_code]',
		seq_pro='$data_promochk[seq_pro]',
		product_id='$data_promochk[product_id]',
		start_date='$data_promochk[start_date]',
		end_date='$data_promochk[end_date]',
		pro_price='$data_promochk[pro_price]',
		unit='$data_promochk[unit]',
		unit_tp='$data_promochk[unit_tp]',
		weight='$data_promochk[weight]',
		promo_group='$data_promochk[promo_group]',
		promo_play='$data_promochk[promo_play]',
		promo_st='$data_promochk[promo_st]',
		condition_tp='$data_promochk[condition_tp]',
		product_tp='$data_promochk[product_tp]',
		reg_date=date(now()),
		reg_time=time(now()),
		reg_user='IT',
		upd_date=date(now()),
		upd_time=time(now()),
		upd_user='IT'
	";

	$rup_promochk=mysql_db_query($dbname,$up_promochk,$conn_shop);


}




?>