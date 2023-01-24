<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>
	<style>
	#newspaper-b{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:90%;
	text-align:left;border-collapse:collapse;border:1px solid #69c;margin:20px;}
	#newspaper-b th{font-weight:bold;font-size:14px;color:#039;padding:15px 10px 10px;background:#d0dafd;}
	#newspaper-b tbody{background:#e8edff;}
	#newspaper-b td{color:#669;border-top:1px dashed #fff;padding:10px;}
	#newspaper-b tbody tr:hover td{color:#339;background:#d0dafd;}
	-->

	.last_logobutton {
    background: -moz-linear-gradient(center top , #D5DEE5 5%, #B7CBDB 100%) repeat scroll 0 0 #D5DEE5;
    border: 2px solid #B2CBDF;
    border-radius: 5px 5px 5px 5px;
    box-shadow: 0 1px 0 0 #BBDAF7 inset;
    color: #FFFFFF;
    cursor: pointer;
    display: inline-block;
    font-family: Trebuchet MS;
    font-size: 10pt;
    font-weight: bold;
    padding: 10px 17px;
    text-decoration: none;
    text-shadow: 1px 1px 0 #1B4A7A;
	}


	</style>
<?php
$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
/*$mysql_user='root';
$mysql_pass='SSUP2007';*/
$dbname="pos_ssup";

//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$shop_active_head="select * from promo_head where date(now()) between start_date and end_date  and promo_pos in('M','L') order by promo_pos desc";
$shop_run_active_head=mysql_db_query($dbname,$shop_active_head,$conn_shop);
$shop_rows_active_head=mysql_num_rows($shop_run_active_head);


echo "<table id='newspaper-b' cellspacing='0' cellpadding='0'>";

echo "<tbody>";
echo "<tr >";
echo "<th colspan='4' align='center'>Promotion Active Now</th>";
echo "</tr>";

for($i_ans=1; $i_ans<=$shop_rows_active_head; $i_ans++){
		$data_ans=mysql_fetch_array($shop_run_active_head);
		echo "<tr >";
		echo "<td align='center'>$i_ans</td>";
		echo "<td  align='center' ><a href='report_pro_check.php?promo_code=$data_ans[promo_code]' >$data_ans[promo_pos]</a></td>";
		echo "<td  align='center' >$data_ans[promo_code]</td>";
		echo "<td >$data_ans[promo_des]</td>";
		
		echo "</tr>";
}
echo "</tbody>";
echo "</table>";


		?>