<?php
// R&D NIDApplet Demo
// Copyright 2013 R&D Computer System Co., Ltd. www.rd-comp.com
// PostTest R130214

  $idFile = "id.jpg"; 
  if (isset($_POST['NIDNumber'])) {
    	$idFile = $_POST['NIDNumber'];             // get data
		$idFile = "NID".$idFile.".jpg";
	}	

  $xCardData = "";
  if (isset($_POST['nidCardData'])) {
    	$xCardData = $_POST['nidCardData'];             // get data
	}	

  $xNIDNumber = "";
  if (isset($_POST['NIDNumber'])) {
    	$xNIDNumber = $_POST['NIDNumber'];             // get data
	}	

// if data are received via POST, with index of 'test'
  if (isset($_POST['Photo'])) {

/*
   decode (urldecode) the parameter wich is encoded in PHP with 'urlencode'
   rezultat = decodeURIComponent(rezultat.replace(/\+/g,  " "));
*/

    $str_init = $_POST['Photo'];             // get data
	$str = str_replace( ' ','+',$str_init); 
	
	// get contents of a file into a string

	$data = base64_decode($str);
	//$File = "id.jpg"; 
	$Handle = fopen($idFile, 'w');
	fwrite($Handle, $data); 
	fclose($Handle); 		
	$str = 'save image ok'	;
    
    echo "<img src='$idFile' width='100' height='120'>";
    echo "</br>";
    echo "The picture '<i>".$idFile."</i>' saved." ;
    echo "<br>  NID Number : <i>".$xNIDNumber.'</i>';
    echo "<br>  NID Data : <i>".$xCardData.'</i>';
	}
	
?> 