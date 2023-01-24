<?php 
	class Socket extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			$ip = "127.0.0.1";
			$msg = strtoupper(trim($this->input->post('user_id')));
			$fingertype = $this->input->post('fingerid');
			$msg =$this->check_finger_step($msg,$fingertype);
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			$result = socket_connect($socket, $ip, 5000);
			socket_write ($socket, $msg) . chr($msg);
			socket_close($socket);
			echo 1;
			$this->clear_error();
		}
		
		private function check_finger_step($user_id,$fingertype){
			$tmp = explode("-",$user_id);
			$id = $tmp[1];
			$fintype = substr($fingertype,-2,1);
			$this->db->where("fing_code",$id);
			$res =$this->db->get('employee_finger');
			if($fintype == "R"){
				// ทำครั้งแรก มือซ้าย
				$user_id = $user_id.'_'.$fingertype;
			}else{
				// ทำครั้งแรก มือขวา
				$user_id = $user_id.'_'.$fingertype;
			}
			//===== check update 
			if($res->num_rows() > 0){
				$rows = $res->row();
				if($rows->fing_left == 3){
					$this->checkupdate($id,$fintype);
				}
			}
			
			return $user_id;
		}
		
		public function checkupdate($id,$type){
			$this->db->where("fing_code",$id);
			$this->db->update("employee_finger",array("fing_right"=>"0","fing_left"=>"0","flag"=>"1"));
		/*	if($type == "R"){
				// ทำครั้งแรก มือซ้าย
				$this->db->update("employee_finger",array("fing_right"=>"0","flag"=>"1"));
			}else{
				// ทำครั้งแรก มือขวา
				$this->db->update("employee_finger",array("fing_left"=>"0","flag"=>"1"));
			}*/
		}
		
		public function get_client_ip() {
			$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
				$ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');
			else
				$ipaddress = 'UNKNOWN';
			echo getenv('REMOTE_ADDR');
			//return $ipaddress;
		}
		
		public function recieve(){
			$user_id 		=trim($this->input->post('user_id'));
			$id_card 		=trim($this->input->post('id_card'));
			$fingerid 		=trim($this->input->post('fingerid'));
			$fingtype = substr($fingerid, -2,1);
			$total = 5;
			$gran_totlal = 10;
			$num = 1;
			//$user_id = "00B399";
			if($user_id != ""){
				$this->update_idcard($user_id,$id_card);
				//== check user_id
				
				$this->db->where("fing_code",$user_id);
				$this->db->where("finger_type",$fingerid);
				$resfg = $this->db->get("employee_finger");
				if($resfg->num_rows() > 0){
					$rows = $resfg->row();
					$num_right = $rows->fing_right;
					$num_left = $rows->fing_left;
					if($num_left == "") {
						$num_left = 0;
					}
				
					//if($num_right < 5){ //แสดงข้อมูลมือขวา
					
					if($fingtype =="L"){
						    $percen = $num_left*20;
						    if($percen != 100){
								echo json_encode(array("beep"=>$num_left,"num"=>"Scanned ".$num_left.'/'.$total,"percen"=>$percen,"type"=>"L"));
						    }else{
						    	echo json_encode(array("beep"=>$num_left,"num"=>"<font style=\"color:#0F0\">SUCCESS</font>","percen"=>$percen,"type"=>"L","final"=>"final","error"=>$this->read_error()));
						    }
						   
					}else{
							$percen = $num_right*20;
							if($percen != 100){
								echo json_encode(array("beep"=>$num_right,"num"=>"Scanned ".$num_right.'/'.$total,"percen"=>$percen,"type"=>"R"));
							}else{
								echo json_encode(array("beep"=>$num_right,"num"=>"<font style=\"color:#0F0\">SUCCESS</font>","percen"=>$percen,"type"=>"R","final"=>"final","error"=>$this->read_error()));
							}
							
					}
					
				
				}
			}else{
						echo json_encode(array("num"=>'0/5'));
			}
		}
		
		public function update_idcard($user_id,$id_card){
			$this->db->where("fing_code",$user_id);
			$res  = $this->db->get("employee_finger");
			if($res->num_rows() > 0){
				$this->db->where("fing_code",$user_id);
				if($id_card == '-'){
					$id_card="";
					$status = 0;
				}else{
					$status = 1;
				}
				$this->db->update("employee_finger",array("card_id"=>$id_card,"card_id_status"=>$status));
			}
		}
		
		public function insert_finger($user_id,$num){
			$total = 5;
			$user_tmp 			=explode('_',$user_id);
			if($user_id != ""){
				$user_id = strtoupper($user_tmp[0]);
				//== check user_id		
				$this->db->where("fing_code",$user_id);
				$this->db->where("finger_type",$user_tmp[1]);
				$res = $this->db->get("employee_finger");
				if($res->num_rows() == 0){
					// insert
					$file_name = $user_id.".tmpl";
					$image_profile = $user_id.".png";
					if($user_tmp[1] == 'l'){
						$this->db->insert("employee_finger",array("fing_code"=>$user_id,"fing_path"=>$file_name,"img_profile"=>$image_profile,"fing_left"=>$num,"finger_type"=>$user_tmp[1],"create_date"=>date("Y-m-d")));
					}else{
						$this->db->insert("employee_finger",array("fing_code"=>$user_id,"fing_path"=>$file_name,"img_profile"=>$image_profile,"fing_right"=>$num,"finger_type"=>$user_tmp[1],"create_date"=>date("Y-m-d")));
					}
				
				}else{
					if($num <= $total){
					if($user_tmp[1] == 'l'){
						$this->db->where("fing_code",$user_id);
						$this->db->update("employee_finger",array("fing_left"=>$num,"create_date"=>date("Y-m-d")));
					}else{
						$this->db->where("fing_code",$user_id);
						$this->db->update("employee_finger",array("fing_right"=>$num,"create_date"=>date("Y-m-d")));
					}
					}
				}
				}else{
					echo 'n';
				}
		}
		
		public  function update_finger_type($user_id,$type,$finger_type){
				$this->db->where("user_id",$user_id);
				if($type =="L"){
					$this->db->update("employee_finger",array("finger_l_type"=>$finger_type));
				}else{
					$this->db->update("employee_finger",array("finger_r_type"=>$finger_type));
				}
				
		}
		
		public function clear_error(){
			if(file_exists("finger/error.txt")){
				$strFileName = "finger/error.txt";
				$objFopen = fopen($strFileName, 'w');
				fwrite($objFopen,"");
				fclose($objFopen);
			}
		}
		
		public function write_error($error){
			if(file_exists("finger/error.txt")){
				$strFileName = "finger/error.txt";
				$objFopen = fopen($strFileName, 'w');
				fwrite($objFopen,$error);
				fclose($objFopen);
			}
		}
		
		private function read_error(){
			if(file_exists("finger/error.txt")){
				$strFileName = "finger/error.txt";
				$objFopen = fopen($strFileName, 'r');
				if ($objFopen) {
					while (!feof($objFopen)) {
						$file = fgets($objFopen, 4096);
						if($file == ""){
							$status = "";
						}else{
							$tmp = explode("_", $file);
							$sqldel = "delete from employee_finger where fing_code='{$tmp[0]}' and finger_type='{$tmp[1]}'";
							$resdel = $this->db->query($sqldel);
							$status = $tmp[1];
						}
					}
					}
				}
				fclose($objFopen);
				return $status;
		}
		
}
?>
