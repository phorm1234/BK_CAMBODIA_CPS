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

	#newspaper-x{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:95%;
	text-align:left;border-collapse:collapse;border:1px solid #69c;margin:20px;}
	#newspaper-x th{font-weight:bold;font-size:14px;color:#039;padding:15px 10px 10px;background:#d0dafd;}
	#newspaper-x tbody{background:#FCFCFA;}
	#newspaper-x td{color:#669;border-top:1px dashed #fff;padding:10px;}
	#newspaper-x tbody tr:hover td{color:#339;background:#F5FAAB;}

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
/*$mysql_user='root';
$mysql_pass='SSUP2007';*/
$dbname="pos_ssup";


$get_promo_code=$_GET['promo_code'];
//JI
$ji_ip="10.100.53.2";
$ji_user="master";
$ji_pass="master";
$ji_db="pos_ssup";

//shop
mysql_close($conn_shop);
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");


$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop);
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];



//ji
$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass);
mysql_query("SET character_set_results=tis620");
mysql_query("SET character_set_client=tis620");
mysql_query("SET character_set_connection=tis620");

$ji_detail="
			select
			a.promo_code,b.count_detail,c.count_branch,d.count_check
			from
			promo_head as a left join (
					SELECT promo_code, count( product_id ) AS count_detail
					FROM promo_detail
					WHERE date( now( ) )
					BETWEEN start_date
					AND end_date
					AND promo_pos
					IN (
					'M', 'L'
					)
					GROUP BY promo_code
			) as b
			on a.promo_code=b.promo_code

			left join (select promo_code,'1' as count_branch from promo_branch where date( now() ) BETWEEN start_date AND end_date group by promo_code) as c
			on a.promo_code=c.promo_code

			left join (select promo_code,count(id) as count_check  from promo_branch where date( now() ) BETWEEN start_date AND end_date group by promo_code) as d
			on a.promo_code=d.promo_code

			where
			date( now() ) BETWEEN a.start_date AND a.end_date
			AND a.promo_pos IN('M', 'L')

";
$run_ji_detail=mysql_db_query($ji_db,$ji_detail,$conn_ji);
$rows_ji_detail=mysql_num_rows($run_ji_detail);

echo "<h2><center>Promotion ปัจจุบันมีทั้งหมด $rows_ji_detail โปรโมชั่น</center></h2>";



if($rows_ji_detail>0){
	$clear_scan="truncate table promo_scan";
	mysql_db_query($dbname,$clear_scan,$conn_shop);
}

for($x=1; $x<=$rows_ji_detail; $x++){
		$data_ji=mysql_fetch_array($run_ji_detail);

		$insert_scan="insert into promo_scan(run_time,`promo_code`, `count_detail`, `count_branch`, `count_check`) values(now(),'$data_ji[promo_code]','$data_ji[count_detail]','$data_ji[count_branch]','$data_ji[count_check]')";
		$run_scan=mysql_db_query($dbname,$insert_scan,$conn_shop);
}






$shop_active_head="select * from promo_head where date(now()) between start_date and end_date  and promo_pos in('M','L') order by promo_pos desc";
$shop_run_active_head=mysql_db_query($dbname,$shop_active_head,$conn_shop);
$shop_rows_active_head=mysql_num_rows($shop_run_active_head);


echo "<table id='newspaper-b' cellspacing='0' cellpadding='0'>";

echo "<tbody>";
echo "<tr >";

		echo "<th align='center'>ลำดับ</th>";
		echo "<th  align='center' >ประเภท</th>";
		echo "<th  align='center' >รหัส</th>";
		echo "<th  align='center' >สิทธิ์</th>";
		echo "<th >รายละเอียด</th>";
		echo "<th >ซื้อครบ</th>";
		echo "<th >Download</th>";
echo "</tr>";

$num_chk_pro=0;
for($i_ans=1; $i_ans<=$shop_rows_active_head; $i_ans++){
		$data_ans=mysql_fetch_array($shop_run_active_head);
		$promo_code=$data_ans[promo_code];
		$type_p=$data_ans[type_p];
		$promo_pos=$data_ans[promo_pos];
		$promo_amt=$data_ans[promo_amt];

		if($promo_pos=="M"){
			$promo_type="โปรหลัก";
			$style_td=" ";
		}else if($promo_pos=="L"){
			$style_td=" bgcolor='#FBE5C9' ";
			$promo_type="ท้ายบิล";
		}

		$member_tp=$data_ans[member_tp];
		if($member_tp=="N"){
			$member_type="";
		}else if($member_tp=="Y"){
			$member_type="<span style='color: #B3983B;	font-weight: bold;'>Member</span>";
		}

		$chk_shop="
				select
				a.promo_code,b.count_detail+c.count_branch+d.count_check as num_shop
				from
				promo_head as a left join (
						SELECT promo_code, count( product_id ) AS count_detail
						FROM promo_detail
						WHERE promo_code='$promo_code'
						GROUP BY promo_code
				) as b
				on a.promo_code=b.promo_code

				left join (select promo_code,'1' as count_branch from promo_branch where promo_code='$promo_code' group by promo_code) as c
				on a.promo_code=c.promo_code

				left join (select promo_code,count(id) as count_check  from promo_branch where promo_code='$promo_code' group by promo_code) as d
				on a.promo_code=d.promo_code

				where
				a.promo_code='$promo_code'
		";
		$run_chk_shop=mysql_db_query($dbname,$chk_shop,$conn_shop);
		$data_chk_shop=mysql_fetch_array($run_chk_shop);
		$num_shop=$data_chk_shop['num_shop'];

		$chk_scan="select count_detail+count_branch+count_check as num_scan from promo_scan where promo_code='$promo_code' ";
		$run_chk_scan=mysql_db_query($dbname,$chk_scan,$conn_shop);
		$data_chk_scan=mysql_fetch_array($run_chk_scan);
		$num_scan=$data_chk_scan['num_scan'];
		

		if($get_promo_code==$promo_code){
			$detail="
				select 
					a.seq_pro,a.product_id,b.name_product,b.barcode,a.type_discount,a.discount 
				from 
					promo_detail as a inner join com_product_master as b
					on a.product_id=b.product_id
				where
					a.promo_code='$promo_code'
				order by seq_pro
			";
			$run_detail=mysql_db_query($dbname,$detail,$conn_shop);
			$rows_detail=mysql_num_rows($run_detail);
			$head_detail="<table border=0 align='center' width=100% id='newspaper-x'>";
			$head_td="";
			for($d=1; $d<=$rows_detail; $d++){
				$data_detail=mysql_fetch_array($run_detail);
				$type_discount=$data_detail['type_discount'];
				$discount=$data_detail['discount'];
				
				if($type_discount=="Free"){
					$target="แถมฟรี";
				}else if($type_discount=="Percent"){
					$target="ลด $discount %";
				}else if($type_discount=="Price1"){
					$target="ขายในราคา $discount บาท";
				}else if($type_discount=="Discount"){
					$target="ลด $discount บาท";
				}else if($type_discount=="Normal"){
					$target="ตัวซื้อ";
				}
				$show_list="
					<tr style='font-size: 10px'><td>$target<td><td>$data_detail[product_id]<td><td>$data_detail[name_product]<td></tr>
				";
				$head_td=$head_td . $show_list;
			}
			$head_last="</table>";

			$show_list_detail="<tr bgcolor='#990000'><td ></td><td></td><td colspan='5'>" . $head_detail.$head_td.$head_last . "</td></tr>";
		}else{
			$show_list_detail="";
		}

			//echo $show_list_detail;
			
	
		//echo "$promo_code : $num_shop=$num_scan<br>";
		if( $num_shop==$num_scan ){
			$chk_pro="";
		}else{
			$chk_pro="Update";
		}

		$promo_amt=$data_ans[promo_amt];
		if($promo_amt==0){
			$promo_amt_show="";
		}else{
			$promo_amt_show=$promo_amt;
		}

		if($chk_pro=="Update"){
			$num_chk_pro++;
			echo "<tr >";
			echo "<td align='center' $style_td valign='top'>$i_ans</td>";
			echo "<td  align='center' $style_td valign='top'>$promo_type</td>";
			echo "<td  align='center' $style_td valign='top'>$data_ans[promo_code]</td>";
			echo "<td  align='center' $style_td valign='top'>$member_type</td>";
			echo "<td $style_td valign='top'>$data_ans[promo_des]</td>";
			echo "<td $style_td valign='top'>$promo_amt_show</td>";
			echo "<td $style_td valign='top'><a href='one_call.php?promo_code=$promo_code&m=manual'>$chk_pro</a></td>";
			echo "</tr>";
		}
		//echo $show_list_detail;
}
echo "</tbody>";
echo "</table>";



mysql_close($conn_shop);
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
if($num_chk_pro==0){
	echo "<h2><center>Promotion Complete</center></h2>";
}else{
	echo "<h2><center>Promotion None Complete</center></h2>";
	$keep="insert into promo_log(tran_date,tran_system,tran_comment,shop) values(now(),'SHOP','Promotion None Complete','$shop')";
	$run_detail=mysql_db_query($ji_db,$keep,$conn_ji);
}


?>