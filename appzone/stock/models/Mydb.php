<?php 
class Model_Mydb{	
	public $db;
	public function __construct(){
		$this->db=Zend_Registry::get('db1');
	}
	public function viewshelf(){
		$sql_shelf=$this->db->select()
		    		  	->from('chk_shelf_master',array('shelf_no'))
		    			->group('shelf_no');
	    $row=$sql_shelf->query()->fetchAll();
	    if(count($row)==0) return false;
		foreach($row as $data){
	   		 $out[] = array('shelf_no'=>$data['shelf_no'],'floor_no' => $this->viewfloor($data['shelf_no']));
	   	}	
		return $out;
	}
	
	public function viewfloor($shelf_no){
		$sql_floor=$this->db->select()
		    		  	->from('chk_shelf_master',array('floor_no'))
		    		  	->where('shelf_no=?',$shelf_no)
		    			->group('floor_no')
		    			->order('floor_no DESC');
	    $row=$sql_floor->query()->fetchAll();
	    
		foreach($row as $data){
	   		 $out[] = array('floor_no'=>$data['floor_no'],'room_no' => $this->viewroom($shelf_no,$data['floor_no']));
	   	}	
		return $out;
	}
	
	public function viewroom($shelf_no,$floor_no){
		$sql_room=$this->db->select()
		    		   ->from('chk_shelf_master',array('room_no'))
		    		   ->where('shelf_no=?',$shelf_no)
		    		   ->where('floor_no=?',$floor_no)
		    		   ->group('room_no')
		    		   ->order('room_no ASC');
	    $row=$sql_room->query()->fetchAll();
	    
		foreach($row as $data){
	   		 $out[] = array('room_no'=>$data['room_no']);
	   	}	
		return $out;
	}
	
	public function checkeditshelf($shelf_no){
		if($shelf_no!="n"){
			$sql=$this->db->select()
		    		  	->from('chk_shelf_master',array('shelf_no','desc'))
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
		$sql=$this->db->select()
					->from(array('sproduct'=>'chk_shelf_product'),
					       array('product_id'))
					->joinLeft(array('product'=>'com_product_master'),
							   'sproduct.product_id=product.product_id',
						       array('product.name_product')
					)
	    		  	->where('sproduct.shelf_no=?',$shelf_no)
	    		  	->where('sproduct.floor_no=?',$floor_no)
	    		  	->where('sproduct.room_no=?',$room_no);
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
	    		   ->order('product_id ASC');
    	$data=$sql->query()->fetchAll();
		return $data;
	}
	
	public function addproducttoshelf($arr_data){
		$sql=$this->db->select()
	    		   ->from('com_product_master',array('product_id'))
	    		   ->where('product_id=?',$arr_data['product_id']);
    	$data=$sql->query()->fetchAll();
    	$num=count($data);
    	if($num>0){
    		$data_insert=array(
    					'corporation_id'=>$arr_data['corporation_id'],
						'company_id'=>$arr_data['company_id'],
						'shelf_no'=>$arr_data['shelf_no'],
						'floor_no'=>$arr_data['floor_no'],
						'room_no'=>$arr_data['room_no'],
    					'product_id'=>$arr_data['product_id'],
						'reg_date'=>date('Y-m-d'),
						'reg_time'=>date('H:i:s'),
						'reg_user'=>$arr_data['user_id']
			);
			$rs=$this->db->insert('chk_shelf_product', $data_insert);
			if($rs){
				$status="y";
			}else{
				$status="w";
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
					$status="y";
				}else{
					$status="w";
				}
			}
		}
		return $status;
	}
}
?>