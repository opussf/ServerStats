<?php
function bytesToString( $bytes ) {
	$bytes = floatval($bytes);
	$Units = array( "", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi" );
	foreach( $Units as $u ) {
		#print( sprintf( "%s %sB\n", $bytes, $u) );
		$b = $bytes / 1024;
		if ($b > 1) {
			$bytes = $b;
		} else { break; }
	}
	return( sprintf( "%s %sB", round( $bytes, 3 ), $u ) );
}

$dataDir = ".";
$dataFile = "du.json";

$data = json_decode( file_get_contents( $dataFile ), $assoc = True );
ksort( $data );

$ts = end( array_keys( $data ) );

$toPost = end($data);
//var_dump( $toPost );

$dsText = bytesToString( $toPost['total'] );
$dfText = bytesToString( $toPost['free'] );
$duText = bytesToString( $toPost['used'] );

$pubDate = date( "r", $ts );

$itemData = array();

$item = array();
$item["title"] = "Disk Space: $dsText";
$item["pubDate"] = $pubDate;
$item["link"] = "http://www.zz9-za.com/~opus/du/";
$item["guid"] = $item["title"];
$itemData[] = $item;

$item=array();
$item["title"] = "Disk Free: $dfText";
$item["pubDate"] = $pubDate;
$item["link"] = "http://www.zz9-za.com/~opus/du/";
$item["guid"] = $item["title"];
$itemData[] = $item;

$item=array();
$item["title"] = "Disk Usage: $duText";
$item["pubDate"] = $pubDate;
$item["link"] = "http://www.zz9-za.com/~opus/du/";
$item["guid"] = $item["title"];
$itemData[] = $item;
//var_dump( $itemData );

header("Content-type: application/xml");

print <<<END
<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet title='XSL_formatting' type='text/xsl' href='/includes/rss.xsl'?>
<rss version="2.0">
<channel>
<title>Server Stats</title>
<link>http://www.zz9-za.com/~opus/du</link>
<description>Server Stats</description>
<generator>php</generator>
<ttl>30</ttl>

END;

foreach( $itemData as $item ){
	print("<item>\n");
	foreach( $item as $key=>$value) {
		print("\t<$key>$value</$key>\n");
	}
	print("</item>\n");
}
?>
</channel>
</rss>
