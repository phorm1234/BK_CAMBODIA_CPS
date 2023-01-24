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
			"Select (F2)": function() {
				select_status_no();
			},
			"Cancle (Esc)": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	
	$( "#dialog_login" ).dialog({
		
		autoOpen: false,
		height: 120,
		width: 330,
		modal: true,
		
	});
	//open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
	$( "#dialog_product" ).dialog({
		autoOpen: false,
		height: 300,
		width: 550,
		modal: true,
		buttons: {
			"Select (F2)": function() {
				inputinv();
			},
			"Cancle (Esc)": function() {
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
					jAlert(txt, 'Alert!', function() {
						$("#invoice").focus();
					});
				}else if(data=="w"){
					jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'Alert!', function() {
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
		jAlert('คุณยังไม่ป้อนรหัสสินค้า', 'Alert!', function() {
			$("#product_id").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/order/order/checkproduct",data:{product_id:product_id,status_no:status_no,doc_tp:doc_tp},success:
			function(data){
				if(data=="y"){
					$("#qty").focus();
				}else if(data=="w"){
					var txt="ไม่สามารถสั่งรายการสินค้านี้ได้ : "+product_id;
					jAlert(txt, 'Alert!', function() {
						$("#product_id").focus();
					});
				}else if(data=="t"){
					var txt="รายการสินค้ารหัส : "+product_id+" นี้ได้สั่งไปแล้ว ";
					jAlert(txt, 'Alert!', function() {
						$("#product_id").focus();
					});
				}else if(data=="q"){
					var txt="วงเงินสั่งรายการสินค้าไม่เพียงพอ ";
					jAlert(txt, 'Alert!', function() {
						$("#product_id").focus();
					});
				}else{
					var check_status_no=$("#status_no").val();
					if(check_status_no==71){
						var txt="ไม่พบรหัสสินค้ารหัสสินค้า Tester : "+product_id;
					}else{
						var txt="ไม่พบรหัสสินค้ารหัสสินค้า : "+product_id;
					}
					
					jAlert(txt, 'Alert!', function() {
						$("#product_id").focus();
					});
				}
			}
		});
	}
}

function chk_product(product_id){
	if(product_id==""){
		jAlert('คุณยังไม่ป้อนรหัสสินค้า', 'Alert!', function() {
			$("#product_id").focus();
		});
	}else{
		$("#qty").focus();
	}
}

function insert_tdiary_or(){
	var chk_qty=$("#qty").val();
	var product_id = $("#product_id").val();
	if(chk_qty==""){
		jAlert('คุณยังไม่กรอกจำนวน!', 'Alert!', function() {
			$("#qty").focus();
		});
	}else{
		if(chk_qty>0){   // old version chk_qty>0 && chk_qty<100
			$.ajax({type: "POST",url: "/order/order/chkproduct",data:{product_id:product_id,qty:chk_qty},success:
				function(data){
					if(data=='y'){
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
						jAlert('ต้องสั่งสินค้าไม่เกินครึ่งหนึ่งของจำนวน Fix Stock', 'Alert!', function() {
							$("#qty").focus();
						});
					}
				}
			});	
		}else{
			jAlert('ต้องสั่งสินค้ามากกว่า 0 ชิ้น', 'Alert!', function() {
				$("#qty").focus();
			});
		}
	}
}

function order_cancel(){
	jConfirm('Confirm Delete?', 'Alert!', function(r) {
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
	jConfirm('Confirm Delete?', 'Alert!', function(r) {
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



function tempor2diaryor(cashier_id){
	
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	var doc_remark=$("#doc_remark").val();
	jConfirm('Confirm Save?', 'Alert!', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/order/order/tempor2diaryor",data:{doc_tp:doc_tp,status_no:status_no,doc_remark:doc_remark,chk_pass:cashier_id},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เลขที่เอกสาร : "+data.doc_no;
					jAlert(txt, 'Alert!', function() {
						$.ajax({type: "POST",url: "/order/order/printor",data:{doc_no:data.doc_no,status_no:status_no,doc_remark:doc_remark},success:
							function(data){
								getquota();
								vieworder("s");	
								$("#doc_remark").focus();
							}
						});
						
						
						
					});			
				}else if(data.status=="n"){
					jAlert('วงเงินสั่งสินค้าไม่เพียงพอ!', 'Alert!', function() {
						return false;
					});
				}else{
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'Alert!', function() {
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
	jConfirm('Confirm Save?', 'Alert!', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/order/order/tempor2diaryiq",data:{doc_tp:doc_tp,status_no:status_no,doc_remark:doc_remark,inv:inv},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เลขที่เอกสาร : "+data.doc_no;
					jAlert(txt, 'Alert!', function() {
						var page="printiq?doc_no="+data.doc_no;
						window.open(page, '_blank');
						viewiq("s");
						$("#doc_remark").val("");
						$("#inv").val("");
					});			
				}else if(data.status=="n"){
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'Alert!', function() {
						return false;
					});
				}else{
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'Alert!', function() {
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
				jAlert('You must delete all of items!', 'Alert', function() {
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
		jAlert('คุณยังไม่ป้อนรหัสสินค้า', 'Alert!', function() {
			$("#product_id").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/order/order/chkproduct",data:{product_id:product_id},success:
			function(data){
				if(data=="y"){
					if(qty==""){
						jAlert('คุณยังไม่ป้อนจำนวนสินค้า', 'Alert!', function() {
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
							jAlert('จำนวนสินค้าต้องมากกว่า 0 ชิ้น', 'Alert!', function() {
								$("#qty").focus();
							});
						}
					}
				}else{
					jAlert('ไม่พบสินค้า : '+product_id, 'Alert!', function() {
						$("#product_id").focus();
					});
				}
			}
		});
	}
}

///===================== Login =========================================================================
function emplogin(){
	
	$.ajax({type: "POST",url: "/order/order/cheklogin",data:{pass:''},success:
		function(data){
			if(data=="n"){
				$.ajax({type: "POST",url: "/order/order/loginorder",success:
					function(data){
						$("#dialog_login").html(data);
						$("#dialog_login").dialog( "open" );
					}
				});
			}else{
				$( "#dialog_login" ).dialog( "close" );
				tempor2diaryor(data);
			}
		}
	});
}

//=====================================================================================================
