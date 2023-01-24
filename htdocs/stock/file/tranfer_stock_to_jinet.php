<?php 
//connect Ji net
$ip_ji="10.100.53.2";
$user_ji="master";
$password_ji="master";
$db="pos_ssup";
$local_ji=@mysql_connect($ip_ji,$user_ji,$password_ji);
@mysql_query("SET character_set_results=tis620");
@mysql_query("SET character_set_client=tis620");
@mysql_query("SET character_set_connection=tis620");
//end

//connect localhost 
$ip="localhost";
$user="pos-ssup";
$password='P0z-$$up';
$local=@mysql_connect($ip,$user,$password);
@mysql_query("SET character_set_results=tis620");
@mysql_query("SET character_set_client=tis620");
@mysql_query("SET character_set_connection=tis620");
//end

//get shop
$sql_system="select branch_id from com_branch where active='1' limit 1 ";
$rs_system=mysql_db_query("pos_ssup",$sql_system,$local);
$branch_id=mysql_fetch_array($rs_system);
$shop=$branch_id[0];
echo "Shop : ".$shop."<br>";
//end

$month=date('n');
$year=date('Y');

$sql="select * from com_stock_master where month='$month' and year='$year' and branch_id='$shop' ";
$rs=mysql_db_query($db,$sql,$local);
$num=mysql_num_rows($rs);
if($num>0){
	while($dat=mysql_fetch_array($rs)){
		$sql_check="select * from com_stock_master where product_id='$dat[product_id]' and branch_id='$shop' and month='$month' and year='$year' ";	
		$rs_check=mysql_db_query($db,$sql_check,$local_ji);
		$check=mysql_num_rows($rs_check);
		if($check>0){
			$sql_update="
			update 
				com_stock_master 
			set 
				begin='$dat[begin]', 
				onhand='$dat[onhand]', 
				allocate='$dat[allocate]', 
				tranfer_date=NOW() 
			where 
				shop='$dat[branch_id]' 
				and product_id='$dat[product_id]' 
				and month='$dat[month]' 
				and year='$dat[year]' 
			";
		}else{
			$sql_update="
			insert into 
				com_stock_master 
			set 
				corporation_id='$dat[corporation_id]', 
				company_id='$dat[company_id]', 
				branch_id='$dat[branch_id]', 
				branch_no='$dat[branch_no]', 
				product_id='$dat[product_id]', 
				month='$dat[month]', 
				year='$dat[year]', 
				product_status='$dat[product_status]', 
				begin='$dat[begin]', 
				onhand='$dat[onhand]', 
				allocate='$dat[allocate]', 
				upd_date='$dat[upd_date]', 
				upd_time='$dat[upd_time]', 
				upd_user='$dat[upd_user]', 
				tranfer_date=NOW() 
			";
		}
		echo $sql_update."<br>";
		$rs_update=mysql_db_query($db,$sql_update,$local_ji);
	}
}
?>