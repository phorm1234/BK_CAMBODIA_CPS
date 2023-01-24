 function closeWinIframe() {
     var frameWindow = document.parentWindow || document.defaultView;
     var outerDiv = $(frameWindow.frameElement.parentNode.parentNode);
     var curWindow = outerDiv.find(".window_top").contents().find("a.window_close");
     $(curWindow).closest('div.window').hide();
     var icons_id = curWindow.attr('href');
     $(icons_id, window.parent.document).hide('fast');
     //refresh parent
     window.parent.location.href = window.parent.location.href;
     return false;
 } //func

 function clearBill(msgResult, msgTitle) {
     $.ajax({
         type: "post",
         url: "/sales/cashier/clearbill",
         data: {
             now: Math.random()
         },
         success: function(data) {
             var arr = data.split('#');
             console.log(arr[0]);
             jAlert(msgResult, msgTitle, function() {
                 if (arr[0] == "01" && arr[1] == "01") {
                     $('#dlg_closeday').dialog("close");
                 } else {
                     $('#dlg_closeday').dialog("close");
                 }
             });
             return false;
         }
     });
 }

 function closeDay() {
     /**
      * @param
      * @author is-wirat		 
      * @returns
      */
     var dialogOpts_closeday = {
         autoOpen: false,
         width: 370,
         height: 250,
         modal: true,
         resizable: true,
         position: ['center', 'center'],
         title: "Confirm Close Daily Bill",
         closeOnEscape: true,
         open: function() {
             $(this).parents(".ui-dialog:first").css({ "padding": "3px", "margin": "0 0 0 0", "border-color": "#C6D5DC" });
             $(this).dialog('widget')
                 .find('.ui-dialog-titlebar')
                 .removeClass('ui-corner-all')
                 .addClass('ui-corner-top');
             $(this).parents(".ui-dialog:first").find(".ui-dialog-content").css({
                 "padding": "10px 5px",
                 "margin-top": "0",
                 "background-color": "#bca0c9",
                 /*BCDCD7*/
                 "font-size": "27px",
                 "color": "#000"
             });
             $(this).dialog("widget").find(".ui-dialog-buttonpane")
                 .css({ "padding": ".1em .1em .1em 0", "margin": "0 0 0 0", "background-color": "#c2bcdc" }); /*C7D9DC*/
             // button style		
             $(this).dialog("widget").find("button")
                 .css({ "padding": "0 .1em 0 .1em", "margin": "0 .1em 0 0" });
             $("#dlg_closeday").html("");
             $("#dlg_closeday").load("/sales/cashier/formconfirmcloseday?action=confirmcloseday&now=" + Math.random(),
                 function(data) {
                     $("#sel_closeday").datepicker({ dateFormat: 'dd/mm/yy', altField: '#sel_closeday_alternate', altFormat: 'yy-mm-dd' });
                 }
             );
         },
         close: function() {
             var sel_closeday = $("#sel_closeday_alternate").val();
             $('#sel_closeday').datepicker('hide');
             $('#dlg_closeday').dialog('destroy');
             //close iframe
             closeWinIframe();
         },
         buttons: {
             "Confirm Close Bill": function(evt) {
                 evt.preventDefault();
                 evt.stopImmediatePropagation();

                 var buttonDomElement = evt.target;
                 $(buttonDomElement).attr('disabled', true);

                 var $sel_docdate = $("#sel_docdate");
                 arr_day = $sel_docdate.val().split('/');
                 $sel_docdate = arr_day[2] + "-" + arr_day[1] + "-" + arr_day[0];
                 var $sel_closeday_alt = $("#sel_closeday_alternate");
                 if ($sel_closeday_alt.val() == '') {
                     jAlert('Please Enter Date of close bill', 'Alert!', function() {
                         return false;
                     });
                 } else if ($sel_closeday_alt.val() < $sel_docdate) {
                     jAlert('Can not close backward bill', 'Alert!', function() {
                         return false;
                     });
                 } else if ($sel_docdate != $sel_closeday_alt.val()) {
                     jAlert('Date of close bill not match', 'Alert!', function() {
                         return false;
                     });
                 } else {
                     var opts = {
                         type: 'post',
                         url: '/sales/cashier/chkforcloseday',
                         cache: false,
                         data: {
                             doc_date: $sel_closeday_alt.val(),
                             now: Math.random()
                         },
                         success: function(data) {
                             var arr_data = data.split('#');
                             var msgResult = "";
                             var msgTitle = "Alert!";
                             if (arr_data[0] == '1') {
                                 //call EDC													
                                 var opts_calledc = $.ajax({
                                     type: 'post',
                                     url: '/sales/cashier/calledc',
                                     cache: false,
                                     data: {
                                         actions: 'settlement',
                                         credit_net_amt: '0.00',
                                         rnd: Math.random()
                                     },
                                     success: function() {
                                         jAlert('Please insert password Summary total amount of EDC credits', 'Alert!', function(r) {
                                             if (r) {
                                                 opts_calledc = null;
                                                 msgResult = "The daily bill have been closed<br> <u>Please send the data and wait for the result before turn off</u>";
                                                 msgTitle = "Result of Daily close bill";
                                                 clearBill(msgResult, msgTitle);
                                                 return false;
                                             }
                                         });
                                     }
                                 });
                                 return false;
                             } else if (arr_data[0] == '2') {
                                 msgResult = "can not find sales data on date " + $("#sel_docdate").val();
                                 msgTitle = "Confirm Close Daily Bill";
                                 jConfirm(msgResult + ' Do you want to close bill?', msgTitle, function(r) {
                                     if (r) {
                                         var opts2 = {
                                             type: 'get',
                                             url: '/sales/cashier/confirmcloseday',
                                             cache: false,
                                             data: {
                                                 action: 'confirmcloseday',
                                                 rnd: Math.random()
                                             },
                                             success: function(data) {
                                                 if (data == '1') {
                                                     //call EDC
                                                     var opts_calledc = $.ajax({
                                                         type: 'post',
                                                         url: '/sales/cashier/calledc',
                                                         cache: false,
                                                         data: {
                                                             actions: 'settlement',
                                                             credit_net_amt: '0.00',
                                                             rnd: Math.random()
                                                         },
                                                         success: function() {
                                                             jAlert('lease insert password Summary total amount of EDC credits', 'Alert!', function(r) {
                                                                 if (r) {
                                                                     opts_calledc = null;
                                                                     ///////////////////// show message close day is complete /////////////////////
                                                                     msgResult = "The daily bill have been closed<br> <u>Please send the data and wait for the result before turn off</u>";
                                                                     msgTitle = "Result of Daily close bill";
                                                                     clearBill(msgResult, msgTitle);
                                                                     return false;
                                                                 }
                                                             });
                                                         }
                                                     }); //end call edc																		

                                                 } else {
                                                     $(buttonDomElement).attr('disabled', false);
                                                     jAlert("Can not close the bill in advance.", msgTitle, function() {
                                                         return false;
                                                     });
                                                 }
                                             }
                                         };
                                         $.ajax(opts2);
                                         return false;
                                     } else {
                                         $(buttonDomElement).attr('disabled', false);
                                     }
                                 });
                             } else if (arr_data[0] == '3') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Document No. " + arr_data[2] + " are discontinuous from a checking of close backward bill";
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '4') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Document No. are discontinuous from a checking the continuous of each type Document No.";
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '5') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Found credit note No. " + arr_data[1] + "  does not open product change bill";
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '6') {
                                 msgResult = "Found the Approve data RQ must be confirmed RQ first";
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '7') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Profile of members must be recorded within 2 days, From the date of register.";
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '8') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Found sales data during check stock";
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '9') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Can not close the bill before. " + arr_data[1];
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             } else if (arr_data[0] == '10') {
                                 $(buttonDomElement).attr('disabled', false);
                                 msgResult = "Found the data of Transfer during check stock " + arr_data[1];
                                 jAlert(msgResult, msgTitle, function() {
                                     return false;
                                 });
                             }
                             return false;
                         }
                     };
                     $.ajax(opts);
                 }
                 return false;
             }
         }
     };
     $('#dlg_closeday').dialog('destroy');
     $('#dlg_closeday').dialog(dialogOpts_closeday);
     $('#dlg_closeday').dialog('open');
     return false;
 } //func

 function initTblTemp() {
     /**
      *@desc
      *@return
      */
     $.ajax({
         type: 'post',
         url: '/sales/cashier/initbltemp',
         cache: false,
         data: {
             rnd: Math.random()
         },
         success: function(data) {}
     });
 } //func

 $(function() {
     initTblTemp();
     closeDay();
 });