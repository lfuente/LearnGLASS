#!/usr/bin/env bash
#
# Create a dump of the as database
#
# Author: Abelardo Pardo <abelardo.pardo@uc3m.es>
#

db_base="YOURDATABASE"
if [ "$1" != "" ]; then
    db_base="$1"
fi
upload_dst="abel@mozart.gast.it.uc3m.es:."
tar_file="${db_base}_mongodump.tgz"

echo "Removing dir $db_base and dump"
rm -rf $db_base dump

command="mongodump --host 127.0.0.1 -d $db_base"
echo "Executing $command"

$command

mv dump/$db_base $db_base

echo "Creating tar $tar_file"
rm -f $tar_file
tar czvf $tar_file $db_base

echo "Uploading dataset to web site"
# scp $tar_file $upload_dst
