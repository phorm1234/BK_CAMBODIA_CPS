<?php

$comd1=exec("ip addr list eth0 |grep 'inet ' |cut -d' ' -f6|cut -d/ -f1");



echo"$comd1<hr>";

?>
