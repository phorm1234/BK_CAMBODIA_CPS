<?php
$shelf_no=json_decode($_POST['shlef'],true);
$doc_no=$_POST['doc_no'];
/*echo "<pre>";
//print_r($shelf_no);
echo "</pre>";

foreach($shelf_no as $val_shelf){
	asort($val_shelf['floor_no']);
	foreach($val_shelf['floor_no'] as $val_floor){
		foreach($val_floor['room_no'] as $val_room){
			if(!empty($val_room['product_id'])){	
				foreach($val_room['product_id'] as $val_product){
					//echo "<pre>";
					echo $val_floor['floor_no']."-->".$val_room['room_no']."->".$val_product['product_id']."<br>";
					//echo "</pre>";
				}
			}
		}
	}
}*/

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
//$doc_no="0000000000000000000";
foreach($shelf_no as $val_shelf){
	$pdf->AddPage();
	$pdf->AddFont('Cordia','','cordia.php');
	$pdf->AddFont('Cordia','B','cordiab.php');
	$pdf->AddFont('Cordia','I','cordiai.php');
	
	$date=date("d/m/Y");
	$time=date("H:i");
	$hline=6;
	$pdf->SetFont('Cordia','B',14);
	$pdf->Cell(140,$hline,"PROC:PRNIVTAG",0,0,'L',0);
	$pdf->Cell(30,$hline,"?ѹ??? : $date",0,0,'L',0);
	$pdf->Cell(30,$hline,"???? : $time",0,0,'L',0);
	$pdf->Ln();
	$pdf->Cell(170,$hline,"?͡??? ??õ?Ǩ?Ѻ?Թ???",0,0,'L',0);
	$pdf->Cell(30,$hline,"˹?ҷ??",0,0,'L',0);
	$pdf->Ln();
	$pdf->Cell(50,$hline,"?ѹ?????Ǩ?Ѻ : $date",0,0,'L',0);
	$pdf->Cell(60,$hline,"?Ţ????͡??? : $doc_no",0,0,'L',0);
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
	$pdf->Cell(30,$head_col,"???ʪ????ҧ",0,0,'C',0);
	$pdf->Cell(30,$head_col,"???ʪ?????ͧ",0,0,'C',0);
	$pdf->Cell(30,$head_col,"?????Թ???",0,0,'C',0);
	$pdf->Cell(25,$head_col,"ʶҹ?",0,0,'C',0);
	$pdf->Cell(25,$head_col,"??Ǩ?Ѻ 1",0,0,'C',0);
	$pdf->Cell(25,$head_col,"??Ǩ?Ѻ 2",0,0,'C',0);
	$pdf->Cell(25,$head_col,"??Ǩ?Ѻ 3",0,0,'C',0);
	$pdf->Ln();

	$head_col_line=1;
	$pdf->Cell(30,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Cell(30,$head_col_line,"----------------",0,0,'C',0);
	$pdf->Cell(30,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Cell(25,$head_col_line,"---------",0,0,'C',0);
	$pdf->Cell(25,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Cell(25,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Cell(25,$head_col_line,"--------------",0,0,'C',0);
	$pdf->Ln();
	asort($val_shelf['floor_no']);
	foreach($val_shelf['floor_no'] as $val_floor){
		foreach($val_floor['room_no'] as $val_room){
			if(!empty($val_room['product_id'])){	
				foreach($val_room['product_id'] as $val_product){
					$head_col_line=5;
					$pdf->SetFont('Cordia','',12);
					$pdf->Cell(30,$head_col_line,$val_floor['floor_no'],0,0,'C',0);
					$pdf->Cell(30,$head_col_line,$val_room['room_no'],0,0,'C',0);
					$pdf->Cell(30,$head_col_line,$val_product['product_id'],0,0,'C',0);
					$pdf->Cell(25,$head_col_line,"N",0,0,'C',0);
					$pdf->Cell(25,$head_col_line,"",0,0,'C',0);
					$pdf->Cell(25,$head_col_line,"",0,0,'C',0);
					$pdf->Cell(25,$head_col_line,"",0,0,'C',0);
					$pdf->Ln();
				}
			}
		}
	}
	$pdf->SetY( 255 );
	$pdf->Cell(0,$head_col_line,"________________________________________________________________________________________________________________________________________",0,0,'L',0);
	$pdf->Ln();
	
	$pdf->SetFont('Cordia','',12);
	$pdf->Cell(18,$head_col_line,"?????Ǩ?Ѻ",0,0,'L',0);
	$pdf->Cell(1,$head_col_line,":",0,0,'C',0);
	$pdf->Cell(40,$head_col_line,"________________________",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"?ѹ???",0,0,'L',0);
	$pdf->Cell(20,$head_col_line,"__/__/______",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"????",0,0,'L',0);
	$pdf->Cell(25,$head_col_line,"_________",0,0,'L',0);
	$pdf->Ln();
	
	$pdf->Cell(18,$head_col_line,"?????Ǩ??????",0,0,'L',0);
	$pdf->Cell(1,$head_col_line,":",0,0,'C',0);
	$pdf->Cell(40,$head_col_line,"________________________",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"?ѹ???",0,0,'L',0);
	$pdf->Cell(20,$head_col_line,"__/__/______",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"????",0,0,'L',0);
	$pdf->Cell(25,$head_col_line,"_________",0,0,'L',0);
	$pdf->Ln();
	
	$pdf->Cell(18,$head_col_line,"?????͹??????",0,0,'L',0);
	$pdf->Cell(1,$head_col_line,":",0,0,'C',0);
	$pdf->Cell(40,$head_col_line,"________________________",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"?ѹ???",0,0,'L',0);
	$pdf->Cell(20,$head_col_line,"__/__/______",0,0,'L',0);
	$pdf->Cell(7,$head_col_line,"????",0,0,'L',0);
	$pdf->Cell(25,$head_col_line,"_________",0,0,'L',0);
	$pdf->Ln();
	
}
		
$pdf->Output();

?>