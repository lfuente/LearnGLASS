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
	
	// ##### TIME CHART ##### //
	
	/*
	 * This fitlers the documents whose content.type is 'qr-scanned' or 'qr-asked'.
	 * If the type is 'qr-scanned', it also checks if the scanned code is the expected one.
	 * Then, it saves the time of each of those events.
	 */
	$map = new MongoCode("
		function() {
			if (this.content.type == 'qr-scanned' && this.content.expected_code == this.content.scanned_code){
				emit({'hint':this.content.expected_code, 'action':'start', 'team':this.team, 'school':this.school}, {time:this.time});
			}
			else if (this.content.type == 'qr-asked'){
				emit({'hint':this.content.previous_code, 'action':'end', 'team':this.team, 'school':this.school}, {time:this.time});
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
	
	/*
	 * Emits the starting time as negative so it can be substracted from the ending time.
	 */
	$map = new MongoCode("
		function() {
			if (this._id.action == 'start'){
				emit({'module':this._id.hint, 'team':this._id.team, 'school':this._id.school}, {'time':-this.value.time, 'count':1});
			}
			else{
				emit({'module':this._id.hint, 'team':this._id.team, 'school':this._id.school}, {'time':this.value.time, 'count':1});
			}
		}
	");
	
	/*
	 * Sums the ending and negative starting time at a module to get the elapsed time.
	 */
	$reduce = new MongoCode("
		function(key, values){
			var result = values[0];
			for (var i = 1; i<values.length; i++){
				result.time += values[i].time;
				result.count += values[i].count;
			}
			return result;
		}
	");
	
	//Parameters  for mapreduce
	$params = array(
		"mapreduce"=>"milestones",
		"map"=>$map,
		"reduce"=>$reduce,
		"out"=>"elapsed_times",
	);
	
	//Execute mapreduce
	$results = $db->command($params);
	
	
	/*
	 * Emits the elapsed time for each team and a count of one. This count can be summed in the reduce to obtain the number of groups that
	 * visited the module and, thus, calculate the mean elapsed time.
	 */
	$map = new MongoCode("
		function() {
			if(this.value.count == 2){
				emit({'module':this._id.module}, {'time':this.value.time, 'count':1});
			}
		}
	");
	
	/*
	 * Sums the elapsed times and count to be able to calculate the mean.
	 */
	$reduce = new MongoCode("
		function(key, values){
			var result = values[0];
			for (var i = 1; i<values.length; i++){
				result.time += values[i].time;
				result.count += values[i].count;
			}
			return result;
		}
	");
	
	//Parameters  for mapreduce
	$params = array(
		"mapreduce"=>"elapsed_times",
		"map"=>$map,
		"reduce"=>$reduce,
		"out"=>"mean_elapsed_times",
	);
	
	//Execute mapreduce
	$results = $db->command($params);
	
	//Get all the mean elapsed times
	$cursor = $db->mean_elapsed_times->find();
	
	//This will hold all the elapsed times information
	$infotimes = array();
	
	//Generates a structure of arrays
	foreach($cursor as $doc){
		$module	= $doc['_id']['module'];
		$time	= $doc['value']['time'];
		$count	= $doc['value']['count'];
	
		$infotimes[$module] = $time/$count;
	}
	
	
	// ##### REPORTS ##### //
	
	/*
	 * This gets the messages a team left at a module.
	 */
	$map = new MongoCode("
		function() {
			if (this.type == 'log'){
				emit({'module':this.content.page, 'team':this.team, 'school':this.school}, {'report':this.content.message});
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
	
	//This will hold all the reports information
	$inforeports = array();
	
	//Generates a structure of arrays
	foreach($cursor as $doc){
		$school	= $doc['_id']['school'];
		$team	= $doc['_id']['team'];
		$module	= $doc['_id']['module'];
		$report	= $doc['value']['report'];
	
		$inforeports[$module][$school][$team] = $report;
	}
	
	include('view.php');
}
else {
	$goto = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	header("Location: ".$CFG->url."index.html?goto=".$goto);
	header("Location: http://".$_SERVER['HTTP_HOST']."/LearnGLASS/index.html?goto=".$goto);
}
?>