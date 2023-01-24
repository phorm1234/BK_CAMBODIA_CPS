<?php
mysql_db_query($dbname,"delete from promo_check where  promo_code='$promo_code'  ",$conn_shop);


$sql_promochk="select * from promo_check_tmp  where  promo_code='$promo_code' ";
$r_promochk=mysql_db_query($dbname,$sql_promochk,$conn_shop);
$rows_promochk=mysql_num_rows($r_promochk);

$txt_detail="";
$txt_start="INSERT INTO promo_check(`corporation_id`, `company_id`, `promo_code`, `seq_pro`, `product_id`, `start_date`, `end_date`, `pro_price`, `unit`, `unit_tp`, `weight`, `promo_group`, `promo_play`, `promo_st`, `condition_tp`, `product_tp`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES";

$text_limit=300;
$loop_run=$text_limit;
$loop_gen=ceil($rows_promochk/$text_limit);

for($x=1; $x<=$rows_promochk; $x++){
	$data_promochk=mysql_fetch_array($r_promochk);



	$gen="
	('$data_promochk[corporation_id]','$data_promochk[company_id]','$data_promochk[promo_code]','$data_promochk[seq_pro]','$data_promochk[product_id]','$data_promochk[start_date]','$data_promochk[end_date]','$data_promochk[pro_price]','$data_promochk[unit]','$data_promochk[unit_tp]','$data_promochk[weight]','$data_promochk[promo_group]','$data_promochk[promo_play]','$data_promochk[promo_st]','$data_promochk[condition_tp]','$data_promochk[product_tp]','$data_promochk[reg_date]','$data_promochk[reg_time]','$data_promochk[reg_user]','$data_promochk[upd_date]','$data_promochk[upd_time]','$data_promochk[upd_user]')
		
	";
	$txt_detail=$txt_detail . "," . $gen;
	if($x==$loop_run){
			
			$txt_detail=$txt_start . substr($txt_detail,1);
			$rup_promochk=mysql_db_query($dbname,$txt_detail,$conn_shop);
			$txt_detail="";
			$loop_run=$x+$text_limit;
	}




}

$txt_detail=$txt_start . substr($txt_detail,1);
$rup_promochk=mysql_db_query($dbname,$txt_detail,$conn_shop);



?>