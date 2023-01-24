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
		        	evt.preventDefault();
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
	//AREA LOCK KEY BARCODE OR KEY MANUAL	
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
	
	function initTblRwd(){
		var opts_ini={
				type:'post',
				url:'/sales/member/initblrwd',
				data:{
					rnd:Math.random()
				},
				success:function(){}
		};
		$.ajax(opts_ini);
	}//func
	
	function initForm(){
		//GET LOCK STATUS
		getLockStatus();
		$("#tblRwdReceive").html('');
		
		$("#rwd_member_fullname").removeAttr('readonly');
 		$("#rwd_member_no").removeAttr('readonly');

		$("#rwd_member_fullname").removeClass('input-text-pos-disabled').addClass('input-text-pos');
 		$("#rwd_member_no").removeClass('input-text-pos-disabled').addClass('input-text-pos');
		
 		$("#rwd_member_fullname").val('');
		$("#rwd_member_no").val('').focus();
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
								
									var address=$.trim(m.address)+" "+
									$.trim(m.sub_district)+" "+
									$.trim(m.district)+" "+
									$.trim(m.province_name)+" "+
									$.trim(m.zip)+" "+
									$.trim(m.mobile_no);		
									
									$('#rwd_member_fullname').val(member_fullname);									
									getRwdReceiveData(member_no);
																	
								}
												
							});
							
						
						}
						
					}				
			);//end json
			
		}//end if	
		return false;		
	}//func
	
	function getRwdReceiveData(member_no){
		/**
		 * @desc
		 * @return
		 */
		var opts_receiveData={
				type:'post',
				url:'/sales/member/receivereward',
				data:{
					member_no:member_no,
					rnd:Math.random()
				},
				success:function(data){
					$('#rwd_member_no').attr('readonly',true);
             		$('#rwd_member_no').removeClass('input-text-pos').addClass('input-text-pos-disabled');
             		$('#rwd_member_fullname').attr('readonly',true);
             		$('#rwd_member_fullname').removeClass('input-text-pos').addClass('input-text-pos-disabled');
             		
             		
					$("#tblRwdReceive").html(data);
					$('form#FrmReceiveReward input:text:enabled:first').focus();										
					var ai=0;
					var n = $(".listrwd").length;
					$('.listrwd').each(function(){						
						$(this).keypress(function(evt){
							var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 
					        if(key == 13){
					        	evt.preventDefault();
					        	var id_curr=$(this).attr('id');
					        	var txt_balance=$(this).attr('txt_balance');
					        	var txt_receive=this.value;
					        	txt_balance=parseInt(txt_balance);
					        	txt_receive=parseInt(txt_receive);
					        	
					        	if(txt_receive>txt_balance){
					        		jAlert('จำนวนที่รับมากกว่าสินค้าคงเหลือ', 'ข้อความแจ้งเตือน',function(){
					        			$("input[id='"+id_curr+"']").focus();
					        			return false;
									});						        		
					        		return false;
					        	}
					        	
					        	 var nextIndex = $('input[class="listrwd"]').index(this) + 1;
							      if(nextIndex < n)
							        $('input[class="listrwd"]')[nextIndex].focus();
							      else
							      {
							        $('input[class="listrwd"]')[nextIndex-1].blur();
							        $('#btnSave').focus();
							      }
					        	
					            return false;
					        }
						});
						ai++;
					});
					
					$("#btnCancel").click(function(e){
						e.preventDefault();
						jConfirm('คุณต้องการยกเลิกรายการแลกคะแนนใช่หรือไม่?', 'ยืนยันการยกเลิกบิล', function(r) {
					        if(r){
					        	initForm();
					        }
						});
					});

					$("#btnSave").click(function(e){
						e.preventDefault();	
						var st='0';
						var arr_rwd=new Array();
						$(".listrwd").each(function(){
							var idd=$(this).attr('id');
							var qty_balance=$(this).attr('txt_balance');
							qty_balance=parseInt(qty_balance);
				        	var qty_receive=parseInt(this.value);
				        	
				        	//alert(qty_receive+" "+qty_balance);
				        	if(isNaN(qty_balance)){
				        		qty_balance=0;
				        	}
				        	
				        	if(isNaN(qty_receive)){
				        		qty_receive=0;
				        	}
				        	
				        	//alert(qty_receive+" > "+qty_balance);
				        	
				        	if(qty_receive>qty_balance){
				        		st='1';
				        		jAlert('จำนวนที่รับมากกว่าสินค้าคงเหลือ', 'ข้อความแจ้งเตือน',function(){
				        			$("input[id='"+idd+"']").focus();
				        			return false;
								});						        		
				        		return false;
				        	}else if(qty_receive!=0){
								if($.trim(this.value)!=''){
									arr_rwd.push(idd+'@'+this.value);
								}
							
				        	}
							
						});
					
						if(st=='1'){
							return false;
						}
					
						
						var str_rwd=arr_rwd.join('&');
						//check key input receive
						var chk_n=0;
						$(".listrwd").each(function(){
							if(this.value!=''){
								chk_n=1;
								return false;
							}
						});
						var opts_save={
								type:'post',
								url:'/sales/member/savereceivereward',
								data:{
									member_no:member_no,
									items:str_rwd,
									rnd:Math.random()
								},success:function(data){
									var arr_data=data.split('#');
									if(arr_data[0]=='1'){
										printBill(data);
										jAlert('รับสินค้าสมบูรณ์', 'ผลการบันทึก',function(){
											$("#tblRwdReceive").html('');
											initForm();
						        			return false;
										});					
									}
								}
								
						};
						
						var chkStockReceive={
								type:'post',
								url:'/sales/member/chekreceivestock',
								data:{
									items:str_rwd,
									rnd:Math.random()
								},success:function(data){
									if(data==''){
										$.ajax(opts_save);
									}else{
										jAlert('รายการสินค้า '+data, 'ข้อความแจ้งเตือน',function(){
						        			return false;
										});				
									}
								}
						};
						
						if(chk_n==1 && str_rwd!=''){
							$.ajax(chkStockReceive);
						}else{
							jAlert('กรุณาระบุจำนวนสินค้าที่รับ', 'ข้อความแจ้งเตือน',function(){
								$('form#FrmReceiveReward input:text:enabled:first').focus();
			        			return false;
							});
						}
						return false;						
					});
					
				}//success
		};
		$.ajax(opts_receiveData);
	}//func
	
	$(function(){
		initTblRwd();
		initForm();				
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
	});//ready