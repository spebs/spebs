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

	require_once("visualizations.lib.php");
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

/******************************************* Visualizations' tabs   ********************************************/
	
	//Hide personal visualizations (i.s. graphs and table) when not logged in
	$tab_class = ($logged_in)? array('class="tab"','class="tab"','class="tab"','class="tab"'):array('class="tab"','class="tab"','class="tab" style="display:none;"','class="tab" style="display:none;"');
	$tab_title = array($lang_new_measurement,$lang_map,$lang_graphs,$lang_tables);
	$tab_content = array('<a href="?v=n">'.$lang_new_measurement.'</a>',
						'<a href="?v=m">'.$lang_map.'</a>',
						'<a href="?v=c">'.$lang_graphs.'</a>',
						'<a href="?v=t">'.$lang_tables.'</a>'
						,'');
	$section_titles = array($lang_new_measurement_with_ndt,$lang_mapsectiontitle,$lang_chartsectiontitle,$lang_tabularsectiontitle);
	
	//show map by default
	$v = 1;  
	if (isset($_REQUEST['v']))
	{
		switch($_REQUEST['v'])
		{
			case 'c':
				$v = ($logged_in)? 2:1;
				break;
			case 'm':
				$v = 1;
				break;
			case 't':
				$v = ($logged_in)? 3:1;
				break;
			case 'n':
				$v = 0;
				break;
			default:
				$v = 1;
		}
	}
	$tab_class[$v] = 'class="tab-active"';
	$tab_content[$v] = $tab_title[$v];
	$section_title = $section_titles[$v];
	
	//Redirect to appropriate help subsection
	$h = $helpsection[$v];
	$helpsection = null;
	$helpsection = $h;

/******************************************* Display   ********************************************/
	include("header.php");
?>

<div id="dashboardtabs">
<?
	//Since tabs float right, display in reverse order
	for($i=count($tab_class)-1;$i>=0;$i--)
		echo "<div {$tab_class[$i]}>
				{$tab_content[$i]}
			</div>";
			
	if(isset($_SESSION['user_id']) && ($v != 0 || true))
	{
		$userconnections  = get_alluser_connections($_SESSION['user_id']);
		if(count($userconnections)>0)
		{	
			$selected = "selected";
			echo "<div style=\"background-color:#FFFFFF;color:#555555;padding-left:6px;\">$lang_connection ".
			"<select id=\"connection_selection\" style=\"width:94px; margin:2px 2px 2px 2px; color:#4466CC;\" id=\"contract\" onchange=\"change_connection(this.selectedIndex);\">";
			foreach($userconnections AS $i => $connectiondetails)
			{
				echo "<option value=\"{$connectiondetails['connection_id']}\" $selected>{$connectiondetails['description']} </option>";
				$connection_ids[] = $connectiondetails['connection_id'];
				$selected = "";
			}
			echo "</select>
			</div>";
		}
	}
?>
</div>

<?
/*

if(isset($_SESSION['user_id']) && $v != 0)
{
		$userconnections  = get_alluser_connections($_SESSION['user_id']);
		if(count($userconnections)>0)
		{	
			echo "<div id=\"connectionswrapper\">";
		
			echo "<div class=\"menu\"><div id=\"connlabel\">$lang_connection</div>";
        	foreach($userconnections AS $i => $connectiondetails)
			{
				echo "<div class=\"popup\">
	    				<a href=\"#\" onclick=\"change_connection($i);\">{$connectiondetails['description']}</a>
				</div>";
			}
			echo "</div></div>";
		}
}*/
?>

<div id="dashboardtitle">
	<? if($v>0) echo $section_title ?>
</div>
<div id="metricsinfo">
	<a href="help.php#metrics" title="<?= $lang_info ?>" target="_blank">
		<img src="images/info_lightblue.jpg">
	</a>
</div>
<?
	if ($v == 1 && $logged_in == false)
		display_top_info_message($lang_call_for_measurements);

?>
<div id="visualization_container">
<?
/*********************************        Show NDT or Glasnost applet      **********************************/

	if ($v == 0)
	{
		require_once("measurement.php");
	}

/*********************************        Show Map         *************************************************/
// Depending on user profile, if logged in, she may or may not see ISP selection and color classification of map icons and polygons
	elseif ($v == 1)
		show_map($user_id); 
	
/*********************************        Show Charts         *************************************************/
	
	elseif ($v == 2)
	{
		$disp = (connection_measurements($connection_id) < 3)? "block":"none";
		
		echo '<div id="notenoughmeasurements" style="displpay:'.$disp.';">';
			display_info_message($lang_not_enough_measurements_for_chart);
		echo '</div>';
		//else
			show_charts($user_id, $connection_ids); 
	}
/*********************************        Show Table         *************************************************/

	elseif ($v == 3)
	{
		show_tabular($user_id,$connection_ids); 
	}
?>
<div style="clear:both;"></div></div><!-- #visualization_container -->
<?
include("footer.php");
?>

