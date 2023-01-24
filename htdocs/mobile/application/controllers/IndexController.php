<?php
class IndexController extends Zend_Controller_Action
{
	private $_bar; 
   // private $arr;
	public function init(){
		
	}
	public function indexAction()
	{
        $this->_redirect("index/receive");
	}
	
	public function addheadAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest ();
		$registry = Zend_Registry::getInstance ();
		$DB = $registry ['DB'];
		$qrcode = $request->getParam("qrcode");
		if(trim($qrcode) != ""){
			$_bar = new Barcode();
			list($state,$doc_no) =$_bar->get_doc_no($qrcode);
			if($state == '1')
						echo json_encode(array("status"=>'y',"doc_no"=>$doc_no));
			else 		echo json_encode(array("status"=>'n',"doc_no"=>"")); 
		}
	}
	
	public function testAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$_bar = new Barcode();
		echo $_bar->get_seq("aaaaaaa");
	}
}
?>


