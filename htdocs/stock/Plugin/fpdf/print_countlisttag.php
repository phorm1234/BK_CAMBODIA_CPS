<?php
$shelf_no=json_decode($_POST['shlef'],true);
$doc_no=$_POST['doc_no_pdf'];
/*echo "<pre>";
print_r($shelf_no);
echo "</pre>";*/

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
foreach($shelf_no as $val_shelf){
	$pdf->AddPage();
	$pdf->AddFont('Cordia','','cordia.php');
	$pdf->AddFont('Cordia','B','cordiab.php');
	$pdf->AddFont('Cordia','I','cordiai.php');
	
	$date=date("d/m/Y");
	$time=date("H:i");
	$hline=6;
	$pdf->SetFont('Cordia','B',14);
	$pdf->Cell(140,$hline,"PROC:PRNCHKL",0,0,'L',0);
	$pdf->Cell(30,$hline,"วันที่ : $date",0,0,'L',0);
	$pdf->Cell(30,$hline,"เวลา : $time",0,0,'L',0);
	$pdf->Ln();
	$pdf->Cell(170,$hline,"รายงานการเช็ค Stock",0,0,'L',0);
	$pdf->Cell(30,$hline,"หน้าที่",0,0,'L',0);
	$pdf->Ln();
	$pdf->Cell(50,$hline,"วันที่ตรวจนับ : $date",0,0,'L',0);
	$pdf->Cell(60,$hline,"เลขที่เอกสาร : $doc_no",0,0,'L',0);
	$pdf->Cell(50,$hline,"Shelf No : ".$val_shelf['shelf_no'],0,0,'L',0);
	$pdf->Ln();
	$head_col_blank=3;
	$pdf->SetFont('Cordia','B',12);
	$pdf->Cell(30,$head_col_blank,"",0,0,'L',0);
	$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
	$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
	$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
	$pdf->Cell(25,$head_col_blank,"",0,0,'L',0);
	$pdf->Ln();
	
	$head_col=5;
	$pdf->SetFont('Cordia','B',12);
	$pdf->Cell(30,$head_col,"รหัสชั้นวาง",0,0,'C',0);
	$pdf->Cell(30,$head_col,"รหัสชั้นห้อง",0,0,'C',0);
	$pdf->Cell(30,$head_col,"รหัสสินค้า",0,0,'C',0);
	$pdf->Cell(30,$head_col,"จำนวนที่นับ",0,0,'C',0);
	$pdf->Cell(25,$head_col,"สถานะ",0,0,'C',0);
	$pdf->Ln();

	$head_col_line=1;
	$pdf->Cell(30,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Cell(30,$head_col_line,"----------------",0,0,'C',0);
	$pdf->Cell(30,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Cell(30,$head_col_line,"---------------",0,0,'C',0);
	$pdf->Cell(25,$head_col_line,"----------",0,0,'C',0);
	$pdf->Ln();
	//asort($val_shelf['floor_no']);
	foreach($val_shelf['data'] as $data){
		$head_col_line=5;
		$pdf->SetFont('Cordia','',12);
		$pdf->Cell(30,$head_col_line,$data['floor_no'],0,0,'C',0);
		$pdf->Cell(30,$head_col_line,$data['room_no'],0,0,'C',0);
		$pdf->Cell(30,$head_col_line,$data['product_id'],0,0,'C',0);
		$pdf->Cell(30,$head_col_line,$data['quantity'],0,0,'C',0);
		$pdf->Cell(25,$head_col_line,"N",0,0,'C',0);
		$pdf->Ln();
	}
	$pdf->SetY( 260 );
	$pdf->Cell(0,$head_col_line,"________________________________________________________________________________________________________________________________________",0,0,'L',0);
	$pdf->Ln();
	
	$pdf->SetFont('Cordia','',12);
	$pdf->Cell(18,$head_col_line,"หัวหน้าจุดขาย",0,0,'L',0);
	$pdf->Cell(1,$head_col_line,":",0,0,'C',0);
	$pdf->Cell(40,$head_col_line,"________________________",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"วันที่",0,0,'L',0);
	$pdf->Cell(20,$head_col_line,"__/__/______",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"เวลา",0,0,'L',0);
	$pdf->Cell(25,$head_col_line,"_________",0,0,'L',0);
	$pdf->Ln();
	
	$pdf->Cell(18,$head_col_line,"ผู้ตรวจสอบ",0,0,'L',0);
	$pdf->Cell(1,$head_col_line,":",0,0,'C',0);
	$pdf->Cell(40,$head_col_line,"________________________",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"วันที่",0,0,'L',0);
	$pdf->Cell(20,$head_col_line,"__/__/______",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"เวลา",0,0,'L',0);
	$pdf->Cell(25,$head_col_line,"_________",0,0,'L',0);
	$pdf->Ln();
	
}
		
$pdf->Output();

?>
