#!/usr/bin/python
# -*- coding: UTF-8 -*-#
#
# Script to post the same message and subject to various forums in Aula Global
# 

import sys, locale, codecs, getopt, os, getpass, mechanize, datetime, time
import StringIO
from lxml import etree

# Fix the output encoding when redirecting stdout
if sys.stdout.encoding is None:
    (lang, enc) = locale.getdefaultlocale()
    if enc is not None:
        (e, d, sr, sw) = codecs.lookup(enc)
        # sw will encode Unicode data to the locale-specific character set.
        sys.stdout = sw(sys.stdout)

html_parser = etree.HTMLParser()

xpath_get_table_rows = etree.XPath('//table[@id = "participants"]/tr')
xpath_get_group_selector = etree.XPath('//select[@id = "selectgroup_jump"]')

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

    -c num: Number of columns to consider in use table (default 1)

    -d: Turn in debugging info

    -g URL_suffix:  student listing url

    -h URL: Base url
  
    -l login_url_suffix: suffix to add to base url to log in

    -p passwd: credentials

    -u username: credentials
   
    Example:

    python FetchUserListing -d \
           -u ldelafuentevalentin \
           -h 'http://formacion.educa.madrid.org' \
           -l '/login/index.php' \
           -g '/user/index.php' \
           student_listing.111


    """

    global html_parser
    global xpath_get_table_rows

    #######################################################################
    #
    # OPTIONS
    #
    #######################################################################
    debug = False
    host = None
    login_url = None
    getlist_url = None
    username = None
    passwd = None
    num_columns = 1

    # Swallow the options
    try:
        opts, args = getopt.getopt(sys.argv[1:], "c:dg:h:l:p:u:", [])
    except getopt.GetoptError, e:
        print >> sys.stderr, 'Incorrect option.'
        print >> sys.stderr, main.__doc__
        sys.exit(2)

    # Parse the options
    for optstr, value in opts:
        # Debug option
        if optstr == "-c":
            try:
                num_columns = int(value)
            except ValueError, e:
                print >> sys.stderr, 'Option c requires an integer.'
                sys.exit(2)
        elif optstr == "-d":
            debug = True
    	elif optstr == "-g":
            getlist_url = value
	elif optstr == "-h":
	    host = value
	elif optstr == "-l":
	    login_url = value
    	elif optstr == "-p":
            passwd = value
    	elif optstr == "-u":
            username = value

    if (host == None) or (login_url == None) or (getlist_url == None): 
	print >> sys.stderr, 'Missing options.'
	print >> sys.stderr, 'Need -h host -l login-url -g getlist_url'
	sys.exit(2)

    # Check that there are additional arguments
    if len(args) == 0:
        print >> sys.stderr, 'Script needs additional parameters'
        sys.exit(1)

    if debug:
        print >> sys.stderr, '-d', debug
        print >> sys.stderr, '-h', host
        print >> sys.stderr, '-l', login_url
        print >> sys.stderr, '-g', getlist_url
        print >> sys.stderr, 'Options: ', args
        
    login_url = host + login_url
    getlist_url = host + getlist_url

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
    # browser.select_form(nr = 1)
    browser.select_form(predicate = lambda f: 'id' in f.attrs and \
                            f.attrs['id'] == 'login')
    browser['username'] = username
    browser['password'] = passwd
    browser.submit()

    if debug:
        print >> sys.stderr, 'Logged in'
    
    labels = ['id', 'fullname']

    # Proceed with the posts
    for (filename, getlist_number) in args:
        if debug:
            print >> sys.stderr, 'Fetching lists from', getlist_number

        url = getlist_url + '?id=' + getlist_number + '&perpage=50000'

        response_str = browser.open(url)

        tree = etree.parse(StringIO.StringIO(response_str.read()), 
                           html_parser)

        # Get the table rows except the first one that is the header
        table_rows = xpath_get_table_rows(tree.getroot())[1:]
            
        if debug:
            print >> sys.stderr, 'Detected', len(table_rows), 'users'

        users = load_page_data(table_rows, num_columns)

        if debug:
            print >> sys.stderr, 'Data stored in', filename + '.csv'
            

        # Open data and dump the first row with the field names
        data_out = codecs.open(filename + '.csv', 'w', 'utf8')

        # Get the group selector and obtain the group ids
        group_selector = xpath_get_group_selector(tree.getroot())
    
        if group_selector != []:
            # At this point, the community has groups that need to be
            # processed. First, obtain get the pairs (group_id, group_name)
            group_ids = [(x.get('value'), x.text) \
                             for x in group_selector[0]]
            group_ids = [([int(x.split('=')[1]) for x in m.split('&') \
                               if x.startswith('group=')][0], n) \
                             for (m, n) in group_ids]
            group_ids = [(a, b) for (a, b) in group_ids if a != 0]

            print >> data_out, ','.join(labels + ['group'])

            # Loop and dump a page for each group
            for (group_id, group_name) in sorted(group_ids):

                if debug:
                    print >> sys.stderr, 'Processing group', group_name

                url = getlist_url + '?id=' + getlist_number + \
                    '&group=' + str(group_id) + '&perpage=50000'

                response_str = browser.open(url)

                tree = etree.parse(StringIO.StringIO(response_str.read()), 
                                   html_parser)

                # Get the table rows except the first one that is the header
                table_rows = xpath_get_table_rows(tree.getroot())[1:]

                if debug:
                    print >> sys.stderr, 'Detected', len(table_rows), 'users'

                process_table(table_rows, num_columns, data_out, users,
                              [group_name])
        else:
            # There are no groups in the community
            print >> data_out, ','.join(labels)


        # Data file header and users within groups have been printed. Add those
        # remaining in "users" which are the ones assigned to no group.
        
        if len(users) != 0 and debug:
            print >> sys.stderr, 'Adding', len(users), 'with no group assigned'
        
        # Loop over the users remaining in the dictionary
        for (user_id, user_name) in sorted(users.items()):
            line = ','.join([user_id, user_name])
            if group_selector != []:
                line += ','
            print >> data_out, line

def load_page_data(table_rows, num_columns):
    """
    Given a table element in a Moodle student listing page, create a hash with
    user_id, username where usename is created by concatenating the text in the
    given number of columns
    """

    # The dictionary will have pairs (user_id, user_name)
    result = {}

    # Row is assumed to have the following cells:
    # - User image
    # - User name 
    # - other fields (irrelevant)
    for row_elem in table_rows:
        user_id = row_elem[1][0][0].get('href')
        user_id = user_id.partition('?')[2]
        user_id = user_id.partition('&')[0]
        user_id = user_id.split('=')[1]

        user_name = ' '.join([row_elem[r][0][0].text \
                                  for r in range(1, num_columns + 1)])
        
        result[user_id] = user_name
    return result
            
# def process_listing_page(table_rows, num_columns, filename, labels, 
#                          tail_values = None):
#     """
#     Recieves:
#     - table_rows: A list of HTML elements containing table rows
#     - num_columns: the number of columns to consider in the table
#     - filename: file to dump the results
#     - labels: List of labels to write as header
#     - tail_values: List of values to add at each user

#     Given an HMTML element pointing to a table row in Moodle with users in a
#     course, open a CSV file, write the given labels as first row, and then write
#     one line per users with the useid, fullname followed by the tail_values.

#     returns Nothing
#     """

#     # Open data and dump the first row with the field names
#     data_out = codecs.open(filename + '.csv', 'w', 'utf8')
#     print >> data_out, ','.join(labels)

#     process_table(table_rows, num_columns, data_out, tail_values)

#     data_out.close()


def process_table(table_rows, num_columns, data_out, users, tail_values = None):
    """
    Given a list of HTML elements with table rows of Moodle course listing, the
    number of columns to consider, a file already opened, and list of tail
    values, dump the data in CSV format.

    Whenever a user_id is processed, it is removed from the users dictionary
    returns nothing

    """
    if tail_values == None:
        tail_values = []

    # Row is assumed to have the following cells:
    # - User image
    # - User name 
    # - other fields (irrelevant)
    for row_elem in table_rows:
        user_id = row_elem[1][0][0].get('href')
        user_id = user_id.partition('?')[2]
        user_id = user_id.partition('&')[0]
        user_id = user_id.split('=')[1]

        user_name = ' '.join([row_elem[r][0][0].text \
                                  for r in range(1, num_columns + 1)])
        
        linedata = [user_id, user_name]
        linedata.extend(tail_values)
        print >> data_out, ','.join(linedata)
        
        users.pop(user_id)

# Execution as script
if __name__ == "__main__":
    # tree = etree.parse('xxx.html', html_parser)
    # group_selector = xpath_get_group_selector(tree.getroot())
    
    # group_ids = [(x.get('value'), x.text) \
    #                      for x in group_selector[0]]
    # group_ids = [([int(x.split('=')[1]) for x in m.split('&') \
    #                    if x.startswith('group=')][0], n) \
    #                  for (m, n) in group_ids]
    # group_ids = [(a, b) for (a, b) in group_ids if a != 0]

    # print group_ids
    main()
