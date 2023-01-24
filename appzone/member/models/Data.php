<?php
class Model_Data
{
	public function __construct(){
		$this->db=Zend_Registry::get('dbOfline'); 
		
	}
	public function registerList($page,$qtype,$query,$rp,$sortname,$sortorder){
		/**
		* @desc
		* @param Integer $page
		* @param String $qtype :field to search
		* @param String $query :key word to search
		* @param Integer $rp :row per page
		* @param String $sortname :field to sort order
		* @param String $sortorder : sort by DESC or ASC
		* @return
		*/
		
		if (!$sortname) $sortname = 'id';
		if (!$sortorder) $sortorder = 'desc';
		$sort = "ORDER BY $sortname $sortorder";
		$search = ($qtype != '' && $query != '') ? " AND $qtype = '$query'" : '';
		$opt = $query ==0?"AND cp.name='' AND cp.surname ='' ":"";
					
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;
			
		$start = (($page-1) * $rp);
		$limit = "LIMIT $start, $rp";
		
		
		/*$strSQL = "SELECT id,doc_no,doc_date,member_id,reg_user,status_no 
		           FROM trn_diary1 WHERE status_no = '01' AND flag !='C' ".$sort." ".$limit;*/
		$strSQL = "SELECT td.id,td.doc_no,td.member_id,td.reg_user,
					DATE_ADD(td.doc_date,INTERVAL 543 YEAR) as doc_date,
					IF(crn.flag_save=1,'บัททึกแล้ว','ยังไม่บันทึก') as status_no,crn.flag_save
					FROM trn_diary1  td
					INNER JOIN com_register_new_card crn ON td.member_id = crn.member_id
					INNER JOIN crm_card cc ON cc.member_no = td.member_id
					INNER JOIN crm_profile cp ON cp.customer_id = cc.customer_id
					WHERE 1 ".$opt." 						  
						  AND  td.status_no = '01' AND td.flag !='C' " .$search." " .$sort." ".$limit;
		//echo $strSQL;
		$arr = $this->db->fetchAll($strSQL);
		
		$strSQL = "SELECT count(id) as allrec  FROM trn_diary1 WHERE status_no = '01' AND flag !='C' ";
		$rs = $this->db->fetchAll($strSQL); //Find All Record
		
		$data = array(
						"page"=> $page,
						"total"=>$rs[0]['allrec'],
						"rows"=>array()
					);
		

		$i=0;
		foreach($arr as $val){
			$i++;
			$row = array(
							"id" => $val['member_id'],
							"absid" => $val['id'],
							"cell" => array(
										$i,
										$val['member_id'],
										$val['doc_no'],
										$val['doc_date'],										
										$val['reg_user'],
										$val['status_no']
									)
						);
						
			array_push($data['rows'], $row);
			
		}
		
		return $data;
		
	}
	function registerDetail($id){
		$strSQL = "SELECT td.id, td.branch_id, td.doc_no, 
						DATE_FORMAT( td.doc_date, '%d/%m/%Y' ) AS doc_date, 
						td.member_id, td.reg_user, td.status_no, cp.id_card,
						 DATE_FORMAT(LAST_DAY(DATE_ADD(td.doc_date,INTERVAL 2 YEAR)),'%d/%m/%Y') AS expire_date,
						cp.title,cp.name,cp.surname,cp.sex,
						cp.birthday,cp.nationality_id,cp.h_address,
						cp.h_village,cp.h_soi,cp.h_road,cp.h_district,
						cp.h_amphur,cp.h_province,cp.h_postcode,
						cp.h_tel_no,cp.o_tel_no,cp.mobile_no,cp.email,zip_province_nm_th,zip_amphur_nm_th,zip_tambon_nm_th
					FROM trn_diary1 td
					INNER JOIN com_register_new_card crn ON td.member_id = crn.member_id
					INNER JOIN crm_card cc ON cc.member_no = td.member_id
					INNER JOIN crm_profile cp ON cp.customer_id = cc.customer_id
					LEFT JOIN com_province_th cph ON cp.h_province = cph.province_id
					LEFT JOIN com_amphur_th cat ON cp.h_amphur=cat.zip_amphur_id
					LEFT JOIN com_tambon_th ctt ON cp.h_district = ctt.zip_tambon_id
					WHERE 1
					AND td.status_no = '01'
					AND td.flag != 'C'
					AND td.id='".$id."'";
		/*$strSQL = "SELECT id,branch_id,doc_no,
				   DATE_FORMAT(doc_date,'%d/%m/%Y') AS doc_date,
				   member_id,reg_user,status_no,
				   DATE_FORMAT(LAST_DAY(DATE_ADD(doc_date,INTERVAL 2 YEAR)),'%d/%m/%Y') AS expire_date
		           FROM trn_diary1 WHERE status_no = '01' AND flag !='C' AND id='".$id."'";*/
		return $this->db->fetchAll($strSQL);
	}
	function getProvince(){
		$strSQL = "SELECT zip_province_id,province_id,zip_province_nm_th FROM com_province_th ORDER BY zip_province_nm_th";
		$arr = $this->db->fetchAll($strSQL);
		$province = array();
		foreach ($arr as $val){
			array_push($province, 
							array(
								"zip"	=> 	$val['zip_province_id'],
								"id"		=> 	$val['province_id'],
								"name"=>	$val['zip_province_nm_th']
							)
						);
			
		}
		return $province;
	}
	function getAmphur($zip){
		$strSQL = "SELECT zip_amphur_id,zip_amphur_nm_th,zip_province_id FROM com_amphur_th WHERE zip_province_id = '".$zip."' ORDER BY zip_amphur_nm_th";
		$arr = $this->db->fetchAll($strSQL);
		$amphur = array();
		foreach ($arr as $val){
			array_push($amphur, 
							array(
								"zip"	=> 	$val['zip_amphur_id'],
								"id"		=> 	$val['zip_amphur_id'],
								"name"=>	$val['zip_amphur_nm_th']
							)
						);
			
		}
		return $amphur;
	}
	function getTumbol($zip){
		$strSQL = "SELECT zip_tambon_id,zip_tambon_nm_th,zip_amphur_id,zipcode FROM com_tambon_th WHERE zip_amphur_id = '".$zip."'";
		$arr = $this->db->fetchAll($strSQL);
		$tumbol = array();
		foreach ($arr as $val){
			array_push($tumbol, 
							array(
								"zip"	=> 	$val['zipcode'],
								"id"		=> 	$val['zip_tambon_id'],
								"name"=>	$val['zip_tambon_nm_th']
							)
						);
			
		}
		return $tumbol;
	}
	function setRegisterDetails($member_id,$id_card,$title,$EMP,$name,
								$surename,$sex,$birthday,$nationality_id,$h_address,
								$h_village,$h_soi,$h_road,$h_district,$h_amphur,
								$h_province,$h_postcode,$h_tel_no,$o_tel_no,$mobile_no,$email){
		$error = 1;							
		$strSQL = "UPDATE crm_profile  cp
					INNER JOIN crm_card cc
						ON cp.customer_id = cc.customer_id
					INNER JOIN com_register_new_card crn
						ON cc.member_no = crn.member_id
					SET 
						cp.id_card = '".$id_card."',
						cp.title = 'คุณ',
						cp.reg_date =CURDATE(),
						cp.reg_time =CURTIME(),
						cp.reg_user = '".$EMP."',
						cp.name = '".$name."',
						cp.surname = '".$surename."',
						cp.sex = '".$sex."',
						cp.birthday = '".$birthday."',
						cp.nationality_id = '".$nationality_id."',
						cp.h_address='".$h_address."',
						cp.h_village='".$h_village."',
						cp.h_soi='".$h_soi."',
						cp.h_road='".$h_road."',
						cp.h_district='".$h_district."',
						cp.h_amphur='".$h_amphur."',
						cp.h_province='".$h_province."',
						cp.h_postcode='".$h_postcode."',
						cp.h_tel_no='".$h_tel_no."',
						cp.o_tel_no='".$o_tel_no."',
						cp.mobile_no='".$mobile_no."',
						cp.email='".$email."',
						crn.flag_save = '1'
					WHERE  crn.member_id = '".$member_id."'";
		$obj = $this->db->query($strSQL);
		if($obj){
			$error = 0;
			//JI-NET UPDATE
			
		}
		return $error;		
		
	}
}
?>