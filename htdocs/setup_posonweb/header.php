<?php 
	function curPageURL() {
 		return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	}
?>
	<div class="header">
		<ui class="nav nav-pills pull-right" >
			<li><a href="create_branch.php">Create Shop</a></li>
			<li><a href="#" class="dropdown-toggle btn-group" data-toggle="dropdown">Set User <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="set_user.php">กำหนดรหัสผู้ใช้</a></li>
					<li><a href="set_inout.php">ลงเวลาทำงาน</a></li>
				</ul>
			</li>
			<li><a href="#" class="dropdown-toggle btn-group" data-toggle="dropdown">Config Shop <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="set_shop.php">เวลาเปิด/ปิดร้าน</a></li>
					<li><a href="set_doc_date.php">วันที่เอกสาร</a></li>
					<!--<li><a href="#">ประเภทเอกสาร</a></li>
					<li><a href="#">เลขที่รหัสสมาชิก</a></li>
					<li><a href="#">รายละเอียดระบบ</a></li>-->
					<li><a href="set_comp.php">Set IP</a></li>
					<li><a href="set_conf.php">Set Config</a></li>
				</ul>
			</li>
			<li><a href="logout.php">Logout</a></li>
		</ui>
		<h3 class="text-muted">Setup New Shop</h3>
	</div>
	