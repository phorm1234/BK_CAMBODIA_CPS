<?php
$product_id=json_decode($_POST['product_id_pdf'],true);
$doc_no=$_POST['doc_no_pdf'];
//echo "<pre>";
//print_r($product_id);
//echo "</pre>";

require('fpdf.php');
class PDF extends FPDF
{
	function Footer()
    {
      $this->SetXY(100,-15);
      $this->SetFont('Helvetica','I',10);
      $this->Write (5, 'This is a footer');
    }
}
$pdf=new FPDF('P','mm','a4');

$pdf->AddPage();
$pdf->AddFont('Cordia','','cordia.php');
$pdf->AddFont('Cordia','B','cordiab.php');
$pdf->AddFont('Cordia','I','cordiai.php');

$date=date("d/m/Y");
$time=date("H:i");
$hline=6;
$pdf->SetFont('Cordia','B',14);
$pdf->Cell(140,$hline,"PROC:PRNCHKL4",0,0,'L',0);
$pdf->Cell(30,$hline,"วันที่ : $date",0,0,'L',0);
$pdf->Cell(30,$hline,"เวลา : $time",0,0,'L',0);
$pdf->Ln();
$pdf->Cell(170,$hline,"รายงานยอดแตกต่าง",0,0,'L',0);
$pdf->Cell(30,$hline,"หน้าที่",0,0,'L',0);
$pdf->Ln();
$pdf->Cell(50,$hline,"วันที่ตรวจนับ : $date",0,0,'L',0);
$pdf->Cell(60,$hline,"เลขที่เอกสาร : $doc_no",0,0,'L',0);
$pdf->Ln();

$head_col_blank=3;
$pdf->SetFont('Cordia','B',12);
$pdf->Cell(30,$head_col_blank,"",0,0,'L',0);
$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
$pdf->Ln();

$l_line=null;
for($c_line=1;$c_line<205;$c_line++){
	$l_line.="-";
}

$j_line=null;
for($b_line=1;$b_line<152;$b_line++){
	$j_line.="-";
}

foreach($product_id as $val_product){
	foreach($val_product['product_dif'] as $val_product_sum){
		$head_col=5;
		$pdf->SetFont('Cordia','B',12);
		$pdf->Cell(15,$head_col,"รหัสสินค้า",0,0,'L',0);
		$pdf->Cell(3,$head_col,":",0,0,'C',0);
		$pdf->Cell(15,$head_col,$val_product_sum['product_id'],0,0,'L',0);
		$pdf->Cell(10,$head_col,"stock",0,0,'C',0);
		$pdf->Cell(3,$head_col,":",0,0,'C',0);
		$pdf->Cell(15,$head_col,$val_product_sum['stock'],0,0,'L',0);
		$pdf->Cell(10,$head_col,"Check",0,0,'C',0);
		$pdf->Cell(3,$head_col,":",0,0,'C',0);
		$pdf->Cell(15,$head_col,$val_product_sum['check'],0,0,'L',0);
		$pdf->Cell(15,$head_col,"ยอดแตกต่าง",0,0,'C',0);
		$pdf->Cell(3,$head_col,":",0,0,'C',0);
		$pdf->Cell(10,$head_col,$val_product_sum['dif'],0,0,'L',0);
		$pdf->Ln();
		
		$head_col_line=1;
		$pdf->Cell(50,$head_col_line,$l_line,0,0,'L',0);
		$pdf->Ln();
		
		$head_col=5;
		$pdf->SetFont('Cordia','B',12);
		$pdf->Cell(30,$head_col,"จำนวนที่นับ",0,0,'C',0);
		$pdf->Cell(30,$head_col,"สถานะ",0,0,'C',0);
		$pdf->Cell(30,$head_col,"Shelf",0,0,'C',0);
		$pdf->Cell(30,$head_col,"Floor",0,0,'C',0);
		$pdf->Cell(25,$head_col,"Room",0,0,'C',0);
		$pdf->Ln();
		
		$head_col_line=1;
		$pdf->Cell(50,$head_col_line,$l_line,0,0,'L',0);
		$pdf->Ln();
		
		foreach($val_product_sum['dif_detail'] as $product_detail){
			$head_col=5;
			$pdf->SetFont('Cordia','B',12);
			$pdf->Cell(30,$head_col,$product_detail['quantity'],0,0,'C',0);
			$pdf->Cell(30,$head_col,$product_detail['product_status'],0,0,'C',0);
			$pdf->Cell(30,$head_col,$product_detail['shelf_no'],0,0,'C',0);
			$pdf->Cell(30,$head_col,$product_detail['floor_no'],0,0,'C',0);
			$pdf->Cell(25,$head_col,$product_detail['room_no'],0,0,'C',0);
			$pdf->Ln();
			
			$head_col_line=1;
			$pdf->Cell(50,$head_col_line,$l_line,0,0,'L',0);
			$pdf->Ln();
		}
		
		$pdf->SetFont('Cordia','B',12);
		$pdf->Cell(30,$head_col_blank,"",0,0,'L',0);
		$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
		$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
		$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
		$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
		$pdf->Ln();
	}
}
$pdf->Output();
?>