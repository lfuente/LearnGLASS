#!/usr/bin/python
# -*- coding: UTF-8 -*-#
#
# Script to post the same message and subject to various forums in Aula Global
# 

import sys, locale, codecs, getopt, os, getpass, mechanize, datetime, time

# Fix the output encoding when redirecting stdout
if sys.stdout.encoding is None:
    (lang, enc) = locale.getdefaultlocale()
    if enc is not None:
        (e, d, sr, sw) = codecs.lookup(enc)
        # sw will encode Unicode data to the locale-specific character set.
        sys.stdout = sw(sys.stdout)

def main():
    """
    Script receives a list of strings of the form STRING.number, where STRING is
    identifying a class and number is a Moodle report IDs (the number that
    appears at the end of a URL when you see the form to download the
    report). The script automatically fetches the data and stores them in a file
    with name equal to the STRING.

    script [options] str1.n1 str2.n2 ...
    ...

    Options:

    -d Boolean to turn in debugging info

    -f from date (included). Format must be %Y-%m-%d

    -g get log url

    -h host
  
    -l login url

    -p passwd

    -t until date (included). Format must be %Y-%m-%d

    -u username
   
    Example:

    script -d GISC.29452

    """

    #######################################################################
    #
    # OPTIONS
    #
    #######################################################################
    debug = False
    host = None
    login_url = None
    getlog_url = None
    from_date = None
    until_date = None
    username = None
    passwd = None
    # Swallow the options
    try:
        opts, args = getopt.getopt(sys.argv[1:], "df:g:h:l:p:t:u:", [])
    except getopt.GetoptError, e:
        print >> sys.stderr, 'Incorrect option.'
        print >> sys.stderr, main.__doc__
        sys.exit(2)

    # Parse the options
    for optstr, value in opts:
        # Debug option
        if optstr == "-d":
            debug = True
	elif optstr == "-h":
	    host = value
	elif optstr == "-l":
	    login_url = value
    	elif optstr == "-g":
            getlog_url = value
    	elif optstr == "-f":
            if value == 'today':
                from_date = datetime.date.toda()
            elif value == 'yesterday':
                from_date = datetime.date.toda() - datetime.timedelta(days - 1)
            else:
                from_date = datetime.datetime.strptime(value, '%Y-%m-%d')
                from_date = from_date.date()
    	elif optstr == "-t":
            until_date = datetime.datetime.strptime(value, '%Y-%m-%d')
            until_date = until_date.date()
    	elif optstr == "-u":
            username = value
    	elif optstr == "-p":
            passwd = value

    if (host == None) or (login_url == None) or (getlog_url == None): 
	print >> sys.stderr, 'Missing options.'
	print >> sys.stderr, 'Need -h host -l login-url -g getlog_url'
	sys.exit(2)

    # Check that there are additional arguments
    if len(args) == 0:
        print >> sys.stderr, 'Script needs additional parameters'
        sys.exit(1)

    if debug:
        print >> sys.stderr, '-d', debug
        print >> sys.stderr, '-h', host
        print >> sys.stderr, '-l', login_url
        print >> sys.stderr, '-g', getlog_url
        print >> sys.stderr, '-f', from_date
        print >> sys.stderr, '-u', until_date
        print >> sys.stderr, 'Options: ', args
        
    login_url = host + login_url
    getlog_url = host + getlog_url

    # Search for a string with no '.' delimiting the two parameters
    args = [x.split('.') for x in args]
    incorrect_param = next(('.'.join(x) for x in args if len(x) != 2), None)
    if incorrect_param:
        print >> sys.stderr, 'Only one dot allowed in param', incorrect_param
        sys.exit(1)

    incorrect_param = next((y for (x, y) in args if not(y.isdigit())), None)
    if incorrect_param:
        print >> sys.stderr, 'Incorrect number', incorrect_param
        sys.exit(1)

    #######################################################################
    #
    # MAIN PROCESSING
    #
    #######################################################################

    # Get credentials
    if username == None:
        print "Username: ",
        username = sys.stdin.readline()[:-1]
    if passwd == None:
        passwd = getpass.getpass()

    # Prepare browser
    browser = mechanize.Browser()
    browser.set_handle_robots(False)

    # Login
    response = browser.open(login_url)
    browser.select_form(nr=0)
    browser['username'] = username
    browser['password'] = passwd
    browser.submit()

    if debug:
        print >> sys.stderr, 'Logged in'
    
    # Proceed with the posts
    for (filename, getlog_number) in args:
        if debug:
            print >> sys.stderr, 'Fetching logs from', getlog_number

        data_out = codecs.open(filename + '.csv', 'w')

        if from_date == None:
            
            suffix = '&chooselog=1&showusers=1&showcourses=0' + \
                '&user=0&date=0&logformat=downloadascsv'

            url = getlog_url + '?id=' + getlog_number + suffix
            
            # Store the response
            dump_response(data_out, browser, url, debug)
        else:
            # If no until_date given, take today...
            if until_date == None:
                until_date = datetime.date.today()

            url = getlog_url + '?id=' + getlog_number + \
                '&chooselog=1&showusers=1&showcourses=0' + \
                '&user=0&logformat=downloadascsv'

            # Step to use to advance the loop
            delta = datetime.timedelta(days = 1)
            this_date = from_date
            while (this_date <= until_date):
                as_time = time.mktime((this_date.year, this_date.month, 
                                      this_date.day, 0, 0, 0, 0, 0, 0))
                dump_response(data_out, browser,
                              url + '&date=' + str(int(as_time)),
                              debug)
                this_date += delta

        data_out.close()

def dump_response(data_out, browser, url, debug = 0): 
    """
    Given a file already open, dump the result of obtaining the given url.
    """
    
    if debug:
        print >> sys.stderr, 'Fetch:', url
    response = browser.open(url)
    data_out.write(response.read())
    return

# Execution as script
if __name__ == "__main__":
    main()
