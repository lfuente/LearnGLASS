<?php
session_start();
include("../config.php");

if(isset ($_SESSION['s_username']))
{
	//Main includes
	include($CFG->dir."lang/lang.php");
	include($CFG->dir."lib/mainlib.php");

	$config = $_POST['conf'];
	$fviewId = $_POST['dashId'];

	//DDBB process
	$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
	or die("error: ".mysql_error());
	mysql_select_db($CFG->dbname)
	or die("error: ".mysql_error());


	//Update de dashboard config
	$query = "UPDATE ".$CFG->prefix."myview
	SET widgetconf='".$config."'
	WHERE id=".$fviewId.";";
	$query = mysql_query($query)
	or die("error: ".mysql_error());

	//close the database connection
	mysql_close($conexion);
	echo utf8_encode(_FVIEW_UPDATED);
}
else
{
	header("Location: ".$CFG->url."index.html");
}
?>
