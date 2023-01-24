function requestServerCall(url) {
	  var head = document.head;
	  var script = document.createElement("script");
	  script.setAttribute("src", url);
	  head.appendChild(script);
	  head.removeChild(script);
}//func


function jsonpCallbackNa(data) {
  //alert(data.message); // Response data from the server
}//func

function openCashDrawerIframe(url,name,windowWidth,windowHeight){
		/**
		*@desc open window call url cash drawer file /var/www/pos/htdocs/sales/cmd
		*@param String url
		*@param String name
		*@param Integer windowWidth
		*@param Integer windowHeight
		*@param modify 04072013
		*@return null
		*/
	 	var myleft=0;
	    var mytop=0;
		windowWidth=100;
		windowHeight=20;
	    var properties = "width=" + windowWidth + ",height=" + windowHeight;	    
	    properties +=",top="+mytop+",left="+myleft + ",toolbar=no, menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no";	  
	    //myWindow =window.open(url,'_self',properties);
	    myWindow =window.open(url,'_blank',properties);
	    //myWindow.close();
	    setTimeout('myWindow.close();',2000);
	    //setTimeout('self.close();',5000);
	    return false;
}//func

function getWidth(){
		/**
		 * @desc 25012013
		 * @return
		 */
	    var xWidth = null;
	    if(window.screen != null)
	      xWidth = window.screen.availWidth;	 
	    if(window.innerWidth != null)
	      xWidth = window.innerWidth;	 
	    if(document.body != null)
	      xWidth = document.body.clientWidth;	 
	    return xWidth;
 }//func
	
function getHeight(){
		/**
		 * @desc 25012013
		 * @return
		 */
	  var xHeight = null;
	  if(window.screen != null)
	    xHeight = window.screen.availHeight;	 
	  if(window.innerHeight != null)
	    xHeight =   window.innerHeight;	 
	  if(document.body != null)
	    xHeight = document.body.clientHeight;	 
	  return xHeight;
}//func
		
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

function roundNumber(num, scale) {
	var number = Math.round(num * Math.pow(10, scale)) / Math.pow(10, scale);
	if(num - number > 0) {
		return (number + Math.floor(2 * Math.round((num - number) * Math.pow(10, (scale + 1))) / 10) / Math.pow(10, scale));
	} else {
		return number;
	}
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
