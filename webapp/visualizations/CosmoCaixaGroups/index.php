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
	
	// ##### CHART ##### //
	
	/*
	 * This fitlers the documents whose content.type is 'qr-scanned' or 'qr-asked'.
	 * If the type is 'qr-scanned', it also checks if the scanned code is the expected one.
	 * Then, it saves the time of each of those events.
	 */
	$map = new MongoCode("
		function() {
			if (this.doc.content.type == 'qr-scanned' && this.doc.content.expected_code == this.doc.content.scanned_code){
				emit({'hint':this.doc.content.expected_code, 'action':'start', 'team':this.doc.team, 'school':this.doc.school}, {time:this.doc.time});
			}
			else if (this.doc.content.type == 'qr-asked'){
				emit({'hint':this.doc.content.previous_code, 'action':'end', 'team':this.doc.team, 'school':this.doc.school}, {time:this.doc.time});
			}
		}
	");
	
	/*
	 * This reduces the time attribute in case there is more than one event for the same hint.
	 * If the type is 'qr-asked' it stays with the latest one, as it is the time at which the previous module was finished.
	 * If the type is 'qr-scanned' it stays with the earliest one, as it is the time at which the current module was started.
	 */
	$reduce = new MongoCode("
		function(key, values) {
			var result = values[0];
			for (var i = 1; i<values.length; i++){
				if(key.action == 'start'){
					if(values[i].time < result.time){
						result.time = values[i].time;
					}
				}
				else{
					if(values[i].time > result.time){
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
	
	//Get all the reduced data
	$cursor = $db->milestones->find();
	
	//This will hold all the information needed by the view.
	$info = array();
	
	//Generates a structure of arrays
	foreach($cursor as $doc){
		$school	= $doc['_id']['school'];
		$team	= $doc['_id']['team'];
		$module	= $doc['_id']['hint'];
		$action	= $doc['_id']['action'];
		$time	= $doc['value']['time'];
		
		$info[$school][$team]['chart'][$module][$action] = $time;
	}
	
	
	// ##### REPORTS and IMAGES ##### //
	
	/*
	 * This gets the messages a team left at a module.
	 */
	$map = new MongoCode("
		function() {
			if (this.doc.type == 'log'){
				emit({'module':this.doc.last_code, 'team':this.doc.team, 'school':this.doc.school}, {'report':this.doc.content.message});
			}
		}
	");
	
	/*
	 * Concatenates all the reports into a single one.
	 */
	$reduce = new MongoCode("
		function(key, values) {
			var result = values[0];
			for (var i = 1; i<values.length; i++){
				result.report += '<br><br>' + values[i].report;
			}
			return result;
		}
	");
	
	//Parameters  for mapreduce
	$params = array(
		"mapreduce"=>"events",
		"map"=>$map,
		"reduce"=>$reduce,
		"out"=>"reports",
	);
	
	//Execute mapreduce
	$results = $db->command($params);
	
	//Get all the reports
	$cursor = $db->reports->find();

	//Generates a structure of arrays
	foreach($cursor as $doc){
		$school	= $doc['_id']['school'];
		$team	= $doc['_id']['team'];
		$module	= $doc['_id']['module'];
		$report	= $doc['value']['report'];
	
		$info[$school][$team]['reports'][$module] = $report;
		
	}
	
	// ##### IMAGES ##### //
	
	//List of profile names, so they can be searched in an array
	$profiles = array('leader', 'rastreator', 'handyman', 'reporter', 'photographer');
	
	//For each team of each school
	foreach ($info as $school=>$teams){
		foreach ($teams as $team=>$data){
			
			//This is where the images should be saved: <LearnGLASS_root>/webapp/ignored/<school>/<team>/
			//The name of the folders must match exactly the name of the school and team in the logs
			$path = '../../ignored/'.$school.'/'.$team.'/';
			
			//For each of the profiles
			foreach ($profiles as $profile){
				//Get all the profile pictures taken
				$cursor = $db->events->find(array(  'doc.content.type'=>'user-camera-pic-taken', 'doc.content.profile'=>$profile, 'doc.school'=>$school, 'doc.team'=>$team  ));
				//Sort them so the newest is first
				$cursor->sort(array(  'doc.time' => -1  ));
			
				//If there are results
				if($cursor->hasNext()){
					//Get the first one (newest or last)
					$doc = $cursor->getNext();
					$info[$school][$team]['images'][$profile] = $path.basename($doc['doc']['content']['pic']);
				}
			}
			
			//Get all the team pictures
			$cursor = $db->events->find(array(  'doc.content.type'=>'camera-pic-taken', 'doc.content.page'=>'team-camera', 'doc.school'=>$school, 'doc.team'=>$team ));
			//Sort them so the newest goes first
			$cursor->sort(array(  'doc.time' => -1  ));
			
			//If there are results
			if($cursor->hasNext()){
				//Get the first one (newest or last)
				$doc = $cursor->getNext();
				$info[$school][$team]['images']['team'] = $path.basename($doc['doc']['content']['pic']);
			}
			
			//Do the same for each of the modules visited
			foreach ($data['reports'] as $module=>$x){
				$cursor = $db->events->find(array(  'doc.content.type'=>'camera-pic-taken', 'doc.last_code'=>$module, 'doc.school'=>$school, 'doc.team'=>$team ));
				$cursor->sort(array(  'doc.time' => -1  ));
				
				if($cursor->hasNext()){
					$doc = $cursor->getNext();
					$info[$school][$team]['images'][$module] = $path.basename($doc['doc']['content']['pic']);
				}
			}
			
		}
	}
	
	
	
	//Load the view
	include('view.php');
}
else {
	$goto = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	header("Location: ".$CFG->url."index.html?goto=".$goto);
	header("Location: http://".$_SERVER['HTTP_HOST']."/LearnGLASS/index.html?goto=".$goto);
}
?>
