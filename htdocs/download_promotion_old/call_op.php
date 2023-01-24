<?php
set_time_limit(0);
?>
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>

<?php
echo "<center>";
echo "<div id='show_load'>";
echo "<img src='preload.gif'>";
echo "</div>";


// $c53_2=mysql_connect("localhost","master","master");
 $c53_2=mysql_connect("10.100.53.2","master","master");
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");
$db_jinet_center="pos_ssup";


$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
/*$mysql_user='root';
$mysql_pass='SSUP2007';*/
$dbname="pos_ssup";


$tran_set=rand();
//echo "Connecting : $ipserver@$mysql_user<br>";
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");


$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];

//echo "SHOP : $shop";
/*$run="SELECT * FROM promo_config_transfer";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];*/


if($conn_shop){

		$chk_ative="select * from trn_promotion_tmp1";
		$r_ative=mysql_db_query($dbname,$chk_ative,$conn_shop);
		$rows_ative=mysql_num_rows($r_ative);
		if($rows_ative==0){
				echo " Status : No Active Bill<br>";
				include("send_promo_head.php");

				echo "<script>document.getElementById('show_load').innerHTML='<h1 style=\"color: #CCCCCC\">Complete</h1>'</script>'</script>";
		} else {//if chk ative 
			$log="insert into promo_log(`tran_date`, `tran_system`,tran_set, tran_comment,shop) values(now(),'Jinet','$tran_set','ActiveBill','$shop')";
			$r_log=mysql_db_query($db_jinet_center,$log,$c53_2);
			echo "<script>document.getElementById('show_load').innerHTML='<h1 style=\"color: #CCCCCC\">Status : Active Bill </h1>'</script>";
			
		}

} else {//if chk connect server
		echo "<script>document.getElementById('show_load').innerHTML='<h1 style=\"color: #CCCCCC\">No Connect</h1>'</script>";
}

echo "</center>";

?>
