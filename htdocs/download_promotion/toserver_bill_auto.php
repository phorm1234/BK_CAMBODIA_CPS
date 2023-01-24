<?php
set_time_limit(0);
if (file_exists("inc/connect.php")) {  //แบบ manual
	include("inc/connect.php");
}else{
    include("/var/www/pos/htdocs/download_promotion/inc/connect.php");	
}

$num_day=5;
//shop

$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) ;
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$conf="select branch_id  from com_branch_computer where branch_id<>'3000' group by branch_id limit 1";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop);
$data_conf=mysql_fetch_array($r_conf);
$shop=$data_conf['branch_id'];




$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$clear="delete from trn_diary1_auto_tmp where branch_id='$shop'";
$result_clear=mysql_db_query($ji_db_sevice,$clear,$conn_ji);





$conf="select *  from trn_diary1 where STR_TO_DATE( concat( doc_date, ' ', doc_time ) , '%Y-%m-%d %H:%i:%s' )>=DATE_SUB(NOW(), INTERVAL 15 MINUTE) ";	
$result=mysql_db_query($dbname,$conf,$conn_shop);
$rows=mysql_num_rows($result);
echo "$conf | rows=".$rows;

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

			$select_test="select '1' ";
			$result_select_test=mysql_db_query($ji_db_sevice,$select_test,$conn_ji);
			if($result_select_test){
			
			}else{
				echo "Time out <br>";
				$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass);
				mysql_query("SET character_set_results=utf8");
				mysql_query("SET character_set_client=utf8");
				mysql_query("SET character_set_connection=utf8");
			}

	if($flag=="C"){
		/*
		$up_cancle="UPDATE promo_play_history SET flag='C' where doc_no='$doc_no'  ";
		echo "$up_cancle<br>";
		$result_up_cancle=mysql_db_query($ji_db_sevice,$up_cancle,$conn_ji);
		*/
		
		$up_cancle="UPDATE trn_diary1 SET flag='C' where doc_no='$doc_no'  limit 1";
		echo "$up_cancle<br>";
		$result_up_cancle=mysql_db_query($ji_db_sevice,$up_cancle,$conn_ji);
	}
			
	$insert="
		insert into trn_diary1_auto_tmp (corporation_id, company_id, branch_id, doc_date, doc_time, doc_no, doc_tp, status_no, flag, member_id, member_percent, special_percent, refer_member_id, refer_doc_no, quantity, amount, discount, member_discount1, member_discount2, co_promo_discount, coupon_discount, special_discount, other_discount, net_amt, vat, ex_vat_amt, ex_vat_net, coupon_cash, coupon_cash_qty, point1, point2, redeem_point, total_point, co_promo_code, coupon_code, special_promo_code, other_promo_code, application_id, new_member_st, birthday_card_st, not_cn_st, paid, pay_cash, pay_credit, pay_cash_coupon, credit_no, credit_tp, bank_tp, `change`, pos_id, computer_no, user_id, cashier_id, saleman_id, cn_status, refer_cn_net, name, address1, address2, address3, doc_remark, create_date, create_time, cancel_id, cancel_date, cancel_time, cancel_tp, cancel_remark, cancel_auth, keyin_st, keyin_tp, keyin_remark, post_id, post_date, post_time, transfer_ts, transfer_date, transfer_time, order_id, order_no, order_date, order_time, acc_name, bank_acc, bank_name, tel1, tel2, dn_name, dn_address1, dn_address2, dn_address3, remark1, remark2, deleted, print_no, reg_date, reg_time, reg_user, upd_date, upd_time, upd_user,time_up,idcard,special_day,mobile_no,point_begin) values('$corporation_id','$company_id','$branch_id','$doc_date','$doc_time','$doc_no','$doc_tp','$status_no','$flag','$member_id','$member_percent','$special_percent','$refer_member_id','$refer_doc_no','$quantity','$amount','$discount','$member_discount1','$member_discount2','$co_promo_discount','$coupon_discount','$special_discount','$other_discount','$net_amt','$vat','$ex_vat_amt','$ex_vat_net','$coupon_cash','$coupon_cash_qty','$point1','$point2','$redeem_point','$total_point','$co_promo_code','$coupon_code','$special_promo_code','$other_promo_code','$application_id','$new_member_st','$birthday_card_st','$not_cn_st','$paid','$pay_cash','$pay_credit','$pay_cash_coupon','$credit_no','$credit_tp','$bank_tp','$change','$pos_id','$computer_no','$user_id','$cashier_id','$saleman_id','$cn_status','$refer_cn_net','$name','$address1','$address2','$address3','$doc_remark','$create_date','$create_time','$cancel_id','$cancel_date','$cancel_time','$cancel_tp','$cancel_remark','$cancel_auth','$keyin_st','$keyin_tp','$keyin_remark','$post_id','$post_date','$post_time','$transfer_ts','$transfer_date','$transfer_time','$order_id','$order_no','$order_date','$order_time','$acc_name','$bank_acc','$bank_name','$tel1','$tel2','$dn_name','$dn_address1','$dn_address2','$dn_address3','$remark1','$remark2','$deleted','$print_no','$reg_date','$reg_time','$reg_user','$upd_date','$upd_time','$upd_user',now(),'$idcard','$special_day','$mobile_no','$point_begin')
	";
	$result_insert=mysql_db_query($ji_db_sevice,$insert,$conn_ji);
	//echo $insert;
	if($result_insert){
		echo "$i $doc_date $doc_no Insert Complete<br>";
	}else{
		echo "$i . $doc_date $doc_no Insert None Complete<br>";
	}

}

if($rows==0){
	return false;
}

$addBill="

insert into trn_diary1(`corporation_id`, `company_id`, `branch_id`, `doc_date`, `doc_time`, `doc_no`, `doc_tp`, `status_no`, `flag`, `member_id`, `member_percent`, `special_percent`, `refer_member_id`, `refer_doc_no`, `quantity`, `amount`, `discount`, `member_discount1`, `member_discount2`, `co_promo_discount`, `coupon_discount`, `special_discount`, `other_discount`, `net_amt`, `vat`, `ex_vat_amt`, `ex_vat_net`, `coupon_cash`, `coupon_cash_qty`, `point1`, `point2`, `redeem_point`, `total_point`, `co_promo_code`, `coupon_code`, `special_promo_code`, `other_promo_code`, `application_id`, `new_member_st`, `birthday_card_st`, `not_cn_st`, `paid`, `pay_cash`, `pay_credit`, `pay_cash_coupon`, `credit_no`, `credit_tp`, `bank_tp`, `change`, `pos_id`, `computer_no`, `user_id`, `cashier_id`, `saleman_id`, `cn_status`, `refer_cn_net`, `name`, `address1`, `address2`, `address3`, `doc_remark`, `create_date`, `create_time`, `cancel_id`, `cancel_date`, `cancel_time`, `cancel_tp`, `cancel_remark`, `cancel_auth`, `keyin_st`, `keyin_tp`, `keyin_remark`, `post_id`, `post_date`, `post_time`, `transfer_ts`, `transfer_date`, `transfer_time`, `order_id`, `order_no`, `order_date`, `order_time`, `acc_name`, `bank_acc`, `bank_name`, `tel1`, `tel2`, `dn_name`, `dn_address1`, `dn_address2`, `dn_address3`, `remark1`, `remark2`, `deleted`, `print_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`, `time_up`,idcard,special_day,mobile_no,point_begin)
SELECT 
a.corporation_id,a.company_id,a.branch_id,a.doc_date,a.doc_time,a.doc_no,a.doc_tp,a.status_no,a.flag,a.member_id,a.member_percent,a.special_percent,a.refer_member_id,a.refer_doc_no,a.quantity,a.amount,a.discount,a.member_discount1,a.member_discount2,a.co_promo_discount,a.coupon_discount,a.special_discount,a.other_discount,a.net_amt,a.vat,a.ex_vat_amt,a.ex_vat_net,a.coupon_cash,a.coupon_cash_qty,a.point1,a.point2,a.redeem_point,a.total_point,a.co_promo_code,a.coupon_code,a.special_promo_code,a.other_promo_code,a.application_id,a.new_member_st,a.birthday_card_st,a.not_cn_st,a.paid,a.pay_cash,a.pay_credit,a.pay_cash_coupon,a.credit_no,a.credit_tp,a.bank_tp,a.change,a.pos_id,a.computer_no,a.user_id,a.cashier_id,a.saleman_id,a.cn_status,a.refer_cn_net,a.name,a.address1,a.address2,a.address3,a.doc_remark,a.create_date,a.create_time,a.cancel_id,a.cancel_date,a.cancel_time,a.cancel_tp,a.cancel_remark,a.cancel_auth,a.keyin_st,a.keyin_tp,a.keyin_remark,a.post_id,a.post_date,a.post_time,a.transfer_ts,a.transfer_date,a.transfer_time,a.order_id,a.order_no,a.order_date,a.order_time,a.acc_name,a.bank_acc,a.bank_name,a.tel1,a.tel2,a.dn_name,a.dn_address1,a.dn_address2,a.dn_address3,a.remark1,a.remark2,a.deleted,a.print_no,a.reg_date,a.reg_time,a.reg_user,a.upd_date,a.upd_time,a.upd_user,now(),a.idcard,a.special_day,a.mobile_no,a.point_begin
FROM trn_diary1_auto_tmp as a left join trn_diary1 as b
on a.doc_no=b.doc_no
where
a.branch_id='$shop' and
b.doc_no is null
";
$run_addBill=mysql_db_query($ji_db_sevice,$addBill,$conn_ji);
echo "$addBill<br>";

?>