<?php
	set_time_limit(0);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_errors','On');
	header("Content-type:text/html; charset=utf-8"); 	
	$localhost="localhost";
	$password='P0z-$$up';
	$user_name="pos-ssup";	
	$localhost = mysql_connect("localhost","$user_name",$password);   
	mysql_query("SET NAMES UTF8");
	mysql_select_db("transfer_to_office",$localhost);	
	$sql0="SELECT * FROM  pos_ssup.`com_doc_date` WHERE 	1 ";	  
	$result0=mysql_query($sql0,$localhost);
	$arr0=mysql_fetch_array($result0);
	$doc_date=$arr0['doc_date'];	
	$sql="SELECT * FROM `com_branch_os` WHERE 1 ";	  
	$result=mysql_query($sql,$localhost);
	$arr=mysql_fetch_array($result);
	$rows=mysql_num_rows($result);
	if($rows>0){
			$BRAND=$arr['company_id'];
			$SHOP=$arr['branch_id'];
			if($SHOP==""){
				echo"$SHOP Empty";
				exit();
			}	
			$sql_update0="UPDATE pos_ssup.com_doc_date SET  reg_user = '$SHOP' ";	
			mysql_query($sql_update0,$localhost);	
			$create_day="";
			$today=date ("Y-m-d");
			$filename = "$SHOP.zip";
			if(file_exists($filename)){
				$create_day=date ("Y-m-d", filemtime($filename));
			}	
			if($create_day!=""){
						if($create_day != $doc_date){
							$sql_send="TRUNCATE TABLE  transfer_to_office.time_transfer";
							mysql_query($sql_send,$localhost);
						}else{
							echo sent_data($BRAND,$SHOP);
							exit();
						}
			}else{				
					echo sent_data($BRAND,$SHOP);
					exit();
			}	
	}else{
					echo"shop empty";
					exit();
	}
  echo sent_data($BRAND,$SHOP);

	function sent_data($BRAND,$SHOP){
					$localhost="localhost";
					$password='P0z-$$up';
					$user_name="pos-ssup";
	
					$localhost = mysql_connect("localhost","$user_name",$password);   
					mysql_query("SET NAMES UTF8");
					mysql_select_db("transfer_to_office",$localhost);
	
					$sql00="SELECT * FROM  pos_ssup.com_doc_date WHERE 	1 ";	  
					$result00=mysql_query($sql00,$localhost);
					$arr00=mysql_fetch_array($result00);
					$doc_date=$arr00['doc_date'];
					$folder="$SHOP";
					$path="/var/www/pos/htdocs/transkh.ssup.co.th/$folder";			
					if(is_dir($folder)){
						$del_folder=full_rmdir($folder);
						$zip="$SHOP.zip";
						system("rm -rf $zip");
					}
					mkdir("$folder",0777,TRUE);
					chmod("$folder", 0777); 
					$sql_insert="
					INSERT INTO transfer_to_office.time_transfer 
					SET     
					start_run = NOW(),
					shop='$SHOP',
					status_run='N',
					num_transfer='1' ";	
					mysql_query($sql_insert,$localhost);
					$sql="SELECT * FROM  transfer_to_office.`table_transfer` WHERE 1 ";	  
					$result=mysql_query($sql,$localhost);
					$rows=mysql_num_rows($result);
					while($arr=mysql_fetch_array($result)){
							$file_mysql=$arr['file_mysql'];
								switch ($file_mysql) {
										case "com_dayend_time":
												$where="dayend_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -0 day) ";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "com_doc_date":
												$where="upd_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -0 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "com_data_po":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -0 day) ";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "com_stock_master":	
												$Year=date('Y');
												$M=date('m');
												$Month=date('m')-1;
												if($Month<10){
													$BG="0$Month";
												}else{
													$BG=$Month;
												}
												$where="com_stock_master.year='$Year'  AND  com_stock_master.month BETWEEN '$BG' AND '$M' ";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary2":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1_ai":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1_iq":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1_or":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1_rq":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1_to":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary2_ai":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary2_iq":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary2_or":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary2_rq":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary2_to":
												$where="doc_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "com_expense_head":
												//$where="doc_dt BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql >  $path/$file_mysql.sql";
												system($command);
										break;
										case "com_expense_list":
												//$where="doc_dt BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql >  $path/$file_mysql.sql";
												system($command);
										break;
										case "crm_card":
												$where="apply_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
												system($command);
										break;
										case "crm_profile":
												$where="upd_date BETWEEN date_add('$doc_date',interval -186 day)  AND date_add('$doc_date',interval -1 day)";
												$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\" >  $path/$file_mysql.sql";
												system($command);
										break;
										case "trn_diary1_rd":
											$where="doc_date BETWEEN date_add('$doc_date',interval -100 day)  AND date_add('$doc_date',interval -1 day)";
											$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
											system($command);
											break;
										case "trn_diary2_rd":
											$where="doc_date BETWEEN date_add('$doc_date',interval -100 day)  AND date_add('$doc_date',interval -1 day)";
											$command = "mysqldump -u$user_name -p'$password' --no-create-db --no-create-info --replace pos_ssup $file_mysql --where=\"$where\">  $path/$file_mysql.sql";
											system($command);
											break;
								}
					}
	
					$sql_update="
					UPDATE transfer_to_office.time_transfer 
					SET     
					end_run = NOW(),
					status_run='Y'
					WHERE 
					shop='$SHOP' AND status_run='N'  ";	
					mysql_query($sql_update,$localhost);
					mysql_close($localhost);
					//--------------------------------------------------------------------------------------------
					$comman="zip -j $SHOP.zip  $SHOP/* >/dev/null ";
					system($comman);
					$comman= "ncftpput -u transopkh -p bkYvewta9G34mHA3Fa36KD2sxBUF9CTB transopkh.ssup.co.th / $SHOP.zip";
					system($comman);
					$BRAND=urlencode($BRAND);
					$SHOP=urlencode($SHOP);
					$fp = @fopen("http://transopkh.ssup.co.th/transfer_to_office/op/run_script_6_month.php?BRAND=$BRAND&SHOP=$SHOP", "r");
					$text=@fgetss($fp, 4096);
					return "Succeed";
	}//func
	function full_rmdir($dirname){
	        if ($dirHandle = @opendir($dirname)){
	            $old_cwd = getcwd();
	            chdir($dirname);	
	            while ($file = readdir($dirHandle)){
	                if ($file == '.' || $file == '..') continue;	
	                if (is_dir($file)){
	                    if (!full_rmdir($file)) return false;
	                }else{
	                    if (!unlink($file)) return false;
	                }
	            }
	            closedir($dirHandle);
	            chdir($old_cwd);
	            if (!rmdir($dirname)) return false;	
	            return true;
	        }else{
	            return false;
	        }
	 }//func
?>