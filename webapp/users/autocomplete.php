<?php
session_start();
include("../config.php");

if(isset ($_SESSION['s_username']))
{
	//Main includes
	include_once($CFG->dir."lib/mainlib.php");

	//get the username & his permisions
	$username = $_SESSION['s_username'];
	$my_permision = new permision($CFG,$username);

	if($my_permision->get_userViewLevel()==4)
	{
		$q = make_safe(strtolower($_GET["q"]));
		if (!$q) return;

		// DB connect
		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die("Imposible to connect to MySQL");
		mysql_select_db($CFG->dbname)
		or die("Imposible to connect to GLASS database");
			
		$query = "SELECT name FROM ".$CFG->prefix."user U";

		$query = mysql_query($query)
		or die(mysql_error());
		$data = mysql_fetch_array($query)
		or die(mysql_error());
		mysql_close($conexion);

		do
		{
			$key = $data['name'];
			if (strpos(strtolower($key), $q) !== false)
				echo "$key\n";
		} while ($data = mysql_fetch_array($query));
	}
}
else
{
	header("Location: ".$CFG->url."index.html");
}

?>