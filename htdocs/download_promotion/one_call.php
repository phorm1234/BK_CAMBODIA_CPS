<?php
set_time_limit(0);

// $c53_2=mysql_connect("localhost","master","master");
 $c53_2=mysql_connect("10.100.53.2","master","master");
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$db_jinet_center="pos_ssup";


$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";



$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");


$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];




mysql_close($c53_2);
 $c53_2=mysql_connect("10.100.53.2","master","master");
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");

if($_GET['promo_code']!=""){
	$promo_code=$_GET['promo_code'];
} else {
	$promo_code=$promo_code;
}



$sql="select  *  from promo_head   where  promo_code='$promo_code' ";
$r=mysql_db_query($db_jinet_center,$sql,$c53_2);
$rows=mysql_num_rows($r);


for($i=1; $i<=$rows; $i++){
	//echo "$i/$rows<br>";
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
	if($rup){
		include("one_send_promo_detail.php");
		include("one_send_promo_check.php");
		include("one_send_promo_branch.php");
	}


}


if($_GET['m']=="manual"){
	echo "<script>window.location='report_pro.php'</script>";
}

?>


