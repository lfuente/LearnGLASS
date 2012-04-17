<?php
    $time = 1000;
    include_once("../config.php");
    include_once("mainlib.php");
    set_time_limit($time);
    ini_set("memory_limit","1024M");
    $datestart = time();

    
    ///ADD TO DE GLASS DATABASE
    $conexion = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass)or die("error: ".mysql_error());
	mysql_select_db($CFG->dbname) or die("error: ".mysql_error());
    //Search matchs
    $query = "SELECT * FROM ".$CFG->prefix."ddbb";
	$result = mysql_query($query) or die("error: ".mysql_error());
    while($mysqldata = mysql_fetch_array($result)){
        //////////////////////////////////////////////////
        ///MONGO CONNECTION
        $objDB = MongoConnect($mysqldata["user"],$mysqldata["pass"],$mysqldata["name"],$mysqldata["host"]);
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
        echo "<br>Filter event_type created";
        
        //user name filter creation
        $des="Name of the user who made the event";
        $f_values = array();
        $f_values[0] = "None";
        $cursor = $objDB->command(array("distinct"=>"users","key"=>"name"));
        $f_values = array_merge($f_values,$cursor['values']);
        $toinsert = array("group"=>"role","name"=>"user_name","des"=>$des,"values"=>$f_values);
        $collection->insert($toinsert);
        $keysArray["user"]=0;//for the mapreduce 
        echo "<br>Filter user_name created";
        
        //Selected filter creation
        $values = json_decode(str_replace("\\","",$mysqldata["filters"]));
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
                echo "<br>Filter user_".$k." created";
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
                echo "<br>Filter ".$k." created"; 
            }    
        }
    
        //////////////////////////////////////////////////
        ///MAP- REDUCE 
        foreach ($keysArray as $k=>$v) {
            if($k!="_id" && $k!="name" && $k!="datetime"){
                if($k=="user"){
                    //Creamos el MAP-REDUCE para los usuarios
                    $strMap = '
                        function() {
                            var myDate = new Date(this.datetime);
                            STRmyDate = (myDate.getYear()+1900)+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate();
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
                    echo "<br> Map-Reduce roleuser created";
                }
                else{
                    $strMap = '
                        function() {
                            var myDate = new Date(this.datetime);
                            STRmyDate = (myDate.getYear()+1900)+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate();
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
                    echo "<br> Map-Reduce role".$k." created";
                }
            }
        }
    }
    mysql_close($conexion);

        
                
    echo "<br>Fin: ".strftime("%Y-%m-%d %I:%M:%S %p",time()).". Tiempo trascurrido: ".(INT)(time() - $datestart)."segundos";
    echo '<br>Memoria utilizada: ' . round(memory_get_usage() / (1024*1024),1) . ' MB de ' . round(memory_get_usage(1) / (1024*1024),1) . ' MB';
   
   
?>