<?php
set_time_limit(0);
include("connect.php");
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
$val_otp=$_GET['val_otp'];
$member_no=$_GET['member_no'];					
$chk_ecoupon=$_GET['chk_ecoupon'];

$confirm_mobile=$_GET['confirm_mobile'];


						
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


//chk have local
/*$find="select * from member_idcard_other where id_card='$id_card' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_local);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "IDCARD_PLAY";
	return false;
}
$find="select * from member_idcard_other where mobile_no='$mobile_no' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_local);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "MOBILE_PLAY";
	return false;
}

mysql_close($conn_local);
*/

//chk have server online
/*$conn_server=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);
//chk have local
$find="select * from member_idcard_other where id_card='$id_card' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_server);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "IDCARD_PLAY";
	return false;
}
$find="select * from member_idcard_other where mobile_no='$mobile_no' and flag<>'C' and doc_no<>''  ";
$run_find=mysql_query($find,$conn_server);
$rows_find=mysql_num_rows($run_find);
if($rows_find>0){
	echo "MOBILE_PLAY";
	return false;
}*/

if($member_no!=""){
	$conn_server=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);
	//chk have local
	$find="
	select b.* from 
	member_history as a inner join member_register as b
	on a.customer_id=b.customer_id
	where a.member_no='$member_no'
	";
	$run_find=mysql_query($find,$conn_server);
	$rows_find=mysql_num_rows($run_find);
	if($rows_find>0){
		$dataprofile=mysql_fetch_array($run_find);
		$profile_mobile=$dataprofile['mobile_no'];
		$profile_id_card=$dataprofile['id_card'];
		if($mobile_no!=$profile_mobile && $mobile_no!="" && $confirm_mobile=="N"){
			echo "MOBILEDIFF###";
			return false;
		}
		if($id_card!=$profile_id_card){
			echo "IDCARDDIFF###";
			return false;
		}
	}else{
		echo "NOPROFILE###";
		return false;
	}
}



if($id_card!="" && $id_card<>'3101700625105'){
	mysql_select_db("ssup");
	//chk id card
	$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' 	";
	//echo $chk_idcard;
	$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
	if($run_chk_idcard){
		$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
		if($rows_chk_idcard>0){
				$dataemp=mysql_fetch_array($run_chk_idcard);
				echo "stop###รหัสประชาชนนี้เป็นของพนักงาน SSUP ซึ่งทาง OP กำหนดไว้ว่า พนักงาน SSUP ไม่สามารถร่วมโปรโมชั่นนี้ได้";
				return false;
		}
	}
}



//สำหรับโปรทัวไม่ต้องเช็ค1คน/1สิทธิ์
if($promo_code=="OX02460217" || $promo_code=="OX02460217_2" ){
	$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&promortion=$promo_code";	
	$ftp_otpcode = @fopen($path_api, "r");
	$arrotpcode=@fgetss($ftp_otpcode, 4096);	


	$arrotpcode=json_decode($arrotpcode, true);
	echo "$arrotpcode[sms_status]###$arrotpcode[msg]";
	return false;
}


if($promo_code=="OI06320417" || $promo_code=="OPPLI300"){
	$otp_code_ok = substr(6320417+$id_card+29381,-5);
	if($otp_code!=$otp_code_ok){
		echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
		return false;
	}
}else if($promo_code=="OS06150616"){ //ให้ใส่คูปอง แต่งไม่ต้องเช็คสูตร ให้เช็คการใช้ซ้ำอย่างเดียว
	if((int)$otp_code>=2016070001 && (int)$otp_code<=2016070180){
		
		if((int)$otp_code>=2016070001 && (int)$otp_code<=2016070030){
			if(strtotime($doc_date)>=strtotime("2016-07-30") && strtotime($doc_date)<=strtotime("2016-08-31") ){
	
			}else{
				//echo "CODE_NOOK###รหัส E-coupon นี้ ไม่อยู่ในระยะเวลาการเล่น ให้ตรวจสอบ Gift Voucher ที่สมาชิกนำมาอีกครั้ง";
				echo "CODE_NOOK###รหัส E-coupon นี้เล่นได้ในช่วงวันที่ 30/7/2016 ถึง 31/8/2016 เท่านั้น";
				return false;
			}
		}else if((int)$otp_code>=2016070031 && (int)$otp_code<=2016070060){
			if(strtotime($doc_date)>=strtotime("2016-08-01") && strtotime($doc_date)<=strtotime("2016-09-30") ){
	
			}else{
				//echo "CODE_NOOK###รหัส E-coupon นี้ ไม่อยู่ในระยะเวลาการเล่น ให้ตรวจสอบ Gift Voucher ที่สมาชิกนำมาอีกครั้ง";
				echo "CODE_NOOK###รหัส E-coupon นี้เล่นได้ในช่วงวันที่ 01/08/2016 ถึง 30/9/2016 เท่านั้น";
				return false;
			}
		}else if((int)$otp_code>=2016070061 && (int)$otp_code<=2016070090){
			if(strtotime($doc_date)>=strtotime("2016-09-01") && strtotime($doc_date)<=strtotime("2016-10-31") ){
	
			}else{
				//echo "CODE_NOOK###รหัส E-coupon นี้ ไม่อยู่ในระยะเวลาการเล่น ให้ตรวจสอบ Gift Voucher ที่สมาชิกนำมาอีกครั้ง";
				echo "CODE_NOOK###รหัส E-coupon นี้เล่นได้ในช่วงวันที่ 01/09/2016 ถึง 31/10/2016 เท่านั้น";
				return false;
			}
		}else if((int)$otp_code>=2016070091 && (int)$otp_code<=2016070120){
			if(strtotime($doc_date)>=strtotime("2016-10-01") && strtotime($doc_date)<=strtotime("2016-11-30") ){
	
			}else{
				//echo "CODE_NOOK###รหัส E-coupon นี้ ไม่อยู่ในระยะเวลาการเล่น ให้ตรวจสอบ Gift Voucher ที่สมาชิกนำมาอีกครั้ง";
				echo "CODE_NOOK###รหัส E-coupon นี้เล่นได้ในช่วงวันที่ 01/10/2016 ถึง 30/11/2016 เท่านั้น";
				return false;
			}
		}else if((int)$otp_code>=2016070121 && (int)$otp_code<=2016070150){
			if(strtotime($doc_date)>=strtotime("2016-11-01") && strtotime($doc_date)<=strtotime("2016-12-31") ){
	
			}else{
				//echo "CODE_NOOK###รหัส E-coupon นี้ ไม่อยู่ในระยะเวลาการเล่น ให้ตรวจสอบ Gift Voucher ที่สมาชิกนำมาอีกครั้ง";
				echo "CODE_NOOK###รหัส E-coupon นี้เล่นได้ในช่วงวันที่ 01/11/2016 ถึง 31/12/2016 เท่านั้น";
				return false;
			}
		}else if((int)$otp_code>=2016070151 && (int)$otp_code<=2016070180){
			if(strtotime($doc_date)>=strtotime("2016-12-01") && strtotime($doc_date)<=strtotime("2016-12-31") ){
	
			}else{
				//echo "CODE_NOOK###รหัส E-coupon นี้ ไม่อยู่ในระยะเวลาการเล่น ให้ตรวจสอบ Gift Voucher ที่สมาชิกนำมาอีกครั้ง";
				echo "CODE_NOOK###รหัส E-coupon นี้เล่นได้ในช่วงวันที่ 01/12/2016 ถึง 31/12/2016 เท่านั้น";
				return false;
			}
		}
	
	}else{
		echo "CODE_NOOK###รหัส E-coupon นี้ ไม่ตรงกัับ Gift Voucher ใดๆเลย ที่ทาง OP แจกให้กับสมาชิก";
		return false;
	}
	
}else if($promo_code=="OC04250816"){ //pro ais เก็บรหัสเฉยๆ แต่ไม่ check
	
	if(strtolower(substr($otp_code,0,2))!="op"){
		echo "CODE_NOOK###รูปแบบรหัส E-coupon ไม่ถูกต้อง รูปแบบที่ถูกคือ OPxxxxxx";
		return false;
	}
	
	if(strlen(trim($otp_code))!=8){
		echo "CODE_NOOK###รูปแบบรหัส E-coupon ไม่ถูกต้อง รูปแบบที่ถูกคือ OPxxxxxx";
		return false;
	}
	
}else if($promo_code=="OPTRUE300"){ //pro OPTRUE300 ให้เช็คการใช้ซ้ำ
			$api_chkcoupon="http://10.100.53.2/ims/joke/app_service_op/api_member/api_coupon_promotion.php?coupon_code=$otp_code&promo_code=$promo_code&doc_date=$doc_date";	
			$ftp_api_chkcoupon = @fopen($api_chkcoupon, "r");
			$ans_api_chkcoupon=@fgetss($ftp_api_chkcoupon, 4096);
			$arr_chkcoupon=explode("#",$ans_api_chkcoupon);	
			if($arr_chkcoupon[0]=="N"){
				echo "CODE_NOOK###$arr_chkcoupon[1]";
				return false;
			}	
		
}else {
	if($chk_ecoupon=="Y"){
		preg_match("/[[:digit:]]+\.?[[:digit:]]*/", $promo_code , $promo_code_num ) ;
		$otp_code_ok = substr(intval($promo_code_num[0])+$id_card+29381,-5);
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
	}

}


/*
$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=$promo_code&idcard=$id_card&act=request&memberid=$member_no";	
echo $path_api;
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
}
*/


$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);

if($promo_code!="OM13160416" && $promo_code!="OM13061216" ){
	//chk have local
	if($id_card!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  ";
		//echo $find_local;
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัส ปชช. นี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	//chk have local
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
}

if($promo_code=="OM13160416" ||  $promo_code=="OM13061216" ){ //chk beatybook 2016 รับได้เดือนละ1 ครั้ง
	//chk have local
	if($id_card!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2015-02-23' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date') 
			union all
	select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPTRUE300','OPDTAC300','OPKTC300','OPTRUE300')
			union all
	select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id  like 'OPN%'
	";
		//echo $find_local;
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัส ปชช. นี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	//chk have local
	if($member_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2015-02-23' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'    and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		 ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2015-02-23' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'    and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id  like 'OPN%'
		 ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}

}


//chk line
if($promo_code=="OI06320417" || $promo_code=="OPPLI300"){
	/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
	$run_find_local=mysql_query($find_local,$conn_local);
	$rows_find_local=mysql_num_rows($run_find_local);
	if($rows_find_local>0){
		$data_local=mysql_fetch_array($run_find_local);
		echo "stop###รหัสคูปองนี้ ใช้แล้วที่สาขา $data_local[branch_id]";
		return false;
	}*/
	if($id_card!="1909900188608"){
		if($id_card!=""){				
			$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and idcard='$id_card'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
				return false;
			}
		}
		if($member_no!=""){	
			$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and member_id='$member_no'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
				return false;
			}	
		}
		if($mobile_no!=""){	
			$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and mobile_no='$mobile_no'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
				return false;
			}	
		}	
	}
}
mysql_close($conn_local);
		
				
//chk have online-------------------------------------------------------
$con_online=mysql_connect($server_service,$user_service,$pass_service);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_service);	

if($promo_code!="OM13160416"  && $promo_code!="OM13061216" ){
	//chk pro patle old
	if($id_card!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	if($mobile_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'   ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}


}


if($promo_code=="OM13160416" ||  $promo_code=="OM13061216"){ //chk beatybook 2016 รับได้เดือนละ1 ครั้ง
	//chk pro patle old
	if($id_card!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2015-07-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date')
			union all
	select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
			union all
	select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		 ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	
	if($member_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2015-07-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		 ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	if($mobile_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2015-07-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		 ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}


}


//chk line
if($promo_code=="OI06320417" || $promo_code=="OPPLI300"){
	/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
	$run_find_local=mysql_query($find_local,$con_online);
	$rows_find_local=mysql_num_rows($run_find_local);
	if($rows_find_local>0){
		$data_local=mysql_fetch_array($run_find_local);
		echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
		return false;
	}*/

	if($id_card!=""){		
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and idcard='$id_card'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช.นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($member_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and member_id='$member_no'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}
	if($mobile_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and mobile_no='$mobile_no'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}	
}	



$path_api="http://mshop.ssup.co.th/shop_op/op_promotion.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&promortion=$promo_code";	
$ftp_otpcode = @fopen($path_api, "r");
$arrotpcode=@fgetss($ftp_otpcode, 4096);	


$arrotpcode=json_decode($arrotpcode, true);
echo "$arrotpcode[sms_status]###$arrotpcode[msg]";
?>