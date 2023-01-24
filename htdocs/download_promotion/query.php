<?php
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
echo "$shop<br>";


$sql=$_GET['sql'];
$sql=str_replace("@@@","'",$sql);
echo "Query:$sql<br>";
$run=mysql_db_query($dbname,$sql,$conn_shop) or die("NONO");

?>