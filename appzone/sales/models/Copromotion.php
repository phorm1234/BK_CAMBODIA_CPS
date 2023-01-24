<?php
	class Model_Copromotion extends Model_PosGlobal{
		function __construct(){
			parent::__construct();	
			$this->curr_date=date("Y-m-d");
			if($this->doc_date){
				$arr_date=explode("-",$this->doc_date);
				$this->year=$arr_date[0];
				$this->month=intVal($arr_date[1]);
			}
		}//func
	
	function chkUSSD($act,$coupon,$promo,$tel){
	
		$postdata = http_build_query( array('act'=>$act,'coupon_code'=>$coupon,'promo_code'=>$promo,'tel'=>$tel));
		
		$opts = array('http' => array( 'method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n" ."Referer: urltopost\r\n", 'content' => $postdata ) );
		$context = stream_context_create($opts); 
		$url = 'http://mshop.ssup.co.th/shop_op/ussd_promo_test.php';
		$result = file_get_contents($url, false, $context);
		//$data = json_decode($result,true);
		echo $result;
	}		
		
	}//class
?>