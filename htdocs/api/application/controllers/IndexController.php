<?php
class IndexController extends Zend_Controller_Action
{
	private $_bar; 
   // private $arr;
	public function init(){
		
	}
	
	
	public function ajaxorderAction(){
		
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		
		$tmp = new Advertis();
		$order = $tmp->showOrder();
		list($profile,$point) 	= $tmp->read_profile();
		list($pro_birthday,$pro_welcome,$pro_renewal,$str_exp,$chk_exp) 		= $tmp->promotion();
		echo 'data: {"order": "'.$order.'","profile": "'.$profile.'","promo_birthday": "'.$pro_birthday.'","promo_welcome": "'.$pro_welcome.'","promo_renewal": "'.$pro_renewal.'","exp": "'.$str_exp.'","chk_exp": "'.$chk_exp.'","point": "'.$point.'"';
		
//		echo "data: 'order': '$order'";
//		echo "data: 'profile': '$profile'";
//		echo "data: 'promo_birthday': '$pro_birthday'";
//		echo "data: 'promo_welcome': '$pro_welcome'";
//		echo "data: 'promo_renewal': '$pro_renewal'";
		echo "}\n\n";
		//echo json_encode(array("order"=>$order,"profile"=>$profile,"promo_birthday"=>$pro_birthday,"promo_welcome"=>$pro_welcome,"promo_renewal"=>$pro_renewal));
		flush();
	}
	
	public function ajaxstatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$tmp = new Advertis();
		$status = $tmp->getStatus();
		
		echo json_encode($status);
	}
	
	public function ajaxshopidAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$tmp = new Advertis();
		$res = $tmp->shop_id();
		echo $res;
	}
	
	public function testAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$tmp = new Advertis();
		echo $order = $tmp->showOrder();
		//list($pro_birthday,$pro_welcome,$pro_renewal) 		= $tmp->promotion();
		//echo json_encode(array("promo_birthday"=>$pro_birthday,"promo_welcome"=>$pro_welcome,"promo_renewal"=>$pro_renewal));
	}
	
	
	public function test2Action(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		$time  = date('r');
		echo "data: The server time is: {$time}\n\n";
		
		flush();
	}

}
?>


