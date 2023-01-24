// Namespace - Module Pattern.
//
var JQD = (function($,window,document,undefined){
  // Expose innards of JQD.
  return {
    go: function() {
      for (var i in JQD.init) {
        JQD.init[i]();
      }
    },
    init: {
      frame_breaker: function() {
        if (window.location !== window.top.location){
          window.top.location = window.location;
        }
      },
      //
      // Initialize the clock.
      //
      clock: function() {
        if (!$('#clock').length) {
          return;
        }

        // Date variables.
        var date_obj = new Date();
        var hour = date_obj.getHours();
        var minute = date_obj.getMinutes();
        var day = date_obj.getDate();
        var year = date_obj.getFullYear();
        var suffix = 'AM';

        // Array for weekday.
        var weekday = [
					          'Sunday',
					          'Monday',
					          'Tuesday',
					          'Wednesday',
					          'Thursday',
					          'Friday',
					          'Saturday'];

        // Array for month.
        var month = [
				          'January',
				          'February',
				          'March',
				          'April',
				          'May',
				          'June',
				          'July',
				          'August',
				          'September',
				          'October',
				          'November',
				          'December'];

        // Assign weekday, month, date, year.
        weekday = weekday[date_obj.getDay()];
        month = month[date_obj.getMonth()];

        // AM or PM?
        if (hour >= 12) {
          suffix = 'PM';
        }

        // Convert to 12-hour.
        if (hour > 12) {
          hour = hour - 12;
        }
        else if (hour === 0) {
          // Display 12:XX instead of 0:XX.
          hour = 12;
        }

        // Leading zero, if needed.
        if (minute < 10) {
          minute = '0' + minute;
        }

        // Build two HTML strings.
        var clock_time = weekday + ' ' + hour + ':' + minute + ' ' + suffix;
        var clock_date = month + ' ' + day + ', ' + year;
        // Shove in the HTML.
        $('#clock').html(clock_time).attr('title', clock_date);
        // Update every 60 seconds.
        setTimeout(JQD.init.clock, 60000);
      },
	  //Create th gedget
	  gedget : function(){
    	 // return false;
		  var max = 1;//2
		  for(var i=0; i<max; i++) {
	  		if(!$('#gadget').length) $( '<div/>' ).attr({id:'gadget'}).insertAfter( $('#program_info'));			
			$('<div/>').attr({class:'draggable gedget'}).append( 
			$('<span/>').attr({class:'float_right'}).append( 
			$('<a/>').attr({href:'#gedget'+i,class:'gedget_close'}) ) ).appendTo($('#gadget'));			
			// Innit Loading
			
			var jloading = $("<p class='loading'></p>").insertAfter('a:[href="#gedget'+i+'"]');
			var jobj = $('<span/>')
				.attr({ id:'getged'+i })
				.css({ margin:'5px 40px 0',position:'relative',border:'none',float:'left'})
				.insertAfter('a:[href="#gedget'+i+'"]')
				
				.load('/pos/extra/gadgets',{gedget:i},function(data){  
				//.load('/pos/theme/assets/gadgets.php',{gedget:i},function(data){  
					//alert(data+'222') ;					
					$('.loading').remove();
					$(this).html(data);
                    $('#analog-clock').parents().parents().parents().addClass('tranparent')
                    $('#analog-clock').clock({offset: '+7', type: 'analog'}); 
                    
					//$('#analog-clock').parents().parents().parents().addClass('tranparent');
					//alert( $('#analog-clock').parents().parents().parents().attr('class'));
					
				   var options = {
						        am_pm: true,
						        fontFamily: 'Verdana, Times New Roman',
						        fontSize: '18px',						        
						        border:'1px solid #111',						        
						        padding:0
					      }; 
					$('.jclock').jclock(options).css({
						'margin-left':'150px',
						width:'130px',
						border:'0px solid #111'});
					$('.loading').remove(); 		
					
				});
				
		  }
  	  },
	  deskCreate: function() {
		  var y = 20,x = 20, winheight = $("#wrapper").height()-200;
		  //alert(winheight)
		  // $.getJSON('/pos/theme/assets/getContents.php',{},function(data){
		  
		  $.getJSON('/pos/index/desktop',{},function(data){
			  $.each(data,function(i,val){
				 
				  y = i==0?y:y+100;
				  if(y>winheight){
					  y=20;
					  x=x+100;
				  }
				  //Prepair desktop
				  if(!$("#desktop").length) $('<div/>')
				  .attr({id:"desktop"})
				  .appendTo( $('#wrapper'));
				  //Create Desktop Icon	
				  var display = $("#desktop"),
				  	  icon_pic = $('<img/>').attr({
						  src:val.icon
					  }),
				      icon_text = val.text,
					  icon = $('<a/>').attr({ 
					      class:"abs icon",
						  style:"left:"+x+"px;top:"+y+"px;",
						  href:"#icon_dock_"+val.menu_id
					   }).append(icon_pic,icon_text).appendTo(display);
					   
				  //Create Desktop Window	
				  var windows = $('<div/>').attr({ 
				  			id:"window_"+val.menu_id,
							class:"abs window",
							page:val.link 
				     }),
					 window_inner = $('<div/>').attr({class:"abs window_inner"}),
					 window_top = $('<div/>').attr({class:"window_top"}).append(
					 			$('<span/>').attr({class:"float_left"})
							   .append( $('<img/>')
							   .attr({src:val.icon,height:'16'}),
								val.text 
							),
							$('<span/>').attr({class:"float_right"}).append(
								$('<a/>').attr({href:"#",class:"window_min"}),
								$('<a/>').attr({href:"#",class:"window_resize"}),
								$('<a/>').attr({href:"#icon_dock_"+val.menu_id,class:"window_close"})
							)
					),
					window_content = $('<div/>').attr({class:"abs window_content"}),
					htmlIframe = $('<iframe/>').attr({ 
									name:"window_main_ifram_"+val.menu_id,
									id:"window_main_ifram_"+val.menu_id,
									scrolling:"auto",
									frameborder:"0" 
								}).addClass("window_main").css({
									left:"0",right:"0",padding:"0",margin:"0", bottom:"0"
								}),
					window_main = $('<div/>').attr({class:"window_main"}),
					window_bottom = $('<div/>').attr({class:"abs window_bottom"}),
					resize_span = $('<span/>').attr({class:"abs ui-resizable-handle ui-resizable-se"});
					//alert(val.isIframe+'-->'+val.link)
					if(val.isIframe=='true'){ window_main.append( htmlIframe ) }
					ele_display = val.isIframe=='true'?htmlIframe:window_main
					window_content.append(ele_display);
					window_inner.append(window_top,window_content,window_bottom,resize_span)
					window_inner.appendTo(windows)
					
					display.append(windows)
					
				  //Create Task Icon			  
				  var dock = $('<li/>').attr({id:"icon_dock_"+val.menu_id});
				  var txtdock = $('<a/>').attr({ href:"#window_"+val.menu_id,title:val.text});	  
				  txtdock.append($('<img/>').attr({src:val.icon,width:'22'}));
				  dock.append(txtdock).appendTo( $('#dock'));
			  })
		  });
		  //}
	  },
      //
      // Initialize the desktop.
      //
      desktop: function() {
        // Cancel mousedown, right-click.
        $(document).mousedown(function(ev) {
          var tags = ['a', 'button', 'input', 'select', 'textarea'];
			
          if (!$(ev.target).closest(tags).length) {
            JQD.util.clear_active();
            ev.preventDefault();
            ev.stopPropagation();
          }
        }).bind('contextmenu', function() {
          return false;
        });

        // Relative or remote links?
        $('a').live('click', function(ev) {
          var url = $(this).attr('href');
          this.blur();

          if (url.match(/^#/)) {
            ev.preventDefault();
            ev.stopPropagation();
          }
          else {
            $(this).attr('target', '_blank');
          }
        });

        //open new child without icon
		$('.open_chind').live('dblclick',function(){
			var y = $(this).attr('href');
			//alert(y)
			if(!$('#window_child').lenght)
			{
			//Create Desktop Window
				  var display = $("#desktop"),window_panel = $('#panel');
				  var windows = $('<div/>').attr({ 
				  			id:"window_child",
							class:"abs window",
							page:'/pos/index/childicon' 
				     }), 
				     window_inner = $('<div/>').attr({class:"abs window_inner"}),
					 window_top = $('<div/>').attr({class:"window_top"})
					 .append(
					 	$('<span/>').attr({class:"float_left"})
						   .prepend( $('<img/>')
						   .attr({src:"/pos/theme/assets/images/icons/icon_32_recycle.png",height:'16'}),
							'test' 
						),
						$('<span/>').attr({class:"float_right"}).prepend(
							$('<a/>').attr({href:"#",class:"window_min"}),
							$('<a/>').attr({href:"#",class:"window_resize"}),
							$('<a/>').attr({href:"#icon_dock_child",class:"window_close"})
						)					  
					),
					window_content = $('<div/>').attr({class:"abs window_content"})
					                           .prepend( $('<div/>').attr({class:"window_main"}) ),
					window_bottom = $('<div/>').attr({class:"abs window_bottom"})
					
					window_inner.prepend(window_top,window_content,window_bottom)
					window_inner.appendTo(windows)
					$('<span/>').attr({class:"abs ui-resizable-handle ui-resizable-se"}).insertAfter(windows)
					windows.appendTo(window_panel);
					display.append(window_panel)
			}
			JQD.util.window_flat();
			$(y).addClass('window_stack').addClass('window_full').show();
		})
		// Make top menus active.
        $('a.menu_trigger').live('mousedown', function() {
          if ($(this).next('ul.menu').is(':hidden')) {
            JQD.util.clear_active();
            $(this).addClass('active').next('ul.menu').show();
          }
          else {
            JQD.util.clear_active();
          }
        }).live('mouseenter', function() {
          // Transfer focus, if already open.
          if ($('ul.menu').is(':visible')) {
            JQD.util.clear_active();
            $(this).addClass('active').next('ul.menu').show();
          }
        });

        // Desktop icons.
        $('a.icon').live('mousedown', function() {
          // Highlight the icon.
          JQD.util.clear_active();
          $(this).addClass('active');
        }).live('dblclick', function() {
          // Get the link's target.
          var x = $(this).attr('href');
          //alert(x);
          var y = $(x).find('a').attr('href');
		  var f = $(y).find('iframe').attr('id');
		  var p = $(y).attr('page');
		  if(!f){
			  if(p) { 
					$.ajax({
						url : p,
						type : 'post',
						success : function(data){						
							$(y+' div[class="window_main"]').html(data);
						}
					})
			  }
		  }else{
			  if ($(x).is(':hidden')) {
					$.get("/pos/index/numchildnode",{
						y:y,
						ran:Math.random()
						},function(data){
							if(data > 0){
								var menu_id=y.replace("#window_","");
								p='/pos/index/childicon?menu_id='+menu_id;
								var url_ifrm = $(y+' iframe[class="window_main"]').attr('src',p)
							}else{
								var url_ifrm = $(y+' iframe[class="window_main"]').attr('src',p)
							}
							
					});
			  }//
		  }

          // Show the taskbar button.
          if ($(x).is(':hidden')) {
           // $(x).remove().appendTo('#dock');
            $(x).show('fast');
          }

          // Bring window to front.
          JQD.util.window_flat();
          $(y).addClass('window_stack').addClass('window_full').show();
		  //calculate ifram area
		  var div = $(y).children().children().find("div").attr('class');
		  var h = $("."+div).height()-6;
          var ifram = $(y).children().children().find("iframe").attr('class');
		  $("."+ifram).attr({height:h})
		  
        }).live('mouseenter', function() {
          $(this).die('mouseenter').draggable({
            revert: true,
            containment: 'parent'
          });
        });

        // Taskbar buttons.
        $('#dock a').live('click', function() {
          // Get the link's target.
          var x = $($(this).attr('href'));
          // Hide, if visible.
          if (x.is(':visible')) {
            x.hide();
            JQD.util.window_front();//keang append 13122012
          }
          else {
            // Bring window to front.
            JQD.util.window_flat();
            x.show().addClass('window_stack');
          }
        }).live('mouseover',function(){
			$(this).tooltip({
                        position: "top center",
                        offset: [10,0],
                        onShow: function() {							
                                this.getTrigger().fadeTo("2000", 0.8);
                        }                
            })			
		})
        // Make windows movable.
        $('div.window').live('mousedown', function() {
          // Bring window to front.
		  
          JQD.util.window_flat();
          $(this).addClass('window_stack');
        }).live('mouseenter', function() {
          $(this).die('mouseenter').draggable({			  
            // Confine to desktop.
            // Movable via top bar only.
            cancel: 'a',
            containment: 'parent',
            handle: 'div.window_top'
          }).resizable({
            containment: 'parent',
            minWidth: 400,
            minHeight: 200
          });

        // Double-click top bar to resize, ala Windows OS.
        }).find('div.window_top').live('dblclick', function() {
          JQD.util.window_resize(this);

        // Double click top bar icon to close, ala Windows OS.
        }).find('img').live('dblclick', function() {
        	
          // Traverse to the close button, and hide its taskbar button.
          $($(this).closest('div.window_top').find('a.window_close').attr('href')).hide('fast');

          // Close the window itself.
          $(this).closest('div.window').hide();
          // Stop propagation to window's top bar.
          return false;
        });

        // Minimize the window.
        $('a.window_min').live('click', function() {
          $(this).closest('div.window').hide();
          JQD.util.window_front();//keang append 13122012
        });

        // Maximize or restore the window.
        $('a.window_resize').live('click', function() {
          JQD.util.window_resize(this);
        });

        // Close the window.
        $('a.window_close').live('click', function(){
          $(this).closest('div.window').hide();
          $($(this).attr('href')).hide('fast');
          JQD.util.window_front();//keang append 13122012
        });
		$('a.gedget_close').live('click', function() {			
			$(this).closest('div.gedget').fadeOut(1000,0);
          //$($(this).attr('href')).hide('fast');
        });
        // Show desktop button, ala Windows OS.
        $('#show_desktop').live('mousedown', function() {
          // If any windows are visible, hide all.
          if ($('div.window:visible').length) {
            $('div.window').hide();
          }
          else {
            // Otherwise, reveal hidden windows that are open.
            $('#dock li:visible a').each(function() {
              $($(this).attr('href')).show();
            });
          }
        });

        $('table.data').each(function() {
          // Add zebra striping, ala Mac OS X.
          $(this).find('tbody tr:odd').addClass('zebra');
        }).find('tr').live('mousedown', function() {
          // Highlight row, ala Mac OS X.
          $(this).closest('tr').addClass('active');
        });
      },
      wallpaper: function() {
        // Add wallpaper last, to prevent blocking.
        if ($('#desktop').length) {
          $('body').prepend('<img id="wallpaper" class="abs" src="/pos/theme/assets/images/misc/wallpaper5.jpg" />');
        }
      }
    },
    util: {
      //
      // Clear active states, hide menus.
      //
      window_front: function() {
		$('div.window:visible').each(
			function(){
				$(this).addClass('window_stack')
			}
		)
	  },	 
      clear_active: function() {
        $('a.active, tr.active').removeClass('active');
        $('ul.menu').hide();
      },
      //
      // Zero out window z-index.
      //
      window_flat: function() {
        $('div.window').removeClass('window_stack');
      },
      //
      // Resize modal window.
      //
      window_resize: function(el) {
        // Nearest parent window.
        var win = $(el).closest('div.window');

        // Is it maximized already?
        if (win.hasClass('window_full')) {
          // Restore window position.
          win.removeClass('window_full').css({
            'top': win.attr('data-t'),
            'left': win.attr('data-l'),
            'right': win.attr('data-r'),
            'bottom': win.attr('data-b'),
            'width': win.attr('data-w'),
            'height': win.attr('data-h')
          });
        }
        else {
          win.attr({
            // Save window position.
            'data-t': win.css('top'),
            'data-l': win.css('left'),
            'data-r': win.css('right'),
            'data-b': win.css('bottom'),
            'data-w': win.css('width'),
            'data-h': win.css('height')
          }).addClass('window_full').css({
            // Maximize dimensions.
            'top': '0',
            'left': '0',
            'right': '0',
            'bottom': '0',
            'width': '100%',
            'height': '100%'
          });
        }
        // Bring window to front.
        JQD.util.window_flat();
        win.addClass('window_stack');
		
		// calculate ifram area
		var h = $(win).height()-59;
        var ifram = $(win).find("iframe").attr('class');
		$("."+ifram).attr({height:h})
      }
    }
  }
  
// Pass in jQuery.
})(jQuery, this, this.document);

//
// Kick things off.
//
var objRegGt=null;
function reGadGet(){	
	var opts_gt={
				type:'get',
				url:'/pos/extra/regadgets',
				async:true,
				data:{
					gedget:'1'
				},success:function(data){
					objRegGt=null;
					$("#getged1").empty().html(data);  
				}
		};
	  //objRegGt=$.ajax(opts_gt);
}//func

jQuery(document).ready(function(){
	//alert('pwit');
	  JQD.go();
	  $('.draggable').draggable({
		  revert: true,
	      containment: 'parent'
	   });
	  $("[title]").tooltip({
	      position: "top center",
	      offset: [10,0],
	      onShow: function() {							
	              this.getTrigger().fadeTo("2000", 0.8);
	      }                
	  })	
	  
	  ///////////////////// start /////////////////////////		
	  
	 var ping_timer=60*60*1000;
	  myPing = setInterval(function () {	     
		  reGadGet();
	  },ping_timer);
//	  window.setInterval(function(){
//		  reGadGet();
//			},1000);	 
	  ///////////////////// stop /////////////////////////
  
});