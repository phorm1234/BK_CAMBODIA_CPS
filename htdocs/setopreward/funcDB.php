<?php 
include("connectDB.php"); 

//---------------------------------------------------------------------------------------------------------------------- Manage
function select_user_form_userpass($user,$pass)
{
	$sql = "select * from conf_employee where user_id = '$user' and password_id = '$pass'";
	$result = mysql_query($sql , $GLOBALS["condb_pro"]);
	if(mysql_num_rows($result) > 0)
	{
		return mysql_fetch_array($result);
	}
	else
	{
		return "norecord";
	}
}

function select_user_from_uid($uid)
{
	$sql = "select * from conf_employee where user_id='$uid'";
	$result = mysql_query($sql , $GLOBALS["condb_pro"]);
	if(mysql_num_rows($result) > 0)
	{
		return mysql_fetch_array($result);
	}
	else
	{
		return "norecord";
	}
}

function add_prog($program_name,$program_path)
{
	$sql = "INSERT INTO `mng_program` (`program_name`, `program_path`) VALUES ('$program_name', '$program_path')";
	return mysql_query($sql , $GLOBALS["condb_mng"]);
}

function select_all_program()
{
	$sql = "SELECT * FROM `mng_program` ORDER BY program_name ASC";
	return mysql_query($sql , $GLOBALS["condb_mng"]);
}

function select_program_from_name($prog_name)
{
	$sql = "SELECT * FROM `mng_program` WHERE `program_name` LIKE '%$prog_name%'";
	$result = mysql_query($sql , $GLOBALS["condb_mng"]);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		return $row["program_name"]."-next-".$row["program_path"]."-next-".$row["program_id"];
	}
	else
	{
		return "norecord";
	}
}

function del_program($prog_id)
{
	$sql = "DELETE FROM `mng_program` WHERE `program_id` = '$prog_id'";
	return mysql_query($sql , $GLOBALS["condb_mng"]);
}

function add_permission($program_id,$company_id,$dept_id)
{
	$sql = "INSERT INTO `mng_permission` (`program_id`, `company_id`, `dept_id`) VALUES ('$program_id', '$company_id', '$dept_id')";
	return mysql_query($sql , $GLOBALS["condb_mng"]);
}

//------------------------------------------------------------------------------------------------------------------- Reward
function add_rwd_head($corporation_id,$company_id,$promo_code,$promo_des,$promo_tp,$start_date,$end_date,$point,$status_no,$reg_date,$reg_time,$reg_user,$upd_date,$upd_time,$upd_user)
{
	$sql = "INSERT INTO `promo_point3_head` (`corporation_id`, `company_id`, `promo_code`, `promo_des`, `promo_tp`, `start_date`, `end_date`, `point`, `status_no`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`,`quantity`) VALUES ('$corporation_id', '$company_id', '$promo_code', '$promo_des', '$promo_tp', '$start_date', '$end_date', '$point', '$status_no' , '$reg_date', '$reg_time', '$reg_user', '$upd_date', '$upd_time', '$upd_user','1')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}

function add_rwd_detail($corporation_id,$company_id,$promo_code,$product_id, $start_date, $end_date, $amount, $reg_date, $reg_time, $reg_user, $upd_date, $upd_time, $upd_user)
{
	$sql = "INSERT INTO `promo_point3_detail` (`corporation_id`, `company_id`, `promo_code`, `product_id`, `start_date`, `end_date`, `amount`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corporation_id', '$company_id', '$promo_code', '$product_id', '$start_date', '$end_date', '$amount', '$reg_date', '$reg_time', '$reg_user', '$upd_date', '$upd_time', '$upd_user')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}

function add_rwd_branch($corporation_id, $company_id, $promo_code, $branch_id, $start_date, $end_date, $reg_date, $reg_time, $reg_user, $upd_date, $upd_time, $upd_user)
{
	$sql = "INSERT INTO `promo_branch` (`corporation_id`, `company_id`, `promo_code`, `branch_id`, `start_date`, `end_date`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corporation_id', '$company_id', '$promo_code', '$branch_id', '$start_date', '$end_date', '$reg_date', '$reg_time', '$reg_user', '$upd_date', '$upd_time', '$upd_user')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}

function add_rwd_opreward($promo_code,$promo_des,$product_id,$point,$start_date, $end_date)
{
	$sql = "INSERT INTO `com_opreward` (`code`, `desc`, `product_id`, `point`, `start_date`, `end_date`) VALUES ('$promo_code', '$promo_des', '$product_id', '$point', '$start_date', '$end_date')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
function select_reward_from_rwd_id($rwd_id)
{
	$sql = "SELECT * FROM `promo_point3_head` h, promo_point3_detail d WHERE h.promo_code = d.promo_code AND h.promo_code = '$rwd_id'";
	$result = mysql_query($sql , $GLOBALS["condb_pro"]);
	
	$sql_rw = "SELECT * FROM `com_opreward` WHERE code = '$rwd_id'";
	$result_rw = mysql_query($sql_rw , $GLOBALS["condb_pro"]);
	
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$row_rw = mysql_fetch_array($result_rw);
		$txt_rw_sdate = explode("-",$row["start_date"]);
		$txt_rw_sdate = $txt_rw_sdate["2"]."/".$txt_rw_sdate["1"]."/".$txt_rw_sdate["0"];
		
		$txt_rw_edate = explode("-",$row["end_date"]);
		$txt_rw_edate = $txt_rw_edate["2"]."/".$txt_rw_edate["1"]."/".$txt_rw_edate["0"];
		
		$txt_rw_usdate = explode("-",$row_rw["start_date"]);
		$txt_rw_usdate = $txt_rw_usdate["2"]."/".$txt_rw_usdate["1"]."/".$txt_rw_usdate["0"];
		
		$txt_rw_uedate = explode("-",$row_rw["end_date"]);
		$txt_rw_uedate = $txt_rw_uedate["2"]."/".$txt_rw_uedate["1"]."/".$txt_rw_uedate["0"];
		
		$row["amount"] = number_format($row["amount"], 2, '.', '');
		
		return $row["promo_code"]."-next-".$row["product_id"]."-next-".$row["promo_des"]."-next-".$row["point"]."-next-".$row["amount"]."-next-".$txt_rw_sdate."-next-".$txt_rw_edate."-next-".$txt_rw_usdate."-next-".$txt_rw_uedate;
	}
	else
	{
		return "norecord";
	}
}
function delete_rwd_from_pro_id($promo_id)
{
	$sql = "DELETE FROM `promo_point3_head` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
	
	$sql = "DELETE FROM `promo_point3_detail` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
	
	$sql = "DELETE FROM `promo_branch` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
	
	$sql = "DELETE FROM `com_opreward` WHERE `code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
}

function edit_rwd($promo_code,$promo_des,$start_date,$end_date,$point,$upd_date,$upd_time,$upd_user,$product_id,$amount)
{
	$sql = "UPDATE `promo_point3_head` SET `promo_des` = '$promo_des', `start_date` = '$start_date', `end_date` = '$end_date', `point` = '$point', `upd_date` = '$upd_date', `upd_time` = '$upd_time', `upd_user` = '$upd_user' WHERE `promo_code` = '$promo_code'";
	$head = mysql_query($sql , $GLOBALS["condb_pro"]);
	if($head)
	{
		$sql = "UPDATE `promo_point3_detail` SET `product_id` = '$product_id', `start_date` = '$start_date', `end_date` = '$end_date', `amount` = '$amount', `upd_date` = '$upd_date', `upd_time` = '$upd_time', `upd_user` = '$upd_user' WHERE `promo_code` = '$promo_code'";
		$detail = mysql_query($sql , $GLOBALS["condb_pro"]);
		if($detail)
		{
			$sql = "UPDATE `com_opreward` SET `desc` = '$promo_des', `product_id` = '$product_id', `point` = '$point', `start_date` = '$start_date', `end_date` = '$end_date' WHERE `code` = '$promo_code'";
			$rwd = mysql_query($sql , $GLOBALS["condb_pro"]);
		}	
	}
	return $rwd;
}

//----------------------------------------------------------------------------------------------------------------------- Activity
function add_avt_head($corporation_id,$company_id,$promo_code,$promo_des,$start_date,$end_date,$point,$amount,$reg_date,$reg_time,$reg_user,$upd_date,$upd_time,$upd_user)
{
	$sql = "INSERT INTO `promo_point2_head` (`corporation_id`, `company_id`, `promo_code`, `promo_des`, `start_date`, `end_date`, `point`, `amount`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corporation_id', '$company_id', '$promo_code', '$promo_des', '$start_date', '$end_date', '$point', '$amount', '$reg_date', '$reg_time', '$reg_user', '$upd_date', '$upd_time', '$upd_user')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}

function add_avt_detail($corporation_id,$company_id,$promo_code,$line,$promo_des,$start_date,$end_date,$reg_date,$reg_time,$reg_user,$upd_date,$upd_time,$upd_user)
{
	$sql = "INSERT INTO `promo_point2_detail` (`corporation_id`, `company_id`, `promo_code`, `line`, `promo_des`, `start_date`, `end_date`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corporation_id', '$company_id', '$promo_code', '$line', '$promo_des', '$start_date', '$end_date', '$reg_date', '$reg_time', '$reg_user', '$upd_date', '$upd_time', '$upd_user')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}

function select_activity_from_pro_id($promo_id)
{
	$sql = "SELECT * FROM `promo_point2_head` WHERE promo_code = '$promo_id'";
	$result = mysql_query($sql , $GLOBALS["condb_pro"]);
	if(mysql_num_rows($result) > 0)
	{
		$txt_return = "";
		$row = mysql_fetch_array($result);
		$sdate = explode("-",$row["start_date"]);
		$sdate = $sdate["2"]."/".$sdate["1"]."/".$sdate["0"];
		
		$edate = explode("-",$row["end_date"]);
		$edate = $edate["2"]."/".$edate["1"]."/".$edate["0"];
		
		$txt_return .= $row["promo_code"]."-next-".$row["promo_des"]."-next-".$row["point"]."-next-".$row["amount"]."-next-".$sdate."-next-".$edate;
		
		//------
		$txt_return .= "-detail-";
		$sql = "SELECT * FROM `promo_point2_detail` WHERE promo_code = '$promo_id' ORDER BY line ASC";
		$result = mysql_query($sql , $GLOBALS["condb_pro"]);
		while($row = mysql_fetch_array($result))
		{
			$txt_return .= $row["promo_des"]."-next-";
		}
		
		$txt_return = substr($txt_return, 0, -6);
		return $txt_return;
		
	}
	else
	{
		return "norecord";
	}
}

function delete_avt_from_pro_id($promo_id)
{
	$sql = "DELETE FROM `promo_point2_head` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
	
	$sql = "DELETE FROM `promo_point2_detail` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
	
	$sql = "DELETE FROM `promo_branch` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
}

function delete_detail($promo_id)
{
	$sql = "DELETE FROM `promo_point2_detail` WHERE `promo_code` = '$promo_id'";
	mysql_query($sql , $GLOBALS["condb_pro"]);
}

function edit_avt_head($promo_code,$promo_des_h,$start_date,$end_date,$point,$amount,$upd_date,$upd_time,$upd_user)
{
	$sql = "UPDATE `promo_point2_head` SET `promo_des` = '$promo_des_h', `start_date` = '$start_date', `end_date` = '$end_date', `point` = '$point', `amount` = '$amount', `upd_date` = '$upd_date', `upd_time` = '$upd_time', `upd_user` = '$upd_user' WHERE `promo_code` = '$promo_code'";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
function select_start_end_time($promo_code)
{
	$sql = "SELECT * FROM `promo_point2_detail` WHERE `promo_code` = '$promo_code'";
	$result = mysql_query($sql , $GLOBALS["condb_pro"]);
	return mysql_fetch_array($result);
	
	/*$sql = "UPDATE `promo_point2_detail` SET `line` = '$line', `promo_des` = '$promo_des', `start_date` = '$start_date', `end_date` = '$end_date', `upd_date` = '$upd_date', `upd_time` = '$upd_time', `upd_user` = '$upd_user' WHERE `promo_code` = '$promo_code'";
	return mysql_query($sql , $GLOBALS["condb_pro"]);*/
}

//------------------------------------------------------------------------------------------------------------------------ bkoff
function select_product_from_id($pid)
{
	$sql = "SELECT * FROM `com_product` WHERE `product_id` = '$pid'";
	$result = mysql_query($sql , $GLOBALS["condb_bk"]);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		return $row["name_eng"];
	}
	else
	{
		return "norecord";
	}
}

//------------------------------------------------------------------------------------------------------------------------ pos
function select_all_doc_no()
{
	$sql = "SELECT * FROM `com_doc_no`";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
function select_all_doc_status_from_doc_tp($doc_tp)
{
	$sql = "SELECT * FROM `com_doc_status` where doc_tp = '$doc_tp'";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
//------------------------------------------------------------------------------------------------------------------------- prod lock
function add_product_lock($corporation_id,$company_id,$product_id,$doc_tp,$start_date,$end_date,$reg_date,$reg_time,$reg_user,$upd_date,$upd_time,$upd_user)
{
	$sql = "INSERT INTO `promotion`.`com_product_lock` (`corporation_id`, `company_id`, `product_id`, `doc_tp`, `start_date`, `end_date`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES ('$corporation_id', '$company_id', '$product_id', '$doc_tp', '$start_date', '$end_date', '$reg_date', '$reg_time', '$reg_user', '$upd_date', '$upd_time', '$upd_user')";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
function select_product_lock_from_prod_id($product_id)
{
	$sql = "SELECT * FROM `com_product_lock` WHERE product_id = '$product_id'";
	$result = mysql_query($sql , $GLOBALS["condb_pro"]);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$sdate = explode("-",$row["start_date"]);
		$sdate = $sdate["2"]."/".$sdate["1"]."/".$sdate["0"];
		
		$edate = explode("-",$row["end_date"]);
		$edate = $edate["2"]."/".$edate["1"]."/".$edate["0"];
		
		return $row["product_id"]."-next-".$row["doc_tp"]."-next-".$sdate."-next-".$edate."-next-".$row["upd_user"];
	}
	else
	{
		return "norecord";
	}
}
function delete_product_lock($product_id)
{
	$sql = "DELETE FROM `com_product_lock` WHERE `product_id` = '$product_id'";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
function edit_product_lock($start_date,$end_date,$product_id)
{
	$sql = "UPDATE `com_product_lock` SET `start_date` = '$start_date', `end_date` = '$end_date' WHERE `product_id` = '$product_id'";
	return mysql_query($sql , $GLOBALS["condb_pro"]);
}
?>