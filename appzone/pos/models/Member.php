<?php 
class Model_Member{
//-----------------------------------------------------------------------------
	function tablemember($query,$qtype,$page,$rp,$sortname,$sortorder){

		$objConf=new Model_Mydbpos();
		if (!$sortname) $sortname = 'crm_profile.id';
		if (!$sortorder) $sortorder = 'desc';
		$sort = " ORDER BY $sortname $sortorder ";
		
		
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;
		$start = (($page-1) * $rp);
		$limit = "LIMIT $start, $rp";

		$where = " WHERE 1  ";
		if($qtype=="name"){$where.= " AND  crm_profile.$qtype  LIKE '$query%' OR crm_profile.surname  LIKE '$query%'  ";}
		if($qtype=="surname"){$where.= " AND  crm_profile.$qtype  LIKE '$query%' OR crm_profile.name  LIKE '$query%'  ";}
		if($qtype=="ename"){$where.= " AND  crm_profile.$qtype  LIKE '$query%' ";}
		if($qtype=="esurname"){$where.= " AND  crm_profile.$qtype  LIKE '$query%' ";}
		if($qtype=="member_no"){$where.= " AND  crm_card.$qtype  LIKE '$query%' ";}
		
		$sql = "SELECT crm_profile.id,crm_profile.birthday,crm_profile.customer_id,crm_profile.corporation_id, crm_profile.company_id, crm_profile.title, crm_profile.name, crm_profile.surname, crm_profile.mobile_no, crm_card.member_no, crm_card.apply_date, crm_card.expire_date, crm_card.cardtype_id, crm_card.apply_shop, crm_card.apply_promo, crm_card.special_day FROM crm_profile LEFT JOIN crm_card ON crm_profile.customer_id = crm_card.customer_id $where ";
		$sql.=" $sort $limit ";
		
		//echo $sql;
		//exit();
	
		$objConf->checkDbOnline('conf','crm_profile');
		
		$res=$objConf->fetchAllrows($sql);
		$total = count($res);
		$data['page'] = $page;
		$data['total'] = $total;
		$rows=array();
		$customer_id_old="";
		foreach($res as $result){
			 $id=$result['id'];
			 $name=$result['name'];
			 $surname=$result['surname'];
			 $birthday=$result['birthday'];
			 $mobile_no=$result['mobile_no'];
			 $customer_id=$result['customer_id'];
			
			 $title=$result['title'];
			 $member_no=$result['member_no'];
			 $apply_date=$result['apply_date'];
			 $expire_date=$result['expire_date'];
			 $cardtype_id=$result['cardtype_id'];
			 $apply_shop=$result['apply_shop'];
			 $apply_promo=$result['apply_promo'];
			 $special_day=$result['special_day'];
			 
			 if($customer_id==$customer_id_old){
			 	$rows[] = array(
					"id" => $id,
					"cell" => array(
							""
							,""
							,""
							,"$member_no"
							,"$apply_date"
							,"$expire_date"
							,"$cardtype_id"
							,"$apply_shop"
							,"$apply_promo"
							//,"$special_day" 
					)
				 );
			 	
			 }else{
			 	 	$customer_id_old=$customer_id;
			 		$rows[] = array(
					"id" => $id,
					"cell" => array(
							" $title $name $surname"
							,"$birthday"
							,"$mobile_no"
							,"$member_no"
							,"$apply_date"
							,"$expire_date"
							,"$cardtype_id"
							,"$apply_shop"
							,"$apply_promo"
							,"$special_day" 
					)
				 );
			 }

			
	    }
		$data['rows'] = $rows;
		echo json_encode($data);    
	}
//-----------------------------------------------------------------------------		

	function getdatamember($id){
		$rows=array();
		if(!empty($menu_id)){
			$sql=" SELECT * FROM com_menu WHERE menu_id='$menu_id' ";
			$objConf=new Model_Mydbpos();
			$res=$objConf->fetchAllrows($sql);
		    foreach($res as $data){
				$menu_id = $data['menu_id'];
				$menu_name = $data['menu_name'];
				$menu_seq = $data['menu_seq'];
		    	$menu_exec = $data['menu_exec'];
		    	$menu_picture = $data['menu_picture'];
		    	if($menu_picture==""){
		    		$menu_picture="/pos/images/ricons/CalendarAlt.png";
		    	}
			    $menu_level = $data['menu_level'];
				$menu_ref = $data['menu_ref'];
				$status_menu = $data['status_menu'];
				$type_menu = $data['type_menu'];
				
		    	$rows=array(
		    	"menu_id"=>$menu_id,
		    	"menu_name"=>$menu_name,
		    	"menu_seq"=>$menu_seq,
		    	"menu_exec"=>$menu_exec,
		    	"menu_picture"=>$menu_picture,
		    	"menu_level"=>$menu_level,
		    	"menu_ref"=>$menu_ref,
		    	"status_menu"=>$status_menu,
		    	"type_menu"=>$type_menu);
		    }
		}
		return json_encode($rows);
	}
	
//-----------------------------------------------------------------------------		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>