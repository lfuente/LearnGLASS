<?php
session_start();
include("../config.php");

if(isset ($_SESSION['s_username']))
{
	//Main includes
	include($CFG->dir."lang/lang.php");
	include($CFG->dir."lib/mainlib.php");

	$username = $_SESSION['s_username'];
	$config = $_POST['conf'];
	$module = $_POST['module'];
	$CAMid = $_POST['CAMid'];
	$name = $_POST['name'];
	$des = $_POST['des'];
	$id = get_user_id($CFG,$username);


	//DDBB process
	$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
	or die("error: ".mysql_error());
	mysql_select_db($CFG->dbname)
	or die("error: ".mysql_error());

	//Search matchs
	$query = "SELECT * FROM ".$CFG->prefix."myview
	WHERE name='".$name."' AND userid='".$id."'";
	$result = mysql_query($query)
	or die("error 0: ".mysql_error());

	//if there isn't match the we can insert into database
	if(mysql_num_rows($result)==0)
	{
		//To place in the list: search the last element
		$query = "SELECT * FROM ".$CFG->prefix."myview WHERE userid='".$id."'";
		$result = mysql_query($query)
		or die("error 1: ".mysql_error());
		$pos = mysql_num_rows($result)+1;

		//search the module id for the especified folder
		$query2 = "SELECT *
		FROM ".$CFG->prefix."modules WHERE folder='$module'";
		$query2 = mysql_query($query2)
		or die("error 2: ".mysql_error());
		$data2 = mysql_fetch_array($query2)
		or die("error 3: ".mysql_error());

		//Insert the data in the database
		$query = "INSERT INTO ".$CFG->prefix."myview
		(name,userId,moduleId,bdCAMid,pos,widgetconf,description) VALUES('".$name."','".$id."','".$data2['id']."','".$CAMid."','".$pos."','".$config."','".$des."')";
		$query = mysql_query($query)
		or die(mysql_error());
	}
	else
	{
		//error message
		echo "error: "._DDBB_EXIST;
		exit();
	}

	//close the database connection
	mysql_close($conexion);
	echo _MYVIEW_ADDED;
}
else
{
	header("Location: ".$CFG->url."index.html");
}
?>
