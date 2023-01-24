/*
 婆婆窗
Author:野草
Date:2013/12/04
Email:129#jinzhe.net
Version:0.2
*/
(function($){
	$.dialog=function(options){
		var options=$.extend({title:'',width:500, height:300,left:false,top:false,html:'',mask:true,drag:true,close:true,callback:function(){}},options);
		try{
			$('#dialog-layout').fadeOut('fast',function(){
				$('#dialog-mask').remove();
				$(this).empty().remove();
			});
		}catch(e){}
		if(options.mask){
			var mask=$('<div />');
				mask.attr('id','dialog-mask');
				mask.addClass('dialog-mask');
				mask.css({
					top:0,
					left:0,
					width:$(document).width()+'px',
					height:$(document).height()+'px'
				});
				$(document.body).append(mask);	
		}
		var layout=$('<div />');
			layout.attr('id','dialog-layout');
			layout.addClass('dialog-layout');
			layout.css({
				top:(($(window).height()-options.height)/2+$(document).scrollTop())+'px',
				left:($(document).width()-options.width)/2+'px',
				width:options.width+'px',
				height:options.height=='auto'?100:options.height+'px'
			});
		$(document.body).append(layout);
		var content=$('<div />');
			content.attr('id','dialog-content');
			content.addClass('dialog-content');
			content.width(options.width);
			content.height(options.height);
			content.appendTo(layout);
		var title=$('<div />');
 			title.addClass('dialog-title');
			title.appendTo(content);
			title.html(options.title).wrapInner("<span />");
 		if(options.close){
			var close=$('<a href="javascript:void(0)" id="dialog-close">x</a>');
				close.addClass('dialog-close');
				close.appendTo(content);
				close.bind('click',function(){
					$('#dialog-layout').addClass('dialog-layout-close');
					setTimeout(function(){
						$('#dialog-mask').remove();	
						$('#dialog-layout').empty().remove();
					},1000);
				});
		}
		var html=$('<div />');
 			html.addClass('dialog-html');
			html.appendTo(content);
			html.html(options.html);
			content.append(html);
			layout.fadeIn('fast',function(){
				layout.addClass('dialog-layout-animate');
			});
			layout.css({height:options.height=='auto'?html.innerHeight():options.height+'px'});
			layout.css({top:(($(window).height()-(options.height=='auto'?html.innerHeight():options.height))/2+$(document).scrollTop())+'px'});
			options.callback();
		if(options.drag){
			var isTouch=('ontouchstart' in window);
			var dragging = false,iX, iY,oX,oY,clone;
			var ww=$(window).width()-options.width;
			var wh=$(window).height()-html.innerHeight();
			$(window).resize(function(){
				ww=$(window).width()-options.width;
				wh=$(window).height()-html.innerHeight();
			});
			var drag={
				start:function(e){
		            dragging = true;
		            var offset =layout.offset();
					if(isTouch){
						var touch=e.originalEvent.changedTouches[0];
						$("#test").html(offset.left);
			            iX = touch.pageX - offset.left;
			            iY = touch.pageY - offset.top;
					}else{
			            var e = e || window.event;
			            iX = e.pageX - offset.left;
			            iY = e.pageY - offset.top;
					}
		            title.css("cursor","move");
					clone=$('<div />');
					clone.css({
						position:'absolute',
						top:offset.top+'px',
						left:offset.left+'px',
						width:options.width+'px',
						height:options.height=='auto'?html.innerHeight():options.height+'px',
						border:'1px dotted #ddd',
						opacity:0.8,
						background:'#efefef',
						zIndex:9999999999999
					});

					$(document.body).append(clone);	
		            return false;
				},
				move:function(e){
		            if (dragging) {
		            	if(isTouch){
		            		var touch=e.originalEvent.changedTouches[0];
				            oX = touch.pageX - iX;
				            oY = touch.pageY - iY;	
		            	}else{
		            		var e=e = e || window.event;
				            oX = e.pageX - iX;
				            oY = e.pageY - iY;	
		            	}
		            	if(oX<0)oX=0;
		            	if(oY<0)oY=0;
		            	if(oX>ww)oX=ww;
		            	if(oY>wh)oY=wh;
			            clone.css({"left":oX + "px", "top":oY + "px","cursor":"move"});
			            return false;
		            }
				},
				end:function(e){

		            dragging = false;
		            layout.css({"left":oX + "px", "top":oY + "px"});
		            clone.remove();
		            title.css("cursor","default");
				}
			};
			if(isTouch){
		        title.on("touchstart",drag.start);
		        $(window).on("touchmove",drag.move);
		        $(window).on("touchend",drag.end);
			}else{
		        title.on("mousedown",drag.start);
		        $(document).on("mousemove",drag.move);
		        $(document).on("mouseup",drag.end);
			}
    	}

	};
	$.dialogClose=function(){
		$('#dialog-layout').fadeOut('slow',function(){
			$('#dialog-mask').remove();
			$(this).removeClass('dialog-layout-animate').empty().remove();
		});	
	}
	return this;
})(jQuery);
