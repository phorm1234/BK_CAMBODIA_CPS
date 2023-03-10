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

function printBillCorner(doc_no,doc_tp){
	/**
	 * @
	 */
	switch(doc_tp){
		case 'SL' :url2print='/sales/report/billvatshortcorner';break;
		case 'VT' :url2print='/sales/report/billvattotal';break;
		case 'DN' :url2print='/sales/report/billdncorner';break;
		case 'CN' :url2print='/sales/report/billcn';break;
		case 'RD' :url2print='/sales/report/billactpointrd';break;
		case 'PL' :url2print='/sales/inventory/reportpick';break;
	}
	//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 
	//return false;
	var opts={
			type:'get',
			url:url2print,		
			actions:'reprint',
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
}//func

function printBill(doc_no,doc_tp,co_promo_code,status_no){
		/**
		*@desc
		*@param String doc_no
		*@param String doc_tp
		*@return
		*/
		var url2print='';
		switch(doc_tp){
			case 'SL' :url2print='/sales/report/billvatshort';break;
			case 'VT' :url2print='/sales/report/billvattotal';break;
			case 'DN' :url2print='/sales/report/billshortdn';break;
			case 'CN' :url2print='/sales/report/billcn';break;
			case 'RD' :url2print='/sales/report/billactpointrd';break;
			case 'PL' :url2print='/sales/inventory/reportpick';break;
		}
		if(co_promo_code=='GROUP_TOUR'){
			url2print='/sales/report/billvatshorten';
		}
		if(status_no=='03'){
			url2print="/sales/report/billecouponemployee";
			if(doc_tp=='VT'){
				url2print="/sales/report/billvatecouponemployee";
			}
		}
		//popup(url2print+"?doc_no="+doc_no+"&rnd="+Math.random(),"",500,500);//for test print 
		//return false;
		var opts={
				type:'get',
				url:url2print,				
				actions:'reprint',
				cache:false,
				data:{
						doc_no:doc_no,
						actions:'reprint',
						rnd:Math.random()
					},
				success:function(){						
						return false;
				}
			};
		$.ajax(opts);
	}//func
	
	function resSearch(doc_no_start,doc_no_stop){
		var opts_QstDetail={
				autoOpen:false,
				width:'60%',
				height:'650',
				modal:true,
				resizeable:true,
				position:'top',
				showOpt: {direction:'up'},		
				closeOnEscape:true,	
				title:"???????????????????????????????????????",
				open:function(){					
					 $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"0 .1em 0 .1em","margin":"0 0 0 0","background-color":"#FFFFFF"});
					$("*:focus").blur();
					$("#dlgQstDetail").html("");
					var doc_tp=$('#doc_type_question').val();
					$.ajax({
						type:'post',
						url:'/sales/report/billdetail',
						cache:false,
						data:{
							doc_tp:doc_tp,
						    doc_no_start:doc_no_start,
						    doc_no_stop:doc_no_stop,
							actions:'brows_docstatus',
							now:Math.random()
						},
						success:function(data){
							$("#dlgQstDetail").html('');
							$("#dlgQstDetail").html(data);
							$(this).parent().find('select, input, textarea').blur();
						}//end success function
					});//end ajax pos
				},
				buttons: {
					"????????????????????????":function(){ 	
							var json_doc=$.parseJSON($('#str_docno').val());
							$.each(json_doc,function(i,data){									
								if(json_doc[i].keyin_st=='Y'){
									
									//---------------------- audit print bill manual -------------------------------
									$("<div id='dlgAuditPrintBill'></div>").dialog({
						            	   autoOpen:true,
							   				width:350,		
							   				height:'auto',	
							   				modal:true,
							   				resizable:false,
							   				position:"center",
							   				showOpt: {direction: 'up'},		
							   				closeOnEscape: true,
							   				title:"<span class='ui-icon ui-icon-person'></span>AUDIT : ???????????????????????????????????????????????????",
							   				open: function(){    
							   					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
												$(this).dialog('widget')
										            .find('.ui-dialog-titlebar')
										            .removeClass('ui-corner-all')
										            .addClass('ui-corner-top');
												$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#BCDCD7",
																																"font-size":"22px","color":"#666666",
																																"padding":"5 0.1em 0 0.1em"});   
								   				$("#dlgAuditPrintBill").append("&nbsp;&nbsp;???????????????????????? : <input type='password' id='audit_password' size='20' class='input-text-pos'/>");
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
																		jAlert('????????????????????????????????? AUDIT ????????????????????????????????????????????????????????????','Alert',function(){
																			$("#audit_password").focus();
																			return false;
														    			});
																	}else if(data!=""){
																		printBill(json_doc[i].doc_no,json_doc[i].doc_tp,json_doc[i].co_promo_code,json_doc[i].status_no);
																		$("#dlgAuditPrintBill").dialog('close');																		
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
									//---------------------- audit print bill manual -------------------------------
									
									return false;
								}else{
									printBill(json_doc[i].doc_no,json_doc[i].doc_tp,json_doc[i].co_promo_code,json_doc[i].status_no);
								}
								initFormQuestion();
							});
							$(this).dialog("close");
							return false;
					}
				},
				close:function(){					
				}
		};
		$("#dlgQstDetail").dialog("destroy");
		$("#dlgQstDetail").dialog(opts_QstDetail);
		$("#dlgQstDetail").dialog("open");
	}//func
	
	function initFormQuestion(){
		$("#doc_no_start").val('');
		$("#doc_no_stop").val('');
		//$("#doc_type_question").val('');
		$("#doc_no_start").focus();
	}//func
	
	$(function(){
		var doc_type_question=$("#doc_type_question").val();
		initFormQuestion();
		//resSearch("","");
		//grip question
		
		$("#start_date").datepicker({dateFormat: 'dd/mm/yy',altField: '#sel_start_date_alternate',altFormat:'yy-mm-dd'});
		$("#end_date").datepicker({dateFormat: 'dd/mm/yy',altField: '#sel_end_date_alternate',altFormat:'yy-mm-dd'});

		$("#btn_cancel").click(function(){
			initFormQuestion();
		});

		$('#bws_doc_start').click(function(e){
			e.preventDefault();
			if($('#start_date').val()==''){
				 jAlert('Please Choose Start Date.','Alert',function(){
					 $('#start_date').focus();
	    				 return false;
		    	});			    
				 return false;
			}else			
			if($('#end_date').val()==''){
				 jAlert('Please Choose End Date. ','Alert',function(){
					 $('#end_date').focus();
	    				 return false;
		    	 });			    
				 return false;
			}
			//alert(doc_type_question);
			var opts_dlgDoc={
					autoOpen:false,
					width:'75%',
					height:'550',
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"Choose Doc no.",
					open:function(){			
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
		    			    										"font-size":"27px","color":"#000","padding":"0 0 0 0"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	    			    // button style		
	    			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
						$("*:focus").blur();
						$("#dlgDocNoQst").html('');
						$("#ajaxBusy").show();						
						var opts_listdoc={
								type:'post',
								url:'/sales/accessory/listdocno',
								cache:false,
								data:{
									doc_tp_cancel:doc_type_question,
									start_date:$('#sel_start_date_alternate').val(),
									end_date:$('#sel_end_date_alternate').val(),
									actions:'qustion_doc',
									now:Math.random()
								},
								success:function(data){
									$("#dlgDocNoQst").html(data);
									if($('p#item_not_found').length != 0){
										return false;
									}
									$("#ajaxBusy").hide();
									 var oTable = $('#datatables_listdocno').dataTable();					       			
									$('#datatables_listdocno').dataTable({
										"bDestroy": true,
						       			"fnDrawCallback": function(){
						       												       	
							       		      $('table#datatables_listdocno td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
							       		      $('table#datatables_listdocno td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
							       		      
								       		   $('#datatables_listdocno tr').each(function(){
									       			$(this).click(function(){
									       				var strJson=$(this).attr('idd');
									       				if(strJson!=''){
											       				var seldocno=$.parseJSON(strJson);
																   $('#doc_no_start').val(seldocno.doc_no);
																   var receive_code=seldocno.doc_date;
																   var strArray = receive_code.split("-")
																   var rec_month=strArray[1];
																   var rec_date=strArray[2];
																   var res_rec=((rec_date*rec_month)*9)/7;
																   res_rec = parseInt(res_rec);
																   $('#receive_code').val(res_rec);
															    $("#dlgDocNoQst").dialog("close");
											       			}
									       			});
									       		});
							       		      
						       			}//end callback
									});	
									 oTable.fnSort([[0,'desc'],[1,'desc']]);
									
									
								}//end success 
						};
						$.ajax(opts_listdoc);
						
					},buttons: {							
						"?????????":function(){ 
							$(this).dialog("close");
							return false;
						}
				  },
					close:function(){
						$('.tableNavDocStatus ul').navigate('destroy');
					}
			};
			
			$("#dlgDocNoQst").dialog("destroy");
			$("#dlgDocNoQst").dialog(opts_dlgDoc);			
			$("#dlgDocNoQst").dialog("open");
		});//bws_doc_start

		$('#bws_doc_stop').click(function(e){
			e.preventDefault();
			if($('#start_date').val()==''){
				 jAlert('Please Choose Start Date.','Alert',function(){
					 $('#start_date').focus();
	    				 return false;
		    	 });
				 return false;
			}else			
			if($('#end_date').val()==''){
				 jAlert('Please Choose End Date. ','Alert',function(){
					 $('#end_date').focus();
	    				 return false;
		    	});
				return false;
			}
			var opts_dlgDoc={
					autoOpen:false,
					width:'75%',
					height:'550',
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"Choose Doc no.",
					open:function(){				
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
		    			    										"font-size":"27px","color":"#000","padding":"0 0 0 0"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	    			    // button style		
	    			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
	    			    $("*:focus").blur();					
						$("#dlgDocNoQst").html("");
						
						$("#ajaxBusy").show();
						var opts_listdoc={
								type:'post',
								url:'/sales/accessory/listdocno',
								cache:false,
								data:{
									doc_tp_cancel:doc_type_question,
								    start_date:$('#sel_start_date_alternate').val(),
									end_date:$('#sel_end_date_alternate').val(),
									actions:'brows_docstatus',
									now:Math.random()
								},
								success:function(data){
									$("#ajaxBusy").hide();
									$("#dlgDocNoQst").html(data);
									if($('p#item_not_found').length != 0){
										return false;
									}
									var oTable2 = $('#datatables_listdocno').dataTable();
					       			  //Sort immediately with columns 0 and 1
					       			  //oTable2.fnSort([[0,'desc'],[1,'asc']]);
					       			  
									$('#datatables_listdocno').dataTable({
										"bDestroy": true,
						       			"fnDrawCallback": function(){
						       				
							       		      $('table#datatables_listdocno td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
							       		      $('table#datatables_listdocno td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
							       		      
								       		   $('#datatables_listdocno tr').each(function(){
									       			$(this).click(function(){
									       				var strJson=$(this).attr('idd');
									       				if(strJson!=''){
																   var seldocno=$.parseJSON(strJson);
																   $('#doc_no_stop').val(seldocno.doc_no);
															    $("#dlgDocNoQst").dialog("close");
											       			}
									       			});
									       		});
							       		      
						       			}//end callback
									
									});	
									 oTable2.fnSort([[0,'desc'],[1,'desc']]);
									
								}//end success 
						};
						$.ajax(opts_listdoc);
						
					},buttons: {							
						"?????????":function(){ 
						$(this).dialog("close");
						return false;
					}
			 	 },
					close:function(){
						$('.tableNavDocStatus ul').navigate('destroy');
					}
			};
						
			$("#dlgDocNoQst").dialog("destroy");
			$("#dlgDocNoQst").dialog(opts_dlgDoc);			
			$("#dlgDocNoQst").dialog("open");
		});//bws_doc_start

		$('#upload_doc_search').click(function(e){
			e.preventDefault();
			if($('#start_date').val()==''){
				 jAlert('Please Choose Start Date.','Alert',function(){
					 $('#start_date').focus();
	    				 return false;
		    	});			    
				 return false;
			}else			
			if($('#end_date').val()==''){
				 jAlert('Please Choose End Date. ','Alert',function(){
					 $('#end_date').focus();
	    				 return false;
		    	 });			    
				 return false;
			}
			//alert(doc_type_question);
			var opts_dlgDoc={
					autoOpen:false,
					width:'75%',
					height:'550',
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"Choose Doc no.",
					open:function(){			
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
		    			    										"font-size":"27px","color":"#000","padding":"0 0 0 0"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	    			    // button style		
	    			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
						$("*:focus").blur();
						$("#dlgDocNoQst").html('');
						$("#ajaxBusy").show();						
						var opts_listdoc={
								type:'post',
								url:'/sales/accessory/listdocnowh',
								cache:false,
								data:{
									doc_tp_cancel:doc_type_question,
									start_date:$('#sel_start_date_alternate').val(),
									end_date:$('#sel_end_date_alternate').val(),
									actions:'qustion_doc_upload',
									now:Math.random()
								},
								success:function(data){
									$("#dlgDocNoQst").html(data);
									if($('p#item_not_found').length != 0){
										return false;
									}
									$("#ajaxBusy").hide();
									 var oTable = $('#datatables_listdocno').dataTable();					       			
									$('#datatables_listdocno').dataTable({
										"bDestroy": true,
						       			"fnDrawCallback": function(){
						       												       	
							       		      $('table#datatables_listdocno td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
							       		      $('table#datatables_listdocno td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
							       		      
								       		   $('#datatables_listdocno tr').each(function(){
									       			$(this).click(function(){
									       				var strJson=$(this).attr('idd');
									       				if(strJson!=''){
											       				var seldocno=$.parseJSON(strJson);
																   $('#doc_no_start').val(seldocno.doc_no);
																   var receive_code=seldocno.doc_date;
																   var strArray = receive_code.split("-")
																   var rec_month=strArray[1];
																   var rec_date=strArray[2];
																   var res_rec=((rec_date*rec_month)*9)/7;
																   res_rec = parseInt(res_rec);
																   $('#receive_code').val(res_rec);
															    $("#dlgDocNoQst").dialog("close");
											       			}
									       			});
									       		});
							       		      
						       			}//end callback
									});	
									 oTable.fnSort([[0,'desc'],[1,'desc']]);
									
									
								}//end success 
						};
						$.ajax(opts_listdoc);
						
					},buttons: {							
						"?????????":function(){ 
							$(this).dialog("close");
							return false;
						}
				  },
					close:function(){
						$('.tableNavDocStatus ul').navigate('destroy');
					}
			};
			
			$("#dlgDocNoQst").dialog("destroy");
			$("#dlgDocNoQst").dialog(opts_dlgDoc);			
			$("#dlgDocNoQst").dialog("open");
		});//search invoice for uplaod page

		$('#dest_shop').click(function(e){
			e.preventDefault();
			if($('#start_date').val()==''){
				 jAlert('Please Choose Start Date.','Alert',function(){
					 $('#start_date').focus();
	    				 return false;
		    	 });
				 return false;
			}else			
			if($('#end_date').val()==''){
				 jAlert('Please Choose End Date. ','Alert',function(){
					 $('#end_date').focus();
	    				 return false;
		    	});
				return false;
			}
			var opts_dlgDoc={
					autoOpen:false,
					width:'75%',
					height:'550',
					modal:true,
					resizeable:true,
					position:'center',
					showOpt: {direction:'up'},		
					closeOnEscape:true,	
					title:"Choose Doc no.",
					open:function(){				
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget')
				            .find('.ui-dialog-titlebar')
				            .removeClass('ui-corner-all')
				            .addClass('ui-corner-top');
	    			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#ebebeb",
		    			    										"font-size":"27px","color":"#000","padding":"0 0 0 0"});
	    			    $(this).dialog("widget").find(".ui-dialog-buttonpane")
	    			                      .css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});
	    			    // button style		
	    			    $(this).dialog("widget").find("button")
		                  .css({"padding":"0 .1em 0 .1em","margin":"0 .1em 0 0"});
	    			    $("*:focus").blur();					
						$("#dlgDocNoQst").html("");
						
						$("#ajaxBusy").show();
						var opts_listdoc={
								type:'post',
								url:'/sales/accessory/listdestshop',
								cache:false,
								data:{
									doc_tp_cancel:doc_type_question,
								    // start_date:$('#sel_start_date_alternate').val(),
									// end_date:$('#sel_end_date_alternate').val(),
									actions:'brows_docstatus',
									now:Math.random()
								},
								success:function(data){
									$("#ajaxBusy").hide();
									$("#dlgDocNoQst").html(data);
									if($('p#item_not_found').length != 0){
										return false;
									}
									var oTable2 = $('#datatables_listdestshop').dataTable();
					       			  //Sort immediately with columns 0 and 1
					       			  //oTable2.fnSort([[0,'desc'],[1,'asc']]);
					       			  
									$('#datatables_listdestshop').dataTable({
										"bDestroy": true,
						       			"fnDrawCallback": function(){
						       				
							       		      $('table#datatables_listdestshop td').bind('mouseenter', function () { $(this).parent().children().each(function(){$(this).addClass('datatablerowhighlight');}); });
							       		      $('table#datatables_listdestshop td').bind('mouseleave', function () { $(this).parent().children().each(function(){$(this).removeClass('datatablerowhighlight');}); });
							       		      
								       		   $('#datatables_listdestshop tr').each(function(){
									       			$(this).click(function(){
									       				var strJson=$(this).attr('idd');
									       				if(strJson!=''){
											       				var seldestshop=$.parseJSON(strJson);
																$('#doc_no_stop').val(seldestshop.branch_name);
																$('#dest_branch').val(seldestshop.branch_id);
																$('#dest_ip').val(seldestshop.com_ip);
																$("#dlgDocNoQst").dialog("close");
											       			}
									       			});
									       		});
							       		      
						       			}//end callback
									
									});	
									 oTable2.fnSort([[0,'desc'],[1,'desc']]);
									
								}//end success 
						};
						$.ajax(opts_listdoc);
						
					},buttons: {							
						"?????????":function(){ 
						$(this).dialog("close");
						return false;
					}
			 	 },
					close:function(){
						$('.tableNavDocStatus ul').navigate('destroy');
					}
			};
						
			$("#dlgDocNoQst").dialog("destroy");
			$("#dlgDocNoQst").dialog(opts_dlgDoc);			
			$("#dlgDocNoQst").dialog("open");
		});//dest_shop


		$("#doc_no_start").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13) {
	        	evt.preventDefault();
	        	var $doc_no=$("#doc_no_start");
	        	var $doc_no_val=$.trim($doc_no.val());
	        	if($doc_no_val.length==0){
	        		 jAlert('Please Choose Document no.','Alert',function(){
			    		 $doc_no.focus();
	    				 return false;
		    		 });			    
		        }else{
		        	var check_docno={
			     		        type:'post',
			     		        url:'/sales/accessory/checkdocno',
			     		        data:{
			     	        		doc_no:$doc_no_val,
			     	        		flg_cancel:'N',
			     	        		rnd:Math.random()
			     	        	},
			     		        success:function(data){
			     			        if(data=='Y'){
			     			        	$("#doc_no_stop").focus();
			     				     }else{
			     				    	 jAlert('Not found this doc no, Please try again.','Alert',function(){
				     				    	$doc_no.focus();
			     		    				 return false;
			     			    		 });			    		
			     					 }
			     			    }
			     		};
			     		$.ajax(check_docno);
		        }
		        return false;
	        }
		});

		$("#doc_no_stop").keypress(function(evt){
			var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
	        if(key == 13) {
	        	evt.preventDefault();
	        	var $doc_no=$("#doc_no_stop");
	        	var $doc_no_val=$.trim($doc_no.val());
	        	if($doc_no_val.length==0){
	        		 jAlert('Please Choose Doc no.','Alert',function(){
			    		 $doc_no.focus();
	    				 return false;
		    		 });			    
		        }else{
		        	var check_docno={
			     		        type:'post',
			     		        url:'/sales/accessory/checkdocno',
			     		        data:{
			     	        		doc_no:$doc_no_val,
			     	        		flg_cancel:'N',
			     	        		rnd:Math.random()
			     	        	},
			     		        success:function(data){
			     			        if(data=='Y'){
			     			        	$("#btn_commit").trigger('click');
			     				     }else{
			     				    	 jAlert('Not found doc no, Please try again','Alert',function(){
				     				    	$doc_no.focus();
			     		    				 return false;
			     			    		 });			    		
			     					 }
			     			    }
			     		};
			     		$.ajax(check_docno);
		        }
		        return false;
	        }
		});
		
		$("#btn_commit").click(function(){
			var $doc_no_start=$("#doc_no_start");
			var $doc_no_stop=$("#doc_no_stop");
			var $doc_no_start_val=$.trim($doc_no_start.val());
			var $doc_no_stop_val=$.trim($doc_no_stop.val());
			if($doc_no_start_val.length==0 && $doc_no_stop_val.length==0){
				 jAlert('Please Choose Doc no.','Alert',function(){
					 $doc_no_start.focus();
	    				 return false;
	    		 });		
				 return false;
			}
			resSearch($doc_no_start_val,$doc_no_stop_val);
		});


		//?????????????????????????????????????????????????????? DB ????????????
		$("#transfer_commit").click(function(){
			var $doc_no_start=$("#doc_no_start");
			var $doc_no=$.trim($doc_no_start.val());
			var $dest_branch_val=$("#dest_branch");
			var $dest_branch=$.trim($dest_branch_val.val());
			var $dest_ip_val=$("#dest_ip");
			var $dest_ip=$.trim($dest_ip_val.val());
			if($doc_no.length==0){
				 jAlert('Please Choose Doc no.','Alert',function(){
					 $doc_no_start.focus();
	    				 return false;
	    		 });		
				 return false;
			}

			// $.ajax({
			// 	type:'post',
			// 	url:'/sales/accessory/listdestshop',
			// 	cache:false,
			// 	data:{
			// 		doc_no:doc_no_start_val,
			// 		destination_branch:dest_branch,
			// 		destination_ip:dest_ip,
			// 		now:Math.random()
			// 	},
			// 	success:function(data){
			// 		$("#dlgQstDetail").html('');
			// 		$("#dlgQstDetail").html(data);
			// 		$(this).parent().find('select, input, textarea').blur();
			// 	}//end success function
			// });//end ajax pos

			// jAlert('???????????????????????????????????????????????????????????????'+$dest_branch+ $dest_ip,'Alert',function(){
			// 	$doc_no_start.focus();
			// 		return false;
			// });		

			var check_docno={
			     		        type:'post',
			     		        url:"/sales/accessory/trninv2shop",
			     		        data:{
									doc_no:$doc_no,
									dest_branch:$dest_branch,
									dest_ip:$dest_ip,
			     	        		rnd:Math.random()
			     	        	},
			     		        success:function(data){
			     			        if(data=='Y'){
										jAlert('Success','Alert',function(){
											 return false;});
			     				     }else{
			     				    	 jAlert('Failed','Alert',function(){
			     		    				 return false;
			     			    		 });			    		
			     					 }
			     			    }
			     		};
			     		$.ajax(check_docno);


		});

		//????????????????????????????????????DB ????????????????????????????????????DB????????????
		$("#download_commit").click(function(){
			var $doc_no_start=$("#doc_no_start");
			var $doc_no=$.trim($doc_no_start.val());
			var $dest_branch_val=$("#dest_branch");
			var $dest_branch=$.trim($dest_branch_val.val());
			var $dest_ip_val=$("#dest_ip");
			var $dest_ip=$.trim($dest_ip_val.val());
			if($doc_no.length==0){
				 jAlert('Please Choose Doc no.','Alert',function(){
					 $doc_no_start.focus();
	    				 return false;
	    		 });		
				 return false;
			}

			var check_docno={
			     		        type:'post',
			     		        url:"/sales/accessory/downloadinvfromwh",
			     		        data:{
									doc_no:$doc_no,
									dest_branch:$dest_branch,
									dest_ip:$dest_ip,
			     	        		rnd:Math.random()
			     	        	},
			     		        success:function(data){
			     			        if(data=='Y'){
										jAlert('Success','Alert',function(){
											 return false;});
			     				     }else{
			     				    	 jAlert('Failed','Alert',function(){
			     		    				 return false;
			     			    		 });			    		
			     					 }
			     			    }
			     		};
			     		$.ajax(check_docno);


		});



	});//ready