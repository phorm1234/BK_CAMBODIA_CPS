<?php
ini_set('display_errors', '1');
set_time_limit(0);
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

$DROP1="DROP TABLE IF EXISTS `trn_diary2` ";
$run0=mysql_query($DROP1,$conn);
if($run0){
         print("DROP TABLE trn_diary2  successfully   \n");
}
$sql_00="
CREATE TABLE IF NOT EXISTS `trn_diary2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corporation_id` varchar(10) NOT NULL DEFAULT '' COMMENT '���ʺ���ѷ',
  `company_id` varchar(10) NOT NULL DEFAULT '' COMMENT '���ʪ�ͧ�ҧ��˹���',
  `branch_id` varchar(10) NOT NULL DEFAULT '' COMMENT '�����Ң�',
  `doc_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ����Դ���',
  `doc_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ���Դ���',
  `doc_no` varchar(25) NOT NULL DEFAULT '' COMMENT '�Ţ����͡���',
  `doc_tp` varchar(2) NOT NULL DEFAULT '' COMMENT '�������͡���',
  `status_no` varchar(2) NOT NULL DEFAULT '' COMMENT 'ʶҹ��͡���',
  `flag` varchar(2) NOT NULL DEFAULT '' COMMENT 'ʶҹ�¡��ԡ�͡���',
  `seq` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '�ӴѺ��¡��',
  `seq_set` int(11) NOT NULL,
  `promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT '�����������',
  `promo_seq` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '�ӴѺ��¡���������',
  `promo_pos` varchar(10) NOT NULL DEFAULT '' COMMENT '�������������',
  `promo_st` varchar(10) NOT NULL DEFAULT '' COMMENT 'ʶҹ��������',
  `promo_tp` varchar(10) NOT NULL DEFAULT '' COMMENT '�������������',
  `product_id` varchar(15) NOT NULL DEFAULT '' COMMENT '�����Թ���',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�Ҥҵ��˹���',
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹ',
  `stock_st` int(2) NOT NULL DEFAULT '0' COMMENT 'ʶҹ��Թ���',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ',
  `discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ',
  `member_discount1` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ��Ҫԡ��� 1',
  `member_discount2` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ��Ҫԡ��� 2',
  `co_promo_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ�����Ѻ�������',
  `coupon_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ�ٻͧ',
  `special_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ�����',
  `other_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ����',
  `net_amt` double(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ�ط��',
  `cost` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�鹷ع���˹���',
  `cost_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�鹷ع',
  `promo_qty` int(6) NOT NULL DEFAULT '0' COMMENT '�ӹǹ�������',
  `weight` decimal(3,0) NOT NULL DEFAULT '0' COMMENT '��Ǥٳ�ӹǹ�Թ���',
  `exclude_promo` varchar(2) NOT NULL DEFAULT '' COMMENT '������������������',
  `location_id` varchar(10) NOT NULL DEFAULT '' COMMENT '��鹷��Ѵ��',
  `product_status` varchar(10) NOT NULL DEFAULT '' COMMENT 'ʶҹ��Թ���',
  `get_point` varchar(2) NOT NULL DEFAULT '' COMMENT '�ӹǳ��ṹ��Ҫԡ',
  `discount_member` char(1) NOT NULL DEFAULT '' COMMENT '�ӹǳ��ǹŴ��Ҫԡ',
  `cal_amt` char(1) NOT NULL DEFAULT '' COMMENT '�ӹǳ��ǹŴ',
  `tax_type` char(1) NOT NULL DEFAULT '' COMMENT '����������',
  `gp` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ Corner',
  `point1` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '��ṹ',
  `point2` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '��ṹ�����',
  `discount_percent` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ��ǹŴ',
  `member_percent1` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ��ǹŴ��Ҫԡ 1',
  `member_percent2` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ��ǹŴ��Ҫԡ 2',
  `co_promo_percent` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ��ǹŴ������蹷��������¡��',
  `co_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT '������蹷��������¡��',
  `coupon_code` varchar(20) NOT NULL DEFAULT '' COMMENT '���ʤٻͧ',
  `coupon_percent` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '��ǹŴ�ٻͧ',
  `not_return` varchar(2) NOT NULL DEFAULT '' COMMENT '�����׹�Թ���',
  `lot_no` varchar(20) NOT NULL DEFAULT '' COMMENT '�����Ţ��ü�Ե',
  `lot_expire` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ�������',
  `lot_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ����Ե',
  `short_qty` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹ�Թ��ҢҴ',
  `short_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ�Թ��ҢҴ',
  `ret_short_qty` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹ�׹�Թ��ҢҴ',
  `ret_short_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�Թ�׹�Թ��ҢҴ',
  `cn_qty` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ӹǹŴ˹��',
  `cn_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '�ʹ�ԹŴ˹��',
  `cn_tp` varchar(10) NOT NULL DEFAULT '' COMMENT '������Ŵ˹��',
  `cn_remark` varchar(50) NOT NULL DEFAULT '' COMMENT '�����˵ء��Ŵ˹��',
  `deleted` varchar(10) NOT NULL DEFAULT '' COMMENT '����к�',
  `reg_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ������ҧ',
  `reg_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ�����ҧ',
  `reg_user` varchar(10) NOT NULL DEFAULT '' COMMENT '������ҧ',
  `upd_date` date NOT NULL DEFAULT '1900-01-01' COMMENT '�ѹ������',
  `upd_time` time NOT NULL DEFAULT '00:00:00' COMMENT '���ҷ�����',
  `upd_user` varchar(10) NOT NULL DEFAULT '' COMMENT '������',
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_no_2` (`doc_no`,`seq`),
  KEY `doc_date` (`doc_date`),
  KEY `doc_no` (`doc_no`),
  KEY `promo_seq` (`promo_seq`),
  KEY `promo_code` (`promo_code`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
$run0=mysql_query($sql_00,$conn);
if($run0){
         print("CREATE TABLE  trn_diary2  Successfully   \n");
}else{
         print("CREATE TABLE  trn_diary2  Error   \n");
}

$sql_2="
INSERT INTO trn_diary2 ( `corporation_id`,
`doc_no`,
`doc_date`,
`doc_tp`,
`flag`,
`company_id`,
`branch_id`,
`seq`,
`promo_code`,
`product_id`,
`price`,
`quantity`,
`amount`,
`member_percent1`,
`member_discount1`,
`discount`,
`net_amt`,
`tax_type`, 
`discount_member`,
`gp`,
`lot_no`,
`lot_expire`,
`status_no`,
`stock_st`,
`reg_date`,`reg_time`,`reg_user`) 
SELECT 
'OP',
DOC_NO,
DOC_DT,
DOC_TP,
FLAG,
BRAND,
SHOP,
SEQ,
PROMO_CODE,
PRODUCT,
PRICE,
QUANTITY,
AMOUNT,	
MEMBER_CRD,
DIS_CARD,
DISCOUNT,
NET_AMT,	
NO_VAT,
EXCLUDE,
GP,
LOT_NO,
LOT_EXPIRE,
STATUS,
CASE DOC_TP 
WHEN 'AI' THEN '1'
WHEN 'AO' THEN '-1'
WHEN 'CK' THEN '0'
WHEN 'CN' THEN '1'
WHEN 'DN' THEN '-1'
WHEN 'DO' THEN '-1'
WHEN 'IQ' THEN '0'
WHEN 'ME' THEN '0'
WHEN 'RD' THEN '0'
WHEN 'RQ' THEN '0'
WHEN 'SL' THEN '-1'
WHEN 'TI' THEN '1'
WHEN 'TO' THEN '-1'
WHEN 'VT' THEN '-1'
END AS stock_st,
CURDATE(),CURTIME(),'005949'
FROM 
transfer_to_branch.DIARY2 
WHERE
transfer_to_branch.DIARY2.DOC_DT>=DATE_SUB(NOW(),INTERVAL 1 YEAR)
GROUP BY DOC_NO,SEQ
";
$run2=mysql_query($sql_2,$conn);

if($run2){
	 print("INSERT INTO trn_diary2 Successfully   \n");
}else{
	 print("INSERT INTO trn_diary2 ERROR  \n");
}
//--------------------------------------------------------------------------
system("touch /home/shopsetup/dbf_to_mysql/pook7.txt");

?>























