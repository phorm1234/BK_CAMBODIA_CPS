<?php
	class Login extends CI_Controller{
		
		public function __construct(){
			parent::__construct();
			//echo $ip;
		}
		
		/*public function index(){
			$data = array();
			$data['error'] = "";
			$url = "http://192.168.2.10/api/fingerprint.php?param=";
			if($_POST){
				$username 	=	trim($this->input->post('username'));
				$password		=	trim($this->input->post("password"));

				if($username != "" && $password != ""){
				 	$this->db->where("username",$username);
				 	$this->db->where("password",$password);
				 	$this->db->where("status",1);
				 	$temres = $this->db->get("admin");
				 	if($temres->num_rows() > 0){
				 			$res =$temres->row();
							if($this->set_session($res->username, 1)){
								redirect("register");
							}

				 	}else{
				 		// ค้นหาไม่เจอในด้าต้าเบส  call api 
				 		$param = base64_encode($username.":".$password);
				 		$json = @file_get_contents($url.$param);
				 		$obj = @json_decode($json);
				 		if(@$obj->status == "1"){
				 			// insert to admin 
				 			$datain = array(
				 					"name"=>$obj->emp_name,
				 					"surname"=>$obj->emp_surname,
				 					"user_id"=>$obj->user_id,
				 					"username"=>$username,
				 					"password"=>$password,
				 					"dept_code"=>$obj->emp_dept_code,
				 					"status"=>$obj->emp_active
				 					);
				 			$resin = $this->db->insert("admin",$datain);
				 			if($resin){
				 				//$lastid = $this->db->insert_id();
				 				if($this->set_session($username, 1)){
				 						redirect("register");
				 				}
				 			}else{
				 				$data['error'] = "บันทึกข้อมูลผิดพลาด";
				 			}
				 		}else{
				 			$data['error'] = "ไม่สามารถล็อกอินได้";
				 		}
				 	}
			 }else{
			 	//login fail
			 	$data['error'] = "กรุณาล็อกอินเข้าระบบ";
			 }
			}//end post
			$this->load->view("login/login",$data);
		}*/
		
		
	public function index(){
			$data = array();
			$data['error'] = "";
			$shop_id = $this->check_shop_id();
			//$shop_id = "6274";
			$url = "http://192.168.252.246/api/ros.php?shop_id=".$shop_id.'&user_id=';
			//http://192.168.2.10/api/ros.php?shop_id=6274&user_id=000908
			if($_POST){
				$username 	=	trim($this->input->post('username'));
				$password		=	trim($this->input->post("password"));
				if($username != ""){
						$url = $url.$username;
						//echo $url;
				 		$json = @file_get_contents($url);
				 		$obj = @json_decode($json);
				 		if(@$obj->status == "1"){
				 				//if($this->set_session($username, 1)){
				 					//echo $this->session->userdata('status_login');
				 					$sess = array(
														"sess_userid"=>$username,
														"status_login"=>'y',
														"status"=>"1"
													);
										$this->session->set_userdata($sess);
				 						redirect("register");
				 				//}
				 		}else{
				 			$data['error'] = "ไม่สามารถล็อกอินได้";
				 		}
			 }else{
			 	//login fail
			 	$data['error'] = "กรุณาล็อกอินเข้าระบบ";
			 }
			}//end post
			$this->load->view("login/login",$data);
		}
		
		public function menu(){
			if($this->session->userdata("status_login") != 'y'){
				redirect("login/index");
			}
			//echo "session =".$this->session->userdata("status_login");
			
			$this->load->view("login/menu");
		}
		
		public function set_session($user_id,$status){
			$arr_ssession = array(
					"userid"=>$user_id,
					"status_login"=>'y',
					"status"=>$status
			);
			$this->session->set_userdata($arr_ssession);
			return true;
		}
		
		public function jsontest(){
			$json = @file_get_contents("http://192.168.252.246/api/fingerprint.php?param=MDAwNDcxOm11bmdraHVuZw==");
			$obj = @json_decode($json);
			//print_r($obj);
			echo $obj->emp_name;
			
		}
		
	//-----------------------  finger scan  ----------------------
		public function getuserid(){
			if(file_exists("/var/www/pos/htdocs/finger_shop/finger/user_id.txt")){
				$strFileName = "/var/www/pos/htdocs/finger_shop/finger/user_id.txt";
				$objFopen = fopen($strFileName, 'r');
				if ($objFopen) {
					while (!feof($objFopen)) {
						$file = fgets($objFopen, 4096);
						if(trim($file) == ""){
							$arr = array("status"=>'',"userid"=>"");
						}else{
							if($file != "000000"){
								if($file == "not_found"){
									// ข้ามไป id card scanner
									$arr = array("status"=>'sk',"userid"=>"");
									echo json_encode($arr);
									exit();
								}
								$tmp = explode("_",$file);
								$shop_id = $this->check_shop_id();
								//$shop_id = "6274";
								$url = "http://192.168.252.246/api/ros.php?shop_id=".$shop_id.'&user_id='.$tmp[0];
								$json = @file_get_contents($url);
				 				$obj = @json_decode($json);
				 				if(@$obj->status == "1"){
				 					$arr = array(
											"status"=>"y",
											"userid"=>$tmp[0],
											"password"=>$tmp[0]
									);
				 				}else{
				 					$arr = array("status"=>'f',"userid"=>$tmp[0]);
				 				}
									
							}else{
								$arr = array("status"=>'n',"userid"=>"");
							}
		
						}
						echo json_encode($arr);
					}
					fclose($objFopen);
					exit();
				}
			}else{
				echo "file not fond";
			}
		}
		
		
		public function check_shop_id(){
			$this->db->order_by("check_id","desc");
			$this->db->limit(1);
			$res = $this->db->get('check_in_out');
			$arr = $res->result_array();
			//print_r($arr);
			if($res->num_rows() > 0){
				$row = $res->row();
				return $row->shop_id;
			}else {
				return "";
			}
		}
		
	}
?>
