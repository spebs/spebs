<?php
include ("init.inc.php");
include("header.php");
$q = "SELECT connection_id, longitude, latitude FROM connection";
$conns = get_results($q);
$q = "UPDATE connection SET exchange_id=?, distance_to_exchange=?, max_bw_ondistance=?, max_vdslbw_ondistance=? WHERE connection_id=?";	
foreach($conns AS $k => $c)
{
	echo "<br/>connection: ".$c['connection_id']."  (lng: {$c['longitude']}, lat: {$c['latitude']})<br/>";
	$exchange_info = find_exchange($c['latitude'],$c['longitude']);
	$pars = array($exchange_info['exchange_id'],$exchange_info['distance_m'],$exchange_info['max_bandwidth'],$exchange_info['max_vbandwidth'],$c['connection_id']);
	//angela_print_array($pars);
	$vartypes = 'iiiii';
	$result = execute_prepared_query($q,$pars,$vartypes);
	echo $c['connection_id']." \t=>\t ".$exchange_info['exchange_id']." [dist={$exchange_info['distance_m']}, adsl={$exchange_info['max_bandwidth']}, vdsl={$exchange_info['max_vbandwidth']},]<hr/>";
}

?>