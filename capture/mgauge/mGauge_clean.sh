#!/bin/bash

if [ $# -ne 2 ]
then
	echo "2 arguments are needed:"
	echo "1) Raw json log"
	echo "2) Clean json log"
	echo "bash mGauge_load.sh <raw_json_log> <clean_json_log>"
	exit 0
fi

echo $1 $2

sed -e 's/^{.*\[/\[/' -e 's/]}$/]/' <$1 >$2
 
