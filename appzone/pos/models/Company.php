<?php 
class Model_Company{
	//--------------------------------------------------------
	function  getdata($table_manq,$fild,$val,$orderby){
		if($table_manq=="")return;
		$sql=" SELECT * FROM $table_manq WHERE  1 ";
		if(!empty($fild)){$sql.="  AND  $fild='$val' ";}
		if(!empty($orderby)){$sql.="  ORDER BY `id` DESC ";}
		$objConf=new Model_Mydbpos();
    	return $objConf->fetchAllrows($sql);
	}
	
	//--------------------------------------------------------
	function  checkduplicate($country_id,$corporation_id,$company_id){
		if($country_id=="")return;
		$sql=" SELECT * FROM com_company WHERE  
		country_id='$country_id' AND
		corporation_id='$corporation_id' AND
		company_id='$company_id' ";
		$objConf=new Model_Mydbpos();
    	return $objConf->checkNumrows($sql);
	}
	//--------------------------------------------------------
	function  getorderRow($texsearch){
		//$sql=" set @num = 0;select `company_id`, @num := @num + 1 as row_number from com_company ";
		$sql="select * from com_company ORDER BY `id` DESC ";
		$objConf=new Model_Mydbpos();
    	$res=$objConf->fetchAllrows($sql);
    	$i=1;
    	foreach($res as $data){
    		$i++;
    		$company_id=$data['company_id'];
    		if($company_id==$texsearch){
    			return $i;
    		}
    	}
	}
	//--------------------------------------------------------
	function  delcompany($id){
		$sql="DELETE FROM `com_company` WHERE `id` = '$id' ";
		$objConf=new Model_Mydbpos();
		$objConf->checkDbOnline('com','com_company');
    	return $objConf->deldata($sql);
	}
	//--------------------------------------------------------
	function  insertcompany($rows){
			$objConf=new Model_Mydbpos();
			$objConf->checkDbOnline('com','com_company');
			return $objConf->insertdata('com_company',$rows);
	}
	//--------------------------------------------------------
	function dialogupdatecompany($res){
		$content="";
		foreach($res as $data){
			$country_id0=$data['country_id'];
			$id=$data['id'];
			$logo=$data['logo'];
			$corporation_id=$data['corporation_id'];
			$company_id=$data['company_id'];
			$company_name=$data['company_name'];
			$address =$data['address'];
			$road =$data['road'];
			$district =$data['district'];
			$amphur =$data['amphur'];
			$province =$data['province'];
			$postcode =$data['postcode'];
			$tel =$data['tel'];
			$fax =$data['fax'];
			$tax_id=$data['tax_id'];
			$active=$data['active'];
			$company_name_print_edit=$data['company_name_print'];
			if($active=="Y"){
				$option=" <option value='Y'>ปกติ</option>";
				$option.=" <option value='N'>ระงับ</option>";
			}else{
				$option=" <option value='N'>ระงับ</option>";
				$option.=" <option value='Y'>ปกติ</option>";
			}
			$active_list="<select name='active_edit' id='active_edit'>$option</select>";

			$objConf=new Model_Mydbpos();
			$sql0=" SELECT * FROM com_country WHERE 1";
	    	$com_country=$objConf->fetchAllrows($sql0);
		
			$com_country_list="<select name='country_id_edit' id='country_id_edit' onchange='search_province_edit(this.value)'>";
			$com_country_list.="<option value='$country_id0'>$country_id0</option>";
			$i=0;
			foreach($com_country as $com_province){
				$i++;
				$country_id=$com_province['country_id'];
				if($country_id==$country_id0){
					$table_province=$com_province['table_province'];
					$table_amphur=$com_province['table_amphur'];
					$table_tambon=$com_province['table_tambon'];
					continue;
				}
				$country_name=$com_province['country_name'];
				if($i==1){
					 $com_country_list.="<option value='$country_id' selected='selected' >$country_name</option>";
				}else{
					$com_country_list.="<option value='$country_id' >$country_name</option>";
				}
				
			}
			$com_country_list.="</select>";
			
			$sql1=" SELECT * FROM $table_province WHERE zip_province_id='$province'";
	    	$res1=$objConf->fetchAllrows($sql1);
	    	
	    	foreach($res1 as $resprovince1){
	    			 $zip_province_id1=$resprovince1['zip_province_id'];
	    			$zip_province_nm_th1=$resprovince1['zip_province_nm_th'];
	    	}
	    	
			$com_province_list="<select name='zip_province_id_edit' id='zip_province_id_edit' onchange=\"search_amphur_edit(this.value,'$table_amphur')\">";
			$com_province_list.="<option value='$zip_province_id1'>$zip_province_nm_th1</option>";
			$sql2=" SELECT * FROM $table_province WHERE 1";
		    $resprovince2=$objConf->fetchAllrows($sql2);
			foreach($resprovince2 as $province){
				$zip_province_id=$province['zip_province_id'];
				$zip_province_nm_th=$province['zip_province_nm_th'];
				if($zip_province_id==$zip_province_id1){
					continue;
				}
				$com_province_list.="<option value='$zip_province_id'>$zip_province_nm_th</option>";
			}
			$com_province_list.="</select>";

			$sql3=" SELECT * FROM $table_amphur WHERE zip_amphur_id='$amphur' ";
	    	$resa=$objConf->fetchAllrows($sql3);
			    	
	    	foreach($resa as $resamphur){
	    		$zip_amphur_nm_th0=$resamphur['zip_amphur_nm_th'];
	    	}
	    
			$com_amphur_list="<select name='zip_amphur_id_edit' id='zip_amphur_id_edit' onchange=\"search_tambon_edit(this.value,'$table_tambon')\">";
			$com_amphur_list.="<option value='$amphur'>$zip_amphur_nm_th0</option>";
			$com_amphur_list.="</select>";
		
			$sql4=" SELECT * FROM $table_tambon WHERE zip_tambon_id='$district'";
	    	$restambon=$objConf->fetchAllrows($sql4);
	    	
	    	
		   	foreach($restambon as $restambon1){
	    		$zip_tambon_nm_th=$restambon1['zip_tambon_nm_th'];
	    	}
			$com_tambon_list="<select name='zip_tambon_id_edit' id='zip_tambon_id_edit' onchange=\"search_zipcode_edit(this.value,'$table_tambon')\">";
			$com_tambon_list.="<option value='$district'>$zip_tambon_nm_th</option>";
			$com_tambon_list.="</select>";
			
			$content.="
			<form action='/pos/admin/updatecompany' method='post' enctype='multipart/form-data' name='formupdatcompany' id='formupdatcompany'>
				<fieldset>
					<legend class='ui-widget ui-widget-header ui-corner-all'></legend>
									<div align='left'  >
			  <table width='100%' border='0' align='center'>
			    <tr>
			      <td width='37%'><div align='right'>รหัสประเทศ<input name='id_edit' type='hidden' id='id_edit' value='$id' /></div></td>
			      <td width='63%'>$com_country_list</td>
		        </tr>
				  <tr>
			      <td><div align='right'>จังหวัด</div></td>
			      <td><div id='div_province_edit'>$com_province_list</div></td>
		        </tr>
		     	 <tr>
			      <td><div align='right'>เขต/อำเภอ</div></td>
			      <td><div id='div_amphur_edit'>$com_amphur_list</div>      </td>
		        </tr>   
		        
		       <tr>
			      <td><div align='right'>แขวง/ตำบล</div></td>
			      <td><div id='div_tambon_edit'>$com_tambon_list</div></td>
		        </tr>
			    <tr>
			      <td><div align='right'>รหัสไปรษณีย์</div></td>
			      <td><input  name='postcode_edit' type='text' id='postcode_edit' value='$postcode' size='10' /></td>
		        </tr>
			    <tr>
			      <td><div align='right'>รหัสบริษัท</div></td>
			      <td><input   name='corporation_id_edit' type='text' id='corporation_id_edit' value='$corporation_id' size='10' /></td>
		        </tr>
			    <tr>
			      <td><div align='right'>รหัสช่องทางจำหน่าย</div></td>
			      <td><input   name='company_id_edit' type='text' id='company_id_edit' value='$company_id' size='10' /></td>
		        </tr>
		        
			    <tr>
			      <td><div align='right'>ชื่อบริษัท</div></td>
			      <td><input   name='company_name_edit' type='text' id='company_name_edit' value='$company_name' size='30' /></td>
		        </tr>
		        <tr valign='top'>
			      <td><div align='right'>ชื่อบริษัทสำหรับพิมพ์</div></td>
			      <td><input   name='company_name_print_edit' type='text' id='company_name_print_edit' value='$company_name_print_edit' size='30' /></td>
	            </tr>
			    <tr valign='top'>
			      <td><div align='right'>ที่อยู่</div></td>
			      <td><textarea   name='address_edit' cols='15' id='address_edit'>$address</textarea></td>
		        </tr>
			    <tr>
			      <td><div align='right'>ถนน</div></td>
			      <td><input   name='road_edit' type='text' id='road_edit' value='$road' size='15' /></td>
		        </tr>
			    <tr>
			      <td><div align='right'>เบอร์โทรศัพท์</div></td>
			      <td><input   name='tel_edit' type='text' id='tel_edit' value='$tel' size='10' /></td>
		        </tr>
			    <tr>
			      <td><div align='right'>เบอร์โทรสาร</div></td>
			      <td><input   name='fax_edit' type='text' id='fax_edit' value='$fax' size='10' /></td>
		        </tr>
			    <tr>
			      <td><div align='right'>เลขประจำตัวผู้เสียภาษี</div></td>
			      <td><input   name='tax_id_edit' type='text' id='tax_id_edit' value='$tax_id' size='15' /></td>
		        </tr>
			    <tr>
			      <td><div align='right'>สถานะการใช้งาน</div></td>
			      <td>$active_list</td>
		        </tr>
			    <tr>
			      <td><div align='right'>logo</div></td>
			      <td><input   name='logo_edit' type='file' id='logo_edit' size='15' /></td>
		        </tr>
			    <tr>
			      <td>&nbsp;</td>
			      <td>
			        <input type='submit'   name='Submit' value='บันทึก' onclick=\"return updatecompany('$company_id')\" />
			      </td>
		        </tr>
			    <tr>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
		        </tr>
			  </table>
			</div>
			</fieldset>
			</form>
		";
		 
		} 	
		return $content;
	}
//--------------------------------------------------------
}	
	
?>