<?php
$contents = array(
				 array('text'=>'Stock','icon'=>'icon_32_network.png','link'=>'/stock/checkstock/configshelf','isIframe'=>'true'),
	             array('text'=>'computer','icon'=>'icon_32_computer.png','link'=>'http://www.google.com','isIframe'=>'true'),	             
				 array('text'=>'Cashier','icon'=>'icon_32_disc.png','link'=>'/sales/demo','isIframe'=>'true')
			);
echo json_encode($contents);


?>