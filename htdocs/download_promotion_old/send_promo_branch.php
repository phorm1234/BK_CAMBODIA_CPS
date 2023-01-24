<?php


		$sql_branch="select * from promo_branch   where promo_code='$promo_code'   order by promo_code";
		$r_branch=mysql_db_query($db_jinet_center,$sql_branch,$c53_2);
		$rows_branch=mysql_num_rows($r_branch);

		for($ibranch=1; $ibranch<=$rows_branch; $ibranch++){
			$data_branch=mysql_fetch_array($r_branch);
			$up_branch="
				insert into
				 promo_branch
				set
				corporation_id='$data_branch[corporation_id]',
				company_id='$data_branch[company_id]',
				promo_code='$data_branch[promo_code]',
				branch_id='$data_branch[branch_id]',
				branch_tp='$data_branch[branch_tp]',
				except='$data_branch[except]',
				start_date='$data_branch[start_date]',
				end_date='$data_branch[end_date]',
				reg_date=date(now()),
				reg_time=time(now()),
				reg_user='IT',
				upd_date=date(now()),
				upd_time=time(now()),
				upd_user='IT'
			";
			$rup_branch=mysql_db_query($dbname,$up_branch,$conn_shop);
		}//loop



		mysql_db_query($dbname,"delete from promo_permission where  promo_code='$promo_code'  ",$conn_shop);

		$sql_permission="select * from promo_permission  where promo_code='$promo_code'   order by promo_code";
		$r_permission=mysql_db_query($db_jinet_center,$sql_permission,$c53_2);
		$rows_permission=mysql_num_rows($r_permission);

		for($ipermission=1; $ipermission<=$rows_permission; $ipermission++){
			$data_permission=mysql_fetch_array($r_permission);
			$up_permission="
				insert into
				 promo_permission
				set
				promo_code='$data_permission[promo_code]',
				start_date='$data_permission[start_date]',
				end_date='$data_permission[end_date]',
				play_member='$data_permission[play_member]'
			";
			$rup_permission=mysql_db_query($dbname,$up_permission,$conn_shop);
		}//loop




		mysql_db_query($dbname,"delete from promo_barcode where  promo_code='$promo_code'  ",$conn_shop);

		$sql_barcode="select * from promo_barcode  where promo_code='$promo_code'  ";
		$r_barcode=mysql_db_query($db_jinet_center,$sql_barcode,$c53_2);
		$data_barcode=mysql_fetch_array($r_barcode);
		$up_barcode="
			insert into
			 promo_barcode
			set
			corporation_id='$data_barcode[corporation_id]',
			company_id='$data_barcode[company_id]',
			promo_code='$data_barcode[promo_code]',
			barcode='$data_barcode[barcode]',
			remark='$data_barcode[remark]',
			reg_date='$data_barcode[reg_date]',
			reg_time='$data_barcode[reg_time]',
			reg_user='$data_barcode[reg_user]',
			upd_date='$data_barcode[upd_date]',
			upd_time='$data_barcode[upd_time]'
		";
		$rup_barcode=mysql_db_query($dbname,$up_barcode,$conn_shop);
?>