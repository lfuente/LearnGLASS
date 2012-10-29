<?php
session_start();
include("../../config.php");

if( isset ($_SESSION['s_username']) ) {
	include_once($CFG->dir."lib/mainlib.php");
	$username = $_SESSION['s_username'];
	$userid = get_user_id($CFG,$username);
	
	$connection = mysql_connect($CFG->dbhost, $CFG->dbuser , $CFG->dbpass)
		or exit(mysql_error());
	mysql_select_db($CFG->dbname)
		or exit(mysql_error());
	
	$table_settings=$CFG->prefix."settings";
	$table_db=$CFG->prefix."ddbb";
	
	$query = "
		SELECT host, name, user, pass
		FROM $table_settings, $table_db
		WHERE (
			$table_settings.userId=$userid
			AND
			$table_settings.ddbbId = $table_db.id
		)
	";
	
	$result = mysql_query($query)
		or exit(mysql_error());
	$data = mysql_fetch_array($result)
		or exit(mysql_error());
	mysql_close($connection);
	
	$dbinfo->host = $data["host"];
	$dbinfo->username = $data["user"];
	$dbinfo->password = $data["pass"];
	$dbinfo->database = $data["name"];
	$db = MongoConnect($dbinfo->username,$dbinfo->password,$dbinfo->database,$dbinfo->host);
	
	/*
	 * This fitlers the documents that whose content.type is 'qr-scanned' or 'qr-asked'.
	 * If the type is 'qr-scanned', it also checks if the scanned code is the expected one.
	 * Then, it saves the time of each of those events.
	 */
	$map = new MongoCode("
		function() {
			if (this.content.type == 'qr-scanned' && this.content.expected_code == this.content.scanned_code){
				emit({'hint':this.content.expected_code, 'action':'arrived', 'team':this.team}, {time:this.time});
			}
			else if (this.content.type == 'qr-asked'){
				emit({'hint':this.content.expected_code, 'action':'search', 'team':this.team}, {time:this.time});
			}
		}
	");
	
	/*
	 * This reduces the time attribute in case there are more than one event for the same hint.
	 * If the type is 'qr-asked' it stays with the latest one.
	 * If the type is 'qr-scanned' it stays with the earliest one.
	 */
	$reduce = new MongoCode("
		function(key, values) {
			var result = values[0];
			for (var i = 1; i<values.length; i++){
				if(key.action == 'search'){
					if(values[i].time > result.time){
						result.time = values[i].time;
					}
				}
				else{
					if(values[i].time < result.time){
						result.time = values[i].time;
					}
				}
			}
			return result;
		}
	");
	
	//Parameters  for mapreduce
	$params = array(
		"mapreduce"=>"events",
		"map"=>$map,
		"reduce"=>$reduce,
		"out"=>"milestones",
	);
	
	//Execute mapreduce
	$results = $db->command($params);
	
	//Crappy code for CosmoCaixa scenario, please, change in the future, once logged events permit unordered paths
	//Constructs several data series for timeline graph
	$cchints = array();
	
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hint1', '_id.action'=>'search')  );
	$cchints['start']=json_encode(array(array($tmp['value']['time'],0)));
	
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hint1', '_id.action'=>'arrived')  );
	$cchints['module1'][0]=array($tmp['value']['time'],1);
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hint2', '_id.action'=>'search')  );
	$cchints['module1'][1]=array($tmp['value']['time'],1);
	$cchints['module1']=json_encode($cchints['module1']);
	
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hint2', '_id.action'=>'arrived')  );
	$cchints['module2'][0]=array($tmp['value']['time'],1);
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hint3', '_id.action'=>'search')  );
	$cchints['module2'][1]=array($tmp['value']['time'],1);
	$cchints['module2']=json_encode($cchints['module2']);
	
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hint3', '_id.action'=>'arrived')  );
	$cchints['module3'][0]=array($tmp['value']['time'],1);
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hintf', '_id.action'=>'search')  );
	$cchints['module3'][1]=array($tmp['value']['time'],1);
	$cchints['module3']=json_encode($cchints['module3']);
	
	$tmp=$db->milestones->findOne(  array('_id.hint'=>'hintf', '_id.action'=>'arrived')  );
	$cchints['end']=json_encode(array(array($tmp['value']['time'],0)));
	//end of crappy code
	
	include('index.html');
}
else {
	$goto = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	header("Location: ".$CFG->url."index.html?goto=".$goto);
	header("Location: http://".$_SERVER['HTTP_HOST']."/LearnGLASS/index.html?goto=".$goto);
}
?>