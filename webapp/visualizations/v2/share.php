<?php
	//configure settings to have the configuration class vars
    include_once("../../config.php");    
    
	//this var has the id of the view to print
    $fview_id = $_GET["fid"];
	
    //Open database conection
    $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die(mysql_error());
	mysql_select_db($CFG->dbname) 
		or die(mysql_error());
	//Select my view with this id
    $query = "SELECT *
		FROM ".$CFG->prefix."myview 
        WHERE id='".$fview_id."'";
    $result = mysql_query($query) 
			or die(mysql_error());
    //the result is saved in $data var
    $data = mysql_fetch_array($result);
    mysql_close($conection);
	
	//Code necesary to print the visualization as a iframe and in my favorite views preview
    

?>