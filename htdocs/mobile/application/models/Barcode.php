<?php
 class Barcode{
 	var $arr=array();
 	var $status = '0';
 	public function get_doc_no($barcode=""){
 		$registry = Zend_Registry::getInstance ();
 		$DB = $registry ['DB'];
 		//=== com doc no
 		$DB->beginTransaction();
		try {	
				$select = new Zend_Db_Select($DB);
		 		$columns = array('doc_no');
		 		$statement = $select->from('com_doc_no',$columns)
		 							->where("doc_tp=?", 'PL');
		 		$rows = $DB->FetchRow($statement);
		 		$num = $rows['doc_no'];
		 		$num++;
		 		//== update doc no
		 		$conditions[] = "doc_tp = 'PL'";
		 		$up = array(
		 					"doc_no"=>$num,
		 					"upd_date"=>new Zend_Db_Expr("Now()"),
		 					"upd_time"=>new Zend_Db_Expr("Now()")
		 				);
		 		$resup = $DB->update("com_doc_no",$up,$conditions);
		 		
		 		//== get branch
		 		$select2 = new Zend_Db_Select($DB);
		 		$statement = $select2->from("com_branch")
		 							->where("active=?",1);
		 		$rowsb		=$DB->FetchRow($statement);
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
		 		$DB->insert("trn_diary1_iq",$data);
		 		
		 		// insert detail record 1
		 		if(substr($barcode, 0, 3) == "LGS" && (strlen($barcode) == 16)){
			 		$product_id  = substr($barcode, 0, 10);
			 		$qty = $tmp  = substr($barcode, 10);
	 				$qty  = (int)substr($tmp,3);
		 		}else{
		 			$product_id = $barcode;
		 			$qty = 0;
		 		}
		 		$data2 = array(
		 				"corporation_id"=>$corporate,
		 				"company_id"=>$company,
		 				"branch_id"=>$branch,
		 				"doc_date"=>new Zend_Db_Expr("Now()"),
		 				"doc_time"=>new Zend_Db_Expr("Now()"),
		 				"doc_no"=>$doc_no,
		 				"seq"=>$this->get_seq($doc_no),
		 				"doc_tp"=>'PL',
		 				"product_id"=>$product_id,
		 				"quantity"=>0,
		 				);
		 		$DB->insert("trn_diary2_iq",$data2);
		 		
		 		//insert record2
		 		$data3 = array(
		 				"corporation_id"=>$corporate,
		 				"company_id"=>$company,
		 				"branch_id"=>$branch,
		 				"doc_date"=>new Zend_Db_Expr("Now()"),
		 				"doc_time"=>new Zend_Db_Expr("Now()"),
		 				"doc_no"=>$doc_no,
		 				"seq"=>$this->get_seq($doc_no),
		 				"doc_tp"=>'PL',
		 				"product_id"=>'BOX001',
		 				"quantity"=>$qty,
		 		);
		 		$DB->insert("trn_diary2_iq",$data3);
		 		
		 		$DB->commit();
 				$this->status = '1';
 		}catch (Zend_Db_Exception $e){
 			$DB->rollBack();
 			$this->status = '0';
 		}
 		return array($this->status,$doc_no);
 	}
 	
 	private function gen_head($company="",$branch=""){
 		$registry = Zend_Registry::getInstance ();
 		$DB = $registry ['DB'];
 		$str = "";
 		$sql = "select doc_no 
 					from trn_diary1_iq 
 					where doc_tp = 'PL'
 					ORDER BY id desc limit 1";
 		$res = $DB->FetchRow($sql);
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
 		$registry = Zend_Registry::getInstance ();
 		$DB = $registry ['DB'];
 		//=== com doc no
 		$DB->beginTransaction();
 		try {
 			$select = new Zend_Db_Select($DB);
 			$columns = array('doc_no');
 			$statement = $select->from('com_doc_no',$columns)
 			->where("doc_tp=?", 'PL');
 			$rows = $DB->FetchRow($statement);
 			$num = $rows['doc_no'];
 			$num++;
 			//== update doc no
 			$conditions[] = "doc_tp = 'PL'";
 			$up = array(
 					"doc_no"=>$num,
 					"upd_date"=>new Zend_Db_Expr("Now()"),
 					"upd_time"=>new Zend_Db_Expr("Now()")
 			);
 			$resup = $DB->update("com_doc_no",$up,$conditions);
 			 
 			//== get branch
 			$select2 = new Zend_Db_Select($DB);
 			$statement = $select2->from("com_branch")
 			->where("active=?",1);
 			$rowsb		=$DB->FetchRow($statement);
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
 			$DB->insert("trn_diary1_iq",$data);
 			 
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
 			$DB->insert("trn_diary2_iq",$data2);
 			 */
 			//insert record2
 			$data3 = array(
 					"corporation_id"=>$corporate,
 					"company_id"=>$company,
 					"branch_id"=>$branch,
 					"doc_date"=>new Zend_Db_Expr("Now()"),
 					"doc_time"=>new Zend_Db_Expr("Now()"),
 					"doc_no"=>$doc_no,
 					"seq"=>$this->get_seq($doc_no),
 					"doc_tp"=>'PL',
 					"product_id"=>'BOX002',
 					"quantity"=>$qty,
 			);
 			$DB->insert("trn_diary2_iq",$data3);
 			 
 			$DB->commit();
 			$this->status = 'y';
 		}catch (Zend_Db_Exception $e){
 			$DB->rollBack();
 			$this->status = 'n';
 		}
 		return $this->status;
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
