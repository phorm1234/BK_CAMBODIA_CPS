<?php 
	error_reporting(E_ALL);
    ini_set('display_errors', 1);
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
			 * @desc สั่งตัด EDC ผ่าน serial port COM2
			 * @param Float $net_amt จำนวนเงินที่ต้องการรูดบัตรเครดิต
			 * @return null
			 */
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
			$fd = dio_open('/dev/ttyS1', O_RDWR | O_NOCTTY);
			if(!$fd){
				echo "Cann't Open port COM2";
				exit();
			}
			
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
			$dataout=chr(0)."5"."60"."0000"."0000"."1"."0"."20"."00"."0".chr(28)."40".chr(0).chr(18)."$bath"."$satang".chr(28).chr(3);
			$dataout=chr(2).$dataout.xorbit($dataout);
			$aa=dio_write($fd,$dataout);
			dio_close($fd);
			echo "complete.";
		}//func
		$net_amt='1.00';
		callEDC($net_amt);
?>