<?php
//Code necesary to print the widget.
//The dimmension must be 400x300
//Can be used:
//$CFG 		-> class var of the global configuration. See conf.php to see the values
//$userid	-> id of the conected user
//$username	-> name of the conected user
//$conf		-> string with the configuration of the module (the format depends of the visualizations)
//$dbid 	-> id of the widget in glass database. It is the id of the dashboard table.
//$CAMdb 	-> id of the events database used

//you should use a div which prints the contents of the widget whose ID that consists of a name
//plus the identifier of the widget in the database (provided as a variable $dbid) to be unique.
//Example: container_widgetId
//The function names should be unique to the module, so it is recommended to use the function name
//to choose preceded by the name of the module which is unique
 
 
 
//check session name
if(isset ($_SESSION['s_username'])){
	//Widget HTML+Javascript
	include("widget.html");
	//this is a container example
	echo '<div id="container'.$dbid.' style="height:300px;width:400px;"></div>';

}
//redirecto to login page to login
else{
	header("Location: ".$CFG->url."index.html");
}
?>