<?php
set_time_limit(0);
$doc_date=$_GET['doc_date'];

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
mysql_close($conn_shop);









$conf="
SELECT a.*
FROM trn_diary1 as a inner join trn_diary2 as b
on a.doc_no=b.doc_no
WHERE 
a.doc_date >= '2014-06-16' and
b.doc_date >= '2014-06-16'
AND b.promo_code
IN (
'OX09150214', 'OX08140214'
)
group by doc_no
order by concat(a.doc_date,' ',a.doc_time) desc limit 5


";	
//echo $conf;	
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);
echo "rows=".$rows;

mysql_close($conn_shop);



for($i=1; $i<=$rows; $i++){

			$databill=mysql_fetch_array($result);
			$doc_no=$databill['doc_no'];
			$member_id=$databill['member_id'];
			$doc_date=$databill['doc_date'];
			$doc_time=$databill['doc_time'];
			//echo "$doc_no/$member_id/$doc_date/<br>";
			
			
			$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");
			
			$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
			on a.customer_id=b.customer_id
			where
			b.member_no='$member_id' ";	
			$run_profile=mysql_db_query($ji_db_sevice,$profile,$conn_ji);
			$rows_profile=mysql_num_rows($run_profile);
			
			
				
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($rows_profile>0){//online
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];
			}else{
				$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");	
				mysql_select_db($dbname);
			
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_shop);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];	
			}
		

			$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");	
			mysql_select_db($dbname);		
						
			$list="select * from  trn_diary2
			where doc_no='$doc_no' and doc_tp in('SL','VT')
			and product_id in('25658','25659','25660') and promo_code in('OX08140214','OX09150214')
			";
		
			
			$run_list=mysql_query($list,$conn_shop);
			$rows_list=mysql_num_rows($run_list);		
			//echo "rows_list=".$rows_list;		
			for($x=1; $x<=$rows_list; $x++){
				$datalist=mysql_fetch_array($run_list);
				$promo_code=$datalist['promo_code'];
				$product_id=$datalist['product_id'];
				$seq=$datalist['seq'];
				$lot_no=$datalist['lot_no'];
				$quantity=$datalist['quantity'];
				$amount=$datalist['amount'];
				$net_amt=$datalist['net_amt'];
				
				
				$add="insert into promotion_input_history
				set
					branch_id='$datalist[branch_id]',
					customer_id='$customer_id',
					id_card='$id_card',
					member_no='$member_id',
					doc_no='$doc_no',
					doc_date='$doc_date',
					doc_time='$doc_time',
					promo_code='$promo_code',
					product_id='$product_id',
					seq='$seq',
					product_code='$lot_no',
					quantity='$quantity',
					amount='$amount',
					net='$net_amt',
					reg_user='$datalist[reg_user]',
					reg_date='$datalist[reg_date]',
					reg_time='$datalist[reg_time]',
					upd_user='$datalist[upd_user]',
					upd_date='$datalist[upd_date]',
					upd_time='$datalist[upd_time]'
				";
				//echo "$add<br>";
				mysql_query($add,$conn_shop);
			}
			

			
			

	

}
mysql_close($conn_shop);

//send to server

$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");	
mysql_select_db($dbname);			

$find="select * from promotion_input_history where send_toserver_status='' and doc_date between doc_date - interval 5 day and date(now())";
//echo "$find<br>";
$run=mysql_query($find,$conn_local);
$rows_find=mysql_num_rows($run);	
echo "<br>Rows send to server : $rows_find<br>";		

	

if($rows_find>0){
		for($i=1; $i<=$rows_find; $i++){
			$datalist=mysql_fetch_array($run);				
			
			$conn_online=mysql_connect($ji_ip,$ji_user,$ji_pass);	
			mysql_select_db($ji_db_sevice);				
			$add="insert into promotion_input_history
					set
						branch_id='$datalist[branch_id]',
						customer_id='$datalist[customer_id]',
						id_card='$datalist[id_card]',
						member_no='$datalist[member_no]',
						doc_no='$datalist[doc_no]',
						doc_date='$datalist[doc_date]',
						doc_time='$datalist[doc_time]',
						promo_code='$datalist[promo_code]',
						product_id='$datalist[product_id]',
						seq='$datalist[seq]',
						product_code='$datalist[product_code]',
						quantity='$datalist[quantity]',
						amount='$datalist[amount]',
						net='$datalist[net_amt]',
						reg_user='$datalist[reg_user]',
						reg_date='$datalist[reg_date]',
						reg_time='$datalist[reg_time]',
						upd_user='$datalist[upd_user]',
						upd_date='$datalist[upd_date]',
						upd_time='$datalist[upd_time]'
					";
			//echo "$add<br>";
			$run_add=mysql_query($add,$conn_online);
			mysql_close($conn_online);
			if($run_add){
				$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
				mysql_select_db($dbname);				
				$up_send="update promotion_input_history set
					send_toserver_status='Y',
					send_toserver_date=date(now()),
					send_toserver_time=time(now())
					where
						id='$datalist[id]'
				";
				mysql_query($up_send,$conn_local);
			}
							
		}
			
}




//return pro=============================================================================================================
$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
mysql_select_db($dbname);		
$bill="
SELECT a.member_id, a.doc_date, a.doc_no
FROM trn_diary1 AS a
INNER JOIN trn_diary2 AS b ON a.doc_no = b.doc_no
WHERE b.promo_code = 'OX17160514'
GROUP BY a.member_id, a.doc_date, a.doc_no
";
$run_bill=mysql_query($bill,$conn_local);
$rows_bill=mysql_num_rows($run_bill);
echo "<br>ROWS return=$rows_bill";
if($rows_bill>0){
	for($x=1; $x<=$rows_bill; $x++){
		$databill=mysql_fetch_array($run_bill);
		$member_id=$databill['member_id'];
		$doc_no=$databill['doc_no'];
		$doc_date=$databill['doc_date'];
		$doc_time=$databill['doc_time'];
		
	
		$conn_online=mysql_connect($ji_ip,$ji_user,$ji_pass);	
		mysql_select_db(ji_db_sevice);	
		$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
		on a.customer_id=b.customer_id
		where
		b.member_no='$member_id' ";	
		$run_profile=mysql_query($profile,$conn_online);
		$dataprofile=mysql_fetch_array($run_profile);
		$customer_id=$dataprofile['customer_id'];
		$id_card=$dataprofile['id_card'];
		$rows_profile_online=mysql_num_rows($run_profile);
		mysql_close($conn_online);
		if($rows_profile_online==0){
			$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
			mysql_select_db($dbname);		
			$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
			on a.customer_id=b.customer_id
			where
			b.member_no='$member_id' ";	
			$run_profile=mysql_query($profile,$conn_local);
			$dataprofile=mysql_fetch_array($run_profile);
			$customer_id=$dataprofile['customer_id'];
			$id_card=$dataprofile['id_card'];	
		}
	
		$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
		mysql_select_db($dbname);			
					
		$list="select * from  trn_diary2
		where doc_no='$doc_no' and doc_tp in('SL','VT')
		and promo_code='OX17160514'
		";
		echo $list . "<br>";
		$run_list=mysql_query($list,$conn_local);
		$rows_list=mysql_num_rows($run_list);				
		for($i=1; $i<=$rows_list; $i++){
			$datalist=mysql_fetch_array($run_list);
			$promo_code=$datalist['promo_code'];
			$product_id=$datalist['product_id'];
			$seq=$datalist['seq'];
			$lot_no=$datalist['lot_no'];
			$quantity=$datalist['quantity'];
			$amount=$datalist['amount'];
			$net_amt=$datalist['net_amt'];
			
			
			$add="insert into promotion_return_history
			set
				branch_id='$datalist[branch_id]',
				customer_id='$customer_id',
				id_card='$id_card',
				member_no='$member_id',
				doc_no='$doc_no',
				doc_date='$doc_date',
				doc_time='$doc_time',
				promo_code='$promo_code',
				product_id='$product_id',
				seq='$seq',
				product_code='$lot_no',
				quantity='$quantity',
				amount='$amount',
				net='$net_amt',
				reg_user='$datalist[reg_user]',
				reg_date='$datalist[reg_date]',
				reg_time='$datalist[reg_time]',
				upd_user='$datalist[upd_user]',
				upd_date='$datalist[upd_date]',
				upd_time='$datalist[upd_time]'
			";
			echo "$add<br>";
			mysql_query($add,$conn_local);
		}
	
	}

}



//send to server

$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");	
mysql_select_db($dbname);			

$find="select * from promotion_return_history where send_toserver_status=''  and doc_date between doc_date - interval 5 day and date(now())";
//echo "$find<br>";
$run=mysql_query($find,$conn_local);
$rows_find=mysql_num_rows($run);	
echo "<br>Rows send to server : $rows_find<br>";		

	

if($rows_find>0){
		for($v=1; $v<=$rows_find; $v++){
			$datalist=mysql_fetch_array($run);				
			
			$conn_online=mysql_connect($ji_ip,$ji_user,$ji_pass);	
			mysql_select_db($ji_db_sevice);				
			$add="insert into promotion_return_history
					set
						branch_id='$datalist[branch_id]',
						customer_id='$datalist[customer_id]',
						id_card='$datalist[id_card]',
						member_no='$datalist[member_no]',
						doc_no='$datalist[doc_no]',
						doc_date='$datalist[doc_date]',
						doc_time='$datalist[doc_time]',
						promo_code='$datalist[promo_code]',
						product_id='$datalist[product_id]',
						seq='$datalist[seq]',
						product_code='$datalist[product_code]',
						quantity='$datalist[quantity]',
						amount='$datalist[amount]',
						net='$datalist[net_amt]',
						reg_user='$datalist[reg_user]',
						reg_date='$datalist[reg_date]',
						reg_time='$datalist[reg_time]',
						upd_user='$datalist[upd_user]',
						upd_date='$datalist[upd_date]',
						upd_time='$datalist[upd_time]'
					";
			echo "$add<br>";
			$run_add=mysql_query($add,$conn_online);
			mysql_close($conn_online);
			if($run_add){
				$conn_local=mysql_connect($ipserver,$mysql_user,$mysql_pass);
				mysql_select_db($dbname);				
				$up_send="update promotion_return_history set
					send_toserver_status='Y',
					send_toserver_date=date(now()),
					send_toserver_time=time(now())
					where
						id='$datalist[id]'
				";
				mysql_query($up_send,$conn_local);
			}
							
		}
			
}



			
?>