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
  `corporation_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'รหัสบริษัท',
  `company_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'รหัสช่องทางจำหน่าย',
  `branch_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'รหัสสาขา',
  `doc_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่เปิดบิล',
  `doc_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่เปิดบิล',
  `doc_no` varchar(25) NOT NULL DEFAULT '' COMMENT 'เลขที่เอกสาร',
  `doc_tp` varchar(10) NOT NULL DEFAULT '' COMMENT 'ประเภทเอกสาร',
  `status_no` varchar(2) NOT NULL DEFAULT '' COMMENT 'สถานะเอกสาร',
  `flag` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะยกเลิกเอกสาร',
  `member_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสสมาชิก',
  `member_percent` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '% ส่วนลดสมาชิก',
  `special_percent` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '% ส่วนลดพิเศษ',
  `refer_member_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสสมาชิกอ้างอิง',
  `refer_doc_no` varchar(25) NOT NULL DEFAULT '' COMMENT 'เอกสารอ้างอิง',
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวน',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงิน',
  `discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลด',
  `member_discount1` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวนเงินส่วนลดที่ 1 ของสมาชิก ',
  `member_discount2` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'จำนวนเงินส่วนลดที่ 2 ของสมาชิก ',
  `co_promo_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดโปรโมชั่นที่ร่วมรายการ',
  `coupon_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดคูปอง',
  `special_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดพิเศษ',
  `other_discount` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ส่วนลดอื่นๆ',
  `net_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินสุทธิ',
  `vat` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ภาษีมูลค่าเพิ่ม',
  `ex_vat_amt` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินที่ยกเว้นภาษี',
  `ex_vat_net` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดสุทธิที่ยกเว้นภาษี',
  `coupon_cash` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดเงินคูปอง',
  `coupon_cash_qty` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'จำนวนคูปอง',
  `point1` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'คะแนน',
  `point2` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'คะแนนพิเศษ',
  `redeem_point` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'คะแนนที่ใช้สิทธิ',
  `total_point` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'คะแนนรวม',
  `co_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'โปรโมชั่นที่ร่วมรายการ',
  `coupon_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสคูปอง',
  `special_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสโปรโมชั่นพิเศษ',
  `other_promo_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสโปรโมชั่นอื่นๆ',
  `application_id` varchar(15) NOT NULL DEFAULT '' COMMENT 'รหัสชุดสมัครสมาชิก',
  `new_member_st` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะสมัครสมาชิกใหม่',
  `birthday_card_st` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะการใช้สิทธิวันเกิด',
  `not_cn_st` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะบิลห้ามคืนสินค้า',
  `paid` varchar(10) NOT NULL DEFAULT '' COMMENT 'ประเภทการชำระเงิน',
  `pay_cash` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดชำระเงินสด',
  `pay_credit` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดชำระบัตรเครดิต',
  `pay_cash_coupon` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดชำระคูปอง',
  `credit_no` varchar(20) NOT NULL DEFAULT '' COMMENT 'หมายเลขบัตรเครดิต',
  `credit_tp` varchar(20) NOT NULL DEFAULT '' COMMENT 'ประเภทบัตรเครดิต',
  `bank_tp` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสธนาคาร',
  `change` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'เงินทอน',
  `pos_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'หมายเลขเครื่อง POS',
  `computer_no` varchar(2) NOT NULL DEFAULT '0' COMMENT 'ลำดับเครื่อง POS',
  `user_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'User เข้าระบบ',
  `cashier_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสผู้รับเงิน',
  `saleman_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสผู้ขาย',
  `cn_status` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะลดหนี้',
  `refer_cn_net` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'ยอดสุทธิที่อ้างลดหนี้',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT 'ชื่อ - นามสกุล',
  `address1` varchar(50) NOT NULL DEFAULT '' COMMENT 'ที่อยู่ 1',
  `address2` varchar(50) NOT NULL DEFAULT '' COMMENT 'ที่อยู่ 2',
  `address3` varchar(50) NOT NULL DEFAULT '' COMMENT 'ที่อยู่ 3',
  `doc_remark` varchar(100) NOT NULL DEFAULT '' COMMENT 'หมายเหตุเอกสาร',
  `create_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่สร้างเอกสาร',
  `create_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่สร้างเอกสาร',
  `cancel_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสผู้ยกเลิกเอกสาร',
  `cancel_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่ยกเลิกเอกสาร',
  `cancel_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่ยกเลิกเอกสาร',
  `cancel_tp` varchar(10) NOT NULL DEFAULT '' COMMENT 'ประเภทการยกเลิกเอกสาร',
  `cancel_remark` varchar(50) NOT NULL DEFAULT '' COMMENT 'หมายเหตุการยกเลิกเอกสาร',
  `cancel_auth` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสผู้อนุมัติการยกเลิกเอกสาร',
  `keyin_st` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะการคีย์รหัสสมาชิก',
  `keyin_tp` varchar(10) NOT NULL DEFAULT '' COMMENT 'ประเภทการคีย์รหัสสมาชิก',
  `keyin_remark` varchar(50) NOT NULL DEFAULT '' COMMENT 'หมายเหตุการคีย์รหัสสมาชิก',
  `post_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสผู้ปิดบิลประจำวัน',
  `post_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่ปิดบิลประจำวัน',
  `post_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่ปิดบิลประจำวัน',
  `transfer_ts` char(1) NOT NULL DEFAULT '' COMMENT 'สถานะการโอนข้อมูล',
  `transfer_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่โอนข้อมูล',
  `transfer_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่โอนข้อมูล',
  `order_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'รหัสผู้สั่งสินค้า',
  `order_no` varchar(25) NOT NULL DEFAULT '' COMMENT 'เลขที่ใบสั่งสินค้า',
  `order_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่สั่งสินค้า',
  `order_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่สั่งสินค้า',
  `acc_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'ชื่อบัญชีลูกค้าที่ลดหนี้',
  `bank_acc` varchar(50) NOT NULL DEFAULT '' COMMENT 'หมายเลขบัญชีลูกค้าที่ลดหนี้',
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'ชื่อธนาคาร',
  `tel1` varchar(20) NOT NULL DEFAULT '' COMMENT 'เบอร์โทรศัพท์ 1',
  `tel2` varchar(20) NOT NULL DEFAULT '' COMMENT 'เบอร์โทรศัพท์ 2',
  `dn_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'ชื่อผู้รับสินค้า',
  `dn_address1` varchar(50) NOT NULL DEFAULT '' COMMENT 'ที่อยู่ส่งสินค้า 1',
  `dn_address2` varchar(50) NOT NULL DEFAULT '' COMMENT 'ที่อยู่ส่งสินค้า 2',
  `dn_address3` varchar(50) NOT NULL DEFAULT '' COMMENT 'ที่อยู่ส่งสินค้า 3',
  `remark1` varchar(50) NOT NULL DEFAULT '' COMMENT 'หมายเหตุ 1',
  `remark2` varchar(50) NOT NULL DEFAULT '' COMMENT 'หมายเหตุ 2',
  `deleted` varchar(10) NOT NULL DEFAULT '' COMMENT 'ไม่ระบุ',
  `print_no` decimal(4,0) NOT NULL DEFAULT '0' COMMENT 'จำนวนครั้งในการพิมพ์',
  `reg_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่สร้าง',
  `reg_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่สร้าง',
  `reg_user` varchar(10) NOT NULL DEFAULT '' COMMENT 'ผู้สร้าง',
  `upd_date` date NOT NULL DEFAULT '1900-01-01' COMMENT 'วันที่แก้ไข',
  `upd_time` time NOT NULL DEFAULT '00:00:00' COMMENT 'เวลาที่แก้ไข',
  `upd_user` varchar(10) NOT NULL DEFAULT '' COMMENT 'ผู้แก้ไข',
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























