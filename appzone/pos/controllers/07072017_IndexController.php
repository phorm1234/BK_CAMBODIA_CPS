<?php 
	class IndexController extends Zend_Controller_Action{
		//---------------------------------------------------
		public $company_id="";
		public $name="";
		public $user_id="";
		public $surname="";
		public $group_id="";
		public $perm_id="";
		
		public function init(){
			header("Content-type:text/html; charset=utf-8");
			$this->initView();
		}
		//--------------------------------------------------------
		function preDispatch()
		{
	   		$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$this->company_id = $empprofile['company_id'];
			$this->name = $empprofile['name'];
			$this->surname = $empprofile['surname'];
			$this->user_id = $empprofile['user_id'];
			$this->group_id = $empprofile['group_id'];
			$this->perm_id = $empprofile['perm_id'];
			
            if($this->user_id==''){
                $this->_redirect('/logout/index');
            }
            $res=SSUP_Controller_Plugin_Db::check_log_out($this->user_id,$this->group_id,$this->perm_id);
            if($res== "out"){
                $this->_redirect('/logout/index');
            } 
		 }

		//--------------------------------------------------------
		public function testAction(){
			$this->_helper->layout()->disableLayout();
			$objConf=new Model_IndexModel();
			$a=$objConf->com_branch_os();
			echo $a;
		}
		
		//--------------------------------------------------------
		public function onlinestatusAction(){
			$this->_helper->layout()->disableLayout();
			$objConf=new Model_IndexModel();
			$onlinestatus=$objConf->onlinestatus();
			echo"$onlinestatus";
		}
		//--------------------------------------------------------
		public function indexAction(){
			$this->view->name=$this->name;
			$this->view->surname=$this->surname;
			//$this->view->version_id=version_id();
			
			$objConf=new Model_User();
			$objConf->conf_employee_update();
			
			$this->_helper->layout()->setLayout('/shopdesktop');
		}
	//--------------------------------------------------------
		public function getmaxidnewAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_IndexModel();
			$getmaxidnew=$objConf->getmaxidnew($this->company_id);
			$objConf->getShotNews($this->company_id);
			echo $getmaxidnew;
		}
		//--------------------------------------------------------
		public function getshortnewAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_IndexModel();
			$getshortnew=$objConf->getshortnew($this->company_id );
			echo $getshortnew;
		}	
			//--------------------------------------------------------
		public function shownewAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$content = $filter->filter($this->getRequest()->getParam("content"));
			echo urldecode($content);
		}
		//--------------------------------------------------------
		public function sentdataAction(){
			$this->_helper->layout()->disableLayout(); 
		}
		//--------------------------------------------------------
		public function sentdatatoofficeAction(){
			$this->_helper->layout()->disableLayout();
			//echo"<iframe src='/transfer_to_office/index.php' frameborder='0' width='200' height='200'></iframe> "; 
			echo"<iframe src='/transop.ssup.co.th/index.php' frameborder='0' width='200' height='200'></iframe> "; 
			
			/*
			$objConf=new Model_IndexModel();
			$content=$objConf->sentdatatooffice();
			//$content_p_tod=$objConf->p_tod_run();
			* */
			
		}
		//--------------------------------------------------------
		public function datatobranchAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_IndexModel();
			$content=$objConf->datatobranch();
			//echo $content;	
		}
		//--------------------------------------------------------
		public function desktopAction(){
			$this->_helper->layout()->disableLayout(); 
			$objConf=new Model_IndexModel();
			$content=$objConf->icondesktop();
			echo $content;
			
		}
		//--------------------------------------------------------
		public function freedomAction(){
			$this->_helper->layout()->disableLayout(); 
		}
		//--------------------------------------------------------
		public function gadgetsAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$do = $filter->filter($this->getRequest()->getParam("gedget"));
			$obj=new Model_IndexModel();
			if(isset($do)){
				switch($do){
					case '0' :
						$msg=$obj->gadgetstime();
						break;
					case '1' :
						$msg = '';	
						break;
					case '2' :
						$msg = '';	
						break;
				}
			}
			echo $msg;
			//echo json_encode($obj);	
		}
		//--------------------------------------------------------
		public function numchildnodeAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$y= $filter->filter($this->getRequest()->getParam("y")); 
			$menu_id = str_replace("#window_","","$y");
			$objConf=new Model_IndexModel();
			$countchild=$objConf->checkHavechildNode($menu_id);
			echo"$countchild";
		}
		//--------------------------------------------------------
		public function childiconAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id")); 
			$objConf=new Model_IndexModel();
			
			$this->view->namemenu=$objConf->namemenu($menu_id);
			$this->view->menu_id=$menu_id;
			$this->view->menu_id=$menu_id;
			$menu_exec=$this->view->menu_exec=$objConf->menu_exec($menu_id);
			
		}
		//--------------------------------------------------------
		public function treemenurefAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$menu_id = $filter->filter($this->getRequest()->getParam("menu_id")); 
			$objConf=new Model_IndexModel();
			echo $objConf->treemenuref($menu_id);
		}
		//--------------------------------------------------------
		public function geturlAction(){
			$this->_helper->layout()->disableLayout(); 
			$filter = new Zend_Filter_StripTags(); 
			$menu_exec = $filter->filter($this->getRequest()->getParam("menu_exec")); 
			$this->view->menu_exec=$menu_exec;
		}
		//--------------------------------------------------------
		
	}
?>