#!/bin/bash

if [ $# -ne 3 ]
then
	echo "3 arguments are needed:"
	echo "1) Folder where the logs are"
	echo "2) Folder where the json files will be"
	echo "3) Mongo database where the data will be loaded"
	echo "bash mongoload.sh <logs_folder> <json_folder> <db_name>"
	exit 0
fi

echo "$1 $2 $3"
mkdir -p $2
for file in $1*
do
	filename=$(basename "$file")
	echo "Converting file $file."
	python csv2json.py -f "$file" -o "$2/$filename.json"
	echo "{_id:\"$filename\", name:\"$filename\"}" >> $2/users.json
	echo "Done."
	mongoimport --db $3 --collection events --file $2/$filename.json
done
mongoimport --db $3 --collection "users" --file $2/users.json