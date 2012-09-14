<?php
	//configure settings
    include_once("../../config.php");
    include_once($CFG->dir."lib/mainlib.php");
    
    $fview_id = $_GET["fid"];
    include_once("v1lib.php");
       
    //Open database conection
    $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die(mysql_error());
	mysql_select_db($CFG->dbname) 
		or die(mysql_error());
   
    $query = "SELECT *
		FROM ".$CFG->prefix."myview 
        WHERE id='".$fview_id."'";
    $result = mysql_query($query) 
			or die(mysql_error());
    
    $data = mysql_fetch_array($result);

    mysql_close($conection);
        	
    $conf = json_decode($data["widgetconf"]);
    $userId = $data["userid"]; 
    $dbid = 0; //it is not needed because only tere is a widget, any value can set
    $CAMdb = $data["bdCAMid"];                 
    $jsondata = get_visualization1_Json_data($CFG,$userId,$conf->view,$conf->group,$conf->key,$conf->value,$conf->mMax,$conf->mMin,$dbid,$CAMdb);

    include("widget.html");
    echo '<div id="container'.$dbid.'"></div>';
    echo '<div id="error'.$dbid.'"></div>';
    echo "<script>show_widget('".$jsondata."');</script>";

?>