<?php
set_time_limit(0);


$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";

//JI
$ji_ip="10.100.59.151";
$ji_user="crm-op-kh";
$ji_pass="BxcfpffA8Y98qsDqMyQCXY4bsPQSbZnp";
$ji_db="pos_ssup_opkh";

$ji_db_sevice="service_pos_opkh";
$num_day=5;


//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$conf="
select 
a.member_id,a.branch_id,b.doc_no,b.doc_date,b.flag,b.promo_code
from trn_diary1 as a inner join trn_diary2 as b
on a.doc_no=b.doc_no
where
a.member_id<>'' and
b.promo_code<>'' and
a.doc_date >= date(now())
a.doc_tp<>'CN'
group by
a.member_id,a.branch_id,b.doc_no,b.doc_date,b.flag,b.promo_code
";

$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or id("Link down");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
		
		
$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);

for($i=1; $i<=$rows; $i++){
	$data_ans=mysql_fetch_array($result);
	$member_id=$data_ans[member_id];
	$branch_id=$data_ans[branch_id];
	$doc_no=$data_ans[doc_no];
	$doc_date=$data_ans[doc_date];
	$flag=$data_ans[flag];
	$promo_code=$data_ans[promo_code];

	$select_test="select '1' ";
	$result_select_test=mysql_db_query($ji_db_sevice,$select_test,$conn_ji);
	if($result_select_test){
	
	}else{
		echo "Time out <br>";
		$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
	}
			
			




	$insert="
		insert into promo_play_history(`date_add`, `time_add`,`shop`, `member_id`, `doc_no`, `doc_date`, `promo_code`) value(date(now()),time(now()),'$branch_id','$member_id','$doc_no','$doc_date','$promo_code')
	";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);
	if($result_insert){
		echo "$i $doc_date Insert Complete<br>";
	}else{
		echo "$i $doc_date Insert None Complete<br>";
	}

}


//============================ up bill cancel
//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$conf="select * from trn_diary1 where doc_date >= date(now())-interval 5 day and flag='C' ";
echo $conf;
$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or id("Link down");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
		
		
$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);
$i=1;
for($i=1; $i<=$rows; $i++){
	$data_ans=mysql_fetch_array($result);
	$doc_no=$data_ans['doc_no'];

	$select_test="select '1' ";
	$result_select_test=mysql_db_query($ji_db_sevice,$select_test,$conn_ji);
	if($result_select_test){
	
	}else{
		echo "Time out <br>";
		$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
	}
			
			


	$insert="update trn_diary1 set flag='C' where doc_no='$doc_no'	 ";
	echo "$insert<br>";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);
	$insert="update trn_diary2 set flag='C' where doc_no='$doc_no'	 ";
	echo "$insert<br>";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);

	$insert="update promo_play_history set flag='C' where doc_no='$doc_no'	 ";
	echo "$insert<br>";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);
	if($result_insert){
		echo "$i $doc_no Cancel Complete<br>";
	}else{
		echo "$i $doc_no Cancel None Complete<br>";
	}

}


?>