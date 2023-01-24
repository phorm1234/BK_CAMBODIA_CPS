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
if($local){
	//echo "y";
}else{
	//echo "n";
}
@mysql_query("SET character_set_results=tis620");
@mysql_query("SET character_set_client=tis620");
@mysql_query("SET character_set_connection=tis620");
//end

$sql_branch="select * from com_branch where active='1' limit 1 ";
$rs_branch=mysql_db_query($db,$sql_branch,$local);
$data_branch=mysql_fetch_array($rs_branch);

$sql_get_data="select * from com_product_master where corporation_id='$data_branch[corporation_id]' and company_id='$data_branch[company_id]' ";
//echo $sql_get_data."<br>";
$rs_get_data=mysql_db_query($db,$sql_get_data,$local_ji);
$j=1;
while($row=mysql_fetch_array($rs_get_data)){
	$product_id=trim($row['product_id']);
	$brand=trim($data_branch['corporation_id']);
	$barcode=trim($row['barcode']);
	$name_product=trim($row['name_product']);
	$name_print=trim($row['name_print']);
	$price=trim($row['price']);
	$cost=trim($row['cost']);
	$tax_type=trim($row['tax_type']);
	
	$sql_chk="SELECT product_id FROM com_product_master where product_id='$product_id' and corporation_id='$brand' and company_id='$brand' ";
	//echo $sql_chk."<br>";
	$rs_chk=mysql_db_query($db,$sql_chk,$local);
	$chk=mysql_fetch_array($rs_chk);
	//print_r($chk);
	//echo $chk['product_id']."<--<br>";
	if(!empty($chk['product_id'])){
		$sql="
		update 
			com_product_master    
		set 
			barcode='$barcode', 
			name_product='$name_product', 
			name_print='$name_print', 
			price='$price',
			cost='$cost', 
			tax_type='$tax_type',
			upd_date=NOW(),
			upd_time=NOW(), 
			upd_user='system' 
		where 
			 product_id='$product_id' 
			 and corporation_id='$brand' 
			 and company_id='$brand'
		";
	}else{
		$sql="
		insert into 
			com_product_master  
		set 
			corporation_id='$brand',
			company_id='$brand', 
			vendor_id='$brand', 
			product_id='$product_id', 
			barcode='$barcode', 
			name_product='$name_product', 
			name_print='$name_print', 
			price='$price',
			cost='$cost',
			tax_type='$tax_type',
			reg_date=NOW(),
			reg_time=NOW(),
			reg_user='system'
		";
	}
	//echo $sql."<br>";
	$rs=mysql_db_query($db,$sql,$local);
// 	if($rs){		
// 		echo "$product_id : OK!<hr/>";	
// 	}else{
// 	    echo "$product_id : FAIL.<hr/>";
// 	}
	$chk['product_id']="";
	$j++;
}
echo "SUCCESS!";
?>