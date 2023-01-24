/**
 *
 */
    var id;
    function chrbox(str){
        var chrbox="", str=str;
        for(var i=0;i<str.length;i++){
            chrbox+='<div class="char-box">'+str[i]+'</div>'
        }
        return chrbox;
    }
    function cboDay(curr){
    	
        var str = "<select name='txt-day' id='txt-day' class='validate[required]'><option value=''>--วัน--</option>";
        for(var i=1; i<=31;i++){
        	var selected = curr==i?"selected=selected":"";
            str+="<option value='"+i+"' "+selected+">"+i+"</option>";
        }
        str+="</select>";
        return str;
    }
    function cboMonth(curr){
        var month = new Array(
                    "--เดือน--",
                    "มกราคม",
                    "กุมภาพันธ์",
                    "มีนาคม",
                    "เมษายน",
                    "พฤษภาคม",
                    "มิถุนายน",
                    "กรกฎาคม",
                    "สิงหาคม",
                    "กันยายน",
                    "ตุลาคม",
                    "พฤศจิกายน",
                    "ธันวาคม"
                ),
            str = "<select name='txt-month' class='validate[required]' id='txt-month'>";

        for(var i=0;i<=12;i++){
        	
            v = i!=0?i:'';
            var selected = curr==v?"selected=selected":"";
            str+="<option value='"+v+"' "+selected+">"+month[i]+"</option>";
        }
        str+="</select>";

        return str;
    }
    function cboYear(curr){
        var d = new Date();
        var y = d.getFullYear();
        var str = "<select name='txt-year' id='txt-year' class='validate[required]'>";
        str+="<option value=''>--ปี--</option>";
        for(var i=y;i>=(y-100);i--){
        	var selected = i==v?"selected=selected":"";
            str+="<option value='"+i+"' "+selected+">"+i+"</option>";
        }
        str+="</select>";

        return str;
    }
    function cboZone(ele,act,zip){
        var link,title;
        $("#"+ele).html('');
        
        
        	
        switch(act){
            case "province":
                link = "/member/index/getprovince";
                title = "--จังหวัด--";
                break
            case "amphur":
                link = "/member/index/getamphur";
                title = "--อำเภอ--";
                break
            case "tumbol":
                link = "/member/index/gettumbol";
                title = "--ตำบล--";
                break

        }
        var opt = {
                    url:link,
                    type:"post",
                    data:{"zip":zip},
                    dataType:"json",
                    success:function(data){
                        $("#"+ele).append('<option value="">'+title+'</option>');
                        $.each(data,function(i,val){
                            $("#"+ele).append('<option value="'+val.id+'" rel="'+val.zip+'">'+val.name+'</option>');
                        });
                    }
                }
        $.ajax(opt);
    }
    function clearZone()
    {
        $("#txt-zip").val('');
        $("#txt-tumbol").html('');
    }
    function createRegisForm(data,card_type){
    	var msex="",fsex="";
    	if(data[0].sex=='M') {
             msex =  "checked='checked'";
    	}else{
			fsex = "checked='checked'";
    	}
         var bdate = data[0].birthday.split('-');
         
        $("<div/>")
        .attr({
                "id":"register-form"
            })
        .append(
                "<div id='rigis-containner'><form id='regist-form' name='regist-form'>"+
                " <div id='regis-top'></div>"+
                " <div id='regis-tab'>"+
                " <ul>"+
                " <li><a href=\"#tabs-1\">ข้อมูลบัตร-Card Infomation</a></li>"+
                " <li><a href=\"#tabs-2\">สถานที่ติดต่อ -Address</a></li>"+
                " </ul>"+
                " <div id=\"tabs-1\">"+
                " <ul class='tb-details'>"+
                " <li>รหัสบัตรสมาชิก<input type='hidden' id='member_id' name='member_id' value='"+data[0].member_id+"'/></li>"+
                " <li class='duecol'>"+chrbox(data[0].member_id)+"</li>"+
                " <li>วันที่สมัคร</li>"+
                " <li class='duecol'>"+chrbox(data[0].doc_date)+"</li>"+
                " <li>วันที่หมดอายุ</li>"+
                " <li class='duecol'>"+chrbox(data[0].expire_date)+"</li>"+
                " <li>สาขาที่สมัคร</li>"+
                " <li class='duecol'>"+chrbox(data[0].branch_id)+"</li>"+
                " </ul>"+
                " <ul class='tb-details'>"+
                " <li>ชื่อ</li>"+
                " <li class='duecol'><input type=\"text\" id='txt-name' name='txt-name' class='validate[required] input-text-pos w-350 text-input' value='"+data[0].name+"'/></li>"+
                " <li>สกุล</li>"+
                " <li class='duecol'><input type='text' id='txt-last-name' name='txt-last-name' class='validate[required] input-text-pos w-350' value='"+data[0].surname+"'/></li>"+
                " <li>เพศ</li>"+
                " <li class='duecol'><input type='radio' name='txt-sex' id='txt-sex' value='M' "+msex+"/>ชาย<input type='radio' name='txt-sex' id='txt-sex' value='F' "+fsex+"/>หญิง</li>"+
                " <li>สัญชาติ</li>"+
                " <li class='duecol'><input type='radio' name='txt-nationality' id='txt-nationality' checked value='th'/>ไทย <input type='radio' name='txt-nationality' id='txt-nationality' value='na'/> ต่างชาติ <input type='radio' name='txt-nationality' id='txt-nationality' value='na'/> ต่างชาติ (ที่มีที่อยู่ในไทย)</li>"+
                " <li>เลขที่บัตรประชาชน</li>"+
                " <li class='duecol'><input type='text' name='txt-id-card' id='txt-id-card' maxlength='13' class='validate[idCard] input-text-pos w-350' value='"+data[0].id_card+"'/></li>"+
                " <li>วันเกิด</li>"+
                " <li class='duecol'>"+cboDay(bdate[0])+" "+cboMonth(bdate[1])+" "+cboYear(bdate[2])+" <div class='next'></div></li>"+
                " </ul>"+
                " </div>"+
                " <div id=\"tabs-2\">"+
                " <p id='tab-2-containner'>"+
                " </p>"+
                " </div>    "+
                " </div>"+
                "</form>"+
                "</div>"
            )
        .appendTo("body");
        createTab2Containner(false,data);
        $("#txt-name").focus();
        $("#txt-id-card").numeric(false);

        $("#register-form")
        .dialog({
                "modal":true,
                "width":850,
                "height":700,
                "resizable":false,
                "closeOnEscape" : true,
                buttons: [
                          {
                              text: "บันทึก",
                              click: function() { saveRegister(); }
                          },
                          {
                              text: "ปิด",
                              click: function() {  $(this).dialog("close"); }
                          }

                          ],
                "title": "ข้อมูลสมาชิก",
                close: function(event, ui) { removeValidLabel(); }

            });
        if(card_type==0){
            $("#regis-tab").tabs({ disabled: [1] });
            $("#regist-form").validationEngine();
            //var param = region=='th'?true:false;

        }else{
            $("#regis-tab").tabs();
            $(".next").remove();
        }
    }
    function saveRegister(){
            var fdata = $("#regist-form").serialize();
            var promp = chkCompleteFrom();
            if(promp){
                var opt = {
                            url : "/member/index/setmemberprofile",
                            type: "post",
                            data:fdata,
                            success:function(data){
                                if(data==0){
                                    jAlert("บันทึกข้อมูลสำเร็จ","ข้อความแจ้ง เตือน",function(){
                                        $("#register-form").dialog("close");
                                        $("#tblRegCard").flexReload()
                                        return false;
                                    });
                                }else{
                                    jAlert("บันทึกข้อมูลผิดพลาด","ข้อความแจ้ง เตือน",function(){
                                        return false;
                                    });
                                }

                            }

                        };
                $.ajax(opt);
            }


    }
    function flgCommand(com,grid){
        switch(com)
        {
            case "Refresh" :
                var card_type=$("input[@name=cardtype]:checked").val();
                $("#tblRegCard").flexOptions({newp:1}).flexReload();
                break;
            case "Register" :
                var card_type=$("input[@name=cardtype]:checked").val();
                if($('.trSelected', grid).length==0){
                    jAlert("กรุณาเลือกรายการที่ต้องการกรอกข้อมูลสมาชิก","ข้อความ แจ้ง เตือน");
                    return false;
                };

                if($("#register-form").length) $("#register-form").remove(); //Remove Old form

                $('.trSelected', grid).each(
                        function(){
                            id = $(this).attr("absid");
                        })//Get Selectd row Id

                var obj = {
                            url: "/member/index/registerdetail",
                            type:"post",
                            data:{
                                'id':id
                            },
                            dataType:"json",
                            success : function(data){
                                    createRegisForm(data,card_type)//call create form function
                                }
                          }
                $.ajax(obj); //Call select row data
                if(card_type==1){
                    $("#regis-tab").tabs( 'enable',1 );
                    /*jAlert("รายการนี้ถูกทำรายการเรียบร้อยแล้ว","ข้อความแจ้ง เตือน");
                    return false;*/
                }

                break;
            case "dblclick":
                var card_type=$("input[@name=cardtype]:checked").val();

                if($("#register-form").length) $("#register-form").remove(); //Remove Old form

                $('.trSelected', grid).each(
                        function(){
                            id = $(this).attr("absid");
                        })//Get Selectd row Id

                var obj = {
                            url: "/member/index/registerdetail",
                            type:"post",
                            data:{
                                'id':id
                            },
                            dataType:"json",
                            success : function(data){
                                    createRegisForm(data,card_type)//call create form function
                                }
                          }
                $.ajax(obj); //Call select row data


                break;
            default :
                break;
        }
    }//func

    function chkCompleteFrom()
    {
        if ($(".formErrorContent").length>0) {
            return false;
        }
        else{
            return true;
        }
    }
    function removeValidLabel()
    {
        $(".formError").each(function(){ $(this).remove(); })
    }

    function createTab2Containner(valid,data){    	
        var validClass = valid==true?"validate[required]":"";       
        if(data!=null){
	        str = " <ul class='tb-details'>"+
	                " <li>เลขที่</li>"+
	                " <li class='qaudcol'><input type='text' name='txt-address' id='txt-address' class='"+validClass+" input-text-pos w-80' value='"+data[0].h_address+"' /></li>"+
	                " <li>หมู่</li>"+
	                " <li class='qaudcol'><input type='text' name='txt-moo' id='txt-moo' class='input-text-pos w-80' value='"+data[0].h_address+"'/></li>"+
	                " <li>หมู่บ้าน</li>"+
	                " <li class='qaudcol'><input type='text' name='txt-ban' id='txt-ban' class='input-text-pos w-200' value='"+data[0].h_village+"'/></li>"+
	                " <li>ซอย</li>"+
	                " <li class='qaudcol'><input type='text' name='txt-soi' id='txt-soi' class='input-text-pos w-200' value='"+data[0].h_soi+"'/></li>"+
	                " <li>ถนน</li>"+
	                " <li class='qaudcol'><input type='text' name='txt-road' id='txt-road' class='input-text-pos w-200' value='"+data[0].h_road+"'/></li>"+
	                " <li>จังหวัด</li>"+
	                " <li class='qaudcol'><select name='txt-province' id='txt-province' class='"+validClass+" w-200' ><option>"+data[0].zip_province_nm_th+"</option></select></li>"+
	                " <li>เขต/อำเภอ</li>"+
	                " <li class='qaudcol'><select name='txt-amphur' id='txt-amphur' class='"+validClass+" w-200'><option>"+data[0].zip_amphur_nm_th+"</option></select></li>"+
	                " <li>แขวง/ตำบล</li>"+
	                " <li class='qaudcol'><select name='txt-tumbol' id='txt-tumbol' class='"+validClass+" w-200'><option>"+data[0].zip_tambon_nm_th+"</option></select></li>"+
	                " <li>รหัสไปรณี</li>"+
	                " <li><input type='text' id='txt-zip' name='txt-zip' readonly='readonly' class='input-text-pos' value='"+data[0].h_postcode+"'/></li>"+
	                " <li>มือถือ</li>"+
	                " <li><input type='text' name='txt-mobile' id='txt-mobile' class='validate[phone] input-text-pos w-350' value='"+data[0].mobile_no+"'/></li>"+
	                " <li>เบอร์บ้าน</li>"+
	                " <li><input type='text' name='txt-home-tel' id='txt-home-tel' class='input-text-pos w-350' value='"+data[0].h_tel_no+"'/></li>"+
	                " <li>เบอร์ที่ทำงาน</li>"+
	                " <li><input type='text' name='txt-office-tel' id='txt-office-tel' class='input-text-pos w-350' value='"+data[0].o_tel_no+"'/></li>"+
	                " <li>อีเมล</li>"+
	                " <li><input type='text' name='txt-email' id='txt-email' class='validate[custom[email]] input-text-pos w-350' value='"+data[0].email+"'/></li>"+
	                " </ul>";	        	
        }
        else{
        	 str = " <ul class='tb-details'>"+
             " <li>เลขที่</li>"+
             " <li class='qaudcol'><input type='text' name='txt-address' id='txt-address' class='"+validClass+" input-text-pos w-80' /></li>"+
             " <li>หมู่</li>"+
             " <li class='qaudcol'><input type='text' name='txt-moo' id='txt-moo' class='input-text-pos w-80' /></li>"+
             " <li>หมู่บ้าน</li>"+
             " <li class='qaudcol'><input type='text' name='txt-ban' id='txt-ban' class='input-text-pos w-200' /></li>"+
             " <li>ซอย</li>"+
             " <li class='qaudcol'><input type='text' name='txt-soi' id='txt-soi' class='input-text-pos w-200' /></li>"+
             " <li>ถนน</li>"+
             " <li class='qaudcol'><input type='text' name='txt-road' id='txt-road' class='input-text-pos w-200' v/></li>"+
             " <li>จังหวัด</li>"+
             " <li class='qaudcol'><select name='txt-province' id='txt-province' class='"+validClass+" w-200' ></select></li>"+
             " <li>เขต/อำเภอ</li>"+
             " <li class='qaudcol'><select name='txt-amphur' id='txt-amphur' class='"+validClass+" w-200'></select></li>"+
             " <li>แขวง/ตำบล</li>"+
             " <li class='qaudcol'><select name='txt-tumbol' id='txt-tumbol' class='"+validClass+" w-200'></select></li>"+
             " <li>รหัสไปรณี</li>"+
             " <li><input type='text' id='txt-zip' name='txt-zip' readonly='readonly' class='input-text-pos' /></li>"+
             " <li>มือถือ</li>"+
             " <li><input type='text' name='txt-mobile' id='txt-mobile' class='validate[phone] input-text-pos w-350' /></li>"+
             " <li>เบอร์บ้าน</li>"+
             " <li><input type='text' name='txt-home-tel' id='txt-home-tel' class='input-text-pos w-350' /></li>"+
             " <li>เบอร์ที่ทำงาน</li>"+
             " <li><input type='text' name='txt-office-tel' id='txt-office-tel' class='input-text-pos w-350' /></li>"+
             " <li>อีเมล</li>"+
             " <li><input type='text' name='txt-email' id='txt-email' class='validate[custom[email]] input-text-pos w-350'/></li>"+
             " </ul>";
        }
        $("#tab-2-containner").html(str);
        
        $("#regist-form").validationEngine();        
        
        if(valid!=false){
        	cboZone('txt-province','province',null); //append province option
        	//cboZone('txt-amphur','amphur',$("#txt-amphur").attr('rel')); //append province option
        	//cboZone('txt-tumbol','tumbol',$("#txt-tumbol").attr('rel')); //append province option
        }
        
        $("#txt-province").bind("change",
                function(){
                    var zip = $("option:selected",this).attr("rel");
                    cboZone('txt-amphur','amphur',zip);
                    clearZone();
                }
        )//bind new object
        $("#txt-amphur").bind("change",
                function(){
                    var zip = $(this).val();
                    cboZone('txt-tumbol','tumbol',zip);
                    $("#txt-zip").val('');
                }
        )//bind new object
        $("#txt-tumbol").bind("change",
                function(){
                    var zip = $("option:selected",this).attr("rel");
                    $("#txt-zip").val(zip);
                }
        )//bind new object
    }

    $(function(){

        var e = $.Event('keydown');

        e.which = 13; // TAB
        $(':focus').live('keydown',function(e){

            if(e.which == 13){
                var fields = $(this).parents('form:eq(0),body').find('button,input,textarea,select,radio');
                var index = fields.index( this );
                //alert(index)
                if ( index > -1 && ( index + 1 ) < fields.length ) {
                    fields.eq( index + 1 ).focus();
                }
                return false;

            }
        });

        $("#txt-year").live("keydown",function(e){
             if(e.which == 13){
                 var vreturn = chkCompleteFrom();
                 var jQuerytabs = $("#regis-tab");
                 var selected = jQuerytabs.tabs('option', 'selected');
                 var region = $("input[name=txt-nationality]:checked").val();
                 //alert(region);

                 if(vreturn){
                     var param = region=='th'?true:false;
                     createTab2Containner(param,null);

                     jQuerytabs.tabs( 'enable',1 );
                     jQuerytabs.tabs('select', selected+1);
                     $("#txt-address").focus();

                 }
             }

        })

        $(".next").live('click',
                function(){
                     $("#regist-form").submit();
                     var vreturn = chkCompleteFrom();
                     var jQuerytabs = $("#regis-tab");
                     var selected = jQuerytabs.tabs('option', 'selected');
                     var region = $("input[name=txt-nationality]:checked").val();
                     //alert(region);

                     if(vreturn){
                         var param = region=='th'?true:false;
                         createTab2Containner(param,null);

                         jQuerytabs.tabs( 'enable',1 );
                         jQuerytabs.tabs('select', selected+1);
                         $("#txt-address").focus();

                     }
            })

        $("input,select")

        $("#tblRegCard tbody tr").live("dblclick",function(){
            id = $(this).attr('absid');
            flgCommand('dblclick','tblRegCard');
        });

        $("#regist-form").live("submit",function(){ return false; })
        //init flexigrid
        var card_type=$("input[@name=cardtype]:checked").val();
        var gHeight =377;
        if ((screen.width>=1280) && (screen.height>=1024)){
            gHeight=(screen.height-(screen.height*(32/100)));
        }

        $("#tblRegCard").flexigrid(
                {
                    url:'/member/index/registerlist',
                    dataType: 'json',
                    colModel : [
                                {display: '#', name : 'id', width :50, sortable : true, align: 'center'},
                                {display: 'รหัสสมาชิก', name : 'member_id', width : 150, sortable : true, align: 'center'},
                                {display: 'เลขที่เอกสาร', name : 'doc_no',width :250, sortable : false, align:'center'},
                                {display: 'วันที่ลงทะเบียน', name : 'doc_date', width :120, sortable : false, align: 'center'},
                                {display: 'ผู้ทำรายการ', name : 'reg_user', width :120, sortable : false, align: 'center'},
                                {display: 'สถานะ', name : 'status_no', width :100, sortable : false, align: 'center'}
                                ],
                    sortname: "doc_date",
                    sortorder: "desc",
                    action:'gettmp',
                    usepager:true,
                    singleSelect: true,
                    nowrap: false,
                    qtype:'flag_save',
                    query:card_type,
                    title:'รายการบัตร',
                    useRp:true,
                    rp:10,
                    buttons : [
                    {name:'Register',bclass: 'flgBtnAddClass',onpress : flgCommand},
                    {separator: true},
                    {name:'Refresh',bclass:'flgBtnRefClass',onpress :flgCommand}
                    ],
                    searchitems : [
                        {display:'รหัสสมาชิก',name:'member_id'}
                    ],
                    showTableToggleBtn:true,
                    striped:false,
                    height:gHeight
                }
        );//end flexigrid

        $("input[name=cardtype]:radio").click(function(){
            var card_type=$("input[@name=cardtype]:checked").val();
            $("#tblRegCard").flexOptions({newp:1,qtype:'flag_save',query:card_type}).flexReload();
        });
    });//ready
