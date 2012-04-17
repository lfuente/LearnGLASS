<?php
	session_start();
	include("config.php");
    include($CFG->dir."lang/lang.php");
    include($CFG->dir."lib/mainlib.php"); 
    //Look if GLASS is istalled        
    if (is_table($CFG->prefix."dashboard") && is_table($CFG->prefix."permision")){
		$user = $_POST["user"];
        $pass  = $_POST["pass"];
        //Look if there is any input empty
        if($user!=null && $pass!=null){
            //LDAP access
            $ldap['user'] = $user;
            $ldap['pass'] = $pass;
            $ldap['host'] = $CFG->LDAPhost;
            $ldap['port'] = (int)$CFG->LDAPport;
            $ldap['dn']   = 'uid='.$ldap['user'].$CFG->LDAPdn;
            // conexion a ldap
            $ldap['conn'] = ldap_connect( $ldap['host'], $ldap['port'] );
            ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);
            // match de usuario y password
            @$ldap['bind'] = ldap_bind( $ldap['conn'], $ldap['dn'], $ldap['pass'] );
            if ($ldap['bind']){
                $user = $_SESSION['s_username'] = $ldap['user'];
                //If user is not in the database there is to create him
                //then, create a conexion to the glass database
                $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
        			or die(mysql_error());
        		mysql_select_db($CFG->dbname) 
        			or die(mysql_error());
                //Search the user in the database  
                $query = "SELECT name 
        			FROM ".$CFG->prefix."user
        			WHERE name='$user'";
        		$result = mysql_query($query) 
        				or die(mysql_error());
                //is there any match?
                if(mysql_num_rows($result)==0){
                    //Create the user in the database
                    $query = "INSERT INTO ".$CFG->prefix."user (name,userType) 
                        VALUES ('".$user."','student')" ;
                    $result = mysql_query($query) 
        				or die("error: ".mysql_error());
                    mysql_close($conexion);
                    //get his created userId
                    $userId = get_user_id($CFG,$user);
                    //get the first CAM database id
                    $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
        			or die(mysql_error());
        		      mysql_select_db($CFG->dbname) 
        			or die(mysql_error());
                    $query = "SELECT id 
        			FROM ".$CFG->prefix."bbdd";
                    $result = mysql_query($query) 
        				or die(mysql_error());
                    $CAMid = mysql_fetch_array($result);
                    //Create the user settings entry in the database
                    $query = "INSERT INTO ".$CFG->prefix."settings (ddbbId,userId,dbcol) 
                        VALUES ('".$CAMid["id"]."','".$userId."','2')" ;
                    $result = mysql_query($query) 
        				or die(mysql_error());
                }
                mysql_close($conexion); 
                //go to home.php or the type page 
                echo 1;
            } 
            else {
                echo _LOGIN_WRONG;
            }   
        }
        else{
            echo _LOGIN_NODATA;
        }
    }
    else{
        //Lack tables in the database, there is to go to install process
        session_destroy();
        echo 2;
    }
    
    
    
    

?>