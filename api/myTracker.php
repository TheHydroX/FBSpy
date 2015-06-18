

    <?php
	//error_reporting(0);
	
	include('config.php');
	
	$connection = mysql_connect($mysql_host,$mysql_user,$mysql_pass) or die ("Wrong mysql_configuration!");
	mysql_select_db($mysql_db) or die ("Wrong Database is selected or no permission!");
	
	ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.0; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
    $cookie = "";
	
    function cURL($url, $header=NULL, $cookie=NULL, $p=NULL)
    {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_NOBODY, $header);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.0; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if ($p) {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
    }
    $result = curl_exec($ch);
    if ($result) {
    return $result;
    } else {
    return curl_error($ch);
    }
    curl_close($ch);
    }
	
	//build connection to facebook and request cookie
    $a = cURL("https://login.facebook.com/login.php?login_attempt=1",true,null,"email=$EMAIL&pass=$PASSWORD");
    preg_match('%Set-Cookie: ([^;]+);%',$a,$b);
    $c = cURL("https://login.facebook.com/login.php?login_attempt=1",true,$b[1],"email=$EMAIL&pass=$PASSWORD");
    preg_match_all('%Set-Cookie: ([^;]+);%',$c,$d);
    for($i=0;$i<count($d[0]);$i++)
    $cookie.=$d[1][$i].";";

	//request standard fbpage
    $myLogin = cURL("http://www.facebook.com/",null,$cookie,null);
    $myID = get_string_between($myLogin,'{"USER_ID":"','"');
	
	//start service
	while(true) {
		run($myID,$cookie);
		sleep(10);
		echo "\n[Debug] [".gmdate('Y-m-d h:i:s \G\M\T', time())."] New Request.\n";
	}
	
	function run($myID,$cookie) {
		$myFriendUIDs = array();
		$myJSON = cURL('https://www.facebook.com/ajax/chat/buddy_list.php?__a=1&user='.$myID, null, $cookie, null);
		$myJSON = str_replace('for (;;);','',$myJSON);
		$myJSON = get_string_between($myJSON, '{"nowAvailableList":', ',"userInfos":{}');
		$myJSONArray = json_decode($myJSON);
		foreach($myJSONArray as $item => $key)
		{
			//parse get-status begin
			$myFriendID = $item;
			$myFriendStatus = $myJSONArray->$item;
			$myStatus = $myFriendStatus->p->status;
			$myWebStatus = $myFriendStatus->p->webStatus;
			$myFBAppStatus = $myFriendStatus->p->fbAppStatus;
			$myMessengerStatus = $myFriendStatus->p->messengerStatus;
			$myOtherStatus = $myFriendStatus->p->otherStatus;
			//end get-status
			
			//echo
			//echo "FriendID:".$myFriendID."\n";
			//echo "status:".$myStatus."\n";
			//echo "websatus:".$myWebStatus."\n";
			//echo "myFBAppStatus:".$myFBAppStatus."\n";
			//echo "myMessengerStatus:".$myMessengerStatus."\n";
			//echo "myOtherStatus:".$myOtherStatus."\n"."\n";
			
			//is User in DB?
			$myUserDBID = -1; //selected user db-id for intern communication
			do 
			{
				$isUserInDBQuery = "SELECT * FROM watchme WHERE user_id = '".$myFriendID."';";
				$result = mysql_query($isUserInDBQuery);
				
				if(mysql_num_rows($result) == 0) {
					//User is not in DB, attach him with UserDisplayName
					//fetch username
					
					$UserDisplayName = cURL("http://www.facebook.com/".$myFriendID, null, $cookie, null);
					$UserDisplayName = get_string_between($UserDisplayName, '"pageTitle">','</title>');
					
					echo "[INFO] User ".$UserDisplayName." was added to the database.\n";
					
					$myUserInsertSQL = "INSERT INTO watchme(user_id, name) VALUES ('".$myFriendID."','".$UserDisplayName."');";
					mysql_query($myUserInsertSQL);
				}
				else {
					$row = mysql_fetch_object($result);
					$myUserDBID = $row->uid;
				}
			}while( $myUserDBID == -1);
			
			array_push($myFriendUIDs,$myUserDBID);
			
			//is given user already in user-active?
			$isUserInListQuery = "SELECT * FROM user_active WHERE uid = '".$myUserDBID."';";
			$result = mysql_query($isUserInListQuery);
			
			//echo "[DEUBG] ".$isUserInListQuery. " COUNT: ".mysql_num_rows($result);
			
			//is user active and not in DB?
			//if(($myStatus == "active" || $myStatus == "idle" ) && mysql_num_rows($result) == 0)
			if(($myStatus == "active") && mysql_num_rows($result) == 0)
			{
				echo "[INFO] User with intern UID ".$myUserDBID." goes ONLINE.\n";
				
				//get the exact actual status
				$myStatusID = 1;
				//if($myWebStatus == "active" || $myWebStatus == "idle")
				if($myWebStatus == "active")
				{
					$myStatusID = 2;
				}
				//else if($myFBAppStatus == "active" || $myFBAppStatus == "idle")
				else if($myFBAppStatus == "active")
				{
					$myStatusID = 3;
				}
				//else if($myMessengerStatus == "active" || $myMessengerStatus == "idle")
				else if($myMessengerStatus == "active")
				{
					$myStatusID = 4;
				}
				//else if($myOtherStatus == "active" || $myOtherStatus == "idle")
				else if($myOtherStatus == "active")
				{
					$myStatusID = 5;
				}
				
				//build insert query to user_active
				$insertQuery = "INSERT INTO user_active(sid,uid,start, start_time) VALUES(".$myStatusID.",".$myUserDBID.",now(), time(now()));";
				mysql_query($insertQuery);
			}
		}
		//clean-up old entrys which are not more active
		$getAllActiveIDs = "SELECT * FROM user_active";
		for($i = 0; $i < count($myFriendUIDs); $i++)
		{
			if($i == 0)
			{
				$getAllActiveIDs = $getAllActiveIDs . " WHERE uid != ".$myFriendUIDs[$i];
			}
			else {
				$getAllActiveIDs = $getAllActiveIDs . " AND uid != ".$myFriendUIDs[$i];
			}
		}
		$getAllActiveIDs = $getAllActiveIDs . ";";		
		$result = mysql_query($getAllActiveIDs);
		if(mysql_num_rows($result) != 0) {
			while($row = mysql_fetch_object($result))
			{
				$sid = $row->sid;
				$uid = $row->uid;
				$start = $row->start;
				$start_time = $row->start_time;
				//insert to user_log
				$myInsertQuery = "INSERT INTO user_log(start,end,sid,uid,start_time,end_time) VALUES ('".$start."',now(),'".$sid."','".$uid."', '".$start_time."', time(now()));";
				mysql_query($myInsertQuery);
				//del user_active-row
				$myDelQuery = "DELETE FROM user_active WHERE uid = ".$uid.";";
				
				echo "[INFO] User with intern UID ".$uid." goes OFFLINE\n";
				
				mysql_query($myDelQuery);
			}
		}
	}
	
	function get_string_between($string, $start, $end){
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}
	
	?>

