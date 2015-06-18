<?php
	
	require ('../api/config.php');
	$connection = mysql_connect($mysql_host,$mysql_user,$mysql_pass) or die ("Wrong mysql_configuration!");
	mysql_select_db($mysql_db) or die ("Wrong Database is selected or no permission!");
	
	$sqlquery = "select * from watchme;";
	$result = mysql_query($sqlquery);
	$length = mysql_num_rows($result);
	$counter = 0;
	echo "{";
	while($row = mysql_fetch_object($result))
	{
		$counter++;
		echo "\"".$row->uid."\":{ \"uid\":\"".$row->uid."\", \"user_id\": \"".$row->user_id."\", \"name\": \"".$row->name."\"}".(($counter ==  $length) ? "":",")."";
	}
	echo "}";
	mysql_close();
	
?>



