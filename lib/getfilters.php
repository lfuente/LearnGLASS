<?php
    
   	session_start();
    include("../config.php");
    ini_set("memory_limit","1024M");
    
    if(isset ($_SESSION['s_username']))
    {
        //Main includes
        include($CFG->dir."lib/mainlib.php");
        
        $filter_type = $_POST["filter_type"];
        $mongoID = $_POST["CAMdb"];
        
        $jdata = array();
        
        $conexion = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die(mysql_error());
    	mysql_select_db($CFG->dbname)or die(mysql_error());
        //get the dashboard values 
    	$query = "SELECT * FROM ".$CFG->prefix."ddbb WHERE id='$mongoID'";
    	$result = mysql_query($query)or die(mysql_error());
    	$data = mysql_fetch_array($result)or die(mysql_error());
        mysql_close($conexion);
        
        $objDB = MongoConnect($data["user"],$data["pass"],$data["name"],$data["host"]);        
        //Role Filter
        if($filter_type=="role"){
            $cursor = $objDB->filters->find(array("group"=>"role"));         
        }
        //Event Type Filter
		else if($filter_type=="event_type"){
            $cursor = $objDB->filters->find(array("group"=>"event_type")); 
        }
        
        foreach ($cursor as $c){
            $jdata[count($jdata)] = array("f_name"=>$c["name"],"f_des"=>$c["des"],"f_values"=>$c["values"]);         
        } 
               
        echo json_encode($jdata);    
  
    }
	else
	{
		header("Location: ".$CFG->url."index.html");
	} 




?>