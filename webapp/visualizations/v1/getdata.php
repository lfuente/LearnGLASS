<?php
session_start();
include_once("../../config.php");
ini_set("memory_limit","512M");



if(isset ($_SESSION['s_username']))
{
	$username = $_SESSION['s_username'];

	include_once($CFG->dir."lang/lang.php");
	include_once($CFG->dir."lib/mainlib.php");
	include_once("v1lib.php");

	$datasetId =  $_GET['CAMid'];
	$mMax  = $_GET['mMax'];
	$mMin  = $_GET['mMin'];
	$id  = $_GET['id'];
	$view  = $_GET['view'];
	$userid = get_user_id($CFG,$username);

	$permission = new permision($CFG, $username);

	$Akey   = array();
	$Avalue = array();
	$Agroup = array();
	$count = 1;
	//create an array with the filter values
	while($_GET["group$count"]!= null && $_GET["key$count"]!= null && $_GET["value$count"]!= null)
	{
		$Agroup[count($Agroup)]   = $_GET["group$count"];
		$Akey[count($Akey)]   = $_GET["key$count"];
		$Avalue[count($Avalue)] = $_GET["value$count"];
		$count++;
	}


	echo get_visualization1_Json_data($CFG,$userid,$view,$Agroup,$Akey,$Avalue,$mMax,$mMin,$id,$datasetId,$permission->get_userViewLevel());



}
else
{
	header("Location: ".$CFG->url."index.html");
}




?>