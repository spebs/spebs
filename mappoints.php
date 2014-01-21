<?php
/**
 * SPEBS 
 *
 * This script responds to AJAX requests originating from map operations (pan, zoom or drag)
 *   
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
require_once("visualizations.lib.php");
global $msg;

$userlocation = null;
$userconnection = -1;
$uid = (isset($_SESSION['user_id']))? $_SESSION['user_id']:0;
if ($uid>0)
{
	$userlocation = getUserLocation($uid);
	$userconnection = get_connection_id($uid);
}
$zoom = 100;
if (is_numeric($_REQUEST['z']) && $_REQUEST['z']>0 && $_REQUEST['z']<18)
	$zoom = $_REQUEST['z'];

//By default all days and hours
$workingday = -1;
$workinghour = -1;
$contract = -1;
$tool = 'ndt';
if (is_numeric($_REQUEST['wd']) && is_numeric($_REQUEST['wh'])  && is_numeric($_REQUEST['c']))
{
	if($_REQUEST['wd'] > -2 && $_REQUEST['wd'] < 2)
		$workingday = $_REQUEST['wd'];
	if($_REQUEST['wh'] > -2 && $_REQUEST['wh'] < 3)
		$workinghour = $_REQUEST['wh'];
	if(isset($bandwidths[$_REQUEST['c']]))
		$contract = $_REQUEST['c'];
	if($_REQUEST['t'] == 'g')
		$tool = 'glasnost';
}	

//Calculate map boundaries slightly wider than the visible area, so that slight drags shows balloons and polygons of neighbouring areas.
$vp_lb = null;
$vp_rt = null;
$min_lat = null;
$min_lng = null;
$max_lat = null;
$max_lng = null;
if(isset($_REQUEST['blt']) && isset($_REQUEST['blg']) && isset($_REQUEST['trt']) && isset($_REQUEST['trg']) )
{
	if(is_numeric($_REQUEST['blt']) && is_numeric($_REQUEST['blg']) && is_numeric($_REQUEST['trt']) && is_numeric($_REQUEST['trg']) )
	{
		$min_lat = $_REQUEST['blt'] - ($_REQUEST['trt']- $_REQUEST['blt'])/5;
		$min_lng = $_REQUEST['blg'] - ($_REQUEST['trg']- $_REQUEST['blg'])/5;
		$max_lat = $_REQUEST['trt'] + ($_REQUEST['trt']- $_REQUEST['blt'])/5;
		$max_lng = $_REQUEST['trg'] + ($_REQUEST['trt']- $_REQUEST['blt'])/5;
		$vp_lb = array("lat"=>$min_lat,"lng"=>$min_lng);
		$vp_rt = array("lat"=>$max_lat,"lng"=>$max_lng);
	}
}
if ($zoom>14 && $zoom!=100)// show balloons (100 for general view of map)
{

	$q = build_map_query($userconnection,$min_lat,$min_lng,$max_lat,$max_lng,$zoom,$workingday,$workinghour,$contract,$tool);
	$json = generate_map_pins($q,$userconnection,$userlocation,$tool,$vp_lb,$vp_rt);
	echo $json;
}
else //show polygons/icons for areas
{
	$q = build_map_aggregated_query($userlocation,$vp_lb,$vp_rt,$zoom,$workingday,$workinghour,$contract,$tool);
	$json = generate_map_circles($q,$tool);
	echo $json;
}
	
?>