<?php	
	class Model_PosGlobal{		
		public $db=null;
		public $dbMaster=null;
		public $dbLocal=null;
		public $doc_tp;/*ประเภทเอกสาร จะเปลี่ยนไปตาม class ลูกที่มีการสืบทอดตามการเปิดบิลนั้นๆ*/
		public $refer_doc_no;//เลขที่เอกสารอ้างอิง
		public $m_lock_status="N";//สถานะระบุการ lock การ key manual
		public $m_country_id;
		public $m_corporation_id;//รหัสบริษัท
		public $m_company_id;//รหัสชองทางจัดจำหน่าย
		public $m_branch_id;//รหัสจุดขาย
		public $m_branch_no;//รหัสสาขาที่จดทะเบียน
		public $m_branch_tp;//ลักษณะสาขาเป็น shop หรือ corner
		public $m_doc_no;//เลขที่เอกสาร 
		public $m_computer_no;// คอมพิวเตอร์เครื่องที่
		public $m_pos_id;// หมายเลข pos id
		public $m_com_ip;//client ip
		public $m_com_master_ip;//server ip		
		public $m_thermal_printer;
		public $m_cashdrawer;
		public $employee_id;//รหัสพนักงาน
		public $user_id;//ชื่อผู้ใช้ระบบ
		public $cashier_id;//รหัสผู้เปิดบิล
		public $group_id;//group of employee
		public $saleman_id;//รหัสผู้ขาย
		public $stock_st;// สถานะตัวคุณสินค้า
		public $onhand;// สินค้าคงเหลือ
		public $doc_date;//วันที่เปิดบิล
		public $doc_time;//เวลาเปิดบิล
		public $arr_date;// data array
		public $year;//ปีของระบบ
		public $month;//เดือนของระบบ
		public $msg_error;//ข้อความแจ้งเตือนความผิดพลาด	
		public $msg_show;//ข้อความแจ้งข้อมูลบอกเล่า
		private $m_ops_day;
			
		public function __construct(){
			$session = new Zend_Session_Namespace('empprofile');
			$empprofile=$session->empprofile; 
			$this->m_lock_status=$empprofile['lock_status'];
			$this->m_country_id=$empprofile['country_id'];
			$this->m_corporation_id=$empprofile['corporation_id'];
			$this->m_company_id=$empprofile['company_id'];
			$this->m_branch_id=$empprofile['branch_id'];
			$this->m_branch_no=$empprofile['branch_no'];
			$this->m_branch_tp=$empprofile['branch_tp'];		
			$this->m_computer_no=$empprofile['computer_no'];			
			//$this->m_com_ip=$empprofile['com_ip'];
			$this->m_com_ip=$_SERVER['REMOTE_ADDR'];//client ip			
			$this->m_pos_id=$empprofile['pos_id'];
			$this->m_thermal_printer=$empprofile['thermal_printer'];
			$this->employee_id=$empprofile['employee_id'];
			$this->group_id=$empprofile['group_id'];
			$this->user_id=$empprofile['user_id'];
			
			$this->db=Zend_Registry::get('dbOfline'); 
			$this->msg_error="";
			$this->msg_show="";
			$this->doc_date=$this->checkDocDate();		
			
			if($this->doc_date!=''){			
				$arr_doc_date=explode('-',$this->doc_date);
				$this->year=$arr_doc_date[0];
				$this->month=$arr_doc_date[1];
			}else{			
				$arr_doc_date=explode('-',date('Y-d-m'));
				$this->year=$arr_doc_date[0];
				$this->month=$arr_doc_date[1];
			}
			
			$arr_client=$this->getClientProfile();
			$arr_server=$this->getServerProfile();
			if(count($arr_client)>0){
				$this->m_com_ip=$arr_client[0]['com_ip'];
				$this->m_cashdrawer=$arr_client[0]['cashdrawer'];
			}else{
				$this->m_com_ip=$arr_server[0]['com_ip'];
				$this->m_cashdrawer=$arr_server[0]['cashdrawer'];
			}
			if(count($arr_server)>0){
				$this->m_com_master_ip=$arr_server[0]['com_ip'];
			}
		}//func	
		
		/**
		 * @create 20062017
		 */
		function cutNum($num, $precision = 2){
			return floor($num).substr($num-floor($num),1,$precision+1);
		}//func
		
	
		
		/**
		 * @desc
		 * @create 31052017
		 * @return current rate per dollar
		 */
		function getRateDefault(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_pos_config");
			$sql_rate="SELECT * FROM `com_currency` WHERE country_id='$this->m_country_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_rate=$this->db->fetchAll($sql_rate);
			$unit2=0;
			if(!empty($row_rate)){
				$unit2=$row_rate[0]['unit2'];				
			}
			return $unit2;
		}//func
		
		/***
		 * @desc
		 * @create 30052017
		 * @return Double Rate
		 */
		function reteChange($net_amt){
			/**
			 * @desc change rate to R
			 * @var $net_amt is net of dollars
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_pos_config");
			if($net_amt<1) return 0;
			$sql_rate="SELECT * FROM `com_currency` WHERE country_id='$this->m_country_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_rate=$this->db->fetchAll($sql_rate);
			if(!empty($row_rate)){
				$unit1=$row_rate[0]['unit1'];//1 $
				$unit2=$row_rate[0]['unit2'];//4000 R
				$net_amt=($net_amt*$unit2)/$unit1;
				//ปัดเศษหลักร้อยขึ้น
				return $net_amt;
			}
		}//func
		
		/***
		 * @desc check lock finger scan
		 * @create 15052017
		 * @return string
		 */
		function checkLogFingerScan(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_pos_config");
			$status_lock='N';
			$sql_chk1="SELECT default_status,condition_status
			FROM com_pos_config
			WHERE
			corporation_id='$this->m_corporation_id' AND
			company_id='$this->m_company_id' AND
			branch_id='$this->m_branch_id' AND
			branch_no='$this->m_branch_no' AND
			code_type='LOCK_FINGER_SCAN' AND
			'$this->doc_date' BETWEEN start_date AND end_date AND
			CURTIME() BETWEEN start_time AND end_time";
			$row_chk1=$this->db->fetchAll($sql_chk1);
			if(count($row_chk1)>0){
				if($row_chk1[0]['condition_status']=='Y'){
					$status_lock='Y';//lock
				}
			}else{
				$sql_chk2="SELECT default_status
				FROM com_pos_config
				WHERE
				corporation_id='$this->m_corporation_id' AND
				company_id='$this->m_company_id' AND
				branch_id='$this->m_branch_id' AND
				branch_no='$this->m_branch_no' AND
				code_type='LOCK_FINGER_SCAN'";
				$row_chk2=$this->db->fetchAll($sql_chk2);
				if(count($row_chk2)>0){
					if($row_chk2[0]['default_status']=='Y'){
						$status_lock='Y';//lock
					}
				}
			}
			return $status_lock;
		}//func
		/***
		 * @desc 
		 * @create 14032017
		 * @param unknown $paytype
		 * @param unknown $storeid
		 * @param unknown $deviceid
		 * @param unknown $reqid
		 * @param unknown $reqdt
		 * @param unknown $custcode
		 * @param unknown $amt
		 * @param unknown $respcode
		 * @param unknown $errmsg
		 * @param unknown $transid
		 * @param unknown $alipaytransid
		 * @param unknown $transdt
		 * @param unknown $buyerid
		 * @param string $cnyamt
		 * @param string $convrate
		 * @param string $refundreqid
		 * @return string
		 */
		function save_alipay_request($paytype,$storeid, $deviceid, $reqid,$reqdt,$custcode,$amt,$respcode, $errmsg, $transid, $alipaytransid, $transdt, $buyerid,$cnyamt="",$convrate="",$refundreqid="") {
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");			
			$ins="
			insert into
			trn_alipay_request
			set
			paytype='$paytype',
			storeid='$storeid',
			deviceid='$deviceid',
			reqid='$reqid',
			reqdt='$reqdt',
			custcode='$custcode',
			amount='$amt',
			respcode='$respcode',
			errmsg='$errmsg',
			transid='$transid',
			alipaytransid='$alipaytransid',
			transdt='$transdt',
			buyerid='$buyerid',
			cnyamt='$cnyamt',
			convrate='$convrate',
			refundreqid='$refundreqid'
			";
			$ins_resp=$this->db->query($ins);
			if($ins_resp){
				return "ins_y";
			}else{
				return "ins_n";
			}
		}//func
		
		function getNetAmtUnGetPoint($tbl_tmp_detail){
			/*
			 * desc get sum net_amt from tmp table by get_point<>'Y'
			 * create 21112016
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$net_amt=0;
			$sql_net="SELECT SUM(net_amt) AS net_amt
			FROM $tbl_tmp_detail
			WHERE
				`corporation_id`='$this->m_corporation_id' AND
				`company_id`='$this->m_company_id' AND
				`branch_id`='$this->m_branch_id' AND
				`computer_ip`='$this->m_com_ip' AND		
				`doc_date`='$this->doc_date' AND
			    `get_point`='N'";
			$arr_net=$this->db->fetchAll($sql_net);
			if(!empty($arr_net)){
				$net_amt=$arr_net[0]['net_amt'];				
			}
			return $net_amt;
		}//func
		
		function getCreditCardInfo(){
			/**
			 * @desc get credit card info from last log transaction
			 * modify : 09072013
			 * @return Array() of credit card info
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_cinfo="SELECT `member_id`, `credit_no`, `credit_tp`, `approve`, `reg_date`, `reg_time`, `reg_user`
			FROM `trn_credit_card`
			WHERE
			`reg_date`='$this->doc_date' AND
			`computer_ip`='$this->m_com_ip'  ORDER BY id DESC LIMIT 0,1";
			$arr_cinfo=$this->db->fetchAll($sql_cinfo);
			return $arr_cinfo;
		}//func
		
		function addCardInfoToValTemp($member_no,$card_info=''){
			/**
			 * @desc init trn_tdiary2_sl_val
			 * @create 10/10/2016
			 * @return card info card_level#ops
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_delval="DELETE FROM `trn_tdiary2_sl_val` WHERE `computer_ip`='$this->m_com_ip'";
			$this->db->query($sql_delval);
			if($card_info!=''){
				$sql_addval="INSERT INTO
				`trn_tdiary2_sl_val`
				SET
				`corporation_id`='$this->m_corporation_id',
				`company_id`='$this->m_company_id',
				`branch_id`='$this->m_branch_id',
				`computer_no`='$this->m_computer_no',
				`computer_ip`='$this->m_com_ip',
				`member_no`='$member_no',
				`doc_date`='$this->doc_date',
				`doc_time`=CURTIME(),
				`cn_remark`='$card_info'";
				$this->db->query($sql_addval);
			}//end if
		}//func
		
		function getCardMemberInfo($member_no){
			/**
			 * @desc get info from trn_tdiary2_sl_val
			 * @create 04/10/2016
			 * @return card level of op member
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$card_level='';
			$sql_getinfo="SELECT member_no, cn_remark FROM `trn_tdiary2_sl_val` WHERE member_no='$member_no'";
			$row_getinfo=$this->db->fetchAll($sql_getinfo);
			if(!empty($row_getinfo)){
				$str_remark=$row_getinfo[0]['cn_remark'];
				$arr_remark=explode('#',$str_remark);
				$card_level=$arr_remark[0];
			}else{
				$sql_local="SELECT doc_no, doc_date, deleted
				FROM trn_diary1
				WHERE member_id = '$member_no'
				AND deleted <> ''
				ORDER BY doc_date DESC , doc_no DESC ";
				unset($row_getinfo);
				$row_getinfo=$this->db->fetchAll($sql_local);
				if(!empty($row_getinfo)){
					$card_level=$row_getinfo[0]['deleted'];
				}
			}
			return $card_level;
		}//func
		
		function chkTypeCardNewMember($application_id){
			/**
			 * @desc
			 * @create 25/02/2016
			 * @return NEW,ALL
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_app="SELECT application_type,card_type
			FROM `com_application_head`
			WHERE `application_id` = '$application_id' AND '$this->doc_date' BETWEEN start_date AND end_date";
			$row_app=$this->db->fetchAll($sql_app);
			if(!empty($row_app)){
				$row_app=$row_app[0];
			}else{
				$row_app='';
			}
			return $row_app;
		}//func
		
		function getCountCash(){
			/***
			 * @desc
			 * @create 26112015
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_ccash="SELECT COUNT( * )
								FROM `com_cash_control`
								WHERE `branch_id` = '$this->m_branch_id' AND `doc_date` = '$this->doc_date' AND STATUS = 'O'";
			$res_ccash=$this->db->fetchOne($sql_ccash);
			return $res_ccash;
		}//func
		
		function getNumScreen(){
			/**
			 * @desc for support 2 display
			 * @create 30062015
			 */
			
			$sql_chkmode="SELECT screen FROM `com_branch_computer` 
										WHERE 
											`branch_id` = '$this->m_branch_id'";
			$row_chkmode=$this->db->fetchAll($sql_chkmode);
			$num_screen=1;
			if(!empty($row_chkmode)){
				$num_screen=$row_chkmode[0]['screen'];
			}
			return $num_screen;
		}//func
		
		public function clsMemberTransaction(){
			/**
			 * 
			 * create 02122014 for support to screen
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_del_trns1="DELETE FROM trn_member_today 
												WHERE 
														`corporation_id`='$this->m_corporation_id' AND 
														`company_id`='$this->m_company_id' AND
														`branch_id`='$this->m_branch_id' AND
														`computer_no`='$this->m_computer_no' AND
 														`computer_ip`='$this->m_com_ip'";
			$res_del_trns1=$this->db->query($sql_del_trns1);
			$sql_del_trns2="DELETE FROM trn_member_privilege 
												WHERE 
														`corporation_id`='$this->m_corporation_id' AND 
														`company_id`='$this->m_company_id' AND
														`branch_id`='$this->m_branch_id' AND
														`computer_no`='$this->m_computer_no' AND
 														`computer_ip`='$this->m_com_ip'";
			$res_del_trns1=$this->db->query($sql_del_trns2);
		}//func
		
		function getOpsDayOfMonth($curr_month,$d_no_of_week=''){
			/**
			 * @desc get all thursday of current month and next month
			 * @des default thursday
			 * @create 10072015
			 * @return array thursday date
			 */
			//for current month
			 $m=date('M');
			 $y=date('Y');		  
			 $date = strtotime("01 $m $y");	
			 if($curr_month=='N'){
			  	 //for next month
			  	 $date = strtotime("01 $m $y");
				  $next = strtotime("+1 month", $date);
				  $m=date('M',$next);
				  $y=date('Y',$next);		  
			}	
			//*WR25042016
			if($d_no_of_week!='' && $d_no_of_week=='5'){
			
				$first_thu = strtotime("first thursday of $m $y");
				$last_thu = strtotime("last thursday of $m $y");
				$thu = $first_thu;
				$arr_thdate=array();
				$i=1;
				while($thu <= $last_thu)
				{
					$arr_thdate[$i]= date('Y-m-d', $thu);
					$thu = strtotime("next thursday", $thu);
					$i++;
				}
			}else if($d_no_of_week!='' && $d_no_of_week=='3'){
			
				$first_thu = strtotime("first tuesday of $m $y");
				$last_thu = strtotime("last tuesday of $m $y");
				$thu = $first_thu;
				$arr_thdate=array();
				$i=1;
				while($thu <= $last_thu)
				{
					$arr_thdate[$i]= date('Y-m-d', $thu);
					$thu = strtotime("next tuesday", $thu);
					$i++;
				}
			}
			
			/*
			  $first_thu = strtotime("first thursday of $m $y");	
			  $last_thu = strtotime("last thursday of $m $y");	
			  $thu = $first_thu;	
			  $arr_thdate=array();
			  $i=1;	
			  while($thu <= $last_thu)
			  {
			  	 	$arr_thdate[$i]= date('Y-m-d', $thu);
				    $thu = strtotime("next thursday", $thu); 
				    $i++;
			  }
			*/  
			  
			  return $arr_thdate;
		}//func
		
		function getSpecialDayInfo($sp_day){
			/**
			 * @desc
			 * @create 24062015
			 * @param String $sp_day : OPS2
			 * @return Array
			 */
			$arrDayOfWeek = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
			$arr_spday=array();
			switch($sp_day){
				case '00':$desc='VIP';$sp_day_num='00';break;
				case 'OPS0':$th_desc='พฤหัสบดีที่ 0';$sday_of_week='50';break;
				case 'OPS1':$th_desc='พฤหัสบดีที่ 1';$sday_of_week='51';break;
				case 'OPS2':$th_desc='พฤหัสบดีที่ 2';$sday_of_week='52';break;
				case 'OPS3':$th_desc='พฤหัสบดีที่ 3';$sday_of_week='53';break;
				case 'OPS4':$th_desc='พฤหัสบดีที่ 4';$sday_of_week='54';break;
				case 'OPT0':$th_desc='อังคารที่ 0';$sday_of_week='30';break;
				case 'OPT1':$th_desc='อังคารที่ 1';$sday_of_week='31';break;
				case 'OPT2':$th_desc='อังคารที่ 2';$sday_of_week='32';break;
				case 'OPT3':$th_desc='อังคารที่ 3';$sday_of_week='33';break;
				case 'OPT4':$th_desc='อังคารที่ 4';$sday_of_week='34';break;
				default: $sday_of_week='';$th_desc='';				
			}
			$arr_spday[0]['sday_of_week']=$sday_of_week;
			$arr_spday[0]['th_desc']=$th_desc;
							
			$d_no_of_week=substr($sday_of_week,0,1);//day no of week
			$w_no_of_month=substr($sday_of_week,1,1);//week no of month
						
			$arr_curr_opsday=self::getOpsDayOfMonth('Y',$d_no_of_week);//current month
			$curr_ops_day=$arr_curr_opsday[$w_no_of_month];
			$arr_spday[0]['curr_sp_date']=$curr_ops_day;
			
			$arr_next_opsday=self::getOpsDayOfMonth('N',$d_no_of_week);//next month				
			$next_ops_day=$arr_next_opsday[$w_no_of_month];
			$arr_spday[0]['next_sp_date']=$next_ops_day;
			return $arr_spday;
		}//func
		
		function getSpecialDayInfoBk13072015($sp_day){
			/**
			 * @desc
			 * @create 24062015
			 * @param String $sp_day : OPS2
			 * @return Array
			 */
			$arrDayOfWeek = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
			$arr_spday=array();
			switch($sp_day){
				case '00':$desc='VIP';$sp_day_num='00';break;
				case 'OPS0':$th_desc='พฤหัสบดีที่ 0';$sday_of_week='50';break;
				case 'OPS1':$th_desc='พฤหัสบดีที่ 1';$sday_of_week='51';break;
				case 'OPS2':$th_desc='พฤหัสบดีที่ 2';$sday_of_week='52';break;
				case 'OPS3':$th_desc='พฤหัสบดีที่ 3';$sday_of_week='53';break;
				case 'OPS4':$th_desc='พฤหัสบดีที่ 4';$sday_of_week='54';break;
				case 'OPT0':$th_desc='อังคารที่ 0';$sday_of_week='30';break;
				case 'OPT1':$th_desc='อังคารที่ 1';$sday_of_week='31';break;
				case 'OPT2':$th_desc='อังคารที่ 2';$sday_of_week='32';break;
				case 'OPT3':$th_desc='อังคารที่ 3';$sday_of_week='33';break;
				case 'OPT4':$th_desc='อังคารที่ 4';$sday_of_week='34';break;
				default: $sday_of_week='';$th_desc='';				
			}
			$arr_spday[0]['sday_of_week']=$sday_of_week;
			$arr_spday[0]['th_desc']=$th_desc;
							
			$w_day=substr($sday_of_week,0,1);
			$timestamp=strtotime($arrDayOfWeek[$w_day]);
			$arr_spday[0]['curr_sp_date']=date("Y-m-d",$timestamp);
			//$next_ops_day=strtotime("+7 day", $timestamp);
			$next_ops_day=strtotime("+1 months", $timestamp);
			$arr_spday[0]['next_sp_date']=date("Y-m-d",$next_ops_day);
			return $arr_spday;
		}//func
		
		public function setOpsDayMember($cust_day){			
			$this->m_ops_day=$cust_day;
		}//func
		
		public function getOpsDayMember(){		
			return $this->m_ops_day;
		}//func
		
		function chkModeOnLine(){
			/**
			 * @desc
			 */
			$online_status='0';
			$sql_chkmode="SELECT online_status FROM `com_branch_computer` WHERE branch_id = '$this->m_branch_id'";
			$row_chkmode=$this->db->fetchAll($sql_chkmode);
			if(!empty($row_chkmode)){
				if($row_chkmode[0]['online_status']=='1'){
					$online_status='1';
				}
			}
			return $online_status;
		}//func

		
		function clearBrowserCache(){
			/**
			 * @desc
			 * @return
			 */
		    header("Pragma: no-cache");
		    header("Cache: no-cache");
		    header("Cache-Control: no-cache, must-revalidate");
		    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");		    
		}//func		
		
		/**
		 * @param $value
		 * @return mixed
		 */
		function escapeJsonString($value) { 
			/**
			 * @desc modify : 2013-06-11
			 * @desc list from www.json.org: (\b backspace, \f formfeed)
			 * @param String $value : value need to escape json
			 * @return String $result : clean value for json format
			 */
		    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		    $result = str_replace($escapers, $replacements, $value);
		    return $result;
		}//func

		function xorbit($data){
			/**
			 * 
			 * @desc
			 * @var unknown_type
			 * @return
			 */
		    $n1=ord(substr($data,0,1));
		    $data=substr($data,1);
		    $num=strlen($data);
		    for($i=0;$i<$num;$i++){
		        $n2=substr($data,$i,1);
		        $n1=$n1^ord($n2);
		    }
		    return(chr($n1));
		}//func		
		
		function callEDC($net_amt){
			/**
			 * @desc สั่งตัด EDC ผ่าน serial port
			 * @param Float $net_amt จำนวนเงินที่ต้องการรูดบัตรเครดิต
			 * @return null
			 */
			if($this->m_branch_tp=='F'){
				echo "complete";
				exit();
			}
			$pos=strpos($net_amt,'.');
			if($pos>0){
				//มีเศษสตางค์				
				$pos_satang=$pos+1;
				$bath=substr($net_amt,0,$pos);
				$satang=substr($net_amt,$pos_satang,2);
				$bath=str_pad($bath,10,"0",STR_PAD_LEFT);
				$satang=str_pad($satang,2,"0",STR_PAD_LEFT);
			}else{
				//ไม่มีเศษสตางค์
				$bath=$net_amt;
				$satang='00';
			}
			
			//open port
			$fd = dio_open('/dev/ttyS0', O_RDWR | O_NOCTTY);
			
			//initial var
//			dio_tcsetattr($fd, array(
//				  'baud' => 9600,
//				  'bits' => 8,
//				  'stop'  => 1,
//				  'parity' => 0
//			));
			
			dio_tcsetattr($fd, array(
			  'baud' => 9600,
			  'bits' => 8,
			  'stop' => 1,
			  'parity' => 0,
			  'flow_control' => 0,
			  'is_canonical' => 0
			));
			
			/* ตัด ยอดเงินในบัตร */
			
			//5 60 0000 0000 1 0 20 00 0 40 000000000200
			
			$dataout=chr(0)."5"."60"."0000"."0000"."1"."0"."20"."00"."0".chr(28)."40".chr(0).chr(18)."$bath"."$satang".chr(28).chr(3);
			$dataout=chr(2).$dataout.$this->xorbit($dataout);
			$aa=dio_write($fd,$dataout);
			
			//var_dump($aa);			
			dio_close($fd);
			echo "complete.";
		}//func
		
		
		function autoSettlement(){
			/**
			 * @desc
			 * @return
			 */
			//open port
			$fd = dio_open('/dev/ttyS0', O_RDWR | O_NOCTTY);
			//initial var
			dio_tcsetattr($fd, array(
				  'baud' => 9600,
				  'bits' => 8,
				  'stop'  => 1,
				  'parity' => 0
			));
			 //Settlement
			$dataout=chr(0)."&"."60"."0000"."0000"."1"."0"."50"."00"."0".chr(28)."HN".chr(0).chr(3)."001".chr(28).chr(3);
			$dataout=chr(2).$dataout.$this->xorbit($dataout);
			dio_write($fd,$dataout);
			dio_close($fd);
		}//func

		
		public function openCashDrawer(){
			/**
			 *@desc
			 *@return null 
			 */
			//echo shell_exec("./draw.out /dev/ttyS0");
			if($this->m_cashdrawer=='Y'){
				//echo shell_exec("/bin/cashdrawer /dev/ttyS0");
				echo shell_exec("/sbin/eject");
			}
		}//func
		
		function productProfile($product_id){
			/**
			 * @desc keep stock product transaction 
			 */
			$sql_set="INSERT INTO `trn_product_profile`
						 SET `corporation_id` = '$this->m_corporation_id',
								`company_id` = '$this->m_company_id',
								`branch_id` = '$this->m_branch_id',
								`doc_date` = '$this->doc_date',
								`product_id` = '$product_id',
								`begin` = '1',
								`onhand` = '1',
								`allocate` = '1',
								`process_id` = '1',
								`promo_code` = '1',
								`quantity` = '1',
								`stock_st` = '1',
								`balance` = '1',
								`reg_date` = CURDATE() ,
								`reg_time` = CURTIME() ,
								`reg_user` = '$this->cashier_id'";
			//$this->db->query($sql_set);
		}//func
		
		function timeOpenBill(){
			/**
			 * @desc
			 * @return
			 */
			//เปิดบิลก่อนเวลาที่กำหนดหรือไม่
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_time_branch");
			/*
			$arr_day = getdate();
			if($arr_day['weekday']=='Saturday' || $arr_day['weekday']=='Sunday'){
				//special day
				$sql_chktime="SELECT normal_open_time AS c_start_time,normal_close_time AS c_end_time
								FROM `com_time_branch`
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									'$this->doc_date' BETWEEN start_date AND end_date AND
									CURTIME( ) BETWEEN special_open_time AND special_close_time";
			}else{
				//normal day
				$sql_chktime="SELECT special_open_time AS c_start_time,special_close_time AS c_end_time
								FROM `com_time_branch`
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									'$this->doc_date' BETWEEN start_date AND end_date AND
									CURTIME( ) BETWEEN normal_open_time AND normal_close_time";
			}
			
			$row_chktime=$this->db->fetchAll($sql_chktime);
			if(count($row_chktime)>0){
				$arr_stime=explode(":",$row_chktime[0]['c_start_time']);
				$str_sdetail=$arr_stime[0].".00 น.";
				
				$arr_etime=explode(":",$row_chktime[0]['c_end_time']);
				$str_edetail=$arr_etime[0].".00 น.";
				return $str_sdetail."#".$str_edetail;
			}else{
				return 'N';
			}
			*/
			$str_result="Y";			
			$sql_time="SELECT 
								`start_date`, `end_date`, `normal_open_time`, `normal_close_time`, `special_open_time`, `special_close_time` 
							FROM 
								`com_time_branch`
							WHERE
								corporation_id = '$this->m_corporation_id' AND 
								company_id = '$this->m_company_id' AND 
								branch_id = '$this->m_branch_id'";
			$rows_time=$this->db->fetchAll($sql_time);
			if(count($rows_time)>0){
				$arr_day = getdate();
				if($arr_day['weekday']=='Saturday' || $arr_day['weekday']=='Sunday'){
					$time_begin=$rows_time[0]['special_open_time'];
					$time_end=$rows_time[0]['special_close_time'];
				}else{
					$time_begin=$rows_time[0]['normal_open_time'];
					$time_end=$rows_time[0]['normal_close_time'];
				}
				$now=time();
				if($now<strtotime($time_begin)){
					$str_result="N#L#".$time_begin;
				}else if($now>strtotime($time_end)){
					$sql_chkcloseday="SELECT count(*)
								FROM `com_dayend_time`
								WHERE 
									corporation_id = '$this->m_corporation_id' AND 
									company_id = '$this->m_company_id' AND 
									branch_id = '$this->m_branch_id' AND 
									dayend_date ='$this->doc_date'";
					$n_chk=$this->db->fetchOne($sql_chkcloseday);
					if($n_chk>0){
						$str_result="N#G#".$time_end;
					}
					
				}
			}
			return $str_result;
		}//func
		
		public function getClientProfile(){
			/**
			 * @desc
			 * @param
			 * @return
			 */
			$sql_profile="SELECT 
       								`com_ip`,`computer_no`,`pos_id`,`thermal_printer`,`cashdrawer`,`network`,`lock_status`
								 FROM 
									`com_branch_computer` 
								WHERE
								       `corporation_id`='$this->m_corporation_id' AND
								       `company_id`='$this->m_company_id' AND
								       `branch_id`='$this->m_branch_id' AND
								       `com_ip`='".$_SERVER['REMOTE_ADDR']."'";
			$row_profile=$this->db->fetchAll($sql_profile);
			return $row_profile;
		}//func
		
		public function getServerProfile(){
			/**
			 * @desc
			 * @param
			 * @return
			 */
			$sql_profile="SELECT 
       								`com_ip`,`computer_no`,`pos_id`,`thermal_printer`,`cashdrawer`,`network`,`lock_status`
								 FROM 
									`com_branch_computer` 
								WHERE
								       `corporation_id`='$this->m_corporation_id' AND
								       `company_id`='$this->m_company_id' AND
								       `branch_id`='$this->m_branch_id' AND
								       `computer_no`='1'";
			$row_profile=$this->db->fetchAll($sql_profile);
			return $row_profile;
		}//func
		
		public function getCorporationID(){
			return $this->m_corporation_id;
		}//func
		
		public function getCompanyID(){
			return $this->m_company_id;
		}//func
		
		public function getBranchID(){
			return $this->m_branch_id;
		}//func
		
		function getBFCCode($doc_no,$fix_number){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_sms_code_head");
			$r_number= substr($doc_no,-6);
			$first_pos = strpos($r_number, '0',0);
			if($first_pos==0){
				$r_number= str_replace("0","",$r_number);
			}			
			$bfc_code=$fix_number-$r_number;
			$front_number=substr($bfc_code,0,3);
			$back_number=substr($bfc_code,-3);
			$bfc_code=substr($this->m_branch_id,-3)."".$back_number."".$front_number;
			return $bfc_code;
		}//func		
		
		public function memberOnTrans($member_no){
			/**
			 * @name memberOnTransaction
			 * @desc
			 * @param String :$member_no is id of member
			 * @return Boolean 'Y' is on buy transaction,'N' is not on buy transaction
			 */
			$status_on_trans='N';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
			$sql_network="SELECT COUNT(*) 
									FROM com_branch_computer
										WHERE
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND
											`branch_id`='$this->m_branch_id' AND 
											`network`='Y'";
			$network=$this->db->fetchOne($sql_network);
			if($network>0){
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
					$sql_chk="SELECT member_no
									FROM `trn_promotion_tmp1` 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND
										`member_no`='$member_no'
								 UNION ALL
								 SELECT member_no
									FROM `trn_tdiary2_sl` 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`branch_id`='$this->m_branch_id' AND
										`doc_date`='$this->doc_date' AND
										`member_no`='$member_no'";	
					$row_chk=$this->db->fetchAll($sql_chk);
					if(count($row_chk)>0){
						$status_on_trans='Y';
					}
			}
			return $status_on_trans;
		}//func		
		
		function markMemberUsePriv($member_no,$customer_id,$promo_code,$promo_year){
			/**
			 * @desc
			 * @return
			 */
			if($member_no=='' && $customer_id=='' && $promo_code=='')
				return false;
			$objMember=new Model_Member();
			$objMember->setLogMemberPrivilege($member_no,$promo_code,$customer_id);
			$objCal=new Model_Calpromotion();
			$objCal->promo_wait($member_no,$this->doc_date,$promo_code,'',$this->m_branch_id);			
		}//func
		
		function markCancelBill($doc_no){
			/**
			 * @desc 10.100.53.52
			 * @return
			 */
			//*WR 28012014 move to Accessory Controllers
			//$objCal=new Model_Calpromotion();
			//$objCal->up_cancle_bill($doc_no);
			//$objCal->promo_wait_cancle($doc_no);	
			//*WR 28012014 move to Accessory Controllers
			unset($this->db);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("pdy","pdiary1");
			$sql_cancel="UPDATE pdiary1 AS a
								INNER JOIN pdiary2 AS b ON a.doc_no = b.doc_no
								SET 
									a.`flag`='C',
									b.`flag`='C'
								WHERE 
									a.doc_no ='$doc_no'";	
			$this->db->query($sql_cancel);			
		}//func
		
		function unMarkMemberUsePriv($member_no,$customer_id,$promo_code,$promo_year){
			/**
			 * @desc
			 * @return
			 */
			$objMember=new Model_Member();
			$objMember->unLogMemberPrivilege($member_no,$promo_code,$customer_id);
		}//func
		
		function chkForCancelMemPrivilage($doc_no){
			/**
			 * @desc เกิดจากการยกเลิกเอกสารหลังการบันทึกบิล
			 * @desc Last modify : 28012014
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_chk="SELECT doc_date, member_id, birthday_card_st, co_promo_code, reg_date
							FROM `trn_diary1`
							WHERE doc_no = '$doc_no' AND birthday_card_st='Y'";
			$row_chk=$this->db->fetchAll($sql_chk);
			if(count($row_chk)>0){
				$member_no=$row_chk[0]['member_id'];
				$promo_code=$row_chk[0]['co_promo_code'];
				$arr_date=explode('-',$row_chk[0]['doc_date']);
				$promo_year=$arr_date[0];				
				$sql_card="SELECT customer_id FROM `crm_card` WHERE member_no = '$member_no'";
				$row_card=$this->db->fetchAll($sql_card);
				if(count($row_card)>0){
					$customer_id=$row_card[0]['customer_id'];					
					//WR28012014 ยกเลิกการ mark สิทธิของ Act เปลี่ยนมาปลดล๊อกเฉพาะข้างล่าง
					//$this->unMarkMemberUsePriv($member_no,$customer_id,$promo_code,$promo_year);
					$objMember=new Model_Member();
					$objMember->unLogMemberPrivilege($member_no,$promo_code,$customer_id);
				}
			}
			
		}//func
		
		function getPromoItems(){
			/**
			 * @modify 25072012
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_promo="SELECT* FROM promo_head 
								WHERE 
									'$this->doc_date' BETWEEN start_date AND end_date AND
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id'";
			$rows_promo=$this->db->fetchAll($sql_promo);
			return $rows_promo;
		}//func
		
		function getPromoItemsNextWeek(){
			/**
			 * @modify 21082012
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_promo="SELECT* FROM promo_head 
								WHERE 
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id' AND
									start_date> DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
			$rows_promo=$this->db->fetchAll($sql_promo);
			return $rows_promo;
		}//func
		
		function getPromoItemsDetails($promo_code){
			/**
			 * @modify 25072012
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
			$sql_promo="SELECT a.promo_tp,
										   a.seq_pro,
										   a.product_id,
										   a.quantity,
										   a.type_discount,
										   a.discount,
										   a.get_point,
										   a.discount_member,
										   b.name_product
								FROM 
										  promo_detail AS a LEFT JOIN com_product_master AS b
										  ON(a.product_id=b.product_id)
								WHERE 
									a.promo_code='$promo_code' AND 
									'$this->doc_date' BETWEEN a.start_date AND a.end_date AND
									a.corporation_id='$this->m_corporation_id' AND 
									a.company_id='$this->m_company_id'";
			$rows_promo=$this->db->fetchAll($sql_promo);
			return $rows_promo;
		}//func
		
		function getPromoItemsDetailsNextWeek($promo_code){
			/**
			 * @modify 25072012
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
			$sql_promo="SELECT a.promo_tp,
										   a.seq_pro,
										   a.product_id,
										   a.quantity,
										   a.type_discount,
										   a.discount,
										   a.get_point,
										   a.discount_member,
										   b.name_product
								FROM 
										  promo_detail AS a LEFT JOIN com_product_master AS b
										  ON(a.product_id=b.product_id)
								WHERE 
									a.promo_code='$promo_code' AND
									a.corporation_id='$this->m_corporation_id' AND 
									a.company_id='$this->m_company_id'";
			$rows_promo=$this->db->fetchAll($sql_promo);
			return $rows_promo;
		}//func
		
		function cancelPoint($doc_no){
			/**
			 * @desc cancel point of member by doc_no on 10.100.53.52
			 * @return null
			 */
			if($doc_no==''){
				return false;
			}else{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("point","mem_pt2");
				$sql_upd="UPDATE mem_pt2
									SET
											flag='C'
									WHERE
											shop='$this->m_branch_id' AND
											doc_no='$doc_no'";
					$res_upd=$this->db->query($sql_upd);
					if($res_upd){
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_mark_point");
						$sql_upstatus="UPDATE					
												trn_mark_point						
											SET
												flag='C'
											WHERE
											 	corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND 	
												branch_id='$this->m_branch_id' AND 
												doc_no='$doc_no'";
						$this->db->query($sql_upstatus);
					}
				
			}
		}//func
		
		function initMarkPoint($doc_no_curr){
			/**
			 * @desc get doc_no from trn_diary1 where flag<>'C'
			 * @desc and insert to trn_mark_point then call function markPoint() to add point by upstatus='N'
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_mark_point");
			//STEP1 CLEAR
			$sql_del="DELETE FROM trn_mark_point WHERE doc_date < DATE_SUB(NOW(), INTERVAL 2 DAY)";
			$this->db->query($sql_del);			
			//STEP2 INSERT LOG
			$sql_add="INSERT INTO trn_mark_point(
								 	`corporation_id`,`company_id`,`branch_id`,
								    `doc_date`,`doc_time`,`doc_no`,`flag`,
								    `member_id`,`point1`,`point2`,`redeem_point`,
								    `total_point`,`upstatus`,`reg_date`,`reg_time`
							)
							SELECT
									 `corporation_id`,`company_id`,`branch_id`,
									  `doc_date`,`doc_time`,`doc_no`,
									  `flag`,`member_id`,`point1`,
									  `point2`,`redeem_point`,`total_point`,'N',CURDATE(),CURTIME()
							FROM
								trn_diary1
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND 	
								branch_id='$this->m_branch_id' AND 
								flag<>'C' AND
								doc_no='$doc_no_curr' AND
								total_point<>'0'	";
			$this->db->query($sql_add);
			//STEP3 UP TO CLUSTER
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_d1="SELECT 
							branch_id,doc_no,doc_date,flag,member_id,point1,point2,redeem_point,total_point 
						FROM 
							trn_mark_point 
						WHERE 
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND 	
							branch_id='$this->m_branch_id' AND 							
							doc_date='$this->doc_date' AND upstatus='N'";
			$row_d1=$this->db->fetchAll($sql_d1);
			if(count($row_d1)>0){
				unset($this->db);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("point","mem_pt2");
				$arr_tmp=array();
				$i=0;
				foreach($row_d1 as $data){
					$doc_no=$data['doc_no'];					
					$sql_chk="SELECT COUNT(*) FROM mem_pt2 WHERE doc_no='$doc_no'";
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk==0){
							//insert to mem_pt2
							$sql_add="INSERT INTO mem_pt2
											SET
													doc_no='$doc_no',
													doc_dt='$data[doc_date]',
													flag='$data[flag]',
													member='$data[member_id]',
													point='$data[total_point]',
													shop='$this->m_branch_id',
													request_time=CURTIME()";
							$res_mark=$this->db->query($sql_add);
							if($res_mark){
								$arr_tmp[$i]=$doc_no;
								$i++;
							}
					}
				}//foreach
				//STEP4 UPDATE STATUS LOG
				if(!empty($arr_tmp)){
					unset($this->db);
					foreach($arr_tmp as $doc_no){						
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_mark_point");
						$sql_upstatus="UPDATE					
												trn_mark_point						
											SET
												upstatus='Y'
											WHERE
											 	corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND 	
												branch_id='$this->m_branch_id' AND 
												doc_no='$doc_no'";
						$this->db->query($sql_upstatus);
					}//foreach
				}
			}		
		}//func
		
		function markPoint(){
			/**
			 * 
			 * @desc add member point to jinet 10.100.53.52
			 * @desc and update field upstatus='N' on table trn_mark_point on local shop
			 * @modify 15/06/2012
			 * @return null
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_d1="SELECT 
							branch_id,doc_no,doc_date,flag,member_id,point1,point2,redeem_point,total_point 
						FROM 
							trn_mark_point 
						WHERE 
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND 	
							branch_id='$this->m_branch_id' AND 							
							doc_date='$this->doc_date'";
			$row_d1=$this->db->fetchAll($sql_d1);
			if(count($row_d1)>0){
				unset($this->db);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("point","mem_pt2");
				$arr_tmp=array();
				$i=0;
				foreach($row_d1 as $data){
					$doc_no=$data['doc_no'];					
					$sql_chk="SELECT COUNT(*) FROM mem_pt2 WHERE doc_no='$doc_no'";
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk==0){
							//insert to mem_pt2
							$sql_add="INSERT INTO mem_pt2
											SET
													doc_no='$doc_no',
													doc_dt='$data[doc_date]',
													flag='$data[flag]',
													member='$data[member_id]',
													point='$data[total_point]',
													shop='$this->m_branch_id',
													request_time=CURTIME()";
							$res_mark=$this->db->query($sql_add);
							if($res_mark){
								$arr_tmp[$i]=$doc_no;
								$i++;
							}
					}
				}//foreach
				
				if(!empty($arr_tmp)){
					unset($this->db);
					foreach($arr_tmp as $doc_no){						
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_mark_point");
						$sql_upstatus="UPDATE					
												trn_mark_point						
											SET
												upstatus='Y'
											WHERE
											 	corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND 	
												branch_id='$this->m_branch_id' AND 
												doc_no='$doc_no'";
						$this->db->query($sql_upstatus);
					}//foreach
				}
			}				
		}//func		
		
		function markPoint99($doc_no){
			/**
			 * 
			 * @desc add member point to jinet 10.100.53.52
			 * @desc and update field upstatus='N' on table trn_mark_point on local shop
			 * @modify 15/06/2012
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_d1="SELECT 
							branch_id,doc_no,doc_date,flag,member_id,point1,point2,redeem_point,total_point 
						FROM 
							trn_diary1 
						WHERE 
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND 	
							branch_id='$this->m_branch_id' AND 
							flag<>'C' AND
							doc_no='$doc_no' AND
							total_point<>'0'	";
			$row_d1=$this->db->fetchAll($sql_d1);
			if(count($row_d1)>0){
				$doc_no=$row_d1[0]['doc_no'];
				$doc_date=$row_d1[0]['doc_date'];
				$flag=$row_d1[0]['flag'];
				$member_id=$row_d1[0]['member_id'];
				$point1=$row_d1[0]['point1'];
				$point2=$row_d1[0]['point2'];
				$redeem_point=$row_d1[0]['redeem_point'];
				$total_point=$row_d1[0]['total_point'];			
				unset($this->db);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("point","mem_pt2");
				$sql_add="INSERT INTO mem_pt2
								SET
										doc_no='$doc_no',
										doc_dt='$doc_date',
										flag='$flag',
										member='$member_id',
										point='$total_point',
										shop='$this->m_branch_id',
										request_time=CURTIME()";
				$res_mark=$this->db->query($sql_add);
				if($res_mark){
					unset($this->db);
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_mark_point");
					$sql_upstatus="UPDATE					
											trn_mark_point						
										SET
											upstatus='Y'
										WHERE
										 	corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND 	
											branch_id='$this->m_branch_id' AND 
											doc_no='$doc_no'";
					$this->db->query($sql_upstatus);
				}
			}				
		}//func		
		
		public function getDocAutoComplete($term="",$str_doctp=""){
			/**
			 * @desc 
			 * @param String $term : word to find auto complete
			 * @param String $str_doctp : 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			if($term=="" || $str_doctp=="") return false;
			$arr_date=explode("-",$this->doc_date);
			$this->year=$arr_date[0];
			$this->month=intVal($arr_date[1]);
			$arr_doctp=explode(",",$str_doctp);
			$in_doctp='';
			foreach($arr_doctp as $doc_tp){
				$doc_tp=strtoupper($doc_tp);
				$in_doctp.="'$doc_tp',";
			}//foreach
			$pos=strrpos($in_doctp,',');
			if($pos!=0){
				$in_doctp=substr($in_doctp,0,$pos);
			}
			$sql_product="SELECT 
									doc_date,doc_no
								FROM 
									`trn_diary1`
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									doc_date='$this->doc_date' AND
									doc_tp IN($in_doctp) AND
									doc_no LIKE '$term%' 
								GROUP BY doc_no
								ORDER by doc_no";	
			$rows=$this->db->fetchAll($sql_product);
			return $rows;
		}//func
		
		public function countQtyTmp($promo_code=''){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$wh_promo_code='';
			if($promo_code!=''){
				$wh_promo_code.=" AND `promo_code`='$promo_code'";
			}
			$sql_item="SELECT 
							COUNT(*) 
						FROM 
							`trn_tdiary2_sl`
						WHERE
							`corporation_id`='$this->m_corporation_id'AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND 
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date' $wh_promo_code";
			$n_item=$this->db->fetchOne($sql_item);
			return $n_item;			
		}//func
		
		public function browsDocStatus($doc_tp='',$str_status_no=''){
			/**
			 * @name browsDocStatus
			 * @desc brow doc_status field from doc_status where doc_tp
			 * @param $id default ""
			 * @return array of rows from where
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_status");
			$wh_doc_tp='';
			if($doc_tp!=''){
				$wh_doc_tp=" AND doc_tp='$doc_tp'";
			}
			
			if($str_status_no!='' && $str_status_no!="all"){
				$pos=strpos($str_status_no,',');
				if($pos){
					$str_stsno='';
					$arr_statusno=explode(',',$str_status_no);
					for($i=0;$i<count($arr_statusno);$i++){
						$str_stsno.="'".$arr_statusno[$i]."',";
					}
					$rpos=strrpos($str_stsno,',');
					if($rpos)
						$str_stsno=substr($str_stsno,0,$rpos);
				}else{
					$str_stsno=$str_status_no;
				}
				$sql_doc="SELECT status_no,doc_tp,description 
								FROM com_doc_status 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											status_no IN($str_stsno)";				
			}else if($str_status_no=="all"){
				$sql_doc="SELECT status_no,doc_tp,description 
							FROM com_doc_status 
								WHERE 
									corporation_id='$this->m_corporation_id' AND 
									company_id='$this->m_company_id'";
			}
			$sql_doc.=$wh_doc_tp;
			$sql_doc.=" AND '$this->doc_date' BETWEEN start_date AND end_date";
			$rows=$this->db->fetchAll($sql_doc);
			return $rows;
		}//func
		
		public function getStockSt($doc_tp){
			/**
			 * @desc
			 * @param
			 * @return
			 */
			$this->doc_tp=$doc_tp;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
			$sql_stkst="SELECT stock_st
							FROM `com_doc_no` 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND 
									doc_tp='$this->doc_tp'";
			$this->stock_st=$this->db->fetchOne($sql_stkst);
			return $this->stock_st;
		}//func
		
		public function getDocTpByDocNo($doc_no){
			/**
			 * @desc
			 * @param String $status_no
			 * @return String doc_tp
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_doctp="SELECT doc_tp 
							FROM `trn_diary1` 
								WHERE doc_no='$doc_no'";
			$doc_tp=$this->db->fetchOne($sql_doctp);
			return $doc_tp;
		}//func
		
		public function getDocTp($status_no){
			/**
			 * @desc
			 * @param String $status_no
			 * @return String doc_tp
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_status");
			$sql_doctp="SELECT doc_tp 
							FROM `com_doc_status` 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									status_no='$status_no'";
			$doc_tp=$this->db->fetchOne($sql_doctp);
			return $doc_tp;
		}//func
		
		public function getSatang($net_amt,$flgs='normal'){
			/**
			 * @desc for support dollars us
			 * @create 05062017
			 * @return
			 */
			//net_amt = 4.275
			if(!is_numeric($net_amt)) return '0.00';	
			if($flgs=='normal'){
				$net_amt=round($net_amt, 2);
				$net_amt= number_format((float)$net_amt, 2, '.', '');
			}			
			return $net_amt;
		}//func
		
		public function getSatang05062017($net_amt,$flgs='normal'){
			/**
			 * @desc
			 * @return
			 */		
			if(!is_numeric($net_amt)) return '0.00';		
			$stangpos=strrpos($net_amt,'.');
			
			$old_pos=strrpos($net_amt,'.');
			
			if($stangpos>0){
				$stangpos+=1;
				$stang=substr($net_amt,$stangpos,2);			
				$stang=substr($stang."00",0,2);//*WR 19072012 resol 412.5 to 412.50
			}else{
				$stang=0;
			}
			$stang=(int)$stang;		
			if($flgs=='normal'){
				if(($stang >= 1) && ($stang <= 24)) $stang=00;
				if(($stang >= 26) && ($stang <= 49)) $stang=25;
				if(($stang >= 51) && ($stang <= 74)) $stang=50;
				if(($stang >= 76) && ($stang <= 99)) $stang=75;
			}else if($flgs=='up'){
				if(($stang >= 1) && ($stang <= 25)) $stang=25;
				if(($stang >= 26) && ($stang <= 50)) $stang=50;
				if(($stang >= 51) && ($stang <= 75)) $stang=75;
				if(($stang >= 76) && ($stang <= 99)){
					$stang='00';
					$net_amt=$net_amt+1;
					$stangpos_net_amt=strrpos($net_amt,'.');
					if($stangpos_net_amt>$old_pos){
						$stangpos+=1;
						//ex. 99.90 ==> 100.00
					}					
					
				}
			}
			if($stangpos>0){
				$net_amt=substr($net_amt,0,$stangpos).$stang;
			}
			return $net_amt;
		}//func
		
		public function getDocDate(){
			/**
			 * @desc ref table com_doc_date current
			 * @return current doc_date value of table com_doc_date
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_date");
			$sql_docdate="SELECT doc_date FROM com_doc_date 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id'";
			$row_docdate=$this->db->fetchCol($sql_docdate);
			return $row_docdate[0];
		}//func		
		
		function getPosConfig($code_type){
			/**
			 *@desc
			 *@param String $code_type : 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_pos_config");
			$sql_posconfig="SELECT 
										value_type,default_status,condition_status,default_day,default_day,condition_day,start_date,end_date
								  FROM 
								  		`com_pos_config` 
								  WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND 
										branch_no='$this->m_branch_no' AND 
										code_type='$code_type'";	
			$row_posconfig=$this->db->fetchAll($sql_posconfig);			
			if(count($row_posconfig)>0){
				return $row_posconfig;
			}else{
				return false;
			}			
		}//func
		
		public function checkDocDate(){
			/**
			 * @desc
			 * @return $this->doc_date
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_date");
			$sql_docdate="SELECT doc_date FROM com_doc_date 
								WHERE corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id'";			
			$row_docdate=$this->db->fetchCol($sql_docdate);
			if(count($row_docdate)<1) return false;
			$this->doc_date=$row_docdate[0];//assign doc_date system
			$row_pos_config=$this->getPosConfig('CHECK_DOC_DATE');				
			$value_type=$row_pos_config[0]['value_type'];
			$default_status=$row_pos_config[0]['default_status'];
			$condition_status=$row_pos_config[0]['condition_status'];
			$start_date=$row_pos_config[0]['start_date'];
			$end_date=$row_pos_config[0]['end_date'];			
			
			$arrDate1 = explode("-",$start_date);
			$arrDate2 = explode("-",$end_date);
			$arrDate3=explode("-",$this->doc_date);
			
			$timStmp1 = mktime(0,0,0,$arrDate1[1],$arrDate1[2],$arrDate1[0]);
			$timStmp2 = mktime(0,0,0,$arrDate2[1],$arrDate2[2],$arrDate2[0]);
			$timStmp3 = mktime(0,0,0,$arrDate3[1],$arrDate3[2],$arrDate3[0]);
			/*
			if($value_type=='C'){
				//echo "$timStmp3>=$timStmp1 &&$timStmp3<=$timStmp2";
					if($timStmp3>=$timStmp1 && $timStmp3<=$timStmp2){
						$default_status=$condition_status;
					}
			}
			*/
			
			if($value_type=='C'){
					if($this->doc_date>=$start_date && $this->doc_date<=$end_date){						
						$default_status=$condition_status;						
					}
			}
			
			if($default_status=='N'){
				//no compare doc_date and system date
				return $this->doc_date;
			}else if($default_status=='Y'){
				if($this->doc_date<>date('Y-m-d')){		
					return false;
				}else{
					return $this->doc_date;
				}
				
			}
			
		}//func
		
		function getLockStatus(){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_pos_config");
			$status_lock='N';
			$sql_chk1="SELECT default_status,condition_status 
								FROM com_pos_config 
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										branch_no='$this->m_branch_no' AND
										code_type='NO_KEYIN_MEMBER' AND
										'$this->doc_date' BETWEEN start_date AND end_date AND
										CURTIME() BETWEEN start_time AND end_time";
			//echo $sql_chk1;
			$row_chk1=$this->db->fetchAll($sql_chk1);
			if(count($row_chk1)>0){
				if($row_chk1[0]['condition_status']=='Y'){
					$status_lock='Y';//lock
				}
			}else{
				$sql_chk2="SELECT default_status
								FROM com_pos_config 
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' AND
										branch_no='$this->m_branch_no' AND
										code_type='NO_KEYIN_MEMBER'";
				$row_chk2=$this->db->fetchAll($sql_chk2);
				if(count($row_chk2)>0){
					if($row_chk2[0]['default_status']=='Y'){
						$status_lock='Y';//lock
					}
				}
			}
			return $status_lock;
		}//func
		
		function checkInvoice(){
			/**
			 * @desc
			 * @return Boolean 
			 */
			$arr_posconfig=$this->getPosConfig('TI_DAY');			
			if($arr_posconfig[0]['value_type']=='N'){
				if($this->doc_date>=$arr_posconfig[0]['start_date'] && $this->doc_date<=$arr_posconfig[0]['end_date']){
					$default_day=$arr_posconfig[0]['condition_day'];
				}else{
					$default_day=$arr_posconfig[0]['default_day'];
				}
			}
		
			$chk_date=date('Y-m-d', strtotime($this->doc_date.' -'.$default_day.' days'));
			//check ti by $chk_date
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_in2shop_head");			
			$sql_inv="SELECT COUNT(*) 
							FROM trn_in2shop_head 
								WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										flag1='D' AND
										'$this->doc_date' > DATE_ADD(doc_date, INTERVAL '$default_day' DAY) AND
										status_transfer='Y'";			
		
			$n_inv=$this->db->fetchOne($sql_inv);
			if($n_inv>0){
				return false;
			}else{
				return true;
			}
		}//func
		
		function checkRQ(){			
			
			$arr_posconfig=$this->getPosConfig('RQ_DAY');			
			if($arr_posconfig[0]['value_type']=='N'){
				if($this->doc_date>=$arr_posconfig[0]['start_date'] && $this->doc_date<=$arr_posconfig[0]['end_date']){
					$default_day=$arr_posconfig[0]['condition_day'];
				}else{
					$default_day=$arr_posconfig[0]['default_day'];
				}
			}
			$chk_date=date('Y-m-d', strtotime($this->doc_date.' -'.$default_day.' days'));			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_rq");
			$sql_rq="SELECT COUNT(*) FROM trn_diary1_rq 
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								branch_id='$this->m_branch_id' AND 
								doc_date > '$chk_date' AND
								status_transfer='Y' ";
			$n_rq=$this->db->fetchOne($sql_rq);
			if($n_rq>0){
				return false;
			}else{
				return true;
			}
		}//func
		
		public function checkDocNoExist($doc_no,$flg_cancel){
			/**
			 * @desc :
			 * @param String $doc_no :reference for check exist
			 * @return Boolean : Y is found and N is not found
			 */
			$chk_flg_cancel="";
			if($flg_cancel=='Y'){
				$chk_flg_cancel=" AND `flag`<>'C'";
			}
			$status_exist='N';
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_t1="SELECT doc_no
						FROM trn_diary1
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`doc_no`='$doc_no' $chk_flg_cancel";
			$row_t1=$this->db->fetchAll($sql_t1);
			if(count($row_t1)>0){
				$status_exist='Y';
			}
			return $status_exist;
		}//func
		
		public function checkEn13($member_id){
			/**
			 * @desc
			 * @param String $member_id
			 * @return Boolean $result TRUE or FALSE
			 */
			$member_id=trim($member_id);
			if(strlen($member_id)!=13) return FALSE;
			$arr_tmp=str_split($member_id);
			$key_odd=0;
			$key_even=0;
			$key_sum=0;
			$arr_code=array();
			$i=1;
			foreach($arr_tmp as $key){
				$arr_code[$i]=$key;
				$i++;
			}
			foreach($arr_code as $key=>$val){
				if($key!=13){
					($key%2==0)?$key_even+=$val*3:$key_odd+=$val;
				}
			}//foreach
			
			$key_sum=$key_odd+$key_even;
			$key_mod=$key_sum%10;
			$key_chk=10-$key_mod;
			if($key_chk==10){
				$key_chk=0;
			}
			($arr_code[13]==$key_chk)?$result=TRUE:$result=FALSE;
			return $result;
		}//func
		
		public function flgTotalPage($tblname,$rp,$where_other){
			/**
			 * @desc for flexigrid 
			 * @param String $tblname is table target
			 * @param Integer $rp is row per page
			 * @return Interger $cpage is total of page
			 */
			if($tblname=='') return 0;
			$sql_row = "SELECT count(*) 
							FROM $tblname 
							WHERE corporation_id='$this->m_corporation_id' 
									 AND company_id='$this->m_company_id' 
									 AND branch_id='$this->m_branch_id' 
									 $where_other";			
			$crow=$this->db->fetchOne($sql_row);
			$cpage=ceil($crow/$rp);
			return $cpage;
		}//func
		
		public function chkRegFree($application_id){
			/**
			 * @desc
			 * @param String $application_id id of catalog
			 * @return value of field register_free
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
			$sql_regfree="SELECT register_free 
								FROM com_application_head
									WHERE corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND 
												application_id='$application_id'";
			$row_regfree=$this->db->fetchCol($sql_regfree);
			if(count($row_regfree)>0){
				return $row_regfree[0];
			}else{
				return '';
			}
		}//func
		
		public function getCompany(){
			/**
			 * @desc
			 * @return data of company
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","`com_company");
			$sql_com="SELECT *
							FROM `com_company`
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id'";
			$row_com=$this->db->fetchAll($sql_com);
			return $row_com;
		}//func
		
		public function getBranch(){
			/**
			 * @desc
			 * @return shop_name of branch
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","`com_branch_detail");
			$sql_branch="SELECT *
							FROM `com_branch_detail`
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								branch_id='$this->m_branch_id' AND
								branch_no='$this->m_branch_no'";
			$row_branch=$this->db->fetchAll($sql_branch);
			return $row_branch;
		}//func
		
		public function setBarcodeToProductID($product_id){
			/**
			 * 
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
			$found=false;
			if($product_id!=""){
				if(substr($product_id,0,3)=='885' && strlen($product_id)==13){
						//assume product_id is barcode
						$sql_nb="SELECT product_id 
										FROM com_product_master 
											WHERE 
												corporation_id='$this->m_corporation_id' AND 
												company_id='$this->m_company_id' AND
												barcode='$product_id'";				
						$row_nb=$this->db->fetchCol($sql_nb);
						if(count($row_nb)>0){
							$found=true;
							$product_id=$row_nb[0];	
						}else{					
							$sql_np="SELECT COUNT(*) 
											FROM `com_product_master` 
												WHERE 
													corporation_id='$this->m_corporation_id' AND 
													company_id='$this->m_company_id' AND
													product_id='$product_id'";
							$n_np=$this->db->fetchOne($sql_np);
							if($n_np>0){
								$found=true;
							}					
						}
					}else{
							//out of case check barcode then check product_id
							$sql_np="SELECT COUNT(*) 
												FROM `com_product_master` 
													WHERE 
														corporation_id='$this->m_corporation_id' AND 
														company_id='$this->m_company_id' AND
														product_id='$product_id'";
								$n_np=$this->db->fetchOne($sql_np);
								if($n_np>0){
									$found=true;
								}					
					}
					return $product_id;
			}else{
				return '';
			}
		}//func
		
		public function getProduct($product_id='',$quantity,$status_no='',$promo_tp=''){
			/**
			 * @desc
			 * @param String $product_id
			 * @return $product_id
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
			$found=false;
			if($product_id!=""){
				if(substr($product_id,0,3)=='885' && strlen($product_id)==13){
						//assume product_id is barcode
						$sql_nb="SELECT product_id 
										FROM com_product_master 
											WHERE 
												corporation_id='$this->m_corporation_id' AND 
												company_id='$this->m_company_id' AND
												barcode='$product_id'";	
						$row_nb=$this->db->fetchCol($sql_nb);
						if(count($row_nb)>0){
							$found=true;
							$product_id=$row_nb[0];	
						}else{					
							$sql_np="SELECT COUNT(*) 
											FROM `com_product_master` 
												WHERE 
													corporation_id='$this->m_corporation_id' AND 
													company_id='$this->m_company_id' AND
													product_id='$product_id'";
							$n_np=$this->db->fetchOne($sql_np);
							if($n_np>0){
								$found=true;
							}
						}
				 }else{
				 	//out of case check barcode then check product_id
				 	$sql_np="SELECT COUNT(*) 
									FROM `com_product_master` 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											product_id='$product_id'";
						$n_np=$this->db->fetchOne($sql_np);
						if($n_np>0){
							$found=true;
						}
				 }
				
			}//
			if($found){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
				//*WR10012013 prepend $status_no for product tester	not for sale except bill 19
				if(substr($product_id,0,2)=='11' && $status_no!='19'){
					return '4';
				}
				//check product lock 18092013
				if($status_no!='19' && $promo_tp!='F'){
					$res_lock=$this->getProductLock($product_id,$status_no);
					if($res_lock){
						return '3';
					}
				}
				//check onhand on com_stock_master
				$arr_year=explode("-",$this->doc_date);
				$year=$arr_year[0];
				$month=$arr_year[1];				
				$sql_stk="SELECT COUNT(*) 
									FROM com_stock_master 
										WHERE 
											corporation_id='$this->m_corporation_id' AND 
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND
											year='$year' AND
											month='$month' AND
											product_id='$product_id' AND
											'$quantity'<=onhand-allocate";
				$n_stk=$this->db->fetchOne($sql_stk);
				if($n_stk>0){
					return $product_id;
				}else{
					return '2';//not found product in com_stock_master
				}
			}else{
				return '1';// not found product in com_product_master
			}
		}//func
		
		public function getRngMemberNo(){
			/**
			 * @desc get runing number for dummy member_no
			 * @return Int member_no
			 * @create 19052015
			 * @modify 19042017
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_customer_id");
			$sql_n="SELECT member_no
			FROM `com_member_dummy`
			WHERE corporation_id='$this->m_corporation_id' AND company_id='$this->m_company_id'";
			$row_n=$this->db->fetchCol($sql_n);
			$n_dummy=$row_n[0];
			$sql_trn="SELECT member_id FROM `trn_diary1`
						WHERE status_no = '01' 	AND doc_date >= DATE_FORMAT( CURDATE( ) , '%Y-%m-01' ) - INTERVAL 3 MONTH ORDER BY member_id DESC LIMIT 0 , 1";
			$row_trn=$this->db->fetchCol($sql_trn);
			$id_trn=$row_trn[0];
			$id_trn=substr($id_trn,6);
			$id_trn=intval($id_trn);
			$id_trn=$id_trn+1;
			if($id_trn>=$n_dummy){
				$n_dummy=$id_trn;
			}
			return $n_dummy;
		}//func
		
		public function getCstNumber(){
			/**
			 * @desc
			 * @return Int costomer_id :running number of table com_customer_id
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_customer_id");
			$sql_n="SELECT customer_id 
						FROM com_customer_id 
							WHERE
								corporation_id='$this->m_corporation_id' AND 
								company_id='$this->m_company_id'";
			$row_n=$this->db->fetchCol($sql_n);
			return $row_n[0];
		}//
		
		public function getDocNumber($doc_tp){
			/**
			 * @name
			 * @desc
			 * @param
			 * @return
			 */
			$this->doc_tp=$doc_tp;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_doc_no");
			$sql_conf="SELECT doc_prefix1,def_value,doc_prefix2,run_no 
							FROM conf_doc_no
							WHERE 
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_conf=$this->db->fetchAll($sql_conf);			
			if(count($row_conf)>0){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
				$sql_docno="SELECT doc_no 
									FROM com_doc_no
										WHERE
											corporation_id='$this->m_corporation_id' AND
											company_id='$this->m_company_id' AND
											branch_id='$this->m_branch_id' AND
											doc_tp='$this->doc_tp'";
				$row_docno=$this->db->fetchAll($sql_docno);
				if(count($row_docno)>0){
					$doc_prefix1=$row_conf[0]['doc_prefix1'];
					$doc_prefix2=$row_conf[0]['doc_prefix2'];
					$def_value=$row_conf[0]['def_value'];
					$run_no=-1*$row_conf[0]['run_no'];
					$prefix2=$doc_prefix2.$row_docno[0]['doc_no'];
					$str_pattern=substr($prefix2,$run_no);
					$this->m_doc_no=$doc_prefix1.$this->doc_tp."-".$def_value."-".$str_pattern;
				}else{
					$this->m_doc_no="";
				}
				return $this->m_doc_no;
			}
		}//func
		
		public function getDocNumber22($doc_tp){
			/**
			 * @name getDocNumber new version
			 * @desc สร้างเลขที่เอกสารตามรูปแบบ เช่น "OP0000SL-0001-00000123"
			 * @param String $doc_tp : ประเภทเอกสาร
			 * @param Interger $doc_no : ลำดับเลขที่เอกสารปัจจุบันอ้างอิงจากตาราง com_doc_no
			 * @return String $this->m_doc_no เลขที่เอกสาร
			 * @modify 17082011
			 */	
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
			$this->doc_tp=$doc_tp;
			$stmt_docno=$this->db->select()
								->from('com_doc_no',array('doc_no'))
								->where('corporation_id=?',$this->m_corporation_id)
								->where('company_id=?',$this->m_company_id)
								->where('branch_id=?',$this->m_branch_id)
								->where('doc_tp=?',$this->doc_tp);
			$rowdoc=$stmt_docno->query()->fetchAll();
			if(count($rowdoc)>0){
				$str_pattern=str_pad($rowdoc[0]['doc_no'],8,"0",STR_PAD_LEFT);
				$this->m_doc_no=$this->m_company_id.$this->m_branch_id.$this->doc_tp."-".$this->m_branch_no."-".$str_pattern;
			}else{
				$this->m_doc_no="";
			}
			return $this->m_doc_no;
		}//func
		
		public function genAutoNumber($doc_type,$doc_no){	
			/**
			 * @name genAutoNumber old version
			 * @desc สร้างเลขที่เอกสารตามรูปแบบ เช่น "OP0001TI-0001-00000001"
			 * @param $doc_type : ประเภทเอกสาร
			 * @param $doc_no : ลำดับเลขที่เอกสารอ้างอิงจากตาราง doc_no
			 * @return $this->m_doc_no เลขที่เอกสาร
			 */	
			if($doc_no!=""){
				$str_pattern=sprintf('%08d',$doc_no);
				$this->m_doc_no=$this->m_branch_id.$doc_type."-".$this->m_branch_no."-".$str_pattern;
				return $this->m_doc_no;			
			}else{
				return "null";
			}
		}//func	
		
		public function TrancateTable($tbl_name=""){
			/**
			 * @name TrancateTable
			 * @desc clear data and auto index in table target
			 * @return null 
			 * delete AND `doc_date`='$this->doc_date' check computer_ip only
			 */
			if($tbl_name=="")	return false;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn",$tbl_name);
			$sql_clear="DELETE FROM 
								$tbl_name 
							WHERE 
									`corporation_id`='$this->m_corporation_id' AND
									`company_id`='$this->m_company_id' AND
									`branch_id`='$this->m_branch_id' AND
									`computer_no`='$this->m_computer_no' AND
									`computer_ip`='$this->m_com_ip'";
			$this->db->query($sql_clear);
		}//func
		
		
		
		public function getOnHandToUpdate(){
			
		}//func
		
		public function getDateTimeServer(){
			/**
			 * @name getDateTimeServer
			 * @desc get time of server
			 */
			$time_now=mktime(date('h'),date('i'),date('s'));
    		//print $time_now=date('l M dS, Y, h:i:s A',$time_now);
			//return date("d/m/Y")." ".date("H:i:s");
			return $time_now=date('d/m/Y H:i:s A',$time_now);
		}//func
		
		public function getDocStatusByType($doc_tp=''){
			/**
			 * @name getDocStatusByType
			 * @desc get document status from table doc_status where type of document
			 */
			$row=array();
			if($doc_tp=='') return $row;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_status");
			$sql_doc="SELECT status_no,description FROM com_doc_status WHERE doc_tp='$doc_tp' GROUP BY status_no";			
			$row=$this->db->fetchPairs($sql_doc);
			return $row;
		}//func
		
		public function getProductStatus(){
			/**
			 * @name getProductStatus
			 * @desc get product status and description from table product status 
			 * @param
			 * @return row of product_status and description
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_status");
			$sql_prod="SELECT product_status,description FROM com_product_status ORDER BY id";
			$stmt_prod=$this->db->query($sql_prod);
			if($stmt_prod->rowCount()>0){
				$row=$stmt_prod->fetchAll(PDO::FETCH_ASSOC);
				return $row;				
			}
		}//func
		
		public function getPaid(){
			/**
			 * @desc
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_paid");
			$sql_paid="SELECT paid,description FROM com_paid ORDER BY id";
			$stmt_paid=$this->db->query($sql_paid);
			if($stmt_paid->rowCount()>0){
				$row=$stmt_paid->fetchAll(PDO::FETCH_ASSOC);
				return $row;				
			}
		}//func
		
		public function week_number($date)
		{
			/**
			 * @desc find week number of the month
			 * @param Date $date
			 * @return week number of month
			 */
		    return ceil(substr($date,-2)/7);
		}//func		

		public function getPromotionDayRef($doc_date){
			/**
			 * @desc get array of promotion day
			 * @return Array $arr_spday : array of promotion day เอาไว้อ้างถึงสอบถามบิลย้อนหลังด้วยตาม $doc_date ใน $doc_no
			 */
			$arr_th_day=array("1"=>"อาทิตย์","2"=>"จันทร์","3"=>"อังคาร","4"=>"พุธ","5"=>"พฤหัสบดี","6"=>"ศุกร์","7"=>"เสาร์");
			$arr_en_day=array("1"=>"sun","2"=>"monday","3"=>"tuesday","4"=>"wednesday","5"=>"thursday","6"=>"friday","7"=>"saturday");
			$arr_spday=array();
			$weekno=$this->week_number($doc_date);
			$dayno=date('w',strtotime($doc_date))+1;			
			
			$arr_spday[0]=$dayno.$weekno;
			$arr_spday[1]=$arr_th_day[$dayno]."ที่".$weekno;
			return $arr_spday;
		}//func
		
		public function getPromotionDay(){
			/**
			 * @desc get array of promotion day
			 * @return Array $arr_spday : array of promotion day
			 */
			$arr_th_day=array("1"=>"อาทิตย์","2"=>"จันทร์","3"=>"อังคาร","4"=>"พุธ","5"=>"พฤหัสบดี","6"=>"ศุกร์","7"=>"เสาร์");
			$arr_en_day=array("1"=>"sun","2"=>"monday","3"=>"tuesday","4"=>"wednesday","5"=>"thursday","6"=>"friday","7"=>"saturday");
			$arr_spday=array();
			$weekno=$this->week_number($this->doc_date);
			$dayno=date('w',strtotime($this->doc_date))+1;			
			
			$arr_spday[0]=$dayno.$weekno;
			$arr_spday[1]=$arr_th_day[$dayno]."&nbsp;ที่&nbsp;".$weekno;
			return $arr_spday;
		}//func
		
		public function getPointOfDay($refer_member_st){
			/**
			 * @desc
			 * @param String $refer_member_st : old member for referent
			 * @return point of current day
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_point="SELECT (sum( point1 ) + sum( point2 ) + sum( redeem_point )) AS pointofdiary
							FROM `trn_diary1`
							WHERE 
								corporation_id='$this->m_corporation_id' AND 
								company_id='$this->m_company_id' AND 
								branch_id='$this->m_branch_id' AND
								member_id='$refer_member_st' AND
								flag<>'C' AND
								doc_date='$this->doc_date'";
			$row_point=$this->db->fetchCol($sql_point);
			($row_point[0]==null)?$pointofdiary=0:$pointofdiary=$row_point[0];
			$pointofmempoint=$this->getMemberPoint($refer_member_st);
			$point=$pointofdiary+$pointofmempoint;
			return $point;
		}//func
		
		public function getMemberPointLocal($member_no){
			/**
			 * @desc :get member point
			 * @param String :$member_no
			 * @return member point
			 */
//			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_mem_pt");
//			$sql_pointshop="SELECT SUM(point) AS point 
//										FROM com_mem_pt 
//										WHERE doc_dt='$this->doc_date' AND 
//											  member='$member_no' AND
//											  flag<>'C'";
//				$row_pointshop=$this->db->fetchCol($sql_pointshop);
//				(count($row_pointshop)>0)?$point_shop= $row_pointshop[0]:$point_shop=0;
//				return $point_shop;
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				$sql_pointshop="SELECT SUM(total_point) AS point 
										FROM trn_diary1 
										WHERE 
											doc_date='$this->doc_date' AND 
											member_id='$member_no' AND
											flag<>'C'";
				$row_pointshop=$this->db->fetchCol($sql_pointshop);
				(count($row_pointshop)>0)?$point_shop= $row_pointshop[0]:$point_shop=0;
				return $point_shop;
		}//func
		
		public function getMemberPoint($member_no){
			/**
			 * @desc 05062013
			 * @desc joke service
			 */
			$objCalX=new Model_Calpromotion();		
// 			$str_point=$objCalX->read_point($member_no);
// 			$arr_point=explode('@@@',$str_point);		
// 			$arr_point[0]=intval($arr_point[0]);
// 			$arr_point[2]=intval($arr_point[2]);
// 			$arr_point[3]=intval($arr_point[3]);			
// 			$r_point=$arr_point[3];	
			
			$r_point=$objCalX->read_point($member_no);			
			return $r_point;
		}//func
				
		public function getMemberPointAxe($member_no){
			/**
			 * version Axe
			 * @desc :get member point refer database of act 10.100.53.52
			 * @param String :$member_no
			 * @return member point
			 */
			//shop			
			try{
			    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_mem_pt");
				$sql_pointshop="SELECT SUM(point) AS point 
										FROM com_mem_pt 
										WHERE doc_dt='$this->doc_date' AND 
											  member='$member_no' AND
											  flag<>'C'";
				$row_pointshop=$this->db->fetchCol($sql_pointshop);
				(count($row_pointshop)>0)?$point_shop= $row_pointshop[0]:$point_shop=0;
			}catch (Exception $e){
			    $point_shop=0;
			}				
			//jinet คะแนนสมาชิกสะสมถึงเมื่อวาน	
			unset($this->db);
			try{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("point","mem_point");
				$sql_pointjinet1="SELECT mp_point_sum 
										FROM mem_point 
										WHERE 
											mp_new='$member_no'";
				$row_pointjinet1=$this->db->fetchCol($sql_pointjinet1);
				(count($row_pointjinet1)>0)?$point_jinet1= $row_pointjinet1[0]:$point_jinet1=0;			
			}catch (Exception $e){
			    $point_jinet1=0;
			}
			//สมาชิกเกิดการซื้อที่สาขาอื่น ที่ไม่ใช่สาขา ณ วันนั้น	
			try{
				$sql_pointjinet2="SELECT 
											SUM(point) AS point 
										FROM 
											mem_pt2 
										WHERE 
											if(SUBSTRING(doc_no,1,1) BETWEEN '0' AND '9', SUBSTRING(doc_no,1,4),SUBSTRING(doc_no,3,4)) <> '$this->m_branch_no' AND
											doc_dt='$this->doc_date' AND 
											 member='$member_no' AND
											 flag<>'C'";		
				$row_pointjinet2=$this->db->fetchCol($sql_pointjinet2);
				(count($row_pointjinet2)>0)?$point_jinet2= $row_pointjinet2[0]:$point_jinet2=0;			
			}catch (Exception $e){
			    $point_jinet2=0;
			}
			$point_total=$point_shop+$point_jinet1+$point_jinet2;
			return $point_total;
		}//func
		
		public function getMemberProfile($col,$txtsearch){
			/**
			 * @desc get member profile by key search
			 * @param String $col :field of table
			 * @param String $txtsearch :key for search
			 * @return JSON format of member profile
			 * @last modify 03/11/2011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			if($col=='') return '0';			
			$col=$this->db->quoteIdentifier($col);
			$where = $this->db->quoteInto("$col LIKE ?","$txtsearch%");
			$stmt_member=$this->db->select()
									->from(array('a'=>'crm_card'),array('a.customer_id','a.member_no','a.apply_date','a.special_day','a.cardtype_id','a.apply_promo','a.expire_date'))
									->join(array('b'=>'crm_profile'),'a.customer_id=b.customer_id',array('b.name','b.surname','b.birthday','b.home',
												'b.h_address','b.h_village_id','b.h_village','b.h_soi','b.h_road','b.h_district','b.h_amphur',
												'b.h_province','b.h_postcode'))
									->where($where);
			$row_member=$stmt_member->query()->fetchAll();
			$json='0';
			if(count($row_member)>0){
				$objUtils=new Model_Utils();
				$json=$objUtils->ArrayToJson('member',$row_member[0],'yes');
			}
			return $json;			
		}//func
		
		public function getSaleman($employee_id){
			/**
			 * @desc
			 * @param String $employee_id
			 * @return info of employee
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$this->employee_id=$employee_id;
			$sql_emp="SELECT employee_id,name,surname,remark 
								FROM conf_employee
								WHERE 
									employee_id='$this->employee_id' AND
									group_id IN('OpShopMng','OpShopEmp','AUDIT')";
			$row_emp=$this->db->fetchAll($sql_emp);
			if(count($row_emp)>0){
				//check check in
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$status_check=$objPos->empCheckIn($this->m_branch_id,$this->doc_date,$employee_id);
				$row_emp[0]['check_status']=$status_check;
				return $row_emp;
			}else{
				return "";
			}
			
		}//func
		
		public function getEmployee($employee_id){
			/**
			 * @name getEmployee
			 * @desc
			 * @param String $employee_id 
			 * @return Array of employee info
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$this->employee_id=$employee_id;
			$stmt_emp=$this->db->select()
								->from('conf_employee',array('employee_id','name','surname','remark'))
								->where('employee_id=?',$this->employee_id)
								->where('group_id=?',$this->group_id);
			$row_emp=$stmt_emp->query()->fetchAll();
			if(count($row_emp)>0){
				return $row_emp;
			}else{
				return "";
			}
		}//func
		
		public function swapCashier($employee_id){
			/**
			 * @desc modify : WR26042013 งดใช้งาน
			 * @param String $employee_id
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$this->employee_id=$employee_id;
			$sql_emp="SELECT employee_id,name,surname,remark 
								FROM conf_employee
								WHERE 
									employee_id='$this->employee_id' AND
									group_id IN('OpShopMng','OpShopEmp','AUDIT')";
			$row_emp=$this->db->fetchAll($sql_emp);
			if(count($row_emp)>0){
				//check login system ตรวจสอบลูกค้ารูดบัตรหรือยัง
				
//				$objPosPlugin=new SSUP_Controller_Plugin_PosGlobal();
//				$res_check=$objPosPlugin->checkInOut($this->doc_date,$this->employee_id);

				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$res_check=$objPos->empCheckIn($this->m_branch_id,$this->doc_date,$employee_id);				
				
				if($res_check=='Y'){
					$row_emp[0]['check_in_status']='Y';
					//set new session
					$session = new Zend_Session_Namespace('empprofile');
					$session->empprofile['user_id']=$row_emp[0]['employee_id']; 
					$session->empprofile['name']=$row_emp[0]['name'];
					$session->empprofile['surname']=$row_emp[0]['surname'];
					
				}else{
					$row_emp[0]['check_in_status']='N';
				}
				sleep(1);
				return $row_emp;
			}else{
				return "";
			}
			
		}//func
		
		public function getRosSaleman($employee_id,$password){
			/**
			 * @name getEmployee
			 * @desc ros
			 * @param String $employee_id
			 * @return Array of employee info
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$sql_ros="SELECT employee_id,name,surname FROM conf_employee
			WHERE employee_id='$employee_id'  AND password_id='$password' AND group_id in('OpArom','OpRos','OpShopEmp','OpShopMng')";
			$rows_ros=$this->db->fetchAll($sql_ros);
			if(!empty($rows_ros)){
				//check check in
				$employee_id_chk=$rows_ros[0]['employee_id'];
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$status_check=$objPos->empCheckIn($this->m_branch_id,$this->doc_date,$employee_id_chk);
				$rows_ros[0]['check_status']=$status_check;
				return $rows_ros;
			}else{
				return "";
			}
		
		}//func
		
		public function getRos($employee_id,$password){
			/**
			 * @name getEmployee
			 * @desc ros
			 * @param String $employee_id
			 * @return Array of employee info
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$sql_ros="SELECT employee_id,name,surname FROM conf_employee
			WHERE employee_id='$employee_id'  AND password_id='$password' AND group_id IN('SsArom','SsRos')";
			$rows_ros=$this->db->fetchAll($sql_ros);
			if(!empty($rows_ros)){
				//check check in
				$employee_id_chk=$rows_ros[0]['employee_id'];
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$status_check=$objPos->empCheckIn($this->m_branch_id,$this->doc_date,$employee_id_chk);
				$rows_ros[0]['check_status']=$status_check;
				return $rows_ros;
			}else{
				return "";
			}
		}//func
		
		public function getAudit($employee_id){
			/**
			 * @name getEmployee
			 * @desc audit ใช้รหัสผ่านตรวจสอบไม่ใช่รหัสพนักงานเหมือนพนักงานขาย
			 * @param String $employee_id 
			 * @return Array of employee info
			 * @lastmodify
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$this->employee_id=$employee_id;
			$stmt_emp=$this->db->select()
								->from('conf_employee',array('employee_id','name','surname'))
								->where('password_id=?',$this->employee_id)
								->where('group_id="AUDIT"');
			$row_emp=$stmt_emp->query()->fetchAll();
			if(count($row_emp)>0){
				//check check in
				$employee_id_chk=$row_emp[0]['employee_id'];
				$objPos=new SSUP_Controller_Plugin_PosGlobal();
				$status_check=$objPos->empCheckIn($this->m_branch_id,$this->doc_date,$employee_id_chk);
				$row_emp[0]['check_status']=$status_check;
				return $row_emp;
			}else{
				return "";
			}			

		}//func		
		
		public function getCustomerIdFormat($customer_id){
			/**
			 * @desc
			 * @param
			 */
			$str_format="000000".$customer_id;
			//$str_pattern=str_pad($customer_id,6,"0",STR_PAD_LEFT);
			$str_pattern=substr($str_format,-6);
			$crm_customer_id=$this->m_company_id."-".$this->m_branch_id."-".$str_pattern;
			return $crm_customer_id;
		}//func
		
		public function getMemberOffLineByKeyWord($mobile_no,$id_card,$name,$surname){
			/**
			 * @desc search local by keyword.case linke offline or search online not found
			 * @modify : 21062013
			 * @return Set of array member data
			 */
			$mobile_no=trim($mobile_no);
			$id_card=trim($id_card);
			$name=trim($name);
			$surname=trim($surname);
			$str_where='';
			if($mobile_no!=''){
				$str_where.="AND b.mobile_no='$mobile_no'  ";
			}
			if($id_card!=''){
				$str_where.="AND b.id_card='$id_card'  ";
			}
			if($name!=''){
				$str_where.="AND b.name LIKE '%$name%'  ";
			}
			if($surname!=''){
				$str_where.="AND b.name LIKE '%$surname%'  ";
			}
			$sql_member="SELECT 
									a.customer_id,a.member_no,a.apply_date,a.special_day,a.cardtype_id,a.apply_promo,a.expire_date,a.status,
									b.name,b.surname,b.birthday,b.home,b.h_address,b.h_village_id,b.h_village,b.h_soi,b.h_road,b.h_district,b.h_amphur,
									b.h_province,b.h_postcode,b.mobile_no
								FROM
									crm_card AS a LEFT JOIN crm_profile AS b
									ON(a.customer_id=b.customer_id)
								WHERE
									a.apply_promo NOT IN('OPID300') 
									$str_where";	
			$row_member=$this->db->fetchAll($sql_member);
			$i=0;
			foreach($row_member as $data){				
				$row_member[$i]['amt']='';
				$row_member[$i]['net']='';
				$row_member[$i]['link_status']='OFFLINE';
				$i++;
			}
			return $row_member;
		}//func
		
		public function getMemberInfo2($mobile_no,$id_card,$name,$surname){
			/**
			 * @desc search data for bill 05
			 * @param String $mobile_no : field token by member_no
			 * @param String $id_card
			 * @param $name
			 * @param $surname
			 * @return multi set of String json
			 */
//			if($mobile_no=='0898185276'){
//				$o[0]['expire_date']='2014-04-30';
//				$o[0]['apply_date']='2012-08-18';
//				$o[0]['birthday']='1979-11-04';
//				$o[0]['member_no']='1120116229320';
//				$o[0]['id_card']='3150600407067';
//				$o[0]['mobile']='0898185276';
//				$o[0]['name']='วันเพ็ญ';
//				$o[0]['surname']='มานพ';
//				$o[0]['link_status']='ONLINE';
//				return $o;
//				exit();
//			}
			$chk_link=@mysql_connect('10.100.53.2','master','master');
			if(!$chk_link){
				$o=$this->getMemberOffLineByKeyWord($mobile_no,$id_card,$name,$surname);
				return $o;
			}else{				
				$ws = "http://10.100.53.2/wservice/webservices/services/member_data.php?";
				$type = "json"; //Only Support JSON 
				$shop=$this->m_branch_id;
				$act = "search_lost";
				$src = $ws."callback=jsonpCallback&mobile_no=".$mobile_no.
											"&id_card=".$id_card.
											"&name=".$name.
											"&surname=".$surname.
											"&brand=op&dtype=".$type."&shop=".$shop."&act=".$act."&_=1334128422190";
				$row_member=array();	
				$o=@file_get_contents($src,0);
				if ($o === FALSE || !$o){				
					//******************* OFFLINE PROCESS ****************
					$o=$this->getMemberOffLineByKeyWord($mobile_no,$id_card,$name,$surname);
					return $o;	
					//******************* OFFLINE PROCESS ****************
				}else{
					//******************* ONLINE PROCESS ****************
					$o = str_replace("jsonpCallback(","",$o);
					$o = str_replace(")","",$o);
					$o = json_decode($o ,true);						
					$i=0;					
					foreach($o as $data){
						$o[$i]['link_status']='ONLINE';
						$i++;
					}					
					return $o;//multi set of json
					//******************* ONLINE PROCESS ****************
				}
			}
			
		}//func getMemberInfo2
		
		public function getOfflineProfile($member_no){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");					
					try{
						$sql_member="SELECT 
												a.customer_id,a.member_no,a.apply_date,a.special_day,a.cardtype_id,a.apply_promo,a.expire_date,a.status,
												b.name,b.surname,b.birthday,b.home,b.h_address,b.h_village_id,b.h_village,b.h_soi,b.h_road,b.h_district,b.h_amphur,
												b.h_province,b.h_postcode,a.action_id
											FROM
												crm_card AS a LEFT JOIN crm_profile AS b
												ON(a.customer_id=b.customer_id)
											WHERE
												a.member_no='$member_no'";	
						$row_member=$this->db->fetchAll($sql_member);						
						if(count($row_member)>0){
							$row_member[0]['cardtype_id']=6;
							//find card_type
							$stmt_cardtype=$this->db->select()
													->from('crm_card_type','remark')
													->where('cardtype_id=?',$row_member[0]['cardtype_id']);
							$row_cardtype=$stmt_cardtype->query()->fetchAll();				
							$remark="";
							if(count($row_cardtype)>0){
								$row_member[0]['remark']=$row_cardtype[0]['remark'];
							}
							//find discount
							$arr_special_day=$this->getPromotionDay();
							$arr_spday=$this->getComSpecialDay($member_no);		
							$special_day=@$arr_spday[0]['special_day'];		
							//$special_day=$row_member[0]['special_day'];	//from crm_card
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
							$sql_spday="SELECT remark 
													FROM com_special_day
														WHERE
															 corporation_id='$this->m_corporation_id' AND 
														 	 company_id='$this->m_company_id' AND
														 	 special_day='$special_day'";	
							$sp_remark=$this->db->fetchCol($sql_spday);
							if(count($sp_remark)>0){
								$row_member[0]['special_day']=$sp_remark[0];
							}						
							$is_field="";
							if($arr_special_day[0]==$special_day){
								$is_field="special_percent";
							}else{
								$is_field="normal_percent";
							}							
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_percent_discount");
							$sql_pdiscount="SELECT $is_field
												FROM com_percent_discount 
													WHERE 
														  corporation_id='$this->m_corporation_id' AND 
														  company_id='$this->m_company_id' AND 
														  cardtype_id='".$row_member[0]['cardtype_id']."' AND
														  '$this->doc_date' BETWEEN start_date AND end_date";	
							$row_pdiscount=$this->db->fetchCol($sql_pdiscount);		
							$percent_discount=0.0;								
							if(count($row_pdiscount)>0){
								$row_member[0]['percent_discount']=$row_pdiscount[0];
							}
							
// 							if($this->doc_date=='2015-12-29' && ($special_day=="31" || $special_day=="32" || $special_day=="33" || $special_day=="34")){
// 								$row_member[0]['percent_discount']=15;
// 							}
// 							if($this->doc_date=='2015-12-29' && ($special_day=="51" || $special_day=="52" || $special_day=="53" || $special_day=="54")){
// 								$row_member[0]['percent_discount']=15;
// 							}
							
							$row_member[0]['mp_point_sum']=$this->getMemberPointLocal($member_no);//local only
							//com_application_head แสดงชุดสมัครของสมาชิก
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
							$sql_promo="SELECT description 
											  FROM com_application_head
											  WHERE
													corporation_id='$this->m_corporation_id' AND 
													company_id='$this->m_company_id' AND 
													'$this->doc_date' BETWEEN start_date AND end_date AND
													application_id='".$row_member[0]['apply_promo']."'";
							$row_promo=$this->db->fetchCol($sql_promo);
							if(count($row_promo)>0){
								$row_member[0]['apply_promo_detail']=$row_promo[0];
							}else{
								$row_member[0]['apply_promo_detail']='';
							}														
							//*WR28032016
							$expire_date=$row_member[0]['expire_date'];							
							//$timStmpExpire = strtotime($expire_date);
							//$timStmpDocDate = strtotime($this->doc_date);							
							$timStmpExpire = $expire_date;
							$timStmpDocDate = $this->doc_date;							
							if($timStmpExpire<$timStmpDocDate){
								$row_member[0]['expire_status']='Y';
							}else{
								$row_member[0]['expire_status']='N';
							}									
							$row_member[0]['exist_status']='YES';							
					}else{
						$row_member[0]['birthday']='';
						$row_member[0]['apply_date']='';
						$row_member[0]['cust_day']='';
						$row_member[0]['exist_status']='NO';
					}				
					$row_member[0]['link_status']='OFFLINE';
					////////////////////////////////////////// init trn_tdiary2_sl_val ///////////////////////////////////////////////////////////////
					if($row_member[0]['action_id']!=''){
						$arr_cardinfo=explode('#',$row_member[0]['action_id']);
						$row_member[0]['card_level']=$arr_cardinfo[0];
						$row_member[0]['ops']=$arr_cardinfo[1];
						$card_info=$row_member[0]['action_id'];
						$this->addCardInfoToValTemp($member_no,$card_info);
					}else{
						$row_member[0]['card_level']='';
						$row_member[0]['ops']='';
					}
					//////////////////////////////////////// init trn_tdiary2_sl_val ////////////////////////////////////////////////////////////////////
					return $row_member;						
				}catch(Zend_Db_Exception $e){
					echo $e->getMessage();
					return '';
				}
		}//func
		
		public function getSpDayLocal($member_no){
			/**
			 * @desc
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
			$sql_spday="SELECT special_day 
								FROM crm_card
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									member_no='$member_no'	";
			$sp_day=$this->db->fetchAll($sql_spday);
			return $sp_day;
		}//func
		
		public function getOps0Problem($member_no){
			/**
			 * @desc
			 */
			$this->db=@SSUP_Controller_Plugin_Db::checkDbOnline("view","service_pos_op");						
			$sql_spd="SELECT member_no, apply_date, expire_date, ops FROM `member_history` WHERE member_no = '$member_no'";
			$row_spd=$this->db->fetchAll($sql_spd);
			return $row_spd;
		}//func
		
		function getCoPromo($doc_no){
			/***
			 * @desc modify 11062013
			 * @return $co_promo
			 */
			unset($this->db);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_copromo="SELECT a.doc_no, a.promo_code, b.promo_pos
									FROM trn_diary2 AS a
									LEFT JOIN promo_head AS b ON ( a.promo_code = b.promo_code )									
									WHERE 
										a.doc_no = '$doc_no' AND 
										b.web_promo = 'Y' AND 
										b.promo_pos = 'O'";
			$rows_copromo=$this->db->fetchAll($sql_copromo);
			if(count($rows_copromo)>1){
				$co_promo=$rows_copromo[0]['promo_code'];
			}else{
				$sql_copromo2="SELECT a.doc_no, a.promo_code, b.promo_code
										FROM trn_diary2 AS a
										LEFT JOIN promo_other AS b ON ( a.promo_code = b.promo_code )
										WHERE a.doc_no = '$doc_no'
										GROUP BY a.doc_no";
				$rows_copromo2=$this->db->fetchAll($sql_copromo2);
				if(count($rows_copromo2)>0){
					$co_promo=$rows_copromo2[0]['promo_code'];
				}else{
					$co_promo='';
				}
			}
			return $co_promo;
		}//func
		
		function up2rePriv(){
			/**
			 * @desc modify 11062013
			 * @return
			 */			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_d1="SELECT doc_no,doc_date,doc_tp,flag,company_id,branch_id,member_id,
										refer_member_id,quantity,amount,net_amt,paid,saleman_id,reg_time,status_no
						FROM trn_diary1 WHERE doc_date='$this->doc_date' AND doc_tp IN('SL','VT','DN') AND flag='C'";
			$row_d1=$this->db->fetchAll($sql_d1);			
			//head
			$arr_doc_cancel=array();
			$j=0;
			foreach($row_d1 as $data1){
				($data1['status_no']=='01')?$new_member=1:$new_member=0;
				($data1['status_no']=='02')?$first_sale=1:$first_sale=0;
				$doc_no=$data1['doc_no'];
				if($data1['flag']=='C'){
					$arr_doc_cancel[$j]=$doc_no;
					$j++;
				}
			}//foreach			
			if(!empty($arr_doc_cancel)){		
				$this->setFlagCancel2Repriv($arr_doc_cancel);
			}
		}//func
		
		function up2cluster(){
			/**
			 * @desc modify 11062013
			 * @return
			 */			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_d1="SELECT doc_no,doc_date,doc_tp,flag,company_id,branch_id,member_id,
										refer_member_id,quantity,amount,net_amt,paid,saleman_id,reg_time,status_no
						FROM trn_diary1 WHERE doc_date='$this->doc_date' AND doc_tp IN('SL','VT','DN')";
			$row_d1=$this->db->fetchAll($sql_d1);			
			//head
			$arr_docno=array();
			$i=0;
			foreach($row_d1 as $data1){
				($data1['status_no']=='01')?$new_member=1:$new_member=0;
				($data1['status_no']=='02')?$first_sale=1:$first_sale=0;
				$doc_no=$data1['doc_no'];
				$arr_docno[$i]=$doc_no;				
				$co_promo=self::getCoPromo($doc_no);
				unset($this->db);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("pdy","pdiary1");
				$sql_chk="SELECT COUNT(*) FROM pdiary1 WHERE doc_no='$doc_no'";
				$n_chk=$this->db->fetchOne($sql_chk);
				if($n_chk==0){
					$sql_p1="INSERT INTO pdiary1(`doc_no`, `doc_dt`, `doc_tp`, `flag`, `brand`, `shop`, 
																`member`, `co_promo`, `ref_no`, `quantity`, `amount`, 
																`net_amt`, `paid`, `emp_id`, `time`,
																 `new_member`, `first_sale`,
																`status`)							
								VALUES('$doc_no','$data1[doc_date]','$data1[doc_tp]','$data1[flag]','$data1[company_id]',$data1[branch_id],
																'$data1[member_id]','$co_promo','$data1[refer_member_id]','$data1[quantity]','$data1[amount]', 
																'$data1[net_amt]','$data1[paid]', '$data1[saleman_id]','$data1[reg_time]',
																'$new_member','$first_sale',
																'$data1[status_no]')";
					$this->db->query($sql_p1);
					$i++;
				}
			}//foreach
			
			//detail
			foreach($arr_docno as $doc_no){
				unset($this->db);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
				$sql_d2="SELECT company_id,branch_id,doc_no,doc_date,doc_tp,flag,seq,promo_code,product_id,price,quantity,amount,net_amt,status_no
								FROM trn_diary2 WHERE doc_no='$doc_no'";
				$row_d2=$this->db->fetchAll($sql_d2);
				unset($this->db);
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("pdy","pdiary2");
				foreach($row_d2 as $data2){
					$sql_p2="INSERT INTO pdiary2(`doc_no`, `doc_dt`, `doc_tp`, `flag`, `brand`, `shop`, `seq`, `promo_code`, 
																`product`, `price`, `quantity`, `amount`, 
																 `net_amt`,
																 `status`)
								VALUES('$doc_no','$data2[doc_date]','$data2[doc_tp]','$data2[flag]','$data2[company_id]','$data2[branch_id]',$data2[seq],'$data2[promo_code]', 
																'$data2[product_id]', '$data2[price]','$data2[quantity]','$data2[amount]',
																'$data2[net_amt]',
																'$data2[status_no]')";
					$this->db->query($sql_p2);
				}
			}//foreach	
		}//func
		
		function setFlagCancel2Repriv($arr_doc_cancel){
			/**
			 * @desc แก้ปัญหายกเลิกเอกสารแล้วไม่คืนสิทธิ
			 * @var modify : 2013-10-03
			 * @return null
			 */
			$_host_crm='10.100.53.2';
			$_user_crm='master';
			$_pwd_crm='master';
			$_db_crm="service_pos_op";
			$link_crm=mysql_pconnect($_host_crm,$_user_crm,$_pwd_crm);
			if($link_crm){
				mysql_select_db($_db_crm) or die(mysql_error);
				for($i=0;$i<count($arr_doc_cancel);$i++){
					$doc_no=$arr_doc_cancel[$i];
					$sql_upd="UPDATE `promo_play_history` SET flag='C' WHERE doc_no='$doc_no'";
					mysql_query($sql_upd);
				}
				mysql_close($link_crm);
			}else{
				echo "cann't connect to it.";
			}
		}//func
		
		public function getMemberInfo($status_no,$member_no){
			/**
			 * @version Joke
			 * @name getMemberInfo
			 * @param String $status_no : type of document
			 * @param String $member_no : id card of member
			 * @desc 21/06/2013			
			 * @return String Json
			 */			
			//Joke 19032013
			$objCal=new Model_Calpromotion();
			$json_meminfo=$objCal->read_profile($member_no); 
			if($json_meminfo=='false' || $json_meminfo=='' || $json_meminfo=='[]'){				
				//******************* OFFLINE PROCESS ****************				
				$row_member=$this->getOfflineProfile($member_no);
				if($row_member!=''){
					if($row_member[0]['exist_status']=='YES'){
						$objUtils=new Model_Utils();
						$json=$objUtils->ArrayToJson('member',$row_member[0],'yes');				
						return $json;
					}else{
						return '2';
					}
				}else{
					return '2';
				}
				//********************OFFLINE PROCESS ****************
			}else{			
				$array_meminfo= json_decode($json_meminfo,true);
				//var_dump($array_meminfo);
				$o=array();
				foreach($array_meminfo as $key=>$val){
					$o[0][$key]=$val;
				}//foreach
				//ONLINE DATA RETRUN					
				if(!empty($o)){
					//case conect jinet and found member
					//*WR20052015
					$url_gcard="http://10.100.53.2/ims/joke/app_service_op/api_member/member_change_opt.php?member_no=".$member_no;
					$str_gcard=@file_get_contents($url_gcard,0);	
					if($str_gcard!='')
						$str_gcard=iconv('TIS-620','UTF-8',$str_gcard);
						
					$row_member=$o;	
					//10062013 valid json					
					$row_member[0]['prefix']=self::escapeJsonString($row_member[0]['prefix']);
					$row_member[0]['name']=self::escapeJsonString($row_member[0]['name']);
					$row_member[0]['surname']=self::escapeJsonString($row_member[0]['surname']);
					$row_member[0]['address']=self::escapeJsonString($row_member[0]['address']);
					$row_member[0]['road']=self::escapeJsonString($row_member[0]['road']);
					$row_member[0]['province_name']=self::escapeJsonString($row_member[0]['province_name']);
					$row_member[0]['district']=self::escapeJsonString($row_member[0]['district']);
					$row_member[0]['sub_district']=self::escapeJsonString($row_member[0]['sub_district']);										
					//*WR28032016
					$s_DateExpire = substr($row_member[0]['expire_date'],0,10);					
					//$timStmpExpire = strtotime($s_DateExpire);
					//$timStmpDocDate = strtotime($this->doc_date);					
					$timStmpExpire = $s_DateExpire;
					$timStmpDocDate = $this->doc_date;					
					if($timStmpExpire<$timStmpDocDate){
						//card is expire date
						if($status_no=='04'){							
							$news_expire=date("Y-m-d",strtotime($row_member[0]['expire_date']." +2 month"));							
							$timStmpNewExpire = strtotime($news_expire);
							if($timStmpNewExpire<$timStmpDocDate){
								//return '0';
								$row_member[0]['expire_status']='Y';
							}
						}else{
							//return '0';
							$row_member[0]['expire_status']='Y';
						}					
					}	
				//find card_type
				if($row_member[0]['vip']=='0'){
					//case normal member	
					$row_member[0]['cardtype_id']=6;//fixed cardtype_id='6'				
					$stmt_cardtype=$this->db->select()
											->from('crm_card_type','remark')
											->where('cardtype_id=?',$row_member[0]['cardtype_id']);
					$row_cardtype=$stmt_cardtype->query()->fetchAll();				
					$remark="";
					if(count($row_cardtype)>0){
						$row_member[0]['remark']=$row_cardtype[0]['remark'];
					}					
					//find discount					
					$arr_special_day=$this->getPromotionDay();					
					$arr_spday=$this->getComSpecialDay($member_no);//check on local is VIP or not
					$special_day=$arr_spday[0]['special_day'];
					if($special_day!='00'){
						//for non VIP 	
						$cust_day=$row_member[0]['cust_day'];
						$this->setOpsDayMember($cust_day);
						//update for joke data 02052013						
						switch($cust_day){
							case 'OPS0':$cust_day="TH0";break;
							case 'OPS1':$cust_day="TH1";break;
							case 'OPS2':$cust_day="TH2";break;
							case 'OPS3':$cust_day="TH3";break;
							case 'OPS4':$cust_day="TH4";break;
							case 'OPT0':$cust_day="TU0";break;
							case 'OPT1':$cust_day="TU1";break;
							case 'OPT2':$cust_day="TU2";break;
							case 'OPT3':$cust_day="TU3";break;
							case 'OPT4':$cust_day="TU4";break;
							default: $cust_day="";break;
						}
						$row_member[0]['cust_day']=$cust_day;
						//update for joke data 02052013
						unset($arr_spday);
						$arr_spday=$this->getComSpecialDayOnline($cust_day);//*WR11102012		
						$special_day=$arr_spday[0]['special_day'];							
					}					
					//*WR20120905
					if($special_day=='50'){
						//if lost card on local
						$special_day=$this->getSpdayLocal($member_no);
					}
					
					$is_field="";
					if($arr_special_day[0]==$special_day){
						$is_field="special_percent";
					}else{
						$is_field="normal_percent";
					}
					$row_member[0]['special_day']=$arr_spday[0]['remark'];					
					$sql_pdiscount="SELECT $is_field
									FROM com_percent_discount 
										WHERE corporation_id='$this->m_corporation_id' AND 
											  company_id='$this->m_company_id' AND 
											  cardtype_id='".$row_member[0]['cardtype_id']."' AND
											  '$this->doc_date' BETWEEN start_date AND end_date";	 		
					$row_pdiscount=$this->db->fetchCol($sql_pdiscount);		
					$percent_discount=0.0;								
					if(count($row_pdiscount)>0){
						$row_member[0]['percent_discount']=$row_pdiscount[0];
					}					
					//*WR for ops tuesday 					
// 					if($this->doc_date=='2015-12-29' && ($cust_day=="TU1" || $cust_day=="TU2" || $cust_day=="TU3" || $cust_day=="TU4")){
// 						$row_member[0]['percent_discount']=15;
// 					}
// 					if($this->doc_date=='2015-12-29' && ($cust_day=="TH1" || $cust_day=="TH2" || $cust_day=="TH3" || $cust_day=="TH4")){
// 						$row_member[0]['percent_discount']=15;
// 					}
					
					//WR27122013///////////////////////////POINT2014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
					/*[0]=คะแนนที่โอนมาจากบัตรเก่า
					  [1]=วันที่หมดอายุของคะแนนบัตรเก่า
					  [2]=คะแนนบัตรปัจจุบัน
					  [3]=คะแนนคงเหลือทั้งสิ้น */
					$objCalX=new Model_Calpromotion();		
					$curr_point=$objCalX->read_point($member_no);
					$curr_point=intval($curr_point);
					
					$transfer_point=0;					
					$balance_point=$curr_point;
					$expire_transfer_point='';
					
// 					$transfer_point=intval($arr_point[0]);
// 					$curr_point=intval($arr_point[2]);
// 					$balance_point=intval($arr_point[3]);
// 					$expire_transfer_point=$arr_point[1];
				
					$row_member[0]['transfer_point']=$transfer_point;
					$row_member[0]['curr_point']=$curr_point;
					$row_member[0]['balance_point']=$balance_point;
					$row_member[0]['expire_transfer_point']=$expire_transfer_point;			
				
					$row_member[0]['mp_point_sum_1']=$balance_point;	
					$row_member[0]['mp_point_sum']=$balance_point;		
					//WR27122013///////////////////////////POINT2014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
					
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
					/////////////////////////////////////////////////////////////////////////////////// 2Display /////////////////////////////////////////////////////////////////////////////////////
					$num_screen=$this->getNumScreen();
					if($num_screen>1){
						//*WR270320127 append type card
						$display_special_day=$cust_day."#".$row_member[0]['card_level'];
						$trn_name=$row_member[0]['name'];
						$trn_surname=$row_member[0]['surname'];
						$str_amt=$objCalX->sale_all($member_no);
						$arr_amt=explode('#',$str_amt);
						$buy_amt=$arr_amt[0];
						$buy_net=$arr_amt[1];
						//*WR02122014 member transaction
						$sql_del_transaction="DELETE FROM trn_member_today 
													WHERE 
															`corporation_id`='$this->m_corporation_id' AND 
															`company_id`='$this->m_company_id' AND
															`branch_id`='$this->m_branch_id' AND
															`computer_no`='$this->m_computer_no' AND
	 														`computer_ip`='$this->m_com_ip'";
						$res_del_transaction=$this->db->query($sql_del_transaction);
						$apply_date=$row_member[0]['apply_date'];
						$expire_date=$row_member[0]['expire_date'];
						$mobile_no=$row_member[0]['mobile_no'];
						$id_card=$row_member[0]['id_card'];
						$s_birthday=$row_member[0]['birthday'];
						$age_card=$row_member[0]['age_card'];
						$sql_add_transaction="INSERT INTO `trn_member_today` 
															SET
																`corporation_id`='$this->m_corporation_id',
																`company_id`='$this->m_company_id',
															    `branch_id`='$this->m_branch_id',
															    `computer_no`='$this->m_computer_no',
	 															`computer_ip`='$this->m_com_ip',
															    `doc_date`='$this->doc_date',
															    `doc_time`=CURTIME(),
															    `member_id`='$member_no',														    
															    `birthday`='$s_birthday',														    
															    `name`='$trn_name',
															    `surname`='$trn_surname',
															    `total_point`='$balance_point',
															    `special_day`='$display_special_day',
															    `apply_date`='$apply_date',
															    `expire_date`='$expire_date',
															    `mobile_no`='$mobile_no',
															    `idcard`='$id_card',
															    `point_expire`='$expire_transfer_point',
															    `trn_status`='N',
															    `trn_queue`='1',
															    `transfer_point`='$transfer_point',
															    `expire_transfer_point`='$expire_transfer_point',		
															    `buy_amt`='$buy_amt',
															    `buy_net`='$buy_net',
															    `age_card`='$age_card',
															    `reg_date`=CURDATE(),
															    `reg_time`=CURTIME(),
															    `reg_user`='ADMIN'";
						$res_add_transaction=$this->db->query($sql_add_transaction);
						//*WR27012015 add 2 trn2display
						//$objM=new Model_Member();
						//$objM->trn2Display($member_no);
					}//end if
					/////////////////////////////////////////////////////////////////////////////////// 2Display /////////////////////////////////////////////////////////////////////////////////////	
					/////////////////////////////////////////////////////////////////////////////////// init table temp //////////////////////////////////////////////////////////////////////////////
					$sql_delval="DELETE FROM `trn_tdiary2_sl_val` WHERE `computer_ip`='$this->m_com_ip'";
					$this->db->query($sql_delval);
					$card_info=$row_member[0]['card_level']."#".$row_member[0]['ops'];
					$sql_addval="INSERT INTO
					`trn_tdiary2_sl_val`
					SET
					`corporation_id`='$this->m_corporation_id',
					`company_id`='$this->m_company_id',
					`branch_id`='$this->m_branch_id',
					`computer_no`='$this->m_computer_no',
					`computer_ip`='$this->m_com_ip',
					`member_no`='$member_no',
					`doc_date`='$this->doc_date',
					`doc_time`=CURTIME(),
					`cn_remark`='$card_info'";
					$this->db->query($sql_addval);
					/////////////////////////////////////////////////////////////////////////////////// init table temp /////////////////////////////////////////////////////////////////////////////////////
					//check cencel doc
					$sql_cdoc="SELECT COUNT(*) FROM trn_diary1 WHERE doc_date='$this->doc_date' AND member_id='$member_no' AND status_no='01' AND flag='C'";
					$n_cdoc=$this->db->fetchOne($sql_cdoc);
					if($n_cdoc<1){			
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
							$sql_crm_card="SELECT 
															member_no,customer_id
													FROM
														crm_card
													WHERE
														corporation_id='$this->m_corporation_id' AND
														company_id='$this->m_company_id' AND
														member_no='$member_no'	";
							$row_crm=$this->db->fetchAll($sql_crm_card);					
							if(count($row_crm)>0){
									$sql_upd_crm_card="UPDATE crm_card
																	SET
																		customer_id='".$row_member[0]['customer_id']."',	
																		action_id='$card_info',
																		upd_date=CURDATE(),
																		upd_time=CURTIME(),
																		upd_user='$this->employee_id'
																	WHERE
																		corporation_id='$this->m_corporation_id' AND
																		company_id='$this->m_company_id' AND
																		member_no='$member_no'	";
									$this->db->query($sql_upd_crm_card);
							
									$sql_chkprofile="SELECT COUNT(*) FROM crm_profile WHERE customer_id='".$row_member[0]['customer_id']."'";
									$n_chkprofile=$this->db->fetchOne($sql_chkprofile);
									if($n_chkprofile>0){
										$sql_upd_crm_profile="UPDATE crm_profile
																		SET						
																			customer_id='".$row_member[0]['customer_id']."',									
																			title='".$row_member[0]['title']."',
																			name='".$row_member[0]['name']."',
																			surname='".$row_member[0]['surname']."',
																			birthday='".$row_member[0]['birthday']."',
																			mobile_no='".$row_member[0]['mobile_no']."',
																			upd_date=CURDATE(),
																			upd_time=CURTIME(),
																			upd_user='$this->employee_id'
																		WHERE
																			corporation_id='$this->m_corporation_id' AND
																			company_id='$this->m_company_id' AND
																			customer_id='".$row_crm[0]['customer_id']."'";
										$this->db->query($sql_upd_crm_profile);		
									}else{
										$sql_crm_profile="INSERT INTO crm_profile
															SET
																corporation_id='$this->m_corporation_id',
																company_id='$this->m_company_id',
																customer_id='".$row_member[0]['customer_id']."',	
																name='".$row_member[0]['name']."',
																surname='".$row_member[0]['surname']."',
																birthday='".$row_member[0]['birthday']."'";
										$res_qry_profile=$this->db->query($sql_crm_profile);
									}				
								}else{
									//insert local							
									$crm_customer_id=$row_member[0]['customer_id'];	
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
									$sql_crm_card="INSERT INTO crm_card 
															SET
																corporation_id='$this->m_corporation_id',
																company_id='$this->m_company_id',
																brand_id='".$row_member[0]['brand_id']."',
																cardtype_id='".$row_member[0]['cardtype_id']."',
																customer_id='$crm_customer_id',
																member_no='$member_no',
																apply_date='".$row_member[0]['apply_date']."',
																expire_date='".$row_member[0]['expire_date']."',
																member_ref='',
																apply_shop='$this->m_branch_id',
																apply_promo='".$row_member[0]['appli_code']."',
																special_day='$special_day',
																action_id='$card_info',
																reg_date=CURDATE(),
																reg_time=CURTIME(),
																reg_user='$this->employee_id'";
									$res_qry_card=$this->db->query($sql_crm_card);
									$sql_crm_profile="INSERT INTO crm_profile
															SET
																corporation_id='$this->m_corporation_id',
																company_id='$this->m_company_id',
																customer_id='$crm_customer_id',
																name='".$row_member[0]['name']."',
																surname='".$row_member[0]['surname']."',
																birthday='".$row_member[0]['birthday']."'";
									$res_qry_profile=$this->db->query($sql_crm_profile);
						
							}//if count
						}//if n_cdoc<1
						//check cencel doc
				
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_application_head");
						//com_application_head แสดงชุดสมัครของสมาชิก
						$sql_promo="SELECT description 
											  FROM com_application_head
											  WHERE
													corporation_id='$this->m_corporation_id' AND 
													company_id='$this->m_company_id' AND 
													'$this->doc_date' BETWEEN start_date AND end_date AND
													application_id='".$row_member[0]['application_id']."'";
						$row_promo=$this->db->fetchCol($sql_promo);
						if(count($row_promo)>0){
							$row_member[0]['apply_promo_detail']=$row_promo[0];
						}else{
							$row_member[0]['apply_promo_detail']='';
						}
						
						//*WR20052015 for support member get member
						$row_member[0]['apply_promo_detail']=$str_gcard." ".$row_member[0]['apply_promo_detail'];
					//case normal member
				}else{
					//case vip member
					$limited_type=$row_member[0]['limited_type'];//ประเภทยอดเงิน Net or Goss
					$sum_amt=$row_member[0]['sum_amt'];//ยอดเงินที่สามารถใช้ได้ ณ ตอนนี้
					$objMember=new Model_Member();
					$diary1_amount=$objMember->chkBalVipToday($member_no,$limited_type);//ยอดเงินที่ Diary1
					if(intval($diary1_amount)!=0){
						$row_member[0]['diary1_sum_amt']=$diary1_amount;
					}else{
						$row_member[0]['diary1_sum_amt']="0.00";
					}
				}//if
					$row_member[0]['link_status']='ONLINE';	
					$objUtils=new Model_Utils();
					$json=$objUtils->ArrayToJson('member',$row_member[0],'yes');		
					return $json;					
				}else{
					####################### connect to jinet and not found member สมาชิกสมัครใหม่ยังไม่ได้อัพไป jinet ####################
					$row_member=$this->getOfflineProfile($member_no);					
					if($row_member!=''){
						if($row_member[0]['exist_status']=='YES'){
							$o[0]['link_status']='ONLINE';
							$objUtils=new Model_Utils();
							$json=$objUtils->ArrayToJson('member',$row_member[0],'yes');				
							return $json;
						}else{
							return '2';
						}
					}else{
						return '2';
					}
					############################### connect to jinet and not found member #################################
				}//end not found on jinet					
			}			
		}//func
		
		public function getPercentDiscountForOffline() {
			/**
			 * @desc
			 * @return
			 */
		}//func
		
		public function removeFormGrid($tbl_target,$str_items=''){
			/**
			 * @name removeFormGrid
			 * @desc remove row data form flexigrid by fixed table
			 * @param $tbl_target is table target to delete row
			 * @param $str_items is string of id item to delete
			 * @return null
			 */
			if($str_items!=''){
				try{
					$arr_data=explode(',',$str_items);
					foreach($arr_data as $id_delete){
						if($id_delete=='') continue;
						$sql_del="DELETE FROM `$tbl_target` WHERE id='$id_delete'";
						$stmt_del=$this->db->exec($sql_del);
						
					}
				}catch(Zend_Db_Exception $e){
					echo $e->getMessage();
				}
			}
		}//func
		
		public function getProductLock($product_id,$status_no=''){
			/**
			 * @desc get status product lock from table com_product_lock
			 * @desc modify : 03092013
			 * @return Boolean True or False
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_lock");
			if($status_no!=''){
				$whr=" AND status_no='$status_no' ";
			}		
			$sql_lock="SELECT COUNT(*)
							FROM `com_product_lock`
							WHERE corporation_id = '$this->m_corporation_id'
									AND company_id = '$this->m_company_id'
									AND product_id='$product_id'	$whr								
									AND '$this->doc_date' BETWEEN start_date 	AND end_date";
			$crow=$this->db->fetchOne($sql_lock);
			if($crow>0){
				return true;
			}else{
				return false;
			}
		}//func
		
		public function browsProduct($product_id=""){
			/**
			 * @name browsProduct
			 * @desc
			 * @param String $id
			 * @return array of product
			 * @lastmodify 14072011
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			$rows=array();
			if($product_id=="") return $rows;
			$arr_date=explode("-",$this->doc_date);
			$this->year=$arr_date[0];
			$this->month=intVal($arr_date[1]);
			
		if($product_id=="all"){
				$sql_product="SELECT a.product_id as product_id,
										a.name_product as name_product,
										a.unit as unit,
										a.price as price,
										a.tax_type as tax_type,
										b.product_status  as product_status,
										b.begin,
										b.onhand
									FROM 
										com_product_master AS a LEFT JOIN  com_stock_master AS b
										ON(a.product_id=b.product_id)
									WHERE
										a.corporation_id='$this->m_corporation_id' AND
										a.company_id='$this->m_company_id' AND
										b.year='$this->year' AND
										b.month='$this->month' AND
										a.product_id  IS NOT NULL 
									ORDER by a.name_product";		
			}else if($product_id!=""){
				$sql_product="SELECT a.product_id as product_id,
										a.name_product as name_product,
										a.unit as unit,
										a.price as price,
										a.tax_type as tax_type,
										b.product_status  as product_status,
										b.begin,
										b.onhand,
										b.allocate
									FROM 
										com_product_master AS a LEFT JOIN  com_stock_master AS b
										ON(b.product_id=a.product_id)
									WHERE
										a.corporation_id='$this->m_corporation_id' AND
										a.company_id='$this->m_company_id' AND										
										a.product_id='$product_id' AND 
										b.year='$this->year' AND
										b.month='$this->month'
									GROUP BY b.product_id
									ORDER by a.name_product";	
				
			}		
			$rows=$this->db->fetchAll($sql_product);
			return $rows;
		}//func
		
		public function updStockMaster($product_id,$quantity,$stock_st){
			/**
			 * @name
			 * @desc
			 * @param
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");			
			$this->arr_date=explode("-",$this->doc_date);				   
		    $this->month=intval($this->arr_date[1]);
		    $this->year=intval($this->arr_date[0]);
	   		$sql_updstock="UPDATE 
			   						 com_stock_master 
			   						SET
			   							onhand=onhand+($quantity*$stock_st),
			   							upd_date=CURDATE(),
			   							upd_time=CURTIME(),
			   							upd_user='$this->employee_id'
			   						WHERE 
			   							corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND 	
										branch_id='$this->m_branch_id' AND 	
										product_id='$product_id' AND 	
										month='$this->month' AND
										year='$this->year'";
			$stmt_updstockmaster=$this->db->query($sql_updstock); 
			
		}//func
		
		public function decreaseStock($product_id,$quantity){
			/**
			 * @name decreaseStock
			 * @desc decrease quantity item in Stock
			 * @param
			 * @return 
			 * @lastmodify 28032012
			 */							
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");		
			
			$sql_st="SELECT stock_st 
							FROM
								com_doc_no
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND 	
								branch_id='$this->m_branch_id' AND 
								doc_tp='$this->doc_tp'";			
			 $row_st=$this->db->fetchCol($sql_st);
			 $this->stock_st=$row_st[0];		
			
			$this->arr_date=explode("-",$this->doc_date);				   
		    $this->month=intval($this->arr_date[1]);
		    $this->year=intval($this->arr_date[0]);
		    $sql_stockmaster="SELECT 
				   						onhand 
				   					 FROM 
				   					 	com_stock_master 
									 WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND 	
										branch_id='$this->m_branch_id' AND 	
										branch_no='$this->m_branch_no' AND 	
										product_id='".$product_id."' AND 	
										month='$this->month' AND
										year='$this->year'";
		    $row_onhand=$this->db->fetchCol($sql_stockmaster);
		   if(count($row_onhand)>0){
			   		$this->onhand= $row_onhand[0]+($quantity*$this->stock_st);
			   		$sql_updstock="UPDATE 
					   						 com_stock_master 
					   						SET
					   							onhand='$this->onhand',
					   							upd_date='$this->doc_date',
					   							upd_time='$this->doc_time',
					   							upd_user='$this->employee_id'
					   						WHERE 
					   							corporation_id='$this->m_corporation_id' AND
												company_id='$this->m_company_id' AND 	
												branch_id='$this->m_branch_id' AND 	
												product_id='".$product_id."' AND 	
												month='$this->month' AND
												year='$this->year'";
			   		$stmt_updstockmaster=$this->db->query($sql_updstock); 
		   }
		}//func
		
		public function increaseStock($product_id,$quantity){
			/**
			 * @name increase quantity item in stock
			 * @desc 
			 * @param
			 * @return 
			 * @lastmodify 28032012
			 */			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");						
			$this->arr_date=explode("-",$this->doc_date);				   
		    $this->month=intval($this->arr_date[1]);
		    $this->year=intval($this->arr_date[0]);
		    $sql_stockmaster="SELECT 
				   						onhand 
				   					 FROM 
				   					 	com_stock_master 
									 WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND 	
										branch_id='$this->m_branch_id' AND 	
										branch_no='$this->m_branch_no' AND 	
										product_id='".$product_id."' AND 	
										month='$this->month' AND
										year='$this->year'";
		   $row_onhand=$this->db->fetchCol($sql_stockmaster);
		   if(count($row_onhand)>0){
		   	   $this->onhand=$row_onhand[0]+$quantity;
		   	   $this->doc_time=date("H:i:s");
		   	   $sql_updstock="UPDATE 
			   						 	com_stock_master 
			   						SET
			   							onhand='$this->onhand',
			   							upd_date='$this->doc_date',
			   							upd_time='$this->doc_time',
			   							upd_user='$this->employee_id'
			   						WHERE 
			   							corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND 	
										branch_id='$this->m_branch_id' AND 	
										product_id='".$product_id."' AND 	
										month='$this->month' AND
										year='$this->year'";
		   	   $stmt_updstockmaster=$this->db->query($sql_updstock); 
		   }
			
		}//func
		
		public function calVat($net_amt){
			/**
			 * @desc WR10102012 การคำนวน vat จะใช้ยอด sum(net_amt) ใน temp ที่ผ่านการปัดเศษสตางค์แล้ว
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_tax");
			$sql_vat="SELECT 
								country_id,tax_type,percent_tax,start_date,end_date
							FROM 
								com_tax
							WHERE
								country_id='$this->m_country_id' AND
								tax_type='V' AND
								'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_vat=$this->db->fetchAll($sql_vat);
			$taxvat=0.00;
			if(count($row_vat)>0){
				$vat=$row_vat[0]['percent_tax'];
				if($net_amt!=''){
					$taxvat=$net_amt*$vat/(100+$vat);
				}
			}	
			return $taxvat;
		}//func	

		public function getNetVT($tbl_target=''){
			/***
			 * @desc 19072013
			 */
			if($tbl_target=='') return 0.00;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn",$tbl_target);
			$sql_l2h="SELECT 
							SUM(quantity) AS sum_quantity,	
						 	SUM(discount) AS sum_discount,
						 	SUM(member_discount1)  AS  sum_member_discount1,
						 	SUM(member_discount2)  AS sum_member_discount2,						 	
						 	SUM(co_promo_discount)  AS sum_co_promo_discount, 
						 	SUM(coupon_discount)  AS sum_coupon_discount,
						 	SUM(special_discount) AS sum_special_discount,
						 	SUM(other_discount) AS sum_other_discount,		
						 	SUM(special_discount) AS sum_special_discount,				 
						 	
						 	(SUM(discount)  +
			SUM(member_discount1)  +
			SUM(member_discount2)  +
			SUM(co_promo_discount)  +
			SUM(coupon_discount)  +			
			SUM(other_discount) +
			SUM(special_discount) ) AS sum_discount,
						 	
							SUM(amount) AS sum_amount,
							SUM(net_amt) AS sum_net_amt							
						FROM 
							`$tbl_target`
						WHERE
							`corporation_id`='$this->m_corporation_id' AND
							`company_id`='$this->m_company_id' AND
							`branch_id`='$this->m_branch_id' AND
							`computer_no`='$this->m_computer_no' AND
 							`computer_ip`='$this->m_com_ip' AND
							`doc_date`='$this->doc_date'";	
			$row_l2h=$this->db->fetchAll($sql_l2h);
			if(empty($row_l2h)) return 0.00;
			
// 			$discount=self::getSatang($row_l2h[0]['sum_discount'],'normal');
// 			$member_discount1= self::getSatang($row_l2h[0]['sum_member_discount1'],'normal');			
// 		 	$member_discount2= self::getSatang($row_l2h[0]['sum_member_discount2'],'normal');						 	
// 		 	$co_promo_discount= self::getSatang($row_l2h[0]['sum_co_promo_discount'],'normal'); 
// 		 	$coupon_discount= self::getSatang($row_l2h[0]['sum_coupon_discount'],'normal');
// 		 	$special_discount= self::getSatang($row_l2h[0]['sum_special_discount'],'normal');
// 		 	$other_discount= self::getSatang($row_l2h[0]['sum_other_discount'],'normal');				
		 	
			$discount=self::getSatang($row_l2h[0]['sum_discount'],'normal');
		 	$amount=$row_l2h[0]['sum_amount'];
		 	$net_amt=$amount-$discount;
		 	//$net_amt=$amount - ($discount+$member_discount1+$member_discount2+$co_promo_discount+$coupon_discount+$special_discount+$other_discount);
		 	//return number_format($net_amt,2,".","");
		 	return $net_amt;
		}//func
		
		public function getPoint1($net){
			/**
			 * @desc :normal point
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_point1");			
			$sql_point="SELECT
								baht,normal_point,special_point,net_price
							FROM
								com_point1
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								cardtype_id='6' AND
								net_price='Y' AND
								'$this->doc_date' BETWEEN start_date AND end_date";
			$row_point=$this->db->fetchAll($sql_point);
			$point=0;
			$net=intval($net);
			$dollars=intval($row_point[0]['baht']);
			if($net>=$dollars){
				$point=floor($net/$row_point[0]['baht']);
			}else{
				$point=0;
			}
			//*WR04042017 new point
			$normal_point=$row_point[0]['normal_point'];
			$point*=$normal_point;
			return $point;			
		}//func
		
		public function getPoint2($net){
			/**
			 * @desc :special point
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_point2");
			$net=intval($net);
			$point=0;
			if(strtoupper($this->m_corporation_id)=='SS'){
				$sql_point="SELECT
								baht,normal_point,special_point,net_price
						    FROM
						    	com_point2
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								cardtype_id='6' AND
								net_price='Y' AND
								'$this->doc_date' BETWEEN start_date AND end_date
							ORDER BY baht DESC";	
				$row_point=$this->db->fetchAll($sql_point);
				$arr_point=array();
				foreach($row_point as $data){
					$baht=$data['baht'];
					$normal_point=$data['normal_point'];
					$arr_point[$baht]=$normal_point;
				}//foreach			
			
				foreach($arr_point as $bath=>$normal_point){
					if($net>=$baht){
						$pt=floor($net/$bath);
						$point+=$pt*$normal_point;
						$net=$net%$bath;
					}else{
						break;
					}	
				}//foreach
					
			}
			return $point;
		}//func
		
		public function getComSpecialDayOnline($sp_day){
			/**
			 * 
			 * @var unknown_type
			 * @param String $sp_day
			 */
			
			$row_com=array();
			switch($sp_day){
				case '00':$desc='VIP';$sp_day_num='00';break;
				case 'TH0':$desc='พฤหัสบดีที่ 0';$sp_day_num='50';break;
				case 'TH1':$desc='พฤหัสบดีที่ 1';$sp_day_num='51';break;
				case 'TH2':$desc='พฤหัสบดีที่ 2';$sp_day_num='52';break;
				case 'TH3':$desc='พฤหัสบดีที่ 3';$sp_day_num='53';break;
				case 'TH4':$desc='พฤหัสบดีที่ 4';$sp_day_num='54';break;
				case 'TU0':$desc='อังคารที่ 0';$sp_day_num='30';break;
				case 'TU1':$desc='อังคารที่ 1';$sp_day_num='31';break;
				case 'TU2':$desc='อังคารที่ 2';$sp_day_num='32';break;
				case 'TU3':$desc='อังคารที่ 3';$sp_day_num='33';break;
				case 'TU4':$desc='อังคารที่ 4';$sp_day_num='34';break;
				default: $sp_day_num='';$desc='';				
			}
			$row_com[0]['special_day']=$sp_day_num;
			$row_com[0]['remark']=$desc;
			return $row_com;
		}//func
		
		public function getComSpecialDay($member_id){
			/**
			 * 
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
			$sql_com="SELECT special_day,remark 
						FROM com_special_day
							WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND
								cardtype_id='6' AND
								'$member_id' BETWEEN start_code AND end_code";
			$row_com=$this->db->fetchAll($sql_com);
			if(count($row_com)>0){				
				$sp_day=$row_com[0]['special_day'];
				switch($sp_day){
					case '00':$desc='VIP';break;
					case '50':$desc='พฤหัสบดีที่ 0';break;
					case '51':$desc='พฤหัสบดีที่ 1';break;
					case '52':$desc='พฤหัสบดีที่ 2';break;
					case '53':$desc='พฤหัสบดีที่ 3';break;
					case '54':$desc='พฤหัสบดีที่ 4';break;
					case '30':$desc='อังคารที่ 0';break;
					case '31':$desc='อังคารที่ 1';break;
					case '32':$desc='อังคารที่ 2';break;
					case '33':$desc='อังคารที่ 3';break;
					case '34':$desc='อังคารที่ 4';break;					
				}
				$row_com[0]['remark']=$desc;
			}else{
				$sp_day='';
				$desc='';
				//$row_com[0]['remark']='';
			}
			
			return $row_com;
		}//func
		
		public function getCnDay(){
			/**
			 * @desc
			 * @var unknown_type
			 */
			$sql_dcn="SELECT default_day 
							FROM com_pos_config 
								WHERE code_type='CN_DAY'";
			$res_dcn=$this->db->fetchOne($sql_dcn);
			return $res_dcn;
		}//func
		
		function lastday($month='',$year=''){
		   /**
		    * @desc
		    * @param String $month
		    * @param String $year
		    * @return last day of month
		    */
		   if (empty($month)) {
		      $month = date('m');
		   }
		   if (empty($year)) {
		      $year = date('Y');
		   }
		   $result = strtotime("{$year}-{$month}-01");
		   $result = strtotime('-1 second', strtotime('+1 month', $result));
		   return date('Y-m-d', $result);
		}//func
		
//		public function get_previous_month($date) {
//			/**
//			 * @desc
//			 * @param Date $date
//			 */
//			$date = str_replace("/", "-", $date); 
//			$year=date("Y",strtotime($date));
//			$month=date("n",strtotime($date)) - 1;
//			if ($month == 0) {
//				$month = 12;
//				$year = $year - 1;
//			}
//			return date("Y-m-d",mktime(0,0,0,$month,1,$year));
//		}//func
		
		function monthDif($start_date,$end_date){
			/**
			 * @desc
			 * @param
			 * @param
			 */			
			 $date_diff=strtotime($end_date)-strtotime($start_date);
			 $month_diff=floor(($date_diff)/2628000);
			 return $month_diff;
		}//func
		
		function monthsDif($start, $end)
			{
			    // Assume YYYY-mm-dd - as is common MYSQL format
			    $splitStart = explode('-', $start);
			    $splitEnd = explode('-', $end);
			
			    if (is_array($splitStart) && is_array($splitEnd)) {
			        $startYear = $splitStart[0];
			        $startMonth = $splitStart[1];
			        $endYear = $splitEnd[0];
			        $endMonth = $splitEnd[1];
			
			        $difYears = $endYear - $startYear;
			        $difMonth = $endMonth - $startMonth;
			
			        if (0 == $difYears && 0 == $difMonth) { // month and year are same
			            return 0;
			        }
			        else if (0 == $difYears && $difMonth > 0) { // same year, dif months
			            return $difMonth;
			        }
			        else if (1 == $difYears) {
			            $startToEnd = 13 - $startMonth; // months remaining in start year(13 to include final month
			            return ($startToEnd + $endMonth); // above + end month date
			        }
			        else if ($difYears > 1) {
			            $startToEnd = 13 - $startMonth; // months remaining in start year 
			            $yearsRemaing = $difYears - 2;  // minus the years of the start and the end year
			            $remainingMonths = 12 * $yearsRemaing; // tally up remaining months
			            $totalMonths = $startToEnd + $remainingMonths + $endMonth; // Monthsleft + full years in between + months of last year
			            return $totalMonths;
			        }
			    }
			    else {
			        return false;
			    }
		}//func		
		
		public function compareDate($date1,$date2,$cmp){
			/**
			 * @desc
			 * @param Date $date1 is date compare1
			 * @param Date $date2 is date compare2
			 * @return Boolean true or false
			 */
			$arrDate1 = explode("-",$date1);
			$arrDate2 = explode("-",$date2);
			$timStmp1 = mktime(0,0,0,$arrDate1[1],$arrDate1[2],$arrDate1[0]);
			$timStmp2 = mktime(0,0,0,$arrDate2[1],$arrDate2[2],$arrDate2[0]);
			$bcmp=false;
			switch($cmp){
				case '=':if($timStmp1==$timStmp2)
							$bcmp=true;
						 break;
				case '>':if($timStmp1>$timStmp2)
							$bcmp=true;
						 break;
				case '<':if($timStmp1<$timStmp2)
							$bcmp=true;
						 break;
			}
			return $bcmp;
		}//func
		
		
		
		public function countDiaryTemp(){
			/**
			 * @name countTemp
			 * @desc
			 * @param String :$tbltemp is table name
			 * @return 
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
			$n_rows=0;
			//$tbl_temp="trn_tdiary2_sl";
			$tbl_temp="N";//*WR28052014
			$sql_row = "SELECT count(*) 
							FROM `trn_promotion_tmp1` 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`branch_id`='$this->m_branch_id' AND
								`doc_date`='$this->doc_date'";		
			$crow=$this->db->fetchOne($sql_row);
			if($crow<1){
				$sql_row2 = "SELECT count(*) 
							FROM `trn_tdiary2_sl` 
							WHERE 
								`corporation_id`='$this->m_corporation_id' AND
								`company_id`='$this->m_company_id' AND
								`branch_id`='$this->m_branch_id' AND
								`computer_no` ='$this->m_computer_no' AND
								`computer_ip` ='$this->m_com_ip' AND
								`doc_date`='$this->doc_date'";		
				$crow2=$this->db->fetchOne($sql_row2);	
				if($crow2>0){
					$tbl_temp="trn_tdiary2_sl";
					$n_rows=$crow2;
				}		
			}else{
				$tbl_temp="trn_promotion_tmp1";
				$n_rows=$crow;
			}
			$str_chk=$n_rows."#".$tbl_temp;
			return $str_chk;
		}//func		
		
		public function getOtherPromotion($promo_code){
			/**
			 * @desc for web_promo =Y?
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_promo="SELECT * FROM `promo_head` 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`promo_code` = '$promo_code'";
			$row_promo=$this->db->fetchAll($sql_promo);
			return $row_promo[0];
		}//func
		
		public function getListOtherPromotion(){
			/**
			 * @desc delete param $net_amt *WR 04052012
			 * @return for web_promo<>'Y'
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_promo="SELECT * FROM `promo_head`
			WHERE
			`corporation_id`='$this->m_corporation_id' AND
			`company_id`='$this->m_company_id' AND
			`promo_pos` = 'O' AND
			`web_promo`<>'Y' AND
			'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_promo=$this->db->fetchAll($sql_promo);
			$arr_promo=array();
			if(count($row_promo)>0){
				$i=0;
				foreach($row_promo as $data){
					$promo_code=$data['promo_code'];
					//////////////////////////////////////// CHECK NEWBIRTH /////////////////////////////////////////////////////
					if($promo_code=='50BTO1P'){
						$n_chk=0;
						$sql_chk="SELECT COUNT(*) FROM `trn_promotion_tmp1` WHERE promo_code IN('NEWBIRTH')";
						$n_chk=$this->db->fetchOne($sql_chk);
						if($n_chk<1){
							$sql_chk="SELECT COUNT(*) FROM `trn_tdiary2_sl` WHERE promo_code IN('NEWBIRTH')";
							$n_chk=$this->db->fetchOne($sql_chk);
						}
						if($n_chk>0){
							continue;
						}
					}
					//////////////////////////////////////// CHECK NEWBIRTH /////////////////////////////////////////////////////
					$sql_chk="SELECT COUNT(*) FROM promo_branch
					WHERE
					`corporation_id`='$this->m_corporation_id' AND
					`company_id`='$this->m_company_id' AND
					`promo_code`='$promo_code' AND
					'$this->doc_date' BETWEEN start_date AND end_date AND (
					`branch_id`='$this->m_branch_id' OR `branch_id`='ALL') ";
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk>0){
						/*
						 $w=$data['point']*$data['point_to_discount'];
						 $discount=floor($net_amt/$data['promo_amt'])*$w;
						 */
		
						//*WR13072016
						if($promo_code=='OM13160416'){
							$arr_promo[$i]['play_last_pro']='Y';
						}else{
							$arr_promo[$i]['play_last_pro']='N';
						}
		
						$arr_promo[$i]['promo_code']=$data['promo_code'];
						$arr_promo[$i]['promo_des']=$data['promo_des'];
						$arr_promo[$i]['promo_tp']=$data['promo_tp'];
						$arr_promo[$i]['member_tp']=$data['member_tp'];
						if($data['promo_tp']=='F'){
							$arr_promo[$i]['promo_st']="Free";
						}else{
							$arr_promo[$i]['promo_st']="";
						}
						$arr_promo[$i]['promo_amt']=$data['promo_amt'];
						$arr_promo[$i]['point']=$data['point'];
						$arr_promo[$i]['point_to_discount']=$data['point_to_discount'];
						$arr_promo[$i]['discount_member']=$data['discount_member'];
						$arr_promo[$i]['web_promo']=$data['web_promo'];
						$arr_promo[$i]['check_repeat']=$data['check_repeat'];
						$arr_promo[$i]['limite_qty']=$data['limite_qty'];
						$i++;
					}
				}
			}
			return $arr_promo;
		}//func
		
		public function getListOtherPromotion21072016(){
			/**
			 * @desc delete param $net_amt *WR 04052012
			 * @return for web_promo<>'Y'
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_promo="SELECT * FROM `promo_head` 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`promo_pos` = 'O' AND
										`web_promo`<>'Y' AND
										'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_promo=$this->db->fetchAll($sql_promo);
			$arr_promo=array();
			if(count($row_promo)>0){				
				$i=0;
				foreach($row_promo as $data){
					$promo_code=$data['promo_code'];
					//////////////////////////////////////// CHECK NEWBIRTH /////////////////////////////////////////////////////
					if($promo_code=='50BTO1P'){
							$n_chk=0;
							$sql_chk="SELECT COUNT(*) FROM `trn_promotion_tmp1` WHERE promo_code IN('NEWBIRTH')";
							$n_chk=$this->db->fetchOne($sql_chk);
							if($n_chk<1){
								$sql_chk="SELECT COUNT(*) FROM `trn_tdiary2_sl` WHERE promo_code IN('NEWBIRTH')";
								$n_chk=$this->db->fetchOne($sql_chk);
							}
							if($n_chk>0){
								continue;
							}
					}
					//////////////////////////////////////// CHECK NEWBIRTH /////////////////////////////////////////////////////
					$sql_chk="SELECT COUNT(*) FROM promo_branch 
										WHERE 
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND											
											`promo_code`='$promo_code' AND 
											'$this->doc_date' BETWEEN start_date AND end_date AND (
											`branch_id`='$this->m_branch_id' OR `branch_id`='ALL') ";
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk>0){		
						/*		
						$w=$data['point']*$data['point_to_discount'];
						$discount=floor($net_amt/$data['promo_amt'])*$w;					
						*/			
						$arr_promo[$i]['promo_code']=$data['promo_code'];
						$arr_promo[$i]['promo_des']=$data['promo_des'];
						$arr_promo[$i]['promo_tp']=$data['promo_tp'];
						$arr_promo[$i]['member_tp']=$data['member_tp'];
						if($data['promo_tp']=='F'){
							$arr_promo[$i]['promo_st']="Free";
						}else{
							$arr_promo[$i]['promo_st']="";
						}
						$arr_promo[$i]['promo_amt']=$data['promo_amt'];
						$arr_promo[$i]['point']=$data['point'];
						$arr_promo[$i]['point_to_discount']=$data['point_to_discount'];
						$arr_promo[$i]['discount_member']=$data['discount_member'];
					    $arr_promo[$i]['web_promo']=$data['web_promo'];
					    $arr_promo[$i]['check_repeat']=$data['check_repeat'];
					    $arr_promo[$i]['limite_qty']=$data['limite_qty'];
						$i++;
					}
				}
			}
			return $arr_promo;
		}//func
		
		public function getListSmsPromotion(){
			/**
			 * @desc *WR 14092012
			 * @return for promo_pos='S' and web_promo<>'Y'
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$sql_promo="SELECT * FROM `promo_head` 
									WHERE 
										`corporation_id`='$this->m_corporation_id' AND
										`company_id`='$this->m_company_id' AND
										`promo_pos` IN('S','MCS') AND
										`web_promo`<>'Y' AND
										'$this->doc_date' BETWEEN start_date AND end_date	";
			$row_promo=$this->db->fetchAll($sql_promo);
			$arr_promo=array();
			if(count($row_promo)>0){
				$i=0;
				foreach($row_promo as $data){
					$promo_code=$data['promo_code'];
					$sql_chk="SELECT COUNT(*) FROM promo_branch 
										WHERE 
											`corporation_id`='$this->m_corporation_id' AND
											`company_id`='$this->m_company_id' AND											
											`promo_code`='$promo_code' AND 
											'$this->doc_date' BETWEEN start_date AND end_date AND (
											`branch_id`='$this->m_branch_id' OR `branch_id`='ALL')";					
					$n_chk=$this->db->fetchOne($sql_chk);
					if($n_chk>0){	

						//*WR23012013 for sms promotion call member survey
						if($data['promo_amt']>0){
							$sql_detail="SELECT type_discount,discount,get_point,discount_member FROM promo_detail WHERE promo_code='$data[promo_code]'";							
							$row_detail=$this->db->fetchAll($sql_detail);
							if(!empty($row_detail)){
								$arr_promo[$i]['type_discount']=$row_detail[0]['type_discount'];
								$arr_promo[$i]['discount']=$row_detail[0]['discount'];
								$arr_promo[$i]['get_point']=$row_detail[0]['get_point'];
								$arr_promo[$i]['discount_member']=$row_detail[0]['discount_member'];
							}else{
								$arr_promo[$i]['type_discount']='';
								$arr_promo[$i]['discount']='';
								$arr_promo[$i]['get_point']='';
								$arr_promo[$i]['discount_member']='';
							}
						}
						$arr_promo[$i]['promo_pos']=$data['promo_pos'];
						$arr_promo[$i]['promo_code']=$data['promo_code'];
						$arr_promo[$i]['promo_des']=$data['promo_des'];
						$arr_promo[$i]['promo_tp']=$data['promo_tp'];//example T,TR
						$arr_promo[$i]['member_tp']=$data['member_tp'];
						if($data['promo_tp']=='F'){
							$arr_promo[$i]['promo_st']="Free";
						}else{
							$arr_promo[$i]['promo_st']="";
						}						
						$arr_promo[$i]['type_p']=$data['type_p'];
						$arr_promo[$i]['promo_amt']=$data['promo_amt'];
						$arr_promo[$i]['promo_amt_type']=$data['promo_amt_type'];
						$arr_promo[$i]['point']=$data['point'];
						$arr_promo[$i]['point_to_discount']=$data['point_to_discount'];
						$arr_promo[$i]['discount_member']=$data['discount_member'];
					    $arr_promo[$i]['web_promo']=$data['web_promo'];
					    $arr_promo[$i]['check_repeat']=$data['check_repeat'];
					    $arr_promo[$i]['limite_qty']=$data['limite_qty'];
					    
						//$arr_promo[$i]['discount']=$discount;
						$i++;
					}
				}
			}			
			return $arr_promo;
		}//func
		
		function get_previous_month($date) {
			/**
			 * @call echo $this->get_previous_month("2013-03-01"); and echo $this->get_previous_month("2013/03/01");
			 * @desc 29/03/2013
			 * @var 
			 */
			$date = str_replace("/", "-", $date);
			$year=date("Y",strtotime($date));
			$month=date("n",strtotime($date)) - 1;
			if ($month == 0) {
				$month = 12;
				$year = $year - 1;
			}
			return date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));
		}//func		
	}//class
?>
