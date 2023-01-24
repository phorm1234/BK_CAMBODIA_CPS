<?php 
	class Compare extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->library("session");
		}
		
		public function index($user_id=""){
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
			if(trim($user_id) != ""){
				$this->db->where("cid",$user_id);
				$this->db->where('check_date ',date('Y-m-d'));
				$this->db->where('unlock_code !=',"");
				if($this->db->count_all_results("check_in_out") > 0){
					redirect('idcard/index');
				}
			}
			$this->load->view("compare");
		}
		
		public function ckhardware(){
			
			$this->load->view("hardware");
		}
		
		public function step(){
		
			$step = $this->input->post('level');
			if($step == 0){
				$str = '
				  <p>
					  <h5 style="text-align:center; color:#CCC; margin:20px; font-size:20px;">
	       							โปรดวางนิ้วมือลงบนเครื่องสแกน<br>
										เพื่อทำการยืนยันตัวตน
						</h5>
					<!--<h4 style="text-align:center; color:#CCC; margin:20px;">
       							0
					</h4>-->
				</p>';
			}else if($step != 0){
				$str = '
				<p>
					<h5 style="text-align:center; color:#FF0000; margin:20px; font-size:16px;">
       							 ไม่พบรหัสพนักงานกรุณาสแกนอีกครั้ง
					</h5>
					<!--<h4 style="text-align:center; color:#CCC; margin:20px;">
       							'.$step.'
					</h4>-->
					</p>
				';
			  }
			 $str .='
				<p style="text-align:center">
						<button  style="width:150px;" type="button" class="btn btn-primary" onclick="clear_finger();">ข้ามขั้นตอนดังกล่าว</button>
				</p>';
			
			$data['str'] = $str;
			$this->load->view("compare_step",$data);
		}
		
		public function socket(){
			if(file_exists("finger/user_id.txt")){
					$strFileName = "finger/user_id.txt";
					$objFopen = fopen($strFileName, 'w');
					fwrite($objFopen,"");
					fclose($objFopen);
			}
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
				
			}
		}
		
		
		
		public function verify($user_id){
			if($user_id != ""){
				if($user_id != "000000"){
					$strText1 = $user_id;
				}else {
					// ค้นหาไม่เจอ
					$strText1 = "000000";
				}
				
				$strFileName = "finger/user_id.txt";
				$objFopen = fopen($strFileName, 'w');
				fwrite($objFopen, $strText1);
				fclose($objFopen);
			}
		}
			
		public function get_user_id(){
				if(file_exists("finger/user_id.txt")){
					$strFileName = "finger/user_id.txt";
					$objFopen = fopen($strFileName, 'r');
					if ($objFopen) {
						while (!feof($objFopen)) {
							$file = fgets($objFopen, 4096);
							if(trim($file) == ""){
								$arr = array("status"=>'',"userid"=>"");
							}else{
								if($file != "000000"){
										$tmp = explode("_",$file);	
										$arr = array(
													"status"=>"y",
													"userid"=>$tmp[0]
												);
									}else{
										$num =$this->session->userdata("row");
										$arr = array("status"=>'n',"userid"=>"","num"=>$num);
										$num +=1;
										if($num > 3){
											$this->session->set_userdata(array("row"=>1));
											
										}else{
											$this->session->set_userdata(array("row"=>$num));
										}
									}
								
							}
							echo json_encode($arr);
						}
						fclose($objFopen);
						exit();
					}
				}
			}
			
		public function clear_finger(){
			// set id card
			$strFileName = "finger/user_id.txt";
			$objFopen = fopen($strFileName, 'w');
			fwrite($objFopen, "not_found");
			fclose($objFopen);
		}	
	}
?>
