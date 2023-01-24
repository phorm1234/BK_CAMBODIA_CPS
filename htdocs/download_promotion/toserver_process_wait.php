<?php
$url=$_GET['url'];
echo $url;
$fp = @fopen("http://localhost/download_promotion/toserver_bill_day_quick.php", "r");
$text=@fgetss($fp, 4096);
echo "SendBill30Minit";
echo "<script>window.location='$url'</script>";
?>
