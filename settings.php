<?php
	session_start();

    include_once("./config.php");

    if(isset ($_SESSION['s_username']))
    {
        //PHP includes
        include_once($CFG->dir."lang/lang.php");
        include_once($CFG->dir."lib/mainlib.php");
        //Username handle
        $username = $_SESSION['s_username'];
        $userid = get_user_id($CFG,$username);
        //HTML structure
        echo "<html>";
        echo "<head>";
        echo "<title>GLASS</title>";
        echo "<LINK href='".$CFG->url."Themes/clasic/style.css' rel='stylesheet' type='text/css'>";
        echo "<meta http-equiv='content-Type' content='text/html; charset=UTF8'/>";
        echo "<script language='JavaScript' type='text/javascript' src='".$CFG->url."lib/jslib.js'></script>";
        echo "<link href='http://fonts.googleapis.com/css?family=Orbitron:400,500' rel='stylesheet' type='text/css'>";    
        echo "</head>";
        echo "<body>";
        echo '<div id="glass_body"><div id="glass_header">'. _APLICATION_TITLE.'</div>';
        echo "<div id='glass_leftsection'>";
        echo "<div id='glass_logo'></div>";
        show_menu($CFG,$username);
        echo "</div>";
        echo "<div id='glass_rightsection'>";
      

		
		//////////////////////////////////////////			
		/////////////SETTINGS/////////////////////
        
        
        //get the username & his permisions
        $username = $_SESSION['s_username'];
        $my_permision = new permision($CFG,$username);
        
        //DDBB manager
        $dashboard_conexion = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
		mysql_select_db($CFG->dbname) 
			or die(mysql_error());
        $query = "SELECT dbcol 
			FROM ".$CFG->prefix."settings
			WHERE userId='$userid'";
		$result = mysql_query($query) 
				or die(mysql_error());
        $data = mysql_fetch_array($result);
        
        echo '<div class="carpet">';
        echo '<div class="carpet-style">';
        echo "<p class='glass-title'>"._BASIC_CONF."</p>";
        include ($CFG->dir."install/basic.html");
        echo '</div>';
        
        if($my_permision->get_addBBDDCAM()==1)
        {     
            echo '<div class="carpet-style">';
            echo "<p class='glass-title'>"._DATASET_TITLE."</p>";           
            include ($CFG->dir."install/newdb.html");
            if($_GET["c"]=="ok")echo "<script>document.getElementById('error').innerHTML = '"._CREATED."'</script>";
            echo '</div>';
        }
        if($my_permision->get_varSettings()==1)
        {    
            echo '<div class="carpet-style">';         
            echo "<p class='glass-title'>"._GLASS_DB_CONF."</p>";
            include ($CFG->dir."install/dbform.html");
            echo '</div>';       
        }
        echo "</div>";
			
        /////////////DASBOARD END/////////////////
        //////////////////////////////////////////
        
        echo "</div></div></body></html>";	
	}
	else
	{
        $goto = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: ".$CFG->url."index.html?goto=".$goto);
	}  
?>