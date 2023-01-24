<?php
	class Model_Otherpromotion extends Model_PosGlobal{
		function __construct(){
			parent::__construct();	
			$this->curr_date=date("Y-m-d");
			if($this->doc_date){
				$arr_date=explode("-",$this->doc_date);
				$this->year=$arr_date[0];
				$this->month=intVal($arr_date[1]);
			}
		}//func
	
		function chkUSSD($act,$coupon,$promo){
		
			$postdata = http_build_query( array('act'=>$act,'coupon_code'=>$coupon,'promo_code'=>$promo));
			
			$opts = array('http' => array( 'method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n" ."Referer: urltopost\r\n", 'content' => $postdata ) );
			$context = stream_context_create($opts); 
			$url = 'http://mshop.ssup.co.th/shop_op/ussd_promo.php';
			$result = file_get_contents($url, false, $context);
			//$data = json_decode($result,true);
			echo $result;
		}	//func	
	
		function chkProHead($promo){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","promo_head");	
			$sql_t2="SELECT * FROM promo_head WHERE `promo_code`='".$promo."' ";
			$n_chk2=$this->db->fetchOne($sql_t2);
		}//func
		
		function chkPriv($act,$coupon_code,$promo_code,$mobile,$otp,$cid){
			$act=$otp?"otp":$act;
			$postdata = http_build_query( array('act'=>$act,'coupon_code'=>$coupon_code,'promo_code'=>$promo_code, 'mobile'=>$mobile,'otp'=>$otp,'idcard'=>$cid));
			
			$opts = array('http' => array( 'method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n" ."Referer: urltopost\r\n", 'content' => $postdata ) );
			$context = stream_context_create($opts); 
			$url = 'http://mshop.ssup.co.th/shop_ll/other_promo.php';
			$result = file_get_contents($url, false, $context);
			//echo "$act,$coupon_code,$promo_code,$mobile,$otp";
			echo $result;
		}//func

		function setSLToDN($pro,$com,$amt=0){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_sl");	
			$sql="SELECT sum(net_amt) AS sum_net_amt FROM trn_tdiary2_sl WHERE computer_no='".$com."' AND promo_code='".$pro."'";

			$n_chk=$this->db->fetchOne($sql);
			if($amt==0 || $n_chk < $amt){
				$sql="UPDATE trn_tdiary2_sl SET amount=0,price=0,net_amt=0 WHERE computer_no='".$com."' AND promo_code='".$pro."'";
				//echo $sql;
				$this->db->query($sql);
				return json_encode(array('status'=>"Y",'msg'=>"OK"));
			}else{
				return  json_encode(array('status'=>"N",'msg'=>"Over Price"));
			}
		}//func
	}//class
?>