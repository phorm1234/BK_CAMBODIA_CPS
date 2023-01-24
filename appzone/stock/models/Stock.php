<?php 
//v.1 แก้ function previewrq 3/11/12
class Model_Stock{	
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
	public $amount;
	public $amount_item;
	public $doc_no;
	public $ref_doc_no;
	public $doc_tp;
	public $get_doc_no;
	public $checkstock;
	public $getstock;
	public $branch_no;
	public $vendor_id;
	public $doc_date;
	
	public function __construct(){
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
		$objPos=new SSUP_Controller_Plugin_PosGlobal();
       	//$doc_date_pos=$objPos->getDocDatePos($this->corporation_id,$this->company_id,$this->branch_id,$this->branch_no); 
		$doc_date_pos=$this->getdocdate();
		$this->doc_date_pos=$doc_date_pos;
		$date_arr = explode('-',$doc_date_pos);
		$this->date=date('Y-m-d');
		$this->year_month=$date_arr[0]."-".$date_arr[1];
		$this->time=date('H:i:s');
		$this->month=$date_arr[1];
		$this->m_month=date('m');
		$this->year=$date_arr[0];
	}
	
	public function getdocdate(){
		$sql="
		SELECT 
			doc_date  
 		FROM 
 			com_doc_date 
 		WHERE 
 			corporation_id='".$this->corporation_id."' 
 			and company_id='".$this->company_id."'
 		limit 1 
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		return $rs[0]['doc_date'];
	}

	public function getinv(){
		//$this->db=SSUP_Controller_Plugin_Db::checkDbOnline("trn","trn_in2shop_head"); 
		$sql3="update `trn_in2shop_head` as a inner join trn_diary1 as b on a.doc_no=b.refer_doc_no set a.status_transfer='T'";
		$stmt=$this->db->query($sql3);
		$sql4="update `trn_in2shop_list` as a inner join trn_diary1 as b on a.doc_no=b.refer_doc_no set a.status_transfer='T' ";
		$stmt=$this->db->query($sql4);
		$sql=$this->db->select()
					->from(array('trn_in2shop_head'=>'trn_in2shop_head'),
					       array('doc_date','doc_no','quantity','refer_doc_no','flag1'))
					->joinLeft(array('trn_diary1'=>'trn_diary1'),
							   'trn_in2shop_head.doc_no=trn_diary1.refer_doc_no',
						       array('trn_diary1.refer_doc_no','trn_diary1.flag','trn_diary1.id')
					)
	    		  	->where('trn_in2shop_head.flag1=?','D');
	    $data=$sql->query()->fetchAll();
	    return $data;
	}

	public function getdoctobyor()
	{
		$d = date('Y-m-d',strtotime("-30 days"));

		$sql = $this->db->select()
			->from(
				array('trn_diary1' => 'trn_diary1'),
				array('doc_date', 'doc_no', 'quantity', 'flag')
			)
			->where('corporation_id=?', $this->corporation_id)
			->where('company_id=?', $this->company_id)
			->where('branch_id=?', $this->branch_id)
			->where('doc_date>?', $d)
			->where('doc_tp=?', 'TI');

		//change to select from trn_diary1
		// $sql = $this->db->select()
		// 	->from(
		// 		array('trn_diary1_or' => 'trn_diary1_or'),
		// 		array('doc_date', 'doc_no', 'quantity', 'flag')
		// 	)
		// 	->where('corporation_id=?', $this->corporation_id)
		// 	->where('company_id=?', $this->company_id)
		// 	->where('branch_id=?', $this->branch_id)
		// 	->where('doc_date>?', $d);

		$data = $sql->query()->fetchAll();
		return $data;
	}

	public function getdoctobyto()
	{
		$d = date('Y-m-d',strtotime("-30 days"));

		$sql = $this->db->select()
			->from(
				array('trn_diary1' => 'trn_diary1'),
				array('doc_date', 'doc_no', 'quantity', 'flag')
			)
			->where('corporation_id=?', $this->corporation_id)
			->where('company_id=?', $this->company_id)
			->where('branch_id=?', $this->branch_id)
			->where('doc_tp=?', 'TI')
			->where('doc_date>?', $d);

		$data = $sql->query()->fetchAll();
		return $data;
	}
	
	public function tshop_head2tshop_tmp($doc_no){
		$this->TrancateTable('trn_in2shop_list_tmp');
		$this->TrancateTable('trn_in2shop_head_tmp');
		
		$sql_h_tmp="INSERT INTO 
						trn_in2shop_head_tmp(doc_date,doc_no,flag1,quantity,refer_doc_no)
						SELECT
							doc_date,doc_no,flag1,quantity,refer_doc_no
						FROM
							trn_in2shop_head
						WHERE
							doc_no='$doc_no' AND flag1='D'";
		$stmt=$this->db->query($sql_h_tmp);
		
		$sql_l_tmp="INSERT INTO
						trn_in2shop_list_tmp(branch_id,doc_date,doc_no,seq,product_id,product_name,quantity,flag1,price,barcode,product_status,mfg_date)
						SELECT
							branch_id,doc_date,doc_no,seq,product_id,product_name,quantity,flag1,price,barcode,product_status,mfg_date
						FROM
							trn_in2shop_list 
						WHERE
							doc_no='$doc_no' AND flag1='D'";
		$stmt=$this->db->query($sql_l_tmp);
	}
	
	public function tshop_head2tshop_tmp_checkstock($doc_no,$status_no,$doc_tp){
		$this->TrancateTable('trn_in2shop_list_tmp');
		$this->TrancateTable('trn_in2shop_head_tmp');
		$sql_l_tmp="INSERT INTO
					trn_in2shop_list_tmp(branch_id,doc_date,doc_no,seq,product_id,product_name,quantity,flag1,price)
					SELECT
						company_id,reg_date,doc_number,seq,product_id,name,dif,'D',price 
					FROM
						chk_stock 
					WHERE
						doc_number='$doc_no' and doc_tp='$doc_tp'";
		$stmt=$this->db->query($sql_l_tmp);
		$sum_qty=$this->sumivn($doc_no);
		
		//$doc_date=$this->chk_password_checkstock($doc_no);
		
		$insert_tem_head=array(
				'corporation_id'=>$this->corporation_id,
				'company_id'=>$this->company_id,
				'branch_id'=>$this->branch_id,
				'doc_date'=>$this->date,
				'doc_no'=>$doc_no,
				'flag1'=>'D',
				'quantity'=>$sum_qty[0]['quantity'],
		    	'reg_date'=>$this->date,
		    	'reg_time'=>$this->time,
		    	'reg_user'=>$this->user_id
			);
		$rs_tmp_head=$this->db->insert('trn_in2shop_head_tmp', $insert_tem_head);	
		return $insert_tem_head;
	}
	
	public function getdataproduct($doc_no,$sort,$start,$rp){
		if(!empty($rp)){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_list_tmp',array('doc_date','doc_no','seq','product_id','product_name','quantity','price','product_status','mfg_date'))
						->where('doc_no = ?', $doc_no)
						->where('flag1 = ?', 'D')
						->order($sort)
						->limit($rp, $start);
		}else{
			$sql=$this->db->select()
		    		  	->from('trn_in2shop_list_tmp',array('doc_date','doc_no','seq','product_id','product_name','quantity','price','product_status','mfg_date'))
						->where('doc_no = ?', $doc_no)
						->where('flag1 = ?', 'D')
						->order($sort);
		}
		$data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function getdataproduct_to($sort){	
		$sql=$this->db->select()
		    		  	->from(array('trn_tdiary2_to'=>'trn_tdiary2_to'),array('trn_tdiary2_to.seq','trn_tdiary2_to.product_id','trn_tdiary2_to.price','trn_tdiary2_to.quantity','trn_tdiary2_to.amount','trn_tdiary2_to.product_status'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_tdiary2_to.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						);
	    $data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}

	public function getdataproduct_to_withdoc($doc_no){	
		$sql=$this->db->select()
			->from(array('trn_diary2'=>'trn_diary2'),array('trn_diary2.seq','trn_diary2.product_id','trn_diary2.price','trn_diary2.quantity','trn_diary2.amount','trn_diary2.product_status'))
			->joinLeft(array('com_product_master'=>'com_product_master'),
			'trn_diary2.product_id=com_product_master.product_id',
			array('com_product_master.name_product')
	 	)
		->where('trn_diary2.doc_no=?',$doc_no);
		// $sql=$this->db->select()
		//     		  	->from(array('trn_tdiary2_to'=>'trn_tdiary2_to'),array('trn_tdiary2_to.seq','trn_tdiary2_to.product_id','trn_tdiary2_to.price','trn_tdiary2_to.quantity','trn_tdiary2_to.amount','trn_tdiary2_to.product_status'))
		//     		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		//     		  	'trn_tdiary2_to.product_id=com_product_master.product_id',
		//     		  	array('com_product_master.name_product')
		// 				);
	    $data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}

	////////////
	public function getdataproduct_toor($sort,$doc_no){	
		$sql=$this->db->select()
		->from(array('trn_diary2'=>'trn_diary2'),array('trn_diary2.seq','trn_diary2.product_id','trn_diary2.price','trn_diary2.quantity','trn_diary2.amount','trn_diary2.product_status'))
		->joinLeft(array('com_product_master'=>'com_product_master'),
		'trn_diary2.product_id=com_product_master.product_id',
		array('com_product_master.name_product')
	  	)
		->where('trn_diary2.doc_no = ?', $doc_no)
		->where('trn_diary2.doc_tp = ?', 'TI');
		// change to trn_diary2 where doc_tp = ti
		// $sql=$this->db->select()
		//     		  	->from(array('trn_diary2_or'=>'trn_diary2_or'),array('trn_diary2_or.seq','trn_diary2_or.product_id','trn_diary2_or.price','trn_diary2_or.quantity','trn_diary2_or.amount','trn_diary2_or.product_status'))
		//     		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		//     		  	'trn_diary2_or.product_id=com_product_master.product_id',
		//     		  	array('com_product_master.name_product')
		// 				)
		//     		  	->where('trn_diary2_or.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}

	public function getdataproduct_toto($sort,$doc_no){	
		$sql=$this->db->select()
		    		  	->from(array('trn_diary2_to'=>'trn_diary2_to'),array('trn_diary2_to.seq','trn_diary2_to.product_id','trn_diary2_to.price','trn_diary2_to.quantity','trn_diary2_to.amount','trn_diary2_to.product_status'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_diary2_to.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						)
		    		  	->where('trn_diary2_to.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}

	public function sumivn_to_or($doc_no){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2` where `doc_no` = '$doc_no' and `doc_tp` = 'TI'
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		// change to trn_diary and doc_tp = ti
		// $sql="
		// select 
		// 	sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		// from 
		// 	(select 
		// 		tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
		// 	from 
		// 		(SELECT 
		// 			product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
		// 		FROM 
		// 			`trn_diary2_or` where `doc_no` = '$doc_no'
		// 		group by 
		// 			product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}

	public function sumivn_to_to($doc_no){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2_to` where `doc_no` = '$doc_no'
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}

	public function checkinv_to_or($doc_no){// public function checkinv_to_or(){
		// $sql=$this->db->select()
		//     		  	->from('trn_diary1',array('doc_date'))
		// 				  ->where('doc_no=?', $doc_no)
		// 				  ->where('doc_tp=?', 'TI');
		$sql = "select * from trn_diary1 where doc_no= '$doc_no' and doc_tp = 'TI' ";

		$stmt=$this->db->query($sql);
		$row=$stmt->fetchAll();


		// change to trn_diary1 and doc_tp = ti
		// $sql=$this->db->select()
		//     		  	->from('trn_diary1_or',array('doc_date'));
	    //$row=$sql->query()->fetchAll();
	    return $row;
	}

	public function checkinv_to_to(){
		$sql=$this->db->select()
		    		  	->from('trn_diary1_to',array('doc_date'));
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	///////////
	
	public function getdataproduct_rq($sort){	
		$sql=$this->db->select()
		    		  	->from(array('trn_tdiary2_rq'=>'trn_tdiary2_rq'),array('trn_tdiary2_rq.seq','trn_tdiary2_rq.product_id','trn_tdiary2_rq.price','trn_tdiary2_rq.quantity','trn_tdiary2_rq.amount','trn_tdiary2_rq.product_status'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_tdiary2_rq.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						);
	    $data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function checkinv($doc_no){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_head',array('doc_no','doc_date'))
		    		  	->where('doc_no = ?', $doc_no)
		    			->where('flag1 = ?', 'D');
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	public function checkinv_to(){
		$sql=$this->db->select()
		    		  	->from('trn_tdiary1_to',array('doc_date'));
	    $row=$sql->query()->fetchAll();
	    return $row;
	}

	public function checkinv_tokeyin($date){
		$sql=$this->db->select()
						->from('trn_diary1',array('doc_date'))
						->where('doc_date = ?', $date);
						// ->where('doc_date = ?', '2019-07-01');
		$row=$sql->query()->fetchAll();
		if(!$row){
			$row[0]['doc_date']=$date;
		}
	    return $row;
	}
	public function countinv_tokeyin($date){
		$date = $this->doc_date_pos;
		$sql=$this->db->select()
						->from('trn_diary1',array('doc_date'))
						->where('doc_tp = ?', 'TO')
						->where('doc_date = ?', $date);
		$row=$sql->query()->fetchAll();
		$count=count($row);
	    return $count;
	}
	
	public function checkinv_rq(){
		$sql=$this->db->select()
		    		  	->from('trn_tdiary1_rq',array('doc_date'));
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	public function checkinv_stock($doc_no){
		$sql=$this->db->select()
		    		  	->from('chk_stock',array('doc_number','reg_date'))
		    		  	->where('doc_number = ?', $doc_no)
		    			->group('doc_number');
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	public function getin2shoplistofproduct($doc_no,$product_id){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_list',array('doc_date','seq','product_id','product_name','quantity','flag1','price','barcode','product_status','mfg_date'))
		    		  	->where('doc_no = ?', $doc_no)
		    		  	->where('product_id = ?', $product_id)
		    			->where('flag1 = ?', 'D');
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	public function sumivn($doc_no){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_in2shop_list_tmp` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivn_to($doc_no){
		$sql="
			select 
				sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
			from 
				(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2` 
				WHERE doc_no = '".$doc_no."'
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		// $sql="
		// select 
		// 	sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		// from 
		// 	(select 
		// 		tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
		// 	from 
		// 		(SELECT 
		// 			product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
		// 		FROM 
		// 			`trn_tdiary2_to` 
		// 		group by 
		// 			product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}

	public function sumivn_to_bykeyin(){
		$sql="
				select 
					sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
				from 
					(select 
						tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
					from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_tdiary2_to` 
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivn_rq(){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_tdiary2_rq` 
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivnconfirm($doc_no){
		/*$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";*/
		
		$sql="
		SELECT 
			product_id,sum(quantity) as quantity, sum(amount) as price 
		FROM 
			`trn_diary2` 
		where 
			doc_no='$doc_no' 
		group by 
			doc_no";
					
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivnconfirmto($doc_no){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivnconfirmrq($doc_no){
		/*$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2_rq` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";*/
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,tbl_sumproduct.sum_price as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(amount) as sum_price 
				FROM 
					`trn_diary2_rq` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivnconfirmiq($doc_no){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2_iq` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";
		/*$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,tbl_sumproduct.sum_price as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2_iq` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";*/
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumivnconfirmor($doc_no){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_diary2_or` 
				where 
					doc_no='$doc_no' 
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function update_trn_in2shop_head_tmp($doc_no){
		$sum_quantity=$this->sumivn($doc_no);
		$data_insert=array(
					'quantity'=>$sum_quantity[0]['quantity']
		);
		$where = array(
					'doc_no = ?' => $doc_no
		);
		$rs=$this->db->update('trn_in2shop_head_tmp', $data_insert, $where);
	}
	
	public function tranfertodiary($doc_no,$doc_remark,$status_no,$doc_tp){
		$this->doc_tp=$doc_tp;
		$this->status_no=$status_no;
		$this->doc_no=$this->gendocno($doc_no,$this->doc_tp);
		$this->tranin2shop=$this->gettrnin2shop($doc_no);
		$chk_data2shop=count($this->tranin2shop);
		$up_head_tmp=$this->update_trn_in2shop_head_tmp($doc_no);
		$this->suminv=$this->sumivn($doc_no);
		if(!empty($this->suminv[0]['price'])){
			$this->amount=$this->suminv[0]['price'];
		}else{
			$this->amount="";
		}
		$this->ref_doc_no=$doc_no;
		$this->get_doc_no=$this->getdocno($this->doc_tp);
		$this->productlist=$this->getdataproduct($doc_no,"product_id","","");
		$chk_productlist=count($this->productlist);
		$rs="y";
		$this->db->beginTransaction();
		try {
			if($chk_data2shop>0){
				foreach($this->tranin2shop as $val_data2shop){
					$sql_insert_diary1="
					insert into 
						trn_diary1 
					set 
						corporation_id='$this->corporation_id',
						company_id='$this->company_id',
						branch_id='$this->branch_id',
						doc_date='$this->doc_date_pos',
						doc_time='$this->time',
						doc_no='$this->doc_no',
						doc_tp='$this->doc_tp',
						status_no='$this->status_no',
				    	refer_doc_no='$this->ref_doc_no',
				    	quantity='$val_data2shop[quantity]',
				    	amount='$this->amount',
			    		net_amt='$this->amount',
				    	computer_no='$this->com_no',
				    	doc_remark='$doc_remark',
			    		reg_date='$this->date',
			    		reg_time='$this->time',
			    		reg_user='$this->user_id'
					";
					$this->db->query($sql_insert_diary1);
				}
			}
			if($chk_productlist>0){
				$stock_st=$this->get_doc_no[0]['stock_st'];
				foreach($this->productlist as $val_productlist){
					if(!empty($val_productlist['price'])){
						$this->amount_item=($val_productlist['price']*$val_productlist['quantity']);
					}else{
						$this->amount_item="";
					}
					$sql_insert_diary2="
					insert into 
						trn_diary2 
					set 
						corporation_id='$this->corporation_id',
						company_id='$this->company_id',
						branch_id='$this->branch_id',
						doc_date='$this->doc_date_pos',
						doc_time='$this->time',
						doc_no='$this->doc_no',
						doc_tp='$this->doc_tp',
						status_no='$this->status_no',
						seq='$val_productlist[seq]',
						product_id='$val_productlist[product_id]',
						stock_st='$stock_st',
						price='$val_productlist[price]',
				    	quantity='$val_productlist[quantity]',
				    	amount='$this->amount_item',
			    		net_amt='$this->amount_item',
				    	product_status='N',
						reg_date='$this->date',
			    		reg_time='$this->time',
			    		reg_user='$this->user_id'
					";
					$rs_td2=$this->db->query($sql_insert_diary2);
					$this->getstock=$this->getstokmaster($val_productlist['product_id'],$this->doc_no);
					$this->checkstock=count($this->getstock);
					if(empty($this->getstock[0]['onhand'])){
						$this->getstock[0]['onhand']="";
					}
					$this->onhand=$this->getstock[0]['onhand']+($val_productlist['quantity']*$this->get_doc_no[0]['stock_st']);
					$this->doc_date=$this->getdiary1($this->doc_no);
					$this->arr_date=explode("-",$this->doc_date[0]['doc_date']);		   
					$m_month=$this->arr_date[1];
					$y_year=$this->arr_date[0];
					
					if($this->checkstock>0){
						$sql_updat_stock="
						update 
							com_stock_master 
						set 
							onhand='$this->onhand',
							upd_date='$this->date',
					    	upd_time='$this->time',
					    	upd_user='$this->user_id' 
					    where 
					    	corporation_id='$this->corporation_id' 
							and company_id = '$this->company_id' 
							and branch_id = '$this->branch_id' 
							and product_id = '$val_productlist[product_id]' 
							and month = '$m_month' 
							and year = '$y_year'
						";
						$this->db->query($sql_updat_stock);
					}else{
						$checkproductmaster=$this->getproductmaster($val_productlist['product_id']);
						$count_checkproductmaster=count($checkproductmaster);
						if($count_checkproductmaster==0){
							$getproduct=$this->getin2shoplistofproduct($doc_no,$val_productlist['product_id']);
							
							$sql_update_product_master="
							insert into 
								com_product_master 
							set 
								corporation_id='$this->corporation_id',
								company_id='$this->company_id',
								vendor_id='$this->vendor_id',
								product_id='$val_productlist[product_id]',
								barcode='$getproduct[0][barcode]',
								name_product='$getproduct[0][product_name]',
								name_print='$getproduct[0][product_name]',
								price='$getproduct[0][price]',
								unit='ชิ้น',
								tax_type='V',
								product_set='N',
								reg_date='$this->date',
					    		reg_time='$this->time',
					    		reg_user='$this->user_id'
							";
							$this->db->query($sql_update_product_master);
						}
						$sql_com_stock_master="
						insert into 
							com_stock_master 
						set 
							corporation_id='$this->corporation_id',
							company_id='$this->company_id',
							branch_id='$this->branch_id',
							branch_no='$this->branch_no',
							product_id='$val_productlist[product_id]',
							month='$m_month',
							year='$y_year',
							product_status='N',
							onhand='$this->onhand',
							reg_date='$this->date',
				    		reg_time='$this->time',
				    		reg_user='$this->user_id'
						";
						$this->db->query($sql_com_stock_master);
					}
				}
			}
			$add_docno=$this->get_doc_no[0]['doc_no']+1;
			$sql_update_com_docno="
			update 
				com_doc_no 
			set 
				doc_no='$add_docno',
				upd_date='$this->date',
	    		upd_time='$this->time',
	    		upd_user='$this->user_id' 
	    	where 
	    		corporation_id='$this->corporation_id' 
				and company_id='$this->company_id' 
				and branch_id='$this->branch_id' 
				and doc_tp='$this->doc_tp' 
			";
			$this->db->query($sql_update_com_docno);
			$rs="y";
			$this->db->commit();
		}catch (Exception $e) {
			$this->db->rollback();
			$rs="n";
		}
		if($rs=="y"){
			$this->TrancateTable('trn_in2shop_list_tmp');
			$this->TrancateTable('trn_in2shop_head_tmp');
			$status="y";
		}else{
			$status="n";
		}
		
		$status_arr=array('status'=>$status,'doc_no'=>$this->doc_no);
		return $status_arr;
	}
	
	public function tranin2shopheadtodiary($doc_no,$doc_remark,$status_no,$doc_tp){
		$this->doc_tp=$doc_tp;
		$this->status_no=$status_no;
		$this->doc_no=$this->gendocno($doc_no,$this->doc_tp);
		$this->tranin2shop=$this->gettrnin2shop($doc_no);
		$chk_data2shop=count($this->tranin2shop);
		$up_head_tmp=$this->update_trn_in2shop_head_tmp($doc_no);

		$this->suminv=$this->sumivn($doc_no);
		if(!empty($this->suminv[0]['price'])){
			$this->amount=$this->suminv[0]['price'];
		}else{
			$this->amount="";
		}
		$this->ref_doc_no=$doc_no;
		$this->get_doc_no=$this->getdocno($this->doc_tp);
		
		$this->productlist=$this->getdataproduct($doc_no,"product_id","","");
		$chk_productlist=count($this->productlist);
		
	    if($chk_data2shop>0){
		    foreach($this->tranin2shop as $val_data2shop){
		    	$insert_data2shop=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->doc_date_pos,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'doc_tp'=>$this->doc_tp,
					'status_no'=>$this->status_no,
			    	'refer_doc_no'=>$this->ref_doc_no,
			    	'quantity'=>$val_data2shop['quantity'],
			    	'amount'=>$this->amount,
		    		'net_amt'=>$this->amount,
			    	'computer_no'=>$this->com_no,
			    	'doc_remark'=>$doc_remark,
		    		'reg_date'=>$this->date,
		    		'reg_time'=>$this->time,
		    		'reg_user'=>$this->user_id
				);
				$rs_data2shop=$this->db->insert('trn_diary1', $insert_data2shop);
				if($rs_data2shop){
					if($chk_productlist>0){
						foreach($this->productlist as $val_productlist){
							if(!empty($val_productlist['price'])){
								$this->amount_item=($val_productlist['price']*$val_productlist['quantity']);
							}else{
								$this->amount_item="";
							}
							$insert_productlist=array(
								'corporation_id'=>$this->corporation_id,
								'company_id'=>$this->company_id,
								'branch_id'=>$this->branch_id,
								'doc_date'=>$this->doc_date_pos,
								'doc_time'=>$this->time,
								'doc_no'=>$this->doc_no,
								'doc_tp'=>$this->doc_tp,
								'status_no'=>$this->status_no,
								'seq'=>$val_productlist['seq'],
								'product_id'=>$val_productlist['product_id'],
								'stock_st'=>$this->get_doc_no[0]['stock_st'],
								'price'=>$val_productlist['price'],
						    	'quantity'=>$val_productlist['quantity'],
						    	'amount'=>$this->amount_item,
					    		'net_amt'=>$this->amount_item,
						    	'product_status'=>'N',
								'reg_date'=>$this->date,
					    		'reg_time'=>$this->time,
					    		'reg_user'=>$this->user_id
							);
							$rs_productlist=$this->db->insert('trn_diary2', $insert_productlist);
							
							if($rs_productlist){
								$this->getstock=$this->getstokmaster($val_productlist['product_id'],$this->doc_no);
								$this->checkstock=count($this->getstock);
								if(empty($this->getstock[0]['onhand'])){
									$this->getstock[0]['onhand']="";
								}
								$this->onhand=$this->getstock[0]['onhand']+($val_productlist['quantity']*$this->get_doc_no[0]['stock_st']);
								$this->doc_date=$this->getdiary1($this->doc_no);
								$this->arr_date=explode("-",$this->doc_date[0]['doc_date']);		   
								$m_month=$this->arr_date[1];
								$y_year=$this->arr_date[0];	
								
								if($this->checkstock>0){
									$data_update=array(
											'onhand'=>$this->onhand,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'product_id = ?' => $val_productlist['product_id'],
												'month = ?' => $m_month,
												'year = ?' => $y_year
									);
									$rs_update=$this->db->update('com_stock_master', $data_update, $where_update);
								}else{
									$checkproductmaster=$this->getproductmaster($val_productlist['product_id']);
									$count_checkproductmaster=count($checkproductmaster);
									if($count_checkproductmaster==0){
										$getproduct=$this->getin2shoplistofproduct($doc_no,$val_productlist['product_id']);
										$data_insert_productmaster=array(
												'corporation_id'=>$this->corporation_id,
												'company_id'=>$this->company_id,
												'vendor_id'=>$this->vendor_id,
												'product_id'=>$val_productlist['product_id'],
												'barcode'=>$getproduct[0]['barcode'],
												'name_product'=>$getproduct[0]['product_name'],
												'name_print'=>$getproduct[0]['product_name'],
												'price'=>$getproduct[0]['price'],
												'unit'=>'ชิ้น',
												'tax_type'=>'V',
												'product_set'=>'N',
												'reg_date'=>$this->date,
									    		'reg_time'=>$this->time,
									    		'reg_user'=>$this->user_id
										);
										$rs_productmaster=$this->db->insert('com_product_master', $data_insert_productmaster);
										
									}
									
									$data_insert=array(
											'corporation_id'=>$this->corporation_id,
											'company_id'=>$this->company_id,
											'branch_id'=>$this->branch_id,
											'branch_no'=>$this->branch_no,
											'product_id'=>$val_productlist['product_id'],
											'month'=>$m_month,
											'year'=>$y_year,
											'product_status'=>'N',
											'onhand'=>$this->onhand,
											'reg_date'=>$this->date,
								    		'reg_time'=>$this->time,
								    		'reg_user'=>$this->user_id
									);
									$rs_update=$this->db->insert('com_stock_master', $data_insert);
								}
								if($rs_update){
									$add_docno=$this->get_doc_no[0]['doc_no']+1;
									$data_update_docno=array(
											'doc_no'=>$add_docno,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update_docno = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'doc_tp = ?' => $this->doc_tp
									);
									$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
									if($rs_update_docno){
										$sql_update_inv="
										update 
											trn_in2shop_head 
										set 
											status_transfer='T' 
										where 
											doc_no='$this->ref_doc_no'
										";
										$rs_update_inv=$this->db->query($sql_update_inv);
										if($rs_update_inv){
											$status="y";
											$this->TrancateTable('trn_in2shop_list_tmp');
											$this->TrancateTable('trn_in2shop_head_tmp');
										}else{
											$status="trn_in2shop_head";
										}
									}
								}else{
									$status="com_stock_master";
								}
							}else{
								$status="trn_diary2";
							}
						}
					}
				}else{
					$status="trn_diary1";
				}
		    }
	    }
	    $status_arr=array('status'=>$status,'doc_no'=>$this->doc_no);
		return $status_arr;
	}
	
	public function gendocno($doc_no,$type_docno){
		$sql="select doc_prefix1,def_value,doc_prefix2,run_no from conf_doc_no";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		for($i=1;$i<$data[0]['run_no'];$i++){
			$this->run_no.="0";
		}
		$d_run_no=$data[0]['run_no']*(-1);
    	$data_docno=$this->getdocno($type_docno);
		$convdocno=$this->run_no.$data_docno[0]['doc_no'];
		$convdocno=substr($convdocno,$d_run_no);
		//$doc_no=$data_docno[0]['company_id'].$data_docno[0]['branch_id'].$data_docno[0]['doc_tp']."-".$data_docno[0]['branch_no']."-".$convdocno;
		$doc_no=$data[0]['doc_prefix1'].$data_docno[0]['doc_tp']."-".$data[0]['def_value']."-".$convdocno;
		return $doc_no;
	}
	
	public function gettrnin2shop($doc_no){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_head_tmp',array('corporation_id','company_id','branch_id','doc_date','doc_no','quantity','refer_doc_no'))
		    		  	->where('doc_no = ?', $doc_no)
		    			->where('flag1 = ?', 'D');
	    $data2shop=$sql->query()->fetchAll();
	    return $data2shop;
	}
	
	public function gettrnto($tbl){
		$sql=$this->db->select()
		    		  	->from($tbl,array('corporation_id','company_id','branch_id','doc_date','doc_no','quantity'));
	    $data2shop=$sql->query()->fetchAll();
	    return $data2shop;
	}
	
	public function gettrntowithdoc($tbl,$doc_no){
		$sql=$this->db->select()
		    		  	->from($tbl,array('corporation_id','company_id','branch_id','doc_date','doc_no','quantity'))
						->where('doc_no=?',$doc_no);
	    $data2shop=$sql->query()->fetchAll();
	    return $data2shop;
	}

	public function getdocno($type_docno){
		$sql=$this->db->select()
	    		   ->from('com_doc_no',array('corporation_id','company_id','branch_id','branch_no','doc_tp','doc_no','stock_st'))
	    		   ->where('doc_tp=?',$type_docno)
	    		   ->where('company_id=?',$this->company_id);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function getstokmaster($product_id,$doc_no){
		$this->doc_date=$this->getdiary1($doc_no);
		$conv_date=date('Y-n-d', strtotime($this->doc_date[0]['doc_date']));
		$this->arr_date=explode("-",$conv_date);		   
		$m_month=$this->arr_date[1];
		$y_year=$this->arr_date[0];	
		$sql=$this->db->select()
	    		   ->from('com_stock_master',array('product_id','month','year','product_status','begin','onhand','allocate'))
	    		   ->where('corporation_id=?',$this->corporation_id)
	    		   ->where('company_id=?',$this->company_id)
	    		   ->where('branch_id=?',$this->branch_id)
	    		   ->where('product_id=?',$product_id)
	    		   ->where('month=?',$m_month)
	    		   ->where('year=?',$y_year);
	    		   
    	$data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getdiary1($doc_no){
		$sql=$this->db->select()
	    		   ->from('trn_diary1',array('doc_date','doc_no'))
	    		   ->where('doc_no=?',$doc_no);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function getproductmaster($product_id){
		$sql=$this->db->select()
	    		   ->from('com_product_master',array('product_id','name_product','price'))
	    		   ->where('product_id=?',$product_id)
	    		   ->orWhere('barcode=?',$product_id);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function TrancateTable($table){
		if($table=="")return false;
		$rs=$this->db->query('TRUNCATE TABLE '.$table);
		return $rs;
	}
	
	public function checkpassword($doc_no,$pwd){
		$getinv=$this->checkinv($doc_no);
		$doc_date=$getinv[0]['doc_date'];
		$ex_doc_date=explode('-',$doc_date);
		$out=((($ex_doc_date[2]*$ex_doc_date[1])*9)/7);
		$data=explode('.',$out);
		return $data[0];
	}

	public function checkpwd_tokeyin($date,$pwd){
		$getinv=$this->checkinv_tokeyin($date);
		$count=$this->countinv_tokeyin($date);
		$doc_date=$getinv[0]['doc_date'];
		// $ex_doc_date=explode('-',$doc_date);
		if($count=='0'){//กรณีไม่มีพบเอกสาร TO ในวันนั้น ให้ count = ( ( 0+9+วัน ) x เดือน x 9/7 )
		$ex_doc_date=explode('-',$doc_date);
		$out=(((( ($count+9) + $ex_doc_date[2])*$ex_doc_date[1])*9)/7);
		}else{//กรณีพบเอกสาร TO ในวันนั้น ให้ ( (5*count) + วัน ) x เดือน x 9/7 )
		$ex_doc_date=explode('-',$doc_date);
		$out=(((( ($count*5) + $ex_doc_date[2])*$ex_doc_date[1])*9)/7);
		}
		$data=explode('.',$out);
		return $data[0];
		// return $count;
	}
	public function checkcount_tokeyin($date,$pwd){
		$count=$this->countinv_tokeyin($date);
		return $count;
	}
	
	public function checkheaddocno($doc_no){
		$sql=$this->db->select()
	    		   ->from('trn_in2shop_head',array('doc_no'))
	    		   ->where('doc_no=?',$doc_no);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function checkdiary1docno($doc_no){
		$sql=$this->db->select()
	    		   ->from('trn_diary1',array('refer_doc_no'))
	    		   ->where('refer_doc_no=?',$doc_no);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function checkproduct($product_id){
		$sql=$this->db->select()
	    		   ->from('com_product_master',array('product_id','type'))
	    		   ->where('product_id=?',$product_id)
	    		   ->orWhere('barcode=?',$product_id);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function checkproducttestterfix($product_id){
		$sql="
		SELECT 
			a.product,a.quantity as qty,a.allocate,sum(b.quantity) as quantity  
		FROM 
			`com_product_testter` as a 
		LEFT JOIN 
			trn_tdiary2_to as b 
		ON 
			a.product=b.product_id
		WHERE 
			a.shop='$this->branch_id' and a.product='$product_id'
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		return $rs;
	}
	
	public function gen_tmp_diary($data){
		$check=$this->gettrnin2shop($data['doc_no']);
		$check_head=count($check);
		if($check_head==0){
			$insert_tem_head=array(
				'corporation_id'=>$this->corporation_id,
				'company_id'=>$this->company_id,
				'branch_id'=>$this->branch_id,
				'doc_date'=>$this->date,
				'doc_no'=>$data['doc_no'],
				'flag1'=>'D',
				'quantity'=>'',
		    	'reg_date'=>$this->date,
		    	'reg_time'=>$this->time,
		    	'reg_user'=>$this->user_id
			);
			$rs_tmp_head=$this->db->insert($data['tbl_head'], $insert_tem_head);
		}
		//if($rs_tmp_head){
			$check_product=$this->checktmplistproduct($data['doc_no'],$data['product_id']);
			$check_product_list=count($check_product);
			$seq=$this->getseq($data['doc_no'],$data['product_id']);
			$get_product=$this->getproductmaster($data['product_id']);
			//if($check_product_list==0){
				$insert_tem_list=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_no'=>$data['doc_no'],
					'seq'=>$seq,
					'product_id'=>$data['product_id'],
					'product_name'=>$get_product[0]['name_product'],
					'quantity'=>$data['qty'],
					'flag1'=>'D',
					'price'=>$get_product[0]['price'],
					'product_status'=>$data['type_product'],
			    	'reg_date'=>$this->date,
			    	'reg_time'=>$this->time,
			    	'reg_user'=>$this->user_id
				);
				$rs_tmp_list=$this->db->insert($data['tbl_list'], $insert_tem_list);
				
				$sum_qty=$this->sumivn($data['doc_no']);
				$data_update_qty=array(
					'quantity'=>$sum_qty[0]['quantity']
				);
				$where = array(
					'doc_no = ?' => $data['doc_no']
				);
				$rs_updat_qty_head=$this->db->update($data['tbl_head'], $data_update_qty, $where);
				
				if($rs_updat_qty_head){
					$status="y";
				}else{
					$status="n";
				}
			//}
		//}
		return $status;
	}
	
	public function gen_tmp_diary_to($data){
		$check=$this->gettrnto('trn_tdiary1_to');
		$check_head=count($check);
		if($check_head==0){
			$insert_tem_head=array(
				'corporation_id'=>$this->corporation_id,
				'company_id'=>$this->company_id,
				'branch_id'=>$this->branch_id,
				'doc_date'=>$this->date,
				'doc_no'=>$data['doc_no'],
				'quantity'=>'',
		    	'reg_date'=>$this->date,
		    	'reg_time'=>$this->time,
		    	'reg_user'=>$this->user_id
			);
			$rs_tmp_head=$this->db->insert($data['tbl_head'], $insert_tem_head);
		}
		//if($rs_tmp_head){
			$check_product=$this->checktmplistproduct_to($data['product_id']);
			$check_product_list=count($check_product);
			$seq=$this->getseq_to($data['product_id']);
			$get_product=$this->getproductmaster($data['product_id']);
			//if($check_product_list==0){
				$insert_tem_list=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_no'=>$data['doc_no'],
					'seq'=>$seq,
					'product_id'=>$data['product_id'],
					'quantity'=>$data['qty'],
					'price'=>$get_product[0]['price'],
					'product_status'=>$data['type_product'],
			    	'reg_date'=>$this->date,
			    	'reg_time'=>$this->time,
			    	'reg_user'=>$this->user_id
				);
				$rs_tmp_list=$this->db->insert($data['tbl_list'], $insert_tem_list);
				$sum_qty=$this->sumivn_to_bykeyin();	// $sum_qty=$this->sumivn_to($data['doc_no']); // dup in transfer out by keyin
				$data_update_qty=array(
					'quantity'=>$sum_qty[0]['quantity']
				);
				// $where = array(
				// 	'doc_no = ?' => $data['doc_no']
				// );
				// $rs_updat_qty_head=$this->db->update($data['tbl_head'], $data_update_qty, $where);
				$rs_updat_qty_head=$this->db->update($data['tbl_head'], $data_update_qty);
				
				if($rs_updat_qty_head){
					$status="y";
				}else{
					$status="n";
				}
			//}
		//}
		return $status;
	}
	
	
	public function gen_tmp_diary_rq($data){
		$check=$this->gettrnto('trn_tdiary1_rq');
		$check_head=count($check);
		if($check_head==0){
			$insert_tem_head=array(
				'corporation_id'=>$this->corporation_id,
				'company_id'=>$this->company_id,
				'branch_id'=>$this->branch_id,
				'doc_date'=>$this->date,
				'doc_no'=>$data['doc_no'],
				'quantity'=>'',
		    	'reg_date'=>$this->date,
		    	'reg_time'=>$this->time,
		    	'reg_user'=>$this->user_id
			);
			$rs_tmp_head=$this->db->insert($data['tbl_head'], $insert_tem_head);
		}
		//if($rs_tmp_head){
			$check_product=$this->checktmplistproduct_to($data['product_id']);
			$check_product_list=count($check_product);
			$seq=$this->getseq_rq($data['product_id']);
			$get_product=$this->getproductmaster($data['product_id']);
			//if($check_product_list==0){
				$insert_tem_list=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_no'=>$data['doc_no'],
					'seq'=>$seq,
					'product_id'=>$data['product_id'],
					'quantity'=>$data['qty'],
					'price'=>$get_product[0]['price'],
					'product_status'=>$data['type_product'],
			    	'reg_date'=>$this->date,
			    	'reg_time'=>$this->time,
			    	'reg_user'=>$this->user_id
				);
				$rs_tmp_list=$this->db->insert($data['tbl_list'], $insert_tem_list);
				
				$sum_qty=$this->sumivn_rq();
				$data_update_qty=array(
					'quantity'=>$sum_qty[0]['quantity']
				);
				$where = array(
					'doc_no = ?' => $data['doc_no']
				);
				$rs_updat_qty_head=$this->db->update($data['tbl_head'], $data_update_qty, $where);
				
				if($rs_updat_qty_head){
					$status="y";
				}else{
					$status="n";
				}
			//}
		//}
		return $status;
	}
	
	public function checktmplistproduct($doc_no,$product_id){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_list_tmp',array('doc_no'))
		    		  	->where('doc_no = ?', $doc_no)
		    		  	->where('product_id = ?', $product_id);
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	public function checktmplistproduct_to($product_id){
		$sql=$this->db->select()
		    		  	->from('trn_tdiary2_to',array('product_id'))
		    		  	->where('product_id = ?', $product_id);
	    $row=$sql->query()->fetchAll();
	    return $row;
	}
	
	public function getseq($doc_no,$product_id){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_list_tmp',array('max(seq) as maxseq'))
		    		  	->where('doc_no = ?', $doc_no);
	    $row=$sql->query()->fetchAll();
	    $seq=$row[0]['maxseq']+1;
	    return $seq;
	}
	
	public function getseq_to($product_id){
		$sql=$this->db->select()
		    		  	->from('trn_tdiary2_to',array('max(seq) as maxseq'));
	    $row=$sql->query()->fetchAll();
	    $seq=$row[0]['maxseq']+1;
	    return $seq;
	}
	
	public function getseq_rq($product_id){
		$sql=$this->db->select()
		    		  	->from('trn_tdiary2_rq',array('max(seq) as maxseq'));
	    $row=$sql->query()->fetchAll();
	    $seq=$row[0]['maxseq']+1;
	    return $seq;
	}
	
	public function delettemlist($doc_no,$seq){
		$check=count($seq);
		if($check>0){
			foreach($seq as $val_seq){
				$where = array(
						'doc_no = ?' => $doc_no,
						'seq = ?' => $val_seq
				);
				$rs=$this->db->delete('trn_in2shop_list_tmp', $where);
			}
		}
		$sum_qty=$this->sumivn($doc_no);
		$data_update_qty=array(
			'quantity'=>$sum_qty[0]['quantity']
		);
		$where = array(
			'doc_no = ?' => $doc_no
		);
		$rs_updat_qty_head=$this->db->update('trn_in2shop_head_tmp', $data_update_qty, $where);
		if($rs_updat_qty_head){
			$status="y";
		}
		return $status;
	}
	
	public function delettemlist_to($seq){
		$check=count($seq);
		if($check>0){
			foreach($seq as $val_seq){
				$where = array(
						'seq = ?' => $val_seq
				);
				$rs=$this->db->delete('trn_tdiary2_to', $where);
			}
		}
		$sum_qty=$this->sumivn_to_bykeyin();	// $sum_qty=$this->sumivn_to();
		$data_update_qty=array(
			'quantity'=>$sum_qty[0]['quantity']
		);
		/*$where = array(
			'doc_no = ?' => $doc_no
		);*/
		$rs_updat_qty_head=$this->db->update('trn_tdiary1_to', $data_update_qty);
		if($rs_updat_qty_head){
			$status="y";
		}
		return $status;
	}
	
	public function delettemlist_rq($seq){
		$check=count($seq);
		if($check>0){
			foreach($seq as $val_seq){
				$where = array(
						'seq = ?' => $val_seq
				);
				$rs=$this->db->delete('trn_tdiary2_rq', $where);
			}
		}
		$sum_qty=$this->sumivn_rq();
		$data_update_qty=array(
			'quantity'=>$sum_qty[0]['quantity']
		);
		/*$where = array(
			'doc_no = ?' => $doc_no
		);*/
		$rs_updat_qty_head=$this->db->update('trn_tdiary1_rq', $data_update_qty);
		if($rs_updat_qty_head){
			$status="y";
		}
		return $status;
	}
	
	public function canceltranfer(){
		$rs_list=$this->TrancateTable('trn_in2shop_list_tmp');
		if($rs_list){
			$rs_head=$this->TrancateTable('trn_in2shop_head_tmp');
			if($rs_head){
				$status="y";
			}
		}
		return $status;
	}
	
	public function canceltranfer_to(){
		$rs_list=$this->TrancateTable('trn_tdiary2_to');
		if($rs_list){
			$rs_head=$this->TrancateTable('trn_tdiary1_to');
			if($rs_head){
				$status="y";
			}
		}
		return $status;
	}
	
	public function canceltranfer_rq(){
		$rs_list=$this->TrancateTable('trn_tdiary2_rq');
		if($rs_list){
			$rs_head=$this->TrancateTable('trn_tdiary1_rq');
			if($rs_head){
				$status="y";
			}
		}
		return $status;
	}
	
	public function cancelkeytranfer($doc_no){
		$sql="
		delete 
			trn_in2shop_list_tmp,trn_in2shop_head_tmp 
		from 
			trn_in2shop_list_tmp 
		right join 
			trn_in2shop_head_tmp 
		on 
			trn_in2shop_list_tmp.doc_no=trn_in2shop_head_tmp.doc_no 
		where 
			trn_in2shop_head_tmp.doc_no='$doc_no'";
		$stmt=$this->db->query($sql);
		if($stmt){
			$status="y";
		}else{
			$status="n";
		}
		return $status;
	}
	
	
	public function clear_diary($doc_no){
		$sql="
		delete 
			trn_diary2,trn_diary1 
		from 
			trn_diary2 
		right join 
			trn_diary1 
		on 
			trn_diary2.doc_no=trn_diary1.doc_no 
		where 
			trn_diary1.refer_doc_no='$doc_no'";
		$stmt=$this->db->query($sql);
	}
	
	public function getdocnocheckstock($type){
		$sql=$this->db->select()
		    		  	->from(array('chk_stock'=>'chk_stock'),array('chk_stock.reg_date','sum(chk_stock.dif) as qty','chk_stock.doc_number'))
		    		  	->joinLeft(array('trn_diary1'=>'trn_diary1'),
		    		  	'chk_stock.doc_number=trn_diary1.refer_doc_no and chk_stock.doc_tp=trn_diary1.doc_tp',
		    		  	array('trn_diary1.doc_no','trn_diary1.refer_doc_no')
						)
		    		  	->where('chk_stock.doc_tp = ?', $type)
		    		  	->group('chk_stock.doc_number')
		    		  	->order('chk_stock.reg_date DESC')
		    		  	->limit(1);
	    $data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function chk_password_checkstock($pwd,$group_id){
		/*$sql=$this->db->select()
		    		  	->from('chk_stock',array('reg_date'))
		    		  	->where('doc_number = ?', $doc_no)
		    		  	->group('doc_number');
	    $data=$sql->query()->fetchAll();*/
		$sql=$this->db->select()
		    		  	->from('conf_employee',array('employee_id','user_id'))
		    		  	->where('password_id = ?', $pwd)
		    		  	->where('group_id = ?', $group_id);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getto($doc_tp){
		$sql=$this->db->select()
		    		  	->from('com_doc_status',array('doc_tp','status_no','description'))
		    		  	->where('doc_tp = ?', $doc_tp)
		    		  	->where('start_date <= ?', $this->date)
		    		  	->where('end_date >= ?', $this->date)
		    		  	->order('status_no');
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}

	public function gettype_id($branch_id){
		$sql=$this->db->select()
		    		  	->from('com_shop_destination',array('type_id','branch_id'))
		    		  	->where('branch_id = ?', $branch_id);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getstatusno($status_no){
		$sql=$this->db->select()
		    		  	->from('com_doc_status',array('doc_tp','status_no','description'))
		    		  	->where('status_no = ?', $status_no)
		    		  	->order('status_no');
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function docnobydocrefer($doc_no){
		$sql=$this->db->select()
		    		  	->from('trn_diary1',array('branch_id','doc_date','doc_time','doc_date','doc_tp','doc_time','doc_no','status_no','flag','refer_doc_no','print_no','doc_remark'))
		    		  	->where('doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getdiary2($doc_no){
		$sql=$this->db->select()
		    		  	->from(array('trn_diary2'=>'trn_diary2'),array('trn_diary2.doc_no','trn_diary2.product_id','trn_diary2.price','trn_diary2.quantity','trn_diary2.amount'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_diary2.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						)
		    		  	->where('trn_diary2.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function docnobydocto($doc_no){
		$sql=$this->db->select()
		    		  	->from('trn_diary1',array('branch_id','doc_date','doc_time','doc_date','doc_time','doc_no','status_no','flag','refer_doc_no','print_no','doc_remark'))
		    		  	->where('doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function docnobydocreferto($doc_no_refer){
		$sql=$this->db->select()
		    		  	->from('trn_diary1',array('branch_id','doc_date','doc_time','doc_date','doc_time','doc_no','status_no','print_no','doc_remark'))
		    		  	->where('refer_doc_no = ?', $doc_no_refer);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getdiary2to($doc_no){
		$sql=$this->db->select()
		    		  	->from(array('trn_diary2'=>'trn_diary2'),array('trn_diary2.doc_no','trn_diary2.product_id','trn_diary2.price','trn_diary2.quantity','trn_diary2.amount'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_diary2.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						)
		    		  	->where('trn_diary2.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getdiary2rq($doc_no){
		$sql=$this->db->select()
		    		  	->from(array('trn_diary2_rq'=>'trn_diary2_rq'),array('trn_diary2_rq.doc_no','trn_diary2_rq.product_id','trn_diary2_rq.price','trn_diary2_rq.quantity','trn_diary2_rq.amount'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_diary2_rq.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						)
		    		  	->where('trn_diary2_rq.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getdiary2or($doc_no){
		$sql=$this->db->select()
		    		  	->from(array('trn_diary2_or'=>'trn_diary2_or'),array('trn_diary2_or.doc_no','trn_diary2_or.product_id','trn_diary2_or.price','trn_diary2_or.quantity','trn_diary2_or.amount','trn_diary2_or.short_qty','trn_diary2_or.short_amt','trn_diary2_or.ret_short_qty'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_diary2_or.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						)
		    		  	->where('trn_diary2_or.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function getdiary2iq($doc_no){
		$sql=$this->db->select()
		    		  	->from(array('trn_diary2_iq'=>'trn_diary2_iq'),array('trn_diary2_iq.doc_no','trn_diary2_iq.product_id','trn_diary2_iq.price','trn_diary2_iq.quantity','trn_diary2_iq.amount'))
		    		  	->joinLeft(array('com_product_master'=>'com_product_master'),
		    		  	'trn_diary2_iq.product_id=com_product_master.product_id',
		    		  	array('com_product_master.name_product')
						)
		    		  	->where('trn_diary2_iq.doc_no = ?', $doc_no);
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function checkproducttesternew($product_id,$tbl){
		$sql="SELECT sum(quantity) AS sum_quan FROM trn_diary2 WHERE doc_tp='TO' AND status_no='25' AND product_id='".$product_id."' AND doc_date LIKE '".substr($this->doc_date_pos,0,7)."%' ";	
		$diary=$this->db->fetchOne($sql);

		$sql="SELECT sum(quantity) FROM trn_tdiary2_to WHERE product_id='".$product_id."'";
		$tdiary=$this->db->fetchOne($sql);
		return $diary+$tdiary;
	}

/* production
	public function checkproducttester($product_id,$tbl){
		$tbl1=$tbl['tbl1'];
		$tbl3=$tbl['tbl3'];
		$sql="
		select 
			tbl.*,tbl.qty+tbl.qty_tmp as sum_qty 
		from 
			(SELECT 
				a.product_id,
				sum(COALESCE(c.quantity,0.00)) as qty,
				b.onhand,b.month,year,
				sum(COALESCE(e.quantity,0.00)) as qty_tmp 
			FROM 
				com_product_master as a 
			left join 
				$tbl1 as c 
			on 
				a.product_id=c.product_id  and substring(doc_date,1,7)='".$this->year_month."' 
			left join 
				$tbl3 as e on a.product_id=e.product_id 
			left join 
				com_stock_master as b 
			on 
				a.product_id=b.product_id and b.month='".$this->month."' and b.year='".$this->year."' 
			where 
				a.product_id='".$product_id."' or a.barcode='".$product_id."'
			) as tbl";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		
		$sql="SELECT sum(quantity) AS sum_quan FROM $tbl3 WHERE product_id='".$product_id."'";
		
		$qq = $this->db->fetchOne($sql);
		$data['0']['qty_to_tester']=$qq;
		$data['0']['sql']=$sql;
		return $data;
	}
/*production*/	

	public function checkproducttester($product_id,$tbl){
		$tbl1=$tbl['tbl1'];
		$tbl3=$tbl['tbl3'];
		$sql="
		select 
			tbl.*,tbl.qty+tbl.qty_tmp as sum_qty 
		from 
			(SELECT 
				a.product_id,
				sum(COALESCE(c.quantity,0.00)) as qty,
				b.onhand,b.month,year,
				sum(COALESCE(e.quantity,0.00)) as qty_tmp 
			FROM 
				com_product_master as a 
			left join 
				$tbl1 as c 
			on 
				a.product_id=c.product_id  and substring(doc_date,1,7)='".$this->year_month."' 
			left join 
				$tbl3 as e on a.product_id=e.product_id 
			left join 
				com_stock_master as b 
			on 
				a.product_id=b.product_id and b.month='".$this->month."' and b.year='".$this->year."' 
			where 
				a.product_id='".$product_id."' or a.barcode='".$product_id."'
			) as tbl";
				
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();

		$sql="SELECT sum(quantity) AS sum_quan FROM $tbl3 WHERE product_id='".$product_id."'";
		$qq = $this->db->fetchOne($sql);

		$data['0']['qty_to_tester']=$qq;
		$data['0']['sql']=$sql;
		return $data;
	}
/*test*/

	public function tranin2shopheadtodiary_to($doc_no,$doc_remark,$status_no,$doc_tp,$inv){
		$this->doc_tp=$doc_tp;
		$this->status_no=$status_no;
		$this->doc_no=$this->gendocno('',$this->doc_tp);
		$this->tranin2shop=$this->gettrntowithdoc('trn_diary1',$doc_no);   //$this->tranin2shop=$this->gettrnto('trn_tdiary1_to');
		$chk_data2shop=count($this->tranin2shop);
		//$up_head_tmp=$this->update_trn_in2shop_head_tmp($doc_no);
		$this->suminv=$this->sumivn_to($doc_no);	//$this->suminv=$this->sumivn_to();
		if(!empty($this->suminv[0]['price'])){
			$this->amount=$this->suminv[0]['price'];
		}else{
			$this->amount="";
		}
		$this->ref_doc_no=$doc_no;
		$this->get_doc_no=$this->getdocno($this->doc_tp);
		$this->productlist=$this->getdataproduct_to_withdoc($doc_no);	//$this->productlist=$this->getdataproduct_to("product_id");
		//$view = $obj->getdataproduct_to_withdoc($doc_no);
		$chk_productlist=count($this->productlist);
		
	    if($chk_data2shop>0){
		    foreach($this->tranin2shop as $val_data2shop){
		    	$insert_data2shop=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->doc_date_pos,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'doc_tp'=>$this->doc_tp,
					'status_no'=>$this->status_no,
			    	'refer_doc_no'=>$doc_no,// 'refer_doc_no'=>$inv,	issue inv checked only first line
			    	'quantity'=>$val_data2shop['quantity'],
			    	'amount'=>$this->amount,
		    		'net_amt'=>$this->amount,
			    	'computer_no'=>$this->com_no,
			    	'doc_remark'=>$doc_remark,
		    		'reg_date'=>$this->date,
		    		'reg_time'=>$this->time,
		    		'reg_user'=>$this->user_id
				);
				$rs_data2shop=$this->db->insert('trn_diary1', $insert_data2shop);
				if($rs_data2shop){
					if($chk_productlist>0){
						foreach($this->productlist as $val_productlist){
							if(!empty($val_productlist['price'])){
								$this->amount_item=($val_productlist['price']*$val_productlist['quantity']);
							}else{
								$this->amount_item="";
							}
							$insert_productlist=array(
								'corporation_id'=>$this->corporation_id,
								'company_id'=>$this->company_id,
								'branch_id'=>$this->branch_id,
								'doc_date'=>$this->doc_date_pos,
								'doc_time'=>$this->time,
								'doc_no'=>$this->doc_no,
								'doc_tp'=>$this->doc_tp,
								'status_no'=>$this->status_no,
								'seq'=>$val_productlist['seq'],
								'product_id'=>$val_productlist['product_id'],
								'stock_st'=>$this->get_doc_no[0]['stock_st'],
								'price'=>$val_productlist['price'],
						    	'quantity'=>$val_productlist['quantity'],
						    	'amount'=>$this->amount_item,
					    		'net_amt'=>$this->amount_item,
						    	'product_status'=>'N',
								'reg_date'=>$this->date,
					    		'reg_time'=>$this->time,
					    		'reg_user'=>$this->user_id
							);
							$rs_productlist=$this->db->insert('trn_diary2', $insert_productlist);
							if($this->status_no=="30"){
								$sql_upd_testter="
								update 
									com_product_testter 
								set 
									allocate=allocate+$val_productlist[quantity] 
								where
									product='$val_productlist[product_id]' 
									and shop='$this->branch_id'
								";
								$this->db->query($sql_upd_testter);
								
							}
							
							if($rs_productlist){
								$this->getstock=$this->getstokmaster($val_productlist['product_id'],$this->doc_no);
								$this->checkstock=count($this->getstock);
								if(empty($this->getstock[0]['onhand'])){
									$this->getstock[0]['onhand']="";
								}
								$this->onhand=$this->getstock[0]['onhand']+($val_productlist['quantity']*$this->get_doc_no[0]['stock_st']);
								$this->doc_date=$this->getdiary1($this->doc_no);
								$this->arr_date=explode("-",$this->doc_date[0]['doc_date']);		   
								$m_month=$this->arr_date[1];
								$y_year=$this->arr_date[0];	
								
								if($this->checkstock>0){
									$data_update=array(
											'onhand'=>$this->onhand,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'product_id = ?' => $val_productlist['product_id'],
												'month = ?' => $m_month,
												'year = ?' => $y_year
									);
									$rs_update=$this->db->update('com_stock_master', $data_update, $where_update);
								}else{
									$checkproductmaster=$this->getproductmaster($val_productlist['product_id']);
									$count_checkproductmaster=count($checkproductmaster);
									if($count_checkproductmaster==0){
										$getproduct=$this->getin2shoplistofproduct($doc_no,$val_productlist['product_id']);
										$data_insert_productmaster=array(
												'corporation_id'=>$this->corporation_id,
												'company_id'=>$this->company_id,
												'vendor_id'=>$this->vendor_id,
												'product_id'=>$val_productlist['product_id'],
												'barcode'=>$getproduct[0]['barcode'],
												'name_product'=>$getproduct[0]['product_name'],
												'name_print'=>$getproduct[0]['product_name'],
												'price'=>$getproduct[0]['price'],
												'unit'=>'ชิ้น',
												'tax_type'=>'V',
												'product_set'=>'N',
												'reg_date'=>$this->date,
									    		'reg_time'=>$this->time,
									    		'reg_user'=>$this->user_id
										);
										$rs_productmaster=$this->db->insert('com_product_master', $data_insert_productmaster);
										
									}
									
									$data_insert=array(
											'corporation_id'=>$this->corporation_id,
											'company_id'=>$this->company_id,
											'branch_id'=>$this->branch_id,
											'branch_no'=>$this->branch_no,
											'product_id'=>$val_productlist['product_id'],
											'month'=>$m_month,
											'year'=>$y_year,
											'product_status'=>'N',
											'onhand'=>$this->onhand,
											'reg_date'=>$this->date,
								    		'reg_time'=>$this->time,
								    		'reg_user'=>$this->user_id
									);
									$rs_update=$this->db->insert('com_stock_master', $data_insert);
								}
								if($rs_update){
									$add_docno=$this->get_doc_no[0]['doc_no']+1;
									$data_update_docno=array(
											'doc_no'=>$add_docno,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update_docno = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'doc_tp = ?' => $this->doc_tp
									);
									$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
									if($rs_update_docno){
										$status="y";
										$this->TrancateTable('trn_tdiary1_to');
										$this->TrancateTable('trn_tdiary2_to');
									}
								}else{
									$status="com_stock_master";
								}
							}else{
								$status="trn_diary2";
								
							}
						}
					}
				}else{
					$status="trn_diary1";
				}
		    }
	    }
	    $status_arr=array('status'=>$status,'doc_no'=>$this->doc_no);
		return $status_arr;
	}
	
	public function tranin2shopheadtodiarytobykeyin($doc_remark,$status_no,$doc_tp,$inv){
		$this->doc_tp=$doc_tp;
		$this->status_no=$status_no;
		$this->doc_no=$this->gendocno('',$this->doc_tp);					// doc_no from conf_doc_no
		$this->tranin2shop=$this->gettrnto('trn_tdiary1_to');				// get data from trn_tdiary1_to
		$chk_data2shop=count($this->tranin2shop);							// count from trn_tdiary1_to
		//$up_head_tmp=$this->update_trn_in2shop_head_tmp($doc_no);
		$this->suminv=$this->sumivn_to_bykeyin();
		if(!empty($this->suminv[0]['price'])){
			$this->amount=$this->suminv[0]['price'];
		}else{
			$this->amount="";
		}
		$this->ref_doc_no=$this->doc_no;
		$this->get_doc_no=$this->getdocno($this->doc_tp);
		$this->productlist=$this->getdataproduct_to("product_id");
		
		$chk_productlist=count($this->productlist);
		
	    if($chk_data2shop>0){
		    foreach($this->tranin2shop as $val_data2shop){
		    	$insert_data2shop=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->doc_date_pos,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'doc_tp'=>$this->doc_tp,
					'status_no'=>$this->status_no,
			    	'refer_doc_no'=>$inv,
			    	'quantity'=>$val_data2shop['quantity'],
			    	'amount'=>$this->amount,
		    		'net_amt'=>$this->amount,
			    	'computer_no'=>$this->com_no,
			    	'doc_remark'=>$doc_remark,
		    		'reg_date'=>$this->date,
		    		'reg_time'=>$this->time,
		    		'reg_user'=>$this->user_id
				);
				$rs_data2shop=$this->db->insert('trn_diary1', $insert_data2shop);
				if($rs_data2shop){
					if($chk_productlist>0){
						foreach($this->productlist as $val_productlist){
							if(!empty($val_productlist['price'])){
								$this->amount_item=($val_productlist['price']*$val_productlist['quantity']);
							}else{
								$this->amount_item="";
							}
							$insert_productlist=array(
								'corporation_id'=>$this->corporation_id,
								'company_id'=>$this->company_id,
								'branch_id'=>$this->branch_id,
								'doc_date'=>$this->doc_date_pos,
								'doc_time'=>$this->time,
								'doc_no'=>$this->doc_no,
								'doc_tp'=>$this->doc_tp,
								'status_no'=>$this->status_no,
								'seq'=>$val_productlist['seq'],
								'product_id'=>$val_productlist['product_id'],
								'stock_st'=>$this->get_doc_no[0]['stock_st'],
								'price'=>$val_productlist['price'],
						    	'quantity'=>$val_productlist['quantity'],
						    	'amount'=>$this->amount_item,
					    		'net_amt'=>$this->amount_item,
						    	'product_status'=>'N',
								'reg_date'=>$this->date,
					    		'reg_time'=>$this->time,
					    		'reg_user'=>$this->user_id
							);
							$rs_productlist=$this->db->insert('trn_diary2', $insert_productlist);
							if($this->status_no=="30"){
								$sql_upd_testter="
								update 
									com_product_testter 
								set 
									allocate=allocate+$val_productlist[quantity] 
								where
									product='$val_productlist[product_id]' 
									and shop='$this->branch_id'
								";
								$this->db->query($sql_upd_testter);
								
							}
							
							if($rs_productlist){
								$this->getstock=$this->getstokmaster($val_productlist['product_id'],$this->doc_no);
								$this->checkstock=count($this->getstock);
								if(empty($this->getstock[0]['onhand'])){
									$this->getstock[0]['onhand']="";
								}
								$this->onhand=$this->getstock[0]['onhand']+($val_productlist['quantity']*$this->get_doc_no[0]['stock_st']);
								$this->doc_date=$this->getdiary1($this->doc_no);
								$this->arr_date=explode("-",$this->doc_date[0]['doc_date']);		   
								$m_month=$this->arr_date[1];
								$y_year=$this->arr_date[0];	
								
								if($this->checkstock>0){
									$data_update=array(
											'onhand'=>$this->onhand,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'product_id = ?' => $val_productlist['product_id'],
												'month = ?' => $m_month,
												'year = ?' => $y_year
									);
									$rs_update=$this->db->update('com_stock_master', $data_update, $where_update);
								}else{
									$checkproductmaster=$this->getproductmaster($val_productlist['product_id']);
									$count_checkproductmaster=count($checkproductmaster);
									if($count_checkproductmaster==0){
										$getproduct=$this->getin2shoplistofproduct($this->doc_no,$val_productlist['product_id']);
										$data_insert_productmaster=array(
												'corporation_id'=>$this->corporation_id,
												'company_id'=>$this->company_id,
												'vendor_id'=>$this->vendor_id,
												'product_id'=>$val_productlist['product_id'],
												'barcode'=>$getproduct[0]['barcode'],
												'name_product'=>$getproduct[0]['product_name'],
												'name_print'=>$getproduct[0]['product_name'],
												'price'=>$getproduct[0]['price'],
												'unit'=>'ชิ้น',
												'tax_type'=>'V',
												'product_set'=>'N',
												'reg_date'=>$this->date,
									    		'reg_time'=>$this->time,
									    		'reg_user'=>$this->user_id
										);
										$rs_productmaster=$this->db->insert('com_product_master', $data_insert_productmaster);
										
									}
									
									$data_insert=array(
											'corporation_id'=>$this->corporation_id,
											'company_id'=>$this->company_id,
											'branch_id'=>$this->branch_id,
											'branch_no'=>$this->branch_no,
											'product_id'=>$val_productlist['product_id'],
											'month'=>$m_month,
											'year'=>$y_year,
											'product_status'=>'N',
											'onhand'=>$this->onhand,
											'reg_date'=>$this->date,
								    		'reg_time'=>$this->time,
								    		'reg_user'=>$this->user_id
									);
									$rs_update=$this->db->insert('com_stock_master', $data_insert);
								}
								if($rs_update){
									$add_docno=$this->get_doc_no[0]['doc_no']+1;
									$data_update_docno=array(
											'doc_no'=>$add_docno,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update_docno = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'doc_tp = ?' => $this->doc_tp
									);
									$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
									if($rs_update_docno){
										$status="y";
										$this->TrancateTable('trn_tdiary1_to');
										$this->TrancateTable('trn_tdiary2_to');
									}
								}else{
									$status="com_stock_master";
								}
							}else{
								$status="trn_diary2";
								
							}
						}
					}
				}else{
					$status="trn_diary1";
				}
		    }
	    }
	    $status_arr=array('status'=>$status,'doc_no'=>$this->doc_no);
		return $status_arr;
	}
	
	public function tranin2shopheadtodiary_rq($doc_no,$doc_remark,$status_no,$doc_tp,$inv){
		$this->doc_tp=$doc_tp;
		$this->status_no=$status_no;
		$this->doc_no=$this->gendocno('',$this->doc_tp);
		$this->tranin2shop=$this->gettrnto('trn_tdiary1_rq');
		$chk_data2shop=count($this->tranin2shop);
		$this->suminv=$this->sumivn_rq();
		if(!empty($this->suminv[0]['price'])){
			$this->amount=$this->suminv[0]['price'];
		}else{
			$this->amount="";
		}
		$this->ref_doc_no=$doc_no;
		$this->get_doc_no=$this->getdocno($this->doc_tp);
		
		$this->productlist=$this->getdataproduct_rq("product_id");
		$chk_productlist=count($this->productlist);
		
	    if($chk_data2shop>0){
		    foreach($this->tranin2shop as $val_data2shop){
		    	$insert_data2shop=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'doc_tp'=>$this->doc_tp,
					'status_no'=>$this->status_no,
			    	'refer_doc_no'=>$inv,
			    	'quantity'=>$val_data2shop['quantity'],
			    	'amount'=>$this->amount,
		    		'net_amt'=>$this->amount,
			    	'computer_no'=>$this->com_no,
			    	'doc_remark'=>$doc_remark,
		    		'reg_date'=>$this->date,
		    		'reg_time'=>$this->time,
		    		'reg_user'=>$this->user_id
				);
				$rs_data2shop=$this->db->insert('trn_diary1_rq', $insert_data2shop);
				if($rs_data2shop){
					if($chk_productlist>0){
						foreach($this->productlist as $val_productlist){
							if(!empty($val_productlist['price'])){
								$this->amount_item=($val_productlist['price']*$val_productlist['quantity']);
							}else{
								$this->amount_item="";
							}
							$insert_productlist=array(
								'corporation_id'=>$this->corporation_id,
								'company_id'=>$this->company_id,
								'branch_id'=>$this->branch_id,
								'doc_date'=>$this->date,
								'doc_time'=>$this->time,
								'doc_no'=>$this->doc_no,
								'doc_tp'=>$this->doc_tp,
								'status_no'=>$this->status_no,
								'seq'=>$val_productlist['seq'],
								'product_id'=>$val_productlist['product_id'],
								'stock_st'=>$this->get_doc_no[0]['stock_st'],
								'price'=>$val_productlist['price'],
						    	'quantity'=>$val_productlist['quantity'],
						    	'amount'=>$this->amount_item,
					    		'net_amt'=>$this->amount_item,
						    	'product_status'=>'N',
								'reg_date'=>$this->date,
					    		'reg_time'=>$this->time,
					    		'reg_user'=>$this->user_id
							);
							$rs_productlist=$this->db->insert('trn_diary2_rq', $insert_productlist);
							
							if($rs_productlist){

									$add_docno=$this->get_doc_no[0]['doc_no']+1;
									$data_update_docno=array(
											'doc_no'=>$add_docno,
											'upd_date'=>$this->date,
								    		'upd_time'=>$this->time,
								    		'upd_user'=>$this->user_id
									);
									$where_update_docno = array(
												'corporation_id = ?' => $this->corporation_id,
												'company_id = ?' => $this->company_id,
												'branch_id = ?' => $this->branch_id,
												'doc_tp = ?' => $this->doc_tp
									);
									$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
									if($rs_update_docno){
										$status="y";
										$this->TrancateTable('trn_tdiary1_rq');
										$this->TrancateTable('trn_tdiary2_rq');
									}
								
							}else{
								$status="trn_diary2_rq";
								
							}
						}
					}
				}else{
					$status="trn_diary1_rq";
				}
		    }
	    }
	    $status_arr=array('status'=>$status,'doc_no'=>$this->doc_no);
		return $status_arr;
	}
	
	public function checkinvoice($inv,$doc_tp,$tbl){
		$sql="
		select 
			doc_no 
		from 
			$tbl  
		where 
			doc_no='$inv' and doc_tp='$doc_tp'
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function checkproductinvoice($inv,$product_id,$doc_tp,$tbl){
		$sql="
		select 
			a.doc_no 
		from 
			$tbl as a 
		left join 
			com_product_master as b 
		on 
			a.product_id=b.product_id 
		where 
			a.doc_no='$inv' and a.doc_tp='$doc_tp' and (a.product_id='$product_id' or b.barcode='$product_id')
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function checkstockproduct($product_id){
		$sql="
		SELECT 
			a.product_id,
			b.onhand,b.month,year
		FROM 
			com_product_master as a 
		
		left join 
			com_stock_master as b 
		on 
			a.product_id=b.product_id and b.month='$this->month' and b.year='$this->year' 
		where 
			(a.product_id='$product_id' or a.barcode='$product_id')
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function checkproductrans($product_id){
		$sql="SELECT sum(quantity) FROM trn_tdiary2_to WHERE product_id='".$product_id."'";
		//$stmt=$this->db->query($sql);
		$data=$this->db->fetchOne($sql);
		return $data;
	
	}


	public function checkproductinvoiceqty($inv,$product_id,$doc_tp,$qty,$tbl,$type_doc_tp){
		//detail diary
		$tbl1=$tbl['tbl1'];
		//head diary
		$tbl2=$tbl['tbl2'];
		//temp diary
		$tbl3=$tbl['tbl3'];
		$sql="
		select 
			tbl2.onhand,sum(COALESCE(tbl2.qty,0.00)) as qty ,if(tbl2.refer_doc_no is null,0,1) as refer_doc_no 
		from
		(
		select 
			tbl.onhand,sum(tbl.qty+tbl.tem_qty) as qty ,tbl.refer_doc_no  
		from 
			(
			select 
				c.doc_no,
				d.refer_doc_no,
				a.product_id,
				b.onhand,
				sum(COALESCE(c.quantity,0.00)) as qty,
				sum(COALESCE(e.quantity,0.00)) as tem_qty 
			from 
				com_product_master as a 
			left join 
				com_stock_master as b 
			on 
				a.product_id=b.product_id 
			left join 
				$tbl1 as c 
			on 
				a.product_id=c.product_id and c.doc_tp='$type_doc_tp' 
			left join 
				$tbl2 as d 
			on  
				c.doc_no=d.doc_no and (c.doc_no='$inv' or d.refer_doc_no='$inv')
			left join 
				$tbl3 as e 
			on 
				a.product_id=e.product_id 
			where 
				(a.product_id='$product_id' or a.barcode='$product_id') and b.month='$this->month' and b.year='$this->year' and d.refer_doc_no<>'null') as tbl) as tbl2
				";
				//echo $sql;
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getmaxcninv($product_id,$doc_tp,$inv){
		//detail diary
		//$tbl1=$tbl['tbl1'];
		$sql="
		select 
			a.quantity 
		from 
			trn_diary2 as a 
		left join  
			com_product_master as b 
		on 
			a.product_id=b.product_id 		
		where 
			a.doc_no='$inv' and (a.product_id='$product_id' or b.barcode='$product_id')
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	
	public function getdocstatus($doc_status){
		if($doc_status=="20"){
			$doc_tp="TI";
		}else{
			$doc_tp="CN";
		}
		return $doc_tp;
	}
	

	public function getdoctranferto($doc_tp,$tbl,$date=0){
		if($date == 0){
		$sql="
			select 
				doc_no,quantity,reg_date,`flag`,refer_doc_no 
			from 
				$tbl  
			where 
				doc_tp='$doc_tp'
			order by 
				doc_date DESC,doc_no DESC
			LIMIT 0 , 30
			";		

		}else{
			$d_cal = "-".$date." days";
			$d = date('Y-m-d', strtotime($d_cal));
			$sql="
			select 
				doc_no,quantity,reg_date,`flag`,refer_doc_no 
			from 
				$tbl  
			where 
				doc_tp='$doc_tp' AND doc_date > '$d'
			order by 
				doc_date DESC,doc_no DESC
			LIMIT 0 , 30
			";		
		}
		//echo $sql;
		//exit();
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}	

	public function checktempdiaryto(){
		$sql="
		select 
			product_id 
		from 
			trn_tdiary2_to 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function checktempdiaryrq(){
		$sql="
		select 
			product_id 
		from 
			trn_tdiary2_rq 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
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
	
	public function test(){
		return $this->empprofile['company_id'];
	}
	
	public function getdataproductfixstock($where,$sort){
		$sql="
		SELECT 
			* 
		FROM 
			trn_fix_stock $where ORDER BY $sort" ;
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
		
		
	}
	
	public function getdataproductquery($sort,$flag){
		$sql="
		SELECT 
			id,doc_date,doc_no,product_id,product_name,quantity,price 
		FROM 
			`trn_in2shop_list` 
		WHERE 
			flag1 = '$flag' 
		ORDER BY 
			$sort 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function getbillvtdetail($doc_no){
		$sql="
		SELECT 
			id,doc_date,doc_time,doc_no,doc_tp,status_no,member_id,quantity,amount,name,address1,address2,address3  
		FROM 
			`trn_diary1` 
		WHERE 
			doc_no = '$doc_no' 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function getdataproductinventories($product_id,$sort){
		$sql="
		select 
			a.product_id,a.name_product,a.product_status,a.onhand,b.price 
		from 
			com_stock_km as a 
		left join  
			com_product_master as b 
		on 
			a.product_id=b.product_id 		
		where 
			a.product_id LIKE '$product_id%' 
		order by 
			$sort
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function countRec($fname,$tname) {
		$sql = "SELECT count($fname) as count FROM $tname ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data[0]['count'];
	}	
	
	public function getdatarq($sort,$where){
		$sql="
		select 
			doc_date,doc_time,doc_no,doc_tp,status_no,flag,status_transfer,refer_doc_no,quantity,doc_remark 
		from 
			trn_diary1_rq 
		where 1 
		$where 
		order by 
			$sort";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function tranferrqto($get_doc_no_rq,$type_docno,$cashier_id){
		$com_status="22";
		$this->get_doc_no=$this->getdocno($type_docno);
		$this->doc_no=$this->gendocno('',$type_docno);
		$sql_chk_rq="
		select 
			refer_doc_no 
		from 
			trn_diary1  
		where 
			refer_doc_no='$get_doc_no_rq'
		";
		$rs_chk_rq=$this->db->query($sql_chk_rq);
		$fetc_chk_rq=$rs_chk_rq->fetchAll();
		$chk_rq=count($fetc_chk_rq);
		if($chk_rq>0){
			$rs="t";
		}else{
			$this->db->beginTransaction();
			try {
				$sql_insert_to_head="
				INSERT INTO
					trn_diary1(
						corporation_id,
						company_id,
						branch_id,
						doc_date,
						doc_time,
						doc_no,
						doc_tp,
						status_no,
						refer_doc_no,
						quantity,
						amount,
						computer_no,
						cashier_id,
						reg_date,
						reg_time,
						reg_user
					)
				SELECT
					corporation_id,
					company_id,
					branch_id,
					'".$this->doc_date_pos."',
					'".$this->time."',
					'".$this->doc_no."',
					'".$type_docno."',
					status_no,
					'".$get_doc_no_rq."',
					quantity,
					amount,
					'".$this->com_no."',
					'".$cashier_id."',
					'".$this->date."',
					'".$this->time."',
					'".$this->user_id."' 
				FROM
					trn_diary1_rq 
				where 
					doc_no='$get_doc_no_rq'";
				$this->db->query($sql_insert_to_head);
				
				$sql_insert_to_detail="
					INSERT INTO
					trn_diary2(
					corporation_id,
					company_id,
					branch_id,
					doc_date,
					doc_time,
					doc_no,
					doc_tp,
					status_no,
					flag,
					seq,
					product_id,
					price,
					quantity,
					stock_st,
					amount,
					reg_date,
					reg_time,
					reg_user
					)
				SELECT
					corporation_id,
					company_id,
					branch_id,
					'".$this->doc_date_pos."',
					'".$this->time."',
					'".$this->doc_no."',
					'".$type_docno."',
					status_no,
					flag,
					seq,
					product_id,
					price,
					quantity,
					'".$this->get_doc_no[0]['stock_st']."',
					amount,
					'".$this->date."',
					'".$this->time."',
					'".$this->user_id."' 
				FROM
					trn_diary2_rq 
				where 
					doc_no='$get_doc_no_rq'";
				$this->db->query($sql_insert_to_detail);
				
				$updat_refer_rq="
				update 
					trn_diary1_rq 
				set 
					status_transfer='T', 
					cashier_id='$cashier_id',
					doc_remark='$this->doc_no'
				where 
					doc_no='$get_doc_no_rq'
				";
				$this->db->query($updat_refer_rq);

				$this->updat_com_doc_no($type_docno);
				
				$this->db->commit();
				$rs="y";
			}catch (Exception $e) {
				$this->db->rollback();
				$rs="n";
			}
		}
		return $rs;
	}
	
	public function updat_com_doc_no($doc_tp){
		$this->get_doc_no=$this->getdocno($doc_tp);
		$add_docno_to=$this->get_doc_no[0]['doc_no']+1;
		$sql="
		update 
			com_doc_no 
		set 
			doc_no='$add_docno_to',
			upd_date='$this->date', 
			upd_time='$this->time', 
			upd_user='$this->user_id' 
		where 
			corporation_id='$this->corporation_id' 
			and company_id='$this->company_id' 
			and branch_id='$this->branch_id' 
			and doc_tp='$doc_tp'
		";
		$this->db->query($sql);
	}
	
	public function checkcashier($cashier_id){
		$sql="
		select 
			employee_id,name,surname 
		from 
			conf_employee  
		where 
			employee_id='$cashier_id'
		";
		$rs=$this->db->query($sql);
		$data=$rs->fetchAll();
		return $data;
	}
	
	public function rqdetail($doc_no){	
		$sql="
		select 
			a.doc_no,a.product_id,a.price,a.quantity,a.amount, 
			b.name_product 
		from 
			trn_diary2_rq as a 
		left join 
			com_product_master as b 
		on 
			a.product_id=b.product_id 
		where 
			doc_no='$doc_no'
		";
		$rs=$this->db->query($sql);
		$data=$rs->fetchAll();
		return $data;
	}
	
	public function unzip_inv(){
		$db_path ="/stock/file/7777_INV.DBF";
		$dbh = dbase_open($db_path, 0)or die("Error! Could not open dbase database file '$db_path'.");
		echo $dbh;
		if (!$dbf = dbase_open ($db_path, 0)){ 
			die("Could not open $dbf_file for import."); 
		}
	}
	
	public function checkproductmaster($doc_no){
		$sql="
		SELECT 
			b.product_id 
		FROM 
			`trn_in2shop_list` as a 
		LEFT JOIN  
			com_product_master as b 
			on a.product_id=b.product_id 
		WHERE `doc_no` ='$doc_no' and b.product_id is null
		";
		$rs=$this->db->query($sql);
		$data=$rs->fetchAll();
		$return=count($data);
		return $return;
	}
	
	public function add_temp_in2shop($arr){
		$this->seq=$this->getseq($arr['invoice'],$arr['product_id']);
		$this->product=$this->getproductmaster($arr['product_id']);
		$product_name=$this->product[0]['name_product'];
		$price=$this->product[0]['price'];
		$status="y";
		$sql_list="
		insert into 
			trn_in2shop_list_tmp 
		set 
			corporation_id='$this->corporation_id', 
			company_id='$this->company_id', 
			branch_id='$this->branch_id', 
			doc_date='', 
			doc_no='$arr[invoice]', 
			seq='$this->seq', 
			product_id='$arr[product_id]', 
			product_name='$product_name', 
			quantity='$arr[qty]', 
			flag1='D', 
			price='$price', 
			barcode='', 
			product_status='' 
		";
		$rs_list=$this->db->query($sql_list);
		if($rs_list){
			$sql_clear="delete from trn_in2shop_head_tmp where doc_no='$arr[invoice]'";
			$rs_clear=$this->db->query($sql_clear);
			if($rs_clear){
				$sql_head="
				insert into 
					trn_in2shop_head_tmp(corporation_id,company_id,branch_id,doc_date,doc_no,flag1,quantity,refer_doc_no) 
				select 
					'$this->corporation_id','$this->company_id','$this->branch_id','','$arr[invoice]','D',sum(quantity),'' 
				from 
					trn_in2shop_list_tmp 
				where 
					doc_no='$arr[invoice]'
				";
				$rs_head=$this->db->query($sql_head);
				if(!$rs_head){
					$status="n";
				}
			}else{
				$status="n";
			}
		}else{
			$status="n";
		}
		return $status;

	}
	
	public function getdatain2shop_temp($sort,$start,$rp){
		if(!empty($rp)){
		$sql=$this->db->select()
		    		  	->from('trn_in2shop_list_tmp',array('doc_date','doc_no','seq','product_id','product_name','quantity','price','product_status','mfg_date'))
						->order($sort)
						->limit($rp, $start);
		}else{
			$sql=$this->db->select()
		    		  	->from('trn_in2shop_list_tmp',array('doc_date','doc_no','seq','product_id','product_name','quantity','price','product_status','mfg_date'))
						->order($sort);
		}
		$data=$sql->query()->fetchAll();
		$chk=count($data);
		if($chk>0){
			$data=$data;
		}else{
			$data=array();
		}
		return $data;
	}
	
	public function sumin2shop(){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,sum(price) as sum_price 
				FROM 
					`trn_in2shop_list_tmp` 
				group by 
					product_id,seq) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getdoctp($doc_tp){
		$sql="
		select 
			doc_no,refer_doc_no  
		from 
			trn_diary1 
		where 
			doc_tp='$doc_tp'
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getdocall($doc_no,$tbl){
		$sql="
		select 
			branch_id,doc_date,doc_time,doc_date,doc_time,doc_no,status_no,flag,refer_doc_no,print_no,doc_remark  
		from 
			$tbl 
		where 
			doc_no='$doc_no'
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getdocnore($doc_no_start,$doc_no_end,$doc_tp,$tbl){
		$sql="
		select 
			doc_no   
		from 
			$tbl 
		where 
			doc_tp='$doc_tp'
			and (doc_no between '$doc_no_start' and '$doc_no_end' or doc_no between '$doc_no_end' and '$doc_no_start')
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function previewti($doc_no_start,$doc_no_end,$doc_tp,$tbl){
		$sql="
		select 
			*    
		from 
			$tbl 
		where 
			doc_tp='$doc_tp'
			and (doc_no between '$doc_no_start' and '$doc_no_end' or doc_no between '$doc_no_end' and '$doc_no_start')
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		foreach($rs as $data){
			if(empty($data['remark'])){
				$data['remark']="";
			}
			
			if(empty($data['flag'])){
				$data['flag']="";
			}
			$sum=$this->sumivnconfirmto($data['doc_no']);
	   		 $out[] = array(
	   		 				'doc_no'=>$data['doc_no'],
	   		 				'doc_date'=>$data['doc_date'],
	   		 				'remark'=>$data['remark'],
	   		 				'flag'=>$data['flag'],
	   		 				'sum_qty'=>$sum[0]['quantity'],
	   		 				'price'=>$sum[0]['price'],
	   		 				'detail' => $this->getdiary2($data['doc_no'])
	   		 		   );
	   	}
	   	if(empty($out)){
	   		$out=array();
	   	}
	   	return $out;

		
	}
	
	public function previewrq($doc_no_start,$doc_no_end,$doc_tp,$tbl){
		$sql="
		select 
			*    
		from 
			$tbl 
		where 
			doc_tp='$doc_tp'
			and (doc_no between '$doc_no_start' and '$doc_no_end' or doc_no between '$doc_no_end' and '$doc_no_start')
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		foreach($rs as $data){
			if(empty($data['remark'])){
				$data['remark']="";
			}
			if(empty($data['flag'])){
				$data['flag']="";
			}
			$sum=$this->sumivnconfirmrq($data['doc_no']);
	   		 $out[] = array(
	   		 				'doc_no'=>$data['doc_no'],
	   		 				'doc_date'=>$data['doc_date'],
	   		 				'remark'=>$data['remark'],
	   		 				'flag'=>$data['flag'],
	   		 				'sum_qty'=>$sum[0]['quantity'],
	   		 				'price'=>$sum[0]['price'],
	   		 				'detail' => $this->getdiary2rq($data['doc_no'])
	   		 		   );
	   	}
	   	if(empty($out)){
	   		$out=array();
	   	}
	   	return $out;
	}
	
	public function previewor($doc_no_start,$doc_no_end,$doc_tp,$tbl){
		$sql="
		select 
			*    
		from 
			$tbl 
		where 
			doc_tp='$doc_tp'
			and (doc_no between '$doc_no_start' and '$doc_no_end' or doc_no between '$doc_no_end' and '$doc_no_start')
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		foreach($rs as $data){
			if(empty($data['remark'])){
				$data['remark']="";
			}
			if(empty($data['flag'])){
				$data['flag']="";
			}
			$sum=$this->sumivnconfirmor($data['doc_no']);
	   		 $out[] = array(
	   		 				'doc_no'=>$data['doc_no'],
	   		 				'doc_date'=>$data['doc_date'],
	   		 				'remark'=>$data['remark'],
	   		 				'flag'=>$data['flag'],
	   		 				'sum_qty'=>$sum[0]['quantity'],		
	   		 				'price'=>$sum[0]['price'],
	   		 				'detail' => $this->getdiary2or($data['doc_no'])
	   		 		   );
	   	}
	   	if(empty($out)){
	   		$out=array();
	   	}
	   	return $out;
	}
	
	public function previewiq($doc_no_start,$doc_no_end,$doc_tp,$tbl){
		$sql="
		select 
			*    
		from 
			$tbl 
		where 
			doc_tp='$doc_tp'
			and (doc_no between '$doc_no_start' and '$doc_no_end' or doc_no between '$doc_no_end' and '$doc_no_start')
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		foreach($rs as $data){
			if(empty($data['remark'])){
				$data['remark']="";
			}
			if(empty($data['flag'])){
				$data['flag']="";
			}
			$sum=$this->sumivnconfirmiq($data['doc_no']);
	   		 $out[] = array(
	   		 				'doc_no'=>$data['doc_no'],
	   		 				'doc_date'=>$data['doc_date'],
	   		 				'remark'=>$data['remark'],
	   		 				'flag'=>$data['flag'],
	   		 				'sum_qty'=>$sum[0]['quantity'],
	   		 				'price'=>$sum[0]['price'],
	   		 				'detail' => $this->getdiary2iq($data['doc_no'])
	   		 		   );
	   	}
	   	if(empty($out)){
	   		$out=array();
	   	}
	   	return $out;
	}
	
	public function getdoctptxt($doc_tp){
		$sql="
		select 
			doc_tp,stock_st,remark     
		from 
			com_doc_no 
		where 
			doc_tp='$doc_tp'
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		return $rs;
	}
	
	public function sumfixstock(){
		$sql="
			SELECT 
				sum(a.quantity_normal) as sum_qty, sum((b.price*a.quantity_normal)) as sum_price 
 			FROM 
 				`trn_fix_stock` as a 
 			left join 
 				com_product_master as b 
			on 
				a.product_id=b.product_id
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		return $rs;
	}
	
	public function sumstockmaster(){
		$sql="
		SELECT 
			sum(a.onhand) as sum_qty, sum((b.price*a.onhand)) as sum_price 
 		FROM 
 			`com_stock_master` as a 
 		left join 
 			com_product_master as b 
		on 
			a.product_id=b.product_id where a.month='$this->month' and a.year='$this->year'
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		return $rs;
	}
	
	public function check_config_ti_keyin(){
		$sql="
		SELECT 
			default_status,condition_status,
			concat(start_date,' ',start_time) as start_date,
			concat(end_date, ' ',end_time) end_date 
		FROM 
			`com_pos_config` 
		where 
			code_type='NO_KEYIN_TI'
		";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		$rs[0]['start_date'];
		$date_now=$this->date." ".$this->time;
		if($date_now>='$rs[0][start_date]' && $date_now<='$rs[0][end_date]'){
			$status=$rs[0]['default_status'];
		}else{
			$status=$rs[0]['condition_status'];
		}
		//$a=$date_now.">=".$rs[0]['start_date']." && ".$date_now."<=".$rs[0]['end_date'];
		//return $a;
		return $status;
	}
	
	public function check_qty_product_rq($product_id){
		$sql="
		SELECT 
			a.product_id,sum(a.quantity) as qty ,
			b.product_id,b.onhand,b.month,b.year 
		FROM 
            com_stock_master as b 
		left join 
			`trn_tdiary2_rq` as a 
		on 
			a.product_id=b.product_id 
		where 
			b.product_id='".$product_id."' 
			and b.month='".$this->month."' 
			and year='".$this->year."'";
		$stmt=$this->db->query($sql);
		$rs=$stmt->fetchAll();
		return $rs;
	}

	public function check_pl(){
		$sql="
		SELECT 
			*,doc_date,doc_no, substr(NOW(),1,10),substr(product_id,1,3) 
		FROM 
			`trn_diary2_iq` 
		where 
			doc_tp='PL' 
			and seq='1' 
			 and 
			(substr(product_id,1,3)>0 or substr(product_id,1,3)='LGS') and (doc_date=DATE_SUB(substr(NOW(),1,10), INTERVAL 1 DAY) or doc_date=substr(NOW(),1,10))";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}

	public function getTOMenu(){
		$dd_date = $this->doc_date_pos;
		$date = explode('-',$this->doc_date_pos);
		$d = $date[2];
		$sql="SELECT *,
		if('$dd_date' BETWEEN start_date AND end_date AND time(now()) BETWEEN start_time AND end_time ,'1','0') AS date_con FROM com_to_config WHERE doc_tp='TO' ORDER BY status_no";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$output_arr = array();
		/*
		echo "<pre>";
		print_r($data);
		exit();
		/**/
		while($tmp = array_shift($data)){
			if($tmp['date_con']=='1'){
				if(($d >= $tmp['start_normal'] && $d <= $tmp['end_normal']) || ($d >= $tmp['start_special'] && $d <= $tmp['end_special'])){
					$enable='1';	
				}else{
					$enable='0';
				}
			}else{
				if($d >= $tmp['start_normal'] && $d <= $tmp['end_normal']){
					$enable='1';	
				}else{
					$enable='0';
				}
			}
			array_push($output_arr,array('description'=>$tmp['description'],'doc_tp'=>$tmp['doc_tp'], 'status_no'=>$tmp['status_no'],'active'=>$enable));
		}
		//array_push($output_arr,array('description'=>$sql,'doc_tp'=>'', 'status_no'=>'','active'=>$enable));
		//print_r();
		return $output_arr;

	}

	function check_stock_master($product,$reg_item){
		$d = explode('-',$this->doc_date_pos);
		$sql="SELECT onhand FROM com_stock_master AS A WHERE A.product_id='".$product."' AND A.month='".$d[1]."' AND A.year='".$d[0]."'";
		$stock=$this->db->fetchOne($sql);
		//$sql="";
		
		$sql="SELECT sum(quantity) FROM trn_tdiary2_to WHERE product_id='".$product."' ";

		$tmp_item=$this->db->fetchOne($sql);
		if($reg_item+$tmp_item > $stock){
			return 1;
		}else{
			return 3;
		}

		

	}

}
?>
