	/*
	*@param dateinput
	*@return valid date format for insert to mysql dd mm yyyy
	*@desc for support plugin jquery jquery.maskedinput-1.2.2.min.js
	*@date modify 24092009
	*/
	function check_formatdate2mysql(dateinput){
		var date_input="#"+dateinput;
		var mydate=$(date_input).val();		
		var arr_date=mydate.split('/');
		var dd=parseInt(arr_date[0],10);
		var mm=parseInt(arr_date[1],10);
		var yyyy=parseInt(arr_date[2]);
		
		var chk_d = arr_date[0].indexOf('_');
		if (chk_d != -1 ) {
		  	alert("รูปแบบวันที่ต้องเป็นตัวเลขสองหลัก 01-31");
			$(date_input).focus();			
			return false;
		} 
		
		var chk_m = arr_date[1].indexOf('_');
		if (chk_m != -1 ) {
		  	alert("รูปแบบเดือนต้องเป็นตัวเลขสองหลัก 01-31");
			$(date_input).focus();
			return false;
		} 
		
		var chk_y = arr_date[1].indexOf('_');
		if (chk_y != -1 ) {
		  	alert("รูปแบบปีต้องเป็นตัวเลขสี่หลักคริสตศักราช เช่น 2009");
			$(date_input).focus();
			return false;
		} 
				
		if(dd<1 || dd >31){
			alert("รูปแบบวันที่ต้องเป็นตัวเลขระหว่าง 01-31");
			$(date_input).focus();
			return false;
		}		
		
		if(mm<1 || mm >12){
			alert("รูปแบบเดือนต้องเป็นตัวเลขระหว่าง 01-12");
			$(date_input).focus();			
			return false;
		}		
		
		var curr_year=getYearCurrent();
		if(yyyy < curr_year || yyyy > (curr_year+1)){
				alert("รูปแบบปีต้องเป็นตัวเลขสี่หลักคริสตศักราช เช่น 2009 และ เป็นปีปัจจุบัน หรือมากกว่าปีปัจจุบัน 1 ปี");
				$(date_input).focus();
				return false;
		}	
		return true;		
	}//func
	
	/*
	*@param none
	*@return current year
	*@desc get year current
	*/
	function getYearCurrent(){
		var currentTime = new Date()
		//var month = currentTime.getMonth() + 1
		//var day = currentTime.getDate()
		var year = currentTime.getFullYear()
		//document.write(month + "/" + day + "/" + year)
		return year;
	}//func
	//------------------------------------------------------------------------------------------------------
	/**
	 * Form text box hints.
	 * 
	 * This plug-in will allow you to set a 'hint' on a text box or
	 * textarea.  The hint will only display when there is no value
	 * that the user has typed in, or that is default in the form.
	 * 
	 * You can define the hint value, either as an option passed to
	 * the plug-in or by altering the default values.  You can also
	 * set the hint class name in the same way.
	 * 
	 * Examples of use:
	 * 
	 *     $('form *').textboxhint();
	 * 
	 *     $('.date').textboxhint({
	 *         hint: 'YYYY-MM-DD'
	 *     });
	 * 
	 *     $.fn.textboxhint.defaults.hint = 'Enter some text';
	 *     $('textarea').textboxhint({ classname: 'blurred' });
	 *
	 * @copyright Copyright (c) 2009, 
	 *            Andrew Collington, andy@amnuts.com
	 * @license New BSD License
	 */
	(function($) {
		$.fn.textboxhint = function(userOptions) {
			var options = $.extend({}, $.fn.textboxhint.defaults, userOptions);
			return $(this).filter(':text,textarea').each(function(){
				if ($(this).val() == '') {
					$(this).focus(function(){
						if ($(this).attr('typedValue') == '') {
							$(this).removeClass(options.classname).val('');
						}
					}).blur(function(){
						$(this).attr('typedValue', $(this).val());
						if ($(this).val() == '') {
							$(this).addClass(options.classname).val(options.hint);
						}
					}).blur();
				}
			});
		};
	 
		$.fn.textboxhint.defaults = {
			hint: 'Please enter a value',
			classname: 'hint'
		};
	})(jQuery);
	
	//--------------------------------------------------------------------------------------------------------
	function reDirectTo(_url){	
		if(_url=="") return false;
		window.location=_url;
	}
	//--------------------------------------------------------------------------------------------------------
	
	function trim2(thisString){
		var newString = thisString;
		while (newString.charCodeAt(0) < 33)
		{
			newString = newString.substring(1,newString.length);
		}		
		while (newString.charCodeAt(newString.length - 1) < 33)
		{
			newString = newString.substring(0, newString.length - 1);
		}
		return newString;
	}//func
	
	//--------------------------------------------------------------------------------------------------------

	function ValidateIntegerFields(thisform,Fields,Desc,AllowComma,AllowNeg)
	{
		var tmpVal='';
		var FieldArray=new Array();
		var DescArray=new Array();
		var tmp=0;
		var tmpMsg='';
		// Create array for Field list
		tmpVal=Fields;
		do
		{
			tmp=tmpVal.indexOf('|');
			if (tmp == -1)
			{
				if (tmpVal != '')
				{
					FieldArray[FieldArray.length]=tmpVal;
					tmpVal='';
				}
			} else {
				FieldArray[FieldArray.length]=tmpVal.substring(0,tmp);
				tmpVal=tmpVal.substring(tmp + 1);
			}
		}
		while (tmpVal != '');
		// Create array for Desc list
		tmpVal=Desc;
		do
		{
			tmp=tmpVal.indexOf('|');
			if (tmp == -1)
			{
				if (tmpVal != '')
				{
					DescArray[DescArray.length]=tmpVal;
					tmpVal='';
				}
			} else {
				DescArray[DescArray.length]=tmpVal.substring(0,tmp);
				tmpVal=tmpVal.substring(tmp + 1);
			}
		}
		while (tmpVal != '');
		// Check to see if passed strings are of equal length
		if (FieldArray.length != DescArray.length)
		{
			alert('Fatal error: ValidateInteger - Passed lists do not have the same length');
			return false;
		}
		// Validate fields
		for (i=0; i<FieldArray.length;i++)
		{
			tmpMsg='';
			eval('tmpVal=thisform.' + FieldArray[i] + '.value');
			if (!AllowNeg && tmpVal < 0)
			{
				alert(DescArray[i] + ' ต้องเป็นตัวเลขจำนวนเต็มเท่านั้น\r\nmay not be a negative value.');
				// Fix for IWOF NSO
				if (FieldArray[i].substring(0,4) == 'Hold')
				{
					eval('thisform.Qty_' + FieldArray[i].substring(5) + '.select()');
				} else {
					eval('thisform.' + FieldArray[i] + '.select()')
				}
				return false;
			}
			if (!ValInt(tmpVal,AllowComma))
			{
				if (AllowComma != 1 && tmpVal.indexOf(',') != -1) tmpMsg=' without commas';
				if (tmpVal.indexOf('.') != -1) tmpMsg=' (no decimal fraction)';
				alert(DescArray[i] + ' ต้องเป็นตัวเลขเท่านั้น\r\nmust be an integer value' + tmpMsg + '.');
				// Fix for IWOF NSO
				if (FieldArray[i].substring(0,4) == 'Hold')
				{
					//eval('thisform.Qty_' + FieldArray[i].substring(5) + '.select()');
					eval('thisform.Qty_' + FieldArray[i].substring(5) + '.value=""');
				} else {
					//eval('thisform.' + FieldArray[i] + '.select()')
					eval('thisform.' + FieldArray[i] + '.value=""')
				}
				return false;
			}
		}
		return true;
	}//func
	//--------------------------------------------------------------------------------------------------------
	function ValidateFloatFields(thisform,Fields,Desc,AllowComma)
	{
		var tmpVal='';
		var FieldArray=new Array();
		var DescArray=new Array();
		var tmp=0;
		var tmpMsg='';
		// Create array for Field list
		tmpVal=Fields;
		do
		{
			tmp=tmpVal.indexOf('|');
			if (tmp == -1)
			{
				if (tmpVal != '')
				{
					FieldArray[FieldArray.length]=tmpVal;
					tmpVal='';
				}
			} else {
				FieldArray[FieldArray.length]=tmpVal.substring(0,tmp);
				tmpVal=tmpVal.substring(tmp + 1);
			}
		}
		while (tmpVal != '');
		// Create array for Desc list
		tmpVal=Desc;
		do
		{
			tmp=tmpVal.indexOf('|');
			if (tmp == -1)
			{
				if (tmpVal != '')
				{
					DescArray[DescArray.length]=tmpVal;
					tmpVal='';
				}
			} else {
				DescArray[DescArray.length]=tmpVal.substring(0,tmp);
				tmpVal=tmpVal.substring(tmp + 1);
			}
		}
		while (tmpVal != '');
		// Check to see if passed strings are of equal length
		if (FieldArray.length != DescArray.length)
		{
			alert('Fatal error: ValidateFloat - Passed lists do not have the same length');
			return false;
		}
		// Validate fields
		for (i=0; i<FieldArray.length;i++)
		{
			tmpMsg='';
			eval('tmpVal=thisform.' + FieldArray[i] + '.value');
			if (!ValFloat(tmpVal,AllowComma))
			{
				if (AllowComma != 1 && tmpVal.indexOf(',') != -1) tmpMsg=' without commas';
				alert(DescArray[i] + 'ต้องเป็นตัวเลขเท่านั้น\r\nmust be a numeric value ' + tmpMsg + '.');
				//eval('thisform.' + FieldArray[i] + '.focus()')
				//eval('thisform.' + FieldArray[i] + '.select()')
				eval('thisform.' + FieldArray[i] + '.value=""')
				return false;
			}
		}
		return true;
	}//func
	
	//--------------------------------------------------------------------------------------------------------

	function ValInt(ObjectValue,AllowComma)
	{
		// Used internally by VaidateInteger
	
		//Returns true if value is a number or is NULL
		//otherwise returns false	
		
		if (ObjectValue.length == 0) return true;
		
		//Returns true if value is an integer defined as
		//   having an optional leading + or -.
		//   otherwise containing only the characters 0-9.
		var decimal_format = ".";
		var check_char;
		
		//The first character can be + -  blank or a digit.
		check_char = ObjectValue.indexOf(decimal_format)
		//Was it a decimal?
		if (check_char < 1) return ValFloat(ObjectValue,AllowComma); else return false;
	}//func
	
	//--------------------------------------------------------------------------------------------------------

	function ValFloat(ObjectValue,AllowComma)
	{
		// Used Internally by ValidateInt, ValidateFloat
	
		//Returns true if value is a number or is NULL
		//otherwise returns false	
		
		if (ObjectValue.length == 0) return true;
		
		//Returns true if value is a number defined as
		//   having an optional leading + or -.
		//   having at most 1 decimal point.
		//   otherwise containing only the characters 0-9.
		var start_format = " .+-0123456789";
		var number_format = " .0123456789";
		if (AllowComma == 1) number_format=number_format + ',';
		var check_char;
		var decimal = false;
		var trailing_blank = false;
		var digits = false;
		
		//The first character can be + - .  blank or a digit.
		check_char = start_format.indexOf(ObjectValue.charAt(0))
		//Was it a decimal?
		if (check_char == 1)
			decimal = true;
		else if (check_char < 1)
			return false;
		
		//Remaining characters can be only . or a digit, but only one decimal.
		for (var i = 1; i < ObjectValue.length; i++)
		{
			check_char = number_format.indexOf(ObjectValue.charAt(i))
			if (check_char < 0)
				return false;
			else if (check_char == 1)
			{
				if (decimal)		// Second decimal.
					return false;
				else
					decimal = true;
			}
			else if (check_char == 0)
			{
				if (decimal || digits) trailing_blank = true;
				// ignore leading blanks
			}
			else if (trailing_blank)
				return false;
			else
				digits = true;
		}	
		//All tests passed, so...
		return true
	}//func
	
	//--------------------------------------------------------------------------------------------------------

	function formatDate2Mysql(vdate){
		var arr_date=vdate.split("/");
		var vd=arr_date[0];
		var vm=arr_date[1];
		var vy=arr_date[2];
		var new_f=vy+"-"+vm+"-"+vd;
		return new_f;
	}//func
	
	//--------------------------------------------------------------------------------------------------------	
	
	function formatDate2Show(vdate){
		var arr_date=vdate.split("-");
		var vd=arr_date[2];
		var vm=arr_date[1];
		var vy=arr_date[0];
		var new_f=vd+"/"+vm+"/"+vy;
		return new_f;
	}//func
	
	//---------------------------------------------------------------------------------------------------------
	
	function trim (str, charlist) {
		// Strips whitespace from the beginning and end of a string  
		// 
		// version: 905.1001
		// discuss at: http://phpjs.org/functions/trim
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: mdsjack (http://www.mdsjack.bo.it)
		// +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
		// +      input by: Erkekjetter
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +      input by: DxGx
		// +   improved by: Steven Levithan (http://blog.stevenlevithan.com)
		// +    tweaked by: Jack
		// +   bugfixed by: Onno Marsman
		// *     example 1: trim('    Kevin van Zonneveld    ');
		// *     returns 1: 'Kevin van Zonneveld'
		// *     example 2: trim('Hello World', 'Hdle');
		// *     returns 2: 'o Wor'
		// *     example 3: trim(16, 1);
		// *     returns 3: 6
		var whitespace, l = 0, i = 0;
		str += '';    
		if (!charlist) {
			// default list
			whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
		} else {
			// preg_quote custom list
			charlist += '';
			whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
		}//    
		l = str.length;
		for (i = 0; i < l; i++) {
			if (whitespace.indexOf(str.charAt(i)) === -1) {
				str = str.substring(i);
				break;
			}
		}//    
		l = str.length;
		for (i = l - 1; i >= 0; i--) {
			if (whitespace.indexOf(str.charAt(i)) === -1) {
				str = str.substring(0, i + 1);
				break;
			}
		}    
		return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
	}//func
	
	//------------------------------------------------------------------------------
	
	function checkReturn(event,nextFieldName){							
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;		
		//var next_field=$('#'+nextFieldName);							
		var next_field='#'+nextFieldName;	
		if (keyCode == 13){		
			$(next_field).focus();	
			return false;
		}else{
			return true;	
		}										
	}//func		
	//-----------------------------------------------------------------------------	
	//highligths the clicked row
	function highlightrow(obj) {		
		if ($(obj).attr("style")=='background-color: rgb(255, 255, 187);' || $(obj).attr("style")=='BACKGROUND-COLOR: #ffffbb') {
			$(obj).removeAttr("style");
		} else {
			$(obj).attr("style","background-color: #FFFFBB;");
		}
	}
	//-----------------------------------------------------------------------------	
	