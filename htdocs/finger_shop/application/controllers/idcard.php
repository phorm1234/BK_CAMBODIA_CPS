<?php 
	class Idcard extends CI_Controller{
		public function __construct(){
			parent::__construct();
			//$this->load->libraries('database');
		}
		
		public function index(){
			header("Expires: Sat, 1 Jan 2005 00:00:00 GMT");
			header("Last-Modified: ".gmdate( "D, d M Y H:i:s")."GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			if(file_exists("finger/user_id.txt")){
				$strFileName = "finger/user_id.txt";
				$objFopen = fopen($strFileName, 'w');
				fwrite($objFopen,"");
				fclose($objFopen);
			}
			$this->load->view("idcard");
		}
		
		public function compare_idcard(){
			$id_card = $this->input->post('id_card');
			$unlock_key = $this->input->post('unlock_key');
			$status = "";
			$msg = "";
			$user_id = "";
			//check webcam active
			if($this->check_webcam() == "Y"){
				if(trim($id_card) != ""){
					$this->db->where("card_id",$id_card);
					$res = $this->db->get("employee_finger");
					if($res->num_rows() > 0){
						$rows = $res->row();
						$user_id =$rows->fing_code;
						$status = $this->unlock_idcard($user_id,$unlock_key);
						if($status == "N"){
							$user_id = "000000";
							$msg     = "รหัสปลดล็อกไม่ถูกต้อง";
							$unlock_key = "";
						}else if($status == "Y"){
							$unlock_key = "_".$unlock_key;
						}
					}else{
						    $user_id = "000000";
						    $unlock_key = "";
					}
					if(file_exists("finger/user_id.txt")){
						$strFileName = "finger/user_id.txt";
						$objFopen = fopen($strFileName, 'w');
						fwrite($objFopen,$user_id.$unlock_key);
						fclose($objFopen);
					}
				}
			}else{
				$status = "N";
				$msg = "กล้องหน้าร้านไม่พร้อมใช้งาน";
			}
			echo json_encode(array("status"=>$status,"user_id"=>$user_id,"msg"=>$msg));
		}
		
		public function clear_finger(){
			// set id card
			$strFileName = "finger/user_id.txt";
			$objFopen = fopen($strFileName, 'w');
			fwrite($objFopen, "not_found");
			fclose($objFopen);
		}
		
		public function check_ip_shop(){
			if($h1 = opendir("/var/www/shop/capture")){
				while (false !== ($p1 = readdir($h1))){
					if(substr($p1,0,1)=="_"){
						$ip=substr($p1,1);
						break;
					}
				}
			}

			if($ip != ""){
				$this->db->where("shop_ip",$ip);
				$res = $this->db->get("shop")->row();
				return $res->shop_number;
			}
		}
		
		public function unlock_idcard($user_id="",$unlock_key=""){
			$this->db->where("fing_code",$user_id);
			$this->db->where("unlock_code",$unlock_key);
			if($this->db->count_all_results("employee_finger") == 0){
				$be = $user_id;
				$shop = $this->check_ip_shop();
				$dd = date("Y-m-d");
				$aa = $be.$shop.$dd;
				$md = md5($aa);
				$key = substr($md,3,4).substr($md,-1);
				if($unlock_key == $key){
					return 'Y';
				}else{
					return 'N';
				}
			}else{
				$this->db->where("fing_code",$user_id);
				$this->db->where("unlock_code",$unlock_key);
				$rows = $this->db->get("employee_finger")->row();
				if($rows->unlock_code == $unlock_key){
					return 'Y';
				}else{
					return 'N';
				}
			}
		}
		
		public function test($user_id="",$unlock_key=""){
				$be = $user_id;
				$shop = $this->check_ip_shop();
				$dd = date("Y-m-d");
				$aa = $be.$shop.$dd;
				$md = md5($aa);
				$key = substr($md,3,4).substr($md,-1);
			echo json_encode(array('uc'=>$key));
			echo "<br>";
			echo $this->unlock_idcard($user_id,$unlock_key);
		}
		
		private function check_webcam(){
			if($h1 = opendir("/var/www/shop/capture")){
				while (false !== ($p1 = readdir($h1))){
					if(substr($p1,0,1)=="_"){
						$ip=substr($p1,1);
						break;
					}
				}
			}
			
			$ex     = explode(".", $ip);
			$cam1_ip="$ex[0].$ex[1].$ex[2].".($ex[3]+1);
			$cam2_ip="$ex[0].$ex[1].$ex[2].".($ex[3]+2);
			$cam3_ip="$ex[0].$ex[1].$ex[2].".($ex[3]+3);
			//$cam1_ip="192.168.1.4";
			//$cam2_ip="192.168.3.247";
			//$cam3_ip="192.168.2.3";
			$ip1 = $this->fping_num($cam1_ip);
			$ip2 = $this->fping_num($cam2_ip);
			$ip3 = $this->fping_num($cam3_ip);
			$ip_all = $ip1+$ip2+$ip3;
			if($ip_all >= 1){
				return "Y";
			}else {
				return "N";
			}

		}
		
		private function fping_num($ip){
		    $aa="/usr/bin/fping $ip";
		    exec($aa,$arr,$err);
		    $ex=explode(" ",$arr[0]);
		    if($ex[2]=="alive"){
		        return 1;
		    }else{
		        return 0;
		    }
		}	
		
	}
?>
