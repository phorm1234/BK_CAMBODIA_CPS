function check_number(ch){
		var len, digit;
		if(ch == " "){ 
			return false;
			len=0;
		}else{
			len = ch.length;
		}

			for(var i=0 ; i<len ; i++)
			{
				digit = ch.charAt(i)
				if(digit >="0" && digit <="9" || digit==","){
				; 
				}else{
				return false; 
				} 

				if (   isNaN(ch)   ) {
					return false; 
				} else {
					;
				}
			} 

		return true;
}


function check_name(ch){
	var len, digit;
	if(ch.length==0 || ch==" "){ 
		return false;
		len=0;
	}else{
		len = ch.length;
	}

		for(var i=0 ; i<len ; i++)
		{
				digit = ch.charAt(i)
				if(digit=="0"  || digit=="1" || digit=="2" || digit=="3" || digit=="4" || digit=="5" || digit=="6" || digit=="7" || digit=="8" || digit=="9"  ){
					return false; 
				}
				
				if(digit=="." || digit=="," || digit=="/" || digit=="*" || digit=="-" || digit=="+"  || digit=="_" || digit=="-" || digit=="!" || digit=="@" || digit=="#" || digit=="%" || digit=="^" || digit=="&"  || digit=="="){
					return false; 
				}	
				
				if(digit=="๐"  || digit=="๑" || digit=="๒" || digit=="๓" || digit=="๓" || digit=="๔" || digit=="๕" || digit=="๖" || digit=="๗" || digit=="๘"   || digit=="๙"  || digit=="|"  ){
					return false; 
				}	
				
				
				if(digit=="}"  || digit=="{" || digit=="[" || digit=="]" || digit=="'" || digit=='"' || digit==";" || digit=="," ){
					return false; 
				}	
																	
				
				

		} 

	return true;
}
function check_surname(ch){
	var len, digit;
	if(ch.length==0 || ch==" "){ 
		return false;
		len=0;
	}else{
		len = ch.length;
	}

		for(var i=0 ; i<len ; i++)
		{
				digit = ch.charAt(i)
				if(digit=="0"  || digit=="1" || digit=="2" || digit=="3" || digit=="4" || digit=="5" || digit=="6" || digit=="7" || digit=="8" || digit=="9"  ){
					return false; 
				}
				
				if(digit=="." || digit=="," || digit=="/" || digit=="*" || digit=="-" || digit=="+"  || digit=="_" || digit=="-" || digit=="!" || digit=="@" || digit=="#" || digit=="%" || digit=="^" || digit=="&" || digit=="(" || digit==")" || digit=="="){
					return false; 
				}	
				
				if(digit=="๐"  || digit=="๑" || digit=="๒" || digit=="๓" || digit=="๓" || digit=="๔" || digit=="๕" || digit=="๖" || digit=="๗" || digit=="๘"   || digit=="๙"  || digit=="|"  ){
					return false; 
				}	
				
				
				if(digit=="}"  || digit=="{" || digit=="[" || digit=="]" || digit=="'" || digit=='"' || digit==";" || digit=="," ){
					return false; 
				}	
																	
				
				

		} 

	return true;
}
function check_homename(ch){
	var len, digit;
	if(ch.length==0 || ch==" "){ 
		return false;
		len=0;
	}else{
		len = ch.length;
	}

		for(var i=0 ; i<len ; i++)
		{
				digit = ch.charAt(i)
				
				if(digit=="." || digit=="," ||  digit=="*" || digit=="-" || digit=="+"  || digit=="_" || digit=="-" || digit=="!" || digit=="@" || digit=="#" || digit=="%" || digit=="^" || digit=="&" || digit=="(" || digit==")" || digit=="="){
					return false; 
				}	
				
				if(digit=="๐"  || digit=="๑" || digit=="๒" || digit=="๓" || digit=="๓" || digit=="๔" || digit=="๕" || digit=="๖" || digit=="๗" || digit=="๘"   || digit=="๙"  || digit=="|"  ){
					return false; 
				}	
				
				
				if(digit=="}"  || digit=="{" || digit=="[" || digit=="]" || digit=="'" || digit=='"' || digit==";" || digit=="," ){
					return false; 
				}	
																	
				
				

		} 

	return true;
}




function mappro(){ //search data for edit
	    $.getJSON("/sales/promotion/mappro",{
	    	doc_no: '',
	        ran:Math.random()},function(data){
		        
	        	selectpro(data.doc_noSend);    
		}); 
	}
	

//select promotion
var x;
$(function(){	
	
	//38 up 40 down
	$(".promotion_panel li").live("keydown",function(e){ 
		
			var key = e.which;		
			if(key==38){
					clearFocus();				
					$(this).prev().addClass("active");				 
			}else if(key==40){
					clearFocus();
					$(this).next().addClass("active");
			}else if(key==13){
				var nameFunction=$(this).parents().find("UL").attr("tag");
				$(this).attr('var1',function(index,val){
					

					if(nameFunction=="playpro"){
						var arr=val.split(',');
						playpro(arr[0],arr[1],arr[2],arr[3]);
					}else if(nameFunction=="lastgipro"){
						lastgipro(val);
					}else if(nameFunction=="gipro"){
						gipro(val);
					}
					
				});
				
			}		
	}).live("click",function(){
			clearFocus();
			$(this).addClass("active").find("input").attr("checked",true).focus() ;
			//alert( $(this).text());
	})
	$(".promotion_panel li div").live("click",function(){  //When click promotion
		var nameFunction=$(this).parents().find("UL").attr("tag");
		$(this).attr('var1',function(index,val){

			if(nameFunction=="playpro"){
				var arr=val.split(',');
				playpro(arr[0],arr[1],arr[2],arr[3]);
			}else if(nameFunction=="lastgipro"){
				lastgipro(val);
			}else if(nameFunction=="gipro"){
				
				gipro(val);
			}
			
		});
	})
	function clearFocus(){
			$(".promotion_panel li").each(function(){  $(this).removeClass("active") });
	}
})




function selectpro(doc_no){ 
	    promotionDetail('N');
	    $.get("/sales/promotion/selectpro",{
	    	doc_no: doc_no,
	        ran:Math.random()},function(data){
	        	var chk=data.substring(0,7);
				if (data==12) {//เล่นโปรหมดแล้ว
		        	playnopro(doc_no);//copy สินค้า
		        } else if(chk=="playpro"){
		        	eval(data);
		        } else{
		        	
		        	
		    		$("#listPro").html(data);
		    		$( "#dialog-modal" ).dialog({height: 400,width:'80%',modal: true,resizable:true});
					$("#radio01").focus().attr("checked",true);
		        }
	    }); 
	   	
}	




function detailpro(promo_code,doc_no,product_id,seq,price) {
	promotionDetail('N');
	$("#dialog-modal" ).dialog( "destroy" );
    $.get("/sales/promotion/detailpro",{
	      	    	promo_code: promo_code,
	    	    	doc_no: doc_no,
	    	    	product_id: product_id,
	    	    	seq: seq,
	    	    	price:price,
	    	    	ran:Math.random()},function(data){
		    	    	
	    	    		$("#dialog-promotion").dialog({
	    	    			height: 400,width:'70%',modal: true,resizable:true,closeOnEscape:false,
	    	    			open:function(){
	    	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    	    		},
	    	    			buttons: {
								"ไม่เล่นโปรโมชั่น":function(){ 		
	    	    							set_product_nopro(doc_no,product_id,seq);
											//$(this).dialog("close");
											return false;
								}
							 }
	    	    		});
	    	    		
		    	    	$("#promotion").html(data);
		    	    	$("#input_product").focus();

    }); 
}

function addproductdetail() {
	
	var product_id=$("#input_product").val();
	var quantity=$("#input_quantity").val();
	var promo_code=$("#promo_code_hide").val();
	var price=$("#price_hide").val();
	var seq=$("#seq_hide").val();
	var product_id_first=$("#product_id_first").val();
	var compare_hide=$("#compare_hide").val();
	
	$("#dialog-modal" ).dialog( "destroy" );
	
    $.get("/sales/promotion/addproductdetail",{
	    	    	product_id: product_id,
	    	    	quantity: quantity,
	    	    	compare:compare_hide,
	    	    	promo_code:promo_code,
	    	    	promo_seq:seq,
	    	    	ran:Math.random()},function(data){
		    	    	
		    	    	if(data==1){//add ok
		    	    		playpro(promo_code,'',product_id_first,seq,price);
		    	    		$("#dialog-promotion").dialog( "destroy" );
		    	    	}else if(data=="no_product"){
			    	    	alert("สินค้านี้ไม่มีในระบบ");
		    	    	}else if(data=="stock_short"){
							alert("สินค้านี้ไม่มีสต๊อค");
		    	    	}else if(data=="NoProductInPro"){
							alert("สินค้าที่ยิงเข้ามาไม่อยู่ในโปรโมชั่นนี้");
		    	    	}else if(data=="product_have"){
		    	    		alert("สินค้านี้มีอยู่แล้ว");
		    	    	}else if(data=="price_no"){
							alert("ราคาตัวแถมแพงกว่าตัวหลัก");
		    	    	}else if(data=="product_dup"){
			    	    	alert("ตัวแถมห้ามซ้ำกับตัวหลัก");
		    	    	}    

    }); 
}







function playpro(promo_code,doc_no,product_id,seq,price) {
		
		$("#dialog-modal" ).dialog( "destroy" );
		var msgErr;
		var txtHead="<table align='center'>";
		var headPro="";
		var colum="";
		var txtLast="</table>";
		var genTbl="";
		var txtTbl="";
		var qtyChk;
		var cssTr;
		var cssTd;
		var btnCancle="<br><input type='button' id='btnCancle' name='btnCancle' value='ไม่เล่นโปรโมชั่น' onclick=\"set_product_nopro('"+doc_no+"','"+product_id+"','"+seq+"');\">";
		
	    $.get("/sales/promotion/playpro",{
		      	    	promo_code: promo_code,
		    	    	doc_no: doc_no,
		    	    	product_id: product_id,
		    	    	seq: seq,
		    	    	price:price,
		    	    	ran:Math.random()},function(data){
			    	    	//alert(data);
			    	    	if(data=="N"){	    	    	
			    	    		detailpro(promo_code,doc_no,product_id,seq,price);
			    	    	}else if(data=="Y"){
			    	    		selectpro(doc_no);
			    	    	}
				    	    	
			    	    	
		       	
		       
	    }); 
	}

	function set_product_nopro(doc_no,product_id,seq){ //กำหนดให้สินค้าไม่เล่นโปร
		$("#dialog-promotion" ).dialog( "destroy" );
	    $.get("/sales/promotion/setproductnopro",{
	    	doc_no: doc_no,
	    	product_id: product_id,
	    	seq: seq,
	        ran:Math.random()},function(data){	
	        	selectpro(doc_no);
		}); 
	}

	
	//function canclepro() {
	function playnopro(doc_no){ //copy สินค้า
		$("#dialog-modal" ).dialog( "destroy" );
	    $.get("/sales/promotion/playnopro",{
	    	doc_no: doc_no,
	        ran:Math.random()},function(data){	
	        	
	        	promotionDetail('N');
	        	lastprochk();
		}); 
	}



	/*function promotionDetail(){ //แสดงรายการ
	    $.get("/sales/promotion/showtbl",{
	        ran:Math.random()},function(data){
	        	$("#showPro").html(data);
		}); 
	}*/



	
	//Hot Promotion
	
	
	function hotprosearch() {
	    $.get("/sales/promotion/hotprosearch",{
	    	doc_no: '',
	        ran:Math.random()},function(data){
	        	$("#dialog-hotpro").dialog({height: 400,width:'70%',modal: true,resizable:true});
	        	$("#hotpro").html(data);   
	        	$("#radio01").focus().attr("checked",true);
	        	$("#hotpro_product_search").focus();
		}); 
		
	}
	function hotchkkey(product,quantity){
		$("#dialog-hotpro" ).dialog( "destroy" );
		if(product=="" || quantity=="" || quantity<=0 || !check_number(quantity)){
			alert("ป้อนข้อมูลไม่ถูกต้อง");
 	    	$("#csh_product_id").val("");
	    	$("#csh_quantity").val(1);
	    	$("#csh_product_id").focus();
			return false;
		}
	    $.get("/sales/promotion/hotchkkey",{
	    	doc_no: '',
	    	member_no:$("#csh_member_no").val(),
	    	hotpro_product_search: product,
	    	hotpro_quantity_search: quantity,
	        ran:Math.random()},function(data){
	        	if(data=="Noproduct"){
	        		alert("ไม่พบรหัสสินค้านี้ในระบบ");
	     	    	$("#csh_product_id").val("");
	    	    	$("#csh_quantity").val(1);
	    	    	$("#csh_product_id").focus();
	        		return false;
	        	}else if(data=="Nostock"){
	        		alert("สต๊อคสินค้าไม่พอ");
	     	    	$("#csh_product_id").val("");
	    	    	$("#csh_quantity").val(1);
	    	    	$("#csh_product_id").focus();
	        		return false;
	        	}else if(data=="stockShort"){
	        		alert("Stock ไม่พอ");
	     	    	$("#csh_product_id").val("");
	    	    	$("#csh_quantity").val(1);
	    	    	$("#csh_product_id").focus();
	        		return false;
	        	}else{
	        		hotpro(product,quantity);
	        	}

		}); 
	    
	}
	
	function hotpro(product,quantity){ //search data for edit
		if(product=="" || quantity=="" || quantity<=0 || !check_number(quantity)){
			alert("ป้อนข้อมูลไม่ถูกต้อง");
 	    	$("#csh_product_id").val("");
	    	$("#csh_quantity").val(1);
	    	$("#csh_product_id").focus();
			return false;
		}
		
		
		$("#dialog-hotpro" ).dialog( "destroy" );
	    $.get("/sales/promotion/hotpro",{
	    	doc_no: '',
	    	hotpro_product_search: product,
	    	hotpro_quantity_search: quantity,
	        ran:Math.random()},function(data){
				
	        if(data=="price_null"){
	        		alert("สินค้านี้ไม่มีราคา ห้ามขาย");
	     	    	$("#csh_product_id").val("");
	    	    	$("#csh_quantity").val(1);
	    	    	$("#csh_product_id").focus();
	        		return false;
	        } else if(data.substring(0,5)=="gipro"){
	        		eval(data);
			}else if(data.substring(0,15)=="promotionDetail"){
				eval(data);
				$("#csh_product_id").val("");
				$("#csh_quantity").val(1);
				$("#csh_product_id").focus();
			}else if(data.substring(0,9)=="addhotpro"){
				eval(data);
			}else{
				
				$("#dialog-hotpro").dialog({height: 400,width:'80%',modal: true,resizable:true});	
				
				$("#hotpro").html(data);  
	
				$("#radio01").focus().attr("checked",true);
				$("#hotpro_product_search").focus();
			}
		}); 
	}
	 
	
	var chk_barcode="";
	function openscanbarcode(promo_code,type_coupon){
		
		if(type_coupon=="alert_coupon_only"){
			var msg_coupon="โปรโมชั่นนี้ต้องใช้ร่วมกับคูปอง";
			var c=window.confirm(msg_coupon);
  			if (!c) {
  				return false;
  			}else{
  				chk_barcode="OK";
  				gipro(promo_code); 
  				
  			}
			
		}else if(type_coupon=="alert_from_idcard" && chk_barcode!="OK"){
			//alert(chk_barcode);
			var datastart=$("#csh_member_no").val()+"#"+promo_code+"#"+$("#csh_id_card").val()+"#"+$("#csh_mobile_no").val();
			var member_no=$("#csh_member_no").val();

			if(member_no.substring(0,2)=="ID"){
			  	chk_barcode="OK";
  				gipro(promo_code); 
			}else{
				apireadwalkin(datastart,"proChkDupByIdcard");	
			}

		}else{
			$("#dialog-hotpro" ).dialog( "destroy" );
		    $.get("/sales/promotion/openscanbarcode",{
		    	promo_code: promo_code,
		        ran:Math.random()},function(data){
	  	    		$("#dialog-hotpro").dialog({
		    			height: 400,width:'50%',modal: true,resizable:true,closeOnEscape:true,
		    			open:function(){  	    	            
	  	    			 $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	  	    			 
		    			
		    		},
				    close:function(evt,ui){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							canclestock();
							return false;
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
							canclestock();
							return false;
						} 
					},
	
	
		    		
	 
		    		
		    			buttons: {
						 
							/*"จบโปร":function(){ 		
		    						chkendpro(promo_code);
									return false;
				 			},
					 
					 
	*/
							"ยกเลิก":function(){ 		
		    							canclestock();
										return false;
							}
		    		
		    		
						 }
		    		});
		        	$("#hotpro").html(data);  
		        	$("#scan_barcode").focus();
		        	//*** check lock unlock
					if($("#csh_lock_status").val()=='Y'){
						lockManualKey();
					}else{
						unlockManualKey();
					}
					//*** check lock unlock	
		        }); 
		}//end if
		
	}
	function chkscanbarcode(promo_code){//chk การยิงบาร์โค้ตเข้ามา

		var scan_barcode=$("#scan_barcode").val();
		if(scan_barcode==""){
			alert("กรุณายิงบาร์โค๊ดด้วยค่ะ");
			return false;
		}
	    $.get("/sales/promotion/chkscanbarcode",{
	    	scan_barcode:scan_barcode ,
	    	promo_code: promo_code,
	  
	        ran:Math.random()},function(data){
	        	if(data=="Y"){
	        		gipro(promo_code); 
	        	}else if(data=="play_have"){
	        		alert("เล่นไปแล้วห้ามเล่นอีก");
	        		$("#dialog-hotpro" ).dialog( "destroy" );
	        	}else{
	        		alert("รหัสบาร์โค๊ดไม่ถูกต้อง");
	        		return false;
	        	}

	        	
		}); 
	    
	   
	}
	
	function proChkDupByIdcard(datastart,data){ //search data for edit

		var datastartarr=datastart.split("#");
		member_no=datastartarr[0];
		promo_code=datastartarr[1];

		var arr=data.split("#");
		var id_card=arr[0];
		var fname=arr[1];
		var lname=arr[2];
		var birthday=arr[3];

		chk_barcode="OK";
		if($("#csh_id_card").val()==""){
			$("#csh_id_card").val(id_card);
		}
		gipro(promo_code); 
	}
	
	
	function frominputchk(promo_code,cr){
		
	    $.get("/sales/promotion/frominputchk",{
	    	promo_code:promo_code ,
	    	cr: cr,
	  
	        ran:Math.random()},function(data){
	        	$("#dialog-hotpro" ).dialog( "destroy" );
	        	$("#dialog-hotpro").dialog({
	    			height: 400,width:'70%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){  	    	            
  	    			 $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
  	    			 
	    			
	    		},
			    close:function(evt,ui){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						$("#dialog-hotpro" ).dialog( "destroy" );
						canclelastpro(promo_code);
						lastaddhotpro();
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){
						$("#dialog-hotpro" ).dialog( "destroy" );
						canclelastpro(promo_code);
						lastaddhotpro();
					} 
				},

	    		
	    			buttons: {

						"ยกเลิก":function(){ 	
							$("#dialog-hotpro" ).dialog( "destroy" );
							canclelastpro(promo_code);
							lastaddhotpro();
						}
	    		
	    		
					 }
	    		});
	        	$("#hotpro").html(data);  
	        	$("#code_check").focus();
	        	//*** check lock unlock
				if($("#csh_lock_status").val()=='Y'){
					lockManualKey();
				}else{
					unlockManualKey();
				}

	        	
		}); 
	}
	
	//---------------------------------------ตรวจสอบให้กรอกได้เฉพาะตัวเลขเท่านั้น
	function check_number(ch){
			var len, digit;
			if(ch == " "){ 
				return false;
				len=0;
			}else{
				len = ch.length;
			}

				for(var i=0 ; i<len ; i++)
				{
														digit = ch.charAt(i)
														if(digit >="0" && digit <="9" || digit=="." || digit==","){
														; 
														}else{
														return false; 
														} 

														if (   isNaN(ch)   ) {
															return false; 
														} else {
															;
														}
				} 

			return true;
	}
	
	
	function codechk(promo_code){//chk การยิงบาร์โค้ตเข้ามา

		var code_check=$("#code_check").val();
		
		if(code_check==""){
			alert("กรุณายิง barcode ด้วยค่ะ");
			return false;
		}

		if(code_check.length!=13){
			alert("รหัส barcode ไม่ครบ 13 หลัก");
			$("#code_check").val('');
			return false;
		}
		if(!check_number(code_check)){
			alert("barcode ต้องเป็นตัวเลขค่ะ");
			$("#code_check").val('');
			return false;
		}
	    $.get("/sales/promotion/codechk",{
	    	code_check:code_check ,
	    	promo_code: promo_code,
	  
	        ran:Math.random()},function(data){
	        	if(data=="no_privilege"){
	        		alert("รหัสบาร์โค๊ดนี้ไม่มีสิทธิเล่นโปรโมชั่น");
	        		$("#code_check").val('');
	        		return false;
	        	}else if(data=="play"){
	        		var_codechk="Y";
	        		var_code=code_check;
	        		$("#dialog-hotpro" ).dialog( "destroy" );
	        		lastgipro(promo_code); 
	        		
	        	}else if(data=="com2_play"){
	        		var_codechk="N";
	        		var_code="";
	        		alert("มีเครื่องอื่นกำลังใช้ Barcode นี้เล่นโปรอยู่ค่ะ");
	        		$("#code_check").val('');
	        		return false;	
	        	}else if(data=="stop"){
	        		var_codechk="N";
	        		var_code="";
	        		alert("เล่นไปแล้วห้ามเล่นอีก");
	        		$("#code_check").val('');
	        		return false;
	        	}else{
	        		var_codechk="N";
	        		var_code="";
	        		alert("รหัสบาร์โค๊ดไม่ถูกต้อง");
	        		$("#code_check").val('');
	        		return false;
	        	}

	        	
		}); 
	    
	   
	}
	
	
	
	function gipro(promo_code){ //search data for edit


		$("#dialog-hotpro" ).dialog( "destroy" );
	    $.get("/sales/promotion/chkstep",{
	    	promo_code: promo_code,
	    	chk_barcode:chk_barcode,
	        ran:Math.random()},function(data){

	    		if(chk=data.substring(0,12)=="limite_false"){
	    			var arr=data.split("X#X");
					if(arr[1]=="N"){
						alert(arr[2]);
					}else{
	    				alert("เล่นโปรโมชั่นเกิน Limit ที่กำหนดไว้ จำนวนที่สามารถเล่นได้ "+arr[1]+" ชิ้น");
					}
	    		}else if(data=="open_scan_coupon"){ //ต้องยิงบาร์โค้ดคูปองก่อน
	    			openscanbarcode(promo_code,'open_scan_coupon');
	    		}else if(data=="alert_coupon_only"){ //เตือนว่าโปรนี้เล่นร่วมกับคูปอง
	    			openscanbarcode(promo_code,'alert_coupon_only');
	    		}else if(data=="open_scan_code_mobile"){ //เตือนว่าให้รับค่ามาเช็คก่อนเล่นโปร
	    			from_input_chk(promo_code,'open_scan_code_mobile');
	    		}else if(data=="alert_from_idcard" && chk_barcode!="OK"){ ////เปิดBlock verify by read IDCARD
	    			openscanbarcode(promo_code,'alert_from_idcard');					
	    		}else if(data==1){//seq==1
	    			addseqhotpro(promo_code);//เล่นโปรเลย
	    			chk_barcode="";	
	    		}else if(data=="open_promotion_auto"){ //เปิด Popup เล่นโปร Auto
	    			openproauto(promo_code);
	    			chk_barcode="";	
	    		}else{//Step seq>1


	    		    $.get("/sales/promotion/gipro",{
	    		    	promo_code: promo_code,
						id_card:$("#csh_id_card").val(),
						
	    		        ran:Math.random()},function(data){
	    	  	    		$("#dialog-hotpro").dialog({
	    		    			height: 500,width:'70%',modal: true,resizable:true,closeOnEscape:true,
	    		    			open:function(){
	    	  	    			
	    	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
	    	  	    			$(this).dialog('widget')
                                // find the title bar element
                                .find('.ui-dialog-titlebar')
                                // alter the css classes
                                .removeClass('ui-corner-all')
                                .addClass('ui-corner-top'); 
	    	  	    			
                                $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EEF3F8",//#CFE2E5
                                    "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
                                $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                                	"margin":"0 0 0 0","background-color":"#5C9397"}); 
                                
                                
	    	  	    			 $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    	  	    			 
	    	  	    			
	    		    			
	    		    		},
	    		    		
						    close:function(evt,ui){
								if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
	    							canclestock();
									return false;
								}
								if(evt.originalEvent && $(evt.originalEvent.which==27)){
	    							canclestock();
									return false;
								} 
							},

	    		    		
    		 
	    		    		
	    		    			/*buttons: {
									"สินค้าที่ร่วมโปร":function(){ 		
									viewproduct(promo_code,$("#manual_promo_seq_hide").val());
									return false;
									},
			 			
			 			
									"จบโปร":function(){ 		
	    		    						chkendpro(promo_code);
											return false;
						 			},
    						 
    						 

	    							"ยกเลิก":function(){ 		
	    		    							canclestock();
	    										return false;
	    							}
	    		    		
	    		    		
	    						 }*/
								 
								 
							 buttons: [
												{
													text: "สินค้าที่ร่วมโปร",
													id:"btnproduct",
													click:function(){
														viewproduct(promo_code,$("#manual_promo_seq_hide").val());
														return false;
													}
		
												},
												{
													text: "จบโปร",
													id:"btnstop",
													click:function(){
														if(promo_code=="OS06270317" || promo_code=="OS06280317" || promo_code=="OS06290317" || promo_code=="OS06300317" || promo_code=="OS06310317" || promo_code=="OS06320317" || promo_code=="OS06330317" || promo_code=="OT04030417_1" || promo_code=="OT04030417_2" || promo_code=="OT04100517_1" || promo_code=="OT04100517_2"){
															alert("โปรโมชั่นนี้ต้องยิงสินค้าให้ครบค่ะ");
															return false;
														}
														chkendpro(promo_code);
														return false;
														
													}
												},
												{
													text: "ยกเลิก",
													id:"btnesc",
													click:function(){
														canclestock();
														return false;
													}
												}
												
								  ] 
					 
					 
	    		    		});
	    		        	
	    	  	    		findpro('ui-dialog-title-dialog-hotpro',promo_code);	
	    		        	$("#hotpro").html(data);   
	    		        	viewpro();
	    		        	$("#manual_input_product").focus();
	    		        	chk_barcode="";	
	    		        	
	    		        	
	    			}); 
	    		}
		}); 
	    
	    

	    
		

	}
	
	
	
	function openproauto(promo_code){
		
	    $.get("/sales/promotion/openproauto",{
	    	promo_code: promo_code,
	        ran:Math.random()},function(data){
  	    		$("#dialog-hotpro").dialog({
	    			height: 600,width:'70%',modal: true,resizable:true,closeOnEscape:false,
	    			open:function(){
  	    			
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EDF0F3",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#ECECEC"}); 
                    
                    
  	    			 $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
  	    			 
  	    			
	    			
	    		},
	    		

			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						canclestockauto();
						return false;
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){
						canclestockauto();
						return false;
						
					} 
				},

				
 
	    		
	    			buttons: {
						"เล่นโปรโมชั่น":function(){ 		
						runproauto();
						return false;
						},
 			
						"สินค้าที่ร่วมโปรโมชั่น":function(){ 		
							viewproductauto(promo_code);
							return false;
							},
							
							
						"ลบรายการ":function(){ 		
							delallauto();
							return false;
							},
							
							
						"ยกเลิก":function(){ 		
									var c=confirm("คุณต้องการยกเลิกโปรโมชั่นนี้ใช่หรือไม่");
									if(!c){
										return false;
									}
	    							canclestockauto();
									return false;
						}
	    		
	    		
					 }
	    		});

	        	 
  	    		findpro('ui-dialog-title-dialog-hotpro',promo_code);	
	        	$("#hotpro").html(data);   
	        	viewproauto();
	        	$("#manual_input_product").focus();
	        	
	        	$("#from_scan_auto").keydown(function(e) {
	        	    if (e.keyCode == 27){
						var c=confirm("คุณต้องการยกเลิกโปรโมชั่นนี้ใช่หรือไม่");
						if(!c){
							return false;
						}
	        	    	canclestockauto();
	        	    	return false;
	        	    }
	        	});
	        	

	        	
	        	
	        }); 
	        	
	}

	
	function runproauto(){

		/*var c=confirm("คุณต้องการประมวลผลใช่หรือไม่");
		if(!c){
			return false;
		}*/
		$.get("/sales/promotion/runproauto",{
			promo_code:$("#manual_promo_code_hide").val(),
	        ran:Math.random()},function(data){
	        	if(data=="No_map"){
	        		alert("รายการสินค้าไม่ครบคู่");
	        		return false; 
	        	}
	        	
	        	$("#dialog-hotpro" ).dialog( "destroy" );
	        	promotionDetail('N');
	        	
		}); 
		
		
	}
	
	function dellistauto(id){

		var trid="tr"+id;
		var keepid=$("#keep").val();

		
		var n=keepid.search(id); 
		if(n>0){//have
			var iddel=","+id;
			keepid=keepid.replace(iddel,""); 
			$("#keep").val(keepid);
			
			$("#"+trid).removeClass("view_auto_click").addClass("view_auto_unclick");
		}else{
			var new_keep=keepid+","+id;
			$("#keep").val(new_keep);
			$("#"+trid).removeClass("view_auto_unclick").addClass("view_auto_click");
		}
		
		
		
		
	}
	
	function delallauto(){
		if($("#keep").val()==""){
			alert("คุณยังไม่ได้เลือกรายการ");
			return false;
		}
		var c=confirm("คุณต้องการลบข้อมูลใช่หรือไม่");
		if(!c){
			return false;
		}
		$.get("/sales/promotion/delallauto",{
			keepid:$("#keep").val(),
	        ran:Math.random()},function(data){
	        	if(data==""){
	        		$("#keep").val("");
	        		viewproauto();
	        	}else{
	        		alert("ลบข้อมูลไม่สำเร็จ");
	        		return false;
	        	}
	        	
		}); 
	}
	
	
	function findpro(id_title,promo_code){

		
		$.get("/sales/promotion/findpro",{
			promo_code:promo_code,
	        ran:Math.random()},function(data){
	        	$("#"+id_title).html(data);	
	        	$("#"+id_title).css({"font-size":"24","font-weight":"bold"});	
	        	
		}); 
		
		
	}
	
	
	function viewpro(){

		$.get("/sales/promotion/viewpro",{
			promo_code:$("#manual_promo_code_hide").val(),
			promo_seq:$("#manual_promo_seq_hide").val(),
			set_quantity:$("#manual_seq_quantity").val(),
	        ran:Math.random()},function(data){
	        	$("#viewpro").html(data);
	        	$("#manual_input_product").val("");
	        	$("#manual_input_quantity").val(1);
	        	$("#manual_input_product").focus();
	        	
	        	

	    	    $("#xblinky").blinky({ count: 10 }); 
	    	    
	    	    /*$("#xblinky").animate({opacity:0},400,"linear",function(){
	    	    	  $(this).animate({opacity:1},200);
	    	    	});*/

	        	
		}); 
		
		
	}
	
	
	
	function viewproauto(){

		$.get("/sales/promotion/viewproauto",{
			promo_code:$("#manual_promo_code_hide").val(),
	        ran:Math.random()},function(data){
	        	$("#viewpro").html(data);
	        	$("#manual_input_product").val("");
	        	$("#manual_input_quantity").val(1);
	        	$("#manual_input_product").focus();

		}); 
		
		
	}
	
	

	function chkendpro(promo_code){
		//alert(parseInt($("#manual_promo_seq_hide").val()));
		if(promo_code=="OS06271016" && parseInt($("#manual_promo_seq_hide").val())<=3){
			
			alert("โปรโมชั่นนี้ต้องยิงสินค้าอย่างน้อย 3 ชิ้นค่ะ");
			return false;
			
		}
	    $.get("/sales/promotion/chkendpro",{
	    	promo_code: promo_code,
	        ran:Math.random()},function(data){
	        	if(data=="Y"){ //add เป็นรายการปกติไป
	        		addnohotpro(promo_code);
	        	}else{//add ตามเงื่อนไขโปร
	        		addhotpro();
	        	}
		}); 
	}
	
	
	
	function canclestock(){
		
	    $.get("/sales/promotion/canclestock",{
	        ran:Math.random()},function(data){
	        $("#dialog-hotpro" ).dialog( "destroy" );		
        	$("#last_input_product").val("");
        	$("#last_input_quantity").val(1);
        	$("#last_input_product").focus();
        	
 	    	$("#csh_product_id").val("");
	    	$("#csh_quantity").val(1);
	    	$("#csh_product_id").focus();
		}); 
	}
	
	function canclestockauto(){
		/*var c=confirm("คุณต้องการยกเลิกการเล่นโปรโมชั่นนี้ใช่หรือไม่");
		if(!c){
			return false;
		}*/
		
		
	    $.get("/sales/promotion/canclestockauto",{
	        ran:Math.random()},function(data){
	        $("#dialog-hotpro" ).dialog( "destroy" );		
        	$("#last_input_product").val("");
        	$("#last_input_quantity").val(1);
        	$("#last_input_product").focus();
        	
 	    	$("#csh_product_id").val("");
	    	$("#csh_quantity").val(1);
	    	$("#csh_product_id").focus();
		}); 
	}
	


	
	
	
	function addproduct(){ //search data for edit
		if($("#manual_input_product").val()=="" || $("#manual_input_quantity").val()=="" || $("#manual_input_quantity").val()<=0 || !check_number($("#manual_input_quantity").val())){
			alert("ป้อนข้อมูลไม่ถูกต้อง");
        	$("#manual_input_product").val("");
        	$("#manual_input_quantity").val(1);
        	$("#manual_input_product").focus();
			return false;
		}
		

		 
		 
	    $.get("/sales/promotion/addproduct",{
	    	promo_code: $("#manual_promo_code_hide").val(),
	    	promo_seq: $("#manual_promo_seq_hide").val(),
	    	product_id: $("#manual_input_product").val(),
	    	quantity: $("#manual_input_quantity").val(),
	        ran:Math.random()},function(data){
				 if(data=="Noproduct"){
						 alert("ไม่มีสินค้านี้ในระบบ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=='stock_short'){
					 	alert("สต๊อคสินค้าไม่พอ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=="price_L_err") {
		        		alert("ราคาสินค้านี้มากกว่าตัวหลัก");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=="price_G_err"){
					 	alert("ราคาสินค้านี้มากกว่าตัวหลัก");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=="productRepeat"){
					 	alert("ตัวแถมห้ามซ้ำกับตัวซื้อ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 }else if(data=="Product_double"){
					 	alert("ห้ามใช้สินค้านี้ เพราะมีแล้ว");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data==4){
					 	alert("ยิงสินค้าเกินจำนวนที่จะเล่นโปรโมชั่น");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data==2) {
	        		alert("ไม่มีรหัสสินค้าในโปรโมชั่นนี้");
		        	$("#manual_input_product").val("");
		        	$("#manual_input_quantity").val(1);
		         } else if(data=="scan_diff"){//ยังไม่ครบ Quantity ที่กำหนดไว้
		        	 var seqNext=$("#manual_promo_seq_hide").val();
		        	 	  viewpro();
		        	 	

	        	} else if(data=="scan_ok" && parseInt($("#manual_promo_seq_hide").val(),10)<parseInt($("#manual_maxPro_hide").val(),10)) {//ยังไม่ครบ Step
	        		
	        		var readSeq=parseInt($("#manual_promo_seq_hide").val(),10); //อ่านมา
    					seqNext=eval(readSeq)+1;//บวก
    					parseInt($("#manual_promo_seq_hide").val(seqNext),10);//แทน
    					viewpro();
    				
	        	}else{//ครบแล้ว
	        	
        	    	$("#manual_input_product").val("");
        	    	$("#manual_input_quantity").val(1);
        	    	$("#manual_input_product").focus();
        	    	addhotpro();
        	    	$("#dialog-hotpro" ).dialog( "destroy" );
	        	}
		}); 
	}

	function addhotpro(){ //search data for edit
	    $.get("/sales/promotion/addhotpro",{
	        ran:Math.random()
	        },function(data){
				 if(data=="") {
					 promotionDetail('N');
					 $("#dialog-hotpro" ).dialog( "destroy" );
		         }
				 
     	    	$("#csh_product_id").val("");
    	    	$("#csh_quantity").val(1);
    	    	$("#csh_product_id").focus();
		}); 
	}
	
	
	function addnohotpro(promo_code){ //เล่นโปรแบบราคาทะเบียน เมื่อออกจากโปรกลางคัน
	    $.get("/sales/promotion/addnohotpro",{
	    	promo_code:promo_code,
	        ran:Math.random()
	        },function(data){
				 if(data=="") {
					 promotionDetail('N');
					 $("#dialog-hotpro" ).dialog( "destroy" );
		         }
				 
     	    	$("#csh_product_id").val("");
    	    	$("#csh_quantity").val(1);
    	    	$("#csh_product_id").focus();
		}); 
	}
	

	
	
	function addseqhotpro(promo_code){ //เล่นโปรแบบที่มี Seq เดียว
	    $.get("/sales/promotion/addseqhotpro",{
	    	promo_code:promo_code,
	        ran:Math.random()
	        },function(data){
				
				/*
				if(promo_code=="OS06270317" || promo_code=="OS06280317" || promo_code=="OS06290317" || promo_code=="OS06300317" || promo_code=="OS06310317" || promo_code=="OS06320317" || promo_code=="OS06330317"){
					if(data!=""){
						alert(data);
						$("#csh_product_id").val("");
						$("#csh_quantity").val(1);
						$("#csh_product_id").focus();					
						return false;
					}
				}*/
				
				 if(data=="") {
					 promotionDetail('N');
					 $("#dialog-hotpro" ).dialog( "destroy" );
		         }
				 
     	    	$("#csh_product_id").val("");
    	    	$("#csh_quantity").val(1);
    	    	$("#csh_product_id").focus();
		}); 
	}


	
	
	function addnormal(product_id,quantity){ //add แบบราคาทะเบียน
		if(product_id=="" || quantity=="" || quantity<=0 || !check_number(quantity)){
			alert("ป้อนข้อมูลไม่ถูกต้อง");
 	    	$("#csh_product_id").val("");
	    	$("#csh_quantity").val(1);
	    	$("#csh_product_id").focus();
			return false;
		}
	    $.get("/sales/promotion/addnormal",{
	    	product_id:product_id,
	    	quantity:quantity,
	        ran:Math.random()
	        },function(data){
	        	if(data=="Noproduct"){
	        		alert("ไม่พบรหัสสินค้านี้ในระบบ");
	        
	        	}else if(data=="price_null"){
	        		alert("สินค้านี้ไม่มีราคา ห้ามขาย");
	        	
	        	}else if(data=="Nostock"){
	        		alert("สต๊อคสินค้าไม่พอ");
	        	
	        	}else if(data=="stockShort"){
	        		alert("Stock ไม่พอ");
	        	
	        	}else{
					 promotionDetail('N');
					 $("#dialog-hotpro" ).dialog( "destroy" );
		         }
				 
     	    	$("#csh_product_id").val("");
    	    	$("#csh_quantity").val(1);
    	    	$("#csh_product_id").focus();
		}); 
	}
	
	
	
	
	function addproductauto(){ //search data for edit
		if($("#manual_input_product").val()=="" || $("#manual_input_quantity").val()=="" || $("#manual_input_quantity").val()<=0 || !check_number($("#manual_input_quantity").val())){
			alert("ป้อนข้อมูลไม่ถูกต้อง");
        	$("#manual_input_product").val("");
        	$("#manual_input_quantity").val(1);
        	$("#manual_input_product").focus();
			return false;
		}
		

		 
	    $.get("/sales/promotion/addproductauto",{
	    	promo_code: $("#manual_promo_code_hide").val(),
	    	promo_seq: $("#manual_promo_seq_hide").val(),
	    	product_id: $("#manual_input_product").val(),
	    	quantity: $("#manual_input_quantity").val(),
	        ran:Math.random()},function(data){
				 if(data=="Noproduct"){
						 alert("ไม่มีสินค้านี้ในระบบ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=='stock_short'){
					 	alert("สต๊อคสินค้าไม่พอ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=='no_in_pro'){
					 	alert("ไม่มีรหัสสินค้านี้ในโปรโมชั่น");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
	        	}else if(data=='Y'){//ครบแล้ว
	        	
        	    	$("#manual_input_product").val("");
        	    	$("#manual_input_quantity").val(1);
        	    	$("#manual_input_product").focus();
        	    	viewproauto();
        	    	
        	    	//addhotpro();
        	    	//$("#dialog-hotpro" ).dialog( "destroy" );
	        	}else{
	        		alert("ครบแล้ว");
	        	}
		}); 
	}
	
	
	function addproductautoclick(product_id){ //search data for edit

		 
	    $.get("/sales/promotion/addproductauto",{
	    	promo_code: $("#manual_promo_code_hide").val(),
	    	promo_seq: $("#manual_promo_seq_hide").val(),
	    	product_id: product_id,
	    	quantity: 1,
	        ran:Math.random()},function(data){
				 if(data=="Noproduct"){
						 alert("ไม่มีสินค้านี้ในระบบ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=='stock_short'){
					 	alert("สต๊อคสินค้าไม่พอ");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
				 } else if(data=='no_in_pro'){
					 	alert("ไม่มีรหัสสินค้านี้ในโปรโมชั่น");
			        	$("#manual_input_product").val("");
			        	$("#manual_input_quantity").val(1);
			        	$("#manual_input_product").focus();
	        	}else if(data=='Y'){//ครบแล้ว
	        	
        	    	$("#manual_input_product").val("");
        	    	$("#manual_input_quantity").val(1);
        	    	$("#manual_input_product").focus();
        	    	viewproauto();
        	    	$("#dialog-viewproduct" ).dialog( "destroy" );
        	    	//addhotpro();
        	    	//$("#dialog-hotpro" ).dialog( "destroy" );
	        	}else{
	        		alert("ครบแล้ว");
	        	}
		}); 
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function lastprochk(){ //หาโปรท้ายบิลมาเล่น
		$("#dialog-modal" ).dialog( "destroy" );
		$("#dialog-lastpro_select" ).dialog( "destroy" );
		$("#dialog-lastpro_play" ).dialog( "destroy" );

	    $.get("/sales/promotion/lastprochk",{
	        ran:Math.random()},function(data){
	        	var chk=data.substring(0,9);
	        	
		        if(data=="stopAmt"){//หมดโปรตามยอดเงิน
		        	lastprochkqty();
		        }else if(chk=="lastgipro"){//โปรเดียว
		        	eval(data);
		        }else{//แสดงโปรให้เลือก
					$("#lastpro_select").html(data);
					$( "#dialog-lastpro_select" ).dialog({
						height: 500,width:'90%',modal: true,resizable:true,closeOnEscape:true,
		    			open:function(){
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
	  	    			$(this).dialog('widget')
                        // find the title bar element
                        .find('.ui-dialog-titlebar')
                        // alter the css classes
                        .removeClass('ui-corner-all')
                        .addClass('ui-corner-top'); 
	  	    			
                        $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EEF3F8",//#CFE2E5
                            "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
                        $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                        	"margin":"0 0 0 0","background-color":"#5C9397"}); 
                        
                        
	  	    			 $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
		    		},
				    close:function(evt,ui){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							var promo_code_this=$("#promo_code_this").val();
							canclelastprolevel(promo_code_this);
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
							

								var promo_code_this=$("#promo_code_this").val();
								canclelastprolevel(promo_code_this);

							
							
						} 
					},
						
		    			buttons: {
							"ข้าม":function(){ 	
										var promo_code_this=$("#promo_code_this").val();
		    							canclelastprolevel(promo_code_this);
										
							}
						 }
					
					
						
						
						});
					
				
					


    	    		

		        }
		}); 
	}

	function lastprochkqty(){ 
		$("#dialog-modal" ).dialog( "destroy" );
		$("#dialog-lastpro_select" ).dialog( "destroy" );
		$("#dialog-lastpro_play" ).dialog( "destroy" );
		var promo_code=$("#level_this").val();
	    $.get("/sales/promotion/lastprochkqty",{
	        ran:Math.random()},function(data){
	        	var chk=data.substring(0,9);
	        	
		        if(data=="stopQty"){//หมดโปรตามยอดชิ้น
		        	promotionDetail('Y');
		    		$("#dialog-lastpro_select" ).dialog( "destroy" );
		    		$("#dialog-lastpro_play" ).dialog( "destroy" );
					//alert("เล่นโปรโมชั่นเสร็จแล้ว");
		        }else if(chk=="lastgipro"){//โปรเดียว
		        	eval(data);
		        }else{//แสดงโปร
					$("#lastpro_select").html(data);
					//$( "#dialog-modal" ).dialog({height: 400,width:500,modal: true,resizable:true

			    	$("#dialog-lastpro_select").dialog({
		    			height: 400,width:'80%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
		    		},
				    close:function(evt,ui){
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							var promo_code_this=$("#promo_code_this").val();
							canclelastprolevel(promo_code_this);
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){

								var promo_code_this=$("#promo_code_this").val();
								canclelastpro(promo_code_this);

						} 
					},

		    			buttons: {
							"ข้าม":function(){ 		
		    							canclelastprolevel(promo_code);
										
							}
						 }
		    		});
			    	
			    	

					
					
		        }
		}); 
	}



function drivepro(val){
	
	var arr=val.split("#");
	var_codechk="Y";
	
	if($("#csh_id_card").val()==""){
		$("#csh_id_card").val(arr[0]);
	}
	
	lastgipro(arr[4]);
}


var var_codechk="N";
var var_code="";
function lastgipro(promo_code){ //search data for edit
		$("#dialog-lastpro_select" ).dialog( "destroy" );
		$("#dialog-lastpro_play" ).dialog( "destroy" );

		
		if((promo_code=="OX08030915" || promo_code=="OX08050915"   || promo_code=="OX08211215" ) && $("#csh_member_no").val().substring(0,2)!="ID"   && (var_codechk=="N" || var_codechk=="") ){ ////เปิดBlock verify by read IDCARD
			var datastart=$("#csh_member_no").val()+"#"+promo_code+"#"+$("#csh_id_card").val()+"#"+$("#csh_mobile_no").val();
			var member_no=$("#csh_member_no").val();

			
			apiread(datastart,"drivepro");
			return false;
			
		}
					
					
		if(promo_code=="OX09010713" && var_codechk!="Y"){//chk code ก่อนเล่นโปร
			frominputchk(promo_code,'open_scan_code_mobile');
			return false;
		}

			
			
		if(promo_code=="OX08030813" || promo_code=="OX08040813" || promo_code=="OX08050813"  || promo_code=="OX08020814"  || promo_code=="OX08040814" || promo_code=="OX09221214" || promo_code=="OX09171214" || promo_code=="OX09181214" || promo_code=="OX09191214" || promo_code=="OX09201214" || promo_code=="OX09391114" || promo_code=="OX09401114" || promo_code=="OX09411114" || promo_code=="OX09431114" || promo_code=="OX09441114" || promo_code=="OX09451114" || promo_code=="OX08310215" || promo_code=="OX08320215" || promo_code=="OX09200115" || promo_code=="OX09070215" || promo_code=="OX09080215" || promo_code=="OX09090215" || promo_code=="OX09220115" || promo_code=="OX09100215" || promo_code=="OX09110215" || promo_code=="OX09120215"  || promo_code=="OT04160315"  || promo_code=="OX10020915"  || promo_code=="OX08030915"  || promo_code=="OX08040915"   || promo_code=="OX08030915"   || promo_code=="OX08040915" || promo_code=="OX08050915"    || promo_code=="OX08211215"  || promo_code=="OS07110116"   || promo_code=="OX10120116"   || promo_code=="OX10050116"   || promo_code=="OX10030216"  || promo_code=="OX10040216" || promo_code=="OX10050216" || promo_code=="OX10060216" || promo_code=="OX10070216" || promo_code=="OX08041016" || promo_code=="OX08051016"  ){//เล่นให้ครบ step
			
			var chk_esc=false;
		}else{
			
			var chk_esc=true;
		}

		if(promo_code=="OX10020915" || promo_code=="OX10050116"){
			var block_width="90%";
			var block_height="700";
		}else{
			var block_width="70%";
			var block_height="500";
		}

		
	    $.get("/sales/promotion/lastgipro",{
	    	promo_code: promo_code,
			id_card: $("#csh_id_card").val(),
	        ran:Math.random()},function(data){
	        		var arr=data.split("@@@");
					var chkstock=arr[0];
					var chkstockarr=chkstock.split("@");
					if(chkstockarr[0]=="Y"){//ถ้ามีstock อยู่ ต้อง แถม ห้าม Esc ข้ามโปรไป
						chk_esc=false;
					}
					
					var htmldata=arr[1];
	        		//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: block_height,width:block_width,modal: true,resizable:true,closeOnEscape:chk_esc,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EEF3F8",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#5C9397"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
		    		},
			    	
				    close:function(evt,ui){

						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);

								canclelastpro(promo_code);
		    					lastaddhotpro();
							
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){

								canclelastpro(promo_code);
		    					lastaddhotpro();
							
								
					
						} 
					},
					
					
					
					 buttons: [
                                        {
                                            text: "PRODUCT",
                                            id:"btnproduct",
											click:function(){
												viewproduct(promo_code,$("#last_step_seq").val());	
											}

                                        },
                                        {
                                            text: "ESCAPE",
                                            id:"btnesc",
											click:function(){
												if((promo_code=="OX08030813" || promo_code=="OX08040813" || promo_code=="OX08050813"  || promo_code=="OX08020814"  || promo_code=="OX08040814" || promo_code=="OX09221214" || promo_code=="OX09171214" || promo_code=="OX09181214" || promo_code=="OX09191214" || promo_code=="OX09201214" || promo_code=="OX09391114" || promo_code=="OX09401114" || promo_code=="OX09411114" || promo_code=="OX09431114" || promo_code=="OX09441114" || promo_code=="OX09451114"  || promo_code=="OX08310215" || promo_code=="OX08320215" || promo_code=="OX09200115" || promo_code=="OX09070215" || promo_code=="OX09080215" || promo_code=="OX09090215" || promo_code=="OX09220115" || promo_code=="OX09100215" || promo_code=="OX09110215" || promo_code=="OX09120215"  || promo_code=="OT04160315"    || promo_code=="OX10020915"  || promo_code=="OX08030915"  || promo_code=="OX08040915"   || promo_code=="OX08030915"   || promo_code=="OX08040915" || promo_code=="OX08050915"    || promo_code=="OX08211215"  || promo_code=="OS07110116"   || promo_code=="OX10120116"   || promo_code=="OX10050116"  || promo_code=="OX10030216"  || promo_code=="OX10040216" || promo_code=="OX10050216" || promo_code=="OX10060216" || promo_code=="OX10070216" || promo_code=="OX08041016" || promo_code=="OX08051016") && $("#last_promo_seq_hide").val()>'1'){
													alert("โปรโมชั่นนี้ต้องยิงสินค้าให้ครบค่ะ");
													return false;
												}else if(chkstockarr[0]=="Y"){
													alert("โปรโมชั่นนี้ถูกกำหนดไว้ว่า หากมีสินค้าอยู่ในร้าน ต้องเล่นโปรนี้ให้ลูกค้าค่ะ");
													return false;
												}else{
													canclelastpro(promo_code);
													lastaddhotpro();
												}
												
											}
											

                                        }
										
                          ] 
					 
					 
		    			/*buttons: [{
							 id:"btnPayBySelCoPromo", 
							"PRODUCT":function(){ 		
								viewproduct(promo_code,'1');
								
							},
						
							/*"END":function(){ 		
							lastaddhotpro();
								
							},*/
							
							/*"ESCAPE":function(){ 
								if((promo_code=="OX08030813" || promo_code=="OX08040813" || promo_code=="OX08050813"   || promo_code=="OX08020814"  || promo_code=="OX08040814"   || promo_code=="OX09221214"  || promo_code=="OX09171214" || promo_code=="OX09181214" || promo_code=="OX09191214" || promo_code=="OX09201214" ) && $("#last_promo_seq_hide").val()>'1'){
									alert("ต้องยิงสินค้าให้ครบค่ะ");
									return false;
								}else{
									canclelastpro(promo_code);
			    					lastaddhotpro();
								}
								
										
							},
							
							
							
							

						 }*/
		    		});
			    	
			    findpro('ui-dialog-title-dialog-lastpro_play',promo_code);		
	        	$("#lastpro_play").html(htmldata); 
	        	$("#show_button").html("xxx");
	        	lastviewpro();
	        	$("#last_input_product").focus();
	        	var_codechk=""; //clear chk code
	        	var_code="";
	    		
	    		
	        	
	        	

		}); 
	}

	
	function lastviewpro(){

		$.get("/sales/promotion/lastviewpro",{
			promo_code:$("#last_promo_code_hide").val(),
			promo_seq:$("#last_promo_seq_hide").val(),
			set_quantity:$("#last_seq_quantity").val(),
	        ran:Math.random()},function(data){
	        	$("#lastviewpro").html(data);
	        	$("#last_input_product").val("");
	        	$("#last_input_quantity").val(1);
	        	$("#last_input_product").focus();
	        	


	        	
	        	
	        	//$("#xblinky").blinky({ count: 10 }); 
	        	
		}); 
		
		
	}
	
	function viewproduct(promo_code,promo_seq){
		
		$("#dialog-viewproduct" ).dialog( "destroy" );
		$.get("/sales/promotion/viewproduct",{
			promo_code:promo_code,
			promo_seq:promo_seq,
	        ran:Math.random()},function(data){
		    	$("#dialog-viewproduct").dialog({
	    			height: 400,width:'60%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EEF3F8",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#5C9397"}); 
                    
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    		},
			    close:function(evt,ui){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						$("#dialog-viewproduct" ).dialog( "destroy" );
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){
						$("#dialog-viewproduct" ).dialog( "destroy" );
					} 
				},
				
				
	    			buttons: {
						"ปิด":function(){ 		
						$("#dialog-viewproduct" ).dialog( "destroy" );
							
						}

					 }
	    		});
		    	$("#viewproduct").html(data); 
		    	
	        	
		}); 
		
		
	}
	
	
	
	function viewproductauto(promo_code){
		$("#dialog-viewproduct" ).dialog( "destroy" );
		$.get("/sales/promotion/viewproductauto",{
			promo_code:promo_code,
	        ran:Math.random()},function(data){
		    	$("#dialog-viewproduct").dialog({
	    			height: 400,width:'60%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EEF3F8",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#5C9397"}); 
                    
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    		},
			    close:function(evt,ui){
					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						$("#dialog-viewproduct" ).dialog( "destroy" );
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){
						$("#dialog-viewproduct" ).dialog( "destroy" );
					} 
				},
				
				
	    			buttons: {
						"ปิด":function(){ 		
						$("#dialog-viewproduct" ).dialog( "destroy" );
							
						}

					 }
	    		});
		    	$("#viewproduct").html(data); 
		    	
	        	
		}); 
		
		
	}
	function canclelastpro(promo_code){ //search data for edit
	    $.get("/sales/promotion/canclelastpro",{
	    	promo_code: promo_code,
	        ran:Math.random()},function(data){
	        	$("#lastviewpro").keydown(function(e) {
	        	    if (e.keyCode == 27){
	        	    	return false;
	        	    }
	        	});
	        	lastprochk();
		}); 
	}
	
	function canclelastprolevel(promo_code){ //search data for edit
	    $.get("/sales/promotion/canclelastprolevel",{
	    	promo_code: promo_code,
	        ran:Math.random()},function(data){
	        	$("#lastviewpro").keydown(function(e) {
	        	    if (e.keyCode == 27){
	        	    	return false;
	        	    }
	        	});
	        	lastprochk();
	        	
		}); 
	}	
	

	function lastaddproduct(){ //search data for edit
		if($("#last_input_product").val()=="" || $("#last_input_quantity").val()=="" || $("#last_input_quantity").val()<=0  || !check_number($("#last_input_quantity").val())){
				alert("ป้อนข้อมูลไม่ถูกต้อง");
			 $("#last_input_product").val("");
			 $("#last_input_quantity").val(1);
			 $("#last_input_product").focus();
			return false;
		}
		
		//check coupon
		if($("#coupon_code_chk").val()=="S"){
			if($("#coupon_code_play").val()==""){
				 alert("โปรโมชั่นนี้ต้องป้อนรหัสคูปองด้วยค่ะ");
				return false;
			}else{
				$("#csh_coupon_code").val($("#coupon_code_play").val());
				$("#csh_promo_code").val($("#last_promo_code_hide").val()); 			
			}
			
			if(parseInt($("#coupon_code_play").val().length,10)>=8 && parseInt($("#coupon_code_play").val().length,10)<=9){
	
			}else{
				alert("รหัสคูปองต้องมี 8 หลัก หรือ 9 หลักเท่านั้นค่ะ");
				$("#csh_coupon_code").val($("#coupon_code_play").val());
				$("#csh_promo_code").val($("#last_promo_code_hide").val()); 
				return false;	
			}
		}

			
		/*if($("#coupon_code_chk").val()=="S"){
				$.get("../../../download_promotion/id_card_quick/chk_coupon_code_play.php",{
					promo_code: $("#last_promo_code_hide").val(),
					coupon_code_play: $("#coupon_code_play").val(),
					ran:Math.random()},function(x){
						var arr=x.split("###");
						if(arr[0]=="N"){
							alert(arr[1]);
							return false;
						}
					 
				}); 
				
				
				
		}*/


		//chk pro lot_expire
		if($("#last_promo_code_hide").val()=="OX07100616" || $("#last_promo_code_hide").val()=="OX07110616" || $("#last_promo_code_hide").val()=="OX07120616"  || $("#last_promo_code_hide").val()=="OX07270616" ){
			if($("#lot_dt").val()==""){
				alert("สำหรับโปรโมชั่นนี้ต้องป้อนวันที่ผลิตเพื่อตรวจสอบด้วยครับ");
				return false;
			}
			var var_lot_dt=$("#lot_dt").val();
			
		}else{
			var var_lot_dt="";
		}
	    $.get("/sales/promotion/lastaddproduct",{
			lot_dt:var_lot_dt,
	    	promo_code: $("#last_promo_code_hide").val(),
	    	promo_seq: $("#last_promo_seq_hide").val(),
	    	product_id: $("#last_input_product").val(),
	    	quantity: $("#last_input_quantity").val(),
	        ran:Math.random()},function(data){
				 if(data=="Noproduct"){
					 alert("ไม่มีสินค้านี้ในระบบ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=='stock_short'){
					 alert("สต๊อคสินค้าไม่พอ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="price_L_err") {
		        		alert("ราคาสินค้านี้มากกว่าตัวหลัก");
						 $("#last_input_product").val("");
						 $("#last_input_quantity").val(1);
						 $("#last_input_product").focus();
				 } else if(data=="price_G_err"){
					 alert("ราคาสินค้านี้มากกว่าตัวหลัก");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="productRepeat"){
					 alert("ตัวแถมห้ามซ้ำกับตัวซื้อ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="Product_double"){
					 alert("ห้ามใช้สินค้านี้ เพราะมีแล้ว");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data==4){
					 alert("ยิงสินค้าเกินจำนวนที่จะเล่นโปรโมชั่น");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="limit_log"){
					 alert("ลูกค้าได้รับถุงครบ 3 ถุง แล้ว ไม่สามารถเล่นโปรนี้ได้อีก ให้กดปุ่ม Esc ข้ามไปค่ะ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();		
				 }else if(data=="limit_log_play"){
					 alert("วันนี้ลูกค้าได้เล่นโปรโมชั่นนี้ครบ 3 ครั้ง แล้ว สามารถเล่นได้อีกในวันพรุ่งนี้ ตอนนี้ให้กดปุ่ม Esc ข้ามไปค่ะ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();	
				 } else if(data==2) {
	        		alert("ไม่มีรหัสสินค้าในโปรโมชั่นนี้");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="lot_no_stock") {
	        		alert("ไม่มี Stock ตามวันผลิตที่ป้อนเข้ามา");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="less300") {
	        		alert("โปรโมชั้นนี้ราคาสินค้าตัวแถมต้องมีราคาไม่เกิน 300 บาท");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="more300-500") {
	        		alert("โปรโมชั้นนี้ราคาสินค้าตัวแถมต้องมีราคา 300 - 500 บาท");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="more501-1000") {
	        		alert("โปรโมชั้นนี้ราคาสินค้าตัวแถมต้องมีราคา 501 - 1,000 บาท");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="more1000") {
	        		alert("โปรโมชั้นนี้ราคาสินค้าตัวแถมต้องมีราคา 1,001 บาท ขึ้นไป");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
		         } else if(data=="scan_diff"){//ยังไม่ครบ Quantity ที่กำหนดไว้
		        	     var seqNext=$("#last_promo_seq_hide").val();
	        	 	     lastviewpro();
		    	} else if(data=="scan_ok" && parseInt($("#last_promo_seq_hide").val(),10)<parseInt($("#last_maxPro_hide").val(),10)) {
		    		
	        		var readSeq=$("#last_promo_seq_hide").val(); //อ่านมา
					//seqNext=eval(readSeq)+1;//บวก
					seqNext=parseInt(readSeq,10)+1;//บวก
					$("#last_promo_seq_hide").val(seqNext);//แทน
					lastviewpro();
		    	}else{
		    	
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
			    	
			    	
		
			    	lastaddhotpro();
			    	$("#dialog-hotpro" ).dialog( "destroy" );
		    	}
				 
		}); 
	}


	
	function lastaddproductclick(product_id){ //search data for edit

		//check coupon
		if($("#coupon_code_chk").val()=="S"){
			if($("#coupon_code_play").val()==""){
				 alert("โปรโมชั่นนี้ต้องป้อนรหัสคูปองด้วยค่ะ");
				return false;
			}else{
				$("#csh_coupon_code").val($("#coupon_code_play").val());
				$("#csh_promo_code").val($("#last_promo_code_hide").val()); 			
			}
			
			if(parseInt($("#coupon_code_play").val().length,10)>=8 && parseInt($("#coupon_code_play").val().length,10)<=9){
	
			}else{
				 alert("รหัสคูปองต้องมี 8 หลัก หรือ 9 หลักเท่านั้นค่ะ");
				$("#csh_coupon_code").val($("#coupon_code_play").val());
				$("#csh_promo_code").val($("#last_promo_code_hide").val()); 
				return false;	
			}
		}
		
		
	    $.get("/sales/promotion/lastaddproduct",{
	    	promo_code: $("#last_promo_code_hide").val(),
	    	promo_seq: $("#last_promo_seq_hide").val(),
	    	product_id: product_id,
	    	quantity: 1,
	        ran:Math.random()},function(data){
				 if(data=="Noproduct"){
					 alert("ไม่มีสินค้านี้ในระบบ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=='stock_short'){
					 alert("สต๊อคสินค้าไม่พอ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="price_L_err") {
		        		alert("ราคาสินค้านี้มากกว่าตัวหลัก");
						 $("#last_input_product").val("");
						 $("#last_input_quantity").val(1);
						 $("#last_input_product").focus();
				 } else if(data=="price_G_err"){
					 alert("ราคาสินค้านี้มากกว่าตัวหลัก");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data=="productRepeat"){
					 alert("ตัวแถมห้ามซ้ำกับตัวซื้อ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="Product_double"){
					 alert("ห้ามใช้สินค้านี้ เพราะมีแล้ว");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 } else if(data==4){
					 alert("ยิงสินค้าเกินจำนวนที่จะเล่นโปรโมชั่น");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="limit_log"){
					 alert("ลูกค้าได้รับถุงครบ 3 ถุง แล้ว ไม่สามารถเล่นโปรนี้ได้อีก ให้กดปุ่ม Esc ข้ามไปค่ะ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
				 }else if(data=="limit_log_play"){
					 alert("วันนี้ลูกค้าได้เล่นโปรโมชั่นนี้ครบ 3 ครั้ง แล้ว สามารถเล่นได้อีกในวันพรุ่งนี้ ตอนนี้ให้กดปุ่ม Esc ข้ามไปค่ะ");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();						 
				 } else if(data==2) {
	        		alert("ไม่มีรหัสสินค้าในโปรโมชั่นนี้");
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
		         } else if(data=="scan_diff"){//ยังไม่ครบ Quantity ที่กำหนดไว้
		        	     var seqNext=$("#last_promo_seq_hide").val();
	        	 	     lastviewpro();
	        	 	     $("#dialog-viewproduct" ).dialog( "destroy" );
		    	} else if(data=="scan_ok" && parseInt($("#last_promo_seq_hide").val(),10)<parseInt($("#last_maxPro_hide").val(),10) ) {
		    		
	        		var readSeq=$("#last_promo_seq_hide").val(); //อ่านมา
					seqNext=parseInt(readSeq,10)+1;//บวก
					$("#last_promo_seq_hide").val(seqNext);//แทน
					lastviewpro();
					$("#dialog-viewproduct" ).dialog( "destroy" );
		    	}else{
		    	
					 $("#last_input_product").val("");
					 $("#last_input_quantity").val(1);
					 $("#last_input_product").focus();
			    	
			    	
		
			    	lastaddhotpro();
			    	$("#dialog-hotpro" ).dialog( "destroy" );
			    	
			    	$("#dialog-viewproduct" ).dialog( "destroy" );
		    	}
				 
		}); 
	}
	
	
	
	function lastaddhotpro(){ //search data for edit
	    $.get("/sales/promotion/lastaddhotpro",{
	        ran:Math.random()
	        },function(data){
				 if(data=="") {
					 promotionDetail('N');
					 lastprochk();
		         }
		}); 
	}
	
	
	function lastdelpro(){ 
	    $.get("/sales/promotion/lastdelpro",{
	        ran:Math.random()
	        },function(data){

					 promotionDetail('N');

		}); 
	}
	
	
	function deltmp1(seq){ 
	    $.get("/sales/promotion/deltmp1",{
	    	seq:'seq',
	        ran:Math.random()
	        },function(data){

					 promotionDetail('N');

		}); 
	}
	

	function mail_promotion(){
	    $.get("/sales/promotion/mailpromotion",{
	        ran:Math.random()},function(data){
	        	if(data=="bill_active"){
	        		$("#mail_promotion").html("มีโปรโมชั่นมาใหม่ให้ UPDATE <span onclick=\"window.open('/download_promotion/call_op.php','','width=200,height=100');\" style='cursor:pointer;'>CLICK</span>");
	        		$('#mail_promotion').fadeIn(3000, function() {
	        			
	        			$("#mail_promotion").blinky({ count: 3 }); 
	        		});
	        		
	        		setTimeout(function(){$('#mail_promotion').fadeOut(3000, function() { });},6000);
	        		
	        		
	        		
	        	}
		}); 
	}
	
	
	function servicekeep(){

		$.get("/sales/promotion/servicekeep",{
	        ran:Math.random()},function(data){
	        	//$("#lastviewpro").html(data);
	        	
		}); 
		
		
	}
	
	
	function testfunction(){
		
		/*$.get("/sales/promotion/testfunction",{
	        ran:Math.random()},function(data){
	        	alert(data);
	        	
		}); */
		//fromidcard('7777777777777');
		//nextstep('9999999999999');
		//frominputidcard();
		//ccsreadidcardfrom();
		alert("Function test");
		ccsregisterfrom('OPID300','xx');
		//fromreadprofileother('OI04140415','','');
		//m2mfromnew();
		//listfriend('9999999999999');
		//ccs_return_from();
		//api_verify_from();
		//fromreadprofileotp('OK18240415','Y','9999999999999');
		//fromreadprofile_idcard('OK27230415','9999999999999','3409900553439','0957198274','idcard');
		//apiread('tour_from');
		//fromreadprofile('OI02110615','Y','9999999999999','3409900553439','0957198274','idcard');
		//tour_from();
		//m2mfromcheck();
		//fromreadprofile_verify('REID','Y','9999999999999','ต่ออายุID','3409900553439')
		
	}
	
	
	
	//เก็บ ID CARD ลูกค้า
	function checkID(id) {
	    if(id.length != 13) return false;
	    for(i=0, sum=0; i < 12; i++)
	        sum += parseFloat(id.charAt(i))*(13-i);
	    if((11-sum%11)%10!=parseFloat(id.charAt(12)))
	        return false;
	    return true;
	}
	
	
	function fromidcard(member_no){
		
		$.get("/sales/promotion/fromidcard",{
			member_no:member_no,
	        ran:Math.random()},function(data){

	        	if(data!="have"){
		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 450,width:'60%',modal: true,resizable:true,closeOnEscape:true,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#DADADA"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#EEF3F8",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#5C9397"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
							addidcardcancle();
							$("#dialog-lastpro_play" ).dialog( "destroy" );
							$("#csh_product_id").focus();
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
							addidcardcancle();
							$("#dialog-lastpro_play" ).dialog( "destroy" );
							$("#csh_product_id").focus();	
					
						} 
					},
					
					
		    			buttons: {
				
	
							
							"Close":function(){ 
								//จะให้ทำไร
								addidcardcancle();
								$("#dialog-lastpro_play" ).dialog( "destroy" );
								$("#csh_product_id").focus();		
								
							},
	
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มสำรวจข้อมูลสมาชิก");
			    $("#lastpro_play").html(data);
	        	$("#keep_id_card").focus();
	        }
		});
		
		
	}
	function addidcard(){
		var keep_member_no=$("#keep_member_no").val();
		var keep_customer_id=$("#keep_customer_id").val();
		var keep_id_card=$("#keep_id_card").val();
		if(keep_id_card==""){
			alert("กรุณายิงป้อนรหัสบัตรประชาชนด้วยค่ะ");
			return false;
		}

		if(keep_id_card.length!=13){
			alert("รหัสบัตรประชาชน ไม่ครบ 13 หลัก");
			$("#keep_id_card").val('');
			return false;
		}
		if(!check_number(keep_id_card)){
			alert("บัตรประชาชนต้องเป็นตัวเลขค่ะ");
			$("#keep_id_card").val('');
			return false;
		}
		if(!checkID(keep_id_card)){
	        alert('รหัสประชาชนไม่ถูกต้อง');
			$("#keep_id_card").val('');
			return false;
		}
		
		
	    $.get("/sales/promotion/addidcard",{
	    	keep_member_no:keep_member_no,
	    	keep_customer_id:keep_customer_id,
	    	keep_id_card:keep_id_card,
	        ran:Math.random()},function(data){
	        	if(data=="Y"){
	        		//$("#lastpro_play").html("<center><br><br><br><span style='font-size:28px;color:#f4bb58 '>บันทึกข้อมูลเรียบร้อยค่ะ</span></center>"); 
					$("#dialog-lastpro_play" ).dialog( "destroy" );
					$("#csh_product_id").focus();	
	        	}else{
	        		alert("ไม่สามารถบันทึกข้อมูลได้");
	        	
	        	}
		}); 
	}
	
	function addidcardcancle(){
		var keep_member_no=$("#keep_member_no").val();
		var keep_customer_id=$("#keep_customer_id").val();
		var keep_id_card=$("#keep_id_card").val();
		
		
		
	    $.get("/sales/promotion/addidcardcancle",{
	    	keep_member_no:keep_member_no,
	    	keep_customer_id:keep_customer_id,
	    	keep_id_card:keep_id_card,
	        ran:Math.random()},function(data){

		}); 
	}	
	
	
	
	//สมัครสมาชิกใหม่
	function registerfrom(doc_no_diary){
	    $.get("/sales/newmember/registerfrom",{
	    	doc_no_diary:doc_no_diary,
	        ran:Math.random()},function(data){
	        	$("#dialog-lastpro_play").dialog({
	    			height: 850,width:'85%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

							//เมื่อปิด
							if($("#application_id").val()=="OPID300" || $("#application_id").val()=="OPPLI300"){
								alert("สำหรับการสมัครแบบใช้บัตรประชาชนแทนบัตรสมาชิกนั้น จะต้องบันทึกข้อมูลสมาชิกทันทีค่ะ");
								return false;
							}else{
							 	$("#dialog-lastpro_play" ).dialog( "destroy" );
							}
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

							//เมื่อปิด
							if($("#application_id").val()=="OPID300" || $("#application_id").val()=="OPPLI300"){
								alert("สำหรับการสมัครแบบใช้บัตรประชาชนแทนบัตรสมาชิกนั้น จะต้องบันทึกข้อมูลสมาชิกทันทีค่ะ");
								return false;
							}else{
							 	$("#dialog-lastpro_play" ).dialog( "destroy" );
							}
							
				
					} 
				},
				
				
	    			buttons: {
			

						"บันทึก":function(){ 
							//จะให้ทำไร
							if($("#application_id").val()=="OPMGMC300" || $("#application_id").val()=="OPMGMI300"){
								registersave();
							}else{
								chkmobile('online');
							}
									
						},
						"ข้าม":function(){ 
							//จะให้ทำไร
							if($("#application_id").val()=="OPID300" || $("#application_id").val()=="OPPLI300"){
								alert("สำหรับการสมัครแบบใช้บัตรประชาชนแทนบัตรสมาชิกนั้น จะต้องบันทึกข้อมูลสมาชิกทันทีค่ะ");
								return false;
							}else{
							 	$("#dialog-lastpro_play" ).dialog( "destroy" );
							}
									
						},
					 }
	    		});
	        	

	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ลงทะเบียนสมาชิกใหม่");
			    $("#lastpro_play").html(data);
	        	$("#id_card").focus();
				if($("#application_id").val()=="OPID300" || $("#application_id").val()=="OPPLI300"){
					document.getElementById('id_card').readOnly=true;
					$("#noid_type").val(5);
					$("#noid_remark").val($("#application_id").val());
					$("#mobile_no").focus();
					
					$(function() {
					$("#dialog-lastpro_play").dialog({
						closeOnEscape: false
					});
					});


				}
				

		}); 
	}
	
	
	//สมัครสมาชิกใหม่
	function registerfromafter(doc_no_diary){
	    $.get("/sales/newmember/registerfrom",{
	    	doc_no_diary:doc_no_diary,
	        ran:Math.random()},function(data){
	        	$("#dialog-lastpro_play").dialog({
	    			height: 850,width:'100%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

							//เมื่อปิด
						$("#dialog-lastpro_play" ).dialog( "destroy" );
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

							//เมื่อปิด
						$("#dialog-lastpro_play" ).dialog( "destroy" );
							
				
					} 
				},
				
				
	    			buttons: {
			

						"บันทึก":function(){ 
							//จะให้ทำไร
							//registersaveafter();
							chkmobile('after');
									
						},
						"ยกเลิก":function(){ 
							//จะให้ทำไร
							var cc=confirm("คุณต้องการยกเลิกใช่หรือไม่");
							if(!cc){
								return false;
							}
							$("#dialog-lastpro_play" ).dialog( "destroy" );
									
						},
					 }
	    		});
	        	

	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ลงทะเบียนสมาชิกใหม่");
			    $("#lastpro_play").html(data);
	        	$("#id_card").focus();

				if($("#application_id").val()=="OPID300" || $("#application_id").val()=="OPPLI300"){
					document.getElementById('id_card').readOnly=true;
					$("#noid_type").val(5);
					$("#noid_remark").val($("#application_id").val());
					$("#mobile_no").focus();
					
				}
		}); 
	}
	
	
	
	function preload(){
		/*$(function() {
			 $( "#from_preload" ).dialog({
			 modal: true,
			 });
			 });*/
		
		/*
    	$("#from_preload").dialog({
			height: 300,width:'30%',modal: true,resizable:true,closeOnEscape:true,
			open:function(){
    		
  			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  			$(this).dialog('widget')
            // find the title bar element
            .find('.ui-dialog-titlebar')
            // alter the css classes
            .removeClass('ui-corner-all')
            .addClass('ui-corner-top'); 
  			
            $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
            $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
            	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
            
            
			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
			//$(".ui-dialog-titlebar").hide();
		},
    	
	    close:function(evt,ui){

			if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
				//setTimeout(function(){canclelastpro(promo_code);},400);

					//เมื่อปิด
				$("#from_preload" ).dialog( "destroy" );
				
			}
			if(evt.originalEvent && $(evt.originalEvent.which==27)){

					//เมื่อปิด
				$("#from_preload" ).dialog( "destroy" );
					
		
			} 
		},
		
		
			
		});
    	
    	$("#from_preload").html("<p><center><img src='/sales/img/promotion/pleasewait1.gif'><br></>Please wait...</p></center>");
		*/
		 document.getElementById("xopen").click();
    	
		
	}
	
	function unpreload(){
		document.getElementById("xclose").click();
	}
	
	function chkmobile(xevent){
			var d = new Date();
			var n = d.getDate();
			var getyear = d.getFullYear();
			
			var id_card=$("#id_card").val();
			var month_apply=$("#apply_date").val();
			var arr_month=month_apply.substring(5,7);
			/*if(arr_month=="05" || arr_month=="06"){
				if(id_card.length==0){
					alert("ในช่วงเดือนพฤษภาคม - มิถุนายน 2557 ต้องมีรหัสบัตรประชาชนเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}
				
				if(!checkID(id_card)){
					alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
					$("#id_card").val('');
					return false;
				}
			}
			*/
			
			if($("#nation").val()==1){//คนไทย
				if(id_card!="000"){
					if(!check_number(id_card)){
						alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
						$("#id_card").focus();
						return false;
					}else if(id_card.length!=13){
						alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
						$("#id_card").focus();
						return false;
					}else if(!checkID(id_card)){
						alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
						$("#id_card").focus();
						return false;
					}
				}
				
				if($("#application_id").val()=="OPMGMC300"){
					if(!check_number(id_card)){
						alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
						$("#id_card").focus();
						return false;
					}else if(id_card!="" &&  id_card.length!=13){
						alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member รหัสบัตรประชาชนต้องเป็นตัวเลขครบ 13 หลักเท่านั้นค่ะ");
						$("#id_card").focus();
						return false;
					}else if(id_card!="" &&  !checkID(id_card)){
						alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
						$("#id_card").focus();
						return false;
					}
				}
			}
			
			
			
			var mobile_no=$("#mobile_no").val();
			if(mobile_no!="000"){
				if(!check_number(mobile_no)){
					alert("เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}else if(mobile_no.length!=10){
					alert("เบอร์มือถือไม่ครบ 10 หลักค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				
				if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
					alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
			}
			
			if($("#application_id").val()=="OPMGMC300"){
				if(!check_number(mobile_no)){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}else if(mobile_no.length!=10){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องครบ 10 หลักเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				
				if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
			}
			
			//check null
			var fname=$("#fname").val();
				chk_fname=fname.length;
				if(chk_fname<1){
					alert("กรุณาป้อนชื่อลูกค้า");
					$("#fname").focus();
					return false;
				}
				if(!check_name(fname)){
					alert("ชื่อลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ค่ะ");
					$("#fname").focus();
					return false;
				}
				
			var lname=$("#lname").val();
			chk_lname=lname.length;
			if(chk_lname<1){
				alert("กรุณาป้อนนามสกุลลูกค้า");
				$("#lname").focus();
				return false;
			}
			/*if(!check_name(lname)){
				alert("นามสกุลลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ค่ะ");
				$("#lname").focus();
				return false;
			}*/
			
			
			var hbd=$("#lyear").val() + "-" + $("#lmonth").val() + "-" + $("#lday").val();
			if(getyear-$("#lyear").val()<7 ){
				alert("อายุของสมาชิกน้อยกว่า 7 ขวบ กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
				return false;
			}
			if(getyear-$("#lyear").val()>100 ){
				alert("อายุของสมาชิกมากกว่า 100 ปี กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
				return false;
			}
			//chk ที่อยู่ตามบัตรประชาชน
			
			if($("#address2").val().length>0 && !check_number($("#address2").val())){
				alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
				$("#address2").focus();
				return false;
			}
			if($("#id_postcode").val().length>0 && !check_number($("#id_postcode").val())){
				alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
				$("#id_postcode").focus();
				return false;
			}
			
			//chk ที่อยู่ในการจัดส่งเอกสาร
	
			if($("#send_mu").val().length>0 && !check_number($("#send_mu").val())){
				alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
				$("#send_mu").focus();
				return false;
			}
			if($("#send_postcode").val().length>0 && !check_number($("#send_postcode").val())){
				alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
				$("#send_postcode").focus();
				return false;
			}		
			if($("#send_tel").val().length>0 && !check_number($("#send_tel").val())){
				alert("โทรศัพท์บ้าน กรอกได้เฉพราะตัวเลขค่ะ");
				$("#send_tel").focus();
				return false;
			}
			if($("#send_fax").val().length>0 && !check_number($("#send_fax").val())){
				alert("Fax กรอกได้เฉพราะตัวเลขค่ะ");
				$("#send_fax").focus();
				return false;
			}		
			
			
			if($("#status_read").val()!="AUTO"){
				if($("#noid_type").val()==""){
					alert("กรุณาระบุเหตุผลที่อ่านบัตร ปชช. ไม่ได้");
					return false;
				}
				if($("#noid_type").val()==5 && $("#noid_remark").val()==""){
					alert("โปรดระบุเหตุผลด้วยค่ะ");
					$("#noid_remark").focus();
					return false;
				}
			}
			
			//OPID300
			if($("#application_id").val()=="OPID300"){
				if($("#id_card_key").val()!=$("#id_card").val()){
					alert("บัตรประชาชนที่เสียบเข้ามาไม่ตรงกับของลูกค้าท่านนี้ค่ะ");
					return false;
				}
				
				if($("#status_read").val()!="AUTO"){
					//alert("สำหรับชุดสมัคร OPID300 บัตรประชาชนของสมาชิกต้องอ่านผ่านเครื่องอ่าน ID CARD ได้เท่านั้นค่ะ");
					//return false;
				}
			}		
		preload();
		 $.get("/sales/newmember/chkmobile",{
		      	    	doc_no:$("#doc_no").val(),
						member_id:$("#member_id").val(),
						apply_date:$("#apply_date").val(),
						expire_date:$("#expire_date").val(),
						branch_id:$("#branch_id").val(),
						
						mobile_no:$("#mobile_no").val(),
						id_card:$("#id_card").val(),
						mr:$("#mr").val(),
						fname:$("#fname").val(),
						lname:$("#lname").val(),
						mr_en:$("#mr_en").val(),
						fname_en:$("#fname_en").val(),
						lname_en:$("#lname_en").val(),
						nation:$("#nation").val(),
						address1:$("#address1").val(),
						address2:$("#address2").val(),
						address3:$("#id_tambon_id").val(),
						address4:$("#id_amphur_id").val(),
						address5:$("#id_province_id").val(),
						sex:$("#sex").val(),
						hbd:hbd,
						card_at:$("#card_at").val(),
						start_date:$("#start_date").val(),
						end_date:$("#end_date").val(),
						noid_type:$("#noid_type").val(),
						noid_remark:$("#noid_remark").val(),
						
						chk_copy_address:$("#chk_copy_address").val(),
						send_company:$("#send_company").val(),
						send_address:$("#send_address").val(),
						send_mu:$("#send_mu").val(),
						send_home_name:$("#send_home_name").val(),
						send_soi:$("#send_soi").val(),
						send_road:$("#send_road").val(),
						send_province_id:$("#send_province_id").val(),
						send_amphur_id:$("#send_amphur_id").val(),
						send_tambon_id:$("#send_tambon_id").val(),
						send_tel:$("#send_tel").val(),
						send_mobile:$("#send_mobile").val(),
						send_fax:$("#send_fax").val(),
						send_remark:$("#send_remark").val(),
						send_email:$("#send_email").val(),
						send_facebook:$("#send_facebook").val(),
						friend_id_card:$("#friend_id_card").val(),
						friend_mobile_no:$("#friend_mobile_no").val(),
		    	    	ran:Math.random()},function(data){
							//alert(data);			
							var arr=data.split("###");
		    	    		if(arr[0]=="CHKMOBILENO"){
								unpreload();
								fromchkmobile(data,xevent);
							}else{ //ไม่มีปัญหา
								//unpreload();
								if(xevent=="online"){
									registersave();
								}else{
									registersaveafter();	
								}
								
							}

	    }); 
			
	}
	
	
	//สมัครสมาชิกใหม่
	function search_newmember(field_search,var_search,table_search){
	    $.get("../../../download_promotion/id_card/search_newmember.php",{
	    	field_search:field_search,
			var_search:var_search,
			table_search:table_search,
	        ran:Math.random()},function(data){
		    	 $("#showlistnewmembersub").html(data);

			
		}); 
	}
	
	
	function fromchkmobile(xdata,xevent){
	    $.get("../../../download_promotion/id_card_newmem/fromchkmobile.php",{
	    	xdata:xdata,
	        ran:Math.random()},function(data){
	        	$("#fromchkmobile").dialog({
	    			height: 400,width:'60%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

							//เมื่อปิด
						$("#fromchkmobile" ).dialog( "destroy" );
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

							//เมื่อปิด
						$("#fromchkmobile" ).dialog( "destroy" );
							
				
					} 
				},
				
				
	    			buttons: {
			

						"ตกลง":function(){ 
							//จะให้ทำไร
							//registersaveafter();
							chkmobilechkotp($("#mobile_no").val(),$("#otp_code_input").val(),xevent);
									
						},
						"ลูกค้าไม่สะดวก":function(){ 
							//จะให้ทำไร
							//registersaveafter();
							$("#otp_code").val("abc");
							$("#fromchkmobile" ).dialog( "destroy" );
							
							if(xevent=="online"){
								registersave();
							}else{
								registersaveafter();	
							}			
						},						
						"ปิด":function(){ 
							//จะให้ทำไร
							$("#fromchkmobile" ).dialog( "destroy" );
									
						},
					 }
	    		});
	        	

	
			    $("#ui-dialog-title-fromchkmobile").html("Confrim Mobile Number");
			    $("#fromchkmobile").html(data);
	        	$("#otp_code_input").focus();


		}); 		
	}
	function chkmobilesendotp(mobile_no,otp_code,xevent){
				 $.get("../../../download_promotion/id_card_newmem/chkmobilesendotp.php",{
		      	    	mobile_no:mobile_no,
						otp_code:otp_code,
		    	    	ran:Math.random()},function(data){
							var arr=data.split("###");
		    	    		alert(arr[1]);	
							$("#otp_code_input").focus();

	    }); 

	}	
	function chkmobilechkotp(mobile_no,otp_code,xevent){
				 $.get("../../../download_promotion/id_card_newmem/chkmobilechkotp.php",{
		      	    	mobile_no:mobile_no,
						otp_code:otp_code,
		    	    	ran:Math.random()},function(data){
							var arr=data.split("###");
							
		    	    		if(arr[0]=="OK"){
								
								$("#mobile_dup").val($("#status_mobile_dup").val());
								$("#otp_code").val(otp_code);
								alert(arr[1]);
								$("#fromchkmobile" ).dialog( "destroy" );
								
								if(xevent=="online"){
									registersave();
								}else{
									registersaveafter();	
								}								
							}else{
								alert(arr[1]);	
								return false;
								
							}

	    }); 

	}
	
	function registersave() {
		var d = new Date();
		var n = d.getDate();
		var getyear = d.getFullYear();
		
		var id_card=$("#id_card").val();
		var month_apply=$("#apply_date").val();
		var arr_month=month_apply.substring(5,7);
		/*if(arr_month=="05" || arr_month=="06"){
			if(id_card.length==0){
				alert("ในช่วงเดือนพฤษภาคม - มิถุนายน 2557 ต้องมีรหัสบัตรประชาชนเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}
			
			if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").val('');
				return false;
			}
		}
		*/
		
		if($("#nation").val()==1){//คนไทย
			if(id_card!="000"){
				if(!check_number(id_card)){
					alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card.length!=13){
					alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
					$("#id_card").focus();
					return false;
				}else if(!checkID(id_card)){
					alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
					$("#id_card").focus();
					return false;
				}
			}
			
			if($("#application_id").val()=="OPMGMC300"){
				if(!check_number(id_card)){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card!="" &&  id_card.length!=13){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member รหัสบัตรประชาชนต้องเป็นตัวเลขครบ 13 หลักเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card!="" &&  !checkID(id_card)){
					alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
					$("#id_card").focus();
					return false;
				}
			}
		}
		
		
		
		var mobile_no=$("#mobile_no").val();
		if(mobile_no!="000"){
			if(!check_number(mobile_no)){
				alert("เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}else if(mobile_no.length!=10){
				alert("เบอร์มือถือไม่ครบ 10 หลักค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
				alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
		}
		
		if($("#application_id").val()=="OPMGMC300"){
			if(!check_number(mobile_no)){
				alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}else if(mobile_no.length!=10){
				alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องครบ 10 หลักเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
				alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
		}
		
		//check null
		var fname=$("#fname").val();
			chk_fname=fname.length;
			if(chk_fname<1){
				alert("กรุณาป้อนชื่อลูกค้า");
				$("#fname").focus();
				return false;
			}
			if(!check_name(fname)){
				alert("ชื่อลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ค่ะ");
				$("#fname").focus();
				return false;
			}
			
		var lname=$("#lname").val();
		chk_lname=lname.length;
		if(chk_lname<1){
			alert("กรุณาป้อนนามสกุลลูกค้า");
			$("#lname").focus();
			return false;
		}
		/*if(!check_name(lname)){
			alert("นามสกุลลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ค่ะ");
			$("#lname").focus();
			return false;
		}*/
		
		
		var hbd=$("#lyear").val() + "-" + $("#lmonth").val() + "-" + $("#lday").val();
		if(getyear-$("#lyear").val()<7 ){
			alert("อายุของสมาชิกน้อยกว่า 7 ขวบ กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
			return false;
		}
		if(getyear-$("#lyear").val()>100 ){
			alert("อายุของสมาชิกมากกว่า 100 ปี กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
			return false;
		}
		if($("#application_id").val()=="OPTRUE300" && getyear-$("#lyear").val()<15 ){
			alert("สำหรับชุดสมัครOPTRUE300สมาชิกต้องมีอายุ 15 ปี ขึ้นไป กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
			return false;
		}
		//chk ที่อยู่ตามบัตรประชาชน
		
		if($("#address2").val().length>0 && !check_number($("#address2").val())){
			alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#address2").focus();
			return false;
		}
		if($("#id_postcode").val().length>0 && !check_number($("#id_postcode").val())){
			alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#id_postcode").focus();
			return false;
		}
		
		//chk ที่อยู่ในการจัดส่งเอกสาร

		if($("#send_mu").val().length>0 && !check_number($("#send_mu").val())){
			alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_mu").focus();
			return false;
		}
		if($("#send_postcode").val().length>0 && !check_number($("#send_postcode").val())){
			alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_postcode").focus();
			return false;
		}		
		if($("#send_tel").val().length>0 && !check_number($("#send_tel").val())){
			alert("โทรศัพท์บ้าน กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_tel").focus();
			return false;
		}
		if($("#send_fax").val().length>0 && !check_number($("#send_fax").val())){
			alert("Fax กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_fax").focus();
			return false;
		}		
		
		
		if($("#status_read").val()!="AUTO"){
			if($("#noid_type").val()==""){
				alert("กรุณาระบุเหตุผลที่อ่านบัตร ปชช. ไม่ได้");
				return false;
			}
			if($("#noid_type").val()==5 && $("#noid_remark").val()==""){
				alert("โปรดระบุเหตุผลด้วยค่ะ");
				$("#noid_remark").focus();
				return false;
			}
		}
		
		//OPID300
		if($("#application_id").val()=="OPID300" || $("#application_id").val()=="OPPLI300"){
			if($("#id_card_key").val()!=$("#id_card").val()){
				alert("บัตรประชาชนที่เสียบเข้ามาไม่ตรงกับของลูกค้าท่านนี้ค่ะ");
				return false;
			}
			
			if($("#status_read").val()!="AUTO"){
				//alert("สำหรับชุดสมัคร OPID300 บัตรประชาชนของสมาชิกต้องอ่านผ่านเครื่องอ่าน ID CARD ได้เท่านั้นค่ะ");
				//return false;
			}
		}
		
		var c=confirm("คุณต้องการบันทึกข้อมูลสมาชิกใช่หรือไม่");
		if(!c){
			return false;
		}
		preload();
	    $.get("/sales/newmember/registersave",{
		      	    	doc_no:$("#doc_no").val(),
						member_id:$("#member_id").val(),
						apply_date:$("#apply_date").val(),
						expire_date:$("#expire_date").val(),
						branch_id:$("#branch_id").val(),
						
						mobile_no:$("#mobile_no").val(),
						id_card:$("#id_card").val(),
						mr:$("#mr").val(),
						fname:$("#fname").val(),
						lname:$("#lname").val(),
						mr_en:$("#mr_en").val(),
						fname_en:$("#fname_en").val(),
						lname_en:$("#lname_en").val(),
						nation:$("#nation").val(),
						address1:$("#address1").val(),
						address2:$("#address2").val(),
						address3:$("#id_tambon_id").val(),
						address4:$("#id_amphur_id").val(),
						address5:$("#id_province_id").val(),
						sex:$("#sex").val(),
						hbd:hbd,
						card_at:$("#card_at").val(),
						start_date:$("#start_date").val(),
						end_date:$("#end_date").val(),
						
						
						chk_copy_address:$("#chk_copy_address").val(),
						send_company:$("#send_company").val(),
						send_address:$("#send_address").val(),
						send_mu:$("#send_mu").val(),
						send_home_name:$("#send_home_name").val(),
						send_soi:$("#send_soi").val(),
						send_road:$("#send_road").val(),
						send_province_id:$("#send_province_id").val(),
						send_amphur_id:$("#send_amphur_id").val(),
						send_tambon_id:$("#send_tambon_id").val(),
						send_tel:$("#send_tel").val(),
						send_mobile:$("#send_mobile").val(),
						send_fax:$("#send_fax").val(),
						send_remark:$("#send_remark").val(),
						send_email:$("#send_email").val(),
						send_facebook:$("#send_facebook").val(),
						friend_id_card:$("#friend_id_card").val(),
						friend_mobile_no:$("#friend_mobile_no").val(),
						otp_code:$("#otp_code").val(),
						mobile_dup:$("#mobile_dup").val(),
		    	    	ran:Math.random()},function(data){
		    	    		if(data=="no_add"){
		    	    			unpreload();
		    	    			alert("ไม่สามารถบันทึกข้อมูลได้");
		    	    			return false;
							} else if(data=="dupemp"){
								unpreload();
		    	    			alert("รหัสบัตรประชาชนนี้ตรงกับบัตรประชาชนของพนักงาน กรุณาใส่บัตรประชาชนของลูกค้าค่ะ");
		    	    			return false;		
							} else if(data=="dupmember"){
								unpreload();
		    	    			alert("รหัสสมาชิกนี้ถูกบันทึกไปแล้ว ไม่สามารถบันทึกซ้ำได้ค่ะ");
		    	    			return false;									
							}else{
								unpreload();
								alert("บันทึกข้อมูลเรียบร้อย");
								
								$("#dialog-lastpro_play" ).dialog( "destroy" );
								//document.location.reload(true);
								//registercardfrom(data);
								
								
							}

	    }); 
		

	}
	
	
	

	function registersaveafter() {
		var d = new Date();
		var n = d.getDate();
		var getyear = d.getFullYear();
		
		var id_card=$("#id_card").val();
		var month_apply=$("#apply_date").val();
		var arr_month=month_apply.substring(5,7);
		/*if(arr_month=="05" || arr_month=="06"){
			if(id_card.length==0){
				alert("ในช่วงเดือนพฤษภาคม - มิถุนายน 2557 ต้องมีรหัสบัตรประชาชนเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}
			
			if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").val('');
				return false;
			}
		}
	   */

		if($("#nation").val()==1){//คนไทย
			if(id_card!="000"){
				if(!check_number(id_card)){
					alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card.length!=13){
					alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
					$("#id_card").focus();
					return false;
				}else if(!checkID(id_card)){
					alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
					$("#id_card").focus();
					return false;
				}
			}
			
			if($("#application_id").val()=="OPMGMC300"){
				if(!check_number(id_card)){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card!="" &&  id_card.length!=13){
					alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member รหัสบัตรประชาชนต้องครบ 13 หลักค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card!="" &&  !checkID(id_card)){
					alert('สำหรับโปรโมชั่นสมัครใหม่ Member get Member รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
					$("#id_card").focus();
					return false;
				}
			}
			
		}
	
		
		
		var mobile_no=$("#mobile_no").val();
		if(mobile_no!="000"){
			if(!check_number(mobile_no)){
				alert("เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}else if(mobile_no.length!=10){
				alert("เบอร์มือถือไม่ครบ 10 หลักค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			/*if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09') ){
				alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08 หรือ 09  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}*/
			
			if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
				alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
		}
		
		if($("#application_id").val()=="OPMGMC300"){
			if(!check_number(mobile_no)){
				alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}else if(mobile_no.length!=10){
				alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เเบอร์มือถือต้องครบ 10 หลักค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
				alert("สำหรับโปรโมชั่นสมัครใหม่ Member get Member เเบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
		}
		
		
		
		//check null
		var fname=$("#fname").val();
			chk_fname=fname.length;
			if(chk_fname<1){
				alert("กรุณาป้อนชื่อลูกค้า");
				$("#fname").focus();
				return false;
			}
			if(!check_name(fname)){
				alert("ชื่อลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ค่ะ");
				$("#fname").focus();
				return false;
			}
			
			
		var lname=$("#lname").val();
		chk_lname=lname.length;
		if(chk_lname<1){
			alert("กรุณาป้อนนามสกุลลูกค้า");
			$("#lname").focus();
			return false;
		}
		/*if(!check_name(lname)){
			alert("นามสกุลลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ค่ะ");
			$("#lname").focus();
			return false;
		}*/
		
		
		var hbd=$("#lyear").val() + "-" + $("#lmonth").val() + "-" + $("#lday").val();
		if(getyear-$("#lyear").val()<7 ){
			alert("อายุของสมาชิกน้อยกว่า 7 ขวบ กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
			return false;
		}
		if(getyear-$("#lyear").val()>100 ){
			alert("อายุของสมาชิกมากกว่า 100 ปี กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
			return false;
		}
		if($("#application_id").val()=="OPTRUE300" && getyear-$("#lyear").val()<15 ){
			alert("สำหรับชุดสมัครOPTRUE300สมาชิกต้องมีอายุ 15 ปี ขึ้นไป กรุณาเลือกปีเกิดของสมาชิกใหม่อีกครั้งค่ะ");
			return false;
		}		
		
		//chk ที่อยู่ตามบัตรประชาชน
	
		if($("#address2").val().length>0 && !check_number($("#address2").val())){
			alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#address2").focus();
			return false;
		}
		if($("#id_postcode").val().length>0 && !check_number($("#id_postcode").val())){
			alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#id_postcode").focus();
			return false;
		}
		
		//chk ที่อยู่ในการจัดส่งเอกสาร

		if($("#send_mu").val().length>0 && !check_number($("#send_mu").val())){
			alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_mu").focus();
			return false;
		}
		if($("#send_postcode").val().length>0 && !check_number($("#send_postcode").val())){
			alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_postcode").focus();
			return false;
		}		
		if($("#send_tel").val().length>0 && !check_number($("#send_tel").val())){
			alert("โทรศัพท์บ้าน กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_tel").focus();
			return false;
		}
		if($("#send_fax").val().length>0 && !check_number($("#send_fax").val())){
			alert("Fax กรอกได้เฉพราะตัวเลขค่ะ");
			$("#send_fax").focus();
			return false;
		}		
		
		

		
		if($("#status_read").val()!="AUTO"){
			if($("#noid_type").val()==""){
				alert("กรุณาระบุเหตุผลที่อ่านบัตร ปชช. ไม่ได้");
				return false;
			}
			if($("#noid_type").val()==5 && $("#noid_remark").val()==""){
				alert("โปรดระบุเหตุผลด้วยค่ะ");
				$("#noid_remark").focus();
				return false;
			}
		}
		
		
		//OPID300
		if($("#application_id").val()=="OPID300"  || $("#application_id").val()=="OPPLI300" ){
			if($("#id_card_key").val()!=$("#id_card").val()){
				alert("บัตรประชาชนที่เสียบเข้ามาไม่ตรงกับของลูกค้าท่านนี้ค่ะ");
				return false;
			}
			
			if($("#status_read").val()!="AUTO"){
				//alert("สำหรับชุดสมัคร OPID300 บัตรประชาชนของสมาชิกต้องอ่านผ่านเครื่องอ่าน ID CARD ได้เท่านั้นค่ะ");
				//return false;
			}
		}
		
		
		var c=confirm("คุณต้องการบันทึกข้อมูลสมาชิกใช่หรือไม่");
		if(!c){
			return false;
		}
		preload();
	    $.get("/sales/newmember/registersave",{
		      	    	doc_no:$("#doc_no").val(),
						member_id:$("#member_id").val(),
						apply_date:$("#apply_date").val(),
						expire_date:$("#expire_date").val(),
						branch_id:$("#branch_id").val(),
						
						mobile_no:$("#mobile_no").val(),
						id_card:$("#id_card").val(),
						mr:$("#mr").val(),
						fname:$("#fname").val(),
						lname:$("#lname").val(),
						mr_en:$("#mr_en").val(),
						fname_en:$("#fname_en").val(),
						lname_en:$("#lname_en").val(),
						nation:$("#nation").val(),
						address1:$("#address1").val(),
						address2:$("#address2").val(),
						address3:$("#id_tambon_id").val(),
						address4:$("#id_amphur_id").val(),
						address5:$("#id_province_id").val(),
						sex:$("#sex").val(),
						hbd:hbd,
						card_at:$("#card_at").val(),
						start_date:$("#start_date").val(),
						end_date:$("#end_date").val(),
						noid_type:$("#noid_type").val(),
						noid_remark:$("#noid_remark").val(),
						
						chk_copy_address:$("#chk_copy_address").val(),
						send_company:$("#send_company").val(),
						send_address:$("#send_address").val(),
						send_mu:$("#send_mu").val(),
						send_home_name:$("#send_home_name").val(),
						send_soi:$("#send_soi").val(),
						send_road:$("#send_road").val(),
						send_province_id:$("#send_province_id").val(),
						send_amphur_id:$("#send_amphur_id").val(),
						send_tambon_id:$("#send_tambon_id").val(),
						send_tel:$("#send_tel").val(),
						send_mobile:$("#send_mobile").val(),
						send_fax:$("#send_fax").val(),
						send_remark:$("#send_remark").val(),
						send_email:$("#send_email").val(),
						send_facebook:$("#send_facebook").val(),
						friend_id_card:$("#friend_id_card").val(),
						friend_mobile_no:$("#friend_mobile_no").val(),
						otp_code:$("#otp_code").val(),
						mobile_dup:$("#mobile_dup").val(),
		    	    	ran:Math.random()},function(data){
							
		    	    		if(data=="no_add"){
		    	    			alert("ไม่สามารถบันทึกข้อมูลได้");
		    	    			return false;
							} else if(data=="dupemp"){
		    	    			alert("รหัสบัตรประชาชนนี้ตรงกับบัตรประชาชนของพนักงาน กรุณาใส่บัตรประชาชนของลูกค้าค่ะ");
		    	    			return false;		
							} else if(data=="dupmember"){
								unpreload();
		    	    			alert("รหัสสมาชิกนี้ถูกบันทึกไปแล้ว ไม่สามารถบันทึกซ้ำได้ค่ะ");
		    	    			return false;						
							}else{
								alert("บันทึกข้อมูลเรียบร้อย");
								$("#dialog-lastpro_play" ).dialog( "destroy" );
								unpreload()
								//document.location.reload(true);
								//registercardfrom(data);
								var varday_hidden=$("#varday_hidden").val();
								listnewmembersub(varday_hidden);
								
							}

	    }); 
		

	}
	
	
	function showamphur() {
	    $.get("/sales/newmember/showamphur",{
	    				send_province_id:$("#send_province_id").val(),
		    	    	ran:Math.random()},function(data){
		    	    		$("#showsend_amphur").html(data);

	    }); 
	}	
	
	function showtambon() {
	    $.get("/sales/newmember/showtambon",{
	    				send_province_id:$("#send_province_id").val(),
	    				send_amphur_id:$("#send_amphur_id").val(),
		    	    	ran:Math.random()},function(data){
		    	    		$("#showsend_tambon").html(data);
		    	    		

	    }); 
	}		
	
	

	function idshowprovince(province_name,amphur_name,tambon_name) {
	    $.get("/sales/newmember/idshowprovince",{
						province_name:province_name,
						amphur_name:amphur_name,
						tambon_name:tambon_name,
		    	    	ran:Math.random()},function(data){
		    	    		$("#idshowprovince").html(data);
		    	    		idshowamphur(province_name,amphur_name,tambon_name);

	    }); 
	}	
	
	function idshowamphur(province_name,amphur_name,tambon_name) {
	    $.get("/sales/newmember/idshowamphur",{
						province_name:province_name,
						amphur_name:amphur_name,
						tambon_name:tambon_name,
		    	    	ran:Math.random()},function(data){
		    	    		$("#idshowamphur").html(data);
		    	    		idshowtambon(province_name,amphur_name,tambon_name)

	    }); 
	}	
	
	function idshowtambon(province_name,amphur_name,tambon_name) {
	    $.get("/sales/newmember/idshowtambon",{
						province_name:province_name,
						amphur_name:amphur_name,
						tambon_name:tambon_name,
		    	    	ran:Math.random()},function(data){
		    	    		$("#idshowtambon").html(data);
		    	    		findpostcode('id_postcode');
							
							if(document.getElementById("status_read").value=="AUTO"){
								document.getElementById("address1").readOnly = "true";	
								document.getElementById("address2").readOnly = "true";	
								document.getElementById("id_province_id").disabled = "disabled";	
								document.getElementById("id_amphur_id").disabled = "disabled";	
								document.getElementById("id_tambon_id").disabled = "disabled";	
								document.getElementById("id_postcode").readOnly = "true";	
							}

	    }); 
	}	
	
	function changeamphur() {//เมื่อเลือกอำเภอ
	    $.get("/sales/newmember/changeamphur",{
	    				id_province_id:$("#id_province_id").val(),
		    	    	ran:Math.random()},function(data){
		    	    		$("#idshowamphur").html(data);

	    }); 
	}	
	function changetambon() {//เมื่อเลือกตำบล
	    $.get("/sales/newmember/changetambon",{
	    				id_province_id:$("#id_province_id").val(),
	    				id_amphur_id:$("#id_amphur_id").val(),
		    	    	ran:Math.random()},function(data){
		    	    		$("#idshowtambon").html(data);
		    	    		findpostcode('id_postcode');
	    }); 
	}
	function findpostcode(div) {//หารหัสไปรษณีย์
		if(div=="id_postcode"){
			var id_province_id=$("#id_province_id").val();
			var id_amphur_id=$("#id_amphur_id").val();
			var id_tambon_id=$("#id_tambon_id").val();
		}else{
			var id_province_id=$("#send_province_id").val();
			var id_amphur_id=$("#send_amphur_id").val();
			var id_tambon_id=$("#send_tambon_id").val();
		}
	    $.get("/sales/newmember/findpostcode",{
	    				id_province_id:id_province_id,
	    				id_amphur_id:id_amphur_id,
	    				id_tambon_id:id_tambon_id,
		    	    	ran:Math.random()},function(data){
		    	    		$("#"+div).val(data);

	    }); 
	}	
	
	function listnewmembersub(varday) {//แสดงบินสมัครใหม่
		$("#varday_hidden").val(varday);
	    $.get("/sales/newmember/listnewmembersub",{
	    				varday:varday,
		    	    	ran:Math.random()},function(data){
		    	    		$("#showlistnewmembersub").html(data);

	    }); 
	}	
	
	function copyidcard(){
			$("#send_address").val($("#address1").val());
			$("#send_mu").val($("#address2").val());
			$("#send_province_id").val($("#id_province_id").val());
			$("#send_amphur_id").text('');
			$("#send_amphur_id").append('<option value='+$("#id_amphur_id").val()+'>'+$("#id_amphur_id option:selected").text()+'</option>');
			$("#send_tambon_id").text('');
			$("#send_tambon_id").append('<option value='+$("#id_tambon_id").val()+'>'+$("#id_tambon_id option:selected").text()+'</option>');
			$("#send_postcode").val($("#id_postcode").val());
	}
	
	
	function nextstep(member_no) {//หาไม่พบ profile  เนื่องจาก link down ในภาวะฉุกเฉิน
		var c=confirm("ไม่พบข้อมูลสมาชิกเนื่องจากระบบไม่ Online\nคุณสามรถทำการขายให้สมาชิกได้ แต่จะไม่สามารถแลกคะแนนได้\nคุณต้องการทำการขายต่อหรือไม่");
		if(!c){
			return false;
		}
	    $.get("/sales/promotion/nextstep",{
	    				member_no:member_no,
		    	    	ran:Math.random()},function(data){
		    	    		alert(data);

	    }); 
	}	
	
	function frominputidcard(application_id){
		$.get("../../../download_promotion/id_card/read.php",{
	        ran:Math.random()},function(data){
	        	$("#dialog-lastpro_play").dialog({
	    			height: 700,width:'70%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

							//เมื่อปิด
						//$("#dialog-lastpro_play" ).dialog( "destroy" );
						$("#dialog-lastpro_play" ).dialog('close');	
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

							//เมื่อปิด
						//$("#dialog-lastpro_play" ).dialog( "destroy" );
						$("#dialog-lastpro_play" ).dialog('close');		
				
					} 
				},
				
				
	    			buttons: {
			

						"ตรวจสอบสิทธิ์":function(){ 
							//จะให้ทำไร
							findnewmember();
									
						},
						"ยกเลิก":function(){ 
							//จะให้ทำไร
							var cc=confirm("คุณต้องการยกเลิกใช่หรือไม่");
							if(!cc){
								return false;
							}
							//$("#dialog-lastpro_play" ).dialog( "destroy" );
							$("#show_from_photo").html("");
							$("#dialog-lastpro_play").dialog('close');		
							$("#dialog-lastpro_play").dialog('destroy');
						},
					 }
	    		});
	        	

	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ตรวจสอบสิทธิ์การสมัครสมาชิกใหม่");
			    $("#lastpro_play").html(data);
			    //$('#ifrm').attr('src', "http://192.168.3.247/download_promotion/id_card/read.php")
			    $("#chk_application_id").val(application_id);
	        	$("#id_card").focus();
	        	
		});	
	}
	
	function findnewmember() {//ค้นหาสมาชิกใหม่
		var status_readcard=$("#status_readcard").val();
		if(status_readcard=="Manual"){
			alert("ต้องอ่านข้อมูลบัตรประชาชนจากเครื่องอ่านบัตรประชาชนค่ะ หากอ่านไม่ได้ต้องถ่ายรูปบัตรประชาชนของลูกค้าไว้ค่ะ");
			return false;
		}
		var application_id=$("#chk_application_id").val();
		if($("#chk_nation").val()=="thai"){
			var id_card=$("#id_card").val();
			var fname=$("#fname").val();
			var lname=$("#lname").val();
			var hbd=$("#hbd").val();
			if(id_card.length==0){
				alert("กรุณาป้อนรหัสบัตรประชาชนเพื่อใช้ในการค้นหาค่ะ");
				$("#id_card").focus();
				return false;
			}
			if(id_card!="2222222222222"){
				if(!checkID(id_card)){
			        alert('รูปแบบรหัสประชาชนไม่ถูกต้อง');
					$("#id_card").val('');
					return false;
				}
			}
			
			
			if(fname.length==0){
				alert("กรุณาป้อนชื่อลูกค้าเพื่อใช้ในการค้นหาค่ะ");
				$("#fname").focus();
				return false;
			}
			if(lname.length==0){
				alert("กรุณาป้อนนามสกุลลูกค้าเพื่อใช้ในการค้นหาค่ะ");
				$("#lname").focus();
				return false;
			}
			if($("#hbd_day").val()==1 && $("#hbd_month").val()==1 && $("#hbd_year").val()==1814){
				alert("กรุณาป้อนเลือกวันเกิดลูกค้าเพื่อใช้ในการค้นหาค่ะ");
				return false;
			}
			
			if(id_card.length!=0 && !check_number(id_card)){
				alert("กรอกได้เฉพราะตัวเลขค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=0 &&  id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}
			var arr=hbd.split("/");

		}
	    $.get("../../../download_promotion/id_card/findnewmember.php",{
	    				application_id:$("#chk_application_id").val(),
	    				id_card:id_card,
	    				chk_nation:$("#chk_nation").val(),
	    				fname:$("#fname").val(),
	    				lname:$("#lname").val(),
	    				hbd:$("#hbd").val(),
	    				hbd_day:$("#hbd_day").val(),
	    				hbd_month:$("#hbd_month").val(),
	    				hbd_year:$("#hbd_year").val(),
	    				num_snap:$("#num_snap").val(),
		    	    	ran:Math.random()},function(data){
		    	    		if(data=="No"){
		    	    			//alert("ลูกค้าท่านนี้สามารถสมัครสมาชิกใหม่ได้ค่ะ");
		    	    			getCatProduct(application_id,0,0,0,0,0); 
		    	    			$("#dialog-lastpro_play" ).dialog( "destroy" );
		    	    		}else{
		    	    			alert("ขออภัยค่ะ ในเดือนพฤศจิกายน 2557 สงวนสิทธิ์สมัครได้เฉพาะลูกค้าที่ยังไม่ได้ถือบัตรสมาชิก OPS มาก่อนค่ะ");
		    	    		}	

	    }); 
	}	
	
	
	function fromsnap(){
		var id_card=$("#id_card").val();
		if($("#id_card").val()==""){
	        alert('กรุณาใส่รหัสบัตรประชาชน ก่อนถ่ายรูปค่ะ');
			$("#id_card").val('');
			return false;
		}
		if(!checkID(id_card)){
	        alert('รูปแบบรหัสประชาชนไม่ถูกต้อง');
			$("#id_card").val('');
			return false;
		}
		/*$.get("../../../download_promotion/id_card_quick/from_snap.php",{
	        ran:Math.random()},function(data){
	        	$("#popup_snapx").dialog({
	    			height: 400,width:'45%',modal: true,resizable:true,closeOnEscape:true,autoOpen:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

							//เมื่อปิด
						//$("#popup_snapx" ).dialog('close');	
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

							//เมื่อปิด
						//$("#popup_snapx" ).dialog('close');	
						//$("#popup_snapx").dialog('destroy');
							
				
					} 

				
				},
				
				
	    			buttons: {
			

						"ตกลง":function(){ 
							//จะให้ทำไร
							webcamsave();
									
						},
					 }
	    		});
	        	

	
	        	//$("#popup_snapx").html("");
			    $("#popup_snapx").html(data);
				$( document ).ready(function() {
					

				});


			    

	        	
		});	
		*/
	}
	

	function webcamsave() {//ค้นหาสมาชิกใหม่
	
		var id_card=$("#id_card").val();
		var num_snap=$("#num_snap").val();
			num_snap=parseInt(num_snap,10)+1;
			$("#num_snap").val(num_snap);
			var path_img=id_card+"_snap"+num_snap+".jpg";
			$("#id_img").val(path_img);	
		
		var date_now = new Date();
		var year_now = date_now.getFullYear(); 	
		var year_month=date_now.getMonth(); 	
		var path_folder=year_now+year_month;
	    $.get("../../../download_promotion/id_card_quick/webcam_save.php",{
	    				id_card:id_card,
	    				num_snap:num_snap,
		    	    	ran:Math.random()},function(data){

		    	    		if(data==1){
		    	    			alert("ไม่สามารถถ่ายรูปได้");
		    	    			return false; 
		    	    		}else{
		    	    			$("#status_photo").val('Y');
								TINY.box.hide();
		    
		    	    			$("#show_photo").html("<img width='230px' height='180px' src='../../../download_promotion/id_card_quick/image_member/"+path_folder+"/"+id_card+"_snap"+num_snap+".jpg'></img>");
								
				
								

		    	    		}	
		    	    		
	    }); 
	}	
	
	function view_phto(){
			var date_now = new Date();
			var year_now = date_now.getFullYear(); 	
			

			month_now=('0' + (date_now.getMonth()+1)).slice(-2);

			var path_folder=year_now+month_now;
			var path_img='http://'+$("#ip_this").val()+'/download_promotion/id_card_quick/image_member/'+path_folder+'/'+$("#id_card").val()+"_snap"+$("#num_snap").val()+".jpg";
			//alert(path_img);
			alert("ถ่ายรูปเรียบร้อยแล้วค่ะ");

			$("#show_photo").html("<img width='230px' height='180px' src='"+path_img+"'></img>");
			
	}
	function showphotokey(member_no,doc_no) {//show photo for key
	    $.get("../../../download_promotion/id_card/showphotokey.php",{
	    				member_no:member_no,
	    				doc_no:doc_no,
		    	    	ran:Math.random()},function(data){
		    	
		    	        	$("#show_photo_key").dialog({
		    	    			height: 500,width:'50%',modal: false,resizable:true,closeOnEscape:true,
		    	    			open:function(){
		    		    		
		      	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
		      	    			$(this).dialog('widget')
		                        // find the title bar element
		                        .find('.ui-dialog-titlebar')
		                        // alter the css classes
		                        .removeClass('ui-corner-all')
		                        .addClass('ui-corner-top'); 
		      	    			
		                        $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
		                            "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
		                        $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
		                        	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
		                        
		                        
		    	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
		    	    			//$(".ui-dialog-titlebar").hide();
		    	    		},
		    		    	
		    			    close:function(evt,ui){

		    					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
		    						//setTimeout(function(){canclelastpro(promo_code);},400);

		    							//เมื่อปิด
		    						$("#show_photo_key" ).dialog('close');
		    						
		    					}
		    					if(evt.originalEvent && $(evt.originalEvent.which==27)){

		    							//เมื่อปิด
		    						$("#show_photo_key" ).dialog('close');
		    						
		    							
		    				
		    					} 
		    				},
		    				
		    				
		    	    			buttons: {
		    						"ยกเลิก":function(){ 
		    							//$("#show_photo_key" ).dialog( "destroy" );
		    							$("#show_photo_key" ).dialog('close');
		    						},
		    					 }
		    	    		});
		    	        	

		    	
		    	
		    			    $("#show_photo_key").html("<img width='500px' hieght='400px' src='/download_promotion/id_card/image_member/"+doc_no+"_snap1.jpg'>");
		    	        	//$("#show_photo_key").html(data);
	    }); 
	}	
	
	
	
	function fromreadidcard(id_card,member_no,status_no){
		/*if(member_no.substr(0,2)=="ID"){
			return false;
		}*/
		if(status_no=="04"){
			var stop_modal=false;
		}else{
			var stop_modal=false;
		}
		
		$.get("../../../download_promotion/id_card_quick/read.php",{
			member_no:member_no,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 730,width:'93%',modal: true,resizable:true,closeOnEscape:stop_modal,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
				
							
							"ตกลง":function(){ 
								//จะให้ทำไร

								changeprofile(member_no);
								
							},
							
							"ยกเลิก":function(){ 
								if(status_no!="04"){
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
									location.reload(); 
								}else{
									$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
									location.reload();
								}
							
							},
							
						 }
		    		});
			     	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				find_profile_old(member_no);
	        	$("#id_card").focus();
	       
		});
		
		
	}

function changeprofile(member_no){
	
	var id_card=$("#id_card").val();
	
	if(document.getElementById("nothai").checked==false){
		var status_nothai=1;
		if(!check_number(id_card)){
			alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
			$("#id_card").focus();
			return false;
		}else if(id_card.length!=13){
			alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
			$("#id_card").focus();
			return false;
		}else if(!checkID(id_card)){
			alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
			$("#id_card").focus();
			return false;
		}
	}else{
		var status_nothai=2;
		if(id_card==""){
			alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
			$("#id_card").focus();
			return false;
		}
	}
	
	
	
	var mobile_no_old=$("#mobile_no_old").val();
	var mobile_no_new=$("#mobile_no_new").val();
	if(document.getElementById("nothai").checked==false){
		
			if(mobile_no_new.length==0 && !check_number(mobile_no_old)){
				alert("เบอร์มือถือเดิมมีรูปแบบที่ไม่ถูกต้อง กรุณาขอเบอร์จากสมาชิกเพื่อ Update ให้ถูกต้องค่ะ");
				$("#mobile_no_new").focus();
				return false;
			}else if(mobile_no_new.length==0 && mobile_no_old.length!=10){
				alert("เบอร์มือถือเดิมไม่ครบ 10 หลัก กรุณาขอเบอร์จากสมาชิกเพื่อ Update ให้ถูกต้องค่ะ");
				$("#mobile_no_new").focus();
				return false;
			}
			
			if( (mobile_no_old.substring(0,2)!='08') &&  (mobile_no_old.substring(0,2)!='09') &&  (mobile_no_old.substring(0,2)!='06') ){
				if(mobile_no_new.length==0){
					alert("เบอร์มือถือเดิมต้องขึ้่นต้นด้วย 08,06 หรือ 09  เท่านั้น กรุณาขอเบอร์มือถือจากสมาชิกเพื่อ Update ให้ถูกต้องค่ะ");
					$("#mobile_no_new").focus();
					return false;
				}
			}
			
			
			
		if(mobile_no_new.length!=0){
			if(!check_number(mobile_no_new)){
				alert("เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no_new").focus();
				return false;
			}else if(mobile_no_new.length!=10){
				alert("เบอร์มือถือไม่ครบ 10 หลักค่ะ");
				$("#mobile_no_new").focus();
				return false;
			}
			
			if((mobile_no_new.substring(0,2)!='08') &&  (mobile_no_new.substring(0,2)!='09')  &&  (mobile_no_new.substring(0,2)!='06') ){
				alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no_new").focus();
				return false;
			}
				
	
				
		}
	
	}
	
	
	//check null
	var fname=$("#fname").val();
		chk_fname=fname.length;
		if(chk_fname<1){
			alert("กรุณาป้อนชื่อลูกค้า");
			$("#fname").focus();
			return false;
		}
		/*if(!check_name(fname)){
			alert("ชื่อลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ กรุณาแก้ไขให้ถูกต้องค่ะ");
			$("#fname").focus();
			return false;
		}*/
		
	var lname=$("#lname").val();
	chk_lname=lname.length;
	if(chk_lname<1){
		alert("กรุณาป้อนนามสกุลลูกค้า");
		$("#lname").focus();
		return false;
	}
	/*if(!check_name(lname)){
		alert("นามสกุลลูกค้ามี  ตัวเลข หรือ อักขระแปลกๆ ปะปนอยู่ กรุณาแก้ไขให้ถูกต้องค่ะ");
		$("#lname").focus();
		return false;
	}*/
	

	//chk ปีเกิด
	var year_hbd=document.getElementById("hbd_year").value;
	var date_now = new Date();
	var year_now = date_now.getFullYear(); 		
	var age=parseInt(year_now,10)-parseInt(year_hbd,10);
	if(age<7){
		alert("สมาชิกอายุน้อยกว่า 7 ขวบ กรุณาตรวจสอบปีเกิดอีกครั้ง");	
		return false;
	}else if(age>100){
		alert("สมาชิกอายุมากกว่า 100 ปี กรุณาตรวจสอบปีเกิดอีกครั้ง");	
		return false;
	}

	
	//chk ที่อยู่ในการจัดส่งเอกสาร
	if($("#send_mu").val().length>0 && !check_number($("#send_mu").val())){
		alert("หมู่ที่ กรอกได้เฉพราะตัวเลขค่ะ");
		$("#send_mu").focus();
		return false;
	}
	if($("#send_home_name").val().length>0 && !check_homename($("#send_home_name").val())){
		alert("ชื่อหมู่บ้านมี อักขระแปลกๆ ปะปนอยู่ค่ะ");
		$("#send_home_name").focus();
		return false;
	}
	if($("#send_soi").val().length>0 && !check_homename($("#send_soi").val())){
		alert("ซอยมี อักขระแปลกๆ ปะปนอยู่ค่ะ");
		$("#send_soi").focus();
		return false;
	}		
	if($("#send_road").val().length>0 && !check_homename($("#send_road").val())){
		alert("ถนนมี อักขระแปลกๆ ปะปนอยู่ค่ะ");
		$("#send_road").focus();
		return false;
	}		
	
	if($("#send_postcode").val().length>0 && !check_number($("#send_postcode").val())){
		alert("รหัสไปรษณีย์ กรอกได้เฉพราะตัวเลขค่ะ");
		$("#send_postcode").focus();
		return false;
	}	
	if($("#send_fax").val().length>0 && !check_number($("#send_fax").val())){
		alert("Fax กรอกได้เฉพราะตัวเลขค่ะ");
		$("#send_fax").focus();
		return false;
	}	


	if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()==""){
		alert("กรณีไม่สามารถอ่านบัตรประชาชนจากเครื่องอ่าน ID CARD ได้ ให้ถ่ายรูปบัตรประชาชนของสมาชิกก่อนค่ะ");	
		return false;
	}
	

	var d = new Date();
	var n = d.getDate();

	if($("#status_photo").val()!="" && $("#noid_type").val()==""){
		alert("กรุณาระบุเหตุผลที่อ่านบัตร ปชช. ไม่ได้");
		return false;
	}
	if($("#noid_type").val()==5 && $("#noid_remark").val()==""){
		alert("โปรดระบุเหตุผลด้วยค่ะ");
		$("#noid_remark").focus();
		return false;
	}
	
	
	
	if(document.getElementById("status_no_address").checked==false){
		if( $("#send_address").val()=="" || $("#send_province_id").val()==0 ){
			alert("กรุณใส่ที่อยู่ในการจัดส่งเอกสาร หากสมาชิกไม่ประสงค์ให้ที่อยู่ ให้ติ๊กถูกตรง สมาชิกไม่ให้ที่อยู่ ค่ะ");
			return false;
		}
	}
	
				
		var path_img=id_card+"_snap"+$("#num_snap").val()+".jpg";
		$("#id_img").val(path_img);							
		$.get("../../../download_promotion/id_card_quick/add_profile.php",{
		member_no:member_no,
		customer_id:$("#customer_id").val(),
		status_readcard:$("#status_readcard").val(),
		status_photo:$("#status_photo").val(),
		num_snap:$("#num_snap").val(),
		id_img:$("#id_img").val(),
		id_card:$("#id_card").val(),
		hbd_day:$("#hbd_day").val(),
		hbd_month:$("#hbd_month").val(),
		hbd_year:$("#hbd_year").val(),
		fname:$("#fname").val(),
		lname:$("#lname").val(),
		mobile_no_old:$("#mobile_no_old").val(),
		mobile_no_new:$("#mobile_no_new").val(),
		noid_type:$("#noid_type").val(),
		noid_remark:$("#noid_remark").val(),
		status_nothai:status_nothai,
		
		status_no_address:document.getElementById("status_no_address").checked,
		address:$("#address").val(),
		mu:$("#mu").val(),
		tambon_name:$("#tambon_name").val(),
		amphur_name:$("#amphur_name").val(),
		province_name:$("#province_name").val(),
		mr:$("#mr").val(),
		sex:$("#sex").val(),
		mr_en:$("#mr_en").val(),
		fname_en:$("#fname_en").val(),
		lname_en:$("#lname_en").val(),
		card_at:$("#card_at").val(),
		start_date:$("#start_date").val(),
		end_date:$("#end_date").val(),
		
		send_address:$("#send_address").val(),
		send_mu:$("#send_mu").val(),
		send_home_name:$("#send_home_name").val(),
		send_soi:$("#send_soi").val(),
		send_road:$("#send_road").val(),
		send_province_id:$("#send_province_id").val(),
		send_province_name:$("#send_province_id option:selected").text(),
		send_amphur_id:$("#send_amphur_id").val(),
		send_amphur_name:$("#send_amphur_id option:selected").text(),
		send_tambon_id:$("#send_tambon_id").val(),
		send_tambon_name:$("#send_tambon_id option:selected").text(),
		send_postcode:$("#send_postcode").val(),
		send_fax:$("#send_fax").val(),
		email_:$("#email_").val(),
		ran:Math.random()},function(data){
			if(data=="Offline"){
				alert("ระบบไม่สามารถเชื่อมต่อ Online ได้ กรุณาตรวจสอบสัญญาณ Internet ค่ะ");
				return false;
			}else if(data=="clear_tmp_error"){
				alert("ระบบไม่สามารถ Clear tmp ของคนก่อนหน้าได้ กรุณาทำใหม่อีกครั้ง");
				return false;
			}else if(data=="keep_befor_error"){
				alert("ระบบไม่สามารถเก็บข้อมูลก่อนแก้ไขได้ กรุณาทำใหม่อีกครั้ง");
				return false;
			}else if(data=="keep_after_error"){
				alert("ระบบไม่สามารถเก็บข้อมูลหลังแก้ไขได้ กรุณาทำใหม่อีกครั้ง");
				return false;
			}else if(data=="keep_coppy_error"){
				alert("ระบบไม่สามารถบันทึกข้อมูลได้ กรุณาทำใหม่อีกครั้ง");
				return false;
			}else if(data=="show_from_otp_code"){
				$("#dialog-lastpro_play" ).dialog('close');
				if($("#mobile_no_new").val()!=""){
					var mobile_otp=$("#mobile_no_new").val();
				}else{
					var mobile_otp=$("#mobile_no_old").val();		
				}
				from_otp($("#id_card").val(),$("#fname").val(),$("#lname").val(),member_no,mobile_otp,$("#customer_id").val(),$("#status_readcard").val(),document.getElementById("status_no_address").checked);
			}else if(data=="finish"){
				//function next pos
				$("#dialog-lastpro_play" ).dialog('close');
				callBackToDo();
			}
			
	}); 
			
			
	
		
		
		
		
}

	
	function from_otp(id_card,fname,lname,member_no,mobile_otp,customer_id,status_readcard,status_no_address){
		$.get("../../../download_promotion/id_card_quick/from_otp.php",{
			id_card:id_card,
			member_no:member_no,
			fname:fname,
			lname:lname,
			mobile_otp:mobile_otp,
			customer_id:customer_id,
			status_readcard:status_readcard,
			status_no_address:status_no_address,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 350,width:'70%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
						
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
							
						} 
					},
					
					
		    			buttons: {
				

							"Confirm":function(){ 
								//จะให้ทำไร
								from_otp_chk(id_card,member_no,mobile_otp,customer_id,status_readcard,status_no_address);
								
							},
							"ยกเลิก":function(){ 
								//จะให้ทำไร
								$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
								location.reload(); 
								
							},
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์ม Confrim OTP CODE");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				from_otp_gen(id_card,member_no,mobile_otp);
				$("#otp_confirm").focus();
	        	
	       
		});
		
		
	}
	
	
	function from_otp_gen(id_card,member_no,mobile_no){
		$.get("../../../download_promotion/id_card_quick/from_otp_gen.php",{
			id_card:id_card,
			member_no:member_no,
			mobile_no:mobile_no,		
	        ran:Math.random()},function(data){
				var arr=data.split("##");
				if(arr[0]=="OK"){
					$("#otp_code").html("<span style='color:#009900;'></span>");
				}else{
					$("#otp_code").html("<span style='color:#FF0000;'>ส่งไม่สำเร็จ</span>");	
				}
				
				$("#otp_confirm").focus();
		}); 
	}
	
	function from_otp_chk(id_card,member_no,mobile_no,customer_id,status_readcard,status_no_address){
		if($("#otp_confirm").val()==""){
			alert("กรุณใส่รหัส OTP CODE ด้วยค่ะ");
			return false;
		}
		$.get("../../../download_promotion/id_card_quick/from_otp_chk.php",{
			id_card:id_card,
			member_no:member_no,
			mobile_no:mobile_no,
			otp_confirm:$("#otp_confirm").val(),
			customer_id:customer_id,
			status_readcard:status_readcard,
			status_no_address:status_no_address,
	        ran:Math.random()},function(data){
				if(data=="keep_coppy_error"){
					alert("ระบบไม่สามารถบันทึกข้อมูลได้ กรุณาทำใหม่อีกครั้ง");
					return false;
				}
				var arr=data.split("##");
				if(arr[0]=="OK"){
					alert("Confirm OTP CODE สำเร็จ");
					$("#dialog-lastpro_play" ).dialog('close');	
					//function next pos
					callBackToDo(); 
				}else{
					alert("OTP CODE ไม่ถูกต้อง กรุณาป้อน OTP CODE ใหม่ค่ะ");	
					$("#otp_confirm").focus();
					return false;
				}
		}); 
	}	
	


//from id card  no check
function fromreadprofile(promo_code,status_otp,member_no,id_card,mobile_no,status_card,promo_des){
		if(promo_code=="OI02340417"){
			$.get("../../../download_promotion/id_card_quick/fromreadprofile_otp_chkplay.php",{
				promo_code:promo_code,
				member_no:member_no,
				id_card:id_card,
				mobile_no:mobile_no,
				status_card:status_card,
				ran:Math.random()},function(data){
				var arr=data.split("###");
				if(arr[0]!="Y"){					
					alert(arr[1]);
					$("#dialog-lastpro_play" ).dialog('close'); 
					location.reload(); 
					return false;
				}
			});	
		}
		
		if(status_card=="idcard"){
				fromreadprofile_idcard(promo_code,member_no,id_card,mobile_no,'idcard',promo_des);
				return false;
		}
		if(status_otp=="Y"){
			fromreadprofileotp(promo_code,status_otp,member_no,promo_des);
		}else{
			
			$.get("../../../download_promotion/id_card_quick/fromreadprofile.php",{
				promo_code:promo_code,
				member_no:member_no,
				ran:Math.random()},function(data){
	
	
						//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
						$("#dialog-lastpro_play").dialog({
							height: 700,width:'60%',modal: true,resizable:true,closeOnEscape:false,
							open:function(){
							
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
							$(this).dialog('widget')
							// find the title bar element
							.find('.ui-dialog-titlebar')
							// alter the css classes
							.removeClass('ui-corner-all')
							.addClass('ui-corner-top'); 
							
							$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
								"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
							$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
								"margin":"0 0 0 0","background-color":"#adb2ba"}); 
							
							
							$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
							
							//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
						},
						
						close:function(evt,ui){
		
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
								//setTimeout(function(){canclelastpro(promo_code);},400);
		
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							}
							if(evt.originalEvent && $(evt.originalEvent.which==27)){
		
									
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							} 
						},
						
						
							buttons: {
					
								
								"ตกลง":function(){ 
									//จะให้ทำไร
									fromreadprofile_save();
									
									
								},
								
								"ยกเลิก":function(){ 
				
										//จะให้ทำไร
										$("#dialog-lastpro_play" ).dialog('close'); 
										location.reload(); 
								
								
								},
								
							 }
						});
						
					$("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
					$("#lastpro_play").html(data);
					$("#dialog-lastpro_play").css("background-color","#FFFFFF");
					$("#content").css("background-color","#FFFFFF");
					
					
					$("#id_card").focus();
					$("#promo_code").val(promo_code);
					$("#promo_des").html(promo_des);

					
			   
			});
		
		
		}// if chk 30%
		
		
	}

function fromreadprofileotp(promo_code,status_otp,member_no,promo_des){
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_otp.php",{
			promo_code:promo_code,
			status_otp:status_otp,
			member_no:member_no,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 830,width:'85%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
				
							
							"OK":function(){ 
								//จะให้ทำไร
								fromreadprofile_otp_save();
								
								
							},
							
							"CANCEL":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
	        	$("#id_card").focus();
				$("#promo_code").val(promo_code);
				$("#promo_des").html(promo_des);				
				
				$("#member_no").val(member_no);
				var chk_ecoupon="Y";
				document.getElementById('chk_ecoupon').value='Y';
				
				//alert(promo_code.substring(0,3));
				if(promo_code!="OPNDTAC" && (promo_code=="OI02220216" || promo_code=="OPID300"  || promo_code=="OI03340516" || 
						promo_code=="OPGNC300"   || promo_code=="OPPGI300" || promo_code=="OPDTAC300"  || 
						promo_code=="OPKTC300"   || promo_code.substring(0,3)=="OPN" || promo_code=="OM13160416"  || 
						promo_code=="OI02280616" || promo_code=="OI02330516" || promo_code=="OI04090716" || 
						promo_code=="OI03100716" || promo_code=="OI03170716" || promo_code.substring(0,2)=="ON"  || 
						promo_code=="OI06220816"  || promo_code=="OI06230816"  || promo_code=="OI06240816"   || 
						promo_code=="OI02340417"  || promo_code=="OM13061216" || promo_code=="OX02460217" || 
						promo_code=="OX02460217_2" || promo_code=="BD001" || promo_code=="POINT50")){ 
					//ไม่ต้องตรวจสอบ coupon					
					chk_ecoupon="";
					document.getElementById('chk_ecoupon').value='';
				}
				
				if(chk_ecoupon!="Y"){
					document.getElementById('show_label_coupon').style.visibility='hidden';
					document.getElementById('otp_code').style.visibility='hidden';
				}
				
							
//			  if(promo_code=="OX02460217" || promo_code=="OX02460217_2" || 
//					  promo_code=="OX02250117" || promo_code=="TOUR01" || promo_code=="BD001"){
			  	 // โปรต่างชาติ ตรวจสอบ barcode
				  document.getElementById("nation1").checked = false;
				  document.getElementById("nation2").checked = true;
				    $('#label_id').html('Id Number');
				
				  document.getElementById("show_country").style.visibility="visible";
				  document.getElementById("country_code").value="KHM";
			 // }
		  
	       
		});
		
		
	}


function fromreadprofile_idcard(promo_code,member_no,id_card,mobile_no,status_card,promo_des){
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_idcard.php",{
			promo_code:promo_code,
			member_no:member_no,
			id_card:id_card,
			mobile_no:mobile_no,
			status_card:status_card,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 500,width:'50%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
				
							
							"ตกลง":function(){ 
								//จะให้ทำไร
								fromreadprofile_idcard_save();
								
								
							},
							
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มตรวจสอบคูปอง");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
	        	$("#id_card").focus();
				$("#promo_code").val(promo_code);
				$("#promo_des").html(promo_des);

				$("#member_no").val(member_no);
				$("#id_card").val(id_card);
				$("#mobile_no").val(mobile_no);
				$("#otp_code").focus();
				$("#chk_ecoupon").val("Y");
				if(promo_code=="OI02220216"){
					document.getElementById('show_text_coupon').style.visibility='hidden';
					document.getElementById('show_label_coupon').style.visibility='hidden';

					$("#show_msg").html("โปรโมชั่นนี้ไม่ต้องใส่รหัสคูปอง ให้กดปุ่มตกลงผ่านไปได้เลยค่ะ");
					
					$("#chk_ecoupon").val("N");
				}else if(promo_code=="OI03340516"){
					document.getElementById('show_text_coupon').style.visibility='hidden';
					document.getElementById('show_label_coupon').style.visibility='hidden';

					$("#show_msg").html("โปรโมชั่นนี้ไม่ต้องใส่รหัสคูปอง ให้กดปุ่มตกลงผ่านไปได้เลยค่ะ");
					
					$("#chk_ecoupon").val("N");
				}else if(promo_code=="OM13160416"){
					document.getElementById('show_text_coupon').style.visibility='hidden';
					document.getElementById('show_label_coupon').style.visibility='hidden';

					$("#show_msg").html("โปรโมชั่นนี้ไม่ต้องใส่รหัสคูปอง ให้กดปุ่มตกลงผ่านไปได้เลยค่ะ");
					
					$("#chk_ecoupon").val("N");
				}else if(promo_code=="OM13061216"){
					document.getElementById('show_text_coupon').style.visibility='hidden';
					document.getElementById('show_label_coupon').style.visibility='hidden';

					$("#show_msg").html("โปรโมชั่นนี้ไม่ต้องใส่รหัสคูปอง ให้กดปุ่มตกลงผ่านไปได้เลยค่ะ");
					
					$("#chk_ecoupon").val("N");					
				}else if(promo_code=="OI02280616"){
					document.getElementById('show_text_coupon').style.visibility='hidden';
					document.getElementById('show_label_coupon').style.visibility='hidden';

					$("#show_msg").html("โปรโมชั่นนี้ไม่ต้องใส่รหัสคูปอง ให้กดปุ่มตกลงผ่านไปได้เลยค่ะ");
					
					$("#chk_ecoupon").val("N");
				}else if(promo_code=="OI02330516" || promo_code=="OI04090716" || promo_code=="OI03100716" || promo_code=="OI03170716" || promo_code.substring(0,2)=="ON"  || promo_code=="OI06220816"  || promo_code=="OI06230816"  || promo_code=="OI06240816"  || promo_code=="OI02340417"){
					document.getElementById('show_text_coupon').style.visibility='hidden';
					document.getElementById('show_label_coupon').style.visibility='hidden';

					$("#show_msg").html("โปรโมชั่นนี้ไม่ต้องใส่รหัสคูปอง ให้กดปุ่มตกลงผ่านไปได้เลยค่ะ");
					
					$("#chk_ecoupon").val("N");
				}
	       
		});
}
	
function fromreadprofileother(promo_code,status_otp,member_no){
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_other.php",{
			promo_code:promo_code,
			status_otp:status_otp,
			member_no:member_no,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 800,width:'85%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
				
							
							"ตกลง":function(){ 
								//จะให้ทำไร
								fromreadprofile_other_save();
								
								
							},
							
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
	        	$("#id_card").focus();
				$("#promo_code").val(promo_code);
				//alert($("#promo_code").val());
				if(promo_code=="OI04140415"){
					$("#promo_des").html('Line07 ซื้อสินค้าชนิดใดก็ได้ภายในร้าน ซื้อ 1 แถม 1');
				}
				
				$("#member_no").val(member_no);
				if(promo_code=="OI04140415" ){
					var chk_ecoupon="Y";
					document.getElementById('chk_ecoupon').value='Y';
				}
				if(chk_ecoupon!="Y"){
					document.getElementById('show_label_coupon').style.visibility='hidden';
					document.getElementById('otp_code').style.visibility='hidden';
				}
				
				
	       
		});
		
		
	}
	
	
	
	
function fromreadprofile_sendotp(){
		var id_card=$("#id_card").val();
		if(document.getElementById("nation2").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
				
		
		var mobile_no=$("#mobile_no").val();
		if(document.getElementById("nation2").checked==false){
			
				if(!check_number(mobile_no)){
					alert("เบอร์มือถือต้องใส่เป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				if(mobile_no.length!=10){
					alert("เบอร์มือถือไม่ครบ 10 หลัก");
					$("#mobile_no").focus();
					return false;
				}
				
				if( (mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09') &&  (mobile_no.substring(0,2)!='06') ){
						alert("เบอร์มือถือต้องขึ้่นต้นด้วย 06,08,09 เท่านั้น");
						$("#mobile_no").focus();
						return false;
					
				}
		
		}

	
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_sendotp.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:$("#otp_code").val(),
			val_otp:$("#val_otp").val(),
			member_no:$("#member_no").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
			confirm_mobile:$("#confirm_mobile").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				
				if(arrdata[0]=="MOBILEDIFF"){
					//alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					var confirm_mobile=confirm("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก หากยืนยันว่าเบอร์นี้เป็นของสมาชิกจริง ให้กด OK ค่ะ");
					if(!confirm_mobile){
						return false;
					}else{
						$("#confirm_mobile").val("Y");	
						$("#show_status_confirm_mobile").html("(ยืนยันเปลี่ยนเบอร์มือถือ)");
						document.getElementById("mobile_no").readOnly = "true";
						fromreadprofile_sendotp();
						return false;
						//fromreadprofile_otp_save();
					}
				}
				
				
				if(arrdata[0]=="IDCARD_PLAY"){
					alert("รหัสบัตรประชาชนนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILE_PLAY"){
					alert("เบอร์มือถือนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="IDCARDDIFF"){
					alert("รหัส ปชช. ที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนรหัสบัตร ปชช. ก่อนค่ะ");
					return false;					
				} else if(arrdata[0]=="NOPROFILE"){
					alert("ไม่พบฐานข้อมูลสมาชิกในระบบ");
					return false;
				} else if(arrdata[0]=="ERROR"){
					alert(arrdata[1]);
					return false;					
				} else {
					alert(arrdata[1]);
					$("#val_otp").focus();
				}

		}); 
	}	
	



function fromreadprofile_sendotp_other(){
		var id_card=$("#id_card").val();
		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		
		
		

		var otp_code=$("#id1").val()+$("#id2").val()+$("#id3").val()+$("#id4").val();
			if(!check_number(otp_code)){
				alert("รหัส Club Card ID ต้องป็นตัวเลขเท่านั้นค่ะ");
				$("#otp_code").focus();
				return false;
			}
			if(otp_code.length<18){
				alert("รหัส Club Card ID ต้องมากกว่า 18 หลักค่ะ");
				$("#otp_code").focus();
				return false;
			}
		
		
			if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
						alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
						return false;
			}
			
			
		
			if($("#status_readcard").val()=="MANUAL"){
				var mobile_no=$("#mobile_no").val();
		
			
				if(!check_number(mobile_no)){
					alert("เบอร์มือถือต้องใส่เป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				if(mobile_no.length!=10){
					alert("เบอร์มือถือไม่ครบ 10 หลัก");
					$("#mobile_no").focus();
					return false;
				}
				
				if( (mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09') &&  (mobile_no.substring(0,2)!='06') ){
						alert("เบอร์มือถือต้องขึ้่นต้นด้วย 06,08,09 เท่านั้น");
						$("#mobile_no").focus();
						return false;
					
				}
				
				
		
			}



			
			
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_sendotp_other.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:otp_code,
			val_otp:$("#val_otp").val(),
			member_no:$("#member_no").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				if(arrdata[0]=="IDCARD_PLAY"){
					alert("รหัสบัตรประชาชนนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILE_PLAY"){
					alert("เบอร์มือถือนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILEDIFF"){
					alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					return false;
				} else if(arrdata[0]=="NOPROFILE"){
					alert("ไม่พบฐานข้อมูลสมาชิกในระบบ");
					return false;
				} else {
					alert(arrdata[1]);
					$("#val_otp").focus();
				}

		}); 
	}	
	
	
	
	
	
	function fromreadprofile_save(){
		var id_card=$("#id_card").val();

		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		
		
		var otp_code=$("#otp_code").val();
		if(otp_code.length==0){
					alert("ต้องใส่ รหัส E-coupon ก่อนค่ะ");
					$("#otp_code").focus();
					return false;
		}


		if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
					alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
					return false;
		}
		
		
	
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_save.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:$("#otp_code").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
			member_no:$("#member_no").val(),
	        ran:Math.random()},function(data){
				//alert(data);
				var arrdata=data.split("###");
				if(arrdata[0]=="IDCARD_PLAY"){
					alert("รหัสบัตรประชาชนนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILE_PLAY"){
					alert("เบอร์มือถือนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILEDIFF"){
					alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					return false;
				} else if(arrdata[0]=="IDCARDDIFF"){
					alert("รหัสบัตร ปชช. ที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					return false;
				} else if(arrdata[0]=="NOPROFILE"){
					alert("ไม่พบฐานข้อมูลสมาชิกในระบบ");
					return false;	
				}else if(arrdata[0]=="Y"){
					$("#dialog-lastpro_play" ).dialog('close');	
					//function next pos
					//callBackToDo();  
					if($("#promo_code").val()=="OPPLC300"){
						lineApp2('OPPLC300',0,0,0,0,0,id_card,$("#otp_code").val());						
					}else if( $("#promo_code").val()=="OK03130915" || $("#promo_code").val()=="OK04080915"  || 
							$("#promo_code").val()=="OK03060915"  || $("#promo_code").val()=="OK03070915"   || 
							$("#promo_code").val()=="OK03020116" || $("#promo_code").val()=="OK04010216" || 
							$("#promo_code").val()=="OK16050215" || $("#promo_code").val()=="OK03020216"  || 
							$("#promo_code").val()=="OK04240216"  || $("#promo_code").val()=="OK06230216" || 
							$("#promo_code").val().substring(0,2)=="ON"){
						callBackToDo2($("#promo_code").val(),'Y','OK',id_card,$("#otp_code").val(),""); 
					}else if($("#promo_code").val()=="OPPLI300"){
						ccsregisterfrom($("#promo_code").val(),$("#otp_code").val());
						//callBackIdCard($("#promo_code").val(),id_card) ;
					}else{
						lineApp($("#promo_code").val(),'Y','OK',id_card,$("#otp_code").val());
					}
				}else{
					alert(arrdata[1]);		
				}				

				
			
		}); 
	}	

	function checkemail(str){
		var emailFilter=/^.+@.+\..{2,3}$/;
		if (!(emailFilter.test(str))) { 
			   return "N";
			   return false;
		}
	    return "Y";
	}
	
	function fromreadprofile_otp_save(){
			var id_card=$("#id_card").val();
	
			if(document.getElementById("nation2").checked==false){
				var status_nothai=1;
				if(!check_number(id_card)){
					alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
					$("#id_card").focus();
					return false;
				}else if(id_card.length!=13){
					alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
					$("#id_card").focus();
					return false;
				}else if(!checkID(id_card)){
					alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
					$("#id_card").focus();
					return false;
				}
			}else{
				var status_nothai=2;
				if(id_card==""){
					alert('Please input ID number.');
					$("#id_card").focus();
					return false;
				}
				
				if($("#promo_code").val()=="OX02460217"  && id_card!="" && id_card.length==13){
					alert('โปรนี้สำหรับชาวต่างชาติเท่านั้น เลขที่ Passport ที่ป้อนเข้ามาน่าจะเป็นของชาวไทย เพราะมี 13 หลักค่ะ');
					$("#id_card").focus();
					return false;
				}
				if($("#promo_code").val()=="OX02460217_2"  && id_card!="" && id_card.length==13){
					alert('โปรนี้สำหรับชาวต่างชาติเท่านั้น เลขที่ Passport ที่ป้อนเข้ามาน่าจะเป็นของชาวไทย เพราะมี 13 หลักค่ะ');
					$("#id_card").focus();
					return false;
				}		
				if($("#promo_code").val()=="OX02250117"  && id_card!="" && id_card.length==13){
					alert('โปรนี้สำหรับชาวต่างชาติเท่านั้น เลขที่ Passport ที่ป้อนเข้ามาน่าจะเป็นของชาวไทย เพราะมี 13 หลักค่ะ');
					$("#id_card").focus();
					return false;
				}	
				if($("#country_code").val()==""){
					alert('กรณีเป็นชาวต่างชาติต้องเลือกประเทศด้วยค่ะ');
					$("#id_card").focus();
					return false;
				}
					
			}
			
			
			
			if($("#chk_ecoupon").val()=="Y"){
				var otp_code=$("#otp_code").val();
				if(otp_code.length==0){
							alert("ต้องใส่ รหัส E-coupon ก่อนค่ะ");
							$("#otp_code").focus();
							return false;
				}			
			}
			
	
			if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
						alert("Please Photographing.");
						return false;
			}
			
			if($("#status_readcard").val()=="AUTO" && $("#promo_code").val()=="OPPLI300"){
				var val_otp=$("#val_otp").val();
				if(val_otp.length==0){
							alert("Please OTP Code Number.");
							$("#val_otp").focus();
							return false;
				}	
			}
			
			
			if($("#status_readcard").val()=="MANUAL"){
				var val_otp=$("#val_otp").val();
				if(val_otp.length==0){
							alert("Please OTP Code Number.");
							$("#val_otp").focus();
							return false;
				}	
			}
			
			if($("#email").val()!="" && checkemail($("#email").val())=="N"){
				alert("Invalid Email Format.");
				$("#email").focus();
				return false;
			}
			
			var dto_pos=$("#country_code").val()+"#"+$("#email").val();
			$.get("../../../download_promotion/id_card_quick/fromreadprofile_otp_save.php",{
				status_readcard:$("#status_readcard").val(),
				status_photo:$("#status_photo").val(),
				num_snap:$("#num_snap").val(),
				id_img:$("#id_img").val(),
				ip_this:$("#ip_this").val(),
				address:$("#address").val(),
				mu:$("#mu").val(),
				tambon_name:$("#tambon_name").val(),
				amphur_name:$("#amphur_name").val(),
				province_name:$("#province_name").val(),
				mr:$("#mr").val(),
				sex:$("#sex").val(),
				mr_en:$("#mr_en").val(),
				fname_en:$("#fname_en").val(),
				lname_en:$("#lname_en").val(),
				card_at:$("#card_at").val(),
				start_date:$("#start_date").val(),
				end_date:$("#end_date").val(),
				promo_code:$("#promo_code").val(),
				id_card:$("#id_card").val(),
				fname:$("#fname").val(),
				lname:$("#lname").val(),
				mobile_no:$("#mobile_no").val(),
				birthday:$("#birthday").val(),
				otp_code:$("#otp_code").val(),
				val_otp:$("#val_otp").val(),
				chk_ecoupon:$("#chk_ecoupon").val(),
				member_no:$("#member_no").val(),
				confirm_mobile:$("#confirm_mobile").val(),
		        ran:Math.random()},function(data){
					//alert(data);
					
					var arrdata=data.split("###");
					
					if(arrdata[0]=="MOBILEDIFF"){
						//alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
						var confirm_mobile=confirm("A mobile phone number that was entered does not match the database members come.\n If it is confirmed that of the actual members, press OK.");
						if(!confirm_mobile){
							return false;
						}else{
							$("#confirm_mobile").val("Y");	
							$("#show_status_confirm_mobile").html("(Confirm change of mobile number)");
							document.getElementById("mobile_no").readOnly = "true";
							fromreadprofile_otp_save();
							return false;
						}
					}
						
						
					if(arrdata[0]=="IDCARD_PLAY"){
						alert("รหัสบัตรประชาชนนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
						return false;
					} else if(arrdata[0]=="MOBILE_PLAY"){
						alert("เบอร์มือถือนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
						return false;
					} else if(arrdata[0]=="IDCARDDIFF"){
						alert("รหัสบัตร ปชช. ที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนรหัสบัตร ปชช. ก่อนค่ะ");
						return false;
					} else if(arrdata[0]=="NOPROFILE"){
						alert("ไม่พบฐานข้อมูลสมาชิกในระบบ");
						return false;
					} else if(arrdata[0]=="ERROR"){
						alert(arrdata[1]);
						return false;
					} else if(arrdata[0]=="Y"){
						$("#dialog-lastpro_play" ).dialog('close');	
						//function next pos
						//callBackToDo();  
						if($("#promo_code").val()=="OPPL300"){
							lineApp2('OPPL300',0,0,0,0,0,id_card,$("#otp_code").val()); 
						}else if($("#promo_code").val()=="OK01160115" || $("#promo_code").val()=="OI27150115"  || 
								$("#promo_code").val()=="OK16050215"  || $("#promo_code").val()=="OK04061215" ||  
								$("#promo_code").val()=="OI07200216"    || $("#promo_code").val()=="OK04070315" || 
								$("#promo_code").val()=="OK27110315" || $("#promo_code").val()=="OK27010415"  || 
								$("#promo_code").val()=="OK18240415" || $("#promo_code").val()=="OI02110615"  || 
								$("#promo_code").val()=="OK03280815"  || $("#promo_code").val()=="OK03290815" || 
								$("#promo_code").val()=="OK04300815" || $("#promo_code").val()=="OK03060915" || 
								$("#promo_code").val()=="OK03070915"   || $("#promo_code").val()=="OK03100915"    || 
								$("#promo_code").val()=="OK03090915"   || $("#promo_code").val()=="OK04110915"   || 
								$("#promo_code").val()=="OK04080915"   || $("#promo_code").val()=="OK04140915"  || 
								$("#promo_code").val()=="OK03120915" || $("#promo_code").val()=="OK03130915" || 
								$("#promo_code").val()=="OK04080915"  || $("#promo_code").val()=="OK03060915"  || 
								$("#promo_code").val()=="OK03070915"   || $("#promo_code").val()=="OK03020116" || 
								$("#promo_code").val()=="OK04010216" || $("#promo_code").val()=="OK16050215" || 
								$("#promo_code").val()=="OK03020216"  || $("#promo_code").val()=="OK04240216"  || 
								$("#promo_code").val()=="OK06230216" || $("#promo_code").val().substring(0,2)=="OK"  || 
								$("#promo_code").val().substring(0,2)=="OI" || $("#promo_code").val()=="OM13160416"  || 
								$("#promo_code").val()=="OX060280516"  || $("#promo_code").val()=="OI02280616"  || 
								$("#promo_code").val()=="OM13061216"  || $("#promo_code").val().substring(0,2)=="ON"  || 
								$("#promo_code").val()=="OC04250816"  || $("#promo_code").val()=="OX02460217" || 
								$("#promo_code").val()=="OX02460217_2" || $("#promo_code").val()=="OX02250117" || 
								$("#promo_code").val()=="TOUR01"){
							callBackToDo2($("#promo_code").val(),'Y','OK',id_card,$("#otp_code").val(),dto_pos); 
						}else if($("#promo_code").val()=="OPPLI300"){
							//ccsregisterfrom($("#promo_code").val(),$("#otp_code").val());
							$("#csh_mobile_no").val($("#mobile_no").val());
							callBackIdCard($("#promo_code").val(),$("#id_card").val(),$("#otp_code").val());
						}else if( $("#promo_code").val()=="OPID300" || $("#promo_code").val()=="OPGNC300"  || 
								$("#promo_code").val()=="OPPGI300" || $("#promo_code").val()=="OPDTAC300" || 
								$("#promo_code").val()=="OPKTC300"  || $("#promo_code").val()=="OPTRUE300"  || 
								$("#promo_code").val().substring(0,3)=="OPN" ){
							$("#csh_mobile_no").val($("#mobile_no").val());
							callBackIdCard($("#promo_code").val(),$("#id_card").val(),$("#otp_code").val());
						}else if($("#promo_code").val()=="BD001" || $("#promo_code").val()=="POINT50"){
							callBackToDo(); 
						}else{
							lineApp($("#promo_code").val(),'Y','OK',id_card,$("#otp_code").val());
						}
					}else{
						alert(arrdata[1]);		
					}		
				
			}); 
	}		
	
	
	function fromreadprofile_other_save(){
		var id_card=$("#id_card").val();

		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
				

		var otp_code=$("#id1").val()+$("#id2").val()+$("#id3").val()+$("#id4").val();
			if(!check_number(otp_code)){
				alert("รหัส Club Card ID ต้องป็นตัวเลขเท่านั้นค่ะ");
				$("#otp_code").focus();
				return false;
			}
			if(otp_code.length<18){
				alert("รหัส Club Card ID ต้องมากกว่า 18 หลักค่ะ");
				$("#otp_code").focus();
				return false;
			}	

			if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
						alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
						return false;
			}
			
		
			if($("#status_readcard").val()=="MANUAL"){
				var mobile_no=$("#mobile_no").val();
		
			
				if(!check_number(mobile_no)){
					alert("เบอร์มือถือต้องใส่เป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				if(mobile_no.length!=10){
					alert("เบอร์มือถือไม่ครบ 10 หลัก");
					$("#mobile_no").focus();
					return false;
				}
				
				if( (mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09') &&  (mobile_no.substring(0,2)!='06') ){
						alert("เบอร์มือถือต้องขึ้่นต้นด้วย 06,08,09 เท่านั้น");
						$("#mobile_no").focus();
						return false;
					
				}
				
				if($("#status_readcard").val()=="MANUAL"){
					var val_otp=$("#val_otp").val();
					if(val_otp.length==0){
								alert("ต้องใส่ รหัส OTP CODE ก่อนค่ะ");
								$("#val_otp").focus();
								return false;
					}	
				}
				
		
			}
		

		
	
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_other_save.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:otp_code,
			val_otp:$("#val_otp").val(),
			member_no:$("#member_no").val(),
	        ran:Math.random()},function(data){
				//alert(data);
				var arrdata=data.split("###");
					
				if(arrdata[0]=="Y"){
					$("#dialog-lastpro_play" ).dialog('close');	
					//function next pos
					//callBackToDo();  
					callBackToDo3($("#promo_code").val(),'Y','OK',id_card,otp_code,$("#mobile_no").val());
				}else{
					alert(arrdata[1]);		
				}				

				
			
		}); 
	}//func		
	
	
	function fromreadprofile_idcard_save(){

			if($("#otp_code").val().length==0 && $("#chk_ecoupon").val()=="Y"){
				alert("ต้องใส่ รหัส E-coupon ก่อนค่ะ");
				$("#otp_code").focus();
				return false;
			}	
		
		
		
	
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_idcard_save.php",{
			id_card:$("#id_card").val(),
			member_no:$("#member_no").val(),
			mobile_no:$("#mobile_no").val(),
			promo_code:$("#promo_code").val(),
			otp_code:$("#otp_code").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
	        ran:Math.random()},function(data){
				//alert(data);
				var arrdata=data.split("###");

				if(arrdata[0]=="Y"){
					$("#dialog-lastpro_play" ).dialog('close');	
					//function next pos
					//callBackToDo();  
					callBackToDo2($("#promo_code").val(),'Y','OK',$("#id_card").val(),$("#otp_code").val(),""); 
				}else{
					alert(arrdata[1]);		
				}				

				
			
		}); 
	}	
	
	
//ccs start=========================================================================================================================
function ccschangecardfrom(member_no){
		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 700,width:'75%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {

							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ขั้นตอนลงทะเบียนแจ้งใช้บัตรประชาชนแทนบัตรสมาชิก");
				 //$(".ui-widget-header").css("background-image","none");
				 //$(".ui-widget-header").css("background-color","#f49f1e");
				 //$(".ui-widget-header").css("color","#144055");

				 
			    $("#lastpro_play").html("<iframe id='ccs_changecard_from' src='../../../download_promotion/ccs/ccs_changecard_from.php' width='95%' height='95%' style='border:0'>");
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
		
		
	}
	
	

function m2mfromcheck(promo_code) {//ตรวจสอบว่าสมัครได้ไหมสมาชิกใหม่
	
	$.get("../../../download_promotion/id_card_m2m/from_check.php",{
				ran:Math.random()},function(data){
					$("#dialog-lastpro_play").dialog({
						height: 800,width:'55%',modal: true,resizable:true,closeOnEscape:true,position:'center',
						open:function(){
						
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
						$(this).dialog('widget')
						// find the title bar element
						.find('.ui-dialog-titlebar')
						// alter the css classes
						.removeClass('ui-corner-all')
						.addClass('ui-corner-top'); 
						
						$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
							"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
						$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
							"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
						
						
						$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						//$(".ui-dialog-titlebar").hide();
					},
					
					close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
							//$("#dialog-lastpro_play" ).dialog( "destroy" );
							$("#dialog-lastpro_play" ).dialog('close');	
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								//เมื่อปิด
							//$("#dialog-lastpro_play" ).dialog( "destroy" );
							$("#dialog-lastpro_play" ).dialog('close');		
					
						} 
					},
					
					
						buttons: {
				
							"ยกเลิก":function(){ 
								//จะให้ทำไร
								var cc=confirm("คุณต้องการยกเลิกใช่หรือไม่");
								if(!cc){
									return false;
								}
								//จะให้ทำไร
								$("#dialog-lastpro_play" ).dialog('close'); 
								location.reload(); 
							},
						 }
					});
					
	
		
					$("#ui-dialog-title-dialog-lastpro_play").html("ตรวจสอบสิทธิ์การสมัครสมาชิกใหม่");
					$("#lastpro_play").html(data);
					$("#promo_code").val(promo_code);
					//$('#ifrm').attr('src', "http://192.168.3.247/download_promotion/id_card/read.php")			
	});	
	
}	
	
function m2mfromcheck_ans() {//ค้นหาสมาชิกใหม่
							$("#show_limit").html("");
							$("#show_ans").html("");
							$("#show_button").html("");

	if($("#coupon_code").val()==""){
		alert("กรุณาป้อนรหัส Coupon Code ด้วยค่ะ");
		return false;
	}
	var id_card=$("#id_card").val();
	if(id_card.length==0){
		alert("กรุณาป้อนรหัสบัตรประชาชนเพื่อใช้ในการค้นหาค่ะ");
		$("#id_card").focus();
		return false;
	}

	if(!checkID(id_card)){
		alert('รูปแบบรหัสประชาชนไม่ถูกต้อง');
		$("#id_card").val('');
		return false;
	}
$.get("../../../download_promotion/id_card_m2m/m2mfromcheck_ans.php",{
						coupon_code:$("#coupon_code").val(),
						id_card:$("#id_card").val(),
		    	    	ran:Math.random()},function(data){
							

							
							var arr=data.split("###");
							

							

							
							
							$("#show_limit").html(arr[2]);
							$("#show_ans").html(arr[1]);
							

							

							var quota_next=parseInt(arr[4],10)+1;
							
							if(quota_next>=6){
								$('#promo_code_play').val('OX02051215');
							}else{
								$('#promo_code_play').val('OX02041215');	
							}
							
								var setdata=arr[3].split("@");
								$("#memold_id_card").val(setdata[0]);
								$("#memold_mobile_no").val(setdata[1]);
								$("#memold_customer_id").val(setdata[2]);
								$("#memold_member_no").val(setdata[3]);
								$("#friend_id_card").val(setdata[4]);
								$("#friend_mobile_no").val(setdata[5]);
								$("#hid_name").val(setdata[6]);
							if(arr[0]=="ok"){

							

							
								if(setdata[4]!=$("#id_card").val()){
									alert("รหัสบัตรประชาชนของลูกค้าที่ป้อนเข้ามา ไม่ตรงกับที่ลงทะเบียนบน มือถือไว้ กรุณาแก้ไขใหม่ค่ะ");
									return false;
								}
								
								$("#show_button").html("<input type='button' value='สมัคร' onclick=\"m2mfromnew('OPMGMC300',$('#memold_id_card').val(),$('#memold_mobile_no').val(),$('#memold_customer_id').val(),$('#memold_member_no').val(),$('#friend_id_card').val(),$('#friend_mobile_no').val(),$('#promo_code_play').val(),$('#promo_code').val(),$('#coupon_code').val());\">");
							}
		    	    			

	    }); 
	}	
	
	
function m2mfromnew(promo_code,memold_id_card,memold_mobile_no,memold_customer_id,memold_member_no,friend_id_card,friend_mobile_no,promo_code_play,promo_code,coupon_code) {//ค้นหาสมาชิกใหม่

			var txtcr="หลังจากสมัครสมาชิกเสร็จแล้ว สิทธิ์จะเข้าผู้ชวนชื่อ : " +$("#hid_name").val()+" รหัสบัตร ปชช. : "+$("#memold_id_card").val()+" เบอร์โทร : "+$("#memold_mobile_no").val() + "  นะคะ \nหากตรวจสอบเรียบร้อยแล้วให้กดปุ่ม OK เพื่อยืนยันค่ะ";
			var c=confirm(txtcr);
			if(!c){
				return false;
			}
			$.get("../../../download_promotion/id_card_m2m/read.php",{
				  memold_id_card:memold_id_card,
				  memold_mobile_no:memold_mobile_no,
				  memold_customer_id:memold_customer_id,
				  memold_member_no:memold_member_no,
				  friend_id_card:friend_id_card,
				  friend_mobile_no:friend_mobile_no,
				  promo_code_play:promo_code_play,
				  promo_code:promo_code,
				  coupon_code:coupon_code,
					ran:Math.random()},function(data){
	        	$("#dialog-lastpro_play").dialog({
	    			height: 600,width:'90%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

							//เมื่อปิด
						//$("#dialog-lastpro_play" ).dialog( "destroy" );
						$("#dialog-lastpro_play" ).dialog('close');	
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

							//เมื่อปิด
						//$("#dialog-lastpro_play" ).dialog( "destroy" );
						$("#dialog-lastpro_play" ).dialog('close');		
				
					} 
				},
				
				
	    			buttons: {
						"ตกลง":function(){ 
							m2mchknew();
						},
						"ยกเลิก":function(){ 
							//จะให้ทำไร
							var cc=confirm("คุณต้องการยกเลิกใช่หรือไม่");
							if(!cc){
								return false;
							}
							//จะให้ทำไร
							$("#dialog-lastpro_play" ).dialog('close'); 
							location.reload(); 
						},
					 }
	    		});
	        	

	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ตรวจสอบสิทธิ์การสมัครสมาชิกใหม่");
			    $("#lastpro_play").html(data);
				
			  	
	        	
	        	
		});	
	}	

function m2mchknew() {//ค้นหาสมาชิกใหม่
		var status_readcard=$("#status_readcard").val();

		var application_id=$("#chk_application_id").val();
		if($("#chk_nation").val()=="thai"){
		
			var mobile_no=$("#mobile_no").val();
			if(!check_number(mobile_no)){
				alert("เบอร์มือถือต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}else if(mobile_no.length!=10){
				alert("เบอร์มือถือไม่ครบ 10 หลักค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			if((mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09')  &&  (mobile_no.substring(0,2)!='06') ){
				alert("เบอร์มือถือต้องขึ้่นต้นด้วย 08,09 หรือ 06  เท่านั้นค่ะ");
				$("#mobile_no").focus();
				return false;
			}
			
			var id_card=$("#id_card").val();
			

			/*
			if(id_card.length==0){
				alert("กรุณาป้อนรหัสบัตรประชาชนเพื่อใช้ในการค้นหาค่ะ");
				$("#id_card").focus();
				return false;
			}

			if(!checkID(id_card)){
				alert('รูปแบบรหัสประชาชนไม่ถูกต้อง');
				$("#id_card").val('');
				return false;
			}*/
			

			
		}
		
		var fname=$("#fname").val();
		var lname=$("#lname").val();
		var hbd=$("#hbd").val();
			if(fname==""){
				alert("กรุณาป้อนชื่อลูกค้าด้วยค่ะ");
				return false;
			}
			if(lname==""){
				alert("กรุณาป้อนนามสกุลลูกค้าด้วยค่ะ");
				return false;
			}
			
		/*	
		if(status_readcard!="AUTO" && $("#promo_code").val()=="OPMGMI300"){
			alert("คุณเลือกสมัครแบบใช้บัตรประชาชนแทนบัตรสมาชิก ดังนั้นบัตรประชาชนของลูกค้าจะต้องอ่านจากเครื่องอ่าน ID CARD ได้เท่านั้นค่ะ หาาอ่านไม่ได้ให้เลือกสมัครแบบใช้บัตรสมาชิกตามปกติค่ะ");
			return false;
		}*/
		
		
		if(status_readcard=="Manual"){
			alert("ต้องอ่านข้อมูลบัตรประชาชนจากเครื่องอ่านบัตรประชาชนค่ะ หากอ่านไม่ได้ต้องถ่ายรูปบัตรประชาชนของลูกค้าไว้ค่ะ");
			return false;
		}		
		
	    $.get("../../../download_promotion/id_card_m2m/findnewmember.php",{
						memold_id_card:$("#memold_id_card").val(),
	    				memold_mobile_no:$("#memold_mobile_no").val(),
						memold_customer_id:$("#memold_customer_id").val(),
						memold_member_no:$("#memold_member_no").val(),
						coupon_code:$("#coupon_code").val(),
						id_card:$("#id_card").val(),
						mobile_no:$("#mobile_no").val(),
	    				fname:$("#fname").val(),
	    				lname:$("#lname").val(),
	    				hbd:$("#hbd").val(),
	    				hbd_day:$("#hbd_day").val(),
	    				hbd_month:$("#hbd_month").val(),
	    				hbd_year:$("#hbd_year").val(),
	    				num_snap:$("#num_snap").val(),
		    	    	ran:Math.random()},function(data){
							
							var arr=data.split("###");
							
							if(arr[0]=="OK"){
								alert($("#id_card").val()+"&"+$("#coupon_code").val()+"&"+$("#memold_id_card").val()+"&"+$("#memold_mobile_no").val()+"&"+$("#promo_code_play").val()+"&"+$("#mobile_no").val());
								lineApp3($("#promo_code").val(),0,0,0,0,0,$("#id_card").val(),$("#coupon_code").val(),$("#memold_id_card").val(),$("#memold_mobile_no").val(),$("#promo_code_play").val(),$("#mobile_no").val()); 
		    	    			$("#dialog-lastpro_play" ).dialog( "destroy" );
		    	    		}else{
		    	    			alert(arr[1]);
		    	    		}	

	    }); 
	}	
	
	function listfriend(member_no) {//ค้นหาสมาชิกใหม่
	
		$.get("../../../download_promotion/id_card_m2m/listfriend.php",{
			  member_no:member_no,
	        ran:Math.random()},function(data){
				
	        	$("#dialog-lastpro_play").dialog({
	    			height: 500,width:'90%',modal: true,resizable:true,closeOnEscape:true,
	    			open:function(){
		    		
  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#a0bddc"});
  	    			$(this).dialog('widget')
                    // find the title bar element
                    .find('.ui-dialog-titlebar')
                    // alter the css classes
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top'); 
  	    			
                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#FFFFFF",//#CFE2E5
                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#f9f9f9"});
                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
                    	"margin":"0 0 0 0","background-color":"#f9f9f9"}); 
                    
                    
	    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
	    			//$(".ui-dialog-titlebar").hide();
	    		},
		    	
			    close:function(evt,ui){

					if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
						//setTimeout(function(){canclelastpro(promo_code);},400);

						//จะให้ทำไร
						$("#dialog-lastpro_play" ).dialog('close'); 
						location.reload(); 
						
					}
					if(evt.originalEvent && $(evt.originalEvent.which==27)){

						//จะให้ทำไร
						$("#dialog-lastpro_play" ).dialog('close'); 
						location.reload(); 

				
					} 
				},
				
				
	    			buttons: {
			
						"ยกเลิก":function(){ 
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
						},
					 }
	    		});
	        	

	
			    $("#ui-dialog-title-dialog-lastpro_play").html("Promotion MGM สำหรับคุณ");
			      $("#lastpro_play").html(data);
			    //$('#ifrm').attr('src', "http://192.168.3.247/download_promotion/id_card/read.php")
			  
	        	
	        	
		});	
	}	
	
	
function m2m_from(promo_code,status_otp,member_no,friend_id_card,friend_mobile_no,chk_id_card,chk_mobile_no){
		$.get("../../../download_promotion/id_card_quick/m2m_from.php",{
			promo_code:promo_code,
			status_otp:status_otp,
			member_no:member_no,
			friend_id_card:friend_id_card,
			friend_mobile_no:friend_mobile_no,
			chk_id_card:chk_id_card,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 700,width:'65%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
				
							
							"ตกลง":function(){ 
								//จะให้ทำไร
								m2m_save();
								
								
							},
							
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
	        	$("#id_card").focus();
				$("#promo_code").val(promo_code);

				$("#promo_des").html("รับส่วนลด <span style='color:#FF0000;font-size:30px;'>50%</span>สูงสุด 2 ชิ้น");
				
				$("#member_no").val(member_no);
				$("#friend_id_card").val(friend_id_card);
				$("#friend_mobile_no").val(friend_mobile_no);
				$("#chk_id_card").val(chk_id_card);
				$("#id_card").val(chk_id_card);
				$("#mobile_no").val(chk_mobile_no);

				
				
	       
		});
		
		
	}
	
function m2m_sendotp(){
		var id_card=$("#id_card").val();
		
		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			/*
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
			*/
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		
		
		

		
		
		var mobile_no=$("#mobile_no").val();
		if(document.getElementById("nothai").checked==false){
			
				if(!check_number(mobile_no)){
					alert("เบอร์มือถือต้องใส่เป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				if(mobile_no.length!=10){
					alert("เบอร์มือถือไม่ครบ 10 หลัก");
					$("#mobile_no").focus();
					return false;
				}
				
				if( (mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09') &&  (mobile_no.substring(0,2)!='06') ){
						alert("เบอร์มือถือต้องขึ้่นต้นด้วย 06,08,09 เท่านั้น");
						$("#mobile_no").focus();
						return false;
					
				}
		
		}


		if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
					alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
					return false;
		}
		if($("#chk_id_card").val()!=$("#id_card").val()){
			alert("บัตรประชาชนที่เสียบเข้ามาไม่ใช่ของลูกค้าท่านนี้ค่ะ");
			return false;
		}
		$.get("../../../download_promotion/id_card_quick/m2m_sendotp.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:$("#otp_code").val(),
			val_otp:$("#val_otp").val(),
			member_no:$("#member_no").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				if(arrdata[0]=="IDCARD_PLAY"){
					alert("รหัสบัตรประชาชนนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILE_PLAY"){
					alert("เบอร์มือถือนี้เล่นไปแล้วไม่สามารถเล่นซ้ำได้");
					return false;
				} else if(arrdata[0]=="MOBILEDIFF"){
					alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					return false;
				} else if(arrdata[0]=="NOPROFILE"){
					alert("ไม่พบฐานข้อมูลสมาชิกในระบบ");
					return false;
				} else {
					alert(arrdata[1]);
					$("#val_otp").focus();
				}

		}); 
	}	
	
	function m2m_save(){
		var id_card=$("#id_card").val();
		
		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			/*
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
			*/
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		
		
		
		if($("#chk_ecoupon").val()=="Y"){
			var otp_code=$("#otp_code").val();
			if(otp_code.length==0){
						alert("ต้องใส่ รหัส E-coupon ก่อนค่ะ");
						$("#otp_code").focus();
						return false;
			}			
		}
		
		
		



		if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
					alert("ถ้าอ่านบัตรประชาชนไม่ได้ ต้องถ่ายรูปบัตรประชาชนก่อนค่ะ");
					return false;
		}
		
		if($("#status_readcard").val()=="MANUAL"){
			var val_otp=$("#val_otp").val();
			if(val_otp.length==0){
						alert("ต้องใส่ รหัส OTP CODE ก่อนค่ะ");
						$("#val_otp").focus();
						return false;
			}	
		}
		

		if($("#chk_id_card").val()!=$("#id_card").val()){
			alert("บัตรประชาชนที่เสียบเข้ามาไม่ใช่ของลูกค้าท่านนี้ค่ะ");
			return false;
		}
		var var_coupon_code=$("#friend_id_card").val()+"#"+$("#friend_mobile_no").val();
		$.get("../../../download_promotion/id_card_quick/m2m_save.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:$("#otp_code").val(),
			val_otp:$("#val_otp").val(),
			member_no:$("#member_no").val(),
			friend_id_card:$("#friend_id_card").val(),
			friend_mobile_no:$("#friend_mobile_no").val(),
	        ran:Math.random()},function(data){
				//alert(data);
				var arrdata=data.split("###");
					
				if(arrdata[0]=="Y"){
					$("#dialog-lastpro_play" ).dialog('close');	
					callBackToDo2($("#promo_code").val(),'Y','OK',id_card,var_coupon_code,""); 
				}else{
					alert(arrdata[1]);		
				}				

				
			
		}); 
	}		
	
	
	
	
function ccsregisterfrom(application_id,coupon_code){
		$.get("../../../download_promotion/id_card_newmem/read.php",{
			  application_id:application_id,
			  coupon_code:coupon_code,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 400,width:'50%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
											
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("โปรโมชั่น : "+application_id+" - สมาชิกใหม่แบบใช้บัตร ปชช. แทนบัตรสมาชิก");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
		
				
	       
		});
		
		
	}	
	
function ccsreadidcardfrom(){
		$.get("../../../download_promotion/id_card_newmem/ccs_readidcard_from.php",{
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 430,width:'50%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
											
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ขั้นตอนที่ 1 : ยืนยันตัวตนสมาชิก");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
		
				
	       
		});
		
		
	}	

function ccsreadidcardfrom_mode_photo(){
		$.get("../../../download_promotion/id_card_newmem/mode_photo.php",{
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 600,width:'45%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
											
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ขั้นตอนที่ 2 : ถ่ายรูปยืนยันตัวตนสมาชิก");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				$("#id_card").focus();
				
		
				
	       
		});
		
		
	}		
	
function search_idcard(id_card,status_readcard,otpcode) {
    $.get("../../../download_promotion/id_card_newmem/search_idcard.php",{
	      	    	id_card:id_card,
					status_readcard:status_readcard,
					otpcode:otpcode,
	    	    	ran:Math.random()},function(data){
						var arr=data.split("###");
						if(arr[0]=="member_null"){
							alert("ไม่พบข้อมูลสมาชิกในระบบ");
							return false
						}else if(arr[0]=="one"){
							close_ccschangecardfrom();
							movemembercard(arr[1],id_card,status_readcard,otpcode,arr[2],arr[3]);
						}else{
						
							ccsreadidcardfrom_select_card(arr[1]);
						}
						
					
    }); 
	
}	


function ccsreadidcardfrom_select_card(data){
	


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 500,width:'45%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {
											
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("ขั้นตอนที่ 3 : เลือกสิทธิ์ OPS DAY");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				$("#id_card").focus();
				
		
}	
function close_ccschangecardfrom(){
		//alert("a");
		$("#dialog-lastpro_play" ).dialog('close'); 	
}
	

	
function movemembercard(member_no,id_card,status_readcard,otpcode,mobile_no,ops){

	$.get("../../../download_promotion/id_card_newmem/ccs_log.php",{
					member_no:member_no,
					id_card:id_card,
					status_readcard:status_readcard,
					otpcode:otpcode,
					ran:Math.random()},function(data){
						if(data=="Y"){
							$('#csh_member_no').val(member_no);	
							$('#csh_member_no').focus();
							//alert($("#csh_status_no").val());
							/*if($("#csh_status_no").val()=="04"){
								redeemPoint();
							}else{
								callMemberInfo();
							}*/
								//alert(ops);
							 $("#csh_id_card").val(id_card); 
							 $("#csh_mobile_no").val(mobile_no); 
							 $("#csh_ops_day").val(ops); 							
							cmdEnterKey("csh_member_no"); 
						}else if(data=="ClearN"){
							alert("Clear Log การใช้สิทธิ์คนก่อนหน้าไม่ได้ กรุณาทำใหม่อีกครั้งค่ะ");	
							return false;
						}else{
							alert("เก็บ Log การใช้สิทธิ์ไม่ได้ กรุณาทำใหม่อีกครั้งค่ะ");	
							return false;
						}
						
					
	}); 

	
}

function ccs_return_from(function_next){
$.get("../../../download_promotion/id_card_newmem/ccs_return_from.php",{
	  		function_next:function_next,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 400,width:'50%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {			
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("Confirm บัตร ปชช. อีกครั้งก่อนบันทึกบิล");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
		
				
	       
		});
		
				
		
}	
	
function api_verify_from(){
		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 400,width:'60%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						} 
					},
					
					
		    			buttons: {

							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("Verify id card");
				 //$(".ui-widget-header").css("background-image","none");
				 //$(".ui-widget-header").css("background-color","#f49f1e");
				 //$(".ui-widget-header").css("color","#144055");

				 
			    $("#lastpro_play").html("<iframe id='ccs_changecard_from' src='../../../download_promotion/id_card_newmem/api_verify_from.php' width='100%' height='100%' style='border:0'>");
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
		
		
	}	
	
	
	
	
	
//API READ ID CARD =================================================================
function apiread(datastart,function_next){

			var arr=datastart.split("#");
			var member_no=arr[0];
			var promo_code=arr[1];
			var id_card_ok=arr[2];
			var mobile_no=arr[3];
			
			//var chk_esc=true;
			
			$("#dialog-lastpro_play" ).dialog( "destroy" );
			$.get("../../../download_promotion/id_card_api/read.php",{
				function_next:function_next,
				member_no:member_no,
				promo_code:promo_code,
				id_card_ok:id_card_ok,
				mobile_no:mobile_no,
				ran:Math.random()},function(data){
						/*if(promo_code=="OX08030915" || promo_code=="OX08040915" || promo_code=="OX08211215"){//ถ้ามีstock อยู่ ต้อง แถม ห้าม Esc ข้ามโปรไป
							var chk_esc=false;
						}*/
	
						//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
						$("#dialog-lastpro_play").dialog({
							height: 547,width:'60%',modal: false,resizable:true,closeOnEscape:true,
							open:function(){
							

							
							//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
						},
						
						close:function(evt,ui){
		
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
								//setTimeout(function(){canclelastpro(promo_code);},400);
		
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
						
									callBackToDo(); 
								
							}
							if(evt.originalEvent && $(evt.originalEvent.which==27)){
		
									
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							} 
						},
						
						
							buttons: {
					
								
								"ตกลง":function(){ 
									if($("#nation").val()==2){
										if($("#id_card").val()==""){
											alert("กรุณาระบุรหัสPassportของสมาชิกด้วยครับ");
											return false;
										}
										if($("#fname").val()==""){
											alert("กรุณาระบุชื่อของสมาชิกด้วยครับ");
											return false;
										}
										if($("#lname").val()==""){
											alert("กรุณาระบุนามสกุลของสมาชิกด้วยครับ");
											return false;
										}	
										
										if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()==""){
											alert("กรณีบัตร ปชช. ลูกค้าอ่านจากเครื่องอ่าน ID CARD ไม่ได้ ต้องถ่ายรูปบัตร ปชช. ลูกค้าไว้ก่อนครับ");
											return false;
										}
										
										$("#dialog-lastpro_play" ).dialog('close'); 
										api_senddata(function_next);										
									}else{
										//จะให้ทำไร
										if($("#id_card").val()==""){
											alert("กรุณาระบุรหัสบัตรประชาชนของสมาชิกด้วยครับ");
											return false;
										}
										if(!checkID($("#id_card").val())){
											alert("รูปแบบรหัสบัตร ปชช. ไม่ถูกต้อง กรุณากรอกใหม่ครับ");
											return false;
										}
										if($("#fname").val()==""){
											alert("กรุณาระบุชื่อของสมาชิกด้วยครับ");
											return false;
										}
										if($("#lname").val()==""){
											alert("กรุณาระบุนามสกุลของสมาชิกด้วยครับ");
											return false;
										}	
										
	
										if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()==""){
											alert("กรณีบัตร ปชช. ลูกค้าอ่านจากเครื่องอ่าน ID CARD ไม่ได้ ต้องถ่ายรูปบัตร ปชช. ลูกค้าไว้ก่อนครับ");
											return false;
										}
										
										
										if($("#id_card").val()!=id_card_ok && member_no!="" && id_card_ok!=""){
											alert("รหัสบัตร ปชช. ของสมาชิกท่านนี้คือ : "+id_card_ok+"แต่ที่เสียบเข้ามานั้นไม่ถูกต้อง กรุณาเสียบบัตร ปชช. ที่ถูกต้องของสมาชิกท่านนี้ด้วยครับ");
											return false;
										}	
										
										if(member_no!=""){
											if($("#status_readcard").val()=="MANUAL" && $("#otpcode").val()==""){
												alert("กรณีบัตร ปชช. ลูกค้าอ่านไม่ได้ต้องใส่รหัส OTP CODE ด้วยครับ");
												return false;
											}
										}
										
	
										
										if($("#status_readcard").val()=="MANUAL" && member_no!=""){
											apicheckotpx(mobile_no,datastart,function_next);
										}else{
											$("#dialog-lastpro_play" ).dialog('close'); 
											api_senddata(function_next);
										}		
									}
									
								},
								
								"ยกเลิก":function(){ 
				
										//จะให้ทำไร
										if(promo_code=="OX08030915" || promo_code=="OX08050915"   || promo_code=="OX08211215"){
											$("#dialog-lastpro_play" ).dialog( "destroy" );
											canclelastpro(promo_code);
											//lastaddhotpro();
										}else{
											$("#dialog-lastpro_play" ).dialog('close'); 
											location.reload(); 
										}
								
								
								},							
								
							 }
						});
						
					$("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
					$("#lastpro_play").html(data);
					$("#dialog-lastpro_play").css("background-color","#FFFFFF");
					$("#content").css("background-color","#FFFFFF");
					
					if(member_no==""){
						$("#showfromotpcode").hide();
					}
					$("#promo_code").val(promo_code);
					$("#id_card").focus();

			   
			});
		
		
		
		
		
}	

function api_senddata(function_next){
		//alert(function_next);
		var id_card=document.getElementById('id_card').value;
		var fname=document.getElementById('fname').value;
		var lname=document.getElementById('lname').value;
		var birthday=document.getElementById('birthday').value;
		var promo_code=document.getElementById('promo_code').value;
		var val=id_card+"#"+fname+"#"+lname+"#"+birthday+"#"+promo_code;
		//alert(val);
		
		if(function_next=="showFormSmsMobile"){ //pro call servey
			showFormSmsMobile(promo_code,'T','Y'); 
		}else{
			var map_function_next=function_next+"('"+val+"');";
			//alert(map_function_next);
			eval(map_function_next);	
		}
}	

function apisendotpx(mobile_no){

	if($("#id_card").val()==""){
		alert("กรุณาระบุรหัสบัตรประชาชนของสมาชิกด้วยครับ");
		return false;
	}
	if(!checkID($("#id_card").val())){
		alert("รูปแบบรหัสบัตร ปชช. ไม่ถูกต้อง กรุณากรอกใหม่ครับ");
		return false;
	}
	if($("#fname").val()==""){
		alert("กรุณาระบุชื่อของสมาชิกด้วยครับ");
		return false;
	}
	if($("#lname").val()==""){
		alert("กรุณาระบุนามสกุลของสมาชิกด้วยครับ");
		return false;
	}
									

	$.get("../../../../download_promotion/id_card_api/sendotp.php",{
	  		mobile_no:mobile_no,
			id_card:$("#id_card").val(),
			
	        ran:Math.random()},function(data){
				
				
				var arr=data.split("###");
				alert(arr[1]);	
				

		});
}

function apicheckotpx(mobile_no,datastart,function_next){

	$.get("../../../../download_promotion/id_card_api/checkotp.php",{
	  		mobile_no:mobile_no,
			id_card:$("#id_card").val(),
			otpcode:$("#otpcode").val(),
	        ran:Math.random()},function(data){
				
				
				var arr=data.split("###");
				
				if(arr[0]=="OK"){
					$("#dialog-lastpro_play" ).dialog('close'); 
					api_senddata(function_next);
				}else{
					alert(arr[1]);	
					return false;
				}
				

		});
}


//API READ ID CARD =================================================================
function apireadwalkin(datastart,function_next){
			
			var arr=datastart.split("#");
			var member_no=arr[0];
			var promo_code=arr[1];
			var id_card_ok=arr[2];
			var mobile_no=arr[3];
			$.get("../../../download_promotion/id_card_api/readwalkin.php",{
				  member_no:member_no,
				 promo_code:promo_code,
				function_next:function_next,
				mobile_no:mobile_no,
				ran:Math.random()},function(data){
	
	
						//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
						$("#dialog-lastpro_play").dialog({
							height: 650,width:'60%',modal: true,resizable:true,closeOnEscape:false,
							open:function(){
							
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
							$(this).dialog('widget')
							// find the title bar element
							.find('.ui-dialog-titlebar')
							// alter the css classes
							.removeClass('ui-corner-all')
							.addClass('ui-corner-top'); 
							
							$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
								"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
							$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
								"margin":"0 0 0 0","background-color":"#adb2ba"}); 
							
							
							$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
							
							//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
						},
						
						close:function(evt,ui){
		
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
								//setTimeout(function(){canclelastpro(promo_code);},400);
		
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							}
							if(evt.originalEvent && $(evt.originalEvent.which==27)){
		
									
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							} 
						},
						
						
							buttons: {
					
								
								"ตกลง":function(){ 
									//จะให้ทำไร
									//alert('jj');
									if($("#birthday").val()==""){										
										alert("กรุณากอรกข้อมูลวันเกิดรูปแบบ แบบ ปี-เดือน-วัน (ex. 1900-01-01)");
										return false;
									}else{
										var dateString=$("#birthday").val();

										 var regEx =/^\d{4}-\d{2}-\d{2}$/;
										 var chk_format=dateString.match(regEx); // true ตรวจ format 										
										if(chk_format){
												//ตรวจสอบอายุห้ามต่ำกว่า 15
												var today = new Date();
												var birthDate = new Date(dateString);
												var age = today.getFullYear() - birthDate.getFullYear();
												var m = today.getMonth() - birthDate.getMonth();
												if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) 
												{
													age--;
												}
												//alert(age);
												if(age<15){
													
													alert("ลูกค้าต้องมีอายุมากกว่า 15 ปีขึ้นไป ");
													return false;
												}
										}else{
										   alert("กรุณากอรกข้อมูลวันเกิดรูปแบบ ปี-เดือน-วัน  (ex. 1900-01-01)");
										   return false;
										}
									}
									if($("#id_card").val()==""){
										alert("กรุณาระบุรหัสบัตรประชาชนของสมาชิกด้วยครับ");
										return false;
									}
									if(!checkID($("#id_card").val())){
										alert("รูปแบบรหัสบัตร ปชช. ไม่ถูกต้อง กรุณากรอกใหม่ครับ");
										return false;
									}
									if($("#fname").val()==""){
										alert("กรุณาระบุชื่อของสมาชิกด้วยครับ");
										return false;
									}
									if($("#lname").val()==""){
										alert("กรุณาระบุนามสกุลของสมาชิกด้วยครับ");
										return false;
									}
									if($("#id_card").val()!=id_card_ok && member_no!="" && id_card_ok!=""){
										alert("รหัสบัตร ปชช. ของสมาชิกท่านนี้คือ : "+id_card_ok+"แต่ที่เสียบเข้ามานั้นไม่ถูกต้อง กรุณาเสียบบัตร ปชช. ที่ถูกต้องของสมาชิกท่านนี้ด้วยครับ");
										return false;
									}	
									if($("#status_readcard").val()=="MANUAL" && $("#otpcode").val()=="" && member_no!=""){
										alert("กรณีบัตร ปชช. ลูกค้าอ่านไม่ได้ต้องใส่รหัส OTP CODE ด้วยครับ");
										return false;
									}									
									if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()==""){
										alert("กรณีบัตร ปชช. ลูกค้าอ่านจากเครื่องอ่าน ID CARD ไม่ได้ ต้องถ่ายรูปบัตร ปชช. ลูกค้าไว้ก่อนครับ");
										return false;
									}
									
									if(member_no!="" && $("#status_readcard").val()=="MANUAL"){
										apicheckotp(mobile_no,datastart,function_next);
									}else{
										$("#dialog-lastpro_play" ).dialog('close'); 
										api_senddatawalkin(datastart,function_next);
									}
									
									

									
								},
								
								"ยกเลิก":function(){ 
				
										//จะให้ทำไร
										$("#dialog-lastpro_play" ).dialog('close'); 
										//location.reload(); 
								
								
								},
								
							 }
						});
						
					$("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
					$("#lastpro_play").html(data);
					$("#dialog-lastpro_play").css("background-color","#FFFFFF");
					$("#content").css("background-color","#FFFFFF");
					
					
					$("#id_card").focus();
					if(member_no==""){
						$("#showfromotpcode").hide();
					}

			   
			});
		
		
		
		
		
}	

function api_senddatawalkin(datastart,function_next){
		//alert(function_next);
		var id_card=document.getElementById('id_card').value;
		var fname=document.getElementById('fname').value;
		var lname=document.getElementById('lname').value;
		var birthday=document.getElementById('birthday').value;
		var val=id_card+"#"+fname+"#"+lname+"#"+birthday;
		//alert(val);
		var map_function_next=function_next+"('"+datastart+"','"+val+"');";
		//alert(map_function_next);
		eval(map_function_next);	
}	

function apisendotp(mobile_no){

	if($("#id_card").val()==""){
		alert("กรุณาระบุรหัสบัตรประชาชนของสมาชิกด้วยครับ");
		return false;
	}
	if(!checkID($("#id_card").val())){
		alert("รูปแบบรหัสบัตร ปชช. ไม่ถูกต้อง กรุณากรอกใหม่ครับ");
		return false;
	}
	if($("#fname").val()==""){
		alert("กรุณาระบุชื่อของสมาชิกด้วยครับ");
		return false;
	}
	if($("#lname").val()==""){
		alert("กรุณาระบุนามสกุลของสมาชิกด้วยครับ");
		return false;
	}
									

	$.get("../../../../download_promotion/id_card_api/sendotp.php",{
	  		mobile_no:mobile_no,
			id_card:$("#id_card").val(),
			
	        ran:Math.random()},function(data){
				
				
				var arr=data.split("###");
				alert(arr[1]);	
				

		});
}

function apicheckotp(mobile_no,datastart,function_next){

	$.get("../../../../download_promotion/id_card_api/checkotp.php",{
	  		mobile_no:mobile_no,
			id_card:$("#id_card").val(),
			otpcode:$("#otpcode").val(),
	        ran:Math.random()},function(data){
				
				
				var arr=data.split("###");
				
				if(arr[0]=="OK"){
					$("#dialog-lastpro_play" ).dialog('close'); 
					api_senddatawalkin(datastart,function_next);
				}else{
					alert(arr[1]);	
					return false;
				}
				

		});
}


//tour================================================================================================================
function tour_from(val){

			$.get("../../../download_promotion/tour/tour_from.php",{
				val:val,
				ran:Math.random()},function(data){
	
	
						//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
						$("#dialog-lastpro_play").dialog({
							height: 545,width:434,modal: true,resizable:true,closeOnEscape:false,
							open:function(){
							
							$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
							$(this).dialog('widget')
							// find the title bar element
							.find('.ui-dialog-titlebar')
							// alter the css classes
							.removeClass('ui-corner-all')
							.addClass('ui-corner-top'); 
							
							$(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
								"padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
							$(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
								"margin":"0 0 0 0","background-color":"#adb2ba"}); 
							
							
							$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
							
							//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
						},
						
						close:function(evt,ui){
		
							if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
								//setTimeout(function(){canclelastpro(promo_code);},400);
		
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							}
							if(evt.originalEvent && $(evt.originalEvent.which==27)){
		
									
									//เมื่อปิด
									//$("#dialog-lastpro_play" ).dialog('close');
									callBackToDo(); 
								
							} 
						},
						
						
							buttons: {
					
								
								"ตกลง":function(){ 
									//จะให้ทำไร
									 
									//api_senddata(function_next);
									tour_chk();
									
								},
								
								"ยกเลิก":function(){ 
				
										//จะให้ทำไร
										$("#dialog-lastpro_play" ).dialog('close'); 
										location.reload(); 
								
								
								},
								
							 }
						});
						
					$("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
					$("#lastpro_play").html(data);
					$("#dialog-lastpro_play").css("background-color","#FFFFFF");
					$("#content").css("background-color","#FFFFFF");
					
					
					$("#id_card").focus();

			   
			});
	
}
function tour_chk(){
	if($("#id_card").val()==""){
		alert("กรุณาใส่รหัสบัตรประชาชนของ GUIDE ด้วยค่ะ");
		return false;
	}
	if($("#tour_id").val()==""){
		alert("กรุณาเลื่อกบริษัททัวร์ด้วยค่ะ");
		return false;
	}	
	$.get("../../../download_promotion/tour/tour_chk.php",{
	  		id_card:$("#id_card").val(),
			fullname:$("#fullname").val(),
			tour_id:$("#tour_id").val(),
			
	        ran:Math.random()},function(data){
				
				
				var arr=data.split("@@@");
				if(arr[0]=="OK"){
					
					$("#dialog-lastpro_play" ).dialog('close'); 
					callBackCoOpration(arr[1]); 
					
				}else{
					alert(arr[1]);	
				}
				

		});
		
				
		
}	
	


function fromreadprofile_verify(promo_code,status_otp,member_no,promo_des,id_card,vadata){
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_verify.php",{
			promo_code:promo_code,
			status_otp:status_otp,
			member_no:member_no,
	        ran:Math.random()},function(data){


		        	//$("#dialog-lastpro").dialog({height: 400,width:'80%',modal: true,resizable:true});
			    	$("#dialog-lastpro_play").dialog({
		    			height: 800,width:'65%',modal: true,resizable:true,closeOnEscape:false,
		    			open:function(){
			    		
	  	    			$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#76a5f1"});
	  	    			$(this).dialog('widget')
	                    // find the title bar element
	                    .find('.ui-dialog-titlebar')
	                    // alter the css classes
	                    .removeClass('ui-corner-all')
	                    .addClass('ui-corner-top'); 
	  	    			
	                    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"background-color":"#8cb1ef",//#CFE2E5
	                        "padding":"0 0 0 0","margin":"0 0 0 0","font-size":"27px","color":"#000"});
	                    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0",
	                    	"margin":"0 0 0 0","background-color":"#adb2ba"}); 
	                    
	                    
		    			$(this).parent().children().children('.ui-dialog-titlebar-close').hide(); 
						
						//$(".ui-widget-header").css({"background-color":"#0e1c32","background-image":"none","color":"#FFFFFF"});
		    		},
			    	
				    close:function(evt,ui){
	
						if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length){
							//setTimeout(function(){canclelastpro(promo_code);},400);
	
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
							
						}
						if(evt.originalEvent && $(evt.originalEvent.which==27)){
	
								
								//เมื่อปิด
								//$("#dialog-lastpro_play" ).dialog('close');
								callBackToDo(); 
						} 
					},
					
					
		    			buttons: {
				
							
							"ตกลง":function(){ 
								//จะให้ทำไร
								fromreadprofile_verify_save();
								
								
							},
							
							"ยกเลิก":function(){ 
			
									//จะให้ทำไร
									$("#dialog-lastpro_play" ).dialog('close'); 
									location.reload(); 
							
							
							},
							
						 }
		    		});
			    	
			    $("#ui-dialog-title-dialog-lastpro_play").html("แบบฟอร์มอ่านบัตร ID CARD");
			    $("#lastpro_play").html(data);
				$("#dialog-lastpro_play").css("background-color","#FFFFFF");
				$("#content").css("background-color","#FFFFFF");
				
				
	        	$("#id_card").focus();
				$("#promo_code").val(promo_code);
				$("#promo_des").html(promo_des);
				$("#id_card_chk").val(id_card);
				if(member_no==""){
					alert("กรุณาส่งรหัสสมาชิกเข้ามาที่หน้าจอนี้ด้วยครับ");
					return false;
				}
				$("#member_no").val(member_no);
				
				
	       
		});
		
		
	}



function fromreadprofile_verify_sendotp(){
		var id_card=$("#id_card").val();
		if($("#id_card_chk").val()!=id_card){
			alert("รหัสบัตรประชาชนที่ป้อนเข้ามาไม่ตรงกับที่มีอยู่ในฐานข้อมูลของสมาชิกค่ะ กรุณาแจ้งแก้ไขฐานข้อมูลสมาชิกกับ Beautyline ก่อนนะคะ");
			$("#id_card").focus();
			return false;
		}
		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		
		
		

		
		
		var mobile_no=$("#mobile_no").val();
		if(document.getElementById("nothai").checked==false){
			
				if(!check_number(mobile_no)){
					alert("เบอร์มือถือต้องใส่เป็นตัวเลขเท่านั้นค่ะ");
					$("#mobile_no").focus();
					return false;
				}
				if(mobile_no.length!=10){
					alert("เบอร์มือถือไม่ครบ 10 หลัก");
					$("#mobile_no").focus();
					return false;
				}
				
				if( (mobile_no.substring(0,2)!='08') &&  (mobile_no.substring(0,2)!='09') &&  (mobile_no.substring(0,2)!='06') ){
						alert("เบอร์มือถือต้องขึ้่นต้นด้วย 06,08,09 เท่านั้น");
						$("#mobile_no").focus();
						return false;
					
				}
		
		}

	
		$.get("../../../download_promotion/id_card_quick/fromreadprofile_verify_sendotp.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:$("#otp_code").val(),
			val_otp:$("#val_otp").val(),
			member_no:$("#member_no").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
			confirm_mobile:$("#confirm_mobile").val(),
	        ran:Math.random()},function(data){
				var arrdata=data.split("###");
				
				if(arrdata[0]=="MOBILEDIFF"){
					//alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					var confirm_mobile=confirm("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก หากยืนยันว่าเบอร์นี้เป็นของสมาชิกจริง ให้กด OK ค่ะ");
					if(!confirm_mobile){
						return false;
					}else{
						$("#confirm_mobile").val("Y");	
						$("#show_status_confirm_mobile").html("(ยืนยันเปลี่ยนเบอร์มือถือ)");
						document.getElementById("mobile_no").readOnly = "true";
						fromreadprofile_sendotp();
						return false;
						//fromreadprofile_otp_save();
					}
				}
				
				
				if(arrdata[0]=="N"){
					alert(arrdata[1]);
					return false;
				} else{
					alert(arrdata[1]);
				}	

		}); 
}	



function fromreadprofile_verify_save(){
		var id_card=$("#id_card").val();
		if($("#id_card_chk").val()!=id_card){
			alert("รหัสบัตรประชาชนที่ป้อนเข้ามาไม่ตรงกับที่มีอยู่ในฐานข้อมูลของสมาชิกค่ะ กรุณาแจ้งแก้ไขฐานข้อมูลสมาชิกกับ Beautyline ก่อนนะคะ");
			$("#id_card").focus();
			return false;
		}
		if(document.getElementById("nothai").checked==false){
			var status_nothai=1;
			if(!check_number(id_card)){
				alert("รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้นค่ะ");
				$("#id_card").focus();
				return false;
			}else if(id_card.length!=13){
				alert("รหัสบัตรประชาชนไม่ครบ 13 หลักค่ะ");
				$("#id_card").focus();
				return false;
			}else if(!checkID(id_card)){
				alert('รูปแบบรหัสบัตรประชาชนไม่ถูกต้อง');
				$("#id_card").focus();
				return false;
			}
		}else{
			var status_nothai=2;
			if(id_card==""){
				alert('กรุณาใส่เลขที่ Passport ของสมาชิกค่ะ');
				$("#id_card").focus();
				return false;
			}
		}
		
		
		
		if($("#chk_ecoupon").val()=="Y"){
			var otp_code=$("#otp_code").val();
			if(otp_code.length==0){
						alert("ต้องใส่ รหัส E-coupon ก่อนค่ะ");
						$("#otp_code").focus();
						return false;
			}			
		}
		
		
		



		if($("#status_readcard").val()=="MANUAL" && $("#status_photo").val()!="Y"){
					alert("กรณีที่บัตร ปชช. ลูกค้าไม่สามารถอ่านจากเครื่องอ่าน ID CARD ได้ ให้ถ่ายรูปบัตร ปชช. ของลูกค้าไว้ด้วยค่ะ");
					return false;
		}
		
		var val_otp=$("#val_otp").val();
		if(val_otp.length==0){
					alert("ต้องใส่ รหัส OTP CODE ก่อนค่ะ");
					$("#val_otp").focus();
					return false;
		}	
		


		$.get("../../../download_promotion/id_card_quick/fromreadprofile_verify_save.php",{
			status_readcard:$("#status_readcard").val(),
			status_photo:$("#status_photo").val(),
			num_snap:$("#num_snap").val(),
			id_img:$("#id_img").val(),
			ip_this:$("#ip_this").val(),
			address:$("#address").val(),
			mu:$("#mu").val(),
			tambon_name:$("#tambon_name").val(),
			amphur_name:$("#amphur_name").val(),
			province_name:$("#province_name").val(),
			mr:$("#mr").val(),
			sex:$("#sex").val(),
			mr_en:$("#mr_en").val(),
			fname_en:$("#fname_en").val(),
			lname_en:$("#lname_en").val(),
			card_at:$("#card_at").val(),
			start_date:$("#start_date").val(),
			end_date:$("#end_date").val(),
			promo_code:$("#promo_code").val(),
			id_card:$("#id_card").val(),
			fname:$("#fname").val(),
			lname:$("#lname").val(),
			mobile_no:$("#mobile_no").val(),
			birthday:$("#birthday").val(),
			otp_code:$("#otp_code").val(),
			val_otp:$("#val_otp").val(),
			chk_ecoupon:$("#chk_ecoupon").val(),
			member_no:$("#member_no").val(),
			confirm_mobile:$("#confirm_mobile").val(),
	        ran:Math.random()},function(data){
				//alert(data);
				var arrdata=data.split("###");
				
				if(arrdata[0]=="MOBILEDIFF"){
					//alert("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก กรุณาติดต่อ Beauty Line เพื่อแจ้งเปลี่ยนเบอร์โทรศัพท์มือถือก่อนค่ะ");
					var confirm_mobile=confirm("เบอร์มือถือที่ป้อนมาไม่ตรงกับฐานข้อมูลสมาชิก หากยืนยันว่าเบอร์นี้เป็นของสมาชิกจริง ให้กด OK ค่ะ");
					if(!confirm_mobile){
						return false;
					}else{
						$("#confirm_mobile").val("Y");	
						$("#show_status_confirm_mobile").html("(ยืนยันเปลี่ยนเบอร์มือถือ)");
						document.getElementById("mobile_no").readOnly = "true";
						fromreadprofile_otp_save();
						return false;
					}
				}
					
					
				if(arrdata[0]=="N"){
					alert(arrdata[1]);
					return false; 
				} else if(arrdata[0]=="Y"){
					$("#dialog-lastpro_play" ).dialog('close');	
					callBackIdCard($("#promo_code").val(),$("#id_card").val(),$("#val_otp").val());
				}else{
					alert(arrdata[1]);		
				}				

				
			
		}); 
	}		