var clicknode='';
var corporation_id='';
var company_id='';
var group_id='';
var rungroup='';
//---------------------------------	
var run_edituser='';
//---------------------------------	
$(function(){	
	$.get("/pos/admin/getbranchid",{
		ran:Math.random()
	},function(data){
		$('#branch_id').val(data);
	});
	
	tableusergroup('');
	//---------------------------------	
	$('#tgroup').tree({
		url: '/pos/admin/tgroup',
		checkbox: false,
		onClick:function(node){
			//tableusergroup(group_id);
		},onBeforeSelect: function(node){
			var arr = node.id;
			//alert(arr);
			var xarr= arr.split("@");
			switch(xarr[0]){
			case 'corporation_id':
				 clicknode=xarr[0];
				 corporation_id=xarr[1];
				 company_id=xarr[2];
				 group_id='';
				 tableusergroup('corporation_id',corporation_id);
				break;
			case 'group_id':
				 clicknode=xarr[0];
				 corporation_id=xarr[1];
				 company_id=xarr[2];
				 group_id=xarr[3];
				 tableusergroup('group_id',group_id);
				break;
			}
		},onContextMenu: function(e, node){
			//alert(node.id);
			e.preventDefault();
			$('#tgroup').tree('select', node.target);
			$('#mgroup').menu('show',{
				left: e.pageX,
				top: e.pageY
			});
		}
	});
	//---------------------------------	
	$( "#start_date" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});
	$( "#end_date" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});

	//---------------------------------	
	$('#divusergroup').dialog({
		buttons:[{
			text:'บันทึก',
			iconCls:'icon-ok',
			handler:function(){
				if(rungroup=='insert'){
					insertgroup();
				}else{
					updategroup();
				}
			}
		},{
			text:'Cancel',
			handler:function(){
				$('#divusergroup').dialog('close');
				resetForm();
			}
		}]
	}); 
	//---------------------------------	 
	$('#divusergroup').dialog('close');
	//---------------------------------	
	$('#divinsert').dialog({
		buttons:[{
			text:'บันทึก',
			iconCls:'icon-ok',
			handler:function(){
				if(run_edituser=='edit'){
					updateusertogroup();
				}else{
					insertusertogroup();
				}
			}
		},{
			text:'Cancel',
			handler:function(){
				$('#divinsert').dialog('close');
				resetForm();
			}
		}]
	}); 
	//---------------------------------	 
	$('#divinsert').dialog('close');
});
//----------------------------------	
var countClick=0;
function  tableusergroup(qtypes,querys){
	countClick++;
	if(countClick > 1){
		$('#fixgrids').flexOptions({query:querys,qtype: qtypes}).flexReload();
	}else{
		var w=(screen.width);
		//var h=(screen.height)-500;
		//var w=660;
		var h=480;
		$("#fixgrids").flexigrid
		(
			{	
			url:'/pos/admin/tableusergroup',
			dataType: 'json',
				colModel : [
					{display: 'รหัสบริษัท ', name : 'corporation_id', width :50, sortable : true, align: 'center'}, 
					{display: 'สาขา', name : 'cancel', width : 35, sortable : true, align: 'left'},
					{display: 'รหัสพนักงาน', name : 'employee_id', width :80, sortable : true, align: 'center'},
					{display: 'ชื่อ ', name : 'name', width : 100, sortable : true, align: 'left'},
					{display: 'นามสกุล', name : 'surname', width : 100, sortable : true, align: 'left'},
					{display: 'user_id ', name : 'user_id', width : 60, sortable : true, align: 'left'},
					{display: 'สถานะ', name : 'cancel', width : 30, sortable : true, align: 'left'},
					{display: '', name : 'cancel', width : 200, sortable : true, align: 'left'}
				],searchitems :[
					{display: 'กลุ่มพนักงาน', name  :'group_id', isdefault: true},
					{display: 'สาขา', name  :'branch_id', isdefault: true},
					{display: 'ชื่อ ', name  :'name'},
					{display: 'รหัสพนักงาน', name  :'employee_id'}
				],
				sortname: "group_id",
				sortorder: "ASC",
				usepager: true,
				title: 'ทะเบียนพนักงาน',
				useRp: true,
				rp: 30,
				query: querys,
				qtype: qtypes,
				showTableToggleBtn: true,
				width: w,
				height: h,
				//onSubmit: addForm,
				singleSelect: true,
				onSuccess:function(){
				}
			}
		);
	}
}
//----------------------------------------------------------------
function insertgroup(){
	if($('#ggroup_id').val()==''){
		$('#ggroup_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ รหัสประกลุ่มผู้ใช้ !','info');
		return false;
	}
	$.get("/pos/admin/checkdupgroup",{
		corporation_id:corporation_id,
		company_id:company_id,
		group_id:$('#ggroup_id').val(),
		ran:Math.random()
	},function(data){
		if(data=='ok'){
			$.get("/pos/admin/insertgroup",{
				corporation_id:corporation_id,
				company_id:company_id,
				group_id:$('#ggroup_id').val(),
				cancel:$('#gcancel').val(),
				remark:$('#gremark').val(),
				ran:Math.random()
			},function(data){
				$('#tgroup').tree('reload');
				$('#divusergroup').dialog('close');
				 resetForm();
			});
		}else{
			$.messager.alert('แจ้งผลการตรวจสอบ',data,'warning');
			return false;
		}

	});
}
//----------------------------------------------------------------
function resetForm(){
	 clicknode='';
	 corporation_id='';
	 company_id='';
	 group_id='';
	 run_edituser='';
	 rungroup='';
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
//----------------------------------------------------------------
function formaddgroup(){
	if(group_id==''){
		 $('#divinsert').dialog('close');
	}else{
		searchgroup();
		$('#divinsert').dialog('open');
	}
}
//----------------------------------------------------------------
function searchgroup(){
	$.get("/pos/admin/searchgroup",{
		corporation_id:corporation_id,
		company_id:company_id,
		ran:Math.random()},function(data){	
		$('#divgroupid').html(data);
		$('#group_id_in').val(group_id);
	});
}

//----------------------------------------------------------------
function insertusertogroup(){
	if($('#branch_id').val()==''){
		$('#branch_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ สาขา !','question');
		return false;
	}
	if($('#user_id').val()==''){
		$('#user_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ User เข้าระบบ !','question');
		return false;
	}
	if($('#password_id').val()==''){
		$('#password_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ Password !','question');
		return false;
	}
	if($('#group_id_in').val()==''){
		$('#group_id_in').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ กลุ่มพนักงาน !','question');
		return false;
	}
	if($('#employee_id').val()==''){
		$('#employee_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ รหัสพนักงาน !','question');
		return false;
	}
	if($('#name').val()==''){
		$('#name').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ชื่อ !','question');
		return false;
	}
	if($('#surname').val()==''){
		$('#surname').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ นามสกุล !','question');
		return false;
	}
	if($('#position').val()==''){
		$('#position').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ตำแหน่งงาน !','question');
		return false;
	}
	
	if($('#start_date').val()==''){
		$('#start_date').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ วันที่เริ่มต้นทำงาน !','question');
		return false;
	}
	
	$.get("/pos/admin/checkdupusertogroup",{
		corporation_id:corporation_id,
		company_id:company_id,
		user_id:$('#user_id').val(),
		employee_id:$('#employee_id').val(),
		ran:Math.random()
	},function(data){
		if(data=='ok'){
			$.get("/pos/admin/insertusertogroup",{
				corporation_id:corporation_id,
				company_id:company_id,
				branch_id:$('#branch_id').val(),
				user_id:$('#user_id').val(),
				password_id:$('#password_id').val(),
				group_id:$('#group_id_in').val(),
				employee_id:$('#employee_id').val(),
				name:$('#name').val(),
				surname:$('#surname').val(),
				position:$('#position').val(),
				start_date:$('#start_date').val(),
				end_date:$('#end_date').val(),
				cancel:$('#cancel').val(),
				remark:$('#remark').val(),
				ran:Math.random()
			},function(data){
				$('#divinsert').dialog('close');
				tableusergroup(group_id);
				resetForm();
			});
		}else{
			$.messager.alert('แจ้งผลการตรวจสอบ',data,'warning');
			return false;
		}

	});
}
//----------------------------------------------------------------
function updateusertogroup(){
	if($('#branch_id').val()==''){
		$('#branch_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ สาขา !','question');
		return false;
	}
	if($('#user_id').val()==''){
		$('#user_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ User เข้าระบบ !','question');
		return false;
	}
	if($('#password_id').val()==''){
		$('#password_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ Password !','question');
		return false;
	}
	if($('#group_id_in').val()==''){
		$('#group_id_in').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ กลุ่มพนักงาน !','question');
		return false;
	}
	if($('#employee_id').val()==''){
		$('#employee_id').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ รหัสพนักงาน !','question');
		return false;
	}
	if($('#name').val()==''){
		$('#name').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ชื่อ !','question');
		return false;
	}
	if($('#surname').val()==''){
		$('#surname').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ นามสกุล !','question');
		return false;
	}
	if($('#position').val()==''){
		$('#position').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ ตำแหน่งงาน !','question');
		return false;
	}
	
	if($('#start_date').val()==''){
		$('#start_date').focus();
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุ วันที่เริ่มต้นทำงาน !','question');
		return false;
	}
	
	$.get("/pos/admin/updateusertogroup",{
		idemp:$('#idemp').val(),
		corporation_id:corporation_id,
		company_id:company_id,
		branch_id:$('#branch_id').val(),
		user_id:$('#user_id').val(),
		password_id:$('#password_id').val(),
		group_id:$('#group_id_in').val(),
		employee_id:$('#employee_id').val(),
		name:$('#name').val(),
		surname:$('#surname').val(),
		position:$('#position').val(),
		start_date:$('#start_date').val(),
		end_date:$('#end_date').val(),
		cancel:$('#cancel').val(),
		remark:$('#remark').val(),
		ran:Math.random()
	},function(data){
		$('#divinsert').dialog('close');
		tableusergroup(group_id);
		resetForm();
	});
}

//--------------------------------------------------------------------------------------
function deluser(id){
	$.messager.confirm('ยืนยันการทำงาน', 'ท่านต้องการลบ ผู้ใช้นี้ใช่หรือไม่?', function(r){
		if (r){
			$.get("/pos/admin/deluser",{
				id:id,
				ran:Math.random()},function(data){
				var node = $('#tgroup').tree('getSelected');
				//tableusergroup(group_id);
				$('#fixgrids').flexOptions({query:id,qtype: id}).flexReload();
				resetForm('empform');
			});
		}
		return false;
	});
}
//--------------------------------------------------------------------------------------
function empformedit(id){
	$.getJSON("/pos/admin/empform",{
		id:id,
		ran:Math.random()
		},function(data){
    		$('#branch_id').val(data.branch_id);	
    		$('#user_id').val(data.user_id);
    		$('#password_id').val(data.password_id);	
    		$('#employee_id').val(data.employee_id);	
    		$('#name').val(data.name);
    		$('#surname').val(data.surname);
    		$('#position').val(data.position);
    		$('#start_date').val(data.start_date);	
    		$('#end_date').val(data.end_date);	
    		$('#cancel').val(data.cancel);	
    		$('#remark').val(data.remark);
    		$('#idemp').val(id);
    		//searchcompany2(data.company_id);
			 clicknode='fixgrids';
			 corporation_id=data.corporation_id;
			 company_id=data.company_id;
			 group_id=data.group_id;
			 searchgroup();
			 run_edituser='edit';
    		$('#divinsert').dialog('open');
	});
		
}
//--------------------------------------------------------------------------------------

function cancel(){
	if(group_id==''){
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุคลิกที่กลุ่มพนักงาน !','info');
		return false;
	}
	$.messager.confirm('ยืนยันการทำงาน', 'ท่านต้องการ ยกเลิก สถานะการใช้งาน กลุ่มผู้ใช้นี้ใช่หรือไม่ ?', function(r){
		if (r){
			$.get("/pos/admin/cancelgroup",{
				 corporation_id:corporation_id,
				 company_id:company_id,
				 group_id:group_id,
				ran:Math.random()},function(data){
					tableusergroup(group_id);
					resetForm();
			});
		}
	});
}
//--------------------------------------------------------------------------------------
function active(){
	if(group_id==''){
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุคลิกที่กลุ่มพนักงาน !','info');
		return false;
	}
	$.messager.confirm('ยืนยันการทำงาน', 'ท่านต้องการ  อนุมัติ สถานะการใช้งาน กลุ่มผู้ใช้นี้ใช่หรือไม่ ?', function(r){
		if (r){
			$.get("/pos/admin/activegroup",{
				 corporation_id:corporation_id,
				 company_id:company_id,
				 group_id:group_id,
				ran:Math.random()},function(data){
					tableusergroup(group_id);
					resetForm();
			});
		}
	});
}
//--------------------------------------------------------------------------------------
function append(){
	$('#divusergroup').dialog('open');
	rungroup='insert';
}
//--------------------------------------------------------------------------------------
function remove(){
	if(group_id==''){
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุคลิกที่กลุ่มพนักงาน !','info');
		return false;
	}
	$.messager.confirm('ยืนยันการทำงาน', 'ท่านต้องการ ลบ  กลุ่มผู้ใช้นี้ใช่หรือไม่ ?', function(r){
		if (r){
			$.get("/pos/admin/delgroup",{
				 corporation_id:corporation_id,
				 company_id:company_id,
				 group_id:group_id,
				 ran:Math.random()},function(data){
					 $('#tgroup').tree('reload');
					tableusergroup(group_id);
					resetForm();
			});
		}
	});
}
//--------------------------------------------------------------------------------------
function formeditgroup(){
	$.getJSON("/pos/admin/editgroup",{
		 corporation_id:corporation_id,
		 company_id:company_id,
		 group_id:group_id,
		 ran:Math.random()
		},function(data){
			$('#idgroup').val(data.id);
			$('#ggroup_id').val(data.group_id);
			$('#gcancel').val(data.cancel);	
			$('#gremark').val(data.remark);	
			$('#divusergroup').dialog('open');
			rungroup='edit';
	});
}
//--------------------------------------------------------------------------------------
function updategroup(){
	if(group_id==''){
		$.messager.alert('แจ้งผลการตรวจสอบ','กรุณาระบุรหัสประกลุ่มผู้ใช้ !','info');
		return false;
	}
	$.messager.confirm('ยืนยันการทำงาน', 'ท่านต้องการ  แก้ไขข้อมูล  กลุ่มผู้ใช้นี้ใช่หรือไม่ ?', function(r){
		if (r){
			$.get("/pos/admin/updategroup",{
				 id:$('#idgroup').val(),
				 corporation_id:corporation_id,
				 company_id:company_id,
				 group_id:$('#ggroup_id').val(),
				 group_id_old:group_id,
				 gcancel:$('#gcancel').val(),
				 remark:$('#gremark').val(),
				 ran:Math.random()},function(data){
					 $('#divusergroup').dialog('close');
					 $('#tgroup').tree('reload');
					 resetForm();
			});
		}
	});
}
//--------------------------------------------------------------------------------------














