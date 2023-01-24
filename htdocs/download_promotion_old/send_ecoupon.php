<?php

		$search_cr="OP" . $shop;
		mysql_db_query($db_jinet_center,"delete from `com_ecoupon` where op = '$search_cr' ",$c53_2);

		
		mysql_db_query($db_jinet_center,"delete from `com_ecoupon` where op = '$search_cr' ",$c53_2);


		$sql_ecoupon="select * from com_ecoupon where op = '$search_cr' ";
		//echo $sql_ecoupon . "<br>";
		$r_ecoupon=mysql_db_query($db_jinet_center,$sql_ecoupon,$c53_2);
		$rows_ecoupon=mysql_num_rows($r_ecoupon);
		
		for($iecoupon=1; $iecoupon<=$rows_ecoupon; $iecoupon++){
			$data_ecoupon=mysql_fetch_array($r_ecoupon);
			$up_ecoupon="
				insert into
				 com_ecoupon
				set
				corporation_id='$data_ecoupon[corporation_id]',
				company_id='$data_ecoupon[company_id]',
				dept_id='$data_ecoupon[dept_id]',
				employee_id='$data_ecoupon[employee_id]',
				name='$data_ecoupon[name]',
				surname='$data_ecoupon[surname]',
				start_date='$data_ecoupon[start_date]',
				end_date='$data_ecoupon[end_date]',
				op='$data_ecoupon[op]',
				cps='$data_ecoupon[cps]',
				gnc='$data_ecoupon[gnc]',
				amount_op='$data_ecoupon[amount_op]',
				amount_cps='$data_ecoupon[amount_cps]',
				amount_gnc='$data_ecoupon[amount_gnc]',
				percent_discount='$data_ecoupon[percent_discount]',
				level='$data_ecoupon[level]',
				cancel='$data_ecoupon[cancel]',
				edit_date='$data_ecoupon[edit_date]',
				reg_date=date(now()),
				reg_time=time(now()),
				reg_user='IT',
				upd_date=date(now()),
				upd_time=time(now()),
				upd_user='IT'
			";

			
			$rup_ecoupon=mysql_db_query($dbname,$up_ecoupon,$conn_shop);
		}//loop

?>