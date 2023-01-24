$(function() {
	//$.datepicker.formatDate( "yy-mm-dd", new Date( 2007, 1 - 1, 26 ) );
	$( "#datepicker" ).datepicker(
			{
				flat: true,
				format: 'd-m-Y'
			}
	);
});

function printing(){
	var ddate =	$("#datepicker").val();
	var typecounting = $("#typecounting").val();
	if(ddate == ""){
		//jAlert("กรุณาเลือกว้นที่");
		$("#datepicker").focus();
	}else if(typecounting == ""){
		jAlert("กรุณาเลือกประเภท");
		$("#typecounting").focus();
	}else{
		$( "#dialog" ).dialog( "open" );	
		jConfirm('ยืนยัน', 'แจ้งเตือน', function(r) {
				if(r==false){
					return false;
				}else{
						
				$.ajax({
					type: "POST",
					url: "/sales/countingmoney/checkprinting",
					data:{
						ddate:ddate,
						typecounting:typecounting
						},
					success:
					function(data){
						if(data == 'y'){
							printdata();
						}else{
							jAlert('ไม่พบข้อมูล','ข้อความแจ้งเตือน',function(){
								$("#datepicker").focus();
								return false;
					     	});
						}
					}
				   });
				}
			});
	}
	
	function printdata(){
		$.ajax({
			type: "POST",
			url: "/sales/countingmoney/printing",
			data:{
				ddate:ddate,
				typecounting:typecounting
				},
			success:
			function(data){
				
			}
		   });
	}
	
}