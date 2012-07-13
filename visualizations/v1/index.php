<?php
    session_start();
    include("../../config.php");

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
        
        ///////////////////// MODULE CODE ////////////////////////
        //////////////////////////////////////////////////////////
        $confiId =  $_GET['conf'];
        $fconfId =  $_GET['fconf'];
        $userid = get_user_id($CFG,$username);
        $my_permision = new permision($CFG,$username);
        //link from widget
        if($confiId!=null || $fconfId!=null)
        {
            $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
        		or die(mysql_error());
        	mysql_select_db($CFG->dbname) 
        		or die(mysql_error());
            if($fconfId!=null){
                //get my favorite views values
                $query = "SELECT * 
        		FROM ".$CFG->prefix."myview
        		WHERE id='$fconfId'";
                $confiId=0;                
            }
            if($confiId!=null){
                //get the dashboard values 
            	$query = "SELECT * 
            		FROM ".$CFG->prefix."dashboard
            		WHERE id='$confiId'";
                    $fconfId=0;
            }
        	
        	$result = mysql_query($query) 
        			or die(mysql_error());
        	$data = mysql_fetch_array($result)
        			or die(mysql_error());
            
            mysql_close($conexion);
            
            //create the _GET structure with the params of the database
            $conf = json_decode($data['widgetconf']);
            $CAMid = $data['bdCAMid'];
                        
            $option = "CAMid=".$CAMid."&";
            
            for($i=0;$i<count($conf->key);$i++)
            {
                $option = $option."group".($i+1)."=".$conf->group[$i]."&key".($i+1)."=".$conf->key[$i]."&value".($i+1)."=".$conf->value[$i]."&";
            }
            if($conf->mMax!=null && $conf->mMin!=null)
            {
                 $option = $option."mMax=".$conf->mMax."&";
                 $option = $option."mMin=".$conf->mMin."&";
            }
            //be careful, view is always 1 because it is necessary for the first view to generate the second
            $option = $option."view=1";
        }
        //link from module menu
        else
        {
            $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
        		or die(mysql_error());
        	mysql_select_db($CFG->dbname) 
        		or die(mysql_error());
            //get the dashboard values 
        	$query = "SELECT * 
        		FROM ".$CFG->prefix."settings
        		WHERE userId='$userid'";
        	
        	$result = mysql_query($query) 
        			or die(mysql_error());
        	$data = mysql_fetch_array($result)
        			or die(mysql_error());
            
            mysql_close($conexion);
            $option=null;
            
            $CAMid = $data['ddbbId'];
            $option = "CAMid=".$data['ddbbId']."&";
            $option = $option."view=1";
            $confiId = $fconfId = 0;
        }   
        
        include ("index.html");
       
        //Get the dafault parameters
        echo '<script>';
        
        echo 'get_control_filters("'.$CAMid.'");';
        echo 'get_data("getdata.php?'.$option.'");';
        echo '</script>';
              
        if($confiId==0)
        {
            echo '<script>
                    document.all.update1.style.visibility="hidden";
                    document.all.update2.style.visibility="hidden";
                </script>';
        }
        if($fconfId==0)
        {
            echo '<script>
                    document.all.update3.style.visibility="hidden";
                    document.all.update4.style.visibility="hidden";
                </script>';
        }
        
        ///////////////////// MODULE END /////////////////////////
        //////////////////////////////////////////////////////////
        
        echo "</div></div></body></html>";	
        	
	}
	else
	{
        $goto = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: ".$CFG->url."index.html?goto=".$goto);
	} 


?>