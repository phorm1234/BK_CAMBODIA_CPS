$(document).ready( function() {
	viewproducttranfer("p");
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
	$( "#dialog_invoice" ).dialog({
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

function open_product(shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/productonshelf",data:{shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
	function(data){
		$("#dialog_product").html(data);
		$( "#dialog_product" ).dialog( "open" );
	}}); 
}

function search_invoice(){
	$.ajax({type: "POST",url: "/stock/stock/getinv",data:{},success:
	function(data){
		$("#dialog_invoice").html(data);
		$( "#dialog_invoice" ).dialog( "open" );
	}}); 
}

function inputinv(sta){
	var doc_no = $("input[name='inv']:checked").val();
	$("#invoice").val(doc_no);
	$( "#dialog_invoice" ).dialog( "close" );
	$("#password").val('');
	$("#password").focus();
	
	/*$.ajax({type: "POST",url: "/stock/stock/gentmp",data:{doc_no:doc_no},success:
	function(data){
		
	}});*/
	viewproducttranfer_b('p');
}

function keyinv(sta){
	viewproducttranfer_b('p');
	$("#password").val('');
	$("#password").focus();
}

function selinv(sta){
	var doc_no=$("#invoice").val();
	var pwd=$("#password").val();
	if(pwd==""){
		jAlert('กรุณาระบุรหัสผ่าน', 'แจ้งเตือน', function() {
			$("#password").focus();
			return false;
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkpassword",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					$.ajax({type: "POST",url: "/stock/stock/checkinv",data:{doc_no:doc_no},success:
					function(data){
						if(data=="n"){
							var txt="ไม่พบเลขที่สินค้าเข้า : "+doc_no;
							jAlert(txt, 'แจ้งเตือน', function() {
								$("#invoice").focus();
								$("#password").val('');
							});
						}else if(data=="w"){
							jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'แจ้งเตือน', function() {
								$("#invoice").focus();
							});
							
						}else{
							$.ajax({type: "POST",url: "/stock/stock/gentmp",data:{doc_no:doc_no},success:
							function(data){	
							}}); 
							$("#doc_remark").focus();
							viewproducttranfer(sta);
						}
					}});
				}else{
					jAlert('รหัสผ่านไม่ถูกต้อง', 'แจ้งเตือน', function() {
						viewproducttranfer_b('p');
						$("#password").focus();
						return false;
					});
				}
			}
		});
	}
}

function selinv_ck(sta){
	var doc_no=$("#invoice").val();
	var pwd=$("#password").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	if(pwd==""){
		jAlert('กรุณาระบุรหัสผ่าน', 'แจ้งเตือน', function() {
			$("#password").focus();
			return false;
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkpasswordck",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					$.ajax({type: "POST",url: "/stock/stock/checkinvstock",data:{doc_no:doc_no},success:
					function(data){
						if(data=="n"){
							var txt="ไม่พบเลขที่สินค้าเข้า : "+doc_no;
							jAlert(txt, 'แจ้งเตือน', function() {
								$("#invoice").focus();
								$("#password").val('');
							});
						}else if(data=="w"){
							jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'แจ้งเตือน', function() {
								$("#invoice").focus();
							});
							
						}else{
							$.ajax({type: "POST",url: "/stock/stock/gentmpcheckstock",data:{doc_no:doc_no,doc_tp:doc_tp,status_no:status_no},success:
							function(data){	
							}}); 
							$("#doc_remark").focus();
							viewproducttranfer_stock(sta);
						}
					}});
				}else{
					jAlert('รหัสผ่านไม่ถูกต้อง', 'แจ้งเตือน', function() {
						viewproducttranfer_b('p');
						$("#password").focus();
						return false;
					});
				}
			}
		});
	}
}


function viewproducttranfer_b(chk){
	var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getproducttranfer",data:{doc_no:'',chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
	}}); 
}

function viewproducttranfer(chk){
	var doc_no=$("#invoice").val();
	var status_no=$("#status_no").val();
	$.ajax({type: "POST",url: "/stock/stock/getproducttranfer",data:{doc_no:doc_no,chk:chk,status_no:status_no},success:
	function(data){
		if(chk=="s"){
			$.ajax({type: "POST",url: "/stock/stock/checkinv",data:{doc_no:doc_no},success:
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
			}});
		}
		$("#viewproducttranfer").html(data);
	}}); 
}

function viewproducttranfer_stock(chk){
	var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getproducttranfer",data:{doc_no:doc_no,chk:chk},success:
	function(data){
		if(chk=="s"){
			$.ajax({type: "POST",url: "/stock/stock/checkinvstock",data:{doc_no:doc_no},success:
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
			}});
		}
		$("#viewproducttranfer").html(data);
		$("#chk").val('1');
	}}); 
}

function tranfer_tranin2shopheadtodiary(){
	var chk=$("#chk").val();
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	if(chk>0){
		var pwd=$("#password").val();
		if(pwd==""){
			jAlert('กรุณาระบุรหัสผ่าน', 'แจ้งเตือน', function() {
				$("#password").focus();
				return false;
			});
		}else{
			
			$.ajax({type: "POST",url: "/stock/stock/checkpassword",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					jConfirm('ยืนยันการโอนสินค้า?', 'แจ้งเตือน', function(r) {
						if(r==false){
							return false;
						}else{
							$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiary",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},success:
							function(data){
								if(data=="y"){
									jAlert('โอนสินค้าเข้าเรียบร้อยแล้ว', 'แจ้งเตือน', function() {
										var page="printstock?doc_no_refer="+doc_no;
										window.open(page, '_blank');
										cleartxt();
									});							
								}else{
									jAlert('เกิดการผิดผลาดกรุณาตรวจข้อมูล', 'แจ้งเตือน', function() {
										clear_diary(doc_no);
									});
								}
							}});
						}
					});
				}else{
					jAlert('รหัสผ่านไม่ถูกต้อง', 'แจ้งเตือน', function() {
						viewproducttranfer_b('p');
						$("#password").focus();
						return false;
					});
				}
			}});
		}
	}else{
		jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'แจ้งเตือน', function() {
			$("#invoice").focus();
		});
	}
}


function tranfer_tranin2shopheadtodiary_checkstock(){
	var chk=$("#chk").val();
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	if(chk>0){
		var pwd=$("#password").val();
		if(pwd==""){
			jAlert('กรุณาระบุรหัสผ่าน', 'แจ้งเตือน', function() {
				$("#password").focus();
				return false;
			});
		}else{
			
			$.ajax({type: "POST",url: "/stock/stock/checkpasswordck",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					jConfirm('ยืนยันการโอนสินค้า?', 'แจ้งเตือน', function(r) {
						if(r==false){
							return false;
						}else{
							$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiary",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},success:
							function(data){
								if(data=="y"){
									jAlert('โอนสินค้าเข้าเรียบร้อยแล้ว', 'แจ้งเตือน', function() {
										cleartxt();
									});							
								}else{
									jAlert('เกิดการผิดผลาดกรุณาตรวจข้อมูล', 'แจ้งเตือน', function() {
										clear_diary(doc_no);
									});
								}
							}});
						}
					});
				}else{
					jAlert('รหัสผ่านไม่ถูกต้อง', 'แจ้งเตือน', function() {
						viewproducttranfer_b('p');
						$("#password").focus();
						return false;
					});
				}
			}});
		}
	}else{
		jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'แจ้งเตือน', function() {
			$("#invoice").focus();
		});
	}
}

function clear_diary(doc_no){
	//alert(doc_no);
	$.ajax({type: "POST",url: "/stock/stock/cleardiary",data:{doc_no:doc_no},success:
	function(data){
		
	}});
}

function tranfer_cancel(){
	jConfirm('ยืนยันการยกเลิก?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			cleartxt();
		}
	});
}

function keytranfer_cancel(){
	var doc_no=$("#invoice").val();
	jConfirm('ยืนยันการยกเลิก?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/cancelkeytranfer",data:{doc_no:doc_no},success:
				function(data){
					if(data=="y"){
						$("#invoice").val("");
						$("#invoice").focus();
						$("#doc_remark").val("");
						$("#product_id").val("");
						$("#password").val("");
						$("#qty").val("");
						viewproducttranfer_b("p");
					}
				}
			});
		}
	});
}

function cleartxt(){
	$("#invoice").val("");
	$("#invoice").focus();
	$("#password").val("");
	$("#doc_remark").val("");
	viewproducttranfer("p");
	//viewproducttranfer_b("p");	
}

function check_docno(){
	$("#doc_remark").focus();
	var doc_no=$("#invoice").val();
	if(doc_no==""){
		var txt="คุณยังไม่ป้อนเลขที่สินค้าเข้า";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#invoice").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkdiary1docno",data:{doc_no:doc_no},success:
			function(data){
				if(data=="y"){
					var txt="เลขที่สินค้า : "+doc_no+" โอนข้อมูลซ้ำโปรดตรวจสอบ";
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#invoice").focus();
					});
				}else if(data=="n"){
					$.ajax({type: "POST",url: "/stock/stock/checkheaddocno",data:{doc_no:doc_no},success:
						function(data){
							if(data=="y"){
								jAlert('ต้องไป Confirm ข้อมูลโอนเข้า', 'แจ้งเตือน', function() {
									$("#invoice").focus();
								});
							}else{
								
							}
						}
					});
				}
			}
		});
	}
}

function check_product(){
	var product_id=$("#product_id").val();
	$("#qty").focus();
	$.ajax({type: "POST",url: "/stock/stock/checkproduct",data:{product_id:product_id},success:
		function(data){
			if(data=="y"){
				
			}else if(data=="n"){
				var txt="ไม่พบรหัสสินค้าเลยที่ : "+product_id;
				jAlert(txt, 'แจ้งเตือน', function() {
					$("#product_id").focus();
				});
			}else{
				return false;
			}
		}
	});
}

function gen_tmpdiary(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var form=$('form#frm_tranferkeyin').serialize();
	$.ajax({type: "POST",url: "/stock/stock/inserttmpdiary?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
		function(data){
			if(data=="y"){
				viewproductkeytranfer("s");
			}
		}
	});
}

function viewproductkeytranfer(chk){
	var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getkeyproducttranfer",data:{doc_no:doc_no,chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
		$("#product_id").val("");
		$("#qty").val("");
		$("#product_id").focus();
	}}); 
}

function viewproductkeytranfer_b(chk){
	var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getkeyproducttranfer",data:{doc_no:doc_no,chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
	}}); 
}

function keytranfer_tranin2shopheadtodiary(){
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	jConfirm('ยืนยันการโอนสินค้า?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiary",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},success:
			function(data){
				if(data=="y"){
					jAlert('โอนสินค้าเข้าเรียบร้อยแล้ว', 'แจ้งเตือน', function() {
						cleartxt();
					});
				}else if(data=="trn_diary1"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary1', 'แจ้งเตือน', function() {
						
					});
				}else if(data=="trn_diary2"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary2', 'แจ้งเตือน', function() {
															
					});
				}else if(data=="com_stock_master"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:com_stock_master', 'แจ้งเตือน', function() {
						
					});									
				}else{
					jAlert('เกิดการผิดผลาดกรุณาตรวจข้อมูล', 'แจ้งเตือน', function() {
						
					});
				}
			}});
		}
	});
}

function search_docno_ai(p_type){
	$.ajax({type: "POST",url: "/stock/stock/getdocnochkstock",data:{p_type:p_type},success:
	function(data){
		$("#dialog_invoice").html(data);
		$( "#dialog_invoice" ).dialog( "open" );
	}});
}
