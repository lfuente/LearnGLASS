<?php
session_start();
//configure settings
include("../config.php");

if(isset ($_SESSION['s_username']))
{
	//PHP includes
	include_once($CFG->dir."lang/lang.php");
	include_once($CFG->dir."lib/mainlib.php");
	//Username handle
	$username = $_SESSION['s_username'];
	$userid = get_user_id($CFG,$username);
	//HTML structure
	echo "<html>";
	echo "<head>";
	echo "<title>GLASS</title>";
	echo "<LINK href='".$CFG->url."themes/classic/style.css' rel='stylesheet' type='text/css'>";
	echo "<meta http-equiv='content-Type' content='text/html; charset=UTF8'/>";
	echo "<script type='text/javascript' src='".$CFG->url."lib/jslib.js'></script>";
	echo "<link href='http://fonts.googleapis.com/css?family=Orbitron:400,500' rel='stylesheet' type='text/css'>";
	echo "</head>";
	echo "<body>";
	echo '<div id="glass_body"><div id="glass_header">'. _APLICATION_TITLE.'</div>';
	echo "<div id='glass_leftsection'>";
	echo "<div id='glass_logo'></div>";
	show_menu($CFG,$username);
	echo "</div>";
	echo "<div id='glass_rightsection'>";


	//////////////////////////////////////////
	///////////// MODULE MANAGER /////////////

	echo "<p class='sectiontitle'>"._MODULE_MANAGER."</p>";
	$my_permision = new permision($CFG,$username);
	if($my_permision->get_moduleInstall()==1)
	{
		include_once("index.html");
	}

	//////////MODULE MANAGER END//////////////
	//////////////////////////////////////////

	echo "</div></div></body></html>";
}
else
{
	$goto = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location: ".$CFG->url."index.html?goto=".$goto);
}
?>