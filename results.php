<?php

/**
 * SPEBS 
 *
 * This page receives the result from NDT measurements.
 *
 * POST Arguments are all the measurement parameters, e.g. rtt, loss, 
 * jitter, etc
 * 
 * returns a simple json object containing the following fields:
 * success: boolean
 * message: string 
 *
 * @copyright (c) 2013 EETT
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE 
 *
 *
 * @author ICCS, NOC Team, National Technical University of Athens
 */

/*
* Originally written  by 
*			Aggeliki Dimitriou <A.Dimitriou@noc.ntua.gr> 
*			Panagiotis Christias <P.Christias@noc.ntua.gr> 
*			Athanasios Douitsis <A.Douitsis@noc.ntua.gr> 
*			Chrysa Papagianni <C.Papagianni@noc.ntua.gr> 
*/

/* change this to 1 to have debug data added to the json response */
$DEBUG = 1;

//TODO: move this variable to parameters.inc.php
$acceptable_measurement_server_ip_pattern = '/^83\.212\./';

$hostip = $_SERVER['REMOTE_ADDR'];

# all-inclusive list of allowed POST variables
$valid_keys = explode(' ','ECN_enabled ccip connection_id downstream_bw duplicate_acks is_application java_vendor java_version jitter loss measurement_tool mss nagle_enabled os_architecture os_name os_version out_of_order packet_size_preserved report_host report_port retransmissions rtt sack_blocks sack_enabled scip ssip time_stamping_enabled timeouts upstream_bw user_id version wait_seconds web100_AckPktsIn web100_AckPktsOut web100_BytesRetrans web100_CWND-Limited web100_CWNDpeaks web100_CongAvoid web100_CongestionOverCount web100_CongestionSignals web100_CountRTT web100_CurCwnd web100_CurMSS web100_CurRTO web100_CurRwinRcvd web100_CurRwinSent web100_CurSsthresh web100_DSACKDups web100_DataBytesIn web100_DataBytesOut web100_DataPktsIn web100_DataPktsOut web100_DupAcksIn web100_DupAcksOut web100_Duration web100_ECNEnabled web100_FastRetran web100_MaxCwnd web100_MaxMSS web100_MaxRTO web100_MaxRTT web100_MaxRwinRcvd web100_MaxRwinSent web100_MaxSsthresh web100_MinMSS web100_MinRTO web100_MinRTT web100_MinRwinRcvd web100_MinRwinSent web100_NagleEnabled web100_OtherReductions web100_PktsIn web100_PktsOut web100_PktsRetrans web100_RcvWinScale web100_SACKEnabled web100_SACKsRcvd web100_SampleRTT web100_SendStall web100_SlowStart web100_SmoothedRTT web100_SndLimBytesCwnd web100_SndLimBytesRwin web100_SndLimBytesSender web100_SndLimTimeCwnd web100_SndLimTimeRwin web100_SndLimTimeSender web100_SndLimTransCwnd web100_SndLimTransRwin web100_SndLimTransSender web100_SndWinScale web100_Sndbuf web100_StartTimeUsec web100_SubsequentTimeouts web100_SumRTT web100_Timeouts web100_TimestampsEnabled web100_WinScaleRcvd web100_WinScaleSent web100_X_Rcvbuf web100_X_Sndbuf web100_aspd web100_avgrtt web100_bad_cable web100_bw web100_c2sAck web100_c2sData web100_congestion web100_cwin web100_cwndtime web100_half_duplex web100_link web100_loss web100_maxCWNDpeak web100_minCWNDpeak web100_mismatch web100_order web100_rttsec web100_rwin web100_rwintime web100_s2cAck web100_s2cData web100_sendtime web100_spd web100_swin web100_timesec web100_waitsec window_scaling' ); 

# list of POST variables that MUST be present 
$required_keys = explode(' ','reporting_host measurement_tool version upstream_bw downstream_bw rtt loss jitter ccip scip ssip');

# this is required to build an appropriate SQL query afterwards
$var_names = array( 
	'reporting_host'		=> 's',
	'user_id'			=> 'i',
	'connection_id'			=> 'i',
	'measurement_tool'		=> 's',
	'version'			=> 's',
	'report_host'			=> 's',
	'report_port'			=> 'i',
	'upstream_bw'			=> 'd',
	'downstream_bw'			=> 'd',
	'rtt'				=> 'd',
	'loss'				=> 'd',
	'jitter'			=> 'i',
	'mss'				=> 'i',
	'out_of_order'			=> 'd',
	'sack_blocks'			=> 'i',
	'sack_enabled'			=> 'i',
	'nagle_enabled'			=> 'i',
	'ECN_enabled'			=> 'i',
	'time_stamping_enabled'		=> 'i',
	'timeouts'			=> 'i',
	'retransmissions'		=> 'i',
	'duplicate_acks'		=> 'i',
	'ccip'				=> 's',
	'scip'				=> 's',
	'ssip'				=> 's',
	'wait_seconds'			=> 'i',
	'web100_SndWinScale'		=> 'i',
	'web100_DSACKDups'		=> 'i',
	'web100_CurRTO'			=> 'i',
	'web100_MaxCwnd'		=> 'i',
	'web100_spd'			=> 'd',
	'web100_MaxRTO'			=> 'i',
	'web100_MaxRwinRcvd'		=> 'i',
	'web100_avgrtt'			=> 'd',
	'web100_maxCWNDpeak'		=> 'i',
	'web100_cwndtime'		=> 'd',
	'web100_rwintime'		=> 'd',
	'web100_CurMSS'			=> 'i',
	'web100_FastRetran'		=> 'i',
	'web100_congestion'		=> 'i',
	'web100_PktsIn'			=> 'i',
	'web100_DupAcksIn'		=> 'i',
	'web100_SndLimBytesRwin'	=> 'i',
	'web100_MinRwinRcvd'		=> 'i',
	'web100_rttsec'			=> 'd',
	'web100_SndLimBytesSender'	=> 'i',
	'web100_CurCwnd'		=> 'i',
	'web100_DataPktsOut'		=> 'i',
	'web100_SndLimBytesCwnd'	=> 'i',
	'web100_X_Rcvbuf'		=> 'i',
	'web100_SndLimTransCwnd'	=> 'i',
	'web100_order'			=> 'd',
	'web100_s2cData'		=> 'i',
	'web100_NagleEnabled'		=> 'i',
	'web100_timesec'		=> 'd',
	'web100_link'			=> 'i',
	'web100_DupAcksOut'		=> 'i',
	'web100_waitsec'		=> 'd',
	'web100_AckPktsOut'		=> 'i',
	'web100_loss'			=> 'd',
	'web100_sendtime'		=> 'd',
	'web100_CongestionSignals'	=> 'i',
	'web100_MinRTO'			=> 'i',
	'web100_MinRwinSent'		=> 'i',
	'web100_rwin'			=> 'd',
	'web100_swin'			=> 'd',
	'web100_StartTimeUsec'		=> 'i',
	'web100_Duration'		=> 'i',
	'web100_TimestampsEnabled'	=> 'i',
	'web100_RcvWinScale'		=> 'i',
	'web100_SndLimTimeRwin'		=> 'i',
	'web100_mismatch'		=> 'i',
	'web100_MaxRTT'			=> 'i',
	'web100_DataBytesOut'		=> 'i',
	'web100_SndLimTransSender'	=> 'i',
	'web100_c2sAck'			=> 'i',
	'web100_SampleRTT'		=> 'i',
	'web100_SndLimTransRwin'	=> 'i',
	'web100_ECNEnabled'		=> 'i',
	'web100_PktsOut'		=> 'i',
	'web100_CountRTT'		=> 'i',
	'web100_MinMSS'			=> 'i',
	'web100_SmoothedRTT'		=> 'i',
	'web100_DataPktsIn'		=> 'i',
	'web100_CWND-Limited'		=> 'd',
	'web100_CurSsthresh'		=> 'd',
	'web100_CurRwinRcvd'		=> 'i',
	'web100_CurRwinSent'		=> 'i',
	'web100_SubsequentTimeouts'	=> 'i',
	'web100_Timeouts'		=> 'i',
	'web100_MaxRwinSent'		=> 'i',
	'web100_SACKsRcvd'		=> 'i',
	'web100_SACKEnabled'		=> 'i',
	'web100_SendStall'		=> 'i',
	'web100_cwin'			=> 'd',
	'web100_WinScaleRcvd'		=> 'i',
	'web100_bad_cable'		=> 'i',
	'web100_MaxSsthresh'		=> 'i',
	'web100_OtherReductions'	=> 'i',
	'web100_aspd'			=> 'd',
	'web100_bw'			=> 'd',
	'web100_CWNDpeaks'		=> 'i',
	'web100_s2cAck'			=> 'i',
	'web100_CongestionOverCount'	=> 'i',
	'web100_Sndbuf'			=> 'i',
	'web100_AckPktsIn'		=> 'i',
	'web100_WinScaleSent'		=> 'i',
	'web100_minCWNDpeak'		=> 'i',
	'web100_X_Sndbuf'		=> 'i',
	'web100_CongAvoid'		=> 'i',
	'web100_PktsRetrans'		=> 'i',
	'web100_c2sData'		=> 'i',
	'web100_MinRTT'			=> 'i',
	'web100_half_duplex'		=> 'i',
	'web100_SlowStart'		=> 'i',
	'web100_SndLimTimeSender'	=> 'i',
	'web100_MaxMSS'			=> 'i',
	'web100_DataBytesIn'		=> 'i',
	'web100_BytesRetrans'		=> 'i',
	'web100_SumRTT'			=> 'i',
	'web100_SndLimTimeCwnd'		=> 'i',
	'os_architecture'		=> 's',
	'is_application'		=> 'i',
	'java_version'			=> 's',
	'java_vendor'			=> 's',
	'os_name'			=> 's',
	'os_version'			=> 's',
	'window_scaling'		=> 'i',
	'packet_size_preserved'		=> 'i'
);

# set error reporting to something appropriate
error_reporting(E_ALL | E_STRICT);

# we need this to get the user session and other stuff
require_once("init.inc.php");

# make sure the user is logged in , otherwise bomb
$session = get_cursession_data();
if( $session[ 'username' ] ) {
	$username = $session[ 'username' ] ;
	$user_id = $session[ 'user_id' ];
	$connection_id = $session[ 'connection_id' ];
	$down_mbps = $session[ 'down_mbps' ];
	$up_mbps = $session[ 'up_mbps' ];	
}
else {
	exit_with( false, 4, 'Sorry, but there does not seem to be a valid user session associated with this request. User should be logged in' );
}

#log that a valid user has connected
log_ndt_info('User connected to results reporter');

# this is gratuitus, but useful because someone might try to visit this url
# directly. As a result, this specific error will be shown
if( count( $_POST )  <= 0 ) {
	exit_with( false, 1, 'No post variables in request' );
}


# we are going to store all our POST values in this array
$query_variables = array();
$redundant_variables = array();

# debug scheme
# (thanks Angela for the & tip) 
$debug_data = ( $DEBUG )? array( 
	'query variables' => &$query_variables, 
	'redundant variables' => &$redundant_variables, 
	'post variables' => &$_POST
) : array() ;

$red = array();
# most of the query_variables we will get from the POST request
foreach ( $_POST as $key => $value) { 
	# this preg_replace will become unnecessary after I fix the applet
	# 2013-02-12: I fixed the applet, but just to be on the safe side I
	# leave this pgreg_replace enabled
	$key = preg_replace( '/:$/', '', $key );
	# only valid keys are allowed, anything else means error. 
	if( ! in_array( $key , $valid_keys ) ) {
		exit_with( false, 2, 'Sorry, but POST variable '.$key.' seems to be illegal' );
	} 
	# everything went fine, store the value in query_variables
	$query_variables[ $key ] = $value;	

	#also check whether any variable is unused
	if( ! array_key_exists( $key, $var_names ) ) {
		array_push( $red, $key );
	}
}

# some fewer variables we will fill in by ourselves
$query_variables[ 'reporting_host' ] = $hostip; #client ip 
$query_variables[ 'user_id' ] = $user_id; #from the session
$query_variables[ 'connection_id' ] = $connection_id; #ditto
$query_variables[ 'report_host' ] = gethostname(); #our own self
$query_variables[ 'report_port' ] = 0; #unused for the time being, but we'll leave it be

foreach( $required_keys as $required_key ) {
	if( ! array_key_exists( $required_key , $query_variables ) ) {
		exit_with( false, 9, 'Sorry, required key '.$required_key.' is missing from the POST request' );
	}
	elseif( ! isset( $query_variables[ $required_key ] ) ) {
		exit_with( false, 10, 'Sorry, required key '.$required_key.' is NULL' );
	}
}


# see if the isp whence the user is coming is valid
if( ! valid_isp() ) {
	exit_with( false, 3, 'Sorry, but address '. $_SERVER['REMOTE_ADDR'] . ' is out of known ISP address ranges.' );
}

if( ! preg_match( $acceptable_measurement_server_ip_pattern , $query_variables[ 'ssip' ] ) ) {
	exit_with(false, 11, 'Sorry, measurement server is out of country');
}

if( $query_variables[ 'upstream_bw' ] > $up_mbps ) {
		exit_with(false, 7, 'Sorry, upstream bandwidth exceeds maximum allowed value for this user and connection');
}
if( $query_variables[ 'downstream_bw' ] > $down_mbps ) {
		exit_with(false, 8, 'Sorry, downstream bandwidth exceeds maximum allowed value for this user and connection');
}

if( $query_variables[ 'measurement_tool' ] == 'ndt' ) {

	log_ndt_info('incoming NDT report');

	$web100_query = 'insert into web100_measurement set '.implode(',',array_map( 'addq', array_keys( $var_names )));
	$web100_values = array_map( 'get_post_var', array_keys( $var_names ) );
	$web100_types = implode( array_values( $var_names ) );

	$result = execute_prepared_query( $web100_query, $web100_values, $web100_types, false );

	if(!$result) {
		exit_with( false, 5, 'Sorry, database query to insert measurement into database failed ' );
	}
		
	$query_variables[ 'red' ] = $red;
	exit_with( true, 0, 'thank you for your submission, have a nice day!' );
} 
else {
	exit_with( false, 6, 'unknown measurement_tool, sorry, I cannot handle this submission' );
}

function addq($n) { 
	return '`'.$n.'`=?'; 
}

function get_post_var($n) { 
	global $query_variables;
	return $query_variables[ $n ] ;
}

function exit_with( $success , $errno, $message ) {
	global $debug_data;
	header('Content-type: application/json');
	( $success )? log_ndt_info($message) : log_ndt_warning($message);
	print json_encode(array('success' => $success , 'errno' => $errno , 'message' => $message, 'debug' => $debug_data ));
	exit;
}
	

?>

