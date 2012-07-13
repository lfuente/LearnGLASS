<?php
	session_start();
	//configure settings
    include_once("./config.php");

    if(isset ($_SESSION['s_username']))
    {
        //PHP includes
        include_once($CFG->dir."lang/lang.php");
        include_once($CFG->dir."lib/mainlib.php");
        //Username handle
        $username = $_SESSION['s_username'];
        $userid = get_user_id($CFG,$username);
        //HTML structure
        echo "<html>";
        echo "<head>";
        echo "<title>GLASS</title>";
        echo "<LINK href='".$CFG->url."Themes/clasic/style.css' rel='stylesheet' type='text/css'>";
        echo "<meta http-equiv='content-Type' content='text/html; charset=UTF8'/>";
        echo "<script language='JavaScript' type='text/javascript' src='".$CFG->url."lib/jslib.js'></script>";
        echo "<link href='http://fonts.googleapis.com/css?family=Orbitron:400,500' rel='stylesheet' type='text/css'>";    
        echo "</head>";
        echo "<body>";
        echo '<div id="glass_body"><div id="glass_header">'. _APLICATION_TITLE.'</div>';
        echo "<div id='glass_leftsection'>";
        echo "<div id='glass_logo'></div>";
        show_menu($CFG,$username);
        echo "</div>";
        echo "<div id='glass_rightsection'>";
        

		
		//////////////////////////////////////////			
		/////////////DASBOARD/////////////////////       
        
        //DDBB manager
        $dashboard_conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
            
        //Get the settings of the dashboard    
         $dashboard_query = "SELECT dbcol 
			FROM ".$CFG->prefix."settings
			WHERE userId='$userid'";
		$dashboard_result = mysql_query($dashboard_query) 
				or die(mysql_error());
        $dashboard_data = mysql_fetch_array($dashboard_result);
        define('_COL',$dashboard_data['dbcol']);
            
        //Get the widget of the dashboard
		$dashboard_query = "SELECT * 
			FROM ".$CFG->prefix."dashboard
			WHERE userId='$userid'
            ORDER BY pos";
		
		$dashboard_result = mysql_query($dashboard_query) 
				or die(mysql_error());
										
		//Get the necesary information of the database
        $dashboard_iteration = 0;
        $dashboard_conf = array();
        $dashboard_dbid = array();
        $dashboard_folder = array();
        while ($dashboard_data = mysql_fetch_array($dashboard_result))
        {
            $dashboard_conf[$dashboard_iteration] = $dashboard_data['widgetconf'];
            $dashboard_CAMdb[$dashboard_iteration] = $dashboard_data['bdCAMid'];
            $dashboard_dbid[$dashboard_iteration] = $dashboard_data['id'];
            
            $dashboard_query2 = "SELECT * 
			FROM ".$CFG->prefix."ddbb
			WHERE id='".$dashboard_CAMdb[$dashboard_iteration]."'";
            $dashboard_result2 = mysql_query($dashboard_query2) 
				or die(mysql_error());
            $dashboard_data2 = mysql_fetch_array($dashboard_result2)
				or die(mysql_error());
            $dashboard_CAMdbName[$dashboard_iteration] = $dashboard_data2['name'];
            
            
            $dashboard_query2 = "SELECT * 
			FROM ".$CFG->prefix."dashboard D, ".$CFG->prefix."modules M
			WHERE M.id=D.moduleId AND D.id='$dashboard_dbid[$dashboard_iteration]'";
            $dashboard_result2 = mysql_query($dashboard_query2) 
				or die(mysql_error());
            $dashboard_data2 = mysql_fetch_array($dashboard_result2)
				or die(mysql_error());
            $dashboard_folder[$dashboard_iteration]= $dashboard_data2['folder'];
           
            $dashboard_iteration++;
        }
        mysql_close($dashboard_conexion);
               
        $col=0;
		echo "<TABLE><TR>";
        for($dashboard_array_postion=0;$dashboard_array_postion<$dashboard_iteration;$dashboard_array_postion++)
        {
		    $conf = $dashboard_conf[$dashboard_array_postion];
            $dbid = $dashboard_dbid[$dashboard_array_postion];
            $CAMdb = $dashboard_CAMdb[$dashboard_array_postion];
            $folder= $dashboard_folder[$dashboard_array_postion];
            $CAMdbName = $dashboard_CAMdbName[$dashboard_array_postion];
            $linkpage = "./visualizations/".$folder."/";
            $info = "Module: $folder \nCAM database: $CAMdbName";
			
            if($col<_COL)
			{
				echo "<td>";
				include("widgettable.html");
				echo "</td>";
				$col++;	
			}
			else
			{
                echo "</TR><TR>";
  	            echo "<td>";
                include("widgettable.html");		
                echo "</td>";
                $col=1;
			}				
		}
		echo "</TR></TABLE>";	
			
        /////////////DASBOARD END/////////////////
        //////////////////////////////////////////
        
        echo "</div></div></body></html>";
        echo "<script>";
        echo "document.getElementById(\"glass_body\").style.width = \"".((422*(_COL))+182)."px\";";
        echo "</script>";
	}
	else
	{
        $goto = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: ".$CFG->url."index.html?goto=".$goto);
	} 
?>