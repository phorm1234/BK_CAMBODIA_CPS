/*=================================================================================*/
/* .click */
/*=================================================================================*/	


/*=================================================================================*/
/*=================================================================================*/	




/*=================================================================================*/
/* function */
/*=================================================================================*/

// paymentBillNormal function run after click ชำระเงิน
////////////////////////////////////WR10022014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
/*
function paymentBillNormal(){	 
	//alert('paymentBillNormal');
	
	var promo_code = $("#csh_promo_code").val();
	if($("#csh_promo_code").val().substring(0,6)=="BPOINT"){
		var sum_amt = $('#csh_sum_amount').val();
		var sum_net = $('#csh_net').val();
			
		var member_no=$.trim($("#csh_member_no").val());
		var play_main_pro=$.trim($("#csh_play_main_promotion").val());
		var play_last_pro=$.trim($("#csh_play_last_promotion").val());
		var csh_gap_promotion=$.trim($('#csh_gap_promotion').val());
		var start_baht=$("#csh_start_baht").val();
		var end_baht=$("#csh_end_baht").val();
		var buy_type=$("#csh_buy_type").val();
		var buy_status=$("#csh_buy_status").val();
		//check ยอดซื้อตามเงื่อนไขโปร
		$.ajax({
			type:'post',
			url:'/sales/member/chkamtprobpoint',
			data:{
				promo_code:promo_code,
				start_baht:start_baht,
				end_baht:end_baht,
				buy_type:buy_type,
				buy_status:buy_status,
				rnd:Math.random()
			},success:function(data){										
				var arr_chk_data=data.split('#');
				//flag Y or N#buy_status L or G # new amount
				if(arr_chk_data[0]=='N'){
					flag_payment = "N";
					var msg_error_amount="";
					if(arr_chk_data[1]=='L'){
						msg_error_amount="ยอดซื้อต้องไม่เกิน "+end_baht;
					}else if(arr_chk_data[1]=='G'){
						msg_error_amount="ยอดซื้ออย่างน้อยต้อง "+start_baht+" ขึ้นไป";
					}
					jAlert(msg_error_amount+' กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){	
						initFieldPromt();
						$("#btn_cal_promotion").removeAttr("disabled");
						$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
						return false;							
					});			
					return false;
				}else{
					gotopaymentBill();
				}
			}
		});		
	}else{
		gotopaymentBill();
	}
}//func
*/
var stack_onetime_pro = "";
function gotopaymentBill(){
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
					getsumdiscountlast("Y");
				}else{
					//////////////////////////////////////////////////
					if($('#status_couponpromo').val()=='Y'){
						//ตรวจสอบยอดซื้อกับวงเงินตามเงื่อนไข		
						var start_baht=parseFloat($('#csh_start_baht').val());
	    				var end_baht=parseFloat($('#csh_end_baht').val());
	    				var buy_status=$("#csh_buy_status").val();	
	    				var csh_net=$('#csh_net').val().replace(/[^\d\.\-\ ]/g,'');
	    				     csh_net=parseFloat(csh_net);
	    				//alert(csh_net + " " + buy_status + " " + start_baht);
						if(csh_net > 0 && buy_status=='G' && csh_net < start_baht ){
							jAlert('ยอดซื้อน้อยกว่า ' + start_baht + ' ไม่สามารถชำระเงินได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
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
								jAlert('ยอดซื้อน้อยกว่า ' + $('#csh_promo_amt').val() + ' ไม่สามารถชำระเงินได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
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
				jAlert('ไม่พบรายการขาย ไม่สามารถชำระเงินได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
					$("#btn_cal_promotion").removeAttr("disabled");
					$("#btn_cal_promotion").removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
					return false;
				});
			}
		}//success
	});//end ajax
}

///////////////////////////////////WR10022014\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


// start pro other =================================================================		
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
				title:"<span class='ui-icon ui-icon-cart'></span>โปรโมชั่นอื่นๆ",
				open: function(){   
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					 $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px 5px","margin-top":"0",
	    					"background-color":"#f2e5ff","font-size":"27px","color":"#666777"});$(this).find('div,button,select, input, textarea').blur();
					$('.ui-dialog-buttonpane button:last').blur();						
					$("#dlgOtherPromotion").html("");
					
					//เลือก promotion ที่ไม่เล่นร่วม promotion หลัก web_promo=Y
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
								var mtmparr = sel_promo.promo_tp.split('_');
								
								if(mtmparr.length>1){
									//sel_promo.promo_tp = mtmparr[0];
									$('#csh_promo_total_discount').val(mtmparr[1]);
								}

							    var csh_member_no=$('#csh_member_no');	
							    var member_type= $('#csh_member_type').html();
							    var csh_net=$('#csh_net').val();
								if(sel_promo.member_tp=='Y' && $.trim(csh_member_no.val())==''){
									jAlert("กรุณาระบุรหัสสมาชิก","ข้อความแจ้งเตือน",function(){	            	
										 $("#dlgOtherPromotion").dialog("close");
										 //initFieldPromt();						
										 initFieldOpenBill();
						            	return false;
							        });
							        return false;
								}//if				

								
								$('#csh_limite_qty').val(sel_promo.limite_qty);
								$('#csh_play_last_promotion').val(sel_promo.play_last_promotion);
								$('#csh_get_promotion').val(sel_promo.get_promotion);
							  	$('#csh_get_point').val(sel_promo.get_point);
							  	$('#csh_member_tp').val(sel_promo.member_tp);
							  	
							  	$("#csh_promo_code").val(sel_promo.promo_code);
							  	$("#csh_buy_type").val(sel_promo.buy_type);
			    				$("#csh_buy_status").val(sel_promo.buy_status);
			    				$('#csh_start_baht').val(sel_promo.start_baht);
			    				$('#csh_end_baht').val(sel_promo.end_baht);
							  	//ws add new
							  	
							  	//$("#status_pro").trigger('change');
							  	if($('#csh_get_promotion').val()=="N"){
							  		$("#status_pro option[value='1']").attr('selected', 'selected');
							  		$("#status_pro").attr('disabled','disabled');
							  	}
							  	
								$('#csh_check_repeat').val(sel_promo.check_repeat);
								$('#csh_promo_st').val(sel_promo.promo_st);//*WR20120724
								$('#csh_web_promo').val(sel_promo.web_promo);
								$('#csh_gap_promotion').val('Y');//เล่นระหว่างโปรได้ cps ไม่มีเล่น morepoint
								$('#csh_discount_member').val(sel_promo.discount_member);
							    $('#csh_promo_tp').val(sel_promo.promo_tp);
							    
							    //$('#csh_promo_discount').val(sel_promo.discount);
								$('#csh_promo_discount').val(10);
							    $("#csh_promo_code").val(sel_promo.promo_code);
							    $('#csh_promo_amt').val(sel_promo.promo_amt);
							    $('#csh_promo_amt_type').val(sel_promo.promo_amt_type);
								$('#csh_promo_point').val(sel_promo.point);
								$('#csh_promo_point_to_discount').val(sel_promo.point_to_discount);	
							  	$("#other_promotion_title").html(sel_promo.promo_des);
							  	
								

								if(stack_onetime_pro!=""){
									jAlert('ไม่สามารถเล่นโปรประเภท 1 คน 1 สิทธิ์ ซ้อนกันใน 1 บิลได้', 'แจ้งเตือน',function(){
										endOfProOther();
										$("#dlgOtherPromotion").dialog("close");
										$('#dlgOtherPromotion').dialog('destroy');
										$('#csh_member_no').focus();
										return false;
									});	
									return false;
								}else{
									stack_onetime_pro = sel_promo.promo_code;
									if(sel_promo.limit_local_play>0){
										$.ajax({
											type:'post',
											url:'/sales/cashier/limitplaypromotion',
											cache:false,
											data:{								
												limitplay	: sel_promo.limit_local_play,
												promocode	: sel_promo.promo_code,
												now:Math.random()
											},success : function(data){
												if(data.status){
													// yeh let's enjoy


												}else{
													jAlert(data.msg, 'แจ้งเตือน',function(){
														endOfProOther();
														$("#dlgOtherPromotion").dialog("close");
														$('#dlgOtherPromotion').dialog('destroy');
														$('#csh_member_no').focus();
														return false;
													});	
												}
											
											}
										});
																			
									}
								}


							  	if($("#csh_promo_code").val().substring(0,6)=="BPOINT"){
									
									points_used = parseInt($("#csh_promo_code").val().slice(-3));
									points_bal = parseInt($('#csh_point_total').html());
									//alert("extra.js :: "+$("#flag_promo_used").val()+"/"+points_bal+"/"+points_used);
							  		if($("#flag_promo_used").val()=="" && points_bal >= points_used){
								  		$("#flag_promo_used").val(sel_promo.promo_code);
								  		$("#dlgOtherPromotion").dialog("close");
								  		
								  		//field key_input in table promo_other = point for cut
								  		$('#csh_point_used').val(sel_promo.key_input);
								  		$('#cut_point_last').val(sel_promo.key_input);
								  		$('#discount_baht').val(sel_promo.discount_baht);
								  		
								  		$("#dlgOtherPromotion").dialog("close");
							  			initFieldPromt();
								  		//alert($('#cut_point_last').val());
								  		
							  		}else{
							  			jAlert('ไม่สามารถเล่นโปรประเภท 1 คน 1 สิทธิ์ ซ้อนกันใน 1 บิลได้', 'แจ้งเตือน',function(){
							  				endOfProOther();
				    						$("#dlgOtherPromotion").dialog("close");
				    						$('#dlgOtherPromotion').dialog('destroy');
				    						$('#csh_member_no').focus();
				    						return false;
					    				});	
					    				return false;
							  		}
							  	}
							  	
							  	
							  	/*
							  	
							  	if(sel_promo.promo_tp=='M'){
							  		if(sel_promo.promo_code=="OPENSHOP"){
							  			alert('aa');
							  			$("#dlgOtherPromotion").dialog("close");
							  			initFieldPromt();
							  		}else if(sel_promo.promo_code=="EX00000001"){
							  			$("#dlgOtherPromotion").dialog("close");
							  			initFieldPromt();
							  			//$('#csh_product_id').attr('disabled','disabled');
							  			$('#csh_product_id').disable().blur();
							  			//open dialog product expire
							  			showFormProductExp();
							  		}else{
							  			//getDiscountNetAmt();
							  			$("#dlgOtherPromotion").dialog("close");
							  			initFieldPromt();
							  		}
							  	*/
								//alert("extra.js::"+sel_promo.promo_tp);
							  	if(sel_promo.promo_tp=="SHORT"){
						  			if(csh_member_no.val().length < 1 && member_type!=='WALK IN'){
				    					jAlert('ลูกค้า WALK IN กรุณากดคีย์ ENTER ที่ช่องรหัสสมาชิก \nเพื่อแสดงสถานะลูกค้าประเภท WALK IN ก่อนเล่นโปรโมชั่น', 'แจ้งเตือน',function(){
				    						$("#dlgOtherPromotion").dialog("close");
				    						$('#dlgOtherPromotion').dialog('destroy');
				    						$('#csh_member_no').focus();
				    						return false;
					    				});	
					    				return false;
				    				}else{
				    					// setproduct free
				    					$("#dlgOtherPromotion").dialog("close");
				    					showFormShort(sel_promo.promo_des,sel_promo.promo_code);
				    				}
						  		}else if(sel_promo.promo_code=="OPENSHOP"){
						  			//alert('aa');
						  			$("#dlgOtherPromotion").dialog("close");
						  			initFieldPromt();
							  	}else if(sel_promo.promo_code=="EX00000001"){
						  			$("#dlgOtherPromotion").dialog("close");
						  			initFieldPromt();
						  			//$('#csh_product_id').attr('disabled','disabled');
						  			$('#csh_product_id').disable().blur();
						  			//open dialog product expire
						  			showFormProductExp();
							  	}else if(sel_promo.promo_tp=="OSPI" || sel_promo.promo_tp=="OSPU"){
							  		//OSPI == add new record to play promotion and mark as used
							  		//OSPU == update record to play promotion and mark as used
							  		if($("#flag_promo_used").val()==""){
								  		$("#flag_promo_used").val(sel_promo.promo_code);
								  		$("#dlgOtherPromotion").dialog("close");
								  		showFormShockPrice(sel_promo.promo_des,sel_promo.promo_code);
							  		}else{
							  			jAlert('ไม่สามารถเล่นโปรประเภท 1 คน 1 สิทธิ์ ซ้อนกันใน 1 บิลได้', 'แจ้งเตือน',function(){
							  				endOfProOther();
				    						$("#dlgOtherPromotion").dialog("close");
				    						$('#dlgOtherPromotion').dialog('destroy');
				    						$('#csh_member_no').focus();
				    						return false;
					    				});	
					    				return false;
							  		}
							  		//initFieldPromt();
							  		//gipro(sel_promo.promo_code);
							  		
							  	}else if(sel_promo.promo_tp=='F'){
							  		$("#other_promotion_cmd").html('');
							  		$("#dlgOtherPromotion").dialog("close");
							  		
							  		//.substring(1, 4);
							  		//SAMPLE
							  		var ss_promo_code = sel_promo.promo_code;
							  		if(ss_promo_code.substring(0, 8)=='SAMPLING'){
							  			if(csh_member_no.val().length < 1 && member_type!=='WALK IN'){
					    					jAlert('ลูกค้า WALK IN กรุณากดคีย์ ENTER ที่ช่องรหัสสมาชิก \nเพื่อแสดงสถานะลูกค้าประเภท WALK IN ก่อนเล่นโปรโมชั่น', 'แจ้งเตือน',function(){
					    						$("#dlgOtherPromotion").dialog("close");
					    						$('#dlgOtherPromotion').dialog('destroy');
					    						$('#csh_member_no').focus();
					    						return false;
						    				});	
						    				return false;
					    				}else{
					    					// setproduct free
					    					$("#dlgOtherPromotion").dialog("close");
					    					selProductFree(sel_promo.promo_code);
					    				}
							  		}
							  	}else if(sel_promo.promo_tp=='M'){
									chkMemPriv(sel_promo);
									$("#dlgOtherPromotion").dialog("close");
									return false;
								}else if(sel_promo.promo_tp.substring(0, 3)=="IDF"){
									fromreadprofile(sel_promo.promo_code,'Y','','','','',sel_promo.promo_des); 
									$("#dlgOtherPromotion").dialog("close");		
									return false;
									
								}else if(sel_promo.promo_tp!=""){
											//rem_amm
									chkPrivForm(sel_promo);	
									//gipro(sel_promo.promo_code);
									$("#dlgOtherPromotion").dialog("close");		
									return false;
									
								}else{
						  			//getDiscountNetAmt();
						  			$("#dlgOtherPromotion").dialog("close");
						  			initFieldPromt();
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
//end click promo other ========================================================


//Short =================================================================
function showFormShort(promo_des,promo_code){
	/**
	 * @desc
	 */		
	var opts_dlgFormShort={
		modal: true,
    	title: promo_des,
    	width:'35%',
    	height:'auto',
    	open:function(){
    		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
    		$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
    		$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
    		$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			    //button style	
    		$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
    		$(".ui-widget-overlay").live('click', function(){				    	
    			$('.ui-dialog-content input:first').focus();
    		}); 				  
    		/*
    		$('.ui-dialog-content input').keydown(function (e) {
    			if(e.which === 13) {
    				var index = $('.ui-dialog-content input').index(this) + 1;
    				$('.ui-dialog-content input').eq(index).focus();
    			}
    		});
    		*/
		},	    	  
		buttons: [{ 
			text: "OK",
			id:"btn_check_short",
			class: 'ui-btndlgpos', 
			click: function(evt){ 
				evt.preventDefault();
				if($('#short_status').val()==1){
					$("#dlgSmsMobile").dialog("close");
				}else{
					
				}
			} 
		}],
		close:function(){
			$("#short_doc_no").unbind('keypress');
			$("#short_unlock").unbind('keypress');		
						
			
			initFieldPromt();
			$("#dlgSmsMobile").dialog("destroy");
		}
	};
	
	$('#dlgSmsMobile').html('');
	var url='/sales/cashier/formshort';
	  $.ajax({
		type:'post',
	    url:url,
	    data:{
		  	promo_code:promo_code,
	    	rnd:Math.random()
	    },
	    success: function(data) {		    	
	    	$('#dlgSmsMobile').html(data);
			$("#dlgSmsMobile").dialog(opts_dlgFormShort);
			
			//$("#dlgSmsMobile").dialog("open");
			//$("#dlgSmsMobile").dialog(varDlgSmsMobile);
	    }
	  });
		
}//func
//show form Short =====================================================



//show form product expire =====================================================
function  showFormProductExp(){
	var opts_dlgProductExpire={
		modal: true,
		title:"สแกนสินค้า Expire",
		width:'60%',
		height:'auto',
		open:function(){
			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
			$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
			$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			/*
			$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
			$(".ui-widget-overlay").live('click', function(){				    	
				$('.ui-dialog-content input:first').focus();
			}); 				  
			
    		$(this).parents(".ui-dialog:first").find('input').keypress(function(e) {									    	
    			if(e.keyCode == $.ui.keyCode.ENTER) {
			    	e.stopImmediatePropagation();				    		
			    	$('.ui-dialog-buttonpane button:first').click();
    			}
    		});
    		*/
			$("#exp_product_id").focus();
		},	    	  
    	buttons:[{ 
    		text: "จบโปร",
    		id:"btn_end_expire",
    		class: 'ui-btndlgpos', 
    		click: function(evt){
    			evt.preventDefault();	    	                	 
    			//do something
    			$("#dlgProductExpire").dialog("destroy");
    			paymentBill('00');
    		} 
    	}],
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
			$("#dlgProductExpire").dialog("destroy");
		}
	};
	
	$('#dlgProductExpire').html('');
	var url='/sales/cashier/formproductexpire';
	$.ajax({
		type:'post',
	    url:url,
	    data:{
	    	rnd:Math.random()
	    },
	    success: function(data) {		    	
	    	$('#dlgProductExpire').html(data);
			$("#dlgProductExpire").dialog(opts_dlgProductExpire);
	    }
	});
}//func
//show form product expire =====================================================







// Shock Price =================================================================
function showFormShockPrice(promo_des,promo_code){
	/**
	 * @desc
	 */		
	var opts_dlgFormShockPrice={
		modal: true,
    	title: promo_des,
    	width:'40%',
    	height:'auto',
    	open:function(){
    		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
    		$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
    		$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
    		$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			    //button style	
    		$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
    		$(".ui-widget-overlay").live('click', function(){				    	
    			$('.ui-dialog-content input:first').focus();
    		}); 				  
    		/*
    		$('.ui-dialog-content input').keydown(function (e) {
    			if(e.which === 13) {
    				var index = $('.ui-dialog-content input').index(this) + 1;
    				$('.ui-dialog-content input').eq(index).focus();
    			}
    		});
    		*/
		},	    	  
		buttons: [{ 
			text: "OK",
			id:"btn_check_shockprice",
			class: 'ui-btndlgpos', 
			click: function(evt){ 
				evt.preventDefault();
				if($("#sp_otp").val() == $("#sp_otp_ok").val() && ($("#sp_otp_ok").val() !="") ){
					
					gipro(promo_code);
					//alert("gipro("+promo_code+")");
					$("#dlgSmsMobile").dialog("close");
				}else{
					jAlert('OTP Code ไม่ถูกต้อง', 'แจ้งเตือน',function(){
						$('#sp_otp').focus();
						return false;
					});
				}
			} 
		}],
		close:function(){
			$("#sp_mobile_no").unbind('keypress');
			$("#sp_idcard").unbind('keypress');
			$(".otp").unbind('keypress');			
						
			endOfProOther();
			initFieldPromt();
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
	var url='/sales/cashier/formshockprice';
	  $.ajax({
		type:'post',
	    url:url,
	    data:{
		  	promo_code:promo_code,
	    	rnd:Math.random()
	    },
	    success: function(data) {		    	
	    	$('#dlgSmsMobile').html(data);
			$("#dlgSmsMobile").dialog(opts_dlgFormShockPrice);
			//$("#dlgSmsMobile").dialog("open");
			//$("#dlgSmsMobile").dialog(varDlgSmsMobile);
	    }
	  });
		
}//func


//Shock Price =================================================================



function setNewCardMember(status_no){
	//new card
	/**
	*@desc add new card
	*@modify 21092015
	*@return
	*/
	/*
	var ops_day_new=$('#csh_ops_day').val(); 
	ops_day_new=$.trim(ops_day_new);
	switch(ops_day_new){
		case 'TH1':msg_show_sel="บัตรพฤหัสบดีที่1";break;
		case 'TH2':msg_show_sel="บัตรพฤหัสบดีที่2";break;
		case 'TH3':msg_show_sel="บัตรพฤหัสบดีที่3";break;
		case 'TH4':msg_show_sel="บัตรพฤหัสบดีที่4";break;
		case 'TU1':msg_show_sel="บัตรอังคารที่1";break;
		case 'TU2':msg_show_sel="บัตรอังคารที่2";break;
		case 'TU3':msg_show_sel="บัตรอังคารที่3";break;
		case 'TU4':msg_show_sel="บัตรอังคารที่4";break;
	}
	*/
	var dialogOpts_NewCard={
			autoOpen:false,
			width:'22%',
			height:105,
			modal:true,
			resizeable:true,
			position:'center',
			showOpt: {direction: 'up'},		
			closeOnEscape:false,	
			title:"<span class='ui-icon ui-icon-person'></span>บันทึกบัตรสมาชิกใหม่",
			open:function(){
				//*** check lock unlock
				if($("#csh_lock_status").val()=='Y'){
					lockManualKey();
				}else{
					unlockManualKey();
				}
				//*** check lock unlock
				//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
				$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
				$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
    			$(this).parents(".ui-dialog:first")
    				.find(".ui-dialog-content")
    				.css({"padding":"20px","background-color":"#f2e5ff","font-size":"26px","color":"#666777"});
					
				 $(".ui-widget-overlay").live('click', function(){
			    	$("#csh_new_card").focus();
				});					
				 
				$("#csh_new_card").val('');
				$("#csh_new_card").focus();
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
								jAlert('กรุณาระบุรหัสบัตรสมาชิก', 'ข้อความแจ้งเตือน',function(){
									$csh_newcard.focus();
									return false;
								});	
							}else{
								//*WR27032014 append check opsday old card and new card
								/*
								var ops_day_old=$('#info_apply_promo_detail').html();
								ops_day_old=$.trim(ops_day_old);
								switch(ops_day_old){
									case 'TH1':msg_show="บัตรพฤหัสบดีที่1";break;
									case 'TH2':msg_show="บัตรพฤหัสบดีที่2";break;
									case 'TH3':msg_show="บัตรพฤหัสบดีที่3";break;
									case 'TH4':msg_show="บัตรพฤหัสบดีที่4";break;
									case 'TU1':msg_show="บัตรอังคารที่1";break;
									case 'TU2':msg_show="บัตรอังคารที่2";break;
									case 'TU3':msg_show="บัตรอังคารที่3";break;
									case 'TU4':msg_show="บัตรอังคารที่4";break;
								}
								*/
								//*WR27032014 append check opsday old card and new card
								var opts_member={
										type:'post',
										url:'/sales/member/checkmember',
										data:{
											member_no:$csh_newcard_val,
											ops_day_old:'',
											ops_day_new:'',
											rndno:Math.random()
										},
										success:function(data){
											if(data=='1'){
												jAlert('รหัสบัตรนี้ถูกใช้สมัครสมาชิกแล้ว กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='2'){
												jAlert('ไม่พบรหัสบัตรนี้ หรือรหัสนี้ถูกใช้สมัครแล้ว กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='3'){
												jAlert('กรุณาเลือก ' + msg_show, 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='4'){
												jAlert('กรุณาเลือกบัตรวันพฤหัสบดี', 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='5'){
												jAlert('กรุณาสแกนบัตร ' + msg_show_sel, 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='6'){
												jAlert('กรุณาเปลี่ยนคีย์บอดเป็นภาษาอังกฤษ', 'ข้อความแจ้งเตือน',function(){
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
												//$("#btn_cal_promotion").trigger("click");//trigger payment
												paymentBillOrg(status_no);
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
					rePaymentBill();
					/*
					initTblTemp();
			        initFormCashier();
					initFieldOpenBill();
					getCshTemp('Y');
					browsDocStatus();
					*/
				}else if(evt.which==27){
					rePaymentBill();
					/*
			        initTblTemp();
			        initFormCashier();
					initFieldOpenBill();
					getCshTemp('Y');
					browsDocStatus();
					*/
			    }
				$("#btn_cal_promotion").removeAttr("disabled");
				$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
			}
	};
	$('#dlgNewCard').dialog('destroy');
	$('#dlgNewCard').dialog(dialogOpts_NewCard);			
	$('#dlgNewCard').dialog('open');
}//func



function  checksampling(promo_code){

	/**
	* @desc
	* opts_dlgFormMobile -> opts_dlgSampling
	* 
	*/		
	var opts_dlgFormMobile={
	    modal: true,
	    title:"กรุณาระบุค่าในฟอร์มให้ครบ",
	    width:'33%',
	    height:'auto',
	    open:function(){
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
		},	    	  
		buttons:[{ 
			text: "OK",
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
				if((arr_tp.indexOf("T") != -1) && (sms_mobile.length!=10)){
					jAlert('กรุณาระบุเบอร์โทรศัพท์จำนวน 10 หลัก', 'ข้อความแจ้งเตือน',function(){
						$("#sms_mobile").focus();
							return false;
						});	    				  
					return false;
				}else{	
					callSmsPromo(sms_promo_code,sms_mobile,redeem_code,member_tp,idcard);
				}
			}
		}],
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
			$("#dlgSmsMobile").dialog(varDlgSmsMobile);
	    }
	});		
}//func


function cal_point_last(net){
	//alert(net);
	$.ajax({
		type:'post',
		url:'/sales/cashier/getpointlast',
		data:{
			net_amt:$('#txt_netvt').val(),
			member_no : $('#csh_member_no').val(),
			promo_code : $('#csh_promo_code').val(),
			rnd:Math.random()
		},success:function(data){
			var point_used=$('#csh_point_used').val();
			var point_net=parseInt(data)-parseInt(point_used);
			$('#csh_point_receive').val(data);
			$('#csh_point_net').val(point_net);
			$('#csh_net').val(net);
			
		}
	});
}

/*
function cps_cal_discount(){
	var discount=0;
	$.ajax({
		type:'post',
		url:'/sales/cashier/calcpsdiscount',
		data:{
			net_amt:$('#txt_netvt').val(),
			per_disc:$('#csh_percent_discount').html(),
			rnd:Math.random()
		},success:function(data){
			
			discount = data;
		}
	});
	return discount;
}
*/

function getsumdiscountlast(last){
	//alert("===getsumdiscountlast");
	//console.log(last);
	var cn_amount=$("#cn_amount").val();//สำหรับตัดคะแนนหลังหลักยอดเงิน CN
	
	if($('#csh_get_point').val()!=""){
		var flg_chk_point=$('#csh_get_point').val();
	}else{
		var flg_chk_point='N';
		if(jQuery.trim($("#csh_member_no").val())!='' && jQuery.trim($("#csh_status_no").val())!='01'){
			var flg_chk_point='Y';
		}
	}
	
	$.getJSON("/sales/cashier/getsumdiscountlast",{
		cn_amount:cn_amount,
		flg_chk_point:flg_chk_point,
		xpoint:$("#csh_xpoint").val(),
		flg_tbl:'promotion',
		discount_member : $('#csh_discount_member').val(),
		promo_code : $('#csh_promo_code').val(),
		do_pay : $("#do_pay").val(),
		cut_point_last : $("#cut_point_last").val(),
		discount_baht : $("#discount_baht").val(),
		member_no : $("#csh_member_no").val(),
		actions:'getSumCshTemp'
	},function(data){
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
				
				if( $('#csh_member_type').html()=="WALK IN" || $('#csh_promo_code').val()=='BD_DIAMOND' || $('#csh_promo_code').val()=='BD_GOLD' || $('#csh_promo_code').val()=='BD_SILVER'){
					$('#csh_percent_discount').html(0);
				}else{
					$('#csh_percent_discount').html(m.percent_discount);
				}
				//--------------------------------------------------------									
				var point_receive=parseInt(m.point_receive);
				$('#csh_sum_qty').html(csh_sum_quantity + " ชิ้น").css({"font-size":"200%","color":"#FFF"});									
				//WR20120901 test for 50BTO1P
				if($('#csh_promo_code').val()=='50BTO1P'){
					$('#csh_point_receive').val(m.mp_point_receive);
					$('#csh_point_used').val(m.mp_point_used);
					$('#csh_point_net').val(m.mp_point_net);
				}else{
					if(flg_chk_point=='Y'){
						var point_used=$('#csh_point_used').val();
						var point_net=parseInt(point_receive)-parseInt(point_used);
						$('#csh_point_receive').val(point_receive);
						$('#csh_point_net').val(point_net);
					}else{
						$('#csh_point_receive').val('0');
						$('#csh_point_net').val('0');	
					}
					
					if($('#csh_promo_code').val()=='BD_SURPRIS' || $('#csh_promo_tp').val() =="OSPI" || $('#csh_promo_tp').val() =="OSPU"){
						endOfProOther();
					}
				}
				//--------------------------------------------------------
				//console.log('aa22');
				displayDigit($('#csh_net').val());	
			}else{
				$('#csh_sum_discount').val('0.00');
				$('#csh_sum_amount').val('0.00');
				$('#csh_net').val('0.00');
				$('#csh_sum_qty').html('');//WR07022013
				//console.log('aa33');
				displayDigit($('#csh_net').val());	
			}	
			
			/*	
			if($('#csh_gap_promotion').val()=='Y' && $('#csh_promo_tp').val()=='M'){			
				getDiscountNetAmt();				
			}
			*/
		});//foreach
		if(last=='N'){
			if($('#csh_promo_code').val()=="TOUR01" || $('#csh_promo_code').val()=="18020004"|| $('#csh_promo_code').val()=="LLBCWG0418"){
				paymentBill('00');
			}
		}else{
			$('#lastpro_start').val("1");
			//alert('lastprochk');
			//alert($('#csh_play_last_promotion').val());
			if($('#csh_play_last_promotion').val()=="N"){
				paymentBill('00');
			}else{
				if($('#csh_play_main_promotion').val()=="N"){
					clearcanclepro();
				}
				lastprochk();
			}
			//lastprochk();
		}
		return false;
	});
}

function getsumcshtemp(){
	//alert("===getsumcshtemp");
	var cn_amount=$("#cn_amount").val();//สำหรับตัดคะแนนหลังหลักยอดเงิน CN
	if($('#csh_get_point').val()!=""){
		var flg_chk_point=$('#csh_get_point').val();
	}else{
		var flg_chk_point='N';
		if(jQuery.trim($("#csh_member_no").val())!='' && jQuery.trim($("#csh_status_no").val())!='01'){
			var flg_chk_point='Y';
		}
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
							
							$('#csh_percent_discount').html(m.percent_discount);
							
							//--------------------------------------------------------									
							var point_receive=parseInt(m.point_receive);
							$('#csh_sum_qty').html(csh_sum_quantity + " ชิ้น").css({"font-size":"200%","color":"#FFF"});									
							//WR20120901 test for 50BTO1P
							if($('#csh_promo_code').val()=='50BTO1P'){
								$('#csh_point_receive').val(m.mp_point_receive);
								$('#csh_point_used').val(m.mp_point_used);
								$('#csh_point_net').val(m.mp_point_net);
							}else{
								if(flg_chk_point=='Y'){
									var point_used=$('#csh_point_used').val();
									var point_net=parseInt(point_receive)-parseInt(point_used);
									$('#csh_point_receive').val(point_receive);
									$('#csh_point_net').val(point_net);
								}else{
									$('#csh_point_receive').val(0);
									$('#csh_point_net').val(0);	
								}
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
							//getDiscountNetAmt();				
						}
					});//foreach
					return false;
			}//
	);
	return false;
}


function setBirthday(promo_code,end_baht){
	$.ajax({
   		type:'post',
   		url:'/sales/member/otherpromo',
   		data:{
			promo_code:promo_code,
			rnd:Math.random()
		},
		success:function(data){
			if(data!=''){
				 var objBirthDay=$.parseJSON(data);	
				 console.log(objBirthDay.promo_code);
				 $('#csh_promo_code').val(objBirthDay.promo_code);
				 $('#csh_promo_tp').val(objBirthDay.promo_tp);//*WR29072014										    				
				 $('#csh_play_main_promotion').val(objBirthDay.play_main_promotion);
				 $('#csh_play_last_promotion').val(objBirthDay.play_last_promotion);
				 $('#csh_get_promotion').val(objBirthDay.get_promotion);
				 $('#csh_start_baht').val(objBirthDay.start_baht);
				 $('#csh_end_baht').val(end_baht);
				 $('#csh_discount_member').val(objBirthDay.member_discount);
				 $('#csh_get_point').val(objBirthDay.get_point);	
				 $("#csh_buy_type").val(objBirthDay.buy_type);
				 $("#csh_buy_status").val(objBirthDay.buy_status);											    				
				 $('#csh_web_promo').val('Y');
				 //var arr_birthday=$('#info_member_birthday').html().split('/');
				 //var arr_docdate=$('#csh_doc_date').html().split('/');
				 $("#csh_xpoint").val(objBirthDay.xpoint);
				 //alert($('#csh_discount_member').val());
				/*
    			if(arr_birthday[1]==arr_docdate[1]){
				 	$("#csh_xpoint").val(objBirthDay.xpoint);						
				}else{
					$("#csh_xpoint").val('1');	
    			}
	    		*/
				 //birthday_month,xpoint
				 $('#other_promotion_title').append(objBirthDay.promo_des);
				 if(objBirthDay.type_discount=='PERCENT'){
					 $('#csh_percent_discount').html(parseInt(objBirthDay.discount));
    			 }		
    			//$('#status_pro').trigger('change');		 
				//initFieldPromt(); 
				 console.log("set bd "+$("#csh_promo_code").val());
    		}
		}//success
   });
}

function showformgrouptour(promo_code,promo_des){
	var opts_dlgFormShort={
		modal: true,
    	title: "โปรโมชั่นทัวร์จีน",
    	width:'40%',
    	height:'auto',
    	open:function(){
    		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
    		$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
    		$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
    		$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			    //button style	
    		$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
    		$(".ui-widget-overlay").live('click', function(){				    	
    			$('.ui-dialog-content input:first').focus();
    		}); 				  
    		/*
    		$('.ui-dialog-content input').keydown(function (e) {
    			if(e.which === 13) {
    				var index = $('.ui-dialog-content input').index(this) + 1;
    				$('.ui-dialog-content input').eq(index).focus();
    			}
    		});
    		*/
    		if($("#csh_lock_status").val()=='Y'){
				lockManualKey();
			}else{
				unlockManualKey();
			}
		},	    	  
		buttons: [{ 
			text: "OK",
			id:"btn_tour_ok",
			class: 'ui-btndlgpos', 
			click: function(evt){ 
				evt.preventDefault();
				var barcode_ok;
				var passport_ok;
				switch ($('#form_tour_barcode').val()) {
				    case "PRCTG001":
				        
				    case "PRCTG001A":
				        
				    case "PRCTG001B":
				        
				    case "PRCTG001C":
				        
				    case "PRCTG001D":
				        
				    case "PRCTG001E":
				        
				    case "PRCTG001F":
				        
				    case "PRCTG001G":
				    	
				    case "PRCTG001H":

				    case "PRCTG001I":

					case "TWBG11":

					case "PRCTG001H":
				    	barcode_ok="Y";
						break;
					default:
						barcode_ok="Y";
						pcheck = $('#form_tour_barcode').val().substr(0, 5); 
						if(pcheck=="TWBG0"){
							barcode_ok="Y";
						}
						if(promo_code=="LLSAM0817"){
							barcode_ok="Y";
						}
				    break;
				}
				if($('#form_tour_passport').val()!=""){
					passport_ok="Y";
				}
				
				if(barcode_ok=="Y"){
					
					if(passport_ok=="Y"){
						$('#tour_barcode').val($('#form_tour_barcode').val());
						$('#tour_passport').val($('#form_tour_passport').val());
						$('#tour_email').val($('#form_tour_email').val());
									
						$("#dlgTour").dialog("close");
						$("#dlgTour").dialog("destroy");
						
						gipro(promo_code);
					}else{
						jAlert('กรุณาระบุ Passport', 'แจ้งเตือน',function(){
							$('#form_tour_passport').focus();
							return false;
	    				});	
					}
				}else{
					jAlert('คูปอง Barcode ไม่ถูกต้อง', 'แจ้งเตือน',function(){
						$('#form_tour_barcode').focus();
						return false;
    				});	
				}
			} 
		}],
		close:function(){
			$("#dlgTour").dialog("destroy");
		}
	};
	$('#dlgTour').html('');
	var status_otp='Y';
	var member_no = $('#csh_member_no').val();
	var id_card = $('#csh_member_no').val();

	fromreadprofile(promo_code,status_otp,member_no,id_card,'','',promo_des); 
	/*
	var url='/sales/cashier/grouptour';
	  $.ajax({
		type:'post',
	    url:url,
	    data:{
	    	rnd:Math.random()
	    },
	    success: function(data) {
	
	    	$('#dlgTour').html(data);
			$("#dlgTour").dialog(opts_dlgFormShort);
			
			//$("#dlgSmsMobile").dialog("open");
			//$("#dlgSmsMobile").dialog(varDlgSmsMobile);
	 
		}
	  });/**/
}

function showformUSSD(promo_code,promo_name){
	var opts_dlgFormShort={
		modal: true,
    	title: promo_name,
    	width:'40%',
    	height:'auto',
    	open:function(){
    		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
    		$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
    		$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
    		$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			    //button style	
    		$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
    		$(".ui-widget-overlay").live('click', function(){				    	
    			$('.ui-dialog-content input:first').focus();
    		}); 				  
    		/*
    		$('.ui-dialog-content input').keydown(function (e) {
    			if(e.which === 13) {
    				var index = $('.ui-dialog-content input').index(this) + 1;
    				$('.ui-dialog-content input').eq(index).focus();
    			}
    		});
    		*/
    		if($("#csh_lock_status").val()=='Y'){
				lockManualKey();
			}else{
				unlockManualKey();
			}
		},	    	  
		buttons: [{ 
			text: "OK",
			id:"btn_tour_ok",
			class: 'ui-btndlgpos', 
			click: function(evt){ 
				evt.preventDefault();
				var barcode_ok;
				var passport_ok;
				//console.log(promo_code+"::"+$('#form_tour_barcode').val());
				if($('#form_tour_barcode').val().length==6){
					$("#dlgTour").dialog("destroy");
					return false;
				}
				var url='/sales/cashier/proussduse';
				$.ajax({
					type:'post',
					url:url,
					data:{
						promo_code:promo_code,
						act:"test",
						coupon_code:$('#form_tour_barcode').val(),
						tel: $('#form_tour_tel').val()
					},
					success: function(data) {
							var obj = JSON.parse(data);
							if(obj.status=="Y"){
								console.log($('#form_tour_barcode').val());
								$('#csh_promo_code').val(promo_code);
								$('#id_redeem_code').val($('#form_tour_barcode').val());
								$('#other_promotion_title').html(promo_name);
								$('#tour_tel').val($('#form_tour_tel').val());
								$("#dlgTour").dialog("destroy");
								//alert($('#tour_tel').val()+" USSDDATA");
								gipro(promo_code);
								//initFieldPromt()
							}else{
								alert(obj.msg);
							}
						
					}
				});	

			} 
		}],
		close:function(){
			$("#dlgTour").dialog("destroy");
		}
	};

	$('#dlgTour').html('');
	var url='/sales/cashier/proussd';
	  $.ajax({
		type:'post',
	    url:url,
	    data:{
	    	rnd:Math.random(),
			promo_code:promo_code
	    },
	    success: function(data) {		    	
	    	$('#dlgTour').html(data);
			$("#dlgTour").dialog(opts_dlgFormShort);
			
			//$("#dlgSmsMobile").dialog("open");
			//$("#dlgSmsMobile").dialog(varDlgSmsMobile);
	    }
	  });
}

function clearcanclepro(){
	var url='/sales/cashier/clearcanclepro';
	$.ajax({
		type:'post',
	    url:url,
	    data:{
	    	rnd:Math.random()
	    },
	    success: function(data) {		    	
	    	
	    }
	});		
}

/*

function setNewCardMember(){
	var ops_day_new=$('#csh_ops_day').val(); 
	ops_day_new=$.trim(ops_day_new);
	switch(ops_day_new){
		case 'TH1':msg_show_sel="บัตรพฤหัสบดีที่1";break;
		case 'TH2':msg_show_sel="บัตรพฤหัสบดีที่2";break;
		case 'TH3':msg_show_sel="บัตรพฤหัสบดีที่3";break;
		case 'TH4':msg_show_sel="บัตรพฤหัสบดีที่4";break;
		case 'TU1':msg_show_sel="บัตรอังคารที่1";break;
		case 'TU2':msg_show_sel="บัตรอังคารที่2";break;
		case 'TU3':msg_show_sel="บัตรอังคารที่3";break;
		case 'TU4':msg_show_sel="บัตรอังคารที่4";break;
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
			title:"<span class='ui-icon ui-icon-person'></span>บันทึกบัตรสมาชิกใหม่",
			open:function(){
				if($("#csh_lock_status").val()=='Y'){
					lockManualKey();
				}else{
					unlockManualKey();
				}
				$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
				$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
    			$(this).parents(".ui-dialog:first")
    				.find(".ui-dialog-content")
    				.css({"padding":"20px","background-color":"#f2e5ff","font-size":"26px","color":"#666777"});
					
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
								jAlert('กรุณาระบุรหัสบัตรสมาชิก', 'ข้อความแจ้งเตือน',function(){
									$csh_newcard.focus();
									return false;
								});	
							}else{
								//*WR27032014 append check opsday old card and new card
								var ops_day_old=$('#info_apply_promo_detail').html();
								ops_day_old=$.trim(ops_day_old);
								switch(ops_day_old){
									case 'TH1':msg_show="บัตรพฤหัสบดีที่1";break;
									case 'TH2':msg_show="บัตรพฤหัสบดีที่2";break;
									case 'TH3':msg_show="บัตรพฤหัสบดีที่3";break;
									case 'TH4':msg_show="บัตรพฤหัสบดีที่4";break;
									case 'TU1':msg_show="บัตรอังคารที่1";break;
									case 'TU2':msg_show="บัตรอังคารที่2";break;
									case 'TU3':msg_show="บัตรอังคารที่3";break;
									case 'TU4':msg_show="บัตรอังคารที่4";break;
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
												jAlert('รหัสบัตรนี้ถูกใช้สมัครสมาชิกแล้ว กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='2'){
												jAlert('ไม่พบรหัสบัตรนี้ หรือรหัสนี้ถูกใช้สมัครแล้ว กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='3'){
												jAlert('กรุณาเลือก ' + msg_show, 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='4'){
												jAlert('กรุณาเลือกบัตรวันพฤหัสบดี', 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='5'){
												jAlert('กรุณาสแกนบัตร ' + msg_show_sel, 'ข้อความแจ้งเตือน',function(){
													$csh_newcard.focus();
													return false;
												});	
											}else if(data=='6'){
												jAlert('กรุณาเปลี่ยนคีย์บอดเป็นภาษาอังกฤษ', 'ข้อความแจ้งเตือน',function(){
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
}


	function paymentBill(status_no){
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
	    				           title: "สแกนรหัส Barcode ถุง ",		
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
						            					jAlert('รหัสบาร์โคดไม่ถูกต้อง','ข้อความแจ้งเตือน',function(b){	
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
									 	"ตกลง":function(evt){
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
						            					jAlert('รหัสบาร์โคดไม่ถูกต้อง','ข้อความแจ้งเตือน',function(b){	
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
										 			jAlert('กรุณาคีย์บาร์โคดให้ครบ','ข้อความแจ้งเตือน',function(b){			
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
	    	alert('axe2');
	    	paymentBillOrg(status_no);
	    }
	}//func
*/

function dlgAlipay(){
	/**
	*@desc
	*@return
	*/
	//dlg-credit
	var dialogOpts_sel_alipay = {
			autoOpen: false,
			width:400,		
			height:580,	
			modal:true,
			resizable:true,
			position:['center','center'],
			title:"<span class='ui-icon ui-icon-person'></span>บัตรเครดิต",
			closeOnEscape:true,
			open: function(){
				$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});				        						
				$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');			        						
				$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#f2e5ff","color":"#666777"});
				$(this).dialog("widget").find(".ui-dialog-buttonpane")
                  .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
				$("#dlgAlipay").html("");
				$("#dlgAlipay").load("/sales/cashier/paycase?actions=alipay&now="+Math.random(),
					function(){
						if($("#csh_lock_status").val()=='Y'){
							lockManualKey();
						}else{
							unlockManualKey();
						}
						var cnet=parseFloat($("#txt_net").val().replace(/[^\d\.\-\ ]/g,''));
						//$('#txt_credit_value').ForceNumericOnly();
						$("#txt_alipay_no_value").focus();
						$("#txt_alipay_no_value").keypress(function(evt){
							var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							if(key == 13) {
								$("#txt_alipay_value").focus();
							}
						});
						$("#txt_alipay_value").keypress(function(evt){
							var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							if(key == 13) {
								$("#btn_alipay_ok").focus();
							}
						});
						displayDigit($('#txt_netvt').val());
						if($('#txt_credit').val()!='0.00'){
		        			$('#txt_alipay_value').val($('#txt_credit').val());
		        		}else{
							$("#txt_alipay_value").val(cnet);
						}
					}
				);			        							
			},
			buttons: [{ //*WR18082012
				// $("#btn_payment_confirm").trigger("click");
				text: "ตกลง",
				id : "btn_alipay_ok",
				class: 'ui-btndlgpos', 
				click :function(evt){
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
			 							var v1 = v[1].split("#");
			 							var opts_dlgAlipayConfirm={
			 							    modal: true,
			 							    title:"ยืนยันการชำระเงินด้วย Alipay",
			 							    width:'33%',
			 							    height:'auto',
			 							    open:function(){
			 									$("#dlgAlipayConfirm").load("/sales/cashier/alipayconfirmform?reqid="+v1[0]+"&reqdt="+v1[1]+"&amt="+v1[2],function(){
			 										
			 										$('#btn_payment_yes').click(function(){
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
			 								},
			 								close:function(){
			 									$("#dlgAlipayConfirm").dialog("destroy");
			 								}
			 							};
			 							$("#dlgAlipayConfirm").dialog(opts_dlgAlipayConfirm);
			 							$('#dlgAlipayConfirm').dialog('open');
			 							
			 						//========================================================================
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
			 	}
		    }],
			close: function(evt,ui) {
				calPaid();
				$("#btn_payment_alipay").removeClass("ui-state-focus ui-state-hover ui-state-active");
				$('#dlgAlipay').dialog('destroy');
			 }
			};			
		//alert("alipay");
		$('#dlgAlipay').dialog('destroy');
		$('#dlgAlipay').dialog(dialogOpts_sel_alipay);			
		$('#dlgAlipay').dialog('open');
		return false;
}//func

/*=================================================================================*/
/*=================================================================================*/	
function chkMemPriv(d){
	var url='/sales/cashier/mempromouse';
	$.ajax({
	type:'post',
	url:url,
	data:{
		promo_code:d.promo_code,
		act:"test",
		member_no:$('#csh_member_no').val()
	},
	success: function(data) {
			var mobj = JSON.parse(data);
			obj = mobj[0];
			//alert(obj.status);
			if(obj.status=="Y"){
				console.log($('#form_tour_barcode').val());
				$('#id_redeem_code').val($('#form_tour_barcode').val());
				$('#id_sms_promo_code').val(d.promo_code);
				$('#id_sms_mobile').val($('#mobile').val());
				$('#id_redeem_code').val($('#coupon_code').val());
				$("#dlgTour").dialog("destroy");

				gipro(d.promo_code);

			}else if(obj.status=="OTP"){
				$('#otp_set').show();
			}else{
				$("#dlgTour").dialog("destroy");
				jAlert(obj.msg, 'ข้อความแจ้งเตือน',function(){
					return false;
				});	
			}
		
	}
});	
}



function chkPrivForm(d){
	if(d.promo_code=='LLB99201701'){
		fromreadprofile(d.promo_code,"N","");	
		return false;
	}
	var opts_dlgFormShort={
		modal: true,
    	title: d.promo_des,
    	width:'40%',
    	height:'auto',
    	open:function(){
    		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
    		$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
    		$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
    		$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			    //button style	
    		$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
    		$(".ui-widget-overlay").live('click', function(){				    	
    			$('.ui-dialog-content input:first').focus();
    		}); 				  
    		/*
    		$('.ui-dialog-content input').keydown(function (e) {
    			if(e.which === 13) {
    				var index = $('.ui-dialog-content input').index(this) + 1;
    				$('.ui-dialog-content input').eq(index).focus();
    			}
    		});
    		*/
    		if($("#csh_lock_status").val()=='Y'){
				lockManualKey();
			}else{
				unlockManualKey();
			}
		},	    	  
		buttons: [{ 
			text: "OK",
			id:"btn_tour_ok",
			class: 'ui-btndlgpos', 
			click: function(evt){ 
				evt.preventDefault();
				var barcode_ok;
				var passport_ok;
				//console.log(d.promo_code+"::"+$('#form_tour_barcode').val());
				var url='/sales/cashier/othpromouse';
				var mypromocode = d.promo_code;
				var myidcard = $('#cid_card').val();
				alert($('#coupon_code').val());
				$.ajax({
					type:'post',
					url:url,
					data:{
						promo_code:d.promo_code,
						act:"test",
						coupon_code:$('#coupon_code').val(),
						mobile:$('#mobile').val(),
						cid_card:$('#cid_card').val(),
						otp:$('#otp').val(),
						othdata:$('#oth').val()
					},
					success: function(data) {
							var obj = JSON.parse(data);
							if(obj.status=="Y"){
								console.log($('#form_tour_barcode').val());
								$('#id_redeem_code').val($('#form_tour_barcode').val());
								$('#id_sms_promo_code').val(mypromocode);
								$('#id_sms_mobile').val($('#mobile').val());
								$('#id_redeem_code').val($('#coupon_code').val());
								$("#dlgTour").dialog("destroy");
								//amm rem
								//alert(mypromocode+"::");
								if(mypromocode=="LLB99201701"){
									fromreadprofile(mypromocode,"N",myidcard);
								}else{
									gipro(mypromocode);
								}
								//initFieldPromt()
							}else if(obj.status=="OTP"){
								$('#otp_set').show();
							}else{
								jAlert(obj.msg, 'ข้อความแจ้งเตือน',function(){
									return false;
								});	
							}
						
					}
				});	

			} 
		}],
		close:function(){
			$("#dlgTour").dialog("destroy");
		}
	};

	$('#dlgTour').html('');
	var url='/sales/cashier/chkpriv';
	  $.ajax({
		type:'post',
		url:url,
	    data:{
			promo_code:d.promo_code,
			promo_tp:d.promo_tp,
			promo_des:d.promo_des,
	    	rnd:Math.random()
	    },
	    success: function(data) {
			if(d.promo_tp=='R'){
				fromreadprofile(d.promo_code,'Y','','','','',d.promo_des); 
			}else{
				$('#dlgTour').html(data);
				$("#dlgTour").dialog(opts_dlgFormShort);
			}
			
	    }
	  });



	//gipro(sel_promo.promo_code);
}

function callBackToDo2(promo_code,chk_status,line_msg_error,idcard,coupon_code,s_data){
    /***
     * for support callback function
     * create : 26092014
     * param chk_status : check cond is ok.
     * param s_data country_code#email
     * modify : 27012015 for support mobile coupon and line
     */
	 //alert(promo_code+":"+chk_status+":"+line_msg_error+":"+idcard+":"+coupon_code+":"+s_data);
    if(chk_status=='Y'){
		$('#tour_barcode').val(coupon_code);
		$('#tour_passport').val(idcard);
		$('#tour_email').val($('#form_tour_email').val());

		var c = s_data.split("#");
        //s_data//c_code#email

        var _coupon_code = coupon_code+"#"+idcard+"#"+c[1];
		if(!c[0]){
			c[0]="THA";
		}        
		var _post_id = "PP:"+idcard+":"+c[0];
        var _val = _coupon_code+"||"+_post_id;
		var member_no=$("#csh_member_no").val();
        //coupon_code = barcode#passport#email
        //post_id = PP:passport:c_code

        $("#tour_barcode").val(_val); 
									
        //start
		if(promo_code=='LL002'){
			//alert("cross coupon");
				$.ajax({
					type:'post',
					url:"/sales/cashier/callsmspro",
					data:{
						sms_promo_code:promo_code,
						act:"test",
						country : c[0],
						idcard:idcard,
						member:member_no,
						redeem_code:coupon_code,
						othdata:""
					},
					success: function(data) {
						var obj = JSON.parse(data);
							if(obj.status.toUpperCase()=="Y" || obj.status.toUpperCase()=="OK"){
								$('#id_redeem_code').val(coupon_code);
								$('#id_sms_promo_code').val(promo_code);
								gipro(promo_code);

							}else if(obj.status=="OTP"){
								$('#otp_set').show();
							}else if(obj.status=="NO"){
								jAlert(obj.status_msg, 'ข้อความแจ้งเตือน',function(){
									initTblTemp();//*WR20120905
									initFormCashier();
									getCshTemp('Y'); 
									return false;
								});	
							}else{

								jAlert(obj.msg, 'ข้อความแจ้งเตือน',function(){
									return false;
								});	
							}
						
					}
				});
		}else if(promo_code=='LL18040005'||promo_code=='LLNN201805'||promo_code=='LLFS061801'||promo_code=='LLFS061800' ||promo_code=='LLFM180701'||promo_code=="LLWISE1018"||promo_code=="LLCC071801" ||promo_code=="LLST1808"||promo_code=="LLOC180801"||promo_code=="LLMDAY1801" ||promo_code=="TURAMI01" ||promo_code=="LLAC180801"||promo_code=="LLSAM0817"||promo_code=="AROMA09"||promo_code=="LLMT091801" ||promo_code=="LLSV081801"||promo_code=="LLKTC18001"||promo_code=="LLCTB18001"||promo_code=="LLSF0918001" ||promo_code=="LLSV101801"||promo_code=="LLPAPURI15"||promo_code=="LLKTCSAM01"||promo_code=="LLCTBSAM01"||promo_code=="LLFP201801"||promo_code=="181001"){
				$.ajax({
					type:'post',
					url:"/sales/cashier/gcoupon",
					data:{
						promo_code:promo_code,
						act:"test",
						country : c[0],
						idcard:idcard,
						member:member_no,
						othdata: coupon_code
					},
					success: function(data) {
						var obj = JSON.parse(data);
							if(obj.status.toUpperCase()=="Y"){
								if(promo_code=='LLFS061801X'){
									setSmsProduct(promo_code);
								}else if(promo_code=="LLFP201801"){
									switch(coupon_code){
									case '50':
										
									break;
									case '100':
									
									break;
									case '200':
									break;
									case '300':
									break;
									case '400':
									break;
									case '500':
									break;
									default:
										$('#csh_promo_code').val(promo_code);
										$('#csh_percent_discount').html(15);
										$('#csh_promo_discount').val(0);
									break;
								}	
							}else{
									if(promo_code=="LLST1808"){
										gipro(promo_code+pad(coupon_code,2));
									}else{
										gipro(promo_code);
									}

								}

							}else if(obj.status=="OTP"){
								$('#otp_set').show();
							}else{
								jAlert(obj.msg, 'ข้อความแจ้งเตือน',function(){
									return false;
								});	
							}
						
					}
				});
		}else{
			gipro(promo_code);
		}
		
        //stop

        return false;
    }

}//func
$(function(){	
	
});

function firstBillForm(d){
	var opts_dlgFormShort={
		modal: true,
    	title: d.promo_des,
    	width:'40%',
    	height:'auto',
    	open:function(){
    		$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});
    		$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
    		$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px","margin-top":"0","background-color":"#f2e5ff","font-size":"22px","color":"#666777"});
    		$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			    //button style	
    		$(this).dialog("widget").find(".ui-btndlgpos").css({"padding":"2","background-color":"#a2a3bc","font-size":"14px"});    			    
    		$(".ui-widget-overlay").live('click', function(){				    	
    			$('.ui-dialog-content input:first').focus();
    		}); 				  
    		/*
    		$('.ui-dialog-content input').keydown(function (e) {
    			if(e.which === 13) {
    				var index = $('.ui-dialog-content input').index(this) + 1;
    				$('.ui-dialog-content input').eq(index).focus();
    			}
    		});
    		*/
    		if($("#csh_lock_status").val()=='Y'){
				lockManualKey();
			}else{
				unlockManualKey();
			}
		},	    	  
		buttons: [{ 
			text: "OK",
			id:"btn_tour_ok",
			class: 'ui-btndlgpos', 
			click: function(evt){ 
				evt.preventDefault();
				var barcode_ok;
				var passport_ok;
				console.log(d.promo_code+"::"+$('#form_tour_barcode').val());
				var url='/sales/cashier/othpromouse';
				var mypromocode = d.promo_code;
				$.ajax({
					type:'post',
					url:url,
					data:{
						promo_code:d.promo_code,
						act:"test",
						coupon_code:$('#coupon_code').val(),
						mobile:$('#mobile').val(),
						otp:$('#otp').val(),
						othdata:$('#oth').val()
					},
					success: function(data) {
							var obj = JSON.parse(data);
							if(obj.status.toUpperCase()=="Y"){
								console.log($('#form_tour_barcode').val());
								$('#id_redeem_code').val($('#form_tour_barcode').val());
								$('#id_sms_promo_code').val(mypromocode);
								$('#csh_promo_code').val(mypromocode);
								$('#id_sms_mobile').val($('#mobile').val());
								$('#id_redeem_code').val($('#coupon_code').val());
								$('#status_couponpromo').val("Y");
								$("#dlgTour").dialog("destroy");
								gipro(mypromocode);

							}else if(obj.status=="OTP"){
								$('#otp_set').show();
							}else{
								jAlert(obj.msg, 'ข้อความแจ้งเตือน',function(){
									return false;
								});	
							}
						
					}
				});	

			} 
		}],
		close:function(){
			$("#dlgTour").dialog("destroy");
		}
	};

	$('#dlgTour').html('');
	var url='/sales/cashier/chkpriv';
	  $.ajax({
		type:'post',
		url:url,
	    data:{
			promo_code:d.promo_code,
			promo_tp:d.promo_tp,
			promo_des:d.promo_des,
	    	rnd:Math.random()
	    },
	    success: function(data) {		    	
	    	$('#dlgTour').html(data);
			$("#dlgTour").dialog(opts_dlgFormShort);
			
			//$("#dlgSmsMobile").dialog("open");
			//$("#dlgSmsMobile").dialog(varDlgSmsMobile);
	    }
	  });



	//gipro(sel_promo.promo_code);
}


	function idcardSelectBox(){
		//e.preventDefault();
		$('<div id="dlgIdCard"><span style="color:#336666;font-size:20px;"></span><input type="hidden" size="15" id="id_card_ref"/></div>').dialog({
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
							//$("#pdt_redeempoint").focus();
						});
					  //var cn_doc_no=$.trim($("#cn_doc_no").val());
					 // var cn_member_id_ref=$("#cn_member_id_ref").val();
					  $.get("../../../download_promotion/id_card_quick/api_verify_from.php?function_next=callbackIdCard", function(data) {
						   $('#dlgIdCard').empty().html(data);
						});
			},close:function(){
		
					$('#dlgIdCard').remove();	
			   }
		});
		//ccsreadidcardfrom();
	}//func

	function callbackIdCard(data){
		$('#dlgIdCard').dialog('close');
		var arr_data=data.split('#');
		$('#rwd_member_no').val(arr_data[0]);
		$('#rwd_idcard_no').val(arr_data[1]);
		//checkMember();
		//alert(arr_data[0]);
		//cmdEnterKey("cn_member_no");
	}//func


	function dlgMobilePayment(){
	/**
	*@desc
	*@return
	*/
	//dlg-credit
	var dialogOpts_mobilepayment = {
		autoOpen: false,
		width:400,		
		height:280,	
		modal:true,
		resizable:true,
		position:['center','center'],
		title:"<span class='ui-icon ui-icon-person'></span>Mobiel Payments",
		closeOnEscape:true,
		open: function(){ 
			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});				        						
			$(this).dialog('widget')
	            .find('.ui-dialog-titlebar')
	            .removeClass('ui-corner-all')
	            .addClass('ui-corner-top');			        						
			$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#f2e5ff","color":"#666777"});
			$(this).dialog("widget").find(".ui-dialog-buttonpane")
              .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
			$("#dlgMobilePayment").html("");
			$("#dlgMobilePayment").load("/sales/cashier/paycase?actions=mobilepayment&now="+Math.random(),
				function(){
					$('#btn_payment_alipay').button({
				        icons: { primary: "btn_payment_alipay"}
				    });
					
					$('#btn_payment_kalipay').button({
				        icons: { primary: "btn_payment_kalipay"}
				    });
					
					
					$('#btn_payment_wechat').button({
				        icons: { primary: "btn_payment_wechat"}
				    });
					
					
					$("#btn_payment_alipay").click( function(evt){
						//check if offline or online mode			
						
						evt.preventDefault();
			        	var online_mode=false;
			        	if(!online_mode){		
			        		$('#txt_cash').val('');//WR20052013 เลือกปรเภทการชำระได้อย่างใดอย่างหนึ่งเท่านั้น ณ ตอนนี้
			        		calPaid();
			        		dlgAlipay();
			        		return false;
				        }
				        							       
					});//click	
					
					$("#btn_payment_kalipay").click( function(evt){
						//check if offline or online mode			
						
						evt.preventDefault();
			        	var online_mode=false;
			        	if(!online_mode){		
			        		$('#txt_cash').val('');//WR20052013 เลือกปรเภทการชำระได้อย่างใดอย่างหนึ่งเท่านั้น ณ ตอนนี้
			        		calPaid();
			        		dlgkAlipay('kalipay');
			        		return false;
				        }
				        							       
					});//click	
					
					$("#btn_payment_wechat").click( function(evt){
						//check if offline or online mode			
						
						evt.preventDefault();
			        	var online_mode=false;
			        	if(!online_mode){		
			        		$('#txt_cash').val('');//WR20052013 เลือกปรเภทการชำระได้อย่างใดอย่างหนึ่งเท่านั้น ณ ตอนนี้
			        		calPaid();
			        		dlgkAlipay('wechat');
			        		return false;
				        }
				        							       
					});//click
					
				}
			);			        							
		},
		buttons: {
		 	"ปิด":function(evt){
		 		evt.preventDefault();
		 		evt.stopPropagation();
		 		$('#dlgMobilePayment').dialog('close');
				return false;
		 	}
	    },
		close: function(evt,ui) {
			$('#dlgMobilePayment').dialog('destroy');
	    }
	};			
		
	$('#dlgMobilePayment').dialog('destroy');
	$('#dlgMobilePayment').dialog(dialogOpts_mobilepayment);			
	$('#dlgMobilePayment').dialog('open');
	return false;
}//func

function dlgkAlipay(pch){
	/**
	*@desc
	*@return
	*/
	//dlg-credit
	var pch = pch;
	var dialogOpts_sel_alipay = {
			autoOpen: false,
			width:400,		
			height:580,	
			modal:true,
			resizable:true,
			position:['center','center'],
			title:"<span class='ui-icon ui-icon-person'></span>บัตรเครดิต",
			closeOnEscape:true,
			open: function(){
				$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#9a9bcd"});				        						
				$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');			        						
				$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#f2e5ff","color":"#666777"});
				$(this).dialog("widget").find(".ui-dialog-buttonpane")
                  .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#a2a3bc"});
				$("#dlgAlipay").html("");
				$("#dlgAlipay").load("/sales/cashier/paycase?actions=kalipay&source="+pch+"&now="+Math.random(),
					function(){
						/*
						if($("#csh_lock_status").val()=='Y'){
							lockManualKey();
						}else{
							unlockManualKey();
						}
						*/
						var cnet=parseFloat($("#txt_net").val().replace(/[^\d\.\-\ ]/g,''));
						//$('#txt_credit_value').ForceNumericOnly();
						$("#txt_kalipay_no_value").focus();
						$("#txt_kalipay_no_value").keypress(function(evt){
							var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							if(key == 13) {
								$("#txt_alipay_value").focus();
							}
						});
						$("#txt_alipay_value").keypress(function(evt){
							var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							if(key == 13) {
								$("#btn_alipay_ok").focus();
							}
						});
						displayDigit($('#txt_netvt').val());
						if($('#txt_credit').val()!='0.00'){
		        			$('#txt_alipay_value').val($('#txt_credit').val());
		        		}else{
							$("#txt_alipay_value").val(cnet);
						}
					}
				);			        							
			},
			buttons: [{ //*WR18082012
				// $("#btn_payment_confirm").trigger("click");
				text: "ตกลง",
				id : "btn_kalipay_ok",
				class: 'ui-btndlgpos', 
				click :function(evt){
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
			 				url:'/sales/cashier/kbanpay',
			 				data:{
			 					txt_credit_no_value:$('#txt_alipay_no_value').val(),
			 					txt_credit_value : $('#txt_alipay_value').val(),
			 					txt_credit_source : $('#txt_alipay_source').val(),
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
									$('#payment_channel').val(pch);
									
									$('#dlgAlipay').dialog('close');
									$('#dlgMobilePayment').dialog('close');
									$("#btn_payment_confirm").trigger("click");
									return false;
			 					}else if(v[0]=="u"){
			 						//========================================================================
			 							var v1 = v[1].split("#");
			 							var opts_dlgAlipayConfirm={
			 							    modal: true,
			 							    title:"ยืนยันการชำระเงินด้วย Alipay",
			 							    width:'33%',
			 							    height:'auto',
			 							    open:function(){
			 									$("#dlgAlipayConfirm").load("/sales/cashier/kalipayconfirmform?partner_transaction_id="+v1[0]+"&kbank_id="+v1[1],function(){
			 										
			 										$('#btn_payment_yes').click(function(){
			 											$.ajax({
			 								 				type:'post',
			 								 				url:'/sales/cashier/kconfirmalipay',
			 								 				data:{
			 													partner_transaction_id : v1[0],
			 													kbank_id : v1[1],
			 													source : pch,
			 								 					rnd:Math.random()
			 								 				},success:function(data){
			 								 					var vv = data.split("||");
			 								 					if(vv[0]=="y"){
			 								 						$('#txt_credit_no').val(credit_no_value);
			 												 		var txt_alipay_value=$("#txt_alipay_value").val();
			 														$("#txt_credit").val(txt_alipay_value);
			 														
			 														//payment_transid
			 														$('#payment_transid').val(vv[1]);
			 														$('#payment_channel').val(pch);
			 														
			 														$('#dlgAlipay').dialog('close');
			 														$('#dlgAlipayConfirm').dialog('close');
			 														$('#dlgMobilePayment').dialog('close');
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
			 								 				url:'/sales/cashier/kcancelalipay',
			 								 				data:{
				 												partner_transaction_id : v1[0],
			 													kbank_id : v1[1],
			 													source : pch,
			 								 					rnd:Math.random()
			 								 				},success:function(data){
			 								 					
			 								 					if(data=="y"){
			 								 									 														
			 														$('#dlgAlipay').dialog('close');
			 														$('#dlgAlipayConfirm').dialog('close');
			 														$('#dlgMobilePayment').dialog('close');
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
			 								},
			 								close:function(){
			 									$("#dlgAlipayConfirm").dialog("destroy");
			 								}
			 							};
			 							$("#dlgAlipayConfirm").dialog(opts_dlgAlipayConfirm);
			 							$('#dlgAlipayConfirm').dialog('open');
			 							
			 						//========================================================================
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
			 	}
		    }],
			close: function(evt,ui) {
				calPaid();
				$("#btn_payment_alipay").removeClass("ui-state-focus ui-state-hover ui-state-active");
				$('#dlgAlipay').dialog('destroy');
			 }
			};			
		
		$('#dlgAlipay').dialog('destroy');
		$('#dlgAlipay').dialog(dialogOpts_sel_alipay);			
		$('#dlgAlipay').dialog('open');
		return false;
}//func


function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}