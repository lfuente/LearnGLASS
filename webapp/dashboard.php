<?php
session_start();


//configure settings
define('_COL','2');


if(isset ($_SESSION['s_username']))
{
	//Main includes
	include("./config.php");
	include("./lang/lang.php");
	include("./lib/mainlib.php");
	echo "<LINK href='./themes/classic/style.css' rel='stylesheet' type='text/css'>";
	echo "<LINK href='./themes/classic/table.css' rel='stylesheet' type='text/css'>";
	echo "<meta http-equiv='content-Type' content='text/html; charset=UTF8'/>";


	// DB connect
	$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
	or die(mysql_error());
	mysql_select_db($CFG->dbname)
	or die(mysql_error());



	//get the username
	$username = $_SESSION['s_username'];

	$query = "SELECT *
	FROM ".$CFG->prefix."dashboard D, ".$CFG->prefix."user U
	WHERE U.userId=D.userId AND U.name='$username'";

	$query = mysql_query($query)
	or die(mysql_error());
	$data = mysql_fetch_array($query)
	or die(mysql_error());



	//Title
	echo "<H1 class='sectiontitle'>"._DASHBOARD."</h1>";

	//Widget generation
	$col=0;
	echo "<TABLE><TR>";
	do {
		$linkpage= $CFG->wwwroot."/visualizations/".$data['widgetconf'];
		if($col<_COL)
		{
			echo "<td>";

			include("widgettable.html");

			echo "</td>";
			$col++;
		}
		else
		{
			echo "</TR><TR>";
			echo "<td>";

			include("widgettable.html");

			echo "</td>";

			$col=1;
		}
			

	} while ($data = mysql_fetch_array($query));
	echo "</TR></TABLE>";


	mysql_close($conexion);


}
else
{
	header("Location: ".$CFG->url."index.html");
}

?>