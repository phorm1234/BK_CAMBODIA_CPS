<?php 
	class Socket extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			$ip = "127.0.0.1";
			$msg = "E-00B399";
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			$result = socket_connect($socket, $ip, 5000);
			//if($result){
			//	echo "fail";
			//}else{
				socket_write ($socket, $msg) . chr($msg);
				socket_close($socket);
				echo 1;
			//}
		}
		
		
}
?>
