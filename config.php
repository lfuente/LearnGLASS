<?php

    unset($CFG);  // Ignore this line
    global $CFG;  
    $CFG = new stdClass();
    
     
    //=========================================================================
    // 1. DATABASE SETUP
    //=========================================================================
    $CFG->dbhost = 'localhost';  // eg 'localhost' o 'it.uc3m.es'
    $CFG->dbname = 'glass5';      // database name, ej GLASS
    $CFG->dbuser = 'root';       // your database username
    $CFG->dbpass = 'root';       // your database password
    $CFG->prefix = 'glass_';       // prefix to use for all table names
    
    
    //=========================================================================
    // 2. CONFIGURATION SETUP
    //=========================================================================
    $CFG->adminName = '';
    $CFG->url = 'http://localhost/LearnGLASS/webapp/'; //URL where glass is published
    $CFG->dir = '/var/www/LearnGLASS/webapp/';                      //path where glass is locally stored
    
    //=========================================================================
    // 3. LDAP SETUP
    //=========================================================================
    $CFG->LDAPhost = 'mozart.gast.it.uc3m.es';          // host or server name
    $CFG->LDAPport = '389';                             // LDAP server port
    $CFG->LDAPdn = 'ou=people,dc=mozart,dc=local';   //LDAP dn
    

?>
