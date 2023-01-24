<?php
set_time_limit(0);
$doc_date=$_GET['doc_date'];

$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";

//JI
$ji_ip="10.100.53.2";
$ji_user="master";
$ji_pass="master";
$ji_db="pos_ssup";


$ji_db_sevice="service_pos_op";
$num_day=5;






//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$conf="select branch_id  from com_branch_computer group by branch_id";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];
mysql_close($conn_shop);



$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$clear="delete from trn_diary1_tmp where branch_id='$shop'  and doc_date='$doc_date' ";

$result_clear=mysql_db_query($ji_db_sevice,$clear,$conn_ji);
mysql_close($conn_ji);





$conf="select *  from trn_diary1 where  doc_date ='$doc_date' ";	
echo $conf;	
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);
echo "rows=".$rows;

mysql_close($conn_shop);



for($i=1; $i<=$rows; $i++){
	$databill=mysql_fetch_array($result);



			$corporation_id=$databill['corporation_id'];
			$company_id=$databill['company_id'];
			$branch_id=$databill['branch_id'];
			$doc_date=$databill['doc_date'];
			$doc_time=$databill['doc_time'];
			$doc_no=$databill['doc_no'];
			$doc_tp=$databill['doc_tp'];
			$status_no=$databill['status_no'];
			$flag=$databill['flag'];
			$member_id=$databill['member_id']; 
			$member_percent=$databill['member_percent'];
			$special_percent=$databill['special_percent'];
			$refer_member_id=$databill['refer_member_id'];
			$refer_doc_no=$databill['refer_doc_no'];
			$quantity=$databill['quantity'];
			$amount=$databill['amount'];
			$discount=$databill['discount'];
			$member_discount1=$databill['member_discount1'];
			$member_discount2=$databill['member_discount2'];
			$co_promo_discount=$databill['co_promo_discount'];
			$coupon_discount=$databill['coupon_discount'];
			$special_discount=$databill['special_discount'];
			$other_discount=$databill['other_discount'];
			$net_amt=$databill['net_amt'];
			$vat=$databill['vat'];
			$ex_vat_amt=$databill['ex_vat_amt'];
			$ex_vat_net=$databill['ex_vat_net'];
			$coupon_cash=$databill['coupon_cash'];
			$coupon_cash_qty=$databill['coupon_cash_qty'];
			$point1=$databill['point1'];
			$point2=$databill['point2'];
			$redeem_point=$databill['redeem_point'];
			$total_point=$databill['total_point'];
			$co_promo_code=$databill['co_promo_code'];
			$coupon_code=$databill['coupon_code'];
			$special_promo_code=$databill['special_promo_code'];
			$other_promo_code=$databill['other_promo_code'];
			$application_id=$databill['application_id'];
			$new_member_st=$databill['new_member_st'];
			$birthday_card_st=$databill['birthday_card_st'];
			$not_cn_st=$databill['not_cn_st'];
			$paid=$databill['paid'];
			$pay_cash=$databill['pay_cash'];
			$pay_credit=$databill['pay_credit'];
			$pay_cash_coupon=$databill['pay_cash_coupon'];
			$credit_no=$databill['credit_no'];
			$credit_tp=$databill['credit_tp'];
			$bank_tp=$databill['bank_tp'];
			$change=$databill['change'];
			$pos_id=$databill['pos_id'];
			$computer_no=$databill['computer_no'];
			$user_id=$databill['user_id'];
			$cashier_id=$databill['cashier_id'];
			$saleman_id=$databill['saleman_id'];
			$cn_status=$databill['cn_status'];
			$refer_cn_net=$databill['refer_cn_net'];
			$name=$databill['name'];
			$address1=$databill['address1'];
			$address2=$databill['address2'];
			$address3=$databill['address3'];
			$doc_remark=$databill['doc_remark'];
			$create_date=$databill['create_date'];
			$create_time=$databill['create_time'];
			$cancel_id=$databill['cancel_id'];
			$cancel_date=$databill['cancel_date'];
			$cancel_time=$databill['cancel_time'];
			$cancel_tp=$databill['cancel_tp'];
			$cancel_remark=$databill['cancel_remark'];
			$cancel_auth=$databill['cancel_auth'];
			$keyin_st=$databill['keyin_st'];
			$keyin_tp=$databill['keyin_tp'];
			$keyin_remark=$databill['keyin_remark'];
			$post_id=$databill['post_id'];
			$post_date=$databill['post_date'];
			$post_time=$databill['post_time'];
			$transfer_ts=$databill['transfer_ts'];
			$transfer_date=$databill['transfer_date'];
			$transfer_time=$databill['transfer_time'];
			$order_id=$databill['order_id'];
			$order_no=$databill['order_no'];
			$order_date=$databill['order_date'];
			$order_time=$databill['order_time'];
			$acc_name=$databill['acc_name'];
			$bank_acc=$databill['bank_acc'];
			$bank_name=$databill['bank_name'];
			$tel1=$databill['tel1'];
			$tel2=$databill['tel2'];
			$dn_name=$databill['dn_name'];
			$dn_address1=$databill['dn_address1'];
			$dn_address2=$databill['dn_address2'];
			$dn_address3=$databill['dn_address3'];
			$remark1=$databill['remark1'];
			$remark2=$databill['remark2'];
			$deleted=$databill['deleted'];
			$print_no=$databill['print_no'];
			$reg_date=$databill['reg_date'];
			$reg_time=$databill['reg_time'];
			$reg_user=$databill['reg_user'];
			$upd_date=$databill['upd_date'];
			$upd_time=$databill['upd_time'];
			$upd_user=$databill['upd_user'];
			$idcard=$databill['idcard'];
			$special_day=$databill['special_day'];
			$mobile_no=$databill['mobile_no'];
			$point_begin=$databill['point_begin'];


		


			
			
	$insert="
		insert into trn_diary1(corporation_id, company_id, branch_id, doc_date, doc_time, doc_no, doc_tp, status_no, flag, member_id, member_percent, special_percent, refer_member_id, refer_doc_no, quantity, amount, discount, member_discount1, member_discount2, co_promo_discount, coupon_discount, special_discount, other_discount, net_amt, vat, ex_vat_amt, ex_vat_net, coupon_cash, coupon_cash_qty, point1, point2, redeem_point, total_point, co_promo_code, coupon_code, special_promo_code, other_promo_code, application_id, new_member_st, birthday_card_st, not_cn_st, paid, pay_cash, pay_credit, pay_cash_coupon, credit_no, credit_tp, bank_tp, `change`, pos_id, computer_no, user_id, cashier_id, saleman_id, cn_status, refer_cn_net, name, address1, address2, address3, doc_remark, create_date, create_time, cancel_id, cancel_date, cancel_time, cancel_tp, cancel_remark, cancel_auth, keyin_st, keyin_tp, keyin_remark, post_id, post_date, post_time, transfer_ts, transfer_date, transfer_time, order_id, order_no, order_date, order_time, acc_name, bank_acc, bank_name, tel1, tel2, dn_name, dn_address1, dn_address2, dn_address3, remark1, remark2, deleted, print_no, reg_date, reg_time, reg_user, upd_date, upd_time, upd_user,time_up,idcard,special_day,mobile_no,point_begin) values('$corporation_id','$company_id','$branch_id','$doc_date','$doc_time','$doc_no','$doc_tp','$status_no','$flag','$member_id','$member_percent','$special_percent','$refer_member_id','$refer_doc_no','$quantity','$amount','$discount','$member_discount1','$member_discount2','$co_promo_discount','$coupon_discount','$special_discount','$other_discount','$net_amt','$vat','$ex_vat_amt','$ex_vat_net','$coupon_cash','$coupon_cash_qty','$point1','$point2','$redeem_point','$total_point','$co_promo_code','$coupon_code','$special_promo_code','$other_promo_code','$application_id','$new_member_st','$birthday_card_st','$not_cn_st','$paid','$pay_cash','$pay_credit','$pay_cash_coupon','$credit_no','$credit_tp','$bank_tp','$change','$pos_id','$computer_no','$user_id','$cashier_id','$saleman_id','$cn_status','$refer_cn_net','$name','$address1','$address2','$address3','$doc_remark','$create_date','$create_time','$cancel_id','$cancel_date','$cancel_time','$cancel_tp','$cancel_remark','$cancel_auth','$keyin_st','$keyin_tp','$keyin_remark','$post_id','$post_date','$post_time','$transfer_ts','$transfer_date','$transfer_time','$order_id','$order_no','$order_date','$order_time','$acc_name','$bank_acc','$bank_name','$tel1','$tel2','$dn_name','$dn_address1','$dn_address2','$dn_address3','$remark1','$remark2','$deleted','$print_no','$reg_date','$reg_time','$reg_user','$upd_date','$upd_time','$upd_user',now(),'$idcard','$special_day','$mobile_no','$point_begin')
	";
	
	mysql_close($conn_ji);
	$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass);
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_select_db($ji_db_sevice) or die("DB fails");
	$result_insert=mysql_query($insert,$conn_ji) ;
	if($result_insert){
		echo "$doc_date Insert Complete<br>";
	}else{
		echo $insert . "<br>";
		//echo "connect time out<br>";
	}
	mysql_close($conn_ji);

}




?>