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

	// TEMP DEFINITION
	$v = 0;

	$logged_in = false;
	
	if(isset($_SESSION['username']))
	{	
		$logged_in = true;
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		$connection_id = $_SESSION['connection_id'];
	}
	else
		$user_id=0;

/*********************************     Mesurement tools    *************************************************/
?>
<iframe name="tools" style="margin-left:32px;margin-right:48px;border:0;width:780px;height:300px;text-align:left;float:left;" src="tools.php">
</iframe>
<?	
/*********************************     Mesurement tools    *************************************************/
?>

<div style="clear:both;"></div>
<div id="ndt" style="text-align:center;">
<?
		display_message($lang_ndt_help);
?>
</div><!-- ndt -->
<script src="js/jquery_latest.js" type="text/javascript"></script>
<script type="text/javascript">
function change_connection(i)
{
	jQuery.post("setconn.php",{c:i});
}
</script>
