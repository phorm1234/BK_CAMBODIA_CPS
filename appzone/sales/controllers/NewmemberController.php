<?php
	class NewmemberController extends Zend_Controller_Action
	{
		
		function preDispatch()
        {
            $this->_helper->layout()->setLayout('default_layout');
            $session = new Zend_Session_Namespace('empprofile');
            $empprofile=$session->empprofile;
            if(!isset($empprofile)){
                
                $empprofile=array(
                    'country_id'=>"KHM",
                    'corporation_id'=>"SS",
                    'company_id'=>"CPS",
                    'company_name'=>"CUTE PRESS / บริษัท เอสเอสยูพี กรุงเทพ 1991 จำกัด",
                    'branch_name'=>"Aeon Mall 2",
                    'branch_name_e'=>"",
                    'address'=>"อาคาร ว.วิโรจน์  89/1 ซอยรัชภัณฑ์",
                    'road'=>"ถนนราชปรารถ",
                    'district'=>"103704",
                    'amphur'=>"1037",
                    'province'=>"10",
                    'postcode'=>"10400",
                    'tel'=>"0-2642-6060",
                    'fax'=>"0-2642-6304",
                    'tax_id'=>"0105528041952",
                    'active'=>"1",
                    'branch_no'=>"1103",
                    'branch_tp'=>"S",
                    'acc_no'=>"0",
                    'start_date'=>"2018-05-21",
                    'end_date'=>"2020-12-31",
                    'com_ip'=>"10.10.35.57",
                    'lock_status'=>"N",
                    'computer_no'=>"99",
                    'pos_id'=>"",
                    'thermal_printer'=>"N",
                    'network'=>"Y",
                    'name'=>"CUSTOMER",
                    'surname'=>"CUSTOMER",
                    'branch_id'=>"1103",
                    'employee_id'=>"000009",
                    'user_id'=>"000009",
                    'group_id'=>"CpShopEmp",
                    'env_id'=>"shop",
                    'perm_id'=>"ShopEmp",
                    'logintime'=>date("Y-m-d H:i:s")
                );
                
                $session = new Zend_Session_Namespace('empprofile');
                $session->empprofile = $empprofile;
                
                //$this->_redirect('/error/sessionexpire');
            }
            $this->view->session_employee_id=$empprofile['employee_id'];
            $this->CHECK_SESSION=$empprofile['employee_id'];
            $this->shop=$empprofile['branch_id'];
           
        }//func 
        
        
        
		function registerfromAction()
        {
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();
			$province=$myRun->province();

			$doc_no_diary= $filter->filter($this->getRequest()->getParam("doc_no_diary"));
			
			$databill=$myRun->search_diary1($doc_no_diary);
			$arrbill=explode("@",$databill);
			$member_id=$arrbill[0];
			$apply_date=$arrbill[1];
			$expire_date=$arrbill[2];
			$doc_no=$arrbill[3];
			$application_id=$arrbill[4];
			
			$this->view->assign('doc_no_diary',$doc_no_diary);
			$this->view->assign('member_id',$member_id);
			$this->view->assign('apply_date',$apply_date);
			$this->view->assign('expire_date',$expire_date);
			$this->view->assign('doc_no',$doc_no);
			$this->view->assign('application_id',$application_id);
			$this->view->assign('province',$province);
        }//func 
        
		public function chkmobileAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();

			$computer_ip=$_SERVER['REMOTE_ADDR'];
			$myRun->clear_tmp_member("member_register_tmp",$computer_ip);
			$myRun->clear_tmp_member("member_history_tmp",$computer_ip);


          	$doc_no= $filter->filter($this->getRequest()->getParam("doc_no"));
			$member_id= $filter->filter($this->getRequest()->getParam("member_id"));
			$apply_date= $filter->filter($this->getRequest()->getParam("apply_date"));
			$expire_date= $filter->filter($this->getRequest()->getParam("expire_date"));
			$branch_id= $filter->filter($this->getRequest()->getParam("branch_id"));
			
			$mobile_no= $filter->filter($this->getRequest()->getParam("mobile_no"));
			$id_card= $filter->filter($this->getRequest()->getParam("id_card"));
			$mr= $filter->filter($this->getRequest()->getParam("mr"));
			$fname= $filter->filter($this->getRequest()->getParam("fname"));
			$lname= $filter->filter($this->getRequest()->getParam("lname"));
			$mr_en= $filter->filter($this->getRequest()->getParam("mr_en"));
			$fname_en= $filter->filter($this->getRequest()->getParam("fname_en"));
			$lname_en= $filter->filter($this->getRequest()->getParam("lname_en"));
			$nation= $filter->filter($this->getRequest()->getParam("nation"));
			$address1= $filter->filter($this->getRequest()->getParam("address1"));
			$address2= $filter->filter($this->getRequest()->getParam("address2"));
			$address3= $filter->filter($this->getRequest()->getParam("address3"));
			$address4= $filter->filter($this->getRequest()->getParam("address4"));
			$address5= $filter->filter($this->getRequest()->getParam("address5"));
			$sex= $filter->filter($this->getRequest()->getParam("sex"));
			$hbd= $filter->filter($this->getRequest()->getParam("hbd"));
			$card_at= $filter->filter($this->getRequest()->getParam("card_at"));
			$start_date= $filter->filter($this->getRequest()->getParam("start_date"));
			$end_date= $filter->filter($this->getRequest()->getParam("end_date"));
			
			$noid_type= $filter->filter($this->getRequest()->getParam("noid_type"));
			$noid_remark= $filter->filter($this->getRequest()->getParam("noid_remark"));
			
			$chk_copy_address= $filter->filter($this->getRequest()->getParam("chk_copy_address"));
			$send_company= $filter->filter($this->getRequest()->getParam("send_company"));
			$send_address= $filter->filter($this->getRequest()->getParam("send_address"));
			$send_mu= $filter->filter($this->getRequest()->getParam("send_mu"));
			$send_home_name= $filter->filter($this->getRequest()->getParam("send_home_name"));
			$send_soi= $filter->filter($this->getRequest()->getParam("send_soi"));
			$send_road= $filter->filter($this->getRequest()->getParam("send_road"));
			$send_province_id= $filter->filter($this->getRequest()->getParam("send_province_id"));
			$send_amphur_id= $filter->filter($this->getRequest()->getParam("send_amphur_id"));
			$send_tambon_id= $filter->filter($this->getRequest()->getParam("send_tambon_id"));
			$send_tel= $filter->filter($this->getRequest()->getParam("send_tel"));
			$send_mobile= $filter->filter($this->getRequest()->getParam("send_mobile"));
			$send_fax= $filter->filter($this->getRequest()->getParam("send_fax"));
			$send_remark= $filter->filter($this->getRequest()->getParam("send_remark"));
			$send_email= $filter->filter($this->getRequest()->getParam("send_email"));
			$send_facebook= $filter->filter($this->getRequest()->getParam("send_facebook"));
			
			$friend_id_card= $filter->filter($this->getRequest()->getParam("friend_id_card"));
			$friend_mobile_no= $filter->filter($this->getRequest()->getParam("friend_mobile_no"));
						
						
			$user_id=$this->CHECK_SESSION;
			
			/*
			$run_add=$myRun->chkmobile($computer_ip,$doc_no,$member_id,$apply_date,$expire_date,$branch_id,$mobile_no,$id_card,$mr,$fname,$lname,$mr_en,$fname_en,$lname_en,$nation,$address1,$address2,$address3,$address4,$address5,$sex,$hbd,$card_at,$start_date,$end_date,$chk_copy_address,$send_company,$send_address,$send_mu,$send_home_name,$send_soi,$send_road,$send_province_id,$send_amphur_id,$send_tambon_id,$send_tel,$send_mobile,$send_fax,$send_remark,$user_id,$send_email,$send_facebook,$noid_type,$noid_remark,$friend_id_card,$friend_mobile_no); 
			*/
			
		}//func		
			
		
			
		
		
						
		public function registersaveAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();

			$computer_ip=$_SERVER['REMOTE_ADDR'];
			$myRun->clear_tmp_member("member_register_tmp",$computer_ip);
			$myRun->clear_tmp_member("member_history_tmp",$computer_ip);


          	$doc_no= $filter->filter($this->getRequest()->getParam("doc_no"));
			$member_id= $filter->filter($this->getRequest()->getParam("member_id"));
			$apply_date= $filter->filter($this->getRequest()->getParam("apply_date"));
			$expire_date= $filter->filter($this->getRequest()->getParam("expire_date"));
			$branch_id= $filter->filter($this->getRequest()->getParam("branch_id"));
			
			$mobile_no= $filter->filter($this->getRequest()->getParam("mobile_no"));
			$id_card= $filter->filter($this->getRequest()->getParam("id_card"));
			$mr= $filter->filter($this->getRequest()->getParam("mr"));
			$fname= $filter->filter($this->getRequest()->getParam("fname"));
			$lname= $filter->filter($this->getRequest()->getParam("lname"));
			$mr_en= $filter->filter($this->getRequest()->getParam("mr_en"));
			$fname_en= $filter->filter($this->getRequest()->getParam("fname_en"));
			$lname_en= $filter->filter($this->getRequest()->getParam("lname_en"));
			$nation= $filter->filter($this->getRequest()->getParam("nation"));
			$address1= $filter->filter($this->getRequest()->getParam("address1"));
			$address2= $filter->filter($this->getRequest()->getParam("address2"));
			$address3= $filter->filter($this->getRequest()->getParam("address3"));
			$address4= $filter->filter($this->getRequest()->getParam("address4"));
			$address5= $filter->filter($this->getRequest()->getParam("address5"));
			$sex= $filter->filter($this->getRequest()->getParam("sex"));
			$hbd= $filter->filter($this->getRequest()->getParam("hbd"));
			$card_at= $filter->filter($this->getRequest()->getParam("card_at"));
			$start_date= $filter->filter($this->getRequest()->getParam("start_date"));
			$end_date= $filter->filter($this->getRequest()->getParam("end_date"));
			
			$noid_type= $filter->filter($this->getRequest()->getParam("noid_type"));
			$noid_remark= $filter->filter($this->getRequest()->getParam("noid_remark"));
			
			$chk_copy_address= $filter->filter($this->getRequest()->getParam("chk_copy_address"));
			$send_company= $filter->filter($this->getRequest()->getParam("send_company"));
			$send_address= $filter->filter($this->getRequest()->getParam("send_address"));
			$send_mu= $filter->filter($this->getRequest()->getParam("send_mu"));
			$send_home_name= $filter->filter($this->getRequest()->getParam("send_home_name"));
			$send_soi= $filter->filter($this->getRequest()->getParam("send_soi"));
			$send_road= $filter->filter($this->getRequest()->getParam("send_road"));
			$send_province_id= $filter->filter($this->getRequest()->getParam("send_province_id"));
			$send_amphur_id= $filter->filter($this->getRequest()->getParam("send_amphur_id"));
			$send_tambon_id= $filter->filter($this->getRequest()->getParam("send_tambon_id"));
			$send_tel= $filter->filter($this->getRequest()->getParam("send_tel"));
			$send_mobile= $filter->filter($this->getRequest()->getParam("send_mobile"));
			$send_fax= $filter->filter($this->getRequest()->getParam("send_fax"));
			$send_remark= $filter->filter($this->getRequest()->getParam("send_remark"));
			$send_email= $filter->filter($this->getRequest()->getParam("send_email"));
			$send_facebook= $filter->filter($this->getRequest()->getParam("send_facebook"));
			
			$friend_id_card= $filter->filter($this->getRequest()->getParam("friend_id_card"));
			$friend_mobile_no= $filter->filter($this->getRequest()->getParam("friend_mobile_no"));
			$otp_code= $filter->filter($this->getRequest()->getParam("otp_code"));
			$mobile_dup= $filter->filter($this->getRequest()->getParam("mobile_dup"));
						
						
			$user_id=$this->CHECK_SESSION;
			
			$run_add=$myRun->registersave($computer_ip,$doc_no,$member_id,$apply_date,$expire_date,$branch_id,$mobile_no,$id_card,$mr,$fname,$lname,$mr_en,$fname_en,$lname_en,$nation,$address1,$address2,$address3,$address4,$address5,$sex,$hbd,$card_at,$start_date,$end_date,$chk_copy_address,$send_company,$send_address,$send_mu,$send_home_name,$send_soi,$send_road,$send_province_id,$send_amphur_id,$send_tambon_id,$send_tel,$send_mobile,$send_fax,$send_remark,$user_id,$send_email,$send_facebook,$noid_type,$noid_remark,$friend_id_card,$friend_mobile_no,$otp_code,$mobile_dup); 

			
		}//func		
		

		

		
		public function showamphurAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$send_province_id= $filter->filter($this->getRequest()->getParam("send_province_id"));
          	$amphur=$myRun->amphur($send_province_id);
          	$this->view->assign('amphur',$amphur);

			
		}//func	
		
		public function showtambonAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$send_province_id= $filter->filter($this->getRequest()->getParam("send_province_id"));
          	$send_amphur_id= $filter->filter($this->getRequest()->getParam("send_amphur_id"));
          	$tambon=$myRun->tambon($send_province_id,$send_amphur_id);
          	$this->view->assign('tambon',$tambon);

			
		}//func	

		public function idshowprovinceAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$province_name= $filter->filter($this->getRequest()->getParam("province_name"));
          	$amphur_name= $filter->filter($this->getRequest()->getParam("amphur_name"));
          	$tambon_name= $filter->filter($this->getRequest()->getParam("tambon_name"));
          	
          	$iddataprovince=$myRun->idshowprovince($province_name,$amphur_name,$tambon_name);
          	
          	
          	$this->view->assign('province_name',$province_name);
          	$this->view->assign('amphur_name',$amphur_name);
          	$this->view->assign('tambon_name',$tambon_name);
          	$this->view->assign('iddataprovince',$iddataprovince);

			
		}//func

		
		public function idshowamphurAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();

	
          	$province_name= $filter->filter($this->getRequest()->getParam("province_name"));
          	$amphur_name= $filter->filter($this->getRequest()->getParam("amphur_name"));
          	$tambon_name= $filter->filter($this->getRequest()->getParam("tambon_name"));
          	$iddataamphur=$myRun->idshowamphur($province_name,$amphur_name,$tambon_name);
          	
          	$this->view->assign('province_name',$province_name);
          	$this->view->assign('amphur_name',$amphur_name);
          	$this->view->assign('tambon_name',$tambon_name);
          	$this->view->assign('iddataamphur',$iddataamphur);

			
		}//func	
		
		public function idshowtambonAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$province_name= $filter->filter($this->getRequest()->getParam("province_name"));
          	$amphur_name= $filter->filter($this->getRequest()->getParam("amphur_name"));
          	$tambon_name= $filter->filter($this->getRequest()->getParam("tambon_name"));
          	
          	$iddatatambon=$myRun->idshowtambon($province_name,$amphur_name,$tambon_name);
          	
          	$this->view->assign('province_name',$province_name);
          	$this->view->assign('amphur_name',$amphur_name);
          	$this->view->assign('tambon_name',$tambon_name);
          	$this->view->assign('iddatatambon',$iddatatambon);

			
		}//func	
		
		public function changeamphurAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$id_province_id= $filter->filter($this->getRequest()->getParam("id_province_id"));    
          	
          	$dataamphur=$myRun->amphur($id_province_id);

          	$this->view->assign('dataamphur',$dataamphur);

			
		}//func	
		public function changetambonAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$id_province_id= $filter->filter($this->getRequest()->getParam("id_province_id"));
          	$id_amphur_id= $filter->filter($this->getRequest()->getParam("id_amphur_id"));      
          	
          	$datatambon=$myRun->tambon($id_province_id,$id_amphur_id);

          	$this->view->assign('datatambon',$datatambon);

			
		}//func	
		public function findpostcodeAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


          	$id_province_id= $filter->filter($this->getRequest()->getParam("id_province_id"));
          	$id_amphur_id= $filter->filter($this->getRequest()->getParam("id_amphur_id"));   
          	$id_tambon_id= $filter->filter($this->getRequest()->getParam("id_tambon_id"));     
          	
          	$postcode=$myRun->findpostcode($id_province_id,$id_amphur_id,$id_tambon_id);
			echo $postcode;
          	

			
		}//func	
		
		public function listnewmemberAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();


			
		}//func	
		
		public function listnewmembersubAction()
		{	
			$this->_helper->layout()->disableLayout();
			$filter = new Zend_Filter_StripTags();
			$myRun=new Model_Newmember();

			$varday= $filter->filter($this->getRequest()->getParam("varday"));
			$listnewmember=$myRun->listnewmembersub($varday);
			
			$this->view->assign('varday',$varday);
			$this->view->assign('listnewmember',$listnewmember);
			
		}//func	
	}
?>