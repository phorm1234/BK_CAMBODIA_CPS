<?php 
	class Model_Menu{
		protected  $db=null;
		function __construct(){
			$this->db=Zend_Registry::get("db1");
		}//func
		
		function getMenu($menu_ref = ''){
		    $stmt_menu=$this->db->select()
		    					->from('conf_menu')
		    					->where('menu_ref=?',$menu_ref)
		    					->order('menu_seq asc');
		    $row=$stmt_menu->query()->fetchAll();
		    if(count($row)==0) return false;	
		   	foreach($row as $data){
		   		 $out[] = array('menu' => $data,
		                             'submenu' => $this->getMenu($data['menu_id']));
		   	}//foreach		
		    return $out;
		 }//func
		 
	function menuHtml($a){
	   if(!is_array($a)) return false;
	   $out = '<ul style="margin-left:5px;">';
	   foreach($a as $s){
	      $out .='<li>';	      
	      $out .=$s['menu']['menu_name'].'<br>';
	      if(is_array($s['submenu'])) $out .= $this->menuHtml($s['submenu']);
	      $out .='</li>';
	   }
	   $out .='</ul>';
	   return $out;
	 }//func
		
	}//class
?>