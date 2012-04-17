<?php
	session_start();
	
	//Main includes
	include("../config.php");
	include("../lib/mainlib.php");
	
	if(isset ($_SESSION['s_username']))
	{
	           
        $op = $_POST["op"];
        $value = $_POST["value"];
        
        if($op=="install"){
            //Install database
            $fp = fopen($CFG->dir."visualizations/".$value."/info.txt","r");
            while($text = fgets($fp)){
                $file_values = explode(":",$text);
                if($file_values[0]=="name"){
                    $name = $file_values[1];
                }
                if($file_values[0]=="description"){
                    $des = $file_values[1];
                }
            }
            fclose($fp);  
            if($name==null || $des==null){
                $msg = "The file info.txt of the module in not created correctly";
            }
            else{                
                //Open database conection
                $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
        			or die(mysql_error());
        		mysql_select_db($CFG->dbname) 
        			or die(mysql_error());
                //fetch if this name already exist
                $query = "SELECT * FROM ".$CFG->prefix."modules
                    WHERE name='".$name."';";
                $result = mysql_query($query) 
        			or die(mysql_error());
                if(mysql_num_rows($result)==0){
                    //Insert the new module
                    $query = "INSERT INTO ".$CFG->prefix."modules
                        (id,name,folder,description) VALUES
                        (NULL,'".$name."','".$value."','".$des."');";
                    $result = mysql_query($query) 
            			or die(mysql_error());
                    $msg = "The module '".$name."' has been installed correctly";  
                }
                else{
                    $msg = "This module name allready exists, change the info.txt and try again";
                } 
            }               
        }
        
        if($op=="uninstall"){
            //Open database conection
            $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
    			or die(mysql_error());
    		mysql_select_db($CFG->dbname) 
    			or die(mysql_error());
            
            //fetch the module id
            $query = "SELECT * FROM ".$CFG->prefix."modules
                WHERE folder='".$value."';";
            $result = mysql_query($query) 
    			or die(mysql_error());
            $data = mysql_fetch_array($result);
            $id = $data["id"];
            $name = $data["name"];
            
            //delete dashboard data
            $query = "DELETE FROM ".$CFG->prefix."dashboard
                WHERE moduleId='".$id."';";
            $result = mysql_query($query) 
    			or die(mysql_error());
            
            //delete myviews data
            $query = "DELETE FROM ".$CFG->prefix."myview
                WHERE moduleId='".$id."';";
            $result = mysql_query($query) 
    			or die(mysql_error());
            
            //delete module data
            $query = "DELETE FROM ".$CFG->prefix."modules
                WHERE id='".$id."';";
            $result = mysql_query($query) 
    			or die(mysql_error());
            
            mysql_close($conection);
            
            $msg = "The module '".$name."' has been uninstalled correctly";
       }
        
        
        //Open database conection
        $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
        
        //build the query
        $query = "SELECT * FROM ".$CFG->prefix."modules";
        $result = mysql_query($query) 
			or die(mysql_error());
        
        //Get the install modules
        $i=0;
        $data_name = array();
        $data_folder = array();
        $data_des = array();
        while($data = mysql_fetch_array($result))
        {
            $data_name[$i] = $data['name'];
            $data_folder[$i] = $data['folder'];
            $data_des[$i] = $data['description'];
            $i++; 
        }
        mysql_close($conection);
             
        //fetch the folders and the module description
        $dir_files = scandir($CFG->dir."visualizations/");
        $avaible_des = array();
        $avaible_files = array();
        $counter=0;
        //for all the folders of the directory
        for($i=0;$i<count($dir_files);$i++){
            if($dir_files[$i]!="." && $dir_files[$i]!=".." && $dir_files[$i]!=".svn"){
                $installed = 0;
                //for all the folders installed
                for($j=0;$j<count($data_folder);$j++){
                    //fetch a match
                    if($dir_files[$i]==$data_folder[$j]){
                       $installed = 1;
                       break; 
                    }
                }
                //if it is not installed
                if($installed==0){
                    //save the name of the folder
                    $avaible_files[$counter] = $dir_files[$i];
                    //fetch his description
                    if(file_exists($file_to_open = $CFG->dir."visualizations/".$avaible_files[$counter]."/info.txt")){
                        $fp = fopen($file_to_open,"r");
                        while($text = fgets($fp)){
                            $file_values = explode(":",$text);
                            if($file_values[0]=="description"){
                                $des = $file_values[1];
                            }
                        }
                        fclose($fp);
                    }
                    $avaible_des[$counter]=$des;
                    $counter++;
                } 
            }             
        }
        
        //build an array with the datas
        $jdata = array("name"=>$data_name,"folder"=>$data_folder,"des"=>$data_des,"fname"=>$avaible_files,"fdes"=>$avaible_des,"msg"=>$msg);
        
        //Build a json string with the datas
        echo "[".json_encode($jdata)."]";	
	}
	else
	{
		header("Location: ".$CFG->url."index.html");
	}  

?>