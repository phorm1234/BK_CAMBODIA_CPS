<?php
	class Model_DayTransaction extends Model_PosGlobal{
		private $doc_dt;
		private $edit_dt;
		private $expense_dt;
		private $account_code;
		private $amount;
		function __construct(){
			parent::__construct();	
		}//func
		
		
		public function getSalemanOpenCashDrawer($employee_id){
			/**
			 * @desc : not check doc_date used Date Sysatem
			 * @desc : modify 05092013
			 * @param String $employee_id
			 * @return info of employee
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_employee");
			$str_status='';
			$sql_emp="SELECT employee_id,name,surname,remark 
								FROM conf_employee
								WHERE 
									employee_id='$employee_id' AND
									group_id IN('CpShopMng','CpShopEmp','AUDIT')";
			$row_emp=$this->db->fetchAll($sql_emp);
			if(count($row_emp)>0){				
				//************************** start					
					unset($this->db);
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("chtime","check_in_out");
					$sql_checkin="SELECT 
											`check_id` , `check_in` , `check_in_img_path` , `check_in_seq` , `check_in_sent` , `check_in_reason`
										FROM 
											`check_in_out`
										WHERE 
											shop_id='$this->m_branch_id' AND	
											cid = '$employee_id' AND
											check_date = CURDATE()
										ORDER BY check_id DESC LIMIT 0,1";	
					$rows_checkin=$this->db->fetchAll($sql_checkin);				
					if(!empty($rows_checkin)){
						if($rows_checkin[0]['check_in_reason']!='1' && $rows_checkin[0]['check_in_reason']!='4' ){
							//not in system
							$str_status='P';
						}else{
							//ok in system
							$str_status='Y';
						}
					}else{
						//not login to system
						$str_status='N';						
					}
				//*************************** end				
				$row_emp[0]['check_status']=$str_status;
				unset($this->db);				
			}else{
				$row_emp[0]['check_status']=$str_status;
			}
			return $row_emp;
		}//func
		
		
		function getRemarkOpenCashDrawer(){
			/**
			 * @desc
			 */
			$sql_remark="SELECT* FROM com_cash_drawer_remark ORDER BY remark_id ";
			$rows_remark=$this->db->fetchAll($sql_remark);
			if(!empty($rows_remark)){
				return $rows_remark;
			}else{
				return array();
			}
		}//func
		
		function getCashDrawer(){			
			return $this->m_cashdrawer;
		}//function
		
		function openCashDrawer($employee_id,$remark_id){
			/**
			 * @desc
			 * @modyfied 22042013
			 * @return 
			 */
			$status_res="N";			
			$sql_openchdw="INSERT INTO `pos_ssup`.`com_cash_drawer` (`id`, `corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, 
																									`open_cash_drawer1`, `open_cash_drawer2`, `computer_no`, `user_id`, `remark_id`, 
																									`reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) 
										VALUES (NULL, '$this->m_corporation_id', '$this->m_company_id', '$this->m_branch_id', '$this->doc_date',CURTIME(),
																								 '1', '0', '$this->m_computer_no', '$employee_id', '$remark_id',CURDATE(),CURTIME(),'$this->user_id',
																								  '1900-01-01', '00:00:00', '')";
			
			$res_openchdw=$this->db->query($sql_openchdw);
			if($res_openchdw){
				//parent::openCashDrawer();
				$status_res="Y";
			}
			return $status_res;
		}//func
		
		function getDocDt($r_date=''){
			/**
			 * @desc get doc_dt from doc_date 
			 * fixed $s_date='21',$e_date='20',$r_date='01'
			 * @return
			 */
			if($r_date==''){
				$r_date=$this->doc_date;
			}
			$arr_expense=array();
			$arr_docdate=explode("-",$r_date);
			$expense_month=intval($arr_docdate[1]);
			
			//*WR30072012
			if(intval($arr_docdate[2])>20){
				$expense_month=$expense_month+1;
			}
			
			if($expense_month==1){
				$s_year=date("Y",strtotime($r_date."-1 year"));
				$s_month=date("m",strtotime($r_date."-1 months"));
			}else{
				$s_year=$arr_docdate[0];				
				$s_month=$expense_month-1;//*WR30072012
				$s_month=substr("00".$s_month,-2);
			}
			$s_date='21';
			$start_date=$s_year."-".$s_month."-".$s_date;			
			$e_year=$arr_docdate[0];
			$e_month=date("m",strtotime($start_date."+1 months"));			
			
			//*WR26122012
			$expense_month=intval($expense_month);
			if($expense_month>12){				
				$e_year=date("Y",strtotime($start_date."+1 year"));
				$expense_month='1';
			}
			
			$e_date='20';
			$r_date='01';			
			$arr_expense['expense_month']=$expense_month;//*WR30072012			
			$arr_expense['start_date']=$s_date."/".$s_month."/".$s_year;
			$arr_expense['end_date']=$e_date."/".$e_month."/".$e_year;
			$arr_expense['doc_dt']=$e_year."-".$e_month."-".$r_date;
			return $arr_expense;		
		}//func
		
		function getExpense($doc_dt){
			/**
			 * @desc
			 * @var unknown_type
			 * @modify 16102015
			 */
			$this->doc_dt=$doc_dt;
			$sql_data="SELECT * FROM `com_expense_data`
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id'";
			$row_data=$this->db->fetchAll($sql_data);
			echo "<table width='100%' border='0' cellpadding='2' cellspacing='1' bgcolor='#cccccc'>
						<tr bgcolor='#B9E7CF'>
							<td align='center' width='15%'><span style='font-size:23px;color:#000;'>รหัส</span></td>
							<td align='center'><span style='font-size:23px;color:#000;'>รายละเอียด</span></td>
							<td align='center' width='15%'><span style='font-size:23px;color:#000;'>จำนวนเงินปัจจุบัน</span></td>
							<td align='center' width='15%'><span style='font-size:23px;color:#000;'>จำนวนเงินใหม่</span></td>
						</tr>";
			$i=0;
			$sum_old_amount=0.00;
			foreach($row_data as $data){
				($i%2==0)?$bg_color="#ffffff":$bg_color="#f2f2f2";
				$i++;
				$sql_list="SELECT * FROM `com_expense_list` 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND 
									branch_id='$this->m_branch_id' AND
									doc_dt='$this->doc_dt' AND 
									account_code='$data[account_code]'";
				$row_list=$this->db->fetchAll($sql_list);
				if(count($row_list)>0){
					$old_amount=$row_list[0]['amount'];
				}else{
					$old_amount='0';
				}
				$sum_old_amount+=$old_amount;
				$account_code=$data['account_code'];
				echo "	<tr bgcolor='$bg_color'>
							<td align='center'>$account_code</td>
							<td align='center'>$data[description]</td>
							<td align='center' ><input type='text' size='10' class='inputnonekey input-text-pos-disabled ui-corner-all' value='".number_format($old_amount,2,'.','')."' id='old_amount' name='old_amount' readonly style='text-align:right'></td>
							<td align='center'><input type='text' size='10' class='inputkey input-text-pos ui-corner-all' value='' id='$account_code' name='new_amount' style='text-align:right'></td>
						</tr>";
			}
			echo "
					<tr height='40' bgcolor='#ffffff'>
						<td align='center' colspan='2'>รวมเงิน</td>
						<td align='center' ><input type='text' size='10' class='input-text-pos-disabled ui-corner-all' value='".number_format($sum_old_amount,2,'.','')."' readonly style='text-align:right'></td>
						<td align='center'><input type='text' size='10' class='input-text-pos-disabled ui-corner-all'  value='' readonly style='text-align:right'></td>
					</tr>";
			echo "
					</table><br>
					<table width='100%' border='0' cellpadding='5' cellspacing='0'>
						<tr height='40' valign='middle'>
						<td align='right' colspan='2'>&nbsp;</td>
						<td align='left' width='15%'>
							<a href='#' name='btnSubmit' id='btnSubmit' class='btnGrayClean' style='padding-top:5px;float:right;'>บันทึก</a>
						</td>
						<td align='left' width='15%'>
							<a href='#' name='btnCancel' id='btnCancel' class='btnGrayClean' style='padding-top:5px;'>ยกเลิก</a>
						</td>
					</tr>
					</table>";
		}//func
		
		function initEquipment(){
				//parent::TrancateTable("com_equipment_temp");
				$sql_init_temp="TRUNCATE TABLE `com_equipment_temp` ";
				$this->db->query($sql_init_temp);
				echo "<p align='left'>
						<table width='100%' border='0' cellpadding='2' cellspacing='0'>
							<tr height='35'>
								<td align='center' width='12%'>
									<a href='#' name='btnGetWait' id='btnGetWait' class='btnGrayClean' style='padding-top:5px;float:right;'>ดึงข้อมูลที่พักไว้</a>
								</td>
								<td align='center' width='12%'>
									<a href='#' name='btnNewData' id='btnNewData' class='btnGrayClean' style='padding-top:5px;float:right;'>บันทึกข้อมูลใหม่</a>
								</td>
								<td align='center'>&nbsp;</td>
							</tr>
						</table>
						</p>";
		}//func
		
		function chkWait(){
			$s_status='N';
			$sql_chk="SELECT COUNT(*) FROM com_equipment_int";
			$n_rows=$this->db->fetchOne($sql_chk);
			if($n_rows>0){
				$s_status='Y';
			}
			return $s_status;
		}//func
		
		function clearWait(){
			//parent::TrancateTable("com_equipment_int");
			$sql_init_temp="TRUNCATE TABLE `com_equipment_int` ";
			$this->db->query($sql_init_temp);
		}//func
		
		function getEquipment($flg_status){
			/**
			 * @desc get from table temp
			 * @return null
			 */
			//parent::TrancateTable("com_equipment_temp");
			$sql_init_temp="TRUNCATE TABLE `com_equipment_temp` ";
			$this->db->query($sql_init_temp);
			if($flg_status=='W'){
				$sql_wait="SELECT COUNT(*) FROM `com_equipment_int`";
				$n_wait=$this->db->fetchOne($sql_wait);
				if($n_wait>0){
					$sql_copy="INSERT INTO com_equipment_temp(`code`,`des`,`unit1`,`unit2`,`quata`,`order`) 
									SELECT `code`,`des`,`unit1`,`unit2`,`quata`,`order` FROM com_equipment_int";
					$this->db->query($sql_copy);
				}
			}else if($flg_status=='T'){
				$sql_group="SELECT `group`,`branch_tp` 
									FROM `com_equipment_group`
									WHERE branch_id='$this->m_branch_id'";
				$row_group=$this->db->fetchAll($sql_group);
				if(count($row_group)>0){
					$group=$row_group[0]['group'];
					$branch_tp=$row_group[0]['branch_tp'];
					$sql_copy="INSERT INTO com_equipment_temp(`code`,`des`,`unit1`,`unit2`,`quata`) 
									SELECT `code`,`des`,`unit1`,`unit2`,`quata` FROM com_equipment_data 
									WHERE `group`='$group' AND `branch_tp`='$branch_tp'";
					$this->db->query($sql_copy);
				}
			}

			
			$sql_temp="SELECT * FROM `com_equipment_temp` ";
			$row_temp=$this->db->fetchAll($sql_temp);
			echo "<table width='100%' border='0' cellpadding='2' cellspacing='1' bgcolor='#cccccc'>
					<tr height='35' bgcolor='#D6E6E9'>
						<td align='center'>รหัส</td>
						<td align='center'>รายละเอียด</td>
						<td align='center'>หน่วย</td>
						<td align='center'>ต่อเดือน</td>
						<td align='center'>จำนวนที่สามารถสั่งได้</td>
						<td align='center'>จำนวนที่สั่ง</td>
					</tr>";
			$i=0;
			foreach($row_temp as $data){
				($i%2==0)?$bg_color="#ffffff":$bg_color="#f2f2f2";
				echo "<tr bgcolor='$bg_color'>
							<td align='center'>$data[code]</td>
							<td align='center'>$data[des]</td>
							<td align='center'>
								<input type='text' size='5' class='input-text-pos-disabled ui-corner-all' value='".intval($data['unit1'])."' id='unit1_$data[code]' readonly style='text-align:right'>
							</td>
							<td align='center'>
								<input type='text' size='5' class='input-text-pos-disabled ui-corner-all' value='".intval($data['unit2'])."' id='unit2_$data[code]' readonly style='text-align:right'>
							</td>
							<td align='center'>
							<input type='text' size='5' class='input-text-pos-disabled ui-corner-all' value='".intval($data['quata'])."' id='quata_$data[code]' readonly style='text-align:right'>						
							</td>
							<td align='center'>
								<input type='text' size='5'  class='inputkey input-text-pos ui-corner-all' value='".intval($data['order'])."' id='$data[code]' name='new_order' style='text-align:right'></input>
							</td>
						</tr>";	
				$i++;
			}//foreach			
			echo "</table>
					<table width='100%' border='0' cellpadding='5' cellspacing='0'>
						<tr height='40' valign='middle'>
						<td>&nbsp;</td>
						<td align='right' width='10%'>
							<a href='#' name='btnWait' id='btnWait' class='btnGrayClean' style='padding-top:5px;float:right;'>พักข้อมูล</a>
						</td>
						<td align='left' width='7%'>
							<a href='#' name='btnSubmit' id='btnSubmit' class='btnGrayClean' style='padding-top:5px;'>บันทึก</a>
						</td>
						<td align='left' width='15%'>
							<a href='#' name='btnCancel' id='btnCancel' class='btnGrayClean' style='padding-top:5px;'>ยกเลิก</a>
						</td>
					</tr>
					</table>";
		}//func
		
		function chkEquipmentDate(){
			/**
			 * @desc
			 * @return Boolean Y: is ok,N:is error
			 */
			$arr_equdate=explode("-",$this->doc_date);
			$date_chk=intval($arr_equdate[2]);
			$status_chk='N';
			if(($date_chk>=8 && $date_chk<=14) || ($date_chk>=22 && $date_chk<=28)){
				$status_chk='Y';
			}
			return $status_chk;
		}//func
		
		function waitEquipment($strjson){
			/**
			 * @desc
			 * @param String $sstrjson
			 * @return
			 */
			//new process save all *WR 19032012
			$strjson=json_decode($strjson);
			//parent::TrancateTable("com_equipment_int");
			$sql_init_temp="TRUNCATE TABLE `com_equipment_int` ";
			$this->db->query($sql_init_temp);
			foreach($strjson->equipment as $data){
					$sql_temp="SELECT `des`,`unit1`,`unit2`,`quata` FROM  com_equipment_temp WHERE `code`='$data->code'";
					$row_temp=$this->db->fetchAll($sql_temp);
					$sql_add_w="INSERT INTO 
											com_equipment_int
										SET
											`code`='$data->code',
											`des`='".$row_temp[0][des]."',
											`unit1`='".$row_temp[0][unit1]."',
											`unit2`='".$row_temp[0][unit2]."',
											`quata`='".$row_temp[0][quata]."',
											`order`='".$data->order."'";
					$this->db->query($sql_add_w);
			}//foreach	
			
		}//func
		
		function saveEquipment($strjson,$flg_status){
			/**
			 * @desc
			 * @return
			 */
			$strjson=json_decode($strjson);
			$this->doc_tp="OR";
			$this->m_doc_no=parent::getDocNumber($this->doc_tp);
			$i=1;
			foreach($strjson->equipment as $data){
				if($data->order!=0){
					$sql_add_l="INSERT INTO 
										trn_diary2_or 
									SET
										 `corporation_id`='$this->m_corporation_id',
										 `company_id`='$this->m_company_id',
										 `branch_id`='$this->m_branch_id',
										 `doc_date`='$this->doc_date',
										 `doc_time`=CURTIME(),
										 `doc_no`='$this->m_doc_no',
										 `doc_tp`='$this->doc_tp',
										 `status_no`='AC',
										 `seq`='$i',
										 `product_id`='$data->code',
										 `quantity`='$data->order',
										 `amount`='',
										 `net_amt`='',
										 `reg_date`=CURDATE(),
								  	     `reg_time`=CURTIME(),
								         `reg_user`='$this->employee_id'";
					$this->db->query($sql_add_l);
					$i++;
				}//if				
			}//foreach
			
			$sql_sum="SELECT 
								SUM(amount) AS amount,SUM(net_amt) AS net_amt,SUM(quantity) AS quantity
							FROM
								trn_diary2_or
							WHERE
								 corporation_id='$this->m_corporation_id' AND
								 company_id='$this->m_company_id' AND
								 branch_id='$this->m_branch_id' AND
								 doc_date='$this->doc_date'";
			$row_sum=$this->db->fetchAll($sql_sum);
			$sum_quantity=$row_sum[0]['quantity'];
			$sum_amount=$row_sum[0]['amount'];//not used
			$sum_net_amt=$row_sum[0]['net_amt'];//not used			
			$sql_add_h="INSERT INTO 
									trn_diary1_or 
								SET
									`corporation_id`='$this->m_corporation_id',
									`company_id`='$this->m_company_id',
									`branch_id`='$this->m_branch_id',
									`doc_date`='$this->doc_date',
									`doc_time`=CURTIME(),
									`doc_no`='$this->m_doc_no',
									`doc_tp`='$this->doc_tp',
									`status_no`='AC',	
									`quantity`='$sum_quantity',								
									`amount`='$sum_amount',
									`net_amt`='$sum_net_amt',
									`computer_no`='$this->m_computer_no',
									`reg_date`=CURDATE(),
								  	`reg_time`=CURTIME(),
								    `reg_user`='$this->employee_id',
									`user_id`='$this->employee_id'";			
			$this->db->query($sql_add_h);
			//update table doc_no
			$arr_temp=explode("-",$this->m_doc_no);
			$this->doc_no_update=intval($arr_temp[2])+1;
			$sql_inc_docno="UPDATE 
										com_doc_no 
									SET 
										doc_no='$this->doc_no_update',
										upd_date=CURDATE(),
				   						upd_time=CURTIME(),
				   						upd_user='$this->employee_id'
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND 	
										branch_id='$this->m_branch_id' AND 
										branch_no='$this->m_branch_no' AND
										doc_tp='$this->doc_tp'";
			$this->db->query($sql_inc_docno);
			//parent::TrancateTable("com_equipment_temp");
			$sql_init_temp="TRUNCATE TABLE `com_equipment_temp` ";
			$this->db->query($sql_init_temp);
			if($flg_status=='W'){
				//parent::TrancateTable("com_equipment_int");
				$sql_init_temp="TRUNCATE TABLE `com_equipment_int` ";
				$this->db->query($sql_init_temp);
			}
		}//func
		
		function getRoundExpense(){
			/**
			 * @desc get History round expense
			 * @return
			 */
			$sql_h="SELECT doc_dt,amount,employee_id 
						FROM `com_expense_head` 
						WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND 
								branch_id='$this->m_branch_id'";
			$row_h=$this->db->fetchAll($sql_h);
			$arr_exp=array();
			$i=0;
			foreach($row_h as $data){
				$arr_docdt=$this->getDocDt($data['doc_dt']);
				$arr_exp[$i]['doc_dt']=$data['doc_dt'];
				$arr_exp[$i]['employee_id']=$data['employee_id'];
				$arr_exp[$i]['start_date']=$arr_docdt['start_date'];
				$arr_exp[$i]['end_date']=$arr_docdt['end_date'];
				$i++;
			}
			return $arr_exp;
		}//func
		
		function saveExpense($json,$doc_dt){
			/**
			 * @desc
			 * @param JSON $json
			 * @param Date $doc_dt
			 * @modify 16102015
			 * @return
			 */
			$this->doc_dt=$doc_dt;
			$json=json_decode($json);
			foreach($json->account as $data){				
				$sql_exist="SELECT amount
								FROM `com_expense_list` 
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND 
									branch_id='$this->m_branch_id' AND
									doc_dt='$this->doc_dt' AND 
									account_code='$data->account_code'";
				$r_exist=$this->db->fetchAll($sql_exist);
				if(!empty($r_exist)){
				//if($r_exist[0]['amount']>0){
					$org_amount=$r_exist[0]['amount'];
					$new_amount=$org_amount+$data->account_value;
					$sql_exp="UPDATE  `com_expense_list` 
									SET
										edit_dt=CURDATE(),
										amount='$new_amount',
										remark='' ,
										upd_date=CURDATE(),
										upd_time=CURTIME(),
										upd_user='$this->employee_id' 
									WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND 
										branch_id='$this->m_branch_id' AND
										doc_dt='$this->doc_dt' AND 
										account_code='$data->account_code'";
				}else{
					$sql_exp="INSERT INTO  
										`com_expense_list` 
									SET
										corporation_id='$this->m_corporation_id',
										company_id='$this->m_company_id',
										branch_id='$this->m_branch_id',
										branch_no='$this->m_branch_no',
										doc_dt='$this->doc_dt',
										edit_dt='$this->edit_dt',
										expense_dt=CURDATE(),
										account_code='$data->account_code',
										amount='$data->account_value',
										remark ='',
										reg_date=CURDATE(),
										reg_time=CURTIME(),
										reg_user='$this->employee_id',
										upd_date='',
										upd_time='',
										upd_user=''";				
				}
				$this->db->query($sql_exp);
			}//foreach		
			$sql_l="SELECT SUM(amount) 
					  FROM `com_expense_list` 
						WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND 
								branch_id='$this->m_branch_id' AND
								doc_dt='$this->doc_dt'";
			$sum_amount=$this->db->fetchOne($sql_l);	
			$sql_h="SELECT COUNT(*) 
					  FROM `com_expense_head` 
						WHERE
								corporation_id='$this->m_corporation_id' AND
								company_id='$this->m_company_id' AND 
								branch_id='$this->m_branch_id' AND
								doc_dt='$this->doc_dt'";
			$n_h=$this->db->fetchOne($sql_h);		
			if($n_h>0){
					$sql_h2="UPDATE  
									`com_expense_head` 
								SET
									amount='$sum_amount',
									remark ='',
									upd_date=CURDATE(),
									upd_time=CURTIME(),
									upd_user='$this->employee_id'
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									branch_no='$this->m_branch_no' AND
									doc_dt='$this->doc_dt'";
				}else{				
					$sql_h2="INSERT INTO  
									`com_expense_head` 
								SET
									corporation_id='$this->m_corporation_id',
									company_id='$this->m_company_id',
									branch_id='$this->m_branch_id',
									branch_no='$this->m_branch_no',
									doc_dt='$this->doc_dt',
									employee_id='$this->employee_id',
									amount='$sum_amount',
									remark ='',
									reg_date=CURDATE(),
									reg_time=CURTIME(),
									reg_user='$this->employee_id'	";
				}		
				$this->db->query($sql_h2);
		}//func		
	} //class
?>