//------------------------------
var treesysmmenu='';
var menu_ref='';
var menu_seq='';
var menu_name='';
var menu_exec='';
var menu_picture='';
var status_menu='';
var runsys='';
//------------------------------
var treeclick='';
var perm_id='';
var perm_id_old='';
var remark='';
var corporation_id='';
var company_id='';
var per_menu_id='';
var menu_id='';
var run='';
//--------------------------
var gtreeclick='';
var gid='';
var gcorporation_id='';
var gcompany_id='';
var group_id='';
var gperm_id='';
var grun='';
//--------------------------
var hiddenclicktree='';
//--------------------------



$(function(){
	//----------------------------------
	//$('#treesysmenu')
    jQuery(document).bind('keydown', 'del',function (evt){
	
		switch(hiddenclicktree){
			case 'treesysmenu':
				var node = $('#treesysmenu').tree('getSelected');
				delcommenuid_no_mg(node.id);
			break;
			
			case 'treepermission':
				var node = $('#treepermission').tree('getSelected');
				removeemenupermission_no_msg(node.id);
				//delcommenuid_no_mg(node.id);
			break;
			
			case 'treeusergroup':
				var node = $('#treeusergroup').tree('getSelected');
				alert(node.id);
			break;
		}
    
    	return false; 
    });

	//----------------------------------
	//tableusergroup();
	$('#divmenusystem').dialog({
		buttons:[{
			text:'Ok',
			iconCls:'icon-ok',
			handler:function(){
				//alert(runsys);
				
				if(runsys=='insertnewmenu'){
					//alert($('#menu_picture').val());
					addcommenuid();
				}else{
					//alert('22222');
					updatecommenuid();
				}	
			}
		},{
			text:'Cancel',
			handler:function(){
				resetForm();
				$('#divmenusystem').dialog('close');
			}
		}]
	});
	$('#divmenusystem').dialog('close');	
	//----------------------------------
	$('#divpermission').dialog({
		buttons:[{
			text:'Ok',
			iconCls:'icon-ok',
			handler:function(){
				if(run=="edit"){
					updatepermission();
				}else{
					addpermission();
				}	
			}
		},{
			text:'Cancel',
			handler:function(){
				$('#divpermission').dialog('close');
			}
		}]
	});
	$('#divpermission').dialog('close');	
	$('#divgroup').dialog({
		buttons:[{
			text:'Ok',
			iconCls:'icon-ok',
			handler:function(){
				
				if(grun=="edit"){
					updategroup();
				}else{
					insertnewgroup();
				}	
			}
		},{
			text:'Cancel',
			handler:function(){
				$('#divgroup').dialog('close');
			}
		}]
	});

	$('#divgroup').dialog('close');	
	//----------------------------------
	$('#treesysmenu').tree({ 
		url: '/pos/admin/treesysmenu',
		onContextMenu: function(e, node){
			e.preventDefault();
			$('#treesysmenu').tree('select', node.target);
			$('#mtreesysmenu').menu('show', {
				left: e.pageX,
				top: e.pageY
			});
		},onBeforeSelect: function(node){
			hiddenclicktree='treesysmenu';
			//resetForm();
			menu_id=node.id;
			menu_ref=node.id;
			treesysmmenu='menu_id';
		}
	});
	//----------------------------------
	$('#treepermission').tree({
		url: '/pos/admin/treepermission',
		onContextMenu: function(e, node){
				e.preventDefault();
				$('#treepermission').tree('select', node.target);
				$('#mtreepermission').menu('show',{
					left: e.pageX,
					top: e.pageY
				});
			
		},onBeforeSelect: function(node){
			hiddenclicktree='treepermission';
			var arr = node.id;
			var xarr= arr.split("@");
			//alert(xarr);
			switch(xarr[0]){
			case 'com_permission':
				 treeclick=xarr[0];
				 perm_id=xarr[1];
				 perm_id_old=xarr[1];
				 remark=xarr[2];
				break;
			case 'com_menu':
				 treeclick=xarr[0];
				 perm_id=xarr[1];
				 perm_id_old=xarr[1];
				 per_menu_id=xarr[2];
				break;
			}
		}
	});

	//----------------------------------
	$('#treeusergroup').tree({
		url: '/pos/admin/treeusergroup',
		checkbox: false,
		onClick:function(node){
		},onContextMenu: function(e, node){
			e.preventDefault();
			$('#treeusergroup').tree('select', node.target);
			$('#mgroup').menu('show',{
				left: e.pageX,
				top: e.pageY
			});
		},onBeforeSelect: function(node){
				hiddenclicktree='treeusergroup';
				var arr = node.id;
				var xarr= arr.split("@");
				switch(xarr[0]){
				case 'country_id':
					resetForm();
				break;
				case 'corporation_id':
					 gtreeclick=xarr[0];
					 gcorporation_id=xarr[1];
					 gcompany_id=xarr[2];
				break;
				case 'conf_usergroup':
					 gtreeclick=xarr[0];
					 gid=xarr[1];
					 gcorporation_id=xarr[2];
					 gcompany_id=xarr[3];
					 group_id=xarr[4];
					 gcancel=xarr[5];
					 gremark=xarr[6];
				break;
				
				case 'conf_permission_access':
					 gtreeclick=xarr[0];
					 gid=xarr[1];
					 gcorporation_id=xarr[2];
					 gcompany_id=xarr[3];
					 group_id=xarr[4];
					 gperm_id=xarr[5];
				break;
				}		
		}
	});
	//----------------------------------
});
//----------------------------------------------------------------
function addmenusep(){
	$.get("/pos/admin/addmenusep",{
		menu_id:menu_id,
		ran:Math.random()
	},function(data){
		var node = $('#treepermission').tree('getSelected');
		$('#treepermission').tree('reload');
	});
}
//----------------------------------------------------------------
function insertnewgroup(){
	$.get("/pos/admin/checkhavegroup",{
		corporation_id:gcorporation_id,
		company_id:gcompany_id,
		group_id:$('#group_id').val(),
		ran:Math.random()
	},function(data){
		if(data=='ok'){
			$.get("/pos/admin/insertnewgroup",{
				corporation_id:gcorporation_id,
				company_id:gcompany_id,
				group_id:$('#group_id').val(),
				cancel:$("#cancel").val(),
				remark:$("#remark_group").val(),
				ran:Math.random()
			},function(data){
				$('#divgroup').dialog('close');
				$('#treeusergroup').tree('reload');
				resetForm();
			});
		}else{
			alert(data);
		}
	});
}
//----------------------------------------------------------------
function removepermissioningroup(){
	switch(gtreeclick){
	case 'conf_permission_access':
		delpermidingroup();
	  break;
	case 'conf_usergroup':
			//$.messager.alert('แจ้งเตือน','สำหรับยกเลิก  permission เท่านั้น กรุณาเลือก  perm_id !','warning');
		$.get("/pos/admin/checkperingroup",{
			perm_id:perm_id,
			corporation_id:gcorporation_id,
			company_id:gcompany_id,
			group_id:group_id,
			ran:Math.random()
			},function(data){
			if(data =="ok"){
				$.messager.alert('แจ้งเตือน','Group นี้ยังไม่ได้กำหนด Permission ไม่สามารถลบได้!','warning');
			}else{
				delpermidingroup();
			}
		});
	  break;
	}

}

function delpermidingroup(){
	$.messager.confirm('ยืนยัน', 'ท่านต้องการลบ permission '+gperm_id+' ออกจาก Group '+group_id+' ใช้หรือไม่ ?', function(r){
		if (r){
			$.get("/pos/admin/removepermidingroup",{
				group_id:group_id,
				perm_id:gperm_id,
				corporation_id:gcorporation_id,
				company_id:gcompany_id,
				ran:Math.random()
			},function(data){
				$('#treeusergroup').tree('reload');
				//var node = $('#treeusergroup').tree('getSelected');
				//$('#treeusergroup').tree('remove', node.target);
				resetForm();
			});
		}
	});
}


//----------------------------------------------------------------
function removegroup(){
	$.messager.confirm('ยืนยัน', 'ท่านต้องการลบ  Group '+group_id+' ใช้หรือไม่ ?', function(r){
		if (r){
			$.get("/pos/admin/removegroup",{
				group_id:group_id,
				corporation_id:gcorporation_id,
				company_id:gcompany_id,
				ran:Math.random()
			},function(data){
				$('#treeusergroup').tree('reload');
				//var node = $('#treeusergroup').tree('getSelected');
				//$('#treeusergroup').tree('remove', node.target);
				resetForm();
			});
		}
	});
}
//----------------------------------------------------------------
function reloadPermission(){
	var node = $('#treepermission').tree('getSelected');
	$('#treepermission').tree('reload');
	/*
	alert(node.id );
	if (node.id !=''){
		$('#treepermission').tree('reload', node.target);
	} else {
		$('#treepermission').tree('reload');
	}
	*/
}
//----------------------------------------------------------------
function reloadmenuid(){
	var node = $('#treesysmenu').tree('getSelected');
	if (node.id !=''){
		$('#treesysmenu').tree('reload', node.target);
	} else {
		$('#treesysmenu').tree('reload');
	}
}

function formmenusystem(){
	runsys='insertnewmenu';
	$('#menu_ref').val(menu_ref);
	$('#divmenusystem').dialog('open');
	$('#iconmenu').html("");
	$.get("/pos/admin/formmenusystem",{
		menu_ref:$("#menu_ref").val(),
		ran:Math.random()
	},function(data){
		$("#menu_seq").val(data);
	});
}

function addcommenuid(){
	//
	if($('#menu_ref').val()==""){
		//alert('กรุณาเลือก เมนูอ้างอิง');
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ เมนูอ้างอิง!','info');
		return false;
	}
	if($('#menu_seq').val()==""){
		//alert('กรุณาเลือก ลำดับของ menu');
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ลำดับของ menu!','info');
		return false;
	}
	if($('#menu_name').val()==""){
		//alert('กรุณาเลือก ชื่อของ menu');
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ชื่อของ menu!','info');
		return false;
	}
	if($('#menu_exec').val()==""){
		//$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ url  ที่เรียกใช้จาก menu!','info');
		//alert('กรุณาระบุ url  ที่เรียกใช้จาก menu');
	}

	/*
	var win = $.messager.progress({
		title:'Please waiting',
		msg:'Loading data...'
	});
	 */

	var ulrl_name="/pos/admin/addcommenuid?menu_ref="+$("#menu_ref").val()+
	"&menu_seq="+$("#menu_seq").val()+
	"&menu_name="+$("#menu_name").val()+
	"&menu_exec="+$("#menu_exec").val()+
	"&status_menu="+$('#status_menu').val()+
	"&ran="+Math.random();
	//alert(ulrl_name);
	ajaxUp(ulrl_name,'menu_picture');
}
//--------------------------------------------------------------------------------------
function ajaxUp(nameurl,fileupload){
	$.ajaxFileUpload({
			url:nameurl,
			secureuri:false,
			fileElementId:fileupload,
			dataType: 'json',
		    success: function (data, status)
		    {
		        if(data.success =='ok')
		        {
					$('#treesysmenu').tree('reload');
					$('#treepermission').tree('reload');
					$('#divmenusystem').dialog('close');
					
					$("#menu_seq").val("");
					$("#menu_name").val("");
					$("#menu_exec").val("");
					$("#menu_picture").val("");
					
		        }else{
		        	alert('ผิดพลาด กรุณาลองใหม่อีกครั้งหนึ่ง !!');
		        }
		    
		    }

			
	});


}
//--------------------------------------------------------------------------------------
function editcommenuid(){
	$.getJSON("/pos/admin/editcommenuid",{
		menu_id:menu_id,
		ran:Math.random()
		},function(data){
		menu_id=data.menu_id;
		$('#menu_ref').val(data.menu_ref);	
		$('#menu_seq').val(data.menu_seq);	
		$('#menu_name').val(data.menu_name);	
		$('#menu_exec').val(data.menu_exec);	
		$('#status_menu').val(data.status_menu);
		//$('#hmenu_picture').val(data.menu_picture);
		$('#iconmenu').html("<img src='"+data.menu_picture+"' width='60' height='60' />");
		$('#divmenusystem').dialog('open');
		runsys='updatemenu';
	});
}

function updatecommenuid(){
	if($('#menu_ref').val()==""){
		//alert('กรุณาเลือก เมนูอ้างอิง');
		return false;
	}
	if($('#menu_seq').val()==""){
		//alert('กรุณาเลือก ลำดับของ menu');
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ลำดับของ menu!','info');
		return false;
	}
	
	if($('#menu_name').val()==""){
		//alert('กรุณาเลือก ชื่อของ menu');
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ชื่อของ menu!','info');
		return false;
	}
	
	if($('#menu_exec').val()==""){
		//alert('กรุณาเลือก url  ที่เรียกใช้จาก menu');
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ  url ที่เรียกใช้จาก menu!','info');
		return false;
	}
	/*
	var win = $.messager.progress({
		title:'Please waiting',
		msg:'Loading data...'
	});
	*/
	
	var menu_picture="";
	if($("#menu_picture").val()==""){
		
		$.get("/pos/admin/updatecommenuidnoimg",{
			menu_ref:$("#menu_ref").val(),
			menu_seq:$("#menu_seq").val(),
			menu_name:$("#menu_name").val(),
			menu_exec:$("#menu_exec").val(),
			status_menu:$("#status_menu").val(),
			menu_id:menu_id,
			ran:Math.random()
		},function(data){
			var node = $('#treesysmenu').tree('getSelected');
			//$(this).tree('reload', node.target);
			$('#treesysmenu').tree('reload',node.target);
			$('#treesysmenu').tree('reload');
			$('#treepermission').tree('reload');
			$('#divmenusystem').dialog('close');
			//$.messager.progress('close');
			$("#menu_seq").val("");
			$("#menu_name").val("");
			$("#menu_exec").val("");
			$("#menu_picture").val("");
		});
		
	}else{
		menu_picture='menu_picture';
		var ulrl_name="/pos/admin/updatecommenuid?menu_ref="+
		$("#menu_ref").val()+
		"&menu_seq="+$("#menu_seq").val()+
		"&menu_name="+$("#menu_name").val()+
		"&menu_exec="+$("#menu_exec").val()+
		"&status_menu="+$("#status_menu").val()+
		"&menu_id="+menu_id+
		"&ran="+Math.random();
		ajaxUp(ulrl_name,'menu_picture');
		//$('#divmenusystem').dialog('close');
	}
}

//--------------------------------------------------------------------------------------
function delcommenuid(){
	$.messager.confirm('ยืนยัน', 'ท่านต้องการลบ เมนูนี้ ใช่หรือไม่ ?', function(r){
		if (r){
			var j=0;
			var nodes = $('#treesysmenu').tree('getChecked');
			var lengths = nodes.length;
			if(lengths == 0){
				if(menu_id !=''){
					$.get("/pos/admin/delcommenuid",{
						menu_id:menu_id,
						ran:Math.random()},function(data){
							var node = $('#treesysmenu').tree('getSelected');
							$('#treesysmenu').tree('remove', node.target);
							$('#treepermission').tree('reload');
							
							//reloadtreesysmenu(Math.random());
					});
				}else{

					$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาคลิกเลือก  menu_id!','info');
					//alert('กรุณาเลือก  menu_id ');
					return false;
				}
			}else{
				for(var i=0; i< lengths; i++){
					$.get("/pos/admin/delcommenuid",{
						menu_id:nodes[i].id,
						ran:Math.random()},function(data){
							var node = $('#treesysmenu').tree('getSelected');
							$('#treesysmenu').tree('reload',nodes.target);
							$('#treepermission').tree('reload');
					});
				}
				
			}
		}
	});
}

//--------------------------------------------------------------------------------------
function delcommenuid_no_mg(menu_id){
	//alert(menu_id);
	$.get("/pos/admin/delcommenuidnomg",{
		menu_id:menu_id,
		ran:Math.random()},function(data){
			var node = $('#treesysmenu').tree('getSelected');
			$('#treesysmenu').tree('reload',menu_id);
			$('#treesysmenu').tree('reload');
	});
}
//----------------------------------------------------------------
function removemenuinper(){
	if(per_menu_id!=""){
		$.get("/pos/admin/removemenuinper",{
			menu_id:per_menu_id,
			perm_id:perm_id,
			ran:Math.random()
		},function(data){
			//$('#treepermission').tree('reload');
			var node = $('#treepermission').tree('getSelected');
			$('#treepermission').tree('remove', node.target);
			resetForm();
		});
	}
}
//----------------------------------------------------------------
function removeemenupermission_no_msg(perm_id){
	var node_id=perm_id;
	var arr = perm_id;
	var xarr= arr.split("@");
	
	switch(xarr[0]){
	case 'com_permission':
		delpermission(node_id,xarr[1],xarr[3]);
		break;
	case 'com_menu':
		delmenuinoermission(node_id,xarr[1],xarr[2]);
		break;
	}
}
function delpermission(node_id,perm_id,id){
	$.get("/pos/admin/delpermission",{
		id:id,
		perm_id:perm_id,
		ran:Math.random()
	},function(data){
		$('#treepermission').tree('reload',node_id);
		$('#treepermission').tree('reload');
	});
}
function delmenuinoermission(node_id,perm_id,menu_id){
	$.get("/pos/admin/delmenuinoermission",{
		menu_id:menu_id,
		perm_id:perm_id,
		ran:Math.random()
	},function(data){
		$('#treepermission').tree('reload',node_id);
		$('#treepermission').tree('reload');
	});
}
//----------------------------------------------------------------
function removeemenupermission(){
	$.messager.confirm('ยืนยันการลบ', 'ท่านต้องการลบ permission นี้ใช่หรือไม่ ? (คำเตือน  การลบอาจทำให้ กลุ่มผู้ใช้ ที่มี permission นี้เข้าใช้งานไม่ได้ )', function(r){
		if (r){
			$.get("/pos/admin/removepermission",{
				perm_id:perm_id,
				ran:Math.random()
			},function(data){
				//var node = $('#treepermission').tree('getSelected');
				$('#treepermission').tree('reload');
				//$('#treepermission').tree('remove', node.target);
				resetForm();
			});
		}
	});
}

var countIn=0;
var loop = 0;

//----------------------------------------------------------------
function pastemenu(){
	var nodes = $('#treesysmenu').tree('getChecked');
	var lengths = nodes.length;
	loop=lengths;
	if(lengths > 0){
		if(perm_id==''){
			alert('กรุณาเลือก perm_id ก่อน');
			return false;
		}
		for(var i=0; i< lengths; i++){
			//pastemenucheck(nodes[i].id);
			countIn++;
			/*
			$.get("/pos/admin/pastemenu",{
				menu_id:nodes[i].id,
				perm_id:perm_id,
				ran:Math.random()
			},function(data){ 			
				if(countIn==loop){
					$('#treepermission').tree('reload');
				}
			});
			*/
			$.ajax({
				  url: "/pos/admin/pastemenu",
				  cache: false,
				  data: {menu_id:nodes[i].id,perm_id:perm_id,ran:Math.random()},
				  success: function(html){
						countIn++;
						if(countIn==loop){
							$('#treepermission').tree('reload');
						}
				  }
			});
		}

		
	}else{
			if(perm_id==''){
				alert('กรุณาเลือก perm_id ก่อน');
				return false;
			}
			$.get("/pos/admin/checkdupper",{
				menu_id:menu_id,
				perm_id:perm_id,
				ran:Math.random()
			},function(data){
					if(data=='ok'){
						if(menu_id !=""){
							$.get("/pos/admin/pastemenu",{
								menu_id:menu_id,
								perm_id:perm_id,
								ran:Math.random()
							},function(data){
								$('#treepermission').tree('reload');
							});
						}
						
					}else{
						//alert(data);
						$.messager.alert('แจ้งเตือน',data,'info');
						return false;
					}
			});
	}
	
}


/*
//----------------------------------------------------------------
function pastemenucheck(mid){
	$.get("/pos/admin/checkdupper",{
		menu_id:mid,
		perm_id:perm_id,
		ran:Math.random()
	},function(data){
			if(data=='ok'){
				if(mid !=""){
					$.get("/pos/admin/pastemenu",{
						menu_id:mid,
						perm_id:perm_id,
						ran:Math.random()
					},function(data){ 
						if(countIn==loop){
							$('#treepermission').tree('reload');
						}
					});
				
				}
			}else{
				$('#treepermission').tree('reload');
			}
	});
}
*/
//----------------------------------------------------------------
function divpermission(){
	$("#perm_id_insert").val('');
	$("#remark").val('');
	$('#divpermission').dialog('open');
	run="add";
}

//----------------------------------------------------------------
function addpermission(){
	$.get("/pos/admin/addpermission",{
		perm_id:$('#perm_id_insert').val(),
		remark:$("#remark").val(),
		ran:Math.random()
	},function(data){
		$('#divpermission').dialog('close');
		$('#treepermission').tree('reload');
		resetForm();
	});
}
//----------------------------------------------------------------
function editpermission(){
	run="edit";
	$("#perm_id_insert").val(perm_id);
	$("#remark").val(remark);
	$('#divpermission').dialog('open');
}
//----------------------------------------------------------------
function updatepermission(){
	$.get("/pos/admin/updatepermission",{
		perm_id:$('#perm_id_insert').val(),
		remark:$("#remark").val(),
		perm_id_old:perm_id_old,
		ran:Math.random()
	},function(data){
		
		var node = $('#treepermission').tree('getSelected');
		
		//$('#treepermission').tree('reload', node.target);
		$('#treepermission').tree('reload');
		$('#divpermission').dialog('close');
		resetForm();
	});
}

//----------------------------------------------------------------
function copymenuid(){
	var node = $('#treesysmenu').tree('getSelected');
	menu_id=node.id;
	//alert(node.id);
	//$("#menu_id").val(node.id);
}

//----------------------------------------------------------------
function appendgroup(){
	$('#divgroup').dialog('open');	
}
//----------------------------------------------------------------
function pastepertogroup(){
	$.get("/pos/admin/checkperingroup",{
		perm_id:perm_id,
		corporation_id:gcorporation_id,
		company_id:gcompany_id,
		group_id:group_id,
		ran:Math.random()
		},function(data){
		if(data =="ok"){
			$.get("/pos/admin/pastepertogroup",{
				perm_id:perm_id,
				corporation_id:gcorporation_id,
				company_id:gcompany_id,
				group_id:group_id,
				ran:Math.random()
			},function(data){
				//var node = $('#treeusergroup').tree('getSelected');
				//$('#treeusergroup').tree('reload', node.target);
				$('#treeusergroup').tree('reload');
				//$('#treeusergroup').tree('remove', node.target);
				//resetForm();
			});
		}else{
			alert(data);
		}
	});
}

//--------------------------------------------------------------------------------------
function editgroup(){
	$.getJSON("/pos/admin/editgroup",{
		group_id:group_id,
		corporation_id:gcorporation_id,
		company_id:gcompany_id,
		ran:Math.random()
		},function(data){
			if(data.group_id !=''){
				grun="edit";
				$('#group_id').val(data.group_id);	
				$('#cancel').val(data.cancel);	
				$('#remark_group').val(data.remark);	
				$('#divgroup').dialog('open');
			}else{
				$.messager.alert('แจ้งเตือน','กรุณาคลิก  group_id !','info');
			}
	});
}
//--------------------------------------------------------------------------------------
function updategroup(){
	$.get("/pos/admin/updategroup",{
		corporation_id:gcorporation_id,
		company_id:gcompany_id,
		group_id:$('#group_id').val(),
		group_id_old:group_id,
		cancel:$('#cancel').val(),
		remark :$('#remark_group').val(),
		ran:Math.random()
	},function(data){
		$('#divgroup').dialog('close');
		$('#treeusergroup').tree('reload');
		resetForm();
	});
}
//--------------------------------------------------------------------------------------
function resetForm() {
	//------------------------------
	 treesysmmenu='';
	 menu_ref='';
	 menu_seq='';
	 menu_name='';
	 menu_exec='';
	 menu_picture='';
	 status_menu='';
	 runsys='';
	//------------------------------
	 treeclick='';
	 perm_id='';
	 perm_id_old='';
	 remark='';
	 corporation_id='';
	 company_id='';
	 per_menu_id='';
	 menu_id='';
	 run='';
	//--------------------------
	 gtreeclick='';
	 gid='';
	 gcorporation_id='';
	 gcompany_id='';
	 group_id='';
	 gperm_id='';
	 grun='';
	$(".easyui-layout").find(':input').each(function() {
	        switch(this.type) {
	            case 'password':
	            case 'select-multiple':
	            case 'select-one':
	            case 'text':
	            case 'textarea':
	                $(this).val('');
	                break;
	            case 'checkbox':
	            case 'radio':
	                this.checked = false;
	        }
	 });
}


