<?php
set_time_limit(0);
include("connect.php");



$id_card=$_GET['id_card'];
$status_readcard=$_GET['status_readcard'];
$member_no=$_GET['member_no'];
if($id_card==""){
	echo "คุณไม่ได้ระบุรหัสบัตรประชาชน";
	return false;
}

$conn_service=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);

/*
if($member_no<>""){
	$show="
		SELECT b . *
		FROM member_register AS a
		INNER JOIN member_history AS b ON a.customer_id = b.customer_id
		inner join member_promotion as c
		on a.application_id=c.promo_code
		WHERE a.id_card = '$id_card' and b.member_no='$member_no'
		AND b.expire_date >= date( now( ) )
		AND b.status_active = 'Y'
		AND c.type_pro='ID'
		GROUP BY b.member_no
		ORDER BY b.ops	
	";	
	//echo $show;
}else{

	$show="
		SELECT b . *
		FROM member_register AS a
		INNER JOIN member_history AS b ON a.customer_id = b.customer_id
		inner join member_promotion as c
		on a.application_id=c.promo_code
		WHERE a.id_card = '$id_card'
		AND b.expire_date >= date( now( ) )
		AND b.status_active = 'Y'
		AND c.type_pro='ID'
		GROUP BY b.member_no
		ORDER BY b.ops	
	";
}
*/
	$show="
		SELECT b . *
		FROM member_register AS a
		INNER JOIN member_history AS b ON a.customer_id = b.customer_id
		inner join member_promotion as c
		on a.application_id=c.promo_code
		WHERE a.id_card = '$id_card'
		AND b.expire_date >= date( now( ) )
		AND b.status_active = 'Y'
		AND c.type_pro='ID'
		GROUP BY b.member_no
		ORDER BY b.ops	
	";
//echo $show;
$result_show=mysql_query($show,$conn_service);
$rows_show=mysql_num_rows($result_show);
if($rows_show==0){
	echo "member_null###";
} else if($rows_show==1){
	$data=mysql_fetch_array($result_show);

	echo "one###$id_card###$status_readcard###$data[member_no]";
}else{
	echo "more###";
	echo "<input name='function_next' type='hidden' id='function_next' value='$_GET[function_next]'/>";
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
		if($i%2>0){
			$css="d0e3ea";
		}else{
			$css="e9f1f5";
		}

		echo "<tr >";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$i</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$data[ops]</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$data[apply_date]</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'>$data[expire_date]</td>";
		echo "<td style='color:#376092' bgcolor='#$css' align='center'><input type='button' value='ใช้สิทธิ์' onclick=\"senddata('$data[member_no]','$id_card','$status_readcard');\"></td>";
		echo "</tr>";



	}
	echo "<table>";
}

mysql_close($conn_service);

?>