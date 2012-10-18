<?php

session_start();
//configure settings

include_once("./../config.php");

if(isset ($_SESSION['s_username']))
{
	include_once($CFG->dir."lib/mainlib.php");

	$username = $_SESSION['s_username'];
	$userid = get_user_id($CFG,$username);
	$dbid = $_GET['widgetId'];


	if($dbid!=null)
	{
		//DDBB manager
		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die(mysql_error());
		mysql_select_db($CFG->dbname)
		or die(mysql_error());


		//Get the position on the dashboard
		$query = "SELECT pos FROM ".$CFG->prefix."dashboard
		WHERE id ='".$dbid."'
		AND userId='".$userid."'";
		$result = mysql_query($query)
		or die(mysql_error());
		$data = mysql_fetch_array($result);
		$pos = $data["pos"];

		//Get the number of user widgets
		$query = "SELECT * FROM ".$CFG->prefix."dashboard
		WHERE userId='".$userid."'";
		$result = mysql_query($query)
		or die(mysql_error());
		$view_user_number = mysql_num_rows($result);

		//Update the position of the widget
		for($i=$pos;$i<=$view_user_number;$i++){
			$query = "UPDATE ".$CFG->prefix."dashboard
			SET pos='".($i)."'
			WHERE pos='".($i+1)."'
			AND userId='".$userid."'";
			$result = mysql_query($query) or die(mysql_error());
		}

		//Delete the widget
		$query = "DELETE FROM ".$CFG->prefix."dashboard
		WHERE id ='".$dbid."'
		AND userId='".$userid."'";
			
		$query = mysql_query($query) or die(mysql_error());

		mysql_close($conexion);

		header("Location: ".$CFG->url."home.php");
	}
	else
	{
		echo "No element to drop";
	}
}
else
{
	header("Location: ".$CFG->url."index.html");
}

?>