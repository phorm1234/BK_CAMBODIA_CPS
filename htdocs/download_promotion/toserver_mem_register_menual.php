<?php
set_time_limit(0);
$doc_date=$_GET['doc_date'];

$ipserver='localhost';
$mysql_user='pos-ssup';
$mysql_pass='P0z-$$up';
$dbname="pos_ssup";



//JI
$ji_ip="10.100.59.151";
$ji_user="crm-op-kh";
$ji_pass="BxcfpffA8Y98qsDqMyQCXY4bsPQSbZnp";
$ji_db="pos_ssup_opkh";

$ji_db_sevice="service_pos_opkh";

$date_now=date("Y-m-d");
$num_day=$_GET['num_day'];





//shop
$conn_shop=mysql_connect($ipserver,$mysql_user,$mysql_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");

$conf="select * from com_doc_date ";
$r_conf=mysql_db_query($dbname,$conf,$conn_shop) or die("NONO");
$data_conf=mysql_fetch_array($r_conf);
$doc_date=$data_conf['doc_date'];
echo "Doc_date:$doc_date<br>";

$day=date("d",strtotime($doc_date));
$month=date("m",strtotime($doc_date));
$year=date("Y",strtotime($doc_date));
			


$find="select * from member_register where  reg_date between date(now())- interval $num_day day  and date(now()) ";
$run_find=mysql_db_query($dbname,$find,$conn_shop) or die("NONO");
$rows_find=mysql_num_rows($run_find);
echo "Rows newmember : $rows_find<br>";
for($i=1; $i<=$rows_find; $i++){
	$data_find=mysql_fetch_array($run_find);


	//ji
	$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");

	$add_register="
	insert into member_register
	set
		var1='$data_find[var1]',
		var2='$data_find[var2]', 
		var3='$data_find[var3]', 
		num1='$data_find[num1]',
		num2='$data_find[num2]',
		num3='$data_find[num3]',
		transfer_date='$data_find[transfer_date]', 
		customer_id_new='$data_find[customer_id_new]', 
		customer_id='$data_find[customer_id]',
		mobile_no='$data_find[mobile_no]',
		phone_home_office='$data_find[phone_home_office]',
		phone_home='$data_find[phone_home]', 
		phone_office='$data_find[phone_office]', 
		id_card='$data_find[id_card]',
		prefix='$data_find[prefix]',
		name='$data_find[name]', 
		surname='$data_find[surname]',
		mr_en='$data_find[mr_en]',
		fname_en='$data_find[fname_en]',
		lname_en='$data_find[lname_en]',
		sex='$data_find[sex]',
		nation='$data_find[nation]',
		address='$data_find[address]', 
		mu='$data_find[mu]', 
		road='$data_find[road]',
		area='$data_find[area]',
		region_id='$data_find[region_id]',
		province_id='$data_find[province_id]',
		province_name='$data_find[province_name]',
		district_id='$data_find[district_id]',
		district='$data_find[district]',
		sub_district='$data_find[sub_district]',
		sub_district_id='$data_find[sub_district_id]',
		zip='$data_find[zip]',
		card_at='$data_find[card_at]',
		start_date='$data_find[start_date]',
		end_date='$data_find[end_date]',
		birthday='$data_find[birthday]',
		brand='$data_find[brand]',
		shop='$data_find[shop]',
		doc_no='$data_find[doc_no]',
		doc_date='$data_find[doc_date]',
		email_='$data_find[email_]',
		customer_type='$data_find[customer_type]',
		reg_user='$data_find[reg_user]',
		reg_date='$data_find[reg_date]',
		reg_time='$data_find[reg_time]',
		upd_user='$data_find[upd_user]',
		upd_date='$data_find[upd_date]',
		upd_time='$data_find[upd_time]',
		application_id='$data_find[application_id]',
		customer_old='0',
		pro1='',
		pro2='',
		facebook='',
		send_company=''

	";
	mysql_select_db($ji_db);
	
	$run_add_register=mysql_query($add_register,$conn_ji);
	if($run_add_register==true){
		echo "Member Register OK<br>";
	}else{
		echo $add_register . "<br>";
	}

	
}

//add card
	//ji
$conn_ji=mysql_connect($ji_ip,$ji_user,$ji_pass) or die("no connect mysql");
mysql_query("SET character_set_results=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_connection=utf8");
$find_card="select * from member_history where  reg_date between date(now())- interval $num_day day  and date(now()) ";
mysql_select_db($dbname);
$run_find_card=mysql_query($find_card,$conn_shop);
$rows_find_card=mysql_num_rows($run_find_card);
for($x=1; $x<=$rows_find_card; $x++){

			

	$data_card=mysql_fetch_array($run_find_card);

	
		
	$add_card="
		insert member_history
		set
			customer_id_new='$data_card[customer_id_new]',
			customer_id='$data_card[customer_id]',
			id_card='$data_card[id_card]',
			seq='$data_card[seq]',
			cardtype_id='$data_card[cardtype_id]',
			brand_id='$data_card[brand_id]',
			member_no='$data_card[member_no]',
			apply_date='$data_card[apply_date]',
			expire_date='$data_card[expire_date]',
			apply_date_next='$data_card[apply_date_next]',
			re_day='$data_card[re_day]',
			bec='$data_card[bec]',
			doc_no='$data_card[doc_no]',
			doc_date='$data_card[doc_date]',
			application_id='$data_card[application_id]',
			status_active='$data_card[status_active]',
			status='$data_card[status]',
			sumquantity='$data_card[sumquantity]',
			sumamount='$data_card[sumamount]',
			sumnet='$data_card[sumnet]',
			ops='$data_card[ops]',
			point_this='$data_card[point_this]',
			age_card='$data_card[age_card]',
			status1='$data_card[status1]',
			status2='$data_card[status2]',
			status3='$data_card[status3]',
			status4='$data_card[status4]',
			status5='$data_card[status5]',
			val1='$data_card[val1]',
			val2='$data_card[val2]',
			val3='$data_card[val3]',
			val4='$data_card[val4]',
			val5='$data_card[val5]',
			reg_user='$data_card[reg_user]',
			reg_date='$data_card[reg_date]',
			reg_time='$data_card[reg_time]',
			upd_user='$data_card[upd_user]',
			upd_date='$data_card[upd_date]',
			upd_time='$data_card[upd_time]',
			time_up='$data_card[time_up]'
	";
	mysql_select_db($ji_db);
	
	$run_add_card=mysql_query($add_card,$conn_ji);
	if($run_add_card==true){
		echo "Member History OK<br>";
	}else{
		echo $add_card . "<br>";
	}
}



?>