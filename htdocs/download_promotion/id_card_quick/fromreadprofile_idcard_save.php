<?php
set_time_limit(0);
include("connect.php");

$id_card=$_GET['id_card'];
$member_no=$_GET['member_no'];
$mobile_no=$_GET['mobile_no'];
$promo_code=$_GET['promo_code'];
$otp_code=$_GET['otp_code'];
$chk_ecoupon=$_GET['chk_ecoupon'];





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


/*if($otp_code=="22222"){
	echo "Y###Complete";
	return false;
}*/

//ถอดรหัส
if($chk_ecoupon=="Y"){
	if($promo_code=="OPPLI300" || $promo_code=="OI06320417"){
		$promo_code_num=substr($promo_code,3,50);
		$otp_code_ok = substr(6320417+$id_card+29381,-5);
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง($id_card)";
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
	
	}else{
		preg_match("/[[:digit:]]+\.?[[:digit:]]*/", $promo_code , $promo_code_num ) ;
		$otp_code_ok = substr(intval($promo_code_num[0])+$id_card+29381,-5);
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง($id_card)";
			return false;
		}
	
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
				echo "stopp###รหัสประชาชนนี้เป็นของพนักงาน SSUP ซึ่งทาง OP กำหนดไว้ว่า พนักงาน SSUP ไม่สามารถร่วมโปรโมชั่นนี้ได้";
				return false;
		}
	}
}
		
		
/*$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=$promo_code&idcard=$id_card&act=request&memberid=$member_no";	
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

if($promo_code!="OM13160416"  && $promo_code!="OM13061216" ){
	//chk pro patle old
	if($id_card!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'   ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code' ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code' ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
}



if($promo_code=="OM13160416"  ||  $promo_code=="OM13061216" ){ //chk beatybook 2016,2017 รับได้เดือนละ1 ครั้ง
	if($id_card!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		";
		//echo $find_local;
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			
			return false;
		}
	}
	
	if($member_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_new!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_new' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_new' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_new' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
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
if($promo_code=="OPPLI300" || $promo_code=="OI06320417"){
	/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
	$run_find_local=mysql_query($find_local,$conn_local);
	$rows_find_local=mysql_num_rows($run_find_local);
	if($rows_find_local>0){
		$data_local=mysql_fetch_array($run_find_local);
		echo "stop###รหัสคูปองนี้ ใช้แล้วที่สาขา $data_local[branch_id]";
		return false;
	}*/
	
	if($id_card!=""){				
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OPPLI300','OI06320417') and idcard='$id_card'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	if($member_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OPPLI300','OI06320417') and member_id='$member_no'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id]  ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}
	
	if($mobile_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OPPLI300','OI06320417') and mobile_no='$mobile_no'  ";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}	
}


//chk pro ที่ห้ามรหัส coupon ซ้ำ
if($promo_code=="OS06150616"){


		if($otp_code!=""){				
			$find_local="select * from trn_diary1 where doc_date>='2016-07-05' and flag<>'C' and co_promo_code='$promo_code' and coupon_code='$otp_code'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตร Coupon นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date] โดยรหัสบัตร ปชช. : $data_local[idcard] รหัสสมาชิก : $data_local[member_id] มือถือ : $data_local[mobile_no]";
				return false;
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



//chk line
if($promo_code=="OPPLI300" || $promo_code=="OI06320417"){
	/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
	$run_find_local=mysql_query($find_local,$con_online);
	$rows_find_local=mysql_num_rows($run_find_local);
	if($rows_find_local>0){
		$data_local=mysql_fetch_array($run_find_local);
		echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
		return false;
	}*/

	if($id_card!=""){		
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OPPLI300','OI06320417') and idcard='$id_card'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช.นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($member_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OPPLI300','OI06320417') and member_id='$member_no'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}
	
	if($mobile_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OPPLI300','OI06320417') and mobile_no='$mobile_no'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}	
}		



if($promo_code!="OM13160416"  && $promo_code!="OM13061216" ){
	//chk pro patle old
	if($id_card!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัส Member นี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}

}


if($promo_code=="OM13160416"  ||  $promo_code=="OM13061216" ){ //chk beatybook 2016,2017 รับได้เดือนละ1 ครั้ง
	//chk pro patle old
	if($id_card!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-03-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  and month(doc_date)=month('$doc_date') 
			union all
	select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')
			union all
	select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	
	if($member_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-03-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'  and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')		
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and member_id='$member_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'	
		 ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-03-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'   and month(doc_date)=month('$doc_date') 
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OPN%'
		 ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}

}


//chk pro ที่ห้ามรหัส coupon ซ้ำ
if($promo_code=="OS06150616"){


		if($otp_code!=""){				
			$find_local="select * from trn_diary1 where doc_date>='2016-07-05' and flag<>'C' and co_promo_code='$promo_code' and coupon_code='$otp_code'  ";
			$run_find_local=mysql_query($find_local,$con_online);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตร Coupon นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date] โดยรหัสบัตร ปชช. : $data_local[idcard] รหัสสมาชิก : $data_local[member_id] มือถือ : $data_local[mobile_no]";
				return false;
			}
		}

}


				
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
otp_code='$val_otp',
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


//mysql_close($conn_local);
?>