<?php

require("parameters.inc.php");					

/*****************************   Session ***********************************/
session_start();
/*****************************   Language ***********************************/
$mlablang = 0;
if (isset($_REQUEST['l']) && in_array($_REQUEST['l'],$languages)
	$mlablang = $languages($_REQUEST['l']);
elseif (!isset($_REQUEST['l']))
{ 
	if(isset($_SESSION['mlablang']))
		$mlablang = $_SESSION['mlablang'];
	elseif(isset($_COOKIE['mlablang']))
		$mlablang = $_COOKIE['mlablang'];
}
$_SESSION['mlablang'] = $mlablang;
setcookie('mlablang', $mlablang, time()+60*60*24*30*12);

$lang = in_array($_REQUEST['l'],$mlablang) ? $languages[$mlablang]:$languages[0];
require_once("lang/$lang.lang.php");
require_once("library.lib.php");


if(!isset($_SESSION['username']) && isset($_GET['v']) && (isset($_GET['rl']) && $_GET['rl'] == 1))
header("Location: $home?action=login&v={$_GET['v']}");


/*****************************   Database ***********************************/

$spebs_db = connectDB();


/* ============== USER TRIES TO LOGIN ========================*/

if (isset($_POST['username']) && isset($_POST['password']) || (!isset($_SESSION['username']) && isset($_COOKIE['bbtsess']) )) 
{
	// check username/password
	if (isset($_POST['username']) && isset($_POST['password']))
	{	
		$loggedin_data = login($_POST['username'],$_POST['password']);
		$rememberme_set = (isset($_POST['remember']) && $_POST['remember'] == 'yes');
	}
	else
	{	
		$loggedin_data = check_remembered_user($_COOKIE['bbtsess']);
		$rememberme_set = true;
	}
	if (is_array($loggedin_data)) 
	{
		// valid user
		$_SESSION['username'] = $loggedin_data['username'];
		$_SESSION['user_id'] = $loggedin_data['user_id'];
		$_SESSION['connection_id'] = $loggedin_data['connection_id'];
		$_SESSION['profile'] = $loggedin_data['profile'];
		if($rememberme_set)
		{	
			if (!isset($loggedin_data['lastsession']))
				$loggedin_data['lastsession'] = null;
			update_user_session(true,$loggedin_data['lastsession']);
			setcookie('bbtsess', get_user_session_key(), time()+$rememberme_duration*24*60*60);
		}
		else
		{	
			update_user_session(false);
			setcookie('bbtsess','',-1);
		}
		$action="dashboard";
	} 
	else if(!isset($_POST['username']))//it is not a login attempt, the cookie exists but it is not valid
	{
			//remove cookie and don't allow myaccount or export_measurements
		setcookie('bbtsess','',-1);
		$action= ($_REQUEST['action'] == "myaccount" || $_REQUEST['action'] == "export_measurements")? "dashboard":$_REQUEST['action'];
	}
	else 
	{
		// invalid user
		$action="invalid";
		$_SESSION['action']="invalid";
	}
}
/* ============== USER DOES DIRTY THINGS WITH HIS/HER PROFILE ========================*/
elseif (isset($_POST['command']))
{	
	$action   = "signup";
	$command = $_POST['command'];
}
/* ============== USER LOGGED IN --> SHOW DASHBOARD ========================*/
elseif (isset($_SESSION['username'])) 
{
	$username = $_SESSION['username'];
	//$_SESSION['user_id'] = get_user_id($_SESSION['username']);
	//$_SESSION['connection_id'] = get_connection_id($_SESSION['user_id']);
	$action   = (isset($_GET['action']) && $_GET['action'] != 'login') ? $_GET['action'] : "dashboard";
} 
/* ============== USER TRIES TO ACTIVATE PROFILE ========================*/
elseif (isset($_GET['key'])) 
{
	$_SESSION['key'] = $_GET['key'];
	$action   = "confirm_registration";
} 
/* ============== ELSE ... WHATEVER ACTION SAYS ========================*/
else 
{
	$action   = (isset($_GET['action'])) ? $_GET['action'] : "";
}

if (!isset($_SESSION['profile'])) $_SESSION['profile'] = 0;

/*****************************   Various Parameters ***********************************/
if (isset($_SESSION['user_id']))
	$loginlogout = "logout";
elseif ($action != 'invalid' && $action != 'login' && $action != 'myaccount')
	$loginlogout = "login";


$helpanchors = array( 
	"public" => "",
	"login"	 => "",
	"invalid"	 => "",
	"signup" => "settings",
	"myaccount" => "settings",
	"logout"	 => "",
	"dashboard" => array("measurement","map","graphs","tabular"),
	"info"	 => "general",
	"map" => "map",
	"graphs" => "graphs",
	"details" => "details");

?>
