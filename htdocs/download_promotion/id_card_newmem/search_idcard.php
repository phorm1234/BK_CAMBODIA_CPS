<?php
set_time_limit(0);
include("connect.php");
include("function.php");
	
	
function chkops($doc_date,$DD){
	$x=substr($doc_date,0,8);

	$last_day=date("d");
	$num=0;
	for($i=1; $i<=$last_day; $i++){
		$day=$x . $i;
		if(date("D", strtotime($day))==$DD){
			$num++;
		}
	}
	return  $num;
}
$doc_date=date("Y-m-d");
if(date("D", strtotime($doc_date))=="Thu"){
	$numops=chkops($doc_date,"Thu");
	$opsday="OPS$numops";
}
if(date("D", strtotime($doc_date))=="Tue"){
	$numops=chkops($doc_date,"Tue");
	$opsday="OPT$numops";
}




$id_card=$_GET['id_card'];
$status_readcard=$_GET['status_readcard'];
$otpcode=$_GET['otpcode'];

if($id_card==""){
	echo "คุณไม่ได้ระบุรหัสบัตรประชาชน";
	return false;
}


$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);
$chkonline="select *  from com_branch_computer limit 0,1";
$run_chkonline=mysql_query($chkonline,$conn_local);
$datachkonline=mysql_fetch_array($run_chkonline);
$online_status=$datachkonline['online_status'];

if($online_status==1){
	mysql_close($conn_local);
	$conn_service=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);
	$show="select b.*,a.mobile_no from member_register as a inner join member_history as b
	on a.customer_id=b.customer_id
	where
	a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y' and a.application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
	group by b.member_no
	order by b.ops
	";
	//echo $show;
	$result_show=mysql_query($show,$conn_service);
	$rows_show=mysql_num_rows($result_show);
	if($rows_show==0){
		$show="select b.*,a.mobile_no from member_register as a inner join member_history as b
		on a.customer_id=b.customer_id
		where
		a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y' and a.application_id like 'OPN%'
		group by b.member_no
		order by b.ops
		";
		$result_show=mysql_query($show,$conn_service);
		$rows_show=mysql_num_rows($result_show);
	}
}else{
	$conn_local=mysql_connect($server_local,$user_local,$pass_local);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_local);
	$show="select b.*,a.mobile_no from member_register_offline as a inner join member_history_offline as b
	on a.customer_id=b.customer_id
	where
	a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y' and a.application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
	group by b.member_no
	order by b.ops
	";
	//echo $show;
	$result_show=mysql_query($show,$conn_local);
	$rows_show=mysql_num_rows($result_show);
	if($rows_show==0){
		$show="select b.*,a.mobile_no from member_register_offline as a inner join member_history_offline as b
		on a.customer_id=b.customer_id
		where
		a.id_card='$id_card' and b.expire_date>=date(now()) and b.status_active='Y' and a.application_id like 'OPN%'
		group by b.member_no
		order by b.ops
		";
		$result_show=mysql_query($show,$conn_local);
		$rows_show=mysql_num_rows($result_show);
	}	
	mysql_close($conn_local);
}



if($rows_show==0){
	echo "member_null###";
} else if($rows_show==1){
	$data=mysql_fetch_array($result_show);
	$mobile_no=$data['mobile_no'];
	$ops=$data['ops'];
	if($online_status==1){
		up_card($data,$server_local,$user_local,$pass_local,$db_local);
		
		$findprofile="select * from member_register where customer_id='$data[customer_id]'";
		$run_findprofile=mysql_query($findprofile,$conn_service);
		$dataprofile=mysql_fetch_array($run_findprofile);
		mysql_close($conn_service);
		up_profile($dataprofile,$server_local,$user_local,$pass_local,$db_local);
	}
	
	echo "one###$data[member_no]###$mobile_no###$ops";
}else{
	echo "more###";
	//echo "OPS DAY : $opsday<br>";
	echo "<center><br><span style='color:#376092' >กรุณาเลือกสิทธิ์ OPS DAY </span></center>";
	echo "<table  border='0' width='90%' align='center'>";
	echo "<tr>";
	echo "<th style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>ลำดับ</th>";
	echo "<th style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>สิทธิ์วัน OPS Day</th>";
	echo "<th style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>วันเริ่มใช้</th>";
	echo "<th style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>วันหมดอายุ</th>";
	echo "<th style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>ใช้สิทธิ์</th>";
	echo "</tr>";
	

	for($i=1; $i<=$rows_show; $i++){
		$data=mysql_fetch_array($result_show);
		$mobile_no=$data['mobile_no'];
		$ops=$data['ops'];
		if($online_status==1){
			up_card($data,$server_local,$user_local,$pass_local,$db_local);
			if($i==1){
				$findprofile="select * from member_register where customer_id='$data[customer_id]'";
				$run_findprofile=mysql_query($findprofile,$conn_service);
				$dataprofile=mysql_fetch_array($run_findprofile);
				mysql_close($conn_service);
				up_profile($dataprofile,$server_local,$user_local,$pass_local,$db_local);
			}
			
		}


		if($data['ops']==$opsday){
			$css="f4cba8";
		}else{
			if($i%2>0){
				$css="d0e3ea";
			}else{
				$css="e9f1f5";
			}
		}
		echo "<tr >";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$i</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$data[ops]</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$data[apply_date]</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$data[expire_date]</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'><input type='button' value='ใช้สิทธิ์' onclick=\"close_ccschangecardfrom();movemembercard('$data[member_no]','$id_card','$status_readcard','$otpcode','$mobile_no','$ops');\"></td>";
		echo "</tr>";



	}
	echo "<table>";
}



?>