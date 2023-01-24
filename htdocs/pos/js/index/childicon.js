$(function(){
	$('#treemenu_ref').tree({
		url: '/pos/index/treemenuref?menu_id='+$("#hiddenmenu_id").val(),
		onClick:function(node){
			//$(this).tree('toggle', node.target);
			$.get("/pos/index/geturl",{
				menu_exec:node.id,
				ran:Math.random()
				},function(data){ 
					$("#con_menu_exec").html(data);
			});
		},
		onContextMenu: function(e, node){
			e.preventDefault();
			$('#treemenu_ref').tree('select', node.target);
			$('#mmenu_ref').menu('show', {
				left: e.pageX,
				top: e.pageY
			});
		}
	});
	
});



