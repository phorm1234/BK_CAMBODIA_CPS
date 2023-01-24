<?php
$ip=$_GET['ip'];
$user=$_GET['user'];
$pass=$_GET['pass'];
$filea=$_GET['filea'];
$fileb=$_GET['fileb'];
echo "Server : $ip<br>";
echo "user : $user<br>";
echo "pass : $pass<br>";
echo "filea : $filea<br>";
echo "fileb : $fileb<br>";
//system("chmod 777 -R  /var/www/pos/appzone/sales/models");
system($fileb);
if($filea!="" && $fileb!=""){
	$conn = ftp_connect($ip) or die("Could not $ip");
	ftp_login($conn,$user,$pass) or die("Could not Login");
	$res = ftp_size($conn, $filea);
	echo 'File a : ' . $res . ' bytes<br>';
	//$flgDelete = unlink($fileb);

	$run=ftp_get($conn,$fileb,$filea,FTP_ASCII) or die("NoCoppy");
	echo 'File B : ' . filesize($fileb) . ' bytes<br>';
	if($run==0){
		echo "<span style='color:#ff0000;'>Coppy Error</span>";
	}else{
		echo "<span  style='color:#55cf15;'>Coppy Ok</span>";
	}
	
	ftp_close($conn);
}

//system("chmod 777 -R  /var/www/pos/appzone/sales/models");
system($fileb);
?>