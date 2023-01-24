<?php
	header("content-type: text/javascript");
	$do = $_REQUEST['gadget'];
	$callback = $_REQUEST['callback'];
	if(isset($do) && isset($callback)){
		switch($do){
			case '1' :
				$obj->body = 'gedget 1';				
				break;
			case '2' :
				$obj->body = 'gedget 2';	
				break;
			case '3' :
				$obj->body = 'gedget 3';	
				break;
		}
		if($obj) echo $callback.'('.json_encode($obj).')';
			
	}
?>