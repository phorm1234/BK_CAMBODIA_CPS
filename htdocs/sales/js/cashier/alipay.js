function dlgAlipay(){	
		/**
		 * @desc for support alipay
		 * @modify:13032017 
		 */		
		$("<div id='dlgAlipay'></div>").dialog({
					autoOpen:true,
					width:400,		
					height:'auto',	
					modal:true,
					resizable:true,	
	  				showOpt: {direction: 'up'},
	  				closeOnEscape: false,
	  				title:"<span class='ui-icon ui-icon-person'></span>Alipay",
	  				open: function(){ 	  					
	  					var win = $(window);
	  		            $(this).parent().css({position:'absolute',
	  		            left: (win.width() - $(this).parent().outerWidth())/2,
	  		            top: (win.height() - $(this).parent().outerHeight())/7});
	  					
						$(this).parents(".ui-dialog:first")
						.find(".ui-dialog-content")
						.css({"background-color":"#BCDCD7","font-size":"24px","color":"#000000","padding":"5 0.1em 0 0.1em"});
		            	$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
		            	$(this).dialog("widget").find(".ui-button-text")
	                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
		            	
		            	
		            	//----------------------START --------------------------
		            	$("#dlgAlipay").html("");
						$("#dlgAlipay").load("/sales/cashier/paycase?actions=alipay&now="+Math.random(),
							function(){
							
//								if($("#csh_lock_status").val()=='Y'){
//									lockManualKey();
//								}else{
//									unlockManualKey();
//								}
								
								var cnet=parseFloat($("#txt_net").val().replace(/[^\d\.\-\ ]/g,''));
								//$('#txt_credit_value').ForceNumericOnly();
								$("#txt_alipay_no_value").focus();
								
								displayDigit($('#txt_netvt').val());
								if($('#txt_credit').val()!='0.00'){
				        			$('#txt_alipay_value').val($('#txt_credit').val());
				        		}else{
									$("#txt_alipay_value").val(cnet);
								}
							}
						);			        	
		            	//----------------------STOP  --------------------------
		            	
		            	
        				
	  				},
	  				buttons: [ 
		    	            { 
		    	                text: "ตกลง",
		    	                id:"btnNextMstProTour",
		    	                class: 'ui-btndlg-next', 
		    	                click: function(evt){ 
		    	                	evt.preventDefault();
							 		evt.stopPropagation();	
							 		//------------------------------ START ---------------------------
							 		evt.preventDefault();
							 		evt.stopPropagation();
							 		var credit_no_value=$("#txt_alipay_no_value").val();
							 		credit_no_value=$.trim(credit_no_value);
							 		if(credit_no_value.length<4 || credit_no_value.length>20){
							 			jAlert('กรุณาระบุเลขที่บัตรเครดิตให้ถูกต้อง','ข้อความแจ้งเตือน',function(r){
											if(r){
												$("#txt_alipay_no_value").focus();
												return false;
											}	
						    			});
							 			return false;
							 		}else{
							 			//$("#splash").show();
							 			$.ajax({
							 				type:'post',
							 				url:'/sales/cashier/doalipay',
							 				data:{
							 					txt_credit_no_value:$('#txt_alipay_no_value').val(),
							 					txt_credit_value : $('#txt_alipay_value').val(),
							 					rnd:Math.random()
							 				},success:function(data){
							 					//$("#splash").hide();
							 					var v = data.split("||");
							 					if(v[0]=="y"){
							 						$('#txt_credit_no').val(credit_no_value);
											 		var txt_alipay_value=$("#txt_alipay_value").val();
													$("#txt_credit").val(txt_alipay_value);
													
													//payment_transid
													$('#payment_transid').val(v[1]);
													$('#payment_channel').val("Alipay");
													
													$('#dlgAlipay').dialog('close');
													$("#btn_payment_confirm").trigger("click");
													return false;
							 					}else if(v[0]=="u"){
							 						//========================================================================
							 						
							 						
							 						//--------------------STA CUSTOM -------------------------------		
							 						var v1 = v[1].split("#");
							 						var dlgAlipayConfirm = $('<div id="dlgAlipayConfirm"></div>');
							 						dlgAlipayConfirm.dialog({
							 				           autoOpen:true,
							 				           modal: true,
						 							   title:"ยืนยันการชำระเงินด้วย Alipay",
						 							   width:'33%',
						 							   height:'auto',
							 				           position:"center",
							 				           open:function(){		
							 				            	$(this).dialog('widget')
							 					            .find('.ui-dialog-titlebar')
							 					            .removeClass('ui-corner-all')
							 					            .addClass('ui-corner-top');
							 				            	$("#dlgAlipayConfirm").load("/sales/cashier/alipayconfirmform?reqid="+v1[0]+"&reqdt="+v1[1]+"&amt="+v1[2],function(){
						 										
						 										$('#btn_payment_yes').click(function(){
						 											//alert($('#reqid_hid').val());
						 											$.ajax({
						 								 				type:'post',
						 								 				url:'/sales/cashier/confirmalipay',
						 								 				data:{
						 								 					reqid : v1[0],
						 								 					reqdt : v1[1],
						 								 					amt : v1[2],
						 								 					rnd:Math.random()
						 								 				},success:function(data){
						 								 					var vv = data.split("||");
						 								 					if(vv[0]=="y"){
						 								 						$('#txt_credit_no').val(credit_no_value);
						 												 		var txt_alipay_value=$("#txt_alipay_value").val();
						 														$("#txt_credit").val(txt_alipay_value);
						 														
						 														//payment_transid
						 														$('#payment_transid').val(vv[1]);
						 														$('#payment_channel').val("Alipay");
						 														
						 														$('#dlgAlipay').dialog('close');
						 														$('#dlgAlipayConfirm').dialog('close');
						 														$("#btn_payment_confirm").trigger("click");
						 														return false;
						 								 					}else{
						 								 						alert('ยังไม่พบรายการยืนยันจาก APP');
						 								 					}
						 								 				}
						 											});
						 											
						 										});
						 										
						 										$('#btn_payment_no').click(function(){
						 											$.ajax({
						 								 				type:'post',
						 								 				url:'/sales/cashier/cancelalipay',
						 								 				data:{
						 								 					reqid : v1[0],
						 								 					reqdt : v1[1],
						 								 					amt : v1[2],
						 								 					rnd:Math.random()
						 								 				},success:function(data){
						 								 					
						 								 					if(data=="y"){
						 								 									 														
						 														$('#dlgAlipay').dialog('close');
						 														$('#dlgAlipayConfirm').dialog('close');
						 														//$("#btn_payment_confirm").trigger("click");
						 														return false;
						 								 					}else{
						 								 						alert('ยังไม่พบรายการยืนยันจาก APP'+data);
						 								 					}
						 								 				}
						 											});
						 										});
						 										
						 									});
						 								    $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
						 									$(this).dialog('widget')
						 										.find('.ui-dialog-titlebar')
						 										.removeClass('ui-corner-all')
						 										.addClass('ui-corner-top');
						 									$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
						 									$(this).dialog("widget").find(".ui-dialog-buttonpane")
						 										.css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
						 										    //button style	
						 									$(this).dialog("widget").find(".ui-btndlgpos")
						 										.css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
						 									$(".ui-widget-overlay").live('click', function(){				    	
						 										$('.ui-dialog-content input:first').focus();
						 									}); 				  
						 											  //$("#sms_mobile").ForceNumericOnly();
						 									$(this).parents(".ui-dialog:first").find('input').keypress(function(e) {									    	
						 										if (e.keyCode == $.ui.keyCode.ENTER) {
						 											e.stopImmediatePropagation();				    		
						 											$('.ui-dialog-buttonpane button:first').click();
						 										}
						 									});	   
							 						       
							 					    			
							 						   },close:function(evt){
							 									if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							 										evt.stopPropagation();
							 			    						evt.preventDefault();
							 									}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							 										evt.stopPropagation();
							 			    						evt.preventDefault();		    		    						
							 									}								 					            	
							 					            	$("#dlgAlipayConfirm").dialog("destroy").remove();
							 						   }
							 						            
							 						});
							 						
							 						//--------------------END CUSTOM -------------------------------
							 						
							 						
							 					}else{
							 						jAlert(v[1],'ข้อความแจ้งเตือน',function(r){
														if(r){
															$("#txt_alipay_no_value").focus();
															return false;
														}	
									    			});
							 					}
							 				}
							 			});
										return false;
							 		}
							 		
							 		//-------------------------------STOP-----------------------------
							 		
							 		$('#dlgAlipay').dialog('close');
	    	                }
	    	            }
		    	  ]					
				,
	  				close:function(evt){
	  					calPaid();
	  					$("#btn_payment_alipay").removeClass("ui-state-focus ui-state-hover ui-state-active");
		            	$('#dlgAlipay').dialog('destroy').remove();
		   			}
	          });
}//func
