<?php
	//get WebStatus:  and sid = 2
	//get FBAppStatus: and sid = 3
	//get MessengerStatus: and sid = 4
	//get otherStatus: and sid = 5
	echo '{';
	if(isset($_GET['uid']))
	{
		require('../api/config.php');
		
		$connection = mysql_connect($mysql_host,$mysql_user,$mysql_pass) or die ("Wrong mysql_configuration!");
		mysql_select_db($mysql_db) or die ("Wrong Database is selected or no permission!");
		
		//how often was the user online?
		$sqlquery = "select count(*) as cnt from user_log where uid = '".$_GET['uid']."';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_was_online": "'.$row->cnt.'",';
		
		//how long was the user online?
		$sqlquery = "select sum(end_time-start_time) as gTime from user_log where uid = '".$_GET['uid']."';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_online_time": "'.$row->gTime.'",';
		
		//how often with WebStatus?
		$sqlquery = "select count(*) as cnt from user_log where uid = '".$_GET['uid']."' and sid = '2';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))		
			echo '"cnt_user_webStatus": "'.$row->cnt.'",';
		
		//and this WebStatus in time?
		$sqlquery = "select sum(end_time-start_time) as gTime from user_log where uid = '".$_GET['uid']."' and sid = '2';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_webStatus_time": "'.$row->gTime.'",';
		
		//how often with FBApp?
		$sqlquery = "select count(*) as cnt from user_log where uid = '".$_GET['uid']."' and sid = '3';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_appstatus": "'.$row->cnt.'",';
		
		//and this FBApp in time?
		$sqlquery = "select sum(end_time-start_time) as gTime from user_log where uid = '".$_GET['uid']."' and sid = '3';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_appstatus_time": "'.$row->gTime.'",';
		
		//how often with Messenger?
		$sqlquery = "select count(*) as cnt from user_log where uid = '".$_GET['uid']."' and sid = '4';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_messengerstatus": "'.$row->cnt.'",';
		
		//and this Messenger in time?
		$sqlquery = "select sum(end_time-start_time) as gTime from user_log where uid = '".$_GET['uid']."' and sid = '4';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_messengerstatus_time": "'.$row->gTime.'",';
		
		//how often with other Applications?
		$sqlquery = "select count(*) as cnt from user_log where uid = '".$_GET['uid']."' and sid = '5';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_otherstatus": "'.$row->cnt.'",';
		
		//and this Applications in time?
		$sqlquery = "select sum(end_time-start_time) as gTime from user_log where uid = '".$_GET['uid']."' and sid = '5';";
		$result = mysql_query($sqlquery);
		if($row = mysql_fetch_object($result))
			echo '"cnt_user_otherstatus_time": "'.$row->gTime.'"';
	}
	echo '}';
?>