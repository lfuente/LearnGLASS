<?php
session_start();
//configure settings

if(isset ($_SESSION['s_username']))
{
	//Code necesary to print the widget.
	//The dimmension must be 400x300
	//Can be used:
	//$CFG 		-> class var of the global configuration. See conf.php to see the values
	//$userid	-> id of the conected user
	//$username	-> name of the conected user
	//$conf		-> string with the configuration of the module (the format depends of the visualizations)
	//$dbid 	-> id of the widget in glass database. It is the id of the dashboard table.
	//$CAMdb 	-> id of the CAM database used





}
else
{
	header("Location: ".$CFG->url."index.html");
}
?>