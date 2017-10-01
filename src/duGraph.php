<?php
require_once( 'jpgraph/jpgraph.php' );
require_once( 'jpgraph/jpgraph_line.php' );
require_once( 'jpgraph/jpgraph_date.php' );
require_once( 'jpgraph/jpgraph_utils.inc.php' );
require_once( 'jpgraph/jpgraph_log.php' );

ini_set('memory_limit', '256M');

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


$xData = array_keys( $data );
$total = end($data);
$total = $total['total'];

$freeData = array_map( function( $a ) { return $a['free']; }, array_values( $data ) );
$usedData = array_map( function( $a ) { global $total; return $a['used'] / $total * 100; }, array_values( $data ) );
$loadData = array_map( function( $a ) { return $a['5m']; }, array_values( $data ) );
$load2Data = array_map( function( $a ) { return $a['15m']; }, array_values( $data ) );

/*
var_dump( $data );
print( "<br/>\n" );
var_dump( $xData );

print( "<br/>\n" );
var_dump( $freeData );
*/

list( $tickPositions, $minTickPositions) = DateScaleUtils::GetTicks( $xData, $aType = DSUTILS_HOUR1);
//$grace = 0;
//$xmin = $xData[0] - $grace;
//$xmax = $xData[count($xData)-1] + $grace;

$graph = new Graph( 1400, 600 );
$graph->SetScale( "datlin" );
$graph->SetY2Scale( "lin" );

$graph->xaxis->SetPos('min');
//$graph->xaxis->SetTickPositions( $tickPositions, $minTickPositions );
$graph->xaxis->SetLabelFormatString( 'D m-d H:i', True );
$graph->xgrid->Show();

$lineFree = new LinePlot( $freeData, $xData );
$lineFree->SetLegend( 'Free' );
$graph->Add( $lineFree );

$lineUsed = new LinePlot( $loadData, $xData );
$lineUsed->SetLegend( 'Load (5m)' );
$graph->AddY2( $lineUsed );
$lineUsed = new LinePlot( $load2Data, $xData );
$lineUsed->SetLegend( 'Load (15m)' );
$graph->AddY2( $lineUsed );

$graph->yaxis->SetLabelFormatCallback('bytesToString');
$graph->xaxis->SetLabelAngle( 90 );
//$graph->yaxis->scale->SetGrace( 50 );

$graph->SetMargin( 75, 50, 25, 75 );
$graph->Stroke();
?>
