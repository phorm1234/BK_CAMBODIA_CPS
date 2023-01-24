// JavaScript Document
function reset_create_branch()
{
	$("#slt_brand").focus();
	$("#txt_branch_id").attr('readonly', false);
	$("#slt_brand").attr('disabled', false);
	$("#slt_branch_tp option:selected").removeAttr("selected");
}
function get_branch_detail()
{
	var txt_branch_id = $("#txt_branch_id").val();
	var slt_brand = $("#slt_brand").val();
	if(txt_branch_id != "")
	{
		var ftype = "get_branch_detail";
		$.ajax({
			  type: "POST",
			  url: "funcPHP.php",
			  data: { ftype: ftype , txt_branch_id: txt_branch_id , slt_brand: slt_brand }, 
			  success: function(msg){
				if(msg != "norecord")
				{
					var record = msg.split("-next-");
					$("#txt_branch_name").val(record[0]);
					$("#slt_branch_tp option[value="+record[1]+"]").attr('selected','selected');
					$("#txt_phone").val(record[2]);
					$("#txt_mobile").val(record[3]);
					$("#txt_address").val(record[4]);
					$("#txt_road").val(record[5]);
					$("#txt_branch_id").attr('readonly', true);
					$("#slt_brand").attr('disabled', true);
				}
				else
				{
					$("#txt_branch_name").val("");
					$("#slt_branch_tp option:selected").removeAttr("selected");
					$("#txt_phone").val("");
					$("#txt_mobile").val("");
					$("#txt_address").val("");
					$("#txt_road").val("");
					$("#txt_branch_id").attr('readonly', false);
					$("#slt_brand").attr('disabled', false);
				}
			}
		});
	}
}
function checkFill_comp()
{
	if($("#txt_ip").val() == "")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_ip").focus();
		return false;
	}
	else
	{
		$("#txt_shop_id").attr('disabled', false);
		$("#txt_ip").attr('disabled', false);
		$("#txt_no").attr('disabled', false);
		return true;
	}
}
function reset_comp()
{
	$("#txt_pos_id").val("");
	$("#txt_shop_id").attr('disabled', false);
	$("#txt_ip").attr('disabled', false);
	$("#txt_no").attr('disabled', false);
	$("#txt_thermal option:selected").removeAttr("selected");
	$("#txt_cash option:selected").removeAttr("selected");
	$("#txt_shop_id").focus();
}
function get_comp()
{
	var txt_ip = $("#txt_ip").val();
	var txt_no = $("#txt_no").val();
	var txt_shop_id = $("#txt_shop_id").val();
	if(txt_ip != "")
	{
		var ftype = "get_comp";
		$.ajax({
			  type: "POST",
			  url: "funcPHP.php",
			  data: { ftype: ftype , txt_ip: txt_ip , txt_no: txt_no , txt_shop_id: txt_shop_id }, 
			  success: function(msg){
				if(msg != "norecord")
				{
					var record = msg.split("-next-");
					$("#txt_pos_id").val(record[0]);
					$("#txt_thermal option[value="+record[1]+"]").attr('selected','selected');
					$("#txt_cash option[value="+record[2]+"]").attr('selected','selected');
					
					$("#hdn_shop_id").val($("#txt_shop_id").val());
					$("#hdn_ip").val($("#txt_ip").val());
					$("#hdn_no").val($("#txt_no").val());
					
					$("#txt_shop_id").attr('disabled', true);
					$("#txt_ip").attr('disabled', true);
					$("#txt_no").attr('disabled', true);
				}
				else
				{
					$("#txt_pos_id").val("");
					$("#txt_shop_id").attr('disabled', false);
					$("#txt_ip").attr('disabled', false);
					$("#txt_no").attr('disabled', false);
					$("#txt_thermal option:selected").removeAttr("selected");
					$("#txt_cash option:selected").removeAttr("selected");
					
				}
			}
		});
	}
}
function checkFill_create_branch()
{
	if($("#txt_branch_id").val() == "")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_branch_id").focus();
		return false;
	}
	else if($("#txt_branch_name").val() == "")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_branch_name").focus();
		return false;
	}
	else
	{
		return true;
	}
}
function checkFill_inout()
{
	if($("#txt_shop_id").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_shop_id").focus();
		return false;
	}
	else if($("#txt_date").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_date").focus();
		return false;
	}
	else if($("#txt_time").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_time").focus();
		return false;
	}
	else 
	{
		var all = "<?php echo $all;?>";
		var arr_all = all.split("*");
		
		for(var i=0;i<arr_all.length-1;i++){
			//alert($("#txt_date").val()+"////"+arr_all[i]);
			if($("#txt_date").val() == arr_all[i])
			{
				var conf = confirm('มีการลงเวลาวันนี้แล้ว คุณต้องการบันทึกซ้ำหรือไม่ ?');
				if(conf)
				{
					$("#txt_type").val("Y");
					return true;
				}
				else
				{
					return false;
				}
					
			}
		}
		//return true;
	}
}
function checkFill_user()
{
	if($("#txt_shop_id").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_shop_id").focus();
		return false;
	}
	else if($("#txt_sdate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_sdate").focus();
		return false;
	}
	else if($("#txt_edate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_edate").focus();
		return false;
	}
	else 
	{
		if($('#txt_sdate').datepicker("getDate") > $('#txt_edate').datepicker("getDate"))
		{ 
			alert("วันที่เริ่มต้นต้องน้อยกว่าวันที่สิ้นสุด");
			$("#txt_sdate").focus();
			return false;
		}
		else return true;
	}
}
function CompareDateTime(strDate1,strDate2)
{
	sdate1 = new Date(strDate1);
	sdate2 = new Date(strDate2);
	if (Date.parse(sdate1) >= Date.parse(sdate2)) {
		return true;
	}
	else
	{
		return false;
	}

}
function checkFill_shop()
{
	if($("#txt_shop_id").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_shop_id").focus();
		return false;
	}
	else 
	{
		return true;
	}
}
function get_code()
{
	var slt_code = $("#slt_code").val();
	var ftype = "get_code";
	$.ajax({
		  type: "POST",
		  url: "funcPHP.php",
		  data: { ftype: ftype , slt_code: slt_code}, 
		  success: function(msg){
			if(msg != "norecord")
			{
				var record = msg.split("-next-");
				/*$("#slt_default option:selected").removeAttr("selected");
				$("#slt_condition option:selected").removeAttr("selected");
				alert(record[0]+"/"+record[1]);
				$("#slt_default option[value='"+record[0]+"']").attr('selected','selected');
				$("#slt_condition option[value='"+record[1]+"']").attr('selected','selected');*/
				$("#slt_default").val(record[0]);
				$("#slt_condition").val(record[1]);
				
				$("#txt_sdate").val(record[2]);
				$("#txt_edate").val(record[3]);
				
				$("#txt_stime").val(record[4]);
				$("#txt_etime").val(record[5]);
			}
		}
	});
}
