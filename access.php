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
            $ldap['dn']   = 'uid='.$ldap['user'].','.$CFG->LDAPdn;
            // conexion a ldap
            $ldap['conn'] = ldap_connect( $ldap['host'], $ldap['port'] );
            ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);
            // match de usuario y password
            @$ldap['bind'] = ldap_bind( $ldap['conn'], $ldap['dn'], $ldap['pass'] );
            if ($ldap['bind']){
            	create_user_session($ldap['user'], $CFG);
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