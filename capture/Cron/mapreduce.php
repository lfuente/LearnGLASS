#!/etc/php5
<?php

	include_once("../../webapp/lib/mainlib.php");
	
	$user = "";
	$pass = "";
	$name = "test2";
	$host = "localhost/test2";

	$database = MongoConnect($user,$pass,$name,$host);
	MongoCursor::$timeout = -1; // if the process is too long
	
	#$database->dropCollection('roleuser');
	
	$strMap = '
		function(){
			var date = new Date(this.datetime);
			dateString = (date.getYear()+1900)+"-"+(date.getMonth()+1)+"-"+date.getDate();
			for (var user in this.user) {
				emit({name:this.user[user]._id,date:dateString,type:this.name},{count:1});
			}
		}
	';
	$strReduce = '
		function(k,v){
			var sum = 0;
			for (var i=0;i<v.length;i++){
				sum += v[i].count;
			}
			return {count:sum};
		}
	';
	$mapCode = new MongoCode($strMap);
	$reduceCode = new MongoCode($strReduce);
	$database->command(array(
		'mapreduce' => 'events',
		'map' => $mapCode,
		'reduce' => $reduceCode,
		'out' => "roleuser"
	));
	
?>