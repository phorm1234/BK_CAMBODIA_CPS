<?php
 define("FPDF_FONTPATH","/font/");
 include "fpdf_thai.php";
  $pdf=new FPDF_Thai($orientation='P',$unit='mm',$format='a4');
            $pdf->SetThaiFont();
            $pdf->Open();
            $pdf->AddPage();
            
            $pdf->SetY(8);
            $pdf->SetX(75);
            $pdf->SetDrawColor('blue');
            $pdf->SetFillColor('#EE7070');
            $pdf->SetTextColor('yellow');
            $pdf->SetFont('FreesiaUPC','U',14);
             $startX=0;
            $startY=10;
            $startY2=15;
            $hline_he=10;
            $hline=7;
            $hline_med=6;
            $hline_me=5;
            $sys_datenow="2001";
            $pdf->SetX(0);
            $pdf->SetY($startY);
            $pdf->SetFont('FreesiaUPC','',14);
            $pdf->Cell(160,$hline_me,'',0,0,'L',0);
            $pdf->Cell(20,$hline_me,"วันที่ : ".$sys_datenow." " ,0,0,'L',0);
?>
