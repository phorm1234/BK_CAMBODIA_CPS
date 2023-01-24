<?php

$sql="select  *  from promo_head   where  upd_date >= date(now()) - interval 0 day  order by promo_code";
$r=mysql_db_query($db_jinet_center,$sql,$c53_2);
$rows=mysql_num_rows($r);
echo "Update : $rows Items <br>";
if($rows==0){
			$log="insert into promo_log(`tran_date`, `tran_system`,tran_set, tran_comment,shop) values(now(),'Jinet','$tran_set','New Promotion is none','$shop')";
			$r_log=mysql_db_query($db_jinet_center,$log,$c53_2);
}

for($i=1; $i<=$rows; $i++){
	//echo "$i/$rows<br>";
	$data=mysql_fetch_array($r);

	echo "$i. Promotion : " . $data[promo_code] . " - " . $data[promo_des] . "<br>";
	$chk="select * from promo_head where promo_code='$data[promo_code]' ";
	$rchk=mysql_db_query($dbname,$chk,$conn_shop);
	$rowsChk=mysql_num_rows($rchk);

	if($rowsChk>0){
		$caseUp="update ";
		$timeUp="
			,
		upd_date=date(now()),
		upd_time=time(now()),
		upd_user='IT'";
		$where=" where promo_code='$data[promo_code]' ";

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


	$up="
		$caseUp
		 promo_head
		set
		corporation_id='$data[corporation_id]',
		company_id='$data[company_id]',
		channel='$data[channel]',
		promo_pos='$data[promo_pos]',
		promo_code='$data[promo_code]',
		promo_des='$data[promo_des]',
		promo_tp='$data[promo_tp]',
		start_date='$data[start_date]',
		end_date='$data[end_date]',
		sround_date='$data[sround_date]',
		eround_date='$data[eround_date]',
		date_tp='$data[date_tp]',
		type_p='$data[type_p]',
		promo_amt='$data[promo_amt]',
		unitn='$data[unitn]',
		promo_price='$data[promo_price]',
		discount_tp='$data[discount_tp]',
		compare='$data[compare]',
		coupon='$data[coupon]',
		re_calculate='$data[re_calculate]',
		`condition`='$data[condition]',
		level='$data[level]',
		process='$data[process]',
		status_no='$data[status_no]',
		multiply='$data[multiply]',
		limite_qty='$data[limite_qty]',
		member_tp='$data[member_tp]',
		short_st='$data[short_st]',
		`option`='$data[option]',
		exclude_promo='$data[exclude_promo]',
		check_paid='$data[check_paid]',
		check_repeat='$data[check_repeat]',
		bundle='$data[bundle]',
		accumulate='$data[accumulate]',
		remark='$data[remark]'
		$timeUp
		$where
	";
	//echo "$up<br>";
	$rup=mysql_db_query($dbname,$up,$conn_shop);
	$promo_code=$data['promo_code'];
	
	if($rup){
		
		$log="insert into promo_log(`tran_date`, `tran_system`,tran_set, `seq`, `promo_code`,find_pro, `tran_status`,shop) values(now(),'Jinet','$tran_set','$i','$promo_code','$rows','Y','$shop')";
		$r_log=mysql_db_query($db_jinet_center,$log,$c53_2);
		include("send_promo_detail.php");
		include("send_promo_check.php");
		include("send_promo_branch.php");
	} else {

		$log="insert into promo_log(`tran_date`, `tran_system`, `tran_set`, `seq`, `promo_code`,find_pro, `tran_status`,shop) values(now(),'Jinet','$tran_set','$i','$promo_code','$rows','N','$shop')";
		$r_log=mysql_db_query($db_jinet_center,$log,$c53_2);
	}
	



}



include("send_ecoupon.php");

?>