$(document).ready( function() {
	vieworderfix("p");
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
function vieworderfix(chk){
	//var doc_no=$("#invoice").val();
	//var status_no=$("#status_no").val();
	$.ajax({type: "POST",url: "/order/order/getproductorderfix",data:{chk:chk},success:
	function(data){
		if(chk=="s"){
			/*$.ajax({type: "POST",url: "/stock/stock/checkinv",data:{doc_no:doc_no},success:
			function(data){
				if(data=="n"){
					var txt="ไม่พบเลขที่สินค้าเข้า : "+doc_no;
					jAlert(txt, 'Alert', function() {
						$("#invoice").focus();
					});
				}else if(data=="w"){
					jAlert('คุณยังไม่ป้อนเลขที่สินค้าเข้า', 'Alert', function() {
						$("#invoice").focus();
					});
				}
			}});*/
		}
		$("#viewproductorderfix").html(data);
	}}); 
}

// function insert_tdiary_or(){
// 	var chk_qty=$("#qty").val();
// 	var product_id = $("#product_id").val();
// 	if(chk_qty==""){
// 		jAlert('คุณยังไม่กรอกจำนวน!', 'Alert', function() {
// 			$("#qty").focus();
// 		});
// 	}else{
// 		if(chk_qty>0){   // old version chk_qty>0 && chk_qty<100
// 			$.ajax({type: "POST",url: "/order/order/chkproduct",data:{product_id:product_id,qty:chk_qty},success:
// 				function(data){
// 					if(data=='y'){
// 						var form=$('form#frm_order').serialize();
// 						$.ajax({type: "POST",url: "/order/order/inserttdiaryor",data:form,success:
// 							function(data){
// 								vieworderfix("s");
// 								$("#product_id").val("");
// 								$("#qty").val("");
// 								$("#product_id").focus();
// 							}
// 						});
// 					}else{
// 						jAlert('ต้องสั่งสินค้าไม่เกินครึ่งหนึ่งของจำนวน Fix Stock test2', 'Alert', function() {
// 							$("#qty").focus();
// 						});
// 					}
// 				}
// 			});	
// 		}else{
// 			jAlert('ต้องสั่งสินค้ามากกว่า 0 ชิ้น', 'Alert', function() {
// 				$("#qty").focus();
// 			});
// 		}
// 	}
// }

function order_cancel(){
	jConfirm('Confirm Delete', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			var tbl="trn_tdiary2_or";
			$.ajax({type: "POST",url: "/order/order/deleteordertemp",data:{tbl:tbl},success:
			function(data){
				if(data=="y"){
					vieworderfix("s");				
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
	jConfirm('Confirm Save', 'Alert', function(r) {
		if(r==false){
			return false;
		}else{
			$.ajax({type: "POST",url: "/order/order/tempor2diaryor",data:{doc_tp:doc_tp,status_no:status_no,doc_remark:doc_remark,chk_pass:cashier_id},dataType: 'json',success:
			function(data){
				if(data.status=="y"){
					var txt="เลขที่เอกสาร : "+data.doc_no;
					jAlert(txt, 'Alert', function() {
						$.ajax({type: "POST",url: "/order/order/printor",data:{doc_no:data.doc_no,status_no:status_no,doc_remark:doc_remark},success:
							function(data){
								getquota();
								vieworderfix("s");	
							}
						});
						
						
						
					});			
				}else if(data.status=="n"){
					jAlert('วงเงินสั่งสินค้าไม่เพียงพอ!', 'Alert', function() {
						return false;
					});
				}else{
					jAlert('เกิดการผิดพลาดไม่สามารถดำเนินการได้!', 'Alert', function() {
						return false;
					});
				}
			}});
		}
	});
}

function Alertmsg(){
	jAlert('Error!', 'Alert', function() {
		return false;
	});
}

function process_to_order(){
	$.ajax({type: "POST",url: "/order/order/checkdocnodiaryor",data:{},success:
		function(data){
			if(data=="y"){
				jAlert('You must delete all of orders list before process again!', 'Alert', function() {
					return false;
				});
			}else{
				var doc_tp=$("#doc_tp").val();
				$.ajax({type: "POST",url: "/order/order/processtoorders",data:{},success:
					function(data){
						jAlert('PROCESS SUCCESS!!, The page will reload', 'Alert', function() {
							window.location.reload();
							return false;
						});
					}
				});
			}
		}
	});
}