function closeWinIframe(){
    var frameWindow = document.parentWindow || document.defaultView;
    var outerDiv = $(frameWindow.frameElement.parentNode.parentNode);
    var curWindow = outerDiv.find(".window_top").contents().find("a.window_close");
    $(curWindow).closest('div.window').hide();
    var icons_id=curWindow.attr('href');
    $(icons_id,window.parent.document).hide('fast');
    //refresh parent
    window.parent.location.href = window.parent.location.href;
    return false;
}//func

$(function(){

	
	$.get("/pos/index/sentdatatooffice",{
		ran:Math.random()
	},function(data){
		var win = $.messager.progress({
			title:'กรุณาคอย การส่งข้อมูลอาจใช้เวลานานประมาณ 2 นาที',
			msg:'กำลังส่งข้อมูล...อย่าปิดเครื่องก่อนเสร็จ'
		});
			$.messager.alert('สถานะการทำงาน',data);
			setTimeout( function(){
				$.messager.progress('close');
				closeWinIframe();
			}, 100000);
	});
});









