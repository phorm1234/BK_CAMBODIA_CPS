<?php 
class Model_Posconfig{
//-----------------------------------------------------------------------------
	function treeposconfiglist(){
		$rows=array();
		$array=array();
		$sql=" SELECT DISTINCT id_config FROM com_pos_config_menu ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		foreach($res as $data){ 	
			$id_config=$data['id_config'];
			$sql_check="SELECT * FROM com_pos_config WHERE id_config='$id_config' AND code_type !=''";
			$countchild=$this->checkHavechildNode($sql_check);
			if($countchild > 0){
					$arr=array();
					$arr=$this->treeposconfiglistshop($id_config);
					array_push($rows,array(
					'id'=>"id_config@$id_config",
					'text'=>"Config Id $id_config",
					'state'=>'open',
					'children'=>$arr));
			}else{
				array_push($rows,array(
				'id'=>"id_config@$id_config",
				'text'=>"Config Id $id_config"));
			}
		}
		
		array_push($array,array(
					'id'=>"config_branch@0",
					'text'=>"Config POS",
					'state'=>'open',
					'children'=>$rows));
	
   		return json_encode($array);
	}
//------------------------------------------------------------------
	function treeposconfiglistshop($id_config){
		$rows=array();
		if(!empty($id_config)){
			$sql="
			SELECT DISTINCT id_config,corporation_id,company_id,branch_id,branch_no 
			FROM com_pos_config  
			WHERE id_config='$id_config'
			AND code_type !=''";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $data){ 
				$id_config =$data['id_config'];
				$corporation_id =$data['corporation_id'];
				$company_id=$data['company_id'];
				$branch_id=$data['branch_id'];
				$branch_no=$data['branch_no'];
				array_push($rows,array('id'=>"com_pos_config@$corporation_id@$company_id@$branch_id@$branch_no@$id_config",'text'=>$branch_id));
			}
		}
	   return $rows;
	}	
//-----------------------------------------------------------------------------
	function posconfigtree(){
		$rows=array();
		$sql=" SELECT * FROM com_country WHERE 1";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		foreach($res as $data){ 	
			$id=$data['id'];
			$country_id =$data['country_id'];
			$country_name=$data['country_name'];
			$sql_check="SELECT * FROM com_company WHERE country_id='$country_id'";
			$countchild=$this->checkHavechildNode($sql_check);
			if($countchild > 0){
					$arr=array();
					$arr=$this->com_company($country_id);
					array_push($rows,array(
					'id'=>"com_country@$id@$country_id",
					'text'=>$country_name,
					'state'=>'open',
					'children'=>$arr));
			}else{
					array_push($rows,array(
					'id'=>"com_country@$id@$country_id",
					'text'=>$country_name));
			}
		}
   		return json_encode($rows);
	}	
//------------------------------------------------------------------
	function com_company($country_id){
		$rows=array();
		$sql=" SELECT * FROM com_company WHERE country_id ='$country_id'";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		foreach($res as $data){ 	
			$id=$data['id'];
			$country_id=$data['country_id'];
			$corporation_id=$data['corporation_id'];
			$company_id=$data['company_id'];
			$company_name=$data['company_name'];
			
			$sql_check="
			SELECT * FROM com_branch  
			WHERE corporation_id='$corporation_id'
			AND company_id='$company_id'";
			
			$countchild=$this->checkHavechildNode($sql_check);
			if($countchild > 0){
					$arr=array();
					$arr=$this->com_branch($corporation_id,$company_id);
					array_push($rows,array(
					'id'=>"com_company@$corporation_id@$company_id",
					'text'=>$company_id,
					'state'=>'closed',
					'children' =>$arr));
			}else{
					array_push($rows,array(
					'id'=>"com_company@$corporation_id@$company_id",
					'text'=>$company_id));
			}
		}
   		return $rows;
	}
//------------------------------------------------------------------
	function com_branch($corporation_id,$company_id){
		$rows=array();
		if(!empty($corporation_id)){
			$sql="
			SELECT  * 
			FROM  com_branch 
			WHERE corporation_id='$corporation_id' 
			AND company_id='$company_id'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $data){ 
				$corporation_id =$data['corporation_id'];
				$company_id=$data['company_id'];
				$branch_id=$data['branch_id'];
				//$branch_no=$data['branch_no'];
				array_push($rows,array('id'=>"com_branch@$corporation_id@$company_id@$branch_id",'text'=>$branch_id));
			}
		}
	   return $rows;
	}		
//-------------------------------------------------------------------
	function checkHavechildNode($sql){
		if(!empty($sql)){
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		  	$c = count($res);
		   	if($c < 1)return 0;
		   	return $c;
		}
	}
//-----------------------------------------------------------------------------
	function tposconfig($table,$branch_id,$branch_no,$company_id,$corporation_id,$query,$qtype,$page,$rp,$sortname,$sortorder){
			if (!$sortname) $sortname = 'id';
			if (!$sortorder) $sortorder = 'desc';
			$sort = " ORDER BY $sortname $sortorder ";
			
			if (!$page) $page = 1;
			if (!$rp) $rp = 10;
			$start = (($page-1) * $rp);
			$limit = "LIMIT $start, $rp";
			
			$where = " WHERE 1  ";
			if($qtype !=""){$where.= " AND  $qtype  = '$query' ";}
			if($branch_id !=""){$where.= " AND  branch_id  = '$branch_id' ";}
			if($branch_no !=""){$where.= " AND  branch_no  = '$branch_no' ";}
			if($company_id !=""){$where.= " AND  company_id  = '$company_id' ";}
			if($corporation_id !=""){$where.= " AND  corporation_id  = '$corporation_id' ";}

			$sql = "SELECT  * FROM $table $where ";
			$sql.=" $sort $limit ";
			
			//echo $sql;
			//exit();
			
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('com',$table);
			$res=$objConf->fetchAllrows($sql);
			
			$total = count($res);
			$data['page'] = $page;
			$data['total'] = $total;
			$rows=array();
			
			foreach($res as $result){
							$id=$result['id'];
							$code_type=$result['code_type'];
							$value_type=$result['value_type'];
							$default_status=$result['default_status'];
							$condition_status=$result['condition_status'];
							$default_day=$result['default_day'];
							$condition_day=$result['condition_day'];
							$start_date=$result['start_date'];
							$end_date=$result['end_date'];
							$start_time=$result['start_time'];
							$end_time=$result['end_time'];
							
							$title= "ลบ";
							$img_del="<img src='/pos/plugin/jquery-easyui-1.2.5/themes/icons/no.png' alt=\"$title\" width='16' height='16' style='cursor:pointer' onclick=\"del_com_pos_config('$id')\"  />";
			
							$title="แก้ไข";
							$img_edit="&nbsp;&nbsp;&nbsp;&nbsp;<img src='/pos/plugin/jquery-easyui-1.2.5/themes/icons/pencil.png' alt=\"$title\" width='16' height='16'  style='cursor:pointer' onclick=\"edit_com_pos_config('$id')\"  />";
			
							$rows[] = array(
							"id" => $id,
									"cell" => array(
										"$code_type"
										,"$value_type"
										,"$default_status"
										,"$condition_status"
										,"$default_day"
										,"$condition_day"
										,"$start_date"
										,"$end_date"
										,"$start_time"
										,"$end_time"
									)
							);
			}
			$data['rows'] = $rows;
			return json_encode($data);    
	}
//-----------------------------------------------------------------------------	
	function cancelconfig($corporation_id,$company_id,$branch_id,$branch_no){
			$sql0="
			DELETE FROM `com_pos_config` 
			WHERE 
				corporation_id='$corporation_id' 
				AND company_id='$company_id' 
				AND branch_id='$branch_id' 
				AND branch_no='$branch_no'";
			$objConf=new Model_Mydbpos();
			return $objConf->deldata($sql0);
	}
//-----------------------------------------------------------------------------	
function sentbranch($corporation_id,$company_id,$branch_id,$branch_no,$id_config){

		$sql=" 
		SELECT * 
		FROM com_pos_config 
		WHERE corporation_id ='$corporation_id'
		AND company_id ='$company_id'
		AND branch_id ='$branch_id'
		AND branch_no ='$branch_no'
		AND id_config ='$id_config'";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		$count=count($res);
		if($count<1){
			$sql0="
			INSERT INTO `com_pos_config`(`id_config`,`corporation_id`,`company_id`,`branch_id`,`branch_no`,`code_type`,`value_type`, `default_status`, `condition_status`, `default_day`, `condition_day`, `start_date`, `end_date`, `start_time`, `end_time`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`)
			SELECT `id_config`,'$corporation_id','$company_id','$branch_id','$branch_no',`code_type`,`value_type`,`default_status`,`condition_status`,`default_day`,`condition_day`,`start_date`,`end_date`, `start_time`, `end_time`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`
			FROM com_pos_config_menu 
			WHERE com_pos_config_menu.id_config='$id_config' AND code_type !=''
			";
			$objConf=new Model_Mydbpos();
			$objConf->runsql($sql0);
			return "succeed";
		}else{
			return "error";
		}
	}
//-----------------------------------------------------------------------------	
function getcodetype($id,$table){
		$where = " WHERE 1  ";
		if($id !=""){$where.= " AND  id  = '$id' ";}
		$sql = "SELECT  * FROM $table $where ";
		$objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
	
		return json_encode($res);
}
//-----------------------------------------------------------------------------		
}
?>