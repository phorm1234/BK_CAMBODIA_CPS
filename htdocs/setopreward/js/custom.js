// JavaScript Document
function check_blank()
{
	if($("#txt_rw_code").val()!="" && $("#txt_rw_desc").val()!="" && $("#txt_rw_prod").val()!="" && $("#txt_rw_shop").val()!="" && $("#txt_rw_point").val()!="" && $("#txt_rw_amnt").val()!="" && $("#txt_rw_sdate").val()!="" && $("#txt_rw_edate").val()!="" && $("#txt_prod_name").val()!="" )
	{
		var conf = confirm("ยืนยันการบันทึกข้อมูล");
		if(conf)
		{
			document.getElementById("reward_submit").submit();
		}
	}
	else if($("#txt_rw_code").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_code").focus();
		return false;
	}
	else if($("#txt_rw_prod").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_prod").focus();
		return false;
	}
	else if($("#txt_rw_desc").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_desc").focus();
		return false;
	}
	else if($("#txt_rw_point").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_point").focus();
		return false;
	}
	else if($("#txt_rw_amnt").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_amnt").focus();
		return false;
	}
	else if($("#txt_rw_sdate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_sdate").focus();
		return false;
	}
	else if($("#txt_rw_edate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_rw_edate").focus();
		return false;
	}/*
	else if($("#txt_prod_name").val()=="")
	{
		alert("ไม่พบข้อมูลสินค้า");
		$("#txt_rw_prod").focus();
		return false;
	}*/
	else
	{
		alert("false");
		return false;
	}
}

function check_field()
{
	var txt_rw_code = $("#txt_rw_code").val();
	var txt_rw_code = $("#txt_rw_code").val();
	var ftype = "check_field";
	if( txt_rw_code != "")
	{
		$.ajax({
		  type: "POST",
		  url: "funcPHP.php",
		  data: { ftype: ftype , txt_rw_code: txt_rw_code }, 
		  success: function(msg){
			if(msg != "norecord")
			{
				var record = msg.split("-next-");
				$("#status_id").html("แก้ไขข้อมูล");
				$("#txt_rw_code").val(record[0]);
				$("#txt_rw_prod").val(record[1]);
				$("#txt_rw_desc").val(record[2]);
				$("#txt_rw_point").val(record[3]);
				$("#txt_rw_amnt").val(record[4]);
				$("#txt_rw_sdate").val(record[5]);
				$("#txt_rw_edate").val(record[6]);
				$("#txt_rw_usdate").val(record[7]);
				$("#txt_rw_uedate").val(record[8]);
				$("#txt_rw_code").attr('readonly', true);
				$("#btn_del").css("display" , "inline");
				//check_product('txt_rw_prod');
			}
			else
			{
				$("#status_id").html("เพิ่มข้อมูลใหม่");
				$("#txt_rw_prod").val("");
				$("#txt_rw_desc").val("");
				$("#txt_rw_point").val("");
				$("#txt_rw_amnt").val("");
				$("#txt_rw_sdate").val("");
				$("#txt_rw_edate").val("");
				$("#txt_rw_code").attr('readonly', false);      
				$("#btn_del").css("display" , "none");
			}
		  }
		});
	}
}
function del_rwd()
{
	var txt_rw_code = $("#txt_rw_code").val();
	var ftype = "del_rwd";
	if( txt_rw_code != "")
	{
		var conf = confirm("ยืนยันการลบข้อมูล");
		if(conf)
		{
			$.ajax({
			  type: "POST",
			  url: "funcPHP.php",
			  data: { ftype: ftype , txt_rw_code: txt_rw_code }, 
			  success: function(msg){
				alert("ลบข้อมูลเรียบร้อย");
				window.location = window.location;
			  }
			});
		}
	}
}
function reset_rwd()
{
	$("#status_id").html("เพิ่มข้อมูลใหม่");
	$("#txt_rw_code").val("");
	$("#txt_rw_prod").val("");
	$("#txt_rw_desc").val("");
	$("#txt_rw_point").val("");
	$("#txt_rw_amnt").val("");
	$("#txt_rw_sdate").val("");
	$("#txt_rw_edate").val("");
	$("#txt_rw_code").attr('readonly', false);
	$("#btn_del").css("display" , "none");
	$("#txt_rw_code").focus();
}
function add_prog()
{
	var txt_prog_name = $("#txt_prog_name").val();
	var txt_prog_link = $("#txt_prog_link").val();
	var txt_prog_id = $("#txt_prog_id").val();
	var ftype = "add_prog";
	if(txt_prog_name != "" && txt_prog_link != "")
	{
		$.ajax({
		  type: "POST",
		  url: "../funcPHP.php",
		  data: { ftype: ftype , txt_prog_name: txt_prog_name , txt_prog_link : txt_prog_link , txt_prog_id: txt_prog_id}, 
		  success: function(msg){
			window.location = window.location;
		  }
		});
	}
}
function del_prog(prog_id)
{
	var ftype = "del_prog";
	var conf = confirm("ยืนยันการลบข้อมูล");
	if(conf)
	{
		$.ajax({
		  type: "POST",
		  url: "../funcPHP.php",
		  data: { ftype: ftype , prog_id: prog_id }, 
		  success: function(msg){
			window.location = window.location;
		  }
		});
	}
}
function check_prog()
{
	var txt_prog_name = $("#txt_prog_name").val();
	var ftype = "check_prog";
	if( txt_prog_name != "")
	{
		$.ajax({
		  type: "POST",
		  url: "../funcPHP.php",
		  data: { ftype: ftype , txt_prog_name: txt_prog_name }, 
		  success: function(msg){
			if(msg != "norecord")
			{
				var record = msg.split("-next-");
				$("#txt_prog_name").val(record[0]);
				$("#txt_prog_link").val(record[1]);
				$("#txt_prog_id").val(record[2]);
			}
			else
			{
				$("#txt_prog_name").val("");
				$("#txt_prog_link").val("");
			}
		  }
		});
	}
}

function check_avt_blank()
{
	if($("#txt_avt_code").val()!="" && $("#txt_avt_name").val()!="" && $("#txt_avt_desc1").val()!="" && $("#txt_avt_point").val()!="" && $("#txt_avt_amnt").val()!="" && $("#txt_avt_amnt").val()!="" && $("#txt_avt_sdate").val()!="" && $("#txt_avt_edate").val()!="")
	{
		var conf = confirm("ยืนยันการบันทึกข้อมูล");
		if(conf)
		{
			document.getElementById("reward_submit").submit();
		}
	}
	else if($("#txt_avt_code").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_code").focus();
		return false;
	}
	else if($("#txt_avt_name").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_name").focus();
		return false;
	}
	else if($("#txt_avt_desc1").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_desc1").focus();
		return false;
	}
	else if($("#txt_avt_point").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_point").focus();
		return false;
	}
	else if($("#txt_avt_amnt").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_amnt").focus();
		return false;
	}
	else if($("#txt_avt_sdate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_sdate").focus();
		return false;
	}
	else if($("#txt_avt_edate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_avt_edate").focus();
		return false;
	}
	else
	{
		alert("false");
		return false;
	}
}
function check_avt_field()
{
	var txt_avt_code = $("#txt_avt_code").val();
	var ftype = "check_avt_field";
	if( txt_avt_code != "")
	{
		$.ajax({
		  type: "POST",
		  url: "funcPHP.php",
		  data: { ftype: ftype , txt_avt_code: txt_avt_code }, 
		  success: function(msg){
			if(msg != "norecord")
			{
				var table = msg.split("-detail-");
				var head = table[0].split("-next-");
				$("#status_id").html("แก้ไขข้อมูล");
				$("#txt_avt_code").val(head[0]);
				$("#txt_avt_name").val(head[1])
				$("#txt_avt_point").val(head[2]);
				$("#txt_avt_amnt").val(head[3]);
				$("#txt_avt_sdate").val(head[4]);
				$("#txt_avt_edate").val(head[5]);
				
				var detail = table[1].split("-next-");
				for(var i=0 ; i<detail.length ; i++)
				{
					var count = i+1;
					$("#txt_avt_desc"+count).val(detail[i]);
				}				
				
				$("#txt_avt_code").attr('readonly', true);
				$("#btn_del").css("display" , "inline");
			}
			else
			{
				$("#status_id").html("เพิ่มข้อมูลใหม่");
				$("#txt_avt_name").val("")
				$("#txt_avt_point").val("");
				$("#txt_avt_amnt").val("");
				$("#txt_avt_sdate").val("");
				$("#txt_avt_edate").val("");
				
				for(var i=0 ; i<5 ; i++)
				{
					var count = i+1;
					$("#txt_avt_desc"+count).val("");
				}				
				
				$("#txt_avt_code").attr('readonly', false);
				$("#btn_del").css("display" , "none");
			}
		  }
		});
	}
}

function reset_avt()
{
	$("#status_id").html("เพิ่มข้อมูลใหม่");
	$("#txt_avt_code").val("");
	$("#txt_avt_name").val("")
	$("#txt_avt_point").val("");
	$("#txt_avt_amnt").val("");
	$("#txt_avt_sdate").val("");
	$("#txt_avt_edate").val("");
	
	for(var i=0 ; i<5 ; i++)
	{
		var count = i+1;
		$("#txt_avt_desc"+count).val("");
	}				
	
	$("#txt_avt_code").attr('readonly', false);
	$("#btn_del").css("display" , "none");	
	$("#txt_avt_code").focus();
}
function del_avt()
{
	var txt_avt_code = $("#txt_avt_code").val();
	var ftype = "del_avt";
	var conf = confirm("ยืนยันการลบข้อมูล");
	if(conf)
	{
		$.ajax({
		  type: "POST",
		  url: "funcPHP.php",
		  data: { ftype: ftype , txt_avt_code: txt_avt_code }, 
		  success: function(msg){
			window.location = window.location;
		  }
		});
	}
}
function check_product(txt_prod)
{
	var text = txt_prod;
	var txt_prod = $("#"+text).val();
	var ftype = "check_product";
	if(txt_prod != "")
	{
		$.ajax({
			type: "POST",
		  	url: "funcPHP.php",
		  	data: { ftype: ftype , txt_prod: txt_prod }, 
		  	success: function(msg){
			  	if(msg != "norecord")
			  	{
				  	$("#txt_prod_name").val(msg);
			  	}	
			  	else
			  	{
					alert("ไม่พบข้อมูลสินค้า");
					$("#"+text).focus();
					$("#txt_prod_name").val("");
				}
		  	}
		});
	}
	else
	{
		$("#txt_prod_name").val("");
	}
}
function check_p_field()
{
	var txt_prod = $("#txt_p_prod").val();
	var ftype = "check_p_field";
	if( txt_prod != "")
	{
		check_product('txt_p_prod');
		$.ajax({
		  type: "POST",
		  url: "funcPHP.php",
		  data: { ftype: ftype , txt_prod: txt_prod }, 
		  success: function(msg){
			if(msg != "norecord")
			{
				var record = msg.split("-next-");
				$("#status_id").html("แก้ไขข้อมูล");
				$("#txt_p_prod").val(record[0]);
				$("#slt_doc_no option[value="+record[1]+"]").attr('selected','selected');
				$("#txt_p_sdate").val(record[2]);
				$("#txt_p_edate").val(record[3]);
				get_doc_status(record[4]);
				$("#txt_p_prod").attr('readonly', true);
				$("#slt_doc_no").attr('disabled', true);
				$("#slt_doc_status").attr('disabled', true);
				$("#btn_del").css("display" , "inline");				
			}
			else
			{
				$("#status_id").html("เพิ่มข้อมูลใหม่");
				//$("#slt_doc_no").val(record[1]);
				$("#txt_p_sdate").val("");
				$("#txt_p_edate").val("");
				//$("#slt_doc_status").val(record[4]);
				$("#txt_p_prod").attr('readonly', false);
				$("#slt_doc_no").attr('disabled', false);
				$("#slt_doc_status").attr('disabled', false);
				$("#slt_doc_no").val('');
				get_doc_status();
				$("#btn_del").css("display" , "none");
				return false;
			}
		  }
		});
	}
}
function get_doc_status(status)
{
	var slt_doc_no = $("#slt_doc_no").val();
	var ftype = "get_doc_status";
	if(slt_doc_no != "")
	{
		$.ajax({
			type: "POST",
		  	url: "funcPHP.php",
		  	data: { ftype: ftype , slt_doc_no: slt_doc_no }, 
		  	success: function(msg){
			  	if(msg != "norecord")
			  	{
					$("#slt_doc_status").html(msg);
					$('#slt_doc_status option[value="'+status+'"]').attr('selected','selected');
			  	}
				else
				{
					msg = "<option value=''>สถานะเอกสาร</option>";
					$("#slt_doc_status").html(msg);
				}
		  	}
		});
	}
	else
	{
		msg = "<option value=''>สถานะเอกสาร</option>";
		$("#slt_doc_status").html(msg);
	}
}
function check_p_blank()
{
	if($("#txt_p_prod").val()!="" && $("#slt_doc_no").val()!="" && $("#slt_doc_status").val()!="" && $("#txt_p_sdate").val()!="" && $("#txt_p_edate").val()!="" && $("#txt_prod_name").val()!="" )
	{
		var conf = confirm("ยืนยันการบันทึกข้อมูล");
		if(conf)
		{
			document.getElementById("reward_submit").submit();
		}
	}
	else if($("#txt_p_prod").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_p_prod").focus();
		return false;
	}
	else if($("#slt_doc_no").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#slt_doc_no").focus();
		return false;
	}
	else if($("#slt_doc_status").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#slt_doc_status").focus();
		return false;
	}
	else if($("#txt_p_sdate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_p_sdate").focus();
		return false;
	}
	else if($("#txt_p_edate").val()=="")
	{
		alert("กรุณากรอกข้อมูลให้ครบ");
		$("#txt_p_edate").focus();
		return false;
	}/*
	else if($("#txt_prod_name").val()=="")
	{
		alert("ไม่พบข้อมูลสินค้า");
		$("#txt_p_prod").focus();
		return false;
	}*/
	else
	{
		alert("false");
		return false;
	}
}
function reset_p()
{
	$("#status_id").html("เพิ่มข้อมูลใหม่");
	$("#txt_p_prod").focus();
	$("#slt_doc_status").html("<option value=''>สถานะเอกสาร</option>");
	$('#slt_doc_no option:selected').removeAttr("selected");
	$('#slt_doc_status option:selected').removeAttr("selected");
	$("#txt_p_prod").attr('readonly', false);
	$("#slt_doc_no").attr('disabled', false);
	$("#slt_doc_status").attr('disabled', false);
	$("#btn_del").css("display" , "none");
}
function del_p()
{
	var txt_p_prod = $("#txt_p_prod").val();
	var ftype = "del_p";
	if( txt_p_prod != "")
	{
		var conf = confirm("ยืนยันการลบข้อมูล");
		if(conf)
		{
			$.ajax({
			  type: "POST",
			  url: "funcPHP.php",
			  data: { ftype: ftype , txt_p_prod: txt_p_prod }, 
			  success: function(msg){
				alert("ลบข้อมูลเรียบร้อย");
				window.location = window.location;
			  }
			});
		}
	}
}