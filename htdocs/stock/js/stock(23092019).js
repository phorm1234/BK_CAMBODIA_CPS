$(document).ready( function() {
	var doc_tp=$("#doc_tp").val();
	if(doc_tp=="TI"){
		viewproducttranfer("p");
	}else if(doc_tp=="RQ"){
		viewproductkeytranfer_rq_b("p");
	}else{
		//viewproductkeytranfer_to_b("p");
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
		width: 472,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    }
	});
	
	$( "#dialog_process" ).dialog({
		autoOpen: false,
		height: 100,
		width: 200,
		modal: true,
		resizable: false,
		open: function(event, ui) {
        $(".ui-widget-overlay").css('opacity',0.3);
        $(".ui-dialog-titlebar").hide();
		}
	});
	
	$( "#dialog_inv" ).dialog({
		autoOpen: false,
		height: 300,
		width: 472,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    }
	});
	
	$( "#dialog_disable" ).dialog({
		autoOpen: false,
		closeOnEscape: false,
		height: 100,
		width: 200,
		modal: true,
		resizable: false,
		open: function(event, ui) {
        $(".ui-widget-overlay").css('opacity',0.9);
        $(".ui-dialog-titlebar").hide();
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
	$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>Please wait...</b></font></center>");
	$( "#dialog_process" ).dialog( "open" );
	$.ajax({type: "POST",url: "/stock/stock/getinv",data:{},success:
	function(data){
		$( "#dialog_process" ).dialog( "close" );
		$("#dialog_invoice").html(data);
		$( "#dialog_invoice" ).dialog( "open" );
	}}); 
}

////////////////// search doc_no or
function search_doc_to_by_or(){
	$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>Please wait...</b></font></center>");
	$( "#dialog_process" ).dialog( "open" );
	$.ajax({type: "POST",url: "/stock/stock/getdoctobyor",data:{},success:
	function(data){
		$( "#dialog_process" ).dialog( "close" );
		$("#dialog_invoice").html(data);
		$( "#dialog_invoice" ).dialog( "open" );
	}}); 
}

function keytranfertoor_cancel(){
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
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
						$("#qty").val("");
						$("#inv").val("");
						checktempdiary_to();
						viewproductkeytranfer_to_or("p");
					}
				}
			});
		}
	});
}
/////////////////


function inputinv(sta){
	var doc_no = $("input[name='inv']:checked").val();
	var check_doc = doc_no.substring(0, 3); 
	if(check_doc!="INV"){
		$.ajax({type: "POST",url: "/stock/stock/checkpl",data:{},success:
		function(data){
			if(data>0){
				$("#invoice").val(doc_no);
				$( "#dialog_invoice" ).dialog( "close" );
				$("#password").val('');
				$("#password").focus();
				viewproducttranfer_b('p');
				// $("#doc_remark").val('');
				// $("#doc_remark").focus();
			}else{
				jAlert('Must Insert Invoice No. ... before receipt', 'Alert', function() {
					$("#invoice").val("");
					$("#invoice").focus();
					return false;
				});
			}
			
		}}); 
	}else{
		$("#invoice").val(doc_no);
		$( "#dialog_invoice" ).dialog( "close" );
		$("#password").val('');
		$("#password").focus();
		viewproducttranfer_b('p');
	}
}

//////////////////
function inputinv2(sta){
	var doc_no = $("input[name='inv']:checked").val();
	var check_doc = doc_no.substring(0, 3); 
	if(check_doc!="INV"){
		$.ajax({type: "POST",url: "/stock/stock/checkpl",data:{},success:
		function(data){
			if(data>0){
				$("#invoice").val(doc_no);
				$( "#dialog_invoice" ).dialog( "close" );
				viewproducttranfer_toor();
				// $("#doc_remark").val('');
				// $("#doc_remark").focus();
			}else{
				jAlert('Must Insert Invoice No. ... before receipt', 'Alert', function() {
					$("#invoice").val("");
					$("#invoice").focus();
					return false;
				});
			}
			$("#doc_remark").val('');
			$("#doc_remark").focus();
		}}); 
	}else{
		$("#invoice").val(doc_no);
		$( "#dialog_invoice" ).dialog( "close" );
		viewproducttranfer_b('p');
	}
}

function insertinputinv2(){
	var doc_no = $("input[name='getinv']:checked").val();
	$("#inv").val(doc_no);
	$( "#dialog_inv" ).dialog( "close" );
	$("#product_id").focus();
}
/////////////////

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
		jAlert('Please Insert Password', 'Alert', function() {
			$("#password").focus();
			return false;
		});
	}else{
		$( "#dialog_process" ).html("<center><font color='#059562'><b><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br>password checking...</b></font><center>");
		$( "#dialog_process" ).dialog( "open" );
		$.ajax({type: "POST",url: "/stock/stock/checkpassword",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					$.ajax({type: "POST",url: "/stock/stock/checkproductmaster",data:{doc_no:doc_no},success:
						function(data){
							if(data=="y"){
								$( "#dialog_process" ).dialog( "close" );
								$.ajax({type: "POST",url: "/stock/stock/checkinv",data:{doc_no:doc_no},success:
								function(data){
									if(data=="n"){
										var txt="Product ID Transfer In not found : "+doc_no;
										jAlert(txt, 'Alert', function() {
											$("#invoice").focus();
											$("#password").val('');
										});
									}else if(data=="w"){
										jAlert('You did not Insert Invoice No. !!', 'Alert', function() {
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
								$( "#dialog_process" ).dialog( "close" );
								jAlert("Some items are not listed", 'Alert', function() {
									$("#invoice").focus();
									$("#password").val('');
								});
							}
						}
					});
				}else{
					$( "#dialog_process" ).dialog( "close" );
					jAlert('Please Insert Password', 'Alert', function() {
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
		jAlert('Please Insert Password', 'Alert', function() {
			$("#password").focus();
			return false;
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkpasswordck",data:{doc_no:doc_no,pwd:pwd},success:
			function(data){
				if(data=="y"){
					$.ajax({type: "POST",url: "/stock/stock/gentmpcheckstock",data:{doc_no:doc_no,doc_tp:doc_tp,status_no:status_no},success:
						function(data){	
							$("#doc_remark").focus();
							viewproducttemp();
						}}); 
						
				}else{
					jAlert('Please Insert Password', 'Alert', function() {
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

////////////////////////
function viewproducttranfer_toor(chk){
	var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getproducttoor",data:{doc_no:doc_no,chk:chk},success:
	function(data){
		$("#viewproducttranfer").html(data);
	}}); 
}
////////////////////////

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
	$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>fetching data...</b></font></center>");
	$( "#dialog_process" ).dialog( "open" );
	$.ajax({type: "POST",url: "/stock/stock/getproducttranfer",data:{doc_no:doc_no,chk:chk,status_no:status_no},success:
	function(data){
		if(chk=="s"){
			$.ajax({type: "POST",url: "/stock/stock/checkinv",data:{doc_no:doc_no},success:
			function(data){
				if(data=="n"){
					var txt="Product ID Transfer In not found : "+doc_no;
					jAlert(txt, 'Alert', function() {
						$("#invoice").focus();
					});
				}else if(data=="w"){
					jAlert('You did not Insert Invoice No. !!', 'Alert', function() {
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
			/*$.ajax({type: "POST",url: "/stock/stock/checkinvstock",data:{doc_no:doc_no},success:
			function(data){
				//if(data=="n"){
					//var txt="Product ID Transfer In not found : "+doc_no;
					//jAlert(txt, 'Alert', function() {
						//$("#invoice").focus();
					//});
				//}else if(data=="w"){
				if(data=="w"){
					jAlert('You did not Insert Invoice No. !!', 'Alert', function() {
						$("#invoice").focus();
					});
				}
			}});*/
			$("#viewproducttranfer").html(data);
			$("#chk").val('1');
		}
		
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
			jAlert('Please Insert Password', 'Alert', function() {
				$("#password").focus();
				return false;
			});
		}else{
			$.ajax({type: "POST",url: "/stock/stock/checkproductmaster",data:{doc_no:doc_no},success:
			function(data){
				if(data=="y"){
					$.ajax({type: "POST",url: "/stock/stock/checkpassword",data:{doc_no:doc_no,pwd:pwd},success:
					function(data){
						if(data=="y"){
							jConfirm('Confirm Transfer?', 'Alert', function(r) {
								if(r==false){
									return false;
								}else{
									$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>Please wait...</b></font></center>");
									$( "#dialog_process" ).dialog( "open" );
									$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiary",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},dataType: 'json',success:
									function(data){
										if(data.status=="y"){
											$( "#dialog_process" ).dialog( "close" );
											var txt="Doc. No. : "+data.doc_no;
											jAlert(txt, 'Alert', function() {
												/*var page="printstock?doc_no="+data.doc_no;
												window.open(page, '_blank');
												cleartxt();*/
												$.ajax({type: "POST",url: "/stock/stock/printstock",data:{doc_no:data.doc_no},success:
												function(data){
													cleartxt();
												}});
											});	
										}else{
											jAlert('Error, please check the data', 'Alert', function() {
												clear_diary(doc_no);
											});
										}
									}});
								}
							});
						}else{
							jAlert('Please Insert Password', 'Alert', function() {
								viewproducttranfer_b('p');
								$("#password").focus();
								return false;
							});
						}
					}});
				}else{
					$( "#dialog_process" ).dialog( "close" );
					jAlert("Some items are not listed", 'Alert', function() {
						
					});
				}
			}});
		}
	}else{
		jAlert('You did not Insert Invoice No. !!', 'Alert', function() {
			$("#invoice").focus();
		});
	}
}

function tranfer_tranin2shopheadtodiary_checkstock(){
	//var chk=$("#chk").val();
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	//if(chk>0){
		var pwd=$("#password").val();
		if(pwd==""){
			jAlert('Please Insert Password', 'Alert', function() {
				$("#password").focus();
				return false;
			});
		}else{
			/*$.ajax({type: "POST",url: "/stock/stock/checkdiary1docno",data:{doc_no:doc_no},success:
				function(data){
					if(data=="y"){
						var txt="Product ID : "+doc_no+" repeat data transfer, please check.";
						jAlert(txt, 'Alert', function() {
							$("#invoice").focus();
						});
					}else if(data=="n"){*/
						$.ajax({type: "POST",url: "/stock/stock/checkpasswordck",data:{doc_no:doc_no,pwd:pwd},success:
							function(data){
								if(data=="y"){
									jConfirm('Confirm Transfer?', 'Alert', function(r) {
										if(r==false){
											return false;
										}else{
											$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>Please wait...</b></font></center>");
											$( "#dialog_process" ).dialog( "open" );
											$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryaiao",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},dataType: 'json',success:
											function(data){
												if(data.status=="y"){
													$( "#dialog_process" ).dialog( "close" );
													var txt="Doc. No. : "+data.doc_no;
													jAlert(txt, 'Alert', function() {
														$.ajax({type: "POST",url: "/stock/stock/printstock",data:{doc_no:data.doc_no},success:
														function(){
															$.ajax({type: "POST",url: "/stock/stock/printstock",data:{doc_no:data.doc_no},success:
																function(){
																	cleartxt();
																	viewproducttemp();
																}});
														}});
														
													});	
												}else{
													jAlert('Error, please check the data', 'Alert', function() {
														clear_diary(doc_no);
													});
												}
											}});
										}
									});
								}else{
									jAlert('Wrong Password', 'Alert', function() {
										viewproducttranfer_b('p');
										$("#password").focus();
										return false;
									});
								}
							}
						});
					/*}
				}
			});*/
		}
	/*}else{
		jAlert('You did not Insert Invoice No. !!', 'Alert', function() {
			$("#invoice").focus();
		});
	}*/
}

function clear_diary(doc_no){
	$.ajax({type: "POST",url: "/stock/stock/cleardiary",data:{doc_no:doc_no},success:
	function(data){
		
	}});
}

function tranfer_cancel(){
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			cleartxt();
		}
	});
}

function keytranfer_cancel(doc_no){
	var doc_no=$("#doc_no").val();
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
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

function keytranferaiao_cancel(doc_no){
	var doc_no=$("#doc_no").val();
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
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
						viewproducttemp();
					}
				}
			});
		}
	});
}



function keytranferto_cancel(){
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
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
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
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

function cancel_tranfer_ti(){
	jConfirm('Comfirm Cancle?', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/canceltranferti",data:{},success:
				function(data){
					if(data=="y"){
						$("#invoice").val("");
						$("#invoice").focus();
						$("#doc_remark").val("");
						$("#product_id").val("");
						$("#qty").val("");
						form_data();
					}
				}
			});
		}
	});
}

function check_docno(){
	$("#doc_remark").focus();
	var doc_no=$("#invoice").val();
	if(doc_no==""){
		var txt="You did not Insert Invoice No. !!";
		jAlert(txt, 'Alert', function() {
			$("#invoice").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkdiary1docno",data:{doc_no:doc_no},success:
			function(data){
				if(data=="y"){
					var txt="Product ID : "+doc_no+" repeat data transfer, please check.";
					jAlert(txt, 'Alert', function() {
						$("#invoice").focus();
					});
				}else if(data=="n"){
					viewproductkeytranfer("s");
					$.ajax({type: "POST",url: "/stock/stock/checkheaddocno",data:{doc_no:doc_no},success:
						function(data){
							if(data=="y"){
								jAlert('You must Confirm Transfer In Data', 'Alert', function() {
									$("#invoice").focus();
								});
							}else{
								$("#doc_remark").focus();
							}
						}
					});
				}
			}
		});
	}
}

function check_docno_aiao(){
	$("#doc_remark").focus();
	var doc_no=$("#invoice").val();
	if(doc_no==""){
		var txt="You did not Insert Invoice No. !!";
		jAlert(txt, 'Alert', function() {
			$("#invoice").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkdiary1docno",data:{doc_no:doc_no},success:
			function(data){
				if(data=="y"){
					var txt="Product ID : "+doc_no+" repeat data transfer, please check.";
					jAlert(txt, 'Alert', function() {
						$("#invoice").focus();
					});
				}else if(data=="n"){
					$("#password").focus();
				}
			}
		});
	}
}

function check_product(){
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var status_no=$("#status_no").val();
	var qty=$("#qty").val();
	/*if(invoice==""){
		var txt="???????????????????????????Product ID?????????????????????";
		jAlert(txt, 'Alert', function() {
			$("#invoice").focus();
		});
	}else{*/
		if(product_id==""){
			var txt="Please insert Product ID";
			jAlert(txt, 'Alert', function() {
				$("#product_id").focus();
			});
		}else{
			if(status_no=="30"){
				var page="checkproducttestterfix";
				$.ajax({type: "POST",url: "/stock/stock/"+page,data:{product_id:product_id,status_no:status_no,qty:qty},success:
					function(data){
						if(data=="n"){
							var txt="Not found Product ID : : "+product_id;
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
								return false;
							});
						}else if(data=="o"){
							var txt="Product quantity exceeded";
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
								return false;
							});
						}else{
							$("#qty").focus();
						}
					}
				});
			}else{
				var page="checkproduct";
				$.ajax({type: "POST",url: "/stock/stock/"+page,data:{product_id:product_id,status_no:status_no},success:
					function(data){
						if(data=="n"){
							var txt="Not found Product ID : : "+product_id;
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
								return false;
							});
						}else if(data=="t"){
							var txt="Must be a Tester product only ";
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
								return false;
							});
						}else{
							$("#qty").focus();
						}
					}
				});
			}
			
	//	}
	}
}

function check_qty(){
	var status_no=$("#status_no").val();
	alert(status_no);
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
	/*if(inv==""){
		var txt="????????????????????????????????????????????? Invoice";
		jAlert(txt, 'Alert', function() {
			$("#inv").focus();
		});
	}else{*/
		//$.ajax({type: "POST",url: "/stock/stock/checkinvoice",data:{inv:inv,doc_status:doc_status},success:
			//function(data){
				//if(data=="y"){
					if(product_id==""){
						var txt="Please insert Product ID";
						jAlert(txt, 'Alert', function() {
							$("#product_id").focus();
						});
					}else{
						
						if(qty==""){
							var txt="Please insert the quantity";
							jAlert(txt, 'Alert', function() {
								$("#qty").focus();
							});
						}else{
							$.ajax({type: "POST",url: "/stock/stock/checkproductinvoiceqty",data:{inv:inv,product_id:product_id,qty:qty,doc_status:doc_status},success:
								function(data){
									if(data=="1"){
										var txt="Quantity exceeded, please check";
										jAlert(txt, 'Alert', function() {
											$("#qty").focus();
										});
									}else if(data=="2"){
										var txt="Quantity exceeds the number of items in the document";
										jAlert(txt, 'Alert', function() {
											$("#qty").focus();
										});
									}else if(data=="0"){
										var txt="Quantity must more than 0 ";
										jAlert(txt, 'Alert', function() {
											$("#qty").focus();
											return false;
										});
									}else if(data=="3"){
										create_tmpdiary_to();
									}else{
										var txt="Can not process";
										jAlert(txt, 'Alert', function() {
											$("#qty").focus();
											return false;
										});
									}
								}
							});
						}
						
					}
				/*}else{
					var txt="Invoice not found : "+inv;
					jAlert(txt, 'Alert', function() {
						$("#inv").focus();
					});
				}*/
			//}
		//});
	//}
}

function get_check_qty_rq(){
	var inv=$("#inv").val();
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	var doc_status=$("#doc_status").val();
	if(inv==""){
		var txt="Please Insert Invoice No.";
		jAlert(txt, 'Alert', function() {
			$("#inv").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkinvoicerq",data:{inv:inv,doc_status:doc_status},success:
			function(data){
				if(data=="y"){
					if(product_id==""){
						var txt="Please insert Product ID";
						jAlert(txt, 'Alert', function() {
							$("#product_id").focus();
						});
					}else{
						$.ajax({type: "POST",url: "/stock/stock/checkproductinvoicerq",data:{inv:inv,product_id:product_id,doc_status:doc_status},success:
							function(data){
								if(data=="y"){
									if(qty==""){
										var txt="Please insert the quantity";
										jAlert(txt, 'Alert', function() {
											$("#qty").focus();
										});
									}else{
										$.ajax({type: "POST",url: "/stock/stock/checkproductinvoiceqtyrq",data:{inv:inv,product_id:product_id,qty:qty,doc_status:doc_status},success:
											function(data){
												if(data=="1"){
													var txt="Quantity exceeded, please check";
													jAlert(txt, 'Alert', function() {
														$("#qty").focus();
													});
												}else if(data=="2"){
													var txt="Quantity exceeds the number of items in the document";
													jAlert(txt, 'Alert', function() {
														$("#qty").focus();
													});
												}else if(data=="0"){
													var txt="Quantity must more than 0 ";
													jAlert(txt, 'Alert', function() {
														$("#qty").focus();
														return false;
													});
												}else if(data=="3"){
													create_tmpdiary_rq();
												}else{
													var txt="Can not process";
													jAlert(txt, 'Alert', function() {
														$("#qty").focus();
														return false;
													});
												}
											}
										});
									}
								}else{
									var txt="Not found the Product ID : "+product_id+" in the Invoice No. : "+inv;
									jAlert(txt, 'Alert', function() {
										$("#product_id").focus();
									});
								}
							}
						});
					}
				}else{
					var txt="Invoice not found : "+inv;
					jAlert(txt, 'Alert', function() {
						$("#inv").focus();
					});
				}
			}
		});
	}
}

function get_check_qty_testter_fix(){
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var status_no=$("#status_no").val();
	var qty=$("#qty").val();
	var doc_tp=$("#doc_tp").val();
	if(product_id==""){
		var txt="Please insert Product ID";
		jAlert(txt, 'Alert', function() {
			$("#product_id").focus();
		});
	}else{
		if(qty==""){
			var txt="Please insert the quantity";
			jAlert(txt, 'Alert', function() {
				$("#qty").focus();
			});
		}else{
			if(qty>0){
				$.ajax({type: "POST",url: "/stock/stock/checkproducttestterfix",data:{product_id:product_id,status_no:status_no,qty:qty},success:
					function(data){
						if(data=="n"){
							var txt="Not found Product ID : : "+product_id;
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
								return false;
							});
						}else if(data=="o"){
							var txt="Product quantity exceeded";
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
								return false;
							});
						}else{
							create_tmpdiary_to();
						}
					}
				});
			}else{
				var txt="Quantity must more than 0 ";
				jAlert(txt, 'Alert', function() {
					$("#qty").focus();
					return false;
				});
			}
		}
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
	}else if(doc_tp=="TO"){
		var page="checkproducttester";
	}else if(doc_tp=="RQ"){
		var page="checkproducttesterrq";
	}
	if(product_id==""){
		var txt="Please insert Product ID";
		jAlert(txt, 'Alert', function() {
			$("#product_id").focus();
		});
	}else{
		if(qty==""){
			var txt="Please insert the quantity";
			jAlert(txt, 'Alert', function() {
				$("#qty").focus();
			});
		}else{
			$.ajax({type: "POST",url: "/stock/stock/checkproduct",data:{product_id:product_id,status_no:status_no},success:
				function(data){
					if(data=="n"){
						var txt="Not found Product ID : : "+product_id;
						jAlert(txt, 'Alert', function() {
							$("#product_id").focus();
							return false;
						});
					}else if(data=="t"){
						var txt="Must be a Tester product only ";
						jAlert(txt, 'Alert', function() {
							$("#product_id").focus();
							return false;
						});
					}else{
						$.ajax({type: "POST",url: "/stock/stock/"+page,data:{product_id:product_id,status_no:status_no,qty:qty},dataType: 'json',success:
							function(data){
								if(data.status=="1"){
									var txt="Product ID : "+product_id+" have ordered the product.";
									jAlert(txt, 'Alert', function() {
										$("#qty").focus();
										return false;
									});
								}else if(data.status=="2"){
									var txt="Product ID : "+product_id+" have ordered the product.";
									jAlert(txt, 'Alert', function() {
										$("#qty").focus();
										return false;
									});
								}else if(data.status=="3"){
									var txt="orders are exceeded";
									jAlert(txt, 'Alert', function() {
										$("#qty").focus();
										return false;
									});
								}else if(data.status=="4"){
									
									if(doc_tp=="TI"){
										create_tmpdiary_to();
									}else if(doc_tp=="TO"){
										if(status_no==25){
											if(qty>2){
												var txt="Can not orders more than 2 pieces.";
												jAlert(txt, 'Alert', function() {
													$("#qty").focus();
													return false;
												});
											}else{
												create_tmpdiary_to();
											}
										}else{
											create_tmpdiary_to();
										}
									}else if(doc_tp=="RQ"){
										create_tmpdiary_rq();
									}
								}else if(data.status=="0"){
									var txt="Out of Stock";
									jAlert(txt, 'Alert', function() {
										$("#qty").focus();
										return false;
									});
									
								}else if(data.status=="6"){
									var txt="Quantity must more than 0 ";
									jAlert(txt, 'Alert', function() {
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
		var txt="Please Insert Transfer In Product ID";
		jAlert(txt, 'Alert', function() {
			$("#invoice").focus();
		});
	}else{
		if(product_id==""){
			var txt="Please insert Product ID";
			jAlert(txt, 'Alert', function() {
				$("#product_id").focus();
			});
		}else{
			if(qty==""){
				var txt="Please insert the quantity";
				jAlert(txt, 'Alert', function() {
					$("#qty").focus();
				});
			}else{
				create_tmpdiary();
			}
		}
	}
}

function gen_tmpdiary_aiao(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	var pwd=$("#password").val();
	var status_no=$("#status_no").val();
	if(qty<="0"){
		var txt="Quantity must be more than 1 piece.";
		jAlert(txt, 'Alert', function() {
			$("#qty").focus();
		});
	}else{
		if(product_id==""){
			var txt="Please insert Product ID";
			jAlert(txt, 'Alert', function() {
				$("#product_id").focus();
			});
		}else{
			if(qty==""){
				var txt="Please insert the quantity";
				jAlert(txt, 'Alert', function() {
					$("#qty").focus();
				});
			}else{
				$.ajax({type: "POST",url: "/stock/stock/checkpasswordck",data:{doc_no:invoice,pwd:pwd},success:
					function(data){
						if(data=="y"){
							$.ajax({type: "POST",url: "/stock/stock/checkproduct",data:{product_id:product_id,status_no:status_no},success:
								function(data){
									if(data=="n"){
										var txt="Not found Product ID : : "+product_id;
										jAlert(txt, 'Alert', function() {
											$("#product_id").focus();
											return false;
										});
									}else{
										create_tmpdiary();
									}
								}
							});
						}else{
							jAlert('Please Insert Password', 'Alert', function() {
								$("#password").focus();
								return false;
							});
						}
					}
				});
			}
		}
	}
}

function create_tmpdiary(){
	var type_tranfer=$("#type_tranfer").val();
	var type_product=$("#type_product").val();
	var product_id=$("#product_id").val();
	var form=$('form#frm_tranferkeyin').serialize();
	var doc_no=$("#invoice").val();
	/*$.ajax({type: "POST",url: "/stock/stock/checkdiary1docno",data:{doc_no:doc_no},success:
		function(data){
			if(data=="y"){
				var txt="Product ID : "+doc_no+" repeat data transfer, please check.";
				jAlert(txt, 'Alert', function() {
					$("#invoice").focus();
				});
			}else if(data=="n"){
				$.ajax({type: "POST",url: "/stock/stock/inserttmpdiary?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
					function(data){
						if(data=="y"){
							//$("#doc_status").attr('disabled', true);
							//$("#doc_status").removeAttr('disabled');
							viewproductkeytranfer("s");
						}else{
							var txt="Error, can not process";
							jAlert(txt, 'Alert', function() {
								$("#product_id").focus();
							});
						}
					}
				});
			}
		}
	});*/
	$.ajax({type: "POST",url: "/stock/stock/inserttmpdiary?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
		function(data){
			if(data=="y"){
				//$("#doc_status").attr('disabled', true);
				//$("#doc_status").removeAttr('disabled');
				//viewproducttemp();
				//viewproductkeytranfer("s");
				viewproducttemp();
				//check_docno();
				$("#product_id").val("");
				$("#qty").val("");
				$("#product_id").focus();
			}else{
				var txt="Error, can not process";
				jAlert(txt, 'Alert', function() {
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
	var qty=$("#qty").val();
	var status_no=$("#status_no").val();
	if(status_no=="25"){
		if(qty>2){
			var txt="Tester products can not more than 2 pieces.";
			jAlert(txt, 'Alert', function() {
				$("#qty").focus();
				return false;
			});
		}else{
			var form=$('form#frm_tranferkeyin').serialize();
			$.ajax({type: "POST",url: "/stock/stock/inserttmpdiaryto?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
				function(data){
					if(data=="y"){
						
						checktempdiary_to();
						viewproductkeytranfer_to("s");			
					}else{
						var txt="Error, can not process";
						jAlert(txt, 'Alert', function() {
							$("#product_id").focus();
						});
					}
				}
			});
		}
	}else{
		var form=$('form#frm_tranferkeyin').serialize();
		$.ajax({type: "POST",url: "/stock/stock/inserttmpdiaryto?type_tranfer="+type_tranfer+"&type_product="+type_product,data:form,success:
			function(data){
				if(data=="y"){
					
					checktempdiary_to();
					viewproductkeytranfer_to("s");			
				}else{
					var txt="Error, can not process";
					jAlert(txt, 'Alert', function() {
						$("#product_id").focus();
					});
				}
			}
		});
	}
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
				var txt="Error, can not process";
				jAlert(txt, 'Alert', function() {
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
		//$("#product_id").focus();
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

function viewproductkeytranfer_to_or(chk){
	//var doc_no=$("#invoice").val();
	$.ajax({type: "POST",url: "/stock/stock/getproducttoor",data:{chk:chk},success:
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

function viewproducttemp(){
	var form=$('form#frm_tranferkeyin').serialize();
	$.ajax({type: "POST",url: "/stock/stock/getproducttemp",data:form,success:
	function(data){
		$("#viewproducttemp").html(data);
	}}); 
}



function keytranfer_tranin2shopheadtodiary(){
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	var pwd=$("#password").val();
	jConfirm('Confirm Transfer?', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			/*$.ajax({type: "POST",url: "/stock/stock/checkdiary1docno",data:{doc_no:doc_no},success:
				function(data){
					if(data=="y"){
						var txt="Product ID : "+doc_no+" repeat data transfer, please check.";
						jAlert(txt, 'Alert', function() {
							$("#invoice").focus();
						});
					}else if(data=="n"){*/
						$.ajax({type: "POST",url: "/stock/stock/checkpasswordck",data:{doc_no:doc_no,pwd:pwd},success:
							function(data){
								if(data=="y"){
									$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryaiao",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},dataType: 'json',success:
										function(data){
											if(data.status=="y"){
												var txt="Doc. No. : "+data.doc_no;
												jAlert(txt, 'Alert', function() {
													var page="printstock?doc_no="+data.doc_no;
													window.open(page, '_blank');
													cleartxt();
													viewproducttemp();
												});	
											}else if(data.status=="trn_diary1"){
												jAlert('Error, please check table:trn_diary1', 'Alert', function() {
													
												});
											}else if(data.status=="trn_diary2"){
												jAlert('Error, please check table:trn_diary2', 'Alert', function() {
													
												});
											}else if(data.status=="com_stock_master"){
												jAlert('Error, please check table:com_stock_master', 'Alert', function() {
													
												});									
											}else{
												jAlert('Error, please check the data', 'Alert', function() {
													
												});
											}
										}
									});
								}
							}
						});
					/*}
				}
			});*/
		}
	});
}

function keytranfer_tranin2shopheadtodiary_to(){
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	var doc_tp=$("#doc_tp").val();
	var status_no=$("#status_no").val();
	var inv=$("#inv").val();
	jConfirm('Confirm Transfer?', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryto",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no,inv:inv},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="Doc. No. : "+data.doc_no;
					jAlert(txt, 'Alert', function() {
						$.ajax({type: "POST",url: "/stock/stock/printtranto",data:{doc_no:data.doc_no},success:
						function(data){
							checktempdiary_to();
							cleartxt();
						}});
					});
				}else if(data.status=="trn_diary1"){
					jAlert('Error, please check table:trn_diary1', 'Alert', function() {
						
					});
				}else if(data.status=="trn_diary2"){
					jAlert('Error, please check table:trn_diary2', 'Alert', function() {
						
					});
				}else if(data.status=="com_stock_master"){
					jAlert('Error, please check table:com_stock_master', 'Alert', function() {
						
					});									
				}else{
					jAlert('Error, please check the data', 'Alert', function() {
						
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
	jConfirm('Confirm Transfer?', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryrq",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no,inv:inv},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="Doc. No. : "+data.doc_no;
					jAlert(txt, 'Alert', function() {
						
						$.ajax({type: "POST",url: "/stock/stock/printrq",data:{doc_no:data.doc_no},success:
						function(data){
							checktempdiary_rq();
							cleartxt();
						}});
						
						
					});
				}else if(data.status=="trn_diary1"){
					jAlert('Error, please check table:trn_diary1', 'Alert', function() {
						
					});
				}else if(data.status=="trn_diary2"){
					jAlert('Error, please check table:trn_diary2', 'Alert', function() {
						
					});
				}else if(data.status=="com_stock_master"){
					jAlert('Error, please check table:com_stock_master', 'Alert', function() {
						
					});									
				}else{
					jAlert('Error, please check the data', 'Alert', function() {
						
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
				jAlert('Error, please check!', 'Alert', function() {
					$("#product_id").focus();
				});	
			}else{
				var status_no=$("#status_no").val();
				var tbl="trn_diary1";
				$.ajax({type: "POST",url: "/stock/stock/getdocnoto",data:{status_no:status_no,tbl:tbl},success:
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
				jAlert('Can not process!', 'Alert', function() {
					$("#product_id").focus();
				});	
			}else{
				var tbl="trn_diary1";
				var status_no=$("#status_no").val();
				$.ajax({type: "POST",url: "/stock/stock/getdocnoto",data:{status_no:status_no,tbl:tbl},success:
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
		var txt="You have not entered a Doc. No.";
		jAlert(txt, 'Alert', function() {
			$("#doc_type").focus();
		});
	}else{
		if(doc_no==""){
			var txt="Doc. No. not found";
			jAlert(txt, 'Alert', function() {
				$("#doc_type").focus();
			});
		}else{
			var form=$('form#frm').serialize();
			$.ajax({type: "POST",url: "/stock/miscellaneous/editcontactbillvt",data:form,success:
				function(data){
					if(data=="y"){
						jAlert('Save success', 'Alert', function() {
							$("#fullname").val("");
							$("#addr1").val("");
							$("#addr2").val("");
							$("#addr3").val("");
							$("#edit_doc_on").val("");
							$("#doc_tpye").val("");
						});
					}else{
						jAlert('Can not save, please check', 'Alert', function() {
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
			jAlert('Save success', 'Alert', function() {
				
			});
		}else{
			jAlert('Can not save, please check', 'Alert', function() {
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
		jAlert('You have not entered the no. of Barcode', 'Alert', function() {
			$("#i_barcode").focus();
		});
	}else{
		jConfirm('Confirm Save?', 'Alert', function(r) {
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
							var txt="Error, can not process";
							jAlert(txt, 'Alert', function() {
								
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

function check_product_master(){
	var invoice=$("#invoice").val();
	var product_id=$("#product_id").val();
	var status_no=$("#status_no").val();
	/*if(invoice==""){
		var txt="???????????????????????????Product ID?????????????????????";
		jAlert(txt, 'Alert', function() {
			$("#invoice").focus();
		});
	}else{*/
		if(product_id==""){
			var txt="Please insert Product ID";
			jAlert(txt, 'Alert', function() {
				$("#product_id").focus();
			});
		}else{
			$.ajax({type: "POST",url: "/stock/stock/checkproduct",data:{product_id:product_id,status_no:status_no},success:
				function(data){
					if(data=="n"){
						var txt="Not found Product ID : : "+product_id;
						jAlert(txt, 'Alert', function() {
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

function gen_tmp2headshop(doc_tp,status_no){
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	if(product_id==""){
		var txt="Please insert Product ID";
		jAlert(txt, 'Alert', function() {
			$("#product_id").focus();
		});
	}else{
		var flag=approved_gen_item(qty,product_id);
		if(flag=="y"){
			add_tmpdiary(doc_tp,status_no);
		}else{
			return false;
			/*jAlert(flag, 'Alert', function() {
				$("#qty").val("");
				$("#qty").focus();
			});*/
		}
	}
}

function approved_gen_item(qty,product_id){
	var chk="y";
	var flag1=check_qty(qty);
	if(flag1=="y"){
		var flag2=product_id_master(product_id);
		if(flag2!="y"){
			var chk="n";
		}
	}else{
		var chk="n";
	}
	if(chk=="y"){
		var rs="y";
		return rs;
	}
}

function check_qty(qty){
	var check_qty = isNaN(qty);
	if(check_qty==true){
		var status="Please insert the quantity only numbers";
	}else{
		if(qty==""){
			var status="Please insert the quantity";
			jAlert(status, 'Alert', function() {
				$("#qty").focus();
				return false
			});
		}else if(qty<1){
			var status="Quantity must be more than 0 pieces.";
			jAlert(status, 'Alert', function() {
				$("#qty").focus();
				return false
			});
		}else{
			var status="y";
			return status;
		}
	}
}

function product_id_master(product_id){
	var rs="";
	if(product_id==""){
		var txt="Please insert Product ID";
		jAlert(txt, 'Alert', function() {
			$("#product_id").focus();
		});
	}else{
		$.ajax({type: "POST",url: "/stock/stock/checkproductid",async: false,data:{product_id:product_id},success:
			function(data){
				if(data=="y"){
					rs="y";
				}else{
					var txt="Not found Product ID : : "+product_id;
					jAlert(txt, 'Alert', function() {
						$("#product_id").focus();
						return false;
					});
				}
			}
		});
	}
	return rs;
}

function add_tmpdiary(doc_tp,status_no){
	var product_id=$("#product_id").val();
	var form=$('form#frm_tranferkeyti').serialize();
	$.ajax({type: "POST",url: "/stock/stock/addtmpdiary?doc_tp="+doc_tp+"&status_no="+status_no,data:form,success:
		function(data){
			if(data=="y"){
				form_data();
				focus_keytranfer();
			}else{
				jAlert("Error, can not process", 'Alert', function() {
					$("#product_id").focus();
				});
			}
		}
	});
}

function focus_keytranfer(){
	$("#product_id").val("");
	$("#qty").val("");
	$("#product_id").focus();
}

function tranfer_in2shop(doc_tp,status_no){
	var doc_no=$("#invoice").val();
	var doc_remark=$("#doc_remark").val();
	jConfirm('Comfirm Transfer?', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>Please wait...</b></font></center>");
			$( "#dialog_process" ).dialog( "open" );
			$.ajax({type: "POST",url: "/stock/stock/tranin2shopheadtodiaryaiao",data:{doc_no:doc_no,doc_remark:doc_remark,doc_tp:doc_tp,status_no:status_no},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					$( "#dialog_process" ).dialog( "close" );
					var txt="Doc. No. : "+data.doc_no;
					jAlert(txt, 'Alert', function() {
						var page="printstock?doc_no="+data.doc_no;
						window.open(page, '_blank');
						form_data()
					});	
				}else{
					jAlert('Error, please check the data', 'Alert', function() {
						clear_diary(doc_no);
					});
				}
			}});
		}
	});
}

function page_disable(msg){
	$(function() {
		$("#dialog_disable").html(msg);
		$( "#dialog_disable" ).dialog( "open" );
	});
}