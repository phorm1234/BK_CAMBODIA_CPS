<?php 
class Model_LoginModel{
	/**
	 * @desc check lock unlock finger scan
	 * @create 15052017
	 * @return Char $status_lock
	 */
	function checkLogFingerScan(){
		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_pos_config");
		$sql_docdate="SELECT* FROM com_doc_date WHERE 1";
		$row_docdate=$this->db->fetchAll($sql_docdate);
		$doc_date=$row_docdate[0]['doc_date'];
		$status_lock='N';
		$sql_chk1="SELECT default_status,condition_status
		FROM com_pos_config
		WHERE
		code_type='LOCK_FINGER_SCAN' AND
		'$doc_date' BETWEEN start_date AND end_date AND
		CURTIME() BETWEEN start_time AND end_time";
		//echo $sql_chk1;
		$row_chk1=$this->db->fetchAll($sql_chk1);
		if(count($row_chk1)>0){
			if($row_chk1[0]['condition_status']=='Y'){
				$status_lock='Y';//lock
			}
		}else{
			$sql_chk2="SELECT default_status
								FROM com_pos_config
									WHERE code_type='LOCK_FINGER_SCAN'";
			$row_chk2=$this->db->fetchAll($sql_chk2);
			if(count($row_chk2)>0){
				if($row_chk2[0]['default_status']=='Y'){
					$status_lock='Y';//lock
				}
			}
		}
		return $status_lock;
	}//func
//-----------------------------------------------------------
function chk_version(){
	$objConf=new Model_Mydbpos();
	$objConf->checkDbOnline('com','com_version_pos');

	$sql="SELECT * FROM com_version_pos  
	WHERE  version_use = version_cur ";
	
	$numrows=$objConf->checkNumrows($sql);
	if(empty($numrows)){
	 	return 'update';
	}else{
 		return 'noupdate';
	}
}
//-----------------------------------------------------------
function checkip_regis(){
	$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
	$objConf=new Model_Mydbpos();
	$objConf->checkDbOnline('conf','conf_employee');
	$sql="SELECT * FROM conf_login WHERE  ip_regis = '$REMOTE_ADDR' ORDER BY `id` DESC LIMIT 0,1 ";
	//echo "$sql";
	
	$numrows=$objConf->checkNumrows($sql);
	$res=$objConf->fetchAllrows($sql);
	$rows=array();
	foreach($res as $data){
		$user_id = $data['user_id'];
		$password_id = $data['password_id'];
		$rows=array(
		    	"num"=>$numrows,
		    	"user_id"=>$user_id,
		    	"password_id"=>$password_id
		);
	}
	return json_encode($rows);
}
//-------------------------------------------------------------------
function checkkeylogin(){
	$objConf=new Model_Mydbpos();
	//start_date end_date
	$objConf->checkDbOnline('com','com_pos_config');
	$sql0="
    SELECT doc_date  FROM  com_doc_date  
    WHERE  1 ORDER BY `id` DESC ";
	$run=$objConf->fetchAllrows($sql0);
	foreach($run as $arrf){
		$doc_date=$arrf['doc_date'];
	}
	
    $sql1="
    SELECT * FROM  com_pos_config 
    WHERE  code_type='NO_KEYIN_MEMBER' 
    AND '$doc_date' BETWEEN start_date AND end_date 
    AND  CURTIME() BETWEEN start_time AND end_time ";
    
   //echo $sql1;
    
	$run1=$objConf->fetchAllrows($sql1);
	$c=count($run1);
	$condition_status="";
	if($c>0){
		foreach($run1 as $arrf){
			$condition_status=$arrf['condition_status'];
		}
	}else{
		    $sql2="
		    SELECT * FROM  com_pos_config 
		    WHERE  code_type='NO_KEYIN_MEMBER'";
			$run2=$objConf->fetchAllrows($sql2);
		
		foreach($run2 as $arrf){
			$condition_status=$arrf['default_status'];
		}
	}
	echo"$condition_status"; 
}
//-----------------------------------------------------------
	function checklogin($user_id,$password_id,$env_id){
		$objConf=new Model_Mydbpos();
		$objConf->checkDbOnline('conf','conf_employee');
		
		$sql="
		SELECT * FROM conf_employee  
		WHERE  user_id='$user_id' 
		AND password_id='$password_id'";
		$numrows=$objConf->checkNumrows($sql);
		if($numrows>0){
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $arr){
				$corporation_id=$arr['corporation_id'];
				$company_id=$arr['company_id'];
				$group_id=$arr['group_id'];
				$branch_id=$arr['branch_id'];
				$user_level=$arr['user_level'];		
				
				$sql_group="SELECT * 
				FROM conf_usergroup 
				WHERE  corporation_id='$corporation_id'
				AND company_id='$company_id'
				AND group_id='$group_id'";
				$res_group=$objConf->fetchAllrows($sql_group);
				if(count($res_group)<1){
					$mag= "ท่านอยู่ใน กลุ่ม User ที่ยกเลิกการใช้งานแล้ว กรุณาติดต่อผู้ดูแลระบบ !";
					return $mag;
				}
				$sql000="
				SELECT * 
				FROM com_pos_config 
				WHERE  corporation_id='$corporation_id'
				AND company_id='$company_id'
				AND branch_id='$branch_id'";
				$res000=$objConf->fetchAllrows($sql000);
				$pos_config=array();
				if(count($res000)>0){
					foreach($res000 as $arr000){
						$code_type=$arr000['code_type'];
						$default_status=$arr000['default_status'];
						$arr_confpos=array($code_type=>$default_status);
						array_push($pos_config,$arr_confpos);
					}
				}
				$session000 = new Zend_Session_Namespace('pos_config');
				$session000->pos_config = $pos_config;
				
				$cancel=$arr['cancel'];
				if($cancel!=1){
					$mag= "UserName ของท่าน ถูกระงับใช้ กรุณาติดต่อผู้ดูแลระบบ !";
					return $mag;
				}
				
				$sql_access="
				SELECT * 
				FROM conf_permission_access  
				WHERE  group_id='$group_id' 
				AND corporation_id='$corporation_id'
				AND company_id='$company_id'";
				//echo $sql_access;
				$re_pre=$objConf->fetchAllrows($sql_access);
				$c=count($re_pre);
				if($c> 0 or $c !=""){
					$perm_id=$re_pre[0]['perm_id'];
					$chk_tim_in_out=$re_pre[0]['chk_tim_in_out'];
					if($perm_id==''){
						$mag= "ท่านไม่มีสิทธิ์เข้าใช้งานระบบ  กรุณาติดต่อผู้ดูแลระบบ !";
						return $mag;
					}
				}

		
						$sql0="
						SELECT * 
						FROM com_company WHERE  
						corporation_id='$corporation_id' 
						AND company_id='$company_id' ";
						//echo $sql0;
						$res0=$objConf->fetchAllrows($sql0);
						$active=$res0[0]['active'];

						if($active!='1'){
								$mag= " UserName ของท่าน อยู่ในบริษัทที่ระงับการใช้งาน แล้ว ไม่สามารถใช้งานได้ กรุณาติดต่อผู้ดูแลระบบ !";
								return $mag;
						}
						
						$sql1="SELECT * FROM com_branch WHERE  
						corporation_id='$corporation_id' 
						AND company_id='$company_id'
						AND branch_id='$branch_id'";
						$res1=$objConf->fetchAllrows($sql1);
						//if(count($res1)>0){
						foreach($res1 as $ares1){			
							$active=$ares1['active'];
							if($active !='1'){
									$mag= " User ของท่าน อยู่ใน สาขาที่ระงับการใช้งาน แล้ว ไม่สามารถใช้งานได้ กรุณาติดต่อผู้ดูแลระบบ !";
									return $mag;
							}
						}
						//}

						$sql1_1="
						SELECT * FROM com_branch_detail 
						WHERE  
						corporation_id='$corporation_id' 
						AND company_id='$company_id'
						AND branch_id='$branch_id'";
						
						$res1_1=$objConf->fetchAllrows($sql1_1);
						
						$c=count($res1_1);
						if($c>0 or $c !=""){
								foreach($res1_1 as $ares1_1){			
											$start_date=$ares1_1['start_date'];
											$end_date=$ares1_1['end_date'];
								
											$branch_id=$ares1_1['branch_id'];
											$branch_no=$ares1_1['branch_no'];
											$branch_name=$ares1_1['branch_name'];
											$branch_name_e=$ares1_1['branch_name_e'];
										
											$com_ip=$_SERVER['REMOTE_ADDR'];
											
											$sql1_2="SELECT * FROM com_branch_computer WHERE  
											corporation_id='$corporation_id' 
											AND company_id='$company_id'
											AND branch_id='$branch_id'
											AND com_ip='$com_ip'";
											$res1_2=$objConf->fetchAllrows($sql1_2);
											
											$com_ip="";
											$lock_status="";
											$computer_no="";
											$pos_id="";
											$thermal_printer="";
											$network="";
											foreach($res1_2 as $arr1_2){
												$com_ip=$arr1_2['com_ip'];
												$lock_status=$arr1_2['lock_status'];
												$computer_no=$arr1_2['computer_no'];
												$pos_id=$arr1_2['pos_id']; 
												$thermal_printer=$arr1_2['thermal_printer'];
												$network=$arr1_2['network'];
											}
											
											$sql2="SELECT * FROM com_system WHERE   branch_id='$branch_id' ";
											$res2=$objConf->fetchAllrows($sql2);
											
											$sql00="SELECT * 
											FROM com_company 
											WHERE  corporation_id='$corporation_id'
											AND company_id='$company_id' ";
											$res00=$objConf->fetchAllrows($sql00);
											$country_id=$res00[0]['country_id'];
											
											$empprofile=array(
												'country_id'=>$country_id,
												'corporation_id'=>$corporation_id,
												'company_id'=>$company_id,
												'company_name'=>$res0[0]['company_name'], 
												'branch_name'=>$branch_name,
												'branch_name_e'=>$branch_name_e,
												'address'=>$res0[0]['address'], 
												'road'=>$res0[0]['road'], 
												'district'=>$res0[0]['district'],
												'amphur'=>$res0[0]['amphur'],
												'province'=>$res0[0]['province'],
												'postcode'=>$res0[0]['postcode'],
												'tel'=>$res0[0]['tel'], 
												'fax'=>$res0[0]['fax'], 
												'tax_id'=>$res0[0]['tax_id'],
												'active'=>$res0[0]['active'],
												'branch_no'=>$branch_no, 
												'branch_tp'=>$ares1_1['branch_tp'],
												'acc_no'=>$ares1_1['acc_no'], 
												'start_date'=>$ares1_1['start_date'],
												'end_date'=>$ares1_1['end_date'],
												'com_ip'=>$com_ip,
												'lock_status'=>$lock_status,
												'computer_no'=>$computer_no,
												'pos_id'=>$pos_id, 
												'thermal_printer'=>$thermal_printer,
												'network'=>$network,
												'name'=>$arr['name'],
											 	'surname'=>$arr['surname'],  
												'branch_id'=>$arr['branch_id'], 
												'employee_id'=>$arr['employee_id'], 
												'user_id'=>$arr['user_id'], 
												'group_id'=>$group_id,
												'env_id'=>$env_id,
												'perm_id'=>$perm_id,
												'user_level'=>$user_level,
												'logintime'=>date("Y-m-d H:i:s")
											);

								}
						}else{
								$text="";
								$empprofile=array(
								'country_id'=>$text,
								'corporation_id'=>$corporation_id,
								'company_id'=>$company_id,
								'company_name'=>$res0[0]['company_name'], 
								'branch_name'=>$text,
								'branch_name_e'=>$text,
								'address'=>$res0[0]['address'], 
								'road'=>$res0[0]['road'], 
								'district'=>$res0[0]['district'],
								'amphur'=>$res0[0]['amphur'],
								'province'=>$res0[0]['province'],
								'postcode'=>$res0[0]['postcode'],
								'tel'=>$res0[0]['tel'], 
								'fax'=>$res0[0]['fax'], 
								'tax_id'=>$res0[0]['tax_id'],
								'active'=>$res0[0]['active'],
								'branch_no'=>"0001", 
								'branch_tp'=>$text,
								'acc_no'=>$text, 
								'start_date'=>$text,
								'end_date'=>$text,
								'com_ip'=>$text,
								'lock_status'=>$text,
								'computer_no'=>$text,
								'pos_id'=>$text, 
								'thermal_printer'=>$text,
								'network'=>$text,
								'name'=>$arr['name'],
								 'surname'=>$arr['surname'],  
								'branch_id'=>$arr['branch_id'], 
								'employee_id'=>$arr['employee_id'], 
								'user_id'=>$arr['user_id'], 
								'group_id'=>$group_id,
								'env_id'=>$env_id,
								'perm_id'=>$perm_id,
								'logintime'=>date("Y-m-d H:i:s")
								);
							
						}
						
						$session = new Zend_Session_Namespace('empprofile');
						$session->empprofile = $empprofile;
						
					//-------------------------------------------------------------	
					//echo ">>$chk_tim_in_out<<";
					//exit();
					if($chk_tim_in_out =="Y"){			
						$sql_chtime="
						SELECT * FROM check_in_out  
						WHERE  cid='$user_id' 
						AND check_date=CURDATE()
						ORDER BY `check_date` DESC, 
						`check_in` DESC LIMIT 0,1 ";
						
						
						$objConf->checkDbOnline('chtime','check_in_out');
						$numrows_chtime=$objConf->checkNumrows($sql_chtime);
						
						if(!empty($numrows_chtime)){
							$res_chtime=$objConf->fetchAllrows($sql_chtime);
							foreach($res_chtime as $arr_chtime){
								$check_in_reason=$arr_chtime['check_in_reason'];
								if($check_in_reason=='2'){
									$session = new Zend_Session_Namespace('empprofile');
									$session->empprofile ="";
									$mag= "ท่านยังไม่ได้ลงเวลาเข้าใช้งานระบบ !";
									return $mag;
								}
								if($check_in_reason=='3'){
									$session = new Zend_Session_Namespace('empprofile');
									$session->empprofile ="";
									$mag= "กรุณาลงเวลาเข้าใช้งานระบบอีกครั้งหนึ่ง!";
									return $mag;
								}
							}
						}else{
							$session = new Zend_Session_Namespace('empprofile');
							$session->empprofile=array();
							$mag= "ท่านยังไม่ได้ลงเวลาเข้าใช้งานระบบ !";
							return $mag;
						}
					}	
					//-------------------------------------------------------------		
						//echo ">>$check_in_reason<<";
						/*
					$from_ip=$_SERVER['REMOTE_ADDR'];
						$data=array(
							'corporation_id'=>$arr['corporation_id'],
							'company_id'=>$arr['company_id'],
							'employee_id'=>$arr['employee_id'], 
							'user_id'=>$arr['user_id'], 
							'password_id'=>$password_id,
							'group_id'=>$arr['group_id'],
							'logintime'=>date("Y-m-d H:i:s"),
							'from_ip'=>$from_ip
						);
									
						$table='login_profile';
						$objConf->checkDbOnline('conf','conf_employee');
						$idlogin=$objConf->insertdata($table,$data);
						$session = new Zend_Session_Namespace('idlogin');
						$session->idlogin = $idlogin;
						*/
						$mag= "OK";
						return $mag;
			}
		}else{
			$mag= "ระหัสผ่านไม่ถูกต้อง";
			return $mag;
		}
	}

//----------------------------------------------------------------------------------------	
function com_environment(){
	$objConf=new Model_Mydbpos();
	$objConf->checkDbOnline('conf','conf_employee');
	$sql_even="SELECT * FROM com_environment WHERE  1 ";
	$res_sql_even=$objConf->fetchAllrows($sql_even);
}	

//----------------------------------------------------------------------------------------	
function checksentdata(){
	$objConf=new Model_Mydbpos();
	$objConf->checkDbOnline('conf','conf_employee');
	$sql_even="SELECT * FROM com_doc_date WHERE  1 ";
	$res=$objConf->fetchAllrows($sql_even);
	$today=date('Y-m-d');
	foreach($res as $arr){
		$doc_date=$arr['doc_date'];
		if($today!=$doc_date){
			return "NO";
		}else{
			return "OK";
		}
	}
}	
//----------------------------------------------------------------------------------------	


}
?>