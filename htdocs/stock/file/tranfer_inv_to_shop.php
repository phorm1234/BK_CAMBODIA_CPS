<?php 
//connect Ji net
$ip_ji="10.100.53.2";
$user_ji="master";
$password_ji="master";
$db="pos_ssup";
$local_ji=@mysql_connect($ip_ji,$user_ji,$password_ji);
@mysql_query("SET character_set_results=tis620");
@mysql_query("SET character_set_client=tis620");
@mysql_query("SET character_set_connection=tis620");
//end

//connect localhost 
$ip="localhost";
$user="pos-ssup";
$password='P0z-$$up';
$local=@mysql_connect($ip,$user,$password);
@mysql_query("SET character_set_results=tis620");
@mysql_query("SET character_set_client=tis620");
@mysql_query("SET character_set_connection=tis620");
//end

//get shop
$sql_system="select branch_id from com_branch where active='1' limit 1 ";
$rs_system=mysql_db_query("pos_ssup",$sql_system,$local);
$branch_id=mysql_fetch_array($rs_system);
$shop=$branch_id[0];
//echo "Shop : ".$shop."<br>";
//end

//$ip=$_SERVER['SERVER_ADDR'];
$ip=exec("ip addr list eth0 |grep 'inet ' |cut -d' ' -f6|cut -d/ -f1");

class porcInv{
	//save log status
	public function save_log($shop,$status,$ip,$detail,$doc_no){
		global $local;
		global $local_ji;
		global $db;
		$sql_insert="insert into logfile_invoice set shop='$shop', doc_no='$doc_no', ip='$ip', `status`='$status', detail='$detail', date_run=NOW(), time_run=NOW() ";
		$rs_insert=mysql_db_query("pos_ssup",$sql_insert,$local_ji);
		echo $detail;
	}

	//function update สถานะการ transfer
	public function updat_status($doc_no,$shop,$ip){
		global $local;
		global $local_ji;
		global $db;
		$sql_chk="
		SELECT 
			if(sum(b.quantity) is null,0,1) as checknull,a.quantity as aqty,sum(b.quantity) as bqty 
		FROM 
			trn_in2shop_head as a 
		left join 
			trn_in2shop_list as b 
		on 
			a.doc_no=b.doc_no 
		where 
			a.doc_no='$doc_no' and a.branch_id='$shop' "; 
			
		$rs_chk=mysql_db_query("pos_ssup",$sql_chk,$local);
		$chk=mysql_fetch_array($rs_chk);
		if($chk['checknull']=="1"){
			if($chk['aqty']==$chk['bqty']){
				$sql_update="
				update 
					trn_in2shop_head  as a 
				left join 
					trn_in2shop_list as b 
				on 
					a.doc_no=b.doc_no 
				set 
					a.status_transfer='Y',b.status_transfer='Y' 
				where 
					a.doc_no='$doc_no' and a.branch_id='$shop'
				";
				$rs_update=mysql_db_query("pos_ssup",$sql_update,$local_ji);
				if($rs_update){
					$detail="complete";
					$status="4";
				}else{
					$detail="ไม่สามารถ update สถานะ status_transfer";
					$status="3";
				}
			}else{
				$sql_del="
				delete 
					a.*,b.* 
				from 
					trn_in2shop_head as a 
				left 
					join trn_in2shop_list as b 
				on 
					a.doc_no=b.doc_no 
				where 
					a.doc_no='$doc_no' and a.branch_id='$shop'
				";
				$rs_del=mysql_db_query("pos_ssup",$sql_del,$local);
				if($rs_del){
					$detail="จำนวนหัวกับตัวไม่เท่ากัน";
					$status="5";
				}else{
					$detail="error";
					$status="6";
				}
			}
		}else{
			$detail="ไม่พบจำนวนใน trn_in2shop_list";
			$status="7";
		}
		$this->save_log($shop,$status,$ip,$detail,$doc_no);
	}
	//end function
}

$porcInv = new porcInv();

if(!empty($shop)){
	$sql_data="select * from trn_in2shop_head where status_transfer='N' and branch_id='$shop' ";
	$rs_data=mysql_db_query($db,$sql_data,$local_ji);
	$num_row=mysql_num_rows($rs_data);
	if($num_row>0){
		while($row=mysql_fetch_array($rs_data)){
			$sql_chk="select doc_no from trn_in2shop_head where doc_no='$row[doc_no]' ";
			$rs_chk=mysql_db_query($db,$sql_chk,$local);
			$chk=mysql_fetch_array($rs_chk);
			if(empty($chk['doc_no'])){
				$sql_insert_head="
				insert into
					trn_in2shop_head 
				set 
					corporation_id='$row[corporation_id]', 
					company_id='$row[company_id]', 
					branch_id='$row[branch_id]', 
					doc_date='$row[doc_date]', 
					doc_no='$row[doc_no]', 
					flag1='$row[flag1]', 
					status_transfer='Y', 
					quantity='$row[quantity]', 
					reg_date=NOW(), 
					reg_time=NOW(), 
					reg_user='system'
				";
				$rs_insert_head=mysql_db_query($db,$sql_insert_head,$local);
				if($rs_insert_head){
					$sql_get_list="select * from trn_in2shop_list where doc_no='$row[doc_no]' and status_transfer='N' and branch_id='$shop' ";
					echo $sql_get_list."<br>";
					$rs_get_list=mysql_db_query($db,$sql_get_list,$local_ji);
					while($data_list=mysql_fetch_array($rs_get_list)){
						$sql_list="
						insert into
							trn_in2shop_list   
						set 
							corporation_id='OP',
							company_id='OP', 
							branch_id='$data_list[branch_id]', 
							doc_date='$data_list[doc_date]', 
							doc_no='$data_list[doc_no]', 
							seq='$data_list[seq]', 
							product_id='$data_list[product_id]', 
							status_transfer='Y',
							product_name='$data_list[product_name]', 
							quantity='$data_list[quantity]', 
							flag1='$data_list[flag1]', 
							price='$data_list[price]', 
							barcode='$data_list[barcode]', 
							product_status='N',
							reg_date=NOW(), 
							reg_time=NOW(), 
							reg_user='system' 
						";
						$rs_list=mysql_db_query($db,$sql_list,$local);	
					}
					$porcInv->updat_status($row['doc_no'],$shop,$ip);
				}else{
					$porcInv->updat_status($row['doc_no'],$shop,$ip);
				}
				
			}else{
				$detail="มี invoice อยู่ในระบบแล้ว ";
				$status="1";
			}
		}
	}else{
		$detail="ไม่มีเอกสาร invoice ใหม่";
		$status="8";
	}
}else{
	$detail="ไม่พบรหัสshop";
	$status="0";
}
$porcInv->save_log($shop,$status,$ip,$detail,"");
?>
