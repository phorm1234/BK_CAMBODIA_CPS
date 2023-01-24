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
	
	function allocateStock(user_action){
		/**
		 * @desc update product allocate local and server
		 * @param null
		 * @create 20042017
		 */
		 $.ajax({	        
		    	url: '/sales/ecommerce/allocatestock',
		    	type:'post',
		        data: {
		        	user_action:user_action,
		        	rnd:Math.random(),
		        	delay:20
		        },
		        
		        beforeSend: function(){
		           $("#loading").dialog('open').empty().html("<p id='img' align='center'><img src='/sales/img/activity_indicators_01.gif' /></p>");
		        },
		        success: function(data) {
		            if(data=='Y'){
		            	$("#loading").dialog('close');
		            }
		            
		            
		         }
		    });
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
	} //func
	
	
	
	function flgCommand(com,grid){
		if(com=='Refresh'){
			$("#tbl_order").flexOptions({newp:1}).flexReload();
		}//if refresh
		
		
	}//func
	
	
	
	function getOrderPicking(){	
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
				$("#tbl_order").flexOptions({newp:data}).flexReload();
				getSubTotalOrderPicking();
				return false;
			}
		});
	}//func

	function getSubTotalOrderPicking(){
		/**
		*@name getSubTotal
		*@desc 
		*/
	
		var doc_no_ref=jQuery.trim($("#doc_no").val());
		$.getJSON(
				"/sales/ecommerce/subtotalorderpicking",
				{
					doc_no_ref:doc_no_ref,
					actions:'getSumOrderPicking'
				},
				
				function(data){
						//alert(data.orderhead);
						$.each(data.orderhead, function(i,m){
							
							if(m.exist=='yes' && m.sum_amount!='' && parseInt(m.sum_amount)!='0'){
								var sum_quantity=parseInt(m.sum_quantity);								
								var sum_amount=parseFloat(m.sum_amount);								
								sum_amount=sum_amount.toFixed(2);
								var sum_net_amt=parseFloat(m.sum_net_amt);
								sum_net_amt=sum_net_amt.toFixed(2);
								$('#sum_quantity').val(sum_quantity);
								$('#sum_amount').val(addCommas(sum_net_amt));
								
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

		
	
	function ajaxCallOrder(user_action) {
		/**
		 * @create 10022017
		 */
		
	    $.ajax({	        
	    	url: '/sales/ecommerce/getneworderpicking',
	    	type:'post',
	        data: {
	        	user_action:user_action,
	        	rnd:Math.random(),
	        	delay:7
	        },
	        
	        beforeSend: function(){
	           $("#loading").dialog('open').empty().html("<p id='img' align='center'><img src='/sales/img/activity_indicators_01.gif' /></p>");
	        },
	        success: function(data) {
	            if(data=='Y'){
	            	setTimeout(function(){getOrderPicking();},800);
	            }
	            
	            
	         }
	    });
	}//func
	
	function printBill(doc_no,user_action){		
		/**
		*@desc
		*@create 16022017
		*@return null
		*/
		var url2print="/sales/ecommerce/printorderpicking";
		var opts={
			type:'get',
			url:url2print,
			cache:false,
			data:{
					doc_no:doc_no,					
					actions:'print',
					rnd:Math.random()
				},
			success:function(){
				$.ajax({
  					 type:'post',
  					 url:'/sales/ecommerce/sendorderstatus',
  					 data:{
  						 order_no:doc_no,
  						 order_status:'B',
  						 user_action:user_action,
  						 rnd:Math.random()
  					 },success:function(){}
  				 });
				return false;
			}
		};
		$.ajax(opts);
		
	}//func
	
	function printDocFromGrid(doc_to_print,doc_list_show,user_action){
			/**
			*@desc
			*@create 16022017
			*@return null
			*/

			 jConfirm('รายการใบจัดสินค้าที่ต้องการพิมพ์\r\n ' + doc_list_show,'ยืนยัน', function(r){
			        if(r){
			        	
			        	var arr_doc_no=doc_to_print.split('#');			        	
			        	$.each(arr_doc_no , function(i, val) { 		
			        		
			        		if(arr_doc_no[i]!=''){		
			        			setTimeout(function(){
			        				printBill(arr_doc_no[i],user_action);
			        			},400);
			        			
			        		}			        		  
		        		  
		        		});
			        	
			        	return false;

			        }
				}
			);
		
	}//func
	
	
	
	function checkSaleMan(doc_to_print,doc_list_show,act){
		 //-------------- CHECK SALEMAN------------------							    	
		var Dialog_Sales = $('<div id="dlgSaleMan">\
	            <p><input type="text" size="25" id="chk_saleman_id" class="input-text-pos ui-corner-all keybarcode"></p>\</div>');
	        Dialog_Sales.dialog({								        	
	            modal: true,
	            title: "กรุณาระบุรหัสพนักงาน",
	            width:275,
	            height:'auto',
	            closeOnEscape:false,
	            open:function(){	            	
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
							            	
							            	///////////////START//////////////
							            	if(act=='getorder'){
							            		ajaxCallOrder(arr_data[0]);
							            	}else if(act=='print'){
							            		printDocFromGrid(doc_to_print,doc_list_show,arr_data[0]);	
							            	}else if(act=='allocate'){
							            		allocateStock(arr_data[0]);
							            	}
							            	///////////////START//////////////
							            	
							            }						            	
						            }
						    };	
							$.ajax(opts);
				            return false;
				        }
					});		
												            	
	            },close:function(evt){
	            	$("#dlgSaleMan").remove();	
	            	$("#dlgSaleMan").dialog("destroy");	            		
	            }
	        });
		//------------- CHECK SALEMAN-----------------
	}//func
	
	$(function(){
		
		$("#loading").dialog({
			autoOpen:false,
			width:'280',
			height:'auto',
			modal:true,
			resizeable:false,
			position:'center',
			showOpt: {direction:'up'},		
			closeOnEscape:false,	
			title:"Please Wait...",
		    hide: 'slide',
			show: 'slide',
			open:function(event,ui){
				$(this).parent().children().children('.ui-dialog-titlebar-close').hide();		
	        	 $(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget')
			            .find('.ui-dialog-titlebar')
			            .removeClass('ui-corner-all')
			            .addClass('ui-corner-top');				
			     $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#F4F4F4",
				    																			"font-size":"26px","color":"#0000FF",
				    																			"padding":"0 0 0 0"});
			     
			     $("body").css({
			         overflow: 'hidden'
			     });
			     $(".ui-widget-overlay").css({
			         background:"rgb(0, 0, 0)",
			         opacity: ".50 !important",
			         filter: "Alpha(Opacity=50)",
			     });
			     
			     setTimeout(function(){
			    	 $("#loading").dialog('close');
			    	 //$("#loading").dialog('destroy');
			     },15000);
				
			},
			
			beforeClose: function(event, ui) {
			    $("body").css({ overflow: 'inherit' })
			},
			close:function(){
				
			}
			
		});
		
		var gHeight=300,wcol=250,gWidth = parseInt((screen.width*70)/100)-600;
		if ((screen.width>=1280) && (screen.height>=1024)){			
			gHeight=(screen.height-(screen.height*(75/100))-5);
			wcol=410;
		}
		$("#tbl_order").flexigrid(
				{
					//cntemp
					url:'/sales/ecommerce/getorderpicking',
					dataType: 'json',
					colModel : [
						{display: '#', name : 'id', width :20, sortable : true, align: 'center'},
						{display: 'เลขที่เอกสาร', name : 'doc_no', width : 130, sortable : true, align: 'center'},
						
						{display: 'วันที่ Order', name : 'doc_date',width :80, sortable : false, align:'center'},
						{display: 'วันที่มารับ', name : 'doc_date',width :80, sortable : false, align:'center'},
						{display: 'เวลาที่มารับ', name : 'doc_date',width :80, sortable : false, align:'center'},
						
						{display: 'ลูกค้า', name : 'member_id',width :250, sortable : false, align:'center'},
						
						{display: 'จำนวน', name : 'quantity', width : 50, sortable : false, align: 'center'},
						{display: 'ยอดสุทธิ', name : 'net_amt', width :80, sortable : false, align: 'center'},
						
						{display: 'สถานะ', name : 'order_status_show', width :80, sortable : false, align: 'center'},						
						],
					sortname: "post_date",
					sortorder: "ASC",
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
						getSubTotalOrderPicking();
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
		
		 $('#btn_printpicking').live('click', {}, function(ev) {
			    
		    	var grid = $('#tbl_order').flexigrid();
				var doc_to_print='',doc_list_show='';				
		        grid.find('tr.trSelected').each(function() {		        	
			        doc_no=$("td div", $(this)).eq(1).text();			        
		        	doc_date=$("td div", $(this)).eq(2).text();
		        	id=$(this).attr('id').substr(3);
		        	doc_list_show += doc_no + "/" + doc_date  + "\r\n";
		        	doc_to_print+= doc_no + "#";
		        }); 		            
		       doc_to_print=doc_to_print.substring(0,doc_to_print.length-1);		       
		       if(doc_to_print==''){
		        	 jAlert('กรุณาเลือกรายการที่ต้องการพิมพ์','ข้อความแจ้งเตือน',function(){		        		
						 return false;
					  });
		         return false;
			   }else{
			       setTimeout(function(){
			    	   //printDocFromGrid(doc_to_print,doc_list_show);		
			    	   checkSaleMan(doc_to_print,doc_list_show,'print');
				    },400);
				   
			   }
		       return false;
		});	   

		$("#btn_getorder").click(function(evt){
			evt.preventDefault();
			checkSaleMan('','','getorder');
			//ajaxCallOrder();
			return false;
			
		});
		
		
		$("#btn_allocate").click(function(evt){
			evt.preventDefault();
			checkSaleMan('','','allocate');			
			return false;
			
		});
		
		
	});//ready