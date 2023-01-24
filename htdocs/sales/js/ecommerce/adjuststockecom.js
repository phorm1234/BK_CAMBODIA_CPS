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
	
	function saveTransOrd(){
		/**
		 * @desc
		 * @return
		 */		
		var user_id=$("#csh_user_id").val();
		var cashier_id=$("#csh_cashier_id").val();
		var saleman_id=$("#csh_saleman_id").val();
		var opts={
				type:'post',
				url:'/sales/ecommerce/saveordweb',
				cache:false,
				data:{
					user_id:user_id,
					cashier_id:cashier_id,
					saleman_id:saleman_id,
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');		
					if(arr_data[0]=='1'){
						printBillOrdWeb(data);
						jAlert('บันทึกข้อมูลสมบูรณ์ '+arr_data[1],'ผลการบันทึก',function(){
							//init form cn
							initOrdForm();
							initOrdTemp();	
		    				 return false;
			    		 });						
					}else{
						jAlert('เกิดปัญหา\n'+arr_data[1]+'\n ไม่สามารถบันทึกได้','ผลการบันทึก',function(){
							 $("#ord_doc_no").focus();
							 return false;
			    		 });
					}
					
				}
			};
			$.ajax(opts);
		
	}//func
	
	function printBillOrdWeb(str_doc_no){	
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
			var url2print="/sales/report/billvatorderweb";		
		}else{
			var url2print="/sales/report/billvatorderweb";		
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
		popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 
		//$.ajax(opts);
		return false;
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
	
	

	function initOrdForm(){		
		$("#ord_doc_no").val('');
		$("#ord_member_no").val('');
		$("#csh_saleman_id").val('');
		$("#ord_fullname").val('');
		$("#ord_address").val('');		
		$("#ord_sum_quantity").val('0').disable();
		$("#ord_sum_amount").val('0.00').disable();	
		$("#ord_sum_net_amt").val('0.00').disable();		
	}//func

	function initOrdTemp(){	
		var opts_ord={
				type:'post',
				url:'/sales/ecommerce/initordtemp',
				data:{
					rnd:Math.random()
				},success:function(){
					getOrderDetailsTemp();
					$("#ord_doc_no").focus();
					return false;
				}
					
			};
		$.ajax(opts_ord);
	}//func
	
	function msgSelProductError(){
		jAlert('จำนวนสินค้าคงเหลือไม่พอ กรุณาคีย์จำนวนไม่เกินยอดคงเหลือในลำดับที่เลือก','ข้อความแจ้งเตือน',function(r){
			if(r){
				$("#cn_quantity").focus();
			}
    		return false;
		 });
	}//func	
	
	function getOrderDetailsTemp(){	
		/**
		 * @desc
		 */		
		$.ajax({
			type:'post',
			url:'/sales/ecommerce/getpages',
			cache: false,
			data:{				
				rp:14,							
				now:Math.random()
			},
			success:function(data){	
				$('#tbl_ord').flexOptions({url:'/sales/ecommerce/getordtemp',newp:data}).flexReload(); 
				getSubTotal();						
				return false;
			}
		});
	}//func

	function getSubTotal(){
		/**
		*@name getSum
		*@desc 
		*@comment need to check flg_chk_point for any bill					
		*/
		$.getJSON(
				"/sales/ecommerce/getsumorderweb",
				{
					rnd:Math.random()
				},
				function(data){
					$.each(data.sumcsh, function(i,m){
						//alert("m.exist="+m.exist+"m.sum_net_amt="+m.sum_net_amt);
						if(m.exist=='yes' && m.sum_net_amt!='' && parseInt(m.sum_net_amt)!='0'){		
							if(m.sum_net_amt==null){							
								$('#ord_sum_quantity').val('0');
								$('#ord_sum_net_amt').val('0.00');
								$('#ord_sum_point').val('0');
							}else{
								var sum_net=parseFloat(m.sum_net_amt);
								sum_net=sum_net.toFixed(2);								
								$('#ord_sum_quantity').val(m.sum_quantity);
								$('#ord_sum_net_amt').val(sum_net);
								$('#ord_sum_point').val(m.point_total);
							}	
						}else{
							$('#ord_sum_quantity').val('0');
							$('#ord_sum_net_amt').val('0.00');
							$('#ord_sum_point').val('0');
						}			
					});
		});

	}//func	
	
	
	
/////////////////////////13022014//////////////////////////////////////
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
	            	$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
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
							            	saveTransOrd();
							            }						            	
						            }
						    };	
							$.ajax(opts);
				            return false;
				        }
					});		
												            	
	            },close:function(evt){
	            	$("#dlgSaleMan").dialog("destroy");
	            	$('#btn_payment_confirm').focus();		
					$('#btn_payment_confirm').removeClass('ui-state-focus ui-state-active').addClass('ui-state-focus');		
	            }
	        });
		//------------- CHECK SALEMAN-----------------
	}//func
	/////////////////////////13022014//////////////////////////////////////
	
	function getProduct(){
		/**
		*@desc
		*@create 30112016
		*@return product id is exist
		*/	
		
		var product_id=$.trim($('#product_id').val());
		var quantity=$.trim($('#quantity').val());	
		var promo_code='';
		var status_no='15';
		if(product_id.length==0){
			jAlert('กรุณาระบุ รหัสสินค้า', 'ข้อความแจ้งเตือน',function(){
				$('#product_id').focus();
				return false;
			});				
			return false;
		}
		var opts={
				type:'post',
				url:'/sales/cashier/product',	
				data:{
					promo_code:promo_code,
					product_id:product_id,
					quantity:quantity,
					status_no:status_no,
					action:"checkproductexist"
				},
				success:function(data){		
					if(data==1){
						jAlert('ไม่พบรหัสสินค้านี้ในทะเบียน กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
							$('#product_id').val('').focus();							
							return false;
						});					
						return false;
					}else if(data==2){						
						jAlert('รหัสสินค้านี้สต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){			
							$('#product_id').val('').focus();	
							return false;							
						});	
						return false;						
					}else if(data==3){
						jAlert('รหัสสินค้านี้ถูกล๊อก กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){	
							$('#product_id').val('').focus();	
							return false;							
						});			
						return false;				
					}else if(data==4){
						jAlert('สินค้า Tester ห้ามขาย กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){								
							$('#product_id').val('').focus();		
							return false;							
						});			
						return false;		
					}else{
						$('#csh_product_id').val(data);	
						$('#quantity').focus();
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
	
	function setPdtStockAllocate(){
		/**
		 * @desc
		 * @create
		 * @return 
		 */
		var product_id=jQuery.trim($("#product_id").val());
		var quantity=jQuery.trim($("#quantity").val());
		$.ajax({
			type:'post',
			url:'/sales/ecommerce/setpdtstockallocate',
			cache:false,
			data:{
				product_id:product_id,
				quantity:quatity,
				rnd:Math.random()
			},success:function(data){
				if(data=='N'){					
					$('#quantity').focus();
				}else{
					jAlert('XXXXX กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
		            	$("#product_id").focus();
		            	return false;
			        });
				}
			}
		});
		
	}//func
	
	$(function(){
		//init form cn
		initOrdForm();
		initOrdTemp();
		var gHeight=370,wcol=250,gWidth = parseInt((screen.width*70)/100)-600;
		if ((screen.width>=1280) && (screen.height>=1024)){		
			gHeight=(screen.height-(screen.height*(75/100))-5);
			wcol=410;
		}
		$("#tbl_ord").flexigrid(
				{
					url:'/sales/cn/cntemp',
					dataType: 'json',
					colModel : [
						{display: '#', name : 'id', width :20, sortable : true, align: 'center'},
						{display: 'รหัส', name : 'product_id', width : 70, sortable : true, align: 'center'},					
						{display: 'รายละเอียด', name : 'product_name',width :gWidth, sortable : false, align:'left'},					
						{display: 'จำนวน', name : 'quantity', width : 50, sortable : false, align: 'center'},
						{display: 'ราคา', name : 'price', width :70, sortable : false, align: 'center'},
						{display: 'จำนวนเงิน', name : 'amount', width :120, sortable : false, align: 'center'},
						
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

		$("#product_id").val('').focus();
		$('#product_id').keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 			
	        if(key == 13){
	        	evt.stopPropagation();
				evt.preventDefault();
				var product_id=jQuery.trim($("#product_id").val());
				if(product_id.length<1){
					jAlert('กรุณาระบุรหัสสินค้า กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
		            	$("#product_id").focus();
		            	return false;
			        });
					return false;
				}else{
					getProduct();
					//$('#quantity').focus();
				}
				
				
				
//				$.ajax({
//					type:'post',
//					url:'/sales/ecommerce/chkeproduct',
//					cache:false,
//					data:{
//						product_id:product_id,
//						rnd:Math.random()
//					},success:function(data){
//						if(data=='N'){
//							//setOrderNo(ord_doc_no);
//							$('#quantity').focus();
//						}else{
//							jAlert('XXXXX กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
//				            	$("#product_id").focus();
//				            	return false;
//					        });
//						}
//					}
//				});
				
	        	return false;
	        }
		});
		
		$('#quantity').keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 			
	        if(key == 13){
	        	evt.stopPropagation();
				evt.preventDefault();
				var quantity=jQuery.trim($("#quantity").val());
				if(quantity.length<1){
					jAlert('กรุณาระบุจำนวนสินค้า กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
		            	$("#quantity").focus();
		            	return false;
			        });
					return false;
				}else{
					setPdtStockAllocate();
				}
	        	return false;
	        }
		});
	

		

		$("#btn_ord_cancel").click(function(){
			jConfirm('คุณต้องการยกเลิกรายการใช่หรือไม่?', 'ยืนยันการยกเลิก', function(r) {
			    if(r){	
			    	initOrdForm();
					initOrdTemp();
				}
			});
			
		});

		$("#btn_ord_submit").click(function(e){
				e.preventDefault();
			
			     var ord_doc_no=$("#ord_doc_no").val();
		         if(ord_doc_no=='0'){
	    			jAlert('กรุณาเลือกเลขที่เอกสาร ไม่สามารถบันทึกได้','ข้อความแจ้งเตือน',function(){
	    				$("#ord_doc_no").focus();
	    				 	return false;
	        		 });
	    			return false;
	    		 }
		         
	    	    var ord_sum_quantity=$('#ord_sum_quantity').val(); 
	    		if(ord_sum_quantity=='0'){
	    			jAlert('ไม่พบการทำรายการสินค้า ไม่สามารถบันทึกได้','ข้อความแจ้งเตือน',function(){
	    				$("#ord_doc_no").focus();
	    				 	return false;
	        		 });
	    			return false;
	    		}
	    		
	    		var ord_sum_net_amt=$('#ord_sum_net_amt').val();
				if(ord_sum_net_amt=='0.00'){
					jAlert('ไม่พบการทำรายการ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
						 $("#ord_doc_no").focus();
	    				 return false;
		    		 });
					return false;
				}
			  
				//open dlg web order							    
				var dlgSetBag = $('<div id="dlgSetBag">\
			            <p>Set Bag</p>\
			        </div>');
			            dlgSetBag.dialog({
				           autoOpen:true,
						   width:'50%',
						   height:'auto',	
						   modal:false,
						   resizable:true,
				           title: "กรุณาระบุจำนวนถุง ",									           
				           //position:[300,115],
				           position: [250,110],
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
							 	"ตกลง":function(evt){
							 		evt.preventDefault();
							 		evt.stopPropagation();												 		
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
			            				if(qty!='' || qty!='0'){
				            				var arr_bag_id=bag_id.split('_');
				            				var bag_set=arr_bag_id[1] + ':'  + $('#' +bag_id).val();
				            				arr_bag.push(bag_set);	
			            				}
			            			});//each
			            			
			            			if($chk_over_bag_qty=='Y'){
			            				jAlert('กรุณาระบุจำนวนไม่เกิน 99 ','ข้อความแจ้งเตือน',function(){
											return false;
						    			});
			            				return false;
			            			}
			            			
			            			var bag_list='';
			            			for(i=0;i<arr_bag.length;i++){
		            					bag_list+= arr_bag[i]+"#";
		            				}   
			            			//alert(bag_list);
			            			var status_no=$('#csh_status_no').val();//รหัสประเภทบิล
			            			$.ajax({
			            				type:'post',
			            				url:'/sales/accessory/setbagtotemp',
			            				cache:false,
			            				data:{
			            					status_no:status_no,
			            					items:bag_list,
			            					rnd:Math.random()
			            				},success:function(){
			            					checkSaleMan();
			            				}
			            			});
							 		
							 		$('#dlgSetBag').dialog('close');
									return false;
							 	},"ยกเลิก":function(evt){
							 		evt.preventDefault();
							 		evt.stopPropagation();
							 		checkSaleMan();
							 		$('#dlgSetBag').dialog('close');
									return false;
							 	}
			    		    },
				            close:function(evt){
								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){	
									evt.stopPropagation();
	        						evt.preventDefault();
	        						checkSaleMan();
								}else if(evt.originalEvent && $(evt.originalEvent.which==27)){
									evt.stopPropagation();
	        						evt.preventDefault();
	        						checkSaleMan();
								}	
				            	$(this).remove();
				            }
			        });				
				//open dlg web order										
				
				
		});
		
	});//ready