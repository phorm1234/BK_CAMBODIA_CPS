function check_password(passw){
	if(passw==""){
		jAlert('คุณยังไม่ป้อนรหัสผ่าน', 'แจ้งเตือน');
		$("#passw").focus();
	}else{
		$("#showname").html("<table width='100%'><tr><td align='left'><img src='/stock/img/ajax-loader.gif' border='0'></td></tr></table>");
		$.ajax({type: "POST",url: "/stock/checkstockrandom/checkpassword",data:{passw:passw},success:
			function(data){
				if(data=="n"){
					jAlert('คุณป้อนรหัสผ่านไม่ถูกต้อง', 'แจ้งเตือน');
					$("#showname").html("");
					$("#button_process").html("");
				}else{
					var html="<table border=\"0\" width=\"100%\"><tr><td width=\"8%\"><a class=\"fg-button ui-state-default ui-corner-all\" onClick=\"return insert_trn_product_random()\";>ตกลง</a></td><td colspan=\"3\"><a class=\"fg-button ui-state-default ui-corner-all\" onClick=\"return cancel_process()\";>ยกเลิก</a></td></tr></table>";
					$("#showname").html(data);
					$("#button_process").html(html);
					$("#passw").attr('disabled', true);
				}
			}
		});
	}
}

function insert_trn_product_random(){
	$("#view_random_stock").html("<table width='100%'><tr><td align='center'><img src='/stock/img/ajax-loader.gif' border='0'><br>Please Wait...</td></tr></table>");
	$.ajax({type: "POST",url: "/stock/checkstockrandom/inserttempproductrandom",data:{},success:
		function(data){
			if(data=="y"){
				view_random_stock();
			}
		}
	});
}

function view_random_stock(){
	$("#view_random_stock").html("<table width='100%'><tr><td align='center'><img src='/stock/img/ajax-loader.gif' border='0'><br>Please Wait...</td></tr></table>");
	$.ajax({type: "POST",url: "/stock/checkstockrandom/viewrandomstock",data:{},success:
		function(data){
			$("#view_random_stock").html(data);
		}
	});
}

function print_random_stock(){
	var form=$('form#frm_rand_stock').serialize();
	$.ajax({type: "POST",url: "/stock/checkstockrandom/printrandomstock",data:form,success:
		function(data){
			if(data=="y"){
				print_report();
			}
		}
	});
}

function cancel_process(){
	jConfirm('ต้องการยกเลิก?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$("#passw").val("");
			$("#passw").removeAttr('disabled');
			$("#showname").html("");
			$("#button_process").html("");
			$("#passw").focus();
			$("#view_random_stock").html("");
		}
	});
}

function print_report(){
	var page="printreport";
	window.open(page, '_blank');
}