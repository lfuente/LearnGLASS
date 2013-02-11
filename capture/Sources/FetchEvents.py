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

	-s {csv | html}. Format to store the data

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
	save_format = 'downloadascsv'

	# Swallow the options
	try:
		opts, args = getopt.getopt(sys.argv[1:], "df:g:h:l:p:s:t:u:", [])
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
				from_date = datetime.date.today()
			elif value == 'yesterday':
				from_date = datetime.date.today() - datetime.timedelta(days = 1)
			else:
				from_date = datetime.datetime.strptime(value, '%Y-%m-%d')
				from_date = from_date.date()
		elif optstr == "-t":
			if value == 'today':
				until_date = datetime.date.today()
			elif value == 'yesterday':
				until_date = datetime.date.today() - datetime.timedelta(days = 1)
			else:
				until_date = datetime.datetime.strptime(value, '%Y-%m-%d')
				until_date = until_date.date()
		elif optstr == "-s":
			if value.lower().strip() == 'csv':
				save_format = 'downloadascsv'
			elif value.lower().strip() == 'html':
				save_format = 'showashtml'
			else:
				print >> sys.stderr, 'Incorrect value for option -s'
				print >> sys.stderr, 'Only "csv" or "html" are allowed'
				sys.exit(2)
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
		print >> sys.stderr, '-u', username
		print >> sys.stderr, '-t', until_date
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

		# Single file download with all the events for ever
		if from_date == None:

			url = getlog_url + '?id=' + getlog_number + \
				'&chooselog=1&showusers=1&showcourses=0' + \
				'&user=0&date=0&logformat=' + save_format

			if debug:
				print '  Dumping data in', filename
			dump_response(filename, save_format, browser, url, debug = 0)

			return
		
		# Multiple dates download


		# If no until_date given, take today...
		if until_date == None:
			until_date = datetime.date.today()

		url = getlog_url + '?id=' + getlog_number + \
			'&chooselog=1&showusers=1&showcourses=0' + \
			'&user=0&logformat=' + save_format

		# Loop day by day
		delta = datetime.timedelta(days = 1) # Step to use to advance the loop
		this_date = from_date
		while (this_date <= until_date):
			if debug:
				print '  Dumping data from', this_date, 'in', filename

			as_time = time.mktime((this_date.year, this_date.month, 
								this_date.day, 0, 0, 0, 0, 0, 0))
			dump_response(filename + '_' + this_date.strftime('%Y%m%d'), 
						save_format,
						browser,
						url + '&date=' + str(int(as_time)),
						debug)
			this_date += delta


def dump_response(filename, save_format, browser, url, debug = 0): 
	"""
	Given a file name, a format to dump, an already open browser, and a URL,
	dump the result of obtaining that url. If the format is HTML and it is
	paginated, a series of files are created with suffix _p??? where ??? is the
	page number.
	"""
	
	global html_parser

	if debug:
		print >> sys.stderr, 'Fetch:', url

	# If format is CSV, open download and close
	if save_format == 'downloadascsv':
		data_out = codecs.open(filename + '.csv', 'w')

		response = browser.open(url)
		data_out.write(response.read())

		data_out.close()
		return

	# If format is HTML, loop over pagination
	page_count = 0
	max_page = sys.maxint
	
	'''
	response = browser.open(url + '&modid=&modaction=0&group=0' + \
		'&perpage=1000&page=' + str(page_count))
	response_str = response.read()
	# If the maximum page has not been obtained, parse the response and get it
	if max_page == sys.maxint:
		tree = etree.parse(StringIO.StringIO(response_str), html_parser)
		# Get the params of all the links in the <div class="paging"> element
		params = []
		[params.extend(el_unit.get('href').split('&')) \
			for el_unit in tree.getroot().xpath('//*[contains(@class, "paging")]/a')]

		# If no params are detected, there is no events.
		if params == []:
			max_page = 0
		else:
			# Obtain the maximum index in the &page=? parameter.
			max_page = max([int(x.replace('page=', '')) for x in params \
				if x.startswith('page=')])
	'''
	
	while (page_count <= max_page):
		if debug:
			print '  Storing', page_count, 'of', max_page

		# Filename contains as suffix the number of pages
		data_out = codecs.open(filename + '_' + "{0:06}".format(page_count) \
			+ '.html', 'w')
		
		response = browser.open(url + '&modid=&modaction=0&group=0' + \
			'&perpage=1000&page=' + str(page_count))
		response_str = response.read()
		data_out.write(response_str)
		data_out.close()
		
		# If the maximum page has not been obtained, parse the response and get it
		if max_page == sys.maxint:
			tree = etree.parse(StringIO.StringIO(response_str), html_parser)
			# Get the params of all the links in the <div class="paging"> element
			params = []
			[params.extend(el_unit.get('href').split('&')) \
					for el_unit in tree.getroot().xpath('//*[contains(@class, "paging")]/a')]

			# If no params are detected, there is no events.
			if params == []:
				max_page = 0
			else:
				# Obtain the maximum index in the &page=? parameter.
				max_page = max([int(x.replace('page=', '')) for x in params \
									if x.startswith('page=')])
		
		page_count += 1
	return

# Execution as script
if __name__ == "__main__":
	main()
