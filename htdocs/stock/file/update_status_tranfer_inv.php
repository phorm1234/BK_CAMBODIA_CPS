<?php 
//connect Ji net
$ip_ji="10.100.53.2";
$user_ji="master";
$password_ji="master";
$db="pos_ssup";
$local_ji=@mysql_connect($ip_ji,$user_ji,$password_ji);
if($local_j){
	echo "y";
}else{
	echo "n";
}
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
echo $sql_system;
$rs_system=mysql_db_query("pos_ssup",$sql_system,$local);
$branch_id=mysql_fetch_array($rs_system);
$shop=$branch_id[0];
$ip=exec("ip addr list eth0 |grep 'inet ' |cut -d' ' -f6|cut -d/ -f1");

if(!empty($shop)){
	$sql_head="update `trn_in2shop_head` as a inner join trn_diary1 as b on a.doc_no=b.refer_doc_no set a.status_transfer='T'";
	$rs_head=mysql_db_query($db,$sql_head,$local);
	if($rs_head){
		$sql_list="update `trn_in2shop_list` as a inner join trn_diary1 as b on a.doc_no=b.refer_doc_no set a.status_transfer='T'";
		$rs_list=mysql_db_query($db,$sql_list,$local);
		if($rs_list){
			echo "Y";
		}else{
			echo "N";
		}
	}
}
?>
