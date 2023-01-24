
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
	margin:0; padding:0;
}
-->
</style>
	<style>
	#newspaper-b{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:95%;
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
	<div align="center"><a href='one_send_ecoupon.php'>Update Ecoupon</a></div>
<?php
set_time_limit(0);

include("from_emp.php");

$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
/*$mysql_user='root';
$mysql_pass='SSUP2007';*/
$dbname="pos_ssup";


//JI
$ji_ip="10.100.53.2";
$ji_user="master";
$ji_pass="master";
$ji_db="pos_ssup";

$employee_id=$_POST['employee_id'];


mysql_close($conn_shop);
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$ecoupon="
select a.* from 
com_ecoupon as a
";
$run_ecoupon=mysql_db_query($dbname,$ecoupon,$conn_shop);;
$rows_ecoupon=mysql_num_rows($run_ecoupon);


echo "<table id='newspaper-b' cellspacing='0' cellpadding='0'>";

echo "<tbody>";
echo "<tr >";

		echo "<th align='center'>รหัสพนักงาน</th>";
		echo "<th  align='center' >ชื่อ</th>";
		echo "<th  align='center' >นามสกุล</th>";
		echo "<th  align='center' >มีผลวันที่</th>";
		echo "<th  align='center' >ถึงวันที่</th>";
		echo "<th  align='center' >ใช้ที่</th>";
		echo "<th  align='center' >วงเงิน</th>";

echo "</tr>";

for($i_ans=1; $i_ans<=$rows_ecoupon; $i_ans++){
		$data_ans=mysql_fetch_array($run_ecoupon);


		echo "<tr >";
		echo "<td align='center'>$data_ans[employee_id]</td>";
		echo "<td  align='center' >$data_ans[name]</td>";
		echo "<td  align='center' >$data_ans[surname]</td>";
		echo "<td  align='center' >$data_ans[start_date]</td>";
		echo "<td align='center'>$data_ans[end_date]</td>";
		echo "<td align='center'>$data_ans[op]</td>";
		echo "<td align='center'>$data_ans[amount_op]</td>";


		
		echo "</tr>";
}
echo "</tbody>";
echo "</table>";


		?>