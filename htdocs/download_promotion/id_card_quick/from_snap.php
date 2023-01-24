    <script type="text/javascript">  
    function popup(url,name,windowWidth,windowHeight){      
        myleft=(screen.width)?(screen.width-windowWidth)/2:100;   
        mytop=(screen.height)?(screen.height-windowHeight)/2:100;     
        properties = "width="+windowWidth+",height="+windowHeight;  
        properties +=",scrollbars=yes, top="+mytop+",left="+myleft;     
        window.open(url,name,properties);  
    }  
    </script>  
<table border='0' align='center'>
			  <tr>
				<td width="568" ><label>
				  <div align="center" style="color:#FF0000; font-size:20px;">
				    <p>

				      <input type="button" name="Button" value="  Photograph  "  onclick="TINY.box.hide();var num_snap=document.getElementById('num_snap').value; num_snap=parseInt(num_snap)+1; document.getElementById('num_snap').value=num_snap; javascript:popup('http://'+document.getElementById('ip_this').value+'/download_promotion/id_card_quick/webcam_save.php?id_card='+document.getElementById('id_card').value+'&num_snap='+num_snap+'&ip_this='+document.getElementById('ip_this').value,'',100,100);document.getElementById('status_photo').value='Y';view_phto(); document.getElementById('show_noid_type').style.display = 'block';" 
						
					  /> 
				    </p>
				    <p>* If the camera does not work, Leave the cash bill and enter again.
				    </p>
				  </div>
				</label></td>
			  </tr>
			  <tr>
			    <td align="center" >
			    <div id='show_from_photo' align="center" >
<iframe src='http://127.0.0.1:8081' align='middle' id='from_iframe' marginwidth='0' marginheight='0'  align="center" width='500px' height='400px' frameborder='0'></iframe>
				</div>
			    
			   </td>
  </tr>
</table>


