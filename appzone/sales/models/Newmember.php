<?php
class Model_Newmember extends Model_Calpromotion
{
	
		function formatmysql($dt) {   
			$arr=explode("/",$dt);
			$y=$arr[2];
			$m=$arr[1];
			$d=$arr[0];
			$dt_fomat="$y-$m-$d";
			return $dt_fomat;
		}
		
	function make_no($type_y,$y,$show) {  //สร้างเลขที่เอกสาร
					$type_y=$type_y;   //อักษรนำ
					$y=$y;  //จำนวนหลัก
					$show=$show;   //ลำดับตัวเลข

					$num=strlen($show);  
					//$countzero=($y-strlen($type_y))-$num;
					$countzero=($y)-$num;
					for ($j=1; $j<=$countzero; $j++) {
						$txt=$txt . "0";
					}
					$doc_no="$type_y$txt$show";

					return $doc_no;
	}
	
		function data_branch(){
			  $sql="select *  from com_branch where active='1' ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
			  return $data;
		}
		
		function province(){
			  $sql="select *  from view_province group by province_id order by  zip_province_nm_th ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
				$jsonarr=array();
	             foreach($data as $data){
					$province_id=$data['province_id'];    
					$province_name=$data['zip_province_nm_th'];       	
	                array_push($jsonarr,array("province_id"=>$province_id,"province_name"=>$province_name));  
	             }
	           return $jsonarr;
		}
		function amphur($province_id){
			  $sql="select zip_amphur_id,zip_amphur_nm_th  from view_province where province_id='$province_id' group by zip_amphur_id,zip_amphur_nm_th order by zip_amphur_nm_th ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
				$jsonarr=array();
	             foreach($data as $data){
					$amphur_id=$data['zip_amphur_id'];    
					$amphur_name=$data['zip_amphur_nm_th'];       	
	                array_push($jsonarr,array("amphur_id"=>$amphur_id,"amphur_name"=>$amphur_name));  
	             }
	           return $jsonarr;
		}
		function tambon($province_id,$amphur_id){
			  $sql="select zip_tambon_id,zip_tambon_nm_th  from view_province where province_id='$province_id' and zip_amphur_id='$amphur_id' group by zip_tambon_id,zip_tambon_nm_th order by zip_tambon_nm_th";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
				$jsonarr=array();
	             foreach($data as $data){
					$tambon_id=$data['zip_tambon_id'];    
					$tambon_name=$data['zip_tambon_nm_th'];       	
	                array_push($jsonarr,array("tambon_id"=>$tambon_id,"tambon_name"=>$tambon_name));  
	             }
	           return $jsonarr;
		}
		
		function findpostcode($province_id,$amphur_id,$tambon_id){
			  $sql="select distinct zipcode  from view_province 
			  where province_id='$province_id' and
			   zip_amphur_id='$amphur_id' and 
			   zip_tambon_id='$tambon_id' ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
			  return $data[0]['zipcode'];	

		}	
		function idshowprovince($province_name){
			  $sql="select *  from view_province group by province_id order by  zip_province_nm_th ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
				$jsonarr=array();
	             foreach($data as $data){
					$province_id=$data['province_id'];    
					$province_name=$data['zip_province_nm_th'];       	
	                array_push($jsonarr,array("province_id"=>$province_id,"province_name"=>$province_name));  
	             }
	           return $jsonarr;
		}
		function idshowamphur($province_name,$amphur_name){
			  $sql="select zip_amphur_id,zip_amphur_nm_th  from view_province where zip_province_nm_th='$province_name' group by zip_amphur_id,zip_amphur_nm_th order by zip_amphur_nm_th ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
				$jsonarr=array();
	             foreach($data as $data){
					$amphur_id=$data['zip_amphur_id'];    
					$amphur_name=$data['zip_amphur_nm_th'];       	
	                array_push($jsonarr,array("amphur_id"=>$amphur_id,"amphur_name"=>$amphur_name));  
	             }
	           return $jsonarr;
		}
		function idshowtambon($province_name,$amphur_name,$tambon_name){
			  $sql="select zip_tambon_id,zip_tambon_nm_th  from view_province where zip_province_nm_th='$province_name' and zip_amphur_nm_th='$amphur_name' group by zip_tambon_id,zip_tambon_nm_th order by zip_tambon_nm_th";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
				$jsonarr=array();
	             foreach($data as $data){
					$tambon_id=$data['zip_tambon_id'];    
					$tambon_name=$data['zip_tambon_nm_th'];       	
	                array_push($jsonarr,array("tambon_id"=>$tambon_id,"tambon_name"=>$tambon_name));  
	             }
	           return $jsonarr;
		}
		
		function data_date_bill(){
			  $sql="select *  from com_doc_date ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
			  $doc_date=$data[0]['doc_date'];
			  return $doc_date;
		}
		
		function card_type($member_no){
			  $sql="select *  from member_card_type where '$member_no' between id_start and id_end ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
			  $ops=$data[0]['ops'];
			  return $ops;
		}
		
		function search_diary1($doc_no_diary){
			$conn_shop=mysql_connect($this->sv_my,$this->user_my,$this->pass_my) or die("Connect Localhost Error");
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");
			mysql_select_db($this->db_my);
			
			
			/*$sql="select *,last_day(doc_date+interval 24 month) as expire_date from trn_diary1 where doc_no='$doc_no_diary'";*/
			$sql="select *,'2100-12-31' as expire_date from trn_diary1 where doc_no='$doc_no_diary'";
			$run=mysql_query($sql);
			$data=mysql_fetch_array($run);
			$member_id=$data['member_id'];
			$apply_date=$data['doc_date'];
			//$expire_date= date ("Y-m-t", strtotime("730 day", strtotime($apply_date))); 
			$expire_date=$data['expire_date'];
			$application_id=$data['application_id']; 
			$special_day=$data['special_day'];
			
			return "$member_id@$apply_date@$expire_date@$doc_no_diary@$application_id@$special_day";
			
		}
		
		
 		function chkmobile($computer_ip,$doc_no,$member_id,$apply_date,$expire_date,$branch_id,$mobile_no,$id_card,$mr,$fname,$lname,$mr_en,$fname_en,$lname_en,$nation,$address1,$address2,$address3,$address4,$address5,$sex,$hbd,$card_at,$start_date,$end_date,$chk_copy_address,$send_company,$send_address,$send_mu,$send_home_name,$send_soi,$send_road,$send_province_id,$send_amphur_id,$send_tambon_id,$send_tel,$send_mobile,$send_fax,$send_remark,$user_id,$send_email,$send_facebook,$noid_type,$noid_remark,$friend_id_card,$friend_mobile_no){

				
				if($mobile_no!=""){
					$path_mobile="http://crmopkh.ssup.co.th/app_service_opkh/api_member/member_chk_mobile.php?mobile_no=$mobile_no&id_card=$id_card&name=$fname&surname=$lname&birthday=$hbd";
					$run_ftp = file_get_contents($path_mobile);			
					$arr_chk_mobile=explode("###",$run_ftp);
					
					if($arr_chk_mobile[0]=="CHKMOBILENO"){				
							echo  $run_ftp;
							return false;
					}
				}
				
								


	   }
	   

	   
	   function registersave($computer_ip,$doc_no,$member_id,$apply_date,$expire_date,$branch_id,$mobile_no,$id_card,$mr,$fname,$lname,$mr_en,$fname_en,$lname_en,$nation,$address1,$address2,$address3,$address4,$address5,$sex,$hbd,$card_at,$start_date,$end_date,$chk_copy_address,$send_company,$send_address,$send_mu,$send_home_name,$send_soi,$send_road,$send_province_id,$send_amphur_id,$send_tambon_id,$send_tel,$send_mobile,$send_fax,$send_remark,$user_id,$send_email,$send_facebook,$noid_type,$noid_remark,$friend_id_card,$friend_mobile_no,$otp_code,$mobile_dup){
				
	   			$doc_no_sale=$doc_no;

				$tambon_id=$address3;
			
			
				$amphur_id=$address4;
			

				$province_id=$address5;

				/*if($sex=="หญิง"){
					$sex=2;
				}else{
					$sex=1;
				}*/
				
				if($mr=="" && $sex=="2"){ //เป็นผู้หญิง
					$mr="นางสาว";
				}else if($mr=="" && $sex=="1"){//เป็นผู้ชาย
					$mr="นาย";
				}
				

							
				$fname=str_replace("นาย","",$fname);
				$fname=str_replace("นางสาว","",$fname);
				$fname=str_replace("นาง","",$fname);
				

				/*$hbd=$this->formatmysql($hbd);
				$hbd_arr=explode("-",$hbd);
				$year=$hbd_arr[0]-543;
				
				$hbd="$year-$hbd_arr[1]-$hbd_arr[2]";*/
				

				$start_date=$this->formatmysql($start_date);
				$start_date_arr=explode("-",$start_date);
				$year_start_date=$start_date_arr[0]-543;
				$start_date="$year_start_date-$start_date_arr[1]-$start_date_arr[2]";
				
				
				$end_date=$this->formatmysql($end_date);
				$end_date_arr=explode("-",$end_date);
				$year_end_date=$end_date_arr[0]-543;
				$end_date="$year_end_date-$end_date_arr[1]-$end_date_arr[2]";
				
				$mu=$address2;					
				$address=$address1;
				
		
				$conn_service=mysql_connect($this->sv_my,$this->user_my,$this->pass_my) or die("Connect Localhost Error");
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");
				
				if($id_card!=""){
					mysql_select_db("ssup");
					//chk id card
					$chk_idcard="SELECT * FROM emp 	where numoffid='$id_card' and emp_active='1' ";
					$run_chk_idcard=mysql_query($chk_idcard,$conn_service);
					if($run_chk_idcard){
						$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
						if($rows_chk_idcard>0){
							echo  "dupemp";
							return false;
						}
					}
				}
				
								


								
				$conn_service=mysql_connect($this->sv_my,$this->user_my,$this->pass_my) or die("Connect Localhost Error");
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");
				

				
								
				mysql_select_db($this->db_my);
				
				
				if($member_id!=""){
					//chk id card
					$chk_idcard="SELECT * FROM member_history 	where member_no='$member_id' ";
					//echo $chk_idcard;
					$run_chk_idcard=mysql_query($chk_idcard,$conn_service);
					if($run_chk_idcard){
						$rows_chk_idcard=mysql_num_rows($run_chk_idcard);
						if($rows_chk_idcard>0){
							echo  "dupmember";
							return false;
						}
					}
				}
				
				//หาข้อมูลจังหวัด
				$find_province="SELECT * FROM `view_province` WHERE province_id = '$province_id'";
				$run_find_province=mysql_query($find_province,$conn_service);
				$data_province=mysql_fetch_array($run_find_province);
				$province_name=$data_province['zip_province_nm_th'];
				
				//หาตำบล
				$find_tambon="SELECT * FROM `view_province` WHERE province_id = '$province_id' and zip_tambon_id = '$tambon_id'";
				$run_find_tambon=mysql_query($find_tambon,$conn_service);
				$data_tambon=mysql_fetch_array($run_find_tambon);
				$tambon_name=$data_tambon['zip_tambon_nm_th'];
				
				//หาอำเภอ
				$find_amphur="SELECT * FROM `view_province` WHERE province_id = '$province_id' and zip_tambon_id = '$tambon_id' and  zip_amphur_id= '$amphur_id'";
				$run_find_amphur=mysql_query($find_amphur,$conn_service);
				$data_amphur=mysql_fetch_array($run_find_amphur);
				$amphur_name=$data_amphur['zip_amphur_nm_th'];
				
				//หารหัสไปรษณีย์
				$find_postcode="SELECT * FROM `view_province` WHERE province_id = '$province_id' and zip_tambon_id = '$tambon_id' and  zip_amphur_id= '$amphur_id'";
				$run_find_postcode=mysql_query($find_postcode,$conn_service);
				$data_postcode=mysql_fetch_array($run_find_postcode);
				$postcode_id=$data_amphur['zipcode'];
	   	
				//data branch
				$data_branch=$this->data_branch();
				$branch_id=$data_branch[0]['branch_id'];
				
				//ข้อมูลการจัดส่ง
				//หาข้อมูลจังหวัด
				$find_province="SELECT * FROM `view_province` WHERE `province_id` = '$send_province_id'";
				$run_find_province=mysql_query($find_province,$conn_service);
				$data_province=mysql_fetch_array($run_find_province);
				$send_province_name=$data_province['zip_province_nm_th'];

				//หาอำเภอ
				$find_amphur="SELECT * FROM `view_province` WHERE `province_id` = '$send_province_id' and zip_amphur_id = '$send_amphur_id' ";
				$run_find_amphur=mysql_query($find_amphur,$conn_service);
				$data_amphur=mysql_fetch_array($run_find_amphur);
				$send_amphur_name=$data_amphur['zip_amphur_nm_th'];
				
				//หาตำบล
				$find_tambon="SELECT * FROM `view_province` WHERE `province_id` = '$send_province_id' and zip_amphur_id = '$send_amphur_id' and zip_tambon_id='$send_tambon_id' ";
				$run_find_tambon=mysql_query($find_tambon,$conn_service);
				$data_tambon=mysql_fetch_array($run_find_tambon);
				$send_tambon_name=$data_tambon['zip_tambon_nm_th'];
				$send_postcode=$data_tambon['zipcode'];
				

				
				
				//gen เลขที่ customer_id_new
				
				$find="select doc_tp,doc_num as doc_num from member_document where doc_tp='NM' ";
				$run_find=mysql_query($find,$conn_service);
				$data=mysql_fetch_array($run_find);
				$doc_tp=$data['doc_tp'];
				$doc_num=$data['doc_num'];
				$now=date("Ym");
				$set_type="CP$branch_id$doc_tp-$now-";
				$doc_no=$this->make_no($set_type,'8',$doc_num);

				$databill=$this->search_diary1($doc_no_sale);
				$arrbill=explode("@",$databill);
				
				$application_id=$arrbill[4];
				$opsauto=$arrbill[5];
				
			   $add="
					insert into member_register
					set 
					customer_id_new='$doc_no',
					customer_id='$doc_no',
					`mobile_no`='$mobile_no',
					 phone_home_office='$send_tel',
					 phone_home='$send_tel',
					 phone_office='$send_tel',
					`id_card`='$id_card',
					`prefix`='$mr',
					`name`='$fname',
					`surname`='$lname',
					mr_en='$mr_en',
					fname_en='$fname_en',
					lname_en='$lname_en',
					nation='$nation',
					`sex`='$sex',
					`address`='$address',
					mu='$mu',
					road='$send_road',
					`province_id`='$province_id',
					`province_name`='$province_name',
					district_id='$tambon_id',
					`district`='$tambon_name',
					sub_district_id='$amphur_id',
					`sub_district`='$amphur_name',
					`zip`='$postcode_id',
					card_at='$card_at',
					start_date='$start_date',
					end_date='$end_date',
					`birthday`='$hbd',
					brand='OP',
					`shop`='$branch_id',
					doc_no='$doc_no_sale',
					doc_date='$apply_date',
					email_='$send_email',
					facebook='$send_facebook',
					`send_company`='$send_company',
					`send_address`='$send_address',
					`send_mu`='$send_mu',
					`send_home_name`='$send_home_name',
					`send_soi`='$send_soi',
					send_road='$send_road',
					`send_tambon_id`='$send_tambon_id',
					`send_tambon_name`='$send_tambon_name',
					`send_amphur_id`='$send_amphur_id',
					`send_amphur_name`='$send_amphur_name',
					`send_province_id`='$send_province_id',
					`send_province_name`='$send_province_name',
					`send_postcode`='$send_postcode',
					`send_tel`='$send_tel',
					`send_mobile`='$mobile_no',
					`send_fax`='$send_fax',
					`send_remark`='$send_remark',
					`reg_user`='$user_id',
					`reg_date`=date(now()),
					`reg_time`=time(now()),
					`upd_user`='$user_id',
					`upd_date`=date(now()),
					`upd_time`=time(now()),
					num1='$noid_type',
					var3='$noid_remark',
					application_id='$application_id'
			   
			   ";
				
				$run_add=mysql_query($add,$conn_service);
				
				$ops=$this->card_type($member_id);
				if($application_id=="OPID300" || substr($application_id,0,3)=="OPN"){
					$ops=$opsauto;
				}
		   		$addcard="insert into member_history
		   		set
	
		   			customer_id_new='$doc_no',
		   			customer_id='$doc_no',
		   			id_card='$id_card',
		   			member_no='$member_id',
		   			shop='$branch_id',
		   			apply_date='$apply_date',
		   			expire_date='$expire_date',
					doc_no='$doc_no_sale',
					doc_date='$apply_date',
					application_id='$application_id',
		   			status_active='Y',
		   			status='0',
		   			age_card='2',
		   			ops='$ops',
		   			reg_user='$user_id',
		   			reg_date=date(now()),
		   			reg_time=time(now()),
		   			upd_user='$user_id',
		   			upd_date=date(now()),
		   			upd_time=time(now()),
		   			time_up=now()
		   		";
				//echo $addcard;
		   		$run_addcard=mysql_query($addcard,$conn_service);
				if($otp_code!=""){
					$addsub="insert into member_register_friend(customer_id,id_card,friend_id_card,friend_mobile_no,reg_date,reg_time,`mobile_new`, `upmobile_date`, `upmobile_time`, `upmobile_otp_code`, `upmobile_channel`,data1) values('$doc_no','$id_card','$friend_id_card','$friend_mobile_no',date(now()),time(now()),'$mobile_no',date(now()),time(now()),'$otp_code','','$mobile_dup')";
					$run_addsub=mysql_query($addsub,$conn_service);
				}
							
				if($run_addcard==true){
						$up_doc="update member_document set doc_num=$doc_num+1 where doc_tp='$doc_tp' ";
						$run_up_doc=mysql_query($up_doc,$conn_service);
						
						$fp = @fopen("http://localhost/download_promotion/toserver_mem_register.php", "r");
						$text=@fgetss($fp, 4096);
									
						if($application_id=="OPMGMC300" || $application_id=="OPMGMI300"){
							$addsub="insert into member_register_friend(customer_id,id_card,friend_id_card,friend_mobile_no,reg_date,reg_time) values('$doc_no','$id_card','$friend_id_card','$friend_mobile_no',date(now()),time(now()))";
							$run_addsub=mysql_query($addsub,$conn_service);
							if($run_addsub){
								//$api_mark="http://mshop.ssup.co.th/shop_op/opmgm_mobilechk.php?mobile=$mobile_no&act=mark";	
		//echo $api_sendsms;
								//$ftp_api_mark = @fopen($api_mark, "r");
								//$arrapi_mark=@fgetss($ftp_api_mark, 4096);	
								//echo $api_mark;
							
					   		 }
							
							

							$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online) or die("Connect Online Error");
							mysql_query("SET character_set_results=utf8");
							mysql_query("SET character_set_client=utf8");
							mysql_query("SET character_set_connection=utf8");
							mysql_select_db($this->db_online);

							//find numpro
							$findnumpro="SELECT *
								FROM trn_diary1
								WHERE doc_date >= '2017-04-01'
								AND coupon_code LIKE '$friend_id_card%'
								AND application_id in('OPMGMC300','OPMGMI300')
								AND flag <> 'C'
								ORDER BY doc_date, doc_time
							";
							//echo $findnumpro;
							$runfindnumpro=mysql_query($findnumpro,$conn_online);
							$rowsnumpro=mysql_num_rows($runfindnumpro);
							echo "numpro=$rowsnumpro";
							mysql_select_db($this->db_my);
						}else{
							$rowsnumpro=0;
						}
						
						$week_ops=substr($ops,-1);
						echo "ops=$ops/w=$week_ops";
						/*if(strtotime($apply_date)>=strtotime("2015-05-18") && strtotime($apply_date)<=strtotime("2015-06-30")){
							$api_sendsms="http://mshop.ssup.co.th/shop_op/op_event_member.php?id_card=$id_card&mobile_no=$mobile_no&numpro=$rowsnumpro&friend_id_card=$friend_id_card&friend_mobile_no=$friend_mobile_no&promo_code=$application_id&doc_no=$doc_no_sale";	
	//echo $api_sendsms;
							$ftp_api_sendsms = @fopen($api_sendsms, "r");
							$arrotpcode=@fgetss($ftp_api_sendsms, 4096);	
							//$arrotpcode=json_decode($arrotpcode, true);
						}*/
						
						
						/*
						if($application_id=="OPPN300" ){
							echo "Case1<br>";
							$msg="ยินดีต้อนรับเข้าสู่ OPS สังคมผู้หญิงไม่หยุดสวย ตรวจสอบสิทธิประโยชน์ของคุณโดยดาวน์โหลด Mobile App ได้ที่ http://m.orientalprincess.com/";
						}else{
							echo "Case5<br>";
							$msg="";
						}
						
						//send sms
						$url="http://mshop.ssup.co.th/shop_op/op_event_member.php";
						$postdata = http_build_query( array('msg'=>$msg,'msisdn'=>$mobile_no,'id_card'=>$id_card,'mobile_no'=>$mobile_no,'numpro'=>$rowsnumpro,'friend_id_card'=>$friend_id_card,'friend_mobile_no'=>$friend_mobile_no,'promo_code'=>$application_id,'doc_no'=>$doc_no_sale));
						$opts = array('http' => array( 'method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n" ."Referer: urltopost\r\n", 'content' => $postdata ) );
						
						$context = stream_context_create($opts);
						$result = file_get_contents($url, false, $context); 
									
							
						echo "Send Mgm";	
						//for mgm GOLD CARD
						if($application_id=="OPMGMC300" || $application_id=="OPMGMI300"  ){
							if(strtotime($apply_date)>=strtotime("2017-04-01") && strtotime($apply_date)<=strtotime("2017-04-30")){
								echo "Send Mgm2";	
								if($rowsnumpro>=1 && $rowsnumpro<=5){
									echo "Send Mgm3";	
									echo "GOLD:1<br>";
									$msg="คุณได้ลด 50%2ชิ้นครั้งที่($rowsnumpro/5)จากการแนะนำเพื่อนสมัครสมาชิกใช้สิทธิ์ถึง 30เมย.นี้";
								}else if($rowsnumpro>=6 && $rowsnumpro<=9){
									echo "GOLD:2<br>";
									$msg="เพื่อนของคุณใช้สิทธิ์จากการแนะนำเพื่อนสมัครสมาชิกเป็นลำดับที่ $rowsnumpro แล้ว ขอบคุณที่ร่วมรายการกับ OP";
								}else if($rowsnumpro==10){
									echo "GOLD:3<br>";
									$msg="เพื่อนของคุณใช้สิทธิ์จากการนำเพื่อนสมัครสมาชิกเป็นลำดับที่10 ครบตามสิทธิ์ที่ได้รับแล้ว ขอบคุณที่ร่วมรายการกับ OP ติดตามสิทธิพิเศษอื่นๆที่ http://m.orientalprincess.com/";
								}
							}
						}
							
						//send sms
						echo "SMS GOLDE : ".$msg."<br>";
						$url="http://mshop.ssup.co.th/shop_op/op_event_member.php";
						$postdata = http_build_query( array('msg'=>$msg,'msisdn'=>$friend_mobile_no,'id_card'=>$id_card,'mobile_no'=>$friend_mobile_no,'numpro'=>$rowsnumpro,'friend_id_card'=>$friend_id_card,'friend_mobile_no'=>$friend_mobile_no,'promo_code'=>$application_id,'doc_no'=>$doc_no_sale));
						$opts = array('http' => array( 'method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n" ."Referer: urltopost\r\n", 'content' => $postdata ) );
						$context = stream_context_create($opts);
						
						$result = file_get_contents($url, false, $context); 
						
																											
						//up mobile
						if($application_id=="OPID300" ||  substr($application_id,0,3)=="OPN"){						
							$path_run="http://$this->sv_api/app_service_opkh/send_mobile/send_profile_byitem.php?customer_id=$doc_no";
							$fp = @fopen($path_run, "r");
							$text=@fgetss($fp, 4096);	
							
							$path_run="http://$this->sv_api/app_service_opkh/send_mobile/send_card_byitem.php?member_no=$member_id";
							$fp = @fopen($path_run, "r");
							$text=@fgetss($fp, 4096);								
						}
						
						
						*/
													
				}else{
						echo "no_add";
					
				}
		

	   }
	   
	   
	   
	
	   function clear_tmp_member($tbl,$computer_ip){
			$conn_service=mysql_connect($this->sv_my,$this->user_my,$this->pass_my) or die("Connect Localhost Error");
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");
			mysql_select_db($this->db_my);
			
			
	   		$del="delete from $tbl where computer_ip='$computer_ip' ";
	   		mysql_query($del,$conn_service);

	   }
	   
	   function listnewmembersub($varday){
			$conn_my=mysql_connect($this->sv_my,$this->user_my,$this->pass_my) or die("Connect Localhost Error");
			mysql_query("SET character_set_results=utf8");
			mysql_query("SET character_set_client=utf8");
			mysql_query("SET character_set_connection=utf8");
			mysql_select_db($this->db_my);
			
			$data_branch=$this->data_branch();
			$branch_id=$data_branch[0]['branch_id'];
			
			$doc_date=$this->data_date_bill();
			//echo "doc_date=$doc_date/";			
			/*if($varday=="today"){
				$cr_date=" and a.doc_date=date(now()) ";
			}else if($varday=="day3"){
				$cr_date=" and a.doc_date between date(now()) - interval 3 day and date(now()) ";
			}else if($varday=="month3"){
				$cr_date=" and a.doc_date between date(now()) - interval 90 day and date(now()) ";
			}else if($varday=="finish"){
				$cr_date=" and if(b.member_no is null,'N','Y')='Y' ";
			}else if($varday=="wait"){
				$cr_date=" and b.member_no is null ";
			}
			
			$show="
				select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
				from trn_diary1 as a left join (select * from member_history where reg_date>='2014-03-25' ) as b 

				on a.member_id=b.member_no
				where
					a.doc_date>='2014-03-25' and
					a.status_no='01' and
					a.application_id in('OPPN300','OPPS300') and
					a.flag<>'C' 
					$cr_date
					
			
			";*/
			if($varday=="today"){
				$cr_date=" and a.doc_date='$doc_date' ";
				$show="
					select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
					from trn_diary1 as a left join (select * from member_history where reg_date>='2014-03-25' ) as b 
	
					on a.member_id=b.member_no
					where
						a.doc_date>='2018-05-01' and
						a.status_no='01' and
						a.application_id in('OPPN300','OPPS300','OPPH300','OPPL300','OPMGMC300','OPMGMI300','OPID300','OPPLI300','OPPLC300','OPPHI300','OPLID300','OPPGI300','OPDTAC300','OPKTC300','OPTRUE300') and
						a.flag<>'C' 
						$cr_date
						
						union all
						select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
						from trn_diary1 as a left join (select * from member_history where reg_date>='2014-03-25' ) as b 
		
						on a.member_id=b.member_no
						where
							a.doc_date>='2018-05-01' and
							a.status_no='01' and
							a.application_id like 'OPN%' and
							a.flag<>'C' 
							$cr_date						
				
				";
				//echo $show;
				$run=mysql_query($show,$conn_my);
			}else if($varday=="day3"){
				$cr_date=" and a.doc_date between '$doc_date' - interval 3 day and '$doc_date' ";
				$show="
					select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
					from trn_diary1 as a left join (select * from member_history where reg_date>='2018-05-01' ) as b 
	
					on a.member_id=b.member_no
					where
						a.doc_date>='2014-03-25' and
						a.status_no='01' and
						a.application_id in('OPPN300','OPPS300','OPPH300','OPPL300','OPMGMC300','OPMGMI300','OPID300','OPPLI300','OPPLC300','OPPHI300','OPLID300','OPPGI300','OPDTAC300','OPKTC300','OPTRUE300') and
						a.flag<>'C' 
						$cr_date
						
						union all
						select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
						from trn_diary1 as a left join (select * from member_history where reg_date>='2018-05-01' ) as b 
	
						on a.member_id=b.member_no
						where
						a.doc_date>='2018-05-01' and
						a.status_no='01' and
						a.application_id like 'OPN%' and
						a.flag<>'C' 
						$cr_date
				
				";		
				$run=mysql_query($show,$conn_my);		
			}else if($varday=="month3"){
				$cr_date=" and a.doc_date between '$doc_date' - interval 90 day and '$doc_date'";
				$show="
					select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
					from trn_diary1 as a left join (select * from member_history where reg_date>='2018-05-01' ) as b 
	
					on a.member_id=b.member_no
					where
						a.doc_date>='2018-05-01' and
						a.status_no='01' and
						a.application_id in('OPPN300','OPPS300','OPPH300','OPPL300','OPMGMC300','OPMGMI300','OPID300','OPPLI300','OPPLC300','OPPHI300','OPLID300','OPPGI300','OPDTAC300','OPKTC300','OPTRUE300') and
						a.flag<>'C' 
						$cr_date
						
						union all
						
					select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
					from trn_diary1 as a left join (select * from member_history where reg_date>='2018-05-01' ) as b 
	
					on a.member_id=b.member_no
					where
						a.doc_date>='2018-05-01' and
						a.status_no='01' and
						a.application_id like 'OPN%' and
						a.flag<>'C' 
						$cr_date						
						
				
				";		
				$run=mysql_query($show,$conn_my);		
				
			}else if($varday=="finish"){
				$cr_date=" and if(b.member_no is null,'N','Y')='Y' ";
				$show="
					select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
					from trn_diary1 as a left join (select * from member_history where reg_date>='2018-05-01' ) as b 
	
					on a.member_id=b.member_no
					where
						a.doc_date>='2018-05-01' and
						a.doc_date 	BETWEEN '$doc_date' - INTERVAL 15 DAY AND '$doc_date' and
						a.status_no='01' and
						a.application_id in('OPPN300','OPPS300','OPPH300','OPPL300','OPMGMC300','OPMGMI300','OPID300','OPPLI300','OPPLC300','OPPHI300','OPLID300','OPPGI300','OPDTAC300','OPKTC300','OPTRUE300') and
						a.flag<>'C' 
						$cr_date
						
						union all
						
					select a.*,if(b.member_no is null,'N','Y') as member_finish,b.sendtoserver_status,b.sendtoserver_date,b.sendtoserver_time
					from trn_diary1 as a left join (select * from member_history where reg_date>='2018-05-01' ) as b 
	
					on a.member_id=b.member_no
					where
						a.doc_date>='2018-05-01' and
						a.doc_date 	BETWEEN '$doc_date' - INTERVAL 15 DAY AND '$doc_date' and
						a.status_no='01' and
						a.application_id like 'OPN%' and
						a.flag<>'C' 
						$cr_date						
						
				
				";		
				//echo $show;
				$run=mysql_query($show,$conn_my);		
				
			}else if($varday=="wait"){
				$cr_date=" and b.member_no is null ";
				$show="
						
						SELECT a.*, if(b.member_no is null,'N','Y') as member_finish,'' as sendtoserver_status,'' as sendtoserver_date,'' as sendtoserver_time
						FROM trn_diary1 AS a
						LEFT JOIN member_history AS b ON a.member_id = b.member_no
						WHERE 
						a.doc_date 	BETWEEN '$doc_date' - INTERVAL 15 DAY AND '$doc_date'  + interval 10 day 
						AND a.branch_id='$branch_id' 
						AND a.flag <> 'C'
						AND a.application_id
						IN ('OPPN300','OPPS300','OPPH300','OPPL300','OPMGMC300','OPMGMI300','OPID300','OPPLI300','OPPLC300','OPPHI300','OPLID300','OPPGI300','OPDTAC300','OPKTC300','OPTRUE300')
						AND b.member_no IS NULL
						
						union all
						
						SELECT a.*, if(b.member_no is null,'N','Y') as member_finish,'' as sendtoserver_status,'' as sendtoserver_date,'' as sendtoserver_time
						FROM trn_diary1 AS a
						LEFT JOIN member_history AS b ON a.member_id = b.member_no
						WHERE 
						a.doc_date 	BETWEEN '$doc_date' - INTERVAL 15 DAY AND '$doc_date'  + interval 10 day 
						AND a.branch_id='$branch_id' 
						AND a.flag <> 'C'
						AND a.application_id like 'OPN%'
						AND b.member_no IS NULL
				
				";		
				//echo $show;
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online) or die("Connect Online Error");
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");
				mysql_select_db($this->db_online);
				$run=mysql_query($show,$conn_online);
				
			}else if($varday=="warning"){
				$cr_date=" and b.member_no is null ";
				$show="
						
						SELECT a.*, if(b.member_no is null,'N','Y') as member_finish,'' as sendtoserver_status,'' as sendtoserver_date,'' as sendtoserver_time
						FROM trn_diary1 AS a
						LEFT JOIN member_history AS b ON a.member_id = b.member_no
						WHERE 
						a.doc_date  between  '$doc_date'-interval 5 day and '$doc_date'-interval 3 day 
						AND a.branch_id='$branch_id' 
						AND a.flag <> 'C'
						AND a.application_id
						IN ('OPPN300','OPPS300','OPPH300','OPPL300','OPMGMC300','OPMGMI300','OPID300','OPPLI300','OPPLC300','OPPHI300','OPLID300','OPPGI300','OPDTAC300','OPKTC300','OPTRUE300')
						
						union all
						
						SELECT a.*, if(b.member_no is null,'N','Y') as member_finish,'' as sendtoserver_status,'' as sendtoserver_date,'' as sendtoserver_time
						FROM trn_diary1 AS a
						LEFT JOIN member_history AS b ON a.member_id = b.member_no
						WHERE 
						a.doc_date  between  '$doc_date'-interval 5 day and '$doc_date'-interval 3 day 
						AND a.branch_id='$branch_id' 
						AND a.flag <> 'C'
						AND a.application_id like 'OPN%'
						
				
				";		
				//echo $show;
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online) or die("Connect Online Error");
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");
				mysql_select_db($this->db_online);
				$run=mysql_query($show,$conn_online);
				
			}
			
						

			$run=mysql_query($show,$conn_my);
			
			$xarray=array();
			$i=0;
			


			
						
			 while($data=mysql_fetch_assoc($run)){
			 	$member_finish=$data['member_finish'];
				
				
			 	if($member_finish=="N"){
					
					$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online) or die("Connect Online Error");
					mysql_query("SET character_set_results=utf8");
					mysql_query("SET character_set_client=utf8");
					mysql_query("SET character_set_connection=utf8");
			
			
					$chk_ji="select * from member_history where member_no='$data[member_id]' limit 1 ";
					mysql_select_db($this->db_online);
					$run_chk_ji=mysql_query($chk_ji,$conn_online);
					$rows_chk_ji=mysql_num_rows($run_chk_ji);
					if($rows_chk_ji>0){
						$member_finish="Y";
					}
					
				}
				

				
				
			 	$xarray[$i]['doc_date']=$data['doc_date'];
				$xarray[$i]['doc_time']=$data['doc_time'];
			 	$xarray[$i]['doc_no']=$data['doc_no'];
			 	$xarray[$i]['branch_id']=$data['branch_id'];
			 	$xarray[$i]['member_id']=$data['member_id'];
				$xarray[$i]['idcard']=$data['idcard'];
				$xarray[$i]['mobile_no']=$data['mobile_no'];
				$xarray[$i]['special_day']=$data['special_day'];
				$xarray[$i]['application_id']=$data['application_id'];
			 	$xarray[$i]['member_finish']=$member_finish;
				
				$conn_my=mysql_connect($this->sv_my,$this->user_my,$this->pass_my) or die("Connect Localhost Error");
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");
				mysql_select_db($this->db_my);
				$find_send="select * from member_history where member_no='$data[member_id]' limit 1 ";
				$run_findsend=mysql_query($find_send,$conn_my);
				$datasend=mysql_fetch_array($run_findsend);
				
				$xarray[$i]['sendtoserver_status']=$datasend['sendtoserver_status'];
				$xarray[$i]['sendtoserver_date']=$datasend['sendtoserver_date'];
				$xarray[$i]['sendtoserver_time']=$datasend['sendtoserver_time'];
				
				$i++;
			 }



			 
			 
			 
			return $xarray;
	   	
		
	   }
}

?>