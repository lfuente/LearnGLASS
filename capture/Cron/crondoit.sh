#!/usr/bin/env bash
#
# Script to execute the generation of the dataset via crontab
#
# Author: Iago Quiñones <iagoqo@inv.it.uc3m.es>
#

# Changes the working directory to the script directory so relative paths work properly
cd $(dirname $0)

cd ../Sources
echo [CRONDOIT] Working on $(pwd)

echo [CRONDOIT] Calling: get_logfiles.sh
bash get_logfiles.sh
echo [CRONDOIT] Finished: get_logfiles.sh

cd ../Process
echo [CRONDOIT] Working on $(pwd)

echo [CRONDOIT] Calling: doit.sh
bash doit.sh
echo [CRONDOIT] Finished: doit.sh

cd ../Cron
echo [CRONDOIT] Working on $(pwd)

echo [CRONDOIT] Calling: mapreduce.php
php mapreduce.php
echo [CRONDOIT] Finished: mapreduce.php