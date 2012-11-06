<?php

	if ($argc != 2){
		echo "The number of parameters (".($argc-1).") is not right.",PHP_EOL;
		echo "Usage: php mapreduce.php <database_name>", PHP_EOL;
		exit;
	}
	
	$database	= $argv[1];

	try{
		$con = new Mongo("mongodb://localhost"); // Connect to Mongo Server
		$db = $con->selectDB($database);
	
		$collection = $db->selectCollection("system.users");
		$collection->insert(array('user' => 'u', 'pwd' => md5('u' . ":mongo:" . 'u'), 'readOnly' => false));
	}
	catch(Exception $e){
		echo "error: Impossible to connect!", PHP_EOL;
		exit();
	}

?> 
