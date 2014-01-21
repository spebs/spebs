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
	
	// TEMP DEFINITION
	$v = 0;

	if(isset($_SESSION['username']))
	{	
		$logged_in = true;
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		$connection_id = $_SESSION['connection_id'];
	}
	else
		$user_id=0;
?>

<div id="visualization_container">

<h1><? if(!isset($_GET['internal2'])) echo $lang_new_measurement_with_glasnost ?></h1>

<?
/*********************************        Show Glasnost tool      *************************************************/

	if ($v == 0)
	{
		require_once("glasnost/glb.php");
	}

?>

</div><!-- glasnost -->
<div style="clear:both;"></div>
</div><!-- #visualization_container -->
<?
include("footersimple.php");
?>

