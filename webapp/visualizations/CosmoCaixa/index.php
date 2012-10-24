<?php
session_start();
include("../../config.php");

if( isset ($_SESSION['s_username']) ) {
	include_once($CFG->dir."lib/mainlib.php");
	$username = $_SESSION['s_username'];
	$userid = get_user_id($CFG,$username);
	
	$conexion = mysql_connect($CFG->dbhost, $CFG->dbuser , $CFG->dbpass)
		or exit(mysql_error());
	mysql_select_db($CFG->dbname)
		or exit(mysql_error());
	
	$table_settings=$CFG->prefix."settings";
	$table_db=$CFG->prefix."ddbb";
	
	$query = "
		SELECT host, name, user, pass
		FROM $table_settings, $table_db
		WHERE (
			$table_settings.userId=1
			AND
			$table_settings.ddbbId = $table_db.id
		)
	";
	
	$result = mysql_query($query)
		or exit(mysql_error());
	$data = mysql_fetch_array($result)
		or exit(mysql_error());
	mysql_close($conexion);
	
	$dbinfo->host = $data["host"];
	$dbinfo->username = $data["user"];
	$dbinfo->password = $data["pass"];
	$dbinfo->database = $data["name"];
	$db = MongoConnect($dbinfo->username,$dbinfo->password,$dbinfo->database,$dbinfo->host);
	
	$map = new MongoCode("
		function() {
			emit({'content.expected_code':this.content.scanned_code, 'content.type':this.content.type}, {time:this.time});
		}
	");
	
	$reduce = new MongoCode("
		function(key, values) {
			var result = values[0];
			for (var i = 1; i<values.length; i++){
				if(values[i].time > result.time){
					result.time = values[i].time;
				}
			}
			return result;
		}
	");
	
	$query = array(
		//"content.type"=>"qr-scanned",
		'content.type'=>array('$in'=>array('qr-scanned','qr-asked')),
	);
	
	$params = array(
		"mapreduce"=>"events",
		"map"=>$map,
		"reduce"=>$reduce,
		"query"=>$query,
		"out"=>"milestones",
	);
	
	$results = $db->command($params);

	$cursor = $db->milestones->find();
	
	foreach ($cursor as $doc) {
		echo '-- ', var_dump($doc), '<br><br>';
	}
	
	
	$cursor = $db->events->find(  array('content.type'=>'qr-asked','content.expected_code'=>'hint1')  );
	
	try {
		foreach ($cursor as $document){
			echo var_dump($document), '<br><br>';
		}
	}
	catch(MongoCursorTimeoutException $e) {
		echo "<br>Exception catched: ".$e;
	}
	
	$cursor = $db->events->find(  array('content.type'=>'qr-scanned','content.expected_code'=>'hint1','content.scanned_code'=>'hint1')  );
	try {
		foreach ($cursor as $document){
			
		}
	}
	catch(MongoCursorTimeoutException $e) {
		echo "<br>Exception catched: ".$e;
	}

	
	
 	include('index.html');
}
else {
	$goto = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	header("Location: ".$CFG->url."index.html?goto=".$goto);
	header("Location: http://".$_SERVER['HTTP_HOST']."/LearnGLASS/index.html?goto=".$goto);
}
?>