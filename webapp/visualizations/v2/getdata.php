<?php

session_start();
include_once("../../config.php");
$time = 60;
set_time_limit($time);
ini_set("memory_limit","512M");

if(isset ($_SESSION['s_username']))
{
	$username = $_SESSION['s_username'];

	include_once($CFG->dir."lib/mainlib.php");
	include_once("v2lib.php");

	$datasetId =  $_POST['CAMid'];
	$entityType =  $_POST['et'];
	$Fgroup =  $_POST['fg'];
	$userid = get_user_id($CFG,$username);

	//set null
	if($datasetId=="null")$datasetId=null;
	if($entityType=="null")$entityType=null;
	if($Fgroup=="null")$Fgroup=null;

	//Fetch $datasetId if it si null
	if($datasetId==null){
		$conexion = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die(mysql_error());
		mysql_select_db($CFG->dbname) or die(mysql_error());
		//get the dashboard values
		$query = "SELECT *
		FROM ".$CFG->prefix."settings
		WHERE userId='$userid'";
		$result = mysql_query($query) or die(mysql_error());
		$data = mysql_fetch_array($result)or die(mysql_error());
		mysql_close($conexion);
		$datasetId = $data['ddbbId'];
	}

	//Default filter
	if($entityType==null || $Fgroup==null || strtolower($entityType)=="none"){
		$entityType="user_name";
		$Fgroup = "role";
	}

	////////////////////
	//Get data       //
	//////////////////
	//Get DDBB vars
	$conexion = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die(mysql_error());
	mysql_select_db($CFG->dbname) or die(mysql_error());
	//get the dashboard values
	$query = "SELECT *
	FROM ".$CFG->prefix."ddbb
	WHERE id='$datasetId'";
	$result = mysql_query($query) or die(mysql_error());
	$data = mysql_fetch_array($result)or die(mysql_error());
	mysql_close($conexion);
	$db->host = $data["host"];
	$db->username = $data["user"];
	$db->password = $data["pass"];
	$db->database = $data["name"];
	$db->entityType = $entityType;
	$db->time = $time;
	//Get data form events
	if(substr($entityType,0,4) == "user"){
		$js = get_event_from_users($db);
	}
	//Get data from users
	else{
		$js =  get_event_from_events($db);
	}

	echo json_encode($js);
}
else
{
	header("Location: ".$CFG->url."index.html");
}







?>