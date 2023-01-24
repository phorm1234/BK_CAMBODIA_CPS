<?php 

class Advertis{
	public 	$pricePromotion;
	public	$amountPromotion;
	public	$discountPromotion;
	public	$netPromotion;
	public  $m_com_ip;
	
	public $sv_online="10.100.53.2";
	public $user_online="master";
	public $pass_online="master";
	public $db_online="service_pos_op";
	public $db_online_old="pos_op";
	
	public $sv_my="localhost";
	public $user_my="pos-ssup";
	public $pass_my='P0z-$$up';
	public $db_my="pos_ssup";
	
public function getName(){
	
	$registry = Zend_Registry::getInstance ();
	$db = $registry ['DB'];
	$shop_id = $this->shop_id();
        $computer_ip = $this->computer_ip();
	$sql = "select * from trn_member_today where branch_id='$shop_id' AND computer_ip = '".$computer_ip."'";
	$res = $db->FetchAll($sql);
	
	if(count($res) > 0){
		$index = $db->FetchRow($sql);
		$get_name = $index['name'].'  '.$index['surname'];
	}
	return $get_name;
}

	public function getStatus(){
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		$shop_id = $this->shop_id();
		$sql = "select screen from com_branch_computer where branch_id='$shop_id'";
		$res = $db->FetchRow($sql);
		
		return $res;
	}
	
	public function showOrder(){
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		// check company branch
		$select = new Zend_Db_Select($db);
		$amount = 0;
		$total = 0;
		$discount = 0;
		$str = "";
                $computer_ip = $this->computer_ip();
		$sql = "select 
					product_id,
					name_product,
					price,quantity,
					amount,
					discount,
					member_discount1,
					member_discount2,
					coupon_discount,
					special_discount,		
					other_discount,
					co_promo_discount,
					net_amt  
				from trn_promotion_tmp1 
				WHERE computer_ip = '".$computer_ip."'
				union all
			select 
					product_id,
					name_product,
					price,quantity,
					amount,
					discount,
					member_discount1,
					member_discount2,
					coupon_discount,
					special_discount,		
					other_discount,
					co_promo_discount,
					net_amt 
				from trn_tdiary2_sl
                                WHERE computer_ip = '".$computer_ip."' ";
		$res = $db->FetchAll($sql);
		if(count($res) > 0){
			/*$str .="
					<div class=\"head\">
					</div>
					<div class=\"product\">
						<div class=\"title\">
						รายการสินค้า
						</div>
					<div class=\"pro_detail\">	";/**/
			$str .="<div class='order-block'>";
			$i = 1;
			foreach ($res as $index){
				$disPerItem = "";
				
				$netQty +=$index['quantity'];
				
				$disPerItem +=$index['discount'];
				$disPerItem +=$index['member_discount1'];
				$disPerItem +=$index['member_discount2'];
				$disPerItem +=$index['coupon_discount'];
				$disPerItem +=$index['special_discount'];
				$disPerItem +=$index['other_discount'];
				$disPerItem +=$index['co_promo_discount'];
				//$amount += $index['net_amt'];
				$total += $index['amount'];
				$discount +=$index['discount'];
				$discount +=$index['member_discount1'];
				$discount +=$index['member_discount2'];
				$discount +=$index['coupon_discount'];
				$discount +=$index['special_discount'];
				$discount +=$index['other_discount'];
				$discount +=$index['co_promo_discount'];
				//parent::getSatang($row_l2h[0]['sum_discount'],'up');
			$str .="<div id='cart_".$i."' class='row-detail'><div class='product-name'>".$i.".".$index['name_product']."</div><div class='qty'>".number_format($index['quantity'],0)."</div><div class='price'>".number_format($index['price'],2)."</div><div class='price'>".number_format($index['price']*$index['quantity'],2)."</div><div class='price'>".number_format($disPerItem,2)."</div><div class='price'>".number_format($index['net_amt'],2)."</div></div>";
			
			$i++;
			}
			$str .="</div>";
			$str.="<script>window.location.href='#cart_".($i-1)."'</script>";
			$discount = $this->getSatang($discount,'up');
			$amount = $total-$discount;
			$str .="<div class='total-detail'>";
			$str .="<div class='total-row net-qty'>สินค้าทั้งหมด : ".$netQty." รายการ</div><br />";
			
			$sql_point = "select 
					amount,
					discount,
					net_amt,
					point_receive,
					point_used,
					point_net
				from  trn_summary2display";
			$res_point = $db->FetchRow($sql_point);
			
			$str .="<div class='total-row'><div class='total'><div class='sub-total right'>คะแนนที่ได้รับ : </div><div class='sub2 right'> ".$res_point['point_receive']."</div></div><div class='total'><div class='sub-total right'>รวมเงิน : </div><div class='sub2 right'> ".number_format($total,2)."</div></div></div>";
			$str .="<div class='total-row'><div class='total'><div class='sub-total right'>คะแนนที่ใช้ : </div><div class='sub2 right'> ".$res_point['point_used']."</div></div><div class='total'><div class='sub-total right'>ส่วนลด : </div><div class='sub2 right'> ".number_format($discount,2)."</div></div></div>";
			$str .="<div class='total-row'><div class='total'><div class='sub-total right'>คะแนนสุทธิ : </div><div class='sub2 right'> ".$res_point['point_net']."</div></div><div class='total  total-all'><div class='sub-total right'>สุทธิ : </div><div class='sub2 right'> ".number_format($amount,2)."</div></div></div>";
			//$str .="<div class='total-row'><div class='total'>คะแนนที่ได้รับ : </div><div class='total'>รวมเงิน : </div></div>";
			//$str .="<div class='total-row'><div class='total'>คะแนนที่ใช้ : </div><div class='total'>ส่วนลด : </div></div>";
			//$str .="<div class='total-row'><div class='total'>คะแนนที่สุทธิ : </div><div class='total  total-all'>สุทธิ : ".number_format($amount,2)."</div></div>";
			$str .="</div>";
		}else{
			$str = "n";
		}
		return $str;
	}
	
	public function getSatang($net_amt,$flgs='normal'){
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
	
	public function shop_id(){
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		$sql = "select * from com_branch where active='1'";
		$res = $db->FetchRow($sql);
		return $res['branch_id'];
	}
        public function computer_ip(){
            return $_SERVER['REMOTE_ADDR'];
        }
                
	function read_profile(){
		$str ="";
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		$shop_id = $this->shop_id();
                $computer_ip = $this->computer_ip();
	    	$sql = "select * from trn_member_today where branch_id='$shop_id' AND computer_ip = '".$computer_ip."'";
		$res = $db->FetchAll($sql);
		//print_r($res);
		if(count($res) > 0){
			$index = $db->FetchRow($sql);

			$point = $index['total_point'];
			$exp_point_array = explode("-", $index['point_expire']);
			$exp_point = $exp_point_array[2].'/'.$exp_point_array[1].'/'.$exp_point_array[0];
			
			$exp_date = explode("-", $index['expire_date']);
			if(count($exp_date) == 3){
                            if($exp_date[0] == '2100'){ //บัตรตลอดชีพในฐานข้อมูลเก็บเป็นปี 2100
                                $txt_exp = 'สมาชิกตลอดชีพ';
                            }else{
				$get_month = $this->change_month($exp_date[1]);
				$txt_exp = $get_month." ".($exp_date[0]+543);
//                              $txt_curpoint = number_format($index['total_point'])."<font size='5'> ( หมดอายุภายใน ".$get_month." ".($exp_date[0]+543)." )</font>";
                            }
			}else{
				$txt_exp = $index['point_expire'];
			}
			//Point 
                        $txt_curpoint = number_format($index['total_point'])." ";
                        $exp_point_month = $this->change_month($exp_point_array[1]);
                        if($index['point_expire'] != 0){
                                $txt_curpoint .= " <font size='5'> ( หมดอายุภายใน ".$exp_point_month." ".($exp_point_array[0]+543)." )</font>";
                        }
			$exp_trnPoint = explode("-", $index['expire_transfer_point']);
			//$exp_date = $exp_date[2].'/'.$exp_date[1].'/'.$exp_date[0];
			
			$get_month_trnPoint = $this->change_month($exp_trnPoint[1]);
			
			$str .="<div class='top-name'>คุณ".$index['name']."  ".$index['surname']."</div>";
			$str .="<div class='mem-row'><div class='mem-title'>ชื่อ / นามสกุล</div><div class='mem-detail'>: ".$index['name']."  ".$index['surname']."</div></div>";
			$str .="<div class='mem-row'><div class='mem-title'>ประเภทบัตร</div><div class='mem-detail'>".$this->check_type_card($index['special_day'])."</div></div>";
			$str .="<div class='mem-row'><div class='mem-title'>บัตรหมดอายุ</div><div class='mem-detail'>:  ".$txt_exp."</div></div>";
			$str .="<div class='mem-row'><div class='mem-title'>คะแนนสะสม ณ ปัจจุบัน</div><div class='mem-detail'>:  ".$txt_curpoint."</div></div>";
			$str .="<input id='get_expm' type='hidden' value='".substr($exp_point_month, 3,10)." ".($exp_point[0]+543)." '>";
			if($index['transfer_point'] != "0" && $index['expire_transfer_point'] != 0){
				$str .="<div class='mem-row'><div class='mem-title'>คะแนนที่โอนจากบัตรเดิม</div><div class='mem-detail'>:  ".$index['transfer_point']." <font size='5'> ( หมดอายุภายใน ".$get_month_trnPoint." ".($exp_trnPoint[0]+543)." )</div></div>";
			}
			/*
			else{
				$str .="<div class='mem-row'><div class='mem-title'>คะแนนสะสมหมดอายุ</div><div class='mem-detail'>:  -</div></div>";
			}
			/**/
			$str .="<div class='mem-row'><div class='mem-title'>เบอร์โทร</div><div class='mem-detail'>:  ".$index['mobile_no']."</div></div>";
			$str .="<div class='mem-row'><div class='mem-title'>หมายเลขสมาชิก</div><div class='mem-detail'>:  xxxxxxxxx".substr($index['idcard'],9,13)."</div></div>";
		}else{
			$str ='n';
		}
		return array($str,$point);
	}
	
	private function check_type_card($type){
		//member_card_type
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		
		$sql = "select des2 from member_card_type where des1='$type'";
		$res = $db->FetchRow($sql);
		/*
		 * : OPS GOLD CARD
							<div style="font-size: 22px; padding-left: 15px;">วันอังคารที่ 1 ของเดือน</div>
		 */
		$str_date = "";
		switch ($type){
			case "TH1":
				$card_type = ': OPS WHITE CARD';
				$card_img = 'card_w.png'; 
				$str_date = 'พฤหัสบดีที่ 1';
				
				break;
			case "TH2": 
				$card_type = ': OPS WHITE CARD';
				$card_img = 'card_w.png'; 
				$str_date = 'พฤหัสบดีที่ 2';
				
				break;
			case "TH3": 
				$card_type = ': OPS WHITE CARD';
				$card_img = 'card_w.png'; 
				$str_date = 'พฤหัสบดีที่ 3';
				
				break;
			case "TH4": 
				$card_type = ': OPS WHITE CARD';
				$card_img = 'card_w.png'; 
				$str_date = 'พฤหัสบดีที่ 4';
				
				break;
			case "TU1": 
				$card_type = ': OPS GOLD CARD';
				$card_img = 'card_g.png'; 
				$str_date = 'อังคารที่ 1';
				
				break;
			case "TU2": 
				$card_type = ': OPS GOLD CARD';
				$card_img = 'card_g.png'; 
				$str_date = 'อังคารที่ 2';
				
				break;
			case "TU3": 
				$card_type = ': OPS GOLD CARD';
				$card_img = 'card_g.png';
				$str_date = 'อังคารที่ 3';
				
				break;
			case "TU4": 
				$card_type = ': OPS GOLD CARD';
				$card_img = 'card_g.png';
				$str_date = 'อังคารที่ 4';
				
				break;
		}
		$str = $card_type."<div style='padding-left: 15px;'>วัน".$str_date." ของเดือน</div><div class='card-img'><img src='http://localhost/mcs/images/".$card_img."' /></div>";
		return $str;
	}
	
	public function promotion(){
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		$shop_id = $this->shop_id();
                $computer_ip = $this->computer_ip();
		// member_id
		$sqlm = "select member_id,birthday from trn_member_today where branch_id='$shop_id' AND computer_ip = '".$computer_ip."'";
		$resm = $db->FetchRow($sqlm);
		$count_pro = 0;
		$sqlcountpro = "SELECT * FROM `trn_member_privilege` WHERE member_no='$resm[member_id]' and STATUS = 'Y' AND computer_ip = '".$computer_ip."' GROUP BY promo_type";
		$count_type = count($db->FetchAll($sqlcountpro));
	
		//
		//Birthday Package
		
		$sqlpro1 = "
		select t1.promo_name,t1.promo_code,t1.promo_detail,t2.des1,t2.des2,t2.des3
		from trn_member_privilege t1 LEFT JOIN promotion_screen t2 ON t1.promo_code = t2.promo_code
		where t1.member_no='$resm[member_id]' and t1.promo_type='1' AND t1.computer_ip = '".$computer_ip."' and t1.status ='Y'";
		$resp1 = $db->FetchAll($sqlpro1);
		$date_pro = $this->check_exp();
		$str_birthday = "";		
		if(count($resp1) > 0){	
			$rows = 1;		
			foreach ($resp1 as $index){
				if($rows == 1){
					$active = " active";
					$rows +=1;
				}else{
					$active ="";
				}				
				$str_des1 = explode("%", $index["des1"]);
				
				switch ($index["promo_code"]){
					case  "OX02651014":	$per = '25'; 
										$limit = '2,500';
					break;
					case  "OX02671014":	$per = '35';
										$limit = '2,000'; 
					break;
					case  "OX02691014":	$per = '35';
										$limit = '3,000'; 
					break;
				}
				
				$str_birthday .="<div class='item".$active."'>";
				$str_birthday .="<div class='line0'> สุขสันต์วันเกิดคุณ ".$this->getName()."</div>";
				$str_birthday .="<div class='line1'><font size='6' color='#a42214'>รับส่วนลด ".$per." % </font><font size='5' color='#666'>วงเงิน ".$limit." บาท</font></div>";
				$str_birthday .="<div class='line3'>ใช้สิทธิ์ได้ตั้งแต่ วันนี้ - ".$date_pro[0]." ".($date_pro[3]+543)."</div>";
				$str_birthday .="</div>";
			}	
		}else{
			$str_birthday = "n";
		}
		
		
	//Welcome  Package
	$now_date = date('Y-m-d');
		$sqlpro3 = "select t1.promo_name,t1.promo_code,t1.status,t1.promo_detail,t2.des1,t2.des2,t2.des3
		from trn_member_privilege t1 LEFT JOIN promotion_screen t2 ON t1.promo_code = t2.promo_code
		where t1.member_no='$resm[member_id]' and t1.promo_type='3' AND t1.computer_ip = '".$computer_ip."' AND $now_date <= t1.reg_date";
		$resp3 = $db->FetchAll($sqlpro3);
		
//		$chk_Y = 0;
//		$chk_max = 0;
//		foreach ($resp3 as $chk){
//			if($chk["status"] == "N"){
//				$chk_Y = $chk_Y+1;
//			}
//			$chk_max = $chk_max+1;
//		}
		$str_welcome = "";
		if(count($resp3) > 0){
			$rows = 1;
			
			foreach ($resp3 as $index){
				if($rows == 1){
					$active = " active";
					$rows +=1;
				}else{
					$active ="";
				}
				
				switch ($index["promo_code"]){
					case  "OX07061114":	$price = '155'; 
										$limit = '800';
					break;
					case  "OX07071114":	$price = '255';
										$limit = '1,200'; 
					break;
					case  "OX07081114":	$price = '315';
										$limit = '1,500'; 
					break;
				}
				
			//if($chk_Y == $chk_max){
				$str_welcome .="<div class='item".$active."'>";
				$str_welcome .="<div class='line0'> ต้อนรับสมาชิกใหม่เลือกรับ 1 สิทธิ์/เดือน <br />(สูงสุด 3 สิทธิ์) ใช้สิทธิ์ได้ถึง<font size='5' color='#a42214'> ".$date_pro[1]." ".($date_pro[4]+543)."</font></div>";
				$str_welcome .="<div class='line1'><font size='6' color='#a42214'>เลือกรับสินค้าฟรี ".$price." บาท</font></div><div class='line2'><font size='5' color='#fff'> เมื่อซื้อครบ ".$limit." บ.สุทธิ</font></div>";
				$str_welcome .="</div>";
//				}else{
//					$str_welcome = "n";
//				}
			}	
		}else{
			$str_welcome = "n";
		}
		
	    //Renewal Package
		$sqlpro4 = "select t1.promo_name,t1.promo_code,t1.promo_detail,t2.des1,t2.des2,t2.des3
		from trn_member_privilege t1 LEFT JOIN promotion_screen t2 ON t1.promo_code = t2.promo_code
		where t1.member_no='$resm[member_id]' and t1.promo_type='4' AND t1.computer_ip = '".$computer_ip."' and t1.status ='Y' order by t1.promo_code asc";
		$resp4 = $db->FetchAll($sqlpro4);
		$str_renewal = "";
		if(count($resp4) > 0){
			$rows = 1;
			foreach ($resp4 as $index){
				if($rows == 1){
					$active = " active";
					$rows +=1;
				}else{
					$active ="";
				}
				switch ($index["promo_code"]){
					case  "OX07771014":	$price = '155'; 
										$limit = '800';
					break;
					case  "OX07781014":	$price = '255';
										$limit = '1,200'; 
					break;
					case  "OX07791014":	$price = '315';
										$limit = '1,500'; 
					break;
				}
				$str_renewal .="<div class='item".$active."'>";
				$str_renewal .="<div class='line0'> แทนคำขอบคุณสำหรับการต่ออายุสมาชิก <br />เลือกรับ 1 สิทธิ์ ภายในเดือน <span style='color:#a42214;'><font size='5'>".$date_pro[2]." ".($date_pro[5]+543)."</font></span></div>";
				$str_renewal .="<div class='line1'><font size='6' color='#a42214'>เลือกรับสินค้าฟรี ".$price." บาท</font></div><div class='line0'>เมื่อซื้อครบ ".$limit." บ.สุทธิ</div>";
				$str_renewal .="</div>";
			}	
		}else{
			$str_renewal = "n";
		}
		
		
	//check expire
	
		$sql = "select * from trn_member_today where branch_id='$shop_id' AND computer_ip = '".$computer_ip."'";
		$res = $db->FetchAll($sql);
		if(count($res) > 0){
			$index = $db->FetchRow($sql);
			
			$exp_date = explode("-", $index['expire_date']);
			$get_date =$exp_date[0]."-".$exp_date[1]; 
			
			$min_date = date('Y-m', strtotime($index['expire_date'] . "-3 months"));
			$max_date = date('Y-m', strtotime($index['expire_date'] . "+2 months"));
		
			//$chk_exp = $min_date."--".date('Y-m')."--".$max_date;
			$gd = $exp_date[1]+2;
			$get_month = $this->change_month($gd);
			
			if($min_date <= date('Y-m') && date('Y-m') <= $max_date){
				
				if($index['expire_date'] < date('Y-m-d')){
					$chk_exp = "Y";
				}else{
					//$chk_exp = "N".":".$min_date."<=".date('Y-m')."<=".$max_date;
					$chk_exp = "N";
				}
				//if($get_date <= $max_date){
					//$str_exp .="$get_date : $max_date = true";
					
	//				if($index['buy_net'] < '15000'){
	//					$sqlpro5 = "SELECT * FROM `com_application_head` where '".$index['doc_date ']."' between start_date and end_date and member_year = '".$index['age_card']."' and start_baht < '15000'";
	//				}else{
	//					$sqlpro5 = "SELECT * FROM `com_application_head` where '".$index['doc_date ']."' between start_date and end_date and member_year = '".$index['age_card']."' and start_baht >= '15000'";
	//				}
	//				$resp4 = $db->FetchRow($sqlpro5);
					if( $index['age_card'] == '2' && $index['buy_net'] < 15000 ){
						$str_exp .="<font size='5'>เป็น <br/><span style='color:#a42214'>OPS Gold Card เมื่อซื้อ 300 บาท(สุทธิ) <br/>พร้อมเลือกสินค้าเพิ่มฟรี 200 บาท <br/>วันนี้ถึง ".$get_month." ".($exp_date[0]+543)."</span></font>";
					}
					if( $index['age_card'] == '4' || ( $index['age_card'] == '2' && $index['buy_net'] >= 15000 )){
						$str_exp .="<font size='5'>เป็น<br/><span style='color:#a42214'>OPS Gold Card ฟรี วันนี้ถึง ".$get_month." ".($exp_date[0]+543)."</span></font>";
					}
					
					
							
				/**/
//				}else{
//						$str_exp = "n";
//					}
			}else{
						$str_exp = "n";
					}
		}
	
		
		return array($str_birthday,$str_welcome,$str_renewal,$str_exp,$chk_exp);
	}
	
	private function check_exp(){
		$str ="";
		$registry = Zend_Registry::getInstance ();
		$db = $registry ['DB'];
		$shop_id = $this->shop_id();
                $computer_ip = $this->computer_ip();
                $sql = "select * from trn_member_today where branch_id='$shop_id' AND computer_ip = '".$computer_ip."'";
		$res = $db->FetchAll($sql);
		if(count($res) > 0){
			$index = $db->FetchRow($sql);
			
			$birth_date = explode("-", $index['birthday']);
			$apply_date = explode("-", $index['apply_date']);
			$exp_date = explode("-", $index['expire_date']);
			
			//$exp_date = $exp_date[2].'/'.$exp_date[1].'/'.$exp_date[0];
			return $this->get_month($birth_date,$apply_date,$exp_date);
		}
	}
	
	private function get_month($birth_date,$apply_date,$exp_date){
		
		
		$birthday = $birth_date[1] +1;
		if($birthday > 12){
			$birthday = $birthday - 12;
			$birth_year = date("Y")+1;
		}else {
			$birth_year = date("Y");
		}
		
		$welcome = $apply_date[1] + 3;
		if($welcome > 12){
			$welcome = $welcome - 12;
			$wel_year = date("Y")+1;
		}else {
			$wel_year = date("Y");
		}
		
		$renew = $exp_date[1]+1;
		if($renew > 12){
			$renew = $renew - 12;
			$renew_year = date("Y")+1;
		}else {
			$renew_year = date("Y");
		}
		/*
		$mon_arr = array('2'=>"ก.พ.","มี.ค.","เม.ย.","มิ.ย.","พ.ค.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.","ม.ค.","ก.พ.","มี.ค.");
		$mon_arr[$birthday];
		$mon_arr[$welcome];
		$mon_arr[$renew];
		return array($mon_arr[$birthday],$mon_arr[$wel],$mon_arr[$renew]);
		/**/
		
		$get_birthday = $this->change_month($birthday);
		$get_welcome = $this->change_month($welcome);
		$get_renew = $this->change_month($renew);
		
		return array($get_birthday,$get_welcome,$get_renew,$birth_year,$wel_year,$renew_year);
		
	}
	
	private function change_month($exp_date){
			
			switch ($exp_date){
				case "1": $month = "31 ม.ค.";
				break;
	
				case "2": $month = "28 ก.พ.";
				break; 
				
				case "3": $month = "31 มี.ค.";
				break; 
				
				case "4": $month = "30 เม.ย.";
				break; 
				
				case "5": $month = "30 พ.ค.";
				break; 
				
				case "6": $month = "31 มิ.ย.";
				break; 
				
				case "7": $month = "31 ก.ค.";
				break; 
				
				case "8": $month = "31 ส.ค.";
				break; 
				
				case "9": $month = "30 ก.ย.";
				break; 
				
				case "10": $month = "31 ต.ค.";
				break; 
				
				case "11": $month = "30 พ.ย.";
				break; 
				
				case "12": $month = "31 ธ.ค.";
				break;
				 
				default: $month = "";
			}
			
			return $month;
		}
		/**/
	
}
?>
