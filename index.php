<?php 
/**
 * SPEBS 
 *
 * The script is the main script which redirects each request to the right page. 
 *
 *
 * @copyright (c) 2011 ΕΕΤΤ
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

$helpsection = (isset($helpanchors[$action]))? $helpanchors[$action]:$helpanchors["dashboard"];
$hide_help = false;
	switch ($action) 
	{
		
		case "signup":
			$command = "signup";
			require_once("signup.php");
			break;
		
		case "fpassword":
			$command = "fpassword";
			require_once("fpassword.php");
			break;
		
		case "myaccount":
			$command = "edit";
			require_once("signup.php");
			break;
		
		case "confirm_registration":
			require_once("registered_user.php");
			break;
		
		case "invalid":
			require_once("login.php");
			break;
		case "login":
			require_once("login.php");
			break;
		case "logout":
			require_once("logout.php");
			break;
		
		case "dashboard":
			require_once("dashboard.php");
			break;
		
		case "export_measurements":
			require_once("measurements_list.php");
			break;
		
		case "info":
			require_once("info.php");
			break;
		case "terms":
			require_once("terms.php");
			break;
		case "help":
			$hide_help = true;
			require_once("help.php");
			break;
		case "measurement":
			require_once("measurement.php");
			break;
		default:
			require_once("dashboard.php");

	}

disconnectDB() ;
?>
