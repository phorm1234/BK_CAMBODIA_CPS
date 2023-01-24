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

if(!empty($shop)){
	mysql_db_query($db,"TRUNCATE TABLE trn_fix_stock",$local);
	$sql_get_data="select * from trn_fix_stock where branch_id='$shop' ";
	echo $sql_get_data."<br>";
	$rs_get_data=mysql_db_query($db,$sql_get_data,$local_ji);
	$j=1;
	while($row=mysql_fetch_array($rs_get_data)){
		$sql="
		insert into  
			trn_fix_stock  
		set 
			corporation_id='$row[corporation_id]', 
			company_id='$row[company_id]', 
			branch_id='$row[branch_id]', 
			product_id='$row[product_id]',
			quantity_normal='$row[quantity_normal]', 
			quantity_promo1='$row[quantity_promo1]', 
			start_date1='$row[start_date1]', 
			end_date1='$row[end_date1]', 
			quantity_promo2='$row[quantity_promo2]', 
			start_date2='$row[start_date2]', 
			end_date2='$row[end_date2]', 
			quantity_promo3='$row[quantity_promo3]', 
			start_date3='$row[start_date3]', 
			end_date3='$row[end_date3]', 
			reg_date=NOW(), 
			reg_time=NOW(), 
			reg_user='system' 
			";
		echo $sql."<br>";
		$rs=mysql_db_query($db,$sql,$local);
		if($rs){
			echo "Y<br>";	
		}else{
			echo "N<br>";
		}
		$j++;
	}
}
?>
