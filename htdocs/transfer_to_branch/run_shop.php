<?php
$user_id=$_REQUEST['user_id'];
$connect = mysql_connect("localhost","pos-ssup",'P0z-$$up');   
//mysql_query("SET NAMES UTF8");
mysql_select_db("transfer_to_branch",$connect);
mysql_query("SET character_set_results=utf8");//ตั้งค่าการดึงข้อมูลออกมาให้เป็น utf8
mysql_query("SET character_set_client=utf8");//ตั้งค่าการส่งข้อมุลลงฐานข้อมูลออกมาให้เป็น utf8
mysql_query("SET character_set_connection=utf8");//ตั้งค่าการติดต่อฐานข้อมูลให้เป็น utf8
$sql_1="INSERT INTO  `resive_file` SET `user_sent` = '$user_id' ";
$run_1=mysql_query($sql_1,$connect);

system("chmod 777  -R /var/www/shop");
system("rm -rf /var/www/pos/htdocs/transfer_to_branch/shop.zip");
system("wget http://10.100.53.2/ims/transfer_to_branch/shop.zip && echo 'Success' || echo 'Fail' ");
system("rm -rf /var/www/shop.zip");
system("unzip -o shop.zip -d /var/www/");
system("chmod 777  -R /var/www/shop");
?>