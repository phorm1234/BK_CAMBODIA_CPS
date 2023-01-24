<style type="text/css">   
    .div-table{display:table; width: 700px; border: 1px; }
    .div-table-row{display:table-row;}
 
    .div-table-col1{display:table-cell; padding-left:  10px; padding-right:  100px; width: 250px;}
    .div-table-col2{display:table-cell; padding-left:  50px; }
    .div-table-col3{display:table-cell; padding-left:  50px; float: right;}
	.div-table-col4{display:table-cell; padding-left:  10px; width: 600px;}
	.div-table-col5{display:table-cell; padding-left:  20px; width: 50px;}
    .div-table-col6{display:table-cell; padding-left:  20px; width: 100px; }
    .div-table-col7{display:table-cell; padding-left:  20px; width: 300px; }
	.div-table-col8{display:table-cell; padding-left:  40px; width: 100px;}
	.div-table-col9{display:table-cell; padding-left:  50px; width: 150px;}
    
</style>

<p align="center">
    <img src="/report/img/logo/op/op-logo.gif">
    <br><br>รายงานภาษีขาย<br>
    ประจำวันที่ <?php echo $this->s_date. " - " .$this->e_date ; ?></p>
    
<div class="div-table">
<div class="div-table-row">   
    <div class="div-table-col1">วันที่พิมพ์เอกสาร <?php echo $this->print_time ;?></div>
    
    <div class="div-table-col3">เวลา  <?php echo $this->ptime ;?></div>
    
</div>

<div class="div-table-row">
    <div class="div-table-col1">จุดขาย :  <?php echo $this->branch_id ." (".$this->branch_name.")" ;?></div>
    
    
</div>
</div>

<hr>

<div class="div-table">

	<div class="div-table-row">
          <div class="div-table-col5">#</div>
          <div class="div-table-col6">รหัสคูปอง</div>
          <div class="div-table-col7">รายละเอียด</div>
          <div class="div-table-col8">จำนวน</div>
          <div class="div-table-col9">จำนวนเงิน</div>
	</div>
</div>

<hr>

<div class="div-table">
	<?php $i=1;$b=0;$d=0; foreach($this->get_data as $v){	
		echo "<div class='div-table-row'>
					<div class='div-table-col5'>$i</div>
			        <div class='div-table-col6'>$v[co_promo_code]</div>
			        <div class='div-table-col7'></div>
			        <div class='div-table-col8'>".number_format($v[bill])."</div>
			        <div class='div-table-col9'>".number_format($v[dis],2)."</div>    
			  </div>";
		$i++;
		$b = $b + $v[bill] ;
		$d = $d + $v[dis] ;
	}?>
	
</div>

<hr>

<div class="div-table">

	<div class="div-table-row">
          <div class="div-table-col5">รวม</div>
          <div class='div-table-col6'><?php echo $i-1 ;?></div>
          <div class='div-table-col7'></div>
          <div class='div-table-col8'><?php echo number_format($b) ;?></div>
          <div class='div-table-col9'><?php echo number_format($d,2) ;?></div>
	</div>
</div>

<hr>

<?php 
	echo "<br><br><br>ลายเซ็นต์พนักงานขาย  :  __________________________________" ;
	
	$dayfrom =$this->dayfrom;
	$tofrom =$this->tofrom;
	$day = $this->get_doc_date;
	$today = date('Y-m-d');
	

	
	
	//if ($tofrom < $day ){
	    echo "<div align=right>
	<form action='/report/shop/printcoupon' method='post'>
	<input type=hidden id=start_date name=start_date value='$dayfrom'>
	<input type=hidden id=end_date name=end_date value='$tofrom'>
	<input type=submit value= พิมพ์รายงาน  id=print>
	</form>
	
	</div>" ;
	/*}  else {
	echo "";    
	}*/

?>
