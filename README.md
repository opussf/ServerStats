# ServerStats
This is a simple system to capture some server stats, and report on them.

It is not database backed (other than a JSON file used for storage).
This might actually be a detriment to performance as the data storage file increases in size.
It also means that a corrupted data storage file, for one reason or another, may cause a catastrophic loss of historic data.

I have used a database backed system in the past, and may do it again.

# Possible ToDo's

* Make it database backed
* Raise WARNINGS in the RSS feed when:
	* Space free reaches a certain threshold.
	* Load reaches a certain threshold.
* Have the `sampleData.py` script accept parameters so that the calling cron can control certain criteria.

# Notes:
JPGraph Legend placement:
http://jpgraph.net/download/manuals/chunkhtml/ch14s04.html

JPGraph Multi graphs, one image:
http://jpgraph.net/download/manuals/chunkhtml/ch15s08.html
