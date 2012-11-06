#!/bin/bash

if [ $# -ne 3 ]
then
	echo "3 arguments are needed:"
	echo "1) Raw json log"
	echo "2) Clean json log (output)"
	echo "3) Mongo database"
	echo "bash mGauge_load.sh <json_file> <db_name>"
	exit 0
fi

echo $1 $2

sh mGauge_clean.sh $1 $2
sh mGauge_load.sh $2 $3
php mongoAddUser.php $3
