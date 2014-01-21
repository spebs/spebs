<?php 
require_once("init.inc.php");
require_once("visualizations.lib.php");

if (isset($_REQUEST['t']) && $_REQUEST['t'] != 'g')
{
	$t = "ndt";
	$q = "SELECT DATE_FORMAT(created,'%d/%m/%y') Date, DATE_FORMAT(created,'%k:%i:%s') AS Time,
		FORMAT(downstream_bw,3) Downstream, upstream_bw Upstream, ROUND(rtt,1) RTT,
		CONCAT(ROUND(loss*100,2),'%') AS 'Packet Loss', Jitter, ssip \"Server\", scip \"Client\"
		FROM generic_measurement gm Natural JOIN web100_measurement nm WHERE user_id={$_SESSION['user_id']} ORDER BY created";
}
else
{
	$t = "glasnost";
	$q = "SELECT DATE_FORMAT(created,'%d/%m/%y') Date, DATE_FORMAT(created,'%k:%i:%s') AS Time,
		CONCAT(protocol1,' (port:',port1,')') AS Application,
		upload_indication \"Upload throttle\", download_indication \"Download throttle\",
		server \"Server\", hostip \"Client\"
		FROM glasnost_measurement WHERE user_id={$_SESSION['user_id']} ORDER BY created";
}
error_log("$t logs: ".$q);
$res = $spebs_db ->query($q);
while($row = $res -> fetch_assoc())
		$measurements[] = $row;

	if (!empty($measurements))
	{
		$today = date("d.m.y"); 
		$fname = $t.$_SESSION['username'].'_'.$today.'.csv';
		$csv   = create_csv_from_array($measurements, true);
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$fname);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . strlen($csv));

		echo $csv;
	}
	
?>
