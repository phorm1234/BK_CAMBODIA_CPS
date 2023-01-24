	//AREA LOCK KEY BARCODE OR KEY MANUAL
	var clear_timer_keybarcode=null;
	function stopTimerKeyBarcode(){
		if(clear_timer_keybarcode){
			clearTimeout(clear_timer_keybarcode);
			clear_timer_keybarcode=null;
		}
	}//func
	
	function clsBarCode($obj){
		$obj.val('');
	}//func	
		
	function lockManualKey(){
		$(document).bind("contextmenu",function(e){
			e.preventDefault();
			return false;
		});	
		
		$(document).bind('cut copy paste', function(e){
	        e.preventDefault();
	        return false;
	   }); 
		
		$(".keybarcode").each(function(){
	    	$(this).keypress( function(evt) {
				var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
				var objBarcode=$(this);
		        if(key == 13){
		        	if($(this).val().length < 6 || $(this).val().length > 13){
		        		//out of range for card between 6 to 13 character
						return false;
					}
		        	stopTimerKeyBarcode();
		        	return false;
		        }else{
		        	stopTimerKeyBarcode();
				    clear_timer_keybarcode=setTimeout(function(){clsBarCode(objBarcode)},100);
				} 
			});
		});//chkbarcode
	}//func

	function unlockManualKey(){
		$(document).bind("contextmenu",function(e){
			return true;
		});	
		$(document).bind('cut copy paste', function(e){
            return true;
       }); 
		
		//unlock drag & drop
		$( ".keybarcode" ).each(function(){
    		$(this).draggable("destroy");
    		$(this).droppable("destroy"); 
    	});
	}//func
	
	function getLockStatus(){
		//GET LOCK STATUS
		$.ajax({
			type:'post',
			url:'/sales/cashier/getlockstatus',
			data:{
				rnd:Math.random()
			},success:function(data){
				$("#csh_lock_status").val(data);
				if(data=='Y'){
					lockManualKey();
				}else{
					unlockManualKey();
				}
			}
		});
	}//func
	//AREA LOCK KEY BARCODE OR KEY MANUAL	
	function initOrdForm(){
		/**
		 * @desc
		 * @create 08032017
		 */
		$('#cst_data').val('');
		$('#cst_idcard').val();
		$('#cst_data2').val('');
		$("#tabs").tabs("option", "active", 1);
		$('#cst_data').focus();
	}//

	function showDialog(){
	   $("#divId").dialog("open");
	   $("#modalIframeId").attr("src","http://127.0.0.1/sales/cmd/webcam/php-webcamera/test2.html");
	   return false;
	}


	function checkSaleMan(){
		 //-------------- CHECK SALEMAN------------------							    	
		var Dialog_Sales = $('<div id="dlgSaleMan">\
	           <p><input type="text" size="25" id="chk_saleman_id" class="input-text-pos ui-corner-all keybarcode"></p>\</div>');
	       Dialog_Sales.dialog({								        	
	           modal: true,
	           title: "กรุณาระบุรหัสผู้ขาย",
	           width:275,
	           height:'auto',
	           closeOnEscape:false,
	           open:function(){
	           	//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
	        	$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
	           	$(".ui-widget-overlay").live('click', function(){
				    	$("#chk_saleman_id").focus();
					});
	           	$("#chk_saleman_id").focus();
					//*** check lock unlock
					if($("#csh_lock_status").val()=='Y'){
						lockManualKey();
					}else{
						unlockManualKey();
					}	
					//*** check lock unlock
					$("#chk_saleman_id").keypress(function(evt){																	
						var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
				        if(key == 13) {
				        	evt.stopImmediatePropagation();
				            evt.preventDefault();
				            var chk_saleman_id=jQuery.trim($("#chk_saleman_id").val());
				            if(chk_saleman_id=='') return false;
				            var opts={
						            type:"post",
						            url:"/sales/cashier/getemp",
						            async: true,
						            data:{
						            	employee_id:chk_saleman_id,
										actions:'saleman'
					            	},
					            	success:function(data){
						            	var arr_data=data.split('#');
										if($.trim(arr_data[0])==""){
											jAlert('ไม่พบรหัสผู้ขาย กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
												$("#chk_saleman_id").val('').focus();
												return false;
							    			});
										}else if($.trim(arr_data[3])=='P'){
											jAlert('ขณะนี้พนักงานขายไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
												$("#chk_saleman_id").val('').focus();
												return false;
							    			});
										}else if($.trim(arr_data[3])=='N'){
											jAlert('ขณะนี้พนักงานขายไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
												$("#chk_saleman_id").val('').focus();
												return false;
							    			});
										}else{
							            	$("#csh_saleman_id").val(arr_data[0]);
							            	$("#dlgSaleMan").dialog("close");
							            	
							            	///////////////START//////////////
							            	
							            	var cst_data=$('#cst_data').val();		
							        		cst_data=$.trim(cst_data);
							        		if(cst_data.length==0){
							        			jAlert('กรุณาสแกนข้อมูลการรับสินค้า','ข้อความแจ้งเตือน',function(){		        
							        				$('#cst_data').focus();
							        				 return false;
							        			  });
							        			return false;
							        		}
							        		
							        		$.ajax({
							        			type:'post',
							        			dataType:'json',
							        			url:'/sales/ecommerce/savereceivegoods',
							        			data:{
							        				qstr_receive:cst_data,
							        				type_receive:'A',
							        				rnd:Math.random()
							        			},success:function(objRes){						        				
							        				
							        				if(objRes.status=='Y'){
							        					
							        					setTimeout(function(){
							                				 $.ajax({
							                					 type:'post',
							                					 url:'/sales/ecommerce/sendorderstatus',
							                					 data:{
							                						 order_no:objRes.order_no,
							                						 order_status:'D',
							                						 user_action:arr_data[0],
							                						 rnd:Math.random()
							                					 },success:function(){}
							                				 });
							                			 },800);
							        					jAlert('บันทึกข้อมูลรับสินค้าตามใบสั่งสินค้า ' + objRes.order_no + ' สมบูรณ์' ,'ผลการบันทึก',function(){							
							        						initOrdForm();
							        	    				return false;
							        		    		 });						
							        				}else{
							        					jAlert(objRes.msg,'ผลการบันทึก',function(){
							        						 $("#cst_data").focus();
							        						 return false;
							        		    		 });
							        				}				
							        				
							        			}
							        			
							        		});
							            	
							            	///////////////START//////////////
							            	
							            }						            	
						            }
						    };	
							$.ajax(opts);
				            return false;
				        }
					});		
												            	
	           },close:function(evt){
	           	$("#dlgSaleMan").remove();	
	           	$("#dlgSaleMan").dialog("destroy");	            		
	           }
	       });
		//------------- CHECK SALEMAN-----------------
	}//func



	$(function() {
		
		$("#tabs").tabs("option", "active", 1);
		$('#cst_data').addClass("input_receive_active");
	    $('#cst_data').focus();    
	
	    $("#tabs").tabs({
	        fx: { opacity: 'toggle', speed: 'fast' },
	        show: function(event, ui) {
	          //alert(ui.index);
	          if(ui.index == 0) { 
	        	  
	        	  
		      	  $('#cst_idcard').val();
		      	  $('#cst_data2').val('');	 
		      	  $('#show_saved_img').html('');
	        	  $('#cst_data').addClass("input_receive_active");
	        	  $('#cst_idcard').removeClass("input_receive_active");
	        	  $('#cst_data').focus();
	          }
	          if(ui.index == 1) { 
	        	  
	        	  $('#cst_data').val('');	        	  
	        	  $('#cst_idcard').addClass("input_receive_active");
	        	  $('#cst_data').removeClass("input_receive_active");
	        	  $('#cst_idcard').focus();
	        	  //return false;
	          }
	        }
	    });
	    
	    $("#webcam").css( 'cursor', 'pointer' );
	    
	    $("#divId").dialog({
	        autoOpen: false,
	        modal: true,
	        height:'auto',
	        width:'auto'
	    });
	    
	    
	//    $("#webcam").click(function(e){
	//    	e.preventDefault();
	//    	
	//    	var iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
	//        var opt_dialog = $("<div id='dlgWebCam'></div>").append(iframe).appendTo("body").dialog({
	//    	
	//            autoOpen: false,
	//            modal: true,
	//            resizable: true,
	//            width: "350",
	//            height: "300",
	//            open:function(){
	//            	
	//            	iframe.attr({
	//            		//"src":"http://127.0.0.1/sales/cmd/webcam/php-webcamera/test2.html",width:"350",height:"300"
	//            		"src":"http://localhost/sales/cmd/webcam/php-webcamera/test2.html",width:"350",height:"300"
	//            	});
	//            	            	
	//            },
	//            close: function () {
	//                iframe.attr("src", "");               
	//                $('#dlgWebCam').dialog('destroy').remove();
	//            }
	//        });        
	//        
	//		$('#dlgWebCam').dialog(opt_dialog);			
	//		$('#dlgWebCam').dialog('open');
	//       
	//    	
	//    });
	    
	   
	    
	    
	    
	    
	    
	    $("#webcam").click(function(e){
	    	e.preventDefault();
	    	
	    	var iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
	        var opt_dialog = $("<div id='dlgWebCam'></div>").append(iframe).appendTo("body").dialog({
	    	
	            autoOpen: false,
	            modal: true,
	            resizable: true,
	            width: "auto",
	            height: "auto",
	            open:function(){
	            	$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	            	iframe.attr({
	            		"src":"http://127.0.0.1:8081",width:"350",height:"220",valign:"middle",marginwidth:"0",marginheight:"0",align:"center"
	            	});
	            	            	
	            },
	            buttons: [ 			
		  	                { 
			    	                text: "ถ่ายรูป",
			    	                id:"btn_capture",
			    	                class: 'ui-btndlgpos2', 
			    	                click: function(evt){ 
			    	                	evt.preventDefault();
								 		evt.stopPropagation();	
								 		$.ajax({
								 			type:'post',
								 			url:'/sales/ecommerce/capturephoto',
								 			data:{
								 				rnd:Math.random()
								 			},success:function(data){							 				
								 				$("#show_saved_img").empty().html('<img src="' + data + '" />');
								 			}
								 		});
								 		$('#dlgWebCam').dialog('close');										 		
			    	                }
		  	                }
				  	  ]		
	            ,
	            close: function () {
	                //iframe.attr("src", "");               
	                $('#dlgWebCam').dialog('destroy').remove();
	            }
	        });        
	        
			$('#dlgWebCam').dialog(opt_dialog);			
			$('#dlgWebCam').dialog('open');
	       
	    	
	    });
	    
	    
	    
		$("#cst_data").keypress(function(evt){																    	
	    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
		        if(key == 13){	
				evt.preventDefault();
				evt.stopPropagation();
				$('#btn_cst_submit').trigger('click');
				return false;
		        }
	    });
	
		$("#btn_cst_submit").click(function(evt){
			evt.preventDefault();
			var cst_data=$('#cst_data').val();		
			cst_data=$.trim(cst_data);
			if(cst_data.length==0){
				jAlert('กรุณาสแกนข้อมูลการรับสินค้า','ข้อความแจ้งเตือน',function(){		        
					$('#cst_data').focus();
					 return false;
				  });
				return false;
			}else{
				checkSaleMan();
			}
		});
		$("#btn_cst_submit2").click(function(evt){
			evt.preventDefault();
			var cst_idcard=$('#cst_idcard').val();
			/////// START //////////
			var cst_data=$('#cst_data').val();
			cst_data=$.trim(cst_data);
			if(cst_data.length==0){
				jAlert('กรุณาสแกนข้อมูลการรับสินค้า','ข้อความแจ้งเตือน',function(){		
					$('#cst_data2').focus();
					 return false;
				  });
				return false;
			}
			
			$.ajax({
				type:'post',
				dataType:'json',
				url:'/sales/ecommerce/savereceivegoods',
				data:{
					qstr_receive:cst_data,
					cst_idcard:cst_idcard,
					type_receive:'B',
					rnd:Math.random()
				},success:function(objRes){	
					if(objRes.status=='Y'){
						setTimeout(function(){
	        				 $.ajax({
	        					 type:'post',
	        					 url:'/sales/ecommerce/sendorderstatus',
	        					 data:{
	        						 order_no:objRes.order_no,
	        						 order_status:'D',
	        						 rnd:Math.random()
	        					 },success:function(){}
	        				 });
	        			 },800);
						jAlert('บันทึกข้อมูลรับสินค้าตามใบสั่งสินค้า ' + objRes.order_no + ' สมบูรณ์' ,'ผลการบันทึก',function(){							
							initOrdForm();
		    				return false;
			    		 });						
					}else{
						jAlert('เกิดปัญหาการบันทึกการรับสินค้าใบสั่งซื้อเลขที่\n ' + order_no + '\n ไม่สามารถบันทึกได้','ผลการบันทึก',function(){
							 $("#ord_doc_no").focus();
							 return false;
			    		 });
					}
					
					
				}
				
			});
			
			/////// START //////////
			
		});
			   
	});