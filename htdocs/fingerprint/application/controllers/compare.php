<?php 
	class Compare extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			$this->load->view("");
		}
		
		public function socket(){
			$ip = "127.0.0.1";
			$msg = "I-000000";
			//$shop_id = $_REQUEST['shop_id'];
			$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			$result = @socket_connect($socket, $ip, 5000);
			if(!$result){
				echo "fail";
			}else{
				socket_write ($socket, $msg) . chr($msg);
				socket_close($socket);
				echo "success";
				/*if(file_exists("finger/user_id.txt")){
					unlink("finger/user_id.txt");
				}*/
				if(file_exists("finger/user_id.txt")){
					$strFileName = "finger/user_id.txt";
					$objFopen = fopen($strFileName, 'w');
					fwrite($objFopen,"");
					fclose($objFopen);
				}
				
			}
			system('echo "" > /var/www/pos/htdocs/finger_shop/finger/user_id.txt');
		}
		
		public function verify($user_id){
			if($user_id != ""){
				if($user_id != "000000"){
					$strText1 = $user_id;
				}else {
					// ค้นหาไม่เจอ
					$strText1 = "000000";
				}
				
//				$strFileName = "/var/www/pos/htdocs/finger_shop/finger/user_id.txt";
//				$objFopen = fopen($strFileName, 'w');
//				fwrite($objFopen, $strText1);
//				fclose($objFopen);
				system('echo "" > /var/www/pos/htdocs/finger_shop/finger/user_id.txt');
			}
		}
			
		public function get_user_id(){
					$strFileName = $_SERVER['REMOTE_ADDR']."/finger_shop/finger/user_id.txt";
					//$objFopen = fopen($strFileName, 'r');
					//if ($objFopen) {
						//while (!feof($objFopen)) {
							$file = trim(file_get_contents('http://'.$_SERVER['REMOTE_ADDR']."/finger_shop/finger/user_id.txt"));
							if($file == ""){
								echo json_encode(array("status"=>""));
							}
							$tmp = explode("_",$file);
							if($tmp[0] != ""){
								$this->db->where("fing_code",$tmp[0]);
								$res = $this->db->get("employee_finger");
								if($res->num_rows() > 0){
									$rows = $res->row();
									$this->db->where("user_id",$tmp[0]);
									$emp = $this->db->get("emp")->row();
									$arr = array(
												"status"=>"1",
												"userid"=>$tmp[0],
												"name"=>$emp->emp_name,
												"surname"=>$emp->emp_surname
											);
								}else{
									$arr = array("status"=>'0');
								}
								echo json_encode($arr);
							}else{
								$arr = array("status"=>'');
								
							}
						//}
						//fclose($objFopen);
						exit();
//					}else{
//						echo "fail";
//					}
				}
	}
?>