<?php
include("send_to_tmp.php");

$sql="select  *  from promo_head_tmp order by promo_code ";
$r=mysql_db_query($dbname,$sql,$conn_shop);
$rows=mysql_num_rows($r);



for($i=1; $i<=$rows; $i++){
	$data=mysql_fetch_array($r);


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
		include("send_promo_detail.php");
		include("send_promo_check.php");
		include("send_promo_branch.php");
	}


}


include("send_ecoupon.php");





 $c53_2=mysql_connect("batchdb.ssup.co.th","pos_ssup_opkh","KQLjV8cWakNqBAr3esnMCTwXh38K6vpN");
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$ji_detail="
			select
			a.promo_code,b.count_detail,c.count_branch,d.count_check
			from
			promo_head as a left join (
					SELECT promo_code, count( product_id ) AS count_detail
					FROM promo_detail
					WHERE date( now( ) )
					BETWEEN start_date
					AND end_date
					AND promo_pos
					IN (
					'M', 'L'
					)
					GROUP BY promo_code
			) as b
			on a.promo_code=b.promo_code

			left join (select promo_code,'1' as count_branch from promo_branch where date( now() ) BETWEEN start_date AND end_date group by promo_code) as c
			on a.promo_code=c.promo_code

			left join (select promo_code,count(id) as count_check  from promo_branch where date( now() ) BETWEEN start_date AND end_date group by promo_code) as d
			on a.promo_code=d.promo_code

			where
			date( now() ) BETWEEN a.start_date AND a.end_date
			AND a.promo_pos IN('M', 'L')

";
$run_ji_detail=mysql_db_query($db_jinet_center,$ji_detail,$c53_2);
$rows_ji_detail=mysql_num_rows($run_ji_detail);
if($rows_ji_detail>0){
	$clear_scan="truncate table promo_scan";
	mysql_db_query($dbname,$clear_scan,$conn_shop);
}

for($x=1; $x<=$rows_ji_detail; $x++){
		$data_ji=mysql_fetch_array($run_ji_detail);

		$insert_scan="insert into promo_scan(run_time,`promo_code`, `count_detail`, `count_branch`, `count_check`) values(now(),'$data_ji[promo_code]','$data_ji[count_detail]','$data_ji[count_branch]','$data_ji[count_check]')";
		$run_scan=mysql_db_query($dbname,$insert_scan,$conn_shop);
}



$shop_active_head="select * from promo_head where date(now()) between start_date and end_date  and promo_pos in('M','L') order by promo_pos desc";
$shop_run_active_head=mysql_db_query($dbname,$shop_active_head,$conn_shop);
$shop_rows_active_head=mysql_num_rows($shop_run_active_head);


$num_chk_pro=0;
for($i_ans=1; $i_ans<=$shop_rows_active_head; $i_ans++){
		$data_ans=mysql_fetch_array($shop_run_active_head);
		$promo_code=$data_ans[promo_code];
		$type_p=$data_ans[type_p];
		$promo_pos=$data_ans[promo_pos];
		$promo_amt=$data_ans[promo_amt];



		$member_tp=$data_ans[member_tp];

		$chk_shop="
				select
				a.promo_code,b.count_detail+c.count_branch+d.count_check as num_shop
				from
				promo_head as a left join (
						SELECT promo_code, count( product_id ) AS count_detail
						FROM promo_detail
						WHERE promo_code='$promo_code'
						GROUP BY promo_code
				) as b
				on a.promo_code=b.promo_code

				left join (select promo_code,'1' as count_branch from promo_branch where promo_code='$promo_code' group by promo_code) as c
				on a.promo_code=c.promo_code

				left join (select promo_code,count(id) as count_check  from promo_branch where promo_code='$promo_code' group by promo_code) as d
				on a.promo_code=d.promo_code

				where
				a.promo_code='$promo_code'
		";
		$run_chk_shop=mysql_db_query($dbname,$chk_shop,$conn_shop);
		$data_chk_shop=mysql_fetch_array($run_chk_shop);
		$num_shop=$data_chk_shop['num_shop'];

		$chk_scan="select count_detail+count_branch+count_check as num_scan from promo_scan where promo_code='$promo_code' ";
		$run_chk_scan=mysql_db_query($dbname,$chk_scan,$conn_shop);
		$data_chk_scan=mysql_fetch_array($run_chk_scan);
		$num_scan=$data_chk_scan['num_scan'];
		


		if( $num_shop==$num_scan ){
			$chk_pro="";
		}else{
			include("one_call.php");
		}




		//echo $show_list_detail;
}



if($num_chk_pro==0){
	echo "<h2><center>Promotion Complete</center></h2>";
}

?>
