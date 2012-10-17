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
		// DB connect
		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die("Imposible to connect to MySQL");
		mysql_select_db($CFG->dbname)
		or die("Imposible to connect to GLASS database");

		$uType = $_GET['uType'];
		$uName = $_GET['uName'];

		if(strcmp($uType,'')!=0)
		{
			$query = "SELECT * FROM ".$CFG->prefix."user U
			WHERE userType='$uType'";
		}
		else if(strcmp($uName,'')!=0)
		{


			$query = "SELECT * FROM ".$CFG->prefix."user U
			WHERE name='$uName'";
		}
		else
		{
			$query = "SELECT * FROM ".$CFG->prefix."user U";
		}

		$query = mysql_query($query)
		or die(mysql_error());
		$data = mysql_fetch_array($query)
		or die(mysql_error());
		mysql_close($conexion);
		$i=0;
		do {
			$jdata[$i] = array("name" => $data['name'], "userType" => $data['userType']);
			$i++;
		} while ($data = mysql_fetch_array($query));
		echo json_encode($jdata);
	}
}
else
{
	header("Location: ".$CFG->url."index.html");
}
?>