<?php
	header("Expires: Sat, 1 Jan 2005 00:00:00 GMT");
	header("Last-Modified: ".gmdate( "D, d M Y H:i:s")."GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("content-type: application/x-javascript; charset=tis-620");
	

$file_name=date("Y_m_d") . "_IDCARD.jpg";
//echo "Filename:$file_name";
$command="/usr/bin/lwp-request http://127.0.0.1:8080/0/action/snapshot > /dev/null";
exec( $command );
usleep(900000);
$path_capture = "cam1";
$scopy="/var/www/shop/cam1/snapshot.jpg";
//chmod("/var/www/shop/cam1/snapshot.jpg",0777) or die("xxx");
$tcopy="/var/www/pos/htdocs/download_promotion/webcam/picture_member/".$file_name;
//chmod("/var/www/pos/htdocs/download_promotion/webcam/picture_member",777);
copy($scopy, $tcopy) or die("snap_error");
/*$delete="rm -f /var/www/shop/cam1/*";
exec( $delete );*/


?>


