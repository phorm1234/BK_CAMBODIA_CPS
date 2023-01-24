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
	#newspaper-b td{color:#669;border-top:1px dashed #fff;padding:5px;}
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
set_time_limit(0);


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

$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];









$conf="
	SELECT a.doc_no,a.doc_time,a.member_id,a.quantity,a.amount FROM 
	trn_diary1 as a left join (select doc_date,doc_no from service_log where doc_date=date(now()) and type_case='HEAD') as b
	on a.doc_date=b.doc_date and 
	a.doc_no=b.doc_no
	where
	a.doc_date=date(now()) and 
	b.doc_no is null
";		
$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);

echo "<table id='newspaper-b' cellspacing='0' cellpadding='0'>";

echo "<tbody>";
echo "<tr >";

		echo "<th align='center'>ลำดับ</th>";
		echo "<th  align='center' >วันที่เปิดบิล</th>";
		echo "<th  align='center' >เลขที่</th>";
		echo "<th  align='center' >สมาชิก</th>";
		echo "<th  align='center' >จำนวนชิ้น</th>";
		echo "<th  align='center' >จำนวนเงิน</th>";

echo "</tr>";


for($i=1; $i<=$rows; $i++){
	$databill=mysql_fetch_array($result);




			$doc_no=$databill['doc_no'];
			$doc_date=$databill['doc_date'];
			$branch_id=$databill['branch_id'];
			
			$doc_tp=$databill['doc_tp'];
			$status_no=$databill['status_no'];
			$flag=$databill['flag'];
			$member_id=$databill['member_id']; 
			
			$quantity=$databill['quantity'];
			$amount=$databill['amount'];
			
			echo "<tr >";
			echo "<td align='center' $style_td valign='top'>$i</td>";
			echo "<td  align='center' $style_td valign='top'>$doc_date</td>";
			echo "<td  align='center' $style_td valign='top'>$doc_no</td>";
			echo "<td  align='center' $style_td valign='top'>$member_id</td>";
			echo "<td  align='center' $style_td valign='top'>$quantity</td>";
			echo "<td  align='center' $style_td valign='top'>$amount</td>";

			echo "</tr>";

}

echo "</tbody>";
echo "</table>";


?>
่<div align="center"><a href="toserver_bill_wait_save.php" class='last_logobutton' >ส่งเข้า Server</a></div>