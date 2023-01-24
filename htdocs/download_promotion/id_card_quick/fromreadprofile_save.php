<?php
set_time_limit(0);
include("connect.php");
$promo_code=$_GET['promo_code'];
$member_no=$_GET['member_no'];
if($_GET['promo_code']=="OI07200216" || $_GET['promo_code']=="OPLID300"){

		$status_readcard=$_GET['status_readcard'];
		$status_photo=$_GET['status_photo'];
		$num_snap=$_GET['num_snap'];
		$id_img=$_GET['id_img'];
		$ip_this=$_GET['ip_this'];
		$address=$_GET['address'];
		$mu=$_GET['mu'];
		$tambon_name=$_GET['tambon_name'];
		$amphur_name=$_GET['amphur_name'];
		$province_name=$_GET['province_name'];
		$mr=$_GET['mr'];
		$sex=$_GET['sex'];
		$mr_en=$_GET['mr_en'];
		$fname_en=$_GET['fname_en'];
		$lname_en=$_GET['lname_en'];
		$card_at=$_GET['card_at'];
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];
		$promo_code=$_GET['promo_code'];
		$id_card=$_GET['id_card'];
		$fname=$_GET['fname'];
		$lname=$_GET['lname'];
		$mobile_no=$_GET['mobile_no'];
		$birthday=$_GET['birthday'];
		
		$otp_code=$_GET['otp_code'];
							
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];
		
		//ถอดรหัส
		/*$len_id=strlen($id_card);
		for($i=0; $i<$len_id; $i++){
			$id=$id+$id_card[$i];
		}
		
		
		
		$len_promo_code_num=strlen($promo_code_num);
		for($x=0; $x<$len_promo_code_num; $x++){
			$numcode=$numcode+$promo_code_num[$x];
		}
		//echo "id=".$id . "<br>";
		//echo "numcode=".$numcode;
		$otp_code_ok=$id+$numcode+(9711*9711);
		$otp_code_ok=substr($otp_code_ok,-5);*/
		//$otp_code_ok = substr($promo_code_num+$id_card+(9711*9711),-5);
		if($id_card!=""){
			mysql_select_db("ssup");
			//chk id card
			$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' 	";
			$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
			if($run_chk_idcard){
				$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
				if($rows_chk_idcard>0){
						$dataemp=mysql_fetch_array($run_chk_idcard);
						echo "stopp###รหัสประชาชนนี้เป็นของพนักงาน SSUP ซึ่งทาง OP กำหนดไว้ว่า พนักงาน SSUP ไม่สามารถร่วมโปรโมชั่นนี้ได้";
						return false;
				}
			}
		}
		mysql_close($conn_local);	


		if($promo_code=="OI07200216" || $promo_code=="OPLID300"){
			$con_online=mysql_connect($server_service,$user_service,$pass_service);
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");
			mysql_select_db($db_service);	
			$chk_new="select * from member_register as a inner join member_history as b
			on a.customer_id=b.customer_id
			where
				a.id_card='$id_card' and b.expire_date>=date(now()) and status_active='Y'
			";
			$run_chk_new=mysql_query($chk_new,$con_online);
			$rows_chk_new=mysql_num_rows($run_chk_new);
			if($rows_chk_new>0){
				echo "NEW_OLD###ลูกค้าท่านนี้เป็นสมาชิกเราอยู่แล้วไม่สามารถสมัครได้ ให้เปิดบิลขายได้เลยไม่ต้องสมัครค่ะ";
				return false;
			}
		}
		
		
		/*if($otp_code=="22222"){
			echo "Y###Complete";
			return false;
		}*/
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(7200216+$id_card+29381,-5);
		
		//preg_match("/[[:digit:]]+\.?[[:digit:]]*/", $promo_code , $promo_code_num ) ;
		//$otp_code_ok = substr(intval($promo_code_num[0])+$id_card+29381,-5);
		
		//echo "$otp_code!=$otp_code_ok";
		
		
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
		
		
		
		/*$path_api="http://mshop.ssup.co.th/shop_op/line_redeem.php?coupon_code=$otp_code&promo_code=$_GET[promo_code]&idcard=$id_card&act=request";	
		//echo $path_api;
		$ftp_otpcode = @fopen($path_api, "r");
		$arrotpcode=@fgetss($ftp_otpcode, 4096);	
		$arrotpcode=json_decode($arrotpcode, true);
		//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
		if($arrotpcode['status']=="lock"){
			echo "stop###$arrotpcode[status_msg]";
			return false;
		}
		if($arrotpcode['status']=="NO"){
			echo "code_eror###$arrotpcode[status_msg]";
			return false;
		}*/
		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		//chk have local-------------------------------------------------------
		$cr_search="$id_card#$otp_code";
		//check ecoupon code
		/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสคูปองนี้ ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}*/
		//check pro by idcard	
		if($id_card!=""){		
			$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI07200216','OPLID300') and idcard='$id_card'  ";
			//echo $find_local;
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id]";
				return false;
			}
		}
		//check pro by member	
		if($member_no!=""){	
			$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI07200216','OPLID300') and member_id='$member_no'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id]";
				return false;
			}	
		}
		mysql_close($conn_local);	
		
		//chk have online-------------------------------------------------------
		$con_online=mysql_connect($server_service,$user_service,$pass_service);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_service);	
		$cr_search="$id_card#$otp_code";
		//check ecoupon code
		/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสคูปองนี้ ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}*/
		
		if($id_card!=""){				
			$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI07200216','OPLID300') and idcard='$id_card'  ";
			$run_find_local=mysql_query($find_local,$con_online);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id]";
				return false;
			}
		}
		if($member_no!=""){	
			$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI07200216','OPLID300') and member_id='$member_no'  ";
			$run_find_local=mysql_query($find_local,$con_online);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id]";
				return false;
			}	
		}
		mysql_close($con_online);
		//end ---------------------------------------------------------------------

		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);

		$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$otp_code',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				   
		";
		$run_add=mysql_query($add,$conn_local);
		if($run_add){
			echo "Y###Complete";
		}else{
			echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
		}


}else if($_GET['promo_code']=="OI27120115"){
		$status_readcard=$_GET['status_readcard'];
		$status_photo=$_GET['status_photo'];
		$num_snap=$_GET['num_snap'];
		$id_img=$_GET['id_img'];
		$ip_this=$_GET['ip_this'];
		$address=$_GET['address'];
		$mu=$_GET['mu'];
		$tambon_name=$_GET['tambon_name'];
		$amphur_name=$_GET['amphur_name'];
		$province_name=$_GET['province_name'];
		$mr=$_GET['mr'];
		$sex=$_GET['sex'];
		$mr_en=$_GET['mr_en'];
		$fname_en=$_GET['fname_en'];
		$lname_en=$_GET['lname_en'];
		$card_at=$_GET['card_at'];
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];
		$promo_code=$_GET['promo_code'];
		$id_card=$_GET['id_card'];
		$fname=$_GET['fname'];
		$lname=$_GET['lname'];
		$mobile_no=$_GET['mobile_no'];
		$birthday=$_GET['birthday'];
		
		$otp_code=$_GET['otp_code'];
							
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];


		
		
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(27120115+$id_card+29381,-5);
		//echo "otp_code_ok=".$otp_code_ok;
		
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
		
		
		$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=OI27120115&idcard=$id_card&act=request";	
		//echo $path_api;
		$ftp_otpcode = @fopen($path_api, "r");
		$arrotpcode=@fgetss($ftp_otpcode, 4096);	
		$arrotpcode=json_decode($arrotpcode, true);
		//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
		if($arrotpcode['status']=="NO"){
			echo "stop###$arrotpcode[status_msg]";
			return false;
		}

		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		//chk have local
		//$cr_search="$id_card#$otp_code";
		$find_local="select * from trn_diary1 where doc_date>='2015-01-23' and flag<>'C' and idcard='$id_card' and coupon_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}
		
		
		
		$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$otp_code',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				   
		";
		$run_add=mysql_query($add,$conn_local);
		if($run_add){
			echo "Y###Complete";
		}else{
			echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
		}

}else if($_GET['promo_code']=="OK01160115"){
		$status_readcard=$_GET['status_readcard'];
		$status_photo=$_GET['status_photo'];
		$num_snap=$_GET['num_snap'];
		$id_img=$_GET['id_img'];
		$ip_this=$_GET['ip_this'];
		$address=$_GET['address'];
		$mu=$_GET['mu'];
		$tambon_name=$_GET['tambon_name'];
		$amphur_name=$_GET['amphur_name'];
		$province_name=$_GET['province_name'];
		$mr=$_GET['mr'];
		$sex=$_GET['sex'];
		$mr_en=$_GET['mr_en'];
		$fname_en=$_GET['fname_en'];
		$lname_en=$_GET['lname_en'];
		$card_at=$_GET['card_at'];
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];
		$promo_code=$_GET['promo_code'];
		$id_card=$_GET['id_card'];
		$fname=$_GET['fname'];
		$lname=$_GET['lname'];
		$mobile_no=$_GET['mobile_no'];
		$birthday=$_GET['birthday'];
		
		$otp_code=$_GET['otp_code'];
							
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];


		
		
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(1160115+$id_card+29381,-5);
		//echo "otp_code_ok=".$otp_code_ok;
		
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
		
		
		$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=OK01160115&idcard=$id_card&act=request";	
		//echo $path_api;
		$ftp_otpcode = @fopen($path_api, "r");
		$arrotpcode=@fgetss($ftp_otpcode, 4096);	
		$arrotpcode=json_decode($arrotpcode, true);
		//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
		if($arrotpcode['status']=="NO"){
			echo "stop###$arrotpcode[status_msg]";
			return false;
		}

		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		//chk have local
		//$cr_search="$id_card#$otp_code";
		$find_local="select * from trn_diary1 where doc_date>='2015-01-23' and flag<>'C' and idcard='$id_card' and coupon_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}
		
		
		
		$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$otp_code',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				   
		";
		$run_add=mysql_query($add,$conn_local);
		if($run_add){
			echo "Y###Complete";
		}else{
			echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
		}
}else if($_GET['promo_code']=="OI27150115"){
		$status_readcard=$_GET['status_readcard'];
		$status_photo=$_GET['status_photo'];
		$num_snap=$_GET['num_snap'];
		$id_img=$_GET['id_img'];
		$ip_this=$_GET['ip_this'];
		$address=$_GET['address'];
		$mu=$_GET['mu'];
		$tambon_name=$_GET['tambon_name'];
		$amphur_name=$_GET['amphur_name'];
		$province_name=$_GET['province_name'];
		$mr=$_GET['mr'];
		$sex=$_GET['sex'];
		$mr_en=$_GET['mr_en'];
		$fname_en=$_GET['fname_en'];
		$lname_en=$_GET['lname_en'];
		$card_at=$_GET['card_at'];
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];
		$promo_code=$_GET['promo_code'];
		$id_card=$_GET['id_card'];
		$fname=$_GET['fname'];
		$lname=$_GET['lname'];
		$mobile_no=$_GET['mobile_no'];
		$birthday=$_GET['birthday'];
		
		$otp_code=$_GET['otp_code'];
							
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];


		
		
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(27150115+$id_card+29381,-5);
		//echo "otp_code_ok=".$otp_code_ok;
		
		/*if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}*/
		
		
		$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=OI27150115&idcard=$id_card&act=request";	
		//echo $path_api;
		$ftp_otpcode = @fopen($path_api, "r");
		$arrotpcode=@fgetss($ftp_otpcode, 4096);	
		$arrotpcode=json_decode($arrotpcode, true);
		//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
		if($arrotpcode['status']=="NO"){
			echo "stop###$arrotpcode[status_msg]";
			return false;
		}

		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		//chk have local
		//$cr_search="$id_card#$otp_code";
		$find_local="select * from trn_diary1 where doc_date>='2015-01-23' and flag<>'C' and idcard='$id_card' and coupon_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}
		
		
		
		$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$otp_code',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				   
		";
		$run_add=mysql_query($add,$conn_local);
		if($run_add){
			echo "Y###Complete";
		}else{
			echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
		}
}else if($_GET['promo_code']=="OK16050215"){
		$status_readcard=$_GET['status_readcard'];
		$status_photo=$_GET['status_photo'];
		$num_snap=$_GET['num_snap'];
		$id_img=$_GET['id_img'];
		$ip_this=$_GET['ip_this'];
		$address=$_GET['address'];
		$mu=$_GET['mu'];
		$tambon_name=$_GET['tambon_name'];
		$amphur_name=$_GET['amphur_name'];
		$province_name=$_GET['province_name'];
		$mr=$_GET['mr'];
		$sex=$_GET['sex'];
		$mr_en=$_GET['mr_en'];
		$fname_en=$_GET['fname_en'];
		$lname_en=$_GET['lname_en'];
		$card_at=$_GET['card_at'];
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];
		$promo_code=$_GET['promo_code'];
		$id_card=$_GET['id_card'];
		$fname=$_GET['fname'];
		$lname=$_GET['lname'];
		$mobile_no=$_GET['mobile_no'];
		$birthday=$_GET['birthday'];
		
		$otp_code=$_GET['otp_code'];
							
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];


		
		
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(16050215+$id_card+29381,-5);
		//echo "otp_code_ok=".$otp_code_ok;
		
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
		
		
		$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=OK16050215&idcard=$id_card&act=request";	
		//echo $path_api;
		$ftp_otpcode = @fopen($path_api, "r");
		$arrotpcode=@fgetss($ftp_otpcode, 4096);	
		$arrotpcode=json_decode($arrotpcode, true);
		//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
		if($arrotpcode['status']=="NO"){
			echo "stop###$arrotpcode[status_msg]";
			return false;
		}

		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		//chk have local
		//$cr_search="$id_card#$otp_code";
		$find_local="select * from trn_diary1 where doc_date>='2015-02-16' and flag<>'C' and idcard='$id_card' and coupon_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}
		
		
		
		$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$otp_code',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				   
		";
		$run_add=mysql_query($add,$conn_local);
		if($run_add){
			echo "Y###Complete";
		}else{
			echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
		}
}else if($_GET['promo_code']=="OK04061215"){
		$status_readcard=$_GET['status_readcard'];
		$status_photo=$_GET['status_photo'];
		$num_snap=$_GET['num_snap'];
		$id_img=$_GET['id_img'];
		$ip_this=$_GET['ip_this'];
		$address=$_GET['address'];
		$mu=$_GET['mu'];
		$tambon_name=$_GET['tambon_name'];
		$amphur_name=$_GET['amphur_name'];
		$province_name=$_GET['province_name'];
		$mr=$_GET['mr'];
		$sex=$_GET['sex'];
		$mr_en=$_GET['mr_en'];
		$fname_en=$_GET['fname_en'];
		$lname_en=$_GET['lname_en'];
		$card_at=$_GET['card_at'];
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];
		$promo_code=$_GET['promo_code'];
		$id_card=$_GET['id_card'];
		$fname=$_GET['fname'];
		$lname=$_GET['lname'];
		$mobile_no=$_GET['mobile_no'];
		$birthday=$_GET['birthday'];
		
		$otp_code=$_GET['otp_code'];
							
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
				
		$find_shop="select *  from com_branch_computer limit 0,1";
		$run_shop=mysql_query($find_shop,$conn_local);
		$data_shop=mysql_fetch_array($run_shop);
		$shop=$data_shop['branch_id'];
		$com_ip=$data_shop['com_ip'];
		
		$find_doc_date="select *  from com_doc_date";
		$run_find_doc_date=mysql_query($find_doc_date,$conn_local);
		$data_doc_date=mysql_fetch_array($run_find_doc_date);
		$doc_date=$data_doc_date['doc_date'];


		
		
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(04061215+$id_card+29381,-5);
		//echo "otp_code_ok=".$otp_code_ok;
		
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
		
		
		$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=OK04061215&idcard=$id_card&act=request";	
		//echo $path_api;
		$ftp_otpcode = @fopen($path_api, "r");
		$arrotpcode=@fgetss($ftp_otpcode, 4096);	
		$arrotpcode=json_decode($arrotpcode, true);
		//echo "x==$arrotpcode[status]/status_msg==$arrotpcode[status_msg]";
		if($arrotpcode['status']=="NO"){
			echo "stop###$arrotpcode[status_msg]";
			return false;
		}

		
		
		$conn_local=mysql_connect($server_local,$user_local,$pass_local);
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		mysql_select_db($db_local);
		
		//chk have local
		//$cr_search="$id_card#$otp_code";
		$find_local="select * from trn_diary1 where doc_date>='2015-02-16' and flag<>'C' and idcard='$id_card' and coupon_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
			return false;
		}
		
		
		
		$add="
		insert into member_idcard_other_tmp
		set
		ip='$ip_this',
		add_readtype ='$status_readcard',
		otp_code='$otp_code',
		shop='$shop',
		member_no='$member_no',
		id_card='$id_card',
		mobile_no='$mobile_no',
		name='$fname',
		surname='$lname',
		birthday='$birthday',
		address='$address',
		mu='$mu',
		tambon_name='$tambon_name',
		amphur_name='$amphur_name',
		province_name='$province_name',
		mr='$mr',
		sex='$sex',
		mr_en='$mr_en',
		fname_en='$fname_en',
		lname_en='$lname_en',
		card_at='$card_at',
		start_date='$start_date',
		end_date='$end_date',
		channel='POS',
		doc_no='$doc_no',
		doc_date='$doc_date',
		doc_time='$doc_time',
		flag='$flag',
		promo_code='$promo_code',
		time_up=now()				   
		";
		$run_add=mysql_query($add,$conn_local);
		if($run_add){
			echo "Y###Complete";
		}else{
			echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
		}
}else if($_GET['promo_code']=="LLSAM0817"){
	echo "Y###Complete";
}else if($_GET['promo_code']=="LLB99201701"){
	echo "Y###Complete";
}

//mysql_close($conn_local);
?>