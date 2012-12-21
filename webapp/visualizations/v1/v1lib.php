<?php
function get_visualization1_Json_data($CFG,$username,$view,$Agroup,$Akey,$Avalue,$mMax,$mMin,$dbid,$datasetId,$userViewLevel)
{
	$time = 60*1000;//60seconds
	//Extract the name of the application is this exists
	$key   = array();
	$value = array();
	$group = array();
	for($i=0;$i<count($Akey);$i++){
		if($Akey[$i]=="event_type"){
			$app=$Avalue[$i];
		}
		else {
			$key[count($key)]=$Akey[$i];
			$value[count($value)]=$Avalue[$i];
			$group[count($group)]=$Agroup[$i];
		}
	}
	$array_data_key = array();
	$array_data_value = array();
	$array_group_value = array();
	
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
	$objDB = MongoConnect($db->username,$db->password,$db->database,$db->host);

	$jd1 = array();

	//Adds a filter with the user fullname in case he is a student
	//TODO: specific information (community) should be retrieved from the DB or a config file. Maybe the filter field, too (user_fullname)
	if($key[0]==null && $value[0]==null && $userViewLevel==1){
		$key[0]="user_fullname";
		$value[0]=$username;
		$group[0]="role";
		$key[1]="community";
		$value[1]="TutoresMoodle";
		$group[1]="role";
	}

	if($key[0]==null && $value[0]==null ){//all events
		$users = $objDB->roleuser->find()->timeout($time*1000);
		try {
			foreach ($users as $user) {
				if(isset($myArray[0][$user["_id"]["date"]][$user["_id"]["type"]])){
					$myArray[0][$user["_id"]["date"]][$user["_id"]["type"]]+=$user["value"]["count"];
				}
				else{
					$myArray[0][$user["_id"]["date"]][$user["_id"]["type"]]=$user["value"]["count"];
				}
			}
		}
		catch(MongoCursorTimeoutException $e) {
			echo "<br>Exception catched: ".$e;
		}
			
		$array_data_key[0] = "";
		$array_data_value[0] = "all";
		$array_group_value[0] = "all";
	}
	else {
		for($i=0;$i<count($key);$i++){//metadata events
			$parseUsers = array();
			$k = $key[$i];
			$v = $value[$i];
			$g = $group[$i];
			
			//role events
			//TODO I think this part of the code can be improved, making less mongo queries
			//echo $k, ' ###<br />';
			if(substr($k,0,4) == "user"){
				//Obtain the ID of the user so other queries can be made
				$userAtt = substr($k,5);
				$users = $objDB->users->find(array($userAtt=>$v))->timeout($time*1000);
				try {
					foreach ($users as $user) {
						$parseUsers[(string)$user["_id"]]= 1;
					}
				}
				catch(MongoCursorTimeoutException $e) {
					echo "<br>Exception catched: ".$e;
				}
					
				//Obtains all the events for all users TODO(needs improvement)
				$cursor = $objDB->roleuser->find()->timeout($time*1000);
				try {
					foreach ($cursor as $cu) {
						//Filters out the events from users different than the one we are dealing with
						if(isset($parseUsers[(string)$cu["_id"]["name"]])){
							if(isset($myArray[$i][$cu["_id"]["date"]][$cu["_id"]["type"]])){
								$myArray[$i][$cu["_id"]["date"]][$cu["_id"]["type"]]+=$cu["value"]["count"];
							}
							else{
								$myArray[$i][$cu["_id"]["date"]][$cu["_id"]["type"]]=$cu["value"]["count"];
							}
						}
					}
				}
				catch(MongoCursorTimeoutException $e) {
					echo "<br>Exception catched: ".$e;
				}
			}
			//metadata events
			else{
				//Find data
				$var = "role".$k;
				$users = $objDB->$var->find()->timeout($time);
				//Sort data
				try {
					foreach ($users as $user) {
						if($user["_id"]["name"]==$v){
							if(isset($myArray[$i][$user["_id"]["date"]][$user["_id"]["type"]])){
								$myArray[$i][$user["_id"]["date"]][$user["_id"]["type"]]+=$user["value"]["count"];
							}
							else{
								$myArray[$i][$user["_id"]["date"]][$user["_id"]["type"]]=$user["value"]["count"];
							}
						}
					}
				}
				catch(MongoCursorTimeoutException $e) {
					echo "<br>Exception catched: ".$e;
				}
			}
			$array_data_key[$i] = $k;
			$array_data_value[$i] = $v;
			$array_group_value[$i] = $g;
		}
	}


	//create the data with visualization require format
	//firstly the settings of the view
	if($app==null) $app = "all";
	$jdata[0]= array("mMax" => $mMax, "mMin" => $mMin, "id" => $dbid, "view" => $view, "app" => $app);
	//secondly the diferet point of the view
	for($j=0;$j<count($myArray);$j++)
	{
		 
		//TODO dodgy line, sorts the array sometimes
		uksort($myArray[$j],"usort_handle");
		$obj_data = json_decode($jd1[$j]);
		$data_key_name = $array_data_key[$j];
		$data_vale_name = $array_data_value[$j];
		$data_group_name = $array_group_value[$j];
			
		//TODO: get the right number of users when group or community filter is used.
		//Right now it doesn't calculate the mean if the filter is for the user collection, it shows an absolute number of events even if the group filter is selected.
		//When a commuinity is chosen as filter it uses all the users, not only those on the community.
		//This is why the cache or MapReduce should be visualization dependant, and not application dependant.
			
		if (substr($data_key_name,0,4) == 'user')
		{
			$n_users = 1;
			// else if ($data_key_name == 'community')
			//			$n_users = db.users.find({community:$value}).count();
		}
		else
		{
			$n_users = $objDB->users->count();
			//$n_users = 186; //fake conf
		}
			
			
		//event-time visualization
		if($view==1)
		{
			$data_value = array();
			$data_date = array();
			//with all event type
			if($app=="all"){
				foreach($myArray[$j] as $k1=>$v1){
					$events_number = 0;
					foreach($myArray[$j][$k1] as $k2=>$v2){
						$events_number += $v2;
					}
					$data_date[count($data_date)] = str_replace("-","/",$k1);
					$data_value[count($data_value)] = $events_number/$n_users;
				}
			}
			//only with the selected event type
			else{
				foreach($myArray[$j] as $k1=>$v1){
					if(isset($myArray[$j][$k1][$app])){
						$events_number = $myArray[$j][$k1][$app];
					}
					else{
						$events_number = 0;
					}
					$data_date[count($data_date)] = str_replace("-","/",$k1);
					$data_value[count($data_value)] = $events_number/$n_users;
				}
			}
			$jdata[$j+1] = array("dgroup" => $data_group_name,"dkey" => $data_key_name, "dvalue" => $data_vale_name,"value" => $data_value, "date" => $data_date);

		}
		//bar visualization
		else
		{
			$aux_array = array();
			$mMinD = gmdate($mMin/1000);
			$mMaxD = gmdate($mMax/1000);
			foreach($myArray[$j] as $k1=>$v1){
				$idate = strtotime($k1." UTC");
				if($mMinD <= $idate && $idate<=$mMaxD){
					foreach($myArray[$j][$k1] as $k2=>$v2){
						if(isset($aux_array[$k2])){
							$aux_array[$k2] += $v2;
						}
						else{
							$aux_array[$k2] = $v2;
						}
					}
				}
			}
			$bardataname = array();
			$bardataresult = array();
			arsort($aux_array);
			foreach($aux_array as $k1 => $v1){
				$bardataname[count($bardataname)] = $k1;
				$bardataresult[count($bardataresult)] = $v1/$n_users;
			}
			$jdata[$j+1] = array("dgroup" => $data_group_name,"dkey" => $data_key_name, "dvalue" => $data_vale_name,"bdn" => $bardataname, "bdv" => $bardataresult);
		}
	}
	//Finally convert to Json format
	$jsondata = json_encode($jdata);
	return $jsondata;
}

function usort_handle($x, $y){
	$x = strtotime($x);
	$y = strtotime($y);
	if ( $x == $y )return 0;
	else if ( $x > $y ) return 1;
	else return -1;
}

?>