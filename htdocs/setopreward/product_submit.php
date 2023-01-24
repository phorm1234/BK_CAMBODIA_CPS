<?php 
	session_start(); 
	require("funcDB.php"); 
	date_default_timezone_set('Asia/Bangkok');
	
	$date = date("Y-m-d");
	$time = date("H:i:s");
	
	$txt_p_prod = $_POST["txt_p_prod"];
	$txt_p_sdate = $_POST["txt_p_sdate"];
	$txt_p_edate = $_POST["txt_p_edate"];
	
	$txt_p_sdate = explode("/",$txt_p_sdate);
	$txt_p_sdate = $txt_p_sdate["2"]."-".$txt_p_sdate["1"]."-".$txt_p_sdate["0"];
	
	$txt_p_edate = explode("/",$txt_p_edate);
	$txt_p_edate = $txt_p_edate["2"]."-".$txt_p_edate["1"]."-".$txt_p_edate["0"];
	
	$check = select_product_lock_from_prod_id($txt_p_prod);
	if($check != "norecord")
	{
		$lock = edit_product_lock($txt_p_sdate,$txt_p_edate,$txt_p_prod);
	}
	else
	{
		$slt_doc_no = $_POST["slt_doc_no"];
		$slt_doc_status = $_POST["slt_doc_status"];
		$lock = add_product_lock("OP","OP",$txt_p_prod,$slt_doc_no,$txt_p_sdate,$txt_p_edate,$date,$time,$_SESSION["u_id"],$date,$time,$slt_doc_status);
	}
	
	
	
if($lock)
{
?>
	<script>
		alert("บันทึกข้อมูลเรียบร้อย");
		window.location = "product.php";
	</script>
<?php
}
else
{
?>
	<script>
		alert("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");
		window.location = "product.php";
	</script>
<?php
}
?>