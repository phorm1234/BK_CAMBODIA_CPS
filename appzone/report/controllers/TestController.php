<?php 
	class TestController extends Zend_Controller_Action{
		public function init(){
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('report_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$templete="templete_".$brand;
			$user_id="";
			$session = new Zend_Session_Namespace('myprofile');
			$myprofile=$session->myprofile; 
			if(!empty($myprofile)){
				foreach($myprofile as $data){
					$company_id=strtolower($data['company_id']);
					$user_id=$data['user_id'];
					$templete="templete_".$company_id;
			     	if(!empty($user_id)){
						$this->view->checklogin=1;
					}
				}xx
			}
			$this->view->arr_layout=array(
						'brand'=>$templete,
						'shop'=>$shop,
						'comno'=>$comno,
						'user_id'=>$user_id
			);
			$this->view->templete=$templete;
		}
		
		public function indexAction(){
            $this->_helper->layout()->setLayout("report_layout");
		}
                
		
	}
?>