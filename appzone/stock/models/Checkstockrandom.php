<?php 
//new v.1.1 เพิ่ม Tester 27/11/12
//new v.1
class Model_Checkstockrandom{	
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
	
	public function checkpassword($passw){
		$sql="
		SELECT  
			user_id,concat(name,' ',surname) as fullname 
		FROM  
			conf_employee 
		WHERE 
			password_id='$passw' 
			and group_id='AUDIT' ";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		$count=count($data);
		if($count>0){
			$data=$data;
		}else{
			$data="n";
		}
		return $data;
	}
	
	public function inserttempproductrandom($backdate,$chk_product){
		$rs="y";
		$this->db->beginTransaction();
		try {
			$this->db->query("TRUNCATE TABLE trn_product_random_stock");
			$this->db->commit();
		}catch (Exception $e) {
		    $this->db->rollback();
		    $rs="n";
		}
		//ประมวลผลข้อมูลยอดขาย ย้อนหลังจากวันเปิดบิลปัจจุบัน
		if($rs=="y"){
			$this->db->beginTransaction();
			try {
				$sql_trn_product_random_stock="
				INSERT INTO 
					trn_product_random_stock (`branch_id`, `doc_date`, `product_id`, `price`, `quantity`, `amount`, `sum_quantity`, `sum_amount`, `reg_date`)
				SELECT 
					branch_id,doc_date,product_id,price,quantity,amount,
					sum(quantity) as sum_quantity, sum(amount) as sum_amount, NOW() 
				FROM 
					trn_diary2 
				WHERE 
					doc_date>=date_sub(curdate(),interval $backdate day)
					and doc_tp in('SL','VT') 
				GROUP BY product_id ";
				$this->db->query($sql_trn_product_random_stock);
				$this->db->commit();
			}catch (Exception $e) {
				$this->db->rollback();
				$rs="n";
			}
		}
		
		$this->db->beginTransaction();
		try {
			$this->db->query("TRUNCATE TABLE chk_random_stock");
			$this->db->commit();
		}catch (Exception $e) {
		    $this->db->rollback();
		    $rs="n";
		}
		
		if($rs=="y"){
			$this->db->beginTransaction();
			try {
				if($chk_product=="D"){
					$sql_insert_random_stock=" 
					INSERT INTO 
						chk_random_stock (`corporation_id`,`company_id`,`product_id`,`name_product`,`price`,`begin`,`onhand`,`qty_sale_this_month`,`qty_sale_30_day`,`reg_date`,`reg_time`,`reg_user`) 
					SELECT 
						'$this->corporation_id',
						'$this->company_id',
						tbl_master.product_id,
						tbl_master.name_product,
						tbl_master.price,
						tbl_master.begin,
						tbl_master.onhand,
						sum(tbl_master.sum_qty),
						tbl_master.trn_rand_30, 
						NOW(),
						NOW(),
						'$this->user_id'  
					FROM  
						(SELECT 
						    dia.product_id,
						    dia.doc_tp,
						    product.name_product,
						    dia.price,
						    stock.begin,
						    stock.onhand,
						    sum(dia.quantity) as sum_qty,
						    com_doc.stock_st, 
						    sum(dia.quantity)*com_doc.stock_st as cus_stock, 
						    trn_rand.sum_quantity as trn_rand_30 
						FROM 
						    trn_diary2 as dia 
						LEFT JOIN 
						    com_stock_master as stock 
						ON 
						    dia.product_id=stock.product_id 
						LEFT JOIN 
						    com_product_master as product 
						ON 
						    dia.product_id=product.product_id 
						LEFT JOIN 
							com_doc_no as com_doc 
						ON 
						   dia.doc_tp=com_doc.doc_tp 
						LEFT JOIN 
							trn_product_random_stock as trn_rand 
						ON 
							dia.product_id=trn_rand.product_id 
						WHERE 
						    dia.doc_date BETWEEN date_add(curdate(),interval -30 day)  AND date_add(curdate(),interval 0 day) 
						    and stock.month=month(now()) 
						    and stock.year=year(now()) 
						    and dia.doc_tp in('SL','VT') 
						    and substring(dia.product_id,1,1)<>'9' 
						    and stock.onhand>'0' 
						GROUP BY 
						    dia.product_id,dia.doc_tp) as tbl_master 
						GROUP BY 
						    tbl_master.product_id 
						ORDER BY 
							tbl_master.trn_rand_30 DESC";
					$this->db->query($sql_insert_random_stock);
				}else{
					$sql_insert_random_stock_tester=" 
					INSERT INTO 
						chk_random_stock (`corporation_id`,`company_id`,`product_id`,`name_product`,`price`,`begin`,`onhand`,`qty_sale_this_month`,`qty_sale_30_day`,`reg_date`,`reg_time`,`reg_user`) 
					SELECT 
						'$this->corporation_id',
						'$this->company_id',
						stock.product_id,
						product.name_product,
						product.price,
						stock.begin,
						stock.onhand,
						'0',
						'0', 
						NOW(),
						NOW(),
						'$this->user_id'  
					FROM  
						com_stock_master as stock 
					INNER JOIN 
						com_product_master as product 
					ON 
						stock.product_id=product.product_id 
					WHERE 
						stock.month='$this->month' and stock.year='$this->year' and substring( stock.product_id, 1, 1 ) = '1'";
					$this->db->query($sql_insert_random_stock_tester);
				}	
				$this->db->commit();
			}catch (Exception $e) {
				$this->db->rollback();
				$rs="n";
			}
		}
		//update check random stock
		if($rs=="y"){
			$this->db->beginTransaction();
			try {
				$sql_update_stock="
				UPDATE 
					chk_random_stock as chk_rand 
				LEFT JOIN 
					com_stock_master as com_stock 
				ON  
					chk_rand.product_id=com_stock.product_id 
				SET 
					com_stock.onhand=chk_rand.onhand,
					option1=if(chk_rand.id>=1 and chk_rand.id<=70,'A',if(chk_rand.id>=71 and chk_rand.id<=100,'B',''))
				WHERE 
					com_stock.month=month(now()) 
					and com_stock.year=year(now()) 
				";
				$this->db->query($sql_update_stock);
				$this->db->commit();
			}catch (Exception $e) {
				$this->db->rollback();
				$rs="n";
			}
		}
		
		if($rs=="y"){
			$this->db->beginTransaction();
			try {
				$sql_get_random_stock="
				SELECT 
					product_id,sum(quantity) as qty,doc_tp  
				FROM 
					trn_diary2 
				WHERE 
					doc_tp IN('TI','TO','CN','DN','AI','AO')
				GROUP BY
					product_id,doc_tp 
				";
				$stmt=$this->db->query($sql_get_random_stock);
				$data=$stmt->fetchAll();
				$chk=count($data);
				if($chk>0){
					foreach($data as $val){
						switch ($val['doc_tp']) {
							case "TI":
								$doc_tp="qty_tran_in";
							    break;
							case "TO":
								$doc_tp="qty_tran_out";
							    break;
							case "CN":
								$doc_tp="qty_cn";
							    break;
							case "DN":
								$doc_tp="qty_dn";
							    break;
							case "AI":
								$doc_tp="qty_adjust_in";
							    break;
							case "AO":
								$doc_tp="qty_adjust_out";
							    break;
						}
						$sql_updat_stock_oth="
						UPDATE 
							chk_random_stock 
						SET 
							$doc_tp='$val[qty]' 
						WHERE 
							product_id='$val[product_id]' 
						";
						$this->db->query($sql_updat_stock_oth);
					}
				}
				
				$this->db->commit();
			}catch (Exception $e) {
				$this->db->rollback();
				$rs="n";
			}
		}
		
		return $rs;
	}
	
	public function viewrandomstock($where){
		$sql="
		SELECT 
			chk_rand.product_id,chk_rand.name_product,chk_rand.begin,chk_rand.onhand,
			chk_rand.qty_tran_in,chk_rand.qty_tran_out,chk_rand.qty_cn,chk_rand.qty_dn,
			chk_rand.qty_adjust_in,chk_rand.qty_adjust_in,chk_rand.qty_tran_in,
			chk_rand.qty_tran_out,chk_rand.qty_cn,chk_rand.qty_adjust_in,chk_rand.qty_adjust_out,
			chk_rand.qty_sale_this_month,chk_rand.qty_sale_30_day, 
			chk_rand.option1,chk_rand.option2,prod.price 
		FROM 
			chk_random_stock as chk_rand 
		LEFT JOIN 
			trn_product_random_stock as trn_prod 
		ON 
			chk_rand.product_id=trn_prod.product_id 
		LEFT JOIN 
			com_product_master as prod 
		ON 
			chk_rand.product_id=prod.product_id 
		WHERE 1 ";
		if(!empty($where)){
			$sql.=$where;
		}
		$sql.="ORDER BY 
			chk_rand.product_id
		";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function sumrandomstock($where){
		$sql="
		SELECT  
		    sum(tbl_master.begin) as sumall_begin, 
		    sum(tbl_master.sum_begin) as sumall_price_begin, 
		    sum(tbl_master.onhand) as sumall_onhand, 
		    sum(tbl_master.sum_onhand) as sumall_price_onhand, 
		    sum(tbl_master.qty_tran_in) as sumall_ti, 
		    sum(tbl_master.sum_ti) as sumall_price_ti, 
		    sum(tbl_master.qty_tran_out) as sumall_to, 
		    sum(tbl_master.sum_to) as sumall_price_to, 
		    sum(tbl_master.qty_cn) as sumall_cn, 
		    sum(tbl_master.sum_cn) as sumall_price_cn, 
		    sum(tbl_master.qty_dn) as sumall_dn, 
		    sum(tbl_master.sum_dn) as sumall_price_dn, 
		    sum(tbl_master.qty_adjust_in) as sumall_ai,
		    sum(tbl_master.sum_ai) as sumall_price_ai, 
		    sum(tbl_master.qty_adjust_out) as sumall_ao,
		    sum(tbl_master.sum_ao)  as sumall_price_ao,
		    sum(tbl_master.qty_sale_this_month) as sumall_sl,
		    sum(tbl_master.sum_sl)  as sumall_price_sl 
		FROM  
		    (SELECT 
		        a.product_id,b.price,
		        a.begin,(a.begin*b.price) as sum_begin,
		        a.onhand,(a.onhand*b.price) as sum_onhand,
		        a.qty_tran_in,(a.qty_tran_in*b.price) as sum_ti,
		        a.qty_tran_out,(a.qty_tran_out*b.price) as sum_to, 
		        a.qty_cn,(a.qty_cn*a.price) as sum_cn,  
		        a.qty_dn,(a.qty_dn*b.price) as sum_dn,
		        a.qty_adjust_in,(a.qty_adjust_in*b.price) as sum_ai, 
		        a.qty_adjust_out,(a.qty_adjust_out*b.price) as sum_ao, 
		        a.qty_sale_this_month,(a.qty_sale_this_month*a.price) as sum_sl 
		    FROM 
		    	chk_random_stock as a 
		    LEFT JOIN  
		    	com_product_master as b 
		    ON  
		    	a.product_id=b.product_id 
		    WHERE 
		    	1 ";
		if(!empty($where)){
			$sql.=$where;
		}
		$sql.=") as tbl_master";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function printrandomstock($option_1){
		$rs="y";
		if(!empty($option_1)){
			$this->db->beginTransaction();
			try {
				$sql_del="
				UPDATE 
					chk_random_stock 
				SET 
					option2='' 
				";
				$stmt=$this->db->query($sql_del);
				$this->db->commit();
			}catch (Exception $e) {
				$this->db->rollback();
				$rs="n";
			}
			
			if($rs=="y"){
				$this->db->beginTransaction();
				try {
					foreach($option_1 as $val){
						$sql="
						UPDATE 
							chk_random_stock 
						SET 
							option2='X' 
						where 
							product_id='$val'
						"; 
						$stmt=$this->db->query($sql);
					}
					$this->db->commit();
				}catch (Exception $e) {
					$this->db->rollback();
					$rs="n";
				}
			}
		}
		return $rs;
	}
	
	public function docnodetail($doc_tp){
		$sql="
		SELECT 
			min(doc_no) as start,
			max(concat(doc_no)) as end,
			count(doc_no) as count_doc_no,
			sum(net_amt) sum_net_amt
		FROM 
			trn_diary1  
		WHERE  
			(doc_date between DATE_FORMAT(CURDATE(),'%Y-%m-01') 
			and NOW()) 
			and doc_tp='$doc_tp'
		ORDER BY 
			doc_date,doc_time,doc_no";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
	
	public function docnodetail2($doc_tp){
		$sql="
		SELECT 
			doc_no,refer_doc_no,quantity,net_amt
		FROM 
			trn_diary1 
		WHERE  
			(doc_date between DATE_FORMAT(CURDATE(),'%Y-%m-01') 
			and NOW()) 
			and doc_tp='$doc_tp'
		ORDER BY 
			doc_date,doc_time,doc_no";
		$stmt=$this->db->query($sql);
		$data=$stmt->fetchAll();
		return $data;
	}
}
?>