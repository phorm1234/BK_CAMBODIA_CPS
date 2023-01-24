<?php

$data 		= $_POST['imgData'];
$filename 	= $_POST['user_id'];
if($data != "" && $filename != ""){
	$file = $filename.".png";
	$uri =  substr($data,strpos($data,",")+1);
	file_put_contents("/var/www/FingerScanTemplate/".$file, base64_decode($uri));
	echo $file;
}

?>