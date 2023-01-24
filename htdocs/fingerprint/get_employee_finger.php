<?php 
	$local=mysql_connect("localhost","pos-ssup",'P0z-$$up') or die("localhost");
	mysql_query("SET character_set_results=tis620");
	mysql_query("SET character_set_client = tis620");
	mysql_query("SET character_set_connection = tis620");
		
	$sql="select * from employee_finger where create_date='$_GET[create_date]'";
	$rs=mysql_db_query("ssup",$sql,$local);
	$json=array();
	if(@mysql_num_rows($rs)>0){
		while($arr=mysql_fetch_array($rs)){
			$json[]=$arr;
		}
		//print_r($json);
		echo json_encode($json);
	}else{
		echo "not_found";
	}
	
	
	
?>