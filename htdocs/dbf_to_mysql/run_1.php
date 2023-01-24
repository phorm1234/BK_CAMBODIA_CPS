<?php
ini_set('display_errors', '1');
set_time_limit(0);
header("Content-type:text/html; charset=tis-620");  

$db_uname = 'pos-ssup';
$db_passwd = 'P0z-$$up';
$db = 'transfer_to_branch';

$conn = mysql_connect('localhost',$db_uname, $db_passwd);
mysql_query("SET NAMES UTF8");  

//--------------------------------------------------------------------------
		$filedbf1="diary1.dbf";
		$db_path1 = "/home/shopsetup/OLDDATA/dbf/".$filedbf1;

		$filedbf2="DIARY1.dbf";
		$db_path2 = "/home/shopsetup/OLDDATA/dbf/".$filedbf2;

		$filedbf3="DIARY1.DBF";
		$db_path3 = "/home/shopsetup/OLDDATA/dbf/".$filedbf3;

		$filedbf4="diary1.DBF";
		$db_path4 = "/home/shopsetup/OLDDATA/dbf/".$filedbf4;

		if(file_exists("$db_path1")){
			$filedbf=$filedbf1;
			$db_path=$db_path1;	
		}
		if(file_exists("$db_path2")){
			$filedbf=$filedbf2;
			$db_path=$db_path2;
		}
		if(file_exists("$db_path3")){
			$filedbf=$filedbf3;
			$db_path=$db_path3;
		}
		if(file_exists("$db_path4")){
			$filedbf=$filedbf4;
			$db_path=$db_path4;
		}

		 print("Transfer data $filedbf \n");
		if(!file_exists("$db_path")){
				 print(" File Not Found $filedbf \n");
				 exit();
		}



		$array=explode(".",$filedbf); 
		$tbl=strtoupper($array[0]);

		$dbh = dbase_open($db_path, 0) or die("Error! Could not open dbase database file '$db_path'.");
		$column_info = dbase_get_header_info($dbh);

		$line = array();
		$key = array();
		foreach($column_info as $col){        
				switch($col['name'])
				{
						case 'DOC_NO':
						$key[]= " KEY `$col[name]` (`$col[name]`)";
						break;

						case 'DOC_DT':
						$key[]= " KEY `$col[name]` (`$col[name]`)";
						break;

						case 'BRAND':
						$key[]= " KEY `$col[name]` (`$col[name]`)";
						break;

						case 'SHOP':
						$key[]= " KEY `$col[name]` (`$col[name]`)";
						break;

						case 'MEMBER':
						$key[]= " KEY `$col[name]` (`$col[name]`)";
						break;
				}

				switch($col['type'])
				{
						case 'character':
						$line[]= "`$col[name]` VARCHAR( $col[length] ) NOT NULL";
						break;
						case 'number':
						$line[]= "`$col[name]` FLOAT NOT NULL";
						break;
						case 'boolean':
						$line[]= "`$col[name]` BOOL NOT NULL";
						break;
						case 'date':
						$line[]= "`$col[name]` DATE NOT NULL DEFAULT '1900-01-01' ";
						break;
						case 'memo':
						$line[]= "`$col[name]` TEXT NOT NULL";
						break;
				}
		}
		$kstr= implode(",",$key);
		$str = implode(",",$line);
		mysql_select_db($db);

		$create_db="CREATE DATABASE IF NOT EXISTS $db";
		mysql_query($create_db,$conn);

		$sql="DROP TABLE IF EXISTS `$tbl`;";
		mysql_query($sql,$conn);
		if($kstr!=""){
				$sql="CREATE TABLE  `$tbl` (`ID`  int(20) NOT NULL auto_increment, $str,PRIMARY KEY  (`ID`) ,$kstr  ) 
				ENGINE=InnoDB  DEFAULT CHARSET=utf8  COMMENT='ตาราง temp โอนไปสาขา' AUTO_INCREMENT=1;";
		}else{
				$sql="CREATE TABLE  `$tbl` (`ID`  int(20) NOT NULL auto_increment, $str,PRIMARY KEY  (`ID`)  ) 
				ENGINE=InnoDB  DEFAULT CHARSET=utf8  COMMENT='ตาราง temp โอนไปสาขา' AUTO_INCREMENT=1;";
		}
		mysql_query($sql,$conn);
		set_time_limit(0); 
		import_dbf($db, $tbl, $db_path);
//--------------------------------------------------------------------------
function import_dbf($db,$table, $dbf_file)
{
    global $conn;
    if (!$dbf = dbase_open ($dbf_file, 0)){ die("Could not open $dbf_file for import."); }
    $num_rec = dbase_numrecords($dbf);
    $num_fields = dbase_numfields($dbf);
    $fields = array();

    for ($i=1; $i<=$num_rec; $i++){
            $row = @dbase_get_record_with_names($dbf,$i);

            $q = "insert into $db.$table values ('',";
            foreach ($row as $key => $val){
                if ($key == 'deleted'){ continue; }
				$val=iconv("TIS-620", "UTF-8", $val);
               $q .= "'" . addslashes(trim($val)) . "',"; // Code modified to trim out whitespaces
			    print ("Step 2 Import dbf to $table : $i \n") ;
            }

            if (isset($extra_col_val)){ $q .= "'$extra_col_val',"; }
            $q = substr($q, 0, -1);
            $q .= ')';
            //if the query failed - go ahead and print a bunch of debug info
            if (!$result = mysql_query($q, $conn)){
                print (mysql_error() . " SQL: $q
                \n");
                print (substr_count($q, ',') + 1) . " Fields total.

                ";
                $problem_q = explode(',', $q);
                $q1 = "desc $db.$table";
                $result1 = mysql_query($q1, $conn);
                $columns = array();
                $i = 1;
                while ($row1 = mysql_fetch_assoc($result1)){
                    $columns[$i] = $row1['Field'];
					 print ("Step 2 run insert $columns[$i] \n") ;
                    $i++;
                }
                $i = 1;
                    foreach ($problem_q as $pq){
                        print "$i column: {$columns[$i]} data: $pq
                        \n";
                        $i++;
						
                    }
                     die();
            }
    }
}
//--------------------------------------------------------------------------
system("touch /home/shopsetup/dbf_to_mysql/pook1.txt");

?>























