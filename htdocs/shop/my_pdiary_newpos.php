<?php
        define ("DB", "ssup");
        define ("SERVER_DB", "ssup");
        define ("SERVER_HOST", "192.168.252.240");
        define ("SERVER_USER", "shop");
        define ("SERVER_PASS", "shop");

        define ("USER_FTP", "shop_ftp");
    define ("PASSWD_FTP", "ftp_shop");

        $local=mysql_connect("localhost","root","SSUP2007");
        mysql_query("SET character_set_results=tis620");
        mysql_query("SET character_set_client = tis620");
        mysql_query("SET character_set_connection = tis620");

        $sql="REPAIR TABLE check_in_out";
        mysql_db_query("ssup",$sql,$local);

        $today=date("Y-m-d");
        $sql="select * from check_in_out where check_in_sent != '1'";
        $rs=mysql_db_query(DB,$sql,$local);
        if(@mysql_num_rows($rs) > 0){
                $serv=mysql_connect("192.168.252.240","shop","shop");
                mysql_query("SET character_set_results=tis620");
                mysql_query("SET character_set_client = tis620");
                mysql_query("SET character_set_connection = tis620");
                while($arr=mysql_fetch_array($rs)){
                        if(!$serv){

                        }else{
                                if($arr['check_date']==$today){
                                        $table="check_in_out";
                                }else{
                                        $table="check_in_out_apast";
                                }
                                $ins="
                                replace into 
                                        $table 
                                set 
                                        client_check_id='$arr[check_id]',
                                        cid='$arr[cid]',
                                        time_id='$arr[time_id]',
                                        shop_ip='$arr[shop_ip]',
                                        shop_id='$arr[shop_id]',
                                        check_date='$arr[check_date]',
                                        check_in='$arr[check_in]',
                                        check_in_img_path='$arr[check_in_img_path]',
                                        check_in_seq='$arr[check_in_seq]',
                                        check_in_reason='$arr[check_in_reason]'
                                ";
                                if(mysql_db_query(SERVER_DB,$ins,$serv)){
                                        //$upd="update check_in_out set check_in_sent ='1' where check_id = $arr[check_id]";
                                        //mysql_db_query(DB,$upd,$local);
                                        //echo "ncftpput -u ".USER_FTP." -p ".PASSWD_FTP." ".SERVER_HOST." /srv/www/htdocs/shop/capture $arr[check_in_img_path]";
                                        system("ncftpput -u ".USER_FTP." -p ".PASSWD_FTP." ".SERVER_HOST." /srv/www/htdocs/shop/capture $arr[check_in_img_path] >>/dev/null 2>>/dev/null &");
                                        $upd="update check_in_out set check_in_sent ='1' where check_id = $arr[check_id]";
                                        mysql_db_query(DB,$upd,$local);

                                }else{
                                        echo"can not connect 252.240";
                                }
                        }
                }
                mysql_close($serv);
        }
?>
