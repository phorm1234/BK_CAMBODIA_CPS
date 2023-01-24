//----------------------------------------------------------------
$(function(){
	loadmybook();

	$('#texsearch').keyup(function(e) {
		if(e.keyCode == 13) {
			gotopagecompany();
		}
	});
	
});
//----------------------------------------------------------------
function loadmybook(){
	var $mybook 		= $('#mybook');
	var $bttn_next		= $('#next_page_button');
	var $bttn_prev		= $('#prev_page_button');
	var $loading		= $('#loading');
	var $mybook_images	= $mybook.find('img');
	var cnt_images		= $mybook_images.length;
	var loaded			= 0;
	
	//$mybook_images.each(function(){
		//var $img 	= $(this);
		//var source	= $img.attr('src');
		//$('<img/>').load(function(){
			//++loaded;
			
			//if(loaded == cnt_images){
				$loading.hide();
				$bttn_next.show();
				$bttn_prev.show();
				$mybook.show().booklet({
					name:               null,                            // name of the booklet to display in the document title bar
					width:              800,                             // container width
					height:             500,                             // container height
					speed:              600,                             // speed of the transition between pages
					direction:          'LTR',                           // direction of the overall content organization, default LTR, left to right, can be RTL for languages which read right to left
					startingPage:       0,                               // index of the first page to be displayed
					easing:             'easeInOutQuad',                 // easing method for complete transition
					easeIn:             'easeInQuad',                    // easing method for first half of transition
					easeOut:            'easeOutQuad',                   // easing method for second half of transition
					closed:             true,                           // start with the book "closed", will add empty pages to beginning and end of book
					closedFrontTitle:   null,                            // used with "closed", "menu" and "pageSelector", determines title of blank starting page
					closedFrontChapter: null,                            // used with "closed", "menu" and "chapterSelector", determines chapter name of blank starting page
					closedBackTitle:    null,                            // used with "closed", "menu" and "pageSelector", determines chapter name of blank ending page
					closedBackChapter:  null,                            // used with "closed", "menu" and "chapterSelector", determines chapter name of blank ending page
					covers:             false,                           // used with  "closed", makes first and last pages into covers, without page numbers (if enabled)
					pagePadding:        10,                              // padding for each page wrapper
					pageNumbers:        true,                            // display page numbers on each page

					hovers:             false,                            // enables preview pageturn hover animation, shows a small preview of previous or next page on hover
					overlays:           false,                            // enables navigation using a page sized overlay, when enabled links inside the content will not be clickable
					tabs:               false,                           // adds tabs along the top of the pages
					tabWidth:           60,                              // set the width of the tabs
					tabHeight:          20,                              // set the height of the tabs
					arrows:             false,                           // adds arrows overlayed over the book edges
					cursor:             'pointer',                       // cursor css setting for side bar areas

					hash:               false,                           // enables navigation using a hash string, ex: #/page/1 for page 1, will affect all booklets with 'hash' enabled
					keyboard:           false, //ture pook                           // enables navigation with arrow keys (left: previous, right: next)
					next:               $bttn_next,          			// selector for element to use as click trigger for next page
					prev:               $bttn_prev,          			// selector for element to use as click trigger for previous page

					menu:               null,                            // selector for element to use as the menu area, required for 'pageSelector'
					pageSelector:       false,                           // enables navigation with a dropdown menu of pages, requires 'menu'
					chapterSelector:    false,                           // enables navigation with a dropdown menu of chapters, determined by the "rel" attribute, requires 'menu'

					shadows:            true,                            // display shadows on page animations
					shadowTopFwdWidth:  166,                             // shadow width for top forward anim
					shadowTopBackWidth: 166,                             // shadow width for top back anim
					shadowBtmWidth:     50,                              // shadow width for bottom shadow

					before:             function(){},                    // callback invoked before each page turn animation
					after:              function(){}                     // callback invoked after each page turn animation
				});
				Cufon.refresh();
			//}
		//}).attr('src',source);
	//});
}
//----------------------------------------------------------------
function editcompany(id){
	$.get("/pos/admin/dialogupdatecompany",{
		id:id,
		ran:Math.random()
		},function(data){
			$("#dialogupdatecompany").html(data);
			
			var dialogupdatecompany = $("#dialogupdatecompany");
			dialogupdatecompany.dialog({
				title:'แก้ไขข้อมูลบริษัท',
				width:600,
				modal:true,
				close: function(){
						dialogupdatecompany.dialog("destroy");
						dialogupdatecompany.hide();
				}
			}).show();
	});
}
//----------------------------------------------------------------
function deletecompany(id,company_id){
	if(confirm('ท่านต้องการลบข้อมูลบริษัทนี้ใช่หรือไม่')==true)
	{
		$.get("/pos/admin/delcompany",{
			id:id,
			company_id:company_id,
			ran:Math.random()
			},function(data){
				$.get("/pos/images/upload/del_folder.php",{
					folder:company_id,
					ran:Math.random()
					},function(data){
						window.location.reload();
				});
		});
	}
	return false;
}
//----------------------------------------------------------------
function searchcompany(id){
	$("#mybook").booklet(id);
}
//----------------------------------------------------------------
function search_province(vals){
	if(vals=='')return false;
	$.get("/pos/admin/gettablemanq",{
		vals:vals,
		ran:Math.random()
		},function(table_manq){
			if(table_manq == "") {
				$('#div_province').html("<select name='province' id='province'> </select>");
				$('#div_amphur').html("<select name='amphur' id='amphur'> </select>");
				$('#div_tambon').html("<select name='tambon' id='tambon'> </select>");
				$('#postcode').val("");
				return false;
			}else{
				$.get("/pos/admin/province",{
					table_manq:table_manq,
					act:'',
					ran:Math.random()
					},function(data){
						$('#div_province').html(data);
					});
			}
	});
}
//----------------------------------------------------------------
function search_province_edit(vals){
	if(vals=='')return false;
	$.get("/pos/admin/gettablemanq",{
		vals:vals,
		ran:Math.random()
		},function(table_manq){
			if(table_manq == "") {
				$('#div_province_edit').html("<select name='province_edit' id='province_edit'> </select>");
				$('#div_amphur_edit').html("<select name='amphur_edit' id='amphur_edit'> </select>");
				$('#div_tambon_edit').html("<select name='tambon_edit' id='tambon_edit'> </select>");
				$('#postcode_edit').val("");
				return false;
			}else{
				$.get("/pos/admin/province",{
					table_manq:table_manq,
					act:'edit',
					ran:Math.random()
					},function(data){
						$('#div_province_edit').html(data);
					});
			}
	});
}
//----------------------------------------------------------------
function search_amphur(vals,table_manq){
	if(vals=='')return false;
	if(table_manq == "") {
		return false;
	}else{
		$.get("/pos/admin/amphur",{
			zip_province_id:vals,
			table_manq:table_manq,
			act:'',
			ran:Math.random()
			},function(data){
				$('#div_amphur').html(data);
				$("#postcode").val("");
				$('#div_tambon').html("<select name='zip_tambon_id' id='zip_tambon_id'></select>");
		});
	}
}

function search_amphur_edit(vals,table_manq){
	if(vals=='')return false;
	$('#div_tambon_edit').html("<select name='tambon_edit' id='tambon_edit'> </select>");
	$('#postcode_edit').val("");
	if(table_manq == "") {
		return false;
	}else{
		$.get("/pos/admin/amphur",{
			zip_province_id:vals,
			table_manq:table_manq,
			act:'edit',
			ran:Math.random()
			},function(data){
				$("#postcode").val("");
				$('#div_amphur_edit').html(data);
		});
	}
}
//----------------------------------------------------------------
function search_tambon(vals,table_manq){
	if(vals=='')return false;
	if(table_manq == "") {
		return false;
	}else{
		$.get("/pos/admin/tambon",{
			zip_amphur_id:vals,
			table_manq:table_manq,
			act:'',
			ran:Math.random()
			},function(data){
				$("#postcode").val("");
				$('#div_tambon').html(data);
		});
	}
}
function search_tambon_edit(vals,table_manq){
	if(vals=='')return false;
	$('#postcode_edit').val("");
	if(table_manq == "") {
		return false;
	}else{
		$.get("/pos/admin/tambon",{
			zip_amphur_id:vals,
			table_manq:table_manq,
			act:'edit',
			ran:Math.random()
			},function(data){
				$('#div_tambon_edit').html(data);
		});
	}
}

//----------------------------------------------------------------
function search_zipcode(vals,table_manq){
	if(vals=='')return false;
	if(table_manq == "") {
		return false;
	}else{
		$.get("/pos/admin/zipcode",{
			zip_tambon_id:vals,
			table_manq:table_manq,
			ran:Math.random()
			},function(data){
				$('#postcode').val(data);
		});
	}
}

function search_zipcode_edit(vals,table_manq){
	if(vals=='')return false;
	if(table_manq == "") {
		return false;
	}else{
		$.get("/pos/admin/zipcode",{
			zip_tambon_id:vals,
			table_manq:table_manq,
			ran:Math.random()
			},function(data){
				$('#postcode_edit').val(data);
		});
	}
}


//----------------------------------------------------------------
function insertcompany(f){
	if($("#country_id").val()==""){
		alert("กรุณาระบุ รหัสประเทศ");
		$("#country_id").focus();
		return false;
	}
	if($("#corporation_id").val()==""){
		alert("กรุณาระบุ รหัสบริษัท ");
		$("#corporation_id").focus();
		return false;
	}
	if($("#company_id").val()==""){
		alert("กรุณาระบุ รหัสช่องทางจำหน่าย   ");
		$("#company_id").focus();
		return false;
	}
	if($("#company_name").val()==""){
		alert("กรุณาระบุ ชื่อบริษัท");
		$("#company_name").focus();
		return false;
	}
	if($("#address").val()==""){
		alert("กรุณาระบุ ที่อยู่ ");
		$("#address").focus();
		return false;
	}
	if($("#zip_province_id").val()==""){
		alert("กรุณาระบุ จังหวัด ");
		$("#zip_province_id").focus();
		return false;
	}
	
	if($("#zip_amphur_id").val()==""){
		alert("กรุณาระบุ เขต/อำเภอ ");
		$("#zip_amphur_id").focus();
		return false;
	}
	
	if($("#zip_tambon_id").val()==""){
		alert("กรุณาระบุ แขวง/ตำบล ");
		$("#address").focus();
		return false;
	}
	
	if($("#tax_id").val()==""){
		alert("กรุณาระบุ เลขประจำตัวผู้เสียภาษี ");
		$("#tax_id").focus();
		return false;
	}
	
	if($("#tel").val()==""){
		alert("กรุณาระบุ เบอร์โทรศัพท์ ");
		$("#tel").focus();
		return false;
	}
	
	
	
	var objName = $("#logo").val();
	var valid = "กขฅคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮ๑๒๓๔๕๖๗๘๙๐ะาอิอีอุอูเอืออึอือไๆโใฯ" ;
	var temp, ok = "no";
	
	  for (var i=0; i< objName.length; i++) {
	    temp = "" + objName.substring(i, i+1);
	    if (valid.indexOf(temp) != "-1") ok = "yes";
	  }

	  if (ok == "yes") {
		  alert("ชื่อภาพมีคำที่เป็นภาษาไทยกรุณาเปลี่ยนชื่อใหม่");
	    return false;
	  }
	  
		
		$.get("/pos/admin/checkduplicate",{
			country_id:$("#country_id").val(),
			corporation_id:$("#corporation_id").val(),
			company_id:$("#company_id").val(),
			ran:Math.random()
			},function(data){
				alert(data);
				if(data>0){
					alert("ข้อมูลบริษัทซ้ำ กรุณาระบุใหม่ !");
					return false;
				}else{
					$("#"+f).submit();
				}
		});
			
	
}
//----------------------------------------------------------------

function updatecompany(company_id){
	
	if($("#country_id_edit").val()==""){
		alert("กรุณาระบุ รหัสประเทศ");
		$("#country_id_edit").focus();
		return false;
	}
	if($("#corporation_id_edit").val()==""){
		alert("กรุณาระบุ รหัสบริษัท ");
		$("#corporation_id_edit").focus();
		return false;
	}
	if($("#company_id_edit").val()==""){
		alert("กรุณาระบุ รหัสช่องทางจำหน่าย   ");
		$("#company_id_edit").focus();
		return false;
	}
	if($("#company_name_edit").val()==""){
		alert("กรุณาระบุ ชื่อบริษัท");
		$("#company_name_edit").focus();
		return false;
	}
	if($("#address_edit").val()==""){
		alert("กรุณาระบุ ที่อยู่ ");
		$("#address_edit").focus();
		return false;
	}
	if($("#zip_province_id_edit").val()==""){
		alert("กรุณาระบุ จังหวัด ");
		$("#zip_province_id_edit").focus();
		return false;
	}
	
	if($("#zip_amphur_id_edit").val()==""){
		alert("กรุณาระบุ เขต/อำเภอ ");
		$("#zip_amphur_id_edit").focus();
		return false;
	}
	
	if($("#zip_tambon_id_edit").val()==""){
		alert("กรุณาระบุ แขวง/ตำบล ");
		$("#address_edit").focus();
		return false;
	}
	
	if($("#tax_id_edit").val()==""){
		alert("กรุณาระบุ เลขประจำตัวผู้เสียภาษี ");
		$("#tax_id_edit").focus();
		return false;
	}
	
	if($("#tel_edit").val()==""){
		alert("กรุณาระบุ เบอร์โทรศัพท์ ");
		$("#tel_edit").focus();
		return false;
	}
	if($("#company_id_edit").val()!= company_id){
		$.get("/pos/images/upload/del_folder.php",{folder:company_id,ran:Math.random()},function(){});
	}
	
	var objName = $("#logo_edit").val();
	var valid = "กขฅคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮ๑๒๓๔๕๖๗๘๙๐ะาอิอีอุอูเอืออึอือไๆโใฯ" ;
	var temp, ok = "no";
	
	  for (var i=0; i< objName.length; i++) {
	    temp = "" + objName.substring(i, i+1);
	    if (valid.indexOf(temp) != "-1") ok = "yes";
	  }

	  if (ok == "yes") {
		  alert("ชื่อภาพมีคำที่เป็นภาษาไทยกรุณาเปลี่ยนชื่อใหม่");
	    return false;
	  }
	  
	  
	if(confirm('ท่านต้องการแก้ไขข้อมูลบริษัทนี้ใช่หรือไม่')==true)
	{
		return true;
	}
	return false;
	
}
//----------------------------------------------------------------
function gotopagecompany(){
	$.get("/pos/admin/gotopagecompany",{
		texsearch:$("#texsearch").val(),
		ran:Math.random()
	},function(page){
		if(page==""){
			alert('ไม่พบรหัสบริษัทที่ท่านระบุ');
			return false;
		}else{
			var i=eval(page)+1;
			$("#mybook").booklet(i);
		}
	});
}
//----------------------------------------------------------------
function gotopage1(){
	$("#mybook").booklet(2);
}
//----------------------------------------------------------------





