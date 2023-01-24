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
	#newspaper-b th{font-weight:bold;font-size:12px;color:#039;padding:5px 5px 5px;background:#FFCC00;}
	#newspaper-b tbody{background:#e8edff;}
	#newspaper-b td{color:#669;border-top:1px dashed #000000;padding:10px;}
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
    cursor: pointer;
    display: inline-block;
    font-family: Trebuchet MS;
    font-size: 14pt;
    font-weight: bold;
    padding: 10px 17px;
	}


	.style1 {
	color: #165c91;
	font-weight: bold;
}
.style7 {color: #0066CC}
.style9 {color: #FF9900}
    </style>
	
	
<div align="center">
  <table border="0" align="center">
    <tr>
      <td><div align="center" class="style1">เลือกโปรโมชั่นที่ต้องการ Update </div></td>
    </tr>
    <tr>
      <td><p align="center"><span class="style7">บรรทัดสีฟ้าคือ โปรหลัก</span><br>
        <span class="style9">บรรทัดสีเหลืองคือ โปรท้ายบิล </span></p>      </td>
    </tr>
  </table>
  <?php
set_time_limit(0);
include("inc/connect.php");
/*
$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";
*/



$ipserver=$bath_ip;
$mysql_user=$bath_user;
$mysql_pass=$bath_pass;
$dbname=$bath_db;




$get_promo_code=$_GET['promo_code'];
//JI
$ji_ip=$bath_ip;
$ji_user=$bath_user;
$ji_pass=$bath_pass;
$ji_db=$bath_db;

//shop

$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die(mysql_error());
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

mysql_select_db($ji_db) or die(mysql_error());


$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop);
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];




$shop_active_head="
select sub.* from (
select *,if(promo_pos='M','a','b') as sort_type from promo_head where date(now()) between start_date and end_date 
) as sub
order by sub.sort_type,sub.level,sub.promo_code";

$shop_run_active_head=mysql_db_query($dbname,$shop_active_head,$conn_shop) or die(mysql_error());
$shop_rows_active_head=mysql_num_rows($shop_run_active_head);


echo "<table id='newspaper-b' cellspacing='0' cellpadding='0'>";

echo "<tbody>";
echo "<tr >";

		echo "<th align='center'>ลำดับ</th>";
		echo "<th  align='center' >ประเภท</th>";
		echo "<th  align='center' >Level</th>";
		echo "<th  align='center' >รหัส</th>";
		echo "<th  align='center' >เริ่มเล่น</th>";
		echo "<th  align='center' >สิ้นสุด</th>";
		echo "<th >รายละเอียด</th>";
		echo "<th >ซื้อครบ</th>";
		echo "<th >Download</th>";
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
			//$member_type="<span style='color: #eb5842;	font-weight: bold;'>Member</span>";
			$member_type="<img src='img/ops.gif' width='40px' height='30px'>";
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
			echo "<td  align='center' $style_td valign='top'><strong>$promo_type</strong></td>";
			echo "<td  align='center' $style_td valign='top'>$level</td>";
			
			echo "<td  align='center' $style_td valign='top'>$data_ans[promo_code]</td>";
			echo "<td  align='center' $style_td valign='top'>$start_date</td>";
			echo "<td  align='center' $style_td valign='top'>$end_date</td>";
			echo "<td $style_td valign='top'>$data_ans[promo_des]</td>";
			echo "<td $style_td valign='top'>$promo_amt_show</td>";
			echo "<td $style_td valign='top'><a href='#' onclick=\"download_pro('$data_ans[promo_code]');\">Update</a></td>";
			echo "<td $style_td valign='top'><a href='step_pro2.php?promo_code=$data_ans[promo_code]' >วิธีเล่น</a></td>";
			

			echo "</tr>";

}
echo "</tbody>";
echo "</table>";


?>
  
</div>
