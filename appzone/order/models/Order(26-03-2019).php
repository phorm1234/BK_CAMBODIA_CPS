<?php 
class Model_Order{	
	public $db1;
	public $corporation_id;
	public $company_id;
	public $branch_id;
	public $com_no;
	public $user_id;
	public $date;
	public $time;
	public $month;
	public $m_month;
	public $year_month;
	public $year;
	public $doc_no;
	public function __construct(){
		
		/*$this->db=Zend_Registry::get('dbOfline');
		$this->corporation_id='OP';
		$this->company_id='OP';
		$this->vendor_id='OP';
		$this->branch_id='7777';
		$this->branch_no='7777';
		$this->date=date('Y-m-d');
		$this->year_month=date('Y-m');
		$this->time=date('H:i:s');
		$this->month=date('n');
		$this->m_month=date('m');
		$this->year=date('Y');
		$this->com_no='1';
		$this->user_id='004716';*/
		$this->db=Zend_Registry::get('db1');
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
	

	public function updateorder($update_value,$seq,$tbl){
		// echo $_POST['update_value'];
					$check="SELECT short_qty from trn_tdiary2_or where seq='$seq' and corporation_id='".$this->corporation_id."' and company_id='".$this->company_id."' and branch_id='".$this->branch_id."' ";
					$rscheck=$this->db->query($check);
					$data=$rscheck->fetchAll();
					if($update_value > number_format($data[0]['short_qty'], 0, '.', ',') ) {
						// echo 'order qty can not  much more than suggest qty!';
						$status="n";
						return $status;
					}else if($update_value >= 0){
						$rs2="SELECT price from trn_tdiary2_or where seq='$seq' and corporation_id='".$this->corporation_id."' and company_id='".$this->company_id."' and branch_id='".$this->branch_id."' ";
						$stmt=$this->db->query($rs2);
						$data=$stmt->fetchAll();
						$amount=number_format($data[0]['price'], 0, '.', ',')*$update_value;
						$sql="update trn_tdiary2_or SET quantity='".$update_value."',amount=$amount where seq='$seq' and corporation_id='".$this->corporation_id."' and company_id='".$this->company_id."' and branch_id='".$this->branch_id."'";
						$rs=$this->db->query($sql);
						$status="y";
						return $status;
					}
					

	}

	//fix stock//
	public function getfixproductdetail(){
		// get data A,B
		// $sql=$this->db->select()
		// 				// ->from('trn_fix_stock',array('product_id','quantity_normal'))
		// 				->from('com_stock_master',array('product_id','onhand'))
		// 				->join('trn_fix_stock','trn_fix_stock.product_id=com_stock_master.product_id',array('quantity_normal'))
		// 				// ->join('com_stock_master','trn_fix_stock.product_id=com_stock_master.product_id')
		// 				->where('month = ?', $this->month)
		// 				->where('year = ?', $this->year);
		// $data=$sql->query()->fetchAll();
		   // return $data;
		   
		   $sql="
		   SELECT 
		   	   a.product_id,a.quantity_normal,
			   b.name_product,b.price,
			   c.onhand 
		   FROM 
		   	   trn_fix_stock as a 
		   left join 
		   	   com_product_master as b
		   on 
			   a.product_id=b.product_id 
				   left join 
			   com_stock_master as c 
		   on 
			   a.product_id=c.product_id and c.month='".$this->month."' and c.year='".$this->year."' where a.corporation_id='".$this->corporation_id."' and a.company_id='".$this->company_id."' and a.branch_id='".$this->branch_id."' ";

		   $stmt=$this->db->query($sql);
		   $data_detail=$stmt->fetchAll();
		   //calculate short_qty
		   foreach($data_detail as $data_array){
			$product_id= $data_array['product_id'];
			$onhand= number_format($data_array['onhand'], 0, '.', ',');
			$quantity_normal= number_format($data_array['quantity_normal'], 0, '.', ',');
			$suggested_qty= (number_format($data_array['quantity_normal'], 0, '.', ','))-(number_format($data_array['onhand'], 0, '.', ','));
			$order_qty= $suggested_qty;
			$data_array['suggested_qty'] = $suggested_qty;
			//insert to DB
			if($data_array['suggested_qty'] > 0){
				$amount=$data_array['price']*$data_array['suggested_qty'];
				$sql_seq="select max(seq) as seq from trn_tdiary2_or ";
				$stmt_seq=$this->db->query($sql_seq);
				$seq=$stmt_seq->fetchAll();
				$seq=$seq[0]['seq']+1;
				$default_value = 0;
				$insert_tdiary2_or=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_time'=>$this->time,
					'flag'=>'',
					'doc_tp'=>"OR",
					'status_no'=>"70",
					'seq'=>$seq,
					'product_id'=>$data_array['product_id'],
					'price'=>$data_array['price'],
					'quantity'=>$order_qty,
					'short_qty'=>$data_array['suggested_qty'],
					'short_amt'=>$default_value,
					'ret_short_qty'=>$default_value,
					'amount'=>$amount,
					'net_amt'=>$amount,
					'reg_date'=>$this->date,
					'reg_time'=>$this->time,
					'reg_user'=>$this->user_id
				);
			$rs_tdiary2_or=$this->db->insert('trn_tdiary2_or', $insert_tdiary2_or);
			  }else{
				echo " Can't transfer to trn_tdiary2_or !! <br><br>";
			  };
		   }


	}

	public function insert_tdiary_or_stock($data_array){
		//$doc_no=$this->gendocno($data_array['doc_tp']);
		//fix stock
		$data_detail=$this->getfixproductdetail($data_array['product_id']);
		
		if(!empty($data_array['qty'])){
			$amount=$data_detail[0]['price']*$data_array['qty'];
		}else{
			$amount="";
		}
		
		$sql_seq="select max(seq) as seq from trn_tdiary2_or ";
		$stmt_seq=$this->db->query($sql_seq);
		$seq=$stmt_seq->fetchAll();
		$seq=$seq[0]['seq']+1;
		$insert_tdiary2_or=array(
				'corporation_id'=>$this->corporation_id,
				'company_id'=>$this->company_id,
				'branch_id'=>$this->branch_id,
				'doc_date'=>$this->date,
				'doc_time'=>$this->time,
				'flag'=>'',
				'doc_tp'=>$data_array['doc_tp'],
				'status_no'=>$data_array['status_no'],
				'seq'=>$seq,
				'product_id'=>$data_array['product_id'],
				'price'=>$data_array['price'],
				'quantity'=>$data_array['qty'],
				'short_qty'=>$data_array['avg'],
				'short_amt'=>$data_array['bal'],
				'ret_short_qty'=>$data_array['fix'],
				'amount'=>$amount,
				'net_amt'=>$amount,
		    	'reg_date'=>$this->date,
		    	'reg_time'=>$this->time,
		    	'reg_user'=>$this->user_id
			);
		$rs_tdiary2_or=$this->db->insert('trn_tdiary2_or', $insert_tdiary2_or);
		//return $sql_seq;
	}
	/////////////

	public function getquotadetail(){
		$sql=$this->db->select()
		    		  	->from('com_order_quota',array('month_amount','month_order','balance_amount'))
		    		  	->where('month = ?', $this->month)
		    		  	->where('year = ?', $this->year)
		    		  	->where('corporation_id  = ?', $this->corporation_id)
		    		  	->where('company_id = ?', $this->company_id)
		    		  	->where('branch_id = ?', $this->branch_id);
	    $data=$sql->query()->fetchAll();
	    $count=count($data);
	    if($count>0){
	    	$data=$data;
	    }else{
	    	//$data=array();
			$data = $this->initOrderQuota();
	    }
	   	return $data;
	}
	
	public function checkproduct($product_id){
		if(strlen($product_id)>12){
			$sql=$this->db->select()
							->from('com_product_master',array('product_id','name_product','price','type'))
							->where('barcode = ?', $product_id)
							->where('corporation_id  = ?', $this->corporation_id)
							->where('company_id = ?', $this->company_id)
							->where('type != ?', 'T');		
		}else{
			$sql=$this->db->select()
							->from('com_product_master',array('product_id','name_product','price','type'))
							->where('product_id = ?', $product_id)
							->where('corporation_id  = ?', $this->corporation_id)
							->where('company_id = ?', $this->company_id)
							->where('type != ?', 'T');
		}

	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	public function checkproduct_tester($product_id){
		$chkproducttester=$this->producttester($product_id,$this->m_month);
		if($chkproducttester>0){
			$data="t";
		}else{
			$sql=$this->db->select()
			    		  	->from('com_product_master',array('product_id','name_product','price','type'))
			    		  	->where('product_id = ?', $product_id)
			    		  	->where('corporation_id  = ?', $this->corporation_id)
			    		  	->where('company_id = ?', $this->company_id)
			    		  	->where('type = ?', 'T');
		    $data=$sql->query()->fetchAll();
		    //$sql="select product_id,type from com_product_master where type='T' and product_id='".$product_id."'";
		}
	    return $data;
	}
	
	public function checkproductlock($product_id){
		$sql=$this->db->select()
		    		  	->from('com_product_lock',array('product_id','doc_tp','start_date', 'end_date'))
		    		  	->where('product_id = ?', $product_id)
		    		  	->where('corporation_id  = ?', $this->corporation_id)
		    		  	->where('company_id = ?', $this->company_id)
		    		  	->where('doc_tp = ?', 'OR');
	    $data=$sql->query()->fetchAll();
	   	return $data;
	}
	
	/*public function gendocno($doc_no,$type_docno){
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
	}*/
	
	public function gendocno($type_docno){
		//print_r(array($type_docno));
    	/*$data_docno=$this->getdocno($type_docno);
		$convdocno="000000000".$data_docno[0]['doc_no'];
		$convdocno=substr($convdocno,-8);*/
		//$doc_no=$data_docno[0]['branch_id'].$data_docno[0]['doc_tp']."-".$data_docno[0]['branch_no']."-".$convdocno;
		/*$doc_no=$data_docno[0]['corporation_id'].$data_docno[0]['branch_id'].$data_docno[0]['doc_tp']."-"."01"."-".$convdocno;
		return $doc_no;*/
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
	
	public function getdocno($type_docno){
		$sql=$this->db->select()
	    		   ->from('com_doc_no',array('corporation_id','company_id','branch_id','branch_no','doc_tp','doc_no','stock_st','remark'))
	    		   ->where('doc_tp=?',$type_docno)
	    		   ->where('company_id=?','CPS');
    	$data=$sql->query()->fetchAll();
		//print_r(array($data,$type_docno));
	    return $data;
	}
	
	public function getproductdetail($product_id){
		$sql="
		SELECT 
			a.name_product,a.price,
            b.product_id,b.quantity_normal,
			c.onhand 
		FROM 
			com_product_master as a 
		left join 
			trn_fix_stock as b
		on 
			a.product_id=b.product_id 
                left join 
			com_stock_master as c 
		on 
			a.product_id=c.product_id and c.month='".$this->month."' and c.year='".$this->year."' 
		where "; 
		
			
		if(strlen($product_id)>12){
			$sql.="a.barcode='".$product_id."'";
		}else{
			$sql.="a.product_id='".$product_id."'";
		}
		$stmt=$this->db->query($sql);
		$data_detail=$stmt->fetchAll();
		return $data_detail;
	}
	
	public function insert_tdiary_or($data_array){
		//$doc_no=$this->gendocno($data_array['doc_tp']);
		$data_detail=$this->getproductdetail($data_array['product_id']);
		//fix stock
		// $data_detail=$this->getfixproductdetail($data_array['product_id']);
		
		if(!empty($data_array['qty'])){
			$amount=$data_detail[0]['price']*$data_array['qty'];
		}else{
			$amount="";
		}
		
		$sql_seq="select max(seq) as seq from trn_tdiary2_or ";
		$stmt_seq=$this->db->query($sql_seq);
		$seq=$stmt_seq->fetchAll();
		$seq=$seq[0]['seq']+1;
		$insert_tdiary2_or=array(
				'corporation_id'=>$this->corporation_id,
				'company_id'=>$this->company_id,
				'branch_id'=>$this->branch_id,
				'doc_date'=>$this->date,
				'doc_time'=>$this->time,
				'flag'=>'',
				'doc_tp'=>$data_array['doc_tp'],
				'status_no'=>$data_array['status_no'],
				'seq'=>$seq,
				'product_id'=>$data_array['product_id'],
				'price'=>$data_detail[0]['price'],
				'quantity'=>$data_array['qty'],
				'short_qty'=>$data_array['avg'],
				'short_amt'=>$data_array['bal'],
				'ret_short_qty'=>$data_array['fix'],
				'amount'=>$amount,
				'net_amt'=>$amount,
		    	'reg_date'=>$this->date,
		    	'reg_time'=>$this->time,
		    	'reg_user'=>$this->user_id
			);
		$rs_tdiary2_or=$this->db->insert('trn_tdiary2_or', $insert_tdiary2_or);
		//return $sql_seq;
	}
	
	public function insert_tdiary_iq($data_array){
		$data_detail=$this->getproductdetail($data_array['product_id']);
		$price=$data_detail[0]['price'];
		if(!empty($data_array['qty'])){
			$amount=$price*$data_array['qty'];
		}else{
			$amount="";
		}
		
		$sql_seq="select max(seq) as seq from trn_tdiary2_iq ";
		$stmt_seq=$this->db->query($sql_seq);
		$seq=$stmt_seq->fetchAll();
		$seq=$seq[0]['seq']+1;
		
		$rs="y";
		$this->db->beginTransaction();
		try {
			$sql="
			INSERT INTO 
				trn_tdiary2_iq 
			SET 
				corporation_id='$this->corporation_id',
				company_id='$this->company_id',
				branch_id='$this->branch_id',
				doc_date='$this->date',
				doc_time='$this->time',
				doc_no='$data_array[inv]',
				doc_tp='IQ',
				status_no='$data_array[doc_status]',
				seq='$seq',
				product_id='$data_array[product_id]',
				price='$price',
				quantity='$data_array[qty]',
				amount='$amount',
				net_amt='$amount',
		    	reg_date='$this->date',
		    	reg_time='$this->time',
		    	reg_user='$this->user_id'
				
			";
			$this->db->query($sql);
			$this->db->commit();
		}catch (Exception $e) {
			$this->db->rollback();
			$rs="n";
		}
		return $rs;

	}
	
	public function getdataproduct($sort){
		$sql="SELECT 
			d.short_qty AS quantity_normal,
			b.product_id,b.name_product,b.price,
			d.amount,d.quantity,d.seq,d.short_qty,d.short_amt AS onhand,d.ret_short_qty,
			e.doc_no,sum(e.quantity) as transit,e.flag1, 
			f.refer_doc_no  
		FROM 
			trn_tdiary2_or as d 
		left join 
			com_product_master as b 
		on 
			d.product_id=b.product_id 
		left join 
			trn_in2shop_list as e 
		on 
			d.product_id=e.product_id and e.flag1='D' 
		left join 
			trn_diary1 as f 
		on 
			e.doc_no=f.refer_doc_no  
       	group by 
			d.product_id,d.seq  
		order by 
			d.".$sort;
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
	
	public function getdataproduct_iq($sort){
		$sql="SELECT 
			a.quantity_normal,
			b.product_id,b.name_product,b.price,
			c.onhand,
			d.amount,d.quantity,d.seq 
		FROM 
			trn_tdiary2_iq as d 
		left join 
			com_product_master as b 
		on 
			d.product_id=b.product_id 
		left join 
			com_stock_master as c 
		on 
			d.product_id=c.product_id and c.month='".$this->month."' and c.year='".$this->year."'  
        left join 
			trn_fix_stock as a 
		on
			d.product_id=a.product_id 
		group by 
			d.product_id,d.seq  
		order by 
			d.".$sort;
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

	public function getdataproductfix($sort){
		$sql="SELECT 
			d.short_qty AS short_qty,
			b.product_id,b.name_product,b.price,
			c.onhand AS test,
			d.amount,d.quantity,d.seq,d.short_qty,d.short_amt AS onhand,d.ret_short_qty,
			e.doc_no,sum(e.quantity) as transit,e.flag1, 
			f.refer_doc_no,
			a.quantity_normal as fix
			
		FROM 
			trn_tdiary2_or as d 
		left join 
			com_product_master as b 
		on 
			d.product_id=b.product_id 
		left join 
			com_stock_master as c 
		on 
			d.product_id=c.product_id and c.month='".$this->month."' and c.year='".$this->year."'
		left join 
			trn_fix_stock as a 
		on 
			a.product_id=c.product_id
		left join 
			trn_in2shop_list as e 
		on 
			d.product_id=e.product_id and e.flag1='D' 
		left join 
			trn_diary1 as f 
		on 
			e.doc_no=f.refer_doc_no  
       	group by 
			d.product_id,d.seq  
		order by 
			d.".$sort;
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
	
	public function sumivn($tbl){
		$sql="
		select 
			sum(tbl_sumall.sum_qty) as quantity,sum(tbl_sumall.sumpriceproduct) as price  
		from 
			(select 
				tbl_sumproduct.sum_qty,(tbl_sumproduct.sum_qty*tbl_sumproduct.sum_price) as sumpriceproduct 
			from 
				(SELECT 
					product_id,sum(quantity) as sum_qty,price as sum_price 
				FROM 
					$tbl  
				group by 
					product_id) as tbl_sumproduct) as tbl_sumall";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function delettemlist($seq,$tbl){
		$check=count($seq);
		if($check>0){
			foreach($seq as $val_seq){
				/*$where = array(
						'seq = ?' => $val_seq
				);
				$rs=$this->db->delete($tbl, $where);*/
				$sql="delete from $tbl where seq='$val_seq'";
				$rs=$this->db->query($sql);
			}
		}
		if($rs){	
			$status="y";
		}else{
			$status="n";
		}
		return $status;
	}
	
	public function deleteordertemp($tbl){
		$rs=$this->TrancateTable($tbl);
		if($rs){
			$out="y";
		}else{
			$out="n";
		}
		return $out;
	}
	
	public function TrancateTable($table){
		if($table=="")return false;
		$rs=$this->db->query('TRUNCATE TABLE '.$table);
		return $rs;
	}
	
	public function tranfertmpor2diaryor($doc_tp,$status_no,$doc_remark,$tbl_tdiray_or){
		$this->doc_no=$this->gendocno($doc_tp);
		$sum=$this->sumivn($tbl_tdiray_or);
		
	
		$check_quota=$this->checkquota($sum[0]['price']);
		
		if($check_quota=="y"){
			$sql_or2="INSERT INTO
						trn_diary2_or(corporation_id,company_id,branch_id,doc_date,doc_time,doc_no,doc_tp,status_no,flag,seq,product_id,price,quantity,amount,reg_date,reg_time,reg_user,short_qty,short_amt,ret_short_qty)
						SELECT
							corporation_id,company_id,branch_id,'".$this->date."','".$this->time."','".$this->doc_no."',doc_tp,status_no,flag,seq,product_id,price,quantity,amount,'".$this->date."','".$this->time."','".$this->user_id."',short_qty,short_amt,ret_short_qty 
						FROM
							trn_tdiary2_or";
							
			$rs_or2=$this->db->query($sql_or2);
			
			if($rs_or2){
				$sql_or1=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'flag'=>'',
					'doc_tp'=>$doc_tp,
					'status_no'=>$status_no,
					'quantity'=>$sum[0]['quantity'],
					'amount'=>$sum[0]['price'],
					'net_amt'=>$sum[0]['price'],
					'remark1'=>$doc_remark,
			    	'reg_date'=>$this->date,
			    	'reg_time'=>$this->time,
			    	'reg_user'=>$this->user_id
				);
				$rs_or1=$this->db->insert('trn_diary1_or', $sql_or1);
				if($rs_or1){
					$get_docno=$this->getdocno($doc_tp);

					$add_docno=$get_docno[0]['doc_no']+1;
					$data_update_docno=array(
							'doc_no'=>$add_docno
					);
					
					$where_update_docno = array(
								'corporation_id = ?' => $this->corporation_id,
								'company_id = ?' => $this->company_id,
								'branch_id = ?' => $this->branch_id,
								'doc_tp = ?' => $doc_tp
					);
					//echo ":".$add_docno."::".$this->corporation_id.":".$this->company_id.":".$this->branch_id.":".$doc_tp;
					$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
					if($rs_update_docno){
						$rs=$this->TrancateTable('trn_tdiary2_or');
						if($rs){
							$quota=$this->getquotadetail();
							$balance_amount=($quota[0]['balance_amount']-$sum[0]['price']);
							$month_order=($quota[0]['month_order']+$sum[0]['price']);
							$data_quota=array(
								'month_order'=>$month_order,
								'balance_amount'=>$balance_amount
							);
							$where_quota = array(
										'corporation_id = ?' => $this->corporation_id,
										'company_id = ?' => $this->company_id,
										'branch_id = ?' => $this->branch_id,
										'month  = ?' => $this->month,
										'year  = ?' => $this->year
							);
							$rs_quota=$this->db->update('com_order_quota', $data_quota, $where_quota);
							//print_r(array($data_quota,$where_quota));
							if($rs_quota){
								$status="y:".$this->doc_no;
							}else{
								$this->clear_diary($this->doc_no);
								$status="w: st1";
							}
						}else{
							//empty table
							$this->clear_diary($this->doc_no);
							$status="w: st2";
						}
						
					}else{
						//empty table
						$this->clear_diary($this->doc_no);
						$status="w: st3";
					}
					
				}else{
					//empty table
					$this->clear_diary($this->doc_no);
					$status="w: st4";
				}
			}else{
				//empty table
				$this->clear_diary($this->doc_no);
				$status="w: st5";
			}
		}else{
			$status="n: ";
		}
		return $status;
	}

	public function tranfertmpor2diaryorextra($doc_tp,$status_no,$doc_remark,$tbl_tdiray_or){
		$this->doc_no=$this->gendocno($doc_tp);
		$sum=$this->sumivn($tbl_tdiray_or);
		
		exit();
		//$check_quota=$this->checkquota($sum[0]['price']);
		$check_quota="y";
		if($check_quota=="y"){
			$sql_or2="INSERT INTO
						trn_diary2_or(corporation_id,company_id,branch_id,doc_date,doc_time,doc_no,doc_tp,status_no,flag,seq,product_id,price,quantity,amount,reg_date,reg_time,reg_user)
						SELECT
							corporation_id,company_id,branch_id,'".$this->date."','".$this->time."','".$this->doc_no."',doc_tp,status_no,flag,seq,product_id,price,quantity,amount,'".$this->date."','".$this->time."','".$this->user_id."' 
						FROM
							trn_tdiary2_or";
			$rs_or2=$this->db->query($sql_or2);
			if($rs_or2){
				$sql_or1=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'flag'=>'',
					'doc_tp'=>$doc_tp,
					'status_no'=>$status_no,
					'quantity'=>$sum[0]['quantity'],
					'amount'=>$sum[0]['price'],
					'net_amt'=>$sum[0]['price'],
					'remark1'=>$doc_remark,
			    	'reg_date'=>$this->date,
			    	'reg_time'=>$this->time,
			    	'reg_user'=>$this->user_id
				);
				$rs_or1=$this->db->insert('trn_diary1_or', $sql_or1);
				if($rs_or1){
					$get_docno=$this->getdocno($doc_tp);
					$add_docno=$get_docno[0]['doc_no']+1;
					$data_update_docno=array(
							'doc_no'=>$add_docno
					);
					$where_update_docno = array(
								'corporation_id = ?' => $this->corporation_id,
								'company_id = ?' => $this->company_id,
								'branch_id = ?' => $this->branch_id,
								'doc_tp = ?' => $doc_tp
					);
					$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
					if($rs_update_docno){
						$rs=$this->TrancateTable('trn_tdiary2_or');
						if($rs){
							$quota=$this->getquotadetail();
							$balance_amount=($quota[0]['balance_amount']-$sum[0]['price']);
							$month_order=($quota[0]['month_order']+$sum[0]['price']);
							$data_quota=array(
								'month_order'=>$month_order,
								'balance_amount'=>$balance_amount
							);
							$where_quota = array(
										'corporation_id = ?' => $this->corporation_id,
										'company_id = ?' => $this->company_id,
										'branch_id = ?' => $this->branch_id,
										'month  = ?' => $this->month,
										'year  = ?' => $this->year
							);
							$rs_quota=$this->db->update('com_order_quota', $data_quota, $where_quota);
							if($rs_quota){
								$status="y:".$this->doc_no;
							}else{
								$this->clear_diary($this->doc_no);
								$status="w: ";
							}
						}else{
							//empty table
							$this->clear_diary($this->doc_no);
							$status="w: ";
						}
						
					}else{
						//empty table
						$this->clear_diary($this->doc_no);
						$status="w: ";
					}
					
				}else{
					//empty table
					$this->clear_diary($this->doc_no);
					$status="w: ";
				}
			}else{
				//empty table
				$this->clear_diary($this->doc_no);
				$status="w: ";
			}
		}else{
			$status="n: ";
		}
		return $status;
	}	

	public function tranfertmpor2diaryiq($doc_tp,$status_no,$doc_remark,$inv){
		$this->doc_no=$this->gendocno($doc_tp);
		$sum=$this->sumivn("trn_tdiary2_iq");
		$qty=$sum[0]['quantity'];
		$price=$sum[0]['price'];
		$rs="y";
		$this->db->beginTransaction();
		try {
			$sql_iq2="
			INSERT INTO
				trn_diary2_iq(
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
				amount,
				reg_date,
				reg_time,
				reg_user
				)
			SELECT
				corporation_id,
				company_id,
				branch_id,
				'".$this->date."',
				'".$this->time."',
				'".$this->doc_no."',
				doc_tp,
				status_no,
				flag,
				seq,
				product_id,
				price,
				quantity,
				amount,
				'".$this->date."',
				'".$this->time."',
				'".$this->user_id."' 
			FROM
				trn_tdiary2_iq";
			$this->db->query($sql_iq2);
		
			$sql_iq1="
			INSERT INTO 
				trn_diary1_iq 
			SET 
				corporation_id='$this->corporation_id', 
				company_id='$this->company_id', 
				branch_id='$this->branch_id', 
				doc_date='$this->date', 
				doc_time='$this->time', 
				doc_no='$this->doc_no', 
				doc_tp='$doc_tp', 
				status_no='$status_no', 
				refer_doc_no='$inv', 
				quantity='$qty', 
				amount='$price', 
				doc_remark='$doc_remark', 
				reg_date='$this->date', 
				reg_time='$this->time', 
				reg_user='$this->user_id' 
			";
			$this->db->query($sql_iq1);
		
			$get_docno=$this->getdocno($doc_tp);
			$add_docno=$get_docno[0]['doc_no']+1;
			
			$update_docno="
			UPDATE 
				com_doc_no 
			SET 
				doc_no='$add_docno' 
			WHERE 
				corporation_id='$this->corporation_id' 
				and company_id='$this->company_id' 
				and branch_id='$this->branch_id' 
				and doc_tp='$doc_tp'
			";
			$this->db->query($update_docno);
			$this->TrancateTable("trn_tdiary2_iq");
			
			$this->db->commit();
		}catch (Exception $e) {
			$this->db->rollback();
			$rs="n";
		}
		return $rs.":".$this->doc_no;
	}
	
	public function tranfertmpor2diaryortester($doc_tp,$status_no,$doc_remark){
		$this->doc_no=$this->gendocno($doc_tp);
		$sum=$this->sumivn();
		$check_quota=$this->checkquota($sum[0]['price']);
		if($check_quota=="y"){
			$sql_or2="INSERT INTO
						trn_diary2_or(corporation_id,company_id,branch_id,doc_date,doc_time,doc_no,doc_tp,status_no,flag,seq,product_id,price,quantity,amount,reg_date,reg_time,reg_user)
						SELECT
							corporation_id,company_id,branch_id,'".$this->date."','".$this->time."','".$this->doc_no."',doc_tp,status_no,flag,seq,product_id,price,quantity,amount,'".$this->date."','".$this->time."','".$this->user_id."' 
						FROM
							trn_tdiary2_or";
			$rs_or2=$this->db->query($sql_or2);
			if($rs_or2){
				$sql_or1=array(
					'corporation_id'=>$this->corporation_id,
					'company_id'=>$this->company_id,
					'branch_id'=>$this->branch_id,
					'doc_date'=>$this->date,
					'doc_time'=>$this->time,
					'doc_no'=>$this->doc_no,
					'flag'=>'',
					'doc_tp'=>$doc_tp,
					'status_no'=>$status_no,
					'quantity'=>$sum[0]['quantity'],
					'amount'=>$sum[0]['price'],
					'net_amt'=>$sum[0]['price'],
					'remark1'=>$doc_remark,
			    	'reg_date'=>$this->date,
			    	'reg_time'=>$this->time,
			    	'reg_user'=>$this->user_id
				);
				$rs_or1=$this->db->insert('trn_diary1_or', $sql_or1);
				if($rs_or1){
					$get_docno=$this->getdocno($doc_tp);
					$add_docno=$get_docno[0]['doc_no']+1;
					$data_update_docno=array(
							'doc_no'=>$add_docno
					);
					$where_update_docno = array(
								'corporation_id = ?' => $this->corporation_id,
								'company_id = ?' => $this->company_id,
								'branch_id = ?' => $this->branch_id,
								'doc_tp = ?' => $doc_tp
					);
					$rs_update_docno=$this->db->update('com_doc_no', $data_update_docno, $where_update_docno);
					if($rs_update_docno){
						$rs=$this->TrancateTable('trn_tdiary2_or');
						if($rs){
								$status="y:".$this->doc_no;
						}else{
							//empty table
							$this->clear_diary($this->doc_no);
							$status="w: ";
						}
						
					}else{
						//empty table
						$this->clear_diary($this->doc_no);
						$status="w: ";
					}
					
				}else{
					//empty table
					$this->clear_diary($this->doc_no);
					$status="w: ";
				}
			}else{
				//empty table
				$this->clear_diary($this->doc_no);
				$status="w: ";
			}
		}else{
			$status="n: ";
		}
		return $status;
	}
	
	public function clear_diary($doc_no){
		$sql="
		delete 
			trn_diary2_or,trn_diary1_or 
		from 
			trn_diary2_or 
		right join 
			trn_diary1_or 
		on 
			trn_diary2_or.doc_no=trn_diary1_or.doc_no 
		where 
			trn_diary1_or.doc_no='$doc_no'";
		$stmt=$this->db->query($sql);
	}
	
	public function checkquota($getsum){
		$quota=$this->getquotadetail();
		if(!empty($quota[0]['month_order'])){
			$add_month_order=($quota[0]['month_order']+$getsum);
			$check=($quota[0]['month_amount']-$add_month_order);
		}
		if(empty($check)){
			$status="n";
		}else{
			if($check>=0){
				$status="y";
			}else{
				$status="n";
			}
		}
		return $status;
	}
	
	public function gettypeproduct($doc_tp){
		$sql=$this->db->select()
	    		   ->from('com_doc_status',array('doc_tp','status_no','description'))
	    		   ->where('doc_tp=?',$doc_tp);
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function checkdocnodiaryor(){
		$sql=$this->db->select()
	    		   ->from('trn_tdiary2_or',array('doc_tp','status_no','product_id'));
    	$data=$sql->query()->fetchAll();
	    return $data;
	}
	
	public function producttester($product_id,$monthofproduct){
		$getmonth=$this->year."-".$monthofproduct;
	    $sql="
		SELECT 
			* 
		FROM 
			trn_diary2_or
		WHERE 
			corporation_id='".$this->corporation_id."' 
			and company_id='".$this->company_id."' 
			and doc_tp='OR' 
			and status_no='71' 
			and substring(doc_date,1,7)='".$getmonth."' 
			and product_id='".$product_id."'";
		$stmt=$this->db->query($sql);
	    $data=$stmt->fetchAll();
		$num=count($data);
		return $num;
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
	
	public function getdocnodetail($doc_no){
		$sql="
		select 
			tbl_iq.product_id, tbl_iq.price, tbl_iq.quantity, tbl_iq.amount, tbl_prod.name_product 
		from 
			trn_diary2_iq as tbl_iq 
		left join 
			com_product_master as tbl_prod 
		on 
			tbl_iq.product_id=tbl_prod.product_id 
		where 
			tbl_iq.doc_no='$doc_no' 
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getdocnohead($doc_no){
		$sql="
		select 
			`doc_date`,`doc_time`,`doc_no`,`refer_doc_no`,`quantity`,`amount`,`reg_user`,`doc_remark`    
		from 
			trn_diary1_iq 
		where 
			doc_no='$doc_no'
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function getproductinfo($product){
		$sql= "SELECT quantity,reg_date FROM trn_diary2_or WHERE product_id='".$product."' ORDER BY reg_date DESC, reg_time DESC LIMIT 0,1";
		$order = $this->db->fetchAll($sql);
		//print_r($order);

		$sql="SELECT onhand FROM com_stock_km WHERE product_id='".$product."'";
		$km=$this->db->fetchOne($sql);
		
		$sql="SELECT quantity_normal FROM trn_fix_stock WHERE product_id='".$product."' AND branch_id='". $this->branch_id."'";
		$fix=$this->db->fetchOne($sql);

		$sql="SELECT onhand FROM com_stock_master AS A WHERE A.product_id='".$product."' AND A.year='".date('Y')."' AND A.month = '".date("n")."' ";
		$bal=$this->db->fetchOne($sql);
		
		$myDate = date("Y-m", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-5 month" ) );

		$sql = "SELECT substring(A.doc_date,1,7)as mm,A.product_id,sum(A.quantity) AS ssum FROM (SELECT * FROM trn_diary2 WHERE product_id='".$product."') AS A WHERE A.doc_tp IN ('SL', 'VT') AND substring(A.doc_date,1,7) >= '$myDate'  GROUP BY substring(A.doc_date,1,7)  ";
		$stmt=$this->db->query($sql);
		$avg=$stmt->fetchAll();
		$avg = $this->calAVG($avg);

		$km = $km?round($km):0;
		$fix = $fix?round($fix):0;  
		$bal = $bal?round($bal):0;
		$avg = $avg?round($avg):0;
		if(count($order)){
			$order_no = $order[0]['quantity'];
			$order_date = $order[0]['reg_date'];
		}else{
			$order_no = 0;
			$order_date = '0';
		}
		return array('order'=>round($order_no)." ชิ้น",'orderdate'=>$order_date,'km'=>$km, 'fix'=>$fix, 'bal'=>$bal, 'avg'=>$avg);
	}

	public function calAVG($avg){
		$loop = count($avg);
		//print_r($avg);
		//exit();
		//echo $loop;
		$darr = array();
		if($loop==0){
			return 0;
		}elseif($loop > 4){
			//echo "incase";
			
			while($a = array_pop($avg)){
				array_push($darr,$a['ssum']);
				//$darr[$ptr]=0;
			}
			sort($darr,1);
			array_pop($darr);
			array_shift($darr);
			return ceil(array_sum($darr)/($loop-2));

		}else{
			$i = 0;
			$dd = 0;
			while($a = array_pop($avg)){
				$dd+=$a['ssum'];
				$i++;
			}	
			return $dd/$i;
		}

	}

	public function initOrderQuota(){
		$myDate = date("Y-m", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-5 month" ) );

		$sql = "SELECT substring( A.doc_date, 1, 7 ) AS mm, sum( amount ) AS ssum FROM `trn_diary1` AS A WHERE A.doc_tp IN ('SL', 'VT') AND substring( A.doc_date, 1, 7 ) > '$myDate' GROUP BY substring( A.doc_date, 1, 7 )";
		$stmt=$this->db->query($sql);
		$avg=$stmt->fetchAll();
		//print_r($avg);
		//exit();
		$avg = $this->calAVG($avg);
		$insert_arr=array(
			'corporation_id'=>$this->corporation_id,
			'company_id'=>$this->company_id,
			'branch_id'=>$this->branch_id,
			'month'=>$this->month,
			'year'=>$this->year,
			'month_amount'=>$avg,
			'balance_amount'=>$avg);
		//print_r($insert_arr);
		//exit();
		$rs_tdiary2_or=$this->db->insert('com_order_quota', $insert_arr);
		//echo $rs_tdiary2_or;
		//exit();
		return array('0'=>array('month_amount'=>$avg,'month_order'=>0,'balance_amount'=>$avg));
	}
}	
?>