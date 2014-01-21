<?php
/***
*
*
* Useful info on db data
*
*
***/

include("../init.inc.php");
if(!isset($_SESSION['profile']) || $_SESSION['profile']<3)
	die("<html><head>
			<title>404 Not Found</title>
			</head><body>
			<h1>Not Found</h1>
			<p>The requested URL was not found on this server.</p>
			</body></html>");
	
if(isset($_REQUEST['e'])) 
{
	if($_REQUEST['e'] == 'n')
	{
		$view = "ndt_measurements_view";
		$t = 'ndt';
	}
	else
	{
		$view = "glasnost_measurements_view";
		$t = 'glasnost';
	}
	
	$today = date("d.m.y"); 
	$fname = $t.'_measurements'.'_'.$today.'.csv';
		
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream; charset=utf-8;');
	header('Content-Disposition: attachment; filename='.$fname);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	//header('Content-Length: ' . strlen($csv));
	
	output_csv_from_dbquery("SELECT * FROM $view", true);
	
	exit();	
}

?>


<!--body style="font-family:calibri,verdana,sans-serif; font-size:10pt; color:#707070"-->


<?
	$export_measurements_links = "&nbsp; | &nbsp;<a href=\"?e=n\">Μετρήσεις NDT</a>&nbsp; | &nbsp;<a href=\"?e=g\">Μετρήσεις Glasnost</a>";
	
	if(isset($_REQUEST['v']) && $_REQUEST['v'] == 't')
	{	
		$toplinks = "<a href=\"?v=s\">Στατιστικά μετρήσεων</a>&nbsp; | &nbsp;<a href=\"?v=ex\">Χάρτης αστικών κέντρων</a>&nbsp; | &nbsp;<a href=\"?v=r\">Βαθμός ικανοποίησης χρηστών</a>&nbsp; | &nbsp;<a href=\"?v=r24\">Βαθμός ικανοποίησης χρηστών (24MBps)</a>".$export_measurements_links; 
		include("traffic.php");
	}
	else if(isset($_REQUEST['v']) && $_REQUEST['v'] == 'ex')
	{	
		$toplinks = "<a href=\"?v=s\">Στατιστικά μετρήσεων</a>&nbsp; | &nbsp;<a href=\"?v=r\">Βαθμός ικανοποίησης χρηστών</a>&nbsp; | &nbsp;<a href=\"?v=r24\">Βαθμός ικανοποίησης χρηστών (24MBps)</a>".$export_measurements_links."&nbsp; | &nbsp;<a href=\"?v=t\">Κίνηση εξυπηρετητών</a>"; 
		include("exchange_view.php");
	}
	else if(isset($_REQUEST['v']) && $_REQUEST['v'] == 'r')
	{	
		$toplinks = "<a href=\"?v=s\">Στατιστικά μετρήσεων</a>&nbsp; | &nbsp;<a href=\"?v=ex\">Χάρτης αστικών κέντρων</a>&nbsp; | &nbsp;<a href=\"?v=r24\">Βαθμός ικανοποίησης χρηστών (24MBps)</a>".$export_measurements_links."&nbsp; | &nbsp;<a href=\"?v=t\">Κίνηση εξυπηρετητών</a>"; 
		include("satisfied_users.php");
	}
	else if(isset($_REQUEST['v']) && $_REQUEST['v'] == 'r24')
	{	
		$toplinks = "<a href=\"?v=s\">Στατιστικά μετρήσεων</a>&nbsp; | &nbsp;<a href=\"?v=ex\">Χάρτης αστικών κέντρων</a>&nbsp; | &nbsp;<a href=\"?v=r\">Βαθμός ικανοποίησης χρηστών</a>".$export_measurements_links."&nbsp; | &nbsp;<a href=\"?v=t\">Κίνηση εξυπηρετητών</a>"; 
		include("satisfied_users24.php");
	}
	else
	{	
		$toplinks = "<a href=\"?v=ex\">Χάρτης αστικών κέντρων</a>&nbsp; | &nbsp; <a href=\"?v=r\">Βαθμός ικανοποίησης χρηστών </a>&nbsp; | &nbsp;<a href=\"?v=r24\">Βαθμός ικανοποίησης χρηστών (24MBps)</a>".$export_measurements_links."&nbsp; | &nbsp;<a href=\"?v=t\">Κίνηση εξυπηρετητών</a>"; 
		include("stats.php");
	}
	
include("footer.php");
	?>

