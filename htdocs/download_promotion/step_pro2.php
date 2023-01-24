<script>
  document.write('<center><a href="' + document.referrer + '">Go Back</a></center>');
</script>

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
	text-align:left;border-collapse:collapse;border:1px solid #69c;margin:5px;}
	#newspaper-b th{font-weight:bold;font-size:14px;color:#039;padding:15px 10px 10px;background:#d0dafd;}
	#newspaper-b tbody{background:#e8edff;}
	#newspaper-b td{color:#669;border-top:1px dashed #fff;padding:10px;}
	#newspaper-b tbody tr:hover td{color:#339;background:#d0dafd;}
	-->

	#newspaper-x{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:95%;
	text-align:left;border-collapse:collapse;border:1px solid #69c;margin:5px;}
	#newspaper-x th{font-weight:bold;font-size:14px;color:#039;padding:5px 10px 10px;background:#F5FAAB;}
	#newspaper-x tbody{background:#FCFCFA;}
	#newspaper-x td{color:#669;border-top:1px dashed #fff;padding:5px;}
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


	<!--
	.text1 {
		color: #FF0000;
		font-size: 36px;
	}
	-->

body {
	margin-left: 10px;
}

	</style>

<?php
set_time_limit(0);

$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";
/*$ipserver='10.100.53.2';
$mysql_user='master';
$mysql_pass='master';
$dbname="pos_ssup";*/


function fomatdate($dt) {    //ปรับรูปแบบวันที่จาก 0000-00-00 เป็น 00/00/0000
	$y=substr($dt,0,4);
	$m=substr($dt,5,2);
	$d=substr($dt,8,2);
	$dt_fomat="$d/$m/$y";
	return $dt_fomat;
}

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

$promo_code=$_GET['promo_code'];



$shop_active_head="select * from promo_head where promo_code='$promo_code' ";
$shop_run_active_head=mysql_db_query($dbname,$shop_active_head,$conn_shop);
$shop_rows_active_head=mysql_num_rows($shop_run_active_head);
$data_head=mysql_fetch_array($shop_run_active_head);
echo "<h2>รหัสโปรโมชั่น : $data_head[promo_code]<br>";
echo "รายละเอียด : $data_head[promo_des]<br>";
$start_date=fomatdate($data_head[start_date]);
$end_date=fomatdate($data_head[end_date]);
echo "ระยะเวลาการเล่น : $start_date ถึง $end_date</h2><br>";
echo "<span class='text1'>เงื่อนไข</span><br><br>";

if($data_head[member_tp]=="N"){
	echo "<br>1. สิทธิ์ : เล่นได้ทุกคน&nbsp;&nbsp;";
}else if($data_head[member_tp]=="Y"){
	echo "<br>1. สิทธิ์ : สมาชิกเท่านั้น&nbsp;&nbsp;";
}else if($data_head[member_tp]=="NM"){
	echo "<br>1. สิทธิ์ : สมาชิกใหม่เท่านั้น&nbsp;&nbsp;";
}


if($data_head[promo_tp]=="OPS"){
	echo "ประเภทบัตรที่ร่วมรายการ คือ OPS White Card(รหัสขึ้นต้นด้วย 11) เท่านั้น<br><br>";
}else if($data_head[promo_tp]=="OPT"){
	echo "ประเภทบัตรที่ร่วมรายการ คือ OPS Gold Card(รหัสขึ้นต้นด้วย 15) เท่านั้น<br><br>";
}else if($data_head[promo_tp]=="OPSOPT"){
	echo "ประเภทบัตรที่ร่วมรายการ คือ OPS White Card และ OPS Gold Card<br><br>";
}else{
	echo "<br><br>";
}

if($data_head[promo_pos]=="L"){
				if($data_head['promo_price']=="G"){
					echo "<br>2. ซื้อครบ $data_head[promo_amt] ราคาเต็ม<br>";
				} else if($data_head['promo_price']=="N"){
					echo "<br>2. ซื้อครบ $data_head[promo_amt] สุทธิ<br>";
				} else if($data_head['promo_price']=="U"){
					echo "<br>2. ซื้อครบ $data_head[promo_amt] ชิ้น<br>";
				}
	
}

//if($data_head[type_p]=="T"){
	$step_check="select distinct promo_group from promo_check where promo_code='$promo_code'	";
	
	$run_step_check=mysql_db_query($dbname,$step_check,$conn_shop);
	$rows_step_check=mysql_num_rows($run_step_check);
	$step_cr=3;
	for($c=1; $c<=$rows_step_check; $c++){
		$data_step_check=mysql_fetch_array($run_step_check);
		$promo_group=$data_step_check['promo_group'];

		if($promo_group=="PROMOTION"){
				$find_pro="select * from promo_check where promo_code='$promo_code' and promo_group='PROMOTION' ";
				$run_find_pro=mysql_db_query($dbname,$find_pro,$conn_shop);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				echo "<br>$step_cr . ต้องเล่นร่วมกับโปรชั่นเหล่านี้: <br>";
				echo "<div style='overflow:auto; height:300px; width:800px;'>";
				echo "<table id='newspaper-x'>";
				for($f=1; $f<=$rows_find_pro; $f++){
					$data_pro=mysql_fetch_array($run_find_pro);

					$name_pro="select * from promo_head where promo_code='$data_pro[promo_play]' ";
					$run_name_pro=mysql_db_query($dbname,$name_pro,$conn_shop);
					$data_name_pro=mysql_fetch_array($run_name_pro);
					echo "<tr><td>$data_name_pro[promo_code]</td><td>$data_name_pro[promo_des] </tr>";

				}
				echo "</table>";
				echo "</div>";

		 }else if($promo_group=="NOPRO"){
				$find_pro="select * from promo_check where promo_code='$promo_code' and promo_group='NOPRO' ";
				$run_find_pro=mysql_db_query($dbname,$find_pro,$conn_shop);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				echo "<br>$step_cr . ห้ามเล่นกับโปรชั่นเหล่านี้ : ";
				echo "<div style='overflow:auto; height:300px; width:800px;'>";
				echo "<table id='newspaper-x'>";
			
				for($f=1; $f<=$rows_find_pro; $f++){
					$data_pro=mysql_fetch_array($run_find_pro);

					$name_pro="select * from promo_head where promo_code='$data_pro[promo_play]' ";
					$run_name_pro=mysql_db_query($dbname,$name_pro,$conn_shop);
					$data_name_pro=mysql_fetch_array($run_name_pro);
					echo "<tr><td>$data_name_pro[promo_code]</td><td>$data_name_pro[promo_des] </tr>";
				}
				echo "</table>";
				echo "</div>";
			 }else if($promo_group=="OTHER"){
					
					//echo "<br>$step_cr . สินค้าอื่นๆ";
			
			}else{
				$find_pro="select * from promo_check where promo_code='$promo_code' and promo_group='$promo_group' ";
				$run_find_pro=mysql_db_query($dbname,$find_pro,$conn_shop);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				$data_pro=mysql_fetch_array($run_find_pro);
				if($data_pro['unit_tp']=="G"){
					echo "<br>$step_cr . ซื้อสินค้าในกลุ่มนี้ รวมอย่างน้อย: $data_pro[unit] ราคาเต็ม";
				} else if($data_pro['unit_tp']=="N"){
					echo "<br>$step_cr . ซื้อสินค้าในกลุ่มนี้ รวมอย่างน้อย: $data_pro[unit] สุทธิ";
				} else if($data_pro['unit_tp']=="U"){
					echo "<br>$step_cr . ซื้อสินค้าในกลุ่มนี้ รวมอย่างน้อย: $data_pro[unit] ชิ้น";
				}
		


				echo "<div style='overflow:auto; height:300px; width:600px;'>";
				echo "<table id='newspaper-x'>";
				echo "<tr><th align='center'>รหัสสินค้า</th><th>ชื่อสินค้า</th></tr>";
				for($f=1; $f<=$rows_find_pro; $f++){
					$data_pro=mysql_fetch_array($run_find_pro);
					$name_pro="select * from com_product_master where product_id='$data_pro[product_id]' ";
					$run_name_pro=mysql_db_query($dbname,$name_pro,$conn_shop);
					$data_name_pro=mysql_fetch_array($run_name_pro);
					
					echo "<tr><td align='center'>$data_name_pro[product_id]</td><td>$data_name_pro[name_product]</td></tr>";

				}
				echo "</table>";
				echo "</div>";
			}


			if($promo_group!="OTHER"){
				$step_cr++;
			}
	}//loop c


//}


echo "<br><br><span class='text1'>วิธีเล่น</span><br><br>";
$step="select  distinct seq_pro,type_discount from promo_detail where promo_code='$promo_code'  order by seq_pro";
$run_step=mysql_db_query($dbname,$step,$conn_shop);
$rows_step=mysql_num_rows($run_step);
for($s=1; $s<=$rows_step; $s++){
				$data_step=mysql_fetch_array($run_step);
				$type_discount=$data_step['type_discount'];
				if($type_discount=="Free"){
					$target="แถมฟรีสินค้าเหล่านี้";
				}else if($type_discount=="Percent"){
					$target="ลดสินค้าเหล่านี้เป็น %";
				}else if($type_discount=="Price1"){
					$target="ขายสินค้าเหล่านี้ราคาพิเศษ";
				}else if($type_discount=="Discount"){
					$target="ลดสินค้าเหล่านี้เป็นบาท";
				}else if($type_discount=="Normal"){
					$target="ซื้อสินค้าเหล่านี้";
				}

			echo "ขั้นตอนที่ $s  .  $target<br>";

			$detail="
				select 
					a.seq_pro,a.product_id,b.name_product,b.barcode,b.price,a.type_discount,a.discount 
				from 
					promo_detail as a inner join com_product_master as b
					on a.product_id=b.product_id
				where
					a.promo_code='$promo_code' and a.seq_pro='$s'
			";
			$run_detail=mysql_db_query($dbname,$detail,$conn_shop);
			$rows_detail=mysql_num_rows($run_detail);
			if($rows_detail==0){
					$detail="
						select 
							a.seq_pro,a.product_id,a.type_discount,a.discount 
						from 
							promo_detail as a 
						where
							a.promo_code='$promo_code' and a.seq_pro='$s'
					";
					$run_detail=mysql_db_query($dbname,$detail,$conn_shop);
					$rows_detail=mysql_num_rows($run_detail);
			}

			echo "<table id='newspaper-b'>";
				echo "<tr >";
				echo "<th  align='center' $style_td valign='top'>รหัสสินค้า</th>";
				echo "<th  align='left' $style_td valign='top'>ชื่อสินค้า</th>";
				echo "<th  align='right' $style_td valign='top'>ราคาสินค้า</th>";
				echo "<th  align='right' $style_td valign='top'>ขายในราคา</th>";
				echo "</tr>";
			for($d=1; $d<=$rows_detail; $d++){
				$data_detail=mysql_fetch_array($run_detail);
				$type_discount=$data_detail['type_discount'];
				$discount=$data_detail['discount'];
				$product_id=$data_detail['product_id'];
				if($product_id=="ALL"){
						$product_id="สินค้าทุกตัว";
				}

				if($type_discount=="Normal"){
					$txt="ขายในราคาปกติ";
				} else if($type_discount=="Free"){
					$txt="แถมฟรี";
				} else if($type_discount=="Percent"){
					$txt="ลด $discount %";
				} else if($type_discount=="Discount"){
					$txt="ลด $discount บาท";
				} else if($type_discount=="Price1"){
					$txt="ขายในราคา $discount บาท";
				}


				echo "<tr >";
				echo "<td  align='center' $style_td valign='top'>$product_id</td>";
				echo "<td  align='left' $style_td valign='top'>$data_detail[name_product]</td>";
				echo "<td  align='right' $style_td valign='top'>$data_detail[price]</td>";
				echo "<td  align='right' $style_td valign='top'>$txt</td>";
				echo "</tr>";

			}
			echo "</table>";

}



?>