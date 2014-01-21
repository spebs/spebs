<?php
/***
*
*
* Useful info on db data
*
*
***/

include("../init.inc.php");
if(!isset($_SESSION['profile']) || $_SESSION['profile']<10)
	die("Go away!");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="font-family:calibri,verdana,sans-serif; font-size:10pt;">


<?
if(!isset($_REQUEST['mode']))
{
	echo "Pass mode=1 for &nbsp; &nbsp; &nbsp;<a href=\"{$_SERVER['PHP_SELF']}?mode=1\">Inactive connections with NO measurements</a><br/>";
	echo "Pass mode=2 for &nbsp; &nbsp; &nbsp;<a href=\"{$_SERVER['PHP_SELF']}?mode=2\">Inactive connections with SOME measurements</a><br/>";
	echo "Pass mode=3 for &nbsp; &nbsp; &nbsp;<a href=\"{$_SERVER['PHP_SELF']}?mode=4\">Connections' distributions per metric</a><br/>";
	echo "Pass mode=4 for &nbsp; &nbsp; &nbsp;<a href=\"{$_SERVER['PHP_SELF']}?mode=4\">Measurements' distributions per metric</a><br/>";
}

/********************         Inactive connections without any measurements              *****************************/
if($_REQUEST['mode']==1)
{
	$q = "SELECT uc1.user_id, ci.connection_id, ci.isp_id, ci.address, ci.longitude, ci.latitude, ci.status, ci.purchased_bandwidth_dl_kbps, ci.purchased_bandwidth_ul_kbps,
				ca.connection_id, ca.isp_id, ca.address, ca.longitude, ca.latitude, ca.status, ca.purchased_bandwidth_dl_kbps, ca.purchased_bandwidth_ul_kbps
			FROM connection ci 
			LEFT JOIN generic_measurement gm ON ci.connection_id=gm.connection_id JOIN user_connection uc1 ON ci.connection_id=uc1.connection_id  
			JOIN user_connection uc2 ON uc1.user_id=uc2.user_id 
			JOIN connection ca ON uc2.connection_id=ca.connection_id 
			WHERE ci.status=0 AND gm.measurement_id IS NULL AND  ca.status=1
			"; 
	 $res_inactive = execute_query($q);
	 
	 
	 echo "<h2>Inactive connections with no measurements</h2>";
	 echo "<table border=\"1px\" bordercolor=\"#dddddd\">
			<tr>
				<th>
					&nbsp;
				</th>
				<th>
					User id
				</th>
				<th>
					Connection
				</th>
				<th>
					ISP
				</th>
				<th>
					Address
				</th>
				<th>
					Longitude
				</th>
				<th>
					Latitude
				</th>
				<th>
					Active
				</th>
				<th>
					Download
				</th>
				<th>
					Upload
				</th>
			</tr>";
	$c = 0;
	 while ($row = $res_inactive-> fetch_array())
	 {
		$c++;
		echo "<tr height=\"8px\"><td colspan=10 bgcolor=\"#dddddd\" ></td></tr>";
		echo "<tr>";
			echo "<td rowspan=\"2\">$c</td>";
			echo "<td>{$row[0]}</td>";
			for($i=1;$i<9;$i++)
			{
				$font = "<font>";
				if($i!=1 && $i!=6 && $row[$i] != $row[$i+8]) 
					$font = "<font color=\"red\">";
				echo "<td>$font{$row[$i]}</font></td>";
			}
		echo "</tr>";
		echo "<tr>";
			echo "<td>{$row[0]}</td>";
			for($i=9;$i<17;$i++)
			{
				$font = "<font>";
				if($i!=9 && $i!=14 && $row[$i] != $row[$i-8]) 
					$font = "<font color=\"red\">";
				echo "<td>$font{$row[$i]}</font></td>";
			}
		echo "</tr>";
	}
} //mode=1
/*****************************         Inactive connections with some measurements              *****************************************************/
elseif($_REQUEST['mode']==2)
{
	$q = "SELECT DISTINCT gm.connection_id, gm.user_id, ci.isp_id, ci.address, ci.longitude, ci.latitude, ci.status, ci.purchased_bandwidth_dl_kbps, ci.purchased_bandwidth_ul_kbps,
				ca.connection_id, ca.isp_id, ca.address, ca.longitude, ca.latitude, ca.status, ca.purchased_bandwidth_dl_kbps, ca.purchased_bandwidth_ul_kbps
			FROM connection ci
			JOIN (SELECT DISTINCT connection_id, user_id FROM generic_measurement) gm ON ci.connection_id=gm.connection_id
			JOIN user_connection uc ON uc.user_id=gm.user_id 
			JOIN connection ca ON uc.connection_id=ca.connection_id 
			WHERE ci.status=0 AND  ca.status=1
			"; 
	 $res_inactive = execute_query($q);
	 
	 
	 echo "<h2>Inactive connections with some measurements</h2>";
	 echo "<table border=\"1px\" bordercolor=\"#dddddd\">
			<tr>
				<th>
					&nbsp;
				</th>
				<th>
					User id
				</th>
				<th>
					Connection
				</th>
				<th>
					ISP
				</th>
				<th>
					Address
				</th>
				<th>
					Longitude
				</th>
				<th>
					Latitude
				</th>
				<th>
					Active
				</th>
				<th>
					Download
				</th>
				<th>
					Upload
				</th>
			</tr>";
	 $c = 0;
	 while ($row = $res_inactive-> fetch_array())
	 {
		$c++;
		echo "<tr height=\"3px\"><td colspan=10 bgcolor=\"#dddddd\">&nbsp;</td></tr>";
		echo "<tr>";
			echo "<td rowspan=\"2\">$c</td>";
			echo "<td>{$row[0]}</td>";
			for($i=1;$i<9;$i++)
			{
				$font = "<font>";
					$font = "<font color=\"red\">";
				echo "<td>$font{$row[$i]}</font></td>";
			}
		echo "</tr>";
		echo "<tr>";
			echo "<td>{$row[0]}</td>";
			for($i=9;$i<17;$i++)
			{
				$font = "<font>";
				if($i!=9 && $i!=14 && $row[$i] != $row[$i-8]) 
					$font = "<font color=\"red\">";
				echo "<td>$font{$row[$i]}</font></td>";
			}
		echo "</tr>";
	}
} //mode=2
elseif($_REQUEST['mode']==3)
{
	echo "  <h2>Connections' distributions per metric<small>(average per connection)</small></h2>";
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
	
}
elseif($_REQUEST['mode']==4)
{
	echo "  <h2>Measurements' distributions per metric</h2>";
	$column_types = array('string','number');
	$vaxis = "# of mesurements";
	$q = "SELECT round(downstream_bw,1) pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Download distrbution",1,$vaxis);
	$q = "SELECT round(upstream_bw,2) pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Upload distrbution",2,$vaxis);
	$q = "SELECT round(rtt/10)*10 pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"RTT distrbution",3,$vaxis);
	$q = "SELECT round(loss*10000)/100 pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Packet loss % distrbution",4,$vaxis);
	$q = "SELECT round(jitter/10)*10 pace, count(*) FROM generic_measurement GROUP BY pace";
	show_column_chart($q,$column_types,"Jitter distrbution",5,$vaxis);
	
}
elseif($_REQUEST['mode']==5)
{
	echo "  <h2>Connections per region</h2>";
	$vaxis = "";
	$column_types = array('string','number','number');
	$q = "SELECT name_el,sum(connections_count) connections,sum(measurements_sum) measurements FROM aggregation_per_periphery a JOIN peripheries p ON a.periphery=p.id WHERE peakHour=-1 AND workingDay=-1 GROUP BY periphery";
	show_bar_chart($q,$column_types,"Connections and measurements per periphery",1,$vaxis,"900px","400px");
	$q = "SELECT name_el,sum(connections_count) connections,sum(measurements_sum) measurements FROM aggregation_per_prefecture a JOIN prefectures p ON a.prefecture=p.id WHERE peakHour=-1 AND workingDay=-1 GROUP BY periphery";
	show_bar_chart($q,$column_types,"Connections and measurements per prefecture",3,$vaxis,"900px","400px");
	
	
}


function show_column_chart($q,$column_types, $title, $h, $vaxis, $width="900px", $height="350px")
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
			chart.draw(data,{vAxis:{title:'$vaxis'}, vAxis:{textStyle:{fontSize:10}}, backgroundColor: '#eeeeee', isStacked:false, legend:'right' $c });
		}
		
		</script>
";
	
}
?>
</body>
</html>

