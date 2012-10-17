<?php
session_start();
if(isset($_SESSION['s_username'])){
	//Main includes
	include("./config.php");
	unset($_SESSION['s_username']);
	session_destroy();
	header("Location: index.html");
}
else{
	header("Location: index.html");
}
?>