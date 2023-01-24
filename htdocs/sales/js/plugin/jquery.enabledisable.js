/*
Disable Plugin
Works for IE7, FF3, Chrome, Safari, Opera
By: Chonla
Create Date: 3 December 2010
URL: http://blog.chonla.com
Original: Unknown source
*/

(function($)  {
	$.fn.disable = function() {return this.attr('readonly', true).removeClass('input-text-enable').addClass('label-text-disable');}
	$.fn.enable = function() {return this.removeClass('label-text-disable').addClass('input-text-enable').removeAttr('readonly');}
})(jQuery); 