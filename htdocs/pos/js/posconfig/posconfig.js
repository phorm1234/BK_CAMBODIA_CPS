//var w=(screen.width)-550;
//var h=(screen.height)-335;
var country_id="";
var corporation_id="";
var company_id="";
var branch_id="";
var branch_no="";
var id_config="";
var fTable="";
var query="";
var qtype="";
var id ="";
$(function(){
	//----------------------------------
	$('#dialogAddCodeType').dialog({
		buttons:[{
			text:'Ok',
			iconCls:'icon-ok',
			handler:function(){
				saveAddCodeType();
			}
		},{
			text:'Cancel',
			handler:function(){
				resetFormAddCodeType();
				$('#dialogAddCodeType').dialog('close');
			}
		}]
	});
	$('#dialogAddCodeType').dialog('close');
	//----------------------------------
	$("#start_date").datepicker({
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'], 
		monthNamesShort: ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
		changeMonth: true,
		changeYear: true,
		yearRange:'-0:+1'
	});
		
	$("#end_date").datepicker({
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'], 
		monthNamesShort: ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
		changeMonth: true,
		changeYear: true,
		yearRange:'-0:+1'
	});
	//----------------------------------
	$('#table_posconfig').click(function() {
		$('.trSelected',this).each(function() {
				id = $(this).attr('id');
				id = id.substring(id.lastIndexOf('row')+3);
				getCodeType(id);
		});
	}); 
	posconfig();
	//----------------------------------
	$('#treeposconfic').tree({ 
		url: '/pos/admin/posconfigtree',
		onClick:function(node){
			//alert('dsfdsfdsf');
		},onContextMenu: function(e, node){
				e.preventDefault();
				$('#treeposconfic').tree('select', node.target);
				var arr = node.id;
				var xarr= arr.split("@");
				
				switch(xarr[0]){
					case 'com_company':
						$('#mtree_company').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
						break;
					case 'com_branch':
						$('#mtree_branch').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
						break;
				}
		},onBeforeSelect: function(node){
			var arr = node.id;
			var xarr= arr.split("@");
			switch(xarr[0]){
				case 'com_company':
					 corporation_id=xarr[1];
					 company_id=xarr[2];
					 branch_id="";
					 branch_no="";
					break;
				case 'com_branch':
					 corporation_id=xarr[1];
					 company_id=xarr[2];
					 branch_id=xarr[3];
					 branch_no=xarr[3];
					break;
			}
		}
	});

	//----------------------------------
	//onDblClick:function(node){
	$('#treeposconfiglist').tree({ 
		url: '/pos/admin/treeposconfiglist',
		onClick:function(node){
			getposconfiglist();
		},onContextMenu: function(e, node){
			e.preventDefault();
			$('#treeposconfiglist').tree('select', node.target);
			var arr = node.id;
			var xarr= arr.split("@");
			switch(xarr[0]){
				case 'config_branch':
				$('#mconfig_branch').menu('show', {
					left: e.pageX,
					top: e.pageY
				});
				break;
				case 'id_config':
					$('#m_id_config').menu('show', {
						left: e.pageX,
						top: e.pageY
					});
					break;
				case 'com_pos_config':
					$('#m_com_pos_config').menu('show', {
						left: e.pageX,
						top: e.pageY
					});
					break;
			}
		},onBeforeSelect: function(node){
			var arr = node.id;
			var xarr= arr.split("@");
			switch(xarr[0]){
			case 'id_config':
				fTable="com_pos_config_menu";
				id_config=xarr[1];
				resetForm_cof();
				$('#BtDel_code_type').show();
				break;
			case 'com_pos_config':
				 fTable=xarr[0];//"com_pos_config";
				 corporation_id=xarr[1];
				 company_id=xarr[2];
				 branch_id=xarr[3];
				 branch_no=xarr[4];
				 id_config=xarr[5];
				 resetForm();
				 $('#BtDel_code_type').hide();
				break;
			}
		}
	});
	
	//----------------------------------
});
//--------------------------------------------------------------------------------------
function resetForm(){
	$("#code_type").val("");
	$("#value_type").val("");
	$("#default_status").val("");
	$("#condition_status").val("");
	$("#default_day").val("");
	$("#condition_day").val("");
	$("#start_date").val("");
	$("#end_date").val("");
	$("#start_time").val("");
	$("#end_time").val("");
	id="";
	/*
	$(".insert_form").find(':input').each(function() {
		$(this).val('');
		id="";
	});
	*/
}

function resetFormAddCodeType(){
	id="";
	$("#corporation_id_add").val("");
	$("#company_id_add").val("");
	$("#branch_id_add").val("");
	$("#branch_no_add").val("");
	$("#code_type_add").val("");
	$("#value_type_add").val("");
	$("#default_status_add").val("");
	$("#condition_status_add").val("");
	$("#default_day_add").val("");
	$("#condition_day_add").val("");
	$("#start_date_add").val("");
	$("#end_date_add").val("");
	$("#start_time_add").val("");
	$("#end_time_add").val("");
	/*
	$(".insert_form").find(':input').each(function() {
		$(this).val('');
		id="";
	});
	*/
}



//--------------------------------------------------------------------------------------
function resetForm_cof(){
	corporation_id="";
	company_id="";
	branch_id="";
	branch_no="";
	id="";
	$("#code_type").val("");
	$("#value_type").val("");
	$("#default_status").val("");
	$("#condition_status").val("");
	$("#default_day").val("");
	$("#condition_day").val("");
	$("#start_date").val("");
	$("#end_date").val("");
	$("#start_time").val("");
	$("#end_time").val("");
	//$(".insert_form").find(':input').each(function() {
		//$(this).val('');
	//});
}
//----------------------------------
function savecomposconfig(){
	/*
	var win = $.messager.progress({
		title:'Please waiting',
		msg:'Loading data...'
	});
	*/
	$.get("/pos/admin/savecomposconfig",{
		corporation_id:$('#corporation_id').val(),
		company_id:$('#company_id').val(),
		branch_id:$('#branch_id').val(),
		branch_no:$('#branch_no').val(),
		code_type:$('#code_type').val(),
		value_type:$('#value_type').val(),
		default_status:$('#default_status').val(),
		condition_status:$('#condition_status').val(),
		default_day:$('#default_day').val(),
		condition_day:$('#condition_day').val(),
		start_date:$('#start_date').val(),
		end_date:$('#end_date').val(),
		start_time:$('#start_time').val(),
		end_time:$('#end_time').val(),
		id_config:id_config,
		Table:fTable,
		id:id,
		ran:Math.random()
	},function(data){
		 //$.messager.progress('close');
		 resetForm();
		 $('#table_posconfig').flexOptions({query:id_config,qtype:'id_config'}).flexReload();
	});
}
//----------------------------------
function  posconfig(){
	var w=880;
	var h=200;
		
		$("#table_posconfig").flexigrid
		(
			{	
			url:'/pos/admin/tposconfig',
			dataType: 'json',
				colModel : [
					{display: 'code_type', name : 'code_type', width :120, sortable : true, align: 'left'},
					{display: 'value_type', name : 'value_type', width : 60, sortable : true, align: 'center'},
					{display: 'default_status', name : 'default_status', width : 75, sortable : true, align: 'center'},
					{display: 'condition_status', name : 'condition_status', width :85, sortable : true, align: 'center'},
					{display: 'default_day', name : 'default_day', width : 65, sortable : true, align: 'center'},
					{display: 'condition_day', name : 'condition_day', width :75, sortable : true, align: 'center'},
					{display: 'วันเริ่มต้น', name : 'start_date', width : 75, sortable : true, align: 'center'},
					{display: 'วันสินสุด', name : 'end_date', width :75, sortable : true, align: 'center'},
					{display: 'เวลาเริ่ม', name : 'start_time', width : 60, sortable : true, align: 'left'},
					{display: 'เวลาสิ้นสุด', name : 'end_time', width : 60, sortable : true, align: 'left'}
				]/*,searchitems :[
								{display: 'corporation_id', name  :'corporation_id', isdefault: true},
								{display: 'company_id', name  :'company_id', isdefault: true},
								{display: 'branch_id', name  :'branch_id', isdefault: true},
								{display: 'branch_no', name  :'branch_no', isdefault: true},
								{display: 'code_type ', name  :'code_type'},
								{display: 'start_date ', name  :'start_date'},
								{display: 'end_date ', name  :'end_date'}
				]*/,
				sortname: "id",
				sortorder: "ASC",
				usepager: true,
				title: 'ตารางแสดงรายละเอียดการ Config',
				useRp: true,
				rp: 30,
				showTableToggleBtn: true,
				width: w,
				height: h,
				query:query,
				qtype: qtype,
				singleSelect: true,
				//onSubmit:sendForm,
				onSuccess:function(){
				
				}
			
			}
		);
}
//----------------------------------
function getposconfiglist(){
	if(id_config==""){
		//alert("กรุณาเลือกชุด Config Id ก่อน");
		return false;
	}else{
		/*
		var win = $.messager.progress({
			title:'Please waiting',
			msg:'Loading data...'
		});
		*/
		$("#fTable").val(fTable);
		$("#corporation_id").val(corporation_id);
		$("#company_id").val(company_id);
		$("#branch_id").val(branch_id);
		$("#branch_no").val(branch_no);
		var dt = $('#form1').serializeArray();
		$("#table_posconfig").flexOptions({
			params:dt,
			query:id_config,
			qtype:'id_config'
		}).flexReload();
		
		//$.messager.progress('close');
	}
}
//----------------------------------
function Sent_All_Branch(){
	if(id_config==""){
		alert("กรุณาเลือกชุด Config Id ก่อน");
		return false;
	}else{
		/*
		var win = $.messager.progress({
			title:'Please waiting',
			msg:'Loading data...'
		});
		*/
		$.get("/pos/admin/sentallbranch",{
			corporation_id:corporation_id,
			company_id:company_id,
			id_config:id_config,
			ran:Math.random()
		},function(data){
			//$.messager.progress('close');
		});
	}
}
//----------------------------------
function CancelConfig(){
	if(branch_id==""){
		alert("กรุณาเลือก รหัสสาขา ก่อน");
		return false;
	}else{
		/*
		var win = $.messager.progress({
			title:'Please waiting',
			msg:'Loading data...'
		});
		*/
		$.get("/pos/admin/cancelconfig",{
			corporation_id:corporation_id,
			company_id:company_id,
			branch_id:branch_id,
			branch_no:branch_no,
			ran:Math.random()
		},function(data){
			$('#treeposconfic').tree('reload');
			$('#treeposconfiglist').tree('reload');
			$('#table_posconfig').flexOptions({query:query,qtype:qtype}).flexReload();
			resetForm();
			//$.messager.progress('close');
		});
	}
}
//----------------------------------
function SentBranch(){
	if(id_config==""){
		alert("กรุณาเลือก Config Id ก่อน");
		return false;
	}else{
		/*
		var win = $.messager.progress({
			title:'Please waiting',
			msg:'Loading data...'
		});
		*/
		$.get("/pos/admin/sentbranch",{
			corporation_id:corporation_id,
			company_id:company_id,
			branch_id:branch_id,
			branch_no:branch_no,
			id_config:id_config,
			ran:Math.random()
		},function(data){
			alert(data);
			if(data=="error"){
				alert("รหัสสาขานี้ถูก config แล้ว กรุณายกเลิก config เดิมก่อน");
				return false;
			}else{
				//$('#treeposconfic').tree('reload');
				$('#treeposconfiglist').tree('reload');
				//$.messager.progress('close');
			}

		});
	}
}
//----------------------------------
function getCodeType(id){
	$.getJSON("/pos/admin/getcodetype",{
		id:id,
		fTable:fTable,
		ran:Math.random()
		},function(data){
			$("#code_type").val(data[0].code_type);
			$("#value_type").val(data[0].value_type);
			$("#default_status").val(data[0].default_status);
			$("#condition_status").val(data[0].condition_status);
			$("#default_day").val(data[0].default_day);
			$("#condition_day").val(data[0].condition_day);
			$("#start_date").val(data[0].start_date);
			$("#end_date").val(data[0].end_date);
			$("#start_time").val(data[0].start_time);
			$("#end_time").val(data[0].end_time);
	});
}
//----------------------------------
function DelConfig(){
	if(confirm('ท่านต้องการลบชุด Config นี้ใช่หรือไม่ !!')==true)
	{
		$.get("/pos/admin/delconfig",{
			id_config:id_config,
			ran:Math.random()
			},function(data){
				$('#treeposconfic').tree('reload');
				$('#treeposconfiglist').tree('reload');
				//$.messager.progress('close');
		});
	}
	return false;
}
//----------------------------------
function AddConfigBranch(){
	$.get("/pos/admin/addconfigbranch",{
		ran:Math.random()
	},function(data){
		$('#treeposconfic').tree('reload');
		$('#treeposconfiglist').tree('reload');
	});
}
//----------------------------------
function AddCodeType(){
	$('#dialogAddCodeType').dialog('open');
}

function saveAddCodeType(){
	$.get("/pos/admin/saveaddcodetype",{
		corporation_id:$('#corporation_id_add').val(),
		company_id:$('#company_id_add').val(),
		branch_id:$('#branch_id_add').val(),
		branch_no:$('#branch_no_add').val(),
		code_type:$('#code_type_add').val(),
		value_type:$('#value_type_add').val(),
		default_status:$('#default_status_add').val(),
		condition_status:$('#condition_status_add').val(),
		default_day:$('#default_day_add').val(),
		condition_day:$('#condition_day_add').val(),
		start_date:$('#start_date_add').val(),
		end_date:$('#end_date_add').val(),
		start_time:$('#start_time_add').val(),
		end_time:$('#end_time_add').val(),
		id_config:id_config,
		Table:fTable,
		id:id,
		ran:Math.random()
	},function(data){
		 resetFormAddCodeType();
		 $('#table_posconfig').flexOptions({query:query,qtype:qtype}).flexReload();
		 $('#dialogAddCodeType').dialog('close');
	});
}

//----------------------------------
function delcodetype(){
	if(confirm('ท่านต้องการลบ Code Type นี้ใช่หรือไม่ !!')==true)
	{
		$.get("/pos/admin/delcodetype",{
			fTable:fTable,
			id:id,
			code_type:$("#code_type").val(),
			ran:Math.random()
			},function(data){
				$('#table_posconfig').flexOptions({query:query,qtype:qtype}).flexReload();
				resetForm();
		});
	}
	return false;
}
//----------------------------------


















