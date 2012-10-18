<?php

session_start();
include("../config.php");

if(isset ($_SESSION['s_username']))
{
	//Main includes
	include_once($CFG->dir."lib/mainlib.php");

	$my_permision = new permision($CFG,$_SESSION['s_username']);
	if($my_permision->get_userModifyPermision()==1)
	{
		$uName = substr($_POST['ref1'],6);
		$value = $_POST['value'];
			
		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die(mysql_error());
		mysql_select_db($CFG->dbname)
		or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."user
		SET userType='$value'
		WHERE name='$uName'";
		mysql_query($query);
		mysql_close($conexion);
	}
}
else
{
	header("Location: ".$CFG->url."index.html");
}


?>