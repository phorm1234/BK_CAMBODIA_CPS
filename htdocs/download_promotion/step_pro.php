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
	#newspaper-b{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:14px;width:95%;
	text-align:left;border-collapse:collapse;border:1px solid #69c;margin:20px;}
	#newspaper-b th{font-weight:bold;font-size:14px;color:#039;padding:15px 10px 10px;background:#d0dafd;}
	#newspaper-b tbody{background:#e8edff;}
	#newspaper-b td{color:#669;border-top:1px dashed #fff;padding:10px;}
	#newspaper-b tbody tr:hover td{color:#339;background:#d0dafd;}
	-->

	#newspaper-x{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:14px;width:95%;
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
$dbname="pos_ssup";

/*
$ipserver='10.100.53.2';
$mysql_user='master';
$mysql_pass='master';
$dbname="pos_ssup";*/


$get_promo_code=$_GET['promo_code'];
//JI
$ji_ip="10.100.53.2";
$ji_user="master";
$ji_pass="master";
$ji_db="pos_ssup";

//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");


$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop);
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];




$shop_active_head="
select sub.* from (
select *,if(promo_pos='M','a','b') as sort_type from promo_head where date(now()) between start_date and end_date  and promo_pos in('M','L') 


) as sub
order by sub.sort_type,sub.level";
$shop_run_active_head=mysql_db_query($dbname,$shop_active_head,$conn_shop);
$shop_rows_active_head=mysql_num_rows($shop_run_active_head);

echo "<div align='center' ><h2>Click รูปแอปเปิ้ลเพื่อดูวิธีการเล่นโปรโมชั่น</h2></div>";
echo "<table id='newspaper-b' cellspacing='0' cellpadding='0'>";

echo "<tbody>";
echo "<tr >";

		echo "<th align='center'>ลำดับ</th>";
		echo "<th  align='center' >ประเภท</th>";
		echo "<th  align='center' >Level</th>";
		echo "<th  align='center' >รหัส</th>";
		echo "<th  align='center' >เริ่มเล่น</th>";
		echo "<th  align='center' >สิ้นสุด</th>";
		echo "<th  align='center' >สิทธิ์</th>";
		echo "<th >รายละเอียด</th>";
		echo "<th >ซื้อครบ</th>";
		echo "<th >วิธีเล่น</th>";
		

echo "</tr>";



				function fomatdate($dt) {    //ปรับรูปแบบวันที่จาก 0000-00-00 เป็น 00/00/0000
					$y=substr($dt,0,4);
					$m=substr($dt,5,2);
					$d=substr($dt,8,2);
					$dt_fomat="$d/$m/$y";
					return $dt_fomat;
				}




$num_chk_pro=0;
for($i_ans=1; $i_ans<=$shop_rows_active_head; $i_ans++){
		$data_ans=mysql_fetch_array($shop_run_active_head);
		$promo_code=$data_ans[promo_code];
		$level=$data_ans[level];
		$type_p=$data_ans[type_p];
		$promo_pos=$data_ans[promo_pos];
		$promo_amt=$data_ans[promo_amt];
		$start_date=$data_ans[start_date];
		$start_date=fomatdate($start_date);
		$end_date=$data_ans[end_date];
		$end_date=fomatdate($end_date);

		if($promo_pos=="M"){
			$promo_type="โปรหลัก";
			$style_td=" ";
			$level="";
		}else if($promo_pos=="L"){
			$style_td=" bgcolor='#f9fab1' ";
			$promo_type="ท้ายบิล";
		}

		$member_tp=$data_ans[member_tp];
		if($member_tp=="N"){
			$member_type="";
		}else if($member_tp=="Y"){
			$member_type="<span style='color: #B3983B;	font-weight: bold;'>Member</span>";
		}


		
	


		$promo_amt=$data_ans[promo_amt];
		if($promo_amt==0){
			$promo_amt_show="";
		}else{
			$promo_amt_show=$promo_amt;
		}


			$num_chk_pro++;
			echo "<tr >";
			echo "<td align='center' $style_td valign='top'>$i_ans</td>";
			echo "<td  align='center' $style_td valign='top'>$level</td>";
			echo "<td  align='center' $style_td valign='top'>$promo_type</td>";
			echo "<td  align='center' $style_td valign='top'>$data_ans[promo_code]</td>";
			echo "<td  align='center' $style_td valign='top'>$start_date</td>";
			echo "<td  align='center' $style_td valign='top'>$end_date</td>";
			echo "<td  align='center' $style_td valign='top'>$member_type</td>";
			echo "<td $style_td valign='top'>$data_ans[promo_des]</td>";
			echo "<td $style_td valign='top'>$promo_amt_show</td>";
			echo "<td $style_td valign='top'><a href='step_pro2.php?promo_code=$promo_code&m=manual'><img src='apple.png' width='50px' height='50px'></a></td>";
			

			echo "</tr>";

}
echo "</tbody>";
echo "</table>";


?>