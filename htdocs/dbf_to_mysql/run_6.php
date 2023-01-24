<?php
ini_set('display_errors', '1');
set_time_limit(0);
header("Content-type:text/html; charset=tis-620");  
//--------------------------------------------------------------------------
$arr_config=array();
$PATH_FILE_INI="/var/www/pos/htdocs/.setuppos.wr";
if(!file_exists($PATH_FILE_INI)){
	echo "FILE NOT FOUND.";
	exit();
}
$file = fopen($PATH_FILE_INI, "r");
$i = 0;
while (!feof($file)) {
	$arr_config[] = fgets($file);
}
fclose($file);
$brand=trim($arr_config[0]);
$brand=strtoupper($brand);
$corporation_id=$brand;
$company_id=$brand;
$branch_id=trim($arr_config[1]);
//--------------------------------------------------------------------------
$db_uname = 'pos-ssup';
$db_passwd = 'P0z-$$up';
$conn = mysql_connect('localhost',$db_uname, $db_passwd);
mysql_query("SET NAMES UTF8");  
//--------------------------------------------------------------------------
$db2="pos_ssup";
mysql_select_db($db2);
$create_db2="CREATE DATABASE IF NOT EXISTS $db2 ";
mysql_query($create_db2,$conn);

$DROP1="DROP TABLE IF EXISTS `trn_diary1` ";
$run0=mysql_query($DROP1,$conn);
if($run0){
         print("DROP TABLE trn_diary1  Successfully   \n");
}else{
         print("DROP TABLE trn_diary1  Error   \n");
}
$sql_0="
CREATE TABLE IF NOT EXISTS `trn_diary1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corporation_id` varchar(10) NOT NULL DEFAULT '' COMMENT '���ʺ���ѷ',
  `company_id` varchar(10) NOT NULL DEFAULT '' COMMENT '���ʪ�ͧ�ҧ��˹���',
  `branch_id` varchar(10) NOT NULL DEFAULT '' COMMENT '�����Ң�',
  `doc_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ����Դ���',
  `doc_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ���Դ���',
  `doc_no` varchar(25) NOT NULL DEFAULT '' COMMENT '�Ţ����͡���',
  `doc_tp` varchar(10) NOT NULL DEFAULT '' COMMENT '�������͡���',
  `status_no` varchar(2) NOT NULL DEFAULT '' COMMENT 'ʶҹ��͡���',
  `flag` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹ�¡��ԡ�͡���',
  `member_id` varchar(20) NOT NULL DEFAULT '' COMMENT '������Ҫԡ',
  `member_percent` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '% ��ǹŴ��Ҫԡ',
  `special_percent` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '% ��ǹŴ�����',
  `refer_member_id` varchar(20) NOT NULL DEFAULT '' COMMENT '������Ҫԡ��ҧ�ԧ',
  `refer_doc_no` varchar(25) NOT NULL DEFAULT '' COMMENT '�͡�����ҧ�ԧ',
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹ',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ',
  `discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ',
  `member_discount1` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹ�Թ��ǹŴ��� 1 �ͧ��Ҫԡ ',
  `member_discount2` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹ�Թ��ǹŴ��� 2 �ͧ��Ҫԡ ',
  `co_promo_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ������蹷��������¡��',
  `coupon_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ�ٻͧ',
  `special_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ�����',
  `other_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ����',
  `net_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ�ط��',
  `vat` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '������Ť������',
  `ex_vat_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ���¡�������',
  `ex_vat_net` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�ط�Է��¡�������',
  `coupon_cash` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ�ٻͧ',
  `coupon_cash_qty` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '�ӹǹ�ٻͧ',
  `point1` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '��ṹ',
  `point2` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '��ṹ�����',
  `redeem_point` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '��ṹ������Է��',
  `total_point` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '��ṹ���',
  `co_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT '������蹷��������¡��',
  `coupon_code` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʤٻͧ',
  `special_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT '����������蹾����',
  `other_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT '���������������',
  `application_id` varchar(15) NOT NULL DEFAULT '' COMMENT '���ʪش��Ѥ���Ҫԡ',
  `new_member_st` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹ���Ѥ���Ҫԡ����',
  `birthday_card_st` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹС�����Է���ѹ�Դ',
  `not_cn_st` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹк�������׹�Թ���',
  `paid` varchar(10) NOT NULL DEFAULT '' COMMENT '��������ê����Թ',
  `pay_cash` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�����Թʴ',
  `pay_credit` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ���кѵ��ôԵ',
  `pay_cash_coupon` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ���Фٻͧ',
  `credit_no` varchar(20) NOT NULL DEFAULT '' COMMENT '�����Ţ�ѵ��ôԵ',
  `credit_tp` varchar(20) NOT NULL DEFAULT '' COMMENT '�������ѵ��ôԵ',
  `bank_tp` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʸ�Ҥ��',
  `change` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�Թ�͹',
  `pos_id` varchar(20) NOT NULL DEFAULT '' COMMENT '�����Ţ����ͧ POS',
  `computer_no` varchar(2) NOT NULL DEFAULT '0' COMMENT '�ӴѺ����ͧ POS',
  `user_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'User ����к�',
  `cashier_id` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʼ���Ѻ�Թ',
  `saleman_id` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʼ����',
  `cn_status` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹ�Ŵ˹��',
  `refer_cn_net` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�ط�Է����ҧŴ˹��',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '���� - ���ʡ��',
  `address1` varchar(50) NOT NULL DEFAULT '' COMMENT '������� 1',
  `address2` varchar(50) NOT NULL DEFAULT '' COMMENT '������� 2',
  `address3` varchar(50) NOT NULL DEFAULT '' COMMENT '������� 3',
  `doc_remark` varchar(100) NOT NULL DEFAULT '' COMMENT '�����˵��͡���',
  `create_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ������ҧ�͡���',
  `create_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ�����ҧ�͡���',
  `cancel_id` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʼ��¡��ԡ�͡���',
  `cancel_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ���¡��ԡ�͡���',
  `cancel_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ��¡��ԡ�͡���',
  `cancel_tp` varchar(10) NOT NULL DEFAULT '' COMMENT '���������¡��ԡ�͡���',
  `cancel_remark` varchar(50) NOT NULL DEFAULT '' COMMENT '�����˵ء��¡��ԡ�͡���',
  `cancel_auth` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʼ��͹��ѵԡ��¡��ԡ�͡���',
  `keyin_st` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹС�ä���������Ҫԡ',
  `keyin_tp` varchar(10) NOT NULL DEFAULT '' COMMENT '��������ä���������Ҫԡ',
  `keyin_remark` varchar(50) NOT NULL DEFAULT '' COMMENT '�����˵ء�ä���������Ҫԡ',
  `post_id` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʼ��Դ��Ż�Ш��ѹ',
  `post_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ���Դ��Ż�Ш��ѹ',
  `post_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ��Դ��Ż�Ш��ѹ',
  `transfer_ts` char(1) NOT NULL DEFAULT '' COMMENT 'ʶҹС���͹������',
  `transfer_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ����͹������',
  `transfer_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ���͹������',
  `order_id` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʼ������Թ���',
  `order_no` varchar(25) NOT NULL DEFAULT '' COMMENT '�Ţ��������Թ���',
  `order_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ�������Թ���',
  `order_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ������Թ���',
  `acc_name` varchar(50) NOT NULL DEFAULT '' COMMENT '���ͺѭ���١��ҷ��Ŵ˹��',
  `bank_acc` varchar(50) NOT NULL DEFAULT '' COMMENT '�����Ţ�ѭ���١��ҷ��Ŵ˹��',
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '���͸�Ҥ��',
  `tel1` varchar(20) NOT NULL DEFAULT '' COMMENT '�������Ѿ�� 1',
  `tel2` varchar(20) NOT NULL DEFAULT '' COMMENT '�������Ѿ�� 2',
  `dn_name` varchar(100) NOT NULL DEFAULT '' COMMENT '���ͼ���Ѻ�Թ���',
  `dn_address1` varchar(50) NOT NULL DEFAULT '' COMMENT '����������Թ��� 1',
  `dn_address2` varchar(50) NOT NULL DEFAULT '' COMMENT '����������Թ��� 2',
  `dn_address3` varchar(50) NOT NULL DEFAULT '' COMMENT '����������Թ��� 3',
  `remark1` varchar(50) NOT NULL DEFAULT '' COMMENT '�����˵� 1',
  `remark2` varchar(50) NOT NULL DEFAULT '' COMMENT '�����˵� 2',
  `deleted` varchar(10) NOT NULL DEFAULT '' COMMENT '����к�',
  `print_no` decimal(4,0) NOT NULL DEFAULT '0' COMMENT '�ӹǹ����㹡�þ����',
  `reg_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ������ҧ',
  `reg_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ�����ҧ',
  `reg_user` varchar(10) NOT NULL DEFAULT '' COMMENT '������ҧ',
  `upd_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ������',
  `upd_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ�����',
  `upd_user` varchar(10) NOT NULL DEFAULT '' COMMENT '������',
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_no` (`doc_no`),
  KEY `doc_no_2` (`doc_no`),
  KEY `doc_date` (`doc_date`),
  KEY `doc_tp` (`doc_tp`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
$run0=mysql_query($sql_0,$conn);
if($run0){
         print("CREATE TABLE trn_diary1  Successfully   \n");
}else{
         print("CREATE TABLE trn_diary1  Error   \n");
}
$sql_1="INSERT INTO trn_diary1 ( `corporation_id`,
`doc_no`,`doc_date`,`doc_time`,`doc_tp`,`flag`,`company_id`,`branch_id`,`member_id`,`co_promo_code`,`refer_doc_no`,`quantity`,
`amount`,`discount`,`coupon_discount`,`net_amt`,`vat`,`pay_cash`,`change`,`paid`,`saleman_id`,`print_no`,`create_time`,
`pos_id`,`new_member_st`,`cn_status`,`total_point`,`status_no`,
`name`,`address1`,`address2`,`address3`,`credit_no`,`doc_remark`,
`coupon_code`, 
`point1`,`point2`,`reg_date`,`reg_time`,`reg_user` 
) 
SELECT 
'$corporation_id',
a.DOC_NO,a.DOC_DT,a.TIME,a.DOC_TP,a.FLAG,a.BRAND,a.SHOP,a.MEMBER,a.CO_PROMO,a.REF_NO,a.QUANTITY,
a.AMOUNT,a.DISCOUNT,a.COUPON,a.NET_AMT,a.VAT,a.CASH,a.CHANGE,a.PAID,a.EMP_ID,a.PRINT,a.TIME,
a.MACHINE,a.NEW_MEMBER,a.CN_STATUS,a.USE_STAMP,a.STATUS,
b.NAME,SUBSTRING(b.ADDRESS,1,35),SUBSTRING(b.ADDRESS,36,35),SUBSTRING(b.ADDRESS,71,30),b.CREDIT,b.REMARK,
c.COUPON,
d.POINT1,d.POINT2,CURDATE(),CURTIME(),'005949'
FROM 
transfer_to_branch.DIARY1 as a 
LEFT JOIN transfer_to_branch.DIARY3 as b on a.DOC_NO=b.DOC_NO
LEFT JOIN transfer_to_branch.DIARY4 as c on a.DOC_NO=c.DOC_NO 
LEFT JOIN transfer_to_branch.DIARY5 as d on a.DOC_NO=d.DOC_NO
WHERE a.DOC_DT>=DATE_SUB(NOW(),INTERVAL 1 YEAR)
GROUP BY a.DOC_NO";
$run1=mysql_query($sql_1,$conn);
if($run1){
	 print("INSERT INTO trn_diary1 Successfully   \n");
}else{
	 print("INSERT INTO trn_diary1 ERROR  \n");
}
//--------------------------------------------------------------------------
system("touch /home/shopsetup/dbf_to_mysql/pook6.txt");
?>























