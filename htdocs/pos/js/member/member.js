var qtypes='';
var querys='';
$(function(){	
	tablemember(qtypes,querys);
	//----------------------------------
	/*
	$('#diveditmemmber').dialog({
		buttons:[{
			text:'Ok',
			iconCls:'icon-ok',
			handler:function(){
				updatemember();
			}
		},{
			text:'Cancel',
			handler:function(){
				resetForm();
				$('#diveditmemmber').dialog('close');
			}
		}]
	});
	$('#diveditmemmber').dialog('close');	
	*/
	//----------------------------------
    $('#urlgo').submit(function(){
    	$('#fixgrids').flexOptions({query:$('#url').val(),qtype:'name'}).flexReload();
        return false;
    });
    $('#searchgo').submit(function(){
    	$('#fixgrids').flexOptions({query:$('#search').val(),qtype:'member_no'}).flexReload();
        return false;
    });
	
    $('#stop').click(function(){
    	$('#fixgrids').flexOptions({query:$('#url').val(),qtype:'name'}).flexReload();
        return false;
    });

});
//----------------------------------	
//function updatemember(){
	
//}
//----------------------------------	
var countClick=0;
function  tablemember(qtypes,querys){
	countClick++;
	if(countClick > 1){
		$('#fixgrids').flexOptions({query:querys,qtype:qtypes}).flexReload();
	}else{
		//var w=(screen.width);
		//var h=(screen.height);
		
		var w=882;
		var h=480;
		$("#fixgrids").flexigrid
		(
			{	
			url:'/pos/admin/tablemember',
			dataType: 'json',
				colModel : [
					{display: 'ชื่อ-นามสกุล', name : 'name', width :120, sortable : true, align: 'left'},
					{display: 'วันเกิด ', name : 'birthday', width : 60, sortable : true, align: 'left'},
					{display: 'โทรศัพท์มือถือ ', name : 'mobile_no', width :65, sortable : true, align: 'left'},
					{display: 'เลขที่บัตรสมาชิก', name : 'member_no', width :80, sortable : false, align: 'left'},
					{display: 'วันที่สมัคร', name : 'apply_date', width :60, sortable : false, align: 'left'},
					{display: 'วันที่บัตรหมดอายุ', name : 'expire_date', width : 80, sortable : false, align: 'center'},
					{display: 'รหัสประเภทบัตร', name : 'cardtype_id', width :80, sortable : false, align: 'center'},
					{display: 'สมัครจากจุดขาย', name : 'apply_shop', width :80, sortable : false, align: 'center'},
					{display: 'รหัสโปรโมชั่นที่ใช้สมัคร', name : 'apply_promo', width : 120, sortable : false, align: 'center'}
					//{display: 'วันโปรโมชั่นพิเศษ', name : 'special_day', width : 80, sortable : false, align: 'left'}
					
				],searchitems :[
					{display: 'เลขที่บัตรสมาชิก', name  :'member_no', isdefault: true},
					{display: 'ชื่อไทย', name  :'name'},
					{display: 'นามสกุลไทย ', name  :'surname'},
					{display: 'ชื่ออังกฤษ', name  :'ename'},
					{display: 'นามสกุลอังกฤษ ', name  :'esurname'}
					
				],
				sortname: "",
				sortorder: "ASC",
				usepager: true,
				title: 'ทะเบียนสมาชิก',
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