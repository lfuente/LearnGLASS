<?php
	session_start();
	include("../../config.php");
	include_once($CFG->dir."lib/mainlib.php");
	include_once($CFG->dir."lang/lang.php");
	include("v1lib.php");
	$token = $_GET["token"];
	$timestamp = $_GET["timestamp"];
	$external_username = $_GET["user"];

			//to debug//////////////////////////////////
	if ($_GET["XDEBUG_SESSION_START"] == "ECLIPSE_DBGP") {
		$timestamp = time();
		$external_username = luis;
		$token = md5($timestamp.$external_username."glasscrif");
	}


	// 1. do we have a session?
	// 2. do we have a correct token?
	if(isset ($_SESSION['s_username']))
	{
		$can_show_page = true; 
	} else if (($token != null) && ($timestamp != null) && ($external_username != null))
	{

		///////////////////////////////////////
		//let's check if the token was created with the valid key
		$valid_token = check_token($token, $timestamp, $external_username, $CFG);
		$in_system = user_in_system($external_username, $CFG);

		if ( !$valid_token[0] ) {
			$msg = $valid_token[1];
		} else if (!$in_system[0]) {
			$msg = $in_system[1];
		} else {
			create_user_session($external_username, $CFG);
			$can_show_page = true;
		}
	} 

	// now we know if the pic can be shown. If not, go to login page 
	if ($can_show_page){
		$username = $_SESSION['s_username'];
		$my_permission = new permision($CFG,$username);

		//TODO: fake conf
		$right_now = strtotime("1 April 2012");
		//$right_now=strtotime("now");
		$one_week_ago = strtotime("-2 week", $right_now);
		//below functions understand the time in milisecons
		$jsrn = $right_now*1000;
		$jsowa = $one_week_ago*1000;

		//TODO: value (now is TICPrimaria) should be taken from mongo user data		
		$cValue = "TICPrimaria";
		if ($my_permission->get_viewUser() == 0)
		{
			//the student view: average view with self-view
			//TODO: $fake_username should be $username
			$fake_username = "00756209b";
			$conf="{\"group\":[\"role\",\"role\"],\"key\":[\"user_name\",\"community\"],\"value\":[\"$fake_username\",\"$cValue\"],\"mMax\":\"$jsrn\",\"mMin\":\"$jsowa\",\"view\":\"1\"}";
		}
		else 
		{
			//the teacher view: average values
			$conf="{\"group\":[\"role\"],\"key\":[\"community\"],\"value\":[\"$cValue\"],\"mMax\":\"$jsrn\",\"mMin\":\"$jsowa\",\"view\":\"1\"}";
		}

		//fake conf
		$userid = $username; //userid is useless
		$dbid = 1; //I think it's useless. But it requires a value. Not sure yet...

		//Open database conection
		$conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname)
			or die(mysql_error());

		//Note that this query starts from the username
		//whether userId or name will depend on the final usermodel
		//userid would be more correct	
		$query = "SELECT s.ddbbId 
				  FROM glass_settings s, glass_user u 
				  WHERE u.name='".$userid."' and u.userId=s.userId";

		$result = mysql_query($query) 
				or die(mysql_error());

		//Id of the mongo database to show. 
		//By default, the ddbb selected by the user.
		$CAMdb = mysql_result($result,0);

		mysql_close($conection);

		$conf = json_decode($conf);
		$jsondata = get_visualization1_Json_data($CFG,$userid,$conf->view,$conf->group,$conf->key,$conf->value,$conf->mMax,$conf->mMin,$dbid,$CAMdb);

		include("pic.html");
		echo '<div id="container'.$dbid.'"></div>';
		echo '<div id="error'.$dbid.'"></div>';
		echo "<script>show_widget('".$jsondata."');</script>";
	}
	else
	{
		$goto = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: ".$CFG->url."index.html?goto=".$goto);
	} 
?>
