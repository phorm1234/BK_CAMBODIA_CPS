<?php 
class Model_Checkstock{	
	public $db;
	public $corporation_id;
	public $company_id;
	public $branch_id;
	public $com_no;
	public $user_id;
	public $date;
	public $time;
	public $year_month;
	public $month;
	public $m_month;
	public $year;
	public function __construct(){
		//$this->db=Zend_Registry::get('db1');
		//$this->db=SSUP_Controller_Plugin_Db::conn2db();
		//ทดสอบ
		/*$this->corporation_id='OP';
		$this->company_id='OP';
		$this->vendor_id='OP';
		$this->branch_id='OP7777';
		$this->branch_no='7777';
		$this->com_no='1';
		$this->user_id='004716';*/
		//
		//ใช้จริง 
		$this->db=Zend_Registry::get('db1');
		$this->dbji=Zend_Registry::get('dbji');
		$session = new Zend_Session_Namespace('empprofile');
        $empprofile=$session->empprofile; 
		$this->empprofile=$empprofile;
		$this->corporation_id=$this->empprofile['corporation_id'];
		$this->company_id=$this->empprofile['company_id'];
		$this->branch_id=$this->empprofile['branch_id'];
		$this->branch_no=$this->empprofile['branch_no'];
		$this->com_no=$this->empprofile['computer_no'];
		$this->user_id=$this->empprofile['user_id'];
		$this->date=date('Y-m-d');
		$this->year_month=date('Y-m');
		$this->time=date('H:i:s');
		$this->month=date('n');
		$this->m_month=date('m');
		$this->year=date('Y');
	}
	public function viewshelf($shelf){
		if(!empty($shelf)){
			$out=$this->selectviewshelfmaster($shelf);
		}else{
			$out=$this->viewsallhelf();
		}
		return $out;
	}
	
	public function viewsallhelf(){
		$sql_shelf=$this->db->select()
		    		  	->from('chk_shelf_master',array('shelf_no','flag'))
		    			->group('shelf_no')
		    			->order('flag,seq,group_concat(reg_date,reg_time) DESC');
	    $row=$sql_shelf->query()->fetchAll();
	    if(count($row)==0) return false;
		foreach($row as $data){
	   		 $out[] = array('shelf_no'=>$data['shelf_no'],'floor_no' => $this->viewfloor($data['shelf_no']));
	   	}	
		return $out;
	}
	
	public function selectviewshelf($dat){
		if(!empty($dat)){
			$sql_shelf=$this->db->select()
			    		  	->from('chk_shelf_master',array('shelf_no','flag'))
			    		  	->where('shelf_no IN (?)', $dat)
			    			->group('shelf_no');	
		    $row=$sql_shelf->query()->fetchAll();
		    if(count($row)==0) return false;
			foreach($row as $data){
		   		 $out[] = array('shelf_no'=>$data['shelf_no'],'floor_no' => $this->viewfloor($data['shelf_no']));
		   	}
		   	return $out;
		}else{
			return "n";
		}	
		//return $dat;
	}
	public function selectviewshelfmaster($data){
		if(!empty($data)){
			$sql_shelf=$this->db->select()
			    		  	->from('chk_shelf_master',array('shelf_no','flag'))
			    		  	->where('shelf_no IN (?)', $data)
			    			->order('group_concat(reg_date,reg_time) DESC');	
		    $row=$sql_shelf->query()->fetchAll();
		    if(count($row)==0) return false;
			foreach($row as $data){
		   		 $out[] = array('shelf_no'=>$data['shelf_no'],'floor_no' => $this->viewfloor($data['shelf_no']));
		   	}
		   	return $out;
		}else{
			return "n";
		}	
	}
	
	public function selectviewshelftaglist($data,$doc_no){
		if(!empty($data)){
			$sql_shelf=$this->db->select()
			    		  	->from('chk_shelf_master',array('shelf_no','flag'))
			    		  	->where('shelf_no IN (?)', $data)
			    			->group('shelf_no');
		    $row=$sql_shelf->query()->fetchAll();
		    if(count($row)==0) return false;
			foreach($row as $data){
		   		 $out[] = array('shelf_no'=>$data['shelf_no'],'floor_no' => $this->viewfloortaglist($data['shelf_no'],$doc_no));
		   	}
		   	return $out;
		}else{
			return "n";
		}	
	}
	
	public function countRec($fname,$tname) {
		$sql = "SELECT count($fname) as count FROM $tname ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data[0]['count'];
	}
	
	
	public function selectcountviewshelf($data,$doc_no){
		if(!empty($data)){
			foreach($data as $val){
				$out[] = array('shelf_no'=>$val,'data' => $this->getdatacountlisttag($val,$doc_no));
			}
			return $out;
		}else{
			return "n";
		}
	}
	
	public function getdatacountlisttag($shelf_no,$doc_no){
		$sql_shelf=$this->db->select()
			    		  	->from(array('listtag'=>'chk_list_tag'),array('shelf_no','floor_no','room_no','listtag.seq','product_id','quantity'))
			    		  	->joinLeft(array('product'=>'com_product_master'),'listtag.product_id=product.product_id',array('product.price','product.name_product'))
			    		  	->where('listtag.shelf_no = ?', $shelf_no)
			    			->where('listtag.doc_no = ?', $doc_no);
		    $row=$sql_shelf->query()->fetchAll();
		//return $shelf_no."----".$doc_no;
		return $row;
	}
	
	public function viewfloor($shelf_no){
		$sql_floor=$this->db->select()
		    		  	->from('chk_shelf_master',array('floor_no','flag'))
		    		  	->where('shelf_no=?',$shelf_no)
		    			->group('floor_no')
		    			->order('floor_no DESC');
	    $row=$sql_floor->query()->fetchAll();
	    
		foreach($row as $data){
	   		 $out[] = array('floor_no'=>$data['floor_no'],'room_no' => $this->viewroom($shelf_no,$data['floor_no']));
	   	}	
		return $out;
	}
	
	public function viewfloortaglist($shelf_no,$doc_no){
		$sql_floor=$this->db->select()
		    		  	->from('chk_shelf_master',array('floor_no','flag'))
		    		  	->where('shelf_no=?',$shelf_no)
		    			->group('floor_no')
		    			->order('floor_no DESC');
	    $row=$sql_floor->query()->fetchAll();
	    
		foreach($row as $data){
	   		 $out[] = array('floor_no'=>$data['floor_no'],'room_no' => $this->viewroomtaglist($shelf_no,$data['floor_no'],$doc_no));
	   	}	
		return $out;
	}
	
	public function viewroom($shelf_no,$floor_no){
		$sql_room=$this->db->select()
		    		   ->from('chk_shelf_master',array('room_no','flag'))
		    		   ->where('shelf_no=?',$shelf_no)
		    		   ->where('floor_no=?',$floor_no)
		    		   ->group('room_no')
		    		   ->order('room_no ASC');
	    $row=$sql_room->query()->fetchAll();
	    
		foreach($row as $data){
	   		 $out[] = array('room_no'=>$data['room_no'],'product_id'=>$this->productonshelf($shelf_no,$floor_no,$data['room_no']));
	   	}	
		return $out;
	}
	
	public function viewroomtaglist($shelf_no,$floor_no,$doc_no){
		$sql_room=$this->db->select()
		    		   ->from('chk_list_tag',array('room_no','flag'))
		    		   ->where('shelf_no=?',$shelf_no)
		    		   ->where('floor_no=?',$floor_no)
		    		   ->where('doc_no=?',$doc_no)
		    		   ->group('room_no')
		    		   ->order('room_no ASC');
	    $row=$sql_room->query()->fetchAll();
	    
		foreach($row as $data){
	   		 $out[] = array('room_no'=>$data['room_no'],'product_id'=>$this->productlisttag($shelf_no,$floor_no,$data['room_no'],$doc_no));
	   	}
	   	if(empty($out)){
	   		$out=array();
	   	}
		return $out;
	}
	
	public function checkeditshelf($shelf_no){
		if($shelf_no!="n"){
			$sql=$this->db->select()
		    		  	->from('chk_shelf_master',array('shelf_no','desc','flag'))
		    		  	->where('shelf_no=?',$shelf_no);
	    	$data=$sql->query()->fetchAll();
		}else{
			$data="n";
		}
		return $data;
	}
	
	public function saveshelf($data,$data_detail){
		if($data_detail['shelf_no_up']!="n"){
			$data_insert=array(
						'shelf_no'=>$data['shelf_no'],
						'desc'=>$data['desc'],
						'upd_date'=>date('Y-m-d'),
						'upd_time'=>date('H:i:s'),
						'upd_user'=>$data_detail['user_id']
			);
			$where = array(
						'shelf_no = ?' => $data_detail['shelf_no_up']
			);
			$rs=$this->db->update('chk_shelf_master', $data_insert, $where);
			if($rs){
				$data_insert_product=array(
						'shelf_no'=>$data['shelf_no'],
						'upd_date'=>date('Y-m-d'),
						'upd_time'=>date('H:i:s'),
						'upd_user'=>$data_detail['user_id']
				);
				$where_product = array(
							'shelf_no = ?' => $data_detail['shelf_no_up']
				);
				$rs_product=$this->db->update('chk_shelf_product', $data_insert_product, $where_product);
				if($rs_product){
					$status="y";
				}else{
					$status="n";
				}
			}else{
				$status="n";
			}
		}else{
			$sql_chk="select shelf_no from chk_shelf_master where shelf_no='$data[shelf_no]' ";
			$stmt=$this->db->query($sql_chk);
			$chk_shelf=$stmt->fetchAll();
			$chk_shelf=count($chk_shelf);
			if($chk_shelf>0){
				$status="n";
			}else{
				$floor_no=$data['shelf_no']."-01";
				$room_no=$floor_no."-01";
				$data_insert=array(
							'corporation_id'=>$data_detail['corporation_id'],
							'company_id'=>$data_detail['company_id'],
							'shelf_no'=>$data['shelf_no'],
							'floor_no'=>$floor_no,
							'room_no'=>$room_no,
							'desc'=>$data['desc'],
							'reg_date'=>date('Y-m-d'),
							'reg_time'=>date('H:i:s'),
							'reg_user'=>$data_detail['user_id']
				);
				$rs=$this->db->insert('chk_shelf_master', $data_insert);
				if($rs){
					$status="y";
				}else{
					$status="n";
				}
			}
		}
		return $status;
	}
	
	public function deleteshelf($shelf_no){
		$sqlCheck=$this->db->select()
	    		   ->from('chk_shelf_master',array('floor_no'))
	    		   ->where('shelf_no=?',$shelf_no);
    	$rsNum=$sqlCheck->query()->fetchAll();
		$chkNum=count($rsNum);
		if($chkNum>0){
			$status="w";
		}else{
			if($checkdelete=="y"){
				$where = $this->db->quoteInto('shelf_no = ?', $shelf_no);
				$rs=$this->db->delete('chk_shelf_master', $where);
				if($rs){
					$status="y";
				}else{
					$status="n";
				}
			}
		}
		return $status;
	}
	
	public function checkeditfloor($shelf_no,$floor_no){
		if($floor_no!="n"){
			$sql=$this->db->select()
		    		  	->from('chk_shelf_master',array('shelf_no','floor_no','desc'))
		    		  	->where('shelf_no=?',$shelf_no)
		    		  	->where('floor_no=?',$floor_no);
	    	$data=$sql->query()->fetchAll();
		}else{
			$data="n";
		}
		return $data;
	}
	
	public function savefloor($data,$data_detail){
		if($data_detail['floor_no_up']!="n"){
			$data_insert=array(
						'floor_no'=>$data['floor_no'],
						'desc'=>$data['desc'],
						'upd_date'=>date('Y-m-d'),
						'upd_time'=>date('H:i:s'),
						'upd_user'=>$data_detail['user_id']
			);
			$where = array(
						'shelf_no = ?' => $data['shelf_no'],
						'floor_no = ?' => $data_detail['floor_no_up']
			);
			$rs=$this->db->update('chk_shelf_master', $data_insert, $where);
			if($rs){
				$data_insert_product=array(
						'floor_no'=>$data['floor_no'],
						'upd_date'=>date('Y-m-d'),
						'upd_time'=>date('H:i:s'),
						'upd_user'=>$data_detail['user_id']
				);
				$where_product = array(
							'shelf_no = ?' => $data['shelf_no'],
							'floor_no = ?' => $data_detail['floor_no_up']
				);
				$rs_product=$this->db->update('chk_shelf_product', $data_insert_product, $where_product);
				if($rs_product){
					$status="y";
				}else{
					$status="n";
				}
			}else{
				$status="n";
			}
			
		}else{
			$sql_chk=$this->db->select()
		    		  	->from('chk_shelf_master',array('floor_no'))
		    		  	->where('shelf_no=?',$data['shelf_no'])
		    		  	->where('floor_no=?',$data['floor_no']);
	    	$data_check=$sql_chk->query()->fetchAll();
	    	$numChk=count($data_check);
			if($numChk>0){
				$status= "w";
			}else{ 
				$floor_no=$data['floor_no'];
				$room_no=$floor_no."-01";
				$data_insert=array(
							'corporation_id'=>$data_detail['corporation_id'],
							'company_id'=>$data_detail['company_id'],
							'shelf_no'=>$data['shelf_no'],
							'floor_no'=>$floor_no,
							'room_no'=>$room_no,
							'desc'=>$data['desc'],
							'reg_date'=>date('Y-m-d'),
							'reg_time'=>date('H:i:s'),
							'reg_user'=>$data_detail['user_id']
				);
				$rs=$this->db->insert('chk_shelf_master', $data_insert);
				if($rs){
					$status="y";
				}else{
					$status="n";
				}
			}
		}
		return $status;
	}
	
	public function deletefloor($shelf_no,$floor_no){
		$sqlCheck=$this->db->select()
	    		   ->from('chk_shelf_master',array('room_no'))
	    		   ->where('shelf_no=?',$shelf_no)
	    		   ->where('floor_no=?',$floor_no);
    	$rsNum=$sqlCheck->query()->fetchAll();
		$chkNum=count($rsNum);
		if($chkNum>0){
			$status="w";
		}else{
			$where = array(
					'shelf_no = ?' => $shelf_no,
					'floor_no = ?' => $floor_no
			);
			$rs=$this->db->delete('chk_shelf_master', $where);
			if($rs){
				$status="y";
			}else{
				$status="n";
			}
		}
		return $status;
	}
	
	public function checkeditroom($shelf_no,$floor_no,$room_no){
		if($room_no!="n"){
			$sql=$this->db->select()
		    		  	->from('chk_shelf_master',array('shelf_no','floor_no','room_no','desc'))
		    		  	->where('shelf_no=?',$shelf_no)
		    		  	->where('floor_no=?',$floor_no)
		    		  	->where('room_no=?',$room_no);
	    	$data=$sql->query()->fetchAll();
		}else{
			$data="n";
		}
		return $data;
	}
	
	public function saveroom($data,$data_detail){
		if($data_detail['room_no_up']!="n"){
			$data_insert=array(
						'room_no'=>$data['room_no'],
						'desc'=>$data['desc'],
						'upd_date'=>date('Y-m-d'),
						'upd_time'=>date('H:i:s'),
						'upd_user'=>$data_detail['user_id']
			);
			$where = array(
						'shelf_no = ?' => $data['shelf_no'],
						'floor_no = ?' => $data['floor_no'],
						'floor_no = ?' => $data_detail['room_no_up']
			);
			$rs=$this->db->update('chk_shelf_master', $data_insert, $where);
		}else{
			$sql_chk=$this->db->select()
		    		  	->from('chk_shelf_master',array('room_no'))
		    		  	->where('shelf_no=?',$data['shelf_no'])
		    		  	->where('floor_no=?',$data['floor_no'])
		    		  	->where('room_no=?',$data['room_no']);
	    	$data_check=$sql_chk->query()->fetchAll();
	    	$numChk=count($data_check);
			if($numChk>0){
				$status= "w";
			}else{ 
				$room_no=$data['room_no'];
				$data_insert=array(
						'corporation_id'=>$data_detail['corporation_id'],
						'company_id'=>$data_detail['company_id'],
						'shelf_no'=>$data['shelf_no'],
						'floor_no'=>$data['floor_no'],
						'room_no'=>$room_no,
						'desc'=>$data['desc'],
						'reg_date'=>date('Y-m-d'),
						'reg_time'=>date('H:i:s'),
						'reg_user'=>$data_detail['user_id']
				);
				$rs=$this->db->insert('chk_shelf_master', $data_insert);
			}
		}
		if($rs){
			$status="y";
		}else{
			$status="n";
		}
		return $status;
	}
	
	public function deleteroom($shelf_no,$floor_no,$room_no){
		$sqlCheck=$this->db->select()
	    		   ->from('chk_shelf_product',array('product_id'))
	    		   ->where('shelf_no=?',$shelf_no)
	    		   ->where('floor_no=?',$floor_no)
	    		   ->where('room_no=?',$room_no);
    	$rsNum=$sqlCheck->query()->fetchAll();
		$chkNum=count($rsNum);
		if($chkNum>0){
			$status="w";
		}else{
			$where = array(
					'shelf_no = ?' => $shelf_no,
					'floor_no = ?' => $floor_no,
					'room_no = ?' => $room_no
			);
			$rs=$this->db->delete('chk_shelf_master', $where);
			if($rs){
				$status="y";
			}else{
				$status="n";
			}
		}
		return $status;
	}
	
	public function productonshelf($shelf_no,$floor_no,$room_no){
		$month=$this->month;
		$year=$this->year;
		$sql=$this->db->select()
					->from(array('sproduct'=>'chk_shelf_product'),
					       array('seq','product_id','flag'))
					->joinLeft(array('product'=>'com_product_master'),
							   'sproduct.product_id=product.product_id',
						       array('product.name_product')
					)
					->joinLeft(array('stock'=>'com_stock_master'),
							   "sproduct.product_id=stock.product_id and stock.month='$month' and stock.year='$year'",
						       array('stock.onhand')
					)
	    		  	->where('sproduct.shelf_no=?',$shelf_no)
	    		  	->where('sproduct.floor_no=?',$floor_no)
	    		  	->where('sproduct.room_no=?',$room_no)
	    		  	->order('sproduct.seq ASC')
	    		  	->order('sproduct.product_id ASC');
	    $data=$sql->query()->fetchAll();
	    if(empty($data)){
	    	return false;
	    }else{
	    	return $data;
	    }
	}
	
	public function productlisttag($shelf_no,$floor_no,$room_no,$doc_no){
		$month=$this->month;
		$year=$this->year;
		/*if(!empty($limit)){
			$sql_limit="select product_id from chk_list_tag where doc_no='$doc_no' and shelf_no='$shelf_no' and  room_no='$room_no' ";
			$stmt=$this->db->query($sql_limit);
			$data_limit=$stmt->fetchAll();
			$count_limit=count($data_limit);
			
			$start_limit=$count_limit-$limit;
			$sql=$this->db->select()
					->from(array('sproduct'=>'chk_list_tag'),
					       array('product_id','quantity','seq'))
					->joinLeft(array('product'=>'com_product_master'),
							   'sproduct.product_id=product.product_id',
						       array('product.name_product')
					)
	    		  	->where('sproduct.shelf_no=?',$shelf_no)
	    		  	->where('sproduct.floor_no=?',$floor_no)
	    		  	->where('sproduct.room_no=?',$room_no)
	    		  	->where('sproduct.doc_no=?',$doc_no)
	    		  	->order('sproduct.seq ASC')
	    		  	->limit($start_limit,$limit)
	    		  	;
		}else{*/
		$sql=$this->db->select()
					->from(array('sproduct'=>'chk_list_tag'),
					       array('product_id','quantity','seq'))
					->joinLeft(array('product'=>'com_product_master'),
							   'sproduct.product_id=product.product_id',
						       array('product.name_product')
					)
	    		  	->where('sproduct.shelf_no=?',$shelf_no)
	    		  	->where('sproduct.floor_no=?',$floor_no)
	    		  	->where('sproduct.room_no=?',$room_no)
	    		  	->where('sproduct.doc_no=?',$doc_no)
	    		  	->order('sproduct.seq ASC')
	    		  	;
		//}
	    $data=$sql->query()->fetchAll();
	    if(empty($data)){
	    	return false;
	    }else{
	    	return $data;
	    }
	}
	
	public function listproduct(){
		$sql=$this->db->select()
	    		   ->from('com_product_master',array('product_id','barcode','name_product'))
	    		   ->order('product_id ASC')
	    		   ->limit(10);
    	$data=$sql->query()->fetchAll();
		return $data;
	}
	
	public function getseqproductonshelf($tbl,$room_no){
		$sql="select max(seq) as seq from $tbl where room_no='$room_no'";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data[0]['seq'];
	}
	
	public function getseqlisttagtonshelf($tbl,$room_no,$doc_no){
		$sql="select max(seq) as seq from $tbl where room_no='$room_no' and doc_no='$doc_no'";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data[0]['seq'];
	}
	
	public function updateseqproduct($room_no){
		$sql="select * from chk_shelf_product where room_no='$room_no' order by seq,product_id ASC ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$i=1;
		foreach($data as $val){
			$room=$val['room_no'];
			$product_id=$val['product_id'];
			$sql_up="update chk_shelf_product set seq='$i' where room_no='$room' and product_id='$product_id' ";
			$this->db->query($sql_up);
			$i++;
		}
		//return $sql_up;
	}
	
	public function updateseqproductlisttag($room_no,$doc_no){
		$sql="select * from chk_list_tag where room_no='$room_no' and doc_no='$doc_no' order by seq,product_id ASC ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$i=1;
		foreach($data as $val){
			$room=$val['room_no'];
			$product_id=$val['product_id'];
			$doc_no=$val['doc_no'];
			$sql_up="update chk_list_tag set seq='$i' where room_no='$room' and product_id='$product_id' and doc_no='$doc_no' ";
			$this->db->query($sql_up);
			$i++;
		}
		//return $sql_up;
	}
	
	public function addproducttoshelf($arr_data){
		
		$sql_chk="
		select 
			a.product_id 
		from 
			com_product_master as a 
		left join 
			com_stock_master as b 
		on 
			a.product_id=b.product_id 
		where 
			a.product_id='$arr_data[product_id]' 
			and b.month='$this->month' 
			and b.year='$this->year' ";
		$stmt=$this->db->query($sql_chk);
		$chk=$stmt->fetchAll();
		$chk_count=count($chk);
		if($chk_count>0){
			$sql=$this->db->select()
		    		   ->from('com_product_master',array('product_id'))
		    		   ->where('product_id=?',$arr_data['product_id']);
	    	$data=$sql->query()->fetchAll();
	    	$num=count($data);
	    	if($num>0){
	    		$get_seq=$this->getseqlisttagtonshelf('chk_list_tag',$arr_data['room_no'],$arr_data['doc_no']);
	    		$seq=$get_seq+1;
		    	if(!empty($arr_data['doc_no'])){
					$data_insert=array(
								'corporation_id'=>$this->corporation_id,
								'company_id'=>$this->company_id,
								'shelf_no'=>$arr_data['shelf_no'],
								'floor_no'=>$arr_data['floor_no'],
								'room_no'=>$arr_data['room_no'],
								'seq'=>$seq,
								'product_id'=>$arr_data['product_id'],
								'quantity'=>$arr_data['qty'],
								'doc_no'=>$arr_data['doc_no'],
								'doc_date'=>$this->date,
								'reg_date'=>$this->date,
								'reg_time'=>$this->time,
								'reg_user'=>$this->user_id
					);
					$rs=$this->db->insert('chk_list_tag', $data_insert);
					$this->updateseqproductlisttag($arr_data['room_no'],$arr_data['doc_no']);
					if($rs){
						$status="y";
					}else{
						$status="w";
					}
				}
				
				$sql_chk=$this->db->select()
		    		   ->from('chk_shelf_product',array('product_id'))
		    		   ->where('shelf_no=?',$arr_data['shelf_no'])
		    		   ->where('floor_no=?',$arr_data['floor_no'])
		    		   ->where('room_no=?',$arr_data['room_no'])
		    		   ->where('product_id=?',$arr_data['product_id']);
	    		$data_chk=$sql_chk->query()->fetchAll();
	    		$chk=count($data_chk);
	    		if($chk<1){
	    			$get_seq_master=$this->getseqproductonshelf('chk_shelf_product',$arr_data['room_no']);
	    			$sql_master=$get_seq_master+1;
		    		$data_insert=array(
		    					'corporation_id'=>$this->corporation_id,
								'company_id'=>$this->company_id,
								'shelf_no'=>$arr_data['shelf_no'],
								'floor_no'=>$arr_data['floor_no'],
								'room_no'=>$arr_data['room_no'],
		    					'seq'=>$sql_master,
		    					'product_id'=>$arr_data['product_id'],
								'reg_date'=>$this->date,
								'reg_time'=>$this->time,
								'reg_user'=>$this->user_id
					);
					$rs=$this->db->insert('chk_shelf_product', $data_insert);
					
					$sql_chk_shelf="select shelf_no from chk_shelf_master where shelf_no='$arr_data[shelf_no]' ";
					$stmt=$this->db->query($sql_chk_shelf);
					$data=$stmt->fetchAll();
					$count_chk_shelf=count($data);
					if($count_chk_shelf<1){
						$sql_insert_shelf="
						insert into 
							chk_shelf_master 
						set 
							corporation_id='$this->corporation_id', 
							company_id='$this->company_id', 
							shelf_no='$arr_data[shelf_no]', 
							floor_no='$arr_data[floor_no]', 
							room_no='$arr_data[room_no]', 
							reg_date=NOW(),
							reg_time=NOW(),
							reg_user='$this->user_id'
						";
						$this->db->query($sql_insert_shelf);
					}
					
					$this->updateseqproduct($arr_data['room_no']);
	    		}
	    	}else{
	    		$status="n";
	    	}
		}else{
			$status="n";
		}
    	
		return $status;
	}
	
	public function deleteproductonshelf($data,$product_id){
		if(empty($product_id)){
			$status="n";
		}else{
			foreach($product_id as $val_product_id){
				$where = array(
					'shelf_no = ?' => $data['shelf_no'],
					'floor_no = ?' => $data['floor_no'],
					'room_no = ?' => $data['room_no'],
					'product_id = ?' => $val_product_id
				);
				$rs=$this->db->delete('chk_shelf_product', $where);
				if($rs){
					/*if(!empty($data['doc_no'])){
						$where = array(
							'shelf_no = ?' => $data['shelf_no'],
							'floor_no = ?' => $data['floor_no'],
							'room_no = ?' => $data['room_no'],
							'product_id = ?' => $val_product_id,
							'doc_no = ?' => $data['doc_no']
						);
						$rs=$this->db->delete('chk_list_tag', $where);
					}
					if($rs){
						$status="y";
					}else{
						$status="w";
					}*/
					$status="y";
				}else{
					$status="w";
				}
			}
		}
		$this->updateseqproduct($data['room_no']);
		return $status;
	}
	
	public function deletelisttag($data,$product_id){
		foreach($product_id as $val_product_id){
			$where = array(
					'shelf_no = ?' => $data['shelf_no'],
					'floor_no = ?' => $data['floor_no'],
					'room_no = ?' => $data['room_no'],
					'product_id = ?' => $val_product_id,
					'doc_no = ?' => $data['doc_no']
			);
			$rs=$this->db->delete('chk_list_tag', $where);
			if($rs){
				$status="y";
			}else{
				$status="w";
			}
		}
		return $status;
	}
	
	public function reflisttag($room_no){
		$sql="update chk_list_tag set quantity='0' where room_no='$room_no'";
		$stmt=$this->db->query($sql);
	}
	
	public function getdocno(){
		$sql_chk=$this->db->select()
	    		   ->from('chk_list_tag',array('doc_no'));	
    	$chk_data=$sql_chk->query()->fetchAll();
    	$chk_num=count($chk_data);
    	if($chk_num>0){
			$doc_no=$chk_data;
    	}else{
    		$doc_no="n";
    	}
    	return $doc_no;
	}
	
	public function genlisttag($data){
		$sqldocno=$this->db->select()
	    		   ->from('com_doc_no',array('corporation_id','company_id','branch_id','branch_no','doc_tp','doc_no'))
	    		   ->where('doc_tp=?','CK');
    	$data_docno=$sqldocno->query()->fetchAll();
		$convdocno="000000000".$data_docno[0]['doc_no'];
		$convdocno=substr($convdocno,-8);
		$doc_no=$data_docno[0]['branch_id'].$data_docno[0]['doc_tp']."-".$data_docno[0]['branch_no']."-".$convdocno;
		foreach($data as $val_shelf){
			asort($val_shelf['floor_no']);
			foreach($val_shelf['floor_no'] as $val_floor){
				foreach($val_floor['room_no'] as $val_room){
					if(!empty($val_room['product_id'])){	
						foreach($val_room['product_id'] as $val_product){
							
							$sql_get_qty="
							SELECT 
								`quantity`  
							FROM 
								`chk_list_tag` 
							where 
								product_id='$val_product[product_id]' 
								and shelf_no='$val_shelf[shelf_no]' 
								and floor_no='$val_floor[floor_no]' 
								and room_no='$val_room[room_no]' 
							order by 
								doc_date DESC 
							limit 1
							";
							$rs_get_qty=$this->db->query($sql_get_qty);
							$data_get_qty=$rs_get_qty->fetchAll();
							if(empty($data_get_qty[0]['quantity'])){
								$data_get_qty[0]['quantity']="";
							}
							
							$get_seq=$this->getseqproductonshelf('chk_list_tag',$val_room['room_no']);
							if($val_product[flag]=="1"){
								$seq=0;
							}else{
    							$seq=$get_seq+1;
							}
							$data_insert=array(
										'corporation_id'=>$data_docno[0]['corporation_id'],
										'company_id'=>$data_docno[0]['company_id'],
										'shelf_no'=>$val_shelf['shelf_no'],
										'floor_no'=>$val_floor['floor_no'],
										'room_no'=>$val_room['room_no'],
										'seq'=>$seq,
										'product_id'=>$val_product['product_id'],
										'quantity'=>$data_get_qty[0]['quantity'],
										'doc_no'=>$doc_no,
										'doc_date' => $this->date,
										'reg_date' => $this->date,
										'reg_time' => $this->time,
										'reg_user' => $this->user_id,
										'flag' => $val_product[flag]
							);
							$rs=$this->db->insert('chk_list_tag', $data_insert);
							
						}
					}
				}
			}
		}
		$run_docno=$data_docno[0]['doc_no']+1;
		$insert_docno=array(
					'doc_no'=>$run_docno
		);
		$where = array(
					'doc_tp = ?' => 'CK'
		);
		$rs=$this->db->update('com_doc_no', $insert_docno, $where);
		return $doc_no;
	}
	
	public function checkdocno($limit){
		$sql=$this->db->select()
	    		   ->from('chk_list_tag',array('doc_no'))
	    		   ->group('doc_no')
	    		   ->order('doc_no DESC')
	    		   ->limit($limit);
    	$data=$sql->query()->fetchAll();
    	if(count($data)==0){
    		$content="n";
    	}else{
    		$content=$data;
    	}
    	return $content;
	}
	
	public function getshelflisttag($doc_no_old,$product_id){
		if(!empty($product_id)){
			$sql=$this->db->select()
		    		   ->from('chk_list_tag',array('shelf_no'))
		    		   ->where('doc_no=?',$doc_no_old)
		    		   ->where('product_id=?',$product_id)
		    		   ->group('shelf_no');

	    	$data=$sql->query()->fetchAll();
		return $data;
		}else{
			$sql=$this->db->select()
		    		   ->from('chk_list_tag',array('shelf_no'))
		    		   ->where('doc_no=?',$doc_no_old)
		    		   ->group('shelf_no');
	    	$data=$sql->query()->fetchAll();
		}
    	return $data;
	}
	
	public function saveproductlisttag($product_id,$seq,$count_product_id,$doc_no,$shelf_no,$floor_no,$room_no){
		$data_insert=array(
					'quantity'=>$count_product_id
		);
		$where = array(
	       		'shelf_no = ?' => $shelf_no,
	       		'floor_no = ?' => $floor_no,
	       		'room_no= ?' => $room_no,
	       		'product_id = ?' => $product_id,
				'seq= ?' => $seq,
				'doc_no = ?' => $doc_no
		);
		$rs=$this->db->update('chk_list_tag', $data_insert, $where);
		if($rs){
			$status="y";
		}else{
			$status="n";
		}
		return $status;
	}
	
	public function getquantity($doc_no,$shelf_no,$floor_no,$room_no){
		$sql=$this->db->select()
	    		   ->from('chk_list_tag',array('product_id','quantity'))
	    		   ->where('doc_no=?',$doc_no)
	    		   ->where('shelf_no=?',$shelf_no)
	    		   ->where('floor_no=?',$floor_no)
	    		   ->where('room_no=?',$room_no)
	    		   ->order('seq ASC');
	    $data=$sql->query()->fetchAll();
	    return $data;	  
	}
	
	public function datadifcheck($doc_no,$orderby,$difby){
		$where="where a.doc_no='$doc_no' ";
		if($difby=="1"){
			$where.="and a.dif<'0' ";
		}else if($difby=="0"){
			$where.="and a.dif>'0' ";
		}else if($difby=="a"){
			$where.="";
		}
		$sql="
		select 
			a.*,b.price,b.name_product   
		from 
			chk_dif_stock as a 
		left join 
			com_product_master as b 
		on 
			a.product_id=b.product_id 
		$where 
		order by 
			a.product_id  
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	/*public function difcheck($doc_no,$sortname,$start,$rp){
		$month=$this->month;
		$year=$this->year;
		$sql=$this->db->select()
	    		   ->from('chk_list_tag',array('product_id','SUM(quantity) as quantity'))
	    		   ->where('doc_no=?',$doc_no)
	    		   ->group('product_id');
	    $data=$sql->query()->fetchAll();
	    $count_listtag=count($data);
	    if($count_listtag>0){
	    	$this->db->query("TRUNCATE TABLE chk_dif_stock");
	    	$where_dif_check = $this->db->quoteInto('doc_no = ?', $doc_no);
			$this->db->delete('chk_dif_stock', $where_dif_check);
				if(!empty($data)){
					foreach($data as $val){
				    	$sql_stockmaster=$this->db->select()
								    		   ->from('com_stock_master',array('product_id','month','year','onhand'))
								    		   ->where('product_id=?',$val['product_id'])
								    		   ->where('month=?',$month)
								    		   ->where('year=?',$year);
			    		$data_stockmaster=$sql_stockmaster->query()->fetchAll();
			    		$count_stockmaster=count($data_stockmaster);
			    		//if($count_stockmaster>0){
					    	if(!empty($data_stockmaster[0]['onhand'])){
					    		$dif=($val['quantity']-$data_stockmaster[0]['onhand']);
					    		$data_insert=array(
									'corporation_id'=>'OP',
									'company_id'=>'OP',
									'product_id'=>$val['product_id'],
									'onhand'=>$data_stockmaster[0]['onhand'],
									'product_status'=>'N',
									'check'=>$val['quantity'],
									'dif'=>$dif,
									'doc_no'=>$doc_no
								);
								$rs_difstock=$this->db->insert('chk_dif_stock', $data_insert);
								if($rs_difstock){
									$sql_data=$this->db->select()
										    		   ->from(array('tbl_difstock'=>'chk_dif_stock'),array('tbl_difstock.product_id','tbl_difstock.onhand','tbl_difstock.product_status','tbl_difstock.check','tbl_difstock.dif','tbl_difstock.doc_no'))
										    		   ->joinLeft(array('product_master'=>'com_product_master'),'tbl_difstock.product_id=product_master.product_id',array('product_master.price','product_master.name_product'))
										    		   ->where('tbl_difstock.doc_no=?',$doc_no)
										    		   ->order($sortname)
													   ->limit($rp, $start);
					    			$out=$sql_data->query()->fetchAll();
								}else{
									$out=array();
								}
					    	}
			    		//}else{
			    			//$out=array();
			    		//}
				    }
				}else{
					$out=array();
				} 			
	    }else{
	    	$out=array();
	    }
	    
		return $out;
	}*/
	public function difcheck($doc_no,$sortname,$start,$rp){
		$month=$this->month;
		$year=$this->year;
		$this->db->query("TRUNCATE TABLE chk_dif_stock");
		$sql="
		insert into 
			chk_dif_stock(`corporation_id`,`company_id`,`product_id`,`onhand`,`product_status`,`check`,`dif`,`doc_no`) 
		select 
			sub.corporation_id,sub.company_id,sub.product_id,sub.onhand,'N',sub.qty,(sub.qty-sub.onhand),'$doc_no' 
		from 
			(SELECT 
				a.corporation_id,a.company_id,a.product_id,a.onhand,
				if(sum(c.quantity) is null,0,sum(c.quantity)) as qty,
				if((sum(c.quantity)-a.onhand) is null,0,sum(c.quantity)-a.onhand) as dif,
				c.doc_no 
			FROM
				`com_stock_master` as a 
			left join 
				com_product_master as b 
			on 
				a.product_id=b.product_id 
			left join 
				chk_list_tag as c 
			on 
				a.product_id=c.product_id and c.doc_no='$doc_no'
			where 
				a.month='$month' and a.year='$year' 
			group by 
				a.product_id,c.product_id) sub 
		";
		$stmt=$this->db->query($sql);
		//$data=$stmt->fetchAll();
		if($stmt){
			$sql_data=$this->db->select()
				    		   ->from(array('tbl_difstock'=>'chk_dif_stock'),array('tbl_difstock.product_id','tbl_difstock.onhand','tbl_difstock.product_status','tbl_difstock.check','tbl_difstock.dif','tbl_difstock.doc_no'))
				    		   ->joinLeft(array('product_master'=>'com_product_master'),'tbl_difstock.product_id=product_master.product_id',array('product_master.price','product_master.name_product'))
				    		   ->where('tbl_difstock.doc_no=?',$doc_no)
				    		   ->order($sortname)
							   ->limit($rp, $start);
	    	$out=$sql_data->query()->fetchAll();
		}else{
			$out=array();
		}
		return $out;
	}
	
	public function getproductlisttag($product_id,$doc_no){
		$sql_chk=$this->db->select()
	    		   ->from('chk_list_tag',array('shelf_no','floor_no','room_no','product_id','quantity'))
	    		   ->where('doc_no=?',$doc_no)
	    		   ->where('product_id IN (?)', $product_id);
    	$chk_data=$sql_chk->query()->fetchAll();
    	$chk_num=count($chk_data);
    	if($chk_num>0){
			$data=$chk_data;
    	}else{
    		$data="n";
    	}
    	return $data;
	}
	
	public function getproducdifstock($product_id){
		$sql="
		select 
			shelf_no,shelf_no,room_no,
		";
		$sql_chk=$this->db->select()
	    		   ->from('chk_shelf_product',array('shelf_no','floor_no','room_no','product_id'))
	    		   ->where('product_id IN (?)', $product_id);
    	$chk_data=$sql_chk->query()->fetchAll();
    	$chk_num=count($chk_data);
    	if($chk_num>0){
			$data=$chk_data;
    	}else{
    		$data=array();
    	}
    	return $data;
	}
	
	public function getarrdifstock($doc_no,$sortname){
		$sql=$this->db->select()
	    		   ->from('chk_dif_stock',array('product_id'))
	    		   ->where('doc_no=?',$doc_no);
	    $row=$sql->query()->fetchAll();
	    $count_listtag=count($row);
		foreach($row as $data){
	   		 $out[] = array('product_id'=>$data['product_id'],'product_dif' => $this->getarrdifstocksum($data['product_id'],$doc_no,$sortname));
	   	}
	   	return $out;
	}
	
	public function getarrdifstocksum($product_id,$doc_no,$sortname){
		$sql=$this->db->select()
	    		   ->from('chk_dif_stock',array('product_id','onhand','check','dif'))
	    		   ->where('product_id=?',$product_id)
	    		   ->where('doc_no=?',$doc_no);
	    $row=$sql->query()->fetchAll();
	    $count_listtag=count($row);
		foreach($row as $data){
	   		 $out[] = array('product_id'=>$data['product_id'],'stock'=>$data['onhand'],'check'=>$data['check'],'dif'=>$data['dif'],'dif_detail'=>$this->getarrdifstockdifdetail($product_id,$doc_no,$sortname));
	   	}
	   	return $out;
	}
	
	public function getarrdifstockdifdetail($product_id,$doc_no,$sortname){
		$sql=$this->db->select()
	    		   ->from('chk_list_tag',array('shelf_no','floor_no','room_no','product_id','quantity','product_status'))
	    		   ->where('doc_no=?',$doc_no)
	    		   ->where('product_id = ?', $product_id);
    	$row=$sql->query()->fetchAll();
    	foreach($row as $data){
	   		 $out[] = array('quantity'=>$data['quantity'],'product_status'=>$data['product_status'],'shelf_no'=>$data['shelf_no'],'floor_no'=>$data['floor_no'],'room_no'=>$data['room_no']);
	   	}
	   	return $out;
	}
	
	public function viewbalanceproduct($doc_no){
		//$month="07";
		//$year="2011";
		$month=$this->month;
		$year=$this->year;
		$sql="
		select 
			cc.rs,sum(cc.diff) as qty,sum(cc.sumprice) as sum 
		from 
			(select 
				if(bb.diff>0,'เกิน','ขาด') as rs,bb.*  
			from  
				(select  
					a.shelf_no,a.floor_no,a.room_no,
					a.product_id as a_product,b.product_id as b_product,c.product_id as c_product,
					b.onhand,sum(a.quantity),
					(sum(a.quantity)-b.onhand) as diff,
					(c.price*(sum(a.quantity)-b.onhand)) as sumprice,
					b.month,b.year ,c.price 
				from  
					chk_list_tag as a 
				left join 
					com_stock_master as b 
				on 
					a.product_id=b.product_id 
				left join 
					com_product_master as c 
				on 
					a.product_id=c.product_id 
				where 
					a.doc_no = '$doc_no' and month='$month' and year='$year'
				group by 
					a.product_id) as bb ) as cc group by cc.rs
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function tranferstock($doc_no,$doc_date){
		$where_del = $this->db->quoteInto('doc_number = ?', $doc_no);
		$rs_del=$this->db->delete('chk_stock', $where_del);
		
		$sql="
		select 
			a.corporation_id,a.company_id,a.product_id,a.onhand,a.check,a.dif,b.name_product,b.price,
			if(dif>0,'AI','AO') as doc_tp,
			if(dif>0,dif,(dif*-1)) as d_dif 
		from 
			chk_dif_stock as  a 
		left join 
			com_product_master as b 
		on 
			a.product_id=b.product_id 
		where 
			a.doc_no='$doc_no' and a.dif<>'0'
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$count=count($data);
		if($count>0){
			$i=1;
			foreach($data as $val){
				$sql_insert="
				insert into 
					chk_stock 
				set 
					corporation_id='$val[corporation_id]', 
					company_id='$val[company_id]', 
					seq='$i', 
					product_id='$val[product_id]', 
					name='$val[name_product]', 
					price='$val[price]', 
					onhand='$val[onhand]', 
					product_status='N', 
					doc_tp='$val[doc_tp]', 
					`check`='$val[check]', 
					dif='$val[d_dif]', 
					doc_number='$doc_no', 
					doc_date='$doc_date', 
					reg_date='$this->date', 
					reg_time='$this->time', 
					reg_user='$this->user_id' 
				";
				$rs=$this->db->query($sql_insert);
				if($rs){
					$out="y";
				}else{
					$out="n3";
				}
				$i++;
			}
		}else{
			$out="n3";
		}
		return $out;
	}
	
	/*public function tranferstock($doc_no,$doc_date){
		$month=$this->month;
		$year=$this->year;
		
		$where_del = $this->db->quoteInto('doc_number = ?', $doc_no);
		$rs_del=$this->db->delete('chk_stock', $where_del);
		
		$sql=$this->db->select()
	    		   ->from('chk_list_tag',array('product_id','SUM(quantity) as quantity'))
	    		   ->where('doc_no=?',$doc_no)
	    		   ->group('product_id');
	    $data=$sql->query()->fetchAll();
	    $count_listtag=count($data);
	    if($count_listtag>0){
	    	
				if(!empty($data)){
					$i=1;
					foreach($data as $val){
				    	$sql_stockmaster=$this->db->select()
								    		   ->from(array('smaster'=>'com_stock_master'),array('product_id','month','year','onhand'))
								    		   ->joinLeft(array('sproduct'=>'com_product_master'),
								    		   				'smaster.product_id=sproduct.product_id',
								    		   				array('sproduct.name_product','sproduct.price')
												)
								    		   ->where('smaster.product_id=?',$val['product_id'])
								    		   ->where('smaster.month=?',$month)
								    		   ->where('smaster.year=?',$year);
	    		   
			    		$data_stockmaster=$sql_stockmaster->query()->fetchAll();
			    		$count_stockmaster=count($data_stockmaster);
			    		if($count_stockmaster>0){
					    	if(!empty($data_stockmaster[0]['onhand'])){
					    		$dif=($data_stockmaster[0]['onhand']-$val['quantity']);
					    		if($dif>0){
					    			$doc_tp="AO";
					    		}else{
					    			$doc_tp="AI";
					    			$dif=abs($dif);
					    		}
					    		$data_insert=array(
									'corporation_id'=>'OP',
									'company_id'=>'OP',
					    			'seq'=>$i,
									'product_id'=>$val['product_id'],
					    			'name'=>$data_stockmaster[0]['name_product'],
					    			'price'=>$data_stockmaster[0]['price'],					    		
									'onhand'=>$data_stockmaster[0]['onhand'],
									'product_status'=>'N',
					    			'doc_tp'=>$doc_tp,
									'check'=>$val['quantity'],
									'dif'=>$dif,
									'doc_number'=>$doc_no,
					    			'doc_date'=>$doc_date,
					    			'reg_date'=>$this->date,
					    			'reg_time'=>$this->time,
					    			'reg_user'=>$this->user_id
								);
								$rs_difstock=$this->db->insert('chk_stock', $data_insert);
								$out="y";
					    	}
			    		}else{
			    			$out="n1";
			    		}
			    		$i++;
				    }
				}else{
					$out="n2";
				} 			
	    }else{
	    	$out="n3";
	    }
	    
		return $out;
	}*/
	
	public function getroom($room){
		$sql="select product_id from chk_shelf_product where room_no='$room' order by seq,product_id asc ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function saveproductseq($product_id,$room,$seq){
		$sql="update chk_shelf_product set seq='$seq',upd_date=NOW(),upd_time=NOW() where room_no='$room' and product_id='$product_id' ";
		$stmt=$this->db->query($sql);
	}
	
	public function convproduct_id($data){
		$sql="
		select 
			product_id,barcode 
		from 
			com_product_master 
		where 
			product_id='$data' or barcode='$data'
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getroomtranfer(){
		$sql="
		select 
			shelf_no,floor_no,room_no  
		from 
			chk_shelf_master  
		group by 
			room_no 
		order by 
			room_no ASC
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function gotranferroom($old_room_no,$new_room,$tranf_product){
		
		if(!empty($tranf_product)){
			foreach($tranf_product as $val_product){
				$get_seq=$this->getseqproductonshelf('chk_shelf_product',$new_room);
				$get_seq=$get_seq+1;
				$sql_sel="
				SELECT 
					corporation_id,company_id,shelf_no,floor_no,room_no  
				FROM 
					chk_shelf_master 
				WHERE 
					room_no='$new_room'
				";
				$stmt=$this->db->query($sql_sel);
				$data_sel=$stmt->fetchAll();
				$corporation_id=$data_sel[0]['corporation_id'];
				$company_id=$data_sel[0]['company_id'];
				$shelf_no=$data_sel[0]['shelf_no'];
				$floor_no=$data_sel[0]['floor_no'];
				$room_no=$data_sel[0]['room_no'];
				$sql_insert="
				INSERT INTO 
					chk_shelf_product 
				SET 
					corporation_id='$corporation_id',
					company_id='$company_id',
					shelf_no='$shelf_no', 
					floor_no='$floor_no', 
					room_no='$room_no', 
					seq='$get_seq',
					product_id='$val_product',
					reg_date='$this->date', 
					reg_time='$this->time',
					reg_user='$this->user_id'
				";
				$stmt_in=$this->db->query($sql_insert);
				
				if($stmt_in){
					$sql_del="DELETE FROM chk_shelf_product where room_no='$old_room_no' and product_id='$val_product'";
					$stmt_del=$this->db->query($sql_del);
					if($stmt_del){
						$status="y";
					}
				}
				
			}
		}
		return $status;
	}
	
	public function checkqtyonshelf($doc_no){
		$sql="
		select 
			sub.*,sum(sub.aqty) as qty,sum(sub.s_price) as sum_price from 
		(SELECT 			
			a.shelf_no,c.`desc`,a.room_no,a.product_id,a.doc_no,a.quantity as aqty,b.price,
			(a.quantity*b.price) as s_price
		FROM 
			`chk_list_tag` as a  
		left join 
             com_product_master as b 
        on 
             a.product_id=b.product_id 
        left join 
			chk_shelf_master as c 
		on 
			 a.shelf_no=c.shelf_no and a.room_no=c.room_no 
		where 
			a.doc_no='$doc_no' 
		order by 
			a.shelf_no) as sub group by sub.shelf_no
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
		
	}
	
	public function getproductroom($doc_no,$product_id){
		$sql="
		select
			shelf_no,room_no,product_id,quantity,doc_no,seq
		from 
			chk_list_tag 
		where 
			doc_no='$doc_no' and product_id='$product_id' 
		order by room_no ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function savelisttagroom($doc_no,$product_id,$room_no,$qty,$seq){
		$sql="
		update 
			chk_list_tag  
		set 
			quantity='$qty' 
		where 
			doc_no='$doc_no' 
			and room_no='$room_no' 
			and product_id='$product_id' 
			and seq='$seq'
		";
		$stmt=$this->db->query($sql);
	}
	
	public function chk_password_checkstock($pwd,$group_id){
		$sql=$this->db->select()
		    		  	->from('conf_employee',array('employee_id','user_id'))
		    		  	->where('password_id = ?', $pwd)
		    		  	->where('group_id = ?', $group_id);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function delroomck($doc_no,$room_no){
		$sql="
		delete 
		from 
			chk_list_tag 
		where 
			doc_no='$doc_no' 
			and room_no='$room_no'
		";
		$rs=$this->db->query($sql);
		if($rs){
			$status="y";
		}else{
			$status="n";
		}
		return $status;
	}
	
	public function tranfershelfmastertolisttag($data,$doc_no){
		foreach($data as $val_shelf){
			asort($val_shelf['floor_no']);
			foreach($val_shelf['floor_no'] as $val_floor){
				foreach($val_floor['room_no'] as $val_room){
					if(!empty($val_room['product_id'])){	
						foreach($val_room['product_id'] as $val_product){
							$get_seq=$this->getseqproductonshelf('chk_list_tag',$val_room['room_no']);
    						$seq=$get_seq+1;
							$data_insert=array(
										'corporation_id'=>$this->corporation_id,
										'company_id'=>$this->company_id,
										'shelf_no'=>$val_shelf['shelf_no'],
										'floor_no'=>$val_floor['floor_no'],
										'room_no'=>$val_room['room_no'],
										'seq'=>$seq,
										'product_id'=>$val_product['product_id'],
										'doc_no'=>$doc_no,
										'doc_date' => $this->date,
										'reg_date' => $this->date,
										'reg_time' => $this->time,
										'reg_user' => $this->user_id
							);
							$rs=$this->db->insert('chk_list_tag', $data_insert);
						}
					}
				}
			}
		}
	}
	
	public function checkrunck(){
		$sql="select doc_no from chk_list_tag where reg_date='$this->date' ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$count=count($data);
		if($count>0){
			$rs="n";
		}else{
			$rs="y";
		}
		return $rs;
	}
	
	public function genshelfaudit(){
		$per_shelf=array('A'=>array('A','20'),'B'=>array('B','20'),'T'=>array('T','20'),'S'=>array('S','10'),'L'=>array('L','40'));
		$sql_del="delete from chk_shelf_master  where flag='1'";
		$rs_del=$this->db->query($sql_del);
		if($rs_del){
			$sql_del_product="delete from chk_shelf_product where flag='1'";
			$rs_del_product=$this->db->query($sql_del_product);
			$j=1;
			foreach($per_shelf as $val){
				$per_shelf=$val[0];
				$count_shelf=$val[1];
				for($i=1;$i<=$count_shelf;$i++){
					$digit_seq="00".$i;
					$digit_seq=substr($digit_seq,-2);
					$shelf_name="AUDIT_".$per_shelf.$digit_seq;
					$shelf_name_floor=$shelf_name."-01";
					$shelf_name_room=$shelf_name_floor."-01";
					$sql="
					insert into 
						chk_shelf_master 
					set 
						corporation_id='$this->corporation_id', 
						company_id='$this->corporation_id', 
						seq='$j', 
						shelf_no='$shelf_name', 
						floor_no='$shelf_name_floor', 
						room_no='$shelf_name_room', 
						`desc`='Shelf Audit $shelf_name_room', 
						reg_date=NOW(), 
						reg_time=NOW(), 
						reg_user='system', 
						flag='1'
					";
					$rs=$this->db->query($sql);
					if($rs){
						$sql_insert_shelf_product="
						insert into
							chk_shelf_product 
						set 
							corporation_id='$this->corporation_id', 
							company_id='$this->corporation_id',
							shelf_no='$shelf_name', 
							floor_no='$shelf_name_floor', 
							room_no='$shelf_name_room', 
							seq='$i', 
							product_id='', 
							reg_date=NOW(), 
							reg_time=NOW(), 
							reg_user='system', 
							flag='1' 
						";
						$rs_product=$this->db->query($sql_insert_shelf_product);
						if($rs_product){
							$status="Y";
						}else{
							$status="N";
						}
					}else{
						$status="N";
					}
					$j++;
				}
			}
		}else{
			$status="N";
		}
		return $status;
	}
}
?>