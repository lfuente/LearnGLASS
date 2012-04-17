<?php
    
    function get_event_from_events($db){
        //database conection
        $objDB = MongoConnect($db->username,$db->password,$db->database,$db->host);
        //Find data
        $var = "role".$db->entityType;        
        $users = $objDB->$var->find()->timeout($db->time);
        //Sort data
        try {
            foreach ($users as $user) {
                $myArray[(string)$user["_id"]["name"]][$user["_id"]["date"]][$user["_id"]["type"]]=$user["value"]["count"];
            }
        }
        catch(MongoCursorTimeoutException $e) {
            echo "<br>Exception catched: ".$e;
        }
        //prepare data with the necesary structure
        foreach($myArray as $k1=>$v1){
            $js1 = array();
            foreach($myArray[$k1] as $k2=>$v2){
                $e_name = array();
                $e_num = array();
                foreach($myArray[$k1][$k2] as $k3=>$v3){
                    $e_name[count($e_name)] =$k3;
                    $e_num[count($e_num)] = $v3;
                }
                $js1[count($js1)] = array("d"=>str_replace("-","/",$k2),"n"=>$e_name,"e"=>$e_num); 
            }
            $js[count($js)] = array("et"=>$k1,"ed"=>$js1);
        }
        //return data
        return  $js;
    }
 
 
 
    
    function get_event_from_users($db){
        //database conection
        $objDB = MongoConnect($db->username,$db->password,$db->database,$db->host);
        //Find users of this metadata
        $userAtt = substr($db->entityType,5);  
        $users = $objDB->users->find()->timeout($db->time);
        try {
            foreach ($users as $user) {
                $ddata[(string)$user["_id"]]=$user[$userAtt];
            }
        }
        catch(MongoCursorTimeoutException $e) {
            echo "<br>Exception catched: ".$e;
        }      
        //Find data
        $var = "roleuser";       
        $users = $objDB->$var->find()->timeout($db->time);
        try {
            foreach ($users as $user) {
                
                if(isset($myArray[$k1][$user["_id"]["date"]][$user["_id"]["type"]])){
                    $myArray[$ddata[(string)$user["_id"]["name"]]][$user["_id"]["date"]][$user["_id"]["type"]]+=$user["value"]["count"];                  
                }
                else{
                    $myArray[$ddata[(string)$user["_id"]["name"]]][$user["_id"]["date"]][$user["_id"]["type"]]=$user["value"]["count"];
                }                         

            }
        }
        catch(MongoCursorTimeoutException $e) {
            echo "<br>Exception catched: ".$e;
        }
        //prepare data with the necesary structure
        foreach($myArray as $k1=>$v1){
            $js1 = array();
            foreach($myArray[$k1] as $k2=>$v2){
                $e_name = array();
                $e_num = array();
                foreach($myArray[$k1][$k2] as $k3=>$v3){
                    $e_name[count($e_name)] = $k3;
                    $e_num[count($e_num)] = $v3;
                }
                $js1[count($js1)] = array("d"=>str_replace("-","/",$k2),"n"=>$e_name,"e"=>$e_num); 
            }
            $js[count($js)] = array("et"=>$k1,"ed"=>$js1);
        }
        //return data
        return  $js;
    }



?>