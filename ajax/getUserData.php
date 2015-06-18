<?php
	
	require ('../api/config.php');
	if(isset($_GET['uid'])) {
		$connection = mysql_connect($mysql_host,$mysql_user,$mysql_pass) or die ("Wrong mysql_configuration!");
		mysql_select_db($mysql_db) or die ("Wrong Database is selected or no permission!");
		
		$sqlquery = "select * from user_log where uid = '".$_GET['uid']."';";
		$result = mysql_query($sqlquery);
		$length = mysql_num_rows($result);
		$counter = 0;
		echo "{";
		while($row = mysql_fetch_object($result))
		{
			$counter++;
			echo "\"".$row->ulid."\":{ \"start\":\"".$row->start."\", \"end\": \"".$row->end."\", \"start_time\": \"".$row->start_time."\", \"end_time\": \"".$row->end_time."\", \"sid\": \"".$row->sid."\"}".(($counter ==  $length) ? "":",")."";
		}
		echo "}";
		mysql_close();
	}
?>



