<?php 
	class Register extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			
		//if($this->session->userdata('status_login') != 'y')
		//	  redirect('login/index');
			//echo "session =".$this->session->userdata("status_login");
			$this->load->view("register/regis2");
		}
		
		public function checkuserid(){
			header('Content-Type: application/json; charset=utf-8');
			$user_id = trim($this->input->post('user_id'));
			if($user_id != ""){
				$this->db->where("user_id",$user_id);
				$this->db->where("emp_active","1");
				$res = $this->db->get('emp');
				if($res ->num_rows() > 0){
					$tmpem = $res->row();
					// check finger duplicate
					$this->db->where("fing_code",$user_id);
					$resfg =$this->db->get('employee_finger');
					
					if($resfg->num_rows() > 0){
						$tmpfg = $resfg->row();
						if($tmpfg->fing_left < 5 && $tmpfg->fing_right < 5){
							// ลบของเดิมทิ้ง
							$sqldel = "delete from employee_finger where fing_code='$user_id'";
							$resdel = $this->db->query($sqldel);
							$status= "y"; // สำเร็จลบของเดิมทิ้ง
							$name = $tmpem->emp_name;
							$surname = $tmpem->emp_surname;
						}else{ //บันทึกซ้้ำ
							$status= 'd';
							$name 		= $tmpem->emp_name;
							$surname 	= $tmpem->emp_surname;
							$right 		= $tmpfg->finger_r_type;
							$left 			= $tmpfg->finger_l_type;
							$id_card	=$tmpfg->card_id;
						}
					}else{ //สำเร็จ
						$name = $tmpem->emp_name;
						$surname = $tmpem->emp_surname;
						$status= "y";
					}
				}else{ //ไม่พบ
					$status= "n";
				}
			}else{
				$status= "n";
			}
			
			if($status  == 'n'){
				echo json_encode(array("status"=>$status));
			}else if($status  == 'y'){
				echo json_encode(array("status"=>$status,"name"=>$name,"surname"=>$surname));
			}else if($status == "d"){
				echo json_encode(array("status"=>$status,"name"=>$name,"surname"=>$surname,"right"=>$right,"left"=>$left,"id_card"=>$id_card));
			}			
		}
		
		public function saveimg(){
			$data 		= $this->input->post('imgData');
			$filename 	= $this->input->post('user_id');
			if($data != "" && $filename != ""){
				$file = $filename.".png";
				$uri =  substr($data,strpos($data,",")+1);
				file_put_contents(base_url()."profile/".$file, base64_decode($uri));
				echo $file;
			}
		}	
		
	}
?>
