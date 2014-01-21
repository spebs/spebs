<?
/**
 * SPEBS 
 *
 * This script displays measurement tool selection. 
 *
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
	
	if(isset($_SESSION['username']))
	{	
		$logged_in = true;
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		$connection_id = $_SESSION['connection_id'];
	}
	else
		$user_id=0;

		//display_top_info_message($lang_call_for_measurements);

?>

<div id="visualization_container">

<h1><?= $GLOBALS['lang_select_measurement_tool'] ?></h1>

<table cellpadding="0" cellspacing="0">
<tr>
<td width="5%">
&nbsp;
</td>
<td width="50%">
<a href="ndt.php"><h2 class="tool">NDT</h2></a>
<p class="description" style="margin-bottom:4px;">
<?= $lang_description_ndt ?>
</p>

<a href="glasnost.php"><h2 class="tool" style="margin-top:10px;">Glasnost</h2></a>
<p class="description">
<?= $lang_description_glasnost ?>
</p>
</td>
<td width="7%">
&nbsp;
</td>
<td valign="top" width="35%">
<a href="notifier.php"><h2 class="tool">M-Lab Notifier</h2></a>
<p class="description">
<?= $lang_description_notifier ?>
</p>
</td>
<td width="3%">
&nbsp;
</td>
</table>

<div style="clear:both;"></div>
</div><!-- #visualization_container -->
<?
include("footersimple.php");
?>

