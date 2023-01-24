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
	
	function closeDay(){
		/**
		 * @param
		 * @author is-wirat		 
		 * @returns
		 */
		var dialogOpts_closeday = {
				autoOpen: false,
				width:370,		
				height:250,	
				modal:true,
				resizable:true,
				position:['center','center'],
				title:"ยืนยันการปิดบิลประจำวัน",
				closeOnEscape:true,
				open: function(){ 
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');
	   			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","margin-top":"0","background-color":"#BCDCD7",
		    			    										"font-size":"27px","color":"#000"});
	   			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	   			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	   			    // button style		
	   			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
					$("#dlg_closeday").html("");
					$("#dlg_closeday").load("/sales/cashier/formconfirmcloseday?action=confirmcloseday&now="+Math.random(),
								function(data){
										$("#sel_closeday").datepicker({dateFormat: 'dd/mm/yy',altField: '#sel_closeday_alternate',altFormat:'yy-mm-dd'});
								}
						);						
				},				
				close: function() {
					var sel_closeday=$("#sel_closeday_alternate").val();
					$('#sel_closeday').datepicker('hide');
					$('#dlg_closeday').dialog('destroy');
					//close iframe
			        closeWinIframe();
				 },
				 buttons: {
							"ยืนยันการปิดบิล":function(evt){ 	
					 				evt.preventDefault();
					 				evt.stopImmediatePropagation();					 				
					 				
					                var buttonDomElement = evt.target;					               
					                $(buttonDomElement).attr('disabled', true);
					 				
					 				var $sel_docdate=$("#sel_docdate");
					 					arr_day=$sel_docdate.val().split('/');
					 					$sel_docdate=arr_day[2]+"-"+arr_day[1]+"-"+arr_day[0];
								 	var $sel_closeday_alt=$("#sel_closeday_alternate");								 	
								 	if($sel_closeday_alt.val()==''){
								 		jAlert('กรุณาป้อนวันที่ปิดบิล','ข้อความแจ้งเตือน',function(){
						    				return false;
							    		 });							    		
									}else if($sel_closeday_alt.val()<$sel_docdate){
										jAlert('ไม่สามารถปิดบิลย้อนหลังได้','ข้อความแจ้งเตือน',function(){
						    				return false;
							    		 });		
									}else if($sel_docdate!=$sel_closeday_alt.val()){
										jAlert('กรุณาป้อนวันที่ปิดบิลให้ตรงกัน','ข้อความแจ้งเตือน',function(){
						    				return false;
							    		 });							    		 
									}else{										
										var opts={
											type:'post',
											url:'/sales/cashier/chkforcloseday',											
											cache:false,
											data:{				
												doc_date:$sel_closeday_alt.val(),
												now:Math.random()
											},
											success:function(data){	
												var arr_data=data.split('#');
												var msgResult="";
												var msgTitle="ข้อความแจ้งเตือน";
												if(arr_data[0]=='1'){													
													//call EDC													
													var opts_calledc=$.ajax({
																type:'post',
																url:'/sales/cashier/calledc',
																cache:false,
																data:{
																	actions:'settlement',
																	credit_net_amt:'0.00',
																	rnd:Math.random()
																},success:function(){
																	 jAlert('กรุณาป้อนรหัสผ่าน สรุปยอดบัตรเครดิตเครื่อง EDC','ข้อความแจ้งเตือน',function(r){
																		 if(r){
																			opts_calledc=null;																			
																			msgResult="ปิดบิลประจำวันเรียบร้อยแล้ว<br> <u>กรุณาส่งข้อมูลและรอผลการส่งก่อนปิดเครื่อง</u>";			
																			msgTitle="ผลการปิดบิลประจำวัน";
																			jAlert(msgResult,msgTitle,function(){
																				if(arr_data[0]=='1'){
																					$('#dlg_closeday').dialog("close");
																				}
															    				return false;
																    		 });																			
														    				return false;
																		 }
															    	 });	
																}
													});													
													return false;
												}else if(arr_data[0]=='2'){
													msgResult="ไม่พบข้อมูลการขายวันที่ "+$("#sel_docdate").val();
													msgTitle="ยืนยันการปิดบิลประจำวัน";
													jConfirm(msgResult+' คุณต้องการปิดบิลใช่หรือไม่?',msgTitle,function(r) {
													    if(r){ 
														    var opts2={
																    	type:'get',
																    	url:'/sales/cashier/confirmcloseday',
																    	cache:false,
																    	data:{
														    				action:'confirmcloseday',
														    				rnd:Math.random()
														    			},
														    			success:function(data){
														    				if(data=='1'){														    					
														    					//call EDC
														    					var opts_calledc=$.ajax({
																							type:'post',
																							url:'/sales/cashier/calledc',
																							cache:false,
																							data:{
																								actions:'settlement',
																								credit_net_amt:'0.00',
																								rnd:Math.random()
																							},success:function(){
																								 jAlert('กรุณาป้อนรหัสผ่าน สรุปยอดบัตรเครดิตเครื่อง EDC','ข้อความแจ้งเตือน',function(r){
																									 if(r){
																										opts_calledc=null;				
																										///////////////////// show message close day is complete /////////////////////
																										msgResult="ปิดบิลประจำวันเรียบร้อยแล้ว <br> <u>กรุณาส่งข้อมูลและรอผลการส่งก่อนปิดเครื่อง</u>";			
																										msgTitle="ผลการปิดบิลประจำวัน";
																										jAlert(msgResult,msgTitle,function(){
																											$('#dlg_closeday').dialog("close");
																						    				return false;
																							    		 });		
																										////////////////////  show message close day is complete ////////////////////
																					    				return false;
																									 }
																						    	 });	
																							}
																				});//end call edc																		
																				
																			}else{
																				$(buttonDomElement).attr('disabled', false);
																				jAlert("ไม่สามารถปิดบิลล่วงหน้าได้",msgTitle,function(){
																					return false;
																				});
																			}
															    		}
																    };
														    $.ajax(opts2);
														    return false;	
														}else{
															 $(buttonDomElement).attr('disabled', false);
														}
													});
												}else if(arr_data[0]=='3'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="เลขที่เอกสาร "+arr_data[2]+" ไม่ต่อเนืองจากการตรวจสอบวันที่ปิดบิลย้อนหลัง";
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });
												}else if(arr_data[0]=='4'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="เลขที่เอกสารไม่ต่อเนือง จากการตรวจสอบความต่อเนื่องของเลขที่เอกสารแต่ละประเภท";
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}else if(arr_data[0]=='5'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="พบใบลดหนี้เลขที่ "+arr_data[1]+"  ไม่ได้เปิดบิลเปลี่ยนสินค้า";
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}else if(arr_data[0]=='6'){
													msgResult="พบข้อมูลการอนุมัติ RQ ต้อง confirm RQ ก่อน";
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}else if(arr_data[0]=='7'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="Profile สมาชิกต้องบันทึกให้ครบภายใน 2 วันนับจากวันที่สมัคร";
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}else if(arr_data[0]=='8'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="พบข้อมูลการขายระหว่างการเช๊คสต๊อก";
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}else if(arr_data[0]=='9'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="ไม่สามารถปิดบิลก่อนเวลา "+arr_data[1];
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}else if(arr_data[0]=='10'){
													$(buttonDomElement).attr('disabled', false);
													msgResult="พบข้อมูลการโอนระหว่างเช็คสต๊อก "+arr_data[1];
													jAlert(msgResult,msgTitle,function(){
									    				return false;
										    		 });	
												}
												return false;
											}
										};
										$.ajax(opts);
									}
									return false;
								}
						 }
			};
		    $('#dlg_closeday').dialog('destroy');
			$('#dlg_closeday').dialog(dialogOpts_closeday);			
			$('#dlg_closeday').dialog('open');
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

	$(function(){
		initTblTemp();
		closeDay();	  
	});