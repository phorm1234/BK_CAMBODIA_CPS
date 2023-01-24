<?php
	class ErrorController extends Zend_Controller_Action
	{
		public function uncountcashAction(){
			//*WR26112015
			$this->_helper->layout()->setLayout('default_layout');
		}//
		public function billopenAction(){
			$this->_helper->layout()->setLayout('default_layout');
			$objError=new Model_PosGlobal();
			$doc_date=$objError->getDocDate();
			$this->view->err_doc_date=$doc_date;
		}//func
		
		public function setupsessionexpireAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		
		public function sessionexpireAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		
		public function confirminvAction(){
			//$this->_helper->layout()->disableLayout();
			$this->_helper->layout()->setLayout('default_layout');
		}//func
		
		public function confirmrqAction(){
			//$this->_helper->layout()->disableLayout();
			$this->_helper->layout()->setLayout('default_layout');
		}//
		
		public function timeopenbillAction(){
			$this->_helper->layout()->setLayout('default_layout');
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();	
			$str_show="";
			if ($this->_request->isGet()) {	
				$status = $filter->filter($this->getRequest()->getParam('status'));
				$compare = $filter->filter($this->getRequest()->getParam('compare'));
				$time = $filter->filter($this->getRequest()->getParam('time'));
				$arr_time=explode(':',$time);
				$str_time=$arr_time[0].".".$arr_time[1];
				if($compare=='L'){
					$str_show="ไม่สามารถเปิดบิลได้ก่อนเวลาเปิดจุดขาย : ".$str_time." น.";
				}else if($compare=='G'){
					$str_show="ไม่สามารถเปิดบิลได้หลังเวลาปิดจุดขาย : ".$str_time." น.";
				}
			}
			$this->view->str_show=$str_show;
		}//
		
		public function outipAction(){
			$this->_helper->layout()->setLayout('default_layout');
		}//
		
		public function errorAction()
		{
				//$this->_helper->layout()->disableLayout();
				$this->_helper->layout()->setLayout('index_layout');
				$errors = $this->_getParam('error_handler');
				switch ($errors->type) {
						case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
						case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
										// 404 error -- controller or action not found
										$this->getResponse()->setHttpResponseCode(404);
										$this->view->message = 'Page not found';
										break;
						default:
										// application error
										$this->getResponse()->setHttpResponseCode(500);
										$this->view->message = 'Application error';
										break;
				}
				$this->view->exception = $errors->exception;
				$this->view->request= $errors->request;
		}
	}//class

?>
