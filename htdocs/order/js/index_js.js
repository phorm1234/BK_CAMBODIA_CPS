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
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			
		}
	});
	$( "#dialog_product" ).dialog({
		autoOpen: false,
		width: 800,
		height: 450,
		modal: true
	});
	
	$( "#dialog_get_docno" ).dialog({
		autoOpen: false,
		width: 500,
		height: 250,
		modal: true
	});
	
	$( "#view_productcheckdif" ).dialog({
		autoOpen: false,
		width: 600,
		height: 300,
		modal: true
	});
	
	$( "#view_orderby" ).dialog({
		autoOpen: false,
		width: 400,
		height: 150,
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
		
});

function open_product(shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/productonshelf",data:{shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
	function(data){
		$("#dialog_product").html(data);
		$( "#dialog_product" ).dialog( "open" );
	}}); 
}

function open_keyproduct(doc_no,shelf_no,floor_no,room_no){
	$.ajax({type: "POST",url: "/stock/checkstock/keyproductonshelf",data:{doc_no:doc_no,shelf_no:shelf_no,floor_no:floor_no,room_no:room_no},success:
	function(data){
		$("#dialog_product").html(data);
		$( "#dialog_product" ).dialog( "open" );
	}}); 
}

view();
function view(){
	$.ajax({type: "POST",url: "/stock/checkstock/viewshelf",data:{},success:
		function(data){
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
		jAlert('คุณยังไม่กรอก รหัส Shelf', 'แจ้งเตือน');
		$("#shelf_no").focus();
		return  false;
	}else{
		jConfirm("บันทึกข้อมูล", 'ยืนยันการทำงาน', function(r) {
			if(r==true){
				var form=$('form#frm_shelf').serialize();
				$.ajax({type: "POST",url: "/stock/checkstock/saveshelf",data:form,success:
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
			var form=$('form#frm_floor').serialize();
				$.ajax({type: "POST",url: "/stock/checkstock/savefloor",data:form,success:
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
				var form=$('form#frm_room').serialize();
				$.ajax({type: "POST",url: "/stock/checkstock/saveroom",data:form,success:
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

function print_list_tag(){
	var txt="ยืนยันการพิมพ์เอกสาร";
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
			var form=$('form#frm_print_list_tag').serialize();
			$.ajax({type: "POST",url: "/stock/checkstock/printlisttag",data:form,success:
				function(data){
				var data = data.split(':::::');
					if(data[1]=="n"){
						jAlert('คุณยังไม่เลือก Shelf!', 'แจ้งเตือน');
						return false;
					}else{
						$("#shlef").val(data[1]);
						$("#doc_no").val(data[0]);
						$("form#send_shelf").submit();
					}
				}
			}); 
		}
	});
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
	var doc_no_lod=$("#doc_no_old").val();
	$.ajax({type: "POST",url: "/stock/checkstock/printlisttagold",data:{doc_no_old:doc_no_lod},success:
		function(data){
			var data = data.split(':::::');
			$("#shlef").val(data[1]);
			$("#doc_no").val(data[0]);
			$("form#send_shelf").submit();
		}
	}); 
}

function getshelfdata(){
	var doc_no=$("#doc_no").val();
	$.ajax({type: "POST",url: "/stock/checkstock/keyshelf",data:{doc_no:doc_no},success:
		function(data){
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

function save_list_tag(event,chk_product_id){
		var form=$('form#onviewproduct').serialize();
		$.ajax({type: "POST",url: "/stock/checkstock/savelisttag?chkproductid="+chk_product_id,data:form,success:
			function(data){
			
			}
		});
}

function print_count_list_tag(){
	var txt="ยืนยันการพิมพ์เอกสาร";
	jConfirm(txt, 'ยืนยันการทำงาน', function(r) {
		if(r==true){
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
						$("form#send_countshelf").submit();
					}
				}
			}); 
		}
	});
}

function getdifcheck(){
	var doc_no=$("#doc_no").val();
	$.ajax({type: "POST",url: "/stock/checkstock/getdifcheck",data:{doc_no:doc_no},success:
		function(data){
			$("#view_difcheck").html(data);
		}
	}); 
}

function print_diff_stock(){
	var doc_no=$("#doc_no").val();
	var form=$('form#frm_orderby').serialize();
	$.ajax({type: "POST",url: "/stock/checkstock/getarrdifstock?doc_no="+doc_no,data:form,success:
		function(data){
			var data = data.split(':::::');
			$("#product_id_pdf").val(data[1]);
			$("#doc_no_pdf").val(data[0]);
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
