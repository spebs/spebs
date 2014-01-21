<?php 
require_once('init.inc.php');
# This is the Glasnost load balancer

# Note: For test fetching, be aware that the webserver's firewall prohibits outgoing connections by default.

error_reporting(E_ALL | E_STRICT);
#error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE); # Default w/o user notice


##############################################################################
# Do not change anything below this line unless you know what you are doing! #
##############################################################################

#$server = array (
# 				'http://loki08.mpi-sws.mpg.de/bb/',
#				'http://loki09.mpi-sws.mpg.de/bb/',
#				'http://loki10.mpi-sws.mpg.de/bb/'
#);
$server = array (
                 $home	
);

# Global variables

##########################
# Select a server to use #
##########################
function select_server() {
  global $server;
 
  return $server[mt_rand(0, (count($server))-1)];
}

##############################################
# Try to get a test from a different location 
##############################################
// Note: Only try to get the test from the servers that are used for uploads!
function find_test(){
	global $server;
		
	$id = $_GET['id'];
	if(!isset($id) || (preg_match("/^[\da-fA-F]+/",	$id) == 0)){
		header("HTTP/1.0 400 Bad Request");
		echo "Bad request.";
	}	
	$requester = $_SERVER['REMOTE_ADDR'];
	
	$server_list;
	for($i=0; $i<count($server); $i++){		
		# Get the IP address of the server
		$s = $server[$i];
		$p = strpos($s, "://");
		if($p){
			$s = substr($s, $p+3);
		}
		$p = strpos($s, "/");
		if($p){
			$s = substr($s, 0, $p);
		}

		$ip = gethostbyname($s); 
		
		if($ip != $requester){
			$server_list[] = $ip;
		}
	}
	
	$serialize = "";
	if(isset($_GET['serialize'])){
		$serialize = "&serialize=".$_GET['serialize'];
	}
	
	function myErrorHandler($errno, $errstr, $errfile, $errline){
		// $errstr contains a long error string that also contains the HTTP error, e.g., "HTTP/1.1 404 Not Found"
		//echo $errstr;
		return true;
	}
	
	for($i=0; $i<count(@$server_list); $i++){		
/* 		// Needs PECL extension which is not available (same for cUrl extension)		
  		$msg = http_parse_message(http_get("http://".$server_list[$i].":19981/?retrieve=script&id=$id"));
		if($msg->responseCode == 200){
			http_send_content_type('Content-type: text/plain');
			http_send_data($msg->body);
			exit(0);
		}
*/		
		$old_err = set_error_handler("myErrorHandler");
		$msg = file_get_contents("http://".$server_list[$i].":19981/?retrieve=script".$serialize."&id=$id");
		restore_error_handler();
		
		if($msg){
			header('Content-type: text/plain');
			echo $msg;		
			exit;
		}
	}
	
	header("HTTP/1.0 404 Not Found");
	echo "Not found.";
	exit;
}


#########################
# Redirect to startpage #
#########################
function redirect($next_page) {

	$params = "";
	
	# Check whether we redirect a GET or a POST request
	if(count($_POST) > 0){
		header("Location: $next_page");
		while($p = each($_POST)){
			header("$p[0]: $p[1]");
		}
				
	} else if(count($_GET) > 0){
		while($p = each($_GET)){
			$params = $params."$p[0]=$p[1]&";
		}
		header("Location: $next_page?$params");
	} else {	
		header("Location: $next_page");
	}

	exit;
}


function show_startpage(){
	
	$mserver = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	# Whether we should display an error message
	$error_code = 0;
	# Server was busy
	if(isset($_GET['busy'])){
		$error_code = 2;
	}
	
	$error = "";
	if($error_code == 2){
		$error = "<p style=\"border: 1px solid red;padding:5px;color:red;font-weight:bold;max-width:860px;\">".$GLOBALS["lang_gls_servers_overloaded1"]." <br> </p>";
	}

	$warning = "";
	# Mac users have to run a signed applet
#X#	if(stripos($_SERVER['HTTP_USER_AGENT'], "mac")){
#X#		$warning = '<li style="margin-left:-20px;">'.$GLOBALS["lang_gls_macosx_warn1"];
#X#	}
	


	 $notifier_glasnost = "The goal of the Glasnost project is to make ISPs' traffic shaping policies transparent to their customers. To this end, we designed Glasnost tests that enable you to check whether traffic from your applications is being rate-limited (i.e., throttled) or blocked. Glasnost tests work by measuring and comparing the performance of different application flows between your host and our measurement servers. The tests can detect traffic shaping in both upstream and downstream directions separately.  The tests can also detect whether application flows are shaped based on their port numbers or their packets' payload. We configured our tests to be conservative when declaring the presence of shaping, i.e., passing our tests does not necessarily mean that there is no throttling occurring on your link.";

##echo "<p>".$notifier_glasnost;"</p>";

#For more details on how Glasnost tests work, please  <!-- a href="http://broadband.mpi-sws.org/transparency/testdetails.html" target="_blank">this link</a>. --> read our <a href="http://broadband.mpi-sws.org/transparency/results/10_nsdi_glasnost.pdf">NSDI 2010 paper</a>.  
echo <<<END
    <form action="$mserver">

      <div align="center" style="border:0px solid #69acff;padding:15px 15px 0 15px;">

      <font style="font-weight:normal;font-size:12pt;">{$GLOBALS["lang_gls_selection1"]}</font>
             
      $error

        <input type="hidden" name="v" value="n"> 
        <input type="hidden" name="measure" value="yes"> 
	<input type="hidden" name="repeat" value="{$GLOBALS["glasnost_repeat"]}">
	<input type="hidden" name="duration" value="{$GLOBALS["glasnost_duration"]}">
        <input type="hidden" name="down" value="yes">
        <input type="hidden" name="up" value="yes">
        <input type="hidden" name="port" value="0">
        <input type="hidden" name="port2" value="0">
                    
      
        <table style="margin-top:5px;width:640px" class="indent"><tr>
        <th style="vertical-align:top;border-bottom:0px solid gray;text-align:left">{$GLOBALS["lang_gls_p2p"]}</th>
        <th style="width:40px;">&nbsp;</th>
        <th style="vertical-align:top;border-bottom:0px solid gray;text-align:left">{$GLOBALS["lang_gls_std"]}</th>
        <th style="width:40px;">&nbsp;</th>
        <th style="vertical-align:top;border-bottom:0px solid gray;text-align:left">{$GLOBALS["lang_gls_vod"]}</th>
        </tr><tr>
        <td style="vertical-align:top;text-align:left;">
	        <span><input type="radio" name="protocol1" value="BitTorrent" checked> BitTorrent</span><br> 
	        <span><input type="radio" name="protocol1" value="eMule"> eMule </span><br>
	        <span><input type="radio" name="protocol1" value="Gnutella"> Gnutella </span><br>
        </td>
        <td>&nbsp;</td>
        <td style="vertical-align:top;text-align:left;">
        	<span><input type="radio" name="protocol1" value="POP"> Email (POP) </span><br>
        	<span><input type="radio" name="protocol1" value="IMAP"> Email (IMAP4) </span><br>
        	<span><input type="radio" name="protocol1" value="HTTP"> HTTP transfer </span><br>
        	<span><input type="radio" name="protocol1" value="SSHTransfer"> SSH transfer </span><br>
        </td>
        <td>&nbsp;</td>
        <td style="vertical-align:top;text-align:left;">
        	<span><input type="radio" name="protocol1" value="FlashVideo"> Flash video (e.g., YouTube)</span><br>
        </td>
        </tr>

	<tr>
	<td colspan="5">
          <ul class="none" style="text-align:left;margin:5px 0 0 0;line-height:125%;">
            <li style="list-style-type:none;margin-left:-20px;padding:0">{$GLOBALS["lang_gls_info1"]}
            <!-- <li style="margin-left:-20px;"> {$GLOBALS["lang_gls_info2"]} -->
            $warning
          </ul>
	</td>
	</tr></table>
        
      <input type="submit" value="&raquo; {$GLOBALS["lang_gls_button"]} &laquo;" class="button">

      <div style="margin-top:8px"><a href="ndt.php">{$GLOBALS['lang_gls_try_ndt']}</a></div>
          
      </div>
    </form> 
    
	  </body>
	</html>
END;

} #show_start_page end here


if(isset($_GET['busy']) && ($_GET['busy'] == 1)){
	show_startpage();
}
# submit-test.php
elseif((isset($_POST['script'])) || (isset($_GET['writescript'])) || (isset($_GET['writetest'])) || (isset($_FILES['scriptfile']))
|| (isset($_GET['retrieve'])) || (isset($_POST['retrieve'])) || (isset($_GET['createtest']))
){
	redirect(select_server()."/submit-test.php");
}
# glasnost.php
elseif((isset($_GET['measure'])) || (isset($_GET['done'])) || (isset($_POST['done'])) || (isset($_GET['error']))){
	#redirect(select_server()."/glasnost.php");
	require_once('glasnost/glasnost.php');
}
elseif((isset($_GET['details'])) && ($_GET['details'] == 'yes')){
	require_once('glasnost/glasnost.php');
}
elseif(isset($_GET['findtest']) && isset($_GET['id'])){
	find_test();
}
elseif((isset($_GET['alltests']))){
	$param = "$server[0]";
	for($i=1; $i<count($server); $i++){
		$param .= ";$server[$i]";
	}	
	$_GET['server'] = $param;
	redirect(select_server()."submit-test.php");
}
else{
	show_startpage();
}

?>
