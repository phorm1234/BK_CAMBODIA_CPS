<?php 
	class Reload extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			$command 	= "cd /home/op-4000/Desktop/ScannerRegister/; ./scannerregister &";
			$output 	= shell_exec('$command');
			//$output 	= shell_exec('pwd');
			echo "<pre>$output</pre>";
		}
	}
?>