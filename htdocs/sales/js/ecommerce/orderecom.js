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
	
	function printBillOrdWeb(doc_no){	
		/**
		 *@desc
		 *@param String str_doc_no :
		 *@return
		 */		
		var url2print="/sales/report/billvattotal";
		//for print auto
		$.ajax({
			type:'get',
			url:url2print,
			cache:false,
			data:{
					doc_no:doc_no,
					actions:'print',
					rnd:Math.random()
				},
			success:function(){
					return false;
			}
		});
		//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 		
		return false;
	}//func	
	
	function saveTransOrd(){
		/**
		 * @desc confirm order and gen bull sl
		 * @create
		 * @modify : 22012017 confirm order by picking
		 * @return
		 */		
		var order_no=$('#ord_doc_no').val();
		order_no=$.trim(order_no);
		var user_id=$("#csh_user_id").val();
		var cashier_id=$("#csh_cashier_id").val();
		var saleman_id=$("#csh_saleman_id").val();
		var opts={
				type:'post',
				dataType:'json',
				url:'/sales/ecommerce/saveordweb',
				cache:false,
				data:{
					doc_no:order_no,
					user_id:user_id,
					cashier_id:cashier_id,
					saleman_id:saleman_id,
					rnd:Math.random()
				},
				success:function(objRes){
							
					if(objRes.status=='1'){
						printBillOrdWeb(objRes.doc_no); //print ใบเสร็จตอนรับสินค้า
						jAlert('บันทึกข้อมูล ' + objRes.doc_no + ' สมบูรณ์' ,'ผลการบันทึก',function(){							
							initOrdForm();
							initOrdTemp();	
		    				return false;
			    		 });	
						setTimeout(function(){
							//send order status and update stock center
	        				 $.ajax({
	        					 type:'post',
	        					 url:'/sales/ecommerce/sendorderstatus',
	        					 data:{
	        						 order_no:order_no,
	        						 order_status:'C',
	        						 user_action:saleman_id,
	        						 rnd:Math.random()
	        					 },success:function(){}
	        				 });
	        			 },800);
											
					}else{
						jAlert('เกิดปัญหาการยืนยันใบสั่งซื้อเลขที่\n ' + order_no + '\n ไม่สามารถบันทึกได้','ผลการบันทึก',function(){
							 $("#ord_doc_no").focus();
							 return false;
			    		 });
					}
					
				}
			};
			$.ajax(opts);
		
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
	
	
	function initOrdFormToKeyProduct(){		
		/**
		 * @desc
		 * @return
		 */		
		$("#quantity").val('1');
		$("#product_id").val('');
	}//func
	
	

	function initOrdForm(){		
		$("#ord_doc_no").val('');
		$("#quantity").val('1');
		$("#product_id").val('');
		
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
	
	/**
	 *@desc : set picking product to temp
	 *@modify : 17022017
	 *@return null
	 */
	
	function setPdtPickingToConfirmOrder(doc_no,product_id,quantity){	
		var doc_no=$('#ord_doc_no').val();
		var product_id=$('#product_id').val();
		var quantity=$('#quantity').val();
		doc_no=$.trim(doc_no);
		product_id=$.trim(product_id);
		quantity=$.trim(quantity);
		
		if(doc_no.length==0){
			jAlert('กรุณาระบุเลขที่ใบสั่งซื้อ', 'ข้อความแจ้งเตือน',function(){			
				$('#ord_doc_no').focus();
            	return false;
	        });
			return false;
		}		
		if(quantity.length==0){
			jAlert('กรุณาระบุจำนวนสินค้า', 'ข้อความแจ้งเตือน',function(){			
				$('#quantity').focus();
            	return false;
	        });
			return false;
		}
		if(product_id.length==0){
			jAlert('กรุณาระบุรหัสสินค้า', 'ข้อความแจ้งเตือน',function(){			
				$('#product_id').focus();
            	return false;
	        });
			return false;
		}
			
		var objReq_order=$.ajax({
			type:'post',
			url:'/sales/ecommerce/setpdtpickingtoconfirmorder',
			dataType:'json',
			data:{
				order_no:doc_no,
				product_id:product_id,
				quantity:quantity,
				status_no:'00',
				rnd:Math.random()
			},
			success:function(data){
				objReq_order=null;	
				
				var objJson=data;				
//				var objJson=$.parseJSON(data);				
				if(objJson.success=='Y'){
					initOrdFormToKeyProduct();
					getOrderDetailsTemp();
					
				}else{
					jAlert(objJson.msg_error, 'ข้อความแจ้งเตือน',function(){			
						$('#quantity').focus().select();
		            	return false;
			        });
				}
				
				
			}
		});			
	}//func
	
//	function setOrderNo(doc_no){		
//		/**
//		 *@desc : set order web to temp
//		 *@modify : 16022017 
//		 *@return null
//		 */
//			if(doc_no=='') return false;
//			var objReq_order=$.ajax({
//					type:'post',
//					url:'/sales/ecommerce/orderweb',
//					dataType:'json',
//					data:{
//						order_no:doc_no,
//						status_no:'00',
//						rnd:Math.random()
//					},
//					success:function(data){
//						objReq_order=null;
//						var objson='';
//						if(data!==null){
//							$.each(data.order, function(i,m){
//								if(m.exist=='yes'){
//									$('#ord_member_no').val(m.member_no);
//									$('#ord_fullname').val(m.name);
//								    var address=m.address1 + " " + m.address2 + " " + m.address3;
//									$('#ord_address').val(address);
//									getOrderDetailsTemp();
//								}else{
//									jAlert('สินค้ารายการ ' + m.product_empty + ' ขาดสต๊อก \nไม่สามารถบันทึกบิลได้', 'ข้อความแจ้งเตือน',function(){						            	
//						            	return false;
//							        });
//								}
//							});
//							
//						}
//						
//					}
//				});			
//	}//func
	
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
		var $chk_none_item='Y';
		var dlgSetBag = $('<div id="dlgSetBag">\
	            <p>Set Bag</p>\
	        </div>');
	            dlgSetBag.dialog({
			           autoOpen:true,
			           width:'80%',/*65*/
					   height:'570',	
					   modal:false,
					   resizable:true,
					   closeOnEscape:false,
			           title: "กรุณาระบุจำนวนถุง ",
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
					 	"ตกลง":function(evt){
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
	            				jAlert('กรุณาระบุจำนวนถุงอย่างน้อย 1 รายการ (ถ้าไม่สะดวกให้คีย์ 0 ถุง) ','ข้อความแจ้งเตือน',function(){
									return false;
				    			});
	            				return false;
	            			}
	            			
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
	            			var status_no=$('#csh_status_no').val();
	            			$.ajax({
	            				type:'post',
	            				url:'/sales/accessory/setbagtotemp',
	            				cache:false,
	            				data:{
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
					 		//*WR19082015 need to key form set bag
	            			if( $chk_none_item=='Y'){
	            				jAlert('กรุณาระบุจำนวนถุงอย่างน้อย 1 รายการ (ถ้าไม่สะดวกให้คีย์ 0 ถุง) ','ข้อความแจ้งเตือน',function(){
									return false;
				    			});
	            				return false;
	            			}
	            			checkSaleMan();
					 		$('#dlgSetBag').dialog('close');
							return false;
					 	}
	    		    },
	    		    beforeClose:function(){
	    		    	//*WR19082015 need to key form set bag
            			if( $chk_none_item=='Y'){
            				jAlert('กรุณาระบุจำนวนถุงอย่างน้อย 1 รายการ (ถ้าไม่สะดวกให้คีย์ 0 ถุง) ','ข้อความแจ้งเตือน',function(){
								return false;
			    			});
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
       //-------------- SET BAG 25092013-------------------------------		
		
	}//func
	/////////////////////////13022014//////////////////////////////////////
	
	
	
	/////////////////////////16022017 /////////////////////////////////////
	function jsonEscape(str)  {
	    return str.replace(/\n/g, "\\\\n").replace(/\r/g, "\\\\r").replace(/\t/g, "\\\\t");
	}
	
	function flgCommand(com,grid){
		if(com=='Refresh'){
			$("#tbl_ord").flexOptions({newp:1}).flexReload();
		}//if refresh
	}//func
	
	$(function(){
		//init form 
		initOrdForm();
		initOrdTemp();
		var gHeight=300,wcol=250,gWidth = parseInt((screen.width*50)/100)-430;
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
						{display: 'จำนวนเงิน', name : 'amount', width :90, sortable : false, align: 'center'},
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

		$("#ord_doc_no").val('').focus();
		$('#ord_doc_no').keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 			
	        if(key == 13){
	        	evt.stopPropagation();
				evt.preventDefault();
				var ord_doc_no=$('#ord_doc_no').val();					
				if(ord_doc_no.length==0){
					jAlert('กรุณาระบุเลขที่เอกสารใบสั่งซ์้อ', 'ข้อความแจ้งเตือน',function(){
		            	$("#ord_doc_no").focus();
		            	return false;
			        });
					return false;
				}
				$.ajax({
					type:'post',
					url:'/sales/ecommerce/chkexistorder',
					cache:false,
					data:{
						refer_doc_no:ord_doc_no,
						rnd:Math.random()
					},success:function(data){
						
						if(data!=''){
							$('#product_id').focus();							
							var objJson=$.parseJSON(data);						
							var address=objJson.address1 + " " + objJson.address2 + " " + objJson.address3;
							$('#ord_member_no').val(objJson.member_id);
							$('#ord_fullname').val(objJson.name);
							$('#ord_address').val(address);
						}else{
							jAlert('เอกสารถุก Confirm แล้วไม่สามารถทำซ้ำได้ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
				            	$("#ord_doc_no").focus();
				            	return false;
					        });
						}
					}
				});
				
	        	return false;
	        }
		});
		
		/***
		 * @desc
		 * @create 17022017
		 */
		
		
		 $("#quantity").keypress( function(evt) {									
				var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
		        if(key == 13){
		            evt.preventDefault();
		            var quantity=jQuery.trim($("#quantity").val());
		            if(quantity.length==0 || quantity=='0'){
		            	jAlert('กรุณาระบุจำนวนสินค้า','ข้อความแจ้งเตือน',function(){
							 $("#quantity").focus();
		    				 return false;
			    		 });
			        }else{
		            	$("#product_id").focus();
			        }
		        }
		 });
		
		/***
		 * @desc
		 * @create 17022017
		 */
		
		$("#product_id").keypress( function(evt) {									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	        	evt.stopPropagation();
	            evt.preventDefault();
	            var doc_no=$.trim($("#ord_doc_no").val());
	            var product_id=$.trim($("#product_id").val());
	            var quantity=$.trim($("#quantity").val());	 	            
				var cashier_id=$("#cashier_id").val();	
				
				if(doc_no.length==0){
	            	jAlert('กรุณาระบุเลขที่เอกสาร','ข้อความแจ้งเตือน',function(){
						 $("#ord_doc_no").focus();
	    				 return false;
		    		 });
		    		 return false;
		        }
		        	
		        if(product_id.length==0){
	            	jAlert('กรุณาระบุรหัสสินค้า','ข้อความแจ้งเตือน',function(){
						 $("#product_id").focus();
	    				 return false;
		    		 });
		    		 return false;
		        }
		        
		        if(quantity.length==0){
	            	jAlert('กรุณาระบุจำนวนสินค้า','ข้อความแจ้งเตือน',function(){
						 $("#quantity").focus();
	    				 return false;
		    		 });
		    		 return false;
		        }
		        
		        //------------------------ check product expire --------------------		       
		       	 
				$.ajax({
					        type:'post',
					        url:'/sales/ecommerce/chkproductpicking',
					        cach:false,
					        data:{	 
				        		doc_no:doc_no,
				        		product_id:product_id,
				        		quantity:quantity,
				        		cashier_id:cashier_id					
				        	},
				        	success:function(data){
				        		var arr_res=data.split('#');	
								if(arr_res[0]=='2'){
									jAlert('จำนวนสินค้าในสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){															
										$('#product_id').select().focus();
										return false;
									});	
								}else if(arr_res[0]=='4'){
									jAlert('ไม่พบรายการ ' + arr_res[1] + ' ในใบสั่งซื้อ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){										
										$('#product_id').select().focus();
										return false;
									});	
								}else if(arr_res[0]=='5'){
									jAlert('ไม่สามารถคีย์จำนวนสินค้า ' + arr_res[1] + ' มากกว่าในใบสั่งซื้อ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){										
										$('#product_id').select().focus();
										return false;
									});	
								}else if(arr_res[0]=='0'){
									jAlert('ไม่พบสินค้าในทะเบียน กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){										
										$('#product_id').select().focus();
										return false;
									});	
								}else{
									 $('#product_id').val(arr_res[1]);
									 $('#btn_submit_product').trigger('click');
								}
				        		
					        }
					    });
		        //------------------------ check product expire --------------------
		        
		        
	            //$('#quantity').focus();	           
	            return false;
	        }
	    });//keypress
		
		
		/**
		 * @desc add and check product to confirm order
		 * @create 22/02/2017
		 * 
		 */
		$('#btn_submit_product').click(function(evt){
			evt.stopPropagation();
			evt.preventDefault();			
			///////////////////////////////////////////			
			setPdtPickingToConfirmOrder();
			///////////////////////////////////////////
			return false;			
		});


	   

		$("#bws_orddocno").click(function(e){
			e.stopPropagation();
			e.preventDefault();
			$('#csh_trn_diary2_sl').val('Y');
			$('#csh_status_no').val('00');
			var status_no=$('#csh_status_no').val();
						
			//---------------- order ----------------------						
			//open dlg web order
			var dlgWebOrd = $('<div id="dlgWebOrder">\
	            <p>Web Order</p>\
	        </div>');
	        dlgWebOrd.dialog({
	        	autoOpen:true,
				width:'60%',
				height:550,	
				modal:true,
				resizable:true,
	            title: "รายการ Order ",
	           position:['center','center'],
	            open:function(){	    
	            	$(this).dialog('widget')
		            .find('.ui-dialog-titlebar')
		            .removeClass('ui-corner-all')
		            .addClass('ui-corner-top');
	            	$('#dlgWebOrder').html('');
	            	$.ajax({
	            		type:'post',
	            		url:'/sales/ecommerce/getweborder',
	            		cache:false,
	            		data:{
	            			rnd:Math.random()
	            		},success:function(data){
	            			$('#dlgWebOrder').html(data);					            			
	            			/////////// add data table ////////
							var oTable = $('#tableorderweb').dataTable();
							var chk_data=$('p[id="item_not_found"]');
							if(chk_data.length>0) return false;
							$('#tableorderweb').dataTable({
								"bJQueryUI":false,
								"bDestroy": true,
				       			"fnDrawCallback": function(){
				       												       	
					       		      $('table#tableorderweb td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
					       		      $('table#tableorderweb td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
					       		      
						       		   $('#tableorderweb tr').each(function(){
							       			$(this).click(function(e){
							       				e.stopImmediatePropagation();	          
							       				//e.stopPropagation();
							       				e.preventDefault();							       				
							       				var strJson=$(this).attr('iddocno');								       				
							       				if(strJson!=''){
									       				var seldocno=$.parseJSON(strJson);
									       				$('#ord_doc_no').val(seldocno.doc_no);	       			 								       				
									       				if(seldocno.doc_no=='') return false;									       				
									       				cmdEnterKey("ord_doc_no");		   
													    $("#dlgWebOrder").dialog('close');
													    return false;														    
									       		}
							       			});
							       		});
					       		      
				       			}//end callback
							});	
							 oTable.fnSort([[0,'desc'],[1,'desc']]);
							/////////// add data table ////////						
	            			
	            		}
	            	});
	            	return false;
	            },
	            close:function(){
	            	$('#tableorderweb').empty();
	            	$(this).remove();
	            }
	        });				
		//open dlg web order
				 
		//---------------- order ----------------------	
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
				
				//check net head compare detail
				$.ajax({
					type:'post',
					url:'/sales/ecommerce/cmppickingconfirmorder',
					dataType:'json',
					data:{
						doc_no:ord_doc_no,
						rnd:Math.random()
					},success:function(data){	
						
						if(data.success=='Y'){
							setBag2Temp();
						}else{
							jAlert(data.msg_error, 'ข้อความแจ้งเตือน',function(){			
								$('#product_id').focus().select();
				            	return false;
					        });
						}
						
						
						
					}
				});
				
		});
		
	});//ready