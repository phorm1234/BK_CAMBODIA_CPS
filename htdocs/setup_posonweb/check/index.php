<?php 
	session_start(); 
	date_default_timezone_set("Asia/Bangkok");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Setup New Post</title>
	<!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/custom.css" rel="stylesheet">
	
	<link href="../css/icon.css" rel="stylesheet">
	<link href="../css/jquery-ui.css" rel="stylesheet" >
</head>

<body>
<?php 
	if(empty($_SESSION["login"]))
	{
		echo "<script>window.location = 'login.php';</script>";		
	}
	require_once("../connect.php");
	$ddate = date("d/m/Y");
	$dtime = date("H:i:s");
	
?>
<div class="container-narrow">
<?php 
	require_once("../header.php");
?>
<center><h3>Check Table</h3></center>
<div class="row marketing">	 
	<?php 
		$sql = "SHOW TABLES FROM pos_ssup";
		$result = mysql_query($sql,$condb2);
		echo "Total : ".mysql_num_rows($result)."<br/>";
		echo "<table class='table table-striped table-bordered'>";
		$count = 1;
		while($row = mysql_fetch_array($result))
		{
			if($count/4==1)
			{
				echo "<tr>";
			}
			echo "<td><p onclick='get_column(\"".$row[0]."\")' class='btn-link'>".$row[0]."</p></td>";
			if($count%3==0)
			{
				echo "</tr>";
			}
			$count++;
		}
		echo "</table>";
	?>
</div>
</body>
</html>
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="../js/custom.js"></script>
<script>
	function get_column(table_name)
	{
		if(table_name != "0")
		{
			$(".marketing").load("get_column.php?table_name="+table_name);
		}
		else
		{
			window.location = window.location;
		}
	}
</script>