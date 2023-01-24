var countClick=0;
var product_id='';
var w=880;
var h=450;
$(function(){
	productprofile(product_id);
});

function productprofile(product_id){

	countClick++;
	if(countClick > 1){
		$('#fixgrids').flexOptions({query:product_id}).flexReload();
	}else{
		$("#fixgrids").flexigrid
		(
			{	
			url:'/pos/admin/productprofile',
			dataType: 'json',
				colModel : [
					{display: 'รหัสบริษัท ', name : 'corporation_id', width :60, sortable : true, align: 'center'}, 
					{display: 'รหัสสินค้า', name : 'product_id', width :100, sortable : true, align: 'left'},
					{display: 'Barcode', name : 'barcode', width : 100, sortable : true, align: 'left'},
					{display: 'ราคาขาย ', name : 'price', width : 100, sortable : true, align: 'left'},
					{display: 'ชื่อสินค้า', name : 'name_product', width : 250, sortable : true, align: 'left'},
					{display: 'เมนู', name : 'cancel', width :100, sortable : true, align: 'center'}
				],searchitems :[
					{display: 'รหัสสินค้า', name  :'product_id', isdefault: true},
					{display: 'รหัสบริษัท', name  :'corporation_id'},
					{display: 'Barcode', name  :'barcode'},
					{display: 'ชื่อสินค้า', name  :'name_product'}
					
				],
				sortname: "product_id",
				sortorder: "ASC",
				usepager: true,
				title: 'ทะเบียนสินค้า',
				useRp: true,
				rp: 30,
				query:product_id,   
				showTableToggleBtn: true,
				width: w,
				height: h,
				onSubmit: addForm,
				singleSelect: true,
				onSuccess:function(){
				}
			}
		);
		$('#sform').submit(function ()
				{	
					//$('#fgAllPatients').flexOptions({ query: 'blah=qweqweqwe' }).flexReload();
					$('#fixgrids').flexOptions({query:product_id}).flexReload();
					return false;
				}
		);	
	}
}
//----------------------------------------------------------------
function addForm()
{
	var dt = $('#sform').serializeArray();
	$("#fixgrids").flexOptions({params: product_id});
	return true;
}

//----------------------------------------------------------------
function productdetail(product_id){
	$.get("/pos/admin/productdetail",{
		product_id:product_id,
		ran:Math.random()
		},function(data){ 
			$("#dialog_productdetail").html(data);
			
			var dialog_productdetail = $("#dialog_productdetail");
			dialog_productdetail.dialog({
				title:'รายละเอียดสินค้า',
				width:700,
				modal:true,
				close: function(){
					dialog_productdetail.dialog("destroy");
					dialog_productdetail.hide();
				}
			}).show();

	});
}
//----------------------------------------------------------------





