<?php




/////////////////// PERMITS CLASS //////////////////////////
////////////////////////////////////////////////////////////
//class which save or load the user permits in the database
////////////////////////////////////////////////////////////
class permision
{
    private $userName;
    private $userType;
    private $userViewLevel;
    private $userModifyPermision;
    private $userTypeChange;
    private $moduleInstall;
    private $importView;
    private $varSettings;
    private $addBBDDCAM;
    private $download;
    private $viewUser;
    private $viewSuggest;
    
    //class constructor
    function __construct($CFG,$user)
    {                   
        $this->userName=$user;

        // DB connect
		$conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		
		$query = "SELECT * 
			FROM ".$CFG->prefix."user U, ".$CFG->prefix."permision P 
			WHERE U.name='$this->userName' AND U.userType=P.userType";

		$result = mysql_query($query) 
				or die(mysql_error());
        $data = mysql_fetch_assoc($result)
				or die(mysql_error());

        $this->userType = $data['userType'];
        $this->userViewLevel = $data['userViewLevel'];
        $this->userModifyPermision = $data['userModifyPermision'];
        $this->userTypeChange = $data['userTypeChange'];
        $this->moduleInstall = $data['moduleInstall'];
        $this->importView = $data['importView'];
        $this->varSettings = $data['varSettings'];
        $this->addBBDDCAM = $data['addBBDDCAM'];
        $this->download = $data['download'];
        $this->viewUser = $data['viewUser'];
        $this->viewSuggest = $data['viewSuggest'];
        
        mysql_close($conexion);
    }
    
    //Class destrcutor
    function __destruct()
    {
        $this->userName;
        $this->userType;
        $this->userViewLevel;
        $this->userModifyPermision;
        $this->userTypeChange;
        $this->moduleInstall;
        $this->importView;
        $this->varSettings;
        $this->addBBDDCAM;
        $this->download;
        $this->viewUser;
        $this->viewSuggest;
    }

    //class UPDATE Database query
    public function set_DB_userViewLevel($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET userViewLevel='$this->userViewLevel'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);            
    }
    public function set_DB_userModifyPermision($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET userModifyPermision='$this->userModifyPermision'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_userTypeChange($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET userTypeChange='$this->userTypeChange'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_moduleInstall($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET moduleInstall='$this->moduleInstall'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_importView($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET importView='$this->importView'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_varSettings($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET varSettings='$this->varSettings'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_addBBDDCAM($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET addBBDDCAM='$this->addBBDDCAM'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_download($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET download='$this->download'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_viewUser($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET viewUser='$this->viewUser'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    public function set_DB_viewSuggest($CFG)
    {
        $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
		$query = "UPDATE ".$CFG->prefix."permision 
        SET viewSuggest='$this->viewSuggest'
		WHERE userType='$this->userType'";
		mysql_query($query);
		mysql_close($conexion);
    }
    
    //class set values
    public function set_userType($uType)
    {
        $this->userType = $uType;
    }
    public function set_userViewLevel($uViewLevel)
    {
        $this->userViewLevel = $uViewLevel;
    }
    public function set_userModifyPermision($uModifyPermision)
    {
        $this->userModifyPermision = $uModifyPermision;
    }
    public function set_userTypeChange($userTypeChange)
    {
        $this->userTypeChange = $userTypeChange;
    }
    public function set_moduleInstall($moduleInstall)
    {
        $this->moduleInstall = $moduleInstall;
    }
    public function set_importView($importView)
    {
        $this->importView = $importView;
    }
    public function set_varSettings($varSettings)
    {
        $this->varSettings = $varSettings;
    }
    public function set_addBBDDCAM($addBBDDCAM)
    {
        $this->addBBDDCAM = $addBBDDCAM;
    }
    public function set_download($download)
    {
        $this->download = $download;
    }
    public function set_viewUser($viewUser)
    {
        $this->viewUser = $viewUser;
    }
    public function set_viewSuggest($viewSuggest)
    {
        $this->viewSuggest = $viewSuggest;
    }
    
    //clss get values
    public function get_userType()
    {
        return $this->userType;
    }
    public function get_userViewLevel()
    {
        return $this->userViewLevel;
    }
    public function get_userModifyPermision()
    {
        return $this->userModifyPermision;
    }
    public function get_userTypeChange()
    {
       return $this->userTypeChange;
    }
    public function get_moduleInstall()
    {
        return $this->moduleInstall;
    }
    public function get_importView()
    {
        return $this->importView;
    }
    public function get_varSettings()
    {
        return $this->varSettings;
    }
    public function get_addBBDDCAM()
    {
        return $this->addBBDDCAM;
    }
    public function get_download()
    {
        return $this->download;
    }
    public function get_viewUser()
    {
        return $this->viewUser;
    }
    public function get_viewSuggest()
    {
        return $this->viewSuggest;
    }
}
    
    
    
    
    
    
    
    

    
    

/////////////////// SQL IMPORT CLASS ///////////////////////
////////////////////////////////////////////////////////////
//Load a sql file to install the necessary tables and values
////////////////////////////////////////////////////////////
class sqlImport 
{ 
    var $ErrorDetected = false; 
    var $CodigoError; 
    var $TextoError; 
    function is_comment($text){ 
        if ($text != ""){ 
            $fL = $text[0]; 
            $sL = $text[1]; 
            switch($fL){ 
                case "#": 
                    $text = ""; 
                    break; 
                case "/": 
                    if ($sL == "*") 
                        $text = ""; 
                    break; 
                case "-": 
                    if ($sL == "-") 
                        $text = ""; 
                    break; 
                     
            } 
        } 
        return $text; 
    } 
     
    //retrieving the vars 
    function sqlImport ($host, $user,$pass, $ArchivoSql) { 
    $this -> host = $host; 
    $this -> user = $user; 
    $this -> pass = $pass; 
    $this -> ArchivoSql = $ArchivoSql; 
    $this->dbConnect(); 
    } 

    //Connecting to the DB 
    function dbConnect () { 
        $this->con = mysql_connect($this -> host, $this -> user, $this -> pass); 
    } 
     
    //Processing and importing of the SQL statements 
    function import ()  
    {    
        //if we're connected to DB 
           if ($this -> con !== false)  
           { 
            //opening and reading the .sql file 
            $f = fopen($this -> ArchivoSql,"r+"); 
            $sqlFile = fread($f, filesize($this -> ArchivoSql)); 
            // processing and parsing the content 
            $sqlFile = str_replace("\r","%BR%",$sqlFile); 
            $sqlFile = str_replace("\n","%BR%",$sqlFile); 
            $sqlFile = str_replace("%BR%%BR%","%BR%",$sqlFile); 
            $sqlArray = explode('%BR%', $sqlFile); 
            $sqlArrayToExecute; 
            foreach ($sqlArray as $stmt)  
            { 
                $stmt = $this->is_comment($stmt); 
                if ($stmt != '') 
                    $sqlArrayToExecute[] = $stmt; 
            } 
            $sqlFile = implode("%BR%",$sqlArrayToExecute); 
            unset($sqlArrayToExecute); 
            $sqlArray = explode(';%BR%', $sqlFile); 
            unset($sqlFile); 
            //making a loop with all the valid statements 
            foreach ($sqlArray as $stmt){ 
                $stmt = str_replace("%BR%"," ",$stmt); 
                $stmt = trim($stmt); 
                //$sqlArrayToExecute[] = $stmt; 
                // making the query 
                $result = mysql_query($stmt,$this->con); 
                if (!$result) 
                { 
                    // if we aren't connected throw an error 
                    $this->ErrorDetected = true; 
                    $this->CodigoError[] = mysql_errno($this->con); 
                    $this->TextoError[] = mysql_error($this->con); 
                } 
            } 
            //print_r($sqlArrayToExecute); 
         } else { 
         // if we aren't connected throw an error 
            $this->ErrorDetected = true; 
            $this->CodigoError[] = "1"; 
            $this->TextoError[] = "MySQL server access denied, please check the access data login"; 
         } 
           
    }//End of importing 
     
    //Controlling and displaying the errors on the process 
    function ShowErr ()  
    {     
           if ($this->ErrorDetected == false) 
           { 
            $OutPut [0] =  true; 
        }else{ 
            $OutPut [0] =  false;            
            $OutPut [1] = $this->CodigoError; 
            $OutPut [2] =  $this->TextoError; 
           } 

    return $OutPut; 
    } 
     
} 
    
    
    
/////////// MANAGE FILE VARS CLASS /////////////////
////////////////////////////////////////////////////
//Change the variable value of a php file.
////////////////////////////////////////////////////    
class file_vars{
    private $data;  //content of the file
    private $file;  //name of the file
    
  
    //class constructor
    function __construct($fileName){
        $this->file = $fileName;
        if($fp = fopen($this->file, "r"))
        {
            $this->data = fread($fp, filesize($fileName));
            fclose($fp);
        }
        else 
        {
            exit ("Error!!! the file {$fileName} can not be read, check permissions.");
        }
    }
    
    //class destrucor
    function __destruct(){
        $this->data;
        $this->varName;
        $this->newVarValue;
        $this->file;
    }
    
    //change the value of the variable
    function replace_PHP_value($varName, $newVarValue){  
        $charEnd = ";";  //endline character
        $dataAux = strchr($this->data,$varName);  //choose the string
        $posEnd = strpos($dataAux,$charEnd);    //end position of the variable sintxis 
        $dataAux = substr($dataAux,0,$posEnd+1); //substract a string with var name and var value
        $newVarValue = $varName." = '".$newVarValue."';"; //prepare the string with the new value
        $this->data = str_replace($dataAux,$newVarValue,$this->data); //replace the string
    }
    
    function replace_SQL_value($var, $varReplace){  
        $this->data = str_replace($var,$varReplace,$this->data); //replace the string
    }
   
    //update the file with the new value set
    function update_file(){
        if($fp = fopen($this->file, "w")){
            fwrite($fp, stripslashes($this->data));        
            fclose($fp);
        } 
        else {
            exit ("Error!!! the file {$this->file} can not be write, check permissions.");
        } 
    }
    
    //to set the neme of the file
    function set_file($fileName){
        $this->file = $fileName;
    }
     
}
    
    
    
    
////////////////// GET USER ID FUNCTION ////////////////////
////////////////////////////////////////////////////////////
// Load the user_id of a user
////////////////////////////////////////////////////////////     
function get_user_id($CFG,$username){
    $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
		or die(mysql_error());
	mysql_select_db($CFG->dbname) 
		or die(mysql_error());

	$query = "SELECT userId
		FROM ".$CFG->prefix."user
		WHERE name='$username'";
	
	$query = mysql_query($query) 
			or die(mysql_error());
	$userid = mysql_fetch_array($query)
			or die(mysql_error());
    mysql_close($conexion);
    return $userid['userId'];
}
    
    
    
    
////////////////// PRINT MENU FUNCTION /////////////////////
////////////////////////////////////////////////////////////
// Prints the menu
////////////////////////////////////////////////////////////  
function show_menu($CFG,$username){   
    $my_permision = new permision($CFG,$username);
    $indexurl = $CFG->url;
    $conexion = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die(mysql_error());
	mysql_select_db($CFG->dbname)or die(mysql_error());
	$query = "SELECT * FROM ".$CFG->prefix."modules";
	$query = mysql_query($query) or die(mysql_error());
	include("menu.html");
    mysql_close($conexion); 
}



////////////////// MAKE SAFE FUNCTION /////////////////////
////////////////////////////////////////////////////////////
// Check the contents of a string of characters in search of 
// unwanted characters
//////////////////////////////////////////////////////////// 
function make_safe($variable) {
    $variable = addslashes(trim($variable));
    return $variable;
}




///////////////// GET USER TYPE FUNCTION ///////////////////
////////////////////////////////////////////////////////////
// Convert user type specified as a number to a text
//////////////////////////////////////////////////////////// 
function getUserType($uType){
    switch ($uType){
        case 000:
            return "admin"; 
            break;
        case 001:
            return "instructor";
            break;
        case 002:
            return "observer";
            break;
        case 003:
            return "student";
            break;
    }
}


///////////////////// TABLES FUCNTION //////////////////////
////////////////////////////////////////////////////////////
// looks for certain tables exist in the database
////////////////////////////////////////////////////////////
function is_table($tablename){
    include("config.php");
    $link = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
    	or die(mysql_error());
    $tables = mysql_list_tables($CFG->dbname) 
        or die(mysql_error());
    while (list($table) = mysql_fetch_array($tables)){
        if ($tablename == $table){
             mysql_close($link);
             return true; 
             break; 
         } 
    }
    mysql_close($link);
    return false; 
}


///////////////////// FUNCTION DELETE FOLDER ////////////////////
////////////////////////////////////////////////////////////////
// delete a folder and his files
//////////////////////////////////////////////////////////////
function delete_folder($dir, $borrarme){
    if(!$dh = @opendir($dir)) return;
    while (false !== ($obj = readdir($dh))){
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj)) borrar_directorio($dir.'/'.$obj, true);
    }
    closedir($dh);
    if ($borrarme){
        @rmdir($dir);
    }
}



///////////////////// FUNCTION IS A IP //////////////////////
////////////////////////////////////////////////////////////
// check if $ipdir is a ip direction
////////////////////////////////////////////////////////////
function is_a_ip($ipdir){
    if (preg_match('^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$^',$ipdir)){
        return true;
    }
    else return false;
}


///////////////////// FUNCTION IS A EMAIL //////////////////////
////////////////////////////////////////////////////////////
// check if $email is a email
////////////////////////////////////////////////////////////
function is_a_email($email){
    if (preg_match(
    '/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/^',
    $email)) {
        return true;
    }
    else return false;
}

///////////////////// FUNCTION RETURN DATE /////////////////
////////////////////////////////////////////////////////////
// Return the dathe of a datetime string
////////////////////////////////////////////////////////////
function return_date($date){
    if(strtotime($date)==-1) return -1;
    return strftime("%Y-%m-%d %I:%M:%S %p",strtotime($date));        
}



///////////////////// FUNCTION MONGO CONNECT ///////////////
////////////////////////////////////////////////////////////
// Return the mongo conection 
////////////////////////////////////////////////////////////
function MongoConnect($username, $password, $database, $host) {
    try{
        $con = new Mongo("mongodb://{$username}:{$password}@{$host}/{$database}"); // Connect to Mongo Server
        if($con->selectDB($database)->events->count()>0){
            $db = $con->selectDB($database); // Connect to Database   
        }
        else{
            echo "error: The database does not have GLASS format!";
            exit();
        }
    }
    catch(Exception $e){
        echo "error: Impossible to connect!", PHP_EOL;
        exit();
    }
    return $db;
}
  
  
  

///////////////// FUNCTION DELETE USER FROM JSON ///////////
////////////////////////////////////////////////////////////
// Return the json code only with the specified user
////////////////////////////////////////////////////////////
function json_delete_users($username,$jdata){
    $pos1 = strpos($jdata,"\"user\"");
    $jdataAux = substr($jdata, $pos1);
    $pos2 = strpos($jdataAux,"\"f_values\"");
    $jdataAux = substr($jdataAux, $pos2);
    $lenJdataAux  = strpos($jdataAux,"]");
    $jdataAux = substr($jdataAux, 0, $lenJdataAux);
    return str_replace($jdataAux,"\"f_values\":[\"None\",\"".$username."\"",$jdata);
    
}

///////////////// FUNCTION CREATE USER SESSION ///////////
////////////////////////////////////////////////////////////
// Return void
////////////////////////////////////////////////////////////
function create_user_session($user, $CFG){

	$_SESSION['s_username'] = $user;
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
				FROM ".$CFG->prefix."ddbb";
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
}
  
///////////////// FUNCTION CHECK TOKEN ///////////
////////////////////////////////////////////////////////////
// Return true if the token is OK and 
// the timestamp is not too old
////////////////////////////////////////////////////////////
function check_token($token, $timestamp, $user, $CFG){
	
	//ONLY FOR THE CRIF PILOT
	include($CFG->dir."tokenkey.php");
		
	$key = $KEY->tokenkey;
	$token_ok = false;
    $msg = null;


	$right_now = time();
	$elapsed_time = $right_now - $timestamp;
	

	//the token should not be older than one hour
	if ($elapsed_time < -3600) {
        $msg = "_TOKEN_ERROR_MSG_1_0"; 
    } else if ($elapsed_time > 3600) {
    	$msg = '_TOKEN_ERROR_MSG_2_0';
    } else {
       //let's check if the token was created with the valid key
        $local_token = md5($timestamp . $user . $key);
        if ($local_token != $token) {
            $msg = '_TOKEN_ERROR_MSG_3_0';
        } else {
            $token_ok = true;
        }    	
    }
    
    return array($token_ok,$msg);
}

///////////////// FUNCTION USER IN SYSTEM ///////////
////////////////////////////////////////////////////////////
// Return true if the user is in the local database or in the ldap (the password is not checked)
//        false otherwise
////////////////////////////////////////////////////////////
function user_in_system($user, $CFG){
    
    $in_system = false; //the value that will be returned
    $msg = '_USER_AUTH_ERROR_MSG_1';

    //first check the local database
    $conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
            or die(mysql_error());
    mysql_select_db($CFG->dbname) 
            or die(mysql_error());
    //Search the user in the local database 
    $query = "SELECT name 
            FROM ".$CFG->prefix."user
            WHERE name='$user'";
    $result = mysql_query($query) 
            or die(mysql_error());

    if(mysql_num_rows($result) > 0){
        $in_system = true;
    } else {
            
        $ldap['host'] = $CFG->LDAPhost;
        $ldap['port'] = (int)$CFG->LDAPport;
        $ldap['dn']   = $CFG->LDAPdn;
        $filter = "(uid=$user)";
        
        // connect to ldap
        $ldap['conn'] = ldap_connect( $ldap['host'], $ldap['port'] );
        ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);	
        
        //does the user exists in the LDAP?
        $sr = ldap_search($ldap['conn'], $ldap['dn'], $filter);
        $info = ldap_first_entry($ldap['conn'], $sr);			
        
        if ($info) {
            $in_system = true;
            $msg = null;
        }
    }
    return array($in_system,$msg);
}



?>
