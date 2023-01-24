var m1000 	=1000;
	var m500	=500;
	var m100	=100;
	var m50		=50;
	var m20 	=20;
	var m10		=10;
	var m5		=5;
	var m2		=2;
	var m1		=1;
	var m05		=0.50;
	var m025	=0.25;
$( document ).ready(function() {
	check_doc_status();
	$("#eid").focus();
});

jQuery(document).bind('keypress', function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	//alert(event.keyCode);
    if(event.keyCode == '114'){
    	save_data();
    }
});

function check_doc_status(){
	$.ajax({
		type:'post',
		url:'/sales/countingmoney/checkdocstatus',
		cache: false,
		dataType: 'json',
		success:function(data){
			//alert(data.status);
			if(data.status == "y"){
				$("#number").html("NO "+data.number+".");
				$("#status_no").val(data.number);
				$("#pay").val(data.pay);
				$("#pay_c").val(data.chg_cash);
				$("#pay_p").val(data.pett_cash);
			}else if(data.status=='err1'){
				jAlert('โปรดปิดบิลประจำวัน...ก่อนการบันทึกเงินสดอีกครั้ง','ข้อความแจ้งเตือน',function(){
					//$("#eid").focus().select();
					//return false;
					setTimeout(
			                  function() 
			                  {
			                     location.reload();
			                  }, 0001); 
		     	});
			}else if(data.status=='err2'){
				cleardata();
				jAlert('ท่านได้ทำรายการครบแล้ว','ข้อความแจ้งเตือน',function(){
					//$("#eid").focus().select();
					//return false;
					setTimeout(
			                  function() 
			                  {
			                     location.reload();
			                  }, 0001); 
		     	});
			}
		}
	});
}

$("#eid").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	var eid = $("#eid").val();
	if(keycode == '13'){  //enter
		if(eid == ""){
			jAlert('กรุณากรอกรหัสพนักงาน','ข้อความแจ้งเตือน',function(){
				$("#eid").focus();
				return false;
	     	});
		}else{
			$.ajax({
				type:'post',
				url:'/sales/countingmoney/checkemp',
				cache: false,
				dataType: 'json',
				data:{ 
					eid : eid	
				},
				success:function(data){	
					if(data.status == "y"){
						$("#position").val(data.position);
						$("#chk").val(data.status);
						$("#txt_remark").focus();
					}else{
						jAlert('ไม่พบรหัสพนักงาน','ข้อความแจ้งเตือน',function(){
							$("#eid").focus().select();
							return false;
				     	});
					}
				}
			});
			
		}	
	}
});//$("#txt_remark").keypress


$("#eid").focusout(function(){
	var eid = $("#eid").val();
	if(eid == ""){
		jAlert('กรุณากรอกรหัสพนักงาน','ข้อความแจ้งเตือน',function(){
			$("#eid").focus();
			return false;
     	});
	}else{
		$.ajax({
			type:'post',
			url:'/sales/countingmoney/checkemp',
			cache: false,
			dataType: 'json',
			data:{ 
				eid : eid	
			},
			success:function(data){	
				if(data.status == "y"){
					$("#position").val(data.position);
					$("#chk").val(data.status);
					$("#txt_remark").focus();
				}else{
					jAlert('ไม่พบรหัสพนักงาน','ข้อความแจ้งเตือน',function(){
						$("#eid").focus().select();
						return false;
			     	});
				}
			}
		});
	}	
});


$("#txt_remark").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	var eid = $("#eid").val();
	if(keycode == '13'){  //enter
		$("#msale1000").focus();
	}
});//$("#txt_remark").keypress

$(".nnomal").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	var eid = $("#eid").val();
	var tmp = 0;
	if(keycode == '13'){  //enter
		var elm=  $(this).attr("id");
		if(elm != "eid" && elm != "txt_remark" && elm != "position"){
			//alert($("#"+elm).val());
		if(IsNumeric($("#"+elm).val())){
			switch(elm){
			case 'msale1000':
				$("#s1000").val(m1000*$("#msale1000").val());
				$("#msale500").focus();
				break;
			case 'msale500': 
				$("#s500").val(m500*$("#msale500").val());
				$("#msale100").focus();
				break;	
			case 'msale100': 
				$("#s100").val(m100*$("#msale100").val());
				$("#msale50").focus();
				break;	
			case 'msale50': 
				$("#s50").val(m50*$("#msale50").val());
				$("#msale20").focus();
				break;	
			case 'msale20': 
				$("#s20").val(m20*$("#msale20").val());
				$("#msale10").focus();
				break;	
			case 'msale10': 
				$("#s10").val(m10*$("#msale10").val());
				$("#msale5").focus();
				break;	
			case 'msale5': 
				$("#s5").val(m5*$("#msale5").val());
				$("#msale2").focus();
				break;	
			case 'msale2': 
				$("#s2").val(m2*$("#msale2").val());
				$("#msale1").focus();
				break;	
			case 'msale1': 
				$("#s1").val(m1*$("#msale1").val());
				$("#msale05").focus();
				break;
			case 'msale05': 
				$("#s05").val(m05*$("#msale05").val());
				$("#msale025").focus();
				break;	
			case 'msale025': 
				$("#s025").val(m025*$("#msale025").val());
				$("#mres1000").focus();
				break;	
			// ============================= เงินทอน =================================	
			case 'mres1000': 
				$("#c1000").val(m1000*$("#mres1000").val());
				$("#mres500").focus();
				break;		
			case 'mres500': 
				$("#c500").val(m500*$("#mres500").val());
				$("#mres100").focus();
				break;		
			case 'mres100': 
				$("#c100").val(m100*$("#mres100").val());
				$("#mres50").focus();
				break;		
			case 'mres50': 
				$("#c50").val(m50*$("#mres50").val());
				$("#mres20").focus();
				break;		
			case 'mres20': 
				$("#c20").val(m20*$("#mres20").val());
				$("#mres10").focus();
				break;		
			case 'mres10': 
				$("#c10").val(m10*$("#mres10").val());
				$("#mres5").focus();
				break;		
			case 'mres5': 
				$("#c5").val(m5*$("#mres5").val());
				$("#mres2").focus();
				break;		
			case 'mres2': 
				$("#c2").val(m2*$("#mres2").val());
				$("#mres1").focus();
				break;		
			case 'mres1': 
				$("#c1").val(m1*$("#mres1").val());
				$("#mres05").focus();
				break;		
			case 'mres05': 
				$("#c05").val(m05*$("#mres05").val());
				$("#mres025").focus();
				break;		
			case 'mres025': 
				$("#c025").val(m025*$("#mres025").val());
				$("#mcash1000").focus();
				break;		
				
				// ============================= เงินสดย่อย =================================	
			case 'mcash1000': 
				$("#p1000").val(m1000*$("#mcash1000").val());
				$("#mcash500").focus();
				break;		
			case 'mcash500': 
				$("#p500").val(m500*$("#mcash500").val());
				$("#mcash100").focus();
				break;		
			case 'mcash100': 
				$("#p100").val(m100*$("#mcash100").val());
				$("#mcash50").focus();
				break;		
			case 'mcash50': 
				$("#p50").val(m50*$("#mcash50").val());
				$("#mcash20").focus();
				break;		
			case 'mcash20': 
				$("#p20").val(m20*$("#mcash20").val());
				$("#mcash10").focus();
				break;		
			case 'mcash10': 
				$("#p10").val(m10*$("#mcash10").val());
				$("#mcash5").focus();
				break;		
			case 'mcash5': 
				$("#p5").val(m5*$("#mcash5").val());
				$("#mcash2").focus();
				break;		
			case 'mcash2': 
				$("#p2").val(m2*$("#mcash2").val());
				$("#mcash1").focus();
				break;		
			case 'mcash1': 
				$("#p1").val(m1*$("#mcash1").val());
				$("#mcash05").focus();
				break;		
			case 'mcash05': 
				$("#p05").val(m05*$("#mcash05").val());
				$("#mcash025").focus();
				break;		
			case 'mcash025': 
				$("#p025").val(m025*$("#mcash025").val());
				break;				
				
			} //end switch
		 }else{  // not numeric
			  jAlert('โปรดใส่ข้อมูลเป็นตัวเลข','ข้อความแจ้งเตือน',function(){
					$("#"+elm).focus().select();
					return false;
		     	});
		  }
		  amt_s();
		  amt_c();
		  amt_p();
		} // end check remark
		
	}//end key code
}); //end keypress


$(".nnomal").focusout(function(){
		var elm=  $(this).attr("id");
		if(elm != "eid" && elm != "txt_remark" && elm != "position"){
			//alert($("#"+elm).val());
		if(IsNumeric($("#"+elm).val())){
			switch(elm){
			case 'msale1000':
				$("#s1000").val(m1000*$("#msale1000").val());
				//$("#msale500").focus();
				break;
			case 'msale500': 
				$("#s500").val(m500*$("#msale500").val());
				//$("#msale100").focus();
				break;	
			case 'msale100': 
				$("#s100").val(m100*$("#msale100").val());
				//$("#msale50").focus();
				break;	
			case 'msale50': 
				$("#s50").val(m50*$("#msale50").val());
				//$("#msale20").focus();
				break;	
			case 'msale20': 
				$("#s20").val(m20*$("#msale20").val());
				//$("#msale10").focus();
				break;	
			case 'msale10': 
				$("#s10").val(m10*$("#msale10").val());
				//$("#msale5").focus();
				break;	
			case 'msale5': 
				$("#s5").val(m5*$("#msale5").val());
				//$("#msale2").focus();
				break;	
			case 'msale2': 
				$("#s2").val(m2*$("#msale2").val());
				//$("#msale1").focus();
				break;	
			case 'msale1': 
				$("#s1").val(m1*$("#msale1").val());
				//$("#msale05").focus();
				break;
			case 'msale05': 
				$("#s05").val(m05*$("#msale05").val());
				//$("#msale025").focus();
				break;	
			case 'msale025': 
				$("#s025").val(m025*$("#msale025").val());
				//$("#mres1000").focus();
				break;	
			// ============================= เงินทอน =================================	
			case 'mres1000': 
				$("#c1000").val(m1000*$("#mres1000").val());
				//$("#mres500").focus();
				break;		
			case 'mres500': 
				$("#c500").val(m500*$("#mres500").val());
				//$("#mres100").focus();
				break;		
			case 'mres100': 
				$("#c100").val(m100*$("#mres100").val());
				//$("#mres50").focus();
				break;		
			case 'mres50': 
				$("#c50").val(m50*$("#mres50").val());
				//$("#mres20").focus();
				break;		
			case 'mres20': 
				$("#c20").val(m20*$("#mres20").val());
				//$("#mres10").focus();
				break;		
			case 'mres10': 
				$("#c10").val(m10*$("#mres10").val());
				//$("#mres5").focus();
				break;		
			case 'mres5': 
				$("#c5").val(m5*$("#mres5").val());
				//$("#mres2").focus();
				break;		
			case 'mres2': 
				$("#c2").val(m2*$("#mres2").val());
				//$("#mres1").focus();
				break;		
			case 'mres1': 
				$("#c1").val(m1*$("#mres1").val());
				//$("#mres05").focus();
				break;		
			case 'mres05': 
				$("#c05").val(m05*$("#mres05").val());
				//$("#mres025").focus();
				break;		
			case 'mres025': 
				$("#c025").val(m025*$("#mres025").val());
				//$("#mcash1000").focus();
				break;		
				
			// ============================= เงินสดย่อย =================================	
			case 'mcash1000': 
				$("#p1000").val(m1000*$("#mcash1000").val());
				//$("#mcash500").focus();
				break;		
			case 'mcash500': 
				$("#p500").val(m500*$("#mcash500").val());
				//$("#mcash100").focus();
				break;		
			case 'mcash100': 
				$("#p100").val(m100*$("#mcash100").val());
				//$("#mcash50").focus();
				break;		
			case 'mcash50': 
				$("#p50").val(m50*$("#mcash50").val());
				//$("#mcash20").focus();
				break;		
			case 'mcash20': 
				$("#p20").val(m20*$("#mcash20").val());
				//$("#mcash10").focus();
				break;		
			case 'mcash10': 
				$("#p10").val(m10*$("#mcash10").val());
				//$("#mcash5").focus();
				break;		
			case 'mcash5': 
				$("#p5").val(m5*$("#mcash5").val());
				//$("#mcash2").focus();
				break;		
			case 'mcash2': 
				$("#p2").val(m2*$("#mcash2").val());
				//$("#mcash1").focus();
				break;		
			case 'mcash1': 
				$("#p1").val(m1*$("#mcash1").val());
				//$("#mcash05").focus();
				break;		
			case 'mcash05': 
				$("#p05").val(m05*$("#mcash05").val());
				//$("#mcash025").focus();
				break;		
			case 'mcash025': 
				$("#p025").val(m025*$("#mcash025").val());
				break;				
				
			} //end switch
		 }else // not numeric
			 if($("#"+elm).val() != ""){
				  jAlert('โปรดใส่ข้อมูลเป็นตัวเลข','ข้อความแจ้งเตือน',function(){
						$("#"+elm).focus().select();
						return false;
			     	});
			 }else{
				 $("#"+elm).focus();
			 }
		amt_s();
		amt_c();
		amt_p();
		} // end check remark
}); //end keypress

function IsNumeric(input)
{
    return (input - 0) == input && (''+input).trim().length > 0;
}

function amt_s(){
	var sum = 0;
	var total_s = $("#total_s").val();
	var pay = $("#pay").val();
	var s1000 = $("#s1000").val();
	var s500 = $("#s500").val();
	var s100 = $("#s100").val();
	var s50 = $("#s50").val();
	var s20 = $("#s20").val();
	var s10 = $("#s10").val();
	var s5 = $("#s5").val();
	var s2 = $("#s2").val();
	var s1 = $("#s1").val();
	var s05 = $("#s05").val();
	var s025 =$("#s025").val();
	if(s1000 != ""){
		sum = parseFloat(sum) + parseFloat(s1000);
	}
	if(s500 != ""){
		sum = parseFloat(sum) + parseFloat(s500);
	}
	if(s100 != ""){
		sum = parseFloat(sum) + parseFloat(s100);
	}
	if(s50 != ""){
		sum = parseFloat(sum) + parseFloat(s50);
	}
	if(s20 != ""){
		sum = parseFloat(sum) + parseFloat(s20);
	}
	if(s10 != ""){
		sum = parseFloat(sum) + parseFloat(s10);
	}
	if(s5 != ""){
		sum = parseFloat(sum) + parseFloat(s5);
	}
	if(s2 != ""){
		sum = parseFloat(sum) + parseFloat(s2);
	}
	if(s1 != ""){
		sum = parseFloat(sum) + parseFloat(s1);
	}
	if(s05 != ""){
		sum = parseFloat(sum) + parseFloat(s05);
	}
	if(s025 != ""){
		sum = parseFloat(sum) + parseFloat(s025);
	}
	
	//alert(sum);
	//alert(total_s-sum);
	$("#total_s").val(parseFloat(sum));
	$("#mnt_s").val(parseFloat(total_s)-parseFloat(pay));
	
}

function amt_c(){
	var sum = 0;
	var total_s = $("#total_c").val();
	var pay		= $("#pay_c").val();
	var s1000 = $("#c1000").val();
	var s500 = $("#c500").val();
	var s100 = $("#c100").val();
	var s50 = $("#c50").val();
	var s20 = $("#c20").val();
	var s10 = $("#c10").val();
	var s5 = $("#c5").val();
	var s2 = $("#c2").val();
	var s1 = $("#c1").val();
	var s05 = $("#c05").val();
	var s025 =$("#c025").val();
	if(s1000 != ""){
		sum = parseFloat(sum) + parseFloat(s1000);
	}
	if(s500 != ""){
		sum = parseFloat(sum) + parseFloat(s500);
	}
	if(s100 != ""){
		sum = parseFloat(sum) + parseFloat(s100);
	}
	if(s50 != ""){
		sum = parseFloat(sum) + parseFloat(s50);
	}
	if(s20 != ""){
		sum = parseFloat(sum) + parseFloat(s20);
	}
	if(s10 != ""){
		sum = parseFloat(sum) + parseFloat(s10);
	}
	if(s5 != ""){
		sum = parseFloat(sum) + parseFloat(s5);
	}
	if(s2 != ""){
		sum = parseFloat(sum) + parseFloat(s2);
	}
	if(s1 != ""){
		sum = parseFloat(sum) + parseFloat(s1);
	}
	if(s05 != ""){
		sum = parseFloat(sum) + parseFloat(s05);
	}
	if(s025 != ""){
		sum = parseFloat(sum) + parseFloat(s025);
	}
	
	$("#total_c").val(parseFloat(sum));
	$("#mnt_c").val(parseFloat(total_s)-parseFloat(pay));
}

function amt_p(){
	var sum = 0;
	var total_s = $("#total_p").val();
	var pay 	=$("#pay_p").val();
	var s1000 = $("#p1000").val();
	var s500 = $("#p500").val();
	var s100 = $("#p100").val();
	var s50 = $("#p50").val();
	var s20 = $("#p20").val();
	var s10 = $("#p10").val();
	var s5 = $("#p5").val();
	var s2 = $("#p2").val();
	var s1 = $("#p1").val();
	var s05 = $("#p05").val();
	var s025 =$("#p025").val();
	if(s1000 != ""){
		sum = parseFloat(sum) + parseFloat(s1000);
	}
	if(s500 != ""){
		sum = parseFloat(sum) + parseFloat(s500);
	}
	if(s100 != ""){
		sum = parseFloat(sum) + parseFloat(s100);
	}
	if(s50 != ""){
		sum = parseFloat(sum) + parseFloat(s50);
	}
	if(s20 != ""){
		sum = parseFloat(sum) + parseFloat(s20);
	}
	if(s10 != ""){
		sum = parseFloat(sum) + parseFloat(s10);
	}
	if(s5 != ""){
		sum = parseFloat(sum) + parseFloat(s5);
	}
	if(s2 != ""){
		sum = parseFloat(sum) + parseFloat(s2);
	}
	if(s1 != ""){
		sum = parseFloat(sum) + parseFloat(s1);
	}
	if(s05 != ""){
		sum = parseFloat(sum) + parseFloat(s05);
	}
	if(s025 != ""){
		sum = parseFloat(sum) + parseFloat(s025);
	}
	
	$("#total_p").val(parseFloat(sum));
	$("#mnt_p").val(parseFloat(total_s)-parseFloat(pay));
}

function save_data(){	
		amt_s();
		amt_c();
		amt_p();
		var eid = $("#eid").val();
		var txt_remark = $("#txt_remark").val();
		var status_no 	=$("#status_no").val();
		var s1000 = $("#s1000").val();
		var s500 = $("#s500").val();
		var s100 = $("#s100").val();
		var s50 = $("#s50").val();
		var s20 = $("#s20").val();
		var s10 = $("#s10").val();
		var s5 = $("#s5").val();
		var s2 = $("#s2").val();
		var s1 = $("#s1").val();
		var s05 = $("#s05").val();
		var s025 = $("#s025").val();
		
		var c1000 = $("#c1000").val();
		var c500 = $("#c500").val();
		var c100 = $("#c100").val();
		var c50 = $("#c50").val();
		var c20 = $("#c20").val();
		var c10 = $("#c10").val();
		var c5 = $("#c5").val();
		var c2 = $("#c2").val();
		var c1 = $("#c1").val();
		var c05 = $("#c05").val();
		var c025 = $("#c025").val();
		
		var p1000 = $("#p1000").val();
		var p500 = $("#p500").val();
		var p100 = $("#p100").val();
		var p50 = $("#p50").val();
		var p20 = $("#p20").val();
		var p10 = $("#p10").val();
		var p5 = $("#p5").val();
		var p2 = $("#p2").val();
		var p1 = $("#p1").val();
		var p05 = $("#p05").val();
		var p025 = $("#p025").val();
		
		var total_s = $("#total_s").val();
		var total_c = $("#total_c").val();
		var total_p = $("#total_p").val();
		var pay 	= $("#pay").val();
		var pay_c 	= $("#pay_c").val();
		var pay_p 	= $("#pay_p").val();
		var mnt_s 	= $("#mnt_s").val();
		var mnt_c 	= $("#mnt_c").val();
		var mnt_p 	= $("#mnt_p").val();
		//alert($("#chk").val());
		if($("#chk").val() == 'y'){
			$( "#dialog" ).dialog( "open" );	
			jConfirm('ยืนยันการบันทึกการตรวจนับเงินสด', 'แจ้งเตือน', function(r) {
					if(r==false){
						return false;
					}else{
			
					$.ajax({
						type: "POST",
						url: "/sales/countingmoney/savedata",
						data:{
							s1000:s1000,c1000:c1000,p1000:p1000,
							s500:s500,c500:c500,p500:p500,
							s100:s100,c100:c100,p100:p100,
							s50:s50,c50:c50,p50:p50,
							s20:s20,c20:c20,p20:p20,
							s10:s10,c10:c10,p10:p10,
							s5:s5,c5:c5,p5:p5,
							s2:s2,c2:c2,p2:p2,
							s1:s1,c1:c1,p1:p1,
							s05:s05,c05:c05,p05:p05,
							s025:s025,c025:c025,p025:p025,
							total_s:total_s,total_c:total_c,total_p:total_p,
							pay:pay,pay_c:pay_c,pay_p:pay_p,mnt_s:mnt_s,mnt_c:mnt_c,mnt_p:mnt_p,
							eid:eid,txt_remark:txt_remark,
							status_no:status_no
							},
						success:
						function(data){
							if(data == 'y'){
								cleardata();
								jAlert('บันทึกข้อมูลสำเร็จ','ข้อความแจ้งเตือน',function(){
									setTimeout(
							                  function() 
							                  {
							                     location.reload();
							                  }, 0001); 
						     	});
							}
						}
						});
					}
				});
		}// end check
		else{
			jAlert("ยังไม่สามารถทำรายการได้กรุณาใส่ข้อมูล");
		}
		
		
}		

function cleardata(){
	var eid = $("#eid").val("");
	var txt_remark = $("#txt_remark").val("");
	var status_no 	=$("#status_no").val("");
	var s1000 = $("#s1000").val("");
	var s500 = $("#s500").val("");
	var s100 = $("#s100").val("");
	var s50 = $("#s50").val("");
	var s20 = $("#s20").val("");
	var s10 = $("#s10").val("");
	var s5 = $("#s5").val("");
	var s2 = $("#s2").val("");
	var s1 = $("#s1").val("");
	var s05 = $("#s05").val("");
	var s025 = $("#s025").val("");
	
	var c1000 = $("#c1000").val("");
	var c500 = $("#c500").val("");
	var c100 = $("#c100").val("");
	var c50 = $("#c50").val("");
	var c20 = $("#c20").val("");
	var c10 = $("#c10").val("");
	var c5 = $("#c5").val("");
	var c2 = $("#c2").val("");
	var c1 = $("#c1").val("");
	var c05 = $("#c05").val("");
	var c025 = $("#c025").val("");
	
	var p1000 = $("#p1000").val("");
	var p500 = $("#p500").val("");
	var p100 = $("#p100").val("");
	var p50 = $("#p50").val("");
	var p20 = $("#p20").val("");
	var p10 = $("#p10").val("");
	var p5 = $("#p5").val("");
	var p2 = $("#p2").val("");
	var p1 = $("#p1").val("");
	var p05 = $("#p05").val("");
	var p025 = $("#p025").val("");
	
	var total_s = $("#total_s").val("");
	var total_c = $("#total_c").val("");
	var total_p = $("#total_p").val("");
	var pay 	= $("#pay").val("");
	var pay_c 	= $("#pay_c").val("");
	var pay_p 	= $("#pay_p").val("");
	var mnt_s 	= $("#mnt_s").val("");
	var mnt_c 	= $("#mnt_c").val("");
	var mnt_p 	= $("#mnt_p").val("");
}

