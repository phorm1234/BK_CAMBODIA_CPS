<?php 
echo  "My : " . $_FILES['uploaded']['tmp_name'] . "/".$_FILES['uploaded']['name'];

$target =$_POST['target']; 
$target = $target . basename( $_FILES['uploaded']['name']) ; 
echo "<br>Remote : ".$target . "<br>";
system("chmod 777 -R  /var/www/pos/appzone/sales/models");

$ok=1; 

if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
	echo "Upload complete<br>";
	chmod("$target", 0777);
} else {
	echo "Sorry<br>";
}

?>