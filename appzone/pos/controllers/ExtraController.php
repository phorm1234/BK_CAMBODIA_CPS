<?php 
	class ExtraController extends Zend_Controller_Action{
		public function gadgetsAction(){
				$this->_helper->layout()->disableLayout(); 
				$filter = new Zend_Filter_StripTags(); 
				$do = $filter->filter($this->getRequest()->getParam("gedget"));
				$obj=new Model_Gadget();
				if(isset($do)){
					switch($do){
						case '0' :
							$msg=$obj->gadgetstime();
							break;
						case '1' :
							$msg = $obj->pingstatus();	
							break;
						case '2' :
							$msg ='bbbbb';	
							break;
					}
				}
				echo $msg;
				//echo json_encode($obj);	
			}
	}
?>