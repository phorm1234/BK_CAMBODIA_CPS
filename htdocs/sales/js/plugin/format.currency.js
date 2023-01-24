(function($) {
	/** +++++ function formatCurrency +++++ */
	$.fn.formatCurrency = function(options) {
		var defaults = {
			splitChar : ',',
			allowChar : '0123456789.-',
			splitLen : 3
		};

		var opts = $.extend(defaults, options);
		return this.each(function(){
			//When Press Keyboard
			$(this).keypress(function(e){
				//Allow enter, backspace,delete
				if(e.which == 13 || e.which == 8 || e.which==0) {
					return true;
				}
				//Disable ctrl,alt key
				if(e.ctrlKey || e.altKey) {
					return false;
				} 

				var ch = String.fromCharCode(e.which);
				if(ch == "-"){
					var value = $(this).val();
					if(value.lastIndexOf("-") > 0) return false;
				}
				return opts.allowChar.indexOf(ch) > -1;
			});
			//When Up keyboard
			$(this).keyup (function(e){
				var value = $(this).val();
				var subChar = "";
				value = value.replace(/,/gi,"");
				if(value.indexOf("-") >= 0){
					value = value.replace(/-/gi,"");
					subChar = "-";
				}
				//Split Decimals
				var arrs = value.toString().split(".");
				//Split data and reverse
				var revs = arrs[0].split("").reverse().join("");
				var len = revs.length;
				var tmp = "";
				for(i = 0; i < len; i++){
					if(i > 0 && (i%opts.splitLen) == 0){
						tmp += opts.splitChar + revs.charAt(i);
					}else{
						tmp += revs.charAt(i);
					}
				} 

				//Split data and reverse back
				tmp = tmp.split("").reverse().join("");
				if(arrs.length > 1 && arrs[1] != undefined){
					tmp += "."+ arrs[1];
					tmp = subChar+tmp;
				}else{
					tmp = subChar+tmp;
				}

				$(this).val(tmp);
			});
		});
	};
})(jQuery);
