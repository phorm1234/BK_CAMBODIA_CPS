<?php 
	class IndexController extends Zend_Controller_Action{
		public function init(){			
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();
			$this->_helper->layout()->setLayout('checkstock_layout');
			$path_config_ini = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'testing');
			$brand=$path_config_ini->common->params->brand;
			$shop=$path_config_ini->common->params->shop;
			$comno=$path_config_ini->common->params->comno;
			$templete="templete_".$brand;
			$user_id="";
			$session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile; 
			$this->empprofile=$empprofile;
			if(!empty($myprofile)){
				foreach($myprofile as $data){
					$company_id=strtolower($data['company_id']);
					$user_id=$data['user_id'];
					$templete="templete_".$company_id;
			     	if(!empty($user_id)){
						$this->view->checklogin=1;
					}
				}
			}
			$this->view->arr_layout=array(
						'brand'=>$templete,
						'shop'=>$shop,
						'comno'=>$comno,
						'user_id'=>$user_id
			);
			$this->view->templete=$templete;
			
		}//func
		
		public function indexAction(){
			$user = new Zend_Session_Namespace('USER');
			$user_id=$user->user_id;
			if(!empty($user_id)){
				$this->_helper->layout()->setLayout('/main_layout');
				//$this->view->greet="Hello my Order project!";
			}else{
				$this->_redirect('../pos/');
			}
		}//func
	}
?>