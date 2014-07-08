<?php
//include("../init.inc.php");

$pagetitle = "Στατιστικά χρήσης";
require("header.php");
	echo "<h3>General stats</h3>";
	$cons = get_single_value("SELECT COUNT(*) FROM connection WHERE status>-1");
	$cons0 = get_single_value("SELECT COUNT(*) FROM connection WHERE status=0");
	$cons1 = get_single_value("SELECT COUNT(*) FROM connection WHERE status=1");
	
	$connections_with_enough_measurements = get_single_value("SELECT COUNT(*) FROM (SELECT COUNT(*) c FROM generic_measurements_stats GROUP BY connection_id) conn WHERE c>$min_measurements_per_user");
	$connections_with_few_measurements = get_single_value("SELECT COUNT(*) FROM (SELECT COUNT(*) c FROM generic_measurements_stats GROUP BY connection_id) conn WHERE c<=$min_measurements_per_user");
	$connections_with_no_measurements = $cons1 - ($connections_with_enough_measurements + $connections_with_few_measurements);
	$timeperiod_between_two_measurements = get_single_value("SELECT ROUND(AVG(c)) FROM (SELECT TIMESTAMPDIFF(DAY,MIN(created), NOW())/COUNT(*) c from generic_measurement GROUP BY connection_id) p");
	
	$meas =  get_single_value("SELECT COUNT(*) FROM generic_measurement");
	$avgmeas = get_single_value("SELECT ROUND(AVG(measurements_count)) FROM aggregation_per_connection where peakHour=-1 and workingDay=-1");
	echo "
			<b>Registered connections:</b> $cons ($cons1 active/$cons0 inactive)<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>With more than $min_measurements_per_user meaurements:</i> $connections_with_enough_measurements (<small>shown on map</small>)<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>With less than $min_measurements_per_user meaurements:</i> $connections_with_few_measurements (<small>not shown on map</small>)<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>With no meaurements:</i> $connections_with_no_measurements (<small>not shown on map</small>)<br/>
			<b>Measurements conducted:</b> $meas (~$avgmeas per connection shown on map, ~one measurement every $timeperiod_between_two_measurements days per active connection)<br/>
		<hr/>
		";

	
	echo "  <h3>Connections per ISP</h3>";
	$vaxis = "";
	$column_types = array('string','number');
	$q = "SELECT name, COUNT(*) cons FROM connection c JOIN isp i ON c.isp_id=i.isp_id GROUP BY c.isp_id";
	show_column_chart($q,$column_types,"",12,$vaxis,"900px","400px");
	echo "<hr/>";

	
	echo "  <h3>Connections and measurements per region <br/><small><font color=\"#888888\">(<i>referring to connections with more than 3 measurements</i>)</font></small></h3>";
	$vaxis = "";
	$column_types = array('string','number','number');
	$q = "SELECT name_el,sum(connections_count) connections,sum(measurements_sum) measurements FROM aggregation_per_periphery a JOIN peripheries p ON a.periphery=p.id WHERE peakHour=-1 AND workingDay=-1 GROUP BY periphery ORDER BY sum(measurements_sum) DESC";
	show_bar_chart($q,$column_types,"Connections and measurements per periphery",11,$vaxis,"900px","400px");
	$q = "SELECT name_el,sum(connections_count) connections,sum(measurements_sum) measurements FROM aggregation_per_prefecture a JOIN prefectures p ON a.prefecture=p.id WHERE peakHour=-1 AND workingDay=-1 GROUP BY prefecture ORDER BY sum(measurements_sum) DESC";
	show_bar_chart($q,$column_types,"Connections and measurements per prefecture",13,$vaxis,"1300px","700px");
	echo "<hr/>";
	

	echo "  <h3>Connections' distributions per metric<small>(average per connection)</small></h3>";
	$column_types = array('string','number');
	$vaxis = "# of connections";
	$q = "SELECT round(avgdown,1) pace, count(*) FROM aggregation_per_connection WHERE peakHour=-1 and workingDay=-1 GROUP BY pace";
	show_column_chart($q,$column_types,"Download",1,$vaxis);
	$q = "SELECT round(avgup,2) pace, count(*) FROM aggregation_per_connection WHERE peakHour=-1 and workingDay=-1 GROUP BY pace";
	show_column_chart($q,$column_types,"Upload",2,$vaxis);
	$q = "SELECT round(avgrtt/10)*10 pace, count(*) FROM aggregation_per_connection WHERE peakHour=-1 and workingDay=-1 GROUP BY pace";
	show_column_chart($q,$column_types,"RTT",3,$vaxis);
	$q = "SELECT round(avgloss*10000)/100 pace, count(*) FROM aggregation_per_connection WHERE peakHour=-1 and workingDay=-1 GROUP BY pace";
	show_column_chart($q,$column_types,"Packet loss %",4,$vaxis);
	$q = "SELECT round(avgjitter/10)*10 pace, count(*) FROM aggregation_per_connection WHERE peakHour=-1 and workingDay=-1 GROUP BY pace";
	show_column_chart($q,$column_types,"Jitter",5,$vaxis);
	echo "<hr/>";
	
	echo "  <h3>Measurements' distributions per metric</h3>";
	$column_types = array('string','number');
	$vaxis = "# of mesurements";
	$q = "SELECT round(downstream_bw,1) pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Download distrbution",6,$vaxis);
	$q = "SELECT round(upstream_bw,2) pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Upload distrbution",7,$vaxis);
	$q = "SELECT round(rtt/10)*10 pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"RTT distrbution",8,$vaxis);
	$q = "SELECT round(loss*10000)/100 pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Packet loss % distrbution",9,$vaxis);
	$q = "SELECT round(jitter/10)*10 pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Jitter distrbution",10,$vaxis);

	function show_column_chart($q,$column_types, $title, $h, $vaxis, $width="900px", $height="350px")
	{
		echo '  <h3>'.$title.'</h3>
				<div id="histogram'.$h.'" style="width:'.$width.'; height: '.$height.';">'.$title.'</div>
			';
		$histogram_data = transform2google_data($q, $column_types, 'data');
		$colors = array("","darkorange","yellowgreen","#0d9562","red","blue","darkorange","yellowgreen","#0d9562","red","blue","darkorange","yellowgreen","#0d9562","red","blue","darkorange","yellowgreen","#0d9562","red","blue");
		$c = (count($column_types)>2)? "": ", colors:['{$colors[$h]}']";
	/*****************/
	echo "
			<script type=\"text/javascript\" src=\"http://www.google.com/jsapi\"></script>
			<script type=\"text/javascript\">
			google.load('visualization', '1', {'packages':['corechart'], 'language':'el_GR'});
			google.setOnLoadCallback(drawColumnChart);
			  
			function drawColumnChart() 
			{
				$histogram_data
				\n var chart = new google.visualization.ColumnChart(document.getElementById('histogram$h'));
				chart.draw(data,{vAxis:{title:'$vaxis'}, hAxis:{textStyle:{fontSize:10},slantedTextAngle:true,slantedTextAngle:90}, backgroundColor: '#eeeeee', isStacked:false, legend:'none' $c });
			}
			
			</script>
	";
	}

	
function show_bar_chart($q,$column_types, $title, $h, $vaxis, $width="900px", $height="350px")
{
	echo '  <h3>'.$title.'</h3>
			<div id="histogram'.$h.'" style="width:'.$width.'; height: '.$height.';">'.$title.'</div>
		';
	$histogram_data = transform2google_data($q, $column_types, 'data');
	$colors = array("","darkorange","yellowgreen","#0d9562","red","blue");
	$c = (count($column_types)>2)? "": ", colors:['{$colors[$h]}']";
/*****************/
echo "
		<script type=\"text/javascript\" src=\"http://www.google.com/jsapi\"></script>
		<script type=\"text/javascript\">
		google.load('visualization', '1', {'packages':['corechart'], 'language':'el_GR'});
		google.setOnLoadCallback(drawColumnChart);
		  
		function drawColumnChart() 
		{
			$histogram_data
			\n var chart = new google.visualization.BarChart(document.getElementById('histogram$h'));
			chart.draw(data,{vAxis:{title:'$vaxis'}, vAxis:{textStyle:{fontSize:10}}, backgroundColor: '#eeeeee', width:'$width', isStacked:false, legend:'right' $c });
		}
		
		</script>
";
	
}
include("footer.php");

?>


