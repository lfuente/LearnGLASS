<?php
session_start();

include_once("./config.php");

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
	/////////////CREDITS PAGE/////////////////

	echo '<div class="carpet">';
	echo '<div class="carpet-style">';
	echo "<p class='glass-title'>"._ABOUT_GRADIENT_TITLE."</p>";
	echo "<p class='glass-body'>"._ABOUT_GRADIENT_BODY."</p>";
	echo "</div>";
	echo '<div class="carpet-style">';
	echo "<p class='glass-title'>"._ABOUT_GRADIENT_RESEARCH_TITLE."</p>";
	echo "<p class='glass-body'>"._ABOUT_GRADIENT_RESEARCH_BODY_1."</p>";
	echo "<p class='glass-body'>"._ABOUT_GRADIENT_RESEARCH_BODY_2."</p>";
	echo "</div>";
	echo "</div>";


	 
	/////////////CREDITS END/////////////////
	//////////////////////////////////////////

	echo "</div></div></body></html>";
}
else
{
	$goto = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location: ".$CFG->url."index.html?goto=".$goto);
}
?>