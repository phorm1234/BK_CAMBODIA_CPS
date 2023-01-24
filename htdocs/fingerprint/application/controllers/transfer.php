<?php 
	class Transfer extends CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			
			
		}
		
		public function upload(){
			$des = "/var/www/shop/finger";
			if(!file_exists($des)){
				@system("ln -s /var/www/FingerScanTemplate/ /var/www/shop/finger");
			}
			$ip=$this->ip();
			$url = "http://192.168.252.246/api/syndata.php?ip=".$ip;
			$json = file_get_contents($url);
			echo $json;
			
		}
		
		public function download($ip = ""){
			$ip=$this->ip();
			$url = "http://192.168.252.246/api/putdata.php?ip=$ip&path=/index.php/transfer/receive/";
			$json = file_get_contents($url);
			//echo $json;
			$uploaddir = '/var/www/FingerScanTemplate/emp_data.zip';
			if(file_exists($uploaddir)){
				@unlink($uploaddir);
				echo 'y';
			}
		}
		
		public function receive(){
			//print_r($_FILES);
			$uploaddir = '/var/www/FingerScanTemplate/';
			if(move_uploaded_file($_FILES["file_data"]["tmp_name"],$uploaddir.$_FILES["file_data"]["name"]))
			{
				$filename = $uploaddir.$_FILES["file_data"]["name"];
				$this->unzipfile($filename);
			}
			
		}
		
		public function unzipfile($filename){
		$zip = new ZipArchive;
		$res = $zip->open($filename);
			if ($res === TRUE) {
			  $zip->extractTo('/var/www/FingerScanTemplate/');
			  $zip->close();
			  	 // แตกไฟล์สำเหร็จ
			  	 $this->readsql('emp.sql');
			  	// $this->readsql('employee_finger.sql');
			  	 
			} else {
			 	 echo 'doh!';
			}
		}	
		
		public function readsql($filename){
			$path ="/var/www/FingerScanTemplate/";
			$strFileName = $path."$filename";
			$objFopen = fopen($strFileName, 'r');
			if ($objFopen) {
				while (!feof($objFopen)) {
					$file = fgets($objFopen, 4096);
						if(trim($file) != ""){
						echo $file."=<br>";
							$this->db->query("SET NAMES TIS620");
							$this->db->query($file);
						}
					}
					fclose($objFopen);
					@unlink($strFileName);
					
			}else{
				echo "n";
			}
		}
		


		public function ip(){
			exec('/sbin/ifconfig | grep "inet addr:" | grep -v "127.0.0.1" | cut -d: -f2 | awk \'{ print $1}\'', $IPS);
			//print_r($IPS);
			return $IPS[0];	
		}
	}
?>
