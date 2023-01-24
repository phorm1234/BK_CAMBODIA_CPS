	function cmdEnterKey(inputTextField){
		////////////////////////////////////////
		if(jQuery.trim(inputTextField)=='') return false;
		var inputkey="#"+inputTextField;
		var e = jQuery.Event("keypress");
		e.keyCode = $.ui.keyCode.ENTER;
		$(inputkey).trigger(e);
		return false;
		///////////////////////////////////////		
	}//func

	function addCommas(nStr)
	{
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;	
	}//func	
	
	// Numeric only control handler
	jQuery.fn.ForceNumericOnly =function()
	{
	    return this.each(function()
	    {
	        $(this).keydown(function(e)
	        {
	            var key = e.charCode || e.keyCode || 0;
	            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
	            return (
		                key == 8 || 
		                key == 9 ||
		                key == 46 ||
		                (key >= 37 && key <= 40) ||
		                (key >= 48 && key <= 57) ||
		                (key >= 96 && key <= 105) ||
		                (key == 110 && key <= 190)//allow .
	                );
	        });
	    });
	};//fn