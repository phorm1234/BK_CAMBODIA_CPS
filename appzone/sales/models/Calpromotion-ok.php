<?php
class Model_Calpromotion extends Model_PosGlobal
{
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


 	   function __construct() {
            parent::__construct();
            //$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl"); 

           $this->m_com_ip=$_SERVER['REMOTE_ADDR'];
           
 	   	
 	   }
		function set_formatdate($var){
			
			$arr=explode("-",$var);
			return "$arr[2]/$arr[1]/$arr[0]";
		}
	   function onconnect($process_id,$table_id){
		$objCall=new SSUP_Controller_Plugin_Db();
		$this->db=$objCall->processdb($process_id,$table_id);
	   }   
 	   
 	   
 	   
 	   
	   function list_product_nopro($doc_no){
		   $sql="select * from trn_tdiary2_sl where computer_ip='$this->m_com_ip' order by seq";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl"); 
		   $result = $this->db->fetchAll($sql, 2);
	 	   return $result ;
	   }
	   function copyToTmp($doc_no) {
	   		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
		   	$result = $this->db->query("delete from trn_promotion_tmp0 where computer_ip='$this->m_com_ip'", 2);
		   	
		   	if($result) {
			   	$sql="select * from trn_tdiary2_sl where computer_ip='$this->m_com_ip' order by seq";
			   	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl"); 
			   	$data=$this->db->fetchAll($sql, 2);
			   	$i=1;
			   	foreach($data as $dataf) {
			   			$copy="
			   				insert trn_promotion_tmp0 set
							corporation_id='$dataf[corporation_id]',
							company_id='$dataf[company_id]',
							branch_id='$dataf[branch_id]',
							doc_date='$dataf[doc_date]',
							doc_time='$dataf[doc_time]',
							doc_no='$dataf[doc_no]',
							doc_tp='$dataf[doc_tp]',
							status_no='$dataf[status_no]',
							flag='$dataf[flag]',
							seq='$i',
							promo_code='$dataf[promo_code]',
							promo_seq='$dataf[promo_seq]',
							promo_pos='$dataf[promo_pos]',
							promo_st='$dataf[promo_st]',
							promo_tp='$dataf[promo_tp]',
							product_id='$dataf[product_id]',
							price='$dataf[price]',
							quantity='$dataf[quantity]',
							stock_st='$dataf[stock_st]',
							amount='$dataf[amount]',
							discount='$dataf[discount]',
							member_discount1='$dataf[member_discount1]',
							member_discount2='$dataf[member_discount2]',
							co_promo_discount='$dataf[co_promo_discount]',
							coupon_discount='$dataf[coupon_discount]',
							special_discount='$dataf[special_discount]',
							other_discount='$dataf[other_discount]',
							net_amt='$dataf[net_amt]',
							cost='$dataf[cost]',
							cost_amt='$dataf[cost_amt]',
							promo_qty='$dataf[promo_qty]',
							weight='$dataf[weight]',
							exclude_promo='$dataf[exclude_promo]',
							location_id='$dataf[location_id]',
							product_status='$dataf[product_status]',
							get_point='$dataf[get_point]',

						
							
							discount_member='$dataf[discount_member]',
							cal_amt='$dataf[cal_amt]',
							tax_type='$dataf[tax_type]',
							gp='$dataf[gp]',
							point1='$dataf[point1]',
							point2='$dataf[point2]',
							discount_percent='$dataf[discount_percent]',
							member_percent1='$dataf[member_percent1]',
							member_percent2='$dataf[member_percent2]',
							
							co_promo_percent='$dataf[co_promo_percent]',
							co_promo_code='$dataf[co_promo_code]',
							coupon_code='$dataf[coupon_code]',
							coupon_percent='$dataf[coupon_percent]',
							not_return='$dataf[not_return]',
							lot_no='$dataf[lot_no]',
							lot_expire='$dataf[lot_expire]',
							lot_date='$dataf[lot_date]',
							short_qty='$dataf[short_qty]',
							short_amt='$dataf[short_amt]',
							ret_short_qty='$dataf[ret_short_qty]',
							ret_short_amt='$dataf[ret_short_amt]',
							cn_qty='$dataf[cn_qty]',
							cn_amt='$dataf[cn_amt]',
							cn_tp='$dataf[cn_tp]',
							cn_remark='$dataf[cn_remark]',
							deleted='$dataf[deleted]',
							user_id='$dataf[user_id]',
							cashier_id='$dataf[cashier_id]',
							saleman_id='$dataf[saleman_id]',
							reg_date='$dataf[reg_date]',
							reg_time='$dataf[reg_time]',
							reg_user='$dataf[reg_user]',
							upd_date='$dataf[upd_date]',
							upd_time='$dataf[upd_time]',
							upd_user='$dataf[upd_user]'
			   			";
			   			//echo $copy;
			   			if($dataf['quantity']>1) {
			   				$quantitySum=$dataf['quantity'];
			   				$member_discount1=$dataf['member_discount1'];
			   				$member_discount1_one=$member_discount1/$quantitySum;
			   				
			   				$price=$dataf['price'];
			   				$net_amt_one=$price-$member_discount1_one;
			   				
			   				
			   				
			   				for($j=1; $j<=$dataf['quantity']; $j++){

			   					/*$percent=$member_discount1*100/$amount;
			   					$discount_this=($price*$percent/100);
			   					$net_amt=$price-$discount_this;*/
			   					
					   			$copy="
					   				insert trn_promotion_tmp0 set
									corporation_id='$dataf[corporation_id]',
									company_id='$dataf[company_id]',
									branch_id='$dataf[branch_id]',
									doc_date='$dataf[doc_date]',
									doc_time='$dataf[doc_time]',
									doc_no='$dataf[doc_no]',
									doc_tp='$dataf[doc_tp]',
									status_no='$dataf[status_no]',
									flag='$dataf[flag]',
									seq='$i',
									promo_code='$dataf[promo_code]',
									promo_seq='$dataf[promo_seq]',
									promo_pos='$dataf[promo_pos]',
									promo_st='$dataf[promo_st]',
									promo_tp='$dataf[promo_tp]',
									product_id='$dataf[product_id]',
									price='$price',
									quantity='1',
									stock_st='$dataf[stock_st]',
									amount='$price',
									discount='$dataf[discount]',
									member_discount1='$member_discount1_one',
									member_discount2='$dataf[member_discount2]',
									co_promo_discount='$dataf[co_promo_discount]',
									coupon_discount='$dataf[coupon_discount]',
									special_discount='$dataf[special_discount]',
									other_discount='$dataf[other_discount]',
									net_amt='$net_amt_one',
									cost='$dataf[cost]',
									cost_amt='$dataf[cost_amt]',
									promo_qty='$dataf[promo_qty]',
									weight='$dataf[weight]',
									exclude_promo='$dataf[exclude_promo]',
									location_id='$dataf[location_id]',
									product_status='$dataf[product_status]',
									get_point='$dataf[get_point]',
									discount_member='$dataf[discount_member]',
									cal_amt='$dataf[cal_amt]',
									tax_type='$dataf[tax_type]',
									gp='$dataf[gp]',
									point1='$dataf[point1]',
									point2='$dataf[point2]',
									discount_percent='$dataf[discount_percent]',
									member_percent1='$dataf[member_percent1]',
									member_percent2='$dataf[member_percent2]',
									co_promo_percent='$dataf[co_promo_percent]',
									co_promo_code='$dataf[co_promo_code]',
									coupon_code='$dataf[coupon_code]',
									coupon_percent='$dataf[coupon_percent]',
									not_return='$dataf[not_return]',
									lot_no='$dataf[lot_no]',
									lot_expire='$dataf[lot_expire]',
									lot_date='$dataf[lot_date]',
									short_qty='$dataf[short_qty]',
									short_amt='$dataf[short_amt]',
									ret_short_qty='$dataf[ret_short_qty]',
									ret_short_amt='$dataf[ret_short_amt]',
									cn_qty='$dataf[cn_qty]',
									cn_amt='$dataf[cn_amt]',
									cn_tp='$dataf[cn_tp]',
									cn_remark='$dataf[cn_remark]',
									deleted='$dataf[deleted]',
									
									user_id='$dataf[user_id]',
									cashier_id='$dataf[cashier_id]',
									saleman_id='$dataf[saleman_id]',
									
									reg_date='$dataf[reg_date]',
									reg_time='$dataf[reg_time]',
									reg_user='$dataf[reg_user]',
									upd_date='$dataf[upd_date]',
									upd_time='$dataf[upd_time]',
									upd_user='$dataf[upd_user]'
					   			";
					   			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
			   					$this->db->query($copy, 2);
			   					$i++;
			   				}
			   			} else {
			   					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
			   					$this->db->query($copy, 2);
			   					$i++;
			   			}
			   			
			   			
			   	}
		   	
		   	}
	   }
	   
	   
	   function list_old($doc_no){
		   $sql="select * from trn_promotion_tmp0 where computer_ip='$this->m_com_ip' order by seq limit 0,1";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
	 	   $result = $this->db->fetchAll($sql, 2);
	 	    return $result ;
	   }	   
	   function list_product($doc_no){
		   $sql="select * from trn_promotion_tmp0 where computer_ip='$this->m_com_ip' and ((promo_code='' and promo_pos='') or (promo_code='Y' and promo_pos='Y')) order by price desc";
	 	   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
		   $result = $this->db->fetchAll($sql, 2);
	 	    return $result ;
	   }

	   function select_productNull($doc_no,$product_id){
		   $sql="select * from trn_promotion_tmp0 where computer_ip='$this->m_com_ip' and product_id='$product_id' and promo_code='' and promo_pos='' order by price desc limit 0,1";
	 	   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
		   $result = $this->db->fetchAll($sql, 2);
	 	   return $result ;
	   }	   
	   function markList($doc_no,$seq){
		   $sql="update trn_promotion_tmp0 set promo_code='Y' where computer_ip='$this->m_com_ip' and seq='$seq' ";
	 	   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0"); 
		   $result = $this->db->query($sql, 2);
	 	   //echo $sql."\n";
	   }	   	   
	   	   
 	   function showtmppro($doc_no) {
 	   //$this->db=Zend_Registry::get('db1');
 	    $sql="SELECT a.*,b.promo_des 
 	    FROM trn_promotion_tmp1 as a left join promo_head as b 
 	    ON a.promo_code=b.promo_code 
 	    WHERE 
 	    a.computer_ip='$this->m_com_ip'  
 	    ORDER BY
 	     a.id,a.promo_code,a.promo_seq";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result ;
 	   }
 	   
 	   
 
 	   function dataPromoHead($promo_code) {
 	    $sql="select * from promo_head where promo_code='$promo_code'";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   }
 	   function chk_promotion($product_id) {
 	   	
 	    $sql="select distinct promo_code from promo_detail where promo_pos='M' and product_id='$product_id'  and '$this->doc_date' between start_date and end_date";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return count($result);
 	   }
 	   function dataPromotion($product_id) {
 	   	
 	    $sql="select distinct promo_code from promo_detail where promo_pos='M' and product_id='$product_id'  and '$this->doc_date' between start_date and end_date";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   } 	
 	   function seqDetailPromotion($promo_code,$seq_pro) {
 	   	
 	    $sql="select * from promo_detail where promo_code='$promo_code' and seq_pro='$seq_pro' ";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   } 
 	   function seqDetailPromotionName($promo_code,$seq_pro) {
 	   	
 	    $sql="select a.product_id,a.seq_pro,b.name_product,b.price,c.stock,shelf.shelf_no
 	     from 
 	     promo_detail as a left join com_product_master as b
 	     on a.product_id=b.product_id
 	     
 	     left join (select product_id,onhand-allocate as stock from com_stock_master where year=year('$this->doc_date') and month=month('$this->doc_date') ) as c
 	     on a.product_id=c.product_id
 	     
 	     left join (SELECT * FROM `chk_shelf_product`) as shelf
 	     on a.product_id=shelf.product_id
 	     
 	     where 
 	     a.promo_code='$promo_code' and a.seq_pro='$seq_pro'
 	     
 	     

 	     order by a.product_id 
 	     ";
 	   
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   } 
 	   
 	   function seqDetailPromotionName_auto($promo_code) {
 	   	
 	    $sql="select a.product_id,b.name_product,b.price,c.stock,shelf.shelf_no
 	     from 
 	     promo_detail as a left join com_product_master as b
 	     on a.product_id=b.product_id
 	     
 	     left join (select product_id,onhand-allocate as stock from com_stock_master where year=year('$this->doc_date') and month=month('$this->doc_date') ) as c
 	     on a.product_id=c.product_id
 	     
 	     left join (SELECT * FROM `chk_shelf_product`) as shelf
 	     on a.product_id=shelf.product_id
 	     
 	     where 
 	     a.promo_code='$promo_code' 
 	     
 	     
 	     group by a.product_id,b.name_product,b.price,c.stock,shelf.shelf_no
 	     order by a.product_id 
 	     ";
 	   	//echo $sql;
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail"); 
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   } 
 	   
 	   function seqDetailPromotionLimit($promo_code,$seq_pro) {
 	   	
 	    $sql="select * from promo_detail where promo_code='$promo_code' and seq_pro='$seq_pro' limit 1";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   } 
 	   function seqPromotion($promo_code,$seq_pro,$product_id) {
 	   	
 	    $sql="select * from promo_detail where promo_code='$promo_code' and seq_pro='$seq_pro' and (product_id='$product_id' or product_id='ALL') ";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   }

 	    	   
 	   function stepPromotion($promo_code) {
 	   	
 	    $sql="select distinct seq_pro from promo_detail where promo_code='$promo_code'";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
 	    $result = $this->db->fetchAll($sql, 2);
 	    return count($result);
 	   } 	   
 	   
 	   
      
 	   function query($sql) {
 	    return $this->db->query($sql);
 	   }

 	   function chkBarcodeProduct($barcode) {
	 	    $sql="select * from com_product_master where barcode='$barcode'";
	 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
	 	    $result = $this->db->fetchAll($sql, 2);
	 	    return $result;
 	   }
 	   
 	   
 	   function dataProduct($product_id) {
	 	    $sql="select * from com_product_master where product_id='$product_id'";
	 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_product_master");
	 	    $result = $this->db->fetchAll($sql, 2);
	 	    return $result;
 	   }
	   function datatmp2($promo_seq){
	   		$sql="select * from trn_promotion_tmp2 where promo_seq='$promo_seq' and computer_ip='$this->m_com_ip'";
	   		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	    $result = $this->db->fetchAll($sql, 2);
	 	    return $result;
	   		
	   	
	   }
 	   
 	   
 	   function dataStockProduct($product_id,$quantity) {
 	    $sql="select onhand-allocate as stock from com_stock_master where product_id='$product_id'
 	    and month=month('$this->doc_date') and year=year('$this->doc_date') and onhand-allocate>='$quantity' ";
 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
 	    $result = $this->db->fetchAll($sql, 2);
 	    return $result;
 	   }
 	   
 	   
 	   
		function caseNet($type_discount,$quantity,$productPromotion,$discount) {
			if(strtolower($type_discount)=="price2") {//Dealer
				$this->pricePromotion=$discount;
				$this->amountPromotion=$quantity*$this->pricePromotion;
				$this->discountPromotion=0;
				$this->netPromotion=$this->amountPromotion;
			} else if (strtolower($type_discount)=="price1"){
				$dataProduct=$this->dataProduct($productPromotion);
				$this->pricePromotion=$dataProduct[0]['price'];
				$this->amountPromotion=$quantity*$this->pricePromotion;
				
				$this->discountPromotion=($this->pricePromotion-$discount)*$quantity;
				$this->netPromotion=$this->amountPromotion-$this->discountPromotion;
				
			} else if (strtolower($type_discount)=="normal"){
				$dataProduct=$this->dataProduct($productPromotion);
				$this->pricePromotion=$dataProduct[0]['price'];
				$this->amountPromotion=$quantity*$this->pricePromotion;
				$this->discountPromotion=0;
				$this->netPromotion=$this->amountPromotion;
			} else if (strtolower($type_discount)=="free"){
				
				$this->pricePromotion=0;
				$this->amountPromotion=$quantity*$this->pricePromotion;
				$this->discountPromotion=0;
				$this->netPromotion=$this->amountPromotion;
			} else if (strtolower($type_discount)=="percent"){
				
				$dataProduct=$this->dataProduct($productPromotion);
				$this->pricePromotion=$dataProduct[0]['price'];
				$this->amountPromotion=$quantity*$this->pricePromotion;
				$discountCal=(($this->amountPromotion*$discount)/100);
				$this->discountPromotion=$discountCal;
				$this->netPromotion=$this->amountPromotion-$discountCal;
			
			} else if (strtolower($type_discount)=="discount"){
				
				$dataProduct=$this->dataProduct($productPromotion);
				$this->pricePromotion=$dataProduct[0]['price'];
				$this->amountPromotion=$quantity*$this->pricePromotion;
				$discountCal=$discount;
				$this->discountPromotion=$discountCal*$quantity;
				//echo $this->amountPromotion . "/";
				//echo $discountCal*$quantity;
				$this->netPromotion=$this->amountPromotion-$this->discountPromotion;
			}
			
			return array("pricePromotion"=>$this->pricePromotion,"amountPromotion"=>$this->amountPromotion,"discountPromotion"=>$this->discountPromotion,"netPromotion"=>$this->netPromotion,);
		
		}
		

		
		function nextSeq($tbl,$fld){
			$sql="select max($fld) as maxSeq from $tbl where  computer_ip='$this->m_com_ip' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","$tbl");
			$result = $this->db->fetchAll($sql, 2);
			if($result){
				$seq=$result[0]['maxSeq']+1;
			}else{
				$seq=1;
			}
			return $seq;
			
		}
		
		
		function insertNoPro($doc_noSend,$product_id,$quantity){
			//from wirat
			$objPos=new Model_PosGlobal();
			$doc_date=$objPos->checkDocDate(); 
			
			
			/*$corporation_id=$empprofile['corporation_id']; 
			$company_id=$empprofile['company_id']; 
			$branch_id=$empprofile['branch_id'];*/
			$corporation_id= $this->m_corporation_id;
			$company_id=$this->m_company_id ; 
			$branch_id=$this->m_branch_id;
			$com_ip=$this->m_com_ip;
			$doc_date=$doc_date;
			$doc_no="";

			
			$dataProduct=$this->dataProduct($product_id);
			$tax_type=$dataProduct[0]['tax_type'];
			$name_product=$dataProduct[0]['name_product'];
			$unit=$dataProduct[0]['unit'];
			
			$findDataOld="select * from trn_tdiary2_sl_val where computer_ip='$this->m_com_ip' limit 1";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl_val");
			//echo $findDataOld;
			$dataOld = $this->db->fetchAll($findDataOld, 2);
			$doc_tp=$dataOld[0]['doc_tp'];
			$status_no=$dataOld[0]['status_no'];
			$member_percent1=$dataOld[0]['member_percent1'];
			$member_percent2=$dataOld[0]['member_percent2'];
			$stock_st=$dataOld[0]['stock_st'];
			$reg_user=$dataOld[0]['reg_user'];
			
			$seq=$this->nextSeq('trn_promotion_tmp1','seq');
			$seq_set=$this->nextSeq('trn_promotion_tmp1','seq_set');
			$dataProduct=$this->dataProduct($product_id);
			$price=$dataProduct[0]['price'];
			$amount=$price*$quantity;
			$member_discount1=($amount*$member_percent1)/100; //ส่วนลดสมาชิก1
			
			$net=$amount-$member_discount1;
			
			$member_discount2=($net*$member_percent2)/100; //ส่วนลดสมาชิก2
			$net=$net-$member_discount2;
			
			$dataPos=$this->dataPos();
			$member_no=$dataPos[0]['member_no'];
			
			
			
			
			
			
			$insert="insert trn_promotion_tmp1 set
						computer_no='$this->m_computer_no',
						computer_ip='$this->m_com_ip',
						corporation_id='$corporation_id',
						company_id='$company_id',
						branch_id='$branch_id',
						member_no='$member_no',
						doc_date='$doc_date',
						doc_time=time(now()),
						doc_no='$com_ip',
						doc_tp='$doc_tp',
						status_no='$status_no',
						seq='$seq',
						seq_set='$seq_set',
						promo_code='',
						promo_seq='',
						promo_pos='',
						promo_tp='',
						promo_st='',
						product_id='$product_id',
						name_product='$name_product',
						unit='$unit',
						price='$price',
						quantity='$quantity',
						amount='$amount',
						discount='0',
						net_amt='$net',
						member_discount1='$member_discount1',
						member_discount2='$member_discount2',
						tax_type='$tax_type',
						member_percent1='$member_percent1',
						member_percent2='$member_percent2',
						stock_st='$stock_st',
						reg_date=date(now()),
						reg_time=time(now()),
						reg_user='$reg_user',
						upd_date=date(now()),
						upd_time=time(now()),
						upd_user ='$reg_user',
						target_pro='HOTPRO'
						";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
						$this->db->query($insert, 2);
						
						
						
						
		}
		
		
		function insertPro($doc_noSend,$seq,$promo_code,$seq_pro,$promo_pos,$promo_tp,$promo_st,$productPromotion,$pricePromotion,$quantity,$amountPromotion,$discountPromotion,$netPromotion,$target_pro) {
			
			//from wirat
			$objPos=new Model_PosGlobal();
			$doc_date=$objPos->checkDocDate(); 
			
			$corporation_id= $this->m_corporation_id;
			$company_id=$this->m_company_id ; 
			$branch_id=$this->m_branch_id;
			$com_ip=$this->m_com_ip;
			$doc_date=$doc_date;
			$doc_no="";

			
			$dataProduct=$this->dataProduct($productPromotion);
			$tax_type=$dataProduct[0]['tax_type'];
			$name_product=$dataProduct[0]['name_product'];
			$unit=$dataProduct[0]['unit'];
			

			
			if($target_pro=="AUTO"){
				$findDataOld="select * from trn_promotion_tmp0 where product_id='$productPromotion' and computer_ip='$this->m_com_ip' limit 1";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
			} else{
				$findDataOld="select * from trn_tdiary2_sl_val where computer_ip='$this->m_com_ip' limit 1";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl_val");
				
			}
			

			
			
			$dataOld = $this->db->fetchAll($findDataOld, 2);
			$doc_tp=$dataOld[0]['doc_tp'];
			$status_no=$dataOld[0]['status_no'];
			$member_percent1=$dataOld[0]['member_percent1'];
			$member_percent2=$dataOld[0]['member_percent2'];
			$stock_st=$dataOld[0]['stock_st'];
			$reg_user=$dataOld[0]['reg_user'];
			
			
			if($promo_code!=""){
				$dataProDetail=$this->seqPromotion($promo_code,$seq_pro,$productPromotion);
				$get_point=$dataProDetail[0]['get_point'];
				$cal_amt=$dataProDetail[0]['cal_amt'];
				$discount_member=$dataProDetail[0]['discount_member'];
				if($discount_member=="Y"){//ให้หักส่วนลดสมาชิกออก
					$member_discount1=($netPromotion*$member_percent1)/100; //ส่วนลดสมาชิก

				}else{//ไม่ต้องหัก
					$member_discount1=0;
				}
			}else{
				$get_point="";
				$cal_amt="";
				$discount_member="";
				$member_discount1=0;
			}

			
			
			
			
			if($promo_st=="Free"){
				$netPromotion=$netPromotion;
				$member_discount1=0;
			}else{
	
					$netPromotion=$netPromotion-$member_discount1;
	
			}
			
			$member_discount2=($netPromotion*$member_percent2)/100; //ส่วนลดสมาชิก2
			$netPromotion=$netPromotion-$member_discount2;
					
					
			$dataHead=$this->dataPromoHead($promo_code);
			$coupon=$dataHead[0]['coupon'];
			if($coupon=="S"){
				$dataBarcode=$this->hotprobarcode($this->m_com_ip);
				$scan_barcode=$dataBarcode[0]['scan_barcode'];
			}else{
				$scan_barcode="";
			}
			
			$dataPos=$this->dataPos();
			$member_no=$dataPos[0]['member_no'];
			
			
			$seq_no=$this->nextSeq('trn_promotion_tmp1','seq');
			$insert="insert trn_promotion_tmp1 set
						computer_no='$this->m_computer_no',
						computer_ip='$this->m_com_ip',
						corporation_id='$corporation_id',
						company_id='$company_id',
						branch_id='$branch_id',
						member_no='$member_no',
						doc_date='$doc_date',
						doc_time=time(now()),
						doc_no='$com_ip',
						doc_tp='$doc_tp',
						status_no='$status_no',
						seq='$seq_no',
						seq_set='$seq',
						promo_code='$promo_code',
						promo_seq='$seq_pro',
						promo_pos='$promo_pos',
						promo_tp='$promo_tp',
						promo_st='$promo_st',
						product_id='$productPromotion',
						name_product='$name_product',
						unit='$unit',
						price='$pricePromotion',
						quantity='$quantity',
						amount='$amountPromotion',
						discount='$discountPromotion',
						net_amt='$netPromotion',
						discount_member='$discount_member',
						member_discount1='$member_discount1',
						member_discount2='$member_discount2',
						get_point='$get_point',
						cal_amt='$cal_amt',
						tax_type='$tax_type',
						member_percent1='$member_percent1',
						member_percent2='$member_percent2',
						coupon_code='$scan_barcode',
						stock_st='$stock_st',
						reg_date=date(now()),
						reg_time=time(now()),
						reg_user='$reg_user',
						upd_date=date(now()),
						upd_time=time(now()),
						upd_user ='$reg_user',
						target_pro='$target_pro'
						";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
						$this->db->query($insert, 2);
						

		}
		
		
		function upStock($product_id,$quantity) {
			$up="
				update com_stock_master
				set
					onhand=onhand-$quantity
				where
					product_id='$product_id' and 
					month=month('$this->doc_date') and 
					year=year('$this->doc_date')
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			$result=$this->db->query($up, 2);
			if($result){
				return 1;
			}else{
				return 0;
			}
		}
		
		
		
		function chkCompare($compare,$priceChk){
			
			$sql="select max(price) as maxPrice from promo_map_select where computer_ip='$this->m_com_ip' and type_discount='Normal'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
			$result = $this->db->fetchAll($sql, 2);
			if($result && ($result[0]['maxPrice']>0 || $result[0]['maxPrice']!= null)){
				if($compare=="G" && $priceChk<=$result[0]['maxPrice']){
					return "price_ok";
				}else if($compare=="L" && $priceChk<$result[0]['maxPrice']){
					return "price_ok";
				}else if($compare=="N" || $compare==""){
					return "price_ok";
				}else{
					return "price_no";
				}
			}else{
				return "price_ok";
			}
		}
		
		function chkReat($product_id){
			
			$sql="select * from promo_map_select where computer_ip='$this->m_com_ip' and type_discount='Normal' and product_id='$product_id' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
			$result = $this->db->fetchAll($sql, 2);
			return count($result);
		}
		

		
		function chkSelectProduct($promo_code,$promo_seq,$product_id,$quantity){
				//chk เป็นสินค้าในโปรโมชั่นนี้หรือไม่่2
				$chk="
				select a.product_id,a.quantity,a.weight ,a.quantity*a.weight as qty
				from 
				promo_detail as a inner join promo_map_select as b
				on a.seq_pro=b.promo_seq and
				   a.product_id=b.product_id
				   
				 where a.promo_code='$promo_code' and a.seq_pro='$promo_seq' and a.product_id='$product_id' and b.computer_ip='$this->m_com_ip' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
				$data = $this->db->fetchAll($chk, 2);
				//echo $chk;
				if($data) {
					$qty=$data[0]['qty'];
					$weight=$data[0]['weight'];
					if($qty==$quantity*$weight){//คีย์เข้ามาครบแล้ว
						return "product_have";
					}else{//ยังไม่คีย์เข้ามา
						return "product_nohave";
					}
				}else{//ถ้ายังไม่คีย์เข้ามา
					return "product_nohave";
				}
		}
		
		function chkHaveInPro($promo_code,$promo_seq,$product_id){//chk ว่าเป็นสินค้าในโปรนี้หรือไม่
				$chk="
				select *
				from 
				 promo_detail 
				where 
				   promo_code='$promo_code' and
				   seq_pro='$promo_seq' and
				   (product_id='$product_id' or product_id='ALL')
				    ";
				//echo $chk;
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
				$data = $this->db->fetchAll($chk, 2);
				if($data){
					return "YesProductInPro";
				}else{
					return "NoProductInPro";
				}
		} 
		
		function addproductdetail($product_id,$quantity,$compare,$promo_code,$promo_seq) {
			$dataproduct=$this->dataProduct($product_id);
			if(empty($dataproduct)){
				return "no_product";
			}
			$price=$dataproduct[0]['price'];
			
			$dataStock=$this->dataStockProduct($product_id,$quantity);
			if(empty($dataStock)){
				return "stock_short";
			}
			
			//chk ว่าเป็นสินค้าในโปรนี้หรือไม่
			$chkHaveInPro=$this->chkHaveInPro($promo_code,$promo_seq,$product_id);
			if($chkHaveInPro=="NoProductInPro"){
				return "NoProductInPro";
			}


			//chk ก่อนว่ามียิงเข้ามาหรือยัง
			$chkHaveSelect=$this->chkSelectProduct($promo_code,$promo_seq,$product_id,$quantity);
			if($chkHaveSelect=="product_have"){
				return "product_have";
			}
			
			$maxSeq="select max(seq) as maxseq from trn_promotion_tmp0 where computer_ip='$this->m_com_ip' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
			$data=$this->db->fetchAll($maxSeq, 2);
			if($data){
				$maxseq=$data[0]['maxseq'];
				$maxseq++;
			}else {
				$maxseq=1;
			}
			
			
			$chkCompare=$this->chkCompare($compare,$price);
			if($chkCompare=="price_no"){
				return "price_no";
			}
			
			$dataProHead=$this->dataPromoHead($promo_code);
			$check_repeat=$dataProHead[0]['check_repeat'];
			
			$dataDetailPro=$this->seqPromotion($promo_code,$promo_seq,$product_id);
			$type_discount=$dataDetailPro[0]['type_discount'];
			if($check_repeat=="Y" && $type_discount!="Normal"){
				$chk=$this->chkReat($product_id);
				if($chk>0){
					return "product_dup";
				}
			}
			
		    $x=0;
			for($i=1; $i<=$quantity; $i++){
				$insert="insert trn_promotion_tmp0 set
							seq='$maxseq',
							product_id='$product_id',
							price='$price',
							quantity='1',
							amount='$price',
							net_amt='$price',
							reg_date=date(now()),
							reg_time=time(now()),
							reg_user='joke',
							upd_date=date(now()),
							upd_time=time(now()),
							upd_user ='joke'
							";
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
				$result=$this->db->query($insert, 2);
				if($result){
					$this->upStock($product_id,$quantity);
					$maxseq++;
					$x++;
				}
				
			}//end loop
			if($x==$quantity){
				return 1;
			}else{
				return 0;
			}
		}
		
		
		
		
		
		
		function playPro($doc_noSend,$typePro) { //ไม่มีโปร
			
			
			//list product
			$result=$this->list_product($doc_noSend); 

            //no and one promotion	
			foreach($result as $list_product){
				$quantity=$list_product['quantity'];
				$this->insertPro($doc_noSend,$list_product['seq'],"","","","","",$list_product['product_id'],$list_product['price'],$quantity,$list_product['amount'],$list_product['discount'],$list_product['net_amt'],'AUTO');
				$this->markList($doc_noSend,$list_product['seq']);
            }
            
		}
		
		
		
		function dataProHead($promo_code) {
			 $dataPro="select * from promo_head where promo_code='$promo_code' ";
			 $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			 $data=$this->db->fetchAll($dataPro, 2);
			 return $data;
		
		}
		
		
		
		
		function cutList($product_id,$quantity) {
			for($i=1; $i<=$quantity; $i++){
				$id="
					select id 
					from 
					 trn_promotion_tmp0 
					where 
					 product_id='$product_id' and
					 promo_code='' and 
					 quantity-allocate_pro>0
					order by 
					 seq 
					limit 1 
				  ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
				$rid=$this->db->fetchAll($id, 2);
				if($rid){
					$id=$rid[0]['id'];

					$upList="
					update trn_promotion_tmp0
					set
						allocate_pro=allocate_pro+1
					where
						id='$id'
					";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
					$this->db->query($upList, 2);
					//echo "$upList\n";
				}
				
			}
		}
		
		
		function stepSms($sms){
			$maxSeq="select max(seq) as maxSeq from promo_step";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_step");
			$dmax=$this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dmax[0]['maxSeq']+1;
			
			$sql="insert into promo_step(seq,sms) values('$maxSeq','$sms')";
			$this->db->query($sql, 2);
		}
		

		
		
		function selectPro($doc_noSend) {
			
			//สินค้าที่มีหลายโปร
		   $sql="
			   select a.* 
			   from trn_promotion_tmp0 as a inner join (select distinct product_id from promo_detail) as b
			   on a.product_id=b.product_id 
			   where 
			   a.doc_no='$doc_noSend' 
			   and a.promo_code='' 
			   and a.promo_pos='' 
			   order by 
			   a.price desc
		   ";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
	 	   $result = $this->db->fetchAll($sql, 2);
	 	   foreach($result as $data){//วนหาสินค้าที่มีหลายโปร
					$this->stepSms("More Promotion");
					
					$product_id=$data['product_id'];
					$seq=$data['seq'];
					$quantity=$data['quantity'];
					$price=$data['price'];
					$chk_promotion=$this->chk_promotion($product_id);
						if($chk_promotion>1) {//หลายโปร
							$dataPromotion=$this->dataPromotion($product_id);

							$rows=array();
				             foreach($dataPromotion as $data){
								$dataPromoHead=$this->dataPromoHead($data['promo_code']);
								
								$proName=$dataPromoHead[0]['promo_des'];          	
				                array_push($rows,array("promo_code"=>$data['promo_code'],"proName"=>$proName,"product_id"=>$product_id,"seq"=>$seq,"price"=>$price));  
				             }
				           
				           return json_encode($rows);
						}
						
	            
            }//end foreah
            
            
			//สินค้าที่มีโปรเดียว
		   $sql="
			   select a.* 
			   from trn_promotion_tmp0 as a inner join (select distinct product_id from promo_detail) as b
			   on a.product_id=b.product_id 
			   where 
			   a.doc_no='$doc_noSend' 
			   and a.promo_code='' 
			   and a.promo_pos='' 
			   order by 
			   a.price desc
		   ";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
	 	   $result = $this->db->fetchAll($sql, 2);
	 	   foreach($result as $data){//วนหาสินค้าที่มีโปรเดียว
					$this->stepSms("One Promotion");
					
					$product_id=$data['product_id'];
					$seq=$data['seq'];
					$quantity=$data['quantity'];
					$price=$data['price'];
					$chk_promotion=$this->chk_promotion($product_id);
						if($chk_promotion>0) {//1โปร
							$dataPromotion=$this->dataPromotion($product_id);

							$rows=array();
				             foreach($dataPromotion as $data){
								$dataPromoHead=$this->dataPromoHead($data['promo_code']);
								//print_r($dataPromoHead);
								$proName=$dataPromoHead[0]['promo_des'];          	
				                array_push($rows,array("promo_code"=>$data['promo_code'],"proName"=>$proName,"product_id"=>$product_id,"seq"=>$seq,"price"=>$price));  
				             }
				            
				           return json_encode($rows);
						}
	            
            }//end foreah
            
            $rows=array();
            array_push($rows,array("promo_code"=>'',"proName"=>'',"product_id"=>'',"seq"=>'',"price"=>''));  
            return json_encode($rows);//บอกว่าหมดโปรแล้ว
              
		}//end func
		
		
		function clearAllocate_pro() {
			$clear_allocate="update trn_promotion_tmp0 set allocate_pro=0 and promo_code=''";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
			$this->db->query($clear_allocate, 2);
			
		}
		
		function productNormal(){
			$sql="select * from promo_map_select where type_discount='Normal' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
			$result = $this->db->fetchAll($sql, 2);
			if($result){
				$in="not in(";
				foreach ($result as $data){
					$product_id=$data['product_id'];
					$in=$in."'".$product_id."',";
				}
				$in=substr($in,0,strlen($in)-1).")";
				$chkRepeat=" and product_id $in ";
			}else{
				$chkRepeat="";
			}
			
			return $chkRepeat;
			
		}
		
		
		function AutoQtyChkWeight($quantityPro,$promo_seq) {
	 		  $dataScan="select sum(quantity) as quantity from promo_map_select where promo_seq='$promo_seq'";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 	   	  if($dataScan){
	 	   	  	$quantity=$dataScan[0]['quantity'];
	 	   	  	if($quantityPro==$quantity){
	 	   	  		//echo "ok\n";
	 	   	  		return "weight_ok";
	 	   	  	}else{
	 	   	  		//echo "no\n";
	 	   	  		return "weight_no";
	 	   	  	}
	 	   	  	
	 	   	  }
		}
		
		
		function numProduct($doc_noSend,$promo_code,$product_id) {
			
			//clear promo_map_seqerr
			$this->db->query("delete from promo_map_seqerr", 2);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seqerr");
			$this->stepSms("Clear Tbl promo_map_seqerr In numProduct");
			
			$this->keepComparePrice("",0);
			$this->stepSms("Clear Tbl promo_keep_compareprice In numProduct");
			
			$this->db->query("delete from promo_map_select ", 2);
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seqerr");
			$this->stepSms("Clear Tbl promo_map_select In numProduct");

			
		    $this->clearAllocate_pro();
		    $this->stepSms("Clear allocate_pro last In selectPro");
		   
		   
			$chkType="select min(seq_pro) as minSeq from promo_detail where promo_code='$promo_code' and product_id='$product_id'  ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
			$dataChkType=$this->db->fetchAll($chkType, 2);
			$seqThis=$dataChkType[0]['minSeq'];

			
			$maxSeq="select distinct seq_pro,quantity,type_discount from promo_detail where promo_code='$promo_code'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
			$dataMaxSeq=$this->db->fetchAll($maxSeq, 2);
			$maxSeq=count($dataMaxSeq);


			$dataProHead=$this->dataProHead($promo_code);
		 	$compare=$dataProHead[0]['compare']; //เปรียบเทียบราคาตัวซื้อกับตัวแถม
		 	$check_repeat=$dataProHead[0]['check_repeat']; //เปรียบเทียบรหัสสินค้าตัวซื้อกับตัวแถม

			//รายการที่เลือกมา add ก่อนเลย
			$dataProductThis=$this->dataProduct($product_id);
			$priceThis=$dataProductThis[0]['price'];
			$dataPro=$this->seqPromotion($promo_code,$seqThis,$product_id);
			$this_type_discount=$dataPro[0]['type_discount'];
			$this_quantity=$dataPro[0]['quantity'];
			$this_weight=$dataPro[0]['weight'];

			//$crSqlWeight="a.weight>0 and a.weight<1 and";
			
			
	    	$this->addItemSelect($promo_code,$maxSeq,$seqThis,$product_id,$priceThis,$this_type_discount,$this_quantity,$this_weight);
			$this->cutList($product_id,$this_quantity);
			//echo "x";

			//เก็บ Seq ไว้ Loop
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seq");
			$this->db->query("delete from promo_map_seq", 2);
			for($a=1; $a<=$maxSeq; $a++){
				$keepSeq="insert into promo_map_seq(promo_seq) values('$a')";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seq");
				$this->db->query($keepSeq, 2);
			}

			
			//chk ว่า Seq นี้ครบ weigth หรือยัง
			$chkWeight=$this->AutoQtyChkWeight($this_quantity,$seqThis);
			if($chkWeight=="weight_ok"){//ครบweight
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seq");
				$this->db->query("delete from promo_map_seq where promo_seq='$seqThis' ", 2);
			}
			
			
			$numChk=1;
			$seqProBefor=1;
			$dataSeq="select promo_seq from promo_map_seq order by promo_seq";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seq");
			$dataSeq = $this->db->fetchAll($dataSeq, 2);
			foreach ($dataSeq as $dataSeq){
					$seqSelect=$dataSeq['promo_seq'];
					$dataProByseq="select distinct quantity,type_discount 
					from promo_detail 
					where promo_code='$promo_code' and seq_pro='$seqSelect'";
					

					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
					$dataProByseq=$this->db->fetchAll($dataProByseq, 2);
					$seqPro=$seqSelect;
					$quantityPro=$dataProByseq[0]['quantity'];
					$type_discount=$dataProByseq[0]['type_discount'];
					

					


					//เช็คราคา
					$chkCompare="";
					$chkRepeat="";
					if((strtolower($type_discount)=="free") || (strtolower($type_discount)=="price") || (strtolower($type_discount)=="percent")) { //ถ้าเป็นตัวแถมหรือลดราคา
						$sqlMaxPrice="select max(price) as maxPrice from promo_map_select where type_discount='normal'";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
						$dataMaxPrice=$this->db->fetchAll($sqlMaxPrice, 2);
						if($dataMaxPrice){
							if( $dataMaxPrice[0]['maxPrice']== null) {
								$maxPrice=0;
							}else{
								$maxPrice=$dataMaxPrice[0]['maxPrice'];
							}
						} else {
							$maxPrice=0;
						}
						
						if(strtolower($compare)=="l") {
							$chkCompare=" and price<'$maxPrice' ";
						}
						if(strtolower($compare)=="g") {
							$chkCompare=" and price<='$maxPrice' ";
						}
						
						
						//เช็คสินค้าห้ามซ้ำกับตัวซื้อ
						if($check_repeat=="Y"){//ห้ามซ้ำกับตัวซื้อ
							$chkRepeat=$this->productNormal();
						}else{
							$chkRepeat="";
						}
						
					} else {//ถ้าเป็นตัวหลัก
						$chkCompare="";
					}
					
					
	
						
						
					$chk="
					   select 
	                        a.promo_code,
							a.product_id,
							a.seq_pro,
							a.quantity,
							a.weight,
							a.type_discount,
							a.discount
						from 
							promo_detail as a inner join (
									select 
										product_id,
										price,
										sum(quantity-allocate_pro) as quantity 
									from 
										trn_promotion_tmp0 
									where 
										promo_code=''  $chkCompare $chkRepeat
									group by 
										product_id,price
									) as b
	                        on a.product_id=b.product_id
						where
							a.promo_code='$promo_code' and
							a.seq_pro='$seqPro' and
							
							b.quantity-a.quantity>=0
						order by b.price desc
						limit 1
						";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
					$rChk=$this->db->fetchAll($chk, 2); //ดึงมาทีละ Seq
					if($rChk){//ถ้ามีสักรายการก็ผ่าน
							$product_id=$rChk[0]['product_id'];
							$quantity=$rChk[0]['quantity'];
							$weight=$rChk[0]['weight'];
	
	
							$dataProduct=$this->dataProduct($product_id);
							$price=$dataProduct[0]['price'];
							
					    	$this->addItemSelect($promo_code,$maxSeq,$seqPro,$product_id,$price,$type_discount,$quantity,$weight);
							$this->cutList($product_id,$quantity);
							
							if($seqProBefor!=$seqPro){
								$numChk=$numChk+1; //เก็บค่าว่าผ่าน
							}
							
							//chk ว่า Seq นี้ครบ weigth หรือยัง
							$chkWeight=$this->AutoQtyChkWeight($quantityPro,$seqPro);
	
					}else{
						//เก็บ Seq pro ที่เล่นไม่ได้
						$keepSeqPro="
						insert into promo_map_seqerr(promo_code,promo_seq,quantity_short)
						values('$promo_code','$seqPro','$quantityPro')
						";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seqerr");
						$this->db->query($keepSeqPro, 2);
						
	
					}
							
							

					
				
					
					$seqProBefor=$seqPro;
				

					
			}//end loop
			//echo "$maxSeq=$numChk";
			if($maxSeq==$numChk) {//ถ้ามีครบ Step
				$chkPro="Y";
			} else {
				$chkPro="N";
			}
			
			$this->stepSms("Check Product For Promotion : $chkPro");

			return $chkPro;
		
			

		}//end function
		
		function detailPro($promo_code) {
		   $maxSeq=$this->maxPro($promo_code);
		   $genSql="";
		   for($i=1; $i<=$maxSeq; $i++){
		   		$chkProduct="select * from promo_map_select where promo_seq='$i'";
		   		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_seqerr");
		   		$dataProduct=$this->db->fetchAll($chkProduct, 2);
		   		//print_r($dataProduct);
		   		if($dataProduct){
		   			   $product_id=$dataProduct[0]['product_id'];
		   			   $dataPro=$this->seqPromotion($promo_code,$i,$product_id);
		   			   $weight=$dataPro[0]['weight'];
		   			   if($weight>0 && $weight<1){//มี Weight
		   			   		$product_id="%";
		   			   }
					   $sql="
						   select 
						   	a.promo_code,
						   	a.promo_des,
						   	a.compare,
						   	c.name_product,
						   	c.price,
						   	f.promo_seq,
						   	f.quantity_short,
						   	if(g.product_id is null,'N','Y') as chkSelect,
						   	b.* 
						   from 
						    promo_detail  as b left join promo_head as a
						    on a.promo_code=b.promo_code
						    
						    left join com_product_master as c
						    on b.product_id=c.product_id
						    
						    

						    
						    left join promo_map_seqerr as f
						    on b.seq_pro=f.promo_seq
						    
						    left join promo_map_select as g
						    on b.product_id=g.product_id and 
						       b.seq_pro=g.promo_seq
						       
						   where 
						    a.promo_code='$promo_code' and 
						    b.seq_pro='$i' and 
						    b.product_id like '$product_id' and 
						    '$this->doc_date' between a.start_date and a.end_date 
			
					   ";
		   		}else{
					   $sql="
						   select 
						   	a.promo_code,
						   	a.promo_des,
						   	a.compare,
						   	c.name_product,
						   	c.price,
					
						   	f.promo_seq,
						   	f.quantity_short,
						   	if(g.product_id is null,'N','Y') as chkSelect,
						   	b.* 
						   from 
						    promo_detail  as b left join promo_head as a
						    on a.promo_code=b.promo_code
						    
						    left join com_product_master as c
						    on b.product_id=c.product_id
						    
						    

						    
						    left join promo_map_seqerr as f
						    on b.seq_pro=f.promo_seq
						    
						    left join promo_map_select as g
						    on b.product_id=g.product_id and 
						       b.seq_pro=g.promo_seq
						    
						   where 
						    a.promo_code='$promo_code' and 
						    b.seq_pro='$i' and 
						    '$this->doc_date' between a.start_date and a.end_date 
			
					   ";
		   		}

		   		if($i==$maxSeq){
		   			$genSql=$genSql.$sql;
		   		}else{
		   			$genSql=$genSql . $sql." union all ";
		   		}

		   }
		   
		   //echo $genSql;
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
	 	   $result = $this->db->fetchAll($genSql, 2);
	 	   $this->stepSms("Show Detail Promotion");
	 	   return $result;

		}
		
		
		
		
		
		
		function addItemSelect($promo_code,$step_all,$promo_seq,$product_id,$price,$type_discount,$quantity,$weight) {
			   //add item
			   $quantity=$quantity*$weight;
			   $sql="
					insert promo_map_select set
					promo_code='$promo_code',
					step_all='$step_all',
					promo_seq='$promo_seq',
					product_id='$product_id',
					price='$price',
					type_discount='$type_discount',
					quantity='$quantity'
					
			   ";
			   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
		 	   $result = $this->db->query($sql, 2);
			
		}

		
		function keepComparePrice($sms,$maxprice){
			$sql="update promo_keep_compareprice set compareprice='$sms',maxprice='$maxprice'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_keep_compareprice");
			$result = $this->db->query($sql, 2);
			if($result){
				return 1;
			}else{
				return 0;
			}
		}
		

		
		
		
		
		
		
		function setproductnopro($product_id,$seq) {//เล่นโปร

			$update="
			update trn_promotion_tmp0 
			set promo_code='Y',
				promo_pos='Y'
			where
				product_id='$product_id' and 
				seq='$seq'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp0");
			$result = $this->db->query($update, 2);
			if($result) {
				return 1;
			}else {
				return 0;
			}


		}
		
		
		function addPro($doc_noSend,$promo_code,$seqOld) {//เล่นโปร
			$maxSeq="select max(seq_set) as maxSeq from trn_promotion_tmp1 WHERE computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
			
			$seqList='';
			$dataSelect="select * from promo_map_select order by promo_seq";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_map_select");
			$selectProduct=$this->db->query($dataSelect, 2);
			foreach($selectProduct as $dataSeq){
				$seqUse=$dataSeq['promo_seq'];
				$product_idUse=$dataSeq['product_id'];
				$price_idUse=$dataSeq['price'];
				$type_discount_idUse=$dataSeq['type_discount'];
				$quantity_idUse=$dataSeq['quantity'];

				
				//ข้อมูลจากโปรโมชั่น
				$seqPromotion=$this->seqPromotion($promo_code,$seqUse,$product_idUse);
				$seq_pro=$seqPromotion[0]['seq_pro'];
				$promo_pos=$seqPromotion[0]['promo_pos'];
				$promo_tp=$seqPromotion[0]['promo_tp'];	
				$quantityPro=$seqPromotion[0]['quantity'];	
				$type_discount=$seqPromotion[0]['type_discount'];
				$discount=$seqPromotion[0]['discount'];
				
				//คำนวนยอดเงินจากเงื่อนไขโปรโมชั่น
				$valNet=$this->caseNet($type_discount,$quantityPro,$product_idUse,$discount);
				$pricePromotion=$valNet['pricePromotion'];
				$amountPromotion=$valNet['amountPromotion'];
				$discountPromotion=$valNet['discountPromotion'];
				$netPromotion=$valNet['netPromotion'];
				
				//เก็บข้อมูลเล่นโปรลงตาราง
				$this->insertPro($doc_noSend,$maxSeq,$promo_code,$seq_pro,$promo_pos,$promo_tp,$type_discount,$product_idUse,$pricePromotion,$quantityPro,$amountPromotion,$discountPromotion,$netPromotion,'AUTO');
				$this->stepSms("Add List Promotion");
				
				
				//มาร์ครายการว่าเล่นไปแล้ว
				for($i=1; $i<=$quantityPro; $i++){
						//ข้อมูลที่ยิงเข้ามา
						$dataProductNull=$this->select_productNull($doc_noSend,$product_idUse);
						//print_r($dataProductNull);
						
						if($dataProductNull){
							$seqList=$dataProductNull[0]['seq'];
						}
						$this->markList($doc_noSend,$seqList); 
						
				}

			} //end foreach	

		}
		
		

		
		

		
		
		
		
		
		
		function clearTmp2() {
			
		   $sql="delete from  `trn_promotion_tmp2` where computer_ip='$this->m_com_ip' ";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   $result = $this->db->query($sql, 2);
		 	  if($result) {
		 	  	return 1;
		 	  } else {
		 	  	return 0;
		 	  }

		}
		
		
		
		function chkLimit_qty($promo_code){
			
			$dataScan=$this->datatmp2('1');
			$quantity=$dataScan[0]['quantity'];
			$sql="select * from promo_head where limite_qty>0 and promo_code='$promo_code' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$result = $this->db->fetchAll($sql, 2);
			if($result){
				$limite_qty=$result[0]['limite_qty'];
				
				$dataSeq1="select * from promo_detail where promo_code='$promo_code' and seq_pro='1' limit 0,1";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
				$dataSeq1 = $this->db->fetchAll($dataSeq1, 2);
				$quantitySeq1=$dataSeq1[0]['quantity'];
			
				
				$maxQuantity=$limite_qty*$quantitySeq1;//ต้องได้ไม่เกินนี้
						
				$sum="select sum(quantity) as sumQuantity from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and promo_code='$promo_code' and promo_seq='1'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				$dataSum = $this->db->fetchAll($sum, 2);
				if($dataSum){
					$sumQty=$dataSum[0]['sumQuantity'];

				}else{
					$sumQty=0;
				}
				
					if(($quantity+$sumQty)>$maxQuantity){
						$limit_yes=$limite_qty-$sumQty;
						return "limite_falseX#X$limit_yes";
					}else{
						return "Yes";
					}
			}else{
				return 'Yes';
				
			}
		}
		
		
		
		function chkbarcode($promo_code,$scan_barcode){
			
			$sql="select * from promo_barcode where  promo_code='$promo_code' and barcode='$scan_barcode' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_barcode");
			$result = $this->db->fetchAll($sql, 2);
			if($result){
				$chk="select * from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and coupon_code='$scan_barcode'  ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				$rChk = $this->db->fetchAll($chk, 2);
				if($rChk){//ถ้าเคยเล่นไปแล้วในบิลนี้
					return "play_have";
				}else{
					return "Y";
				}
			}else {
				return "N";
			}
		}
		
		function dataPos(){
			$sql="select * from trn_tdiary2_sl_val WHERE computer_ip='$this->m_com_ip' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl_val");
				$r = $this->db->fetchAll($sql, 2);
				return $r;
			
		}
		
		
		function chkNewmember($member_no){
			$sql="select * from member_history where member_no='$member_no' and apply_date='$this->doc_date'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
				$r = $this->db->fetchAll($sql, 2);
				if($r){
					$new_member="Y";
				}else{
					$new_member="N";
				}
				return $new_member;
			
		}
		function chkNewmemberFromBill($member_no){
			$sql="select * from trn_diary1 where member_id='$member_no' and doc_date='$this->doc_date' and status_no='01' and application_id in('OPPN300','OPPS300') and flag<>'C'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("crm","crm_card");
				$r = $this->db->fetchAll($sql, 2);
				if($r){
					$new_member="Y";
				}else{
					$new_member="N";
				}
				return $new_member;
			
		}
		
		
		
		
		//Manual
		function dataHotPro($hotpro_product_search,$hotpro_quantity_search) {
			$branch_id=$this->m_branch_id;
			
			$dataPos=$this->dataPos();
			$member_no=$dataPos[0]['member_no'];
			$status_no=$dataPos[0]['status_no'];
			
			
			$new_member=$this->chkNewmember($member_no);
			

			if($member_no==""){//ไม่ใช่สมาชิก
				$chk_member_tp="and a.member_tp='N'";
			}else if($member_no!="" && $new_member=="N"){//สมาชิกเก่า
				$chk_member_tp="and a.member_tp in('N','Y')";
			}else if($member_no!="" && $new_member=="Y"){//สมาชิกพึ่งสมัครใหม่ จะเล่น NM ได้ด้วย
				$chk_member_tp="and a.member_tp in('N','Y','NM')";
			}
			
			if($member_no!="" && substr($member_no,0,2)==15){//สำหรับสมาชิก OPT
				if($new_member=="Y"){
					$chk_member_tp="and a.member_tp in('N','Y','OPT')";
				}else{
					$chk_member_tp="and a.member_tp in('N','Y','NM','OPT')";
				}
			}
			
						
		
		   $chk_status_bill="select * from promo_set where status_no='$status_no'";
		   
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_set");
	 	   $result_chk_status_bill = $this->db->fetchAll($chk_status_bill, 2);
	 	   if($result_chk_status_bill){
	 	   		
	 	   	   $num_step=$result_chk_status_bill[0]['num_step'];
	 	   	   $promo_pos=$result_chk_status_bill[0]['promo_pos'];
	 	   	   $promo_seq=$result_chk_status_bill[0]['promo_seq'];
	 	   	   $type_discount=$result_chk_status_bill[0]['type_discount'];
	 	   	   $discount=$result_chk_status_bill[0]['discount'];
	 	   	   
			   $sql="
					   select 
					   		a.promo_code,a.promo_des,c.quantity
					   from 
						  promo_head  as a inner join promo_branch as b
						  on a.promo_code=b.promo_code
						  
						  inner join promo_detail as c
						  on a.promo_code=c.promo_code
						  
						  inner join (
									SELECT x.promo_code
									FROM 
									`promo_detail` as x inner join (
									SELECT promo_code   FROM `promo_detail` WHERE promo_pos='$promo_pos'  group by promo_code
									having count(distinct seq_pro)='$num_step'
									) as y
									on x.promo_code=y.promo_code
									WHERE
									 x.promo_pos='$promo_pos'  and
									 if(x.seq_pro='$promo_seq' and x.type_discount='$type_discount','Y','N')='Y'
									group by x.promo_code
						  ) as e
						  on a.promo_code=e.promo_code
						  
					   where
					     '$this->doc_date' between a.start_date and a.end_date and
					     a.promo_pos='M' 
						 $chk_member_tp
						 and '$this->doc_date' between b.start_date and b.end_date and
						 b.branch_id in('ALL','$branch_id') and
						 c.product_id in('$hotpro_product_search','ALL') and
						 c.seq_pro=1 and
						 a.promo_code not in(
									SELECT 
									distinct a.promo_code
									  FROM promo_branch as a inner join (
											SELECT xx.promo_code,sum(xx.quantity) as sumqty from 
											(
											select promo_code,quantity from trn_diary2 where flag<>'C' and product_id='23567'
											union all
											select promo_code,quantity from trn_promotion_tmp1 where product_id='23567' and doc_no='$this->m_com_ip'
											) as xx
									  
									  ) as b
									on a.promo_code=b.promo_code
									WHERE 
									a.limit_quantity>0 and
									b.sumqty>=a.limit_quantity
						 )
						 
	
						 
	  				   group by 
					   	 a.promo_code,a.promo_des 
					   order by 
					   	 a.promo_code,a.promo_des
					 
			   ";
			  
			   
	 	   }else{
	 	   
			   $sql="
					   select 
					   		a.promo_code,a.promo_des,c.quantity
					   from 
						  promo_head  as a inner join promo_branch as b
						  on a.promo_code=b.promo_code
						  
						  inner join promo_detail as c
						  on a.promo_code=c.promo_code
						  
					   where
					     '$this->doc_date' between a.start_date and a.end_date and
					     a.promo_pos='M' 
						 $chk_member_tp
						 and '$this->doc_date' between b.start_date and b.end_date and
						 b.branch_id in('ALL','$branch_id') and
						 c.product_id in('$hotpro_product_search','ALL') and
						 c.seq_pro=1 and
						 a.promo_code not in(
									SELECT 
									distinct a.promo_code
									  FROM promo_branch as a inner join (
											SELECT xx.promo_code,sum(xx.quantity) as sumqty from 
											(
											select promo_code,quantity from trn_diary2 where flag<>'C' and product_id='23567'
											union all
											select promo_code,quantity from trn_promotion_tmp1 where product_id='23567' and doc_no='$this->m_com_ip'
											) as xx
									  
									  ) as b
									on a.promo_code=b.promo_code
									WHERE 
									a.limit_quantity>0 and
									b.sumqty>=a.limit_quantity
						 )
						 
	  				   group by 
					   	 a.promo_code,a.promo_des 
					   order by 
					   	 a.promo_code,a.promo_des
					 
			   ";
	
	 	   }
	 	   
			//echo "$sql";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
	 	   $result = $this->db->fetchAll($sql, 2);

	 	   if($result){//มีโปร
	 	   	 $dataProduct=$this->dataProduct($hotpro_product_search);
	 	   	 $price=$dataProduct[0]['price'];
	 	   
	 	   	 $insert="insert into trn_promotion_tmp2(computer_no,computer_ip,promo_seq,product_id,price,type_discount,quantity) values('$this->m_computer_no','$this->m_com_ip','1','$hotpro_product_search','$price','Normal','$hotpro_quantity_search')";
			 $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   	 $x = $this->db->query($insert, 2);

			 
	 	   }else{//ไม่มีโปร
	 	   		$this->insertNoPro('',$hotpro_product_search,$hotpro_quantity_search);
	 	   }
	 	   $this->upStock($hotpro_product_search,$hotpro_quantity_search);
	 	   
	 	   return $result;
	 	  
		}
		
		

		function maxPro($promo_code) {
			
		   $sql="
			   select max(seq_pro) as maxPro
					
			   from 
			    promo_detail 

			   where 
			    promo_code='$promo_code' 
		   ";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
	 	   $result = $this->db->fetchAll($sql, 2);
	 	   $maxPro=$result[0]['maxPro'];
	 	   return $maxPro;
		}
		

		function dataDetailHotPro($promo_code,$promo_seq) {

			
			
				 	    $sqlPromoDetail="
					 	    select 
					 	     b.promo_des,b.compare,a.* 
					 	    from 
					 	     promo_detail as a inner join promo_head as b 
					 	     on a.promo_code=b.promo_code
					 	    where 
					 	     a.promo_code='$promo_code' and
					 	     a.seq_pro='$promo_seq' 
					 	     limit 1
				 	     ";
				 	 
				 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
				 	    $promoDetail = $this->db->fetchAll($sqlPromoDetail, 2);
	 	   				return $promoDetail;
	 	   
	 	   
		  
		}
		
		
		function setQty($promo_code,$doc_no){
			
				$findPro="select * from promo_detail where promo_code='$promo_code' and seq_pro='1' limit 1";
		 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
		 	    $pro = $this->db->fetchAll($findPro, 2);
		 	    $quantitySeq1=$pro[0]['quantity'];
		 	    
		 	    
				$sumQty="select sum(quantity) as quantityScan from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' AND promo_seq='1' ";
		 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		 	    $sumQty = $this->db->fetchAll($sumQty, 2);
		 	    if($sumQty){
		 	    	$sumSeq1=$sumQty[0]['quantityScan'];
		 	    }else{
		 	    	$sumSeq1=0;
		 	    }
		 	    
				$config=$this->hotproconfig($doc_no);	
				$set_quantity=$config[0]['set_quantity'];
				$max_quantity=$quantitySeq1*$set_quantity;

				if($sumSeq1<$max_quantity){
					$startSeq=1;
				}else{
					$startSeq=2;
				}
				return $startSeq;
		}
		function dataScanHotPro() {
   		   		$scan="
   		   		select 
   		   			a.*,b.name_product ,c.discount
   		   		from 
   		   			trn_promotion_tmp2 as a inner join com_product_master as b
   		   			on a.product_id=b.product_id 
   		   		
   		   		inner join promo_detail as c
   		   		on 
	   		   		a.promo_code=c.promo_code and
	   		   		a.promo_seq=c.seq_pro and
                    ((a.product_id=c.product_id) or (c.product_id='ALL'))
                WHERE
                	a.computer_ip='$this->m_com_ip'                        
   		   		order by 
   		   		promo_seq
   		   		";
   		   		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		   		$dataScan=$this->db->fetchAll($scan, 2);
   		   
		   
	 	   return $dataScan;
		}
		
		
		
		
		function dataScanHotPro_auto() {
   		   		$scan="
   		   		select 
   		   			a.*,b.name_product
   		   		from 
   		   			trn_promotion_tmp_auto as a inner join com_product_master as b
   		   			on a.product_id=b.product_id 
   		   		

                WHERE
                	a.computer_ip='$this->m_com_ip'                        
   		   		order by 
   		   		 a.id
   		   		
   		   		";
   		   		
   		   		//echo $scan;
   		   		$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
		   		$dataScan=$this->db->fetchAll($scan, 2);
   		   
		   
	 	   return $dataScan;
		}

		
		
		
		function hotproconfig($doc_no){
			$sql="select * from trn_promotion_config where computer_ip='$this->m_com_ip' ";
		
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_config");
			$result=$this->db->fetchAll($sql, 2);
			return $result;
		}
		
		function hotprobarcode($computer_ip){
			$sql="select * from trn_promotion_chkbarcode where computer_ip='$computer_ip' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_chkbarcode");
			$result=$this->db->fetchAll($sql, 2);
			return $result;
		}
		
		
		
		
		function runproauto($promo_code,$step){
			$step=2;
			$tmp="
				select * from trn_promotion_tmp_auto  
				where 
					computer_ip='$this->m_com_ip' 

			 ";
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
			$dataTmp=$this->db->fetchAll($tmp, 2);
			$set_pro=ceil(count($dataTmp)/$step);
			//echo "Loop H ".$set_pro ."<br>";
			
			
			
			$maxSeq="select max(seq_set) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
			

			$seq_set=$maxSeq;
			for($h=1; $h<=$set_pro; $h++){
					//echo "h=" .$h ."--------------------------\n";
					for($x=1; $x<=$step; $x++){//วนหาว่าสินค้านี้อยู่ใน Seq ไหน
						//echo "x=" .$x ."--------------------------\n";
						$rowsAll="
							select * from trn_promotion_tmp_auto  
							where 
								computer_ip='$this->m_com_ip'  and seq_set  not in('3000','5000') 
						 ";
						
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
						$dataRowsAll=$this->db->fetchAll($rowsAll, 2);
						
						for($loop=1; $loop<=count($dataRowsAll); $loop++){
							//echo "loop=" .$loop ."--------------------------\n";
							$up_seq="
								select * from trn_promotion_tmp_auto  
								where 
									computer_ip='$this->m_com_ip'  and seq_set  not in('3000','5000') 
								order by
									price desc
								limit 1
							 ";
							
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
							$result=$this->db->fetchAll($up_seq, 2);
							$id=$result[0]['id'];
							$product_id=$result[0]['product_id'];
						
						
							$chk_type="
								 select 
								 	seq_pro,type_discount,discount
								 from 
								 	promo_detail 
								 where 
									 	promo_code='$promo_code'
									 	and seq_pro='$x'
									 	and product_id='$product_id'
									 group by 
									 	seq_pro,type_discount,discount
					
								";
								//echo  $chk_type .  "\n";
								$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","promo_detail");
								$dataPro=$this->db->fetchAll($chk_type, 2);
								if($dataPro){//หาได้แล้ว
									$type_discount=$dataPro[0]['type_discount'];
			
									$up="update trn_promotion_tmp_auto 
									set
										promo_code='$promo_code',
										seq_set='5000',
										promo_seq='$x',
										type_discount='$type_discount'
									where
										id='$id'
									";
									echo  $up .  "\n";
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
									$this->db->query($up, 2);
									$loop=count($dataRowsAll)+1;
									
								}else{//หาต่อ
									$up="update trn_promotion_tmp_auto 
									set
										seq_set='3000'
			
									where
										id='$id'
									";
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
									$this->db->query($up, 2);
									
								}
						}//end loop
		
						$clear="update trn_promotion_tmp_auto 
						set
							seq_set='0'
	
						where
							seq_set='3000'
						";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
						$this->db->query($clear, 2);
		
		
						
					}//end x
					$clear="update trn_promotion_tmp_auto 
					set
						seq_set='0'

					where
						seq_set='3000'
					";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
					$this->db->query($clear, 2);
					
			}//end h
			
			
			//up seq_set
			$up_set="
			select * from trn_promotion_tmp_auto  
			where 
				computer_ip='$this->m_com_ip' and promo_seq='1'
			order by
			  price desc
			
			 ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
			$result=$this->db->fetchAll($up_set, 2);
		
			foreach($result as $data){
				$promo_seq=$data['promo_seq'];
				for($y=1; $y<=$step; $y++){
					
						$find_id="select * from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip' and seq_set='5000' and promo_seq='$y' order by price desc limit 1 ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
						$dataID=$this->db->fetchAll($find_id, 2);
						$idup=$dataID[0]['id'];
						
						$upset="update trn_promotion_tmp_auto 
						set seq_set='$seq_set'	where id='$idup'
						";
						
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
						$this->db->query($upset, 2);
					
					
				}
				$seq_set=$seq_set+1;
			}
			
			//up เศษ
			$upset="update trn_promotion_tmp_auto 
			set seq_set='1000'	where seq_set in('0','3000') and computer_ip = '$this->m_com_ip'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
			$this->db->query($upset, 2);
			
			

			$upset="
				update trn_promotion_tmp_auto as a inner join (
				SELECT seq_set AS dd
				FROM `trn_promotion_tmp_auto`
				WHERE 
				computer_ip = '$this->m_com_ip'
				GROUP BY seq_set
				HAVING count(  seq_set ) =1
				) as b
				on a.seq_set=b.dd
							set a.seq_set='1000'	
				where a.computer_ip = '$this->m_com_ip'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
			$this->db->query($upset, 2);
			
			
			$this->addpro_auto("AutoScan");
			
			
			$clear_tmp="delete from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
			$this->db->query($clear_tmp, 2);
			
			

			
			
		}
		
		
		function delallauto($keepid){
			
			$keepid=substr($keepid,1);
			$arr_id=explode(",",$keepid);
			$count_arr=count($arr_id);
			
			for($i=0; $i<$count_arr; $i++){
				$id=$arr_id[$i];
				
				$find="select * from trn_promotion_tmp_auto where id='$id' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
				$data=$this->db->fetchAll($find, 2);
				$product_id=$data[0]['product_id'];
				$quantity=$data[0]['quantity'];
				
				
				$upStock="
				update com_stock_master
				set
				onhand=onhand+$quantity
	
				where 
				product_id='$product_id'
	 	    	and month=month('$this->doc_date') and
	 	    	 year=year('$this->doc_date')
	 	    	";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
				$this->db->query($upStock, 2);

				
				$del="delete from trn_promotion_tmp_auto where  id='$id' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
				$this->db->query($del, 2);
			}
			
			
			
		}
		
		
		
		function findProductReserve($promo_code,$promo_seq,$balance_qty,$product_id,$quantity){
			$sumChk="select sum(b.onhand) as sumChk from 
				promo_detail as a inner join com_stock_master as b
				on a.product_id=b.product_id
				where
				a.promo_code='$promo_code'
				and a.seq_pro='$promo_seq'
				and a.product_st='P'
				and b.month=month('$this->doc_date')
				and b.year=year('$this->doc_date')
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			$result=$this->db->fetchAll($sumChk, 2);
			if($result){
				$sumChk=$result[0]['sumChk'];
			}else{
				$sumChk=0;
			}
			
			
			if($sumChk<=$balance_qty){//ถ้าสินค้าในโปรไม่พอ
				$find="
				select a.product_id,b.onhand from 
				promo_detail as a inner join com_stock_master as b
				on a.product_id=b.product_id
				where
				a.promo_code='$promo_code'
				and a.seq_pro='$promo_seq'
				and a.product_st='O'
				and b.month=month('$this->doc_date')
				and b.year=year('$this->doc_date')
				order by seq_order
				limit 0,1
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
				$dataFind=$this->db->fetchAll($find, 2);
				if($dataFind){
					$product_id=$dataFind[0]['product_id'];
					
				}else{
					$product_id='';
				}
				return $product_id;
			}else{//มีสินค้าอื่นที่พออยู่
				return "Next_product";//ให้หาตัวใหม่มา
			}
			
			
			
			
		}
		
		
		function addProduct($promo_code,$promo_seq,$product_id,$quantity) {
				$chkProduct=$this->dataProduct($product_id);
				if(empty($chkProduct)){
					$chkBarcode=$this->chkBarcodeProduct($product_id);
					if($chkBarcode){
						$product_id=$chkBarcode[0]['product_id'];
					}else{
						return "Noproduct";
					}
				}
				
				$chkStock=$this->dataStockProduct($product_id,$quantity);
				if(empty($chkStock)){
					//chk ว่าให้ไปเอาสินค้าทดแทดนหรือไม่
					$dataDetail=$this->seqDetailPromotionLimit($promo_code,$promo_seq);
					$order_st=$dataDetail[0]['order_st'];
					if($order_st=="Y"){//ให้ไปหาสินค้าทดแทด
						$balance_qty=$dataDetail[0]['balance_qty'];
						$chkReserve=$this->findProductReserve($promo_code,$promo_seq,$balance_qty,$product_id,$quantity);
						if($chkReserve=="Next_product"){//ไปหาตัวอื่นดิ
							return "stock_short";
						}else if($chkReserve==""){//แม้แต่ทดแทนก็ไม่มีให้
							return "stock_short";
						}else if($chkReserve!="" && $chkReserve!="Next_product"){//มีสินค้าทดแทนให้
							$product_id=$chkReserve;
						}
					}else{
						return "stock_short";
					}
					
				}
				
				
		 	    $sqlPromoDetail="select * from promo_detail where promo_code='$promo_code' and seq_pro='$promo_seq' limit 1";
		 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
		 	    $promoDetail = $this->db->fetchAll($sqlPromoDetail, 2);
				$promo_product_id=$promoDetail[0]['product_id'];
				if($promo_product_id=="ALL"){//ถ้าเป็นตัวไหนก็ได้
					$type_discount=$promoDetail[0]['type_discount'];
					$quantityPro=$promoDetail[0]['quantity'];
					$weightPro=$promoDetail[0]['weight'];
					$check_double=$promoDetail[0]['check_double'];
				}else{
					//chk เป็นสินค้าในโปรโมชั่นนี้หรือไม่่2
					$chk="
					select 
					  * 
					from 
					  promo_detail 
					 where 
					  promo_code='$promo_code' and
					  seq_pro='$promo_seq' and
					  (product_id='$product_id' or product_id='ALL') ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
					$dataProdetail = $this->db->fetchAll($chk, 2);
					if(empty($dataProdetail)) {
						return 2;
					}
					$type_discount=$dataProdetail[0]['type_discount'];
					$quantityPro=$dataProdetail[0]['quantity'];
					$weightPro=$dataProdetail[0]['weight'];
					$check_double=$dataProdetail[0]['check_double'];
				}
				

				$quantity=$quantity*$weightPro;
				
				
				//--------------------------------
				//chk ว่ายิงมาเกินหรือเปล่า
			   $chkScan=$this->quantityScan($quantityPro,$quantity,$promo_code,$promo_seq);
				if($chkScan=="4"){
					return 4;
				}

	 	   	   //--------------------------------
		 	   	  
	 	   	   
	 	   	   
				//data head pro
				$dataProHead=$this->dataProHead($promo_code);
				$compare=$dataProHead[0]['compare'];
				$check_repeat=$dataProHead[0]['check_repeat'];
				
				//data product
				$dataProduct=$this->dataProduct($product_id);
				$price=$dataProduct[0]['price'];

				
				
				
				//--------------------------------
				//chk ว่าตัวแถมห้ามซ้ำกับตัวหลัก
				if($promo_seq!=1 && $check_repeat=="Y"){
					$chkRepeat=$this->chkRepeatProduct($product_id,$promo_seq);
					if($chkRepeat=="Y"){
						return "productRepeat";
					}
				}
				//--------------------------------
				
				
				
				
				
				
				//--------------------------------
				//data max price normal
				$chkNomal="
				SELECT if(max(price) is null,'null',max(price)) as maxNormal 
				FROM trn_promotion_tmp2 
				WHERE 
					computer_ip='$this->m_com_ip' 
					AND type_discount='normal' 
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				$dataPriceNomal = $this->db->fetchAll($chkNomal, 2);
				$maxNormal=$dataPriceNomal[0]['maxNormal'];
				if($maxNormal!="null"){
						
					//เปรียบเทียบราคาตัวแถม
					if(strtolower($type_discount)!="normal" && $compare=="L" && $price>=$maxNormal){
						return "price_L_err";//ห้ามราคา=>ตัวหลัก
					}
					if(strtolower($type_discount)!="normal" && $compare=="G" && $price>$maxNormal){
						return "price_G_err";//ห้ามราคา>ตัวหลัก
					}
				}
				//--------------------------------
				
				
				//--------------------------------
			   $hotproconfig=$this->hotproconfig('');
			   $set_quantity=$hotproconfig[0]['set_quantity'];
			   $max_quantity=$set_quantity*$quantityPro;
			   //--------------------------------
			   
			   
				//--------------------------------
				if($quantityPro>1 && $check_double=="Y"){//ถ้าต้อง Chkว่าห้ามซ้ำใน Seq เดียวกัน
					
					
					if($quantity>$set_quantity){//ถ้ายิงเข้ามาทีเีดียวมากกว่า 1 ก็แสดงว่าซ้ำเลย
						return "Product_double";
					}
					$chk_double=$this->datatmp2($promo_seq);
					if($chk_double){
						$find_double="SELECT sum(quantity) as sumQuantity 
						from trn_promotion_tmp2 
						where 
						computer_ip='$this->m_com_ip' and promo_seq='$promo_seq' and product_id='$product_id'";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
						$chk_double=$this->db->fetchAll($find_double, 2);
						if($chk_double){//ถ้ามีแล้ว
							$sumQuantity=$chk_double[0]['sumQuantity'];
							$num_quantity=$sumQuantity+$quantity;
							if($num_quantity>$set_quantity)//ถ้าเจอว่าใน SEQ นี้มีมากกว่า Set ที่ำกำหนดไว้
							return "Product_double";
						}
					}
				}
				
				
				
				//--------------------------------
				
				
				
			   //add item

			   $sql="
					insert trn_promotion_tmp2 set
					computer_no='$this->m_computer_no',
					computer_ip='$this->m_com_ip',
					promo_code='$promo_code',
					promo_seq='$promo_seq',
					product_id='$product_id',
					price='$price',
					type_discount='$type_discount',
					quantity='$quantity'
					
			   ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		 	   $result = $this->db->query($sql, 2);
		 	   if($result) {
		 	   	  $quantityStock=ceil($quantity);
		 	   	  $this->upStock($product_id,$quantity);
		 	   	  $chkQuantityShort=$this->quantityChkWeigth($promo_code,$promo_seq);
		 	   	  if($chkQuantityShort=="weight_no") {//ยิงไม่ครบ
		 	   	  	return "scan_diff";
		 	   	  } else {//ยิงครบ
		 	   	  	return "scan_ok";
		 	   	  }
		 	   } else {
		 	   	  return "add_error";
		 	   }
		 	   
		 	   
		 	   
		 	   
		}//end function

		
		
		function clear_tmp_auto(){ 
			
				//ถ้ามีค้าง
				$chkbefor="select * from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
		 	   	$r_chkbefor= $this->db->fetchAll($chkbefor, 2);
		 	   	if($r_chkbefor){
		 	   		//$this->cancle_stock_auto();
		 	   	}
		 	   	
		 	   	
		 	   	// ให้ Move จากtmp2 -> tmp_auto
	 		  $clear_tmp2="delete from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip'";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->query($clear_tmp2, 2);
	 		  
	 		  
	 	   		$move="
	 	   		insert into trn_promotion_tmp_auto(`computer_no`, `computer_ip`, `promo_code`, `promo_seq`, `product_id`, `price`, `type_discount`, `quantity`)
	 	   		select `computer_no`, `computer_ip`, `promo_code`, `promo_seq`, `product_id`, `price`, `type_discount`, `quantity` 
	 	   		from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'
	 	   		";
	 	   		
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
		 	   	$r_chkbefor= $this->db->query($move, 2);
			 	   	if($r_chkbefor){//move ok
		 	   		$del="
		 	   		delete 
		 	   		from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'
		 	   		";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp_auto");
			 	   	$r_chkbefor= $this->db->query($del, 2);
		 	   		
		 	   	}
			 	   
		 	   		
		 	   
		 	   	
		}
		
		function addProductauto($promo_code,$promo_seq,$product_id,$quantity) {
			

		 	   
				$chkProduct=$this->dataProduct($product_id);
				if(empty($chkProduct)){
					$chkBarcode=$this->chkBarcodeProduct($product_id);
					if($chkBarcode){
						$product_id=$chkBarcode[0]['product_id'];
					}else{
						return "Noproduct";
					}
				}
				
				$chkStock=$this->dataStockProduct($product_id,$quantity);
				if(empty($chkStock)){
					//chk ว่าให้ไปเอาสินค้าทดแทดนหรือไม่
					$dataDetail=$this->seqDetailPromotionLimit($promo_code,'1');
					$order_st=$dataDetail[0]['order_st'];
					if($order_st=="Y"){//ให้ไปหาสินค้าทดแทด
						$balance_qty=$dataDetail[0]['balance_qty'];
						$chkReserve=$this->findProductReserve($promo_code,$promo_seq,$balance_qty,$product_id,$quantity);
						if($chkReserve=="Next_product"){//ไปหาตัวอื่นดิ
							return "stock_short";
						}else if($chkReserve==""){//แม้แต่ทดแทนก็ไม่มีให้
							return "stock_short";
						}else if($chkReserve!="" && $chkReserve!="Next_product"){//มีสินค้าทดแทนให้
							$product_id=$chkReserve;
						}
					}else{
						return "stock_short";
					}
					
				}
				
				
				$chk_pro="select * from promo_detail where promo_code='$promo_code' and product_id='$product_id' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
				$chk_pro_data=$this->db->fetchAll($chk_pro, 2);
				if(empty($chk_pro_data)){
					return "no_in_pro";
				}
						
						
				//data product
				$dataProduct=$this->dataProduct($product_id);
				$price=$dataProduct[0]['price'];
				
					   //add item

			   $sql="
					insert trn_promotion_tmp_auto set
					computer_no='$this->m_computer_no',
					computer_ip='$this->m_com_ip',
					promo_code='$promo_code',
					promo_seq='$promo_seq',
					product_id='$product_id',
					price='$price',
					type_discount='',
					quantity='$quantity'
					
			   ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		 	   $result = $this->db->query($sql, 2);
		 	   if($result) {
		 	   		$this->upStock($product_id,$quantity);
					return "Y";
		 	   } else {
		 	   	  return "N";
		 	   }
				
				
				
		}
		
		
		
		
		function lastaddProduct($promo_code,$promo_seq,$product_id,$quantity) {
				$chkProduct=$this->dataProduct($product_id);
				if(empty($chkProduct)){
					$chkBarcode=$this->chkBarcodeProduct($product_id);
					if($chkBarcode){
						$product_id=$chkBarcode[0]['product_id'];
					}else{
						return "Noproduct";
					}
				}
				
				$chkStock=$this->dataStockProduct($product_id,$quantity);
				if(empty($chkStock)){
					//chk ว่าให้ไปเอาสินค้าทดแทดนหรือไม่
					$dataDetail=$this->seqDetailPromotionLimit($promo_code,$promo_seq);
					$order_st=$dataDetail[0]['order_st'];
					if($order_st=="Y"){//ให้ไปหาสินค้าทดแทด
						$balance_qty=$dataDetail[0]['balance_qty'];
						$chkReserve=$this->findProductReserve($promo_code,$promo_seq,$balance_qty,$product_id,$quantity);
						if($chkReserve=="Next_product"){//ไปหาตัวอื่นดิ
							return "stock_short";
						}else if($chkReserve==""){//แม้แต่ทดแทนก็ไม่มีให้
							return "stock_short";
						}else if($chkReserve!="" && $chkReserve!="Next_product"){//มีสินค้าทดแทนให้
							$product_id=$chkReserve;
						}
					}else{
						return "stock_short";
					}
					
				}
				
				
				
				
		 	    $sqlPromoDetail="select * from promo_detail where promo_code='$promo_code' and seq_pro='$promo_seq' limit 1";
		 	    $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
		 	    $promoDetail = $this->db->fetchAll($sqlPromoDetail, 2);
				$promo_product_id=$promoDetail[0]['product_id'];
				if($promo_product_id=="ALL"){//ถ้าเป็นตัวไหนก็ได้
					$type_discount=$promoDetail[0]['type_discount'];
					$quantityPro=$promoDetail[0]['quantity'];
					$weightPro=$promoDetail[0]['weight'];
					$check_double=$promoDetail[0]['check_double'];
				}else{
					//chk เป็นสินค้าในโปรโมชั่นนี้หรือไม่่2
					$chk="
					select 
					  * 
					from 
					  promo_detail 
					 where 
					  promo_code='$promo_code' and
					  seq_pro='$promo_seq' and
					  (product_id='$product_id' or product_id='ALL') ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_detail");
					$dataProdetail = $this->db->fetchAll($chk, 2);
					if(empty($dataProdetail)) {
						return 2;
					}
					$type_discount=$dataProdetail[0]['type_discount'];
					$quantityPro=$dataProdetail[0]['quantity'];
					$weightPro=$dataProdetail[0]['weight'];
					$check_double=$dataProdetail[0]['check_double'];
				}
				

				$quantity=$quantity*$weightPro;
				
				
				//--------------------------------
				//chk ว่ายิงมาเกินหรือเปล่า
				
				//log promotion green bag
				if($promo_code=="OX09150214" || $promo_code=="OX08140214"){
					$dataPos=$this->dataPos();
					$member_no=$dataPos[0]['member_no'];
				
					$quantity_input=$this->chk_have_promotion($member_no);
					if($quantity_input>=3){
						return "limit_log";
					}
					
				}	
				
								
			   $chkScan=$this->lastquantityScan($quantityPro,$quantity,$promo_code,$promo_seq);
				if($chkScan=="4"){
					return 4;
				}

	 	   	   //--------------------------------
		 	   	  
	 	   	   
	 	   	   
				//data head pro
				$dataProHead=$this->dataProHead($promo_code);
				$compare=$dataProHead[0]['compare'];
				$check_repeat=$dataProHead[0]['check_repeat'];
				
				//data product
				$dataProduct=$this->dataProduct($product_id);
				$price=$dataProduct[0]['price'];

				
				
				
				//--------------------------------
				//chk ว่าตัวแถมห้ามซ้ำกับตัวหลัก
				if($promo_seq!=1 && $check_repeat=="Y"){
					$chkRepeat=$this->chkRepeatProduct($product_id,$promo_seq);
					if($chkRepeat=="Y"){
						return "productRepeat";
					}
				}
				//--------------------------------
				
				
					if($promo_code=="OX02110613"){
					$chkNomal="select  price  
					from trn_promotion_tmp2 
					where computer_ip='$this->m_com_ip' and type_discount='Percent' and promo_code='OX02110613' and promo_seq='1' ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
					$dataPriceNomal = $this->db->fetchAll($chkNomal, 2);
					if($dataPriceNomal){
						$price1=$dataPriceNomal[0]['price'];
							if($compare=="G" && $price>$price1){
								return "price_G_err";//ห้ามราคา>ตัวหลัก
							}
						}
					}
				
					if($promo_code=="OX09310714"){
						$chkNomal="select  price  
						from trn_promotion_tmp2 
						where computer_ip='$this->m_com_ip' and type_discount='Percent' and promo_code='OX09310714' and promo_seq='1' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
						$dataPriceNomal = $this->db->fetchAll($chkNomal, 2);
						if($dataPriceNomal){
							$price1=$dataPriceNomal[0]['price'];
								if($price>$price1){
									return "price_G_err";//ห้ามราคา>ตัวก่อนหน้า
								}
						}
					}
					
					
				
				//--------------------------------
				//data max price normal
				$chkNomal="select if(max(price) is null,'null',max(price)) as maxNormal 
				from trn_promotion_tmp2 
				where computer_ip='$this->m_com_ip' and type_discount='normal' ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				$dataPriceNomal = $this->db->fetchAll($chkNomal, 2);
				$maxNormal=$dataPriceNomal[0]['maxNormal'];
				if($maxNormal!="null"){
						
					//เปรียบเทียบราคาตัวแถม
					if(strtolower($type_discount)!="normal" && $compare=="L" && $price>=$maxNormal){
							return "price_L_err";//ห้ามราคา=>ตัวหลัก
					}

					if(strtolower($type_discount)!="normal" && $compare=="G" && $price>$maxNormal){
						return "price_G_err";//ห้ามราคา>ตัวหลัก
					}
					

				}
				//--------------------------------
				
				
				
				
				//--------------------------------
			   $setData="select * from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$promo_code' ";
			   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			   $setData = $this->db->fetchAll($setData, 2);
			   $set_quantity=$setData[0]['set_quantity'];
			   $max_quantity=$set_quantity*$quantityPro;
			   
			   
			   
			   
			   
				//--------------------------------
				if($quantityPro>1 && $check_double=="Y"){//ถ้าต้อง Chkว่าห้ามซ้ำใน Seq เดียวกัน
					
					
					if($quantity>$set_quantity){//ถ้ายิงเข้ามาทีเีดียวมากกว่า 1 ก็แสดงว่าซ้ำเลย
						return "Product_double";
					}
					$chk_double=$this->datatmp2($promo_seq);
					if($chk_double){
						$find_double="select sum(quantity) as sumQuantity 
						from trn_promotion_tmp2 
						where computer_ip='$this->m_com_ip' and promo_seq='$promo_seq' and product_id='$product_id'";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
						$chk_double=$this->db->fetchAll($find_double, 2);
						if($chk_double){//ถ้ามีแล้ว
							$sumQuantity=$chk_double[0]['sumQuantity'];
							$num_quantity=$sumQuantity+$quantity;
							if($num_quantity>$set_quantity)//ถ้าเจอว่าใน SEQ นี้มีมากกว่า Set ที่ำกำหนดไว้
							return "Product_double";
						}
					}
				}
				
				
				
				//--------------------------------
				
				
				
				
			   //add item

			   $sql="
					insert trn_promotion_tmp2 set
					computer_no='$this->m_computer_no',
					computer_ip='$this->m_com_ip',
					promo_code='$promo_code',
					promo_seq='$promo_seq',
					product_id='$product_id',
					price='$price',
					type_discount='$type_discount',
					quantity='$quantity'
					
			   ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		 	   $result = $this->db->query($sql, 2);
		 	   if($result) {
		 	   	  $quantityStock=ceil($quantity);
		 	   	  $this->upStock($product_id,$quantity);
		 	   	  $chkQuantityShort=$this->lastquantityChkWeigth($promo_code,$promo_seq);
		 	   	  if($chkQuantityShort=="weight_no") {//ยิงไม่ครบ
		 	   	  	return "scan_diff";
		 	   	  } else {//ยิงครบ
		 	   	  	return "scan_ok";
		 	   	  }
		 	   } else {
		 	   	  return "add_error";
		 	   }
		 	   
		 	   
		 	   
		 	   
		}//end function
		
		
		
		
		
		function scanProduct() {
			   $sql="select * from trn_promotion_tmp2 WHERE computer_ip='$this->m_com_ip' ";
			   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		 	   $result = $this->db->fetchAll($sql, 2);
		 	   return $result;
		}
		
		function chkRepeatProduct($product_id,$promo_seq) {//chk ว่าสินค้านี้ไปตรงกับสินค้าหลักที่ยิงมาแล้วหรือไม่
			   $sql="select * from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' and product_id='$product_id' and promo_seq<'$promo_seq' ";
			   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
		 	   $result = $this->db->fetchAll($sql, 2);
		 	   if($result) {
		 	     return "Y";
		 	   }else {
		 	   	 return "N";
		 	   }
		 	   
		}
		
		
		
		function quantityScan($quantityPro,$quantity,$promo_code,$promo_seq) {
	 		  $dataScan="select sum(quantity) as quantityScan
	 		   from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' and promo_seq='$promo_seq' group by promo_code
	 		    ";
	 		 $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 		  if($dataScan){
	 		  	$quantityScan=$dataScan[0]['quantityScan'];
	 		  }else{
	 		  	$quantityScan=0;
	 		  }
	 		  
	 		  $proconfig="select * from trn_promotion_config  WHERE computer_ip='$this->m_com_ip' ";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_config");
	 		  $proconfig = $this->db->fetchAll($proconfig, 2);
	 		  $set_quantity=$proconfig[0]['set_quantity'];
	 		  
	 		  $max_quantity=$set_quantity*$quantityPro;	 	   	  
	 	   	  	
      		if(($quantity+$quantityScan)>$max_quantity){
      			return 4;
      		}
	 	   	  
		}
		
		function lastquantityScan($quantityPro,$quantity,$promo_code,$promo_seq) {
			

			  
			  
	 		  $dataScan="select sum(quantity) as quantityScan
	 		   from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' and promo_seq='$promo_seq' group by promo_code
	 		    ";
	 		 $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 		  if($dataScan){
	 		  	$quantityScan=$dataScan[0]['quantityScan'];
	 		  }else{
	 		  	$quantityScan=0;
	 		  }
	 		  
			  $dataSet=$this->datapromocute($promo_code);
			  $set_quantity=$dataSet[0]['set_quantity'];
	 		  
	 		  $max_quantity=$set_quantity;	 	   	  
	 	   	  	
      		if(($quantity+$quantityScan)>$max_quantity){
      			return 4;
      		}
	 	   	  
		}
		

		
		function quantityChkWeigth($promo_code,$promo_seq) {
			  $dataSet=$this->hotproconfig('');
			  $set_quantity=$dataSet[0]['set_quantity'];
			  
			  
			  $proDetail=$this->seqDetailPromotionLimit($promo_code,$promo_seq);
			  $quantityPro=$proDetail[0]['quantity'];
			  
			  $max_quantity=$quantityPro*$set_quantity;
			  
			  
	 		  $dataScan="select sum(quantity) as quantity 
	 		  from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' and promo_seq='$promo_seq'
	 		   ";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 	   	  if($dataScan){
	 	   	  	$quantity=$dataScan[0]['quantity'];
	 	   	  	if($max_quantity==$quantity){
	 	   	  		return "weight_ok";
	 	   	  	}else{
	 	   	  		return "weight_no";
	 	   	  	}
	 	   	  	
	 	   	  }else{
	 	   	  	return "weight_no";
	 	   	  }
		}
		
		
		function lastquantityChkWeigth($promo_code,$promo_seq) {
			  $dataSet=$this->datapromocute($promo_code);
			  $set_quantity=$dataSet[0]['set_quantity'];
			  
			  
			  $proDetail=$this->seqDetailPromotionLimit($promo_code,$promo_seq);
			  $quantityPro=$proDetail[0]['quantity'];
			  
			  $max_quantity=$set_quantity;
			  //echo "$max_quantity";
			  
	 		  $dataScan="select sum(quantity) as quantity 
	 		  from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' and promo_seq='$promo_seq'
	 		   ";
	 		  //echo $dataScan;
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 	   	  if($dataScan){
	 	   	  	$quantity=$dataScan[0]['quantity'];
	 	   	  	if($max_quantity==$quantity){
	 	   	  		return "weight_ok";
	 	   	  	}else{
	 	   	  		return "weight_no";
	 	   	  	}
	 	   	  	
	 	   	  }else{
	 	   	  	return "weight_no";
	 	   	  }
		}
		

		
		function addHotPro($target_pro) {//เล่นโปรแบบเลือกจาก HOTPRO
			
			$maxSeq="select max(seq_set) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
		   $sql="select * from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' order by promo_seq";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   $dataScan = $this->db->fetchAll($sql, 2);
	 	   
			foreach($dataScan as $dataScan){
				
				$promo_code=$dataScan['promo_code'];
				$promo_seq=$dataScan['promo_seq'];
				$product_id=$dataScan['product_id'];
				$quantity=$dataScan['quantity'];
				
				

				//ข้อมูลจากโปรโมชั่น
				$seqPromotion=$this->seqPromotion($promo_code,$promo_seq,$product_id);
				
				$seq_pro=$seqPromotion[0]['seq_pro'];
				$promo_pos=$seqPromotion[0]['promo_pos'];
				$promo_tp=$seqPromotion[0]['promo_tp'];	
				$type_discount=$seqPromotion[0]['type_discount'];
				$discount=$seqPromotion[0]['discount'];

				

				
				//คำนวนยอดเงินจากเงื่อนไขโปรโมชั่น
				$valNet=$this->caseNet($type_discount,$quantity,$product_id,$discount);
				$pricePromotion=$valNet['pricePromotion'];
				$amountPromotion=$valNet['amountPromotion'];
				$discountPromotion=$valNet['discountPromotion'];
				$netPromotion=$valNet['netPromotion'];
				

				
				
				//เก็บข้อมูลเล่นโปรลงตาราง
				$this->insertPro("",$maxSeq,$promo_code,$seq_pro,$promo_pos,$promo_tp,$type_discount,$product_id,$pricePromotion,$quantity,$amountPromotion,$discountPromotion,$netPromotion,$target_pro);

				

				//$maxSeq++;
			} //end foreach	
			
			
			//เก็บยอดเงินการเล่นโปรท้ายบิลเอาไว้ตัดออก
			/*$this->lastNetBill($promo_code,'1');
			
			
			//ยกเลิกโปรนี้โปรเดียว
			$dataHeadPro=$this->dataPromoHead($promo_code);
			$condition=$dataHeadPro[0]['condition'];
			if($condition){//ถ้าให้เล่นครั้งเดียว ก็เก็บลงตารางยกเลิกโปรโมชั่นด้วย(promo_last_cancle)
				$this->canclelastpro_one($promo_code);
			}*/
			
			
			//clear trn_promotion_tmp2
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
			$this->db->query("delete from  trn_promotion_tmp2 where computer_ip='$this->m_com_ip'", 2);
		}
		
		
		
		
		
		function addpro_auto($target_pro) {//เล่นโปรแบบเลือกจาก HOTPRO
			
			$maxSeq="select max(seq_set) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
			

			
			
		   $sql="select * from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip' order by seq_set,promo_seq";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   $dataScan = $this->db->fetchAll($sql, 2);
	 	   $count_data=count($dataScan);

			if($count_data%2!=0){
				$chk_map="No";
			} else{
				$chk_map="Yes";
			}

			$i=1;
			foreach($dataScan as $dataScan){
				
				$promo_code=$dataScan['promo_code'];
				$seq_set=$dataScan['seq_set'];
				$promo_seq=$dataScan['promo_seq'];
				$product_id=$dataScan['product_id'];
				$quantity=$dataScan['quantity'];
				
				

				//ข้อมูลจากโปรโมชั่น
				$seqPromotion=$this->seqPromotion($promo_code,$promo_seq,$product_id);
				
				$seq_pro=$seqPromotion[0]['seq_pro'];
				$promo_pos=$seqPromotion[0]['promo_pos'];
				$promo_tp=$seqPromotion[0]['promo_tp'];	
				$type_discount=$seqPromotion[0]['type_discount'];
				$discount=$seqPromotion[0]['discount'];

				

				
				//คำนวนยอดเงินจากเงื่อนไขโปรโมชั่น
				$valNet=$this->caseNet($type_discount,$quantity,$product_id,$discount);
				$pricePromotion=$valNet['pricePromotion'];
				$amountPromotion=$valNet['amountPromotion'];
				$discountPromotion=$valNet['discountPromotion'];
				$netPromotion=$valNet['netPromotion'];
				

				
				
				//เก็บข้อมูลเล่นโปรลงตาราง
				//if($i==$count_data && $chk_map=="No") {
				if($seq_set=='1000') {
					$this->insertNoPro("",$product_id,$quantity);
					
				}else{
					$this->insertPro("",$seq_set,$promo_code,$seq_pro,$promo_pos,$promo_tp,$type_discount,$product_id,$pricePromotion,$quantity,$amountPromotion,$discountPromotion,$netPromotion,$target_pro);
				}
				

				$i++;
			} //end foreach	

		}
		
		
		function addNoHotPro($promo_code) {//add แบบราคาปกติ เมื่อจบโปรกลางคัน
			$maxSeq="select max(seq) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
		   $sql="select * from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' order by promo_seq";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   $dataScan = $this->db->fetchAll($sql, 2);
			foreach($dataScan as $dataScan){
				$promo_seq=$dataScan['promo_seq'];
				$product_id=$dataScan['product_id'];
				$quantity=$dataScan['quantity'];
				

				//เก็บข้อมูลเล่นโปรลงตาราง
				$this->insertNoPro("",$product_id,$quantity);
				
			} //end foreach	
				//clear trn_promotion_tmp2
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				$this->db->query("delete from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'", 2);

				//$maxSeq++;
	   }
			
			
		function addnormal($product_id,$quantity) {//add แบบราคาปกติ 
			$maxSeq="select max(seq) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
			$upStock="
			update com_stock_master
			set
			onhand=onhand-$quantity

			where 
			product_id='$product_id'
 	    	and month=month('$this->doc_date') and
 	    	 year=year('$this->doc_date')
 	    	";
			$this->db->query($upStock, 2);
			
			
 	    
 	    
			//เก็บข้อมูลเล่นโปรลงตาราง
			$this->insertNoPro("",$product_id,$quantity);

	   }
	   
	   
		
		
		function addSeqHotPro($promo_code) {//เล่นโปรแบบที่มี Seq เดียว
			$maxSeq="select max(seq_set) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
		   $sql="select * from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' order by promo_seq";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   $dataScan = $this->db->fetchAll($sql, 2);
			foreach($dataScan as $dataScan){
				$promo_seq=$dataScan['promo_seq'];
				$product_id=$dataScan['product_id'];
				$quantity=$dataScan['quantity'];
				
				

				//ข้อมูลจากโปรโมชั่น
				$seqPromotion=$this->seqPromotion($promo_code,$promo_seq,$product_id);
			
				$seq_pro=$seqPromotion[0]['seq_pro'];
				$promo_pos=$seqPromotion[0]['promo_pos'];
				$promo_tp=$seqPromotion[0]['promo_tp'];	
				$quantityPro=$seqPromotion[0]['quantity'];	
				$type_discount=$seqPromotion[0]['type_discount'];
				$discount=$seqPromotion[0]['discount'];

				

				
				//คำนวนยอดเงินจากเงื่อนไขโปรโมชั่น
				$valNet=$this->caseNet($type_discount,$quantity,$product_id,$discount);
				$pricePromotion=$valNet['pricePromotion'];
				$amountPromotion=$valNet['amountPromotion'];
				$discountPromotion=$valNet['discountPromotion'];
				$netPromotion=$valNet['netPromotion'];
				

				
				
				//เก็บข้อมูลเล่นโปรลงตาราง
				$this->insertPro("",$maxSeq,$promo_code,$seq_pro,$promo_pos,$promo_tp,$type_discount,$product_id,$pricePromotion,$quantity,$amountPromotion,$discountPromotion,$netPromotion,'HOTPROSEQ');

				
				//clear trn_promotion_tmp2
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				$this->db->query("delete from  trn_promotion_tmp2 where computer_ip='$this->m_com_ip' ", 2);
				//$maxSeq++;
			} //end foreach	
			
			
			//เก็บยอดเงินการเล่นโปรท้ายบิลเอาไว้ตัดออก
			//$this->lastNetBill($promo_code,'1');
			
			
			//ยกเลิกโปรนี้โปรเดียว
			/*$dataHeadPro=$this->dataPromoHead($promo_code);
			$condition=$dataHeadPro[0]['condition'];
			if($condition){//ถ้าให้เล่นครั้งเดียว ก็เก็บลงตารางยกเลิกโปรโมชั่นด้วย(promo_last_cancle)
				$this->canclelastpro_one($promo_code);
			}*/
			
			
			//clear trn_promotion_tmp2
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
			$this->db->query("delete from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'", 2);
		}
		
		
		
		
		function addLastHotPro($target_pro) {//เล่นโปรแบบท้ายบิล
			
			$maxSeq="select max(seq_set) as maxSeq from trn_promotion_tmp1 where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dataMaxSeq = $this->db->fetchAll($maxSeq, 2);
			$maxSeq=$dataMaxSeq[0]['maxSeq'];
			if($maxSeq==0){
				$maxSeq=1;
			}else {
				$maxSeq=$maxSeq+1;
			}
			
		   $sql="select * from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' order by promo_seq";
		   $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 	   $dataScan = $this->db->fetchAll($sql, 2);
			foreach($dataScan as $dataScan){
				
				$promo_code=$dataScan['promo_code'];
				$promo_seq=$dataScan['promo_seq'];
				$product_id=$dataScan['product_id'];
				$quantity=$dataScan['quantity'];
				
				

				//ข้อมูลจากโปรโมชั่น
				$seqPromotion=$this->seqPromotion($promo_code,$promo_seq,$product_id);
				$seq_pro=$seqPromotion[0]['seq_pro'];
				$promo_pos=$seqPromotion[0]['promo_pos'];
				$promo_tp=$seqPromotion[0]['promo_tp'];	
				$quantityPro=$seqPromotion[0]['quantity'];	
				$type_discount=$seqPromotion[0]['type_discount'];
				$discount=$seqPromotion[0]['discount'];

				

				
				//คำนวนยอดเงินจากเงื่อนไขโปรโมชั่น
				$valNet=$this->caseNet($type_discount,$quantity,$product_id,$discount);
				$pricePromotion=$valNet['pricePromotion'];
				$amountPromotion=$valNet['amountPromotion'];
				$discountPromotion=$valNet['discountPromotion'];
				$netPromotion=$valNet['netPromotion'];
				

				
				
				//เก็บข้อมูลเล่นโปรลงตาราง
				$this->insertPro("",$maxSeq,$promo_code,$seq_pro,$promo_pos,$promo_tp,$type_discount,$product_id,$pricePromotion,$quantity,$amountPromotion,$discountPromotion,$netPromotion,$target_pro);

				

				//$maxSeq++;
			} //end foreach	
			
			
			//เก็บยอดเงินการเล่นโปรท้ายบิลเอาไว้ตัดออก
			$sumSeqFirst="select sum(quantity) as sumQuantityFirst from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' and promo_seq='1' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
			$data_first=$this->db->fetchAll($sumSeqFirst, 2);
			$quantity_first=$data_first[0]['sumQuantityFirst'];
			$this->lastNetBill($promo_code,$quantity_first);
			
			
			//ยกเลิกโปรนี้โปรเดียว
			$dataHeadPro=$this->dataPromoHead($promo_code);
			$condition=$dataHeadPro[0]['condition'];
			if($condition){//ถ้าให้เล่นครั้งเดียว ก็เก็บลงตารางยกเลิกโปรโมชั่นด้วย(promo_last_cancle)
				$this->canclelastpro_one($promo_code);
			}
			
			
			//clear trn_promotion_tmp2
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
			$this->db->query("delete from  trn_promotion_tmp2 where computer_ip='$this->m_com_ip' ", 2);
		}	
		
		
		
		
		
		
		
		
		
		
		
		
		function lastNetBill($promo_code,$set_pro){
			$dataPro=$this->dataPromoHead($promo_code);
			$promo_pos=$dataPro[0]['promo_pos'];
			$promo_tp=$dataPro[0]['promo_tp'];
			$process=$dataPro[0]['process'];
			$promo_amt=$dataPro[0]['promo_amt'];
			$promo_price=$dataPro[0]['promo_price'];
			$level=$dataPro[0]['level'];
			$accumulate=$dataPro[0]['accumulate'];
			$sum_price=$promo_amt*$set_pro;
			
			if($promo_pos!="M"){//เป็นโปรท้ายบิล
				$sql="insert into promo_last_net(computer_no,computer_ip,level,promo_code,process,promo_amt,promo_price,set_pro,sum_price) values('$this->m_computer_no','$this->m_com_ip','$level','$promo_code','$process','$promo_amt','$promo_price','$set_pro','$sum_price')";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");
				$this->db->query($sql, 2);
			}
		}
		


		
		
		function datapromocute($promo_code){
			$sql="select * from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$promo_code' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$result=$this->db->fetchAll($sql, 2);
			return $result;
		}
		
		function cutepromolast($level){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query("delete from promo_last_cute where computer_ip='$this->m_com_ip'", 2);
			
			
			  //sum lastbill
	 		  $sumLastQty="
		 		  select 
			 		  sum(sum_price) as lastQty
		 		  from 
		 		  	promo_last_net 
		 		  where
		 		  	computer_ip='$this->m_com_ip' and 
		 		  	promo_price='U'
	 		   ";
	 	   	  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");
	 		  $sumLastQty = $this->db->fetchAll($sumLastQty, 2);
		  	  $lastQty=$sumLastQty[0]['lastQty'];
			  	  
			  	  
			  //sum lastbill
		 	  $sumLastAmount="
		 		  select 
			 		  sum(sum_price) as lastAmount
		 		  from 
		 		  	promo_last_net 
		 		  where
		 		  	computer_ip='$this->m_com_ip' and 
		 		  	promo_price='G' and
		 		  	level='$level'
		 	   ";
		    	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");  
		 	  $sumLastAmount = $this->db->fetchAll($sumLastAmount, 2);
		  	  $lastAmount=$sumLastAmount[0]['lastAmount'];
		
		 	  $sumLastNet="
		 		  select 
			 		  sum(sum_price) as lastNet
		 		  from 
		 		  	promo_last_net 
		 		  where
		 		    computer_ip='$this->m_com_ip' and
		 		  	promo_price='N' and
		 		  	level='$level'
		 	   ";
		 	  //echo $sumLastNet."<br>";
		    	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");  
		 	  $sumLastNet = $this->db->fetchAll($sumLastNet, 2);
		  	  $lastNet=$sumLastNet[0]['lastNet'];
			  	  
			  	  
			  	  
			
			//A สินค้าทุกตัว
			/*if($level==21){
				$val="
				 select 
					sum(quantity) as quantity,
					sum(amount) as amount,
					sum(net_amt) as net
				 from 
				 	trn_promotion_tmp1 
				 where 
				 	computer_ip='$this->m_com_ip' 
				 ";
			}else{
				$val="
				 select 
					sum(quantity) as quantity,
					sum(amount) as amount,
					sum(net_amt) as net
				 from 
				 	trn_promotion_tmp1 
				 where 
				 	computer_ip='$this->m_com_ip' and 
				 	promo_pos<>'L'
				 ";
			
			}
			*/
				$val="
				 select 
					sum(quantity) as quantity,
					sum(amount) as amount,
					sum(net_amt) as net
				 from 
				 	trn_promotion_tmp1 
				 where 
				 	computer_ip='$this->m_com_ip' 
				 ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dval=$this->db->fetchAll($val, 2);
			if($dval){
				$quantity=$dval[0]['quantity']-$lastQty;
				$amount=$dval[0]['amount']-$lastAmount;
				/*if($level==21){
					$net=$dval[0]['net'];
				}else{
					$net=$dval[0]['net']-$lastNet;
				}
				*/
				$net=$dval[0]['net'];
			}else{
				$quantity=0;
				$amount=0;
				$net=0;
			}
			$sql="
				insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
				SELECT 
				'$this->m_computer_no','$this->m_com_ip',
				'A',
				a.promo_code,
				a.promo_des,
				a.level ,
				a.promo_amt,
				a.promo_price,
				'$quantity',
				'$amount',
				'$net',
				if(a.promo_price='U' and '$quantity'>=a.promo_amt,'Y',if(a.promo_price='N' and '$net'>=a.promo_amt,'Y',if(a.promo_price='G' and '$amount'>=a.promo_amt,'Y','N'))) as chk,
				if(a.promo_price='U' and '$quantity'>=a.promo_amt,floor('$quantity'/a.promo_amt),if(a.promo_price='N' and '$net'>=a.promo_amt,floor('$net'/a.promo_amt),if(a.promo_price='G' and '$amount'>=a.promo_amt,floor('$amount'/a.promo_amt),'0'))) as set_quantity
				FROM 
				promo_head as a 
				where 
				 '$this->doc_date' between a.start_date and a.end_date and
				 a.level='$level' and
				 a.type_p='A' and a.promo_pos='L' and
				if(a.promo_price='U' and '$quantity'>=a.promo_amt,'Y',if(a.promo_price='N' and '$net'>=a.promo_amt,'Y',if(a.promo_price='G' and '$amount'>=a.promo_amt,'Y','N')))='Y'
			";
			//echo $sql;
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($sql, 2);
			//####################################################################
			
			
			
			//N สินค้าราคาปกติ
			$val="
			 select 
				sum(quantity) as quantity,
				sum(amount) as amount,
				sum(net_amt) as net
			 from 
			 	trn_promotion_tmp1 
			 where 
			    computer_ip='$this->m_com_ip' and 
			 	promo_code='' and
			 	promo_pos<>'L'
			 ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			$dval=$this->db->fetchAll($val, 2);
			if($dval){
				$quantity=$dval[0]['quantity']-$lastQty;
				$amount=$dval[0]['amount']-$lastAmount;
				$net=$dval[0]['net']-$lastNet;
			}else{
				$quantity=0;
				$amount=0;
				$net=0;
			}
			$sql="
				insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
				SELECT 
				'$this->m_computer_no','$this->m_com_ip',
				'N',
				a.promo_code,
				a.promo_des,
				a.level ,
				a.promo_amt,
				a.promo_price,
				'$quantity',
				'$amount',
				'$net',
				if(a.promo_price='U' and '$quantity'>=a.promo_amt,'Y',if(a.promo_price='N' and '$net'>=a.promo_amt,'Y',if(a.promo_price='G' and '$amount'>=a.promo_amt,'Y','N'))) as chk,
				if(a.promo_price='U' and '$quantity'>=a.promo_amt,floor('$quantity'/a.promo_amt),if(a.promo_price='N' and '$net'>=a.promo_amt,floor('$net'/a.promo_amt),if(a.promo_price='G' and '$amount'>=a.promo_amt,floor('$amount'/a.promo_amt),'0'))) as set_quantity
				FROM 
				promo_head as a 
				where 
				 '$this->doc_date' between a.start_date and a.end_date and
				 a.level='$level' and
				 a.type_p='N' and a.promo_pos='L' and
				if(a.promo_price='U' and '$quantity'>=a.promo_amt,'Y',if(a.promo_price='N' and '$net'>=a.promo_amt,'Y',if(a.promo_price='G' and '$amount'>=a.promo_amt,'Y','N')))='Y'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($sql, 2);
			//####################################################################
			
			
			
			//C สินค้าที่ระบุในตาราง Promo_chk
			$pro="
			select 
				* 
			from 
				promo_head
			where
				'$this->doc_date' between start_date and end_date and
				level='$level' and
				type_p='C' and promo_pos='L' 
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$dataPro=$this->db->fetchAll($pro, 2);
			foreach ($dataPro as $dataPro){//1
				$promo_code=$dataPro['promo_code'];
				$seqgrp="
					select 
						promo_group,unit,unit_tp,condition_tp 
					from 
						promo_check 
					where 
						promo_code='$promo_code' 
					group by promo_group,unit,unit_tp,condition_tp
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
				$dataGrp=$this->db->fetchAll($seqgrp, 2);
				$countGrp=count($dataGrp);
				$chkPro=0;
				$fieldChk="";
				foreach ($dataGrp as $dataGrp){//chk group
					$promo_group=$dataGrp['promo_group'];
					$unit=$dataGrp['unit'];
					$unit_tp=$dataGrp['unit_tp'];
					if($unit_tp=="U"){
						$fieldChk="a.quantity";
					}else if($unit_tp=="G"){
						$fieldChk="a.amount";
					}else if($unit_tp=="N"){
						$fieldChk="a.net_amt";	
					}
					$condition_tp=$dataGrp['condition_tp'];
					if($promo_group=="OTHER"){//สินค้าอื่น
							/*$chk="
								select 
									sum($fieldChk) as sumUnit
								from 
									trn_promotion_tmp1 as a left join (
																	select 
																		product_id 
																	from 
																		promo_check
																	where 
																		promo_code='$promo_code' 
																	) as b
									on a.product_id=b.product_id
								where
									a.promo_pos<>'L' 
									and b.product_id is null
								
				
							";*/
							$chk="
								select 
									sum($fieldChk) as sumUnit
								from 
									trn_promotion_tmp1 as a 
								where
									a.computer_ip='$this->m_com_ip' and 
									a.promo_pos<>'L' 
								
				
							";
					}else{

							$chk="
								select 
									sum($fieldChk) as sumUnit
								from 
									trn_promotion_tmp1 as a inner join promo_check as b
									on a.product_id=b.product_id
								where
									a.computer_ip='$this->m_com_ip' and
									a.promo_pos<>'L' and
									b.promo_code='$promo_code' and 
									b.promo_group='$promo_group'
								
				
							";			
					}
					//echo $chk."<br>";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
					$dataChk=$this->db->fetchAll($chk, 2);
				
					if($dataChk){
						$sumUnit=$dataChk[0]['sumUnit'];
					}else{
						$sumUnit=0;
					}
					
					if($sumUnit>=$unit){//ยอดซื้อมากกว่าที่กำหนด
						$chkPro=$chkPro+1;
					}
					
					
				}//end chk group
				
				
				//chk ว่าผ่านหรือไม่ผ่าน
				if($chkPro==$countGrp){//ครบทุกกลุ่มก็Insert Pro
					$insertPro="
						insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
						SELECT 
						'$this->m_computer_no','$this->m_com_ip',
						'C',
						a.promo_code,
						a.promo_des,
						a.level ,
						a.promo_amt,
						a.promo_price,
						0,
						0,
						0,
						'Y' as chk,
						'0' as set_quantity
						FROM 
							promo_head as a
						where 
						 a.promo_code='$promo_code'
						
				 	";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$dataChk=$this->db->query($insertPro, 2);
				}
				
				
				
			}//1
			
			//####################################################################
			
			
			
			
			
			
			//T สินค้าที่ระบุในตาราง Promo_chk
			$pro="
			select 
				* 
			from 
				promo_head
			where
				'$this->doc_date' between start_date and end_date and
				level='$level' and
				type_p='T' and promo_pos='L' 
			";
		
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			$dataPro=$this->db->fetchAll($pro, 2);
			foreach ($dataPro as $dataPro){//1
				$promo_code=$dataPro['promo_code'];
				$head_unit_type=$dataPro['promo_price'];
				$head_unit=$dataPro['promo_amt'];
				//echo "unit_type=$head_unit_type/$unit=$head_unit\n";
				$seqgrp="
					select 
						promo_group,unit,unit_tp,condition_tp
					from 
						promo_check 
					where 
						promo_code='$promo_code' 
					group by promo_group,unit,unit_tp,condition_tp
				";
				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
				$dataGrp=$this->db->fetchAll($seqgrp, 2);
				
				$countGrp=0;//ครบตามเงื่อนไข
				$chkPro=0;
				$fieldChk="";
				$condition_or="";//ตรวจสอบว่ามี OR หรือไม่
				
				foreach ($dataGrp as $dataGrp){//chk group
					$promo_group=$dataGrp['promo_group'];
					$unit=$dataGrp['unit'];
					$unit_tp=$dataGrp['unit_tp'];

					if($unit_tp=="U"){
						$fieldChk="a.quantity";
					}else if($unit_tp=="G"){
						$fieldChk="a.amount";
					}else if($unit_tp=="N"){
						$fieldChk="a.net_amt";	
					}
					$condition_tp=$dataGrp['condition_tp'];
					
					if($condition_tp=="OR"){
						$condition_or="Y"; //มีเงื่อนไข ORในนี้
					}else{//AND
						$condition_or=""; //ไม่มีเงื่อนไข ORในนี้
						$countGrp=$countGrp+1;
					}
					
					
					
					if($promo_group=="OTHER"){//ซื้อสินค้าอื่นๆครบ
							if($head_unit_type=="U"){
								$fieldChk_other="a.quantity";
								$where_cr=" and a.price>0";
							}else if($head_unit_type=="G"){
								$fieldChk_other="a.amount";
								$where_cr="";
							}else if($head_unit_type=="N"){
								$fieldChk_other="a.net_amt";	
								$where_cr="";
							}
							/*$chk="
								select 
									sum($fieldChk_other) as sumUnit
								from 
									trn_promotion_tmp1 as a 
								where
									a.computer_ip='$this->m_com_ip' and 
									a.promo_pos<>'L' 
				
							";*/
							
							$chk="
								select 
									sum($fieldChk_other) as sumUnit
								from 
									trn_promotion_tmp1 as a 
								where
									a.computer_ip='$this->m_com_ip' 
				
							";
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
							$dataChk=$this->db->fetchAll($chk, 2);
						
							if($dataChk){
								$sumUnit=$dataChk[0]['sumUnit'];
							}else{
								$sumUnit=0;
							}
							if($sumUnit>=$head_unit){//ยอดซื้อมากกว่าที่กำหนด
								$chkPro=$chkPro+1;//ผ่าน
								
							}
							
					}else if ($promo_group=="PROMOTION"){//ต้องเล่นโปรนี้มาก่อน
						
						$count_pro="select * from promo_check where promo_code='$promo_code' and promo_group='PROMOTION'";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
						$data_count_pro=$this->db->fetchAll($count_pro, 2);
						$num_count_pro=count($data_count_pro);
						
						$chk="
								select distinct a.promo_code 
								from 
									trn_promotion_tmp1 as a inner join promo_check as b
									on a.promo_code=b.promo_play
								where
									a.computer_ip='$this->m_com_ip' and
									b.promo_code='$promo_code' and 
									b.promo_group='$promo_group'
						";
						//echo $chk."<br>";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
						$dataChk=$this->db->fetchAll($chk, 2);
						$num_chk=count($dataChk);
						if($num_count_pro==$num_chk){//เล่นครบทุกโปร
							$chkPro=$chkPro+1;//ผ่าน
						}
					}else if ($promo_group=="NOPRO"){//ต้องไม่เล่นโปรนี้มาก่อน

						$chk="
								select distinct a.promo_code 
								from 
									trn_promotion_tmp1 as a inner join promo_check as b
									on a.promo_code=b.promo_play
								where
									a.computer_ip='$this->m_com_ip' and
									b.promo_code='$promo_code' and 
									b.promo_group='$promo_group'
						";
						//echo $chk."<br>";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
						$dataChk=$this->db->fetchAll($chk, 2);
						if($dataChk){
							$chkPro=$chkPro;//ไม่ผ่าน
						}else{
							$chkPro=$chkPro+1;//ผ่าน
						}
					}else{//ซื้อสินค้าในกลุ่มนี้ครบ
					
							/*$chk="
								select 
									sum($fieldChk) as sumUnit
								from 
									trn_promotion_tmp1 as a inner join promo_check as b
									on a.product_id=b.product_id
								where
									a.computer_ip='$this->m_com_ip' and 
									a.promo_pos<>'L' and
									b.promo_code='$promo_code' and 
									b.promo_group='$promo_group'
								
				
							";	*/
							$chk="
								select 
									if(sum($fieldChk) is null,0,sum($fieldChk))  as sumUnit
								from 
									trn_promotion_tmp1 as a inner join promo_check as b
									on a.product_id=b.product_id
								where
									a.computer_ip='$this->m_com_ip' and 
									b.promo_code='$promo_code' and 
									b.promo_group='$promo_group'
									and a.price>0
				
							";	
								
						
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
							$dataChk=$this->db->fetchAll($chk, 2);

							if($dataChk){
								$sumUnit=$dataChk[0]['sumUnit'];
								if($sumUnit==0){
									$sumUnit=0;
								}
							}else{
								$sumUnit=0;
							}
							
							if($sumUnit>=$unit){//ยอดซื้อมากกว่าที่กำหนด
								
								$chkPro=$chkPro+1;//ผ่าน
								
							}
							
					}
					//echo $chk."<br>";

					
					
				}//end chk group
				if($condition_or=="Y"){
					$countGrp=$countGrp+1;
				}
				
				//chk ว่าผ่านหรือไม่ผ่าน
				//echo "map T " . $chkPro . "==" .$countGrp ."Pro:" . $promo_code . "<br>\n";
				if($chkPro==$countGrp && ($chkPro!=0 || $chkPro!="")){//ครบทุกกลุ่มก็Insert Pro
					$insertPro="
						insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
						SELECT 
						'$this->m_computer_no','$this->m_com_ip',
						'T',
						a.promo_code,
						a.promo_des,
						a.level ,
						a.promo_amt,
						a.promo_price,
						0,
						0,
						0,
						'Y' as chk,
						'0' as set_quantity
						FROM 
							promo_head as a
						where 
						 a.promo_code='$promo_code'
						
				 	";
					//echo $insertPro;
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$dataChk=$this->db->query($insertPro, 2);
				}
				
				
				
			}//1
			
			//####################################################################
			
			
			
			
			
			
		
			
			
			//B สินค้าที่ระบุในตาราง Promo_chk ตามราคาที่กำหนด(ราคาตรงกัน)
			$sql="
			insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
			SELECT 
				'$this->m_computer_no','$this->m_com_ip',
				'B',
				a.promo_code,
				a.promo_des,
				a.level ,
				a.promo_amt,
				a.promo_price,
				c.quantity,
				c.amount,
				c.net  ,
				if(a.promo_price='U' and c.quantity-'$lastQty'>=a.promo_amt,'Y',if(a.promo_price='N' and c.net-'$lastNet'>=a.promo_amt,'Y',if(a.promo_price='G' and c.amount-'$lastAmount'>=a.promo_amt,'Y','N'))) as chk,
				if(a.promo_price='U' and c.quantity-'$lastQty'>=a.promo_amt,floor(c.quantity-'$lastQty'/a.promo_amt),if(a.promo_price='N' and c.net-'$lastNet'>=a.promo_amt,floor(c.net-'$lastNet'/a.promo_amt),if(a.promo_price='G' and c.amount-'$lastAmount'>=a.promo_amt,floor(c.amount-'$lastAmount'/a.promo_amt),'0'))) as set_quantity
				FROM 
				promo_head as a inner join promo_check as b
				on a.promo_code=b.promo_code
				inner join (select product_id,price,sum(quantity) as quantity,sum(amount) as amount,sum(net_amt) as net from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and promo_pos<>'L') as c
				on b.product_id=c.product_id and
				     b.pro_price=c.price
				where 
				 '$this->doc_date' between a.start_date and a.end_date and
				 a.level='$level' and
				 a.type_p='B' and a.promo_pos='L' and
				 '$this->doc_date' between b.start_date and b.end_date and
				if(a.promo_price='U' and c.quantity>=a.promo_amt,'Y',if(a.promo_price='N' and c.net>=a.promo_amt,'Y',if(a.promo_price='G' and c.amount>=a.promo_amt,'Y','N')))='Y'
			
			";	
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($sql, 2);
			//####################################################################
			
			
			
			//P Promotion ที่ระบุในตาราง Promo_chk ฟิว Promo_play
			$sql="
			insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
			SELECT 
				'$this->m_computer_no','$this->m_com_ip',
				'P',
				a.promo_code,
				a.promo_des,
				a.level ,
				a.promo_amt,
				a.promo_price,
				c.quantity,
				c.amount,
				c.net  ,
				if(a.promo_price='U' and c.quantity-'$lastQty'>=a.promo_amt,'Y',if(a.promo_price='N' and c.net-'$lastNet'>=a.promo_amt,'Y',if(a.promo_price='G' and c.amount-'$lastAmount'>=a.promo_amt,'Y','N'))) as chk,
				if(a.promo_price='U' and c.quantity-'$lastQty'>=a.promo_amt,floor(c.quantity-'$lastQty'/a.promo_amt),if(a.promo_price='N' and c.net-'$lastNet'>=a.promo_amt,floor(c.net-'$lastNet'/a.promo_amt),if(a.promo_price='G' and c.amount-'$lastAmount'>=a.promo_amt,floor(c.amount-'$lastAmount'/a.promo_amt),'0'))) as set_quantity
				FROM 
				promo_head as a inner join promo_check as b
				on a.promo_code=b.promo_code
				inner join (select promo_code,sum(quantity) as quantity,sum(amount) as amount,sum(net_amt) as net from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and promo_pos<>'L') as c
				on b.promo_play=c.promo_code
				where 
				 '$this->doc_date' between a.start_date and a.end_date and
				 a.level='$level' and
				 a.type_p='P' and a.promo_pos='L' and
				 '$this->doc_date' between b.start_date and b.end_date and
				if(a.promo_price='U' and c.quantity>=a.promo_amt,'Y',if(a.promo_price='N' and c.net>=a.promo_amt,'Y',if(a.promo_price='G' and c.amount>=a.promo_amt,'Y','N')))='Y'
			
			";	
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($sql, 2);
			//####################################################################
			
			
			
			
			//X สินค้าทุกตัว ยกเว้นที่ระบุในตาราง Promo_chk
			$pro="
				 select 
					a.promo_code,
					a.promo_des,
					a.level ,
					a.promo_amt,
					a.promo_price
				from 
				promo_head as a
				where
				'$this->doc_date' between a.start_date and a.end_date
				and a.level='$level'
				and a.promo_pos='L'
				and a.type_p='X'
				 	
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			 $dataPro = $this->db->fetchAll($pro, 2);
			 if($dataPro){
				 foreach($dataPro as $dataPro){
				 	$promo_code=$dataPro['promo_code'];
				 	$promo_des=$dataPro['promo_des'];
				 	$level=$dataPro['level'];
				 	$promo_amt=$dataPro['promo_amt'];
				 	$promo_price=$dataPro['promo_price'];

				 	$sum="
						SELECT 
							sum(a.quantity) as quantity,sum(a.amount) as amount,sum(a.net_amt) as net
						FROM 
						trn_promotion_tmp1 as a  LEFT JOIN (
											select product_id,promo_play 
											from promo_check where promo_code='$promo_code' 
											and '$this->doc_date' between start_date and end_date
											) as b
											 ON a.product_id = b.product_id
						WHERE 
						a.computer_ip='$this->m_com_ip' and 
						b.product_id Is Null
				 	";
				 	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				 	$dsum = $this->db->fetchAll($sum, 2);
					if($dsum){
						$quantity=$dsum[0]['quantity']-$lastQty;
						$amount=$dsum[0]['amount']-$lastAmount;
						$net=$dsum[0]['net']-$lastNet;
					}else{
						$quantity=0;
						$amount=0;
						$net=0;
					}
					$sql="
						insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
						SELECT 
						'$this->m_computer_no','$this->m_com_ip',
						'X',
						'$promo_code',
						'$promo_des',
						'$level' ,
						'$promo_amt',
						'$promo_price',
						'$quantity',
						'$amount',
						'$net',
						if('$promo_price'='U' and '$quantity'>='$promo_amt','Y',if('$promo_price'='N' and '$net'>='$promo_amt','Y',if('$promo_price'='G' and '$amount'>='$promo_amt','Y','N'))) as chk,
						if('$promo_price'='U' and '$quantity'>='$promo_amt',floor('$quantity'/'$promo_amt'),if('$promo_price'='N' and '$net'>='$promo_amt',floor('$net'/'$promo_amt'),if('$promo_price'='G' and '$amount'>='$promo_amt',floor('$amount'/'$promo_amt'),'0'))) as set_quantity
						
					";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$this->db->query($sql, 2);

					//####################################################################
				 }
			 }
			
			//E สินค้าทุกตัว ยกเว้นโปรโมชั่นที่ระบุในตาราง Promo_chk
			$pro="
				 select 
					a.promo_code,
					a.promo_des,
					a.level ,
					a.promo_amt,
					a.promo_price
				from 
				promo_head as a
				where
				'$this->doc_date' between a.start_date and a.end_date
				and a.level='$level'
				and a.promo_pos='L'
				and a.type_p='E'
				 	
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			 $dataPro = $this->db->fetchAll($pro, 2);
			 if($dataPro){
				 foreach($dataPro as $dataPro){
				 	$promo_code=$dataPro['promo_code'];
				 	$promo_des=$dataPro['promo_des'];
				 	$level=$dataPro['level'];
				 	$promo_amt=$dataPro['promo_amt'];
				 	$promo_price=$dataPro['promo_price'];

				 	$sum="
						SELECT 
							sum(a.quantity) as quantity,sum(a.amount) as amount,sum(a.net_amt) as net
						FROM 
						trn_promotion_tmp1 as a  LEFT JOIN (
											select product_id,promo_play 
											from promo_check where promo_code='$promo_code' 
											and '$this->doc_date' between start_date and end_date
											) as b
											 ON a.promo_code = b.promo_code
						WHERE 
						a.computer_ip='$this->m_com_ip' and 
						b.promo_code Is Null
				 	";
				 	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				 	$dsum = $this->db->fetchAll($sum, 2);
					if($dsum){
						$quantity=$dsum[0]['quantity']-$lastQty;
						$amount=$dsum[0]['amount']-$lastAmount;
						$net=$dsum[0]['net']-$lastNet;
					}else{
						$quantity=0;
						$amount=0;
						$net=0;
					}
					$sql="
						insert into promo_last_cute(computer_no,computer_ip,type_p,`promo_code`, `promo_des`, `level`, `promo_amt`, `promo_price`, `quantity`, `amount`, `net`, `chk`,set_quantity)
						SELECT 
						'$this->m_computer_no','$this->m_com_ip',
						'E',
						'$promo_code',
						'$promo_des',
						'$level' ,
						'$promo_amt',
						'$promo_price',
						'$quantity',
						'$amount',
						'$net',
						if('$promo_price'='U' and '$quantity'>='$promo_amt','Y',if('$promo_price'='N' and '$net'>='$promo_amt','Y',if('$promo_price'='G' and '$amount'>='$promo_amt','Y','N'))) as chk,
						if('$promo_price'='U' and '$quantity'>='$promo_amt',floor('$quantity'/'$promo_amt'),if('$promo_price'='N' and '$net'>='$promo_amt',floor('$net'/'$promo_amt'),if('$promo_price'='G' and '$amount'>='$promo_amt',floor('$amount'/'$promo_amt'),'0'))) as set_quantity
						
					";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$this->db->query($sql, 2);
					//####################################################################
				 }
			 }
			 
			
			
			
			
			
			$delN="delete from promo_last_cute where computer_ip='$this->m_com_ip' and chk='N' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($delN, 2);
			
			
			$upSet_quantity="
			update promo_last_cute as a inner join promo_head as b
			on a.promo_code=b.promo_code				
			set 
				a.set_quantity=b.unitn
			where 
				a.computer_ip='$this->m_com_ip' and 
			    a.chk='Y' and a.set_quantity='0'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($upSet_quantity, 2);
			
			
			//ปรับ set_quantity ห้ามเกินที่กำหนดไว้
			$upSet_quantity="
			update promo_last_cute as a inner join promo_head as b
			on a.promo_code=b.promo_code				
			set 
				a.set_quantity=if(b.multiply='N',b.unitn,a.set_quantity*b.unitn)
			where
				a.computer_ip='$this->m_com_ip'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$this->db->query($upSet_quantity, 2);
			
			


		
			/*
			//delpro ที่ไม่มี Stock เล่น ออก
			$listpro="select * from promo_last_cute where computer_ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$dataLastPro=$this->db->fetchAll($listpro, 2);
			if($dataLastPro){
				foreach($dataLastPro as $dataL){
					$last_promo_code=$dataL['promo_code'];
					$chk="	select 
								a.promo_code 
					
							from 
								promo_detail as a inner join com_stock_master as b
								on a.product_id=b.product_id
							where 
								a.promo_code='$last_promo_code'
								and b.onhand>0
								and b.month=month('$this->doc_date')
								and b.year=year('$this->doc_date')
								
							union all
							select 
								promo_code
							from 
							 	promo_detail
							where
								promo_code='$last_promo_code'
								and product_id='ALL'
							group by 
								promo_code
					";
					//echo $chk;
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
					$dataChk=$this->db->fetchAll($chk, 2);
					if(empty($dataChk)){
						$delpro="delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$last_promo_code' ";
						//echo $delpro."<br>";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$this->db->query($delpro, 2);
					}
				}
			}
			*/

			
			
			//del โปรการเล่มร่วมออก
			$list_pro="select * from promo_last_cute order by promo_code";			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_list_pro=$this->db->fetchAll($list_pro, 2);
			if($data_list_pro){
				foreach($data_list_pro as $data_chk_pro){
					$promo_code_chk=$data_chk_pro['promo_code'];
					$ans_chk=$this->clear_permission_pro($promo_code_chk);
					//echo $ans_chk;
					if($ans_chk=='N'){
						//echo "$promo_code_chk";
						$del_pro=" delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$promo_code_chk' ";
						//echo $del_pro;
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$data_list_pro=$this->db->query($del_pro, 2);
					}
					
				}
			}
					
					
			//ลบโปรที่ไม่มีสิทธิเล่นออก
			$dataPos=$this->dataPos();
			if($dataPos){
				$member_no=$dataPos[0]['member_no'];
			}else{
				$member_no="";
			}
			
			$new_member=$this->chkNewmemberFromBill($member_no);


			if($member_no==""){//ไม่ใช่สมาชิก เอา Y,NM ออก
				$delpro="
					delete promo_last_cute from promo_last_cute inner join promo_head 
					on promo_last_cute.promo_code=promo_head.promo_code
					where 
					promo_last_cute.computer_ip='$this->m_com_ip' and 
					promo_head.member_tp<>'N'
				 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($delpro, 2);
			}else if($member_no!="" && $new_member=="N"){//สมาชิกเก่า เอา NM ออก
				$delpro="
					delete promo_last_cute from promo_last_cute inner join promo_head 
					on promo_last_cute.promo_code=promo_head.promo_code
					where 
					promo_last_cute.computer_ip='$this->m_com_ip' and
					promo_head.member_tp='NM'
				 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($delpro, 2);
			}else if($member_no!="" && $new_member=="Y"){//สมาชิกพึ่งสมัครใหม่
				//ไม่ทำอะไร
			}
			
			
			//delpro ที่เคยเล่นไปแล้วออก เพราะถูกกำหนดไว้ว่าให้เล่นได้กี่ครั้ง
			$chk_limit_pro="
			SELECT  a.promo_code,b.play_member 
			FROM promo_last_cute as a INNER JOIN promo_permission as b
			ON a.promo_code=b.promo_code
			WHERE
			a.computer_ip='$this->m_com_ip'
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_limit_pro=$this->db->fetchAll($chk_limit_pro, 2);
			if($data_limit_pro){
				foreach($data_limit_pro as $promo_chk_limit){
					$promo_code_limit=$promo_chk_limit['promo_code'];
					$play_member=$promo_chk_limit['play_member'];
					$chk_btn_online=$this->chk_btn_online();
					if($chk_btn_online=="Y"){
						$ftp_missing = @fopen("http://$this->sv_online/ims/joke/app_service_op/process/member_missing.php?member_no=$member_no", "r");
						$missing_seq=@fgetss($ftp_missing, 4096);
									
						$chk_bill="				 
						SELECT member_id, count( distinct doc_no ) AS count_play
						FROM `promo_play_history`
						WHERE member_id in($missing_seq)
						AND promo_code = '$promo_code_limit'
						AND flag<>'C'
						GROUP BY member_id
						";
						//echo $chk_bill;
					    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
					    mysql_select_db($this->db_online);
					    $result_chk_bill=mysql_query($chk_bill,$conn_online);
					    $rows_chk_bill=mysql_num_rows($result_chk_bill);
				    
					}else{
						$chk_bill="
							SELECT a.member_id, count( b.promo_code ) AS count_play
							FROM trn_diary1 as a inner join trn_diary2 as b
                             on a.doc_no=b.doc_no
							WHERE a.member_id = '$member_no'
							AND b.promo_code = '$promo_code_limit'
							AND a.flag<>'C'
							GROUP BY a.member_id
						";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$data_chk_pro=$this->db->fetchAll($chk_bill, 2);
						$rows_chk_bill=count($data_chk_pro);
					}
					if($rows_chk_bill>$play_member){
						
						$del_pro="delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$promo_code_limit' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$this->db->query($del_pro, 2);
					}
					
				}
				
			}
			
			
			//del โปรที่ต้องรอ โปรก่อนหน้านี้เล่นครบทุกstepก่อน
			$list_pro="select * from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='OX04330714' ";			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_list_pro=$this->db->fetchAll($list_pro, 2);
			if($data_list_pro){
				$chk_all_step="select * from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and promo_code='OX09310714' and promo_seq='2'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$data_chk_all_step=$this->db->fetchAll($chk_all_step, 2);	
				if(count($data_chk_all_step)<=0){
						//echo "DEL:$promo_code_chk";
						$del_pro=" delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='OX04330714' ";
						//echo $del_pro;
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$data_list_pro=$this->db->query($del_pro, 2);
				}			
				
			}
						
			
			//chk Promotion OPS
			/*$chk_ops= parent::getPromotionDayRef($this->doc_date);
            $val_ops=$chk_ops[1];
			$get_week=substr($val_ops, -1);
			

			$chk_btn_online=$this->chk_btn_online();
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($chk_btn_online=="Y"){//online
				//$jrr_profile=$this->read_profile($member_no);
				//$jrr_profile=json_decode($jrr_profile,true);
				//$ops_customer=$jrr_profile['cust_day'];
				//echo "$member_no!=$_COOKIE[set_member_no]<br>";
				if($member_no!=$_COOKIE["set_member_no"]){
					//echo "FIND OPS<br>";
					$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				    mysql_select_db($this->db_online);
					$find_card="select ops from member_history where member_no='$member_no' ";
					$result_find_card=mysql_query($find_card,$conn_online);
					$data_card=mysql_fetch_array($result_find_card);
					$ops_customer=$data_card['ops'];
					//$this->set_ops=$ops_customer;
					//$this->set_member_no=$member_no;
					setcookie("set_member_no",$member_no);
					setcookie("set_ops",$ops_customer);
				}else{
					$ops_customer=$_COOKIE["set_ops"];
				}
		
			}else{
				$sql_ops="select * from member_card_type where '$member_no' between id_start and id_end limit 1 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
				$result_ops=$this->db->fetchAll($sql_ops, 2);
				if(empty($result_ops)){
					$ops_customer="";
				}else{
					$ops_customer=$result_ops[0]['ops'];
				}
			}
			
			
			if($ops_customer==""){ //หาที่เครื่องอีก
				
				$sql_ops="select * from member_card_type where '$member_no' between id_start and id_end limit 1 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
				$result_ops=$this->db->fetchAll($sql_ops, 2);
				if(empty($result_ops)){
					$ops_customer="";
				}else{
					$ops_customer=$result_ops[0]['ops'];
				}
			}
			
			
			$val_ops_code="OPS$get_week";
			//echo "OPS CUSTOMER:$ops_customer===$val_ops_code<br>";
			if($val_ops_code!=$ops_customer){
				//echo "DELETE PRO OPS<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPS'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
			}
			
			if(substr($member_no,0,2)!="15"){
				//echo "DELETE PRO OPT CASE 15<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPT'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
			}
			
			$val_ops_code="OPT$get_week";
			if($val_ops_code!=$ops_customer){	
				//echo "DELETE PRO OPT<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPT'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);	
			}
			*/
			
			
			
			//chk Promotion OPS
			$chk_ops= parent::getPromotionDayRef($this->doc_date);
            $val_ops=$chk_ops[1];
			$get_week=substr($val_ops, -1);
			

			$chk_btn_online=$this->chk_btn_online();
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($chk_btn_online=="Y"){//online
				//$jrr_profile=$this->read_profile($member_no);
				//$jrr_profile=json_decode($jrr_profile,true);
				//$ops_customer=$jrr_profile['cust_day'];
				//echo "$member_no!=$_COOKIE[set_member_no]<br>";
				if($member_no!=$_COOKIE["set_member_no"]){
					//echo "FIND OPS<br>";
					$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				    mysql_select_db($this->db_online);
					$find_card="select ops from member_history where member_no='$member_no' ";
					$result_find_card=mysql_query($find_card,$conn_online);
					$data_card=mysql_fetch_array($result_find_card);
					$ops_customer=$data_card['ops'];
					//echo "New Find online=$ops_customer<br>";
					//$this->set_ops=$ops_customer;
					//$this->set_member_no=$member_no;
					setcookie("set_member_no",$member_no);
					setcookie("set_ops",$ops_customer);
				}else{
					//echo "Cookie Find online=$ops_customer<br>";
					$ops_customer=$_COOKIE["set_ops"];
				}
		
			}else{
				
				$sql_ops="select * from member_card_type where '$member_no' between id_start and id_end limit 1 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
				$result_ops=$this->db->fetchAll($sql_ops, 2);
				if(empty($result_ops)){
					$ops_customer="";
				}else{
					$ops_customer=$result_ops[0]['ops'];
				}
				
				//echo "Find Offline=$ops_customer<br>";
			}
			
			if($ops_customer==""){ //หาที่เครื่องอีก
				
				$sql_ops="select * from member_card_type where '$member_no' between id_start and id_end limit 1 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_special_day");
				$result_ops=$this->db->fetchAll($sql_ops, 2);
				if(empty($result_ops)){
					$ops_customer="";
				}else{
					$ops_customer=$result_ops[0]['ops'];
				}
			}
			
			//unlock
			if($this->doc_date=="2014-07-29" && substr($ops_customer,0,3)=="OPT"){
				$ops_customer="OPT5";
			}
			if($this->doc_date=="2014-07-31" && substr($ops_customer,0,3)=="OPS"){
				$ops_customer="OPS5";
			}			
			
			$val_ops_code="OPS$get_week";
			$date_bill =new DateTime($this->doc_date);
			$weekly=$date_bill->format('D');
			
			//$ops_customer=$this->getOpsDayMember();
			//echo "wirat-OPS-CUSTOMER:$ops_customer<br>";
			//echo "OPS CUSTOMER:$ops_customer===$val_ops_code<br>";
			if($weekly!="Thu" && $weekly!="Tue"){//ไม่ใช่วัน d day
				//echo "DELETE PRO ไม่ใช่วัน D day<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp in('OPS','OPT','OPSOPT')
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
				//echo $del_pro_ops;
			}else if($weekly!="Thu"){//ไม่ใช่พฤหัส
				//echo "DELETE PRO วันพฤหัส<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPS'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
				//echo $del_pro_ops;
			} else if($weekly!="Tue"){//ไม่ใช่วัน อังคาร
				//echo "DELETE PRO วัน อังคาร<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPT'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
				//echo $del_pro_ops;
			}			

			//ถ้ามาไม่ตรงวัน
			if(substr($ops_customer,0,3)=="OPS" && $weekly!="Thu"){//
				//echo "DELETE PRO บัตร ops มาไม่ตรงวันพฤหัส<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp in('OPS','OPT','OPSOPT')
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
			}

			if(substr($ops_customer,0,3)=="OPT" && $weekly!="Tue"){//
				//echo "DELETE PRO บัตร opt มาไม่ตรงวันอังคาร<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp in('OPS','OPT','OPSOPT')
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
			}			

			
			if($val_ops_code!=$ops_customer){ //มาไม่ตรงวัน ops
				//echo "DELETE มาไม่ตรง OPSbr>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPS'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
				//echo $del_pro_ops;
			}
			
			
			//chk opt
			if(substr($member_no,0,2)!="15"){
				//echo "DELETE PRO OPT CASE 15<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp ='OPT'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);
				//echo $del_pro_ops;
			}
			
			$val_ops_code="OPT$get_week";
			if($val_ops_code!=$ops_customer){	
				//echo "DELETE มาไม่ตรง OPTbr>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp ='OPT'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);	
				//echo $del_pro_ops;
			}
			
			if(substr($ops_customer,-1)!=$get_week){	
				//echo "DELETE PRO OPSOPT<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPSOPT'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);	
				//echo $del_pro_ops;
			}			

			//echo "Type:card=".substr($ops_customer,0,3) . "<br>";
			//pro dday ที่เล่นได้ทุกวัน
			if(substr($ops_customer,0,3)=="OPS"){	//ตัดโปรของบัตร OPT ออก
				//echo "DELETE PRO OPSOPT<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPTALL'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);	
				//echo $del_pro_ops;
			}	
			if(substr($ops_customer,0,3)=="OPT"){	//ตัดโปรของบัตร OPS ออก
				//echo "DELETE PRO OPSOPT<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp='OPSALL'
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);	
				//echo $del_pro_ops;
			}
			if(substr($ops_customer,0,3)==""){	//ตัดโปรของบัตร OPS ออก
				//echo "DELETE PRO OPSOPT<br>";
				$del_pro_ops="
				delete promo_last_cute from promo_last_cute inner join promo_head
				on promo_last_cute.promo_code=promo_head.promo_code
				where 
				promo_last_cute.computer_ip='$this->m_com_ip'
				and promo_head.promo_tp in('OPSALL','OPTALL')
				";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				$this->db->query($del_pro_ops, 2);	
				//echo $del_pro_ops;
			}
			
			//del pro by member
			if($member_no!=""){
				$del_pro_member="
					select 
							*
					 from promo_last_cute as a inner join promo_head as b
					 on a.promo_code=b.promo_code
					 
					where 
						b.channel='MEMBER'
				";
				
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$data_pro_member=$this->db->fetchAll($del_pro_member, 2);
					foreach($data_pro_member as $data_pro){
						$promo_code_chk=$data_pro['promo_code'];
						$chk_pro="select * from log_member_privilege where member_no='$member_no' and doc_date='$this->doc_date' and promo_code='$promo_code_chk' and flag<>'C' ";
						//echo $chk_pro;
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$data_chk_pro=$this->db->fetchAll($chk_pro, 2);
						$count_data_chk_pro=count($data_chk_pro);
						if($count_data_chk_pro==0){
							$del_pro="delete from promo_last_cute where promo_code='$promo_code_chk' ";
							
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
							$this->db->query($del_pro, 2);
						}
					
					}
		
			}
			
			
						
			//deletepro play by month
			/*$list_pro="select * from promo_last_cute order by promo_code";			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_list_pro=$this->db->fetchAll($list_pro, 2);
			if($data_list_pro){
				foreach($data_list_pro as $data_chk_pro){
					$promo_code_chk=$data_chk_pro['promo_code'];
					if($promo_code_chk=="OX08020814"){
						$ans_chk=$this->clear_probymonth($promo_code,$member_no,$this->doc_date);
						//echo $ans_chk;
						if($ans_chk=='Del'){
							//echo "$promo_code_chk";
							$del_pro=" delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$promo_code_chk' ";
							//echo $del_pro;
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
							$data_list_pro=$this->db->query($del_pro, 2);
						}
					}
					
				}
			}*/
			
			
			//deletepro play by month on Limit
			/*$list_pro="select * from promo_last_cute order by promo_code";			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_list_pro=$this->db->fetchAll($list_pro, 2);
			if($data_list_pro){
				foreach($data_list_pro as $data_chk_pro){
					$promo_code_chk=$data_chk_pro['promo_code'];
					$find_pro="select * from promo_head where promo_code='$promo_code_chk' ";
					//echo $del_pro;
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
					$data_pro=$this->db->fetchAll($find_pro, 2);
					$start_date=$data_pro[0]['start_date'];
					$end_date=$data_pro[0]['end_date'];
									
					if($promo_code_chk=="OX08020814"){
						$ans_chk=$this->clear_probymonth_onlimit($promo_code_chk,$start_date,$end_date,$member_no,$this->doc_date,6,3);
						//echo $ans_chk;
						if($ans_chk=='Del'){
							//echo "$promo_code_chk";
							//$del_pro=" delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code='$promo_code_chk' ";
							//echo $del_pro;
							//$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
							//$data_list_pro=$this->db->query($del_pro, 2);
						}
					}
					
				}
			}	*/		
			
			$list_pro="select * from promo_last_cute where promo_code in('OX07320814','OX07561014','OX07571014','OX07581014','OX07041114','OX07051114')";			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_list_pro=$this->db->fetchAll($list_pro, 2);
			if($data_list_pro){
				foreach($data_list_pro as $data_chk_pro){
					$promo_code_chk=$data_chk_pro['promo_code'];
					
					$ans_chk=$this->clear_probyone($promo_code,$member_no,$this->doc_date);
					//echo $ans_chk;
					if($ans_chk=='Del'){
						//echo "$promo_code_chk";
						$del_pro=" delete from promo_last_cute where computer_ip='$this->m_com_ip' and promo_code in('OX07320814','OX07561014','OX07571014','OX07581014','OX07041114','OX07051114') ";
						//echo $del_pro;
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
						$data_list_pro=$this->db->query($del_pro, 2);
					}
					
					
				}
			}	
			
						
			
			
			
				
		}
		
		
		//ลบโปรที่เล่นได้แค่เดือนเดียวออก
		function clear_probymonth($promo_code,$member_no,$doc_date){
			$day=date("d",strtotime($doc_date));
			$month=date("m",strtotime($doc_date));
			$year=date("Y",strtotime($doc_date));
			
			$chk="
				select * from trn_diary2 as a inner join trn_diary1 as b
				on a.doc_no=b.doc_no
				where
				month(a.doc_date)='$month' and 
				a.promo_code='$promo_code' and
				a.flag<>'C' and 
				month(b.doc_date)='$month' and 
				b.flag<>'C' and 
				b.member_id='$member_no'
			";

			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_chk=$this->db->fetchAll($chk, 2);
			$count_chk=count($data_chk);
			if($count_chk>0){
				return "Del";
				
			}else{
				$chk_btn_online=$this->chk_btn_online();
				if($chk_btn_online=="Y"){//ถ้า online ได้
					$chkonline="select * from promo_play_history
					where
					month(doc_date)='$month' and 
					promo_code='$promo_code' and
					flag<>'C' and
					member_id='$member_no'
					
					";

				    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				    mysql_select_db($this->db_online);
				    $result_chkonline=mysql_query($chkonline,$conn_online);
				    $rows_chkonline=mysql_num_rows($result_chkonline);
					if($rows_chkonline>0){
						return "Del";
						
					}
				}else{
					return "Play";
				}
			}
			

			
		}
		
		function clear_probymonth_onlimit($promo_code,$start_date,$end_date,$member_no,$doc_date,$limitall,$limitmonth){
			$day=date("d",strtotime($doc_date));
			$month=date("m",strtotime($doc_date));
			$year=date("Y",strtotime($doc_date));
			
			$chk_btn_online=$this->chk_btn_online();
			if($chk_btn_online=="Y"){//ถ้า online ได้
				//seq missing
				$ftp_missing = @fopen("http://$this->sv_online/ims/joke/app_service_op/process/member_missing.php?member_no=$member_no", "r");
				$missing_seq=@fgetss($ftp_missing, 4096);
				$cr_member="b.member_id in($missing_seq) ";
				$cr_member_online="member_id in($missing_seq)";
			}else{
				$cr_member="b.member_id='$member_no'";
				$cr_member_online="member_id='$member_no'";
			}
				
				

												
			$chk="select * from trn_diary2 as a inner join trn_diary1 as b
			on a.doc_no=b.doc_no
			where
			a.doc_date between '$start_date' and '$end_date' and
			b.doc_date between '$start_date' and '$end_date' and						
			month(a.doc_date)='$month' and 
			a.promo_code='$promo_code' and
			a.flag<>'C' and 
			month(b.doc_date)='$month' and 
			b.flag<>'C' and 
			$cr_member
			group by a.doc_no 
			";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_chk=$this->db->fetchAll($chk, 2);
			$count_chk=count($data_chk);
			if($count_chk>$limitmonth){
				return "Del";
				
			}else{
				
				if($chk_btn_online=="Y"){//ถ้า online ได้
					$chkonline="select * from promo_play_history
					where
					doc_date between '$start_date' and '$end_date' and
					month(doc_date)='$month' and 
					promo_code='$promo_code' and
					flag<>'C' and
					$cr_member_online
					group by doc_no
					
					";
					//echo $chkonline;
				    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				    mysql_select_db($this->db_online);
				    $result_chkonline=mysql_query($chkonline,$conn_online);
				    $rows_chkonline=mysql_num_rows($result_chkonline);
					if($rows_chkonline>$limitmonth){
						return "Del";
						
					}
				}else{
					return "Play";
				}
			}
			

			
		}	
		
		function clear_probyone($promo_code,$member_no,$doc_date){
			$day=date("d",strtotime($doc_date));
			$month=date("m",strtotime($doc_date));
			$year=date("Y",strtotime($doc_date));
			$chk02="select * from trn_diary1 where  member_id='$member_no' and status_no='02'	 and flag<>'C'		";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_chk02=$this->db->fetchAll($chk02, 2);
			$count_chk02=count($data_chk02);
			if($count_chk02>0){
				return "Del";
				
			}
			$chk="select * from trn_diary2 as a inner join trn_diary1 as b
			on a.doc_no=b.doc_no
			where
			a.doc_date between '2014-11-01' and '2015-12-31' and 
			a.promo_code in('OX07320814','OX07561014','OX07571014','OX07581014','OX07041114','OX07051114') and
			a.flag<>'C' and 
			b.doc_date between '2014-11-01' and '2014-12-31' and 
			b.flag<>'C' and 
			b.member_id='$member_no'
			";

			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
			$data_chk=$this->db->fetchAll($chk, 2);
			$count_chk=count($data_chk);
			if($count_chk>0){
				return "Del";
				
			}else{
				$chk_btn_online=$this->chk_btn_online();
				if($chk_btn_online=="Y"){//ถ้า online ได้
					$chkonline="select * from promo_play_history
					where
					doc_date between '2014-11-01' and '2015-12-31' and 
					promo_code in('OX07320814','OX07561014','OX07571014','OX07581014','OX07041114','OX07051114') and
					flag<>'C' and
					member_id='$member_no'
					
					";

				    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				    mysql_select_db($this->db_online);
				    $result_chkonline=mysql_query($chkonline,$conn_online);
				    $rows_chkonline=mysql_num_rows($result_chkonline);
					if($rows_chkonline>0){
						return "Del";
						
					}
				}else{
					return "Play";
				}
			}
			

			
		}	
		
		
				
		//ตัดโปรที่ห้ามเล่นออก
		function clear_permission_pro($promo_code){

			$find="select * from promo_check where promo_code='$promo_code' and promo_group='PROMOTION' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
			$data=$this->db->fetchAll($find, 2);
			if($data){

				//ต้องเล่นโปรนี้มาก่อน
				$count_pro="select * from promo_check where promo_code='$promo_code' and promo_group='PROMOTION'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
				$data_count_pro=$this->db->fetchAll($count_pro, 2);
				$num_count_pro=count($data_count_pro);
				
				$chk="
						select distinct a.promo_code 
						from 
							trn_promotion_tmp1 as a inner join promo_check as b
							on a.promo_code=b.promo_play
						where
							a.computer_ip='$this->m_com_ip' and
							b.promo_code='$promo_code' and 
							b.promo_group='PROMOTION'
				";
				//echo $chk."<br>";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
				$dataChk=$this->db->fetchAll($chk, 2);
				$num_chk=count($dataChk);
				//echo "$num_count_pro=$num_chk";
				if($num_count_pro!=$num_chk){//เล่นครบทุกโปร
					return "N";//เล่นไม่ได้
				}
			}
			
			
			$find="select * from promo_check where promo_code='$promo_code' and promo_group='NOPRO' ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
			$data=$this->db->fetchAll($find, 2);
			if($data){


						$chk="
								select distinct a.promo_code 
								from 
									trn_promotion_tmp1 as a inner join promo_check as b
									on a.promo_code=b.promo_play
								where
									a.computer_ip='$this->m_com_ip' and
									b.promo_code='$promo_code' and 
									b.promo_group='NOPRO'
						";
						//echo $chk."<br>";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_check");
						$dataChk=$this->db->fetchAll($chk, 2);
						if($dataChk){
							return "N";//เล่นไม่ได้
						}
				
					
			}else{
				return "Y";//ไม่ได้กำหนดเล่นได้
			}
			
			
			return "Y"; //ผ่านหมดเล่นได้
			
		}
		
		
		
		
		function chk_pro_free($promo_code){
		  	  //Show Pro by level
			  $sql="
				  select distinct type_discount
				  from
				   promo_detail
				  where 
				   promo_code='$promo_code'
			   ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data = $this->db->fetchAll($sql, 2);
			  if(count($data)>0){
			  		foreach($data as $x){
			  			$type_discount=$x['type_discount'];
			  			if($type_discount!="Free"){
			  				return "NOFREE";
			  				
			  			}
			  			
			  		}
			  		return "FREE";
			  }else{
			  		return "NOFREE";
			  }
			
		}
		
		
		
		
		
		//last bill ########################################################################################
		function findProByAmtlastbill() {
			  $branch_id=$this->m_branch_id;
			  
			  
			  //ถ้าเล่นโปรนี้ไปแล้วให้หยุด ไปชำระเงินเลย
			  $find_chk="select * from trn_promotion_tmp1 where doc_no='$this->m_com_ip' and promo_code in('OX09040714','OX09050714','OX09060714','OX09130714', 'OX09140714', 'OX09150714', 'OX09160714', 'OX09170714', 'OX09180714') ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $dataLevel_find_chk = $this->db->fetchAll($find_chk, 2);
			  if($dataLevel_find_chk){
			  	return "stopAmt";
			  }
			  
			
		  	  //Show Pro by level
			  $levelPro="
				  select distinct a.level 
				  from
				   promo_head as a inner join promo_branch as b
				   on a.promo_code=b.promo_code
				  where 
				   '$this->doc_date' between a.start_date and  a.end_date and
				   a.promo_pos<>'M' and
				   '$this->doc_date' between b.start_date and b.end_date and
				   b.branch_id in('ALL','$branch_id') and
				   a.promo_code not in(select promo_code from promo_last_cancle where computer_ip='$this->m_com_ip') and
				   a.level not in(select level from promo_last_net where computer_ip='$this->m_com_ip' and process='F')
				   
				  order by 
				   a.level ASC limit 1
			   ";
			  
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $dataLevel = $this->db->fetchAll($levelPro, 2);
			  
			  if($dataLevel){
			  	$level=$dataLevel[0]['level'];
			  }else{//หมด Level
			  	return "stopAmt";
			  }
			 
				  
				  
			for($i=1; $i<=2; $i++){
			
				  $clearTmp="delete from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'";
				  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				  $this->db->query($clearTmp, 2);

				$this->cutepromolast($level);
				$chkPro="
				  		select 
				  			a.promo_code,a.promo_des,a.level 
				  		from 
							promo_last_cute as a inner join promo_branch as b
							on a.promo_code=b.promo_code
						where
							a.computer_ip='$this->m_com_ip' and 
							b.branch_id in('ALL','$branch_id') and
							a.level='$level' and
							a.promo_price in('G','N') and
							a.level not in(select level from promo_last_net where computer_ip='$this->m_com_ip' and process='F') and
							a.promo_code not in(select promo_code from promo_last_cancle where computer_ip='$this->m_com_ip')
						order by a.promo_des
				  		
				  	";
					
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				  	$dataPro = $this->db->fetchAll($chkPro, 2);
				  	if($dataPro){//มีโปร
				  		return $dataPro;
				  	}else{//หา level ถัดไป
				  	  //Show Pro by level
					  $levelPro="
						  select distinct a.level 
						  from
						   promo_head as a inner join promo_branch as b
						   on a.promo_code=b.promo_code
						  where 
						   a.level>'$level' and 
						   '$this->doc_date' between a.start_date and  a.end_date and
						   a.promo_pos<>'M' and
			
						   '$this->doc_date' between b.start_date and b.end_date and
						   b.branch_id in('ALL','$branch_id') and
						   a.promo_code not in(select promo_code from promo_last_cancle where computer_ip='$this->m_com_ip') and
						   a.level not in(select level from promo_last_net where computer_ip='$this->m_com_ip' and process='F')
						   
						  order by 
						   a.level ASC limit 1
					  ";
					  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
					  $dataLevel = $this->db->fetchAll($levelPro, 2);
					  
				  	  if($dataLevel){
				  	  	$level=$dataLevel[0]['level'];
				  	  }else{
			  	        return "stopAmt";
			          } 
					  
				  		$i=1;
				  	}
			  	
			  	
			}//end loop
			  	
			  	
		}//end function
		
		
		//last bill
		function findProByQtylastbill() {
			$branch_id=$this->m_branch_id;
			
		  	  //Show Pro by level
			  $levelPro="
				  select 
				  	  distinct a.level 
				  from 
					  promo_head  as a inner join promo_branch as b
					  on a.promo_code=b.promo_code
	
				  where 
				  	 '$this->doc_date' between a.start_date and a.end_date and
				   	 a.promo_pos<>'M' and
		
					 '$this->doc_date' between b.start_date and b.end_date and
					 b.branch_id in('ALL','$branch_id') and
					 a.promo_code not in(select promo_code from promo_last_cancle where computer_ip='$this->m_com_ip') and
					 a.level not in(select level from promo_last_net where computer_ip='$this->m_com_ip' and process='F')
				  order by 
				   	a.level ASC limit 1
			   ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $dataLevel = $this->db->fetchAll($levelPro, 2);
			  
			  if($dataLevel){
			  	$level=$dataLevel[0]['level'];
			  }else{
			  	return "stopQty";
			  }
			 
				  
				  
			for($i=1; $i<=2; $i++){
			
				$clearTmp="delete from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
				$this->db->query($clearTmp, 2);

				$this->cutepromolast($level);
				$chkPro="
				  		select 
				  			a.promo_code,a.promo_des,a.level 
				  		from 
							promo_last_cute as a inner join promo_branch as b
							on a.promo_code=b.promo_code
						where
							a.computer_ip='$this->m_com_ip' and 
							b.branch_id in('ALL','$branch_id') and
							a.level='$level' and
							a.promo_price='U' and 
							a.level not in(select level from promo_last_net where computer_ip='$this->m_com_ip' and process='F') and
							a.promo_code not in(select promo_code from promo_last_cancle where computer_ip='$this->m_com_ip')
						order by a.promo_des
				  	";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cute");
				  	$dataPro = $this->db->fetchAll($chkPro, 2);
				  	if($dataPro){
				  		return $dataPro;
				  	}else{
				  	  //Show Pro by level
					  $levelPro="
						  select 
						  	  distinct a.level 
						  from 
							  promo_head  as a inner join promo_branch as b
							  on a.promo_code=b.promo_code
			
						  where 
						     a.level>'$level' and   
						  	 '$this->doc_date' between a.start_date and a.end_date and
						   	 a.promo_pos<>'M' and
						   
							 '$this->doc_date' between b.start_date and b.end_date and
							 b.branch_id in('ALL','$branch_id') and
							 a.promo_code not in(select promo_code from promo_last_cancle where computer_ip='$this->m_com_ip') and
							 a.level not in(select level from promo_last_net where computer_ip='$this->m_com_ip' and process='F')
						  order by 
						   	a.level ASC limit 1
					  ";
					  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
					  $dataLevel = $this->db->fetchAll($levelPro, 2);
					  
				  	  if($dataLevel){
				  	  	$level=$dataLevel[0]['level'];
				  	  }else{
			  	        return "stopQty";
			          } 
					  
				  		$i=1;
				  	}
			  	
			  	
			}//end loop
			  	
			  	
		}//end function

		
		
		function cancle_stock(){
	 		  $dataScan="select * from trn_promotion_tmp2 where computer_ip='$this->m_com_ip' order by product_id";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 		  foreach($dataScan as $data){
	 		  	$product_id=$data['product_id'];
	 		  	$quantity=$data['quantity'];
	 		  	
	 		  	$up="
	 		  	update 
	 		  		com_stock_master
	 		  		set 
	 		  			onhand=onhand+$quantity
	 		  		where
	 		  			month=month('$this->doc_date') and
	 		  			year=year('$this->doc_date') and
	 		  			product_id='$product_id'
	 		  	";
	 		  	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
	 		  	$this->db->query($up, 2);
	 		  	
	 		  }
	 		  
	 		  $clear_tmp2="delete from trn_promotion_tmp2 where computer_ip='$this->m_com_ip'";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->query($clear_tmp2, 2);
	 		  

			
		}
		
		
		function cancle_stock_auto(){
	 		  $dataScan="select * from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip' order by product_id";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 		  foreach($dataScan as $data){
	 		  	$product_id=$data['product_id'];
	 		  	$quantity=$data['quantity'];
	 		  	
	 		  	$up="
	 		  	update 
	 		  		com_stock_master
	 		  		set 
	 		  			onhand=onhand+$quantity
	 		  		where
	 		  			month=month('$this->doc_date') and
	 		  			year=year('$this->doc_date') and
	 		  			product_id='$product_id'
	 		  	";
	 		  	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
	 		  	$this->db->query($up, 2);
	 		  	
	 		  }
	 		  
	 		  $clear_tmp2="delete from trn_promotion_tmp_auto where computer_ip='$this->m_com_ip'";
	 		  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp2");
	 		  $dataScan = $this->db->query($clear_tmp2, 2);
	 		  

			
		}
		
		
		function canclelastpro($promo_code){//ยกเลิกโปรที่อยู่ใน Level เดียวกันทั้งหมด
			  $branch_id=$this->m_branch_id;
			
			  //หาโปรโมชั้นที่อยู่ใน Level เดียวกันกับโปรที่ยกเลิกนี้ เพื่อยกเลิกทั้ังหมด และให้ข้ามไป Level ถัดไป
			  $find_level="select level from promo_head where promo_code='$promo_code' limit 0,1";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data_level=$this->db->fetchAll($find_level, 2);
			  $level=$data_level[0]['level'];
			  
			  $sql="
			  			  insert into promo_last_cancle(computer_no,computer_ip,promo_code,level)
						  select 
						  	  '$this->m_computer_no','$this->m_com_ip',a.promo_code,'$level'
						  from 
							  promo_head  as a inner join promo_branch as b
							  on a.promo_code=b.promo_code
			
						  where  
						  	 '$this->doc_date' between a.start_date and a.end_date and
						   	 a.promo_pos<>'M' and
							 '$this->doc_date' between b.start_date and b.end_date and
							 b.branch_id in('ALL','$branch_id') and
							 a.level='$level'	 
			  ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cancle");
	 		  $this->db->query($sql, 2);

		}
		
		
		function canclelastpro_one($promo_code){//ยกเลิกโปรนี้โปรเดียว
			
			  //หาโปรโมชั้นที่อยู่ใน Level เดียวกันกับโปรที่ยกเลิกนี้ เพื่อยกเลิกทั้ังหมด และให้ข้ามไป Level ถัดไป
			  $find_level="select level from promo_head where promo_code='$promo_code' limit 0,1";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data_level=$this->db->fetchAll($find_level, 2);
			  $level=$data_level[0]['level'];
			  
			  $sql="insert into promo_last_cancle(computer_no,computer_ip,promo_code,level) values('$this->m_computer_no','$this->m_com_ip','$promo_code','$level')";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cancle");
			  //echo $sql;
	 		  $this->db->query($sql, 2);
	 		  
		}
		
		
		function lastdelpro(){//ลบรายการที่เล่นโปรท้ายบิลออก
			
			  $dataScan="select * from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and promo_pos='L' order by product_id";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 		  foreach($dataScan as $data){
	 		  	$product_id=$data['product_id'];
	 		  	$quantity=$data['quantity'];
	 		  	
	 		  	$up="
	 		  	update 
	 		  		com_stock_master
	 		  		set 
	 		  			onhand=onhand+$quantity
	 		  		where
	 		  			month=month('$this->doc_date') and
	 		  			year=year('$this->doc_date') and
	 		  			product_id='$product_id'
	 		  	";
	 		  	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
	 		  	$this->db->query($up, 2);
	 		  	
	 		  }
	 		  
	 		  
			  $sql="delete from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and promo_pos='L'";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
	 		  $this->db->query($sql, 2);
	 		  
	 		  //clear last tmp
			  $sql="delete from promo_last_cancle where computer_ip='$this->m_com_ip'";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_cancle");
	 		  $this->db->query($sql, 2);
	 		  
			  $sql="delete from promo_last_net where computer_ip='$this->m_com_ip'";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_last_net");
	 		  $this->db->query($sql, 2);
	 		  

		}
		
		
		function deltmp1($seq){//ลบรายการในตาราง tmp_promotion_tmp1
			  $dataScan="select * from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and seq='$seq' order by product_id";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
	 		  $dataScan = $this->db->fetchAll($dataScan, 2);
	 		  foreach($dataScan as $data){
	 		  	$product_id=$data['product_id'];
	 		  	$quantity=$data['quantity'];
	 		  	
	 		  	$up="
	 		  	update 
	 		  		com_stock_master
	 		  		set 
	 		  			onhand=onhand+$quantity
	 		  		where
	 		  			month=month('$this->doc_date') and
	 		  			year=year('$this->doc_date') and
	 		  			product_id='$product_id'
	 		  	";
	 		  	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_stock_master");
	 		  	$this->db->query($up, 2);
	 		  	
	 		  }
			  
			  $sql="delete from trn_promotion_tmp1 where computer_ip='$this->m_com_ip' and seq='$seq'";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
	 		  $this->db->query($sql, 2);
	 		  
		}
		
		function mail_promotion(){
				return  "bill_active";
		}
		
		function re_chk_bill(){
			  $clear="delete  FROM `service_log` WHERE doc_date<date_sub('$this->doc_date',interval 1 day)";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			  $this->db->query($clear, 2);
			  
			  $max_time="select * from service_log  where doc_date='$this->doc_date' order by rechk_up desc";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			  $datamax_time=$this->db->fetchAll($max_time, 2);
			  if($datamax_time){
			  	$max_time_chk=$datamax_time[0]['rechk_up'];
			  }else{
			  	$max_time_chk=$this->doc_date . " 00:00:00";
			  }
			  $find="
			    SELECT a.doc_no,a.doc_time FROM 
				trn_diary1 as a 
				where
				a.doc_date='$this->doc_date' and 
				a.doc_time>time('$max_time_chk')
				order by a.doc_time
			 ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			  $datachk=$this->db->fetchAll($find, 2);
			  foreach($datachk as $rechk){
			  	$doc_no=$rechk['doc_no'];
			  	
				$this->send_bill($doc_no);

				
			  }
			  

		}
		
		function re_chk_new_member(){

			  $find="select * from service_log where type_case='NEWMEMBER' and rechk=''  ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
			  $datachk=$this->db->fetchAll($find, 2);
			  foreach($datachk as $rechk){
			  	$doc_no=$rechk['doc_no'];
			  	
				$ans=$this->send_new_member($doc_no);
				if($ans=="Y"){
				  $chk_ok="update service_log set rechk='Y',rechk_up=now() where type_case='NEWMEMBER' and rechk='' and doc_no='$doc_no'  ";
				  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_promotion_tmp1");
				  $this->db->query($chk_ok, 2);
				}
				
			  }
			  

		}
		
		
		
		
		
		function send_bill($doc_no){
			

			    
			$bill="select *,last_day(doc_date+ interval 730 day) as expire_date from trn_diary1 where doc_no='$doc_no'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 		$databill=$this->db->fetchAll($bill, 2);
			$corporation_id=$databill[0]['corporation_id'];
			$company_id=$databill[0]['company_id'];
			$branch_id=$databill[0]['branch_id'];
			$doc_date=$databill[0]['doc_date'];
			$doc_time=$databill[0]['doc_time'];
			$doc_no=$databill[0]['doc_no'];
			$doc_tp=$databill[0]['doc_tp'];
			$status_no=$databill[0]['status_no'];
			$flag=$databill[0]['flag'];
			$member_id=$databill[0]['member_id']; 
			$member_percent=$databill[0]['member_percent'];
			$special_percent=$databill[0]['special_percent'];
			$refer_member_id=$databill[0]['refer_member_id'];
			$refer_doc_no=$databill[0]['refer_doc_no'];
			$quantity=$databill[0]['quantity'];
			$amount=$databill[0]['amount'];
			$discount=$databill[0]['discount'];
			$member_discount1=$databill[0]['member_discount1'];
			$member_discount2=$databill[0]['member_discount2'];
			$co_promo_discount=$databill[0]['co_promo_discount'];
			$coupon_discount=$databill[0]['coupon_discount'];
			$special_discount=$databill[0]['special_discount'];
			$other_discount=$databill[0]['other_discount'];
			$net_amt=$databill[0]['net_amt'];
			$vat=$databill[0]['vat'];
			$ex_vat_amt=$databill[0]['ex_vat_amt'];
			$ex_vat_net=$databill[0]['ex_vat_net'];
			$coupon_cash=$databill[0]['coupon_cash'];
			$coupon_cash_qty=$databill[0]['coupon_cash_qty'];
			$point1=$databill[0]['point1'];
			$point2=$databill[0]['point2'];
			$redeem_point=$databill[0]['redeem_point'];
			$total_point=$databill[0]['total_point'];
			$co_promo_code=$databill[0]['co_promo_code'];
			$coupon_code=$databill[0]['coupon_code'];
			$special_promo_code=$databill[0]['special_promo_code'];
			$other_promo_code=$databill[0]['other_promo_code'];
			$application_id=$databill[0]['application_id'];
			$new_member_st=$databill[0]['new_member_st'];
			$birthday_card_st=$databill[0]['birthday_card_st'];
			$not_cn_st=$databill[0]['not_cn_st'];
			$paid=$databill[0]['paid'];
			$pay_cash=$databill[0]['pay_cash'];
			$pay_credit=$databill[0]['pay_credit'];
			$pay_cash_coupon=$databill[0]['pay_cash_coupon'];
			$credit_no=$databill[0]['credit_no'];
			$credit_tp=$databill[0]['credit_tp'];
			$bank_tp=$databill[0]['bank_tp'];
			$change=$databill[0]['change'];
			$pos_id=$databill[0]['pos_id'];
			$computer_no=$databill[0]['computer_no'];
			$user_id=$databill[0]['user_id'];
			$cashier_id=$databill[0]['cashier_id'];
			$saleman_id=$databill[0]['saleman_id'];
			$cn_status=$databill[0]['cn_status'];
			$refer_cn_net=$databill[0]['refer_cn_net'];
			$name=$databill[0]['name'];
			$address1=$databill[0]['address1'];
			$address2=$databill[0]['address2'];
			$address3=$databill[0]['address3'];
			$doc_remark=$databill[0]['doc_remark'];
			$create_date=$databill[0]['create_date'];
			$create_time=$databill[0]['create_time'];
			$cancel_id=$databill[0]['cancel_id'];
			$cancel_date=$databill[0]['cancel_date'];
			$cancel_time=$databill[0]['cancel_time'];
			$cancel_tp=$databill[0]['cancel_tp'];
			$cancel_remark=$databill[0]['cancel_remark'];
			$cancel_auth=$databill[0]['cancel_auth'];
			$keyin_st=$databill[0]['keyin_st'];
			$keyin_tp=$databill[0]['keyin_tp'];
			$keyin_remark=$databill[0]['keyin_remark'];
			$post_id=$databill[0]['post_id'];
			$post_date=$databill[0]['post_date'];
			$post_time=$databill[0]['post_time'];
			$transfer_ts=$databill[0]['transfer_ts'];
			$transfer_date=$databill[0]['transfer_date'];
			$transfer_time=$databill[0]['transfer_time'];
			$order_id=$databill[0]['order_id'];
			$order_no=$databill[0]['order_no'];
			$order_date=$databill[0]['order_date'];
			$order_time=$databill[0]['order_time'];
			$acc_name=$databill[0]['acc_name'];
			$bank_acc=$databill[0]['bank_acc'];
			$bank_name=$databill[0]['bank_name'];
			$tel1=$databill[0]['tel1'];
			$tel2=$databill[0]['tel2'];
			$dn_name=$databill[0]['dn_name'];
			$dn_address1=$databill[0]['dn_address1'];
			$dn_address2=$databill[0]['dn_address2'];
			$dn_address3=$databill[0]['dn_address3'];
			$remark1=$databill[0]['remark1'];
			$remark2=$databill[0]['remark2'];
			$deleted=$databill[0]['deleted'];
			$print_no=$databill[0]['print_no'];
			$reg_date=$databill[0]['reg_date'];
			$reg_time=$databill[0]['reg_time'];
			$reg_user=$databill[0]['reg_user'];
			$upd_date=$databill[0]['upd_date'];
			$upd_time=$databill[0]['upd_time'];
			$upd_user=$databill[0]['upd_user'];
			$expire_date=$databill[0]['expire_date'];
			$bill_time=$doc_date . " " .$doc_time;

		 		
		  	$up="
		  	insert into trn_diary1(corporation_id, company_id, branch_id, doc_date, doc_time, doc_no, doc_tp, status_no, flag, member_id, member_percent, special_percent, refer_member_id, refer_doc_no, quantity, amount, discount, member_discount1, member_discount2, co_promo_discount, coupon_discount, special_discount, other_discount, net_amt, vat, ex_vat_amt, ex_vat_net, coupon_cash, coupon_cash_qty, point1, point2, redeem_point, total_point, co_promo_code, coupon_code, special_promo_code, other_promo_code, application_id, new_member_st, birthday_card_st, not_cn_st, paid, pay_cash, pay_credit, pay_cash_coupon, credit_no, credit_tp, bank_tp, `change`, pos_id, computer_no, user_id, cashier_id, saleman_id, cn_status, refer_cn_net, name, address1, address2, address3, doc_remark, create_date, create_time, cancel_id, cancel_date, cancel_time, cancel_tp, cancel_remark, cancel_auth, keyin_st, keyin_tp, keyin_remark, post_id, post_date, post_time, transfer_ts, transfer_date, transfer_time, order_id, order_no, order_date, order_time, acc_name, bank_acc, bank_name, tel1, tel2, dn_name, dn_address1, dn_address2, dn_address3, remark1, remark2, deleted, print_no, reg_date, reg_time, reg_user, upd_date, upd_time, upd_user,time_up) values('$corporation_id','$company_id','$branch_id','$doc_date','$doc_time','$doc_no','$doc_tp','$status_no','$flag','$member_id','$member_percent','$special_percent','$refer_member_id','$refer_doc_no','$quantity','$amount','$discount','$member_discount1','$member_discount2','$co_promo_discount','$coupon_discount','$special_discount','$other_discount','$net_amt','$vat','$ex_vat_amt','$ex_vat_net','$coupon_cash','$coupon_cash_qty','$point1','$point2','$redeem_point','$total_point','$co_promo_code','$coupon_code','$special_promo_code','$other_promo_code','$application_id','$new_member_st','$birthday_card_st','$not_cn_st','$paid','$pay_cash','$pay_credit','$pay_cash_coupon','$credit_no','$credit_tp','$bank_tp','$change','$pos_id','$computer_no','$user_id','$cashier_id','$saleman_id','$cn_status','$refer_cn_net','$name','$address1','$address2','$address3','$doc_remark','$create_date','$create_time','$cancel_id','$cancel_date','$cancel_time','$cancel_tp','$cancel_remark','$cancel_auth','$keyin_st','$keyin_tp','$keyin_remark','$post_id','$post_date','$post_time','$transfer_ts','$transfer_date','$transfer_time','$order_id','$order_no','$order_date','$order_time','$acc_name','$bank_acc','$bank_name','$tel1','$tel2','$dn_name','$dn_address1','$dn_address2','$dn_address3','$remark1','$remark2','$deleted','$print_no','$reg_date','$reg_time','$reg_user','$upd_date','$upd_time','$upd_user',now())
		  	";	

		    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
		    mysql_select_db($this->db_online);
		    $result=mysql_query($up,$conn_online);
		    
		    if($status_no=="05" && $member_id!="" && $refer_member_id!=""){
					$this->re_card_missing('Missing',$doc_no,$doc_date,$application_id,$member_id,$refer_member_id);
		    }
			if($status_no=="01" && $member_id!="" && $refer_member_id!="" && $application_id!="" && $application_id!="OPPN300" && $application_id!="OPPS300"){
					$this->re_card('Renew',$doc_no,$doc_date,$expire_date,$application_id,$member_id,$refer_member_id);
		    }
		    
			/*$conn_new=mysql_connect("crmop.ssup.co.th","crm-op","CRM@1701");
			 mysql_select_db("service_pos_op");
			mysql_query($up,$conn_new);*/
						
			if($result){
				$keeplog="insert into service_log(doc_no,doc_date,type_case,time_up,rechk_up) values('$doc_no','$doc_date','HEAD',now(),'$bill_time')";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		 		$databill=$this->db->query($keeplog, 2); 
	 			return "Y";
	 		}else{
	 			return "N";
	 		}
	 		
			

		}//end function
		
		function max_seq_customer($customer_id){
		    	$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			    mysql_select_db($this->db_online);
				$max_seq="select max(seq)+1 as seq from member_history where customer_id='$customer_id' ";
				$result=mysql_query($max_seq,$conn_online);
				$data=mysql_fetch_array($result);
				$seq=$data['seq'];
				return $seq;
		}
		function ops_type($member_id){
		    	$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			    mysql_select_db($this->db_online);
				$sql="select * from member_card_type where '$member_id' between id_start and id_end ";
				$result=mysql_query($sql,$conn_online);
				$data=mysql_fetch_array($result);
				$ops=$data['ops'];
				return $ops;
		}
		function age_card($member_id){
			if($member_id=="1120103200592"){
			return 4;
			}
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			    mysql_select_db($this->db_online);
			    
			    $find="select * from member_his_seq where member_no='$member_id' and member_refer<>''";
				$run_find=mysql_query($find,$conn_online);
				$rows=mysql_num_rows($run_find);

				if($rows==0){
					return 2;
				}else{
					$data=mysql_fetch_array($run_find);
					$set_id=$data['set_id'];
					$member_no=$data['member_no'];
					$member_refer=$data['member_refer'];
					
					$find2="select * from member_his_seq where set_id='$set_id' and member_no='$member_no' and member_refer<>'' and remark='Renew' and
doc_date between 
				 concat(if(month(expire_date_refer)=1,year(expire_date_refer)-1,year(expire_date_refer)),'-',if(month(expire_date_refer)=1,'13',month(expire_date_refer))-1,'-01') and 
				 last_day(concat(if(month(expire_date_refer)=12,year(expire_date_refer)+1,year(expire_date_refer)),'-',if(month(expire_date_refer)=12,'0',month(expire_date_refer))+1,'-01'))";

					$run_find2=mysql_query($find2,$conn_online);
					$rows2=mysql_num_rows($run_find2);
					if($rows2>0){
						return 4;
					}else if($rows2==0){
						$find2="select * from member_his_seq where  member_no='$member_refer' and member_refer<>'' ";
						$run_find2=mysql_query($find2,$conn_online);
						$rows2=mysql_num_rows($run_find2);
						if($rows2==0){
							return 2;
						}else{
							$data2=mysql_fetch_array($run_find2);
							$set_id=$data2['set_id'];
							$member_no=$data2['member_no'];
							$member_refer=$data2['member_refer'];
							
							$find3="select * from member_his_seq where  member_no='$member_refer' and member_refer<>'' and remark='Renew' and
		doc_date between 
						 concat(if(month(expire_date_refer)=1,year(expire_date_refer)-1,year(expire_date_refer)),'-',if(month(expire_date_refer)=1,'13',month(expire_date_refer))-1,'-01') and 
						 last_day(concat(if(month(expire_date_refer)=12,year(expire_date_refer)+1,year(expire_date_refer)),'-',if(month(expire_date_refer)=12,'0',month(expire_date_refer))+1,'-01'))";
		
							$run_find3=mysql_query($find3,$conn_online);
							$rows3=mysql_num_rows($run_find3);
							if($rows3>0){
								return 4;
							}else{
								return 2;
							}
							
						}
					}
					
					

				
					
				}
				
		}
		
		function re_card($bec,$doc_no,$doc_date,$expire_date_new,$application_id,$member_id,$refer_member_id){
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			    mysql_select_db($this->db_online);
				
			    $findbill="SELECT * FROM trn_diary1 WHERE doc_no='$doc_no'";
			    $result_findbill=mysql_query($findbill,$conn_online);
			    $data_findbill=mysql_fetch_array($result_findbill);
			    $reg_date=$data_findbill['reg_date'];	
				$reg_time=$data_findbill['reg_time'];	
				$reg_user=$data_findbill['reg_user'];
						
			    $find="SELECT * FROM `member_history` WHERE member_no='$refer_member_id'";
			    $result_find=mysql_query($find,$conn_online);
			    $rows_find=mysql_num_rows($result_find);
			    if($rows_find>0){
			   		 $data_find=mysql_fetch_array($result_find);
			   		 $customer_id=$data_find['customer_id'];
			   		 $ops=$this->ops_type($member_id);
			   		 $max_seq=$this->max_seq_customer($customer_id);
			   		 
			   		 $add_card="insert into member_history(`customer_id`, `seq`, `member_no`, `apply_date`, `expire_date`,  `bec`, `doc_no`, `doc_date`, `application_id`, `status_active`, `status`, `ops`, `time_up`,`reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`) values('$customer_id','$max_seq','$member_id','$doc_date','$expire_date_new','Renew','$doc_no','$doc_date','$application_id','Y','0','$ops',now(),'$reg_user','$reg_date','$reg_time','$reg_user','$reg_date','$reg_time') ";
			   		 $result_add_card=mysql_query($add_card,$conn_online);
			   		 $stop_active="update member_history set status_active='',status='1',upd_date='$doc_date',upd_time='$reg_time',upd_user='$reg_user' where member_no='$refer_member_id' ";
			   		 $result_stop_active=mysql_query($stop_active,$conn_online);		    		
			    }

		}
		
		function re_card_missing($bec,$doc_no,$doc_date,$application_id,$member_id,$refer_member_id){
		    	$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			    mysql_select_db($this->db_online);
			    
			    $findbill="SELECT * FROM trn_diary1 WHERE doc_no='$doc_no'";
			    $result_findbill=mysql_query($findbill,$conn_online);
			    $data_findbill=mysql_fetch_array($result_findbill);
			    $reg_date=$data_findbill['reg_date'];	
				$reg_time=$data_findbill['reg_time'];	
				$reg_user=$data_findbill['reg_user'];

				
			    $find="SELECT * FROM `member_history` WHERE member_no='$refer_member_id'";
			    $result_find=mysql_query($find,$conn_online);
			    $rows_find=mysql_num_rows($result_find);
			    if($rows_find>0){
			   		 $data_find=mysql_fetch_array($result_find);
			   		 $customer_id=$data_find['customer_id'];
			   		 $apply_date=$data_find['apply_date'];
			   		 $expire_date=$data_find['expire_date'];
			   		 $ops=$data_find['ops'];
			   		 $age_card_old=$data_find['age_card'];
			   		 $max_seq=$this->max_seq_customer($customer_id);

			    	if($application_id=="OPT"){

						$find_ops="select * from member_card_type where '$member_id' between id_start and id_end ";
						$result_ops=mysql_query($find_ops,$conn_online);
						$data_ops=mysql_fetch_array($result_ops);
						$ops=$data_ops['ops'];
					}
					
				
			   		 $add_card="
					  	insert into member_history(`customer_id`, `seq`, `member_no`, `apply_date`, `expire_date`,  `bec`, `doc_no`, `doc_date`, `application_id`, `status_active`, `status`, `ops`, `time_up`,age_card,`reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`)	values('$customer_id','$max_seq','$member_id','$apply_date','$expire_date','Missing','$doc_no','$doc_date','$application_id','Y','0','$ops',now(),'$age_card_old','$reg_user','$reg_date','$reg_time','$reg_user','$reg_date','$reg_time')
					  	";
			   		 $result_add_card=mysql_query($add_card,$conn_online);
			   		 
			   		 $stop_active="update member_history set status_active='',status='1',upd_date='$doc_date',upd_time='$reg_time',upd_user='$reg_user' where member_no='$refer_member_id' ";
			   		 $result_stop_active=mysql_query($stop_active,$conn_online);	
			   		 
			   		 /*$find="select max(set_id)+1 as set_id from member_his_seq  ";
			   		 $result_find=mysql_query($find,$conn_online);
			   		 $data_find=mysql_fetch_array($result_find);
			   		 $set_id=$data_find['set_id'];*/
		
			   		 
		    		
			    }
		    
				
		
		    	
			}
		
		
			function send_point($doc_no){
			
			$bill="select * from trn_diary1 where doc_no='$doc_no'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 		$databill=$this->db->fetchAll($bill, 2);
			$corporation_id=$databill[0]['corporation_id'];
			$company_id=$databill[0]['company_id'];
			$branch_id=$databill[0]['branch_id'];
			$doc_date=$databill[0]['doc_date'];
			$doc_time=$databill[0]['doc_time'];
			$doc_no=$databill[0]['doc_no'];
			$doc_tp=$databill[0]['doc_tp'];
			$status_no=$databill[0]['status_no'];
			$flag=$databill[0]['flag'];
			$member_id=$databill[0]['member_id']; 
			$quantity=$databill[0]['quantity'];
			$amount=$databill[0]['amount'];
			$net_amt=$databill[0]['net_amt'];
			$point1=$databill[0]['point1'];
			$point2=$databill[0]['point2'];
			$redeem_point=$databill[0]['redeem_point'];
			$total_point=$databill[0]['total_point'];
			$application_id=$databill[0]['application_id'];

			if($status_no=="01" && $application_id=="OPPN300" && $member_id!=""){//New member
					$chk_have="select * from point_summary where member_no='$member_id' ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_chk_have=$this->db->fetchAll($chk_have, 2);
					if($result_chk_have){
						return "Y";
					}
				  $add_new_member="
				  	insert into point_summary(`member_no`,new_member, `point`,point_start_date, `point_start`, `point_today`, `date_up`, `time_up`, `time_add`) values('$member_id','Y','$total_point',now(),'$total_point','$total_point',date(now()),time(now()),now())
				  ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_new_member=$this->db->query($add_new_member, 2);
					if($result_new_member){
						$keeplog="insert into service_log(doc_no,doc_date,type_case,time_up) values('$doc_no','$doc_date','NEWMEMBER',now())";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				 		$this->db->query($keeplog, 2); 
				 		return "N";
					}else{
						return "Y";
					}

		 		
			}else if($member_id=="" && $total_point!=0){//Old member
				  $uppoint="
				  	update point_summary
				  	set point=point+'$total_point',
				  		point_today=point_today+'$total_point',
				  		date_up=date(now()),
				  		time_up=time(now())
				  	where
				  	member_no='$member_id'
				  ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_uppoint=$this->db->query($uppoint, 2);
					if($result_uppoint){
						$keeplog="insert into service_log(doc_no,doc_date,type_case,time_up) values('$doc_no','$doc_date','UPPOINT',now())";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
				 		$this->db->query($keeplog, 2); 
				 		return "N";
					}else{
						return "Y";
					}
			}


			
		}//end function
		
		
		
		
		
		function up_point($doc_no){
			$this->re_chk_bill($doc_no); //send bill
			//$this->send_new_member($doc_no); //add new member

			$this->add_diary2($doc_no); //add trn_diary

			$this->keep_promotion_input_history($doc_no);
			$this->send_promotion_input_history();
			
			$this->keep_promotion_return_history($doc_no);
			$this->send_promotion_return_history();	
			
            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_new2.php", "r");
            $text=@fgetss($fp, 4096);
            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_renew_seq.php", "r");
            $text=@fgetss($fp, 4096);
            
            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_new3.php", "r");
            $text=@fgetss($fp, 4096);
            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_his_seq.php", "r");
            $text=@fgetss($fp, 4096);
              

						    
			/*$bill="select * from trn_diary1 where doc_no='$doc_no'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 		$databill=$this->db->fetchAll($bill, 2);
	 		$doc_date=$databill[0]['doc_date'];
			$member_id=$databill[0]['member_id'];
			$refer_member_id=$databill[0]['refer_member_id'];
			$total_point=$databill[0]['total_point'];
			$application_id=$databill[0]['application_id'];

			if($application_id=="OPPN300" || $application_id=="OPPS300"){
				$this->copy_photo($doc_no);
			}*/
			
			//$this->transfer_point($doc_no); //ต่ออายุบัตร

		}//end function
		
		
		function up_cancle_bill($doc_no){
				$bill="select * from trn_diary1 where doc_no='$doc_no'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		 		$databill=$this->db->fetchAll($bill, 2);
		 		$doc_date=$databill[0]['doc_date'];
				$member_id=$databill[0]['member_id'];
				$refer_member_id=$databill[0]['refer_member_id'];
				$total_point=$databill[0]['total_point'];
				$application_id=$databill[0]['application_id'];
				
				  $up_cancle_bill="
				  	update trn_diary1
				  	set flag='C'
				  	where
				  	doc_no='$doc_no'
				  ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
			    $result_cancle_bill=$this->db->query($up_cancle_bill, 2);
			   
			    //cancle promo play
				  $up_cancle_promoplay="
				  	delete from  promo_play_history
				  	
				  	where
				  	doc_no='$doc_no'
				  ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
			    $result_up_cancle_promoplay=$this->db->query($up_cancle_promoplay, 2);
				
		 		if($refer_member_id!=""){
				    //del member
				    $del_member_no="delete from member_history where member_no='$member_id' ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_del_member=$this->db->query($del_member_no, 2);
				    
				    //return active
				    $re_active="update member_history set status_active='Y' , status='0',upd_date='$reg_date',upd_time='$reg_time',upd_user='$reg_user' where member_no='$refer_member_id'  ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_re_active=$this->db->query($re_active, 2);
				    
				    //cancle promo play
					$up_cancle_seq="delete from member_his_seq 	where 	doc_no='$doc_no' ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_up_cancle_seq=$this->db->query($up_cancle_seq, 2);
		 		}
			    
				 if($application_id=="OPPN300" || $application_id=="OPPS300"){
				    //del member
				    $del_member_no="delete from member_history where member_no='$member_id' ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
				    $result_del_member=$this->db->query($del_member_no, 2);
				    
				     //cancle ที่ เครื่อง
					  $del_profile="
					  	delete member_register from member_register inner join member_history
					  	on member_register.customer_id=member_history.customer_id
					  	where
					  	member_history.member_no='$member_id'
					  ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 		$this->db->query($del_profile, 2);
	
				     //cancle ที่ เครื่อง
					  $del_card="
					  	delete from member_history where member_no='$member_id'
					  ";
					  //echo "$del_card";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 		$this->db->query($del_card, 2);	
					
		 		}
			    
		 		
			    //cancle promo play mobile
			    //cancle ที่ server
				  $up_cancle_pro_mobile="
				  	update promo_mobile_play
				  	set flag='C'
				  	where
				  	doc_no='$doc_no'
				  ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("view","point_summary");
			    $this->db->query($up_cancle_pro_mobile, 2);

			     //cancle ที่ เครื่อง
				  $up_cancle_pro_mobile="
				  	update promo_mobile_play
				  	set flag='C'
				  	where
				  	doc_no='$doc_no'
				  ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		 		$this->db->query($up_cancle_pro_mobile, 2);
			    
	 		$this->promotion_input_cancle($doc_no);
	 		
	 		$this->promotion_return_cancle($doc_no);				

		}

		function keep_play_promotino($doc_no){
			$insert="
				insert into promo_play_history(`date_add`, `time_add`,  `shop`, `member_id`, `doc_no`, `doc_date`, `promo_code`)
				select
				date(now()) as date_add,
				time(now()) as time_add,
				a.branch_id,
				b.member_id,
				a.doc_no,
				a.doc_date,
				a.promo_code
				from
				trn_diary2 as a inner join trn_diary1 as b
				on a.doc_no=b.doc_no
				where
				a.doc_no='$doc_no' and a.promo_code<>'' and b.member_id<>''
				group by 
				a.promo_code
			";
			
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 		$databill=$this->db->query($insert, 2); 
		}
		
		
		function member_his_point($ip,$user,$pass,$db,$member_no){
				$connect=mysql_connect($ip,$user,$pass);
				mysql_select_db($db);
				$find_set="
					SELECT * 
					FROM member_his_seq
					WHERE
					 member_no = '$member_no' and 
					 member_refer<>'' and
					((if( doc_date
					BETWEEN '2012-01-01'
					AND date( now( ) )
					AND remark = 'Renew', 'Y', 'N' )='Y') or (remark='Missing')) 
					AND doc_date>='2013-06-21'
			 	";
				//echo $find_set;
				$result_find_set=mysql_query($find_set,$connect);
				$rows_find_set=mysql_num_rows($result_find_set);
				if($rows_find_set==0){
					$cr="'$member_no'";
				}else{
					$data_set=mysql_fetch_array($result_find_set);
					$set_id=$data_set['set_id'];
					$doc_date=$data_set['doc_date'];
					$member_no=$data_set['member_no'];
					$member_refer=$data_set['member_refer'];
					
					$txt="'$member_no','$member_refer'";
					//echo "txt=$txt<br>";
					
					
					$find_step2="
					SELECT * 
					FROM member_his_seq
					WHERE 
					member_no = '$member_refer' and
					member_refer<>'' and 
					((if( doc_date
					BETWEEN '2012-01-01'
					AND date( now( ) )
					AND remark = 'Renew', 'Y', 'N' )='Y') or (remark='Missing'))
					AND doc_date>='2013-06-21'
					";
					$result_find_step2=mysql_query($find_step2,$connect);
					$rows_find_step2=mysql_num_rows($result_find_step2);
					if($rows_find_step2>0){
						$data2=mysql_fetch_array($result_find_step2);
						$set_id=$data2['set_id'];
						$doc_date=$data2['doc_date'];
						$member_no=$data2['member_no'];
						$member_refer=$data2['member_refer'];
						$txt2=",'$member_no','$member_refer'";
					}else{
						$txt2="";
					}
							
						$find_step3="
						SELECT * 
						FROM member_his_seq
						WHERE 
						member_no = '$member_refer' and
						member_refer<>'' and 
						((if( doc_date
						BETWEEN '2012-01-01'
						AND date( now( ) )
						AND remark = 'Renew', 'Y', 'N' )='Y') or (remark='Missing')) 
						AND doc_date>='2013-06-21'
						";
						$result_find_step3=mysql_query($find_step3,$connect);
						$rows_find_step3=mysql_num_rows($result_find_step3);
						if($rows_find_step3>0){
							$data3=mysql_fetch_array($result_find_step3);
							$set_id=$data3['set_id'];
							$doc_date=$data3['doc_date'];
							$member_no=$data3['member_no'];
							$member_refer=$data3['member_refer'];
							$txt3=",'$member_no','$member_refer'";
						}else{
							$txt3="";
						}
					
					
						$find_step4="
						SELECT * 
						FROM member_his_seq
						WHERE 
						member_no = '$member_refer' and
						member_refer<>'' and 
						((if( doc_date
						BETWEEN '2012-01-01'
						AND date( now( ) )
						AND remark = 'Renew', 'Y', 'N' )='Y') or (remark='Missing')) 
						AND doc_date>='2013-06-21'
						";
						$result_find_step4=mysql_query($find_step4,$connect);
						$rows_find_step4=mysql_num_rows($result_find_step4);
						if($rows_find_step4>0){
							$data4=mysql_fetch_array($result_find_step4);
							$set_id=$data4['set_id'];
							$doc_date=$data4['doc_date'];
							$member_no=$data4['member_no'];
							$member_refer=$data4['member_refer'];
							$txt4=",'$member_no','$member_refer'";
						}else{
							$txt4="";
						}
						
						$cr="$txt$txt2$txt3$txt4";
				}
				
				
				return $cr;
		}

		function member_his_pointxx($ip,$user,$pass,$db,$member_no){
			$connect=mysql_connect($ip,$user,$pass);
			mysql_select_db($db);
			$find_set="select * from member_his_seq where member_no='$member_no' ";
			$result_find_set=mysql_query($find_set,$connect);
			$rows_find_set=mysql_num_rows($result_find_set);
			if($rows_find_set==0){
				$cr="'$member_no'";
			}else{
				$data_set=mysql_fetch_array($result_find_set);
				$set_id=$data_set['set_id'];
				$doc_date=$data_set['doc_date'];
				$date_his=$data_set['date_his'];
				
				$set="select distinct member_no from member_his_seq where set_id='$set_id' and  date_his<='$date_his'  order by date_his";
				$result_set=mysql_query($set,$connect);
				$rows_set=mysql_num_rows($result_set);
				$map_id="";
				for($g=1; $g<=$rows_set; $g++){
					$data_g=mysql_fetch_array($result_set);
					$member_no_find=$data_g['member_no'];
					
					$chk_renew="
					SELECT member_refer,expire_date_refer,
						if(doc_date between 
						 concat(if(month(expire_date_refer)=1,year(expire_date_refer)-1,year(expire_date_refer)),'-',if(month(expire_date_refer)=1,'13',month(expire_date_refer))-1,'-01') and 
						 last_day(concat(if(month(expire_date_refer)=12,year(expire_date_refer)+1,year(expire_date_refer)),'-',if(month(expire_date_refer)=12,'0',month(expire_date_refer))+1,'-01')),'Y','N') as chk_renew
						 FROM `member_his_seq` WHERE set_id='$set_id' and remark='renew'
					";
					$result_chk_renew=mysql_query($chk_renew,$connect);
					$data_chk_renew=mysql_fetch_array($result_chk_renew);
					$chk_renew=$data_chk_renew['chk_renew'];
					if($chk_renew!="N"){
						if($g==1){
							$map_id="'$member_no_find'";
						}else{
							$map_id=$map_id . "," . "'$member_no_find'";
						}
					}
					 
					
				}
				
				$cr=$map_id;
			}
			
			return $cr;
		}
		
		/*function read_point($member_no){


		    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
		    mysql_select_db($this->db_online);
			$map_id=$this->member_his_point($this->sv_online,$this->user_online,$this->pass_online,$this->db_online,$member_no);
			$arr_map_id=explode(",",$map_id);
			$count_map_id=count($arr_map_id);
			if($count_map_id==1){
				$cr="member_id=$map_id";
			}else{
				$cr="member_id in($map_id)";
			}
			
			
			$show="
			select sum(sub.total_point) as point from (
			SELECT total_point FROM trn_diary1 where $cr and flag<>'C'
			Union all
			SELECT total_point FROM trn_diary1_all where $cr and flag<>'C'
			Union all
			SELECT total_point FROM member_point_sp where $cr and flag<>'C'
			) as sub
			
			";
			//echo $show;
			$result_show=mysql_query($show,$conn_online);
			$rows_show=mysql_num_rows($result_show);
			if($rows_show==0){
				$point=0;
			}else{
				$data=mysql_fetch_array($result_show);
				$point=$data['point'];
			}
			return $point;
		}*/
		
		
		function read_point($member_no){


		    /*$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
		    mysql_select_db($this->db_online);
			$map_id=$this->member_his_point($this->sv_online,$this->user_online,$this->pass_online,$this->db_online,$member_no);
			$arr_map_id=explode(",",$map_id);
			$count_map_id=count($arr_map_id);
			if($count_map_id==1){
				$cr="member_id=$map_id";
				$cr2="mp_new=$map_id";
			}else{
				$cr="member_id in($map_id)";
				$cr2="mp_new = $arr_map_id[1]";
			}
			
			
			$show="
			select sum(sub.total_point) as point from (
			SELECT total_point FROM trn_diary1 where doc_date>='2013-06-21' and $cr and flag<>'C'
			Union all
			SELECT total_point FROM member_point_sp where doc_date>='2013-06-21' and $cr and flag<>'C'
			Union all
			SELECT mp_point_sum as total_point FROM mem_point where $cr2 

			) as sub
			
			";
			//echo $show;
			$result_show=mysql_query($show,$conn_online);
			$rows_show=mysql_num_rows($result_show);
			if($rows_show==0){
				$point=0;
			}else{
				$data=mysql_fetch_array($result_show);
				$point=$data['point'];
			}
			return $point;*/
			
			
			$fp =fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_point_all.php?member_no=$member_no", "r");
            $text=fgetss($fp, 4096);
            return $text;
		}
		
		function chk_online(){
			return mysql_connect($sv_online,$user_online,$pass_online) or die("cannot_connect");
		}
		
		function read_ecoupon($member_id){
				$find="
					SELECT 
					a . * , 
					if( b.amount IS NULL , 0, b.amount ) AS amount,
					 a.amount_op - if( b.amount IS NULL , 0, b.amount ) AS credit
					FROM com_ecoupon AS a
					LEFT JOIN (
					
					SELECT member_id, sum( coupon_discount*2 ) AS amount, sum( net_amt ) AS net
					FROM trn_diary1
					WHERE member_id = '$member_id'
					AND status_no = '03'
					AND flag <> 'C'
					AND doc_date
					BETWEEN concat( year( '$this->doc_date' ) , '-', month( '$this->doc_date' ) , '-10' )
					AND concat( year( '$this->doc_date' ) , '-', month( '$this->doc_date' ) , '-25' )
					GROUP BY member_id
					) AS b ON a.employee_id = b.member_id
					WHERE 
					a.employee_id = '$member_id' 
					and  '$this->doc_date' between a.start_date and a.end_date
					and month('$this->doc_date') in('02','04','06','08','10','12')
				";
			    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			    mysql_query("SET NAMES utf8"); 
			     mysql_select_db($this->db_online);
			    $result=mysql_query($find,$conn_online);
			    $data=mysql_fetch_assoc($result);
			    return $data;
		}
		
		function read_profile($member_id){
			
				
				$start_date=substr($this->doc_date,0,7) . "-01";
				//echo "start_date" . $start_date . "<br>";
				$sql_vip="select * from member_register_vip where member_no='$member_id' ";
			    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				mysql_query("SET NAMES utf8");
				mysql_query("SET collection_connection='utf8_general_ci'");
			    mysql_select_db($this->db_online);
			    $run_vip=mysql_query($sql_vip,$conn_online);
			    $rows_vip=mysql_num_rows($run_vip);
			    if($rows_vip>0){
			    	$data_vip=mysql_fetch_array($run_vip);
			    	$limited=$data_vip['limited'];
			    	$limited_type=$data_vip['limited_type'];
			    	
			    	if($limited_type=="N"){
			    		$chk_sum="if(sum(net_amt) is null,0,sum(net_amt)) as amount";
			    	}else{
			    		$chk_sum="if(sum(amount) is null,0,sum(amount)) as amount";
			    	}
			    	$sumamount="
			    	select $chk_sum from trn_diary1 
			    	where 
			    		member_id='$member_id' and
			    		doc_date between '$start_date' and last_day('$this->doc_date') and
			    		 flag<>'C' and 
			    		 doc_tp in('SL','VT') ";
			    	//echo $sumamount;
			    	$run_sumamount=mysql_query($sumamount,$conn_online);
			    	$data_sumamount=mysql_fetch_array($run_sumamount);
			    	$sumamountall=$data_sumamount['amount'];
			    	$diff=$limited-$sumamountall;
			    	$sql_vip="select 
						*,'$diff' as sum_amt,
						percent1 as member_percent,
						percent1 as special_percent,
						'1' as vip,
						apply_date as valid_dt,
						expire_date as expire_dt,
						address as add1,
						address2 as add2,
						address3 as add3,
						phone_home as tel,
						birthday as birth,
						apply_date as first_app
			    	from member_register_vip where member_no='$member_id' ";
			    	$run_vip=mysql_query($sql_vip,$conn_online);
			    	$data_vip=mysql_fetch_assoc($run_vip);
			    	return json_encode($data_vip);
			    }
			    


			    //$age_member=$this->age_card($member_id);

			    //ขอรับบัตร
			    $forgot="select * from a_member_forgot_card where member_no='$member_id' and user_id_out='' ";
			    mysql_select_db($this->db_online_old);
			    $result_forgot=mysql_query($forgot,$conn_online);
			    $rows_forgot=mysql_num_rows($result_forgot);
			    if($rows_forgot>0){
			    	$forgot_card="Y";
			    }else{
					$forgot_card="";
			    }
			    
/*
			    $chk_re="select * from member_his_seq where member_refer='$member_id' ";
				$run_chk_re=mysql_query($chk_re,$conn_online);
				$rows_chk_re=mysql_num_rows($run_chk_re);
				if($rows_chk_re>0){
					$ans_chk_re='Y';
				}else{
					$ans_chk_re='N';
				}
				*/
				


				$find="
					SELECT 
					a.member_no,
					 a.sumquantity AS qty, a.sumamount AS amt, a.sumnet AS net,
					  a.apply_date, a.expire_date,if(a.age_card=4,4,2) as age_card,
					  if(a.status_active='Y','0','1') as status ,
					  if(a.bec='card_missing','T','N') as mem_status, 
					  '$forgot_card' as forgot_card,
					  '0' as vip,
					
					  a.ops as cust_day,
					  a.brand_id,a.cardtype_id,
					  a.application_id,
					  b.customer_id, b.mobile_no, b.id_card, b.prefix, b.name, b.surname, b.sex, b.send_address as address, b.send_road as road, b.send_province_name as province_name, b.send_amphur_name as district, b.send_tambon_name as sub_district, b.send_postcode as zip, b.birthday, b.shop, a.doc_no, a.doc_date AS doc_dt,b.mobile_no as mobile_no
					FROM member_history AS a
					INNER JOIN member_register AS b ON a.customer_id = b.customer_id
					WHERE a.member_no = '$member_id'
				";
				//echo $find;
				mysql_select_db($this->db_online);
			    $result=mysql_query($find,$conn_online);
			    $data=mysql_fetch_assoc($result);
				
				/*$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);
				mysql_select_db($this->db_my) or die(mysql_error());
				$clear_tmp="delete from member_val_tmp where ip='$_SERVER[REMOTE_ADDR]' ";
				$run_clear_tmp=mysql_query($clear_tmp,$conn_local);
				if($run_clear_tmp){
					$keep_tmp="insert into member_val_tmp
					set
					ip='$_SERVER[REMOTE_ADDR]',
					member_no='$data[member_no]',
					customer_id='$data[customer_id]',
					id_card='$data[id_card]',
					ops='$data[cust_day]',
					age_card='$data[age_card]',
					mobile_no='$data[mobile_no]',
					birthday='$data[birthday]'
					";
					mysql_query($keep_tmp,$conn_local);
				}*/
			    //print_r($data);
			    
			    
			    return json_encode($data);
			    //return $age_member;
		}
		
		
		function jsonType($obj) {
				header("content-type: Access-Control-Allow-Origin: *");
				header("content-type: Access-Control-Allow-Methods: GET");
				header('Content-type: application/json');
				echo $_GET['callback']."(".json_encode($obj).")";
		}
		
		function send_new_member($doc_no){
			
			$bill="select * from trn_diary1 where doc_no='$doc_no'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 		$databill=$this->db->fetchAll($bill, 2);
			$corporation_id=$databill[0]['corporation_id'];
			$company_id=$databill[0]['company_id'];
			$branch_id=$databill[0]['branch_id'];
			$doc_date=$databill[0]['doc_date'];
			$doc_time=$databill[0]['doc_time'];
			$doc_no=$databill[0]['doc_no'];
			$doc_tp=$databill[0]['doc_tp'];
			$status_no=$databill[0]['status_no'];
			$flag=$databill[0]['flag'];
			$member_id=$databill[0]['member_id']; 
			$quantity=$databill[0]['quantity'];
			$amount=$databill[0]['amount'];
			$net_amt=$databill[0]['net_amt'];
			$point1=$databill[0]['point1'];
			$point2=$databill[0]['point2'];
			$redeem_point=$databill[0]['redeem_point'];
			$total_point=$databill[0]['total_point'];
			$application_id=$databill[0]['application_id'];

			if(( ($status_no=="01") || ($status_no=="05")) &&  $member_id!=""){//New member
					
				  $add_new_member="
				  	insert into point_summary(`member_no`,new_member, `point`,point_start_date, `point_start`, `point_today`, `date_up`, `time_up`, `time_add`) values('$member_id','Y','$total_point',now(),'$total_point','$total_point',date(now()),time(now()),now())
				  ";
				    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				    mysql_select_db($this->db_online);
				    $result_new_member=mysql_query($add_new_member,$conn_online);
				    mysql_close($conn_online);
					if($result_new_member){
				 		return "Y";
					}else{
						return "N";
					}
			}
			
	}

	
		function transfer_point($doc_no){
			$bill="select * from trn_diary1 where doc_no='$doc_no'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 		$databill=$this->db->fetchAll($bill, 2);
			$corporation_id=$databill[0]['corporation_id'];
			$company_id=$databill[0]['company_id'];
			$branch_id=$databill[0]['branch_id'];
			$doc_date=$databill[0]['doc_date'];
			$doc_time=$databill[0]['doc_time'];
			$doc_no=$databill[0]['doc_no'];
			$doc_tp=$databill[0]['doc_tp'];
			$status_no=$databill[0]['status_no'];
			$flag=$databill[0]['flag'];
			$member_id=$databill[0]['member_id']; 
			$quantity=$databill[0]['quantity'];
			$amount=$databill[0]['amount'];
			$net_amt=$databill[0]['net_amt'];
			$point1=$databill[0]['point1'];
			$point2=$databill[0]['point2'];
			$redeem_point=$databill[0]['redeem_point'];
			$total_point=$databill[0]['total_point'];
			$application_id=$databill[0]['application_id'];
			$refer_member_id=$databill[0]['refer_member_id'];
			$reg_date=$databill[0]['reg_date'];
			$reg_time=$databill[0]['reg_time'];
			$reg_user=$databill[0]['reg_user'];
			
			$find="SELECT 
				member_no,
				expire_date
				FROM member_history
				WHERE 
				member_no = '$refer_member_id' and
				'$doc_date' between concat( left(date_sub(expire_date,interval 40 day),7) ,'-01') and last_day(date_add(expire_date,interval 5 day))

			";
		    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
		    mysql_select_db($this->db_online);
			$result_find=mysql_query($find,$conn_online);	
			$rows_find=mysql_num_rows($result_find);
	    
			if($rows_find>0){
					if( ($status_no=="01") && ($application_id!="OPPN300") && ($application_id!="") &&  ($member_id!="") ){//ต่ออายุ  member
					
							$data_point=$this->read_point($refer_member_id);
							$count_chk=count($data_point);
							if($count_chk>0){
								$point=$data_point['total_point'];
								
								$add="
									insert into member_transfer_point(`member_old`, `point_old`, `doc_no`, `doc_date`,  `member_no`, `point`) values('$refer_member_id','$point','$doc_no','$doc_date','$member_id','$point')
								";
								
							    $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
							    mysql_select_db($this->db_online);
							    $result=mysql_query($add,$conn_online);
							    if($result){
							    	$up_status="update member_history  set  status_active='',status='1',upd_user='$reg_user',upd_date='$reg_date',upd_time='$reg_time' where member_no='$member_old' ";
							    	$result=mysql_query($up_status,$conn_online);
							    	
							    	$up_status="update member_history  set  status_active='Y',upd_user='$reg_user',upd_date='$reg_date',upd_time='$reg_time' where member_no='$member_new' ";
							    	$result=mysql_query($up_status,$conn_online);
							    }
							}
					}
			}
		
		}
		
		
		
		function promo_chk_play($member_no){
			$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			mysql_query("SET NAMES utf8");
			mysql_query("SET collection_connection='utf8_general_ci'");

			$sql="
				SELECT *
				FROM `member_his_seq`
				WHERE `member_no` = '$member_no'
				";
				//echo "$sql<br>";		
				mysql_select_db($this->db_online);
				$run_sql=mysql_query($sql,$conn_online);
				$rows_sql=mysql_num_rows($run_sql);
				$data=mysql_fetch_array($run_sql);
				$remark=$data['remark'];
			    $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_status_renew.php?member_no=$member_no", "r");
	            $chk_status_renew=@fgetss($fp, 4096);
	            
				if($chk_status_renew=="R"){
					$type_member="Re";
				}else{
					$type_member="New";
				}
				
				
				$profile="
					SELECT 
						a.customer_id,a.apply_date,a.expire_date,
						year(a.apply_date) as year_app,
						month('$this->doc_date') as month_now,
						year('$this->doc_date') as year_now  
					FROM `member_history`  as a 
					where
						a.member_no='$member_no'
				";
				$run_profile=mysql_query($profile,$conn_online);
				$rows_profile=mysql_num_rows($run_profile);
				$data_profile=mysql_fetch_array($run_profile);
				$apply_date=$data_profile['apply_date'];
				$expire_date=$data_profile['expire_date'];
				$month_hbd=$data_profile['month_hbd'];
				$month_now=$data_profile['month_now'];
				$year_now=$data_profile['year_now'];
				$year_app=$data_profile['year_app'];
				$year_next=$year_app+1;


				//seq missing
			    $ftp_missing = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_missing.php?member_no=$member_no", "r");
	            $missing_seq=@fgetss($ftp_missing, 4096);
	            
				//point bill 02 First phurchase
				$find_point="select * from trn_diary1 where member_id in($missing_seq) and status_no='02' and flag<>'C' ";
				$run_find_point=mysql_query($find_point,$conn_online);
				$rows_find_point=mysql_num_rows($run_find_point);
				if($rows_find_point>0){
					$data_find_point=mysql_fetch_array($run_find_point);
					$point_first=$data_find_point['total_point'];
				}else{
					$point_first=0;
				}
				
				if($year_app<2015){//round
				$find="
	
					SELECT 
						a.*,
						if(b.promo_code is null,'N','Y')as chk_play,

						month('$apply_date'+interval a.next_month month) as time_play,
						year('$apply_date'+interval a.next_month month) as year_play,
						year(a.end_date) as promo_year,
						if(c.promo_code is null,'N',concat('ใช้แล้วในบิล ',c.doc_no,' เมื่อ',c.doc_date)) as chk_play_last,
						c.doc_no as doc_no_play
					from
						promotion_cr as a left join (
							 SELECT 
							 	*,month('$apply_date'+interval next_month month) as time_play
							 FROM
							  `promotion_cr` 
							 where 
							 date(now()) between start_date and end_date and
							 type_member='$type_member' and
							 month('$apply_date'+interval next_month month)=month('$this->doc_date') and
							  year( '$apply_date' ) = year_apply
						) as b
						on a.promo_code=b.promo_code and
						month('$apply_date'+interval a.next_month month)=b.time_play 
						
						left join (SELECT promo_code,doc_no,doc_date FROM `promo_play_history` WHERE member_id='$member_no' and flag<>'C') as c
						on a.promo_code=c.promo_code
						where
						'$this->doc_date' between a.start_date and a.end_date and
						a.type_member  ='New' and a.flag=''

				";
				
			    $result=mysql_query($find,$conn_online);
			    
			    $rows=$rows_find=mysql_num_rows($result);
			    $xarray=array();
				$i=0;
			    while($data=mysql_fetch_assoc($result)){
			    	$chk_play=$data['chk_play'];
			    	if($chk_play=="Y"){//เล่นได้
			    		$promo_color="#66FF99";
			    	}else{//ยังเล่นไม่ได้
			    		$promo_color="#dfdfdf";
			    	}
			    	$typ_member=$data['type_member'];
			    	if($typ_member=="New"){
			    		$type_member_show="สมัครใหม่";
			    	}else{
			    		$type_member_show="ต่ออายุ";
			    	}
			    	$point_dupble=$data['point_dupble'];
			    	if($point_dupble>1){
			    		$point_show="POINT x$point_dupble";
			    	}else{
			    		$point_show="";
			    	}
			    	$promo_year=$data['promo_year'];
			    	$des_cr=$data['des_cr'];
			    	$next_month=$data['next_month'];
			    	$time_play=$data['time_play'];
			    	$year_play=$data['year_play'];
			    	if($time_play==0){
			    		$time_play=12;
			    	}
			    	if($des_cr=="This"){
			    		$promo_detail="(เล่นได้ในเดือน $time_play-$year_play  $point_show)";
			    	}else if($des_cr=="Month_next_number"){
			    		$promo_detail="(เล่นได้ในเดือน $time_play-$year_play   $point_show)";
			    	}
			  
			    	$doc_no_play=$data['doc_no_play'];
			    	$chk_play_last=$data['chk_play_last'];
			    	if($chk_play_last=="N"){//at server online ไม่เคยใช้
			    		//chk local
			    		$chk_local="select a.* from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
			    		where 
			    		b.member_id='$member_no' and a.flag<>'C' and 
			    		a.promo_code='$data[promo_code]' and
			    		 a.doc_date between '$data[start_date]' and '$data[end_date]' ";
			    		
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
			 			$count_chk_local=count($data_chk_local);
			 			if($count_chk_local>0){//เล่นแล้ว
				    		$promo_detail="ใช้ไปแล้วก่อนหน้านี้ครับ";
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}
			    	}else{//เล่นแล้ว
			    		$chk_cancle="select doc_no from trn_diary1 where flag='C' and doc_no='$doc_no_play' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_cancle=$this->db->fetchAll($chk_cancle, 2); 
			 			$count__chk_cancle=count($data_chk_cancle);
			 			if($count__chk_cancle>0){ //ถ้าบิลโดน cancle
				    		$promo_detail="";
				    		$promo_color="#66FF99";
				    		$chk_play="Y";
			 			}else{//เล่นแล้ว
				    		$promo_detail=$chk_play_last;
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}
			    		
			    	}

			    	$xarray[$i]['start_date']=$data['start_date'];
			    	$xarray[$i]['end_date']=$data['end_date'];
			    	$xarray[$i]['promo_year']=$promo_year;
			    	$xarray[$i]['promo_code']=$data['promo_code'];
			    	$xarray[$i]['promo_name']=$data['promo_des'];
			    	$xarray[$i]['next_month']=$next_month;
			    	$xarray[$i]['dupble_point']=$point_dupble;
			    	$xarray[$i]['promo_status']=$chk_play;//รอ เพราะยังเล่นไม่ได้
			    	$xarray[$i]['promo_color']=$promo_color;
			    	$xarray[$i]['time_play']=$data['time_play'];
			    	$xarray[$i]['promo_detail']=$promo_detail;
			    	$xarray[$i]['xpoint']=$point_dupble;
					$xarray[$i]['point_first']=$point_first;
			    	
			    	

			    	/*$add="insert into promo_member(`type_member`, `start_date`, `end_date`, `promo_year`, `promo_code`, `promo_name`, `next_month`, `dupble_point`, `promo_status`, `promo_color`, `time_play`, `promo_detail`)
			    	values('$typ_member','$data[start_date]','$data[end_date]','$promo_year','$data[promo_code]','$data[promo_des]','$next_month','$point_dupble','$chk_play','$promo_color','$data[time_play]','$promo_detail')";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		 			$databill=$this->db->query($add, 2); */
		 			
			    	$i++;
			    }
			    
				
				}//end round
				
				
				
				
				
				
				//promotion new2015==================================================================
				if($year_app==2015){
				$find="
	
					SELECT 
						a.*,
						if(b.promo_code is null,'N','Y')as chk_play,

						month('$apply_date'+interval a.next_month month) as time_play,
						year('$apply_date'+interval a.next_month month) as year_play,
						year(a.end_date) as promo_year,
						if(c.promo_code is null,'N',concat('ใช้แล้วในบิล ',c.doc_no,' เมื่อ',c.doc_date)) as chk_play_last,
						c.doc_no as doc_no_play
					from
						promotion_cr as a left join (
							 SELECT 
							 	*,month('$apply_date'+interval next_month month) as time_play
							 FROM
							  `promotion_cr` 
							 where 
							 date(now()) between start_date and end_date and
							 type_member='$type_member' and
							 month('$apply_date'+interval next_month month)=month('$this->doc_date') and
							  year( '$apply_date' ) = year_apply
						) as b
						on a.promo_code=b.promo_code and
						month('$apply_date'+interval a.next_month month)=b.time_play 
						
						left join (SELECT promo_code,doc_no,doc_date FROM `promo_play_history` WHERE member_id='$member_no' and flag<>'C') as c
						on a.promo_code=c.promo_code
						where
						'$this->doc_date' between a.start_date and a.end_date and
						a.type_member  ='New' and a.flag=''

				";
				
			    $result=mysql_query($find,$conn_online);
			    
			    $rows=$rows_find=mysql_num_rows($result);
			    $xarray=array();
				$i=0;
			    while($data=mysql_fetch_assoc($result)){
			    	$chk_play=$data['chk_play'];
			    	if($chk_play=="Y"){//เล่นได้
			    		$promo_color="#66FF99";
			    	}else{//ยังเล่นไม่ได้
			    		$promo_color="#dfdfdf";
			    	}
			    	$typ_member=$data['type_member'];
			    	if($typ_member=="New"){
			    		$type_member_show="สมัครใหม่";
			    	}else{
			    		$type_member_show="ต่ออายุ";
			    	}
			    	$point_dupble=$data['point_dupble'];
			    	if($point_dupble>1){
			    		$point_show="POINT x$point_dupble";
			    	}else{
			    		$point_show="";
			    	}
			    	$promo_year=$data['promo_year'];
			    	$des_cr=$data['des_cr'];
			    	$next_month=$data['next_month'];
			    	$time_play=$data['time_play'];
			    	$year_play=$data['year_play'];
			    	if($time_play==0){
			    		$time_play=12;
			    	}
			    	if($des_cr=="This"){
			    		$promo_detail="(เล่นได้ในเดือน $time_play-$year_play  $point_show)";
			    	}else if($des_cr=="Month_next_number"){
			    		$promo_detail="(เล่นได้ในเดือน $time_play-$year_play   $point_show)";
			    	}
			  
			    	$doc_no_play=$data['doc_no_play'];
			    	$chk_play_last=$data['chk_play_last'];
			    	if($chk_play_last=="N"){//at server online ไม่เคยใช้
			    		//chk local
			    		$chk_local="select a.* from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
			    		where 
			    		b.member_id='$member_no' and a.flag<>'C' and 
			    		a.promo_code='$data[promo_code]' and
			    		 a.doc_date between '$data[start_date]' and '$data[end_date]' ";
			    		
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
			 			$count_chk_local=count($data_chk_local);
			 			if($count_chk_local>0){//เล่นแล้ว
				    		$promo_detail="ใช้ไปแล้วก่อนหน้านี้ครับ";
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}
			    	}else{//เล่นแล้ว
			    		$chk_cancle="select doc_no from trn_diary1 where flag='C' and doc_no='$doc_no_play' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_cancle=$this->db->fetchAll($chk_cancle, 2); 
			 			$count__chk_cancle=count($data_chk_cancle);
			 			if($count__chk_cancle>0){ //ถ้าบิลโดน cancle
				    		$promo_detail="";
				    		$promo_color="#66FF99";
				    		$chk_play="Y";
			 			}else{//เล่นแล้ว
				    		$promo_detail=$chk_play_last;
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}
			    		
			    	}

			    	$xarray[$i]['start_date']=$data['start_date'];
			    	$xarray[$i]['end_date']=$data['end_date'];
			    	$xarray[$i]['promo_year']=$promo_year;
			    	$xarray[$i]['promo_code']=$data['promo_code'];
			    	$xarray[$i]['promo_name']=$data['promo_des'];
			    	$xarray[$i]['next_month']=$next_month;
			    	$xarray[$i]['dupble_point']=$point_dupble;
			    	$xarray[$i]['promo_status']=$chk_play;//รอ เพราะยังเล่นไม่ได้
			    	$xarray[$i]['promo_color']=$promo_color;
			    	$xarray[$i]['time_play']=$data['time_play'];
			    	$xarray[$i]['promo_detail']=$promo_detail;
			    	$xarray[$i]['xpoint']=$point_dupble;
					$xarray[$i]['point_first']=$point_first;
			    	
			    	

			    	/*$add="insert into promo_member(`type_member`, `start_date`, `end_date`, `promo_year`, `promo_code`, `promo_name`, `next_month`, `dupble_point`, `promo_status`, `promo_color`, `time_play`, `promo_detail`)
			    	values('$typ_member','$data[start_date]','$data[end_date]','$promo_year','$data[promo_code]','$data[promo_des]','$next_month','$point_dupble','$chk_play','$promo_color','$data[time_play]','$promo_detail')";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		 			$databill=$this->db->query($add, 2); */
		 			
			    	$i++;
			    }
				
				
				}//end round
								
			    //print_r($xarray);
				return $xarray;
			    //return json_encode($xarray);
			
		}
		
		function promo_chk_play_renew($member_no){
 			 $conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			 mysql_query("SET NAMES utf8");
			 mysql_query("SET collection_connection='utf8_general_ci'");

				$sql="
				SELECT *
				FROM `member_his_seq`
				WHERE `member_no` = '$member_no'
				";
				//echo "$sql<br>";		
				mysql_select_db($this->db_online);
				$run_sql=mysql_query($sql,$conn_online);
				$rows_sql=mysql_num_rows($run_sql);
				$data=mysql_fetch_array($run_sql);
				$remark=$data['remark'];
			    $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_status_renew.php?member_no=$member_no", "r");
	            $chk_status_renew=@fgetss($fp, 4096);
	            
				if($chk_status_renew=="R"){
					$type_member="Re";
				}else{
					$type_member="New";
				}

	            
	            
				$profile="
					SELECT 
						a.customer_id,a.apply_date,a.expire_date,
						day(a.apply_date) as day_app,
						month(a.apply_date) as month_app,
						year(a.apply_date) as year_app,
						day('$this->doc_date') as day_now,
						month('$this->doc_date') as month_now,
						year('$this->doc_date') as year_now  
					FROM `member_history`  as a 
					where
						a.member_no='$member_no'
				";
				$run_profile=mysql_query($profile,$conn_online);
				$rows_profile=mysql_num_rows($run_profile);
				$data_profile=mysql_fetch_array($run_profile);
				$apply_date=$data_profile['apply_date'];
				$expire_date=$data_profile['expire_date'];
				$month_hbd=$data_profile['month_hbd'];
				$month_now=$data_profile['month_now'];
				$year_now=$data_profile['year_now'];
				$year_app=$data_profile['year_app'];
				$month_app=$data_profile['month_app'];
				$year_next=$year_app+1;
				

				

				
				$find="
				SELECT 
						a.*,
						if(b.promo_code is null,'N','Y')as chk_play,
						month('$apply_date'+interval a.next_month month) as time_play,
						
						year(a.end_date) as promo_year,
						if(c.promo_code is null,'N',concat('ใช้แล้วในบิล ',c.doc_no,' เมื่อ',c.doc_date)) as chk_play_last,
						c.doc_no as doc_no_play
					from
						promotion_cr as a left join (
							 SELECT 
							 	*,month('$apply_date'+interval next_month month) as time_play
							 FROM
							  `promotion_cr` 
							 where 
							 date(now()) between start_date and end_date and
							 type_member='$type_member' and
							 '$this->doc_date' between '$apply_date' and last_day('$apply_date'+interval next_month month) and
							  year( '$apply_date' ) = year_apply
						) as b
						on a.promo_code=b.promo_code and
						month('$apply_date'+interval a.next_month month)=b.time_play 
						
						left join (SELECT promo_code,doc_no,doc_date FROM `promo_play_history` WHERE member_id='$member_no' and flag<>'C') as c
						on a.promo_code=c.promo_code
						where
						'$this->doc_date' between a.start_date and a.end_date and
						a.type_member='Re' and a.flag=''
				";
				//echo $find;
			    $result=mysql_query($find,$conn_online);
			    
			    $rows=$rows_find=mysql_num_rows($result);
			    $xarray=array();
				$i=0;
			    while($data=mysql_fetch_assoc($result)){
			    	$chk_play=$data['chk_play'];
			    	if($chk_play=="Y"){//เล่นได้
			    		$promo_color="#66FF99";
			    	}else{//ยังเล่นไม่ได้
			    		$promo_color="#dfdfdf";
			    	}
			    	$typ_member=$data['type_member'];
			    	if($typ_member=="New"){
			    		$type_member_show="สมัครใหม่";
			    	}else{
			    		$type_member_show="ต่ออายุ";
			    	}
			    	$point_dupble=$data['point_dupble'];
			    	if($point_dupble>1){
			    		$point_show="POINT x$point_dupble";
			    	}else{
			    		$point_show="";
			    	}
			    	$promo_year=$data['promo_year'];
			    	$des_cr=$data['des_cr'];
			    	$next_month=$data['next_month'];
			    	$time_play=$data['time_play'];
			    	if($time_play==0){
			    		$time_play=12;
			    	}
			    	if($des_cr=="This"){
			    		$promo_detail="(เล่นเดือน $time_play  $point_show)";
			    	}else if($des_cr=="Month_next_number"){
			    		$promo_detail="(เล่นเดือน $time_play   $point_show)";

			    	}
			  
			    	$doc_no_play=$data['doc_no_play'];
			    	$chk_play_last=$data['chk_play_last'];
			    	if($chk_play_last=="N"){//at server online ไม่เคยใช้
			    		//chk local
			    		$chk_local="select a.* from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
			    		where 
			    		b.member_id='$member_no' and a.flag<>'C' and 
			    		a.promo_code='$data[promo_code]' and
			    		 a.doc_date between '$data[start_date]' and '$data[end_date]' ";
			    		
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
			 			$count_chk_local=count($data_chk_local);
			 			if($count_chk_local>0){//เล่นแล้ว
				    		$promo_detail="ใช้ไปแล้วก่อนหน้านี้ครับ";
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}
			    	}else{//เล่นแล้ว
			    		$chk_cancle="select doc_no from trn_diary1 where flag='C' and doc_no='$doc_no_play' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_cancle=$this->db->fetchAll($chk_cancle, 2); 
			 			$count__chk_cancle=count($data_chk_cancle);
			 			if($count__chk_cancle>0){ //ถ้าบิลโดน cancle
				    		$promo_detail="";
				    		$promo_color="#66FF99";
				    		$chk_play="Y";
			 			}else{//เล่นแล้ว
				    		$promo_detail=$chk_play_last;
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}

			    	}

			    	$xarray[$i]['start_date']=$data['start_date'];
			    	$xarray[$i]['end_date']=$data['end_date'];
			    	$xarray[$i]['promo_year']=$promo_year;
			    	$xarray[$i]['promo_code']=$data['promo_code'];
			    	$xarray[$i]['promo_name']=$data['promo_des'];
			    	$xarray[$i]['next_month']=$next_month;
			    	$xarray[$i]['dupble_point']=$point_dupble;
			    	$xarray[$i]['promo_status']=$chk_play;//รอ เพราะยังเล่นไม่ได้
			    	$xarray[$i]['promo_color']=$promo_color;
			    	$xarray[$i]['time_play']=$data['time_play'];
			    	$xarray[$i]['promo_detail']=$promo_detail;
					$xarray[$i]['xpoint']=$point_dupble;
			    	/*$add="insert into promo_member(`type_member`, `start_date`, `end_date`, `promo_year`, `promo_code`, `promo_name`, `next_month`, `dupble_point`, `promo_status`, `promo_color`, `time_play`, `promo_detail`)
			    	values('$typ_member','$data[start_date]','$data[end_date]','$promo_year','$data[promo_code]','$data[promo_des]','$next_month','$point_dupble','$chk_play','$promo_color','$data[time_play]','$promo_detail')";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
		 			$databill=$this->db->query($add, 2); */
		 			
			    	$i++;
			    }
			    
				
			if($year_app<2015){ //รอบ	
			//renew 2014
			//if($date_now>='2014-01-01'){
			$chk_set="select * from promo_play_history where member_id='$member_no' and flag<>'C' and promo_code in('OX07111113','OX07141113','OX07151113')";
			mysql_select_db($this->db_online);
			$run_chk_set=mysql_query($chk_set,$conn_online);
			$rows_chk_set=mysql_num_rows($run_chk_set);
			$data_chk_set=mysql_fetch_array($run_chk_set);
			$promo_code_set=$data_chk_set['promo_code'];
			$doc_no_set=$data_chk_set['doc_no'];
			$doc_date_set=$data_chk_set['doc_date'];
			$member_set=$data_chk_set['member_id'];
			if($rows_chk_set==0){ //ที่ online ไม่มีให้ chk ที่เครื่องต่อ
			    //chk local
	    		$chk_local="select a.*,b.member_id from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
	    		where 
	    		b.member_id='$member_no' and a.flag<>'C' and 
	    		a.promo_code in('OX07111113','OX07141113','OX07151113') and
	    		 a.doc_date >='2014-01-01' limit 1 ";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
	 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
	 			$rows_chk_set=count($data_chk_local);
				$promo_code_set=$data_chk_local[0]['promo_code'];
				$doc_no_set=$data_chk_local[0]['doc_no'];
				$doc_date_set=$data_chk_local[0]['doc_date'];
				$member_set=$data_chk_local[0]['member_id'];
			
			}

			 			
			 			
			if($rows_chk_set>0){


				$x="ใช้แล้วในบิล $doc_no_set เมื่อ $doc_date_set ด้วยบัตร $member_set";
				
	    		$promo_detail=$x;
	    		$promo_color="#FF3333";
	    		$chk_play="N";
				if($promo_code_set=="OX07111113"){
					$show_play1=$x;
				}else if($promo_code_set=="OX07141113"){
					$show_play2=$x;
				}else if($promo_code_set=="OX07151113"){	
					$show_play2=$x;
				}
				
			}else{
				if($month_app==12){
					$month_app_pointx2=1;
					$year_app_pointx2=$year_app+1;
				}else{
					$month_app_pointx2=$month_app+1;
					$year_app_pointx2=$year_app;
				}
				$point_dupble_remark="(เล่นเดือน $month_app_pointx2-$year_app_pointx2 POINT x2)";	
				//echo "chk:$month_now==$month_app || $month_now==$month_app_pointx2";
				if($type_member=="Re" && $year_app==2014 && ($month_now==$month_app || $month_now==$month_app_pointx2) ){

					
					
					
					if($month_now==$month_app_pointx2){
						$point_dupble=2;
					}else{
						$point_dupble=1;
					}
				
					$promo_detail=$point_dupble_remark;
					$promo_color="#66FF99";
					$chk_play="Y";				
					$show_play1="<img src='../img/tick.png' hieght='20' width='20'></img>";
					$show_play2="<img src='../img/tick.png' hieght='20' width='20'></img>";
					$show_play3="<img src='../img/tick.png' hieght='20' width='20'></img>";
					


			
				}else{
					
					$promo_detail=$point_dupble_remark;
					$promo_color="#FF3333";
					$chk_play="N";				
					$show_play1="";
					$show_play2="";
					$show_play3="";
				}
			}


			array_push($xarray,array("start_date"=>"2014-01-01","end_date"=>"2015-02-28","promo_year"=>$promo_year,"promo_code"=>"OX07111113","promo_name"=>"ซื้อหมวด Colours + สินค้าอื่นๆครบ 1,200(สุทธิ) รับฟรี OC 1 ชิ้นราคาไม่เกิน 255บาท","next_month"=>3,"dupble_point"=>$point_dupble,"promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>$time_play,"promo_detail"=>$promo_detail,"xpoint"=>$point_dupble));  
			array_push($xarray,array("start_date"=>"2014-01-01","end_date"=>"2015-02-28","promo_year"=>$promo_year,"promo_code"=>"OX07141113","promo_name"=>"ซื้อหมวด Body หรือ Hair + สินค้าอื่นๆครบ 800(สุทธิ) รับฟรี Body หรือ Hair 1 ชิ้นราคาไม่เกิน 155บาท","next_month"=>3,"dupble_point"=>$point_dupble,"promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>$time_play,"promo_detail"=>$promo_detail,"xpoint"=>$point_dupble));  
			array_push($xarray,array("start_date"=>"2014-01-01","end_date"=>"2015-02-28","promo_year"=>$promo_year,"promo_code"=>"OX07151113","promo_name"=>"ซื้อหมวด Facial Regimen + สินค้าอื่นๆครบ 1,500(สุทธิ) รับฟรี Cleansing Foam หรือ Cleansing Gel 1 ชิ้น","next_month"=>3,"dupble_point"=>$point_dupble,"promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>$time_play,"promo_detail"=>$promo_detail,"xpoint"=>$point_dupble));  
			
			} //รอบ
			
			
			
			if($year_app==2015){ //รอบ
				//renew 2015 ============================================================================================
				//if($date_now>='2014-01-01'){
				$chk_set="select * from promo_play_history where member_id='$member_no' and flag<>'C' and promo_code in('OX07771014','OX07781014','OX07791014')";
				mysql_select_db($this->db_online);
				$run_chk_set=mysql_query($chk_set,$conn_online);
				$rows_chk_set=mysql_num_rows($run_chk_set);
				$data_chk_set=mysql_fetch_array($run_chk_set);
				$promo_code_set=$data_chk_set['promo_code'];
				$doc_no_set=$data_chk_set['doc_no'];
				$doc_date_set=$data_chk_set['doc_date'];
				$member_set=$data_chk_set['member_id'];
				if($rows_chk_set==0){ //ที่ online ไม่มีให้ chk ที่เครื่องต่อ
					//chk local
					$chk_local="select a.*,b.member_id from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
					where 
					b.member_id='$member_no' and a.flag<>'C' and 
					a.promo_code in('OX07771014','OX07781014','OX07791014') and
					 a.doc_date >='2015-01-01' limit 1 ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
					$data_chk_local=$this->db->fetchAll($chk_local, 2); 
					$rows_chk_set=count($data_chk_local);
					$promo_code_set=$data_chk_local[0]['promo_code'];
					$doc_no_set=$data_chk_local[0]['doc_no'];
					$doc_date_set=$data_chk_local[0]['doc_date'];
					$member_set=$data_chk_local[0]['member_id'];
				
				}
	
							
							
				if($rows_chk_set>0){
	
	
					$x="ใช้แล้วในบิล $doc_no_set เมื่อ $doc_date_set ด้วยบัตร $member_set";
					
					$promo_detail=$x;
					$promo_color="#FF3333";
					$chk_play="N";
					if($promo_code_set=="OX07771014"){
						$show_play1=$x;
					}else if($promo_code_set=="OX07781014"){
						$show_play2=$x;
					}else if($promo_code_set=="OX07791014"){	
						$show_play2=$x;
					}
					
				}else{
					if($month_app==12){
						$month_app_pointx2=1;
						$year_app_pointx2=$year_app+1;
					}else{
						$month_app_pointx2=$month_app+1;
						$year_app_pointx2=$year_app;
					}
					$point_dupble_remark="(เล่นเดือน $month_app_pointx2-$year_app_pointx2 POINT x2)";	
					//echo "chk:$month_now==$month_app || $month_now==$month_app_pointx2";
					if($type_member=="Re" && $year_app==2015 && ($month_now==$month_app || $month_now==$month_app_pointx2) ){
	
						
						
						
						if($month_now==$month_app_pointx2){
							$point_dupble=2;
						}else{
							$point_dupble=1;
						}
					
						$promo_detail=$point_dupble_remark;
						$promo_color="#66FF99";
						$chk_play="Y";				
						$show_play1="<img src='../img/tick.png' hieght='20' width='20'></img>";
						$show_play2="<img src='../img/tick.png' hieght='20' width='20'></img>";
						$show_play3="<img src='../img/tick.png' hieght='20' width='20'></img>";
						
	
	
				
					}else{
						
						$promo_detail=$point_dupble_remark;
						$promo_color="#FF3333";
						$chk_play="N";				
						$show_play1="";
						$show_play2="";
						$show_play3="";
					}
				}
	
	
				array_push($xarray,array("start_date"=>"2015-01-01","end_date"=>"2016-02-28","promo_year"=>$promo_year,"promo_code"=>"OX07771014","promo_name"=>"ซื้อผลิตภัณฑ์ใดๆภายในร้านครบ 800 บาท(สุทธิ) รับฟรีสินค้าใดก็ได้ในร้าน 1 ชิ้น ราคาไม่เกิน 155 บาท","next_month"=>3,"dupble_point"=>$point_dupble,"promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>$time_play,"promo_detail"=>$promo_detail,"xpoint"=>$point_dupble));  
				array_push($xarray,array("start_date"=>"2015-01-01","end_date"=>"2016-02-28","promo_year"=>$promo_year,"promo_code"=>"OX07781014","promo_name"=>"ซื้อผลิตภัณฑ์ใดๆภายในร้านครบ 1,200 บาท(สุทธิ) รับฟรีสินค้าใดก็ได้ในร้าน 1 ชิ้น ราคาไม่เกิน 255 บาท","next_month"=>3,"dupble_point"=>$point_dupble,"promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>$time_play,"promo_detail"=>$promo_detail,"xpoint"=>$point_dupble));  
				array_push($xarray,array("start_date"=>"2015-01-01","end_date"=>"2016-02-28","promo_year"=>$promo_year,"promo_code"=>"OX07791014","promo_name"=>"ซื้อผลิตภัณฑ์ใดๆภายในร้านครบ 1,500 บาท(สุทธิ) รับฟรีสินค้าใดก็ได้ในร้าน 1 ชิ้น ราคาไม่เกิน 315 บาท","next_month"=>3,"dupble_point"=>$point_dupble,"promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>$time_play,"promo_detail"=>$promo_detail,"xpoint"=>$point_dupble));  
			
			}//รอบ
						
			return $xarray;

		}
		
		function member_bypeple($member_no){

            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_bypeple.php?member_no=$member_no", "r");
            $text=@fgetss($fp, 4096);
            return  $text;

		}
		function fomatdate($dt) {   
			$y=substr($dt,0,4);
			$m=substr($dt,5,2);
			$d=substr($dt,8,2);
			$dt_fomat="$d/$m/$y";
			return $dt_fomat;
		}
		
		function promo_chk_hbd($member_no){
			$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			mysql_query("SET NAMES utf8");
			mysql_query("SET collection_connection='utf8_general_ci'");
				$sql="
				SELECT *
				FROM `member_his_seq`
				WHERE `member_no` = '$member_no'
				";
				//echo "$sql<br>";		
				mysql_select_db($this->db_online);
				$run_sql=mysql_query($sql,$conn_online);
				$rows_sql=mysql_num_rows($run_sql);
				$data=mysql_fetch_array($run_sql);
				$remark=$data['remark'];
	            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_status_renew.php?member_no=$member_no", "r");
	            $chk_status_renew=@fgetss($fp, 4096);
	            
				if($chk_status_renew=="R"){
					$type_member="Re";
				}else{
					$type_member="New";
				}
				
				
				
				$profile="
					SELECT 
						a.customer_id,a.apply_date,a.expire_date,
						day(a.apply_date) as day_app,
						month(a.apply_date) as month_app,
						year(a.apply_date) as year_app,
						month('$this->doc_date') as month_now,
						year('$this->doc_date') as year_now,
						a.age_card
					FROM `member_history`  as a 
					where
						a.member_no='$member_no'
				";
				mysql_select_db($this->db_online);
				$run_profile=mysql_query($profile,$conn_online);
				$rows_profile=mysql_num_rows($run_profile);
				$data_profile=mysql_fetch_array($run_profile);
				$customer_id=$data_profile['customer_id'];
				$apply_date=$data_profile['apply_date'];
				$expire_date=$data_profile['expire_date'];
				$month_now=$data_profile['month_now'];
				$year_now=$data_profile['year_now'];
				$day_app=$data_profile['day_app'];
				$month_app=$data_profile['month_app'];
				$year_app=$data_profile['year_app'];
				$year_next=$year_app+1;
				$age_card=$data_profile['age_card'];
	

				$map_member=$this->member_bypeple($member_no);

				$regis="
					SELECT *,day(birthday) as d_hbd,month(birthday) as m_hbd,year(birthday) as y_hbd from member_register where customer_id='$customer_id'
				";

				$run_regis=mysql_query($regis,$conn_online);
				$data_regis=mysql_fetch_array($run_regis);
				$birthday=$data_regis['birthday'];
				$d_hbd=$data_regis['d_hbd'];
				$m_hbd=$data_regis['m_hbd'];
				$y_hbd=$data_regis['y_hbd'];
				//echo "Month_now=$month_now<br>";
				if($m_hbd==12 && $month_now==1){ //คนที่เกิด ธ.ค. แต่มา ม.ค.
					$year12=$year_now-1;
					$first_hbd="$year12-$m_hbd-01";
				}else if($m_hbd==1 && $month_now==1){ //คนที่เกิด ม.ค. มาเล่นโปรเก่า ต้องไปเล่นในรอบโปรใหม่ โปรนี้ไม่ได้แล้ว
					$year12=$year_now-1;
					$first_hbd="$year12-$m_hbd-01";					
				}else{
					$first_hbd="$year_now-$m_hbd-01";
				}
				$first_hbd_format=$this->fomatdate($first_hbd);	


				$xarray=array();
				
				
			    if(($year_now==2014) || ($year_now=='2015' && $m_hbd==12)){ //รอบ
				//สมัครใหม่ 2014
				$find_pro="select *,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				,if('$this->doc_date' between '$first_hbd' and last_day('$first_hbd' + interval 1 month),'yes_play_hbd','no_play_hdb') as chk_play_hbd
				,last_day('$first_hbd' + interval 1 month) as end_hbd
				from
				promotion_cr where type_member='HBD' and '$this->doc_date' between start_date and end_date and promo_code='OM02071113' and flag<>'C' ";
				//echo $find_pro;
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
					$promo_year=$data_find_pro['promo_year'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$year_start_pro=date("Y",strtotime($this->doc_date));
					$month_start_pro=date("m",strtotime($this->doc_date));
					if($year_start_pro=="2014" && ($month_start_pro=="11" || $month_start_pro=="12")){
						$chk_play_hbd="yes_play_hbd";
					}
					
					
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);					
					
					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
							//ต้องสมัครปี 2014
							if($type_member=="New" && $year_app=='2014' && ($age_card==0 || $age_card==2)){
									//<=เดือนเกิดหรือไม่
									if($year_app==$year_now && $month_app<$m_hbd){
										$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
										$remark="";//เหตุผล
									}else if($year_app==$year_now && $month_app==$m_hbd && $day_app<=$d_hbd){
										$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
										$remark="";//เหตุผล									
									}else{
										$show_play="";
										$remark="ต้องสมัครก่อนเดือนเกิดหรือก่อนวันเกิดเท่านั้นครับ";//เหตุผล
								
									}
									
									//เช็คระยะเวลาตามเดือนเกิด
									if($chk_play_hbd=="no_play_hdb"){
										$show_play="";
										$remark="เล่นในเดือนเกิดหรือถัดจากเดือนเกิด 1 เดือน";//เหตุผล
									}
									
									//เล่นได้ปีละครัง
									$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C' group by doc_no order by doc_date desc limit 1  ";
									//echo $play;
									$run_play=mysql_query($play,$conn_online);
									$rows_play=mysql_num_rows($run_play);
									if($rows_play>0){//
										$dataplay=mysql_fetch_array($run_play);
										$mem_play=$dataplay['member_id'];
										$doc_no=$dataplay['doc_no'];
										$doc_date_play=$dataplay['doc_date'];
										$doc_date_format=$this->fomatdate($doc_date_play);
										$year_play=date("Y",strtotime($doc_date_play));
										if($year_play==$year_now){
											$show_play="";
											$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
								
										}
										
										$chk_play_all="1";
								
								   }else{
										//chk local
							    		$chk_local="
										select 
											a.* 
										from 
										 	trn_diary1 as a left join (select refer_doc_no from trn_diary1 where doc_date between '$start_date' and '$end_date' and doc_tp='CN' and flag<>'C') as b
										 	on a.doc_no=b.refer_doc_no
							    		where 
							    			a.member_id='$member_no' and a.flag<>'C' and 
							    			a.co_promo_code='$promo_code' and
							    		 	a.doc_date between '$start_date' and '$end_date' and
											year(a.doc_date)=year('$this->doc_date') and
											b.refer_doc_no is null
										";
							    		//echo $chk_local;
										$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
							 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
							 			$count_chk_local=count($data_chk_local);
							 			if($count_chk_local>0){//เล่นแล้ว
											$show_play="";
											$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
											
							 			}
							
							       }
							}else{
								$show_play="";
								$remark="ต้องสมัครในปี 2014 ";//เหตุผล	
						
							}		
							
							
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
				
					}
					
					

					
					//echo "SHOW=".$show_play;
					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}

				
					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>""));  
				}
				
				
				
				//สมาชิก 2 ปี สมัคร ปี 2012-2013
				$find_pro="select 
				
				*,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				,if('$this->doc_date' between '$first_hbd' and last_day('$first_hbd' + interval 1 month),'yes_play_hbd','no_play_hdb') as chk_play_hbd
				,last_day('$first_hbd' + interval 1 month) as end_hbd
				from
				promotion_cr where type_member='HBD' and '$this->doc_date' between start_date and end_date and promo_code='OM02081113' and flag<>'C' ";
				
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$year_start_pro=date("Y",strtotime($this->doc_date));
					$month_start_pro=date("m",strtotime($this->doc_date));
					if($year_start_pro=="2014" && ($month_start_pro=="11" || $month_start_pro=="12")){
						$chk_play_hbd="yes_play_hbd";
					}
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);					

					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
							//ต้องสมัครปี 2012 - 2013
							//echo "ya=$year_app/type_new=$type_member";
							//if( ($type_member=="New") && ($year_app=="2012" || $year_app=="2013")  ){
							if( ($age_card==0 || $age_card==2) && $year_app>=2012 && $year_app!='2014'  ){
							
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล
							}else{
								$show_play="";
								$remark="ต้องสมัครในปี 2012 - 2013 ";//เหตุผล	
						
							}		
							
		
							//เช็คระยะเวลาตามเดือนเกิด
							if($chk_play_hbd=="no_play_hdb"){
								$show_play="";
								$remark="เล่นในเดือนเกิดหรือถัดจากเดือนเกิด 1 เดือน";//เหตุผล
							}
						
							
							//เล่นได้ปีละครัง
							$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C' group by doc_no  order by doc_date desc limit 1 ";
							$run_play=mysql_query($play,$conn_online);
							$rows_play=mysql_num_rows($run_play);
							if($rows_play>0 && $chk_play_all==""){
								$dataplay=mysql_fetch_array($run_play);
								$mem_play=$dataplay['member_id'];
								$doc_no=$dataplay['doc_no'];
								$doc_date_play=$dataplay['doc_date'];
								$doc_date_format=$this->fomatdate($doc_date_play);
								$year_play=date("Y",strtotime($doc_date_play));
								if($year_play==$year_now){
									$show_play="";
									$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
								}
								
								$chk_play_all="2";
						
							}else{
									//chk local
						    		$chk_local="
										select 
											a.* 
										from 
										 	trn_diary1 as a left join (select refer_doc_no from trn_diary1 where doc_date between '$start_date' and '$end_date' and doc_tp='CN' and flag<>'C') as b
										 	on a.doc_no=b.refer_doc_no
							    		where 
							    			a.member_id='$member_no' and a.flag<>'C' and 
							    			a.co_promo_code='$promo_code' and
							    		 	a.doc_date between '$start_date' and '$end_date' and
											year(a.doc_date)=year('$this->doc_date') and
											b.refer_doc_no is null
									  ";
						    		//echo $chk_local;
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
						 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
						 			$count_chk_local=count($data_chk_local);
						 			if($count_chk_local>0){//เล่นแล้ว
										$show_play="";
										$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
						 			}
							
							}
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
					}
					

					if($chk_play_all=="1"){ //ดัก
							$show_play="";
							$remark="ต้องสมัครในปี 2012 - 2013 ";//เหตุผล	
					}
					
					
					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}
					
					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>"")); 
				
				}
				
				
				
				//สมาชิก 4 ปี ต่ออายุ ปี 2012
				$find_pro="select 
				
				*,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				,if('$this->doc_date' between '$first_hbd' and last_day('$first_hbd' + interval 1 month),'yes_play_hbd','no_play_hdb') as chk_play_hbd
				,last_day('$first_hbd' + interval 1 month) as end_hbd
				from
				promotion_cr where type_member='HBD' and '$this->doc_date' between start_date and end_date and promo_code='OM02091113' and flag<>'C' ";
				//echo $find_pro;
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$year_start_pro=date("Y",strtotime($this->doc_date));
					$month_start_pro=date("m",strtotime($this->doc_date));
					if($year_start_pro=="2014" && ($month_start_pro=="11" || $month_start_pro=="12")){
						$chk_play_hbd="yes_play_hbd";
					}
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);
					
					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
							
							//if( $type_member=="Re" && $age_card==4   ){
							//echo "$age_card==4 && $year_app==2014 (int)$month_app<(int)$m_hbd";
							if($age_card==4){
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล				
							}else{
								$show_play="";
								$remark="ต้องเป็นสมาชิก 4 ปี";//เหตุผล	
						
							}		
		
							
							//เช็คระยะเวลาตามเดือนเกิด
							if($chk_play_hbd=="no_play_hdb"){
								$show_play="";
								$remark="เล่นในเดือนเกิดหรือถัดจากเดือนเกิด 1 เดือน";//เหตุผล
							}
							
							//เล่นได้ปีละครัง
							$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C' group by doc_no order by doc_date desc limit 1 ";
							//echo $play;
							$run_play=mysql_query($play,$conn_online);
							$rows_play=mysql_num_rows($run_play);
							if($rows_play>0  && $chk_play_all==""){//
								$dataplay=mysql_fetch_array($run_play);
								$mem_play=$dataplay['member_id'];
								$doc_no=$dataplay['doc_no'];
								$doc_date_play=$dataplay['doc_date'];
								$doc_date_format=$this->fomatdate($doc_date_play);
								$year_play=date("Y",strtotime($doc_date_play));
								if($year_play==$year_now){
									$show_play="";
									$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
						
								}
						
							}else{
									//chk local
						    		$chk_local="
										select 
											a.* 
										from 
										 	trn_diary1 as a left join (select refer_doc_no from trn_diary1 where doc_date between '$start_date' and '$end_date' and doc_tp='CN' and flag<>'C') as b
										 	on a.doc_no=b.refer_doc_no
							    		where 
							    			a.member_id='$member_no' and a.flag<>'C' and 
							    			a.co_promo_code='$promo_code' and
							    		 	a.doc_date between '$start_date' and '$end_date' and
											year(a.doc_date)=year('$this->doc_date') and
											b.refer_doc_no is null
									 
									 ";
						    		//echo $chk_local;
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
						 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
						 			$count_chk_local=count($data_chk_local);
						 			if($count_chk_local>0){//เล่นแล้ว
										$show_play="";
										$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
						 			}
							
							}
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
				
					}
					
					if($chk_play_all=="2"){ //ดัก
								$show_play="";
								$remark="ต้องเป็นสมาชิก 4 ปี";//เหตุผล	
					}


					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}

					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>"")); 
				}
				
				
				}//รอบ
				
				
				
				
				if($year_now==2015 && $m_hbd!=12){ //รอบ
				//promotion2015=======================================================================
				$first_hbd="$year_now-$m_hbd-01";
				//สมัครใหม่ 2015
				$find_pro="select *,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				,if('$this->doc_date' between '$first_hbd' and last_day('$first_hbd' + interval 1 month),'yes_play_hbd','no_play_hdb') as chk_play_hbd
				,last_day('$first_hbd' + interval 1 month) as end_hbd
				from
				promotion_cr where type_member='HBD' and '$this->doc_date' between start_date and end_date and promo_code='OX02651014' and flag<>'C' ";
				//echo $find_pro;
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
					$promo_year=$data_find_pro['promo_year'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$year_start_pro=date("Y",strtotime($this->doc_date));
					$month_start_pro=date("m",strtotime($this->doc_date));
					//สำหรับขายสิทธิ์
					/*if($year_start_pro=="2015" && ($month_start_pro=="11" || $month_start_pro=="12")){
						$chk_play_hbd="yes_play_hbd";
					}*/
					
					
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);					
					
					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
							//ต้องสมัครปี 2014
							if($type_member=="New" && $year_app=='2015' && ($age_card==0 || $age_card==2)){
									//<=เดือนเกิดหรือไม่
									
									if($year_app==$year_now && $month_app<$m_hbd){
										$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
										$remark="";//เหตุผล
									}else if($year_app==$year_now && $month_app==$m_hbd && (int)$day_app<=(int)$d_hbd){
										$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
										$remark="";//เหตุผล
									}else{
										$show_play="";
										$remark="ต้องสมัครก่อนเดือนเกิดหรือก่อนวันเกิดเท่านั้นครับ";//เหตุผล
								
									}
									
									//เช็คระยะเวลาตามเดือนเกิด
									if($chk_play_hbd=="no_play_hdb"){
										$show_play="";
										$remark="เล่นในเดือนเกิดหรือถัดจากเดือนเกิด 1 เดือน";//เหตุผล
									}
									
									//เล่นได้ปีละครัง
									$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C' group by doc_no  ";
									//echo $play;
									$run_play=mysql_query($play,$conn_online);
									$rows_play=mysql_num_rows($run_play);
									if($rows_play>=2){ //เล่นครบ 2 ครั้ง
										$show_play="";
										$remark="ลูกค้าใช้ครบ 2 ครั้งแล้ว";//เหตุผล	
								
									}else if($rows_play==1){//
										$dataplay=mysql_fetch_array($run_play);
										$mem_play=$dataplay['member_id'];
										$doc_no=$dataplay['doc_no'];
										$doc_date_play=$dataplay['doc_date'];
										$doc_date_format=$this->fomatdate($doc_date_play);
										$year_play=date("Y",strtotime($doc_date_play));
										if($year_play==$year_now){
											$show_play="";
											$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
								
										}
								
								   }else{
										//chk local
							    		$chk_local="
										select 
											a.* 
										from 
										 	trn_diary1 as a left join (select refer_doc_no from trn_diary1 where doc_date between '$start_date' and '$end_date' and doc_tp='CN' and flag<>'C') as b
										 	on a.doc_no=b.refer_doc_no
							    		where 
							    			a.member_id='$member_no' and a.flag<>'C' and 
							    			a.co_promo_code='$promo_code' and
							    		 	a.doc_date between '$start_date' and '$end_date' and
											year(a.doc_date)=year('$this->doc_date') and
											b.refer_doc_no is null
										";
							    		//echo $chk_local;
										$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
							 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
							 			$count_chk_local=count($data_chk_local);
							 			if($count_chk_local>0){//เล่นแล้ว
											$show_play="";
											$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
							 			}
							
							       }
							}else{
								$show_play="";
								$remark="ต้องสมัครในปี 2015 ";//เหตุผล	
						
							}		
							
							
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
				
					}
					
					

					
					//echo "SHOW=".$show_play;
					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}

				
					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>""));  
				}
				
				
				
				//สมาชิก 2 ปี สมัคร ปี 2013-2014
				$find_pro="select 
				
				*,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				,if('$this->doc_date' between '$first_hbd' and last_day('$first_hbd' + interval 1 month),'yes_play_hbd','no_play_hdb') as chk_play_hbd
				,last_day('$first_hbd' + interval 1 month) as end_hbd
				from
				promotion_cr where type_member='HBD' and '$this->doc_date' between start_date and end_date and promo_code='OX02671014' and flag<>'C' ";
				
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$year_start_pro=date("Y",strtotime($this->doc_date));
					$month_start_pro=date("m",strtotime($this->doc_date));
					//สำหรับขายสิทธิ์
					/*if($year_start_pro=="2015" && ($month_start_pro=="11" || $month_start_pro=="12")){
						$chk_play_hbd="yes_play_hbd";
					}*/
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);					

					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
							//ต้องสมัครปี 2012 - 2013
							//echo "ya=$year_app/type_new=$type_member";
							//if( ($type_member=="New") && ($year_app=="2012" || $year_app=="2013")  ){
							if( ($age_card==0 || $age_card==2) && $year_app>=2013 && $year_app!='2015'  ){
							
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล
							}else{
								$show_play="";
								$remark="ต้องสมัครในปี 2013 - 2014 ";//เหตุผล	
						
							}		
							
		
							//เช็คระยะเวลาตามเดือนเกิด
							if($chk_play_hbd=="no_play_hdb"){
								$show_play="";
								$remark="เล่นในเดือนเกิดหรือถัดจากเดือนเกิด 1 เดือน";//เหตุผล
							}
						
							
							//เล่นได้ปีละครัง
							$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C' group by doc_no   ";
							$run_play=mysql_query($play,$conn_online);
							$rows_play=mysql_num_rows($run_play);
							if($rows_play>=2){ //เล่นครบ 2 ครั้ง
								$show_play="";
								$remark="ลูกค้าใช้ครบ 2 ครั้งแล้ว";//เหตุผล	
						
							}else if($rows_play==1){//
								$dataplay=mysql_fetch_array($run_play);
								$mem_play=$dataplay['member_id'];
								$doc_no=$dataplay['doc_no'];
								$doc_date_play=$dataplay['doc_date'];
								$doc_date_format=$this->fomatdate($doc_date_play);
								$year_play=date("Y",strtotime($doc_date_play));
								if($year_play==$year_now){
									$show_play="";
									$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
						
								}
						
							}else{
									//chk local
						    		$chk_local="
										select 
											a.* 
										from 
										 	trn_diary1 as a left join (select refer_doc_no from trn_diary1 where doc_date between '$start_date' and '$end_date' and doc_tp='CN' and flag<>'C') as b
										 	on a.doc_no=b.refer_doc_no
							    		where 
							    			a.member_id='$member_no' and a.flag<>'C' and 
							    			a.co_promo_code='$promo_code' and
							    		 	a.doc_date between '$start_date' and '$end_date' and
											year(a.doc_date)=year('$this->doc_date') and
											b.refer_doc_no is null
									  ";
						    		//echo $chk_local;
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
						 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
						 			$count_chk_local=count($data_chk_local);
						 			if($count_chk_local>0){//เล่นแล้ว
										$show_play="";
										$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
						 			}
							
							}
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
					}
					

					
					
					
					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}
					
					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>"")); 
				
				}
				
				
				
				//สมาชิก 4 ปี ต่ออายุ ปี 2012
				$find_pro="select 
				
				*,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				,if('$this->doc_date' between '$first_hbd' and last_day('$first_hbd' + interval 1 month),'yes_play_hbd','no_play_hdb') as chk_play_hbd
				,last_day('$first_hbd' + interval 1 month) as end_hbd
				from
				promotion_cr where type_member='HBD' and '$this->doc_date' between start_date and end_date and promo_code='OX02691014' and flag<>'C' ";
				
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$year_start_pro=date("Y",strtotime($this->doc_date));
					$month_start_pro=date("m",strtotime($this->doc_date));
					//สำหรับขายสิทธิ์
					/*if($year_start_pro=="2015" && ($month_start_pro=="11" || $month_start_pro=="12")){
						$chk_play_hbd="yes_play_hbd";
					}*/
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);
					
					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
							
							//if( $type_member=="Re" && $age_card==4   ){
							if($age_card==4 && $year_app<2015){
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล
							}else if($age_card==4 && $year_app==2015  &&  $month_app<$m_hbd){
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล	
							}else if($age_card==4 && $year_app==2015  &&  $month_app==$m_hbd && $day_app<=$d_hbd){
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล																
							}else{
								$show_play="";
								$remark="ต้องเป็นสมาชิก 4 ปี";//เหตุผล	
						
							}		
		
							
							//เช็คระยะเวลาตามเดือนเกิด
							if($chk_play_hbd=="no_play_hdb"){
								$show_play="";
								$remark="เล่นในเดือนเกิดหรือถัดจากเดือนเกิด 1 เดือน";//เหตุผล
							}
							
							//เล่นได้ปีละครัง
							$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C' group by doc_no  ";
							//echo $play;
							$run_play=mysql_query($play,$conn_online);
							$rows_play=mysql_num_rows($run_play);
							if($rows_play>=2){ //เล่นครบ 2 ครั้ง
								$show_play="";
								$remark="ลูกค้าใช้ครบ 2 ครั้งแล้ว";//เหตุผล	
						
							}else if($rows_play==1){//
								$dataplay=mysql_fetch_array($run_play);
								$mem_play=$dataplay['member_id'];
								$doc_no=$dataplay['doc_no'];
								$doc_date_play=$dataplay['doc_date'];
								$doc_date_format=$this->fomatdate($doc_date_play);
								$year_play=date("Y",strtotime($doc_date_play));
								if($year_play==$year_now){
									$show_play="";
									$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
						
								}
						
							}else{
									//chk local
						    		$chk_local="
										select 
											a.* 
										from 
										 	trn_diary1 as a left join (select refer_doc_no from trn_diary1 where doc_date between '$start_date' and '$end_date' and doc_tp='CN' and flag<>'C') as b
										 	on a.doc_no=b.refer_doc_no
							    		where 
							    			a.member_id='$member_no' and a.flag<>'C' and 
							    			a.co_promo_code='$promo_code' and
							    		 	a.doc_date between '$start_date' and '$end_date' and
											year(a.doc_date)=year('$this->doc_date') and
											b.refer_doc_no is null
									 
									 ";
						    		//echo $chk_local;
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
						 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
						 			$count_chk_local=count($data_chk_local);
						 			if($count_chk_local>0){//เล่นแล้ว
										$show_play="";
										$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
						 			}
							
							}
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
				
					}
					


					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}

					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>"")); 
				}
				
				}//รอบ
								
				return $xarray;
			    //return json_encode($xarray);
		}
		
		
		function promo_wait($member_no,$doc_date,$promo_code,$product_id,$shop){
			
			$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/promo_wait.php?member_no=$member_no&doc_date=$doc_date&promo_code=$promo_code&product_id=$product_id&shop=$shop", "r");
            $text=@fgetss($fp, 4096);
		
		}
		
		function promo_wait_finish($member_no,$doc_date,$promo_code,$product_id,$shop,$doc_no){
			//echo "member_no=$member_no&doc_date=$doc_date&promo_code=$promo_code&product_id=$product_id&shop=$shop&doc_no=$doc_no";
			$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/promo_wait_finish.php?member_no=$member_no&doc_date=$doc_date&promo_code=$promo_code&product_id=$product_id&shop=$shop&doc_no=$doc_no", "r");
            $text=@fgetss($fp, 4096);

		
		}
		function promo_wait_cancle($doc_no){
			
			$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/promo_wait_cancle.php?doc_no=$doc_no", "r");
            $text=@fgetss($fp, 4096);

		}
		
		
		
		
		
		
		function promo_chk_other($member_no){
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			mysql_query("SET NAMES utf8");
			mysql_query("SET collection_connection='utf8_general_ci'");
				$sql="
				SELECT *
				FROM `member_his_seq`
				WHERE `member_no` = '$member_no'
				";
				//echo "$sql<br>";		
				mysql_select_db($this->db_online);
				$run_sql=mysql_query($sql,$conn_online);
				$rows_sql=mysql_num_rows($run_sql);
				$data=mysql_fetch_array($run_sql);
				$remark=$data['remark'];
			    $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_status_renew.php?member_no=$member_no", "r");
	            $chk_status_renew=@fgetss($fp, 4096);
	           
				if($chk_status_renew=="R"){

					$type_member="Re";
				}else{
		
					$type_member="New";
				}
				

				$profile="
					SELECT 
						a.customer_id,a.apply_date,a.expire_date,
						year(a.apply_date) as year_app,
						month('$this->doc_date') as month_now,
						year('$this->doc_date') as year_now  
					FROM `member_history`  as a 
					where
						a.member_no='$member_no'
				";
				mysql_select_db($this->db_online);
				$run_profile=mysql_query($profile,$conn_online);
				$rows_profile=mysql_num_rows($run_profile);
				$data_profile=mysql_fetch_array($run_profile);
				$apply_date=$data_profile['apply_date'];
				$expire_date=$data_profile['expire_date'];
				$month_hbd=$data_profile['month_hbd'];
				$month_now=$data_profile['month_now'];
				$year_now=$data_profile['year_now'];
				$year_app=$data_profile['year_app'];
				$year_next=$year_app+1;


				$map_member=$this->member_bypeple($member_no);

				$find="
					SELECT 
						a.start_date,a.end_date,a.type_member,a.register_befor,a.promo_code,a.promo_des,
						'Y' as chk_play,
						
						year(a.end_date) as promo_year,
						if(c.promo_code is null,'N',concat('ใช้แล้วในบิล ',c.doc_no,' เมื่อ',c.doc_date)) as chk_play_last,
						if('$apply_date'<=a.register_befor,'play_ok','play_stop') as chk_register,
						c.doc_no as doc_no_play
					from
						promotion_cr as a
						
						left join (
						SELECT promo_code,doc_no,doc_date FROM `promo_play_history` 
						WHERE member_id in($map_member)  and flag<>'C') as c
						on a.promo_code=c.promo_code
						where
						'$this->doc_date' between a.start_date and a.end_date and
						a.type_member='magazine'  and a.flag='' and a.promo_code<>'OM13050114'
				";
				//echo $find;

			    $result=mysql_query($find,$conn_online);
			    
			    $rows=$rows_find=mysql_num_rows($result);
			    //$xarray=array();
				$i=0;
			    while($data=mysql_fetch_assoc($result)){
			    	$chk_play=$data['chk_play'];
			    	if($chk_play=="Y"){//เล่นได้
			    		$promo_color="#66FF99";
			    	}else{//ยังเล่นไม่ได้
			    		$promo_color="#dfdfdf";
			    	}
			    	$typ_member=$data['type_member'];


			    	$point_dupble=$data['point_dupble'];
			    	if($point_dupble>1){
			    		$point_show="POINT x$point_dupble";
			    	}else{
			    		$point_show="";
			    	}
			    	$promo_year=$data['promo_year'];
			    	$des_cr=$data['des_cr'];
			    	$next_month=$data['next_month'];
			    	$time_play=$data['time_play'];
			    	$doc_no_play=$data['doc_no_play'];


			    	$register_befor=$data['register_befor'];
			    	$chk_register=$data['chk_register'];
			    	$chk_register_befor_show=$this->set_formatdate($register_befor);
			    	if($chk_register=="play_stop"){
				    		$promo_detail="ต้องสมัครก่อนวันที่ ".$chk_register_befor_show." เท่านั้น";
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			    	}

					$chk_play_last=$data['chk_play_last'];
			    	if($data['promo_code']=="OM13110114"){
			    		if($chk_play_last=="N"  && $type_member=="New" && strtotime($apply_date)>strtotime($register_befor)){
				    		$promo_detail="ต้องสมัครก่อนวันที่ ".$chk_register_befor_show." เท่านั้น";
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			    		}else{
					    		$promo_detail="";
					    		$promo_color="#66FF99";
					    		$chk_play="Y";
			    		}
			    	}
					
					
			    	
			    	if($chk_play_last=="N"){//at server online ไม่เคยใช้
			    		//chk local
			    		$chk_local="select a.* from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
			    		where 
			    		b.member_id='$member_no' and a.flag<>'C' and 
			    		a.promo_code='$data[promo_code]' and
			    		 a.doc_date between '$data[start_date]' and '$data[end_date]' and year(a.doc_date)=year('$this->doc_date') ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
			 			$count_chk_local=count($data_chk_local);
			 			if($count_chk_local>0){//เล่นแล้ว
				    		$promo_detail="ใช้ไปแล้วก่อนหน้านี้ครับ";
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}else{//เล่นได้
				    		$promo_detail="";
				    		$promo_color="#66FF99";
				    		$chk_play="Y";
			 			}
			    	}else{//เล่นแล้ว
			    		$chk_cancle="select doc_no from trn_diary1 where flag='C' and doc_no='$doc_no_play' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			 			$data_chk_cancle=$this->db->fetchAll($chk_cancle, 2); 
			 			$count__chk_cancle=count($data_chk_cancle);
			 			if($count__chk_cancle>0){ //ถ้าบิลโดน cancle
				    		$promo_detail="";
				    		$promo_color="#66FF99";
				    		$chk_play="Y";
			 			}else{//เล่นแล้ว
				    		$promo_detail=$chk_play_last;
				    		$promo_color="#FF3333";
				    		$chk_play="N";
			 			}

			    	}


					

			


			    	$xarray[$i]['start_date']=$data['start_date'];
			    	$xarray[$i]['end_date']=$data['end_date'];
			    	$xarray[$i]['promo_year']=$promo_year;
			    	$xarray[$i]['promo_code']=$data['promo_code'];
			    	$xarray[$i]['promo_name']=$data['promo_des'];
			    	$xarray[$i]['next_month']=$next_month;
			    	$xarray[$i]['dupble_point']=$point_dupble;
			    	$xarray[$i]['promo_status']=$chk_play;//รอ เพราะยังเล่นไม่ได้
			    	$xarray[$i]['promo_color']=$promo_color;
			    	$xarray[$i]['time_play']=$data['time_play'];
			    	$xarray[$i]['promo_detail']=$promo_detail;

			    	$i++;
			    }
			    
			    
				//รับ Beauty book 2014
				$find_pro="select 
				
				*,
				if('$this->doc_date' between start_date and end_date,'yes_play_date','no_play_date') as chk_play_date
				from
				promotion_cr where type_member='magazine' and promo_code='OM13050114' and flag<>'C' ";
				
				mysql_select_db($db_online);
				$run_find_pro=mysql_query($find_pro,$conn_online);
				$rows_find_pro=mysql_num_rows($run_find_pro);
				if($rows_find_pro>0){
					$data_find_pro=mysql_fetch_array($run_find_pro);
					$start_date=$data_find_pro['start_date'];$start_date_format=$this->fomatdate($start_date);
					$end_date=$data_find_pro['end_date'];$end_date_format=$this->fomatdate($end_date);
					$register_befor=$data_find_pro['register_befor'];
					$promo_code=$data_find_pro['promo_code'];
					$promo_des=$data_find_pro['promo_des'];
				
					$des_cr=$data_find_pro['des_cr'];
					$next_month=$data_find_pro['next_month'];
					$point_dupble=$data_find_pro['point_dupble'];
					$chk_play_date=$data_find_pro['chk_play_date'];//ระยะเวลาตามโปร
					$chk_play_hbd=$data_find_pro['chk_play_hbd'];//ระยะเวลาตามเดือนเกิด
					$end_hbd=$data_find_pro['end_hbd'];//วันที่สิ้นสุดสิทธิ์เดือนเกิด
					$end_hbd_format=$this->fomatdate($end_hbd);
					
					//อยู่ในช่วงเล่นโปรหรือไม่
					if($chk_play_date=="yes_play_date"){
				
							if( ($type_member=="New" && strtotime($apply_date)<=strtotime($register_befor)) ||   ($type_member=="Re" && strtotime($apply_date)<=strtotime('2014-03-31'))  ){
								$show_play="<img src='../img/tick.png' hieght='20' width='20'></img>";
								$remark="";//เหตุผล
							}else{
								$show_play="";
								$remark="ต้องสมัครภายในปี 2013 หรือ ต่ออายุภายในเดือนกุมภาพันธ์ 2014";//เหตุผล	
						
							}		
		
							
							
							//เล่นได้ปีละครัง
							$play="select * from promo_play_history where member_id in($map_member) and promo_code='$promo_code' and flag<>'C'  ";
							//echo $play;
							$run_play=mysql_query($play,$conn_online);
							$rows_play=mysql_num_rows($run_play);
							if($rows_play>=2){ //เล่นครบ 2 ครั้ง
								$show_play="";
								$remark="ลูกค้าใช้ครบ 2 ครั้งแล้ว";//เหตุผล	
						
							}else if($rows_play==1){//
								$dataplay=mysql_fetch_array($run_play);
								$mem_play=$dataplay['member_id'];
								$doc_no=$dataplay['doc_no'];
								$doc_date_play=$dataplay['doc_date'];
								$doc_date_format=$this->fomatdate($doc_date_play);
								$year_play=date("Y",strtotime($doc_date_play));
								if($year_play==$year_now){
									$show_play="";
									$remark="ปีนี้ลูกค้าเล่นไปแล้วในบิล $doc_no เมื่อ $doc_date_format ด้วยบัตร $mem_play";//เหตุผล	
						
								}
						
							}else{
									//chk local
						    		$chk_local="select a.* from trn_diary2 as a inner join trn_diary1 as b on a.doc_no=b.doc_no
						    		where 
						    		b.member_id='$member_no' and a.flag<>'C' and 
						    		a.promo_code='$promo_code' and
						    		 a.doc_date between '$start_date' and '$end_date' ";
						    		//echo $chk_local;
									$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
						 			$data_chk_local=$this->db->fetchAll($chk_local, 2); 
						 			$count_chk_local=count($data_chk_local);
						 			if($count_chk_local>0){//เล่นแล้ว
										$show_play="";
										$remark="ใช้ไปแล้วก่อนหน้านี้";//เหตุผล	
						 			}
							
							}
					}else{
						$show_play="";
						$remark="ยังไม่ถึงระยะเวลาเล่นโปรโมชั่น";//เหตุผล
				
					}
					


					if($show_play==""){//เล่นไม่ได้
						$remark_show=$remark;
						$promo_color="#FF3333";
						$chk_play="N";
						$promo_detail=$remark;
					}else{//เล่นได้
						$remark_show=$show_play;
						$promo_color="#66FF99";
						$chk_play="Y";
						$promo_detail=$remark;
					}

					array_push($xarray,array("start_date"=>$start_date_format,"end_date"=>$end_date_format,"promo_year"=>$promo_year,"promo_code"=>$promo_code,"promo_name"=>$promo_des,"next_month"=>"","dupble_point"=>"","promo_status"=>$chk_play,"promo_color"=>$promo_color,"time_play"=>"","promo_detail"=>$promo_detail,"xpoint"=>"")); 
				}

				
				
				return $xarray;
			    //return json_encode($xarray);
			
		}
		
		
		function chk_ops($id_card){
	
			$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
			mysql_query("SET NAMES utf8");
			mysql_query("SET collection_connection='utf8_general_ci'");
			$sql="
				SELECT a.id_card,a.customer_id,b.member_no,b.apply_date,b.expire_date,b.ops FROM member_register as a inner join member_history as b
					on a.customer_id=b.customer_id
					where
					a.id_card='$id_card'AND status_active = 'Y'
			";
			mysql_select_db($this->db_online);
			$run_sql=mysql_query($sql,$conn_online);
	
			
            $i=0;
            $arr_dx=array();
            while($data=mysql_fetch_assoc($run_sql)){
                $arr_dx[$i]['member_no']=$data['member_no'];
                $arr_dx[$i]['customer_id']=$data['customer_id'];
                $arr_dx[$i]['apply_date']=$data['apply_date'];
                $arr_dx[$i]['expire_date']=$data['expire_date'];
                $arr_dx[$i]['ops']=$data['ops'];
                $i++;
            }
            return $arr_dx; 

		}
		
		function chk_btn_online(){
			  $sql="select *  from com_branch_computer limit 0,1";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $data=$this->db->fetchAll($sql, 2);
			  $online_status=$data[0]['online_status'];
			  if($online_status=="1"){
			  	return "Y";
			  }else{
			  	return "N";
			  }
		}
		
		function sale_history($member_id){
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				mysql_query("SET NAMES utf8");
				mysql_query("SET collection_connection='utf8_general_ci'");
			  	$sql="
			  	SELECT a.* FROM (
				  	select *  from trn_diary1 where member_id='$member_id' 
				  	UNION ALL
				  	select *  from trn_diary1_all where member_id='$member_id' 
			  	) as a 
			  	ORDER BY a.doc_date desc,a.doc_time asc
			  	";
				mysql_select_db($this->db_online);
				$run_sql=mysql_query($sql,$conn_online);
	            $i=0;
	            $arr_dx=array();
	            while($data=mysql_fetch_assoc($run_sql)){
	                $arr_dx[$i]['branch_id']=$data['branch_id'];
	                $arr_dx[$i]['doc_no']=$data['doc_no'];
	                $arr_dx[$i]['doc_date']=$data['doc_date'];
	                $arr_dx[$i]['amount']=$data['amount'];
	                $arr_dx[$i]['net_amt']=$data['net_amt'];
	                $arr_dx[$i]['point1']=$data['point1'];
	                $arr_dx[$i]['point2']=$data['point2'];
	                $arr_dx[$i]['redeem_point']=$data['redeem_point'];
	                $arr_dx[$i]['total_point']=$data['total_point'];
	                $i++;
	            }
	            return $arr_dx; 
		}
		
		function sale_all($member_no){
            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_sale_all.php?member_no=$member_no", "r");
            $text=@fgetss($fp, 4096);
            return  $text;
		}
		
		function add_diary2($doc_no){
			$find="select * from trn_diary2  where doc_no='$doc_no'  ";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$result=$this->db->fetchAll($find, 2);
			if($result){
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				mysql_query("SET NAMES utf8");
				foreach($result as $data ){
					$add="insert into trn_diary2(`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`, `seq`, `seq_set`, `promo_code`, `promo_seq`, `promo_pos`, `promo_st`, `promo_tp`, `product_id`, `price`, `quantity`, `stock_st`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`, `coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `cost`, `cost_amt`, `promo_qty`, `weight`, `exclude_promo`, `location_id`, `product_status`, `get_point`, `discount_member`, `cal_amt`, `tax_type`, `gp`, `point1`, `point2`, `discount_percent`, `member_percent1`, `member_percent2`, `co_promo_percent`, `co_promo_code`, `coupon_code`, `coupon_percent`, `not_return`, `lot_no`, `lot_expire`, `lot_date`, `short_qty`, `short_amt`, `ret_short_qty`, `ret_short_amt`, `cn_qty`, `cn_amt`, `cn_tp`, `cn_remark`, `deleted`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`,`time_up`) values('$data[corporation_id]','$data[company_id]','$data[branch_id]','$data[doc_date]','$data[doc_time]','$data[doc_no]','$data[doc_tp]','$data[status_no]','$data[flag]','$data[seq]','$data[seq_set]','$data[promo_code]','$data[promo_seq]','$data[promo_pos]','$data[promo_st]','$data[promo_tp]','$data[product_id]','$data[price]','$data[quantity]','$data[stock_st]','$data[amount]','$data[discount]','$data[member_discount1]','$data[member_discount2]','$data[co_promo_discount]','$data[coupon_discount]','$data[special_discount]','$data[other_discount]','$data[net_amt]','$data[cost]','$data[cost_amt]','$data[promo_qty]','$data[weight]','$data[exclude_promo]','$data[location_id]','$data[product_status]','$data[get_point]','$data[discount_member]','$data[cal_amt]','$data[tax_type]','$data[gp]','$data[point1]','$data[point2]','$data[discount_percent]','$data[member_percent1]','$data[member_percent2]','$data[co_promo_percent]','$data[co_promo_code]','$data[coupon_code]','$data[coupon_percent]','$data[not_return]','$data[lot_no]','$data[lot_expire]','$data[lot_date]','$data[short_qty]','$data[short_amt]','$data[ret_short_qty]','$data[ret_short_amt]','$data[cn_qty]','$data[cn_amt]','$data[cn_tp]','$data[cn_remark]','$data[deleted]','$data[reg_date]','$data[reg_time]','$data[reg_user]','$data[upd_date]','$data[upd_time]','$data[upd_user]',now())";
					 mysql_select_db($this->db_online);
					$run_sql=mysql_query($add,$conn_online);
				}
				
				$tmp="select * from  promo_mobile_play_tmp where com_ip='$this->m_com_ip' 	";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
				$result_tmp=$this->db->fetchAll($tmp, 2);
				if($result_tmp){
				$promo_code=$result_tmp[0]['promo_code'];
				$code_check=$result_tmp[0]['code_check'];
				
				//ลงเครื่อง
				$keep="insert into promo_mobile_play(`promo_code`, `code_check`,doc_no,doc_date,doc_time,branch_id,reg_user,reg_date,reg_time,`upd_user`, `upd_date`, `upd_time`) values('$promo_code','$code_check','$data[doc_no]','$data[doc_date]','$data[doc_time]','$data[branch_id]','Process',date(now()),time(now()),'Process',date(now()),time(now()))";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
				$this->db->query($keep, 2);
				
				//ลง server
				$keep="insert into promo_mobile_play(`promo_code`, `code_check`,doc_no,doc_date,doc_time,branch_id,reg_user,reg_date,reg_time,`upd_user`, `upd_date`, `upd_time`) values('$promo_code','$code_check','$data[doc_no]','$data[doc_date]','$data[doc_time]','$data[branch_id]','Process',date(now()),time(now()),'Process',date(now()),time(now()))";
				 mysql_select_db($this->db_online);
				$run_sql=mysql_query($keep,$conn_online);
				
				$clear="delete from promo_mobile_play_tmp where com_ip='$this->m_com_ip'";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
				$this->db->query($clear, 2);
				}

			}
		}
		
		function chk_code_mobile($promo_code,$code_check){
            $fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/read_mobile/read_redeam.php?promo_code=$promo_code&code_check=$code_check", "r");
            $text=@fgetss($fp, 4096);
            return  $text;
		}
		function code_check($promo_code,$code_check){
			$chk_privilege=$this->chk_code_mobile($promo_code,$code_check);
			if($chk_privilege!="Y"){//มีสิทธิเล่นมั้ย
				return "no_privilege";
			}else {
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);
				mysql_query("SET NAMES utf8");
				$chk="select * from promo_mobile_play  where promo_code='$promo_code' and code_check='$code_check' and flag='' ";
				mysql_select_db($this->db_online);
				$run_sql=mysql_query($chk,$conn_online);
				$rows_chk=mysql_num_rows($run_sql);
				if($rows_chk==0){//chk ที่ server เคยเล่นยัง ถ้ายัง
					//chk ที่เครื่องสาขาต่อ
					$chk="select * from promo_mobile_play  where promo_code='$promo_code' and code_check='$code_check' and flag='' ";
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
					$data=$this->db->fetchAll($chk, 2);
					$num_play=count($data);
					if($num_play==0){
						$clear="delete from promo_mobile_play_tmp where com_ip='$this->m_com_ip'";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
						$this->db->query($clear, 2);
					
						//ตรวจสอบอีกว่าเครื่องที่2 มีเล่น redeem code นี้หรือไม่
						$chk_com2="select * from promo_mobile_play_tmp where promo_code='$promo_code' and code_check='$code_check' ";
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
						$run_chk_com2=$this->db->fetchAll($chk_com2, 2);
						if($run_chk_com2){//ถ้าเครื่อง2เล่นอยู่ก้ห้ามเล่น
							return "com2_play"; //เครื่องอื่นกำลังใช้ bacode นี้เล่นอยู่
						}else{
							$add="insert into promo_mobile_play_tmp(com_ip,`promo_code`, `code_check`, `upd_user`, `upd_date`, `upd_time`) values('$this->m_com_ip','$promo_code','$code_check','Process',date(now()),time(now()))";
							$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
							$this->db->query($add, 2);
							return 'play';//เล่นได้
						}
						
					}else{
						return "stop";		
					}
				}else{//เล่นแล้ว
					return "stop";				
				}
			
			}

		}
		
		
		function copy_photo($doc_no){
			$add="select * from member_idcard_tmp where ip='$this->m_com_ip'";
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$data=$this->db->fetchAll($add, 2);
			
			$id_card=$data[0][id_card];

			
			  $find_ip="select *  from com_branch_computer where computer_no='1' ";
			  $this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_head");
			  $dataip=$this->db->fetchAll($find_ip, 2);
			  $ip=$dataip[0]['com_ip'];
			  
			$filename_start="http://$ip/download_promotion/id_card/image_member/" . $id_card . "_snap1.jpg";
			//echo $filename_start;
			$filename_end="/var/www/pos/htdocs/download_promotion/id_card/image_member/" . $doc_no . "_snap1.jpg";
			copy($filename_start,$filename_end);
		}
		


		function keep_promotion_input_history($doc_no){
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);		
			$bill="select * from trn_diary1 
			where doc_no='$doc_no' and doc_tp in('SL','VT')	";
			$run_bill=mysql_query($bill,$conn_local);
			$databill=mysql_fetch_array($run_bill);
			$member_id=$databill['member_id'];
			$doc_date=$databill['doc_date'];
			$doc_time=$databill['doc_time'];
			
			$chk_btn_online=$this->chk_btn_online();
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($chk_btn_online=="Y"){//online
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);	
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_online);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];
				mysql_close($conn_online);
			}else{
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_local);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];	
			}

			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);		
						
			$list="select * from  trn_diary2
			where doc_no='$doc_no' and doc_tp in('SL','VT')
			and product_id in('25658','25659','25660') and promo_code in('OX08140214','OX09150214')
			";
			//echo $list;
			$run_list=mysql_query($list,$conn_local);
			$rows_list=mysql_num_rows($run_list);				
			for($i=1; $i<=$rows_list; $i++){
				$datalist=mysql_fetch_array($run_list);
				$promo_code=$datalist['promo_code'];
				$product_id=$datalist['product_id'];
				$seq=$datalist['seq'];
				$lot_no=$datalist['lot_no'];
				$quantity=$datalist['quantity'];
				$amount=$datalist['amount'];
				$net_amt=$datalist['net_amt'];
				
				
				$add="insert into promotion_input_history
				set
					branch_id='$datalist[branch_id]',
					customer_id='$customer_id',
					id_card='$id_card',
					member_no='$member_id',
					doc_no='$doc_no',
					doc_date='$doc_date',
					doc_time='$doc_time',
					promo_code='$promo_code',
					product_id='$product_id',
					seq='$seq',
					product_code='$lot_no',
					quantity='$quantity',
					amount='$amount',
					net='$net_amt',
					reg_user='$datalist[reg_user]',
					reg_date='$datalist[reg_date]',
					reg_time='$datalist[reg_time]',
					upd_user='$datalist[upd_user]',
					upd_date='$datalist[upd_date]',
					upd_time='$datalist[upd_time]'
				";
				//echo "$add<br>";
				mysql_query($add,$conn_local);
			}
			
			

			
		}
		
	function send_promotion_input_history(){
		$date_now=$this->doc_date;
		//$date_now='2014-06-01';
		$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
		mysql_select_db($this->db_my);	
		$find="select * from promotion_input_history where send_toserver_status='' and doc_date between doc_date - interval 5 day and '$date_now' ";
		$run=mysql_query($find,$conn_local);
		$rows_find=mysql_num_rows($run);	
				
		
		$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
		mysql_select_db($this->db_online);		
		$run_ok="";	
		if($rows_find>0){
				for($i=1; $i<=$rows_find; $i++){
					$datalist=mysql_fetch_array($run);				
					
					$add="insert into promotion_input_history
					set
						branch_id='$datalist[branch_id]',
						customer_id='$datalist[customer_id]',
						id_card='$datalist[id_card]',
						member_no='$datalist[member_no]',
						doc_no='$datalist[doc_no]',
						doc_date='$datalist[doc_date]',
						doc_time='$datalist[doc_time]',
						promo_code='$datalist[promo_code]',
						product_id='$datalist[product_id]',
						seq='$datalist[seq]',
						product_code='$datalist[product_code]',
						quantity='$datalist[quantity]',
						amount='$datalist[amount]',
						net='$datalist[net_amt]',
						reg_user='$datalist[reg_user]',
						reg_date='$datalist[reg_date]',
						reg_time='$datalist[reg_time]',
						upd_user='$datalist[upd_user]',
						upd_date='$datalist[upd_date]',
						upd_time='$datalist[upd_time]'
					";
					//echo "$add<br>";
					$run_add=mysql_query($add,$conn_online);
				
					if($run_add){
						$run_ok=$run_ok . "','" . $datalist['id'];
					}
					
				}
				
				if($rows_find>1){
					$run_ok=substr($run_ok, 3);
					$key_up="in('" . $run_ok . "')";
				}else{
					$key_up="in('" . $datalist['id'] . "')";
				}
				mysql_close($conn_online);
				
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);					
				$up_send="update promotion_input_history set
					send_toserver_status='Y',
					send_toserver_date=date(now()),
					send_toserver_time=time(now())
					where
						id $key_up
				";
				mysql_query($up_send,$conn_local);
	
		
		}

	}	
	
	
	function promotion_add_tmp($member_id){
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);	
			$clear="delete from promotion_input_history_tmp where ip='$this->m_com_ip' ";
			mysql_query($clear,$conn_local);
			mysql_close($conn_local);
				
			$chk_btn_online=$this->chk_btn_online();
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($chk_btn_online=="Y"){//online
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);	
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_online);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];
				mysql_close($conn_online);
			
				
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);						
				$add_from_local="insert into promotion_input_history_tmp(`ip`, from_data,`branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq, `product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time`)
					select '$this->m_com_ip','Local', `branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`, seq,`product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time` from promotion_input_history where customer_id='$customer_id'
				";
				
				mysql_query($add_from_local,$conn_local);
				
					$add_from_bill_tmp="insert into promotion_input_history_tmp(`ip`, from_data,`branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq,  `quantity`, `amount`, `net`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`)
					select '$this->m_com_ip','billtmp', `branch_id`, '$customer_id', '$id_card', '$member_id', `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`, seq, `quantity`, `amount`, `net_amt`,  `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time` from  trn_promotion_tmp1 where doc_no='$this->m_com_ip' and product_id in('25658','25659','25660')
				";
				//echo $add_from_bill_tmp;
				mysql_query($add_from_bill_tmp,$conn_local);

				
				mysql_close($conn_local);
								
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);					
				$find_online="select * from promotion_input_history where customer_id='$customer_id'";		
				$run_find_online=mysql_query($find_online,$conn_online);
				$rowsonline=mysql_num_rows($run_find_online);
				mysql_close($conn_online);
				
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);				
				for($i=1; $i<=$rowsonline; $i++){
					$dataonline=mysql_fetch_array($run_find_online);
					$add_from_online="
						insert into promotion_input_history_tmp
						set
							ip='$this->m_com_ip',
							from_data='Online',
							flag='$dataonline[flag]',
							branch_id='$dataonline[branch_id]',
							customer_id='$dataonline[customer_id]',
							id_card='$dataonline[id_card]',
							member_no='$dataonline[member_no]',
							doc_no='$dataonline[doc_no]',
							doc_date='$dataonline[doc_date]',
							doc_time='$dataonline[doc_time]',
							promo_code='$dataonline[promo_code]',
							product_id='$dataonline[product_id]',
							seq='$dataonline[seq]',
							product_code='$dataonline[product_code]',
							quantity='$dataonline[quantity]',
							amount='$dataonline[amount]',
							net='$dataonline[net_amt]',
							reg_user='$dataonline[reg_user]',
							reg_date='$dataonline[reg_date]',
							reg_time='$dataonline[reg_time]',
							upd_user='$dataonline[upd_user]',
							upd_date='$dataonline[upd_date]',
							upd_time='$dataonline[upd_time]'
					";
					//echo "$add_from_online<br>";
					mysql_query($add_from_online,$conn_local);					
				}					
				
				
				
			}else{
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);				
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_local);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];	
				
				$add_from_local="insert into promotion_input_history_tmp(`ip`,from_data, `branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq, `product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time`)
					select '$this->m_com_ip','Local', `branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq, `product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time` from promotion_input_history where customer_id='$customer_id'
				";
				mysql_query($add_from_local,$conn_local);
				mysql_close($conn_local);
				
			}		
	}
	
	function chk_have_promotion($member_id){
			$this->promotion_add_tmp($member_id);

			$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_seq_missing.php?member_no=$member_id", "r");
            $cr=@fgetss($fp, 4096);
            	
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);					
			$chk="select x.customer_id,if(sum(x.quantity)is null,0,sum(x.quantity)) as sumquantity from
				(
				select customer_id,quantity
				from promotion_input_history_tmp 
				where 
				 ip='$this->m_com_ip' and 
				 member_no in($cr) and
				 flag<>'C'
				 group by doc_no,seq
				) as x
			
			 ";
			//echo $chk;
			$run_chk=mysql_query($chk,$conn_local);
			$datachk=mysql_fetch_array($run_chk);
			$sumquantity=$datachk['sumquantity'];	
			mysql_close($conn_local);			

			return $sumquantity;	

			
			
		
	}
	
	function promotion_input_cancle($doc_no){
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);					
			$up_local="update promotion_input_history set flag='C' where doc_no='$doc_no' ";
			mysql_query($up_local,$conn_local);
			mysql_close($conn_local);	
			
			$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
			mysql_select_db($this->db_online);	
			$up_local="update promotion_input_history set flag='C' where doc_no='$doc_no' ";
			mysql_query($up_local,$conn_online);
			mysql_close($conn_online);		
	}
	
	function promotion_return_cancle($doc_no){
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);					
			$up_local="update promotion_return_history set flag='C' where doc_no='$doc_no' ";
			mysql_query($up_local,$conn_local);
			mysql_close($conn_local);	
			
			$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
			mysql_select_db($this->db_online);	
			$up_local="update promotion_return_history set flag='C' where doc_no='$doc_no' ";
			mysql_query($up_local,$conn_online);
			mysql_close($conn_online);		
	}
	
	function promotion_product_privacy($product_code,$member_id){
			
			$this->promotion_add_tmp($member_id);
			
			$chk_btn_online=$this->chk_btn_online();
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($chk_btn_online=="Y"){//online
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);	
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_online);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];
				mysql_close($conn_online);
			}else{
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);				
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_local);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];		
			}		

			$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_seq_missing.php?member_no=$member_id", "r");
            $cr=@fgetss($fp, 4096);
            
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);					
			$chk="select * from promotion_input_history_tmp where  ip='$this->m_com_ip' and member_no in($cr) and product_code='$product_code'";
			$run_chk=mysql_query($chk,$conn_local);
			$rows_chk=mysql_num_rows($run_chk);
			mysql_close($conn_local);	
			if($rows_chk>0){
				return "Y";
			}else{
				return "N";
			}
		
	}
	

	
		function keep_promotion_return_history($doc_no){
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);		
			$bill="
				select a.member_id,a.doc_date,a.doc_no from trn_diary1 as a inner join trn_diary2 as b
				on a.doc_no=b.doc_no
				where a.doc_no='$doc_no'  and b.promo_code='OX17160514'
				group by a.member_id,a.doc_date,a.doc_no
			";
			$run_bill=mysql_query($bill,$conn_local);
			$rows_bill=mysql_num_rows($run_bill);
			//echo "ROWS=$rows_bill";
			if($rows_bill>0){
				$databill=mysql_fetch_array($run_bill);
				$member_id=$databill['member_id'];
				$doc_date=$databill['doc_date'];
				$doc_time=$databill['doc_time'];
				
				$chk_btn_online=$this->chk_btn_online();
				//echo "SALE ONLINE:$chk_btn_online<br>";
				if($chk_btn_online=="Y"){//online
					$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
					mysql_select_db($this->db_online);	
					$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
					on a.customer_id=b.customer_id
					where
					b.member_no='$member_id' ";	
					$run_profile=mysql_query($profile,$conn_online);
					$dataprofile=mysql_fetch_array($run_profile);
					$customer_id=$dataprofile['customer_id'];
					$id_card=$dataprofile['id_card'];
					mysql_close($conn_online);
				}else{
					$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
					on a.customer_id=b.customer_id
					where
					b.member_no='$member_id' ";	
					$run_profile=mysql_query($profile,$conn_local);
					$dataprofile=mysql_fetch_array($run_profile);
					$customer_id=$dataprofile['customer_id'];
					$id_card=$dataprofile['id_card'];	
				}
	
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);		
							
				$list="select * from  trn_diary2
				where doc_no='$doc_no' and doc_tp in('SL','VT')
				and promo_code='OX17160514'
				";
				//echo $list;
				$run_list=mysql_query($list,$conn_local);
				$rows_list=mysql_num_rows($run_list);				
				for($i=1; $i<=$rows_list; $i++){
					$datalist=mysql_fetch_array($run_list);
					$promo_code=$datalist['promo_code'];
					$product_id=$datalist['product_id'];
					$seq=$datalist['seq'];
					$lot_no=$datalist['lot_no'];
					$quantity=$datalist['quantity'];
					$amount=$datalist['amount'];
					$net_amt=$datalist['net_amt'];
					
					
					$add="insert into promotion_return_history
					set
						branch_id='$datalist[branch_id]',
						customer_id='$customer_id',
						id_card='$id_card',
						member_no='$member_id',
						doc_no='$doc_no',
						doc_date='$doc_date',
						doc_time='$doc_time',
						promo_code='$promo_code',
						product_id='$product_id',
						seq='$seq',
						product_code='$lot_no',
						quantity='$quantity',
						amount='$amount',
						net='$net_amt',
						reg_user='$datalist[reg_user]',
						reg_date='$datalist[reg_date]',
						reg_time='$datalist[reg_time]',
						upd_user='$datalist[upd_user]',
						upd_date='$datalist[upd_date]',
						upd_time='$datalist[upd_time]'
					";
					echo "$add<br>";
					mysql_query($add,$conn_local);
				}
			
			}

			
		}
		function send_promotion_return_history(){
		$date_now=$this->doc_date;
		//$date_now='2014-06-01';
		$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
		mysql_select_db($this->db_my);	
		$find="select * from promotion_return_history where send_toserver_status='' and doc_date between doc_date - interval 5 day and '$date_now' ";
		$run=mysql_query($find,$conn_local);
		$rows_find=mysql_num_rows($run);	
				
		
		$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
		mysql_select_db($this->db_online);		
		$run_ok="";	
		if($rows_find>0){
				for($i=1; $i<=$rows_find; $i++){
					$datalist=mysql_fetch_array($run);				
					
					$add="insert into promotion_return_history
					set
						branch_id='$datalist[branch_id]',
						customer_id='$datalist[customer_id]',
						id_card='$datalist[id_card]',
						member_no='$datalist[member_no]',
						doc_no='$datalist[doc_no]',
						doc_date='$datalist[doc_date]',
						doc_time='$datalist[doc_time]',
						promo_code='$datalist[promo_code]',
						product_id='$datalist[product_id]',
						seq='$datalist[seq]',
						product_code='$datalist[product_code]',
						quantity='$datalist[quantity]',
						amount='$datalist[amount]',
						net='$datalist[net_amt]',
						reg_user='$datalist[reg_user]',
						reg_date='$datalist[reg_date]',
						reg_time='$datalist[reg_time]',
						upd_user='$datalist[upd_user]',
						upd_date='$datalist[upd_date]',
						upd_time='$datalist[upd_time]'
					";
					//echo "$add<br>";
					$run_add=mysql_query($add,$conn_online);
				
					if($run_add){
						$run_ok=$run_ok . "','" . $datalist['id'];
					}
					
				}
				
				if($rows_find>1){
					$run_ok=substr($run_ok, 3);
					$key_up="in('" . $run_ok . "')";
				}else{
					$key_up="in('" . $datalist['id'] . "')";
				}
				mysql_close($conn_online);
				
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);					
				$up_send="update promotion_return_history set
					send_toserver_status='Y',
					send_toserver_date=date(now()),
					send_toserver_time=time(now())
					where
						id $key_up
				";
				mysql_query($up_send,$conn_local);
	
		
		}

	}	
	
	function chk_play_promotion($member_id){
			$this->promotion_addplay_tmp($member_id);

			$fp = @fopen("http://10.100.53.2/ims/joke/app_service_op/process/member_seq_missing.php?member_no=$member_id", "r");
            $cr=@fgetss($fp, 4096);
            	
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);					
			$chk="	select *
				from promotion_return_history_tmp 
				where 
				  doc_date='$this->doc_date' and
				 ip='$this->m_com_ip' and 
				 member_no in($cr) and
				 flag<>'C' and promo_code='OX17160514'
				 group by doc_no
			 ";
			//echo $chk;
			$run_chk=mysql_query($chk,$conn_local);
			$rows_chk=mysql_num_rows($run_chk);

			mysql_close($conn_local);			

			return $rows_chk;	
			
			
		
	}		

		function promotion_addplay_tmp($member_id){
			
			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);	
			$clear="delete from promotion_return_history_tmp where ip='$this->m_com_ip' ";
			mysql_query($clear,$conn_local);
			mysql_close($conn_local);
				
			$chk_btn_online=$this->chk_btn_online();
			//echo "SALE ONLINE:$chk_btn_online<br>";
			if($chk_btn_online=="Y"){//online
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);	
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_online);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];
				mysql_close($conn_online);
			
				
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);						
				$add_from_local="insert into promotion_return_history_tmp(`ip`, from_data,`branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq, `product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time`)
					select '$this->m_com_ip','Local', `branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`, seq,`product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time` from promotion_return_history where customer_id='$customer_id'
				";
				//echo $add_from_local;
				mysql_query($add_from_local,$conn_local);


				
				mysql_close($conn_local);
								
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);					
				$find_online="select * from promotion_return_history where customer_id='$customer_id'";		
				$run_find_online=mysql_query($find_online,$conn_online);
				$rowsonline=mysql_num_rows($run_find_online);
				mysql_close($conn_online);
				
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);				
				for($i=1; $i<=$rowsonline; $i++){
					$dataonline=mysql_fetch_array($run_find_online);
					$add_from_online="
						insert into promotion_return_history_tmp
						set
							ip='$this->m_com_ip',
							from_data='Online',
							flag='$dataonline[flag]',
							branch_id='$dataonline[branch_id]',
							customer_id='$dataonline[customer_id]',
							id_card='$dataonline[id_card]',
							member_no='$dataonline[member_no]',
							doc_no='$dataonline[doc_no]',
							doc_date='$dataonline[doc_date]',
							doc_time='$dataonline[doc_time]',
							promo_code='$dataonline[promo_code]',
							product_id='$dataonline[product_id]',
							seq='$dataonline[seq]',
							product_code='$dataonline[product_code]',
							quantity='$dataonline[quantity]',
							amount='$dataonline[amount]',
							net='$dataonline[net_amt]',
							reg_user='$dataonline[reg_user]',
							reg_date='$dataonline[reg_date]',
							reg_time='$dataonline[reg_time]',
							upd_user='$dataonline[upd_user]',
							upd_date='$dataonline[upd_date]',
							upd_time='$dataonline[upd_time]'
					";
					//echo "$add_from_online<br>";
					mysql_query($add_from_online,$conn_local);					
				}					
				
				
				
			}else{
				$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
				mysql_select_db($this->db_my);				
				$profile="select a.customer_id,a.id_card,b.member_no from member_register as a inner join member_history as b
				on a.customer_id=b.customer_id
				where
				b.member_no='$member_id' ";	
				$run_profile=mysql_query($profile,$conn_local);
				$dataprofile=mysql_fetch_array($run_profile);
				$customer_id=$dataprofile['customer_id'];
				$id_card=$dataprofile['id_card'];	
				
				$add_from_local="insert into promotion_return_history_tmp(`ip`,from_data, `branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq, `product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time`)
					select '$this->m_com_ip','Local', `branch_id`, `customer_id`, `id_card`, `member_no`, `doc_no`, `doc_date`, `doc_time`, `flag`, `promo_code`, `product_id`,seq, `product_code`, `quantity`, `amount`, `net`, `var1`, `var2`, `reg_user`, `reg_date`, `reg_time`, `upd_user`, `upd_date`, `upd_time`, `send_toserver_status`, `send_toserver_date`, `send_toserver_time` from promotion_return_history where customer_id='$customer_id'
				";
				mysql_query($add_from_local,$conn_local);
				mysql_close($conn_local);
				
			}		
	}
	


	function chk_coupon_code($coupon){


			$conn_local=mysql_connect($this->sv_my,$this->user_my,$this->pass_my);	
			mysql_select_db($this->db_my);					
			$chk="select *
				from trn_diary1 
				where 
				doc_date>='2014-08-16' and
				 flag<>'C' and coupon_code='$coupon'
			 ";
			//echo $chk;
			$run_chk=mysql_query($chk,$conn_local);
			$rows_chk=mysql_num_rows($run_chk);
			mysql_close($conn_local);
			if($rows_chk>0){
				$data=mysql_fetch_array($run_chk);
				$ans="Y@@$data[doc_no]@@$data[doc_date]@@$data[member_id]";
			}else{
				$conn_online=mysql_connect($this->sv_online,$this->user_online,$this->pass_online);	
				mysql_select_db($this->db_online);					
				$chk="select *
					from trn_diary1 
					where 
					doc_date>='2014-08-16' and
					 flag<>'C'  and coupon_code='$coupon'
				 ";
				$run_chk=mysql_query($chk,$conn_online);
				$rows_chk=mysql_num_rows($run_chk);
				mysql_close($conn_online);
				if($rows_chk>0){
					$data=mysql_fetch_array($run_chk);
					$ans="Y@@$data[doc_no]@@$data[doc_date]@@$data[member_id]";
				}else{
					$ans="N@@Null@@Null@@Null";
				}
			}
						

			return $ans;	
			
			
		
	}	
	
	
	
	
	
}//end class

?>
