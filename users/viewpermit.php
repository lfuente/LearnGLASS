<?php
    session_start();
    include_once("../config.php");
	    
	if(isset ($_SESSION['s_username']))
	{
		//Main includes
        include_once($CFG->dir."lang/lang.php");
        include_once($CFG->dir."lib/mainlib.php");
		
        //get the username & his permisions
        $username = $_SESSION['s_username'];
        $my_permision = new permision($CFG,$username);
        
        if($my_permision->get_userModifyPermision()==1)
        {
            // DB connect
    		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
    			or die("Imposible to connect to MySQL");
    		mysql_select_db($CFG->dbname) 
    			or die("Imposible to connect to GLASS database");
    		
            $query = "SELECT * FROM ".$CFG->prefix."permision";
            
    		$query = mysql_query($query) 
    				or die(mysql_error());
    		$data = mysql_fetch_array($query)
    				or die(mysql_error());
    		mysql_close($conexion);
            $i=0;
    		do
            {
                $jdata[$i] = array("userType" => $data['userType'],
                    "userViewLevel" => $data['userViewLevel'],
                    "userModifyPermision" => $data['userModifyPermision'],
                    "userTypeChange" => $data['userTypeChange'],
                    "moduleInstall" => $data['moduleInstall'],
                    "importView" => $data['importView'],
                    "varSettings" => $data['varSettings'],
                    "addBBDDCAM" => $data['addBBDDCAM'],
                    "download" => $data['download'],
                    "viewUser" => $data['viewUser'],
                    "viewSuggest" => $data['viewSuggest'],
                    );
              $i++;             		
    		}while ($data = mysql_fetch_array($query));
            echo json_encode($jdata);
		
        }		
	}
	else
	{
		header("Location: ".$CFG->url."index.html");
	}    
?>