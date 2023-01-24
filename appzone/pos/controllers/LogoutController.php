<?php
class LogoutController extends Zend_Controller_Action
{
	function init(){       
		//header("Content-type:text/html; charset=tis-620");
		header('Content-type: text/html; charset=utf-8'); 
		$this->initView();
	}     
    public function indexAction()
    {
    	$this->_helper->layout()->disableLayout(); 
		$session = new Zend_Session_Namespace('idlogin');
		/*
		$idlogin=$session->idlogin;
    	$table='login_profile';
    	$data=array('logouttime'=>date("Y-m-d H:i:s"));
    	$where="id='$idlogin'";
    	
    	$objConf=new Model_Mydbpos();
    	$objConf->updatedata($table,$data,$where,'login_profile');
    		*/	
    	$session = new Zend_Session_Namespace('empprofile');
		$session->empprofile=array();
    	echo'<Meta http-equiv="refresh"content="1;URL=/pos/login/login">';
    	
    	/*
    	Zend_Auth::getInstance()->clearIdentity();
    	Zend_Session::destroy(true);
    	//echo '<script language=javascript>window.history.go(-1);</script>';
    	echo'<Meta http-equiv="refresh"content="1;URL=/pos/login/login">';
       */
    	
    	
    	
    	//$this->_redirect('/login/login');
    }
}
