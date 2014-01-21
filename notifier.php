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

<h1><?= $lang_install_mlab_notifier ?></h1>

<?
/*******************************     Show M-Lab Notifier page     ***********************************/
?>

<div id="mlab-notifier" style="text-align:left;margin-top:30px;">

<p style="margin-bottom:24px;">
<?= $lang_description_notifier_full ?>
</p>

<div class="center">
<a class="btn btn-large" style="width:200px;" href="http://broadbandtest.eett.gr/notifier/launch.jnlp"><?= $lang_download_run_mlab_notifier ?></a>
</div>

<br/>

<p>
<a href="/tools.php"><?= $lang_back_to_tools ?></a>
</p>

</div><!-- mlab-notifier -->

<div style="clear:both;"></div>
</div><!-- #visualization_container -->
<?
include("headersimple.php");
?>

