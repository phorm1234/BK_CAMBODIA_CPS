<?php 
class Model_Permission{	
//ob_end_flush();
//------------------------------------------------------------------	
	function com_permission(){
		$rows=array();
		$sql=" SELECT perm_id,remark ,id FROM com_permission WHERE id ='1'";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		foreach($res as $data){ 	
			$id=$data['id'];
			$perm_id=$data['perm_id'];
			$remark=$data['remark'];
			$arr=array();
			$arr=$this->com_permission_list();
			array_push($rows,array(
			'id'=>"all_permission",
			'text'=>$perm_id,
			'state'=>'open',
			'children' =>$arr));
		}
   		return json_encode($rows);
	}		
//------------------------------------------------------------------
	function com_permission_list(){
		$rows=array();
		$sql=" SELECT perm_id,remark ,id FROM com_permission WHERE 1 AND id !='1' ORDER BY `perm_id` ASC ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		foreach($res as $data){ 	
			$id=$data['id'];
			$perm_id=$data['perm_id'];
			$remark=$data['remark'];
			$countchild=$this->checkHavechildNode('com_permission_menu','perm_id',$perm_id);
			if($countchild > 0){
					$arr=array();
					$arr=$this->com_permission_menu_list($perm_id);
					array_push($rows,array(
					'id'=>"com_permission@$perm_id@$remark@$id",
					'text'=>$perm_id,
					'state'=>'closed',
					'children' =>$arr));
			}else{
					array_push($rows,array(
					'id'=>"com_permission@$perm_id@$remark@$id",
					'text'=>$perm_id));
			}
		}
   		return $rows;
	}
//------------------------------------------------------------------
	function com_permission_menu_list($perm_id){
		$objConf=new Model_Mydbpos();
		$rows=array();
		$arr=array();
		if(!empty($perm_id)){
			$sql="
			SELECT com_menu.menu_id,
			com_menu.menu_name,
			com_permission_menu.perm_id
			FROM com_menu  INNER JOIN com_permission_menu 
			ON   com_menu.menu_id = com_permission_menu.menu_id
			WHERE com_menu.type_menu = 'program' 
			AND com_permission_menu.perm_id = '$perm_id'";
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $data){ 
					$menu_id=$data['menu_id'];
					$menu_name=$data['menu_name'];
					$perm_id=$data['perm_id'];
					$countchild=$this->checkHavechildNode('com_menu','menu_ref',$menu_id);		
					if($countchild > 0){
						$arr=array();
						$arr=$this->getChildNodePermission($menu_id,$perm_id);
						array_push($rows,array(
						'id'=>"com_menu@$perm_id@$menu_id",
						'text'=>$menu_name,
						'state'=>'closed',
						'children' =>$arr));
					}else{
						array_push(
						$rows,array('id'=>"com_menu@$perm_id@$menu_id",
						'text'=>$menu_name));
					}
			}
		}
	   return $rows;
	}	
//------------------------------------------------------------------	
	function getChildNodePermission($menu_id,$perm_id){
		$sql="SELECT menu_id,menu_name FROM com_menu WHERE menu_ref='$menu_id' ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		   $rows=array();
			foreach($res as $data){
				$menu_id=$data['menu_id'];
				$menu_name=$data['menu_name'];
				$countchild=$this->checkHavechildNode('com_menu','menu_ref',$menu_id);		
				if($countchild > 0){
					$countinpermission=$this->ChavechildNode_per($menu_id,$perm_id);		
			    	if($countinpermission>0){
			    		$arr=array();
			    		$arr=$this->getChildNodePermission_list($menu_id,$perm_id);
						if(!empty($arr)){
							array_push($rows,array(
							'id'=>"com_menu@$perm_id@$menu_id",
							'text'=>$menu_name,
							'state'=>'closed',
							'children' =>$arr));
						}
			    	}
				}else{
						array_push(
						$rows,array(
							'id'=>"com_menu@$perm_id@$menu_id",
							'text'=>$menu_name
						));
				}
		    }
		 return $rows;   
	}
//------------------------------------------------------------------	
	function getChildNodePermission_list($menu_id,$perm_id){
 		$sql2="SELECT menu_id,menu_name FROM com_menu WHERE menu_ref='$menu_id'";
 		$objConf=new Model_Mydbpos();
		$res2=$objConf->fetchAllrows($sql2);
		$rows=array();
		foreach($res2 as $data){
			$countinpermission=$this->ChavechildNode_per($menu_id,$perm_id);
			if($countinpermission>0){
				$menu_id=$data['menu_id'];
				$menu_name=$data['menu_name'];	
				array_push(
				$rows,array(
					'id'=>"com_menu@$perm_id@$menu_id",
					'text'=>$menu_name
				));
			}
		}
		 return $rows;  
	}
//------------------------------------------------------------------	
	function ChavechildNode_per($menu_id,$perm_id){
 		$sql2=" 
    	SELECT *
    	FROM com_permission_menu
    	WHERE   perm_id='$perm_id'
    	AND menu_id='$menu_id' ";
 		$objConf=new Model_Mydbpos();
		$res2=$objConf->fetchAllrows($sql2);
		$c = count($res2);
		if($c < 1)return 0;
		return $c;
	}
//------------------------------------------------------------------
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
		if(!empty($table)){
		    $sql=" SELECT * FROM $table WHERE $field='$val'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		  	$c = count($res);
		   	if($c < 1)return 0;
		   	return $c;
		}
	}	
	//-------------------------------------------------------------------
	function  pastemenu($menu_id,$perm_id){
		//menu_id	menu15
		//perm_id	ShopMng
		$sql="SELECT * FROM com_menu WHERE `menu_id`='$menu_id' ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	    
		foreach($res as $data){
			$menu_id=$data['menu_id'];
			$menu_ref=$data['menu_ref'];
			$type_menu=$data['type_menu'];
			if($type_menu=='program' || $type_menu=='mainmenu'){
				$countchild=$this->checkHavechildNode('com_menu','menu_ref',$menu_id);  
				if($countchild > 0){
					//echo"pastemenu1 $menu_id<br>";
					$res=$this->insert_child_menu($menu_id,$perm_id);
				}
				$countchild=$this->cHaveParentInComPermission($menu_id,$perm_id);  
				if($countchild<1){
					$res=$this->insert_menu($menu_id,$perm_id);
				}
			}else{
				return;
			}
		}
	}

	function insert_child_menu($menu_id,$perm_id){
		$cHaveInComPermission=$this->cHaveParentInComPermission($menu_id,$perm_id);  
		if($cHaveInComPermission<1){
			$sql="SELECT * FROM com_menu WHERE   menu_ref='$menu_id'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    $rows=array();
			foreach($res as $data){
				$menu_id=$data['menu_id'];
				$menu_ref=$data['menu_ref'];
				$countchild=$this->checkHavechildNode('com_menu','menu_ref',$menu_id);  
				if($countchild > 0){
					//echo"pastemenu1 $menu_id<br>";
					$res=$this->insert_child_menu($menu_id,$perm_id);
				}
				$countchild=$this->cHaveParentInComPermission($menu_id,$perm_id);  
				if($countchild<1){
					//echo"pastemenu2 $menu_id<br>";
					$this->insert_menu($menu_id,$perm_id);
				}
			}
		}
		return;
	}
	function insert_menu($menu_id,$perm_id){
		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		$sql="
		INSERT INTO 
		com_permission_menu(`perm_id`, `menu_id`,  `reg_date`, `reg_time`, `reg_user`)
		SELECT '$perm_id', `menu_id`, CURDATE(), CURTIME(), '$user_id'
		FROM com_menu
		WHERE menu_id = '$menu_id'
		AND menu_level > '1'";
		$objConf=new Model_Mydbpos();
		$objConf->runsql($sql);
		return;
	}
	function cHaveParentInComPermission($menu_id,$perm_id){
		$sql=" SELECT * FROM com_permission_menu WHERE menu_id='$menu_id' AND perm_id='$perm_id'";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	  	$c = count($res);
	  	if($c < 1 || $c=='')return 0;
	   	return $c;
	}
	//--------------------------------------------------------
	function  delmenu($menu_id){
		$objConf=new Model_Mydbpos();
		$sql="SELECT * FROM com_menu WHERE  menu_id='$menu_id'";
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){
			$menu_picture=$data['menu_picture'];
			$dirname=$_SERVER['DOCUMENT_ROOT'].$data['menu_picture'];
			unlink($dirname);
			//$run= $this->full_rmdir($dirname);
		}
		
		$sql_del="DELETE FROM `com_menu` WHERE `menu_id` = '$menu_id' AND type_menu !='mainmenu' LIMIT 1";
    	return $objConf->deldata($sql_del);
	}
	
	
//--------------------------------------------------------
	function  delcommenuidnomg($menu_id){
		$objConf=new Model_Mydbpos();
		$sql="SELECT * FROM com_menu WHERE  menu_id='$menu_id' OR menu_ref='$menu_id'";
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){
			$menu_picture=$data['menu_picture'];
			$dirname=$_SERVER['DOCUMENT_ROOT'].$data['menu_picture'];
			unlink($dirname);
			//$run= $this->full_rmdir($dirname);
		}
		$sql_del="DELETE FROM `com_menu` WHERE (menu_id='$menu_id' OR menu_ref='$menu_id') AND type_menu !='mainmenu' LIMIT 1";
    	return $objConf->deldata($sql_del);
	}
//-------------------------------------------------------------------
	function  removepermission($perm_id){
		$objConf=new Model_Mydbpos();
		$sql0="
		DELETE FROM com_permission 
		WHERE   perm_id='$perm_id' AND id !='1'  ";
		$objConf->runsql($sql0);
		
		$sql1="
		DELETE FROM com_permission_menu 
		WHERE  perm_id='$perm_id' AND id !='1' ";
		$objConf->runsql($sql1);
		
		$sq2="
		DELETE FROM conf_permission_access 
		WHERE   perm_id='$perm_id' AND id !='1' ";
		$objConf->runsql($sq2);
	}
	
	function  updatepermission($perm_id,$remark,$perm_id_old){
		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		
		$objConf=new Model_Mydbpos();
		$sql0="
		UPDATE com_permission 
		SET
		perm_id='$perm_id',
		remark='$remark',
		upd_date =CURDATE(),
		upd_time =CURTIME(),
		upd_user='$user_id'
		WHERE perm_id='$perm_id_old'";
		$objConf->runsql($sql0);		
				
		$sql1="
		UPDATE conf_permission_access  
		SET
		perm_id='$perm_id',
		upd_date = CURDATE(),
		upd_time =CURTIME(),
		upd_user='$user_id'
		WHERE perm_id = '$perm_id_old'";
		$objConf->runsql($sql1);
		
		$sql2="
		UPDATE com_permission_menu  
		SET
		perm_id='$perm_id',
		upd_date=CURDATE(),
		upd_time=CURTIME(),
		upd_user='$user_id'
		WHERE perm_id = '$perm_id_old'";
		$objConf->runsql($sql2);
		
		$sql3="
		UPDATE conf_environment  
		SET
		perm_id='$perm_id',
		upd_date=CURDATE(),
		upd_time=CURTIME(),
		upd_user='$user_id'
		WHERE perm_id = '$perm_id_old'";
		$objConf->runsql($sql3);
		
	}
	
	function  addpermission($perm_id,$remark){
		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		$data = array(
			'perm_id'=>$perm_id,
			'remark'=>$remark,
			'reg_date'=>date('Y-m-d'),
			'reg_time'=>date('H:i:s'),
			'reg_user'=>$user_id
		);
		$table='com_permission';
		$objConf=new Model_Mydbpos();
		$id=$objConf->insertdata($table,$data);
		$where="id='$id'";
	}
	//-------------------------------------------------------------------
	function  delpermission($perm_id,$id){
		$objConf=new Model_Mydbpos();
		$sql="DELETE FROM com_permission  WHERE  id='$id' ";
		$objConf->runsql($sql);
		$sql="DELETE FROM com_permission_menu WHERE  perm_id='$perm_id' ";
		$objConf->runsql($sql);
	}
	//-------------------------------------------------------------------
	function  delmenuinoermission($perm_id,$menu_id){
		$objConf=new Model_Mydbpos();
		$sql="DELETE FROM com_permission_menu WHERE  perm_id='$perm_id' AND menu_id='$menu_id'";
		echo $sql;
		$objConf->runsql($sql);
	}
	//-------------------------------------------------------------------
	
	
	
	
	function  removemenuinper($menu_id,$perm_id){
		$sql=" SELECT * FROM com_menu WHERE menu_id='$menu_id' ";
		//return $sql;
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	   
		foreach($res as $data){
			$menu_id=$data['menu_id'];
			$countchild=$this->checkHavechildNode('com_menu','menu_ref',$menu_id);  
			$this->del_com_permission_menu($menu_id,$perm_id);
			if($countchild > 0){
				$this->check_del_childNode($menu_id,$perm_id);
			}
		}
	}
	
	
	function check_del_childNode($menu_id,$perm_id){
		if(!empty($menu_id)){
			$sql=" SELECT * FROM com_menu WHERE menu_ref='$menu_id'  ";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
		    	$menu_id=$data['menu_id'];
				$countchild=$this->checkHavechildNode('com_menu','menu_ref',$menu_id);  
				if($countchild > 0){
					$this->check_del_childNode($menu_id,$perm_id);
					$this->del_com_permission_menu($menu_id,$perm_id);
				}else{
					$this->del_com_permission_menu($menu_id,$perm_id);
				}
		    }
		  
		}
	}
	function del_com_permission_menu($menu_id,$perm_id){
		$sql="DELETE FROM com_permission_menu  WHERE menu_id = '$menu_id' AND perm_id='$perm_id' ";
		$objConf=new Model_Mydbpos();
		$objConf->runsql($sql);
	}

	//-------------------------------------------------------------------
	function  checkdupper($menu_id,$perm_id){
		if(!empty($menu_id)){
			$objConf=new Model_Mydbpos();
			$sql=" 
			SELECT type_menu 
			FROM com_menu 
			WHERE menu_id='$menu_id' AND menu_level > '1' ";
			$res=$objConf->fetchAllrows($sql);
	    	//$type_menu=$res[0]['type_menu'];
	
			foreach($res as $data){
		    	$type_menu=$data['type_menu'];
				if($type_menu=='file'){return 'กรุณาคลิก ที่ Node แม่';}
		    }
	    	
	    	
	    	
		    
	    	$sql0=" 
		    SELECT menu_id FROM com_permission_menu 
		    WHERE  perm_id='$perm_id' AND menu_id='$menu_id'";
			$res=$objConf->fetchAllrows($sql0);
		  	$c = count($res);
		   	if($c < 1){
		   		return 'ok';
		   	}else{
		   		return "เมนูนี้มีอยู่ใน permission $perm_id แล้ว ไม่สามารถเพิ่มได้";
		   	}
		   
		}else{
			return 'ท่านยังไม่ได้เลือกเมนู';
		}
	}

//-------------------------------------------------------------------
	function addmenusep($menu_id){
		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		$objConf=new Model_Mydbpos();
		
		$sql0="SELECT `separator` FROM com_menu  WHERE menu_id='$menu_id' ";
		$res=$objConf->fetchAllrows($sql0);
		$c=count($res);
		if($c !=''|| $c > 0){
					$sql1="
					UPDATE `com_menu` SET `separator` = '', 
					upd_date =CURDATE(),
					upd_time =CURTIME(),
					upd_user='$user_id'
					WHERE menu_id = '$menu_id' ";
		}else{
					$sql1="
					UPDATE `com_menu` SET `separator` = 'Y', 
					upd_date =CURDATE(),
					upd_time =CURTIME(),
					upd_user='$user_id'
					WHERE menu_id = '$menu_id' ";
		}
		$objConf->runsql($sql1);
	}

//-------------------------------------------------------------------
	function treesysmenu($menu_id){
		$objConf=new Model_Mydbpos();
		$sql=" SELECT * FROM com_menu WHERE menu_level='1' ";
		$state='';
		if(!empty($menu_id)){
	    	$sql=" SELECT * FROM com_menu WHERE  menu_ref='$menu_id' ";
	    	$sql0=" SELECT menu_ref,menu_level FROM com_menu WHERE menu_id='$menu_id'  ORDER BY `menu_seq` ASC ";
			$res0=$objConf->fetchAllrows($sql0);
	    	$menu_level=$res0[0]['menu_level'];
	    	if($menu_level==1){
	    		$state='closed';
	    	}else{
	    		$state='open';
	    	}
		}
		if($state==''){
			$state='open';
		}

		$sql.=" ORDER BY `menu_seq` ASC ";
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){
			$menu_id=$data['menu_id'];
			$menu_name=$data['menu_name'];
			$countchild=$this->checkH($menu_id);  
			/*
			$separator=$data['separator'];
			if($separator=='Y'){
				array_push($rows,
				array("id"=>'0000000',
					"text"=>"----------------------",
					"iconCls"=>""
					)
				);
			}
			*/
			if($countchild > 0){
				$arr=$this->childNodetreesysmenu($menu_id);
				array_push($rows,array(
				"id"=>$menu_id,
				"text"=>$menu_name,
				"state"=>$state,
				"children" =>$arr));
			}else{
				array_push($rows,array("id"=>$menu_id,'text'=>$menu_name));
			}
			
		

		}
	   return json_encode($rows);
	}
	//-------------------------------------------------------------------	
	function childNodetreesysmenu($menu_id){
		$arr=array();
		if(!empty($menu_id)){
			$sql=" SELECT * FROM com_menu WHERE menu_ref='$menu_id' ORDER BY `menu_seq` ASC ";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
		    	$menu_id=$data['menu_id'];
		    	$menu_name=$data['menu_name'];
				$countchild=$this->checkH($menu_id); 
				/*     
				$separator=$data['separator'];
			
			    $separator=$data['separator'];
				if($separator=='Y'){
					array_push($arr,
					array(
						"id"=>'0000000',
						"text"=>"----------------------",
						"iconCls"=>""
						)
					);
				}
		*/
				if($countchild > 0){//closed
					array_push($arr,array(
					"id"=>$menu_id,
					"text"=>$menu_name,
					"state"=>"closed",
					"children" =>$this->childNodetreesysmenu($menu_id)));
				}else{
			    	array_push($arr,array(
				    	'id'=>$menu_id,
				    	'text'=>$menu_name
			    	));
				}
				
		    }
		}
	   return $arr;
	}
	//-------------------------------------------------------------------
	function checkH($menu_id){
		if(!empty($menu_id)){
		    $sql=" SELECT * FROM com_menu WHERE menu_ref='$menu_id'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		  	$c = count($res);
		   	if($c < 1)return 0;
		   	return $c;
		}
	}
	
	//-------------------------------------------------------------------
	function editcommenuid($menu_id){
		$rows=array();
		if(!empty($menu_id)){
			$sql=" SELECT * FROM com_menu WHERE menu_id='$menu_id' ";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
				$menu_id = $data['menu_id'];
				$menu_name = $data['menu_name'];
				$menu_seq = $data['menu_seq'];
		    	$menu_exec = $data['menu_exec'];
		    	$menu_picture = $data['menu_picture'];
		    	if($menu_picture==""){
		    		$menu_picture="/pos/images/ricons/CalendarAlt.png";
		    	}
			    $menu_level = $data['menu_level'];
				$menu_ref = $data['menu_ref'];
				$status_menu = $data['status_menu'];
				$type_menu = $data['type_menu'];
		    	
		
		    	$rows=array(
		    	"menu_id"=>$menu_id,
		    	"menu_name"=>$menu_name,
		    	"menu_seq"=>$menu_seq,
		    	"menu_exec"=>$menu_exec,
		    	"menu_picture"=>$menu_picture,
		    	"menu_level"=>$menu_level,
		    	"menu_ref"=>$menu_ref,
		    	"status_menu"=>$status_menu,
		    	"type_menu"=>$type_menu);
		    }
		}
		return json_encode($rows);
	}
	//-------------------------------------------------------------------	
	function treeusergroup(){
		$sql=" SELECT * FROM com_country WHERE 1 ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){ 
			$id=$data['id'];
			$country_id=$data['country_id'];
			$countchild=$this->checkHavechildNode('com_company','country_id',$country_id);  
			if($countchild > 0){
				$oder=" ORDER BY `corporation_id` ASC ";
				$arr=array();
				$arr=$this->treeUserCompany($country_id,$oder);
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
	function treeUserCompany($country_id,$oder){
		$sql=" SELECT * FROM com_company WHERE country_id='$country_id' $oder ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){ 	
			$id=$data['id'];
			$corporation_id=$data['corporation_id'];
			$company_id=$data['company_id'];
			$countchild=$this->checkHavechildNode('conf_usergroup','company_id',$company_id);
			if($countchild > 0){
					$arr=array();
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
		$sql=" 
		SELECT id,corporation_id ,company_id,group_id,cancel,remark
		FROM conf_usergroup 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id' ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){ 	
			$id=$data['id'];
			$corporation_id=$data['corporation_id'];
			$company_id=$data['company_id'];
			$group_id=$data['group_id'];
			$cancel=$data['cancel'];
			$remark=$data['remark'];
			
			$countchild=$this->checkHavechildNode('conf_permission_access','group_id',$group_id);
			if($countchild > 0){
					$arr=array();
					$arr=$this->conf_permission_access($corporation_id,$company_id,$group_id);
					array_push($rows,array(
						'id'=>"conf_usergroup@$id@$corporation_id@$company_id@$group_id@$cancel@$remark",
						'text'=>$group_id,
						'state'=>'closed',
						'children' =>$arr
					));
			}else{
				array_push($rows,array(
					'id'=>"conf_usergroup@$id@$corporation_id@$company_id@$group_id@$cancel@$remark",
					'text'=>$group_id
				));
			}
		}
	   return $rows;
	}
//-------------------------------------------------------------------
	function conf_permission_access($corporation_id,$company_id,$group_id){
		$sql=" 
		SELECT id,corporation_id ,company_id,perm_id,group_id
		FROM conf_permission_access 
		WHERE corporation_id='$corporation_id' 
		AND company_id='$company_id' 
		AND group_id='$group_id' ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){ 	
			$id=$data['id'];
			$corporation_id=$data['corporation_id'];
			$company_id=$data['company_id'];
			$perm_id=$data['perm_id'];
			$group_id=$data['group_id'];
			
			array_push($rows,array(
				'id'=>"conf_permission_access@$id@$corporation_id@$company_id@$group_id@$perm_id",
				'text'=>$perm_id
			));
		}
	   return $rows;
	}
	
//-------------------------------------------------------------------
	function pastepertogroup($corporation_id,$company_id,$perm_id,$group_id){
		$session = new Zend_Session_Namespace('myprofile');
		$myprofile=$session->myprofile;
		$user_id=$myprofile['user_id'];
		$objConf=new Model_Mydbpos();
		$data = array(
			'corporation_id'=>$corporation_id,
			'company_id'=>$company_id,
			'group_id'=>$group_id,
			'perm_id'=>$perm_id,
			'reg_date'=>date('Y-m-d'),
			'reg_time'=>date('H:i:s'),
			'reg_user'=>$user_id
		);
		$table='conf_permission_access';
		$id=$objConf->insertdata($table,$data);
		return $id;
	}	
	//-------------------------------------------------------------------
	function removegroup($corporation_id,$company_id,$group_id){
		$objConf=new Model_Mydbpos();
		$sql="DELETE FROM `conf_usergroup` 
		WHERE group_id = '$group_id'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->deldata($sql);
	    /*
	    $sql0="DELETE FROM `conf_employee` 
	    WHERE group_id = '$group_id' 
	    AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
	    $objConf->deldata($sql0);
	    */
    	$sql0="UPDATE `conf_employee` SET group_id = '' 
    		WHERE group_id = '$group_id' 
    		AND corporation_id='$corporation_id'
			AND company_id='$company_id'";
    		$objConf->runsql($sql0);

    	$sql1="
    	DELETE FROM `conf_permission_access` 
    	WHERE 
    	group_id = '$group_id'
    	AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->deldata($sql1);	
	}
	
	//-------------------------------------------------------------------
	function updategroup($corporation_id,$company_id,$group_id,$group_id_old,$cancel,$remark){
		$objConf=new Model_Mydbpos();
		$sql="
		UPDATE `conf_usergroup` 
		SET 
		group_id='$group_id',
		cancel='$cancel',
		remark='$remark'
		WHERE group_id = '$group_id_old'
		AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->runsql($sql);
    	//echo "$sql<br>";
	    $sql0="
	    UPDATE `conf_employee` 
	    SET group_id = '$group_id' 
	    WHERE group_id = '$group_id_old' 
	    AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
	    $objConf->runsql($sql0);
		//echo "$sql0<br>";
    	$sql1="
    	UPDATE `conf_permission_access` 
    	SET group_id = '$group_id'
    	WHERE 
    	group_id = '$group_id_old'
    	AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->runsql($sql1);	
		//echo "$sql1<br>";
	}
	//-------------------------------------------------------------------
	function removepermidingroup($corporation_id,$company_id,$perm_id,$group_id){
		$objConf=new Model_Mydbpos();
		if(empty($perm_id)){
			$sql0=" 
		    SELECT perm_id FROM conf_permission_access 
		    WHERE corporation_id='$corporation_id'
		    AND company_id='$company_id'
		    AND group_id='$group_id'";
			$res=$objConf->fetchAllrows($sql0);
			$c=count($res);
			if($c !=''|| $c>0){
				$perm_id=$res[0]['perm_id'];
			}
		}
		$sql1="DELETE FROM `conf_permission_access` 
    	WHERE group_id = '$group_id'
    	AND perm_id = '$perm_id'
    	AND corporation_id='$corporation_id'
		AND company_id='$company_id'";
    	$objConf->deldata($sql1);	
		
		

	}
	//-------------------------------------------------------------------

	function  checkperingroup($corporation_id,$company_id,$perm_id,$group_id){
		if(!empty($group_id)){
			$objConf=new Model_Mydbpos();
			$sql=" 
		    SELECT perm_id FROM conf_permission_access 
		    WHERE corporation_id='$corporation_id'
		    AND company_id='$company_id'
		    AND group_id='$group_id'";
			$res=$objConf->fetchAllrows($sql);
		  	//$perm_id=$res[0]['perm_id'];
		  	$c=count($res);
			if($c ==''|| $c<1){
				return 'ok';
			}else{
				return 'มี perm_id แล้ว กรุณาลบ perm_id เดิมก่อน ';
			}
		}else{
			return 'กรุณาระบุ group_id';
		}
	}
	//-------------------------------------------------------------------
	function checkhavegroup($corporation_id,$company_id,$group_id){
		if(!empty($group_id)){
			$objConf=new Model_Mydbpos();
			$sql=" 
		    SELECT group_id 
		    FROM conf_usergroup 
		    WHERE corporation_id='$corporation_id'
		    AND company_id='$company_id'
		    AND group_id='$group_id'";
			$res=$objConf->fetchAllrows($sql);
		  	$c=count($res);
			if($c < 0 || $c==''){
				return 'ok';
			}else{
				return 'มี group_id แล้ว กรุณาลบ ระบุใหม่ ';
			}
		}else{
			return 'กรุณาระบุ group_id';
		}
	}
	//-------------------------------------------------------------------
	function editgroup($corporation_id,$company_id,$group_id){
		$rows=array();
		if(!empty($group_id)){
			$sql="
			SELECT * 
			FROM conf_usergroup
			WHERE  corporation_id='$corporation_id'
			AND company_id='$company_id'
			AND group_id='$group_id'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
			    $corporation_id = $data['corporation_id'];
				$company_id = $data['company_id'];
				$group_id = $data['group_id'];
		    	$cancel = $data['cancel'];
		    	$remark = $data['remark'];
	
		    	$rows=array(
		    	"corporation_id"=>$corporation_id,
		    	"company_id"=>$company_id,
		    	"group_id"=>$group_id,
		    	"cancel"=>$cancel,
		    	"remark"=>$remark);
		    }
		    
		}
		return json_encode($rows);
	}
	//-------------------------------------------------------------------

	
}
?>