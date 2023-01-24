<?php 
	class Model_Receivedb extends Model_PosGlobal{
		public function head_box(){
			//$registry = Zend_Registry::getInstance ();
			//$DB = $registry ['DB'];
			// check company branch
			$select = new Zend_Db_Select($this->db);
			$statement = $select->from("com_branch")
								->where("active=?",1);
			$rowsb		=$this->db->FetchRow($statement);
			$corporate = $rowsb['corporation_id'];
			$company	=	$rowsb['company_id'];
			$branch		=	$rowsb['branch_id'];
			
			$sql = "select t1.id,t1.corporation_id,t1.company_id,t1.branch_id,t1.doc_no,t1.refer_doc_no 
						from trn_diary1_iq t1 inner join trn_diary2_iq t2 on t1.doc_no = t2.doc_no
						where t1.doc_tp ='PL' 
						and t1.company_id='$company' 
						and t1.branch_id='$branch' 
						and t1.refer_doc_no <> ''
						and t2.product_id='BOX001'  
						ORDER BY t1.doc_no desc
						limit 10";
			$res = $this->db->FetchAll($sql);
			return $res;
		}
		
		public function check_key_barcode(){
			$date 	= date('Y-m-d');
			$time	= date("H:i:s");
			$sql = "
				select * 
				from  com_pos_config 
				where 
					code_type ='NO_KEYIN_BARCODE' 
					and start_date <= '$date' and end_date >= '$date'
					and start_time <= '$time' and end_time >= '$time'
					";
			$res = $this->db->FetchAll($sql);
			if(count($res) > 0){
				return 'Y';
			}else{
				return 'N';
			}
			
		}
		
		public function head_box2(){
			$str = "";
			$select = new Zend_Db_Select($this->db);
			$statement = $select->from("com_branch")
			->where("active=?",1);
			$rowsb		=$this->db->FetchRow($statement);
			$corporate = $rowsb['corporation_id'];
			$company	=	$rowsb['company_id'];
			$branch		=	$rowsb['branch_id'];
				
			$sql = "select t1.id,t1.corporation_id,t1.company_id,t1.branch_id,t1.doc_no,t1.refer_doc_no
			from trn_diary1_iq t1 inner join trn_diary2_iq t2 on t1.doc_no = t2.doc_no
			where t1.doc_tp ='PL'
			and t1.company_id='$company'
			and t1.branch_id='$branch'
			and t1.refer_doc_no <> ''
			and t2.product_id='BOX001'
			ORDER BY t1.doc_no desc
			limit 10";
			$res = $this->db->FetchAll($sql);
			foreach ($res as $index){
			$str .='<tr>
						<td>#</td>
						<td>'.$index['company_id'].'</td>
			            <td>'.$index['branch_id'].'</td>
			            <td>'.$index['doc_no'].'</td>
			            <td>'.strtoupper($index['refer_doc_no']).'</td>
			            <td><i class="icon-edit" title="เพิ่มข้อมูล" onclick="showdetail(\''.$index['id'].'\');"></i></td>
			        </tr>';
			  }
			return $str;
		}
		
		public function head_box_reg(){
			//$registry = Zend_Registry::getInstance ();
			//$DB = $registry ['DB'];
			// check company branch
			$select = new Zend_Db_Select($this->db);
			$statement = $select->from("com_branch")
			->where("active=?",1);
			$rowsb		=$this->db->FetchRow($statement);
			$corporate = $rowsb['corporation_id'];
			$company	=	$rowsb['company_id'];
			$branch		=	$rowsb['branch_id'];
				
			$sql = "
			select t1.id,t2.id as id_dr2,
				t2.quantity,t1.corporation_id,
				t1.company_id,t1.branch_id,
				t1.doc_no,t1.refer_doc_no
			from trn_diary1_iq t1 
				inner join trn_diary2_iq t2 on t1.doc_no = t2.doc_no
			where t1.doc_tp ='PL'
				and t1.company_id='$company'
				and t1.branch_id='$branch'
				and t1.refer_doc_no <> ''
				and t2.product_id='BOX002'
			ORDER BY t1.doc_no desc";
			$res = $this->db->FetchAll($sql);
			return $res;
		}
		
		public function head_box_reg_show(){
			//$registry = Zend_Registry::getInstance ();
			//$DB = $registry ['DB'];
			// check company branch
			$select = new Zend_Db_Select($this->db);
			$statement = $select->from("com_branch")
			->where("active=?",1);
			$rowsb		=$this->db->FetchRow($statement);
			$corporate = $rowsb['corporation_id'];
			$company	=	$rowsb['company_id'];
			$branch		=	$rowsb['branch_id'];
			
		 	$sql = "select t1.id,t2.id as id_dr2,t2.quantity,t1.corporation_id,t1.company_id,t1.branch_id,t1.doc_no,t1.refer_doc_no
			from trn_diary1_iq t1 inner join trn_diary2_iq t2 on t1.doc_no = t2.doc_no
			where t1.doc_tp ='PL'
			and t1.company_id='$company'
			and t1.branch_id='$branch'
			and t1.refer_doc_no <> ''
			and t2.product_id='BOX002'
			ORDER BY t1.doc_no desc";
			$res = $this->db->FetchAll($sql);
			$str = "";
		foreach($res as $index){ 
	        $str .='<tr>
	        	<td>#</td>
	            <td>'.$index['company_id'].'</td>
	            <td>'.$index['branch_id'].'</td>
	            <td>'.$index['doc_no'].'</td>
	            <td>'.$index['refer_doc_no'].'</td>
	            <td>'.number_format($index['quantity']).'</td>
	            <td>
	            <a href="#myModal_'.$index['id'].'" role="button" class="btn" data-toggle="modal">
            		<i class="icon-edit" title="แก้ไข" ></i>
           		 </a>
           		  <a href="#" role="button" class="btn" data-toggle="modal" onclick="print_data(\''.$index['id_dr2'].'\');">
            <i class="icon-print" title="พิมพ์"></i>
            </a>
            <div id="myModal_'.$index['id'].'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				    <h5 id="myModalLabel">Refer: <span id="id_bar">'.$index['refer_doc_no'].'</span></h5>
				  </div>
				  <div class="modal-body">
				    <p>
				    <input type="hidden" id="id" value="" />
				   	<label>จำนวน: </label><input type="text" id="id_qty_'.$index['id'].'" value="'.$index['quantity'].'" />
				    </p>
				  </div>
				  <div class="modal-footer">
				    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				     <button class="btn btn-primary" onclick="update_qty(\''.$index['id'].'\',\''.$index['id_dr2'].'\');">Save changes</button>
  </div>
</div>
	            </td>
	             
	        </tr>';
  			 } 
  			 echo $str;
		}
		
	  public function add_box($id,$val){
	  	//$registry = Zend_Registry::getInstance ();
	  	//$DB = $registry ['DB'];
	  	// check company branch
	  	$select2 = new Zend_Db_Select($this->db);
	  	$sql = "select * from trn_diary1_iq where id='$id'";
	  	$rowsb		=$this->db->FetchRow($sql);
	  	$corporate = $rowsb['corporation_id'];
	  	$company	=	$rowsb['company_id'];
	  	$branch		=	$rowsb['branch_id'];
	  	$doc_no	=  $rowsb['doc_no'];
	  	
	  	// insert detail
	  	$data = array(
	  			"corporation_id"=>$corporate,
	  			"company_id"=>$company,
	  			"branch_id"=>$branch,
	  			"doc_date"=>new Zend_Db_Expr("Now()"),
	  			"doc_time"=>new Zend_Db_Expr("Now()"),
	  			"doc_no"=>$doc_no,
	  			"doc_tp"=>'PL',
	  			"seq"=>$this->get_seq($doc_no),
	  			"product_id"=>$val,
	  			"quantity"=>0,
	  	);
	  	$res =$this->db->insert("trn_diary2_iq",$data);
	  	if($res) return "y";
	  	else return "n";
 	  }
 	  
	private function get_seq($doc_no){
 		$sql = "select seq from trn_diary2_iq 
 				where doc_tp='PL' 
 				AND doc_no = '$doc_no'
 				ORDER BY seq DESC limit 1";
 		$res = $this->db->FetchRow($sql);
 		if(count($res) > 0){
 			return $res['seq']+1;
 		}else{
 			return "1";
 		}
 		
 	}
 	  
 	  public function show_table_receive($id=""){
 	  	$str = "";
 	  	$sql 	= "select doc_no,refer_doc_no from trn_diary1_iq where id='$id'";
		$rowsb	=$this->db->FetchRow($sql);
		$doc_no	= $rowsb['doc_no'];
		$ref 	= $rowsb['refer_doc_no'];
		$str 	= '<p><strong>หมายเลข : '.$doc_no.'   ,   รหัสบาร์โค๊ด : '.$ref.'</strong></p>';
		$str   .='<p><label>เพิ่มกล่อง :</label><input  type="text" id="val" value="" onKeyPress="return process(event,\'add\',\''.$id.'\');"></p>';
 	  	return $str;
 	  }
 	  
 	  public function show_table_receve_detail($id = ""){
 	  	$str = "";
 	  	$sql = "select doc_no from trn_diary1_iq where id='$id'";
	  	$rowsb		=$this->db->FetchRow($sql);
	  	 $doc_no	=  $rowsb['doc_no'];
	  	// select detail
	  	$sql = "
			  	select id,product_id,quantity 
			  	from trn_diary2_iq 
			  	where doc_no='$doc_no' and doc_tp='PL'";
	  	$res = $this->db->FetchAll($sql);
	  	$str = "<table class=\"table table-bordered\">
	  				<thead>
	  				<tr>
	  					<th>Picking</th>
	  					<th>Qty</th>
	  					<th>Manage</th>
	  				</tr>
	  				</thead><tbody>";
	  	if(count($res) >0){
		  	foreach ($res as $index){
		  		$str .="<tr>";
		  		$str .='<td> '.$index['product_id'].'</td><td>'.number_format($index['quantity']).'</td>
		  		<td>
		  			<a href="#myModal" role="button" class="btn" data-toggle="modal" onclick="show_qty(\''.$index['id'].'\',\''.$index['product_id'].'\',\''.number_format($index['quantity']).'\');">
		  			<i class="icon-edit"></i></a>
		  		</td>';
		  		$str .="</tr>";
		  	}
	  	}
	  	$str .='</tbody></table>';
	  	return $str;
 	  }
 	  
 	  public function update_receive_detail($qty,$id){
 	  	$sql = "update trn_diary2_iq set quantity='$qty' where id='$id'";
			$res = $this->db->query($sql);
			if($res){
				echo "y";
			}else{
				echo "n";
			}
 	  }
 	  
 	  public function show_pdf($id=""){
 	  		$str = "";
 	  		$sql = "
 	  			select t1.id,t2.id as id_dr2,
 	  				t2.quantity,t1.corporation_id,
 	  				t1.company_id,t1.branch_id,
 	  				t1.doc_no,t1.refer_doc_no,
 	  				t3.branch_name,t2.doc_date,t2.doc_time
			  	from trn_diary1_iq t1 
			  		inner join trn_diary2_iq t2 on t1.doc_no = t2.doc_no
			  		inner join com_branch_detail t3 on t3.branch_id= t1.branch_id
			  	where t2.id='$id'";
			  	$res = $this->db->FetchRow($sql);
			  	$str = '
			  	
			  	<table border="0" width="90%" cellpadding="0" cellspacing="0">
			  	
			  	<tbody>
			  	<tr><td colspan="2" align="center"><img src="'.LOGO_PATH.'/op-logo.gif"></td></tr>
			  	<tr><td colspan="2" align="center">ใบคืนกล่อง</td></tr>
			  	<tr>
			  	<td><B>เลขที่.</B> '.$res['doc_no'].'</td>
			  	<td align="right"><B>วันที่</B>  '.$res['doc_date'].'</td>
			  	</tr>
			  	<tr>
			  	<td><B>สาขา</B> '.$res['branch_name'].'</td>
			  	<td align="right"><B>เวลา</B> '.$res['doc_time'].'</td>
			  	</tr>
			  	</tbody>
			  	</table>
			  	<table width="90%">
			  	<tr><td></td></tr>
			  	<tr>
			  	<td >Picking No</td>
			  	<td align="right">Qty</td>
			  	</tr>
			  	
			  	<tr>
			  	<td>'.$res['refer_doc_no'].'</td>
			  	<td align="right">'.number_format($res['quantity'],'2','.','').'</td>
			  	</tr>
			  	</table>
			  	
			  	<table width="90%">
			  	<tr>
			  	<td>ผู้ออกเอกสาร</td>
			  	<td>___________</td>
			  	<td>ผู้รับเอกสาร</td>
			  	<td>___________</td>
			  	</tr>
			  	<tr>
			  	<td colspan="2" align="right">&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;</td>
			  	<td colspan="2" align="left">&nbsp;&nbsp;&nbsp;&nbsp;
			  	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
			  	</tr>
			  	<tr><td colspan="4">&nbsp;</td></tr>
			  	</table>
			  	
			  	';
		 return $str;	  	
 	  }
 	  
 	  public function showreg_edit($id=""){
 	  	$sql = "select doc_no,refer_doc_no from trn_diary1_iq where id='$id'";
		$rowsb		=$this->db->FetchRow($sql);
		$doc_no	= $rowsb['doc_no'];
		$ref 			= $rowsb['refer_doc_no'];
		$str = '<p><strong>หมายเลข : '.$doc_no.'   ,   รหัสบาร์โค๊ด : '.$ref.'</strong></p>';
		return $str;	
 	  }
	}
?>