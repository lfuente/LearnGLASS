<?php

session_start();
include("../config.php");

if(isset ($_SESSION['s_username']))
{
	//Main includes
	include($CFG->dir."lang/lang.php");
	include($CFG->dir."lib/mainlib.php");

	$value = $_POST['value'];
	$role = $_POST['role'];
	$CAMdb = $_POST['CAMdb'];

	//manage the permits
	$username = $_SESSION['s_username'];
	$my_permision = new permision($CFG,$username);
	if($my_permision->get_viewSuggest()==1){
		if($value!="none"){
			//Look for the CAM database parameters
			$linker = mysql_connect ($CFG->dbhost, $CFG->dbuser , $CFG->dbpass )
			or die(mysql_error());
			mysql_select_db($CFG->dbname)
			or die(mysql_error());

			//serch the user, pass, ... of the CAM database $CAMdb
			$query = "SELECT *
			FROM ".$CFG->prefix."bbdd
			WHERE id='".$CAMdb."'";
			 
			$result = mysql_query($query)
			or die(mysql_error());

			$data = mysql_fetch_array($result)
			or die(mysql_error());

			mysql_close($linker);
			 
			//get de data from the cam database
			$linker = mysql_connect ($data['host'], $data['user'] , $data['pass'] )
			or die(mysql_error());
			mysql_select_db($data['name'])
			or die(mysql_error());

			$query = 'SELECT `key`
			FROM relatedentitymetadata
			GROUP BY `key`';

			$result = mysql_query($query)
			or die(mysql_error());
			$i = 0;
			while($data = mysql_fetch_array($result))
			{
				$query = "SELECT relatedentitymetadata.`value`
				FROM relatedentitymetadata, relatedentity, eventrelatedentity
				WHERE eventrelatedentity.role='".$role."'
				AND eventrelatedentity.relatedentityid=relatedentity.id
				AND relatedentity.name = '".$value."'
				AND relatedentityfk = relatedentity.id
				AND relatedentitymetadata.key='".$data['key']."'
				GROUP BY value";
				$subresult = mysql_query($query)
				or die(mysql_error());

				while($subdata = mysql_fetch_array($subresult))
				{
					$jdata[$i] = array("text" => "none", "group" => "metadata","key" => $data['key'], "value" => $subdata['value']);
					$i++;
				}
			}
			if(count($jdata)>0){
				//Finally convert to Json format
				$jsondata = json_encode($jdata);
				mysql_close($linker);
				echo $jsondata;
			}
			else{
				$jdata[0] = array("text" => _NOSUGGESTDATA);
				$jsondata = json_encode($jdata);
				echo $jsondata;
			}

		}
	}
	else{
		$jdata[0] = array("text" => _NOSUGGEST);
		$jsondata = json_encode($jdata);
		echo $jsondata;
	}
}
else
{
	header("Location: ".$CFG->url."index.html");
}
?>