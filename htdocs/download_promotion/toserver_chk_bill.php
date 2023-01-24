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



$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$find_time="SELECT *
FROM `sys_config_download` where target='download_bill' ";
$result_find_time=mysql_db_query($ji_db_sevice,$find_time,$conn_ji);
$data_find_time=mysql_fetch_array($result_find_time);
$start_date=$data_find_time['start_date'];
$end_date=$data_find_time['end_date'];


//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$conf="select branch_id,doc_date,count(doc_no) as bill,sum(quantity) as sumquantity,sum(amount) as sumamount  from 
trn_diary1 where  doc_date between '$start_date' and '$end_date'
group by 
branch_id,doc_date
";
echo $conf;

$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);


for($i=1; $i<=$rows; $i++){
	$data_ans=mysql_fetch_array($result);
	$branch_id=$data_ans[branch_id];
	$doc_date=$data_ans[doc_date];
	$bill=$data_ans[bill];
	$sumquantity=$data_ans[sumquantity];
	$sumamount=$data_ans[sumamount];

	$select_test="select '1'";
	$result_select_test=mysql_db_query($ji_db_sevice,$select_test,$conn_ji);
	if($result_select_test){
	
	}else{
		echo "Time out <br>";
		$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
	}

	$clear="delete from bill_report where doc_date='$doc_date' and shop='$branch_id' ";
	$result_clear=mysql_db_query($ji_db_sevice,$clear,$conn_ji);

	$insert="
		insert into bill_report(`date_add`, `time_add`, `shop`, `doc_date`, `bill`, `sumquantity`, `sumamount`) value(date(now()),time(now()),'$branch_id','$doc_date','$bill','$sumquantity','$sumamount')
	";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);
	if($result_insert){
		echo "$doc_date Insert Complete<br>";
	}else{
		echo "$doc_date Insert None Complete<br>";
	}

}




?>