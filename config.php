<?php

    unset($CFG);  // Ignore this line
    global $CFG;  
    $CFG = new stdClass();
    
     
    //=========================================================================
    // 1. DATABASE SETUP
    //=========================================================================
    $CFG->dbhost = 'localhost';  // eg 'localhost' o 'it.uc3m.es'
    $CFG->dbname = 'glass';      // database name, ej GLASS
    $CFG->dbuser = '';       // your database username
    $CFG->dbpass = '';       // your database password
    $CFG->prefix = '';       // prefix to use for all table names
    
    
    //=========================================================================
    // 2. CONFIGURATION SETUP
    //=========================================================================
    $CFG->adminName = 'admin';
    $CFG->url = 'http://glass.mozart.gast.it.uc3m.es/'; //URL where glass is published
    $CFG->dir = '/var/www/glass/';                      //path where glass is locally stored
    
    //=========================================================================
    // 3. LDAP SETUP
    //=========================================================================
    $CFG->LDAPhost = '';          // host or server name
    $CFG->LDAPport = '';                             // LDAP server port
    $CFG->LDAPdn   = '';   //LDAP dn
    

?>
