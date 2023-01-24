<?php
	/*header("Expires: Sat, 1 Jan 2005 00:00:00 GMT");
	header("Last-Modified: ".gmdate( "D, d M Y H:i:s")."GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("content-type: application/x-javascript; charset=tis-620");*/

$id_card=$_GET['id_card'];
$num_snap=$_GET['num_snap'];
$ip_this=$_GET['ip_this'];

$path_folder=date("Ym");

$file_name=$id_card . "_snap" . $num_snap . ".jpg";
$file_name_bk=date("Y_m_d_H_i_s") . "_" . $id_card . "_snap" . $num_snap . ".jpg";
//echo "Filename:$file_name";
$command="/usr/bin/lwp-request http://127.0.0.1:8080/0/action/snapshot > /dev/null";
exec( $command );
usleep(900000);

$command3="ls -n image_member/$path_folder";
//$command3="ls -n image_member/aaa";
$ls=exec($command3);
if(empty($ls)){
		$command1="mkdir image_member/$path_folder";
		exec($command1);
		$command2="chmod 777 image_member/$path_folder";
		exec( $command2);
		print "create $path_folder <br> ";
}

$path_capture = "cam1";
$path_img="http://$ip_this/download_promotion/id_card_quick/image_member/$path_folder/$file_name";

//$scopy="/var/www/shop/cam1/snapshot.jpg";
$scopy="http://localhost/shop/cam1/snapshot.jpg";

//chmod("/var/www/shop/cam1/snapshot.jpg",0777) or die("xxx");
$tcopy="/var/www/pos/htdocs/download_promotion/id_card_quick/image_member/$path_folder/".$file_name;
//chmod("/var/www/pos/htdocs/download_promotion/webcam/picture_member",777);
echo $tcopy;
copy($scopy, $tcopy) or die(1);

$tcopy="/var/www/pos/htdocs/download_promotion/id_card_quick/image_member/$path_folder/".$file_name_bk;
//chmod("/var/www/pos/htdocs/download_promotion/webcam/picture_member",777);
copy($scopy, $tcopy) or die(1);


/*$delete="rm -f /var/www/shop/cam1/*";
exec( $delete );*/



?>

<script>window.close(); </script>





