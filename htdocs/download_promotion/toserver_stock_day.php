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

$date_now=date("Y-m-d");
$ji_db_sevice="service_pos_op";
$num_day=5;


$h=date("H");
if($h>=10 && $h<=11){


			
			
			//shop
			$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");
			
			$conf="select * from com_doc_date ";
			$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
			$data_conf=mysql_fetch_array($r_conf);
			$doc_date=$data_conf['doc_date'];
			echo "Doc_date:$doc_date<br>";
			
			$day=date("d",strtotime($doc_date));
			$month=date("m",strtotime($doc_date));
			$year=date("Y",strtotime($doc_date));
						
						
			if($doc_date==$date_now){//ถ้าปิดบิลของเมื่อวานเรียบร้อยแล้ว
				
			}
			
			
			
			
			$conn_start=mysql_connect($ipserver,$mysql_user,$mysql_pass)  or die("xx");
			mysql_query("SET NAMES utf8"); 
			mysql_select_db("service_pos_op");
			$sql_detail="SELECT *
			FROM `com_stock_master_history`
			WHERE year = '$year'
			AND MONTH = '$month'";
			//echo $sql_detail;
			$r_detail=mysql_query($sql_detail,$conn_start);
			
			$rows_detail=mysql_num_rows($r_detail);
			//echo "Rows : $rows_detail<br>";
			
			$txt_detail="";
			$txt_start="INSERT INTO com_stock_master_history(`date_bk`, `time_bk`, `corporation_id`, `company_id`, `branch_id`, `branch_no`, `product_id`, `month`, `year`, `product_status`, `begin`, `onhand`, `allocate`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES";
			
			$text_limit=100;
			$loop_run=$text_limit;
			$loop_gen=ceil($rows_detail/$text_limit);
			echo "All Data:$loop_gen<br>";
			for($i_detail=1; $i_detail<=$rows_detail; $i_detail++){
			
				$data_detail=mysql_fetch_array($r_detail);
				
				$gen="(date(now()),time(now()),'$data_detail[corporation_id]','$data_detail[company_id]','$data_detail[branch_id]','$data_detail[branch_no]','$data_detail[product_id]','$data_detail[month]','$data_detail[year]','$data_detail[product_status]','$data_detail[begin]','$data_detail[onhand]','$data_detail[allocate]','$data_detail[reg_date]','$data_detail[reg_time]','$data_detail[reg_user]','$data_detail[upd_date]','$data_detail[upd_time]','$data_detail[upd_user]')
					
				";
				$txt_detail=$txt_detail . "," . $gen;
				if($i_detail==$loop_run){
						
						$txt_detail=$txt_start . substr($txt_detail,1);
						echo "ADD Rows:$txt_detail" ."<br>";
						$conn_target=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no run1");
						mysql_query("SET NAMES utf8"); 
						mysql_select_db($ji_db_sevice);
						$r_add=mysql_query($txt_detail,$conn_target);
						mysql_close($conn_target);
						
						$txt_detail="";
						$loop_run=$i_detail+$text_limit;
				}
				
			
			
			
			
			
			}
			$txt_detail=$txt_start . substr($txt_detail,1);
			
			echo "$txt_detail" ."<br>";
			$conn_target=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no run1");
			mysql_query("SET NAMES utf8"); 
			mysql_select_db($ji_db_sevice);
			$r_add=mysql_query($txt_detail,$conn_target) or die("no run2");
			mysql_close($conn_target);









}

?>