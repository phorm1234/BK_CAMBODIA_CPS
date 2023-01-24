$(function(){
	clearTmp();
	$("#txt_remark").focus();

	shortcut.add("F10",function(e) {
		e.stopPropagation();
		e.preventDefault();
		$("#btnSave").trigger('click');
	},{
		'type':'keypress',
		'propagate':true,
		'disable_in_input':false,
		'target':document
	});//shortcut.add("F10",function()
	
	url='/sales/inventory/getpickinglist';
	$("#grd_picking").flexigrid({
		url: url,
		dataType: 'json',
		colModel : [
			{display: '#', name : 'idx', width : 60, sortable : true, align: 'center'},
			{display: 'Document No', name : 'picking',width : 300,  sortable : true, align: 'left'},
			{display: 'จำนวนกล่อง', name : 'cbox',width : 80,  sortable : true, align: 'center'},
			{display: 'สถานะ', name : 'status',width : 130,  sortable : true, align: 'center'},
			{display: 'หมายเหตุ', name : 'remark',width : 350,  sortable : true, align: 'center'}
		],
		sortname: "id",
		sortorder: "asc",
		action:'gettmp',
		usepager:true,
		singleSelect:true,
		nowrap: false,
		title:'',
		useRp:true,
		buttons : [
					{name:'Delete',bclass: 'flgBtnDelClass',onpress : flgCommand},
					{separator: true},
					{name:'Refresh',bclass:'flgBtnRefClass',onpress :flgCommand}				
				],
		rp: 10,
		showTableToggleBtn: true,
		height:'320'
	});

	getDataCount();

	$("#txt_cbox").bind('keypress', function(e) {
    	return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
    });

	$("#txt_detail_lgs_all").click(function() {
		$("#txt_detail_lgs_all").select();		
	});
	
	$("#txt_detail_lgs_control").click(function() {
		$("#txt_detail_lgs_control").select();		
	});
});//load $(function(){

$("#txt_remark").keypress(function(event){
	 
	var keycode = (event.keyCode ? event.keyCode : event.which);
	
	if(keycode == '13'){
		$("#txt_pl").focus();			
	}
});//$("#txt_remark").keypress

$("#txt_pl").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	var txt_pl = ($("#txt_pl").val()).toUpperCase();
	//txt_pl=txt_pl.replace("'",""); 
	if(keycode == '13' && txt_pl != ""){

		var txt_lgs_status = $("#txt_lgs_status").val();
		if(txt_lgs_status=="0" && $("#txt_pl").val().length < 13)
		{
			jAlert('กรุณากรอกรหัสเอกสารให้ครบ','ข้อความแจ้งเตือน',function(){
				$("#txt_pl").val("").focus();
            	return false;
	     	});
		}
		else if(txt_lgs_status=="1" && ( $("#txt_pl").val().length < 0 || $("#txt_pl").val().length != 10))
		{
			jAlert('กรุณากรอกรหัสเอกสารให้ครบ 10 หลัก','ข้อความแจ้งเตือน',function(){
				$("#txt_pl").val("").focus();
            	return false;
	     	});
		}
		else
		{		
			var pick_type = txt_pl.substring(0,3);
			var status = "P";
	
			if(pick_type!="LGS" && txt_lgs_status=="1")
			{
				$("#txt_cbox").val("1");
				forInsert("P");
			}
			else if(pick_type!="LGS" && txt_lgs_status=="0")
			{
				jAlert('กรุณากรอกรหัสใบจัดส่งสินค้าก่อน','ข้อความแจ้งเตือน',function(){
					$("#txt_pl").val("").focus();
				});
			}
			else
			{
				var dlg_detail_lgs = {
					autoOpen: false,
					width:'auto',	
					height:'auto',	
					modal:true,
					stack: true,
					resizable:true,
					position:"center",
					showOpt: {direction: 'down'},		
					closeOnEscape:false,	
					title:"จำนวนกล่องที่ส่งทั้งหมด",
					open: function()
					{ 					
						$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
						$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
					    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb","margin":"0 0 0 0","font-size":"27px","color":"#000"});
					    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});							    			    

        				$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        				
        				var txt_pl = $("#txt_pl").val();
        				var txt_detail_lgs_no = txt_pl.substring(0,10).toUpperCase();
        				
        				var txt_detail_lgs_all = txt_pl.substring(10,13); 
        				if( txt_detail_lgs_all != "" ) txt_detail_lgs_all = parseInt(txt_detail_lgs_all,10);
        				else txt_detail_lgs_all = 0;
        				
        				var txt_detail_lgs_control=txt_pl.substring(13,16);
        				if( txt_detail_lgs_control != "" ) txt_detail_lgs_control = parseInt(txt_detail_lgs_control,10);
        				else txt_detail_lgs_control = 0;
        				
        				$("#hdn_detail_lgs_no").val(txt_detail_lgs_no);
        				$("#hdn_detail_lgs_all").val(txt_detail_lgs_all);
        				$("#hdn_detail_lgs_control").val(txt_detail_lgs_control);
        				
        				$("#txt_detail_lgs_no").val(txt_detail_lgs_no);
        				$("#txt_detail_lgs_all").val(txt_detail_lgs_all);
        				$("#txt_detail_lgs_control").val(txt_detail_lgs_control);
        				
        				$("#txt_detail_lgs_no").focus();
        				
        				$("#txt_detail_lgs_no").keypress(function(event){
        					var keycode = (event.keyCode ? event.keyCode : event.which);        					
        					if(keycode == '13'){
        						$("#txt_detail_lgs_all").focus();			
        						$("#txt_detail_lgs_all").select();		
        					}
        				});
        				
        				$("#txt_detail_lgs_all").keypress(function(event){
        					var keycode = (event.keyCode ? event.keyCode : event.which);        					
        					if(keycode == '13'){
        						$("#txt_detail_lgs_control").focus();			
        						$("#txt_detail_lgs_control").select();		
        					}
        				});
					},				
					close: function(evt,ui){	         
						$('#dlg_detail_lgs').dialog('destroy');													
					},//close: function
					buttons: {
        	            "บันทึก": function() {		
							if($("#txt_detail_lgs_all").val() < 0)
							{
								jAlert('จำนวนกล่องผิดปกติ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
									$("#txt_detail_lgs_all").focus();
									return false;
								});		
							}
							else if($("#txt_detail_lgs_control").val() < 0)
							{
								jAlert('จำนวนกล่องผิดปกติ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
									$("#txt_detail_lgs_control").focus();
									return false;
								});
							}
							else
							{
								forInsert("G");
								$('#dlg_detail_lgs').dialog('destroy');	
							}
        	            },
        	            "ยกเลิก": function() {		   
        	            	$('#dlg_detail_lgs').dialog('destroy');			
        	            	clearTmp();
        	            	$("#txt_pl").val("");
        					$("#txt_cbox").val("1");
        					
        					$("#hdn_detail_lgs_no").val("");
            				$("#hdn_detail_lgs_all").val("");
            				$("#hdn_detail_lgs_control").val("");
            				
            				$("#txt_detail_lgs_no").val("");
            				$("#txt_detail_lgs_all").val("");
            				$("#txt_detail_lgs_control").val("");

        	            	$("#txt_remark").focus();	
        	            }
        	        }	
				};
				$('#dlg_detail_lgs').dialog('destroy');
				$('#dlg_detail_lgs').dialog(dlg_detail_lgs);			
				$('#dlg_detail_lgs').dialog('open');
			}
		}
	}
});//$("#txt_pl").keypress

$("#txt_cbox").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);			
	if(keycode == '13'){
		var txt_cbox = $("#txt_cbox").val();
		var status = "I";
		var txt_pl = ($("#txt_pl").val()).toUpperCase();
		txt_pl=txt_pl.replace("'",""); 
		var pick_type = txt_pl.substring(0,3);
		if(txt_cbox=="")
		{
			$("#txt_cbox").val("1");
		}
		if(txt_pl != "")
		{
			if(pick_type=="LGS") { status = "G"; forInsert(status);}
			else if(pick_type=="PCK")
			{
				$("#txt_cbox").val("1");
				$("#slt_promo_st").focus().select();
			}
			else { 
				status = "I"; 
				forInsert(status);
			}
		}
		else
		{
			$("#txt_pl").focus();		
		}	
	} 
});//$("#txt_cbox").keypress
$("#slt_promo_st").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);			
	if(keycode == '13'){
		var txt_cbox = $("#txt_cbox").val();
		var status = "I";
		var txt_pl = ($("#txt_pl").val()).toUpperCase().trim();
		txt_pl=txt_pl.replace("'",""); 
		var pick_type = txt_pl.substring(0,3);
		if(txt_cbox=="")
		{
			$("#txt_cbox").val("1");
		}
		if(txt_pl != "")
		{
			if(pick_type=="LGS") { status = "G";}
			else if(pick_type=="PCK")
			{
				status = "P";
				$("#txt_cbox").val("1");
				$("#slt_promo_st").focus().select();
			}
			else { 
				status = "I"; 
			}
			forInsert(status);
		}
		else
		{
			$("#txt_pl").focus();		
		}	
	} 
});//$("#txt_cbox").keypress
function forInsert(status)
{
		var txt_pl = ($("#txt_pl").val()).toUpperCase();
		txt_pl=txt_pl.replace("'",""); 
		
		var txt_plno = $("#txt_plno").val();
		
		var tmp_seq = parseInt($("#txt_sum_box").val(),10)+1;
		var txt_cbox = $("#txt_cbox").val();
		var txt_remark = $("#txt_remark").val();
		
		var slt_promo_st = $("#slt_promo_st").val();
		
		var point1 = $("#hdn_detail_lgs_all").val();
		var point2 = $("#hdn_detail_lgs_control").val();
		var redeem_point = $("#txt_detail_lgs_all").val();
		var total_point = $("#txt_detail_lgs_control").val();
		
		//slt_promo_st="";
		
		if(status == "G" ) 
		{
			txt_pl = $("#hdn_detail_lgs_no").val();
			txt_cbox = redeem_point;
			slt_promo_st="";
			$("#txt_box_amount").val("0");
		}
		var tmp_status = status;
		if(status == "box")
		{
			var txt_box_qty = parseInt($("#txt_box_qty").val(),10);
			var txt_box_qty_r = parseInt($("#txt_box_qty_r").val(),10);
			var txt_box_amount = txt_box_qty_r - txt_box_qty;
			$("#txt_box_amount").val(txt_box_amount);
			//status=txt_box_qty_r - txt_box_qty;
			status = "";
			slt_promo_st="";
		}
		if(txt_cbox == "")
		{
			txt_cbox=1;
		}
		$.ajax({
			type:'post',
			url:'/sales/inventory/setpicktemp',
			cache: false,
			data:{ 
				txt_pl : txt_pl	, 
				txt_plno : txt_plno ,
				tmp_seq : tmp_seq , 
				txt_cbox : txt_cbox , 
				txt_remark : txt_remark , 
				status : status,
				slt_promo_st:slt_promo_st,
				point1 : point1,
				point2 : point2,
				redeem_point : redeem_point,
				total_point : total_point,
				box_amount : txt_box_amount
			},
			success:function(data){	
				$("#grd_picking").flexOptions({newp:data}).flexReload();
				$("#txt_pl").val("");
				$("#slt_promo_st").val("L");
				$("#txt_cbox").val("1");
				
				$("#hdn_detail_lgs_no").val("");
				$("#hdn_detail_lgs_all").val("");
				$("#hdn_detail_lgs_control").val("");
				
				$("#txt_detail_lgs_no").val("");
				$("#txt_detail_lgs_all").val("");
				$("#txt_detail_lgs_control").val("");
				$("#txt_pl").focus();		
				getDataCount();
			}
		});		
}//forInsert

function getDataCount()
{
	var doc_no = $("#txt_plno").val();
	$.ajax({
		type:'post',
		url:'/sales/inventory/getcountpick',
		data: { doc_no : doc_no },
		cache: false,
		success:function(data){	
		
			var each_count = data.split("//");
			//alert(each_count[0]+"--"+each_count[1]+"--"+each_count[2]);
			$("#txt_sum_pick").val(each_count[0]);
			$("#txt_sum_box").val(each_count[1]);
			$("#txt_lgs_status").val(each_count[2]);
		}
	});
}//getDataCount

function flgCommand(com,grid){
	//alert(com);
	if(com=='Refresh'){
		
		$("#grd_picking").flexOptions({newp:1}).flexReload();
		//alert("Refresh");
	
	}//if refresh
	if (com == 'Delete') {
		var del_list='';
		var del_list_show='';
		$('.trSelected', grid).each(function() {
			del_list+= $(this).attr('id');
			del_list_show+=$(this).attr('absid');
		});
		del_list=del_list.substring(3);
		//del_list_show=del_list_show.substring(0,del_list_show.length-1);
		//alert(del_list+"//"+del_list_show);
		jConfirm('คุณต้องการลบรายการ '+del_list_show+' ใช่หรือไม่?','ยืนยันการลบรายการ', function(r){
	        if(r){
				var opts={
						   type:"POST",
						   cache:false,
						   url: "/sales/inventory/delpicklist",
						   data:{ items:del_list },
						   //alert(items);
						   success: function(data){
									if(data=='-1'){
										 jAlert('ไม่สามารถลบรายการดังกล่าวได้','ข้อความแจ้งเตือน',function(){
											 return false;
										  });
									}else{
										$("#grd_picking").flexOptions({newp:1}).flexReload();
										getDataCount();
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
}//flgCommand

function cancelTmp()
{
	jConfirm('ยืนยันการยกเลิกรายการ','ยืนยันการลบรายการ' , function(r){
		if(r) 
		{
			clearTmp();			
		}
	});
	$("#txt_pl").focus();			
}//cancelTmp
function clearTmp()
{
	var txt_plno = $("#txt_plno").val();
	var opts={
		type:"POST",
		cache:false,
		data:{ txt_plno : txt_plno },
		url:"/sales/inventory/cleartmp",
		success:function(data){
			//alert(data);
			$("#grd_picking").flexOptions({newp:1}).flexReload();
			$("#txt_pl").val("");
			$("#txt_remark").val("");
			$("#txt_lgs_status").val("0");
			$("#txt_cbox").val("1");
			$("#txt_remark").focus();							
			getDataCount();
		}
	};
	$.ajax(opts);		
}
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


function printBill(doc_no,page){
	var url2print="/sales/inventory/reportpick";
	var rnd = $("#hdn_md5").val();
	//popup(url2print+"?doc_no="+doc_no+"&rnd="+rnd+"&page="+page,"",500,500);

	var opts={
			type:'get',
			url:url2print,
			cache:false,
			data:{
					doc_no:doc_no,
					actions:'print',
					page:page,
					rnd:rnd
				},
			success:function(){
					return false;
			}
		};
		$.ajax(opts);
		return false;
}//func

function saveTmp()
{		
	getDataCount();
	var txt_sum_pick = $("#txt_sum_pick").val();
	var txt_sum_box = $("#txt_sum_box").val();
	var txt_plno = $("#txt_plno").val();
	var round=0;
	if(parseInt(txt_sum_pick,10) > 0 && parseInt(txt_sum_box,10) > 0)
	{	
		//--------------------------------------------- insert box001 ---------------------------------------------------
		var dlg_box = {
				autoOpen: false,
				width:'auto',	
				height:'auto',	
				modal:true,
				stack: true,
				resizable:true,
				position:"center",
				showOpt: {direction: 'down'},		
				closeOnEscape:false,	
				title:"คืนกล่องส่งคลัง",
				open: function()
				{ 					
					$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
					$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
				    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb","margin":"0 0 0 0","font-size":"27px","color":"#000"});
				    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});							    			    

			    	$.ajax({
			    		type:"POST",
			    		data:{ doc_no : txt_plno },
			    		url:"/sales/inventory/getoldbox",
			    		success:function(data){
			    			$("#txt_box_qty").val(data);
			    			$("#txt_box_qty_r").val("").focus();
			    		}
			    	});	
				    
				},				
				close: function(evt,ui){	         
					$('#dlg_box').dialog('destroy');													
				},//close: function(evt,ui)			
				buttons: {
		            "บันทึก": function() {		
						if($("#txt_box_qty_r").val() < 0)
						{
							jAlert('จำนวนกล่องผิดปกติ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
								$("#txt_box_qty_r").focus();
								return false;
							});		
						}
						else
						{
							$("#txt_pl").val("box001");
							$("#txt_cbox").val($("#txt_box_qty_r").val());
							forInsert("box");
							
			                $("#dlg_box").dialog("close"); 
			                setTimeout(function(){
			                //--------------------------------------------- check emp id ---------------------------------------------------
			        		var dialogOpts_picking = {
			        			autoOpen: false,
			        			width:275,
			        			height:'auto',	
			        			modal:true,
			        			stack: true,
			        			resizable:true,
			        			position:"center",
			        			showOpt: {direction: 'down'},		
			        			closeOnEscape:false,	
			        			title:"<span class='ui-icon ui-icon-person'> </span> รหัสพนักงาน",
			        			open: function()
			        			{ 					
			        				$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
			        				$(this).parents(".ui-dialog:first").css({"padding":"3px","margin":"0 0 0 0","border-color":"#C6D5DC"});
			        				$(this).dialog('widget').find('.ui-dialog-titlebar').removeClass('ui-corner-all').addClass('ui-corner-top');
			        			    $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({"padding":"10px 5px","background-color":"#ebebeb","margin":"0 0 0 0","font-size":"27px","color":"#000","height":"50px"});
			        			    $(this).dialog("widget").find(".ui-dialog-buttonpane").css({"padding":".1em .1em .1em 0","margin":"0 0 0 0","background-color":"#C7D9DC"});							    			    
		
			        				$("#chk_saleman_id").val('').focus();
			        				
			        				$("#chk_saleman_id").keypress( function(evt) {	
			        					
			        					var key = evt.keyCode ? evt.keyCode : evt.which ? evt.which : evt.charCode; 						
			        					if(key == 13){
			        						round++;
			        						if(round <= 1)
			        						{
				        						evt.preventDefault();
				        						var chk_saleman_id=$("#chk_saleman_id").val();
				        						$("#emp").val(chk_saleman_id);
				        						var opts={
				        					    	type:"post",
				        					       	url:"/sales/inventory/getemp",
				        					   		async: true,
				        							data:{ employee_id : chk_saleman_id, actions : 'saleman' },
				        							success:function(data){
				        								var arr_data=data.split('#');
				        								arr_data[3]="Y"; 
				        								if($.trim(arr_data[0])=="")
				        								{
				        									jAlert('ไม่พบรหัสพนักงาน กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
				        										round--;
				        										$("#chk_saleman_id").val('').focus();
				        										return false;
				        						    		});
				        								}
				        								else if($.trim(arr_data[3])=='P')
				        								{
				        									jAlert('ขณะนี้พนักงานไม่อยู่ในระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
				        										round--;
				        										$("#chk_saleman_id").val('').focus();
				        											return false;
				        						    		});
				        								}
				        								else if($.trim(arr_data[3])=='N')
				        								{
				        									jAlert('ขณะนี้พนักงานไม่ได้ลงเวลาเข้าระบบ กรุณาตรวจสอบอีกครั้ง','ข้อความแจ้งเตือน',function(){
				        										round--;
				        										$("#chk_saleman_id").val('').focus();
				        										return false;
				        						    		});
				        								}
				        								else
				        								{
				        									//alert("SAVE");
				        									var emp = $("#emp").val();
				        									$.ajax({
				        										type : "POST",
				        										url : "/sales/inventory/savepicking",
				        										data : { emp : emp },
				        										cache : false,
				        										success:function(data){
				        											var arr_data=data.split('#');
				        											
				        											if(arr_data[0] == "1")
				        											{
				        												printBill(arr_data[1],"real");
				        												printBill(arr_data[1],"copy");
				        												$("#grd_picking").flexOptions({newp:data}).flexReload();
				        											}			
				        											else
				        											{
				        												jAlert('ไม่สามารถบันทึกข้อมูลได้','ผลการบันทึก',function(){
				        												 	return false;
				        											  	});	
				        											}		
		
				        											$('#dlg_inventory').dialog('close');	
				        											$("#txt_pl").val("");
				        											$("#txt_cbox").val("1");
				        											$("#txt_remark").val("");	
				        											$("#txt_plno").val(arr_data[2]);		
				        											$("#txt_lgs_status").val("0");									
				        											$("#grd_picking").flexOptions({newp:data}).flexReload();
				        											$("#txt_remark").focus();
				        											getDataCount();																
				        										}
				        									});//$.ajax			  														
				        								}
				        				            }//success:function(data)
				        						};//var opts
				        						$.ajax(opts);
			        						}//round
			        					}//if(key == 13)
			        				})//$("#chk_saleman_id").keypress( function(evt) 
			        			},				
			        			close: function(evt,ui){	         
			        				$('#dlg_inventory').dialog('destroy');
			        				if(evt.originalEvent && $(evt.originalEvent.target).closest(".ui-dialog-titlebar-close").length)
			        				{	
			        					evt.stopPropagation();
			                			evt.preventDefault();
			        				}
			        				else if(evt.originalEvent && $(evt.originalEvent.which==27))
			        				{
			        					evt.stopPropagation();
			        					evt.preventDefault();
			        				}													
			        			},//close: function(evt,ui)			
			        			buttons: {
			        	            "ยกเลิก": function() {		         
			        					
			        					$.ajax({
											type : "POST",
											url : "/sales/inventory/delbox",
											cache : false,
											success:function(data){									
											}
										});//$.ajax		
			        	                $("#dlg_inventory").dialog("close");   
			        	                $("#grd_picking").flexOptions({newp:1}).flexReload();
			        	                return false;            
			        	            }
			        	        }	
			        		};//var dialogOpts_picking			
			        		$('#dlg_inventory').dialog('destroy');
			        		$('#dlg_inventory').dialog(dialogOpts_picking);			
			        		$('#dlg_inventory').dialog('open');
			        		//--------------------------------------------- end check emp id ------------------------------------------------------------------
			                },200);
						}//else
		            }//ปุ่มบันทึก
		        }	
			};//var dialogOpts_picking			
			$('#dlg_box').dialog('destroy');
			$('#dlg_box').dialog(dlg_box);			
			$('#dlg_box').dialog('open');
					
	}//if(parseInt(txt_sum_pick) > 0 && parseInt(txt_sum_box) > 0)							
	else
	{
		 jAlert('กรุณาเพิ่มรายการ Picking List','ข้อความแจ้งเตือน',function(){
			 $("#txt_pl").focus();
			 return false;
		  });
	}
}//saveTmp
