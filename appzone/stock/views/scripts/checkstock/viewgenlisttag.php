<form name="frm_print_list_tag" id="frm_print_list_tag">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
<?php
$i=0; 
if(!empty($shelf)){
	//asort($shelf);
	foreach($shelf as $val_shelf){
		if($i=='0'){echo"<tr>";}
		echo "
		<td>
			<table width='100%' border='0' cellspacing='2' cellpadding='2'>
				<tr>
					<td width='1%' bgcolor='#edf0f1' class='head_sub_shelf'><input type='checkbox' name='shelf_no[]' id='shelf_no[]' value='".$val_shelf['shelf_no']."' class='sel_checkbox'></td>
					<td class='head_sub_shelf'>".$val_shelf['shelf_no']."</td>
				</tr>
			</table>
		</td>";
		$i++;
		if($i=='7'){echo"</tr>"; $i='0';}
	}
}
?>
</table>
</form>