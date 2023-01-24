<?php 
	class AdminController extends Zend_Controller_Action{
		//---------------------------------------------------
		public function init(){
			header('Content-type: text/html; charset=utf-8'); 
			header("Cache-Control: no-cache, must-revalidate");
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s")." GMT");
			$this->initView();
			//$session00 = new Zend_Session_Namespace('pos_config');
			//$pos_config=$session00->pos_config;
		}
		//--------------------------------------------------------
		public function testAction(){
			$this->_helper->layout()->disableLayout(); 
			//$this->_helper->layout()->setLayout('/test');
			 echo"<pre>";
			 print_r($_SERVER);
			 echo"</pre>";
			 echo $computer_ip=$_SERVER['HTTP_HOST']; 
		}	
		//--------------------------------------------------------
		public function updateversionposAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('com','com_version_pos');
			$sql="SELECT * FROM com_version_pos  WHERE  1 ";
			$res=$objConf->fetchAllrows($sql);
		
			foreach($res as $data){
				$path=$_SERVER['DOCUMENT_ROOT'];
				$version_old=$data['version_use'];
				$version_new=$data['version_cur'];
				$get_from_ip=$data['get_from_ip'];
				$filetype=$data['filetype'];
				$file_name=$data['file_name'];
				
				//shell_exec("mkdir /var/www/$version_old");
				//shell_exec("chmod 777 -R /var/www/$version_old");
				//shell_exec("cp -R /var/www/pos /var/www/$version_old");
				
				shell_exec("wget http://$get_from_ip/$filetype");
				shell_exec("chmod 777 -R /var/www/$filetype");
				shell_exec("unrar x $filetype");
				shell_exec("mv $file_name /var/www");
				shell_exec("chmod 777 -R /var/www/$file_name");
			}
			
			/*
			$msg= exec('/var/www/pos/htdocs/commit-pook.sh');
			if($msg=='success'){
				$objConf=new Model_Mydbpos();
				$objConf->checkDbOnline('com','com_version_pos');
				$sql="UPDATE `pos_ssup`.`com_version_pos` SET `version_use` = `version_cur`";
				$objConf->runsql($sql);
				echo $msg;
			}else{
				echo 'fail';
			}
			*/
		}
		//--------------------------------------------------------
		public function memberAction(){
			$this->_helper->layout()->disableLayout(); 
		}	
		//--------------------------------------------------------
		public function getdatamemberAction(){
			$this->_helper->layout()->disableLayout(); 
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$id = $filter->filter($this->getRequest()->getParam("id")); 
			$objConf=new Model_Member();
			echo $point->getdatamember($id);
		}	
		//--------------------------------------------------------
		
		public function maptableAction(){
			$this->_helper->layout()->disableLayout(); 
		}	
		//--------------------------------------------------------
		public function treeprocessconfigAction(){
			$this->_helper->layout()->disableLayout();
			$pointter=new Model_Processconfig();
			echo $pointter->treeprocessconfig();
		}	
		//--------------------------------------------------------
		public function tablememberAction(){
				$this->_helper->layout()->disableLayout(); 
				$filter = new Zend_Filter_StripTags(); 
				$query = $filter->filter($this->getRequest()->getParam("query")); 
				$qtype = $filter->filter($this->getRequest()->getParam("qtype")); 
				$page = $filter->filter($this->getRequest()->getParam("page")); 
				$rp = $filter->filter($this->getRequest()->getParam("rp")); 
				$sortname = $filter->filter($this->getRequest()->getParam("sortname")); 
				$sortorder = $filter->filter($this->getRequest()->getParam("sortorder")); 	
				
				$objConf=new Model_Member();
				echo $objConf->tablemember($query,$qtype,$page,$rp,$sortname,$sortorder);
			}	
		//--------------------------------------------------------
		public function indextouchAction(){
			$this->_helper->layout->disableLayout();
		}
		//--------------------------------------------------------
		public function editshopAction(){
			$this->_helper->layout->disableLayout();
		}
		//--------------------------------------------------------
		public function genmarkerAction(){
			$this->_helper->layout->disableLayout();
			$objConf=new Model_Shop();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$shop_id = $filter->filter($this->getRequest()->getParam("shop_id")); 
			$this->view->marker=$objConf->genmarker($corporation_id,$company_id,$shop_id);
		}
		
		//--------------------------------------------------------
		public function indexAction(){
		    $this->_helper->layout()->setLayout('/admin');
			//$this->_helper->layout()->setLayout('/adminipad');
			//$p = new Model_Admin();
			$mydel = new Model_Adminlogin();
			$this->view->login_for=$mydel->login_for();
		}
		//--------------------------------------------------------
		public function versionAction(){
			$this->_helper->layout()->disableLayout(); 
		}
		//--------------------------------------------------------
		public function checkhaveloginAction(){
			$this->_helper->layout()->disableLayout(); 
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			//$user_id=$myprofile['user_id'];
			
			if(empty($myprofile)){
				echo"no";
			}else{
				echo"ok";
			}
		}
		//--------------------------------------------------------
		public function checkuserAction(){
			$this->_helper->layout->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$password_id = $filter->filter($this->getRequest()->getParam("password_id")); 
			$user_id = $filter->filter($this->getRequest()->getParam("user_id")); 
			$env_id = $filter->filter($this->getRequest()->getParam("env_id"));
			
			$objConf=new Model_Adminlogin();
			$mag=$objConf->checkuser($env_id,$password_id,$user_id);
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if($mag == 'OK'){
				if($myprofile['employee_id']!=''){
	     			echo"1";
				}else{
					echo"ท่านไม่มีสิทธิ์เข้าใช้งาน";
				}
			}else{
				echo"$mag";
			}
		}
		//--------------------------------------------------------

		public function logoutAction(){
			    $this->_helper->layout()->disableLayout(); 
			    //Zend_Auth::getInstance()->clearIdentity();
			    //Zend_Session::destroy(true);
				$session = new Zend_Session_Namespace('myprofile');
				$session->myprofile = array();
    			echo"no";
		}
		public function cameraAction(){
			$this->_helper->layout()->disableLayout(); 
		}	
		public function mapsAction(){
			$this->_helper->layout()->disableLayout(); 
		}
		public function getbranchidAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_Mydbpos();
			$sql="SELECT * FROM `com_branch` WHERE `active`='1' ";
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $data){
				$branch_id=$data['branch_id'];
				echo"$branch_id";
			}
		}
		
		
		public function uploadAction(){
			$this->_helper->layout()->disableLayout(); 
			
			// We only need to handle POST requests:
			if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
				exit;
			}
			
			$folder = '/pos/plugin/photobooth/uploads/';
			$filename = md5($_SERVER['REMOTE_ADDR'].rand()).'.jpg';
			
			$original = $folder.$filename;
			
			// The JPEG snapshot is sent as raw input:
			$input = file_get_contents('php://input');
			
			if(md5($input) == '7d4df9cc423720b7f1f3d672b89362be'){
				// Blank image. We don't need this one.
				exit;
			}
			
			$result = file_put_contents($original, $input);
			if (!$result) {
				echo '{
					"error"		: 1,
					"message"	: "Failed save the image. Make sure you chmod the uploads folder and its subfolders to 777."
				}';
				exit;
			}
			
			$info = getimagesize($original);
			if($info['mime'] != 'image/jpeg'){
				unlink($original);
				exit;
			}
			
			// Moving the temporary file to the originals folder:
			rename($original,'/pos/plugin/photobooth/uploads/original/'.$filename);
			$original = '/pos/plugin/photobooth/uploads/original/'.$filename;
			
			// Using the GD library to resize 
			// the image into a thumbnail:
			
			$origImage	= imagecreatefromjpeg($original);
			$newImage	= imagecreatetruecolor(154,110);
			imagecopyresampled($newImage,$origImage,0,0,0,0,154,110,520,370); 
			imagejpeg($newImage,'/pos/plugin/photobooth/uploads/thumbs/'.$filename);
			echo '{"status":1,"message":"Success!","filename":"'.$filename.'"}';
		}
		
		public function browseAction(){
			$this->_helper->layout()->disableLayout(); 
			header('Content-type: application/json');
			$perPage = 24;
			$g = glob('/pos/plugin/photobooth/uploads/thumbs/*.jpg');
			
			if(!$g){
				$g = array();
			}
			
			$names = array();
			$modified = array();
			
			for($i=0,$z=count($g);$i<$z;$i++){
				$path = explode('/',$g[$i]);
				$names[$i] = array_pop($path);
				$modified[$i] = filemtime($g[$i]);
			}
			array_multisort($modified,SORT_DESC,$names);
			$start = 0;
			if(isset($_GET['start']) && strlen($_GET['start'])>1){
				$start = array_search($_GET['start'],$names);
				if($start === false){
					$start = 0;
				}
			}
			$nextStart = '';
			
			if($names[$start+$perPage]){
				$nextStart = $names[$start+$perPage];
			}
			$names = array_slice($names,$start,$perPage);
			
			echo json_encode(array(
				'files' => $names,
				'nextStart'	=> $nextStart
			));
		}

		//--------------------------------------------------------
		public function notesAction(){
			$this->_helper->layout()->disableLayout(); 
		}
		//--------------------------------------------------------
		public function permissionAction(){
			$this->_helper->layout()->disableLayout(); 
		}
		//--------------------------------------------------------
		public function appAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$appid= $filter->filter($this->getRequest()->getParam("appid")); 
			$this->_redirect("/admin/$appid");
		}
		//--------------------------------------------------------
		public function deluserAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$id=$filter->filter($this->getRequest()->getParam("id")); 
			$objConf=new Model_User();
			$objConf->deluser($id);
		}
		//--------------------------------------------------------	
		public function empformAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$id= $filter->filter($this->getRequest()->getParam('id')); 
			$pointter=new Model_User();
			echo $pointter->empform($id);
		}
		//--------------------------------------------------------	
	
		public function settingsAction(){
			$this->_helper->layout()->disableLayout();
		}
		//--------------------------------------------------------	
	
		public function clockAction(){
			$this->_helper->layout()->disableLayout();
		}
		//--------------------------------------------------------	
	
		public function mailAction(){
			$this->_helper->layout()->disableLayout();
		}
		
		//--------------------------------------------------------	
	
		public function imovieAction(){
			$this->_helper->layout()->disableLayout();
		}
		//--------------------------------------------------------	
	
		public function cameraaltAction(){
			$this->_helper->layout()->disableLayout();
		}
			//--------------------------------------------------------	
	
		public function messagesAction(){
			$this->_helper->layout()->disableLayout();
		}
		//--------------------------------------------------------	
		public function companyAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_Company();
			$com_country=$objConf->getdata('com_country','','','');
			
			$com_country_list="<select name='country_id' id='country_id' onchange='search_province(this.value)'>";
			$com_country_list.="<option value='' selected='selected'></option>";
			foreach($com_country as $com_province){
				$country_id=$com_province['country_id'];
				$country_name=$com_province['country_name'];
				$country_id =iconv('UTF-8', 'TIS-620',"$country_id");
				$country_name =iconv('UTF-8', 'TIS-620',"$country_name");
				$com_country_list.="<option value='$country_id'>$country_name</option>";
			}
			$com_country_list.="</select>";
			$this->view->com_country_list=$com_country_list;
			
			$content="";
			$com_company=$objConf->getdata('com_company','','','orderby');
			foreach($com_company as $data){
					$id=$data['id'];
					$logo=$data['logo'];
					$country_id=$data['country_id'];
					$company_name=$data['company_name'];
					$address =$data['address'];
					$road =$data['road'];
					$company_id=$data['company_id'];
					
					$province =$data['province'];
					$amphur =$data['amphur'];
					$district =$data['district'];
					$postcode =$data['postcode'];
					$tel =$data['tel'];
					$fax =$data['fax'];
					$tax_id=$data['tax_id'];
					$active =$data['active'];
					if($active=="Y"){
						$option="ปกติ";
					}else{
						$option="ระงับ";
					}

					$sql0=" SELECT * FROM com_country WHERE country_id='$country_id'";
					$objConf=new Model_Mydbpos();
			    	$res0=$objConf->fetchAllrows($sql0);
			    	if(!empty($res0)){
			    		foreach($res0 as $data1){
			    				$table_province=$data1['table_province'];
						    	$table_amphur=$data1['table_amphur'];
						    	$table_tambon=$data1['table_tambon'];
						    	
						    	$sql1=" SELECT * FROM $table_province WHERE zip_province_id='$province'";
						    	$resprovince1=$objConf->fetchAllrows($sql1);
						    	foreach($resprovince1 as $data2){
								    	$zip_province_id1=$data2['zip_province_id'];
								    	$province=$data2['zip_province_nm_th'];
								    	
							   			$sql2=" SELECT * FROM $table_amphur WHERE zip_amphur_id='$amphur' ";
									    $resamphur=$objConf->fetchAllrows($sql2);
								    	foreach($resamphur as $data3){
										    $amphur=$data3['zip_amphur_nm_th'];
										    $sql3=" SELECT * FROM $table_tambon WHERE zip_tambon_id='$district'";
										    $restambon=$objConf->fetchAllrows($sql3);
										    foreach($restambon as $data4){
										    	  $district=$data4['zip_tambon_nm_th'];
										    }
								    	}
						    	}
			    		}
			
			    	}
					if($logo==""){$logo="/pos/images/upload/index.jpg";}
							$address_all=
							"เลขประจำตัวผู้เสียภาษี   $tax_id <br/>
							ที่อยู่  <br/> 
							$address <br/> 
							ถนน $road <br/>
							แขวง/ตำบล $district เขต /อำเภอ  $amphur <br/>
							จังหวัด $province $postcode <br/>
							เบอร์โทรศัพท์  $tel <br/>
							เบอร์โทรสาร  $fax<br/>
							สถานะบริบัท  $option ";
							
							$content.="<div>
								<img src='$logo' alt=''/>
								<h1>$company_name</h1>
								<p>$address_all</p>
								<a href='#' onclick=\"editcompany('$id')\" class='article'>แก้ไขข้อมูล</a>
								<a href='#' onclick=\"deletecompany('$id','$company_id')\" class='demo'>ลบข้อมูล</a>
							</div>";
					} 
			$this->view->com_company_list=$content;
			
		}
		
		//--------------------------------------------------------
		public function checkduplicateAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$country_id= $filter->filter($this->getRequest()->getParam("country_id"));
			$corporation_id= $filter->filter($this->getRequest()->getParam("corporation_id"));
			$company_id= $filter->filter($this->getRequest()->getParam("company_id"));
			$objConf=new Model_Company();
			echo $objConf->checkduplicate($country_id,$corporation_id,$company_id);
		}
		public function gotopagecompanyAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$texsearch= $filter->filter($this->getRequest()->getParam("texsearch")); 
			$texsearch = strtoupper($texsearch);
			$objConf=new Model_Company();
			echo $objConf->getorderRow($texsearch);
		}
		public function dialogupdatecompanyAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$id= $filter->filter($this->getRequest()->getParam("id")); 
			$objConf=new Model_Company();
			$res =$objConf->getdata('com_company','id',$id,'');
			echo $objConf->dialogupdatecompany($res);
		}
		public function delcompanyAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$id= $filter->filter($this->getRequest()->getParam("id"));  
			$objConf=new Model_Company();
			$objConf->delcompany($id);
		}

		public function provinceAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$table_manq= $filter->filter($this->getRequest()->getParam("table_manq"));
			$act= $filter->filter($this->getRequest()->getParam("act"));
			$objConf=new Model_Company();
			$res=$objConf->getdata('com_country','table_province',$table_manq,'');
			
			foreach($res as $r){
				$table_amphur=$r['table_amphur'];
			}
			
			$com_province=$objConf->getdata($table_manq,'','','');
			if($act=='edit'){
				$com_province_list="<select name='zip_province_id_edit'  id='zip_province_id_edit' onchange=\"search_amphur_edit(this.value,'$table_amphur')\">";
				$com_province_list.="<option value=''></option>";
				foreach($com_province as $province){
					$zip_province_id=$province['zip_province_id'];
					$zip_province_nm_th=$province['zip_province_nm_th'];
					$com_province_list.="<option value='$zip_province_id'>$zip_province_nm_th</option>";
				}
				$com_province_list.="</select>";
		
			}else{
				$com_province_list="<select name='zip_province_id'   id='zip_province_id' onchange=\"search_amphur(this.value,'$table_amphur')\">";
				$com_province_list.="<option value=''></option>";
				foreach($com_province as $province){
					$zip_province_id=$province['zip_province_id'];
					$zip_province_nm_th=$province['zip_province_nm_th'];
					$com_province_list.="<option value='$zip_province_id'>$zip_province_nm_th</option>";
				}
				$com_province_list.="</select>";
			}
			echo"$com_province_list";
		
		}
		
		public function amphurAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$table_manq= $filter->filter($this->getRequest()->getParam("table_manq")); 
			$zip_province_id= $filter->filter($this->getRequest()->getParam("zip_province_id")); 
			$act= $filter->filter($this->getRequest()->getParam("act"));
			
			$objConf=new Model_Company();
			$res=$objConf->getdata('com_country','table_amphur',$table_manq,'');
			
			foreach($res as $r){
				$table_tambon=$r['table_tambon'];
			}
			$com_amphur=$objConf->getdata($table_manq,'zip_province_id',$zip_province_id,'');
			
			if($act=='edit'){
				$com_amphur_list="<select name='zip_amphur_id_edit'  id='zip_amphur_id_edit' onchange=\"search_tambon_edit(this.value,'$table_tambon')\">";
				$com_amphur_list.="<option value=''></option>";
				foreach($com_amphur as $amphur){
					$zip_amphur_id=$amphur['zip_amphur_id'];
					$zip_amphur_nm_th=$amphur['zip_amphur_nm_th'];
					$com_amphur_list.="<option value='$zip_amphur_id'>$zip_amphur_nm_th</option>";
				}
				$com_amphur_list.="</select>";
			}else{
				$com_amphur_list="<select name='zip_amphur_id'  id='zip_amphur_id' onchange=\"search_tambon(this.value,'$table_tambon')\">";
				$com_amphur_list.="<option value=''></option>";
				foreach($com_amphur as $amphur){
					$zip_amphur_id=$amphur['zip_amphur_id'];
					$zip_amphur_nm_th=$amphur['zip_amphur_nm_th'];
					$com_amphur_list.="<option value='$zip_amphur_id'>$zip_amphur_nm_th</option>";
				}
				$com_amphur_list.="</select>";
			}
			

			echo"$com_amphur_list";
		}
		
		public function tambonAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$table_manq= $filter->filter($this->getRequest()->getParam("table_manq")); 
			$zip_amphur_id= $filter->filter($this->getRequest()->getParam("zip_amphur_id")); 
			$act= $filter->filter($this->getRequest()->getParam("act"));
			$objConf=new Model_Company();
			$com_tambon=$objConf->getdata($table_manq,'zip_amphur_id',$zip_amphur_id,'');
			
			if($act=='edit'){
				$com_tambon_list="<select name='zip_tambon_id_edit'  id='zip_tambon_id_edit' onchange=\"search_zipcode_edit(this.value,'$table_manq')\">";
				$com_tambon_list.="<option value=''></option>";
				foreach($com_tambon as $tambon){
					$zip_tambon_id=$tambon['zip_tambon_id'];
					$zip_tambon_nm_th=$tambon['zip_tambon_nm_th'];
					$com_tambon_list.="<option value='$zip_tambon_id'>$zip_tambon_nm_th</option>";
				}
				$com_tambon_list.="</select>";
			}else{
				$com_tambon_list="<select name='zip_tambon_id'   id='zip_tambon_id' onchange=\"search_zipcode(this.value,'$table_manq')\">";
				$com_tambon_list.="<option value=''></option>";
				foreach($com_tambon as $tambon){
					$zip_tambon_id=$tambon['zip_tambon_id'];
					$zip_tambon_nm_th=$tambon['zip_tambon_nm_th'];
					$com_tambon_list.="<option value='$zip_tambon_id'>$zip_tambon_nm_th</option>";
				}
				$com_tambon_list.="</select>";
			}

			echo"$com_tambon_list";
		}
		public function zipcodeAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$table_manq= $filter->filter($this->getRequest()->getParam("table_manq")); 
			$zip_tambon_id= $filter->filter($this->getRequest()->getParam("zip_tambon_id")); 
			$objConf=new Model_Company();
			$res=$objConf->getdata($table_manq,'zip_tambon_id',$zip_tambon_id,'');
			foreach($res as $r){
				$zipcode=$r['zipcode'];
			}
			echo"$zipcode";
		}
		
		public function insertcompanyAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$country_id= $filter->filter($this->getRequest()->getParam("country_id")); 
			$corporation_id= $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id= $filter->filter($this->getRequest()->getParam("company_id")); 
			$company_id = strtoupper($company_id);
			$company_name= $filter->filter($this->getRequest()->getParam("company_name")); 
			$company_name_print = $filter->filter($this->getRequest()->getParam("company_name_print")); 
			
			$address= $filter->filter($this->getRequest()->getParam("address")); 
			$road= $filter->filter($this->getRequest()->getParam("road")); 
			$zip_province_id= $filter->filter($this->getRequest()->getParam("zip_province_id")); 
			$zip_amphur_id= $filter->filter($this->getRequest()->getParam("zip_amphur_id")); 
			$zip_tambon_id= $filter->filter($this->getRequest()->getParam("zip_tambon_id")); 
			$postcode= $filter->filter($this->getRequest()->getParam("postcode")); 
			$tel= $filter->filter($this->getRequest()->getParam("tel")); 
			$fax= $filter->filter($this->getRequest()->getParam("fax")); 
			$tax_id= $filter->filter($this->getRequest()->getParam("tax_id")); 
			$active= $filter->filter($this->getRequest()->getParam("active")); 
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			
			$user_id=$myprofile['user_id'];
			$logo=$_FILES['logo']['name'];
			$pathinsret="/pos/images/upload/$company_id/$logo";
			
		   	$pathlogo=$_SERVER['DOCUMENT_ROOT']."/pos/images/upload/$company_id/";
			if(!is_dir($pathlogo)){
				mkdir("$pathlogo",0777,TRUE);
			}
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$adapter->setDestination($pathlogo);
			if (!$adapter->receive()) {
				$logo=$pathinsret;
			}
			
			$rows=array(
				"country_id"=>$country_id,
				"corporation_id"=>$corporation_id,
				"company_id"=>$company_id,
				"company_name"=>$company_name,
				"company_name_print"=>$company_name_print,
				"address"=>$address,
				"road"=>$road,
				"province"=>$zip_province_id,
				"amphur"=>$zip_amphur_id,
				"district"=>$zip_tambon_id,
				"postcode"=>$postcode,
				"tel"=>$tel,
				"fax"=>$fax,
				"tax_id"=>$tax_id,
				"active"=>$active,
				"logo"=>$pathinsret,
				"reg_date"=>date('Y-m-d'),
				"reg_time"=>date('H:i:s'),
				"reg_user"=>$user_id
			);
			
			$objConf=new Model_Company();
			$run=$objConf->insertcompany($rows);
			if($run){
				echo'<Meta http-equiv="refresh"content="1;URL=/pos/admin/company?menu_exec=company">';
			}
		}
		public function updatecompanyAction(){
			$this->_helper->layout()->disableLayout(); 
			
			$filter = new Zend_Filter_StripTags(); 
			$id= $filter->filter($this->getRequest()->getParam("id_edit"));
		
			$country_id= $filter->filter($this->getRequest()->getParam("country_id_edit")); 
			$corporation_id= $filter->filter($this->getRequest()->getParam("corporation_id_edit")); 
			$company_id= $filter->filter($this->getRequest()->getParam("company_id_edit")); 
			$company_id = strtoupper($company_id);
			$company_name= $filter->filter($this->getRequest()->getParam("company_name_edit")); 
			$company_name_print_edit= $filter->filter($this->getRequest()->getParam("company_name_print_edit")); 
			$address= $filter->filter($this->getRequest()->getParam("address_edit")); 
			$road= $filter->filter($this->getRequest()->getParam("road_edit")); 
			$zip_province_id= $filter->filter($this->getRequest()->getParam("zip_province_id_edit")); 
			$zip_amphur_id= $filter->filter($this->getRequest()->getParam("zip_amphur_id_edit")); 
			$zip_tambon_id= $filter->filter($this->getRequest()->getParam("zip_tambon_id_edit")); 
			$postcode= $filter->filter($this->getRequest()->getParam("postcode_edit")); 
			$tel= $filter->filter($this->getRequest()->getParam("tel_edit")); 
			$fax= $filter->filter($this->getRequest()->getParam("fax_edit")); 
			$tax_id= $filter->filter($this->getRequest()->getParam("tax_id_edit")); 
			$active= $filter->filter($this->getRequest()->getParam("active_edit")); 
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$user_id=$myprofile['user_id'];
			
			$logo=$_FILES['logo_edit']['name'];
			$pathinsret="/pos/images/upload/$company_id/$logo";
		   	$pathlogo=$_SERVER['DOCUMENT_ROOT']."/pos/images/upload/$company_id/";
		   	
			if(!is_dir($pathlogo)){
				mkdir("$pathlogo",0777,TRUE);
			}
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$adapter->setDestination($pathlogo);
			if (!$adapter->receive()) {
				$logo=$pathinsret;
			}
			
			$rows=array(
				"country_id"=>$country_id,
				"corporation_id"=>$corporation_id,
				"company_id"=>$company_id,
				"company_name"=>$company_name,
				"company_name_print"=>$company_name_print_edit,
				"address"=>$address,
				"road"=>$road,
				"province"=>$zip_province_id,
				"amphur"=>$zip_amphur_id,
				"district"=>$zip_tambon_id,
				"postcode"=>$postcode,
				"tel"=>$tel,
				"fax"=>$fax,
				"tax_id"=>$tax_id,
				"active"=>$active,
				"logo"=>$pathinsret,
				"upd_date"=>date('Y-m-d'),
				"upd_time"=>date('H:i:s'),
				"upd_user"=>$user_id
			);
			
			//print_r($myprofile);
			$where=" id='$id' ";
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('com','com_company');
			$objConf->updatedata('com_company',$rows,$where);
			echo'<Meta http-equiv="refresh"content="1;URL=/pos/admin/company?menu_exec=company">';
		}
		
		
		public function gettablemanqAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$vals= $filter->filter($this->getRequest()->getParam("vals")); 
			$objConf=new Model_Company();
			$res=$objConf->getdata('com_country','country_id',$vals,'');

			foreach($res as $r){
				$table_province=$r['table_province'];
			}
			echo"$table_province";
		}
		
		
	//--------------------------------------------------------	
	//permission
	//--------------------------------------------------------
		public function  delpermissionAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$id = $filter->filter($this->getRequest()->getParam("id")); 
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id"));  
			$objConf=new Model_Permission();
			$objConf->delpermission($perm_id,$id);
		}
		
		//--------------------------------------------------------
		public function  delmenuinoermissionAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id")); 
			$objConf=new Model_Permission();
			$objConf->delmenuinoermission($perm_id,$menu_id);
		}	
	//--------------------------------------------------------
	
		public function formmenusystemAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$menu_ref= $filter->filter($this->getRequest()->getParam("menu_ref")); 
			$sql0="SELECT COUNT(menu_ref) AS count_menu_ref FROM `com_menu` WHERE `menu_ref` = '$menu_ref' ";
			$objConf=new Model_Mydbpos();
			$res0=$objConf->fetchAllrows($sql0);
			echo $count_menu_ref=$res0[0]['count_menu_ref']+1;
		}
		
		public function addcommenuidAction(){
			$this->_helper->layout()->disableLayout(); 
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$user_id=$myprofile['user_id'];
			
			$filter = new Zend_Filter_StripTags(); 
			$menu_ref = $filter->filter($this->getRequest()->getParam("menu_ref"));
			$menu_seq = $filter->filter($this->getRequest()->getParam("menu_seq"));
			$menu_name = $filter->filter($this->getRequest()->getParam("menu_name"));
			$menu_exec = $filter->filter($this->getRequest()->getParam("menu_exec"));
			$status_menu = $filter->filter($this->getRequest()->getParam("status_menu"));
			
			$sql0="SELECT *  FROM `com_menu` WHERE `menu_id` LIKE '$menu_ref' ";
			$objConf=new Model_Mydbpos();
			$res0=$objConf->fetchAllrows($sql0);
			$menu_level=$res0[0]['menu_level']+1;
			$type_menu=$res0[0]['type_menu'];
			
			if($type_menu=="mainmenu"){
				$type_menu="program";
			}else{
				$type_menu="file";
			}
			$data = array(
					'menu_ref'=>$menu_ref,
					'menu_seq'=>$menu_seq,
					'menu_name'=>$menu_name,
					'menu_exec'=>$menu_exec,
					'status_menu'=>$status_menu,
					'menu_level'=>$menu_level,
					'type_menu'=>$type_menu,
					'reg_date'=>date('Y-m-d'),
					'reg_time'=>date('H:i:s'),
					'reg_user'=>$user_id
			);
			
			//print_r($data);
			$table='com_menu';
			$id=$objConf->insertdata($table,$data);
	
			if($id !=""){
				$menu_picture=$_FILES['menu_picture']['name'];
				$menu_picture_size=$_FILES['menu_picture']['size'];
				if($menu_picture_size > 0){
						$pathmenu_picture=$_SERVER['DOCUMENT_ROOT']."/pos/images/icon/sysmenu/$menu_ref";
						//$pathinsret="/pos/images/icon/sysmenu/$menu_ref";
						if(!is_dir($pathmenu_picture)){
							mkdir("$pathmenu_picture",0777,TRUE);
						}
						$pathinsret="/pos/images/icon/sysmenu/$menu_ref/$menu_picture";
						if(!file_exists($pathinsret)) {
							$adapter = new Zend_File_Transfer_Adapter_Http();
							$adapter->setDestination($pathmenu_picture);
							if(!$adapter->receive()) {
								$menu_picture=$pathinsret;
							}
						}
				}else{
					$pathinsret="";
				}	
				$where="id='$id'";
				$data = array('menu_id'=>"menu$id",'menu_picture'=>$pathinsret);
				$objConf->updatedata($table,$data,$where);
				$msg="ok";
			}else{
				$msg="error";
			}
			print json_encode(array(
				"success" => $msg,
				"failure" => false,
				"file_name" => $menu_picture, 
				"size" => $menu_picture_size
			));
		}
	//--------------------------------------------------------
		public function updatecommenuidAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$menu_ref = $filter->filter($this->getRequest()->getParam("menu_ref"));
			$menu_seq = $filter->filter($this->getRequest()->getParam("menu_seq"));
			$menu_name = $filter->filter($this->getRequest()->getParam("menu_name"));
			$menu_exec = $filter->filter($this->getRequest()->getParam("menu_exec"));
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));
			$status_menu = $filter->filter($this->getRequest()->getParam("status_menu"));
			$menu_picture_size=$_FILES['menu_picture']['size'];	
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$user_id=$myprofile['user_id'];
			$objConf=new Model_Mydbpos();
			$sql0="SELECT *  FROM `com_menu` WHERE `menu_id` LIKE '$menu_id' ";
			$res0=$objConf->fetchAllrows($sql0);
			foreach($res0 as $data){
				 $menu_picture=$data['menu_picture'];
			}
			if($menu_picture_size > 0 or $menu_picture_size !=""){
					$dirname=$_SERVER['DOCUMENT_ROOT'].$data['menu_picture'];
				 	unlink($dirname);
					$menu_picture_size=$_FILES['menu_picture']['size'];	
					$menu_picture=$_FILES['menu_picture']['name'];
					$pathmenu_picture=$_SERVER['DOCUMENT_ROOT']."/pos/images/icon/sysmenu/$menu_ref";
					//$pathinsret="/pos/images/icon/sysmenu/$menu_ref";
					if(!is_dir($pathmenu_picture)){
						mkdir("$pathmenu_picture",0777,TRUE);
					}
					$pathinsret="/pos/images/icon/sysmenu/$menu_ref/$menu_picture";
					if(!file_exists($pathinsret)) {
						$adapter = new Zend_File_Transfer_Adapter_Http();
						$adapter->setDestination($pathmenu_picture);
						if(!$adapter->receive()) {
							$menu_picture=$pathinsret;
						}
					}
			}else{
				$pathinsret="";
				$menu_picture_size=0;
			}
	
			$data = array(
					'menu_seq'=>$menu_seq,
					'menu_name'=>$menu_name,
					'menu_exec'=>$menu_exec,
					'menu_picture'=>$pathinsret,
					'status_menu'=>$status_menu,
					'upd_date'=>date('Y-m-d'),
					'upd_time'=>date('H:i:s'),
					'upd_user'=>$user_id
			);
			//print_r($data);
			
			$table='com_menu';
			$where="menu_id='$menu_id'";
			$uu=$objConf->updatedata($table,$data,$where);
		
			$msg="ok";
			print json_encode(array(
				"success" => $msg,
				"failure" => false,
				"file_name" => $menu_picture, 
				"size" => $menu_picture_size
			));
		}
		
		public function updatecommenuidnoimgAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$menu_ref = $filter->filter($this->getRequest()->getParam("menu_ref"));
			$menu_seq = $filter->filter($this->getRequest()->getParam("menu_seq"));
			$menu_name = $filter->filter($this->getRequest()->getParam("menu_name"));
			$menu_exec = $filter->filter($this->getRequest()->getParam("menu_exec"));
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));
			$status_menu = $filter->filter($this->getRequest()->getParam("status_menu"));
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			
			$user_id=$myprofile['user_id'];
			$objConf=new Model_Mydbpos();
			$data = array(
					'menu_seq'=>$menu_seq,
					'menu_name'=>$menu_name,
					'menu_exec'=>$menu_exec,
					'status_menu'=>$status_menu,
					'upd_date'=>date('Y-m-d'),
					'upd_time'=>date('H:i:s'),
					'upd_user'=>$user_id
			);
			
			$table='com_menu';
			$where="menu_id='$menu_id'";
			$run=$objConf->updatedata($table,$data,$where);
			
			if($run==0){
				$sql="
				UPDATE com_menu 
				SET 
				menu_seq = '$menu_seq', 
				menu_name = '$menu_name', 
				menu_exec = '$menu_exec', 
				status_menu = '$status_menu',
				upd_date = date('Y-m-d'),
				upd_time = date('H:i:s'), 
				upd_user = '$user_id' 
				WHERE menu_id = '$menu_id' ";
				//echo $sql;
				$run=$objConf->runsql($sql);
			}
			
		}
		//--------------------------------------------------------
		public function delcommenuidAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));  
			$ran = $filter->filter($this->getRequest()->getParam("ran"));  
			$objConf=new Model_Permission();
			$run=$objConf->delmenu($menu_id);	
			echo $ran;
		}
		//--------------------------------------------------------
		public function delcommenuidnomgAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));  
			$ran = $filter->filter($this->getRequest()->getParam("ran"));  
			$objConf=new Model_Permission();
			$run=$objConf->delcommenuidnomg($menu_id);	
			echo $ran;
		}
		//--------------------------------------------------------
		public function editcommenuidAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));  
			$point = new Model_Permission(); 
			echo $point->editcommenuid($menu_id);
		}

		//--------------------------------------------------------
		public function treepermissionAction(){
			$this->_helper->layout()->disableLayout(); 
			//$filter = new Zend_Filter_StripTags();
			//$perm_id = $filter->filter($this->getRequest()->getParam("perm_id"));  
			$point = new Model_Permission(); 
			echo $point->com_permission();
		}
	//--------------------------------------------------------
		public function removemenuinperAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));  
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$objConf=new Model_Permission();
			echo $objConf->removemenuinper($menu_id,$perm_id);
		}
		public function removepermissionAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$objConf=new Model_Permission();
			echo $objConf->removepermission($perm_id);
		}
		public function addpermissionAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$remark = $filter->filter($this->getRequest()->getParam("remark")); 

			$objConf=new Model_Permission();
			echo $objConf->addpermission($perm_id,$remark);
		}
		public function updatepermissionAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$remark = $filter->filter($this->getRequest()->getParam("remark")); 
			$perm_id_old = $filter->filter($this->getRequest()->getParam("perm_id_old")); 
			$ran = $filter->filter($this->getRequest()->getParam("perm_id_old")); 
			
			$objConf=new Model_Permission();
			$objConf->updatepermission($perm_id,$remark,$perm_id_old);
			echo $ran;
		}
		
		//--------------------------------------------------------
		public function pastemenuAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));  
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$ran = $filter->filter($this->getRequest()->getParam("ran")); 
			$objConf=new Model_Permission();
			$objConf->pastemenu($menu_id,$perm_id);
			echo $ran;
		}
		//--------------------------------------------------------
		public function checkdupperAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id"));   
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$objConf=new Model_Permission();
			echo $objConf->checkdupper($menu_id,$perm_id);
		}
			//--------------------------------------------------------
		public function checkperingroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));  
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id"));  
			$group_id = $filter->filter($this->getRequest()->getParam("group_id")); 
			$objConf=new Model_Permission();
			echo $objConf->checkperingroup($corporation_id,$company_id,$perm_id,$group_id);
		}
		//---------------------------------------------------------------------------------------		
		public function treesysmenuAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("id")); 
			$pointter=new Model_Permission();
			echo $pointter->treesysmenu($menu_id);
		}
		//---------------------------------------------------------------------------------------		
		public function addmenusepAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id")); 
			$pointter=new Model_Permission();
			echo $pointter->addmenusep($menu_id);
		}	
	//---------------------------------------------------------------------------------------	
		public function treeusergroupAction(){
			$this->_helper->layout()->disableLayout(); 
			$pointter=new Model_Permission();
			echo $pointter->treeusergroup();
		}
	//---------------------------------------------------------------------------------------	
		public function pastepertogroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$group_id = $filter->filter($this->getRequest()->getParam("group_id"));  
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$objConf=new Model_Permission();
			echo $objConf->pastepertogroup($corporation_id,$company_id,$perm_id,$group_id);
		}
		//---------------------------------------------------------------------------------------		
		public function removegroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id"));  
			$objConf=new Model_Permission();
			echo $objConf->removegroup($corporation_id,$company_id,$group_id);
		}
		//---------------------------------------------------------------------------------------	
		public function removepermidingroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$perm_id = $filter->filter($this->getRequest()->getParam("perm_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id"));   
			$objConf=new Model_Permission();
			echo $objConf->removepermidingroup($corporation_id,$company_id,$perm_id,$group_id);
		}
		//---------------------------------------------------------------------------------------	
		public function insertnewgroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id")); 
			$cancel = $filter->filter($this->getRequest()->getParam("cancel")); 
			$remark = $filter->filter($this->getRequest()->getParam("remark"));   
			$objConf=new Model_Mydbpos();
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			
			$user_id=$myprofile['user_id'];
			$data = array(
					'corporation_id'=>$corporation_id,
					'company_id'=>$company_id,
					'group_id'=>$group_id,
					'cancel'=>$cancel,
					'remark'=>$remark,
					'reg_date'=>date('Y-m-d'),
					'reg_time'=>date('H:i:s'),
					'reg_user'=>$user_id
				);
			$table='conf_usergroup';
			$id=$objConf->insertdata($table,$data);
		}
			//---------------------------------------------------------------------------------------	
		public function checkhavegroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id"));  
			$objConf=new Model_Permission();
			echo $objConf->checkhavegroup($corporation_id,$company_id,$group_id);
		}	
		//---------------------------------------------------------------------------------------	
		public function updategroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id")); 
			$group_id_old = $filter->filter($this->getRequest()->getParam("group_id_old")); 
			$cancel = $filter->filter($this->getRequest()->getParam("cancel")); 
			$remark = $filter->filter($this->getRequest()->getParam("remark")); 
			$objConf=new Model_Permission();
			$objConf->updategroup($corporation_id,$company_id,$group_id,$group_id_old,$cancel,$remark);
		}
		//---------------------------------------------------------------------------------------	
		public function editgroupAction(){			
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags();
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id")); 
			$objConf=new Model_Permission();
			echo $objConf->editgroup($corporation_id,$company_id,$group_id);
		}
		//---------------------------------------------------------------------------------------	
		//user

		public function userAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_User();
			$res=$objConf->corporation_id();
			
			$corporation_id_list="<select name='gcorporation_id' id='gcorporation_id' onchange='searchcompany(this.value)'>";
			$corporation_id_list.="<option value=''></option>";
			foreach($res as $corporation_id){
				$corporation_id=$corporation_id['corporation_id'];
				$corporation_id_list.="<option value='$corporation_id'>$corporation_id</option>";
			}
			$corporation_id_list.="</select>";
			$this->view->gcorporation_id_list=$corporation_id_list;
			
			$res2=$objConf->corporation_id();
			$corporation_id_list2="<select name='corporation_id' id='corporation_id' onchange='searchcompany2(this.value)'>";
			$corporation_id_list2.="<option value=''></option>";
			foreach($res2 as $corporation_id2){
				$corporation_id22=$corporation_id2['corporation_id'];
				$corporation_id_list2.="<option value='$corporation_id22'>$corporation_id22</option>";
			}
			$corporation_id_list2.="</select>";
			$this->view->corporation_id_list=$corporation_id_list2;
			
			$res3=$objConf->com_permission();
			$list3="<select name='perm_id' id='perm_id'>";
			$list3.="<option value=''></option>";
			foreach($res3 as $arr){
				$perm_id=$arr['perm_id'];
				$list3.="<option value='$perm_id'>$perm_id</option>";
			}
			$list3.="</select>";
			$this->view->perm_id=$list3;
			
		}
			//--------------------------------------------------------	
		public function tgroupAction(){
			$this->_helper->layout()->disableLayout();
			$pointter=new Model_User();
			echo $pointter->treecom_country();
		}
		//--------------------------------------------------------	
		public function tableusergroupAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			//$company_id= $filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id= $filter->filter($this->getRequest()->getParam("group_id")); 
			$query = $filter->filter($this->getRequest()->getParam("query")); 
			$qtype = $filter->filter($this->getRequest()->getParam("qtype")); 
			$page = $filter->filter($this->getRequest()->getParam("page")); 
			$rp = $filter->filter($this->getRequest()->getParam("rp")); 
			$sortname = $filter->filter($this->getRequest()->getParam("sortname")); 
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder")); 	
			
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));
			$company_id = $filter->filter($this->getRequest()->getParam("company_id"));
			$clicknode = $filter->filter($this->getRequest()->getParam("clicknode"));
			
			$objConf=new Model_User();
			$table_name="conf_employee";
			echo $objConf->tableusergroup($corporation_id,$company_id,$clicknode,$group_id,$table_name,$query,$qtype,$page,$rp,$sortname,$sortorder);
		}
		//--------------------------------------------------------
			public function activegroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$group_id=$filter->filter($this->getRequest()->getParam("group_id")); 
			$corporation_id=$filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id=$filter->filter($this->getRequest()->getParam("company_id")); 
			$objConf=new Model_User();
			$objConf->activegroup($group_id,$corporation_id,$company_id);
		}
			//--------------------------------------------------------	
		public function checkdupusertogroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id=$filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id=$filter->filter($this->getRequest()->getParam("company_id")); 
			$employee_id=$filter->filter($this->getRequest()->getParam("employee_id")); 
			$user_id=$filter->filter($this->getRequest()->getParam("user_id")); 
			$objConf=new Model_User();
			echo $objConf->checkdupusertogroup($corporation_id,$company_id,$employee_id,$user_id);
		}
		public function delgroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id=$filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id=$filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id=$filter->filter($this->getRequest()->getParam("group_id")); 
			
			$objConf=new Model_User();
			$objConf->delgroup($corporation_id,$company_id,$group_id);
		}

		//--------------------------------------------------------
		public function searchgroupAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$company_id = $filter->filter($this->getRequest()->getParam('company_id')); 
			$corporation_id = $filter->filter($this->getRequest()->getParam('corporation_id')); 
	
			$objConf=new Model_User();
			$res=$objConf->getusergroup($corporation_id,$company_id);
			$list="<select name='group_id_in' id='group_id_in'>";
			$list.="<option value=''></option>";
			foreach($res as $arr){
				$group_id=$arr['group_id'];
				$list.="<option value='$group_id'>$group_id</option>";
			}
			$list.="</select>";
			echo $list;
		}
		
			
		public function cancelgroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$group_id=$filter->filter($this->getRequest()->getParam("group_id")); 
			$corporation_id=$filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id=$filter->filter($this->getRequest()->getParam("company_id")); 
			$objConf=new Model_User();
			$objConf->cancelgroup($group_id,$corporation_id,$company_id);
		}
			//--------------------------------------------------------	
		public function checkdupgroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id=$filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id=$filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id=$filter->filter($this->getRequest()->getParam("group_id")); 
			$objConf=new Model_User();
			echo $objConf->checkdupgroup($corporation_id,$company_id,$group_id);
		}
			//--------------------------------------------------------	
		public function insertusertogroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id")); 
			$user_id_insert= $filter->filter($this->getRequest()->getParam("user_id")); 
			$password_id = $filter->filter($this->getRequest()->getParam("password_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id")); 
			$employee_id = $filter->filter($this->getRequest()->getParam("employee_id")); 
			$name= $filter->filter($this->getRequest()->getParam("name")); 
			$surname = $filter->filter($this->getRequest()->getParam("surname")); 
			$position = $filter->filter($this->getRequest()->getParam("position")); 
			$start_date = $filter->filter($this->getRequest()->getParam("start_date")); 
			$end_date = $filter->filter($this->getRequest()->getParam("end_date")); 
			$cancel = $filter->filter($this->getRequest()->getParam("cancel")); 
			
			
			
			
			

			
			
			
			
			$remark = $filter->filter($this->getRequest()->getParam("remark")); 
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$user_id=$myprofile['user_id'];
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('conf','conf_employee');
			
			
			$sql0="SELECT *  FROM `conf_usergroup` WHERE `group_id` LIKE '$group_id' ";
			$res0=$objConf->fetchAllrows($sql0);
			foreach($res0 as $data){
				 $user_level=$data['user_level'];
			}
			if(count($res0)<1){
				$user_level=1;
			}
			
			
			$data = array(
				"corporation_id"=>$corporation_id,
				"company_id"=>$company_id,
				"branch_id"=>$branch_id,
				"employee_id"=>$employee_id,
				"user_id"=>$user_id_insert,
				"password_id"=>$password_id,
				"group_id"=>$group_id,
				"name"=>$name,
				"surname"=>$surname,
				"position"=>$position,
				"start_date"=>$start_date,
				"end_date"=>$end_date,
				"cancel"=>$cancel,
				"remark"=>$remark,
				'reg_date'=>date('Y-m-d'),
				'reg_time'=>date('H:i:s'),
				'reg_user'=>$user_id,
				'user_level'=>$user_level
			);
			echo $objConf->insertdata('conf_employee',$data);
	
			
			
			
			
			
			if($group_id=="Developer"){
					$sql0="SELECT *  FROM `conf_login` WHERE `user_id` LIKE '$user_id_insert' ";
					$res0=$objConf->fetchAllrows($sql0);
					if(count($res0>1)){
						foreach($res0 as $data0){
						 	$data_1 = array(
								"corporation_id"=>$corporation_id,
								"company_id"=>$company_id,
								"user_id"=>$user_id_insert,
								"password_id"=>$password_id,
								"group_id"=>$group_id,
								"ip_regis"=>$remark,
								'upd_date'=>date('Y-m-d'),
								'upd_time'=>date('H:i:s'),
								'upd_user'=>$user_id
							);
							$id=$data0['id'];
							$table='conf_login';
							$where="id='$id'";
							$objConf->updatedata($table,$data_1,$where);
						}
					}else{	
							$data_1 = array(
								"corporation_id"=>$corporation_id,
								"company_id"=>$company_id,
								"ip_regis"=>$remark,
								"user_id"=>$user_id_insert,
								"password_id"=>$password_id,
								"group_id"=>$group_id,
								'reg_date'=>date('Y-m-d'),
								'reg_time'=>date('H:i:s'),
								'reg_user'=>$user_id
							);
							$objConf->insertdata('conf_login',$data_1);
					}
			}
	}
		
	public function updateusertogroupAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id")); 
			$user_id_update= $filter->filter($this->getRequest()->getParam("user_id")); 
			$password_id = $filter->filter($this->getRequest()->getParam("password_id")); 
			$group_id= $filter->filter($this->getRequest()->getParam("group_id")); 
			$employee_id = $filter->filter($this->getRequest()->getParam("employee_id")); 
			$name= $filter->filter($this->getRequest()->getParam("name")); 
			$surname = $filter->filter($this->getRequest()->getParam("surname")); 
			$position = $filter->filter($this->getRequest()->getParam("position")); 
			$start_date = $filter->filter($this->getRequest()->getParam("start_date")); 
			$end_date = $filter->filter($this->getRequest()->getParam("end_date")); 
			$cancel = $filter->filter($this->getRequest()->getParam("cancel")); 
			$remark = $filter->filter($this->getRequest()->getParam("remark")); 
			$idemp = $filter->filter($this->getRequest()->getParam("idemp")); 
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			
			$user_id=$myprofile['user_id'];
	
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('conf','conf_employee');

			
			$sql0="SELECT *  FROM `conf_usergroup` WHERE `group_id` LIKE '$group_id' ";
			$res0=$objConf->fetchAllrows($sql0);
			foreach($res0 as $data){
				 $user_level=$data['user_level'];
			}
			if(count($res0)<1){
				$user_level=1;
			}
			
			
			
			
			$data = array(
				"corporation_id"=>$corporation_id,
				"company_id"=>$company_id,
				"branch_id"=>$branch_id,
				"employee_id"=>$employee_id,
				"user_id"=>$user_id_update,
				"password_id"=>$password_id,
				"group_id"=>$group_id,
				"name"=>$name,
				"surname"=>$surname,
				"position"=>$position,
				"start_date"=>$start_date,
				"end_date"=>$end_date,
				"cancel"=>$cancel,
				"remark"=>$remark,
				'upd_date'=>date('Y-m-d'),
				'upd_time'=>date('H:i:s'),
				'upd_user'=>$user_id,
				'user_level'=>$user_level
			);
			$table='conf_employee';
			$where="id='$idemp'";
			$objConf->updatedata($table,$data,$where);
		}	
		//---------------------------------------------------------------------------------------	
		public function groupbranchAction(){
			$this->_helper->layout()->disableLayout(); 
		}	
	//---------------------------------------------------------------------------------------	
		public function insertgroupAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id=$filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id=$filter->filter($this->getRequest()->getParam("company_id")); 
			$group_id=$filter->filter($this->getRequest()->getParam("group_id")); 
			$cancel=$filter->filter($this->getRequest()->getParam("cancel")); 
			$remark=$filter->filter($this->getRequest()->getParam("remark")); 
			$remarks=$filter->filter($this->getRequest()->getParam("remark")); 
		
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			
			
		    if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$user_id=$myprofile['user_id'];
			
			
			$objConf=new Model_Mydbpos();
			
			$data = array(
				'corporation_id'=>$corporation_id,
				'company_id'=>$company_id,
				'group_id'=>$group_id,
				'cancel'=>$cancel,
				'remark'=>$remark,
				'reg_date'=>date('Y-m-d'),
				'reg_time'=>date('H:i:s'),
				'reg_user'=>$user_id
			);
			$table='conf_usergroup';
			$id=$objConf->insertdata($table,$data);
			$rows=array();
			array_push($rows,array(
			'id'=>"group_id@$corporation_id@$company_id@$group_id@$remarks",
			'text'=>$group_id,
			'checked'=>true));
			echo json_encode($rows); 
		}	

//--------------------------------------------------------		
		public function insertplacestandAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$country_id = $filter->filter($this->getRequest()->getParam("country_id")); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$shop_number = $filter->filter($this->getRequest()->getParam("shop_number")); 
			$province_id = $filter->filter($this->getRequest()->getParam("province_id")); 
			$amphur_id= $filter->filter($this->getRequest()->getParam("amphur_id")); 
			$tambon_id = $filter->filter($this->getRequest()->getParam("tambon_id")); 
			$group_id = $filter->filter($this->getRequest()->getParam("group_id")); 
			$address = $filter->filter($this->getRequest()->getParam("address")); 
			$road = $filter->filter($this->getRequest()->getParam("road")); 
			$lane = $filter->filter($this->getRequest()->getParam("lane")); 
			$placestand = $filter->filter($this->getRequest()->getParam("placestand")); 
			$floor = $filter->filter($this->getRequest()->getParam("floor")); 
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			$user_id=$myprofile['user_id'];
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('conf','conf_employee');

			
			$sql0="SELECT *  FROM `conf_usergroup` WHERE `group_id` LIKE '$group_id' ";
			$res0=$objConf->fetchAllrows($sql0);
			foreach($res0 as $data){
				 $user_level=$data['user_level'];
			}
			if(count($res0)<1){
				$user_level=1;
			}
			
			
			
			$data = array(
				'corporation_id'=>$corporation_id,
				'company_id'=>$company_id,
				'shop_number'=>$shop_number,
				'placestand'=>$placestand,
				'address'=>$address,
				'group_id'=>$group_id,
				'lane'=>$lane,
				'road'=>$road,
				'provinct_id'=>$province_id,
				'amphur_id'=>$amphur_id,
				'tambon_id'=>$tambon_id,
				'floor'=>$floor,
				'reg_date'=>date('Y-m-d'),
				'reg_time'=>date('H:i:s'),
				'reg_user'=>$user_id,
				'user_level'=>$user_level
			);
			
			echo $objConf->insertdata('conf_shop',$data);
		}	

//---------------------------------------------------------------------------------------			
		public function checkhaveimagesAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$logo=$filter->filter($this->getRequest()->getParam("logo")); 
			$pathlogo=$_SERVER['DOCUMENT_ROOT']."$logo";
			if(!is_dir($pathlogo)){
				echo"no";
			}		
		}
//---------------------------------------------------------------------------------------		
		public function shopdetailAction(){
			$this->_helper->layout()->disableLayout();
		}		
//--------------------------------------------------------		
		public function shopdataAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_Shop();
			$this->view->com_country=$objConf->com_country();
		}			
//--------------------------------------------------------		
		public function forminsertmapAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$shop_number = $filter->filter($this->getRequest()->getParam("shop_number"));  
			$this->view->shop_number=$shop_number;
		}				
//--------------------------------------------------------		
		public function getcorporationidAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$country_id = $filter->filter($this->getRequest()->getParam("country_id")); 
			
			$objConf=new Model_Shop();
			echo $objConf->getcorporationid($country_id);
		}
//--------------------------------------------------------		
		public function getcompanyidAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$objConf=new Model_Shop();
			echo $objConf->getcompanyid($corporation_id);
		}	
//--------------------------------------------------------		
		public function tableemployeeAction(){
			$this->_helper->layout()->disableLayout();
		}	
//--------------------------------------------------------	
		public function processconfigAction(){
			$this->_helper->layout()->disableLayout();
		}	
//--------------------------------------------------------		
		public function processconfigtreeAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$operation = $filter->filter($this->getRequest()->getParam("operation")); 
			$id = $filter->filter($this->getRequest()->getParam("id")); 
			
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('conf','conf_process_tree');
		
			$jstree = new Model_TreeProcess();
			$jstree->$operation($id);
		
			//echo var_dump(method_exists($jstree,$operation));
			
			//if($operation && strpos($operation, "_") !== 0 && method_exists($jstree, $operation)) {
			if($operation && strpos($operation, "_") !== 0 && method_exists($jstree, $operation)) {	
				header("HTTP/1.0 200 OK");
				header('Content-type: application/json; charset=utf-8');
				header("Cache-Control: no-cache, must-revalidate");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Pragma: no-cache");
				echo "$operation";
				
				
				//echo $jstree->{$operation}($_REQUEST);
				//die();
			}
		}	
//--------------------------------------------------------	
		public function processconfigdetailAction(){
			$this->_helper->layout()->disableLayout();
		}	

//--------------------------------------------------------	
		public function transferAction(){
			$this->_helper->layout()->disableLayout();
		}	
//--------------------------------------------------------	
		public function replicationAction(){
			$this->_helper->layout()->disableLayout();
		}		
//--------------------------------------------------------	
		public function tableshopAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$query = $filter->filter($this->getRequest()->getParam("query")); 
			$qtype = $filter->filter($this->getRequest()->getParam("qtype")); 
			$page = $filter->filter($this->getRequest()->getParam("page")); 
			$rp = $filter->filter($this->getRequest()->getParam("rp")); 
			$sortname = $filter->filter($this->getRequest()->getParam("sortname")); 
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder")); 
				
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$shop_id = $filter->filter($this->getRequest()->getParam("shop_id")); 
			$shop_name = $filter->filter($this->getRequest()->getParam("shop_name")); 
			
			$objConf=new Model_Shop();
			echo $objConf->tableshop($corporation_id,$company_id,$shop_id,$shop_name,$query,$qtype,$page,$rp,$sortname,$sortorder);
		}	
//--------------------------------------------------------	
		public function productAction(){
			$this->_helper->layout()->disableLayout();
		}
//--------------------------------------------------------		
		public function productprofileAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$query = $filter->filter($this->getRequest()->getParam("query")); 
			$qtype = $filter->filter($this->getRequest()->getParam("qtype")); 
			$page = $filter->filter($this->getRequest()->getParam("page")); 
			$rp = $filter->filter($this->getRequest()->getParam("rp")); 
			$sortname = $filter->filter($this->getRequest()->getParam("sortname")); 
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder")); 	
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));
			$barcode = $filter->filter($this->getRequest()->getParam("barcode"));
			$name_product = $filter->filter($this->getRequest()->getParam("name_product"));
			$objConf=new Model_Product();
			$table_name="com_product_master";
			echo $objConf->productprofile($product_id,$corporation_id,$barcode,$name_product,$table_name,$query,$qtype,$page,$rp,$sortname,$sortorder);
		
		}
	//--------------------------------------------------------	
		public function productdetailAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$objConf=new Model_Product();
			echo $objConf->productdetail($product_id);
		}
		
	//--------------------------------------------------------	
	
	//posconfic	
		public function posconfigAction(){
			$this->_helper->layout()->disableLayout();
		}		
		
		public function tposconfigAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$query = $filter->filter($this->getRequest()->getParam("query")); 
			$qtype = $filter->filter($this->getRequest()->getParam("qtype")); 
			$page = $filter->filter($this->getRequest()->getParam("page")); 
			$rp = $filter->filter($this->getRequest()->getParam("rp")); 
			$sortname = $filter->filter($this->getRequest()->getParam("sortname")); 
			$sortorder = $filter->filter($this->getRequest()->getParam("sortorder")); 
			
			$table = $filter->filter($this->getRequest()->getParam("fTable")); 
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id")); 
			$branch_no = $filter->filter($this->getRequest()->getParam("branch_no")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			
			if($table !=""){
				$objConf=new Model_Posconfig();
				echo $objConf->tposconfig($table,$branch_id,$branch_no,$company_id,$corporation_id,$query,$qtype,$page,$rp,$sortname,$sortorder);
			}

		}
		
		public function posconfigtreeAction(){
			$this->_helper->layout()->disableLayout();
			$objConf=new Model_Posconfig();
			echo $objConf->posconfigtree();
		}
		public function treeposconfiglistAction(){
			$this->_helper->layout()->disableLayout();
			$objConf=new Model_Posconfig();
			echo $objConf->treeposconfiglist();
		}
		
		public function savecomposconfigAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id")); 
			$branch_no = $filter->filter($this->getRequest()->getParam("branch_no")); 
			$code_type = $filter->filter($this->getRequest()->getParam("code_type")); 
			$value_type= $filter->filter($this->getRequest()->getParam("value_type")); 
			$default_status = $filter->filter($this->getRequest()->getParam("default_status")); 
			$condition_status = $filter->filter($this->getRequest()->getParam("condition_status")); 
			$default_day = $filter->filter($this->getRequest()->getParam("default_day")); 
			$condition_day = $filter->filter($this->getRequest()->getParam("condition_day")); 
			$start_date = $filter->filter($this->getRequest()->getParam("start_date")); 
			$end_date = $filter->filter($this->getRequest()->getParam("end_date")); 
			$start_time = $filter->filter($this->getRequest()->getParam("start_time")); 
			$end_time = $filter->filter($this->getRequest()->getParam("end_time")); 
			$Table= $filter->filter($this->getRequest()->getParam("Table")); 
			$id= $filter->filter($this->getRequest()->getParam("id")); 
			$id_config= $filter->filter($this->getRequest()->getParam("id_config"));
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			$user_id=$myprofile['user_id'];
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('com',$Table);
			if($id !=""){
				$data = array(
					'id_config'=>$id_config,
					'code_type'=>$code_type,
					'value_type'=>$value_type,
					'default_status'=>$default_status,
					'condition_status'=>$condition_status,
					'default_day'=>$default_day,
					'condition_day'=>$condition_day,
					'start_date'=>$start_date,
					'end_date'=>$end_date,
					'start_time'=>$start_time,
					'end_time'=>$end_time,
					'upd_date'=>date('Y-m-d'),
					'upd_time'=>date('H:i:s'),
					'upd_user'=>$user_id
				);
				
				$where=" id = '$id' ";
				$run=$objConf->updatedata($Table,$data,$where);
				
			}else{
				$data = array(
					'id_config'=>$id_config,
					'corporation_id'=>$corporation_id,
					'company_id'=>$company_id,
					'branch_id'=>$branch_id,
					'branch_no'=>$branch_no,
					'code_type'=>$code_type,
					'value_type'=>$value_type,
					'default_status'=>$default_status,
					'condition_status'=>$condition_status,
					'default_day'=>$default_day,
					'condition_day'=>$condition_day,
					'start_date'=>$start_date,
					'end_date'=>$end_date,
					'start_time'=>$start_time,
					'end_time'=>$end_time,
					'reg_date'=>date('Y-m-d'),
					'reg_time'=>date('H:i:s'),
					'reg_user'=>$user_id
				);
				$run=$objConf->insertdata($Table,$data);
			}
			echo $run;
		}
	//--------------------------------------------------------
		public function saveaddcodetypeAction(){
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id")); 
			$company_id = $filter->filter($this->getRequest()->getParam("company_id")); 
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id")); 
			$branch_no = $filter->filter($this->getRequest()->getParam("branch_no")); 
			$code_type = $filter->filter($this->getRequest()->getParam("code_type")); 
			$value_type= $filter->filter($this->getRequest()->getParam("value_type")); 
			$default_status = $filter->filter($this->getRequest()->getParam("default_status")); 
			$condition_status = $filter->filter($this->getRequest()->getParam("condition_status")); 
			$default_day = $filter->filter($this->getRequest()->getParam("default_day")); 
			$condition_day = $filter->filter($this->getRequest()->getParam("condition_day")); 
			$start_date = $filter->filter($this->getRequest()->getParam("start_date")); 
			$end_date = $filter->filter($this->getRequest()->getParam("end_date")); 
			$start_time = $filter->filter($this->getRequest()->getParam("start_time")); 
			$end_time = $filter->filter($this->getRequest()->getParam("end_time")); 
			$Table= $filter->filter($this->getRequest()->getParam("Table")); 
			$id_config= $filter->filter($this->getRequest()->getParam("id_config"));
			
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			
			$user_id=$myprofile['user_id'];
			
			$objConf=new Model_Mydbpos();
			
			$data = array(
				'id_config'=>$id_config,
				'code_type'=>$code_type,
				'value_type'=>$value_type,
				'default_status'=>$default_status,
				'condition_status'=>$condition_status,
				'default_day'=>$default_day,
				'condition_day'=>$condition_day,
				'start_date'=>$start_date,
				'end_date'=>$end_date,
				'start_time'=>$start_time,
				'end_time'=>$end_time,
				'reg_date'=>date('Y-m-d'),
				'reg_time'=>date('H:i:s'),
				'reg_user'=>$user_id
			);
			$run=$objConf->insertdata('com_pos_config_menu',$data);
			
			$sql="SELECT DISTINCT id_config,corporation_id,company_id,branch_id,branch_no 
			FROM com_pos_config  
			WHERE id_config='$id_config'";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $data){ 
				$id_config =$data['id_config'];
				$corporation_id =$data['corporation_id'];
				$company_id=$data['company_id'];
				$branch_id=$data['branch_id'];
				$branch_no=$data['branch_no'];
				$data = array(
					'id_config'=>$id_config,
					'corporation_id'=>$corporation_id,
					'company_id'=>$company_id,
					'branch_id'=>$branch_id,
					'branch_no'=>$branch_no,
					'code_type'=>$code_type,
					'value_type'=>$value_type,
					'default_status'=>$default_status,
					'condition_status'=>$condition_status,
					'default_day'=>$default_day,
					'condition_day'=>$condition_day,
					'start_date'=>$start_date,
					'end_date'=>$end_date,
					'start_time'=>$start_time,
					'end_time'=>$end_time,
					'reg_date'=>date('Y-m-d'),
					'reg_time'=>date('H:i:s'),
					'reg_user'=>$user_id
				);
				$run=$objConf->insertdata('com_pos_config',$data);
			}
			echo $run;
		}	
	//--------------------------------------------------------
		public function branchAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$product_id = $filter->filter($this->getRequest()->getParam("product_id"));
			$objConf=new Model_Branch();
			//echo $objConf->productdetail($product_id);
		}
	//--------------------------------------------------------
		public function sentallbranchAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));
			$company_id = $filter->filter($this->getRequest()->getParam("company_id"));
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id"));
			$branch_no = $filter->filter($this->getRequest()->getParam("branch_no"));
			$id_config = $filter->filter($this->getRequest()->getParam("id_config"));
			
			$objConf=new Model_Posconfig();
			$objConf->sentallbranch($corporation_id,$company_id,$id_config);
		}
	//--------------------------------------------------------
		public function cancelconfigAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));
			$company_id = $filter->filter($this->getRequest()->getParam("company_id"));
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id"));
			$branch_no = $filter->filter($this->getRequest()->getParam("branch_no"));
			$objConf=new Model_Posconfig();
			$objConf->cancelconfig($corporation_id,$company_id,$branch_id,$branch_no);
		}
	//--------------------------------------------------------
		public function sentbranchAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$corporation_id = $filter->filter($this->getRequest()->getParam("corporation_id"));
			$company_id = $filter->filter($this->getRequest()->getParam("company_id"));
			$branch_id = $filter->filter($this->getRequest()->getParam("branch_id"));
			$branch_no = $filter->filter($this->getRequest()->getParam("branch_no"));
			$id_config = $filter->filter($this->getRequest()->getParam("id_config"));
			$objConf=new Model_Posconfig();
			$objConf->sentbranch($corporation_id,$company_id,$branch_id,$branch_no,$id_config);
		}

	//--------------------------------------------------------
		public function getcodetypeAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$table = $filter->filter($this->getRequest()->getParam("fTable")); 
			$id=$filter->filter($this->getRequest()->getParam("id"));
			$objConf=new Model_Posconfig();
			$show=$objConf->getcodetype($id,$table);
			echo $show;
		}
	//--------------------------------------------------------	
		public function delconfigAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$id_config = $filter->filter($this->getRequest()->getParam("id_config")); 
			$objConf=new Model_Mydbpos();
			$sql="DELETE FROM `com_pos_config_menu` WHERE `id_config` = '$id_config' ";
			$objConf->deldata($sql);
			
			$sql2="DELETE FROM `com_pos_config` WHERE `id_config` = '$id_config' ";
			$objConf->deldata($sql2);
			echo "1";
		}
	//--------------------------------------------------------	
		public function delcodetypeAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$fTable = $filter->filter($this->getRequest()->getParam("fTable")); 
			$id = $filter->filter($this->getRequest()->getParam("id")); 
			$code_type = $filter->filter($this->getRequest()->getParam("code_type")); 
			
			$objConf=new Model_Mydbpos();
	
			$sql="DELETE FROM $fTable WHERE `id` = '$id' ";
			$objConf->deldata($sql);
			
			$sql2="DELETE FROM `com_pos_config` WHERE `code_type` = '$code_type' ";
			$objConf->deldata($sql2);
			
			echo "1";
		}
	//--------------------------------------------------------	
			public function addconfigbranchAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$fTable = $filter->filter($this->getRequest()->getParam("fTable")); 
			$id = $filter->filter($this->getRequest()->getParam("id")); 
	
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile;
			if(empty($myprofile)){
				$session = new Zend_Session_Namespace('empprofile');
				$myprofile=$session->empprofile; 
			}
			$user_id=$myprofile['user_id'];
			
			$objConf=new Model_Mydbpos();
			$sql="SELECT MAX(`id_config`)+1 AS id_config FROM `com_pos_config_menu` WHERE 1";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
			foreach($res as $data){ 
				$id_config =$data['id_config'];
				$data = array(
				'id_config'=>$id_config,
				'reg_date'=>date('Y-m-d'),
				'reg_time'=>date('H:i:s'),
				'reg_user'=>$user_id
				);
				$run=$objConf->insertdata('com_pos_config_menu',$data);
			}
			
		}
	//--------------------------------------------------------		
		
}
	
?>