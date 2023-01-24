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

//	    $(".keybarcode").each(function(){
//	    	$(this).keypress( function(evt) {
//				var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
//		        if(key == 13){
//		        	if($(this).val().length != 0 && $(this).val().length != 13){
//			        	return false;
//		        	}
//					return true;
//		        }else{
//		        		//number only
//						if((key!=8 && key!=0) && (key<48) || (key>57)){
//							return false;
//						}
//				} 
//			});		    	
//		 });
	    
	}//func
	//AREA LOCK KEY BARCODE OR KEY MANUAL	
	function popup(url,name,windowWidth,windowHeight){
	    myleft=(screen.width)?(screen.width-windowWidth)/2:100;
	    mytop=(screen.height)?(screen.height-windowHeight)/2:100;
	    properties = "width="+windowWidth+",height="+windowHeight;
	    properties +=",scrollbars=yes, top="+mytop+",left="+myleft;
	    window.open(url,name,properties);
	}//func
	
	function printBill(doc_no,doc_tp,thermal_printer){
		if(doc_no=='' || doc_no =='undefined') return false;
		var urlprint='';
		if(doc_tp=='RD'){
			if(thermal_printer=='Y'){
				urlprint='/sales/report/billactpointrd';
			}else{
				urlprint='/sales/report/billactpointrddotmatrix';
			}
		}else if(doc_tp=='SL'){
			if(thermal_printer=='Y'){
				urlprint='/sales/report/billactpointsl';
			}else{
				urlprint='/sales/report/billactpointsldotmatrix';
			}
		}
		var opts_print={
				type:'get',
				url:urlprint,
				cache:false,
				data:{
					doc_no:doc_no,
					rnd:Math.random
				},
				success:function(data){
				}
		};
		//popup(urlprint+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print
		$.ajax(opts_print);
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

	function initFormActPoint(){
		//GET LOCK STATUS
		getLockStatus();
		$("#act_member_no").val('');
		$("#act_point").val('');
		$("#act_fullname").val('');
		$("#act_address").val('');	
		$("#act_promo_code_hidden").val('');
	    $("#act_redeempoint_hidden").val('');
	    $("#act_amount_hidden").val('');
	    $("#txt_promo_description").html('');
		$("#act_member_no").focus();
	}//func

function confirmRedeemPoint(){
	var act_point=$("#act_redeempoint_hidden").val();
	var act_promo_code=$("#act_promo_code_hidden").val();
	var member_no=$("#act_member_no").val();
	var amount=$("#act_amount_hidden").val();	
	
	jConfirm('คะแนนที่คุณใช้แลกคือ '+act_point+' คะแนน\nคุณต้องการแลกใช่หรือไม่','ยืนยันการแลกคะแนน', function(r){
        if(r){
         
	          var opts_dlgcashier={
	        			autoOpen:false,
	  					width:'280',
	  					modal:true,
	  					resizeable:true,
	  					position:'center',
	  					showOpt: {direction: 'up'},		
	  					closeOnEscape:true,	
	  					title:"รหัสผู้เปิดบิล",
	  					open:function(){					
	  						 $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							  $(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
							    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
								    																			"font-size":"26px","color":"#0000FF",
								    																			"padding":"0 0 0 0"});	
	  						$("*:focus").blur();
	  						$("#dlgActivityPointCashier").html("");
	  						$("#dlgActivityPointCashier").load("/sales/member/formcashier",function(){
		  						var $act_cashier_id=$('#act_cashier_id');
		  						$act_cashier_id.val('').focus();
		  						$act_cashier_id.keypress( function(evt){									
	  								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	  						        if(key == 13){
	  						        	evt.stopImmediatePropagation();
	  						            evt.preventDefault();
	  						          	if($.trim($act_cashier_id.val()).length==0){
		  						          	jAlert('กรุณาระบุรหัสผู้เปิดบิล', 'ข้อความแจ้งเตือน',function(){                        		
			  						          	$act_cashier_id.focus();
		  		    							return false;
		  		    						});	
		  						        }else{
		  						        	 var opts_emp={
											            type:"post",
											            url:"/sales/cashier/getemp",
											            data:{
											            	employee_id:$.trim($act_cashier_id.val()),
															actions:'saleman'
										            	},
										            	success:function(data){				
															if($.trim(data)==""){
																jAlert('ไม่พบรหัสผู้เปิดบิล', 'ข้อความแจ้งเตือน',function(){   
																	$("*:focus").blur();                     		
							  		                    	  		$act_cashier_id.select();
							  		    							return false;
							  		    						});	
															}
											            	if(data!=""){
											            	  //-------------------------- start to save --------------
											            	  var arr_data=data.split('#');
											            	  var act_fullname=$("#act_fullname").val();
											            	  var act_address=$("#act_address").val();
											            	  
										            		  var opts_payment={
										                             type:'post',
										                             url:'/sales/member/paymentactpoint',
										                             data:{
										                         			promo_code:act_promo_code,
										                         			redeem_point:act_point,
										                         			member_no:member_no,
										                         			amount:amount,
										                         			cashier_id:arr_data[0],
										                         			name:act_fullname,
										                         			address:act_address,
										                         			rnd:Math.random()
										                         		},
										                         	  success:function(data){
										                             	 $("#dlgActivityPointCashier").dialog("close");
										                             	  var arr_result=data.split('#');
										                             	  if(arr_result[0]=='1'){
										                                 	  	printBill(arr_result[1],arr_result[2],arr_result[3]);
											                                 	 //mark point act cluster											
																					$.ajax({
																						type:'post',
																						url:'/sales/cashier/initmarkpoint',
																						cache:false,
																						data:{
																							doc_no:arr_data[1],
																							rnd:Math.random()
																						},
																						success:function(){}
																					});
												                               		jAlert('ผลการแลกคะแนนกิจกรรมสมบูรณ์', 'ผลการบันทึก',function(){                        		
												                               			initFormActPoint();
												               							return false;
												               						});	
										                                 }else  if(arr_result[0]=='0'){
												                               	  	jAlert('เกิดความผิดพลาดไม่สามารถบันทึกผลการแลกคะแนนได้', 'ข้อความแจ้งเตือน',function(){                        		
												                               	  		$("#act_member_no").focus();
												               							return false;
												               						});	
										                                 }
										                             }
										                         };
											            		 $.ajax(opts_payment);
											            		//-------------------------- end to save ----------------
											            		
												            }
											            	
											            }								            
											    };
									            $.ajax(opts_emp);
			  						    }
	  						            return false;
	  						        }
	  							});
	  						});//load ajax
	  					}
	  					
	  			};
	  			$("#dlgActivityPointCashier").dialog("destroy");
	  			$("#dlgActivityPointCashier").dialog(opts_dlgcashier);			
	  			$("#dlgActivityPointCashier").dialog("open");
	    	};
                
	      return false;
        });

}//func
function redeemPoint(){
	var $act_status_no=$("#act_status_no");
	var status_no=jQuery.trim($act_status_no.val());
	var $act_member_no=$("#act_member_no");
	var member_no=$.trim($act_member_no.val());
	if(member_no.length==0){
		jAlert('กรุณาระบุรหัสสมาชิก','ข้อความแจ้งเตือน',function(){
			$act_member_no.focus();
			return false;
		});	
	}else{	
		$.getJSON(
				"/sales/cashier/ajax",
				{
					status_no:status_no,
					member_no:member_no,
					actions:'memberinfo'
				},
				function(data){		
					if(data=='2'){
						jAlert('ไม่พบรหัสสมาชิกนี้กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
							$act_member_no.focus();
							return false;
						});
					}else{
						$.each(data.member, function(i,m){
							if(m.exist=='yes'){
								var member_fullname=m.name+" "+m.surname;
								if(m.birthday!=''){
									var arr_birthday=m.birthday.split('-');
									var birthday=arr_birthday[2]+"/"
										+arr_birthday[1]+"/"
										+(parseInt(arr_birthday[0]));
								}else{
									var birthday='';
								}

								if(m.apply_date!=''){
									var arr_apply_date=m.apply_date.split('-');
									var apply_date=arr_apply_date[2]+"/"
										+arr_apply_date[1]+"/"
										+(parseInt(arr_apply_date[0]));
								}else{
									var apply_date='';
								}

								if(m.expire_date!=''){
									var arr_expire_date=m.expire_date.split('-');
									var expire_date=arr_expire_date[2]+"/"
										+arr_expire_date[1]+"/"
										+(parseInt(arr_expire_date[0]));
								}else{
									var expire_date='';
								}	

								var refer_member_id='';
								var remark=m.remark;
								var percent_discount=parseInt(m.percent_discount);
								var mp_point_sum=m.mp_point_sum;
								var buy_net=m.buy_net;
								/*
								var address=$.trim(m.h_address)+" "+
											$.trim(m.h_village_id)+" "+
											$.trim(m.h_village)+" "+
											$.trim(m.h_soi)+" "+
											$.trim(m.h_road)+" "+
											$.trim(m.h_district)+" "+
											$.trim(m.h_amphur)+" "+
											$.trim(m.h_province)+" "+
											$.trim(m.h_postcode);
								*/
								var address=$.trim(m.address)+" "+
								$.trim(m.sub_district)+" "+
								$.trim(m.district)+" "+
								$.trim(m.province_name)+" "+
								$.trim(m.zip);
								//alert(address);
								
								$('#act_fullname').val(member_fullname);
								$('#csh_birthday').html(birthday);
								$('#csh_apply_date').html(apply_date);
								$('#csh_expire_date').html(expire_date);
								$('#csh_member_type').html(remark);
								$('#csh_percent_discount').html(percent_discount);
								$('#act_point').val(mp_point_sum);
								$('#csh_buy_net').html(buy_net);
								$('#act_address').val(address);
							}
							
							if(m.status=='1'){
								if(m.mem_status=='N'){
									jAlert('รหัสสมาชิกนี้ถูกกำหนดให้มีสถานะเป็น non active เพราะสมัครบัตรใหม่ \n กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
										initFieldOpenBill();
										return false;
									});	
								}else if(m.mem_status=='F'){
									jAlert('รหัสสมาชิกนี้ถูกกำหนดให้มีสถานะเป็น non active \n เพราะเป็นบัตรเสีย\n กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
										initFieldOpenBill();
										return false;
									});	
								}else if(m.mem_status=='T'){
									jAlert('รหัสสมาชิกนี้ถูกกำหนดให้มีสถานะเป็น non active \n เพราะเป็นบัตรหาย\n กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
										initFieldOpenBill();
										return false;
									});	
								}
								return false;
							}
							
							if(m.expire_status=='Y'){
								jAlert('รหัสสมาชิกนี้บัตรหมดอายุกรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
									//initFormCashier();
									initFieldOpenBill();
									return false;
								});	
								return false;									
							}
							//----------- start redeem point---------
							var opts_dlgActivityPoint={
									autoOpen:false,
									width:'70%',
									modal:true,
									resizeable:true,
									position:'center',
									showOpt: {direction: 'up'},		
									closeOnEscape:true,	
									title:"เลือกโปรโมชั่น",
									open:function(){					
										 $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
										  $(this).dialog('widget')
								            .find('.ui-dialog-titlebar')
								            .removeClass('ui-corner-all')
								            .addClass('ui-corner-top');
										    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
											    																			"font-size":"26px","color":"#0000FF",
											    																			"padding":"0 0 0 0"});	
										$("*:focus").blur();
										$("#dlgActivityPoint").html("");
										$.ajax({
											type:'post',
											url:'/sales/member/promoactpointhead',
											cache:false,
											data:{
												now:Math.random()
											},
											success:function(data){
												$("#dlgActivityPoint").html(data);
												$(this).parent().find('select, input, textarea').blur();
												$('.tableNavPromoActPoint ul li').not('.nokeyboard').navigate({
											        wrap:true
											    }).click(function(evt){		
											    	evt.stopImmediatePropagation();	
												    evt.preventDefault();						
												   	if($(this).attr('idpromo')==''){
												   		$("#dlgActivityPoint").dialog("close");
												   		setTimeout(function(){
												   			$('#act_member_no').focus();
													   	},400);
													   	return false;
													}				    
												    var arr_promopoint=$(this).attr('idpromo').split('#');
												    $("#act_promo_code_hidden").val(arr_promopoint[0]);
												    $("#act_redeempoint_hidden").val(arr_promopoint[2]);
												    $("#act_amount_hidden").val(arr_promopoint[3]);
												    
												   // $("#txt_promo_code").html(arr_promopoint[0]+"  "+arr_promopoint[1]);
												   // $("#txt_promo_des").html();
												   // $("#txt_point_used").html(arr_promopoint[2]);
												    
												    var act_point=$("#act_point").val();
												  	if(parseInt(act_point)<parseInt(arr_promopoint[2])){
												  		jAlert('คะแนนสะสมน้อยกว่า '+arr_promopoint[2]+' ไม่สามารถแลกได้', 'ข้อความแจ้งเตือน',function(){
															$('#act_member_no').focus();
															return false;
														});	  	
														return false;
													}else{
														$("#dlgActivityPoint").dialog("close");														
														//setTimeout("confirmRedeemPoint()",200);
														//confirmRedeemPoint(arr_promopoint[2]);
														$("#txt_promo_description").html('');
														var opts_desc={
																type:'post',
																url:'/sales/member/promoactpointdescription',
																cache:false,
																data:{
																   member_no:member_no,
																   promo_code:arr_promopoint[0],
																   //redeem_point:arr_promopoint[2],
																   //hdesc:arr_promopoint[1],
																   rnd:Math.random()																    
																},success:function(data){
																	$("#txt_promo_description").html(data);		
																}
														};
														$.ajax(opts_desc);
														
													}
													
												});
												
											}//end success function
										});//end ajax pos
										
									},
									close:function(){
										$('.tableNavPromoActPoint ul').navigate('destroy');
										$("#dlgActivityPoint").dialog("destroy");
									}
									
							};
							$("#dlgActivityPoint").dialog("destroy");
							$("#dlgActivityPoint").dialog(opts_dlgActivityPoint);			
							$("#dlgActivityPoint").dialog("open");
							//----------- end redeem point ----------						
						});					
					}
				}				
		);//end json
		
	}//end if	
	return false;		
}//func

//*WR 24052016 for suport idcard
function callbackReadIdCard(data){
		$('#dlgActPoint').dialog('close');
		var arr_data=data.split('#');
		$('#act_member_no').val(arr_data[0]);
		cmdEnterKey("act_member_no");
}//func

$(function(){
	initFormActPoint();
	
	//*WR24052016 for support id card
	$("#bws_idcard").click(function(e){
		e.preventDefault();
		$('<div id="dlgActPoint"><span style="color:#336666;font-size:20px;"></span><input type="hidden" size="15" id="id_card_ref"/></div>').dialog({
	           autoOpen:true,
			   width:'550',
			   height:'350',	
			   modal:true,
			   resizable:false,
			   closeOnEscape:false,		
	           title: "สแกนรหัสบัตรประชาชน",
	           position:"center",
	           open:function(){
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
		  			$(this).dialog("widget").find(".ui-btndlgpos")
		                  .css({"padding":"2","background-color":"#C7D9DC","font-size":"14px"});
		  			$(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
					    	$("#pdt_redeempoint").focus();
						});
		  			  
		  			  $.get("../../../download_promotion/id_card_newmem/api_verify_from.php?function_next=callbackReadIdCard&member_no=", function(data) {
		  				   $('#dlgActPoint').empty().html(data);
		  				});
		  	},close:function(evt){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
						evt.stopPropagation();
						evt.preventDefault();		
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();  						
					}				
					$('#dlgActPoint').remove();	
	           }
		});
		
	});//func
	
	$("#act_member_no").keypress( function(evt){									
		var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
        if(key == 13){
            evt.preventDefault();
            redeemPoint();
            return false;
        }
	});
	
	$("#btnSave").click(function(e){
		e.preventDefault();
		var act_member_no=$("#act_member_no").val();
		var act_promo_code_hidden=$("#act_promo_code_hidden").val();
		var act_point=$("#act_point").val();
		if($.trim(act_member_no).length==0){
			jAlert('กรุณาระบุรหัสสมาชิก', 'ข้อความแจ้งเตือน',function(){
				$("#act_member_no").focus();
				return false;
			});	
			return false;
		}
		
		if($.trim(act_point).length==0 || act_point=='0'){
			jAlert('คะแนนไม่พอใช้', 'ข้อความแจ้งเตือน',function(){
				$("#act_point").focus();
				return false;
			});	
			return false;
		}
		
		if($.trim(act_promo_code_hidden).length==0){
			jAlert('ไม่พบโปรโมชั่น', 'ข้อความแจ้งเตือน',function(){
				$("#act_member_no").focus();
				return false;
			});	
			return false;
		}
		
		confirmRedeemPoint();
	});
	
	$("#btnCancel").click(function(e){
		e.preventDefault();
		initFormActPoint();
	});
	
});