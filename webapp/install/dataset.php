<?php

session_start();

include_once("../config.php");
set_time_limit(0);
ini_set("memory_limit","256M");

if(isset ($_SESSION['s_username']))
{
	include_once($CFG->dir."lib/mainlib.php");
	include_once($CFG->dir."lang/lang.php");

	$username = $_SESSION['s_username'];
	$userId = get_user_id($CFG,$username);
	$host = $_POST["host"];
	$name = $_POST["name"];
	$user = $_POST["user"];
	$pass = $_POST["pass"];
	$des = $_POST["des"];
	$op = $_POST["op"];
	$datestart = time();

	//Select database process
	if($op=="select" && $name!=null)
	{
		//mysql conection
		$conexion = mysql_connect ($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die("error: ".mysql_error());
		mysql_select_db($CFG->dbname)or die("error: ".mysql_error());
		$query = "SELECT * FROM ".$CFG->prefix."ddbb WHERE name='".$name."'";
		$result = mysql_query($query)or die("error: ".mysql_error());
		$data = mysql_fetch_array($result);
		$dbid = $data['id'];
		//Changes the DB for the current user only.
// 		$query = "UPDATE ".$CFG->prefix."settings SET ddbbId='".$dbid."' WHERE userId='".$userId."'" ;
		//Changes de DB for all users, not just the admin.
		$query = "UPDATE ".$CFG->prefix."settings SET ddbbId='".$dbid."'";
		$result = mysql_query($query) or die("error: ".mysql_error());
		mysql_close($conexion);
	}
	//drop database process
	if($op=="drop" && $name!=null)
	{
		//mysql conection
		$conexion = mysql_connect ($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die("error: ".mysql_error());
		mysql_select_db($CFG->dbname)or die("error: ".mysql_error());
		//fetch the module id
		$query = "SELECT * FROM ".$CFG->prefix."ddbb WHERE name='".$name."'";
		$result = mysql_query($query) or die(mysql_error());
		$data = mysql_fetch_array($result);
		$id = $data["id"];
		$name = $data["name"];
		//Delete the data that are in the dashboard
		$query = "DELETE FROM ".$CFG->prefix."dashboard WHERE bdCAMid='".$id."'";
		$result = mysql_query($query) or die("error: ".mysql_error());
		//Delete the data that are in my views
		$query = "DELETE FROM ".$CFG->prefix."myview WHERE bdCAMid='".$id."'";
		$result = mysql_query($query) or die("error: ".mysql_error());
		//Delete the CAM database
		$query = "DELETE FROM ".$CFG->prefix."ddbb WHERE id='".$id."'";
		$result = mysql_query($query) or die("error: ".mysql_error());
		//fetch some CAM id
		$query = "SELECT * FROM ".$CFG->prefix."ddbb";
		$result = mysql_query($query) or die(mysql_error());
		$data = mysql_fetch_array($result);
		
		//If there are other databases, change to the first one
		if(mysql_num_rows($result)>0){
			$new_id = $data["id"];
			$query = "UPDATE ".$CFG->prefix."settings
			SET ddbbId='".$new_id."' WHERE ddbbId='".$id."'" ;
		}
		mysql_close($conexion);
	}
	//Add new database process
	if($host!=null && $name!=null && $user!=null && $pass!=null && $des!=null && $op=="installing"){
		$objDB = MongoConnect($user,$pass,$name,$host);
		//Fetch each filter in events object
		$events = $objDB->events->find();
		$AuxArray = array();
		foreach ($events as $event) {
			foreach ($event as $k=>$v) {
				if($k!="name" && $k!="datetime" && $k!="user" && $k!="_id"){
					$AuxArray[$k]=0;
				}
			}
		}
		foreach($AuxArray as $k=>$v){
			$EventsArray[count($EventsArray)] = $k;
			$cursor = $objDB->command(array("distinct"=>"events","key"=>$k));
			$EventsArrayNum[count($EventsArrayNum)] = count($cursor['values']);
		}
		//Fetch each filter in users object
		$AuxArray = array();
		$users = $objDB->users->find();
		foreach ($users as $user) {
			foreach ($user as $k=>$v) {
				if($k!="_id" && $k!="name"){
					$AuxArray[$k]=0;
				}
			}
		}
		foreach($AuxArray as $k=>$v){
			$UsersArray[count($UsersArray)] = $k;
			$cursor = $objDB->command(array("distinct"=>"users","key"=>$k));
			$UsersArrayNum[count($UsersArrayNum)] = count($cursor['values']);
		}

		$timeleft = (INT)(time() - $datestart);
		$memoryused[0] = round(memory_get_usage() / (1024*1024),1);
		$memoryused[1] = round(memory_get_usage(1) / (1024*1024),1);
		$jdata[0] = $op;
		$jdata[1] = array(  "e"=>$EventsArray,
				"en"=>$EventsArrayNum,
				"u"=>$UsersArray,
				"un"=>$UsersArrayNum);
		echo json_encode($jdata);//trasform in json format
		exit();//finish script process
	}
	//Add new database process
	if($host!=null && $name!=null && $user!=null && $pass!=null && $des!=null && $op=="created"){
		//////////////////////////////////////////////////
		///ADD TO THE GLASS DATABASE
		$conexion = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die("error: ".mysql_error());
		mysql_select_db($CFG->dbname) or die("error: ".mysql_error());
		//Search matchs
		$query = "SELECT * FROM ".$CFG->prefix."ddbb WHERE name='".$name."'" ;
		$result = mysql_query($query) or die("error: ".mysql_error());
		//check if there already exists a database with the same name
		//if no other database has the same name, add it, else, throw error
		if(mysql_num_rows($result)==0){
			$query = "INSERT INTO ".$CFG->prefix."ddbb (host,name,user,pass,description,filters)
			VALUES ('".$host."','".$name."','".$user."','".$pass."','".$des."','".$_POST["values"]."')";
			$result = mysql_query($query) or die("error: ".mysql_error());
		}
		else{
			//error message
			echo "error: "._DDBB_EXIST;
			mysql_close($conexion);
			exit();
		}
		mysql_close($conexion);

		//////////////////////////////////////////////////
		///MONGO CONNECTION
		$objDB = MongoConnect($user,$pass,$name,$host);
		MongoCursor::$timeout = -1; // if the process is too long

		/////////////////////////////////////////////////
		///FILTER CREATION
		//Delete filters if exists
		$collection = $objDB->filters;
		$objDB->dropCollection('filters');
		$collection = $objDB->filters;

		//event type filter creation
		$des="Captured event type";
		$f_values = array();
		$f_values[0] = "None";
		$cursor = $objDB->command(array("distinct"=>"events","key"=>"name"));
		$f_values = array_merge($f_values, $cursor['values']);
		$toinsert = array("group"=>"event_type","name"=>"event_type","des"=>$des,"values"=>$f_values);
		$collection->insert($toinsert);

		//user name filter creation
		$des="Name of the user who made the event";
		$f_values = array();
		$f_values[0] = "None";
		$cursor = $objDB->command(array("distinct"=>"users","key"=>"name"));
		$f_values = array_merge($f_values,$cursor['values']);
		$toinsert = array("group"=>"role","name"=>"user_name","des"=>$des,"values"=>$f_values);
		$collection->insert($toinsert);
		$keysArray["user"]=0;//for the mapreduce

		//Selected filter creation
		$values = json_decode(str_replace("\\","",$_POST["values"]));
		foreach($values->filters as $index=>$k){
			//USER FILTER
			if(substr($k,0,4) == "user"){
				$des = $values->description[$index];
				$k = substr($k,5);
				$f_values = array();
				$cursor = $objDB->command(array("distinct"=>"users","key"=>$k));
				$f_values[0] = "None";
				$f_values = array_merge($f_values,$cursor['values']);
				$toinsert = array("group"=>"role","name"=>"user_".$k,"des"=>$des,"values"=>$f_values);
				$collection->insert($toinsert);
			}
			//EVENT FILTERS
			else{
				$des = $values->description[$index];
				$f_values = array();
				$cursor = $objDB->command(array("distinct"=>"events","key"=>$k));
				$f_values[0] = "None";
				$f_values = array_merge($f_values,$cursor['values']);
				$toinsert = array("group"=>"role","name"=>$k,"des"=>$des,"values"=>$f_values);
				$collection->insert($toinsert);
				$keysArray[$k]=0;   //for the mapreduce
			}
		}

		//////////////////////////////////////////////////
		///MAP-REDUCE
		foreach ($keysArray as $k=>$v) {
			if($k!="_id" && $k!="name" && $k!="datetime"){
				if($k=="user"){
					//Users MAP-REDUCE
					$strMap = '
					function() {
					var myDate = new Date(this.datetime);
					STRmyDate = (myDate.getFullYear())+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate();
					for (var user in this.user) {
					emit({name:this.user[user]._id,date:STRmyDate,type:this.name},{count:1});
				}
				}
				';
					$strReduce = '
					function(k,v){
					var sum = 0;
					for (var i=0;i<v.length;i++){
					sum += v[i].count; ;
				}
				return {count:sum};
				}
				';
					$objMapFunc = new MongoCode($strMap);
					$objReduceFunc = new MongoCode($strReduce);
					$objDB->command(array(
							'mapreduce' => 'events',
							'map' => $objMapFunc,
							'reduce' => $objReduceFunc,
							'out' => "roleuser"
					));
				}
				else{
					$strMap = '
					function() {
					var myDate = new Date(this.datetime);
					STRmyDate = (myDate.getFullYear())+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate();
					if(this.'.$k.'){
					emit({name:this.'.$k.',date:STRmyDate,type:this.name},{count:1});
				}
				}
				';
					$strReduce = '
					function(k,v){
					var sum = 0;
					for (var i=0;i<v.length;i++){
					sum += v[i].count; ;
				}
				return {count:sum};
				}
				';
					$objMapFunc = new MongoCode($strMap);
					$objReduceFunc = new MongoCode($strReduce);
					$objDB->command(array(
							'mapreduce' => 'events',
							'map' => $objMapFunc,
							'reduce' => $objReduceFunc,
							'out' => "role".$k
					));
				}
			}
		}
	}


	// DB connect
	$conexion = mysql_connect ($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die("error: ".mysql_error());
	mysql_select_db($CFG->dbname) or die("error: ".mysql_error());
	//Slect the database the user is currently using
	$query = "SELECT ".$CFG->prefix."ddbb.name
	FROM ".$CFG->prefix."settings, ".$CFG->prefix."ddbb
	WHERE ".$CFG->prefix."settings.ddbbId=".$CFG->prefix."ddbb.id
	AND ".$CFG->prefix."settings.userId='".$userId."'";
	$result = mysql_query($query) or die("error: ".mysql_error());
	$data = mysql_fetch_array($result);
	$selname = $data["name"];

	//Search the CAM database
	$query = "SELECT * FROM ".$CFG->prefix."ddbb";
	$result = mysql_query($query) or die("error: ".mysql_error());
	$jdata[0] = $op;
	$jdata[1] = $selname;
	$i=1;
	while ($data = mysql_fetch_array($result)){
		$jdata[count($jdata)] = $data['name'];
		//$jdata[count($jdata)] = $data['description'];
	}

	mysql_close($conexion);
	echo json_encode($jdata);//trasform in json format


}
else
{
	header("Location: ".$CFG->url."index.html");
}
?>