#!/etc/php5
<?php

	include_once("../../webapp/lib/mainlib.php");
	
	$user = "user";
	$pass = "user";
	$name = "test1";
	$host = "localhost";

	$database = MongoConnect($user,$pass,$name,$host);
	//$connection = new Mongo("mongodb://$user:$pass@$host/$name");
	MongoCursor::$timeout = -1; // if the process is too long
	
	//$database = $connection->selectDB("$name");
	
	$collections = $database->listCollections();
	
	foreach ($collections as $collection) {
		$colname = $collection->getName();
		if(strstr($colname,'role')){
			$colname = substr($colname,4);
			echo "$colname\n";
			
			if($colname=="user"){
				//Users MAP-REDUCE
				$mapString = '
					function() {
						var date = new Date(this.datetime);
						dateString = (date.getYear()+1900)+"-"+(date.getMonth()+1)+"-"+date.getDate();
						for (var user in this.user) {
							emit({name:this.user[user]._id,date:dateString,type:this.name},{count:1});
						}
					}
				';
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
					'out' => "roleuser"
				));
			}
			else {
				$mapString = '
					function() {
						var date = new Date(this.datetime);
						dateString = (date.getYear()+1900)+"-"+(date.getMonth()+1)+"-"+date.getDate();
						if(this.'.$colname.'){
							emit({name:this.'.$colname.',date:dateString,type:this.name},{count:1});
						}
					}
				';
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
			}
		}
	}
	
?>
