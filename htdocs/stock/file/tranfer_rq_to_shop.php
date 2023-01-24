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
	$sql_rq="select * from trn_diary1_rq where status_transfer='' ";
	echo $sql_rq."<br>";
	$rs_get_data=mysql_db_query($db,$sql_rq,$local);
	$j=1;
	while($row=mysql_fetch_array($rs_get_data)){
		$doc_no=$row['doc_no'];
		$sql_chk="SELECT doc_no,flag FROM trn_shoprq_head where doc_no='$doc_no' and status_transfer='' and shop='$shop' ";
		$rs_chk=mysql_db_query($db,$sql_chk,$local_ji);
		$chk=mysql_fetch_array($rs_chk);
		$num=mysql_num_rows($rs_chk);
		if($num>0){
			if($chk['flag']=="C"){
				$flag="C";
				$flag2="C";
			}else{
				$flag="Y";
				$flag2="";
			}
			$sql_update="update trn_diary1_rq set flag='$flag2',status_transfer='$flag' where doc_no='$doc_no'  ";
			echo $sql_update."<br>";
			$rs=mysql_db_query($db,$sql_update,$local);
			if($rs){
				$sql_up_ji="update trn_shoprq_head set status_transfer='Y' where doc_no='$doc_no' and shop='$shop'  ";
				$rs_ji=mysql_db_query($db,$sql_up_ji,$local_ji);
				if($rs_ji){
					echo "Y<br>";	
				}else{
					echo "N<br>";
				}
			}else{
				echo "N<br>";
			}
			//echo $sql."<br>";
		}else{
			echo "T";
		}
		$j++;
	}
	
	$sql_status="SELECT doc_no,flag FROM trn_shoprq_head where shop='$shop' and flag='C' order by doc_dt DESC limit 30 ";
	$rs_status=mysql_db_query($db,$sql_status,$local_ji);
	$num_status=mysql_num_rows($rs_status);
	if($num_status>0){
		while($dat_status=mysql_fetch_array($rs_status)){
			$sql_updata_status="update trn_diary1_rq set flag='C',status_transfer='C' where doc_no='$dat_status[doc_no]' ";
			$rs_status=mysql_db_query($db,$sql_updata_status,$local);
			if($rs_status){
				$sql_updata_status_list="update trn_diary2_rq set flag='C' where doc_no='$dat_status[doc_no]' ";
				$rs_status_list=mysql_db_query($db,$sql_updata_status_list,$local);
			}
		}
	}

}else{
	echo "ไม่พบรหัส Shop";
}
?>
