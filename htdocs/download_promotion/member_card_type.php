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





$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$find="select * from member_card_type  ";
echo $find;
$run_find=mysql_db_query($ji_db_sevice,$find,$conn_ji);
$rows_find=mysql_num_rows($run_find);
echo $rows_find;
mysql_close($conn_ji);

//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
for($i=1; $i<=$rows_find; $i++){
	$data=mysql_fetch_array($run_find);
	
	$chk="select * from member_card_type where id_start='$data[id_start]' ";
	$run_chk=mysql_db_query($dbname,$chk,$conn_shop);
	$rows_chk=mysql_num_rows($run_chk);
	if($rows_chk>0){
		$up="update member_card_type 
			set
				id_start='$data[id_start]',
				id_end='$data[id_end]',
				ops='$data[ops]',
				des1='$data[des1]',
				des2='$data[des2]'
			where
				id_start='$data[id_start]'
		";
	}else{
		$up="insert into member_card_type(`id_start`, `id_end`, `ops`, `des1`, `des2`) values('$data[id_start]','$data[id_end]','$data[ops]','$data[des1]','$data[des2]')";
	}
	
	echo "Loop:$i" . $up . "<br>";
	mysql_db_query($dbname,$up,$conn_shop);
}







?>