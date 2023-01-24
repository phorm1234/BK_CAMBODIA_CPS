<script type="text/javascript">
	function creatajax() {

			if (window.XMLHttpRequest)
			{ 
					ajaxRequest = new XMLHttpRequest();
			}
			else if(window.ActiveXObject)
			{ 
					 ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); 
			}
			else
			{
					alert("Browser error");
					return false;
			}
	}

function webcam_save()
{
	var c=confirm("Snap ?");
	if(!c){
		return false;
	}
   creatajax();
   var my=document.frmbill_open;
	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState == 4)
		{
						var data= ajaxRequest.responseText;
						if(data=="snap_error"){
                                                  alert("Snap_error");
						  return false;
						}else{
						  alert("Snap OK");
	                                          
						}
						
		}
	}


	
	var ran=Math.random();
	ajaxRequest.open("GET", "webcam_save.php?ran="+ran, true);
	ajaxRequest.send(null); 

}



	</script>
<iframe src='http://127.0.0.1:8081' align='middle' marginwidth='0' marginheight='0' width='500' height='500' frameborder='0'></iframe>
<input type='button' id='btn_snap' name='btn_snap' value='Snap' onclick="webcam_save();">

