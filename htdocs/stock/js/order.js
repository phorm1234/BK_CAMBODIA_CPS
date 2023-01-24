$(document).ready( function() {
	vieworder("p");
	function msg_box(){
		jConfirm('Can you confirm this?', 'Confirmation Dialog', function(r) {
			jAlert('Confirmed: ' + r, 'Confirmation Results');
		});
	}
	
	$("#prompt_button").click( function() {
		jPrompt('Type something:', 'Prefilled value', 'Prompt Dialog', function(r) {
			if( r ) alert('You entered ' + r);
		});
	});
	
	$("#alert_button_with_html").click( function() {
		jAlert('You can use HTML, such as <strong>bold</strong>, <em>italics</em>, and <u>underline</u>!');
	});
	
	$(".alert_style_example").click( function() {
		$.alerts.dialogClass = $(this).attr('id'); // set custom style class
		jAlert('This is the custom class called &ldquo;style_1&rdquo;', 'Custom Styles', function() {
			$.alerts.dialogClass = null; // reset to default
		});
	});
});

$(function() {
	$( "#dialog_type_product" ).dialog({
		autoOpen: false,
		height: 250,
		width: 400,
		modal: true,
		buttons: {
			"เลือก (F2)": function() {
				select_status_no();
			},
			"ออก (Esc)": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	
	$( "#dialog_product" ).dialog({
		autoOpen: false,
		height: 300,
		width: 550,
		modal: true,
		buttons: {
			"เลือก (F2)": function() {
				inputinv();
			},
			"ออก (Esc)": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	
	$( "#create_shelf" )
		.button()
		
	$(".fg-button")
	.hover(
		function(){ 
			$(this).addClass("ui-state-hover"); 
		},
		function(){ 
			$(this).removeClass("ui-state-hover"); 
		}
	)
	.mousedown(function(){
			$(this).parents('.fg-buttonset-single:first').find(".fg-button.ui-state-active").removeClass("ui-state-active");
			if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ $(this).removeClass("ui-state-active"); }
			else { $(this).addClass("ui-state-active"); }	
	})
	.mouseup(function(){
		if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
			$(this).removeClass("ui-state-active");
		}
	});
		
});

function vieworder(chk){
	//var doc_no=$("#invoice").val();
	//var status_no=$("#status_no").val();
	$.ajax({type: "POST",url: "/order/order/getproductorder",data:{chk:chk},success:
	function(data){
		if(chk=="s"){
			/*$.ajax({type: "POST",url: "/stock/stock/checkinv",data:{doc_no:doc_no},success:
			function(data){
				if(data=="n"){
					var txt="ไม่พบเลขที่สินค้าเข้า : "+doc_no;
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#invoice").focus();
					});
				}else if(data=="w"){
					jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'แจ้งเตือน', function() {
						$("#invoice").focus();
					});
				}
			}});*/
		}
		$("#viewproductorder").html(data);
	}}); 
}

function viewiq(chk){
	$.ajax({type: "POST",url: "/order/order/getproductiq",data:{chk:chk},success:
	function(data){
		if(chk=="s"){
			
		}
		$("#viewproductiq").html(data);
	}}); 
}

function check_product(){
	var product_id=$("#product_id").val();
	var status_no=$("#status_no").val();
	var doc_tp=$("#doc_tp").val();
	if(product_id==""){
		jAlert('คุณยังไม่ป้อนรหัสสินค้า', 'แจ้งเตือน', function() {
			$("#product_id").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/order/order/checkproduct",data:{product_id:product_id,status_no:status_no,doc_tp:doc_tp},success:
			function(data){
				if(data=="y"){
					$("#qty").focus();
				}else if(data=="w"){
					var txt="ไม่สามารถสั่งรายการสินค้านี้ได้ : "+product_id;
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#product_id").focus();
					});
				}else if(data=="t"){
					var txt="รายการสินค้ารหัส : "+product_id+" นี้ได้สั่งไปแล้ว ";
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#product_id").focus();
					});
				}else if(data=="q"){
					var txt="วงเงินสั่งรายการสินค้าไม่เพียงพอ ";
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#product_id").focus();
					});
				}else{
					var check_status_no=$("#status_no").val();
					if(check_status_no==71){
						var txt="ไม่พบรหัสสินค้ารหัสสินค้า Tester : "+product_id;
					}else{
						var txt="ไม่พบรหัสสินค้ารหัสสินค้า : "+product_id;
					}
					
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#product_id").focus();
					});
				}
			}
		});
	}
}

function chk_product(product_id){
	if(product_id==""){
		jAlert('คุณยังไม่ป้อนรหัสสินค้า', 'แจ้งเตือน', function() {
			$("#product_id").focus();
		});
	}else{
		$("#qty").focus();
	}
}

function insert_tdiary_or(){
	var chk_qty=$("#qty").val();
	if(chk_qty==""){
		jAlert('คุณยังไม่กรอกจำนวน!', 'แจ้งเตือน', function() {
			$("#qty").focus();
		});
	}else{
		if(chk_qty>0 && chk_qty<100){
			var form=$('form#frm_order').serialize();
			$.ajax({type: "POST",url: "/order/order/inserttdiaryor",data:form,success:
				function(data){
					vieworder("s");
					$("#product_id").val("");
					$("#qty").val("");
					$("#product_id").focus();
				}
			});
		}else{
			jAlert('ต้องสั่งสินค้ามากกว่า 0 ชิ้นและไม่สามารถสั่งสินค้าได้เกิน 99 ชิ้น', 'แจ้งเตือน', function() {
				$("#qty").focus();
			});
		}
	}
}

function order_cancel(){
	jConfirm('ยืนยันการยกเลิก', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			var tbl="trn_tdiary2_or";
			$.ajax({type: "POST",url: "/order/order/deleteordertemp",data:{tbl:tbl},success:
			function(data){
				if(data=="y"){
					vieworder("s");				
				}else{
					
				}
			}});
		}
	});
}

function iq_cancel(){
	jConfirm('ยืนยันการยกเลิก', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			var tbl="trn_tdiary2_iq";
			$.ajax({type: "POST",url: "/order/order/deleteordertemp",data:{tbl:tbl},success:
			function(data){
				if(data=="y"){
					viewiq("s");
					$("#doc_remark").val("");
					$("#inv").val("");
				}else{
					
				}
			}});
		}
	});
}

function tempor2diaryor(){
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	var doc_remark=$("#doc_remark").val();
	jConfirm('ยืนยันการบันทึก', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/order/order/tempor2diaryor",data:{doc_tp:doc_tp,status_no:status_no,doc_remark:doc_remark},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เลขที่เอกสาร : "+data.doc_no;
					jAlert(txt, 'แจ้งเตือน', function() {
						getquota();
						vieworder("s");	
					});			
				}else if(data.status=="n"){
					jAlert('วงเงินสั่งสินค้าไม่เพียงพอ!', 'แจ้งเตือน', function() {
						return false;
					});
				}else{
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'แจ้งเตือน', function() {
						return false;
					});
				}
			}});
		}
	});
}

function tempor2diaryiq(){
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#doc_status").val();
	var doc_remark=$("#doc_remark").val();
	var inv=$("#inv").val();
	jConfirm('ยืนยันการบันทึก', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/order/order/tempor2diaryiq",data:{doc_tp:doc_tp,status_no:status_no,doc_remark:doc_remark,inv:inv},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เลขที่เอกสาร : "+data.doc_no;
					jAlert(txt, 'แจ้งเตือน', function() {
						var page="printiq?doc_no="+data.doc_no;
						window.open(page, '_blank');
						viewiq("s");
						$("#doc_remark").val("");
						$("#inv").val("");
					});			
				}else if(data.status=="n"){
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'แจ้งเตือน', function() {
						return false;
					});
				}else{
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'แจ้งเตือน', function() {
						return false;
					});
				}
			}});
		}
	});
}

function getquota(){
	$.ajax({type: "POST",url: "/order/order/getquota",data:{},dataType: 'json',success:
	function(data){
		$("#balance_amount").val(data.month_amount);
		$("#month_order").val(data.month_order);
		$("#balance_amount").val(data.balance_amount);
	}});
}

function search_type_product(){
	$.ajax({type: "POST",url: "/order/order/checkdocnodiaryor",data:{},success:
		function(data){
			if(data=="y"){
				jAlert('คุณต้องลบรายการสินค้าก่อน!', 'แจ้งเตือน', function() {
					return false;
				});
			}else{
				var doc_tp=$("#doc_tp").val();
				$.ajax({type: "POST",url: "/order/order/gettypeproduct",data:{doc_tp:doc_tp},success:
					function(data){
						$("#dialog_type_product").html(data);
						$("#dialog_type_product").dialog( "open" );
					}
				});
			}
		}
	});
}

function search_product(){
	$("#dialog_product").dialog( "open" );
}

function select_status_no(){
	var getdata = $("input[name='status_no']:checked").val();
	var data=getdata.split(':');
	$("#type_dec").val(data[1]);
	$("#status_no").val(data[0]);
	$( "#dialog_type_product" ).dialog( "close" );
}

function insert_tdiary_iq(){
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	if(product_id==""){
		jAlert('คุณยังไม่ป้อนรหัสสินค้า', 'แจ้งเตือน', function() {
			$("#product_id").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/order/order/chkproduct",data:{product_id:product_id},success:
			function(data){
				if(data=="y"){
					if(qty==""){
						jAlert('คุณยังไม่ป้อนจำนวนสินค้า', 'แจ้งเตือน', function() {
							$("#qty").focus();
						});
					}else{
						if(qty>0){
							//alert("OK");
							var form=$('form#frm_iq').serialize();
							$.ajax({type: "POST",url: "/order/order/inserttdiaryiq",data:form,success:
								function(data){
									viewiq("s");
									$("#product_id").val("");
									$("#qty").val("");
									$("#product_id").focus();
								}
							});
						}else{
							jAlert('จำนวนสินค้าต้องมากกว่า 0 ชิ้น', 'แจ้งเตือน', function() {
								$("#qty").focus();
							});
						}
					}
				}else{
					jAlert('ไม่พบสินค้า : '+product_id, 'แจ้งเตือน', function() {
						$("#product_id").focus();
					});
				}
			}
		});
	}
}