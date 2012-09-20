#!/usr/bin/python
# -*- coding: UTF-8 -*-#
#
# Author: Luis de la Fuente (lfuente@it.uc3m.es)

import sys, csv, json, getopt, time, os

def main():
    """
    <script> [-f inputfilename] [-o outputfilename]

    Test module's functions

    -f filename     The csv file with student data (# is assumed as delimiter)
    -o filename     The json file used as output. Defaults to output.json.
                    If the file already exists, the content is appended
    """
    # Swallow the options
    try:
        opts, args = getopt.getopt(sys.argv[1:],"f:o:")
    except getopt.GetoptError, e:
        print e.msg
        print main.__doc__
        sys.exit(1)

    output_filename = 'output.json' #default 
    # Process the options
    for optstr, value in opts:
        if optstr == "-f":
            input_filename = value
        if optstr == "-o":
            output_filename = value

    try:
        input_filename
    except NameError:
        print main.__doc__
        sys.exit(1)
    else:
        f = open( input_filename, 'r' )
        o = open( output_filename , 'a')
        reader = csv.DictReader( f, delimiter='#',fieldnames = ( "name","datetime","ref","correct","choice" ))

        for row in reader:
            if (row["name"] == 'answer'):
                tmp = row["ref"]
                row["ref"] = row["choice"];
                row["choice"] = tmp

            if (row["correct"] == 'incorrect'):
                row["correct"] = 'no'
            elif (row["correct"] == 'correct'):
                row["correct"] = 'yes'

            row["user"] = os.path.basename(input_filename)

            #ugly code to set today in the expected format
            timestamp = time.strptime(row["datetime"], "%H:%M:%S")
            l = list(timestamp)
            rightnow = time.gmtime();
            l[0] = rightnow.tm_year
            l[1] = rightnow.tm_mon
            l[2] = rightnow.tm_mday
            timestamp = time.struct_time(l)
            date = dict()
            date["$date"] = int(time.mktime(timestamp) * 1000 )

            row["datetime"] = date
#            row["datetime"] = time.strftime("%Y-%m-%dT%H:%M:%SZ",timestamp)
#            row["datetime"] = "ISODate(" + row["datetime"] + ")"

            o.write(json.dumps(row))
            o.write("\n")

        f.close()
        o.close()

if __name__ == "__main__":
    main()
