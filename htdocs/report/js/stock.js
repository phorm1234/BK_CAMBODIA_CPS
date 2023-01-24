$(document).ready( function() {
	var doc_tp=$("#doc_tp").val();
	if(doc_tp=="TI"){
		viewproducttranfer("p");
	}else if(doc_tp=="RQ"){
		viewproductkeytranfer_rq_b("p");
	}else{
		viewproductkeytranfer_to_b("p");
	}
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
	
	$( "#dialog_process" ).dialog({
		autoOpen: false,
		height: 100,
		width: 200,
		modal: true
	});
	
	$( "#dialog_inv" ).dialog({
		autoOpen: false,
		height: 300,
		width: 550,
		modal: true,
		buttons: {
			"เลือก (F2)": function() {
				insertinputinv();
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
	viewproducttranfer_b('p');
}

function insertinputinv(){
	var doc_no = $("input[name='getinv']:checked").val();
	$("#inv").val(doc_no);
	$( "#dialog_inv" ).dialog( "close" );
	$("#product_id").focus();
}

function insertinputbillvt(){
	var doc_no = $("input[name='getinv']:checked").val();
	$("#doc_tpye").val(doc_no);
	$( "#dialog_bill_vt" ).dialog( "close" );
	$("#doc_tpye").focus();
	getbillvtdetail(doc_no);
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
		$( "#dialog_process" ).html("<font color='blue'><b>กำลังตรวจสอบรหัสผ่าน...</b></font>");
		$( "#dialog_process" ).dialog( "open" );
		$.ajax({type: "POST",url: "/stock/stock/checkpassword",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					$( "#dialog_process" ).dialog( "close" );
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

function viewproductquery(){
	var flag=$("#flag").val();
	var txt=$("#txt").val();
	$.ajax({type: "POST",url: "/stock/stock/getproductquery",data:{flag:flag,txt:txt},success:
	function(data){
		$("#viewproductquery").html(data);
	}}); 
}

function viewproducttranfer(chk){
	var doc_no=$("#invoice").val();
	var status_no=$("#status_no").val();
	$( "#dialog_process" ).html("<font color='blue'><b>กำลังดึงข้อมูล...</b></font>");
	$( "#dialog_process" ).dialog( "open" );
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
		$( "#dialog_process" ).dialog( "close" );
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
							$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiary",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},dataType: 'json',success:
							function(data){
								if(data.status=="y"){
									var txt="เอกสารเลขที่ : "+data.doc_no;
									jAlert(txt, 'แจ้งเตือน', function() {
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

function keytranfer_cancel(doc_no){
	var doc_no=$("#doc_no").val();
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

function keytranferto_cancel(){
	jConfirm('ยืนยันการยกเลิก?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/cancelkeytranferto",data:{},success:
				function(data){
					if(data=="y"){
						$("#invoice").val("");
						$("#invoice").focus();
						$("#doc_remark").val("");
						$("#product_id").val("");
						$("#password").val("");
						$("#qty").val("");
						$("#inv").val("");
						checktempdiary_to();
						viewproductkeytranfer_to_b("p");
					}
				}
			});
		}
	});
}

function keytranferrq_cancel(){
	jConfirm('ยืนยันการยกเลิก?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/cancelkeytranferrq",data:{},success:
				function(data){
					if(data=="y"){
						$("#invoice").val("");
						$("#invoice").focus();
						$("#doc_remark").val("");
						$("#product_id").val("");
						$("#password").val("");
						$("#qty").val("");
						$("#inv").val("");
						checktempdiary_rq();
						viewproductkeytranfer_rq_b("p");
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
	$("#inv").val("");
	$("#product_id").val("");
	$("#qty").val("");
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
					viewproductkeytranfer("s");
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
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var status_no=$("#status_no").val();
	/*if(invoice==""){
		var txt="กรุณาป้อนเลขที่สินค้าโอนเข้า";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#invoice").focus();
		});
	}else{*/
		if(product_id==""){
			var txt="กรุณาป้อนรหัสสินค้า";
			jAlert(txt, 'แจ้งเตือน', function() {
				$("#product_id").focus();
			});
		}else{
			$.ajax({type: "POST",url: "/stock/stock/checkproduct",data:{product_id:product_id,status_no:status_no},success:
				function(data){
					if(data=="n"){
						var txt="ไม่พบรหัสสินค้าเลขที่ : "+product_id;
						jAlert(txt, 'แจ้งเตือน', function() {
							$("#product_id").focus();
							return false;
						});
					}else{
						$("#qty").focus();
					}
				}
			});
	//	}
	}
}

function check_qty(){
	var status_no=$("#status_no").val();
	if(status_no=="25"){
		get_check_qty_tester();
	}else{
		get_check_qty();
	}
}

function check_qty_rq(){
	var status_no=$("#status_no").val();
	if(status_no=="20" || status_no=="26"  || status_no=="27"){
		get_check_qty_rq();
	}else{
		get_check_qty_tester();
	}
}

function get_check_qty(){
	var inv=$("#inv").val();
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	var doc_status=$("#doc_status").val();
	if(inv==""){
		var txt="กรุณาป้อนเลขที่ Invoice";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#inv").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkinvoice",data:{inv:inv,doc_status:doc_status},success:
			function(data){
				if(data=="y"){
					if(product_id==""){
						var txt="กรุณาป้อนรหัสสินค้า";
						jAlert(txt, 'แจ้งเตือน', function() {
							$("#product_id").focus();
						});
					}else{
						$.ajax({type: "POST",url: "/stock/stock/checkproductinvoice",data:{inv:inv,product_id:product_id,doc_status:doc_status},success:
							function(data){
								if(data=="y"){
									if(qty==""){
										var txt="กรุณาป้อนจำนวน";
										jAlert(txt, 'แจ้งเตือน', function() {
											$("#qty").focus();
										});
									}else{
										$.ajax({type: "POST",url: "/stock/stock/checkproductinvoiceqty",data:{inv:inv,product_id:product_id,qty:qty,doc_status:doc_status},success:
											function(data){
												if(data=="1"){
													var txt="จำนวนสินค้าเกินโปรดตรวจสอบ";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
													});
												}else if(data=="2"){
													var txt="จำนวนสินค้าเกินจำนวนสินค้าที่อยู่ในเอกสาร";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
													});
												}else if(data=="0"){
													var txt="จำนวนต้องมากกว่า 0 ชิ้น";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
														return false;
													});
												}else if(data=="3"){
													create_tmpdiary_to();
												}else{
													var txt="ไม่สามารถทำรายการได้";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
														return false;
													});
												}
											}
										});
									}
								}else{
									var txt="ไม่พบสินค้าเลขที่ : "+product_id+" ใน Invoice เลขที่ : "+inv;
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#product_id").focus();
									});
								}
							}
						});
					}
				}else{
					var txt="ไม่พบ Invoice : "+inv;
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#inv").focus();
					});
				}
			}
		});
	}
}

function get_check_qty_rq(){
	var inv=$("#inv").val();
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	var doc_status=$("#doc_status").val();
	if(inv==""){
		var txt="กรุณาป้อนเลขที่ Invoice";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#inv").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkinvoicerq",data:{inv:inv,doc_status:doc_status},success:
			function(data){
				if(data=="y"){
					if(product_id==""){
						var txt="กรุณาป้อนรหัสสินค้า";
						jAlert(txt, 'แจ้งเตือน', function() {
							$("#product_id").focus();
						});
					}else{
						$.ajax({type: "POST",url: "/stock/stock/checkproductinvoicerq",data:{inv:inv,product_id:product_id,doc_status:doc_status},success:
							function(data){
								if(data=="y"){
									if(qty==""){
										var txt="กรุณาป้อนจำนวน";
										jAlert(txt, 'แจ้งเตือน', function() {
											$("#qty").focus();
										});
									}else{
										$.ajax({type: "POST",url: "/stock/stock/checkproductinvoiceqtyrq",data:{inv:inv,product_id:product_id,qty:qty,doc_status:doc_status},success:
											function(data){
												if(data=="1"){
													var txt="จำนวนสินค้าเกินโปรดตรวจสอบ";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
													});
												}else if(data=="2"){
													var txt="จำนวนสินค้าเกินจำนวนสินค้าที่อยู่ในเอกสาร";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
													});
												}else if(data=="0"){
													var txt="จำนวนต้องมากกว่า 0 ชิ้น";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
														return false;
													});
												}else if(data=="3"){
													create_tmpdiary_rq();
												}else{
													var txt="ไม่สามารถทำรายการได้";
													jAlert(txt, 'แจ้งเตือน', function() {
														$("#qty").focus();
														return false;
													});
												}
											}
										});
									}
								}else{
									var txt="ไม่พบสินค้าเลขที่ : "+product_id+" ใน Invoice เลขที่ : "+inv;
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#product_id").focus();
									});
								}
							}
						});
					}
				}else{
					var txt="ไม่พบ Invoice : "+inv;
					jAlert(txt, 'แจ้งเตือน', function() {
						$("#inv").focus();
					});
				}
			}
		});
	}
}

function get_check_qty_tester(){
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var status_no=$("#status_no").val();
	var qty=$("#qty").val();
	var doc_tp=$("#doc_tp").val();
	if(doc_tp=="TI"){
		var page="checkproducttester";
	}else if(doc_tp=="RQ"){
		var page="checkproducttesterrq";
	}
	if(product_id==""){
		var txt="กรุณาป้อนรหัสสินค้า";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#product_id").focus();
		});
	}else{
		if(qty==""){
			var txt="กรุณาป้อนจำนวน";
			jAlert(txt, 'แจ้งเตือน', function() {
				$("#qty").focus();
			});
		}else{
			$.ajax({type: "POST",url: "/stock/stock/checkproduct",data:{product_id:product_id,status_no:status_no},success:
				function(data){
					if(data=="n"){
						var txt="ไม่พบรหัสสินค้าเลขที่ : "+product_id;
						jAlert(txt, 'แจ้งเตือน', function() {
							$("#product_id").focus();
							return false;
						});
					}else{
						$.ajax({type: "POST",url: "/stock/stock/"+page,data:{product_id:product_id,status_no:status_no,qty:qty},dataType: 'json',success:
							function(data){
								if(data.status=="1"){
									var txt="สินค้ารหัส : "+product_id+" ได้สั้่งครบแล้ว";
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#qty").focus();
										return false;
									});
								}else if(data.status=="2"){
									var txt="สินค้ารหัส : "+product_id+" ได้สั้่งครบแล้ว";
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#qty").focus();
										return false;
									});
								}else if(data.status=="3"){
									var txt="ได้สั่งสินค้ามากกว่าที่กำหนดไว้";
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#qty").focus();
										return false;
									});
								}else if(data.status=="4"){
									/*var txt="สินค้ารหัส : "+product_id+" ได้สั้่งครบแล้ว";
									jAlert(txt, 'แจ้งเตือน', function() {
										return false;
									});*/
									
									if(doc_tp=="TI"){
										create_tmpdiary_to();
									}else if(doc_tp=="RQ"){
										create_tmpdiary_rq();
									}
								}else if(data.status=="0"){
									var txt="ไม่มีสินค้าอยู่ใน Stock";
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#qty").focus();
										return false;
									});
									
								}else if(data.status=="6"){
									var txt="จำนวนต้องมากกว่า 0 ชิ้น";
									jAlert(txt, 'แจ้งเตือน', function() {
										$("#qty").focus();
										return false;
									});
									
								}else{
									return false;
								}
							}
						});
					}
				}
			});
		}
	}
}

function gen_tmpdiary(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	if(invoice==""){
		var txt="กรุณาป้อนเลขที่สินค้าโอนเข้า";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#invoice").focus();
		});
	}else{
		if(product_id==""){
			var txt="กรุณาป้อนรหัสสินค้า";
			jAlert(txt, 'แจ้งเตือน', function() {
				$("#product_id").focus();
			});
		}else{
			if(qty==""){
				var txt="กรุณาป้อนจำนวน";
				jAlert(txt, 'แจ้งเตือน', function() {
					$("#qty").focus();
				});
			}else{
				create_tmpdiary();
			}
		}
	}
}

function create_tmpdiary(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var product_id=$("#product_id").val();
	var form=$('form#frm_tranferkeyin').serialize();
	$.ajax({type: "POST",url: "/stock/stock/inserttmpdiary?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
		function(data){
			if(data=="y"){
				//$("#doc_status").attr('disabled', true);
				//$("#doc_status").removeAttr('disabled');
				viewproductkeytranfer("s");
			}else{
				var txt="เกิดการผิดพลาดไม่สามารถทำรายการได้";
				jAlert(txt, 'แจ้งเตือน', function() {
					$("#product_id").focus();
				});
			}
		}
	});
}

function create_tmpdiary_to(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var product_id=$("#product_id").val();
	var form=$('form#frm_tranferkeyin').serialize();
	$.ajax({type: "POST",url: "/stock/stock/inserttmpdiaryto?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
		function(data){
			if(data=="y"){
				//$("#doc_status").attr('disabled', true);
				//$("#doc_status").removeAttr('disabled');
				checktempdiary_to();
				viewproductkeytranfer_to("s");
				
			}else{
				var txt="เกิดการผิดพลาดไม่สามารถทำรายการได้";
				jAlert(txt, 'แจ้งเตือน', function() {
					$("#product_id").focus();
				});
			}
		}
	});
}

function create_tmpdiary_rq(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var product_id=$("#product_id").val();
	var form=$('form#frm_tranferkeyin').serialize();
	$.ajax({type: "POST",url: "/stock/stock/inserttmpdiaryrq?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
		function(data){
			if(data=="y"){
				checktempdiary_rq();
				viewproductkeytranfer_rq("s");
				
			}else{
				var txt="เกิดการผิดพลาดไม่สามารถทำรายการได้";
				jAlert(txt, 'แจ้งเตือน', function() {
					$("#product_id").focus();
				});
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

function viewproductkeytranfer_to(chk){
	//var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getkeyproducttranferto",data:{chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
		$("#product_id").val("");
		$("#qty").val("");
		$("#product_id").focus();
	}});
}

function viewproductkeytranfer_to_b(chk){
	//var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getkeyproducttranferto",data:{chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
	}}); 
}


function viewproductkeytranfer_rq(chk){
	//var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getkeyproducttranferrq",data:{chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
		$("#product_id").val("");
		$("#qty").val("");
		$("#product_id").focus();
	}});
}

function viewproductkeytranfer_rq_b(chk){
	//var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getkeyproducttranferrq",data:{chk:chk},success:
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
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiary",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เอกสารเลขที่ : "+data.doc_no;
					jAlert(txt, 'แจ้งเตือน', function() {
						cleartxt();
					});
				}else if(data.status=="trn_diary1"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary1', 'แจ้งเตือน', function() {
						
					});
				}else if(data.status=="trn_diary2"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary2', 'แจ้งเตือน', function() {
						
					});
				}else if(data.status=="com_stock_master"){
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

function keytranfer_tranin2shopheadtodiary_to(){
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	var inv=$("#inv").val();
	jConfirm('ยืนยันการโอนสินค้า?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryto",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no,inv:inv},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เอกสารเลขที่ : "+data.doc_no;
					jAlert(txt, 'แจ้งเตือน', function() {
						checktempdiary_to();
						cleartxt();
					});
				}else if(data.status=="trn_diary1"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary1', 'แจ้งเตือน', function() {
						
					});
				}else if(data.status=="trn_diary2"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary2', 'แจ้งเตือน', function() {
						
					});
				}else if(data.status=="com_stock_master"){
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

function keytranfer_tranin2shopheadtodiary_rq(){
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	var inv=$("#inv").val();
	jConfirm('ยืนยันการโอนสินค้า?', 'แจ้งเตือน', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryrq",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no,inv:inv},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เอกสารเลขที่ : "+data.doc_no;
					jAlert(txt, 'แจ้งเตือน', function() {
						checktempdiary_rq();
						cleartxt();
					});
				}else if(data.status=="trn_diary1"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary1', 'แจ้งเตือน', function() {
						
					});
				}else if(data.status=="trn_diary2"){
					jAlert('เกิดการผิดผลาดกรุณาตรวจสอบ table:trn_diary2', 'แจ้งเตือน', function() {
						
					});
				}else if(data.status=="com_stock_master"){
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

function search_bill_vt(doc_tp){
	$.ajax({type: "POST",url: "/stock/miscellaneous/getdocnobill",data:{doc_tp:doc_tp},success:
		function(data){
		$("#dialog_bill_vt").html(data);
		$( "#dialog_bill_vt" ).dialog( "open" );
	}});
}

function search_docno_to(){
	$.ajax({type: "POST",url: "/stock/stock/checktempdiaryto",data:{},success:
		function(data){
			if(data=="y"){
				jAlert('ไม่สามารถทำรายการได้!', 'แจ้งเตือน', function() {
					$("#product_id").focus();
				});	
			}else{
				var status_no=$("#status_no").val();
				$.ajax({type: "POST",url: "/stock/stock/getdocnoto",data:{status_no:status_no},success:
				function(data){
					$("#dialog_inv").html(data);
					$( "#dialog_inv" ).dialog( "open" );
				}});
			}
		}
	});
}

function search_docno_rq(){
	$.ajax({type: "POST",url: "/stock/stock/checktempdiaryrq",data:{},success:
		function(data){
			if(data=="y"){
				jAlert('ไม่สามารถทำรายการได้!', 'แจ้งเตือน', function() {
					$("#product_id").focus();
				});	
			}else{
				var status_no=$("#status_no").val();
				$.ajax({type: "POST",url: "/stock/stock/getdocnoto",data:{status_no:status_no},success:
				function(data){
					$("#dialog_inv").html(data);
					$( "#dialog_inv" ).dialog( "open" );
				}});
			}
		}
	});
}

function checktempdiary_to(){
	$.ajax({type: "POST",url: "/stock/stock/checktempdiaryto",data:{},success:
	function(data){
		if(data=="y"){
			select_disabled();
		}else{
			select_enable();
		}
	}});
}

function checktempdiary_rq(){
	$.ajax({type: "POST",url: "/stock/stock/checktempdiaryrq",data:{},success:
	function(data){
		if(data=="y"){
			select_disabled();
		}else{
			select_enable();
		}
	}});
}

function dbclick(){
	insertinputinv();
}

function getbillvtdetail(doc_no){
	$.ajax({type: "POST",url: "/stock/miscellaneous/getbillvtdetail",data:{doc_no:doc_no},dataType: 'json',success:
		function(data){
			$("#fullname").val(data[0].name);
			$("#addr1").val(data[0].address1);
			$("#addr2").val(data[0].address2);
			$("#addr3").val(data[0].address3);
			$("#edit_doc_on").val(data[0].doc_no);
		}
	});
}

function eidt_contace_bill_vt(){
	var doc_tpye=$("#doc_tpye").val();
	var doc_no=$("#edit_doc_on").val();
	if(doc_tpye==""){
		var txt="คุณยังไม่ป้อนเลขที่เอกสาร";
		jAlert(txt, 'แจ้งเตือน', function() {
			$("#doc_type").focus();
		});
	}else{
		if(doc_no==""){
			var txt="ไม่พบเลขที่เอกสาร";
			jAlert(txt, 'แจ้งเตือน', function() {
				$("#doc_type").focus();
			});
		}else{
			var form=$('form#frm').serialize();
			$.ajax({type: "POST",url: "/stock/miscellaneous/editcontactbillvt",data:form,success:
				function(data){
					if(data=="y"){
						jAlert('บันทึกข้อมูลแล้ว', 'แจ้งเตือน', function() {
							$("#fullname").val("");
							$("#addr1").val("");
							$("#addr2").val("");
							$("#addr3").val("");
							$("#edit_doc_on").val("");
							$("#doc_tpye").val("");
						});
					}else{
						jAlert('ไม่สามารถบันทึกข้อมูลได้ กรุณาตรวจสอบข้อมูล', 'แจ้งเตือน', function() {
							return false;
						});
					}
				}
			});
		}
	}
}

function editconfigprinter(){
	var code_type=$("#code_type").val();
	var default_status=$("#default_status").val();
	$.ajax({type: "POST",url: "/stock/miscellaneous/editconfigprinter",data:{code_type:code_type,default_status:default_status},success:
		function(data){
		if(data=="y"){
			jAlert('บันทึกข้อมูลแล้ว', 'แจ้งเตือน', function() {
				
			});
		}else{
			jAlert('ไม่สามารถบันทึกข้อมูลได้ กรุณาตรวจสอบข้อมูล', 'แจ้งเตือน', function() {
				return false;
			});
		}
		}
	});
}

function search_gp(){
	$.ajax({type: "POST",url: "/stock/miscellaneous/getsearchgp",data:{},success:
		function(data){
		$("#dialog_gp").html(data);
		$( "#dialog_gp" ).dialog( "open" );
	}});
}

function insertinpugp(){
	var getbarcode = $("input[name='getbarcode']:checked").val();
	$("#barcode").val(getbarcode);
	$( "#dialog_gp" ).dialog( "close" );
	$("#barcode").focus();
	viewgp();
}

function save_form_gp(){
	var barcode=$("#i_barcode").val();
	if(barcode==""){
		jAlert('คุณยังไม่ได้กรอกรหัส Barcode', 'แจ้งเตือน', function() {
			$("#i_barcode").focus();
		});
	}else{
		jConfirm('ยืนยันการบันทึก?', 'แจ้งเตือน', function(r) {
			if(r==false){
				return false;
			}else{
				var form=$('form#frm_gp').serialize();
				$.ajax({type: "POST",url: "/stock/miscellaneous/saveformgp",data:form,success:
					function(data){
						if(data=="y"){
							$( "#dialog_frm_gp" ).dialog( "close" );	
							viewgp();
						}else{
							var txt="เกิดการผิดพลาดไม่สามารถทำรายการได้";
							jAlert(txt, 'แจ้งเตือน', function() {
								
							});
						}
					}
				});
			}
		});
	}
}

function print_form_count_stock(){
	var page="printcountstock";
	window.open(page, '_blank');
}

function print_form_check_cash(){
	var page="printcheckcash";
	window.open(page, '_blank');
}