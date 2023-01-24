<?php 
class Model_User{
	//------------------------------------------------------------------
	function treecom_country(){
		$session = new Zend_Session_Namespace('empprofile');
		$empprofile=$session->empprofile; 
		
		$objConf=new Model_Mydbpos();	
		$sql_update="
		UPDATE  conf_employee
		INNER JOIN conf_usergroup
		ON conf_employee.group_id= conf_usergroup.group_id
		AND conf_usergroup.user_level='0'
		SET
		conf_employee.user_level=conf_usergroup.user_level 
		";
		$objConf->runsql($sql_update);
		
		
		if(!empty($empprofile)){
			$country_id=$empprofile['country_id'];
			$corporation_id=$empprofile['corporation_id'];
			$company_id=$empprofile['company_id']; 
			$branch_no=$empprofile['branch_no'];
			$branch_id=$empprofile['branch_id']; 
			$group_id=$empprofile['group_id']; 
			$sql=" SELECT * FROM com_country WHERE country_id='$country_id' ";
		}else{
			$sql=" SELECT * FROM com_country WHERE 1 ";
		}

		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){ 
			$id=$data['id'];
			$country_id=$data['country_id'];
			$countchild=$this->checkHavechildNode('com_company','country_id',$country_id);  
			if($countchild > 0){
				$oder=" ORDER BY `corporation_id` ASC ";
				//$arr=array();
				$arr=$this->treecom_company($country_id,$oder);
				//closed
				array_push($rows,array(
					'id'=>"country_id@$country_id",
					'text'=>$country_id,
					'state'=>'open',
					'children' =>$arr
				));
			}else{
				array_push($rows,array(
					'id'=>"country_id@$country_id",
					'text'=>$country_id
				));
			}
		}
	   return json_encode($rows);
	}	
//-------------------------------------------------------------------
	function treecom_company($country_id,$oder){
		$session = new Zend_Session_Namespace('empprofile');
		$empprofile=$session->empprofile; 
		if(!empty($empprofile)){
			$country_id=$empprofile['country_id'];
			$corporation_id=$empprofile['corporation_id'];
			$company_id=$empprofile['company_id']; 
			$branch_no=$empprofile['branch_no'];
			$branch_id=$empprofile['branch_id']; 
			$group_id=$empprofile['group_id']; 
			$sql="
			 SELECT * FROM com_company
			 WHERE 
			 country_id='$country_id' 
			 AND company_id='$company_id' 
			AND corporation_id='$corporation_id'
			 $oder   ";
		}else{
			$sql=" SELECT * FROM com_company WHERE country_id='$country_id' $oder ";
		}
		
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){ 	
			$id=$data['id'];
			$corporation_id=$data['corporation_id'];
			$company_id=$data['company_id'];
			$countchild=$this->checkHavechildNode('conf_usergroup','company_id',$company_id);
			if($countchild > 0){
					//$arr=array();
					$arr=$this->conf_usergroup($corporation_id,$company_id);
					array_push($rows,array(
						'id'=>"corporation_id@$corporation_id@$company_id",
						'text'=>$company_id,
						'state'=>'open',
						'children' =>$arr
					));
			}else{
					array_push($rows,array(
						'id'=>"corporation_id@$corporation_id@$company_id",
						'text'=>$company_id
					));
			}
		}
	   return $rows;
	}
	//-------------------------------------------------------------------	
	function conf_usergroup($corporation_id,$company_id){
		$objConf=new Model_Mydbpos();
		$session = new Zend_Session_Namespace('empprofile');
		$empprofile=$session->empprofile; 
		if(!empty($empprofile)){
			if($empprofile['user_id']!='' && $empprofile['employee_id']!='' ){
			     $user_level=$empprofile['user_level'];
			}
		}
		//print_r($empprofile);
		
		if(empty($empprofile)){
			$session = new Zend_Session_Namespace('myprofile');
			$empprofile=$session->myprofile; 
			if($empprofile['user_id']!='' && $empprofile['employee_id']!='' ){
			    $user_level=$empprofile['user_level'];
			}
		}
		
		

		$arr=array();
		$sql="
		SELECT * FROM conf_usergroup 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id' 
		AND user_level<='$user_level'";
		//echo"$sql";
		$res=$objConf->fetchAllrows($sql);
	    foreach($res as $data){
				$group_id=$data['group_id'];
				$cancel=$data['cancel'];
				$remark=$data['remark'];

				$d="group_id@$corporation_id@$company_id@$group_id@$remark";
				
	    		array_push($arr,array(
	    		'id'=>$d,
	    		'text'=>$group_id));
	    }
		return $arr;
	}	


	function checkdupusertogroup($corporation_id,$company_id,$employee_id,$user_id){
		$objConf=new Model_Mydbpos();
	
		$sql=" 
		SELECT * FROM conf_employee 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id'
		AND employee_id='$employee_id'";
		$res=$objConf->fetchAllrows($sql);
  		$c = count($res);
	   	if($c > 0)return 'employee_id นี้มีอยู่แล้ว';
	  
	   	$sql=" 
		SELECT * FROM conf_employee 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id'
		AND user_id='$user_id'";
		$res=$objConf->fetchAllrows($sql);
  		$c = count($res);
	   	if($c > 0)return 'user_id นี้มีอยู่แล้ว';
	  
	   	$sql=" 
		SELECT * FROM conf_employee 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id'
		AND employee_id='$employee_id'
		AND user_id='$user_id'";
		$res=$objConf->fetchAllrows($sql);
  		$c = count($res);
	   	if($c > 0)return 'employee_id และ user_id นี้มีอยู่แล้ว';
	   	
	   	return 'ok';

	}	

	//-------------------------------------------------------------------	
	function checkdupgroup($corporation_id,$company_id,$group_id){
		$objConf=new Model_Mydbpos();
		$sql=" 
		SELECT * FROM conf_usergroup 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id'
		AND group_id='$group_id'";
		$res=$objConf->fetchAllrows($sql);
  		$c = count($res);
	   	if($c > 0)return 'group_id นี้มีอยู่แล้ว';
		return 'ok';
	}
	//-------------------------------------------------------------------	
	function childNode($table,$field,$val,$oder,$f1,$f2){
		$objConf=new Model_Mydbpos();
		$arr=array();
		if(!empty($table)){
			$sql=" SELECT * FROM $table WHERE $field='$val' ";
			if(!empty($oder)){$sql.=" $oder ";}
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
					$arrreturn=array('id'=>$data[$f1],'text'=>$data[$f2]);
		    		array_push($arr,$arrreturn);
		    }
		}
	   return $arr;
	}
		//-------------------------------------------------------------------
	function checkHavechildNode($table,$field,$val){
		if(!empty($table)) {
		    $sql=" SELECT * FROM $table WHERE $field='$val'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		  	$c = count($res);
		   	if($c < 1)return 0;
		   	return $c;
		}
	}	
	//-------------------------------------------------------------------
	function  checkempdup($employee_id,$user_id,$password_id){
		$sql=" SELECT * FROM conf_employee WHERE employee_id='$employee_id' ";
		$objConf=new Model_Mydbpos();
		$c=$objConf->checkNumrows($sql);
	
		if($c==0){
			$sql0=" 
			SELECT * FROM conf_employee 
			WHERE user_id='$user_id' 
			AND password_id='$password_id'";
			$c0=$objConf->checkNumrows($sql0);
			if($c==0){
				return "OK";
			}else{
				return "user และ  password_id นี้มีอยู่แล้ว  ท่านต้องการบันทึกใช่หรือไม่";
			}
		}else{
			return "ระหัสพนักงาน  นี้มีอยู่แล้ว  ท่านต้องการบันทึกเปลี่ยนแปลงใช่หรือไม่";
		}
	}
	

	//-------------------------------------------------------------------
	function  cancelgroup($group_id,$corporation_id,$company_id){
		$objConf=new Model_Mydbpos();

		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		
		$objConf=new Model_Mydbpos();
		$sql=" 
		UPDATE `conf_usergroup` 
		SET `cancel` = '0',
		upd_date = CURDATE(),
		upd_time =CURTIME(),
		upd_user='$user_id' 
		WHERE group_id = '$group_id'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->runsql($sql);
    	
    	$sql0=" 
		UPDATE `conf_employee` 
		SET `cancel` = '0',
		upd_date = CURDATE(),
		upd_time =CURTIME(),
		upd_user='$user_id' 
		WHERE group_id = '$group_id'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->runsql($sql0);
	}
	//-------------------------------------------------------------------
	function  activegroup($group_id,$corporation_id,$company_id){
		$objConf=new Model_Mydbpos();
		
		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		
		$sql=" 
		UPDATE `conf_usergroup` 
		SET `cancel` = '1',
		upd_date = CURDATE(),
		upd_time =CURTIME(),
		upd_user='$user_id' 
		WHERE group_id = '$group_id'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->runsql($sql);
    	
    	$sql0=" 
		UPDATE `conf_employee` 
		SET `cancel` = '1',
		upd_date = CURDATE(),
		upd_time =CURTIME(),
		upd_user='$user_id' 
		WHERE group_id = '$group_id'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->runsql($sql0);
	}
	//-------------------------------------------------------------------
	function  corporation_id(){
		$sql=" SELECT corporation_id FROM com_company WHERE  1 ";
		$objConf=new Model_Mydbpos();
    	return $objConf->fetchAllrows($sql);
	}
	//-------------------------------------------------------------------
	function  com_permission(){
		$sql=" SELECT perm_id FROM com_permission  WHERE  1 ";
		$objConf=new Model_Mydbpos();
    	return $objConf->fetchAllrows($sql);
	}
	//-------------------------------------------------------------------
	function  getusergroup($corporation_id,$company_id){
		$session = new Zend_Session_Namespace('empprofile');
		$empprofile=$session->empprofile; 
		$user_level=1;
		if(!empty($empprofile)){
			if($empprofile['user_id']!='' && $empprofile['employee_id']!='' ){
			     $user_level=$empprofile['user_level'];
			}
		}
		
		$sql=" SELECT group_id FROM conf_usergroup 
		WHERE  corporation_id='$corporation_id'
		AND company_id='$company_id' 
		AND user_level <= '$user_level' ";
		$objConf=new Model_Mydbpos();
    	return $objConf->fetchAllrows($sql);
	}
	//-------------------------------------------------------------------
	function  getdata($table_manq,$fild,$val,$orderby){
		if($table_manq=="")return;
		$sql=" SELECT * FROM $table_manq WHERE  1 ";
		if(!empty($fild)){$sql.="  AND  $fild='$val' ";}
		if(!empty($orderby)){$sql.="  ORDER BY `$orderby` DESC ";}
		$objConf=new Model_Mydbpos();
    	return $objConf->deldata($sql);
	}
	//-------------------------------------------------------------------
	function  delgroup($corporation_id,$company_id,$group_id){
		$objConf=new Model_Mydbpos();

		$sql="DELETE FROM `conf_usergroup` 
		WHERE group_id = '$group_id'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->deldata($sql);
    
    	$sql0="DELETE FROM `conf_employee` 
    	WHERE group_id = '$group_id' 
    	AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->deldata($sql0);
    	
    	$sql1="DELETE FROM `conf_permission_access` 
    	WHERE 
    	group_id = '$group_id'
    	AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->deldata($sql1);			
	}	
	//-------------------------------------------------------------------
	function  deluser($id){
		$objConf=new Model_Mydbpos();
		$sql="DELETE FROM `conf_employee` WHERE `id` = '$id' ";
    	$objConf->deldata($sql);
	}	
	
	//-------------------------------------------------------------------
	function  conf_employee_update(){
		$objConf=new Model_Mydbpos();
		$sql="
		UPDATE  conf_employee  
		INNER JOIN com_branch
		ON com_branch.active = '1'
		SET conf_employee.branch_id  = com_branch.branch_id ";
		$objConf->runsql($sql);
	}	
	//-------------------------------------------------------------------
	function tableusergroup($corporation_id,$branch_id,$clicknode,$group_id,$table_name,$query,$qtype,$page,$rp,$sortname,$sortorder)
	{
		
		$session = new Zend_Session_Namespace('empprofile');
		$empprofile=$session->empprofile; 
		$branch_no="";
		$branch_id=""; 
		$corporation_id="";
		$company_id=""; 
		if(!empty($empprofile)){ 
			$branch_no=$empprofile['branch_no'];
			$branch_id=$empprofile['branch_id']; 
			$corporation_id=$empprofile['corporation_id'];
			$company_id=$empprofile['company_id']; 
		}
		
		if (!$sortname) $sortname = 'employee_id';
		if (!$sortorder) $sortorder = 'desc';
		$sort = " ORDER BY $sortname $sortorder ";
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;
		$start = (($page-1) * $rp);
		$limit = "LIMIT $start, $rp";
		
		
		
		
		$where = " WHERE 1  ";
		if($query != $qtype) $where= " WHERE  $qtype  LIKE '$query' ";
		if($query == $qtype) $where= " WHERE  group_id  LIKE '$query' ";
		
		if($corporation_id !="") $where.= " AND  corporation_id  LIKE '$corporation_id' ";
		if($company_id !="") $where.= " AND  company_id  LIKE '$company_id' ";
		if($group_id !="") $where.= " AND  group_id  LIKE '$group_id' ";
		if($branch_id !="" || $branch_no!="") $where.= " AND ( branch_id  LIKE '$branch_id' OR branch_id  LIKE '$branch_no') ";
		
		$sql = "SELECT * FROM $table_name  $where ";
		$sql.=" $sort $limit ";
		
		
		//echo $sql;
		//exit();

		
		
	
		$objConf=new Model_Mydbpos();
		$objConf->checkDbOnline('conf',$table_name);
		$res=$objConf->fetchAllrows($sql);
		
		$total = count($res);
		$data['page'] = $page;
		$data['total'] = $total;
		$rows=array();
		foreach($res as $result){
			 $id=$result['id'];
			 $corporation_id=$result['corporation_id'];
			 $company_id=$result['company_id'];
			 $employee_id=$result['employee_id'];
			 $user_id=$result['user_id'];
			 $password_id=$result['password_id'];
			 $name=$result['name'];
			 $surname=$result['surname'];
			 $position=$result['position'];
			 $end_date=$result['end_date'];
			 $cancel=$result['cancel'];
			 $branch_id=$result['branch_id'];
			 
			if($cancel==1){
				$title='ปกติ';
				$imgstatus="&nbsp;<img src='/pos/plugin/jquery-easyui-1.2.5/themes/icons/ok.png'  
		 		title='$title'  width='16' height='16' style='cursor:pointer'  >";
			}else{
				$title='ระงับ';
				$imgstatus="&nbsp;<img src='/pos/plugin/jquery-easyui-1.2.5/themes/icons/no.png'  
		 		title='$title'  width='16' height='16' style='cursor:pointer' >";
			}

			$title='แก้ไข';
	 		$imgEdit="
	 		<button type='button'  name='empform'  onclick='empformedit($id)'  style='cursor:pointer'>$title
	 		<img src='/pos/plugin/jquery-easyui-1.2.5/themes/icons/pencil.png' width='16' height='16' alt=''/>
	 		</button>";
			
			
	 		$title='ลบ';
	 		$imgDel="
	 		<button type='button'  name='logout'  onclick='deluser($id)'  style='cursor:pointer'>$title
	 		<img src='/pos/plugin/jquery-easyui-1.2.5/themes/icons/cancel.png' width='16' height='16' alt=''/>
	 		</button>";
			
	 		$rows[] = array(
				"id" => $id,
				"cell" => array(
						"$corporation_id"
	 					,"$branch_id"
						,"$employee_id"
						,"$name"
						,"$surname"
						,"$user_id"
						,"$imgstatus"
						,"$imgDel$imgEdit"
				)
			);
	    }
		$data['rows'] = $rows;
		echo json_encode($data);                                                      
	}
	//-------------------------------------------------------------------
	function empform($id){
		$rows=array();
		if(!empty($id)){
			$sql=" SELECT * FROM conf_employee WHERE id='$id' ";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
				$corporation_id = $data['corporation_id'];
			    $company_id = $data['company_id'];
				$employee_id = $data['employee_id'];
				$user_id = $data['user_id'];
				$password_id = $data['password_id'];
		    	$group_id = $data['group_id'];
		    	$name = $data['name'];
			    $surname = $data['surname'];
				$position = $data['position'];
				$start_date = $data['start_date'];
				$end_date = $data['end_date'];
				$cancel = $data['cancel'];
				$remark = $data['remark'];
				$branch_id = $data['branch_id'];
				if(empty($branch_id)){
					$branch_id=$company_id.'0001';
				}
				
				
		    	$rows=array(
					'corporation_id' => $corporation_id,
				    'company_id' => $company_id,
		    	  	'branch_id' => $branch_id,
					'employee_id' => $employee_id,
					'user_id' => $user_id,
					'password_id' => $password_id,
			    	'group_id' => $group_id,
			    	'name' => $name,
				    'surname' => $surname,
					'position' => $position,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'cancel' => $cancel,
					'remark' => $remark
		    	);
		    }
		}
	return json_encode($rows);
	}
	//-------------------------------------------------------------------
	function editgroup($corporation_id,$company_id,$group_id){
		$rows=array();
		if(!empty($group_id)){
			$sql=" 
			SELECT * FROM conf_usergroup 
			WHERE group_id='$group_id'
			 AND  corporation_id  LIKE '$corporation_id'
			 AND  company_id  LIKE '$company_id' ";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
		    	$id = $data['id'];
				$corporation_id = $data['corporation_id'];
			    $company_id = $data['company_id'];
				$group_id = $data['group_id'];
				$remark = $data['remark'];
				$cancel = $data['cancel'];
				
				
		    	$rows=array(
			    	'id' => $id,
					'corporation_id' => $corporation_id,
				    'company_id' => $company_id,
					'group_id' => $group_id,
			    	'cancel' => $cancel,
					'remark' => $remark
		    	);
		    }
		}
	return json_encode($rows);
	}
	//-------------------------------------------------------------------
		
}
?>