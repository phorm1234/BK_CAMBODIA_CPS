function logoutConfirm()
{
	jConfirm('คุณต้องการออกจากโปรแกรมใช่หรือไม่?','ข้อความยืนยัน', function(r){
	        if(r){
	        	$.get("/pos/login/checksentdata",{
	        		ran:Math.random()
	        	},function(data){ 
	        			if(data=='NO'){
	        				var ran=Math.random();
	        				var url="transfer_to_office/index.php?ran="+ran;
	        				var name="transfer_to_office";
	        				var windowWidth="1";
	        				var windowHeight="1";
	        				popup(url,name,windowWidth,windowHeight);
	        			}
	        			
	        	});
	        	window.location = '/pos/logout/index';
				return false;
	        }
	});//jconfirm
}

//var $j = jQuery.noConflict();
var use_debug = false;
//var instanse = false;
var maxid=0;
var countlop=0;
function debug(){
	if( use_debug && window.console && window.console.log ) console.log(arguments);
}



//----------------------------------------------------------------------------------------	
function popup(url,name,windowWidth,windowHeight){    
	myleft=(screen.width)?(screen.width-windowWidth)/2:100;	
	mytop=(screen.height)?(screen.height-windowHeight)/2:100;	
	properties = "width="+windowWidth+",height="+windowHeight;
	properties +=",scrollbars=yes, top="+mytop+",left="+myleft;   
	window.open(url,name,properties);
}

//----------------------------------------------------------------------------------------


$(function (){
	$(".marquee").marquee({
		loop: -1
		, init: function ($marquee, options){
			debug("init", arguments);
		}
		, beforeshow: function ($marquee, $li){
			debug("beforeshow", arguments);
			var $author = $li.find(".author");
			if($author.length ){
				$("#marquee6-author").html("<span style='display:none;'>" + $author.html() + "</span>").find("> span").fadeIn(550);
			}
		}
		, show: function (){
			debug("show", arguments);
		}
		, aftershow: function ($marquee, $li){
			
			countlop=countlop+1;
			if(countlop > maxid){
				
				/*
				setTimeout(function(){
					countlop=0;
					getmaxidnew();
				},3600000)
				*/
			}
			
			debug("aftershow", arguments);
			var $author = $li.find(".author");
			if( $author.length ) $("#marquee6-author").find("> span").fadeOut(550);
		}
	});
	
	
	 $.get("/pos/index/onlinestatus",{ran:Math.random()},function(data){
		if(data==1){
			getmaxidnew();
		}
	 });
	
	
	
});
//=============================================================

function getmaxidnew(){
	
	 $.get("/pos/index/getmaxidnew",{ran:Math.random()},function(data){
			maxid=data;
			getshortnew();
	 });
}


//=============================================================
function getshortnew(){
	$.get("/pos/index/getshortnew",{ran:Math.random()},function(data){
			//alert(data);
			var $ul = $("#marquee6").append(data);
			$ul.marquee("update");
	});	
}
//=============================================================
function showNew(content){
	$.get("/pos/index/shownew",{content:content,ran:Math.random()},function(data){
			jAlert(data, 'ข่าวสั้นวันนี้');
	});	
}
//=============================================================

function pause(){
	$("#marquee6").marquee('pause');
}
//=============================================================
function resume(){
	$("#marquee6").marquee('resume');
}
//=============================================================
function logoutConfirm()
{
	jConfirm('คุณต้องการออกจากโปรแกรมใช่หรือไม่?','ข้อความยืนยัน', function(r){
	        if(r){
	        	window.location = '/pos/logout/index';
				return false;
	        }
	});//jconfirm
}
//=============================================================