<?php
	class Model_Report extends Model_PosGlobal{
		private $typebill;
		public function __construct($typebill=''){
			parent::__construct();
			if($typebill!=''){
				$this->typebill=$typebill;
			}
		}//func
		
		function billRTDetail($doc_no_start,$doc_no_stop){
			/**
			 * @desc
			 * @param
			 */
			$where="";
			if($doc_no_start!='' && $doc_no_stop!=''){
				$where.=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
			}else if($doc_no_start!=''){
				$where.=" AND doc_no='$doc_no_start'";
			}else if($doc_no_stop!=''){
				$where.=" AND doc_no='$doc_no_stop'";
			}
			$sql_docno="SELECT *
			FROM trn_diary1_rt
			WHERE
			corporation_id='$this->m_corporation_id' AND
			company_id='$this->m_company_id' AND
			branch_id='$this->m_branch_id' $where ORDER BY doc_no";
			$row_docno=$this->db->fetchAll($sql_docno);
			$cur_date=date("d/m/Y");
			$arr_docno=array();
			if(count($row_docno)>0){
				$i=0;
				foreach($row_docno as $data){
					$doc_no=$data['doc_no'];
					$doc_tp=$data['doc_tp'];
					$status_no=$data['status_no'];
					$arr_docno[$i]['doc_tp']=$doc_tp;
					$arr_docno[$i]['doc_no']=$doc_no;
					$arr_docno[$i]['branch_tp']=$this->m_branch_tp;
					$arr_docno[$i]['thermal_printer']=$this->m_thermal_printer;
					$arr_docno[$i]['computer_no']=$this->m_computer_no;
					$doc_date=$data['doc_date'];
					$billHead=$this->getHeaderBillRt($doc_no);
					$billDetail=$this->getDetailBillRt($doc_no);					
					$arr_docdate=explode("-",$billHead[0]['doc_date']);
					$doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
					echo "<p align='center'>
								<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>
								<tr height='35'>
									<td colspan='5' align='center'><img src='/sales/img/op/ssup_logo.png'></td>
								</tr>
								<tr height='35'>
									<td colspan='5' align='center'>".$billHead[0]['company_name_print']."</td>
								</tr>
								<tr height='30'>
									<td colspan='5' align='center'>ใบคำขอคืน Tester(RT)</td>
								</tr>
											
							   <tr height='30'>
									<td colspan='5' align='center'>&nbsp;</td>
								</tr>
											
								<tr>
									<td width='15%' align='right'>เลขที่เอกสาร : </td>
									<td width='25%'>".$billHead[0]['doc_no']."</td>
									<td width=''>&nbsp;</td>
									<td width='15%' align='right'>ว้นที่เอกสาร : </td>
									<td width='25%'>".$doc_date_show."</td>
								</tr>
								
								<tr>
									<td align='right'>เวลาเอกสาร : </td>
									<td>".$billHead[0]['doc_time']."</td>
									<td width='5%'>&nbsp;</td>
									<td align='right'>วันที่พิมพ์ : </td>
									<td>$cur_date</td>
								</tr>
								
								<tr>
									<td align='right'>Remark :</td>
									<td colspan='3'>".$billHead[0]['refer_doc_no']."</td>
									<td width='5%'>&nbsp;</td>
								</tr>
								<tr>
									<td align='right'>สถานะ : </td>
									<td colspan='3'>(".$billHead[0]['status_no'].") ใบคำขอคืน Tester(RT)</td>
									<td width='5%'>&nbsp;</td>
								</tr>
											
								<tr>
									<td align='right'>อ้างถึงเอกสาร : </td>
									<td>".$billHead[0]['doc_no']."</td>
									<td width='5%'>&nbsp;</td>
									<td align='right'>User : </td>
									<td>".$billHead[0]['reg_user']."</td>
								</tr>
								<tr>
									<td align='right'>รหัสจุดขาย : </td>
									<td colspan='3'>".$billHead[0]['transfer_status']."</td>
									<td width='5%'>&nbsp;</td>
								</tr>
					";
					$member_percent=$billHead[0]['member_percent'];
					$special_percent=$billHead[0]['special_percent'];
					
// 					echo "<pre>";
// 					print_r($billHead);
// 					echo "</pre>";
					$i=0;
					$q_sum=0;
					foreach($billDetail as $dataL){
						$i++;
						$q_sum+=$dataL['quantity'];
						$name_product=trim($dataL['name_product']);
						if(strlen($name_product)>30){
							$name_product=mb_substr($name_product,0,20,'utf-8');
							$name_product.="...";
						}
						if(intval($dataL['member_discount1'])==0){
							$chk_member_percent=0;
						}else{
							$chk_member_percent=$member_percent;
						}
		
						if(intval($dataL['member_discount2'])==0){
							$chk_special_percent=0;
						}else{
							$chk_special_percent=$special_percent;
						}
						echo "<tr>
								  <td colspan='5' align='left'>
										<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff'>
											<tr>
								       		<td width='5%' align='center'>".intval($dataL['quantity'])."</td>
											<td align='left'>".$dataL['product_id']."</td>
											<td align='left'>$name_product</td>
											<td width='13%' align='right'>".number_format($dataL['price'],2)."</td>
											<td width='10%' align='right'>".number_format($dataL['amount'],2)."</td>
											</tr>
													
									</table>		
							   </td>	
							</tr>";
					}//foreach
					
					echo "<tr><td colspan='5'><hr/></td></tr>
							<tr>
								  <td colspan='5'>
										<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff'>
											<tr>
								       		<td width='5%' align='center'>".$q_sum."</td>
											<td align='right'>จำนวนทั้งหมด </td>
																<td align='left'>&nbsp;&nbsp; $i &nbsp;รายการ</td>
																<td width='13%' align='right'>&nbsp;</td>
											<td width='10%' align='right'>".number_format($billHead[0]['amount'],2)."&nbsp;</td>
											</tr>
							
									</table>
							   </td>
							</tr>";
		
// 					echo "
// 							<tr><td colspan='5'><hr/></td></tr>
//     						<tr>
// 								<td align='right'>&nbsp;</td>
// 								<td align='right'>จำนวนชิ้นรวม = </td>
// 								<td width='5%'> ".intval($billHead[0]['quantity'])."</td>
// 								<td align='right'>Total = </td>
// 								<td align='left'>".number_format($billHead[0]['amount'],2)."</td>								
// 						    </tr>";
					echo "
					</tr>
					</table>
					</p>";
					$i++;
				}//foreach
				$json_docno=json_encode($arr_docno);
				echo "<input type='hidden' id='str_docno' name='str_docno' value='$json_docno'></input>";
			}else{
				//not found
				return false;
			}//if
				
		}//func
		
		function getHeaderBillRt($doc_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_h="SELECT* FROM trn_diary1_rq WHERE doc_no='$doc_no'";
			$row_h=$this->db->fetchAll($sql_h);
			
			$objPos=new Model_PosGlobal();
			$row_branch=$objPos->getBranch();
			$row_company=$objPos->getCompany();
			if(count($row_branch)>0){
				$sql_qty_details="SELECT SUM(quantity) FROM trn_diary2_rq WHERE doc_no='$doc_no'";
				$qty_details=$this->db->fetchOne($sql_qty_details);
				$row_h[0]['quantity']=$qty_details;
				$row_h[0]['branch_seq']=$row_branch[0]['branch_seq'];
				$row_h[0]['branch_name']=$row_branch[0]['branch_name'];
				$row_h[0]['branch_name_e']=$row_branch[0]['branch_name_e'];
				$row_h[0]['branch_tel']=$row_branch[0]['branch_phone'];
				$row_h[0]['company_name_print']=$row_company[0]['company_name_print'];
				$row_h[0]['tax_id']=$row_company[0]['tax_id'];
			}else{
				$row_h[0]['branch_name']='';
				$row_h[0]['branch_name_e']='';
				$row_h[0]['branch_tel']='';
				$row_h[0]['company_name_print']='';
				$row_h[0]['tax_id']='';
			}
				
			$sql_discount="SELECT (sum( discount ) +
									sum( member_discount1 ) +
									sum( member_discount2 ) +
									sum( co_promo_discount ) +
									sum( coupon_discount ) +
									sum( special_discount ) +
									sum( other_discount )) AS sum_discount
								FROM
									trn_diary1_rq
								WHERE
									doc_no='$doc_no'";
			$row_discount=$this->db->fetchAll($sql_discount);
			$row_h[0]['discount']=$row_discount[0]['sum_discount'];				
			if($this->m_com_ip=='127.0.0.1'){
				$row_h[0]['print2ip']='LOCAL';
			}else{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
				$sql_chk="SELECT COUNT(*)
				FROM com_branch_computer
				WHERE
				corporation_id='$this->m_corporation_id' AND
				company_id='$this->m_company_id' AND
				branch_id='$this->m_branch_id' AND
				com_ip='$this->m_com_ip' AND
				computer_no<>'1' AND
				thermal_printer='Y'";
				$n_chk=$this->db->fetchOne($sql_chk);
				if($n_chk>0){
					$row_h[0]['print2ip']=$this->m_com_ip;
				}else{
					$row_h[0]['print2ip']='LOCAL';
				}
			}				
			$row_h[0]['branch_tp']=$this->m_branch_tp;			
			return $row_h;
		}//func
		
		
		function getDetailBillRt($doc_no){
			/**
			 * @desc get detail bill rt
			 * @var String $doc_no
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$sql_detail="
			SELECT
			a.seq,
			a.quantity,
			a.product_id,
			a.price,
			a.discount,
			a.amount,
			a.net_amt,
			a.member_discount1,
			a.member_discount2,
			b.name_product
			from
			trn_diary2_rt as a inner join com_product_master as b
			on a.product_id=b.product_id
			where
			a.doc_no='$doc_no'";
			$row_l=$this->db->fetchAll($sql_detail);
			return $row_l;
		}//func
		
		function chkProlineMsgLstBill($doc_no){
			/**
			 * @desc ตรวจสอบได้สิทธิ 35% บิลถัดไปหรือไม่ แสดงข้อความท้ายบิล
			 * @create : 10/03/2016
			 * @last modify 18012017
			 * @cond : OPLID300 แสดงข้อความทุกบิล
			 * 			: OI07200216 Goss 1000 ขึ้นไป
			 * @return Boolean Y or N
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$res_chk='N';
			$sql_h="SELECT COUNT(*) FROM trn_diary1
						WHERE 
							doc_no='$doc_no' AND flag<>'C' AND net_amt>='1000' AND co_promo_code IN('OPPLI300','OI06501116')";
			$n_chk=$this->db->fetchOne($sql_h);
			if($n_chk>0){
				$res_chk='Y';
			}
			return  $res_chk;
		}//func
		
		function getGreenPoint($doc_no){
			/**
			 * @desc 12062014 ได้คะแนนถึง 2014-12-31 09022015-31032015
			 * @return $g_point
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_t2="SELECT COUNT(*) FROM trn_diary2 
			WHERE `doc_no`='$doc_no' AND  `promo_code`='OX17160514' AND `doc_date` BETWEEN '2015-02-09' AND '2015-03-31'";
			$n_chk2=$this->db->fetchOne($sql_t2);
			if($n_chk2>0){
				$n_gpoint=4;
			}else{
				$n_gpoint=0;
			}
			return $n_gpoint;
		}//func
		
		function setReprintNo($doc_no){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_upd="UPDATE trn_diary1 SET print_no=print_no + 1 WHERE doc_no='$doc_no'";
			$this->db->query($sql_upd);
		}//func
		
		function getReprintNo($doc_no){
			/**
			 * @desc
			 * @return
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_print_no="SELECT print_no FROM trn_diary1 WHERE doc_no='$doc_no'";
			$row_print_no=$this->db->fetchAll($sql_print_no);
			return $row_print_no[0]['print_no'];
		}//func
		
	    function discountRefMember($doc_no){
			/**
			 * @desc for non member to reference to be member
			 * @param String $doc_no
			 * @return
			 */
	    	$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
	    	$sql_diary1="SELECT doc_date,amount,discount,net_amt 
	    						FROM trn_diary1 
	    							WHERE doc_no='$doc_no'";
	    	$rows_diary1=$this->db->fetchAll($sql_diary1);
	    	$sum_discount=0.00;
	    	if(count($rows_diary1)>0){	    
	    		$doc_date=$rows_diary1[0]['doc_date'];
	    		////////////////////////////////////////// start //////////////////////////////////////////////////////
		    	$arr_special_day=parent::getPromotionDayRef($doc_date);
				$special_day=$arr_special_day[0];
				$arr_result[0]['dw']=$arr_special_day[0];
				$arr_result[0]['dw_desc']=$arr_special_day[1];
				 $is_field="";
				$arr_th=array('50','51','52','53','54');
				if(in_array($special_day,$arr_th)){
					$is_field="special_percent";
					$arr_result[0]['dw_status']="special";
				}else{
					$is_field="normal_percent";
					$arr_result[0]['dw_status']="normal";
				}
				
		   		$sql_pdiscount="SELECT $is_field
								FROM com_percent_discount 
								WHERE corporation_id='$this->m_corporation_id' AND 
									  company_id='$this->m_company_id' AND 
									  cardtype_id='6'";	
				$row_pdiscount=$this->db->fetchCol($sql_pdiscount);
				$percent_discount=0.0;								
				if(count($row_pdiscount)>0){
					$percent_discount=$row_pdiscount[0];
				}
	    		////////////////////////////////////////// stop //////////////////////////////////////////////////////
	    		//*WR25042016
	    		//$member_discount=$rows_diary1[0]['net_amt']*($percent_discount/100);
				$member_discount=$rows_diary1[0]['amount']*($percent_discount/100);
				
	    		$arr_result[0]['ref_discount']=parent::getSatang($member_discount,'up');
	    		//get point
	    		$ref_net_amt=$rows_diary1[0]['amount']-($rows_diary1[0]['discount']+$arr_result[0]['ref_discount']);
	    		$point1=parent::getPoint1($ref_net_amt);
				$point2=parent::getPoint2($ref_net_amt);	
	    		$ref_total_point=$point1+$point2;
	    		$arr_result[0]['ref_total_point']=$ref_total_point;
	    	}
	    	return $arr_result;
	    }//func
		
		function getHeaderBill($doc_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$sql_h="SELECT* FROM trn_diary1 WHERE doc_no='$doc_no'";
			$row_h=$this->db->fetchAll($sql_h);	
			if(!empty($row_h)){
				$credit_no_ref=$row_h[0]['credit_no'];
				$credit_no_ref=trim($credit_no_ref);
				$credit_no_len=strlen($credit_no_ref);
				if($credit_no_len==16){
					$credit_no_ref=substr($credit_no_ref,-4);
				}else if($credit_no_len>0 && $credit_no_len<16){
					$credit_no_ref=substr($credit_no_ref,-4);
				}
				$row_h[0]['credit_no']=$credit_no_ref;
			}
			
			$objPos=new Model_PosGlobal();
			
			//*===================== WR 24062015 for idcard ===============
			$sp_day=$row_h[0]['special_day'];
			if($sp_day!=''){
					$arr_spday=$objPos->getSpecialDayInfo($sp_day);						
					$curr_sp_date=$arr_spday[0]['curr_sp_date'];
					$next_sp_date=$arr_spday[0]['next_sp_date']; //+ 1 month for bill 01 or next ops day
					$arr_currspdate=explode("-",$curr_sp_date);
					$timeStmpCurrSpDate = mktime(0,0,0,$arr_currspdate[1],$arr_currspdate[2],$arr_currspdate[0]);	
					$arr_currdocdate=explode("-",$this->doc_date);
					$timeStmpCurrDocDate = mktime(0,0,0,$arr_currdocdate[1],$arr_currdocdate[2],$arr_currdocdate[0]);	
					if($timeStmpCurrSpDate>$timeStmpCurrDocDate){
						//วัน opsday > วันที่เอกสาร	ยังไม่ถึง opsday
						$arr_spdate_show=explode('-',$curr_sp_date);
						$str_sp_date_show=$arr_spdate_show[2]."/".$arr_spdate_show[1]."/".$arr_spdate_show[0];
						$row_h[0]['next_sp_date']=$str_sp_date_show;
					}else{
						//วันที่ opsday <= วันที่เอกสาร 
						//next month
						$arr_spdate_show=explode('-',$next_sp_date);
						$str_sp_date_show=$arr_spdate_show[2]."/".$arr_spdate_show[1]."/".$arr_spdate_show[0];
						$row_h[0]['next_sp_date']=$str_sp_date_show;
					}
			}else{
				$row_h[0]['next_sp_date']='';
			}//end if
			
			//*===================== WR 24062015 for idcard ===============
			
			$row_branch=$objPos->getBranch();
			$row_company=$objPos->getCompany();
			if(count($row_branch)>0){
				$sql_qty_details="SELECT SUM(quantity) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code NOT IN('FREEBAG')";
				$qty_details=$this->db->fetchOne($sql_qty_details);
				$row_h[0]['quantity']=$qty_details;
				$row_h[0]['branch_seq']=$row_branch[0]['branch_seq'];//WR19122013
				$row_h[0]['branch_name']=$row_branch[0]['branch_name'];
				$row_h[0]['branch_name_e']=$row_branch[0]['branch_name_e'];
				$row_h[0]['branch_tel']=$row_branch[0]['branch_phone'];
				$row_h[0]['company_name_print']=$row_company[0]['company_name_print'];
				$row_h[0]['tax_id']=$row_company[0]['tax_id'];
			}else{
				$row_h[0]['branch_name']='';
				$row_h[0]['branch_name_e']='';
				$row_h[0]['branch_tel']='';	
				$row_h[0]['company_name_print']='';
				$row_h[0]['tax_id']='';
			}	
			
			if($row_h[0]['doc_tp']=='VT'){
				$vt_vat=$row_h[0]['vat'];
				$vt_net=$row_h[0]['net_amt'];
				$row_h[0]['net_amt_before']=$vt_net-$vt_vat;
				$row_h[0]['vat']=$vt_vat;	
			}
			
			if($row_h[0]['doc_tp']=='CN'){
				$sql_ref="SELECT status_no,amount as ref_amount,net_amt as ref_net_amt FROM trn_diary1 WHERE doc_no='".$row_h[0]['refer_doc_no']."'";
				$row_ref=$this->db->fetchAll($sql_ref);
				if(count($row_ref)>0){
					$status_cn=$row_h[0]['status_no'];
					$row_h[0]['ref_net_amount']=$row_ref[0]['ref_amount'];
					$row_h[0]['ref_net_amt']=$row_ref[0]['ref_net_amt'];
					$net_amt_valid=$row_h[0]['ref_net_amt']-$row_h[0]['net_amt'];
				    $net_amt_cn=$row_h[0]['net_amt']*100/110;
				    $vat_add=$row_h[0]['net_amt']*10/110;
				    $net_amt_total=$row_h[0]['net_amt'];
				    $row_h[0]['net_amt_valid']=parent::getSatang($net_amt_valid,'normal');
					$row_h[0]['net_amt_cn']=parent::getSatang($net_amt_cn,'normal');
					$row_h[0]['vat_add']=parent::getSatang($vat_add,'normal');
					$row_h[0]['net_amt_total']=parent::getSatang($net_amt_total,'normal');
					$sql_desc="SELECT description FROM com_doc_status WHERE status_no='$status_cn'";
					$row_desc=$this->db->fetchAll($sql_desc);
					$row_h[0]['cn_description']=$row_desc[0]['description'];
				}				
			}else{
				$row_h[0]['ref_net_amount']=0.00;
				$row_h[0]['ref_net_amt']=0.00;
		        $row_h[0]['net_amt_valid']=0.00;
				$row_h[0]['net_amt_cn']=0.00;
				$row_h[0]['vat_add']=0.00;
				$row_h[0]['net_amt_total']=0.00;
				$row_h[0]['cn_description']='';
			}
			
			//WR23122013
			if($row_h[0]['status_no']=='02'){
				$member_id_ref=$row_h[0]['member_id'];
				$sql_refapp="SELECT application_id FROM trn_diary1 WHERE member_id='$member_id_ref' AND status_no='01' ORDER BY doc_date DESC LIMIT 0,1";
				$row_refapp=$this->db->fetchAll($sql_refapp);
				if(!empty($row_refapp)){
					$row_h[0]['application_id']=$row_refapp[0]['application_id'];
				}
				$today = $row_h[0]['doc_date'];
				$next_month = date("Y-m-d", strtotime("$today +1 month"));
				$arr_nxt=explode('-',$next_month);
				$first_date=$arr_nxt[2]."/".$arr_nxt[1]."/".$arr_nxt[0];
				$last_date=$objPos->lastday($arr_nxt[1],$arr_nxt[0]);
				$arr_lxt=explode("-",$last_date);			
				$row_h[0]['rage_date']="01-".$arr_lxt[2]."/".$arr_lxt[1]."/".$arr_lxt[0];				
			}//end status_no=02
			
			if($row_h[0]['status_no']=='03'){
				$sql_emp="SELECT  name,surname FROM com_ecoupon WHERE employee_id='".$row_h[0]['member_id']."'";
				$rows_emp=$this->db->fetchAll($sql_emp);		
				if(count($rows_emp)>0){		
					$row_h[0]['emp_fullname']=$rows_emp[0]['name']." ".$rows_emp[0]['surname'];
				}else{
					$row_h[0]['emp_fullname']='';
				}
			}else{
				$row_h[0]['emp_fullname']='';
			}
			
			$sql_discount="SELECT (sum( discount ) + 
							 	sum( member_discount1 ) +
							 	sum( member_discount2 ) + 
							 	sum( co_promo_discount ) + 
							 	sum( coupon_discount ) + 
							 	sum( special_discount ) + 
							 	sum( other_discount )) AS sum_discount
			FROM 
				trn_diary1
			WHERE
				doc_no='$doc_no'";
			$row_discount=$this->db->fetchAll($sql_discount);
			$row_h[0]['discount']=$row_discount[0]['sum_discount'];			
			
		//ข้อความท้ายบิล beauty first class
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("promo","promo_sms_code_head");
			$sql_hpro="SELECT promo_code,promo_des,promo_amt,promo_amt_type,promo_price,member_tp,fix_number
						FROM promo_sms_code_head
						WHERE
							corporation_id='$this->m_corporation_id' AND
							company_id='$this->m_company_id' AND
							'$this->doc_date' BETWEEN start_date AND end_date	";
			$rows_hpro=$this->db->fetchAll($sql_hpro);
			if(count($rows_hpro)>0){
				foreach($rows_hpro as $dataPro){
					$fix_number=$dataPro['fix_number'];
					if($dataPro['member_tp']=='Y' && $row_h[0]['member_id']!=''){
						//member only
						if($dataPro['promo_price']=='N'){
							//ยอด net
							if($dataPro['promo_amt_type']=='G'){								
								if($row_h[0]['net_amt']>=$dataPro['promo_amt']){
									$row_h[0]['bfc_code']=parent::getBFCCode($doc_no,$fix_number);
								}else{
									$row_h[0]['bfc_code']='';
								}
							}else if($dataPro['promo_amt_type']=='N'){
								if($row_h[0]['net_amt']<$dataPro['promo_amt']){
									$row_h[0]['bfc_code']=parent::getBFCCode($doc_no,$fix_number);
								}else{
									$row_h[0]['bfc_code']='';
								}
							}
						}else{
							//ยอด amount
						}
					}else if($dataPro['member_tp']=='NM' && $row_h[0]['member_id']!=''){
						//new member
						$row_h[0]['bfc_code']='';//temp
					}else{
						//every one
						$row_h[0]['bfc_code']='';//temp
					}
					$promo_code=$dataPro['promo_code'];
					$sql_lpro="SELECT line,promo_des,print_code
								   FROM promo_sms_code_detail 
								   WHERE
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										promo_code='$promo_code' AND
										'$this->doc_date' BETWEEN start_date AND end_date	";
					$rows_lpro=$this->db->fetchAll($sql_lpro);
					if(count($rows_lpro)>0){
						$row_h[0]['promo_message']=$rows_lpro;
					}
									
				}//foreach
			}else{
				$row_h[0]['bfc_code']='';
				$row_h[0]['promo_message']=array();
			}
			
			if($this->m_com_ip=='127.0.0.1'){
				$row_h[0]['print2ip']='LOCAL';				
			}else{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_computer");
				$sql_chk="SELECT COUNT(*) 
								FROM com_branch_computer
								WHERE
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' AND
									com_ip='$this->m_com_ip' AND
									computer_no<>'1' AND
									thermal_printer='Y'";
				$n_chk=$this->db->fetchOne($sql_chk);
				if($n_chk>0){
					$row_h[0]['print2ip']=$this->m_com_ip;
				}else{
					$row_h[0]['print2ip']='LOCAL';	
				}
			}
			
			//get name,address from crm_card
			if($row_h[0]['member_id']!=''){
				$member_no=$row_h[0]['member_id'];
				$sql_cust="SELECT 
									a.customer_id,a.member_no,a.apply_date,a.special_day,a.cardtype_id,a.apply_promo,a.expire_date,a.status,
									b.name,b.surname,b.birthday,b.home,b.h_address,b.h_village_id,b.h_village,b.h_soi,b.h_road,b.h_district,b.h_amphur,
									b.h_province,b.h_postcode,b.mobile_no
								FROM
									crm_card AS a LEFT JOIN crm_profile AS b
									ON(a.customer_id=b.customer_id)
								WHERE
									member_no='$member_no'";
				$row_cust=$this->db->fetchAll($sql_cust);
				if(count($row_cust)>0){
					$cust_name=$row_cust[0]['name']." ".$row_cust[0]['surname'];
					$cust_addr=$row_cust[0]['h_address']." ".$row_cust[0]['h_village_id']." "
									.$row_cust[0]['h_village']." ".$row_cust[0]['h_soi']." "
									.$row_cust[0]['h_road']." ".$row_cust[0]['h_district']." ".$row_cust[0]['h_amphur']." "
									.$row_cust[0]['h_province']." ".$row_cust[0]['h_postcode'];
				}else{
					$cust_name='';
					$cust_addr='';
				}
				$row_h[0]['cust_name']=$cust_name;
				$row_h[0]['cust_addr']=$cust_addr;
			}else{
				$row_h[0]['cust_name']='';
				$row_h[0]['cust_addr']='';
			}
			
			$row_h[0]['branch_tp']=$this->m_branch_tp;
			if($this->m_branch_tp=='F'){
				$row_h[0]['tax_id']=$row_branch[0]['tax_id'];				
				$row_h[0]['title_line1']=$row_branch[0]['title_line1'];
				$row_h[0]['title_line2']=$row_branch[0]['title_line2'];
				$row_h[0]['title_line3']=$row_branch[0]['title_line3'];
				$row_h[0]['title_line4']=$row_branch[0]['title_line4'];
			}			
			//*WR10032016 for support proline ท้ายบิล
			$row_h[0]['msg_line']=$this->chkProlineMsgLstBill($doc_no);
			//*WR03032017
			$row_h[0]['cnprivilege']=$this->chkCnPrivilege($doc_no);
			//*WR31032017
			$row_h[0]['alipay_last_title']=$this->chkAlipayLstTitle($doc_no);	
			return $row_h;
		}//func
		
		function chkAlipayLstTitle($doc_no){
			/**
			 * @desc show last title bill alipay
			 * @create 31032017
			 * @return Boolean Y,N
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$type_ali='';
			$sql_chk_alipay="SELECT COUNT(*) FROM trn_diary2 WHERE doc_no='$doc_no' AND promo_code IN('OX07140317')";
			$n_chk_alipay=$this->db->fetchOne($sql_chk_alipay);
			if($n_chk_alipay>0){
				$type_ali='A';
			}else{
				$sql_chk_alipay="SELECT COUNT(*) FROM trn_diary2 
						WHERE doc_no='$doc_no' AND 
										promo_code IN('TOUR01','OX02460217','OS06270317', 'OS06280317', 'OL06290317', 'OT06300317'  , 'OT06310317', 'OX06320317', 'OX06330317')";
				$n_chk_alipay=$this->db->fetchOne($sql_chk_alipay);
				if($n_chk_alipay>0){
					$type_ali='B';
				}
			}
			return $type_ali;
		}//func
		
		function chkCnPrivilege($doc_no){
			/**
			 * @desc bill is cn privilege or not
			 * @create 03032017
			 * @return Boolean Y,N
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$priv_cn='Y';
			$sql_chkp_cn="SELECT COUNT(*) FROM trn_diary1 WHERE doc_no='$doc_no' AND co_promo_code IN('OX02460217')";
			$n_chkp_cn=$this->db->fetchOne($sql_chkp_cn);
			if($n_chkp_cn>0){
				$priv_cn='N';
			}
			return $priv_cn;
		}//func
		
	
		function getDetailBill($doc_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary2");
			$sql_detail="
			SELECT 
						a.seq,
						a.quantity,
						a.product_id,
						a.price,
						a.discount,
						a.amount,
						a.net_amt,
						a.member_discount1,
						a.member_discount2,
						b.name_product
			from 
						trn_diary2 as a inner join com_product_master as b
						on a.product_id=b.product_id
			where 
				a.doc_no='$doc_no' AND a.promo_code NOT IN('FREEBAG','OX17160514')";
			$row_l=$this->db->fetchAll($sql_detail);
			return $row_l;
		}//func
		
		function getPaidDesc($paid){
			/**
			 * @desc
			 * @var unknown_type
			 */
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","com_paid");
			$stmt_desc=$this->db->select()
							->from("com_paid",array('description'))
							->where("paid=?",$paid);
			$row_desc=$stmt_desc->query()->fetchAll();
			if(count($row_desc)>0){
				return $row_desc[0]['description'];
			}else{
				return "";
			}
		}//func
		
		function getProfile($member_no){
			if($member_no=='') return false;
			$stmt_member=$this->db->select()
								->from(array('a'=>'crm_card'),array('a.customer_id','a.member_no','a.apply_date','a.special_day',
												'a.cardtype_id','a.apply_promo','a.expire_date','a.status'))
								->joinLeft(array('b'=>'crm_profile'),'a.customer_id=b.customer_id',array('b.name','b.surname',
												'b.birthday','b.home','b.h_address','b.h_village_id','b.h_village','b.h_soi',
												'b.h_road','b.h_district','b.h_amphur','b.h_province','b.h_postcode'))
								->where('a.member_no=?',$member_no);
			$row_member=$stmt_member->query()->fetchAll();
			$address='';
			$fullname='';
			if(count($row_member)>0){
				$fullname=$row_member[0]['name']." ".$row_member[0]['surname'];
				$address.=$row_member[0]['h_address'].$row_member[0]['h_village_id'].$row_member[0]['h_village'].$row_member[0]['h_soi'];
				$address.=$row_member[0]['h_road'].$row_member[0]['h_district'].$row_member[0]['h_amphur'].$row_member[0]['h_province'];
			}
			$row_member[0]['fullname']=$fullname;
			$row_member[0]['fulladdress']=$address;
			return $row_member;
		}//func
		
		function getActPointDetail($refer_doc_no){
			$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1");
			$this->refer_doc_no=$refer_doc_no;
			if($this->refer_doc_no=='') return false;
			$sql_t1="SELECT refer_doc_no FROM trn_diary1 WHERE doc_no='$refer_doc_no'";
			$row_t1=$this->db->fetchAll($sql_t1);			
			$promo_code=$row_t1[0]['refer_doc_no'];
			$sql_l="SELECT * FROM `promo_point2_detail` 
					WHERE 
						corporation_id='$this->m_corporation_id' AND 
					  	company_id='$this->m_company_id' AND
					  	promo_code='$promo_code'
					ORDER BY line";					
			$arr_l=$this->db->fetchAll($sql_l);
			return $arr_l;
		}//func
		
		function billCnDetail($doc_no_start,$doc_no_stop){
			/**
			 * @desc
			 * @param String $doc_no_start
			 * @param String $doc_no_stop
			 * @return void
			 */
			$where="";
			if($doc_no_start!='' && $doc_no_stop!=''){
				$where.=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
			}else if($doc_no_start!=''){
				$where.=" AND doc_no='$doc_no_start'";
			}else if($doc_no_stop!=''){
				$where.=" AND doc_no='$doc_no_stop'";
			}
			$sql_docno="SELECT 
									*
								FROM trn_diary1 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' $where ORDER BY doc_no";
			
			$row_docno=$this->db->fetchAll($sql_docno);
			$arr_docno=array();
			if(count($row_docno)>0){
				$i=0;
				foreach($row_docno as $data){
					$doc_no=$data['doc_no'];
					$doc_tp=$data['doc_tp'];
					$arr_docno[$i]['doc_tp']=$doc_tp;
					$arr_docno[$i]['doc_no']=$doc_no;
					
					$arr_docno[$i]['thermal_printer']=$this->m_thermal_printer;
					$arr_docno[$i]['computer_no']=$this->m_computer_no;	
					
					$doc_date=$data['doc_date'];
					$billHead=$this->getHeaderBill($doc_no);	
					$billDetail=$this->getDetailBill($doc_no);
					$paiddesc=$this->getPaidDesc($billHead[0]['paid']);
					$total_point_receive=$billHead[0]['point1']+$billHead[0]['point2'];
					
					$arr_docdate=explode("-",$billHead[0]['doc_date']);
					$doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
					
				$str_cancel='';
				if($billHead[0]['flag']=='C'){
					$str_cancel='<ยกเลิก>';				
				}
					
					echo "<p align='center'>
								<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>								
								<tr height='35'>
									<td colspan='6' align='center'><img src='/sales/img/op/ssup_logo.png'></td>
								</tr>
								<tr height='35'>
									<td colspan='6' align='center'>".$billHead[0]['company_name_print']."</td>
								</tr>
								
								<tr height='35'>
									<td colspan='6' align='center'>89/1 ซอยรัชฏภัณฑ์ ถนนราชปรารภ แขวงมักกะสัน</td>
								</tr>
								<tr height='35'>
									<td colspan='6' align='center'>เขตราชเทวี กรุงเทพฯ 10400</td>
								</tr>
								
								<tr height='30'>
									<td colspan='6' align='center'>$str_cancel ใบลดหนี้(CREDIT NOTE)</td>
								</tr>
								<tr>
									<td width='18%'>TAX ID</td>
									<td width='25%'>".$billHead[0]['tax_id']."</td>
									<td width='30%'>&nbsp;</td>
									<td colspan='3' align='right'>POS ID : (04)C 20060 91001248</td>
								</tr>
								<tr>
									<td>Branch :</td>
									<td colspan='5'>".$billHead[0]['branch_id']."&nbsp;&nbsp;".$billHead[0]['branch_name']."</td>
								</tr>
								<tr>
									<td>Rcpt:</td>
									<td colspan='2'>$doc_no</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>".$doc_date_show."&nbsp;&nbsp;".$billHead[0]['doc_time']."</td>
								</tr>
								<tr>
									<td>Member ID:</td>
									<td colspan='2'>".$billHead[0]['member_id']."</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>Cashier :".$billHead[0]['saleman_id']."</td>
								</tr>";
					$member_percent=$billHead[0]['member_percent'];
					$special_percent=$billHead[0]['special_percent'];
					
					echo "<tr>
									<td>นามผู้ซื้อ:</td>
									<td colspan='5'>".$billHead[0]['name']."</td>
								</tr>
								<tr>
									<td>ที่อยู่:</td>
									<td colspan='5'>".$billHead[0]['address1']."</td>
								</tr>
								<tr>
									<td></td>
									<td colspan='5'>".$billHead[0]['address2']."</td>
								</tr>
								<tr>
									<td>สาเหตุที่ลดหนี้ : </td>
									<td colspan='5'>สินค้าไม่ตรงตามคำพรรณนา</td>									
								</tr>
								<tr>
									<td>เงื่อนไข :</td>
									<td colspan='5'> เงินสด</td>
								</tr>
								";

				        foreach($billDetail as $dataL){
					        	$name_product=trim($dataL['name_product']);
					        	if(strlen($name_product)>20){
					        		$name_product=mb_substr($name_product,0,20,'utf-8');
					        		$name_product.="...";
					        	}
						        if(intval($dataL['member_discount1'])==0){
					        		$chk_member_percent=0;
					        	}else{
					        		$chk_member_percent=$member_percent;
					        	}
					        	
					        	if(intval($dataL['member_discount2'])==0){
					        		$chk_special_percent=0;
					        	}else{
					        		$chk_special_percent=$special_percent;
					        	}
						       	echo "<tr>
								       		<td align='center'>".intval($dataL['quantity'])."</td>
											<td>".$dataL['product_id']."</td>
											<td>$name_product</td>
											<td width='13%' align='right'>[".number_format($dataL['discount'],2)."+".intval($chk_member_percent)."+".intval($chk_special_percent)."%]</td>
											<td width='10%' align='right'>".number_format($dataL['price'],2)."</td>
											<td width='15%' align='right'>".number_format($dataL['net_amt'],2)."</td>
										</tr>";
				        }//foreach 		
				        echo "<tr>								
									<td colspan='6'>ผิด ตก ยกเว้น E.&O.E.</td>
								</tr>";
							echo "
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>รวมเงิน</td>
									<td align='right'>".number_format($billHead[0]['amount'],2)."</td>
								</tr>
									<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>ส่วนลด</td>
									<td align='right'>".number_format($billHead[0]['discount'],2)."</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>สุทธิ</td>
									<td align='right'>".number_format($billHead[0]['net_amt'],2)."</td>
								</tr>
								<tr>
									<td colspan='3'>มูลค่าสินค้าตามใบกำกับภาษีเดิม</td>								
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align='right'>".number_format($billHead[0]['ref_net_amt'],2)."</td>
								</tr>";
					
							echo "
								<tr>
									<td colspan='3'>มูลค่าสินค้าที่ถูกต้อง</td>								
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align='right'>".$billHead[0]['net_amt_valid']."</td>
								</tr>
								<tr>
									<td colspan='3'>มูลค่าสินค้าที่ลดหนี้</td>								
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align='right'>".$billHead[0]['net_amt_cn']."</td>
								</tr>
								<tr>
									<td colspan='3'>ภาษีมูลค่าเพิ่ม</td>								
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align='right'>".$billHead[0]['vat_add']."</td>
								</tr>
								<tr>
									<td colspan='3'>รวมทั้งสิ้น</td>								
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align='right'>".$billHead[0]['net_amt_total']."</td>
								</tr>
								<tr>
									<td colspan='3'>อ้างถึงบิลเงินสด/ใบกำกับภาษีอย่างย่อ : </td>
									<td colspan='3' align='right'>".$billHead[0]['refer_doc_no']."</td>
								</tr>
								<tr>
									<td >สถานะ</td>			
									<td colspan='4' align='left'>".$billHead[0]['status_no']."&nbsp;(".$billHead[0]['cn_description'].")</td>		
									<td>&nbsp;</td>						
								</tr>
								<tr>
									<td>หมายเหตุ : </td>
									<td colspan='5'>".$billHead[0]['doc_remark']."</td>
								</tr>
									";
							
							 if($billHead[0]['status_no']=='41'){
							 	echo "<tr>
												<td>บัญชี:</td>
												<td colspan='5'>".$billHead[0]['acc_name']."</td>
											</tr>
											<tr>
												<td>ชื่อธนาคาร:</td>
												<td colspan='5'>".$billHead[0]['bank_name']."</td>
											</tr>
											<tr>
												<td>เลขที่บัญชี:</td>
												<td colspan='5'>".$billHead[0]['bank_acc']."</td>
											</tr>
											<tr>
												<td>โทรศัพท์:</td>
												<td colspan='5'>".$billHead[0]['tel1']."&nbsp;&nbsp;".$billHead[0]['tel2']."</td>
											</tr>";
					        }
							
						
								
							echo "
								<tr>
									<td colspan='6'>Customer Satisfaction Guarantee</td>
								</tr>
								<tr>
									<td colspan='6'>ยินดีรับคืนผลิตภัณฑ์ที่ไม่พึงพอใจในทุกกรณี ภายใน 14</td>
								</tr>
								<tr>
									<td colspan='6'>&nbsp;</td>
								</tr>
								<tr>
									<td colspan='6'>ลูกค้า ............................................</td>
								</tr>
								<tr>
									<td colspan='6'>&nbsp;</td>
								</tr>
								<tr>
									<td colspan='3'>www.orientalprincesssociety.com</td>
									<td colspan='3' align='right'>Beauty Line : 0-2642-6606</td>
								</tr>
								<tr>
									<td colspan='6'>เบอร์โทรศัพท์สาขา : {$billHead[0]['branch_tel']}</td>
								</tr>
								</table>
							</p>";
					$i++;
				}//foreach
				
				$json_docno=json_encode($arr_docno);
				echo "<input type='hidden' id='str_docno' name='str_docno' value='$json_docno'></input>";
			}else{
				//not found
				return false;
			}//if
		
		}//func
		
		function billDNDetail($doc_no_start,$doc_no_stop){
			/**
			 * @desc
			 * @param
			 */
			$where="";
			if($doc_no_start!='' && $doc_no_stop!=''){
				$where.=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
			}else if($doc_no_start!=''){
				$where.=" AND doc_no='$doc_no_start'";
			}else if($doc_no_stop!=''){
				$where.=" AND doc_no='$doc_no_stop'";
			}
			$sql_docno="SELECT 
									*
								FROM trn_diary1 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' $where ORDER BY doc_no";
			$row_docno=$this->db->fetchAll($sql_docno);
			$arr_docno=array();
			if(count($row_docno)>0){
				$i=0;
				foreach($row_docno as $data){
					$doc_no=$data['doc_no'];
					$doc_tp=$data['doc_tp'];
					$status_no=$data['status_no'];
					$arr_docno[$i]['doc_tp']=$doc_tp;
					$arr_docno[$i]['doc_no']=$doc_no;
					$arr_docno[$i]['branch_tp']=$this->m_branch_tp;
					
					$arr_docno[$i]['thermal_printer']=$this->m_thermal_printer;
					$arr_docno[$i]['computer_no']=$this->m_computer_no;			
					
					
					
					$doc_date=$data['doc_date'];
					$billHead=$this->getHeaderBill($doc_no);	
					$billDetail=$this->getDetailBill($doc_no);
					$paiddesc=$this->getPaidDesc($billHead[0]['paid']);
					$total_point_receive=$billHead[0]['point1']+$billHead[0]['point2'];
					
					$arr_docdate=explode("-",$billHead[0]['doc_date']);
					$doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
					echo "<p align='center'>
								<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>								
								<tr height='35'>
									<td colspan='6' align='center'><img src='/sales/img/op/ssup_logo.png'></td>
								</tr>
								<tr height='35'>
									<td colspan='6' align='center'>".$billHead[0]['company_name_print']."</td>
								</tr>								
								<tr height='30'>
									<td colspan='6' align='center'>ใบเบิกสินค้า</td>
								</tr>
								<tr>
									<td width='18%'>TAX ID</td>
									<td width='25%'>".$billHead[0]['tax_id']."</td>
									<td width='30%'>&nbsp;</td>
									<td colspan='3' align='right'>POS ID : (04)C 20060 91001248</td>
								</tr>
								<tr>
									<td>Branch :</td>
									<td colspan='5'>".$billHead[0]['branch_id']."&nbsp;&nbsp;".$billHead[0]['branch_name']."</td>
								</tr>
								<tr>
									<td>Rcpt:</td>
									<td colspan='2'>$doc_no</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>".$doc_date_show."&nbsp;&nbsp;".$billHead[0]['doc_time']."</td>
								</tr>
								<tr>
									<td>Member ID:</td>
									<td colspan='2'>".$billHead[0]['member_id']."</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>Cashier :".$billHead[0]['saleman_id']."</td>
								</tr>";
					$member_percent=$billHead[0]['member_percent'];
					$special_percent=$billHead[0]['special_percent'];
					
					echo "<tr>
									<td>นามผู้ซื้อ:</td>
									<td colspan='5'>".$billHead[0]['cust_name']."</td>
								</tr>
								<tr>
									<td>ที่อยู่:</td>
									<td colspan='5'>".$billHead[0]['cust_addr']."</td>
								</tr>";
					   /*
					    <tr>
							<td></td>
							<td colspan='5'>".$billHead[0]['address2']."</td>
						</tr>
					    */

				        foreach($billDetail as $dataL){
					        	$name_product=trim($dataL['name_product']);
					        	if(strlen($name_product)>20){
					        		$name_product=mb_substr($name_product,0,20,'utf-8');
					        		$name_product.="...";
					        	}
						        if(intval($dataL['member_discount1'])==0){
					        		$chk_member_percent=0;
					        	}else{
					        		$chk_member_percent=$member_percent;
					        	}
					        	
					        	if(intval($dataL['member_discount2'])==0){
					        		$chk_special_percent=0;
					        	}else{
					        		$chk_special_percent=$special_percent;
					        	}
						       	echo "<tr>
								       		<td align='center'>".intval($dataL['quantity'])."</td>
											<td>".$dataL['product_id']."</td>
											<td>$name_product</td>
											<td width='13%' align='right'>[".number_format($dataL['discount'],2)."+".intval($chk_member_percent)."+".intval($chk_special_percent)."%]</td>
											<td width='10%' align='right'>".number_format($dataL['price'],2)."</td>
											<td width='15%' align='right'>".number_format($dataL['net_amt'],2)."</td>
										</tr>";
				        }//foreach 
    
    				   echo "
    						<tr>
								<td>จำนวนชิ้นรวม =</td>
								<td align='left' colspan='2'>".intval($billHead[0]['quantity'])."</td>
								<td colspan='2'>Total =</td>
								<td align='right'>".number_format($billHead[0]['amount'],2)."</td>
						    </tr>";    				
    					echo "
    						<tr>
								<td colspan='2'>$paiddesc</td>
								<td align='right'>&nbsp;</td>
								<td colspan='2'>Discount</td>
								<td align='right'>".number_format($billHead[0]['discount'],2)."</td>
							</tr>";
    					
    					if($doc_tp=='DN' && $status_no=='01'){
							echo "<tr>
												<td>อ้างถึงรหัสสมาชิก</td>
												<td align='left'>".$billHead[0]['refer_member_id']."</td>
												<td colspan='4'>&nbsp;</td>
											</tr>";
						}
    					
					    echo "
								<tr>
									<td colspan='6'>Customer Satisfaction Guarantee</td>
								</tr>
								<tr>
									<td colspan='6'>ยินดีรับคืนผลิตภัณฑ์ที่ไม่พึงพอใจในทุกกรณี ภายใน 14</td>
								</tr>
								<tr>
									<td colspan='6'>&nbsp;</td>
								</tr>
								<tr>
									<td colspan='6'>ลูกค้า ............................................</td>
								</tr>
								<tr>
									<td colspan='6'>&nbsp;</td>
								</tr>
								<tr>
									<td colspan='3'>www.orientalprincesssociety.com</td>
									<td colspan='3' align='right'>Beauty Line : 0-2642-6606</td>
								</tr>
								<tr>
									<td colspan='6'>เบอร์โทรศัพท์สาขา : {$billHead[0]['branch_tel']}</td>
								</tr>
								</table>
							</p>";
					$i++;
				}//foreach				
				$json_docno=json_encode($arr_docno);
				echo "<input type='hidden' id='str_docno' name='str_docno' value='$json_docno'></input>";
			}else{
				//not found
				return false;
			}//if
			
		}//func
		
		function billRdDetail($doc_no_start,$doc_no_stop){
			/**
			 * @desc
			 * @param String $doc_no_start
			 * @param String $doc_no_stop
			 * @return void
			 */								
			$where="";
			if($doc_no_start!='' && $doc_no_stop!=''){
				$where.=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
			}else if($doc_no_start!=''){
				$where.=" AND doc_no='$doc_no_start'";
			}else if($doc_no_stop!=''){
				$where.=" AND doc_no='$doc_no_stop'";
			}
			$sql_docno="SELECT 
									*
								FROM trn_diary1 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' $where ORDER BY doc_no";
			$row_docno=$this->db->fetchAll($sql_docno);
			$arr_docno=array();
			if(count($row_docno)>0){
				$i=0;
				foreach($row_docno as $data){
					$doc_no=$data['doc_no'];
					$doc_tp=$data['doc_tp'];
					$arr_docno[$i]['doc_tp']=$doc_tp;
					$arr_docno[$i]['doc_no']=$doc_no;
					
					$arr_docno[$i]['thermal_printer']=$this->m_thermal_printer;
					$arr_docno[$i]['computer_no']=$this->m_computer_no;						
					
					$doc_date=$data['doc_date'];
					$billHead=$this->getHeaderBill($doc_no);	
					$billDetail=$this->getDetailBill($doc_no);
					$paiddesc=$this->getPaidDesc($billHead[0]['paid']);
					$total_point_receive=$billHead[0]['point1']+$billHead[0]['point2'];
					
					$arr_docdate=explode("-",$billHead[0]['doc_date']);
					$doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
					
					$print_no=$this->getReprintNo($doc_no);
					$print_no=$print_no+1;
					echo "<p align='center'>
								<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>								
								<tr height='35'>
									<td colspan='6' align='center'><img src='/sales/img/op/ssup_logo.png'></td>
								</tr>
								<tr height='35'>
									<td colspan='6' align='center'>".$billHead[0]['company_name_print']."</td>
								</tr>					
								<tr>
									<td colspan='6' align='center'>< สำเนาพิมพ์ครั้งที่ $print_no ></td>
								</tr>	
								<tr>
									<td>Branch :</td>
									<td colspan='5'>".$billHead[0]['branch_id']."&nbsp;&nbsp;".$billHead[0]['company_name_print']."</td>
								</tr>
								<tr>
									<td>Rcpt:</td>
									<td colspan='2'>$doc_no</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>".$doc_date_show."&nbsp;&nbsp;".$billHead[0]['doc_time']."</td>
								</tr>
								<tr>
									<td>Member ID:</td>
									<td colspan='2'>".$billHead[0]['member_id']."</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>Cashier :".$billHead[0]['saleman_id']."</td>
								</tr>";
				
					echo "<tr>
									<td>นามผู้ซื้อ:</td>
									<td colspan='5'>".$billHead[0]['name']."</td>
								</tr>
								<tr>
									<td>ที่อยู่:</td>
									<td colspan='5'>".$billHead[0]['address1']."</td>
								</tr>
								<tr>
									<td></td>
									<td colspan='5'>".$billHead[0]['address2']."</td>
								</tr>";
					
						$objReport=new Model_Report();
						$arr_actpointdetail=$objReport->getActPointDetail($doc_no);						
						foreach($arr_actpointdetail as $dataAct){
				        		echo "<tr>
							       		<td align='center'>&nbsp;</td>
										<td colspan='5'>".$dataAct['promo_des']."</td>
									</tr>";
       					}    
       			   $redeem_point=abs($billHead[0]['redeem_point']);	
       			   echo "<tr><td colspan='6'>คะแนนที่ใช้สิทธิ&nbsp;{$redeem_point}&nbsp;คะแนน</td></tr>
       			   			<tr><td colspan='2'>&nbsp;</td><td colspan='4'>ขอสงวนสิทธิเข้าร่วมกิจกรรม หากไม่มีเอกสารนี้มาแสดง</td></tr>
							<tr>
								<td colspan='3'>www.orientalprincesssociety.com</td>
								<td colspan='3' align='right'>Beauty Line : 0-2642-6606</td>
							</tr>
							<tr>
								<td colspan='6'>เบอร์โทรศัพท์สาขา : {$billHead[0]['branch_tel']}</td>
							</tr>
							</table>
						</p>";
						$i++;
				}//foreach
				
				$json_docno=json_encode($arr_docno);
				echo "<input type='hidden' id='str_docno' name='str_docno' value='$json_docno'></input>";			
			}else{
				//not found
				return false;
			}//if
		
		}//func
		
		function billDetail($doc_no_start,$doc_no_stop){
			/**
			 * @desc
			 * @param String $doc_no_start
			 * @param String $doc_no_stop
			 * @return void
			 */
			$where="";
			if($doc_no_start!='' && $doc_no_stop!=''){
				$where.=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
			}else if($doc_no_start!=''){
				$where.=" AND doc_no='$doc_no_start'";
			}else if($doc_no_stop!=''){
				$where.=" AND doc_no='$doc_no_stop'";
			}
			$sql_docno="SELECT 
									doc_no,doc_date,doc_tp,keyin_st,co_promo_code,status_no
								FROM trn_diary1 
								WHERE 
									corporation_id='$this->m_corporation_id' AND
									company_id='$this->m_company_id' AND
									branch_id='$this->m_branch_id' $where ORDER BY doc_no ASC";
						
			$row_docno=$this->db->fetchAll($sql_docno);
			$arr_docno=array();
			if(count($row_docno)>0){
				$i=0;
				foreach($row_docno as $data){
					$doc_no=$data['doc_no'];					
					$doc_tp=$data['doc_tp'];			
					$arr_docno[$i]['status_no']=$data['status_no'];	
					$arr_docno[$i]['doc_tp']=$doc_tp;
					$arr_docno[$i]['doc_no']=$doc_no;
					//*WR09092015 for supoort GUID_TOUR
					$arr_docno[$i]['co_promo_code']=$data['co_promo_code'];
					
					$arr_docno[$i]['branch_tp']=$this->m_branch_tp;
					$arr_docno[$i]['thermal_printer']=$this->m_thermal_printer;
					$arr_docno[$i]['computer_no']=$this->m_computer_no;					
					//*WR13052015
					$arr_docno[$i]['keyin_st']=$data['keyin_st'];	
					$doc_date=$data['doc_date'];
					$billHead=$this->getHeaderBill($doc_no);	
					$billDetail=$this->getDetailBill($doc_no);
					$paiddesc=$this->getPaidDesc($billHead[0]['paid']);
					$total_point_receive=$billHead[0]['point1']+$billHead[0]['point2'];					
					$arr_docdate=explode("-",$billHead[0]['doc_date']);
					$doc_date_show=$arr_docdate[2]."/".$arr_docdate[1]."/".$arr_docdate[0];
					
					//*WR06062017
					$row_branch=parent::getBranch();
					################################# BILL SL ############################
					echo "<p align='center'>
								<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>								
								<tr height='35'>
									<td colspan='6' align='center'><img src='/sales/img/op/ssup_logo.png'></td>
								</tr>
								<tr height='35'>
									<td colspan='6' align='center'>".$billHead[0]['company_name_print']."</td>
								</tr>";
					$str_cancel='';
								if($billHead[0]['flag']=='C'){
									$str_cancel="<ยกเลิก>";
								}
					if($doc_tp=='SL'){
						echo "
								<tr height='30'>
									<td colspan='6' align='center'>$str_cancel TAX INVOICE (ABB.)</td>
								</tr>";
					}else if($doc_tp=='VT'){
							echo "<tr height='30'>
										<td colspan='6' align='center'>89/1 Soi Ratchataphan,Rachaprarop</td>
									</tr>
									<tr height='30'>
										<td colspan='6' align='center'>Rd. Makkasun Rachatevee Bangkok 10400.</td>
									</tr>
									<tr height='30'>
										<td colspan='6' align='center'>TAX INVOICE / RECEIPT</td>
									</tr>";
					}


								echo "<tr>
									<td>Branch NO :</td>
									<td colspan='2'>".$billHead[0]['branch_id']."(".$row_branch[0]['branch_name'].")</td>
									<td>&nbsp;</td>
									<td colspan='2' align='right'>&nbsp;</td>
								</tr>";
								
								echo "<tr>
									<td>Rcpt : </td>";
								echo "<td colspan='2'>".$billHead[0]['doc_no']."</td>";
								echo "<td>&nbsp;</td>
									<td colspan='2' align='right'>".$billHead[0]['doc_date']." ".$billHead[0]['doc_time']."</td></tr>";
								
								echo "<tr>
									<td>Member ID:</td>";
									if(substr($billHead[0]['member_id'],0,2)=='ID'){							        	
							        	echo "<td colspan='2'>".$billHead[0]['idcard']."</td>";
							        }else{
							        	echo "<td colspan='2'>".$billHead[0]['member_id']."</td>";
							        }									
								echo "<td>&nbsp;</td>
									<td colspan='2' align='right'>Cashier : ".$billHead[0]['saleman_id']."</td>
								</tr>";
							
					if($doc_tp=='VT'){
							  	echo "<tr>
											<td>Name :</td>
											<td colspan='2'>".$billHead[0]['name']."</td>
											<td>&nbsp;</td>
											<td colspan='2' align='right'>&nbsp;</td>
										</tr>
										<tr>
											<td>Address :</td>
											<td colspan='5'>".$billHead[0]['address1']."</td>
										</tr>
										<tr>
											<td></td>
											<td colspan='5'>".$billHead[0]['address2']."</td>
										</tr>
										<tr>
											<td></td>
											<td colspan='5'>".$billHead[0]['address3']."</td>
										</tr>";
					}
					
					echo "</tr>";
					echo "</table>";
					echo "<hr/>";
					echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>";
					echo "<tr>
									<td width='10%' align='center'>Qty</td>
									<td colspan='2' align='center'>Description</td>
									<td align='center'>Disc.</td>
									<td align='center'>Price</td>
									<td align='center'>Total</td>
							</tr>";
					
					$member_percent=$billHead[0]['member_percent'];
					$special_percent=$billHead[0]['special_percent'];
			        foreach($billDetail as $dataL){
				        	$name_product=trim($dataL['name_product']);
				        	if(strlen($name_product)>20){
				        		$name_product=mb_substr($name_product,0,20,'utf-8');
				        		$name_product.="...";
				        	}
					        if(intval($dataL['member_discount1'])==0){
				        		$chk_member_percent=0;
				        	}else{
				        		$chk_member_percent=$member_percent;
				        	}
				        	
				        	if(intval($dataL['member_discount2'])==0){
				        		$chk_special_percent=0;
				        	}else{
				        		$chk_special_percent=$special_percent;
				        	}
				        	
				        	//*WR09012018 show decimal net_amt
				        	$item_net_amt=$dataL['net_amt'];
				        	$item_net_amt =  floor(($item_net_amt*100))/100;
				        	$item_net_amt=number_format($item_net_amt,2);
				        	
					       	echo "<tr>
							       		<td align='center'>".intval($dataL['quantity'])."</td>
										<td>".$dataL['product_id']."</td>
										<td>$name_product</td>
										<td width='13%' align='right'>[".number_format($dataL['discount'],2)."+".intval($chk_member_percent)."+".intval($chk_special_percent)."%]</td>
										<td width='10%' align='right'>".number_format($dataL['price'],2)."</td>
										<td width='15%' align='right'>".$item_net_amt."</td>
									</tr>";
			        	}//foreach 		
				        ################################# BILL SL ################################
// 						echo "
// 								<tr>
// 									<td colspan='2' align='center'>จำนวนชิ้นรวม =</td>
// 									<td>".intval($billHead[0]['quantity'])."</td>
// 									<td>&nbsp;</td>
// 									<td>Total</td>
// 									<td align='right'>".number_format($billHead[0]['amount'],2)."</td>
// 								</tr>";
						echo "</table>";
						echo "<hr/>";
						echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>";
						if($doc_tp=='SL'){
							echo "
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>Sub Total($)</td>
									<td>&nbsp;</td>									
									<td align='right'>".number_format($billHead[0]['amount'],2)."</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>Discount</td>
									<td>&nbsp;</td>
									<td align='right'>".number_format($billHead[0]['discount'],2)."</td>
								</tr>
											
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>Grand Total($)</td>
									<td>&nbsp;</td>
									<td align='right'>".number_format($billHead[0]['net_amt'],2)."</td>
								</tr>			
											
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>Grand Total(R)</td>
									<td>&nbsp;</td>
									<td align='right'>".number_format($billHead[0]['net_amt']*4000,2)."</td>
								</tr>			
								
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>Received($)</td>
									<td>&nbsp;</td>									
									<td align='right'>".number_format($billHead[0]['pay_cash'],2)."</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>Received(R)</td>
									<td>&nbsp;</td>									
									<td align='right'>".number_format($billHead[0]['pay_cash2'],2)."</td>
								</tr>							        
						       	<tr>
									<td align='center'>&nbsp;</td>
									<td  colspan='2'>Change($)</td>
									<td >&nbsp;</td>
									<td align='right'>".number_format($billHead[0]['change'],2)."</td>
								</tr>
								<tr>
									<td  align='center'>&nbsp;</td>
									<td colspan='2'>Change(R)</td>
									<td >&nbsp;</td>
									<td align='right'>".number_format($billHead[0]['change2'],2)."</td>
								</tr>";
					  echo "</table>";
					  echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>";
						echo "								        
						       	<tr>
						       		<td colspan='5'>&nbsp;</td>				
								</tr>";
						if($billHead[0]['member_id']!='' && @$this->point_total_today!=''){
								$point_total_today=$this->point_total_today;
								
								echo "								        
							       	<tr>
							       		<td>&nbsp;</td>
										<td colspan='2' align='left'>คะแนนสะสมเดิม</td>										
										<td colspan='2' align='left'>$point_total_today คะแนน</td>
										<td ></td>										
									</tr>";
								
						 }
						 
						$total_point_receive=0;
					     if(intval($billHead[0]['point1'])!=0 || intval($billHead[0]['point2'])!=0){
						        $total_point_receive=$billHead[0]['point1']+$billHead[0]['point2'];
								if(trim($billHead[0]['member_id'])!=''){
									echo "								        
							       	<tr>
							       		<td>&nbsp;</td>
										<td colspan='2' align='left'>คะแนนสะสมที่ได้รับในบิลนี้</td>										
										<td colspan='2' align='left'>(".$billHead[0]['point1']." + ".$billHead[0]['point2']." = ".$total_point_receive.") คะแนน</td>
										<td ></td>										
									</tr>";				
							     
								}							
					      }
					      
						$total_point_used=0;
						if(intval($billHead[0]['redeem_point'])!=0){
				         	$total_point_used=$billHead[0]['redeem_point'];
							if(trim($billHead[0]['member_id'])!=''){
								echo "								        
							       	<tr>
							       		<td>&nbsp;</td>
										<td colspan='2' align='left'>คะแนนสะสมที่แลกใช้ในบิลนี้</td>										
										<td colspan='2' align='left'>(".$total_point_used.") คะแนน</td>
										<td ></td>										
									</tr>";						      
							}
				        }
				        
				        $total_point_balance=0;
						if(trim($billHead[0]['member_id'])!=''){
				           $total_point_balance=intval($billHead[0]['total_point']);		
				           echo "								        
							       	<tr>
							       		<td>&nbsp;</td>
										<td colspan='2' align='left'>คะแนนสะสมคงเหลือทั้งสิ้น</td>										
										<td colspan='2' align='left'>(".$total_point_balance.") คะแนน</td>
										<td ></td>										
									</tr>";
				        }		
				        
						 if(@intval($this->point_total_today)!=0){
				        	//คะแนนคงเหลือ ณ วันนี้				       
					        $redeem_point=intval($billHead[0]['redeem_point']);
					        $point1=intval($billHead[0]['point1']);
					        $point2=intval($billHead[0]['point2']);
					        $receive_point=$point1+$point2;
					        $summary_point=@$this->point_total_today+$receive_point+$redeem_point; 
					          echo "								        
							       	<tr>
							       		<td>&nbsp;</td>
										<td colspan='2' align='left'>รวมคะแนนสะสมทั้งสิ้น</td>										
										<td colspan='2' align='left'>(".$summary_point.") คะแนน</td>
										<td ></td>										
									</tr>";						
				        }
				        
						//sms message
						if($billHead[0]['status_no']!='01' && $billHead[0]['bfc_code']!=''){
							$arr_line2print=$billHead[0]['promo_message'];
							if(!empty($arr_line2print)){
								foreach($arr_line2print as $dataMsg){
									if($dataMsg['print_code']=='Y'){
										 echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>".$dataMsg['promo_des']."".$billHead[0]['bfc_code']."</td>
												<td ></td>										
											</tr>";									
									}else{
										echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>".$dataMsg['promo_des']."</td>
												<td ></td>										
											</tr>";			
										
									}
							        $startY+=$l2br;
								    $pdf->SetY($startY);
									$pdf->SetX($startX);  
								}
							}
						}
						
						//case reprint not know 
						if($billHead[0]['member_id']=='' && !empty($this->arr_refdiscount)){							
								if($this->arr_refdiscount[0]['dw_status']=='special'){
									echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>หากคุณเป็นสมาชิกบัตร OPS </td>
												<td ></td>										
											</tr>";
									echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>คุณจะได้รับส่วนลดในครั้งนี้ ".number_format($this->arr_refdiscount[0]['ref_discount'],2)." บาท </td>
												<td ></td>										
											</tr>";
									echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>และคะแนนสะสม ".$this->arr_refdiscount[0]['ref_total_point']." คะแนน </td>
												<td ></td>										
											</tr>";							
								}else{
									echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>หากคุณเป็นสมาชิกบัตร OPS </td>
												<td ></td>										
											</tr>";
									echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>คุณจะได้รับส่วนลดในครั้งนี้  ".number_format($this->arr_refdiscount[0]['ref_discount'],2)." บาท</td>
												<td ></td>										
											</tr>";
									echo "								        
									       	<tr>
									       		<td>&nbsp;</td>															
												<td colspan='4' align='left'>และคะแนนสะสม  ".$this->arr_refdiscount[0]['ref_total_point']." คะแนน</td>
												<td ></td>										
											</tr>";					        
						        	
								}
							}
						        
						   if($billHead[0]['status_no']=='06'){
						   		echo "<tr>
											<td colspan='3' align='left'>อ้างถึงใบลดหนี้</td>
											<td colspan='3' align='right'>".$billHead[0]['refer_doc_no']."</td>
										</tr>
										<tr>
											<td colspan='3' align='left'>จำนวนเงิน</td>
											<td colspan='3' align='right'>".number_format($billHead[0]['refer_cn_net'],2)."</td>
										</tr>
										";
						   }
						   
						   //sms message
							if($billHead[0]['status_no']!='01' && $billHead[0]['bfc_code']!=''){
								$arr_line2print=$billHead[0]['promo_message'];
								if(!empty($arr_line2print)){
									foreach($arr_line2print as $dataMsg){
										if($dataMsg['print_code']=='Y'){
								       		echo "<tr><td colspan='6' align='left'>".$dataMsg['promo_des']."&nbsp;".$billHead[0]['bfc_code']."</td></tr>";
										}else{
											echo "<tr><td colspan='6' align='left'>".$dataMsg['promo_des']."</td></tr>";
										}
									}
								}
							}
							//sms message
						}else if($doc_tp=='VT'){
							
							echo "<tr>
										<td colspan='2' align='center'>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>Discount</td>
										<td align='right'>".number_format($billHead[0]['discount'],2)."</td>
									</tr>";
						
							echo "<tr>
										<td colspan='2' align='center'>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>Net</td>";
							
							if(intVal($billHead[0]['net_amt'])!=0){
								//$net_before=$billHead[0]['net_amt']-$billHead[0]['vat'];
								echo "<td align='right'>".number_format($billHead[0]['net_amt_before'],2)."</td>
									</tr>";
							}else{
								echo "<td align='right'>0.00</td>
									</tr>";
							}
							
							echo "<tr>
										<td colspan='2' align='center'>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>VAT</td>";
							
							if(intVal($billHead[0]['net_amt'])!=0){
								echo "<td align='right'>".number_format($billHead[0]['vat'],2)."</td>
									</tr>";
							}else{
								echo "<td align='right'>0.00</td>
									</tr>";
							}
	
							echo "<tr>
										<td colspan='2' align='center'>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>Net (INC.VAT)</td>
										<td align='right'>".number_format($billHead[0]['net_amt'],2)."</td>
									</tr>";
						}
						################################# BILL SL ################################	
						if(substr($billHead[0]['member_id'],0,2)=='ID'){				         
							echo "
							<tr>
								<td colspan='6'>&nbsp;</td>
							</tr>";
					       echo "	<tr>
										<td colspan='6'>&nbsp;Special Day : ".$billHead[0]['special_day']."</td>
									</tr>";
					 		$week_spday=$billHead[0]['special_day'];
					 		$chk_spday=substr($week_spday,0,3);
					 		if($chk_spday=='OPS'){
					 			$str_spday="พฤหัสบดี";
					 		}else if($chk_spday=='OPT'){
					 			$str_spday="อังคาร";
					 		}else{
					 			$str_spday='';
					 		}
					 		
					 		
					 		$week_spday=substr($week_spday,3,1);
					         echo "<tr>
										<td colspan='6'>&nbsp;OPS Day ของคุณรับส่วนลด 15% ทุกวัน".$str_spday." ที่ ".$week_spday." ของทุกเดือน</td>
									</tr>";
							$curr_sp_date=$billHead[0]['curr_sp_date'];
							$next_sp_date=$billHead[0]['next_sp_date'];			
							
							//echo $next_sp_date;
						    echo "<tr>
										<td colspan='6'>&nbsp;OPS Day  ครั้งถัดไปคือ ".$next_sp_date." </td>
									</tr>";
				        }//end if
// 						echo "
// 								<tr>
// 									<td colspan='6'>&nbsp;</td>
// 								</tr>
// 								<tr>
// 									<td colspan='6'>Customer Satisfaction Guarantee</td>
// 								</tr>
// 								<tr>
// 									<td colspan='6'>ยินดีรับคืนผลิตภัณฑ์ที่ไม่พึงพอใจในทุกกรณี ภายใน 14</td>
// 								</tr>
// 								<tr>
// 									<td colspan='6'>ผู้รับเงิน............................................</td>
// 								</tr>";
								if($billHead[0]['status_no']=='05'){
									echo "<tr>
												<td colspan='3'>อ้างถึงรหัสสมาชิก</td>
												<td colspan='3' align='right'>".$billHead[0]['refer_member_id']."</td>
											</tr>";
								}
// 								echo "
// 								<tr>
// 									<td colspan='3'>www.orientalprincesssociety.com</td>
// 									<td colspan='3' align='right'>Beauty Line : 0-2642-6606</td>
// 								</tr>
// 								<tr>
// 									<td colspan='6'>เบอร์โทรศัพท์สาขา : {$billHead[0]['branch_tel']}</td>
// 								</tr>";
						echo "	</table>
							</p>";
					$i++;
				}//foreach						
				$json_docno=json_encode($arr_docno);
				echo "<input type='hidden' id='str_docno' name='str_docno' value='$json_docno'></input>";
			}else{
				//not found
				return false;
			}//if
		
		}//func
		
	}//class
