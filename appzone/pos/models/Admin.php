<?php 
class Model_Admin{
//-----------------------------------------------------------------------------
function get_company(){
	    $sql="SELECT * FROM  com_company WHERE  1 ";
	    $objConf=new Model_Mydbpos();
		$res=$objConf->fetchAllrows($sql);
		
	/*
		$content="<table border='1'>";
		foreach($res as $arr){
			$corporation_id=$arr['corporation_id'];
			$company_id=$arr['company_id'];
			$company_name=$arr['company_name'];
			$logo=$arr['logo'];

		    $content.="
				<tr valign='bottom'>
			    <td><img src='$logo' /></td>
			    <td>$company_name</td>
			 	</tr>
			 ";
		}
		$content.="</table>";
		*/
		$content=" <div align='center'>";
		$i=0;
		foreach($res as $arr){
			$i++;
			$corporation_id=$arr['corporation_id'];
			$company_id=$arr['company_id'];
			$company_name=$arr['company_name'];
			$logo=$arr['logo'];

		    $content.="<br><input name='Button$i' type='button' id='Button$i' value='$company_id' /><br/>";
		}
		$content.="</div>";
		return $content;
}

//-----------------------------------------------------------------------------

}
?>