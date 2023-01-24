<?php
ini_set('display_errors', '1');
set_time_limit(0);
header("Content-type:text/html; charset=tis-620");  
//--------------------------------------------------------------------------
$arr_config=array();
$PATH_FILE_INI="/var/www/pos/htdocs/.setuppos.wr";
if(!file_exists($PATH_FILE_INI)){
	echo "FILE NOT FOUND.";
	exit();
}
$file = fopen($PATH_FILE_INI, "r");
$i = 0;
while (!feof($file)) {
	$arr_config[] = fgets($file);
}
fclose($file);
$brand=trim($arr_config[0]);
$brand=strtoupper($brand);
$corporation_id=$brand;
$company_id=$brand;
$branch_id=trim($arr_config[1]);
//--------------------------------------------------------------------------
$db_uname = 'pos-ssup';
$db_passwd = 'P0z-$$up';
$db = 'transfer_to_branch';
$reg_user="005949";

$conn = mysql_connect('localhost',$db_uname, $db_passwd);
mysql_query("SET NAMES UTF8");  
//--------------------------------------------------------------------------
		$filedbf1="employee.dbf";
		$db_path1 = "/home/shopsetup/OLDDATA/dbf_dos/".$filedbf1;

		$filedbf2="EMPLOYEE.dbf";
		$db_path2 = "/home/shopsetup/OLDDATA/dbf_dos/".$filedbf2;

		$filedbf3="EMPLOYEE.DBF";
		$db_path3 = "/home/shopsetup/OLDDATA/dbf_dos/".$filedbf3;

		$filedbf4="employee.DBF";
		$db_path4 = "/home/shopsetup/OLDDATA/dbf_dos/".$filedbf4;

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

						case 'EMP_ID':
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
						case 'emp_id':
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
$upd_user="005949";
$end_y=date('Y')+10;
$end_date=date('$end_y-m-d');

$sql_clear="TRUNCATE TABLE `pos_ssup`.`conf_employee` ";
$res_clear=mysql_query($sql_clear);

$sql_1_2="INSERT INTO `pos_ssup`.`conf_employee` (`id`, `corporation_id`, `company_id`, `branch_id`, `employee_id`, `user_id`, `password_id`, `group_id`, `name`, `surname`, `position`, `start_date`, `end_date`, `cancel`, `remark`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES
(1, '$corporation_id', '$company_id', '$branch_id', '$upd_user', '$upd_user', '$upd_user', 'Programmer', 'Admin', 'Temp', 'Programmer', CURDATE(), '$end_date', '1', '', CURDATE(), CURTIME(), '$upd_user', '$end_date', CURTIME(), '$upd_user');";
mysql_query($sql_1_2);

$sql_user="SELECT DISTINCT cid FROM ssup.check_in_out";
$res_user=mysql_query($sql_user);

while($rows_user=mysql_fetch_assoc($res_user)){
	$cid=$rows_user['cid'];
	$sql_1_2_1="INSERT INTO `pos_ssup`.`conf_employee` (`id`, `corporation_id`, `company_id`, `branch_id`, `employee_id`, `user_id`, `password_id`, `group_id`, `name`, `surname`, `position`, `start_date`, `end_date`, `cancel`, `remark`, `reg_date`, `reg_time`, `reg_user`, `upd_date`, `upd_time`, `upd_user`) VALUES
	(NULL, '$corporation_id', '$company_id', '$branch_id', '$cid', '$cid', '$cid', 'OpShopEmp', '$cid', '$cid', 'พนักงานขาย', CURDATE(), '$end_date', '1', '', CURDATE(), CURTIME(), '$upd_user', CURDATE(), CURTIME(), '$upd_user');";
	mysql_query($sql_1_2_1);
}

$group_id="";
$sql_1="SELECT * FROM `EMPLOYEE` WHERE `GROUP` = 'AUDIT' OR `GROUP` = 'ROS'  ";
$run1=mysql_query($sql_1,$conn);
while($rows1=mysql_fetch_assoc($run1)){
	$EMP_ID=$rows1['EMP_ID'];
	$USER=$rows1['USER'];
	$PASSWORD=$rows1['PASSWORD'];
	$GROUP=$rows1['GROUP'];
	$F_NAME=$rows1['F_NAME'];
	$S_NAME=$rows1['S_NAME'];

	if($GROUP=="ROS"){
		$group_id="OpRos";
	}

	if($GROUP=="AUDIT"){
		$group_id="OpAudit";
	}

	$sql_2="SELECT  *  FROM  pos_ssup.conf_employee WHERE employee_id='$EMP_ID' ";
	$res_2=mysql_query($sql_2);
	$num_2=mysql_num_rows($res_2);

	$end_y=date('Y')+10;
	$end_date=date('$end_y-m-d');

	if($num_2>0){
		$sql_3="
		UPDATE pos_ssup.conf_employee 
		SET 
		corporation_id='$corporation_id',
		company_id='$company_id',
		branch_id='$branch_id',
		employee_id='$EMP_ID',
		user_id='$USER',
		password_id='$PASSWORD',
		group_id='$group_id',
		name='$F_NAME',
		surname='$S_NAME',
		position='$GROUP',
		start_date=CURDATE(),
		end_date='$end_date',
		cancel='1',
		reg_date=CURDATE(),
		reg_time=CURTIME(),
		reg_user='$reg_user'
		WHERE corporation_id='$corporation_id' 
		AND  company_id ='$corporation_id' 
		AND employee_id='$EMP_ID' ";
	}else{
		$sql_3="
		INSERT IN TO pos_ssup.conf_employee 
		SET 
		corporation_id='$corporation_id',
		company_id='$company_id',
		branch_id='$branch_id',
		employee_id='$EMP_ID',
		user_id='$USER',
		password_id='$PASSWORD',
		group_id='$group_id',
		name='$F_NAME',
		surname='$S_NAME',
		position='$GROUP',
		start_date=CURDATE(),
		end_date='$end_date',
		cancel='1',
		reg_date=CURDATE(),
		reg_time=CURTIME(),
		reg_user='$reg_user' ";
	}
	$result = mysql_query($sql_3, $conn);
}

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
system("touch /home/shopsetup/dbf_to_mysql/pook9.txt");
?>
