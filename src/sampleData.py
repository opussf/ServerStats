#!/usr/bin/env python

import os
import json
import time
import datetime

dataDir = "."
dataFile = "du.json"
daySeconds = 86400
dataAge = (daySeconds * 7) # set as number of seconds
pruneAge = daySeconds * 2

# get the int timestamp
now = int( time.time() )

# load the data structure, or start a new one
try:
	duData = json.load( open( os.path.join( dataDir, dataFile ), "r" ), parse_int=int )
except:
	duData = {}

# there has to be a better way...  but...  force all the keys to int
duData = dict( [ (int(k), v) for k, v in duData.items() ] )

# get the disk info
st = os.statvfs( dataDir )
load = os.getloadavg()

memPageSize = os.sysconf( 'SC_PAGE_SIZE' )
physicalMem = os.sysconf( 'SC_PHYS_PAGES' ) * memPageSize
availMem    = os.sysconf( 'SC_AVPHYS_PAGES' ) * memPageSize

# store in a dict
du = { 'free': st.f_bavail * st.f_frsize,
		'total': st.f_blocks * st.f_frsize,
		'used': ( st.f_blocks - st.f_bfree ) * st.f_frsize,
		'1m': round( load[0], 3),
		'5m': round( load[1], 3),
		'15m': round( load[2], 3)
}

# store in the data structure, with the timestamp as the key
duData[ now ] = du

# truncate old data

cutOffTS = now - dataAge
pruneTS = now - pruneAge

for testTS in duData.keys():
	if ( testTS < cutOffTS ):  # data point is older than the max cutoff
		del duData[ testTS ]
	elif ( testTS < pruneTS ):  # older than a day
		testDT = datetime.datetime.fromtimestamp( testTS )
		if testDT.minute != 0:
			del duData[ testTS ]

# write the data out
json.dump( duData, open( os.path.join( dataDir, dataFile ), "w" ) )

