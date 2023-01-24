<?php
	class Model_Utils
	{		
		public function ArrayToJson($jname,$jdata,$jstatus){
			/***
			 * @desc modify : 11062013
			 * @desc modify : for 1 set of data
			 * @param String $jname : name of json set
			 * @param Array $jdata : array one dimention of data
			 * @param String $jstatus :status yes is exist or no is not
			 * @return string json format
			 */		
			if(is_array($jdata))
				$jdata['exist']=$jstatus;
			$jsonResponse=array("$jname"=>array());
			array_push($jsonResponse["$jname"],$jdata);	
			return json_encode($jsonResponse);
		}//func
		
		public function cleanArrayToJson($jname,$jdata,$jstatus){
			/***
			 * @desc modify : 11062013
			 * @desc modify : for multi set of data
			 * @param String $jname : name of json set
			 * @param Array $jdata : array one dimention of data
			 * @param String $jstatus :status yes is exist or no is not
			 * @return string json format
			 */			
			$jsonResponse=array("$jname"=>array());
			foreach($jdata as $k=>$v){
				$jsonRow=array(
					"$k"=>$v,
				);			
				array_push($jsonResponse["$jname"],$jsonRow);	
			}
			$jsonRow=array(
				"exist"=>$jstatus,
			);			
			array_push($jsonResponse["$jname"],$jsonRow);	
			return json_encode($jsonResponse);
		}//func
		
		public function ArrayToJson88($jname,$jdata,$jstatus){
			/**
			 * @desc
			* @param String $jname : name of json set
			 * @param Array $jdata : array one dimention of data
			 * @param String $jstatus :status yes is exist or no is not
			 * @return string json format
			 */
			$jreturn='{';
			$jreturn.='"'.$jname.'":[';
			$jreturn.='{';
			foreach($jdata as $k=>$v){
				$v=stripslashes($v);
				$v=str_replace('\r\n','\n',$v);
				$v=str_replace('\r','\n',$v);
				$v=str_replace('\n','\\n',$v);
				$jreturn.='"'.$k.'":"'.strip_tags($v).'",';
			}
			$jreturn.='"exist":"'.$jstatus.'",';
			$len_json=strlen($jreturn);
			$jreturn=substr($jreturn,0,$len_json-1);
			$jreturn.='}';
			$jreturn.="]}";
			return $jreturn;
		}//func
		
		public function ArrayToJson_bk($jname,$jdata,$jstatus){
			/**
			 * @param String $jname : name of json set
			 * @param Array $jdata : array one dimention of data
			 * @param String $jstatus :status yes is exist or no is not
			 * @return string json format
			 */
			$jreturn="{";
			$jreturn.="'$jname':[";
			$jreturn.="{";
			foreach($jdata as $k=>$v){
				$v=stripslashes($v);
				$v=str_replace("\r\n","\n",$v);
				$v=str_replace("\r","\n",$v);
				$v=str_replace("\n","\\n",$v);
				$jreturn.="'".$k."':'".strip_tags($v)."',";
			}
			$jreturn.="exist:".$jstatus.'","';
			$len_json=strlen($jreturn);
			$jreturn=substr($jreturn,0,$len_json-1);
			$jreturn.="}";
			$jreturn.=']}';
			return $jreturn;
		}//func
		
		public function getFormatJson($pt,$table,$where,$db,$local){	
			/*
			*	@modify 20/08/2010
			*	@param $pt,$table,$where
			*	@return strings format of json
			*	@desc
			*/
			if($pt=='' || $table=='') return false;
			$sql_json="SELECT * FROM $table $where";			
			$res_json=mysql_db_query($db,$sql_json,$local);
			if(mysql_num_rows($res_json)>0){				
				$json = '{';
				$json.="'$pt': [";
				$arr_json=mysql_fetch_assoc($res_json);
				$json.="{";	
				foreach($arr_json as $key_field=>$val){
						$val=stripslashes($val);				
						$val=str_replace("\r\n","\n",$val);
						$val=str_replace("\r","\n",$val);	
						$val=str_replace("\n","\\n",$val);				
						$json.="'".$key_field."':'".strip_tags($val)."',";
				}//
				$json.="'"."exist"."':'yes',";			
				$len_json=strlen($json);
				$json=substr($json,0,$len_json-1);				
				$json.="}";						
				$json.=']}';					
			}else{
				$json = '{';
				$json.="'seq': [";
				$json.="{";	
				$json.="'"."exist"."':'no'";
				$json.="}";						
				$json.=']}';				
			}
			$json="aaaa";
			return $json;
		}//func
		
		function findFirstDayOfWeek($month, $year, $day, $offset){ 
			/*
			 *@ param :$month
			 *@ param :$year
			 *@ param :$day
			 *@ param :$offset
			 *@ desc :example echo findFirstDayofWeek(6,2009, "Wednesday", 2); 
			 *@ desc :Where 6 is "June", 2009 is the year, Wednesday is the weekday we want and 2 is the 2nd occurence. 
			 *@ return
			 */
			// supply the month, year, day and offset
			$FirstDay = mktime(0, 0, 0, $month, 1, $year); // Get the first day of the month in question
			$DayName = date("l", $FirstDay); //set the name of the first day for the while loop
			$CurrentStamp = $FirstDay; // set a disposable variable for use in the while loop
			$Results = 0; //set the number of results (for use with the offset argument)
			While($Results != $offset){ //while the number of results does not equal the offset we are looking for
				$CurrentStamp = $CurrentStamp + 86400; // add a day
				if(date("l", $CurrentStamp) == $day){
					// if the name of the weekday we are currently looping at is the same name as the argument supplied, set the date variable to that weekday and increment the results variable by 1
					$Date = date("Y-m-d", $CurrentStamp);
					$Results++;
				}
			}
			if(date("n", $CurrentStamp) != $month){ 
				// this line checks whether or not the date that the while loop has found is in the same month we are asking form otherwise, there must be no "3rd friday in august"
				return "No weekday at this offset";
			} else {
				return $Date; //send back the date
			}
						
		}//func
		
		//echo findFirstDayofWeek(12,2010, "Friday", 1); // returns a formatted date for the first instance of a certain day in the argumental month
		//echo findFirstDayofWeek(12,2010, "Wednesday",2); 
		
		function js_thai_encode($data)
		{	
			/*
			 * @credit thank you for http://blog.chonla.com
			 * @name js_thai_encode
			 * @desc
			 * ปัญหาภาษาไทยกับการใช้งานฟังก์ชั่น json_encode() 
			 * ปัญหานี้เกิดขึ้นเฉพาะกับภาษาไทยที่ใช้ชุดตัวอักษร (Character Set) ในกลุ่ม ANSI (เช่น Windows-874, TIS-620) 
			 * วิธีการแก้ปัญหา ง่ายนิดเดียวคือ การแปลงให้มันเป็น UTF-8 ก่อนด้วยฟังก์ชั่น iconv() แล้วค่อยเอาไปใส่ json_encode() 			
			 */
			
			// fix all thai elements
			if (is_array($data))
			{
				foreach($data as $a => $b)
				{
					if (is_array($data[$a]))
					{
						$data[$a] = js_thai_encode($data[$a]);
					}
					else
					{
						$data[$a] = iconv("tis-620","utf-8",$b);
					}
				}
			}
			else{
				$data =iconv("tis-620","utf-8",$data);
			}
			return $data;
		}//func
		
		function swapColoRows($arr_data){
			/*
			 *@desc example of highlight row background by different product_no column 
			 *@return
			 *@modify 03/10/2011
			 */
			$this->arr_cataloglist=$arr_data;
			if(!empty($this->arr_cataloglist)){
				$i=0;
				$bg_color_temp="#ffffff";
				$qty=1;
				foreach($this->arr_cataloglist as $data){
					$data_qty=intval($data['product_no']);
					if($qty!=$data_qty){
						($bg_color_temp=="#ffffff")?$bg_color_temp="#E1F1FF":$bg_color_temp="#ffffff";
						$qty=$data_qty;
					}
					$bg_color=$bg_color_temp;
					?>
					<tr bgcolor="<?php echo $bg_color;?>">
						<td align="center"><?php echo $data['application_id'];?></td>
						<td align="center"><?php echo $data['product_no'];?></td>
						<td align="center"><?php echo $data['product_id'];?></td>
						<td align="left">&nbsp;<?php echo $data['name_product'];?></td>
						<td align="center"><?php echo intval($data['quantity']);?></td>
						<td align="center"><?php echo $data['price'];?></td>
						<td align="center">
							 <button idp="<?php echo $data['product_id'];?>" class="btn_sel2"><span class="ui-button-text">เลือก</span></button>
						</td>
					</tr>
					<?php 
					$i++;
				}
				
			}
			
		}//func
		
		function lastDayNextMonth(){
			/*
			 * @desc
			 * @param
			 * @return
			 * @modify 03/10/2011
			 */
			$today_next_month = strtotime('this day next month');
			$last_day_next_month = strtotime('last day next month');
			if (date('d', $today_next_month) < date('d', $last_day_next_month)){
			    $date = date('m-d-Y',$last_day_next_month); 
			}else{
			    $date = date('m-d-Y',$today_next_month);
			}
			
			return "And the winner is : $date<br/>";
		}//func
		
		function getNextMonthN($date,$n=1){
			echo $date;
			echo "<br>";
			echo $n;
		  /*
		   * @desc
		   * @param $date
		   * @param $n
		   */
	      $newDate = strtotime('+$n months',$date);
	      echo "<hr>$newDate";
	      exit();
	      if (date('j', $date) !== (date('j', $newDate))) {
	    	$newDate = mktime(0, 0, 0, date('n', $newDate), 0, date('Y', $newDate));
	      }
	      return $newDate;	
	    }//func		
		
		
	}//class

?>