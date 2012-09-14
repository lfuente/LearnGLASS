<?php
	session_start();
	//configure settings
    include_once("../config.php");    

    if(isset ($_SESSION['s_username']))
    {
        $username = $_SESSION['s_username'];
        include_once($CFG->dir."lang/lang.php");
        include_once($CFG->dir."lib/mainlib.php");
        $my_per = new permision($CFG,$username);
        
        
        $op = $_POST["op"];
        $name = $_POST["name"];
        $userId = get_user_id($CFG,$username);

        //Open database conection
        $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
            
            
        if($op == "delete")
        {
            //Get the position
            $query = "SELECT pos FROM ".$CFG->prefix."myview 
                WHERE name='".$name."'
                AND userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
            $data = mysql_fetch_array($result);
            $pos = $data["pos"];
            
            //Get the number of views of the user
            $query = "SELECT pos FROM ".$CFG->prefix."myview 
                WHERE userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
            $view_user_number = mysql_num_rows($result);
            
            //Update the position of the views
            for($i=$pos;$i<=$view_user_number;$i++){
                $query = "UPDATE ".$CFG->prefix."myview
                    SET pos='".($i)."'
                    WHERE pos='".($i+1)."'
                    AND userid='".$userId."'";
                $result = mysql_query($query) or die(mysql_error());
                
            }
            
            //Delete the view
            $query = "DELETE FROM ".$CFG->prefix."myview 
                WHERE name='".$name."'
                AND userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
                
                
            
        }
        if($op == "up")
        {
            //Get the position
            $query = "SELECT pos FROM ".$CFG->prefix."myview 
                WHERE name='".$name."'
                AND userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
            $data = mysql_fetch_array($result);
            $pos = $data["pos"];
            //If it is not in the first element
            if($pos>1)
            {
                //Update de upper element
                $query = "UPDATE ".$CFG->prefix."myview
                    SET pos='".$pos."'
                    WHERE pos='".($pos-1)."'
                    AND userid='".$userId."'";
                $result = mysql_query($query) or die(mysql_error());
                //Update the selected element
                $query = "UPDATE ".$CFG->prefix."myview
                    SET pos='".($pos-1)."'
                    WHERE name='".$name."'
                    AND userid='".$userId."'";
                $result = mysql_query($query) or die(mysql_error());

            }
        }
        if($op == "down")
        {
            //Get the position
            $query = "SELECT pos FROM ".$CFG->prefix."myview 
                WHERE name='".$name."'
                AND userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
            $data = mysql_fetch_array($result);
            $pos = $data["pos"];
            //Update de downer element
            $query = "UPDATE ".$CFG->prefix."myview
                SET pos='".$pos."'
                WHERE pos='".($pos+1)."'
                AND userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
            //Update the selected element
            $query = "UPDATE ".$CFG->prefix."myview
                SET pos='".($pos+1)."'
                WHERE name='".$name."'
                AND userid='".$userId."'";
            $result = mysql_query($query) 
				or die(mysql_error());
        }
        
        
        
        
        $query = "SELECT *
			FROM ".$CFG->prefix."myview 
            WHERE userid='".$userId."'
            ORDER BY pos";
        $result = mysql_query($query) 
				or die(mysql_error());
        
        $i=0;
        while($data = mysql_fetch_array($result))
        {
            $query = "SELECT *
			FROM ".$CFG->prefix."modules
            WHERE id='".$data["moduleId"]."'";
            $subresult = mysql_query($query) 
				or die(mysql_error());
            $subdata = mysql_fetch_array($subresult);
            if($my_per->get_importView()==1)            { 
                $per = 1;
            }
            else{
                $per = 0;
            }
            $share =  $CFG->url."visualizations/".$subdata["folder"]."/share.php?fid=".$data["id"];
            $link = $CFG->url."visualizations/".$subdata["folder"]."/index.php?fconf=".$data["id"];
            $jdata[$i] = array("name"=>$data['name'],"url"=>$link,"des"=>$data['description'],"url"=>$link,"link"=>$share,"per"=>$per);
            $i++;           
        }
        mysql_close($conection);
        echo json_encode($jdata);
	}
	else
	{
		header("Location: ".$CFG->url."index.html");
	} 
?>