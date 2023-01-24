<?php 
	class Receivedb{
		public function head_box(){
			$registry = Zend_Registry::getInstance ();
			$DB = $registry ['DB'];
			// check company branch
			$select = new Zend_Db_Select($DB);
			$statement = $select->from("com_branch")
								->where("active=?",1);
			$rowsb		=$DB->FetchRow($statement);
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
						ORDER BY t1.doc_no desc";
			$res = $DB->FetchAll($sql);
			return $res;
		}
		
		public function head_box_reg(){
			$registry = Zend_Registry::getInstance ();
			$DB = $registry ['DB'];
			// check company branch
			$select = new Zend_Db_Select($DB);
			$statement = $select->from("com_branch")
			->where("active=?",1);
			$rowsb		=$DB->FetchRow($statement);
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
			$res = $DB->FetchAll($sql);
			return $res;
		}
		
		public function head_box_reg_show(){
			$registry = Zend_Registry::getInstance ();
			$DB = $registry ['DB'];
			// check company branch
			$select = new Zend_Db_Select($DB);
			$statement = $select->from("com_branch")
			->where("active=?",1);
			$rowsb		=$DB->FetchRow($statement);
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
			$res = $DB->FetchAll($sql);
			$str = "";
		foreach($res as $index){ 
	        $str .='<tr>
	        	<td>#</td>
	            <td>'.$index['company_id'].'</td>
	            <td>'.$index['branch_id'].'</td>
	            <td>'.$index['doc_no'].'</td>
	            <td>'.$index['refer_doc_no'].'</td>
	            <td>'.number_format($index['quantity'], 2, '.', '').'</td>
	            <td><!--<i class="icon-edit" title="เพิ่มข้อมูล" onclick="showdetail(\''.$index['id'].'\');"></i>-->
	            <a href="#myModal_'.$index['id'].'" role="button" class="btn" data-toggle="modal">
            		<i class="icon-edit" title="เพิ่มข้อมูล" onclick="showdetail(\''.$index['id_dr2'].'\');"></i>
           		 </a>
           		  <a href="#" role="button" class="btn" data-toggle="modal" onclick="print_data(\''.$index['id_dr2'].'\');">
            <i class="icon-print" title="พิมพ์"></i>
            </a>
	            </td>
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
	        </tr>';
  			 } 
  			 echo $str;
		}
		
	  public function add_box($id,$val){
	  	$registry = Zend_Registry::getInstance ();
	  	$DB = $registry ['DB'];
	  	// check company branch
	  	$select2 = new Zend_Db_Select($DB);
	  	$sql = "select * from trn_diary1_iq where id='$id'";
	  	$rowsb		=$DB->FetchRow($sql);
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
	  	$res =$DB->insert("trn_diary2_iq",$data);
	  	if($res) return "y";
	  	else return "n";
 	  }
 	  
	  
	public function get_seq($doc_no){
		$registry = Zend_Registry::getInstance ();
	  	$DB = $registry ['DB'];
	  	// check company branch
	  	$select2 = new Zend_Db_Select($DB);
 		$sql = "select seq from trn_diary2_iq 
 				where doc_tp='PL' 
 				AND doc_no = '$doc_no'
 				ORDER BY seq DESC limit 1";
 		$res = $DB->FetchRow($sql);
 		if(count($res) > 0){
 			return $res['seq']+1;
 		}else{
 			return "1";
 		}
 		
 	}
 	
}
?>