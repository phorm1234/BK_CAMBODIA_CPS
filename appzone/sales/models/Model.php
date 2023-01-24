<?php
require_once('DB.php'); 
class Model extends Zend_Db_Table
{
	protected $_name = 'model';
	public $db = null;
	public function selectData($sql,$database)
	{
			$db=$this->db=DB::conn($database);
			$result = $db->fetchAll($sql,2);
			return $result;
	}
	function numRows($sql,$database)
	{
			$db=$this->db=DB::conn($database);
			$result = $db->fetchAll($sql,2);
			return count($result); 
	}
	function runSql($sql,$database)
	{
			$db=$this->db=DB::conn($database);
			$result = $db->query($sql);
			return count($result);
			
	}
	
	function closeDb()
	{
		$this->db->closeConnection();
			
	}
}
//end class Model









