<?php
session_start();

if(isset ($_SESSION['s_username']))
{
	//Main includes
	include("../config.php");
	include("../Lang/lang.php");
	include("../lib/mainlib.php");

	echo "<H1 class='sectiontitle'>"._USER_MANAGER."</H1>";

	//get the username & his permisions
	$username = $_SESSION['s_username'];
	$my_permision = new permision($username);

	if($my_permision->get_userViewLevel()==4)
	{
		include ("users.html");
	}
	echo "<BR><BR>";
	if($my_permision->get_userModifyPermision()==1)
	{
		include("permits.html");
	}
	 
	 
}
else
{
	header("Location: index.html");
}

?>