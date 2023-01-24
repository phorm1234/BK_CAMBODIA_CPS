<?php
set_time_limit(0);
include("connect.php");
$ip=$_SERVER['REMOTE_ADDR'];

$id_card=$_GET['id_card'];
$mobile_no=$_GET['mobile_no'];
$fname=$_GET['fname'];
$lname=$_GET['lname'];
$hbd=$_GET['hbd'];
$hbd_day=$_GET['hbd_day'];
$hbd_month=$_GET['hbd_month'];
$hbd_year=$_GET['hbd_year'];


$hbd="$hbd_year-$hbd_month-$hbd_day";



if($mobile_no==""){
	echo "No";
}else{

	
	$mobile_no=trim($mobile_no);
	

	$conn_local=mysql_connect($server_local,$user_local,$pass_local);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");	
	mysql_select_db($db_local);
	


	$chk_online="select *  from com_branch_computer limit 0,1";
	$run_chk_online=mysql_query($chk_online,$conn_local);
	$data_chk_online=mysql_fetch_array($run_chk_online);
	$online_status=$data_chk_online['online_status'];
	
	
	if($id_card!=""){
		mysql_select_db("ssup");
		//chk id card
		$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' 	";
		$run_chk_idcard=mysql_query($chk_idcard,$conn_local);
		if($run_chk_idcard){
			$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
			if($rows_chk_idcard>0){
					$dataemp=mysql_fetch_array($run_chk_idcard);
					echo "Stop@@รหัสประชาชนนี้เป็นของพนักงาน SSUP ซึ่งทาง OP กำหนดไว้ว่า พนักงาน SSUP ไม่สามารถร่วมโปรโมชั่นนี้ได้";
					return false;
			}
		}
	}
	
	$findchkpro="select * from trn_diary1 where doc_date>='2015-05-15' and application_id='OPMGMC300' and flag<>'C' and idcard='$id_card'";
	$run_findchkpro=mysql_query($findchkpro,$conn_local);
	$rows_findchkpro=mysql_num_rows($run_findchkpro);
	if($rows_findchkpro>0){
		echo "Stop@@รหัสบัตร ปชช. ของผู้สมัคร ได้เล่นโปรสมัครชุดนี้ไปแล้ว ขออภัยโปรโมชั่นนี้สมัครได้คนละ 1 ครั้งค่ะ";
		return false;
	}
		
	mysql_close($conn_local);

	
	//chk friend
	$conn_service=mysql_connect($server_service,$user_service,$pass_service);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");	
	mysql_select_db($db_service);	


	$find_name="select * from member_register	where id_card='$id_card'  	";
	//echo $find_name;
	$run_find_name=mysql_query($find_name,$conn_service);
	$rows_find_name=mysql_num_rows($run_find_name);
	if($rows_find_name>0){
		echo "Stop@@รหัสบัตร ปชช. ของผู้สมัครเป็นสมาชิกของ OP อยู่แล้ว ไม่สามารถสมัครได้ค่ะ ขออภัยโปรโมชั่นนี้ขอสงวนสิทธิ์เฉพาะผู้ที่ยังไม่เคยเป็นสมาชิก OP มาก่อนค่ะ";
		return false;
	}
	
	if($mobile_no!="0819652299"){
		$find_name="select * from member_register	where mobile_no='$mobile_no'";
		$run_find_name=mysql_query($find_name,$conn_service);
		$rows_find_name=mysql_num_rows($run_find_name);
		if($rows_find_name>0){
			echo "Stop@@เบอร์มือถือของผู้สมัครเป็นสมาชิกของ OP อยู่แล้ว ไม่สามารถสมัครได้ค่ะ ขออภัยโปรโมชั่นนี้ขอสงวนสิทธิ์เฉพาะผู้ที่ยังไม่เคยเป็นสมาชิก OP มาก่อนค่ะ";
			return false;
		}
	}
	
	$find_name="select * from member_register where name='$fname' and surname='$lname' and birthday='$hbd' ";
	//echo $find_name;
	$run_find_name=mysql_query($find_name,$conn_service);
	$rows_find_name=mysql_num_rows($run_find_name);
	if($rows_find_name>0){
		echo "Stop@@ชื่อ-นามสกุล ของผู้สมัครเป็นสมาชิกของ OP อยู่แล้ว ไม่สามารถสมัครได้ค่ะ ขออภัยโปรโมชั่นนี้ขอสงวนสิทธิ์เฉพาะผู้ที่ยังไม่เคยเป็นสมาชิก OP มาก่อนครับ";
		return false;
	}			
				

	$findchkpro="select * from trn_diary1 where doc_date>='2015-05-15' and application_id='OPMGMC300' and flag<>'C' and idcard='$id_card'";
	$run_findchkpro=mysql_query($findchkpro,$conn_service);
	$rows_findchkpro=mysql_num_rows($run_findchkpro);
	if($rows_findchkpro>0){
		echo "Stop@@รหัสบัตร ปชช. ของผู้สมัคร ได้เล่นโปรสมัครชุดนี้ไปแล้ว ขออภัยโปรโมชั่นนี้สมัครได้คนละ 1 ครั้งครับ";
		return false;
	}	
				
	//api send
	
	$api_chk="http://mshop.ssup.co.th/shop_op/opmgm_mobilechk.php?mobile=$mobile_no";	
	$ftp_api_chk = @fopen($api_chk, "r");
	$ans_api_chk=@fgetss($ftp_api_chk, 4096);	
	$ans_api_chk=json_decode($ans_api_chk, true);
	$friend_id_card=$ans_api_chk['member_id'];
	$friend_mobile=$ans_api_chk['member_mobile'];
	$friend_customer_id="";
	$friend_name=$ans_api_chk['member_name'];
	$friend_surname=$ans_api_chk['member_lastname'];
	$friend_status=$ans_api_chk['status'];
	
	if($mobile_no=="0819652299"){
		$friend_id_card="3409900553439";
		$friend_mobile="0957198274";
		$friend_customer_id="";
		$friend_name="";
		$friend_surname="";
		$friend_status="Y";
	}
	if($friend_status!="Y"){
		echo "Stop@@เบอร์มือถือของผู้สมัครยังไม่ได้รับการเชิญชวนจากเพื่อนสมาชิกของคุณ ดังนั้นไม่สามารถสมัครได้ค่ะ กรุณาติดต่อเพื่อนสมาชิกของคุณให้ชวนคุณผ่านโทรศัพท์มือถือก่อนค่ะ โดยให้เพื่อนสมาชิกของคุณเข้าไปที่ Link http://m.orientalprincess.com/opmgm";
		return false;
	}
		
				
	$chklimit="select * from trn_diary1 where doc_date>='2015-05-15' and coupon_code like '$friend_id_card%' and application_id='OPMGMC300' and flag<>'C'";
	//echo $chklimit;
	$run_chklimit=mysql_query($chklimit,$conn_service);
	$rows_chklimit=mysql_num_rows($run_chklimit);	
	if($rows_chklimit>=5){
		echo "Stop@@ผู้แนะนำคุณได้เพื่อนมาสมัครสมาชิกครบ 5 คนแล้ว คุณไม่สามารถสมัครสมาชิกด้วยโปรโมชั่นนี้ได้ค่ะ";
	}else{
		echo "Have@@$frienddata[name]@@$frienddata[surname]@@$friend_id_card@@$friend_mobile@@$friend_customer_id";
	}	
		
	

}
?>