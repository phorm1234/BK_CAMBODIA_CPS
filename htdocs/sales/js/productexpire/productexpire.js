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
	
	
	function openFormConfirmDoc(){		
		/**
		 *@desc
		 *@param
		 *@last modify:30102012
		 */				
		var opts_dlgFormConfirmDoc={
				autoOpen:false,
				width:'55%',
				height:'300',
				modal:true,
				resizeable:true,
				position:'top',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"ยกเลิกเอกสาร",
				open:function(){					
					$("*:focus").blur();
					$("#dlgFormConfirmDoc").html("");
					$.ajax({
						type:'post',						
						url:'/sales/productexpire/formconfirmdoc',
						cache:false,
						data:{
							now:Math.random()
						},
						success:function(data){
							$(this).parent().find('select, input, textarea').blur();
							$("#dlgFormConfirmDoc").html(data);
							//----------------- START NEW ARIVAL
						    

						   $(".ui-widget-overlay").live('click', function(){
						    	$("#ros_employee_id").focus();
							});
							
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
								$("#ros_employee_id").focus();
							});
								
							$("#ros_employee_id").focus();
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
					            var ros_employee_id=jQuery.trim($("#ros_employee_id").val());	
					            var ros_password=jQuery.trim($("#ros_password").val());
					            
					            if(ros_employee_id.length==0){
					            	jAlert('กรุณาป้อนรหัส ROS/AROM','ข้อความแจ้งเตือน',function(){
										$("#ros_employee_id").val('').focus();
										return false;
					    			});
					            	return false;
					            }					           	            
					            if(ros_password==''){
					            	jAlert('กรุณาป้อนรหัสผ่านผู้ทำการยกเลิกเอกสาร','ข้อความแจ้งเตือน',function(){
										$("#ros_password").val('').focus();
										return false;
					    			});
					            	return false;
					            }				
					          
					            //check ros/arom
					            
					            var opts={
							            type:"post",
							            url:"/sales/cashier/getemp",
							            data:{
							            	employee_id:ros_employee_id,
							            	password:ros_password,
											actions:'ros'
						            	},
						            	success:function(data){
						            		var arr_data=data.split('#');
											if($.trim(arr_data[0])==""){
												jAlert('ไม่พบรหัส ROS หรือรหัสผ่านไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#ros_employee_id").val('').focus();
													return false;
								    			});
											}else if($.trim(arr_data[3])=='P'){
												jAlert('ขณะนี้พนักงาน ROS ไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#ros_employee_id").val('').focus();
													return false;
								    			});
											}else if($.trim(arr_data[3])=='N'){
												jAlert('ขณะนี้พนักงาน ROS ไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#ros_employee_id").val('').focus();
													return false;
								    			});
											}else{
								            	if(data!=""){
										            	$("#ros_employee_id").val(arr_data[0]);
										            	////////////// CANCEL BILL /////////////////
										            	confirmProductExpire(ros_employee_id);
										            	$("#dlgFormConfirmDoc").dialog("close");
										            	////////////// CANCEL BILL /////////////////													       
									            	}//end if data!=''		
												}//end else arr_data
							            }//success							            
							    };//opts
					            $.ajax(opts);
					            return false;
					            
					            return false;				     
						//---------------------- submit
						$(this).dialog("close");
						return false;
					}
				},
				close:function(){
					$("#dlgFormConfirmDoc").dialog("destroy");
				}
		};
		$("#dlgFormConfirmDoc").dialog("destroy");
		$("#dlgFormConfirmDoc").dialog(opts_dlgFormConfirmDoc);			
		$("#dlgFormConfirmDoc").dialog("open");
	}//func
	
	
	function openFormCancelDoc(){		
		/**
		 *@desc
		 *@param
		 *@last modify:30102012
		 */				
		var opts_dlgFormCancelDoc={
				autoOpen:false,
				width:'55%',
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
						url:'/sales/productexpire/formcanceldoc',
						cache:false,
						data:{
							now:Math.random()
						},
						success:function(data){
							$(this).parent().find('select, input, textarea').blur();
							$("#dlgFormCancelDoc").html(data);
							//----------------- START NEW ARIVAL
						    

						   $(".ui-widget-overlay").live('click', function(){
						    	$("#ros_employee_id").focus();
							});
							
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
								$("#ros_employee_id").focus();
							});
								
							$("#ros_employee_id").focus();
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
					            var ros_employee_id=jQuery.trim($("#ros_employee_id").val());	
					            var ros_password=jQuery.trim($("#ros_password").val());
					            
					            if(ros_employee_id.length==0){
					            	jAlert('กรุณาป้อนรหัส ROS/AROM','ข้อความแจ้งเตือน',function(){
										$("#ros_employee_id").val('').focus();
										return false;
					    			});
					            	return false;
					            }					           	            
					            if(ros_password==''){
					            	jAlert('กรุณาป้อนรหัสผ่านผู้ทำการยกเลิกเอกสาร','ข้อความแจ้งเตือน',function(){
										$("#ros_password").val('').focus();
										return false;
					    			});
					            	return false;
					            }				
					          
					            //check ros/arom
					            
					            var opts={
							            type:"post",
							            url:"/sales/cashier/getemp",
							            data:{
							            	employee_id:ros_employee_id,
							            	password:ros_password,
											actions:'ros-saleman'
						            	},
						            	success:function(data){
						            		var arr_data=data.split('#');
											if($.trim(arr_data[0])==""){
												jAlert('ไม่พบรหัส พนักงานขาย/ROS หรือรหัสผ่านไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#ros_employee_id").val('').focus();
													return false;
								    			});
											}else if($.trim(arr_data[3])=='P'){
												jAlert('ขณะนี้พนักงานขาย/ROS ไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#ros_employee_id").val('').focus();
													return false;
								    			});
											}else if($.trim(arr_data[3])=='N'){
												jAlert('ขณะนี้พนักงานขาย/ROS ไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
													$("#ros_employee_id").val('').focus();
													return false;
								    			});
											}else{
								            	if(data!=""){
										            	$("#ros_employee_id").val(arr_data[0]);
										            	////////////// CANCEL BILL /////////////////
										            	cancelProductExpire2(ros_employee_id);
										            	$("#dlgFormCancelDoc").dialog("close");
										            	////////////// CANCEL BILL /////////////////													       
									            	}//end if data!=''		
												}//end else arr_data
							            }//success							            
							    };//opts
					            $.ajax(opts);
					            return false;
					            
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
	
	function cancelProductExpire2(ros_employee_id){
		/**
		 * @desc
		 * @return
		 */
		var doc_no=$.trim($("#doc_no").val());
		var doc_date=$("#doc_date_alternate").val();
		var remark=$("#remark").val();
		var cashier_id=$('#cashier_id').val();
				
		if(doc_no.length<1){
			jAlert('กรุณาระบุเลขที่เอกสารที่ต้องการยกเลิก กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
				 $("#doc_no").focus();
				 return false;
    		 });				    	
	    	return false;
		}
		
		
		var opts={
				type:'post',
				url:'/sales/productexpire/cancelproductexpire',
				cache:false,
				data:{
					doc_no:doc_no,
					doc_date:doc_date,
					cashier_id:cashier_id,
					ros_employee_id:ros_employee_id,
					remark:remark,
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');		
					if(arr_data[0]=='1'){						
						jAlert('ยกเลิกเอกสาร ' + arr_data[1] + ' สมบูรณ์','ผลการบันทึก',function(){							
							initFormProductExpire();
							initProductExpireTemp();										
		    				 return false;
			    		 });
						
					}else{
						jAlert('เกิดปัญหา ไม่สมารถยกเลิกเอกสารเลขที่\n' + arr_data[1],'ผลการบันทึก',function(){
							 $("#product_id").focus();
							 return false;
			    		 });
					}
					
				}
			};
			jConfirm('คุณต้องการ ยกเลิกเอกสาร ' + doc_no + ' ใช่หรือไม่?', 'ยืนยันการบันทึก', function(r) {
			    if(r){	
			    	$.ajax(opts);
				}else{
					$("#doc_no").focus();
				}
			});
		
	}//func
	
	function confirmProductExpire(ros_employee_id){
		/**
		 * @desc
		 * @return
		 */
		var doc_no=$.trim($("#doc_no").val());
		var doc_date=$("#doc_date_alternate").val();
		var remark=$("#remark").val();
		var cashier_id=$('#cashier_id').val();
		
		
		if(doc_no.length<1){
			jAlert('กรุณาระบุเลขที่เอกสารที่ต้องการ Confirm กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
				 $("#doc_no").focus();
				 return false;
    		 });				    	
	    	return false;
		}
		
		
		var opts={
				type:'post',
				url:'/sales/productexpire/confirmproductexpire',
				cache:false,
				data:{
					doc_no:doc_no,
					doc_date:doc_date,
					cashier_id:cashier_id,		
					ros_employee_id:ros_employee_id,
					remark:remark,
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');		
					if(arr_data[0]=='1'){						
						jAlert('Confirm เอกสาร ' + arr_data[1] + ' สมบูรณ์','ผลการบันทึก',function(){							
							initFormProductExpire();
							initProductExpireTemp();										
		    				 return false;
			    		 });
						
					}else{
						jAlert('เกิดปัญหา ไม่สมารถ Confirm เอกสารเลขที่\n' + arr_data[1],'ผลการบันทึก',function(){
							 $("#doc_no").focus();
							 return false;
			    		 });
					}
					
				}
			};
			jConfirm('คุณต้องการ Confirm เอกสาร ' + doc_no + ' ใช่หรือไม่?', 'ยืนยันการบันทึก', function(r) {
			    if(r){	
			    	$.ajax(opts);
				}else{
					$("#doc_no").focus();
				}
			});
		
	}//func
	
	function saveTransProductExpire(){
		/**
		 * @desc
		 * @return
		 */
		var doc_no=$.trim($("#doc_no").val());
		var doc_date=$("#doc_date_alternate").val();
		var remark=$("#remark").val();
		var cashier_id=$('#cashier_id').val();
		
		if(sum_quantity=='0' || sum_quantity==''){
			jAlert('ไม่พบการทำรายการสินค้า ไม่สามารถบันทึกได้','ข้อความแจ้งเตือน',function(){
				$("#product_id").focus();
				 	return false;
    		 });
			return false;
		}
		var opts={
				type:'post',
				url:'/sales/productexpire/saveproductexpire',
				cache:false,
				data:{
					doc_no:doc_no,
					doc_date:doc_date,
					cashier_id:cashier_id,			
					remark:remark,
					rnd:Math.random()
				},
				success:function(data){
					var arr_data=data.split('#');		
					if(arr_data[0]=='1'){
						//printBillCN(data);
						jAlert('บันทึกข้อมูลสมบูรณ์ '+arr_data[1],'ผลการบันทึก',function(){
							//init form cn
							initFormProductExpire();
							initProductExpireTemp();										
		    				 return false;
			    		 });
						
					}else{
						jAlert('เกิดปัญหา\n'+arr_data[1]+'\n ไม่สามารถบันทึกได้','ผลการบันทึก',function(){
							 $("#product_id").focus();
							 return false;
			    		 });
					}
					
				}
			};
			jConfirm('คุณต้องการบันทึกรายการใช่หรือไม่?', 'ยืนยันการบันทึก', function(r) {
			    if(r){	
			    	$.ajax(opts);
				}else{
					$("#product_id").focus();
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
			$("#tbl_product_expire").flexOptions({newp:1}).flexReload();
		}//if refresh
		
		if (com == 'Delete') {
			
			var doc_status=$('#doc_status').val();
			if(doc_status=='C'){
	        	 jAlert('เอกสาร Cancel แล้ว ไม่สามารถลบรายการได้','ข้อความแจ้งเตือน',function(){
	        		 $('#doc_no').focus();
					 return false;
				  });
	        	 return false;
		    }
			
			var doc_status=$('#doc_status').val();
			if(doc_status=='Y'){
	        	 jAlert('เอกสาร Confirm แล้ว ไม่สามารถลบรายการได้','ข้อความแจ้งเตือน',function(){
	        		 $('#doc_no').focus();
					 return false;
				  });
	        	 return false;
		    }
			
			
			var doc_no=$('#doc_no').val();				
			if(doc_no.length>0){
	        	 jAlert('เอกสารรอการ Confirm ไม่สามารถลบรายการได้','ข้อความแจ้งเตือน',function(){
	        		 $('#doc_no').focus();
					 return false;
				  });
	        	 return false;
		    }
			
			var del_list='';
			var del_list_show='';
			$('.trSelected', grid).each(function() {
				del_list+= $(this).attr('absid')+"#";
				del_list_show+=$(this).attr('id').substr(3)+",";
			});
			
			//del_list=del_list.substring(0,del_list.length-1);
			del_list=del_list.substring(0,del_list.length-1);
			
			//del_list_show=del_list_show.substring(0,del_list_show.length-1);
			jConfirm('คุณต้องการลบรายการ '+del_list_show+' ใช่หรือไม่?','ยืนยันการลบรายการ', function(r){
		        if(r){
					var opts={
							   type:"POST",
							   cache:false,
							   url: "/sales/productexpire/delproductexpire",
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
											getProductExpire();
											$('#product_id').focus();
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
	
	function setProductExpire(){
		var doc_no=jQuery.trim($("#doc_no").val());
		var remark=$("#remark").val();
		var doc_date=$("#doc_date_alternate").val();
		var manufac_date=$("#sel_manufac_date_alternate").val();
		var product_id=$("#product_id").val();
		var quantity=$("#quantity").val();
		var cashier_id=$("#cashier_id").val();			 
		$.ajax({
			        type:'post',
			        url:'/sales/productexpire/setproductexpire',
			        cach:false,
			        data:{		        		
		        		doc_no:doc_no,	
		        		remark:remark,
		        		doc_date:doc_date,
		        		manufac_date:manufac_date,
		        		product_id:product_id,
		        		quantity:quantity,
		        		cashier_id:cashier_id					
		        	},
		        	success:function(data){
		        		var arr_res=data.split('#');
		        		if(arr_res[0]=='6'){
							jAlert('รหัสสินค้า ' + product_id + ' ถูก Lock ห้ามคีย์ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){															
								$('#product_id').select().focus();
								return false;
							});	
						}else if(arr_res[0]=='5'){
							jAlert('สินค้าต้องผลิตมาแล้วครบ 18 เดือนขึ้นไป กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){															
								$('#product_id').select().focus();
								return false;
							});	
						}else if(arr_res[0]=='2'){
							jAlert('จำนวนสินค้าในสต๊อกไม่พอ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){															
								$('#product_id').select().focus();
								return false;
							});	
						}else if(arr_res[0]=='3'){
							jAlert('ระบบมีปัญหาไม่สมารถบันทึกรายการได้ กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
								//ไม่พบสินค้า ใน com_product_master
								$('#product_id').select().focus();
								return false;
							});	
						}else if(arr_res[0]=='4'){
							jAlert('ต้องเป็นสินค้าในกลุ่งสินค้า Expire เท่านั้น กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
								//ไม่พบสินค้า ใน com_product_master
								$('#product_id').select().focus();
								return false;
							});	
						}else if(arr_res[0]=='0'){
							jAlert('ไม่พบสินค้าในทะเบียน กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
								//ไม่พบสินค้า ใน com_product_expire
								$('#product_id').select().focus();
								return false;
							});	
						}else{
							//disableFormProductExpire();
							$("#quantity").val('1');
							$("#manufac_date").val('');
				        	$("#product_id").val('').focus();
				        	
			        		getProductExpire();
						}
		        		
			        }
			    });
	}//func
	
	function getProductExpire(){	
		/*
		 *@desc 
		 */
		$.ajax({
			type:'post',
			url:'/sales/productexpire/getpagesproductexpire',
			cache: false,
			data:{				
				rp:14,
				now:Math.random()
			},
			success:function(data){	
				$("#tbl_product_expire").flexOptions({newp:data}).flexReload();
				getSubTotalProductExpire();
				return false;
			}
		});
	}//func

	function getSubTotalProductExpire(){
		/**
		*@name getSubTotalCn
		*@desc 
		*/
		
		var doc_no_ref=jQuery.trim($("#doc_no").val());
		$.getJSON(
				"/sales/productexpire/subtotalproductexpire",
				{
					doc_no_ref:doc_no_ref,
					actions:'getSumProductExpireTemp'
				},
				
				function(data){
						$.each(data.sumproductexpire, function(i,m){
							
							if(m.exist=='yes' && m.sum_amount!='' && parseInt(m.sum_amount)!='0'){
								var sum_quantity=parseInt(m.sum_quantity);								
								var sum_amount=parseFloat(m.sum_amount);								
								sum_amount=sum_amount.toFixed(2);
								$('#sum_quantity').val(sum_quantity);
								$('#sum_amount').val(addCommas(sum_amount));
								
								//--------------------------------------------------------
								
							}else{
								$('#sum_quantity').val('0');								
								$('#sum_amount').val('0.00');
								//$('#sum_net_amt').val('0.00');
							}	
						});//foreach
						return false;
				}//
		);
		
	}//func

	function initFormProductExpire(){
		$("#doc_no").val('');
		$("#doc_status").val('');
		$("#doc_no").attr('readonly', true);
		$("#remark").val('').removeAttr('readonly');		
		$("#manufac_date").val('');
		$("#product_id").val('');
		$("#quantity").val('1');
		//button
		
		$("#btn_addpdtexpire").prop("disabled", false );
		$("#btn_submitpdtexpire").prop("disabled", false );
	}

	function disableFormProductExpire(){
		
		$("#bws_docno").attr('readonly',true);
		$("#remark").attr("disabled",true);
		$("#doc_date").attr('readonly', true);
		$("#manufac_date").attr('readonly', true);
		$("#product_id").attr('readonly', true);
		$("#quantity").attr('readonly', true);	
		
	}//func
		
	
	function disableFormProductExpireByConfirm(){
		//confirm and cancel
		$("#doc_no").attr('readonly', true);
		$("#doc_date").attr('readonly', true);
		$("#remark").attr("readonly",true);
		$("#manufac_date").attr('disabled', true);
		$("#product_id").attr('readonly', true);
		$("#quantity").attr('readonly', true);	
		
		
		$("#btn_addpdtexpire").prop("disabled", true );
		$("#btn_submitpdtexpire").prop("disabled", true );
		
		
		
	}//func

	function initProductExpireTemp(){	
		var opts_cn={
				type:'post',
				url:'/sales/productexpire/initproductexpiretemp',
				data:{
					rnd:Math.random()
				},success:function(){
					getProductExpire();
					$("#remark").focus();
					return false;
				}
					
			};
		$.ajax(opts_cn);
	}//func
	
	function msgSelProductError(){
		jAlert('จำนวนสินค้าคงเหลือไม่พอ กรุณาคีย์จำนวนไม่เกินยอดคงเหลือในลำดับที่เลือก','ข้อความแจ้งเตือน',function(r){
			if(r){
				$("#quantity").focus();
			}
    		return false;
		 });
	}//func
	
	
	
	
	$(function(){
		//init form product expire
		initFormProductExpire();
		initProductExpireTemp();
		
		$("#manufac_date").datepicker({
			dateFormat: 'dd/mm/yy',
			altField: '#sel_manufac_date_alternate',
			altFormat:'yy-mm-dd',
			changeMonth: true,
	        changeYear: true
		});
		
		
		
		var gHeight=300,wcol=250,gWidth = parseInt((screen.width*70)/100)-600;
		if ((screen.width>=1280) && (screen.height>=1024)){
			//gHeight=(screen.height-(screen.height*(75/100))-5);
			gHeight=(screen.height-(screen.height*(75/100))-5);
			wcol=410;
		}
		$("#tbl_product_expire").flexigrid(
				{
					//cntemp
					url:'/sales/productexpire/productexpiretemp',
					dataType: 'json',
					colModel : [
						{display: '#', name : 'id', width :20, sortable : true, align: 'center'},
						{display: 'รหัส', name : 'product_id', width : 100, sortable : true, align: 'center'},
						
						{display: 'รายละเอียด', name : 'product_name',width :gWidth, sortable : false, align:'left'},
						{display: 'วันผลิต', name : 'lot_date_show', width : 100, sortable : true, align: 'center'},
						
						{display: 'ราคา', name : 'price', width :70, sortable : false, align: 'center'},
						{display: 'จำนวน', name : 'quantity', width : 50, sortable : false, align: 'center'},
						{display: 'จำนวนเงิน', name : 'amount', width :70, sortable : false, align: 'center'},
						
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

		$("#remark").val('').focus();
		var chk_process=0;
		$("#doc_no").keypress( function(evt) {			
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            var doc_no=jQuery.trim($("#doc_no").val());
	            
	            if(doc_no==''){
	            	jAlert('กรุณาระบุรหัสเอกสารอ้างอิง','ข้อความแจ้งเตือน',function(){
						 $("#doc_no").focus();
	    				 return false;
		    		 });
		        }else{	 
		        	if(chk_process==0){
		        		chk_process=1;
			        	$.ajax({
					        	type:'post',
					        	url:'/sales/productexpire/getproductexpire2confirm',
					        	cache:false,
					        	data:{
				        			doc_no:doc_no,
				        			rnd:Math.random()
				        		},
				        		success:function(data){
				        			chk_process=0;
				        			getProductExpire();
					        		$('#btn_confirmpdtexpire').focus();
					        	}
					        });
		        	}
		        	
		        }					           
	            return false;
	        }
	    });//keypress
		

		$("#remark").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
		        evt.preventDefault();
//		        var remark= $("#remark");
//		        remark=$.trim(remark.val());
//	        	if(remark.length<1){
//	        		jAlert('กรุณาระบุรายละเอียดเอกสาร','ข้อความแจ้งเตือน',function(){			        		
//						 $("#remark").focus();
//	    				 return false;
//		    		});
//		    		return false;
//		        }else{
//			        $("#product_id").focus();
//			    }
		        $("#product_id").focus();
	        }
		});//keypress
		
		
		$("#product_id").keypress( function(evt) {									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	        	evt.stopPropagation();
	            evt.preventDefault();
	            
	            var product_id=$.trim($("#product_id").val());
	            var quantity=$.trim($("#quantity").val());
	            var remark=$.trim($("#remark").val());
	            
	            
//	            if(remark.length==0){
//	            	jAlert('กรุณาระบุรายละเอียดเอกสาร','ข้อความแจ้งเตือน',function(){
//						 $("#remark").focus();
//	    				 return false;
//		    		 });
//		    		 return false;
//		        }else 
		        	
		        if(product_id.length==0){
	            	jAlert('กรุณาระบุรหัสสินค้า','ข้อความแจ้งเตือน',function(){
						 $("#product_id").focus();
	    				 return false;
		    		 });
		    		 return false;
		        }
		        
		        //------------------------ check product expire --------------------
		       
				var doc_date=$("#doc_date_alternate").val();				
				var product_id=$("#product_id").val();
				var quantity=$("#quantity").val();
				var cashier_id=$("#cashier_id").val();			 
				$.ajax({
					        type:'post',
					        url:'/sales/productexpire/chkproductexpire',
					        cach:false,
					        data:{	 
				        		doc_date:doc_date,
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
									jAlert('ต้องเป็นสินค้าในกลุ่งสินค้า Expire เท่านั้น กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
										//ไม่พบสินค้า ใน com_product_master
										$('#product_id').select().focus();
										return false;
									});	
								}else if(arr_res[0]=='0'){
									jAlert('ไม่พบสินค้าในทะเบียน กรุณาตรวจสอบอีกครั้ง', 'ข้อความแจ้งเตือน',function(){
										//ไม่พบสินค้า ใน com_product_expire
										$('#product_id').select().focus();
										return false;
									});	
								}else{
									 $('#product_id').val(arr_res[1]);
									 $('#quantity').focus();	
								}
				        		
					        }
					    });
		        //------------------------ check product expire --------------------
		        
		        
	            //$('#quantity').focus();	           
	            return false;
	        }
	    });//keypress


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
	            	$("#manufac_date").focus();
		        }
	        }
	    });
	    
	    
	    $("#manufac_date").keypress( function(evt){									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            var chk_manufac_date=jQuery.trim($("#manufac_date").val());
	            if(chk_manufac_date.length==0){
	            	jAlert('กรุณาระบุวันที่ผลิต','ข้อความแจ้งเตือน',function(){
						 $("#manufac_date").focus();
	    				 return false;
		    		 });
		        }else{
	            	$("#btn_addpdtexpire").focus();
		        }
	        }
	    });
	    
	    
	    
	    $('#manufac_date').datepicker({
	    	
    	    onSelect: function (date) {
    	    	alert("aaa");
    	    	$("#btn_addpdtexpire").focus();
    	    }
    	})
	    
	    $("#btn_addpdtexpire").keypress( function(evt){									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            setProductExpire();
	        }
	    });
	    
	    $("#btn_addpdtexpire").click(function(evt){
	    	evt.preventDefault();
            setProductExpire();
            return false;
	    });
	    
	    $('#btn_confirmpdtexpire').click(function(evt){
	    	evt.preventDefault();
	    	var doc_no=$.trim($("#doc_no").val());	
			var doc_status=$('#doc_status').val();
			
			if(doc_no.length>0){
				if(doc_status=='Y'){						
					jAlert('เอกสารนี้ถูก Confirm แล้วไม่สามารถ Confirm ซ้ำได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
						 $("#doc_no").focus();
	    				 return false;
		    		 });						
				}else if(doc_status=='C'){						
					jAlert('เอกสารนี้ถูก Cancel แล้วไม่สามารถ Confirm ซ้ำได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
						 $("#doc_no").focus();
	    				 return false;
		    		 });						
				}else{
					openFormConfirmDoc();
				}				
				return false;
				
			}else if(doc_no.length<1){
				jAlert('กรุณาระบุเลขที่เอกสารที่ต้องการ Confirm กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
					 $("#doc_no").focus();
					 return false;
	    		 });				    	
		    	return false;
			}
			openFormConfirmDoc();	    	
	    	//confirmProductExpire();	    	
	    	return false;
	    });
	    
	    $("#btn_confirmpdtexpire").keypress( function(evt){									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            $("#btn_confirmpdtexpire").trigger("click");	            
	            return false;
	        }
	    });
	    
	    $('#btn_cancel').click(function(evt){	    	
			evt.preventDefault();	
			var doc_no=$.trim($("#doc_no").val());		
			var doc_status=$('#doc_status').val();
			
			if(doc_no.length<1){
				jAlert('กรุณาระบุเลขที่เอกสารที่ต้องการ Cancel กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
					 $("#doc_no").focus();
					 return false;
	    		 });				    	
		    	return false;
			}else if(doc_status=='Y'){						
				jAlert('เอกสารนี้ถูก Confirm แล้วไม่สามารถยกเลิกได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
					 $("#doc_no").focus();
    				 return false;
	    		 });	
				return false;
			}else if(doc_status=='C'){						
				jAlert('เอกสารนี้ถูก Cancel แล้วไม่สามารถยกเลิกซ้ำได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
					 $("#doc_no").focus();
    				 return false;
	    		 });
				return false;
			}
			openFormCancelDoc();
			//cancelProductExpire2();	
			return false;
		});
	    
	    $('#btn_cancel').keypress( function(evt){									
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13){
	            evt.preventDefault();
	            $("#btn_cancel").trigger("click");	             
	            return false;
	        }
	    });

		$("#btn_submitpdtexpire").click(function(e){
				e.preventDefault();
				var doc_no=$('#doc_no').val();
				var doc_status=$('#doc_status').val();
				
				var sum_quantity=$('#sum_quantity').val();
				if(doc_no.length>0){
					if(doc_status=='Y'){						
						jAlert('เอกสารนี้ถูก Confirm แล้วไม่สามารถบันทึกซ้ำได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							 $("#doc_no").focus();
		    				 return false;
			    		 });						
					}else if(doc_status=='C'){						
						jAlert('เอกสารนี้ถูก Cancel แล้วไม่สามารถบันทึกซ้ำได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							 $("#doc_no").focus();
		    				 return false;
			    		 });						
					}else{
						jAlert('เอกสารนี้รอ Confirm ไม่สามารถบันทึกซ้ำได้ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
							 $("#doc_no").focus();
		    				 return false;
			    		 });	
					}
					
					return false;
					
				}else if(sum_quantity=='0' || sum_quantity==''){
					jAlert('ไม่พบการทำรายการ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
						 $("#product_id").focus();
	    				 return false;
		    		 });
					return false;
				}else{
			    	saveTransProductExpire();
			    }	
		});
		

		$("#bws_pdtexpire").click(function(evt){
			evt.preventDefault();
			
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
							$("#dlg_pdtexpire").html("");
							$("#dlg_pdtexpire").load("/sales/productexpire/bwspdtexpiredocno?now="+Math.random(),
									function(evt,ui){
										/////////// add data table ////////
								      
										var oTable = $('#tableNavCnDocNo').dataTable();		
										var chk_data=$('p[id="item_not_found"]');
										//alert(chk_data.length);
										if(chk_data.length>0){
											initFormProductExpire();
											initProductExpireTemp();
											return false;
										}
										$('#tableNavCnDocNo').dataTable({
											"bJQueryUI":false,
											"bDestroy": true,
							       			"fnDrawCallback": function(){
							       												       	
								       		      $('table#tableNavCnDocNo td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
								       		      $('table#tableNavCnDocNo td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
								       		      
									       		   $('#tableNavCnDocNo tr').each(function(){
										       			$(this).click(function(evt){
										       				evt.preventDefault();
										       				evt.stopPropagation();
										       				var strJson=$(this).attr('iddocno');			
										       				//alert(strJson);
										       				if(strJson!=''){
										       						initFormProductExpire();										       						
												       				var seldocno=$.parseJSON(strJson);
																    $("#doc_no").val(seldocno.doc_no);
																    $("#doc_date").val(seldocno.doc_date_show);
																    $("#doc_date_alternate").val(seldocno.doc_date);
																    $("#remark").val(seldocno.remark1);
																    $("#doc_status").val(seldocno.flag);
																    $("#doc_no").focus();
																    cmdEnterKey("doc_no");
																    $("#dlg_pdtexpire").dialog('close');
																    disableFormProductExpireByConfirm();
																    //alert(seldocno.flag);
																    /*
																    if(seldocno.flag=='Y' || seldocno.flag=='C'){
																    	disableFormProductExpireByConfirm();
																    }else{																    	
																    	$("#product_id").focus();
																    }*/
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
						$('#tableNavCnDocNo').empty();
					 },
					 buttons:{
						 "ปิด":function(){ 				
							$(this).dialog("close");
							return false;
						}
					}
				};			
				
				$('#dlg_pdtexpire').dialog('destroy');
				$('#dlg_pdtexpire').dialog(dialogOpts_cndocno);			
				$('#dlg_pdtexpire').dialog('open');
				return false;
		});
		
		
	});//ready