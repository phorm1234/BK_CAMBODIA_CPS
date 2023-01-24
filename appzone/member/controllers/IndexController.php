<?php 
	class IndexController extends Zend_Controller_Action{
		
		public $_EMPLOYEE_ID;
		public function init(){
			/*$this->initView();*/
			$this->view->baseUrl = $this->_request->getBaseUrl();
		}//func
		
		function preDispatch()
		{				
		
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$this->_EMPLOYEE_ID=$empprofile['employee_id'];
		}
		public function indexAction(){
			$this->_helper->layout()->setLayout('/index_layout');
			$this->view->greet="Hello my Member project!";
		}//func
		public function registerAction(){
			$this->_helper->layout()->setLayout('/default_layout');			
		
		}
		public function registerlistAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$ob = new Model_Data();
			$this->view->rslist = json_encode(
										$ob->registerList(
												$filter->filter($this->getRequest()->getPost('page')),
												$filter->filter($this->getRequest()->getPost('qtype')),
												$filter->filter($this->getRequest()->getPost('query')),
												$filter->filter($this->getRequest()->getPost('rp')),
												$filter->filter($this->getRequest()->getPost('sortname')),
												$filter->filter($this->getRequest()->getPost('sortorder'))
										)
									);
		}
		public function registerdetailAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$ob = new Model_Data();
			$this->view->data = json_encode(
										$ob->registerDetail(
												$filter->filter($this->getRequest()->getPost('id'))
										)
									);
			
		}
		public function getprovinceAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$obj = new Model_Data();
			$this->view->province = json_encode($obj->getProvince());
			
		}
		public function getamphurAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$zip = $filter->filter($this->getRequest()->getPost('zip'));
			$obj = new Model_Data();
			$this->view->amphur = json_encode($obj->getAmphur($zip));
		}
		public function gettumbolAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$zip = $filter->filter($this->getRequest()->getPost('zip'));
			$obj = new Model_Data();
			$this->view->tumbol = json_encode($obj->getTumbol($zip));
		}
		public function setmemberprofileAction(){
			$this->_helper->layout()->disableLayout();
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			
			if($this->getRequest()->isPost()){
				$member_id 	= $filter->filter($this->getRequest()->getPost('member_id'));
				$id_card = $filter->filter($this->getRequest()->getPost('txt-id-card'));
				$title = 'คุณ';
				$EMP =$this->_EMPLOYEE_ID;
				$name = $filter->filter($this->getRequest()->getPost('txt-name'));
				$surename = $filter->filter($this->getRequest()->getPost('txt-last-name'));
				$sex = $filter->filter($this->getRequest()->getPost('txt-sex'));
				$birthday = $filter->filter($this->getRequest()->getPost('txt-year'))."-".$filter->filter($this->getRequest()->getPost('txt-month'))."-".$filter->filter($this->getRequest()->getPost('txt-day'));
				$nationality_id = $filter->filter($this->getRequest()->getPost('txt-nationality'));
				$h_address = $filter->filter($this->getRequest()->getPost('txt-address'));
				$h_village = $filter->filter($this->getRequest()->getPost('txt-ban'));
				$h_soi = $filter->filter($this->getRequest()->getPost('txt-soi'));
				$h_road= $filter->filter($this->getRequest()->getPost('txt-road'));
				$h_district = $filter->filter($this->getRequest()->getPost('txt-tumbol'));
				$h_amphur = $filter->filter($this->getRequest()->getPost('txt-amphur'));
				$h_province = $filter->filter($this->getRequest()->getPost('txt-province'));
				$h_postcode = $filter->filter($this->getRequest()->getPost('txt-zip'));
				$h_tel_no = $filter->filter($this->getRequest()->getPost('txt-home-tel'));
				$o_tel_no = $filter->filter($this->getRequest()->getPost('txt-office-tel'));
				$mobile_no = $filter->filter($this->getRequest()->getPost('txt-mobile'));
				$email = $filter->filter($this->getRequest()->getPost('txt-email'));
				
				$obj = new Model_Data();
				
				$this->view->result =$obj->setRegisterDetails($member_id,$id_card,$title,$EMP,$name,
								$surename,$sex,$birthday,$nationality_id,$h_address,
								$h_village,$h_soi,$h_road,$h_district,$h_amphur,
								$h_province,$h_postcode,$h_tel_no,$o_tel_no,$mobile_no,$email);
			}
		}
		
	}
?>