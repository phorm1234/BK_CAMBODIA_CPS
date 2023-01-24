<?php
set_time_limit(0);
include("connect.php");
$ip=$_SERVER['REMOTE_ADDR'];
$member_no=$_GET['member_no'];

	//chk friend
	$conn_service=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");	
	mysql_select_db($db_service);	
	$local_find="select a.* from member_register as a inner join member_history as b
	on a.customer_id=b.customer_id
	 where b.member_no='$member_no'  limit 1";

	$run_local_find=mysql_query($local_find,$conn_service);
	$rows_local_find=mysql_num_rows($run_local_find);
	if($rows_local_find>0){
		$frienddata=mysql_fetch_array($run_local_find);
		$id_card=$frienddata['id_card'];
		$mobile_no=$frienddata['mobile_no'];
		
		$find="select * from trn_diary1 where doc_date>='2017-04-01' and application_id like 'OPMGM%' and flag<>'C' and coupon_code like '$id_card%' order by STR_TO_DATE( concat( doc_date, ' ', doc_time ) , '%Y-%m-%d %H:%i:%s' ) limit 5";
		//secho $find;
		$run=mysql_query($find,$conn_service);
		$rows=mysql_num_rows($run);
		echo "<center><br><span style='color:#376092' >รายการเพื่อนทั้งหมดของ <span style='color:#9bbb59' >คุณ$frienddata[name] $frienddata[surname]</span> ที่แนะนำให้มาสมัครสมาชิก</span></center>";
		echo "<table border='0' width='90%' align='center'>";
		echo "<tr style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>";
		echo "<td style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>ลำดับ</td>";
		echo "<td style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>รหัสบัตรประชาชน</td>";
		echo "<td style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>ชื่อ-นามสกุล</td>";
		echo "<td style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>เบอร์มือถือ</td>";
		echo "<td style='color:#FFFFFF; font-size:26px;' bgcolor='#4bacc6' align='center'>เลือกใช้สิทธื์</td>";
		echo "/<tr>";
		if($id_card!=""){
			for($i=1; $i<=$rows; $i++){
				$data=mysql_fetch_array($run);
				$coupon_code=$data['coupon_code'];
				$list_id_card=$data['idcard']; //id card คนสมัคร
				$list_mobile=$data['mobile_no'];//mobile คนสมัคร
				$list_member_no=$data['member_id'];//mobile คนสมัคร
				if($list_id_card!="" && $list_mobile!=""){
					$findprofile="select a.* from member_register as a inner join member_history as b on a.customer_id=b.customer_id where a.id_card='$list_id_card' and a.mobile_no='$list_mobile' and b.member_no='$list_member_no' ";
					//echo $findprofile;
					$runfindprofile=mysql_query($findprofile,$conn_service);
					$profile=mysql_fetch_array($runfindprofile);
					$list_name=$profile['name'];
					$list_surname=$profile['surname'];
				}else{
					$list_name="";
					$list_surname="";
				}

				
				
				$chkplay="select * from trn_diary1 where doc_date>='2017-04-01' and co_promo_code='OX02031215' and flag<>'C' and idcard='$id_card' and coupon_code like '$list_id_card%' ";
				$runchkplay=mysql_query($chkplay,$conn_service);
				$rowschkplay=mysql_num_rows($runchkplay);
				if($rowschkplay>0){
					$datachkplay=mysql_fetch_array($runchkplay);
					$doc_no=$datachkplay['doc_no'];
					$doc_date=$datachkplay['doc_date'];
					$doc_time=$datachkplay['doc_time'];
					$status="ใช้แล้วเมื่อวันที่ $doc_date $doc_time เลขที่บิล $doc_no" ;
				}else{
					//chk local
					$conn_local=mysql_connect($server_local,$user_local,$pass_local);
					mysql_query("SET character_set_results=utf8");
					mysql_query("SET character_set_client=utf8");
					mysql_query("SET character_set_connection=utf8");
					mysql_select_db($db_local);
					
					$chkplay_local="select * from trn_diary1 where doc_date>='2017-04-01' and co_promo_code='OX02031215' and flag<>'C' and idcard='$id_card' and coupon_code like '$list_id_card%' ";
					$runchkplay_local=mysql_query($chkplay_local,$conn_local);
					$rowschkplay_local=mysql_num_rows($runchkplay_local);
					if($rowschkplay_local>0){
						$datachkplay_local=mysql_fetch_array($runchkplay_local);
						$doc_no=$datachkplay_local['doc_no'];
						$doc_date=$datachkplay_local['doc_date'];
						$doc_time=$datachkplay_local['doc_time'];
						$status="ใช้แล้วเมื่อวันที่ $doc_date $doc_time เลขที่บิล $doc_no" ;
					}else{
						$status="<input type='button' name='Button' value='ใช้สิทธิ์' onclick=\"m2m_from('OX02031215','','$member_no','$list_id_card','$list_mobile','$id_card','$mobile_no')\"/>";
					}	
					
				}
				if($i%2>0){
					$css="d0e3ea";
				}else{
					$css="e9f1f5";
				}
				echo "<tr >";
				echo "<td style='color:#376092' bgcolor='#$css' align='center'>คนที่ $i</td>";
				echo "<td style='color:#376092' bgcolor='#$css' align='center'>$list_id_card</td>";
				echo "<td style='color:#376092' bgcolor='#$css' align='center'>คุณ$list_name $list_surname</td>";
				echo "<td style='color:#376092' bgcolor='#$css' align='center'>$list_mobile</td>";
				echo "<td style='color:#376092' bgcolor='#$css' align='center'>$status</td>";
				echo "/<tr>";
			}

		}
		echo "</table>";
	}else{
		echo "No@@$frienddata[name]@@$frienddata[surname]@@$frienddata[id_card]@@$frienddata[customer_id]";
		
	}	
	

?>