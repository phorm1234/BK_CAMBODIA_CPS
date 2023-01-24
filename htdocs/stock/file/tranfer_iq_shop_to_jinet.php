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
echo "Shop : ".$shop."<br>";
//end

$sql="select * from trn_diary1_iq where status_transfer_ji='' ";
$rs=mysql_db_query($db,$sql,$local);
$num=mysql_num_rows($rs);
if($num>0){
	while($dat=mysql_fetch_array($rs)){
		$sql_insert_iq_head="
		insert into 
			trn_diary1_iq 
		set 
			corporation_id='$dat[corporation_id]', 
			company_id='$dat[company_id]', 
			branch_id='$dat[branch_id]', 
			doc_date='$dat[doc_date]', 
			doc_time='$dat[doc_time]', 
			doc_no='$dat[doc_no]', 
			doc_tp='$dat[doc_tp]', 
			status_no='$dat[status_no]', 
			flag='$dat[flag]', 
			refer_doc_no='$dat[refer_doc_no]', 
			quantity='$dat[quantity]', 
			amount='$dat[amount]', 
			discount='$dat[discount]', 
			net_amt='$dat[net_amt]', 
			vat='$dat[vat]', 
			computer_no='$dat[computer_no]', 
			reg_date='$dat[reg_date]', 
			reg_time='$dat[reg_time]', 
			reg_user='$dat[reg_user]', 
			upd_date='$dat[upd_date]', 
			upd_time='$dat[upd_time]', 
			upd_user='$dat[upd_user]'
		";
		echo $sql_insert_iq_head."<br>";
		$rs_iq_head=mysql_db_query($db,$sql_insert_iq_head,$local_ji);
		if($rs_iq_head){
			
			$sql_up_1="update trn_diary1_iq set status_transfer_ji='Y',date_transfer_ji=NOW() where doc_no='$dat[doc_no]' ";
			$rs_up_1=mysql_db_query($db,$sql_up_1,$local);
			
			if($rs_up_1){
				$sql_select_iq_list="select * from trn_diary2_iq where doc_no='$dat[doc_no]' ";
				$rs_select_iq_list=mysql_db_query($db,$sql_select_iq_list,$local);
				while($dat_list=mysql_fetch_array($rs_select_iq_list)){
					$sql_insert_iq_list="
					insert into 
						trn_diary2_iq 
					set 
						corporation_id='$dat_list[corporation_id]', 
						company_id='$dat_list[company_id]', 
						branch_id='$dat_list[branch_id]', 
						doc_date='$dat_list[doc_date]', 
						doc_time='$dat_list[doc_time]', 
						doc_no='$dat_list[doc_no]', 
						doc_tp='$dat_list[doc_tp]', 
						status_no='$dat_list[status_no]', 
						flag='$dat_list[flag]',
						seq='$dat_list[seq]', 
						product_id='$dat_list[product_id]', 
						price='$dat_list[price]', 
						quantity='$dat_list[quantity]', 
						stock_st='$dat_list[stock_st]', 
						amount='$dat_list[amount]', 
						discount='$dat_list[discount]', 
						net_amt='$dat_list[net_amt]',  
						reg_date='$dat_list[reg_date]', 
						reg_time='$dat_list[reg_time]', 
						reg_user='$dat_list[reg_user]', 
						upd_date='$dat_list[upd_date]', 
						upd_time='$dat_list[upd_time]', 
						upd_user='$dat_list[upd_user]'
					";
					echo $sql_insert_iq_list."<br>";
					$rs_iq_list=mysql_db_query($db,$sql_insert_iq_list,$local_ji);
					if($rs_iq_list){
						$sql_up_2="update trn_diary2_iq set status_transfer_ji='Y',date_transfer_ji=NOW() where doc_no='$dat[doc_no]' and product_id='$dat_list[product_id]' ";
						echo $sql_up_2."<br>";
						$rs_up_2=mysql_db_query($db,$sql_up_2,$local);
					}
				}
			}
		}
	}
}
?>