<?php 
class Model_Mydbpos{
	function __construct(){
		$this->db=Zend_Registry::get('dbOfline');
	}
	

	//--------------------------------------------------------
	function checkDbOnline($process_id,$table_id){
		$objCall=new SSUP_Controller_Plugin_Db();
		$objCall->processDb($process_id,$table_id);	
		$this->db=Zend_Registry::get('dbOnline');
	}
	//--------------------------------------------------------
	function checkNumrows($sql){
		$result=$this->db->fetchAll($sql,2);
		$this->db->closeConnection();
		unset($sql); 
		return count($result);
	}
	//----------------------------------------------------------
	function fetchAllrows($sql){
		$result=$this->db->fetchAll($sql,2);
		$this->db->closeConnection();
		unset($sql); 
		return $result; 
	}

	//-----------------------------------------------------------
	function insertdata($table,$data){
		$this->db->insert($table,$data);
		$id = $this->db->lastInsertId();
		$this->db->closeConnection();
		unset($data); 
		unset($table);
		return $id;
	}
	//-----------------------------------------------------------
	function deldata($sql){
		$run=$this->db->query($sql);
		$this->db->closeConnection();
		unset($sql); 
		return $run;
	}
	//-----------------------------------------------------------
	function selectdata($select,$from,$where,$order){
			if(!empty($order)) {
				$menu=$this->db->select()
	    		->from($from,$select)
	    		->where($where)
	    		->order($order);
	    		unset($select); 
	    		unset($where); 
	    		return $menu->query()->fetchAll();
			}
		   $menu=$this->db->select()
		    ->from($from,$select)
		    ->where($where);
		   	unset($select); 
	    	unset($where);
    		return $menu->query()->fetchAll();
	}
	//-------------------------------------------------------------------	
	function updatedata($table,$data,$where){
    	$this->db->update($table,$data,$where);
		$this->db->closeConnection();
		unset($data);
		unset($where);
	}
	//-------------------------------------------------------------------	
	function runsql($sql){
		$run=$this->db->query($sql);
		$this->db->closeConnection();
		unset($sql);
		return $run;
	}
	//-----------------------------------------------------------
	
}
?>