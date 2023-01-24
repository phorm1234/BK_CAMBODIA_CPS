<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
set_time_limit(0);

include("id_card_quick/connect.php");

$doc_no=$_POST['doc_no'];
if($doc_no==""){
	echo "กรุณาใส่เลขที่บิลที่ยกเลิกด้วยครับ";
	return false;
}


//shop
$conn_shop=mysql_connect($server_local,$user_local,$pass_local) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($db_local,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];

//chkbill
$chk="select * from trn_diary1 where doc_no='$doc_no' and flag='C' ";
$run_chk=mysql_db_query($db_local,$chk,$conn_shop) or die("NONO");
$rows_chk=mysql_num_rows($run_chk);
if($rows_chk==0){
	echo "บิลเลขที่ $doc_no ยังไม่ถูกยกเลิก กรุณายกเลิกบิลก่อนครับ";
	return false;
}else{
	$data_chk=mysql_fetch_array($run_chk);
	$doc_date=$data_chk['doc_date'];
	$refer_member_id=$data_chk['refer_member_id'];
	$status_no=$data_chk['status_no'];
	$application_id=$data_chk['application_id'];
	$member_id=$data_chk['member_id'];
	
	
	$conn_service=mysql_connect($server_service,$user_service,$pass_service) or die("no connect mysql");
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	
	  $up_cancle_bill="
		update trn_diary1
		set flag='C'
		where
		doc_date='$doc_date' and doc_no='$doc_no' limit 1
	  ";
	  //echo "<br>$up_cancle_bill<br>";
	  $run_up_cancle_bill=mysql_db_query($db_service,$up_cancle_bill,$conn_service);	
	  if($run_up_cancle_bill){
	  	echo "Up trn_diary1 Complete<br>";
	  }else{
	  	$ans=1;
	  	echo "Up trn_diary1 None Complete<br>";
	  }
	  
	  if($doc_no!=""){
		  $up_cancle_bill2="
			update trn_diary2
			set flag='C'
			where
			doc_date='$doc_date' and doc_no='$doc_no'
		  ";
		  //echo "<br>$up_cancle_bill<br>";
		  $run_up_cancle_bill2=mysql_db_query($db_service,$up_cancle_bill2,$conn_service);	
		  if($run_up_cancle_bill2){
			echo "Up trn_diary2 Complete<br>";
		  }else{
			$ans=1;
			echo "Up trn_diary2 None Complete<br>";
		  }	 
	  } 
	  
	  
	  $up_cancle_promoplay="
		update  promo_play_history set flag='C'	
		where
		doc_date='$doc_date' and doc_no='$doc_no'
	  ";
	   //echo "<br>$up_cancle_promoplay<br>";
	  $run_up_cancle_promoplay=mysql_db_query($db_service,$up_cancle_promoplay,$conn_service);	
	  if($run_up_cancle_promoplay){
	  	echo "Up promo_play_history Complete<br>";
	  }else{
	  	$ans=1;
	  	echo "Up promo_play_history None Complete<br>";
	  }
	  
	  /*
		if($refer_member_id!="" && $doc_no!=""){
			//del member
			 $del_member_no="delete from member_history where doc_no='$doc_no' limit 1 ";
			 echo "<br>$del_member_no<br>";
			 // $run_del_member_no=mysql_db_query($db_service,$del_member_no,$conn_service);	
			  if($run_del_member_no){
				echo "delete from member_history Complete<br>";
			  }else{
			  	$ans=1;
				echo "delete from member_history None Complete<br>";
			  }
			
			  //return active
			  $re_active="update member_history set status_active='Y' , status='0',upd_date='$reg_date',upd_time='$reg_time',upd_user='$reg_user' where member_no='$refer_member_id' limit 1 ";
			  echo "<br>$re_active<br>";
			  //$run_re_active=mysql_db_query($db_service,$re_active,$conn_service);	
			  if($run_re_active){
				echo "re_active Complete<br>";
			  }else{
			  	$ans=1;
				echo "re_active None Complete<br>";
			  }
			
			//cancle promo play
			$up_cancle_seq="delete from member_his_seq 	where 	doc_no='$doc_no' limit 1";
			 echo "<br>$up_cancle_seq<br>";
			  //$run_up_cancle_seq=mysql_db_query($db_service,$up_cancle_seq,$conn_service);	
			  if($run_up_cancle_seq){
				echo "delete from member_his_seq Complete<br>";
			  }else{
			  	$ans=1;
				echo "delete from member_his_seq None Complete<br>";
			  }
			
			//return application_id
			$find_profile="select * from  member_history where member_no='$refer_member_id'  ";
			$run_find_profile=mysql_db_query($db_service,$find_profile,$conn_service);	
			$datat_profile=mysql_fetch_array($run_find_profile);
			$customer_id=$datat_profile['customer_id'];
			
			$find_appold="select * from  member_register_changeid where doc_no='$doc_no'  ";
			$run_find_appold=mysql_db_query($db_service,$find_appold,$conn_service);	
			$dataappold=mysql_fetch_array($run_find_appold);
			$application_id_old=$dataappold['application_id_old'];
			if($customer_id!=""){
				$up_application="update member_register set application_id='$application_id_old' where customer_id='$customer_id' limit 1 ";
				 echo "<br>$up_application<br>";
				  //$run_up_application=mysql_db_query($db_service,$up_application,$conn_service);	
				  if($run_up_application){
					echo "up_applicationOLD Complete<br>";
				  }else{
				  	$ans=1;
					echo "up_applicationOLD None Complete<br>";
				  }					
			}
			
			
								
		}
		*/
		
		
		
		//============================
		 if($application_id=="OPPN300" || $application_id=="OPPS300" || $application_id=="OPPH300"  || $application_id=="OPPL300"  || $application_id=="OPMGMC300"  || $application_id=="OPID300" || $application_id=="OPPLI300" || $application_id=="OPPLC300" || $application_id=="OPMGMI300" || $application_id=="OPPHI300"  || $application_id=="OPLID300"  || $application_id=="OPPGI300"   || $application_id=="OPGNC300" || $application_id=="OPDTAC300"  || $application_id=="OPKTC300"  || $application_id=="OPTRUE300"  || substr($application_id,0,3)=="OPN"){
		 
		 	echo "Case Newmember <br>";
		 
				if($member_id!="" && $doc_no!=""){
				
					  $find_cust="select * from member_history where member_no='$member_id' and doc_no='$doc_no' ";	
					  $run_find_cust=mysql_db_query($db_service,$find_cust,$conn_service);
					  $rows_find_cust=mysql_num_rows($run_find_cust);
					  if($rows_find_cust>0){
							  $datacust=mysql_fetch_array($run_find_cust);
							  $customer_del=$datacust['customer_id'];
							  if($customer_del!=""){
								  $del_profile="
									delete  from member_register
									where
									customer_id='$customer_del' and doc_no='$doc_no' limit 1
								  ";
								  //echo "<br>$del_profile<br>";
								  $run_del_profile=mysql_db_query($db_service,$del_profile,$conn_service);
								  if($run_del_profile){
									echo "delete profile Complete<br>";
								  }else{
									echo "delete profile None Complete<br>";
									$ans=1;
								  }	
							  
							  }
						  
						  
							//del member
							$del_member_no="delete from member_history where member_no='$member_id' and doc_no='$doc_no' limit 1";
								//echo "<br>$del_member_no<br>";
							  $run_del_member_no=mysql_db_query($db_service,$del_member_no,$conn_service);	
							  if($run_del_member_no){
								echo "delete from member_history Complete<br>";
							  }else{
								echo "delete from member_history None Complete<br>";
								$ans=1;
							  }	
					  
					  }
					  
				}
			
		}
				
				
	
}//if ใหญ่



if($ans==1){
	echo "<hr>คืนสิทธิ์ไม่สมบูรณ์";
}else{
	echo "<hr>คืนสิทธิ์เรียบร้อยครับ";
}


?>
<div align="center">
	<br>
	<br>
	<br>
  <input type="button" onclick="window.history.back();"  value='ย้อนกลับ' />
</div>
