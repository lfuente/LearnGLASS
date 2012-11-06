#!/bin/bash

if [ $# -ne 2 ]
then
	echo "2 arguments are needed:"
	echo "1) json log"
	echo "2) Mongo database where the data will be loaded"
	echo "bash mGauge_load.sh <json_file> <db_name>"
	exit 0
fi

echo $1 $2

mongoimport --jsonArray --db $2 --collection events --file "$1"
