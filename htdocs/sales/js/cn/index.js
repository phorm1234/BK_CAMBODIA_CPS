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
//		        		//number only
//						if((key!=8 && key!=0) && (key<48) || (key>57)){
//							//return false;
//							stopTimerKeyBarcode();
//						    clear_timer_keybarcode=setTimeout(function(){clsBarCode(objBarcode)},100);
//						}else{
//							stopTimerKeyBarcode();
//						    clear_timer_keybarcode=setTimeout(function(){clsBarCode(objBarcode)},100);
//						}
//				} 
//			});	
//	    	
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
//	    	
//		 });
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
	} //func
	
	function saveTransCN(){
		/**
		 * @desc
		 * @return
		 */
		var cn_cashier_id=$.trim($("#cn_cashier_id").val());
		var cn_member_no=$.trim($("#cn_member_no").val());
		var cn_discount=$.trim($("#cn_discount").val());
		var cn_doc_no_ref=$.trim($("#cn_doc_no").val());
		var cn_status_no=$.trim($("#cn_status_no").val());
		var cn_sum_quantity=$.trim($("#cn_sum_quantity").val());
		var cn_fullname=$("#cn_fullname").val();
		var cn_remark=$("#cn_remark").val();
		var cn_address=$("#cn_address").val();	
		
		var acc_name='';
		var bank_acc='';
		var bank_name='';
		var tel1='';
		var tel2='';
		if(cn_status_no=='41'){
			 //case status_no=41
			 acc_name=$('#cn_acc_name').val();
			 bank_acc=$('#cn_bank_acc').val();
			 bank_name=$('#cn_bank_name').val();
			 tel1=$('#cn_tel1').val();
			 tel2=$('#cn_tel2').val();
		}
		if(cn_sum_quantity=='0'){
			jAlert('ไม่พบการทำรายการสินค้า ไม่สามารถบันทึกได้','ข้อความแจ้งเตือน',function(){
				$("#cn_product_id").focus();
				 	return false;
    		 });
		}else if(cn_doc_no_ref.length==0){
			jAlert('กรุณาระบุเลขที่เอกสารอ้างอิง','ข้อความแจ้งเตือน',function(){
				$("#cn_doc_no").focus();
				 	return false;
    		 });
		}else if(cn_status_no.length==0){
			jAlert('กรุณาระบุประเภทเอกสาร','ข้อความแจ้งเตือน',function(){
				$("#cn_status_no").focus();
				 	return false;
    		 });
		}
		var opts={
				type:'post',
				url:'/sales/cn/savecn',
				cache:false,
				data:{
					doc_no_ref:cn_doc_no_ref,
					status_no:cn_status_no,
					cashier_id:cn_cashier_id,
					member_no:cn_member_no,
					member_percent:cn_discount,
					cn_fullname:cn_fullname,
					cn_remark:cn_remark,		
					cn_address:cn_address,								
					acc_name:acc_name,
					bank_acc:bank_acc,
					bank_name:bank_name,
					tel1:tel1,
					tel2:tel2,
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');		
					if(arr_data[0]=='1'){
						printBillCN(data);
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
						jAlert('บันทึกข้อมูลสมบูรณ์ '+arr_data[1],'ผลการบันทึก',function(){
							//init form cn
							initCnForm();
							initCnTemp();										
		    				 return false;
			    		 });
						//NEW process for joke
						var doc_no=arr_data[1];
						var opts_up2j={
								type:'post',
								url:'/sales/cn/up2j',
								data:{
									doc_no:doc_no,
									rnd:Math.random()
								},success:function(){}
						};
						$.ajax(opts_up2j);
						//NEW process for joke
					}else{
						jAlert('เกิดปัญหา\n'+arr_data[1]+'\n ไม่สามารถบันทึกได้','ผลการบันทึก',function(){
							 $("#cn_product_id").focus();
							 return false;
			    		 });
					}
					
				}
			};
			jConfirm('คุณต้องการบันทึกรายการใช่หรือไม่?', 'ยืนยันการบันทึก', function(r) {
			    if(r){	
			    	$.ajax(opts);
				}else{
					$("#cn_product_id").focus();
				}
			});
	}//func
	
	function printBillCN(str_doc_no){	
		/**
		 *@desc
		 *@param String str_doc_no :
		 *@return
		 */
		var arr_docno=str_doc_no.split('#');
		var doc_no=arr_docno[1];	
		var thermal_printer=arr_docno[3];
		var net_amt=parseInt(arr_docno[4]);		
		var status_no=arr_docno[5];
		
		if(thermal_printer=='Y'){
			var url2print="/sales/report/billcn";		
		}else{
			var url2print="/sales/report/billcn";		
		}
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
		//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 
		$.ajax(opts);
		return false;
	}//func
	
	function enableFormCN(){
	}//func
	
	function simpleObjInspect(oObj, key, tabLvl)
	{
	    key = key || "";
	    tabLvl = tabLvl || 1;
	    var tabs = "";
	    for(var i = 1; i < tabLvl; i++){
	        tabs += "\t";
	    }
	    var keyTypeStr = " (" + typeof key + ")";
	    if (tabLvl == 1) {
	        keyTypeStr = "(self)";
	    }
	    var s = tabs + key + keyTypeStr + " : ";
	    if (typeof oObj == "object" && oObj !== null) {
	        s += typeof oObj + "\n";
	        for (var k in oObj) {
	            if (oObj.hasOwnProperty(k)) {
	                s += simpleObjInspect(oObj[k], k, tabLvl + 1);
	            }
	        }
	    } else {
	        s += "" + oObj + " (" + typeof oObj + ") \n";
	    }
	    return s;
	}//func
	
	function flgCommand(com,grid){
		if(com=='Refresh'){
			$("#tbl_cn").flexOptions({newp:1}).flexReload();
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
							   url: "/sales/cn/delcn",
							   data:{
						   				items:del_list,
						   				action:'removeFormGrid'
						   			},
							   success: function(data){
										if(data=='0'){
											 jAlert('ไม่สามารถลบรายการดังกล่าวได้','ข้อความแจ้งเตือน',function(){
												 return false;
											  });
										}else{
											getCN();
											$('#cn_product_id').focus();
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
	
	function setCN(){
		var cn_cashier_id=jQuery.trim($("#cn_cashier_id").val());
		var cn_status_no=$("#cn_status_no").val();
		var cn_doc_no=$("#cn_doc_no").val();
		var cn_product_id=$("#cn_product_id").val();
		var cn_quantity=$("#cn_quantity").val();
		var cn_price=$("#cn_price").val();
		var cn_seq=$("#cn_seq").val();
	    var cn_tp=$("#cn_tp").val();
		var lot_expire=$("#cn_lot_expire").val();
		var lot_no=$("#cn_lot_no").val();			
		var cn_remark2=$("#cn_lot_remark").val();		 
		var opts={
			        type:'post',
			        url:'/sales/cn/setcn',
			        cach:false,
			        data:{
		        		status_no:cn_status_no,
		        		doc_no:cn_doc_no,		        		
		        		product_id:cn_product_id,
		        		quantity:cn_quantity,
		        		price:cn_price,
		        		seq:cn_seq,
		        		cashier_id:cn_cashier_id,
		        		cn_tp:cn_tp,
						lot_expire:lot_expire,
						lot_no:lot_no,
						cn_remark2:cn_remark2
		        	},
		        	success:function(data){
			        	if(data=='1'){
			        		disableFormCN();
				        	$("#cn_product_id").val('').focus();
			        		getCN();
			        	}else if(data=='2'){
			        		jAlert('ไม่สามารถบันทึกรายการนี้ได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
								 $("#cn_product_id").focus();
			    				 return false;
				    		 });
				        }
			        }
			    };
		    $.ajax(opts);
	}//func
	
	function getCN(){	
		/*
		 *@desc 
		 */
		$.ajax({
			type:'post',
			url:'/sales/cn/getpages',
			cache: false,
			data:{				
				rp:14,
				now:Math.random()
			},
			success:function(data){	
				$("#tbl_cn").flexOptions({newp:data}).flexReload();
				getSubTotalCn();
				return false;
			}
		});
	}//func

	function getSubTotalCn(){
		/**
		*@name getSubTotalCn
		*@desc 
		*/
		
		var cn_doc_no_ref=jQuery.trim($("#cn_doc_no").val());
		$.getJSON(
				"/sales/cn/subtotalcn",
				{
					doc_no_ref:cn_doc_no_ref,
					actions:'getSumCnTemp'
				},
				
				function(data){
						$.each(data.sumcn, function(i,m){
							//alert("m.exist="+m.exist+"m.sum_net_amt="+m.sum_net_amt);
							if(m.exist=='yes' && parseInt(m.sum_net_amt)!='0'){
								var cn_sum_quantity=parseInt(m.sum_quantity);
								var cn_sum_discount=parseFloat(m.sum_discount);
								var cn_sum_amount=parseFloat(m.sum_amount);
								var cn_net_amt=parseFloat(m.sum_net_amt);
								cn_sum_discount=cn_sum_discount.toFixed(2);
								cn_sum_amount=cn_sum_amount.toFixed(2);
								$('#cn_sum_quantity').val(cn_sum_quantity);
								$('#cn_sum_discount').val(cn_sum_discount);
								$('#cn_sum_amount').val(addCommas(cn_sum_amount));
								$('#cn_sum_net_amt').val(addCommas(cn_net_amt.toFixed(2)));
								//--------------------------------------------------------
								
							}else{
								$('#cn_sum_quantity').val('0');
								$('#cn_sum_discount').val('0.00');
								$('#cn_sum_amount').val('0.00');
								$('#cn_sum_net_amt').val('0.00');
							}	
						});//foreach
						return false;
				}//
		);
		
	}//func

	function initCnForm(){
		//GET LOCK STATUS
		getLockStatus();
		$("#cn_tp").val('');
		$("#cn_seq").val('');
		$("#cn_doc_no").val('').removeAttr('readonly');
		$("#bws_cndocno").removeAttr('readonly');
		$("#cn_member_id_ref").val('');
		$("#cn_status_no").val('').attr("disabled",false);
		$("#cn_member_no").val('').removeAttr('readonly');
		$("#cn_member_no_hide").val('');
		$("#cn_discount").val('').disable();
		$("#cn_fullname").val('').removeAttr('readonly');
		$("#cn_remark").val('').removeAttr('readonly');
		$("#cn_address").val('');
		$("#cn_quantity").val('1');
		$("#cn_product_id").val('');
		$("#cn_sum_quantity").val('0').disable();
		$("#cn_sum_amount").val('0.00').disable();
		$("#cn_sum_discount").val('0.00').disable();
		$("#cn_sum_net_amt").val('0.00').disable();
		
		//case status_no=41
		$('#cn_acc_name').val('');
		$('#cn_bank_acc').val('');
		$('#cn_bank_name').val('');
		$('#cn_tel1').val('');
		$('#cn_tel2').val('');
		
	}//func

	function disableFormCN(){
		$("#cn_doc_no").attr('readonly', true);
		$("#bws_cndocno").attr('readonly',true);
		$("#cn_status_no").attr("disabled",true);
		$("#cn_member_no").attr('readonly', true);
		$("#cn_discount").attr('readonly', true);
		$("#cn_fullname").attr('readonly', true);
		//$("#cn_remark").attr('readonly', true);
		$("#cn_sum_quantity").disable();
		$("#cn_sum_amount").disable();
		$("#cn_sum_discount").disable();
		$("#cn_sum_net_amt").disable();
	}//func

	function initCnTemp(){	
		var opts_cn={
				type:'post',
				url:'/sales/cn/initcntemp',
				data:{
					rnd:Math.random()
				},success:function(){
					getCN();
					$("#cn_doc_no").focus();
					return false;
				}
					
			};
		$.ajax(opts_cn);
	}//func
	
	function msgSelProductError(){
		jAlert('จำนวนสินค้าคงเหลือไม่พอ กรุณาคีย์จำนวนไม่เกินยอดคงเหลือในลำดับที่เลือก','ข้อความแจ้งเตือน',function(r){
			if(r){
				$("#cn_quantity").focus();
			}
    		return false;
		 });
	}//func
	
	function bwsCnProduct(product_id){
		/***
		 * @desc
		 * @return
		 */		
		var doc_no_ref=$.trim($("#cn_doc_no").val());
		var cn_member_id_ref=$("#cn_member_id_ref").val();
		var cn_member_no=$("#cn_member_no").val();
		if(doc_no_ref.length==0){
			jAlert('กรุณาระบุเลขที่เอกสารอ้างอิง','ข้อความแจ้งเตือน',function(){
				$("#cn_doc_no").focus();
				 	return false;
    		 });
		}else if(cn_member_id_ref!='' && cn_member_id_ref!=cn_member_no){
			jAlert('กรุณาระบุรหัสสมาชิก','ข้อความแจ้งเตือน',function(){
				$("#cn_member_no").focus();
				 	return false;
    		 });
		}else{
			//************************* start *******************************
			var key_cn_quantity=$('#cn_quantity').val();
			var dialogOpts_cnproduct = {
					autoOpen: false,
					width:'75%',
					height:450,	
					modal:true,
					resizable:true,
					position:['center','center'],
					title:"รายการสินค้าตามเลขที่เอกสาร "+doc_no_ref,
					closeOnEscape:true,
					open: function(){ 
							//$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
							$(this).parents().css({"padding":"0 0 0 0","margin":"0 0 0 0","border-color":"#C6D5DC"});
		    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7",
		    			    	"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
		    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
		    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"} );
							$("#dlg_cnproduct").html("");
							$("#dlg_cnproduct").load("/sales/cn/bwscnproduct?doc_no_ref="+doc_no_ref+"&product_id="+product_id+"&now="+Math.random(),
									function(evt,ui){
										////////////////////////////
										$('#cn_product_id').blur();
										$('.ui-dialog-buttonpane button:last').blur();										
										$('.tableNavCnProduct ul li').not('.nokeyboard').navigate({
									        wrap:true
									     }).click(function(e){
										    e.preventDefault();
										    if($(this).attr('idproduct')=='nodata'){
										    	$('.tableNavCnProduct ul li').navigate('destroy');
											    $('#dlg_cnproduct').dialog('close');	
											    return false;
											}										    
										    var selseq=$.parseJSON($(this).attr('idproduct'));	
										    var cn_product_id=selseq.product_id;
										    var cn_seq=selseq.seq;
										    var balance_qty=parseInt(selseq.balance_qty,10);
										    key_cn_quantity=parseInt(key_cn_quantity,10);
										    var cn_quantity=parseInt(selseq.quantity);
										    $("#cn_seq").val(selseq.seq);
										    $("#cn_product_id").val(selseq.product_id);
										    $('.tableNavCnProduct ul li').navigate('destroy');
											$('#dlg_cnproduct').dialog('close');									
											
										    //if(balance_qty<1){
											if(key_cn_quantity>balance_qty){//ตรวจสอบจำนวนที่ key มากกว่าจำนวนสินค้าคงเหลือแต่ละลำดับหรือไม่
										    	$("#cn_product_id").val('');
										    	setTimeout(function(){msgSelProductError();},800);
										    	return false;
										    }else{
										    	///////////////////// start cn_tp
										    	 //select  cn_tp
											    var dlg_cntp={
											    		autoOpen: false,
														width:'45%',
														height:350,	
														modal:true,
														resizable:true,
														position:['center','center'],
														title:"ประเภทเอกสาร ",
														closeOnEscape:false,
														open: function(){ 
															$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
															$(this).parents().css({"padding":"0 0 0 0","margin":"0 0 0 0","border-color":"#C6D5DC"});
										    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7",
										    			    	"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
										    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
										    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"} );
															$("#dlg_cntp").html("");
															$("#cn_product_id").blur();
															$("#dlg_cntp").load("/sales/cn/bwscntp?now="+Math.random(),
																function(){
																	$('.tableNavCnTp ul li').not('.nokeyboard').navigate({
																        wrap:true
																    }).click(function(e){
																	    e.preventDefault();
																	    if($(this).attr('idcntp')=='nodata'){
																	    	$('.tableNavCnTp ul li').navigate('destroy');
																		    $('#dlg_cntp').dialog('close');	
																		    return false;
																		}										    
																	    var sel_cntp=$.parseJSON($(this).attr('idcntp'));
																	    $('#cn_tp').val(sel_cntp.cn_tp);
																	    $('.tableNavCnTp ul li').navigate('destroy');
																	    $('#dlg_cntp').dialog('close');
																	    if(sel_cntp.get_remark=='Y'){
																	    	//////////////////////////////////////////บันทึกเหตุผล//////////////////////////////////
																	    	var dlg_cntp_desc={
																		    		autoOpen: false,
																					width:'50%',
																					height:350,	
																					modal:true,
																					resizable:true,
																					position:['center','center'],
																					title:"*หมายเหตุ ",
																					closeOnEscape:false,
																					open: function(){ 
																						$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
																						$(this).parents().css({"padding":"0 0 0 0","margin":"0 0 0 0","border-color":"#C6D5DC"});
																	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7",
																	    			    	"padding-top":"10","margin":"0 0 0 0","font-size":"27px","color":"#000"});
																	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
													    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"} )
																	    			    // button style		
																	    			    $(this).dialog("widget").find("button")
																		                  .css({"padding":"0","margin":".1em .1em 0 0"});
														                  
																	    			    $("#dlg_cntp_desc").html("");
																	    			   
																						$("#dlg_cntp_desc").load("/sales/cn/bwscntpdesc?now="+Math.random(),
																							function(){	
																								$("#lot_expire").datepicker({
																										dateFormat: 'dd/mm/yy',
																										altField: '#lot_expire_alternate',
																										altFormat:'yy-mm-dd',
																										changeMonth: true,
																										changeYear: true
																									});
																									$("#lot_expire").focus();
																							}
																						);																						
																					},
																					buttons: {
																						"ตกลง":function(){ 	
																								var lot_expire_alternate=$("#lot_expire_alternate").val();
																								lot_expire_alternate=$.trim(lot_expire_alternate);
																								var lot_no=$("#lot_no").val();
																								lot_no=$.trim(lot_no);
																								var cn_remark2=$("#cn_remark2").val();
																								cn_remark2=$.trim(cn_remark2);
																								if(lot_expire_alternate.length==0){
																									jAlert('กรุณาระบุวันที่ผลิต','ข้อความแจ้งเตือน',function(){
																										$("#lot_expire_alternate").focus();
																					  				 	return false;
																						    		 });
																									return false;
																								}
																								
																								if(lot_no.length==0){
																									jAlert('กรุณาระบุเลขที่ lot ที่ผลิต','ข้อความแจ้งเตือน',function(){
																										$("#lot_no").focus();
																					  				 	return false;
																						    		 });
																									return false;
																								}
																								
																								if(cn_remark2.length==0){
																									jAlert('กรุณาระบุสาเหตุ','ข้อความแจ้งเตือน',function(){
																										$("#cn_remark2").focus();
																					  				 	return false;
																						    		 });
																									return false;
																								}
																								
																			    		    	$("#cn_lot_expire").val(lot_expire_alternate);
																			    		    	$("#cn_lot_no").val(lot_no);
																			    		    	$("#cn_lot_remark").val(cn_remark2);
																			    		    	///////////////////////////////////////////// set cn product /////////////////////////////
																							    //setCNProduct(doc_no_ref,cn_product_id,cn_seq,cn_quantity);
																			    		    	setCN();
																							    ///////////////////////////////////////////// set cn product /////////////////////////////
																			    		    	$('#dlg_cntp_desc').dialog('close');
																							}
																					}
																	    	};
																	    	$('#dlg_cntp_desc').dialog('destroy');
																			$('#dlg_cntp_desc').dialog(dlg_cntp_desc);			
																			$('#dlg_cntp_desc').dialog('open');
																	    	//////////////////////////////////////////บันทึกเหตุผล/////////////////////////////////																	    	
																	    }else{
																	    	setCN();
														    		    	$('#dlg_cntp_desc').dialog('close');
																	    }
																	    
																	  
																	    
																    });
																    
																}
															);
											    	}
														
											    };
											    $('#dlg_cntp').dialog('destroy');
												$('#dlg_cntp').dialog(dlg_cntp);			
												$('#dlg_cntp').dialog('open');
											    return false;
										    
										    	////////////////////  end cn_tp
										    }
										   
										   
										////////////////////////////
									});
							});
							
					},	
					close: function() {
						$('.tableNavCnProduct ul li').navigate('destroy');
					 },
					 buttons:{
						 "ปิด":function(){ 				
							$(this).dialog("close");
							return false;
						}
					}
				};			
				
				$('#dlg_cnproduct').dialog('destroy');
				$('#dlg_cnproduct').dialog(dialogOpts_cnproduct);			
				$('#dlg_cnproduct').dialog('open');
			//************************* end ********************************
		}
	}//func
	
	function  setCNProduct(doc_no_ref,cn_product_id,cn_seq,cn_quantity){
		/**
		 * @desc
		 * @param String cn_doc_no;
		 * @param String cn_product_id;
		 * @return
		 */
		var opts={
		        type:'post',
		        url:'/sales/cn/productcn',
		        async:true,
		        cach:false,
		        data:{
	        		cn_seq:cn_seq,
	        		doc_no:doc_no_ref,
	        		product_id:cn_product_id,
	        		quantity:cn_quantity
	        	},
	        	success:function(data){
		        	if(data=='1'){
		        		setCN();
		        	}else if(data=='2'){
		        		jAlert('จำนวนสินค้ามากกว่าจำนวนรายการในลำดับนี้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							 $("#cn_product_id").focus();
		    				 return false;
			    		 });
			        }else if(data=='3'){
		        		jAlert('ไม่พบรหัสสินค้าในบิลนี้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							 $("#cn_product_id").focus();
		    				 return false;
			    		 });
			        }
		        }
		  };
		 $.ajax(opts);
	}//func
	
	//*WR 11062015 for suport idcard
	function callbackCnIdCard(data){
			$('#dlgCnIdCard').dialog('close');
			var arr_data=data.split('#');
			$('#cn_member_no').val(arr_data[0]);
			cmdEnterKey("cn_member_no");
			//$('#cn_member_no').val(arr_data[1]);
			//$('#txt_idcard_show').empty().html(arr_data[1]);
	}//func
	
	$(function(){
		//init form cn
		initCnForm();
		initCnTemp();
		var gHeight=370,wcol=250,gWidth = parseInt((screen.width*70)/100)-600;
		if ((screen.width>=1280) && (screen.height>=1024)){
			//gHeight=(screen.height-(screen.height*(65/100))-5);
			gHeight=(screen.height-(screen.height*(75/100))-5);
			wcol=410;
		}
		$("#tbl_cn").flexigrid(
				{
					url:'/sales/cn/cntemp',
					dataType: 'json',
					colModel : [
						{display: '#', name : 'id', width :20, sortable : true, align: 'center'},
						{display: 'รหัส', name : 'product_id', width : 70, sortable : true, align: 'center'},
						{display: 'โปรโมชั่น', name : 'promo_code', width : 70, sortable : true, align: 'center'},
						{display: 'รายละเอียด', name : 'product_name',width :gWidth, sortable : false, align:'left'},
						{display: 'ส่วนลดสมาชิก', name : 'discount', width : 90, sortable : true, align: 'center'},
						{display: 'จำนวน', name : 'quantity', width : 50, sortable : false, align: 'center'},
						{display: 'ราคา', name : 'price', width :70, sortable : false, align: 'center'},
						{display: 'จำนวนเงิน', name : 'amount', width :70, sortable : false, align: 'center'},
						{display: 'ส่วนลด', name : 'discount', width :70, sortable : false, align: 'center'},
						{display: 'รวมเงิน', name : 'net_amt', width :90, sortable : false, align: 'center'}
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
					buttons : [
								{name:'Delete',bclass: 'flgBtnDelClass',onpress : flgCommand},
								{separator: true},
								{name:'Refresh',bclass:'flgBtnRefClass',onpress :flgCommand}					
							],	
					onSuccess:function(data){
						//getSubTotal();
					},
					onError:function(data){
						alert("ERROR : "+data);
					}
					,
					showTableToggleBtn:true,
					striped:false,
					height:gHeight
				}
		);//end flexigrid

		$("#cn_doc_no").val('').focus();
		$("#cn_doc_no").keypress( function(evt) {			
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            var cn_doc_no=jQuery.trim($("#cn_doc_no").val());
	            if(cn_doc_no==''){
	            	jAlert('กรุณาระบุรหัสเอกสารอ้างอิง','ข้อความแจ้งเตือน',function(){
						 $("#cn_doc_no").focus();
	    				 return false;
		    		 });
		        }else{	   
			        $.ajax({
				        	type:'post',
				        	url:'/sales/cn/chkdocno',
				        	cache:false,
				        	data:{
			        			doc_no:cn_doc_no,
			        			rnd:Math.random()
			        		},
			        		success:function(data){
				        		var arr_data=data.split('#');
				        		if(arr_data[0]=='1'){
				        			jAlert('ไม่พบเลขที่เอกสารที่ต้องการ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(b){
										 $("#cn_doc_no").val('').focus();
					    				 return false;
						    		 });
					        	}else if(arr_data[0]=='2'){
					        		jAlert('เลขที่เอกสารดังกล่าวถูกยกเลิก ไม่สามารถอ้างถึงได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(b){
										 $("#cn_doc_no").val('').focus();
					    				 return false;
						    		 });
						        }else if(arr_data[0]=='3'){
						        	jAlert('เลขที่เอกสารดังกล่าวไม่อยู่ในเงื่อนไขย้อนหลัง '+arr_data[1]+' วัน กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(b){
										 $("#cn_doc_no").val('').focus();
					    				 return false;
						    		 });
							    }else if(arr_data[0]=='9'){
								    $("#cn_member_no_hide").val(arr_data[1]);
								    //cn_member_id_ref
							    	//next field         
					            	$("#cn_status_no").focus();
								}
				        	}
				        });
			       
		        }					           
	            return false;
	        }
	    });//keypress

		$("#cn_status_no").keypress( function(evt) {									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            var cn_doc_no=jQuery.trim($("#cn_doc_no").val());
	            var cn_status_no=jQuery.trim($("#cn_status_no").val());
	            if(cn_doc_no.length==0){
	            	jAlert('กรุณาระบุเลขที่เอกสารอ้างอิง','ข้อความแจ้งเตือน',function(){
						 $("#cn_doc_no").focus();
	    				 return false;
		    		 });
		        }else if(cn_status_no.length==0){
	            	jAlert('กรุณาระบุเงื่อนไข','ข้อความแจ้งเตือน',function(){
						 $("#cn_status_no").focus();
	    				 return false;
		    		 });
		        }else{
	            	$("#cn_member_no").focus();	
		        }				           
	            return false;
	        }
	    });//keypress

		$("#cn_member_no").keypress( function(evt) {									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
		        evt.preventDefault();
	            var cn_doc_no_ref=jQuery.trim($("#cn_doc_no").val());
	            var cn_member_no=jQuery.trim($("#cn_member_no").val());
	            var cn_member_no_hide=jQuery.trim($("#cn_member_no_hide").val());	            
	        	if(cn_doc_no_ref.length==0){
           			jAlert('กรุณาระบุเลขที่เอกสารอ้างอิง','ข้อความแจ้งเตือน',function(){
						 $("#cn_doc_no").focus();
	    				 return false;
		    		 });
	           		return false;
	           	}	        
	        	
	            if(cn_member_no_hide.length!=0){
	           		if(cn_member_no.length==0){
	           			jAlert('กรุณาระบุรหัสสมาชิก','ข้อความแจ้งเตือน',function(){
							 $("#cn_member_no").focus();
		    				 return false;
			    		 });
		           		return false;
		           	}
		            //check  member
	            	$.getJSON(
	        				"/sales/cn/chkmember",
	        				{
	        					status_no:$("#cn_status_no").val(),
	        					doc_no_ref:cn_doc_no_ref,
	        					member_no:cn_member_no,
	        					cn_member_no_ref:cn_member_no_hide,//*WR08052014
	        					rnd:Math.random()
	        				},	        				
	        				function(data){
	        					if(data!='0'){
	        						$.each(data.member, function(i,m){
	        							if(m.exist=='yes'){	        
	        								
	        								if(m.member_percent==''){
	        									member_percent=0;
	        								}else{
	        									member_percent=m.member_percent;
	        								}
	        								if(m.special_percent==''){
	        									special_percent=0;
	        								}else{
	        									special_percent=m.special_percent;
	        								}	        								
	        								var str_discount=parseInt(member_percent)+"%+"+parseInt(special_percent)+"%";
	        								
	        								var fullname=m.name+" "+m.surname;
	        								var address=$.trim(m.address)+" "+
											$.trim(m.sub_district)+" "+
											$.trim(m.district)+" "+
											$.trim(m.province_name)+" "+
											$.trim(m.zip);
	        								$('#cn_fullname').val(fullname);
	        								$('#cn_discount').val(str_discount);
	        								$('#cn_address').val(address);
	        								$('#cn_member_id_ref').val(cn_member_no);	        								
	        							}else{
	        								$('#cn_fullname').val('');
	        								$('#cn_discount').val('0');
	        							}	
	        							//$("#cn_member_no").attr('readonly', true);
	        							disableFormCN();
	        							$("#cn_remark").focus();
	        							$("#cn_remark").keypress(function(evt){
	        								var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        						        if(key == 13){
	        							        evt.preventDefault();
	        							        $("#cn_product_id").focus();
	        							        return false;
	        						        }
	        							});
	        						});//foreach
	        					}else{
	        						jAlert('ไม่พบข้อมูลรหัสสมาชิกนี้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
	        							 $("#cn_member_no").focus();
	        		    				 return false;
	        			    		});	        						 
		        				}
	        					return false;
	        				}//
	        		);
		         
		        }else{
		        	$("#cn_fullname").focus();
			    }	
		        //$("#cn_product_id").focus();	           
	            return false;
	        }
	    });//keypress

		$("#cn_fullname").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
		        evt.preventDefault();
		        var cn_fullname= $("#cn_fullname");
		        	cn_fullname=$.trim(cn_fullname.val());
		        	if(cn_fullname.length<1){
		        		jAlert('กรุณาระบุชื่อ-นามสกุลสมาชิก','ข้อความแจ้งเตือน',function(){			        		
							 $("#cn_fullname").focus();
		    				 return false;
			    		});
			    		return false;
			        }else{
				        $("#cn_remark").focus();
				    }
	        }
		});//keypress


	    $("#cn_quantity").keypress( function(evt) {									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            var quantity=jQuery.trim($("#cn_quantity").val());
	            if(quantity.length==0 || quantity=='0'){
	            	jAlert('กรุณาระบุจำนวนสินค้า','ข้อความแจ้งเตือน',function(){
						 $("#cn_quantity").focus();
	    				 return false;
		    		 });
		        }else{
	            	$("#cn_product_id").focus();
		        }
	        }
	    });

		$("#cn_product_id").keypress( function(evt) {									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	        	evt.stopPropagation();
	            evt.preventDefault();
	            var cn_seq=$.trim($("#cn_seq").val());
	            var cn_product_id=$.trim($("#cn_product_id").val());
	            var cn_quantity=$.trim($("#cn_quantity").val());
	            var cn_doc_no=$.trim($("#cn_doc_no").val());
	            var cn_member_no=$.trim($("#cn_member_no").val());
	            var cn_member_no_hide=$.trim($("#cn_member_no_hide").val());
	            
	            if(cn_doc_no.length==0){
	            	jAlert('กรุณาระบุรเลขที่เอกสาร','ข้อความแจ้งเตือน',function(){
						 $("#cn_doc_no").focus();
	    				 return false;
		    		 });
		    		 return false;
		        }else if(cn_member_no_hide.length>0){
	           		if(cn_member_no.length==0){
	           			jAlert('กรุณาระบุรหัสสมาชิก','ข้อความแจ้งเตือน',function(){
							 $("#cn_member_no").focus();
		    				 return false;
			    		 });
		           		 return false;
		           	}else if(cn_product_id.length==0){
		            	jAlert('กรุณาระบุรหัสสินค้า','ข้อความแจ้งเตือน',function(){
							 $("#cn_product_id").focus();
		    				 return false;
			    		 });
		            	 return false;
		           	}
		        }else if(cn_product_id.length==0){
	            	jAlert('กรุณาระบุรหัสสินค้า','ข้อความแจ้งเตือน',function(){
						 $("#cn_product_id").focus();
	    				 return false;
		    		 });
		    		 return false;
		        }
	           
	            bwsCnProduct(cn_product_id);//all product open brows dialog
	            
	            //check product sigle or multiple seq
//	            var opts_chk_seq={
//	            		type:'post',
//	            		url:'/sales/cn/chkseq',
//	            		async:false,
//	            		data:{
//	            			product_id:cn_product_id,
//	            			doc_no:cn_doc_no,	            			
//	            			rnd:Math.random()	            			
//	            		},success:function(data){	            			
//	            			if(parseInt(data)>1){
//	            				bwsCnProduct(cn_product_id);
//	            				return false;
//	            			}else{
//	            				//case product is only
//	            				//setCNProduct(cn_doc_no,cn_product_id,1,1);
//	            				setCN();
//	            				return false;
//	            			}
//	            			
//	            		}
//	            };
//	            $.ajax(opts_chk_seq);
	            return false;
	        }
	    });//keypress

		$("#bws_cndocno").click(function(){
			var dialogOpts_cndocno = {
					autoOpen: false,
					width:'60%',
					height:550,	
					modal:true,
					resizable:true,
					position:['center','center'],
					title:"รายการเลขที่เอกสาร",
					closeOnEscape:true,
					open: function(){ 
							$(this).parents().css({"padding":"0 0 0 0","margin":"0 0 0 0","border-color":"#C6D5DC"});
						    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7",
						    	"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
						    $(this).dialog("widget").find(".ui-dialog-buttonpane")
						                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"} );
							$("#dlg_cndocno").html("");
							$("#dlg_cndocno").load("/sales/cn/bwscndocno?now="+Math.random(),
									function(evt,ui){
										/////////// add data table ////////
								      
										var oTable = $('#tableNavCnDocNo').dataTable();		
										var chk_data=$('p[id="item_not_found"]');
										if(chk_data.length>0) return false;
										$('#tableNavCnDocNo').dataTable({
											"bJQueryUI":false,
											"bDestroy": true,
							       			"fnDrawCallback": function(){
							       												       	
								       		      $('table#tableNavCnDocNo td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
								       		      $('table#tableNavCnDocNo td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
								       		      
									       		   $('#tableNavCnDocNo tr').each(function(){
										       			$(this).click(function(){
										       				var strJson=$(this).attr('iddocno');								       				
										       				if(strJson!=''){
												       				var seldocno=$.parseJSON(strJson);
																    $("#cn_doc_no").val(seldocno.doc_no);
																    $("#cn_member_id_ref").val(seldocno.member_id);										   
																    cmdEnterKey("cn_doc_no");														   					   
																    $("#dlg_cndocno").dialog('close');
																    return false;														    
												       		}
										       			});
										       		});
								       		      
							       			}//end callback
										});	
										 oTable.fnSort([[0,'desc'],[1,'desc']]);
										/////////// add data table ////////								 
									
									}
							);
							
					},	
					close: function() {
						//Delete the datable object first
						//if(oTable != null) oTable.fnDestroy();
						//Remove all the DOM elements
						$('#tableNavCnDocNo').empty();
					 },
					 buttons:{
						 "ปิด":function(){ 				
							$(this).dialog("close");
							return false;
						}
					}
				};			
				
				$('#dlg_cndocno').dialog('destroy');
				$('#dlg_cndocno').dialog(dialogOpts_cndocno);			
				$('#dlg_cndocno').dialog('open');
				return false;
		});
		
		//*WR19052015 for support id card
		$("#bws_cnidcard").click(function(e){
			e.preventDefault();
			$('<div id="dlgCnIdCard"><span style="color:#336666;font-size:20px;"></span><input type="hidden" size="15" id="id_card_ref"/></div>').dialog({
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
			  			  var cn_member_id_ref=$("#cn_member_id_ref").val();
			  			  //*WR23032016
			  			  if(cn_member_id_ref==''){
			  				  cn_member_id_ref=$('#cn_member_no_hide').val();
			  			  }
			  			  $.get("../../../download_promotion/id_card_newmem/api_verify_from.php?function_next=callbackCnIdCard&member_no=" + cn_member_id_ref, function(data) {
			  				   $('#dlgCnIdCard').empty().html(data);
			  				});
			  	},close:function(evt){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
							evt.stopPropagation();
							evt.preventDefault();		
						}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
							evt.stopPropagation();
							evt.preventDefault();  						
						}				
						$('#dlgCnIdCard').remove();	
		           }
			});
			//ccsreadidcardfrom();
		});//func

		$("#bws_cnproduct").click(function(){
			bwsCnProduct('');
			return false;
		});

		$("#btn_cn_cancel").click(function(){
			jConfirm('คุณต้องการยกเลิกรายการใช่หรือไม่?', 'ยืนยันการยกเลิก', function(r) {
			    if(r){	
			    	initCnForm();
					initCnTemp();
				}
			});
			
		});

		$("#btn_cn_submit").click(function(e){
				e.preventDefault();
				var cn_sum_net_amt=$('#cn_sum_net_amt').val();
				
				//*WR05052016 for support ทำ Cn ของแถม
				if(cn_sum_net_amt=='x0.00'){
					jAlert('ไม่พบการทำรายการลดนี้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
						 $("#cn_product_id").focus();
	    				 return false;
		    		 });
					return false;
				}
			    if($('#cn_status_no').val()=='41'){
			    	//////////////////////////////////////// คืนเงินลูกค้า ////////////////////////////////
			    	var opts_cnrefund={
				    		autoOpen: false,
							width:'55%',
							height:380,	
							modal:true,
							resizable:true,
							position:['center','center'],
							title:"*คืนเงินลูกค้า ",
							closeOnEscape:false,
							open: function(){ 
								$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
								$(this).parents().css({"padding":"0 0 0 0","margin":"0 0 0 0","border-color":"#C6D5DC"});
			    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7",
			    			    	"padding-top":"10","margin":"0 0 0 0","font-size":"27px","color":"#000"});
			    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
			                      .css({"padding":"5px","margin":"0 0 0 0","background-color":"#C7D9DC"});
			    			    // button style		
			    			    $(this).dialog("widget").find("button")
				                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
                  
			    			    $("#dlg_cnrefund").html("");
								$("#dlg_cnrefund").load("/sales/cn/bwscnrefund?now="+Math.random(),
									function(){
										$('#acc_name').focus();						
										//test arrow key
										var maxControl=3;
										var at=new Array();
											at[0]="#acc_name";
										  	at[1]="#bank_acc";
										  	at[2]="#bank_name";
										  	at[3]="#tel1";
										  	at[4]="#tel2";
									  	var ac=new Array();
										 	ac[0]="#btn_payment_credit";
											

										var at_index=0;//for up,down arrow
										var ac_index=0;//fo left,right arrow
										$('#dlg_cnrefund').keydown(function (e) {
											  var keyCode = e.keyCode || e.which,
											      arrow = {esc:27,left: 37, up: 38, right: 39, down: 40 ,select:13};
											  switch (keyCode) {
											  	case arrow.esc:
												break;
											    case arrow.left:
												     ac_index=ac_index-1;
											      	 if(ac_index<=0){
											      	 	ac_index=0;
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
											    break;
											    case arrow.right:
											    	ac_index=ac_index+1;
											    	if(ac_index>=2){
											      	 	ac_index=2;
											      	 }
												    $(ac[ac_index]).focus();
													 
											    break;
											    case arrow.down:
											    	at_index=at_index+1;
											      	if(at_index>4){
											      	 	at_index=4;
											      	}
											      	//alert(at_index);
												    $(at[at_index]).focus();
											    break;
											    case arrow.select:
											    	at_index=at_index+1;
											      	if(at_index>4){
											      	 	at_index=4;
											      	  $('.ui-dialog-buttonpane button:last').focus();
											      	}else{
											      		$(at[at_index]).focus();
											      	}
											      	
											    break;
											  }
											});					
										//test arrow key
									}
								);
							},
							buttons: {
								"ตกลง":function(){ 
										var acc_name=$.trim($('#acc_name').val());
										var bank_acc=$.trim($('#bank_acc').val());
										var bank_name=$.trim($('#bank_name').val());
										var tel1=$.trim($('#tel1').val());
										var tel2=$.trim($('#tel2').val());
										if(acc_name.length==0){
											jAlert('กรุณาระบุชื่อบัญชี','ข้อความแจ้งเตือน',function(){
												 $("#acc_name").focus();
							    				 return false;
								    		 });
											return false;
										}
										if(bank_acc.length==0){
											jAlert('กรุณาระบุเลขที่บัญชี','ข้อความแจ้งเตือน',function(){
												 $("#bank_acc").focus();
							    				 return false;
								    		 });
											return false;
										}
										if(bank_name.length==0){
											jAlert('กรุณาระบุชื่อธนาคาร/สาขา','ข้อความแจ้งเตือน',function(){
												 $("#bank_name").focus();
							    				 return false;
								    		 });
											return false;
										}
										if(tel1.length==0){
											jAlert('กรุณาระบุโทรศัพท์1','ข้อความแจ้งเตือน',function(){
												 $("#tel1").focus();
							    				 return false;
								    		 });
											return false;
										}
										if(tel2.length==0){
											jAlert('กรุณาระบุโทรศัพท์2','ข้อความแจ้งเตือน',function(){
												 $("#tel2").focus();
							    				 return false;
								    		 });
											return false;
										}
										
										$('#cn_acc_name').val(acc_name);
										$('#cn_bank_acc').val(bank_acc);
										$('#cn_bank_name').val(bank_name);
										$('#cn_tel1').val(tel1);
										$('#cn_tel2').val(tel2);
										setTimeout(function(){saveTransCN();},400);
					    		    	$('#dlg_cnrefund').dialog('close');
									}
							}
			    	};
			    	$('#dlg_cnrefund').dialog('destroy');
					$('#dlg_cnrefund').dialog(opts_cnrefund);			
					$('#dlg_cnrefund').dialog('open');
			    	/////////////////////////////////////// คืนเงินลูกค้า /////////////////////////////////
			    	
			    }else{
			    	saveTransCN();
			    }	
		});
		
	});//ready