<?php
 class Model_Barcode extends Model_PosGlobal{
 	var $arr=array();
 	var $status = false;
 	public function get_doc_no($barcode=""){
 		//$registry = Zend_Registry::getInstance ();
 		//$DB = $registry ['DB'];
 		//=== com doc no
 		$db = $this->db->beginTransaction();
		try {	
				$select = new Zend_Db_Select($this->db);
		 		$columns = array('doc_no');
		 		$statement = $select->from('com_doc_no',$columns)
		 							->where("doc_tp=?", 'PL');
		 		$rows = $this->db->FetchRow($statement);
		 		$num = $rows['doc_no'];
		 		$num++;
		 		//== update doc no
		 		$conditions[] = "doc_tp = 'PL'";
		 		$up = array(
		 					"doc_no"=>$num,
		 					"upd_date"=>new Zend_Db_Expr("Now()"),
		 					"upd_time"=>new Zend_Db_Expr("Now()")
		 				);
		 		$resup = $this->db->update("com_doc_no",$up,$conditions);
		 		
		 		//== get branch
		 		$select2 = new Zend_Db_Select($this->db);
		 		$statement = $select2->from("com_branch")
		 							->where("active=?",1);
		 		$rowsb		=$this->db->FetchRow($statement);
		 		$corporate = $rowsb['corporation_id'];
		 		$company	=	$rowsb['company_id'];
		 		$branch		=	$rowsb['branch_id'];
		 		// gen doc_no
		 		if(substr($barcode, 0, 3) == "LGS" && (strlen($barcode) == 16)){
		 			$total_box = $this->bar_code_qty($barcode);
		 		}else{
		 			$total_box = 0;
		 		}
		 		$doc_no = $this->gen_head($company,$branch);
		 		$data = array(
		 				"corporation_id"=>$corporate,
		 				"company_id"=>$company,
		 				"branch_id"=>$branch,
		 				"doc_date"=>new Zend_Db_Expr("Now()"),
		 				"doc_time"=>new Zend_Db_Expr("Now()"),
		 				"doc_no"=>$doc_no,
		 				"doc_tp"=>'PL',
		 				"refer_doc_no"=>$barcode,
		 				"quantity"=>$total_box
		 				);
		 		$this->db->insert("trn_diary1_iq",$data);
		 		
		 		// insert detail record 1
		 		if(substr($barcode, 0, 3) == "LGS" && (strlen($barcode) == 16)){
			 		$product_id  = substr($barcode, 0, 10);
			 		$qty = $tmp  = substr($barcode, 10);
	 				$qty  = (int)substr($tmp,3);
		 		}else{
		 			$product_id = $barcode;
		 			$qty = 0;
		 		}
		 		//$seq = "seq=".$this->get_seq($doc_no);
		 		$data2 = array(
		 				"corporation_id"=>$corporate,
		 				"company_id"=>$company,
		 				"branch_id"=>$branch,
		 				"doc_date"=>new Zend_Db_Expr("Now()"),
		 				"doc_time"=>new Zend_Db_Expr("Now()"),
		 				"doc_no"=>$doc_no,
		 				"doc_tp"=>'PL',
		 				"seq"=>$this->get_seq($doc_no),
		 				"product_id"=>$product_id,
		 				"quantity"=>0,
		 				);
		 		$this->db->insert("trn_diary2_iq",$data2);
		 		
		 		//insert record2
		 		$data3 = array(
		 				"corporation_id"=>$corporate,
		 				"company_id"=>$company,
		 				"branch_id"=>$branch,
		 				"doc_date"=>new Zend_Db_Expr("Now()"),
		 				"doc_time"=>new Zend_Db_Expr("Now()"),
		 				"doc_no"=>$doc_no,
		 				"doc_tp"=>'PL',
		 				"seq"=>$this->get_seq($doc_no),
		 				"product_id"=>'BOX001',
		 				"quantity"=>$qty,
		 		);
		 		$this->db->insert("trn_diary2_iq",$data3);
		 		
		 		$this->db->commit();
 				$this->status = true;
 		}catch (Zend_Db_Exception $e){
 			$this->db->rollBack();
 			$this->status = false;
 		}
 		return $this->status;
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
 	
 	private function gen_head($company="",$branch=""){
 		//$registry = Zend_Registry::getInstance ();
 		//$this->db = $registry ['DB'];
 		$str = "";
 		$sql = "select doc_no 
 					from trn_diary1_iq 
 					where doc_tp = 'PL'
 					ORDER BY id desc limit 1";
 		$res = $this->db->FetchRow($sql);
 		$str =substr($company,0,2);
 		$str =$str.$branch."PL-01-";
 		$tmp =(int)substr($res['doc_no'], -8);
 		$tmp +=1;
 		$pad= str_pad($tmp,8,"0",STR_PAD_LEFT);
 		$str = $str.$pad;
 		return $str;
 	}
 	
 	private function bar_code_qty($code){
 		$tmp  = substr($code, 10);
 		$tmp  = (int)substr($tmp, 0,3);
 		return $tmp;
 	} 
 	
 	public function gen_doc_no_rep($barcode="",$qty=""){
 		//$registry = Zend_Registry::getInstance ();
 		//$DB = $registry ['DB'];
 		//=== com doc no
 		$this->db->beginTransaction();
 		try {
 			$select = new Zend_Db_Select($this->db);
 			$columns = array('doc_no');
 			$statement = $select->from('com_doc_no',$columns)
 			->where("doc_tp=?", 'PL');
 			$rows = $this->db->FetchRow($statement);
 			$num = $rows['doc_no'];
 			$num++;
 			//== update doc no
 			$conditions[] = "doc_tp = 'PL'";
 			$up = array(
 					"doc_no"=>$num,
 					"upd_date"=>new Zend_Db_Expr("Now()"),
 					"upd_time"=>new Zend_Db_Expr("Now()")
 			);
 			$resup = $this->db->update("com_doc_no",$up,$conditions);
 			 
 			//== get branch
 			$select2 = new Zend_Db_Select($this->db);
 			$statement = $select2->from("com_branch")
 			->where("active=?",1);
 			$rowsb		=$this->db->FetchRow($statement);
 			$corporate = $rowsb['corporation_id'];
 			$company	=	$rowsb['company_id'];
 			$branch		=	$rowsb['branch_id'];
 			// gen doc_no
 			/*if(substr($barcode, 0, 3) == "LGS" && (strlen($barcode) == 16)){
 				$total_box = $this->bar_code_qty($barcode);
 			}else{*/
 				$total_box = 0;
 			//}
 			$doc_no = $this->gen_head($company,$branch);
 			$data = array(
 					"corporation_id"=>$corporate,
 					"company_id"=>$company,
 					"branch_id"=>$branch,
 					"doc_date"=>new Zend_Db_Expr("Now()"),
 					"doc_time"=>new Zend_Db_Expr("Now()"),
 					"doc_no"=>$doc_no,
 					"doc_tp"=>'PL',
 					"refer_doc_no"=>$barcode,
 					"quantity"=>$total_box
 			);
 			$this->db->insert("trn_diary1_iq",$data);
 			 
 			// insert detail record 1
 			/*if(substr($barcode, 0, 3) == "LGS" && (strlen($barcode) == 16)){
 				$product_id  = substr($barcode, 0, 10);
 				$qty = $tmp  = substr($barcode, 10);
 				$qty  = (int)substr($tmp,3);
 			}else{*/
 				$product_id = $barcode;
 				//$qty = 0;
 			//}
 			/*$data2 = array(
 					"corporation_id"=>$corporate,
 					"company_id"=>$company,
 					"branch_id"=>$branch,
 					"doc_date"=>new Zend_Db_Expr("Now()"),
 					"doc_time"=>new Zend_Db_Expr("Now()"),
 					"doc_no"=>$doc_no,
 					"doc_tp"=>'PL',
 					"product_id"=>$product_id,
 					"quantity"=>0,
 			);
 			$this->db->insert("trn_diary2_iq",$data2);
 			 */
 			//insert record2
 			$data3 = array(
 					"corporation_id"=>$corporate,
 					"company_id"=>$company,
 					"branch_id"=>$branch,
 					"doc_date"=>new Zend_Db_Expr("Now()"),
 					"doc_time"=>new Zend_Db_Expr("Now()"),
 					"doc_no"=>$doc_no,
 					"doc_tp"=>'PL',
 					"seq"=>$this->get_seq($doc_no),
 					"product_id"=>'BOX002',
 					"quantity"=>$qty,
 			);
 			$this->db->insert("trn_diary2_iq",$data3);
 			 
 			$this->db->commit();
 			$this->status = 'y';
 		}catch (Zend_Db_Exception $e){
 			$this->db->rollBack();
 			$this->status = 'n';
 		}
 		return $this->status;
 	}
 	
 }
?>