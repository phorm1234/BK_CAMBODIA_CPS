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
$mobile_new=$_GET['mobile_no'];
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


if($member_no!=""){
	$conn_server=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);
	//chk have local
	$find="
	select b.*,a.member_no from 
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
		$mobile_no=$profile_mobile;
	}else{
		echo "NOPROFILE###";
		return false;
	}
}

if($val_otp!="99999" && $val_otp!="55555"){
	if($confirm_mobile=="Y"){
			//chk otp
			$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_new&otp=$val_otp&promotion=$promo_code";	
			//echo $api_chkotp;
			$ftp_api_chkotp = @fopen($api_chkotp, "r");
			$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
			$ans_api_chkotp=json_decode($ans_api_chkotp, true);
			if($ans_api_chkotp['otp_status']!="OK"){
				echo "$ans_api_chkotp[otp_status]###$ans_api_chkotp[msg]";
				return false;
			}
	
	}else{
		if($status_readcard=="MANUAL"){
			
			//chk otp
			$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_new&otp=$val_otp&promotion=$promo_code";	
			//echo $api_chkotp;
			$ftp_api_chkotp = @fopen($api_chkotp, "r");
			$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
			$ans_api_chkotp=json_decode($ans_api_chkotp, true);
			if($ans_api_chkotp['otp_status']!="OK"){
				echo "$ans_api_chkotp[otp_status]###$ans_api_chkotp[msg]";
				return false;
			}
		}
	}
}
//no coupon code
$arr_nocoupon=array('LLSV101801');
if(in_array($promo_code,$arr_nocoupon)){
echo "Y###Completed";
			return false;
}

//สำหรับโปรทัวไม่ต้องเช็ค1คน/1สิทธิ์
if($promo_code=="OX02460217" || $promo_code=="LLWOM00118" || $promo_code=="18020004" || $promo_code=="TOUR01"|| $promo_code=="LLBCWG0418"||$promo_code=="LLNN201805"||$promo_code=="ALIPAY1018"||$promo_code=="LLFS061801" ||$promo_code=="LLFS061800"||$promo_code=="LLFS061802"||$promo_code=="LLFM180701"||$promo_code=="LLCC071801" ||$promo_code=="180801"||$promo_code=="LLST1808"||$promo_code=="LLOC180801"||$promo_code=="LLMDAY1801" ||$promo_code=="TURAMI01"||$promo_code=="LLAC180801" ||$promo_code=="AROMA09" ||$promo_code=="LLMT091801" ||$promo_code=="LLSV081801"||$promo_code=="LLCTB18001"||$promo_code=="LLKTC18001"||$promo_code=="LLSF0918001"||$promo_code=="LLPAPURI15"||$promo_code=="LLCTBSAM01"||$promo_code=="LLKTCSAM01"||$promo_code=="LL17090011"){

	if($promo_code=="OX02250117"){
		if(strtolower($otp_code)=="twbg01" || strtolower($otp_code)=="twbg02" || strtolower($otp_code)=="twbg03" || 
				strtolower($otp_code)=="twbg04" || strtolower($otp_code)=="twbg05" || strtolower($otp_code)=="twbg06" || 
				strtolower($otp_code)=="twbg07" || strtolower($otp_code)=="twbg08" || strtolower($otp_code)=="twbg09" || 
				strtolower($otp_code)=="mtop1701" || strtolower($otp_code)=="apop1701"){
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย TW,MT,AP ค่ะ";
			return false;
		}
	}elseif($promo_code=="LLST1808"){
		if(strtolower($otp_code)=="2"||strtolower($otp_code)=="3"||strtolower($otp_code)=="4"||strtolower($otp_code)=="1"){
			echo "Y###Completed";
			return false;	
		}else{
			echo "CODE_NOOK###กรุณาระบุจำนวนสินค้าที่จะซื้อในโปรโมชั่น (1-4)";
			return false;		
		}
	
	}

	if($promo_code=="TOUR01"){
		if(strtoupper($otp_code)=="CNBG11" || strtoupper($otp_code)=="CNBG12" || 
				strtolower($otp_code)=="twbg01" || strtolower($otp_code)=="twbg02" || strtolower($otp_code)=="twbg03" || 
				strtolower($otp_code)=="twbg04" || strtolower($otp_code)=="twbg05" || strtolower($otp_code)=="twbg06" || 
				strtolower($otp_code)=="twbg07" || strtolower($otp_code)=="twbg08" || strtolower($otp_code)=="twbg09" ||
				strtoupper($otp_code)=="CNBG13" || strtoupper($otp_code)=="CNBG14" || 
				strtoupper($otp_code)=="CNBG15" || strtoupper($otp_code)=="CNBG16" || 
				strtoupper($otp_code)=="CNBG17" || strtoupper($otp_code)=="CNBG18" || 
				strtoupper($otp_code)=="CNBG19" || strtoupper($otp_code)=="CNBG20" ||
				strtoupper($otp_code)=="CNBG13" || strtoupper($otp_code)=="CNBG14" || 
				strtoupper($otp_code)=="CNBG15" || strtoupper($otp_code)=="CNBG16" || 
				strtoupper($otp_code)=="CNBG17" || strtoupper($otp_code)=="CNBG18" || 
				strtoupper($otp_code)=="CNBG19" || strtoupper($otp_code)=="CNBG20" ||
				strtoupper($otp_code)=="HKBG01" || strtoupper($otp_code)=="HKBG02" ||
				strtoupper($otp_code)=="HKBG03" || strtoupper($otp_code)=="HKBG04" ||
				strtolower($otp_code)=="twbg13" ||strtoupper($otp_code)=="HKBG05"||
				strtolower($otp_code)=="twbg15"){
				
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย CNBG ค่ะ";
			return false;
		}
	}elseif($promo_code=="18020004" || $promo_code=="LLFS061800" ||$promo_code=="LLFS061802"||$promo_code=="180801"||$promo_code=="ALIPAY1018"||$promo_code=="TAMURI01" ||$promo_code=="LLAC180801"||$promo_code=="LLCTB18001"||$promo_code=="LLKTC18001"||$promo_code=="LLSF0918001"){
		
		echo "Y###Completed";
		return false;	
	}elseif($promo_code=="LLFS061801"){
		if(strtoupper(substr($otp_code,0,4))=="LLFS"){
			echo "Y###Completed";
			return false;					
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย LLFS ค่ะ";
			return false;
		}	
	}elseif($promo_code=="LLCC071801"||$promo_code=="LLOC180801"||$promo_code=="LLMDAY1801" ||$promo_code=="AROMA09" ||$promo_code=="LLMT091801" ||$promo_code=="LLSV081801" ||$promo_code=="TURAMI01"){
		// X Coupon


		$status="Y";
		$msg="Completed";
		echo $status."###".$msg;
		return false;

	}elseif($promo_code=="LLWOM00118"){
		if(strtoupper($otp_code)=="WOM00118"){
				
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย WOM ค่ะ";
			return false;
		}
	}elseif($promo_code=="LLNN201805"){
		if(strtoupper($otp_code)=="LLNS1"||strtoupper($otp_code)=="LLNS2"||strtoupper($otp_code)=="LLNS3" ||strtoupper($otp_code)=="LLNS4"||strtoupper($otp_code)=="LLNS5"||strtoupper($otp_code)=="CAFETELLER"){
			echo "Y###Completed";
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย LLNS ค่ะ";
			return false;
		}
	}elseif($promo_code=="LLBCWG0418"){
		if(strtoupper($otp_code)=="BCWG001"||strtoupper($otp_code)=="BCWG002"||strtoupper($otp_code)=="BCWG003" || strtoupper($otp_code)=="BCWG004"||strtoupper($otp_code)=="BCWG005"||strtoupper($otp_code)=="BCWG006" ||strtoupper($otp_code)=="BCWG007"||strtoupper($otp_code)=="BCWG008"||strtoupper($otp_code)=="BCWG009" ||strtoupper($otp_code)=="BCWG010"||strtoupper($otp_code)=="BCWG011"||strtoupper($otp_code)=="BCWG012" ||strtoupper($otp_code)=="BCWG013"||strtoupper($otp_code)=="BCWG014"||strtoupper($otp_code)=="BCWG015" ||strtoupper($otp_code)=="BCWG016"||strtoupper($otp_code)=="BCWG017"||strtoupper($otp_code)=="BCWG018" ||strtoupper($otp_code)=="BCWG019"||strtoupper($otp_code)=="BCWG020"||strtoupper($otp_code)=="BCWG021" ||strtoupper($otp_code)=="BCWG022"||strtoupper($otp_code)=="BCWG023"||strtoupper($otp_code)=="BCWG024" ||strtoupper($otp_code)=="BCWG025"||strtoupper($otp_code)=="BCWG026"||strtoupper($otp_code)=="BCWG027" ||strtoupper($otp_code)=="BCWG028"||strtoupper($otp_code)=="BCWG029"||strtoupper($otp_code)=="BCWG030" ||strtoupper($otp_code)=="BCWG031"||strtoupper($otp_code)=="BCWG032"||strtoupper($otp_code)=="BCWG033" ||strtoupper($otp_code)=="BCWG035"||strtoupper($otp_code)=="BCWG035"){
				
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย BCWG ค่ะ";
			return false;
		}
	}elseif($promo_code=="LLFM180701"){
		if(strtoupper($otp_code)=="PANSY" || strtoupper($otp_code)=="ESPIRIT" || strtoupper($otp_code)=="SBDESIGN" ){
			echo "Y###Complete";
			return false;
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องคือ PANSY , ESPIRIT , SBDESIGN ค่ะ";
			return false;
		}
	}elseif($promo_code=="LLBJAV1804"){
		if(strtoupper($otp_code)=="LLBJAV001"||strtoupper($otp_code)=="LLBJAV002"||strtoupper($otp_code)=="LLBJAV003" || strtoupper($otp_code)=="LLBJAV004"||strtoupper($otp_code)=="LLBJAV005"||strtoupper($otp_code)=="LLBJAV007"){
				
		}else{
			echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย LLBJAV ค่ะ";
			return false;
		}
	}
	echo "Y###Complete";
	return false;
}elseif($promo_code=="LLWISE1018"){
	if(strtoupper($otp_code)=="LLWISE10" ){
		echo "Y###Complete";
		return false;
	}else{
		echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องคือ LLWISE10 ค่ะ";
		return false;
	}
}elseif($promo_code=="LLFP201801"){
	echo "Y###Complete";
	return false;
}

// Check use duplicate
if($promo_code=="LLB012017"){
	$array_coupon = array("LLB01201701","LLB01201702","LLB01201703","LLB01201704","LLB01201705","LLB01201706","LLB01201707", "LLB01201708","LLB01201709","LLB01201710","LLB01201711","LLB01201712","LLB01201713","LLB01201714","LLB01201715");
	if(in_array(strtoupper($otp_code),$array_coupon)){
			$postdata = http_build_query( array('act'=>"test",'promo_code'=>$promo_code,'idcard'=>$id_card));

			$opts = array('http' => array( 'method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n" ."Referer: urltopost\r\n", 'content' => $postdata ) );
			$context = stream_context_create($opts); 
			$result = file_get_contents('http://mshop.ssup.co.th/shop_ll/other_promo.php', false, $context);
			//echo "CODE_NOOK###".$result;
			
			$data = json_decode($result,true); 
			
			if($data['status']=="Y"){
				echo "Y###Complete";
			}else{
				echo "CODE_NOOK###".$data[msg];
			}
			/**/
		
		return false;
		
	}else{
		echo "CODE_NOOK###รูปแบบรหัส E-coupon นั้นไม่ถูกต้อง รูปแบบที่ถูกต้องจะขึ้นต้นด้วย LLB ค่ะ";
		return false;
	}

}




if($confirm_mobile=="Y" && mobile_no!=""){
	$keep_log="insert into member_changeprofile_bypro(`promo_code`, `shop`, `customer_id`, `member_id`, `id_card`, `mobile_old`, `mobile_new`, `upd_date`, `upd_time`) values('$promo_code','$shop','$dataprofile[customer_id]','$dataprofile[member_no]','$id_card','$dataprofile[mobile_no]','$mobile_new',date(now()),time(now()))";
	//echo $keep_log;
	$run_keep_log=mysql_query($keep_log,$conn_server);
	if(!$run_keep_log){
		echo "ERROR###ไม่สามารถ Update เบอร์มือถือให้สมาชิกได้ กรุณาลองใหม่อีกครั้ง หรือ ติดต่อที่ Helpdesk ค่ะ";
		return false;
	}else{
		if($dataprofile['customer_id']!=""){
			$up_profile_bymobile="update member_register set mobile_no='$mobile_new',upd_user='$promo_code', upd_date=date(now()),upd_time=time(now()) where customer_id='$dataprofile[customer_id]' ";
			//echo $up_profile;
			$run_up_profile_bymobile=mysql_query($up_profile_bymobile,$conn_server);
			if(!$run_up_profile_bymobile){
				echo "ERROR###ไม่สามารถ Update เบอร์มือถือสมาชิกได้ กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนค่ะ";
				return false;
			}
		}else{
			echo "ERROR###ไม่รหัส CustomerID ของสมาชิกท่านนี้ ระบบจึงไม่สามารถ Update เบอร์มือถือสมาชิกได้ กรุณาลองใหม่อีกครั้ง หรือ ติดต่อที่ Helpdesk ค่ะ";
			return false;
		}
	}
}

/*
if($promo_code=="OPLID300"){
	//chk otp
	$api_chkotp="http://mshop.ssup.co.th/shop_op/op_promotion_check.php?idcard=$id_card&shop=$shop&memberid=xxx&mobile=$mobile_no&otp=$val_otp&promotion=$promo_code";	
	$ftp_api_chkotp = @fopen($api_chkotp, "r");
	$ans_api_chkotp=@fgetss($ftp_api_chkotp, 4096);	
	$ans_api_chkotp=json_decode($ans_api_chkotp, true);
	if($ans_api_chkotp['otp_status']!="OK"){
		echo "$ans_api_chkotp[otp_status]###$ans_api_chkotp[msg]";
		return false;
	}
}
*/
//ถอดรหัส



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




//chk pro
###########################################################################


if($promo_code=="OPID300" || $promo_code=="OPGNC300" || $promo_code=="OPPGI300" || $promo_code=="OPDTAC300" || $promo_code=="OPKTC300"  || $promo_code=="OPTRUE300"  || substr($promo_code,0,3)=="OPN"){
//chk have online-------------------------------------------------------
	$con_online=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);	
	
	if($id_card!=""){
		$check_profile_dup="select * from member_register as a inner join member_history as b on a.customer_id=b.customer_id where a.id_card='$id_card' and expire_date>=date(now()) and b.status_active='Y' ";
		//echo $check_profile_dup;
		$run_check_profile_dup=mysql_query($check_profile_dup,$con_online);
		$rows_check_profile_dup=mysql_num_rows($run_check_profile_dup);
		if($rows_check_profile_dup>0){
			echo "CODE_NOOK###รหัสบัตร ปชช. นี้เป็นสมาชิกของเราอยู่แล้วค่ะ ไม่สามารถสมัครใหม่ได้อีก";
			return false;
		}
	}
	
}
if($promo_code=="OPPLI300" ){ //proline
//chk have online-------------------------------------------------------
	$con_online=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($db_service);	
	
	if($id_card!=""){
		$check_profile_dup="select * from member_register as a inner join member_history as b on a.customer_id=b.customer_id where a.id_card='$id_card' and expire_date>=date(now()) and b.status_active='Y' ";
		//echo $check_profile_dup;
		$run_check_profile_dup=mysql_query($check_profile_dup,$con_online);
		$rows_check_profile_dup=mysql_num_rows($run_check_profile_dup);
		if($rows_check_profile_dup>0){
			echo "CODE_NOOK###รหัสบัตร ปชช. นี้เป็นสมาชิกของเราอยู่แล้วค่ะ ไม่สามารถสมัครใหม่ได้อีก";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$check_profile_dup="select * from member_register as a inner join member_history as b on a.customer_id=b.customer_id where a.mobile_no='$mobile_no' and expire_date>=date(now()) and b.status_active='Y' ";
		$run_check_profile_dup=mysql_query($check_profile_dup,$con_online);
		$rows_check_profile_dup=mysql_num_rows($run_check_profile_dup);
		//echo $check_profile_dup;
		if($rows_check_profile_dup>0){
			echo "CODE_NOOK###เบอร์มือถือนี้เป็นสมาชิกของเราอยู่แล้วค่ะ ไม่สามารถสมัครใหม่ได้อีก";
			return false;
		}	
	}
	mysql_close($con_online);
}




if($promo_code=="OI06320417" || $promo_code=="OPPLI300"){
	$otp_code_ok = substr(6320417+$id_card+29381,-5);
	//echo "$otp_code!=$otp_code_ok";
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

}else if($promo_code=="LLF001" || $promo_code=='LL18040005'|| $promo_code=='LL002'){
		echo "Y###Completed";
		return false;	
}else{

	if($chk_ecoupon=="Y"){
		preg_match("/[[:digit:]]+\.?[[:digit:]]*/", $promo_code , $promo_code_num ) ;
		$otp_code_ok = substr(intval($promo_code_num[0])+$id_card+29381,-5);
		//echo "$otp_code!=$otp_code_ok";
		if($otp_code!=$otp_code_ok){
			echo "CODE_NOOK###รหัส E-coupon ไม่ถูกต้อง";
			return false;
		}
	}
}







/*
$path_api="http://mshop.ssup.co.th/shop_op/promo_redeem.php?coupon_code=$otp_code&promo_code=$promo_code&idcard=$id_card&act=request&memberid=$member_no";	
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
}
*/


$conn_local=mysql_connect($server_local,$user_local,$pass_local);
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
mysql_select_db($db_local);

//chk pro patle old

if($promo_code!="OM13160416" && $promo_code!="OM13061216"){
	if($id_card!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code' ";
		
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'";
		$run_find_local=mysql_query($find_local,$conn_local);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_new!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and mobile_no='$mobile_new' and flag<>'C' and co_promo_code='$promo_code'";
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
				echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
				return false;
			}
		}
		if($member_no!=""){	
			$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and member_id='$member_no'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
				return false;
			}	
		}
		if($mobile_no!=""){	
			$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and mobile_no='$mobile_no'  ";
			$run_find_local=mysql_query($find_local,$conn_local);
			$rows_find_local=mysql_num_rows($run_find_local);
			if($rows_find_local>0){
				$data_local=mysql_fetch_array($run_find_local);
				echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
				return false;
			}	
		}	
	
	}
	
	
}



if($promo_code=="OM13160416"  ||  $promo_code=="OM13061216" ){ //chk beatybook 2016,2017 รับได้เดือนละ1 ครั้ง
	if($id_card!=""){
		$find_local="select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  and month(doc_date)=month('$doc_date')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id in('OPID300','OPPLI300','OPMGMI300','OPPHI300','OPLID300','REID','CHID','OPPGI300','OPGNC300','OPPLI300','OPDTAC300','OPKTC300','OPTRUE300')
		union all
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and idcard='$id_card' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id like 'OP%'
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

if($promo_code!="OM13160416"  && $promo_code!="OM13061216"){
	//chk pro patle old
	if($id_card!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and idcard='$id_card' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	
	if($member_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and member_id='$member_no' and flag<>'C' and co_promo_code='$promo_code'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($mobile_no!=""){
		$find_local="select * from trn_diary1 where doc_date>='2017-01-01' and mobile_no='$mobile_no' and flag<>'C' and co_promo_code='$promo_code'   ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###เบอร์มือถือนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}

}


if($promo_code=="OM13160416"  ||  $promo_code=="OM13061216"){ //chk beatybook 2016,2017 รับได้เดือนละ1 ครั้ง
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
select doc_no,branch_id,doc_date from trn_diary1 where doc_date>='2016-06-01' and mobile_no='$mobile_no' and flag<>'C'   and month(doc_date)=month('$doc_date') and application_id  like 'OPN%'
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



//chk line online
if( $promo_code=="OI06320417" || $promo_code=="OPPLI300" ){
	/*$find_local="select * from trn_diary1 where doc_date>='2015-07-07' and flag<>'C' and co_promo_code in('OI06010615','OPPLC300','OPPLI300') and coupon_code='$otp_code'  ";
	$run_find_local=mysql_query($find_local,$con_online);
	$rows_find_local=mysql_num_rows($run_find_local);
	if($rows_find_local>0){
		$data_local=mysql_fetch_array($run_find_local);
		echo "stop###ใช้แล้วที่สาขา $data_local[branch_id]";
		return false;
	}*/

	if($id_card!=""){		
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and idcard='$id_card'   ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตร ปชช. นี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}
	}
	
	if($member_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and member_id='$member_no'  ";
		$run_find_local=mysql_query($find_local,$con_online);
		$rows_find_local=mysql_num_rows($run_find_local);
		if($rows_find_local>0){
			$data_local=mysql_fetch_array($run_find_local);
			echo "stop###รหัสบัตรสมาชิกนี้ ใช้แล้วที่สาขา $data_local[branch_id] ด้วยบิลเลขที่ $data_local[doc_no] เมื่อวันที่ $data_local[doc_date]";
			return false;
		}	
	}
	
	if($mobile_no!=""){	
		$find_local="select * from trn_diary1 where doc_date>='2017-06-06' and flag<>'C' and co_promo_code in('OI06320417','OPPLI300') and mobile_no='$mobile_no' ";
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
//echo $add;
$run_add=mysql_query($add,$conn_local);
if($run_add){
	echo "Y###Complete";
}else{
	echo "N###ไม่สามารถบันทึกข้อมูลบันบัตรประชาชนได้";
}
?>