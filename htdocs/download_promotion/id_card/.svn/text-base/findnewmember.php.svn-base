<?php
set_time_limit(0);
include("connect.php");
$ip=$_SERVER['REMOTE_ADDR'];
$id_card=$_GET['id_card'];
if($id_card==""){
	echo "No";
}else{
	$chk_nation=$_GET['chk_nation'];
	$fname=$_GET['fname'];
	$lname=$_GET['lname'];
	$hbd=$_GET['hbd'];
	$hbd_day=$_GET['hbd_day'];
	$hbd_month=$_GET['hbd_month'];
	$hbd_year=$_GET['hbd_year'];
	$hbd_search="$hbd_year-$hbd_month-$hbd_day";	
	$num_snap=$_GET['num_snap'];	
	
	if($num_snap!=1){
		$path="/var/www/pos/htdocs/download_promotion/id_card/image_member/";
		$filename_old=$path.$id_card . "_snap" . $num_snap . ".jpg";
		$filename_new=$path.$id_card . "_snap1.jpg";
		
		unlink($filename_new);
		
		rename($filename_old,$filename_new);
		
		//del
		for($x=2; $x<$num_snap; $x++){
			$filename_del=$path.$id_card . "_snap" . $x . ".jpg";
			unlink($filename_del) or die("Del error");
		}
	
	}

		
	$id_card=trim($id_card);
	$fname=trim($fname);
	$lname=trim($lname);
	
	
	$local_server="localhost";
	$local_user="pos-ssup";
	$local_pass="P0z-\$\$up";
	$local_db="pos_ssup";
	$conn_local=mysql_connect($local_server,$local_user,$local_pass);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");	
	mysql_select_db($local_db);
	
	$clear_tmp="delete from member_idcard_tmp where ip='$ip' ";
	$run_clear_tmp=mysql_query($clear_tmp,$conn_local);
	
	if($run_clear_tmp){
		$add_tmp="insert into member_idcard_tmp(ip,id_card,name,surname,hbd) values('$ip','$id_card','$fname','$lname','$hbd_search')";
		$run_add_tmp=mysql_query($add_tmp,$conn_local);
	}
	$chk_online="select *  from com_branch_computer limit 0,1";
	$run_chk_online=mysql_query($chk_online,$conn_local);
	$data_chk_online=mysql_fetch_array($run_chk_online);
	$online_status=$data_chk_online['online_status'];
	mysql_close($conn_local);
	if($online_status=="0"){
			$conn_local=mysql_connect($local_server,$local_user,$local_pass);
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");	
			mysql_select_db($local_db);
			$local_find="select * from member_register as a inner join member_history as b
			on a.customer_id=b.customer_id
			where
			a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y'
			";
			$run_local_find=mysql_query($local_find,$conn_local);
			$rows_local_find=mysql_num_rows($run_local_find);
			if($rows_local_find>0){
				echo "Have";
			}else{
				$local_find_name="select * from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				a.name='$fname' and a.surname='$lname' and a.birthday='$hbd_search'  and b.expire_date>=date(now()) and b.status_active='Y'
				";
				$run_local_find_name=mysql_query($local_find_name,$conn_local);
				$rows_local_find_name=mysql_num_rows($run_local_find_name);
				if($rows_local_find_name>0){
					echo "Have";
				}else{
					echo "No";
				}
			}	
	
	}else{
		if($chk_nation=="nothai"){
			echo "No";
		}else{
		
			$conn=mysql_connect($server_service,$user_service,$pass_service);
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");	
			mysql_select_db($db_service);
			
			$find="select * from member_register as a inner join member_history as b
			on a.customer_id=b.customer_id
			where
			a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y'
			";
		
			$run_find=mysql_query($find,$conn);
			$rows_find=mysql_num_rows($run_find);
			if($rows_find>0){
				echo "Have";
			}else{
			
				$find_name="select * from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				a.name='$fname' and a.surname='$lname' and a.birthday='$hbd_search'  and b.expire_date>=date(now()) and b.status_active='Y'
				";
				$run_find_name=mysql_query($find_name,$conn);
				$rows_find_name=mysql_num_rows($run_find_name);
				if($rows_find_name>0){
					echo "Have";
				}else{
						$conn_local=mysql_connect($local_server,$local_user,$local_pass);
						mysql_query("SET character_set_results=utf8");
						mysql_query("SET character_set_client=utf8");
						mysql_query("SET character_set_connection=utf8");	
						mysql_select_db($local_db);
						$local_find="select * from member_register as a inner join member_history as b
						on a.customer_id=b.customer_id
						where
						a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y'
						";
						$run_local_find=mysql_query($local_find,$conn_local);
						$rows_local_find=mysql_num_rows($run_local_find);
						if($rows_local_find>0){
							echo "Have";
						}else{
							$local_find_name="select * from member_register as a inner join member_history as b
							on a.customer_id=b.customer_id
							where
							a.name='$fname' and a.surname='$lname' and a.birthday='$hbd_search'  and b.expire_date>=date(now()) and b.status_active='Y'
							";
							$run_local_find_name=mysql_query($local_find_name,$conn_local);
							$rows_local_find_name=mysql_num_rows($run_local_find_name);
							if($rows_local_find_name>0){
								echo "Have";
							}else{
								echo "No";
							}
						}				
						
				}
			}
		}
	
	
	}



}
?>