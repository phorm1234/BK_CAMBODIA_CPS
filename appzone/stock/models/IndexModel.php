<?php 
class Model_IndexModel{	
	public $db;
	public function __construct(){
		$this->db=Zend_Registry::get('db1');
	}
	public function viewshelf(){
		$content="fdfdsf";
		return $content;
	}	
}
?>