<?php 
echo "<pre>";
print_r($_POST);
echo "</pre>";
echo "<br><br>";
$a=json_decode($_POST['shlef']);
echo "<pre>";
print_r($a);
echo "</pre>";

?>
<a href="/stock/Plugin/fpdf/test.php"  target="_blank">link</a>