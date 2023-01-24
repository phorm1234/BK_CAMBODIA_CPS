<?php 
	class Idcard extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			if(file_exists("finger/user_id.txt")){
				$strFileName = "finger/user_id.txt";
				$objFopen = fopen($strFileName, 'w');
				fwrite($objFopen,"");
				fclose($objFopen);
			}
			$this->load->view("idcard/idcard");
		}
		
		public function compare_idcard(){
			$id_card = $this->input->post('id_card');
			if(trim($id_card) != ""){
				$this->db->where("card_id",$id_card);
				$res = $this->db->get("employee_finger");
				if($res->num_rows() > 0){
					$rows = $res->row();
					$user_id =$rows->fing_code;
				}else{
					$user_id = "000000";
				}
				if(file_exists("finger/user_id.txt")){
					$strFileName = "finger/user_id.txt";
					$objFopen = fopen($strFileName, 'w');
					fwrite($objFopen,$rows->fing_code);
					fclose($objFopen);
				}
			}
			echo json_encode(array("user_id"=>$user_id));
		}
	}
?>