<?php
    session_start();
	//configure settings
     
    if(isset ($_SESSION['s_username']))
    {
        include_once("v1lib.php");
        
        $conf = json_decode($conf);                             
        $jsondata = get_visualization1_Json_data($CFG,$userid,$conf->view,$conf->group,$conf->key,$conf->value,$conf->mMax,$conf->mMin,$dbid,$CAMdb);
        include("widget.html");
        echo '<div id="container'.$dbid.'"></div>';
        echo '<div id="error'.$dbid.'"></div>';
        echo "<script>show_widget('".$jsondata."');</script>";
    }
	else
	{
		header("Location: ".$CFG->url."index.html");
	}
?>