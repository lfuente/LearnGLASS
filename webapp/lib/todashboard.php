<?php
	session_start();
    include("../config.php");

    if(isset ($_SESSION['s_username']))
    {
        //Main includes
        include($CFG->dir."lang/lang.php");
        include($CFG->dir."lib/mainlib.php");
        
        $username = $_SESSION['s_username'];
        $config = $_POST['conf'];
        $module = $_POST['module'];
        $CAMid = $_POST['CAMid'];
        $id = get_user_id($CFG,$username);
        
        
        //DDBB process
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die("error: ".mysql_error());
		mysql_select_db($CFG->dbname) 
			or die("error: ".mysql_error());
            
        //fetch the possition of the widget
        $query3 = "SELECT *
			FROM ".$CFG->prefix."dashboard WHERE userId='$id'";
        $query3 = mysql_query($query3) 
				or die("error: ".mysql_error());
        $pos = mysql_num_rows($query3)+1;
              
        //search the module id for the especified folder
        $query2 = "SELECT *
			FROM ".$CFG->prefix."modules WHERE folder='$module'";
        $query2 = mysql_query($query2) 
				or die("error: ".mysql_error());
        $data2 = mysql_fetch_array($query2)
				or die("error: ".mysql_error());   
        //Insert the data in the database
		$query = "INSERT INTO ".$CFG->prefix."dashboard
                  (userId,moduleId,widgetconf,bdCAMid,pos) VALUES('".$id."','".$data2['id']."','".$config."','".$CAMid."','".$pos."')";
		$query = mysql_query($query) 
				or die("error: ".mysql_error());
                
        //close the database connection       
        mysql_close($conexion);
        echo utf8_encode(_DASH_ADDED);
	}
	else
	{
		header("Location: ".$CFG->url."index.html");
	} 
?>