<?php
	/***
		PHP File to create user gedget; 
	*/
	$do = $_REQUEST['gedget'];	
	if(isset($do)){
		switch($do){
			case '0' :
				$obj->body = 'gedget 1';				
				break;
			case '1' :
				$obj->body = 'gedget 2';	
				break;
			case '2' :
				$obj->body = 'gedget 3';	
				break;
		}
		echo json_encode($obj);			
	}
?>