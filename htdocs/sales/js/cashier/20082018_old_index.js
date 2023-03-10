function validatePhone(phoneText) {
	var filter = /^[0-9-+]+$/;
	if (filter.test(phoneText)) {
		return true;
	}
	else {
		return false;
	}	
}//func
$(function(){	
//				$('#xxx').click(function(e){
//					e.preventDefault();
//					testfunction();
//				});
				$("#icons li")
				.mouseenter(function() {
					$(this).addClass('ui-state-hover');
				})
				.mouseleave(function() {
					$(this).removeClass("ui-state-hover");
				});	
				
				//*WR18062015 for idcard
				$('#btnBwsIdCard').click(function(e){
					e.preventDefault();
					var chk_member_no=$('#csh_member_no').val();
					chk_member_no=$.trim(chk_member_no);
					if(chk_member_no.length>0){
						jAlert('Found a pending transaction. Please cancel the document and make a new transaction.', 'WARNING!',function(){
							$('#btnBwsIdCard').focus();
							return false;
						});	
						return false;
					}
					ccsreadidcardfrom(); 
					return false;
				}).css({"cursor":"pointer"});
	});//ready
	
	//Global Variable
	var MSGBOX_DISPLAY_ERROR_01;
	var MSGBOX_DISPLAY_INFO_01;
	function PreventRefresh(e) {
		var key;//IE
		if(window.event){
			key = window.event.keyCode;
			if (key == 116){//keycode for F5 function
				e.keyCode = 0;e.returnValue = false;
				e.cancelBubble = true;
				return false;
			}
		}
		//firefox
		else{
			key = e.which;
			if (key == 116){
				e.stopPropagation();
				e.preventDefault();
				return false;
			}
		}
	}//func

	$(document).bind("keydown",PreventRefresh);
	setOnlineMode('1');
	
	function formTour(promo_code){
		/**
		 * @param application_id promo_code
		 * @desc modify:24042014
		 */		
		$("<div id='dlgFormTour'></div>").dialog({
	       	   autoOpen:true,
	  				width:'35%',		
	  				height:'auto',	
	  				modal:true,
	  				resizable:false,
	  				position: { my: "center bottom", at: "center center", of: window },
	  				showOpt: {direction: 'up'},		
	  				closeOnEscape: false,
	  				title:"<span class='ui-icon ui-icon-person'></span>Please confirm customer information.",
	  				open: function(){ 
	  					//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
																										"font-size":"24px","color":"#000000",
																										"padding":"5 0.1em 0 0.1em"});   //ui-corner-all
						$("#dlgFormTour").html('');
		            	$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
		            	$(this).dialog("widget").find(".ui-button-text")
	                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
		            	//create form tour
        				$.ajax({
        					type:'post',
        					url:'/sales/accessory/formtour',
        					data:{
        						rnd:Math.random()
        					},success:function(data){
        						$("#dlgFormTour").html(data);
        						$('#tour_barcode').val('');
        						$('#tour_passport_no').val('');
        						$('#tour_email').val('');
        						$('#tour_barcode').focus();
        						
        						//*** check lock unlock
    							if($("#csh_lock_status").val()=='Y'){
    								lockManualKey();
    							}else{
    								unlockManualKey();
    							}	
    							//*** check lock unlock		 
    							
    							//*WR modify 22052017    							
    							$('select#tour_country option[value=""]').attr("selected",true);
    							
    							$('#tour_barcode').keypress(function(evt){
    								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
    						        if(key == 13){
    						            evt.preventDefault();
    						            var tour_barcode=$('#tour_barcode').val();
    						            tour_barcode=$.trim(tour_barcode);
    						            if(tour_barcode.length<1){
    						            	jAlert('Please specify tour barcode.','WARNING!',function(){
    				    						$('#popup_ok').focus();
    				    						setTimeout(function(){				    							
    				    							if($('#popup_ok').not(":focus")){
    						    						$('#popup_ok').focus();
    						    					}
    				    						},1000);
    				    						$('#tour_barcode').val('').focus();
    											return false;
    										});	
    							 			return false;    						            	
    						            }
    						            var chk_tour_digit=tour_barcode.substring(0,5);
    						            //alert("chk_tour_digit=>" + chk_tour_digit);    						            
    						           	if(chk_tour_digit!=='PRCTG'){
    						           		
    						           		jAlert('Barcode coupon code is invalid.','WARNING!',function(){
    				    						$('#popup_ok').focus();
    				    						setTimeout(function(){				    							
    				    							if($('#popup_ok').not(":focus")){
    						    						$('#popup_ok').focus();
    						    					}
    				    						},1000);
    				    						$('#tour_barcode').val('').focus();
    											return false;
    										});	
    							 			return false;    
    						           		
    						           	}
    						            $('#tour_passport_no').focus();    						            
    						            return false;
    						        }
    							});    							
        						
    							$('#tour_passport_no').keypress(function(evt){
    								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
    						        if(key == 13){
    						            evt.preventDefault();
    						            var tour_passport_no=$('#tour_passport_no').val();
    						            tour_passport_no=$.trim(tour_passport_no);
    						            if(tour_passport_no.length<1){
    						            	jAlert('Please specify Passport number.','WARNING!',function(){
    				    						$('#popup_ok').focus();
    				    						setTimeout(function(){				    							
    				    							if($('#popup_ok').not(":focus")){
    						    						$('#popup_ok').focus();
    						    					}
    				    						},1000);
    				    						$('#tour_passport_no').val('').focus();
    											return false;
    										});	
    							 			return false;    						            	
    						            }
    						            $('#tour_email').focus();    						            
    						            return false;
    						        }
    							});
    							
    							$('#tour_email').keypress(function(evt){
    								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
    						        if(key == 13){
    						            evt.preventDefault();
    						            $('#btnNextMstProTour').focus();    						            
    						            return false;
    						        }
    							});
    							
        					}
        				});//end ajax
	  				},
	  				///////////////////////////XXX//////////////////////////
	  				buttons: [ 
		    	            { 
		    	                text: "Next",//???????????????
		    	                id:"btnNextMstProTour",
		    	                class: 'ui-btndlg-next', 
		    	                click: function(evt){ 
		    	                	evt.preventDefault();
							 		evt.stopPropagation();	
							 		var tour_barcode=$('#tour_barcode').val();
							 		var tour_passport_no=$('#tour_passport_no').val();
							 		var tour_email=$('#tour_email').val();
							 		var coupon_code=tour_barcode + "#" + tour_passport_no + "#" + tour_email;
							 		
							 		
						            tour_barcode=$.trim(tour_barcode);
						            if(tour_barcode.length<1){
						            	jAlert('Please specify tour barcode.','WARNING!',function(){
				    						$('#popup_ok').focus();
				    						setTimeout(function(){				    							
				    							if($('#popup_ok').not(":focus")){
						    						$('#popup_ok').focus();
						    					}
				    						},1000);
				    						$('#tour_barcode').val('').focus();
											return false;
										});	
							 			return false;    						            	
						            }
						            
						            var chk_tour_digit=tour_barcode.substring(0,5);
						            //alert("chk_tour_digit=>" + chk_tour_digit);						            
						           	if(chk_tour_digit!=='PRCTG'){
						           		
						           		jAlert('Barcode coupon code is invalid.','WARNING!',function(){
				    						$('#popup_ok').focus();
				    						setTimeout(function(){				    							
				    							if($('#popup_ok').not(":focus")){
						    						$('#popup_ok').focus();
						    					}
				    						},1000);
				    						$('#tour_barcode').val('').focus();
											return false;
										});	
							 			return false;    
						           		
						           	}
						           	
						        	//*WR modify 22052017
						           	var tour_country=$('select#tour_country').find('option:selected').val();
						           	if(tour_country.length<1){							 			
							 			jAlert('Please specify country.','WARNING!',function(){		
							 				$('#popup_ok').focus();
				    						setTimeout(function(){				    							
				    							if($('#popup_ok').not(":focus")){
						    						$('#popup_ok').focus();
						    					}
				    						},1000);
				    						$('#tour_country').focus();
											return false;
										});	
							 			return false;
							 		}
							 		
							 		//alert("coupon_code=>" + coupon_code);							 		
							 		if(tour_passport_no.length<1){							 			
							 			jAlert('Please specify Passport number.','WARNING!',function(){
				    						$('#popup_ok').focus();
				    						setTimeout(function(){				    							
				    							if($('#popup_ok').not(":focus")){
						    						$('#popup_ok').focus();
						    					}
				    						},1000);
				    						$('#tour_passport_no').val('').focus();
											return false;
										});	
							 			return false;
							 		}
							 		
							 		//*WR modify 22052017
							 		tour_passport_no='PP:'+ tour_passport_no + ':' + tour_country;
							 		//tour_passport_no='PP:'+ tour_passport_no;
							 		$('#vt_passport_no_val').val(tour_passport_no);							 		
							 		callBackToDo2(promo_code,'Y','',tour_passport_no,coupon_code,'');							 		
							 		$('#dlgFormTour').dialog('close');
	    	                }
	    	            }
		    	  ]			
				,beforeClose:function(evt){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
						//evt.stopPropagation();
						evt.preventDefault();
						jConfirm('Do you want to cancel promotions?','WARNING!', function(r){
					        if(r){
					        	initFormCashier();
								initTblTemp();
								getCshTemp('Y');
								$("#dlgFormTour").dialog('close');
					        }
						});			
						
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();  						
					}					
									
					//*WR14012015 ===========================
				}
				,
	  				///////////////////////////XXX//////////////////////////
	  				close:function(evt){
	  					$("#dlgFormTour").remove();
		            	$("#dlgFormTour").dialog("destroy");	  					
		   			}
	          });
	}//func
	
	function callLstChkPro(){
		/**
		 * @desc call lastprochk()
		 * @create 04102016
		 * @retrun
		 */			
		var member_no=$('#csh_member_no').val();		
		var mem_card_info=$('#id_smspromo').val();
		$.ajax({
			type:'post',
			url:'/sales/member/setcshvaltemp',
			data:{
				member_no:member_no,
				mem_card_info:mem_card_info,
				rnd:Math.random()
			},success:function(){
				lastprochk();
			}
		});		
	}//func
	
	function setCardDummy2Temp(application_id,member_no){
		/**
		 * @desc set product dummy for change card to idcard
		 * @create 15032016
		 * @retrun
		 */
        
        var member_no=$("#csh_refer_member_id").val();        
        //alert("you call setCardDummy2Temp func.  " + member_no);		
		var doc_tp=$('#csh_doc_tp').val();
		var status_no=$('#csh_status_no').val();
		var employee_id=$('#csh_employee_id').val();		
		$.ajax({
			type:'post',
			url:'/sales/accessory/setcarddummy2temp',
			data:{
				application_id:application_id,
				rnd:Math.random()
			},success:function(data){
				getCshTemp('N');
				if(application_id=='OPPF300'){
					paymentBill('01');
				}else if(application_id=='OPPD300' || application_id=='OPPA300' || application_id=='OPPC300'){
					freeProduct(application_id,employee_id,member_no,status_no,doc_tp);
				}else if(application_id=='OPPB300'){					
					giftsetProduct(application_id,employee_id,member_no,status_no,doc_tp);					
				}else{
					setTimeout(function(){
						  getCatProduct(selpromo.application_id,1,0,0,0,0);
					  },400);
				}
				return false;
			}
		});
		
	}//func
	function callBackCardLost(data){
		/**
		 * @desc
		 * @create 06082015
		 */
		var arr_callbackdata=data.split('#');//idcard reader callback
		var str_search_result=$('#csh_id_card').val();//search crm callback
		var arr_search_result=str_search_result.split('#');
		if(arr_search_result[0]==arr_callbackdata[0]){
			//idcard
			$('#csh_member_no').focus();
		}else if(arr_search_result[1]==arr_callbackdata[1] && arr_search_result[2]==arr_callbackdata[2] && arr_search_result[3]==arr_callbackdata[3]){
			$('#csh_member_no').focus();
		}else{
			jAlert("CRM DATA :: " + str_search_result + "\nIDCARD READER :: " + data+"\n???????????????????????????????????????????????????????????????/????????????????????? ????????????????????????????????????????????????????????????????????????????????????\n????????????????????????????????????????????????????????????","WARNING!",function(){				
				$('#csh_id_card').val('');
				initTblTemp();
				initFormCashier();
				window.location.reload(true);
				return false;
			});			
		}
		$('#csh_id_card').val(arr_search_result[0]);
	}//func
	////////////////////////////////////////////Support Cooporation//////////
	function callBackCoOpration(data){
		/**
		 * @create 27072015
		 * @return 
		 */
		var arr_coop=data.split('#');
		$('#csh_ops_day').val(data);
		var e = $.Event('keypress');
	    e.which = 13; 
	    $('#csh_member_no').trigger(e);
	    if(arr_coop[2]=='GROUP_TOUR'){
	    	$('#other_promotion_title').append('<span> : ' + arr_coop[3]  + '</span>');
	    }
	}//func	
	//////////////////////////////////////////xx////////////////////////////////////////
	function lstMcpPromotion(promo_code){
		/**
		 * @desc play last promotion 
		 * @create 24032015
		 */		
		$.ajax({
			type:'post',
			url:'/sales/member/chklstotherpro',
			data:{
				promo_code:promo_code,
				rnd:Math.random()
			},success:function(data){
				if(data=='Y'){
					lastprochk(); //Joke append 22072014
				}else{
					paymentBill('00');	
				}
				return false;
			}
		});
		return false;
	}//func
	
	function mstMcpPromotion(promo_code,chk_status,line_msg_error,idcard,coupon_code){
		/**
		 * @desc : 
		 * *support table promo_other only
		 * *set to temp ver. support promotion M,L
		 * @create 24032015 for support LINE & Mobile App.
		 * @return null
		 */
	   //alert(promo_code +","+chk_status+","+line_msg_error +","+ idcard +","+ coupon_code);
		if(chk_status=='Y'){
			if($("#dlgPdtOtherPro").dialog( "isOpen" )===true) {
            	return false;
            }//if
			//*WR08092015
    		var chk_tbl_temp="";
			$('<div id="dlgPdtOtherPro"><span style="color:#336666;font-size:20px;">?????????????????????????????? : </span><input type="text" size="15" id="pdt_otherpro"/></div>').dialog({
		           autoOpen:true,
				   width:'400',
				   height:'200',	
				   modal:true,
				   resizable:false,
				   closeOnEscape:false,		
		           title: "Scan Product",
		           position:"center",
		           open:function(){
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');		
						//button style	
		  			    $(this).dialog("widget").find(".ui-btndlgpos")
		                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
		  			  $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
					    	$("#pdt_redeempoint").focus();
						});
			  		  $('#pdt_otherpro').keypress(function(evt){																    	
						    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
			   			        if(key == 13){	
					    			evt.preventDefault();
					    			evt.stopPropagation();
					    			//*WR10092015
									if(chk_tbl_temp=='trn_promotion_tmp1'){
										lastdelpro();//*WR30032015 joke
									}
					    			//lastdelpro();//*WR30032015 joke
					    			$('#btnCommitOtherProm').trigger('click');
					    			return false;
			   			        }
						    });
		           },
    				buttons: [ 
				    	            { 
				    	                text: "Save",
				    	                id:"btnCommitOtherProm",
				    	                class: 'ui-btndlgpos', 
				    	                click: function(evt){ 
				    	                	evt.preventDefault();
									 		evt.stopPropagation();
									 		var product_id=$('#pdt_otherpro').val();
									 		product_id=$.trim(product_id);																		 			
								    		if(product_id.length<1){
							    				jAlert('Scan Product', 'WARNING!',function(){
							    					$('#pdt_otherpro').val('').focus();
							    					return false;
							    				});	
							    				return false;
								    		}
								    		var member_no=$('#csh_member_no').val();																													    	
								    		var status_no=$('#csh_status_no').val();
								    		//*WR12012015
								    		var member_percent=$('#csh_percent_discount').html();
				  							 $.ajax({
				  								 type:'post',
				  								 url:'/sales/member/setpdtotherpro',
				  								 data:{
				  									 member_no:member_no,																									  									
				  									 promo_code:promo_code,
				  									 product_id:product_id,
				  									 quantity:'1',
				  									 status_no:status_no,
				  									 member_percent:member_percent,
				  									 rnd:Math.random()
				  								 },success:function(data){	
				  									var arr_data=data.split('#');
				  									if(arr_data[0]=='1'){	
				  										//*WR08092015
				  										if(arr_data[2]=='trn_promotion_tmp1'){
				  											chk_tbl_temp='trn_promotion_tmp1';
				  											getPmtTemp('P');
				  										}else{
				  											getCshTemp('P');
				  										}
				  										setTimeout(function(){
				  											$('#pdt_otherpro').val('').focus();
				  										},400);
								    				}else{		
								    					//*WR04092045 Mcoupon
								    					jAlert(arr_data[1],'WARNING!',function(){
								    						$('#popup_ok').focus();
								    						setTimeout(function(){
								    							//if($('#csh_product_id').is(":focus")){
								    							if($('#popup_ok').not(":focus")){
										    						$('#popup_ok').focus();
										    					}
								    						},1000);
								    						$('#pdt_otherpro').val('').focus();
															return false;
														});	
    					
								    				}//end if	
				  								 }
				  							 });//ajax		
			    	                }
			    	            },
			    	            { 
			    	                text: "Payment",
			    	                id:"btnMcpPayment",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	evt.preventDefault();
								 		evt.stopPropagation();							    		
								 		$('#dlgPdtOtherPro').dialog('close');
		    	                }
		    	            }
			    	  ]			
					,beforeClose:function(){
						//*WR10092015
						if(chk_tbl_temp=='trn_promotion_tmp1'){
							getPmtTemp('P');
						}else{
							getCshTemp('P');
						}						
						//*WR14012015 =======================
						var start_baht=$('#csh_start_baht').val();
						var end_baht=$('#csh_end_baht').val();
						var buy_type=$("#csh_buy_type").val();//Gross,Net
						var buy_status=$("#csh_buy_status").val();//G >=,L<
						if(buy_type=='N' ){
							var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
					 		net_amt=parseFloat(net_amt);
							var start_baht=start_baht.replace(/[^\d\.\-\ ]/g,'');
							 start_baht=parseFloat(start_baht);
							 if(net_amt<start_baht){
								 jAlert("Minimum net amount required.  " + start_baht + " baht", 'WARNING!',function(){																						
									 $('#pdt_otherpro').val('').focus();
										return false;
								});
								 return false;																		    										 
							 }
						}						
						//*WR14012015 ===========================
					}
					,close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
    						evt.preventDefault();  						
						}
						if(onSales()==0){
							jAlert('No sale transaction can not be found.', 'WARNING!',function(){
								initFormCashier();
								initFieldOpenBill();
								$("#csh_member_no").focus();
		    					return false;
		    				});		 
							return false;
						}
						//*WR08092015
						if(chk_tbl_temp=='trn_promotion_tmp1'){
								lastdelpro();//*WR30032015 joke
								setTimeout(function(){
									lstMcpPromotion(promo_code);
								},400);
						}else{
							setTimeout(function(){
								paymentBill('00');
							},400);
						}
						$('#dlgPdtOtherPro').remove();	
		           }
			});
		}else{
			jAlert(line_msg_error, 'WARNING!',function(){
				initFormCashier();
				initFieldOpenBill();
				$("#csh_member_no").focus();
				return false;
			});
		}
		return false;
	}//func
	 //////////////////////////////////////////xx////////////////////////////////////////
	function lineApp3(application_id,a,b,c,d,e,idcard,coupon_code,friend_id_card,friend_mobile_no,promo_code,reg_mobile_no){
		 /***
		  * @desc
		  * @create WR05052015 OPMGMC300
		  * @modify WR23122015 OPMGMI300
		  */
		  var str_coupon=friend_id_card+"#"+friend_mobile_no+"#"+promo_code+"#"+coupon_code;
		  if(application_id=='OPMGMC300' || application_id=='OPMGMI300'){
			   $("#csh_mobile_no").val(reg_mobile_no);
		   }
		    $("#csh_id_card").val(idcard);
			$("#csh_coupon_code").val(str_coupon);
			if(application_id=='OPMGMI300'){
				$.ajax({
					  type:'post',
					  url:'/sales/member/getcardquota',
					  data:{
						  rnd:Math.random()
					  },success:function(data){
						  var objcq=$.parseJSON(data);
						  var sp_day=objcq[0].ops;
						  var cq_detail= " Special Day : " + objcq[0].ops;
						  $('#csh_ops_day').val(sp_day);
						  $('#info_apply_promo').empty().html(application_id);
						  $('#info_apply_promo_detail').empty().html(cq_detail);
						  //freeProduct(application_id,'','','01','SL');
						  //*WR27052016 append mini bueaty book 2016
						  getCatProduct(application_id,0,0,0,0,0);
					  }
				});
			}else{
				getCatProduct(application_id,0,0,0,0,0);
			}
			return false;
	 }//func
	
	
	
	 function lineApp2(application_id,a,b,c,d,e,idcard,coupon_code){
		 /*
		  * @desc line ?????????????????????????????????
		  */
		 var str_coupon=idcard + "#" + coupon_code;
		 $("#csh_coupon_code").val(str_coupon);
		 if(application_id=='OPPLC300'){
			 $('#status_couponpromo').val('Y');
			 $("#csh_id_card").val(idcard);
			 $("#csh_coupon_code").val(coupon_code);
			 getCatProduct(application_id,0,0,0,0,0);
		 }
		 return false;
	 }//func
	 
	 function lineApp(promo_code,chk_status,line_msg_error,idcard,coupon_code){
			/**
			 * @desc
			 * @create 20012015
			 * @return
			 */
		   //alert(promo_code +","+chk_status+","+line_msg_error +","+ idcard +","+ coupon_code);
			if(chk_status=='Y'){
				//*WR 08052015
				$('#status_couponpromo').val('N');
				if(promo_code=='OX02230315' || promo_code=='OX02031215'){
					//2016 member get member
					//var str_coupon=promo_code+"#"+coupon_code;
					//$("#csh_coupon_code").val(str_coupon);
					$("#csh_coupon_code").val(coupon_code);//coupon_code=3409900553439#095719
				}
				
				//*WR13022015
				if(promo_code=='OI27010115'){
					var str_coupon=idcard+"#"+coupon_code;
					$("#csh_coupon_code").val(str_coupon);
				}
				
				//*WR23012015
				if(promo_code=='OI27120115'){
					$('#status_couponpromo').val('Y');
					$('#csh_coupon_code').val(promo_code);
					$('#csh_id_card').val(idcard);
				}
				
				//*WR30012015
				if(promo_code=='OI27150115'){
					//on top discount 100
					$('#status_couponpromo').val('Y');
					$('#csh_coupon_code').val(coupon_code);
					$('#csh_id_card').val(idcard);
				}
				
				//*WR04072016 OI02330516 04/07/2016 - 30/09/2016 ?????? 35% 2000
				//*WR11032016 OI02220216 14/03/2016 - 31/05/2016 ?????? 35% 2000
				if(promo_code=='OI02220216' || promo_code=='OI02330516'){
					$('#csh_coupon_code').val(promo_code);
					$('#csh_id_card').val(idcard);
				}
				
				//*WR25042016
				//alert('this l app');
				if(promo_code=='OK27290316' || promo_code=='OK03280316' || promo_code=='OK27440616' || 
						promo_code=='ON31350816' || promo_code=='OX31330916'){
					$('#status_couponpromo').val('Y');
					$('#csh_coupon_code').val(promo_code);
					$('#csh_id_card').val(idcard);
					$('#csh_promo_code').val(promo_code);
				}
				
				if($("#dlgPdtLineApp").dialog( "isOpen" )===true) {
	            	return false;
	            }//if
				//*WR26082015 for LINE support
				var promo_tp=$('#csh_promo_tp').val();
				$('<div id="dlgPdtLineApp"><span style="color:#336666;font-size:20px;">?????????????????????????????? : </span>' + 
						'<input type="text" size="15" id="pdt_lineapp"/></div>').dialog({
			           autoOpen:true,
					   width:'400',
					   height:'200',	
					   modal:true,
					   resizable:false,
					   closeOnEscape:false,		
			           title: "????????????????????????????????????????????????",
			           position:"center",
			           open:function(){
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');		
							//button style	
			  			    $(this).dialog("widget").find(".ui-btndlgpos")
			                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
			  			  $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
						    	$("#pdt_redeempoint").focus();
							});
				  		  $('#pdt_lineapp').keypress(function(evt){																    	
							    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
				   			        if(key == 13){	
						    			evt.preventDefault();
						    			evt.stopPropagation();
						    			$('#btnMCoupon').trigger('click');
						    			return false;
				   			        }
							    });
			           },
	    				buttons: [ 
					    	            { 
					    	                text: "????????????",
					    	                id:"btnMCoupon",
					    	                class: 'ui-btndlgpos', 
					    	                click: function(evt){ 
					    	                	evt.preventDefault();
										 		evt.stopPropagation();
										 		var product_id=$('#pdt_lineapp').val();
										 		product_id=$.trim(product_id);																		 			
									    		if(product_id.length<1){
								    				jAlert('Scan Product', 'WARNING!',function(){
								    					$('#pdt_lineapp').val('').focus();
								    					return false;
								    				});	
								    				return false;
									    		}
									    		//alert(m_promo_code + " ==> " + promo_id);
									    		var member_no=$('#csh_member_no').val();																													    	
									    		var status_no=$('#csh_status_no').val();
									    		//*WR12012015
									    		var member_percent=$('#csh_percent_discount').html();
					  							 $.ajax({
					  								 type:'post',
					  								 url:'/sales/member/setpdtmcoupon',
					  								 data:{
					  									 member_no:member_no,																									  									
					  									 promo_code:promo_code,
					  									 product_id:product_id,
					  									 quantity:'1',
					  									 status_no:status_no,
					  									 member_percent:member_percent,
					  									 rnd:Math.random()
					  								 },success:function(data){			
					  									// alert(data);
					  									var arr_data=data.split('#');
					  									if(arr_data[0]=='1'){			
					  										//*WR28082015
					  										if(promo_tp=='LINE'){
					  											getPmtTemp();
						  									}else{
						  										getCshTemp('P');
						  									}
					  										setTimeout(function(){
					  											$('#pdt_lineapp').val('').focus();
					  										},400);					  										
									    				}else{													    					
									    					jAlert(arr_data[1],'WARNING!',function(){
									    						$('#pdt_lineapp').val('').focus();
																return false;
															});
									    					
									    				}//end if									  									
					  									
					  								 }
					  							 });//ajax													 		
									 		
				    	                }
				    	            },
				    	            { 
				    	                text: "??????????????? >>",
				    	                id:"btnNextLineApp",
				    	                class: 'ui-btndlgpos', 
				    	                click: function(evt){ 
				    	                	evt.preventDefault();
									 		evt.stopPropagation();	
									 		$('#dlgPdtLineApp').dialog('close');
				    	                }
			    	                }
				    	  ]			
						,beforeClose:function(){
							//*WR11032016 =======================
							var net_amt=$('#csh_net').val();					
 						net_amt=net_amt.replace(/[^\d\.\-\ ]/g,'');	
 						if(net_amt<1){
 							jAlert('No sale transaction can not be found.', 'WARNING!',function(){
 								 $('#pdt_lineapp').val('').focus();
			    					return false;
			    				});		    							
 							return false;
 						}
							//*WR14012015 =======================
							var start_baht=$('#csh_start_baht').val();
							var end_baht=$('#csh_end_baht').val();
							var buy_type=$("#csh_buy_type").val();//Gross,Net
							var buy_status=$("#csh_buy_status").val();//G >=,L<
							if(buy_type=='N' ){
								var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
						 		net_amt=parseFloat(net_amt);
								var start_baht=start_baht.replace(/[^\d\.\-\ ]/g,'');
								 start_baht=parseFloat(start_baht);
								 //alert(net_amt +" < " +start_baht);
								 if(net_amt<start_baht){
									 jAlert("Minimum net amount required. " + start_baht + " Baht", 'WARNING!',function(){																						
										 $('#pdt_lineapp').val('').focus();
											return false;
									});
									 return false;																		    										 
								 }
								
							}
						
						//*WR14012015 ===========================
						}
						,close:function(evt){
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
								evt.stopPropagation();
	    						evt.preventDefault();					
	    						var net_amt=$('#csh_net').val();					
	    						net_amt=net_amt.replace(/[^\d\.\-\ ]/g,'');	
	    						if(net_amt<1){
	    							jAlert('No sale transaction can not be found.', 'WARNING!',function(){
	    								initFormCashier();
		    							initFieldOpenBill();
		    							$("#csh_member_no").focus();
				    					return false;
				    				});		    							
	    							return false;
	    						}
							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
								evt.stopPropagation();
	    						evt.preventDefault();  						
							}
							
							var chk_is_member=$('#csh_member_no').val();
							if(promo_tp=='LINE' && chk_is_member.length>10 && promo_code!='OX02031215'){
								selCoPromotion(promo_code,'','');
							}else{
								setTimeout(function(){
									paymentBill('00');
								},400);
							}
							$('#dlgPdtLineApp').remove();	
			           }
				});
			}else{
				jAlert(line_msg_error, 'WARNING!',function(){
					initFormCashier();
					initFieldOpenBill();
					$("#csh_member_no").focus();
					return false;
				});
			}
			return false;
	 }//func lineApp	
	 
	 function callBackIdCard(application_id,idcard,coupon_code){
		 /**
		  * @desc for support application_id OPID300,OPPLI300,
		  * @create 21052015
		  * @return 
		  */
		  //alert("You call func. callBackIdCard!");
		  $("#csh_id_card").val(idcard);
		  if(application_id=='OPPLI300' || application_id=='OPLID300' || application_id=='OPTRUE300'){
			 $('#status_couponpromo').val('Y');
			 $("#csh_coupon_code").val(coupon_code);			
		  }
		  
		  //*WR22032016
		  if(application_id=='OPPF300' || application_id=='OPPD300' || 
			   application_id=='OPPA300' || application_id=='OPPB300' || application_id=='OPPC300'){					  
			  setCardDummy2Temp(application_id,'');					  
		  }else{	
			  
			  $.ajax({
				  type:'post',
				  url:'/sales/member/getcardquota',
				  data:{
					  rnd:Math.random()
				  },success:function(data){
					  var objcq=$.parseJSON(data);
					  var sp_day=objcq[0].ops;
					  var cq_detail= " Special Day : " + objcq[0].ops;
					  $('#csh_ops_day').val(sp_day);
					  
					  $('#info_apply_promo').empty().html(application_id);
					  $('#info_apply_promo_detail').empty().html(cq_detail);
					  getCatProduct(application_id,0,0,0,0,0);
					 
				  }
			  });//ajax
			  
		  }//if
		  
		  return false;
	 }//func
	 	 
	 function callBackToDo(){
			/***
			 * for support callback function
			 * create : 26092014
			 * modify : 27012015 for support mobile coupon and line
			 */				 
		 
		    var member_no=$('#csh_member_no').val();
			var status_no=$('#csh_status_no').val();
			var promo_code=$('#csh_promo_code').val();			
			var promo_tp=$('#csh_promo_tp').val();			
			if(status_no=='04'){
				 setTimeout('initFieldPromt()',400);
				//redeemPoint();
			}else 	if(promo_tp=='NEWBIRTH'){			
				$('#csh_product_id').focus();				
			}		
			return false;
	 }//func
	 
	 function callBackToDo2(promo_code,chk_status,line_msg_error,idcard,coupon_code,s_data){
			/***
			 * for support callback function
			 * create : 26092014
			 * param chk_status : check cond is ok.
			 * param s_data country_code#email
			 * modify : 22032018
			 */		
		 		    
			if(chk_status=='Y'){				
				var s_country_code='';			
				if(s_data!='' && s_data!='#'){					
					var arr_scoupon=s_data.split('#');
					s_country_code=arr_scoupon[0];		
					var tour_email=arr_scoupon[1];	
					var tour_barcode=coupon_code;
					coupon_code=tour_barcode + "#" + idcard + "#" + tour_email;					
					var tour_passport_no='PP:'+ idcard + ':' + s_country_code;
			 		$('#vt_passport_no_val').val(tour_passport_no);	
				}								
				
				$('#status_couponpromo').val('Y');
				$('#csh_coupon_code').val(coupon_code);
				$('#csh_id_card').val(idcard);	
				$('#csh_promo_code').val(promo_code);
				if(coupon_code==''){
					$('#csh_coupon_code').val(promo_code);
				}
				playMstPromotion(promo_code,'1');
				return false;
				
			}
			
	 }//func
	 
	 function setPdt2Temp(promo_code){
			/***
			 * @desc : ???????????? 1 ????????? 1
			 * @create 20102014
			 * @modify 20102014
			 */
			$('<div id="dlgPdtMaster"><span style="color:#336666;font-size:20px;">?????????????????????????????? : </span><input type="text" size="15" id="pdt_master_id"/></div>').dialog({
			           autoOpen:true,
			           width:'40%',
					   height:'200',	
					   modal:true,
					   resizable:false,
					   closeOnEscape:false,		
			           title: "Scan the main item at the promotion.",
			           position:"center",
			           open:function(){
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');		
							//button style	
			  			    $(this).dialog("widget").find(".ui-btndlgpos")
			                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
			  			  $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
						    	$("#pdt_master_id").focus();
							});
			  			  
			  			$('#pdt_master_id').focus();
			  			    
				  			  $('#pdt_master_id').keypress(function(evt){																    	
							    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
				   			        if(key == 13){	
						    			evt.preventDefault();
						    			evt.stopPropagation();
						    			$('#btnMasterProduct').trigger('click');
						    			return false;
				   			        }
							    });
			           },
						buttons: [ 
					    	            { 
					    	                text: "Save",
					    	                id:"btnMasterProduct",
					    	                class: 'ui-btndlgpos', 
					    	                click: function(evt){ 
					    	                	evt.preventDefault();
										 		evt.stopPropagation();
										 		var product_id=$('#pdt_master_id').val();
										 		product_id=$.trim(product_id);																		 			
									    		if(product_id.length<1){
								    				jAlert('Scan the main item at the promotion.', 'WARNING!',function(){
								    					$('#pdt_master_id').val('').focus();
								    					return false;
								    				});	
								    				return false;
									    		}
									    		//alert(m_promo_code + " ==> " + promo_id);
									    		var member_no=$('#csh_member_no').val();																													    	
									    		var status_no=$('#csh_status_no').val();
									    		var member_percent=$('#csh_percent_discount').html();//*WR17122014
					  							 $.ajax({
					  								 type:'post',
					  								 url:'/sales/member/setpd2temp',				  								 
					  								 data:{
					  									 member_no:member_no,																									  									
					  									 promo_code:promo_code,
					  									 product_id:product_id,
					  									 quantity:'1',
					  									 status_no:status_no,
					  									 seq_pro:'1',
					  									member_percent:member_percent,
					  									 rnd:Math.random()
					  								 },success:function(data){											  									
					  									var arr_data=data.split('#');
					  									if(arr_data[0]=='1'){	
					  										$('#dlgPdtMaster').dialog('close');	
					  										getCshTemp('P');
					  										////////////////////// show dialog product detail ???????????????????????????????????? /////////////////////
					  										var dlg_titledetail_show=$('#other_promotion_title').html();
					  										if($.trim(dlg_titledetail_show)==''){
					  											dlg_titledetail_show="?????????????????????????????????????????????????????????????????????????????????????????????????????????";
					  										}
					  										$('<div id="dlgPdtDetail"><span style="color:#336666;font-size:20px;">?????????????????????????????? : </span><input type="text" size="15" id="pdt_detail_id"/></div>').dialog({
							  								           autoOpen:true,
							  								           width:'50%',
							  										   height:'200',	
							  										   modal:true,
							  										   resizable:false,
							  										   closeOnEscape:false,		
							  										   title:dlg_titledetail_show,
							  								           position:"center",
							  								           open:function(){						  								        	
							  												$(this).dialog('widget')
							  										            .find('.ui-dialog-titlebar')
							  										            .removeClass('ui-corner-all')
							  										            .addClass('ui-corner-top');	
							  												$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
								  											 //button style	
								  								  			 $(this).dialog("widget").find(".ui-btndlgpos")
								  								                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
								  								  			    
								  								  			 $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
								  											    	$("#pdt_detail_id").focus();
								  												});
							  								  			  
							  								  			  	  $('#pdt_detail_id').focus();
							  								  			    
							  									  			  $('#pdt_detail_id').keypress(function(evt){																    	
							  												    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							  									   			        if(key == 13){	
							  											    			evt.preventDefault();
							  											    			evt.stopPropagation();
							  											    			$('#btnDetailProduct').trigger('click');
							  											    			return false;
							  									   			        }
							  												    });
							  								           },
							  											buttons: [ 
							  										    	            { 
							  										    	                text: "????????????",
							  										    	                id:"btnDetailProduct",
							  										    	                class: 'ui-btndlgpos', 
							  										    	                click: function(evt){ 
							  										    	                	evt.preventDefault();
							  															 		evt.stopPropagation();
							  															 		var product_id=$('#pdt_detail_id').val();
							  															 		product_id=$.trim(product_id);																		 			
							  														    		if(product_id.length<1){
							  													    				jAlert('Scan the free product', 'WARNING!',function(){
							  													    					$('#pdt_detail_id').val('').focus();
							  													    					return false;
							  													    				});	
							  													    				return false;
							  														    		}
							  														    		//alert(m_promo_code + " ==> " + promo_id);
							  														    		var member_no=$('#csh_member_no').val();																													    	
							  														    		var status_no=$('#csh_status_no').val();
							  										  							 $.ajax({
							  										  								 type:'post',
							  										  								 url:'/sales/member/setpd2temp',
							  										  								 data:{
							  										  									 member_no:member_no,																									  									
							  										  									 promo_code:promo_code,
							  										  									 product_id:product_id,
							  										  									 quantity:'1',
							  										  									 status_no:status_no,
							  										  									seq_pro:'2',
							  										  									 rnd:Math.random()
							  										  								 },success:function(data){											  									
							  										  									var arr_data=data.split('#');
							  										  									if(arr_data[0]=='1'){
							  										  										$('#dlgPdtDetail').dialog('close');						  										  										
							  										  										//paymentBill('00');
							  										  										getCshTemp('P');	
							  										  										setPdt2Temp(promo_code);
							  										  										
							  														    				}else{													    					
							  														    					jAlert(arr_data[1],'WARNING!',function(){
							  														    						$('#pdt_detail_id').val('').focus();
							  																					return false;
							  																				});
							  														    				}//end if									  									
							  										  									
							  										  								 }
							  										  							 });//ajax													 		
							  														 		
							  									    	                }
							  									    	            }
							  									    	  ]	
							  								           
							  								           
							  								         ,beforeClose:function(){
							  											//*WR28012015 =======================			    		  
							  				  							var start_baht=$('#csh_start_baht').val();
							  											var end_baht=$('#csh_end_baht').val();
							  											var buy_type=$("#csh_buy_type").val();//Gross,Net
							  											var buy_status=$("#csh_buy_status").val();//G >=,L<
							  											if(promo_code=='OK01160115'){
							  												//if(buy_type=='N' ){
							  													var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
							  											 		net_amt=parseFloat(net_amt);
							  													var start_baht=start_baht.replace(/[^\d\.\-\ ]/g,'');
							  													 start_baht=parseFloat(start_baht);
							  													 var end_baht=end_baht.replace(/[^\d\.\-\ ]/g,'');
							  													 end_baht=parseFloat(end_baht);
							  													 //alert(net_amt +" > " +end_baht);
							  													 if(net_amt>end_baht){
							  														 jAlert("Net balance must not exceed " + end_baht + " Baht ", 'WARNING!',function(){																						
							  															$('#pdt_detail_id').val('').focus();
							  															return false;
							  														});
							  														 return false;																		    										 
							  													 }
							  												//}
							  											}//end if
							  											//*WR28012015 ===========================
							  										}
							  								           
							  								           
							  											,close:function(evt){
							  												if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							  													evt.stopPropagation();
							  													evt.preventDefault();	
							  													setTimeout(function(){
							  														paymentBill('00');
							  													},400);
								  												
							  												}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							  													evt.stopPropagation();
							  													evt.preventDefault();  						
							  												}
//							  												setTimeout(function(){
//							  													paymentBill('00');
//							  												},400);
							  												$('#dlgPdtDetail').remove();
							  								           }
							  								});
					  										////////////////////// show dialog product detail ???????????????????????????????????? /////////////////////
					  										
									    				}else{													    					
									    					jAlert(arr_data[1],'WARNING!',function(){
									    						$('#pdt_master_id').val('').focus();
																return false;
															});
									    					
									    				}//end if									  									
					  									
					  								 }
					  							 });//ajax													 		
									 		
				    	                }
				    	            }
				    	  ]
						,close:function(evt){
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
								evt.stopPropagation();
								evt.preventDefault();	
								//*WR13102015
								if(onSales()==0){
									jAlert('No sale transaction can not be found.', 'WARNING!',function(){
										$('#dlgPdtMaster').remove();
										initFormCashier();
										initFieldOpenBill();
										$("#csh_member_no").focus();
				    					return false;
				    				});		 
									return false;
								}else{
									setTimeout(function(){
										paymentBill('00');
									},400);
								}
							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
								evt.stopPropagation();
								evt.preventDefault();  						
							}						
							$('#dlgPdtMaster').remove();
			           }
			});
	}//func
	
	function changeCardTuesDay(member_id_old){
		/***
		 * @desc 21032014
		 * @return
		 */
		var dlgSetBag = $('<div id="dlgChangeCardTuesDay">\
	            <p>Set Bag</p>\
	        </div>');
	            dlgSetBag.dialog({
		           autoOpen:true,
				   //width:'25%',
				   height:'auto',	
				   modal:true,
				   resizable:false,
		           title: "???????????????????????????????????????????????????????????????(??????????????????) ",		
		           position:"center",
		            open:function(){		
		            	$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	    			    $('#dlgChangeCardTuesDay').empty();
		            	$('#dlgChangeCardTuesDay').html('<input type="text" size="25" id="new_card_tues" class="keybarcode input-text-pos">');	
		            	//*** check lock unlock ***
						if($("#csh_lock_status").val()=='Y'){
							lockManualKey();
						}else{
							unlockManualKey();
						}
						//*** check lock unlock ***
		            	$('#new_card_tues').focus();
		            	$('#new_card_tues').keypress(function(evt){
		            		var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					        if(key == 13){
					            evt.preventDefault();
					            var member_id_new=$('#new_card_tues').val();
					            var member_no_ref=$('#info_refer_member_id').html();
					            var ops_day=$("#info_apply_promo_detail").html();
					            ops_day=$.trim(ops_day);
					            $.ajax({
					            	type:'post',
					            	url:'/sales/member/changenewcard',
					            	data:{
					            		member_no:member_id_new,
					            		member_no_ref:member_no_ref,
					            		ops_day:ops_day,
					            		rnd:Math.random()
					            	},success:function(data){
					            		if(data!='Y'){
					            			jAlert(data, 'WARNING!',function(){
					            				$('#new_card_tues').focus();
	           									return false;
	           								});
					            		}else{					            		
					            			$('#csh_member_no').val(member_id_new);
					            			$('#dlgChangeCardTuesDay').dialog('close');
					            			getCshTemp('N');
					            			selCoPromotion('OPS2OPT','1','1');
					            			//paymentBill('10');
					            		}
					            	}
					            });
					        }
					        
		            	});
		            	
		            },
		            close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();
    						initFormCashier();
							initFieldOpenBill();
							$("#csh_member_no").focus();
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
    						evt.preventDefault();
    						initFormCashier();
							initFieldOpenBill();
							$("#csh_member_no").focus();
						}	
		            	$(this).remove();
		            }
	        });	
	}//func
	function chkOldCardToChange(){
		/***
		 * @desc 21032014
		 * @return
		 */
		var dlgSetBag = $('<div id="dlgChkOldCardToChange">\
	            <p>Set Bag</p>\</div>');
	            dlgSetBag.dialog({
		           autoOpen:true,
				   //width:'25%',
				   height:'auto',	
				   modal:true,
				   resizable:false,
		           title: "???????????????????????????????????????????????????????????????(????????????????????????) ",	
		           position:"center",
		            open:function(){		
		            	$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
		            	$('#dlgChkOldCardToChange').empty();	
		            	$('#dlgChkOldCardToChange').html('<input type="text" size="25" id="old_card_thu" class="keybarcode input-text-pos">');
		            	//*** check lock unlock ***
						if($("#csh_lock_status").val()=='Y'){
							lockManualKey();
						}else{
							unlockManualKey();
						}
						//*** check lock unlock ***
		            	$('#old_card_thu').val('').focus();
		            	$('#old_card_thu').keypress(function(evt){
		            		var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					        if(key == 13){
					            evt.preventDefault();
					           	var member_id_old=$('#old_card_thu').val();
					           	var status_no=$('#csh_status_no').val();					           	
					            //*WR 12052015
					           	member_id_old=$.trim(member_id_old);
					           	var chk_member_th_old=member_id_old.substring(0,4);
					           	if(chk_member_th_old!='1120'){
					           		jAlert("????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????", "WARNING!",function(){       									
       									$('#old_card_thu').focus();
       									return false;
       								});
					           		return false;
					           	}
					           	//------------------------- start response -----------------------------
					           	var objReqChkCardToChange=$.getJSON(
					           						"/sales/cashier/ajax",
					           						{
					           							status_no:status_no,
					           							member_no:member_id_old,
					           							actions:'memberinfo'
					           						},
					           						function(data){							
					           							objReqChkCardToChange=null;
					           							if(data=='2'){
					           								jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
					           									initFieldOpenBill();
					           									return false;
					           								});
					           							}else{
					           								$.each(data.member, function(i,m){
					           									if(m.exist=='yes'){					           										
					           										if(m.age_card!='4'){
					           											jAlert('?????????????????????????????????????????? 4 ??????????????????????????????', 'WARNING!',function(){
								           									$('#old_card_thu').focus();
								           									return false;
								           								});
					           											return false;
					           										}					           										
					           										if(m.surname!=undefined){
					           											var member_fullname=m.name+" "+m.surname;
					           										}else{
					           											var member_fullname=m.name;
					           										}									
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
					           										var percent_discount='0';
					           										if(m.percent_discount!=undefined){
					           											percent_discount=parseInt(m.percent_discount);
					           										}					           										
					           										var mp_point_sum=m.mp_point_sum;
					           										var buy_net=m.buy_net;
					           										var address=$.trim(m.address)+" "+
					           										$.trim(m.sub_district)+" "+
					           										$.trim(m.district)+" "+
					           										$.trim(m.province_name)+" "+
					           										$.trim(m.zip)+" <br>"+
					           										$.trim(m.mobile_no);					           										
					           										$('#csh_member_fullname').html(member_fullname);
					           										$('#csh_birthday').html(birthday);
					           										$('#csh_apply_date').html(apply_date);
					           										$('#csh_expire_date').html(expire_date);
					           										$('#csh_member_type').html(remark);
					           										//$('#csh_percent_discount').html(percent_discount);
					           										//$('#csh_point_total').html(mp_point_sum);
					           										//$('#csh_buy_net').html(buy_net);
					           										$('#csh_address').html(address);					           										
					           										//------------zone member info -----									
					           										$("#info_refer_member_id").html(member_id_old);
					           										$("#info_member_fullname").html(member_fullname);
					           										$("#info_member_applydate").html(apply_date);
					           										$("#info_member_expiredate").html(expire_date);
					           										$("#info_member_birthday").html(birthday);
					           										$("#info_member_opsday").html(m.special_day);
					           										$("#info_apply_promo").html(m.apply_promo);
					           										//$("#info_apply_promo_detail").html(m.apply_promo_detail);
					           										$("#info_apply_promo_detail").empty().html(m.cust_day);
					           										if(m.status=='1'){
					           											$("#info_apply_promo_detail").html(m.card_detail);
					           										}					           										
					           										$("#info_member_address").html(address);
					           										//*WR13032013
					           										if(m.mp_point_sum_1!=''){
					           											var str_mp_tmp=m.mp_point_sum_1;
					           										}else{
					           											var str_mp_tmp=0;
					           										}					           										
					           										//------------zone member info -----								           									
					           									}else if(m.exist=='no'){
					           										//call member com_special_day
					           										var remark=m.remark;
					           										var percent_discount=parseInt(m.percent_discount);
					           										$('#csh_member_type').html(remark);
					           										$('#csh_percent_discount').html(percent_discount);
					           									}
					           									$("#csh_accordion").accordion({ active:0});		
					           									
					           								//------------------ zone alert --------------------
					           									
						           								if(m.status=='1'){
					           										if(m.mem_status=='N'){
					           											jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active ?????????????????????????????????????????????????????? \n Please check and try again.', 'WARNING!',function(){
					           												initFieldOpenBill();
					           												return false;
					           											});	
					           										}else if(m.mem_status=='F'){
					           											jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ???????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
					           												initFieldOpenBill();
					           												return false;
					           											});	
					           										}else if(m.mem_status=='T'){
					           											jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
					           												initFieldOpenBill();
					           												return false;
					           											});	
					           										}
					           										return false;
					           									}
						           								
						           								if(m.forgot_card=='Y'){
					           										jAlert('????????????????????? ?????????????????????????????? ??????????????????????????????<br>????????????????????????????????????????????????????????????(SMS) ???????????????????????????<br>?????????????????????????????? Admin', 'WARNING!',function(){
					           											initFieldOpenBill();
					           											return false;
					           										});	
					           										return false;	
					           									}
						           								
					           									if(m.expire_status=='Y'){
					           										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
					           											initFieldOpenBill();
					           											return false;
					           										});	
					           										return false;				
					           									}
					           									
				           										changeCardTuesDay(member_id_old);
				    					           				$('#dlgChkOldCardToChange').dialog('close');
						           								//------------------ zone alert --------------------
					           									
					           								});//end each
					           								
					           							}
					           						}				
					           				);//end json
					           				//------------------------- end respoonse ----------------------------

					            return false;
					        }
		            		
		            	});
		            },
		            close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();
    						initFormCashier();
							initFieldOpenBill();
							$("#csh_member_no").focus();
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){			
							initFormCashier();
							initFieldOpenBill();
							$("#csh_member_no").focus();
						}
		            	$(this).remove();
		            }
	        });	
	}//func
	function callMemberOffline(){
		/**
		 * @desc
		 */
		var status_no=$("#csh_status_no").val();
		var member_no=$("#csh_member_no").val();	
		if(jQuery.trim(member_no)==''){	
			jAlert('Please specify member id.','WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
		}else{	
			$.getJSON(
					"/sales/cashier/callmemberoffline",
					{
						status_no:status_no,
						member_no:member_no,
						actions:'offline'
					},
					function(data){							
						if(data=='2'){
							jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});
						}else{
							$.each(data.member, function(i,m){
								if(m.exist=='yes'){
									if(m.surname!=undefined){
										var member_fullname=m.name+" "+m.surname;
									}else{
										var member_fullname=m.name;
									}
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
									var percent_discount='0';
									if(m.percent_discount!=undefined){
										percent_discount=parseInt(m.percent_discount);
									}
									var mp_point_sum=m.mp_point_sum;
									var buy_net=m.buy_net;
									var address=$.trim(m.h_address)+" "+
													$.trim(m.h_village_id)+" "+
													$.trim(m.h_village)+" "+
													$.trim(m.h_soi)+" "+
													$.trim(m.h_road)+" "+
													$.trim(m.h_district)+" "+
													$.trim(m.h_amphur)+" "+
													$.trim(m.h_province)+" "+
													$.trim(m.h_postcode);
									$('#csh_member_fullname').html(member_fullname);
									$('#csh_birthday').html(birthday);
									$('#csh_apply_date').html(apply_date);
									$('#csh_expire_date').html(expire_date);
									$('#csh_member_type').html(remark);
									if($("#csh_status_no").val()!='02'){
										$('#csh_percent_discount').html(percent_discount);
									}
									
									$('#csh_point_total').html(mp_point_sum);//on local only
									$('#csh_buy_net').html(buy_net);
									$('#csh_address').html(address);
									//------------zone member info -----									
									$("#info_refer_member_id").html(refer_member_id);
									$("#info_member_fullname").html(member_fullname);
									$("#info_member_applydate").html(apply_date);
									$("#info_member_expiredate").html(expire_date);
									$("#info_member_birthday").html(birthday);
									$("#info_member_opsday").html(m.special_day);
									$("#info_apply_promo").html(m.apply_promo);
									$("#info_apply_promo_detail").html(m.apply_promo_detail);
									if(m.status=='1'){
										$("#info_apply_promo_detail").html(m.card_detail);
									}
									$("#info_member_address").html(address);
									//------------zone member info -----			
									
//									if(m.expire_status=='Y'){
//										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
//											initFieldOpenBill();
//											return false;
//										});	
//										return false;									
//									}
									
									$("#csh_customer_id").val(m.customer_id);
//									if($("#csh_status_no").val()=='02'){
//										//case bill first buy 02
//										var str_percent_discount=$("#csh_first_percent").val()+"+"+$("#csh_add_first_percent").val();
//										$('#csh_percent_discount').html(str_percent_discount);
//									}
									//?????????????????????????????????????????????
									$('#csh_get_point').val('Y');					
									// for VIP member
									$('#csh_member_vip').val(m.vip);
									$('#csh_vip_limited').val(m.limited);
									$('#csh_vip_limited_type').val(m.limited_type);
									$('#csh_vip_sum_amt').val(m.sum_amt);//???????????????????????????????????????????????? balance
									if(m.vip=='1'){
										$("#csh_play_main_promotion").val('N');
										$("#csh_play_last_promotion").val('N');
										$('#csh_get_point').val('N');
										$('#csh_percent_discount').html(m.member_percent);
										//check today balance
										var str_type="";
										(m.limited_type=='N')?str_type="???????????????????????????":str_type="?????????????????????";									
										//??????????????????????????????????????????
										var vip_balance=parseFloat(m.sum_amt)-parseFloat(m.diary1_sum_amt);										
										//????????????????????????????????????
										var vip_used=parseFloat(m.limited)-vip_balance;
										//???????????????????????????????????????????????? balance
										$('#csh_vip_sum_amt').val(vip_balance);										
										var str_vip_info="?????????????????? "+addCommas(m.limited)+" ?????????  "+str_type+"<br>??????????????????????????????????????????????????? "+addCommas(vip_used)+" ?????????<br>????????????????????? "+addCommas(vip_balance)+" ?????????";
										$("#info_apply_promo_detail").html(str_vip_info);
										$("#csh_member_type").html("VIP Card");										
									}		
									//-------------17082016 add card type for platinum card lucky draw promotion---------------
									var mem_card_info=m.card_level + "#" + m.ops;
									$('#id_smspromo').val(mem_card_info);
								}else if(m.exist=='no'){
									//call member com_special_day
									var remark=m.remark;
									var percent_discount=parseInt(m.percent_discount);
									$('#csh_member_type').html(remark);
									if($("#csh_status_no").val()!='02'){
										$('#csh_percent_discount').html(percent_discount);
									}
								}								
								$("#csh_accordion").accordion({ active:0});								
								if(m.status=='1'){
									if(m.mem_status=='N'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active ?????????????????????????????????????????????????????? \n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}else if(m.mem_status=='F'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ???????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}else if(m.mem_status=='T'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}
									//return false;
								}								
								if( m.status=='1' || m.expire_status=='Y'){
									jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
										initFormCashier();
										initTblTemp();
										initFieldOpenBill();
										return false;
									});	
									return false;									
								}								
								if(status_no=='04'){
									redeemPointOffline();
								}else if(status_no=='06'){									
									 chkCnMember();
								}else{
									initFieldPromt();
								}								
													
							});
							
						}
					}				
			);//end json
			
		}		
		return false;
	}//func
	
	function unMarkMemberUsePriv(promo_code,promo_year,member_no,customer_id){
		/**
		 * @desc
		 */
	
		var opts_unmark={
				type:'post',
				url:'/sales/cashier/unmarkmemberuse',
				data:{
					promo_code:promo_code,
					promo_year:promo_year,
					member_no:member_no,
					customer_id:customer_id,
					rnd:Math.random()
				},
				success:function(){}
		};
		$.ajax(opts_unmark);
	}//func
	
	function chkCnMember(){
		/**
		 * @desc
		 */
		var member_no=$("#csh_member_no").val();
		var cn_member_no=$("#cn_member_no").val();
		var cn_doc_no=$("#cn_doc_no").val();
		var cn_amount=$("#cn_amount").val();
		member_no=$.trim(member_no);
		cn_member_no=$.trim(cn_member_no);
		cn_doc_no=$.trim(cn_doc_no);
		cn_amount=parseFloat(cn_amount);
		cn_amount=addCommas(cn_amount.toFixed(2));
		//alert(member_no+"!="+cn_member_no);
		if(member_no!=cn_member_no){
			jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? (CN) <br>Please check and try again.','WARNING!',function(b){
					 return false;
	   		 });
		}else{
			$('#other_promotion_title').append("  ????????????????????? "+cn_doc_no+"  ????????????????????? "+cn_amount+" ?????????");
			initFieldPromt();
		}
		
	}//func

	function reCalEcouponDiscount(){
		/**
		*@desc
		*/
		$.ajax({
			type:'post',
			url:'/sales/member/recalecoupondiscount',
			data:{
				rnd:Math.random()
			},
			success:function(data){
				getCshTemp('N');
			}
		});
	}//func
	
	function getDiscountNetAmt(){
		/**
		*@var
		*@desc 
		*/		
		var bal_discount=$("#csh_bal_discount").val();
		var point=$('#csh_promo_point').val();
		var point_to_discount=$('#csh_promo_point_to_discount').val();
		var promo_amt=$('#csh_promo_amt').val();
		
		//alert("bal_discount=>"+bal_discount+"point=>"+point+",point_to_discount=>"+point_to_discount+",promo_amt=>"+promo_amt);
		
		//check promo discount
		var promo_code=$('#csh_promo_code').val();
		if($.trim(promo_code).length==0)
			return false;
		var opts_checkpro={
				type:'post',
				url:'/sales/cashier/chkmorepointmoreval',
				data:{
					promo_code:promo_code,
					rnd:Math.random()
				},
				success:function(data){
					if(data!=''){
						var net_amt=data;					
						net_amt=net_amt.replace(/[^\d\.\-\ ]/g,'');	
					}else{
						var net_amt=$('#csh_net').val();					
						net_amt=net_amt.replace(/[^\d\.\-\ ]/g,'');	
					}
				//////////////////////////// show
					var w=point*point_to_discount;
					 w=parseInt(w);
					var d=Math.floor(net_amt/promo_amt);
						 d=parseInt(d);
					var discount=d*w;	
					if(bal_discount!=''){
						discount=parseInt(discount) + parseInt(bal_discount);//*WR17102012
					}
					$("#other_promotion_cmd").html("?????????????????? = "+discount+" ??????????????????????????????????????????");
					$("#csh_promo_discount").val(discount);
					$("#other_promotion_cmd").blinky({ count: 3 });
					
				////////////////////////////////////
					
					
				}
		};
		
		$.ajax(opts_checkpro);
		return false;
	}//func

	function empPrivilage(){
		/**
		*@var
		*/
//		initFieldOpenBill();
//		return false;
		$.ajax({
			type:'post',
			url:'/sales/member/chkedate',
			cache:false,
			data:{
				rmd:Math.random()
			},
			success:function(data){
				var obj_json=$.parseJSON(data);
				if(obj_json.status_ck=='Y'){
					initFieldOpenBill();
				}else{
					if(obj_json.month_enable=='Y' && obj_json.outofday=='Y' && obj_json.outtime=='Y'){
						jAlert('?????????????????????????????????????????????????????????????????????????????????????????? '+obj_json.start_day+' ??????????????????????????? '+obj_json.end_day+'\n'
								+' ???????????? '+obj_json.start_time+' ????????? '+obj_json.end_time+' ???.'
								,'WARNING!',function(b){
							initTblTemp();
					        initFormCashier();
							initFieldOpenBill();
		    				 return false;
			    		 });
					}else if(obj_json.month_enable=='Y' && obj_json.outofday=='Y'){
						jAlert('?????????????????????????????????????????????????????????????????????????????????????????? '+obj_json.start_day+' ??????????????????????????? '+obj_json.end_day+'\n','WARNING!',function(b){
							initTblTemp();
					        initFormCashier();
							initFieldOpenBill();
		    				 return false;
			    		 });
					}else if(obj_json.month_enable=='Y' && obj_json.outoftime=='Y'){
						jAlert('???????????????????????????????????????????????????????????????????????????????????? '+obj_json.start_time+' ????????? '+obj_json.end_time+' ???.\n','WARNING!',function(b){
							initTblTemp();
					        initFormCashier();
							initFieldOpenBill();
		    				 return false;
			    		 });
					}else{
						jAlert('????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(b){
							initTblTemp();
					        initFormCashier();
							initFieldOpenBill();
		    				 return false;
			    		 });
					}
				}
		
			}
		});
	}//func

	function chkEmpPrivilage(){
		/**
		*@var
		*/
		var employee_id=$.trim($('#csh_member_no').val());
		if(employee_id.length==0){
			jAlert('????????????????????????????????????????????????????????????','WARNING!',function(b){
				$("#csh_member_no").focus();
				 return false;
    		 });		
	   		 return false;	
		}
		$.ajax({
				type:'post',
				url:'/sales/member/chkempprivilage',
				cache:false,
				data:{
						employee_id:employee_id,
						rnd:Math.random()
				},
				success:function(data){
					if(data!=''){
						var ecp=$.parseJSON(data);
						if(ecp.error_opsday=='Y'){
							jAlert('??????????????????????????????????????????????????????????????????????????????????????? OPS Day ?????????','WARNING!',function(b){
								$("#csh_member_no").focus();
			    				 return false;
				    		 });
							return false;
						}else if(ecp.error_cam=='Y'){
							jAlert('??????????????? Scan ????????????????????????????????? ??????????????????????????????????????????????????????????????????','WARNING!',function(b){
								$("#csh_member_no").focus();
			    				 return false;
				    		 });
							return false;
						}else if(ecp.error_empty=='Y'){
							jAlert('???????????????????????????????????????????????????????????????????????????????????? ????????????????????????????????????????????????????????????????????????????????? HR','WARNING!',function(b){
								$("#csh_member_no").focus();
			    				 return false;
				    		 });
							return false;
						}else if(ecp.is_used=='Y'){							
							jAlert('????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(b){
								$("#csh_member_no").focus();
			    				 return false;
				    		 });
							return false;
						}else{
							var amount_op=parseFloat(ecp.amount_op);
								 amount_op.toFixed(2);
							var amount_used=parseFloat(ecp.amount_used);
								 amount_used.toFixed(2);
							var amount_balance=parseFloat(ecp.amount_balance);
								 amount_balance.toFixed(2);							 
							 $('#info_member_fullname').html(ecp.name+" "+ecp.surname);
							 $('#other_promotion_title').html(" "+ecp.name+" "+ecp.surname+
										"  ?????????????????? "+addCommas(amount_op)+
										"  ??????????????? "+addCommas(amount_used)+
										" ????????? ????????????????????? "+addCommas(amount_balance)+" ?????????");
								$('#csh_ecp_percent_discount').val(ecp.percent_discount);				
								$('#csh_ecp_amount_balance').val(ecp.amount_balance);								
								$('#csh_percent_discount').html('0+'+ecp.percent_discount);
								$('#csh_play_main_promotion').val('N');
			    				$('#csh_play_last_promotion').val('N');
			    				$('#csh_get_promotion').val('N');
			    				$('#csh_start_baht').val('');
			    				$('#csh_end_baht').val('');
			    				$('#csh_discount_member').val('N');
			    				$('#csh_get_point').val('N');	
			    				 //init sel type promotion
								$("#status_pro option[value='1']").attr('selected', 'selected');
								$("#status_pro").focus();		
								$("#status_pro").trigger('change');
								//*WR 10022015
								var msg_emp_tp="";
								if(ecp.emp_tp!=='O'){
									msg_emp_tp="----------------------------------------------------------------------\n<span style='color:#FF0000'>???????????????????????????????????????????????????????????????????????????...???????????????????????????????????????????????????????????????????????????????????????</span>";
								}
								jAlert('?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? \n????????????????????????????????????????????????????????????????????????????????????????????????<br>' +
										msg_emp_tp,'WARNING!',function(b){
									initFieldPromt();
				    				 return false;
					    		 });	
						}
					}else{
						jAlert('???????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(b){
							$("#csh_member_no").focus();
		    				 return false;
			    		 });
					}
				}
			});
	}//func

	function selCN(){
		/**
		*@var
		*/
		var dialogOpts_dlgSelCN = {
				autoOpen:false,
				width:550,		
				height:'auto',	
				modal:true,
				resizable:true,
				position:"center",
				showOpt: {direction: 'up'},		
				closeOnEscape: true,	
				title:"<span class='ui-icon ui-icon-cart'></span>???????????????????????????",
				open: function(){  
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
				    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
					    																			"font-size":"26px","color":"#0000FF",
					    																			"padding":"0 0 0 0"});	
				    //$(this).parent().append('<div id="footer" style="position:absolute;bottom:0;float:left;width:100%;background-color:#c9c9c9;padding:5px;"></div>');	 
				    $('#dlgSelCN').html('');
				    $.ajax({
			   	    	type:'post',
			   	    	url:'/sales/cashier/listcn',
			   	    	data:{
			       			rmd:Math.random()
			       		},
			       		success:function(data){
				       		$('#dlgSelCN').html(data);
				       		$('.tableNavListCn ul li').navigate({
					       		wrap:true
					       	}).click(function(e){
					       		e.preventDefault();
					       		//e.stopImmediatePropagation();
					       		//e.stopPropagation();
					       		var idcn=$(this).attr('idcn');		

					       		//alert(idcn);
					       		
					       		if(idcn!='nodata'){					       			
						       		var selcn=$.parseJSON(idcn);					
						       		$("#csh_member_no").focus();
						       		$("#cn_member_no").val(selcn.member_id);
						       		$("#cn_doc_no").val(selcn.doc_no);
						       		$("#cn_amount").val(selcn.net_amt);//*WR 06082012 update from selcn.amount to selcn.net_amt								       	
						       	}else{
						       		$('.tableNavListCn ul li').navigate('destroy');
						       		browsDocStatus()
						       	}
					       		
				       			$('#dlgSelCN').dialog('close');					      
						   });
				       		
			       		}
			       	});
					
				},				
				close: function(evt,ui) {				
					$('div.tableNavListCn ul>li').navigate('destroy');	
					$('#dlgSelCN').dialog('destroy');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){					
						initFormCashier();
						browsDocStatus();
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){			
						initFormCashier();
						browsDocStatus();
					}					
					
				}
				
			 };			
			$('#dlgSelCN').dialog('destroy');
			$('#dlgSelCN').dialog(dialogOpts_dlgSelCN);			
			$('#dlgSelCN').dialog('open');
	}//func
	

	function initCshFlexigrid(url){
		/*
		 *@desc
		 *@param String url
		 *@modify 27102011
		 */		 
	}//func

	function delayTime(objFocus){
		/**
		*@desc
		*@return
		*/
		$("#"+objFocus).focus();
		//$("#csh_member_no").focus();
		return false;
	}//

	
	
	function cmdEnterKey(inputTextField){
		/**
		*@desc
		*@param Object Jquery inputTextField:
		*@return Boolean True
		*/
		var target_inputTextField=$('#' + inputTextField);		
		if(jQuery.trim(target_inputTextField.val())=='') return false;		
		var inputkey="#"+inputTextField;
		var e = jQuery.Event("keypress");
		e.keyCode = $.ui.keyCode.ENTER;
		$(inputkey).trigger(e);
		//e.stopPropagation();//add for test
		return true;//need for use other function
	}//func
	
	function addCommas(nStr){
		/**
		*@desc
		*@param String nStr :target string to add comma
		*@return String : new format add comma
		*/
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		//alert("x1+x2 => "+x1 + x2);
		return x1 + x2;		
	}//func	

	function deleteFromGrid(del_list,del_list_show){
		/**
		*@desc
		*@return
		*/

		 jConfirm('DO YOU WANT TO DELETE ITEM NO :\r\n '+del_list_show,'CONFIRM MESSAGE', function(r){
		        if(r){
					var opts={
							   type:"POST",
							   url: "/sales/cashier/deletetemp",
							   cache:false,
							   data:{
						   				items:del_list,
						   				action:'removeFormGrid'
						   			},
							   success: function(data){
								    if(data=='LOCKDEL'){
								    	 jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(b){
								    		 initFieldPromt();
						    				 return false;
							    		 });
								    }else if(data=='trn_tdiary2_sl'){
								    	
								    	//*WR09092014 fixed promo_code for promo NEWBIRTH
								    	var promo_code=$('#csh_promo_code').val();
								    	var promo_tp=$('#csh_promo_tp').val();
								    	if(promo_tp=='NEWBIRTH'){
								    	//if(promo_code=='OM02071113' || promo_code=='OM02081113' || promo_code=='OM02091113'){
								    		$.ajax({
												type:'post',
												url:'/sales/member/chkamtprobalance',
												data:{
													promo_code:promo_code,											
													product_id:'',
													quantity:'',
													rnd:Math.random()
												},success:function(data){
													getCshTemp('N');	
												}
											});
								    	}else{
								    		getCshTemp('N');
								    	}
								    	
						   				//getCshTemp('N');
									}else{
										getPmtTemp();
									}
									
							   }
							};
					
					$.ajax(opts);
		        }else{
		        	$('#btnDelRowSelect').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
			    }
			}
		);
		
	}//func
	
	var lastRequest = 0; 
	function onSales(){
		/**
		*@desc cashier is playing sale item on table temp or not
		*@for payment or switch type of bill
		*/
		var nck=0;
		var thisRequest = ++lastRequest;
		var opts={
				type:'post',
				url:'/sales/cashier/countdiarytemp',
				async:false,
				cache:false,
				data:{
					rnd:Math.random()
				},
				success:function(data){
					if (thisRequest !== lastRequest) return;					
					var arr_data=data.split('#');
					if(parseInt(arr_data[0])>0){					
						nck=parseInt(arr_data[0]);
					}			
					
				}
			};
		$.ajax(opts);
		return nck;
	}//func
	
	function cancelPro(){
		/**
		*@desc
		*@return void
		*/
		var opts={
				type:"post",
				url:"/sales/cashier/ajax",
				cache: false,
				data:{
					actions:"cancelPro",
					now:Math.random()
				},
				success:function(data){					
					//anything
					getCshTemp('N');
				}
			};
		$.ajax(opts);
	}//func

	function promotionDetail(flgstop){
		/**
		 *@desc
		 *@param Char flgstop 'Y'=stop,'N'=play
		 *@modify 01072015
		 *@return Boolean false
		 */
		//lastprochk();
		getPmtTemp();
		if(flgstop=='Y'){
			//*WR01072015 
			if($('#status_couponpromo').val()=='Y'){
				//??????????????????????????????????????????????????????????????????????????????????????????????????????		
				var start_baht=parseFloat($('#csh_start_baht').val());
				var end_baht=parseFloat($('#csh_end_baht').val());
				var buy_status=$("#csh_buy_status").val();	
				var csh_net=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
				     csh_net=parseFloat(csh_net);
				//alert(csh_net + " " + buy_status + " " + start_baht);
				if(csh_net > 0 && buy_status=='G' && csh_net < start_baht ){
					jAlert('????????????????????????????????????????????? ' + start_baht + ' ???????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
						$("#btn_cal_promotion").removeAttr("disabled");
						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
						return false;
					});
				}else{
					paymentBill('00');
				}
			}else{
				//////////////////////// START ///////////////////////////
				//check bill 01 for 9000745
				$.ajax({
					type:'post',
					url:'/sales/member/formregister',
					cache:false,
					data:{
						rnd:Math.random()
					},
					success:function(data){			
						
						if(data=="Y"){
							//show form
							$('<div id="dlgFormRegister">' + 
									'<span style="color:#333333;"> MEMBER CODE :  <span>' + 
									'<input type="text" size="15" id="reg_member_id"/><br/>' + 
									'<span style="color:#333333;"> MOBILE NO :  <span>' + 
									'<input type="text" size="15" id="reg_mobile_no"/><br/>' +
									'</div>').dialog({
						           autoOpen:true,
								   width:'350',
								   height:'250',	
								   modal:true,
								   resizable:false,
								   closeOnEscape:false,		
						           title: "MEMBER REGISTER",
						           position:"center",
						           open:function(){
										$(this).dialog('widget')
								            .find('.ui-dialog-titlebar')
								            .removeClass('ui-corner-all')
								            .addClass('ui-corner-top');		
										//button style	
						  			    $(this).dialog("widget").find(".ui-btndlgpos")
						                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
						  			    
						  			    setTimeout(function(){
						  			    	$("#reg_member_id").focus();
						  			    },800);
						  			    
							  			  $('#reg_member_id').keypress(function(evt){																    	
										    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							   			        if(key == 13){	
									    			evt.preventDefault();
									    			evt.stopPropagation();
									    			$("#reg_mobile_no").focus();									    			
									    			return false;
							   			        }
										  });
								  		  $('#reg_mobile_no').keypress(function(evt){																    	
										    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							   			        if(key == 13){	
									    			evt.preventDefault();
									    			evt.stopPropagation();
									    			$('#btnChkMemberExist').trigger('click');
									    			return false;
							   			        }
										  });
						           },
				    			buttons: [ 
					    					{ 
						    	                text: "OK",
						    	                id:"btnChkMemberExist",
						    	                class: 'ui-btndlgpos', 
						    	                click: function(evt){ 
						    	                	evt.preventDefault();
											 		evt.stopPropagation();
											 		var member_no=$('#reg_member_id').val();
											 		var mobile_no= $('#reg_mobile_no').val();
											 		member_no=$.trim(member_no);
											 		mobile_no=$.trim(mobile_no);
											 		//alert(member_no.length);
											 		
											 		if(member_no.length!=13){
											 			jAlert('PLEASE ENTER MEMBER CODE 13 DIGIT', 'WARNING',function(){
											 				$('#reg_member_id').focus();
									    					return false;
									    				});	
											 			return false;
											 		}else if(member_no.substr(0,6)!="511180" && member_no.substr(0,1)!="x"){
											 			jAlert('INVALID FORMAT MEMBER CODE.', 'WARNING',function(){
											 				$('#reg_member_id').focus();
									    					return false;
									    				});	
											 			return false;
											 		}else if(mobile_no.length<9 && mobile_no.length>10){
									    				jAlert('PLEASE ENTER MOBILE NO 9 OR 10 DIGIT', 'WARNING',function(){
									    					$('#reg_mobile_no').focus();
									    					return false;
									    				});	
									    				return false;
										    		}else if(validatePhone(mobile_no)===false){
										    			jAlert('INVALID FORMAT MOBILE NO.', 'WARNING',function(){
									    					$('#reg_mobile_no').focus();
									    					return false;
									    				});	
									    				return false;
										    		}else {
										    			
										    			//check api joke	
											    		$.ajax({
											    			type:'post',
											    			url:'/sales/member/checkismember',
											    			data:{
											    				member_no:member_no,
											    				mobile_no:mobile_no,
											    				rnd:Math.random()
											    			},
											    			success:function(data){
											    				//alert(data);
											    				var obj=$.parseJSON(data);		
											    				//alert(obj[0].status);
											    				if(obj[0].status=="N"){												    					
															    	$("#csh_status_no").val("01");
															    	$("#csh_application_id").val("OPID300");
															    	$('#csh_application_type').val("NEW"); 
															    	$('#csh_card_type').val("MBC");
															    	$('#csh_member_no').val(member_no);
															    	$('#csh_mobile_no').val(mobile_no);
															    	$('#dlgFormRegister').dialog('close');
															    	paymentBill('00');
															    	
											    				}else{
											    					jAlert(obj[0].msg, 'WARNING!',function(){	            	
											    		            	return false;
											    			        });
											    				}
											    				
											    			}
											    		});
										    			
										    		}//end if
										    	
										 																				 		
					    	                }
					    	            }
							        ]			
	    							,close:function(evt){
	    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
											evt.stopPropagation();
				    						evt.preventDefault();
				    						//initTblTemp();
				    						//initFormCashier();
											//initFieldOpenBill();
											initFieldPromt();
											$("#btn_cal_promotion").removeAttr("disabled");
											$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
									 	
										}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
											evt.stopPropagation();
				    						evt.preventDefault();  						
										}
	    								$('#dlgFormRegister').dialog("destroy").remove();	
						           }
							});
							//show form
							
						}else{
							/////////////////// START ORG /////////////////
							paymentBill('00');							
							/////////////////// END   ORG /////////////////
						}
										
					}
				});
				//////////////////////// START ///////////////////////////
				//paymentBill('00');	
			}//if
			//paymentBill('00');	
		}//if
		return false;
	}//func
	
	function calPaid(){
		/**
		*@desc dynamic cal payment
		*@last modify 04012018
		*@return void
		*/
		
		//*WR04012018
		$('#txt_cash_back').val('0');
		$('#txt_cash_back_2').val('0');
		
		//$ ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
		 var net_total_payment=jQuery.trim($("#net_total_payment").html().replace(/[^\d\.\-\ ]/g,''));
		 net_total_payment=parseFloat(net_total_payment);
		 //R
		 $("#net_total_payment_2").html(addCommas($('#txt_netvt_2').val()));
		 var net_total_payment_2=jQuery.trim($("#net_total_payment_2").html().replace(/[^\d\.\-\ ]/g,''));
		 net_total_payment_2=parseFloat(net_total_payment_2);		 
		 //=========================================================================		 
		 var txtVoucher=jQuery.trim($("#txt_voucher_cash").val().replace(/[^\d\.\-\ ]/g,''));		 
		 //============================ Receive ====================================
		 //$ dollars
		 var txtCredit=jQuery.trim($("#txt_credit").val().replace(/[^\d\.\-\ ]/g,''));	
		 if(txtCredit.length==0)
			 txtCredit=0.00;	
		     txtCredit=parseFloat(txtCredit);
		 
		 var txtCash=jQuery.trim($("#txt_cash").val().replace(/[^\d\.\-\ ]/g,''));
		 if(txtCash.length==0)
			 txtCash=0.00;	
		 txtCash=parseFloat(txtCash);
		 
		 if(txtCredit!=0.00){
			 txtCash=txtCredit;
		 }
		 
		 //R reals
		 var txtCash2=jQuery.trim($("#txt_cash_2").val().replace(/[^\d\.\-\ ]/g,''));
		 if(txtCash2.length==0)
			 txtCash2=0.00;	
		 if(txtCash2==0 || txtCash2==0.00 || txtCash2=='')
			 txtCash2=0.00;
		 txtCash2=parseFloat(txtCash2);
		 
		 //for support 2018 change
		 var default_curr_rate_change=jQuery.trim($("#default_curr_rate_change").val().replace(/[^\d\.\-\ ]/g,''));
		 if(default_curr_rate_change.length==0)
			 default_curr_rate_change=1.00;
		 
		 
		 //find balance dollar
		 var txt_balance=0.00;
		 var txt_balance_2=0.00;
		 var default_curr_rate=jQuery.trim($("#default_curr_rate").val().replace(/[^\d\.\-\ ]/g,''));
		 if(default_curr_rate.length==0)
			 default_curr_rate=1.00;
		 var txt_cash_back=0.00;
		 var txt_cash_back_2=0.00;		 
		 
		 txt_balance=(txtCash*default_curr_rate) + txtCash2;		 
		 if(txt_balance >= net_total_payment_2){
			 txt_balance=0.00;
		 }else{
			 //alert("txt_balance="+net_total_payment+"-"+txtCash);
			 txt_balance=net_total_payment-txtCash;
		 }
		 txt_balance=roundNumber(parseFloat(txt_balance),2);
		 $('#txt_net').val(txt_balance);
		 
		 //find balance2
		 txt_balance_2=txt_balance*default_curr_rate;
		 txt_balance_2=roundNumber(parseFloat(txt_balance_2),2);
		 $('#txt_net').val(txt_balance);
		 $('#txt_net_2').val(txt_balance_2);
		 
		 // txt_balance=if(receive($)*4000+receive(R) >= amount(R)) =0,amount($)-receive($)
		 //=IF(D9*B1+D10>=D4,0,D3-D9)
		 
		 //find cash back dollar
		 if(txtCash>=net_total_payment){
			 txt_cash_back=txtCash-net_total_payment;
			 //if(txt_cash_back<0)
			 //	txt_cash_back=0.00;
			 txt_cash_back=roundNumber(parseFloat(txt_cash_back),2);
			 $('#txt_cash_back').val(txt_cash_back);
		 }
		 //change amount($)=IF(D9>=D3,D9-D3,0)
		 
		 //find cash back real
		 
		
		 
		 if(txtCash2>=0.00){
			 
			 //alert(txtCash2+"-("+net_total_payment_2+"-("+txtCash+"*"+default_curr_rate+"))");
			 txt_cash_back_2=txtCash2-(net_total_payment_2-(txtCash*default_curr_rate));
			 
			 
			 //*WR21122017 for support change 2018
			var cmpDate_Start ='01/01/2018';        
			var cmpDate_Today=$('#csh_doc_date').html();			
	        var arr_fromdate = cmpDate_Start.split('/');
	        cmpDate_Start = new Date();
	        cmpDate_Start.setFullYear(arr_fromdate[2],arr_fromdate[1]-1,arr_fromdate[0]);
	        
	        var arr_todate = cmpDate_Today.split('/');
	        cmpDate_Today = new Date();
	        cmpDate_Today.setFullYear(arr_todate[2],arr_todate[1]-1,arr_todate[0]);
		    //alert("cmpDate_Start=>" + cmpDate_Start + "\n" + "cmpDate_Today=>" + cmpDate_Today);        
		    //alert("default_curr_rate_change :: " + default_curr_rate_change);
		    if(cmpDate_Today >= cmpDate_Start){
		    	
		    	if(txtCash>=net_total_payment){
		    		var net_total_payment_3=net_total_payment*default_curr_rate_change;
		    		txt_cash_back_2=(net_total_payment_3-(txtCash*default_curr_rate_change));
		    	}else{
		    		var net_total_payment_3=net_total_payment*default_curr_rate;
		    		txt_cash_back_2=txtCash2-(net_total_payment_3-(txtCash*default_curr_rate));
		    	}
		    	
		    	//////////var net_total_payment_3=net_total_payment*default_curr_rate_change;
		    	//var net_total_payment_3=net_total_payment*default_curr_rate;
		    	
		    	//alert(txtCash2+"-("+net_total_payment_3+"-("+txtCash+"*"+default_curr_rate_change+"))");
		    	
		    	//////////txt_cash_back_2=(net_total_payment_3-(txtCash*default_curr_rate_change));
				//txt_cash_back_2=txtCash2-(net_total_payment_3-(txtCash*default_curr_rate_change));
				//alert("txt_cash_back_2 :: " + txt_cash_back_2);
		    }
		    
		    //*WR04012018
		    /*
		    if(txtCash!='0' || txtCash>0.00){
		    	txt_cash_back_2= txt_cash_back*default_curr_rate_change;
		    }*/
		    
		    
			 
			// if(txt_cash_back_2<0)
			//	 txt_cash_back_2=0.00;
		 }else{
			 
			 
			 
			 txt_cash_back_2=0.00;
		 }
		 
		 //txt_cash_back_2=Math.abs(txt_cash_back_2);		 
		 // txt_cash_back_2=roundNumber(parseFloat(txt_cash_back_2),2);
		 //alert(txt_cash_back_2);
		 
		 
		 if($('#txt_net').val()!='0' && $('#txt_net_2').val()!='0' ){
			 $('#txt_cash_back_2').val('0');
		 }else{
			 $('#txt_cash_back_2').val(txt_cash_back_2);
		 }
		 //IF(D10>=0,D10-(D4-(D9*B1)),0)
		 
		 //$
		 	 
		 displayDigit(txt_cash_back);
	}//func

	function calPaid31052017(){
		/**
		*@desc
		*@return void
		*/
		 //var txtPoint=jQuery.trim($("#txt_redeem_point_cash").val().replace(/[^\d\.\-\ ]/g,''));	
		 var txtPoint=0.00;//??????????????????????????? txt_redeem_point_cash ?????????????????????
		 var txtVoucher=jQuery.trim($("#txt_voucher_cash").val().replace(/[^\d\.\-\ ]/g,''));
		 var txtCash=jQuery.trim($("#txt_cash").val().replace(/[^\d\.\-\ ]/g,''));
		 if(txtCash=='')
			 txtCash=0.00;		 		 
		 var txtCredit=jQuery.trim($("#txt_credit").val().replace(/[^\d\.\-\ ]/g,''));
		 var net_total_payment=jQuery.trim($("#net_total_payment").html().replace(/[^\d\.\-\ ]/g,''));	
		 var txtSumPaid=0.00;
		 var txtCashBack=0.00;
		 var txtCurrNet=0.00;
		 txtPoint=parseFloat(txtPoint);		
		 txtVoucher=parseFloat(txtVoucher);
		 txtCash=parseFloat(txtCash);
		 txtCredit=parseFloat(txtCredit);
		 net_total_payment=parseFloat(net_total_payment);
		 
		 //*WR18072014
		 if(txtVoucher!='' && txtVoucher!=undefined){
			 net_total_payment=net_total_payment+txtVoucher;
		 }	 
		 txtSumPaid=txtPoint+txtVoucher+txtCash+txtCredit;        
		
		 if(txtSumPaid>=net_total_payment){
			 txtCurrNet=txtSumPaid-net_total_payment;
			 txtCashBack=txtCurrNet.toFixed(2);
			 $("#txt_net").val('0.00');
		 }else{
			 txtCurrNet=net_total_payment-txtSumPaid;
			 txtCashBack='0.00';		
			 $("#txt_net").val(addCommas(txtCurrNet));	 
		 }
		 $("#txt_cash_back").val(addCommas(txtCashBack));
		 displayDigit(txtCashBack);
	}//func

	function getPmtTemp(){
		/**
		 *@desc update data in flexigrid ,point and net amount
		 *@retrun Boolean false
		 *@modify 12062014
		 */
		$.ajax({
			type:'post',
			url:'/sales/cashier/getpages',
			cache: false,
			data:{				
				rp:14,
				flg:'promotion',
				now:Math.random()
			},
			success:function(data){					
				$('#tbl_cashier').flexOptions({url:'/sales/cashier/promotiontemp',newp:data}).flexReload(); 
				
				if($("#dlgSetBagBarcode").dialog( "isOpen" )!==true){
					 initFieldPromt();//*WR12062014 for check forcus
	            }
				//initFieldPromt();
				/////////////
				var cn_amount=$("#cn_amount").val();//??????????????????????????????????????????????????????????????????????????????????????? CN
				var flg_chk_point='no';
				if(jQuery.trim($("#csh_member_no").val())!='' && jQuery.trim($("#csh_status_no").val())!='01'){
					flg_chk_point='yes';
				}
				$.getJSON(
						//"/sales/cashier/ajax",
						"/sales/cashier/getsumcshtemp",
						{
							cn_amount:cn_amount,
							flg_chk_point:flg_chk_point,
							xpoint:$("#csh_xpoint").val(),
							flg_tbl:'promotion',
							actions:'getSumCshTemp'
						},
						
						function(data){
								$.each(data.sumcsh, function(i,m){
									//alert("m.exist="+m.exist+"m.sum_net_amt="+m.sum_net_amt);
									if(m.exist=='yes' && parseInt(m.sum_net_amt)!='0'){
										var csh_sum_quantity=parseInt(m.sum_quantity);
										var csh_sum_discount=parseFloat(m.sum_discount);
										var csh_sum_amount=parseFloat(m.sum_amount);
										var csh_net_amt=parseFloat(m.sum_net_amt);
										//var csh_coupons=0.00;
										csh_sum_discount=csh_sum_discount.toFixed(2);
										csh_sum_amount=csh_sum_amount.toFixed(2);
										$('#csh_sum_discount').val(csh_sum_discount);
										$('#csh_sum_amount').val(addCommas(csh_sum_amount));
										$('#csh_net').val(addCommas(csh_net_amt.toFixed(2)));
										//--------------------------------------------------------									
										var point_receive=parseInt(m.point_receive);
										$('#csh_sum_qty').html(csh_sum_quantity + " PCS").css({"font-size":"200%","color":"#FFF"});									
										//WR20120901 test for 50BTO1P
										if($('#csh_promo_code').val()=='50BTO1P'){
											$('#csh_point_receive').val(m.mp_point_receive);
											$('#csh_point_used').val(m.mp_point_used);
											$('#csh_point_net').val(m.mp_point_net);
										}else{
											var point_used=$('#csh_point_used').val();
											var point_net=parseInt(point_receive)-parseInt(point_used);
											$('#csh_point_receive').val(point_receive);
											$('#csh_point_net').val(point_net);
										}
										//--------------------------------------------------------
									}else{
										$('#csh_sum_discount').val('0.00');
										$('#csh_sum_amount').val('0.00');
										$('#csh_net').val('0.00');
										$('#csh_sum_qty').html('');//WR07022013
									}	
									displayDigit($('#csh_net').val());		
									if($('#csh_gap_promotion').val()=='Y' && $('#csh_promo_tp').val()=='M'){			
										getDiscountNetAmt();				
									}
								});//foreach
								return false;
						}//
				);
				/////////////
				return false;
			}
		});		
	}//func
	
	function reDisCntFshPurchase(){
		/**
		 * @desc WR10022014
		 */
		var member_no=$('#csh_member_no').val();
		$.ajax({
			type:'post',
			url:'/sales/member/rediscntfshpurchase',
			data:{
				member_no:member_no,
				rnd:Math.random()
			},success:function(data){
				if(data!=''){
					$('#csh_percent_discount').html(data);
				}
				getPmtTemp();
			}
		});
	}//func
	
	function rePaymentBill(){
		/**
		 * @desc
		 */
		if($("#csh_status_no").val()=='01'){
			initTblTemp();
			getCshTemp('Y');
			initFormCashier();
		}else if($("#csh_status_no").val()=='02'){
			//*WR10022014
			reDisCntFshPurchase();
		}else if($("#csh_status_no").val()=='03'){
			reCalEcouponDiscount();
		}else if($("#csh_status_no").val()=='04'){
			//*WR22072014 ??????????????????????????????????????????
			var sp_promo_code=$('#csh_promo_code').val();
			if(sp_promo_code=='P2DR'){
				initFormCashier();
				initTblTemp();
				getCshTemp('Y');				
			}else{
				rePromoPointDiscount();
			}			
		}else if($("#csh_status_no").val()=='05'){
			//?????????????????????????????????????????????,?????????????????????????????????????????????
			initFormCashier();
			initTblTemp();
			getCshTemp('Y');
		}else if($('#csh_promo_code').val().substring(0,2)=='R_'){
			//*WR26012015
			initFormCashier();
			initTblTemp();
			getCshTemp('Y');
			return false;
		}else{
			//*WR16102014-27102014
			if($('#csh_promo_tp').val()=='MCOUPON' || $('#csh_promo_tp').val()=='LINE' || 
					$('#csh_promo_tp').val()=='MCS' || $('#csh_promo_tp').val()=='S' || $('#csh_promo_tp').val()=='O'){
				initFormCashier();
				initTblTemp();
				getCshTemp('Y');
				var play_last_pro=$("#csh_play_last_promotion").val();
				if($.trim(play_last_pro)=='Y'){
					lastdelpro();//*WR30032015 joke
				}
				return false;
			}
			//?????????????????????
			//for manual pro
			if($('#csh_trn_diary2_sl').val()=='Y'){
				getCshTemp('N');
				return false;
			}else if($('#status_pro').val()=='0' || $('#status_pro').val()=='1'){
    			getPmtTemp();//call temp of promotion trn_tdiary_tmp1
    		}else{
    			getCshTemp('N');
        	}
    		lastdelpro();//joke
    		//return false;
		}
	}//func
	
	function clearFormPayment(){
		/**
		 * @desc
		 * @param
		 */		
		$('#txt_cash').val('');//????????????????????????????????????????????????????????????????????????????????????
		$('#txt_cash_2').val('');
		
		$('#txt_credit').val('0.00');//????????????????????????????????????????????????????????????????????????????????????????????????
		$("#txt_redeem_point_cash").val('0.00');//???????????????????????? ???????????????????????????????????? csh_point_net
		$('#txt_voucher').val('0.00');//???????????? coupon or voucher
		$('#txt_voucher_cash').val('0.00');//???????????????????????????		
		$('#txt_cash_back').val('0.00');//????????????????????????????????????	
	}//func
	
	function paymentBill(status_no){
		/**
		*@desc : create 22052014
		*          : modify 23052014
		*@param : String status_no :
		*@return void
		*/
		var chk_member_no=$('#csh_member_no').val();
	    if(status_no=='00' && chk_member_no!=''){
	    	$.ajax({
	    		type:'post',
	    		url:'/sales/accessory/getformbagbarcode',
	    		data:{
	    			rnd:Math.random()
	    		},
	    		success:function(data){		   
	    			data=$.trim(data);
	    			if(data!=''){
	    				//key barcode to map bag
	    				var dlgSetBagBarcode = $('<div id="dlgSetBagBarcode">\
	    			            <p>Set Bag</p>\
	    			        </div>');
	    			            if($("#dlgSetBagBarcode").dialog( "isOpen" )===true) {
	    			            	return false;
	    			            }//if
	    			            dlgSetBagBarcode.dialog({
	    				           autoOpen:true,
	    						   //width:'25%',
	    						   height:'auto',	
	    						   modal:true,
	    						   resizable:false,
	    						   closeOnEscape:false,		
	    				           title: "???????????????????????? Barcode ????????? ",		
	    				           position:"center",
	    				            open:function(){		
	    				            	 $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
	    				            	$(this).dialog('widget')
	    					            .find('.ui-dialog-titlebar')
	    					            .removeClass('ui-corner-all')
	    					            .addClass('ui-corner-top');
	    			    			    $(this).dialog("widget").find(".ui-button-text")
	    			                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});	    			    			    
	    			    			    $('#dlgSetBagBarcode').empty().html(data);	   
	    			    			    $(":input.bag_barcode").first().focus();
	    			    			    $(".ui-widget-overlay").live('click', function(){
	    			    			    	$(":input.bag_barcode").first().focus();
										});
	    			    			    var c_bag_barcode=$('.bag_barcode').length;
	    			    			    $('.bag_barcode').each(function(){
	    			    			    	$(this).keypress(function(evt){
	    			    			    		var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	    								        if(key == 13){
	    								            evt.preventDefault();
	    								            var str_bagbarcode_id=$(this).attr('id');
	    								            var this_bag_barcode=$('#' +str_bagbarcode_id).val();
	    								            if(this_bag_barcode.substr(0,2)!='14'){
						            					jAlert('Invalid Barcode','WARNING!',function(b){	
						            						$('#' +str_bagbarcode_id).val('').focus();
										    				return false;
											    		 });
						            					return false;
						            				}
	    								            var arr_bagbarcode_id=str_bagbarcode_id.split('_');
	    								            if(arr_bagbarcode_id[0]=='1' && c_bag_barcode==2){
	    								            	var thisIndex = $(this).index();
	    								            	var nextIndex = thisIndex + 1;
	    								            	//var nextTextFieldId ='#' + $('input:text').eq(nextIndex).attr('id');
	    								            	var nextTextFieldId ='#' + $(":input.bag_barcode").eq(nextIndex).attr('id');
	    								            	$(nextTextFieldId).focus(); 
	    								            }else{
	    								            	$("#dlgSetBagBarcode").parents('.ui-dialog').first().find('.ui-button').first().click();
	    								            }
	    								            return false;
	    								        }	    			    			    		
	    			    			    	});	    			    			    	
	    			    			    });
	    				            },
	    				            buttons: {
									 	"OK":function(evt){
									 		evt.preventDefault();
									 		evt.stopPropagation();
									 		var chk_invalid_barcode='Y';
									 		var bag_barcode_null='';
									 		var chk_keyin=1;
									 		var arr_keybarcode= new Array();
									 		$('.bag_barcode').each(function(){
						            				var bagbarcode_id=$(this).attr('id');
						            				var bag_barcode=$('#' + bagbarcode_id).val();
						            				if(bag_barcode.substr(0,2)!='14'){
						            					chk_invalid_barcode='N';
						            					jAlert('Invalid Barcode','WARNING!',function(b){	
						            						$('#' + bagbarcode_id).focus();
										    				 return false;
											    		 });
						            					return false;
						            				}						            				
						            				if(bag_barcode.length<1){
						            					chk_keyin=0;
						            					bag_barcode_null=bagbarcode_id;
						            					return false;
						            				}		
						            				arr_keybarcode.push(bag_barcode);
					    		    		});
									 		
									 		if(chk_invalid_barcode=='N'){
									 			return false;
									 		}
									 		  
										 	if(chk_keyin == 0){
										 			jAlert('??????????????????????????????????????????????????????????????????','WARNING!',function(b){			
										 				$('#' + bag_barcode_null).focus();
									    				 return false;
										    		 });
										 	}else{
										 			var bagbarcode_list='';
							            			for(i=0;i<arr_keybarcode.length;i++){
							            				bagbarcode_list+= arr_keybarcode[i]+"#";
						            				} 
							            			$.ajax({
							            				type:'post',
							            				url:'/sales/accessory/updbagbarcode',
							            				data:{
							            					bagbarcode:bagbarcode_list,
							            					rnd:Math.random()
							            				},success:function(data){}
							            			});
							            			
							            			$('#dlgSetBagBarcode').dialog('close');							            			
								            		setTimeout(function(){
								            			paymentBillOrg('00');
								            		},800);
										 			
										 		}
        									return false;
									 	}
					    		    },
	    				            close:function(evt){
	    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
	    									evt.stopPropagation();
	    		    						evt.preventDefault();
	    								}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
	    									evt.stopPropagation();
	    		    						evt.preventDefault();		    		    						
	    								}	
	    				            	$(this).remove();
	    				            }
	    			        });	
	    			}else{
	    				//normal step 
	    				paymentBillOrg('00');
	    			}
	    		}
	    	});//end ajax
	    }else{
	    	paymentBillOrg(status_no);
	    }
	}//func
	
	//*WR 11062015 move for support idcard
	//////////////start save transaction/////////////
	function saveTransaction(){
		/*
		 *@desc
		 *@return
		 *@lastmodify : 27032018
		 */
		var user_id=$("#csh_user_id").val();
		var cashier_id=$("#csh_cashier_id").val();
		var saleman_id=$("#csh_saleman_id").val();
		var doc_tp="";//????????????????????????????????????
		var url="/sales/cashier/payment";
		if($("#csh_doc_tp_vt").val()!=''){
			doc_tp=$("#csh_doc_tp_vt").val();
		}else{
			doc_tp=$("#csh_doc_tp").val();
		}
		
		var xpoint_promo_code=$('#csh_promo_code').val();
		var application_id='';//????????????????????????????????????
		var expire_date='';//??????????????????????????????????????????
		var status_no=$('#csh_status_no').val();//???????????????????????????????????????
		var member_no=$('#csh_member_no').val();//??????????????????????????????
		     member_no=$.trim(member_no);								
		var arr_percent_discount=$('#csh_percent_discount').html().split('+');
		var member_percent=arr_percent_discount[0];//????????????????????????????????????????????????%	
		var special_percent=arr_percent_discount[1];//???????????????????????????????????????????????????%									
		var refer_member_id=$('#csh_refer_member_id').val();//????????????????????????????????????????????????????																
		if($.trim(status_no)=='01'){
			//????????????????????????????????? ????????????????????? ????????????????????????/?????????????????????
			application_id=$("#csh_application_id").val();
			expire_date=jQuery.trim($("#csh_expire_date").val());
			//*WR25022016
			if(member_no.length==0 && $('#csh_application_type').val()=="NEW" && $('#csh_card_type').val()=="MBC"){
				jAlert('??????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){			
					return false;
				});		
				return false;
			}else if(refer_member_id.length==0 && $('#csh_application_type').val()=="ALL"){
				jAlert('?????????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){			
					return false;
				});	
				return false;
			}//end if			
			//url="/sales/member/payment";									
		}
		var card_status='';//???????????????????????????????????????/?????????????????????
		if($.trim(status_no)=='05'){
			url="/sales/member/payment";
			card_status=$("#csh_card_status").val();
			//*WR28032014
			refer_member_id=$('#info_refer_member_id').html();
			refer_member_id=$.trim(refer_member_id);
		}
		var other_discount;//?????????????????????????????????
		var net_amount=$('#txt_netvt').val();//new for support promotion
		var ex_vat_amt;//???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
		var ex_vat_net;//???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
		var vat;//???????????????????????????????????????????????????????????????????????? (net_amount-ex_vat_net)*7/107
		var paid=$('#sel_credit').val();//????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
		/*var paid=$('#sel_credit:selected').val();//????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????*/
		
		var pay_cash=$('#txt_cash').val();//???????????????????????????????????????????????????????????????????????????????????? $
		var pay_cash2=$('#txt_cash_2').val();//???????????????????????????????????????????????????????????????????????????????????? R
		
		var pay_credit=$('#txt_credit').val();//????????????????????????????????????????????????????????????????????????????????????????????????
		var credit_no=$('#txt_credit_no').val();//???????????????????????????????????????????????????																
		var redeem_point=$("#txt_redeem_point_cash").val();//???????????????????????? ???????????????????????????????????? csh_point_net
		
		//*WR23062014 support play coupon promotion
		var status_couponpromo=$('#status_couponpromo').val();
		var coupon_code=$("#csh_coupon_code").val();//*WR05082014 ???????????? coupon promotion only							
		if(coupon_code!=''){
			if($.trim(status_no)=='01'){
				var promo_coupon_code=application_id;
			}else{
				var promo_coupon_code=$("#csh_promo_code").val();
			}
			coupon_code=promo_coupon_code + "#" + coupon_code;
			//*WR04012016
			if($.trim(status_no)=='01' && (application_id=='OPMGMC300'  || application_id=='OPMGMI300')){
				var coupon_code=$("#csh_coupon_code").val();
			}
		}
		
		var pay_cash_coupon=$('#txt_voucher_cash').val();//???????????????????????????
		
		if($('#payment_channel').val()=="Alipay"){
			var credit_no=$('#payment_transid').val();//???????????????????????????????????????????????????		
			var credit_tp=$('#payment_channel').val();//????????????????????????????????????????????????
			var bank_tp=$('#payment_channel').val();//??????????????????????????????
		}else{
			var credit_no=$('#txt_credit_no').val();//???????????????????????????????????????????????????		
			var credit_tp=jQuery.trim($("#sel_credit option:selected").text());//????????????????????????????????????????????????
			var bank_tp=jQuery.trim($("#sel_credit option:selected").text());//??????????????????????????????
		}
		if(pay_credit=='0.00'){
			var credit_tp='';
			var bank_tp='';
		}
		
		
		var change=$('#txt_cash_back').val().replace(/[^\d\.\-\ ]/g,'');//????????????????????????????????????  $
		var change2=$('#txt_cash_back_2').val().replace(/[^\d\.\-\ ]/g,'');//???????????????????????????????????? R
		
		var name=$('#vt_name_val').val();
		var address1=$('#vt_address_val1').val();
		var address2=$('#vt_address_val2').val();
		var address3=$('#vt_address_val3').val();
		var remark1=$('#vt_taxid').val();
		var remark2=$('#vt_taxid_branch_seq').val();
		//*WR10032017
		var passport_no=$('#vt_passport_no_val').val();
		var point_begin=$("#csh_balance_point").val();
		var csh_get_point=$("#csh_get_point").val();//????????????????????????????????????????????????????????????????????? N,Y
		var csh_point_receive=$("#csh_point_receive").val();   //??????????????????????????????????????????
		var csh_point_used=$("#csh_point_used").val();	     //????????????????????????????????? 
		var csh_point_net=$("#csh_point_net").val();      //??????????????????????????????
		var csh_xpoint=$("#csh_xpoint").val();
		//for cn
		var refer_doc_no=$("#csh_refer_doc_no").val();
		var cn_amount=0.00;
		if(status_no=='06'){
			refer_doc_no=$("#cn_doc_no").val();
			cn_amount=$("#cn_amount").val();
		}
		//*WR22012015
		var csh_ops_day= $('#csh_ops_day').val();
		var csh_id_card=$('#csh_id_card').val();
		var csh_mobile_no=$('#csh_mobile_no').val();
		//*WR 10032015
		var csh_bill_manual_no=$('#csh_bill_manual_no').val();
 		var csh_ticket_no=$('#csh_ticket_no').val();
		var opts={
    			type:'post',
    			url:url,
    			async:false,
    			cache: false,
    			data:{
						user_id:user_id,
						cashier_id:cashier_id,
						saleman_id:saleman_id,
						doc_tp:doc_tp,
						status_no:status_no,
						member_no:member_no,
						member_percent:member_percent,
						special_percent:special_percent,
						refer_member_id:refer_member_id,
						net_amount:net_amount,
						ex_vat_amt:ex_vat_amt,
						ex_vat_net:ex_vat_net,
						vat:vat,
						paid:paid,
						pay_cash:pay_cash,
						pay_cash2:pay_cash2,
						pay_credit:pay_credit,
						redeem_point:redeem_point,
						change:change,
						change2:change2,
						coupon_code:coupon_code,
						pay_cash_coupon:pay_cash_coupon,
						credit_no:credit_no,
						credit_tp:credit_tp,
						bank_tp:bank_tp,
						name:name,
						address1:address1,
						address2:address2,
						address3:address3,
						remark1:remark1,
						remark2:remark2,
						point_begin:point_begin,
						application_id:application_id,
						expire_date:expire_date,
						point_receive:csh_point_receive,
						point_used:csh_point_used,
						point_net:csh_point_net,												
						xpoint:csh_xpoint,
						get_point:csh_get_point,
						card_status:card_status,
						refer_doc_no:refer_doc_no,
						cn_amount:cn_amount,
						xpoint_promo_code:xpoint_promo_code,
						special_day:csh_ops_day,
						idcard:csh_id_card,
						mobile_no:csh_mobile_no,
						bill_manual_no:csh_bill_manual_no,
						ticket_no:csh_ticket_no,
						passport_no:passport_no,
						application_id:application_id,
						action:"savetransaction"
				},
    			success:function(data){
					var arr_data=data.split('#');
					if(arr_data[0]=='1'){
						$("#dlgSaleMan").dialog("close");
						$("#dlgPayment").dialog("close");
						if(arr_data[6]!='C' && arr_data[9]!='Y'){
							printBill(data);
						}							
						//cash drawer
						if(arr_data[7]=='Y' && (arr_data[2]=='SL' || arr_data[2]=='VT')){
							requestServerCall("http://localhost/sales/cmd/opencashdrawer.php?rnd="+ Math.random() +"");
						}										
						if(getOnlineStatus()===1){
							//*WR23062014 coupon promotion 
							var promo_code=$("#csh_promo_code").val();	
							var chk_coupon_code=$("#csh_coupon_code").val();		
							var idcard=$('#csh_id_card').val();
							if(application_id=='OPPLI300' || application_id=='OPPLC300'){
								$('#status_couponpromo').val('Y');
								//promo_code='OI06010615';
								//promo_code='OI06470416';
								//promo_code='OI06501116';//2017
								promo_code='OI06320417';//2017/2
							}
							
							//*WR18022016 
							if(application_id=='OPLID300'){
								//line promotion
								$('#status_couponpromo').val('Y');
								promo_code='OI07200216';
							}
							
							if(application_id=='OPMGMC300' || application_id=='OPMGMI300'){
								var str_coupon_mgm=$("#csh_coupon_code").val();
								var arr_coupon_mgm=str_coupon_mgm.split('#');
								promo_code=arr_coupon_mgm[2];
								chk_coupon_code=arr_coupon_mgm[3];
								$('#status_couponpromo').val('Y');
								promo_code='OX02041215';//amp ?????????????????????????????????????????????
							}
							
							if(promo_code!=''  && chk_coupon_code!=''){
								var objReqCouponPro=$.ajax({
									type:'post',
									url:'/sales/member/lockcouponpro',
									cache:false,
									data:{
										promo_code:promo_code,
										coupon_code:chk_coupon_code,
										member_no:member_no,
										doc_no:arr_data[1],
										idcard:idcard,
										rnd:Math.random()
									},success:function(data){
										objReqCouponPro=null;
									}
								});
								setTimeout(function(){
								  if (objReqCouponPro) objReqCouponPro.abort();
								},2000);
							}
							//sms promotion												
							if($('#status_smspromo').val()=='Y' && $('#id_smspromo').val()!=''){
								var objReqSmsPro=$.ajax({
									type:'post',
									url:'/sales/cashier/locksmspro',
									cache:false,
									data:{
										sms_promo_code:$('#id_sms_promo_code').val(),
										sms_mobile:$('#id_sms_mobile').val(),
										id_smspromo:$('#id_smspromo').val(),
										id_redeem_code:$('#id_redeem_code').val(),
										doc_no:arr_data[1],
										rnd:Math.random()
									},success:function(data){
										objReqSmsPro=null;
									}
								});
								setTimeout(function(){
								  if (objReqSmsPro) objReqSmsPro.abort();
								},5000);
							}												
							//insert to act cluster 
							var objReqAdd2Pdiary=$.ajax({
									type:'post',
									url:'/sales/cashier/add2pdiary',
									cache:false,
									data:{
										doc_no:arr_data[1],
										rnd:Math.random()
									},
									success:function(){	
										objReqAdd2Pdiary=null;
									}
								});	
							setTimeout(function(){
							  if (objReqAdd2Pdiary) objReqAdd2Pdiary.abort();
							},5000);													
     		        	}//IF ONLINE MODE
						
						//*WR25022016
						if(arr_data[5]=='01'){
							setTimeout(function(){
								registerfrom(arr_data[1]);
							},400);
						}//func
						
						
						
			    		$("<div id='dlgCloseBill'></div>").dialog({
			    			 	autoOpen:true,
			    		        modal: true,
			    		        width: 430,
			    		        height:255,
			    		        title:"<span class='ui-icon ui-icon-cart'></span>Save Results",
			    		        position: 'middle',
			    		        resizable:false,
			    		        closeOnEscape:true,	
			    		        open:function(){
			    		        	$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
									$(this).dialog('widget')
							            .find('.ui-dialog-titlebar')
							            .removeClass('ui-corner-all')
							            .addClass('ui-corner-top');
				    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
					    			    										"font-size":"27px","color":"#000"});
				    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
				    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
				    			    $(this).dialog("widget").find("button")
					                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});	
			    		        	$(this).html('<p align="center" style="margin-top:35px;">Save '+arr_data[1]+' Success','?????????????????????????????????</p>');
			    		        },close:function(){
			    		        	initTblTemp();//*WR20120905
			    		        	initFormCashier();
						    		getCshTemp('Y'); 
						    		lastdelpro();//*WR30032015 joke
				    		        $(this).remove();
				    		    },
				    		    buttons: {
								 	"Close":function(){ 	
								 		initTblTemp();//*WR20120905
					    		    	initFormCashier();
							    		getCshTemp('Y'); 
					    		        $(this).remove();
					    		    }
							}							    		 
				    	});
							
					}else{
						jAlert('????????????????????????????????????????????????????????????????????????????????????????????? \n'+arr_data[1] + '\n<u>??????????????? Click ???????????? Refresh ?????????????????????????????????????????????????????????</u>','?????????????????????????????????',function(){
							$("#dlgSaleMan").dialog("close");
	    					return false;
			    		 });
					}
	    			
	    		}
    		};
		$.ajax(opts);
	}//func
	//////////////end save transaction //////////////
	
	//*WR 11062015 move for support idcard
	function checkSaleMan(){
		/**
		 * @desc : 25092013 for support set bag
		 */
		//--------- SHOW CHECK SALEMAN ----------
		var dialogOpts_saleman = {
				autoOpen: false,
				width:500,
				height:'auto',	
				modal:true,
				stack: true,
				resizable:true,
				position: { my: "center bottom", at: "center center", of: window },
				showOpt: {direction: 'down'},		
				closeOnEscape:false,	
				title:"<span class='ui-icon ui-icon-person'></span>??????????????????????????????",
				open: function(){ 
					$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					/*
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
	    			    																				"margin":"0 0 0 0","font-size":"27px","color":"#000","height":"50px"});*/
					$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
						"margin":"0 0 0 0","font-size":"27px","color":"#000","height":"400px"});
					
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});							    			    
					    $(".ui-widget-overlay").live('click', function(){
					    	$("#chk_saleman_id").focus();
						});
						$("#dlgSaleMan").html("");
						$("#dlgSaleMan").load("/sales/cashier/saleman?now="+Math.random(),
						function(){	
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
													jAlert('????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
														$("#chk_saleman_id").val('').focus();
														return false;
									    			});
												}else if($.trim(arr_data[3])=='P'){
													jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
														$("#chk_saleman_id").val('').focus();
														return false;
									    			});
												}else if($.trim(arr_data[3])=='N'){
													jAlert('???????????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
														$("#chk_saleman_id").val('').focus();
														return false;
									    			});
												}else{
									            	$("#csh_saleman_id").val(arr_data[0]);
											        saveTransaction();
									            }
								            }
								    };
						            $.ajax(opts);
						            return false;
						        }
							});
							
						});
				},				
				close: function(evt,ui){	         
					$('#dlgSaleMan').dialog('destroy');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
						evt.stopPropagation();
						evt.preventDefault();
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();
					}	
					$('#btn_payment_confirm').focus();		
					$('#btn_payment_confirm').removeClass('ui-state-focus ui-state-active').addClass('ui-state-focus');												
				 }
			};			
			$('#dlgSaleMan').dialog('destroy');
			$('#dlgSaleMan').dialog(dialogOpts_saleman);			
			$('#dlgSaleMan').dialog('open');
		//--------- SHOW CHECK SALEMAN ----------
			return true;
	}//func
	
	//*WR 11062015 move for support idcard
	function conFirmMemberID(){
		/**
		 * @param application_id promo_code
		 * @desc modify:24042014
		 */		
		$("<div id='dlgConFirmMemberID'></div>").dialog({
	       	   autoOpen:true,
	  				width:'20%',		
	  				height:'auto',	
	  				modal:true,
	  				resizable:false,
	  				position: { my: "center bottom", at: "center center", of: window },
	  				showOpt: {direction: 'up'},		
	  				closeOnEscape: false,
	  				title:"<span class='ui-icon ui-icon-person'></span>???????????????????????????????????????????????????????????????",
	  				open: function(){ 
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
																										"font-size":"24px","color":"#000000",
																										"padding":"5 0.1em 0 0.1em"});   //ui-corner-all
						$("#dlgConFirmMemberID").html('');
		            	$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
		            	$(this).dialog("widget").find(".ui-button-text")
	                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
		            	//create form confirm member_id
        				$.ajax({
        					type:'post',
        					url:'/sales/member/formconfirmmemberid',
        					data:{
        						rnd:Math.random()
        					},success:function(data){
        						$("#dlgConFirmMemberID").html(data);
        						$('#confirm_member_no').val('');
        						$('#confirm_member_no').focus();
        						
        						//*** check lock unlock
    							if($("#csh_lock_status").val()=='Y'){
    								lockManualKey();
    							}else{
    								unlockManualKey();
    							}	
    							//*** check lock unlock		
        						
    							$('#confirm_member_no').keypress(function(evt){
    								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
    						        if(key == 13){
    						            evt.preventDefault();
    						            var cmp_member_no=$('#csh_member_no').val();
    						            var confirm_member_no=$('#confirm_member_no').val();
    						            cmp_member_no=$.trim(cmp_member_no);
    						            confirm_member_no=$.trim(confirm_member_no);
    						            if(cmp_member_no.length>9){
    						            	///////////// MEMBER CARD ONLY //////////////
    						            	if(cmp_member_no==confirm_member_no){
	    						            	checkSaleMan();
	    						            	$('#dlgConFirmMemberID').dialog("close");
	    						            }else{
	    						            	jAlert("MEMBER CODE NOT MATCH. PLEASE CHECK AND TRY AGAIN.", 'WARNING!',function(){
						            				$('#confirm_member_no').focus();
		           									return false;
		           								});
	    						            	return false;
	    						            }
				                        	return false;
    						            	///////////// MEMBER CARD ONLY //////////////
    						            }else{
    						            	
    						            	 ///////////////// START VIRTUAL CARD ////////////////////
        						            $.ajax({
        						                type:'post',
        						                url:'/sales/cashier/decodecaller',
        						                data:{
        						                	member_no:confirm_member_no,
        						                	rnd:Math.random()
        						                },
        						                success:function(data){
        						                        var arr_data = data.split("#");
        						                        confirm_member_no = arr_data[1];
        						                        if(arr_data[0]=='Y'){
        						                        	
        						                        	if(cmp_member_no==confirm_member_no){
            		    						            	checkSaleMan();
            		    						            	$('#dlgConFirmMemberID').dialog("close");
            		    						            }else{
            		    						            	jAlert("???????????????????????????????????????????????????????????????????????????????????? Please check and try again.", 'WARNING!',function(){
            							            				$('#confirm_member_no').focus();
            			           									return false;
            			           								});
            		    						            	return false;
            		    						            }
        						                        	return false;
        						                        	
        						                        }else if(arr_data[0]=='T'){
        						                        	
        						                        	jAlert("Code ?????????????????????????????????????????? Code ????????????????????????","WARNING!",function(){	                                       
        				                                        $("#confirm_member_no").val('');
        				                                        $("#confirm_member_no").focus();
        				                                        return false;
        				                                    });
        						                        	
        		    						            	
        		    						            }
        						                        
        						                    }
        						                }); 
        						            ///////////////// START VIRTUAL CARD ////////////////////
    						            	
    						            }

    						            
    						            return false;
    						        }
    							});
        					}
        				});//end ajax
	  				},
	  				close:function(evt){
	  					$("#dlgConFirmMemberID").remove();
		            	$("#dlgConFirmMemberID").dialog("destroy");	  					
		   			}
	          });
	}//func
	
	//*WR 11062015 move for support idcard
	/////////////////// call EDC ////////////////////
	function callEDC(credit_net_amt){
		/**
		 * @desc
		 * @param Float credit_net_amt
		 * @return
		 */
		var credit_net_amt=parseFloat(credit_net_amt);
		var net_total_payment=$('#net_total_payment').html();
		net_total_payment=net_total_payment.replace(/[^\d\.\-\ ]/g,'');
		net_total_payment=parseFloat(net_total_payment);
		var txt_cash=0.00;								
		if(isNaN($('#txt_cash').val())){
			txt_cash=parseFloat($('#txt_cash').val());	
		}	
		var curr_total_payment=net_total_payment-txt_cash;			
		
		if(parseFloat(credit_net_amt)<1){
			jAlert('????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
				$("#txt_credit_value").focus();
				return false;
			});
		}else if(parseFloat(credit_net_amt)>curr_total_payment){
			jAlert('???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
				$("#txt_credit_value").focus();
				return false;
			});
		}else{
			var opts_edc={
					type:'post',
					url:'/sales/cashier/calledc',
					cache:false,
					data:{
						credit_net_amt:credit_net_amt.toFixed(2),
						actions:'calledc',
						rnd:Math.random()
					},success:function(data){
						//////////////////////\\\\\alert\\\\\/////////////////////////
						jConfirm('????????????????????????????????????????????????????????????????????????????????????','WARNING!', function(r){
					        if(r){
					        	$('#btn_payment_confirm').trigger('click');
					        }else{
					        	//////////////\ WR10072014 /\\\\\\\\\\\\\\\\\\
					        	$("#sel_credit option[value='0000']").attr("selected", "selected");//paid type default pay_cash
					        	$('#txt_credit_no').val('');
						 		$("#txt_credit_value").val('');
								$("#txt_credit").val('0.00');
					        	//////////////\ WR10072014 /\\\\\\\\\\\\\\\\\\\
					        	$('#txt_credit').val('0.00');	
					        	calPaid();
					        	$('#txt_cash').focus();											        	
						     }
					        return false;
						});						
						//////////////////////\\\\\alert\\\\\/////////////////////////												
					}
			};
						
			jAlert('?????????????????????????????????????????????????????????????????????????????????','WARNING!',function(r){
				$('#popup_ok').focus();
				if(r){
					$.ajax(opts_edc);
				}			
			});									
		}								
		return false;
	}//func							
	
	//*WR 11062015 move for support idcard
	////////////////////start credit //////////////////
	function dlgCredit(){
		/**
		*@desc
		*@return
		*/
		//dlg-credit
		var dialogOpts_sel_credit = {
				autoOpen: false,
				width:450,		
				height:'auto',	
				modal:true,
				resizable:true,
				position:['center','center'],
				title:"<span class='ui-icon ui-icon-person'></span>??????????????????????????????",
				closeOnEscape:true,
				open: function(){ 
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});				        						
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');			        						
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc","color":"#000"});
						$(this).dialog("widget").find(".ui-dialog-buttonpane")
	                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
						$("#dlgCredit").html("");
						$("#dlgCredit").load("/sales/cashier/paycase?actions=wincredit&now="+Math.random(),
								function(){
									var cnet=parseFloat($("#txt_net").val().replace(/[^\d\.\-\ ]/g,''));
									//$('#txt_credit_value').ForceNumericOnly();
									$("#txt_credit_no_value").focus();
									if($('#txt_credit').val()!='0.00'){
					        			$('#txt_credit_value').val($('#txt_credit').val());
					        		}else{
        								$("#txt_credit_value").val(cnet);
									}
									
									$("#txt_credit_no_value").keypress(function(evt){
										var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
										  if (key == 46 || key == 8 || key == 37 || key == 39) {
											  return true;
										  }else {
											    if (key == 13) {
											    	var credit_no=$.trim($("#txt_credit_no_value").val());
		        									if(credit_no.length<4 || credit_no.length>16){
		        										jAlert('?????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(r){
		        											if(r){
		        												$("#txt_credit_no_value").focus();
		        												return false;
		        											}	
		        						    			});				
		        										return false;
		        									}
		        									$('#txt_credit_no').val(credit_no);
		        									$("#txt_credit_value").focus();
		        									return false;
											    }else if (key < 48 || key > 57) {
											    	evt.preventDefault();	
											    }else{
											    	var $this = $(this);
												    $this.val($this.val().replace(/[^\d.]/g, ''));
												    if (($this.val().indexOf('.') != -1) && ($this.val().substring($this.val().indexOf('.')).length > 2)) {																		    	
												    	$this.val($this.val().substr(0,$this.val().length-1));
												    }
											    }
											    
										   }
									});//keypress
									
//									$("#txt_credit_no_value").keypress(function(e){
//        								var key = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
//        								if(key==13){
//        									var credit_no=$.trim($("#txt_credit_no_value").val());
//        									if(credit_no.length<4 || credit_no.length>16){
//        										jAlert('?????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(r){
//        											if(r){
//        												$("#txt_credit_no_value").focus();
//        												return false;
//        											}	
//        						    			});				
//        										return false;
//        									}
//        									$('#txt_credit_no').val(credit_no);
//        									$("#txt_credit_value").focus();
//        									return false;
//        								}
//									});//keypress
									
        							$("#sel_credit").keypress(function(e){
        								var key = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
        								if(key==13){
	        								$("#txt_credit_value").focus();
	        								return false;		
	        							}
	        						});
        							
        							$("#txt_credit_value").keypress(function(evt){
										var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
										  if (key == 46 || key == 8 || key == 37 || key == 39) {
											  return true;
										  }else {
											    if (key == 13) {
											    	evt.stopPropagation();
		        									evt.preventDefault();						        									
		        									var credit_no_value=$("#txt_credit_no_value").val();
		        									credit_no_value=$.trim(credit_no_value);
		        									if(credit_no_value.length<4 || credit_no_value.length>16){
											 			jAlert('?????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(r){
		        											if(r){
		        												$("#txt_credit_no_value").focus();
		        												return false;
		        											}	
		        						    			});
											 			return false;
											 		}
											 		$('#txt_credit_no').val(credit_no_value);
		        									var txt_credit_value=$("#txt_credit_value").val();
		        									$("#txt_credit").val(txt_credit_value);						        									
		            								$('#dlgCredit').dialog('close');						            								
		            								callEDC(txt_credit_value);//*WR20120827
		        									return false;
											    }else if (key < 48 || key > 57) {
											    	evt.preventDefault();	
											    }else{
											    	var $this = $(this);
												    $this.val($this.val().replace(/[^\d.]/g, ''));
												    if (($this.val().indexOf('.') != -1) && ($this.val().substring($this.val().indexOf('.')).length > 2)) {																		    	
												    	$this.val($this.val().substr(0,$this.val().length-1));
												    }
											    }
											    
										   }
									});//keypress
        							
//        							$("#txt_credit_value").keypress(function(evt){
//        								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
//        								if(key==13){
//        									evt.stopPropagation();
//        									evt.preventDefault();						        									
//        									var credit_no_value=$("#txt_credit_no_value").val();
//        									credit_no_value=$.trim(credit_no_value);
//        									if(credit_no_value.length<4 || credit_no_value.length>16){
//									 			jAlert('?????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(r){
//        											if(r){
//        												$("#txt_credit_no_value").focus();
//        												return false;
//        											}	
//        						    			});
//									 			return false;
//									 		}
//									 		$('#txt_credit_no').val(credit_no_value);
//        									var txt_credit_value=$("#txt_credit_value").val();
//        									$("#txt_credit").val(txt_credit_value);						        									
//            								$('#dlgCredit').dialog('close');						            								
//            								callEDC(txt_credit_value);//*WR20120827
//        								}
//        							});//keypress
								}
						);			        							
				},
				buttons: {//*WR18082012
				 	"????????????":function(evt){
				 		evt.preventDefault();
				 		evt.stopPropagation();
				 		var credit_no_value=$("#txt_credit_no_value").val();
				 		credit_no_value=$.trim(credit_no_value);
				 		
				 		//*WR21022017 for new mem OPKTC300
				 		var status_no=$('#csh_status_no').val();
				 		var application_id=$('#csh_application_id').val();
				 		//alert(status_no + "::" + application_id);
				 		if(status_no=='01' && application_id=='OPKTC300'){				 			
				 			var arr_ktc300 = ["430445","439137", "456723","493113","456724","493112","451347","523910",
				 			           "540716","540605","540604","543420","356342"];
				 			var chk_ktc300=credit_no_value.substring(0,6);
				 			if(jQuery.inArray(chk_ktc300, arr_ktc300) != -1) {
				 				credit_no_value=chk_ktc300;				 			    
				 			} else {
				 				jAlert('??????????????????????????????????????????????????????????????????????????? KTC ???????????????????????? 6 ????????????\n??????????????????????????????????????? OPKTC300 ????????????????????????','WARNING!',function(r){
									if(r){
										$("#txt_credit_no_value").focus();
										return false;
									}	
				    			});				 			    
				 				return false;
				 			} 
				 			
				 			if(credit_no_value.length<6 ){
					 			jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????? 6 ??????????????????????????????????????????????????? OPKTC300','WARNING!',function(r){
									if(r){
										$("#txt_credit_no_value").focus();
										return false;
									}	
				    			});
					 			return false;
					 		}
				 			
				 		}else 
				 		
				 		if(credit_no_value.length<4 || credit_no_value.length>16){
				 			jAlert('?????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(r){
								if(r){
									$("#txt_credit_no_value").focus();
									return false;
								}	
			    			});
				 			return false;
				 		}
				 		$('#txt_credit_no').val(credit_no_value);
				 		var txt_credit_value=$("#txt_credit_value").val();
						$("#txt_credit").val(txt_credit_value);
						$('#dlgCredit').dialog('close');	        									
						callEDC(txt_credit_value);//*WR20120827	      
						return false;
				 	}
    		    },
				close: function(evt,ui) {
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){			        							
						$("#sel_credit option[value='0000']").attr("selected", "selected");//paid type
						$('#txt_credit_no_value').val('');
						 if(evt.target!=this){
							return true;
						}
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						//is ok to stop bublig event for firefox
						evt.stopPropagation();
						evt.preventDefault();
						$("#sel_credit option[value='0000']").attr("selected", "selected");//paid type
						$('#txt_credit_no_value').val('');
						 if(evt.target!=this){
							return true;
						}
					}
					calPaid();
					$("#btn_payment_credit").removeClass("ui-state-focus ui-state-hover ui-state-active");
					$('#dlgCredit').dialog('destroy');
				 }
				};			
			
			$('#dlgCredit').dialog('destroy');
			$('#dlgCredit').dialog(dialogOpts_sel_credit);			
			$('#dlgCredit').dialog('open');
			return false;
	}//func
	
	function alertReturnIdcard(){
		/**
		 * @desc
		 * @return 
		 */
		var dlgAlertReturnIdCard = $('<div id="dlgAlertReturnIdCard"></div>');
	            dlgAlertReturnIdCard.dialog({
			           autoOpen:true,
			           width:'38%',
					   height:'250',	
					   modal:false,
					   resizable:false,
					   closeOnEscape:false,
			           title: "WARNING! ",
			           position: { my: "center bottom", at: "center center", of: window },
			           open:function(){				
			            	$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
		    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
		    			    																				"margin":"0 0 0 0","font-size":"28px","color":"#0000A0"});/*#606060#A52A2A#c8c7dc*/
		    			$(this).dialog("widget").find(".ui-button-text")
		                    .css({"padding":".1em .2em .1em .2em","font-size":"25px"});
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
		    			    
		            	$('#dlgAlertReturnIdCard').html('<p align="center" style="margin-top:35px;"><span id="msgReturnIdCard" style="font-size:35px;color:#FF0000;"><u>?????????????????????!!!</u></span>   ??????????????????????????????????????????????????????????????????????????????????????????</p>');	
		            	$('#msgReturnIdCard').blinky({ count:10});
		            	return false;
		            },buttons: {
					 	"Confirm":function(evt){
					 		evt.preventDefault();
					 		evt.stopPropagation();		
					 		checkSaleMan();
					 		$('#dlgAlertReturnIdCard').dialog('close');
							return false;
					 	}
	    		    },
		            close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();
    						return false;
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
    						evt.preventDefault();    		
    						return false;
						}	
		            	$(this).remove();
		            }
	        });	
	}//func
	
	function setBag2Temp(){
		/**
		 * @desc
		 * @create 22072015
		 * @return 
		 */
		if($("#dlgSetBag").dialog( "isOpen" )===true) {
        	return false;
        }//if
		 //-------------- SET BAG 25092013-------------------------------			
		var $chk_none_item='Y';//*WR 19082015 need to set bag option 0
    	var prefix_member_no_idcard=$('#csh_member_no').val();
	    prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
		var dlgSetBag = $('<div id="dlgSetBag">\
	            <p>Set Bag</p>\
	        </div>');
	            dlgSetBag.dialog({
			           autoOpen:true,
			           width:'70%',/*65*/
					   height:'570',	
					   modal:false,
					   resizable:true,
					   closeOnEscape:false,
			           title: "PLEASE SPECIFY BAG ",
			           position: { my: "center bottom", at: "center center", of: window },
			           open:function(){				
			            	$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
		    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
		    			    																				"margin":"0 0 0 0","font-size":"27px","color":"#000"});								            	
		            	$('#dlgSetBag').html('');		
		            	$.ajax({
		            		type:'post',
		            		url:'/sales/accessory/formsetbag',
		            		cache:false,
		            		data:{
		            			rnd:Math.random()
		            		},success:function(data){
		            			$('#dlgSetBag').html(data);
		            			/////start focus bag
		            			var textboxes_bag = $("input.inputkey_bag");
	    						$("input.inputkey_bag").eq(0).focus();
	    						textboxes_len=textboxes_bag.length;
	    						nextBox=0;
	    						$(".inputkey_bag").each(function(){
	    							$(this).keypress(function(evt){
	    								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	    						        if(key == 13){
	    							        evt.stopPropagation();
	    							        evt.preventDefault();
	    							        currentBoxNumber = textboxes_bag.index(this);
	    							        if (textboxes_bag[currentBoxNumber+1] != null) {
	    							        	nextBox = textboxes_bag[currentBoxNumber+1];			    				    					
	    				    					nextBox.focus();
	    							        }else{
	    							        	$('.ui-dialog-buttonpane button:first').focus();
		    							    }
	    							        return false;
	    						        }
	    							});
	    						});
		            			/////end focus bag
		            		}
		            	});
		            	return false;
		            },buttons: {
					 	"OK":function(evt){
					 		evt.preventDefault();
					 		evt.stopPropagation();
					 		
					 		//*WR 21092015 recheck unlock key bill manual
					 		getLockStatus();
					 		//*WR 21092015 recheck unlock key bill manual
					 		
					 		var arr_bag = new Array();
					 		$chk_over_bag_qty='N';
	            			$('.bag_items').each(function(){
	            				var bag_id=$(this).attr('id');
	            				var qty=$('#' +bag_id).val();
	            				qty=parseInt(qty);
	            				if(qty>99){
	            					$chk_over_bag_qty='Y';
	            					$('#' +bag_id).focus();
	            					return false;
	            				}
	            				
	            				//*WR19082015
	            				if(!isNaN(qty) && qty!==''){
	            					$chk_none_item='N';
		            				var arr_bag_id=bag_id.split('_');
		            				var bag_set=arr_bag_id[1] + ':'  + $('#' +bag_id).val();		            				
		            				arr_bag.push(bag_set);	
	            				}	            				
	            			});//each
	            			
	            			if( $chk_none_item=='Y'){
	            				jAlert('PLEASE SPECIFY QUANTITY BAG (OR KEY 0 QTY) ','WARNING!',function(){
									return false;
				    			});
	            				return false;
	            			}
	            			
	            			if($chk_over_bag_qty=='Y'){
	            				jAlert('??????????????????????????????????????????????????????????????? 99 ','WARNING!',function(){
									return false;
				    			});
	            				return false;
	            			}
	            			var bag_list='';
	            			for(i=0;i<arr_bag.length;i++){
            					bag_list+= arr_bag[i]+"#";
            				}   
	            			//alert(bag_list);
	            			var status_no=$('#csh_status_no').val();
	            			$.ajax({
	            				type:'post',
	            				url:'/sales/accessory/setbagtotemp',
	            				cache:false,
	            				data:{
	            					items:bag_list,
	            					rnd:Math.random()
	            				},success:function(){
	            					//*WR24042014								            					
							    	var chk_walkin=$('#csh_member_type').html();
							    	if(chk_walkin!=="WALK IN" && (status_no=='00' || status_no=='02' || status_no=='04')){							    		
							    		if(prefix_member_no_idcard=='ID' && $("#csh_lock_status").val()=='Y'){
							    			ccs_return_from("checkSaleMan"); 	
							    			//alertReturnIdcard();
							    		}else{
							    			conFirmMemberID();
							    		}							    		
							    		//checkSaleMan();
	        						}else{
	        							checkSaleMan();
	        						}
	            					
	            				}
	            			});
					 		
					 		$('#dlgSetBag').dialog('close');
							return false;
					 	},"CANCEL":function(evt){
					 		evt.preventDefault();
					 		evt.stopPropagation();
					 		//*WR19082015 need to key form set bag
	            			if( $chk_none_item=='Y'){
	            				jAlert('?????????????????????????????????????????????????????????????????????????????? 1 ?????????????????? (?????????????????????????????????????????????????????? 0 ?????????) ','WARNING!',function(){
									return false;
				    			});
	            				return false;
	            			}
					 		//*WR24042014
					    	var chk_walkin=$('#csh_member_type').html();
					    	var chk_status_no=$('#csh_status_no').val();												    	
					    	if(chk_walkin!=="WALK IN" && (status_no=='00' || status_no=='02' || status_no=='04')){					    		
					    		if(prefix_member_no_idcard=='ID' && $("#csh_lock_status").val()=='Y'){
					    			ccs_return_from("checkSaleMan"); 
					    			//alertReturnIdcard();
					    		}else{
					    			conFirmMemberID();
					    		}					    		
					    		//checkSaleMan();
    						}else{
    							checkSaleMan();
    						}
					 		$('#dlgSetBag').dialog('close');
							return false;
					 	}
	    		    },
	    		    beforeClose:function(){
	    		    	//*WR19082015 need to key form set bag
            			if( $chk_none_item=='Y'){
            				jAlert('?????????????????????????????????????????????????????????????????????????????? 1 ?????????????????? (?????????????????????????????????????????????????????? 0 ?????????) ','WARNING!',function(){
								return false;
			    			});
            				return false;
            			}
	    		    },
		            close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();
    						//*WR24042014
					    	var chk_walkin=$('#csh_member_type').html();
					    	if(chk_walkin!=="WALK IN" && (status_no=='00' || status_no=='02' || status_no=='04')){					    		
					    		if(prefix_member_no_idcard=='ID' && $("#csh_lock_status").val()=='Y'){
					    			ccs_return_from("checkSaleMan"); 
					    			//alertReturnIdcard();
					    		}else{
					    			conFirmMemberID();
					    		}					    		
					    		//checkSaleMan();
    						}else{
    							checkSaleMan();
    						}
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
    						evt.preventDefault();
    						//*WR24042014
					    	var chk_walkin=$('#csh_member_type').html();
					    	if(chk_walkin!=="WALK IN" && (status_no=='00' || status_no=='02' || status_no=='04')){					    		
					    		if(prefix_member_no_idcard=='ID' && $("#csh_lock_status").val()=='Y'){
					    			ccs_return_from("checkSaleMan"); 
					    			//alertReturnIdcard();
					    		}else{
					    			conFirmMemberID();
					    		}					    		
					    		//checkSaleMan();
    						}else{
    							checkSaleMan();
    						}
						}	
		            	$(this).remove();
		            }
	        });		    
       //-------------- SET BAG 25092013-------------------------------		
		
	}//func
	
	function chkBeforeConfirmPayment(){
		/**
		 * @desc
		 * @param
		 * @create 30032017
		 * @return
		 */
		//*WR21022017 for new mem OPKTC300
		var status_no=$('#csh_status_no').val();
		var application_id=$('#csh_application_id').val();
		var credit_no_value=$('#txt_credit_no').val();
		//alert(status_no + "::" + application_id + "::" + credit_no_value);
		if(status_no=='01' && application_id=='OPKTC300'){									
			var arr_ktc300 = ["430445","439137", "456723","493113","456724","493112","451347","523910",
			           "540716","540605","540604","543420","356342"];
			var chk_ktc300=credit_no_value.substring(0,6);
			if(jQuery.inArray(chk_ktc300, arr_ktc300) == -1) {											
				jAlert('??????????????????????????????????????????????????????????????????????????? KTC ???????????????????????? 6 ????????????\n??????????????????????????????????????? OPKTC300 ????????????????????????','WARNING!',function(r){
					if(r){
						$("#txt_credit_no_value").focus();
						return false;
					}	
				});									    
				return false;
			} 
		}
		
		//*WR01072014 SELECT ONE CASE PAYMENT ONLY								
		var chk_txt_credit=$("#txt_credit").val();
		chk_txt_credit=$.trim(chk_txt_credit);																
		var chk_txt_cash=$("#txt_cash").val();
		chk_txt_cash=$.trim(chk_txt_cash);								
		chk_txt_credit=parseFloat(chk_txt_credit);
		chk_txt_cash=parseFloat(chk_txt_cash);	
		//alert(chk_txt_cash + " " + chk_txt_credit);
		if(chk_txt_cash>0 && chk_txt_credit>0 ){
        	jAlert('Please select only one payment method.Please check and try again.','WARNING!',function(){
				$("#txt_cash").focus();
				return false;
			});
        	$("#btn_payment_confirm").removeClass("ui-state-focus ui-state-hover ui-state-active");
	    	return false;
	    }	
		//*WR01072014 SELECT ONE CASE PAYMENT ONLY
		
		var chk_net_curr=parseFloat($("#txt_net").val());								
        if(chk_net_curr!=0){
        	$("#btn_payment_confirm").removeClass("ui-state-focus ui-state-hover ui-state-active");
        	jAlert('Incomplete net purchase amount. Please check and try again.','WARNING!',function(){
				$("#txt_cash").focus();
			});
	    	return false;
	    }else if(chk_txt_cash>999999){
	    	$("#btn_payment_confirm").removeClass("ui-state-focus ui-state-hover ui-state-active");
        	jAlert('Overpayment! Please check and try again.','WARNING!',function(){
				$("#txt_cash").focus();
			});
	    	return false;
	    }else{
	    	var csh_bill_manual_no=$('#csh_bill_manual_no').val();
	 		var csh_ticket_no=$('#csh_ticket_no').val();
	 		if(csh_bill_manual_no.length>0){
	 			jConfirm('???????????????????????????????????????????????????????????????????????????????????????\n??????????????????????????? : ' + csh_bill_manual_no +
	 					'\n?????????????????? TICKET : ' + csh_ticket_no +
	 					'\n??????????????????????????????','CONFIRM MESSAGE', function(r){
	 		        if(r){
	 		        	setBag2Temp();
	 		        }else{
	 		        	return false;
	 			     }
	 		        return false;
	 			});	
	 		}else{
	 			setBag2Temp();
	 		}
	    	//original setbag here	      
	    }
	}//func
	
	function paymentBillOrg(status_no){
		/**
		*@desc last modify : 31052017
		*@param String status_no :
		*@return void
		*/	
		
		if(status_no=='') return false;
		//*WR23062014
		var status_couponpromo=$('#status_couponpromo').val();
		var coupon_code="";
		if(status_couponpromo=='Y'){
			coupon_code=$('#csh_promo_code').val();
		}
		var cn_amount=$("#cn_amount").val();
		var dialogOpts_payment = {
				autoOpen: false,
				width:'50%',
				height:'auto',		
				modal:true,
				stack:true,				
				position: [250,110],
				resizable:true,				
				closeOnEscape:false,				
				title:'<span class="ui-icon ui-icon-cart"></span>',
				open: function(){ 
						$(this).parent().find('.ui-dialog-titlebar').append('PAYMENT');
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#b7b7e5","color":"#000000"});					
						$("#dlgPayment").empty();
						//*WR23062014 serious
						$("#dlgPayment").load("/sales/cashier/paycase?actions=winpayment&status_no="+status_no+"&cn_amount="+cn_amount+ "&coupon_code=" + coupon_code + "&now="+Math.random(),
						function(){
							//Arrow key
							var maxControl=3;
							var at=new Array();
								at[0]="#txt_redeem_point";
							  	at[1]="#txt_voucher";
							  	at[2]="#txt_cash";
							  	at[3]="#txt_cash_2";
						  	var ac=new Array();
						  	    ac[0]="#btn_payment_alipay";
							  	ac[1]="#btn_payment_credit";
							  	ac[2]="#btn_vat_total";
								ac[3]="#btn_payment_confirm";
								ac[4]="#btn_payment_cancel";
								
							var at_index=3;//2
							var ac_index=0;
							var maxButton=3;
							var minButton=0;
							$('#dlgPayment').keydown(function (e) {
								  var keyCode = e.keyCode || e.which,
								      arrow = {esc:27,left: 37, up: 38, right: 39, down: 40 };
								  switch (keyCode) {
								  	case arrow.esc:
									break;
								    case arrow.left:
									     ac_index=ac_index-1;
								      	 if(ac_index<=minButton){
								      	 	ac_index=minButton;
								      	 }
									     $(ac[ac_index]).focus();
									      	 
								    break;
								    case arrow.up:
								    	at_index=at_index-1;
								    	if(at_index<0){
								      	 	at_index=0;
								      	 }
								    	//alert(at_index);
									    $(at[at_index]).focus();
									    ac_index=0;//clear position
								    break;
								    case arrow.right:
								    	ac_index=ac_index+1;
								    	if(ac_index>=maxButton){
								      	 	ac_index=maxButton;
								      	 }
									    $(ac[ac_index]).focus();
										 
								    break;
								    case arrow.down:
								    	at_index=at_index+1;
								      	if(at_index>2){
								      	 	at_index=3;//2
								      	}
								      	//alert(at_index);
									    $(at[at_index]).focus();
									    ac_index=0;//clear position
								    break;
								  }
								});					
							//test arrow key
							//$("#txt_cash").ForceNumericOnly();
							//init button style
							$(".btn_cmd_brows_2").button().css({width:'70',height:'52'});							
							$('#btn_payment_confirm').button({
						        icons: { primary: "btn_payment_confirm"}
						    });

							$('#btn_payment_cancel').button({
						        icons: { primary: "btn_payment_cancel"}
						    });
							
							$('#btn_payment_alipay').button({
						        icons: { primary: "btn_payment_alipay"}
						    });

							$('#btn_payment_credit').button({
						        icons: { primary: "btn_payment_credit"}
						    });
							
							$('#btn_vat_total').button({
						        icons: { primary: "btn_vat_total"}
						    });					
							//*WR05032015
							$('#btn_key_manual').button({
						        icons: {primary: "btn_key_manual"}
						    });
							
							var application_id=jQuery.trim($("#csh_application_id").val());
							
							var net_total_payment="";
							var net_total_payment_2=$("#txt_netvt_2").val();
							
							var csh_register_free=jQuery.trim($("#csh_register_free").val());
							
							//????????????????????????????????????????????????????????????????????? *** 07022012 ????????????????????????????????? flow ???????????????????????????????????????????????????????????????????????????????????? premium
							if(csh_register_free=='Y' && parseInt($("#txt_netvt").val())==0){
								net_total_payment="0.00";
							}else{
								net_total_payment=$("#txt_netvt").val();
							}
							$("#net_total_payment").html(net_total_payment);							
							$("#txt_net").val(net_total_payment);	
							
							$("#net_total_payment_2").html(net_total_payment_2);
							
							
							$(this).find('div,select, input, textarea').blur();
							
							calPaid();//*WR23062014
							$(this).find("#txt_cash").focus();
							
							$("#txt_cash").keypress( function(evt) {	
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 								
								  if (key == 46 || key == 8 || key == 37 || key == 39) {
									  return true;
								  }else {
									    if (key == 13) {
									    	 var txt_cash=jQuery.trim($("#txt_cash").val());
									            if(txt_cash=='') return false;
									            calPaid();
									            setTimeout(function(){
									            	var chk_net_curr=parseFloat($("#txt_net").val());								
									            	if(chk_net_curr!=0){
									            		$("#txt_cash_2").focus();
									            	}else{
									            		$("#btn_payment_confirm").focus();
									            	}
									            	
									            },800);
									        return false;
									    }else if (key < 48 || key > 57) {
									    	evt.preventDefault();	
									    }else{
									    	var $this = $(this);
										    $this.val($this.val().replace(/[^\d.]/g, ''));
										    if (($this.val().indexOf('.') != -1) && ($this.val().substring($this.val().indexOf('.')).length > 2)) {										    	
										    	$this.val($this.val().substr(0,$this.val().length-1));
										    }
										 
									    }
									    
								   }
							});//keypress

						    $("#txt_cash").focusout(function(){
							    var txt_cash_chk=parseFloat($("#txt_cash").val());
						    	if(isNaN(txt_cash_chk)){
						        	return false; 
						    	}
						    	 calPaid();           
						         return false;
							});//focusout
						    
						    
						    $("#txt_cash_2").keypress( function(evt) {	
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 								
								  if (key == 46 || key == 8 || key == 37 || key == 39) {
									  return true;
								  }else {
									    if (key == 13) {
									    	 var txt_cash_2=jQuery.trim($("#txt_cash_2").val());
									    	 
									            if(txt_cash_2=='') return false;
									            calPaid();									            
									            setTimeout(function(){
									            	var chk_net_curr=parseFloat($("#txt_net").val());								
									            	if(chk_net_curr!=0){
									            		$("#txt_cash_2").focus();
									            	}else{
									            		$("#btn_payment_confirm").focus();
									            	}
									            },800);
									        return false;
									    }else if (key < 48 || key > 57) {
									    	evt.preventDefault();	
									    }else{
									    	var $this = $(this);
										    $this.val($this.val().replace(/[^\d.]/g, ''));
										    if (($this.val().indexOf('.') != -1) && ($this.val().substring($this.val().indexOf('.')).length > 2)) {										    	
										    	$this.val($this.val().substr(0,$this.val().length-1));
										    }
										 
									    }
									    
								   }
							});//keypress

						    $("#txt_cash_2").focusout(function(){
							    var txt_cash_chk=parseFloat($("#txt_cash_2").val());
						    	if(isNaN(txt_cash_chk)){
						        	return false; 
						    	}
						    	 calPaid();           
						         return false;
							});//focusout
						    
						    
						    
						    $("#txt_voucher").keypress( function(evt) {	
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								  if (key == 46 || key == 8 || key == 37 || key == 39) {
									  return true;
								  }else {
									    if (key == 13) {
									    	evt.preventDefault();
								            var voucher_cash=jQuery.trim($("#txt_voucher").val());
								            if(voucher_cash=='') return false;
									         $("#txt_voucher_cash").val(voucher_cash);
									         calPaid();
									         $("#txt_cash").focus();
								        	 return false;
									    }else if (key < 48 || key > 57) {
									    	evt.preventDefault();	
									    }else{
									    	var $this = $(this);
										    $this.val($this.val().replace(/[^\d.]/g, ''));
										    if (($this.val().indexOf('.') != -1) && ($this.val().substring($this.val().indexOf('.')).length > 2)) {
										    	$this.val($this.val().substr(0,$this.val().length-1));
										    }										  
									    }
									    
								   }
							});//keypress				
						    
						    $("#btn_payment_confirm").click( function(e){
								e.preventDefault();								
								//*WR30032017 check need to pay with alipay
								$.ajax({
									type:'post',
									url:'/sales/accessory/chkpaywithalipay',
									data:{
										rnd:Math.random()
									},success:function(data){		
										
										if(data=='Y'){												
											if($('#payment_channel').val()!="Alipay"){
												jAlert('????????????????????????????????????(OX07140317)???????????????????????????????????????????????? Alipay ????????????????????????','WARNING!',function(){														
													return false;
								    			});
												return false;
											}else{
												chkBeforeConfirmPayment();
											}
										}else{
											
											chkBeforeConfirmPayment();
											
										}										
									}
									
								});
								return false;
							});//click						
							
							$("#btn_payment_confirm").keypress( function(evt) {
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						        if(key == 13) {
						        	evt.preventDefault();
							        $("#btn_payment_confirm").trigger("click");
						        	return false;
						        }
							});//keypress

							////////////////////start cancel //////////////////
							$("#btn_payment_cancel").click( function(e) {
								e.preventDefault();
								e.stopPropagation();
					        	jConfirm('Do you want to cancel this payment?','CONFIRM MESSAGE', function(r){
								        if(r){
								        	rePaymentBill();
								        	$('#dlgPayment').dialog('close');
											return false;
								        }
					        	});
						       return false;
							});//click

							$("#btn_payment_cancel").keypress( function(evt) {
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						        if(key == 13) {
						        	rePaymentBill();
						        	$('#dlgPayment').dialog('close');
						        	return false;
						        }
							});//keypress

							////////////////////end cancel //////////////////
							
							$("#btn_payment_alipay").click( function(evt){
								evt.preventDefault();
								var member_type=$('#csh_member_type').html();
								if(member_type!=='WALK IN'){
									jAlert('????????????????????????????????????????????? Alipay ????????????????????????????????????????????? WALK IN ????????????????????????', 'WARNING!',function(){									    	
								    	return false;
								    });	
								    return false;
								}else{
									
									var online_mode=false;
						        	if(!online_mode){		
						        		$('#txt_cash').val('');
						        		calPaid();
						        		dlgAlipay();
						        		return false;
							        }	
									
								}
					        						        							       
							});

							$("#btn_payment_credit").click( function(evt){
								//check if offline or online mode			
								evt.preventDefault();
					        	var online_mode=false;
					        	if(!online_mode){		
					        		$('#txt_cash').val('');//WR20052013 ??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? ??? ??????????????????
					        		calPaid();
						        	dlgCredit();
					        		return false;
						        }							       
							});//click							
							
							$("#btn_vat_total").click(function(evt){
								evt.preventDefault();
								$('#btnVT').trigger('click');
								return false;
							});
							
							$("#btn_vat_total").keypress( function(evt) {
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						        if(key == 13) {
							        evt.preventDefault();
							        $('#btnVT').trigger('click');
						        	return false;
						        }
							});//keypress		
							
							$("#btn_key_manual").click(function(evt){
								evt.preventDefault();
								if($("#dlgFrmKeyManual").dialog("isOpen")===true) {
					            	return false;
					            }//if
								$("<div id='dlgFrmKeyManual'></div>").dialog({
							       	   autoOpen:true,
							  				width:'65%',		
							  				height:'auto',	
							  				modal:true,
							  				resizable:true,							  			
							  				position:"center",
							  				showOpt: {direction: 'up'},		
							  				closeOnEscape: false,
							  				title:"<span class='ui-icon ui-icon-person'></span>?????????????????????????????????????????????????????????????????????????????????",
							  				open: function(){ 
												$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
																																"font-size":"24px","color":"#000000",
																																"padding":"5 0.1em 0 0.1em"});
												$("#dlgConFirmMemberID").html('');
								            	$(this).dialog('widget')
										            .find('.ui-dialog-titlebar')
										            .removeClass('ui-corner-all')
										            .addClass('ui-corner-top');
								            	$(this).dialog("widget").find(".ui-button-text")
							                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
								            	$(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
												   return false;
												});
								            	var val_bill_manual_no=$('#csh_bill_manual_no').val();
										 		var val_ticket_no=$('#csh_ticket_no').val();
						        				$.ajax({
						        					type:'post',
						        					url:'/sales/accessory/formkeymanual',
						        					data:{
						        						str_bill_manual_no:val_bill_manual_no,
												 		str_ticket_no:val_ticket_no,
						        						rnd:Math.random()
						        					},success:function(data){
						        						$("#dlgFrmKeyManual").html(data);
						        						$('#fkm1').focus();	
						        					}
						        				});//end ajax									   		
							  				},
							  				buttons: [ 			
								    	                { 
									    	                text: "Confirm",
									    	                id:"btnConfirmKeyManual",
									    	                class: 'ui-btndlgpos', 
									    	                click: function(evt){ 
									    	                	evt.preventDefault();
														 		evt.stopPropagation();	
														 		var bill_manual_no="";
														 		var ticket_no="";
														 		$(':text.frmKeymanualFixedTextbox').each(function(){														 			
														 			bill_manual_no=bill_manual_no + "" + $(this).val();
														 			if($(this).index()==3){
														 				bill_manual_no=bill_manual_no + "/";
														 			}
														 		});														 		
														 		if(bill_manual_no!=""){
														 			bill_manual_no="OP" + bill_manual_no;
														 		}														 		
														 		$(':text.frmKeymanualFixedTextboxTicket').each(function(){											 			
														 			ticket_no=ticket_no + "" + $(this).val();
														 		});
														 		$('#csh_bill_manual_no').val(bill_manual_no);
														 		$('#csh_ticket_no').val(ticket_no);
														 		
														 		//*WR20072015 check bill manual no is exist
														 		$.ajax({
														 			type:'post',
														 			url:'/sales/cashier/checkbillmanualexist',
														 			data:{
														 				bill_manual_no:bill_manual_no,
														 				rnd:Math.random()
														 			},success:function(data){
														 				if(data=='Y'){
														 					jAlert('???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
														 						$('.frmKeymanualFixedTextbox').val('');
																		 		$('.frmKeymanualFixedTextboxTicket').val('');
																		 		$('#csh_bill_manual_no').val('');
																		 		$('#csh_ticket_no').val('');
																				return false;
																			});	
														 				}else{
														 					$('#dlgFrmKeyManual').dialog('close');
														 				}
														 			}
														 		});
														 		
														 		//$('#dlgFrmKeyManual').dialog('close');									 		
									    	                }
								    	                },
								    	                { 
									    	                text: "Cancel",
									    	                id:"btnCanCelConfirmKeyManual",
									    	                class: 'ui-btndlgpos', 
									    	                click: function(evt){ 
									    	                	evt.preventDefault();
														 		evt.stopPropagation();	
														 		jConfirm('???????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????','CONFIRM MESSAGE', function(r){
															        if(r){
															        	$('.frmKeymanualFixedTextbox').val('');
																 		$('.frmKeymanualFixedTextboxTicket').val('');
																 		$('#csh_bill_manual_no').val('');
																 		$('#csh_ticket_no').val('');
																 		$('#dlgFrmKeyManual').dialog('close');
															        }
																});
														 										 		
									    	                }
								    	                }
								    	  ],
							  				close:function(evt){
							  					$("#dlgFrmKeyManual").remove();
								            	$("#dlgFrmKeyManual").dialog("destroy");	  					
								   			}
							          });								
								return false;
							});
							
							$("#btn_key_manual").keypress( function(evt) {
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						        if(key == 13) {
							        evt.preventDefault();
							        $('#btn_key_manual').trigger('click');
						        	return false;
						        }
							});//keypress		

							$("#btn_payment_credit").keypress( function(evt) {
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						        if(key == 13) {
							        evt.preventDefault();
							        calPaid();
									dlgCredit();
						        	return false;
						        }
							});//keypress
							////////////////////end cancel //////////////////

						});
				},
				close: function(evt,ui){
						clearFormPayment();					
						$('#dlgPayment').dialog('destroy');
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){						
							rePaymentBill();	
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){						
							rePaymentBill();
						}
						$("#btn_cal_promotion").removeAttr("disabled");
						$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
				 }
			};	
			$('#dlgPayment').dialog('destroy');
			$('#dlgPayment').dialog(dialogOpts_payment);			
			$('#dlgPayment').dialog('open');
			return false;
		//end payment
	}//func
	
	function cancelBill(){
		/**
		*@desc
		*@return
		*/
		var opts={
					type:"post",
					url:"/sales/cashier/ajax",
					cache: false,
					data:{
						actions:"cancelBill",
						now:Math.random()
					},
					success:function(data){
						if(data=='0'){
							jAlert('???????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								return false;
							});	
						}else{						
							$('#btnCancle').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
							initFormCashier();
							getCshTemp('C');		
						}				
					}
				};
		$.ajax(opts);
	}//func
	function getProduct(){
		/**
		*@desc
		*@return
		*/	
		var csh_promo_code=$('#csh_promo_code').val();//*WR 08042015
		var product_id=$.trim($('#csh_product_id').val());
		var quantity=$.trim($('#csh_quantity').val());
		var status_no=$.trim($('#csh_status_no').val());
		if(product_id.length==0){
			jAlert('PLEASE SPECIFY PRODUCT CODE.', 'WARNING!',function(){
				return false;
			});	
			$('#csh_product_id').attr('disabled','');//lock for duplicate enter key
			$("#csh_product_id").focus();
			return false;
		}
		var opts={
				type:'post',
				url:'/sales/cashier/product',	
				data:{
					promo_code:csh_promo_code,
					product_id:product_id,
					quantity:quantity,
					status_no:status_no,
					action:"checkproductexist"
				},
				success:function(data){		
					if(data==1){
						jAlert('THIS PRODUCT CODE IS NOT AVAILABLE.PLEASE CHECK AND TRY AGAIN.', 'WARNING!',function(){
							if($('#csh_gap_promotion').val()=='Y'){
								//*WR22072014
								if($('#csh_promo_code').val()=='50BTO1P'){
									endOfProOther();//uncomment on 05012013
								}								
							}
							initFieldPromt();
							return false;
						});					
						return false;
					}else if(data==2){
						
						var pdt_lost_id=$("#csh_product_id").val();
						if(pdt_lost_id=='9900730'){
							$("#csh_product_id").val('');
							cardLostStock();//check card lost stock							
						}else{
							
							jAlert('THIS STOCK IS NOT ENOUGH.', 'WARNING!',function(){			
								if($('#csh_gap_promotion').val()=='Y'){								
									//*WR22072014
									if($('#csh_promo_code').val()=='50BTO1P'){
										endOfProOther();//uncomment on 05012013
									}		
								}
								initFieldPromt();		
								return false;							
							});	
							
						}
						return false;						
					}else if(data==3){
						jAlert('THIS PRODUCT CODE WAS LOCKED.', 'WARNING!',function(){	
							if($('#csh_gap_promotion').val()=='Y'){
								//endOfProOther();								
							}
							initFieldPromt();		
							return false;							
						});			
						return false;				
					}else if(data==4){
						jAlert('TESTER PRODUCT NOT FOR SALE', 'WARNING!',function(){								
							initFieldPromt();		
							return false;							
						});			
						return false;		
					}else if(data==5){
						jAlert('?????????????????? ???????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){								
							initFieldPromt();		
							return false;							
						});			
						return false;		
					}else if(data==6){
						jAlert('CAN NOT PURCHASE MORE THAN THE SPECIFILED QUANITY NUMBER.', 'WARNING!',function(){								
							initFieldPromt();		
							return false;							
						});			
						return false;		
					}else{
						$('#csh_product_id').val(data);						
						var member_no=$.trim($("#csh_member_no").val());
						var play_main_pro=$.trim($("#csh_play_main_promotion").val());
						var play_last_pro=$.trim($("#csh_play_last_promotion").val());
						var csh_gap_promotion=$.trim($('#csh_gap_promotion').val());
						var promo_code=$("#csh_promo_code").val();	
						var start_baht=$("#csh_start_baht").val();
						var end_baht=$("#csh_end_baht").val();
						var buy_type=$("#csh_buy_type").val();
	    				var buy_status=$("#csh_buy_status").val();	
						if(member_no!='' && play_main_pro=='N' && play_last_pro=='N' && csh_gap_promotion!='Y' &&  $('#csh_trn_diary2_sl').val()=='Y'){							
							if(start_baht!='' && end_baht!=''){
								//*WR17122014
								var promo_tp=$('#csh_promo_tp').val();
								if(promo_tp=='NEWBIRTH'){
									setCshTemp();	
								}else{
									//check ???????????????????????????????????????????????????????????????
									var opts_amt={
													type:'post',
													url:'/sales/member/chkamtpro',
													data:{
														promo_code:promo_code,
														start_baht:start_baht,
														end_baht:end_baht,
														buy_type:buy_type,
														buy_status:buy_status,
														product_id:product_id,
														quantity:quantity,
														rnd:Math.random()
													},success:function(data){													
														var arr_chk_data=data.split('#');
														//flag Y or N#buy_status L or G # new amount
														if(arr_chk_data[0]=='N'){
															var msg_error_amount="";
															if(arr_chk_data[1]=='L'){
																msg_error_amount="?????????????????????????????????????????????????????? "+end_baht;
															}else if(arr_chk_data[1]=='G'){
																msg_error_amount="???????????????????????????????????????????????????????????? "+start_baht+" ??????????????????";
															}
															jAlert(msg_error_amount+' Please check and try again.', 'WARNING!',function(){						
																initFieldPromt();		
																return false;							
															});			
															return false;
														}else{
															setCshTemp();	
															//setPromotionTemp();//for support table promotion temp of joke
														}
													}
											};
									$.ajax(opts_amt);
								}//end if 20122013
							}else{								
								setCshTemp();			
							}							
						}else if(member_no=='' && play_main_pro=='N' && play_last_pro=='N' && csh_gap_promotion!='Y' &&  $('#csh_trn_diary2_sl').val()=='Y'){	
							//case non member for support mobile coupon non member
							setCshTemp();	
						}else{	
							if($('#csh_member_vip').val()=='1' || $("#csh_status_no").val()=='03' || $("#csh_status_no").val()=='05'){								
								setCshTemp();	
							}else if(csh_gap_promotion=='Y'){
								// other promotion ???????????????????????????????????????????????????
								setGapValTemp();
							}else{								
								if($("#status_pro").val()=='0'){										
									
									//*WR30032017 check product not play in pro									 
									   if(promo_code=='OX02460217' || promo_code=='OX02460217_2'){
										   $.ajax({
											   type:'post',
											   url:'/sales/accessory/chkcoproduct',
											   data:{
												   product_id:product_id,
												   rnd:Math.random()
											   },success:function(data){												   
												   if(data=='Y'){
													   setCshValTemp();
												       hotchkkey($("#csh_product_id").val(),$("#csh_quantity").val());
												       return false;
												   }else{
													   jAlert('?????????????????? ???????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){								
															initFieldPromt();		
															return false;							
														});			
														return false;	
												   }
											   }
										   });
										   return false;										   
									   }else{									
										   setCshValTemp();
									       hotchkkey($("#csh_product_id").val(),$("#csh_quantity").val());
									       return false;
										}
									
								
//									   setCshValTemp();
//								       hotchkkey($("#csh_product_id").val(),$("#csh_quantity").val());
//								       return false;
								}else{										
									var csh_first_limited=$("#csh_first_limited").val();
									if($('#csh_status_no').val()=='02' && parseFloat(csh_first_limited)>0){			
										var csh_first_percent=$('#csh_first_percent').val();
										var csh_add_first_percent=$('#csh_add_first_percent').val();
										var csh_product_id=$('#csh_product_id').val();
										var csh_quantity=$('#csh_quantity').val();
										var opts_chk_first_lmt={
													type:'post',
													url:'/sales/member/chkfirstlmt',
													data:{
														first_limited:csh_first_limited,
														first_percent:csh_first_percent,
														add_first_percent:csh_add_first_percent,
														product_id:csh_product_id,
														quantity:csh_quantity,
														rnd:Math.random()
													},success:function(data){
														if(data=='Y'){
															setCshValTemp();//add temp of joke
															setTimeout(function(){
																addnormal(csh_product_id,csh_quantity);
															 },400);
														}else{
															jAlert('?????????????????????????????????????????????????????? ' + parseFloat(csh_first_limited).toFixed(2) + ' Please check and try again.', 'WARNING!',function(){						
																initFieldPromt();		
																return false;							
															});	
														}
													}
											};
										//$.ajax(opts_chk_first_lmt);			
										//////////////////// check status_no for bill 02 ////////////////////										
										if(status_no==''){
											jAlert('Document status is invalid. Please cancel the sale and re-open the bill.', 'WARNING!',function(){
												$("#csh_product_id").focus();
												return false;
											});	
											return false;
										}else{
											$.ajax(opts_chk_first_lmt);
										}
										//////////////////// check status_no for bill 02 ////////////////////
									}else{									
										//if bill 02 for newmember OPPN300
										//////////////////// check status_no for bill 02 ////////////////////															
										if(status_no==''){
											jAlert('Document status is invalid. Please cancel the sale and re-open the bill.', 'WARNING!',function(){
												$("#csh_product_id").focus();
												return false;
											});	
											return false;
										}else{
											 setCshValTemp();//add temp of joke
											 setTimeout(function(){
												 addnormal(product_id,quantity);
											 },400);
										}
										//////////////////// check status_no for bill 02 ////////////////////
										
									}
									
							    }
							}
						}
					
					}
					return false;
				},
			error:function (xhr, ajaxOptions, thrownError){
                    alert(xhr.status);
                    alert(thrownError);
                }   
			};	
		$.ajax(opts);	
	}//func

	function initFieldOpenBill(){
		/**
		*@desc
		*@return
		*/
		$('#csh_refer_doc_no').val('');//01072013
		$('#csh_point_total').html('');		
		$('#csh_expire_transfer_point_show').html('');		
		$('#csh_percent_discount').html('');		
		$('#other_promotion_title').html('');
		$('#other_promotion_cmd').html('');
		$('#csh_promo_code').val('');
		$('#csh_member_type').html('');
		$('#csh_product_id').val('');
		$('#csh_product_id').disable();
		$('#csh_quantity').val('1');
		$('#csh_quantity').disable();
		$('#csh_member_no').val('');
		$('#csh_member_no').enable();
		$('#csh_member_no').focus();
		$('#csh_application_type').val('');//NEW=???????????????????????????, ALL=?????????????????????
		$('#csh_card_type').val('');//MBC=??????????????????????????????, IDC=?????????????????????????????????????????????????????????
		return false;
	}//func

	function initFieldForDialog(){
		/**
		*@desc
		*@return false
		*/
		$('#csh_member_no').disable().blur();
		$('#csh_quantity').disable().blur();
		$('#csh_product_id').disable().blur();
		return false;
	}//func

	function initFieldPromt(){
		/**
		*@desc
		*@return false
		*/
		$('#csh_member_no').disable();
		$('#csh_quantity').val('1').enable();
		$('#csh_product_id').val('').enable().focus();		
	}//func

	function setPromotionTemp(){
		/**
		*@ name
		*@ desc
		*@ lastmodify :30042012
		*/
		var doc_tp="";//????????????????????????????????????
		if($("#csh_doc_tp_vt").val()!=''){
			doc_tp=$("#csh_doc_tp_vt").val();
		}else{
			doc_tp=$("#csh_doc_tp").val();
		}
		var employee_id=$('#csh_cashier_id').val();
		var member_no=$('#csh_member_no').val();
		var product_id=$('#csh_product_id').val();
		var quantity=$('#csh_quantity').val();
		var status_no=$('#csh_status_no').val();
		var product_status=$('#csh_product_status').val();
		var application_id=$('#csh_application_id').val();
		//for bill card lost
		var card_status=$("#csh_card_status").val();
		var promo_code=$("#csh_promo_code").val();
		var promo_id=$("#csh_promo_id").val();
		var discount_member=$("#csh_discount_member").val();
		var get_point=$("#csh_get_point").val();
		var discount_percent='';
		var member_percent1=$('#csh_percent_discount').html();
			member_percent1=$.trim(member_percent1);		
		var arr_mp1=member_percent1.split('+');
			member_percent1=arr_mp1[0];
		var member_percent2=$('#csh_add_first_percent').val();
		var co_promo_percent='';
		var coupon_percent='';
		if(product_id.length==0){
			jAlert('??????????????????????????? ??????????????????????????????', 'WARNING!',function(){				
				initFieldPromt();
				return false;
			});	
			return false;
		}
		var csh_net=$('#csh_net').val();
		var csh_promo_discount=$('#csh_promo_discount').val();
		var opts={
				type:'post',
				url:'/sales/cashier/setpromotiontemp',
				cache: false,	
				data:{
					employee_id:employee_id,
					member_no:member_no,
					product_id:product_id,
					quantity:quantity,	
					status_no:status_no,
					product_status:product_status,
					doc_tp:doc_tp,
					application_id:application_id,
					card_status:card_status,
					promo_code:promo_code,
					promo_id:promo_id,
					promo_st:'',
					get_point:get_point,
					discount_member:discount_member,
					discount_percent:discount_percent,
					member_percent1:member_percent1,
					member_percent2:member_percent2,
					co_promo_percent:co_promo_percent,
					coupon_percent:coupon_percent,
					net_amt:csh_net,
					discount:csh_promo_discount,
					actions:'set_promtion_tmp'
				},
				success:function(data){
					var arr_data=data.split('#');	
					if(arr_data[0]=='0'){
						jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
							initFieldPromt();
							return false;
						});	
					}else{		
						getPmtTemp();
						initFieldPromt();
					}
				}
			};	
		$.ajax(opts);
	}//func
	
	function endOfProOther(){
		/**
		 * @return null
		 */
		//?????????????????????????????????????????????????????????????????????
		 $('#csh_gap_promotion').val('');
		 $('#other_promotion_title').html('');
		 $('#other_promotion_cmd').html('');
		 $('#csh_promo_discount').val('');
		 $('#csh_bal_discount').val('');
		 $('#csh_promo_amt').val('');
		 $('#csh_promo_amt_type').val('');
		 $('#csh_promo_point').val('');
		 $('#csh_promo_tp').val('');
		 $('#csh_promo_point_to_discount').val('');	
		 $('#csh_product_id').val('');			 
		 $('#csh_web_promo').val('');
		 $('#csh_check_repeat').val('');
		 //get previous promo_code
		 $.ajax({
			 type:'post',
			 url:'/sales/cashier/prevpromo',
			 cache:false,
			 data:{
			 	promo_code:$('#csh_promo_code').val(),
			 	rnd:Math.random()
			 },success:function(data){
				 $('#csh_product_id').val('');
				 $('#csh_promo_code').val(data);
			}
		 });		
	}//func	
	
	// ******************  sms promotion ***************************
	function setSmsProduct(sms_promo_code){
		/**
		 * @desc
		 */
		var opts_smsproduct={
				type:'post',
				url:'/sales/cashier/setsmstemp',
				data:{
					promo_code:sms_promo_code,
					rnd:Math.random()
				},success:function(data){
					var arr_data=data.split('#');
					if(arr_data[0]=='0'){
						jAlert('???????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){							
							return false;
						});	
					}else{
						$('#dlgSmsMobile').dialog('close');
						getCshTemp('N');
						paymentBill('00');
						setTimeout(function(){
							$('#btn_payment_confirm').trigger('click');
						},800);
					}
				}
		};
		$.ajax(opts_smsproduct);
	}//func
	function callSmsPromo(sms_promo_code,sms_mobile,redeem_code,member_tp,idcard){
		/**
		 * @desc
		 */		
		var objResCallSmsPro=null;
		var member_no=$('#csh_member_no').val();
		//*WR20092016
		var member_id_card=idcard;
		//*WR26012016 for member call survey 2016
		var prefix_member_no_idcard=$('#csh_member_no').val();
		prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
		
		if(prefix_member_no_idcard=='ID'){
			//member_no='';
			idcard='';
		}
		
		var opts_smspromo={
				type:'post',
				url:'/sales/cashier/callsmspro',
				data:{
					member_no:member_no,
					sms_promo_code:sms_promo_code,
					sms_mobile:sms_mobile,
					redeem_code:redeem_code,
					idcard:idcard,
					rnd:Math.random()
				},success:function(data){
					objResCallSmsPro=null;
					if(data=='null'){
						jAlert("????????????????????????????????? Please check and try again.", 'WARNING!',function(){
							$('.ui-dialog-content input:first').focus();
							return false;
						});	
						return false;
					}
					var objJson=$.parseJSON(data);
					if(objJson.status=='OK'){
						$('#status_smspromo').val('Y');
						$('#id_smspromo').val(objJson.id);
						$('#id_sms_promo_code').val(sms_promo_code);
						$('#id_sms_mobile').val(sms_mobile);
						$('#id_redeem_code').val(redeem_code);
						 //*WR23012013						
						  if(prefix_member_no_idcard!='ID' && member_tp=='Y'  && objJson.member!=$('#csh_member_no').val()){
							  jAlert("???????????????????????????????????????????????????????????? Please check and try again.", 'WARNING!',function(){
			    				   $("#sms_mobile").focus();
									return false;
								});	
							  return false;
						  }
						  
						//*WR20092016 fix code for mcs promotion						  
						  if(sms_promo_code=='OX31330916'){
							  //call line function 
							  lineApp('OX31330916','Y','',member_id_card,'');
							  $("#dlgSmsMobile").dialog("close");
							  return false;
						  }else if(sms_promo_code=='OX27061116'){
							  $('#status_couponpromo').val('Y');
							  $('#csh_promo_code').val(sms_promo_code);
							  playMstPromotion(sms_promo_code,'1');
							  $("#dlgSmsMobile").dialog("close");
							  return false;
						  }else
						  
		    			  if(parseFloat($('#csh_promo_amt').val())>0){
		    				  $("#dlgSmsMobile").dialog("close");
		    				  initFieldPromt();
		    				  return false;
		    			  }else{
		    				  //set product sms to temp normal
		    				  setSmsProduct(sms_promo_code);
		    			  }
					}else if(objJson.status=='NO'){
						 jAlert(objJson.status_msg, 'WARNING!',function(){
							  //---------------- 25072013 --------------------------------
							 $('#status_smspromo').val('');
							 $('#id_smspromo').val('');
							 $('#id_sms_promo_code').val('');
							 $('#id_sms_mobile').val('');
							 $('#id_redeem_code').val('');			
							 //----------------- 25072013 --------------------------------
		    				   $("#sms_mobile").focus();
								return false;
							});	
						//input another field need
					}
					
				}
		};		
		if(sms_promo_code=='' || (sms_mobile=='' && redeem_code=='')){
			return false;
		}else{
			objResCallSmsPro=$.ajax(opts_smspromo);
			setTimeout(function(){
				if(objResCallSmsPro){
					objResCallSmsPro.abort();
					$('.ui-dialog-content input:first').focus();
				}
			},30000);
		}
		
	}//func
	
	function  showFormSmsMobile(sms_promo_code,promo_tp,member_tp){
		/**
		 * @desc
		 */		
		var opts_dlgFormMobile={
	    	  modal: true,
	    	  title:"???????????????????????????????????????????????????????????????????????????",
	    	  width:'33%',
	    	  height:'auto',
	    	  open:function(){
	    		  $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
  			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0",
  			    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
  			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
  			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
  			    //button style	
  			    $(this).dialog("widget").find(".ui-btndlgpos")
                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});    			    
				    $(".ui-widget-overlay").live('click', function(){				    	
				    	$('.ui-dialog-content input:first').focus();
					}); 				  
				  //$("#sms_mobile").ForceNumericOnly();
				  /*
				$(this).parents(".ui-dialog:first").find('input').keypress(function(e) {									    	
					if (e.keyCode == $.ui.keyCode.ENTER) {
						e.stopImmediatePropagation();				    		
						$('.ui-dialog-buttonpane button:first').click();
						}
				});
				*/
				$('#sms_mobile').keypress(function(evt){
					var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					if(key == 13){
						evt.stopPropagation();
						evt.preventDefault();	
						//alert("key press me!");
						$("#btnMcsSmsMobile").trigger('click');
						//$('.ui-dialog-buttonpane button:first').click();
						return false;
					}
				});
	    	  },	    	  
	    	  buttons: [ 
	    	            { 
	    	                text: "OK",
				  id:"btnMcsSmsMobile",
	    	                class: 'ui-btndlgpos', 
	    	                click: function(evt){ 
	    	                	  evt.preventDefault();	    	                	 
	    		    			  if($("#sms_mobile").length > 0){
	    		    				  var sms_mobile=$('#sms_mobile').val();
	    		    			  }else{
	    		    				  var sms_mobile='';
	    		    			  }
	    		    			  if($("#redeem_code").length > 0){
	    		    				  var redeem_code=$('#redeem_code').val();
	    		    			  }else{
	    		    				  var redeem_code='';
	    		    			  }
	    		    			  
	    		    			  if($("#sms_card_id").length > 0){
	    		    				  var idcard=$('#sms_card_id').val();
	    		    			  }else{
	    		    				  var idcard='';
	    		    			  }
	    		    			  //if(sms_mobile=='') return false;	    
	    		    			  var arr_tp=promo_tp.split(',');	 
//	    		    			  var a=arr_tp.indexOf("T");
//	    		    			  var b=sms_mobile.length;
//	    		    			  alert("index of a is " + a + "\n and length of a is " + b);
	    		    			  if((arr_tp.indexOf("T") != -1) && (sms_mobile.length!=10)){
	    		    				  jAlert('????????????????????????????????????????????????????????????????????????????????? 10 ????????????', 'WARNING!',function(){
					    				   $("#sms_mobile").focus();
											return false;
										});	    				  
	    		    				  return false;
	    		    			  }else{	
								//*WR27012016 for support member call survey 2016
								var prefix_member_no_idcard=$('#csh_member_no').val();
								prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
								if(prefix_member_no_idcard=='ID'){
									var idcard=$('#csh_id_card').val();
								}
	    		    				  callSmsPromo(sms_promo_code,sms_mobile,redeem_code,member_tp,idcard);
	    		    			  }
	    		    			  
	    	                } 
	    	            }
	    	        ],
	    	        
	    	  close:function(){
	    		  $("#dlgSmsMobile").dialog("destroy");
	    	  }
		};
		
		//*WR30012015 clear init openbill when click close dialog Member Call Survey
		var varDlgSmsMobile={
				closeOnEscape:false,
				close:function(evt,ui){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
						evt.stopPropagation();
						evt.preventDefault();
						initFormCashier();
						initFieldOpenBill();
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();
						initFormCashier();
						initFieldOpenBill();
					}	
				}
		};
		
		$('#dlgSmsMobile').html('');
		var url='/sales/cashier/formsmspromobile';
		  $.ajax({
			type:'post',
		    url:url,
		    data:{
		    	inputfield:promo_tp,
		    	rnd:Math.random()
		    },
		    success: function(data) {		    	
		    	$('#dlgSmsMobile').html(data);
				$("#dlgSmsMobile").dialog(opts_dlgFormMobile);
				//$("#dlgSmsMobile").dialog("open");
				$("#dlgSmsMobile").dialog(varDlgSmsMobile);
		    }
		  });
			
	}//func
	// ******************  sms promotion ***************************
	
	function playCoPromo(promo_code,product_id,member_no){
		/**
		 * @des
		 */
		var opts_chkcopromo={
				type:'post',
				url:'/sales/cashier/chkcopromo',
				data:{
					promo_code:promo_code,
					product_id:product_id,
					actions:'50BTO1P',
					rnd:Math.random()
				},success:function(data){
					//return ????????????????????????????????????????????????????????????????????????
					if($.trim(data)!=''){
						var promo_play=$.trim(data);//????????????????????????????????????????????????????????????
						var member_no=$.trim($('#csh_member_no').val());
						/// play co promotion
						var dlgOpts_dlgCopromotion = {
								autoOpen:true,
								modal:true,
								position:'center',
								width:'25%',
								height:250,
								title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????? ????????????????????? 1 ????????????????????? 2 ?????? 50%",
	        					closeOnEscape:true,
	        					open: function(){ 
	        						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
	        						$(this).dialog('widget')
	        				            .find('.ui-dialog-titlebar')
	        				            .removeClass('ui-corner-all')
	        				            .addClass('ui-corner-top');
									    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
									    																				"height":"50px",
										    																			"font-size":"26px","color":"#444",
										    																			"padding":"5"});	
									    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"})
									    // button style		
					    			    $(this).dialog("widget").find(".ui-button")
						                  .css({"padding":"0 .01em 0 .01em","margin":"0 .1em 0 0","font-size":"22px"});
									    
									    $(".ui-widget-overlay").live('click', function(){
									    	$("#co_product_id").focus();
										});
									    
									    $("#co_product_id").focus();
									    $("#btnAddCoProduct").click(function(evt){
									    	evt.preventDefault();
									    	return false;
									    });
									  
									    $(this).parents(".ui-dialog:first").find('input').keypress(function(e) {									    	
									    	if (e.keyCode == $.ui.keyCode.ENTER) {
									    		e.stopImmediatePropagation();
									    		$('.ui-dialog-buttonpane button:first').click();
									          }
									    });
									    
	        					},
	        					buttons:{
	        						"????????????":function(evt){
	        							evt.preventDefault();
	        							$.ajax({
									    	   type:'post',
									    	   url:'/sales/cashier/addcoproduct',
									    	   cache:false,
									    	   //async:true,
									    	   data:{
									    		   promo_code:promo_play,
									    		   product_id:$("#co_product_id").val(),
									    		   member_no:member_no,
									    		   rnd:Math.random()
									    	   },success:function(data){
									    		  var arr_data=data.split('#');
									    		   if(arr_data[0]=='1'){
										    		   $("#co_product_id").val('');
										    		   getPmtTemp();
										    		   $("#dlgCopromotion").dialog("close");
									    		   }else if(arr_data[3]=='0'){
									    			   jAlert('?????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
									    				   $("#co_product_id").focus();
															return false;
														});	
									    		   }else if(arr_data[3]=='1'){
									    			   jAlert('???????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
									    				   $("#co_product_id").focus();
															return false;
														});	
									    		   }else if(arr_data[3]=='2'){
									    			   jAlert('?????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
									    				   $("#co_product_id").select().focus();
															return false;
														});	
									    		   }else if(arr_data[3]=='3'){
									    			   jAlert('??????????????????????????????????????????????????? Lock Please check and try again.', 'WARNING!',function(){
									    				   $("#co_product_id").focus();
															return false;
														});	
									    		   }else if(arr_data[3]=='4'){
									    			   jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
									    				   $("#co_product_id").focus();
															return false;
														});	
									    		   }
									    	   }
									       });	
	        							
	        						}
	        					},
	        					close:function(evt,ui){
	        						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
										evt.stopPropagation();
		        						evt.preventDefault();
		        						initFieldPromt();
									}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
										evt.stopPropagation();
		        						evt.preventDefault();
		        						initFieldPromt();
									}	
	        						
	        					}
	        			};
						$("#dlgCopromotion").dialog("destroy");
						$("#dlgCopromotion").dialog(dlgOpts_dlgCopromotion);
						$("#dlgCopromotion").dialog("open");
						/// play co promotion
					}
				}
				
		};
		$.ajax(opts_chkcopromo);
	}//func

	function setGapValTemp(){
		/**
		*@ name
		*@ desc
		*@ lastmodify :09042012
		*/
		var doc_tp="";//????????????????????????????????????
		if($("#csh_doc_tp_vt").val()!=''){
			doc_tp=$("#csh_doc_tp_vt").val();
		}else{
			doc_tp=$("#csh_doc_tp").val();
		}
		var employee_id=$('#csh_cashier_id').val();
		var member_no=$('#csh_member_no').val();
		var product_id=$('#csh_product_id').val();
		var quantity=$('#csh_quantity').val();
		var status_no=$('#csh_status_no').val();
		var product_status=$('#csh_product_status').val();
		var application_id=$('#csh_application_id').val();
		//for bill card lost
		var card_status=$("#csh_card_status").val();
		var promo_code=$("#csh_promo_code").val();
		var promo_tp=$("#csh_promo_tp").val();
		var promo_id=$("#csh_promo_id").val();
		var discount_member=$("#csh_discount_member").val();
		var get_point=$("#csh_get_point").val();
		var discount_percent='';
		var member_percent1=$('#csh_percent_discount').html();
			member_percent1=$.trim(member_percent1);		
		var arr_mp1=member_percent1.split('+');
			member_percent1=arr_mp1[0];
		var member_percent2=$('#csh_add_first_percent').val();
		var co_promo_percent='';
		var coupon_percent='';
		if(product_id.length==0){
			jAlert('??????????????????????????? ??????????????????????????????', 'WARNING!',function(){
				initFieldPromt();
				return false;
			});	
			return false;
		}//

		var csh_net=$('#csh_net').val();
		var csh_promo_discount=$('#csh_promo_discount').val();
		var check_repeat=$('#csh_check_repeat').val();
		var opts={
				type:'post',
				url:'/sales/cashier/ajax',
				cache: false,	
				data:{
					employee_id:employee_id,
					member_no:member_no,
					product_id:product_id,
					quantity:quantity,	
					status_no:status_no,
					product_status:product_status,
					doc_tp:doc_tp,
					application_id:application_id,
					card_status:card_status,
					promo_code:promo_code,
					promo_tp:promo_tp,
					promo_id:promo_id,
					promo_st:'',
					get_point:get_point,
					discount_member:discount_member,
					discount_percent:discount_percent,
					member_percent1:member_percent1,
					member_percent2:member_percent2,
					co_promo_percent:co_promo_percent,
					coupon_percent:coupon_percent,
					net_amt:csh_net,
					discount:csh_promo_discount,
					check_repeat:check_repeat,
					actions:'set_gap_val_tmp'
				},
				success:function(data){					
					var play_main_pro=$.trim($("#csh_play_main_promotion").val());
					var play_last_pro=$.trim($("#csh_play_last_promotion").val());
					var arr_data=data.split('#');	
					$("#csh_bal_discount").val(arr_data[3]);//*WR17102012 ??????????????????????????????????????????????????????????????? 50BTO1P
					if(arr_data[0]=='0'){
						jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
							initFieldPromt();
							return false;
						});	
					}else if(arr_data[0]=='3'){
						jAlert('????????????????????????????????????????????????????????????????????????????????? ???????????? ?????? ESC Key ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
							initFieldPromt();
							return false;
						});	
					}else{						
						if($('#csh_promo_tp').val()=='F' && $('#csh_limite_qty').val()=='1'){
							//?????????????????????????????????????????????????????? 1 ????????????????????? 1 ?????????
							endOfProOther();
						}
						if(arr_data[2]=='trn_tdiary2_sl'){
							/*
							if($('#csh_promo_tp').val()=='F' && $('#csh_limite_qty').val()=='1'){
								$('#csh_promo_tp').val('N');//clear ??????????????????????????????????????????????????????????????????
							}
							*/
							getCshTemp('N');
						}else{
							getPmtTemp();
							//////////////////////////////// start check co promotion ///////////////////////////////////
							///* case promotion 50BTO1P ?????????????????????????????????????????????????????????????????????????????????????????????????????????
							if(promo_code=='50BTO1P'){
								playCoPromo(promo_code,product_id,member_no);								
							}
							//////////////////////////////// end check co promotion ////////////////////////////////////
							///////////////////////////////////////// 04082014 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
							//*WR18082015 Lucky Draw
							if(promo_tp=='COUPON'){
									//*WR22072014 coupon lucky draw
									$.ajax({
										type:'post',
										url:'/sales/member/chkamtprobalancecoupon',
										data:{
											promo_code:promo_code,											
											product_id:product_id,
											quantity:quantity,
											rnd:Math.random()
										},success:function(data){
											var arr_data=data.split("#");
											if(arr_data[0]!='Y'){
												jAlert(arr_data[1], 'WARNING!',function(){
													$("#csh_product_id").focus();
													return false;
												});	
											}
											getPmtTemp('N');
										}
									});
							}
							//////////////////////////////////////// 04082014 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
						}
						initFieldPromt();
					}
				}
			};	
		$.ajax(opts);
	}//func

	function setCshValTemp(){
		/**
		*@ name : setCshValTemp
		*@ desc : for joke
		*@ lastmodify : 14022012
		*/
		var doc_tp="";//????????????????????????????????????
		if($("#csh_doc_tp_vt").val()!=''){
			doc_tp=$("#csh_doc_tp_vt").val();
		}else{
			doc_tp=$("#csh_doc_tp").val();
		}
		var employee_id=$('#csh_cashier_id').val();
		var member_no=$('#csh_member_no').val();
		var product_id=$('#csh_product_id').val();
		var quantity=$('#csh_quantity').val();
		var status_no=$('#csh_status_no').val();
		var product_status=$('#csh_product_status').val();
		var application_id=$('#csh_application_id').val();
		//for bill card lost
		var card_status=$("#csh_card_status").val();
		var promo_code=$("#csh_promo_code").val();
		var promo_id=$("#csh_promo_id").val();
		var discount_member=$("#csh_discount_member").val();
		var get_point=$("#csh_get_point").val();
		var discount_percent='';
		var member_percent1=$('#csh_percent_discount').html();
			member_percent1=$.trim(member_percent1);
		var arr_mp1=member_percent1.split('+');
			member_percent1=arr_mp1[0];
		var member_percent2=$('#csh_add_first_percent').val();
		var co_promo_percent='';
		var coupon_percent='';
		if(quantity.length==0){
			jAlert('??????????????????????????? ?????????????????????????????????', 'WARNING!',function(){
				$("#csh_quantity").focus();
				return false;
			});	
			return false;
		}//
		if(product_id.length==0){
			jAlert('??????????????????????????? ??????????????????????????????', 'WARNING!',function(){
				$("#csh_product_id").focus();
				return false;
			});	
			return false;
		}//
		
		//*WR25042013
		if(member_percent1 != '' && member_no==''){
			jAlert('????????????????????????????????????????????????????????????????????????????????? Please cancel the sale and re-open the bill.', 'WARNING!',function(){
				$("#csh_product_id").focus();
				return false;
			});	
			return false;
		}
		//for wait complete set temp!
		$("#csh_quantity").disable();
		$("#csh_product_id").disable();		
		//*WR17082016 format card_level#ops for support lucky draw promotion
		var mem_card_info=$('#id_smspromo').val();
		var opts={
				type:'post',
				//url:'/sales/cashier/ajax',
				url:'/sales/cashier/setcshvaltemp',
				cache:false,	
				data:{
					employee_id:employee_id,
					member_no:member_no,
					product_id:product_id,
					quantity:quantity,
					status_no:status_no,
					product_status:product_status,
					doc_tp:doc_tp,
					application_id:application_id,
					card_status:card_status,
					promo_code:promo_code,
					promo_id:promo_id,
					promo_st:'',
					get_point:get_point,
					discount_percent:discount_percent,
					member_percent1:member_percent1,
					member_percent2:member_percent2,
					co_promo_percent:co_promo_percent,
					coupon_percent:coupon_percent,
					mem_card_info:mem_card_info,
					actions:'set_csh_val_tmp'
				},
				success:function(data){
					initFieldPromt();
					var arr_data=data.split('#');	
					 if(arr_data[0]=='0'){
						jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){							
							return false;
						});	
					}				
					
				}
			};	
		$.ajax(opts);	
	}//func

	function setCshTemp(){
		/**
		*@ name :setCshTemp
		*@ desc :
		*@ comm :
		*@ lastmodify : 14072011
		*/
		var doc_tp="";//????????????????????????????????????
		if($("#csh_doc_tp_vt").val()!=''){
			doc_tp=$("#csh_doc_tp_vt").val();
		}else{
			doc_tp=$("#csh_doc_tp").val();
		}
		var employee_id=$('#csh_cashier_id').val();
		var member_no=$('#csh_member_no').val();
		var product_id=$('#csh_product_id').val();
		var quantity=$('#csh_quantity').val();
		var status_no=$('#csh_status_no').val();
		var product_status=$('#csh_product_status').val();
		var application_id=$('#csh_application_id').val();
		//for bill card lost
		var card_status=$("#csh_card_status").val();
		var promo_code=$("#csh_promo_code").val();
		if(application_id==''){
			application_id=promo_code;
		}
		var promo_tp=$('#csh_promo_tp').val();
		var promo_id=$("#csh_promo_id").val();
		var discount_member=$("#csh_discount_member").val();
		var get_point=$("#csh_get_point").val();
		var discount_percent='';
		var member_percent1=$('#csh_percent_discount').html();
		member_percent1=$.trim(member_percent1);		
		var arr_mp1=member_percent1.split('+');
		member_percent1=arr_mp1[0];
		var member_percent2=$('#csh_add_first_percent').val();		
		var co_promo_percent='';
		var coupon_percent='';
		var promo_amt=$('#csh_promo_amt').val();
		var promo_amt_type=$('#csh_promo_amt_type').val();
		var check_repeat=$('#csh_check_repeat').val();
		var web_promo=$('#csh_web_promo').val();
		if(quantity.length==0){
			jAlert('??????????????????????????? ?????????????????????????????????', 'WARNING!',function(){
				$("#csh_quantity").focus();
				return false;
			});	
		}
		if(product_id.length==0){
			jAlert('??????????????????????????? ??????????????????????????????', 'WARNING!',function(){
				$("#csh_product_id").focus();
				return false;
			});	
		}
		var opts={
				type:'post',
				url:'/sales/cashier/ajax',
				cache: false,	
				data:{
					employee_id:employee_id,
					member_no:member_no,
					product_id:product_id,
					quantity:quantity,	
					status_no:status_no,
					product_status:product_status,
					doc_tp:doc_tp,
					application_id:application_id,
					card_status:card_status,
					promo_code:promo_code,
					promo_id:promo_id,
					promo_tp:promo_tp,
					promo_st:'',
					get_point:get_point,
					discount_percent:discount_percent,
					member_percent1:member_percent1,
					member_percent2:member_percent2,
					co_promo_percent:co_promo_percent,
					coupon_percent:coupon_percent,
					promo_amt:promo_amt,
					promo_amt_type:promo_amt_type,
					check_repeat:check_repeat,
					web_promo:web_promo,
					actions:'set_csh_tmp'
				},
				success:function(data){
					var arr_data=data.split('#');	
					if(arr_data[0]=='0'){
						jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
							initFieldPromt();
							return false;
						});	
					}else if(arr_data[0]=='3'){
						jAlert('????????????????????????????????????????????????????????????????????????????????? ???????????? ?????? ESC Key ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
							initFieldPromt();
							return false;
						});	
					}else{		
						if($('#csh_promo_tp').val()=='F' && $('#csh_limite_qty').val()=='1'){//??????????????????????????? 1??????????????????????????????
							$('#csh_promo_tp').val('N');//clear ??????????????????????????????????????????????????????????????????
							$('#csh_promo_code').val('');					
						}
						//WR 23012013 check amount for sms pro 
						//if($('#csh_promo_pos').val()=='S' && parseFloat($('#csh_promo_amt').val())>0){
						if(($('#csh_promo_pos').val()=='S' || $('#csh_promo_pos').val()=='MCS') && parseFloat($('#csh_promo_amt').val())>0){
							$.ajax({
								type:'post',
								url:'/sales/member/chkamtprosms',
								data:{
									promo_code:promo_code,											
									product_id:product_id,
									quantity:quantity,
									promo_amt:$('#csh_promo_amt').val(),
									discount:$('#csh_promo_discount').val(),
									type_discount:$('#csh_type_discount').val(),
									rnd:Math.random()
								},success:function(data){
									getCshTemp('N');	
								}
							});
						}else if(promo_tp=='NEWBIRTH'){
							//if(promo_code=='OM02071113' || promo_code=='OM02081113' || promo_code=='OM02091113'){
								$.ajax({
									type:'post',
									url:'/sales/member/chkamtprobalance',
									data:{
										promo_code:promo_code,											
										product_id:product_id,
										quantity:quantity,
										rnd:Math.random()
									},success:function(data){
										getCshTemp('N');	
									}
								});
						}else{
							getCshTemp('N');
						}
						//WR 23012013 check amount for sms pro						
						//getCshTemp('N');	
					}
				}
		};		
		
		//check ?????????????????????????????????????????????????????? VIP
		if($('#csh_member_vip').val()=='1'){
			var vip_percent_discount=$('#csh_percent_discount').html();
			var limited=$('#csh_vip_limited').val();
			var limited_type=$('#csh_vip_limited_type').val();	
			var sum_amt=$('#csh_vip_sum_amt').val();//?????????????????????????????????????????????????????????????????????
			//var vip_balance=parseFloat(limited)-parseFloat(sum_amt);
			var vip_balance=parseFloat(sum_amt);
			var opts_vip={
						type:'post',
						url:'/sales/cashier/chkamtvip',
						data:{
							product_id:product_id,
							quantity:quantity,
							limited:limited,
							limited_type:limited_type	,
							sum_amt:sum_amt,
							vip_percent_discount:vip_percent_discount
						},
						success:function(data){
							if(data=='Y'){
								$.ajax(opts);
							}else{
								jAlert('????????????????????????????????????????????????????????????????????????????????? '+addCommas(vip_balance)+' ?????????','WARNING!',function(){
									initFieldPromt();
									return false;
								});	
								return false;
							}
						}
					};
			$.ajax(opts_vip);
		}else{
			$.ajax(opts);
		}	
	}//func

	function getCshTemp(flgSale){	
		/**
		 *@desc flgSale : 'Y' is sale transaction complete
		 				        'N' is init field promt to key sale transaction
		  				        'C' is cancel bill reopen bill
		  				        'P' is key product by dialog open
		 */
		$.ajax({
			type:'post',
			url:'/sales/cashier/getpages',
			cache: false,
			data:{				
				rp:14,
				flg:'cashier',
				now:Math.random()
			},
			success:function(data){	
				$('#tbl_cashier').flexOptions({url:'/sales/cashier/cashiertemp',newp:data}).flexReload(); 
				getSubTotal(flgSale);
				if(flgSale=='N'){
					initFieldPromt();
				}else if(flgSale=='P'){
					initFieldForDialog();
				}else{
					initFieldOpenBill();
				}
				return false;
			}
		});
	}//func

	function getSubTotal(flgSale){
		/**
		*@name getSum
		*@desc 
		*@comment need to check flg_chk_point for any bill
		*@problem : OPPD300 ?????????????????????????????????????????? x3 ????????????????????????????????????????????????????????????????????????????????????
		*/
		var cn_amount=$("#cn_amount").val();//??????????????????????????????????????????????????????????????????????????????????????? CN
		var flg_chk_point='no';
		if( ($.trim($("#csh_member_no").val())!='' ||  $("#csh_refer_member_id").val()!='' ) && $("#csh_get_point").val()=='Y' ){
			flg_chk_point='yes';
		}
		$.getJSON(
				"/sales/cashier/ajax",
				{
					cn_amount:cn_amount,
					flg_chk_point:flg_chk_point,
					xpoint:$("#csh_xpoint").val(),
					flg_tbl:'cashier',
					actions:'getSumCshTemp'
				},
				function(data){
						$.each(data.sumcsh, function(i,m){
							if(m.exist=='yes' && parseInt(m.sum_net_amt)!='0'){								
								var csh_sum_quantity=parseInt(m.sum_quantity);
								var csh_sum_discount=parseFloat(m.sum_discount);
								var csh_sum_amount=parseFloat(m.sum_amount);
								var csh_net_amt=parseFloat(m.sum_net_amt);
								//var csh_coupons=0.00;
								csh_sum_discount=csh_sum_discount.toFixed(2);
								csh_sum_amount=csh_sum_amount.toFixed(2);
								$('#csh_sum_discount').val(csh_sum_discount);
								$('#csh_sum_amount').val(addCommas(csh_sum_amount));
								$('#csh_net').val(addCommas(csh_net_amt.toFixed(2)));
								//--------------------------------------------------------
								//*WR22072014 for support used point to redeem
								if(parseInt(m.used_point)>0){
									$('#csh_point_used').val(m.used_point);
								}
								//--------------------------------------------------------
								var point_receive=parseInt(m.point_receive);
								var point_used=$('#csh_point_used').val();
								var point_net=point_receive-point_used;
								$('#csh_point_receive').val(point_receive);
								$('#csh_point_net').val(point_net);
								//--------------------------------------------------------								
							}else{
								$('#csh_sum_discount').val('0.00');
								$('#csh_sum_amount').val('0.00');
								$('#csh_net').val('0.00');
							}	
							if(flgSale=='Y'){
								displayDigit($('#txt_cash_back').val());
							}else{
								displayDigit($('#csh_net').val());
								//for gab promotion
								if($('#csh_gap_promotion').val()=='Y' && $('#csh_promo_tp').val()=='M'){	
									getDiscountNetAmt();				
								}
							}
							
						});//foreach
						return false;
				}
		);
	}//func

	function displayDigit(ndigit){
		/**
		*@desc
		*@param String ndigit
		*@return void
		*/		
		if(typeof(ndigit)=='undefined' || ndigit=='' || ndigit=='0'){
			ndigit="0.00";
		} 
		var arr_num= [];
		    arr_num=ndigit.split('');
		var str_num="";
		var str_imgname="";
		var w = screen.width;
		var h = w=='1024'?"70px":"90px";
		for(k=0;k<arr_num.length;k++){
			if(arr_num[k]=='.'){
				str_imgname="dotted";
			}else if(arr_num[k]==','){
				str_imgname="comma";
			}else{
				str_imgname=arr_num[k];
			}
			str_num+="<img src='/sales/img/numbers_red/"+str_imgname+".png' height="+h+">";
		}
		$("#sub-other").html("<p>"+str_num+"</p>");	
	}//func

	function checkNeedFields(){
		var arr_nfield=new Array("csh_status_no","csh_quantity","csh_product_id");
	}//func

	function rePromoPointDiscount(){
		/**
		 *@desc ?????????????????????????????????????????? ????????????????????????????????? net_amt ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
		 *@return null
		 */
		//cal % discount									
		$.ajax({
				type:'post',
				url:'/sales/member/repromodiscount',
				data:{
						promo_code:$("#csh_promo_code").val(),
						promo_id:$("#csh_promo_id").val(),
						promo_tp:$("#csh_promo_tp").val(),
						rnd:Math.random()
				},
				success:function(data){
					if(data=='1'){
						$("#csh_point_used").val('0');
						//*WR30012014
						if($("#csh_promo_code").val()=='BURNPOINT2' || $("#csh_promo_code").val()=='OX24171113'){
							getPmtTemp('N');
						}else{
							getCshTemp('N');
						}
					}
				}
			});
		return false;
	}//func
	
	function redeemPointOffline(){
		/**
		*@desc ?????????????????????????????????????????????????????????????????? offline ????????? ???????????????????????????????????????????????????????????????????????????????????????
		*@return
		*/		
		var $csh_status_no=$("#csh_status_no");
		var status_no=jQuery.trim($csh_status_no.val());
		var $csh_member_no=$("#csh_member_no");
		var member_no=jQuery.trim($csh_member_no.val());
		if(member_no.length==0){
			jAlert('Please specify member id.','WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
		}else{				
				//----------- start redeem point---------
				var opts_dlgRedeemPoint={
						autoOpen:false,
						width:'70%',
						modal:true,
						resizeable:true,
						position:'center',
						showOpt: {direction: 'up'},		
						closeOnEscape:true,	
						title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????",
						open:function(){					
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
			    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 0.05em 0 0.05em",
			    				"background-color":"#c5bcdc","font-size":"27px","color":"#000"});
							$("*:focus").blur();
							$("#dlgRedeemPoint").html("");
							$.ajax({
								type:'post',
								url:'/sales/member/promopointhead',
								cache:false,
								data:{
									now:Math.random()
								},
								success:function(data){												
									$("#dlgRedeemPoint").html(data);
									$(this).parent().find('select, input, textarea').blur();
									$('.tableNavPromoPoint ul li').not('.nokeyboard').navigate({
								        wrap:true
								    }).click(function(e){												 
								    	e.stopPropagation();
									    e.preventDefault();
									    $('.tableNavPromoPoint ul').navigate('destroy');
									    if($(this).attr('idpromo')=='nodata'){
										    $("#dlgRedeemPoint").dialog('close');
										    setTimeout(function(){delayTime("csh_member_no");},400);
										    return false;
										}
									    $('#csh_trn_diary2_sl').val('Y');
									    var selpro=$.parseJSON($(this).attr('idpromo'));													   
									    $("#csh_promo_code").val(selpro.promo_code);	
									    $("#csh_promo_tp").val(selpro.promo_tp);
									    $("#csh_play_main_promotion").val(selpro.play_main_promotion);
									    $("#csh_play_last_promotion").val(selpro.play_last_promotion);
									    $("#csh_get_promotion").val(selpro.get_promotion);
									    if(selpro.member_discount){
									    	$('#csh_percent_discount').html('0');
									    }
									    $("#csh_discount_member").val(selpro.member_discount);
									    $("#csh_get_point").val(selpro.get_point);													
									    //---- start ------
									    var opts_dlgRedeemPointDetail={
												autoOpen:false,
												width:'70%',
												height:'400',
												modal:true,
												resizeable:true,
												position:'center',
												showOpt: {direction:'up'},		
												closeOnEscape:true,	
												title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????",
												open:function(){
													$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
													$(this).dialog('widget')
											            .find('.ui-dialog-titlebar')
											            .removeClass('ui-corner-all')
											            .addClass('ui-corner-top');
									    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 0.05em 0 0.05em",
									    				"background-color":"#c5bcdc","font-size":"26px","color":"#000"});
													$(this).parent().find('select, input, textarea').blur();
													$("#dlgRedeemPointDetail").html("");
													var opts_promopointdetail={
														type:'post',
														url:'/sales/member/promopointdetail',
														cache:false,
														data:{
															promo_code:selpro.promo_code,
															promo_tp:selpro.promo_tp,
															member_no:member_no,
															now:Math.random()
														},
														success:function(data){
															$("#dlgRedeemPointDetail").html(data);
															$(".tableNavPromoPointDetail ul li").not('.nokeyboard').navigate({
														        wrap:true
														    }).click(function(evt){
																    evt.stopPropagation();
																    evt.preventDefault();								    
																    $("#dlgRedeemPointDetail").dialog("close");																    
																    var selprodetail=$.parseJSON($(this).attr('idpromo'));
																    $("#info_notify").css({'display':''});
																    $("#info_notify").html(selprodetail.promo_des);
																    $("#csh_point_used").val(selprodetail.point);
																    $("#csh_start_baht").val(selprodetail.start_baht);
																    $("#csh_end_baht").val(selprodetail.end_baht);	
																    
																    if(selpro.promo_code=='OM19560317' || selpro.promo_code=='OM19570317' || 
																    		selpro.promo_code=='OM19580317' || selpro.promo_code=='OM19590317'){													    	
																    	playMstPromotion(selpro.promo_code,1);
																    	$("#dlgRedeemPointDetail").dialog("close");																					    
																    	return false;
																    }else
																    
																    if(selprodetail.promo_id!=0){
																	    $("#csh_promo_id").val(selprodetail.promo_id);
																	    setTimeout('initFieldPromt()',400);
																	}else{
																		//case point ???????????????
																		initFieldOpenBill();
																		return false;
																	}																					
														    });																			
														}//success
													};
													//** bug ???????????????????????????????????????????????????????????? ESCAP ???????????????????????? key new member ???????????????????????????????????????? 
													if($('#csh_status_no').val()=='04'){
														$.ajax(opts_promopointdetail);
													}
													else{
														 $("#dlgRedeemPointDetail").dialog("close");
													}	
												},
												close:function(evt,ui){	
														if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
															initFieldOpenBill();
														}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
															evt.stopPropagation();
															evt.preventDefault();
															initFieldOpenBill();
														}
														$('.tableNavPromoPointDetail ul').navigate('destroy');
														$("#dlgRedeemPointDetail").dialog("destroy");
												}
									    };
									    $("#dlgRedeemPoint").dialog("close");
									    $("#dlgRedeemPointDetail").dialog("destroy");
										$("#dlgRedeemPointDetail").dialog(opts_dlgRedeemPointDetail);
										$("#dlgRedeemPointDetail").dialog("open");
									    //---- end --------		
										return false;
									});//end click
									
								}//end success function
							});//end ajax pos
							
						},
						close:function(evt,ui){
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
								initFormCashier();
								initFieldOpenBill();
							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
								evt.stopPropagation();
								evt.preventDefault();
								initFormCashier();
								initFieldOpenBill();
							}
							//$('.tableNavPromoPoint ul').navigate('destroy');
						}
				};
			}
			$("#dlgRedeemPoint").dialog("destroy");
			$("#dlgRedeemPoint").dialog(opts_dlgRedeemPoint);			
			$("#dlgRedeemPoint").dialog("open");
			//----------- end redeem point ----------								
			return false;
	}//func
	
	function selCoPromotion(promo_code,net_amt,amount){
		/**
		 * @desc 
		 * * create 22072014
		 * * modify 20102014 
		 * *?????????????????????????????????????????? ????????????????????????????????????
		 * @return 
		 */		
		var play_last_promotion=$('#csh_play_last_promotion').val();//*WR18082015 Lucky Draw
		var status_no=$('#csh_status_no').val();		
		var member_no=$('#csh_member_no').val();
		var ops_day=$('#csh_ops_day').val();
		//*WR03012017
		var promo_tp=$('#csh_promo_tp').val();
		var str_bd=$('#info_member_birthday').html();
		var arr_bd=str_bd.split('/');
		str_bd=arr_bd[2]+"-"+ arr_bd[1] + "-" +arr_bd[0];
		$.ajax({
			type:'post',
			url:'/sales/member/selcopromotion',
			data:{
				member_no:member_no,
				ops_day:ops_day,
				promo_code:promo_code,
				net_amt:net_amt,
				amount:amount,
				bday:str_bd,
				promo_tp:promo_tp,
				rnd:Math.random()
			},success:function(data){	
				if($.trim(data)!=''){
					$('<div id="dlgSelCoPromotion"></div>').dialog({
				           autoOpen:true,
						   width:'65%',
						   height:'350',	
						   modal:true,
						   resizable:true,
						   closeOnEscape:false,		
				           title: "?????????????????????????????????????????????????????????????????????????????????",		
				           position:"center",
				           open:function(){
				        	   $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
								$(this).dialog('widget')
						            .find('.ui-dialog-titlebar')
						            .removeClass('ui-corner-all')
						            .addClass('ui-corner-top');
							    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
								    																			"font-size":"26px","color":"#0000FF",
								    																			"padding":"0 0 0 0"});	
							    $('#dlgSelCoPromotion').empty().html(data);	
	    			    		 $('.tableNavSelCopromotion ul li').not('.nokeyboard').navigate({
							        wrap:true
							    }).click(function(evt){		
								    evt.stopPropagation();
								    evt.preventDefault();										    
								    var sel_promo=$.parseJSON($(this).attr('idpromo'));	
								    var promo_play=sel_promo.promo_code;		
								    var promo_des=sel_promo.promo_des;
								    var unitn=sel_promo.unitn;
								    //*WR19122014 ????????????????????????????????????????????????????????? OPPN300
								    if(promo_play=='OX07320814' || promo_play=='OX07561014'){
								    	var chk_oppn=$('#info_apply_promo').html();
								    	chk_oppn=$.trim(chk_oppn);
								    	if(chk_oppn != 'OPPN300'){
								    		jAlert('??????????????????????????????????????????????????? OPPN300 ????????????????????????','WARNING!',function(){
												return false;
											});	
								    		return false;
								    	}
								    }
								    $('#dlgSelCoPromotion').dialog('close');
								    /////////////////// KEY PRODUCT FREE ///////////////////////////
								    $('<div id="dlgCoProduct">' + '?????????????????????????????? : <input type="text" size="15" id="co_product_id2"/></div>').dialog({
									           autoOpen:true,
											   width:'35%',
											   height:'auto',	
											   modal:true,
											   resizable:true,
											   closeOnEscape:false,		
											   title: " " + promo_des,
									           position:"center",
									           open:function(){
													$(this).dialog('widget')
											            .find('.ui-dialog-titlebar')
											            .removeClass('ui-corner-all')
											            .addClass('ui-corner-top');		
													//button style	
									  			    $(this).dialog("widget").find(".ui-btndlgpos")
									                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
										  			  $('#co_product_id2').keypress(function(evt){																    	
													    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
										   			        if(key == 13){	
												    			evt.preventDefault();
												    			evt.stopPropagation();
												    			$('#btnCoProductSubmit').trigger('click');												    			
												    			return false;
										   			        }
													    });
									           },
							    				buttons: [ 
											    	            { 
											    	                text: "????????????",
											    	                id:"btnCoProductSubmit",
											    	                class: 'ui-btndlgpos', 
											    	                click: function(evt){ 
											    	                	evt.preventDefault();
																 		evt.stopPropagation();
																 		var member_no=$('#csh_member_no').val();
																 		var product_id=$('#co_product_id2').val();																		 		
															 			 product_id=$.trim(product_id);																		 			
															    		if(product_id.length == 0){
														    				jAlert('?????????????????????????????????????????????????????????', 'WARNING!',function(){
														    					$('#co_product_id2').val('').focus();
														    					return false;
														    				});	
														    				return false;
															    		}
															    		
															    		$.ajax({
															    			type:'post',
															    			url:'/sales/member/setcopromoproduct',
															    			data:{
															    				status_no:status_no,
															    				member_no:member_no,
															    				promo_code:promo_play,															    				
															    				product_id:product_id,
															    				unitn:unitn,
															    				rnd:Math.random()
															    			},success:function(data){
															    				arr_data=data.split('#');													    				
															    				if(arr_data[0]=='1'){															    					
															    					$('#dlgCoProduct').dialog('close');
															    					if(arr_data[2]=='trn_tdiary2_sl'){
															    						getCshTemp('N');
															    					}else if(arr_data[2]=='trn_promotion_tmp1'){
															    						getPmtTemp('N');
															    					}
															    					selCoPromotion(promo_code,net_amt,amount);//*WR01092014
															    				}else 	if(arr_data[0]=='0'){
															    					msg_result="????????????????????????????????????????????????????????????????????????";						
															    					jAlert(msg_result,'WARNING!',function(){
																						$("#btn_cal_promotion").removeAttr("disabled");
																						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
																						$('#co_product_id2').focus();
																						return false;
																					});
															    				}else 	if(arr_data[0]=='2'){
															    					msg_result="???????????????????????????????????? ??????????????????????????????";						
															    					jAlert(msg_result,'WARNING!',function(){
																						$("#btn_cal_promotion").removeAttr("disabled");
																						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
																						$('#co_product_id2').focus();
																						return false;
																					});
															    				}else if(arr_data[0]=='3'){
															    					msg_result="?????????????????????????????????????????????????????????????????????";
															    					jAlert(msg_result,'WARNING!',function(){
																						$("#btn_cal_promotion").removeAttr("disabled");
																						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
																						$('#co_product_id2').focus();
																						return false;
																					});
															    				}else if(arr_data[0]=='4'){
															    					msg_result="?????????????????????????????????????????????????????????????????????????????????????????????";
															    					jAlert(msg_result,'WARNING!',function(){
																						$("#btn_cal_promotion").removeAttr("disabled");
																						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
																						$('#co_product_id2').focus();
																						return false;
																					});
															    				}else if(arr_data[0]=='5'){
															    					msg_result="?????????????????????????????????????????????????????????????????????????????? " + unitn +" ????????????";
															    					jAlert(msg_result,'WARNING!',function(){
																						$("#btn_cal_promotion").removeAttr("disabled");
																						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
																						$('#co_product_id2').focus();
																						return false;
																					});
															    				}																	    				
															    				
															    			}
															    		});								 		
															 																		 		
										    	                }
										    	            }
										    	  ]			
				    							,close:function(evt){
				    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
														evt.stopPropagation();
							    						evt.preventDefault();		
							    						selCoPromotion(promo_code,net_amt,amount);//*WR01092014
													}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
														evt.stopPropagation();
							    						evt.preventDefault();
							    						selCoPromotion(promo_code,net_amt,amount);//*WR01092014
													}
				    								$('#dlgCoProduct').remove();	
									           }
				    				});
								   /////////////////// KEY PRODUCT FREE ///////////////////////////
							    });
				           },				           
				           buttons: [ 			
				    	                { 
					    	                text: "PAYMENT",
					    	                id:"btnPayBySelCoPromo",
					    	                class: 'ui-btndlgpos', 
					    	                click: function(evt){ 
					    	                	evt.preventDefault();
										 		evt.stopPropagation();	
										 		if(status_no=='01' && promo_code!='OPPLI300' && promo_code!='OPMGMI300'){ //and type<>'IDCARD'
										 			setTimeout('setNewCardMember()',400);										 		
										 		}else if(play_last_promotion=='Y'){
										 			callLstChkPro();
										 		}else{
										 			paymentBill(status_no);
										 		}
										 		$('#dlgSelCoPromotion').dialog('close');										 		
					    	                }
				    	                }
				    	  ]						           
				           ,close:function(evt){
				        	   if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
									evt.stopPropagation();
									evt.preventDefault();									
									//paymentBill(status_no);
									if(status_no=='01' && promo_code!='OPPLI300' && promo_code!='OPMGMI300'){
							 			setTimeout('setNewCardMember()',400);							 		
							 		}else if(play_last_promotion=='Y'){
							 			//*WR18082015 Lucky Draw
							 			callLstChkPro();
							 		}else{
							 			paymentBill(status_no);
							 		}
								}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
									evt.stopPropagation();
									evt.preventDefault();									
									paymentBill(status_no);
								}
				        	   $('.tableNavSelCopromotion ul').navigate('destroy');
				        	   $('#dlgSelCoPromotion').remove();
				           } 
				    });			    			
					//select co promotion
				}else{			
					if(status_no=='01' && promo_code!='OPPLI300' && promo_code!='OPMGMI300' && 
							promo_code!='OPPHI300' && application_id!='OPDTAC300' && application_id!='OPKTC300' && application_id!='OPTRUE300'){
			 			setTimeout('setNewCardMember()',400);			 			
			 		}else if(play_last_promotion=='Y'){
			 			//*WR18082015 Lucky Draw
			 			callLstChkPro();
			 		}else{
			 			paymentBill(status_no);
			 		}
				}
			}
		});
	}//func	
	
	function redeemPointSpecial(promo_code){
		/**
		 * @desc redeem and receive point 
		 * * 22072014 ???????????????????????????????????????????????????????????????????????????????????????
		 * @return
		 */
		$('<div id="dlgProRedeemSpecial"></div>').dialog({
	           autoOpen:true,
			   width:'50%',
			   height:'450',	
			   modal:true,
			   resizable:false,
			   closeOnEscape:false,		
	           title: "?????????????????????????????????????????????",
	           position:"center",
	           open:function(){
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
					//button style	
	  			    $(this).dialog("widget").find(".ui-btndlgpos")
	                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"16px"});
	  			 $(this).dialog("widget").find('.ui-dialog-buttonset').css('float','none');
	  			 $(this).dialog("widget").find('.ui-dialog-buttonset>button:first-child').css('left','10px');
	  			 $(this).dialog("widget").find('.ui-dialog-buttonset>button:last-child').css('float','right');
	  			    
	  			 $.ajax({
	  				 type:'post',
	  				 url:'/sales/member/getproredeempoint',
	  				 data:{
	  					 promo_code:promo_code,
	  					 rnd:Math.random()
	  				 },success:function(data){
	  					 $('#dlgProRedeemSpecial').html(data);
	  					 $('.pro_rp_sp').each(function(){
	  						 $(this).click(function(e){
	  							 e.preventDefault();
	  							 var m_promo_code=promo_code;
	  							 var promo_id=$(this).attr('id');
	  							 var promo_des=$(this).attr('iddes');	  			
	  							 $('#dlgProRedeemSpecial').dialog('close');
	  							 /////////////////////////////////////// FORM KEYPRODUCT \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	  							 //???????????????????????????????????????????????????????????????????????????????????????????????????????????? 
			  							$('<div id="dlgPdtRedeemPoint"><span style="color:#336666;font-size:20px;">?????????????????????????????? : </span><input type="text" size="15" id="pdt_redeempoint"/></div>').dialog({
									           autoOpen:true,
											   width:'400',
											   height:'200',	
											   modal:true,
											   resizable:false,
											   closeOnEscape:false,		
									           title: " " + promo_des,
									           position:"center",
									           open:function(){
													$(this).dialog('widget')
											            .find('.ui-dialog-titlebar')
											            .removeClass('ui-corner-all')
											            .addClass('ui-corner-top');		
													//button style	
									  			    $(this).dialog("widget").find(".ui-btndlgpos")
									                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
									  			    
									  			  $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
												    	$("#pdt_redeempoint").focus();
													});
									  			    
										  			  $('#pdt_redeempoint').keypress(function(evt){																    	
													    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
										   			        if(key == 13){	
												    			evt.preventDefault();
												    			evt.stopPropagation();
												    			$('#btnSetPdtRedeemPoint').trigger('click');
												    			return false;
										   			        }
													    });
									           },
							    				buttons: [ 
											    	            { 
											    	                text: "????????????",
											    	                id:"btnSetPdtRedeemPoint",
											    	                class: 'ui-btndlgpos', 
											    	                click: function(evt){ 
											    	                	evt.preventDefault();
																 		evt.stopPropagation();
																 		var product_id=$('#pdt_redeempoint').val();
																 		product_id=$.trim(product_id);																		 			
															    		if(product_id.length<1){
														    				jAlert('???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
														    					$('#pdt_redeempoint').val('').focus();
														    					return false;
														    				});	
														    				return false;
															    		}
															    		//alert(m_promo_code + " ==> " + promo_id);
															    		var member_no=$('#csh_member_no').val();
															    		var member_point=$('#csh_point_total').html();
															    		var status_no=$('#csh_status_no').val();
											  							 $.ajax({
											  								 type:'post',
											  								 url:'/sales/member/setpdtredeempoint',
											  								 data:{
											  									 member_no:member_no,
											  									 member_point:member_point,
											  									 promo_code:promo_id,
											  									 product_id:product_id,
											  									 quantity:'1',
											  									 status_no:status_no,
											  									 rnd:Math.random()
											  								 },success:function(data){											  									
											  									var arr_data=data.split('#');
											  									if(arr_data[0]=='1'){
											  										$('#dlgPdtRedeemPoint').dialog('close');	
											  										getCshTemp('N');
															    				}else{													    					
															    					jAlert(arr_data[1],'WARNING!',function(){
															    						$('#pdt_redeempoint').val('').focus();
																						return false;
																					});
															    					
															    				}//end if									  									
											  									
											  								 }
											  							 });//ajax													 		
															 		
										    	                }
										    	            }
										    	  ]			
				    							,close:function(evt){
				    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
														evt.stopPropagation();
							    						evt.preventDefault();					    					
													}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
														evt.stopPropagation();
							    						evt.preventDefault();  						
													}
				    								setTimeout(function(){
				    									redeemPointSpecial(m_promo_code);
				    								},400);
				    								$('#dlgPdtRedeemPoint').remove();	
									           }
				    				});
	  							 
	  							 /////////////////////////////////////// FORM KEYPRODUCT \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	  							 
	  							 
	  						 });
	  					 });
	  					 
	  				 }
	  			 });
		  			
	           },
				buttons: [ 								
			    	            { 
			    	                text: "Cancel",
			    	                id:"btnCancelRedeemPoint",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	evt.preventDefault();
								 		evt.stopPropagation();
								 		
								 		jConfirm('DO YOU WANT TO CANCEL THE SALE?','CONFIRM MESSAGE', function(r){
									        if(r){
									        	$('#dlgProRedeemSpecial').dialog('close');
										 		initTblTemp()
												initFormCashier();
												initFieldOpenBill();
												getCshTemp();
									        }
										});							 		
								 		
								 		
			    	                }
			    	            },
		    	                { 
			    	                text: "Payment",
			    	                id:"btnPayRedeemPoint",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	evt.preventDefault();
								 		evt.stopPropagation();					 		
								 		$('#dlgProRedeemSpecial').dialog('close');
								 		//check ????????????????????? next to tomorrow 500 net free coupon 900062								 		
								 		var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
								 		net_amt=parseFloat(net_amt);
								 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
								 		amount=parseFloat(amount);
								 		if(net_amt>=500){
								 			//last pro check free product method ???????????????
								 			selCoPromotion(promo_code,net_amt,amount);
								 		}else{
								 			paymentBill('04');
								 		}
								 		
								 		
			    	                }
		    	                }
		    	            
		    	  ]			
					,close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
							evt.preventDefault();
							initTblTemp()
							initFormCashier();
							initFieldOpenBill();
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
							evt.preventDefault();
							initTblTemp()
							initFormCashier();
							initFieldOpenBill();
						}
						$('#dlgProRedeemSpecial').remove();
		           }
		});
	}//func
	
	function redeemPointDetail(promo_code,promo_tp,member_no){
		 //---- start ------
		var opts_dlgRedeemPointDetail={
				autoOpen:false,
				width:'70%',
				height:'400',
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction:'up'},		
				closeOnEscape:false,	
				title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????",
				open:function(){
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 0.05em 0 0.05em",
	    				"background-color":"#c5bcdc","font-size":"26px","color":"#000"});
					$(this).parent().find('select, input, textarea').blur();
					$("#dlgRedeemPointDetail").html("");
					var opts_promopointdetail={
						type:'post',
						url:'/sales/member/promopointdetail',
						cache:false,
						data:{
							promo_code:promo_code,
							promo_tp:promo_tp,
							member_no:member_no,
							now:Math.random()
						},
						success:function(data){
							$("#dlgRedeemPointDetail").html(data);
							$(".tableNavPromoPointDetail ul li").not('.nokeyboard').navigate({
						        wrap:true
						    }).click(function(evt){
								    evt.stopPropagation();
								    evt.preventDefault();								    
								    $("#dlgRedeemPointDetail").dialog("close");
								    var selprodetail=$.parseJSON($(this).attr('idpromo'));
								    $("#other_promotion_title").html(selprodetail.promo_des);
								    $("#csh_point_used").val(selprodetail.point);
								    $("#csh_start_baht").val(selprodetail.start_baht);
								    $("#csh_end_baht").val(selprodetail.end_baht);	
								    
//								    if(promo_code=='OM19560317' || promo_code=='OM19570317' || 
//								    		promo_code=='OM19580317' || promo_code=='OM19590317'){													    	
//								    	playMstPromotion(promo_code,1);
//								    	$("#dlgRedeemPointDetail").dialog("close");																					    
//								    	return false;
//								    }else 
								    
								    if(selprodetail.promo_id!=0){
									    $("#csh_promo_id").val(selprodetail.promo_id);
									    
									    setTimeout(function(){
											fromreadprofile(promo_code,'Y',member_no,'','mobile_no','chk_mem_idcard','Redeem 50 Points Get Free Product  <= $5  USD.');		            									
										},400);
									    
									    //setTimeout('initFieldPromt()',400);
									}else{
										//case point ???????????????
										initFieldOpenBill();
										return false;
									}																					
						    });																			
						}//success
					};
					//** bug ???????????????????????????????????????????????????????????? ESCAP ???????????????????????? key new member ???????????????????????????????????????? 
					if($('#csh_status_no').val()=='04'){
						$.ajax(opts_promopointdetail);
					}
					else{
						 $("#dlgRedeemPointDetail").dialog("close");
					}																			
				},
				close:function(evt,ui){	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							initFieldOpenBill();
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
							evt.preventDefault();
							initFieldOpenBill();
						}
						$('.tableNavPromoPointDetail ul').navigate('destroy');
						$("#dlgRedeemPointDetail").dialog("destroy");
				}
	    };
	    $("#dlgRedeemPoint").dialog("close");
	    $("#dlgRedeemPointDetail").dialog("destroy");
		$("#dlgRedeemPointDetail").dialog(opts_dlgRedeemPointDetail);
		$("#dlgRedeemPointDetail").dialog("open");
	    //---- end --------		
	}//func

	
	
	function redeemPoint(){
		/**
		*@desc
		*@return
		*/		
		var $csh_status_no=$("#csh_status_no");
		var status_no=jQuery.trim($csh_status_no.val());
		var $csh_member_no=$("#csh_member_no");
		var member_no=jQuery.trim($csh_member_no.val());
		if(member_no.length==0){
			jAlert('Please specify member id.','WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
		}else{	
			var opts_redeempoint=$.getJSON(
					"/sales/cashier/ajax",
					{
						status_no:status_no,
						member_no:member_no,
						actions:'memberinfo'
					},
					function(data){		
						opts_redeempoint=null;
						if(data=='2'){
							jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});
						}
						else{
							$.each(data.member, function(i,m){
								if(m.exist=='yes'){
									
									//*WR18102016		
									if(status_no=='04' && m.link_status=='OFFLINE'){										
										jAlert("?????????????????????????????????????????????????????? Server ???????????????????????????????????????????????????(OFFLINE)?????????","WARNING!",function(){
											initFieldOpenBill();
    	     				            	return false;
    	     					        });
    	     				    		return false;										
									}
									
									var member_fullname=$.trim(m.name)+" "+$.trim(m.surname);
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

									var address=jQuery.trim(m.h_address)+" "+
												jQuery.trim(m.h_village_id)+" "+
												jQuery.trim(m.h_village)+" "+
												jQuery.trim(m.h_soi)+" "+
												jQuery.trim(m.h_road)+" "+
												jQuery.trim(m.h_district)+" "+
												jQuery.trim(m.h_amphur)+" "+
												jQuery.trim(m.h_province)+" "+
												jQuery.trim(m.h_postcode);
									
									$('#csh_member_fullname').html(member_fullname);
									$('#csh_birthday').html(birthday);//not exist form
									$('#csh_apply_date').html(apply_date);//not exist form
									$('#csh_expire_date').html(expire_date);
									$('#csh_member_type').html(remark);
									$('#csh_percent_discount').html(percent_discount);
									$('#csh_point_total').html(mp_point_sum);
									
									//WR24122013 POINT EXPIRE
									$("#csh_transfer_point").val(m.transfer_point);
									$("#csh_expire_transfer_point").val(m.expire_transfer_point);
									$("#csh_curr_point").val(m.curr_point);
									$("#csh_balance_point").val(m.balance_point);
									
									//*WR08022017 for suport show point expire date									
									if ( typeof(m.expire_transfer_point) !== "undefined" && 
											m.expire_transfer_point !== null && m.expire_transfer_point!=="0000-00-00") {
										var arr_pointexpire=m.expire_transfer_point.split('-');
										var year_pointexpire=parseInt(arr_pointexpire[0])+543;
										var expire_point_transfer_show=arr_pointexpire[2]+"/" +arr_pointexpire[1]+ "/" +  year_pointexpire;
										expire_point_transfer_show="("+expire_point_transfer_show+")";										
										//$('#csh_expire_transfer_point_show').html(expire_point_transfer_show);
									}
									
									$('#csh_buy_net').html(buy_net);
									$('#csh_address').html(address);
									//------------zone member info -----									
									$("#info_refer_member_id").html(refer_member_id);
									$("#info_member_fullname").html(member_fullname);
									$("#info_member_applydate").html(apply_date);
									$("#info_member_expiredate").html(expire_date);
									$("#info_member_birthday").html(birthday);
									$("#info_member_opsday").html(m.special_day);
									$("#info_apply_promo").html(m.apply_promo);
									$("#info_apply_promo_detail").html(m.apply_promo_detail);
									$("#info_member_address").html(address);
								}
								if(m.status=='1'){
									if(m.mem_status=='N'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active ?????????????????????????????????????????????????????? \n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}else if(m.mem_status=='F'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ???????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}else if(m.mem_status=='T'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}
									return false;
								}
								if(m.expire_status=='Y'){
									jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
										//initFormCashier();
										initFieldOpenBill();
										return false;
									});	
									return false;									
								}
								//----------- start redeem point---------
								var opts_dlgRedeemPoint={
										autoOpen:false,
										width:'50%',
										modal:true,
										resizeable:true,
										position: { my: "center bottom", at: "center center", of: window },
										showOpt: {direction: 'up'},		
										closeOnEscape:false,	
										title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????",
										open:function(){					
											$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
											$(this).dialog('widget')
									            .find('.ui-dialog-titlebar')
									            .removeClass('ui-corner-all')
									            .addClass('ui-corner-top');
							    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 0.05em 0 0.05em",
							    				"background-color":"#c5bcdc","font-size":"27px","color":"#000"});
											$("*:focus").blur();
											$("#dlgRedeemPoint").html("");
											$.ajax({
												type:'post',
												url:'/sales/member/promopointhead',
												cache:false,
												data:{
													now:Math.random()
												},
												success:function(data){												
													$("#dlgRedeemPoint").html(data);
													$(this).parent().find('select, input, textarea').blur();
													$('.tableNavPromoPoint ul li').not('.nokeyboard').navigate({
												        wrap:true
												    }).click(function(e){												 
												    	e.stopPropagation();
													    e.preventDefault();
													    $('.tableNavPromoPoint ul').navigate('destroy');
													    if($(this).attr('idpromo')=='nodata'){
														    $("#dlgRedeemPoint").dialog('close');
														    setTimeout(function(){delayTime("csh_member_no");},400);
														    return false;
														}
													    $('#csh_trn_diary2_sl').val('Y');
													    var selpro=$.parseJSON($(this).attr('idpromo'));														    
													    $("#csh_promo_code").val(selpro.promo_code);	
													    $("#csh_promo_tp").val(selpro.promo_tp);
													    $("#csh_play_main_promotion").val(selpro.play_main_promotion);
													    $("#csh_play_last_promotion").val(selpro.play_last_promotion);
													    $("#csh_get_promotion").val(selpro.get_promotion);													    
													    if(selpro.member_discount!='Y'){
													    	$('#csh_percent_discount').html('0');
													    }
													    $("#csh_discount_member").val(selpro.member_discount);
													    $("#csh_get_point").val(selpro.get_point);	
													  //*WR22072014
													    if(selpro.promo_code=='P2DR'){
													    	redeemPointSpecial(selpro.promo_code);
													    	$("#dlgRedeemPoint").dialog('close');
													    	return false;
													    }													    
													    
													    //---- start ------
													    
														$("#dlgRedeemPoint").dialog('close');
													    redeemPointDetail(selpro.promo_code,selpro.promo_tp,member_no)
													    //---- end --------		
														return false;
													});//end click
													
												}//end success function
											});//end ajax pos
											
										},
										close:function(evt,ui){
											$('.tableNavPromoPoint ul>li').navigate('destroy');											
											if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
												initFormCashier();
												initFieldOpenBill();
											}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
												evt.stopPropagation();
												evt.preventDefault();
												initFormCashier();
												initFieldOpenBill();	
											}									
										}										
								};								
								$("#dlgRedeemPoint").dialog("destroy");
								$("#dlgRedeemPoint").dialog(opts_dlgRedeemPoint);	
								$("#dlgRedeemPoint").dialog("open");
								//----------- end redeem point ----------						
							});
							$("#csh_accordion").accordion({ active:0});
						}
					}				
			);//end json
			setTimeout(function(){
				if(opts_redeempoint){
					opts_redeempoint.abort();
					jAlert('?????????????????????????????????????????????????????? Server ????????????????????????????????????????????????????????????','WARNING!',function(){
						initFieldOpenBill();
						return false;
					});	
				}
			},30000);
		}//end if	
		return false;		
	}//func

	function setNewCardMember(){
		/**
		*@desc add new card
		*@modify 21092015
		*@return
		*/
		var ops_day_new=$('#csh_ops_day').val(); 
		ops_day_new=$.trim(ops_day_new);
		switch(ops_day_new){
			case 'TH1':msg_show_sel="?????????????????????????????????????????????1";break;
			case 'TH2':msg_show_sel="?????????????????????????????????????????????2";break;
			case 'TH3':msg_show_sel="?????????????????????????????????????????????3";break;
			case 'TH4':msg_show_sel="?????????????????????????????????????????????4";break;
			case 'TU1':msg_show_sel="???????????????????????????????????????1";break;
			case 'TU2':msg_show_sel="???????????????????????????????????????2";break;
			case 'TU3':msg_show_sel="???????????????????????????????????????3";break;
			case 'TU4':msg_show_sel="???????????????????????????????????????4";break;
		}
		var dialogOpts_NewCard={
				autoOpen:false,
				width:'22%',
				height:105,
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction: 'up'},		
				closeOnEscape:false,	
				title:"<span class='ui-icon ui-icon-person'></span>????????????????????????????????????????????????????????????",
				open:function(){
					//*** check lock unlock
					if($("#csh_lock_status").val()=='Y'){
						lockManualKey();
					}else{
						unlockManualKey();
					}
					//*** check lock unlock
					//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	    			$(this).parents(".ui-dialog:first")
	    				.find(".ui-dialog-content")
	    				.css({"padding":"20px","background-color":"#c5bcdc","font-size":"26px","color":"#000"});
						
					 $(".ui-widget-overlay").live('click', function(){
				    	$("#csh_new_card").focus();
					});					
					$("#csh_new_card").val('').focus();
					$("#csh_new_card").keypress(function(evt){
						
						var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						if(key == 13) {
							evt.stopPropagation();
						    evt.preventDefault();		
						       //---- start ---
						       var $csh_newcard=$("#csh_new_card");
								var $application_id=$("#csh_application_id");
								var $csh_newcard_val=$.trim($csh_newcard.val());
								if($csh_newcard_val.length==0){
									jAlert('?????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
										$csh_newcard.focus();
										return false;
									});	
								}else{
									//*WR27032014 append check opsday old card and new card
									var ops_day_old=$('#info_apply_promo_detail').html();
									ops_day_old=$.trim(ops_day_old);
									switch(ops_day_old){
										case 'TH1':msg_show="?????????????????????????????????????????????1";break;
										case 'TH2':msg_show="?????????????????????????????????????????????2";break;
										case 'TH3':msg_show="?????????????????????????????????????????????3";break;
										case 'TH4':msg_show="?????????????????????????????????????????????4";break;
										case 'TU1':msg_show="???????????????????????????????????????1";break;
										case 'TU2':msg_show="???????????????????????????????????????2";break;
										case 'TU3':msg_show="???????????????????????????????????????3";break;
										case 'TU4':msg_show="???????????????????????????????????????4";break;
									}
									//*WR27032014 append check opsday old card and new card
									var opts_member={
											type:'post',
											url:'/sales/member/checkmember',
											data:{
												member_no:$csh_newcard_val,
												ops_day_old:ops_day_old,
												ops_day_new:ops_day_new,
												rndno:Math.random()
											},
											success:function(data){
												if(data=='1'){
													jAlert('???????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
														$csh_newcard.focus();
														return false;
													});	
												}else if(data=='2'){
													jAlert('???????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
														$csh_newcard.focus();
														return false;
													});	
												}else if(data=='3'){
													jAlert('?????????????????????????????? ' + msg_show, 'WARNING!',function(){
														$csh_newcard.focus();
														return false;
													});	
												}else if(data=='4'){
													jAlert('???????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
														$csh_newcard.focus();
														return false;
													});	
												}else if(data=='5'){
													jAlert('??????????????????????????????????????? ' + msg_show_sel, 'WARNING!',function(){
														$csh_newcard.focus();
														return false;
													});	
												}else if(data=='6'){
													jAlert('???????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
														$csh_newcard.focus();
														return false;
													});	
												}else{
													$("#csh_accordion").accordion({ active:0});
													var arr_expire_date=data.split('-');
													var expire_date=arr_expire_date[2]+"/"
														+arr_expire_date[1]+"/"+arr_expire_date[0];													
													$("#csh_expire_date").val(data);
													$("#info_member_expiredate").html(expire_date);
													//************** start paybill ***********************
													$("#csh_member_no").val($csh_newcard_val);
													$("#dlgNewCard").dialog('close');
													$("#btn_cal_promotion").trigger("click");//trigger payment
													//*************** end paybill ***********************
												}
											}
										};
									$.ajax(opts_member);
								}
						       //---- end  ---
						       return false;
						}
					});
				},
				close:function(evt,ui){
					evt.stopPropagation();
					evt.preventDefault();
					$('#dlgNewCard').dialog('destroy');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						initTblTemp();
				        initFormCashier();
						initFieldOpenBill();
						getCshTemp('Y');
						browsDocStatus();
					}else if(evt.which==27){
				        initTblTemp();
				        initFormCashier();
						initFieldOpenBill();
						getCshTemp('Y');
						browsDocStatus();
				    }
				}
		};
		$('#dlgNewCard').dialog('destroy');
		$('#dlgNewCard').dialog(dialogOpts_NewCard);			
		$('#dlgNewCard').dialog('open');
	}//func

	function setProBalance($application_id){
		/**
		*@desc
		*@return
		*/
		var $free_product_amount=$('#csh_free_product_amount').val();
		var $product_amount_type=$('#csh_product_amount_type').val();
		var opts={
					type:'post',
					url:'/sales/member/setprobalance',
					data:{
						application_id:$application_id,
						free_product_amount:$free_product_amount,
						product_amount_type:$product_amount_type,
						rnd:Math.random()
					},success:function(data){
						//reload flexigrid		
						getCshTemp('P');				
					}
				};
		$.ajax(opts);
		return false;
	}//func

	function giftsetProduct(application_id,employee_id,member_no,status_no,doc_tp){
		/**
		*@desc
		*@return
		*/		
		var $gift_set_amount=$("#csh_gift_set_amount");
		var dialogOpts_giftsetproduct = {
				autoOpen:false,
				width:'35%',
				height:'120',
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????????????? "+parseFloat($gift_set_amount.val()).toFixed(2)+" ?????????",
				open: function(){  
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc","color":"#000000"});	
						$("#dlgGiftSetAmount").html("");
						$("#dlgGiftSetAmount").load("/sales/member/giftsetproduct",function(){
							///////////////////// start content ////////////////////////////////////
							var $input_giftset_product=$("#input_giftset_product");
							var $input_giftset_quantity=$("#input_giftset_quantity");
							$input_giftset_product.val('').focus();
							$input_giftset_product.keypress(function(evt){
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								if(key == 13){
									evt.stopPropagation();
								    evt.preventDefault();	
								    $("#btn_giftset_submit").trigger('click');
								    return false;
								}
							});
							$("#btn_giftset_submit").live("click",function(e){
								e.stopPropagation();
								e.preventDefault();
								var $input_giftset_product_val=$.trim($input_giftset_product.val());
								var $input_giftset_quantity_val=$.trim($input_giftset_quantity.val());
							    // ################## start giftset product ##################
										if($input_giftset_quantity_val.length==0){
											jAlert('??????????????????????????? ?????????????????????????????????', 'WARNING!',function(){
												$input_giftset_quantity.focus();
												return false;
											});	
											return false;
										}else if($input_giftset_product_val.length==0){
											$input_giftset_product.focus();
											return false;
										}else{
											//set product to temp
											var opts={
													type:'post',
													url:'/sales/cashier/ajax',
													//async:true,
													cache:false,	
													data:{
														employee_id:employee_id,
														member_no:member_no,
														product_id:$input_giftset_product_val,
														quantity:$input_giftset_quantity_val,	
														status_no:status_no,
														product_status:'',
														promo_st:'S',
														doc_tp:doc_tp,
														application_id:application_id,
														card_status:'',
														promo_code:'',
														promo_id:'',
														get_point:'',
														discount_percent:'',
														member_percent1:'',
														member_percent2:'',
														co_promo_percent:'',
														coupon_percent:'',
														actions:'set_csh_tmp'
													},
													success:function(data){	
														var arr_data=data.split('#');
														if(arr_data[0]=='3'){
															jAlert('???????????????????????????????????? lock ??????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
																$input_giftset_product.val('').focus();
																return false;
															});	
														}else if(arr_data[0]=='2'){
															jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
																$input_giftset_product.val('').focus();
																return false;
															});	
														}else if(arr_data[0]=='0'){
															jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
																$input_giftset_product.val('').focus();
																return false;
															});	
														}else{	
															$input_giftset_product.val('');
															$input_giftset_product.focus();															
															//* ============== WR17122014 for OPPB300 ============== 
															if(application_id=='OPPB300'){
																var $gift_set_amount=$("#csh_gift_set_amount");
																var $gift_set_amount_val=$.trim($gift_set_amount.val());
																$.ajax({
																	type:'post',
																	url:'/sales/member/chkamtprosms',
																	data:{
																		promo_code:'OPPB300',											
																		product_id:$input_giftset_product_val,
																		quantity:$input_giftset_quantity_val,
																		promo_amt:$gift_set_amount_val,
																		discount:'40',
																		type_discount:'percent',
																		rnd:Math.random()
																	},success:function(data){
																		getCshTemp('P');	
																	}
																});
															}else{
																getCshTemp('P');	
															}
															//* ============== WR17122014 for OPPB300 ==============	
														}
													}
												};	
											$.ajax(opts);
											//set product to temp										
										}//if
									    // ################### end giftset product ##################
								});//click
							///////////////////// end content /////////////////////////////////////
						});
				},				
				close: function(evt,ui) {
					var $csh_net=$("#csh_net");
					if(application_id=='OPPB300'){
						var $csh_net=$("#csh_sum_amount");
					}
					var $csh_net_val=$.trim($csh_net.val());
					var $gift_set_amount=$("#csh_gift_set_amount");
					var $gift_set_amount_val=$.trim($gift_set_amount.val());
					$csh_net_val=parseFloat($csh_net_val.replace(/[^\d\.\-\ ]/g,''));
					$gift_set_amount_val=parseFloat($gift_set_amount_val);
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						if($csh_net_val<$gift_set_amount_val){
							jAlert("????????????????????????????????????????????? " + parseFloat($("#csh_gift_set_amount").val()).toFixed(2) + " Please check and try again.","WARNING!",function(){
								window.parent.$(".ui-dialog").remove();
								$('#dlgGiftSetAmount').dialog('open');
								$("#input_giftset_product").focus();
								return false;
							});	
							return false;
						}
					
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();
						if($csh_net_val<$gift_set_amount_val){
							jAlert("????????????????????????????????????????????? " + parseFloat($("#csh_gift_set_amount").val()).toFixed(2) + " Please check and try again.","WARNING!",function(){
								window.parent.$(".ui-dialog").remove();
								$('#dlgGiftSetAmount').dialog('open');
								$("#input_giftset_product").focus();
								return false;
							});	
							return false;
						}
					}

					if($("#csh_free_product").val()=='Y'){
						//------------ start free product -------------		
						freeProduct(application_id,employee_id,member_no,status_no,doc_tp);
						//------------ end free product -------------	
					}else if($("#csh_free_premium").val()=='Y'){
						//----------------- start free premium ----------
						freePremium(application_id,employee_id,member_no,status_no,doc_tp);
						//------------------ end free premium ----------
					}else{						
						//*WR 28052015
			           	var chk_member_tu_new=$('#csh_ops_day').val(); 
			          //*WR23032016 for support renew to id card
			           	if(application_id=='OPPB300'){
			           		paymentBill('01');
			           	}else if(chk_member_tu_new=='TU1' || chk_member_tu_new=='TU2' || chk_member_tu_new=='TU3' || chk_member_tu_new=='TU4'){
			           		selCoPromotion('OPS2OPT','1','1');
			           	}else{
			           		setTimeout('setNewCardMember()',400);
			           	}
						//setTimeout('setNewCardMember()',400);//*WR02012013 for promotion 2013
					}
					
				}
			};			
			$('#dlgGiftSetAmount').dialog('destroy');
			$('#dlgGiftSetAmount').dialog(dialogOpts_giftsetproduct);			
			$('#dlgGiftSetAmount').dialog('open');
		return false;
	}//func
	
	function playMstPromotion(promo_code,seq_pro){
		/**
		 * @desc
		 * @create 18022016
		 * @modify
		 * @return seq_pro
		 */
		//alert("playMstPromotion seq_pro=>" + seq_pro);
		$.ajax({
				 type:'post',
				 url:'/sales/member/playbyseqpro',
				 data:{																											  									
					 promo_code:promo_code,
					 seq_pro:seq_pro,
					 rnd:Math.random()
				 },success:function(data){						 
					 var arr_data=data.split('#');	
					 var title=arr_data[1];					 
					 if(arr_data[0]=='Y'){
						 mstPromotion(promo_code,seq_pro,data,title);
					 }else{						 
						 if(arr_data[2]=='trn_promotion_tmp1'){								
								getPmtTemp('P');
						 }else{
								getCshTemp('P');
						 }
						 setTimeout(function(){
							 lstPromotion(promo_code);
						 },800);
						
					 }
					 
				 }
		 });//ajax		
	}//func
	
	
	function lstPromotion(promo_code){		
		/**
		 * @desc 
		 * * create 22072014
		 * * modify 20102014 
		 * *??????????????????????????????????????????
		 * @return 
		 */		
		var play_last_promotion=$('#csh_play_last_promotion').val();//*WR18082015 Lucky Draw
		var status_no=$('#csh_status_no').val();		
		var member_no=$('#csh_member_no').val();
		var ops_day=$('#csh_ops_day').val();		
		$.ajax({
			type:'post',
			url:'/sales/member/sellstpromotion',
			data:{
				member_no:member_no,
				ops_day:ops_day,
				promo_code:promo_code,				
				rnd:Math.random()
			},success:function(data){				
				if($.trim(data)!=''){
					$('<div id="dlgSelLStPromotion"></div>').dialog({
				           autoOpen:true,
						   width:'65%',
						   height:'350',	
						   modal:true,
						   resizable:true,
						   closeOnEscape:false,		
				           title: "?????????????????????????????????????????????????????????????????????????????????",		
				           position:"center",
				           open:function(){
				        	   $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
								$(this).dialog('widget')
						            .find('.ui-dialog-titlebar')
						            .removeClass('ui-corner-all')
						            .addClass('ui-corner-top');
							    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
								    																			"font-size":"26px","color":"#0000FF",
								    																			"padding":"0 0 0 0"});	
							    $('#dlgSelLStPromotion').empty().html(data);	
	    			    		 $('.tableNavSelCopromotion ul li').not('.nokeyboard').navigate({
							        wrap:true
							    }).click(function(evt){		
								    evt.stopPropagation();
								    evt.preventDefault();										    
								    var sel_promo=$.parseJSON($(this).attr('idpromo'));	
								    var promo_play=sel_promo.promo_code;		
								    var promo_des=sel_promo.promo_des;
								    var unitn=sel_promo.unitn;
								    
								    $('#dlgSelLStPromotion').dialog('close');
								    /////////////////// KEY PRODUCT FREE ///////////////////////////								   
								    			
					    			$('<div id="dlgLstProduct">' + 
					    					'<center><span style="color:#336666;font-size:20px">???????????????&nbsp;:&nbsp;</span>' + 
					    					'<input type="text" size="1" value="1" id="lst_quantity_id" readonly style="width: 30px;align:center"/>' +
					    					'<span style="color:#336666;font-size:20px;">&nbsp;&nbsp;?????????????????????????????? : </span>' + 
					    					'<input type="text" size="10" id="lst_product_id" style="width: 180px;"/></center></div>').dialog({
									           autoOpen:true,
											   width:'50%',
											   height:'420',	
											   modal:true,
											   resizable:true,
											   closeOnEscape:false,		
											   title: " " + promo_des,
									           position:"center",
									           open:function(){
									        	   $(this).dialog('widget')
										            .find('.ui-dialog-titlebar')
										            .removeClass('ui-corner-all')
										            .addClass('ui-corner-top');	
												$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
												//button style	
								  			    $(this).dialog("widget").find(".ui-btndlgpos")
									                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"18px"});
										  			  $('#lst_product_id').val('').focus();
											  		  $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
														    	$("#lst_product_id").focus();
														});
										  			  $('#lst_product_id').keypress(function(evt){																    	
													    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
										   			        if(key == 13){	
												    			evt.preventDefault();
												    			evt.stopPropagation();
												    			$('#btnLstProductSubmit').trigger('click');												    			
												    			return false;
										   			        }
													    });
									           },
							    				buttons: [ 
											    	            { 
											    	                text: "OK",
											    	                id:"btnLstProductSubmit",
											    	                class: 'ui-btndlgpos', 
											    	                click: function(evt){ 
											    	                	evt.preventDefault();
																 		evt.stopPropagation();
																 		
																 		//alert("play_last_promotion=> " + play_last_promotion);
																 		
																 		var member_no=$('#csh_member_no').val();
																 		var product_id=$('#lst_product_id').val();																		 		
															 			 product_id=$.trim(product_id);																		 			
															    		if(product_id.length == 0){
														    				jAlert('?????????????????????????????????????????????????????????', '???????????????????????????',function(){
														    					$('#lst_product_id').val('').focus();
														    					return false;
														    				});	
														    				return false;
															    		}
															    		
															    		$.ajax({
															    			type:'post',
															    			url:'/sales/member/setlstpromoproduct',
															    			data:{
															    				status_no:status_no,
															    				member_no:member_no,
															    				promo_code:promo_play,															    				
															    				product_id:product_id,
															    				unitn:unitn,
															    				play_last_promotion:play_last_promotion,
															    				rnd:Math.random()
															    			},success:function(data){
															    				//----------------------START---------------------
															    				var arr_data=data.split('#');
											  									if(arr_data[0]=='1'){	
											  										//*WR08092015
											  										if(arr_data[2]=='trn_promotion_tmp1'){
											  											chk_tbl_temp='trn_promotion_tmp1';
											  											getPmtTemp('P');
											  										}else{
											  											getCshTemp('P');
											  										}
											  										setTimeout(function(){
											  											$('#lst_product_id').val('').focus();
											  										},400);
															    				}else{		
															    					//*WR04092045 Mcoupon
															    					jAlert(arr_data[1],'????????????????????????????????????????????????',function(){
															    						$('#lst_product_id').focus();
															    						setTimeout(function(){							    							
															    							if($('#lst_product_id').not(":focus")){
																	    						$('#lst_product_id').focus();
																	    					}
															    						},1000);
															    						$('#lst_product_id').val('').focus();
																						return false;
																					});	
													
															    				}//end if	
															    				//----------------------STOP ---------------------															    																				    				
															    				
															    			}
															    		});								 		
															 																		 		
										    	                }
										    	            },
										    	            { 
										    	                text: " NEXT >> ",
										    	                id:"btnNextLstPro",
										    	                class: 'ui-btndlgpos', 
										    	                click: function(evt){ 
										    	                	evt.preventDefault();
															 		evt.stopPropagation();		
															 		$('#dlgLstProduct').dialog('close');
										    	                }
										    	          }
										    	  ]			
				    							,close:function(evt){
				    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
														evt.stopPropagation();
							    						evt.preventDefault();
													}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
														evt.stopPropagation();
							    						evt.preventDefault();
													}
				    								$('#dlgLstProduct').remove();	
				    								lstPromotion(promo_code);
									           }
				    				});
								   /////////////////// KEY PRODUCT FREE ///////////////////////////
							    });
				           },				           
				           buttons: [ 			
				    	                { 
					    	                
					    	                text: "PAYMENT",
					    	                id:"btnPayBySelLstPromo",
					    	                class: 'ui-btndlgpos', 
					    	                click: function(evt){ 
					    	                	evt.preventDefault();
										 		evt.stopPropagation();	
										 		//alert("promo_code => " + promo_code);										 		
										 		
										 		
										 		if(status_no=='01' && $('#csh_card_type').val()=='MBC'){
										 			setTimeout('setNewCardMember()',400);	
										 			$('#dlgSelLStPromotion').dialog('close');
										 		}else if(status_no=='01' && $('#csh_card_type').val()=='IDC'){
										 			paymentBill(status_no);		
										 			$('#dlgSelLStPromotion').dialog('close');
										 		}else if(play_last_promotion=='Y'){
										 			//*WR18082015 for support Lucky Draw
										 			$.ajax({
										 				type:'post',
										 				url:'/sales/accessory/need2playpro',
										 				data:{
										 					promo_code:promo_code,
										 					rnd:Math.random()
										 				},success:function(data){
										 					var arr_needpro=data.split("#");
										 					if(arr_needpro[0]=='N'){
										 						jAlert('NEED TO PLAY ' + arr_needpro[1] , 'WARNING!',function(){
											    					
											    					return false;
											    				});	
										 						return false;
										 					}else{
										 						callLstChkPro();
										 						$('#dlgSelLStPromotion').dialog('close');
										 					}
										 				}
										 			});
										 			//callLstChkPro();
										 		}else{
										 			//alert('C');
										 			paymentBill(status_no);
										 			$('#dlgSelLStPromotion').dialog('close');
										 		}
										 		//$('#dlgSelLStPromotion').dialog('close');										 		
					    	                }
				    	                }
				    	  ],
				           close:function(evt){
				        	   if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
									evt.stopPropagation();
									evt.preventDefault();									
									//paymentBill(status_no);
									if(status_no=='01' && $('#csh_card_type').val()=='MBC'){
							 			setTimeout('setNewCardMember()',400);			 			
							 		}else if(status_no=='01' && $('#csh_card_type').val()=='IDC'){
							 			paymentBill(status_no);			 			
							 		}else if(play_last_promotion=='Y'){
							 			//*WR18082015 Lucky Draw
							 			callLstChkPro();
							 		}else{
							 			paymentBill(status_no);
							 		}
								}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
									evt.stopPropagation();
									evt.preventDefault();									
									paymentBill(status_no);
								}
				        	   $('.tableNavSelCopromotion ul').navigate('destroy');
				        	   $('#dlgSelLStPromotion').remove();
				           } 
				    });			    			
					//select co promotion
				}else{			
					//$('#csh_application_type').val('');//NEW=???????????????????????????, ALL=?????????????????????
					//$('#csh_card_type').val('');//MBC=??????????????????????????????, IDC=?????????????????????????????????????????????????????????
					if(status_no=='01' && $('#csh_card_type').val()=='MBC'){
			 			setTimeout('setNewCardMember()',400);			 			
			 		}else if(status_no=='01' && $('#csh_card_type').val()=='IDC'){
			 			paymentBill(status_no);			 			
			 		}else if(play_last_promotion=='Y'){
			 			//*WR18082015 Lucky Draw
			 			callLstChkPro();
			 		}else{
			 			paymentBill(status_no);
			 		}
				}
			}
		});
	}//func
	
	function mstPromotion(promo_code,seq_pro,s_play,title){
		/**
		 * @desc for support new member line pro
		 * @modify : 17022016
		 * @return null
		 */	
		
		if($("#dlgMstPdtPromotion").dialog( "isOpen" )===true) {
         	return false;
         }//if
		
		//-------------------- start--------------------------		
		
		var play_last_pro=$("#csh_play_last_promotion").val();
		//*WR31032017
		var is_readonly="readonly";
		var btn_titles="NEXT >>";
		if(promo_code=='OX02460217'){
			//btn_titles=">> ????????????????????? Value Set for Tourists/????????????????????????";
			is_readonly="";
		}else if(promo_code=='OX02250117' || promo_code=='OX02580118'){
			  is_readonly="";
		}
		
		if(title==''){
			title="Scan items";
		}
		
		var seq_msg_show="";
		
		
		if(promo_code=="OK06010318"){
			if(seq_pro=='1'){
				seq_msg_show="???????????? Perfect Matte UV Protection For Face SPF50(26605) 1 ????????????";
			}else if(seq_pro=='2'){
				seq_msg_show="???????????? All Day Sun Powder(25810,25811,25824) 1 ????????????";
			}
			
			seq_msg_show = seq_msg_show + " <br><span><u>Scan is finished. Press next button.</u></span>";
		}	
		
		
		if(promo_code=="OK02500118"){
			if(seq_pro=='1'){
				seq_msg_show="???????????? Velvet Eye Colours(26648-26677) 1 ????????????";
			}else if(seq_pro=='2'){
				seq_msg_show="???????????? Velvet Cheek Colours(26678-26692) 1 ????????????";
			}else if(seq_pro=='3'){
				seq_msg_show="???????????? Velvet Eye Colours(26648-26677) 1 ????????????";
			}else if(seq_pro=='4'){
				seq_msg_show="???????????? Velvet Cheek Colours(26678-26692) 1 ????????????";
			}
			
			seq_msg_show = seq_msg_show + " <br><span><u>????????????????????????????????????????????????????????????????????????</u></span>";
		}	
		
		
		
		var chk_tbl_temp="";
		$('<div id="dlgMstPdtPromotion">' + 
				
				'<center><span id="seq_msg_show" style="color:#B22222;font-size:26px;">&nbsp;&nbsp; ' +  seq_msg_show + ' </span></center><br>' + 
				
				'<center><span style="color:#336666;font-size:20px">QTY.&nbsp;:&nbsp;</span>' + 
				
				'<input type="text" size="3" value="1" id="keyin_mstquantity_id" ' + is_readonly + ' style="width: 40px;text-align:center"/>' +
				
				'<span style="color:#336666;font-size:20px;">&nbsp;&nbsp;PRODUCT CODE : </span>' + 
				
				'<input type="text" size="10" id="keyin_mstproduct_id" style="width: 180px;"/></center></div>').dialog({
	           autoOpen:true,
			   width:'50%',
			   height:'280',	
			   modal:true,
			   resizable:true,
			   closeOnEscape:false,		
	           title:title,
	           position:"center",
	           open:function(){
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');	
					$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
					//button style	
	  			    $(this).dialog("widget").find(".ui-btndlgpos")
	                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"18px"});
		  			  $('#keyin_mstproduct_id').val('').focus();
			  		  $(this).dialog("widget").find(".ui-widget-overlay").live('click', function(){
						    	$("#keyin_mstproduct_id").focus();
							});
			  		  
			  		  if(seq_msg_show!=""){
			  			$("#seq_msg_show").blinky({ count: 3 });
			  		  }
			  		  
				  		$('#keyin_mstquantity_id').keypress(function(evt){																    	
					    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
		   			        if(key == 13){	
				    			evt.preventDefault();
				    			evt.stopPropagation();
				    			var keyin_mstquantity_id=$('#keyin_mstquantity_id').val();
				    			keyin_mstquantity_id=$.trim(keyin_mstquantity_id);																		 			
					    		if(keyin_mstquantity_id.length<1){
				    				jAlert('Please specify Product Quantity', 'WARNING!',function(){
				    					$('#keyin_mstquantity_id').val('').focus();
				    					return false;
				    				});	
				    				return false;
					    		}
				    			$('#keyin_mstproduct_id').focus();
				    			return false;
		   			        }
					  });
			  		  $('#keyin_mstproduct_id').keypress(function(evt){																    	
					    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
		   			        if(key == 13){	
				    			evt.preventDefault();
				    			evt.stopPropagation();
								if(chk_tbl_temp=='trn_promotion_tmp1'){
									lastdelpro();
								}				    			
				    			$('#btnCommitMstPro').trigger('click');
				    			return false;
		   			        }
					    });
	           },
				buttons: [ 
			    	            { 
			    	                text: "SAVE",
			    	                id:"btnCommitMstPro",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	evt.preventDefault();
								 		evt.stopPropagation();
								 		var product_id=$('#keyin_mstproduct_id').val();
								 		product_id=$.trim(product_id);																		 			
							    		if(product_id.length<1){
						    				jAlert('Please scan the code.', 'WARNING!',function(){
						    					$('#keyin_mstproduct_id').val('').focus();
						    					return false;
						    				});	
						    				return false;
							    		}
							    		
							    		var member_no=$('#csh_member_no').val();																													    	
							    		var status_no=$('#csh_status_no').val();
							    		var member_percent=$('#csh_percent_discount').html();
							    		var get_point =$('#csh_get_point').val();
							    		var keyin_mstquantity=$('#keyin_mstquantity_id').val();
							    		keyin_mstquantity=$.trim(keyin_mstquantity);
							    		
							    		//---------------------- save to temp --------------------------	
							    		
							    		 $.ajax({
			  								 type:'post',
			  								 url:'/sales/member/setmstpdtpro',
			  								 data:{
			  									 member_no:member_no,																									  									
			  									 promo_code:promo_code,
			  									 product_id:product_id,
			  									 quantity:keyin_mstquantity,
			  									 seq_pro:seq_pro,
			  									 status_no:status_no,
			  									 member_percent:member_percent,
			  									 play_last_pro:play_last_pro,
			  									 rnd:Math.random()
			  								 },success:function(data){	
			  									var arr_data=data.split('#');
			  									if(arr_data[0]=='1'){	
			  										//*WR08092015
			  										if(arr_data[2]=='trn_promotion_tmp1'){
			  											chk_tbl_temp='trn_promotion_tmp1';
			  											getPmtTemp('P');
			  										}else{
			  											getCshTemp('P');
			  										}
			  										setTimeout(function(){
			  											$('#keyin_mstproduct_id').val('').focus();
			  										},400);
							    				}else{		
							    					//*WR04092045 Mcoupon
							    					jAlert(arr_data[1],'WARNING!',function(){
							    						$('#keyin_mstproduct_id').focus();
							    						setTimeout(function(){							    							
							    							if($('#keyin_mstproduct_id').not(":focus")){
									    						$('#keyin_mstproduct_id').focus();
									    					}
							    						},1000);
							    						$('#keyin_mstproduct_id').val('').focus();
														return false;
													});	
					
							    				}//end if	
			  								 }
			  							 });//ajax		
							    		
							    		//--------------------- save to temp ---------------------------	
							    		
			  							 
		    	                }
		    	            },
//		    	            { 
//		    	                text:"???????????????",
//		    	                id:"btnEndMstPro",
//		    	                class: 'ui-btndlgpos', 
//		    	                click: function(evt){ 
//		    	                	evt.preventDefault();
//							 		evt.stopPropagation();	
//							 		
//							 		$.ajax({
//										type:'post',
//										url:'/sales/cashier/countdiarytemp',										
//										cache:false,
//										data:{
//											rnd:Math.random()
//										},
//										success:function(data){														
//											var arr_data=data.split('#');
//											chk_tbl_temp=arr_data[1];
//											$('#dlgMstPdtPromotion').dialog('close');
//											
//										}
//									});		
//							 		
//		    	                }
//		    	            },
		    	            { 
		    	                text:btn_titles,
		    	                id:"btnNextMstPro",
		    	                class: 'ui-btndlgpos', 
		    	                click: function(evt){ 
		    	                	evt.preventDefault();
							 		evt.stopPropagation();	
							 		
							 		//*WR13022018
							 		//seq_pro++;
							 		
							 		//------------check net amount --------------------
							 		$.ajax({
							 			type:'post',
							 			url:'/sales/member/chkcompbyseqpro',
							 			data:{
							 				seq_pro:seq_pro,
							 				promo_code:promo_code,
							 				play_last_pro:play_last_pro,
							 				rnd:Math.random()
							 			},
							 			success:function(data){								 				
							 				if(data=='Y'){
							 					
										 		$.ajax({
													type:'post',
													url:'/sales/cashier/countdiarytemp',										
													cache:false,
													data:{
														rnd:Math.random()
													},
													success:function(data){														
														var arr_data=data.split('#');
														chk_tbl_temp=arr_data[1];
														$('#dlgMstPdtPromotion').dialog('close');
														
													}
												});		
										 		
												//$('#dlgMstPdtPromotion').dialog('close');												
											}else{
												jAlert(data, 'WARNING!',function(){																						
													$('#keyin_mstproduct_id').val('').focus();
													return false;
												});
											}
										 }
							 		});
							 		
							 		//------------check net amount --------------------
							 		
							 		//$('#dlgMstPdtPromotion').dialog('close');
	    	                }
	    	            }
		    	  ]			
				,beforeClose:function(evt){
					
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
						evt.stopPropagation();
						evt.preventDefault();
						//alert("You click close! promo_code : " +promo_code + " seq : " + seq_pro);return false;
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();  						
					}
					
					
					//*WR10092015
					
					if(chk_tbl_temp=='trn_promotion_tmp1'){
						getPmtTemp('P');
					}else{
						getCshTemp('P');
					}						
					//*WR14012015 =======================
					var start_baht=$('#csh_start_baht').val();
					var end_baht=$('#csh_end_baht').val();
					var buy_type=$("#csh_buy_type").val();//Gross,Net
					var buy_status=$("#csh_buy_status").val();//G >=,L<
					if(buy_type=='N' ){
						var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
				 		net_amt=parseFloat(net_amt);
						var start_baht=start_baht.replace(/[^\d\.\-\ ]/g,'');
						 start_baht=parseFloat(start_baht);
						 if(net_amt<start_baht){
							 jAlert("Minimum net amount required. " + start_baht + " Riel", 'WARNING!',function(){																						
								 $('#pdt_otherpro').val('').focus();
									return false;
							});
							 return false;																		    										 
						 }
					}						
					//*WR14012015 ===========================
				}
				,close:function(evt){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
						evt.stopPropagation();
						evt.preventDefault();
					}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
						evt.stopPropagation();
						evt.preventDefault();  						
					}
					
					if(onSales()==0){
						jAlert('No sale transaction can not be found.', 'WARNING!',function(){
							initFormCashier();
							initFieldOpenBill();
							$("#csh_member_no").focus();
	    					return false;
	    				});		 
						return false;
					}
					
					$('#dlgMstPdtPromotion').remove();	
					//alert("s_play: " + s_play);//??????????????????????????? case n ??????????????????
					
					if(s_play=='N'){						
						paymentBill('01');
					}else{						
						seq_pro++;						
						playMstPromotion(promo_code,seq_pro);
					}
					
	           }
		});
	
		//-------------------- end  --------------------------
		
		return false;		
		
	}//func
	
	function freeProduct(application_id,employee_id,member_no,status_no,doc_tp){
		/**
		*@desc for support OPPL300
		*@modify : 20012015
		*@return
		*/
		var $free_product=$("#csh_free_product");
		var $free_product_amount=$("#csh_free_product_amount");
		var $product_amount_type=$("#csh_product_amount_type");
		var $free_premium=$("#csh_free_premium");
		var $free_premium_amount=$("#csh_free_premium_amount");
		var $premium_amount_type=$("#csh_premium_amount_type");
		var dialogOpts_freeproduct = {
				autoOpen:true,
				width:'35%',
				height:'120',
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction:'up'},		
				closeOnEscape:false,	
				title:"<span class='ui-icon ui-icon-cart'></span>????????????????????????????????????",
				open: function(){  
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc","color":"#000000"});	
						$("#dlgFreeProduct").empty().html("");
						$("#dlgFreeProduct").load("/sales/member/freeproduct",function(){
							///////////////////// start content ////////////////////////////////////
							$("#csh_product_id").val('');
							$("#input_free_product").val('');
							var $c_amount=0.00;
							var $input_free_product=$("#input_free_product");
							var $input_free_quantity=$("#input_free_quantity");
							
							//*WR25122015
							$("#input_free_product_quantity").keypress(function(evt){
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								if(key == 13){		
									evt.stopImmediatePropagation();
								    evt.preventDefault();	
								    var qty_free_product=$("#input_free_product_quantity").val();
								    if(qty_free_product=='0'){
								    	jAlert('??????????????????????????? ?????????????????????????????????', 'WARNING!',function(){
								    		$("#input_free_product_quantity").focus();
											return false;
										});	
								    }
								    $("#input_free_product").focus();
								    return false;
								}
							});
							
							$input_free_product.val('').focus();
							$input_free_product.live('keypress',function(evt){
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								if(key == 13){		
									evt.stopImmediatePropagation();
								    evt.preventDefault();	
								    $("#btn_free_product_submit").trigger('click');
								    return false;
								}
							});
							$("#btn_free_product_submit").die();
							$("#btn_free_product_submit").live("click",function(e){
								e.stopPropagation();
								//e.stopImmediatePropagation();
								e.preventDefault();
								//################## start free_product ##################
									var $input_free_product_val=$.trim($input_free_product.val());
									var $input_free_product_quantity=$("#input_free_product_quantity");
									var $input_free_product_quantity_val=$.trim($input_free_product_quantity.val());
									if($input_free_product_quantity_val.length==0){
										jAlert('??????????????????????????? ?????????????????????????????????', 'WARNING!',function(){
											$input_free_product_quantity.focus();
											return false;
										});	
										return false;
									}else if($input_free_product_val.length==0){
										$input_free_product.focus();
										return false;
									}else{
										var status_no=$('#csh_status_no').val();//WR09012013
										var opts_chkproduct={
												type:'post',
												url:'/sales/cashier/product',	
												data:{
													product_id:$input_free_product_val,
													quantity:$input_free_product_quantity_val,
													status_no:status_no,
													action:"checkproductexist"
												},
												success:function(data){
													if(data==1){
														jAlert('This product code is not available. Please check and try again.', 'WARNING!',function(){
															$input_free_product.val('').focus();
															return false;
														});					
														return false;
													}else if(data==2){
														jAlert('????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){						
															$input_free_product.val('').focus();
															return false;							
														});	
														return false;						
													}else if(data==3){
														jAlert('???????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){						
															$input_free_product.val('').focus();	
															return false;							
														});			
														return false;				
													}else{
														
														//* ================ WR17122014 ==================
														if(application_id=='OPPC300'){				
																$.ajax({
																	type:'post',
																	url:'/sales/member/getproductprice',
																	data:{
																		product_id:$input_free_product_val,
																		rnd:Math.random()
																	},success:function(data2){
																			var price_check=parseFloat(data2);
																			if(price_check > parseFloat($free_product_amount.val())){
																				jAlert("?????????????????????????????????????????? " + parseFloat($free_product_amount.val()).toFixed(2) + " Please check and try again.","WARNING!",function(){
																					window.parent.$(".ui-dialog").remove();
																					$('#dlgFreeProduct').dialog('open');
																					$("#input_free_product").focus();
																					return false;
																				});	
																				return false;
																			}else{
																				//------------------------------- START NEW ---------------------------------
																				
																				$input_free_product.val(data);
																				var get_point='';
																				if($('#csh_xpoint').val()!='0'){
																					var get_point='Y';
																				}
																				var opts_setcshtemp={
																						type:'post',
																						url:'/sales/cashier/ajax',
																						cache: false,	
																						data:{
																							employee_id:employee_id,
																							member_no:member_no,
																							product_id:data,
																							quantity:$input_free_product_quantity_val,	
																							status_no:status_no,
																							product_status:'',
																							promo_st:'P',
																							promo_tp:'F',
																							doc_tp:doc_tp,
																							application_id:application_id,
																							card_status:'',
																							promo_code:'',
																							promo_id:'',
																							get_point:get_point,
																							discount_percent:'',
																							member_percent1:'',
																							member_percent2:'',
																							co_promo_percent:'',
																							coupon_percent:'',
																							actions:'set_csh_tmp'
																						},
																						success:function(data){	
																							var arr_data=data.split('#');
																							$c_amount=$c_amount+parseFloat(arr_data[1]);		
																							$('#dlgFreeProduct').dialog('close');
																							setProBalance(application_id);
																						}
																					};	
																				$.ajax(opts_setcshtemp);
																				//end setCshTemp
																				//------------------------------- START NEW ---------------------------------
																			}
																			return false;
																	}
																	
																});
														}else if(application_id=='OPPH300' || application_id=='OPPHC300' || application_id=='OPPHI300' || 
																application_id=='OPDTAC300' || application_id=='OPKTC300' || application_id=='OPTRUE300'){
																//check group product to play
																var product_id=data;
																$.ajax({
																	type:'post',
																	url:'/sales/member/chkprodgroup',
																	data:{
																		promo_code:application_id,
																		product_id:product_id,
																		rnd:Math.random()
																	},success:function(data){
																		if(data=='N'){
																			jAlert("????????????????????????????????????????????????????????????????????? Body/Hair ","WARNING!",function(){
																				$("#input_free_product").val('').focus();
																				return false;
																			});	
																			return false;
																		}else{
																			//---------------------- save to temp --------------------------
																			
																			$input_free_product.val(product_id);
																			var get_point='';
																			if($('#csh_xpoint').val()!='0'){
																				var get_point='Y';
																			}
																			
																			var opts_setcshtemp={
																					type:'post',
																					url:'/sales/cashier/ajax',
																					cache: false,	
																					data:{
																						employee_id:employee_id,
																						member_no:member_no,
																						product_id:product_id,
																						quantity:$input_free_product_quantity_val,	
																						status_no:status_no,
																						product_status:'',
																						promo_st:'P',
																						doc_tp:doc_tp,
																						application_id:application_id,
																						card_status:'',
																						promo_code:'',
																						promo_id:'',
																						get_point:get_point,
																						discount_percent:'',
																						member_percent1:'',
																						member_percent2:'',
																						co_promo_percent:'',
																						coupon_percent:'',
																						actions:'set_csh_tmp'
																					},
																					success:function(data){	
																						var arr_data=data.split('#');
																						$c_amount=$c_amount+parseFloat(arr_data[1]);	
																						 if($c_amount>=parseFloat($free_product_amount.val()) && 
																								 application_id!='OPDTAC300' && application_id!='OPKTC300' && application_id!='OPTRUE300'){
																							$('#dlgFreeProduct').dialog('close');
																							setProBalance(application_id);
																							return false;
																						}else{
																							getCshTemp('P');	
																							$input_free_product.val('').focus();
																						}
																					}
																				};	
																			$.ajax(opts_setcshtemp);
																			
																			//--------------------- save to temp ---------------------------
																		}
																	}
																});
															
															}else if(application_id=='OPPL300' || application_id=='OPMGMC300' || application_id=='OPMGMI300' || 
																	application_id=='OPPLC300' || application_id=='OPPLI300'){
																	var product_id=data;
																	$input_free_product.val(product_id);
																	var get_point='';
																	if($('#csh_xpoint').val()!='0'){
																		var get_point='Y';
																	}
																	//---------------------- save to temp --------------------------																	 
																	$.ajax({
																			type:'post',
																			url:'/sales/cashier/ajax',
																			cache: false,	
																			data:{
																				employee_id:employee_id,
																				member_no:member_no,
																				product_id:product_id,
																				quantity:$input_free_product_quantity_val,	
																				status_no:status_no,
																				product_status:'',
																				promo_st:'P',
																				doc_tp:doc_tp,
																				application_id:application_id,
																				card_status:'',
																				promo_code:'',
																				promo_id:'',
																				get_point:get_point,
																				discount_percent:'PERCENT',
																				discount:'50',
																				member_percent1:'',
																				member_percent2:'',
																				co_promo_percent:'',
																				coupon_percent:'',
																				actions:'line_set_csh_tmp'
																			},
																			success:function(data){	
																				//alert(data + " ====> " + data);
																				var arr_data=data.split('#');
																				if(arr_data[0]=="E_QTYLIMIT"){
																					 jAlert(arr_data[1],"WARNING!",function(){
																						 	getCshTemp('P');	
																							$input_free_product.val('').focus();
																							return false;
																						});	
																				 	return false;
																				}
																				
																		 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
																		 		amount=parseFloat(amount);
																		 		var qty_chk=parseInt(arr_data[3]);//??????????????????????????????????????????
																		 		if(amount>=parseFloat($free_product_amount.val()) && qty_chk==3 && 
																		 				application_id!='OPMGMC300' && application_id!='OPMGMI300' && 
																		 				application_id!='OPPLC300' && application_id!='OPPLI300'){
																		 			getCshTemp('P');	
																		 			$('#dlgFreeProduct').dialog('close');
																					return false;
																		 		}else{
																		 			getCshTemp('P');	
																					$input_free_product.val('').focus();
																		 		}
																				 
																			}
																	});//end ajax
																	//--------------------- save to temp ---------------------------																
															}else{													
																$input_free_product.val(data);//product id
																var get_point='';
																if($('#csh_xpoint').val()!='0'){
																	var get_point='Y';
																}
																var opts_setcshtemp={
																		type:'post',
																		url:'/sales/cashier/ajax',
																		cache: false,	
																		data:{
																			employee_id:employee_id,
																			member_no:member_no,
																			product_id:data,
																			quantity:$input_free_product_quantity_val,	
																			status_no:status_no,
																			product_status:'',
																			promo_st:'P',
																			doc_tp:doc_tp,
																			application_id:application_id,
																			card_status:'',
																			promo_code:'',
																			promo_id:'',
																			get_point:get_point,
																			discount_percent:'',
																			member_percent1:'',
																			member_percent2:'',
																			co_promo_percent:'',
																			coupon_percent:'',
																			actions:'set_csh_tmp'
																		},
																		success:function(data){	
																			var arr_data=data.split('#');
																			$c_amount=$c_amount+parseFloat(arr_data[1]);	
																			 if(application_id!='OPPGI300' && $c_amount>=parseFloat($free_product_amount.val())){
																				$('#dlgFreeProduct').dialog('close');
																				setProBalance(application_id);
																				return false;
																			}else{
																				getCshTemp('P');	
																				$input_free_product.val('').focus();
																			}
																		}
																	};	
																$.ajax(opts_setcshtemp);
																//end setCshTemp																
															}//end else opph300												
														//* ================ WR17122014 ==================
														//end setCshTemp
													}
											}
										};
										$.ajax(opts_chkproduct);																				
									}
							    // ################### end free_product ##################
									return false;//WR09012013
								});//click
							///////////////////// end content /////////////////////////////////////
						});
				},				
				close: function(evt,ui) {
					var $csh_net=$("#csh_net");
					//*WR20012015
					if($product_amount_type.val()=='G'){
						$csh_net=$('#csh_sum_amount');
					}
					//*WR27062016
					if($product_amount_type.val()=='N'){
						$csh_net=$('#csh_net');
					}
					var $csh_net_val=$.trim($csh_net.val());
					$csh_net_val=parseFloat($csh_net_val.replace(/[^\d\.\-\ ]/g,''));
					
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						if($csh_net_val<parseFloat($free_product_amount.val())){
							jAlert("????????????????????????????????????????????? " + parseFloat($free_product_amount.val()).toFixed(2) + " Please check and try again.","WARNING!",function(){
								window.parent.$(".ui-dialog").remove();
								$('#dlgFreeProduct').dialog('open');
								$("#input_free_product").focus();
								return false;
							});	
							return false;
						}else{
							//need to check
							if(application_id!='OPPL300' || application_id=='OPMGMC300' || application_id=='OPMGMI300' || application_id=='OPPLC300' || application_id=='OPPLI300'){
								setProBalance(application_id);
							}
						}
					}else if(evt.which==27){
					    evt.stopPropagation();
					    evt.preventDefault();
					    if($csh_net_val<parseFloat($free_product_amount.val())){
						    jAlert("????????????????????????????????????????????? " + parseFloat($free_product_amount.val()).toFixed(2) + " Please check and try again.","WARNING!",function(){
								window.parent.$(".ui-dialog").remove();
								$('#dlgFreeProduct').dialog('open');
								$("#input_free_product").focus();
								return false;
							});	
							return false;
						}else{
							setProBalance(application_id);
						}
					}
					
					if($free_premium.val()=='Y'){						
						//----------------- start free premium ----------
						$("#input_free_product").val('');
						freePremium(application_id,employee_id,member_no,status_no,doc_tp);
						//------------------ end free premium ----------
					}else{
						//*WR 03062015
			           	var chk_member_tu_new=$('#csh_ops_day').val();	
			          //*WR 22032016
			           	if(application_id=='OPPD300' || application_id=='OPPA300' || 
			           			application_id=='OPPC300' || application_id=='OPPGI300' || 
			           			application_id=='OPGNC300' || application_id=='OPDTAC300' || application_id=='OPKTC300' || application_id=='OPTRUE300'){
			           		paymentBill('01');
			           	}else if(chk_member_tu_new=='TU1' || chk_member_tu_new=='TU2' || chk_member_tu_new=='TU3' || chk_member_tu_new=='TU4'){
			           		selCoPromotion('OPS2OPT','1','1');
			           	}else if(application_id=='OPPLC300' || application_id=='OPPLI300' || application_id=='OPMGMI300' || application_id=='OPPHI300'){
			           		selCoPromotion(application_id,'1','1');
			           	}else{
			           		setTimeout('setNewCardMember()',400);
			           	}
						//setTimeout('setNewCardMember()',400);
					}//end if free_premium
				}
			};			
			$('#dlgFreeProduct').dialog('destroy');
			$('#dlgFreeProduct').dialog(dialogOpts_freeproduct);			
			$('#dlgFreeProduct').dialog('open');
		     return false;
	}//func

	function freePremium(application_id,employee_id,member_no,status_no,doc_tp){
		/**
		*@desc : ???????????????????????????????????????????????? ???????????????????????????????????? premium
		*@return
		*/
		var $free_product=$("#csh_free_product");
		var $free_product_amount=$("#csh_free_product_amount");
		var $product_amount_type=$("#csh_product_amount_type");
		var $free_premium=$("#csh_free_premium");
		var $free_premium_amount=$("#csh_free_premium_amount");
		var $premium_amount_type=$("#csh_premium_amount_type");
		var c_free_premium_amount=0.00;//count free premium amount by product id
		var dialogOpts_freepremium = {
				autoOpen:true,
				width:'35%',
				height:'120',
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"<span class='ui-icon ui-icon-cart'></span>????????????????????????????????? "+parseFloat($free_premium_amount.val()).toFixed(2)+" ?????????",
				open: function(){  
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc","color":"#000000"});	
						$("#dlgFreePremium").html("");
						$("#dlgFreePremium").load("/sales/member/freepremium",function(){
							///////////////////// start content ////////////////////////////////////
							var $input_free_premium=$("#input_free_premium");
							var $input_free_premium_quantity=$("#input_free_premium_quantity");																								
							$input_free_premium.val('').focus();
							$input_free_premium.keypress(function(evt){
								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								if(key == 13) {
								    evt.preventDefault();
								    $("#btn_free_premium_submit").trigger('click');
								    return false;
								}
							});
							$("#btn_free_premium_submit").click(function(e){
								e.preventDefault();
								var opts_freepremium={
											type:'post',
											url:'/sales/member/productfreepremium',
											data:{
													free_premium_amount:$free_premium_amount.val(),
													premium_amount_type:$premium_amount_type.val(),
													product_id:$input_free_premium.val(),
													quantity:$input_free_premium_quantity.val(),
													application_id:application_id,
													rnd:Math.random()																														
											},
											success:function(data){
												var arr_fp=data.split(',');						
												$input_free_premium.val(arr_fp[3]);					
												if(!isNaN(arr_fp[1])){
													c_free_premium_amount=parseFloat(c_free_premium_amount)+parseFloat(arr_fp[1]);
												}
												
												if(c_free_premium_amount>parseFloat($free_premium_amount.val())){
													c_free_premium_amount=parseFloat(c_free_premium_amount)-parseFloat(arr_fp[1]);
													jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
														$input_free_premium.focus().select();
								        				return false;
								        			});	
													return false;
												}else
												if(arr_fp[0]=='4'){
													jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
														$input_free_premium.focus().select();
								        				return false;
								        			});	
												}else
												if(arr_fp[0]=='1'){
													jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								           				$input_free_premium.focus().select();
								        				return false;
								        			});	
												}else
												if(arr_fp[0]=='2'){
													jAlert('???????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								           				$input_free_premium.focus().select();
								        				return false;
								        			});	
												}else
													if(arr_fp[0]=='3'){
														jAlert('??????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
									           				$input_free_premium.focus().select();
									        				return false;
									        			});	
												}else if(arr_fp[0]=='5'){
													//
													var opts={
															type:'post',
															url:'/sales/member/setcshtemp',	
															cache: false,
															data:{
																employee_id:employee_id,
																member_no:member_no,
																application_id:$("#csh_application_id").val(),
																product_id:arr_fp[3],
																product_status:'',
																promo_st:'F',
																quantity:$("#input_free_premium_quantity").val(),	
																price:arr_fp[1],
																status_no:status_no,
																doc_tp:doc_tp,
																actions:'set_csh_tmp_free_premium'
															},
															success:function(data){	
																getCshTemp('P');	
																$input_free_premium_quantity.val('1');
																$input_free_premium.val('').focus();
															}
													};
													$.ajax(opts);
													//
												}
											}
										};
									$.ajax(opts_freepremium);
							});
							///////////////////// end content /////////////////////////////////////
						});
				},				
				close: function(evt,ui) {
					evt.stopPropagation();
					evt.preventDefault();
					$("#input_free_premium_quantity").val('1');
					$("#input_free_premium").val('');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						setTimeout('setNewCardMember()',400);
					}else if(evt.which==27){
					    evt.stopPropagation();
					    setTimeout('setNewCardMember()',400);
					}
				}
			};			
			$('#dlgFreePremium').dialog('destroy');
			$('#dlgFreePremium').dialog(dialogOpts_freepremium);			
			$('#dlgFreePremium').dialog('open');
		return false;
	}//func
	
	function getCatProduct(application_id,product_no,product_seq,product_sub_seq,pn,ps){
		/**
		 *@param String application_id : id of catalog exam OPPN300,OPPG300
		 *@param Integer maxno : max value of product_no ????????????????????????????????????????????????????????????????????????
		 *@param Integer i : number of round maxno
		 *@param Integer product_seq : ???????????????????????????????????? product_no
		 *@param Integer j : product_sub_seq number of round product_seq
		 *@return void
		 */	
		var csh_application_type= $('#csh_application_type').val();//NEW,ALL
		var dialogOpts_MemberCatalogList={
				autoOpen:false,
				width:'70%',
				height:450,
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction: 'up'},		
				closeOnEscape:true,	
				title:"<span class='ui-icon ui-icon-cart'></span>?????????????????????????????????",
				open:function(){
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 0.05em 0 0.05em","background-color":"#c5bcdc","font-size":"27px","color":"#000"});
					$("#dlgMemberCatalogList").html("");
					$.ajax({
						type:'post',
						url:'/sales/member/cataloglist',
						async:true,
						cache:false,
						data:{
							ajaxI:i,
							application_id:application_id,
							product_no:product_no,
							product_seq:product_seq,
							product_sub_seq:product_sub_seq,
							pn:pn,
							ps:ps,
							now:Math.random()
						},
						success:function(data){
							$("#dlgMemberCatalogList").html(data);
							///////// start ///////////
							//$(this).parent().find('select, input, textarea').blur();
							$('.tableNavCatList ul li').not('.nokeyboard').navigate({
						        wrap:true
						    }).click(function(e){
							    e.preventDefault();
							    e.stopPropagation();
							    //---- start ------
							  //*WR05022014 OPPN300 Revised
								var str_des=$(this).attr('pdt_desc');
								var arr_des=str_des.split('_');
								var status_wcgif=arr_des[1];
								//*WR24032014 for ops tues day
								var chk_ops_day_new=arr_des[2];
								var employee_id=$("#csh_cashier_id").val();
								var member_no=$("#csh_member_no").val();
								var status_no=$("#csh_status_no").val();								
								var doc_tp=$("#csh_doc_tp").val();
								if($(this).attr("idp")==''){
									$('div.tableNavCatList ul>li').navigate('destroy');
									$("#dlgMemberCatalogList").dialog("close");
									setTimeout(function(){cancelBill();},400);
									return false;
								}//end if
								var arr_data=$(this).attr("idp").split('#');
								//*WR29042013
								var clsp=$(this).attr('class');
								var arr_clsp=clsp.split(' ');
								var chk_ops="N";
								$(arr_clsp).each(function(ci,cc){
									if(cc=='img_disabled'){
										chk_ops="Y";
										return false;
									}
								});
								if(chk_ops=='Y'){
									jAlert('??????????????????????????????????????? ' + arr_data[9] + ' ???????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){				           				
				        				return false;
				        			});	
									return false;
								}
								//*WR29042013								
								var i=arr_data[4];
								var j=arr_data[5];
								var k=arr_data[6];
								var pn=arr_data[7];
								var ps=arr_data[8];
								var max_product_no=$("#csh_max_product_no").val();
								//*WR25052015 for support ?????????????????????????????? OPS ???????????? OPT ??????????????????????????? ?????????????????????????????? OPS ???????????? OPS ???????????????????????????????????????????????????????????????
								if(csh_application_type!='NEW'){								
									//case ???????????????????????????????????????????????????????????????????????????????????????????????? ????????????????????????????????????????????????
									var chk_ops_day_old=$('#info_apply_promo_detail').html();		
									chk_ops_day_old=$.trim(chk_ops_day_old);
									var str_opsday="";	
									switch(chk_ops_day_old){
										case 'TH1':str_opsday="??????????????????????????????????????????????????????1";break;
										case 'TH2':str_opsday="??????????????????????????????????????????????????????2";break;
										case 'TH3':str_opsday="??????????????????????????????????????????????????????3";break;
										case 'TH4':str_opsday="??????????????????????????????????????????????????????4";break;
										case 'TU1':str_opsday="????????????????????????????????????????????????1";break;
										case 'TU2':str_opsday="????????????????????????????????????????????????2";break;
										case 'TU3':str_opsday="????????????????????????????????????????????????3";break;
										case 'TU4':str_opsday="????????????????????????????????????????????????4";break;
										default:chk_ops_day_old='';
									}									
									$('#csh_ops_day').val(chk_ops_day_new); //*WR ???????????????????????????????????????????????????????????? ?????????????????????????????? OPID300
								}//if		
								//*WR25052015
								//----------------- set csh temp ----------------------
								//alert("arr_data[1]=>"+arr_data[1]);//???????????????????????? application_product.product_no ??????????????????????????????????????? appcliation_list.product_no
								if(arr_data[1]!=''){
										var opts={
												type:'post',
												url:'/sales/member/setcshtemp',	
												cache: false,
												data:{
													employee_id:employee_id,
													member_no:member_no,
													application_id:arr_data[0],
													product_id:arr_data[1],
													quantity:arr_data[2],	
													price:arr_data[3],
													status_no:status_no,
													doc_tp:doc_tp,												
													actions:'set_csh_tmp'
												},
												success:function(data){	
													var arr_res=data.split('#');		
													if(arr_res[0]=='2'){
														jAlert('???????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){															
															//TEST ON 13052012
															$('div.tableNavCatList ul>li').navigate('destroy');
															$("#dlgMemberCatalogList").dialog("close");
															if(i<=parseInt(max_product_no)){
																getCatProduct(application_id,product_no,product_seq,product_sub_seq,pn,ps);
															}
															return false;
														});	
													}else if(arr_res[0]=='0'){
														jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
															//????????????????????????????????? ?????? com_product_master
															return false;
														});	
													}else{	
															//alert(i+"<="+parseInt(max_product_no));
															getCshTemp('P');//disable key product field
															if(i<=parseInt(max_product_no)){
																//getCatProduct(application_id,i,j,k,pn,ps);
																//alert(application_id+","+i+","+j+","+k+","+pn+","+ps);
																
																//*WR17122014
																var doc_date_cmp=$('#csh_doc_date').html();
															     doc_date_cmp=$.trim(doc_date_cmp);
																var cmpDate = new Date(doc_date_cmp);
																var cmpDate_Start = new Date('01/01/2015');
																
																//*WR05022014 OPPN300 Revised
																if(application_id=='OPPN300'  && status_wcgif=='Y' && cmpDate<=cmpDate_Start){//&& i==3
																	i++;//*WR17122014 remove for year 2015
																	getCatProduct(application_id,i,j,k,pn,ps);
																}else{
																	//original
																	getCatProduct(application_id,i,j,k,pn,ps);
																	//alert(application_id+","+i+","+j+","+k+","+pn+","+ps);
																}
															}else{
																//alert("csh_gift_set==>"+$("#csh_gift_set").val());
																if($("#csh_gift_set").val()=='N'){
																	$("#dlgMemberCatalogList").dialog("close");
																	$("#csh_accordion").accordion({ active:1});
																	//?????????????????????????????????????????????????????? Gift Set																	
																	if($("#csh_gift_set_amount").val()!='0'){
																		///////////////////// start giftset amount ////////////////		
																		giftsetProduct(application_id,employee_id,member_no,status_no,doc_tp);														
																		////////////////////  end giftset amount ////////////////
																	}else if($("#csh_free_product").val()=='Y'){
																		freeProduct(application_id,employee_id,member_no,status_no,doc_tp);
																	}else if($("#csh_free_product").val()=='O'){
																		//alert("gift set is Y.");
																		//setPdt2Temp(application_id);
																		
																		//*WR24052017
																		if(application_id=='OPPLI300'){
																			freeProduct(application_id,employee_id,member_no,status_no,doc_tp);
																		}else{
																			playMstPromotion(application_id,1);
																		}
																		
																		//playMstPromotion(application_id,1);
																		//freeProduct(application_id,employee_id,member_no,status_no,doc_tp);
																	}else if($("#csh_free_premium").val()=='Y'){
																		freePremium(application_id,employee_id,member_no,status_no,doc_tp);
																	}else{
																		//*WR11062015 OPPF300
																		var chk_member_tu_new=$('#csh_ops_day').val(); 
															           	if(chk_member_tu_new=='TU1' || chk_member_tu_new=='TU2' || chk_member_tu_new=='TU3' || chk_member_tu_new=='TU4'){
															           		selCoPromotion('OPS2OPT','1','1');
															           	}else{
															           		setTimeout('setNewCardMember()',400);//*WR02012013 for promotion 2013
															           	}
																		//setTimeout('setNewCardMember()',400);//*WR02122012 support promotion 2013
																	}
																	return false;
																}else if($("#csh_gift_set").val()=='Y'){
																	//??????????????? ???????????????????????????????????????????????????????????????????????????
																	$("#csh_quantity").disable();
																	$("#csh_product_id").disable();
																	//???????????????????????? flow ???????????? key new member card ????????????????????????????????????????????????
																	$("#dlgMemberCatalogList").dialog("close");
																	//*WR 24062015 for idcard
																	if(application_id=='OPID300' || application_id=='OPPLI300'){
																		paymentBill('01');
																	}else{
																		setTimeout(function(){
																			setNewCardMember();
																		},400);
																	}
//																	if(status_no=='01'){
//																		setTimeout('setNewCardMember()',400);
//																	}
																	$("#csh_accordion").accordion({ active:1});
																	return false;
																}
															}
													}//end else
												}
											};
										
										//*WR 26062013 ???????????????????????????????????????????????? 01 ??????????????????????????????????????? 00
										if(status_no!='01'){
											$("#dlgMemberCatalogList").dialog("close");
											jAlert('????????????????????????????????????????????????????????????????????????????????? \n??????????????????????????????????????????????????? ???????????????????????????????????? 01 ????????????????????????', 'WARNING!',function(){
												initTblTemp();
												initFormCashier();
												getCshTemp('N');
												browsDocStatus();
												return false;
											});	
										}else{
											$.ajax(opts);
										}
								}else{
									//case ??????????????????????????????????????????????????????????????? ???????????? ????????? stock
									//alert(i+"***<="+parseInt(max_product_no));
									if(i<=parseInt(max_product_no)){
										getCatProduct(application_id,i,j,k,pn,ps);
									}else{
										$('div.tableNavCatList ul>li').navigate('destroy');
										$('#dlgMemberCatalogList').dialog('close');
										//???????????????????????? flow ???????????? key new member card ????????????????????????????????????????????????
										if(status_no=='01' && $("#csh_gift_set").val()!='Y'){
											//????????????????????? free product ????????????????????????????????????????????????????????? free premium ???????????????????????????????????????????????????????????????										
											if($("#csh_gift_set_amount").val()!='0'){												
												giftsetProduct(application_id,employee_id,member_no,status_no,doc_tp);		
											}else if($("#csh_free_product").val()=='Y'){
												freeProduct(application_id,employee_id,member_no,status_no,doc_tp);
											}else if($("#csh_free_product").val()=='O'){												
												//*WR07032016  gifset ???????????????
												playMstPromotion(application_id,1);												
											}else if($("#csh_free_premium").val()=='Y'){
												freePremium(application_id,employee_id,member_no,status_no,doc_tp);
											}else{
												setTimeout('setNewCardMember()',400);//*WR02122012 support promotion 2013
											}
										}else{
											//*WR26022014 case ??????????????????????????? 2014
											$.ajax({
												type:'post',
												url:'/sales/member/setnewcardbalance',
												data:{
													promo_code:application_id,
													rnd:Math.random()
												},success:function(){
													getCshTemp('Y');
												}
											});
											//*WR26022014 case ??????????????????????????? 2014
											
											//modify on 04032012
											//??????????????? ???????????????????????????????????????????????????????????????????????????
											$("#csh_quantity").disable();
											$("#csh_product_id").disable();
											//???????????????????????? flow ???????????? key new member card ????????????????????????????????????????????????
											$("#dlgMemberCatalogList").dialog("close");
											
											//*WR01052015 OPIDCARD											
											if(application_id=='OPID300'){
												paymentBill('01');
											}else if(application_id=='OPPLI300'){
												selCoPromotion(application_id,'','');
											}else{
												setTimeout(function(){
													setNewCardMember();
												},400);
											}
											
//											setTimeout(function(){
//												setNewCardMember();
//											},400);
											
											$("#csh_accordion").accordion({ active:1});
											return false;
										}
									
									}
								}
								//------------------end set csh temp-------------------							
							    //---- end --------		
								$('div.tableNavCatList ul>li').navigate('destroy');
								return false;
							});//end click
							///////// end ////////////
						}//end success function
					});//end ajax pos
					
				},
				close:function(evt,ui){
					$('.tableNavCatList ul').navigate('destroy');
					$('#dlgMemberCatalogList').dialog('destroy');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						initTblTemp();
						initFormCashier();
						getCshTemp('N');
						browsDocStatus();
						//setTimeout('selMemberPromotion()',400);					
					}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){	
						initTblTemp();
						initFormCashier();
						getCshTemp('N');
						browsDocStatus();
						//setTimeout('selMemberPromotion()',400);		
					}				
				}
		};
		$('#dlgMemberCatalogList').dialog('destroy');
		$('#dlgMemberCatalogList').dialog(dialogOpts_MemberCatalogList);			
		$('#dlgMemberCatalogList').dialog('open');
	}//func
	
	function chkOpsExist(application_id){
		/**
		 * @desc
		 * @param application_id promo_code
		 * @modify:25042013
		 * @modify 28042015 
		 */
		var NewDialog = $('<div id="dlgIDCardOPS">\
	            <p><input type="text" size="13" id="chk_idcard_exist"><a href="#"  id="btn_idcard" class="btnGrayCleanIco">????????????</a></p>\<p><span id="ops_id_exist"></span></p>\</div>');
	        NewDialog.dialog({
	            modal: true,
	            title: "????????????????????????????????????????????????????????????",
	            //show: 'clip',
	            //hide: 'clip',
	            width:'27%',
	            height:'auto',
	            closeOnEscape:true,	
	            open:function(){
	            	$("#dlgIDCardOPS").parents('.ui-dialog').first().find('.ui-button').first().hide();
	            	$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	            	var ops_clsops={
	            			type:'post',
	            			url:'/sales/member/clsopsday',
	            			data:{
	            				rnd:Math.random()
	            			},success:function(){}
	            	};
	            	$.ajax(ops_clsops);
	            	$('#ops_id_exist').html('');
	            	$('#btn_idcard').live('click',function(evt){
	            		evt.preventDefault();
	            		var b_opsday='1'; 
	            		var chk_idcard_exist=$('#chk_idcard_exist').val();
			            if(chk_idcard_exist.length==0){
			            	return false;
			            }
			            var opts_chkops={
			            		type:'post',
			            		url:'/sales/member/chkopsdaybyidcard',
			            		data:{
			            			idcard:chk_idcard_exist,
			            			rnd:Math.random()
			            		},success:function(data){
			            			b_opsday='0'; 
			            			$('#ops_id_exist').html(data);
			            			$("#dlgIDCardOPS").parents('.ui-dialog').first().find('.ui-button').first().show();
			            		}
			            };
			            if(b_opsday=='1'){
			            	$.ajax(opts_chkops);
			            }
			           return false;	            		 
	            	});
	            	
	            	$('#chk_idcard_exist').live("keypress",function(evt){
	            		var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						if(key == 13) {
								evt.stopImmediatePropagation();
					            evt.preventDefault();
					            var chk_idcard_exist=$('#chk_idcard_exist').val();
					            if(chk_idcard_exist.length==0){
					            	return false;
					            }
					            var opts_chkops={
					            		type:'post',
					            		url:'/sales/member/chkopsdaybyidcard',
					            		data:{
					            			idcard:chk_idcard_exist,
					            			rnd:Math.random()
					            		},success:function(data){
					            			$('#ops_id_exist').html(data);
					            			$("#dlgIDCardOPS").parents('.ui-dialog').first().find('.ui-button').first().show();
					            		}
					            };
					           $.ajax(opts_chkops);
					            return false;
						}
	            	});
	            	
	            },close:function(evt){
	            	$('#dlgIDCardOPS').remove();
	            	NewDialog='';
	            	$("#dlgIDCardOPS").dialog("destroy");
	            	if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
	            		setTimeout(function(){
			            	getCatProduct(application_id,0,0,0,0,0);
			            },400);
					}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){	
						setTimeout(function(){
			            	getCatProduct(application_id,0,0,0,0,0);
			            },400);
						return false;
					}
	            	
	            }	            
	            	,
	            buttons: [
	                {text: "??????????????? >>", click: function(e) {
	                	 e.stopPropagation();
	                	 e.preventDefault();
	                	 var chk_idcard_exist=$('#chk_idcard_exist').val();
			             if(chk_idcard_exist.length==0){
			            	 jAlert('????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
			            		$('#chk_idcard_exist').focus();
								return false;
							});	
			            	return false;
			             }
	                	 $('#dlgIDCardOPS').dialog("close");
				            setTimeout(function(){
				            	getCatProduct(application_id,0,0,0,0,0);
				            },400);
	                }}
	                //,{text: "??????????????????", click: function() {$(this).dialog("close")}}
	            ]
	        });
	        return false;
	}//func
	
	function checkMemberByCompany(promo_code,promo_des){
		/**
		 * WR 10062016
		 */
		
		$("<div id='dlgCheckMemberByCompany'></div>").dialog({
	       	   autoOpen:true,
	  				width:'30%',		
	  				height:'250',	
	  				modal:true,
	  				resizable:false,
	  				//position: { my: "center bottom", at: "center center", of: window },
	  				position:"center",
	  				//showOpt: {direction: 'up'},		
	  				closeOnEscape: false,
	  				title:"<span class='ui-icon ui-icon-person'></span>????????????????????????????????????????????????????????????????????????????????????????????????????????????",
	  				open: function(){ 
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
																										"font-size":"24px","color":"#000000",
																										"padding":"5 0.1em 0 0.1em"});   //ui-corner-all
						$("#dlgCheckMemberByCompany").html('');
		            	$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
		            	$(this).dialog("widget").find(".ui-button-text")
	                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
		            	//create form confirm member_id
     				$.ajax({
     					type:'post',
     					url:'/sales/member/formcheckmemberbycompany',
     					data:{     						
     						rnd:Math.random()
     					},success:function(data){
     						$("#dlgCheckMemberByCompany").html(data);
     						$('#check_member_no_by_comp').val('');
     						$('#check_member_no_by_comp').focus();
     						
     						//*** check lock unlock
     						
 							if($("#csh_lock_status").val()=='Y'){
 								lockManualKey();
 							}else{
 								unlockManualKey();
 							}	
 							
 							//*** check lock unlock	
 							$('#check_member_no_by_comp').live('keypress',function(evt){ 							
 								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
 								if(key == 13) {
 										evt.stopImmediatePropagation();
 							            evt.preventDefault();
 							            $('#btnCheckMemberByComp').trigger('click');
 							            return false;
 								}
 							});
 							
 							
     					}
     				});//end ajax
	  				},
	  				
	  				close:function(evt){
	  					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
	  						initTblTemp();
				    		getCshTemp('Y'); 
		    		        $(this).remove();
						}
	  					$("#dlgCheckMemberByCompany").remove();
		            	$("#dlgCheckMemberByCompany").dialog("destroy");	
		            	
		            	
		            	
		   			},
		   			
		   			
		   			buttons: [ 
			    	            { 
			    	                text: "????????????",
			    	                id:"btnCheckMemberByComp",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	  evt.preventDefault();	  
			    	                	 //---- start ---
			    	                	 var member_no_by_comp=$('#check_member_no_by_comp').val();
			 				             if(member_no_by_comp.length==0){
			 				            	jAlert('?????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
			 				            		$('#check_member_no_by_comp').focus();	
												return false;
											});	
			 				            	return false;
			 				             }
			 					         var opts_chkops={
			 					            		type:'post',
			 					            		url:'/sales/member/chkmemberbycompany',
			 					            		data:{
			 					            			member_no_by_comp:member_no_by_comp,
			 					            			rnd:Math.random()
			 					            		},success:function(data){
			 					            			var objMb=$.parseJSON(data);			 					            			
			 					            			var str_show="";
			 					            			str_show=str_show + " Member ID : " + member_no_by_comp + "\n";
			 					            			str_show=str_show + " Name : " + objMb.name + " ";
		 					            				str_show=str_show + objMb.surname + "\n";
		 					            				str_show=str_show + " ID Card : " + objMb.id_card + "\n";
		 					            				str_show=str_show + " Birthday : " + objMb.birthday + "\n";
		 					            				str_show=str_show + " Apply Date : " + objMb.apply_date + "\n";
		 					            				str_show=str_show + " Expire Date : " + objMb.expire_date + "\n"; 
		 					            				$('#check_member_no_by_comp').val('');
			 					            			if(objMb.status=='Y'){	
			 					            				
			 					            				jConfirm(str_show,'CONFIRM MESSAGE', function(r){
			 											        if(r){
			 											        	fromreadprofile(promo_code,'Y','','','','',promo_des);
			 											        	$("#dlgCheckMemberByCompany").dialog('close');			 												 		
			 														return false;
			 											        }else{
			 											        	$('#check_member_no_by_comp').focus();
			 											        }
			 												});			
			 					            				
			 					            				
			 					            			}else{
			 					            				
			 					            				str_show=str_show + "<hr> ????????????????????????????????????????????????????????????????????????????????? Please check and try again. \n";			 					            				
			 					            				jAlert(str_show, 'WARNING!',function(){
		 									    				$("#dlgCheckMemberByCompany").dialog('close');
		 														return false;
		 													});	
			 					            				
			 					            			}
			 					            			
			 					            			
			 					            		}
			 					            };
			 					           $.ajax(opts_chkops);
			    	                	  //---- stop ----

			    	                } 
			    	            }
			    	        ],	   			
		   			
		   			
		           
		   			
	          });
		
		
	}//func
	
	function getRegMemberPromotion($member_no_ref){
		/**
		*@pram String $member_no_ref : ?????????????????????????????????????????????????????????????????? OPPN300 
		*@return
		*/
		var opts_dlgRegPromo={
				autoOpen:false,
				width:'60%',
				height:350,
				modal:true,
				resizeable:true,
				position:'center',
				showOpt:{direction:'up'},		
				closeOnEscape:true,	
				title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????",
				open:function(){					
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 0.05em 0 0.05em","background-color":"#c5bcdc","font-size":"27px","color":"#000"});
					$("*:focus").blur();
					$("#dlgRegPromo").html("");
					$.ajax({
								type:'post',
								url:'/sales/member/regmemberpromotion',
								data:{
									refer_member_st:$member_no_ref,
									rnd:Math.random()
								},success:function(data){								
									$("#dlgRegPromo").html(data);
									$('div.tableNavMemberPromotion2 ul>li').not('.nokeyboard').navigate({
								        wrap:true
								    }).click(function(evt){	
									    //evt.preventDefault();
								    	evt.stopImmediatePropagation();
									    evt.stopPropagation();
									    if($(this).attr('idpromo')=='none'){
										    //??????????????????????????????????????????
									    	$('div.tableNavMemberPromotion2 ul>li').navigate('destroy');
									    	initMemberCatalog();//*WR25092012
									    	$("#dlgRegPromo").dialog("close");
									    	setTimeout('selMemberPromotion()',400);									    	
										    return false;
										}
									    var selpromo=$.parseJSON($(this).attr('idpromo'));
									  //*WR08012016 for support bill 01
									    $('#csh_application_type').val(selpromo.application_type);
									    $('#csh_card_type').val(selpromo.card_type);
									    
									  //*WR06122013 New OPPC300 2014
										var mp_point_sum=$('#csh_point_total').html();
										mp_point_sum=parseInt(mp_point_sum,10);
									    if(selpromo.application_id=='OPPC300' && mp_point_sum < 30){
									    	 jAlert('?????????????????????????????? Please check and try again.', 'WARNING!',function(){
									    		 $("#dlgRegPromo").dialog("close");
									    		 initTblTemp();
											     initFormCashier();
											     initFieldOpenBill();
									    		 return false;
							        		});	
									    	return false;
									    }else if(selpromo.application_id=='OPPC300'){
									    	$("#csh_point_receive").val(0);   //??????????????????????????????????????????
											$("#csh_point_used").val('30');	     //????????????????????????????????? 
											$("#csh_point_net").val('-30');      //??????????????????????????????
											$("#csh_xpoint").val('1');
									    }
									    
										//---------------------??????????????????????????????????????????----------------
									    $('#csh_application_id').val(selpromo.application_id);
									    $("#csh_max_product_no").val(selpromo.maxno);
										$("#csh_gift_set").val(selpromo.gift_set);//????????????????????????????????????????????????????????? Gift Set ?????????????????????		
										$("#csh_gift_set_amount").val(selpromo.amount);//?????????????????????????????????????????????????????? Gift Set	
										$("#csh_register_free").val(selpromo.register_free);//check for free is 'Y' or not free is 'N' effect for bill DN or SL
										$("#csh_free_product").val(selpromo.free_product);//?????????????????????????????????????????????????????????
										$("#csh_free_product_amount").val(selpromo.free_product_amount);
										$("#csh_product_amount_type").val(selpromo.product_amount_type);
										$('#csh_free_premium').val(selpromo.free_premium);//???????????????????????????????????????????????????????????????		
										$("#csh_free_premium_amount").val(selpromo.free_premium_amount);
										$("#csh_premium_amount_type").val(selpromo.premium_amount_type);
										$("#csh_xpoint").val(selpromo.xpoint);
										$("#csh_get_point").val(selpromo.get_point);
										$("#other_promotion_title").html(selpromo.application_id+" "+selpromo.description);	
										
										//*WR24042014										
										if (selpromo.oppn_opps_range=='Y'){
											if(selpromo.application_id=='OPPN300' || selpromo.application_id=='OPPS300'){
											    	$("#dlgRegPromo").dialog("close");
											    	//chkOpsExist(selpromo.application_id);				
											    	var ops_clsops={
									            			type:'post',
									            			url:'/sales/member/clsopsday',
									            			data:{
									            				rnd:Math.random()
									            			},success:function(){}
									            	};
									            	$.ajax(ops_clsops);
											    	frominputidcard(selpromo.application_id); 											    	
											    	return false;
											 }
										}
										if(selpromo.redeem_point!=0){
							           		//redeem_point <> 0 ???????????????????????????????????????
											opts_aa={
							           			type:'post',
							           			url:'/sales/member/currpoint',
							           			cache: false,
							           			data:{
						           					refer_member_st:refer_member_st,
						           					rnd:Math.random()
						           				},success:function(data){
							           				$("#csh_point_total").html(data);//???????????????????????????
							           				var point_receive=$("#csh_point_receive").val();
							           				var point_used=selpromo.redeem_point;//?????????????????????????????????
							           				var point_net=parseInt(point_receive)-parseInt(point_used);
							           				$("#csh_point_used").val(point_used);
							           				$("#csh_point_net").val(point_net);//??????????????????????????????									           		
							           			}
							           		};
							           		$.ajax(opts_aa);
							           	}//if	
										$('div.tableNavMemberPromotion2 ul>li').navigate('destroy');
									    $("#dlgRegPromo").dialog("close");
									    
									    //*WR20012015
									   
									    if(selpromo.application_id=='OPPL300' || selpromo.application_id=='OPPLI300'  || selpromo.application_id=='OPPLC300'){
									    	//getCatProduct(selpromo.application_id,0,0,0,0,0);
									    	//fromreadprofile(selpromo.application_id,'','','','idcard',selpromo.promo_des);
									    	fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   return false;
										}else if(selpromo.application_id=='OPPF300' || selpromo.application_id=='OPPD300' || 
												selpromo.application_id=='OPPA300' || selpromo.application_id=='OPPB300' || 
												selpromo.application_id=='OPPC300'){
											var ref_old_spday=$('#info_apply_promo_detail').html();
											$('#csh_ops_day').val(ref_old_spday); 
											var chk_special_day=$.trim(ref_old_spday);
											var idcard=$('#csh_id_card').val();
											fromreadprofile_verify(selpromo.application_id,'Y',$member_no_ref,$('#other_promotion_title').html(),idcard,selpromo.application_id);
											return false;
										}else if(selpromo.application_id=='OPLID300'){
											   //*WR 07052015				
											   //fromreadprofile(selpromo.application_id,'','','','idcard',selpromo.promo_des);
											   fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   //alert("call check idcard for line promotion.");
											   //getCatProduct(selpromo.application_id,0,0,0,0,0);
											   return false;
										}else if(selpromo.application_id=='OPMGMC300'){
											   //*WR 07052015
											   //getCatProduct(selpromo.application_id,0,0,0,0,0);
											   //m2mfromnew(selpromo.application_id);
											   m2mfromcheck(selpromo.application_id);
											   return false;
										}else if(selpromo.application_id=='OPMGMI300'){
											   //*WR 07052015
											   //getCatProduct(selpromo.application_id,0,0,0,0,0);											   
											   m2mfromcheck(selpromo.application_id);
											   return false;
										}else if(selpromo.application_id=='OPID300'){
											   //*WR 07052015											   
											   fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   return false;
										}else if(selpromo.application_id=='OPTRUE300'){
											   //*WR 07052015			
											   getCatProduct(selpromo.application_id,0,0,0,0,0);
											   //fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   return false;
										}else if(selpromo.application_id=='OPKTC300'){
											   //*WR 22/02/2017											   
											   fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   return false;
										}else if(selpromo.application_id=='OPGNC300'){
											   //*WR 07052015	
												checkMemberByCompany(selpromo.application_id,selpromo.promo_des);
											   //fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   return false;
										}else if(selpromo.application_id=='OPPGI300'){
											   //*WR 07052015
											   //getCatProduct(selpromo.application_id,0,0,0,0,0);
											   fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
											   return false;
										}else if(selpromo.application_id=='OPPHI300'){
											   //*WR 07052015
											   ccsregisterfrom(selpromo.application_id); 
											   return false;
										}else if(selpromo.application_id=='OPDTAC300'){
											   //*WR 30082016
												fromreadprofile(selpromo.application_id,'Y','','','','',selpromo.promo_des);
												return false;
										}else{
											   setTimeout(function(){
											    	getCatProduct(selpromo.application_id,0,0,0,0,0);
											    },800);
										}
									    return false;//WR28012013
									});//end click
								}
							});
				},
				close:function(evt,ui){
					evt.stopPropagation();
					evt.preventDefault();
					$('.tableNavMemberPromotion2 ul>li').navigate('destroy');	
					$("#dlgRegPromo").dialog("destroy");//*WR25042013
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//selMemberPromotion();		
						browsDocStatus();
					}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){	
						//selMemberPromotion();
						browsDocStatus();
						return false;
					}					
				}
		};
		$("#dlgRegPromo").dialog("destroy");
		$("#dlgRegPromo").dialog(opts_dlgRegPromo);			
		$("#dlgRegPromo").dialog("open");		
	}//func
	
	var timer2 = undefined; // must be global referring to keyup_handler conte
	var timer3 =undefined;
	function keyup_handler(event){
		   if (timer2 != undefined)
		        window.clearTimeout(timer2);
		    timer2=window.setTimeout(function(){
		    	getRegMemberPromotion('');
		    },400);
	}//func
	
	function keyup_handler2(refer_member_st){
		   if (timer3 != undefined)
		        window.clearTimeout(timer3);
		    timer3=window.setTimeout(function(){
		    	getRegMemberPromotion(refer_member_st);
		    },400);
	}//func
	
	function selMemberPromotion(){
		/**
		 * @desc
		 * @modidy:24092012
		 * @return null
		 */
		var dialogOpts_MemberRef={
		        autoOpen:false,
		        resizable:true,
		        modal: true,
		        closeOnEscape:true,
		        title:"<span class='ui-icon ui-icon-person'></span>??????????????????????????????????????????????????????",
		        width:'auto',
		        height:'auto',
		        overlay:{ backgroundColor:"#999",opacity:0.5},
		        open:function(event,ui){
		        	//*** check lock unlock
					if($("#csh_lock_status").val()=='Y'){
						lockManualKey();
					}else{
						unlockManualKey();
					}
					//*** check lock unlock
					
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					 $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0",
	    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
	    			$(this).dialog("widget").find(".ui-dialog-buttonpane")
                    .css({"padding":"0","margin":"0 0 0 0","background-color":"#c8c7dc"});
	    			
	    			$(this).dialog("widget").find(".ui-button-text")
                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
	    			
		        	 $(".ui-widget-overlay").live('click', function(){
					    $("#refer_member_st").focus();
					 });
		        	 
		        	$('#csh_status_no').val('01');//WR 29052013 make sure this is bill 01 
		        	 
		        	 $("#refer_member_st").val('').focus();
		        	 $("#refer_member_st").keypress(function(evt){		        		
		        		var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						if(key == 13) {
							//evt.stopPropagation();
							evt.stopImmediatePropagation();
						    evt.preventDefault();		
						    $('.ui-dialog-buttonpane button:first').click();
						    return false;
						}
		        	 });		        	 
			        },
			   buttons:{
				    	'????????????':function(evt){
				    		evt.preventDefault();				    		
				    		///////// start //////////
				    		$('#csh_status_no').val('01');//WR 19042013 make sure this is bill 01 
				    		var $csh_member_no=$("#csh_member_no");			
				        	var $refer_member_st=$("#refer_member_st");	
				    		var $refer_member_st_val=$.trim($refer_member_st.val());
				            if($refer_member_st_val.length==0){
				            	$("#dlgMemberRef").dialog('close');
				            	//?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? OPPN300
				            	getRegMemberPromotion('');
					            return false;
					        }else{
					            $("#csh_refer_member_id").val($refer_member_st_val);
					            $("#info_refer_member_id").html($refer_member_st_val);
					            var actions='OFFLINE';
					            if(getOnlineStatus()===1){
					            	actions='ONLINE';
					            }
					           	var opts_memexpire={
							           	type:'post',
							           	url:'/sales/member/commemberexpire',
							           	cache: false,
							           	data:{
					           				refer_member_st:$.trim($refer_member_st_val),
					           				actions:actions,
					           				rnd:Math.random()
						           		},
						           		success:function(data){						           			
							           		if(data==''){
							           			jAlert('????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
							           				initMemberCatalog();//*WR25092012
							           				$refer_member_st.val('').focus();
							        				return false;
							        			});	
								           	}else{										          
									           	/*######################################*/
								           			var m=$.parseJSON(data);
								           			
								           			///////////////// check member 10092013 //////////////////////	
													
													if(m.status=='1' && m.mem_status!=''){
														if(m.mem_status=='N'){
															jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active ?????????????????????????????????????????????????????? \n Please check and try again.', 'WARNING!',function(){
																initFormCashier();
																initFieldOpenBill();
																return false;
															});																
														}else if(m.mem_status=='F'){
															jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ???????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
																initFormCashier();
																initFieldOpenBill();
																return false;
															});	
														}else if(m.mem_status=='T'){
															jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
																initFormCashier();
																initFieldOpenBill();
																return false;
															});	
														}
														$("#dlgMemberRef").dialog('close');
														$("#dlgMemberRef").dialog('destroy');
														return false;
													}
													///////////////// check member 10092013 //////////////////////
													if(m.surname!=undefined){
														var member_fullname=m.name+" "+m.surname;
													}else{
														var member_fullname=m.name;
													}
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

													if(m.link_status=='ONLINE'){
														
														//*WR22032016														
														$('#csh_id_card').val(m.id_card);
														$("#csh_ops_day").val(m.cust_day);
														$("#csh_mobile_no").val(m.mobile_no);
														
														var refer_member_id='';
														var remark=m.remark;
														var percent_discount=parseInt(m.percent_discount);
														var mp_point_sum=m.point;
														
														//var buy_net=m.net;				
														var buy_net=m.buy_net;
														var address=$.trim(m.address)+" "+
														$.trim(m.sub_district)+" "+
														$.trim(m.district)+" "+
														$.trim(m.province_name)+" "+
														$.trim(m.zip)+" <br>"+
														$.trim(m.mobile_no);														
														switch(m.cust_day){
															case "TH0":cust_day="";break;
															case "TH1":cust_day="?????????????????????????????????1";break;
															case "TH2":cust_day="?????????????????????????????????2";break;
															case "TH3":cust_day="?????????????????????????????????3";break;
															case "TH4":cust_day="?????????????????????????????????4";break;
															case "TU1":cust_day="???????????????????????????1";break;
															case "TU2":cust_day="???????????????????????????2";break;
															case "TU3":cust_day="???????????????????????????3";break;
															case "TU4":cust_day="???????????????????????????4";break;
															default:cust_day="";
														}											
														$('#csh_member_fullname').html(member_fullname);
														$('#csh_birthday').html(birthday);
														$('#csh_apply_date').html(apply_date);
														$('#csh_expire_date').html(expire_date);
														$('#csh_member_type').html(remark);
														$('#csh_address').html(address);													
														
														//WR24122013 POINT EXPIRE
														$("#csh_transfer_point").val(m.transfer_point);
														$("#csh_expire_transfer_point").val(m.expire_transfer_point);
														$("#csh_curr_point").val(m.curr_point);
														$("#csh_balance_point").val(m.balance_point);	
														//*WR06122013
														$('#csh_point_total').html(m.mp_point_sum);
														
														//------------zone member info -----									
														$("#info_refer_member_id").html(m.member_no);
														$("#info_member_fullname").html(member_fullname);
														$("#info_member_applydate").html(apply_date);
														$("#info_member_expiredate").html(expire_date);
														$("#info_member_birthday").html(birthday);
														$("#info_member_opsday").html(cust_day);
														$("#info_apply_promo").html(m.apply_promo);
														//$("#info_apply_promo_detail").html(m.apply_promo_detail);
														$("#info_apply_promo_detail").html(m.cust_day);
														$("#info_member_address").html(address);
														//------------zone member info -----	
													}else if(m.link_status=='OFFLINE'){
														//*WR21032013
														if(m.exist_status=='NO'){
															jAlert('??????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
																initFormCashier();
																initFieldOpenBill();
																return false;
															});	
															return false;
														}
														var refer_member_id='';
														var remark=m.remark;
														var percent_discount='0';
														if(m.percent_discount!=undefined){
															percent_discount=parseInt(m.percent_discount);
														}
														var mp_point_sum=m.mp_point_sum;
														var buy_net=m.buy_net;
														var address=$.trim(m.h_address)+" "+
																		$.trim(m.h_village_id)+" "+
																		$.trim(m.h_village)+" "+
																		$.trim(m.h_soi)+" "+
																		$.trim(m.h_road)+" "+
																		$.trim(m.h_district)+" "+
																		$.trim(m.h_amphur)+" "+
																		$.trim(m.h_province)+" "+
																		$.trim(m.h_postcode);
														$('#csh_member_fullname').html(member_fullname);
														$('#csh_birthday').html(birthday);
														$('#csh_apply_date').html(apply_date);
														$('#csh_expire_date').html(expire_date);
														$('#csh_member_type').html(remark);
														$('#csh_percent_discount').html(percent_discount);
														$('#csh_point_total').html(mp_point_sum);
														$('#csh_buy_net').html(buy_net);
														$('#csh_address').html(address);
														//------------zone member info -----									
														$("#info_refer_member_id").html(m.member_no);
														$("#info_member_fullname").html(member_fullname);
														$("#info_member_applydate").html(apply_date);
														$("#info_member_expiredate").html(expire_date);
														$("#info_member_birthday").html(birthday);
														$("#info_member_opsday").html(m.special_day);
														$("#info_apply_promo").html(m.apply_promo);
														$("#info_apply_promo_detail").html(m.apply_promo_detail);
														$("#info_member_address").html(address);
														//------------zone member info -----			
													}
												$("#csh_accordion").accordion({ active:0});		
												/*######################################*/
												
								           		$("#dlgMemberRef").dialog('close');
								           		$("#dlgMemberRef").dialog('destroy');
									           	getRegMemberPromotion($refer_member_st_val);	
									        }
							           		return false;
							           	}
							        };
					           
					           	$.ajax(opts_memexpire);
					        }
				    		///////// stop //////////
				    	}
				 },
		        close: function(evt, ui){
			        	if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
			        		  	evt.stopPropagation();
						        evt.preventDefault();
						        initTblTemp();
						        initFormCashier();
								initFieldOpenBill();
						}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){	
							  	evt.stopPropagation();
						        evt.preventDefault();
						        initTblTemp();
						        initFormCashier();
								initFieldOpenBill();
						} 	
			        	$('#dlgMemberRef').dialog('destroy');
				    }
			};		
			$('#dlgMemberRef').dialog('destroy');
			$('#dlgMemberRef').dialog(dialogOpts_MemberRef);			
			$('#dlgMemberRef').dialog('open');
	}//func
	
	function selMemberPromotion2(){
		/**
		*@desc new process
		*@return 
		*/		
		var dialogOpts_MemberRef={
	        autoOpen:false,
	        resizable:false,
	        modal: true,
	        closeOnEscape:true,
	        title:"<span class='ui-icon ui-icon-person'></span>??????????????????????????????????????????????????????",
	        width:250,
	        height:'auto',
	        overlay:{ backgroundColor:"#999",opacity:0.5},
	        open:function(event,ui){
	        	//*** check lock unlock
				if($("#csh_lock_status").val()=='Y'){
					lockManualKey();
				}else{
					unlockManualKey();
				}
				//*** check lock unlock
				
				$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
				$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc","color":"#000"});
	        	//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();			
	        	 $(".ui-widget-overlay").live('click', function(){
				    $("#refer_member_st").focus();
				 });
	        	 var $csh_member_no=$("#csh_member_no");			
	        	 var $refer_member_st=$("#refer_member_st");	
	        	 $refer_member_st.val('').focus();
	        	 var c=0;
	        	 $refer_member_st.keypress(function(evt){   
					var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					if(key == 13) {
								evt.stopPropagation();
					            evt.preventDefault();	
					            var $refer_member_st_val=$.trim($refer_member_st.val());
					            if($refer_member_st_val.length==0){
					            	$("#dlgMemberRef").dialog('close');
					            	//?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? OPPN300
					            	//setTimeout(function(){getRegMemberPromotion('');},1000);
					            	$("#refer_member_st").keyup(keyup_handler());
						            return false;
						        }else{
						            $("#csh_refer_member_id").val($refer_member_st_val);
						            $("#info_refer_member_id").html($refer_member_st_val);
						           	var opts={
								           	type:'post',
								           	url:'/sales/member/commemberexpire',
								           	async:false,
								           	cache: false,
								           	data:{
						           				refer_member_st:$.trim($refer_member_st_val),
						           				rnd:Math.random()
							           		},
							           		success:function(data){
								           		if(data==''){
								           			jAlert('????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								           				$refer_member_st.focus();
								        				return false;
								        			});	
									           	}else{										          
										           	/*######################################*/
									           			var m=$.parseJSON(data);
														if(m.surname!=undefined){
															var member_fullname=m.name+" "+m.surname;
														}else{
															var member_fullname=m.name;
														}
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

														if(m.link_status=='ONLINE'){
															var refer_member_id='';
															var remark=m.remark;
															var percent_discount=parseInt(m.percent_discount);
															var mp_point_sum=m.point;
															var buy_net=m.net;													
															var address=$.trim(m.address)+" "+
															$.trim(m.sub_district)+" "+
															$.trim(m.district)+" "+
															$.trim(m.province_name)+" "+
															$.trim(m.zip)+" <br>"+
															$.trim(m.mobile_no);
															switch(m.cust_day){
																case "TH0":cust_day="";break;
																case "TH1":cust_day="?????????????????????????????????1";break;
																case "TH2":cust_day="?????????????????????????????????2";break;
																case "TH3":cust_day="?????????????????????????????????3";break;
																case "TH4":cust_day="?????????????????????????????????4";break;
															}													
															$('#csh_member_fullname').html(member_fullname);
															$('#csh_birthday').html(birthday);
															$('#csh_apply_date').html(apply_date);
															$('#csh_expire_date').html(expire_date);
															$('#csh_member_type').html(remark);
															$('#csh_address').html(address);
															//------------zone member info -----									
															$("#info_refer_member_id").html(m.member_no);
															$("#info_member_fullname").html(member_fullname);
															$("#info_member_applydate").html(apply_date);
															$("#info_member_expiredate").html(expire_date);
															$("#info_member_birthday").html(birthday);
															$("#info_member_opsday").html(cust_day);
															$("#info_apply_promo").html(m.apply_promo);
															$("#info_apply_promo_detail").html(m.apply_promo_detail);
															$("#info_member_address").html(address);
															//------------zone member info -----	
														}else if(m.link_status=='OFFLINE'){
															var refer_member_id='';
															var remark=m.remark;
															var percent_discount='0';
															if(m.percent_discount!=undefined){
																percent_discount=parseInt(m.percent_discount);
															}
															var mp_point_sum=m.mp_point_sum;
															var buy_net=m.buy_net;
															var address=$.trim(m.h_address)+" "+
																			$.trim(m.h_village_id)+" "+
																			$.trim(m.h_village)+" "+
																			$.trim(m.h_soi)+" "+
																			$.trim(m.h_road)+" "+
																			$.trim(m.h_district)+" "+
																			$.trim(m.h_amphur)+" "+
																			$.trim(m.h_province)+" "+
																			$.trim(m.h_postcode);
															
															$('#csh_member_fullname').html(member_fullname);
															$('#csh_birthday').html(birthday);
															$('#csh_apply_date').html(apply_date);
															$('#csh_expire_date').html(expire_date);
															$('#csh_member_type').html(remark);
															$('#csh_percent_discount').html(percent_discount);
															$('#csh_point_total').html(mp_point_sum);
															$('#csh_buy_net').html(buy_net);
															$('#csh_address').html(address);
															//------------zone member info -----									
															$("#info_refer_member_id").html(m.member_no);
															$("#info_member_fullname").html(member_fullname);
															$("#info_member_applydate").html(apply_date);
															$("#info_member_expiredate").html(expire_date);
															$("#info_member_birthday").html(birthday);
															$("#info_member_opsday").html(m.special_day);
															$("#info_apply_promo").html(m.apply_promo);
															$("#info_apply_promo_detail").html(m.apply_promo_detail);
															$("#info_member_address").html(address);
															//------------zone member info -----			
														}
													$("#csh_accordion").accordion({ active:0});		
													/*######################################*/
									           		$("#dlgMemberRef").dialog('close');
									           		$("#dlgMemberRef").dialog('destroy');
										           	//getRegMemberPromotion($refer_member_st_val);			
									           		$("#refer_member_st").keyup(keyup_handler2($refer_member_st_val));
										        }
										        c=1;
								           		return false;
								           	}
								        };
							        if(c==0){
						        		$.ajax(opts);
							        }
						        }
						        return false;
					        }
					});
					
		        },
	        close: function(evt, ui){
		        	if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
		        		  	evt.stopPropagation();
					        evt.preventDefault();
					        initTblTemp();
					        initFormCashier();
							initFieldOpenBill();
					}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){	
						  	evt.stopPropagation();
					        evt.preventDefault();
					        initTblTemp();
					        initFormCashier();
							initFieldOpenBill();
					} 	
			    }
		};		
		$('#dlgMemberRef').dialog('destroy');
		$('#dlgMemberRef').dialog(dialogOpts_MemberRef);			
		$('#dlgMemberRef').dialog('open');		
	}//func
	
	function getMemberCatalog(){
		/**
		*@desc old process last update 02022012 ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? ???????????? flow ?????????????????????
		*@return
		*/
		var dialogOpts_MemberCatalog = {
				autoOpen: false,
				width:'90%',		
				height:'auto',	
				modal:true,
				resizable:true,
				position:"top",
				showOpt: {direction: 'up'},		
				closeOnEscape:true,	
				title:"<span class='ui-icon ui-icon-person'></span>???????????????????????????????????????",
				open: function(){    
						//init value 			
						$("#csh_application_id").val('');
						$("#csh_refer_member_id").val('');
						$("#csh_max_product_no").val('');
						$("#info_apply_promo").html('');
						$("#info_apply_promo_detail").html('');
						/*$('.ui-dialog').css({'font-size':'11px'});*/
						$("#dlgMemberCatalog").html("");
						$("#dlgMemberCatalog").load("/sales/member/catalog?now="+Math.random(),
						function(){	

							$('.tableNavCat ul li').not('.nokeyboard').navigate({
						        wrap:true
						    }).click(function(e){
							    e.preventDefault();
							    e.stopPropagation();
							    //---- start ------
							    //---- init member catalog
							    initMemberCatalog();
							    var arr_cat=$(this).attr("idcat").split("^");
								//---------------------??????????????????????????????????????????----------------
								$("#csh_application_id").val(arr_cat[0]);
								$("#csh_max_product_no").val(arr_cat[1]);
								$("#csh_gift_set").val(arr_cat[5]);//????????????????????????????????????????????????????????? Gift Set ?????????????????????		
								$("#csh_gift_set_amount").val(arr_cat[6]);//?????????????????????????????????????????????????????? Gift Set	
								$("#csh_register_free").val(arr_cat[7]);//check for free is 'Y' or not free is 'N' effect for bill DN or SL
								$('#dlgMemberCatalog').dialog("close");
								//---------------------??????????????????????????????????????????----------------							
								$("#info_apply_promo").html(arr_cat[0]);
								$("#info_apply_promo_detail").html(arr_cat[4]);
								
								if(arr_cat[2]=='Y'){
									//refer_member_st='Y' ???????????????????????????????????????????????????????????????????????????
									$("#dlgMemberRef").dialog({
								        autoOpen:true,
								        resizable:false,
								        modal: true,
								        closeOnEscape: true,
								        title:"<span class='ui-icon ui-icon-person'></span>??????????????????????????????????????????????????????",
								        width:300,
								        overlay:{ backgroundColor:"#999",opacity:0.5},
								        open:function(event,ui){
								        		$(this).parent().children().children('.ui-dialog-titlebar-close').hide();								        		
								        		$("#refer_member_st").val('');
								        		$("#refer_member_st").val('').focus();
								        		$("#refer_member_st").keypress(function(evt){
													var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
											        if(key == 13) {
											            evt.preventDefault();			
											            evt.stopImmediatePropagation();
											            var application_id=$("#csh_application_id").val();
											            var refer_member_st=jQuery.trim($("#refer_member_st").val());
											            if(refer_member_st=='') return false;
											            $("#csh_refer_member_id").val(refer_member_st);
											            $("#info_refer_member_id").html(refer_member_st);
											           	var opts={
													           	type:'post',
													           	url:'/sales/member/commemberexpire',
													           	async:true,
													           	cache: false,
													           	data:{
												           			application_id:application_id,
											           				refer_member_st:refer_member_st,
											           				rnd:Math.random()
												           		},
												           		success:function(data){												           			
													           		var arr_app=data.split('#');
													           		if(arr_cat[8]!=0){
														           		//redeem_point <> 0 ???????????????????????????????????????
														           		$.ajax({
															           			type:'post',
															           			url:'/sales/member/currpoint',
															           			cache: false,
															           			data:{
														           					refer_member_st:refer_member_st,
														           					rnd:Math.random()
														           				},success:function(data){
															           				//----------------------point-------------------------------
															           				$("#csh_point_total").html(data);//???????????????????????????
															           				var point_receive=$("#csh_point_receive").val();
															           				var point_used=arr_cat[8];//?????????????????????????????????
															           				var point_net=parseInt(point_receive)-parseInt(point_used);
															           				$("#csh_point_used").val(point_used);
															           				$("#csh_point_net").val(point_net);//??????????????????????????????
																           			//----------------------point-------------------------------
															           			}
															           		});
														           	}//if
													           		
													           		if(arr_app[0]=='2'){														           			
													           			jAlert('????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													           				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='3'){
													           			jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													        				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='4'){
													           			jAlert('????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													        				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='5'){
													           			jAlert('????????????????????????????????????????????? '+addCommas(arr_app[2])+' ????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													        				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='6'){														           			
													           			jAlert('????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													           				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='7'){														           			
													           			jAlert('??????????????????????????????????????????????????? '+arr_cat[8]+' Please check and try again.', 'WARNING!',function(){
													           				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='8'){														           			
													           			jAlert('AFTER CASE COMMING SOON.', 'WARNING!',function(){
													           				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='9'){
													           			jAlert('??????????????????????????????????????????????????????????????????????????? '+addCommas(arr_app[2])+' ????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													        				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='10'){
													           			jAlert('??????????????????????????????????????????????????????????????????????????? 1 ?????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
													        				getMemberCatalog();
													        				return false;
													        			});	
													           			return false;
														           	}else if(arr_app[0]=='1'){
														           		var arr_expire_date=arr_app[1].split('-');
																		var expire_date=arr_expire_date[2]+"/"
																			+arr_expire_date[1]+"/"
																			+(parseInt(arr_expire_date[0])+543);
															           	$("#info_member_expiredate").html(expire_date);
													           			$("#csh_product_id").disable();
													           			$("#csh_quantity").disable();
													           			$("#csh_member_no").focus();
													           			return false;
														           	}
													           	}
													        };
												        $.ajax(opts);
												        $("#dlgMemberRef").dialog('close');
												        return false;
											        }
												});
												
									        },
								        close: function(evt, ui){
										        if(evt.which==27){
											        evt.stopPropagation();
										        	getMemberCatalog();
											    }else{
										        	$("#dlgMemberRef").dialog('destroy');
											    }
										    }
								    });
																		
								}else{
									//?????????????????????????????????????????????????????????????????????????????????????????????????????? OPPN300,OPPG300
									initFieldOpenBill();
									//$("#csh_member_no").focus();
								}//end if
																
							    //---- end --------								
								$('.tableNavCat ul').navigate('destroy');
								return false;
							});//end click
						});//end func open
				},				
				close: function(){
					$('.tableNavCat ul').navigate('destroy');
					if(jQuery.trim($("#csh_application_id").val())==''){
						initFormCashier();
						initFieldOpenBill();
					}
					$('#btnSelMemberCatalog').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
				 }
			};	
			$('#dlgMemberCatalog').dialog('destroy');
			$('#dlgMemberCatalog').dialog(dialogOpts_MemberCatalog);			
			$('#dlgMemberCatalog').dialog('open');
		return false;
	}//func

	function checkMemberExist(){
		/**
		*@desc ??????????????????????????????????????????????????????????????????????????? key ?????????????????????
		*@return
		*/
		var $member_no=$("#csh_member_no");
		var $application_id=$("#csh_application_id");
		var $member_no_val=$.trim($member_no.val());
		if($member_no_val.length==0){
			jAlert('?????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
		}else{
			var opts_member={
					type:'post',
					url:'/sales/member/checkmember',
					data:{
						member_no:$member_no_val,
						rndno:Math.random()
					},
					success:function(data){
						if(data=='1'){
							jAlert('???????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='2'){
							jAlert('???????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else{
							$("#csh_accordion").accordion({ active:0});
							var arr_expire_date=data.split('-');
							var expire_date=arr_expire_date[2]+"/"
								+arr_expire_date[1]+"/"
								+(parseInt(arr_expire_date[0])+543);
							$("#csh_expire_date").val(data);
							$("#info_member_expiredate").html(expire_date);
							//????????????????????????????????? show promotion ?????????????????????????????????????????????????????????????????????????????????????????????????????????
							//---------------------- start ---------------------------------------------
							$("#dlgRegPromo").dialog({
										autoOpen:true,
										width:'60%',
										height:'350',
										modal:true,
										resizeable:true,
										position:'top',
										showOpt: {direction:'up'},		
										closeOnEscape:true,	
										title:"<span class='ui-icon ui-icon-cart'></span>??????????????????????????????????????????",
										open:function(){					
											$("*:focus").blur();
											$("#dlgRegPromo").html("");
											$.ajax({
														type:'post',
														url:'/sales/member/newmemberpromotion',
														data:{
															application_id:$('#csh_application_id').val(),
															rnd:Math.random()
														},success:function(data){
															$("#dlgRegPromo").html(data);
															$('.tableNavMemberPromotion ul li').not('.nokeyboard').navigate({
														        wrap:true
														    }).click(function(evt){	
															    evt.preventDefault();
															    evt.stopPropagation();
															    var selpromo=$.parseJSON($(this).attr('idpromo'));
																  //---------------------??????????????????????????????????????????----------------
															    $('#csh_application_id').val(selpromo.application_id);
															    $("#csh_max_product_no").val(selpromo.maxno);
																$("#csh_gift_set").val(selpromo.gift_set);//????????????????????????????????????????????????????????? Gift Set ?????????????????????		
																$("#csh_gift_set_amount").val(selpromo.amount);//?????????????????????????????????????????????????????? Gift Set	
																$("#csh_register_free").val(selpromo.register_free);//check for free is 'Y' or not free is 'N' effect for bill DN or SL
																$("#info_apply_promo").html(selpromo.application_id);
																$("#info_apply_promo_detail").html(selpromo.description);	
															    $("#dlgRegPromo").dialog("close");
															    //getCatProduct(selpromo.application_id,selpromo.maxno,selpromo.maxnoseq,0,0,0);		
															    getCatProduct(selpromo.application_id,0,0,0,0,0);								   
															});
														}
													});
										},
										close:function(){
											$('.tableNavMemberPromotion ul').navigate('destroy');
											$("#dlgRegPromo").dialog("destroy");
										}
								});
							//---------------------- end ----------------------------------------------					
							
						}
					}
				};
			$.ajax(opts_member);
		}
		return false;
	}//func
	
	function firstBuy(){
		/**
		*@desc run on offline mode only
		*@return
		*/
		var member_id=jQuery.trim($("#csh_member_no").val());
		if(member_id.length==0){
			jAlert('Please specify member id.','WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
		}else{
			//callMemberInfo('N');
			$.getJSON(
					"/sales/member/firstbuy",
					{
						member_id:member_id,
						rnd:Math.random(),
						actions:'firstbuy'
					},
					function(data){
						if(data==1){
							jAlert('???????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFormCashier();
								initTblTemp();
								getCshTemp('Y');
								initFieldOpenBill();
								return false;
							});	
							return false;
						}else if(data==2){
							jAlert('??????????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFormCashier();
								initTblTemp();
								getCshTemp('Y');
								initFieldOpenBill();
								return false;
							});	
						}else if(data==3){
							jAlert('????????????????????????????????? ?????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFormCashier();
								initTblTemp();
								getCshTemp('Y');
								initFieldOpenBill();
								return false;
							});	
						}else{								
							$.each(data.firstbuy, function(i,m){							
								if(m.exist=='yes'){
									var str_percent_discount=m.first_percent+'+'+m.add_first_percent;
									$("#csh_first_sale").val(m.first_sale);
									$("#csh_first_percent").val(m.first_percent);	
									//assume is one record only
									$("#csh_xpoint").val(m.xpoint);
									$("#csh_play_main_promotion").val(m.play_main_promotion);
									$("#csh_play_last_promotion").val(m.play_last_promotion);
									$("#csh_first_limited").val(m.first_limited);				
									//discount
									$("#csh_add_first_percent").val(m.add_first_percent);
									$("#csh_start_date1").val(m.start_date1);
									$("#csh_end_date1").val(m.end_date1);
									//$("#csh_percent_discount").html(m.percent_discount);			
									$("#csh_percent_discount").html(str_percent_discount);
									//25022016
									$('#csh_application_type').val(m.application_type);
									$('#csh_card_type').val(m.card_type);
									callMemberOffline();
								}			
							});

							 //init sel type promotion
							$("#status_pro option[value='1']").attr('selected', 'selected');
							$("#status_pro").focus();		
							$("#status_pro").trigger('change');
							initFieldPromt();
							
						}//
					});
		}
	}//func

	function delayPayment(){
		$("#btn_cal_promotion").trigger('click');
	}//
	
	function cardLostStock(){
		/**
		*@desc Re check card stock out
		*@careate 15092015
		*@return
		*/
		var member_id=jQuery.trim($("#csh_member_no").val());		
		var msg_show="";
		var ops_day="";
		var opts={
					type:'post',
					url:'/sales/member/checknewcard',
					data:{
						member_id:member_id,
						ops_day:ops_day,
						action:'cardlost',
						rnd:Math.random()
					},
					success:function(data){
						if(data=='0'){
							jAlert('??????????????????????????????????????????????????????????????????????????? ????????????????????????????????????????????????????????????-?????????????????????????????????????????????','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='2'){
							jAlert('????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='3'){
							jAlert('?????????????????????????????????????????? ' + msg_show,'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='4'){
							jAlert('??????????????????????????????????????????????????????????????????????????? ??????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else{
							$("#csh_product_id").val('9900730');
							if(cmdEnterKey("csh_product_id")){
								setTimeout("delayPayment()",1000);
							}
							
						}
					}
				};
		$.ajax(opts);
	}//func
	
	function cardLost(){
		/**
		*@desc
		*@return
		*/
		var member_id=jQuery.trim($("#csh_member_no").val());
		//*WR03042014
		var msg_show="";
		var ops_day="";
		
		var opts={
					type:'post',
					//url:'/sales/member/checknewcard',
					url:'/sales/member/chknewcardlost',
					data:{
						member_id:member_id,
						ops_day:ops_day,
						action:'cardlost',
						rnd:Math.random()
					},
					success:function(data){
						if(data=='0'){
							jAlert('??????????????????????????????????????????????????????????????????????????? ????????????????????????????????????????????????????????????-?????????????????????????????????????????????','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='2'){
							jAlert('????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='3'){
							jAlert('?????????????????????????????????????????? ' + msg_show,'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='4'){
							jAlert('??????????????????????????????????????????????????????????????????????????? ??????????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='5'){
							jAlert('??????????????????????????????????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='6'){
							jAlert('?????????????????????????????????????????? OPS White Card','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else if(data=='7'){
							jAlert('?????????????????????????????????????????? OPS Gold Card','WARNING!',function(){
								initFieldOpenBill();
								return false;
							});	
						}else{
							
							$("#csh_product_id").val('9900730');
							if(cmdEnterKey("csh_product_id")){
								setTimeout("delayPayment()",1000);
							}
						}
					}
				};
		$.ajax(opts);
	}//func

	

	function getMemberProfile(col,txtsearch){
		/**
		*@desc DEFAULT OFFLINE MODE ONLY
		*@param String col :
		*@param String txtsearch :
		*@return void
		*/
		 $.getJSON(
					"/sales/member/memberprofile",
					{
						col:col,
						txtsearch:txtsearch,
						action:'memberprofile'
					},
					function(data){
						if(data=='0'){
							jAlert('???????????????????????????????????????????????????', 'WARNING!',function(){
								$("#info_refer_member_id").html('');
								$("#info_member_fullname").html('');
								$("#info_member_applydate").html('');
								$("#info_member_expiredate").html('');
								$("#info_member_birthday").html('');
								$("#info_member_opsday").html('');
								$("#info_apply_promo").html('');
								$("#info_apply_promo_detail").html('');
								$("#info_member_address").html('');
								$("#lost_member_name").focus();
								return false;
							});	
						}else{
							$.each(data.member, function(i,m){
								//alert(m.exist);
								if(m.exist=='yes'){
									var member_fullname=m.name+" "+m.surname;
									var arr_birthday=m.birthday.split('-');
									var birthday=arr_birthday[2]+"/"
										+arr_birthday[1]+"/"
										+(parseInt(arr_birthday[0]));
									var arr_apply_date=m.apply_date.split('-');
									var apply_date=arr_apply_date[2]+"/"
										+arr_apply_date[1]+"/"
										+(parseInt(arr_apply_date[0]));
									var arr_expire_date=m.expire_date.split('-');
									var expire_date=arr_expire_date[2]+"/"
										+arr_expire_date[1]+"/"
										+(parseInt(arr_expire_date[0]));
									var refer_member_id='';
									var remark=m.remark;
									var percent_discount=parseInt(m.percent_discount);
									var mp_point_sum=m.mp_point_sum;
									var buy_net=m.buy_net;
									var address=jQuery.trim(m.h_address)+" "+
												jQuery.trim(m.h_village_id)+" "+
												jQuery.trim(m.h_village)+" "+
												jQuery.trim(m.h_soi)+" "+
												jQuery.trim(m.h_road)+" "+
												jQuery.trim(m.h_district)+" "+
												jQuery.trim(m.h_amphur)+" "+
												jQuery.trim(m.h_province)+" "+
												jQuery.trim(m.h_postcode);
									//------------zone member info -----
									$("#info_refer_member_id").html(refer_member_id);
									$("#info_member_fullname").html(member_fullname);
									$("#info_member_applydate").html(apply_date);
									$("#info_member_expiredate").html(expire_date);
									$("#info_member_birthday").html(birthday);
									$("#info_member_opsday").html(m.special_day);
									$("#info_apply_promo").html(m.apply_promo);
									$("#info_apply_promo_detail").html(m.apply_promo_detail);
									$("#info_member_address").html(address);
								}									
							});
							$('#dlgFrmCardLost').dialog('close');
						}
					}				
			);
	}//func
	
	function getProMemPriv(member_no,birthday,apply_date,expire_date,tabs){
		/***
		 * @desc
		 */
		var opts_mempriv={
				type:'post',
				url:'/sales/member/memberprivilege',
				data:{
					member_no:member_no,
					birthday:birthday,
					apply_date:apply_date,
					expire_date:expire_date,
					rnd:Math.random()
				},success:function(data){
					objReqMemPriv=null;
					$(".tabs-priv-panel").html(data);												
					$('.tableNavMemberPriv ul li').not('.nokeyboard').navigate({
				        wrap:true
				    }).click(function(evt){	
					    evt.preventDefault();
					    evt.stopPropagation();									    						    
					    if($(this).attr('idpromo')=='nodata'){
					    	$('#dlgMemberPrivilege').dialog('close');
					    	initFieldPromt();
					    	return false;
						}
					    var selpromo=$.parseJSON($(this).attr('idpromo'));
					    $('#csh_promo_code').val(selpromo.promo_code);
					    if(selpromo.promo_status=='N'){
						    //????????????????????????????????????
					    	jAlert(selpromo.promo_detail,'WARNING!',function(){
								return false;
							});	
						    return false;
						}
					    
					    $('#csh_trn_diary2_sl').val('Y');
					    $('.tableNavMemberPriv ul').navigate('destroy');
					    $('#dlgMemberPrivilege').dialog('close');
					    
					    //lock ???????????????????????? jinet 10.100.53.2
					    $('#csh_promo_year').val(selpromo.promo_year);
					    var opts_lockpriv={
						    	type:'post',
						    	url:'/sales/cashier/markmemberuse',
						    	data:{
						    		promo_code:selpromo.promo_code,
						    		member_no:$('#csh_member_no').val(),
						    		customer_id:$('#csh_customer_id').val(),
						    		promo_year:selpromo.promo_year,
						    		rnd:Math.random()									    		
						    	}
						    };
					    $.ajax(opts_lockpriv);
					    
					    //???????????????????????????????????????????????????????????????
					    if(selpromo.promo_code=='NEWBIRTH'){
						    //other promotion in table promo_other
							    var opts_pro={
									    	type:'post',
									    	url:'/sales/member/otherpromo',
									    	data:{
							    					promo_code:selpromo.promo_code,
							    					rnd:Math.random()
							    			},
							    			success:function(data){
								    			if(data!=''){
								    				 var selpromo2=$.parseJSON(data);												    				
								    				 $('#csh_play_main_promotion').val(selpromo2.play_main_promotion);
								    				 $('#csh_play_last_promotion').val(selpromo2.play_last_promotion);
								    				 $('#csh_get_promotion').val(selpromo2.get_promotion);
								    				 $('#csh_start_baht').val(selpromo2.start_baht);
								    				 $('#csh_end_baht').val(selpromo2.end_baht);
								    				 $('#csh_discount_member').val(selpromo2.discount_member);
								    				 $('#csh_get_point').val(selpromo2.get_point);	
								    				 $("#csh_buy_type").val(selpromo2.buy_type);
								    				 $("#csh_buy_status").val(selpromo2.buy_status);											    				
								    				 $('#csh_web_promo').val('Y');//*WR14082012 ????????????????????????????????????????????????????????????????????????
								    				 var arr_birthday=$('#info_member_birthday').html().split('/');
								    				 var arr_docdate=$('#csh_doc_date').html().split('/');
								    				if(arr_birthday[1]==arr_docdate[1]){
								    				 	$("#csh_xpoint").val(selpromo2.xpoint);						
								    				}else{
								    					$("#csh_xpoint").val('1');	
									    			}
								    				 //birthday_month,xpoint
								    				 $('#other_promotion_title').html(selpromo2.promo_des);
								    				 if(selpromo2.type_discount=='PERCENT'){
								    					 $('#csh_percent_discount').html(parseInt(selpromo2.discount));
									    			 }		
									    			$('#status_pro').trigger('change');		 
								    				initFieldPromt(); 
									    		}
								    		}
									    };
							    $.ajax(opts_pro);

					    }else{
						    //other promotion in table promo_head
						    $.ajax({
							    type:'post',
							    url:'/sales/member/memotherpro',
							    data:{
							    	promo_code:selpromo.promo_code,
							    	rnd:Math.random()
							    },success:function(data){
								    if(data=='0'){
								    	jAlert('???????????????????????????????????????????????????????????? Please check and try again.', 'WARNING!',function(){
								    		$("#other_promotion_cmd").html('');
									    	$('.tableNavMemberPriv ul').navigate('destroy');
									  		$("#dlgOtherPromotion").dialog("close");
									  		initFormCashier();
											initFieldOpenBill();
											return false;
										});	
								    	return false;
									}
								    var sel_promo=$.parseJSON(data);
								    $('#csh_web_promo').val(sel_promo.web_promo);//forcheck not play with main promotion
							    	$('#csh_gap_promotion').val('N');
									$('#csh_trn_diary2_sl').val('Y');
									 $('#csh_discount_member').val(sel_promo.discount_member);
									if(parseInt(sel_promo.point)==0){
										$('#csh_get_point').val('N');
									}
									if(sel_promo.discount_member=='N'){
										$('#csh_percent_discount').html('0');
									}
									//alert(sel_promo.limite_qty);
									$('#csh_limite_qty').val(sel_promo.limite_qty);
									$('#csh_check_repeat').val(sel_promo.check_repeat);
								    $('#csh_promo_tp').val(sel_promo.promo_tp);
								    $('#csh_member_tp').val(sel_promo.member_tp);
								    $('#csh_promo_discount').val(sel_promo.discount);
								    $("#csh_promo_code").val(sel_promo.promo_code);
								    $('#csh_promo_amt').val(sel_promo.promo_amt);
								    $('#csh_promo_amt_type').val(sel_promo.promo_amt_type);
									$('#csh_promo_point').val(sel_promo.point);
									$('#csh_promo_point_to_discount').val(sel_promo.point_to_discount);	
								  	$("#other_promotion_title").html(sel_promo.promo_des);
								  	 if(parseInt(sel_promo.promo_amt)!=0 && sel_promo.promo_amt_type!=''){
										//freebirth
									    if(sel_promo.promo_amt_type=='L'){
									    	$("#csh_start_baht").val('0');
											$("#csh_end_baht").val(sel_promo.promo_amt);
											$("#csh_buy_type").val('G');
						    				$("#csh_buy_status").val('L');	
						    				///////TEST 11062012///
						    				$("#csh_play_main_promotion").val('N');
						    				$("#csh_play_last_promotion").val('N');
						    				//////TEST 11062012///////
										}		
										 $("#dlgOtherPromotion").dialog("close");
										 initFieldPromt();
									}else 	if(sel_promo.promo_tp=='M'){
								  		getDiscountNetAmt();
								  		$("#dlgOtherPromotion").dialog("close");
										 initFieldPromt();
								  	}else if(sel_promo.promo_tp=='F'){
								  		$("#other_promotion_cmd").html('');
								  		$("#dlgOtherPromotion").dialog("close");
								  		//select product freee																  		
								  		selProductFree(sel_promo.promo_code);
									 }
								}
							 });										
						    //other promotion in table promo_head
						}								    
						});//end click
					}
		};
		objReqMemPriv=$.ajax(opts_mempriv);//call ajax					
		setTimeout(function(){
			if(objReqMemPriv){
				$('#dlgMemberPrivilege').dialog('close');
				objReqMemPriv.abort();
				objReqMemPriv=null;
				jConfirm('????????????????????????????????????????????????????????????????????? Server ?????????????????????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????','CONFIRM MESSAGE', function(r){
			        if(r){
			        	showMemberPrivilege(member_no,birthday,apply_date,expire_date);
			        }else{
			        	initFieldPromt();
				     }
				});
				//objReqMemPriv=$.ajax(opts_mempriv);//call ajax 1 again							
			}
			
		},60000);//1 minute
	}//func
	
	function showMemberPrivilege(member_no,birthday,apply_date,expire_date){
		/**
		*@name 
		*@desc from talble promo_other
		*/
		var objReqMemPriv=null;
		var member_no=member_no;
		var birthday=birthday;
		var apply_date=apply_date;
		var expire_date=expire_date;
		var chk_status_pro='Y';
		var opts_dlgMemberPriv={
				autoOpen:false,
				width:'85%',
				height:'575',
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"<span class='ui-icon ui-icon-cart'></span>CHOOSE PRIVILEGE",
				open:function(){					
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
    			   $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"2px","background-color":"#F9F9F9","font-size":"18px","color":"#000"}); 
					$('#tabs-priv').tabs({
						collapsible: true
					});
					var postData = {};				
					var tcase=1;
					$("#tabs-priv").tabs('destroy');					
					$("#tabs-priv").tabs({
						 selected:0,				
						 load: function(event, ui){							
					       },
						  select: function(evt,ui) {	
							  if(objReqMemPriv){
								 $('#tabs-priv').tabs('abort');
								 //objReqMemPriv.abort();								 
							 }
							tcase=ui.index+1;				
							postData = {
									member_no:member_no,
									birthday:birthday,
									apply_date:apply_date,
									expire_date:expire_date,
									tcase:tcase,
									rnd:Math.random()
						        };							
						    $(this).tabs("option", {ajaxOptions:{
						    	beforeSend: function (xhttp) {	
						    		objReqMemPriv=xhttp;						    	
                                },
						    	type:'post',			
						    	cache:false,
						    	data: postData
						    }});
						  },
						  ajaxOptions: {
						    type: 'post',
						    error: function(xhr,status,index,anchor){						     
						    },
						    data:{
						    	member_no:member_no,
								birthday:birthday,
								apply_date:apply_date,
								expire_date:expire_date,
								tcase:tcase,
								rnd:Math.random()
						    },
						    complete:function(data){
						    	///////// START //////			
						    	objReqMemPriv=null;
						    	///////// END    //////
						    }
						  }
						});
				},
				buttons:{
					 "Close":function(){ 		
						 chk_status_pro='N';
						 $("#tabs-priv").tabs('destroy');
						 $("#dlgMemberPrivilege").dialog('close');
						 setTimeout(function(){initFieldPromt();},400);
					 }
				}
				,
				close:function(evt,ui){						
					 if(objReqMemPriv){
						 //objReqMemPriv.abort();
						 $("#tabs-priv").tabs('destroy');
					 }
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){										
						 setTimeout(function(){initFieldPromt();},400);
					}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){						 	
						setTimeout(function(){initFieldPromt();},400);						
					}else{
						var status_no=$('#csh_status_no').val();
						var promo_code=$('#csh_promo_code').val();
						var member_no=$('#csh_member_no').val();	
						var promo_tp=$('#csh_promo_tp').val();
						//alert("status_pro :: " + chk_status_pro);
						if(promo_tp=='NEWBIRTH' && chk_status_pro=='Y'){
							//setTimeout(function(){fromreadidcard('',member_no,status_no);},1000);
							setTimeout(function(){
								fromreadprofile(promo_code,'Y',member_no,'','mobile_no','chk_mem_idcard','New Birth');
								//fromreadprofile(sel_promo.promo_code,otp_status,member_no,id_card,mobile_no,chk_mem_idcard,sel_promo.promo_des);
							},1000);
							
						}						
					}							
					////////////////////////////////////////26092014 ///////////////////////////////////////////
					/*
					var status_no=$('#csh_status_no').val();
					var promo_code=$('#csh_promo_code').val();
					var member_no=$('#csh_member_no').val();	
					var promo_tp=$('#csh_promo_tp').val();
					if(promo_tp=='NEWBIRTH'){
						setTimeout(function(){fromreadidcard('',member_no,status_no);},1000);
					}
					*/
					////////////////////////////////////////26092014 ///////////////////////////////////////////					
				}
		};		
		$('#dlgMemberPrivilege').dialog('destroy');
		$('#dlgMemberPrivilege').dialog(opts_dlgMemberPriv);			
		$('#dlgMemberPrivilege').dialog('open');		
	}//func
	
	var objReqMemberInfo=null;	
	function callMemberInfo(flg){
		/**
		 *@desc
		 *@param Char flg : N=normal;S=Special
		 *@return
		 *@modify 17112011
		 */
		
		var status_no=$("#csh_status_no").val();
		var member_no=$("#csh_member_no").val();	
		if(jQuery.trim(member_no)=='' && flg=='N'){	
			$('#csh_member_fullname').html("&nbsp;");
			$('#csh_member_type').html("WALK IN");
			initFieldPromt();
			return false;
		}else if(jQuery.trim(member_no)=='' && flg=='S'){	
			jAlert('Please specify member id.','WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
		}else{				
			objReqMemberInfo=$.getJSON(
					"/sales/cashier/ajax",
					{
						status_no:status_no,
						member_no:member_no,
						actions:'memberinfo'
					},
					function(data){							
						objReqMemberInfo=null;
						if(data=='2'){
							jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
								initFieldOpenBill();
								return false;
							});
						}else{
							$.each(data.member, function(i,m){
								if(m.exist=='yes'){
									if(m.surname!=undefined){
										var member_fullname=m.name+" "+m.surname;
									}else{
										var member_fullname=m.name;
									}									
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
										//*WR18042016
										if(m.expire_date=="2100-12-31"){
											expire_date="???????????????????????????????????????????????????";
										}
									}else{
										var expire_date='';
									}
									
									var refer_member_id='';
									var remark=m.remark;
									var percent_discount='0';
									if(m.percent_discount!=undefined){
										percent_discount=parseInt(m.percent_discount);
									}
									//var percent_discount=parseInt(m.percent_discount);
									var mp_point_sum=m.mp_point_sum;
									var buy_net=m.buy_net;									
									var address=jQuery.trim(m.address)+" "+
									jQuery.trim(m.sub_district)+" "+
									jQuery.trim(m.district)+" "+
									jQuery.trim(m.province_name)+" "+
									jQuery.trim(m.zip)+" <br>"+
									jQuery.trim(m.mobile_no);
									$('#csh_ops_day').val(m.cust_day);
									$('#csh_id_card').val(m.id_card);
									$('#csh_mobile_no').val(m.mobile_no);									
									$('#csh_member_fullname').html(member_fullname);
									$('#csh_birthday').html(birthday);
									$('#csh_apply_date').html(apply_date);
									$('#csh_expire_date').html(expire_date);
									$('#csh_member_type').html(remark);
									$('#csh_percent_discount').html(percent_discount);
									$('#csh_point_total').html(mp_point_sum);
									$('#csh_buy_net').html(buy_net);
									$('#csh_address').html(address);
									//WR24122013 POINT EXPIRE
									$("#csh_transfer_point").val(m.transfer_point);
									$("#csh_expire_transfer_point").val(m.expire_transfer_point);
									$("#csh_curr_point").val(m.curr_point);
									$("#csh_balance_point").val(m.balance_point);
									
									//*WR08022017 for suport show point expire date									
//									if ( typeof(m.expire_transfer_point) !== "undefined" && 
//											m.expire_transfer_point !== null && m.expire_transfer_point!=="0000-00-00") {
//										var arr_pointexpire=m.expire_transfer_point.split('-');
//										var year_pointexpire=parseInt(arr_pointexpire[0])+543;
//										var expire_point_transfer_show=arr_pointexpire[2]+"/" +arr_pointexpire[1]+ "/" +  year_pointexpire;
//										expire_point_transfer_show="("+expire_point_transfer_show+")";										
//										$('#csh_expire_transfer_point_show').html(expire_point_transfer_show);
//									}
									//------------zone member info -----		
									//*WR04042014
									if(m.application_id=='' || m.application_id==undefined){
										var spd_age_card=m.special_day + " (" + m.age_card + " ??????) ";
										var apply_promo=m.apply_promo;
									}else{
										var spd_age_card=m.special_day;
										var apply_promo=m.application_id;
									}
									$("#info_refer_member_id").html(refer_member_id);
									$("#info_member_fullname").html(member_fullname);
									$("#info_member_applydate").html(apply_date);
									$("#info_member_expiredate").html(expire_date);
									$("#info_member_birthday").html(birthday);
									
									//$("#info_member_opsday").html(m.special_day);
									//$("#info_member_opsday").html(spd_age_card);
									
									//*WR04102016 append card level
									var info_member_opsday=spd_age_card + " #" + m.card_level;									
									$("#info_member_opsday").html(info_member_opsday);
									
									//$("#info_apply_promo").html(m.apply_promo);
									$("#info_apply_promo").html(apply_promo);
									$("#info_apply_promo_detail").html(m.apply_promo_detail);
									if(m.status=='1'){
										$("#info_apply_promo_detail").html(m.card_detail);
									}
									$("#info_member_address").html(address);
									//*WR13032013
									if(m.mp_point_sum_1!=''){
										var str_mp_tmp=m.mp_point_sum_1;
									}else{
										var str_mp_tmp=0;
									}
									$("div #info_member_address").append("<br><span style='color:#999;'>[" + parseInt(str_mp_tmp) + "]</span>");
									
									//------------zone member info -----							
									$("#csh_customer_id").val(m.customer_id);
									//?????????????????????????????????????????????
									$('#csh_get_point').val('Y');					
									// for VIP member
									$('#csh_member_vip').val(m.vip);
									$('#csh_vip_limited').val(m.limited);
									$('#csh_vip_limited_type').val(m.limited_type);
									$('#csh_vip_sum_amt').val(m.sum_amt);//???????????????????????????????????????????????? balance
									if(m.vip=='1'){
										$("#csh_play_main_promotion").val('N');
										$("#csh_play_last_promotion").val('N');
										$('#csh_get_point').val('N');
										$('#csh_percent_discount').html(m.member_percent);
										//check today balance
										var str_type="";
										(m.limited_type=='N')?str_type="???????????????????????????":str_type="?????????????????????";									
										//??????????????????????????????????????????
										//var vip_balance=parseFloat(m.sum_amt)-parseFloat(m.diary1_sum_amt);										
										//????????????????????????????????????
										//var vip_used=parseFloat(m.limited)-vip_balance;
										//14102013 apply for joke service
										var vip_balance=parseFloat(m.sum_amt);	
										var vip_used=parseFloat(m.limited) - parseFloat(m.sum_amt);
										//???????????????????????????????????????????????? balance
										$('#csh_vip_sum_amt').val(vip_balance);
										var str_vip_info="?????????????????? "+addCommas(m.limited)+" ?????????  "+str_type+"<br>??????????????????????????????????????????????????? "+addCommas(vip_used)+" ?????????<br>????????????????????? "+addCommas(vip_balance)+" ?????????";
										$("#info_apply_promo_detail").html(str_vip_info);
										$("#csh_member_type").html("VIP Card");
									}	
									//-------------17082016 add card type for platinum card lucky draw promotion---------------
									var mem_card_info=m.card_level + "#" + m.ops;
									$('#id_smspromo').val(mem_card_info);
									
									//-------------------------start add 2 display ---------------------
									
									if(getOnlineStatus()===1){
										var obj_m2display=$.ajax({
											  type:'post',
											  url:'/sales/accessory/add2display',
											  data:{
												  member_no:member_no,
												  rnd:Math.random()
											  },success:function(){
												  obj_m2display=null;
											  }
										 });
										
										// Wait for 5 seconds
										setTimeout(function(){									  
										  if (obj_m2display){
											  obj_m2display.abort();
											  obj_m2display=null;
										  }
										},5000);
									}
									
									//-------------------------end   add 2 display ---------------------
									
								}else if(m.exist=='no'){
									//call member com_special_day
									var remark=m.remark;
									var percent_discount=parseInt(m.percent_discount);
									$('#csh_member_type').html(remark);
									$('#csh_percent_discount').html(percent_discount);
								}
								
								$("#csh_accordion").accordion({ active:0});				
								
								if(m.status=='1'){
									if(m.mem_status=='N'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active ?????????????????????????????????????????????????????? \n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}else if(m.mem_status=='F'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ???????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}else if(m.mem_status=='T'){
										jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ????????????????????????????????????????????????\n Please check and try again.', 'WARNING!',function(){
											initFieldOpenBill();
											return false;
										});	
									}
									//return false;
								}
								
								if(m.forgot_card=='Y'){
									jAlert('????????????????????? ?????????????????????????????? ??????????????????????????????<br>????????????????????????????????????????????????????????????(SMS) ???????????????????????????<br>?????????????????????????????? Admin', 'WARNING!',function(){
										initFieldOpenBill();
										return false;
									});	
									return false;	
								}
								
								if( m.status=='1' || m.expire_status=='Y'){
									jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
										initFieldOpenBill();
										return false;
									});	
									return false;									
								}
								
								//call member privilege
								if($('#csh_member_vip').val()!='1' && status_no!='01' && status_no!='02' && status_no!='04' && status_no!='05'  && status_no!='06'){
									jConfirm('DO YOU WANT MEMBER PRIVILEGE?','CONFIRM MESSAGE', function(r){
										 if(r){
											 	showMemberPrivilege(m.member_no,m.birthday,m.apply_date,m.expire_date);
									        }else{
									        	initFieldPromt();
										     }
									        return false;
									});									
								}else if(status_no=='06'){									
									 chkCnMember();
								}else{
									initFieldPromt();
								}													
							});
							
						}
					}				
			);//end json
			
			// Wait for 15 seconds
			setTimeout(function(){
			  // If the request is still running, abort it.
			  if (objReqMemberInfo){
				  objReqMemberInfo.abort();
				  objReqMemberInfo=null;
				  jConfirm('??????????????????????????????????????????????????????????????????????????????????????????????????? ONLINE\n ???????????????????????????????????????????????????????????????????????????	OFFLINE ??????????????????????','CONFIRM MESSAGE', function(r){
				        if(r){
				        	callMemberOffline();
				        	return false;
				        }else{
				        	initFieldOpenBill();
				        	return false;
				        }
					});//jconfirm
			  }
			},30000);
			
		}//end if
		return false;
	}//func

	function stockLost(){
		/**
		*@desc
		*@return
		*/
		var employee_id=$('#csh_member_no').val();
		employee_id=$.trim(employee_id);
		if(employee_id.length==0){
			jAlert('????????????????????????????????????????????????????????????', 'WARNING!',function(){
				initFieldOpenBill();
				return false;
			});	
			return false;
		}else{			
			$("#status_pro option[value='1']").attr('selected', 'selected');
			$("#status_pro").focus();		
			$("#status_pro").trigger('change');
			$('#csh_play_main_promotion').val('N');
			$('#csh_play_last_promotion').val('N');
			$('#csh_get_point').val('N');
			$('#csh_trn_diary2_sl').val('Y');
			$('#csh_start_baht').val('');//by set on initFormCashier
			$('#csh_end_baht').val('');//by set on initFormCashier
			//check employee info
			 var opts={
			            type:"post",
			            url:"/sales/cashier/getemp",
			            async:false,
			            data:{
			            	employee_id:employee_id,
							actions:'saleman'
		            	},
		            	success:function(data){
			            	var arr_data=data.split('#');
							if($.trim(arr_data[0])==""){
								jAlert('???????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
									initFieldOpenBill();
									return false;
				    			});
							}else if(data!=""){
				               $("#info_refer_member_id").html(arr_data[0]);
				               $("#info_member_fullname").html(arr_data[1]+" "+arr_data[2]);
				               //audit
				               $("<div id='dlgAuditChkStock'></div>").dialog({
				            	   autoOpen:true,
					   				width:250,		
					   				height:'auto',	
					   				modal:true,
					   				resizable:false,
					   				position:"center",
					   				showOpt: {direction: 'up'},		
					   				closeOnEscape: true,
					   				title:"<span class='ui-icon ui-icon-person'></span>???????????????????????? Audit",
					   				open: function(){    
					   					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
										$(this).dialog('widget')
								            .find('.ui-dialog-titlebar')
								            .removeClass('ui-corner-all')
								            .addClass('ui-corner-top');
										$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
																														"font-size":"26px","color":"#FF0000",
																														"padding":"5 0.1em 0 0.1em"});   
						   				$("#dlgAuditChkStock").append("<input type='password' id='audit_password' size='20' class='input-text-pos'/>");
							   			$("#audit_password").focus();
						   				$("#audit_password").keypress(function(evt){
						   					var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						   			        if(key == 13){
							   			        $.ajax({
								   			         type:"post",
											            url:"/sales/cashier/getemp",
											            async:false,
											            data:{
											            	employee_id:$("#audit_password").val(),
															actions:'audit'
										            	},
										            	success:function(data){
										            		var arr_data=data.split('#');
															if($.trim(arr_data[0])==""){
																jAlert('????????????????????????????????? Audit Please check and try again.','WARNING!',function(){
																	$("#audit_password").focus();
																	return false;
												    			});
															}else if(data!=""){
																$("#dlgAuditChkStock").dialog('close');
																//get AO
																$.ajax({
																	type:'post',
																	url:'/sales/cashier/getao',
																	data:{
																		status_no:$('#csh_status_no').val(),
																		rnd:Math.random()
																	},success:function(){
																		getCshTemp('N');
																		initFieldPromt();
																	}
																});
																
															}
										            	}
								   			        });
							   			        return false;
						   			        }
							   			 });
					   				},close:function(){
						   				$(this).remove();
						   			}
					           });
						       
				            }
			            	
			            }								            
			    };
	            $.ajax(opts);
		}//if
	}//func
	
	function dlgCardLost(){
		/**
		*@desc
		** modify : 22092014
		*@return
		*/
		var dialogOpts_dlgCardLost = {
				autoOpen:false,
				width:550,		
				height:'auto',	
				modal:true,
				resizable:true,
				position:"center",
				showOpt: {direction: 'up'},		
				closeOnEscape: true,
				title:"<span class='ui-icon ui-icon-person'></span>Select",
				open: function(){     
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	    			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc","padding":"0","color":"#000"});
						$("input:text").blur();
						$("#dlgCardLost").html("");
						$("#dlgCardLost").load("/sales/member/cardlost?actions=cardlost&now="+Math.random(),
						function(){	
							$('.tableNavCardLost ul li').not('.nokeyboard').navigate({
						          wrap:true
						    }).click(function(e){
							    e.preventDefault();
							    e.stopPropagation();
							    var chk_ble=0;
							    var card_status=$(this).attr("id");//C1 ?????????????????????,C2 ????????????????????????
							    $("#csh_card_status").val(card_status);
							    var status_no=$("#csh_status_no").val();
								$("#dlgCardLost").dialog("close");
								//*WR28032014 change card ops day
								if(card_status=='C3'){
									chkOldCardToChange();
									return false;
								}
								var opt_frmcardlost={
										autoOpen:true,
										width:'75%',		
										height:450,	
										modal:true,
										resizable:true,
										position:"center",
										showOpt: {direction: 'up'},		
										closeOnEscape: true,
										title:"<span class='ui-icon ui-icon-person'></span>SEARCH MEMBER CARD",
										open:function(){
											$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
											$(this).dialog('widget')
									            .find('.ui-dialog-titlebar')
									            .removeClass('ui-corner-all')
									            .addClass('ui-corner-top');
						    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#CFE2E5",//#CFE2E5
						    			    	"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
						    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
						    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"} );
							    			$('#lost_mobile_no').html('');
							    			$('#card_lost_content').html('');
											$("#lost_member_name").val('');
											$("#lost_member_surname").val('');
											$("#lost_id_card").val('').focus();					
											$("#lost_mobile_no,#lost_id_card,#lost_member_name,#lost_member_surname").keypress(function(evt){
												var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 												
										        if(key == 13){
										        	e.stopImmediatePropagation();	
											        //evt.stopPropagation();
											        evt.preventDefault();
											        $("#dlgFrmCardLost").parents('.ui-dialog').first().find('.ui-button').first().click();
											        return false;
										        }
											});						
										},
										close:function(evt,ui){											
											if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
												if($("#csh_status_no").val()=='05'){	
													initFormCashier();
													initFieldOpenBill();
												}
											}
											if(evt.originalEvent && $(evt.originalEvent.which==27)){
												if($("#csh_status_no").val()=='05'){
													initFormCashier();
													initFieldOpenBill();
												}
											}
											initFieldOpenBill();
										},
										 buttons:{
											 "SEARCH":function(e){ 			
												e.stopImmediatePropagation();
												// e.stopPropagation();
												e.preventDefault();
												// START MEMBER CARD LOST											  
												var lost_mobile_no=$.trim($("#lost_mobile_no").val());
											    var lost_id_card=$.trim($("#lost_id_card").val());											    
												var lost_member_name=$.trim($("#lost_member_name").val());
												var lost_member_surname=$.trim($("#lost_member_surname").val());		
												if(lost_mobile_no=='' && lost_id_card=='' && lost_member_name=='' && lost_member_surname==''){
													return false;
												}
												
												if(chk_ble==0){
													//////////////////////////////////////////////////////////////////////START\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
													chk_ble=1;
													$.ajax({
														type:'post',
														url:'/sales/member/searchmember',
														data:{
															mobile_no:lost_mobile_no,
															id_card:lost_id_card,
															name:lost_member_name,
															surname:lost_member_surname,
															card_status:card_status,
															rnd:Math.random()
														},success:function(data){
															chk_ble=0;
															$('#card_lost_content').html(data);
															$('.sel_cardlost').each(function(){
																$(this).click(function(e){																
																	e.preventDefault();
																	var sel_member_no=$(this).attr('id');			
																	//XXXXXXXXXXXXXXXXXXXXXXXXXXXX																			
																	$("#info_refer_member_id").html('');
																	$("#info_member_fullname").html('');
																	$("#info_member_applydate").html('');
																	$("#info_member_expiredate").html('');
																	$("#info_member_birthday").html('');
																	$("#info_member_opsday").html('');
																	$("#info_apply_promo").html('');
																	$("#info_apply_promo_detail").html('');
																	$("#info_member_address").html('');	
																	var obj_json=$(this).attr('idjson');	
																	obj=$.parseJSON(obj_json);
																	if(obj.err_status=='Y'){
																		alert(obj.remark);
																		return false;
																	}
																	var member_fullname=obj.name+" "+obj.surname;
																	if(obj.birthday!=''){
																		var arr_birthday=obj.birthday.split('-');
																		var birthday=arr_birthday[2]+"/"
																			+arr_birthday[1]+"/"
																			+(parseInt(arr_birthday[0]));
																	}else{
																		var birthday='';
																	}
																	if(obj.apply_date!=''){
																		var arr_apply_date=obj.apply_date.split('-');
																		var apply_date=arr_apply_date[2]+"/"
																			+arr_apply_date[1]+"/"
																			+(parseInt(arr_apply_date[0]));
																	}else{
																		var apply_date='';
																	}
		
																	if(obj.expire_date!=''){
																		var arr_expire_date=obj.expire_date.split('-');
																		var expire_date=arr_expire_date[2]+"/"
																			+arr_expire_date[1]+"/"
																			+(parseInt(arr_expire_date[0]));
																	}else{
																		var expire_date='';
																	}
																//*WR17082015 for check idcard
																/*
																var str_c=obj.idcard+"#"+obj.name+"#"+obj.surname+"#"+obj.birthday;
																$('#csh_id_card').val(str_c);
																apiread('callBackCardLost');
																*/
																var str_c=obj.idcard+"#"+obj.name+"#"+obj.surname+"#"+obj.birthday;
																$('#csh_id_card').val(str_c);
																var datastart=obj.member_no+"##"+obj.idcard+"#"+obj.mobile_no;
																apiread(datastart,'callBackCardLost'); 
																
																if(obj.link_status=='ONLINE'){
																	var refer_member_id=obj.member_no;
																	var remark=obj.remark;
																	var percent_discount=parseInt(obj.percent_discount);
																	//var mp_point_sum=obj.mp_point_sum;
																	var buy_net=obj.net;
																	var address=jQuery.trim(obj.address)+" "+
																				jQuery.trim(obj.sub_district)+" "+
																				jQuery.trim(obj.district)+" "+
																				jQuery.trim(obj.province_name)+" "+
																				jQuery.trim(obj.zip)+" <br>"+
																				jQuery.trim(obj.mobile_no);
																	if(obj.cust_day!=null && obj.cust_day!=''){
																				switch(obj.cust_day){
																				case "OPS1":cust_day="?????????????????????????????????1";break;
																				case "OPS2":cust_day="?????????????????????????????????2";break;
																				case "OPS3":cust_day="?????????????????????????????????3";break;
																				case "OPS4":cust_day="?????????????????????????????????4";break;
																				case "OPS0":cust_day="?????????????????????????????????0";break;
																				case "OPT1":cust_day="???????????????????????????1";break;
																				case "OPT2":cust_day="???????????????????????????2";break;
																				case "OPT3":cust_day="???????????????????????????3";break;
																				case "OPT4":cust_day="???????????????????????????4";break;
																					default:cust_day="";
																				}
																	}else{
																		cust_day='';
																	}
																	//------------zone member info -----
																	$("#info_refer_member_id").html(refer_member_id);
																	$("#info_member_fullname").html(member_fullname);
																	$("#info_member_applydate").html(apply_date);
																	$("#info_member_expiredate").html(expire_date);
																	$("#info_member_birthday").html(birthday);
																	$("#info_member_opsday").html(cust_day);
																	$("#info_apply_promo").html(obj.apply_promo);
																	//$("#info_apply_promo_detail").html(obj.apply_promo_detail);
																	$("#info_apply_promo_detail").empty().html(obj.cust_day);//*WR03042014
																	$("#info_member_address").html(address);																
																}else if(obj.link_status=='OFFLINE'){
																	var refer_member_id='';
																	var remark=obj.remark;
																	var percent_discount='0';
																	if(obj.percent_discount!=undefined){
																		percent_discount=parseInt(obj.percent_discount);
																	}
																	var mp_point_sum=obj.mp_point_sum;
																	var buy_net=obj.buy_net;
																	var address=$.trim(obj.h_address)+" "+
																					$.trim(obj.h_village_id)+" "+
																					$.trim(obj.h_village)+" "+
																					$.trim(obj.h_soi)+" "+
																					$.trim(obj.h_road)+" "+
																					$.trim(obj.h_district)+" "+
																					$.trim(obj.h_amphur)+" "+
																					$.trim(obj.h_province)+" "+
																					$.trim(obj.h_postcode);
																	$('#csh_member_fullname').html(member_fullname);
																	$('#csh_birthday').html(birthday);
																	$('#csh_apply_date').html(apply_date);
																	$('#csh_expire_date').html(expire_date);
																	$('#csh_member_type').html(remark);
																	$('#csh_percent_discount').html(percent_discount);
																	$('#csh_point_total').html(mp_point_sum);
																	$('#csh_buy_net').html(buy_net);
																	$('#csh_address').html(address);
																	//------------zone member info -----									
																	$("#info_refer_member_id").html(obj.member_no);
																	$("#info_member_fullname").html(member_fullname);
																	$("#info_member_applydate").html(apply_date);
																	$("#info_member_expiredate").html(expire_date);
																	$("#info_member_birthday").html(birthday);
																	$("#info_member_opsday").html(obj.special_day);
																	$("#info_apply_promo").html(obj.apply_promo);
																	$("#info_apply_promo_detail").html(obj.apply_promo_detail);
																	$("#info_member_address").html(address);
																	//------------zone member info -----			
																}
																$('#dlgFrmCardLost').dialog('close');
																//XXXXXXXXXXXXXXXXXXXXXXXXXXXX
																//add or update to crm_card obj		
																var opts_add2crm={
																		type:'post',
																		url:'/sales/member/add2crm',
																		data:{
																			str_json:obj_json,
																			link_status:obj.link_status,
																			rnd:Math.random()
																		},
																		success:function(data){																			
																		}
																	};
																	if(obj.link_status!='OFFLINE'){
																		$.ajax(opts_add2crm);
																	}
															
																});
																
															});
														}
													});							
													//////////////////////////////////////////////////////////////////////STOP\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
												}//end if
						
												// END MEMBER CARD LOST
												return false;
											}
										}
								};//end var opt_frmcardlost
								$('#dlgFrmCardLost').dialog('destroy');
								$('#dlgFrmCardLost').dialog(opt_frmcardlost);			
								$('#dlgFrmCardLost').dialog('open');
								return false;
							});					
						});
				},				
				close: function(evt,ui) {
					$('.tableNavCardLost ul').navigate('destroy');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						initFormCashier();
						initFieldOpenBill();
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){
						initFormCashier();
						initFieldOpenBill();
					}				
					$('#csh_accordion').accordion({active:0});
				 }
			};
			$('#dlgCardLost').dialog('destroy');
			$('#dlgCardLost').dialog(dialogOpts_dlgCardLost);			
			$('#dlgCardLost').dialog('open');
	}//func
	
	function browsDocStatus(){
		/**
		*@desc ?????????????????????????????????????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????????
		*@return
		*/
		var dialogOpts_dlgBrowsDocStatus = {
				autoOpen:false,
				width:550,		
				height:'auto',	
				modal:true,
				resizable:true,
				position: [250,110],
				show:"slid",
				showOpt: {direction: 'down'},		
				closeOnEscape: true,	
				title:"<span class='ui-icon ui-icon-document'></span>Bill type",
				open: function(){  
					  $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					  $(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
						    																			"font-size":"26px","color":"#0000FF",
						    																			"padding":"0 0 0 0"});			
						$("#dlgBrowsDocStatus").html("");
						$("#dlgBrowsDocStatus").load("/sales/cashier/docstatus?actions=brows_docstatus&now="+Math.random(),
						function(){	
							$("#csh_member_no").blur();
							$('input').blur();
							$('div.tableNavDocStatus ul>li').not('.nokeyboard').navigate({
						          wrap:true
						    }).click(function(e){						    	
							    e.stopPropagation();
							    e.preventDefault();							    
							    $("#dlgBrowsDocStatus").dialog("close");
							    var seldoc=$.parseJSON($(this).attr('idd'));	
							    
//							    alert($(this).attr('idd'));
							    
//							    $("#csh_status_no").val(seldoc.status_no);
//								$("#csh_doc_tp_show").empty().html(seldoc.description);
								//show catalog
								if(seldoc.status_no=='01'){
									//?????????????????????????????????????????????
									initFormBySelDoc();
									//initFieldOpenBill();	
									initFieldForDialog();//*WR25032013
									//$('#csh_member_no').blur();									
									setTimeout(function(){selMemberPromotion();},400);											
									//getMemberCatalog();
								}else if(seldoc.status_no=='02'){
									//?????????????????????????????????????????????????????????????????????
									initFormBySelDoc();
									initFieldOpenBill();
								}else if(seldoc.status_no=='03'){
									//coupon ????????????????????????????????????
									initFormBySelDoc();
									empPrivilage();				
								}else if(seldoc.status_no=='04'){
									//??????????????????????????????????????????
									initFormBySelDoc();
									initFieldOpenBill();		
								}else if(seldoc.status_no=='05'){
									//?????????????????????????????????????????????
									initFormBySelDoc();
									dlgCardLost();
								}else if(seldoc.status_no=='19'){									
									//??????????????????????????????????????????
									initFormBySelDoc();
									setTimeout(function(){initFieldOpenBill();},400);
								}else if(seldoc.status_no=='06'){
									//??????????????????????????????????????? CN ????????????????????????????????????????????????????????????
									initFormBySelDoc();
									selCN();
								}else if(seldoc.status_no=='18'){
									initFormCashier();
									alert('comming soon!');
								}else{
									initFormCashier();
									initFieldOpenBill();
									$("#csh_member_no").focus();
								}		
								$("#csh_status_no").val(seldoc.status_no);
								$("#csh_doc_tp_show").empty().html(seldoc.description);
							});//navigate
							return false;								
						});//load ajax
				},				
				close: function(evt,ui) {
					evt.preventDefault();					
					$('div.tableNavDocStatus ul>li').navigate('destroy');
					$('#dlgBrowsDocStatus').dialog('destroy');
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){						
						initFormCashier();
						initFieldOpenBill();
					}else	if(evt.originalEvent && $(evt.originalEvent.which==27)){				
						initFormCashier();
						initFieldOpenBill();
					}else if($('#csh_status_no').val()!='19'){
						initTblTemp();//*WR05012013
					}
					$('#btnDocType').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
				}
			};
		    $('.tableNavDocStatus ul>li').navigate('destroy');
			$('#dlgBrowsDocStatus').dialog('destroy');
			$('#dlgBrowsDocStatus').dialog(dialogOpts_dlgBrowsDocStatus);			
			$('#dlgBrowsDocStatus').dialog('open');
		return false;
	}//func
	
	function initUser(){
		/**
		*@desc
		*@return
		*/
		var csh_user_id=$("#csh_user_id").val();
		if(csh_user_id!=''){
			var opts={
						type:'post',
						url:'/sales/cashier/getemp',
						data:{
							employee_id:csh_user_id,
							actions:'userlogin',
							now:Math.random()
						},
						success:function(data){
							$("#employee_name").html(data);
						}
					};
			$.ajax(opts);
		}
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
	
	function initFormBySelDoc(){
		/**
		 *@desc clear for init cashier ?????????????????????????????????????????????????????????????????????
		 *@desc ???????????????????????? default bill 00
		 *@return null
		 *@modify 15022013
		 */
		//get lock status
		getLockStatus();
		 //init sel type promotion
		$("#status_pro option[value=0]").attr('selected', 'selected');
		$('#csh_sum_qty').html('');
		//init doc_tp to status_no 00 SL default
		$('#csh_doc_tp').val('SL');		
		$('#flgbill').html('');
		$('#csh_discount_member').val('N');//???????????????????????????????????????????????????????????????????????????
		$('#csh_get_point').val('N');//??????????????????????????????????????????????????????
		//vat total
		$('#csh_doc_tp_vt').val('');
		$('#vt_name_val').val('');
		$('#vt_address_val1').val('');
		$('#vt_address_val2').val('');
		$('#vt_address_val3').val('');
		//*WR26012017
		$('#vt_passport_no_val').val('');
		
		//WR18122013
		$('#vt_taxid').val('');
		$('#vt_taxid_branch_seq').val('');
		$("#csh_transfer_point").val('');
		$("#csh_expire_transfer_point").val('');
		$("#csh_curr_point").val('');
		$("#csh_balance_point").val('');
		//sale
		$('#csh_member_no').val('');
		$('#csh_product_id').val('');
		$('#csh_quantity').val('1');		
		$('#csh_sum_amount').val('0.00');
		$('#csh_sum_discount').val('0.00');
		$('#csh_net').val('0.00');
		//point
		$('#csh_point_receive').val('0');
		$('#csh_point_used').val('0');
		$('#csh_point_net').val('0');
		//member data
		$('#csh_point_total').html('');		
		$('#csh_member_type').html('');
		$('#csh_point_total').html('');
		$('#csh_percent_discount').html('');
		$('#csh_member_fullname').html('');
		//member info	
		$('#info_refer_member_id').html('');
		$('#info_member_fullname').html('');
		$('#info_member_expiredate').html('');
		$('#info_member_applydate').html('');
		$('#info_member_birthday').html('');
		$('#info_member_opsday').html('');
		$('#info_member_address').html('');
		$('#info_apply_promo').html('');
		$('#info_apply_promo_detail').html('');
		$('#info_notify').html('');
		$('#info_notify').css({'display':'none'});
		//member catalog
		$('#csh_expire_date').val('');
		$('#csh_application_id').val('');
		$('#csh_max_product_no').val('');
		$('#csh_refer_member_id').val('');
		$('#csh_gift_set').val('');
		$('#csh_gift_set_amount').val('');
		$('#csh_register_free').val('');
		$('#csh_free_product').val('');//?????????????????????????????????????????????????????????
		$('#free_product_amount').val('');//??????????????????????????????????????????????????????
		$('#product_amount_type').val('');//????????????????????????????????????????????????????????????????????? G,L
		$('#csh_free_premium').val('');//???????????????????????????????????????????????????????????????
		$('#free_premium_amount').val('');//????????????????????????????????????????????????????????????
		$('#premium_amount_type').val('');//????????????????????????????????????????????????????????????????????? G,L
		//firstbuy
		$('#csh_first_sale').val('');
		$('#csh_first_percent').val('');
		$('#csh_xpoint').val('1');
		$('#csh_play_main_promotion').val('');//???????????????????????? select box  status_pro ?????????????????????????????????????????????????????????????????????
		$('#csh_play_last_promotion').val('');//???????????????????????? select box status_pro ?????????????????????????????????????????????????????????????????????
		$('#csh_first_limited').val('');
		$('#csh_add_first_percent').val('');
		$('#csh_start_date1').val('');
		$('#csh_end_date1').val('');
		//redeem point
		$('#csh_promo_code').val('');
		$('#csh_promo_tp').val('');
		$('#csh_promo_id').val('');
		$('#csh_get_promotion').val('');
		$('#csh_start_baht').val('');
		$('#csh_end_baht').val('');
		$('#csh_discount_member').val('');
		$('#csh_get_point').val('N');
		//other promtion
		$('#csh_gap_promotion').val('');
		$('#other_promotion_title').html('');
		$('#other_promotion_cmd').html('');
		$('#csh_promo_tp').val('');
		$('#csh_promo_st').val('');
	    $('#csh_member_tp').val('');
	    $('#csh_promo_discount').val('');
	    $('#csh_bal_discount').val('');
	    $("#csh_buy_type").val('');
	    $("#csh_buy_status").val('');
	    $('#csh_promo_amt').val('');
	    $('#csh_promo_amt_type').val('');
	    $('#csh_promo_point').val('');
	    $('#csh_promo_point_to_discount').val('');
	    $('#csh_trn_diary2_sl').val('N');
	    $('#csh_web_promo').val('N');
	    $('#csh_limite_qty').val('0');
	    $('#csh_check_repeat').val('');
	    //for ecoupon employee
	    $('#csh_ecp_amount_balance').val('');
	    $('#csh_ecp_percent_discount').val('');
	    //for member vip
	    $('#csh_member_vip').val('0');
	    $('#csh_vip_limited').val('0');
		$('#csh_vip_limited_type').val('');
		$('#csh_vip_sum_amt').val('');
		//for cn
		$('#cn_member_no').val('');
		$("#cn_doc_no").val('');
		$("#cn_amount").val('');
		//for log member privilage
		$('#csh_customer_id').val('');
		$('#csh_promo_year').val('');
		//for button
		$('#btn_cal_promotion').removeAttr('disabled');
		$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
		$('#csh_accordion').accordion({active:0});
		//for sms promotion
		$('#csh_promo_pos').val('');
		$('#csh_type_discount').val('');
		$('#status_smspromo').val('N');
		$('#id_smspromo').val('');
		$('#id_sms_promo_code').val('');
		$('#id_sms_mobile').val('');
		$('#id_redeem_code').val('');		
		//for form credit
		$('#txt_credit_value').val('');
		$('#txt_credit_no_value').val('');
		$('#txt_credit_no').val('');
		//*WR27042017 for support alipay
		$('#payment_channel').val('');
		$('#payment_transid').val('');
		
		//for coupon promotion WR23062014
		$('#status_couponpromo').val('');
		$('#csh_promo_code').val('');
		$('#csh_coupon_code').val('');
		//*WR 24122014
		$('#csh_ops_day').val('');
		$('#csh_id_card').val('');
		$('#csh_mobile_no').val('');
		//*WR20072015 bill manual
		$('#csh_bill_manual_no').val('');
 		$('#csh_ticket_no').val('');
	}//func

	function initFormCashier(){
		/**
		 *@desc clear for init cashier
		 *@return null
		 */
		//get lock status
		getLockStatus();
		 //init sel type promotion
		$("#status_pro option[value=0]").attr('selected', 'selected');		
		$('#csh_sum_qty').html('');
		$('#csh_refer_doc_no').val('');//01072013
		//init doc_tp to status_no 00 SL default
		$('#csh_doc_tp').val('SL');
		$('#csh_doc_tp_show').html('RECEIPT/BILLING');
		$('#csh_status_no').val('00');
		$('#flgbill').html('');
		$('#csh_discount_member').val('N');//???????????????????????????????????????????????????????????????????????????
		$('#csh_get_point').val('N');//??????????????????????????????????????????????????????
		//vat total
		$('#csh_doc_tp_vt').val('');
		$('#vt_name_val').val('');
		$('#vt_address_val1').val('');
		$('#vt_address_val2').val('');
		$('#vt_address_val3').val('');
		//*WR26012017
		$('#vt_passport_no_val').val('');
		
		//WR18122013
		$('#vt_taxid').val('');
		$('#vt_taxid_branch_seq').val('');
		$("#csh_transfer_point").val('');
		$("#csh_expire_transfer_point").val('');
		$("#csh_curr_point").val('');
		$("#csh_balance_point").val('');
		//sale
		$('#csh_member_no').val('');
		$('#csh_product_id').val('');
		$('#csh_quantity').val('1');
		$('#csh_sum_amount').val('0.00');
		$('#csh_sum_discount').val('0.00');
		$('#csh_net').val('0.00');
		//point
		$('#csh_point_receive').val('0');
		$('#csh_point_used').val('0');
		$('#csh_point_net').val('0');
		//member data
		$('#csh_point_total').html('');		
		$('#csh_member_type').html('');
		$('#csh_point_total').html('');
		$('#csh_percent_discount').html('');
		$('#csh_member_fullname').html('');
		//member info	
		$('#info_refer_member_id').html('');
		$('#info_member_fullname').html('');
		$('#info_member_expiredate').html('');
		$('#info_member_applydate').html('');
		$('#info_member_birthday').html('');
		$('#info_member_opsday').html('');
		$('#info_member_address').html('');
		$('#info_apply_promo').html('');
		$('#info_apply_promo_detail').html('');
		$('#info_notify').html('');
		$('#info_notify').css({'display':'none'});
		//member catalog
		$('#csh_expire_date').val('');
		$('#csh_application_id').val('');
		$('#csh_max_product_no').val('');
		$('#csh_refer_member_id').val('');
		$('#csh_gift_set').val('');
		$('#csh_gift_set_amount').val('');
		$('#csh_register_free').val('');
		$('#csh_free_product').val('');//?????????????????????????????????????????????????????????
		$('#free_product_amount').val('');//??????????????????????????????????????????????????????
		$('#product_amount_type').val('');//????????????????????????????????????????????????????????????????????? G,L
		$('#csh_free_premium').val('');//???????????????????????????????????????????????????????????????
		$('#free_premium_amount').val('');//????????????????????????????????????????????????????????????
		$('#premium_amount_type').val('');//????????????????????????????????????????????????????????????????????? G,L
		$('#csh_application_type').val('');//NEW=???????????????????????????, ALL=?????????????????????
		$('#csh_card_type').val('');//CARD=??????????????????????????????, IDC=?????????????????????????????????????????????????????????
		//firstbuy
		$('#csh_first_sale').val('');
		$('#csh_first_percent').val('');
		$('#csh_xpoint').val('1');
		$('#csh_play_main_promotion').val('');//???????????????????????? select box  status_pro ?????????????????????????????????????????????????????????????????????
		$('#csh_play_last_promotion').val('');//???????????????????????? select box status_pro ?????????????????????????????????????????????????????????????????????
		$('#csh_first_limited').val('');
		$('#csh_add_first_percent').val('');
		$('#csh_start_date1').val('');
		$('#csh_end_date1').val('');
		//redeem point
		$('#csh_promo_code').val('');
		$('#csh_promo_tp').val('');
		$('#csh_promo_id').val('');
		$('#csh_get_promotion').val('');
		$('#csh_start_baht').val('');
		$('#csh_end_baht').val('');
		$('#csh_discount_member').val('');
		$('#csh_get_point').val('N');
		//other promtion
		$('#csh_gap_promotion').val('');
		$('#other_promotion_title').html('');
		$('#other_promotion_cmd').html('');
		$('#csh_promo_tp').val('');
		$('#csh_promo_st').val('');
	    $('#csh_member_tp').val('');
	    $('#csh_promo_discount').val('');
	    $('#csh_bal_discount').val('');
	    $("#csh_buy_type").val('');
	    $("#csh_buy_status").val('');
	    $('#csh_promo_amt').val('');
	    $('#csh_promo_amt_type').val('');
	    $('#csh_promo_point').val('');
	    $('#csh_promo_point_to_discount').val('');
	    $('#csh_trn_diary2_sl').val('N');
	    $('#csh_web_promo').val('N');
	    $('#csh_limite_qty').val('0');
	    $('#csh_check_repeat').val('');
	    //for ecoupon employee
	    $('#csh_ecp_amount_balance').val('');
	    $('#csh_ecp_percent_discount').val('');
	    //for member vip
	    $('#csh_member_vip').val('0');
	    $('#csh_vip_limited').val('0');
		$('#csh_vip_limited_type').val('');
		$('#csh_vip_sum_amt').val('');
		//for cn
		$('#cn_member_no').val('');
		$("#cn_doc_no").val('');
		$("#cn_amount").val('');
		//for log member privilage
		$('#csh_customer_id').val('');
		$('#csh_promo_year').val('');
		//for button
		$('#btn_cal_promotion').removeAttr('disabled');
		$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
		$('#csh_accordion').accordion({active:0});
		//for sms promotion
		$('#csh_promo_pos').val('');
		$('#csh_type_discount').val('');
		$('#status_smspromo').val('N');
		$('#id_smspromo').val('');
		$('#id_sms_promo_code').val('');
		$('#id_sms_mobile').val('');
		$('#id_redeem_code').val('');
		//for form credit
		$('#txt_credit_value').val('');
		$('#txt_credit_no_value').val('');
		$('#txt_credit_no').val('');
		//*WR27042017 for support alipay
		$('#payment_channel').val('');
		$('#payment_transid').val('');
		
		//for coupon promotion WR23062014
		$('#status_couponpromo').val('');
		$('#csh_promo_code').val('');
		$('#csh_coupon_code').val('');
		//*WR 24122014
		$('#csh_ops_day').val('');
		$('#csh_id_card').val('');
		$('#csh_mobile_no').val('');
		//*WR20072015 bill manual
		$('#csh_bill_manual_no').val('');
 		$('#csh_ticket_no').val('');
	}//func

	function initMemberCatalog(){
		/**
		*@desc clear for init member catalog
		*@return null
		*/
		//vat total
		$('#csh_doc_tp_vt').val('');
		$('#vt_name_val').val('');
		$('#vt_address_val1').val('');
		$('#vt_address_val2').val('');
		$('#vt_address_val3').val('');
		//*WR26012017
		$('#vt_passport_no_val').val('');
		
		//WR18122013
		$('#vt_taxid').val('');
		$('#vt_taxid_branch_seq').val('');
		$("#csh_transfer_point").val('');
		$("#csh_expire_transfer_point").val('');
		$("#csh_curr_point").val('');
		$("#csh_balance_point").val('');
		//sale
		$('#csh_product_id').enable();
		$('#csh_quantity').enable();
		$('#csh_member_no').enable();
		
		$('#csh_product_id').val('');
		$('#csh_quantity').val('1');
		$('#csh_member_no').val('');
		$('#csh_sum_amount').val('0.00');
		$('#csh_sum_discount').val('0.00');
		$('#csh_net').val('0.00');
		//point
		$('#csh_point_receive').val('0');
		$('#csh_point_used').val('0');
		$('#csh_point_net').val('0');

		//member data
		$('#csh_point_total').html('');
		$('#csh_percent_discount').html('');
		$('#csh_member_type').html('');
		$('#csh_point_total').html('');
		$('#csh_member_fullname').html('');
		
		//member info	
		$('#info_refer_member_id').html('');
		$('#info_member_fullname').html('');
		$('#info_member_expiredate').html('');
		$('#info_member_applydate').html('');
		$('#info_member_birthday').html('');
		$('#info_member_opsday').html('');
		$('#info_member_address').html('');
		$('#info_apply_promo').html('');
		$('#info_apply_promo_detail').html('');
		$('#info_notify').html('');
		
		//member catalog
		$('#csh_expire_date').val('');
		$('#csh_application_id').val('');
		$('#csh_max_product_no').val('');
		$('#csh_refer_member_id').val('');
		$('#csh_gift_set').val('');
		$('#csh_gift_set_amount').val('');
		$('#csh_register_free').val('');
		//firstbuy
		$('#csh_first_sale').val('');
		$('#csh_first_percent').val('');
		$('#csh_xpoint').val('1');
		$('#csh_play_main_promotion').val('');
		$('#csh_play_last_promotion').val('');
		$('#csh_first_limited').val('');
		
		$('#csh_application_type').val('');//NEW=???????????????????????????, ALL=?????????????????????
		$('#csh_card_type').val('');//CARD=??????????????????????????????, IDC=?????????????????????????????????????????????????????????
		//*WR20072015 bill manual
		$('#csh_bill_manual_no').val('');
 		$('#csh_ticket_no').val('');
	}//func
	
	function swapCashier(swap_cashier_id){
		/**
		*@desc
		*@param String swap_cashier_id
		*@return
		*/
		$.getJSON(
				"/sales/cashier/getemp",
				{
					employee_id:swap_cashier_id,
					actions:'swapcashier'
				},
				function(data){
					if(data==null){
						jAlert('????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
							return false;
						});	
					}else{
						$.each(data.emp, function(i,m){
							if(m.exist=='yes'){
								if(m.check_in_status=='Y'){
									$("#csh_cashier_id").val(m.employee_id);
									$("#employee_name").html(m.name+" "+m.surname);
								}else{
									jAlert('??????????????????????????????????????????????????????????????? Login ????????????', 'WARNING!',function(){
										return false;
									});	
								}
							}							
						});
						
					}//
				});
		return false;
	}//func

	function popup(url,name,windowWidth,windowHeight){
		/**
		*@desc
		*@param String url
		*@param String name
		*@param Integer windowWidth
		*@param Integer windowHeight
		*@return
		*/
	    myleft=(screen.width)?(screen.width-windowWidth)/2:100;
	    mytop=(screen.height)?(screen.height-windowHeight)/2:100;
	    properties = "width="+windowWidth+",height="+windowHeight;
	    properties +=",scrollbars=yes, top="+mytop+",left="+myleft;
	    window.open(url,name,properties);
	} 

	function printBill(str_doc_no){			
		/**
		 *@desc
		 *@param String str_doc_no :
		 *@return
		 */
		var arr_docno=str_doc_no.split('#');
		var doc_no=arr_docno[1];	
		var doc_tp=arr_docno[2];
		var thermal_printer=arr_docno[3];
		var net_amt=parseInt(arr_docno[4]);		
		var status_no=arr_docno[5];
		var point_total_today=$('#csh_point_total').html();//???????????????????????? ??? ??????????????????
		
		//WR24122013
		var transfer_point=$('#csh_transfer_point').val();
		var expire_transfer_point=$('#csh_expire_transfer_point').val();
		var curr_point=$("#csh_curr_point").val();
		var balance_point=$("#csh_balance_point").val();
		var url2print="/sales/report/billvatshort";
		if(status_no=='03'){
			url2print="/sales/report/billecouponemployee";
			if(doc_tp=='VT'){
				url2print="/sales/report/billvatecouponemployee";
			}
		}else{
			if(doc_tp=='DN'){
				url2print="/sales/report/billshortdn";
			}else if(doc_tp=='VT'){
				url2print="/sales/report/billvattotal";
			}
		}
		//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 		
		//for print auto
		var str_tour=$('#csh_ops_day').val();
		if(str_tour!=''){
			var arr_cop=str_tour.split('#');
			if(arr_cop[2]=='GROUP_TOUR'){
				url2print="/sales/report/billvatshorten";
			}
		}
		var opts={
			type:'get',
			url:url2print,
			cache:false,
			data:{
					doc_no:doc_no,
					point_total_today:point_total_today,
					transfer_point:transfer_point,//WR24122013
					expire_transfer_point:expire_transfer_point,//WR24122013
					curr_point:curr_point,//WR24122013
					balance_point:balance_point,//WR24122013
					actions:'print',
					rnd:Math.random()
				},
			success:function(){
					return false;
			}
		};
		$.ajax(opts);
		return false;
	}//func

	function initTblTemp(){
		/**
		*@desc
		*@return
		*/
		$.ajax({
			type:'post',
			url:'/sales/cashier/initbltemp',
			cache:false,
			data:{
				rnd:Math.random()
			},
			success:function(data){
			}
		});
	}//func

	function chkDiaryTemp(){
		/**
		 *@desc 
		 *@return Char :Y is exist data ,N is none data
		*/
		var ret;
		var opts={
				type:'post',
				url:'/sales/cashier/countdiarytemp',
				cache:false,
				async:false,
				data:{
					actions:'cashier',
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');
					if(parseInt(arr_data[0])>0){		
						ret= 'Y';
					}else{
						jAlert('Not found Sell ??????Items. Please check and try again.','WARNING!',function(){
							return false;
		    			});
				    	ret= 'N';
					}
				}
			};
		$.ajax(opts);
		return ret;
	}//func
	
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
		        	evt.preventDefault();
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
	//AREA LOCK KEY BARCODE OR KEY MANUAL

	function getSelectedID(obj){
	    var id= "";
	    $(obj).closest('select').find("option:selected").each(function () {
	        id += $(this).val() + " ";
	    });
	    return id;
	}//func

	function getSelectedText(obj){
	    var str = "";
	    $(obj).closest('select').find("option:selected").each(function () {
	        str += $(this).text() + " ";
	    });
	    return str;
	}//func

	function selProductFree(promo_code){		
		/**
		 * @desc ???????????????????????????????????? ???????????? SAMPLING01
		 * @note :05092014 ??????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
		 * ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? ????????????????????????????????????
		 * @return NULL
		 */
		$("#dlgMemberProductFree").dialog({
	        autoOpen:true,
	        resizable:false,
	        modal: true,
	        closeOnEscape: true,
	        title:"<span class='ui-icon ui-icon-person'></span>?????????????????????????????????",
	        width:'60%',
	        height:350,
	        overlay:{ backgroundColor:"#999",opacity:0.5},
	        open:function(event,ui){
	        	 $(this).parent().children().children('.ui-dialog-titlebar-close').hide();		
	        	 $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');				
			     $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
				    																			"font-size":"26px","color":"#0000FF",
				    																			"padding":"0 0 0 0"});				   			
	        	 		$.ajax({
		        	 			type:'post',
		        	 			url:'/sales/cashier/productfree',
		        	 			data:{
	        	 					promo_code:promo_code,
	        	 					rnd:Math.random()
	        	 				},
	        	 				success:function(data){
		        	 				$("#dlgMemberProductFree").html('');
		        	 				$("#dlgMemberProductFree").html(data);
		        	 				$('.tableNavProductFree ul li').not('.nokeyboard').navigate({
								        wrap:true
								    }).click(function(e){		
								    	e.stopImmediatePropagation();	
								    	e.preventDefault();					
								    	//*WR22032013
								    	if($(this).attr('idpromo')=='nodata'){
										    $("#dlgMemberProductFree").dialog('close');
										    endOfProOther();//05092013
										    initFieldPromt();//05092013
										    return false;
										}
								    	var sel_promo=$.parseJSON($(this).attr('idpromo'));								    	
								    	$("#csh_play_main_promotion").val('N');
								    	$("#csh_play_last_promotion").val('N');								    	
								    	//*WR 22032013 ?????????????????????????????????????????????????????? auto ?????? temp ????????????????????????????????? key input csh_product_id
								    	$("#csh_product_id").val(sel_promo.product_id);
								    	$(this).blur();
								    	cmdEnterKey("csh_product_id");
								    	$("#csh_product_id").val('');//*WR17012013
									    $("#dlgMemberProductFree").dialog('close');			  
									    return false;
								    });
		        	 			}
		        	 		});	
		    },
	        close: function(evt, ui){
			        if(evt.which==27){
				        evt.stopPropagation();
				        evt.preventDefault();
				        //*WR05092014
				        endOfProOther();
					    initFieldPromt();
				    }
			        $('.tableNavProductFree ul li').navigate('destroy');
			        $("#dlgMemberProductFree").dialog('destroy');
			    }
	    });
		return false;
	}//func
	
	function getMemberInfo(member_no){
		/**
		 * desc :ref form search member window
		 * 23052016 ??????????????????????????????
		 */
		var objReq=$.getJSON(
				"/sales/cashier/ajax",
				{
					status_no:'',
					member_no:member_no,
					actions:'memberinfo'
				},
				function(data){	
					objReq=null;
					if(data=='2'){
						jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
							$('#sch_member_no').focus();
							return false;
						});
					}else{
						$.each(data.member, function(i,m){
							if(m.exist=='yes'){
								if(m.surname!=undefined){
									var member_fullname=m.name+" "+m.surname;
								}else{
									var member_fullname=m.name;
								}
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
								
								////-------------------------------
								if(m.link_status=='ONLINE'){
									var remark=m.remark;
									var percent_discount=parseInt(m.percent_discount);
									var mp_point_sum=m.point;
									var buy_net=m.net;													
									var address=$.trim(m.address)+" "+
									$.trim(m.sub_district)+" "+
									$.trim(m.district)+" "+
									$.trim(m.province_name)+" "+
									$.trim(m.zip);
									switch(m.cust_day){
									case "TH0":cust_day="";break;
									case "TH1":cust_day="?????????????????????????????????1";break;
									case "TH2":cust_day="?????????????????????????????????2";break;
									case "TH3":cust_day="?????????????????????????????????3";break;
									case "TH4":cust_day="?????????????????????????????????4";break;
									case "TU1":cust_day="???????????????????????????1";break;
									case "TU2":cust_day="???????????????????????????2";break;
									case "TU3":cust_day="???????????????????????????3";break;
									case "TU4":cust_day="???????????????????????????4";break;
									default:cust_day="";
								}				
									$("#sch_fullname").val(member_fullname);
									$("#sch_card_type").val(remark);
									$("#sch_apply_date").val(apply_date);
									$("#sch_expire_date").val(expire_date);
									$("#sch_birthday").val(birthday);
									$("#sch_opsday").val(cust_day);									
									$("#sch_address").val(address);
									$("#sch_mobile_no").val(m.mobile_no);
									
								}else if(m.link_status=='OFFLINE'){
									var remark=m.remark;
									var percent_discount='0';
									if(m.percent_discount!=undefined){
										percent_discount=parseInt(m.percent_discount);
									}
									var mp_point_sum=m.mp_point_sum;
									var buy_net=m.buy_net;
									var address=$.trim(m.h_address)+" "+
													$.trim(m.h_village_id)+" "+
													$.trim(m.h_village)+" "+
													$.trim(m.h_soi)+" "+
													$.trim(m.h_road)+" "+
													$.trim(m.h_district)+" "+
													$.trim(m.h_amphur)+" "+
													$.trim(m.h_province)+" "+
													$.trim(m.h_postcode);
									$("#sch_fullname").val(member_fullname);
									$("#sch_apply_date").val(apply_date);
									$("#sch_expire_date").val(expire_date);
									$("#sch_birthday").val(birthday);
									$("#sch_opsday").val(m.special_day);
									$("#sch_address").val(address);	
								}
								////-------------------------------
								if(m.status=='1'){
									$("#sch_desc").val(m.card_detail);
									if(m.mem_status=='N'){
										$("#sch_desc").append('\n????????????????????????????????????????????????????????????????????????????????????????????????????????? non active ??????????????????????????????????????????????????????');
									}else if(m.mem_status=='F'){
										$("#sch_desc").append('\n????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ???????????????????????????????????????????????????\n Please check and try again.');
									}else if(m.mem_status=='T'){
										$("#sch_desc").append('\n????????????????????????????????????????????????????????????????????????????????????????????????????????? non active \n ????????????????????????????????????????????????');
									}									
								}
							}						
						});
						
					}
				}				
		);//end json
		
	}//func
	
	function chkMarkMemberUsePriv(){
		var web_promo=$('#csh_web_promo').val();
    	var promo_code=$('#csh_promo_code').val();
    	var promo_year=$('#csh_promo_year').val();
    	var member_no=$('#csh_member_no').val();
    	var customer_id=$('#csh_customer_id').val();
		if(web_promo!='' && promo_code!='' && member_no!='' && customer_id!=''){
			unMarkMemberUsePriv(promo_code,promo_year,member_no,customer_id);
		}
	}//func
	
	function setOnlineMode(online_status){
		var opts_online_status={
				type:'post',
				url:'/sales/cashier/setonlinemode',
				data:{
					online_status:online_status,
					rnd:Math.random()
				},success:function(){}
		};
		$.ajax(opts_online_status);
	}//func
	
	function getOnlineStatus(){
		var sys_online;
		if($("#sys_on").is(':checked')){
			sys_online=1;
		}else{
			sys_online=0;
		}
		return sys_online;
	}//func
	
////////////////////////////////////WR10022014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function paymentBillNormal(){	
			$.ajax({								
				type:'post',
				url:'/sales/cashier/countdiarytemp',
				cache:false,
				data:{
					actions:'',
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');
					if(parseInt(arr_data[0])>0){									
						if(arr_data[1]=="trn_promotion_tmp1"){							
							lastprochk(); //Joke append 14022012
						}else{
							//////////////////////////////////////////////////
							if($('#status_couponpromo').val()=='Y'){
								//??????????????????????????????????????????????????????????????????????????????????????????????????????		
								var start_baht=parseFloat($('#csh_start_baht').val());
			    				var end_baht=parseFloat($('#csh_end_baht').val());
			    				var buy_status=$("#csh_buy_status").val();	
			    				var csh_net=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
			    				     csh_net=parseFloat(csh_net);
			    				//alert(csh_net + " " + buy_status + " " + start_baht);
								if(csh_net > 0 && buy_status=='G' && csh_net < start_baht ){
									jAlert('????????????????????????????????????????????? ' + start_baht + ' ???????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
										$("#btn_cal_promotion").removeAttr("disabled");
										$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
										return false;
									});
								}else{
									paymentBill('00');
								}
							}else if($('#csh_promo_pos').val()=='S'){
									var csh_promo_amt=$('#csh_promo_amt').val().replace(/[^\d\.\-\ ]/g,'');
									csh_promo_amt=parseFloat(csh_promo_amt);
									var csh_sum_amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
									csh_sum_amount=parseFloat(csh_sum_amount);
									if($('#csh_promo_amt_type').val()=='G' && csh_sum_amount < csh_promo_amt){											
										jAlert('????????????????????????????????????????????? ' + $('#csh_promo_amt').val() + ' ???????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
											$("#btn_cal_promotion").removeAttr("disabled");
											$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
											return false;
										});
									}else{
										paymentBill('00');
									}
						    }else{
						    	//*WR17072014
						    	paymentBill('00');
						    }
							//////////////////////////////////////////////////
						}//end if<>trn_promotion_tmp1
					}else{								
							jAlert('Not found Sell ??????Items. Please check and try again.','WARNING!',function(){
								$("#btn_cal_promotion").removeAttr("disabled");
								$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
								return false;
							});
					}
				}//success
		});//end ajax
	}//func
	///////////////////////////////////WR10022014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
	//dom ready
	$(function(){
		//lock & unlock key manual
		if($("#csh_lock_status").val()=='Y'){
			lockManualKey();
		}else{
			unlockManualKey();
		}
		//swap button online offline
		$('#sys_on').attr('checked', true);		
		$(".cb-enable").click(function(){
			if($("#sys_on").is(':checked')) return false;
			var parent = $(this).parents('.switch');
			$('.cb-disable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('.sys_on',parent).attr('checked', true);
			initTblTemp();
			initFormCashier();
			getCshTemp('Y');
			setOnlineMode('1');
		});
		$(".cb-disable").click(function(){
			if(!$("#sys_on").is(':checked')) return false;
			var parent = $(this).parents('.switch');
			$('.cb-enable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('.sys_on',parent).attr('checked', false);	
			chkMarkMemberUsePriv(); //check member used privillage or not?
			initTblTemp();
			initFormCashier();
			getCshTemp('Y');
			setOnlineMode('0');
		});
		//swap button online offline
		
		//disble right click
		//$(document).bind("contextmenu",function(e){
        //    return false;
     	//}); 
	
		initUser();
		initTblTemp();
		initFormCashier();
		//init text field to open bill
		initFieldOpenBill();
		//select type promotion
		$("#status_pro").change(function(){
			var play_main_promotion=$('#csh_play_main_promotion').val();
			var play_last_promotion=$('#csh_play_last_promotion').val();
			var status_no=$('#csh_status_no').val();
			if(play_main_promotion=='N' && play_last_promotion=='N'){
				$("#status_pro option[value='1']").attr('selected','selected');	
			}

			var chk_key_product_readonly=$('#csh_product_id').is('[readonly]');			
			if(!chk_key_product_readonly){ 
				$('#csh_product_id').focus();
			}
		});
		
		//start control hot key		
		shortcut.add("F10",function(e){
			e.stopPropagation();
			e.preventDefault();
			//e.stopImmediatePropagation();			
			if (!e) var e = window.event;			
			var key = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
//			var character = String.fromCharCode(code);
//			character = String.fromCharCode(key);
//			alert('Character was ' + character);
//			if(character=='y' || character=='Y'){
//				return false;
//			}
			if($("#btn_cal_promotion").is("disabled")==false){	
				$("#btn_cal_promotion").trigger("click");
				$("#csh_accordion").accordion({ active:1});
			}else{
				return false;
			}
		},{
			'type':'keydown',
			'propagate':false,
			'disable_in_input':false,
			'target':document,
			'keycode':121
		});
		
		shortcut.add("ESC",function(e){
			//*WR10022014 Revised ??????????????????????????????????????????????????????????????? 4 ???????????????????????? 25096
			var chk_promo_code=$('#csh_promo_code').val();
			if(chk_promo_code!='50BTO1P'){
				return false;
			}
//			if(chk_promo_code=='OM02091113'){
//				return false;
//			}
			 endOfProOther();
		},{
			'type':'keypress',
			'propagate':false,
			'disable_in_input':false,
			'target':document.getElementById('csh_product_id'),
			'keycode':true			
		});
		//end control hot key
		$(".btn-cmd-brows").button().css({width:'90',height:'30'}).addClass('ui-state-active');
		$(".btn-cmd-brows-1").button().css({width:'100',height:'50'});
		$(".btn-cmd-brows-2").button().css({width:'70',height:'52'});
		$(".btn-cmd-cal").button().css({width:'70',height:'50'});
		$("#btn-profile").button().css({width:'75',height:'75'});
		$("#btn_cal_promotion").button().css({width:'95%',height:'80'});
		$("#btn_hot_promotion").button().css({width:'95%',height:'80'});		
		$("#btnBwsMemberBuy").click(function(e){
			e.preventDefault();
			 var member_no=$("#csh_member_no").val();
		        if($.trim(member_no).length==0){
		        	jAlert('Please specify member id.','WARNING!',function(){
		        		$("#csh_member_no").focus();
						return false;
	    			});
			        return false;
			    }
			$("<div id='dlgMemberBuy'></div>").dialog({
				 autoOpen: true,
			     resizable: false,
			     modal: true,
			     width:'50%',
			     height:350,
			     title:"<span class='ui-icon ui-icon-person'></span>Purchase Items",
			     open:function(){
			    	 $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","margin-top":"0","background-color":"#c5bcdc",
	    			    										"font-size":"26px","color":"#000"});
    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
				     $("div#dlgMemberBuy").html('');
				     var objReqProfile=$.ajax({
					    type:'GET',
					    url:"/sales/member/memberbuyprofile",
					    data:{
						    	member_no:member_no
						    },
						 success:function(data){
							 objReqProfile=null;
						    	$("div#dlgMemberBuy").html(data);						    	
						}
					 });
				     setTimeout(function(){
						  if (objReqProfile) objReqProfile.abort();
						},5000);
			     },close:function(){
				     $(this).remove();
				 }	
			});
		});

		$("#btnBwsMemberScoreOfDay").click(function(e){
			e.preventDefault();
			 var member_no=$("#csh_member_no").val();
	         if($.trim(member_no).length==0){
	        	jAlert('Please specify member id.','WARNING!',function(){
	        		$("#csh_member_no").focus();
					return false;
    			});
		        return false;
		     }
			$("<div id='dlgMemberPoint'></div>").dialog({
				 autoOpen: true,
			     resizable: false,
			     modal: true,
			     width: 600,
			     title:"<span class='ui-icon ui-icon-person'></span>????????????????????????????????????????????????????????????",
			     open:function(){
			    	 $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","margin-top":"0","background-color":"#c5bcdc",
		    			    										"font-size":"26px","color":"#000"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
				     $("div#dlgMemberPoint").html('');
				     $.ajax({
					    type:'GET',
					    url:"/sales/member/memberpointtoday",
					    data:{
						    	member_no:member_no
						    },
						 success:function(data){
						    	$("div#dlgMemberPoint").html(data);
								 }
					 });
			     },close:function(){
				     $(this).remove();
				 }
							
			});
		});	
//		$("#csh_quantity").numericFilter();		
//		$("#csh_product_id").numericFilter();
		$("#csh_quantity").keydown(function(event) {
	        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 	       
	            (event.keyCode == 65 && event.ctrlKey === true) || 	          
	            (event.keyCode >= 35 && event.keyCode <= 39)) {
	                 return;
	        }
	        else {	           
	            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
	                event.preventDefault(); 
	            }   
	        }
	    });
		$("#csh_product_id").keydown(function(event) {	     
	        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 	         
	            (event.keyCode == 65 && event.ctrlKey === true) || 	 
	            (event.keyCode >= 35 && event.keyCode <= 39)) {
	                 return;
	        }
	        else {	           
	            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
	                event.preventDefault(); 
	            }   
	        }
	    });
		$("#btn_cal_promotion").click(function(e){
			//---------- cal promotion -----------
			e.preventDefault();
			var chk_member_no=$('#csh_member_no').val();
			chk_member_no=$.trim(chk_member_no);
			var chk_percent_discount=$('#csh_percent_discount').html();			
			chk_percent_discount=$.trim(chk_percent_discount);			
			if(chk_percent_discount.length>0 && chk_member_no.length==0){
				jAlert('????????????????????????????????????????????? ????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){					
					return false;
    			});
				return false;
			}			
			$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access').addClass('ui-state-disabled');
			$("#btn_cal_promotion").attr("disabled",true);
			if(jQuery.trim($("#csh_status_no").val())=='01'){
				//-----------------??????????????????????????????????????????????????????????????????????????????--------------------
				var opts={
						type:'post',
						url:'/sales/cashier/countdiarytemp',
						cache:false,
						data:{
							actions:'cashier',
							rnd:Math.random()
						},
						success:function(data){
							var arr_data=data.split('#');
							if(parseInt(arr_data[0])>0){						
								paymentBill('01');
							}else{
								jAlert('Not found Sell ??????Items. Please check and try again.','WARNING!',function(){
									$("#btn_cal_promotion").removeAttr("disabled");
									$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
									return false;
				    			});
						    	return false;
							}
						}
					};
				$.ajax(opts);
			}else if(jQuery.trim($("#csh_status_no").val())=='02'){
				//-----------------?????????????????????????????????????????????--------------------
				var play_main_pro=jQuery.trim($("#csh_play_main_promotion").val());
				var play_last_pro=jQuery.trim($("#csh_play_last_promotion").val());
				var csh_net=$("#csh_net").val().replace(/[^\d\.\-\ ]/g,'');			
				if(parseInt(csh_net)>0){					
					//*WR29012015 for ?????????????????? First Purchase OPPN300
					var chk_newmem=$('#info_apply_promo').html();
					chk_newmem=$.trim(chk_newmem);
					
					//*WR25022016
					if($('#csh_application_type').val()=="NEW"){
						//*WR19122016 for support bill 02 2017		
						var cmpDate_Start ='01/01/2017';        
						var cmpDate_Today=$('#csh_doc_date').html();
						
				        var arr_fromdate = cmpDate_Start.split('/');
				        cmpDate_Start = new Date();
				        cmpDate_Start.setFullYear(arr_fromdate[2],arr_fromdate[1]-1,arr_fromdate[0]);
				        
				        var arr_todate = cmpDate_Today.split('/');
				        cmpDate_Today = new Date();
				        cmpDate_Today.setFullYear(arr_todate[2],arr_todate[1]-1,arr_todate[0]);
				        //alert("cmpDate_Start=>" + cmpDate_Start + "\n" + "cmpDate_Today=>" + cmpDate_Today);        
				        if(cmpDate_Today >= cmpDate_Start) 
				        {
				        	var promo_code='OX02321116';//for bill 02 2017
				        	$.ajax({
								type:'post',
								url:'/sales/member/chkamtprobalance',
								data:{
									promo_code:promo_code,											
									product_id:'',
									quantity:'',
									rnd:Math.random()
								},success:function(data){
									getPmtTemp('N');
									setTimeout(function(){																			
										var promo_code=$("#csh_promo_code").val();
										var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
								 		net_amt=parseFloat(net_amt);
								 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
								 		amount=parseFloat(amount);
								 		//*WR26082014 ???????????????????????????????????????????????? method selCoPromotion
							 			var promo_code="FSTPCH02";
							 			selCoPromotion(promo_code,net_amt,amount);
									},1000);
								}
							});
				            
				        }else{
				        	setTimeout(function(){																			
								var promo_code=$("#csh_promo_code").val();
								var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
						 		net_amt=parseFloat(net_amt);
						 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
						 		amount=parseFloat(amount);
						 		//*WR26082014 ???????????????????????????????????????????????? method selCoPromotion
					 			var promo_code="FSTPCH02";
					 			selCoPromotion(promo_code,net_amt,amount);
							},1000);				                    	
				        }  
					}else{
						//*WR03092015 for bill 02 renew receive coupon lucky draw
						setTimeout(function(){																			
							var promo_code="FSTPCH02_RENEW";
							var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
					 		net_amt=parseFloat(net_amt);
					 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
					 		amount=parseFloat(amount);
				 			selCoPromotion(promo_code,net_amt,amount);
						},1000);	
					}
				}else{
					jAlert('Not found Sell ??????Items. Please check and try again.','WARNING!',function(){
						$("#btn_cal_promotion").removeAttr("disabled");
						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
						return false;
	    			});
				}
			}else if(jQuery.trim($("#csh_status_no").val())=='03'){
				//-----------------?????????????????????????????????????????????--------------------
				var ecoupon_percent_discount=$("#csh_ecp_percent_discount").val();
				var ecoupon_amount_balance=$("#csh_ecp_amount_balance").val();				
				var play_main_pro=jQuery.trim($("#csh_play_main_promotion").val());
				var play_last_pro=jQuery.trim($("#csh_play_last_promotion").val());
				var csh_net=$("#csh_net").val();
				ecoupon_percent_discount=parseInt(ecoupon_percent_discount);
				ecoupon_percent_discount=ecoupon_percent_discount/100;
				ecoupon_amount_balance=parseFloat(ecoupon_amount_balance);
				csh_net=parseFloat(csh_net.replace(/[^\d\.\-\ ]/g,''));
				var ecp_amount_bal=0.00;//used
				if(ecoupon_amount_balance>csh_net){
					//??????????????????????????????????????????????????????????????????					
					ecp_amount_bal=csh_net;
				}else{
					//?????????????????????????????????????????????????????????????????????					
					ecp_amount_bal=ecoupon_amount_balance;
				}
				
				if(parseInt(csh_net)>0){
					//check ecoupon amount
					var dlgOpts_dlgEcpEmployee = {
							autoOpen:true,
							modal:true,
							position:'center',
							width:400,
							height:200,
							title:"<span class='ui-icon ui-icon-person'></span>Ecoupon",
        					closeOnEscape:true,
        					open: function(){ 
        						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
        						$(this).dialog('widget')
        				            .find('.ui-dialog-titlebar')
        				            .removeClass('ui-corner-all')
        				            .addClass('ui-corner-top');
								    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#c5bcdc",
									    																			"font-size":"24px","color":"#0000FF",
									    																			"padding":"5 0 0 0"});						   				
					   				//$(this).parent().append('<div id="dlgfooter" style="position:absolute;bottom:0;float:left;width:100%;background-color:#c9c9c9;padding:5px;"></div>'); 
					   				$('#ecp_amount').val('');
			   						$('#ecp_amount').val(ecp_amount_bal).focus();
			   						$('#ecp_amount').keypress(function(evt){
			   							var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					   			        if(key == 13){
					   			        	evt.stopImmediatePropagation();
						   			        evt.preventDefault();						   			       
						   			        var chk_ecp_amount_bal=$("#ecp_amount").val();
						   			        ecoupon_amount_balance=$("#csh_ecp_amount_balance").val();
						   			        //alert(parseFloat(chk_ecp_amount_bal) + ">" + ecoupon_amount_balance);
						   			        if(parseFloat(chk_ecp_amount_bal)>parseFloat(ecoupon_amount_balance)){
						   			        	jAlert('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? Please check and try again.','WARNING!',function(){
						   			        		$('#ecp_amount').val(ecp_amount_bal).focus();
						   							return false;
						   		    			});
							   			        return false;
							   			    }
							   			     $('#dlgEcpEmployee').dialog('close');
							   			     //cal new discount 50%
							   			     //alert(chk_ecp_amount_bal);
							   			     $.ajax({
								   			     type:'post',
								   			     url:'/sales/member/calecoupondiscount',
								   			     data:{
								   			    	ecoupon_percent_discount:ecoupon_percent_discount,
								   			    	ecp_amount_bal:chk_ecp_amount_bal,
								   			    	rnd:Math.random()							   			    	
								   			     },
								   			     success:function(data){									   			    	
									   			     getCshTemp('P');
										   			 paymentBill('03');
									   			 }
								   			 });							   			    
						   			        return false;	
					   			        }
				   					});
        					},				
        					close: function(evt,ui) {
        						$('#dlgEcpEmployee').dialog('destroy');
        						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
        							$("#btn_cal_promotion").removeAttr("disabled");
    								$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
        						}
        						if(evt.originalEvent && $(evt.originalEvent.which==27)){
        							$("#btn_cal_promotion").removeAttr("disabled");
    								$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
        						}        						
        					 }
        				};		
        				$('#dlgEcpEmployee').dialog('destroy');
        				$('#dlgEcpEmployee').dialog(dlgOpts_dlgEcpEmployee);			
        				$('#dlgEcpEmployee').dialog('open');				
				}else{
					jAlert('Not found Sell ??????Items. Please check and try again.','WARNING!',function(){
						$("#btn_cal_promotion").removeAttr("disabled");
						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
						return false;
	    			});
				}
			}else if(jQuery.trim($("#csh_status_no").val())=='04'){
				//-----------------??????????????????????????????????????????--------------------
				var $play_main_pro=$("#csh_play_main_promotion");
					$play_main_pro=jQuery.trim($play_main_pro.val());
				var $play_last_pro=$("#csh_play_last_promotion");
					$play_last_pro=jQuery.trim($play_last_pro.val());
				var $csh_net=$("#csh_net");
					$csh_net=jQuery.trim($csh_net.val());
					$csh_net=$csh_net.replace(/[^\d\.\-\ ]/g,'');
					$csh_net=parseFloat($csh_net);
				var $promo_tp=$("#csh_promo_tp");	
				
				if($promo_tp.val()=='N'){
					var $start_baht=$("#csh_start_baht");
						$start_baht=jQuery.trim($start_baht.val());
						$start_baht=parseFloat($start_baht);
					var $end_baht=$("#csh_end_baht");
						$end_baht=jQuery.trim($end_baht.val());
					if($csh_net<$start_baht){
						jAlert('???????????????????????????????????????????????????????????? '+$start_baht+' Please check and try again.','WARNING!',function(){
							$("#btn_cal_promotion").removeAttr("disabled");
							$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
							$("#csh_product_id").focus();
		    			});
				    	return false;
					}else{
						if(chkDiaryTemp()=='Y'){
							if($play_main_pro=='N' && $play_last_pro=='N'){
								
								//cal % discount									
								$.ajax({
										type:'post',
										url:'/sales/member/promodiscount',
										data:{
												promo_code:$("#csh_promo_code").val(),
												promo_id:$("#csh_promo_id").val(),
												promo_tp:$promo_tp.val(),
												rnd:Math.random()
										},
										success:function(data){
											if(data=='3'){
												//*WR01092014 for support coupon we love ops 2014 coproomtion
												setTimeout(function(){																			
													var promo_code=$("#csh_promo_code").val();
													var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
											 		net_amt=parseFloat(net_amt);
											 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
											 		amount=parseFloat(amount);
											 		if(promo_code=='BURNPOINT1' && net_amt>=500){
											 			//last pro check free product method ???????????????
											 			selCoPromotion(promo_code,net_amt,amount);
											 		}else{
											 			paymentBill('04');
											 		}
												},1500);
												
											}else if(data=='E9'){
												jAlert('!!! ??????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????? !!!\n????????????????????????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????','WARNING!',function(){	
													rePromoPointDiscount();
													$("#btn_cal_promotion").removeAttr("disabled");
				    								$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
													return false;
								    			});
										    	return false;
											}
											getCshTemp('N');
										}
									});
							}
						}
					}
				}else if($promo_tp.val()=='S'){
					//find cal_amout
					var dlgOpts_percentrange = {
        					autoOpen: false,
        					width:'70%',		
        					height:420,	
        					modal:true,
        					resizable:true,
        					position:['center','center'],
        					title:"<span class='ui-icon ui-icon-cart'></span>?????????????????????????????????",
        					closeOnEscape:true,
        					open: function(){ 
									$("#dlgPromoPercentRange").css({'height':'400px'});
        							$("#dlgPromoPercentRange").html("");
        							$.ajax({
            							type:'post',
            							url:'/sales/member/percentrange',
            							data:{
            								promo_code:$('#csh_promo_code').val(),
    										promo_id:$('#csh_promo_id').val(),
    										net_amt:$csh_net,
    										rnd:Math.random()
            							},
            							success:function(data){
            								$("#dlgPromoPercentRange").html(data);
            								$('.tableNavPercentRange ul li').not('.nokeyboard').navigate({
										        wrap:true
										    }).click(function(e){
										    	////////////////*wr 20120901
										    	var point_total=$('#csh_point_total').html();
										    	point_total=$.trim(point_total);
										    	point_total=parseInt(point_total);
										    	////////////////*wr 20120901										    	
											    var arr_promo=$(this).attr('idpromo').split('^');
											    var percent=arr_promo[0];
											    var point=arr_promo[1];	
											    ////////////////*wr 20120901
											    point=parseInt(point);
											    if(point>point_total){
											    	jAlert('?????????????????????????????????????????? Please check and try again.','WARNING!',function(){													
														return false;
									    			});
											    	return false;
											    }
											    ////////////////*wr 20120901
											    
											    var discount=arr_promo[2];
											    $("#csh_point_used").val(point);
											    if(chkDiaryTemp()=='Y'){
													//if($play_main_pro=='N' && $play_last_pro=='N'){
											    	//*WR20120904 modify for bill 04 promo_code=BURNPOINT2
											    	if($play_last_pro=='N'){
														//cal % discount									
														$.ajax({
																type:'post',
																url:'/sales/member/promodiscount',
																data:{
																		promo_code:$("#csh_promo_code").val(),
																		promo_id:$("#csh_promo_id").val(),
																		promo_tp:$promo_tp.val(),
																		percent:percent,
																		point:point,
																		discount:discount,
																		rnd:Math.random()
																},
																success:function(data){
																	if(data=='3'){
																		//*WR30012014 OX24171113=The first purchase 1 ????????????????????????????????? 5 ????????? ?????????????????????????????????????????? 30-50%
																		if($.trim($("#csh_promo_code").val())=='BURNPOINT2' || $.trim($("#csh_promo_code").val())=='OX24171113'){
																			getPmtTemp('N');
																		}else{
																			getCshTemp('N');
																		}
																		//*WR01092014 for support coupon we love ops 2014 coproomtion
																		setTimeout(function(){																			
																			var promo_code=$("#csh_promo_code").val();
																			var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
																	 		net_amt=parseFloat(net_amt);
																	 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
																	 		amount=parseFloat(amount);
																	 		if(promo_code=='BURNPOINT2' && net_amt>=500){
																	 			//last pro check free product method ???????????????
																	 			selCoPromotion(promo_code,net_amt,amount);
																	 		}else{
																	 			paymentBill('04');
																	 		}																			
																		},1500);
																		//paymentBill('04');																		
																	}
																}
															});// end ajax
													}
												}
											    $('#dlgPromoPercentRange').dialog('close');												
										    });
										    
                						}
            						});//ajax
        							
        					},				
        					close: function(evt,ui) {
        						$('.tableNavPercentRange ul').navigate('destroy');
        						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
        							$("#btn_cal_promotion").removeAttr("disabled");
    								$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
        						}
        						if(evt.originalEvent && $(evt.originalEvent.which==27)){
        							$("#btn_cal_promotion").removeAttr("disabled");
    								$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
        						}
        						
        					 }
        				};		
        				$('#dlgPromoPercentRange').dialog('destroy');
        				$('#dlgPromoPercentRange').dialog(dlgOpts_percentrange);			
        				$('#dlgPromoPercentRange').dialog('open');
					return false;
				}				
			}else if(jQuery.trim($("#csh_status_no").val())=='05'){
				//-----------------??????????????????????????????????????????????????????--------------------
				var opts={
						type:'post',
						url:'/sales/cashier/countdiarytemp',
						cache:false,
						data:{
							actions:'cashier',
							rnd:Math.random()
						},
						success:function(data){
							//alert(data);
							var arr_data=data.split('#');
							if(parseInt(arr_data[0])>0){
							//if(parseInt(data)>0){
								paymentBill('05');
							}else{
								jAlert('Not found Sell ??????Items. Please check and try again.','WARNING!',function(){
									$("#btn_cal_promotion").removeAttr("disabled");
									$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
									return false;
				    			});
							}							
						}
					};
				$.ajax(opts);				
			}else{
				
				//////*WR29072014 START NEW BIRTH				
				var promo_code=$('#csh_promo_code').val();				
				var net_amt=$('#csh_net').val();					
				net_amt=net_amt.replace(/[^\d\.\-\ ]/g,'');	
				var amount=$('#csh_sum_amount').val();
				amount=amount.replace(/[^\d\.\-\ ]/g,'');	
				var promo_type=$('#csh_promo_tp').val();
				
				//check bill 01 for 9900730
				$.ajax({
					type:'post',
					url:'/sales/member/formregister',
					cache:false,
					data:{
						rnd:Math.random()
					},
					success:function(data){	
						
						if(data=="Y"){
							//show form
							$('<div id="dlgFormRegister">' + 
									'<span style="color:#333333;"> MEMBER CODE :  <span>' + 
									'<input type="text" size="15" id="reg_member_id"/><br/>' + 
									'<span style="color:#333333;"> MOBILE NO :  <span>' + 
									'<input type="text" size="15" id="reg_mobile_no"/><br/>' +
									'</div>').dialog({
						           autoOpen:true,
								   width:'350',
								   height:'250',	
								   modal:true,
								   resizable:false,
								   closeOnEscape:false,		
						           title: "MEMBER REGISTER",
						           position:"center",
						           open:function(){
										$(this).dialog('widget')
								            .find('.ui-dialog-titlebar')
								            .removeClass('ui-corner-all')
								            .addClass('ui-corner-top');		
										//button style	
						  			    $(this).dialog("widget").find(".ui-btndlgpos")
						                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
						  			    
						  			    setTimeout(function(){
						  			    	$("#reg_member_id").focus();
						  			    },800);
						  			    
							  			  $('#reg_member_id').keypress(function(evt){																    	
										    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							   			        if(key == 13){	
									    			evt.preventDefault();
									    			evt.stopPropagation();
									    			$("#reg_mobile_no").focus();									    			
									    			return false;
							   			        }
										  });
								  		  $('#reg_mobile_no').keypress(function(evt){																    	
										    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							   			        if(key == 13){	
									    			evt.preventDefault();
									    			evt.stopPropagation();
									    			$('#btnChkMemberExist').trigger('click');
									    			return false;
							   			        }
										  });
						           },
				    			buttons: [ 
					    					{ 
						    	                text: "OK",
						    	                id:"btnChkMemberExist",
						    	                class: 'ui-btndlgpos', 
						    	                click: function(evt){ 
						    	                	evt.preventDefault();
											 		evt.stopPropagation();
											 		var member_no=$('#reg_member_id').val();
											 		var mobile_no= $('#reg_mobile_no').val();
											 		member_no=$.trim(member_no);
											 		mobile_no=$.trim(mobile_no);
											 		//alert(member_no.length);
											 		
											 		if(member_no.length!=13){
											 			jAlert('PLEASE ENTER MEMBER CODE 13 DIGIT', 'WARNING',function(){
											 				$('#reg_member_id').focus();
									    					return false;
									    				});	
											 			return false;
											 		}else if(member_no.substr(0,6)!="112018" && member_no.substr(0,1)!="x"){
											 			jAlert('INVALID FORMAT MEMBER CODE.', 'WARNING',function(){
											 				$('#reg_member_id').focus();
									    					return false;
									    				});	
											 			return false;
											 		}else if(mobile_no.length<9 && mobile_no.length>10){
									    				jAlert('PLEASE ENTER MOBILE NO 9 OR 10 DIGIT', 'WARNING',function(){
									    					$('#reg_mobile_no').focus();
									    					return false;
									    				});	
									    				return false;
										    		}else if(validatePhone(mobile_no)===false){
										    			jAlert('INVALID FORMAT MOBILE NO.', 'WARNING',function(){
									    					$('#reg_mobile_no').focus();
									    					return false;
									    				});	
									    				return false;
										    		}else {
										    			
										    			//check api joke	
											    		$.ajax({
											    			type:'post',
											    			url:'/sales/member/checkismember',
											    			data:{
											    				member_no:member_no,
											    				mobile_no:mobile_no,
											    				rnd:Math.random()
											    			},
											    			success:function(data){
											    				//alert(data);
											    				var obj=$.parseJSON(data);		
											    				//alert(obj[0].status);
											    				if(obj[0].status=="N"){												    					
															    	$("#csh_status_no").val("01");
															    	$("#csh_application_id").val("OPID300");
															    	$('#csh_application_type').val("NEW"); 
															    	$('#csh_card_type').val("MBC");
															    	$('#csh_member_no').val(member_no);
															    	$('#csh_mobile_no').val(mobile_no);
															    	$('#dlgFormRegister').dialog('close');
															    	paymentBill('00');
															    	
											    				}else{
											    					jAlert(obj[0].msg, 'WARNING!',function(){	            	
											    		            	return false;
											    			        });
											    				}
											    				
											    			}
											    		});
										    			
										    		}//end if
										    	
										 																				 		
					    	                }
					    	            }
							        ]			
	    							,close:function(evt){
	    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
											evt.stopPropagation();
				    						evt.preventDefault();
				    						//initTblTemp();
				    						//initFormCashier();
											//initFieldOpenBill();
											initFieldPromt();
											$("#btn_cal_promotion").removeAttr("disabled");
											$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
									 	
										}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
											evt.stopPropagation();
				    						evt.preventDefault();  						
										}
	    								$('#dlgFormRegister').dialog("destroy").remove();	
						           }
							});
							//show form
							
						}else{
							/////////////////// START ORG /////////////////
							
							
							
							var play_last_promotion=$('#csh_play_last_promotion').val();
							if(promo_type=='MCOUPON'){
								if(play_last_promotion!='Y'){
									paymentBill('00');
									return false;
								}
							}else if(promo_type=='COUPON'){
								lastdelpro();//joke
								//*WR19082015 Lucky Draw
								setTimeout(function(){																			
									var promo_code=$("#csh_promo_code").val();
									var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
							 		net_amt=parseFloat(net_amt);
							 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
							 		amount=parseFloat(amount);	
						 			selCoPromotion(promo_code,net_amt,amount);
								},1000);
								return false;
							}else if(promo_type=='NEWBIRTH'){
									setTimeout(function(){																			
										var promo_code=$("#csh_promo_code").val();
										var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
								 		net_amt=parseFloat(net_amt);
								 		var amount=$('#csh_sum_amount').val().replace(/[^\d\.\-\ ]/g,'');
								 		amount=parseFloat(amount);	
							 			selCoPromotion(promo_code,net_amt,amount);
									},1000);
							}else{
								paymentBillNormal();
							}
							
							/////////////////// END   ORG /////////////////
						}
										
					}
				});
				
				
			}
			return false;
			//---------- cal promotion -----------			
		});

		$("#btn_hot_promotion").click(function(e){
			//---------- hot promotion Joke -----------
			e.preventDefault();
			promotionDetail();
			//hotpro();
			hotprosearch();
			return false;
			//---------- hot promotion Joke -----------			
		});

		//init flexigrid		
		//var gHeight=320,wcol=250,gWidth = parseInt((screen.width*50)/100)-430;
		var gHeight=320,wcol=250,gWidth = parseInt((screen.width*50)/100)-530;
		if ((screen.width>=1280) && (screen.height>=1024)){			
			gHeight=(screen.height-(screen.height*(40/100)))-60;
			wcol=410;
		}else if(screen.width>1280 && screen.height<1024){
			//for width screen 19 "
			gHeight=(screen.height-(screen.height*(50/100)));
		}else{
			//other screen
			gHeight=(screen.height-(screen.height*(50/100)));
		}
		
		var r_bgcolor="#ffffff";
		var r_promo_code="";
		var c_promo_code="";
		
		$("#tbl_cashier").flexigrid(
				{
					url:'/sales/cashier/cashiertemp',
					dataType: 'json',
					colModel : [
						{display: '#', name : 'id', width :20, sortable : true, align: 'center'},						
						{display: 'STATUS', name : 'promo_st', width : 80, sortable : true, align: 'center'},
						{display: 'PROMO CODE', name : 'promo_code', width : 100, sortable : true, align: 'center'},
						{display: 'PRODUCT ID', name : 'product_id', width : 80, sortable : true, align: 'center'},
						{display: 'DESCRIPTION', name : 'product_name',width :gWidth, sortable : false, align:'left'},
						{display: 'QUANTITY', name : 'quantity', width : 70, sortable : false, align: 'center'},
						{display: 'PRICE', name : 'price', width :80, sortable : false, align: 'center'},
						{display: 'AMOUNT', name : 'amount', width :80, sortable : false, align: 'center'},
						{display: 'DISCOUNT', name : 'discount', width :80, sortable : false, align: 'center'},
						{display: 'TOTAL', name : 'amount', width :80, sortable : false, align: 'center'}
						],
					sortname: "id",
					sortorder: "asc",
					action:'gettmp',
					usepager:true,
					singleSelect: false,
					nowrap: false,
					title:'',
					useRp:true,
					rp:14,
					onSuccess:function(data){
						var grid = $('#tbl_cashier').flexigrid();
				        grid.find('tr').each(function() {
					        c_promo_code=$("td div", $(this)).eq(2).text();	
					        if(c_promo_code!=r_promo_code){
					        	r_promo_code=c_promo_code;
					        	if(r_bgcolor!="#FFFFFF"){
					        		r_bgcolor="#FFFFFF";
					        	}else{
					        		r_bgcolor="#ffcc99";
					        	}
					        }
					        $(this).css("background-color",r_bgcolor);
				        }); 
						/*
						$("#tbl_cashier").find('tr').each(function() {
							  if ($(this).find(".checkMe").val() < 0)
							    $(this).css("background-color", "#ffffff");
							  else
							    $(this).css("background-color", "#ccffff");
						});
						*/						
					},
					onError:function(data){
						alert("ERROR : "+data);
					},
					showTableToggleBtn:true,
					striped:false,
					height:gHeight
				}
		);//end flexigrid	
		
		//*WR23062014 button Coupon/SMS
		$('#btnCoupon').button({
	        icons: { primary: "btnCoupon"}
	    }).click(function(e){
	    	e.preventDefault();
	    	
	    	var status_no=$("#csh_status_no").val();
	    	if(status_no!='00'){
			 	jAlert('?????????????????????????????????????????????????????????????????? ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', '????????????????????????????????????????????????',function(){	            	
	            	return false;
		        });
			 	return false;
			}
	    	
	    	var onsales=onSales();
		    if(onsales>0){
			 	jAlert('?????????????????????????????????????????????????????????????????? ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', '????????????????????????????????????????????????',function(){	            	
	            	return false;
		        });
			 	return false;
			}
	    	//////////////////////////////////////////xx////////////////////////////////////////
	    	$('<div id="dlgProCoupon"></div>').dialog({
			           autoOpen:true,
					   width:'70%',
					   height:'350',	
					   modal:true,
					   resizable:true,
					   closeOnEscape:false,		
			           title: "COUPON / SMS",	
			           position:"center",
			            open:function(){	
			            	$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
						    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
							    																			"font-size":"26px","color":"#0000FF",
							    																			"padding":"0 0 0 0"});	
		    			    ///////////////////////////////////START////////////////////////////////
						    //*WR17082016 for support lucky draw promotion						    
							var mem_card_info=$('#id_smspromo').val();
		    			    $.ajax({
		    			    	type:'post',
		    			    	url:'/sales/member/couponpro',
		    			    	cache:false,
		    			    	data:{
		    			    		mem_card_info:mem_card_info,
		    			    		rnd:Math.random()
		    			    	},success:function(data){
		    			    		$('#dlgProCoupon').empty().html(data);	
		    			    		 $('.tableNavCouponPro ul li').not('.nokeyboard').navigate({
									        wrap:true
									    }).click(function(evt){		
										    evt.stopPropagation();
										    evt.preventDefault();										    
										    var sel_promo=$.parseJSON($(this).attr('idpromo'));	
										    
										  //*WR27102014
										    var form_key_input=sel_promo.key_input; 
										    var member_no=$('#csh_member_no').val();
										    var member_type=$('#csh_member_type').html();
										    if(sel_promo.member_tp == 'Y' && member_no.length<1){
										    	jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????', '???????????????????????????',function(){
										    		$('#dlgProCoupon').dialog('close');
										    		initTblTemp();
													initFormCashier();
													initFieldOpenBill();
							    					return false;
							    				});	
							    				return false;
						    				}else if(member_no.length<1 && member_type!=='WALK IN'){
						    					jAlert('?????????????????? WALK IN ????????????????????????????????? ENTER ??????????????????????????????????????????????????? \n?????????????????????????????????????????????????????????????????????????????? WALK IN ???????????????????????????????????????????????????', '???????????????????????????',function(){
										    		$('#dlgProCoupon').dialog('close');
										    		initTblTemp();
													initFormCashier();
													initFieldOpenBill();
							    					return false;
							    				});	
							    				return false;
						    				}
										    //alert(sel_promo.promo_code + " " + sel_promo.play_main_promotion + " " + sel_promo.play_last_promotion);
										    ///////////////////////////////////////\\\/////////////////////////////////////////////
										    $('#csh_promo_tp').val(sel_promo.promo_tp);//22072014
										    $('#csh_promo_code').val(sel_promo.promo_code);
										    $('#csh_play_main_promotion').val(sel_promo.play_main_promotion);
						    				$('#csh_play_last_promotion').val(sel_promo.play_last_promotion);
						    				$('#csh_get_promotion').val(sel_promo.get_promotion);
						    				$('#csh_start_baht').val(sel_promo.start_baht);
						    				$('#csh_end_baht').val(sel_promo.end_baht);
						    				$('#csh_discount_member').val(sel_promo.member_discount);						    				
						    				$('#csh_get_point').val(sel_promo.get_point);	
						    				$("#csh_buy_type").val(sel_promo.buy_type);
						    				$("#csh_buy_status").val(sel_promo.buy_status);
						    				$('#other_promotion_title').html(sel_promo.promo_des);//*WR22072014						    				
						    				$('#csh_web_promo').val('N');
						    				$("#csh_xpoint").val('1');	
						    				$('#csh_gap_promotion').val('N');
						    				$('#csh_trn_diary2_sl').val('Y');
						    				//*WR16102014
						    				if(sel_promo.type_discount=='PERCENT'){
						    					var m_discount=parseInt(sel_promo.discount);
							    				$('#csh_percent_discount').html(m_discount);
						    				}						    				

						    				if(sel_promo.promo_tp !='LINE' && sel_promo.member_discount != 'Y'){
						    					$('#csh_percent_discount').html('');
						    				}
							    			$('#status_pro').trigger('change');
							    			
							    			var member_no=$('#csh_member_no').val();
						    				var otp_status="";
						    				if(sel_promo.key_input=='OTP'){
						    					otp_status='Y';
						    				}
						    				
						    				//*WR02072015
							    			 var id_card=$('#csh_id_card').val();
					    					 var mobile_no=$('#csh_mobile_no').val();
						    				 var prefix_member_no_idcard=$('#csh_member_no').val();
											 prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
											 var chk_mem_idcard='';
											 if(prefix_member_no_idcard=='ID'){
												 chk_mem_idcard="idcard";
											 }
											
							    			if(sel_promo.promo_tp=='LINE' || sel_promo.promo_tp=='MCOUPON'){
							    				callBackToDo2(sel_promo.promo_code,'Y','','','','');		    				
												return false;
											}
							    									    			
							    			//*WR22072014////////////////////////////////////////////////////////// 
						    				if(sel_promo.promo_tp=='COUPON' && sel_promo.type_discount == 'PERCENT'){
						    					
						    					//*WR18082015 Lucky Draw
								    			if($('#csh_play_last_promotion').val()=='Y'){
								    				$('#csh_trn_diary2_sl').val('N');
								    				$('#csh_gap_promotion').val('Y');
								    			}else{
								    				$('#csh_trn_diary2_sl').val('Y');	
								    				$('#csh_gap_promotion').val('N');
								    			}
						    					$('#dlgProCoupon').dialog('close');	
							    				 //check coupon discount for match pro ?????????????????? coupon percent
							    				$('<div id="dlgChkCouponDiscount"><input type="text" size="15" id="coupon_code2" class="keybarcode"/></div>').dialog({
												           autoOpen:true,
														   width:'350',
														   height:'180',	
														   modal:true,
														   resizable:false,
														   closeOnEscape:false,		
												           title: "Verify Coupon  " + parseInt(sel_promo.discount) + "%",
												           position:"center",
												           open:function(){
																$(this).dialog('widget')
														            .find('.ui-dialog-titlebar')
														            .removeClass('ui-corner-all')
														            .addClass('ui-corner-top');		
																//button style	
												  			    $(this).dialog("widget").find(".ui-btndlgpos")
												                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
												  			    //*** check lock unlock ***
																if($("#csh_lock_status").val()=='Y'){
																	lockManualKey();
																}else{
																	unlockManualKey();
																}
																//*** check lock unlock ***
													  			  $('#coupon_code2').keypress(function(evt){																    	
																    	var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
													   			        if(key == 13){	
															    			evt.preventDefault();
															    			evt.stopPropagation();
															    			$('#btnChkCouponDiscount').trigger('click');
															    			return false;
													   			        }
																    });
												           },
										    				buttons: [ 
														    	            { 
														    	                text: "OK",
														    	                id:"btnChkCouponDiscount",
														    	                class: 'ui-btndlgpos', 
														    	                click: function(evt){ 
														    	                	evt.preventDefault();
																			 		evt.stopPropagation();
																			 		var member_no=$('#csh_member_no').val();
																			 		var coupon_code= $('#coupon_code2').val();
																		 			 coupon_code=$.trim(coupon_code);																		 			
																		    		if(coupon_code.length != 10){
																	    				jAlert('Please enter the coupon 10 digit code.', 'WARNING!',function(){
																	    					$('#coupon_code2').val('').focus();
																	    					return false;
																	    				});	
																	    				return false;
																		    		}
																		    		
//																		    		
																		    																				    		
																		    		//check coupon is used
																		    		$.ajax({
																		    			type:'post',
																		    			url:'/sales/member/chkcouponisused',
																		    			data:{
																		    				member_no:member_no,
																		    				coupon_code:coupon_code,
																		    				rnd:Math.random()
																		    			},success:function(data){
																		    				var arr_data=data.split('#');
																		    				if(arr_data[0]=='Y'){
																		    					//is used
																		    					jAlert(arr_data[1], 'WARINING!',function(){
																		    						initTblTemp();
																									initFormCashier();
																									initFieldOpenBill();
																									$('#coupon_code2').focus();
																			    					return false;
																			    				});	
																		    					return false;
																		    				}else{
																		    					// is available
																		    					$("#csh_coupon_code").val(coupon_code);																		    					
																		    					//check coupon discount for match pro
																		    					var coupon_percent_discount=parseInt(sel_promo.discount);
																		    					$('#csh_percent_discount').html(coupon_percent_discount);
																		    					$('#dlgChkCouponDiscount').dialog('close');
																		    					callBackToDo2(sel_promo.promo_code,'Y','','',coupon_code,'');		    				
																								return false;
																		    				}//end if
																		    			}
																		    		});
																		 														 		
																		 		//$('#dlgChkCouponDiscount').dialog('close');																		 		
													    	                }
													    	            }
													    	  ]			
							    							,close:function(evt){
							    								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
																	evt.stopPropagation();
										    						evt.preventDefault();
										    						initTblTemp()
																	initFormCashier();
																	initFieldOpenBill();
																}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
																	evt.stopPropagation();
										    						evt.preventDefault();  						
																}
							    								$('#dlgChkCouponDiscount').dialog("destroy").remove();	
												           }
							    				});
						    					return false;
						    		}//end if sel_promo.type_discount = PERCENT
						    		//*WR22072014//////////////////////////////////////////////////////////
						    				
						    		//Show Dialog Check Mobile Application Coupon
							    	$('<div id="dlgChkProCoupon"></div>').dialog({
										           autoOpen:true,
												   width:'450',
												   height:'auto',	
												   modal:true,
												   resizable:true,
												   closeOnEscape:false,		
										           title: "?????????????????????????????????????????????",		
										           position:"center",
										           open:function(){
														$(this).dialog('widget')
												            .find('.ui-dialog-titlebar')
												            .removeClass('ui-corner-all')
												            .addClass('ui-corner-top');
														$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0",
						  			    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
						  			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
						  			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
						  			    $(this).dialog("widget").find(".ui-btndlgpos")
						                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});    	
						  			    
						  			   ///////////////////////////////////  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
						  			  //*WR21072017						  			    
							  			var prefix_member_no_idcard=$('#csh_member_no').val();
											 prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
										var csh_idcard='';
									    if(prefix_member_no_idcard=='ID'){									    	
									    	csh_idcard=$('#csh_id_card').val();
									    } 
						  			    
						  			  //*WR27102014
						  			    var key_idcad='';
						  			    var key_redeem='' ;
						  			    var key_mobile='' ;
						  			    var arr_keyinput=form_key_input.split(',');
						  			    var j_mc=0;
						  			    $.each(arr_keyinput , function(i, val) { 
						  			    	if(arr_keyinput[i]=='I'){
						  			    		j_mc+=1;
						  			    		key_idcad='<tr><td>????????????????????????????????????????????? :</td><td><input type="text" id="coupon_idcard" size="20" value="' + csh_idcard + '" index="'+j_mc+'" class="input-text-pos keymbinput ui-corner-all">' + 
						  			    		'<span style="vertical-align:top"><a href="#" name="bws_idcard" id="bws_idcard" class="btnBwsIdCard"></a></span></td></tr>';
						  			    	}
						  			    	if(arr_keyinput[i]=='R'){
						  			    		j_mc+=1;
						  			    		key_redeem='<tr><td>Coupon/Redeem Code : </td><td><input type="text" id="coupon_code" size="20" index="'+j_mc+'" class="input-text-pos keymbinput ui-corner-all"><td></tr>' ;
						  			    	}
						  			    	if(arr_keyinput[i]=='M'){
						  			    		j_mc+=1;
						  			    		key_mobile='<tr><td>??????????????????????????????????????? : </td><td><input type="text" id="mobile_code" size="20" index="'+j_mc+'" class="input-text-pos keymbinput ui-corner-all"><td></tr>' ;
						  			    	}
						  				});
						  			    
						  			   $('#dlgChkProCoupon').html('<form id="keymbinput"><table border="0">' 
									    		+ key_idcad
									    		+ key_redeem
									    		+ key_mobile
									    		+ '</table></form>');
										  			 $('#keymbinput').find('input[type="text"]').first().focus()
											  			var n =$('#keymbinput').find('input:text').length;	 			
											  			$('.keymbinput').each(function(){
											  				$(this).bind("keydown", function(e) {			  								
															      if (e.which == 13) 
															      { //Enter key
															        e.preventDefault(); //to skip default behavior of the enter key
															        var curr_id=$(this).attr('id');
															        var index=$(this).attr('index');
															        index=parseInt(index);
															        var curr_index=index-1;
															        var nextIndex=curr_index+1;
															        //alert("currId=> "+ curr_id +", curr index=> "+ curr_index  +",nextindex=>" + nextIndex);
															        if(nextIndex < n)
															        	$('#keymbinput').find('input:text')[nextIndex].focus();
															        else
															        {
															        	$('#keymbinput').find('input:text')[curr_index].blur();
															        	$('#btnChkCoupon').trigger('click');
															        	 $('#dlgChkProCoupon').dialog('close');
															        }
															      }
															   });
											  			});
											  			
											  			//////////////////////// APPEND IDCARD READER //////////////////////

											  			$("#bws_idcard").click(function(e){
											  				e.preventDefault();											  				
											  				$('<div id="dlgFormReadIdCard"><span style="color:#336666;font-size:20px;"></span><input type="hidden" size="15" id="id_card_ref"/></div>').dialog({
											  			           autoOpen:true,
											  					   width:'550',
											  					   height:'350',	
											  					   modal:true,
											  					   resizable:false,
											  					   closeOnEscape:false,		
											  			           title: "?????????????????????????????????????????????????????????",
											  			           position:"center",
											  			           open:function(){
											  							$(this).dialog('widget')
											  					            .find('.ui-dialog-titlebar')
											  					            .removeClass('ui-corner-all')
											  					            .addClass('ui-corner-top');
											  				  			$(this).dialog("widget").find(".ui-btndlgpos")
											  				                  .css({"padding":"2","background-color":"#c8c7dc","font-size":"14px"});
											  				  			
											  				  			  
											  				  			  $.get("../../../download_promotion/id_card_newmem/api_verify_from.php?function_next=callbackReadIdCard&member_no=", function(data) {
											  				  				   
											  				  				  $('#dlgFormReadIdCard').empty().html(data);
											  				  				});
											  				  	},close:function(evt){
											  							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
											  								evt.stopPropagation();
											  								evt.preventDefault();		
											  							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
											  								evt.stopPropagation();
											  								evt.preventDefault();  						
											  							}				
											  							$('#dlgFormReadIdCard').dialog("destroy").remove();
											  							
											  			           }
											  				});
											  				
											  			});//func
											  			//////////////////////// APPEND IDCARD READER //////////////////////
											  			
											  			
													    ///////////////////////////////////  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\													    
										            },
										            buttons: [ 
											    	            { 
											    	                text: "????????????",
											    	                id:"btnChkCoupon",
											    	                class: 'ui-btndlgpos', 
											    	                click: function(evt){ 
											    	                	evt.preventDefault();
																 		evt.stopPropagation();
																 		var member_no=$('#csh_member_no').val();
																 		var promo_code= $('#csh_promo_code').val();			
																 		var idcard= $('#coupon_idcard').val();	
																 		     idcard=$.trim(idcard);
																 		var mobile_code=$('#mobile_code').val();//*WR27102014   
																 		var coupon_code= $('#coupon_code').val();
																 			 coupon_code=$.trim(coupon_code);
																 		$.ajax({
																 			type:'post',
																 			url:'/sales/member/callcoupon',
																 			data:{
																 				promo_code:promo_code,
																 				member_no:member_no,
																 				idcard:idcard,	
																		 		coupon_code:coupon_code,
																		 		mobile_no:mobile_code,
																 				rnd:Math.random()
																 			},success:function(data){
																 				var objJson=$.parseJSON(data);
																				if(objJson.status=='OK'){																					
																					$('#status_couponpromo').val('Y');
																					$('#csh_coupon_code').val(coupon_code);
																					$('#csh_id_card').val(idcard);
																					//WR19072017
																					if(promo_code=='OC02340617'){
																						playMstPromotion(promo_code,1);
																						$('#dlgChkProCoupon').dialog('close');
																						return false;
																					}//if 
																					
																				}else{
																					$('#status_couponpromo').val('N');
																					$('#csh_coupon_code').val('');
																					 jAlert(objJson.status_msg, '????????????????????????????????????',function(){
																						$('#csh_member_no').focus(); 
																						return false;
																					});	
																					initTblTemp()
																					initFormCashier();
																					initFieldOpenBill();																					
																 				}
																 			}
																 		});
																 		
																 		$('#dlgChkProCoupon').dialog('close');
																 		
											    	                }
											    	            }
											    	  ]
										            ,
										            close:function(evt){
										            	if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
															evt.stopPropagation();
								    						evt.preventDefault();
								    						initTblTemp()
															initFormCashier();
															initFieldOpenBill();
														}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
															evt.stopPropagation();
								    						evt.preventDefault();		    		    						
														}	
										            	$(this).remove();
										           }
										            
										      });
							    			//show dialog check ecoupon
							    			
										    ///////////////////////////////////////\\\////////////////////////////////////////////
						    				$('#dlgProCoupon').dialog("close");
									  });
		    			    		 
		    			    	}
		    			    });

		    			    //////////////////////////////////END/////////////////////////////////////
			            },
			            close:function(evt){
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
								evt.stopPropagation();
	    						evt.preventDefault();
	    						initTblTemp()
								initFormCashier();
								initFieldOpenBill();
							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
								evt.stopPropagation();
	    						evt.preventDefault();
	    						initTblTemp()
								initFormCashier();
								initFieldOpenBill();
							}	
			            	$(this).remove();
			        }
		         });
	    	//////////////////////////////////////////xx////////////////////////////////////////
	    });//end
		
		$('#btnClosePageCashier').button({
			icons:{primary:"btnClosePageCashier"}
		}).click(function(e){
			e.preventDefault();
			jConfirm('???????????????????????????????????????????????????????????????????????????????????????????', 'CONFIRM MESSAGE', function(r) {
			    if(r){	
			    	//close current iframe
					window.parent.location.href = window.parent.location.href;
				}
			});
			return false;
		});
		
		
		$('#btnContact').button({
			icons:{primary:"btnContact"}
		});
		
		//up diary1 to cluster
		$('#btnUp2Pdy').button({
			icons:{primary:"btnUp2Pdy"}
		}).click(function(e){
			e.preventDefault();
			if(getOnlineStatus()==1){
				
				//-------------------------------------------------------------------------
				if($('#ajaxBusy').length){
					$('#ajaxBusy').hide();
				}
				var dlgUpdData = $('<div id="dlgUpdData"><div id="myProgressBar"><span id="pg-status" class="pg-status"></span></div></div>');
	            if($("#dlgUpdData").dialog( "isOpen" )===true) {
	            	return false;
	            }//if
	            var p=0;
	            dlgUpdData.dialog({
		           autoOpen:true,
				   width:'500',
				   height:'100',	
				   modal:true,
				   resizable:false,
		           title: "Please wait... ",		
		           position:"center",
		            open:function(){		
		            	$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
		            	//run process background
					    
		            	var objReqUp2Data=$.ajax({
							type:'post',
							url:'/sales/accessory/reupddata',
							cache:false,
							data:{
								rnd:Math.random()
							},
							success:function(){
								objReqUp2Data=null;
//								 jAlert('?????????????????????????????????????????????????????????', '??????????????????????????????',function(){			
//										return false;
//									});	
								 return false;
								
							}
						});		
		            	setTimeout(function(){
		            		if(objReqUp2Data!=null){
		            			objReqUp2Data=null;
		            			objReqUp2Data.abort();
		            		}
		            	},10000);
				
	    			    $("#myProgressBar").progressbar({
	    			    	value:1,
	    			    	 create: function(event, ui) {
	    			    		 $("#myProgressBar .ui-widget-header").css("background-image", "none");
	    			    		 $(this).find('.ui-widget-header').css({'background-color':'#0ba1b5'});
	    			    	 }
	    			    });
	    			    
	    			    $("#myProgressBar").css({
	    			    	"background-color":"#F2F2F2"
	    			    });
	    			       			    
	    			    $("#pg-status").css({    	    			    	
	    			    	"float":"left",
		    			    "margin-left":"50%",
		    			    "margin-top":"5px",
		    			    "font-weight":"bold",
		    			    "text-shadow":"1px 1px 0 #fff",
		    			    "color":"#FF3300"
	    			    });
	    			   
				        var timer9 = setInterval(function(){	
					            //This animates the bar
					            $("#myProgressBar .ui-progressbar-value").animate({width: p + "%"},200);
					            $("#myProgressBar").children('span.pg-status').html(p + "%");					            
					            p = p +1;
					            if(p>100){
					                $("#myProgressBar .ui-progressbar-value").animate({width: "100%"},200);
					                $("#myProgressBar").children('span.pg-status').html("100%");					                
					                clearInterval(timer9);
					                $("#dlgUpdData").dialog('close');
					                window.location.reload(true);
					    			return false;
					            }
					    },200);
			    			
				   },close:function(evt){
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
								evt.stopPropagation();
	    						evt.preventDefault();
							}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
								evt.stopPropagation();
	    						evt.preventDefault();		    		    						
							}	
			            	$(this).remove();
				   }
				            
				});
				//-------------------------------------------------------------------------
			}
				
			return false;
		});
		
		$('#btn_trn_alipay').button({
			icons:{primary:"btn_payment_alipay"}
		}).click(function(e){
			e.preventDefault();
			//----------------------------------------------
			var dlgTrnAlipay = $('<div id="dlgTrnAlipay"></div>');
            if($("#dlgTrnAlipay").dialog("isOpen")===true) {
            	return false;
            }//if
            var p=0;
            dlgTrnAlipay.dialog({
	           autoOpen:true,
			   width:'60%',
			   height:'250',	
			   modal:true,
			   resizable:false,
	           title: "BILL PAYMENT BY ALIPAY (TODAY ONLY)",		
	           position:"center",
	            open:function(){		
	            	$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
	            	     
				    $.ajax({
						type:'post',
						url:'/sales/accessory/trnalipaytoday',
						cache:false,						
						data:{
							rnd:Math.random()
						},success:function(data){
							$("#dlgTrnAlipay").empty().html(data);
							return true;
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
		            	$('#dlgTrnAlipay').remove();
		            	$('#dlgTrnAlipay').dialog('destroy');
		            	
			   }
			            
			});
			//----------------------------------------------
		});
		
		
		$('#btnReloadPageCashier').button({
			icons:{primary:"btnReloadPageCashier"}
		}).click(function(e){
			e.preventDefault();
			//------------------------------------------------------------
			var dlgReloadPage = $('<div id="dlgReloadPage"><div id="myProgressBar"><span id="pg-status" class="pg-status"></span></div></div>');
            if($("#dlgReloadPage").dialog( "isOpen" )===true) {
            	return false;
            }//if
            var p=0;
            dlgReloadPage.dialog({
	           autoOpen:true,
			   width:'500',
			   height:'100',	
			   modal:true,
			   resizable:false,
	           title: "Please wait... ",		
	           position:"center",
	            open:function(){		
	            	$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
	            	//-----------------------------------------------------------------------------------	     
				    $.ajax({
						type:'post',
						url:'/sales/cashier/repairmysqldb',
						data:{
							rnd:Math.random()
						},success:function(){
							return true;
						}
					});
			
    			    $("#myProgressBar").progressbar({
    			    	value:1,
    			    	 create: function(event, ui) {
    			    		 $("#myProgressBar .ui-widget-header").css("background-image", "none");
    			    		 $(this).find('.ui-widget-header').css({'background-color':'#0ba1b5'});
    			    	 }
    			    });
    			    
    			    $("#myProgressBar").css({
    			    	"background-color":"#F2F2F2"
    			    });
    			       			    
    			    $("#pg-status").css({    	    			    	
    			    	"float":"left",
	    			    "margin-left":"50%",
	    			    "margin-top":"5px",
	    			    "font-weight":"bold",
	    			    "text-shadow":"1px 1px 0 #fff",
	    			    "color":"#FF3300"
    			    });
			        var timer9 = setInterval(function(){	
				            $("#myProgressBar .ui-progressbar-value").animate({width: p + "%"},200);
				            $("#myProgressBar").children('span.pg-status').html(p + "%");
				            p = p +3;
				            if(p>100){
				                $("#myProgressBar .ui-progressbar-value").animate({width: "100%"},200);
				                $("#myProgressBar").children('span.pg-status').html("100%");
				                clearInterval(timer9);
				                $("#dlgReloadPage").dialog('close');
				                window.location.reload(true);
				    			return false;
				            }
				    },200);
			   },close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
    						evt.preventDefault();		    		    						
						}	
		            	$(this).remove();
			   }
			            
			});
			//------------------------------------------------------------			
		});
		
		

		$('#btnDocType').button({
	        icons: { primary: "btnDocType"}
	    });

		$('#btnOther').button({
	        icons: { primary: "btnOther"}
	    });

		$('#btnVT').button({
	        icons: { primary: "btnVT"}
	    });
		
		$('#btnSmsPro').button({
	        icons: { primary: "btnSmsPro"}
	    });
		// *WR12062014 ??????????????????????????????
		$('#btnGreenBag').button({
	        icons: { primary: "btnGreenBag"}
	    });
		
		$('#btnCoOperation').button({
	        icons: { primary: "btnCoOperation"}
	    });

		$('#btnBrowsProduct').button({
	        icons: { primary: "btnBrowsProduct"}
	    });
	    
		//*WR 26042013 ??????????????????????????????
		$('#btnSwapCashier').button({
	        icons: { primary: "btnSwapCashier"}
	    });

		$('#btnPaySummary').button({
	        icons: { primary: "btnPaySummary"}
	    });

		$('#btnBillDetail').button({
	        icons: { primary: "btnBillDetail"}
	    });

		$('#btnCheckStock').button({
	        icons: { primary: "btnCheckStock"}
	    });
	    		
		$('#btnPromotionData').button({
	        icons: { primary: "btnPromotionData"}
	    });
		
		$('#btnPromotionData').click(function(e){
			/**
			 * @desc
			 *
			 */
			e.stopPropagation();
			e.preventDefault();
			var opts_dlgPromoItems = {
					autoOpen:true,
					width:'80%',		
					height:580,	
					modal:true,
					resizable:true,
					position:"center",
					showOpt: {direction: 'up'},		
					closeOnEscape: true,	
					title:"<span class='ui-icon ui-icon-cart'></span>PROMOTION",
					open: function(){  
						$(this).parents(".ui-dialog:first").css({"padding":"0","margin":"0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first")
					    .find(".ui-dialog-content")
					    .css({"background-color":"#c5bcdc","font-size":"22px","color":"#0000FF","padding":"0"});						  
					    $("#tabs-promo-items").tabs({
					    	selected:0,
					        ajaxOptions: {
					            error: function( xhr, status, index, anchor ) {
					                $( anchor.hash ).html(
					                    "error occured while ajax loading.");
					            },
					            success: function(data) {
					            	 $(".display").each(function(){
					            		///////////////////////////////////////////////////////////
					            		 var datatable_promo_id=$(this).attr('id');
						            	 if(datatable_promo_id=='datatables_promoitems'){
						            		
						            		 $('#datatables_promoitems').dataTable( {
						            			 "bDestroy": true,
									       		 "fnDrawCallback": function(){
									       				
										       		      $('table#datatables_promoitems td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
										       		      $('table#datatables_promoitems td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
										       		      
											       		   $('#datatables_promoitems tr').each(function(){
												       			$(this).click(function(){
												       				var strJson=$(this).attr('idd');
												       				if(strJson!=''){
														       				var selPro=$.parseJSON(strJson);
														       				dlgPromoDetails(selPro.promo_code);
														       			}
												       			});
												       		});
										       		      
									       			}
												} );
						            	 }else if(datatable_promo_id=='datatables_promoitems2'){
						            		
						            		 $('#datatables_promoitems2').dataTable( {
						            			 "bDestroy": true,
									       			"fnDrawCallback": function(){
										       		      $('table#datatables_promoitems2 td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
										       		      $('table#datatables_promoitems2 td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
										       		      
										       		   $('#datatables_promoitems2 tr').each(function(){
									     	       			$(this).click(function(){
									     	       				var strJson=$(this).attr('idd');
									     	       				if(strJson!=''){
									     			       				var selPro=$.parseJSON(strJson);
									     			       				dlgPromoDetails(selPro.promo_code);
									     			       			}
									     	       			});
									     	       		});
										       		   
									       			}
						            		 
												} );
						            	 }
					            		 //////////////////////////////////////////////////////////
					            		 
					            	 });
					            		 
					  
					            }
					        }
					    });					
						
					},				
					close: function(evt,ui) {
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){						
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){						
						}
						
					}
					
				 };			
		
			//$('#dlgPromoItems').dialog('destroy');
			$('#dlgPromoItems').dialog(opts_dlgPromoItems);			
			$('#dlgPromoItems').dialog('open');
					
		});

		$('#btnMemberData').button({
	        icons: { primary: "btnMemberData"}
	    });

		$('#btnProductData').button({
	        icons: { primary: "btnProductData"}
	    });
		
		$('#btnQstPdtOfBill').button({
	        icons: { primary: "btnQstPdtOfBill"}
	    });

		$('#btnRegNewCard').button({
	        icons: { primary: "btnRegNewCard"}
	    });
		
		$('#btnSelMemberCatalog').button({
	        icons: { primary: "btnSelMemberCatalog"}
	    });

		/*
		$('#btn_cal_promotion').button({
	        icons: { primary: "btn_cal_promotion" }
	    });
	    */

		//button cancel bill
		$('#btnCancle').button({
	        icons: { primary: "btnCancle" }
	    });

		$("#btnSelMemberCatalog").click(function(e){
		    e.preventDefault();
		    return false;
		});
		
		$('#btnCoOperation').click(function(evt){
			evt.preventDefault();
			///999999999999999
			$('<div id="dlgCoOperation"></div>').dialog({
		           autoOpen:true,
				   width:'65%',
				   height:'350',	
				   modal:true,
				   resizable:true,
				   closeOnEscape:false,		
		           title: "CO-PROMOTION",		
		           position:"center",
		           open:function(){
		        	   $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
						    																			"font-size":"26px","color":"#0000FF",
						    																			"padding":"0 0 0 0"});	
					    
					    $.ajax({
					    	type:'post',
							url:'/sales/accessory/cooperation',
							cache:false,
							data:{							
								now:Math.random()
							},
							success:function(data){
								//START CONTENT
								$('#dlgCoOperation').empty().html(data);	
					    		 $('.tableNavSelCooperation ul li').not('.nokeyboard').navigate({
							        wrap:true
							    }).click(function(evt){		
								    evt.stopPropagation();
								    evt.preventDefault();										    
								    var sel_promo=$.parseJSON($(this).attr('idpromo'));	
								    var promo_play=sel_promo.promo_code;		
								    var promo_des=sel_promo.promo_des;		
								    $('#other_promotion_title').html(promo_des);
								    //$('#dlgCoOperation').dialog('close');
								    
								    //*WR24012017 for support pro tour								  
								    //var form_key_input=sel_promo.key_input; 
								    var member_no=$('#csh_member_no').val();
								    var member_type=$('#csh_member_type').html();								    
								    if(sel_promo.member_tp == 'Y' && member_no.length<1){
									    jAlert('????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){
									    	$('#dlgCoOperation').dialog('close');
									    	initTblTemp();
									    	initFormCashier();
									    	initFieldOpenBill();
									    	return false;
									    });	
									    return false;
								    }else if(member_no.length<1 && member_type!=='WALK IN'){
									    jAlert('?????????????????? WALK IN ????????????????????????????????? ENTER ??????????????????????????????????????????????????? \n?????????????????????????????????????????????????????????????????????????????? WALK IN ???????????????????????????????????????????????????', 'WARNING!',function(){
									    	$('#dlgCoOperation').dialog('close');
									    	initTblTemp();
									    	initFormCashier();
									    	initFieldOpenBill();
									    	return false;
									    });	
									    return false;
								    }
								    if(promo_play=='TOUR01'){				    					
				    					$('#csh_promo_tp').val('COR');
				    					formTour(promo_play);				    					
				    					$('#dlgCoOperation').dialog('close');
			    						return false;
								    }else if(sel_promo.promo_code=='OX02460217' || sel_promo.promo_code=='OX02460217_2'){
				    					$('#csh_promo_tp').val('COR');				    					
				    					$('#csh_play_main_promotion').val('Y');
				    					$('#csh_play_last_promotion').val('Y');				    					
				    					if(member_type!=='WALK IN'){
				    						jAlert('This promotion is for WALK IN customers only.', 'WARNING!',function(){										    	
										    	return false;
										    });	
										    return false;
				    					}else{
				    						//callBackToDo2(sel_promo.promo_code,'Y','','N','45150');
				    						fromreadprofile(sel_promo.promo_code,'Y','','','','N',sel_promo.promo_des);
				    						$('#dlgCoOperation').dialog('close');
											return false;
				    					}				    					
				    					
				    					return false;
				    					
				    				}else{
				    					 //apiread("######",'tour_from');
				    					 $('#dlgCoOperation').dialog('close');
										 return false;
				    				}
								   
								    //apiread("######",'tour_from');
								    return false;
							    });
								//START CONTENT
							}
					    });
			    		 
		           },				           
		           buttons: [ 			
		    	                { 
			    	                text: "Close",
			    	                id:"btnCloseSelCoPromo",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	evt.preventDefault();
								 		evt.stopPropagation();	
								 		$('#dlgCoOperation').dialog('close');										 		
			    	                }
		    	                }
		    	  ]		
		           ,close:function(evt){
		        	   if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
							evt.preventDefault();			
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
							evt.preventDefault();		
						}
		        	   $('.tableNavSelCooperation ul').navigate('destroy');
		        	   $('#dlgCoOperation').remove();
		           } 
		    });			    					
			///999999999999999
			return false;
		});
		
		$('#btnBillDetail').click(function(evt){
			evt.preventDefault();
			var opts_dlgBillDetail={
					autoOpen:false,
					modal:true,
					width:'75%',
					height:580,
					resizable:true,
					position:'center',
					closeOnEscape: true,
					title:"<span class='ui-icon ui-icon-document-b'></span>BILL DESCRIPTION",
					open:function(){
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px","margin-top":"0",
	    			    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
	    			    //button style	
	    			    $(this).dialog("widget").find(".ui-btndlgpos")
	                    .css({"padding":"2","background-color":"#c8c7dc","font-size":"16px"});
	    			    
					    $(".ui-widget-overlay").live('click', function(){
					    	$("#sch_member_no").focus();
						});
					    
					    $.ajax({
					    	type:'post',
					    	url:'/sales/cashier/bill2day',
					    	data:{
					    		rnd:Math.random()
					    	},
					    	success:function(data){
					    		$('#dlgBillDetail').html(data);
					    		if($('p#item_not_found').length != 0){
									return false;
								}
					    		//start data table
					    		var oTable = $('#datatables_bill2day').dataTable();
								$('#datatables_bill2day').dataTable({
									"bDestroy": true,
					       			"fnDrawCallback": function(){
					       												       	
						       		      $('table#datatables_bill2day td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
						       		      $('table#datatables_bill2day td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
						       		      
							       		   $('#datatables_bill2day tr').each(function(){
								       			$(this).click(function(){
								       				var strJson=$(this).attr('idd');
								       				if(strJson!=''){
										       				var seldocno=$.parseJSON(strJson);
														    
										       			}
								       			});
								       		});
						       		      
					       			}//end callback
								});	
								 oTable.fnSort([[0,'desc'],[1,'desc']]);
					    		//start data table
					    		
					    	}
					    });
						
					},
					 buttons: [ 
			    	            { 
			    	                text: "Close",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	  evt.preventDefault();	
			    	                	  $('#dlgBillDetail').dialog('close');
			    	                } 
			    	            }
			    	        ],
					close:function(){
					}
			};
			$('#dlgBillDetail').dialog('destroy');
			$('#dlgBillDetail').dialog(opts_dlgBillDetail);
			$('#dlgBillDetail').dialog('open');			
		});
		
		$('#btnContact').click(function(evt){
			evt.preventDefault();
			var opts_dlgContact={
					autoOpen:false,
					modal:true,
					width:'60%',
					height:300,
					resizable:true,
					position:'center',
					closeOnEscape: true,
					title:"<span class='ui-icon ui-icon-person'></span>Contact Number",
					open:function(){
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px","margin-top":"0",
	    			    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
	    			    //button style	
	    			    $(this).dialog("widget").find(".ui-btndlgpos")
	                    .css({"padding":"2","background-color":"#c8c7dc","font-size":"16px"});
	    			    
					    $(".ui-widget-overlay").live('click', function(){
					    	$("#sch_member_no").focus();
						});
					    
					    $.ajax({
					    	type:'post',
					    	url:'/sales/cashier/contact',
					    	data:{
					    		rnd:Math.random()
					    	},
					    	success:function(data){
					    		$('#dlgContact').html(data);
					    	}
					    });
						
					},
					 buttons: [ 
			    	            { 
			    	                text: "Close",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	  evt.preventDefault();	
			    	                	  $('#dlgContact').dialog('close');
			    	                } 
			    	            }
			    	        ],
					close:function(){
					}
			};
			$('#dlgContact').dialog('destroy');
			$('#dlgContact').dialog(opts_dlgContact);
			$('#dlgContact').dialog('open');
		});
		
		$('#btnMemberData').click(function(evt){
			evt.preventDefault();
			var opts_dlgMemberData={
					autoOpen:false,
					modal:true,
					width:'60%',
					height:'450',
					resizable:true,
					position:'center',
					closeOnEscape: true,
					title:"<span class='ui-icon ui-icon-person'></span>MEMBER PROFILE",
					open:function(){
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px","margin-top":"0",
	    			    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
	    			    $(this).dialog("widget").find(".ui-btndlgpos")
	                    .css({"padding":"2","background-color":"#c8c7dc","font-size":"16px"});
					    $(".ui-widget-overlay").live('click', function(){
					    	$("#sch_member_no").focus();
						});
					    ////////////////// WR 09062014 ///////////////////////
					    $.ajax({
				    	type:'post',
				    	url:'/sales/member/formmember',
				    	data:{
				    		rnd:Math.random()
				    	},
				    	success:function(data){
				    		$('#dlgMemberData').html(data);
				    		$('#sch_member_no').focus();				    		
				    		$('#sch_member_no,#sch_id_card,#sch_mobile_no,#sch_member_name,#sch_member_surname').keypress(function(evt){
				    			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
			   			        if(key == 13){	
					    			evt.preventDefault();
					    			$('#btnSchMemberOk').trigger('click');
					    			return false;
			   			        }
				    		});
				    	}
				    });
					 ////////////////////////////// WR09062014 /////////////////////////////////   
					},
					 buttons: [ 
			    	            { 
			    	                text: "Search",
			    	                id:"btnSchMemberOk",
			    	                class: 'ui-btndlgpos', 
			    	                click: function(evt){ 
			    	                	  evt.preventDefault();
			    	                	 /////////////////////////////////START\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
			    	                	  $('#sch_member_info').html('');
			    	                	  var sch_member_no=$('#sch_member_no').val();
			    	                	  var sch_id_card=$('#sch_id_card').val();
			    	                	  var sch_mobile_no=$('#sch_mobile_no').val();
			    	                	  var sch_member_name=$('#sch_member_name').val();
			    	                	  var sch_member_surname=$('#sch_member_surname').val();			    	                	  
											$.ajax({
												type:'post',
												url:'/sales/member/searchmemberprofile',
												data:{
													member_no:sch_member_no,
													mobile_no:sch_mobile_no,
													id_card:sch_id_card,
													name:sch_member_name,
													surname:sch_member_surname,												
													rnd:Math.random()
												},success:function(data){
													$('#sch_member_content').html(data);
													//------------------data---------------------
													$('.sch_member').each(function(){
														$(this).click(function(e){
															e.preventDefault();
															$("#dlgMemberData").scrollTop("0");
															//xxxxxxxxxxxxxxxxxxxxxxxxxx
															var member_no=this.id;
															var objReq=$.getJSON(
																	"/sales/member/callmemberprofile",
																	{
																		status_no:'00',
																		member_no:member_no,
																		actions:'memberinfo'
																	},
																	function(data){		
																		objReq=null;
																			$.each(data.member, function(i,m){
																				if(m.exist=='yes'){
																					if(m.surname!=undefined){
																						var member_fullname=m.name+" "+m.surname;
																					}else{
																						var member_fullname=m.name;
																					}									
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
																					var percent_discount='0';
																					if(m.percent_discount!=undefined){
																						percent_discount=parseInt(m.percent_discount);
																					}
																					var mp_point_sum=m.mp_point_sum;
																					var buy_net=m.buy_net;
																					var show_info=jQuery.trim(m.member_no)+" \n"+
																					member_fullname +" \n"+
																				 	"????????????" + jQuery.trim(m.special_day)+" \n"+
																					jQuery.trim(m.address)+" "+
																					jQuery.trim(m.sub_district)+" "+
																					jQuery.trim(m.district)+" "+
																					jQuery.trim(m.province_name)+" "+
																					jQuery.trim(m.zip)+" \n"+																					
																					jQuery.trim(m.mobile_no);
																					show_info  = show_info.replace(/\\/g, "");
																					$('#sch_member_info').html(show_info);
																				}
																									
																			});
																		}
																			
															);//end json
															// Wait for 5 seconds
															setTimeout(function(){
															  // If the request is still running, abort it.
															  if (objReq){
																  objReq.abort();
																  objReq=null;
															  }
															},5000);
															//xxxxxxxxxxxxxxxxxxxxxxxxxx
															return false;
														});
													});
													//------------------data---------------------
												}
											});							
											///////////////////////////STOP\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\    	                	 
			    	                } 
			    	            }
			    	        ],
					close:function(){
					}
			};
			$('#dlgMemberData').dialog('destroy');
			$('#dlgMemberData').dialog(opts_dlgMemberData);
			$('#dlgMemberData').dialog('open');
		});		

		$("#btnQstPdtOfBill").click(function(e){
			e.preventDefault();
			//start
			$("<div id='dlgQstPdtOfBill'></div>").dialog({
		     	   autoOpen:true,
						width:'70%',		
						height:'auto',	
						modal:true,
						resizable:true,
						position:"center",
						showOpt: {direction: 'slid'},
						closeOnEscape: true,
						title:"<span class='ui-icon ui-icon-cart'></span>ITEM ON BILL",
						open: function(){    
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
						    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ffffff",
							    																			"font-size":"24px","color":"#000000",
							    																			"padding":"5 0 0 0"});	
			   				$("#dlgQstPdtOfBill").append("&nbsp;&nbsp;PRODUCT ID&nbsp;:&nbsp;<input type='text' id='adt_product_id' size='20' class='input-text-pos'/><hr style='border:0pt solid#e0e0e0;'>");
			   				$("#dlgQstPdtOfBill").append("<p><div id='res_qproduct' style='left:0;'></div></p>");
			   				//$(this).parent().append('<div id="footer" style="position:absolute;bottom:0;float:left;width:100%;background-color:#c9c9c9;padding:5px;"></div>'); 
				   			$("#adt_product_id").focus();
			   				$("#adt_product_id").keypress(function(evt){
					   					var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					   			        if(key == 13){	
						   			        var product_id=$.trim($("#adt_product_id").val());
						   			        if(product_id.length==0){
						   			        	jAlert('Please specify product id.', 'Try again.',function(){		
						   			        		$("#adt_product_id").focus();																				
													return false;
												});	
							   			    }else{
								   			    $("#res_qproduct").html('');
								   			    $.ajax({
									   			    	type:'post',
									   			    	url:'/sales/cashier/qestpdtofbill',
									   			    	data:{
								   			    			product_id:product_id,
								   			    			rmd:Math.random
								   			    		},
								   			    		success:function(data){
								   			    			$("#res_qproduct").html(data);
									   			    	}
									   			    	
									   			    });
								   			}
						   			        return false;
					   			        }
						   			 });
							},close:function(){
				   				$(this).remove();
				   			}
				});
			//stop
		});

	    $('#btnPaySummary').click(function(e){
		    e.preventDefault();		 
	    	var arr_docdate=$('#csh_doc_date').html().split('/');
	    	var date_start=$.trim(arr_docdate[2])+'-'+$.trim(arr_docdate[1])+'-'+$.trim(arr_docdate[0]);    
	    	var opts_rptsummary={
                   autoOpen:false,
                   width:'60%',
                   height:'600',
                   modal:true,
                   resizeable:true,
                   position:'top',
                   showOpt: {direction:'up'},
                   closeOnEscape:true,
                   title:"<span class='ui-icon ui-icon-print'></span>",
                   open:function(){
                       $("#dlgRptSummary").html("");
                       $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ededed","font-size":"20px","color":"#222"});
                       $.ajax({
                           type:'post',
                           url:'/report/report/viewreport',
                           cache:false,
                           data:{                                     
                           		data1:date_start,
                                data2:date_start,                                                          
                                now:Math.random()
                           },
                           success:function(data){
                               $("#dlgRptSummary").html(data);
                           }//end success function
                       });//end ajax pos
                   }};
	           $("#dlgRptSummary").dialog("destroy");
	           $("#dlgRptSummary").dialog(opts_rptsummary);
	           $("#dlgRptSummary").dialog("open");
         });

		$("#btnCheckStock").click(function(e){
			//return false;//set temp for wait new solution to resolve load data is long time.
		    e.preventDefault();
		    var dialogOpts_CheckStock = {
					autoOpen: false,
					width:'75%',		
					height:'auto',	
					modal:true,
					resizable:true,
					position:"center",
					showOpt: {direction: 'up'},		
					closeOnEscape:false,	
					title:"<span class='ui-icon ui-icon-cart'></span>STOCK ONHAND",
					open: function(){     	
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
		    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
			    			    										"font-size":"27px","color":"#000"});
//		    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
//		    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"} )
		    			    // button style		
		    			    $(this).dialog("widget").find("button")
			                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
		                  
							$("#dlgCheckStock").html("<p><img src='/sales/img/activity_indicators_02.gif'></p>");
							$("#dlgCheckStock").load("/sales/cashier/checkstock?now="+Math.random(),
							function(){	
								$("#stock_product_id").focus();
								$("#stock_product_id").keypress(function(evt){
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
								        var product_id=jQuery.trim($("#stock_product_id").val());
								        if(product_id==''){
									    	//product_id="all";    	
									    	return false;
									    }
								        var opts={
										        	type:'post',
										        	url:'/sales/cashier/checkstocklist',
										        	cache:false,
										        	data:{
								        				product_id:product_id,
								        				actions:'checkstock_list',
								        				num_rnd:Math.random()
								        			},
								        			success:function(data){
									        			$("#checkstock_content").html(data);
									        		}
										        };
								       $.ajax(opts); 
							           return false;
							        }
								});
								cmdEnterKey("stock_product_id");
								//auto stock start
								var cache = {};
								$("#stock_product_id").autocomplete({
									source: function(request, response) {
									  var term          = request.term.toLowerCase(),
									      element       = this.element,
									      cache         = this.element.data('autocompleteCache') || {},
									      foundInCache  = false;
									  $.each(cache, function(key, data){
									    if (term.indexOf(key) === 0 && data.length > 0) {
									      response(data);
									      foundInCache = false;
									      return;
									    }
									  });

										if (foundInCache) return;
										
										$.ajax({
											url: '/sales/cashier/autoproduct',
											dataType: "json",
											data: request,
											success: function(data) {
												cache[term] = data;
												element.data('autocompleteCache', cache);
												response(data);
											}
										});
									},
									select: function(event,ui){
										$("#stock_product_id").val(ui.item.value);
										//$("#dlgCheckStock").dialog("close");
										cmdEnterKey("stock_product_id");
									},					
									minLength:3,
									change: function() {
										//prevent 'to' field being updated and correct position
										$("#stock_product_id").val("").css("top", 2);
									}
								});
								//auto stock end
							});
					},				
					close: function(){
						$('#btnCheckStock').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
					 },
					 buttons:{
						 "CLOSE":function(){ 				
							$(this).dialog("close");
							return false;
						}
					}
				};
				$('#dlgCheckStock').dialog('destroy');
				$('#dlgCheckStock').dialog(dialogOpts_CheckStock);			
				$('#dlgCheckStock').dialog('open');
			return false;
		});

		 $("#btnSwapCashier").click(function(e){
			    e.preventDefault();
			    var dialogOpts_swapCashier = {
						autoOpen: false,
						width:'23%',		
						height:'auto',	
						modal:true,
						resizable:true,
						position:"center",
						showOpt: {direction: 'up'},		
						closeOnEscape:true,	
						title:"<span class='ui-icon ui-icon-person'></span>???????????????????????????????????????????????????",
						open: function(){     						
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							 $(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
		    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","margin-top":"0","background-color":"#c5bcdc",
			    			    										"font-size":"27px","color":"#000"});
		    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
		    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
		    			    // button style		
		    			    $(this).dialog("widget").find("button")
			                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
								$("#dlgSwapCashier").html("");
								$("#dlgSwapCashier").load("/sales/cashier/swapcashier?now="+Math.random(),
								function(){	
									$("#swap_cashier_id").focus();
									//*** check lock unlock
									if($("#csh_lock_status").val()=='Y'){
										lockManualKey();
									}else{
										unlockManualKey();
									}
									//*** check lock unlock
									$("#swap_cashier_id").keypress(function(evt){
										var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
								        if(key == 13) {
								            evt.preventDefault();
								            var swap_cashier_id=jQuery.trim($("#swap_cashier_id").val());
								            if(swap_cashier_id=='') return false;
								            swapCashier(swap_cashier_id);
								            $("#dlgSwapCashier").dialog("close");
								            return false;
								        }
									});
									
								});
						},				
						close: function(){
							$('#dlgSwapCashier').dialog('destroy');
							$('#btnSwapCashier').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');			
							//refresh parent
							//window.parent.location.href = window.parent.location.href;
							//reload all iFrames
							$('iframe').each(function() {
							  this.contentWindow.location.reload(true);
							});
							
							
							
						 }
					};			
				
					$('#dlgSwapCashier').dialog('destroy');
					$('#dlgSwapCashier').dialog(dialogOpts_swapCashier);			
					$('#dlgSwapCashier').dialog('open');
				return false;
		});
	    
		 $("#btnVT").click(function(e){
			    e.preventDefault();		    
			    var dialogOpts_vt = {
						autoOpen: false,
						width:'55%',		
						height:'auto',	
						modal:true,
						resizable:true,
						position:"center",
						showOpt: {direction: 'up'},		
						closeOnEscape:true,	
						title:"<span class='ui-icon ui-icon-document-b'></span>TAX INVOICE",
						open: function(){   
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
		    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px 5px","margin-top":"0","background-color":"#c5bcdc",
			    			    										"font-size":"27px","color":"#000"});
		    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
		    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
		    			    // button style		
		    			    $(this).dialog("widget").find("button")
			                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
							$(this).find('div,button,select, input, textarea').blur();
							$('.ui-dialog-buttonpane button:last').blur();
							var member_id=$('#csh_member_no').val();
							$("#dlgVatTotal").html("");
							$("#dlgVatTotal").load("/sales/cashier/vattotal?member_id=" + member_id + "&now="+Math.random(),
							function(){								
								$("#vt_name").focus();
								$("#vt_name").keypress( function(evt) {
							        var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
							            evt.preventDefault();
							            if(jQuery.trim($("#vt_name").val())==''){
							            	 jAlert('???????????????????????????????????????','WARNING!',function(){
									            	$("#vt_name").focus();
									            	return false;
										      });
								        }else{
								        	$("#vt_address1").focus();
									    }
							            return false;
							        }
								});//
								$("#vt_address1").keypress( function(evt) {
							        var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
							            evt.preventDefault();
								        $("#vt_address2").focus();
							            return false;
							        }
								});//
								$("#vt_address2").keypress( function(evt) {
							        var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
							            evt.preventDefault();
								        $("#vt_address3").focus();
							            return false;
							        }
								});//
								$("#vt_address3").keypress( function(evt) {
							        var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
							            evt.preventDefault();
							            $('.ui-dialog-buttonpane button:last').focus();
							            return false;
							        }
								});//		
								
								var vt_name_val=$("#vt_name_val").val();
								var vt_address_val1=$("#vt_address_val1").val();
								var vt_address_val2=$("#vt_address_val2").val();
								var vt_address_val3=$("#vt_address_val3").val();
								
								//*WR26012017
								var csh_member_no=$('#csh_member_no').val();
					            csh_member_no=$.trim(csh_member_no);
					            if(csh_member_no.length>0){					            	
					            	$('#vt_passport_no').attr('readonly', true);
						        }else{						        	
						        	$('#vt_passport_no').removeAttr('readonly');
						        }
								var vt_passport_no=$("#vt_passport_no_val").val();
								var arr_pp=vt_passport_no.split(':');
								vt_passport_no=arr_pp[1];
								$("#vt_passport_no").val(vt_passport_no);
								//*WR23052017 for store country code
								var vt_country_code=arr_pp[2];
								if(vt_country_code!==''){									
									$("select#tour_country option[value='" + vt_country_code + "']").attr("selected", "selected");
								}
								
								//WR18122013
								var vt_taxid=$('#vt_taxid').val();
								var vt_taxid_branch_seq=$('#vt_taxid_branch_seq').val();
								
								if($.trim(vt_name_val).length>0){
										$("#vt_name").val(vt_name_val);
										$("#vt_address1").val(vt_address_val1);
										$("#vt_address2").val(vt_address_val2);
										$("#vt_address3").val(vt_address_val3);
										//WR18122013
										$('#taxid').val(vt_taxid);
										if(vt_taxid_branch_seq!='-1'){
											$('input:radio[name=taxid_branch][value=b]').click();
											$('#taxid_branch_seq').val(vt_taxid_branch_seq);
										}else{
											$('#taxid_branch_seq').val('');
										}
								}
							});
						},				
						close: function() {
							$('#dlgVatTotal').dialog('destroy');
							$('#btnVT').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
						 },
						buttons: {							
								"OK":function(){ 	
									var vt_name=$("#vt_name").val();			
									var vt_address1=$("#vt_address1").val();
									var vt_address2=$("#vt_address2").val();
									var vt_address3=$("#vt_address3").val();
									
									//WR18122013
									var taxid=$("#taxid").val();
									taxid=$.trim(taxid);
									vt_name=$.trim(vt_name);
									
									var taxid_branch=$("input[name='taxid_branch']:checked").val();
									var taxid_branch_seq='';
									if(taxid_branch=='b'){
										taxid_branch_seq=$('#taxid_branch_seq').val();
										taxid_branch_seq=$.trim(taxid_branch_seq);
										if(vt_name!='' && taxid_branch_seq==''){
											jAlert('PLEASE SPECIFY BRANCH NO.','WARNING!',function(){
												$('#taxid_branch_seq').focus();
												 return false;
											  });
											return false;
										}
									}else{
										taxid_branch_seq="-1";
									}			
									
									//alert(taxid + "," + taxid_branch + "," + taxid_branch_seq);
									
									if(jQuery.trim(vt_name)!="" && jQuery.trim(vt_address1)!=""){
										$("#csh_doc_tp_show").html("??????????????????????????????????????????");
										$("#flgbill").html("{?????????????????????????????????}");
										$("#csh_doc_tp_vt").val("VT");
									}else{
										$("#flgbill").html("");
										$("#csh_doc_tp_vt").val("");
									}
									$("#vt_name_val").val(vt_name);
									$("#vt_address_val1").val(vt_address1);		
									$("#vt_address_val2").val(vt_address2);
									$("#vt_address_val3").val(vt_address3);
									
									//WR18122013
									$('#vt_taxid').val(taxid);
									$('#vt_taxid_branch_seq').val(taxid_branch_seq);
									
									//*WR26012017 for support pro tour
									var vt_passport_no=$('#vt_passport_no').val();
									var csh_member_no=$('#csh_member_no').val();
						            csh_member_no=$.trim(csh_member_no);
						            if(csh_member_no.length>0 && vt_passport_no.length>0){
						            	 jAlert('????????????????????????????????????????????????????????????????????????????????? ??????????????????????????????????????? Passport ?????????','WARNING!',function(){		
						            		 $('#vt_passport_no').val('');
								             return false;
									      });
						            	 return false;
							        }
									
						            //*WR23052017 for support country code
						            var tour_country=$('select#tour_country').find('option:selected').val();
						            if(vt_passport_no.length>0 && tour_country.length<1){							 			
						            	jAlert('Please specify country.','WARNING!',function(){		
						            		$('#popup_ok').focus();
						            	setTimeout(function(){				    							
						            		if($('#popup_ok').not(":focus")){
						            			$('#popup_ok').focus();
						            		}
						            	},1000);
						            	$('#tour_country').focus();
						            		return false;
						            	});	
						            	return false;
						            }else if(vt_passport_no.length<1){
						            	//clear passport value
						            	$("#vt_passport_no_val").val('');
						            }
									var vt_passport_no=$('#vt_passport_no').val();
									vt_passport_no=$.trim(vt_passport_no);
									if(vt_passport_no.length>0){
										vt_passport_no='PP:' + vt_passport_no + ':' + tour_country;
									}
									
									if(vt_passport_no.length>1){
										$('#csh_id_card').val(vt_passport_no);
										$('#vt_passport_no_val').val(vt_passport_no);
										var csh_coupon_code=$('#csh_coupon_code').val();
										var arr_chkpassport=csh_coupon_code.split('#');
										var tour_barcode=arr_chkpassport[0];
										var chk_tour_digit=tour_barcode.substring(0,5);
										if(chk_tour_digit=='PRCTG'){
											//$('#vt_passport_no').val(arr_chkpassport[1]);
											arr_chkpassport[1]=vt_passport_no;
											var new_coupon_code=arr_chkpassport[0]+"#"+arr_chkpassport[1]+"#"+arr_chkpassport[2];
											$('#csh_coupon_code').val(new_coupon_code);
											
										}
										//alert(new_coupon_code);
										
									}
									
									$(this).dialog("close");
									return false;
								}
							 }
						};	
						$('#dlgVatTotal').dialog('destroy');
						$('#dlgVatTotal').dialog(dialogOpts_vt);			
						$('#dlgVatTotal').dialog('open');
				return false;
			});
		 
		 $('#btnGreenBag').click(function(e){	    	
		    	e.preventDefault();
		    	// *WR12062014
		    	////////////////////////////////////////////// start ////////////////////////////////////////
		    	//*WR01092014 for support green bag and coupon welove
		    	var status_no=$('#csh_status_no').val();
		    	var net_amt=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
		 		net_amt=parseFloat(net_amt);
		 		var promo_tp=$('#csh_promo_tp').val();
		 		if(status_no=='02'){
					jAlert('????????? 02 (?????????????????????????????????????????????????????????????????????) ????????????????????????????????????????????????', 'WARNING!',function(){	            
						$('#csh_product_id').focus();
		            	return false;
			        });
					return false;
				}else if(status_no=='04' && net_amt<1){
					jAlert('????????? 04 (??????????????????????????????????????????) ??????????????????????????? Barcode ?????????????????????????????????\n?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){	            
						$('#csh_product_id').focus();
		            	return false;
			        });
					return false;
				}else if(promo_tp=='NEWBIRTH' && net_amt<1){
					jAlert('??????????????????????????????????????????????????????????????? (NEWBIRTH) ??????????????????????????? Barcode ?????????????????????????????????\n?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){	            
						$('#csh_product_id').focus();
		            	return false;
			        });
					return false;
				}	
//		    	var onsales=onSales();
//			    if(onsales>0){
//				 	jAlert('?????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){	            	
//		            	return false;
//			        });
//				 	return false;
//				}

			    $("#dlgGreenBag").dialog({
				           autoOpen:true,
						   height:'auto',	
						   modal:true,
						   resizable:false,
				           title: "??????????????????????????? Barcode ?????????????????????????????????",		
				           position:"center",
				            open:function(){		
				            	$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
			    			    $(this).dialog("widget").find(".ui-button-text")
			                    .css({"padding":".1em .2em .1em .2em","font-size":"20px"});
			    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px 15px","margin-top":"0",
			    					"background-color":"#c5bcdc","font-size":"20px","color":"#666"});
			    			    //$('#dlgGreenBag').empty().html('<p><input type="text" id="txt_greenbag1" size="15" class="keybarcode"/><br/></p>');	
			    			    $('#txt_greenbag1').val('').focus();
				            },
				            buttons: {
							 	"????????????":function(evt){
							 		evt.preventDefault();
							 		evt.stopPropagation();
							 		var g1=$('#txt_greenbag1').val();
							 		if(g1!=''){
							 			var str_greenbag=g1;
								 		var member_no=$('#csh_member_no').val();
							 			$.ajax({
							 				type:'post',
							 				url:'/sales/accessory/usedbagtotemp',
							 				data:{
							 					str_greenbag:str_greenbag,
							 					member_no:member_no,
							 					rnd:Math.random()
							 				},success:function(data){
							 					if(data=='O'){
							 					 	jAlert('?????????????????????????????????????????????????????????????????????????????? 5 ?????????????????????????????????', 'WARNING!',function(){	       
							 					 		initFieldPromt();
										            	return false;
											        });
							 					 	return false;
							 					}else if(data=='N'){
							 					 	jAlert('???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){	       
							 					 		initFieldPromt();
										            	return false;
											        });
							 					 	return false;
							 					}else if(data=='R'){
							 						jAlert('????????????????????????????????????????????????????????? ????????????????????????????????????????????????????????? 1 ????????????????????????????????????', 'WARNING!',function(){	       
							 					 		initFieldPromt();
										            	return false;
											        });
							 					 	return false;
							 					}else{
							 						//*WR18082014
							 						if(data=='trn_promotion_tmp1'){
							 							getPmtTemp('N');
							 						}else if(data=='trn_tdiary2_sl'){
							 							getCshTemp('N');
							 						}else{
							 							getPmtTemp('N');
							 						}
							 						
							 					}
							 					
							 				}
							 			});
							 			
							 		}
							 		$('#dlgGreenBag').dialog('close');
							 		initFieldPromt();
							 	}
							 		
			    		    },
				            close:function(evt){
								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
									evt.stopPropagation();
									evt.preventDefault();
								}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
									evt.stopPropagation();
									evt.preventDefault();		    		    						
								}	
				            	//$(this).remove();
								$("#dlgGreenBag").dialog("destroy");
				            }
			        });	
					/////////////////////////////////////////////  end ////////////////////////////////////////
		    	 
		    });//end
	    
	    $('#btnSmsPro').click(function(e){
	    	e.preventDefault();
	    	var opts_dlgSmsPro={
	    			autoOpen:true,
					width:'60%',		
					height:'300',	
					modal:true,
					resizable:true,
					position:"center",
					showOpt: {direction: 'up'},		
					closeOnEscape:true,	
					title:"<span class='ui-icon ui-icon-document-b'></span>SMS Promotion",
					open: function(){   
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px 5px","margin-top":"0",
	    			    					"background-color":"#e7e7e7","font-size":"27px","color":"#0000FF"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
	    			    //button style		
	    			    $(this).dialog("widget").find("button")
		                 .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
	    			    $('#dlgSmsPro').html('');
	    			    var opts_smspromo={
	    			    		type:'post',
	    			    		url:'/sales/cashier/smspro',
	    			    		data:{
	    			    			rnd:Math.random()
	    			    		},success:function(data){
	    			    			$('#dlgSmsPro').html(data);
	    			    			////////////// start //////////////////
	    			    			$('.tableNavSmsPro ul>li').not('.nokeyboard').navigate({
								        wrap:true
								    }).click(function(evt){		
									    evt.stopPropagation();
									    evt.preventDefault();
									    var csh_member_no=$('#csh_member_no').val();									   
									    var sel_promo=$.parseJSON($(this).attr('idpromo'));											    
									    if(sel_promo.member_tp=='Y' && $.trim(csh_member_no).length==0){
									    	jAlert('?????????????????????????????????????????????????????????????????????','WARNING!',function(){
												 return false;
											  });
									    	return false;
									    }
									    
									    //check in transaction									    
									    var sms_promo_code=sel_promo.promo_code;
									    $('.tableNavSmsPro ul li').navigate('destroy');
									    $('#dlgSmsPro').dialog('close');
									    //init var									   
										$('#csh_play_main_promotion').val('N');
										$('#csh_play_last_promotion').val('N');
										$('#csh_get_point').val('N');
										$('#csh_trn_diary2_sl').val('Y');
										$('#csh_start_baht').val('');
										$('#csh_end_baht').val('');	
										$('#other_promotion_title').html(sel_promo.promo_des);
										//*WR25012016
										$('#csh_promo_tp').val(sel_promo.promo_tp);
										//____________WR23012013_____________________
										if(parseFloat(sel_promo.promo_amt)>0){
											$('#csh_promo_code').val(sel_promo.promo_code);
											$('#csh_promo_pos').val(sel_promo.promo_pos);//new
											$('#csh_promo_amt').val(sel_promo.promo_amt);
											$('#csh_promo_amt_type').val(sel_promo.promo_amt_type);										
											$('#csh_type_discount').val(sel_promo.type_discount);//new
											$('#csh_promo_discount').val(sel_promo.discount);//new
											$('#csh_get_point').val(sel_promo.get_point);
											$('#csh_discount_member').val(sel_promo.discount_member);
											if(sel_promo.discount_member!='Y'){
												$('#csh_percent_discount').html('0');												
											}
										}
										//____________WR23012013____________________
										
										//*WR20012016 Member Call Survey MCS									
										var prefix_member_no_idcard=$('#csh_member_no').val();
											 prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
										if(sel_promo.promo_pos=='MCS'){
											//var mp_discount=parseInt(sel_promo.discount);
											//$('#csh_percent_discount').html(mp_discount);
											var member_no=$('#csh_member_no').val();
											var promo_code=sel_promo.promo_code;
											var id_card=$('#csh_id_card').val();
											var mobile_no=$('#csh_mobile_no').val();
											if(prefix_member_no_idcard!='ID'){
												//callBackToDo2(sel_promo.promo_code,'Y','','3100504005654','45150');
												var datastart=datastart = member_no+'#'+promo_code+'#'+id_card+'#'+mobile_no;
												//alert(datastart);
												apiread(datastart,'showFormSmsMobile');	
											}else{
												showFormSmsMobile(sms_promo_code,sel_promo.promo_tp,sel_promo.member_tp);
											}																					
											$('#dlgProCoupon').dialog('close');
											return false;
										}else{
											showFormSmsMobile(sms_promo_code,sel_promo.promo_tp,sel_promo.member_tp);
										}
										//*WR20012016 Member Call Survey MCS
										
										//init var										
										//showFormSmsMobile(sms_promo_code,sel_promo.promo_tp,sel_promo.member_tp);	
									
									    
								    });
	    			    			///////////// end ////////////////////
	    			    		}	    			    		
	    			    };
	    			    $.ajax(opts_smspromo);
					},close:function(evt,ui){
						$('.tableNavSmsPro ul').navigate('destroy');
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
    						evt.preventDefault();    						
    						$('#dlgSmsPro').html('');
    						$('#dlgSmsPro').dialog('destroy');
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
    						evt.preventDefault();
    						$('#dlgSmsPro').html('');
    						$('#dlgSmsPro').dialog('destroy');
						}	
						$("#dlgSmsMobile").dialog('destroy');
					}
					
	    	};
	    	
	    	var opts_ctemp={
					type:'post',
					url:'/sales/cashier/countdiarytemp',
					cache:false,
					async:false,
					data:{
						actions:'cashier',
						rnd:Math.random()
					},
					success:function(data){
						var arr_data=data.split('#');
						if(parseInt(arr_data[0])>0){		
							jAlert('CAN NOT PLAY PROMOTION BECAUSE IT FOUND ITEM.','WARNING!',function(){
								 return false;
							  });
						}else{
							///////////////
							$('#dlgSmsPro').dialog('destroy');
							$('#dlgSmsPro').dialog(opts_dlgSmsPro);			
							$('#dlgSmsPro').dialog('open');
							//////////////
					    	
						}
					}
				};
			$.ajax(opts_ctemp);
	    	
	    });
	    
		$('#btnCancle').click(function(e){
			e.preventDefault();
			jConfirm('DO YOU WANT TO CANCEL THE SALE?', 'CONFIRM MESSAGE', function(r) {
			    if(r){	
		    		chkMarkMemberUsePriv();
			    	cancelBill();
				}else{
					$('#btnCancle').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
				}
			});
			return false;
		});

		//button delele Grid Row
		$('#btnDelRowSelect').button({
	        icons: { primary: "btnDelRowSelect" }
	    });
	    
	    $('#btnDelRowSelect').live('click', {}, function(ev) {
		    if($("#csh_status_no").val()=='01') return false;
	    	var grid = $('#tbl_cashier').flexigrid();
			var del_list='',del_list_show='';
			//var arr_out_str=new Array();
	        grid.find('tr.trSelected').each(function() {
		        product_id=$("td div", $(this)).eq(3).text();
	        	product_name=$("td div", $(this)).eq(4).text();
	        	//arr_out_str.push(product_id+":"+product_name);
	        	id=$(this).attr('id').substr(3);
	        	del_list_show+=id+":"+product_id+":"+product_name+"\r\n";
	        	del_list+=id+"#";
	            //arr_out_str.push($(this).text());
	            /*
	        	var arr_data = $(this);
	        	for(i=0;i<arr_data.length;i++){
					del_list+= arr_data[i].id.substr(3)+"#";
				}   
				*/
	        }); 
	        //alert(del_list);	        
	       del_list=del_list.substring(0,del_list.length-1);
	       if(del_list==''){
	        	 jAlert('Please select the item you want to delete.','WARNING!',function(){
	        		 $('#btnDelRowSelect').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
					 return false;
				  });
		    }else{
		       setTimeout(function(){
		    	   deleteFromGrid(del_list,del_list_show);		    	  
			    },400);
			   
		    }
	        return false;
	    });
		//button delele Grid Row
	
	    $("#csh_member_no").focus();
	    //
		$("#csh_member_no").keypress( function(evt){
			if($(this).is('[readonly]')){ 
				initFieldPromt();
				return false;
			}
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13) {	        	
	            evt.preventDefault();
	            //evt.stopImmediatePropagation();	                  		
	            var csh_status_no=$("#csh_status_no").val();
	            var member_no=$.trim($('#csh_member_no').val());
	            var on_trans='N';
	            if(member_no.length==13){
	            	
	            	if(csh_status_no=='01'){
     		            //bill ????????????????????????????????????????????? 
     		            checkMemberExist();
     		        }else if(csh_status_no=='02'){
     			        //bill ?????????????????????????????????????????????????????????????????????????????????
     		        	firstBuy();		            	     		        	
     			    }else if(csh_status_no=='03'){
     			        //bill coupon ?????????????????????
     			   		chkEmpPrivilage(); 
     			   		return false;
     			    }else if(csh_status_no=='04'){
     			        //??????????????????????????????????????????
     			    	//redeemPoint();		            	     			    	
     			    	if(getOnlineStatus()===1){	
     			    		redeemPoint();
     			    	}else{
     			    		//callMemberOffline();
     			    		jAlert("???????????????????????????????????????????????????????????? OFFLINE ?????????","WARNING!",function(){
 				    			$("#csh_member_no").focus();
 				            	return false;
 					        });
 				    		return false;
     			    	}
     			    }else if(csh_status_no=='05'){
     			        //?????????????????????????????????????????????
     			        cardLost();
     			    }else if(csh_status_no=='06'){
     			        //??????????????????????????????????????? ??????????????????
     			    	if($("#cn_member_no").val()!=''){
     			    		if($("#csh_member_no").val()==''){
     				    		jAlert("??????????????????????????? ??????????????????????????????","WARNING!",function(){
     				    			$("#csh_member_no").focus();
     				            	return false;
     					        });
     				    		return false;
     				    	}else if($("#csh_member_no").val()!=$("#cn_member_no").val()){
     				    		jAlert("?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????(CN)","WARNING!",function(){
     				    			$("#csh_member_no").focus();
     				            	return false;
     					        });
     				    		return false;
     				    	}else{
     				    		if(getOnlineStatus()===1){
     				    			callMemberInfo('N');
    	     		        	}else{
    	     		        		callMemberOffline();
    	     		        	}
     				    		
     				    	}
     			    	}else{
     			    		initFieldPromt();
     			    	}
     			    }else{
     			    	callMemberInfo('N');
     			    }
	            	
	            }else if(member_no.length>=6){
	            	var opts_ontrans={
	            			type:'post',
	            			url:'/sales/cashier/memberontrans',
	            			async:false,
	            			cache:false,	            		   
	            			data:{
	            				member_no:member_no,
	            				rmd:Math.random()
	            			},success:function(data){	         
	            				
	            					mdata = data.split("#");	            				
	            					//---------------------------------------------------------------------
	            					if(mdata[0]=='Y'){
	            	            		jAlert("Members can not do more than one.","WARNING!",function(){
	            			    			$("#csh_member_no").focus();
	            			            	return false;
	            				        });
	            	            	}else if(mdata[0]=='T'){
	                                    jAlert("Code ?????????????????????????????????????????? Code ????????????????????????","WARNING!",function(){	                                       
	                                        $("#csh_member_no").val('');
	                                        $("#csh_member_no").focus();
	                                        return false;
	                                    });
	                                }else{
	                                	$('#csh_member_no').val(mdata[1]);
	                                	
	            	            		//satart old
	            	            		 if(csh_status_no=='01'){
		            	     		            //bill ????????????????????????????????????????????? 
		            	     		            checkMemberExist();
		            	     		        }else if(csh_status_no=='02'){
		            	     			        //bill ?????????????????????????????????????????????????????????????????????????????????
		            	     		        	firstBuy();		            	     		        	
		            	     			    }else if(csh_status_no=='03'){
		            	     			        //bill coupon ?????????????????????
		            	     			   		chkEmpPrivilage(); 
		            	     			   		return false;
		            	     			    }else if(csh_status_no=='04'){
		            	     			        //??????????????????????????????????????????
		            	     			    	//redeemPoint();		            	     			    	
		            	     			    	if(getOnlineStatus()===1){	
		            	     			    		redeemPoint();
		            	     			    	}else{
		            	     			    		//callMemberOffline();
		            	     			    		jAlert("???????????????????????????????????????????????????????????? OFFLINE ?????????","WARNING!",function(){
	            	     				    			$("#csh_member_no").focus();
	            	     				            	return false;
	            	     					        });
	            	     				    		return false;
		            	     			    	}
		            	     			    }else if(csh_status_no=='05'){
		            	     			        //?????????????????????????????????????????????
		            	     			        cardLost();
		            	     			    }else if(csh_status_no=='06'){
		            	     			        //??????????????????????????????????????? ??????????????????
		            	     			    	if($("#cn_member_no").val()!=''){
		            	     			    		if($("#csh_member_no").val()==''){
		            	     				    		jAlert("??????????????????????????? ??????????????????????????????","WARNING!",function(){
		            	     				    			$("#csh_member_no").focus();
		            	     				            	return false;
		            	     					        });
		            	     				    		return false;
		            	     				    	}else if($("#csh_member_no").val()!=$("#cn_member_no").val()){
		            	     				    		jAlert("?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????(CN)","WARNING!",function(){
		            	     				    			$("#csh_member_no").focus();
		            	     				            	return false;
		            	     					        });
		            	     				    		return false;
		            	     				    	}else{
		            	     				    		if(getOnlineStatus()===1){
		            	     				    			callMemberInfo('N');
				            	     		        	}else{
				            	     		        		callMemberOffline();
				            	     		        	}
		            	     				    		
		            	     				    	}
		            	     			    	}else{
		            	     			    		initFieldPromt();
		            	     			    	}
		            	     			    	
		            	     			    }else if(csh_status_no=='19'){
		            	     			        //???????????????????????????(???????????????????????????)
		            	     			        stockLost();
		            	     			    }else{
		            	     				    //bill short 00
		            	     			    	if(getOnlineStatus()===1){
            	     				    			callMemberInfo('N');
		            	     		        	}else{
		            	     		        		callMemberOffline();
		            	     		        	}
		            	     		        }
	            	            		//end old
	            	            	}
	            					//---------------------------------------------------------------------
	            			}
	            	};
	            	$.ajax(opts_ontrans);
	            }else{
	            	//member walk in
	            	if(csh_status_no=='02'){
	            		var key_employee=$.trim($("#csh_member_no").val());
	     			   	if(key_employee.length==0){
					    		jAlert("??????????????????????????? ??????????????????????????????","WARNING!",function(){
					    			$("#csh_member_no").focus();
					            	return false;
						        });
					    		return false;
	     			   	}
 			   		}else if(csh_status_no=='03'){
			            	var key_employee=$.trim($("#csh_member_no").val());
		     			   	if(key_employee.length==0){
						    		jAlert("??????????????????????????? ?????????????????????????????????","WARNING!",function(){
						    			$("#csh_member_no").focus();
						            	return false;
							        });
						    		return false;
		     			   	}
     			   	}else if(csh_status_no=='04'){
			            	var key_employee=$.trim($("#csh_member_no").val());
		     			   	if(key_employee.length==0){
						    		jAlert("??????????????????????????? ??????????????????????????????","WARNING!",function(){
						    			$("#csh_member_no").focus();
						            	return false;
							        });
						    		return false;
		     			   	}
	 			 	}else if(csh_status_no=='05'){
		            	
					    		jAlert("??????????????????????????? ????????????????????????????????????/?????????????????????","WARNING!",function(){
					    			$("#csh_member_no").focus();
					            	return false;
						        });
					    	
 			 	}else if(csh_status_no=='06'){
	 			 		 //??????????????????????????????????????? ?????????????????? ???????????????????????????????????? event bubling on key enter 			 			
     			    	if($("#cn_member_no").val()!=''){
     			    		if($("#csh_member_no").val()==''){
     				    		jAlert("??????????????????????????? ??????????????????????????????","WARNING!",function(){
     				    			$("#csh_member_no").focus();
     				            	return false;
     					        });
     				    		return false;
     				    	}
     			    	}else{
     			    		chkCnMember();
     			    		initFieldPromt();
     			    	}     			    	
	 			   	}else if(csh_status_no=='19'){
     			        //???????????????????????????(???????????????????????????)
     			        stockLost();
     			    }else{
		 			   		if(getOnlineStatus()===1){
				    			callMemberInfo('N');
				        	}else{
				        		if(member_no.length==0){
				        			initFieldPromt();
				        		}else{
				        			callMemberOffline();
				        		}
				        	}
		     			}
				   }
	            return false;
	        }
	    });
		
		$("#csh_quantity").click( function(evt) {
	       evt.preventDefault();
	       $(this).select();
	    });
		$("#csh_quantity").keypress( function(evt) {
	        var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13) {
	            evt.preventDefault();	            
	            var csh_quantity=jQuery.trim($("#csh_quantity").val());
	            if(csh_quantity=='' || parseInt(csh_quantity)==0){
	            	jAlert("??????????????????????????? ?????????????????????????????????","WARNING!",function(){
		            	initFieldPromt();
		            	return false;
			        });
			        return false;
		         }else{
	            	$('#csh_product_id').focus();
		         }
	        }
	    });

		$("#btnBrowsProduct").click(function(e){
			e.preventDefault();
			var dialogOpts_brows_product = {
					autoOpen: false,
					width:350,		
					height:200,	
					modal:true,
					resizable:true,
					position:['center','center'],
					title:"<span class='ui-icon ui-icon-cart'></span>????????????????????????????????????",
					overlay:{opacity:0.5,background:"black"},
					open: function(){     	
							$('.ui-widget-overlay').hide().fadeIn();
							$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
						
		    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"height":"50px","padding-top":"10","margin-top":"0","background-color":"#c5bcdc",
			    			    										"font-size":"27px","color":"#000"});		    			    
		    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
		    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
		    			    // button style		
		    			    $(this).dialog("widget").find("button")
			                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
							
							$("#dlgBrowsProduct").html("<p><img src='/sales/img/activity_indicators_02.gif'></p>");
							$("#dlgBrowsProduct").load("/sales/cashier/formbrows?actions=brows_product&now="+Math.random(),
							function(){	
								//start auto complete
								$("#auto_product_id").focus();
								var cache = {};
								$("#auto_product_id").autocomplete({
									source: function(request, response) {
									  var term          = request.term.toLowerCase(),
									      element       = this.element,
									      cache         = this.element.data('autocompleteCache') || {},
									      foundInCache  = false;

									  $.each(cache, function(key, data){
									    if (term.indexOf(key) === 0 && data.length > 0) {
									      response(data);
									      foundInCache = false;
									      return;
									    }
									  });

										if (foundInCache) return;
										
										$.ajax({
											url: '/sales/cashier/autoproduct',
											dataType: "json",
											data: request,
											success: function(data) {
												cache[term] = data;
												element.data('autocompleteCache', cache);
												response(data);
											}
										});
									},
									select: function(event,ui){
												$("#csh_product_id").val(ui.item.value);
												$("#dlgBrowsProduct").dialog("close");
												cmdEnterKey("csh_product_id");
									},					
									minLength:3,
									change: function() {
										//prevent 'to' field being updated and correct position
										$("#auto_product_id").val("").css("top", 2);
									}

									
								});
								
								//end auto complete														
							});
					},				
					close: function(){
					 },
								
					buttons: {
							"Close":function(){ 				
										$(this).dialog("close");
										return false;
							}
						 }
					};			
				
					$('#dlgBrowsProduct').dialog('destroy');
					$('#dlgBrowsProduct').dialog(dialogOpts_brows_product);			
					$('#dlgBrowsProduct').dialog('open');
					return false;
		});
		//end click brows product
	    
	
		$('#csh_product_id').keypress(function(evt){			
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        var product_id=$.trim($("#csh_product_id").val());
	        if(key == 13) {
		        evt.stopPropagation();
		        evt.preventDefault();		
		        if($('#csh_quantity').val()=='' || $('#csh_quantity').val()=='0'){
		        	 jAlert('PLEASE ENTER A VALID PRODUCT QUANTITY.', 'WARNING!',function(){
			            	$("#csh_quantity").focus();
			            	return false;
				     });
			        return false;
			    }
		        if($('#csh_status_no').val()=='02' && $("#csh_member_no").val()==''){
		           initFieldOpenBill();			           
				   return false;
			    }else if($.trim($("#csh_member_no").val())==''){
	    			$('#csh_member_fullname').html("&nbsp;");
	    			$('#csh_member_type').html("WALK IN");
	    		}
	    		if(product_id==''){
	            	 jAlert('PLEASE SPECIFY PRODUCT CODE.', 'WARNING!',function(){
		            	$("#csh_product_id").val('');
		            	$("#csh_product_id").focus();
		            	return false;
			        });
				        return false;
		         }			    
		         getProduct();
		         $( "#csh_accordion").accordion({ active:1});
		        return false;
	        }
	    });
	    //other promotion from table promo_head
		$("#btnOther").click(function(){
			 var dialogOpts_Other = {
						autoOpen: false,
						width:'60%',		
						height:350,	
						modal:true,
						resizable:true,
						position:"center",
						/*show: "blind",*/
					    showOpt: {direction: 'up'},		
						closeOnEscape:true,	
						title:"<span class='ui-icon ui-icon-cart'></span>OTHER",
						open: function(){   
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
							$(this).dialog('widget')
					            .find('.ui-dialog-titlebar')
					            .removeClass('ui-corner-all')
					            .addClass('ui-corner-top');
						    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
							    																			"font-size":"26px","color":"#0000FF",
							    																			"padding":"0 0 0 0"});	
							$(this).find('div,button,select, input, textarea').blur();
							$('.ui-dialog-buttonpane button:last').blur();						
							$("#dlgOtherPromotion").html("");
							//??????????????? promotion ?????????????????????????????????????????? promotion ???????????? web_promo=Y
							$.ajax({
								type:'post',
								url:'/sales/cashier/otherpro',
								cache:false,
								data:{								
									net_amt:$('#csh_net').val(),
									now:Math.random()
								},
								success:function(data){
									$("#dlgOtherPromotion").html(data);
									$(this).parent().find('select, input, textarea').blur();
									$('.tableNavOtherPro ul li').not('.nokeyboard').navigate({
								        wrap:true
								    }).click(function(evt){		
									    evt.stopPropagation();
									    evt.preventDefault();										    
									    var sel_promo=$.parseJSON($(this).attr('idpromo'));	
									    
									    //*WR13072016 for support last promotion									   
									    $("#csh_play_last_promotion").val(sel_promo.play_last_pro);
									    
									  //*WR02062016 for support mini bueaty book 2016
									    if(sel_promo.promo_code=='OM13160416' || sel_promo.promo_code=='OM13061216'){
									    	$("#dlgOtherPromotion").dialog("close");
									    	//var otp_status=sel_promo.key_input;
									    	var otp_status='Y';
									    	var member_no=$('#csh_member_no').val();
									    	var id_card=$('#csh_id_card').val();
									    	var mobile_no=$('#csh_mobile_no').val();
									    	var prefix_member_no_idcard=$('#csh_member_no').val();
												 prefix_member_no_idcard=prefix_member_no_idcard.substring(0,2);
										    var chk_mem_idcard='';
												 if(prefix_member_no_idcard=='ID'){
													 chk_mem_idcard="idcard";
											}
									    	fromreadprofile(sel_promo.promo_code,otp_status,member_no,id_card,mobile_no,chk_mem_idcard,sel_promo.promo_des);
									    	//callBackToDo2(sel_promo.promo_code,'Y','','3100504005654','45150');
									    	return false;									    	
									    }
									    
									    
									    
									    /// *WR20120906  case promotion 50BTO1P ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
									    if(sel_promo.promo_code=='50BTO1P'){
									    	var csh_net_to_check=parseInt($('#csh_net').val());
									    	if(csh_net_to_check<1){
									    		jAlert("Not found Sell ??????Items. Please check and try again.","WARNING!",function(){	
													 initFieldPromt();												
									            	return false;
										        });
									    		return false;
									    	}
									    	
									    	//*WR14032014 bill 02 problem
									    	var mp_status_no=$('#csh_status_no').val();
									    	if(mp_status_no=='02'){
									    		jAlert("??????????????????????????? More Point ????????????????????????????????????????????????????????????????????? 02(First Purchase) ?????????","WARNING!",function(){	
									            	return false;
										        });
									    		return false;
									    	}
									    	
									    }
									    /// *WR20120906
									    
									    var csh_member_no=$('#csh_member_no');	
									    var csh_net=$('#csh_net').val();
										if(sel_promo.member_tp=='Y' && $.trim(csh_member_no.val())==''){
											jAlert("Please specify member id.","WARNING!",function(){	            	
												 $("#dlgOtherPromotion").dialog("close");
												 //initFieldPromt();						
												 initFieldOpenBill();
								            	return false;
									        });
									        return false;
										}//if										
										
										/////////////////////////////// Temp  play only one ////////////////////////////////////////
									    /*WR10012013 ???????????????????????????????????????????????????????????????????????????????????????????????? short product*/	
									    if(sel_promo.promo_code.substring(0,2)=='R_'){
									    	$('#csh_promo_code').val(sel_promo.promo_code);			
									    	$('#csh_trn_diary2_sl').val('Y');									    	
									    	var title_returns="";
									    	if(sel_promo.promo_code.substring(0,6)=='R_STOR'){
									    		title_returns="???????????????????????????";
									    	}else{
									    		title_returns="???????????????????????????(???????????????????????????????????????????????????)";
									    	}
									    	
									    	$("#dlgOtherPromotion").dialog("close");
									    	var dlgOpts_DocTmp = {
													autoOpen: false,
													width:'auto',		
													height:150,	
													modal:true,
													resizable:true,
													position:"center",													
													closeOnEscape:true,	
													title:"<span class='ui-icon ui-icon-cart'></span>" + title_returns,
													open: function(){   
														$('#dlgDocNoTemp').html('');
														if(sel_promo.promo_code.substring(0,6)=='R_STOR'){
																$('#dlgDocNoTemp').append("<input type='text' id='doc_no_temp' size='15'></input>" +
																		"<button id='btnSubmitTemp' style='font-size:14px;'>????????????</button>");
														}else{
															$('#dlgDocNoTemp').append("<input type='text' id='doc_no_temp' size='15' readonly style='border:1pt solid#999;background:#ccc;'></input>" +
																	"&nbsp;<button id='btnBwsMemBill' style='font-size:14px;'>???????????????</button>" +
																	"<div style='text-align:left;'><button id='btnSubmitTemp' style='font-size:14px;'>????????????</button></div>"
																	);
														}
														$('#doc_no_temp').val('').focus();
														//test new
														//WR 28062013 check popup win to key product
														var sel_product='N';
														var limited_qty='1';
																												
														$('#btnBwsMemBill').click(function(e){
															e.preventDefault();
															var promo_code=sel_promo.promo_code;
															var opts_dlgbillProfiles={
																	autoOpen:false,
																	modal:true,
																	width:'75%',
																	height:'auto',
																	resizable:true,
																	position:'center',
																	closeOnEscape: true,
																	title:"<span class='ui-icon ui-icon-document-b'></span>?????????????????????????????????????????????",
																	open:function(){
																		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
																		$(this).dialog('widget')
																            .find('.ui-dialog-titlebar')
																            .removeClass('ui-corner-all')
																            .addClass('ui-corner-top');
													    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px","margin-top":"0",
													    			    					"background-color":"#c5bcdc","font-size":"22px","color":"#000"});
													    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
													    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c8c7dc"});
													    			    //button style	
													    			    $(this).dialog("widget").find(".ui-btndlgpos")
													                    .css({"padding":"2","background-color":"#c8c7dc","font-size":"16px"});
													    			    
																	    $(".ui-widget-overlay").live('click', function(){
																	    	$("#sch_member_no").focus();
																		});																	    
																	    $.ajax({
																	    	type:'post',
																	    	//url:'/sales/cashier/billshortproduct',
																	    	url:'/sales/returns/billshortproduct',
																	    	data:{
																	    		member_no:$('#csh_member_no').val(),
																	    		promo_code:promo_code,
																	    		rnd:Math.random()
																	    	},
																	    	success:function(data){
																	    		$('#dlgbillProfiles').html(data);
																	    		if($('p#item_not_found').length != 0){
																					return false;
																				}
																	    		//start data table
																	    		var oTable = $('#datatables_billprofiles').dataTable();
																				$('#datatables_billprofiles').dataTable({
																					"bJQueryUI":false,
																					"bDestroy": true,
																	       			"fnDrawCallback": function(){
																	       												       	
																		       		      $('table#datatables_billprofiles td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
																		       		      $('table#datatables_billprofiles td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
																		       		      
																			       		   $('#datatables_billprofiles tr').each(function(){
																				       			$(this).click(function(){
																				       				var strJson=$(this).attr('idd');
																				       				if(strJson!=''){
																						       				var seldocno=$.parseJSON(strJson);
																						       				//alert(seldocno.sel_product + "," + seldocno.limited_qty);
																						       				
																						       				//WR 28062013 check popup win to key product
																						       				sel_product=seldocno.sel_product;
																						       				limited_qty=seldocno.limited_qty;
																						       				$('#csh_refer_doc_no').val(seldocno.doc_no);
																						       				
																						       				if(seldocno.status_receive=='Y'){
																						       					jAlert("???????????????????????????????????????","WARNING!",function(){																					
																													return false;
																										        });
																						       					return false;
																						       				}
																						       				if(seldocno.flag=='C'){
																						       					jAlert("??????????????????????????????????????????????????????","WARNING!",function(){																					
																													return false;
																										        });
																							       				return false;
																						       				}
																						       				
																						       				$('#doc_no_temp').val(seldocno.doc_no);
																										    $('#dlgbillProfiles').dialog('close');
																										    
																						       			}
																				       			});
																				       		});
																		       		      
																	       			}//end callback
																				});	
																				 oTable.fnSort([[0,'desc'],[1,'desc']]);
																	    		//start data table
																	    		
																	    	}
																	    });
																		
																	},
																	 buttons: [ 
															    	            { 
															    	                text: "Close",
															    	                class: 'ui-btndlgpos', 
															    	                click: function(evt){ 
															    	                	  evt.preventDefault();	
															    	                	  $('#dlgbillProfiles').dialog('close');
															    	                } 
															    	            }
															    	        ],
																	close:function(){
																		endOfProOther();//05092013
																	}
															};
															$('#dlgbillProfiles').dialog('destroy');
															$('#dlgbillProfiles').dialog(opts_dlgbillProfiles);
															$('#dlgbillProfiles').dialog('open');
															return false;
														});
														//test new
														
														$('#btnSubmitTemp').click(function(e){
															e.stopPropagation();
															e.preventDefault();															
															var onsales=onSales();
														    if(onsales>0){
														    	$('#doc_no_temp').val('');
																$('#dlgDocNoTemp').dialog('close');
															 	jAlert('????????????????????????????????????????????????????????????????????????', 'WARNING!',function(){															 		
																	getCshTemp('N');
																	paymentBill('00');
													            	return false;
														        });
															 	return false;
															}
															
															var doc_no_temp=$('#doc_no_temp').val();
															if($.trim(doc_no_temp).length==0) return false;		
															var promo_code=sel_promo.promo_code;
															$('#csh_promo_code').val(promo_code);//*WR26012015
															
															
													//alert(sel_product + "," + limited_qty);
													if(sel_product=='Y'){
														
														$('<div id="dlgReturnProducts" >' + 
																'<span style="color:#444;">???????????????</span>&nbsp;' +
																'<input type="text" size="1" value="1" style="width:30px;" id="key_quantity_return"/>&nbsp;' +
																'<span style="color:#444;">??????????????????????????????</span>&nbsp;' +
																'<input type="text" size="15" style="width:175px"  id="key_product_return"/></div>').dialog(
														   {
															   	  title:"????????????????????????????????????",
															      autoOpen:true,
															      width:365,
															      height:120,
															      modal:true,
															      open: function(){
															    	  $("#key_product_return").val('').focus();
															    	  $("#key_quantity_return").keypress(function(evt){	
															    		  var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
															    		  if(key==13){
															    			  evt.stopPropagation();
																    		  evt.preventDefault();
																    		  var key_product_return=$("#key_product_return").val();
															    			  var key_quantity_return=$("#key_quantity_return").val();
															    			  key_product_return=$.trim(key_product_return);
															    			  key_quantity_return=$.trim(key_quantity_return);
																    		  if(key_quantity_return.length==0 || key_quantity_return=='0'){
															    				  jAlert("????????????????????????????????????????????????????????????","WARNING!",function(){				
															    					   $("#key_quantity_return").val('1').focus();
																						return false;
																			        });
															    				  return false;
															    			  }
															    			  
															    			  if(key_quantity_return>limited_qty){
															    				  jAlert("?????????????????????????????????????????????????????????????????????????????????????????????????????? " + limited_qty + " ??????????????????","WARNING!",function(){				
															    					   $("#key_quantity_return").val('1').focus();
																						return false;
																			        });
															    				  return false;
															    			  }
																    		  $("#key_product_return").val('').focus();
																    		  return false;
															    		  }
															    	  });
															    	  $("#key_product_return").keypress(function(evt){															    		 
															    		  var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
															    		  if(key==13){
															    			  evt.stopPropagation();
																    		  evt.preventDefault();
															    			  var key_product_return=$("#key_product_return").val();
															    			  var key_quantity_return=$("#key_quantity_return").val();
															    			  key_product_return=$.trim(key_product_return);
															    			  key_quantity_return=$.trim(key_quantity_return);
															    			  if(key_product_return.length==0){
															    				  jAlert("?????????????????????????????????????????????????????????","WARNING!",function(){				
															    					   $("#key_product_return").val('').focus();
																						return false;
																			        });
															    			  }
															    			  
															    			  if(key_quantity_return.length==0){
															    				  jAlert("????????????????????????????????????????????????????????????","WARNING!",function(){				
															    					   $("#key_quantity_return").val('1').focus();
																						return false;
																			        });
															    			  }
															    			  
															    			  if(key_quantity_return>limited_qty){
															    				  jAlert("?????????????????????????????????????????????????????????????????????????????????????????????????????? " + limited_qty + " ??????????????????","WARNING!",function(){				
															    					   $("#key_quantity_return").val('1').focus();
																						return false;
																			        });
															    				  return false;
															    			  }
															    			  
															    			  $.ajax({
															    				  //url:'/sales/cashier/chkfreeshort',
															    				  url:'/sales/returns/chkfreeshort',
															    				  type:'post',
																				  cache:false,
															    				  data:{
															    					 	doc_no:doc_no_temp,
																						member_no:$('#csh_member_no').val(),
																						promo_code:promo_code,
																						member_tp:sel_promo.member_tp,
																						product_id:key_product_return,
																						quantity:key_quantity_return,
																						rnd:Math.random()
															    				  },success:function(data){
															    					  if(data!=''){
															    						  jAlert(data,"WARNING!",function(){				
																	    					   $("#key_product_return").val('').focus();
																								return false;
																					        });
															    					  }else{
															    						    $("#dlgReturnProducts").dialog('close');
															    							getCshTemp('N');
																							paymentBill('00');
															    					  }
															    				  }
															    			  });
															    			  return false;
															    		  }
															    		  
															    	  });
															      }, 
															      close: function(){
															        $(this).remove();
															      }   
														   });
																$('#doc_no_temp').val('');
																$('#dlgDocNoTemp').dialog('close');															
																return false;
															}
															
															
															var opts_chkshort=null;
															var opts_chk={
																	type:'post',
																	//url:'/sales/cashier/chkfreeshort',
																	url:'/sales/returns/chkfreeshort',
																	cache:false,
																	data:{
																		doc_no:doc_no_temp,
																		member_no:$('#csh_member_no').val(),
																		promo_code:promo_code,
																		member_tp:sel_promo.member_tp,
																		rnd:Math.random()
																	},success:function(data){																		
																		if(data=='1'){
																			//???????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("???????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????????????????????????????????","WARNING!",function(){																					
																				return false;
																	        });
																			return false;
																		}else if(data=='2'){
																			$('#dlgDocNoTemp').dialog('close');
																			//member not math with doc
																			jAlert("??????????????????????????????????????????????????????????????????????????????????????????????????????????????????","WARNING!",function(){																						
																				return false;
																	        });
																			return false;
																		}else if(data=='3'){			
																			//???????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("??????????????????????????????????????????????????????????????? short <br>?????????????????????????????????????????????","WARNING!",function(){																					
																				return false;
																	        });
																			return false;
																		}else if(data=='4'){
																			$('#dlgDocNoTemp').dialog('close');
																			//????????????????????????????????????????????? 2000 ???????????????????????????????????????????????????????????????	
																			jAlert("????????????????????????????????????????????? 2000 ???????????????????????????????????????????????????????????????","WARNING!",function(){																					
																				return false;
																	        });
																			return false;
																		}else if(data=='5'){	
																			$('#dlgDocNoTemp').dialog('close');
																			//????????????????????????????????????????????? 3000 ??????????????????????????????????????????????????????????????????
																			jAlert("????????????????????????????????????????????? 3000 ??????????????????????????????????????????????????????????????????","WARNING!",function(){																						
																				return false;
																	        });
																			return false;
																		}else if(data=='6'){																				
																			//?????????????????????????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("????????????????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='7'){
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("????????????????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='8'){			
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("??????????????????????????? 01 ?????????????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='9'){																				
																			//?????????????????????????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("bill ???????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='10'){																				
																			//?????????????????????????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("??????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='11'){																				
																			//?????????????????????????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("????????????????????????????????????????????? promo_short","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='12'){																				
																			//?????????????????????????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("????????????????????????????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else if(data=='13'){																				
																			//?????????????????????????????????????????????????????????????????????????????????????????????????????????
																			$('#dlgDocNoTemp').dialog('close');
																			jAlert("????????????????????????????????????????????????????????????????????????????????????????????????????????? ???????????????????????????????????????????????????????????????","WARNING!",function(){																				
																				return false;
																	        });
																			return false;
																		}else{
																			opts_chkshort=null;
																			$('#doc_no_temp').val('');
																			$('#dlgDocNoTemp').dialog('close');
																			getCshTemp('N');
																			paymentBill('00');//																			
																		}
																		
																	}
															};
															opts_chkshort=$.ajax(opts_chk);
															return false;
														});
														
														$('#doc_no_temp').keypress(function(evt){
															var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
															var timeoutId = 0;
															if(key == 13){
																evt.stopPropagation();
																evt.preventDefault();															
															    $('#btnSubmitTemp').trigger('click');																													
																return false;
															}
															
														});
													},
													close:function(){														
														$('#dlgDocNoTemp').dialog('destroy');														
													}
									    	};
									    	$('#dlgDocNoTemp').dialog('destroy');
											$('#dlgDocNoTemp').dialog(dlgOpts_DocTmp);			
											$('#dlgDocNoTemp').dialog('open');	    
									    	return false;
									    }
									    
									    /////////////////////////////// Temp  play only one ////////////////////////////////////////
										$('#csh_limite_qty').val(sel_promo.limite_qty);
										$('#csh_check_repeat').val(sel_promo.check_repeat);
										$('#csh_promo_st').val(sel_promo.promo_st);//*WR20120724
										$('#csh_web_promo').val(sel_promo.web_promo);
										$('#csh_gap_promotion').val('Y');//???????????????????????????????????????????????????
										$('#csh_discount_member').val(sel_promo.discount_member);
									    $('#csh_promo_tp').val(sel_promo.promo_tp);
									    $('#csh_member_tp').val(sel_promo.member_tp);
									    $('#csh_promo_discount').val(sel_promo.discount);
									    $("#csh_promo_code").val(sel_promo.promo_code);
									    $('#csh_promo_amt').val(sel_promo.promo_amt);
									    $('#csh_promo_amt_type').val(sel_promo.promo_amt_type);
										$('#csh_promo_point').val(sel_promo.point);
										$('#csh_promo_point_to_discount').val(sel_promo.point_to_discount);	
									  	$("#other_promotion_title").html(sel_promo.promo_des);
									  	if(sel_promo.promo_tp=='M'){
									  		getDiscountNetAmt();
									  		 $("#dlgOtherPromotion").dialog("close");
											 initFieldPromt();
									  	}else if(sel_promo.promo_tp=='F'){
									  		$("#other_promotion_cmd").html('');
									  		$("#dlgOtherPromotion").dialog("close");
									  		/*select product freee*/
									  		 if(sel_promo.promo_code=='OM13070214'){
											      //*WR31032014 for change ops tuesday
										  		   	 var member_no=$('#csh_member_no').val(); 
											  		 $.ajax({
											  			 type:'post',
											  			 url:'/sales/member/chkopstuespriv',
											  			 data:{
											  				 member_no:member_no,
											  				 promo_code:sel_promo.promo_code,
											  				 rnd:Math.random()
											  			 },success:function(data){
											  				 if(data=='Y'){
											  					selProductFree(sel_promo.promo_code);
											  				 }else{
											  					jAlert("????????????????????????????????????????????????????????????????????????????????????????????????????????????", 'WARNING!',function(){
											  						initFieldOpenBill();
											  		            	return false;
											  			        });
											  				 }
											  			 }
											  		 });
											    }else{
											    	selProductFree(sel_promo.promo_code);
											    }
										 }    
									});
								}/*end success function*/
							});/*end ajax pos*/							
						},				
						close: function(evt,ui) {
							$('.tableNavOtherPro ul').navigate('destroy');
							$("#dlgOtherPromotion").dialog("destroy");
    						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
    							$("#btnOther").removeAttr("disabled");
								$("#btnOther").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
    						}
    						if(evt.originalEvent && $(evt.originalEvent.which==27)){
    							$("#btnOther").removeAttr("disabled");
								$("#btnOther").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
    						}
						 }
						};	
						$('#dlgOtherPromotion').dialog('destroy');
						$('#dlgOtherPromotion').dialog(dialogOpts_Other);			
						$('#dlgOtherPromotion').dialog('open');
				return false;
		});
	    /*control menu button*/
	    $("#btnDocType").click(function(evt){
		    evt.preventDefault();
		    var onsales=onSales();
		    if(onsales>0){
			 	jAlert('You make a pending transaction. Document type can not be changed.', 'WARNING!',function(){
	            	$("#csh_product_id").focus();
	            	return false;
		        });
			}else if(onsales==0){			
				browsDocStatus();
			}		
		});
	    
	    $("#csh_accordion").accordion({
			 fillSpace: true,
			 autoHeight:false
		});		
		/*for set height accordion*/
	    var hacc = $("#left_content").height();
	    if ((screen.width>=1280) && (screen.height>=1024)){
			hacc=hacc-80;/*65,70,80*/
		}else if(screen.width>=1280 && screen.height<1024){
			//19 " 1440x900
			//for width screen
			hacc=hacc-95;
		}else{
			hacc=hacc-85;
		}
		$(".ui-accordion .ui-accordion-content").css({
			'height':hacc,
			'background':'#c8aad0 url(/sales/img/op/bg_acor_content.png) repeat-x bottom left',
			'border':'1pt solid#444',
			'overflow':'auto',
			'width':'100% fixed',
			'padding-left':'10%'
		});
		
	});//ready