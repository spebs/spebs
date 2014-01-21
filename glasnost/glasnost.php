<?php

# This script serves two purposes:
# (1) Start Glasnost test by loading the applet from an arbitrary server
# (2) Display results from Glasnost
#
# The measurements are run from mserver which runs as a daemon on
# the measurement server(s) specified below.
#
# Note: The load-balancing code in select_mserver() requires the geoip 
#       php module to be installed and operational!
require_once('init.inc.php');


error_reporting(E_ALL | E_STRICT);
#error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE); # Default w/o user notice

# Where to store logs, use an absolute path if possible!
$store_directory = "/var/www/tmp/";

# Use DNS name instead of IP address to name this server (for virtual webservers)
$useDnsName = 1;

# The startpage of the Glasnost test. We redirect users there.
#$startpage = "glb.php";
$startpage = "glasnost.php";

# This array holds the names of all measurement servers (where gserver runs on)
$glasnost_server_info = json_decode(file_get_contents($home.'appspot.php?t=g'),true);

$mlab_server = array (
$glasnost_server_info['fqdn']
#  'glasnost.mpi-sws.donar.measurement-lab.org',
#  $GLOBALS["glasnost_server"],
);

##############################################################################
# Do not change anything below this line unless you know what you are doing! #
##############################################################################

# Global variables
$title = $lang_gls_title;
$subtitle = "";
$server = $_SERVER['SERVER_ADDR'];
if($useDnsName == 1){
  $server = $_SERVER['SERVER_NAME'];
}
$version = "19.08.2010";

# Information about the location of this script on the server
$subdir = substr($_SERVER['PHP_SELF'], 0, -12);
#$this_script = 'http://'.$server.$_SERVER['PHP_SELF'];
#$this_script = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

// aduitsis 2012-12-15 removed the http:// in front to try and eliminate error=1 bug when
// the applet redirects
$this_script = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
#$this_script = $_SERVER['SERVER_NAME'].'/'.$relative_path;

define("MIN_TEST_DURATION", 10);


###############################################
# Select a Glasnost measurement server randomly
###############################################
function select_mserver() {
  global $mlab_server;

  return $mlab_server[mt_rand(0, (count($mlab_server))-1)];
}


###############################################
# Process test results and display a summary
###############################################
function glasnost_get_results() {
	global $title, $subtitle, $store_directory, $this_script;

	# Some general information
	$ts = time();   # Timestamp
	$hostip = $_SERVER['REMOTE_ADDR'];
	$hostname = "";
	$args = "";


	# Read in all parameters passed from the applet
	while($p = each($_GET)){

		if(($p[0] == "sysinfo") || ($p[0] == "exception") || ($p[0] == "server") ||
		($p[0] == "msg") || ($p[0] == "mid") || ($p[0] == "peer") || $p[0] == "intern"){}
		else{
			$args .= "$p[0]=$p[1]&";
		}

		if($p[0] == "done"){}
		elseif($p[0] == "down"){}
		elseif($p[0] == "up"){}
		elseif($p[0] == "protocol1"){
			$protocol1 = $p[1];
		}
		elseif($p[0] == "protocol2"){
			$protocol2 = $p[1];
		}
		elseif($p[0] == "duration"){
			$duration = $p[1];
		}
		elseif($p[0] == "sysinfo"){}
		elseif($p[0] == "id"){
			if(is_numeric($p[1])){ $ts = $p[1]; }
		}
		elseif($p[0] == "server"){
			$server = $p[1];
		}

		elseif($p[0] == "peer"){
			$hostip = $p[1];
		}
		elseif($p[0] == "hostname"){
			$hostname = $p[1];
		}
		elseif($p[0] == "port"){
			$port1 = $p[1];
		}
		elseif($p[0] == "port2"){
			$port2 = $p[1];
		}
		elseif($p[0] == "repeat"){
			$repeat = $p[1];
		}


		# Now read what the client found
		elseif(preg_match('/^expu\d+/', $p[0])){
			$index = substr($p[0], 4); # Just give me the number at the end
			$expu[$index] = $p[1];
			if(! isset($expr[$index])){ $expr[$index] = 0; }
		}
		elseif(preg_match('/^expd\d+/', $p[0])){
			$index = substr($p[0], 4); # Just give me the number at the end
			$expd[$index] = $p[1];
			if(! isset($expr[$index])){ $expr[$index] = 0; }
		}
		elseif(preg_match('/^expl\d+/', $p[0])){
			$index = substr($p[0], 4); # Just give me the number at the end
			$expl[$index] = $p[1];
		}
		elseif(preg_match('/^expr\d+/', $p[0])){
			$index = substr($p[0], 4); # Just give me the number at the end
			$expr[$index] = $p[1];
		}
		elseif(preg_match('/^expstate\d+/', $p[0])){
			$index = substr($p[0], 8); # Just give me the number at the end
			$expstate[$index] = $p[1];
		}
		elseif(preg_match('/^expp\d+/', $p[0])){
			$index = substr($p[0], 4); # Just give me the number at the end
			$port[$index] = $p[1];
		}
		elseif(preg_match('/^expprot\d+/', $p[0])){
			$index = substr($p[0], 7); # Just give me the number at the end
			$proto[$index] = $p[1];
		}
		elseif(preg_match('/^expserv\d+/', $p[0])){
			$index = substr($p[0], 7); # Just give me the number at the end
			$serverIs[$index] = $p[1];
		}

		# Server side
		elseif(preg_match('/^expsu\d+/', $p[0])){
			$index = substr($p[0], 5); # Just give me the number at the end
			$expsu[$index] = $p[1];
			if(! isset($expsr[$index])){ $expsr[$index] = 0; }
			if(! isset($expsrs[$index])){ $expsrs[$index] = 0; }
		}
		elseif(preg_match('/^expsd\d+/', $p[0])){
			$index = substr($p[0], 5); # Just give me the number at the end
			$expsd[$index] = $p[1];
			if(! isset($expsr[$index])){ $expsr[$index] = 0; }
			if(! isset($expsrs[$index])){ $expsrs[$index] = 0; }
		}
		elseif(preg_match('/^expsl\d+/', $p[0])){
			$index = substr($p[0], 5); # Just give me the number at the end
			$expsl[$index] = $p[1];
		}
		elseif(preg_match('/^expsr\d+/', $p[0])){
			$index = substr($p[0], 5); # Just give me the number at the end
			$expsr[$index] = $p[1];
		}
		elseif(preg_match('/^expsrs\d+/', $p[0])){
			$index = substr($p[0], 5); # Just give me the number at the end
			$expsrs[$index] = $p[1];
		}
		elseif(preg_match('/^expsstate\d+/', $p[0])){
			$index = substr($p[0], 9); # Just give me the number at the end
			$expsstate[$index] = $p[1];
		}

		# DEBUG ONLY
		else{
			echo "<p style=\"color:red\">{$GLOBALS["lang_gls_debug2"]} \"$p[0]=$p[1]\"</p>";
		}
	}
	
	# Fallback if repeat was not set (but it should)
	if(! isset($repeat)){
		if(isset($port2) && ($port2 > -3)){
			$repeat = floor(count($prdown)/2);
		}
		else{
			$repeat = count($prdown);
		}
		echo "<p style=\"color:red\"> {$GLOBALS["lang_gls_debug3"]} \"$repeat\"</p>";
	}
	
	if($hostname == ""){
		$hostname = gethostbyaddr($hostip);
	}	

	# Sanity check for short tests
	if(1)
	{
		$avg_duration = 0;
		$num_exp = 0;
		for($i=0; $i<count($expu); $i++){
			if(($expu[$i] <= 0) && ($expd[$i] <= 0) && ($expsd[$i] <= 0) && ($expsu[$i] <= 0)){ next; }
			
			if(isset($expl[$i]) && isset($expsl[$i])){
				$avg_duration += max($expl[$i], $expsl[$i]);
				$num_exp++;
			}
			elseif(isset($expl[$i])){
				$avg_duration += $expl[$i];
				$num_exp++;
			}
			elseif(isset($expsl[$i])){
				$avg_duration += $expsl[$i];
				$num_exp++;
			}
		}
		if($num_exp > 5){
			$avg_duration /= $num_exp;
			
			if($avg_duration < MIN_TEST_DURATION){ # in seconds
				$string_msg = sprintf($GLOBALS["lang_gls_warning1"], round($avg_duration,2), MIN_TEST_DURATION);
				$warning[]=$string_msg;
			}
		}
	}

	# Check ports used
	if($port1 <= 0){ 
		$port1_orig = $port1;
		$port1 = $port[0];
	}
	if(($port2 <= 0) && ($port2 > -3)){
		$port2_orig = $port2;
		$port2 = $port[$repeat * 2];
	}
		
	if($port2 > -3){ # Test run on two ports

		$num_exp = $repeat * 2;
		
		$p = 0;
		for($i=0; $i<$num_exp; $i++){
			$p += $port[$i];
		}
		for($i=($repeat*4); $i<(($repeat*4)+$num_exp); $i++){
			$p += $port[$i];
		}
		if($p != ($port[0]*$num_exp*2 )){
			$string_msg = sprintf($GLOBALS["lang_gls_warning2 "],$port1) ;
			$warning[] = $string_msg;
		}

		$p = 0;
		for($i=($repeat*2); $i<(($repeat*2)+$num_exp); $i++){
			$p += $port[$i];
		}
		for($i=($repeat*6); $i<count($port); $i++){
			$p += $port[$i];
		}
		
		if($p != ($port[$num_exp]*$num_exp*2)){
			$string_msg = sprintf($GLOBALS["lang_gls_warning3 "],$port2) ;
			$warning[] =$string_msg;
		}
	}
	else { # Only on a single port
		for($i=0; $i<$count($port); $i++){
			$p += $port[$i];
		}
		if($p != ($port[0]*8)){
			$string_msg = sprintf($GLOBALS["lang_gls_warning4"],$port1);
			$warning[] = $string_msg;
		}
	}	
	
	if(count(@$warning) == 0){
		if($port[0] != $port1){
			$string_msg = sprintf($GLOBALS["lang_gls_warning5"],$port1, $port[0]);
			$warning[] = $string_msg; 
			$port1_orig = $port1;
			$port1 = $port[0];
		}
		if(($port2 > -3) && ($port[(int)$repeat*2] != $port2)){
			$string_msg = sprintf($GLOBALS["lang_gls_warning6"],$port2, $port[(int)($repeat*2)]);
			$warning[] = $string_msg;
			$port2_orig = $port2;
			$port2 = $port[(int)($repeat*2)];
		}
	}

	# TMP store what we display
	ob_start();

	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
  <head>
    <title>$title</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <link rel=\"stylesheet\" href=\"mpi.css\" type=\"text/css\">
    <link rel=\"shortcut icon\" href=\"./favicon.ico\" type=\"image/vnd.microsoft.icon\">
    <link rel=\"icon\" href=\"./favicon.ico\" type=\"image/vnd.microsoft.icon\"> 
  </head>

  <body>
    <h1 style=\"font-family:serif;\">$title$subtitle</h1>
  <div style=\"border:1px solid #69acff;padding:15px;\">
    <div style=\"border-bottom:1px solid #69acff;font-family:serif;font-weight:bold;\">
      Results for your host <i>($hostname - $hostip)</i>:</div>    
	";

	if(count(@$warning) > 0){
		echo "<p style=\"border:1px solid #ff7200; padding:10px; color: #ff7200;\">";
		for($i=0; $i<count($warning); $i++){
			echo "$warning[$i]<br>\n";
		}
		echo "</p>";
	}

	$string_msg = sprintf($GLOBALS["lang_gls_info3 "],$server, $hostname,$hostip, $server, $ts, $hostname, $hostip);	
	echo "<p>".$string_msg."</p>\n";
	

	# Display results
	####################

	$blocked = "N/A";


	# For now, just create a big table
	echo "<p>&nbsp;</p>
  <table class=\"box\" cellspacing=\"1\">
   <tr>
  <th class=\"box box2\">Application flow</th><th class=\"box box2\">{$GLOBALS["lang_gls_info39"]}</th>
  <th class=\"box box2\">User acts as</th><th class=\"box box2\">{$GLOBALS["lang_gls_info40"]}</th>
  <th class=\"box box2\">Upload speed</th><th class=\"box box2\">{$GLOBALS["lang_gls_info41"]}</th></tr>\n
  <tr><td colspan=\"6\" class=\"spacer\">&nbsp;</td></tr>\n";

	for($i=0; $i<count($expu); $i++){

		echo "<tr>";

		if ($expl[$i] > 0) {
		  $downbps = sprintf("%.2f", 0.008* $expd[$i] / $expl[$i]) . " Kbps";
		} else {
		  $downbps = "NA";
		}
		if ($expsl[$i] > 0) {
		  $upbps = sprintf("%.2f", 0.008*$expsd[$i] / $expsl[$i]) . " Kbps";
		} else {
		  $upbps = "NA";
		}
		if((($i % 2) == 1) && ($proto[$i] == $proto[$i-1]."-cf")){
			echo "<td class=\"box\"> {$GLOBALS["lang_gls_info34"]} </td>";
		} else {
			echo "<td class=\"box\">$proto[$i]</td>";
		}
		echo "<td class=\"box\">$port[$i]</td>";

		if($serverIs[$i] == "client"){
			echo "<td class=\"box\">{$GLOBALS["lang_gls_info36"]} </td>";
		}
		elseif($serverIs[$i] == "server"){
			echo "<td class=\"box\"> {$GLOBALS["lang_gls_info37"]} </td>";
		}

		echo "<td class=\"box\">$downbps</td><td class=\"box\">$upbps</td><td class=\"box\">$blocked</td>";
		echo "</tr>\n";
	}

	echo "</table>\n";

	echo " <p>&nbsp;</p> ". $GLOBALS["lang_gls_info4"] . "</p></div>";


	echo "</body></html>";

	# Store what we showed to the user
	#  $htmlfile = sprintf("bt_%s_%s_%d.html", $hostip, $hostname, $ts);
	#  $fp = fopen("${store_directory}/$htmlfile", "w");
	#  fwrite($fp, ob_get_contents());
	#  fclose($fp);

	# Now send all content to the user
	ob_end_flush();

	exit(0);
}



############################################################################################
# Start the measurement: Fork the server-side infrastructure and load the client-side applet
############################################################################################
function start_measurement() {
	global $title, $subtitle, $subdir, $server, $this_script , $lang_about, $lang_contact, $lang_terms;
	global $lang_gls_testing1, $lang_need_java, $lang_gls_please_wait;
	#  $id = getenv('REMOTE_ADDR'); # This might be a proxy!
	$id = time();
	$ep_param = "";
	

	
	# Get all parameters for the measurement

	$port = 0; # Autoselect
	if(isset($_GET['port']) && is_numeric($_GET['port'])){
		$port = $_GET['port'];
	}

	$port2 = -3; # Disabled. -2 lets the server choose
	if(isset($_GET['port2']) && is_numeric($_GET['port2'])){
		$port2 = $_GET['port2'];
	}

	$protocol1 = "";
	if(isset($_GET['protocol1']) && ($_GET['protocol1'] != "")){
		$protocol1 = $_GET['protocol1'];
		$ep_param .= "protocol1=$protocol1&"; 
	}
	$protocol2 = "";
	if(isset($_GET['protocol2']) && ($_GET['protocol2'] != "")){
		$protocol2 = $_GET['protocol2'];
		$ep_param .= "protocol2=$protocol2&";
	}

	$down = "false";
	if(isset($_GET['down']) && (($_GET['down'] == "yes") || ($_GET['down'] == "true"))){
		$down = "true";
	}
	$up = "false";
	if(isset($_GET['up']) && (($_GET['up'] == "yes") || ($_GET['up'] == "true"))){
		$up = "true";
	}

	$num_repeat = 3;
	if(isset($_GET['repeat'])){
		$num_repeat = $_GET['repeat'];
	}

	$duration = 30; # Use default or value specified in script
	if(isset($_GET['duration'])){
		$duration = $_GET['duration'];
	}

	$scriptFile = "";
	if(isset($_GET['scriptFile']) && ($_GET['scriptFile'] != "")){
		$scriptFile = $_GET['scriptFile'];
		$ep_param .= "scriptFile=$scriptFile";
		
		// Tell the first server we contact to fetch the script to do a recursive search
		if(strncasecmp($scriptFile, "http://", 7) == 0){
			$scriptFile .= "&recursive=1";
		}
	}

	$measurement_server = select_mserver();

	# We use a JavaScript timeout in case the applet was not loaded
	# The applet will disable this timeout if it was loaded successfully using JavaScript	
	$error_page = "http://${this_script}?error=2&id=$id&exception=php%20timeout&server=$measurement_server&port=$port&up=$up&down=$down&port2=$port2&duration=$duration&repeat=$num_repeat&$ep_param";
	$timeout = 60; # seconds


	# Mac gets a special jar (Mac Java needs privileges for select() calls)
	global $glasnost_applet_archive;
	global $glasnost_applet_mac_archive;
	$jar = $glasnost_applet_archive;
	#$jar = "http://${measurement_server}:19981/GlasnostReplayer.jar";
	if (stripos($_SERVER['HTTP_USER_AGENT'], "mac")){
		#$jar = "http://${measurement_server}:19981/GlasnostReplayerMac.jar";
		$jar = $glasnost_applet_mac_archive;
  }

  # In some cases we have to work-around a limitation in the Internet Explorer
  $browserWorkaround = "";
  $browserWorkaround_js = '';
  if (stripos($_SERVER['HTTP_USER_AGENT'], "MSIE")){
  	$browserWorkaround = "<param name=\"browserWorkaround\" value=\"true\">";
	$browserWorkaround_js = ", browserWorkaround: 'true'";
  }


  # Display HTML page and load Applet chrisa
$string_msg = $GLOBALS["lang_gls_warning7"];
echo <<<EOF
	<div style="border:0px solid #69acff;padding:15px;text-align:center;"> 
        <p class="description" style="margin-top:20px;margin-bottom:30px;"> $lang_gls_testing1: $protocol1 </p>
	<script src="http://www.java.com/js/deployJava.js"></script>
	<script>
		var attributes = { archive:'$jar', code:'GlasnostReplayer', height: 40, width: 200, alt: '$string_msg' } ;
		var parameters = {
			ID: '$id',
			protocol1: '$protocol1',
			server: '$measurement_server',
			port: $port,
			port2: $port2,
			up: $up,
			down: $down,
			duration: $duration,
			repeat: $num_repeat,
			cache_option: 'no',
			cache_version: '1.0',
			nextPage: '$this_script'
			$browserWorkaround_js
		};
		deployJava.returnPage = location.href;
		//as recommended by: http://download.oracle.com/docs/cd/E17409_01/javase/tutorial/deployment/deploymentInDepth/ensuringJRE.html
		//alert("versioncheck " + deployJava.versionCheck('1.6.0_10+'));
		if (deployJava.versionCheck('1.5+') == false) {      
			document.write("<p><a href=\"javascript:deployJava.installLatestJRE()\">$lang_need_java</a></p>");
		}
		else {
			//deployJava.runApplet(attributes, parameters, '1.5'); 
			deployJava.writeAppletTag(attributes, parameters);
		}
	</script>

	<p class="description" style="margin-top:27px;">{$GLOBALS["lang_gls_please_wait"]}</p>

	<br/>
	<br/>

	<span style='color:grey'>{$GLOBALS['lang_gls_try_ndt']}</span>

	</div>
EOF;

  echo "
<!--<body>-->

    <!--
    <h1 style=\"font-family:serif;\">$title$subtitle</h1>

    <table style=\"border:1px solid #69acff;padding:15px;\">
     <tr><td style=\"padding-bottom:20px;text-align:center;\">
        <b>$lang_gls_testing1: $protocol1</b>
     </td></tr><tr><td style=\"text-align:center;font-weight:bold;color:red;\">
       <aaaapplet archive=\"$jar\" code=\"GlasnostReplayer\" height=\"40\" width=\"200\" alt=\"". $GLOBALS["lang_gls_warning7"]. "\">
       <param name=\"ID\" value=\"$id\">
       <param name=\"protocol1\" value=\"$protocol1\">
       ";

  if($protocol2 != ""){ echo "<param name=\"protocol2\" value=\"$protocol2\">\n"; }
  if($scriptFile != ""){ echo "<param name=\"scriptFile\" value=\"$scriptFile\">\n"; }


   
  echo "       <param name=\"server\" value=\"$measurement_server\">
       <param name=\"port\" value=\"$port\">
       <param name=\"port2\" value=\"$port2\">
       <param name=\"up\" value=\"$up\">
       <param name=\"down\" value=\"$down\">
       <param name=\"duration\" value=\"$duration\">
       <param name=\"repeat\" value=\"$num_repeat\">
       <param name=\"cache_option\" value=\"no\">
       <param name=\"cache_version\" value=\"1.0\">
       <param name=\"nextPage\" value=\"$this_script\">
       $browserWorkaround
       <b style=\"color:red\">$lang_need_java</b>
       </applet>
     </td></tr><tr><td style=\"padding-top:20px;text-align:center;\">
       <b>Please wait while Glasnost tests your link for traffic shaping.</b>
     </td></tr></table>

    <p style=\"width:400px;\" id=\"javaInfo\"><i>{$GLOBALS["lang_gls_warning8"]}</i></p>
    -->

     <script type=\"text/javascript\"> 
      <!-- 
      // After the applet is loaded, it calls disablePhpTimeout()
      var seconds=$timeout
      var enableTimeout=1;
      
      function disablePhpTimeout(){
        enableTimeout = 0;
	// Hide the java info box
	if (document.layers) {document.layers[\"javaInfo\"].visibility=\"hide\";}
	if (document.all) {document.all[\"javaInfo\"].style.visibility=\"hidden\";}
	else if (document.getElementById){document.getElementById(\"javaInfo\").style.visibility=\"hidden\";}
      }

      function display(){ 
        if(enableTimeout != 1)
         return;

        if (seconds<=0){ 
         seconds=0
         window.location=\"$error_page\" 
        } 
        else 
         seconds-=1 
        setTimeout(\"display()\",1000) 
      } 
      
      function detectJavaPlugin(){
	if(navigator && navigator.plugins){
	  var length = navigator.plugins.length;
	  for(var i=0; i<length; i++) {
	    
	    if((navigator.plugins[i].name.indexOf('Java(TM)') >= 0)
	      || (navigator.plugins[i].name.indexOf('Java ') >= 0)
	      || (navigator.plugins[i].name.indexOf('IcedTea') >= 0)){
		return true;
	    }
	  }
	}
	return false;
      }
      
      if(detectJavaPlugin()){
	disablePhpTimeout();
      }
      
      display() 
      --> 
     </script> 

";
	echo "</div>";
	## include("footer.php");
       echo "</body></html>";
       exit(0);
}

###############
# Log to file #
###############
function log_to_file($filename, $line){
	$timeout = 1000; # Milliseconds

	if ($fp = fopen($filename, 'a')) {
		$startTime = microtime();
		do {
			$canWrite = flock($fp, LOCK_EX);
			// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
			if(!$canWrite) usleep(round(rand(0, 100)*1000));
		} while ((!$canWrite) && ((microtime()-$startTime) < $timeout));

		//file was locked so now we can store information
		if ($canWrite) {
			fseek($fp, 0, SEEK_END);
			fwrite($fp, $line);
		}
		else{
			send_error_report("Cannot write to file $filename", $line);
		}
		fclose($fp);
	}
}

################################################
# Send a mail to us reporting an unknown error #
################################################
function send_error_report($error_msg, $param){
	
	$hostip = $_SERVER['REMOTE_ADDR'];
	$hostname = gethostbyaddr($hostip);

	# email to us.
	$from = ""; # Enter your mail address here
	$msg = "<No message given> (";
	if(isset($_GET['msg'])){
		$msg = $_GET['msg'] . " (";
	}
	if(isset($_GET['mid'])){
		$msg .= $_GET['mid'] . ')';
	}
	else{
		$msg .= "-)";
	}

	$server = "<No server given>";
	if(isset($_GET['server'])){
		$server = $_GET['server'];
	}

	$out = "";
	if(isset($_GET['exception'])){
		$out .= "Exception: ".$_GET['exception'] . "\n";
	}
	if(isset($_GET['sysinfo'])){
		$out .= "Sysinfo: ".$_GET['sysinfo']."\n";
	}
	$out .= "Parameters: $param\n";

	if(isset($_SERVER['HTTP_REFERER'])){
		$out .= "HTTP info: Referer=".$_SERVER['HTTP_REFERER'];
	}
	$out .=" User-Agent=".$_SERVER['HTTP_USER_AGENT']." Accept-Charset=".$_SERVER['HTTP_ACCEPT_CHARSET']."\n";

	$message = "Glasnost failed for $hostip ($hostname) on $server!\n\n$msg\n\n$out\n";
	# Send mail
	#$ret = mail("EnterYourMailAddressHere", "Glasnost error: $error_msg", $message, "From: $from", "-f$from");

	echo "<p class=\"indent\">
        <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
        {$GLOBALS["lang_gls_info42"]}</p>";

}

################
# Handle error #
################
function error_handling($error_code) {
	global $startpage, $title, $subtitle, $store_directory;

	$hostip = $_SERVER['REMOTE_ADDR'];
	$hostname = "";

	$ts = time();
	if(isset($_GET['id']) && is_numeric($_GET['id'])){
		$ts = $_GET['id'];
	}

	$param = "";
	while($p = each($_GET)){

		if(($p[0] == "sysinfo") || ($p[0] == "exception") || ($p[0] == "server") ||
		($p[0] == "msg") || ($p[0] == "mid")){}

		elseif($p[0] == "peer"){
			if($p[1] != "null"){
				$hostip = $p[1];
				$param .= "ip=$p[1]&";
			}
			else {					
				$param .= "ip=$hostip&";
			}
		}
		else{
			$param .= "$p[0]=$p[1]&";
		}
	}
	if($hostname == ""){
		$hostname = gethostbyaddr($hostip);
	}

	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
  <head>
    <title>$title</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <link rel=\"stylesheet\" href=\"mpi.css\" type=\"text/css\">
    <link rel=\"shortcut icon\" href=\"./favicon.ico\" type=\"image/vnd.microsoft.icon\">
    <link rel=\"icon\" href=\"./favicon.ico\" type=\"image/vnd.microsoft.icon\"> 
  </head>

  <body>
    <h1 style=\"font-family:serif;\">$title$subtitle</h1>

  <div style=\"border:0px solid #69acff;padding:15px;\">
    
    <div style=\"border-bottom:0px solid #69acff;font-family:serif;font-weight:bold;\">
      An error occured while measuring your host <i>($hostname - $hostip)</i>:</div>    
";

	echo "<h3 class=\"indent\" style=\"color:#777777;\">{$GLOBALS["lang_gls_info43"]}</h3>";
	if($error_code == 1){ # Errors thrown by the Java applet
		if(isset($_GET['mid'])){
			if($_GET['mid'] == 5){
				echo "<p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:0px;\"> {$GLOBALS["lang_gls_error5"]}</p>";
				#	send_error_report("Selector failed", $param);
			}
			elseif($_GET['mid'] == 10){
				echo "<p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:0px;\">{$GLOBALS["lang_gls_error6"]} </p>";
				#	send_error_report("Server unreachable", $param);
			}
			elseif($_GET['mid'] == 11){
				echo "<p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:0px;\">{$GLOBALS["lang_gls_error7"]}</p>";
			}
			elseif($_GET['mid'] == 12){
				echo "<p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:0px;\"> {$GLOBALS["lang_gls_error12"]}";
				if(isset($_GET['btport']) && ($_GET['btport'] > 0)){
					if(isset($_GET['port2']) && ($_GET['port2'] > 0)){
						$string_msg = sprintf($GLOBALS["lang_gls_error9"],$_GET['btport'], $_GET['port2']);
						echo $string_msg;
					}
					else{
						$string_msg = sprintf($GLOBALS["lang_gls_error10"],$_GET['btport']);
						echo $string_msg;
					}
				}
				else{
					echo $GLOBALS["lang_gls_error11"];
				}


				echo "</p>";
				send_error_report("Server unreachable for experiment", $param);
			}
			else{
				echo "<p class=\"indent\">
          <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">{$GLOBALS["lang_gls_error13"]} </p>";
			}

			echo "<p class=\"indent\">
          <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
          <a href=\"$startpage\"> {$GLOBALS["lang_gls_info38"]} </a>.</p>";
		}
		else{
			send_error_report("Unknown mid", $param);
		}
	}
	elseif($error_code == 2){ # Error thrown by the php script
		echo "<p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
           {$GLOBALS["lang_gls_error14"]} </p>
          <p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
            {$GLOBALS["lang_gls_error15"]}</p>
          <p class=\"indent\">
            <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
            <a href=\"$startpage\"> {$GLOBALS["lang_gls_info38"]} </a>.</p>
         ";
	}
	elseif($error_code == 3){ # Error thrown by http proxy (browser workaround)
		echo "<p class=\"indent\">
          <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
          {$GLOBALS["lang_gls_error16"]} </p>";

		send_error_report("Browser Workaround failed", $param);

		echo "<p class=\"indent\">
          <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
          <a href=\"$startpage\">{$GLOBALS["lang_gls_info38"]}</a>.</p>";
	}
	else{
		echo "<p class=\"indent\">
          <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
          {$GLOBALS["lang_gls_error17"]}</p>";

		send_error_report("Unknown error", $param);

		echo "<p class=\"indent\">
          <img src=\"images/square.png\" alt=\"*\" style=\"margin-right:5px;margin-left:5px;margin-bottom:3px;\">
          <a href=\"$startpage\">{$GLOBALS["lang_gls_info38"]}</a>.</p>";
	}

	echo "<p>&nbsp;</p> <p>{$GLOBALS["lang_gls_info4"]}</p>

  </div></body></html>";

	# Log to file
	$http_info = "Referer=".@$_SERVER['HTTP_REFERER']."&User-Agent=".@$_SERVER['HTTP_USER_AGENT']."&Accept-Charset=".@$_SERVER['HTTP_ACCEPT_CHARSET'];
	log_to_file("${store_directory}/bttest.stats", "$ts;$hostname;$hostip;error;".@$_GET['server'].';'.@$_GET['sysinfo'].';'.@$_GET['msg'].';'.@$_GET['exception'].';'.$param.';'.$http_info.";\n");

	exit(0);
}

#########################
# Redirect to startpage #
#########################
###function redirect($new_param) {
###	global $startpage;
###
###	$params = "";
###	if(isset($new_param) && ($new_param != "")){
###		$params = "$new_param&";
###	}
###	# Read in all parameters
###	while($p = each($_GET)){
###		$params = $params."$p[0]=$p[1]&";
###	}
###	reset($_GET);
###
###	header("Location: $startpage?$params");
###
###	exit(0);
###}

#############
# Main loop #
#############

# Check if directory to store logs and traces is present
if(!is_dir($store_directory)){
	print "Internal error: '$store_directory' does not exist or is not a directory.\n";
	print "Please create this directory.\n";
	exit(1);
}

if(isset($_GET['version'])){
	echo "$version\n";
	echo "$store_directory\n";
	echo count($mlab_server)." measurement servers:\n";
	for($i=0; $i<count($mlab_server); $i++){
		echo "$mlab_server[$i]\n";
	}
}
elseif(isset($_GET['busy']) && ($_GET['busy'] == 1)){
	redirect(null);
}
# Client wants to be measured, check if we can do it
elseif(isset($_GET['measure']) && ($_GET['measure'] == "yes") && isset($_GET['protocol1']) && (isset($_GET['test']) || isset($_GET['down']) || isset($_GET['up']))) {

	if(isset($_GET['test'])){
		if($_GET['test'] == "standard"){
			$_GET['duration'] = 20;
			$_GET['repeat'] = 3;
		} elseif($_GET['test'] == "upstream"){
			$_GET['duration'] = 20;
			$_GET['up'] = "yes";
			$_GET['down'] = "no";
			$_GET['repeat'] = 5;
		} elseif($_GET['test'] == "downstream"){
			$_GET['duration'] = 20;
			$_GET['up'] = "no";
			$_GET['down'] = "yes";
			$_GET['repeat'] = 5;
		} elseif($_GET['test'] == "upstream-long"){
			$_GET['duration'] = 60;
			$_GET['up'] = "yes";
			$_GET['down'] = "no";
			$_GET['repeat'] = 2;
		} elseif($_GET['test'] == "downstream-long"){
			$_GET['duration'] = 60;
			$_GET['up'] = "no";
			$_GET['down'] = "yes";
			$_GET['repeat'] = 2;
		} else {
			trigger_error("Unknown parameter: test=".$_GET['test'].". Ignoring it.");
		}
	}

	

##	require_once('header.php');
  ##echo "<div style=\"border:1px solid #69acff;padding:15px;\">";
	start_measurement();
  ##echo "</p></div>";
##	require_once('footer.php');
}
# Show details to a given result
elseif(isset($_GET['details']) && $_GET['details'] == "yes" && (isset($_GET['port']))){
	require("glasnost/glasnost-analysis2.php");
	show_details();
}
# Measurement done, present results
elseif((isset($_GET['done']) && $_GET['done'] == "yes" && isset($_GET['id']) && isset($_GET['server']) && isset($_GET['port'])) ||
		(isset($_POST['done']) && isset($_POST['id']) && isset($_POST['server']) && isset($_POST['port']))){
	
	# Copy POST variables to GET variables before displaying results as there we can only handle $_GET
	if(isset($_POST['done'])){
		while($p = each($_POST)){
			$_GET[$p[0]] = $p[1];
		}
	}	
			
	require("glasnost/glasnost-analysis2.php");

	if(isset($_GET['internal2']) && ($_GET['internal2'] == 1)){
		get_detailed_differentiation_results(); # Found in glasnost-analysis.php
	}
	elseif(isset($_GET['internal']) && ($_GET['internal'] == 1)){
		get_differentiation_results(); # Found in glasnost-analysis.php
	}
	else {
		#get_differentiation_results("<b>Warning:</b> You are using a user-created Glasnost test. Please be warned that we cannot give any guarantee for the correctness of the results you get with this test.", TRUE);
		get_differentiation_results(null, TRUE);
		#get_detailed_results("<b>Warning:</b> You are using a user-created Glasnost test. Please be warned that we cannot give any guarantee for the correctness of the results you get with this test.");
		glasnost_get_results(); # Simple table
	}
}
# Error handling
elseif(isset($_GET['error'])){
  error_handling($_GET['error']);
}
# Display start page
else {
  redirect(null);
}

?>
