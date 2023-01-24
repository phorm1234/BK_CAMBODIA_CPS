<?php
set_time_limit(0);
$doc_date=$_GET['doc_date'];

$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";

//JI
$ji_ip="10.100.53.2";
$ji_user="master";
$ji_pass="master";
$ji_db="pos_ssup";


$ji_db_sevice="service_pos_op";
$num_day=5;




//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$find="select * from member_idcard_other_tmp  ";
$run_find=mysql_db_query($dbname,$find,$conn_shop);
$rows_find=mysql_num_rows($run_find);
echo $rows_find;
mysql_close($conn_shop);

$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");






for($i=1; $i<=$rows_find; $i++){
	$data=mysql_fetch_array($run_find);
	

		$up="insert into member_idcard_other_tmp(`ip`, `shop`, `add_type`, `add_readtype`, `otp_code`, `member_no`, `id_card`, `mobile_no`, `name`, `surname`, `birthday`, `address`, `mu`, `tambon_name`, `amphur_name`, `province_name`, `mr`, `sex`, `mr_en`, `fname_en`, `lname_en`, `card_at`, `start_date`, `end_date`, `channel`, `doc_no`, `doc_date`, `doc_time`, `amount`, `net`, `flag`, `promo_code`, `time_up`) values('$data[ip]','$data[shop]','$data[add_type]','$data[add_readtype]','$data[otp_code]','$data[member_no]','$data[id_card]','$data[mobile_no]','$data[name]','$data[surname]','$data[birthday]','$data[address]','$data[mu]','$data[tambon_name]','$data[amphur_name]','$data[province_name]','$data[mr]','$data[sex]','$data[mr_en]','$data[fname_en]','$data[lname_en]','$data[card_at]','$data[start_date]','$data[end_date]','$data[channel]','$data[doc_no]','$data[doc_date]','$data[doc_time]','$data[amount]','$data[net]','$data[flag]','$data[promo_code]','$data[time_up]')";

	
	echo "Loop:$i" . $up . "<br>";
	
	mysql_db_query($ji_db_sevice,$up,$conn_ji);
}







?>