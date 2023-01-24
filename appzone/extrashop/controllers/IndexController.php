	<?php 
	class IndexController extends Zend_Controller_Action
	{
		
		public function init()
		{	
			$this->initView();
			$this->view->baseUrl = $this->_request->getBaseUrl();	
		}//func		
		
		function preDispatch()
		{				
			$this->_helper->layout()->setLayout('default_layout');			
			//check session
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			if(!isset($empprofile)){
				$this->_redirect('/error/sessionexpire');
				exit();				
			}
		
			/*
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
			exit();
			//172.63.69.24
			*/
		}//func
		
		function indexAction(){
			$this->_helper->layout->disableLayout();
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/frame_shop/shop_ranking.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
			
		}//func
		
		
		function statisticAction(){
			//ยอดขาย
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			//$url2="http://192.168.252.240/ims/frame_shop/shop_ranking.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]&p=shop_ranking";
			

			
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function shopnewsAction(){
			//ข่าวสาร | OP ร้องเรียน princess awards
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/frame_shop/shop_news.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function shopactivityAction(){
			//กิจกรรมสาขา
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/frame_shop/op_activity.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function selfserviceAction(){
			//Self Service
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/self_service/index.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function questionareAction(){
			//แบบประเมิณ Questionare
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.246/questionaire_training/Manage/preview/call_t.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function shopinformAction(){
			//Self Service
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/shop_inform/index.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function phonebookAction(){
			//phonebook
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/frame_shop/shop_phone.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function fixedAction(){
			//phonebook
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/frame_shop/fixed/checklog.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function awardsAction(){
			//Princess awards
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://192.168.252.240/ims/frame_shop/award.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function vipmemtelAction(){
			//vip memtel รายชื่อสมาชิก
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?p=vip_memtel&shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		
		function shopiqAction(){
			//shop iq
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?p=shop_iq&shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function stockcountAction(){
			//Shop Stock count
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?p=stock_count&shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function voeAction(){
			//Voice of employee
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?p=voice_of_emp&shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function regismemberAction(){
			//Voice of employee
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile;
			$url2="http://10.100.53.2/ims/memregis/index.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
		
		function voiceyourchoiceAction(){
            //Voice of employee
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            $session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile;
                 //http://10.100.53.2/ims/vote_shop/vote.php?shop=$_GET[shop]&status=SHOP&userID=wcrm_004845
            $url2="http://10.100.53.2/ims/vote_shop/vote.php?shop=$empprofile[branch_id]&status=SHOP&userID=wcrm_004845";
            $this->_redirector = $this->_helper->getHelper('Redirector');
            $this->_redirector->setCode(303)
                ->setExit(false)
                ->gotoUrl($url2);
        }//func 
        
		function member4gotcardAction(){
            //Voice of employee
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            $session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile;
            $url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]&p=mem_forgot";
            $this->_redirector = $this->_helper->getHelper('Redirector');
            $this->_redirector->setCode(303)
                ->setExit(false)
                ->gotoUrl($url2);
        }//func 
        
        function shoprankingAction(){            
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            $session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile;
            $url2="http://10.100.53.2/ims/frame_shop/admin_brand/index.php?shop=$empprofile[branch_id]&shop_number=$empprofile[branch_id]&brand=$empprofile[corporation_id]&ip=$empprofile[com_ip]&p=shop_ranking";
            $this->_redirector = $this->_helper->getHelper('Redirector');
            $this->_redirector->setCode(303)
                ->setExit(false)
                ->gotoUrl($url2);
        }//func 

		function pacesetterAction(){
			//Voice of employee
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$url2="http://pacesetter.ssup.co.th/index.php?shop=$empprofile[branch_id]";
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->setCode(303)
				->setExit(false)
				->gotoUrl($url2);
		}//func
       		
	}
       		
?>
