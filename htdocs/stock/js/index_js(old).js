$(document).ready( function() {
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
	$( "#dialog" ).dialog({
		autoOpen: false,
		height: 300,
		width: 600,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		buttons: {
			"บันทึก": function() {
				var chk_func_save=$("#chk_func_save").val();
				if(chk_func_save=="shelf"){
					save_shelf();
				}else if(chk_func_save=="floor"){
					save_floor();
				}else if(chk_func_save=="room"){
					save_room();
				}
			},
			"ยกเลิก": function() {
				//alert("aa");
				//view();
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	$( "#dialog_product" ).dialog({
		autoOpen: false,
		width: 800,
		height: 500,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		close: function(){view();getshelfdata();}
	});
	
	$( "#dialog_product_listtag" ).dialog({
		autoOpen: false,
		width: 800,
		height: 500,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		close: function(){getshelfdata();}
	});
	
	$( "#dialog_tranfer" ).dialog({
		autoOpen: false,
		width: 350,
		height: 300,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		close: function(){view();getshelfdata();}
	});
	
	$( "#dialog_process" ).dialog({
		autoOpen: false,
		height: 100,
		width: 200,
		modal: true,
		resizable: false,
		open: function(event, ui) {
        $(".ui-widget-overlay").css('opacity',0.3);
        //$(".ui-dialog-titlebar-close").hide(); 
        $(".ui-dialog-titlebar").hide();
		//$(".ui-dialog:first").find(".ui-dialog-titlebar").css("display", "none");
        //$(".ui-dialog").css("border", 5);
        //$(".ui-dialog:first").find(".ui-dialog-content").css("border", 5);
		//$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css("background", "#fff");
		//$(".ui-dialog:first").find(".ui-dialog-content").css("overflow", "hidden");

    	}
	});
	
	$( "#dialog_get_docno" ).dialog({
		autoOpen: false,
		height: 200,
		width: 300,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		buttons: {
			"พิมพ์": function() {
	    		print_doc_no_old();
			},
			"ยกเลิก": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	
	
	
	$( "#view_productcheckdif" ).dialog({
		autoOpen: false,
		width: 600,
		height: 300,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		modal: true
	});
	
	$( "#view_orderby" ).dialog({
		autoOpen: false,
		height: 300,
		width: 300,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		buttons: {
			"พิมพ์": function() {
				print_diff_stock();
			},
			"ยกเลิก": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	
	$( "#dialog_preview" ).dialog({
		autoOpen: false,
		height: 500,
		width: 400,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		buttons: {
			"พิมพ์": function() {
				print_count_list_tag_out();
			},
			"ยกเลิก": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	
	
	$( "#view_password" ).dialog({
		
			autoOpen: false,
			width: 250,
			height: 150,
			open: function(event, ui) {
		        $(".ui-widget-overlay").css('opacity',0.3);
		        $(".ui-dialog-titlebar").show();
		    },
			modal: true

		
	});
	
	$( "#dialog_add_shlef" ).dialog({
		/*autoOpen: false,
		height: 200,
		width: 300,
		modal: true,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	    },
		buttons: {
			"ตกลง": function() {
	    		save_shelf_ck();
			},
			"ยกเลิก": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}*/
		autoOpen: false,
		width: 200,
		height: 120,
		open: function(event, ui) {
	        $(".ui-widget-overlay").css('opacity',0.3);
	        $(".ui-dialog-titlebar").show();
	    },
		modal: true
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
	
	$(".nexttxt").live("keydown",function(event){
		var product_id=$(this).attr('id');
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		if(keyCode==13){
			$(this).parents().parents().next().find('.nexttxt').focus().select();
			savelisttag(product_id);
		}
		
		if(keyCode==40){
			$(this).parents().parents().next().find('.nexttxt').focus().select();
			savelisttag(product_id);
		}
		
		if(keyCode==38){
			$(this).parents().parents().prev().find('.nexttxt').focus().select();
			savelisttag(product_id);
		}
	})	
});

function process_stock(){
	var pwd=$("#pwd").val();
	$.ajax({type: "POST",url: "/stock/checkstock/checkpassword",data:{pwd:pwd},success:
		function(data){
			if(data=="y"){
				var doc_no=$("#doc_no").val();
				var txt="ยืนยันการประมวลผลสินค้าขาดเกินเอกสารเลขที่ "+doc_no;
				jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
				if(r==true){
					$( "#view_password" ).dialog( "close" );
					$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>กำลังประมวลผล...</b></font></center>");
					$( "#dialog_process" ).dialog( "open" );
					$.ajax({type: "POST",url: "/stock/checkstock/tranferstock",data:{doc_no:doc_no},success:
					function(data){
						if(data=="y"){
							
							$( "#dialog_process" ).dialog( "close" );
							jAlert('โอนข้อมูลยอดขาดเกินเรียบร้อยแล้ว!', 'แจ้งเตือน');
						}else{
							$( "#dialog_process" ).dialog( "close" );
							jAlert('ไม่สามารถดำเนินการได้!', 'แจ้งเตือน');
						}
					}
					});
				}
				});
				
			}else{
				jAlert('ไม่พบรหัสผ่าน!', 'แจ้งเตือน');
			}
		}
	});
}

function savelisttag(product_id){
	var form=$('form#onviewproduct').serialize();
	$.ajax({type: "POST",url: "/stock/checkstock/savelisttag?chkproductid="+product_id,data:form,success:
		function(data){
			
		}
	});
}

function savelisttagroom(product_id,room,sproduct_id,doc_no,seq){
	var qty=$("#"+product_id).val();
	
	$.ajax({type: "POST",url: "/stock/checkstock/savelisttagroom",data:{room_no:room,product_id:sproduct_id,qty:qty,doc_no:doc_no,seq:seq},success:
		function(data){
			
		}
	});
}

function open_product(shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/productonshelf",data:{shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
	function(data){
		$("#dialog_product").html(data);
		$( "#dialog_product" ).dialog( "open" );
	}}); 
}

function tranfer_room(shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/producttranferroom",data:{shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
	function(data){
		$("#dialog_tranfer").html(data);
		$( "#dialog_tranfer" ).dialog( "open" );
	}}); 
}

function open_keyproduct(doc_no,shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/keyproductonshelf",data:{doc_no:doc_no,shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
	function(data){
		$("#dialog_product_listtag").html(data);
		$( "#dialog_product_listtag" ).dialog( "open" );
	}}); 
}

view();
function view(){
	$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>กำลังดึงข้อมูล...</b></font></center>");
	$( "#dialog_process" ).dialog( "open" );
	var shelf=$("#sel_shelf").val();
	$.ajax({type: "POST",url: "/stock/checkstock/viewshelf",data:{shelf:shelf},success:
		function(data){
			$( "#dialog_process" ).dialog( "close" );
			$("#view").html(data);
		}}); 
}

function add_shelf(shelf_no){
	frm_shelf(shelf_no);
	$( "#dialog" ).dialog( "open" );
		return false;
}

function frm_shelf(shelf_no){
	$.ajax({type: "POST",url: "/stock/checkstock/formaddshelf",data:{shelf_no:shelf_no},success:
	function(data){
		$("#dialog").html(data);
		//$("#shelf_no").focus();
	}}); 
}

function save_shelf(){
	var shelf_no=$("#shelf_no").val();
	if(shelf_no==""){
		jAlert('คุณยังไม่กรอก รหัส Shelf', 'แจ้งเตือน',function(r) {
			$("#shelf_no").focus();
			return  false;
		});
		
	}else{
		jConfirm("บันทึกข้อมูล", 'ยืนยันการทำงาน', function(r) {
			if(r==true){
				//var form=$('form#frm_shelf').serialize();
				var chk_func_save=$("#chk_func_save").val();
				var shelf_desc=$("#shelf_desc").val();
				var shelf_no=$("#shelf_no").val();
				var shelf_no_up=$("#shelf_no_up").val();
				
				$.ajax({type: "POST",url: "/stock/checkstock/saveshelf",data:{chk_func_save:chk_func_save,shelf_desc:shelf_desc,shelf_no:shelf_no,shelf_no_up:shelf_no_up},success:
					function(data){
						if(data=="y"){
							view();
							$( "#dialog" ).dialog( "close" );
						}else if(data=="w"){
							jAlert('รหัส Shelf นี้มีการสร้างไว้แล้ว!', 'แจ้งเตือน');
						}else{
							jAlert('เกิดความผิดพลาดไม่สามารถบันทึกได้!', 'แจ้งเตือน');
						}
					}
				}); 
			}
		});
	}
}

function frmaddshelf(){
	$.ajax({type: "POST",url: "/stock/checkstock/frmaddshelf",data:{},success:
		function(data){
			$( "#dialog_add_shlef" ).html(data);
			$( "#dialog_add_shlef" ).dialog( "open" );
		}
	}); 
}

function save_shelf_ck(){
	var doc_no=$("#doc_no").val();
	var shelf_no=$("#shelf_no").val();
	if(shelf_no==""){
		jAlert('คุณยังไม่กรอก รหัส Shelf', 'แจ้งเตือน', function() {
			$("#shelf_no").focus();
			return false;
		});
	}else{
		jConfirm("บันทึกข้อมูล", 'ยืนยันการทำงาน', function(r) {
			if(r==true){
				//var form=$('form#frm_shelf').serialize();
				var chk_func_save=$("#chk_func_save").val();
				var shelf_desc=$("#shelf_desc").val();
				var shelf_no_up=$("#shelf_no_up").val();
				$.ajax({type: "POST",url: "/stock/checkstock/saveshelf",data:{shelf_no:shelf_no,chk_func_save:chk_func_save,shelf_desc:shelf_desc,shelf_no_up:shelf_no_up},success:
					function(data){
						if(data=="y"){
							$( "#dialog_add_shlef" ).dialog( "close" );
							var floor_no=shelf_no+"-01";
							var room_no=floor_no+"-01";
							open_keyproduct(doc_no,shelf_no,floor_no,room_no);
						}else if(data=="w"){
							jAlert('รหัส Shelf นี้มีการสร้างไว้แล้ว!', 'แจ้งเตือน');
						}else if(data=="n"){
							$.ajax({type: "POST",url: "/stock/checkstock/tranfershelfmastertolisttag",data:{shelf_no:shelf_no,doc_no:doc_no},success:
							function(data){
								$( "#dialog_add_shlef" ).dialog( "close" );
								var floor_no=shelf_no+"-01";
								var room_no=floor_no+"-01";
								open_keyproduct(doc_no,shelf_no,floor_no,room_no);
							}}); 
							
						}else{
							jAlert('เกิดความผิดพลาดไม่สามารถบันทึกได้!', 'แจ้งเตือน');
						}
					}
				}); 
			}
		});
	}
}

function del_shelf(shelf_no){
	var txt="ยืนยันการลบ Shelf รหัส "+shelf_no;
	//$.alerts.dialogClass = $(this).attr('style_1'); // set custom style class
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			$.ajax({type: "POST",url: "/stock/checkstock/deleteshelf",data:{shelf_no:shelf_no},success:
				function(data){
					if(data=="n"){
						jAlert('เกิดความผิดพลาดไม่สามารถลบได้', 'แจ้งเตือน');
						//alert("เกิดความผิดพลาดไม่สามารถลบได้!");	
						return false;
					}else if(data=="w"){
						//alert("ยังมีชั้นวางสินค้าไม่สามารถลบได้!");
						jAlert('ยังมีชั้นวางสินค้าไม่สามารถลบได้', 'แจ้งเตือน');
					}else{
						view();
					}
				}
			});
		}else{
			return false;
		}
		/*$.ajax({type: "POST",url: "/stock/checkstock/deleteshelf",data:{shelf_no:shelf_no},success:
			function(data){
				if(data=="n"){
					alert("เกิดความผิดพลาดไม่สามารถลบได้!");	
					return false;
				}else if(data=="w"){
					alert("ยังมีชั้นวางสินค้าไม่สามารถลบได้!");
				}else{
					view();
				}
			}
		}); */
		
		//jAlert('Confirmed: ' + r, 'Confirmation Results');
	});
	
	/*if (answer_ques){
		$.ajax({type: "POST",url: "/stock/checkstock/deleteshelf",data:{shelf_no:shelf_no},success:
			function(data){
				if(data=="n"){
					alert("เกิดความผิดพลาดไม่สามารถลบได้!");	
					return false;
				}else if(data=="w"){
					alert("ยังมีชั้นวางสินค้าไม่สามารถลบได้!");
				}else{
					view();
				}
			}
		}); 
	}*/
}

function add_floor(shelf_no,floor_no){
	frm_floor(shelf_no,floor_no);
	$( "#dialog" ).dialog( "open" );
		return false;
}

function frm_floor(shelf_no,floor_no){
	$.ajax({type: "POST",url: "/stock/checkstock/formaddfloor",data:{shelf_no:shelf_no,floor_no:floor_no},success:
	function(data){
		$("#dialog").html(data);
	}}); 
}

function save_floor(){
	var floor_no=$("#floor_no").val();
	if(floor_no==""){
		jAlert('คุณยังไม่กรอก รหัสชั้นวาง', 'แจ้งเตือน');
		$("#floor_no").focus();
		return  false;
	}else{
		jConfirm('บันทึกข้อมูล', 'ยืนยันการทำงาน', function(r) {
			if(r==true){
			//var form=$('form#frm_floor').serialize();	
				var chk_func_save=$("#chk_func_save").val();
				var shelf_no=$("#shelf_no").val();
				var floor_no=$("#floor_no").val();
				var floor_desc=$("#floor_desc").val();
				var floor_no_up=$("#floor_no_up").val();
				$.ajax({type: "POST",url: "/stock/checkstock/savefloor",
					data:{chk_func_save:chk_func_save,shelf_no:shelf_no,floor_no:floor_no,floor_desc:floor_desc,floor_no_up:floor_no_up},
					success:
					function(data){
						if(data=="y"){
							view();
							$( "#dialog" ).dialog( "close" );
						}else if(data=="w"){
							jAlert('รหัสชั้นวางนี้มีการสร้างไว้แล้ว!', 'แจ้งเตือน');
						}else{
							jAlert('เกิดความผิดพลาดไม่สามารถบันทึกได้!', 'แจ้งเตือน');
						}
					}
				}); 
			}
		});
	}
}

function del_floor(shelf_no,floor_no){
	var txt="ยืนยันการลบชั้นวาง "+floor_no;
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			$.ajax({type: "POST",url: "/stock/checkstock/deletefloor",data:{shelf_no:shelf_no,floor_no:floor_no},success:
				function(data){
					if(data=="n"){
						jAlert('เกิดความผิดพลาดไม่สามารถลบได้!', 'แจ้งเตือน');
						return false;
					}else if(data=="w"){
						jAlert('ยังมีห้องวางสินค้าในชั้นวางไม่สามารถลบได้!', 'แจ้งเตือน');
					}else{
						view();
					}
				}
			}); 
		}
	});
}


function add_room(shelf_no,floor_no,room_no){
	frm_room(shelf_no,floor_no,room_no);
	$( "#dialog" ).dialog( "open" );
		return false;
}

function frm_room(shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/formaddroom",data:{shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
		function(data){
			$("#dialog").html(data);
		}}); 
}

function save_room(){
	var room_no=$("#room_no").val();
	if(room_no==""){
		jAlert('คุณยังไม่กรอก รหัสห้องวางสินค้า', 'แจ้งเตือน');
		$("#room_no").focus();
		return  false;
	}else{
		jConfirm('บันทึกข้อมูล', 'ยืนยันการทำงาน', function(r) {
			if(r==true){
				//var form=$('form#frm_room').serialize();
				var room_no=$("#room_no").val();
				var chk_func_save=$("#chk_func_save").val();
				var shelf_no=$("#shelf_no").val();
				var floor_no=$("#floor_no").val();
				var room_desc=$("#room_desc").val();
				var room_no_up=$("#room_no_up").val();
				
				$.ajax({type: "POST",url: "/stock/checkstock/saveroom",
					data:{room_no:room_no,chk_func_save:chk_func_save,shelf_no:shelf_no,floor_no:floor_no,room_desc:room_desc,room_no_up:room_no_up},
					success:
					function(data){
						if(data=="y"){
							view();
							$( "#dialog" ).dialog( "close" );
						}else if(data=="w"){
							jAlert('รหัสห้องวางสินค้า นี้มีการสร้างไว้แล้ว!', 'แจ้งเตือน');
						}else{	
							jAlert('เกิดความผิดพลาดไม่สามารถบันทึกได้!', 'แจ้งเตือน');
						}
					}
				}); 
			}
		}); 
	}
}

function del_room(shelf_no,floor_no,room_no){
	var txt="ยืนยันการลบห้องวางสินค้ารหัส "+room_no;
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			$.ajax({type: "POST",url: "/stock/checkstock/deleteroom",data:{shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
				function(data){
					if(data=="n"){
						jAlert('เกิดความผิดพลาดไม่สามารถลบได้!', 'แจ้งเตือน');
						return false;
					}else if(data=="w"){
						jAlert('ยังมีสินค้าในชั้นวางไม่สามารถลบได้!', 'แจ้งเตือน');
						return false;
					}else{
						view();
					}
				}
			}); 
		}
	});
}


function print_shelf(){
			var form=$('form#frm_print_list_tag').serialize();
			$.ajax({type: "POST",url: "/stock/checkstock/printshelf",data:form,success:
				function(data){
					var data = data.split(':::::');
					if(data[1]=="n"){
						jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
						return false;
					}else{
						var txt_confirm_print="ต้องการพิมพ์กด OK";
						jConfirm(txt_confirm_print, 'ยืนยันการทำงาน', function(r) {
							if(r==true){
								$("#shlef").val(data[1]);
								$("#doc_no").val(data[0]);
								$.ajax({type: "POST",url: "/stock/checkstock/printshelfno",data:{shelf:data[1]},success:
									function(data){
									
									}
								});
							}
						});
					}
				}
			});
			/*var form=$('form#frm_print_list_tag').serialize();
			$.ajax({type: "POST",url: "/stock/checkstock/getdoconprintlisttag",data:form,success:
				function(data){
					var data = data.split(':::::');
					if(data[1]=="n"){
						jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
						return false;
					}else{
						var txt_confirm_print="เอกสารเลขที่ : "+data[0]+" ต้องการพิมพ์กด OK";
						jConfirm(txt_confirm_print, 'ยืนยันการทำงาน', function(r) {
							if(r==true){
								$("#shlef").val(data[1]);
								$("#doc_no").val(data[0]);
								$.ajax({type: "POST",url: "/stock/checkstock/printlisttagold",data:{doc_no_old:data[0]},success:
									function(data){
									
									}
								});
							}
						});
					}
				}
			}); */
}

function viewgenlisttag(){
	$.ajax({type: "POST",url: "/stock/checkstock/viewgenlisttag",data:{},success:
		function(data){
			$("#viwgenlisttag").html(data);
		}
	}); 
}

function print_list_tag(){
	$.ajax({type: "POST",url: "/stock/checkstock/checkrunck",data:{},success:
		function(data){
			if(data=="y"){
				var txt="ยืนยันการรันเลขที่เอกสาร";
				jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
					if(r==true){
						$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>กำลังประมวลผล...</b></font></center>");
						$( "#dialog_process" ).dialog( "open" );
						var form=$('form#frm_print_list_tag').serialize();
						$.ajax({type: "POST",url: "/stock/checkstock/getdoconprintlisttag",data:form,success:
							function(data){
							var data = data.split(':::::');
								if(data[1]=="n"){
									jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
									return false;
								}else{
									$( "#dialog_process" ).dialog( "close" );
									var txt_confirm_print="เอกสารเลขที่ : "+data[0]+" ต้องการพิมพ์กด OK";
									jConfirm(txt_confirm_print, 'ยืนยันการทำงาน', function(r) {
										if(r==true){
											$("#shlef").val(data[1]);
											$("#doc_no").val(data[0]);
											$.ajax({type: "POST",url: "/stock/checkstock/printlisttagold",data:{doc_no_old:data[0]},success:
												function(data){
													
												}
											});
										}
									});
								}
							}
						}); 
					}
				});
			}else if(data=="n"){
				jAlert('วันนี้มีการรันเลขที่เอกสารแล้ว ไม่สามารถทำรายการได้', 'แจ้งเตือน', function(r){return false;});
			}else{
				jAlert('เกิดความผิดผลาด ไม่สามารถทำรายการได้', 'แจ้งเตือน', function(r){return false;});
			}
		}
	});
	
	/*var txt="ยืนยันการรันเลขที่เอกสาร";
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>กำลังประมวลผล...</b></font></center>");
			$( "#dialog_process" ).dialog( "open" );
			var form=$('form#frm_print_list_tag').serialize();
			$.ajax({type: "POST",url: "/stock/checkstock/getdoconprintlisttag",data:form,success:
				function(data){
				var data = data.split(':::::');
					if(data[1]=="n"){
						jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
						return false;
					}else{
						$( "#dialog_process" ).dialog( "close" );
						var txt_confirm_print="เอกสารเลขที่ : "+data[0]+" ต้องการพิมพ์กด OK";
						jConfirm(txt_confirm_print, 'ยืนยันการทำงาน', function(r) {
							if(r==true){
								$("#shlef").val(data[1]);
								$("#doc_no").val(data[0]);
								$.ajax({type: "POST",url: "/stock/checkstock/printlisttagold",data:{doc_no_old:data[0]},success:
									function(data){
										
									}
								});
							}
						});
					}
				}
			}); 
		}
	});*/
}


function print_doc_no(){
	$.ajax({type: "POST",url: "/stock/checkstock/checkdocno",data:{},success:
		function(data){
			if(data=="n"){
				jAlert('ยังไม่มีการรันเลขที่เอกสาร', 'แจ้งเตือน');
			}else{
				$( "#dialog_get_docno" ).html(data);
				$( "#dialog_get_docno" ).dialog( "open" );
				return false;
			}
		}
	}); 
}

function print_doc_no_old(){
	$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>กำลังพิมพ์...</b></font></center>");
	$( "#dialog_process" ).dialog( "open" );
	var doc_no_lod=$("#doc_no_old").val();
	$.ajax({type: "POST",url: "/stock/checkstock/printlisttagold",data:{doc_no_old:doc_no_lod},success:
		function(data){
			$( "#dialog_process" ).dialog( "close" );
			$( "#dialog_get_docno" ).dialog( "close" );
		}
	}); 
}

function getshelfdata(){
	var doc_no=$("#doc_no").val();
	//var s_product_id=$("#search_product_id").val();
	var s_product_id="";
	$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>Wait...</b></font></center>");
	$( "#dialog_process" ).dialog( "open" );
	$.ajax({type: "POST",url: "/stock/checkstock/keyshelf",data:{doc_no:doc_no,product_id:s_product_id},success:
		function(data){
			var p_product_id=$("#search_product_id").val();
			if(p_product_id!=""){
				$( "#dialog_process" ).dialog( "close" );
				$("#search_product_id").focus().select();
			}else{
				$( "#dialog_process" ).dialog( "close" );
				$("#search_room").focus().select();
			}
			$("#view_key_shelf").html(data);
		}
	}); 
}

function getcountshelfdata(){
	var doc_no=$("#doc_no").val();
	$.ajax({type: "POST",url: "/stock/checkstock/getshelf",data:{doc_no:doc_no},success:
		function(data){
			$("#view_count_shelf").html(data);
		}
	});
}

function save_list_tag(event,chk_product_id,n_num){
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	keyCode = event.which; // Firefox   
	//alert(n_num);
	if (keyCode == 13) {
		var form=$('form#onviewproduct').serialize();
		$.ajax({type: "POST",url: "/stock/checkstock/savelisttag?chkproductid="+chk_product_id,data:form,success:
			function(data){
				/*var tot_idx = $('body').find('input[type=text]').length;
				if(tot_idx == n_num){
					$('input[type=text]:eq(0)').focus();
				}else{
					var num=parseInt(n_num)+1;
					$('input[type=text]:eq('+num+')').focus();
				}*/
				
			}
		});
	}
}

function update_seq(event,room){
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	keyCode = event.which; // Firefox    
	if (keyCode == 13) {
		var form=$('form#onviewproduct').serialize();
		$.ajax({type: "POST",url: "/stock/checkstock/updateseqroom?room="+room,data:form,success:
			function(data){
				viewproductonshelf();
			}
		});
	}
}

function print_count_list_tag(){
	var form=$('form#frm_print_count_list_tag').serialize();
	$.ajax({type: "POST",url: "/stock/checkstock/printcountlisttag",data:form,success:
		function(data){
		var data = data.split(':::::');
			if(data[1]=="\"n\""){
				jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
				return false;
			}else{
				$("#shlef").val(data[1]);
				$("#doc_no").val(data[0]);
				$.ajax({type: "POST",url: "/stock/checkstock/printcounttagpaper",data:{shelf:data[1],doc_no:data[0]},success:
					function(data){
						$("#dialog_preview").html(data);
						$( "#dialog_preview" ).dialog( "open" );
					}
				});
			}
		}
	}); 
}


function print_count_list_tag_out(){
	var txt="ยืนยันการพิมพ์เอกสาร";
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		/*if(r==true){
			var form=$('form#frm_print_count_list_tag').serialize();
			
			$.ajax({type: "POST",url: "/stock/checkstock/printcountlisttag",data:form,success:
				function(data){
					if(data[1]=="n"){
						jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
						return false;
					}else{
						var data = data.split(':::::');
						$("#shlef").val(data[1]);
						$("#doc_no_pdf").val(data[0]);
						//var page="printcounttag?doc_no="+data[0];
						//window.open(page, '_blank');
						//$("form#send_countshelf").submit();
						$.ajax({type: "POST",url: "/stock/checkstock/printcounttag",data:{shelf:data[0]},success:
							function(data){
							
							}
						});
					}
				}
			}); 
		}*/
		
		
		
		if(r==true){
			$( "#dialog_process" ).html("<center><font color='#059562'><img src='/stock/img/ajax-loader.gif' border='0' align='absmiddle'><br><b>กำลังประมวลผล...</b></font></center>");
			$( "#dialog_process" ).dialog( "open" );
			var form=$('form#frm_print_count_list_tag').serialize();
			$.ajax({type: "POST",url: "/stock/checkstock/printcountlisttag",data:form,success:
				function(data){
				var data = data.split(':::::');
					if(data[1]=="n"){
						jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
						return false;
					}else{
						$( "#dialog_process" ).dialog( "close" );
						var txt_confirm_print="เอกสารเลขที่ : "+data[0]+" ต้องการพิมพ์กด OK";
						jConfirm(txt_confirm_print, 'ยืนยันการทำงาน', function(r) {
							if(r==true){
								$("#shlef").val(data[1]);
								$("#doc_no").val(data[0]);
								$.ajax({type: "POST",url: "/stock/checkstock/printcounttag",data:{shelf:data[1],doc_no:data[0]},success:
									function(data){
										
									}
								});
							}
						});
					}
				}
			}); 
		}
		
		
		
		
	});
}



function getdifcheck(){
	var doc_no=$("#doc_no").val();
	if(doc_no!=""){
		$.ajax({type: "POST",url: "/stock/checkstock/getdifcheck",data:{doc_no:doc_no},success:
			function(data){
				$("#view_difcheck").html(data);
			}
		}); 
	}
}

function print_diff_stock(){
	var doc_no=$("#doc_no").val();
	var difby=$('input[name=sel_rpt]:checked').val();
	var orderby=$("#orderby").val();
	$.ajax({type: "POST",url: "/stock/checkstock/getarrdifstock",data:{doc_no:doc_no,difby:difby,orderby:orderby},success:
		function(data){
			var data = data.split(':::::');
			var difby=$('input[name=sel_rpt]:checked').val();
			$("#product_id_pdf").val(data[1]);
			$("#doc_no_pdf").val(data[0]);
			$("#sel_rpt_pdf").val(difby);

			$("form#send_print_dif").submit();
		}
	});

}

function balance_product(){
	var doc_no=$("#doc_no").val();
	//var form=$('form#frm_orderby').serialize();
	$.ajax({type: "POST",url: "/stock/checkstock/getbalanceproduct",data:{doc_no:doc_no},success:
		function(data){
			$("#view_balanceproduct").html(data);
		}
	});
}

function tranfer_stock(){
	var doc_no=$("#doc_no").val();
	
	jConfirm("ยืนยันการปรับยอดสินค้า", 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			$.ajax({type: "POST",url: "/stock/checkstock/tranferstock",data:{doc_no:doc_no},success:
				function(data){
					if(data=="y"){
						jAlert('ทำการปรับยอดสินค้าเรียบร้อยแล้ว!', 'แจ้งเตือน');
					}else{
						jAlert('ไม่สามารถดำเนินการได้!', 'แจ้งเตือน');
						return false;
					}
				}
			});
		}else{
			return false;
		}
	});
}

function select_shelf(){
	//var shelf=$("#sel_shelf").val();
	view();
}

function search_product_listtag(event){
	var s_product_id=$("#search_product_id").val();
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if(keyCode==13){
			getshelfdata();
	}
}

function search_product_listtag_room(event){
	var s_product_id=$("#search_product_id").val();
	var doc_no=$("#doc_no").val();
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if(keyCode==13){
		//var form=$('form#frm_tranfer_product').serialize();
		$.ajax({type: "POST",url: "/stock/checkstock/keyproductonroom",data:{product_id:s_product_id,doc_no:doc_no},success:
			function(data){
				$( "#dialog_key_product_room" ).html(data);
				$( "#dialog_key_product_room" ).dialog( "open" );
				//getshelfdata();
			}
		});
			//
	}
}

function search_room(event){
	var doc_no=$("#doc_no").val();
	var room_no=$("#search_room").val();
	
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		if(keyCode==13){
			if(room_no==""){
				jAlert('คุณยังไม่ระบุรหัสห้อง!', 'แจ้งเตือน');
				room_no=$("#search_room").val();
				
			}else{
				var substr = room_no.split('-');
				var shelf_no=substr[0];
				var floor_no=substr[0]+"-"+substr[1];
				open_keyproduct(doc_no,shelf_no,floor_no,room_no);
			}
		}
}

function go_tranfer_room(){
	var new_room=$("#new_room").val();
	if(new_room==""){
		jAlert('คุณยังไม่เลือกห้องที่ต้องการโอน!', 'แจ้งเตือน');
	}else{
		var txt="ยืนยันการโอนสินค้า!";
		jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			var form=$('form#frm_tranfer_product').serialize();
			$.ajax({type: "POST",url: "/stock/checkstock/gotranferroom",data:form,success:
			function(data){
				if(data=="y"){
					$( "#dialog_tranfer" ).dialog( "close" );
					//viewproductonshelf();
					view();
				}
			}
			});
		}
		});
	}
}
