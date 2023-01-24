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
	
function openIFrameInWindow(url) {
    var myWindow = window.open("");
    var iframe = document.createElement("iframe");
    iframe.src = url;
    myWindow.document.body.appendChild(iframe); 
    // myWindow.document.body.onbeforeunload = function () { alert("WINDOW CLOSED") }; 
	//    setTimeout(function(){
	//    	myWindow.close();
	//    },1500);
    myWindow.close();
    return false;
}//func

function openCashDrawer(url,name,windowWidth,windowHeight){
		/**
		*@desc
		*@param String url
		*@param String name
		*@param Integer windowWidth
		*@param Integer windowHeight
		*@return
		*/
//	    var myleft=(screen.width)?(screen.width-windowWidth)/2:100;
//	    var mytop=(screen.height)?(screen.height-windowHeight)/2:100;
	 	var myleft=0;
	    var mytop=0;
	    var properties = "width="+windowWidth+",height="+windowHeight;	    
	    properties +=",top="+mytop+",left="+myleft + ",toolbar=no, menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no";	  
	    myWindow =window.open(url,'_self',properties);
	    
	    //window.open('','_self',''); //this is needed to prevent IE from asking about closing the window.
	    //setTimeout('self.close();',500);
	    
	    //myWindow.close();
	    return false;
}//func


function closeWinIframe(){
		var frameWindow = document.parentWindow || document.defaultView;
        var outerDiv = $(frameWindow.frameElement.parentNode.parentNode);
        var curWindow = outerDiv.find(".window_top").contents().find("a.window_close");
        $(curWindow).closest('div.window').hide();
		var icons_id=curWindow.attr('href');
		$(icons_id,window.parent.document).hide('fast');
		//refresh parent
		window.parent.location.href = window.parent.location.href;
        return false;
	}//func
	
	function formConfirmOpen(){
		/**
		 * @param
		 * @author is-wirat		 
		 * @returns
		 */
		var m_cashdrawer="N";
		var dialogOpts_OpenCashDrawer= {
				autoOpen: false,
				width:400,		
				height:'auto',	
				modal:true,
				resizable:true,
				position:['center','center'],
				title:"Confirm Open CashDrawer",
				closeOnEscape:true,
				open: function(){ 
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	   			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","margin-top":"0","background-color":"#bca0c9",/*BCDCD7*/
		    			    										"font-size":"27px","color":"#000"});
	   			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	   			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#c2bcdc"}); /*C7D9DC*/
	   			    // button style		
	   			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
					$("#dlg_opencashdrawer").html("");
					$("#dlg_opencashdrawer").load("/sales/daytransaction/formconfirmopencashdrawer?action=open&now="+Math.random(),
								function(data){
										getLockStatus();
										m_cashdrawer=$("#m_cashdrawer").val();						 				
								}
						);						
				},				
				close: function() {
					$('#dlg_opencashdrawer').dialog('destroy');
					//close iframe
			        closeWinIframe();
				 },
				 buttons: {
							"Confirm":function(evt){ 	
					 				evt.preventDefault();
					 				evt.stopImmediatePropagation();			
					 				
					 				if(m_cashdrawer!='Y'){
					 					jAlert('Not found CashDrawer, please check again','Save Result Open CashDrawer',function(){
					    					return false;
							    		 });
					 					return false;
					 				}
					 				
					 				var cshdrw_employee_id=$("#cshdrw_employee_id").val();
					 				cshdrw_employee_id=$.trim(cshdrw_employee_id);
					 				if(cshdrw_employee_id.length==0){
					 					jAlert('Please Insert Employee ID','Alert!',function(){
					 						$("#cshdrw_employee_id").focus();
					    					return false;
							    		 });
					 					return false;
					 				}
					 				
					 				var remark_id=$('#remark_id').val();					 				
					 				if(remark_id=='0'){
					 					jAlert('Please provide a reason.','Alert!',function(){
					 						$("#remark_id").focus();
					    					return false;
							    		 });
					 					return false;
					 				}
					 				
					 				var opts={
								            type:"post",
								            //url:"/sales/cashier/getemp",
								            url:"/sales/daytransaction/getempopencashdrawer",
								            async: true,
								            data:{
								            	employee_id:cshdrw_employee_id,
												actions:'saleman'
							            	},
							            	success:function(data){		
							            		if(data==''){
							            			jAlert('Employee ID not found, please check again.','Alert!',function(){
														$("#cshdrw_employee_id").val('').focus();
														return false;
									    			});
							            			return false;
							            		}
							            									            		
						            			var obj_json=$.parseJSON(data);
						            			//alert(obj_json.check_status);
												if(obj_json.check_status == '' || obj_json.check_status == null){
							            			jAlert('Employee ID not found, please check again','Alert!',function(){
														$("#cshdrw_employee_id").val('').focus();
														return false;
									    			});
							            			return false;
							            		}													
												
//								            	var arr_data=data.split('#');
//												if($.trim(arr_data[0])==""){
//													jAlert('ไม่พบรหัสพนักงาน กรุณาตรวจสอบอีกครั้ง','Alert!',function(){
//														$("#cshdrw_employee_id").val('').focus();
//														return false;
//									    			});
//												}else
												if(obj_json.check_status=='P'){//$.trim(arr_data[3])
													jAlert('Employee are not online in the system, please check again','Alert!',function(){
														$("#cshdrw_employee_id").val('').focus();
														return false;
									    			});
												}else if(obj_json.check_status=='N'){
													jAlert('Employee are not checkin to the system, please check again','Alert!',function(){
														$("#cshdrw_employee_id").val('').focus();
														return false;
									    			});
												}else{													
													//confirm open cashdrawer
													var buttonDomElement = evt.target;					               
									                $(buttonDomElement).attr('disabled', true);	
													jConfirm('Do you want to open CashDrawer?','Confirm',function(r) {
													    if(r){ 
													    	//var remark=$.trim($('#remark').val());
													    	var remark_id=$('#remark_id').val();
													    	var opts_cashdr={
													    			type:'post',
													    			url:'/sales/daytransaction/confirmopencashdrawer',
													    			act:'open',
													    			data:{
													    				employee_id:cshdrw_employee_id,
													    				remark_id:remark_id,
														    			rnd:Math.random()
													    			},success:function(data){													    				
													    				if(data=='Y'){
													    					$('#dlg_opencashdrawer').dialog('close');
													    					openCashDrawer("http://localhost/sales/cmd/opencashdrawer.php?rnd="+Math.random(),"",5,5); 
													    					//openIFrameInWindow("http://localhost/sales/cmd/opencashdrawer.php?rnd="+Math.random());
													    					return false;
													    				}else{
													    					jAlert('Error, Can not save!','Save Result Open CashDrawer',function(){
																				$("#dlg_opencashdrawer").dialog("close");
														    					return false;
																    		 });
													    					return false;
													    				}
													    			}
													    	};
													    	$.ajax(opts_cashdr);
														    return false;	
														}else{
															 $(buttonDomElement).attr('disabled', false);
														}
													});
													//confirm open cashdrawer									            	
									            }
								            	
								            }
								    };
						            $.ajax(opts);
								}
						 }
			};
		    $('#dlg_opencashdrawer').dialog('destroy');
			$('#dlg_opencashdrawer').dialog(dialogOpts_OpenCashDrawer);			
			$('#dlg_opencashdrawer').dialog('open');
			return false;
	}//func	
	

	$(function(){
		formConfirmOpen();	  
	});//ready