<?php
class Model_Setpromotion extends Zend_Db_Table
{
 	   protected $db="";
 	   function __construct() {
 	   	//$this->db=Zend_Registry::get('dbOfline');
 	   	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");
 	   	//$this->db=SSUP_Controller_Plugin_Db::conn2db();
 	   }

	   function onconnect($process_id,$table_id){
		$objCall=new SSUP_Controller_Plugin_Db();
		$this->db=$objCall->processdb($process_id,$table_id);
	   }   
 	   
 	   
 	   
	   function chkHeadPro($promo_code){
		   $sql="select * from promo_head where promo_code='$promo_code' ";
		   $result = $this->db->fetchAll($sql, 2);
		   if($result){
		   		return 1;
		   }else{
		   		return 0;
		   }
	   }
	   
	   
	   function insertProHead($promo_code,$promo_des,$promo_pos,$level,$process,$start_date,$end_date,$compare,$check_repeat){
		   $sql="insert into promo_head
		   set
			corporation_id='OP',
			company_id='OP',
			channel='OP',
		   	promo_code='$promo_code',
		   	promo_des='$promo_des',
		   	promo_pos='$promo_pos',
		   	level='$level',
		   	process='$process',
		   	start_date='$start_date',
		   	end_date='$end_date',
		   	compare='$compare',
		   	check_repeat='$check_repeat'
		   	
		   ";
		   $result = $this->db->query($sql, 2);
		   if($result){
		   		return 1;
		   }else{
		   		return 0;
		   }
	   }
	   
 	   
	   function insertProdetail($promo_code,$promo_pos,$seq_pro,$product_id,$quantity,$type_discount,$discount,$weight){
			
	   	   $find="select * from promo_head where promo_code='$promo_code' ";
	   	   $dataProHead = $this->db->fetchAll($find, 2);
	   	   $start_date=$dataProHead[0]['start_date'];
	   	   $end_date=$dataProHead[0]['end_date'];
	   	   
	   	   
	   	   if($product_id=="ALL"){
	   	   		$product_find="select * from com_product_master order by product_id";
	   	   		$dataProduct = $this->db->fetchAll($product_find, 2);
	   	   		foreach ($dataProduct as $data){
	   	   			   $product_id=$data['product_id'];
	   	   			   
				   	   $sql="
				   	   insert into promo_detail
					   set
					    corporation_id='OP',
					    company_id='OP',
					   	promo_code='$promo_code',
					   	promo_pos='$promo_pos',
					   	start_date='$start_date',
					   	end_date='$end_date',
					   	seq_pro='$seq_pro',
					   	product_id='$product_id',
					   	quantity='$quantity',
					   	type_discount='$type_discount',
					   	discount='$discount',
					   	weight='$weight'
					   ";
					   $result = $this->db->query($sql, 2);
	   	   		}
	   	   } else {
		   	   $sql="
		   	   insert into promo_detail
			   set
			    corporation_id='OP',
			    company_id='OP',
			   	promo_code='$promo_code',
			   	promo_pos='$promo_pos',
			   	start_date='$start_date',
			   	end_date='$end_date',
			   	seq_pro='$seq_pro',
			   	product_id='$product_id',
			   	quantity='$quantity',
			   	type_discount='$type_discount',
			   	discount='$discount',
			   	weight='$weight'
			   ";
			   $result = $this->db->query($sql, 2);
	   	   }
		   if($result){
		   		return 1;
		   }else{
		   		return 0;
		   }
	   }
	   
	   function showlistpro($promo_code){
			
	   	   $sql="select * from promo_detail where promo_code='$promo_code' 
	   	   order by promo_code,seq_pro,product_id";
	   	   return  $this->db->fetchAll($sql, 2);
	   	   
	   }
}