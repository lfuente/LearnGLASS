<?php
	//first ask whether the variable that will contain the language of the visitor is empty
	if(empty($sitelang))
	{ 
		//if it is empty we get the language of visitors and put it in the variable $sitelang
		$sitelang = getenv("HTTP_ACCEPT_LANGUAGE"); 
		$sitelang = substr($sitelang, 0, 2);
	}
	//load the file containing the language according to the varible we took before
	//Open database conection
    $conection = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die(mysql_error());
	mysql_select_db($CFG->dbname) 
		or die(mysql_error());
    //build the query
    $query = "SELECT folder FROM ".$CFG->prefix."modules";
    $result = mysql_query($query) 
			or die(mysql_error());
    //make switch
    switch($sitelang)
	{  
		//es = spanish  
		case "es" :
            $lang_file = "lang-sp.php";
            $lang_glass_file = $CFG->dir."lang/".$lang_file;
			include_once($lang_glass_file);
            while($data = mysql_fetch_array($result)){
                $lang_module_file = $CFG->dir."visualizations/".$data['folder']."/lang/".$lang_file;
                if(file_exists($lang_module_file)){
                    include_once($lang_module_file);
                }
            }
			break;
		//en = english  
		case "en" :  
			$lang_file = "lang-eng.php"; 
			$lang_glass_file = $CFG->dir."lang/".$lang_file;
			include_once($lang_glass_file);
            while($data = mysql_fetch_array($result)){
                $lang_module_file = $CFG->dir."visualizations/".$data['folder']."/lang/".$lang_file;
                if(file_exists($lang_module_file)){
                    include_once($lang_module_file);
                }
            }
			break;
		//default language
		default : 
			$lang_file = "lang-sp.php"; 
			$lang_glass_file = $CFG->dir."lang/".$lang_file;
			include_once($lang_glass_file);
            while($data = mysql_fetch_array($result)){
                $lang_module_file = $CFG->dir."visualizations/".$data['folder']."/lang/".$lang_file;
                if(file_exists($lang_module_file)){
                    include_once($lang_module_file);
                }
            }
			break;
	}
    //close database conection
    mysql_close($conection);

	
?> 