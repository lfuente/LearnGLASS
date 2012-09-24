#!/etc/php5
<?php

	include_once("../../webapp/lib/mainlib.php");
	
	$user = "user";
	$pass = "user";
	$name = "test1";
	$host = "localhost";

	$database = MongoConnect($user,$pass,$database,$host);
	//$connection = new Mongo("mongodb://$user:$pass@$host/$name");
	MongoCursor::$timeout = -1; // if the process is too long
	
	$database = $connection->selectDB("$name");
	
	$collections = $database->listCollections();
	
	foreach ($collections as $collection) {
		$name = $collection->getName();
		if(strstr($name,'role')){
			$name = substr($name,4);
			echo "$name\n";
		}
	}
	
	//~ $strMap = '
		//~ function(){
			//~ var date = new Date(this.datetime);
			//~ dateString = (date.getYear()+1900)+"-"+(date.getMonth()+1)+"-"+date.getDate();
			//~ for (var user in this.user) {
				//~ emit({name:this.user[user]._id,date:dateString,type:this.name},{count:1});
			//~ }
		//~ }
	//~ ';
	//~ $strReduce = '
		//~ function(k,v){
			//~ var sum = 0;
			//~ for (var i=0;i<v.length;i++){
				//~ sum += v[i].count;
			//~ }
			//~ return {count:sum};
		//~ }
	//~ ';
	//~ $mapCode = new MongoCode($strMap);
	//~ $reduceCode = new MongoCode($strReduce);
	//~ $database->command(array(
		//~ 'mapreduce' => 'events',
		//~ 'map' => $mapCode,
		//~ 'reduce' => $reduceCode,
		//~ 'out' => "roleuser"
	//~ ));
	
?>
