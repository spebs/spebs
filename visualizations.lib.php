<?php
/**
 * SPEBS 
 *
 * The script contains all functions that produce statistics and display them using Google Maps API and Google Visualizations API. 
 *
 * DB Tables refernced:
 * ---
 *    generic_measurement: Records measurements directly from NDT
 *    generic_measurements_stats: A trigger transforms appropriately records from generic_measurement and inserts them in this table
 *    aggregation_per_[some area type]: Statistics generated asynchronously per area, after processing generic_measurements_stats according to spebs_update.php script. 
 *
 *    user, user_connection and connection: Contain users' and their connections' information (location, contract etc)
 *    various area tables: Contain names, descriptions and polygons when applicable for areas
 *
 *
 * @copyright (c) 2011 EETT
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE 
 *
 *
 * @author ICCS, NOC Team, National Technical University of Athens
 */

/*
* Originally written  by Aggeliki Dimitriou <A.Dimitriou@noc.ntua.gr> 
*                        Panagiotis Christias <P.Christias@noc.ntua.gr> 
*                        Athanasios Douitsis <A.Douitsis@noc.ntua.gr> 
*                        Chrysa Papagianni <C.Papagianni@noc.ntua.gr> 
*/

/**********************************************************************************************************************/
/*******************************************************      MAP     *************************************************/
/**********************************************************************************************************************/

/****
*
*  Show main map (see also map.js and mappoints.php)
*   
* 
*****/

function show_map($user_id = 0)
{
	global $message,$lang_disclaimer_message,$lang_not_enough_data,$googleMapsKey,$googleMapsV3Key,$lang_lang_short;
	global $lang_upstream,$lang_downstream,$lang_jitter,$lang_rtt,$lang_packet_loss,$lang_info,$lang_upstream_short,$lang_downstream_short,$lang_jitter_short,$lang_rtt_short,$lang_packet_loss_short,$lang_outof;
	global $home,$lang_statistics_from,$lang_measurement_count,$lang_measurements_count,$lang_measurements,$lang_connections,$lang_all,$lang_allcontracts,$lang_none;
	global $lang_day_type, $lang_working, $lang_weekend, $lang_alldaysrtimes, $lang_time_zone, $lang_alldays, $lang_alltimes, $glasnost_throttles_accepted_percentage, $glasnost_throttled_connections_accepted_percentage, $lang_throttled_connections, $lang_throttled, $lang_non_throttled;
	global $bandwidths,$lang_contract;
	
	echo '<script src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.1.0/prototype.js"></script>'."\n"
      .'<script src="js/json2.js"></script>'."\n";
	echo 
	//'<script src="http://maps.google.com/maps?file=api&hl='.$lang_lang_short.'&amp;v=2&amp;sensor=false&amp;key='.$googleMapsKey.'" type="text/javascript"></script>'.
	'<script src="https://maps.googleapis.com/maps/api/js?key='.$googleMapsV3Key.'&libraries=geometry&sensor=false"  type="text/javascript"></script>'.
        '<script src="js/markermanager.js"  type="text/javascript">
	</script>
	<script src="js/mapiconmaker.js" type="text/javascript">
	</script>'.
	'<script src="js/jquery_latest.js"></script>';


/*
				*********** Notes for map.js: *********************
				
				limit_values * scale = value_steps
				comparison: value * reverse > value_step
				============================================
				if metric continues till infinite (ex. rtt), limit value is one , so only scale matters
				if as metric increases quality falls (ex. rtt )
					1.limit value gets negative to reverse comparison in value_color() 
					2.step order in scale reverses too
					3.corresponding value of metric in reverse array  is set to -1
*/

	if ($user_id > 0)
	{
		$userlocation = getUserLocation($user_id);
		$userisp = getUserISP($user_id);
		$userconnection = get_connection_id($user_id);
	}	
	if ($_SESSION['profile'] >= 0) //show colored map, currently show colors to everyone
	{	
		echo '<script src="js/map.js"></script>'."\n";
		$metrics_tabs_display = "inline";
		$gmetrics_tabs_display = "none";
	}
	else
	{	
		echo '<script src="js/map_basic.js"></script>'."\n";
		$metrics_tabs_display = "none";
		$gmetrics_tabs_display = "none";
	}
	
	
	$q = "SELECT * FROM isp ORDER BY name";
	$res = execute_query($q);
	$ispscheck = "";
	
	//**********************  ISPs  *********************
	echo "<script type=\"text/javascript\">\n";
	$ispc = 0;	
	echo "var metricnames = new Array();";
	echo "metricnames[0] = ['$lang_downstream','$lang_upstream','$lang_packet_loss','$lang_rtt','$lang_jitter'];";
	echo "metricnames[1] = ['Flash Video','BitTorrent','eMule','Gnutella','POP','IMAP','HTTP','SSH'];";
	echo "var units = ['Mbps','Mbps','%','msec','msec'];";
	echo "var pass = $glasnost_throttled_connections_accepted_percentage;var langnonthrottled='$lang_non_throttled';var langthrottled='$lang_throttled';var langthrottledconnections = '$lang_throttled_connections';var langoutof = '$lang_outof';";
	echo "var regionicons = new Array();";
	
	if ($_SESSION['profile'] >= 0) //show colored map, currently show colors to everyone
		echo "regionicons[0] =['".$home."images/smallcity_red.png','".$home."images/smallcity_orange.png','".$home."images/smallcity_cyan.png','".$home."images/smallcity_blue.png','".$home."images/smallcity_green.png','".$home."images/smallcity_green.png','".$home."images/smallcity_grey.png','".$home."images/smallcity_green.png'];\n
			  regionicons[1] =['".$home."images/bigcity_red.png','".$home."images/bigcity_orange.png','".$home."images/bigcity_cyan.png','".$home."images/bigcity_blue.png','".$home."images/bigcity_green.png','".$home."images/bigcity_green.png','".$home."images/bigcity_grey.png','".$home."images/bigcity_green.png'];\n";
	else
		echo "regionicons[0] =['".$home."images/smallcity-blue1.png','".$home."images/smallcity-blue1.png','".$home."images/smallcity-blue1.png','".$home."images/smallcity-blue1.png','".$home."images/smallcity_grey.png'];\n
			  regionicons[1] =['".$home."images/bigcity-red1.png','".$home."images/bigcity-red1.png','".$home."images/bigcity-red1.png','".$home."images/bigcity-red1.png','".$home."images/bigcity_grey.png'];\n";
	echo "var langmcount='$lang_measurements';\n var langccount='$lang_connections';\n var langnotenaoughdata='$lang_not_enough_data';\n";
	
	$ispscheckallnone = '<div id="allnone"><a href="#" style="padding-left:5px" onClick="selectAllISPs(true);return false;">'.$lang_all.'</a>&nbsp;<span style="color:#b0b0b0">|</span>&nbsp;<a href="#" onClick="selectAllISPs(false);return false;">'.$lang_none.'</a></div>';
	
	//For not displaying isps
	echo "isps[0] = 1;\n"; 
	while ($row = $res -> fetch_assoc())
	{
		$id = $row['isp_id'];
		echo "isps[$id] = 1;\n";
		$ispscheck .= "<li><a href=\"#\" name=\"ispselection\" id=\"isp$id\" class=\"selected\" onclick=\"updispvis('$id');return false;\">{$row['name']}</a></li>";
		$ispc++;	
	}
	$lat_diff = 0.0065798;
	$lng_diff = 0.0119737;
	if ($user_id > 0)
	{
		$min_lat = $userlocation['latitude'] - $lat_diff;
		$min_lng = $userlocation['longitude'] - $lng_diff;
		$max_lat = $userlocation['latitude'] + $lat_diff;
		$max_lng = $userlocation['longitude'] + $lng_diff;
	
		echo "\nswpoint=new google.maps.LatLng($min_lat,$min_lng);\n";
		echo "nepoint=new google.maps.LatLng($max_lat,$max_lng);\n";
		echo "areabounds = new google.maps.LatLngBounds(swpoint,nepoint);\n";
 
 	}
	echo "</script>";	
    
	//**** Time and days selection   *********************
	$dayselection = 
		"<li><a id=\"alldays\" href=\"#\" class=\"selected\" onclick=\"change_day_time_contract('alldays','','');return false;\">$lang_alldays</a></li>".
		"<li><a id=\"working\" href=\"#\" onclick=\"change_day_time_contract('working','','');return false;\">$lang_working</a></li>".
		"<li><a id=\"nonworking\" href=\"#\" onclick=\"change_day_time_contract('nonworking','','');return false;\">$lang_weekend</a></li>";
	$timeselection = 
		"<li><a id=\"alltimes\" href=\"#\" class=\"selected\" onclick=\"change_day_time_contract('','alltimes','');return false;\">$lang_alltimes</a></li>".
		"<li><a id=\"p1\" href=\"#\" onclick=\"change_day_time_contract('','p1','');return false;\">00:00 - 08:00</a></li>".
		"<li><a id=\"p2\" href=\"#\" onclick=\"change_day_time_contract('','p2','');return false;\">08:00 - 16:00</a></li>".
		"<li><a id=\"p3\" href=\"#\" onclick=\"change_day_time_contract('','p3','');return false;\">16:00 - 00:00</a></li>";
	$packetselection = 
		"<div style=\"background-color:#F0F0F0;color:#555555;padding-left:6px;\">$lang_contract</div>".
		"<select style=\"width:94px; margin:2px 2px 2px 2px; color:#4466CC;\" id=\"contract\" onchange=\"change_day_time_contract('','',this.selectedIndex-1);\"><option selected>$lang_allcontracts</option>";
	foreach($bandwidths AS $comboid => $thiscontract)
	{
		$d = kbps2Mbps($thiscontract['d'],0,1);
		$u = kbps2Mbps($thiscontract['u'],0,1);
		$packetselection .= "<option>$d/$u</option>";
	}
	$packetselection .= "</select>";
	
	
	//****Tabs for selecting metric to display   *********************
	$html = '
		<div id="tools" style="float:left;">
			<div class="metricstab" id="ndt"><a class="selected" href="#" onclick="change_tool(\'n\');return false;">NDT</a></div>
			<div class="metricstab" id="glasnost"><a href="#" onclick="change_tool(\'g\');return false;">Glasnost</a></div>
			<div style="clear:both"></div>
		</div>
		<div id="nmetrics" style="float:left;display:'.$metrics_tabs_display.';">
			<div class="metricstab" id="downstream"><a href="#" onclick="update_points(\'downstream\');return false;">'.$lang_downstream.'</a></div>
			<div class="metricstab" id="upstream"><a href="#" onclick="update_points(\'upstream\');return false;">'.$lang_upstream.'</a></div>
			<div class="metricstab" id="loss"><a href="#" onclick="update_points(\'loss\');return false;">'.$lang_packet_loss.'</a></div>
			<div class="metricstab" id="rtt"><a href="#" onclick="update_points(\'rtt\');return false;">'.$lang_rtt.'</a></div>
			<div class="metricstab" id="jitter"><a href="#" onclick="update_points(\'jitter\');return false;">'.$lang_jitter_short.'</a></div>
			<div style="clear:both"></div>
		</div>
		<div id="gmetrics" style="float:left;display:'.$gmetrics_tabs_display.';">
			<div class="metricstab" id="flash"><a href="#" onclick="update_points(\'flash\');return false;">Flash Video</a></div>
			<div class="metricstab" id="bittorrent"><a href="#" onclick="update_points(\'bittorrent\');return false;">BitTorrent</a></div>
			<div class="metricstab" id="emule"><a href="#" onclick="update_points(\'emule\');return false;">eMule</a></div>
			<div class="metricstab" id="gnutella"><a href="#" onclick="update_points(\'gnutella\');return false;">Gnutella</a></div>
			<div class="metricstab" id="pop"><a href="#" onclick="update_points(\'pop\');return false;">POP</a></div>
			<div class="metricstab" id="imap"><a href="#" onclick="update_points(\'imap\');return false;">IMAP</a></div>
			<div class="metricstab" id="http"><a href="#" onclick="update_points(\'http\');return false;">HTTP</a></div>
			<div class="metricstab" id="ssh"><a href="#" onclick="update_points(\'ssh\');return false;">SSH</a></div>
			<div style="clear:both"></div>
		</div>
		<div style="clear:both"></div>
		<div id="statsmap"></div>
		<div id="maprightside">';
	//Show ISPs to priviledged users
	if ($_SESSION['profile'] > 1)
	$html .= '<div id="ispselection">'
			.$ispscheckallnone
			.'<ul class="isplist">'
			.$ispscheck.'</ul>'
			.'</div>';
	$html .= '
			<div id="timeselection">'
			.'<ul class="timezone">'
			.$dayselection.'</ul>'
			.'<ul class="timezone">'
			.$timeselection.'</ul>'
			.'</div>'
			.'<ul  style="background-color:#f0f0f0" class="timezone">'
			.$packetselection.'</ul>'
			.'</div>'
		.'</div><div style="clear:both;display:block;"></div>'
		.'<div id="maplegend"></div>';
	
		//****  Disclaimer   *********************
	$html .= '<div id="disclaimer">'
				.info_message($lang_disclaimer_message)
				.'</div>';
		
	$html .= "<script type=\"text/javascript\">\n tabs = document.getElementById(\"nmetrics\").innerHTML; \n</script>\n";
	
	echo $html;
}	

/****
*
*  Build simple map query that returns single balloon per connection
*  (function called by mappoints.php) 
*   
* parameters:
* 		connection of user (in case of logged in user) for including her ballon in any case
* 		latitude/longitude of map viewport boundaries
* 		zoom level of map viewport
* 		timezone selection
*
*
*****/
function build_map_query($connection_id = -1,$min_lat = "37.9973578",$min_lng = "23.7738808",$max_lat = "38,0152388",$max_lng = "23,8072396",$zoom = 15, $workingDays = -1, $peakHours = -1, $contract = -1, $tool = "ndt")
{
	global $min_measurements_per_user,$min_glasnostmeasurements_per_user,$bandwidths;
	//In case of zoom out, don't show any ballon
	if ($zoom < 15) 
		return null;
	
	$isp_join = "";
	$isp_fields = "";
	$union_ndt_subquery = "";
	$union_glasnost_subquery = "";
	
	if (isset($_SESSION['user_id']))
	{	
		$userconnections = get_alluser_connections($_SESSION['user_id']);
		$conns = "";
		foreach($userconnections as $conn)
		{
			if($conns != "")
				$conns .= ",";
			$conns .= $conn['connection_id'];
		}
	}
	else
		$conns = $connection_id;
	
	$contract_filter = (isset($bandwidths[$contract]))? " AND contract = '".$bandwidths[$contract]['d']." ".$bandwidths[$contract]['u']."' ":"";
	//Logged in user
	if($connection_id >0 )
	{	
		$isp_fields = ($_SESSION['profile'] < 2)? "":", i.name, i.isp_id isp_id ";
			
		$isp_join = ($_SESSION['profile'] < 2)? "":" JOIN isp i ON c.isp_id=i.isp_id ";
	
	// Use union to show grey balloon for user with no measurements	
		$union_ndt_subquery = 
			" UNION
			SELECT c.connection_id cid, measurements_count mcount, avgdown downstream_bw, avgup upstream_bw, avgrtt rtt,  round(avgloss,3)*100 loss, avgjitter jitter, 
			c.longitude longitude, c.latitude latitude, c.description address, c.distance_to_exchange ex_distance, round(c.purchased_bandwidth_dl_kbps/1000,0) contract_dl, c.purchased_bandwidth_ul_kbps contract_ul,
			exchange_id, distance_to_exchange, max_bw_ondistance, max_vdslbw_ondistance	
			$isp_fields
			FROM connection c
			LEFT JOIN (SELECT * FROM aggregation_per_connection aggm WHERE workingDay = $workingDays AND peakHour = $peakHours $contract_filter AND connection_id=$connection_id) aggm ON aggm.connection_id=c.connection_id
			$isp_join
			WHERE c.connection_id in ($conns)";
			
		$union_glasnost_subquery = 
			" UNION
			SELECT c.connection_id cid, measurements_count mcount, bittorent_throttled_measurements, bittorent_measurements, emule_throttled_measurements, emule_measurements, gnutella_throttled_measurements, gnutella_measurements, http_throttled_measurements, http_measurements,
			ssh_throttled_measurements, ssh_measurements, pop_throttled_measurements, pop_measurements, imap_throttled_measurements, imap_measurements, flash_throttled_measurements, flash_measurements,  
			c.longitude longitude, c.latitude latitude, c.address, c.distance_to_exchange ex_distance, round(c.purchased_bandwidth_dl_kbps/1000,0) contract_dl, c.purchased_bandwidth_ul_kbps contract_ul,
			exchange_id, distance_to_exchange, max_bw_ondistance, max_vdslbw_ondistance	
			$isp_fields
			FROM connection c
			LEFT JOIN (SELECT * FROM aggregation_per_connection_glasnost aggm WHERE workingDay = $workingDays AND peakHour = $peakHours $contract_filter AND connection_id=$connection_id) aggm ON aggm.connection_id=c.connection_id
			$isp_join
			WHERE c.connection_id in ($conns)";
	}
	
	$ndt_query = "SELECT STRAIGHT_JOIN aggm.connection_id cid, measurements_count mcount, avgdown downstream_bw, avgup upstream_bw, avgrtt rtt,  round(avgloss,3)*100 loss, avgjitter jitter, 
		c.longitude longitude, c.latitude latitude, c.address, c.distance_to_exchange ex_distance, round(c.purchased_bandwidth_dl_kbps/1000,0) contract_dl, c.purchased_bandwidth_ul_kbps contract_ul,
		exchange_id, distance_to_exchange, max_bw_ondistance, max_vdslbw_ondistance	
		$isp_fields
		FROM connection c USE INDEX (latlng_index)
		JOIN aggregation_per_connection aggm ON aggm.connection_id=c.connection_id
		$isp_join
		WHERE c.status > 0
		AND c.latitude > $min_lat AND c.latitude < $max_lat AND c.longitude > $min_lng AND c.longitude < $max_lng
		AND workingDay = $workingDays AND peakHour = $peakHours $contract_filter AND measurements_count>=$min_measurements_per_user
		$union_ndt_subquery";
		
	$glasnost_query = "SELECT STRAIGHT_JOIN aggm.connection_id cid, measurements_count mcount, 
		bittorent_throttled_measurements, bittorent_measurements, emule_throttled_measurements, emule_measurements, gnutella_throttled_measurements, gnutella_measurements, http_throttled_measurements, http_measurements,
		ssh_throttled_measurements, ssh_measurements, pop_throttled_measurements, pop_measurements, imap_throttled_measurements, imap_measurements, flash_throttled_measurements, flash_measurements, 
		c.longitude longitude, c.latitude latitude, c.address, c.distance_to_exchange ex_distance, round(c.purchased_bandwidth_dl_kbps/1000,0) contract_dl, c.purchased_bandwidth_ul_kbps contract_ul,
		exchange_id, distance_to_exchange, max_bw_ondistance, max_vdslbw_ondistance	
		$isp_fields
		FROM connection c USE INDEX (latlng_index)
		JOIN aggregation_per_connection_glasnost aggm ON aggm.connection_id=c.connection_id
		$isp_join
		WHERE c.status > 0
		AND c.latitude > $min_lat AND c.latitude < $max_lat AND c.longitude > $min_lng AND c.longitude < $max_lng
		AND workingDay = $workingDays AND peakHour = $peakHours $contract_filter AND measurements_count>=$min_glasnostmeasurements_per_user
		$union_glasnost_subquery";
		

	return ${$tool."_query"};
}

/****
*
*  Generate balloons per connection executing query coming from build_map_query()
*  (function called by mappoints.php) 

* parameters:
* 		connection of user (in case of logged in user) for including her ballon in any case
* 		latitude/longitude of map viewport boundaries
* 		zoom level of map viewport
* 		timezone selection
*
*
*****/

function generate_map_pins($query,$userconnection,$userlocation,$tool="ndt",$bottomleft_coor=null,$topright_coor=null)
{
	global $lang_statistics_from,$lang_measurements_count, $lang_measurements,$lang_measurement_count,$lang_not_enough_measurements_for_this_time_period,$lang_undefined,$lang_undefined_f,$max_distance_from_exchange_meters, $max_vdsl_distance_from_exchange_meters, $lang_outof;
	global $lang_downstream, $lang_upstream, $lang_packet_loss, $lang_rtt, $lang_jitter,$lang_bandwidth_purchased,$lang_distance_to_exchange,$lang_max_bw_ondistance,$glasnost_throttles_accepted_percentage,$lang_throttled_measurements;
	$phparraytojson = array();
	if (is_null($query))
		return array("","",0,0,0,0);
	
	//If viewport boundaries not given, build an area around user location
	if (!is_array($bottomleft_coor) && !is_array($topright_coor))
	{
		$lat_diff = 0.0065798;
		$lng_diff = 0.0119737;
		$min_lat = $userlocation['latitude'] - $lat_diff;
		$min_lng = $userlocation['longitude'] - $lng_diff;
		$max_lat = $userlocation['latitude'] + $lat_diff;
		$max_lng = $userlocation['longitude'] + $lng_diff;
	}
	else
	{
		$min_lat = $bottomleft_coor['lat'];
		$min_lng = $bottomleft_coor['lng'];
		$max_lat = $topright_coor['lat'];
		$max_lng = $topright_coor['lng'];
	}
	
	
	//Simple execution without prepared statement, since parameters are properly sanitized in mappoints.php
	$res = execute_query($query);
	$jscode = "";
	$i=0;
	$current_exchange = array('lat'=>"",'lng'=>"");
	$exchid = 0;
	$mypointexists = false;
	$ispid = 0;
	$protocols = array("flash","bittorent","emule","gnutella","pop","imap","http","ssh");
	if(isset($_SESSION['user_id']))
	{	
		$userconnections = array();
		$userconns = get_alluser_connections($_SESSION['user_id']);
		foreach($userconns as $uc)	
			$userconnections[] = $uc['connection_id'];
	}
	$phparraytojson["mypointid"] = array();
	
	while ($row = $res -> fetch_assoc())
	{
		if ($_SESSION['profile'] > 1)
			$ispid = $row['isp_id'];

		
		if (in_array($row['cid'], $userconnections))
		{
			//specify user's big balloon
			$phparraytojson["mypointid"][] = $i;
			$mypointexists = true;
		}
		$lang_mcount = ($row['mcount'] == 1)? $lang_measurement_count:$lang_measurements_count;
		$mcount = ($row['mcount'] == NULL)? 0:$row['mcount'];
		$phparraytojson["points"][] = array("new google.maps.LatLng({$row['latitude']},{$row['longitude']})",$ispid);
		//****************************** NDT ***********************************
		if($tool == "ndt")
		{
			$phparraytojson["values"][0][$i] = ($row['downstream_bw'] == NULL)? -1:$row['downstream_bw'];
			$phparraytojson["values"][1][$i] = ($row['upstream_bw'] == NULL)? -1:$row['upstream_bw'];
			$phparraytojson["values"][2][$i] = ($row['loss'] == NULL)? -1:$row['loss'];
			$phparraytojson["values"][3][$i] = ($row['rtt'] == NULL)? -1:$row['rtt'];
			$phparraytojson["values"][4][$i] = ($row['jitter'] == NULL)? -1:$row['jitter'];
			
			$balloon_content = '<div class="balloonmetric balloonline">'.$lang_downstream.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue balloonline">'.round($row['downstream_bw'],1).' Mbps</div>'
			.'<div class="balloonmetric">'.$lang_upstream.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue">'.round($row['upstream_bw'],1).' Mbps</div>'
			.'<div class="balloonmetric">'.$lang_packet_loss.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue">'.round($row['loss'],1).' %</div>'
			.'<div class="balloonmetric">'.$lang_rtt.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue">'.round($row['rtt'],0).' msec</div>'
			.'<div class="balloonmetric">'.$lang_jitter.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue">'.round($row['jitter'],1).' msec</div>';
		}
		//****************************** Glasnost ***********************************
		else
		{
			foreach($protocols as $k => $protocol)
			{
				if ($row[$protocol.'_measurements'] == 0)
					$phparraytojson["values"][$k][$i] = -1;
				else if($row[$protocol.'_throttled_measurements'] > $glasnost_throttles_accepted_percentage * $row[$protocol.'_measurements']) 
					$phparraytojson["values"][$k][$i] = 1;
				else
					$phparraytojson["values"][$k][$i] = 0;
					
				$lang_m = ($row[$protocol.'_measurements'] == 1)? $lang_measurement_count:$lang_measurements_count;
				$protocolval[$protocol] = ($row[$protocol.'_measurements'] > 0)? "{$row[$protocol.'_throttled_measurements']} $lang_outof {$row[$protocol.'_measurements']} (". round($row[$protocol.'_throttled_measurements']*100.0/$row[$protocol.'_measurements'])."%)":" - ";
			}
			
			$balloon_content = '<div class="balloonmetric balloonline" style="width:100%;text-align:center">'.$lang_throttled_measurements."</div>"
			.'<div class="balloonmetricsmall">'."Flash Video".':&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['flash']."</div>"
			.'<div class="balloonmetricsmall">'."BitTorrent".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['bittorent']."</div>"
			.'<div class="balloonmetricsmall">'."eMule".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['emule']."</div>"
			.'<div class="balloonmetricsmall">'."Gnutella".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['gnutella']."</div>"
			.'<div class="balloonmetricsmall">'."POP".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['pop']."</div>"
			.'<div class="balloonmetricsmall">'."IMAP".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['imap']."</div>"
			.'<div class="balloonmetricsmall">'."HTTP".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['http']."</div>"
			.'<div class="balloonmetricsmall">'."SSH".'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvaluelarge">'.$protocolval['ssh']."</div>";
			
		}
		$addr = (is_null($row['address']))? $row['address2']:$row['address'];
		$addr = address_format($addr);
		$kbps = $row['contract_ul'] % 1000;
		$mbps = round($row['contract_ul']/1000,0);
		$contractupstream = ($kbps > 0)? "$kbps kbps":"$mbps Mbps";
		$dist = "";
		$vdist = "";
		if ($row['distance_to_exchange'] > $max_distance_from_exchange_meters )
			$dist = "&gt; ".meters2Km($max_distance_from_exchange_meters);
		
		if($row['distance_to_exchange'] > $max_vdsl_distance_from_exchange_meters)
			$vdist = "&gt; ".meters2Km($max_vdsl_distance_from_exchange_meters);
		
		$dist = (empty($dist) && $row['exchange_id']>0)? "~&nbsp;".meters2Km($row['distance_to_exchange']):$lang_undefined;
		$maxbwdist = (($row['distance_to_exchange'] <= $max_distance_from_exchange_meters) && $row['exchange_id']>0)? "~&nbsp;".kbps2Mbps($row['max_bw_ondistance'])." ADSL":$lang_undefined_f." ADSL";
		$maxvdslbwdist = (empty($vdist) && $row['exchange_id']>0)? "~&nbsp;".kbps2Mbps($row['max_vdslbw_ondistance'])." VDSL":$lang_undefined_f." VDSL";
		
		$phparraytojson["infos"][$i] = '<div class="balloon">'
			.'<div class="balloonaddrrow"><img class="balloonisplogo" align="right" src="images/isp'.$ispid.'logo.png">'
			.''.$addr.'</div>'
			.'<div class="balloonmetric balloonline">'.$lang_bandwidth_purchased.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue balloonline">&darr;'.$row['contract_dl'].'Mbps&nbsp;&nbsp;&uarr;'.$contractupstream.'</div>'
			.'<div class="balloonmetric balloonline">'.$lang_distance_to_exchange.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue balloonline">'.$dist.'</div>'
			.'<div class="balloonmetric">'.$lang_max_bw_ondistance.'&nbsp;&nbsp;&nbsp;</div><div class="balloonmetricvalue">'.$maxbwdist.'</div>'
			.'<div class="balloonmetric">'.'&nbsp;'.'&nbsp;&nbsp;&nbsp;</div><div style="color:#555555;" class="balloonmetricvalue">'.$maxvdslbwdist.'</div>'
			.$balloon_content
			.'<div class="balloonfooter">'.$lang_statistics_from.' '.$mcount.' '.$lang_mcount.'</div>'
			.'<div style="clear: both"></div>'
			.'</div>';
		$min_lat = min($min_lat,$row['latitude']);
		$min_lng = min($min_lng,$row['longitude']);
		$max_lat = max($max_lat,$row['latitude']);
		$max_lng = max($max_lng,$row['longitude']);
		$i++;
	}
	
	$json = json_encode($phparraytojson);
	return $json;
}

/****
*
*  Build map query that returns statistics for the area type corresponding to specific zoom level 
*  (function called by mappoints.php) 
*   
* parameters:
* 		user location
* 		bottom-left and right-top points of map viewport boundaries
* 		zoom level of map viewport
* 		timezone selection
* retutns:
		areaqueries['pols']: containing actual polygons ancoded appropriately for showing with Google Maps API
		areaqueries[0]: containing measurements' statistics for areas
*
*
*****/

function build_map_aggregated_query($userloc = null, $bottomleft_coor=null,$topright_coor=null,$zoom = 15, $workingDays = -1, $peakHours = -1, $contract = -1, $tool = "ndt")
{
	global $geographical_divisions, $geographical_division_zoom_levels, $geographical_division_map_shape;
	global $min_connections_per_postal_code, $min_connections_per_municipality, $min_connections_per_prefecture, $min_connections_per_periphery, $min_connections_per_country, $sliding_window_in_days;
	global $lang_lang_short,$lang_postal_code_short, $lang_postal_code_prefix, $lang_periphery_prefix, $lang_prefecture_prefix, $bandwidths;
	
	$region_level = -1;
	if($zoom == 100)
		$region_level = 1;
	else
	{
		foreach ($geographical_division_zoom_levels as $lev => $z)
		{
			if($zoom < $z)
			{ 
				$region_level = ($lev == 0)? 0: ($lev-1);
				break;
			}
		}
	}
	if($region_level<0)
		$region_level = 0;
	$area_mode = $geographical_division_map_shape[$region_level];
	/*
	if ($zoom < 8)   //peripheries view (polygons)
		$granularity = 3;
	elseif ($zoom < 11)  //prefectures view (polygons)
		$granularity = 2;
	elseif ($zoom < 13)  //municiplaities view (icons)
		$granularity = 1;
	elseif ($zoom < 15) //postal codes view (polygons)
		$granularity = 0;
	elseif($zoom == 100)
	{
		$granularity = 2;
	}	
	else
		return null;*/
		
	$areatypes = array("periphery","prefecture","municipality","postal_code");
	
	
	/**************************************** Build region range clause **********************************************/
	/*          Either restrict view in a specified window, or show a window of selected zoom around user location   */
	/*****************************************************************************************************************/
	if (!is_array($bottomleft_coor) && !is_array($topright_coor))
	{
		if (is_array($userloc)) //no boundaries set -> show everything around user
		{
			$lat_diff = 0.0065798;
			$lng_diff = 0.0119737;
			$min_lat = $userloc['latitude'] - $lat_diff;
			$min_lng = $userloc['longitude'] - $lng_diff;
			$max_lat = $userloc['latitude'] + $lat_diff;
			$max_lng = $userloc['longitude'] + $lng_diff;
		}
	}
	else //map dragged/zoomed/panned show everything in selected area
	{
		$min_lat = $bottomleft_coor['lat'];
		$min_lng = $bottomleft_coor['lng'];
		$max_lat = $topright_coor['lat'];
		$max_lng = $topright_coor['lng'];
	}
	
	$viewportpolygon = "GeomFromText('Polygon(($min_lng $min_lat,$min_lng $max_lat,$max_lng $max_lat,$max_lng $min_lat,$min_lng $min_lat))')";
	
	$region_range_where_clause ="";
	$region_range_join_clause ="";
	//$i=$granularity;
	$areatype = $areatypes[$region_level];
	if($region_level >= $geographical_divisions)
		$areatable = "postal_codes";
	else
		$areatable = "region_level_".$region_level;
	$areaagregationtable = "aggregation_per_".$areatype;
	$areaagregationtable .= ($tool == "ndt")? "":"_glasnost";
	$min_connections_per_area_variable = ($tool == "ndt")? "min_connections_per_$areatype":"min_connections_per_$areatype"."_glasnost";
	global $$min_connections_per_area_variable;
	$min_connections = $$min_connections_per_area_variable;
	$prefix = isset(${"lang_".$areatype."_prefix"})? ${"lang_".$areatype."_prefix"}:"";
	$areanamecol = "CONCAT('".$prefix."',name_$lang_lang_short)";
	$idcol = "id";
	if ($region_level == 3)
		$idcol = "code";
	
	$contract_filter = (isset($bandwidths[$contract]))? " AND contract = '".$bandwidths[$contract]['d']." ".$bandwidths[$contract]['u']."' ":" AND contract = 'all'";
	$filter = " workingDay = $workingDays AND peakHour = $peakHours".$contract_filter;
	
	if (isset($min_lat) && $area_mode=="point") // detailed view with boundaries or user location with showing circles around a center and not polygons
		$region_range_where_clause ="WHERE a.latitude > $min_lat AND a.latitude < $max_lat AND a.longitude > $min_lng AND a.longitude < $max_lng ";

	//if in prefectures or peripheries zoom level then check possibly multiple polygons (namely prefecture polygons) for intersecting with viewport
	if ($region_level < 2)
	{		
		$prefecturetype = 1; // i.e. areatype = prefecture: table field for checking overlaps with viewport
		$wc = "WHERE ".$areatype."_id IS NOT NULL";
		//Show all peripheries no matter what region viewport defines
		if (isset($min_lat) && $region_level == 1)
			//old one, replaced by spatial query: $wc .= " AND latitude > $min_lat AND latitude < $max_lat AND longitude > $min_lng AND longitude < $max_lng ";
			$wc .= " AND Intersects($viewportpolygon, SHAPE) ";
		
		$region_range_join_clause = " JOIN  
					(SELECT distinct ".$areatype."_id AS id 
					FROM detailed_{$areatypes[$prefecturetype]}_polygons 
					$wc) d ON a.id=d.id ";
		
		$area_query['pols'] = "SELECT ".$areatype."_id id, enc_pol_points as points, enc_pol_levels as levels
						FROM detailed_{$areatypes[$prefecturetype]}_polygons d
						$wc
						ORDER BY ".$areatype."_id, polid, aa";  
		//$area_query['pols'] = "SELECT id, enc_pol_points as points, enc_pol_levels as levels
			//			FROM local_exchange_polygons WHERE Intersects($viewportpolygon, SHAPE)";  
	}
	
	if ($_SESSION['profile'] > 1)//privileged user level 1 ---> show her isps
		$order_by_clause = "a.$idcol, isp_id";
	else
		$order_by_clause = "a.$idcol";
		
	if($tool == "ndt")
		$metric_fields = " avgup, avgdown, round(avgloss,3)*100 avgloss, avgrtt, avgjitter";
	else
		$metric_fields = " bittorent_throttled_connections, bittorent_measurements, connections_with_bittorent_measurements,
		emule_throttled_connections, emule_measurements, connections_with_emule_measurements, gnutella_throttled_connections, gnutella_measurements,
		connections_with_gnutella_measurements, http_throttled_connections, http_measurements, connections_with_http_measurements, ssh_throttled_connections,
		ssh_measurements, connections_with_ssh_measurements, pop_throttled_connections, pop_measurements, connections_with_pop_measurements, imap_throttled_connections,
		imap_measurements, connections_with_imap_measurements, flash_throttled_connections, flash_measurements, connections_with_flash_measurements";
		
	$area_query[] = "SELECT STRAIGHT_JOIN a.$idcol as $areatype, $areanamecol as areaname, latitude, longitude, $region_level category, isp_id, 
						$metric_fields,
						measurements_sum measurements, connections_count connections
						FROM 
						$areatable a $region_range_join_clause							
						LEFT JOIN 
						(SELECT * FROM $areaagregationtable WHERE $filter) aggms 
						ON a.$idcol=aggms.$areatype   
						$region_range_where_clause
						 ORDER BY $order_by_clause";

	return $area_query;
}

/****
*
*  Generate polygons or area icons executing query coming from build_map_aggregated_query()
*  (function called by mappoints.php) 

* parameters:
* 		connection of user (in case of logged in user) for including her ballon in any case
* 		latitude/longitude of map viewport boundaries
* 		zoom level of map viewport
* 		timezone selection
*
*
*****/

function generate_map_circles($aggrq, $tool = "ndt")
{
	global $message, $glasnost_throttles_accepted_percentage;
	//$arearadiuses = array(0.003,0.005,0.15,0.25,0.5);
	$arearadiuses = array(0.005,0.008,0.18,0.28,0.6);
	$areapolygonangles = array(60,45,30,15,10);
	$thisarealat = 0;
	$thisarealng = 0;
	$index = 0;
	$protocols = array("flash","bittorent","emule","gnutella","pop","imap","http","ssh");
	$jscode = "";
	$phparraytojson = array();
	if (is_null($aggrq))
		return array("","");
	$polygons_defined = false;
	if (isset($aggrq['pols']))//this is the query for retrieving actual polygons
	{
		$polygons_defined = true;
		//Simple execution without prepared statement, since parameters are properly sanitized in mappoints.php
		$res = execute_query($aggrq['pols']);
		$aid = 0;
		//$i = -1;
		while($row = $res -> fetch_assoc())
		{	
			if ($aid != $row['id'])  //[[p,l],[p,l],[p,l]]
				$aid = $row['id'];
			$areapolygons[$aid]['php'][] = array($row['points'], $row['levels']); 
			$komma = (count($areapolygons[$aid]['php']) > 1)? ',':'';
		}
	}
	$metricscount =($tool == "ndt")? 5:8;
	$measurements_index = $metricscount+1;
	$connections_index = $metricscount+2;
	
	foreach($aggrq AS $key => $q)
	{ 
		if (is_numeric($key)) 
		{
			//Simple execution without prepared statement, since parameters are properly sanitized in mappoints.php
			$res = execute_query($q);
			$array_length =  ($polygons_defined)? 7:6;
			$region_connections = 0;
			$processed_array = init_value_holders($tool, 0);
			
			/***********         Iterate over results and construct appropriate area data rows. Multiple rows may refer to one area when area connections  *******
			*************         span accross multiple ISPs                                                                   *******************************************/
			while($row = $res -> fetch_array(MYSQLI_BOTH))
			{	
				//If area is changed and not only ISP inside the same area
				//...........create NEW area data row
				if (($row['longitude'] != $thisarealng) && ($row['latitude'] != $thisarealat )) 
				{
					$table_fields = array_keys($row);
					$areatype = $table_fields[1];
					global ${"min_connections_per_$areatype"}, ${"min_connections_per_$areatype".'_glasnost'}; 
					$min_connections_per_area = ($tool == "ndt")? ${"min_connections_per_$areatype"}:${"min_connections_per_$areatype".'_glasnost'};
					
					//if previous region contained very few connections, denote by assigning negative values to all metrics (i.e. make area polygon gray)
					if ($processed_array[$connections_index] > 0)
					{	
					    if( $processed_array[$connections_index] < $min_connections_per_area)
						//Too few connections in area
						{
							unset($phparraytojson["circles"][$i][5]);
							$phparraytojson["circles"][$i][5][0] = init_value_holders($tool, -1);
							$phparraytojson["circles"][$i][5][0][0] = 0; //isp = 0
							$phparraytojson["circles"][$i][5][0][$measurements_index] = 0; // measurements = 0
						}
						else if($_SESSION['profile'] < 2) //show aggregate for isps since not privileged profile
						{	
							//Normailize by dividing with sum of connections in case of ndt that contains averages for the metrics
							if($tool == "ndt")
							{
								for($y=1;$y<$metricscount+1;$y++)
									$processed_array[$y] = $processed_array[$y]/$processed_array[$connections_index]; 
							}
							unset($phparraytojson["circles"][$i][5]);
							$phparraytojson["circles"][$i][5][0] = $processed_array;
						}
					}
					unset($processed_array);
					$processed_array = init_value_holders($tool, 0);
					$thisarea_no = $row[0];
					$thisarealat = $row['latitude'];
					$thisarealng = $row['longitude'];
					$thisareaname = $row['areaname'];
					$thisareacat = $row['category'];
					$phparraytojson["circles"][$index][0] = floatval($thisarealat);
					$phparraytojson["circles"][$index][1] = floatval($thisarealng);
					$phparraytojson["circles"][$index][2] = "$thisareaname";
					$phparraytojson["circles"][$index][3] = $arearadiuses[$thisareacat];
					$phparraytojson["circles"][$index][4] = $areapolygonangles[$thisareacat];
					if($polygons_defined)
					{
						$phparraytojson["circles"][$index][6] = $areapolygons[$thisarea_no]['php'];
					}
					$index++;
					$areaisps = 0;
				
				}
				
			
				//...........Now add ISP data, maybe multiple times
				
				$thisisp = $row['isp_id'];
				$i = $index-1;
				
				if($row['measurements'] == NULL)
				// The area emerged from region table's left outer join. It doesn't contain enough measurements. Corresponding records contain NULLs	
				{
					$phparraytojson["circles"][$i][5][0] = init_value_holders($tool, -1);
					$phparraytojson["circles"][$i][5][0][0] = 0; //isp = 0
					$phparraytojson["circles"][$i][5][0][$measurements_index] = 0; // measurements = 0
				}
				else
				{	
					if($tool == "ndt")
					{
						$phparraytojson["circles"][$i][5][$areaisps] = array(0=>intval($thisisp),1=>round($row['avgdown'],1),2=>round($row['avgup'],1),3=>round($row['avgloss'],1),4=>round($row['avgrtt'],0),5=>round($row['avgjitter'],1),6=>intval($row['measurements']),7=>intval($row['connections']));
						for($x=1;$x<6;$x++)
							$processed_array[$x] += $row['connections']*$phparraytojson["circles"][$i][5][$areaisps][$x];
						for($x=6;$x<8;$x++)
							$processed_array[$x] += $phparraytojson["circles"][$i][5][$areaisps][$x];
					}
					else // $tool == glasnost
					{
						$phparraytojson["circles"][$i][5][$areaisps][0] = intval($thisisp);
						$p = 1;
						foreach($protocols AS $protocol)
						{
							$phparraytojson["circles"][$i][5][$areaisps][$p++] = array(intval($row[$protocol.'_throttled_connections']), intval($row['connections_with_'.$protocol.'_measurements']), intval($row[$protocol.'_measurements']));
						}
						$phparraytojson["circles"][$i][5][$areaisps][$p] = intval($row['measurements']);
						$phparraytojson["circles"][$i][5][$areaisps][$p+1] = intval($row['connections']);
						
						for($x=1;$x<11;$x++)
						{	
							if($x>0 && $x<9)
							{
								for($j=0;$j<3;$j++)
									$processed_array[$x][$j] += $phparraytojson["circles"][$i][5][$areaisps][$x][$j];
							}
							else
								$processed_array[$x] += $phparraytojson["circles"][$i][5][$areaisps][$x];
						}
					}
				}	
				$areaisps++;
			}
			

			
			/////***** Do the same processing for last area's tuples [ln. 657-675]
			if ($processed_array[$connections_index] > 0)
			{	
				if( $processed_array[$connections_index] < $min_connections_per_area)
				//Too few connections in area
				{
					unset($phparraytojson["circles"][$i][5]);
					$phparraytojson["circles"][$i][5][0] = init_value_holders($tool, -1);
					$phparraytojson["circles"][$i][5][0][0] = 0; //isp = 0
					$phparraytojson["circles"][$i][5][0][$measurements_index] = 0; // measurements = 0
				}
				else if($_SESSION['profile'] < 2) //show aggregate for isps since not priviledged profile
				{	
					//Normailize by dividing with sum of connections in case of ndt that contains averages for the metrics
					if($tool == "ndt")
					{
						for($y=1;$y<$metricscount+1;$y++)
							$processed_array[$y] = $processed_array[$y]/$processed_array[$connections_index]; 
					}
					unset($phparraytojson["circles"][$i][5]);
					$phparraytojson["circles"][$i][5][0] = $processed_array;
				}
			}
			unset($processed_array);
			
		}
	}
	$json = json_encode($phparraytojson);
	return $json;
}

function init_value_holders($t, $initval)
{
	$arr = array();
	if($t == "ndt")
		$arr = array_fill(0,8,$initval);
	else
	{
		$arr = array_fill(1,8,array($initval,$initval,$initval));
		$arr[0] = $initval;
		$arr[9] = $initval;
		$arr[10] = $initval;
	}
	return $arr;
}

	
/****  Not relevant with main map....
*
*  Show map for giving user address and calculating coordinates during registration (see also: signup.php, addressmap.js)
*
*
*
*****/
	

function show_give_address_map()
{
	global $spebs_db, $googleMapsKey, $lang_lang_short, $googleMapsV3Key;
	global $lang_connection, $lang_street, $lang_street_num, $lang_postal_code, $lang_municipality, $lang_location, $lang_show_on_map, $lang_isp, $lang_connection_name, $lang_mainconnection, $lang_mainconnection_expl, $lang_connection_selection, $isp, $bandwidth, $lang_connection_add;
	global $connections, $connectionid;
	
	//$street, $street_num, $postal_code, $municipality, $addrlat, $addrlng, $description, $status;
	
	echo //'<script src="http://maps.google.com/maps?file=api&hl='.$lang_lang_short.'&amp;v=2&amp;sensor=false&amp;key='.$googleMapsKey.'" type="text/javascript"></script>	
	'<script src="https://maps.googleapis.com/maps/api/js?key='.$googleMapsV3Key.'&libraries=geometry&sensor=false"  type="text/javascript"></script>
		<script src="js/mapiconmaker.js" type="text/javascript">
		</script>'.
		'<script src="js/jquery.autocomplete.js" type="text/javascript">
		</script>';

	echo '<script src="js/addressmap.js"></script>'."\n";
	echo "<script type=\"text/javascript\">\n";	
	
	echo "</script>";	
	if(count($connections)>0)
	{
		$selected_connection = 0;
		$connselection = "";
		$i = 0;
		foreach($connections as $conn)
		{
			$conndescr = (empty($conn['description']))? "$lang_connection $i":$conn['description'];
			$connstat = ($conn['status']==1)? "true":"false";
			$connselection .= 
				"<a id=\"conn$i\" href=\"#\" onclick=\"changeConnection({$conn['connection_id']}, '{$conn['description']}', $connstat, '{$conn['street']}', '{$conn['street_num']}', '{$conn['postal_code']}', '{$conn['region']}', {$conn['addrlat']}, {$conn['addrlng']}, {$conn['isp']}, {$conn['bandwidth']});return false;\">$conndescr</a>&nbsp;|&nbsp;";
			if(isset($connectionid) && $conn['connection_id'] == $connectionid)
					$selected_connection = $i;
			$i++;
		}
		
		$connselection .= 
			"<a id=\"connnew\" href=\"#\" onclick=\"resetConnection();return false;\">$lang_connection_add</a>";
		
		$connselection = '
		<div class="formfield">
			<div class="formlabel">'
				.$lang_connection_selection.'
			</div>
			<div class="forminput">
				'.$connselection.'
			</div>
		</div>';
	}
	
	/******* $connections[0] will always contain the main connection, so let it set the field values ********/
	/******* unless connectionid is already set so prefer the selected one  ***/
	if(count($connections)>0)
	{
		foreach($connections[$selected_connection] as $k => $v)
			$$k = $v;
		$mainchecked = ($status ==  1)? "checked":"";
		
		$mainconn = '
		<div class="formfield" style="height:20px;padding-bottom:20px;">
			<div class="formlabel" style="height:20px;">'
				.$lang_mainconnection.
			'</div>
			<div class="forminput" style="height:20px;">
				<input type="checkbox" id="mainconnection" name="mainconnection" value="yes" '.$mainchecked.'/>
				<span>'.$lang_mainconnection_expl.'</span>
			</div>
		</div>';
	}
	else
		$mainconn = "";
		
	$html3 = $connselection.'
		<div class="formfield">
			<div class="formlabel">'
				.$lang_connection_name.'
			</div>
			<div class="forminput">
				<input type="hidden" name="connectionid" id="connectionid" maxlength="50" type="text" value="'.$connection_id.'">
				<input class="textbox w15em" name="connectionname" id="connectionname" maxlength="50" type="text" value="'.$description.'">
			</div>
		</div>
		'.$mainconn.'
		<div class="formfield">
			<div class="formlabel">'
				.$lang_street.'
			</div>
			<div class="forminput">
				<input class="textbox w15em" name="street" id="street" maxlength="32" type="text" onchange="showInputAddres()"  value="'.$street.'">
			</div>
		</div>
		<div class="formfield">
			<div class="formlabel">'
				.$lang_street_num.'
			</div>
			<div class="forminput">
				<input class="textbox w15em" name="street_num" id="street_num" maxlength="5" type="text" onchange="showInputAddres()" value="'.$street_num.'">
			</div>
		</div>
		<div class="formfield">
			<div class="formlabel">'
				.$lang_postal_code.'
			</div>
			<div class="forminput">
				<input class="textbox w15em ac_input" name="postal_code" id="postal_code" maxlength="5" type="text" onchange="showInputAddres()" value="'.$postal_code.'">
			</div>
		</div>
		<div class="formfield">
			<div class="formlabel">'
				.$lang_municipality.'
			</div>
			<div class="forminput">
				<input class="textbox w15em" name="region" id="region" type="text" onchange="showInputAddres()" value="'.$region.'">
			</div>
		</div>
		<div class="formfield" style="height:410px;">
			<div class="formlabel" id="addrmaplabel" style="height:410px;">'
				.$lang_location.'
			</div>
			<div class="forminput" style="height:410px;">
				<div id="greece" style="width: 400px; height: 400px; float:left;"></div>
				<div id="errcont" style="width: 200px; height: 400px; float:left;"></div>
				<div style="clear:both; display:block;">
					<input type="hidden" id="addrlat" name="addrlat" value="'.$addrlat.'">
					<input type="hidden" id="addrlng" name="addrlng" value="'.$addrlng.'">
					<div style="display:none;width:0px;height:0px;" name="drc" id="drc"></div>
				</div>
			</div>
		</div>
	';
	
	echo $html3;
}	

/**********************************************************************************************************************/
/*******************************************************    Table     *************************************************/
/**********************************************************************************************************************/

/****
*
*  Show table with detailed user measurements
*   
* 
*****/

function show_tabular($user_id, $connection_ids)
{
	global $relative_path,$lang_lang_google,$lang_export_csv,$lang_export_csv_glasnost,
		$lang_export_csv_ndt,$lang_not_enough_measurements_for_table;
	$tablelang = (empty($lang_lang_google))? "": ", $lang_lang_google";
	
	$ndt = array();
	$glasnost = array();
	
	foreach($connection_ids as $i => $connection_id)
	{
		$ndt[] = (connection_measurements($connection_id) > 0) ? 1:0;
		$glasnost[] = (connection_glasnost_measurements($connection_id) > 0) ? 1:0;
	
		//if($ndt[count($ndt)-1])
		//{
			$qNDT = build_table_query($user_id, $connection_id, "ndt");
			$jsgdata[] = transform2google_data($qNDT, array('datetime','string','string','string','string','string'), "ndtdata[$i]");
		//}

		//if($glasnost[count($glasnost)-1])
		//{
			$qGlasnost = build_table_query($user_id, $connection_id, "glasnost");
			$jsgdata[] = transform2google_data($qGlasnost,array('datetime','string','boolean','boolean'), "glasnostdata[$i]");
		//}
	}
	echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>';

	echo '<h2 style="margin-bottom:5px;">NDT</h2>';
	echo '<div id="ndt-table" style="border: 1px solid gray;width:860px">
		<div id="userdata" name="userdata" style="height:218px;width:860px">
		<!-- Table container-->
		</div>
		</div>';

	echo "<a href=\"/$relative_path?action=export_measurements&t=n\"><button type=\"button\" style=\"margin-top:5px\">$lang_export_csv</button></a>";
	echo '<br/><br/>';
	if($ndt[0])
	{
		$disp = "none";
	}
	else
		$disp = "block";
	
	echo '<div id="ndtnotenoughmeasurements" style="displpay:'.$disp.';">';
		display_info_message($lang_not_enough_measurements_for_table);
	echo '</div>';
	
	echo '<h2 style="margin-bottom:5px;">Glasnost</h2>';
	echo '<div id="glasnost-table" style="border: 1px solid gray;width:860px">
		<div id="userdata2" name="userdata2" style="height:218px;width:860px">
		<!-- Table container-->
		</div>
		</div>';

	echo "<a href=\"/$relative_path?action=export_measurements&t=g\"><button type=\"button\" style=\"margin-top:5px\">$lang_export_csv</button></a>";
	if($glasnost[0])
	{
		$disp = "none";
	}
	else
		$disp = "block";
	
	echo '<div id="glasnostnotenoughmeasurements" style="displpay:'.$disp.';">';
		display_info_message($lang_not_enough_measurements_for_table);
	echo '</div>';
	
	echo "<script type='text/javascript'>
			google.load('visualization', '1', {packages:['table'] $tablelang});
		</script>";
		
	echo "<script type='text/javascript'>
		var ndtdata = new Array();
		var glasnostdata = new Array();";
			
	for($i=0;$i<count($jsgdata);$i++)
	{	
		echo $jsgdata[$i];
	}
		
	echo '</script>';
	echo '<script type="text/javascript" src="js/tables.js"></script>';

}

/****
*
*  Build table query that returns data for table visualization
* 
*
*****/
function build_table_query($user_id, $connection_id, $measurement_tool)
{
	$q = "";
	if($measurement_tool == "ndt")
	{
		//CONCAT('new Date(',DATE_FORMAT(date_created,'%Y'),',',DATE_FORMAT(date_created,'%c')-1,',',DATE_FORMAT(date_created,'%e'),',', DATE_FORMAT(time_created,'%k,%i,%s'),')') datetime
		$dateselection = "CONCAT('new Date(',DATE_FORMAT(date_created,'%Y'),',',DATE_FORMAT(date_created,'%c')-1,',',DATE_FORMAT(date_created,'%e'),',',DATE_FORMAT(time_created,'%k')+0,',',DATE_FORMAT(time_created,'%i')+0,',',DATE_FORMAT(time_created,'%s'+0),')') datetime";
		$q = "SELECT $dateselection, ROUND(downstream_bw,3) downstream_unit_short, ROUND(upstream_bw,3) upstream_unit_short, 
		ROUND(rtt,1) rtt_unit_short, CONCAT(ROUND(loss*100,2),'%') AS packet_loss_short, jitter jitter_short 
		FROM generic_measurements_stats WHERE connection_id=$connection_id ORDER BY date_created, time_created";
	}
	elseif($measurement_tool == "glasnost")
	{
		//CONCAT('new Date(',DATE_FORMAT(created,'%Y'),',',DATE_FORMAT(created,'%c')-1,',',DATE_FORMAT(created,'%e'),',',DATE_FORMAT(created,'%k,%i,%s'),')') datetime
		$dateselection = "CONCAT('new Date(',DATE_FORMAT(created,'%Y'),',',DATE_FORMAT(created,'%c')-1,',',DATE_FORMAT(created,'%e'),',',DATE_FORMAT(created,'%k')+0,',',DATE_FORMAT(created,'%i')+0,',',DATE_FORMAT(created,'%s'+0),')') datetime";
		$q = "SELECT $dateselection, CONCAT(protocol1,' (port:',port1,')') AS application, IF(download_indication,'false','true') download, IF(upload_indication,'false','true') upload
		FROM glasnost_measurement WHERE connection_id=$connection_id ORDER BY created";
	}
	return $q;
}  

/**********************************************************************************************************************/
/*******************************************************      Charts     *************************************************/
/**********************************************************************************************************************/

/****
*
*  Show graphs with user measurements per metric
*   
* 
*****/

function show_charts($user_id = 39, $connection_ids, $n_measurements = 100)
{
	global $lang_lang_google, $lang_lang_short, $lang_last_year, $lang_last_measurements;
  		
	$lang_last_n_measurements = sprintf($lang_last_measurements,$n_measurements);
	
	echo '  <div id="metrics" style="width: 860px; border-bottom: 3px solid #f0f0f0; height:18px;display:block;">
				<span class="graphstab" style="background-color:#f0f0f0;" id="lyear" name="lyear"><a href="#" onclick="update_graphs(\'lyear\', getElementById(\'connection_selection\').selectedIndex)">'.$lang_last_year.'</a></span>
				<span class="graphstab" id="lmeasurements"><a href="#" onclick="update_graphs(\'lmeasurements\', getElementById(\'connection_selection\').selectedIndex)">'.$lang_last_n_measurements.'</a></span>
			</div>
			<div id="graphs" style="display:block;">
				<div id="updownchart_div" style="width:860px; height: 260px; border:1px solid #aaa; margin:25px 0 30px 0;">Upstream/Downstream</div>
				<div id="rttchart_div" style="width:860px; height: 130px; border:1px solid #aaa; margin:25px 0 30px 0;">Ping Time</div>
				<div id="jitterchart_div" style="width:860px; height: 130px; border:1px solid #aaa; margin:25px 0 30px 0;">Jitter</div>
				<div id="losschart_div" style="width:860px; height: 130px; border:1px solid #aaa; margin:25px 0 30px 0;">Packet loss</div>
			</div>';
	
	foreach($connection_ids as $i => $connection_id)
	{
		//First chart: N months to the past
		$query = build_chart_query($connection_id,false,0,0); 
		$jsgdata[] = transform2google_data($query, array('datetime','number','number','number','number','number'), array("nmupdowndata[$i]","nmrttdata[$i]","nmlossdata[$i]","nmjitterdata[$i]"),array(array(0,1,2),array(0,3),array(0,4),array(0,5)));
		//Last M measurements
		$query = build_chart_query($connection_id,false,0,$n_measurements); 
		$jsgdata[] = transform2google_data($query, array('datetime','number','number','number','number','number'), array("mmupdowndata[$i]","mmrttdata[$i]","mmlossdata[$i]","mmjitterdata[$i]"),array(array(0,1,2),array(0,3),array(0,4),array(0,5)));	
	}
	echo '
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
			  google.load(\'visualization\', \'1\', {packages:[\'annotatedtimeline\'], language:\''.$lang_lang_short.'\'});
		</script>';
	echo '<script type="text/javascript">
			var nmupdowndata = new Array();
			var nmrttdata = new Array();
			var nmlossdata = new Array();
			var nmjitterdata = new Array();
			var mmupdowndata = new Array();
			var mmrttdata = new Array();
			var mmlossdata = new Array();
			var mmjitterdata = new Array();';

	for($i=0;$i<count($jsgdata);$i++)
		echo $jsgdata[$i];
		
	echo '</script>';
	echo '<script type="text/javascript" src="js/graphs.js"></script>';

}
/****
*
*  Build chart query that returns data for charts visualization
* 
*
*****/

function build_chart_query($connection_id, $grouped_per_day = false, $months_to_the_past = 0, $limit = 0)
{
	
	$dateclause = "";
	if ($months_to_the_past>0)
	{	
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		
		$offsetyear = $year - intval($months_to_the_past/12); 
		$offsetmonth = ($month + (12-$months_to_the_past)) % 12;
		
		$offsetdate = $offsetyear."-".$offsetmonth."-".$day;
		/***************As of PHP 5.3********************/
		/*
		$today = date("Y-m-d");
		$offsetdate = date_sub($today, new DateInterval('P'.$months_to_the_past.'M'));
		*/
		$dateclause = " AND date_created >= '$offsetdate'";
		
		
	}
	$limitclause = "";
	if ($limit>0)
		$limitclause = " DESC LIMIT $limit";  //DESC for taking the last measurements
	
	$dateselection = "CONCAT('new Date(',DATE_FORMAT(date_created,'%Y'),',',DATE_FORMAT(date_created,'%c')-1,',',DATE_FORMAT(date_created,'%e'),',',DATE_FORMAT(time_created,'%k')+0,',',DATE_FORMAT(time_created,'%i')+0,',',DATE_FORMAT(time_created,'%s'+0),')') mdate";
	//Group user measurements daily and show one point per day in graph
	if($grouped_per_day)
	{	
		$upstream_bw = "AVG(upstream_bw)";
		$downstream_bw = "AVG(downstream_bw)";
		$rtt = "AVG(rtt)";
		$loss = "AVG(loss)";
		$jitter = "AVG(jitter)";
		$groupbyclause = "GROUP BY date_created";
	}
	else
	{	
		$upstream_bw = "upstream_bw";
		$downstream_bw = "downstream_bw";
		$rtt = "rtt";
		$loss = "loss";
		$jitter = "jitter";
		$groupbyclause = "";
	}
	
	$query  = "SELECT $dateselection, 
				ROUND($upstream_bw,1) upstream, ROUND($downstream_bw,1) downstream,
				ROUND($rtt,1) rtt, ROUND($loss,2) 'packet_loss', ROUND($jitter,1) 'jitter' 
				FROM generic_measurements_stats 
				WHERE connection_id=$connection_id AND upstream_bw<>0 AND downstream_bw <> 0 AND rtt IS NOT NULL AND loss IS NOT NULL AND jitter IS NOT NULL
				$groupbyclause 
				ORDER BY date_created, time_created
				$limitclause";
	return $query;
}


?>
