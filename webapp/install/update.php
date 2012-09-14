<?php

	session_start();

    include_once("../config.php");

    if(isset ($_SESSION['s_username']))
    {
        include_once($CFG->dir."lib/mainlib.php");
        $username = $_SESSION['s_username'];
        $userid = get_user_id($CFG,$username);
        $col = $_POST["col"];
        
        // DB connect
		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die("error: ".mysql_error());
		mysql_select_db($CFG->dbname) 
			or die("error: ".mysql_error());
        $query = "UPDATE ".$CFG->prefix."settings 
            SET dbcol='".$col."' WHERE userId='".$userid."'" ;
        $result = mysql_query($query) 
			or die("error: ".mysql_error());
        mysql_close($conexion);
	}
	else
	{
		header("Location: ".$CFG->url."index.html");
	} 



?>