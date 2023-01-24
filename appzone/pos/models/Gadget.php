<?php 
	class Model_Gadget{
		function pingstatus(){
			return "
				<ul class='ping-status'>
				  <li class='ico-office'></li>
				  <li class='ico-router'></li>
				  <li class='ico-computer'></li>
				  <li class='ico-cam1'></li>
				  <li class='ico-cam2'></li>
				  <li class='ico-edc'></li>
				  <li class='ico-bank'></li>
				</ul>
			";
		}
		function gadgetstime(){
			return "
				<ul id='analog-clock' class='analog'>
				  <li class='hour'></li>
				  <li class='min'></li>
				  <li class='sec'></li>
				  <li class='meridiem'></li>
				</ul>
				<div  class='jclock'></div>
			";
		}
	}
?>