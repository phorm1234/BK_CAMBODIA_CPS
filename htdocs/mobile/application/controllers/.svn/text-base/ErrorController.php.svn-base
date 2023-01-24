<?php
class ErrorController extends Zend_Controller_Action {
	
	public function errorAction() {
		//logic code
		//return $this->_redirect('/');
		$errors = $this->_getParam ( 'error_handler' );
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
				// error 404
				$this->getResponse ()->setHttpResponselCode ( 404 );
				$this->view->message = "Page not found";
				break;
			default :
				//application error
				$this->getResponse ()->setHttpResponseCode ( 500 );
				$this->view->message = 'Application error';
				break;
		} //end switch
		$this->view->exception = $errors->exception;
		$this->view->request = $errors->request;
	}
}

?>