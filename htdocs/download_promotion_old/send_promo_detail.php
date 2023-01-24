<?php


$sql_detail="select * from promo_detail  where  promo_code ='$promo_code' order by seq_pro ";
$r_detail=mysql_db_query($db_jinet_center,$sql_detail,$c53_2);
$rows_detail=mysql_num_rows($r_detail);

$del_detail="delete from promo_detail where promo_code='$promo_code' ";
$r_del_detail=mysql_db_query($dbname,$del_detail,$conn_shop);


for($i_detail=1; $i_detail<=$rows_detail; $i_detail++){
	//echo "$i/$rows<br>";
	$data_detail=mysql_fetch_array($r_detail);
	//mysql_db_query($dbshop,"delete from promo_detail  where promo_code='$data[promo_code]' ",$conshop);


	
	/*$chk_detail="select * from promo_detail where promo_code='$data_detail[promo_code]' and seq_pro='$data_detail[seq_pro]' and product_id='$data_detail[product_id]' ";
	$rchk_detail=mysql_db_query($dbname,$chk_detail,$conn_shop);
	$rowsChk_detail=mysql_num_rows($rchk_detail);*/
	$rowsChk_detail=0;

	if($rowsChk_detail>0){
		$caseUp="update ";
		$timeUp="
		,
		upd_date=date(now()),
		upd_time=time(now()),
		upd_user='IT'";

		$where=" where promo_code='$data_detail[promo_code]'  and seq_pro='$data_detail[seq_pro]' and product_id='$data_detail[product_id]' ";
	}else{
		$caseUp="insert into";
		$timeUp="
		,
		reg_date=date(now()),
		reg_time=time(now()),
		reg_user='IT',
		upd_date=date(now()),
		upd_time=time(now()),
		upd_user='IT'";
		$where="";
	}


	$up_detail="
		$caseUp
		 promo_detail
		set
		corporation_id='$data_detail[corporation_id]',
		company_id='$data_detail[company_id]',
		promo_pos='$data_detail[promo_pos]',
		promo_code='$data_detail[promo_code]',
		promo_tp='$data_detail[promo_tp]',
		status_pro='$data_detail[status_pro]',
		seq_pro='$data_detail[seq_pro]',
		order_st='$data_detail[order_st]',
		product_id='$data_detail[product_id]',
		location_id='$data_detail[location_id]',
		product_status='$data_detail[product_status]',
		start_date='$data_detail[start_date]',
		end_date='$data_detail[end_date]',
		round_date='$data_detail[round_date]',
		show_promo='$data_detail[show_promo]',
		quantity='$data_detail[quantity]',
		type_discount='$data_detail[type_discount]',
		discount='$data_detail[discount]',
		promo_baht='$data_detail[promo_baht]',
		promo_price='$data_detail[promo_price]',
		promo_over='$data_detail[promo_over]',
		check_double='$data_detail[check_double]',
		cal_amt='$data_detail[cal_amt]',
		weight='$data_detail[weight]',
		doc_status='$data_detail[doc_status]',
		check_pro='$data_detail[check_pro]',
		get_point='$data_detail[get_point]',
		discount_member='$data_detail[discount_member]',
		product_st='$data_detail[product_st]',
		balance_qty='$data_detail[balance_qty]',
		seq_order='$data_detail[seq_order]',
		re_calculate='$data_detail[re_calculate]',
		limite_qty='$data_detail[limite_qty]'

		$timeUp
		$where
	";
	$rup_detail=mysql_db_query($dbname,$up_detail,$conn_shop);
	if($rup){
		$up_log="
		update promo_log
			set
				find_l='$rows_detail',
				insert_l=insert_l+1
			where 
				tran_set='$tran_set' and promo_code='$promo_code'
		";
		$r_up_log=mysql_db_query($db_jinet_center,$up_log,$c53_2);
		
	}


}

?>