#!/usr/bin/python
# -*- coding: UTF-8 -*-#
#
# Copyright (C) 2012 Carlos III University of Madrid
# This file is part of the PLA: Personal Learning Assistant

# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor
# Boston, MA  02110-1301, USA.
#
# Author: Abelardo Pardo (abelardo.pardo@uc3m.es)
#
import sys, os, getopt, locale, codecs, hashlib

import anonymize, mongodb

# Fix the output encoding when redirecting stdout
if sys.stdout.encoding is None:
    (lang, enc) = locale.getdefaultlocale()
    if enc is not None:
        (e, d, sr, sw) = codecs.lookup(enc)
        # sw will encode Unicode data to the locale-specific character set.
        sys.stdout = sw(sys.stdout)

def main():
    """
    Script that given a matrix encoded in a CSV file with the following
    structure:
    
    EMTPY,Label1,Label2,...,LabelN
    UserID1,Value1,Value2,...,ValueN
    UserID2,Value1,Value2,...,ValueN
    ...
    UserIDM,Value1,Value2,...,ValueN

    and the parameters to connect to a mongo database:
      - host
      - user
      - passwd
      - dbname
      
    connects to a mongo database and inserts in the user collection an entry for
    each element in the matrix with the following structure:

    TO BE WRITTEN

    Script invocation:

    script [options] CSVFile CSVFile ...

    Options :

     -a file: Use the given file as anonymization map

     -c : Allow the creation of new users

     -d dbname: For DB connection

     -h hostname: For DB connection (default localhost)
    
     -n: When given, the queries are printed instead of executed

     -p passwd: For DB connection (default is empty)
    
     -r: When given, the elements are removed from the database instead of
      inserted.

     -s char: Character to use as separator (default ',')

     -u username: For DB connection
  
    Example:
    Insert (dry run)
    insertusermetadata.py -a mapfile -n -u user -p passwd -d dbname FILE.csv

    Insert
    insertusermetadata.py -a mapfile -u user -p passwd -d dbname FILE.csv

    Remove (dry run)
    insertusermetadata.py -a mapfile -r -n -u user -p passwd -d dbname FILE.csv

    Remove
    insertusermetadata.py -a mapfile -r -u user -p passwd -d dbname FILE.csv
    """

    # Default value for the options
    anonymize_file = None
    hostname = 'localhost'
    username = None
    passwd = ''
    dbname = None
    dryRun = False
    drop = False
    separator = ','
    create_new_users = False
    
    # Swallow the options
    try:
        opts, args = getopt.getopt(sys.argv[1:], "a:cd:h:np:rs:u:", [])
    except getopt.GetoptError, e:
        print str(e)
        sys.exit(2)

    # Parse the options
    for optstr, value in opts:
        # Map file
        if optstr == "-a":
            if not os.path.exists(value) or not os.path.isfile(value):
                print >> sys.stderr, 'File', value, 'does not exists'
                sys.exit(1)
            anonymize_file = value

        # Allow the creation of new users
        elif optstr == "-c":
            create_new_users = True

        # DB Name
        elif optstr == "-d":
            dbname = value
            
        # Hostname
        elif optstr == "-h":
            hostname = value

        # Dry run
        elif optstr == "-n":
            dryRun = True

        # Passwd
        elif optstr == "-p":
            passwd = value

        # Remove entries
        elif optstr == "-r":
            drop = True

        # Separator
        elif optstr == "-s":
            separator = value

        # Username
        elif optstr == "-u":
            username = value

    if args == []:
        print 'The script needs at least a file name'
        print main.__doc__
        sys.exit(1)

    cursor = None
    try:
        mongodb.connect(hostname, username, passwd, dbname)
    except Exception, e:
        print 'Unable to connect with the database'
        print str(e)
        sys.exit(1)

    # slurp anonymization information
    if anonymize_file != None:
        anonymize.load_data(anonymize_file)

    # Loop over all the given data files
    skip_count = 0
    line_number = 0
    param_list = []
    for file_name in args:
        if not os.path.isfile(file_name):
            print >> sys.stderr, 'Only regular files allowed. Skipping', 
            print >> sys.stderr, file_name
            continue

        print >> sys.stderr, 'Processing', file_name
        
        # Open file and get the labels from the first line
        data_in = codecs.open(file_name, 'r')
        labels = data_in.readline()[:-1].split(separator)[1:]
        labels = map(lambda x: x.replace('"', ''), labels)
        print >> sys.stderr, 'Labels:', ', '.join(labels)

        # Loop over all the remaining lines in the file
        check_length = len(labels) + 1
        for line in data_in:
            # Remove quotes
            line = line.replace('"', '')

            # Chop line into fields
            fields = line[:-1].split(separator)

            # If anonymization map is given, do the anonymization (do not allow
            # the creation of new IDs though
            if anonymize_file != None:
                new_user_id = anonymize.find_string(fields[0])
                if new_user_id == None:
                    if not create_new_users:
                        print >> sys.stderr, 'ID', fields[0], \
                            'not found in anonymization map.'
                        sys.exit(1)
                    else:
                        new_user_id = anonymize.find_or_encode(fields[0])
                fields[0] = new_user_id

            if len(fields) != check_length:
                print >> sys.stderr, 'Line', line_number, 
                print >> sys.stderr, 'with incorrect number of elements'
                sys.exit(1)

            # Select the person with the ID and leave it in the cursor_obj
            if create_new_users:
                user_doc = mongodb.find_or_add_user(fields[0])
            else:
                user_doc = mongodb.find_user(fields[0])
            if user_doc == None:
                print >> sys.stderr, 'ID', fields[0], \
                    'not found in users. Skipping'
                skip_count += 1
                continue

            # Update the user with the data

            # Either print or execute the query
            label_dict = dict(zip(labels, fields[1:]))
            if dryRun:
                print >> sys.stderr, "Insert {'_id': " + str(user_doc) + '}'
                print >> sys.stderr, label_dict
            else:
                print >> sys.stderr, "Insert {'_id': " + str(user_doc) + '}'
                print >> sys.stderr, label_dict
                result = mongodb.update_user({'_id': user_doc}, label_dict)

            line_number += 1

    # Done. Close connection
    mongodb.disconnect()
    
    print >> sys.stderr, 'Done'
    print >> sys.stderr, 'Summary:'
    if drop:
        print >> sys.stderr, '  - Deleted:', line_number
    else:
        print >> sys.stderr, '  - Inserted:', line_number
    print >> sys.stderr, '  - Skipped IDs (not found):', skip_count

if __name__ == "__main__":
    main()
    
