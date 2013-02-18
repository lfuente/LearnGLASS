#!/etc/php5
<?php

	include_once("../../webapp/lib/mainlib.php");
	
	if ($argc != 5){
		echo "The number of parameters (".($argc-1).") is not right.",PHP_EOL;
		echo "Usage: php mapreduce.php <host> <database name> <user> <password>", PHP_EOL;
		exit;
	}
	
	$user = $argv[3];
	$pass = $argv[4];
	$name = $argv[2];
	$host = $argv[1];

	$database = MongoConnect($user,$pass,$name,$host);
	MongoCursor::$timeout = -1; // if the process is too long
		
	$collections = $database->listCollections();
	
	foreach ($collections as $collection) {
		$colname = $collection->getName();
		if(strstr($colname,'role')){
			$colname = substr($colname,4);
			echo "Updating role".$colname."...";
			
			if($colname=="user"){
				//Users MAP-REDUCE
				$mapString = '
					function() {
						var date = new Date(this.datetime);
						var dateString = date.getUTCFullYear()+"-"+(date.getUTCMonth()+1)+"-"+date.getUTCDate();
						for (var user in this.user) {
							emit({name:this.user[user]._id,date:dateString,type:this.name},{count:1});
						}
					}
				';
			}
			else {
				$mapString = '
					function() {
						var date = new Date(this.datetime);
						var dateString = date.getUTCFullYear()+"-"+(date.getUTCMonth()+1)+"-"+date.getUTCDate();
						if(this.'.$colname.'){
							emit({name:this.'.$colname.',date:dateString,type:this.name},{count:1});
						}
					}
				';
				
			}
			
			$reduceString = '
				function(k,v){
					var sum = 0;
					for (var i=0;i<v.length;i++){
						sum += v[i].count; ;            
					}
					return {count:sum};
				}
			';
			$mapCode = new MongoCode($mapString);
			$reduceCode = new MongoCode($reduceString);
			$database->command(array(
				'mapreduce' => 'events',
				'map' => $mapCode,
				'reduce' => $reduceCode,
				'out' => "role".$colname
			));
			echo " Done.", PHP_EOL;
		}
	}
	
?>
