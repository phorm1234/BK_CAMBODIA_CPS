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
		
//	    $(".keybarcode").each(function(){
//	    	$(this).keypress( function(evt) {
//				var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
//				var objBarcode=$(this);
//		        if(key == 13){
//		        	if($(this).val().length <6 && $(this).val().length > 13){
//						return false;
//					}
//		        	stopTimerKeyBarcode();
//		        	return false;
//		        }else{
//						if((key!=8 && key!=0) && (key<48) || (key>57)){
//							stopTimerKeyBarcode();
//						    clear_timer_keybarcode=setTimeout(function(){clsBarCode(objBarcode)},100);
//						}else{
//							stopTimerKeyBarcode();
//						    clear_timer_keybarcode=setTimeout(function(){clsBarCode(objBarcode)},100);
//						}
//		        } 
//			});	    	
//		});
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
	
	function initRwdTemp(){
		var opts_inittmp={
				type:'post',
				url:'/sales/member/initrwdtemp',
				cache:false,
				data:{
					rnd:Math.random()
				},success:function(){
				}
			};
		$.ajax(opts_inittmp);
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
	
	function initForm(){
		//GET LOCK STATUS
		getLockStatus();
		
		$("#rwd_member_no").val('');
		$("#rwd_point_sum").val('');
		$("#rwd_fullname").val('');
		$("#rwd_address").val('');

		$("#rwd_sum_quantity").val('0');
		$("#rwd_sum_amount").val('0');
		$("#rwd_promo_code").val('');
		$("#rwd_point_used").val('0');
		$("#rwd_product_id").val('');
		$("#rwd_quantity").val('');	

		$("#rwd_point_used_balance").val('0');//เก็บ ณ ปัจจุบันใช้คะแนนไปเท่าไร

		$('#rwd_doc_tp').html('');	
		$('#rwd_doc_tp_vt').val('');
		$('#rwd_doc_tp_vs').val('');
		$("#rwd_product_set").val('');
		$("#rwd_fix_quantity").val('');
		
		
		$("#rwd_promo_quantity").val('0');
		$("#rwd_promo_amount").val('0');
		
		$("#rwd_redeem_point").val('');//store point redeem
		$("#rwd_promo_point_sum").val('0');

		$("#rwd_promo_code").removeAttr('readonly');
 		$("#rwd_product_id").removeAttr('readonly');

		$("#rwd_promo_code").removeClass('input-text-pos-disabled').addClass('input-text-pos');
 		$("#rwd_product_id").removeClass('input-text-pos-disabled').addClass('input-text-pos');
	
		$("#bws_promocode").removeClass('btnGrayDisabled').addClass('btnGrayClean');
 		$("#bws_product").removeClass('btnGrayDisabled').addClass('btnGrayClean');
 		
 		$("#rwd_member_no").removeAttr('readonly');
 		$("#rwd_status_no").removeAttr('readonly');
 		$("#rwd_member_no").removeClass('input-text-pos-disabled').addClass('input-text-pos');
 		$("#rwd_status_no").removeClass('input-text-pos-disabled').addClass('input-text-pos'); 	
		
		$("#rwd_status_no").val('');
		$("#rwd_status_no").focus();
	}//func
	
	//////////////////////////////******************************
	function showFomVT(){
		    var dialogOpts_vt = {
					autoOpen: false,
					width:'30%',		
					height:'auto',	
					modal:true,
					resizable:true,
					position:"center",
					showOpt: {direction: 'up'},		
					closeOnEscape:true,	
					title:"<span class='ui-icon ui-icon-document-b'></span>ขอใบกำกับภาษีเต็มรูป",
					open: function(){   
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"5px 5px","margin-top":"0","background-color":"#BCDCD7",
		    			    										"font-size":"27px","color":"#000"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	    			    // button style		
	    			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
						$(this).find('div,button,select, input, textarea').blur();
						$('.ui-dialog-buttonpane button:last').blur();
						$("#dlgVatTotal").html("");
						$("#dlgVatTotal").load("/sales/cashier/vattotal?now="+Math.random(),
						function(){	
							///////////////test key enter vat total////////////////////////
							$("#vt_name").focus();
							$("#vt_name").keypress( function(evt) {
						        var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
						        if(key == 13) {
						            evt.preventDefault();
						            if(jQuery.trim($("#vt_name").val())==''){
						            	 jAlert('กรุณาระบุชื่อ','ข้อความแจ้งเตือน',function(){
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
							///////////////test key enter vat total////////////////////////
							var vt_name_val=$("#vt_name_val").val();
							var vt_address_val1=$("#vt_address_val1").val();
							var vt_address_val2=$("#vt_address_val2").val();
							var vt_address_val3=$("#vt_address_val3").val();
							$("#vt_name").val(vt_name_val);
							$("#vt_address1").val(vt_address_val1);
							$("#vt_address2").val(vt_address_val2);
							$("#vt_address3").val(vt_address_val3);
						});
					},				
					close: function() {
						$('#btnVT').removeClass('ui-state-focus ui-state-access').addClass('ui-state-default');
					 },
					buttons: {							
							"ตกลง":function(){ 	
								var vt_name=$("#vt_name").val();			
								var vt_address1=$("#vt_address1").val();
								var vt_address2=$("#vt_address2").val();
								var vt_address3=$("#vt_address3").val();
								if(jQuery.trim(vt_name)!="" && jQuery.trim(vt_address1)!=""){
									$("#csh_doc_tp_show").html("บิลเต็มรูปแแบบ");
									$("#flgbill").html("{ใบกำกับภาษี}");
									$("#csh_doc_tp_vt").val("VT");
								}else{
									$("#flgbill").html("");
									$("#csh_doc_tp_vt").val("");
								}
								$("#vt_name_val").val(vt_name);
								$("#vt_address_val1").val(vt_address1);		
								$("#vt_address_val2").val(vt_address2);
								$("#vt_address_val3").val(vt_address3);
								$(this).dialog("close");
								return false;
							}
						 }
					};	
					$('#dlgVatTotal').dialog('destroy');
					$('#dlgVatTotal').dialog(dialogOpts_vt);			
					$('#dlgVatTotal').dialog('open');
			return false;
	}//func	
	/////////////////////////////*******************************

	function getRwdTemp(){	
		/*
		 *@desc 
		 */
		$.ajax({
			type:'post',
			url:'/sales/member/getpages',
			cache: false,
			data:{				
				rp:14,
				now:Math.random()
			},
			success:function(data){	
				$("#tblRWD").flexOptions({newp:data}).flexReload();
				getSubTotalRwd();
				return false;
			}
		});
	}//func

	function getSubTotalRwd(){
		/**
		*@name getSubTotalCn
		*@desc 
		*/
		$.getJSON(
				"/sales/member/subtotalrwd",
				{
					actions:'getSumRwdTemp'
				},
				
				function(data){
						$.each(data.sumcn, function(i,m){
							if(m.exist=='yes' && parseInt(m.sum_net_amt)!=0){
								if(isNaN(m.sum_quantity)){
									var rwd_sum_quantity=0;
									var rwd_sum_amount=0.00;
								}else{
									var rwd_sum_quantity=parseInt(m.sum_quantity);
									var rwd_sum_amount=parseFloat(m.sum_amount);
								}								
								rwd_sum_amount=rwd_sum_amount.toFixed(2);
								$('#rwd_sum_quantity').val(rwd_sum_quantity);
								$('#rwd_sum_amount').val(addCommas(rwd_sum_amount));								
							}else if(parseInt(m.sum_net_amt)==0){
								if(isNaN(m.sum_quantity) || m.sum_quantity==''){
									$('#rwd_sum_quantity').val('0');
								}else{
									var rwd_sum_quantity=parseInt(m.sum_quantity);
									$('#rwd_sum_quantity').val(rwd_sum_quantity);
								}
								$('#rwd_sum_amount').val('0.00');
							}else{
								$('#rwd_sum_quantity').val('0');
								$('#rwd_sum_amount').val('0.00');
							}	
							
						});//foreach
						return false;
				}//
		);
		
	}//func

	function flgCommand(com,grid){
		if(com=='Refresh'){
			$("#tblRWD").flexOptions({newp:1}).flexReload();
		}//if refresh
		if (com == 'Delete') {
			var del_list='';
			var del_list_show='';
			$('.trSelected', grid).each(function() {
				del_list+= $(this).attr('absid')+"#";
				del_list_show+=$(this).attr('id').substr(3)+",";
			});
			del_list=del_list.substring(0,del_list.length-1);
			del_list_show=del_list_show.substring(0,del_list_show.length-1);
			jConfirm('คุณต้องการลบรายการ '+del_list_show+' ใช่หรือไม่?','ยืนยันการลบรายการ', function(r){
		        if(r){
					var opts={
							   type:"POST",
							   cache:false,
							   url: "/sales/member/delrwd",
							   data:{
						   				items:del_list,
						   				action:'removeFormGrid'
						   			},
							   success: function(data){
										if(data=='-1'){
											 jAlert('ไม่สามารถลบรายการดังกล่าวได้','ข้อความแจ้งเตือน',function(){
												 return false;
											  });
										}else{
											var repoint=parseInt($("#rwd_point_used").val()-parseInt(data));
											$("#rwd_point_used").val(repoint);
											getRwdTemp();
										}
							   }
							};
					$.ajax(opts);
		        }
			});
	        if(del_list==''){
	        	 jAlert('กรุณาเลือกรายการที่ต้องการลบ','ข้อความแจ้งเตือน',function(){
					 return false;
				  });
		    }
		}//if delete
	}//func

	function addProductItem($promo_code,$product_id,$quantity){
		var $fix_quantity=$("#rwd_fix_quantity").val();
		var $quantity=parseInt($quantity);
		var $status_no=$("#rwd_status_no").val();
		var $amount=$("#rwd_promo_amount").val();
		if($quantity==0) return false;
		$.ajax({
		 	type:'post',
		 	url:'/sales/member/addproductitem',
		 	cache:false,
		 	data:{
		 		promo_code:$promo_code,
		 		product_id:$product_id,
		 		fix_quantity:$fix_quantity,
		 		quantity:$quantity,
		 		amount:$amount,
		 		status_no:$status_no,
		 		action:'rwdproductitem',
		 		rnd:Math.random()
		 	},
		 	success:function(data){
			 	if(data=='1'){
				 	getRwdTemp();
				 	$('#rwd_quantity').val('1');
				 	$('#rwd_product_id').val('').focus();
				 }
			}
		});
	}//func	

	function addProductSet($promo_code,$qty,$status_no){
		var $rwd_quantity=$("#rwd_quantity");
		var $quantity=parseInt($qty);
		var $amount=$("#rwd_promo_amount").val();
		if($quantity==0) return false;
		$.ajax({
		 	type:'post',
		 	url:'/sales/member/addproductset',
		 	cache:false,
		 	data:{
		 		promo_code:$promo_code,
		 		quantity:$quantity,
		 		amount:$amount,
		 		status_no:$status_no,
		 		action:'rwdproductset',
		 		rnd:Math.random()
		 	},
		 	success:function(data){
			 	if(data=='1'){
				 	getRwdTemp();
				 	$rwd_quantity.focus();
				 }else if(data=='2'){
					 jAlert('รหัสสินค้านี้สต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){	
							return false;							
						});	
				}
			}
		});
	}//func

	function checkMember(){
		var $rwd_status_no=$("#rwd_status_no");
		var status_no=jQuery.trim($rwd_status_no.val());
		var $rwd_member_no=$("#rwd_member_no");
		var member_no=$.trim($rwd_member_no.val());
		if(member_no.length==0){
			jAlert('กรุณาระบุรหัสสมาชิก','ข้อความแจ้งเตือน',function(){
				$rwd_member_no.focus();
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
						if(data==0){
							jAlert('รหัสสมาชิกนี้บัตรหมดอายุกรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
								$rwd_member_no.focus();
								return false;
							});	
						}else if(data=='2'){
							jAlert('ไม่พบรหัสสมาชิกนี้กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
								$rwd_member_no.focus();
								return false;
							});
						}else if(data=='3'){
							jAlert('รหัสสมาชิกนี้ถูกกำหนดให้มีสถานะเป็นบัตรเสียหรือบัตรหาย\n กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
								$rwd_member_no.focus();
								return false;
							});	
						}else{
							$.each(data.member, function(i,m){
								if(m.exist=='yes'){
									
									$('#rwd_member_no').attr('readonly',true);
				             		$('#rwd_member_no').removeClass('input-text-pos').addClass('input-text-pos-disabled');
				             		$('#rwd_status_no').attr('readonly',true);
				             		$('#rwd_status_no').removeClass('input-text-pos').addClass('input-text-pos-disabled');
									
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
									$.trim(m.zip)+" "+
									$.trim(m.mobile_no);			
									
									$('#rwd_fullname').val(member_fullname);
									$('#rwd_point_sum').val(mp_point_sum);
									$('#rwd_promo_point_sum').val(mp_point_sum);
									$('#rwd_address').val(address);
								
								}
								//----------- start rwd---------
								$("#rwd_promo_code").focus();
								//----------- end rwd ----------						
							});
							
						
						}
						
					}				
			);//end json
			
		}//end if	
		return false;		
	}//func

	function selProductItem(promo_code,fix_quantity,quantity){
		if(promo_code =='') return false;
		var opts_dlgRwdProduct={
				autoOpen:false,
				width:'70%',
				modal:true,
				resizeable:true,
				position:'center',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"เลือกสินค้า",
				open:function(){					
				  $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
				  $(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
				    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
					    																			"font-size":"26px","color":"#444444",
					    																			"padding":"0 0 0 0"});	
					$("*:focus").blur();
					$("#dlgRwdProduct").html("");
					$.ajax({
						type:'post',
						url:'/sales/member/rwdproduct',
						cache:false,
						data:{
							promo_code:promo_code,
							action:'rwdproduct',
							now:Math.random()
						},
						success:function(data){
							$("#dlgRwdProduct").html(data);
							$(this).parent().find('select, input, textarea').blur();
							$('.tableNavRwdProduct ul li').not('.nokeyboard').navigate({
						        wrap:true
						    }).click(function(evt){		
							    evt.preventDefault();
							    var selproduct=$.parseJSON($(this).attr('idproduct'));
							    $("#dlgRwdProduct").dialog("close");
							    /*
							    if(fix_quantity=='Y'){
								    addProductItem(promo_code,selproduct.product_id,quantity);								   
								}else{
									$('#rwd_product_id').val(selproduct.product_id);
								    $('#rwd_quantity').focus();
								}
								*/
							    $('#rwd_product_id').val(selproduct.product_id);
							    $('#rwd_promo_amount').val(selproduct.amount);
							    $('#rwd_quantity').focus();
							});
							
						}//end success function
					});//end ajax pos
					
				},
				close:function(){
					$('.tableNavRwdProduct ul').navigate('destroy');
				}
				
		};
		$("#dlgRwdProduct").dialog("destroy");
		$("#dlgRwdProduct").dialog(opts_dlgRwdProduct);
		$("#dlgRwdProduct").dialog("open");
	}//func

	function setRwdItem(srcEvt){
		var $rwd_promo_code=$('#rwd_promo_code');//รหัสโปรโมชั่น
        var $rwd_point_sum=$('#rwd_point_sum');//คะแนนสะสม
        var $rwd_point_used=$('#rwd_point_used');//คะแนนที่ใช้
        var $rwd_redeem_point=$('#rwd_redeem_point');//คะแนนที่ใช้แรกในตาราง promo_point3_head
        var $rwd_quantity=$('#rwd_quantity');//จำนวนที่ key ใน form
        //var $rwd_sum_quantity=$('#rwd_sum_quantity');//จำนวนรวม        
        var $rwd_product_id=$('#rwd_product_id');//รหัสสินค้าที่ key ใน form
        var $rwd_fix_quantity=$("#rwd_fix_quantity");//สถานะมีการ fixed จำนวนที่เล่นหรือไม่
        var $rwd_promo_quantity=$("#rwd_promo_quantity");//จำนวน ในตาราง promo_point3_head
        var $rwd_promo_point_sum=$("#rwd_promo_point_sum");//คะแนน ที่ใช้แลกในตาราง promo_point3_head
    	var $rwd_point_check=parseInt($rwd_quantity.val())*parseInt($rwd_redeem_point.val());//คะแนนที่ใช้จากการ key จำนวน=จำนวน*คะแนนที่ใช้แลก
    	var $uc=parseInt($("#rwd_point_used_balance").val())+$rwd_point_check;//คะแนนที่ใช้=คะแนนที่ใช้ไปสะสม+คะแนนที่ใช้จากการ key จำนวน		
       
        if(parseInt($.trim($rwd_quantity.val()))<1){
        	jAlert('กรุณาระบุจำนวนสินค้าต้องอย่างน้อย 1 รายการ', 'ข้อความแจ้งเตือน',function(){
         	$rwd_quantity.focus();
				return false;
			});
			return false;
	    }
	        
        if($("#rwd_fix_quantity").val()=='Y'){
	        var opts_chk={
			        type:'post',
			        url:'/sales/cashier/countqtytemp',
			        data:{
		        		promo_code:$rwd_promo_code.val(),
		        		rnd:Math.random()
		        	},
		        	success:function(data){
			        	var dataqty=parseInt(data);
						var chk_qty=parseInt($rwd_quantity.val())+dataqty;//check จำนวนรายการที่ add เข้าเกินกว่าที่ fix ไว้หรือไม่
						if(chk_qty>parseInt($rwd_promo_quantity.val())){
			            	jAlert('คุณระบุจำนวนสินค้าเกินกว่าที่กำหนด', 'ข้อความแจ้งเตือน',function(){
			            		$rwd_quantity.focus();
								return false;
							});
							return false;
				        }else{
					        //start
				        	//alert("คะแนนที่จะใช้ --> "+$uc+" > คะแนนสะสม"+$rwd_point_sum.val());							
				        	if($uc > parseInt($rwd_point_sum.val())){	
				            	jAlert('คะแนนสะสมของสมาชิกไม่พอใช้แลก', 'ข้อความแจ้งเตือน',function(){
				            		$rwd_quantity.val('1').focus();
									return false;
								});
				            }else{
					            if($("#rwd_product_set").val()=='Y'){
				            		//จัดสินค้าเป็นชุด
							 		addProductSet($('#rwd_promo_code').val(),$('#rwd_quantity').val(),$("#rwd_status_no").val());
							 		if(chk_qty==parseInt($rwd_promo_quantity.val())){
				            			$("#rwd_point_used_balance").val($uc);
				                 		$rwd_point_used.val($("#rwd_point_used_balance").val());
				                 	
				                 		//$rwd_promo_code.attr('readonly',true);
				                 		$rwd_product_id.attr('readonly',true);
				                 		//$rwd_promo_code.removeClass('input-text-pos').addClass('input-text-pos-disabled');
				                 		$rwd_product_id.removeClass('input-text-pos').addClass('input-text-pos-disabled');
				                 		//$("#bws_promocode").removeClass('btnGrayClean').addClass('btnGrayDisabled');
				                 		$("#bws_product").removeClass('btnGrayClean').addClass('btnGrayDisabled');
				                 	
					                 	
				     	             }
					            }else{
					            	//จัดสินค้าเป็นชิ้น
					            	if($.trim($rwd_product_id.val()).length==0){
					            		if(srcEvt=='B'){
						            		jAlert('กรุณาระบุรหัสสินค้า','ข้อความแจ้งเตือน',function(){
							            		$rwd_product_id.focus();
							        			return false;
							        		});
							            }else{
					            			$rwd_product_id.focus();
					            			return false;
							            }
					            	}else{
					            		addProductItem($('#rwd_promo_code').val(),$('#rwd_product_id').val(),$('#rwd_quantity').val());
					            		if(chk_qty==parseInt($rwd_promo_quantity.val())){
					            			$("#rwd_point_used_balance").val($uc);
					            			//$("#rwd_sum_quantity").val(chk_qty);
					                 		$rwd_point_used.val($("#rwd_point_used_balance").val());
					     	            }
					            	}	 		
						        }//else
				            }//else
					    }//else
			        	
			        }
			    };
		    $.ajax(opts_chk);
            
        }else{
        	//----------------------------->start<------------------------------------------------
			//alert("คะแนนที่จะใช้ --> "+$uc+" > คะแนนสะสม"+$rwd_point_sum.val());
        	if($uc > parseInt($rwd_point_sum.val())){
            	jAlert('คะแนนสะสมของสมาชิกไม่พอใช้แลก','ข้อความแจ้งเตือน',function(){
            		$rwd_quantity.val('1').focus();
					return false;
				});
            }else{
	            if($("#rwd_product_set").val()=='Y'){
            		//จัดสินค้าเป็นชุด
			 		addProductSet($('#rwd_promo_code').val(),$('#rwd_quantity').val(),$("#rwd_status_no").val());
			 		if(parseInt($rwd_quantity.val())>=parseInt($rwd_promo_quantity.val())){
     	            	$("#rwd_point_used_balance").val($uc);
                 		$rwd_point_used.val($("#rwd_point_used_balance").val());
                 		//$rwd_promo_code.attr('readonly',true);
                 		$rwd_product_id.attr('readonly',true);
                 		//$rwd_promo_code.removeClass('input-text-pos').addClass('input-text-pos-disabled');
                 		$rwd_product_id.removeClass('input-text-pos').addClass('input-text-pos-disabled');
                 		//$("#bws_promocode").removeClass('btnGrayClean').addClass('btnGrayDisabled');
                 		$("#bws_product").removeClass('btnGrayClean').addClass('btnGrayDisabled');
     	             }
	            }else{
	            	//จัดสินค้าเป็นชิ้น
	            	if($.trim($rwd_product_id.val()).length==0){
		            	if(srcEvt=='B'){
		            		jAlert('กรุณาระบุรหัสสินค้า','ข้อความแจ้งเตือน',function(){
			            		$rwd_product_id.focus();
			        			return false;
			        		});
			            }else{
	            			$rwd_product_id.focus();
	            			return false;
			            }
	            	}else{
	            		addProductItem($('#rwd_promo_code').val(),$('#rwd_product_id').val(),$('#rwd_quantity').val());
	            	   	if(parseInt($rwd_quantity.val())>=parseInt($rwd_promo_quantity.val())){
	            			$("#rwd_point_used_balance").val($uc);
	                 		$rwd_point_used.val($("#rwd_point_used_balance").val());
	     	             }
	            	}	 		
		        }
		        	            
            }
	        //------------------------------>end<------------------------------------------------
	    }
	}//func

	//------------ start payment bill ---------------------------
	function calPaid(){
		/**
		*@desc
		*@return void
		*/
		 //var txtPoint=jQuery.trim($("#txt_redeem_point_cash").val().replace(/[^\d\.\-\ ]/g,''));	
		 var txtPoint=0.00;//ตอนนี้เอา txt_redeem_point_cash ออกก่อน
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
		 txtSumPaid=txtPoint+txtVoucher+txtCash+txtCredit;        
		 //alert(txtPoint+"+"+txtVoucher+"+"+txtCash+"+"+txtCredit); 
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
		 //displayDigit(txtCashBack);
	}//func

	function saveMe($status_no){
		if($status_no=='') return false;
		var opts={
				type:'post',
				url:'/sales/cashier/countdiarytemp',
				cache:false,
				data:{
					actions:'rewards',							
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');
					if(parseInt(arr_data[0])>0){	
					//if(parseInt(data)>0){
						paymentBill($status_no);
					}else{
						jAlert('ไม่พบการทำรายการ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							return false;
		    			});
				    	return false;
					}
				}
			};
		$.ajax(opts);		
	}//func
	
	function paymentBill(status_no){
		/**
		*@desc
		*@param String status_no :
		*@return void
		*/
		//start payment
		if(status_no=='') return false;
		var cn_amount=$("#cn_amount").val();		
		var dialogOpts_payment = {
				autoOpen: false,
				width:'50%',
				height:'auto',	
				modal:true,
				position:"top",
				resizable:true,
				show: 'slide',
				showOpt: {direction:'down'},
				closeOnEscape:true,				
				title:'<span class="ui-icon ui-icon-cart"></span>',
				open: function(){ 
						$(this).parent().find('.ui-dialog-titlebar').append('ชำระเงิน');
						
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#509193","color":"#000000"});					
					
						
						$("#dlgRwdPayment").empty();
						$("#dlgRwdPayment").load("/sales/cashier/paycase?actions=rwdpayment&status_no="+status_no+"&cn_amount="+cn_amount+"&now="+Math.random(),
						function(){	
							//test arrow key
							var maxControl=3;
							var at=new Array();
								at[0]="#txt_redeem_point";
							  	at[1]="#txt_voucher";
							  	at[2]="#txt_cash";
						  	var ac=new Array();
							  	ac[0]="#btn_payment_credit";
							  	ac[1]="#btn_vat_total";
								ac[2]="#btn_payment_confirm";
								ac[3]="#btn_payment_cancel";
								var at_index=2;
								var ac_index=0;
								var maxButton=3;
								var minButton=0;
								$('#dlgRwdPayment').keydown(function (e) {
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
									      	 	at_index=2;
									      	}
									      	//alert(at_index);
										    $(at[at_index]).focus();
										    ac_index=0;//clear position
									    break;
									  }
									});					
								//test arrow key
								$("#txt_cash").ForceNumericOnly();
								//init button style
								$(".btn_cmd_brows_2").button().css({width:'70',height:'52'});							
								$('#btn_payment_confirm').button({
							        icons: { primary: "btn_payment_confirm"}
							    });

								$('#btn_payment_cancel').button({
							        icons: { primary: "btn_payment_cancel"}
							    });

								$('#btn_payment_credit').button({
							        icons: { primary: "btn_payment_credit"}
							    });
								
								$('#btn_vat_total').button({
							        icons: { primary: "btn_vat_total"}
							    });
								
								//alert($("#txt_netvt").val());
								var application_id=jQuery.trim($("#csh_application_id").val());
								var net_total_payment="";
								var csh_register_free=jQuery.trim($("#csh_register_free").val());
								//สินค้าพรีสำหรับชุดสมัคร *** 07022012 ปรับปรุงตาม flow ใหม่รอบรับสินค้าแถมกับสินค้า premium
								if(csh_register_free=='Y' && parseInt($("#txt_netvt").val())==0){
									net_total_payment="0.00";
								}else{
									net_total_payment=$("#txt_netvt").val();
								}
								$("#net_total_payment").html(net_total_payment);
								$("#txt_net").val(net_total_payment);
								
								//$(this).find('select, input, textarea').first().blur();
								$(this).find('div,select, input, textarea').blur();
								$(this).find("#txt_cash").focus()
								
								$("#txt_cash").keypress( function(evt) {									
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13){
							            evt.preventDefault();
							            var txt_cash=jQuery.trim($("#txt_cash").val());
							            if(txt_cash=='') return false;
							            calPaid();
							            $("#btn_payment_confirm").focus();					           
							            return false;
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

						
							
								$("#txt_voucher").keypress( function(evt) {
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
								         var voucher_cash=0.00;//assume 200.00
								         $("#txt_voucher_cash").val(voucher_cash);
								         calPaid();
								         $("#btn_payment_confirm").focus();
							        	 return false;
							        }
							    });//keypress

								$("#txt_voucher").focusout(function(){
									var voucher_cash=200.00;//assume
									var txt_voucher_show=$("#txt_voucher_show").val();
									if(isNaN(txt_voucher_show)){
										return false;
									}
							        $("#txt_voucher_cash").val(voucher_cash);
							    	calPaid();           
							        return false;
								});//focusout
								
								$("#btn_payment_confirm").click( function(e){
									e.preventDefault();
									var chk_net_curr=parseFloat($("#txt_net").val());								
							        if(chk_net_curr!=0){
							        	$("#btn_payment_confirm").removeClass("ui-state-focus ui-state-hover ui-state-active");
							        	jAlert('จำนวนเงินชำระไม่ครบตามยอดสุทธิ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
											$("#txt_cash").focus();
						    			});
								    	return false;
								    }else{
								       //--------------CHECK SALEMAN------------------
										var dialogOpts_saleman = {
												autoOpen: false,
												width:275,
												height:'auto',	
												modal:true,
												stack: true,
												resizable:true,
												position:"center",
												showOpt: {direction: 'down'},		
												closeOnEscape:false,	
												title:"<span class='ui-icon ui-icon-person'></span>ระบุผู้ขาย",
												open: function(){ 
													$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
													$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
													$(this).dialog('widget')
											            .find('.ui-dialog-titlebar')
											            .removeClass('ui-corner-all')
											            .addClass('ui-corner-top');
									    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb",
									    			    																				"margin":"0 0 0 0","font-size":"27px","color":"#000","height":"50px"});
									    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
									    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
								    			    
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
														        	evt.stopPropagation();
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
																						$("#chk_saleman_id").focus();
																						return false;
																	    			});
																				}else if($.trim(arr_data[3])=='P'){
																					jAlert('ขณะนี้พนักงานขายไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
																						$("#chk_saleman_id").focus();
																						return false;
																	    			});
																				}else if($.trim(arr_data[3])=='N'){
																					jAlert('ขณะนี้พนักงานขายไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
																						$("#chk_saleman_id").focus();
																						return false;
																	    			});
																				}else{
																	            	$("#rwd_saleman_id").val(arr_data[0]);
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
										//-------------CHECK SALEMAN-----------------
								    }
						        	
								});//click
								
								$("#btn_payment_confirm").keypress( function(evt) {
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
								        var chk_net_curr=parseFloat($("#txt_net").val());
								        if(chk_net_curr!=0){
								        	$("#btn_payment_confirm").removeClass("ui-state-focus ui-state-hover ui-state-active");
								        	jAlert('จำนวนเงินชำระไม่ครบตามยอดสุทธิ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
												$("#txt_cash").focus();
							    			});
									    	return false;
									    }
								        $("#btn_payment_confirm").trigger("click");
							        	return false;
							        }
								});//keypress

								////////////////////start cancel //////////////////

								$("#btn_payment_cancel").click( function(e) {
									e.preventDefault();
						        	jConfirm('คุณต้องการยกเลิกการชำระเงินนี้ ใช่หรือไม่?','ยืนยันการยกเลิกการชำระเงิน', function(r){
									        if(r){
									        	$(".ui-dialog-titlebar-close").trigger('click');								     
												return false;
									        }
						        	});
							       
								});//keypress

								$("#btn_payment_cancel").keypress( function(evt) {
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
							        	$("#btn_payment_cancel").trigger('click');
							        	return false;
							        }
								});//keypress

								////////////////////end cancel //////////////////
								
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
									//alert(net_total_payment+"-"+txt_cash);
									var curr_total_payment=net_total_payment-txt_cash;
									//alert(credit_net_amt+' > '+curr_total_payment);
									if(parseFloat(credit_net_amt)<1){
										jAlert('กรุณาระบุจำนวนเงินชำระบัตรเครดิต','ข้อความแจ้งเตือน',function(){
											$("#txt_credit_value").focus();
											return false;
						    			});
									}else if(parseFloat(credit_net_amt)>curr_total_payment){
										jAlert('จำนวนเงินชำระบัตรเครดิตมากกว่ายอดสุทธิที่ต้องชำระ','ข้อความแจ้งเตือน',function(){
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
												},success:function(){
													
												}
										};
													
										jAlert('กรุณารูดหรือเสียบบัตรเครดิต','ข้อความแจ้งเตือน',function(r){
											$('#popup_ok').focus();
											if(r){
												$.ajax(opts_edc);
											}
											$('#btn_payment_confirm').trigger('click');
						    			});
										
									}								
									return false;
								}//func

							
							////////////////////start credit //////////////////
								function dlgCredit(){
									/**
									*@desc
									*@return
									*/
									//dlg-credit
				        			var dialogOpts_sel_credit = {
				        					autoOpen: false,
				        					width:400,		
				        					height:280,	
				        					modal:true,
				        					resizable:true,
				        					position:['center','center'],
				        					title:"<span class='ui-icon ui-icon-person'></span>บัตรเครดิต",
				        					closeOnEscape:true,
				        					open: function(){ 
					        						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					        						//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
					        						$(this).dialog('widget')
					        				            .find('.ui-dialog-titlebar')
					        				            .removeClass('ui-corner-all')
					        				            .addClass('ui-corner-top');			        						
				        							$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7","color":"#000"});
				        							$(this).dialog("widget").find(".ui-dialog-buttonpane")
				    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
				        							$("#dlgCredit").html("");
													$("#dlgCredit").load("/sales/cashier/paycase?actions=wincredit&now="+Math.random(),
															function(){
																var cnet=parseFloat($("#txt_net").val().replace(/[^\d\.\-\ ]/g,''));
																$('#txt_credit_value').ForceNumericOnly();
																//*WR18082012
																if($('#txt_credit').val()!='0.00'){
												        			$('#txt_credit_value').val($('#txt_credit').val()).focus();
												        		}else{
							        								$("#txt_credit_value").val(cnet).focus();
																}
							        							//$("#sel_credit").focus();
							        							$("#sel_credit").keypress(function(e){
							        								var key = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
							        								if(key==13){
								        								$("#txt_credit_value").focus();
								        								return false;		
								        							}
								        						});
							        							$("#txt_credit_value").keypress(function(evt){
							        								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        								if(key==13){
							        									evt.stopPropagation();
							        									evt.preventDefault();
							        									var txt_credit_value=$("#txt_credit_value").val();
							        									$("#txt_credit").val(txt_credit_value);
							        									callEDC(txt_credit_value);//*WR20120827
							            								$('#dlgCredit').dialog('close');
							        								}
							        							});//keypress
															}
													);
				        							
				        					},
				        					buttons: {//*WR18082012
											 	"ตกลง":function(){
											 		var txt_credit_value=$("#txt_credit_value").val();
		        									$("#txt_credit").val(txt_credit_value);
		        									callEDC(txt_credit_value);//*WR20120827
		            								$('#dlgCredit').dialog('close');
											 	}
							    		    },
				        					close: function(evt,ui) {
				        						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
				        							 if(evt.target!=this){
				        								return true;
				        							}
				        						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
					        						//is ok to stop bublig event for firefox
					        						evt.stopPropagation();
					        						evt.preventDefault();
				        							 if(evt.target!=this){
				        								return true;
				        							}
				        						}
				        						calPaid();
				        						$("#btn_payment_credit").removeClass("ui-state-focus ui-state-hover ui-state-active");
				        						//$("#btn_payment_confirm").focus();
				        					 }
				        					};			
				        				
				        				$('#dlgCredit').dialog('destroy');
				        				$('#dlgCredit').dialog(dialogOpts_sel_credit);			
				        				$('#dlgCredit').dialog('open');
				        				return false;
						        	
								}//func

								$("#btn_payment_credit").click( function(){								
									//check if offline or online mode						        	
						        	var online_mode=false;
						        	if(!online_mode){					        		
							        	dlgCredit();
						        		return false;
							        }							       
								});//click
								
								
								$("#btn_vat_total").click(function(evt){
									evt.preventDefault();
									showFomVT();
									return false;
								});
								
								$("#btn_vat_total").keypress( function(evt) {
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
								        evt.preventDefault();
								        showFomVT();
							        	return false;
							        }
								});//keypress

								$("#btn_payment_credit").keypress( function(evt) {
									var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
							        if(key == 13) {
								        evt.preventDefault();
										dlgCredit();
							        	return false;
							        }
								});//keypress
						

							////////////////////end cancel //////////////////
							function popup(url,name,windowWidth,windowHeight){
								/**
								*@desc
								*@param String url
								*@param String name
								*@param windowWidth
								*@param windowHeight
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
								
								
								if(thermal_printer=='Y'){
									var url2print="/sales/report/billvatshort";
									if(status_no=='03'){
										url2print="/sales/report/billecouponemployee";
									}else{
										if(doc_tp=='DN'){
											url2print="/sales/report/billshortdn";
										}else if(doc_tp=='VT'){
											url2print="/sales/report/billvattotal";
										}
									}
								}else{
									var url2print="/sales/report/billvatshortdotmatrix";
									if(doc_tp=='DN'){
										url2print="/sales/report/billshortdndotmatrix";
									}else if(doc_tp=='VT'){
										url2print="/sales/report/billvattotaldotmatrix";
									}
								}
								
								//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 		
								//for print auto
								var opts={
									type:'get',
									url:url2print,
									cache:false,
									data:{
											doc_no:doc_no,
											rnd:Math.random()
										},
									success:function(){
											return false;
									}
								};
								$.ajax(opts);
								return false;
							}//func

							function printBill222(str_doc_no){	
								/**
								*@desc
								*@param str_doc_no
								*@return Boolean false
								*/
								var arr_docno=str_doc_no.split('#');
								var doc_no=arr_docno[1];	
								var thermal_printer=arr_docno[3];
								if(thermal_printer=='Y'){
									var url2print="/sales/report/billvatshort";
									if($("#csh_doc_tp_vt").val()=='VT'){
										url2print="/sales/report/billvattotal";
									}else if(parseFloat($("#rwd_sum_amount").val())==0.00 || $("#csh_register_free").val()=='Y'){
										url2print="/sales/report/billshortdn";
									}		
								}else{
									var url2print="/sales/report/billvatshortdotmatrix";
									if($("#csh_doc_tp_vt").val()=='VT'){
										url2print="/sales/report/billvattotaldotmatrix";
									}else if($("#csh_register_free").val()=='Y'){
										url2print="/sales/report/billshortdndotmatrix";
									}
								}
								
								//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 		
								//for print auto
								var opts={
									type:'get',
									url:url2print,
									cache:false,
									data:{
											doc_no:doc_no,
											rnd:Math.random()
										},
									success:function(){
											return false;
									}
								};
								$.ajax(opts);
								return false;
							}//func
							
							//////////////start save transaction/////////////		
							
							function saveTransaction(){
								/**
								 *@desc : for redeem point to recieve rewards
								 *@return : void
								 *@lastmodify : 28122011
								 */
								
								var user_id=$('#rwd_user_id').val();
								var cashier_id=$('#rwd_cashier_id').val();
								//var arr_saleman_id=$("#rwd_saleman_id").val().split('#');
								//var saleman_id=arr_saleman_id[0];
								var saleman_id=$('#rwd_saleman_id').val();
								var doc_tp='';//ประเภทเอกสาร
								var url="/sales/cashier/payment";
								if($("#rwd_doc_tp_vt").val()!=''){
									doc_tp=$("#rwd_doc_tp_vt").val();
								}else{
									doc_tp=$("#rwd_doc_tp_vs").val();
								}
								
								var application_id='';//รหัสชุดสมัคร
								var expire_date='';//วันบัตรหมดอายุ
								var status_no=$('#rwd_status_no').val();//รหัสประเภทบิล
								var member_no=$('#rwd_member_no').val();//รหัสสมาชิก
								var member_percent='';//ส่วนลดสมาชิก%								
								var refer_member_id='';//อ้างถึงรหัสสมาชิก?
								var card_status='';//สถานะบัตรเสีย/บัตรหาย
								var other_discount;//ส่วนลดอื่นๆ
								var net_amount=$('#txt_netvt').val().replace(/[^\d\.\-\ ]/g,'');//ตอนนี้ค่าไม่ได้ใช้เพราะ query เอาก่อน save
								var ex_vat_amt;//จำวนวนเงินก่อนหักส่วนลดที่ได้รับการยกเว้นภาษี
								var ex_vat_net;//จำวนวนเงินหลังหักส่วนลดที่ได้รับการยกเว้นภาษี
								var vat;//จำนวนเงินภาษีมูลค่าเพิ่ม (net_amount-ex_vat_net)*7/107
								var paid=$('#sel_credit').val();//ประเภทการชำระเงินใช้กรณีชำระผ่านบัตรเครดิตอย่างเดียว
								var pay_cash=$('#txt_cash').val();//จำนวนเงินที่ลูกค้าชำระเงินสด
								var pay_credit=$('#txt_credit').val();//จำนวนเงินที่ลูกค้าชำระบัตรเครดิต
								//var redeem_point=$("#txt_redeem_point_cash").val();//แลกคะแนน ตอนนี้ยังอิง csh_point_net
								var coupon_code=$('#txt_voucher').val();//รหัส coupon or voucher
								var pay_cash_coupon=$('#txt_voucher_cash').val();//จำนวนเงิน
								var credit_no;//หมายเลขบัตรเครดิต
								var credit_tp=jQuery.trim($("#sel_credit option:selected").text());//ประเภทบัตรเครดิต
								var bank_tp=jQuery.trim($("#sel_credit option:selected").text());//บัตรธนาคาร
								if(pay_credit=='0.00'){
									var credit_tp='';
									var bank_tp='';
								}
								
								var change=$('#txt_cash_back').val();//จำนวนเงินทอน	
								var name=$('#vt_name_val').val();
								var address1=$('#vt_address_val1').val();
								var address2=$('#vt_address_val2').val();
								var address3=$('#vt_address_val3').val();
								var csh_get_point='N';//สถานะได้รับคะแนนหรือไม่ N,Y
								var csh_point_receive=$("#rwd_point_receive").val();   //คะแนนที่ได้รับ
								var csh_point_used=$("#rwd_point_used").val();	     //คะแนนที่ใช้ 
								var csh_point_net=parseInt(csh_point_receive)-parseInt($("#rwd_point_used").val());      //คะแนนสุทธิ
								
								var opts={
						    			type:'post',
						    			url:url,
						    			cache: false,
						    			data:{
												user_id:user_id,
												cashier_id:cashier_id,
												saleman_id:saleman_id,
												doc_tp:doc_tp,
												status_no:status_no,
												member_no:member_no,
												member_percent:member_percent,
												refer_member_id:refer_member_id,
												net_amount:net_amount,
												ex_vat_amt:ex_vat_amt,
												ex_vat_net:ex_vat_net,
												vat:vat,
												paid:paid,
												pay_cash:pay_cash,
												pay_credit:pay_credit,
												//redeem_point:redeem_point,
												change:change,
												coupon_code:coupon_code,
												pay_cash_coupon:pay_cash_coupon,
												credit_no:credit_no,
												credit_tp:credit_tp,
												bank_tp:bank_tp,
												name:name,
												address1:address1,
												address2:address2,
												address3:address3,
												application_id:application_id,
												expire_date:expire_date,
												point_receive:csh_point_receive,
												point_used:csh_point_used,
												point_net:csh_point_net,
												get_point:csh_get_point,
												card_status:card_status,
												action:"savetransaction"
										},
						    			success:function(data){
											var arr_data=data.split('#');
											if(arr_data[0]=='1'){
												$("#dlgSaleMan").dialog("close");
												$("#dlgRwdPayment").dialog("close");
												printBill(data);
												jAlert('บันทึกข้อมูล '+arr_data[1]+' สมบูรณ์','ผลการบันทึก',function(){
													initRwdTemp();
													initForm();		
													getRwdTemp();
								    				return false;
									    		 });
											}else{
												jAlert('เกิดปัญหา\n'+arr_data[1]+'\n ไม่สามารถบันทึกได้','ผลการบันทึก',function(){
													$("#dlgSaleMan").dialog("close");
							    					return false;
									    		 });
											}
							    		}
						    		};
								$.ajax(opts);
							}//func
							//////////////end save transaction //////////////
						   
						});
				},				
				close: function(evt,ui){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){
					}
					$("#btn_cal_promotion").removeAttr("disabled");
					$('#btn_cal_promotion').removeClass('ui-state-focus ui-state-access ui-state-disabled').addClass('ui-state-default');
				 }
			};			
			$('#dlgRwdPayment').dialog('destroy');
			$('#dlgRwdPayment').dialog(dialogOpts_payment);			
			$('#dlgRwdPayment').dialog('open');
			return false;
		//end payment
	}//func
	//------------ end payment bill -----------------------------
	
	$(function(){
		initRwdTemp();
		initForm();
		//start control hot key
		shortcut.add("F10",function() {
			$("#btnSave").trigger('click');
		},{
			'type':'keypress',
			'propagate':true,
			'disable_in_input':false,
			'target':document
		});
		url='/sales/member/rwdtemp';
		$("#tblRWD").flexigrid({
			url: url,
			dataType: 'json',
			colModel : [
				{display: '#', name : 'idx', width : 40, sortable : true, align: 'center'},
				{display: 'รหัสโปรโมชั่น', name : 'promo_code', width : 100, sortable : true, align: 'center'},
				{display: 'รหัสสินค้า', name : 'product_id', width : 120, sortable : true, align: 'center'},
				{display: 'รายละเอียด', name : 'product_name', width : 260, sortable : true, align: 'left'},
				{display: 'จำนวน', name : 'quantity', width : 100, sortable : true, align: 'center'},
				{display: 'จำนวนเงิน', name : 'amount', width : 100, sortable : true, align: 'center'}
			],
			sortname: "id",
			sortorder: "asc",
			action:'gettmp',
			usepager:true,
			singleSelect:true,
			nowrap: false,
			title:'',
			useRp:true,
			rp:14,
			buttons : [
						{name:'Delete',bclass: 'flgBtnDelClass',onpress : flgCommand},
						{separator: true},
						{name:'Refresh',bclass:'flgBtnRefClass',onpress :flgCommand}					
					],	
			rp: 10,
			showTableToggleBtn: true,
			height:'320'
		});
		
		$("#rwd_member_no").keypress( function(evt){									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	        	$("#btnKeyMemberOk").trigger('click');
	            return false;
	        }
		});
		
		$("#btnKeyMemberOk").click(function(e){
			e.preventDefault();
	         checkMember();
	         return false;
		});

		$("#bws_promocode").click(function(e){
			e.preventDefault();
			if($(this).hasClass("btnGrayDisabled")) return false;
			//ตอนนี้กำหนดให้เปิดบิลได้ครั้งละหลายโปรโมชั่น		
			var $rwd_status_no=$("#rwd_status_no");	
			var $rwd_member_no=$("#rwd_member_no");
			var $rwd_status_no=$("#rwd_status_no");
			if($rwd_status_no.val()==''){
				jAlert('กรุณาระบุประเภทเอกสาร', 'ข้อความแจ้งเตือน',function(){
					$rwd_status_no.focus();
					return false;
				});	
				return false;
			}
			if($rwd_member_no.val()==''){
				jAlert('กรุณาระบุรหัสสมาชิก', 'ข้อความแจ้งเตือน',function(){
					$rwd_member_no.focus();
					return false;
				});	
				return false;
			}
			var opts_dlgRwdPromo={
					autoOpen:false,
					width:'60%',
					height:450,
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"<span class='ui-icon ui-icon-cart'></span>เลือกโปรโมชั่น",
					open:function(){		
						  $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						  $(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
						    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
							    																			"font-size":"26px","color":"#444",
							    																			"padding":"0 0 0 0"});	
						$("*:focus").blur();
						$("#dlgRwdPromo").html("");
						$.ajax({
							type:'post',
							url:'/sales/member/rwdpromohead',
							cache:false,
							data:{
								status_no:$rwd_status_no.val(),
								now:Math.random()
							},
							success:function(data){
								$("#dlgRwdPromo").html(data);
								$(this).parent().find('select, input, textarea').blur();
								$('.tableNavRwdPromo ul li').not('.nokeyboard').navigate({
							        wrap:true
							    }).click(function(evt){		
								    evt.preventDefault();
								    evt.stopPropagation();
								    var selpromo=$.parseJSON($(this).attr('idpromo'));
								    $("#rwd_promo_code").val(selpromo.promo_code);
								    $("#rwd_redeem_point").val(selpromo.point);								    
								    $("#rwd_status_no").val(selpromo.status_no);
								    $("#rwd_product_set").val(selpromo.product_set);
								    $("#rwd_fix_quantity").val(selpromo.fix_quantity);//Y or N
								    $("#rwd_promo_quantity").val(parseInt(selpromo.quantity));//hidden field
								    $("#rwd_quantity").val('1');//default field 1 to show
								    $("#dlgRwdPromo").dialog("close");
								    setRwdItem('A');
								    if(selpromo.product_set!='Y'){
								 		$("#rwd_product_id").removeAttr('readonly');
								 		$("#rwd_product_id").removeClass('input-text-pos-disabled').addClass('input-text-pos');
								 		$("#bws_product").removeClass('btnGrayDisabled').addClass('btnGrayClean');
									}
								});
							}//end success function
						});//end ajax pos
					},
					close:function(){
						$('.tableNavRwdPromo ul').navigate('destroy');
					}
					
			};
			$("#dlgRwdPromo").dialog("destroy");
			$("#dlgRwdPromo").dialog(opts_dlgRwdPromo);			
			$("#dlgRwdPromo").dialog("open");
		});

		$('#rwd_quantity').keypress( function(evt){									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	           	$('#btnAddItem').trigger('click');
	            return false;
	        }
		});
		
		$('#btnAddItem').click(function(e){
			e.preventDefault();
			setRwdItem('B');	      
           	//$('#rwd_quantity').val('1').focus();// reset quantity to init 1
			$('#rwd_quantity').val('1');// reset quantity to init 1
		});

		$('#btn_rwd_doc_tp').click(function(e){
			e.preventDefault();
			var opts_dlgRwdDocTp={
					autoOpen:false,
					width:'60%',
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"<span class='ui-icon ui-icon-cart'></span>เลือกโปรโมชั่น",
					open:function(){					
					  $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					  $(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#e7e7e7",
						    																			"font-size":"26px","color":"#444444",
						    																			"padding":"0 0 0 0"});	
						$("*:focus").blur();
						$("#dlgRwdPromo").html("");
						$.ajax({
							type:'get',
							url:'/sales/cashier/docstatus',
							cache:false,
							data:{
								actions:'brows_rwddocstatus',
								now:Math.random()
							},
							success:function(data){
								$("#dlgRwdDocTp").html(data);
								$(this).parent().find('select, input, textarea').blur();
								$('.tableNavDocStatus ul li').not('.nokeyboard').navigate({
							        wrap:true
							    }).click(function(evt){		
								    evt.preventDefault();	
								    var seldoc=$.parseJSON($(this).attr('idd'));
								    $('#rwd_status_no').val(seldoc.status_no);
								    $('#rwd_doc_tp').html(seldoc.description);
								    $('#rwd_doc_tp_vs').val(seldoc.doc_tp);		
								    $("#dlgRwdDocTp").dialog("close");	
								    $("#rwd_member_no").focus();							
								});
							}//end success function
						});//end ajax pos
					},
					close:function(){
						$('.tableNavDocStatus ul').navigate('destroy');
					}
			};
			$("#dlgRwdDocTp").dialog("destroy");
			$("#dlgRwdDocTp").dialog(opts_dlgRwdDocTp);			
			$("#dlgRwdDocTp").dialog("open");
		});//btn_rwd_doc_tp click

		$("#btnSave").click(function(e){
			e.preventDefault();			
			var csh_net=$.trim($("#rwd_sum_amount").val());
			csh_net=csh_net.replace(/[^\d\.\-\ ]/g,'');
			csh_net=parseFloat(csh_net);				
			//check fixed quantity complete or not

			$.ajax({
				type:'post',
				url:'/sales/member/chkrwdcomplete',
				data:{
					rnd:Math.random()
				},
				success:function(data){
					if(data==''){
						if($.trim($("#rwd_status_no").val())=='04'){
							//-----------------บิลสมาชิกแลกคะแนน---------------------------------
							saveMe('04');
						}else if($.trim($("#rwd_status_no").val())=='18'){
							//-----------------บิลแลกของรางวัล Premier Rewards--------------
							saveMe('18');
						}else if($.trim($("#rwd_status_no").val())=='51'){
							//-----------------แลกคะแนนของรางวัล CPS Shop-----------------
						}
					}else{
						var arr_chk=data.split('#');
						jAlert('โปรโมชั่น '+arr_chk[0]+' ต้องเล่นให้ครบ '+parseInt(arr_chk[1])+' ชิ้น กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							$("#rwd_quantity").focus();
							return false;
		    			});
					}
				}
			});
			return false;
			//---------- cal promotion -----------			
		});

		$("#rwd_product_id").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            var $rwd_promo_code=$("#rwd_promo_code");
	            var $rwd_product_id=$("#rwd_product_id");
	            if($rwd_promo_code.val()=='' || $rwd_product_id.val()==''){
		            return false;
		        }
	            $.ajax({
		            type:'post',
		            url:'/sales/member/chkrwdproduct',
		            data:{
		            	promo_code:$rwd_promo_code.val(),
		            	product_id:$rwd_product_id.val(),
		            	action:'chkrwdproduct'
		            },
		            success:function(data){
			            if(data=='N'){
			            	jAlert('ไม่พบรหัสสินค้าในโปรโมชั่นนี้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
								return false;
			    			});
				        }else{
					        $("#rwd_quantity").focus();
					    }
			        }
		        });
	            return false;
	        }
		});

		$("#bws_product").click(function(e){
			e.preventDefault();
			if($(this).hasClass("btnGrayDisabled")) return false;
			var $promo_code=$("#rwd_promo_code");
			var $fix_quantity=$("#rwd_fix_quantity");
			var $quantity=$("#rwd_quantity");
			if($promo_code.val()==''){
				jAlert('กรุณาระบุรายการโปรโมชั่น','ข้อความแจ้งเตือน',function(){
					$promo_code.focus();
					return false;
    			});
				return false;
			}
			selProductItem($promo_code.val(),$fix_quantity.val(),$quantity.val());
		});

		$("#btnCancel").click(function(e){
			e.preventDefault();
			jConfirm('คุณต้องการยกเลิกรายการแลกคะแนนใช่หรือไม่?', 'ยืนยันการยกเลิกบิล', function(r) {
		        if(r){
		        	var opts={
							type:"post",
							url:"/sales/cashier/ajax",
							cache: false,
							data:{
								actions:"cancelBill",
								now:Math.random()
							},
							success:function(data){
								initRwdTemp();
								initForm();		
								getRwdTemp();				
							}
						};
					$.ajax(opts);
		        }
			});
		});		
	
		
	});//ready