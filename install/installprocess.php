<?php

    include("../config.php");
   // include("../lang/lang.php");
    include("../lib/mainlib.php");
    
    $dbhost = $_POST["dbhost"];
    $dbname = $_POST["dbname"];
    $dbuser = $_POST["dbuser"];
    $dbpass = $_POST["dbpass"];
    $prefix = $_POST["prefix"];
    $admin = $_POST["admin"];
    $url = $_POST["url"];
    $dir = $_POST["dir"];
    $dbOp = $_POST["dbOp"];
    $LDAPhost = $_POST["LDAPhost"];
    $LDAPport = $_POST["LDAPport"];
    $LDAPdn = $_POST["LDAPdn"];
    
    
    //Call the construct
    $change_config = new file_vars("../config.php");
    $change_dbfile = new file_vars("install.sql");
    
    //Set the values of the variables
    if ($dbhost!='' && $dbhost!=null && $dbhost!=$CFG->dbhost){
        $change_config->replace_PHP_value("\$CFG->dbhost" , $dbhost);
    }
    if ($dbname!='' && $dbname!=null && $dbname!=$CFG->dbname){
        $change_dbfile->replace_SQL_value("create database if not exists ".$CFG->dbname, "create database if not exists ".$dbname);
        $change_dbfile->replace_SQL_value("use ".$CFG->dbname, "use ".$dbname);
        $change_config->replace_PHP_value("\$CFG->dbname" , $dbname);
    }
    if ($dbuser!='' && $dbuser!=null && $dbuser!=$CFG->dbuser){
        $change_config->replace_PHP_value("\$CFG->dbuser" , $dbuser);
    }
    if ($dbpass!='' && $dbpass!=null && $dbpass!=$CFG->dbpass){
        $change_config->replace_PHP_value("\$CFG->dbpass" , $dbpass);
    }
    if ($prefix!='' && $prefix!=null && $prefix!=$CFG->prefix){
        $change_dbfile->replace_SQL_value($CFG->prefix , $prefix);
        $change_config->replace_PHP_value("\$CFG->prefix" , $prefix);
    }
    if ($url!='' && $url!=null && $url!=$CFG->url){
        $change_config->replace_PHP_value("\$CFG->url" , $url);
    }
    if ($dir!='' && $dir!=null && $dir!=$CFG->dir){
        $change_dbfile->replace_SQL_value($CFG->dir , $dir);
        $change_config->replace_PHP_value("\$CFG->dir" , $dir);
    }
    if ($LDAPhost!='' && $LDAPhost!=null && $LDAPhost!=$CFG->LDAPhost){
        $change_config->replace_PHP_value("\$CFG->LDAPhost" , $LDAPhost);
    }
    if ($LDAPport!='' && $LDAPport!=null && $LDAPport!=$CFG->LDAPport){
        $change_config->replace_PHP_value("\$CFG->LDAPport" , $LDAPport);
    }
    if ($LDAPdn!='' && $LDAPdn!=null && $LDAPdn!=$CFG->LDAPdn){
        $change_config->replace_PHP_value("\$CFG->LDAPdn" , $LDAPdn);
    }
    
    //update file
    $change_config->update_file();
    $change_dbfile->update_file();
   
    if($dbOp=='yes'){
        //Install de data base
        $newImport = new sqlImport ($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, "install.sql"); 
        $newImport->import(); 
        //------------------ Show Messages !!! --------------------------- 
        $import = $newImport -> ShowErr (); 
        if ($import[0] == true) { 
            echo _INSTALL_DB_SUCCESS;
            echo "<BR><A HREF='../index.html'>Login</A>";
        } 
        else { 
            echo _INSTALL_DB_FAIL."<br>\r\n"; 
            foreach($import[1] as $index => $value){ 
                echo $import [1][$index].": ".$import [2][$index]."<br>\r\n"; 
            } 
        }
    }
    else{
        echo "Done";
    }
    
?>
