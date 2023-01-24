<?php 
		class Model_Inventory extends Model_PosGlobal{
				/**
				 * 
				 * @desc :global variable
				 * public $this->db ;connection
				 * public $this->m_corporation_id ;OP
				 * public $this->m_company_id   ;OP
				 * public $this->m_branch_id       ;ex 7777
				 * public $this->m_computer_no   ;computer no.
				 * public $this->m_com_ip;
				 * pubic $this->doc_date;
				 * public $this->m_thermal_printer ;Y,N
				 */
				function __construct(){
					parent::__construct();	
				}//func		
			
				
			function getPickingList($order_no,$page,$qtype,$query,$rp,$sortname,$sortorder){
			/**
			 * @name getCshTemp
			 * @desc
			 * @param $order_no is
			 * @param $page is
			 * @param $qtype is
			 * @param $query is
			 * @param $rp is
			 * @param $sortname is
			 * @param $sortorder is
			 * @return json format string of data.
			 */
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_iq");//trn_promotion_tmp1
				$this->order_no=$order_no;
				if (!$sortname) $sortname = 'id';
				if (!$sortorder) $sortorder = 'desc';
				$sort = "ORDER BY $sortname $sortorder";
				if (!$page) $page = 1;
				if (!$rp) $rp = 10;
				$start = (($page-1) * $rp);
				$limit = "LIMIT $start, $rp";		
					
				$sql = "SELECT * FROM  trn_tdiary2_iq";				
				$arr_list=$this->db->fetchAll($sql);		
				$total = count($arr_list);
				//test start
		
				if(count($arr_list)>0)
				{
					$data['page'] = $page;
					$data['total'] = $total;
					//$i=1;
					$i=(($page*$rp)-$rp)+1;
					foreach($arr_list as $row){	
						$sql_all_pick = "SELECT doc_remark FROM trn_tdiary1_iq where corporation_id='".$row['corporation_id']."' and
												company_id='".$row['company_id']."' and
												branch_id='".$row['branch_id']."' and
												doc_date='".$row['doc_date']."' and
												doc_no='".$row['doc_no']."' and
												doc_tp='PL'";
						//echo $sql_all_pick;
						$arr_chkdist=$this->db->fetchOne($sql_all_pick);	
						//echo $arr_conf['doc_remark'];
						$qty = number_format($row['quantity']);
						$product_status = $row['product_status'];
						if($qty < 1 && $row['product_id'] != "BOX001")
						{
							$qty = "0";
						}
						if($row['product_id'] == "BOX001")
						{
							$row['product_id'] = "BOX001 (จำนวนกล่องที่ส่งคืน)";
							$product_status = intval($row['amount'],10);
						}
						$rows[] = array(
								"id" => $row['id'],
								"absid" => $row['product_id'],
								"cell" => array(
											$i,
											$row['product_id'],	
											$qty,
											$row['promo_st'],
											$product_status
								)
						);
						$i++;
					}//foreach
					$data['rows'] = $rows;	
				}
				else
				{
					$data=array();
				}			
				return $data;			
			}//getPickingList

			function getDocDate()
			{
				/*$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_date");
				
				$sql = "SELECT DATE_FORMAT(doc_date,'%d/%m/%Y') FROM   com_doc_date";				
				$arr_list=$this->db->fetchOne($sql);*/
					
				return date("d/m/Y", strtotime($this->doc_date));	
			}//getDocDate
			
			function getPickNo()
			{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("conf","conf_doc_no");
				
				$sql_conf = "SELECT * FROM conf_doc_no";				
				$arr_conf=$this->db->fetchAll($sql_conf);		
				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
				$sql_docno = "SELECT * FROM com_doc_no WHERE doc_tp LIKE 'PL'"; 
				$arr_docno=$this->db->fetchAll($sql_docno);						
				
				$run_val = $arr_conf[0]["doc_prefix2"].$arr_docno[0]["doc_no"];
				$run_val = substr($run_val, -$arr_conf[0]["run_no"]);
				
				return $arr_conf[0]["doc_prefix1"].$arr_docno[0]["doc_tp"]."-".$arr_conf[0]["def_value"] ."-".$run_val;		
			}//getPickNo
				
			function setPickTemp($txt_pl,$txt_plno,$tmp_seq,$txt_cbox,$txt_remark,$status,$slt_promo_st,$point1,$point2,$redeem_point,$total_point,$box_amount)
			{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn1","trn_tdiary1_iq");
				$time = date("H:i:s");
				
				$txt_plno = str_replace("'","",$txt_plno);
				
				$sql_all_pick = "SELECT * FROM trn_tdiary1_iq where corporation_id='".$this->m_corporation_id."' and
												company_id='".$this->m_company_id."' and
												branch_id='".$this->m_branch_id."' and
												doc_date='".$this->doc_date."' and
												doc_no='".$txt_plno."' and
												doc_tp='PL'";
				$arr_conf=$this->db->fetchAll($sql_all_pick);		
				$numrows = count($arr_conf);
				//echo $numrows;
				if($numrows <1)
				{
					
					$sql_set_pick="INSERT INTO  trn_tdiary1_iq
												SET
													corporation_id='".$this->m_corporation_id."',
													company_id='".$this->m_company_id."',
													branch_id='".$this->m_branch_id."',
													doc_date='".$this->doc_date."',
													doc_time='".$time."',
													doc_no='".$txt_plno."',
													doc_tp='PL',
													doc_remark='".$txt_remark."',
													point1='".$point1."',
													point2='".$point2."',
													redeem_point='$redeem_point',
													total_point='".$total_point."'
												";
					
					//return $sql_set_pick;
					$this->db->query($sql_set_pick);	
				}
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_tdiary2_iq");
				
				$sql_chk_dup = "select * from trn_tdiary2_iq where product_id='".$txt_pl."'";
				$arr_chk_dup=$this->db->fetchAll($sql_chk_dup);		
				$numrows_chk_dup = count($arr_chk_dup);
				//echo $numrows;
				/*if($numrows_chk_dup <1)
				{*/
					$sql_set_pick="INSERT INTO  trn_tdiary2_iq
											SET
												corporation_id='".$this->m_corporation_id."',
												company_id='".$this->m_company_id."',
												branch_id='".$this->m_branch_id."',
												doc_date='".$this->doc_date."',
												doc_time='".$time."',
												doc_no='".$txt_plno."',
												doc_tp='PL',
												seq='".$tmp_seq."',
												product_id='".$txt_pl."',
												product_status='".$status."',
												quantity='".$txt_cbox."',
												amount='".$box_amount."',
												promo_st='".$slt_promo_st."'";			
					$this->db->query($sql_set_pick);	
					return "1";
				/*}//chk_dup.
				else
				{
					return "0";
				}*/
			}//setPickTemp
			
			function delPickList($items){
				/**
				 * @desc : delete item product from trn_tdiary2_sl for bill reward
				 *           : delete by promotion sequence not by item
				 * @param String $items : value of field promo_seq to remove from table trn_tdiary2_sl
				 * @return void
				 */
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_iq'); 
				//$arr_items=explode(",",$items);
				if(empty($items)) return '-1';
				
				$sql_del_pick = "DELETE FROM trn_tdiary2_iq where id='".$items."'";
				$this->db->query($sql_del_pick);
				
				$sql = "SELECT * FROM  trn_tdiary2_iq";				
				$arr_list=$this->db->fetchAll($sql);		
				$count = 1;
				foreach($arr_list as $list)
				{
					$repoint = $count;
					$sql_edit_seq = "UPDATE trn_tdiary2_iq SET seq = '".$count++."' WHERE id ='".$list["id"]."'";
					$this->db->query($sql_edit_seq);				
				}
				return $repoint;
			}//delPick
			
			function delBox(){
				/**
				 * @desc : delete item product from trn_tdiary2_sl for bill reward
				 *           : delete by promotion sequence not by item
				 * @param String $items : value of field promo_seq to remove from table trn_tdiary2_sl
				 * @return void
				 */
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_iq'); 
				//$arr_items=explode(",",$items);
				
				$sql_del_pick = "DELETE FROM trn_tdiary2_iq where product_id LIKE 'BOX001'";
				$this->db->query($sql_del_pick);
				//echo $sql_del_pick;
				$sql = "SELECT * FROM  trn_tdiary2_iq";				
				$arr_list=$this->db->fetchAll($sql);		
				$count = 1;
				foreach($arr_list as $list)
				{
					$repoint = $count;
					$sql_edit_seq = "UPDATE trn_tdiary2_iq SET seq = '".$count++."' WHERE id ='".$list["id"]."'";
					$this->db->query($sql_edit_seq);				
				}
			}//delBox
			
			function getCountPick($doc_no){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_iq'); 
				$sql_dist_pick = "SELECT DISTINCT product_id FROM trn_tdiary2_iq where doc_no='".$doc_no."'";
				$all_dist = $this->db->fetchAll($sql_dist_pick);
				$count_dist = count($all_dist);
				
				$sql_all_pick = "SELECT product_id FROM trn_tdiary2_iq where doc_no='".$doc_no."'";
				$all_pick = $this->db->fetchAll($sql_all_pick);
				$count_all = count($all_pick);
				
				$sql_g = "SELECT product_id FROM trn_tdiary2_iq where doc_no='".$doc_no."' and product_status='G'";
				$all_g = $this->db->fetchAll($sql_g);
				$count_g = count($all_g);
				
				if($count_g > 0)
				{
					$count_g = 1;
				}
				else
				{
					$count_g = 0;
				}
				
				return $count_dist."//".$count_all."//".$count_g;
			}//getCountPick
			
			function clearTmp($txt_plno){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary2_iq'); 
				$sql_del_pick = "DELETE FROM trn_tdiary2_iq where doc_no='".$txt_plno."'";
				$this->db->query($sql_del_pick);
				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline('trn','trn_tdiary1_iq'); 
				$sql_del_pick = "DELETE FROM trn_tdiary1_iq where doc_no='".$txt_plno."'";
				$this->db->query($sql_del_pick);
			}//clearTmp			
			
			function savePicking($emp){
				//-------------------------------------------------------------------------------------------- add trn_diary1_iq
				$rtime = date("H:i:s");
				$rdate = date("Y-m-d");
				$txt_plno = "";
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn1","trn_tdiary1_iq");				
				$sql_tmp1 = "SELECT * FROM trn_tdiary1_iq where corporation_id='".$this->m_corporation_id."' and
												company_id='".$this->m_company_id."' and
												branch_id='".$this->m_branch_id."' and
												doc_date='".$this->doc_date."' and
												doc_tp='PL'";
				$arr_tmp1=$this->db->fetchAll($sql_tmp1);		
				$numrows = count($arr_tmp1);
				//echo $numrows;
				if($numrows > 0)
				{
					foreach($arr_tmp1 as $tmp)
					{
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn1","trn_diary1_iq");				
						$sql_chk_tmp1 = "SELECT * FROM trn_diary1_iq where corporation_id='".$this->m_corporation_id."' and
														company_id='".$this->m_company_id."' and
														branch_id='".$this->m_branch_id."' and
														doc_date='".$this->doc_date."' and
														doc_no='".$tmp['doc_no']."' and
														doc_tp='PL'";	
						$arr_chk_tmp1=$this->db->fetchAll($sql_chk_tmp1);		
						$numrows_chk = count($arr_chk_tmp1);	
						if($numrows_chk <1)
						{
							$sql_d1="INSERT INTO  trn_diary1_iq SET
										corporation_id='".$this->m_corporation_id."',
										company_id='".$this->m_company_id."',
										branch_id='".$this->m_branch_id."',
										doc_date='".$this->doc_date."',
										doc_time='".$tmp['doc_time']."',
										doc_no='".$tmp['doc_no']."',
										doc_tp='PL',
										reg_date='".$rdate."',
										reg_time='".$rtime."',
										reg_user='".$emp."',
										doc_remark='".$tmp['doc_remark']."',
										point1='".$tmp['point1']."',
										point2='".$tmp['point2']."',
										redeem_point='".$tmp['redeem_point']."',
										total_point='".$tmp['total_point']."'
										";	
							//echo $sql_d1;
							$this->db->query($sql_d1);
							$txt_plno = $tmp['doc_no'];
						}//if($numrows_chk <1)
					}//foreach($arr_tmp1 as $tmp)
				}//if($numrows <1)
				
				//-------------------------------------------------------------------------------------------- add trn_diary2_iq
				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn1","trn_tdiary2_iq");				
				$sql_tmp2 = "SELECT * FROM trn_tdiary2_iq where corporation_id='".$this->m_corporation_id."' and
												company_id='".$this->m_company_id."' and
												branch_id='".$this->m_branch_id."' and
												doc_date='".$this->doc_date."' and
												doc_tp='PL'";
				
				$arr_tmp2=$this->db->fetchAll($sql_tmp2);		
				$numrows = count($arr_tmp2);
				//return $numrows;		
				if($numrows > 0)
				{
					$count=0;
					foreach($arr_tmp2 as $tmp)
					{
						$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn1","trn_diary2_iq");				
						$sql_chk_tmp2 = "SELECT * FROM trn_diary2_iq where corporation_id='".$this->m_corporation_id."' and
														company_id='".$this->m_company_id."' and
														branch_id='".$this->m_branch_id."' and
														doc_date='".$this->doc_date."' and
														doc_no='".$tmp['doc_no']."' and
														product_id='".$tmp['product_id']."' and
														doc_tp='PL'";							
						$arr_chk_tmp2=$this->db->fetchAll($sql_chk_tmp2);		
						$numrows_chk = count($arr_chk_tmp2);	
						//return $numrows_chk."--".$sql_chk_tmp2;
						if($numrows_chk <1)
						{
							$count++;
							$sql_d2="INSERT INTO  trn_diary2_iq SET
												corporation_id='".$this->m_corporation_id."',
												company_id='".$this->m_company_id."',
												branch_id='".$this->m_branch_id."',
												doc_date='".$this->doc_date."',
												doc_time='".$tmp['doc_no']."',
												doc_no='".$tmp['doc_no']."',
												doc_tp='PL',
												seq='".$tmp['seq']."',
												amount='".$tmp['amount']."',
												reg_date='".$rdate."',
												reg_time='".$rtime."',
												reg_user='".$emp."',
												product_id='".$tmp['product_id']."',
												promo_st='".$tmp['promo_st']."',
												product_status='".$tmp['product_status']."',
												quantity='".$tmp['quantity']."'";				
							//return $sql_set_pick;
							$this->db->query($sql_d2);		
							$txt_plno = $tmp['doc_no'];
						}//if($numrows_chk <1)
					}//foreach($arr_tmp1 as $tmp)
				}//if($numrows <1)
				//return $sql_chk_tmp2."--".$count;
				
				//-------------------------------------------------------------------------------------------- update doc_no
				
				if($txt_plno!="")
				{
					$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
					$sql_docno = "SELECT * FROM com_doc_no WHERE doc_tp LIKE 'PL'"; 
					$arr_docno=$this->db->fetchAll($sql_docno);	
					
					$doc_no = intval($arr_docno[0]["doc_no"])+1;
					
					$sql_update_dno = "UPDATE com_doc_no SET doc_no  = '".$doc_no."' WHERE 
														corporation_id='".$this->m_corporation_id."' and
														company_id='".$this->m_company_id."' and
														branch_id='".$this->m_branch_id."' and
														doc_tp='PL'";		
					$this->db->query($sql_update_dno);	
				}		
				return $txt_plno;
				
			}//savePicking
			
			function reportPick($doc_no)
			{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_iq");
				$sql = "select d1.doc_no , d1.branch_id , d1.doc_remark ,d1.doc_date , d1.doc_time , d1.reg_user , d2.product_id , d2.product_status , d2.quantity , d2.promo_st , d2.amount   from trn_diary1_iq d1 , trn_diary2_iq d2 where d1.doc_no='".$doc_no."' and d1.doc_no=d2.doc_no";
				$arr_data=$this->db->fetchAll($sql);	
				
				if($this->m_com_ip=='127.0.0.1'){
					$arr_data[0]['print2ip']='LOCAL';				
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
						$arr_data[0]['print2ip']=$this->m_com_ip;
					}else{
						$arr_data[0]['print2ip']='LOCAL';	
					}
				}
				
				return $arr_data;
			}//reportPick
			
			function getBranchData($branch_id)
			{
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_branch_detail");
				$sql = "select branch_name from com_branch_detail where branch_id='".$branch_id."'";
				$arr_data=$this->db->fetchOne($sql);	
				return $arr_data;
			}//getBranchData
			
			function setReprintNo($doc_no){
				/**
				 * @desc
				 * @return
				 */
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_iq");
				$sql_upd="UPDATE trn_diary1_iq SET print_no=print_no + 1 WHERE doc_no='$doc_no'";
				$this->db->query($sql_upd);
			}//func
			
			function getReprintNo($doc_no){
				/**
				 * @desc
				 * @return
				 */
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_iq");
				$sql_print_no="SELECT print_no FROM trn_diary1_iq WHERE doc_no='$doc_no'";
				$row_print_no=$this->db->fetchAll($sql_print_no);
				return $row_print_no[0]['print_no'];
			}//func

			function pickinglistReport($doc_no_start,$doc_no_stop){
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_diary1_iq");
				$where="";
				if($doc_no_start!='' && $doc_no_stop!=''){
					$where.=" AND doc_no BETWEEN '$doc_no_start' AND '$doc_no_stop'";
				}else if($doc_no_start!=''){
					$where.=" AND doc_no='$doc_no_start'";
				}else if($doc_no_stop!=''){
					$where.=" AND doc_no='$doc_no_stop'";
				}
				$sql_docno="SELECT 
										doc_no,doc_date,doc_tp,branch_id,doc_time,doc_remark,reg_user
									FROM trn_diary1_iq 
									WHERE 
										corporation_id='$this->m_corporation_id' AND
										company_id='$this->m_company_id' AND
										branch_id='$this->m_branch_id' $where ORDER BY doc_no ASC";
							
				$row_docno=$this->db->fetchAll($sql_docno);
				$arr_docno=array();
				if(count($row_docno)>0)
				{
					$i=0;
					foreach($row_docno as $data)
					{
						$arr_docno[$i]['doc_tp']=$data['doc_tp'];
						$arr_docno[$i]['doc_no']=$data['doc_no'];
						
						$arr_docno[$i]['thermal_printer']=$this->m_thermal_printer;
						$arr_docno[$i]['computer_no']=$this->m_computer_no;
					
					
						$objPos=new Model_PosGlobal();
						$result=$objPos->getSaleman($data["reg_user"]);		
						//$result=$result[0]['employee_id']."#".$result[0]['name']."#".$result[0]['surname']."#".$result[0]['check_status'];
						if($result[0]['name']=="")
						{
							$result[0]['name']=$data["reg_user"];
							$result[0]['surname']=$data["reg_user"];
						}
						$data_emp = $result[0]['name']." ".$result[0]['surname'];	
						$branch_name = $this->getBranchData($data['branch_id']);
						echo "<p align='center'>
								<table width='100%' border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>								
								<tr height='35'>
									<td colspan='6' align='center'><img src='/sales/img/op/op-logo.gif'></td>
								</tr>";
						echo "<tr><td colspan='6' align='center'>ใบรับ Picking List<td></tr>";
						echo "<tr>
									<td colspan='3' align='left'>เลขที่ ".$data['doc_no']."</td>
									<td colspan='3' align='right'>วันที่ ".$data['doc_date']."</td>
								</tr>";
						echo "<tr>
									<td colspan='3' align='left'>สาขา ".$branch_name."</td>
									<td colspan='3' align='right'>เวลา  ".$data['doc_time']."</td>
								</tr>";
						echo "<tr><td colspan='6' align='center' style='padding:10px;'>";	
							
								echo "<table width='90%' border='1'><tr>
											<td width='10%' align='center'>#</td>
											<td width='40%' align='center'>Picking No</td>
											<td width='15%' align='center'>จำนวน</td>
											<td width='15%' align='center'>หมายเหตุ</td></tr>";								
								
								$sql_plist = "select  product_id , product_status , quantity  from trn_diary2_iq where doc_no='".$data['doc_no']."'";
								$arr_plist=$this->db->fetchAll($sql_plist);
								$c_plist = 1;
								foreach($arr_plist as $data_p)
								{
									echo "<tr><td align='center'>".$c_plist++."</td>";
									echo "<td align='left'>".$data_p["product_id"]."</td>";
									echo "<td align='right'>".number_format($data_p["quantity"])."</td>";
									echo "<td align='center'>".$data_p["product_status"]."</td>";									
									echo "</tr>";
								}
								
															
								echo "</table>";							
						
						echo "</td></tr>";
						echo "<tr><td colspan='6'>G : รหัสบาร์โค้ดสรุป Picking List / P : Picking List ที่ไม่มีสินค้าส่งมา</td></tr>";
						if($data['doc_remark'] != "")
						{
							echo "<tr><td colspan='6'>หมายเหตุ : ".$data['doc_remark']."</td></tr>";
						}
						echo "<tr><td colspan='6' height='35'></td></tr>";
						
						echo "<tr><td align='center'>ผู้ออกเอกสาร </td>";
						echo "<td align='center' colspan='2'>__________________________ </td>";
						echo "<td align='right'>ผู้รับเอกสาร </td>";
						echo "<td align='center' colspan='2'>__________________________ </td>";
						echo "</tr>";						
						
						echo "<tr><td>  </td>";
						echo "<td colspan='2' align='center'>( ".$data_emp." )</td>";
						echo "<td> </td>";
						echo "<td colspan='2' align='center'>( <span style='margin-left:180px;'></span> )</td>";
						echo "</tr>";
						echo "</table>";
						
						$i++;
					}//foreach($row_docno as $data)
					
					$json_docno=json_encode($arr_docno);
					echo "<input type='hidden' id='str_docno' name='str_docno' value='$json_docno'></input>";
					
				}//if(count($row_docno)>0)
				else{
					return false;
				}
				
			}//function 
			
			function getOldBox($doc_no)
			{
				$arr_doc_no = explode("-",$doc_no);
				$runno_now = intval($arr_doc_no[2]);
				$runno_old = $runno_now-1;
				
				$sql_conf = "SELECT * FROM conf_doc_no";				
				$arr_conf=$this->db->fetchAll($sql_conf);		
				
				$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("com","com_doc_no");
				$sql_docno = "SELECT * FROM com_doc_no WHERE doc_tp LIKE 'PL'"; 
				$arr_docno=$this->db->fetchAll($sql_docno);						
				
				$run_val = $arr_conf[0]["doc_prefix2"]."".$runno_old;
				$run_val = substr($run_val, -$arr_conf[0]["run_no"]);
				
				$doc_no_old = $arr_doc_no[0]."-".$arr_doc_no[1]."-".$run_val;
				
				$sql_get_box = "SELECT total_point
							FROM `trn_diary1_iq`
							WHERE doc_no = '$doc_no_old'";
				$arr_box=$this->db->fetchOne($sql_get_box);			
				if(count($arr_box) < 1) $arr_box=0;
				return intval($arr_box);
			}
		}//class		
?>