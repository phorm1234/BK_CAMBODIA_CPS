<?php 
	$table_name = $_GET["table_name"];
	require_once("../connect.php");
	$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '$table_name'";
	$result = mysql_query($sql,$condb2);
	echo $table_name." has : ".mysql_num_rows($result)." columns <lebel onclick='get_column(0)' class='btn-link'> Back </lebel>"."<br/>";
	echo "<table class='table table-striped table-bordered'>";
	$count = 1;
	while($row = mysql_fetch_array($result))
	{
		if($count/4==1)
		{
			echo "<tr>";
		}
		echo "<td>".$row[0]."</td>";
		if($count%3==0)
		{
			echo "</tr>";
		}
		$count++;
	}
	echo "</table>";
?>