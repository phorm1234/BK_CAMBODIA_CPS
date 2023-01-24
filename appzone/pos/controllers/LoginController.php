<?php 
	class LoginController extends Zend_Controller_Action{
		public static $chk_optimizemysql=0;
		//---------------------------------------------------
		public function init(){
			header('Content-type: text/html; charset=utf-8'); 			
			$this->initView();
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if(!empty($empprofile)){
				if($empprofile['user_id']!='' && $empprofile['employee_id']!='' ){
	     			$this->_redirect('/index/index');
				}
			}
		}
		//--------------------------------------------------------
		public function checkkeyloginAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_LoginModel();
			$checkkeylogin=$objConf->checkkeylogin();
			echo "$checkkeylogin";
		}
		//--------------------------------------------------------
		public function testAction(){
			$this->_helper->layout()->disableLayout(); 
		}	
		//--------------------------------------------------------
		public function getipAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$user_id = $filter->filter($this->getRequest()->getParam("user_id")); 
			$password_id = $filter->filter($this->getRequest()->getParam("password")); 
			
			$objConf=new Model_LoginModel();
			$checkip_regis=$objConf->checkip_regis();
			echo"$checkip_regis";
		}	
		//--------------------------------------------------------
		public function killallAction(){
			$this->_helper->layout()->disableLayout(); 
			$comman="killall firefox";
			shell_exec($comman);   
		}	
		//--------------------------------------------------------
		public function checksentdataAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_LoginModel();
			$checksentdata=$objConf->checksentdata();
			echo "$checksentdata";
		}
		//--------------------------------------------------------
		public function indexAction(){
			$this->_helper->layout()->disableLayout(); 
			$this->_redirect('/login/login');
		}
		//--------------------------------------------------------
		public function loginAction(){
			$this->_helper->layout()->setLayout('/shoplogin');
			$objConf=new Model_Adminlogin();
			$this->view->login_for=$objConf->login_for();
			//*WR04052018
			$objLogin=new Model_LoginModel();
			$this->view->lockfingerscan=$objLogin->checkLogFingerScan();
			//*WR 04072012 repair mysql table
			if($this->chk_optimizemysql>0){
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$objPos->optimizeMysqlTable();
			}
			$this->chk_optimizemysql++;
		}
		//--------------------------------------------------------
		public function checkversionAction(){
			$this->_helper->layout->disableLayout();
			$objConf=new Model_LoginModel();
			$version=$objConf->chk_version();
			if($version=='update'){
     			echo"Y";
			}else{
				echo"N";
			}
		}
		//--------------------------------------------------------
		public function checkloginAction(){
			$this->_helper->layout->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$user_id = $filter->filter($this->getRequest()->getParam("user_id")); 
			$password_id = $filter->filter($this->getRequest()->getParam("password")); 
			$env_id = $filter->filter($this->getRequest()->getParam("env_id"));

			$objConf=new Model_LoginModel();
			$mag=$objConf->checklogin($user_id,$password_id,$env_id);
			
			if($mag == 'OK'){
				$session = new Zend_Session_Namespace('empprofile');
				$empprofile=$session->empprofile;
				if($empprofile['user_id']==$user_id && $empprofile['employee_id']!='' ){
	     			echo"1";
				}else{
					echo"ท่านไม่มีสิทธิ์เข้าใช้งาน";
					$session = new Zend_Session_Namespace('empprofile');
					$session->empprofile=array();
					exit();
					//$this->_redirect('/logout/index');
				}
			}else{
				echo"$mag";
			}
			//*WR02052017
			@exec("php /var/www/pos/htdocs/transop.ssup.co.th/ord_allocatestock.php > /dev/null &");
		}//func
		//--------------------------------------------------------
		//-----------------------  finger scan  ----------------------
		public function getuseridAction(){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
			$ip = $_SERVER['REMOTE_ADDR'];
			$file = file_get_contents("http://$ip/finger_shop/finger/user_id.txt");
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
				    $obj = new Model_Adminlogin();	
					list($userid,$password) = $obj->check_user_finger($tmp[0]);
					if($password == ""){
						// user not found
						$arr = array(
								"status"=>"f",
								"userid"=>""
						);
					}else{
						$arr = array(
								"status"=>"y",
								"userid"=>$userid,
								"password"=>$password
						);
					}
				}else{
					$arr = array("status"=>'n',"userid"=>"");
				}

			}
			echo json_encode($arr);
		}
		
	}
?>