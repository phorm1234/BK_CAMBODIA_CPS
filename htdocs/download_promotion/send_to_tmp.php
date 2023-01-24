<?php
mysql_close($c53_2);
$c53_2=mysql_connect($bath_ip,$bath_user,$bath_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");

if($type_pro=="New"){
	$cr="  upd_date >= date(now()) - interval 2 day   ";
	$cr_detail=" b.upd_date >= date(now()) - interval 2 day     ";
}else if($type_pro=="One"){
	$cr=" promo_code='$key_promo_code' ";
	$cr_detail=" b.promo_code='$key_promo_code'     ";
}else if($type_pro=="All"){
	$cr="  date(now()) between start_date and end_date    ";
	$cr_detail="  date(now()) between b.start_date and b.end_date    ";
}else{
	$cr="  upd_date >= date(now()) - interval 2 day   ";
	$cr_detail=" b.upd_date >= date(now()) - interval 2 day     ";
}


$tmp="select  *  from promo_head   where  $cr   order by promo_code ";
$rtmp=mysql_db_query($db_jinet_center,$tmp,$c53_2);
$rows_tmp=mysql_num_rows($rtmp);

if($rows_tmp==0){
	echo "No promotion update";
	exit();
}


if($rows_tmp>0){
	$clear_tmp="truncate table promo_head_tmp";
	mysql_db_query($dbname,$clear_tmp,$conn_shop);
}
for($tmp=1; $tmp<=$rows_tmp; $tmp++){
	$datatmp=mysql_fetch_array($rtmp);

	$insert_tmp="
		insert into promo_head_tmp
		set
			corporation_id='$datatmp[corporation_id]',
			company_id='$datatmp[company_id]',
			channel='$datatmp[channel]',
			promo_pos='$datatmp[promo_pos]',
			promo_code='$datatmp[promo_code]',
			promo_des='$datatmp[promo_des]',
			promo_tp='$datatmp[promo_tp]',
			start_date='$datatmp[start_date]',
			end_date='$datatmp[end_date]',
			sround_date='$datatmp[sround_date]',
			eround_date='$datatmp[eround_date]',
			date_tp='$datatmp[date_tp]',
			type_p='$datatmp[type_p]',
			promo_amt='$datatmp[promo_amt]',
			unitn='$datatmp[unitn]',
			promo_price='$datatmp[promo_price]',
			discount_tp='$datatmp[discount_tp]',
			compare='$datatmp[compare]',
			coupon='$datatmp[coupon]',
			re_calculate='$datatmp[re_calculate]',
			`condition`='$datatmp[condition]',
			level='$datatmp[level]',
			process='$datatmp[process]',
			status_no='$datatmp[status_no]',
			multiply='$datatmp[multiply]',
			limite_qty='$datatmp[limite_qty]',
			member_tp='$datatmp[member_tp]',
			short_st='$datatmp[short_st]',
			`option`='$datatmp[option]',
			exclude_promo='$datatmp[exclude_promo]',
			check_paid='$datatmp[check_paid]',
			check_repeat='$datatmp[check_repeat]',
			bundle='$datatmp[bundle]',
			accumulate='$datatmp[accumulate]',
			remark='$datatmp[remark]',
			reg_date=date(now()),
			reg_time=time(now()),
			reg_user='IT',
			upd_date=date(now()),
			upd_time=time(now()),
			upd_user='IT'
	";
	//echo $insert_tmp."<br>";
	mysql_db_query($dbname,$insert_tmp,$conn_shop);
}



mysql_close($c53_2);
$c53_2=mysql_connect($bath_ip,$bath_user,$bath_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$tmp="select  a.*  from promo_detail  as a inner join promo_head as b
on a.promo_code=b.promo_code

where  
$cr_detail
order by a.promo_code ";

$rtmp=mysql_db_query($db_jinet_center,$tmp,$c53_2);
$rows_tmp=mysql_num_rows($rtmp);
echo "Rows Pro".$rows_tmp."<br>";
if($rows_tmp>0){
	$clear_tmp="truncate table promo_detail_tmp";
	mysql_db_query($dbname,$clear_tmp,$conn_shop);
}

$txt_detail="";
$txt_start="INSERT INTO promo_detail_tmp( `corporation_id`, `company_id`, `promo_pos`, `promo_code`, `promo_tp`, `status_pro`, `seq_pro`, `order_st`, `product_id`, `location_id`, `product_status`, `start_date`, `end_date`, `round_date`, `show_promo`, `quantity`, `type_discount`, `discount`, `promo_baht`, `promo_price`, `promo_over`, `check_double`, `cal_amt`, `weight`, `doc_status`, `check_pro`, `get_point`, `discount_member`, `product_st`, `balance_qty`, `seq_order`, `re_calculate`, `limite_qty`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES";

$text_limit=300;
$loop_run=$text_limit;
$loop_gen=ceil($rows_tmp/$text_limit);
for($i_detail=1; $i_detail<=$rows_tmp; $i_detail++){

	$data_detail=mysql_fetch_array($rtmp);
	
	$gen="('$data_detail[corporation_id]','$data_detail[company_id]','$data_detail[promo_pos]','$data_detail[promo_code]','$data_detail[promo_tp]','$data_detail[status_pro]','$data_detail[seq_pro]','$data_detail[order_st]','$data_detail[product_id]','$data_detail[location_id]','$data_detail[product_status]','$data_detail[start_date]','$data_detail[end_date]','$data_detail[round_date]','$data_detail[show_promo]','$data_detail[quantity]','$data_detail[type_discount]','$data_detail[discount]','$data_detail[promo_baht]','$data_detail[promo_price]','$data_detail[promo_over]','$data_detail[check_double]','$data_detail[cal_amt]','$data_detail[weight]','$data_detail[doc_status]','$data_detail[check_pro]','$data_detail[get_point]','$data_detail[discount_member]','$data_detail[product_st]','$data_detail[balance_qty]','$data_detail[seq_order]','$data_detail[re_calculate]','$data_detail[limite_qty]','$data_detail[reg_date]','$data_detail[reg_time]','$data_detail[reg_user]','$data_detail[upd_date]','$data_detail[upd_time]','$data_detail[upd_user]')
		
	";
	$txt_detail=$txt_detail . "," . $gen;
	if($i_detail==$loop_run){
			
			$txt_detail=$txt_start . substr($txt_detail,1);
			$rup_detail=mysql_db_query($dbname,$txt_detail,$conn_shop);

			$txt_detail="";
			$loop_run=$i_detail+$text_limit;
	}
	





}
$txt_detail=$txt_start . substr($txt_detail,1);
$rup_detail=mysql_db_query($dbname,$txt_detail,$conn_shop);





mysql_close($c53_2);
$c53_2=mysql_connect($bath_ip,$bath_user,$bath_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$tmp="select  a.*  from promo_check  as a inner join promo_head as b
on a.promo_code=b.promo_code

where  
$cr_detail  
order by a.promo_code ";
$rtmp=mysql_db_query($db_jinet_center,$tmp,$c53_2);
$rows_tmp=mysql_num_rows($rtmp);
echo "Rows Pro".$rows_tmp."<br>";
if($rows_tmp>0){
	$clear_tmp="truncate table promo_check_tmp";
	mysql_db_query($dbname,$clear_tmp,$conn_shop);
}

$txt_detail="";
$txt_start="INSERT INTO promo_check_tmp(`corporation_id`, `company_id`, `promo_code`, `seq_pro`, `product_id`, `start_date`, `end_date`, `pro_price`, `unit`, `unit_tp`, `weight`, `promo_group`, `promo_play`, `promo_st`, `condition_tp`, `product_tp`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES";

$text_limit=300;
$loop_run=$text_limit;
$loop_gen=ceil($rows_tmp/$text_limit);

for($x=1; $x<=$rows_tmp; $x++){
	$data_promochk=mysql_fetch_array($rtmp);



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




mysql_close($c53_2);
$c53_2=mysql_connect($bath_ip,$bath_user,$bath_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$tmp="
select  a.*  from promo_branch  as a inner join promo_head as b
on a.promo_code=b.promo_code

where  
$cr_detail   
order by a.promo_code
";
$rtmp=mysql_db_query($db_jinet_center,$tmp,$c53_2);
$rows_tmp=mysql_num_rows($rtmp);
echo "Rows Pro".$rows_tmp."<br>";
if($rows_tmp>0){
	$clear_tmp="truncate table promo_branch_tmp";
	mysql_db_query($dbname,$clear_tmp,$conn_shop);
}
for($tmp=1; $tmp<=$rows_tmp; $tmp++){
	$datatmp=mysql_fetch_array($rtmp);

			$insert_tmp="
				insert into
				 promo_branch_tmp
				set
				corporation_id='$datatmp[corporation_id]',
				company_id='$datatmp[company_id]',
				promo_code='$datatmp[promo_code]',
				branch_id='$datatmp[branch_id]',
				branch_tp='$datatmp[branch_tp]',
				except='$datatmp[except]',
				start_date='$datatmp[start_date]',
				end_date='$datatmp[end_date]',
	
				reg_date=date(now()),
				reg_time=time(now()),
				reg_user='IT',
				upd_date=date(now()),
				upd_time=time(now()),
				upd_user='IT'
			";
	//echo $insert_tmp."<br>";
	mysql_db_query($dbname,$insert_tmp,$conn_shop);
}




?>