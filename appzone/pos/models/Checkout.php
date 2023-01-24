<?php 
class Model_Checkout{
	function check_log_out($user_id,$group_id,$perm_id){
			$objConf=new Model_Mydbpos();
			$sql="SELECT *  FROM `conf_employee` WHERE user_id ='$user_id'";
		
			$run=$objConf->fetchAllrows($sql,2);
			$c=count($run);
	
			if($c<1){
				return "out";
			}
		
			$group_id_db=$run[0]['group_id'];
		
			if($group_id != $group_id_db){
				return "out";
			}
			
			$sql2="SELECT * FROM `conf_permission_access` WHERE group_id='$group_id' ";
			$run2=$objConf->fetchAllrows($sql2,2);
			$perm_id_db=$run2[0]['perm_id'];
		
			if($perm_id != $perm_id_db){
				return "out";
			}
			
	}
}
?>