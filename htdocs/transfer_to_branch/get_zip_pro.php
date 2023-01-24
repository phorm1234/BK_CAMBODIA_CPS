<?php
	set_time_limit(0);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_errors','On');
	$hst="localhost";	
	$usr="pos-ssup";
	$pwd='P0z-$$up';
	$db="pos_ssup";
	$local = mysql_connect($hst,$usr,$pwd) OR DIE(MYSQL_ERROR());   
	mysql_select_db($db,$local) OR DIE(MYSQL_ERROR());
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");

	function fulldelete($location) {   
	    if (is_dir($location)) {   
			$currdir = opendir($location);   
			while ($file = readdir($currdir)) {   
			    if ($file  <> ".." && $file  <> ".") {   
			        $fullfile = $location."/".$file;   
			        if (is_dir($fullfile)) {   
			            if (!fulldelete($fullfile)) {   
			                return false;   
			            }   
			        } else {   
			            if (!unlink($fullfile)) {   
			                return false;   
			            }   
			        }   
			    }   
			}//while  
			closedir($currdir); 
			/*  
			if (! rmdir($location)) {   
			    return false;   
			}*/
	    }else { 
			/*  
			if (!unlink($location)) {   
			    return false;   
			}*/
			return false; 
	    }   
	    return true;   
	}//func

	$brand=$_REQUEST['brand'];
	$branch_id=$_REQUEST['branch_id'];
	$datapro_zip=$branch_id.".zip";
	system("chmod 777  -R /var/www/pos/htdocs/transfer_to_branch/datapro");
	system("rm -rf /var/www/pos/htdocs/transfer_to_branch/$datapro_zip");
	//START
	system("wget http://10.100.53.2/wservice/possupport/up2shop/op/data/$datapro_zip && echo 'Success' || echo 'Fail'");
	system("unzip -o $datapro_zip -d /var/www/pos/htdocs/transfer_to_branch/datapro/");
	system("chmod 777  -R /var/www/pos/htdocs/transfer_to_branch/datapro/");
	$dirname="datapro";
	if($dirHandle = @opendir($dirname)){
            $old_cwd = getcwd();
            chdir($dirname);
            while ($file = readdir($dirHandle)){
                if ($file == '.' || $file == '..') continue;
                if (!is_dir($file)){
				    $cmd="mysql -u".$usr." -p'".$pwd."' pos_ssup <  /var/www/pos/htdocs/transfer_to_branch/datapro/$file";
				    system($cmd);
                }
            }//while
            closedir($dirHandle);
            chdir($old_cwd);
            //if (!rmdir($dirname)) return false;
    }//if
	//delete all file
	sleep(5);
	fulldelete($dirname);
	echo "STEP3\n";
?>
