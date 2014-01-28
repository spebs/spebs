<?
/**
 * SPEBS 
 *
 * This script may switch among displaying measurements, map, graphs or details. 
 *
 * MODES:
 *	 public: User is not logged in. She may not see graphs or details (i.e. table). NDT should not register any measurement.
 *   full: If user is connected through a domain outside her ISP scope, NDT should not register any measurement.
 *   	profile 1: Default simple user profile
 *   	profile 2: Priviledged user profile level 1
 *   	profile 3: Priviledged user profile level 2
 *   	profile 10: Administrative user profile
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

require_once("init.inc.php");
include("headersimple.php");
$logged_in = false;

$session = get_cursession_data();
if( $session[ 'username' ] ) {
	$logged_in = true;
        $username = $session[ 'username' ] ;
        $user_id = $session[ 'user_id' ];
        $connection_id = $session[ 'connection_id' ];
        #$down_mbps = $session[ 'down_mbps' ];
        #$up_mbps = $session[ 'up_mbps' ];
}
else {
	$user_id = 0;
}

if($logged_in) 
{
	if( ! valid_isp() ) {
		display_error_message($lang_ip_unknown);
	}
	$details = get_user_connection_details($user_id);
	$maximum_upload = $details['purchased_bandwidth_ul_kbps'] / 1000;
	$maximum_download = $details['purchased_bandwidth_dl_kbps'] / 1000;
}
else
{
	$maximum_upload = $MAXUPLOAD;
	$maximum_download = $MAXDOWNLOAD;
}

if( isset($_REQUEST['ndt']) ) {
	$ndt = $_REQUEST['ndt'];
	$minimum_java='1.6+';
	$compatibility_mode = 1;
}
else {
	$ndt = 'ndt';
	$minimum_java='1.7.0_45+';
	$compatibility_mode = 0;
}

?>

<script src="//d3nslu0hdya83q.cloudfront.net/dist/1.0/raven.min.js"></script>
<script>
Raven.config('https://7674b9f5c32e4924837be2cc43f6285c@app.getsentry.com/6922').install();
</script>

<script src="js/deployJava.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>

<script>


try {
	var console = (window.console = window.console || { 'log': function() { } });
} 
catch(err) {
	Raven.captureException(err);
}

var tick = 500;
var naming_service = 'appspot.php';

$.ajax({ 
	url: naming_service,
	dataType: 'json'
})
.then( 	function(data,textStatus,jqXHR) {
		console.log('json data from '+naming_service+' downloaded successfully');
		//if( data === null ) {
		//	console.log('alert, data is null');
		//	data = {"city": "Athens", "url": "http://ndt.iupui.mlab3.ath01.measurement-lab.org:7123", "ip": ["83.212.4.37"], "fqdn": "ndt.iupui.mlab3.ath01.measurement-lab.org", "site": "ath01", "country": "GR"} ; 
		//}
		return data;
	},
	function(jqXHR, textStatus, errorThrown) {
		console.log('GET  <?= $naming_server ?> failed:' + textStatus) 
		console.log( 'Detailed error thrown follows:' ); 
		console.log( errorThrown ); 
	} 
)
.then( 	function(data) { 
		//console.log(data);

		deployJava.returnPage = location.href;

		//console.log("versioncheck " + deployJava.versionCheck('1.6.0_10+'));

		console.log( 'detected JREs : ' + deployJava.getJREs() ) ;

		if( '<?=$compatibility_mode?>' == 1 ) {
			$('#warning_box').css('display','block');
			$('#warning_box').html('<a href="javascript:deployJava.installLatestJRE()"><?= $lang_recommend_java ?></a>');
		}

		if (deployJava.versionCheck('<?=$minimum_java?>') == false) {
			//document.write("<p><a href=\"javascript:deployJava.installLatestJRE()\"><?= $lang_need_java ?></a></p>");
			console.log('old version of java detected');
			$('#measurement_applet').detach();
			if( deployJava.getJREs().length > 0 ) {
				console.log('Will try an older applet and see if it works');
				if( '<?=$compatibility_mode?>' == 0 ) {
					setTimeout( function(){  window.location.href = '?ndt=ndt_old' },50);
				}
			}
			$('#ndt').append( '<p><a href="javascript:deployJava.installLatestJRE()"><?= $lang_need_java ?></a></p>' );
			return $.Deferred().reject('java is too old');
		}
		else {
			console.log('up-to-date version of java detected');
			return data;
		}
	}
)
.then( function(data) {
	console.log('applet created, proceeding with polling until it is alive');
	init_sequence(data);
	}, function(message) {
	console.log('cannot proceed because of error:' + message);
}) 

function waitAppletLoaded() {
	var deferred = $.Deferred();
	// should be : setTimeout( pollAppletLoaded, 0, deferred );
	setTimeout( (function(whatev) { return function() { pollAppletLoaded( whatev ); }; } )(deferred), 0 );
	//console.log( deferred );
	return deferred.promise();
}

function pollAppletLoaded(deferred) {

        var applet = document.getElementById('measurement_applet');
	console.log(applet);
                
        if((applet == null) || (typeof(applet) == "undefined")) {       
                console.log('applet element is still undefined');
                //should be : setTimeout(pollAppletLoaded,1000,deferred);
		setTimeout( (function(whatev) { return function() { pollAppletLoaded( whatev ); }; } )(deferred), tick ); 
                return;
        } else {
                console.log('applet element exists');
                try {
                        if(!applet.isActive()){
                                console.log('applet element not active yet');
				//should be : setTimeout(pollAppletLoaded,1000,deferred);
				setTimeout( (function(whatev) { return function() { pollAppletLoaded( whatev ); }; } )(deferred), tick ); 
                                return;
                        }
                } 
                catch(e) {
                        console.log('applet is not yet available, error was '+e.toString());    
			//should be : setTimeout(pollAppletLoaded,1000,deferred);
			setTimeout( (function(whatev) { return function() { pollAppletLoaded( whatev ); }; } )(deferred), tick ); 
                        return;
                }
        }
        //console.log('applet is ready!');
	deferred.resolve(applet);
}

function init_sequence(data) { 
	console.log(data);
	var loaded = waitAppletLoaded();
	loaded.then( function(applet) {
		console.log('applet is ready');
		console.log('setting measurement server to '+data['fqdn']);
		applet.set_host( data['fqdn'] );
		console.log('setting user agent to ' + navigator.userAgent );
		applet.setUserAgent( navigator.userAgent );
		//create_status_box( applet );
	});
	loaded.then( function(applet) {
		waitForCompletedMeasurement( applet ) ;
	});
	
}


//creates the status box under the applet and starts the periodic update process for it
function create_status_box(applet) {
	//create and new element right after the applet
	var name = 'status_box';
	// // //document.getElementById('measurement applet').insertAdjacentHTML('afterend','<div id="'+name+'"></div>');
	applet.insertAdjacentHTML('afterend','<div id="'+name+'"></div>');

	//get the new element
	var status_box = document.getElementById(name);
	if( status_box == null ) {
		console.log('status box creation failed');
	}
	else {
		//updateStatusBox(status_box,applet);
		console.log('status box created');
	}
}

//periodic function to update the status box. Repeats itself periodically
function updateStatusBox(status_box,applet) {
	//ask the applet whether it is running
	if(applet.measurement_running()) { 
		status_box.innerHTML = '<span style=\'color:grey\'><?= $GLOBALS['lang_ndt_try_gls'] ?></span>';
	}
	else {
		status_box.innerHTML = '<a href=\'glasnost.php\'><?= $GLOBALS['lang_ndt_try_gls'] ?></a>';	
	}
	//wake up after a while later and do the same
	setTimeout( function() { updateStatusBox(status_box,applet) } , tick );
}	



function waitForCompletedMeasurement( applet ) {
	console.log('waiting for a running measurement');
	$('#status_box').html('<a href=\'glasnost.php\'><?= $GLOBALS['lang_ndt_try_gls'] ?></a>');

	var new_ajax_promise = $.ajax({ 
        	url: naming_service,
        	dataType: 'json'
	});
	

	appletPromise( applet, 'applet.measurement_running()' )
	.then( function( applet ) {
		console.log( 'running measurement detected' );
		$('#status_box').html('<span style=\'color:grey\'><?= $GLOBALS['lang_ndt_try_gls'] ?></span>');
		$('#message_box').html('');
		//console.log( "applet.isReady()  " + applet.isReady() );
		return $.when( appletPromise( applet, "applet.get_errmsg() == 'All tests completed OK.'" ), new_ajax_promise );
	}).then( function(applet, data,textStatus,jqXHR ) {
		handleSuccessfullMeasurement( applet ) ;
		console.log( 'applet is ready to accept new measurements' );
		console.log( data[0] );
		console.log('setting measurement server to '+data[0]['fqdn']);
		applet.set_host( data[0]['fqdn'] );
		// do whatever needs to be done
		setTimeout( function() { waitForCompletedMeasurement( applet ); } , 0 ); //restart the loop
	});
}
	
function appletPromise( applet, predicate_eval ) {
	var deferred = $.Deferred();
	setTimeout( function() { pollAppletForPredicate( applet, predicate_eval, deferred ) } , 0 );		
	return deferred.promise();
}	
	
function pollAppletForPredicate( applet, predicate_eval, deferred ) {
	if( eval( predicate_eval ) ) {
		console.log('detected '+predicate_eval);
		deferred.resolve( applet );
	}
	else {
		//console.log( 'still no '+predicate_eval );
		setTimeout( function() { pollAppletForPredicate( applet, predicate_eval, deferred ) } , tick );
	}
}

function handleSuccessfullMeasurement(applet) {
	console.log('handling successfull measurement');
	var measurement_result = JSON.parse(applet.get_measurement_result()); 
	submitMeasurement( measurement_result ); 
}

function submitMeasurement( measurement_result ) {
	$.ajax({
		type: 'POST',
		url:  'results.php',
		data: measurement_result,
		dataType: 'json'
	})
	.then( function(data, textStatus, jqXHR) { 
			console.log('POST ok: ' + textStatus + "\nstatus is: " + data.success + "\nmessage is: " + data.message ); 
			console.log(data);
			if( data.success ) { 
				console.log('Server seems to have accepted our submission. Everything went fine');
			}
			else {
				console.log('Server did not accept our submission, because: ' + data.message );
			}
			setupMessageBox( data );
	}, function(jqXHR, textStatus, errorThrown) { console.log('POST failed:' + textStatus) } );
}

function setupMessageBox( data ) { 
	console.log( data );
	if( data.errno == 0 ) {
		$('#message_box').html('<span style=\'color:green\'><?= $lang_ndt_successful_submission ?></span>');
	}
	else if( data.errno == 3 ) {
		$('#message_box').html('<span style=\'color:red\'><?= $lang_ip_unknown ?></span>');
		//alert('<?= $lang_ip_unknown ?>');
	}
	else if( data.errno == 11 ) {
		$('#message_box').html('<span style=\'color:red\'><?= $lang_ndt_measurement_server_out_of_county ?></span>');
		//alert('<?= $lang_ndt_measurement_server_out_of_county ?>');
	}
	else if( ( data.errno == 7 ) || ( data.errno == 8 ) ) {
		$('#message_box').html('<span style=\'color:red\'><?= $lang_ndt_bw_warn ?></span>');
		//alert( ' <?= $lang_ndt_bw_warn ?> ' );
	}
	else if( data.errno == 4 )  {
		$('#message_box').html('<span style=\'color:gray\'><?= $lang_ndt_user_not_logged_in ?></span>');
	}
	else  {
		$('#message_box').html('<span style=\'color:red\'><?= $lang_ndt_internal_error ?><br>[error '+data.errno+': '+data.message+']</span>');
		//alert('<?= $lang_ndt_internal_error ?> '+data.errno+' '+data.message);
	}
}

function test_all_errors() {
	var a = [0,3,7,11,7,4,9];
	for ( var errno in a ) {
		(function () { var e=errno; setTimeout( function() { setupMessageBox( { 'errno': a[e], message: '' } ) }, errno*5000 ) })();
	}
}

//$( function() { test_all_errors(); }  );
</script>

<div id="visualization_container">
	<h1><?= $lang_new_measurement_with_ndt ?></h1>
	<div id="ndt" style="text-align:center;margin-top:30px;">
		<APPLET id="measurement_applet" code="Tcpbw100" MAYSCRIPT="yes" archive="ndt/<?= $ndt?>.jar" width="400" height="200">
			<!-- <?= $disablereport_ndt_param ?> -->
			<PARAM NAME="disableReport" VALUE="yes_please_disable_the_report"/>

			<?= $connectionid_ndt_param ?>

			<?= $userid_ndt_param ?>

			<PARAM NAME="reportHost" VALUE="<?= $report_host?>"/>
			<PARAM NAME="client" VALUE="<?= $GLOBALS["ndt_applet_id"] ?>"/>
			<PARAM NAME="image" VALUE="<?= $home.'images/ajax-loader.gif'?>"/>
			<PARAM NAME="centerimage" VALUE="true"/>
			<PARAM NAME="boxbgcolor" VALUE="#ffffff"/>
			<PARAM NAME="language" VALUE="<?= $lang_lang_short ?>"/>
			<PARAM NAME="country" VALUE="<?= $lang_country ?>"/>
			<PARAM NAME="max_up" VALUE="<?= $maximum_upload ?>"/>
			<PARAM NAME="max_down" VALUE="<?= $maximum_download ?>"/>

			<PARAM NAME="cache_option" VALUE="no"/>
			<PARAM NAME="permissions" VALUE="all-permissions"/>
		</APPLET>
		<div id="status_box" style="display:none;"></div>
		<div id="warning_box" style="display:none;"></div>
		<div id="message_box" style="height:36px;"></div>
	</div><!-- ndt -->
	<div style="clear:both;"/>
	<!--
	<div class="p_return"> 
	<a href="tools.php" class="return_btn"><?= $lang_back_to_tools ?></a>
	</div>
	-->
</div><!-- #visualization_container -->
</body>
</html>

