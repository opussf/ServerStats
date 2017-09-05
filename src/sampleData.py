#!/usr/bin/env python

import os
import json
import time

dataDir = "."
dataFile = "du.json"
dataAge = 86400 * 28  # set as number of seconds

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
timeStamps = sorted( duData.keys() )

cutOff = now - dataAge

done = False if len( timeStamps ) > 1 else True
while( not done ):
	testTS = timeStamps.pop( 0 )
	if testTS < cutOff:
		del duData[ testTS ] 
	else: # no data to delete.  short circut the loop
		done = True


# write the data out
json.dump( duData, open( os.path.join( dataDir, dataFile ), "w" ) )

