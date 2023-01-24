<?php class Model_Adminlogin{
	//-----------------------------------------------------------
	//checkuser($user_id,$password_id,$env_id)
	//function checkuser($user_id,$password_id,$env_id){
	function checkuser($env_id,$password_id,$user_id){
		$objConf=new Model_Mydbpos();
		
	    $sql="
		SELECT * FROM conf_employee  
		WHERE 
		 user_id='$user_id' AND
		 password_id='$password_id'";
		
		
		//$objConf->checkDbOnline('conf','conf_employee');
		$numrows=$objConf->checkNumrows($sql);
		if(!empty($numrows)){
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $arr){
				$corporation_id=$arr['corporation_id'];
				$company_id=$arr['company_id'];
				$group_id=$arr['group_id'];
				$cancel=$arr['cancel'];
				$user_level=$arr['user_level'];
				$branch_id=$arr['branch_id'];
				if($cancel!='1'){
					$mag= "สถานะการเข้าใช้งานของท่านถูกระงับกรุณาติดต่อผู้ดูและระบบ";
					return $mag;
				}
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
				$sql_access="
				SELECT * 
				FROM conf_permission_access  
				WHERE  group_id='$group_id' 
				AND corporation_id='$corporation_id'
				AND company_id='$company_id'";
				$re_pre=$objConf->fetchAllrows($sql_access);
				$perm_id=$re_pre[0]['perm_id'];
				if($perm_id==''){
					$mag= "ท่านไม่มีสิทธิ์เข้าใช้งานระบบ  กรุณาติดต่อผู้ดูแลระบบ !";
					return $mag;
				}
				
		
				$sql0="SELECT * FROM com_company WHERE  company_id='$company_id' ";
				$res0=$objConf->fetchAllrows($sql0);
				/*
				$sql1="SELECT * FROM com_branch WHERE  company_id='$company_id' ";
				$res1=$objConf->fetchAllrows($sql1);
				*/
				$sql2="SELECT * FROM com_system WHERE  1 ";
				$res2=$objConf->fetchAllrows($sql2);
				
				$myprofile=array(
					'corporation_id'=>$res0[0]['corporation_id'],
					'company_id'=>$res0[0]['company_id'],
					'employee_id'=>$arr['employee_id'],
					'user_id'=>$arr['user_id'],
					'group_id'=>$arr['group_id'],
					'perm_id'=>$perm_id,
					'env_id'=>$env_id,
					'user_level'=>$user_level,
				
				);
				$session = new Zend_Session_Namespace('myprofile');
				$session->myprofile = $myprofile;
				
				$data=array(
					'corporation_id'=>$arr['corporation_id'],
					'company_id'=>$arr['company_id'],
					'employee_id'=>$arr['employee_id'], 
					'user_id'=>$arr['user_id'], 
					'group_id'=>$arr['group_id'],
					'logintime'=>date("Y-m-d H:i:s")
				);
				$table='login_profile';
				$idlogin=$objConf->insertdata($table,$data);
				$session = new Zend_Session_Namespace('idlogin');
				$session->idlogin = $idlogin;
				$mag= "OK";
				return $mag;
				
			}
		}else{
			$mag= "ระหัสผ่านไม่ถูกต้อง";
			return $mag;
		}
	}
	//----------------------------------------------------------
		function login_for(){
    			$sql="SELECT * FROM  com_environment WHERE active='1' ORDER BY `order_by` ASC ";
    			$objConf=new Model_Mydbpos();
				$res=$objConf->fetchAllrows($sql);
				$content="<select  name='env_id' class='styledselect' id='env_id'>";
				foreach($res as $arr){
					$env_id=$arr['env_id'];
     			 	$content.="<option value='$env_id'>$env_id</option>";
				}
				$content.="</select>";
				return $content;
		}
	//----------------------------------------------------------
	
	//------------------ check user finger 
	function check_user_finger($user_id){
		$sql="
		SELECT * FROM conf_employee
		WHERE
		user_id='$user_id'";
		$this->db=Zend_Registry::get('dbOfline');
		$res=$this->db->fetchRow($sql);
		if(count($res) > 0){
			$password_id 	= $res['password_id'];
			$user_id 		= $res['user_id'];
		}else{
			$password_id 		="";
			$user_id			="";
		}
		return array($user_id,$password_id);
		
	}
	
}