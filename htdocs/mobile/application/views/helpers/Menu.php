<?php 
Class Zend_View_Helper_Menu { //ต้องใช่ prefix:Zend_View_Helper_ เสมอ

	public function main_menu($m) {
		if($m == 0){
					$str = '<ul>
	                	<li><a href="index.php">Home</a></li>            
	                	<li ><a href="admin.php">Admin</a></li>	
	                	<li ><a href="regis.php">Register</a></li>
	                	<li ><a href="contact.php">Contact Us</a></li>
	                	<li ><a href="logout.php">Logout</a></li>                    	
	               </ul>';
               }
            	if($m == 1){
					$str = '<ul>
	                	<li class="current"><a href="index.php">Home</a></li>            
	                	<li ><a href="admin.php">Admin</a></li>
	                	<li ><a href="regis.php">Register</a></li>
	                	<li ><a href="contact.php">Contact Us</a></li>
	                	<li ><a href="logout.php">Logout</a></li>                      	
	               </ul>';
               }
               if($m == 2){
               		$str = '<ul>
	                	<li ><a href="index.php">Home</a></li>            
	                	<li class="current"><a href="admin.php">Admin</a></li>
	                	<li ><a href="regis.php">Register</a></li>
	                	<li ><a href="contact.php">Contact Us</a></li>
	                	<li ><a href="logout.php">Logout</a></li>            
	               </ul>';
               }
				if($m == 3){
               		$str = '<ul>
	                	<li ><a href="index.php">Home</a></li>            
	                	<li ><a href="admin.php">Admin</a></li>
	                	<li class="current"><a href="regis.php">Register</a></li>
	                	<li ><a href="contact.php">Contact Us</a></li>
	                	<li ><a href="logout.php">Logout</a></li>            
	               </ul>';
               }
				if($m == 4){
               		$str = '<ul>
	                	<li ><a href="index.php">Home</a></li>            
	                	<li ><a href="admin.php">Admin</a></li>
	                	<li ><a href="regis.php">Register list</a></li>
	                	<li class="current"><a href="contact.php">Contact Us</a></li>
	                	<li ><a href="logout.php">Logout</a></li>            
	               </ul>';
               }
			return $str;
	}
}
