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
  `corporation_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'รหัสบริษัท',
  `company_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'รหัสช่องทางจำหน่าย',
  `branch_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'รหัสสาขา',
  `doc_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่เปิดบิล',
  `doc_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่เปิดบิล',
  `doc_no` varchar(25) NOT NULL DEFAULT '' COMMENT 'เลขที่เอกสาร',
  `doc_tp` varchar(2) NOT NULL DEFAULT '' COMMENT 'ประเภทเอกสาร',
  `status_no` varchar(2) NOT NULL DEFAULT '' COMMENT 'สถานะเอกสาร',
  `flag` varchar(2) NOT NULL DEFAULT '' COMMENT 'สถานะยกเลิกเอกสาร',
  `seq` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'ลำดับรายการ',
  `seq_set` int(11) NOT NULL,
  `promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสโปรโมชั่น',
  `promo_seq` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'ลำดับรายการโปรโมชั่น',
  `promo_pos` varchar(10) NOT NULL DEFAULT '' COMMENT 'การเล่นโปรโมชั่น',
  `promo_st` varchar(10) NOT NULL DEFAULT '' COMMENT 'สถานะโปรโมชั่น',
  `promo_tp` varchar(10) NOT NULL DEFAULT '' COMMENT 'ประเภทโปรโมชั่น',
  `product_id` varchar(15) NOT NULL DEFAULT '' COMMENT 'รหัสสินค้า',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ราคาต่อหน่วย',
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวน',
  `stock_st` int(2) NOT NULL DEFAULT '0' COMMENT 'สถานะสินค้า',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงิน',
  `discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลด',
  `member_discount1` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดสมาชิกที่ 1',
  `member_discount2` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดสมาชิกที่ 2',
  `co_promo_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดร่วมกับโปรโมชั่น',
  `coupon_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดคูปอง',
  `special_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดพิเศษ',
  `other_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดอื่นๆ',
  `net_amt` double(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินสุทธิ',
  `cost` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ต้นทุนต่อหน่วย',
  `cost_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ต้นทุน',
  `promo_qty` int(6) NOT NULL DEFAULT '0' COMMENT 'จำนวนโปรโมชั่น',
  `weight` decimal(3,0) NOT NULL DEFAULT '0' COMMENT 'ตัวคูณจำนวนสินค้า',
  `exclude_promo` varchar(2) NOT NULL DEFAULT '' COMMENT 'ไม่ร่วมโปรโมชั่นอื่นๆ',
  `location_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'พื้นที่จัดเก็บ',
  `product_status` varchar(10) NOT NULL DEFAULT '' COMMENT 'สถานะสินค้า',
  `get_point` varchar(2) NOT NULL DEFAULT '' COMMENT 'คำนวณคะแนนสมาชิก',
  `discount_member` char(1) NOT NULL DEFAULT '' COMMENT 'คำนวณส่วนลดสมาชิก',
  `cal_amt` char(1) NOT NULL DEFAULT '' COMMENT 'คำนวณส่วนลด',
  `tax_type` char(1) NOT NULL DEFAULT '' COMMENT 'ประเภทภาษี',
  `gp` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลด Corner',
  `point1` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'คะแนน',
  `point2` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'คะแนนพิเศษ',
  `discount_percent` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ส่วนลด',
  `member_percent1` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ส่วนลดสมาชิก 1',
  `member_percent2` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ส่วนลดสมาชิก 2',
  `co_promo_percent` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '% ส่วนลดโปรโมชั่นที่ร่วมรายการ',
  `co_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'โปรโมชั่นที่ร่วมรายการ',
  `coupon_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสคูปอง',
  `coupon_percent` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดคูปอง',
  `not_return` varchar(2) NOT NULL DEFAULT '' COMMENT 'ห้ามคืนสินค้า',
  `lot_no` varchar(20) NOT NULL DEFAULT '' COMMENT 'หมายเลขการผลิต',
  `lot_expire` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันหมดอายุ',
  `lot_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่ผลิต',
  `short_qty` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวนสินค้าขาด',
  `short_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินสินค้าขาด',
  `ret_short_qty` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวนคืนสินค้าขาด',
  `ret_short_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินคืนสินค้าขาด',
  `cn_qty` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวนลดหนี้',
  `cn_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินลดหนี้',
  `cn_tp` varchar(10) NOT NULL DEFAULT '' COMMENT 'ประเภทลดหนี้',
  `cn_remark` varchar(50) NOT NULL DEFAULT '' COMMENT 'หมายเหตุการลดหนี้',
  `deleted` varchar(10) NOT NULL DEFAULT '' COMMENT 'ไม่ระบุ',
  `reg_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่สร้าง',
  `reg_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่สร้าง',
  `reg_user` varchar(10) NOT NULL DEFAULT '' COMMENT 'ผู้สร้าง',
  `upd_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่แก้ไข',
  `upd_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่แก้ไข',
  `upd_user` varchar(10) NOT NULL DEFAULT '' COMMENT 'ผู้แก้ไข',
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























