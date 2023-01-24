<?php 
class Model_Product{
	//-------------------------------------------------------------------
	function productprofile($product_id,$corporation_id,$barcode,$name_product,$table_name,$query,$qtype,$page,$rp,$sortname,$sortorder)
	{
		if (!$sortname) $sortname = 'product_id';
		if (!$sortorder) $sortorder = 'desc';
		$sort = " ORDER BY $sortname $sortorder ";
		if (!$page) $page = 1;
		if (!$rp) $rp = 10;
		$start = (($page-1) * $rp);
		$limit = "LIMIT $start, $rp";
		
		$where = " WHERE 1  ";
		if($query) $where= " WHERE  $qtype  LIKE '$query' ";
		
		if($product_id) $where.= " AND  product_id  LIKE '$product_id' ";
		if($corporation_id) $where.= " AND  corporation_id  LIKE '$corporation_id' ";
		if($name_product) $where.= " AND  name_product  LIKE '$name_product%' ";
		
		$sql = "SELECT * FROM $table_name  $where ";
		$sql.=" $sort $limit ";
		//echo $sql;
		//exit();

		$objConf=new Model_Mydbpos();
		$objConf->checkDbOnline('conf',$table_name);
		$res=$objConf->fetchAllrows($sql);
		
		$total = count($res);
		$data['page'] = $page;
		$data['total'] = $total;
		$rows=array();
		foreach($res as $result){
			 $id=$result['id'];
			 $corporation_id=$result['corporation_id'];
			 $company_id=$result['company_id'];
			 $product_id=$result['product_id'];
			 $barcode=$result['barcode'];
			 $name_product=$result['name_product'];
			 
			 $name_product = iconv('TIS-620', 'UTF-8',$name_product);
			
			 $price=$result['price'];
			 $picture=$result['picture'];
			$bt="<input type='button'   iconCls='icon-search' name='Button' onclick='productdetail($product_id)' value='Open' />";
	 		$rows[] = array(
				"id" => $id,
				"cell" => array(
						$corporation_id
						,$product_id
						,$barcode
						,$price
						,$name_product
						,$bt
				)
			);
	    }
		$data['rows'] = $rows;
		echo json_encode($data);                                                      
	}
	//-------------------------------------------------------------------
	function productdetail($product_id){
		
	$sql="SELECT * FROM com_product_master WHERE  product_id='$product_id' ";
	$objConf=new Model_Mydbpos();
	$res=$objConf->fetchAllrows($sql);
		
		
	foreach($res as $result){
			 $id=$result['id'];
			 $corporation_id=$result['corporation_id'];
			 $company_id=$result['company_id'];
			 $product_id=$result['product_id'];
			 $barcode=$result['barcode'];
			 $name_product=$result['name_product'];
			 $price=$result['price'];
			 $picture=$result['picture'];
			 $name_print=$result['name_print'];
			 $vendor_id=$result['vendor_id'];
			 $unit=$result['unit'];
			 $group=$result['group'];
			 $type=$result['type'];
			 $tax_type=$result['tax_type'];
			 $picture=$result['picture'];
			 $product_set=$result['product_set'];
	
			return $conten="
			<table width='671' height='240' border='1' align='center'>
			  
			  <tr>
			    <td colspan='2' rowspan='2' valign='top'>
			    <div align='center'>
			    <img src='/pos/images/ricons/ContactsALT.png' width='59' height='60' />    </div>    </td>
			    <td width='7'>&nbsp;</td>
			    <td width='189'>รหัสช่องทางจำหน่าย</td>
			    <td width='19'>&nbsp;</td>
			    <td width='218'>$corporation_id</td>
			  </tr>
			  
			  <tr>
			    <td>&nbsp;</td>
			    <td>รหัสผู้ขาย</td>
			    <td>&nbsp;</td>
			    <td>$vendor_id</td>
			  </tr>
			  <tr>
			    <td width='81'>&nbsp;</td>
			    <td width='117'>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>ชื่อสินค้า</td>
			    <td>&nbsp;</td>
			    <td>$name_product</td>
			  </tr>
			  <tr>
			    <td>Barcode : </td>
			    <td>$barcode</td>
			    <td>&nbsp;</td>
			    <td>ชื่อสินค้าที่ใช้พิมพ์บิล</td>
			    <td>&nbsp;</td>
			    <td>$name_print</td>
			  </tr>
			  <tr>
			    <td>รหัสสินค้า:</td>
			    <td>$product_id</td>
			    <td>&nbsp;</td>
			    <td>ราคาขาย</td>
			    <td>&nbsp;</td>
			    <td>$price</td>
			  </tr>
			  <tr>
			    <td>หน่วยนับ:</td>
			    <td>$unit</td>
			    <td>&nbsp;</td>
			    <td>กลุ่มสินค้า</td>
			    <td>&nbsp;</td>
			    <td>$group</td>
			  </tr>
			  <tr>
			    <td>ชุดสินค้า</td>
			    <td>$product_set</td>
			    <td>&nbsp;</td>
			    <td>ประเภทสินค้า</td>
			    <td>&nbsp;</td>
			    <td>$type</td>
			  </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>ประเภทของภาษี</td>
			    <td>&nbsp;</td>
			    <td>$tax_type</td>
			  </tr>
</table>
		";
	}
	}
	//-------------------------------------------------------------------
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}
?>