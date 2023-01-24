<?php 
class Model_Processconfig{
//-----------------------------------------------------------------------------
	function treeprocessconfig(){
		$objConf=new Model_Mydbpos();
		$sql=" SELECT * FROM com_list_process WHERE 1 ";
		$res=$objConf->fetchAllrows($sql);
	    $rows=array();
		foreach($res as $data){
			$id=$data['id'];
			$config_processname=$data['config_processname'];
			array_push($rows,array("id"=>$id,"text"=>$config_processname));
		}
	   return json_encode($rows);
	}
//-----------------------------------------------------------------------------		
}
?>