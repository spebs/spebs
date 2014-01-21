<?php
$pagetitle = "Βαθμμός ικανοποίησης χρηστών (24 Μβπσ)";
require("header.php");

	echo "  <h3>24Mbps contracts: Satisfied users for all ISPs</h3>";
	$vaxis = '# of connections';
	$column_types = array('number','number');
	$q = "SELECT round(round(1000*avgdown/purchased_bandwidth_dl_kbps,2)*100,0) ratio, COUNT(*) cons 
              FROM aggregation_per_connection m JOIN connection c ON m.connection_id=c.connection_id
              WHERE purchased_bandwidth_dl_kbps = 24000 AND workingDay=-1 AND peakHour=-1
              GROUP BY ratio";
	show_column_chart($q,$column_types,"All ISPs",1,$vaxis,"900px","400px");
	echo "<hr/>";
	
        $isps = get_results("SELECT * from isp");
        foreach($isps as $k => $isp)
        {
           echo "  <h3>24Mbps contracts: Satisfied users for {$isp['name']}</h3>";
           $vaxis = '# of connections';
           $column_types = array('number','number');
           $q = "SELECT round(round(1000*avgdown/purchased_bandwidth_dl_kbps,2)*100,0) ratio, COUNT(*) cons
              FROM aggregation_per_connection m JOIN connection c ON m.connection_id=c.connection_id
              WHERE purchased_bandwidth_dl_kbps = 24000 AND m.isp_id={$isp['isp_id']} AND workingDay=-1 AND peakHour=-1
              GROUP BY ratio";
           show_column_chart($q,$column_types,"",$k+2,$vaxis,"900px","400px");
           echo "<hr/>";
        }


	function show_column_chart($q,$column_types, $title, $h, $vaxis, $width="900px", $height="350px")
	{
		$histogram_data = transform2google_data($q, $column_types, 'data');
                if(!empty($histogram_data))
                { 
		   echo '  <h3>'.$title.'</h3>
				<div id="histogram'.$h.'" style="width:'.$width.'; height: '.$height.';">'.$title.'</div>
			';
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
				chart.draw(data,{vAxis:{title:'$vaxis'}, hAxis:{textStyle:{fontSize:10},slantedTextAngle:true,slantedTextAngle:90, title:'%', viewWindowMode: 'explicit', viewWindow: {min: 0, max: 100}, gridlines:{count: 11}}, backgroundColor: '#eeeeee', isStacked:false, legend:'none' $c });
			}
			
			</script>
	              ";
                 }
                 else
                   echo "There are no users for this ISP";
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
?>


