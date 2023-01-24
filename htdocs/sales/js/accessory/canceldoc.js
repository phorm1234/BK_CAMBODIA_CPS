var clear_timer;
	function clsBarCode($obj){
			$obj.val('');
			clearTimeout(clear_timer); 
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
	
//	    $(".keybarcode").each(function(){
//	    	$(this).keypress( function(evt) {
//				var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
//				var objBarcode=$(this);
//		        if(key == 13){
//		        	if($(this).val().length != 0 && $(this).val().length != 13){
//						return false;
//					}
//		        }else{
//		        		//number only
//						if((key!=8 && key!=0) && (key<48) || (key>57)){
//							return false;
//						}else{
//							setTimeout(function(){clsBarCode(objBarcode);},800);
//						}
//				} 
//			});	
//		});//chkbarcode
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
	
	function openFormCancelDoc19(){		
		/**
		 *@desc
		 *@param
		 *@last modify:30102012
		 */		
		var opts_dlgFormCancelDoc={
				autoOpen:false,
				width:'45%',
				height:'220',
				modal:true,
				resizeable:true,
				position:'top',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"ยกเลิกเอกสารบิล 19",
				open:function(){					
					$("*:focus").blur();
					$("#dlgFormCancelDoc").html("");
					$.ajax({
						type:'post',
						url:'/sales/accessory/formcanceldoc19',
						cache:false,
						data:{
							now:Math.random()
						},
						success:function(data){
							$(this).parent().find('select, input, textarea').blur();
							$("#dlgFormCancelDoc").html(data);
							//----------------- START NEW ARIVAL						    
						   $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar").html("กรุณาระบุรหัสผ่าน");
						   $(".ui-widget-overlay").live('click', function(){
						    	$("#chk_saleman_id").focus();
							});
							$("#dlgSaleMan").html("");
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						
//							$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
//								"margin":"0 0 0 0","font-size":"27px","color":"#000","height":"50px"});
//							$(this).parents(".ui-dialog:first").dialog("widget").find(".ui-dialog-buttonpane")
//								.css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
							
							$(".ui-widget-overlay").live('click', function(){
								$("#chk_saleman_id").focus();
							});
								
							$("#cpassword").focus();
							//*** check lock unlock
							if($("#csh_lock_status").val()=='Y'){
								lockManualKey();
							}
							//*** check lock unlock							
							//----------------- START NEW ARIVAL
							
						}//end success function
					});//end ajax pos
				},
				buttons: {							
					"ตกลง":function(evt){ 
						//---------------------- submit						
			        	evt.stopImmediatePropagation();
			            evt.preventDefault();	
			            var chk_saleman_id=jQuery.trim($("#chk_saleman_id").val());					             	            
			            if(chk_saleman_id==''){
			            	jAlert('กรุณาป้อนรหัสผ่านผู้ทำการยกเลิกเอกสาร','ข้อความแจ้งเตือน',function(){
								$("#chk_saleman_id").val('').focus();
								return false;
			    			});
			            	return false;
			            }			
			            
			            //check ros/arom
			            var n_cancel='0';					          
			            var acc_doc_no=$('#acc_doc_no').val();
			            acc_doc_no=$.trim(acc_doc_no);							
									  
			            var opts={
					            type:"post",
					            url:"/sales/cashier/getemp",
					            data:{
					            	employee_id:chk_saleman_id,
									actions:"audit"
				            	},
				            	success:function(data){
				            		var arr_data=data.split('#');
									if($.trim(arr_data[0])==""){
										jAlert('ไม่พบรหัสพนักงาน กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
											$("#chk_saleman_id").val('').focus();
											return false;
						    			});
									}else if($.trim(arr_data[3])=='P'){
										jAlert('ขณะนี้พนักงานไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
											$("#chk_saleman_id").val('').focus();
											return false;
						    			});
									}else if($.trim(arr_data[3])=='N'){
										jAlert('ขณะนี้พนักงานไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
											$("#chk_saleman_id").val('').focus();
											return false;
						    			});
									}else{
						            	if(data!=""){
								            	$("#csh_saleman_id").val(arr_data[0]);
								            	////////////// CANCEL BILL /////////////////
								            	 var opts_doccancel={
								    				     type:'post',
								    				     url:'/sales/accessory/canceldocno',
								    					 data:{														    						  
								    				    		doc_no:$acc_doc_no_val,
								    				    		doc_tp:$('#sel_typecancel').val(),
								    				    		employee_id:arr_data[0],
								    				    		aromros_id:'',
								    				    		cancel_type:'',
								    				    		cancel_description:'',
								    				    		rnd:Math.random()
								    				    	},success:function(data){
								    				    		//return message :: status,promt message,title massage
									    				    	var arr_data=data.split('#');
									    				    	if(arr_data[0]=='Y'){
									    				    		$("#dlgFormCancelDoc").dialog("close");
									    				    		$("#sel_typecancel option:first").attr('selected','selected');
									    				    		$("#n_cancel").html('');
									    				    		 jAlert(arr_data[1],arr_data[2],function(){														    					    			 
								    					    			 $('#acc_doc_no').val('').focus();
								    				    				 return false;
								    					    		 });
										    				    }else{
										    				    	 $('#dlgSaleMan').dialog('close');
										    				    	 jAlert(arr_data[1],arr_data[2],function(){																    				    															    				    		
								    				    				 return false;
								    					    		 });
											    				}											    					    
								    					    }
								    					  };
								    			  
								    			  $.ajax(opts_doccancel);
								            	////////////// CANCEL BILL /////////////////													       
							            	}//end if data!=''		
										}//end else arr_data
					            }//success							            
					    };//opts
			            $.ajax(opts);			              				
						//---------------------- submit
						$(this).dialog("close");
						return false;
					}
				},
				close:function(){
					$("#dlgFormCancelDoc").dialog("destroy");
				}
		};
		$("#dlgFormCancelDoc").dialog("destroy");
		$("#dlgFormCancelDoc").dialog(opts_dlgFormCancelDoc);			
		$("#dlgFormCancelDoc").dialog("open");
	}//func
	
	function openFormCancelDoc(){		
		/**
		 *@desc
		 *@param
		 *@last modify:30102012
		 */				
		var opts_dlgFormCancelDoc={
				autoOpen:false,
				width:'45%',
				height:'300',
				modal:true,
				resizeable:true,
				position:'top',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"ยกเลิกเอกสาร",
				open:function(){					
					$("*:focus").blur();
					$("#dlgFormCancelDoc").html("");
					$.ajax({
						type:'post',
						url:'/sales/accessory/formcanceldoc',
						cache:false,
						data:{
							now:Math.random()
						},
						success:function(data){
							$(this).parent().find('select, input, textarea').blur();
							$("#dlgFormCancelDoc").html(data);
							//----------------- START NEW ARIVAL
						      if($('#sel_typecancel').val()=='AI' || $('#sel_typecancel').val()=='AO'){
						    	  $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar").html("กรุณาระบุรหัสผ่าน");
							  }else{
								  $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar").html("ระบุรหัสผู้ยกเลิกบิล");
							 }

						   $(".ui-widget-overlay").live('click', function(){
						    	$("#chk_saleman_id").focus();
							});
							$("#dlgSaleMan").html("");
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
								
							$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
								"margin":"0 0 0 0","font-size":"27px","color":"#000","height":"50px"});
							$(this).parents(".ui-dialog:first").dialog("widget").find(".ui-dialog-buttonpane")
								.css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
							
							$(".ui-widget-overlay").live('click', function(){
								$("#chk_saleman_id").focus();
							});
								
							$("#cpassword").focus();
							//*** check lock unlock
							if($("#csh_lock_status").val()=='Y'){
								lockManualKey();
							}
							//*** check lock unlock							
							//----------------- START NEW ARIVAL
							
						}//end success function
					});//end ajax pos
				},
				buttons: {							
					"ตกลง":function(evt){ 
						//---------------------- submit						
					        	evt.stopImmediatePropagation();
					            evt.preventDefault();
					            var cpassword=jQuery.trim($("#cpassword").val());					           
					            var cancel_type=$('#cancel_description').val();		
					            var cancel_description=$('#cancel_description option:selected').text();	
					            var chk_saleman_id=jQuery.trim($("#chk_saleman_id").val());
					            
					            if(cpassword.length==0){
					            	jAlert('กรุณาป้อนรหัสผ่าน ROS/AROM','ข้อความแจ้งเตือน',function(){
										$("#cpassword").val('').focus();
										return false;
					    			});
					            	return false;
					            }					           	            
					            if(chk_saleman_id==''){
					            	jAlert('กรุณาป้อนรหัสผ่านผู้ทำการยกเลิกเอกสาร','ข้อความแจ้งเตือน',function(){
										$("#chk_saleman_id").val('').focus();
										return false;
					    			});
					            	return false;
					            }				
					          
					            if(cancel_type==''){
					            	jAlert('กรุณาเลือกเหตุผลการยกเลิกบิล','ข้อความแจ้งเตือน',function(){
										$("#cancel_description").focus();
										return false;
					    			});
					            	return false;
					            }	
					            
					            //check ros/arom
					            var n_cancel=$('#n_cancel').html();		
					            n_cancel=$.trim(n_cancel);
					            var acc_doc_no=$('#acc_doc_no').val();
					            acc_doc_no=$.trim(acc_doc_no);
					            var opts_chkrosarom={
					            		type:'post',
					            		url:'/sales/accessory/chkrosarom',
					            		data:{
					            			cpassword:cpassword,
					            			n_cancel:n_cancel,
					            			doc_no:acc_doc_no,
					            			rnd:Math.random()
					            		},success:function(data){
					            			var arr_data=data.split('#');
					            			var aromros_id=arr_data[2];
					            			if(arr_data[0]=='Y'){
					            				//valid ros/arom next step
					            				//check employee and submit cancel
					            				 //check employee
									            if($('#sel_typecancel').val()=='AI' || $('#sel_typecancel').val()=='AO'){
										        	actions="audit";
									            }else{
										            actions="saleman";
										         }
									            var opts={
											            type:"post",
											            url:"/sales/cashier/getemp",
											            data:{
											            	employee_id:chk_saleman_id,
															actions:actions
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
												            	if(data!=""){
														            	$("#csh_saleman_id").val(arr_data[0]);
														            	////////////// CANCEL BILL /////////////////
														            	 var opts_doccancel={
														    				     type:'post',
														    				     url:'/sales/accessory/canceldocno',
														    					 data:{														    						  
														    				    		doc_no:$acc_doc_no_val,
														    				    		doc_tp:$('#sel_typecancel').val(),
														    				    		employee_id:arr_data[0],
														    				    		aromros_id:aromros_id,
														    				    		cancel_type:cancel_type,
														    				    		cancel_description:cancel_description,
														    				    		rnd:Math.random()
														    				    	},success:function(data){
														    				    		//alert(data);
														    				    		//return message :: status,promt message,title massage
															    				    	var arr_data=data.split('#');
															    				    	if(arr_data[0]=='Y'){
															    				    		$("#dlgFormCancelDoc").dialog("close");
															    				    		$("#sel_typecancel option:first").attr('selected','selected');
															    				    		$("#n_cancel").html('');
															    				    		 jAlert(arr_data[1],arr_data[2],function(){														    					    			 
														    					    			 $('#acc_doc_no').val('').focus();
														    				    				 return false;
														    					    		 });
																    				    }else{
																    				    	 $('#dlgSaleMan').dialog('close');
																    				    	 jAlert(arr_data[1],arr_data[2],function(){
																    				    		 //$('#chk_saleman_id').select().focus();																    				    		
														    				    				 return false;
														    					    		 });
																	    				}											    					    
														    					    }
														    					  };
														    			  
														    			  $.ajax(opts_doccancel);
														            	////////////// CANCEL BILL /////////////////													       
													            	}//end if data!=''		
																}//end else arr_data
											            }//success							            
											    };//opts
									            $.ajax(opts);
									            return false;
					            				//check employee and submit cancel
					            				
					            			}else{
					            				//invalid ros/arom
					            				jAlert('รหัสผิด กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#cpassword").val('').focus();
													return false;
								    			});
					            			}
					            		}
					            };
					            $.ajax(opts_chkrosarom);
					            return false;				     
						//---------------------- submit
						$(this).dialog("close");
						return false;
					}
				},
				close:function(){
					$("#dlgFormCancelDoc").dialog("destroy");
				}
		};
		$("#dlgFormCancelDoc").dialog("destroy");
		$("#dlgFormCancelDoc").dialog(opts_dlgFormCancelDoc);			
		$("#dlgFormCancelDoc").dialog("open");
	}//func
	
	function submitCancelDoc($acc_doc_no_val){
		/**
		 * @des
		 */
		  //----------------------- new version ---------------------------------
	     var cType=$('#sel_typecancel').val();
	     var cancel_type=$('#cancel_description').val();    
	     var acc_status_no=$('#acc_status_no').val();
	     if(cType=='SL' || cType=='VT' || cType=='DN'){
	    	 if(acc_status_no=='19'){
	    		 openFormCancelDoc19();
	    	 }else{
	    		 openFormCancelDoc();
	    	 }
	     }else{
	    	   //--------------CHECK SALEMAN AUDIT ------------------		     
			  var dialogOpts_saleman = {
						autoOpen: false,
						width:350,		
						height:'auto',	
						modal:true,
						stack: true,
						resizable:true,
						position:"center",
						showOpt: {direction: 'up'},		
						closeOnEscape:true,	
						title:'',
						open: function(){    
								var url_form_user="/sales/cashier/saleman";
						      if($('#sel_typecancel').val()=='AI' || $('#sel_typecancel').val()=='AO'){
						    	  $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar").html("กรุณาระบุรหัสผ่าน");
						    	  url_form_user="/sales/accessory/formauditcanceldoc";
							  }else{
								  $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar").html("ระบุรหัสผู้ยกเลิกบิล");
							 }						     

							   $(".ui-widget-overlay").live('click', function(){
							    	$("#chk_saleman_id").focus();
								});
								$("#dlgSaleMan").html("");
								
								$("#dlgSaleMan").load(url_form_user+"?now="+Math.random(),
								function(){	
									
									$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
									$(this).dialog('widget')
							            .find('.ui-dialog-titlebar')
							            .removeClass('ui-corner-all')
							            .addClass('ui-corner-top');
									
									$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
										"margin":"0 0 0 0","font-size":"27px","color":"#000","height":"50px"});
									$(this).parents(".ui-dialog:first").dialog("widget").find(".ui-dialog-buttonpane")
										.css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
									
									$(".ui-widget-overlay").live('click', function(){
										$("#chk_saleman_id").focus();
									});
									
									$("#chk_saleman_id").focus();
									//*** check lock unlock
									if($("#csh_lock_status").val()=='Y'){
										lockManualKey();
									}
									//*** check lock unlock
									$("#chk_saleman_id").keypress(function(evt){
										var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								        if(key == 13) {
								        	evt.stopImmediatePropagation();
								            evt.preventDefault();
								            var chk_saleman_id=jQuery.trim($("#chk_saleman_id").val());
								            if(chk_saleman_id=='') return false;
								            if($('#sel_typecancel').val()=='AI' || $('#sel_typecancel').val()=='AO'){
									        	actions="audit";
								            }else{
									            actions="saleman";
									         }
								            var opts={
										            type:"post",
										            url:"/sales/cashier/getemp",
										            data:{
										            	employee_id:chk_saleman_id,
														actions:actions
									            	},
									            	success:function(data){
									            		var arr_data=data.split('#');
									            		var msg_error_notice='ข้อความแจ้งเตือน';
														if($.trim(arr_data[0])==""){															
															var msg_error_1='';
															if(actions='audit'){
																msg_error_1="ไม่พบรหัสผ่าน กรุณาตรวจสอบอีกครั้ง";
															}else{
																msg_error_1="ไม่พบรหัสผู้ขาย กรุณาตรวจสอบอีกครั้ง";
															}
															jAlert(msg_error_1,msg_error_notice,function(){
																$("#chk_saleman_id").val('').focus();
																return false;
											    			});
														}else if($.trim(arr_data[3])=='P'){
															var msg_error_2='';
															if(actions='audit'){
																msg_error_2="ขณะนี้พนักงานไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง";
															}else{
																msg_error_2="ขณะนี้พนักงานขายไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง";
															}
															jAlert(msg_error_2,msg_error_notice,function(){
																$("#chk_saleman_id").val('').focus();
																return false;
											    			});
														}else if($.trim(arr_data[3])=='N'){
															var msg_error_3='';
															if(actions='audit'){
																msg_error_3="ขณะนี้พนักงานไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง";
															}else{
																msg_error_3="ขณะนี้พนักงานขายไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง";
															}
															jAlert(msg_error_3,msg_error_notice,function(){															
																$("#chk_saleman_id").val('').focus();
																return false;
											    			});
														}else{
										            	if(data!=""){
											            	$("#csh_saleman_id").val(arr_data[0]);
											            	////////////// CANCEL BILL /////////////////
											            	 var opts_doccancel={
											    				     type:'post',
											    				     url:'/sales/accessory/canceldocno',
											    					 data:{
											    				    		doc_no:$acc_doc_no_val,
											    				    		doc_tp:$('#sel_typecancel').val(),
											    				    		employee_id:arr_data[0],
											    				    		rnd:Math.random()
											    				    	},success:function(data){
											    				    		alert(data);
											    				    		//return message :: status,promt message,title massage
												    				    	var arr_data=data.split('#');
												    				    	if(arr_data[0]=='Y'){
												    				    		 jAlert(arr_data[1],arr_data[2],function(){
											    					    			 $("#dlgSaleMan").dialog("close");
											    					    			 $("#acc_doc_no").val('').focus();
											    				    				 return false;
											    					    		 });
													    				    }else{
													    				    	 $('#dlgSaleMan').dialog('close');
													    				    	 jAlert(arr_data[1],arr_data[2],function(){
													    				    		// $("#chk_saleman_id").select().focus();														    				    		
											    				    				 return false;
											    					    		 });
														    				}											    					    
											    					    }
											    					  };
											    			  
											    			  $.ajax(opts_doccancel);
											            	////////////// CANCEL BILL /////////////////													       
											            	}//end if data!=''		
														}//end else arr_data
										            }//success							            
										    };//opts
								            $.ajax(opts);
								            return false;
								        }//key 13
									});//chk_saleman_id keypress
								});//dlgSaleman Load
						},				
						close: function(evt,ui){
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
								evt.stopPropagation();
 								evt.preventDefault();
							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
								evt.stopPropagation();
 								evt.preventDefault();
							}	
							$('#dlgSaleMan').dialog('destroy');							
						 }
					};			
					
					$('#dlgSaleMan').dialog('destroy');
					$('#dlgSaleMan').dialog(dialogOpts_saleman);			
					$('#dlgSaleMan').dialog('open');
				//-------------CHECK SALEMAN-----------------
	     }//end else
	     
	     //----------------------- new version ---------------------------------
	}//func
	
	$(function(){
		$("#sel_typecancel").change(function(evt){
			evt.preventDefault();
			$('#acc_doc_no').val('');
			var doc_tp=$(this).val();
			var opts_ncancel={
					type:'post',
					url:'/sales/accessory/countcancel',
					data:{
						doc_tp:doc_tp,
						rnd:Math.random()						
					},
					success:function(data){
						$('#n_cancel').html('  '+data).css({'font-size':'26px','color':'blue'});
					}
			};
			$.ajax(opts_ncancel);
			
			
		});
		$('#bws_docstatus').click(function(e){
			e.preventDefault();
			if($('#sel_typecancel').val()==''){
				 jAlert('กรุณาเลือกประเภทเอกสาร','ข้อความแจ้งเตือน',function(){
					 $('#sel_typecancel').focus();
    				 return false;
	    		 });
				return false;
			}
			var opts_dlgDoc={
					autoOpen:false,
					width:'75%',
					height:'550',
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"เลือกเลขที่เอกสาร",
					open:function(){		
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
		    			    										"font-size":"27px","color":"#000","padding":"5"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	    			    // button style		
	    			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});		
						$("*:focus").blur();
						$("#dlgDocNoCancel").html("");
						var opts_listdoc={
								type:'post',
								url:'/sales/accessory/listdocno',
								cache:true,
								data:{
									doc_tp_cancel:$("#sel_typecancel").val(),
									actions:'cancel_doc',
									now:Math.random()
								},
								success:function(data){	
									$("#dlgDocNoCancel").html(data);
									if($('p#item_not_found').length != 0){
										return false;
									}									
									var oTable2 = $('#datatables_listdocno').dataTable();
									$('#datatables_listdocno').dataTable({
										"bDestroy": true,
						       			"fnDrawCallback": function(){
							       		      $('table#datatables_listdocno td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
							       		      $('table#datatables_listdocno td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
							       		      
								       		   $('#datatables_listdocno tr').each(function(){
									       			$(this).click(function(e){
									       				e.preventDefault();
									       				var strJson=$(this).attr('idd');
									       				if(strJson!=''){
											       				var seldocno=$.parseJSON(strJson);											       				
											       				$('#acc_doc_no').val(seldocno.doc_no);				
											       				$('#acc_status_no').val(seldocno.status_no);//*WR02042013
											       				if(seldocno.status_no=='19'){
											       					$('#n_cancel').html('');
											       				}
															    $("#dlgDocNoCancel").dialog("close");
											       			}
									       			});
									       		});
							       		      
						       			}//end callback
									
									});	
									oTable2.fnSort([[0,'desc'],[1,'desc']]);
									
								}//end success 
						};
						$.ajax(opts_listdoc);
						
					},buttons: {							
						"ปิด":function(){ 
						$(this).dialog("close");
						return false;
					}
				 },
					close:function(){
						$('.tableNavDocStatus ul').navigate('destroy');
					}
			};
			$("#dlgDocNoCancel").dialog("destroy");
			$("#dlgDocNoCancel").dialog(opts_dlgDoc);			
			$("#dlgDocNoCancel").dialog("open");
		});//btn_rwd_doc_tp click

		$("#acc_doc_no").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13) {
	        	var cType=$('#sel_typecancel').val();
	        	if(cType==''){
	        		 jAlert('กรุณาระบุประเภทเอกสาร','ข้อความแจ้งเตือน',function(){
	        			 $('#sel_typecancel').focus();
	    				 return false;
		    		 });
	        		return false;
	        	}
		        var $acc_doc_no=$("#acc_doc_no");
		        $acc_doc_no_val=$.trim($acc_doc_no.val());
		        if($acc_doc_no_val.length==0) return false;
		        //check exist
		        var check_docno={
				        type:'post',
				        url:'/sales/accessory/checkdocno',
				        data:{
			        		doc_no:$acc_doc_no_val,
			        		flg_cancel:'Y',
			        		rnd:Math.random()
			        	},
				        success:function(data){
					        if(data=='Y'){
						        //cancel doc
						       $("#btn_commit").trigger("click");
						     }else{
						    	 jAlert('ไม่พบเลขที่เอกสารนี้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
						    		 $acc_doc_no.focus().select();
				    				 return false;
					    		 });
							 }
					    }
				};
				$.ajax(check_docno);
	            return false;
	        }
		});

		$("#btn_commit").click(function(e){
			e.preventDefault();			
			var cType=$('#sel_typecancel').val();			
        	if(cType==''){
        		 jAlert('กรุณาระบุประเภทเอกสาร','ข้อความแจ้งเตือน',function(){
        			 $('#sel_typecancel').focus();
    				 return false;
	    		 });
        		return false;
        	}
			 var $acc_doc_no=$("#acc_doc_no");
		          $acc_doc_no_val=$.trim($acc_doc_no.val());
		     if($acc_doc_no_val.length==0) return false;		     
		     $.ajax({
		    	 type:'post',
		    	 url:'/sales/accessory/chkdocdatecancel',
		    	 data:{
		    		 doc_no:$acc_doc_no_val,
		    		 doc_tp:cType,
		    		 rnd:Math.random()
		    	 },
		    	 success:function(data){
		    		 if(data=='N'){
		    			 jAlert('ไม่สามารถยกเลิกเอกสารย้อนหลังได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
				    		 $acc_doc_no.focus().select();
		    				 return false;
			    		 });
		    			 return false;
		    		 }else{
		    			 submitCancelDoc($acc_doc_no_val);
		    		 }
		    	 }
		     });			
		});

		$("#btn_commit").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13) {
	        	$("#btn_commit").trigger('click');
	            return false;
	        }
		});
		
	});//ready