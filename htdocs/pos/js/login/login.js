var clear_timer_keybarcode=null;
//---------------------------------------------------------------------------------
function stopTimerKeyBarcode(){
    if(clear_timer_keybarcode){
        clearTimeout(clear_timer_keybarcode);
        clear_timer_keybarcode=null;
    }
}//
//---------------------------------------------------------------------------------
function clsBarCode($obj){
        $obj.val('');
}//
//---------------------------------------------------------------------------------
$(function(){
	/*
	$.get("/pos/login/checksentdata",{
		ran:Math.random()
		},function(data){ 
			if(data=='NO'){
				var ran=Math.random();
				var url="transfer_to_office/index.php?ran="+ran;
				var name="transfer_to_office";
				var windowWidth="1";
				var windowHeight="1";
				popup(url,name,windowWidth,windowHeight);
			}
	});
	*/
	///*
	$('#user_id').focus();
	 check_key_login();
	 $('#user_id').draggable( "disable" );

	 $('#user_id').live("cut copy paste",function(e) {
	      e.preventDefault();
	 });
	 
   $('#user_id').keypress(function(e) {
       if(e.keyCode == 13) {
	        if($(this).val().length < 6){
	        	$(this).val("");
	            return false;
	        }else{
	        	$('#password').focus();
	        	return;
	        }
       }
    });
   
   $('#password').keypress(function(e) {
       if(e.keyCode == 13) {
       	//checkversion();
    	   checkLogin();
       }
    });
    
	$.getJSON("/pos/login/getip",{
		ran:Math.random()
		},function(data){
			if(data != null){
				var d=$.trim(data.num);
				var n=eval(d);
				if(d>0){
					var user_id=$.trim(data.user_id);
					var password=$.trim(data.password_id);
					$("#user_id").val(user_id);
					$("#password").val(password);
					checkLogin();
				}
			}
	});
    //*/
	
	
});
//----------------------------------------------------------------------------------------	
function popup(url,name,windowWidth,windowHeight){    
	myleft=(screen.width)?(screen.width-windowWidth)/2:100;	
	mytop=(screen.height)?(screen.height-windowHeight)/2:100;	
	properties = "width="+windowWidth+",height="+windowHeight;
	properties +=",scrollbars=yes, top="+mytop+",left="+myleft;   
	window.open(url,name,properties);
}
//---------------------------------------------------------------------------------
function killall(){
		jConfirm('ท่านต้องการ ออกจากระบบ POS.ใช่หรือไม่?','ข้อความยืนยัน', function(r){
		        if(r){
		        	//window.opener='x';
		        	//$.get("/pos/login/killall",{ran:Math.random()},function(){});
		        	window.close();
		        }else{
		        	return false;
		        }
		});
}
//---------------------------------------------------------------------------------
function check_key_login(){
	$.get("/pos/login/checkkeylogin",{
		ran:Math.random()
		},function(data){ 
			if(data=="Y"){
				//$("#checkkeylogin").html(data);
				$("#user_id").keypress( function(evt) {
					    var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode;
					    var objBarcode=$(this);
					    if(key == 13){
					        if($(this).val().length == 0){
					            return false;
					        }
					        stopTimerKeyBarcode();
					        //$('#password').focus();
					        return false;
					    }else{
					        stopTimerKeyBarcode();
				            clear_timer_keybarcode=setTimeout(function(){clsBarCode(objBarcode)},100);
					    }
					});
			}else{
				$("#user_id").val("");
				$("#password").val("");
			}
			
	});
}
//---------------------------------------------------------------------------------
function checkversion(){
		$.get("/pos/login/checkversion",{
			ran:Math.random()
			},function(data){ 
				//alert(data);
				if(data=='Y'){
					jConfirm('Version ระบบ ไม่ตรงกับ Server ท่านต้องการ Update เดี๋ยวนี้ใช่หรือไม่?','ข้อความยืนยัน', function(r){
					        if(r){
					        	updateversionpos();
					        }else{
					        	checkLogin();
					        }
					});//jconfirm
				}else{
					checkLogin();
				}
		});
}
//---------------------------------------------------------------------------------
function checkLogin(){
	if($("#user_id").val()=="") {
		alert("กรุณาระบุ หรัสพนักงาน");
		return false;
	}else if($("#password").val()=="") {
		alert("กรุณาระบุ  รหัสผ่าน  ");
		return false;
	}else{
		$.get("/pos/login/checklogin",{
			user_id:$("#user_id").val(),
			password:$("#password").val(),
			env_id:$("#env_id").val(),
			ran:Math.random()
			},function(data){
					if(data == 1){
						//window.location='/pos/index/index';
						window.location.replace("/pos/index/index");//*WR19092013
					}else{
						//if(data=="test"){window.location='/pos/index/index';}
						alert(data);
						$("#user_id").val("");
						$("#password").val("");
						return false;
					}
					
					
		});
	}
}
//---------------------------------------------------------------------------------
function updateversionpos(){
	var win = $.messager.progress({
		title:'Please waiting',
		msg:'Loading data...'
	});
	$.get("/pos/admin/updateversionpos",{ 
		ran:Math.random()
		},function(data){
			if(data=='success'){
				$.messager.progress('close');
				alert('เรียบร้อยแล้วกรุณา Login ใหม่');
			}
	});
	
}
//---------------------------------------------------------------------------------
